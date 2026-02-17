<?php

namespace App\Models;

class StudentFeeModel extends BaseModel
{
    protected $table = 'tbl_student_fees';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'enrollment_id',
        'fee_structure_id',
        'reference_number',
        'due_date',
        'amount',
        'fine_amount',
        'discount_amount',
        'total_amount',
        'status',
        'payment_method',
        'payment_date',
        'payment_reference',
        'observations',
        'created_by'
    ];
    
    protected $validationRules = [
        'enrollment_id' => 'required|numeric',
        'fee_structure_id' => 'required|numeric',
        'reference_number' => 'required|is_unique[tbl_student_fees.reference_number,id,{id}]',
        'due_date' => 'required|valid_date',
        'amount' => 'required|numeric'
    ];
    
    protected $useTimestamps = true;
    
    /**
     * Get student fees with details
     */
    public function getStudentFees($studentId, $status = null)
    {
        $builder = $this->select('
                tbl_student_fees.*,
                tbl_fee_types.type_name,
                tbl_fee_types.type_category,
                tbl_fee_types.is_recurring,
                tbl_academic_years.year_name,
                tbl_classes.class_name
            ')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_student_fees.enrollment_id')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_enrollments.academic_year_id')
            ->join('tbl_fee_structure', 'tbl_fee_structure.id = tbl_student_fees.fee_structure_id')
            ->join('tbl_fee_types', 'tbl_fee_types.id = tbl_fee_structure.fee_type_id')
            ->where('tbl_students.id', $studentId);
        
        if ($status) {
            if (is_array($status)) {
                $builder->whereIn('tbl_student_fees.status', $status);
            } else {
                $builder->where('tbl_student_fees.status', $status);
            }
        }
        
        return $builder->orderBy('tbl_student_fees.due_date', 'ASC')
            ->findAll();
    }
    
    /**
     * Get fees by class
     */
    public function getByClass($classId, $status = null)
    {
        $builder = $this->select('
                tbl_student_fees.*,
                tbl_students.student_number,
                tbl_users.first_name,
                tbl_users.last_name,
                tbl_fee_types.type_name
            ')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_student_fees.enrollment_id')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->join('tbl_fee_structure', 'tbl_fee_structure.id = tbl_student_fees.fee_structure_id')
            ->join('tbl_fee_types', 'tbl_fee_types.id = tbl_fee_structure.fee_type_id')
            ->where('tbl_enrollments.class_id', $classId)
            ->where('tbl_enrollments.status', 'Ativo');
        
        if ($status) {
            if (is_array($status)) {
                $builder->whereIn('tbl_student_fees.status', $status);
            } else {
                $builder->where('tbl_student_fees.status', $status);
            }
        }
        
        return $builder->orderBy('tbl_student_fees.due_date', 'ASC')
            ->findAll();
    }
    
    /**
     * Get overdue fees
     */
    public function getOverdue()
    {
        return $this->select('
                tbl_student_fees.*,
                tbl_students.student_number,
                tbl_users.first_name,
                tbl_users.last_name,
                tbl_users.email,
                tbl_users.phone,
                tbl_classes.class_name
            ')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_student_fees.enrollment_id')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
            ->where('tbl_student_fees.due_date <', date('Y-m-d'))
            ->where('tbl_student_fees.status', 'Pendente')
            ->orderBy('tbl_student_fees.due_date', 'ASC')
            ->findAll();
    }
    
    /**
     * Get fee statistics
     */
    public function getStatistics($academicYearId = null)
    {
        $builder = $this->select('
                COUNT(*) as total,
                SUM(total_amount) as total_amount,
                SUM(CASE WHEN ' . $this->table . '.status = "Pago" THEN total_amount ELSE 0 END) as paid_amount,
                SUM(CASE WHEN ' . $this->table . '.status IN ("Pendente", "Vencido") THEN total_amount ELSE 0 END) as pending_amount,
                COUNT(CASE WHEN ' . $this->table . '.status = "Pago" THEN 1 END) as paid_count,
                COUNT(CASE WHEN ' . $this->table . '.status = "Pendente" THEN 1 END) as pending_count,
                COUNT(CASE WHEN ' . $this->table . '.status = "Vencido" THEN 1 END) as overdue_count
            ');
        
        if ($academicYearId) {
            $builder->join('tbl_enrollments', 'tbl_enrollments.id = ' . $this->table . '.enrollment_id')
                ->where('tbl_enrollments.academic_year_id', $academicYearId);
        }
        
        return $builder->first();
    }
}