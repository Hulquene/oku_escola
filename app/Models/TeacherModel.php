<?php
// app/Models/TeacherModel.php

namespace App\Models;

class TeacherModel extends BaseModel
{
    protected $table = 'tbl_teachers';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'user_id',
        'birth_date',
        'gender',
        'nationality',
        'identity_document',
        'identity_type',
        'nif',
        'city',
        'municipality',
        'province',
        'address',
        'emergency_contact',
        'emergency_contact_name',
        'qualifications',
        'specialization',
        'admission_date',
        'bank_account',
        'bank_name',
        'is_active'
    ];
    
    protected $validationRules = [
        'user_id' => 'required|numeric|is_unique[tbl_teachers.user_id,id,{id}]',
        'birth_date' => 'permit_empty|valid_date',
        'gender' => 'permit_empty|in_list[Masculino,Feminino]',
        'identity_document' => 'permit_empty|max_length[50]',
        'identity_type' => 'permit_empty|in_list[BI,Passaporte,Cédula,Outro]',
        'nif' => 'permit_empty|max_length[20]'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    /**
     * Get teacher by user id
     */
    public function getByUserId($userId)
    {
        return $this->where('user_id', $userId)->first();
    }
    
    /**
     * Get teacher with user info
     */
    public function getWithUser($id)
    {
        return $this->select('
                tbl_teachers.*,
                tbl_users.username,
                tbl_users.email,
                tbl_users.first_name,
                tbl_users.last_name,
                tbl_users.phone,
                tbl_users.photo,
                tbl_users.is_active,
                tbl_users.last_login, 
                tbl_users.created_at as user_created_at,
                tbl_users.updated_at as user_updated_at,
                CONCAT(tbl_users.first_name, " ", tbl_users.last_name) as full_name
            ')
            ->join('tbl_users', 'tbl_users.id = tbl_teachers.user_id')
            ->where('tbl_teachers.id', $id)
            ->first();
    }
    
    /**
     * Get all teachers with user info
     */
    public function getAllWithUser()
    {
        return $this->select('
                tbl_teachers.*,
                tbl_users.username,
                tbl_users.email,
                tbl_users.first_name,
                tbl_users.last_name,
                tbl_users.phone,
                tbl_users.photo,
                tbl_users.is_active,
                tbl_users.last_login,  // <-- ADICIONAR ESTA LINHA
                CONCAT(tbl_users.first_name, " ", tbl_users.last_name) as full_name
            ')
            ->join('tbl_users', 'tbl_users.id = tbl_teachers.user_id')
            ->where('tbl_users.user_type', 'teacher')
            ->orderBy('tbl_users.first_name', 'ASC')
            ->findAll();
    }
}