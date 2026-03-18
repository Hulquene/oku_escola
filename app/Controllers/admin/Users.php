<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\RoleModel;
use App\Models\UserLogModel;

class Users extends BaseController
{
    protected $userModel;
    protected $roleModel;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
        
        // Helpers já são carregados no BaseController
    }
    
    /**
     * List users
     */
    public function index()
    {
        // Verificar permissão usando método do BaseController
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
        
        // Verificar permissão usando método do BaseController
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
        
        // Verificar permissões usando método do BaseController
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
        
        // Verificar permissões usando método do BaseController
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
        
        // Regras de validação
        $rules = [
            'username' => "required|min_length[3]|is_unique[tbl_users.username,id,{$id}]",
            'email' => "required|valid_email|is_unique[tbl_users.email,id,{$id}]",
            'first_name' => 'required|min_length[2]',
            'last_name' => 'required|min_length[2]',
            'role_id' => 'required|numeric'
        ];
        
        $messages = [
            'username' => [
                'required' => 'O username é obrigatório',
                'min_length' => 'O username deve ter pelo menos 3 caracteres',
                'is_unique' => 'Este username já está em uso'
            ],
            'email' => [
                'required' => 'O email é obrigatório',
                'valid_email' => 'Email inválido',
                'is_unique' => 'Este email já está em uso'
            ],
            'first_name' => [
                'required' => 'O primeiro nome é obrigatório',
                'min_length' => 'O primeiro nome deve ter pelo menos 2 caracteres'
            ],
            'last_name' => [
                'required' => 'O último nome é obrigatório',
                'min_length' => 'O último nome deve ter pelo menos 2 caracteres'
            ],
            'role_id' => [
                'required' => 'O perfil é obrigatório',
                'numeric' => 'Perfil inválido'
            ]
        ];
        
        // Se for novo usuário ou senha fornecida, validar senha
        if (!$id || !empty($this->request->getPost('password'))) {
            $rules['password'] = 'required|min_length[6]';
            $messages['password'] = [
                'required' => 'A palavra-passe é obrigatória',
                'min_length' => 'A palavra-passe deve ter pelo menos 6 caracteres'
            ];
        }
        
        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
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
        
        // Password only for new users or when provided
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = $password; // Será hasheado pelo model (UserModel tem método hashPassword)
        }
        
        // Buscar role para obter o role_type
        $role = $this->roleModel->find($data['role_id']);
        if ($role && $role['role_type'] == 'admin') {
            $data['user_type'] = 'admin';
        }
        
        if ($id) {
            // Verificar se está tentando desativar o próprio usuário
            if ($id == currentUserId() && isset($data['is_active']) && $data['is_active'] == 0) {
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
        // Verificar permissão usando método do BaseController
        if (!$this->hasPermission('users.delete')) {
            return redirect()->back()->with('error', 'Não tem permissão para eliminar utilizadores');
        }
        
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return redirect()->back()->with('error', 'Utilizador não encontrado');
        }
        
        // Don't allow deleting yourself
        if ($id == currentUserId()) {
            return redirect()->back()->with('error', 'Não é possível eliminar o próprio utilizador');
        }
        
        // Não permitir eliminar admin (ID 1)
        if ($id == 1) {
            return redirect()->back()->with('error', 'Não é possível eliminar o administrador principal');
        }
        
        $username = $user['username'];
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
        $userId = currentUserId();
        
        $data['title'] = 'Meu Perfil';
        $data['user'] = $this->userModel
            ->select('tbl_users.*, tbl_roles.role_name')
            ->join('tbl_roles', 'tbl_roles.id = tbl_users.role_id')
            ->where('tbl_users.id', $userId)
            ->first();
        
        if (!$data['user']) {
            return redirect()->to('/admin/dashboard')->with('error', 'Utilizador não encontrado');
        }
        
        // Log de visualização
        log_view('user', $userId, 'Visualizou próprio perfil');
        
        return view('admin/users/profile', $data);
    }
    
    /**
     * Update profile
     */
    public function updateProfile()
    {
        $userId = currentUserId();
        
        $rules = [
            'first_name' => 'required|min_length[2]',
            'last_name' => 'required|min_length[2]',
            'email' => "required|valid_email|is_unique[tbl_users.email,id,{$userId}]",
            'phone' => 'permit_empty|max_length[20]'
        ];
        
        $messages = [
            'first_name' => [
                'required' => 'O primeiro nome é obrigatório',
                'min_length' => 'O primeiro nome deve ter pelo menos 2 caracteres'
            ],
            'last_name' => [
                'required' => 'O último nome é obrigatório',
                'min_length' => 'O último nome deve ter pelo menos 2 caracteres'
            ],
            'email' => [
                'required' => 'O email é obrigatório',
                'valid_email' => 'Email inválido',
                'is_unique' => 'Este email já está em uso'
            ]
        ];
        
        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address')
        ];
        
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
     * Update profile photo
     */
    public function updatePhoto()
    {
        $userId = currentUserId();
        
        $file = $this->request->getFile('photo');
        
        if (!$file || !$file->isValid()) {
            return redirect()->back()->with('error', 'Nenhum arquivo selecionado');
        }
        
        // Verificar tipo
        $validTypes = ['image/jpeg', 'image/png', 'image/jpg'];
        if (!in_array($file->getMimeType(), $validTypes)) {
            return redirect()->back()->with('error', 'Formato de imagem inválido. Use JPG ou PNG.');
        }
        
        // Verificar tamanho (2MB)
        if ($file->getSize() > 2 * 1024 * 1024) {
            return redirect()->back()->with('error', 'A imagem não pode ter mais de 2MB');
        }
        
        // Criar diretório se não existir
        $uploadPath = FCPATH . 'uploads/users/';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0777, true);
        }
        
        // Gerar nome único
        $newName = $userId . '_' . time() . '.' . $file->getExtension();
        
        // Mover arquivo
        if ($file->move($uploadPath, $newName)) {
            // Buscar usuário para apagar foto antiga
            $user = $this->userModel->find($userId);
            
            // Apagar foto antiga se existir
            if (!empty($user['photo']) && file_exists($uploadPath . $user['photo'])) {
                unlink($uploadPath . $user['photo']);
            }
            
            // Atualizar banco de dados
            $this->userModel->update($userId, ['photo' => $newName]);
            
            // Atualizar sessão com nova foto
            $this->session->set('photo', $newName);
            
            // Log
            log_update('user', $userId, 'Foto de perfil atualizada');
            
            return redirect()->to('/admin/users/profile')->with('success', 'Foto atualizada com sucesso');
        }
        
        return redirect()->back()->with('error', 'Erro ao fazer upload da foto');
    }
    
    /**
     * Change password
     */
    public function changePassword()
    {
        $userId = currentUserId();
        
        $rules = [
            'current_password' => 'required',
            'new_password' => 'required|min_length[6]',
            'confirm_password' => 'required|matches[new_password]'
        ];
        
        $messages = [
            'current_password' => [
                'required' => 'A senha atual é obrigatória'
            ],
            'new_password' => [
                'required' => 'A nova senha é obrigatória',
                'min_length' => 'A nova senha deve ter pelo menos 6 caracteres'
            ],
            'confirm_password' => [
                'required' => 'A confirmação é obrigatória',
                'matches' => 'As senhas não coincidem'
            ]
        ];
        
        if (!$this->validate($rules, $messages)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Verificar senha atual
        $user = $this->userModel->find($userId);
        
        if (!password_verify($this->request->getPost('current_password'), $user['password'])) {
            return redirect()->back()->with('error', 'Senha atual incorreta');
        }
        
        // Atualizar senha
        $this->userModel->update($userId, [
            'password' => $this->request->getPost('new_password') // Será hasheado pelo model
        ]);
        
        // Log
        log_update('user', $userId, 'Senha alterada');
        
        return redirect()->to('/admin/users/profile')->with('success', 'Senha alterada com sucesso');
    }
    
    /**
     * View user details
     */
    public function view_user($id)
    {
        // Verificar permissão usando método do BaseController
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
        $logModel = new UserLogModel();
        $data['logs'] = $logModel
            ->where('user_id', $id)
            ->orderBy('created_at', 'DESC')
            ->limit(50)
            ->findAll();
        
        // Log de visualização
        log_view('user', $id, "Visualizou detalhes do utilizador '{$data['user']['username']}'");
        
        return view('admin/users/view', $data);
    }
    
    /**
     * Toggle user active status (AJAX)
     */
    public function toggleActive($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->respondWithError('Requisição inválida');
        }
        
        // Verificar permissão usando método do BaseController
        if (!$this->hasPermission('users.toggle')) {
            return $this->respondWithError('Sem permissão');
        }
        
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return $this->respondWithError('Utilizador não encontrado');
        }
        
        // Não permitir desativar o próprio usuário
        if ($id == currentUserId()) {
            return $this->respondWithError('Não é possível desativar o próprio utilizador');
        }
        
        // Não permitir desativar admin (ID 1)
        if ($id == 1) {
            return $this->respondWithError('Não é possível desativar o administrador principal');
        }
        
        $newStatus = $user['is_active'] ? 0 : 1;
        
        $this->userModel->update($id, ['is_active' => $newStatus]);
        
        $statusText = $newStatus ? 'ativado' : 'desativado';
        
        // Log de alteração de status
        log_action('update', "Utilizador '{$user['username']}' {$statusText}", $id, 'user', [
            'old_status' => $user['is_active'],
            'new_status' => $newStatus
        ]);
        
        return $this->respondWithSuccess("Utilizador '{$user['username']}' {$statusText} com sucesso", [
            'new_status' => $newStatus
        ]);
    }
    
    /**
     * Reset user password (admin function) - AJAX
     */
    public function resetPassword($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->respondWithError('Requisição inválida');
        }
        
        // Verificar permissão usando método do BaseController
        if (!$this->hasPermission('users.edit')) {
            return $this->respondWithError('Sem permissão');
        }
        
        $user = $this->userModel->find($id);
        
        if (!$user) {
            return $this->respondWithError('Utilizador não encontrado');
        }
        
        // Gerar password aleatória
        $newPassword = substr(str_shuffle('abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'), 0, 8);
        
        $this->userModel->update($id, [
            'password' => $newPassword // Será hasheado pelo model
        ]);
        
        // Log de reset de password
        log_action('update', "Password do utilizador '{$user['username']}' redefinida", $id, 'user');
        
        return $this->respondWithSuccess('Senha redefinida com sucesso', [
            'password' => $newPassword
        ]);
    }
}