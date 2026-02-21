<?php

namespace App\Models;

class SemesterModel extends BaseModel
{
    protected $table = 'tbl_semesters';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'academic_year_id',
        'semester_name',
        'semester_type',
        'start_date',
        'end_date',
        'is_current',
        'is_active',
        'status'  // NOVO CAMPO ADICIONADO
    ];
    
    protected $validationRules = [
        'id' => 'permit_empty|is_natural_no_zero',
        'academic_year_id' => 'required|numeric',
        'semester_name' => 'required|min_length[3]|max_length[100]',
        'semester_type' => 'required|in_list[1º Trimestre,2º Trimestre,3º Trimestre,1º Semestre,2º Semestre]',
        'start_date' => 'required|valid_date',
        'end_date' => 'required|valid_date',
        'status' => 'permit_empty|in_list[ativo,inativo,processado,concluido]'
    ];
    
    protected $validationMessages = [
        'semester_name' => [
            'required' => 'O nome do período é obrigatório.',
            'min_length' => 'O nome do período deve ter pelo menos 3 caracteres.',
            'max_length' => 'O nome do período não pode ter mais de 100 caracteres.'
        ],
        'semester_type' => [
            'required' => 'O tipo de período é obrigatório.',
            'in_list' => 'O tipo de período selecionado é inválido.'
        ],
        'status' => [
            'in_list' => 'O status selecionado é inválido.'
        ]
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    /**
     * Get semesters by academic year
     */
    public function getByAcademicYear($academicYearId)
    {
        return $this->where('academic_year_id', $academicYearId)
            ->where('is_active', 1)
            ->orderBy('start_date', 'ASC')
            ->findAll();
    }
    
    /**
     * Get current semester
     */
    public function getCurrent()
    {
        return $this->where('is_current', 1)
            ->where('is_active', 1)
            ->first();
    }
    
    /**
     * Set current semester
     */
    public function setCurrent($id)
    {
        $semester = $this->find($id);
        
        if (!$semester) {
            return false;
        }
        
        $this->transStart();
        
        // Remove current from all in same academic year
        $this->where('academic_year_id', $semester->academic_year_id)
            ->set(['is_current' => 0])
            ->update();
        
        // Set new current
        $this->update($id, ['is_current' => 1]);
        
        $this->transComplete();
        
        return $this->transStatus();
    }
    
    /**
     * Get active semesters
     */
    public function getActive()
    {
        $academicYearModel = new AcademicYearModel();
        $currentYear = $academicYearModel->getCurrent();
        
        if (!$currentYear) {
            return [];
        }
        
        return $this->where('academic_year_id', $currentYear->id)
            ->where('is_active', 1)
            ->orderBy('start_date', 'ASC')
            ->findAll();
    }
    
    /**
     * Check if semester has exams
     */
    public function hasExams($id)
    {
        $examModel = new ExamModel();
        return $examModel->where('semester_id', $id)->countAllResults() > 0;
    }
    
    /**
     * Get semester duration in days
     */
    public function getDuration($id)
    {
        $semester = $this->find($id);
        
        if (!$semester) {
            return 0;
        }
        
        $start = new \DateTime($semester->start_date);
        $end = new \DateTime($semester->end_date);
        $interval = $start->diff($end);
        
        return $interval->days + 1;
    }
    
    /**
     * Update semester status
     */
    public function updateStatus($id, $status)
    {
        return $this->update($id, ['status' => $status]);
    }
}