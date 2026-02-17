<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\FeeStructureModel;
use App\Models\FeeTypeModel;
use App\Models\GradeLevelModel;
use App\Models\AcademicYearModel;
use App\Models\CurrencyModel;

class TuitionFees extends BaseController
{
    protected $feeStructureModel;
    protected $feeTypeModel;
    protected $gradeLevelModel;
    protected $academicYearModel;
    protected $currencyModel;
    
    public function __construct()
    {
        $this->feeStructureModel = new FeeStructureModel();
        $this->feeTypeModel = new FeeTypeModel();
        $this->gradeLevelModel = new GradeLevelModel();
        $this->academicYearModel = new AcademicYearModel();
        $this->currencyModel = new CurrencyModel();
    }
    
    /**
     * List fee structure
     */
    public function index()
    {
        $data['title'] = 'Estrutura de Propinas';
        
        $academicYearId = $this->request->getGet('academic_year') ?: 
            ($this->academicYearModel->getCurrent()->id ?? null);
        
        $data['structures'] = $this->feeStructureModel
            ->select('tbl_fee_structure.*, tbl_grade_levels.level_name, tbl_fee_types.type_name, tbl_fee_types.type_category, tbl_currencies.currency_code, tbl_currencies.currency_symbol')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_fee_structure.grade_level_id')
            ->join('tbl_fee_types', 'tbl_fee_types.id = tbl_fee_structure.fee_type_id')
            ->join('tbl_currencies', 'tbl_currencies.id = tbl_fee_structure.currency_id', 'left')
            ->where('tbl_fee_structure.academic_year_id', $academicYearId)
            ->orderBy('tbl_grade_levels.sort_order', 'ASC')
            ->orderBy('tbl_fee_types.type_category', 'ASC')
            ->orderBy('tbl_fee_types.type_name', 'ASC')
            ->findAll();
        
        $data['academicYears'] = $this->academicYearModel->where('is_active', 1)->findAll();
        $data['gradeLevels'] = $this->gradeLevelModel->getActive();
        $data['feeTypes'] = $this->feeTypeModel->where('is_active', 1)->findAll();
        $data['currencies'] = $this->currencyModel->findAll();
        
        $data['currentYearId'] = $academicYearId;
        
        return view('admin/fees/structure/index', $data);
    }
    
    /**
     * Save fee structure
     */
    public function save()
    {
        $rules = [
            'academic_year_id' => 'required|numeric',
            'grade_level_id' => 'required|numeric',
            'fee_type_id' => 'required|numeric',
            'amount' => 'required|numeric|greater_than[0]'
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
            $message = 'Estrutura de propina atualizada com sucesso';
        } else {
            $this->feeStructureModel->insert($data);
            $message = 'Estrutura de propina criada com sucesso';
        }
        
        return redirect()->to('/admin/fees/structure?academic_year=' . $academicYearId)
            ->with('success', $message);
    }
    
    /**
     * Get fee structure by class (AJAX)
     */
    public function getByClass($classId)
    {
        $classModel = new \App\Models\ClassModel();
        $class = $classModel->find($classId);
        
        if (!$class) {
            return $this->response->setJSON([]);
        }
        
        $academicYearId = $class->academic_year_id;
        
        $structures = $this->feeStructureModel
            ->select('tbl_fee_structure.*, tbl_fee_types.type_name, tbl_fee_types.type_category')
            ->join('tbl_fee_types', 'tbl_fee_types.id = tbl_fee_structure.fee_type_id')
            ->where('tbl_fee_structure.academic_year_id', $academicYearId)
            ->where('tbl_fee_structure.grade_level_id', $class->grade_level_id)
            ->where('tbl_fee_structure.is_active', 1)
            ->orderBy('tbl_fee_types.type_category', 'ASC')
            ->orderBy('tbl_fee_types.type_name', 'ASC')
            ->findAll();
        
        return $this->response->setJSON($structures);
    }
    
    /**
     * Delete fee structure
     */
    public function delete($id)
    {
        $structure = $this->feeStructureModel->find($id);
        
        if (!$structure) {
            return redirect()->back()->with('error', 'Estrutura não encontrada');
        }
        
        // Check if used in student fees
        $studentFeeModel = new \App\Models\StudentFeeModel();
        $used = $studentFeeModel->where('fee_structure_id', $id)->countAllResults();
        
        if ($used > 0) {
            return redirect()->back()
                ->with('error', 'Não é possível eliminar esta estrutura pois já foi aplicada a alunos');
        }
        
        $this->feeStructureModel->delete($id);
        
        return redirect()->to('/admin/fees/structure?academic_year=' . $structure->academic_year_id)
            ->with('success', 'Estrutura de propina eliminada com sucesso');
    }
    
    /**
     * Bulk create fee structures
     */
    public function bulkCreate()
    {
        $academicYearId = $this->request->getPost('academic_year_id');
        $gradeLevelIds = $this->request->getPost('grade_level_ids') ?? [];
        $feeTypeIds = $this->request->getPost('fee_type_ids') ?? [];
        $amount = $this->request->getPost('amount');
        
        if (empty($gradeLevelIds) || empty($feeTypeIds) || !$amount) {
            return redirect()->back()->with('error', 'Selecione os níveis e tipos de taxa');
        }
        
        $count = 0;
        foreach ($gradeLevelIds as $gradeLevelId) {
            foreach ($feeTypeIds as $feeTypeId) {
                // Check if exists
                $existing = $this->feeStructureModel
                    ->where('academic_year_id', $academicYearId)
                    ->where('grade_level_id', $gradeLevelId)
                    ->where('fee_type_id', $feeTypeId)
                    ->first();
                
                if (!$existing) {
                    $this->feeStructureModel->insert([
                        'academic_year_id' => $academicYearId,
                        'grade_level_id' => $gradeLevelId,
                        'fee_type_id' => $feeTypeId,
                        'amount' => $amount,
                        'currency_id' => 1,
                        'due_day' => 10,
                        'is_mandatory' => 1,
                        'is_active' => 1
                    ]);
                    $count++;
                }
            }
        }
        
        return redirect()->to('/admin/fees/structure?academic_year=' . $academicYearId)
            ->with('success', "$count estruturas de propina criadas com sucesso");
    }
}