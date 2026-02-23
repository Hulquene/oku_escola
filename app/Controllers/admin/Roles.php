<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\RoleModel;
use App\Models\PermissionModel;
use App\Models\RolePermissionModel;

class Roles extends BaseController
{
    protected $roleModel;
    protected $permissionModel;
    protected $rolePermissionModel;
    
    public function __construct()
    {
        $this->roleModel = new RoleModel();
        $this->permissionModel = new PermissionModel();
        $this->rolePermissionModel = new RolePermissionModel();
    }
    
    /**
     * List roles
     */
    public function index()
    {
        // Verificar permissão
        if (!has_permission('roles.list')) {
            return redirect()->to('/admin/dashboard')->with('error', 'Não tem permissão para aceder a esta página');
        }
        
        $data['title'] = 'Perfis de Acesso';
        $data['roles'] = $this->roleModel->findAll();
        
        return view('admin/roles/index', $data);
    }
    
public function form($id = null)
{
    // DEBUG TEMPORÁRIO
    log_message('debug', '=== MÉTODO FORM CHAMADO ===');
    log_message('debug', 'URL completa: ' . current_url());
    log_message('debug', 'ID recebido: ' . ($id ?? 'null'));
    log_message('debug', 'Método HTTP: ' . $this->request->getMethod());
    
    // Mostrar TODOS os parâmetros da rota
    log_message('debug', 'Todos os parâmetros: ' . json_encode($this->request->getUri()->getSegments()));
    
    $data['title'] = $id ? 'Editar Perfil' : 'Novo Perfil';
    $data['role'] = null;
    
    // Se for edição, verificar permissão de editar
    if ($id) {
        log_message('debug', 'Modo edição - ID: ' . $id);
        
        if (!has_permission('roles.edit')) {
            log_message('debug', 'Sem permissão para editar');
            return redirect()->to('/admin/roles')->with('error', 'Não tem permissão para editar perfis');
        }
        
        log_message('debug', 'Buscando perfil no banco...');
        $data['role'] = $this->roleModel->find($id);
        
        if ($data['role']) {
            log_message('debug', 'Perfil ENCONTRADO!');
            log_message('debug', 'Dados: ' . json_encode($data['role']));
        } else {
            log_message('debug', 'Perfil NÃO ENCONTRADO no banco!');
            
            // Listar todos os perfis para verificar
            $allRoles = $this->roleModel->findAll();
            log_message('debug', 'Perfis disponíveis: ' . json_encode(array_column($allRoles, 'id')));
            
            return redirect()->to('/admin/roles')->with('error', 'Perfil não encontrado');
        }
    } else {
        log_message('debug', 'Modo criação');
        
        if (!has_permission('roles.create')) {
            log_message('debug', 'Sem permissão para criar');
            return redirect()->to('/admin/roles')->with('error', 'Não tem permissão para criar perfis');
        }
    }
    
    log_message('debug', 'Renderizando view com data:');
    log_message('debug', 'title: ' . $data['title']);
    log_message('debug', 'role: ' . ($data['role'] ? 'presente' : 'null'));
    
    return view('admin/roles/form', $data);
}
    
/**
 * Save role (Create/Update)
 */
public function save($id = null)
{
    // Regras de validação
    $rules = [
        'role_name' => 'required|min_length[3]|max_length[100]|is_unique[tbl_roles.role_name,id,' . $id . ']',
        'role_description' => 'permit_empty|max_length[255]'
    ];
    
    $messages = [
        'role_name' => [
            'required' => 'O nome do perfil é obrigatório',
            'min_length' => 'O nome do perfil deve ter pelo menos 3 caracteres',
            'is_unique' => 'Este nome de perfil já está em uso'
        ]
    ];
    
    if (!$this->validate($rules, $messages)) {
        return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
    }
    
    $data = [
        'role_name' => $this->request->getPost('role_name'),
        'role_description' => $this->request->getPost('role_description')
    ];
    
    // Log para debug
    log_message('debug', 'Dados recebidos: ' . json_encode($data));
    log_message('debug', 'ID: ' . ($id ?? 'novo'));
    
    if ($id) {
        // Verificar permissão de editar
        if (!has_permission('roles.edit')) {
            return redirect()->to('/admin/roles')->with('error', 'Não tem permissão para editar perfis');
        }
        
        // Não permitir editar o administrador (ID 1) se não for admin
        if ($id == 1 && !is_admin()) {
            return redirect()->to('/admin/roles')->with('error', 'Não tem permissão para editar o perfil Administrador');
        }
        
        // Verificar se o perfil existe
        $role = $this->roleModel->find($id);
        if (!$role) {
            return redirect()->to('/admin/roles')->with('error', 'Perfil não encontrado');
        }
        
        if ($this->roleModel->update($id, $data)) {
            return redirect()->to('/admin/roles')->with('success', 'Perfil atualizado com sucesso');
        } else {
            log_message('error', 'Erro ao atualizar perfil: ' . json_encode($this->roleModel->errors()));
            return redirect()->back()->with('error', 'Erro ao atualizar perfil')->withInput();
        }
    } else {
        // Verificar permissão de criar
        if (!has_permission('roles.create')) {
            return redirect()->to('/admin/roles')->with('error', 'Não tem permissão para criar perfis');
        }
        
        $newId = $this->roleModel->insert($data);
        if ($newId) {
            return redirect()->to('/admin/roles')->with('success', 'Perfil criado com sucesso');
        } else {
            log_message('error', 'Erro ao criar perfil: ' . json_encode($this->roleModel->errors()));
            return redirect()->back()->with('error', 'Erro ao criar perfil')->withInput();
        }
    }
}
    
