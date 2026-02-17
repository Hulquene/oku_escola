<?php

namespace App\Models;

class ClassModel extends BaseModel
{
    protected $table = 'tbl_classes';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'class_name',
        'class_code',
        'grade_level_id',
        'academic_year_id',
        'class_shift',
        'class_room',
        'capacity',
        'class_teacher_id',
        'is_active'
    ];
    
    protected $validationRules = [
        'class_name' => 'required',
        'class_code' => 'required|is_unique[tbl_classes.class_code,id,{id}]',
        'grade_level_id' => 'required|numeric',
        'academic_year_id' => 'required|numeric',
        'class_shift' => 'required',
        'capacity' => 'permit_empty|numeric'
    ];
    
    protected $useTimestamps = true;
    
    /**
     * Get classes by academic year
     */
    public function getByAcademicYear($academicYearId)
    {
        return $this->where('academic_year_id', $academicYearId)
            ->where('is_active', 1)
            ->orderBy('class_name', 'ASC')
            ->findAll();
    }
    
    /**
     * Get classes by grade level
     */
    public function getByGradeLevel($gradeLevelId, $academicYearId = null)
    {
        $builder = $this->where('grade_level_id', $gradeLevelId)
            ->where('is_active', 1);
        
        if ($academicYearId) {
            $builder->where('academic_year_id', $academicYearId);
        } else {
            $academicYearModel = new AcademicYearModel();
            $currentYear = $academicYearModel->getCurrent();
            if ($currentYear) {
                $builder->where('academic_year_id', $currentYear->id);
            }
        }
        
        return $builder->orderBy('class_name', 'ASC')->findAll();
    }
    
    /**
     * Get class with teacher info
     */
    public function getWithTeacher($id)
    {
        return $this->select('tbl_classes.*, tbl_users.first_name, tbl_users.last_name, tbl_users.email')
            ->join('tbl_users', 'tbl_users.id = tbl_classes.class_teacher_id', 'left')
            ->where('tbl_classes.id', $id)
            ->first();
    }
    
    /**
     * Get available seats
     */
    public function getAvailableSeats($classId)
    {
        $class = $this->find($classId);
        
        if (!$class) {
            return 0;
        }
        
        $enrollmentModel = new EnrollmentModel();
        $enrolled = $enrollmentModel->where('class_id', $classId)
            ->where('status', 'Ativo')
            ->countAllResults();
        
        return $class->capacity - $enrolled;
    }
    
    /**
     * Get classes by teacher
     */
    public function getByTeacher($teacherId, $academicYearId = null)
    {
        $builder = $this->where('class_teacher_id', $teacherId)
            ->where('is_active', 1);
        
        if ($academicYearId) {
            $builder->where('academic_year_id', $academicYearId);
        } else {
            $academicYearModel = new AcademicYearModel();
            $currentYear = $academicYearModel->getCurrent();
            if ($currentYear) {
                $builder->where('academic_year_id', $currentYear->id);
            }
        }
        
        return $builder->orderBy('class_name', 'ASC')->findAll();
    }
}