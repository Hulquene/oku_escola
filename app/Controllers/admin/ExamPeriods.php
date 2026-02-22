<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\ExamPeriodModel;
use App\Models\AcademicYearModel;
use App\Models\SemesterModel;
use App\Models\ExamScheduleModel;

class ExamPeriods extends BaseController
{
    protected $examPeriodModel;
    protected $academicYearModel;
    protected $semesterModel;
    protected $examScheduleModel;
    
    public function __construct()
    {
        $this->examPeriodModel = new ExamPeriodModel();
        $this->academicYearModel = new AcademicYearModel();
        $this->semesterModel = new SemesterModel();
        $this->examScheduleModel = new ExamScheduleModel();
    }
    
    /**
     * List all exam periods
     */
    public function index()
    {
        $data['title'] = 'Períodos de Exame';
        
        $academicYearId = $this->request->getGet('academic_year');
        
        $data['periods'] = $this->examPeriodModel->getActivePeriods($academicYearId);
        $data['academicYears'] = $this->academicYearModel->where('is_active', 1)->findAll();
        $data['selectedYear'] = $academicYearId;
        return view('admin/exams/periods/index', $data);
    }
    
    /**
     * Show form to create new exam period
     */
    public function create()
    {
        $data['title'] = 'Novo Período de Exame';
        $data['academicYears'] = $this->academicYearModel->where('is_active', 1)->findAll();
        $data['semesters'] = $this->semesterModel->where('status', 'ativo')->findAll();
        
        return view('admin/exams/periods/form', $data);
    }
    
    /**
     * Show form to edit exam period
     */
    public function edit($id)
    {
        $data['title'] = 'Editar Período de Exame';
        $data['period'] = $this->examPeriodModel->find($id);
        
        if (!$data['period']) {
            return redirect()->to('/admin/exams/periods')
                ->with('error', 'Período de exame não encontrado.');
        }
        
        $data['academicYears'] = $this->academicYearModel->where('is_active', 1)->findAll();
        $data['semesters'] = $this->semesterModel->where('status', 'ativo')->findAll();
        
        return view('admin/exams/periods/form', $data);
    }
    
    /**
     * Save exam period (handles both create and update)
     */
    public function save()
    {
        $id = $this->request->getPost('id');
        
        // Base validation rules for all
        $rules = [
            'period_name' => 'required|min_length[3]|max_length[255]',
            'academic_year_id' => 'required|numeric',
            'semester_id' => 'required|numeric',
            'period_type' => 'required|in_list[Normal,Recurso,Especial,Final,Admissão]',
            'start_date' => 'required|valid_date',
            'end_date' => 'required|valid_date'
        ];
        
        // Add status validation only for updates
        if ($id) {
            $rules['status'] = 'required|in_list[Planejado,Em Andamento,Concluído,Cancelado]';
        }
        
        if (!$this->validate($rules)) {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }
        
        // Prepare common data
        $data = [
            'period_name' => $this->request->getPost('period_name'),
            'academic_year_id' => $this->request->getPost('academic_year_id'),
            'semester_id' => $this->request->getPost('semester_id'),
            'period_type' => $this->request->getPost('period_type'),
            'start_date' => $this->request->getPost('start_date'),
            'end_date' => $this->request->getPost('end_date'),
            'description' => $this->request->getPost('description')
        ];
        
        if ($id) {
            // UPDATE existing period
            $data['status'] = $this->request->getPost('status');
            
            if ($this->examPeriodModel->update($id, $data)) {
                return redirect()->to('/admin/exams/periods')
                    ->with('success', 'Período de exame atualizado com sucesso!');
            }
        } else {
            // INSERT new period
            $data['status'] = 'Planejado';
            $data['created_by'] = session()->get('user_id');
            
            if ($this->examPeriodModel->insert($data)) {
                return redirect()->to('/admin/exams/periods')
                    ->with('success', 'Período de exame criado com sucesso!');
            }
        }
        
        return redirect()->back()
            ->with('error', 'Erro ao salvar período de exame.')
            ->withInput();
    }
    
    /**
     * View exam period details
     */
    public function view($id)
    {
        $data['period'] = $this->examPeriodModel->getWithDetails($id);
        
        if (!$data['period']) {
            return redirect()->to('/admin/exams/periods')
                ->with('error', 'Período de exame não encontrado.');
        }
        
        $data['exams'] = $this->examScheduleModel->getByPeriod($id);
        $data['stats'] = $this->examPeriodModel->getPeriodStats($id);
        $data['title'] = 'Período: ' . $data['period']->period_name;
        
        return view('admin/exams/periods/view', $data);
    }
    