   /**
 * Delete role
 */
public function delete($id = null)
{
    // Verificar permissão
    if (!has_permission('roles.delete')) {
        return redirect()->to('/admin/roles')->with('error', 'Não tem permissão para eliminar perfis');
    }
    
    if (!$id) {
        return redirect()->to('/admin/roles')->with('error', 'Perfil não especificado');
    }
    
    $role = $this->roleModel->find($id);
    
    if (!$role) {
        return redirect()->to('/admin/roles')->with('error', 'Perfil não encontrado');
    }
    
    // Verificar se é um perfil de sistema (protegido)
    if (in_array($role->role_type, ['admin', 'teacher', 'student'])) {
        return redirect()->to('/admin/roles')->with('error', 'O perfil ' . $role->role_name . ' é um perfil de sistema e não pode ser eliminado');
    }
    
    // Verificar se existem usuários com este perfil
    $userModel = new \App\Models\UserModel();
    $usersWithRole = $userModel->where('role_id', $id)->countAllResults();
    
    if ($usersWithRole > 0) {
        return redirect()->to('/admin/roles')->with('error', 'Não é possível eliminar um perfil com usuários associados');
    }
    
    if ($this->roleModel->delete($id)) {
        return redirect()->to('/admin/roles')->with('success', 'Perfil eliminado com sucesso');
    } else {
        return redirect()->to('/admin/roles')->with('error', 'Erro ao eliminar perfil');
    }
}
    
    /**
     * Role permissions page
     */
    public function role($roleId = null)
    {
        // Verificar permissão
        if (!has_permission('roles.permissions')) {
            return redirect()->to('/admin/roles')->with('error', 'Não tem permissão para gerir permissões');
        }
        
        if (!$roleId) {
            return redirect()->to('/admin/roles')->with('error', 'Perfil não especificado');
        }
        
        $data['title'] = 'Permissões do Perfil';
        $data['role'] = $this->roleModel->find($roleId);
        
        if (!$data['role']) {
            return redirect()->to('/admin/roles')->with('error', 'Perfil não encontrado');
        }
        
        // Get all permissions grouped by module
        $data['permissions'] = $this->permissionModel
            ->orderBy('module', 'ASC')
            ->orderBy('permission_name', 'ASC')
            ->findAll();
        
        // Get role permissions
        $rolePermissions = $this->rolePermissionModel
            ->where('role_id', $roleId)
            ->findAll();
        
        $data['rolePermissions'] = array_column($rolePermissions, 'permission_id');
        
        return view('admin/roles/permissions', $data);
    }
    
    /**
     * Update role permissions
     */
    public function update_permissions()
    {
        // Verificar permissão
        if (!has_permission('roles.permissions')) {
            return $this->response->setJSON(['error' => 'Sem permissão para esta ação']);
        }
        
        $roleId = $this->request->getPost('role_id');
        $permissions = $this->request->getPost('permissions') ?? [];
        
        if (!$roleId) {
            return redirect()->back()->with('error', 'Perfil não especificado');
        }
        
        $result = $this->rolePermissionModel->updatePermissions($roleId, $permissions);
        
        if ($result) {
            return redirect()->to('/admin/roles')->with('success', 'Permissões atualizadas com sucesso');
        } else {
            return redirect()->back()->with('error', 'Erro ao atualizar permissões');
        }
    }

    /**
     * Permissions settings page
     */
    public function grouppermissions()
    {
        // Verificar permissão
        if (!has_permission('roles.permissions')) {
            return redirect()->to('/admin/roles')->with('error', 'Não tem permissão para aceder a esta página');
        }
        
        $data['title'] = 'Configurações de Permissões';
        
        // Get all roles
        $data['roles'] = $this->roleModel->findAll();
        
        // Get all permissions grouped by module
        $permissions = $this->permissionModel
            ->orderBy('module', 'ASC')
            ->orderBy('permission_name', 'ASC')
            ->findAll();
        
        $permissionsByModule = [];
        foreach ($permissions as $perm) {
            $permissionsByModule[$perm->module][] = $perm;
        }
        
        $data['permissionsByModule'] = $permissionsByModule;
        
        return view('admin/roles/grouppermissions', $data);
    }
    
    /**
     * Get role permissions via AJAX
     */
    public function getRolePermissions()
    {
        // Verificar permissão
        if (!has_permission('roles.permissions')) {
            return $this->response->setJSON(['error' => 'Sem permissão para esta ação']);
        }
        
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Requisição inválida']);
        }
        
        $roleId = $this->request->getGet('role_id');
        
        if (!$roleId) {
            return $this->response->setJSON(['error' => 'Perfil não especificado']);
        }
        
        $role = $this->roleModel->find($roleId);
        
        if (!$role) {
            return $this->response->setJSON(['error' => 'Perfil não encontrado']);
        }
        
        // Buscar permissões do perfil
        $rolePermissions = $this->rolePermissionModel
            ->where('role_id', $roleId)
            ->findAll();
        
        $permissionIds = array_column($rolePermissions, 'permission_id');
        
        // Buscar todas as permissões (para referência)
        $allPermissions = $this->permissionModel
            ->orderBy('module', 'ASC')
            ->orderBy('permission_name', 'ASC')
            ->findAll();
        
        return $this->response->setJSON([
            'success' => true,
            'role' => $role,
            'permission_ids' => $permissionIds,
            'all_permissions' => $allPermissions
        ]);
    }
}