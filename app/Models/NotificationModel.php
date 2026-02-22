<?php
// app/Models/NotificationModel.php

namespace App\Models;

class NotificationModel extends BaseModel
{
    protected $table = 'tbl_notifications';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'user_id',
        'user_type',
        'title',
        'message',
        'icon',
        'color',
        'link',
        'link_text',
        'entity_type',
        'entity_id',
        'is_read',
        'read_at',
        'is_global',
        'expires_at',
        'created_by'
    ];
    
    protected $validationRules = [
        'user_id' => 'required|numeric',
        'user_type' => 'required|in_list[admin,teacher,student,guardian,staff]',
        'title' => 'required|min_length[3]|max_length[255]',
        'message' => 'required',
        'icon' => 'permit_empty|max_length[50]',
        'color' => 'permit_empty|in_list[primary,success,warning,danger,info,secondary]',
        'link' => 'permit_empty|max_length[500]',
        'link_text' => 'permit_empty|max_length[100]',
        'entity_type' => 'permit_empty|max_length[50]',
        'entity_id' => 'permit_empty|numeric',
        'is_read' => 'permit_empty|in_list[0,1]'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    /**
     * Get unread notifications count for a user
     */
    public function getUnreadCount($userId)
    {
        return $this->where('user_id', $userId)
            ->where('is_read', 0)
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->orWhere('expires_at IS NULL')
            ->countAllResults();
    }
    
    /**
     * Get recent notifications for a user
     */
    public function getRecent($userId, $limit = 10)
    {
        return $this->where('user_id', $userId)
            ->where('expires_at >', date('Y-m-d H:i:s'))
            ->orWhere('expires_at IS NULL')
            ->orderBy('created_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }
    
    /**
     * Get paginated notifications for a user
     */
    public function getForUser($userId, $perPage = 20)
    {
        return $this->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->paginate($perPage);
    }
    
    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId, $userId)
    {
        return $this->where('id', $notificationId)
            ->where('user_id', $userId)
            ->set([
                'is_read' => 1,
                'read_at' => date('Y-m-d H:i:s')
            ])
            ->update();
    }
    
    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead($userId)
    {
        return $this->where('user_id', $userId)
            ->where('is_read', 0)
            ->set([
                'is_read' => 1,
                'read_at' => date('Y-m-d H:i:s')
            ])
            ->update();
    }
    
    /**
     * Create a notification for a single user
     */
    public function createForUser($userId, $userType, $data)
    {
        $notificationData = [
            'user_id' => $userId,
            'user_type' => $userType,
            'title' => $data['title'] ?? '',
            'message' => $data['message'] ?? '',
            'icon' => $data['icon'] ?? 'fa-info-circle',
            'color' => $data['color'] ?? 'primary',
            'link' => $data['link'] ?? null,
            'link_text' => $data['link_text'] ?? 'Ver detalhes',
            'entity_type' => $data['entity_type'] ?? null,
            'entity_id' => $data['entity_id'] ?? null,
            'expires_at' => $data['expires_at'] ?? date('Y-m-d H:i:s', strtotime('+30 days')),
            'created_by' => $data['created_by'] ?? null
        ];
        
        return $this->insert($notificationData);
    }
    
    /**
     * Create notifications for multiple users of same type
     */
    public function createForUserType($userType, $data, $userIds = [])
    {
        $userModel = new UserModel();
        
        // If specific user IDs provided, use them
        if (!empty($userIds)) {
            $users = $userModel->whereIn('id', $userIds)
                ->where('user_type', $userType)
                ->where('is_active', 1)
                ->findAll();
        } else {
            // Otherwise get all users of that type
            $users = $userModel->where('user_type', $userType)
                ->where('is_active', 1)
                ->findAll();
        }
        
        $insertData = [];
        foreach ($users as $user) {
            $insertData[] = [
                'user_id' => $user->id,
                'user_type' => $userType,
                'title' => $data['title'] ?? '',
                'message' => $data['message'] ?? '',
                'icon' => $data['icon'] ?? 'fa-info-circle',
                'color' => $data['color'] ?? 'primary',
                'link' => $data['link'] ?? null,
                'link_text' => $data['link_text'] ?? 'Ver detalhes',
                'entity_type' => $data['entity_type'] ?? null,
                'entity_id' => $data['entity_id'] ?? null,
                'expires_at' => $data['expires_at'] ?? date('Y-m-d H:i:s', strtotime('+30 days')),
                'created_by' => $data['created_by'] ?? null,
                'created_at' => date('Y-m-d H:i:s')
            ];
        }
        
        if (!empty($insertData)) {
            return $this->insertBatch($insertData);
        }
        
        return false;
    }
    
    /**
     * Create a global notification for all users
     */
    public function createGlobal($data)
    {
        $userModel = new UserModel();
        $users = $userModel->where('is_active', 1)->findAll();
        
        $insertData = [];
        foreach ($users as $user) {
            $insertData[] = [
                'user_id' => $user->id,
                'user_type' => $user->user_type,
                'title' => $data['title'] ?? '',
                'message' => $data['message'] ?? '',
                'icon' => $data['icon'] ?? 'fa-info-circle',
                'color' => $data['color'] ?? 'primary',
                'link' => $data['link'] ?? null,
                'link_text' => $data['link_text'] ?? 'Ver detalhes',
                'entity_type' => $data['entity_type'] ?? null,
                'entity_id' => $data['entity_id'] ?? null,
                'is_global' => 1,
                'expires_at' => $data['expires_at'] ?? date('Y-m-d H:i:s', strtotime('+30 days')),
                'created_by' => $data['created_by'] ?? null,
                'created_at' => date('Y-m-d H:i:s')
            ];
        }
        
        if (!empty($insertData)) {
            return $this->insertBatch($insertData);
        }
        
        return false;
    }
    
    /**
     * Delete old notifications
     */
    public function deleteOld($days = 30)
    {
        $date = date('Y-m-d H:i:s', strtotime("-$days days"));
        
        return $this->where('created_at <', $date)
            ->where('is_read', 1)
            ->delete();
    }
    
    /**
     * Get notification with entity details
     */
    public function getWithEntity($id, $userId)
    {
        $notification = $this->where('id', $id)
            ->where('user_id', $userId)
            ->first();
        
        if ($notification && $notification->entity_type && $notification->entity_id) {
            $modelName = 'App\\Models\\' . ucfirst($notification->entity_type) . 'Model';
            
            if (class_exists($modelName)) {
                $model = new $modelName();
                $notification->entity = $model->find($notification->entity_id);
            }
        }
        
        return $notification;
    }
}