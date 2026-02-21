<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\GradeWeightModel;
use App\Models\AcademicYearModel;
use App\Models\GradeLevelModel;
use App\Models\ExamBoardModel;

class GradeWeights extends BaseController
{
    protected $gradeWeightModel;
    protected $academicYearModel;
    protected $gradeLevelModel;
    protected $examBoardModel;

    public function __construct()
    {
        $this->gradeWeightModel = new GradeWeightModel();
        $this->academicYearModel = new AcademicYearModel();
        $this->gradeLevelModel = new GradeLevelModel();
        $this->examBoardModel = new ExamBoardModel();
    }

    /**
     * List all grade weights
     */
    public function index()
    {
        $data['title'] = 'Pesos das Avaliações';
        
        // Get filters
        $academicYearId = $this->request->getGet('academic_year');
        $gradeLevelId = $this->request->getGet('grade_level');
        
        // If no academic year selected, get current
        if (!$academicYearId) {
            $currentYear = $this->academicYearModel->getCurrent();
            $academicYearId = $currentYear->id ?? null;
        }
        
        // Build query
        $builder = $this->gradeWeightModel
            ->select('
                tbl_grade_weights.*,
                tbl_academic_years.year_name,
                tbl_grade_levels.level_name,
                tbl_grade_levels.education_level,
                tbl_exam_boards.board_name,
                tbl_exam_boards.board_code,
                tbl_exam_boards.board_type
            ')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_grade_weights.academic_year_id')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_grade_weights.grade_level_id')
            ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_grade_weights.exam_board_id');
        
        if ($academicYearId) {
            $builder->where('tbl_grade_weights.academic_year_id', $academicYearId);
        }
        
        if ($gradeLevelId) {
            $builder->where('tbl_grade_weights.grade_level_id', $gradeLevelId);
        }
        
        $data['weights'] = $builder->orderBy('tbl_grade_levels.sort_order', 'ASC')
            ->orderBy('tbl_exam_boards.id', 'ASC')
            ->findAll();
        
        // Group weights by grade level for display
        $groupedWeights = [];
        foreach ($data['weights'] as $weight) {
            $key = $weight->grade_level_id . '_' . $weight->level_name;
            if (!isset($groupedWeights[$key])) {
                $groupedWeights[$key] = [
                    'grade_level_id' => $weight->grade_level_id,
                    'level_name' => $weight->level_name,
                    'education_level' => $weight->education_level,
                    'weights' => []
                ];
            }
            $groupedWeights[$key]['weights'][] = $weight;
        }
        $data['groupedWeights'] = $groupedWeights;
        
        // Get filter data
        $data['academicYears'] = $this->academicYearModel->where('is_active', 1)->findAll();
        $data['gradeLevels'] = $this->gradeLevelModel->where('is_active', 1)->orderBy('sort_order', 'ASC')->findAll();
        $data['selectedYear'] = $academicYearId;
        $data['selectedLevel'] = $gradeLevelId;
        
        // Calculate totals
        $data['totals'] = [];
        foreach ($groupedWeights as $key => $group) {
            $total = 0;
            foreach ($group['weights'] as $weight) {
                $total += $weight->weight_percentage;
            }
            $data['totals'][$key] = $total;
        }
        
        return view('admin/exams/weights/index', $data);
    }

    /**
     * Create new grade weight
     */
    public function create()
    {
        $data['title'] = 'Adicionar Peso de Avaliação';
        
        if ($this->request->getMethod() === 'post') {
            return $this->save();
        }
        
        $data['academicYears'] = $this->academicYearModel->where('is_active', 1)->findAll();
        $data['gradeLevels'] = $this->gradeLevelModel->where('is_active', 1)->orderBy('sort_order', 'ASC')->findAll();
        $data['examBoards'] = $this->examBoardModel->where('is_active', 1)->orderBy('id', 'ASC')->findAll();
        
        return view('admin/exams/weights/form', $data);
    }

