<?php

namespace App\Models;

class ExamBoardModel extends BaseModel
{
    protected $table = 'tbl_exam_boards';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'board_name',
        'board_code',
        'board_type',
        'weight',
        'is_active'
    ];
    
    protected $validationRules = [
        'board_name' => 'required',
        'board_code' => 'required|is_unique[tbl_exam_boards.board_code,id,{id}]',
        'board_type' => 'required',
        'weight' => 'permit_empty|numeric'
    ];
    
    /**
     * Get active boards
     */
    public function getActive()
    {
        return $this->where('is_active', 1)
            ->orderBy('board_type', 'ASC')
            ->orderBy('weight', 'DESC')
            ->findAll();
    }
    
    /**
     * Get boards by type
     */
    public function getByType($type)
    {
        return $this->where('board_type', $type)
            ->where('is_active', 1)
            ->orderBy('weight', 'DESC')
            ->findAll();
    }
}