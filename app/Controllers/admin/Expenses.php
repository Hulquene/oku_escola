<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\ExpenseModel;
use App\Models\ExpenseCategoryModel;
use App\Models\UserModel;

class Expenses extends BaseController
{
    protected $expenseModel;
    protected $expenseCategoryModel;
    protected $userModel;
    
    public function __construct()
    {
        $this->expenseModel = new ExpenseModel();
        $this->expenseCategoryModel = new ExpenseCategoryModel();
        $this->userModel = new UserModel();
    }
    
    /**
     * List expenses
     */
    public function index()
    {
        $data['title'] = 'Despesas';
        
        $categoryId = $this->request->getGet('category');
        $status = $this->request->getGet('status');
        $startDate = $this->request->getGet('start_date');
        $endDate = $this->request->getGet('end_date');
        
        $builder = $this->expenseModel
            ->select('tbl_expenses.*, tbl_expense_categories.category_name, 
                     CONCAT(tbl_users.first_name, " ", tbl_users.last_name) as created_by_name')
            ->join('tbl_expense_categories', 'tbl_expense_categories.id = tbl_expenses.expense_category_id')
            ->join('tbl_users', 'tbl_users.id = tbl_expenses.created_by', 'left');
        
        if ($categoryId) {
            $builder->where('tbl_expenses.expense_category_id', $categoryId);
        }
        
        if ($status) {
            $builder->where('tbl_expenses.payment_status', $status);
        }
        
        if ($startDate) {
            $builder->where('tbl_expenses.expense_date >=', $startDate);
        }
        
        if ($endDate) {
            $builder->where('tbl_expenses.expense_date <=', $endDate);
        }
        
        $data['expenses'] = $builder->orderBy('tbl_expenses.expense_date', 'DESC')
            ->paginate(20);
        
        $data['pager'] = $this->expenseModel->pager;
        
        // Get categories for filter
        $data['categories'] = $this->expenseCategoryModel
            ->where('is_active', 1)
            ->orderBy('category_name', 'ASC')
            ->findAll();
        
        $data['selectedCategory'] = $categoryId;
        $data['selectedStatus'] = $status;
        $data['selectedStartDate'] = $startDate;
        $data['selectedEndDate'] = $endDate;
        
        return view('admin/financial/expenses/index', $data);
    }
    
    /**
     * Expense form
     */
    public function form($id = null)
    {
        $data['title'] = $id ? 'Editar Despesa' : 'Nova Despesa';
        $data['expense'] = $id ? $this->expenseModel->find($id) : null;
        
        $data['categories'] = $this->expenseCategoryModel
            ->where('is_active', 1)
            ->orderBy('category_name', 'ASC')
            ->findAll();
        
        $data['users'] = $this->userModel
            ->where('is_active', 1)
            ->orderBy('first_name', 'ASC')
            ->findAll();
        
        $data['nextNumber'] = $this->expenseModel->generateExpenseNumber();
        
        return view('admin/financial/expenses/form', $data);
    }
    
    /**
     * Save expense
     */
    public function save()
    {
        $rules = [
            'expense_category_id' => 'required|numeric',
            'description' => 'required',
            'expense_date' => 'required|valid_date',
            'amount' => 'required|numeric|greater_than[0]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'expense_number' => $this->request->getPost('expense_number') ?: $this->expenseModel->generateExpenseNumber(),
            'expense_category_id' => $this->request->getPost('expense_category_id'),
            'description' => $this->request->getPost('description'),
            'expense_date' => $this->request->getPost('expense_date'),
            'amount' => $this->request->getPost('amount'),
            'supplier_name' => $this->request->getPost('supplier_name'),
            'supplier_nif' => $this->request->getPost('supplier_nif'),
            'invoice_reference' => $this->request->getPost('invoice_reference'),
            'payment_method' => $this->request->getPost('payment_method'),
            'payment_status' => $this->request->getPost('payment_status') ?: 'Pendente',
            'notes' => $this->request->getPost('notes'),
            'approved_by' => $this->request->getPost('approved_by') ?: null,
            'created_by' => $this->session->get('user_id')
        ];
        
        $id = $this->request->getPost('id');
        
        if ($id) {
            $this->expenseModel->update($id, $data);
            $message = 'Despesa atualizada com sucesso';
        } else {
            $this->expenseModel->insert($data);
            $message = 'Despesa criada com sucesso';
        }
        
        return redirect()->to('/admin/financial/expenses')->with('success', $message);
    }
    
    /**
     * Delete expense
     */
    public function delete($id)
    {
        $expense = $this->expenseModel->find($id);
        
        if (!$expense) {
            return redirect()->back()->with('error', 'Despesa não encontrada');
        }
        
        $this->expenseModel->delete($id);
        
        return redirect()->to('/admin/financial/expenses')->with('success', 'Despesa eliminada com sucesso');
    }
    
    /**
     * Mark as paid
     */
    public function markPaid($id)
    {
        $expense = $this->expenseModel->find($id);
        
        if (!$expense) {
            return redirect()->back()->with('error', 'Despesa não encontrada');
        }
        
        $this->expenseModel->update($id, ['payment_status' => 'Pago']);
        
        return redirect()->back()->with('success', 'Despesa marcada como paga');
    }
    
    /**
     * Get expense summary
     */
    public function summary()
    {
        $data['title'] = 'Resumo de Despesas';
        
        $year = $this->request->getGet('year') ?: date('Y');
        $month = $this->request->getGet('month');
        
        $data['years'] = $this->expenseModel
            ->select('YEAR(expense_date) as year')
            ->distinct()
            ->orderBy('year', 'DESC')
            ->findAll();
        
        $data['selectedYear'] = $year;
        $data['selectedMonth'] = $month;
        
        // Summary by category
        $builder = $this->expenseModel
            ->select('tbl_expense_categories.category_name, 
                     COUNT(*) as total_count,
                     SUM(tbl_expenses.amount) as total_amount')
            ->join('tbl_expense_categories', 'tbl_expense_categories.id = tbl_expenses.expense_category_id')
            ->where('YEAR(tbl_expenses.expense_date)', $year);
        
        if ($month) {
            $builder->where('MONTH(tbl_expenses.expense_date)', $month);
        }
        
        $data['categorySummary'] = $builder->groupBy('tbl_expenses.expense_category_id')
            ->orderBy('total_amount', 'DESC')
            ->findAll();
        
        // Monthly summary
        $monthlyData = $this->expenseModel
            ->select('MONTH(expense_date) as month, 
                     SUM(amount) as total')
            ->where('YEAR(expense_date)', $year)
            ->groupBy('MONTH(expense_date)')
            ->orderBy('month', 'ASC')
            ->findAll();
        
        $months = [];
        $totals = [];
        for ($m = 1; $m <= 12; $m++) {
            $months[] = date('F', mktime(0, 0, 0, $m, 1));
            $found = false;
            foreach ($monthlyData as $data) {
                if ($data->month == $m) {
                    $totals[] = $data->total;
                    $found = true;
                    break;
                }
            }
            if (!$found) {
                $totals[] = 0;
            }
        }
        
        $data['chartMonths'] = $months;
        $data['chartTotals'] = $totals;
        
        return view('admin/financial/expenses/summary', $data);
    }
}