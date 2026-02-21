<?php
namespace App\Controllers\students;

use App\Controllers\BaseController;
use App\Models\ClassDisciplineModel;
use App\Models\EnrollmentModel;
use App\Models\DisciplineModel;
use App\Models\ExamResultModel;
use App\Models\AttendanceModel;
use App\Models\SemesterModel;
use App\Models\DisciplineAverageModel;

class Subjects extends BaseController
{
    protected $classDisciplineModel;
    protected $enrollmentModel;
    protected $disciplineModel;
    protected $examResultModel;
    protected $attendanceModel;
    protected $semesterModel;
    protected $disciplineAverageModel;
    
    public function __construct()
    {
        helper(['auth', 'student', 'student_new']);
        
        $this->classDisciplineModel = new ClassDisciplineModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->disciplineModel = new DisciplineModel();
        $this->examResultModel = new ExamResultModel();
        $this->attendanceModel = new AttendanceModel();
        $this->semesterModel = new SemesterModel();
        $this->disciplineAverageModel = new DisciplineAverageModel();
    }
    
    /**
     * Lista todas as disciplinas do aluno
     */
    public function index()
    {
        $data['title'] = 'Minhas Disciplinas';
        
        $studentId = getStudentIdFromUser();
        $enrollment = getStudentCurrentEnrollment($studentId);
        
        if (!$enrollment) {
            return redirect()->to('/students/dashboard')
                ->with('error', 'Nenhuma matrícula ativa encontrada');
        }
        
        // Buscar todas as disciplinas da turma
        $data['subjects'] = $this->getStudentSubjects($enrollment->id, $enrollment->class_id);
        
        // Estatísticas
        $data['stats'] = $this->calculateStats($data['subjects']);
        
        return view('students/subjects/index', $data);
    }
    
