<?php

namespace App\Models;

class UserModel extends BaseModel
{
    protected $table = 'tbl_users';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'username',
        'email',
        'password',
        'first_name',
        'last_name',
        'phone',
        'address',
        'photo',
        'role_id',
        'user_type',
        'is_active',
        'last_login'
    ];
    
    protected $validationRules = [
        'username' => 'required|min_length[3]|is_unique[tbl_users.username,id,{id}]',
        'email' => 'required|valid_email|is_unique[tbl_users.email,id,{id}]',
        'first_name' => 'required',
        'last_name' => 'required',
        'role_id' => 'required|numeric',
        'user_type' => 'required'
    ];
    
    protected $validationMessages = [
        'email' => [
            'is_unique' => 'Este email já está em uso'
        ],
        'username' => [
            'is_unique' => 'Este nome de usuário já está em uso'
        ]
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    /**
     * Hash password before insert
     */
    protected function hashPassword(array $data)
    {
        if (isset($data['data']['password'])) {
            $data['data']['password'] = password_hash($data['data']['password'], PASSWORD_DEFAULT);
        }
        return $data;
    }
    
    /**
     * Get users by role
     */
    public function getByRole($roleId)
    {
        return $this->where('role_id', $roleId)
            ->where('is_active', 1)
            ->findAll();
    }
    
    /**
     * Get users by type
     */
    public function getByType($type)
    {
        return $this->where('user_type', $type)
            ->where('is_active', 1)
            ->findAll();
    }
    
    /**
     * Get full user data with role
     */
    public function getFullUser($id)
    {
        return $this->select('tbl_users.*, tbl_roles.role_name')
            ->join('tbl_roles', 'tbl_roles.id = tbl_users.role_id')
            ->where('tbl_users.id', $id)
            ->first();
    }
    
    /**
     * Find user by email or username
     */
    public function findByEmailOrUsername($login)
    {
        return $this->where('username', $login)
            ->orWhere('email', $login)
            ->where('is_active', 1)
            ->first();
    }
    
    /**
     * Update last login
     */
    public function updateLastLogin($userId)
    {
        return $this->update($userId, ['last_login' => date('Y-m-d H:i:s')]);
    }
}