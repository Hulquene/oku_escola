<?php

namespace App\Models;

class GradeLevelModel extends BaseModel
{
    protected $table = 'tbl_grade_levels';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'level_name',
        'level_code',
        'education_level',
        'grade_number',
        'sort_order',
        'is_active'
    ];
    
    protected $validationRules = [
        'level_name' => 'required',
        'level_code' => 'required|is_unique[tbl_grade_levels.level_code,id,{id}]',
        'education_level' => 'required',
        'grade_number' => 'required|numeric'
    ];
    
    /**
     * Get levels by education level
     */
    public function getByEducationLevel($educationLevel)
    {
        return $this->where('education_level', $educationLevel)
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->findAll();
    }
    
    /**
     * Get all active levels ordered
     */
    public function getActive()
    {
        return $this->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->findAll();
    }
    
    /**
     * Get next level
     */
    public function getNextLevel($currentLevelId)
    {
        $current = $this->find($currentLevelId);
        
        if (!$current) {
            return null;
        }
        
        return $this->where('sort_order >', $current->sort_order)
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->first();
    }
    
    /**
     * Get previous level
     */
    public function getPreviousLevel($currentLevelId)
    {
        $current = $this->find($currentLevelId);
        
        if (!$current) {
            return null;
        }
        
        return $this->where('sort_order <', $current->sort_order)
            ->where('is_active', 1)
            ->orderBy('sort_order', 'DESC')
            ->first();
    }
}