<?php
// app/Controllers/guardians/Notifications.php
namespace App\Controllers\guardians;

use App\Controllers\BaseController;

class Notifications extends BaseController
{
    public function index()
    {
        $userId = currentUserId();
        
        $db = db_connect();
        
        $notifications = $db->table('tbl_notifications')
            ->where('user_id', $userId)
            ->orderBy('created_at', 'DESC')
            ->get()
            ->getResultArray();
        
        $data['title'] = 'Notificações';
        $data['notifications'] = $notifications;
        $data['unread_count'] = $db->table('tbl_notifications')
            ->where('user_id', $userId)
            ->where('is_read', 0)
            ->countAllResults();
        
        return view('guardians/notifications/index', $data);
    }
    
    public function read($id)
    {
        if (!$this->request->isAJAX()) {
            return redirect()->to('/guardians/notifications');
        }
        
        $userId = currentUserId();
        
        $db = db_connect();
        
        $db->table('tbl_notifications')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->update(['is_read' => 1]);
        
        return $this->response->setJSON(['success' => true]);
    }
    
    public function markAllRead()
    {
        $userId = currentUserId();
        
        $db = db_connect();
        
        $db->table('tbl_notifications')
            ->where('user_id', $userId)
            ->where('is_read', 0)
            ->update(['is_read' => 1]);
        
        return redirect()->to('/guardians/notifications')
            ->with('success', 'Todas as notificações foram marcadas como lidas.');
    }
    
    public function delete($id)
    {
        $userId = currentUserId();
        
        $db = db_connect();
        
        $db->table('tbl_notifications')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->delete();
        
        return redirect()->to('/guardians/notifications')
            ->with('success', 'Notificação removida.');
    }
    
    public function getUnreadCount()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['count' => 0]);
        }
        
        $userId = currentUserId();
        
        $db = db_connect();
        
        $count = $db->table('tbl_notifications')
            ->where('user_id', $userId)
            ->where('is_read', 0)
            ->countAllResults();
        
        return $this->response->setJSON(['count' => $count]);
    }
}