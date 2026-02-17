<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\PaymentModel;
use App\Models\InvoiceModel;
use App\Models\PaymentModeModel;
use App\Models\StudentModel;

class Payments extends BaseController
{
    protected $paymentModel;
    protected $invoiceModel;
    protected $paymentModeModel;
    protected $studentModel;
    
    public function __construct()
    {
        $this->paymentModel = new PaymentModel();
        $this->invoiceModel = new InvoiceModel();
        $this->paymentModeModel = new PaymentModeModel();
        $this->studentModel = new StudentModel();
    }
    
    /**
     * List payments - CORRIGIDO
     */
    public function payments()
    {
        $data['title'] = 'Pagamentos';
        
        $paymentMethod = $this->request->getGet('method');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        
        $builder = $this->paymentModel
            ->select('tbl_payments.*, 
                     tbl_invoices.invoice_number,
                     CONCAT(tbl_users.first_name, " ", tbl_users.last_name) as student_name,
                     tbl_students.student_number')
            ->join('tbl_invoices', 'tbl_invoices.id = tbl_payments.invoice_id')
            ->join('tbl_students', 'tbl_students.id = tbl_invoices.student_id', 'left')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id', 'left');
        
        if ($paymentMethod) {
            $builder->where('tbl_payments.payment_method', $paymentMethod);
        }
        
        if ($startDate) {
            $builder->where('tbl_payments.payment_date >=', $startDate);
        }
        
        if ($endDate) {
            $builder->where('tbl_payments.payment_date <=', $endDate);
        }
        
        $data['payments'] = $builder->orderBy('tbl_payments.payment_date', 'DESC')
            ->paginate(20);
        
        $data['pager'] = $this->paymentModel->pager;
        
        // Get payment methods for filter
        $data['paymentMethods'] = $this->paymentModeModel
            ->where('is_active', 1)
            ->orderBy('mode_name', 'ASC')
            ->findAll();
        
        $data['selectedMethod'] = $paymentMethod;
        $data['selectedStartDate'] = $startDate;
        $data['selectedEndDate'] = $endDate;
        
        return view('admin/financial/payments/index', $data);
    }
    
    /**
     * Payment modes list
     */
    public function paymentsmode()
    {
        $data['title'] = 'Métodos de Pagamento';
        $data['modes'] = $this->paymentModeModel
            ->orderBy('mode_name', 'ASC')
            ->findAll();
        
        return view('admin/financial/payments/modes', $data);
    }
    
    /**
     * Save payment mode
     */
    public function save_paymentsmode()
    {
        $rules = [
            'mode_name' => 'required',
            'mode_code' => 'required|is_unique[tbl_payment_modes.mode_code,id,{id}]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'mode_name' => $this->request->getPost('mode_name'),
            'mode_code' => $this->request->getPost('mode_code'),
            'description' => $this->request->getPost('description'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];
        
        $id = $this->request->getPost('id');
        
        if ($id) {
            $this->paymentModeModel->update($id, $data);
            $message = 'Método de pagamento atualizado com sucesso';
        } else {
            $this->paymentModeModel->insert($data);
            $message = 'Método de pagamento criado com sucesso';
        }
        
        return redirect()->to('/admin/financial/payments/modes')->with('success', $message);
    }
    
    /**
     * Receipts list - CORRIGIDO
     */
    public function receipts()
    {
        $data['title'] = 'Recibos';
        
        $paymentId = $this->request->getGet('payment');
        $studentId = $this->request->getGet('student');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        
        $builder = $this->paymentModel
            ->select('tbl_payments.*, 
                     tbl_invoices.invoice_number,
                     CONCAT(tbl_users.first_name, " ", tbl_users.last_name) as student_name,
                     tbl_students.student_number,
                     tbl_users.first_name as received_by_name,
                     tbl_users.last_name as received_by_lastname')
            ->join('tbl_invoices', 'tbl_invoices.id = tbl_payments.invoice_id')
            ->join('tbl_students', 'tbl_students.id = tbl_invoices.student_id', 'left')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id', 'left')
            ->join('tbl_users as received_by', 'received_by.id = tbl_payments.received_by', 'left')
            ->where('tbl_payments.receipt_number IS NOT NULL');
        
        if ($paymentId) {
            $builder->where('tbl_payments.id', $paymentId);
        }
        
        if ($studentId) {
            $builder->where('tbl_invoices.student_id', $studentId);
        }
        
        if ($startDate) {
            $builder->where('tbl_payments.payment_date >=', $startDate);
        }
        
        if ($endDate) {
            $builder->where('tbl_payments.payment_date <=', $endDate);
        }
        
        $data['receipts'] = $builder->orderBy('tbl_payments.payment_date', 'DESC')
            ->paginate(20);
        
        $data['pager'] = $this->paymentModel->pager;
        
        // Filters data
        $data['students'] = $this->studentModel
            ->select('tbl_students.id, tbl_users.first_name, tbl_users.last_name, tbl_students.student_number')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->where('tbl_students.is_active', 1)
            ->orderBy('tbl_users.first_name', 'ASC')
            ->findAll(100);
        
        $data['selectedStudent'] = $studentId;
        $data['selectedStartDate'] = $startDate;
        $data['selectedEndDate'] = $endDate;
        
        return view('admin/financial/payments/receipts', $data);
    }
    
    /**
     * Generate receipt for payment - CORRIGIDO
     */
    public function generateReceipt($id)
    {
        $payment = $this->paymentModel
            ->select('tbl_payments.*, 
                     tbl_invoices.invoice_number,
                     CONCAT(tbl_users.first_name, " ", tbl_users.last_name) as student_name,
                     tbl_students.student_number,
                     tbl_invoices.total_amount')
            ->join('tbl_invoices', 'tbl_invoices.id = tbl_payments.invoice_id')
            ->join('tbl_students', 'tbl_students.id = tbl_invoices.student_id', 'left')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id', 'left')
            ->where('tbl_payments.id', $id)
            ->first();
        
        if (!$payment) {
            return redirect()->back()->with('error', 'Pagamento não encontrado');
        }
        
        // Generate receipt number if not exists
        if (!$payment->receipt_number) {
            $receiptNumber = 'REC' . date('Ymd') . str_pad($id, 4, '0', STR_PAD_LEFT);
            $this->paymentModel->update($id, ['receipt_number' => $receiptNumber]);
            $payment->receipt_number = $receiptNumber;
        }
        
        return redirect()->to('/admin/financial/payments/receipt/' . $id);
    }
    
    /**
     * View receipt - CORRIGIDO
     */
    public function receipt($id)
    {
        $data['payment'] = $this->paymentModel
            ->select('tbl_payments.*, 
                     tbl_invoices.invoice_number,
                     tbl_invoices.invoice_date,
                     tbl_invoices.due_date,
                     tbl_invoices.subtotal,
                     tbl_invoices.tax_amount,
                     tbl_invoices.discount_amount,
                     tbl_invoices.total_amount,
                     tbl_invoices.notes as invoice_notes,
                     CONCAT(tbl_users.first_name, " ", tbl_users.last_name) as student_name,
                     tbl_students.student_number,
                     tbl_students.address,
                     tbl_students.phone,
                     tbl_students.email,
                     received_by.first_name as received_by_name,
                     received_by.last_name as received_by_lastname')
            ->join('tbl_invoices', 'tbl_invoices.id = tbl_payments.invoice_id')
            ->join('tbl_students', 'tbl_students.id = tbl_invoices.student_id', 'left')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id', 'left')
            ->join('tbl_users as received_by', 'received_by.id = tbl_payments.received_by', 'left')
            ->where('tbl_payments.id', $id)
            ->first();
        
        if (!$data['payment']) {
            return redirect()->to('/admin/financial/payments/receipts')
                ->with('error', 'Recibo não encontrado');
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
        
        return view('admin/financial/payments/receipt', $data);
    }
}