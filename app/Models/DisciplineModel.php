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
        'id' => 'permit_empty|is_natural_no_zero',
        'discipline_name' => 'required|min_length[3]|max_length[255]',
        'discipline_code' => 'required|min_length[2]|max_length[50]|is_unique[tbl_disciplines.discipline_code,id,{id}]',
        'discipline_type' => 'permit_empty|in_list[Obrigatória,Opcional,Complementar]',
        'workload_hours' => 'permit_empty|numeric|greater_than[0]|less_than[1000]',
        'min_grade' => 'permit_empty|numeric|greater_than_equal_to[0]',
        'max_grade' => 'permit_empty|numeric|greater_than[0]',
        'approval_grade' => 'permit_empty|numeric|greater_than_equal_to[0]',
        'is_active' => 'permit_empty|in_list[0,1]'
    ];
    
    protected $validationMessages = [
        'discipline_name' => [
            'required' => 'O nome da disciplina é obrigatório',
            'min_length' => 'O nome da disciplina deve ter pelo menos 3 caracteres',
            'max_length' => 'O nome da disciplina não pode ter mais de 255 caracteres'
        ],
        'discipline_code' => [
            'required' => 'O código da disciplina é obrigatório',
            'min_length' => 'O código deve ter pelo menos 2 caracteres',
            'max_length' => 'O código não pode ter mais de 50 caracteres',
            'is_unique' => 'Este código de disciplina já está em uso'
        ],
        'discipline_type' => [
            'in_list' => 'Tipo de disciplina inválido. Deve ser Obrigatória, Opcional ou Complementar'
        ],
        'workload_hours' => [
            'numeric' => 'Carga horária deve ser um número',
            'greater_than' => 'Carga horária deve ser maior que 0',
            'less_than' => 'Carga horária não pode ser maior que 1000'
        ],
        'min_grade' => [
            'numeric' => 'Nota mínima deve ser um número',
            'greater_than_equal_to' => 'Nota mínima não pode ser negativa'
        ],
        'max_grade' => [
            'numeric' => 'Nota máxima deve ser um número',
            'greater_than' => 'Nota máxima deve ser maior que 0'
        ],
        'approval_grade' => [
            'numeric' => 'Nota de aprovação deve ser um número',
            'greater_than_equal_to' => 'Nota de aprovação não pode ser negativa'
        ]
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

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
        return $this->select('
                tbl_disciplines.*, 
                tbl_class_disciplines.teacher_id, 
                tbl_class_disciplines.workload_hours as class_workload,
                tbl_users.first_name as teacher_first_name,
                tbl_users.last_name as teacher_last_name
            ')
            ->join('tbl_class_disciplines', 'tbl_class_disciplines.discipline_id = tbl_disciplines.id')
            ->join('tbl_users', 'tbl_users.id = tbl_class_disciplines.teacher_id', 'left')
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
        $builder = $this->select('
                tbl_disciplines.*, 
                tbl_classes.class_name, 
                tbl_classes.id as class_id,
                tbl_academic_years.year_name
            ')
            ->join('tbl_class_disciplines', 'tbl_class_disciplines.discipline_id = tbl_disciplines.id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_class_disciplines.class_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_classes.academic_year_id')
            ->where('tbl_class_disciplines.teacher_id', $teacherId)
            ->where('tbl_disciplines.is_active', 1);
        
        if ($academicYearId) {
            $builder->where('tbl_classes.academic_year_id', $academicYearId);
        }
        
        return $builder->orderBy('tbl_disciplines.discipline_name', 'ASC')
            ->findAll();
    }
    
    /**
     * Check if discipline is assigned to any class
     */
    public function isAssigned($disciplineId)
    {
        $classDisciplineModel = new \App\Models\ClassDisciplineModel();
        return $classDisciplineModel->where('discipline_id', $disciplineId)->countAllResults() > 0;
    }
    
    /**
     * Get total count by type
     */
    public function countByType($type = null)
    {
        if ($type) {
            return $this->where('discipline_type', $type)->countAllResults();
        }
        return $this->countAll();
    }
    
    /**
     * Get disciplines with pending assignments
     */
    public function getUnassigned($academicYearId = null)
    {
        $builder = $this->select('tbl_disciplines.*')
            ->where('tbl_disciplines.is_active', 1)
            ->whereNotIn('tbl_disciplines.id', function($builder) use ($academicYearId) {
                $builder->select('discipline_id')
                    ->from('tbl_class_disciplines')
                    ->join('tbl_classes', 'tbl_classes.id = tbl_class_disciplines.class_id');
                
                if ($academicYearId) {
                    $builder->where('tbl_classes.academic_year_id', $academicYearId);
                }
            })
            ->orderBy('tbl_disciplines.discipline_name', 'ASC');
        
        return $builder->findAll();
    }
    
    /**
     * Search disciplines
     */
    public function search($query)
    {
        return $this->groupStart()
                ->like('discipline_name', $query)
                ->orLike('discipline_code', $query)
                ->orLike('description', $query)
            ->groupEnd()
            ->where('is_active', 1)
            ->orderBy('discipline_name', 'ASC')
            ->limit(20)
            ->findAll();
    }
}