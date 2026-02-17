<?php

namespace App\Models;

class AcademicYearModel extends BaseModel
{
    protected $table = 'tbl_academic_years';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'year_name',
        'start_date',
        'end_date',
        'is_current',
        'is_active'
    ];
    
    protected $validationRules = [
        'id' => 'permit_empty|is_natural_no_zero',  // <-- ADICIONADO!
        'year_name' => 'required|is_unique[tbl_academic_years.year_name,id,{id}]',
        'start_date' => 'required|valid_date',
        'end_date' => 'required|valid_date'
    ];
    
    protected $validationMessages = [
        'year_name' => [
            'required' => 'O nome do ano letivo é obrigatório.',
            'is_unique' => 'Já existe um ano letivo com este nome. Por favor, escolha outro nome.'
        ],
        'start_date' => [
            'required' => 'A data de início é obrigatória.',
            'valid_date' => 'A data de início fornecida não é válida.'
        ],
        'end_date' => [
            'required' => 'A data de fim é obrigatória.',
            'valid_date' => 'A data de fim fornecida não é válida.'
        ]
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    
    /**
     * Get current academic year
     */
    public function getCurrent()
    {
        $current = $this->where('is_current', 1)
            ->where('is_active', 1)
            ->first();
            
        if (!$current) {
            // If no current, get the latest
            $current = $this->where('is_active', 1)
                ->orderBy('start_date', 'DESC')
                ->first();
        }
        
        return $current;
    }
    
    /**
     * Set current academic year
     */
    public function setCurrent($id)
    {
        $this->transStart();
        
        // Remove current from all
        $this->where('is_current', 1)->set(['is_current' => 0])->update();
        
        // Set new current
        $this->update($id, ['is_current' => 1]);
        
        $this->transComplete();
        
        return $this->transStatus();
    }
    
    /**
     * Get active academic years
     */
    public function getActive()
    {
        return $this->where('is_active', 1)
            ->orderBy('start_date', 'DESC')
            ->findAll();
    }
    
    /**
     * Check if date is within academic year
     */
    public function isWithinYear($yearId, $date)
    {
        $year = $this->find($yearId);
        
        if (!$year) {
            return false;
        }
        
        $timestamp = strtotime($date);
        return $timestamp >= strtotime($year->start_date) && 
               $timestamp <= strtotime($year->end_date);
    }
}