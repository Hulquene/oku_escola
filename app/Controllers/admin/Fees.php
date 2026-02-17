<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\FeeTypeModel;
use App\Models\FeeStructureModel;
use App\Models\StudentFeeModel;
use App\Models\FeePaymentModel;
use App\Models\GradeLevelModel;
use App\Models\AcademicYearModel;

class Fees extends BaseController
{
    protected $feeTypeModel;
    protected $feeStructureModel;
    protected $studentFeeModel;
    protected $feePaymentModel;
    protected $gradeLevelModel;
    protected $academicYearModel;
    
    public function __construct()
    {
        $this->feeTypeModel = new FeeTypeModel();
        $this->feeStructureModel = new FeeStructureModel();
        $this->studentFeeModel = new StudentFeeModel();
        $this->feePaymentModel = new FeePaymentModel();
        $this->gradeLevelModel = new GradeLevelModel();
        $this->academicYearModel = new AcademicYearModel();
    }
    
    /**
     * Fee types list
     */
    public function types()
    {
        $data['title'] = 'Tipos de Taxas';
        $data['types'] = $this->feeTypeModel->orderBy('type_category', 'ASC')
            ->orderBy('type_name', 'ASC')
            ->findAll();
        
        return view('admin/fees/types', $data);
    }
    
