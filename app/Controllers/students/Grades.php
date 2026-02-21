<?php
namespace App\Controllers\students;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use App\Models\EnrollmentModel;
use App\Models\ExamResultModel;
use App\Models\SemesterModel;
use App\Models\DisciplineAverageModel;

class Grades extends BaseController
{
    protected $studentModel;
    protected $enrollmentModel;
    protected $examResultModel;
    protected $semesterModel;
    protected $disciplineAverageModel;
    
    public function __construct()
    {
        helper(['auth', 'student', 'student_new']);
        
        $this->studentModel = new StudentModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->examResultModel = new ExamResultModel();
        $this->semesterModel = new SemesterModel();
        $this->disciplineAverageModel = new DisciplineAverageModel();
    }
    
    /**
     * Minhas notas (todas)
     */
    public function index()
    {
        $data['title'] = 'Minhas Notas';
        
        $studentId = getStudentIdFromUser();
        $enrollment = getStudentCurrentEnrollment($studentId);
        
        if (!$enrollment) {
            return redirect()->to('/students/dashboard')
                ->with('error', 'Nenhuma matrícula ativa encontrada');
        }
        
        $semesterId = $this->request->getGet('semester') ?: 
            ($this->semesterModel->where('is_current', 1)->first()->id ?? null);
        
        // Buscar notas agrupadas por disciplina
        $data['disciplines'] = getStudentGradesByDiscipline($enrollment->id, $semesterId);
        
        // Buscar médias por disciplina (resultados finais)
        $data['averages'] = $this->disciplineAverageModel
            ->select('
                tbl_discipline_averages.*,
                tbl_disciplines.discipline_name
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_discipline_averages.discipline_id')
            ->where('tbl_discipline_averages.enrollment_id', $enrollment->id)
            ->where('tbl_discipline_averages.semester_id', $semesterId)
            ->findAll();
        
        // Calcular média geral
        $total = 0;
        $count = 0;
        foreach ($data['averages'] as $avg) {
            if ($avg->final_score) {
                $total += $avg->final_score;
                $count++;
            }
        }
        $data['overallAverage'] = $count > 0 ? round($total / $count, 1) : 0;
        
        $data['semesters'] = $this->semesterModel
            ->orderBy('start_date', 'DESC')
            ->findAll();
        $data['selectedSemester'] = $semesterId;
        
        return view('students/grades/index', $data);
    }
    
    /**
     * Notas por disciplina específica
     */
    public function bySubject($disciplineId)
    {
        $data['title'] = 'Notas por Disciplina';
        
        $studentId = getStudentIdFromUser();
        $enrollment = getStudentCurrentEnrollment($studentId);
        
        if (!$enrollment) {
            return redirect()->to('/students/dashboard')
                ->with('error', 'Nenhuma matrícula ativa encontrada');
        }
        
        // Buscar informações da disciplina
        $disciplineModel = new \App\Models\DisciplineModel();
        $data['discipline'] = $disciplineModel->find($disciplineId);
        
        if (!$data['discipline']) {
            return redirect()->to('/students/grades')
                ->with('error', 'Disciplina não encontrada');
        }
        
        // Buscar todas as notas da disciplina
        $data['grades'] = $this->examResultModel
            ->select('
                tbl_exam_results.*,
                tbl_exam_schedules.exam_date,
                tbl_exam_boards.board_name,
                tbl_exam_boards.board_type,
                tbl_exam_periods.period_name,
                DATE_FORMAT(tbl_exam_schedules.exam_date, "%d/%m/%Y") as formatted_date
            ')
            ->join('tbl_exam_schedules', 'tbl_exam_schedules.id = tbl_exam_results.exam_schedule_id')
            ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exam_schedules.exam_board_id')
            ->join('tbl_exam_periods', 'tbl_exam_periods.id = tbl_exam_schedules.exam_period_id')
            ->where('tbl_exam_results.enrollment_id', $enrollment->id)
            ->where('tbl_exam_schedules.discipline_id', $disciplineId)
            ->orderBy('tbl_exam_schedules.exam_date', 'DESC')
            ->findAll();
        
        // Buscar média final da disciplina
        $data['average'] = $this->disciplineAverageModel
            ->where('enrollment_id', $enrollment->id)
            ->where('discipline_id', $disciplineId)
            ->orderBy('semester_id', 'DESC')
            ->first();
        
        return view('students/grades/subject', $data);
    }
    /**
     * Boletim de notas
     */
    /**
 * Boletim de notas - Versão com helper
 */
public function reportCard()
{
    $data['title'] = 'Boletim de Notas';
    
    $studentId = getStudentIdFromUser();
    $enrollment = getStudentCurrentEnrollment($studentId);
    
    if (!$enrollment) {
        return redirect()->to('/students/dashboard')
            ->with('error', 'Nenhuma matrícula ativa encontrada');
    }
    
    // CORREÇÃO: Garantir que o semesterId nunca seja nulo
    $semesterId = $this->request->getGet('semester');
    
    if (!$semesterId) {
        // Buscar o semestre atual
        $currentSemester = $this->semesterModel->where('is_current', 1)->first();
        $semesterId = $currentSemester ? $currentSemester->id : null;
        
        // Se ainda assim não encontrar, pegar o primeiro semestre disponível
        if (!$semesterId) {
            $firstSemester = $this->semesterModel
                ->orderBy('start_date', 'DESC')
                ->first();
            $semesterId = $firstSemester ? $firstSemester->id : null;
        }
    }
    
    // Se ainda não tiver semesterId, mostrar erro
    if (!$semesterId) {
        return redirect()->to('/students/grades')
            ->with('error', 'Nenhum período letivo encontrado. Entre em contato com a secretaria.');
    }
    
    // Buscar relatório existente
    $reportCardModel = new \App\Models\ReportCardModel();
    $reportCard = $reportCardModel
        ->where('enrollment_id', $enrollment->id)
        ->where('semester_id', $semesterId)
        ->first();
    
    // Função auxiliar para obter ID
    $getId = function($item) {
        if (is_array($item)) {
            return $item['id'];
        }
        return $item->id;
    };
    
    if (!$reportCard) {
        // Gerar novo relatório
        $reportCardId = $reportCardModel->generateForStudent($enrollment->id, $semesterId);
        $data['reportCard'] = $reportCardModel->getWithDetails($reportCardId);
    } else {
        $data['reportCard'] = $reportCardModel->getWithDetails($getId($reportCard));
    }
    
    // Buscar médias por disciplina
    $data['averages'] = $this->disciplineAverageModel
        ->select('
            tbl_discipline_averages.*,
            tbl_disciplines.discipline_name
        ')
        ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_discipline_averages.discipline_id')
        ->where('tbl_discipline_averages.enrollment_id', $enrollment->id)
        ->where('tbl_discipline_averages.semester_id', $semesterId)
        ->findAll();
    
    $data['semesters'] = $this->semesterModel
        ->orderBy('start_date', 'DESC')
        ->findAll();
    $data['selectedSemester'] = $semesterId;
    
    return view('students/grades/report_card', $data);
}
}