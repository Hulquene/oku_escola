<?php

namespace App\Models;

class GradeWeightModel extends BaseModel
{
    protected $table = 'tbl_grade_weights';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'academic_year_id',
        'grade_level_id',
        'exam_board_id',
        'weight_percentage',
        'is_mandatory'
    ];
    
    protected $validationRules = [
        'academic_year_id' => 'required|numeric',
        'grade_level_id' => 'required|numeric',
        'exam_board_id' => 'required|numeric',
        'weight_percentage' => 'required|numeric|greater_than[0]|less_than[100]'
    ];
    
    protected $useTimestamps = true;
    
    /**
     * Get weights for a specific grade level
     */
    public function getByGradeLevel($gradeLevelId, $academicYearId = null)
    {
        if (!$academicYearId) {
            $yearModel = new AcademicYearModel();
            $currentYear = $yearModel->getCurrent();
            $academicYearId = $currentYear->id ?? null;
        }
        
        return $this->select('
                tbl_grade_weights.*,
                tbl_exam_boards.board_name,
                tbl_exam_boards.board_type,
                tbl_exam_boards.board_code
            ')
            ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_grade_weights.exam_board_id')
            ->where('tbl_grade_weights.grade_level_id', $gradeLevelId)
            ->where('tbl_grade_weights.academic_year_id', $academicYearId)
            ->where('tbl_exam_boards.is_active', 1)
            ->orderBy('tbl_exam_boards.id', 'ASC')
            ->findAll();
    }
    
    /**
     * Get total weight for a grade level
     */
    public function getTotalWeight($gradeLevelId, $academicYearId = null)
    {
        $weights = $this->getByGradeLevel($gradeLevelId, $academicYearId);
        $total = 0;
        
        foreach ($weights as $weight) {
            $total += $weight->weight_percentage;
        }
        
        return $total;
    }
    
    /**
     * Validate total weight equals 100%
     */
    public function validateTotalWeight($gradeLevelId, $academicYearId = null)
    {
        $total = $this->getTotalWeight($gradeLevelId, $academicYearId);
        return abs($total - 100) < 0.01; // Allow small floating point errors
    }
    
    /**
     * Get default weights for a new academic year
     */
    public function getDefaultWeights()
    {
        return [
            ['board_code' => 'AC1', 'weight' => 10],
            ['board_code' => 'AC2', 'weight' => 10],
            ['board_code' => 'AC3', 'weight' => 10],
            ['board_code' => 'EX-TRI', 'weight' => 30],
            ['board_code' => 'EX-FIN', 'weight' => 40]
        ];
    }
}