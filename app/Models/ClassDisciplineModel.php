<?php

namespace App\Models;

class ClassDisciplineModel extends BaseModel
{
    protected $table = 'tbl_class_disciplines';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'class_id',
        'discipline_id',
        'teacher_id',
        'workload_hours',
        'semester_id',
        'is_active'
    ];
    
    protected $validationRules = [
        'class_id' => 'required|numeric',
        'discipline_id' => 'required|numeric',
        'teacher_id' => 'permit_empty|numeric',
        'semester_id' => 'permit_empty|numeric'
    ];
    
    /**
     * Assign teacher to discipline
     */
    public function assignTeacher($classId, $disciplineId, $teacherId)
    {
        return $this->where('class_id', $classId)
            ->where('discipline_id', $disciplineId)
            ->set(['teacher_id' => $teacherId])
            ->update();
    }
    
    /**
     * Get disciplines by class with teacher info
     */
    public function getClassDisciplines($classId)
    {
        return $this->select('tbl_class_disciplines.*, tbl_disciplines.discipline_name, tbl_disciplines.discipline_code, tbl_users.first_name, tbl_users.last_name')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
            ->join('tbl_users', 'tbl_users.id = tbl_class_disciplines.teacher_id', 'left')
            ->where('tbl_class_disciplines.class_id', $classId)
            ->where('tbl_class_disciplines.is_active', 1)
            ->findAll();
    }
    
    /**
     * Get teacher schedule
     */
    public function getTeacherSchedule($teacherId, $semesterId = null)
    {
        $builder = $this->select('tbl_class_disciplines.*, tbl_classes.class_name, tbl_disciplines.discipline_name')
            ->join('tbl_classes', 'tbl_classes.id = tbl_class_disciplines.class_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
            ->where('tbl_class_disciplines.teacher_id', $teacherId);
        
        if ($semesterId) {
            $builder->where('tbl_class_disciplines.semester_id', $semesterId);
        }
        
        return $builder->findAll();
    }
    public function getByClassAndSemester($classId, $semesterId)
    {
        return $this->select('
                tbl_class_disciplines.*,
                tbl_disciplines.discipline_name,
                tbl_disciplines.discipline_code,
                CONCAT(tbl_users.first_name, " ", tbl_users.last_name) as teacher_name
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
            ->join('tbl_users', 'tbl_users.id = tbl_class_disciplines.teacher_id', 'left')
            ->where('tbl_class_disciplines.class_id', $classId)
            ->where('tbl_class_disciplines.semester_id', $semesterId)
            ->where('tbl_class_disciplines.is_active', 1)
            ->findAll();
    }
}