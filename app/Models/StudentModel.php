<?php

namespace App\Models;

class StudentModel extends BaseModel
{
    protected $table = 'tbl_students';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'user_id',
        'student_number',
        'birth_date',
        'birth_place',
        'gender',
        'nationality',
        'identity_document',
        'identity_type',
        'nif',
        'address',
        'city',
        'municipality',
        'province',
        'phone',
        'email',
        'emergency_contact',
        'emergency_contact_name',
        'previous_school',
        'previous_grade',
        'special_needs',
        'health_conditions',
        'blood_type',
        'photo',
        'is_active'
    ];
    
    protected $validationRules = [
        'user_id' => 'required|numeric|is_unique[tbl_students.user_id,id,{id}]',
        'student_number' => 'required|is_unique[tbl_students.student_number,id,{id}]',
        'birth_date' => 'required|valid_date',
        'gender' => 'required',
        'identity_document' => 'permit_empty'
    ];
    
    protected $validationMessages = [
        'user_id' => [
            'is_unique' => 'Este usuário já está associado a um aluno'
        ],
        'student_number' => [
            'is_unique' => 'Este número de aluno já existe'
        ]
    ];
    
    /**
     * Generate student number
     */
    public function generateStudentNumber()
    {
        $year = date('Y');
        $last = $this->select('student_number')
            ->like('student_number', "STU{$year}", 'after')
            ->orderBy('id', 'DESC')
            ->first();
        
        if ($last) {
            $lastNumber = intval(substr($last->student_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return "STU{$year}{$newNumber}";
    }
    
    /**
     * Get student with user info
     */
    public function getWithUser($id)
    {
        return $this->select('tbl_students.*, tbl_users.username, tbl_users.email, tbl_users.first_name, tbl_users.last_name, tbl_users.phone as user_phone')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->where('tbl_students.id', $id)
            ->first();
    }
    
    /**
     * Get student by user id
     */
    public function getByUserId($userId)
    {
        return $this->where('user_id', $userId)->first();
    }
    
    /**
     * Get current enrollment
     */
    public function getCurrentEnrollment($studentId)
    {
        $enrollmentModel = new EnrollmentModel();
        
        return $enrollmentModel->select('tbl_enrollments.*, tbl_classes.class_name, tbl_academic_years.year_name')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_enrollments.academic_year_id')
            ->where('tbl_enrollments.student_id', $studentId)
            ->where('tbl_enrollments.status', 'Ativo')
            ->orderBy('tbl_enrollments.id', 'DESC')
            ->first();
    }
    
    /**
     * Get student academic history
     */
    public function getAcademicHistory($studentId)
    {
        $historyModel = new AcademicHistoryModel();
        
        return $historyModel->select('tbl_academic_history.*, tbl_classes.class_name, tbl_academic_years.year_name')
            ->join('tbl_classes', 'tbl_classes.id = tbl_academic_history.class_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_academic_history.academic_year_id')
            ->where('tbl_academic_history.student_id', $studentId)
            ->orderBy('tbl_academic_years.start_date', 'DESC')
            ->findAll();
    }
    
    /**
     * Get students by class
     */
    public function getByClass($classId)
    {
        return $this->select('tbl_students.*, tbl_users.first_name, tbl_users.last_name, tbl_users.email, tbl_enrollments.enrollment_date')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->join('tbl_enrollments', 'tbl_enrollments.student_id = tbl_students.id')
            ->where('tbl_enrollments.class_id', $classId)
            ->where('tbl_enrollments.status', 'Ativo')
            ->orderBy('tbl_users.first_name', 'ASC')
            ->findAll();
    }
    
    /**
     * Search students
     */
    public function search($query)
    {
        return $this->select('tbl_students.*, tbl_users.first_name, tbl_users.last_name, tbl_users.email')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->groupStart()
                ->like('tbl_users.first_name', $query)
                ->orLike('tbl_users.last_name', $query)
                ->orLike('tbl_students.student_number', $query)
                ->orLike('tbl_students.identity_document', $query)
            ->groupEnd()
            ->where('tbl_students.is_active', 1)
            ->limit(20)
            ->findAll();
    }
    
    /**
     * Get student fees summary
     */
    public function getFeesSummary($studentId)
    {
        $studentFeeModel = new StudentFeeModel();
        
        $total = $studentFeeModel->select('
                SUM(CASE WHEN status = "Pago" THEN total_amount ELSE 0 END) as paid,
                SUM(CASE WHEN status IN ("Pendente", "Vencido") THEN total_amount ELSE 0 END) as pending,
                COUNT(*) as total_fees,
                SUM(CASE WHEN status = "Vencido" THEN 1 ELSE 0 END) as overdue
            ')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_student_fees.enrollment_id')
            ->where('tbl_enrollments.student_id', $studentId)
            ->first();
        
        return $total;
    }
}