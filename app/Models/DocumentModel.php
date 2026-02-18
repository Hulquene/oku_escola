<?php
// app/Models/DocumentModel.php

namespace App\Models;

class DocumentModel extends BaseModel
{
    protected $table = 'tbl_documents';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'user_id',
        'user_type',
        'document_type',
        'document_name',
        'document_path',
        'document_size',
        'document_mime',
        'description',
        'is_verified',
        'verified_by',
        'verified_at',
        'verification_notes',
        'is_active'
    ];
    
    protected $validationRules = [
        'user_id' => 'required|numeric',
        'user_type' => 'required|in_list[student,teacher]',
        'document_type' => 'required',
        'document_name' => 'required',
        'document_path' => 'required'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    protected $deletedField = 'deleted_at';
    protected $useSoftDeletes = true;
    
    /**
     * Get documents by user
     */
    public function getByUser($userId, $userType)
    {
        return $this->where('user_id', $userId)
            ->where('user_type', $userType)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }
    
    /**
     * Get verified documents by user
     */
    public function getVerifiedByUser($userId, $userType)
    {
        return $this->where('user_id', $userId)
            ->where('user_type', $userType)
            ->where('is_verified', 1)
            ->where('is_active', 1)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }
    
    /**
     * Get pending documents (for admin)
     */
    public function getPending($limit = null)
    {
        $builder = $this->where('is_verified', 0)
            ->where('is_active', 1)
            ->orderBy('created_at', 'ASC');
        
        if ($limit) {
            $builder->limit($limit);
        }
        
        return $builder->findAll();
    }
    
    /**
     * Get documents by type
     */
    public function getByType($documentType, $userType = null)
    {
        $builder = $this->where('document_type', $documentType)
            ->where('is_active', 1);
        
        if ($userType) {
            $builder->where('user_type', $userType);
        }
        
        return $builder->orderBy('created_at', 'DESC')->findAll();
    }
    
    /**
     * Verify document
     */
    public function verify($id, $status, $notes = null, $verifiedBy = null)
    {
        $data = [
            'is_verified' => $status,
            'verification_notes' => $notes,
            'verified_at' => date('Y-m-d H:i:s')
        ];
        
        if ($verifiedBy) {
            $data['verified_by'] = $verifiedBy;
        }
        
        return $this->update($id, $data);
    }
    
    /**
     * Get document statistics
     */
    public function getStatistics($userId = null)
    {
        $builder = $this->select('
                COUNT(*) as total,
                SUM(CASE WHEN is_verified = 0 THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN is_verified = 1 THEN 1 ELSE 0 END) as verified,
                SUM(CASE WHEN is_verified = 2 THEN 1 ELSE 0 END) as rejected
            ');
        
        if ($userId) {
            $builder->where('user_id', $userId);
        }
        
        return $builder->first();
    }
    
    /**
     * Get monthly statistics (for admin reports)
     */
    public function getMonthlyStats($year = null)
    {
        $year = $year ?: date('Y');
        
        return $this->select("
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COUNT(*) as total,
                SUM(CASE WHEN is_verified = 1 THEN 1 ELSE 0 END) as verified,
                SUM(CASE WHEN is_verified = 0 THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN user_type = 'student' THEN 1 ELSE 0 END) as from_students,
                SUM(CASE WHEN user_type = 'teacher' THEN 1 ELSE 0 END) as from_teachers
            ")
            ->where('YEAR(created_at)', $year)
            ->groupBy("DATE_FORMAT(created_at, '%Y-%m')")
            ->orderBy('month', 'DESC')
            ->findAll();
    }
    
    /**
     * Search documents
     */
    public function search($query, $filters = [])
    {
        $builder = $this->groupStart()
                ->like('document_name', $query)
                ->orLike('description', $query)
                ->orLike('document_type', $query)
            ->groupEnd();
        
        if (!empty($filters['user_type'])) {
            $builder->where('user_type', $filters['user_type']);
        }
        
        if (!empty($filters['is_verified']) || $filters['is_verified'] === '0') {
            $builder->where('is_verified', $filters['is_verified']);
        }
        
        if (!empty($filters['date_from'])) {
            $builder->where('created_at >=', $filters['date_from'] . ' 00:00:00');
        }
        
        if (!empty($filters['date_to'])) {
            $builder->where('created_at <=', $filters['date_to'] . ' 23:59:59');
        }
        
        return $builder->orderBy('created_at', 'DESC')
            ->limit(50)
            ->findAll();
    }
    
    /**
     * Count documents by user type
     */
    public function countByUserType($userType)
    {
        return $this->where('user_type', $userType)
            ->where('is_active', 1)
            ->countAllResults();
    }
    
    /**
     * Get document with user info
     */
    public function getWithUser($id)
    {
        return $this->select('
                tbl_documents.*,
                tbl_users.username,
                tbl_users.first_name,
                tbl_users.last_name,
                tbl_users.email,
                CONCAT(tbl_users.first_name, " ", tbl_users.last_name) as user_fullname
            ')
            ->join('tbl_users', 'tbl_users.id = tbl_documents.user_id')
            ->where('tbl_documents.id', $id)
            ->first();
    }
}