    /**
     * Delete exam period
     */
    public function delete($id)
    {
        if ($this->examPeriodModel->delete($id)) {
            return redirect()->to('/admin/exams/periods')
                ->with('success', 'Período de exame removido com sucesso!');
        } else {
            return redirect()->to('/admin/exams/periods')
                ->with('error', 'Erro ao remover período de exame.');
        }
    }
    
/**
 * Generate exam schedule automatically - mostra o formulário (GET)
 */
public function generateSchedule($periodId)
{
    $period = $this->examPeriodModel->find($periodId);
    
    if (!$period) {
        return redirect()->to('/admin/exams/periods')
            ->with('error', 'Período de exame não encontrado.');
    }
    
    // Verificar se o período pode ter calendário gerado
 /*    if ($period->status !== 'Planejado') {
        return redirect()->to('/admin/exams/periods/view/' . $periodId)
            ->with('error', 'Apenas períodos com status "Planejado" podem ter calendários gerados.');
    } */
    
    // Load models
    $classModel = new \App\Models\ClassModel();
    $boardModel = new \App\Models\ExamBoardModel();
    
    // Buscar turmas com JOIN para trazer level_name
    $data['classes'] = $classModel
        ->select('
            tbl_classes.id,
            tbl_classes.class_name,
            tbl_classes.class_code,
            tbl_grade_levels.level_name
        ')
        ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
        ->where('tbl_classes.academic_year_id', $period->academic_year_id)
        ->where('tbl_classes.is_active', 1)
        ->orderBy('tbl_classes.class_name', 'ASC')
        ->findAll();
    
    $data['examBoards'] = $boardModel->where('is_active', 1)->findAll();
    $data['period'] = $period;
    $data['title'] = 'Gerar Calendário - ' . $period->period_name;
    
    return view('admin/exams/periods/generate_schedule', $data);
}

/**
 * Process the generation of exam schedule (POST)
 */
public function saveSchedule($periodId)
{
    // Log início do processo
    log_message('debug', '========== INÍCIO saveSchedule ==========');
    log_message('debug', 'Period ID: ' . $periodId);
    log_message('debug', 'Request Method: ' . $this->request->getMethod());
    log_message('debug', 'POST data: ' . json_encode($this->request->getPost()));
    
    // Verificar se é POST
    if (!$this->request->is('post')) {
        log_message('error', 'Método não é POST: ' . $this->request->getMethod());
        return redirect()->to('/admin/exams/periods/generate-schedule/' . $periodId)
            ->with('error', 'Método não permitido.');
    }
    
    $period = $this->examPeriodModel->find($periodId);
    
    if (!$period) {
        log_message('error', 'Período não encontrado: ' . $periodId);
        return redirect()->to('/admin/exams/periods')
            ->with('error', 'Período de exame não encontrado.');
    }
    
    log_message('debug', 'Período encontrado: ' . json_encode($period));
    
    // Validar dados do formulário
    $rules = [
        'exam_board_id' => 'required|numeric'
    ];
    
    if (!$this->validate($rules)) {
        $errors = $this->validator->getErrors();
        log_message('error', 'Erros de validação: ' . json_encode($errors));
        return redirect()->back()
            ->with('errors', $errors)
            ->withInput();
    }
    
    $classIds = $this->request->getPost('class_ids') ?: [];
    $examBoardId = $this->request->getPost('exam_board_id');
    
    log_message('debug', 'Class IDs recebidos: ' . json_encode($classIds));
    log_message('debug', 'Exam Board ID: ' . $examBoardId);
    
    // Validar se pelo menos uma turma foi selecionada
    if (empty($classIds)) {
        log_message('error', 'Nenhuma turma selecionada');
        return redirect()->back()
            ->with('error', 'Selecione pelo menos uma turma para gerar o calendário.')
            ->withInput();
    }
    
    // Verificar se as turmas têm disciplinas antes de gerar
    $classDisciplineModel = new \App\Models\ClassDisciplineModel();
    $turmasSemDisciplinas = [];
    $turmasComDisciplinas = [];
    
    foreach ($classIds as $classId) {
        $disciplinas = $classDisciplineModel
            ->where('class_id', $classId)
            ->where('semester_id', $period->semester_id)
            ->where('is_active', 1)
            ->countAllResults();
        
        if ($disciplinas == 0) {
            // Buscar nome da turma
            $classModel = new \App\Models\ClassModel();
            $turma = $classModel->find($classId);
            $turmasSemDisciplinas[] = $turma->class_name . ' (ID: ' . $classId . ')';
        } else {
            $turmasComDisciplinas[] = $classId;
        }
    }
    
    // Se todas as turmas não têm disciplinas
    if (empty($turmasComDisciplinas)) {
        $mensagem = 'Nenhuma das turmas selecionadas possui disciplinas associadas para este semestre.';
        if (!empty($turmasSemDisciplinas)) {
            $mensagem .= ' Turmas sem disciplinas: ' . implode(', ', $turmasSemDisciplinas);
        }
        log_message('error', $mensagem);
        return redirect()->back()
            ->with('error', $mensagem)
            ->withInput();
    }
    
    // Se algumas turmas não têm disciplinas, avisar mas continuar com as que têm
    if (!empty($turmasSemDisciplinas)) {
        $mensagem = 'Aviso: Algumas turmas não têm disciplinas e serão ignoradas: ' . implode(', ', $turmasSemDisciplinas);
        log_message('warning', $mensagem);
        session()->setFlashdata('warning', $mensagem);
    }
    
    // Gerar calendário apenas para as turmas que têm disciplinas
    log_message('debug', 'Chamando generateForPeriod com turmas: ' . json_encode($turmasComDisciplinas));
    
    $result = $this->examScheduleModel->generateForPeriod($periodId, $turmasComDisciplinas, $examBoardId);
    
    log_message('debug', 'Resultado do generateForPeriod: ' . ($result ? 'true' : 'false'));
    
    if ($result) {
        log_message('debug', 'SUCESSO: Calendário gerado');
        return redirect()->to('/admin/exams/periods/view/' . $periodId)
            ->with('success', 'Calendário de exames gerado com sucesso para ' . count($turmasComDisciplinas) . ' turmas!');
    } else {
        log_message('error', 'ERRO: Falha ao gerar calendário');
        return redirect()->back()
            ->with('error', 'Erro ao gerar calendário de exames. Verifique se as turmas têm disciplinas associadas.')
            ->withInput();
    }
}
    /**
 * Start an exam period (change status to 'Em Andamento')
 */
public function start($id)
{
    $period = $this->examPeriodModel->find($id);
    
    if (!$period) {
        return redirect()->to('/admin/exams/periods')
            ->with('error', 'Período de exame não encontrado.');
    }
    
    // Verificar se o período pode ser iniciado
    $today = date('Y-m-d');
    
    if ($period->status !== 'Planejado') {
        return redirect()->back()
            ->with('error', 'Apenas períodos com status "Planejado" podem ser iniciados.');
    }
    
    if ($period->start_date > $today) {
        return redirect()->back()
            ->with('warning', 'A data de início do período é ' . date('d/m/Y', strtotime($period->start_date)) . '. Deseja iniciar mesmo assim?');
    }
    
    // Atualizar status
    $updateData = [
        'status' => 'Em Andamento'
    ];
    
    if ($this->examPeriodModel->update($id, $updateData)) {
        return redirect()->to('/admin/exams/periods/view/' . $id)
            ->with('success', 'Período de exame iniciado com sucesso!');
    } else {
        return redirect()->back()
            ->with('error', 'Erro ao iniciar período de exame.');
    }
}

/**
 * Pause an exam period (change status to 'Planejado')
 */
public function pause($id)
{
    $period = $this->examPeriodModel->find($id);
    
    if (!$period) {
        return redirect()->to('/admin/exams/periods')
            ->with('error', 'Período de exame não encontrado.');
    }
    
    if ($period->status !== 'Em Andamento') {
        return redirect()->back()
            ->with('error', 'Apenas períodos com status "Em Andamento" podem ser pausados.');
    }
    
    $updateData = [
        'status' => 'Planejado'
    ];
    
    if ($this->examPeriodModel->update($id, $updateData)) {
        return redirect()->to('/admin/exams/periods/view/' . $id)
            ->with('success', 'Período de exame pausado com sucesso!');
    } else {
        return redirect()->back()
            ->with('error', 'Erro ao pausar período de exame.');
    }
}

/**
 * Complete an exam period (change status to 'Concluído')
 */
public function complete($id)
{
    $period = $this->examPeriodModel->find($id);
    
    if (!$period) {
        return redirect()->to('/admin/exams/periods')
            ->with('error', 'Período de exame não encontrado.');
    }
    
    if ($period->status !== 'Em Andamento') {
        return redirect()->back()
            ->with('error', 'Apenas períodos com status "Em Andamento" podem ser concluídos.');
    }
    
    $updateData = [
        'status' => 'Concluído'
    ];
    
    if ($this->examPeriodModel->update($id, $updateData)) {
        return redirect()->to('/admin/exams/periods/view/' . $id)
            ->with('success', 'Período de exame concluído com sucesso!');
    } else {
        return redirect()->back()
            ->with('error', 'Erro ao concluir período de exame.');
    }
}

/**
 * Cancel an exam period (change status to 'Cancelado')
 */
public function cancel($id)
{
    $period = $this->examPeriodModel->find($id);
    
    if (!$period) {
        return redirect()->to('/admin/exams/periods')
            ->with('error', 'Período de exame não encontrado.');
    }
    
    $updateData = [
        'status' => 'Cancelado'
    ];
    
    if ($this->examPeriodModel->update($id, $updateData)) {
        return redirect()->to('/admin/exams/periods')
            ->with('success', 'Período de exame cancelado com sucesso!');
    } else {
        return redirect()->back()
            ->with('error', 'Erro ao cancelar período de exame.');
    }
}
}