    /**
     * Detalhes de uma disciplina específica
     */
    public function details($disciplineId)
    {
        $data['title'] = 'Detalhes da Disciplina';
        
        $studentId = getStudentIdFromUser();
        $enrollment = getStudentCurrentEnrollment($studentId);
        
        if (!$enrollment) {
            return redirect()->to('/students/dashboard')
                ->with('error', 'Nenhuma matrícula ativa encontrada');
        }
        
        // Verificar se disciplina pertence à turma
        $classDiscipline = $this->classDisciplineModel
            ->where('class_id', $enrollment->class_id)
            ->where('discipline_id', $disciplineId)
            ->where('is_active', 1)
            ->first();
        
        if (!$classDiscipline) {
            return redirect()->to('/students/subjects')
                ->with('error', 'Disciplina não encontrada na sua turma');
        }
        
        // Informações da disciplina
        $data['discipline'] = $this->disciplineModel->find($disciplineId);
        
        // Informações do professor
        if ($classDiscipline->teacher_id) {
            $teacherModel = new \App\Models\UserModel();
            $teacher = $teacherModel->find($classDiscipline->teacher_id);
            $data['teacher'] = $teacher;
        }
        
        // Notas da disciplina
        $data['grades'] = $this->examResultModel
            ->select('
                tbl_exam_results.*,
                tbl_exam_schedules.exam_date,
                tbl_exam_boards.board_name,
                tbl_exam_boards.board_type,
                DATE_FORMAT(tbl_exam_schedules.exam_date, "%d/%m/%Y") as formatted_date
            ')
            ->join('tbl_exam_schedules', 'tbl_exam_schedules.id = tbl_exam_results.exam_schedule_id')
            ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exam_schedules.exam_board_id')
            ->where('tbl_exam_results.enrollment_id', $enrollment->id)
            ->where('tbl_exam_schedules.discipline_id', $disciplineId)
            ->orderBy('tbl_exam_schedules.exam_date', 'DESC')
            ->findAll();
        
        // Média final
        $data['average'] = $this->disciplineAverageModel
            ->where('enrollment_id', $enrollment->id)
            ->where('discipline_id', $disciplineId)
            ->orderBy('semester_id', 'DESC')
            ->first();
        
        // Presenças
        $attendanceRecords = $this->attendanceModel
            ->select('
                tbl_attendance.*,
                DATE_FORMAT(attendance_date, "%d/%m/%Y") as formatted_date
            ')
            ->where('enrollment_id', $enrollment->id)
            ->where('discipline_id', $disciplineId)
            ->orderBy('attendance_date', 'DESC')
            ->findAll();
        
        // Adicionar classes CSS para os status
        foreach ($attendanceRecords as $record) {
            switch ($record->status) {
                case 'Presente':
                    $record->badge_class = 'success';
                    break;
                case 'Atrasado':
                    $record->badge_class = 'warning';
                    break;
                case 'Falta Justificada':
                    $record->badge_class = 'info';
                    break;
                default:
                    $record->badge_class = 'danger';
            }
        }
        
        $data['attendance'] = $attendanceRecords;
        
        // Estatísticas de presença
        $total = count($attendanceRecords);
        $present = 0;
        foreach ($attendanceRecords as $a) {
            if (in_array($a->status, ['Presente', 'Atrasado', 'Falta Justificada'])) {
                $present++;
            }
        }
        $data['attendanceStats'] = [
            'total' => $total,
            'present' => $present,
            'absent' => $total - $present,
            'percentage' => $total > 0 ? round(($present / $total) * 100, 1) : 0
        ];
        
        return view('students/subjects/details', $data);
    }
    
    /**
     * Busca disciplinas com informações completas
     */
    private function getStudentSubjects($enrollmentId, $classId)
    {
        $subjects = $this->classDisciplineModel
            ->select('
                tbl_class_disciplines.*,
                tbl_disciplines.discipline_name,
                tbl_disciplines.discipline_code,
                tbl_disciplines.discipline_type,
                tbl_disciplines.workload_hours,
                tbl_disciplines.approval_grade,
                CONCAT(tbl_users.first_name, " ", tbl_users.last_name) as teacher_name
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
            ->join('tbl_users', 'tbl_users.id = tbl_class_disciplines.teacher_id', 'left')
            ->where('tbl_class_disciplines.class_id', $classId)
            ->where('tbl_class_disciplines.is_active', 1)
            ->findAll();
        
        foreach ($subjects as $subject) {
            // Notas da disciplina
            $grades = $this->examResultModel
                ->select('score')
                ->join('tbl_exam_schedules', 'tbl_exam_schedules.id = tbl_exam_results.exam_schedule_id')
                ->where('tbl_exam_results.enrollment_id', $enrollmentId)
                ->where('tbl_exam_schedules.discipline_id', $subject->discipline_id)
                ->findAll();
            
            // Média das notas
            if (!empty($grades)) {
                $total = 0;
                foreach ($grades as $g) {
                    $total += $g->score;
                }
                $subject->average = round($total / count($grades), 1);
            } else {
                $subject->average = null;
            }
            
            // Nota final
            $final = $this->disciplineAverageModel
                ->where('enrollment_id', $enrollmentId)
                ->where('discipline_id', $subject->discipline_id)
                ->orderBy('semester_id', 'DESC')
                ->first();
            
            $subject->final_grade = $final ? $final->final_score : null;
            
            // Status
            if ($subject->final_grade) {
                $subject->status = $subject->final_grade >= $subject->approval_grade ? 'Aprovado' : 'Reprovado';
                $subject->status_color = $subject->final_grade >= $subject->approval_grade ? 'success' : 'danger';
            } elseif ($subject->average) {
                $subject->status = 'Em andamento';
                $subject->status_color = 'warning';
            } else {
                $subject->status = 'Sem avaliações';
                $subject->status_color = 'secondary';
            }
            
            // Estatísticas de presença
            $attendance = $this->attendanceModel
                ->select('COUNT(*) as total, SUM(CASE WHEN status IN ("Presente", "Atrasado", "Falta Justificada") THEN 1 ELSE 0 END) as present')
                ->where('enrollment_id', $enrollmentId)
                ->where('discipline_id', $subject->discipline_id)
                ->first();
            
            $subject->attendance_total = $attendance->total ?? 0;
            $subject->attendance_present = $attendance->present ?? 0;
            $subject->attendance_percentage = ($subject->attendance_total > 0) 
                ? round(($subject->attendance_present / $subject->attendance_total) * 100, 1) 
                : 0;
        }
        
        return $subjects;
    }
    
    /**
     * Calcula estatísticas gerais
     */
    private function calculateStats($subjects)
    {
        $total = count($subjects);
        $approved = 0;
        $failed = 0;
        $inProgress = 0;
        $noAssessments = 0;
        $totalAttendance = 0;
        
        foreach ($subjects as $subject) {
            if ($subject->status == 'Aprovado') $approved++;
            elseif ($subject->status == 'Reprovado') $failed++;
            elseif ($subject->status == 'Em andamento') $inProgress++;
            else $noAssessments++;
            
            $totalAttendance += $subject->attendance_percentage;
        }
        
        return [
            'total' => $total,
            'approved' => $approved,
            'failed' => $failed,
            'in_progress' => $inProgress,
            'no_assessments' => $noAssessments,
            'average_attendance' => $total > 0 ? round($totalAttendance / $total, 1) : 0,
            'approval_rate' => $total > 0 ? round(($approved / $total) * 100, 1) : 0
        ];
    }
}