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
        'id' => 'permit_empty|is_natural_no_zero',  // <-- IMPORTANTE: Adicionar regra para o ID
        'user_id' => 'required|numeric|is_unique[tbl_students.user_id,id,{id}]',
        'student_number' => 'required|is_unique[tbl_students.student_number,id,{id}]',
        'birth_date' => 'permit_empty|valid_date',
        'gender' => 'required|in_list[Masculino,Feminino]',
        'nationality' => 'permit_empty|max_length[100]',
        'identity_document' => 'permit_empty|max_length[50]',
        'identity_type' => 'permit_empty|in_list[BI,Passaporte,Cédula,Outro]',
        'nif' => 'permit_empty|max_length[20]',
        'email' => 'permit_empty|valid_email',
        'phone' => 'permit_empty|max_length[20]',
        'emergency_contact' => 'permit_empty|max_length[20]',
        'emergency_contact_name' => 'permit_empty|max_length[255]',
        'blood_type' => 'permit_empty|max_length[5]',
        'is_active' => 'permit_empty|in_list[0,1]'
    ];
    
    protected $validationMessages = [
        'user_id' => [
            'required' => 'O ID do usuário é obrigatório',
            'numeric' => 'O ID do usuário deve ser um número',
            'is_unique' => 'Este usuário já está associado a um aluno'
        ],
        'student_number' => [
            'required' => 'O número do aluno é obrigatório',
            'is_unique' => 'Este número de aluno já existe'
        ],
        'gender' => [
            'required' => 'O gênero é obrigatório',
            'in_list' => 'Gênero inválido. Deve ser Masculino ou Feminino'
        ]
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
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
        
        if ($last && isset($last->student_number)) {
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
        return $this->select('
                tbl_students.*, 
                tbl_users.username, 
                tbl_users.email as user_email, 
                tbl_users.first_name, 
                tbl_users.last_name, 
                tbl_users.phone as user_phone,
                CONCAT(tbl_users.first_name, " ", tbl_users.last_name) as full_name
            ')
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
        $enrollmentModel = new \App\Models\EnrollmentModel();
        
        return $enrollmentModel->select('
                tbl_enrollments.*, 
                tbl_classes.class_name,
                tbl_classes.class_code,
                tbl_classes.class_shift,
                tbl_academic_years.year_name,
                tbl_academic_years.start_date as year_start,
                tbl_academic_years.end_date as year_end
            ')
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
        $enrollmentModel = new \App\Models\EnrollmentModel(); // Usar EnrollmentModel em vez de AcademicHistoryModel
        
        return $enrollmentModel->select('
                tbl_enrollments.*, 
                tbl_classes.class_name,
                tbl_classes.class_code,
                tbl_academic_years.year_name,
                tbl_academic_years.start_date,
                tbl_academic_years.end_date
            ')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_enrollments.academic_year_id')
            ->where('tbl_enrollments.student_id', $studentId)
            ->orderBy('tbl_academic_years.start_date', 'DESC')
            ->findAll();
    }
    
    /**
     * Get students by class
     */
    public function getByClass($classId)
    {
        return $this->select('
                tbl_students.*, 
                tbl_users.first_name, 
                tbl_users.last_name, 
                tbl_users.email,
                tbl_enrollments.enrollment_date,
                tbl_enrollments.enrollment_number
            ')
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
        return $this->select('
                tbl_students.*, 
                tbl_users.first_name, 
                tbl_users.last_name, 
                tbl_users.email,
                CONCAT(tbl_users.first_name, " ", tbl_users.last_name) as full_name
            ')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->groupStart()
                ->like('tbl_users.first_name', $query)
                ->orLike('tbl_users.last_name', $query)
                ->orLike('CONCAT(tbl_users.first_name, " ", tbl_users.last_name)', $query)
                ->orLike('tbl_students.student_number', $query)
                ->orLike('tbl_students.identity_document', $query)
                ->orLike('tbl_students.email', $query)
            ->groupEnd()
            ->where('tbl_students.is_active', 1)
            ->orderBy('tbl_users.first_name', 'ASC')
            ->limit(20)
            ->findAll();
    }
    
    /**
     * Get student fees summary
     */
    public function getFeesSummary($studentId)
    {
        $db = db_connect();
        
        $builder = $db->table('tbl_student_fees sf');
        $builder->select('
                SUM(CASE WHEN sf.status = "Pago" THEN sf.total_amount ELSE 0 END) as paid,
                SUM(CASE WHEN sf.status IN ("Pendente", "Vencido") THEN sf.total_amount ELSE 0 END) as pending,
                COUNT(*) as total_fees,
                SUM(CASE WHEN sf.status = "Vencido" THEN 1 ELSE 0 END) as overdue
            ')
            ->join('tbl_enrollments e', 'e.id = sf.enrollment_id')
            ->where('e.student_id', $studentId);
        
        $result = $builder->get()->getRow();
        
        // Garantir que o resultado não seja nulo
        if (!$result) {
            return (object)[
                'paid' => 0,
                'pending' => 0,
                'total_fees' => 0,
                'overdue' => 0
            ];
        }
        
        return $result;
    }
    
    /**
     * Count active students
     */
    public function countActive()
    {
        return $this->where('is_active', 1)->countAllResults();
    }
    
    /**
     * Get students by birth month (for birthday list)
     */
    public function getByBirthMonth($month)
    {
        return $this->select('
                tbl_students.*,
                tbl_users.first_name,
                tbl_users.last_name,
                tbl_users.email,
                MONTH(tbl_students.birth_date) as birth_month,
                DAY(tbl_students.birth_date) as birth_day
            ')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->where('tbl_students.is_active', 1)
            ->where('MONTH(tbl_students.birth_date)', $month)
            ->orderBy('DAY(tbl_students.birth_date)', 'ASC')
            ->findAll();
    }
    
    /**
     * Get students statistics
     */
    public function getStatistics()
    {
        $total = $this->countAll();
        $active = $this->countActive();
        $male = $this->where('gender', 'Masculino')->countAllResults();
        $female = $this->where('gender', 'Feminino')->countAllResults();
        
        return [
            'total' => $total,
            'active' => $active,
            'inactive' => $total - $active,
            'male' => $male,
            'female' => $female
        ];
    }
    
    /**
     * Get student by document number
     */
    public function getByIdentityDocument($docNumber)
    {
        return $this->where('identity_document', $docNumber)->first();
    }
    
    /**
     * Get student by NIF
     */
    public function getByNif($nif)
    {
        return $this->where('nif', $nif)->first();
    }
    
    /**
     * Update student photo
     */
    public function updatePhoto($studentId, $photoPath)
    {
        return $this->update($studentId, ['photo' => $photoPath]);
    }
    /**
 * Count students by enrollment status
 */
public function countByEnrollmentStatus($status = null)
{
    $builder = $this->db->table('tbl_students s')
        ->join('tbl_enrollments e', 'e.student_id = s.id', 'left');
    
    if ($status) {
        $builder->where('e.status', $status);
    }
    
    return $builder->countAllResults();
}
}