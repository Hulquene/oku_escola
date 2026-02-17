<?php

namespace App\Models;

class PermissionModel extends BaseModel
{
    protected $table = 'tbl_permissions';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'permission_name',
        'permission_key',
        'module'
    ];
    
    protected $validationRules = [
        'permission_name' => 'required',
        'permission_key' => 'required|is_unique[tbl_permissions.permission_key,id,{id}]',
        'module' => 'required'
    ];
    
    /**
     * Get permissions by module
     */
    public function getByModule()
    {
        return $this->select('module, GROUP_CONCAT(permission_name) as permissions')
            ->groupBy('module')
            ->findAll();
    }
    
    /**
     * Get permissions by role
     */
    public function getByRole($roleId)
    {
        return $this->select('tbl_permissions.*')
            ->join('tbl_role_permissions', 'tbl_role_permissions.permission_id = tbl_permissions.id')
            ->where('tbl_role_permissions.role_id', $roleId)
            ->findAll();
    }
}