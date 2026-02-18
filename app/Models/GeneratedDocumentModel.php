<?php
// app/Models/GeneratedDocumentModel.php

namespace App\Models;

class GeneratedDocumentModel extends BaseModel
{
    protected $table = 'tbl_generated_documents';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'request_id',
        'document_path',
        'document_name',
        'document_size',
        'document_mime',
        'generated_by',
        'download_count'
    ];
    
    protected $validationRules = [
        'request_id' => 'required|numeric',
        'document_path' => 'required',
        'document_name' => 'required',
        'document_mime' => 'permit_empty'
    ];
    
    protected $validationMessages = [
        'request_id' => [
            'required' => 'O ID da solicitação é obrigatório',
            'numeric' => 'O ID da solicitação deve ser um número'
        ],
        'document_path' => [
            'required' => 'O caminho do documento é obrigatório'
        ],
        'document_name' => [
            'required' => 'O nome do documento é obrigatório'
        ]
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'generated_at';
    protected $updatedField = null; // Não tem updated_at
    
    /**
     * Get documents by request ID
     */
    public function getByRequest($requestId)
    {
        return $this->where('request_id', $requestId)
            ->orderBy('generated_at', 'DESC')
            ->findAll();
    }
    
    /**
     * Get document with generator info
     */
    public function getWithGenerator($id)
    {
        return $this->select('
                tbl_generated_documents.*,
                tbl_users.first_name,
                tbl_users.last_name,
                tbl_users.email,
                CONCAT(tbl_users.first_name, " ", tbl_users.last_name) as generated_by_name
            ')
            ->join('tbl_users', 'tbl_users.id = tbl_generated_documents.generated_by', 'left')
            ->where('tbl_generated_documents.id', $id)
            ->first();
    }
    
    /**
     * Increment download count
     */
    public function incrementDownloadCount($id)
    {
        $document = $this->find($id);
        if ($document) {
            $newCount = ($document->download_count ?? 0) + 1;
            return $this->update($id, ['download_count' => $newCount]);
        }
        return false;
    }
    
    /**
     * Get statistics for a request
     */
    public function getStatsByRequest($requestId)
    {
        return $this->select('
                COUNT(*) as total_documents,
                SUM(download_count) as total_downloads
            ')
            ->where('request_id', $requestId)
            ->first();
    }
    
    /**
     * Delete document and file
     */
    public function deleteWithFile($id)
    {
        $document = $this->find($id);
        
        if ($document) {
            // Delete physical file
            if (file_exists($document->document_path)) {
                unlink($document->document_path);
            }
            
            // Delete record
            return $this->delete($id);
        }
        
        return false;
    }
    
    /**
     * Get recent generated documents
     */
    public function getRecent($limit = 10)
    {
        return $this->select('
                tbl_generated_documents.*,
                tbl_document_requests.request_number,
                tbl_users.first_name,
                tbl_users.last_name,
                CONCAT(tbl_users.first_name, " ", tbl_users.last_name) as user_name
            ')
            ->join('tbl_document_requests', 'tbl_document_requests.id = tbl_generated_documents.request_id')
            ->join('tbl_users', 'tbl_users.id = tbl_document_requests.user_id')
            ->orderBy('tbl_generated_documents.generated_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }
    
    /**
     * Get total downloads by period
     */
    public function getTotalDownloads($startDate = null, $endDate = null)
    {
        $builder = $this->select('SUM(download_count) as total');
        
        if ($startDate) {
            $builder->where('generated_at >=', $startDate . ' 00:00:00');
        }
        
        if ($endDate) {
            $builder->where('generated_at <=', $endDate . ' 23:59:59');
        }
        
        $result = $builder->first();
        return $result->total ?? 0;
    }
    
    /**
     * Get monthly generation statistics
     */
    public function getMonthlyStats($year = null)
    {
        $year = $year ?: date('Y');
        
        return $this->select("
                DATE_FORMAT(generated_at, '%Y-%m') as month,
                COUNT(*) as total,
                SUM(download_count) as downloads
            ")
            ->where('YEAR(generated_at)', $year)
            ->groupBy("DATE_FORMAT(generated_at, '%Y-%m')")
            ->orderBy('month', 'ASC')
            ->findAll();
    }
}