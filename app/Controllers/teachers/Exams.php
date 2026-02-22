<?php

namespace App\Controllers\teachers;

use App\Controllers\BaseController;
use App\Models\ExamScheduleModel;      // NOVO
use App\Models\ExamPeriodModel;         // NOVO
use App\Models\ExamBoardModel;
use App\Models\ClassModel;
use App\Models\DisciplineModel;
use App\Models\EnrollmentModel;
use App\Models\ExamResultModel;
use App\Models\ClassDisciplineModel;
use App\Models\SemesterModel;

class Exams extends BaseController
{
    protected $examScheduleModel;       // NOVO
    protected $examPeriodModel;          // NOVO
    protected $examBoardModel;
    protected $classModel;
    protected $disciplineModel;
    protected $enrollmentModel;
    protected $examResultModel;
    protected $classDisciplineModel;
    protected $semesterModel;
    
    public function __construct()
    {
        $this->examScheduleModel = new ExamScheduleModel();   // NOVO
        $this->examPeriodModel = new ExamPeriodModel();       // NOVO
        $this->examBoardModel = new ExamBoardModel();
        $this->classModel = new ClassModel();
        $this->disciplineModel = new DisciplineModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->examResultModel = new ExamResultModel();
        $this->classDisciplineModel = new ClassDisciplineModel();
        $this->semesterModel = new SemesterModel();
    }
    
