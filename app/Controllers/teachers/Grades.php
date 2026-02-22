<?php

namespace App\Controllers\teachers;

use App\Controllers\BaseController;
use App\Models\ExamResultModel;
use App\Models\ExamBoardModel;
use App\Models\ExamScheduleModel;  // NOVO
use App\Models\ExamPeriodModel;    // NOVO
use App\Models\ClassDisciplineModel;
use App\Models\EnrollmentModel;
use App\Models\DisciplineModel;
use App\Models\SemesterModel;

class Grades extends BaseController
{
    protected $examResultModel;
    protected $examBoardModel;
    protected $examScheduleModel;   // NOVO
    protected $examPeriodModel;      // NOVO
    protected $classDisciplineModel;
    protected $enrollmentModel;
    protected $disciplineModel;
    protected $semesterModel;
    
    public function __construct()
    {
        $this->examResultModel = new ExamResultModel();
        $this->examBoardModel = new ExamBoardModel();
        $this->examScheduleModel = new ExamScheduleModel();   // NOVO
        $this->examPeriodModel = new ExamPeriodModel();       // NOVO
        $this->classDisciplineModel = new ClassDisciplineModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->disciplineModel = new DisciplineModel();
        $this->semesterModel = new SemesterModel();
    }
    
 public function index()
{
    $data['title'] = 'Lançar Notas';
    
    $teacherId = $this->session->get('user_id');
    
    // Get classes for filter
    $data['classes'] = $this->classDisciplineModel
        ->select('tbl_classes.id, tbl_classes.class_name, tbl_classes.class_code')
        ->join('tbl_classes', 'tbl_classes.id = tbl_class_disciplines.class_id')
        ->where('tbl_class_disciplines.teacher_id', $teacherId)
        ->where('tbl_classes.is_active', 1)
        ->distinct()
        ->findAll();
    
    $classId = $this->request->getGet('class');
    $disciplineId = $this->request->getGet('discipline');
    
    $data['selectedClass'] = $classId;
    $data['selectedDiscipline'] = $disciplineId;
    
    if ($classId && $disciplineId) {
        // Get students
        $data['students'] = $this->enrollmentModel
            ->select('tbl_enrollments.id as enrollment_id, tbl_students.id, tbl_users.first_name, tbl_users.last_name, tbl_students.student_number')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->where('tbl_enrollments.class_id', $classId)
            ->where('tbl_enrollments.status', 'Ativo')
            ->orderBy('tbl_users.first_name', 'ASC')
            ->findAll();
        
        // DEBUG: Quantos alunos encontrou?
        log_message('debug', "Alunos encontrados: " . count($data['students']));
        
        // Get current semester
        $currentSemester = $this->semesterModel->getCurrent();
        $data['currentSemester'] = $currentSemester;
        
        // DEBUG: Semestre atual
        log_message('debug', "Semestre atual: " . ($currentSemester ? $currentSemester->id : 'NULL'));
        
        // Buscar todas as avaliações AC para esta disciplina
        foreach ($data['students'] as $student) {
            // Buscar todos os exam_schedules do tipo AC para esta disciplina
            $acSchedules = $this->examScheduleModel
                ->select('tbl_exam_schedules.*')
                ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exam_schedules.exam_board_id')
                ->join('tbl_exam_periods', 'tbl_exam_periods.id = tbl_exam_schedules.exam_period_id')
                ->where('tbl_exam_schedules.class_id', $classId)
                ->where('tbl_exam_schedules.discipline_id', $disciplineId)
                ->where('tbl_exam_boards.board_code', 'AC')
                ->where('tbl_exam_periods.semester_id', $currentSemester->id ?? null)
                ->orderBy('tbl_exam_schedules.exam_date', 'ASC')
                ->findAll();
            
            // DEBUG: Quantos agendamentos AC encontrou?
            log_message('debug', "Agendamentos AC para turma {$classId}, disciplina {$disciplineId}: " . count($acSchedules));
            
            $student->assessments = [];
            $seq = 1;
            foreach ($acSchedules as $schedule) {
                $result = $this->examResultModel
                    ->where('exam_schedule_id', $schedule->id)
                    ->where('enrollment_id', $student->enrollment_id)
                    ->first();
                
                $student->assessments[$seq] = $result;
                $seq++;
            }
        }
        
        // Número de avaliações contínuas (configurável)
        $data['acCount'] = 6; // Até 6 ACs por trimestre
    }
    
    return view('teachers/grades/index', $data);
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
            ->select('tbl_disciplines.id, tbl_disciplines.discipline_name, tbl_disciplines.discipline_code')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
            ->where('tbl_class_disciplines.class_id', $classId)
            ->where('tbl_class_disciplines.teacher_id', $teacherId)
            ->orderBy('tbl_disciplines.discipline_name', 'ASC')
            ->findAll();
        
        return $this->response->setJSON($disciplines);
    }
    
