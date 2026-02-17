<?php

namespace App\Models;

class DisciplineModel extends BaseModel
{
    protected $table = 'tbl_disciplines';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'discipline_name',
        'discipline_code',
        'discipline_type',
        'workload_hours',
        'min_grade',
        'max_grade',
        'approval_grade',
        'description',
        'is_active'
    ];
    
    protected $validationRules = [
        'discipline_name' => 'required',
        'discipline_code' => 'required|is_unique[tbl_disciplines.discipline_code,id,{id}]',
        'workload_hours' => 'permit_empty|numeric'
    ];
    
    /**
     * Get active disciplines
     */
    public function getActive()
    {
        return $this->where('is_active', 1)
            ->orderBy('discipline_name', 'ASC')
            ->findAll();
    }
    
    /**
     * Get disciplines by class
     */
    public function getByClass($classId)
    {
        return $this->select('tbl_disciplines.*, tbl_class_disciplines.teacher_id, tbl_class_disciplines.workload_hours')
            ->join('tbl_class_disciplines', 'tbl_class_disciplines.discipline_id = tbl_disciplines.id')
            ->where('tbl_class_disciplines.class_id', $classId)
            ->where('tbl_disciplines.is_active', 1)
            ->orderBy('tbl_disciplines.discipline_name', 'ASC')
            ->findAll();
    }
    
    /**
     * Get disciplines by teacher
     */
    public function getByTeacher($teacherId, $academicYearId = null)
    {
        $builder = $this->select('tbl_disciplines.*, tbl_classes.class_name, tbl_classes.id as class_id')
            ->join('tbl_class_disciplines', 'tbl_class_disciplines.discipline_id = tbl_disciplines.id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_class_disciplines.class_id')
            ->where('tbl_class_disciplines.teacher_id', $teacherId)
            ->where('tbl_disciplines.is_active', 1);
        
        if ($academicYearId) {
            $builder->where('tbl_classes.academic_year_id', $academicYearId);
        }
        
        return $builder->orderBy('tbl_disciplines.discipline_name', 'ASC')
            ->findAll();
    }
}