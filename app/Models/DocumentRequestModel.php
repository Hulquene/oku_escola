<?php
// app/Models/DocumentRequestModel.php

namespace App\Models;

class DocumentRequestModel extends BaseModel
{
    protected $table = 'tbl_document_requests';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'user_id',
        'user_type',
        'request_number',
        'document_type',
        'document_code',
        'purpose',
        'quantity',
        'format',
        'delivery_method',
        'delivery_address',
        'fee_amount',
        'payment_status',
        'payment_reference',
        'payment_date',
        'status',
        'processed_by',
        'processed_at',
        'ready_at',
        'delivered_at',
        'rejection_reason',
        'notes'
    ];
    
    protected $validationRules = [
        'user_id' => 'required|numeric',
        'user_type' => 'required|in_list[student,teacher,guardian,staff]',
        'request_number' => 'required|is_unique[tbl_document_requests.request_number,id,{id}]',
        'document_type' => 'required',
        'document_code' => 'required|max_length[50]', 
        'purpose' => 'required',
        'quantity' => 'numeric|greater_than[0]|less_than[11]',
        'format' => 'required|in_list[digital,impresso,ambos]',
        'delivery_method' => 'required|in_list[retirar,email,correio]',
        'status' => 'required|in_list[pending,processing,ready,delivered,cancelled,rejected]',
        'payment_status' => 'required|in_list[pending,paid,waived]'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    /**
     * Generate request number
     */
    public function generateRequestNumber()
    {
        $year = date('Y');
        $month = date('m');
        $last = $this->select('request_number')
            ->like('request_number', "REQ{$year}{$month}", 'after')
            ->orderBy('id', 'DESC')
            ->first();
        
        if ($last) {
            $lastNumber = intval(substr($last->request_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return "REQ{$year}{$month}{$newNumber}";
    }
    
    /**
     * Get pending requests
     */
    public function getPending()
    {
        return $this->where('status', 'pending')
            ->orderBy('created_at', 'ASC')
            ->findAll();
    }
    
    /**
     * Get requests by user
     */
    public function getByUser($userId, $userType = null)
    {
        $builder = $this->where('user_id', $userId);
        
        if ($userType) {
            $builder->where('user_type', $userType);
        }
        
        return $builder->orderBy('created_at', 'DESC')->findAll();
    }
    
    /**
     * Get requests by status
     */
    public function getByStatus($status, $userType = null)
    {
        $builder = $this->where('status', $status);
        
        if ($userType) {
            $builder->where('user_type', $userType);
        }
        
        return $builder->orderBy('created_at', 'DESC')->findAll();
    }
    
    /**
     * Get request with user info
     */
    public function getWithUser($id)
    {
        return $this->select('
                tbl_document_requests.*,
                tbl_users.username,
                tbl_users.first_name,
                tbl_users.last_name,
                tbl_users.email,
                tbl_users.phone,
                CONCAT(tbl_users.first_name, " ", tbl_users.last_name) as user_fullname
            ')
            ->join('tbl_users', 'tbl_users.id = tbl_document_requests.user_id')
            ->where('tbl_document_requests.id', $id)
            ->first();
    }
    
    /**
     * Process request
     */
    public function process($id, $processedBy)
    {
        return $this->update($id, [
            'status' => 'processing',
            'processed_by' => $processedBy,
            'processed_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Mark as ready
     */
    public function markAsReady($id)
    {
        return $this->update($id, [
            'status' => 'ready',
            'ready_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Mark as delivered
     */
    public function markAsDelivered($id)
    {
        return $this->update($id, [
            'status' => 'delivered',
            'delivered_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Reject request
     */
    public function reject($id, $reason, $processedBy)
    {
        return $this->update($id, [
            'status' => 'rejected',
            'rejection_reason' => $reason,
            'processed_by' => $processedBy,
            'processed_at' => date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Register payment
     */
    public function registerPayment($id, $reference, $paymentDate = null)
    {
        return $this->update($id, [
            'payment_status' => 'paid',
            'payment_reference' => $reference,
            'payment_date' => $paymentDate ?: date('Y-m-d H:i:s')
        ]);
    }
    
    /**
     * Get statistics
     */
    public function getStatistics($year = null)
    {
        $year = $year ?: date('Y');
        
        $stats = $this->select('
                COUNT(*) as total,
                SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = "processing" THEN 1 ELSE 0 END) as processing,
                SUM(CASE WHEN status = "ready" THEN 1 ELSE 0 END) as ready,
                SUM(CASE WHEN status = "delivered" THEN 1 ELSE 0 END) as delivered,
                SUM(CASE WHEN status = "cancelled" THEN 1 ELSE 0 END) as cancelled,
                SUM(CASE WHEN status = "rejected" THEN 1 ELSE 0 END) as rejected,
                SUM(CASE WHEN payment_status = "pending" THEN 1 ELSE 0 END) as payment_pending,
                SUM(CASE WHEN payment_status = "paid" THEN 1 ELSE 0 END) as payment_paid,
                SUM(fee_amount) as total_fees,
                SUM(CASE WHEN payment_status = "paid" THEN fee_amount ELSE 0 END) as collected_fees
            ')
            ->where('YEAR(created_at)', $year)
            ->first();
        
        return $stats;
    }
    
    /**
     * Get monthly trends
     */
    public function getMonthlyTrends($year = null)
    {
        $year = $year ?: date('Y');
        
        return $this->select("
                DATE_FORMAT(created_at, '%Y-%m') as month,
                COUNT(*) as total,
                SUM(CASE WHEN status = 'delivered' THEN 1 ELSE 0 END) as completed,
                SUM(fee_amount) as total_fees,
                SUM(CASE WHEN payment_status = 'paid' THEN fee_amount ELSE 0 END) as collected_fees
            ")
            ->where('YEAR(created_at)', $year)
            ->groupBy("DATE_FORMAT(created_at, '%Y-%m')")
            ->orderBy('month', 'ASC')
            ->findAll();
    }
}