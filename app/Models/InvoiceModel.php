<?php

namespace App\Models;

class InvoiceModel extends BaseModel
{
    protected $table = 'tbl_invoices';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'invoice_number',
        'student_id',
        'guardian_id',
        'invoice_date',
        'due_date',
        'subtotal',
        'tax_amount',
        'discount_amount',
        'total_amount',
        'status',
        'notes',
        'created_by'
    ];
    
    protected $validationRules = [
        'invoice_number' => 'required|is_unique[tbl_invoices.invoice_number,id,{id}]',
        'invoice_date' => 'required|valid_date',
        'due_date' => 'required|valid_date',
        'subtotal' => 'required|numeric',
        'total_amount' => 'required|numeric'
    ];
    
    protected $validationMessages = [
        'invoice_number' => [
            'is_unique' => 'Este número de fatura já existe'
        ]
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    /**
     * Generate invoice number
     */
    public function generateInvoiceNumber()
    {
        $year = date('Y');
        $month = date('m');
        $last = $this->select('invoice_number')
            ->like('invoice_number', "INV{$year}{$month}", 'after')
            ->orderBy('id', 'DESC')
            ->first();
        
        if ($last) {
            $lastNumber = intval(substr($last->invoice_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return "INV{$year}{$month}{$newNumber}";
    }
    
    /**
     * Get invoice with items
     */
    public function getWithItems($id)
    {
        $invoice = $this->select('tbl_invoices.*, 
                                 CONCAT(tbl_students.first_name, " ", tbl_students.last_name) as student_name,
                                 tbl_students.student_number,
                                 tbl_guardians.full_name as guardian_name')
            ->join('tbl_students', 'tbl_students.id = tbl_invoices.student_id', 'left')
            ->join('tbl_guardians', 'tbl_guardians.id = tbl_invoices.guardian_id', 'left')
            ->where('tbl_invoices.id', $id)
            ->first();
        
        if ($invoice) {
            $invoiceItemModel = new InvoiceItemModel();
            $invoice->items = $invoiceItemModel->where('invoice_id', $id)->findAll();
        }
        
        return $invoice;
    }
    
    /**
     * Get invoices by student
     */
    public function getByStudent($studentId)
    {
        return $this->where('student_id', $studentId)
            ->orderBy('invoice_date', 'DESC')
            ->findAll();
    }
    
    /**
     * Get invoices by status
     */
    public function getByStatus($status)
    {
        return $this->where('status', $status)
            ->orderBy('due_date', 'ASC')
            ->findAll();
    }
    
    /**
     * Get overdue invoices
     */
    public function getOverdue()
    {
        return $this->where('due_date <', date('Y-m-d'))
            ->whereIn('status', ['Emitida', 'Parcial'])
            ->orderBy('due_date', 'ASC')
            ->findAll();
    }
    
    /**
     * Get invoice statistics
     */
    public function getStatistics($year = null)
    {
        $year = $year ?: date('Y');
        
        return $this->select('
                COUNT(*) as total_invoices,
                SUM(CASE WHEN status = "Paga" THEN total_amount ELSE 0 END) as total_paid,
                SUM(CASE WHEN status IN ("Emitida", "Parcial") THEN total_amount ELSE 0 END) as total_pending,
                SUM(CASE WHEN status = "Vencida" THEN total_amount ELSE 0 END) as total_overdue,
                AVG(total_amount) as average_amount
            ')
            ->where('YEAR(invoice_date)', $year)
            ->first();
    }
    
    /**
     * Get monthly totals
     */
    public function getMonthlyTotals($year = null)
    {
        $year = $year ?: date('Y');
        
        return $this->select('
                MONTH(invoice_date) as month,
                COUNT(*) as count,
                SUM(total_amount) as total
            ')
            ->where('YEAR(invoice_date)', $year)
            ->groupBy('MONTH(invoice_date)')
            ->orderBy('month', 'ASC')
            ->findAll();
    }
}