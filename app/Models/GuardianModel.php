<?php

namespace App\Models;

class GuardianModel extends BaseModel
{
    protected $table = 'tbl_guardians';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'user_id',
        'guardian_type',
        'full_name',
        'identity_document',
        'identity_type',
        'nif',
        'profession',
        'workplace',
        'work_phone',
        'phone',
        'email',
        'address',
        'city',
        'municipality',
        'province',
        'is_primary',
        'is_active'
    ];
    
    protected $validationRules = [
        'full_name' => 'required',
        'phone' => 'required',
        'guardian_type' => 'required'
    ];
    
    /**
     * Get guardians by student
     */
    public function getByStudent($studentId)
    {
        return $this->select('tbl_guardians.*, tbl_student_guardians.relationship, tbl_student_guardians.is_authorized')
            ->join('tbl_student_guardians', 'tbl_student_guardians.guardian_id = tbl_guardians.id')
            ->where('tbl_student_guardians.student_id', $studentId)
            ->where('tbl_guardians.is_active', 1)
            ->orderBy('tbl_guardians.is_primary', 'DESC')
            ->findAll();
    }
    
    /**
     * Get primary guardian
     */
    public function getPrimaryByStudent($studentId)
    {
        return $this->select('tbl_guardians.*')
            ->join('tbl_student_guardians', 'tbl_student_guardians.guardian_id = tbl_guardians.id')
            ->where('tbl_student_guardians.student_id', $studentId)
            ->where('tbl_guardians.is_primary', 1)
            ->where('tbl_guardians.is_active', 1)
            ->first();
    }
    
    /**
     * Get students by guardian
     */
    public function getStudents($guardianId)
    {
        $studentModel = new StudentModel();
        
        return $studentModel->select('tbl_students.*, tbl_users.first_name, tbl_users.last_name, tbl_student_guardians.relationship')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->join('tbl_student_guardians', 'tbl_student_guardians.student_id = tbl_students.id')
            ->where('tbl_student_guardians.guardian_id', $guardianId)
            ->where('tbl_students.is_active', 1)
            ->findAll();
    }
}