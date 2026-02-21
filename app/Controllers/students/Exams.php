<?php
namespace App\Controllers\students;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use App\Models\EnrollmentModel;
use App\Models\ExamScheduleModel;
use App\Models\ExamResultModel;
use App\Models\SemesterModel;

class Exams extends BaseController
{
    protected $studentModel;
    protected $enrollmentModel;
    protected $examScheduleModel;
    protected $examResultModel;
    protected $semesterModel;
    
    public function __construct()
    {
        helper(['auth', 'student', 'student_new']);
        
        $this->studentModel = new StudentModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->examScheduleModel = new ExamScheduleModel();
        $this->examResultModel = new ExamResultModel();
        $this->semesterModel = new SemesterModel();
    }
    
    /**
     * Lista de exames (próximos e passados)
     */
    public function index()
    {
        $data['title'] = 'Meus Exames';
        
        $studentId = getStudentIdFromUser();
        $enrollment = getStudentCurrentEnrollment($studentId);
        
        if (!$enrollment) {
            return redirect()->to('/students/dashboard')
                ->with('error', 'Nenhuma matrícula ativa encontrada');
        }
        
        // Próximos exames
        $data['upcomingExams'] = $this->examScheduleModel
            ->select('
                tbl_exam_schedules.*,
                tbl_disciplines.discipline_name,
                tbl_exam_boards.board_name,
                tbl_exam_boards.board_type,
                tbl_exam_periods.period_name
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id')
            ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exam_schedules.exam_board_id')
            ->join('tbl_exam_periods', 'tbl_exam_periods.id = tbl_exam_schedules.exam_period_id')
            ->where('tbl_exam_schedules.class_id', $enrollment->class_id)
            ->where('tbl_exam_schedules.exam_date >=', date('Y-m-d'))
            ->orderBy('tbl_exam_schedules.exam_date', 'ASC')
            ->orderBy('tbl_exam_schedules.exam_time', 'ASC')
            ->findAll();
        
        // Exames passados
        $data['pastExams'] = $this->examScheduleModel
            ->select('
                tbl_exam_schedules.*,
                tbl_disciplines.discipline_name,
                tbl_exam_boards.board_name,
                tbl_exam_boards.board_type,
                tbl_exam_periods.period_name
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id')
            ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exam_schedules.exam_board_id')
            ->join('tbl_exam_periods', 'tbl_exam_periods.id = tbl_exam_schedules.exam_period_id')
            ->where('tbl_exam_schedules.class_id', $enrollment->class_id)
            ->where('tbl_exam_schedules.exam_date <', date('Y-m-d'))
            ->orderBy('tbl_exam_schedules.exam_date', 'DESC')
            ->findAll();
        
        // Buscar resultados para exames passados
        $scheduleIds = array_column($data['pastExams'], 'id');
        if (!empty($scheduleIds)) {
            $results = $this->examResultModel
                ->whereIn('exam_schedule_id', $scheduleIds)
                ->where('enrollment_id', $enrollment->id)
                ->findAll();
            
            $data['results'] = [];
            foreach ($results as $result) {
                $data['results'][$result->exam_schedule_id] = $result;
            }
        } else {
            $data['results'] = [];
        }
        
        return view('students/exams/index', $data);
    }
    
    /**
     * Calendário de exames
     */
    public function schedule()
    {
        $data['title'] = 'Calendário de Exames';
        
        $studentId = getStudentIdFromUser();
        $enrollment = getStudentCurrentEnrollment($studentId);
        
        if (!$enrollment) {
            return redirect()->to('/students/dashboard')
                ->with('error', 'Nenhuma matrícula ativa encontrada');
        }
        
        $month = $this->request->getGet('month') ?: date('m');
        $year = $this->request->getGet('year') ?: date('Y');
        
        $startDate = "$year-$month-01";
        $endDate = date('Y-m-t', strtotime($startDate));
        
        // Buscar exames do mês
        $data['exams'] = $this->examScheduleModel
            ->select('
                tbl_exam_schedules.*,
                tbl_disciplines.discipline_name,
                tbl_exam_boards.board_name,
                tbl_exam_boards.board_type,
                DATE_FORMAT(exam_date, "%d/%m/%Y") as formatted_date,
                DATE_FORMAT(exam_time, "%H:%i") as formatted_time
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id')
            ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exam_schedules.exam_board_id')
            ->where('tbl_exam_schedules.class_id', $enrollment->class_id)
            ->where('tbl_exam_schedules.exam_date >=', $startDate)
            ->where('tbl_exam_schedules.exam_date <=', $endDate)
            ->orderBy('tbl_exam_schedules.exam_date', 'ASC')
            ->orderBy('tbl_exam_schedules.exam_time', 'ASC')
            ->findAll();
        
        $data['month'] = $month;
        $data['year'] = $year;
        $data['months'] = [
            '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', '04' => 'Abril',
            '05' => 'Maio', '06' => 'Junho', '07' => 'Julho', '08' => 'Agosto',
            '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
        ];
        
        return view('students/exams/schedule', $data);
    }
    
    /**
     * Resultados de exames
     */
    public function results()
    {
        $data['title'] = 'Meus Resultados';
        
        $studentId = getStudentIdFromUser();
        $enrollment = getStudentCurrentEnrollment($studentId);
        
        if (!$enrollment) {
            return redirect()->to('/students/dashboard')
                ->with('error', 'Nenhuma matrícula ativa encontrada');
        }
        
        $semesterId = $this->request->getGet('semester') ?: 
            ($this->semesterModel->where('is_current', 1)->first()->id ?? null);
        
        // Buscar resultados agrupados por disciplina
        $data['disciplines'] = getStudentGradesByDiscipline($enrollment->id, $semesterId);
        
        // Calcular estatísticas gerais
        $totalGrades = 0;
        $countGrades = 0;
        $approvedCount = 0;
        
        foreach ($data['disciplines'] as $discipline) {
            foreach ($discipline['grades'] as $grade) {
                $totalGrades += $grade->score;
                $countGrades++;
                if ($grade->score >= ($grade->approval_score ?? 10)) {
                    $approvedCount++;
                }
            }
        }
        
        $data['stats'] = [
            'total_exams' => $countGrades,
            'average' => $countGrades > 0 ? round($totalGrades / $countGrades, 1) : 0,
            'approved' => $approvedCount,
            'approval_rate' => $countGrades > 0 ? round(($approvedCount / $countGrades) * 100, 1) : 0
        ];
        
        $data['semesters'] = $this->semesterModel
            ->orderBy('start_date', 'DESC')
            ->findAll();
        $data['selectedSemester'] = $semesterId;
        
        return view('students/exams/results', $data);
    }
    
    /**
     * Detalhes de um exame específico
     */
    public function view($scheduleId)
    {
        $data['title'] = 'Detalhes do Exame';
        
        $studentId = getStudentIdFromUser();
        $enrollment = getStudentCurrentEnrollment($studentId);
        
        if (!$enrollment) {
            return redirect()->to('/students/dashboard')
                ->with('error', 'Nenhuma matrícula ativa encontrada');
        }
        
        // Buscar detalhes do exame
        $data['exam'] = $this->examScheduleModel
            ->select('
                tbl_exam_schedules.*,
                tbl_classes.class_name,
                tbl_disciplines.discipline_name,
                tbl_disciplines.discipline_code,
                tbl_exam_boards.board_name,
                tbl_exam_boards.board_type,
                tbl_exam_periods.period_name,
                DATE_FORMAT(exam_date, "%d/%m/%Y") as formatted_date,
                DATE_FORMAT(exam_time, "%H:%i") as formatted_time
            ')
            ->join('tbl_classes', 'tbl_classes.id = tbl_exam_schedules.class_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id')
            ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exam_schedules.exam_board_id')
            ->join('tbl_exam_periods', 'tbl_exam_periods.id = tbl_exam_schedules.exam_period_id')
            ->where('tbl_exam_schedules.id', $scheduleId)
            ->first();
        
        if (!$data['exam']) {
            return redirect()->to('/students/exams')
                ->with('error', 'Exame não encontrado');
        }
        
        // Verificar se o exame é da turma do aluno
        if ($data['exam']->class_id != $enrollment->class_id) {
            return redirect()->to('/students/exams')
                ->with('error', 'Exame não pertence à sua turma');
        }
        
        // Buscar resultado se existir
        $data['result'] = $this->examResultModel
            ->where('exam_schedule_id', $scheduleId)
            ->where('enrollment_id', $enrollment->id)
            ->first();
        
        return view('students/exams/view', $data);
    }
}