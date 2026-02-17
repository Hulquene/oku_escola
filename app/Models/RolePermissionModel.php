<?php

namespace App\Models;

class RolePermissionModel extends BaseModel
{
    protected $table = 'tbl_role_permissions';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'role_id',
        'permission_id'
    ];
    
    protected $validationRules = [
        'role_id' => 'required|numeric',
        'permission_id' => 'required|numeric'
    ];
    
    /**
     * Update role permissions
     */
    public function updatePermissions($roleId, array $permissions)
    {
        $this->transStart();
        
        // Delete existing
        $this->where('role_id', $roleId)->delete();
        
        // Insert new
        if (!empty($permissions)) {
            $data = [];
            foreach ($permissions as $permissionId) {
                $data[] = [
                    'role_id' => $roleId,
                    'permission_id' => $permissionId
                ];
            }
            $this->insertBatch($data);
        }
        
        $this->transComplete();
        
        return $this->transStatus();
    }
}