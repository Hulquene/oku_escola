<?php

namespace App\Models;

class ExpenseCategoryModel extends BaseModel
{
    protected $table = 'tbl_expense_categories';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'category_name',
        'category_code',
        'description',
        'is_active'
    ];
    
    protected $validationRules = [
        'category_name' => 'required',
        'category_code' => 'required|is_unique[tbl_expense_categories.category_code,id,{id}]'
    ];
    
    protected $validationMessages = [
        'category_code' => [
            'is_unique' => 'Este código de categoria já existe'
        ]
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    /**
     * Get active categories
     */
    public function getActive()
    {
        return $this->where('is_active', 1)
            ->orderBy('category_name', 'ASC')
            ->findAll();
    }
    
    /**
     * Get category with expense totals
     */
    public function getWithTotals($year = null)
    {
        $year = $year ?: date('Y');
        
        return $this->select('
                tbl_expense_categories.*,
                COUNT(tbl_expenses.id) as expense_count,
                SUM(tbl_expenses.amount) as total_amount
            ')
            ->join('tbl_expenses', 'tbl_expenses.expense_category_id = tbl_expense_categories.id', 'left')
            ->where('YEAR(tbl_expenses.expense_date)', $year)
            ->orWhere('tbl_expenses.id IS NULL')
            ->groupBy('tbl_expense_categories.id')
            ->orderBy('total_amount', 'DESC')
            ->findAll();
    }
    
    /**
     * Get category by code
     */
    public function getByCode($code)
    {
        return $this->where('category_code', $code)->first();
    }
    
    /**
     * Check if category has expenses
     */
    public function hasExpenses($id)
    {
        $expenseModel = new ExpenseModel();
        $count = $expenseModel->where('expense_category_id', $id)->countAllResults();
        
        return $count > 0;
    }
}