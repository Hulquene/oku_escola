<?php
// app/Models/CourseModel.php

namespace App\Models;

class CourseModel extends BaseModel
{
    protected $table = 'tbl_courses';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'course_name',
        'course_code',
        'course_type',
        'description',
        'duration_years',
        'start_grade_id',
        'end_grade_id',
        'is_active'
    ];
    
    protected $validationRules = [
        'id' => 'permit_empty|is_natural_no_zero',
        'course_name' => 'required|min_length[3]|max_length[100]',
        'course_code' => 'required|min_length[2]|max_length[20]|is_unique[tbl_courses.course_code,id,{id}]',
        'course_type' => 'required|in_list[Ciências,Humanidades,Económico-Jurídico,Técnico,Profissional,Outro]',
        'duration_years' => 'permit_empty|numeric|greater_than[0]|less_than[6]',
        'start_grade_id' => 'required|numeric',
        'end_grade_id' => 'required|numeric',
        'is_active' => 'permit_empty|in_list[0,1]'
    ];
    
    protected $validationMessages = [
        'course_name' => [
            'required' => 'O nome do curso é obrigatório',
            'min_length' => 'O nome deve ter pelo menos 3 caracteres',
            'max_length' => 'O nome não pode ter mais de 100 caracteres'
        ],
        'course_code' => [
            'required' => 'O código do curso é obrigatório',
            'min_length' => 'O código deve ter pelo menos 2 caracteres',
            'max_length' => 'O código não pode ter mais de 20 caracteres',
            'is_unique' => 'Este código de curso já está em uso'
        ],
        'course_type' => [
            'required' => 'O tipo de curso é obrigatório',
            'in_list' => 'Tipo de curso inválido'
        ],
        'start_grade_id' => [
            'required' => 'O nível inicial é obrigatório',
            'numeric' => 'Nível inicial inválido'
        ],
        'end_grade_id' => [
            'required' => 'O nível final é obrigatório',
            'numeric' => 'Nível final inválido'
        ]
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    /**
     * Get active courses
     */
    public function getActive()
    {
        return $this->where('is_active', 1)
            ->orderBy('course_name', 'ASC')
            ->findAll();
    }
    
    /**
     * Get courses by type
     */
    public function getByType($type)
    {
        return $this->where('course_type', $type)
            ->where('is_active', 1)
            ->orderBy('course_name', 'ASC')
            ->findAll();
    }
    
    /**
     * Get courses for a specific grade level
     */
    public function getByGradeLevel($gradeLevelId)
    {
        return $this->where('start_grade_id <=', $gradeLevelId)
            ->where('end_grade_id >=', $gradeLevelId)
            ->where('is_active', 1)
            ->orderBy('course_name', 'ASC')
            ->findAll();
    }
    
    /**
     * Get courses for high school (Ensino Médio)
     */
    public function getHighSchoolCourses()
    {
        return $this->where('start_grade_id >=', 13) // 10ª classe é ID 13
            ->where('is_active', 1)
            ->orderBy('course_name', 'ASC')
            ->findAll();
    }
    
    /**
     * Get course with grade levels
     */
    public function getWithGradeLevels($id)
    {
        return $this->select('
                tbl_courses.*,
                start_level.level_name as start_level_name,
                end_level.level_name as end_level_name,
                start_level.education_level as start_education_level,
                end_level.education_level as end_education_level
            ')
            ->join('tbl_grade_levels as start_level', 'start_level.id = tbl_courses.start_grade_id')
            ->join('tbl_grade_levels as end_level', 'end_level.id = tbl_courses.end_grade_id')
            ->where('tbl_courses.id', $id)
            ->first();
    }
    
    /**
     * Get course statistics
     */
    public function getStatistics()
    {
        $total = $this->countAll();
        $active = $this->where('is_active', 1)->countAllResults();
        
        $byType = $this->select('course_type, COUNT(*) as total')
            ->where('is_active', 1)
            ->groupBy('course_type')
            ->findAll();
        
        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $total - $active,
            'by_type' => $byType
        ];
    }
}