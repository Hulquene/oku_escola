<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\StudentFeeModel;
use App\Models\FeePaymentModel;
use App\Models\StudentModel;
use App\Models\ClassModel;
use App\Models\EnrollmentModel;
use App\Models\FeeStructureModel;

class FeesPayments extends BaseController
{
    protected $studentFeeModel;
    protected $feePaymentModel;
    protected $studentModel;
    protected $classModel;
    protected $enrollmentModel;
    protected $feeStructureModel;
    
    public function __construct()
    {
        $this->studentFeeModel = new StudentFeeModel();
        $this->feePaymentModel = new FeePaymentModel();
        $this->studentModel = new StudentModel();
        $this->classModel = new ClassModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->feeStructureModel = new FeeStructureModel();
    }
    
    /**
     * List payments
     */
    public function index()
    {
        $data['title'] = 'Pagamentos de Propinas';
        
        $status = $this->request->getGet('status');
        $classId = $this->request->getGet('class');
        $studentId = $this->request->getGet('student');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        
        $builder = $this->studentFeeModel
            ->select('
                tbl_student_fees.*,
                tbl_users.first_name,
                tbl_users.last_name,
                tbl_students.student_number,
                tbl_fee_types.type_name,
                tbl_fee_types.type_category,
                tbl_classes.class_name,
                tbl_classes.class_code,
                (SELECT SUM(amount_paid) FROM tbl_fee_payments WHERE student_fee_id = tbl_student_fees.id) as total_paid
            ')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_student_fees.enrollment_id')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
            ->join('tbl_fee_structure', 'tbl_fee_structure.id = tbl_student_fees.fee_structure_id')
            ->join('tbl_fee_types', 'tbl_fee_types.id = tbl_fee_structure.fee_type_id');
        
        if ($status) {
            $builder->where('tbl_student_fees.status', $status);
        }
        
        if ($classId) {
            $builder->where('tbl_enrollments.class_id', $classId);
        }
        
        if ($studentId) {
            $builder->where('tbl_students.id', $studentId);
        }
        
        if ($startDate) {
            $builder->where('tbl_student_fees.due_date >=', $startDate);
        }
        
        if ($endDate) {
            $builder->where('tbl_student_fees.due_date <=', $endDate);
        }
        
        $data['payments'] = $builder->orderBy('tbl_student_fees.due_date', 'ASC')
            ->orderBy('tbl_users.first_name', 'ASC')
            ->paginate(20);
        
        $data['pager'] = $this->studentFeeModel->pager;
        
        // Filters data
        $data['classes'] = $this->classModel
            ->select('tbl_classes.id, tbl_classes.class_name, tbl_classes.class_code')
            ->where('tbl_classes.is_active', 1)
            ->orderBy('tbl_classes.class_name', 'ASC')
            ->findAll();
        
        $data['students'] = $this->studentModel
            ->select('tbl_students.id, tbl_users.first_name, tbl_users.last_name, tbl_students.student_number')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->where('tbl_students.is_active', 1)
            ->orderBy('tbl_users.first_name', 'ASC')
            ->limit(100)
            ->findAll();
        
        $data['selectedStatus'] = $status;
        $data['selectedClass'] = $classId;
        $data['selectedStudent'] = $studentId;
        $data['selectedStartDate'] = $startDate;
        $data['selectedEndDate'] = $endDate;
        
        return view('admin/fees/payments/index', $data);
    }
    
    /**
     * Payment form
     */
    public function form($studentFeeId = null)
    {
        if (!$studentFeeId) {
            return redirect()->to('/admin/fees/payments')->with('error', 'Propina não especificada');
        }
        
        $data['title'] = 'Registrar Pagamento';
        $data['fee'] = $this->studentFeeModel
            ->select('
                tbl_student_fees.*,
                tbl_users.first_name,
                tbl_users.last_name,
                tbl_students.student_number,
                tbl_fee_types.type_name,
                tbl_fee_types.type_category,
                tbl_classes.class_name,
                (SELECT SUM(amount_paid) FROM tbl_fee_payments WHERE student_fee_id = tbl_student_fees.id) as total_paid
            ')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_student_fees.enrollment_id')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
            ->join('tbl_fee_structure', 'tbl_fee_structure.id = tbl_student_fees.fee_structure_id')
            ->join('tbl_fee_types', 'tbl_fee_types.id = tbl_fee_structure.fee_type_id')
            ->where('tbl_student_fees.id', $studentFeeId)
            ->first();
        
        if (!$data['fee']) {
            return redirect()->to('/admin/fees/payments')->with('error', 'Propina não encontrada');
        }
        
        // Get payment history
        $data['payments'] = $this->feePaymentModel
            ->where('student_fee_id', $studentFeeId)
            ->orderBy('payment_date', 'DESC')
            ->findAll();
        
        $data['balance'] = $data['fee']->total_amount - ($data['fee']->total_paid ?? 0);
        
        return view('admin/fees/payments/form', $data);
    }
    
