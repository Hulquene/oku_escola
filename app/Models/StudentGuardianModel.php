<?php

namespace App\Models;

class StudentGuardianModel extends BaseModel
{
    protected $table = 'tbl_student_guardians';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'student_id',
        'guardian_id',
        'relationship',
        'is_authorized'
    ];
    
    protected $validationRules = [
        'student_id' => 'required|numeric',
        'guardian_id' => 'required|numeric',
        'relationship' => 'permit_empty'
    ];
    
    /**
     * Link guardian to student
     */
    public function linkGuardian($studentId, $guardianId, $relationship = null, $isAuthorized = true)
    {
        $data = [
            'student_id' => $studentId,
            'guardian_id' => $guardianId,
            'relationship' => $relationship,
            'is_authorized' => $isAuthorized ? 1 : 0
        ];
        
        // Check if already exists
        $exists = $this->where('student_id', $studentId)
            ->where('guardian_id', $guardianId)
            ->first();
        
        if ($exists) {
            return $this->update($exists->id, $data);
        } else {
            return $this->insert($data);
        }
    }
    
    /**
     * Unlink guardian from student
     */
    public function unlinkGuardian($studentId, $guardianId)
    {
        return $this->where('student_id', $studentId)
            ->where('guardian_id', $guardianId)
            ->delete();
    }
    
    /**
     * Get authorized guardians for student
     */
    public function getAuthorizedGuardians($studentId)
    {
        return $this->select('tbl_guardians.*')
            ->join('tbl_guardians', 'tbl_guardians.id = tbl_student_guardians.guardian_id')
            ->where('tbl_student_guardians.student_id', $studentId)
            ->where('tbl_student_guardians.is_authorized', 1)
            ->where('tbl_guardians.is_active', 1)
            ->findAll();
    }
}