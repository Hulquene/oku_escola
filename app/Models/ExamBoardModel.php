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
        'id' => 'permit_empty|is_natural_no_zero',  // <-- ADICIONAR ESTA LINHA!
        'board_name' => 'required|min_length[3]|max_length[100]',
        'board_code' => 'required|min_length[2]|max_length[50]|is_unique[tbl_exam_boards.board_code,id,{id}]',
        'board_type' => 'required|in_list[Normal,Recurso,Especial,Final,Admissão]',
        'weight' => 'permit_empty|numeric|greater_than[0]|less_than[11]',
        'is_active' => 'permit_empty|in_list[0,1]'
    ];
    
    protected $validationMessages = [
        'board_name' => [
            'required' => 'O nome do tipo de exame é obrigatório',
            'min_length' => 'O nome deve ter pelo menos 3 caracteres',
            'max_length' => 'O nome não pode ter mais de 100 caracteres'
        ],
        'board_code' => [
            'required' => 'O código é obrigatório',
            'min_length' => 'O código deve ter pelo menos 2 caracteres',
            'max_length' => 'O código não pode ter mais de 50 caracteres',
            'is_unique' => 'Este código já está em uso'
        ],
        'board_type' => [
            'required' => 'O tipo é obrigatório',
            'in_list' => 'Tipo inválido'
        ],
        'weight' => [
            'numeric' => 'O peso deve ser um número',
            'greater_than' => 'O peso deve ser maior que 0',
            'less_than' => 'O peso não pode ser maior que 10'
        ]
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