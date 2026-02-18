<?php
// app/Models/DocumentTypeModel.php

namespace App\Models;

class DocumentTypeModel extends BaseModel
{
    protected $table = 'tbl_document_types';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'type_code',
        'type_name',
        'type_category',
        'allowed_extensions',
        'max_size',
        'is_required',
        'for_student',
        'for_teacher',
        'description',
        'sort_order',
        'is_active'
    ];
    
    protected $validationRules = [
        'type_code' => 'required|is_unique[tbl_document_types.type_code,id,{id}]|max_length[50]',
        'type_name' => 'required|max_length[100]',
        'type_category' => 'required|in_list[identificacao,academico,pessoal,outro]',
        'allowed_extensions' => 'required|max_length[255]',
        'max_size' => 'permit_empty|numeric|greater_than[0]|less_than[20480]',
        'sort_order' => 'permit_empty|numeric'
    ];
    
    protected $validationMessages = [
        'type_code' => [
            'required' => 'O código do tipo é obrigatório',
            'is_unique' => 'Este código já está em uso',
            'max_length' => 'O código não pode ter mais de 50 caracteres'
        ],
        'type_name' => [
            'required' => 'O nome do tipo é obrigatório',
            'max_length' => 'O nome não pode ter mais de 100 caracteres'
        ],
        'type_category' => [
            'required' => 'A categoria é obrigatória',
            'in_list' => 'Categoria inválida'
        ],
        'allowed_extensions' => [
            'required' => 'As extensões permitidas são obrigatórias'
        ]
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = null;
    
    /**
     * Get types for student
     */
    public function getForStudent()
    {
        return $this->where('for_student', 1)
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('type_name', 'ASC')
            ->findAll();
    }
    
    /**
     * Get types for teacher
     */
    public function getForTeacher()
    {
        return $this->where('for_teacher', 1)
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('type_name', 'ASC')
            ->findAll();
    }
    
    /**
     * Get required types for a user type
     */
    public function getRequired($userType)
    {
        $field = $userType == 'student' ? 'for_student' : 'for_teacher';
        
        return $this->where($field, 1)
            ->where('is_required', 1)
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->orderBy('type_name', 'ASC')
            ->findAll();
    }
    
    /**
     * Get by code
     */
    public function getByCode($code)
    {
        return $this->where('type_code', $code)->first();
    }
    
    /**
     * Get allowed extensions array
     */
    public function getAllowedExtensionsArray($typeCode)
    {
        $type = $this->getByCode($typeCode);
        if ($type && $type->allowed_extensions) {
            return array_map('trim', explode(',', $type->allowed_extensions));
        }
        return ['jpg', 'jpeg', 'png', 'pdf']; // default
    }
    
    /**
     * Get categories for dropdown
     */
    public function getCategories()
    {
        return [
            'identificacao' => 'Identificação',
            'academico' => 'Académico',
            'pessoal' => 'Pessoal',
            'outro' => 'Outro'
        ];
    }
}