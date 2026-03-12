<?php
// app/Models/EmailLogModel.php

namespace App\Models;

use CodeIgniter\Model;

class EmailLogModel extends Model
{
    protected $table = 'tbl_email_logs';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'recipient',
        'recipient_name',
        'recipient_id',
        'recipient_type',
        'subject',
        'template',
        'status',
        'sent_by',
        'ip_address'
    ];
    
    protected $useTimestamps = false;
    protected $returnType = 'array';
    
    protected $validationRules = [
        'recipient' => 'required|valid_email|max_length[255]',
        'recipient_name' => 'required|max_length[255]',
        'subject' => 'required|max_length[255]',
        'status' => 'required|in_list[sent,failed,queued]'
    ];
    
    protected $validationMessages = [
        'recipient' => [
            'required' => 'O email do destinatário é obrigatório',
            'valid_email' => 'Por favor, insira um email válido'
        ]
    ];
    
    /**
     * Get logs by recipient
     */
    public function getByRecipient($email)
    {
        return $this->where('recipient', $email)
            ->orderBy('sent_at', 'DESC')
            ->findAll();
    }
    
    /**
     * Get logs by sender
     */
    public function getBySender($userId)
    {
        return $this->select('tbl_email_logs.*, tbl_users.first_name, tbl_users.last_name')
            ->join('tbl_users', 'tbl_users.id = tbl_email_logs.sent_by', 'left')
            ->where('tbl_email_logs.sent_by', $userId)
            ->orderBy('tbl_email_logs.sent_at', 'DESC')
            ->findAll();
    }
    
    /**
     * Get logs by date range
     */
    public function getByDateRange($startDate, $endDate)
    {
        return $this->where('sent_at >=', $startDate)
            ->where('sent_at <=', $endDate)
            ->orderBy('sent_at', 'DESC')
            ->findAll();
    }
    
    /**
     * Get statistics
     */
    public function getStatistics($days = 30)
    {
        $db = db_connect();
        
        // Total geral
        $total = $this->countAll();
        
        // Últimos 30 dias
        $last30Days = $this->where('sent_at >=', date('Y-m-d', strtotime("-{$days} days")))
            ->countAllResults();
        
        // Por dia (últimos 7 dias)
        $daily = $db->table('tbl_email_logs')
            ->select('DATE(sent_at) as date, COUNT(*) as total')
            ->where('sent_at >=', date('Y-m-d', strtotime('-7 days')))
            ->groupBy('DATE(sent_at)')
            ->orderBy('date', 'DESC')
            ->get()
            ->getResultArray();
        
        // Por template
        $byTemplate = $db->table('tbl_email_logs')
            ->select('template, COUNT(*) as total')
            ->where('template IS NOT NULL')
            ->groupBy('template')
            ->orderBy('total', 'DESC')
            ->limit(10)
            ->get()
            ->getResultArray();
        
        return [
            'total' => $total,
            'last_' . $days . '_days' => $last30Days,
            'daily' => $daily,
            'by_template' => $byTemplate
        ];
    }
    
    /**
     * Clean old logs
     */
    public function cleanOldLogs($days = 90)
    {
        $date = date('Y-m-d', strtotime("-{$days} days"));
        return $this->where('sent_at <', $date)->delete();
    }
}