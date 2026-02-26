<?php
// app/Controllers/teachers/Notifications.php

namespace App\Controllers\teachers;

use App\Controllers\BaseController;
use App\Models\NotificationModel;

class Notifications extends BaseController
{
    protected $notificationModel;
    protected $currentUser;
    
    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
        
        // Verificar se é professor
        if (session()->get('user_type') != 'teacher') {
            return redirect()->to('/login')->with('error', 'Acesso não autorizado');
        }
        
        // Carregar dados do usuário atual
        $this->currentUser = (object)[
            'id' => session()->get('user_id'),
            'name' => session()->get('name'),
            'email' => session()->get('email'),
            'user_type' => session()->get('user_type')
        ];
    }
    
    public function index()
    {
        $data['title'] = 'Minhas Notificações';
        
        $data['notifications'] = $this->notificationModel
            ->where('user_id', $this->currentUser->id)
            ->orderBy('created_at', 'DESC')
            ->orderBy('is_read', 'ASC')
            ->paginate(20);
        
        $data['pager'] = $this->notificationModel->pager;
        
        // Contar não lidas
        $data['unreadCount'] = $this->notificationModel
            ->where('user_id', $this->currentUser->id)
            ->where('is_read', 0)
            ->countAllResults();
        
        return view('teachers/notifications/index', $data);
    }
    
    public function read($id)
    {
        // Verificar se a notificação pertence ao usuário
        $notification = $this->notificationModel
            ->where('id', $id)
            ->where('user_id', $this->currentUser->id)
            ->first();
        
        if (!$notification) {
            return redirect()->to('/teachers/notifications')
                ->with('error', 'Notificação não encontrada.');
        }
        
        // Marcar como lida usando o helper
        mark_notification_as_read($id, $this->currentUser->id);
        
        // Redirecionar para o link da notificação se existir
        if (!empty($notification->link)) {
            return redirect()->to($notification->link);
        }
        
        return redirect()->to('/teachers/notifications');
    }
    
    public function markAllRead()
    {
        // Marcar todas como lidas usando o helper
        mark_all_notifications_as_read($this->currentUser->id);
        
        return redirect()->back()->with('success', 'Todas as notificações foram marcadas como lidas.');
    }
    
    public function delete($id)
    {
        // Deletar notificação usando o helper
        delete_notification($id, $this->currentUser->id);
        
        return redirect()->back()->with('success', 'Notificação removida.');
    }
    
    public function getUnreadCount()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }
        
        $count = get_unread_notifications_count();
        
        return $this->response->setJSON([
            'success' => true,
            'count' => $count
        ]);
    }
}