    /**
     * Save continuous assessments (ACs)
     */
    public function save()
    {
        $classId = $this->request->getPost('class_id');
        $disciplineId = $this->request->getPost('discipline_id');
        $semesterId = $this->request->getPost('semester_id');
        
        if (!$classId || !$disciplineId || !$semesterId) {
            return redirect()->back()->with('error', 'Dados incompletos');
        }
        
        // Buscar o board AC
        $acBoard = $this->examBoardModel->where('board_code', 'AC')->first();
        if (!$acBoard) {
            return redirect()->back()->with('error', 'Tipo de avaliação "AC" não configurado');
        }
        
        // Buscar o período atual para este semestre
        $currentPeriod = $this->examPeriodModel
            ->where('semester_id', $semesterId)
            ->where('status', 'Planejado')
            ->first();
        
        if (!$currentPeriod) {
            return redirect()->back()->with('error', 'Nenhum período de exames ativo para este semestre');
        }
        
        // Receber notas por sequência (AC1, AC2, AC3...)
        $acScores = $this->request->getPost('ac') ?? [];
        
        $db = db_connect();
        $db->transStart();
        
        foreach ($acScores as $sequence => $enrollmentScores) {
            foreach ($enrollmentScores as $enrollmentId => $score) {
                if ($score !== '') {
                    // Buscar ou criar agendamento para esta AC
                    $schedule = $this->examScheduleModel
                        ->where('exam_period_id', $currentPeriod->id)
                        ->where('class_id', $classId)
                        ->where('discipline_id', $disciplineId)
                        ->where('exam_board_id', $acBoard->id)
                        ->first();
                    
                    if (!$schedule) {
                        // Criar novo agendamento
                        $scheduleId = $this->examScheduleModel->insert([
                            'exam_period_id' => $currentPeriod->id,
                            'class_id' => $classId,
                            'discipline_id' => $disciplineId,
                            'exam_board_id' => $acBoard->id,
                            'exam_date' => date('Y-m-d'),
                            'status' => 'Agendado'
                        ]);
                    } else {
                        $scheduleId = $schedule->id;
                    }
                    
                    // Buscar o agendamento para obter max_score
                    $scheduleData = $this->examScheduleModel->find($scheduleId);
                    
                    // Calcular percentual
                    $scorePercentage = $scheduleData->max_score > 0 
                        ? ($score / $scheduleData->max_score) * 100 
                        : 0;
                    
                    $data = [
                        'enrollment_id' => $enrollmentId,
                        'exam_schedule_id' => $scheduleId,
                        'score' => $score,
                        'score_percentage' => $scorePercentage,
                        'recorded_by' => $this->session->get('user_id')
                    ];
                    
                    // Verificar se já existe
                    $existing = $this->examResultModel
                        ->where('exam_schedule_id', $scheduleId)
                        ->where('enrollment_id', $enrollmentId)
                        ->first();
                    
                    if ($existing) {
                        $this->examResultModel->update($existing->id, $data);
                    } else {
                        $this->examResultModel->insert($data);
                    }
                }
            }
        }
        
        $db->transComplete();
        
        if ($db->transStatus()) {
            return redirect()->to('/teachers/grades?class=' . $classId . '&discipline=' . $disciplineId)
                ->with('success', 'Avaliações contínuas salvas com sucesso');
        } else {
            return redirect()->back()->with('error', 'Erro ao salvar avaliações');
        }
    }
    
