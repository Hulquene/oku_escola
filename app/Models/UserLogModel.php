<?php

namespace App\Models;

class UserLogModel extends BaseModel
{
    protected $table = 'tbl_user_logs';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'user_id',
        'action',
        'description',
        'target_id',
        'target_type',
        'ip_address',
        'user_agent',
        'request_method',
        'request_url',
        'details',
        'created_at'
    ];
    
    protected $validationRules = [
        'user_id' => 'required|numeric',
        'action' => 'required'
    ];
    
    protected $useTimestamps = false; // Usamos created_at manualmente
    protected $createdField = 'created_at';
    protected $updatedField = null;
    
    /**
     * Get user logs with user info
     */
    public function getUserLogs($userId = null, $limit = 100)
    {
        $builder = $this->select('tbl_user_logs.*, tbl_users.username, tbl_users.first_name, tbl_users.last_name, tbl_users.photo')
            ->join('tbl_users', 'tbl_users.id = tbl_user_logs.user_id', 'left');
        
        if ($userId) {
            $builder->where('tbl_user_logs.user_id', $userId);
        }
        
        return $builder->orderBy('tbl_user_logs.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }
    
    /**
     * Get recent activities
     */
    public function getRecentActivities($limit = 20)
    {
        return $this->select('tbl_user_logs.*, tbl_users.username, tbl_users.photo, tbl_users.first_name, tbl_users.last_name')
            ->join('tbl_users', 'tbl_users.id = tbl_user_logs.user_id', 'left')
            ->orderBy('tbl_user_logs.created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }
    
    /**
     * Get logs by date range
     */
    public function getByDateRange($startDate, $endDate, $userId = null, $action = null)
    {
        $builder = $this->select('tbl_user_logs.*, tbl_users.username, tbl_users.first_name, tbl_users.last_name')
            ->join('tbl_users', 'tbl_users.id = tbl_user_logs.user_id', 'left')
            ->where('tbl_user_logs.created_at >=', $startDate . ' 00:00:00')
            ->where('tbl_user_logs.created_at <=', $endDate . ' 23:59:59');
        
        if ($userId) {
            $builder->where('tbl_user_logs.user_id', $userId);
        }
        
        if ($action) {
            $builder->where('tbl_user_logs.action', $action);
        }
        
        return $builder->orderBy('tbl_user_logs.created_at', 'DESC')
            ->findAll();
    }
    
    /**
     * Get logs by target
     */
    public function getByTarget($targetType, $targetId)
    {
        return $this->select('tbl_user_logs.*, tbl_users.username, tbl_users.first_name, tbl_users.last_name')
            ->join('tbl_users', 'tbl_users.id = tbl_user_logs.user_id', 'left')
            ->where('tbl_user_logs.target_type', $targetType)
            ->where('tbl_user_logs.target_id', $targetId)
            ->orderBy('tbl_user_logs.created_at', 'DESC')
            ->findAll();
    }
    
    /**
     * Get statistics
     */
    public function getStatistics($days = 30)
    {
        $stats = [
            'total' => $this->countAll(),
            'today' => $this->where('DATE(created_at)', date('Y-m-d'))->countAllResults(),
            'week' => $this->where('created_at >=', date('Y-m-d', strtotime('-7 days')))->countAllResults(),
            'month' => $this->where('created_at >=', date('Y-m-01'))->countAllResults(),
        ];
        
        // Actions breakdown
        $actions = $this->select('action, COUNT(*) as count')
            ->groupBy('action')
            ->orderBy('count', 'DESC')
            ->findAll();
        
        $stats['actions'] = $actions;
        
        // Users breakdown
        $users = $this->select('tbl_users.username, COUNT(*) as count')
            ->join('tbl_users', 'tbl_users.id = tbl_user_logs.user_id')
            ->groupBy('tbl_user_logs.user_id')
            ->orderBy('count', 'DESC')
            ->limit(10)
            ->findAll();
        
        $stats['top_users'] = $users;
        
        return $stats;
    }
    
    /**
     * Clean old logs
     */
    public function cleanOldLogs($daysToKeep = 90)
    {
        $date = date('Y-m-d', strtotime("-{$daysToKeep} days"));
        return $this->where('created_at <', $date . ' 00:00:00')->delete();
    }
}