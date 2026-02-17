<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\InvoiceModel;
use App\Models\StudentModel;
use App\Models\GuardianModel;
use App\Models\FeeTypeModel;
use App\Models\SettingsModel;

class Invoices extends BaseController
{
    protected $invoiceModel;
    protected $studentModel;
    protected $guardianModel;
    protected $feeTypeModel;
    protected $settingsModel;
    
    public function __construct()
    {
        $this->invoiceModel = new InvoiceModel();
        $this->studentModel = new StudentModel();
        $this->guardianModel = new GuardianModel();
        $this->feeTypeModel = new FeeTypeModel();
        $this->settingsModel = new SettingsModel();
    }
    
  /**
   * List invoices
   */
  public function index()
  {
      $data['title'] = 'Faturas/Recibos';
      
      $status = $this->request->getGet('status');
      $studentId = $this->request->getGet('student');
      $startDate = $this->request->getGet('start_date');
      $endDate = $this->request->getGet('end_date');
      
      $builder = $this->invoiceModel
          ->select('tbl_invoices.*, 
                  CONCAT(tbl_users.first_name, " ", tbl_users.last_name) as student_name,
                  tbl_students.student_number,
                  tbl_guardians.full_name as guardian_name')
          ->join('tbl_students', 'tbl_students.id = tbl_invoices.student_id', 'left')
          ->join('tbl_users', 'tbl_users.id = tbl_students.user_id', 'left')
          ->join('tbl_guardians', 'tbl_guardians.id = tbl_invoices.guardian_id', 'left');
      
      if ($status) {
          $builder->where('tbl_invoices.status', $status);
      }
      
      if ($studentId) {
          $builder->where('tbl_invoices.student_id', $studentId);
      }
      
      if ($startDate) {
          $builder->where('tbl_invoices.invoice_date >=', $startDate);
      }
      
      if ($endDate) {
          $builder->where('tbl_invoices.invoice_date <=', $endDate);
      }
      
      $data['invoices'] = $builder->orderBy('tbl_invoices.created_at', 'DESC')
          ->paginate(15);
      
      $data['pager'] = $this->invoiceModel->pager;
      
      // Filters data - CORRIGIDO
      $data['students'] = $this->studentModel
          ->select('tbl_students.id, tbl_users.first_name, tbl_users.last_name, tbl_students.student_number')
          ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
          ->where('tbl_students.is_active', 1)
          ->orderBy('tbl_users.first_name', 'ASC')
          ->findAll(100);
      
      $data['selectedStatus'] = $status;
      $data['selectedStudent'] = $studentId;
      $data['selectedStartDate'] = $startDate;
      $data['selectedEndDate'] = $endDate;
      
      return view('admin/financial/invoices/index', $data);
  }
   /**
     * Invoice form
     */
    public function form($id = null)
    {
        $data['title'] = $id ? 'Editar Fatura' : 'Nova Fatura';
        $data['invoice'] = $id ? $this->invoiceModel->getWithItems($id) : null;
        
        // CORRIGIDO: Adicionar join com tbl_users
        $data['students'] = $this->studentModel
            ->select('tbl_students.id, tbl_users.first_name, tbl_users.last_name, tbl_students.student_number')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->where('tbl_students.is_active', 1)
            ->orderBy('tbl_users.first_name', 'ASC')
            ->findAll();
        
        $data['guardians'] = $this->guardianModel
            ->where('is_active', 1)
            ->orderBy('full_name', 'ASC')
            ->findAll();
        
        $data['feeTypes'] = $this->feeTypeModel
            ->where('is_active', 1)
            ->orderBy('type_category', 'ASC')
            ->orderBy('type_name', 'ASC')
            ->findAll();
        
        $data['nextNumber'] = $this->invoiceModel->generateInvoiceNumber();
        
        return view('admin/financial/invoices/form', $data);
    }
    
    /**
     * Save invoice
     */
    public function save()
    {
        $rules = [
            'invoice_date' => 'required|valid_date',
            'due_date' => 'required|valid_date',
            'items' => 'required'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        $items = json_decode($this->request->getPost('items'), true);
        
        if (empty($items)) {
            return redirect()->back()->withInput()
                ->with('error', 'Adicione pelo menos um item à fatura');
        }
        
        // Calculate totals
        $subtotal = 0;
        $taxAmount = 0;
        foreach ($items as $item) {
            $itemTotal = $item['quantity'] * $item['unit_price'];
            $subtotal += $itemTotal;
            if (!empty($item['tax_rate'])) {
                $taxAmount += $itemTotal * ($item['tax_rate'] / 100);
            }
        }
        
        $totalAmount = $subtotal + $taxAmount - ($this->request->getPost('discount_amount') ?: 0);
        
        $data = [
            'invoice_number' => $this->request->getPost('invoice_number') ?: $this->invoiceModel->generateInvoiceNumber(),
            'student_id' => $this->request->getPost('student_id') ?: null,
            'guardian_id' => $this->request->getPost('guardian_id') ?: null,
            'invoice_date' => $this->request->getPost('invoice_date'),
            'due_date' => $this->request->getPost('due_date'),
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'discount_amount' => $this->request->getPost('discount_amount') ?: 0,
            'total_amount' => $totalAmount,
            'status' => $this->request->getPost('status') ?: 'Rascunho',
            'notes' => $this->request->getPost('notes'),
            'created_by' => $this->session->get('user_id')
        ];
        
        $db = db_connect();
        $db->transStart();
        
        $id = $this->request->getPost('id');
        
        if ($id) {
            // Delete old items
            $db->table('tbl_invoice_items')->where('invoice_id', $id)->delete();
            $this->invoiceModel->update($id, $data);
            $invoiceId = $id;
            $message = 'Fatura atualizada com sucesso';
        } else {
            $invoiceId = $this->invoiceModel->insert($data);
            $message = 'Fatura criada com sucesso';
        }
        
        // Insert items
        foreach ($items as $item) {
            $itemTotal = $item['quantity'] * $item['unit_price'];
            $db->table('tbl_invoice_items')->insert([
                'invoice_id' => $invoiceId,
                'description' => $item['description'],
                'quantity' => $item['quantity'],
                'unit_price' => $item['unit_price'],
                'tax_rate' => $item['tax_rate'] ?? 0,
                'tax_amount' => $itemTotal * (($item['tax_rate'] ?? 0) / 100),
                'discount_rate' => $item['discount_rate'] ?? 0,
                'discount_amount' => $itemTotal * (($item['discount_rate'] ?? 0) / 100),
                'total' => $itemTotal,
                'fee_type_id' => $item['fee_type_id'] ?? null
            ]);
        }
        
        $db->transComplete();
        
        if ($db->transStatus()) {
            return redirect()->to('/admin/financial/invoices/view/' . $invoiceId)
                ->with('success', $message);
        } else {
            return redirect()->back()->withInput()
                ->with('error', 'Erro ao salvar fatura');
        }
    }
    
    /**
     * View invoice
     */
    public function viewinvoices($id)
    {
        $data['title'] = 'Detalhes da Fatura';
        $data['invoice'] = $this->invoiceModel->getWithItems($id);
        
        if (!$data['invoice']) {
            return redirect()->to('/admin/financial/invoices')->with('error', 'Fatura não encontrada');
        }
        
        // Get school info
        $data['school'] = (object)[
            'name' => $this->settingsModel->get('school_name') ?? 'Escola Angolana',
            'address' => $this->settingsModel->get('school_address') ?? 'Luanda, Angola',
            'phone' => $this->settingsModel->get('school_phone') ?? '+244 000 000 000',
            'email' => $this->settingsModel->get('school_email') ?? 'info@escola.ao',
            'nif' => $this->settingsModel->get('school_nif') ?? '000000000'
        ];
        
        return view('admin/financial/invoices/view', $data);
    }
    
    /**
     * Delete invoice
     */
    public function delete($id)
    {
        $invoice = $this->invoiceModel->find($id);
        
        if (!$invoice) {
            return redirect()->back()->with('error', 'Fatura não encontrada');
        }
        
        // Check if has payments
        $paymentModel = new \App\Models\PaymentModel();
        $payments = $paymentModel->where('invoice_id', $id)->countAllResults();
        
        if ($payments > 0) {
            return redirect()->back()
                ->with('error', 'Não é possível eliminar fatura com pagamentos associados');
        }
        
        $db = db_connect();
        $db->transStart();
        
        // Delete items
        $db->table('tbl_invoice_items')->where('invoice_id', $id)->delete();
        
        // Delete invoice
        $this->invoiceModel->delete($id);
        
        $db->transComplete();
        
        if ($db->transStatus()) {
            return redirect()->to('/admin/financial/invoices')
                ->with('success', 'Fatura eliminada com sucesso');
        } else {
            return redirect()->back()->with('error', 'Erro ao eliminar fatura');
        }
    }
    
    /**
     * Mark invoice as paid
     */
    public function markPaid($id)
    {
        $invoice = $this->invoiceModel->find($id);
        
        if (!$invoice) {
            return redirect()->back()->with('error', 'Fatura não encontrada');
        }
        
        $this->invoiceModel->update($id, ['status' => 'Paga']);
        
        return redirect()->back()->with('success', 'Fatura marcada como paga');
    }
    
    /**
     * Print invoice
     */
    public function print($id)
    {
        $data['invoice'] = $this->invoiceModel->getWithItems($id);
        
        if (!$data['invoice']) {
            return redirect()->to('/admin/financial/invoices')->with('error', 'Fatura não encontrada');
        }
        
        // Get school info
        $data['school'] = (object)[
            'name' => $this->settingsModel->get('school_name') ?? 'Escola Angolana',
            'address' => $this->settingsModel->get('school_address') ?? 'Luanda, Angola',
            'phone' => $this->settingsModel->get('school_phone') ?? '+244 000 000 000',
            'email' => $this->settingsModel->get('school_email') ?? 'info@escola.ao',
            'nif' => $this->settingsModel->get('school_nif') ?? '000000000'
        ];
        
        return view('admin/financial/invoices/print', $data);
    }
}