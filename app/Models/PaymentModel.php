<?php

namespace App\Models;

class PaymentModel extends BaseModel
{
    protected $table = 'tbl_payments';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'payment_number',
        'invoice_id',
        'payment_date',
        'amount',
        'payment_method',
        'payment_reference',
        'receipt_number',
        'notes',
        'received_by'
    ];
    
    protected $validationRules = [
        'payment_number' => 'required|is_unique[tbl_payments.payment_number,id,{id}]',
        'invoice_id' => 'required|numeric',
        'payment_date' => 'required|valid_date',
        'amount' => 'required|numeric|greater_than[0]',
        'payment_method' => 'required'
    ];
    
    protected $validationMessages = [
        'payment_number' => [
            'is_unique' => 'Este número de pagamento já existe'
        ]
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
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
     * Generate receipt number
     */
    public function generateReceiptNumber($paymentId)
    {
        return 'REC' . date('Ymd') . str_pad($paymentId, 4, '0', STR_PAD_LEFT);
    }
    
    /**
     * Get payments by invoice
     */
    public function getByInvoice($invoiceId)
    {
        return $this->where('invoice_id', $invoiceId)
            ->orderBy('payment_date', 'DESC')
            ->findAll();
    }
    
    /**
     * Get payments by date range
     */
    public function getByDateRange($startDate, $endDate)
    {
        return $this->where('payment_date >=', $startDate)
            ->where('payment_date <=', $endDate)
            ->orderBy('payment_date', 'DESC')
            ->findAll();
    }
    
    /**
     * Get total payments by date
     */
    public function getTotalByDate($date)
    {
        $result = $this->select('SUM(amount) as total')
            ->where('payment_date', $date)
            ->first();
        
        return $result->total ?? 0;
    }
    
    /**
     * Get payment methods summary
     */
    public function getMethodsSummary($startDate = null, $endDate = null)
    {
        $builder = $this->select('
                payment_method,
                COUNT(*) as count,
                SUM(amount) as total
            ')
            ->groupBy('payment_method');
        
        if ($startDate) {
            $builder->where('payment_date >=', $startDate);
        }
        
        if ($endDate) {
            $builder->where('payment_date <=', $endDate);
        }
        
        return $builder->findAll();
    }
    
    /**
     * Get daily summary
     */
    public function getDailySummary($date)
    {
        return $this->select('
                COUNT(*) as total_transactions,
                SUM(amount) as total_amount,
                AVG(amount) as average_amount
            ')
            ->where('payment_date', $date)
            ->first();
    }
}