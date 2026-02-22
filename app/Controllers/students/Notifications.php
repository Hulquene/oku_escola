<?php
// app/Controllers/teachers/Notifications.php

namespace App\Controllers\students;

use App\Controllers\BaseController;
use App\Models\NotificationModel;

class Notifications extends BaseController
{
    protected $notificationModel;
    
    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
    }
    
    public function index()
    {
        $data['title'] = 'Notificações';
        $data['notifications'] = $this->notificationModel
            ->where('user_id', $this->currentUser->id)
            ->orderBy('created_at', 'DESC')
            ->paginate(20);
        
        $data['pager'] = $this->notificationModel->pager;
        
        return view('teachers/notifications/index', $data);
    }
    
    public function read($id)
    {
        $this->notificationModel->markAsRead($id, $this->currentUser->id);
        return redirect()->to('/teachers/notifications');
    }
    
    public function markAllRead()
    {
        $this->notificationModel->markAllAsRead($this->currentUser->id);
        return redirect()->back()->with('success', 'Todas as notificações foram marcadas como lidas.');
    }
    
    public function delete($id)
    {
        $this->notificationModel
            ->where('id', $id)
            ->where('user_id', $this->currentUser->id)
            ->delete();
        
        return redirect()->back()->with('success', 'Notificação removida.');
    }
    
    public function getUnreadCount()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }
        
        $count = $this->notificationModel->getUnreadCount($this->currentUser->id);
        
        return $this->response->setJSON([
            'success' => true,
            'count' => $count
        ]);
    }
}