<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\RoleModel;

class Users extends BaseController
{
    protected $userModel;
    protected $roleModel;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
    }
    
    /**
     * List users
     */
    public function index()
    {
        // Verificar permissão
        if (!$this->hasPermission('users.list')) {
            return redirect()->to('/admin/dashboard')->with('error', 'Não tem permissão para aceder a esta página');
        }
        
        $data['title'] = 'Utilizadores';
        $data['users'] = $this->userModel
            ->select('tbl_users.*, tbl_roles.role_name')
            ->join('tbl_roles', 'tbl_roles.id = tbl_users.role_id')
            ->orderBy('tbl_users.created_at', 'DESC')
            ->paginate(10);
        
        $data['pager'] = $this->userModel->pager;
        
        // Log de visualização
        log_view('user', 0, 'Visualizou lista de utilizadores');
        
        return view('admin/users/index', $data);
    }
    
    /**
     * Get users for datatable
     */
    public function get_users_table()
    {
        if (!$this->request->isAJAX()) {
            return $this->respondWithError('Requisição inválida');
        }
        
        // Verificar permissão
        if (!$this->hasPermission('users.list')) {
            return $this->respondWithError('Sem permissão');
        }
        
        $datatable = $this->getDatatableRequest();
        
        $columns = ['id', 'username', 'first_name', 'last_name', 'email', 'role_name', 'is_active', 'last_login'];
        $searchable = ['username', 'first_name', 'last_name', 'email'];
        
        $builder = $this->userModel
            ->select('tbl_users.*, tbl_roles.role_name')
            ->join('tbl_roles', 'tbl_roles.id = tbl_users.role_id');
        
        // Apply search
        if (!empty($datatable['search'])) {
            $builder->groupStart();
            foreach ($searchable as $field) {
                $builder->orLike('tbl_users.' . $field, $datatable['search']);
            }
            $builder->groupEnd();
        }
        
        // Get total records
        $recordsTotal = $this->userModel->countAllResults();
        $recordsFiltered = $builder->countAllResults(false);
        
        // Apply order
        if (!empty($datatable['order'])) {
            $order = $datatable['order'][0];
            $columnIndex = $order['column'];
            $columnName = $columns[$columnIndex] ?? 'id';
            $dir = $order['dir'];
            $builder->orderBy('tbl_users.' . $columnName, $dir);
        } else {
            $builder->orderBy('tbl_users.id', 'DESC');
        }
        
        // Apply limit
        $builder->limit($datatable['length'], $datatable['start']);
        
        $data = $builder->get()->getResult();
        
        return $this->respondWithJson([
            'draw' => $datatable['draw'],
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ]);
    }
    
    /**
     * User form (add/edit)
     */
    public function form($id = null)
    {
        $data['title'] = $id ? 'Editar Utilizador' : 'Novo Utilizador';
        $data['user'] = $id ? $this->userModel->find($id) : null;
        
        // Verificar permissões
        if ($id) {
            // Edição
            if (!$this->hasPermission('users.edit')) {
                return redirect()->to('/admin/users')->with('error', 'Não tem permissão para editar utilizadores');
            }
            
            // Não permitir editar admin (ID 1) a não ser que seja root
            if ($id == 1 && !$this->isRoot()) {
                return redirect()->to('/admin/users')->with('error', 'Não tem permissão para editar o administrador principal');
            }
        } else {
            // Criação
            if (!$this->hasPermission('users.create')) {
                return redirect()->to('/admin/users')->with('error', 'Não tem permissão para criar utilizadores');
            }
        }
        
        $data['roles'] = $this->roleModel->findAll();
        
        return view('admin/users/form', $data);
    }
    
    /**
     * Save user
     */
    public function save()
    {
        $id = $this->request->getPost('id');
        
        // Verificar permissões
        if ($id) {
            if (!$this->hasPermission('users.edit')) {
                return redirect()->to('/admin/users')->with('error', 'Não tem permissão para editar utilizadores');
            }
            
            // Não permitir editar admin (ID 1) a não ser que seja root
            if ($id == 1 && !$this->isRoot()) {
                return redirect()->to('/admin/users')->with('error', 'Não tem permissão para editar o administrador principal');
            }
        } else {
            if (!$this->hasPermission('users.create')) {
                return redirect()->to('/admin/users')->with('error', 'Não tem permissão para criar utilizadores');
            }
        }
        
        $rules = [
            'username' => 'required|min_length[3]|is_unique[tbl_users.username,id,{id}]',
            'email' => 'required|valid_email|is_unique[tbl_users.email,id,{id}]',
            'first_name' => 'required',
            'last_name' => 'required',
            'role_id' => 'required|numeric'
        ];
        
        // Password required for new users
        if (!$id) {
            $rules['password'] = 'required|min_length[6]';
        }
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'username' => $this->request->getPost('username'),
            'email' => $this->request->getPost('email'),
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address'),
            'role_id' => $this->request->getPost('role_id'),
            'user_type' => $this->request->getPost('user_type') ?? 'staff',
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];
        
        // Set password if provided
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }
        
        // Buscar role para obter o role_type
        $role = $this->roleModel->find($data['role_id']);
        if ($role) {
            // Se a role é admin, definir user_type como admin
            if ($role->role_type == 'admin') {
                $data['user_type'] = 'admin';
            }
        }
        
        if ($id) {
            // Verificar se está tentando desativar o próprio usuário
            if ($id == $this->session->get('user_id') && isset($data['is_active']) && $data['is_active'] == 0) {
                return redirect()->back()->withInput()
                    ->with('error', 'Não é possível desativar o próprio utilizador');
            }
            
            $oldData = $this->userModel->find($id);
            $this->userModel->update($id, $data);
            
            // Log de atualização
            log_update('user', $id, "Utilizador '{$data['username']}' atualizado", [
                'old' => $oldData,
                'new' => $data
            ]);
            
            $message = 'Utilizador atualizado com sucesso';
        } else {
            $newId = $this->userModel->insert($data);
            
            // Log de inserção
            log_insert('user', $newId, "Novo utilizador criado: {$data['username']}", $data);
            
            $message = 'Utilizador criado com sucesso';
        }
        
        return redirect()->to('/admin/users')->with('success', $message);
    }
    
    /**
     * Delete user
     */
    public function delete($id)
    {
        // Verificar permissão
        if (!$this->hasPermission('users.delete')) {
            return redirect()->back()->with('error', 'Não tem permissão para eliminar utilizadores');
        }
        
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->back()->with('error', 'Utilizador não encontrado');
        }
        
        // Don't allow deleting yourself
        if ($id == $this->session->get('user_id')) {
            return redirect()->back()->with('error', 'Não é possível eliminar o próprio utilizador');
        }
        
        // Não permitir eliminar admin (ID 1)
        if ($id == 1) {
            return redirect()->back()->with('error', 'Não é possível eliminar o administrador principal');
        }
        
        $username = $user->username;
        $this->userModel->delete($id);
        
        // Log de eliminação
        log_delete('user', $id, "Utilizador eliminado: {$username}", ['username' => $username]);
        
        return redirect()->to('/admin/users')->with('success', 'Utilizador eliminado com sucesso');
    }
    
    /**
     * User profile
     */
    public function profile()
    {
        $userId = $this->session->get('user_id');
        
        $data['title'] = 'Meu Perfil';
        $data['user'] = $this->userModel
            ->select('tbl_users.*, tbl_roles.role_name')
            ->join('tbl_roles', 'tbl_roles.id = tbl_users.role_id')
            ->where('tbl_users.id', $userId)
            ->first();
        
        // Log de visualização
        log_view('user', $userId, 'Visualizou próprio perfil');
        
        return view('admin/users/profile', $data);
    }
    
    /**
     * Update profile
     */
    public function updateProfile()
    {
        $userId = $this->session->get('user_id');
        
        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => "required|valid_email|is_unique[tbl_users.email,id,{$userId}]",
            'phone' => 'permit_empty'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address')
        ];
        
        // Update password if provided
        $password = $this->request->getPost('password');
        $passwordConfirm = $this->request->getPost('password_confirm');
        
        if (!empty($password)) {
            if ($password != $passwordConfirm) {
                return redirect()->back()->withInput()
                    ->with('error', 'As palavras-passe não coincidem');
            }
            
            if (strlen($password) < 6) {
                return redirect()->back()->withInput()
                    ->with('error', 'A palavra-passe deve ter pelo menos 6 caracteres');
            }
            
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }
        
        $oldData = $this->userModel->find($userId);
        $this->userModel->update($userId, $data);
        
        // Log de atualização do perfil
        log_update('user', $userId, 'Perfil atualizado', [
            'old' => $oldData,
            'new' => $data
        ]);
        
        // Atualizar sessão com novo nome
        $this->session->set('name', $data['first_name'] . ' ' . $data['last_name']);
        
        return redirect()->to('/admin/users/profile')->with('success', 'Perfil atualizado com sucesso');
    }
    
    /**
     * View user details
     */
    public function view_user($id)
    {
        // Verificar permissão
        if (!$this->hasPermission('users.view')) {
            return redirect()->to('/admin/users')->with('error', 'Não tem permissão para ver detalhes de utilizadores');
        }
        
        $data['title'] = 'Detalhes do Utilizador';
        $data['user'] = $this->userModel
            ->select('tbl_users.*, tbl_roles.role_name')
            ->join('tbl_roles', 'tbl_roles.id = tbl_users.role_id')
            ->where('tbl_users.id', $id)
            ->first();
        
        if (!$data['user']) {
            return redirect()->to('/admin/users')->with('error', 'Utilizador não encontrado');
        }
        
        // Buscar logs deste utilizador
        $logModel = new \App\Models\UserLogModel();
        $data['logs'] = $logModel
            ->where('user_id', $id)
            ->orderBy('created_at', 'DESC')
            ->limit(50)
            ->findAll();
        
        // Log de visualização
        log_view('user', $id, "Visualizou detalhes do utilizador '{$data['user']->username}'");
        
        return view('admin/users/view', $data);
    }
    
    /**
     * Toggle user active status
     */
    public function toggleActive($id)
    {
        // Verificar permissão
        if (!$this->hasPermission('users.toggle')) {
            return redirect()->back()->with('error', 'Não tem permissão para ativar/desativar utilizadores');
        }
        
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->back()->with('error', 'Utilizador não encontrado');
        }
        
        // Não permitir desativar o próprio usuário
        if ($id == $this->session->get('user_id')) {
            return redirect()->back()->with('error', 'Não é possível desativar o próprio utilizador');
        }
        
        // Não permitir desativar admin (ID 1)
        if ($id == 1) {
            return redirect()->back()->with('error', 'Não é possível desativar o administrador principal');
        }
        
        $newStatus = $user->is_active ? 0 : 1;
        
        $this->userModel->update($id, ['is_active' => $newStatus]);
        
        $statusText = $newStatus ? 'ativado' : 'desativado';
        
        // Log de alteração de status
        log_action('update', "Utilizador '{$user->username}' {$statusText}", $id, 'user', [
            'old_status' => $user->is_active,
            'new_status' => $newStatus
        ]);
        
        return redirect()->back()->with('success', "Utilizador '{$user->username}' {$statusText} com sucesso");
    }
    
    /**
     * Reset user password (admin function)
     */
    public function resetPassword($id)
    {
        // Verificar permissão
        if (!$this->hasPermission('users.edit')) {
            return redirect()->back()->with('error', 'Não tem permissão para redefinir palavras-passe');
        }
        
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->back()->with('error', 'Utilizador não encontrado');
        }
        
        // Gerar password aleatória
        $newPassword = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
        
        $this->userModel->update($id, [
            'password' => password_hash($newPassword, PASSWORD_DEFAULT)
        ]);
        
        // Log de reset de password
        log_action('update', "Password do utilizador '{$user->username}' redefinida", $id, 'user');
        
        // TODO: Enviar email com a nova password
        
        return redirect()->back()->with('success', "Password redefinida com sucesso. Nova password: {$newPassword}");
    }
}