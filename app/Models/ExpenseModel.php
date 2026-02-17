<?php

namespace App\Models;

class ExpenseModel extends BaseModel
{
    protected $table = 'tbl_expenses';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'expense_number',
        'expense_category_id',
        'description',
        'expense_date',
        'amount',
        'supplier_name',
        'supplier_nif',
        'invoice_reference',
        'payment_method',
        'payment_status',
        'notes',
        'approved_by',
        'created_by'
    ];
    
    protected $validationRules = [
        'expense_number' => 'required|is_unique[tbl_expenses.expense_number,id,{id}]',
        'expense_category_id' => 'required|numeric',
        'description' => 'required',
        'expense_date' => 'required|valid_date',
        'amount' => 'required|numeric|greater_than[0]'
    ];
    
    protected $validationMessages = [
        'expense_number' => [
            'is_unique' => 'Este número de despesa já existe'
        ]
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    /**
     * Generate expense number
     */
    public function generateExpenseNumber()
    {
        $year = date('Y');
        $month = date('m');
        $last = $this->select('expense_number')
            ->like('expense_number', "EXP{$year}{$month}", 'after')
            ->orderBy('id', 'DESC')
            ->first();
        
        if ($last) {
            $lastNumber = intval(substr($last->expense_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return "EXP{$year}{$month}{$newNumber}";
    }
    
    /**
     * Get expenses by category
     */
    public function getByCategory($categoryId, $startDate = null, $endDate = null)
    {
        $builder = $this->where('expense_category_id', $categoryId);
        
        if ($startDate) {
            $builder->where('expense_date >=', $startDate);
        }
        
        if ($endDate) {
            $builder->where('expense_date <=', $endDate);
        }
        
        return $builder->orderBy('expense_date', 'DESC')
            ->findAll();
    }
    
    /**
     * Get expenses by status
     */
    public function getByStatus($status)
    {
        return $this->where('payment_status', $status)
            ->orderBy('expense_date', 'DESC')
            ->findAll();
    }
    
    /**
     * Get total by category
     */
    public function getTotalByCategory($year = null)
    {
        $year = $year ?: date('Y');
        
        return $this->select('
                expense_category_id,
                SUM(amount) as total
            ')
            ->where('YEAR(expense_date)', $year)
            ->groupBy('expense_category_id')
            ->findAll();
    }
    
    /**
     * Get monthly totals
     */
    public function getMonthlyTotals($year = null)
    {
        $year = $year ?: date('Y');
        
        return $this->select('
                MONTH(expense_date) as month,
                COUNT(*) as count,
                SUM(amount) as total
            ')
            ->where('YEAR(expense_date)', $year)
            ->groupBy('MONTH(expense_date)')
            ->orderBy('month', 'ASC')
            ->findAll();
    }
    
    /**
     * Get pending expenses total
     */
    public function getPendingTotal()
    {
        $result = $this->select('SUM(amount) as total')
            ->where('payment_status', 'Pendente')
            ->first();
        
        return $result->total ?? 0;
    }
    
    /**
     * Get summary statistics
     */
    public function getSummary($year = null)
    {
        $year = $year ?: date('Y');
        
        return $this->select('
                COUNT(*) as total_count,
                SUM(amount) as total_amount,
                AVG(amount) as average_amount,
                SUM(CASE WHEN payment_status = "Pago" THEN amount ELSE 0 END) as paid_amount,
                SUM(CASE WHEN payment_status = "Pendente" THEN amount ELSE 0 END) as pending_amount
            ')
            ->where('YEAR(expense_date)', $year)
            ->first();
    }
}