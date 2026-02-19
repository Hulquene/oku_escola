<?php
// app/Models/CourseDisciplineModel.php

namespace App\Models;

class CourseDisciplineModel extends BaseModel
{
    protected $table = 'tbl_course_disciplines';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'course_id',
        'discipline_id',
        'grade_level_id',
        'workload_hours',
        'is_mandatory',
        'semester'
        // NÃO incluir created_at e updated_at aqui!
    ];
    
    protected $validationRules = [
        'course_id' => 'required|numeric',
        'discipline_id' => 'required|numeric',
        'grade_level_id' => 'required|numeric',
        'workload_hours' => 'permit_empty|numeric|greater_than[0]|less_than[1000]',
        'is_mandatory' => 'permit_empty|in_list[0,1]',
        'semester' => 'permit_empty|in_list[1º,2º,Anual]'
    ];
    
    protected $validationMessages = [
        'course_id' => [
            'required' => 'O curso é obrigatório',
            'numeric' => 'Curso inválido'
        ],
        'discipline_id' => [
            'required' => 'A disciplina é obrigatória',
            'numeric' => 'Disciplina inválida'
        ],
        'grade_level_id' => [
            'required' => 'O nível de ensino é obrigatório',
            'numeric' => 'Nível de ensino inválido'
        ]
    ];
    
    // IMPORTANTE: Desabilitar timestamps COMPLETAMENTE
    protected $useTimestamps = false;
    
    // Remover estas linhas completamente:
    // protected $createdField = 'created_at';
    // protected $updatedField = null;
    
    /**
     * Get disciplines by course and grade level
     */
    public function getByCourseAndGrade($courseId, $gradeLevelId)
    {
        return $this->select('
                tbl_course_disciplines.*,
                tbl_disciplines.discipline_name,
                tbl_disciplines.discipline_code,
                tbl_disciplines.workload_hours as default_workload,
                tbl_disciplines.approval_grade
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_course_disciplines.discipline_id')
            ->where('tbl_course_disciplines.course_id', $courseId)
            ->where('tbl_course_disciplines.grade_level_id', $gradeLevelId)
            ->where('tbl_disciplines.is_active', 1)
            ->orderBy('tbl_disciplines.discipline_name', 'ASC')
            ->findAll();
    }
    
    /**
     * Get all disciplines for a course (all years)
     */
    public function getFullCourseCurriculum($courseId)
    {
        return $this->select('
                tbl_course_disciplines.*,
                tbl_disciplines.discipline_name,
                tbl_disciplines.discipline_code,
                tbl_grade_levels.level_name,
                tbl_grade_levels.grade_number
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_course_disciplines.discipline_id')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_course_disciplines.grade_level_id')
            ->where('tbl_course_disciplines.course_id', $courseId)
            ->where('tbl_disciplines.is_active', 1)
            ->orderBy('tbl_grade_levels.sort_order', 'ASC')
            ->orderBy('tbl_disciplines.discipline_name', 'ASC')
            ->findAll();
    }
    
    /**
     * Check if discipline is already assigned to course
     */
    public function isAssigned($courseId, $disciplineId, $gradeLevelId)
    {
        return $this->where('course_id', $courseId)
            ->where('discipline_id', $disciplineId)
            ->where('grade_level_id', $gradeLevelId)
            ->countAllResults() > 0;
    }
    
    /**
     * Get courses that offer a specific discipline
     */
    public function getCoursesByDiscipline($disciplineId, $gradeLevelId = null)
    {
        $builder = $this->select('
                tbl_courses.*,
                tbl_course_disciplines.workload_hours,
                tbl_course_disciplines.semester
            ')
            ->join('tbl_courses', 'tbl_courses.id = tbl_course_disciplines.course_id')
            ->where('tbl_course_disciplines.discipline_id', $disciplineId)
            ->where('tbl_courses.is_active', 1);
        
        if ($gradeLevelId) {
            $builder->where('tbl_course_disciplines.grade_level_id', $gradeLevelId);
        }
        
        return $builder->orderBy('tbl_courses.course_name', 'ASC')
            ->findAll();
    }
    
    /**
     * Get workload summary by course and grade
     */
    public function getWorkloadSummary($courseId, $gradeLevelId)
    {
        $result = $this->select('
                SUM(workload_hours) as total_workload,
                COUNT(*) as total_disciplines,
                SUM(CASE WHEN is_mandatory = 1 THEN 1 ELSE 0 END) as mandatory_count,
                SUM(CASE WHEN semester = "1º" THEN workload_hours ELSE 0 END) as first_semester,
                SUM(CASE WHEN semester = "2º" THEN workload_hours ELSE 0 END) as second_semester,
                SUM(CASE WHEN semester = "Anual" THEN workload_hours ELSE 0 END) as anual
            ')
            ->where('course_id', $courseId)
            ->where('grade_level_id', $gradeLevelId)
            ->first();
        
        return $result ?: (object)[
            'total_workload' => 0,
            'total_disciplines' => 0,
            'mandatory_count' => 0,
            'first_semester' => 0,
            'second_semester' => 0,
            'anual' => 0
        ];
    }
}