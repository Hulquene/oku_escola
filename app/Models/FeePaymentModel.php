<?php

namespace App\Models;

class FeePaymentModel extends BaseModel
{
    protected $table = 'tbl_fee_payments';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'student_fee_id',
        'payment_number',
        'payment_date',
        'amount_paid',
        'payment_method',
        'payment_reference',
        'bank_name',
        'bank_account',
        'check_number',
        'receipt_number',
        'observations',
        'received_by'
    ];
    
    protected $validationRules = [
        'student_fee_id' => 'required|numeric',
        'payment_number' => 'required|is_unique[tbl_fee_payments.payment_number,id,{id}]',
        'payment_date' => 'required|valid_date',
        'amount_paid' => 'required|numeric'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = null;
    
    /**
     * Generate payment number
     */
    public function generatePaymentNumber()
    {
        $year = date('Y');
        $month = date('m');
        $last = $this->select('payment_number')
            ->like('payment_number', "PAY{$year}{$month}", 'after')
            ->orderBy('id', 'DESC')
            ->first();
        
        if ($last) {
            $lastNumber = intval(substr($last->payment_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return "PAY{$year}{$month}{$newNumber}";
    }
    
    /**
     * Process payment
     */
    public function processPayment($studentFeeId, $amount, $method, $data = [])
    {
        $studentFeeModel = new StudentFeeModel();
        $studentFee = $studentFeeModel->find($studentFeeId);
        
        if (!$studentFee) {
            return false;
        }
        
        $this->transStart();
        
        // Create payment record
        $paymentData = [
            'student_fee_id' => $studentFeeId,
            'payment_number' => $this->generatePaymentNumber(),
            'payment_date' => $data['payment_date'] ?? date('Y-m-d'),
            'amount_paid' => $amount,
            'payment_method' => $method,
            'payment_reference' => $data['reference'] ?? null,
            'bank_name' => $data['bank_name'] ?? null,
            'bank_account' => $data['bank_account'] ?? null,
            'check_number' => $data['check_number'] ?? null,
            'receipt_number' => $data['receipt_number'] ?? null,
            'observations' => $data['observations'] ?? null,
            'received_by' => session()->get('user_id')
        ];
        
        $this->insert($paymentData);
        
        // Update student fee status (trigger will handle this, but we need to trigger it)
        $studentFeeModel->update($studentFeeId, [
            'payment_method' => $method,
            'payment_reference' => $data['reference'] ?? null
        ]);
        
        $this->transComplete();
        
        return $this->transStatus();
    }
    
    /**
     * Get payments by student
     */
    public function getByStudent($studentId)
    {
        return $this->select('
                tbl_fee_payments.*,
                tbl_student_fees.reference_number,
                tbl_fee_types.type_name,
                tbl_academic_years.year_name
            ')
            ->join('tbl_student_fees', 'tbl_student_fees.id = tbl_fee_payments.student_fee_id')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_student_fees.enrollment_id')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_fee_structure', 'tbl_fee_structure.id = tbl_student_fees.fee_structure_id')
            ->join('tbl_fee_types', 'tbl_fee_types.id = tbl_fee_structure.fee_type_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_enrollments.academic_year_id')
            ->where('tbl_students.id', $studentId)
            ->orderBy('tbl_fee_payments.payment_date', 'DESC')
            ->findAll();
    }
    
    /**
     * Get daily collections
     */
    public function getDailyCollections($date = null)
    {
        if (!$date) {
            $date = date('Y-m-d');
        }
        
        return $this->select('
                SUM(amount_paid) as total,
                COUNT(*) as transactions,
                payment_method,
                COUNT(DISTINCT student_fee_id) as fees_paid
            ')
            ->where('payment_date', $date)
            ->groupBy('payment_method')
            ->findAll();
    }
}