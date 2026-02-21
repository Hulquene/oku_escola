<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\ExamScheduleModel;
use App\Models\ExamPeriodModel;
use App\Models\ClassModel;
use App\Models\DisciplineModel;
use App\Models\ExamBoardModel;
use App\Models\ExamAttendanceModel;
use App\Models\ExamResultModel;
use App\Models\EnrollmentModel;

class ExamSchedules extends BaseController
{
    protected $examScheduleModel;
    protected $examPeriodModel;
    protected $classModel;
    protected $disciplineModel;
    protected $examBoardModel;
    protected $examAttendanceModel;
    protected $examResultModel;
    protected $enrollmentModel;
    
    public function __construct()
    {
        $this->examScheduleModel = new ExamScheduleModel();
        $this->examPeriodModel = new ExamPeriodModel();
        $this->classModel = new ClassModel();
        $this->disciplineModel = new DisciplineModel();
        $this->examBoardModel = new ExamBoardModel();
        $this->examAttendanceModel = new ExamAttendanceModel();
        $this->examResultModel = new ExamResultModel();
        $this->enrollmentModel = new EnrollmentModel();
    }
    
    /**
     * List all exam schedules
     */
    public function index()
    {
        $data['title'] = 'Calendário de Exames';
        
        $periodId = $this->request->getGet('period_id');
        $classId = $this->request->getGet('class_id');
        $date = $this->request->getGet('date');
        
        $builder = $this->examScheduleModel
            ->select('
                tbl_exam_schedules.*,
                tbl_exam_periods.period_name,
                tbl_classes.class_name,
                tbl_disciplines.discipline_name,
                tbl_exam_boards.board_name,
                tbl_exam_boards.board_type
            ')
            ->join('tbl_exam_periods', 'tbl_exam_periods.id = tbl_exam_schedules.exam_period_id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_exam_schedules.class_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id')
            ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exam_schedules.exam_board_id');
        
        if ($periodId) {
            $builder->where('tbl_exam_schedules.exam_period_id', $periodId);
        }
        
        if ($classId) {
            $builder->where('tbl_exam_schedules.class_id', $classId);
        }
        
        if ($date) {
            $builder->where('tbl_exam_schedules.exam_date', $date);
        }
        
        $data['schedules'] = $builder->orderBy('exam_date', 'ASC')
            ->orderBy('exam_time', 'ASC')
            ->paginate(20);
        
        $data['pager'] = $this->examScheduleModel->pager;
        $data['periods'] = $this->examPeriodModel->findAll();
        $data['classes'] = $this->classModel->where('is_active', 1)->findAll();
        
        return view('admin/exams/schedules/index', $data);
    }
    
    /**
     * Create new exam schedule
     */
    public function create()
    {
        $data['title'] = 'Agendar Exame';
        
        if ($this->request->getMethod() === 'post') {
            $rules = [
                'exam_period_id' => 'required|numeric',
                'class_id' => 'required|numeric',
                'discipline_id' => 'required|numeric',
                'exam_board_id' => 'required|numeric',
                'exam_date' => 'required|valid_date',
                'exam_time' => 'permit_empty'
            ];
            
            // Check for conflicts
            $classId = $this->request->getPost('class_id');
            $examDate = $this->request->getPost('exam_date');
            $examTime = $this->request->getPost('exam_time');
            
            if ($this->examScheduleModel->checkConflicts($classId, $examDate, $examTime)) {
                return redirect()->back()
                    ->with('error', 'Já existe um exame agendado para esta turma nesta data/horário.')
                    ->withInput();
            }
            
            if ($this->validate($rules)) {
                $data = [
                    'exam_period_id' => $this->request->getPost('exam_period_id'),
                    'class_id' => $classId,
                    'discipline_id' => $this->request->getPost('discipline_id'),
                    'exam_board_id' => $this->request->getPost('exam_board_id'),
                    'exam_date' => $examDate,
                    'exam_time' => $examTime,
                    'exam_room' => $this->request->getPost('exam_room'),
                    'duration_minutes' => $this->request->getPost('duration_minutes') ?: 120,
                    'observations' => $this->request->getPost('observations'),
                    'status' => 'Agendado'
                ];
                
                if ($this->examScheduleModel->insert($data)) {
                    return redirect()->to('/admin/exams/schedules')
                        ->with('success', 'Exame agendado com sucesso!');
                } else {
                    return redirect()->back()
                        ->with('error', 'Erro ao agendar exame.')
                        ->withInput();
                }
            } else {
                return redirect()->back()
                    ->with('errors', $this->validator->getErrors())
                    ->withInput();
            }
        }
        
        $data['periods'] = $this->examPeriodModel->where('status !=', 'Concluído')->findAll();
        $data['classes'] = $this->classModel->where('is_active', 1)->findAll();
        $data['examBoards'] = $this->examBoardModel->where('is_active', 1)->findAll();
        
        return view('admin/exams/schedules/form', $data);
    }
   /**
 * Edit exam schedule
 */
public function edit($id)
{
    $data['schedule'] = $this->examScheduleModel
        ->select('
            tbl_exam_schedules.*,
            tbl_disciplines.discipline_name,
            tbl_disciplines.discipline_code
        ')
        ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id', 'left')
        ->find($id);
    
    if (!$data['schedule']) {
        return redirect()->to('/admin/exams/schedules')
            ->with('error', 'Agendamento não encontrado.');
    }
    
    $data['title'] = 'Editar Exame: ' . $data['schedule']->discipline_name;
    
    if ($this->request->getMethod() === 'post') {
        $rules = [
            'exam_period_id' => 'required|numeric',
            'class_id' => 'required|numeric',
            'discipline_id' => 'required|numeric',
            'exam_board_id' => 'required|numeric',
            'exam_date' => 'required|valid_date',
            'status' => 'required|in_list[Agendado,Realizado,Cancelado,Adiado]'
        ];
        
        // Check for conflicts (excluding current)
        $classId = $this->request->getPost('class_id');
        $examDate = $this->request->getPost('exam_date');
        $examTime = $this->request->getPost('exam_time');
        
        if ($this->examScheduleModel->checkConflicts($classId, $examDate, $examTime, $id)) {
            return redirect()->back()
                ->with('error', 'Já existe um exame agendado para esta turma nesta data/horário.')
                ->withInput();
        }
        
        if ($this->validate($rules)) {
            $updateData = [
                'exam_period_id' => $this->request->getPost('exam_period_id'),
                'class_id' => $classId,
                'discipline_id' => $this->request->getPost('discipline_id'),
                'exam_board_id' => $this->request->getPost('exam_board_id'),
                'exam_date' => $examDate,
                'exam_time' => $examTime,
                'exam_room' => $this->request->getPost('exam_room'),
                'duration_minutes' => $this->request->getPost('duration_minutes'),
                'observations' => $this->request->getPost('observations'),
                'status' => $this->request->getPost('status')
            ];
            
            if ($this->examScheduleModel->update($id, $updateData)) {
                return redirect()->to('/admin/exams/schedules')
                    ->with('success', 'Agendamento atualizado com sucesso!');
            } else {
                return redirect()->back()
                    ->with('error', 'Erro ao atualizar agendamento.')
                    ->withInput();
            }
        } else {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }
    }
    
    $data['periods'] = $this->examPeriodModel->findAll();
    $data['classes'] = $this->classModel->where('is_active', 1)->findAll();
    $data['examBoards'] = $this->examBoardModel->where('is_active', 1)->findAll();
    
    return view('admin/exams/schedules/form', $data);
}
/**
 * Display attendance form
 */
public function attendance($id)
{
    $data['schedule'] = $this->examScheduleModel
        ->select('
            tbl_exam_schedules.*,
            tbl_classes.class_name,
            tbl_classes.class_code,
            tbl_disciplines.discipline_name,
            tbl_disciplines.discipline_code,
            tbl_exam_periods.period_name,
            tbl_academic_years.year_name
        ')
        ->join('tbl_classes', 'tbl_classes.id = tbl_exam_schedules.class_id')
        ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id')
        ->join('tbl_exam_periods', 'tbl_exam_periods.id = tbl_exam_schedules.exam_period_id')
        ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_classes.academic_year_id', 'left')
        ->find($id);
    
    if (!$data['schedule']) {
        return redirect()->to('/admin/exams/schedules')
            ->with('error', 'Agendamento não encontrado.');
    }
    
    $data['title'] = 'Registar Presenças - ' . $data['schedule']->discipline_name . ' (' . $data['schedule']->class_name . ')';
    
    // Buscar alunos com status "Ativo" e "Pendente"
    $data['students'] = $this->enrollmentModel
        ->select('
            tbl_enrollments.id as enrollment_id,
            tbl_enrollments.enrollment_number,
            tbl_enrollments.enrollment_date,
            tbl_enrollments.status as enrollment_status,
            tbl_students.id as student_id,
            tbl_students.student_number,
            tbl_students.photo,
            tbl_users.id as user_id,
            tbl_users.first_name,
            tbl_users.last_name,
            tbl_users.email,
            CONCAT(tbl_users.first_name, " ", tbl_users.last_name) as full_name
        ')
        ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
        ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
        ->where('tbl_enrollments.class_id', $data['schedule']->class_id)
        ->whereIn('tbl_enrollments.status', ['Ativo', 'Pendente'])
        ->orderBy('tbl_users.first_name', 'ASC')
        ->orderBy('tbl_users.last_name', 'ASC')
        ->findAll();
    
    // Get existing attendance if any
    $existingAttendance = $this->examAttendanceModel
        ->where('exam_schedule_id', $id)
        ->findAll();
    
    // Map existing attendance by enrollment_id
    $data['attendanceMap'] = [];
    foreach ($existingAttendance as $att) {
        $data['attendanceMap'][$att->enrollment_id] = $att;
    }
    
    // Statistics
    $data['totalStudents'] = count($data['students']);
    $data['presentCount'] = count(array_filter($existingAttendance, function($a) { 
        return $a->attended == 1; 
    }));
    $data['absentCount'] = $data['totalStudents'] - $data['presentCount'];
    
    // Verificar se já foi registado
    $data['isRecorded'] = !empty($existingAttendance);
    
    // Informações do agendamento formatadas
    $data['schedule_info'] = [
        'date' => date('d/m/Y', strtotime($data['schedule']->exam_date)),
        'time' => $data['schedule']->exam_time ? date('H:i', strtotime($data['schedule']->exam_time)) : 'Horário não definido',
        'duration' => $data['schedule']->duration_minutes ? $data['schedule']->duration_minutes . ' minutos' : 'Duração não definida',
        'room' => $data['schedule']->exam_room ?? 'Não definida',
        'academic_year' => $data['schedule']->year_name ?? 'N/A'
    ];
    
    return view('admin/exams/schedules/attendance', $data);
}
/**
 * Save exam attendance
 */
public function saveAttendance($id)
{
    $schedule = $this->examScheduleModel->find($id);
    
    if (!$schedule) {
        return redirect()->to('/admin/exams/schedules')
            ->with('error', 'Agendamento não encontrado.');
    }
    
    $attendance = $this->request->getPost('attendance') ?? [];
    $action = $this->request->getPost('action');
    
    // Verificar se existem dados de presença
    if (empty($attendance)) {
        return redirect()->back()->withInput()
            ->with('error', 'Nenhum dado de presença foi recebido. Verifique se o formulário está correto.');
    }
    
    $db = db_connect();
    $db->transStart();
    
    // Delete existing attendance
    $this->examAttendanceModel->where('exam_schedule_id', $id)->delete();
    
    // Insert new attendance
    $attendanceData = [];
    $presentCount = 0;
    
    foreach ($attendance as $enrollmentId => $value) {
        // Ignorar o campo 'exists'
        if (!is_numeric($enrollmentId)) {
            continue;
        }
        
        // Verificar se o campo attended existe (checkbox marcado)
        $attended = isset($value['attended']) ? 1 : 0;
        if ($attended) $presentCount++;
        
        $attendanceData[] = [
            'exam_schedule_id' => $id,
            'enrollment_id' => $enrollmentId,
            'attended' => $attended,
            'check_in_time' => $attended ? date('Y-m-d H:i:s') : null,
            'check_in_method' => 'Manual',
            'observations' => $value['observations'] ?? null,
            'recorded_by' => session()->get('user_id')
        ];
    }
    
    if (!empty($attendanceData)) {
        if (!$this->examAttendanceModel->insertBatch($attendanceData)) {
            $db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Erro ao registar presenças.');
        }
        
        // REMOVIDO: Não mudar o status para "Realizado" aqui
        // O status só será alterado quando as notas forem lançadas
        
        $db->transComplete();
        
        if ($db->transStatus()) {
            $totalStudents = count($attendanceData);
            $message = "Presenças registadas com sucesso! {$presentCount} de {$totalStudents} alunos presentes.";
            
            if ($action === 'save_and_return') {
                return redirect()->to('/admin/exams/schedules/view/' . $id)
                    ->with('success', $message);
            } else {
                return redirect()->to('/admin/exams/schedules')
                    ->with('success', $message);
            }
        }
    }
    
    return redirect()->back()->withInput()
        ->with('error', 'Nenhum dado de presença foi processado.');
}
 /**
 * Display exam results form
 */
public function results($id)
{
    $data['schedule'] = $this->examScheduleModel
        ->select('
            tbl_exam_schedules.*,
            tbl_classes.class_name,
            tbl_disciplines.discipline_name,
            tbl_exam_boards.board_name,
            tbl_exam_boards.board_type,
            tbl_exam_boards.weight,
            tbl_exam_periods.period_name
        ')
        ->join('tbl_classes', 'tbl_classes.id = tbl_exam_schedules.class_id')
        ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id')
        ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exam_schedules.exam_board_id')
        ->join('tbl_exam_periods', 'tbl_exam_periods.id = tbl_exam_schedules.exam_period_id')
        ->find($id);
    
    if (!$data['schedule']) {
        return redirect()->to('/admin/exam-schedules')
            ->with('error', 'Agendamento não encontrado.');
    }
    
    $data['title'] = 'Registar Notas - ' . $data['schedule']->discipline_name;
    
    // Get students with attendance (apenas alunos presentes)
    $data['students'] = $this->examAttendanceModel
        ->select('
            tbl_exam_attendance.*,
            tbl_enrollments.student_id,
            tbl_students.student_number,
            tbl_users.first_name,
            tbl_users.last_name,
            CONCAT(tbl_users.first_name, " ", tbl_users.last_name) as full_name
        ')
        ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_exam_attendance.enrollment_id')
        ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
        ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
        ->where('tbl_exam_attendance.exam_schedule_id', $id)
        ->where('tbl_exam_attendance.attended', 1)
        ->orderBy('tbl_users.first_name', 'ASC')
        ->orderBy('tbl_users.last_name', 'ASC')
        ->findAll();
    
    // Get existing results
    $existingResults = $this->examResultModel
        ->where('exam_schedule_id', $id)
        ->findAll();
    
    $data['existingScores'] = [];
    foreach ($existingResults as $result) {
        $data['existingScores'][$result->enrollment_id] = $result->score;
    }
    
    // Estatísticas para o card
    $data['totalStudents'] = count($data['students']);
    $data['hasResults'] = !empty($existingResults);
    
    return view('admin/exams/schedules/results', $data);
}
/**
 * Save exam results
 */
/**
 * Save exam results
 */
public function saveResults($id)
{
    log_message('debug', '=== INÍCIO saveResults ===');
    log_message('debug', 'ID recebido: ' . $id);
    
    $schedule = $this->examScheduleModel->find($id);
    
    if (!$schedule) {
        log_message('error', 'Agendamento não encontrado: ' . $id);
        return redirect()->to('/admin/exam-schedules')
            ->with('error', 'Agendamento não encontrado.');
    }
    
    $scores = $this->request->getPost('scores') ?? [];
    $action = $this->request->getPost('action');
    
    log_message('debug', 'Dados recebidos - scores: ' . json_encode($scores));
    log_message('debug', 'Action: ' . ($action ?? 'null'));
    log_message('debug', 'POST completo: ' . json_encode($_POST));
    
    if (empty($scores)) {
        log_message('error', 'Nenhuma nota recebida');
        return redirect()->back()->withInput()
            ->with('error', 'Nenhuma nota foi recebida.');
    }
    
    $db = db_connect();
    $db->transStart();
    
    // Delete existing results
    log_message('debug', 'Deletando resultados existentes para exam_schedule_id: ' . $id);
    $this->examResultModel->where('exam_schedule_id', $id)->delete();
    
    // Insert new results
    $resultsData = [];
    $totalStudents = 0;
    $approvedCount = 0;
    $appealCount = 0;
    $failedCount = 0;
    $sumScores = 0;
    
    foreach ($scores as $enrollmentId => $score) {
        log_message('debug', "Processando enrollment_id: {$enrollmentId}, score: {$score}");
        
        if ($score !== '' && is_numeric($enrollmentId)) {
            $scoreValue = floatval($score);
            
            // Estatísticas
            if ($scoreValue >= 10) $approvedCount++;
            elseif ($scoreValue >= 7) $appealCount++;
            else $failedCount++;
            
            $sumScores += $scoreValue;
            $totalStudents++;
            
            $resultsData[] = [
                'exam_schedule_id' => $id,
                'enrollment_id' => $enrollmentId,
                'score' => $scoreValue,
                'is_absent' => 0,
                'recorded_by' => session()->get('user_id')
            ];
        } else {
            log_message('warning', "Dados inválidos - enrollmentId: {$enrollmentId}, score: {$score}");
        }
    }
    
    log_message('debug', 'Dados para inserção: ' . json_encode($resultsData));
    
    if (!empty($resultsData)) {
        log_message('debug', 'Tentando inserir ' . count($resultsData) . ' registros');
        
        try {
            if (!$this->examResultModel->insertBatch($resultsData)) {
                $errors = $this->examResultModel->errors();
                log_message('error', 'Erro no insertBatch: ' . json_encode($errors));
                $db->transRollback();
                return redirect()->back()->withInput()
                    ->with('error', 'Erro ao registar notas: ' . json_encode($errors));
            }
        } catch (\Exception $e) {
            log_message('error', 'Exceção no insertBatch: ' . $e->getMessage());
            $db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Erro ao registar notas: ' . $e->getMessage());
        }
        
        // Calcular média
        $average = $totalStudents > 0 ? round($sumScores / $totalStudents, 2) : 0;
        
        // ATUALIZAR status do exame para "Realizado"
        log_message('debug', 'Atualizando status do exame para Realizado');
        $this->examScheduleModel->update($id, [
            'status' => 'Realizado'
        ]);
        
        $db->transComplete();
        
        if ($db->transStatus()) {
            $message = "Notas registadas com sucesso! {$totalStudents} alunos processados. "
                     . "Média: {$average} | Aprovados: {$approvedCount} | Recurso: {$appealCount} | Reprovados: {$failedCount}";
            
            log_message('debug', 'Transação completada com sucesso');
            
            if ($action === 'save_and_return') {
                return redirect()->to('/admin/exam-schedules/view/' . $id)
                    ->with('success', $message);
            } else {
                return redirect()->to('/admin/exams/schedules')
                    ->with('success', $message);
            }
        } else {
            log_message('error', 'Transação falhou');
            $db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Erro na transação ao registar notas.');
        }
    }
    
    log_message('error', 'Nenhum dado válido para processar');
    return redirect()->back()->withInput()
        ->with('error', 'Nenhuma nota válida foi processada.');
} 
    /**
     * View exam schedule details
     */
    public function view($id)
    {
        $data['schedule'] = $this->examScheduleModel
            ->select('
                tbl_exam_schedules.*,
                tbl_classes.class_name,
                tbl_classes.class_code,
                tbl_disciplines.discipline_name,
                tbl_disciplines.discipline_code,
                tbl_exam_boards.board_name,
                tbl_exam_boards.board_type,
                tbl_exam_boards.weight,
                tbl_exam_periods.period_name,
                tbl_exam_periods.period_type,
                tbl_academic_years.year_name,
                tbl_semesters.semester_name
            ')
            ->join('tbl_classes', 'tbl_classes.id = tbl_exam_schedules.class_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id')
            ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exam_schedules.exam_board_id')
            ->join('tbl_exam_periods', 'tbl_exam_periods.id = tbl_exam_schedules.exam_period_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_exam_periods.academic_year_id')
            ->join('tbl_semesters', 'tbl_semesters.id = tbl_exam_periods.semester_id')
            ->find($id);
        
        if (!$data['schedule']) {
            return redirect()->to('/admin/exams/schedules')
                ->with('error', 'Agendamento não encontrado.');
        }
        
        $data['title'] = 'Detalhes do Exame';
        
        // Get attendance
        $data['attendance'] = $this->examAttendanceModel->getByExam($id);
        $data['attendanceStats'] = $this->examAttendanceModel->getStats($id);
        
        // Get results
        $data['results'] = $this->examResultModel
            ->select('
                tbl_exam_results.*,
                tbl_enrollments.student_id,
                tbl_students.student_number,
                tbl_users.first_name,
                tbl_users.last_name
            ')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_exam_results.enrollment_id')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->where('tbl_exam_results.exam_schedule_id', $id)
            ->findAll();
        
        return view('admin/exams/schedules/view', $data);
    }
    
    /**
     * Delete exam schedule
     */
    public function delete($id)
    {
        // Check if there are results or attendance
        $hasResults = $this->examResultModel->where('exam_schedule_id', $id)->countAllResults() > 0;
        $hasAttendance = $this->examAttendanceModel->where('exam_schedule_id', $id)->countAllResults() > 0;
        
        if ($hasResults || $hasAttendance) {
            return redirect()->back()
                ->with('error', 'Não é possível remover um exame que já possui presenças ou notas registadas.');
        }
        
        if ($this->examScheduleModel->delete($id)) {
            return redirect()->to('/admin/exams/schedules')
                ->with('success', 'Exame removido com sucesso!');
        } else {
            return redirect()->to('/admin/exams/schedules')
                ->with('error', 'Erro ao remover exame.');
        }
    }
    
    /**
     * Get disciplines by class (AJAX)
     */
    public function getDisciplinesByClass($classId)
    {
        $classDisciplineModel = new \App\Models\ClassDisciplineModel();
        
        $disciplines = $classDisciplineModel
            ->select('
                tbl_disciplines.id,
                tbl_disciplines.discipline_name,
                tbl_disciplines.discipline_code
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
            ->where('tbl_class_disciplines.class_id', $classId)
            ->where('tbl_class_disciplines.is_active', 1)
            ->findAll();
        
        return $this->response->setJSON($disciplines);
    }

    /**
 * Save new exam schedule
 */
public function save()
{
    $rules = [
        'exam_period_id' => 'required|numeric',
        'class_id' => 'required|numeric',
        'discipline_id' => 'required|numeric',
        'exam_board_id' => 'required|numeric',
        'exam_date' => 'required|valid_date',
        'exam_time' => 'permit_empty'
    ];
    
    // Check for conflicts
    $classId = $this->request->getPost('class_id');
    $examDate = $this->request->getPost('exam_date');
    $examTime = $this->request->getPost('exam_time');
    
    if ($this->examScheduleModel->checkConflicts($classId, $examDate, $examTime)) {
        return redirect()->back()
            ->with('error', 'Já existe um exame agendado para esta turma nesta data/horário.')
            ->withInput();
    }
    
    if ($this->validate($rules)) {
        $data = [
            'exam_period_id' => $this->request->getPost('exam_period_id'),
            'class_id' => $classId,
            'discipline_id' => $this->request->getPost('discipline_id'),
            'exam_board_id' => $this->request->getPost('exam_board_id'),
            'exam_date' => $examDate,
            'exam_time' => $examTime,
            'exam_room' => $this->request->getPost('exam_room'),
            'duration_minutes' => $this->request->getPost('duration_minutes') ?: 120,
            'observations' => $this->request->getPost('observations'),
            'status' => 'Agendado'
        ];
        
        if ($this->examScheduleModel->insert($data)) {
            return redirect()->to('/admin/exams/schedules')
                ->with('success', 'Exame agendado com sucesso!');
        } else {
            return redirect()->back()
                ->with('error', 'Erro ao agendar exame.')
                ->withInput();
        }
    } else {
        return redirect()->back()
            ->with('errors', $this->validator->getErrors())
            ->withInput();
    }
}

/**
 * Update exam schedule
 */
public function update($id)
{
    $schedule = $this->examScheduleModel->find($id);
    
    if (!$schedule) {
        return redirect()->to('/admin/exams/schedules')
            ->with('error', 'Agendamento não encontrado.');
    }
    
    $rules = [
        'exam_period_id' => 'required|numeric',
        'class_id' => 'required|numeric',
        'discipline_id' => 'required|numeric',
        'exam_board_id' => 'required|numeric',
        'exam_date' => 'required|valid_date',
        'status' => 'required|in_list[Agendado,Realizado,Cancelado,Adiado]'
    ];
    
    // Check for conflicts (excluding current)
    $classId = $this->request->getPost('class_id');
    $examDate = $this->request->getPost('exam_date');
    $examTime = $this->request->getPost('exam_time');
    
    if ($this->examScheduleModel->checkConflicts($classId, $examDate, $examTime, $id)) {
        return redirect()->back()
            ->with('error', 'Já existe um exame agendado para esta turma nesta data/horário.')
            ->withInput();
    }
    
    if ($this->validate($rules)) {
        $updateData = [
            'exam_period_id' => $this->request->getPost('exam_period_id'),
            'class_id' => $classId,
            'discipline_id' => $this->request->getPost('discipline_id'),
            'exam_board_id' => $this->request->getPost('exam_board_id'),
            'exam_date' => $examDate,
            'exam_time' => $examTime,
            'exam_room' => $this->request->getPost('exam_room'),
            'duration_minutes' => $this->request->getPost('duration_minutes'),
            'observations' => $this->request->getPost('observations'),
            'status' => $this->request->getPost('status')
        ];
        
        if ($this->examScheduleModel->update($id, $updateData)) {
            return redirect()->to('/admin/exams/schedules')
                ->with('success', 'Agendamento atualizado com sucesso!');
        } else {
            return redirect()->back()
                ->with('error', 'Erro ao atualizar agendamento.')
                ->withInput();
        }
    } else {
        return redirect()->back()
            ->with('errors', $this->validator->getErrors())
            ->withInput();
    }
}
}