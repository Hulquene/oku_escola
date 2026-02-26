<?php
// app/Controllers/students/Notifications.php

namespace App\Controllers\students;

use App\Controllers\BaseController;
use App\Models\NotificationModel;

class Notifications extends BaseController
{
    protected $notificationModel;
    protected $currentUser;
    
    public function __construct()
    {
        $this->notificationModel = new NotificationModel();
        
        // Verificar se é aluno
        if (session()->get('user_type') != 'student') {
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
        
        return view('students/notifications/index', $data);
    }
    
    public function read($id)
    {
        // Verificar se a notificação pertence ao usuário
        $notification = $this->notificationModel
            ->where('id', $id)
            ->where('user_id', $this->currentUser->id)
            ->first();
        
        if (!$notification) {
            return redirect()->to('/students/notifications')
                ->with('error', 'Notificação não encontrada.');
        }
        
        $this->notificationModel->markAsRead($id, $this->currentUser->id);
        
        // Redirecionar para o link da notificação se existir
        if (!empty($notification->link)) {
            return redirect()->to($notification->link);
        }
        
        return redirect()->to('/students/notifications');
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