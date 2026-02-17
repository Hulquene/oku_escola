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
        $data['title'] = 'Perfis de Acesso';
        $data['roles'] = $this->roleModel->findAll();
        
        return view('admin/roles/index', $data);
    }
    
    /**
     * Role permissions page
     */
    public function role($roleId = null)
    {
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
}