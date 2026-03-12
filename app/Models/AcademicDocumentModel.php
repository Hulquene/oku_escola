<?php
// app/Models/AcademicDocumentModel.php

namespace App\Models;

use CodeIgniter\Model;

class AcademicDocumentModel extends Model
{
    protected $table = 'tbl_academic_documents';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'student_id',
        'enrollment_id',
        'document_type',
        'document_number',
        'issue_date',
        'file_path',
        'status',
        'issued_by',
        'observations'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    /**
     * Gerar número único para certificado/declaração
     */
    public function generateDocumentNumber($type)
    {
        $prefix = ($type == 'certificate') ? 'CERT' : 'DECL';
        $year = date('Y');
        $last = $this->select('document_number')
            ->like('document_number', "{$prefix}{$year}", 'after')
            ->orderBy('id', 'DESC')
            ->first();
        
        if ($last) {
            $lastNumber = intval(substr($last['document_number'], -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return "{$prefix}{$year}{$newNumber}";
    }
}