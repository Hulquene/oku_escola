<?php

namespace App\Controllers\students;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use App\Models\EnrollmentModel;
use App\Models\StudentFeeModel;
use App\Models\FeePaymentModel;
use App\Models\SettingsModel;

class Fees extends BaseController
{
    protected $studentModel;
    protected $enrollmentModel;
    protected $studentFeeModel;
    protected $feePaymentModel;
    protected $settingsModel;
    
    public function __construct()
    {
        $this->studentModel = new StudentModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->studentFeeModel = new StudentFeeModel();
        $this->feePaymentModel = new FeePaymentModel();
        $this->settingsModel = new SettingsModel();
        
        // Check if user is student
   /*      if ($this->session->get('user_type') !== 'student') {
            return redirect()->to('/auth/login')->with('error', 'Acesso não autorizado');
        } */
    }
    
    /**
     * My fees
     */
    public function index()
    {
        $data['title'] = 'Minhas Propinas';
        
        $userId = $this->session->get('user_id');
        $student = $this->studentModel->getByUserId($userId);
        
        if (!$student) {
            return redirect()->to('/students/dashboard')->with('error', 'Perfil não encontrado');
        }
        
        // Get current enrollment
        $enrollment = $this->studentModel->getCurrentEnrollment($student->id);
        
        if (!$enrollment) {
            return redirect()->to('/students/dashboard')->with('error', 'Nenhuma matrícula ativa encontrada');
        }
        
        // Get all fees for this enrollment
        $data['fees'] = $this->studentFeeModel
            ->select('
                tbl_student_fees.*,
                tbl_fee_types.type_name,
                tbl_fee_types.type_category,
                (SELECT SUM(amount_paid) FROM tbl_fee_payments WHERE student_fee_id = tbl_student_fees.id) as paid_amount
            ')
            ->join('tbl_fee_structure', 'tbl_fee_structure.id = tbl_student_fees.fee_structure_id')
            ->join('tbl_fee_types', 'tbl_fee_types.id = tbl_fee_structure.fee_type_id')
            ->where('tbl_student_fees.enrollment_id', $enrollment->id)
            ->orderBy('tbl_student_fees.due_date', 'ASC')
            ->findAll();
        
        // Calculate totals
        $totalAmount = 0;
        $totalPaid = 0;
        $totalPending = 0;
        
        foreach ($data['fees'] as $fee) {
            $totalAmount += $fee->total_amount;
            $paid = $fee->paid_amount ?? 0;
            $totalPaid += $paid;
            $totalPending += ($fee->total_amount - $paid);
        }
        
        $data['totals'] = (object)[
            'total' => $totalAmount,
            'paid' => $totalPaid,
            'pending' => $totalPending
        ];
        
        $data['enrollment'] = $enrollment;
        
        return view('students/fees/index', $data);
    }
    
    /**
     * Payment history
     */
    public function history()
    {
        $data['title'] = 'Histórico de Pagamentos';
        
        $userId = $this->session->get('user_id');
        $student = $this->studentModel->getByUserId($userId);
        
        if (!$student) {
            return redirect()->to('/students/dashboard')->with('error', 'Perfil não encontrado');
        }
        
        // Get all payments for this student
        $data['payments'] = $this->feePaymentModel
            ->select('
                tbl_fee_payments.*,
                tbl_student_fees.reference_number,
                tbl_fee_types.type_name,
                tbl_academic_years.year_name
            ')
            ->join('tbl_student_fees', 'tbl_student_fees.id = tbl_fee_payments.student_fee_id')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_student_fees.enrollment_id')
            ->join('tbl_fee_structure', 'tbl_fee_structure.id = tbl_student_fees.fee_structure_id')
            ->join('tbl_fee_types', 'tbl_fee_types.id = tbl_fee_structure.fee_type_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_enrollments.academic_year_id')
            ->where('tbl_enrollments.student_id', $student->id)
            ->orderBy('tbl_fee_payments.payment_date', 'DESC')
            ->findAll();
        
        return view('students/fees/history', $data);
    }
    
    /**
     * Pay fee (redirect to payment gateway or show instructions)
     */
    public function pay($feeId)
    {
        $fee = $this->studentFeeModel
            ->select('tbl_student_fees.*, tbl_fee_types.type_name, tbl_fee_types.type_category')
            ->join('tbl_fee_structure', 'tbl_fee_structure.id = tbl_student_fees.fee_structure_id')
            ->join('tbl_fee_types', 'tbl_fee_types.id = tbl_fee_structure.fee_type_id')
            ->where('tbl_student_fees.id', $feeId)
            ->first();
        
        if (!$fee) {
            return redirect()->to('/students/fees')->with('error', 'Propina não encontrada');
        }
        
        // Get student info
        $userId = $this->session->get('user_id');
        $student = $this->studentModel->getByUserId($userId);
        
        $data['title'] = 'Pagamento de Propina';
        $data['fee'] = $fee;
        $data['student'] = $student;
        
        // Get school info
        $data['school'] = (object)[
            'name' => $this->settingsModel->get('school_name') ?? 'Escola Angolana',
            'bank_name' => $this->settingsModel->get('bank_name') ?? 'Banco Angolano',
            'bank_account' => $this->settingsModel->get('bank_account') ?? '0000 0000 0000 0000',
            'bank_iban' => $this->settingsModel->get('bank_iban') ?? 'AO00 0000 0000 0000 0000 0000 0',
            'bank_swift' => $this->settingsModel->get('bank_swift') ?? 'XXXXXX'
        ];
        
        return view('students/fees/pay', $data);
    }
    
    /**
     * Receipts list
     */
    public function receipts()
    {
        $data['title'] = 'Meus Recibos';
        
        $userId = $this->session->get('user_id');
        $student = $this->studentModel->getByUserId($userId);
        
        if (!$student) {
            return redirect()->to('/students/dashboard')->with('error', 'Perfil não encontrado');
        }
        
        // Get all receipts (payments with receipt number)
        $data['receipts'] = $this->feePaymentModel
            ->select('
                tbl_fee_payments.*,
                tbl_student_fees.reference_number,
                tbl_fee_types.type_name,
                tbl_academic_years.year_name
            ')
            ->join('tbl_student_fees', 'tbl_student_fees.id = tbl_fee_payments.student_fee_id')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_student_fees.enrollment_id')
            ->join('tbl_fee_structure', 'tbl_fee_structure.id = tbl_student_fees.fee_structure_id')
            ->join('tbl_fee_types', 'tbl_fee_types.id = tbl_fee_structure.fee_type_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_enrollments.academic_year_id')
            ->where('tbl_enrollments.student_id', $student->id)
            ->where('tbl_fee_payments.receipt_number IS NOT NULL')
            ->orderBy('tbl_fee_payments.payment_date', 'DESC')
            ->findAll();
        
        return view('students/fees/receipts', $data);
    }
}