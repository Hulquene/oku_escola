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
        'id' => 'permit_empty|is_natural_no_zero',  // <-- ADICIONAR ESTA LINHA!
        'class_name' => 'required|min_length[3]|max_length[100]',
        'class_code' => 'required|min_length[2]|max_length[50]|is_unique[tbl_classes.class_code,id,{id}]',
        'grade_level_id' => 'required|numeric',
        'academic_year_id' => 'required|numeric',
        'class_shift' => 'required|in_list[Manhã,Tarde,Noite,Integral]',
        'class_room' => 'permit_empty|max_length[50]',
        'capacity' => 'permit_empty|numeric|greater_than[0]|less_than[101]',
        'class_teacher_id' => 'permit_empty|numeric',
        'is_active' => 'permit_empty|in_list[0,1]'
    ];
    
    protected $validationMessages = [
        'class_name' => [
            'required' => 'O nome da turma é obrigatório',
            'min_length' => 'O nome da turma deve ter pelo menos 3 caracteres',
            'max_length' => 'O nome da turma não pode ter mais de 100 caracteres'
        ],
        'class_code' => [
            'required' => 'O código da turma é obrigatório',
            'min_length' => 'O código deve ter pelo menos 2 caracteres',
            'max_length' => 'O código não pode ter mais de 50 caracteres',
            'is_unique' => 'Este código de turma já está em uso'
        ],
        'grade_level_id' => [
            'required' => 'O nível de ensino é obrigatório',
            'numeric' => 'Nível de ensino inválido'
        ],
        'academic_year_id' => [
            'required' => 'O ano letivo é obrigatório',
            'numeric' => 'Ano letivo inválido'
        ],
        'class_shift' => [
            'required' => 'O turno é obrigatório',
            'in_list' => 'Turno inválido'
        ],
        'capacity' => [
            'numeric' => 'Capacidade deve ser um número',
            'greater_than' => 'Capacidade deve ser maior que 0',
            'less_than' => 'Capacidade não pode ser maior que 100'
        ]
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
            $academicYearModel = new \App\Models\AcademicYearModel();
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
        
        $enrollmentModel = new \App\Models\EnrollmentModel();
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
            $academicYearModel = new \App\Models\AcademicYearModel();
            $currentYear = $academicYearModel->getCurrent();
            if ($currentYear) {
                $builder->where('academic_year_id', $currentYear->id);
            }
        }
        
        return $builder->orderBy('class_name', 'ASC')->findAll();
    }
    /**
     * Get class with academic year and grade level
     */
    public function getWithAcademicYear($id)
    {
        return $this->select('
                tbl_classes.*, 
                tbl_academic_years.year_name,
                tbl_grade_levels.level_name
            ')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_classes.academic_year_id')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
            ->where('tbl_classes.id', $id)
            ->first();
    }
    /**
     * Get class with teacher, academic year and grade level
     */
    public function getWithTeacherAndAcademicYear($id)
    {
        return $this->select('
                tbl_classes.*, 
                tbl_users.first_name as teacher_first_name, 
                tbl_users.last_name as teacher_last_name, 
                tbl_users.email as teacher_email,
                tbl_academic_years.year_name,
                tbl_grade_levels.level_name,
                tbl_grade_levels.education_level
            ')
            ->join('tbl_users', 'tbl_users.id = tbl_classes.class_teacher_id', 'left')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_classes.academic_year_id')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
            ->where('tbl_classes.id', $id)
            ->first();
    }
        /**
     * Get classes with academic year and grade level for enrollment form
     */
    public function getForEnrollment($academicYearId)
    {
        return $this->select('
                tbl_classes.*, 
                tbl_grade_levels.level_name,
                tbl_academic_years.year_name
            ')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_classes.academic_year_id')
            ->where('tbl_classes.academic_year_id', $academicYearId)
            ->where('tbl_classes.is_active', 1)
            ->orderBy('tbl_classes.class_name', 'ASC')
            ->findAll();
    }
    /**
     * Get classes with available seats count
     */
    public function getWithAvailableSeats($academicYearId)
    {
        return $this->select('
                tbl_classes.*, 
                tbl_grade_levels.level_name,
                (SELECT COUNT(*) FROM tbl_enrollments 
                WHERE class_id = tbl_classes.id 
                AND status = "Ativo") as enrolled_count
            ')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
            ->where('tbl_classes.academic_year_id', $academicYearId)
            ->where('tbl_classes.is_active', 1)
            ->orderBy('tbl_classes.class_name', 'ASC')
            ->findAll();
    }
    /**
     * Get classes by level and year with enrolled count
     */
    public function getByLevelAndYearWithStats($levelId, $yearId)
    {
        return $this->select('
                tbl_classes.*, 
                tbl_grade_levels.level_name,
                (SELECT COUNT(*) FROM tbl_enrollments 
                WHERE class_id = tbl_classes.id 
                AND status = "Ativo") as enrolled_count
            ')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
            ->where('tbl_classes.grade_level_id', $levelId)
            ->where('tbl_classes.academic_year_id', $yearId)
            ->where('tbl_classes.is_active', 1)
            ->orderBy('tbl_classes.class_name', 'ASC')
            ->findAll();
    }
}