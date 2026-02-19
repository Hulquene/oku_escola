<?php
// app/Models/GradeLevelModel.php
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
        'is_course_start',   
        'is_course_end',       
        'course_type',      
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
    /**
     * Get high school levels (Ensino Médio)
     */
    public function getHighSchoolLevels()
    {
        return $this->whereIn('education_level', ['2º Ciclo', 'Ensino Médio'])
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->findAll();
    }
    
    /**
     * Get course start levels (e.g., 10ª classe)
     */
    public function getCourseStartLevels()
    {
        return $this->where('is_course_start', 1)
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->findAll();
    }
    
    /**
     * Get course end levels (e.g., 12ª or 13ª classe)
     */
    public function getCourseEndLevels()
    {
        return $this->where('is_course_end', 1)
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->findAll();
    }
    /**
     * Check if level is part of a course (high school)
     */
    public function isCourseLevel($levelId)
    {
        $level = $this->find($levelId);
        return $level && in_array($level->education_level, ['2º Ciclo', 'Ensino Médio']);
    }
}