    /**
     * List exams for the teacher (via exam_schedules)
     *//**
 * List exams for the teacher (via exam_schedules)
 */
public function index()
{
    $data['title'] = 'Meus Exames';
    
    $teacherId = $this->session->get('user_id');
    
    $filters = [
        'class' => $this->request->getGet('class'),
        'status' => $this->request->getGet('status'),
        'type' => $this->request->getGet('type'),
        'board' => $this->request->getGet('board'),
        'period' => $this->request->getGet('period'),
        'start_date' => $this->request->getGet('start_date'),
        'end_date' => $this->request->getGet('end_date'),
        'has_results' => $this->request->getGet('has_results')
    ];
    
    $builder = $this->examScheduleModel
        ->select('
            tbl_exam_schedules.*,
            tbl_classes.class_name,
            tbl_disciplines.discipline_name,
            tbl_exam_boards.board_name,
            tbl_exam_boards.board_type,
            tbl_exam_boards.board_code,
            tbl_exam_periods.period_name,
            tbl_exam_periods.period_type,
            (SELECT COUNT(*) FROM tbl_exam_results WHERE exam_schedule_id = tbl_exam_schedules.id) as results_count
        ')
        ->join('tbl_classes', 'tbl_classes.id = tbl_exam_schedules.class_id')
        ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id')
        ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exam_schedules.exam_board_id')
        ->join('tbl_exam_periods', 'tbl_exam_periods.id = tbl_exam_schedules.exam_period_id')
        ->join('tbl_class_disciplines', 'tbl_class_disciplines.class_id = tbl_exam_schedules.class_id AND tbl_class_disciplines.discipline_id = tbl_exam_schedules.discipline_id')
        ->where('tbl_class_disciplines.teacher_id', $teacherId);
    
    if (!empty($filters['class'])) {
        $builder->where('tbl_exam_schedules.class_id', $filters['class']);
    }
    
    if (!empty($filters['board'])) {
        $builder->where('tbl_exam_schedules.exam_board_id', $filters['board']);
    }
    
    if (!empty($filters['period'])) {
        $builder->where('tbl_exam_schedules.exam_period_id', $filters['period']);
    }
    
    if (!empty($filters['type'])) {
        $builder->where('tbl_exam_boards.board_code', $filters['type']);
    }
    
    if (!empty($filters['start_date'])) {
        $builder->where('tbl_exam_schedules.exam_date >=', $filters['start_date']);
    }
    
    if (!empty($filters['end_date'])) {
        $builder->where('tbl_exam_schedules.exam_date <=', $filters['end_date']);
    }
    
    if ($filters['status'] == 'pending') {
        $builder->where('tbl_exam_schedules.exam_date >=', date('Y-m-d'));
    } elseif ($filters['status'] == 'completed') {
        $builder->where('tbl_exam_schedules.exam_date <', date('Y-m-d'));
    } elseif ($filters['status'] == 'today') {
        $builder->where('tbl_exam_schedules.exam_date', date('Y-m-d'));
    }
    
    if ($filters['has_results'] == 'yes') {
        $builder->having('results_count >', 0);
    } elseif ($filters['has_results'] == 'no') {
        $builder->having('results_count =', 0);
    }
    
    $data['exams'] = $builder->orderBy('tbl_exam_schedules.exam_date', 'DESC')->findAll();
    
    // Classes para filtro
    $data['classes'] = $this->classDisciplineModel
        ->select('tbl_classes.id, tbl_classes.class_name')
        ->join('tbl_classes', 'tbl_classes.id = tbl_class_disciplines.class_id')
        ->where('tbl_class_disciplines.teacher_id', $teacherId)
        ->where('tbl_classes.is_active', 1)
        ->distinct()
        ->findAll();
    
    // ✅ ADICIONAR: Boards para filtro
    $data['boards'] = $this->examBoardModel
        ->where('is_active', 1)
        ->findAll();
    
    // ✅ ADICIONAR: Períodos para filtro
    $currentYear = model('App\Models\AcademicYearModel')->getCurrent();
    if ($currentYear) {
        $data['periods'] = $this->examPeriodModel
            ->where('academic_year_id', $currentYear->id)
            ->orderBy('start_date', 'DESC')
            ->findAll();
    } else {
        $data['periods'] = [];
    }
    
    // ✅ ADICIONAR: Estatísticas para os cards
    $data['totalExams'] = count($data['exams']);
    
    $data['pendingCount'] = 0;
    $data['todayCount'] = 0;
    $data['withResultsCount'] = 0;
    
    foreach ($data['exams'] as $exam) {
        if ($exam->exam_date >= date('Y-m-d')) {
            $data['pendingCount']++;
        }
        if ($exam->exam_date == date('Y-m-d')) {
            $data['todayCount']++;
        }
        if (($exam->results_count ?? 0) > 0) {
            $data['withResultsCount']++;
        }
    }
    
    // Tipos de exame para filtro
    $data['examTypes'] = [
        'AC' => 'Avaliação Contínua (MAC)',
        'NPP' => 'Prova do Professor (NPP)',
        'NPT' => 'Prova Trimestral (NPT)',
        'E' => 'Exame Final (E)'
    ];
    
    $data['filters'] = $filters;
    $data['selectedClass'] = $filters['class'];
    $data['selectedStatus'] = $filters['status'];
    $data['selectedType'] = $filters['type'];
    
    return view('teachers/exams/index', $data);
}
    /**
 * Form to create/edit exam
 */
public function form($id = null)
{
    $data['title'] = $id ? 'Editar Exame' : 'Novo Exame';
    
    // Buscar exame se for edição
    if ($id) {
        $data['exam'] = $this->examScheduleModel
            ->select('
                tbl_exam_schedules.*,
                tbl_classes.class_name,
                tbl_disciplines.discipline_name
            ')
            ->join('tbl_classes', 'tbl_classes.id = tbl_exam_schedules.class_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id')
            ->where('tbl_exam_schedules.id', $id)
            ->first();
        
        if (!$data['exam']) {
            return redirect()->to('/teachers/exams')->with('error', 'Exame não encontrado');
        }
    } else {
        $data['exam'] = null;
    }
    
    $teacherId = $this->session->get('user_id');
    
    // Get teacher's classes (ativas)
    $data['classes'] = $this->classDisciplineModel
        ->select('tbl_classes.id, tbl_classes.class_name, tbl_classes.class_code')
        ->join('tbl_classes', 'tbl_classes.id = tbl_class_disciplines.class_id')
        ->where('tbl_class_disciplines.teacher_id', $teacherId)
        ->where('tbl_classes.is_active', 1)
        ->distinct()
        ->findAll();
    
    // Get exam boards
    $data['boards'] = $this->examBoardModel
        ->where('is_active', 1)
        ->findAll();
    
    // Get exam periods (ativos)
    $currentYear = model('App\Models\AcademicYearModel')->getCurrent();
    if ($currentYear) {
        $data['periods'] = $this->examPeriodModel
            ->where('academic_year_id', $currentYear->id)
            ->where('status', 'Planejado')
            ->orderBy('start_date', 'ASC')
            ->findAll();
    } else {
        $data['periods'] = [];
    }
    
    return view('teachers/exams/form', $data);
}
/**
 * Save exam
 */
public function save()
{
    $id = $this->request->getPost('id');
    
    // Regras de validação
    $rules = [
        'exam_name' => 'required|min_length[3]|max_length[255]',
        'class_id' => 'required|numeric',
        'discipline_id' => 'required|numeric',
        'exam_board_id' => 'required|numeric',
        'exam_period_id' => 'required|numeric',
        'exam_date' => 'required|valid_date'
    ];
    
    if (!$this->validate($rules)) {
        return redirect()->back()->withInput()
            ->with('errors', $this->validator->getErrors());
    }
    
    $data = [
        'exam_period_id' => $this->request->getPost('exam_period_id'),
        'class_id' => $this->request->getPost('class_id'),
        'discipline_id' => $this->request->getPost('discipline_id'),
        'exam_board_id' => $this->request->getPost('exam_board_id'),
        'exam_date' => $this->request->getPost('exam_date'),
        'exam_time' => $this->request->getPost('exam_time') ?: null,
        'exam_room' => $this->request->getPost('exam_room') ?: null,
        'duration_minutes' => $this->request->getPost('duration_minutes') ?: 120,
        'max_score' => $this->request->getPost('max_score') ?: 20,
        'approval_score' => 10,
        'observations' => $this->request->getPost('observations'),
        'status' => 'Agendado'
    ];
    
    // Adicionar nome do exame se não for gerado automaticamente
    if (empty($data['exam_name'])) {
        // Buscar nome da disciplina e turma
        $class = $this->classModel->find($data['class_id']);
        $discipline = $this->disciplineModel->find($data['discipline_id']);
        $board = $this->examBoardModel->find($data['exam_board_id']);
        
        $data['exam_name'] = $board->board_name . ' - ' . 
                            ($discipline->discipline_name ?? '') . ' - ' . 
                            ($class->class_name ?? '');
    } else {
        $data['exam_name'] = $this->request->getPost('exam_name');
    }
    
    $db = db_connect();
    $db->transStart();
    
    if ($id) {
        // Atualizar
        $this->examScheduleModel->update($id, $data);
        $message = 'Exame atualizado com sucesso';
    } else {
        // Inserir
        $this->examScheduleModel->insert($data);
        $message = 'Exame criado com sucesso';
    }
    
    $db->transComplete();
    
    if ($db->transStatus()) {
        return redirect()->to('/teachers/exams')->with('success', $message);
    } else {
        return redirect()->back()->withInput()
            ->with('error', 'Erro ao salvar exame');
    }
}
    
    /**
     * View exam results
     */
    public function results($examScheduleId)
    {
        $data['title'] = 'Resultados do Exame';
        
        // Buscar dados do exame (agendamento)
        $data['exam'] = $this->examScheduleModel
            ->select('
                tbl_exam_schedules.*,
                tbl_classes.class_name,
                tbl_disciplines.discipline_name,
                tbl_exam_boards.board_name,
                tbl_exam_boards.board_type,
                tbl_exam_boards.board_code,
                tbl_exam_periods.period_name
            ')
            ->join('tbl_classes', 'tbl_classes.id = tbl_exam_schedules.class_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id')
            ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exam_schedules.exam_board_id')
            ->join('tbl_exam_periods', 'tbl_exam_periods.id = tbl_exam_schedules.exam_period_id')
            ->where('tbl_exam_schedules.id', $examScheduleId)
            ->first();
        
        if (!$data['exam']) {
            return redirect()->to('/teachers/exams')->with('error', 'Exame não encontrado');
        }
        
        // Buscar resultados
        $data['results'] = $this->examResultModel
            ->select('
                tbl_exam_results.*,
                tbl_enrollments.student_id,
                tbl_users.first_name,
                tbl_users.last_name,
                tbl_students.student_number
            ')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_exam_results.enrollment_id')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->where('tbl_exam_results.exam_schedule_id', $examScheduleId)
            ->orderBy('tbl_users.first_name', 'ASC')
            ->findAll();
        
        // Estatísticas
        $data['statistics'] = $this->examResultModel->getExamStatistics($examScheduleId);
        
        // Total de alunos na turma
        $data['totalStudents'] = $this->enrollmentModel
            ->where('class_id', $data['exam']->class_id)
            ->where('status', 'Ativo')
            ->countAllResults();
        
        return view('teachers/exams/results', $data);
    }
    
    /**
     * Grade exam - show form to enter grades
     */
   /**
 * Grade exam - show form to enter grades
 */
public function grade($examScheduleId)
{
    $data['exam'] = $this->examScheduleModel
        ->select('
            tbl_exam_schedules.*,
            tbl_classes.class_name,
            tbl_disciplines.discipline_name,
            tbl_exam_boards.board_name,
            tbl_exam_boards.board_type
        ')
        ->join('tbl_classes', 'tbl_classes.id = tbl_exam_schedules.class_id')
        ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id')
        ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exam_schedules.exam_board_id')
        ->where('tbl_exam_schedules.id', $examScheduleId)
        ->first();
    
    if (!$data['exam']) {
        return redirect()->to('/teachers/exams')->with('error', 'Exame não encontrado');
    }
    
    // ✅ ADICIONAR: Definir approval_score se não existir
    if (!isset($data['exam']->approval_score) || $data['exam']->approval_score === null) {
        $data['exam']->approval_score = 10; // Valor padrão
    }
    
    // Get students in this class
    $data['students'] = $this->enrollmentModel
        ->select('tbl_enrollments.id as enrollment_id, tbl_students.id, tbl_users.first_name, tbl_users.last_name, tbl_students.student_number')
        ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
        ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
        ->where('tbl_enrollments.class_id', $data['exam']->class_id)
        ->where('tbl_enrollments.status', 'Ativo')
        ->orderBy('tbl_users.first_name', 'ASC')
        ->findAll();
    
    // Get existing results
    $results = $this->examResultModel
        ->where('exam_schedule_id', $examScheduleId)
        ->findAll();
    
    $data['results'] = [];
    foreach ($results as $result) {
        $data['results'][$result->enrollment_id] = $result;
    }
    
    // ✅ NOVO: Buscar presenças do exame
    $attendanceModel = new \App\Models\ExamAttendanceModel();
    $attendances = $attendanceModel->getByExam($examScheduleId);
    
    $data['attendances'] = [];
    foreach ($attendances as $attendance) {
        $data['attendances'][$attendance->enrollment_id] = $attendance;
    }
    
    // ✅ NOVO: Estatísticas
    $data['totalStudents'] = count($data['students']);
    $data['totalWithScores'] = count($results);
    $data['attendanceStats'] = $attendanceModel->getStats($examScheduleId);
    
    return view('teachers/exams/grade', $data);
}
    
    /**
     * Save grades
     */
    public function saveGrades()
    {
        $examScheduleId = $this->request->getPost('exam_schedule_id');
        $scores = $this->request->getPost('scores') ?? [];
        $absences = $this->request->getPost('absences') ?? [];
        
        if (!$examScheduleId || empty($scores)) {
            return redirect()->back()->with('error', 'Dados incompletos');
        }
        
        $exam = $this->examScheduleModel->find($examScheduleId);
        if (!$exam) {
            return redirect()->back()->with('error', 'Exame não encontrado');
        }
        
        $db = db_connect();
        $db->transStart();
        
        foreach ($scores as $enrollmentId => $score) {
            $data = [
                'exam_schedule_id' => $examScheduleId,
                'enrollment_id' => $enrollmentId,
                'score' => $score !== '' ? $score : null,
                'is_absent' => isset($absences[$enrollmentId]) ? 1 : 0,
                'recorded_by' => $this->session->get('user_id')
            ];
            
            // Verificar se já existe
            $existing = $this->examResultModel
                ->where('exam_schedule_id', $examScheduleId)
                ->where('enrollment_id', $enrollmentId)
                ->first();
            
            if ($existing) {
                $this->examResultModel->update($existing->id, $data);
            } else {
                $this->examResultModel->insert($data);
            }
        }
        
        $db->transComplete();
        
        if ($db->transStatus()) {
            return redirect()->to('/teachers/exams/grade/' . $examScheduleId)
                ->with('success', 'Notas salvas com sucesso');
        } else {
            return redirect()->back()->with('error', 'Erro ao salvar notas');
        }
    }
    
    /**
     * Get disciplines for a class (AJAX)
     */
    public function getDisciplines($classId = null)
    {
        if (!$classId) {
            return $this->response->setJSON([]);
        }
        
        $teacherId = $this->session->get('user_id');
        
        $disciplines = $this->classDisciplineModel
            ->select('tbl_disciplines.id, tbl_disciplines.discipline_name')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
            ->where('tbl_class_disciplines.class_id', $classId)
            ->where('tbl_class_disciplines.teacher_id', $teacherId)
            ->findAll();
        
        return $this->response->setJSON($disciplines);
    }
    
    /**
     * Upcoming exams
     */
    public function upcoming()
    {
        $data['title'] = 'Próximos Exames';
        
        $teacherId = $this->session->get('user_id');
        
        $data['exams'] = $this->examScheduleModel
            ->select('
                tbl_exam_schedules.*,
                tbl_classes.class_name,
                tbl_disciplines.discipline_name,
                tbl_exam_boards.board_name,
                tbl_exam_boards.board_type
            ')
            ->join('tbl_classes', 'tbl_classes.id = tbl_exam_schedules.class_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id')
            ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exam_schedules.exam_board_id')
            ->join('tbl_class_disciplines', 'tbl_class_disciplines.class_id = tbl_exam_schedules.class_id AND tbl_class_disciplines.discipline_id = tbl_exam_schedules.discipline_id')
            ->where('tbl_class_disciplines.teacher_id', $teacherId)
            ->where('tbl_classes.is_active', 1)  // ✅ Adicionar esta linha se quiser
            ->where('tbl_exam_schedules.exam_date >=', date('Y-m-d'))
            ->where('tbl_exam_schedules.status', 'Agendado')
            ->orderBy('tbl_exam_schedules.exam_date', 'ASC')
            ->findAll();
        
        return view('teachers/exams/upcoming', $data);
    }
    
    /**
     * Completed exams
     */
    public function completed()
    {
        $data['title'] = 'Exames Realizados';
        
        $teacherId = $this->session->get('user_id');
        
        $data['exams'] = $this->examScheduleModel
            ->select('
                tbl_exam_schedules.*,
                tbl_classes.class_name,
                tbl_disciplines.discipline_name,
                tbl_exam_boards.board_name,
                tbl_exam_boards.board_type
            ')
            ->join('tbl_classes', 'tbl_classes.id = tbl_exam_schedules.class_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id')
            ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exam_schedules.exam_board_id')
            ->join('tbl_class_disciplines', 'tbl_class_disciplines.class_id = tbl_exam_schedules.class_id AND tbl_class_disciplines.discipline_id = tbl_exam_schedules.discipline_id')
            ->where('tbl_class_disciplines.teacher_id', $teacherId)
            ->where('tbl_exam_schedules.exam_date <', date('Y-m-d'))
            ->orderBy('tbl_exam_schedules.exam_date', 'DESC')
            ->findAll();
        
        return view('teachers/exams/completed', $data);
    }
    
/**
 * Attendance for exam
 */
public function attendance($examScheduleId)
{
    $data['exam'] = $this->examScheduleModel
        ->select('
            tbl_exam_schedules.*,
            tbl_classes.class_name,
            tbl_disciplines.discipline_name
        ')
        ->join('tbl_classes', 'tbl_classes.id = tbl_exam_schedules.class_id')
        ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id')
        ->where('tbl_exam_schedules.id', $examScheduleId)
        ->first();
    
    if (!$data['exam']) {
        return redirect()->to('/teachers/exams')->with('error', 'Exame não encontrado');
    }
    
    // Get students
    $data['students'] = $this->enrollmentModel
        ->select('tbl_enrollments.id as enrollment_id, tbl_users.first_name, tbl_users.last_name, tbl_students.student_number')
        ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
        ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
        ->where('tbl_enrollments.class_id', $data['exam']->class_id)
        ->where('tbl_enrollments.status', 'Ativo')
        ->orderBy('tbl_users.first_name', 'ASC')
        ->findAll();
    
    // CORREÇÃO: Usar ExamAttendanceModel em vez de examResultModel
    $attendanceModel = new \App\Models\ExamAttendanceModel();
    $attendance = $attendanceModel->getByExam($examScheduleId);
    
    $data['attendanceMap'] = [];
    foreach ($attendance as $att) {
        $data['attendanceMap'][$att->enrollment_id] = $att;
    }
    
    return view('teachers/exams/attendance', $data);
}

/**
 * Save exam attendance
 */
public function saveAttendance($examScheduleId)
{
    $attendances = $this->request->getPost('attendance') ?? [];
    
    $attendanceModel = new \App\Models\ExamAttendanceModel();
    $recordedBy = $this->session->get('user_id');
    
    $db = db_connect();
    $db->transStart();
    
    foreach ($attendances as $enrollmentId => $att) {
        // Verificar se já existe
        $existing = $attendanceModel
            ->where('exam_schedule_id', $examScheduleId)
            ->where('enrollment_id', $enrollmentId)
            ->first();
        
        $data = [
            'exam_schedule_id' => $examScheduleId,
            'enrollment_id' => $enrollmentId,
            'attended' => isset($att['absent']) ? 0 : 1, // Se marcou "absent", attended = 0
            'check_in_time' => isset($att['absent']) ? null : date('Y-m-d H:i:s'),
            'check_in_method' => 'Manual',
            'observations' => $att['observations'] ?? null,
            'recorded_by' => $recordedBy
        ];
        
        if ($existing) {
            $attendanceModel->update($existing->id, $data);
        } else {
            $attendanceModel->insert($data);
        }
    }
    
    $db->transComplete();
    
    if ($db->transStatus()) {
        return redirect()->to('/teachers/exams/attendance/' . $examScheduleId)
            ->with('success', 'Presenças registradas com sucesso');
    } else {
        return redirect()->back()->with('error', 'Erro ao registrar presenças');
    }
}
/**
 * Update exam status
 */
public function updateStatus($id, $status)
{
    $userId = $this->session->get('user_id');
    
    $result = $this->examScheduleModel->updateStatus($id, $status, $userId);
    
    if ($result) {
        return redirect()->back()->with('success', 'Status atualizado com sucesso');
    } else {
        return redirect()->back()->with('error', 'Erro ao atualizar status');
    }
}

}