    /**
     * Report for a specific class/discipline
     */
    public function report($classId)
    {
        $data['title'] = 'Relatório de Notas';
        
        $teacherId = $this->session->get('user_id');
        $disciplineId = $this->request->getGet('discipline');
        
        if (!$disciplineId) {
            return redirect()->to('/teachers/grades')->with('error', 'Selecione uma disciplina');
        }
        
        // Get class info
        $classModel = new \App\Models\ClassModel();
        $data['class'] = $classModel->find($classId);
        
        // Get discipline info
        $data['discipline'] = $this->disciplineModel->find($disciplineId);
        
        // Get students
        $data['students'] = $this->enrollmentModel
            ->select('tbl_enrollments.id as enrollment_id, tbl_students.id, tbl_users.first_name, tbl_users.last_name, tbl_students.student_number')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->where('tbl_enrollments.class_id', $classId)
            ->where('tbl_enrollments.status', 'Ativo')
            ->orderBy('tbl_users.first_name', 'ASC')
            ->findAll();
        
        // Get current semester
        $currentSemester = $this->semesterModel->getCurrent();
        $data['currentSemester'] = $currentSemester;
        
        // Buscar todos os agendamentos AC para esta disciplina
        $acSchedules = $this->examScheduleModel
            ->select('tbl_exam_schedules.*')
            ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exam_schedules.exam_board_id')
            ->join('tbl_exam_periods', 'tbl_exam_periods.id = tbl_exam_schedules.exam_period_id')
            ->where('tbl_exam_schedules.class_id', $classId)
            ->where('tbl_exam_schedules.discipline_id', $disciplineId)
            ->where('tbl_exam_boards.board_code', 'AC')
            ->where('tbl_exam_periods.semester_id', $currentSemester->id ?? null)
            ->orderBy('tbl_exam_schedules.exam_date', 'ASC')
            ->findAll();
        
        // Para cada aluno, buscar notas
        foreach ($data['students'] as $student) {
            $scores = [];
            $total = 0;
            $count = 0;
            
            foreach ($acSchedules as $index => $schedule) {
                $result = $this->examResultModel
                    ->where('exam_schedule_id', $schedule->id)
                    ->where('enrollment_id', $student->enrollment_id)
                    ->first();
                
                $seq = $index + 1;
                $scores["ac{$seq}"] = $result ? $result->score : '-';
                
                if ($result && $result->score) {
                    $total += $result->score;
                    $count++;
                }
            }
            
            $student->ac_scores = $scores;
            $student->ac_average = $count > 0 ? round($total / $count, 1) : 0;
        }
        
        return view('teachers/grades/report', $data);
    }
    
    /**
     * Select class for report
     */
    public function selectReport()
    {
        $data['title'] = 'Selecionar Relatório';
        
        $teacherId = $this->session->get('user_id');
        
        // Get teacher's classes
        $data['classes'] = $this->classDisciplineModel
            ->select('tbl_classes.id, tbl_classes.class_name, tbl_classes.class_code')
            ->join('tbl_classes', 'tbl_classes.id = tbl_class_disciplines.class_id')
            ->where('tbl_class_disciplines.teacher_id', $teacherId)
            ->where('tbl_classes.is_active', 1)
            ->distinct()
            ->findAll();
        
        return view('teachers/grades/select_report', $data);
    }
}