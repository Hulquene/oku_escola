<?php

namespace App\Models;

class RoleModel extends BaseModel
{
    protected $table = 'tbl_roles';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'role_name',
        'role_description'
    ];
    
    protected $validationRules = [
        'role_name' => 'required|min_length[3]|is_unique[tbl_roles.role_name,id,{id}]'
    ];
    
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