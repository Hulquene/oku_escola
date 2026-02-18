<?php
// app/Models/RequestableDocumentModel.php

namespace App\Models;

class RequestableDocumentModel extends BaseModel
{
    protected $table = 'tbl_requestable_documents';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'document_code',
        'document_name',
        'description',
        'category',
        'fee_amount',
        'processing_days',
        'available_for',
        'requires_approval',
        'template_path',
        'is_active'
    ];
    
    protected $validationRules = [
        'document_code' => 'required|is_unique[tbl_requestable_documents.document_code,id,{id}]|max_length[50]',
        'document_name' => 'required|max_length[255]',
        'category' => 'required|in_list[certificado,declaracao,atestado,historico,outro]',
        'fee_amount' => 'permit_empty|numeric|greater_than_equal_to[0]',
        'processing_days' => 'permit_empty|numeric|greater_than[0]|less_than[31]',
        'available_for' => 'required|in_list[all,students,teachers,guardians,staff]'
    ];
    
    protected $validationMessages = [
        'document_code' => [
            'required' => 'O código do documento é obrigatório',
            'is_unique' => 'Este código já está em uso'
        ],
        'document_name' => [
            'required' => 'O nome do documento é obrigatório'
        ]
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = null;
    
    /**
     * Get active requestable documents
     */
    public function getActive()
    {
        return $this->where('is_active', 1)
            ->orderBy('category', 'ASC')
            ->orderBy('document_name', 'ASC')
            ->findAll();
    }
    
    /**
     * Get by user type
     */
    public function getByUserType($userType)
    {
        return $this->where('is_active', 1)
            ->groupStart()
                ->where('available_for', $userType)
                ->orWhere('available_for', 'all')
            ->groupEnd()
            ->orderBy('category', 'ASC')
            ->orderBy('document_name', 'ASC')
            ->findAll();
    }
    
    /**
     * Get by code
     */
    public function getByCode($code)
    {
        return $this->where('document_code', $code)->first();
    }
    
    /**
     * Get categories for dropdown
     */
    public function getCategories()
    {
        return [
            'certificado' => 'Certificado',
            'declaracao' => 'Declaração',
            'atestado' => 'Atestado',
            'historico' => 'Histórico',
            'outro' => 'Outro'
        ];
    }
    
    /**
     * Get available for options
     */
    public function getAvailableForOptions()
    {
        return [
            'all' => 'Todos',
            'students' => 'Alunos',
            'teachers' => 'Professores',
            'guardians' => 'Encarregados',
            'staff' => 'Funcionários'
        ];
    }
    
    /**
     * Calculate total fees
     */
    public function calculateTotalFees($documentCodes = [])
    {
        if (empty($documentCodes)) {
            return 0;
        }
        
        $result = $this->select('SUM(fee_amount) as total')
            ->whereIn('document_code', $documentCodes)
            ->where('is_active', 1)
            ->first();
        
        return $result ? $result->total : 0;
    }
}