    /**
     * Save payment
     */
    public function save()
    {
        $rules = [
            'student_fee_id' => 'required|numeric',
            'amount_paid' => 'required|numeric|greater_than[0]',
            'payment_date' => 'required|valid_date',
            'payment_method' => 'required'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        $studentFeeId = $this->request->getPost('student_fee_id');
        $amount = $this->request->getPost('amount_paid');
        
        // Check if amount is valid
        $fee = $this->studentFeeModel->find($studentFeeId);
        if (!$fee) {
            return redirect()->back()->with('error', 'Propina não encontrada');
        }
        
        $totalPaid = $this->feePaymentModel
            ->select('SUM(amount_paid) as total')
            ->where('student_fee_id', $studentFeeId)
            ->first()
            ->total ?? 0;
        
        if (($totalPaid + $amount) > $fee->total_amount) {
            return redirect()->back()->withInput()
                ->with('error', 'O valor do pagamento excede o valor total da propina');
        }
        
        $paymentData = [
            'student_fee_id' => $studentFeeId,
            'payment_number' => $this->feePaymentModel->generatePaymentNumber(),
            'payment_date' => $this->request->getPost('payment_date'),
            'amount_paid' => $amount,
            'payment_method' => $this->request->getPost('payment_method'),
            'payment_reference' => $this->request->getPost('payment_reference'),
            'bank_name' => $this->request->getPost('bank_name'),
            'bank_account' => $this->request->getPost('bank_account'),
            'check_number' => $this->request->getPost('check_number'),
            'observations' => $this->request->getPost('observations'),
            'received_by' => session()->get('user_id')
        ];
        
        $result = $this->feePaymentModel->insert($paymentData);
        
        if ($result) {
            // Update fee status
            $newTotalPaid = $totalPaid + $amount;
            if ($newTotalPaid >= $fee->total_amount) {
                $this->studentFeeModel->update($studentFeeId, ['status' => 'Pago']);
            } else {
                $this->studentFeeModel->update($studentFeeId, ['status' => 'Parcial']);
            }
            
            return redirect()->to('/admin/fees/payments/receipt/' . $result)
                ->with('success', 'Pagamento registado com sucesso');
        } else {
            return redirect()->back()->with('error', 'Erro ao registar pagamento');
        }
    }
    
    /**
     * Payment receipt
     */
    public function receipt($id)
    {
        $data['title'] = 'Recibo de Pagamento';
        
        $data['payment'] = $this->feePaymentModel
            ->select('
                tbl_fee_payments.*,
                tbl_student_fees.reference_number as fee_reference,
                tbl_student_fees.total_amount,
                tbl_student_fees.due_date,
                tbl_users.first_name,
                tbl_users.last_name,
                tbl_students.student_number,
                tbl_fee_types.type_name,
                tbl_fee_types.type_category,
                tbl_classes.class_name,
                tbl_classes.class_code,
                tbl_academic_years.year_name
            ')
            ->join('tbl_student_fees', 'tbl_student_fees.id = tbl_fee_payments.student_fee_id')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_student_fees.enrollment_id')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_enrollments.academic_year_id')
            ->join('tbl_fee_structure', 'tbl_fee_structure.id = tbl_student_fees.fee_structure_id')
            ->join('tbl_fee_types', 'tbl_fee_types.id = tbl_fee_structure.fee_type_id')
            ->where('tbl_fee_payments.id', $id)
            ->first();
        
        if (!$data['payment']) {
            return redirect()->to('/admin/fees/payments')->with('error', 'Pagamento não encontrado');
        }
        
        // Get school info
        $settingsModel = new \App\Models\SettingsModel();
        $data['school'] = (object)[
            'name' => $settingsModel->get('school_name') ?? 'Escola Angolana',
            'address' => $settingsModel->get('school_address') ?? 'Luanda, Angola',
            'phone' => $settingsModel->get('school_phone') ?? '+244 000 000 000',
            'email' => $settingsModel->get('school_email') ?? 'info@escola.ao',
            'nif' => $settingsModel->get('school_nif') ?? '000000000'
        ];
        
        return view('admin/fees/payments/receipt', $data);
    }
    
    /**
     * Payment history for student
     */
    public function history($studentId)
    {
        $student = $this->studentModel->getWithUser($studentId);
        
        if (!$student) {
            return redirect()->to('/admin/students')->with('error', 'Aluno não encontrado');
        }
        
        $data['title'] = 'Histórico de Pagamentos - ' . $student->first_name . ' ' . $student->last_name;
        $data['student'] = $student;
        
        $data['payments'] = $this->feePaymentModel
            ->select('
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
        
        return view('admin/fees/payments/history', $data);
    }
    
    /**
     * Get fee details (AJAX)
     */
    public function getFee($id)
    {
        $fee = $this->studentFeeModel
            ->select('
                tbl_student_fees.*,
                (SELECT SUM(amount_paid) FROM tbl_fee_payments WHERE student_fee_id = tbl_student_fees.id) as total_paid
            ')
            ->where('id', $id)
            ->first();
        
        if (!$fee) {
            return $this->response->setJSON(['success' => false, 'message' => 'Propina não encontrada']);
        }
        
        $balance = $fee->total_amount - ($fee->total_paid ?? 0);
        
        return $this->response->setJSON([
            'success' => true,
            'fee' => $fee,
            'balance' => $balance
        ]);
    }
}