<?php

namespace App\Models;

class RoleModel extends BaseModel
{
    protected $table = 'tbl_roles';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'role_name',
        'role_description',
        'role_type'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    protected $returnType = 'object';
    protected $useSoftDeletes = false;
    
    protected $validationRules = [
        'role_name' => 'required|min_length[3]|is_unique[tbl_roles.role_name,id,{id}]',
        'role_type' => 'required|in_list[admin,teacher,student,staff]'
    ];
    
    protected $validationMessages = [
        'role_name' => [
            'required' => 'O nome do perfil é obrigatório',
            'min_length' => 'O nome do perfil deve ter pelo menos 3 caracteres',
            'is_unique' => 'Este nome de perfil já existe'
        ],
        'role_type' => [
            'required' => 'O tipo do perfil é obrigatório',
            'in_list' => 'Tipo de perfil inválido'
        ]
    ];
    
    /**
     * Get system roles (tipos especiais)
     */
    public function getSystemRoles()
    {
        return $this->whereIn('role_type', ['admin', 'teacher', 'student'])->findAll();
    }
    
    /**
     * Check if role is system protected
     */
    public function isSystemRole($id)
    {
        $role = $this->find($id);
        return $role && in_array($role->role_type, ['admin', 'teacher', 'student']);
    }
    
    /**
     * Get roles with permissions
     */
    public function getWithPermissions()
    {
        return $this->select('tbl_roles.*, GROUP_CONCAT(tbl_permissions.permission_key) as permissions')
            ->join('tbl_role_permissions', 'tbl_role_permissions.role_id = tbl_roles.id', 'left')
            ->join('tbl_permissions', 'tbl_permissions.id = tbl_role_permissions.permission_id', 'left')
            ->groupBy('tbl_roles.id')
            ->findAll();
    }
}