    /**
     * Save fee type
     */
    public function saveType()
    {
        $rules = [
            'type_name' => 'required',
            'type_code' => 'required|is_unique[tbl_fee_types.type_code,id,{id}]',
            'type_category' => 'required'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'type_name' => $this->request->getPost('type_name'),
            'type_code' => $this->request->getPost('type_code'),
            'type_category' => $this->request->getPost('type_category'),
            'is_recurring' => $this->request->getPost('is_recurring') ? 1 : 0,
            'recurrence_period' => $this->request->getPost('recurrence_period'),
            'description' => $this->request->getPost('description'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];
        
        $id = $this->request->getPost('id');
        
        if ($id) {
            $this->feeTypeModel->update($id, $data);
            $message = 'Tipo de taxa atualizado com sucesso';
        } else {
            $this->feeTypeModel->insert($data);
            $message = 'Tipo de taxa criado com sucesso';
        }
        
        return redirect()->to('/admin/fees/types')->with('success', $message);
    }
    
    /**
     * Delete fee type
     */
    public function deleteType($id)
    {
        // Check if used in fee structure
        $used = $this->feeStructureModel->where('fee_type_id', $id)->countAllResults();
        
        if ($used > 0) {
            return redirect()->back()
                ->with('error', 'Não é possível eliminar este tipo pois está associado a estruturas de taxas');
        }
        
        $this->feeTypeModel->delete($id);
        
        return redirect()->to('/admin/fees/types')->with('success', 'Tipo de taxa eliminado com sucesso');
    }
    
    /**
     * Fee structure list
     */
    public function structure()
    {
        $data['title'] = 'Estrutura de Taxas';
        
        $academicYearId = $this->request->getGet('academic_year') ?: 
            ($this->academicYearModel->getCurrent()->id ?? null);
        
        $data['structures'] = $this->feeStructureModel
            ->select('tbl_fee_structure.*, tbl_grade_levels.level_name, tbl_fee_types.type_name, tbl_currencies.currency_code')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_fee_structure.grade_level_id')
            ->join('tbl_fee_types', 'tbl_fee_types.id = tbl_fee_structure.fee_type_id')
            ->join('tbl_currencies', 'tbl_currencies.id = tbl_fee_structure.currency_id', 'left')
            ->where('tbl_fee_structure.academic_year_id', $academicYearId)
            ->orderBy('tbl_grade_levels.sort_order', 'ASC')
            ->orderBy('tbl_fee_types.type_category', 'ASC')
            ->findAll();
        
        $data['academicYears'] = $this->academicYearModel->findAll();
        $data['currentYearId'] = $academicYearId;
        
        return view('admin/fees/structure', $data);
    }
    
    /**
     * Save fee structure
     */
    public function saveStructure()
    {
        $rules = [
            'academic_year_id' => 'required|numeric',
            'grade_level_id' => 'required|numeric',
            'fee_type_id' => 'required|numeric',
            'amount' => 'required|numeric'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        $academicYearId = $this->request->getPost('academic_year_id');
        $gradeLevelId = $this->request->getPost('grade_level_id');
        $feeTypeId = $this->request->getPost('fee_type_id');
        
        // Check if already exists
        $existing = $this->feeStructureModel
            ->where('academic_year_id', $academicYearId)
            ->where('grade_level_id', $gradeLevelId)
            ->where('fee_type_id', $feeTypeId)
            ->first();
        
        if ($existing && !$this->request->getPost('id')) {
            return redirect()->back()->withInput()
                ->with('error', 'Esta combinação já existe na estrutura de taxas');
        }
        
        $data = [
            'academic_year_id' => $academicYearId,
            'grade_level_id' => $gradeLevelId,
            'fee_type_id' => $feeTypeId,
            'amount' => $this->request->getPost('amount'),
            'currency_id' => $this->request->getPost('currency_id') ?: 1,
            'due_day' => $this->request->getPost('due_day') ?: 10,
            'description' => $this->request->getPost('description'),
            'is_mandatory' => $this->request->getPost('is_mandatory') ? 1 : 0,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];
        
        $id = $this->request->getPost('id');
        
        if ($id) {
            $this->feeStructureModel->update($id, $data);
            $message = 'Estrutura de taxa atualizada com sucesso';
        } else {
            $this->feeStructureModel->insert($data);
            $message = 'Estrutura de taxa criada com sucesso';
        }
        
        return redirect()->to('/admin/fees/structure?academic_year=' . $academicYearId)
            ->with('success', $message);
    }
    
    /**
     * Delete fee structure
     */
    public function deleteStructure($id)
    {
        $structure = $this->feeStructureModel->find($id);
        
        if (!$structure) {
            return redirect()->back()->with('error', 'Estrutura não encontrada');
        }
        
        // Check if used in student fees
        $used = $this->studentFeeModel->where('fee_structure_id', $id)->countAllResults();
        
        if ($used > 0) {
            return redirect()->back()
                ->with('error', 'Não é possível eliminar esta estrutura pois já foi aplicada a alunos');
        }
        
        $this->feeStructureModel->delete($id);
        
        return redirect()->to('/admin/fees/structure')
            ->with('success', 'Estrutura de taxa eliminada com sucesso');
    }
    
    /**
     * Student fees list
     */
    public function payments()
    {
        $data['title'] = 'Pagamentos de Taxas';
        
        $status = $this->request->getGet('status');
        $classId = $this->request->getGet('class');
        
        $builder = $this->studentFeeModel
            ->select('tbl_student_fees.*, tbl_users.first_name, tbl_users.last_name, tbl_students.student_number, tbl_fee_types.type_name, tbl_classes.class_name')
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
        
        $data['fees'] = $builder->orderBy('tbl_student_fees.due_date', 'ASC')
            ->paginate(20);
        
        $data['pager'] = $this->studentFeeModel->pager;
        $data['classes'] = $this->classModel->findAll();
        
        return view('admin/fees/payments', $data);
    }
    
    /**
     * Generate fees for all students
     */
    public function generateFees()
    {
        $academicYearId = $this->request->getPost('academic_year_id') ?: 
            ($this->academicYearModel->getCurrent()->id ?? null);
        
        if (!$academicYearId) {
            return redirect()->back()->with('error', 'Ano letivo não especificado');
        }
        
        $result = $this->feeStructureModel->generateStudentFees($academicYearId);
        
        if ($result) {
            return redirect()->to('/admin/fees/payments')
                ->with('success', 'Taxas geradas com sucesso para todos os alunos matriculados');
        } else {
            return redirect()->back()
                ->with('error', 'Erro ao gerar taxas');
        }
    }
    
    /**
     * Process payment
     */
    public function processPayment()
    {
        $rules = [
            'student_fee_id' => 'required|numeric',
            'amount_paid' => 'required|numeric',
            'payment_method' => 'required'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        $studentFeeId = $this->request->getPost('student_fee_id');
        $amount = $this->request->getPost('amount_paid');
        $method = $this->request->getPost('payment_method');
        
        $paymentData = [
            'reference' => $this->request->getPost('reference'),
            'bank_name' => $this->request->getPost('bank_name'),
            'check_number' => $this->request->getPost('check_number'),
            'observations' => $this->request->getPost('observations')
        ];
        
        $result = $this->feePaymentModel->processPayment($studentFeeId, $amount, $method, $paymentData);
        
        if ($result) {
            return redirect()->back()->with('success', 'Pagamento registado com sucesso');
        } else {
            return redirect()->back()->with('error', 'Erro ao registar pagamento');
        }
    }
    
    /**
     * Payment receipt
     */
    public function receipt($id)
    {
        $data['payment'] = $this->feePaymentModel
            ->select('tbl_fee_payments.*, tbl_student_fees.reference_number, tbl_users.first_name, tbl_users.last_name, tbl_students.student_number, tbl_fee_types.type_name, tbl_academic_years.year_name')
            ->join('tbl_student_fees', 'tbl_student_fees.id = tbl_fee_payments.student_fee_id')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_student_fees.enrollment_id')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->join('tbl_fee_structure', 'tbl_fee_structure.id = tbl_student_fees.fee_structure_id')
            ->join('tbl_fee_types', 'tbl_fee_types.id = tbl_fee_structure.fee_type_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_enrollments.academic_year_id')
            ->where('tbl_fee_payments.id', $id)
            ->first();
        
        if (!$data['payment']) {
            return redirect()->back()->with('error', 'Pagamento não encontrado');
        }
        
        $data['title'] = 'Recibo de Pagamento';
        
        return view('admin/fees/receipt', $data);
    }
}