    /**
     * Edit grade weight
     */
    public function edit($id)
    {
        $data['weight'] = $this->gradeWeightModel->find($id);
        
        if (!$data['weight']) {
            return redirect()->to('/admin/exams/weights')
                ->with('error', 'Peso de avaliação não encontrado.');
        }
        
        $data['title'] = 'Editar Peso de Avaliação';
        
        if ($this->request->getMethod() === 'post') {
            return $this->save($id);
        }
        
        $data['academicYears'] = $this->academicYearModel->where('is_active', 1)->findAll();
        $data['gradeLevels'] = $this->gradeLevelModel->where('is_active', 1)->orderBy('sort_order', 'ASC')->findAll();
        $data['examBoards'] = $this->examBoardModel->where('is_active', 1)->orderBy('id', 'ASC')->findAll();
        
        return view('admin/exams/weights/form', $data);
    }

    /**
     * Save grade weight
     */
    public function save($id = null)
    {
        $rules = [
            'academic_year_id' => 'required|numeric',
            'grade_level_id' => 'required|numeric',
            'exam_board_id' => 'required|numeric',
            'weight_percentage' => 'required|numeric|greater_than[0]|less_than[100]',
            'is_mandatory' => 'permit_empty|in_list[0,1]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }
        
        $data = [
            'academic_year_id' => $this->request->getPost('academic_year_id'),
            'grade_level_id' => $this->request->getPost('grade_level_id'),
            'exam_board_id' => $this->request->getPost('exam_board_id'),
            'weight_percentage' => $this->request->getPost('weight_percentage'),
            'is_mandatory' => $this->request->getPost('is_mandatory') ? 1 : 0
        ];
        
        // Check for duplicate
        $existing = $this->gradeWeightModel
            ->where('academic_year_id', $data['academic_year_id'])
            ->where('grade_level_id', $data['grade_level_id'])
            ->where('exam_board_id', $data['exam_board_id']);
        
        if ($id) {
            $existing->where('id !=', $id);
        }
        
        if ($existing->first()) {
            return redirect()->back()
                ->with('error', 'Já existe um peso configurado para esta combinação.')
                ->withInput();
        }
        
        // Check total weight for this grade level
        $currentTotal = $this->getTotalWeightForLevel(
            $data['grade_level_id'], 
            $data['academic_year_id'], 
            $id
        );
        
        $newTotal = $currentTotal + $data['weight_percentage'];
        
        if ($newTotal > 100) {
            return redirect()->back()
                ->with('error', 'O total de pesos para este nível não pode ultrapassar 100%. Total atual: ' . $currentTotal . '%')
                ->withInput();
        }
        
        if ($id) {
            if ($this->gradeWeightModel->update($id, $data)) {
                return redirect()->to('/admin/exams/weights')
                    ->with('success', 'Peso de avaliação atualizado com sucesso!');
            }
        } else {
            if ($this->gradeWeightModel->insert($data)) {
                return redirect()->to('/admin/exams/weights')
                    ->with('success', 'Peso de avaliação adicionado com sucesso!');
            }
        }
        
        return redirect()->back()
            ->with('error', 'Erro ao salvar peso de avaliação.')
            ->withInput();
    }

    /**
     * Delete grade weight
     */
    public function delete($id)
    {
        if ($this->gradeWeightModel->delete($id)) {
            return redirect()->to('/admin/exams/weights')
                ->with('success', 'Peso de avaliação removido com sucesso!');
        }
        
        return redirect()->to('/admin/exams/weights')
            ->with('error', 'Erro ao remover peso de avaliação.');
    }

    /**
     * Get weights by grade level (AJAX)
     */
    public function getByLevel($gradeLevelId)
    {
        $academicYearId = $this->request->getGet('academic_year');
        
        if (!$academicYearId) {
            $currentYear = $this->academicYearModel->getCurrent();
            $academicYearId = $currentYear->id ?? null;
        }
        
        $weights = $this->gradeWeightModel->getByGradeLevel($gradeLevelId, $academicYearId);
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $weights,
            'total' => array_sum(array_column($weights, 'weight_percentage'))
        ]);
    }

    /**
     * Copy weights from previous academic year
     */
    public function copyFromPrevious()
    {
        if ($this->request->getMethod() !== 'post') {
            return redirect()->to('/admin/exams/weights')
                ->with('error', 'Método não permitido.');
        }
        
        $targetYearId = $this->request->getPost('target_academic_year_id');
        $sourceYearId = $this->request->getPost('source_academic_year_id');
        $gradeLevelIds = $this->request->getPost('grade_level_ids') ?: [];
        
        if (!$targetYearId || !$sourceYearId) {
            return redirect()->back()
                ->with('error', 'Ano letivo de origem e destino são obrigatórios.');
        }
        
        // Get source weights
        $sourceWeights = $this->gradeWeightModel
            ->where('academic_year_id', $sourceYearId);
        
        if (!empty($gradeLevelIds)) {
            $sourceWeights->whereIn('grade_level_id', $gradeLevelIds);
        }
        
        $sourceWeights = $sourceWeights->findAll();
        
        if (empty($sourceWeights)) {
            return redirect()->back()
                ->with('error', 'Nenhum peso encontrado para copiar.');
        }
        
        // Prepare new weights
        $newWeights = [];
        foreach ($sourceWeights as $weight) {
            // Check if already exists in target
            $exists = $this->gradeWeightModel
                ->where('academic_year_id', $targetYearId)
                ->where('grade_level_id', $weight->grade_level_id)
                ->where('exam_board_id', $weight->exam_board_id)
                ->first();
            
            if (!$exists) {
                $newWeights[] = [
                    'academic_year_id' => $targetYearId,
                    'grade_level_id' => $weight->grade_level_id,
                    'exam_board_id' => $weight->exam_board_id,
                    'weight_percentage' => $weight->weight_percentage,
                    'is_mandatory' => $weight->is_mandatory
                ];
            }
        }
        
        if (empty($newWeights)) {
            return redirect()->back()
                ->with('info', 'Todos os pesos já existem no ano letivo de destino.');
        }
        
        if ($this->gradeWeightModel->insertBatch($newWeights)) {
            return redirect()->to('/admin/exams/weights?academic_year=' . $targetYearId)
                ->with('success', count($newWeights) . ' pesos copiados com sucesso!');
        }
        
        return redirect()->back()
            ->with('error', 'Erro ao copiar pesos.');
    }

    /**
     * Get total weight for a grade level (excluding current if editing)
     */
    private function getTotalWeightForLevel($gradeLevelId, $academicYearId, $excludeId = null)
    {
        $builder = $this->gradeWeightModel
            ->where('grade_level_id', $gradeLevelId)
            ->where('academic_year_id', $academicYearId);
        
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        
        $weights = $builder->findAll();
        
        return array_sum(array_column($weights, 'weight_percentage'));
    }

    /**
     * Quick configure default weights
     */
    public function quickConfigure()
    {
        if ($this->request->getMethod() !== 'post') {
            return redirect()->to('/admin/exams/weights');
        }
        
        $academicYearId = $this->request->getPost('academic_year_id');
        $gradeLevelIds = $this->request->getPost('grade_level_ids') ?: [];
        
        if (!$academicYearId || empty($gradeLevelIds)) {
            return redirect()->back()
                ->with('error', 'Selecione o ano letivo e pelo menos um nível.');
        }
        
        $defaultWeights = $this->gradeWeightModel->getDefaultWeights();
        $boards = $this->examBoardModel->whereIn('board_code', array_column($defaultWeights, 'board_code'))->findAll();
        
        $boardMap = [];
        foreach ($boards as $board) {
            $boardMap[$board->board_code] = $board->id;
        }
        
        $inserted = 0;
        foreach ($gradeLevelIds as $gradeLevelId) {
            foreach ($defaultWeights as $default) {
                if (!isset($boardMap[$default['board_code']])) {
                    continue;
                }
                
                // Check if exists
                $exists = $this->gradeWeightModel
                    ->where('academic_year_id', $academicYearId)
                    ->where('grade_level_id', $gradeLevelId)
                    ->where('exam_board_id', $boardMap[$default['board_code']])
                    ->first();
                
                if (!$exists) {
                    $this->gradeWeightModel->insert([
                        'academic_year_id' => $academicYearId,
                        'grade_level_id' => $gradeLevelId,
                        'exam_board_id' => $boardMap[$default['board_code']],
                        'weight_percentage' => $default['weight'],
                        'is_mandatory' => 1
                    ]);
                    $inserted++;
                }
            }
        }
        
        return redirect()->to('/admin/exams/weights?academic_year=' . $academicYearId)
            ->with('success', $inserted . ' pesos configurados com sucesso!');
    }
}