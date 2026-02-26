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
use App\Models\NotificationModel;

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
    protected $notificationModel;
    
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
        $this->notificationModel = new NotificationModel();
    }
    
    /**
     * List all exam schedules
     */
    public function index()
    {
        // Verificar permissão
        if (!can('exam_schedules.list')) {
            return redirect()->to('/admin/dashboard')
                ->with('error', 'Não tem permissão para ver a lista de calendários de exames.');
        }
        
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
                tbl_exam_boards.board_type,
                tbl_exam_boards.board_code
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
        
        // Log de visualização
        log_view('exam_schedules', null, 'Visualizou lista de calendários de exames');
        
        return view('admin/exams/schedules/index', $data);
    }
    
    /**
     * Create new exam schedule
     */
    public function create()
    {
        // Verificar permissão
        if (!can('exam_schedules.create')) {
            return redirect()->to('/admin/exams/schedules')
                ->with('error', 'Não tem permissão para criar calendários de exames.');
        }
        
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
                    $scheduleId = $this->examScheduleModel->getInsertID();
                    
                    // Log de inserção
                    log_insert('exam_schedule', $scheduleId, 'Criou novo agendamento de exame');
                    
                    // Notificar professores
                    $this->notifyTeachers($scheduleId, 'Novo exame agendado');
                    
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
    // Verificar permissão
    if (!can('exam_schedules.edit')) {
        return redirect()->to('/admin/exams/schedules')
            ->with('error', 'Não tem permissão para editar calendários de exames.');
    }
    
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
                // Log de atualização
                log_update('exam_schedule', $id, 'Atualizou agendamento de exame');
                
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
    // Verificar permissão
    if (!can('exams.attendance')) {
        return redirect()->to('/admin/exams/schedules')
            ->with('error', 'Não tem permissão para registar presenças em exames.');
    }
    
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
    // Verificar permissão
    if (!can('exams.attendance')) {
        return redirect()->to('/admin/exams/schedules')
            ->with('error', 'Não tem permissão para registar presenças em exames.');
    }
    
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
            
            // Log de ação
            log_action('attendance', "Registou presenças para exame ID {$id}: {$presentCount} presentes de {$totalStudents} alunos", $id, 'exam_schedule');
            
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
    // Verificar permissão
    if (!can('exams.enter_grades')) {
        return redirect()->to('/admin/exams/schedules')
            ->with('error', 'Não tem permissão para registar notas de exames.');
    }
    
    $data['schedule'] = $this->examScheduleModel
        ->select('
            tbl_exam_schedules.*,
            tbl_classes.class_name,
            tbl_disciplines.discipline_name,
            tbl_exam_boards.board_name,
            tbl_exam_boards.board_type,
            tbl_exam_boards.board_code,
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
public function saveResults($id)
{
    // Verificar permissão
    if (!can('exams.enter_grades')) {
        return redirect()->to('/admin/exams/schedules')
            ->with('error', 'Não tem permissão para registar notas de exames.');
    }
    
    log_message('debug', '=== INÍCIO saveResults ===');
    log_message('debug', 'ID recebido: ' . $id);
    
    $schedule = $this->examScheduleModel->find($id);
    
    if (!$schedule) {
        log_message('error', 'Agendamento não encontrado: ' . $id);
        return redirect()->to('/admin/exam-schedules')
            ->with('error', 'Agendamento não encontrado.');
    }
    
    // ✅ Buscar o board para obter o assessment_type
    $board = $this->examBoardModel->find($schedule->exam_board_id);
    $assessmentType = $board ? $board->board_code : 'AC';
    
    $scores = $this->request->getPost('scores') ?? [];
    $action = $this->request->getPost('action');
    
    log_message('debug', 'Dados recebidos - scores: ' . json_encode($scores));
    log_message('debug', 'Action: ' . ($action ?? 'null'));
    log_message('debug', 'Assessment Type: ' . $assessmentType);
    
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
                'assessment_type' => $assessmentType, // ✅ NOVO: tipo de avaliação do board
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
            
            // Log de ação
            log_action('grades', "Registou notas para exame ID {$id}: {$totalStudents} alunos, média {$average}", $id, 'exam_schedule');
            
            // Notificar alunos
            $this->notifyStudents($id, 'Notas do exame publicadas');
            
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
        // Verificar permissão
        if (!can('exam_schedules.view')) {
            return redirect()->to('/admin/exams/schedules')
                ->with('error', 'Não tem permissão para ver detalhes de calendários de exames.');
        }
        
        $data['schedule'] = $this->examScheduleModel
            ->select('
                tbl_exam_schedules.*,
                tbl_classes.class_name,
                tbl_classes.class_code,
                tbl_disciplines.discipline_name,
                tbl_disciplines.discipline_code,
                tbl_exam_boards.board_name,
                tbl_exam_boards.board_type,
                tbl_exam_boards.board_code,
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
        
        // Log de visualização
        log_view('exam_schedule', $id, 'Visualizou detalhes do exame');
        
        return view('admin/exams/schedules/view', $data);
    }
    
    /**
     * Delete exam schedule
     */
    public function delete($id)
    {
        // Verificar permissão
        if (!can('exam_schedules.delete')) {
            return redirect()->to('/admin/exams/schedules')
                ->with('error', 'Não tem permissão para eliminar calendários de exames.');
        }
        
        // Check if there are results or attendance
        $hasResults = $this->examResultModel->where('exam_schedule_id', $id)->countAllResults() > 0;
        $hasAttendance = $this->examAttendanceModel->where('exam_schedule_id', $id)->countAllResults() > 0;
        
        if ($hasResults || $hasAttendance) {
            return redirect()->back()
                ->with('error', 'Não é possível remover um exame que já possui presenças ou notas registadas.');
        }
        
        if ($this->examScheduleModel->delete($id)) {
            // Log de eliminação
            log_delete('exam_schedule', $id, 'Eliminou agendamento de exame');
            
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
    // Verificar permissão
    if (!can('exam_schedules.create')) {
        return redirect()->to('/admin/exams/schedules')
            ->with('error', 'Não tem permissão para criar calendários de exames.');
    }
    
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
            $scheduleId = $this->examScheduleModel->getInsertID();
            
            // Log de inserção
            log_insert('exam_schedule', $scheduleId, 'Criou novo agendamento de exame');
            
            // Notificar professores
            $this->notifyTeachers($scheduleId, 'Novo exame agendado');
            
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
    // Verificar permissão
    if (!can('exam_schedules.edit')) {
        return redirect()->to('/admin/exams/schedules')
            ->with('error', 'Não tem permissão para editar calendários de exames.');
    }
    
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
            // Log de atualização
            log_update('exam_schedule', $id, 'Atualizou agendamento de exame');
            
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

/**
 * Get calendar events for FullCalendar
 * 
 * @return JSON
 */
public function getCalendarEvents()
{
    // Verificar permissão (qualquer utilizador com acesso ao calendário)
    if (!can('exams.calendar') && !can('calendar.view')) {
        return $this->response->setJSON([]);
    }
    
    // Buscar todos os agendamentos
    $schedules = $this->examScheduleModel
        ->select('
            tbl_exam_schedules.id,
            tbl_exam_schedules.exam_date,
            tbl_exam_schedules.exam_time,
            tbl_exam_schedules.status,
            tbl_exam_schedules.exam_room,
            tbl_exam_schedules.duration_minutes,
            CONCAT(tbl_disciplines.discipline_name, " - ", tbl_classes.class_name) as title,
            tbl_exam_boards.board_name,
            tbl_exam_boards.board_type,
            tbl_exam_boards.board_code,
            tbl_classes.class_name,
            tbl_disciplines.discipline_name
        ')
        ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id')
        ->join('tbl_classes', 'tbl_classes.id = tbl_exam_schedules.class_id')
        ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exam_schedules.exam_board_id')
        ->orderBy('tbl_exam_schedules.exam_date', 'ASC')
        ->orderBy('tbl_exam_schedules.exam_time', 'ASC')
        ->findAll();
    
    $events = [];
    
    // Cores por status
    $colors = [
        'Agendado' => '#0d6efd',   // Azul
        'Realizado' => '#198754',   // Verde
        'Cancelado' => '#dc3545',   // Vermelho
        'Adiado' => '#ffc107'       // Amarelo
    ];
    
    foreach ($schedules as $schedule) {
        // Formatar data e hora para o FullCalendar
        $start = $schedule->exam_date;
        if ($schedule->exam_time) {
            $start .= 'T' . $schedule->exam_time;
        }
        
        // Calcular end com base na duração (se tiver)
        $end = null;
        if (!empty($schedule->duration_minutes)) {
            $end = date('Y-m-d H:i:s', strtotime($start . ' + ' . $schedule->duration_minutes . ' minutes'));
        }
        
        $events[] = [
            'id' => $schedule->id,
            'title' => $schedule->title,
            'start' => $start,
            'end' => $end,
            'color' => $colors[$schedule->status] ?? '#6c757d',
            'textColor' => '#ffffff',
            'extendedProps' => [
                'status' => $schedule->status,
                'board' => $schedule->board_name,
                'board_type' => $schedule->board_type,
                'board_code' => $schedule->board_code,
                'room' => $schedule->exam_room ?? 'Não definida',
                'class' => $schedule->class_name,
                'discipline' => $schedule->discipline_name
            ]
        ];
    }
    
    return $this->response->setJSON($events);
}

/**
 * Get filtered calendar events
 * 
 * @return JSON
 */
public function getFilteredCalendarEvents()
{
    // Verificar permissão (qualquer utilizador com acesso ao calendário)
    if (!can('exams.calendar') && !can('calendar.view')) {
        return $this->response->setJSON([]);
    }
    
    $periodId = $this->request->getGet('period_id');
    $classId = $this->request->getGet('class_id');
    $startDate = $this->request->getGet('start');
    $endDate = $this->request->getGet('end');
    
    $builder = $this->examScheduleModel
        ->select('
            tbl_exam_schedules.id,
            tbl_exam_schedules.exam_date,
            tbl_exam_schedules.exam_time,
            tbl_exam_schedules.status,
            tbl_exam_schedules.exam_room,
            CONCAT(tbl_disciplines.discipline_name, " - ", tbl_classes.class_name) as title,
            tbl_exam_boards.board_name,
            tbl_exam_boards.board_type,
            tbl_exam_boards.board_code,
            tbl_classes.class_name,
            tbl_disciplines.discipline_name
        ')
        ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id')
        ->join('tbl_classes', 'tbl_classes.id = tbl_exam_schedules.class_id')
        ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exam_schedules.exam_board_id');
    
    if ($periodId) {
        $builder->where('tbl_exam_schedules.exam_period_id', $periodId);
    }
    
    if ($classId) {
        $builder->where('tbl_exam_schedules.class_id', $classId);
    }
    
    if ($startDate && $endDate) {
        $builder->where('tbl_exam_schedules.exam_date >=', $startDate)
                ->where('tbl_exam_schedules.exam_date <=', $endDate);
    }
    
    $schedules = $builder->orderBy('tbl_exam_schedules.exam_date', 'ASC')
        ->orderBy('tbl_exam_schedules.exam_time', 'ASC')
        ->findAll();
    
    $events = [];
    $colors = [
        'Agendado' => '#0d6efd',
        'Realizado' => '#198754',
        'Cancelado' => '#dc3545',
        'Adiado' => '#ffc107'
    ];
    
    foreach ($schedules as $schedule) {
        $start = $schedule->exam_date;
        if ($schedule->exam_time) {
            $start .= 'T' . $schedule->exam_time;
        }
        
        $events[] = [
            'id' => $schedule->id,
            'title' => $schedule->title,
            'start' => $start,
            'color' => $colors[$schedule->status] ?? '#6c757d',
            'textColor' => '#ffffff',
            'url' => site_url('admin/exams/schedules/view/' . $schedule->id),
            'extendedProps' => [
                'status' => $schedule->status,
                'board' => $schedule->board_name,
                'board_code' => $schedule->board_code,
                'room' => $schedule->exam_room
            ]
        ];
    }
    
    return $this->response->setJSON($events);
}

/**
 * Notify teachers about exam
 */
private function notifyTeachers($scheduleId, $message)
{
    // Buscar detalhes do exame
    $schedule = $this->examScheduleModel
        ->select('
            tbl_exam_schedules.*,
            tbl_classes.class_name,
            tbl_disciplines.discipline_name,
            tbl_exam_boards.board_name
        ')
        ->join('tbl_classes', 'tbl_classes.id = tbl_exam_schedules.class_id')
        ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id')
        ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exam_schedules.exam_board_id')
        ->find($scheduleId);
    
    if (!$schedule) return false;
    
    // Buscar professores da disciplina na turma
    $classDisciplineModel = new \App\Models\ClassDisciplineModel();
    $teachers = $classDisciplineModel
        ->select('teacher_id')
        ->where('class_id', $schedule->class_id)
        ->where('discipline_id', $schedule->discipline_id)
        ->findAll();
    
    $teacherIds = array_column($teachers, 'teacher_id');
    
    if (empty($teacherIds)) return false;
    
    // Criar notificação
    $notificationData = [
        'title' => 'Novo Exame Agendado',
        'message' => "Foi agendado um exame de {$schedule->discipline_name} para a turma {$schedule->class_name} no dia " . date('d/m/Y', strtotime($schedule->exam_date)),
        'type' => 'exam_schedule',
        'icon' => 'fa-calendar-check',
        'color' => 'primary',
        'link' => site_url('teachers/exams')
    ];
    
    return notify_user_type('teacher', $notificationData, $teacherIds);
}

/**
 * Notify students about exam results
 */
private function notifyStudents($scheduleId, $message)
{
    // Buscar detalhes do exame
    $schedule = $this->examScheduleModel
        ->select('
            tbl_exam_schedules.*,
            tbl_classes.class_name,
            tbl_disciplines.discipline_name,
            tbl_exam_boards.board_name
        ')
        ->join('tbl_classes', 'tbl_classes.id = tbl_exam_schedules.class_id')
        ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id')
        ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exam_schedules.exam_board_id')
        ->find($scheduleId);
    
    if (!$schedule) return false;
    
    // Buscar alunos da turma com presença
    $students = $this->examAttendanceModel
        ->select('enrollment_id')
        ->where('exam_schedule_id', $scheduleId)
        ->where('attended', 1)
        ->findAll();
    
    $enrollmentIds = array_column($students, 'enrollment_id');
    
    if (empty($enrollmentIds)) return false;
    
    // Buscar IDs dos alunos (users)
    $enrollments = $this->enrollmentModel
        ->select('student_id')
        ->whereIn('id', $enrollmentIds)
        ->findAll();
    
    $studentIds = array_column($enrollments, 'student_id');
    
    if (empty($studentIds)) return false;
    
    // Buscar user_ids dos alunos
    $studentModel = new \App\Models\StudentModel();
    $users = $studentModel
        ->select('user_id')
        ->whereIn('id', $studentIds)
        ->findAll();
    
    $userIds = array_column($users, 'user_id');
    
    if (empty($userIds)) return false;
    
    // Criar notificação
    $notificationData = [
        'title' => 'Notas Publicadas',
        'message' => "As notas do exame de {$schedule->discipline_name} foram publicadas. Consulte o seu boletim.",
        'type' => 'exam_result',
        'icon' => 'fa-star',
        'color' => 'success',
        'link' => site_url('student/grades')
    ];
    
    return notify_user_type('student', $notificationData, $userIds);
}
}