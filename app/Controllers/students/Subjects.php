<?php
// app/Controllers/students/Subjects.php

namespace App\Controllers\students;

use App\Controllers\BaseController;
use App\Models\ClassDisciplineModel;
use App\Models\EnrollmentModel;
use App\Models\DisciplineModel;
use App\Models\FinalGradeModel;
use App\Models\AttendanceModel;
use App\Models\ContinuousAssessmentModel;
use App\Models\SemesterModel;
use App\Models\AcademicYearModel;
use App\Models\GradeLevelModel;
use App\Models\ExamModel;
use App\Models\ExamResultModel;
use App\Models\ClassModel;

class Subjects extends BaseController
{
    protected $classDisciplineModel;
    protected $enrollmentModel;
    protected $disciplineModel;
    protected $finalGradeModel;
    protected $attendanceModel;
    protected $continuousAssessmentModel;
    protected $semesterModel;
    protected $academicYearModel;
    protected $gradeLevelModel;
    protected $examModel;
    protected $examResultModel;
    protected $classModel;
    
    public function __construct()
    {
        helper(['auth', 'student']);
        
        $this->classDisciplineModel = new ClassDisciplineModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->disciplineModel = new DisciplineModel();
        $this->finalGradeModel = new FinalGradeModel();
        $this->attendanceModel = new AttendanceModel();
        $this->continuousAssessmentModel = new ContinuousAssessmentModel();
        $this->semesterModel = new SemesterModel();
        $this->academicYearModel = new AcademicYearModel();
        $this->gradeLevelModel = new GradeLevelModel();
        $this->examModel = new ExamModel();
        $this->examResultModel = new ExamResultModel();
        $this->classModel = new ClassModel();
    }
    
    /**
     * Lista todas as disciplinas do aluno com opções de filtro
     */
    public function index()
    {
        $data['title'] = 'Minhas Disciplinas';
        
        $studentId = getStudentIdFromUser();
        
        // Buscar matrícula ATIVA do aluno
        $enrollment = $this->enrollmentModel->getCurrentForStudent($studentId);
        
        if (!$enrollment) {
            return redirect()->to('/students/dashboard')->with('error', 'Nenhuma matrícula ativa encontrada');
        }
        
        // Filtros
        $data['currentFilters'] = [
            'status' => $this->request->getGet('status')
        ];
        
        // Buscar disciplinas APENAS da turma do aluno
        $data['subjects'] = $this->getStudentSubjects(
            $enrollment->class_id, 
            $enrollment->id, 
            $studentId, 
            $data['currentFilters']
        );
        
        // Buscar informações da turma atual
        $data['currentClass'] = $this->classModel
            ->select('tbl_classes.*, tbl_grade_levels.level_name, tbl_academic_years.year_name')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_classes.academic_year_id')
            ->where('tbl_classes.id', $enrollment->class_id)
            ->first();
        
        // Estatísticas das disciplinas
        $data['stats'] = $this->getSubjectsStats($data['subjects']);
        
        return view('students/subjects/index', $data);
    }
    
    /**
     * Detalhes de uma disciplina específica
     */
    public function details($disciplineId)
    {
        $data['title'] = 'Detalhes da Disciplina';
        
        $studentId = getStudentIdFromUser();
        
        // Buscar matrícula ATIVA do aluno
        $enrollment = $this->enrollmentModel->getCurrentForStudent($studentId);
        
        if (!$enrollment) {
            return redirect()->to('/students/dashboard')->with('error', 'Nenhuma matrícula ativa encontrada');
        }
        
        // Verificar se a disciplina realmente pertence à turma do aluno
        $classDiscipline = $this->classDisciplineModel
            ->where('class_id', $enrollment->class_id)
            ->where('discipline_id', $disciplineId)
            ->where('is_active', 1)
            ->first();
        
        if (!$classDiscipline) {
            return redirect()->to('/students/subjects')->with('error', 'Disciplina não encontrada na sua turma');
        }
        
        // Buscar detalhes completos da disciplina
        $data['subject'] = $this->disciplineModel
            ->select('
                tbl_disciplines.*,
                tbl_class_disciplines.teacher_id,
                tbl_class_disciplines.workload_hours as class_workload,
                tbl_class_disciplines.semester_id
            ')
            ->join('tbl_class_disciplines', 'tbl_class_disciplines.discipline_id = tbl_disciplines.id')
            ->where('tbl_class_disciplines.class_id', $enrollment->class_id)
            ->where('tbl_disciplines.id', $disciplineId)
            ->first();
        
        if (!$data['subject']) {
            return redirect()->to('/students/subjects')->with('error', 'Disciplina não encontrada');
        }
        
        // Buscar informações do professor
        if ($data['subject']->teacher_id) {
            $teacherModel = new \App\Models\UserModel();
            $teacher = $teacherModel->select('first_name, last_name, email, phone')
                ->find($data['subject']->teacher_id);
            $data['teacher'] = $teacher;
            $data['teacher_name'] = $teacher ? $teacher->first_name . ' ' . $teacher->last_name : 'Não atribuído';
        } else {
            $data['teacher'] = null;
            $data['teacher_name'] = 'Não atribuído';
        }
        
        // Buscar semestre ativo
        $currentSemester = $this->semesterModel->where('is_active', 1)->first();
        $semesterId = $currentSemester ? $currentSemester->id : null;
        
        // Buscar TODAS as avaliações contínuas da disciplina
        $data['assessments'] = $this->continuousAssessmentModel
            ->select('
                tbl_continuous_assessment.*,
                DATE_FORMAT(assessment_date, "%d/%m/%Y") as formatted_date
            ')
            ->where('enrollment_id', $enrollment->id)
            ->where('discipline_id', $disciplineId)
            ->orderBy('assessment_date', 'DESC')
            ->findAll();
        
        // Calcular média das avaliações contínuas
        if (!empty($data['assessments'])) {
            $total = 0;
            foreach ($data['assessments'] as $assessment) {
                $total += $assessment->score;
            }
            $data['averageGrade'] = round($total / count($data['assessments']), 1);
            $data['averageGradeFormatted'] = number_format($data['averageGrade'], 1, ',', '.');
        } else {
            $data['averageGrade'] = null;
            $data['averageGradeFormatted'] = '-';
        }
        
        // Buscar TODOS os resultados de exames
        $data['examResults'] = $this->examResultModel
            ->select('
                tbl_exam_results.*,
                tbl_exams.exam_name,
                tbl_exams.exam_date,
                tbl_exam_boards.board_name,
                tbl_exam_boards.board_type,
                tbl_exam_boards.weight,
                DATE_FORMAT(tbl_exams.exam_date, "%d/%m/%Y") as formatted_date
            ')
            ->join('tbl_exams', 'tbl_exams.id = tbl_exam_results.exam_id')
            ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exams.exam_board_id')
            ->where('tbl_exam_results.enrollment_id', $enrollment->id)
            ->where('tbl_exams.discipline_id', $disciplineId)
            ->orderBy('tbl_exams.exam_date', 'DESC')
            ->findAll();
        
        // Buscar nota final se disponível (todos os semestres)
        $data['finalGrades'] = $this->finalGradeModel
            ->select('final_score, status, exam_score, observations, semester_id')
            ->where('enrollment_id', $enrollment->id)
            ->where('discipline_id', $disciplineId)
            ->orderBy('semester_id', 'DESC')
            ->findAll();
        
        // Para compatibilidade, manter a nota final do semestre atual
        $data['finalGrade'] = null;
        if (!empty($data['finalGrades'])) {
            // Se houver nota final do semestre atual, usa ela
            foreach ($data['finalGrades'] as $fg) {
                if ($fg->semester_id == $semesterId) {
                    $data['finalGrade'] = $fg;
                    break;
                }
            }
            // Se não, usa a mais recente
            if (!$data['finalGrade'] && isset($data['finalGrades'][0])) {
                $data['finalGrade'] = $data['finalGrades'][0];
            }
        }
        
        $data['finalGradeFormatted'] = $data['finalGrade'] ? number_format($data['finalGrade']->final_score, 1, ',', '.') : '-';
        
        // Buscar presenças da disciplina
        $attendanceRecords = $this->attendanceModel
            ->select('
                tbl_attendance.*,
                DATE_FORMAT(attendance_date, "%d/%m/%Y") as formatted_date
            ')
            ->where('enrollment_id', $enrollment->id)
            ->where('discipline_id', $disciplineId)
            ->orderBy('attendance_date', 'DESC')
            ->findAll();
        
        // Adicionar badge_color baseado no status
        foreach ($attendanceRecords as $record) {
            switch ($record->status) {
                case 'Presente':
                    $record->badge_color = 'success';
                    break;
                case 'Atrasado':
                    $record->badge_color = 'warning';
                    break;
                case 'Falta Justificada':
                    $record->badge_color = 'info';
                    break;
                default:
                    $record->badge_color = 'danger';
            }
        }
        
        $data['attendance'] = $attendanceRecords;
        
        // Calcular estatísticas de presença
        $totalAttendance = count($data['attendance']);
        if ($totalAttendance > 0) {
            $presentCount = 0;
            foreach ($data['attendance'] as $a) {
                if (in_array($a->status, ['Presente', 'Atrasado', 'Falta Justificada'])) {
                    $presentCount++;
                }
            }
            $data['attendancePercentage'] = round(($presentCount / $totalAttendance) * 100, 1);
            $data['attendanceStats'] = [
                'total' => $totalAttendance,
                'present' => $presentCount,
                'absent' => $totalAttendance - $presentCount
            ];
        } else {
            $data['attendancePercentage'] = 0;
            $data['attendanceStats'] = ['total' => 0, 'present' => 0, 'absent' => 0];
        }
        
        // Buscar próximos exames (não realizados)
        $data['upcomingExams'] = $this->examModel
            ->select('
                tbl_exams.*,
                tbl_exam_boards.board_name,
                tbl_exam_boards.board_type,
                tbl_exam_boards.weight,
                DATE_FORMAT(exam_date, "%d/%m/%Y") as formatted_date,
                DATE_FORMAT(exam_time, "%H:%i") as formatted_time
            ')
            ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exams.exam_board_id')
            ->where('tbl_exams.class_id', $enrollment->class_id)
            ->where('tbl_exams.discipline_id', $disciplineId)
            ->where('tbl_exams.exam_date >=', date('Y-m-d'))
            ->orderBy('tbl_exams.exam_date', 'ASC')
            ->orderBy('tbl_exams.exam_time', 'ASC')
            ->findAll();
        
        // Estatísticas adicionais
        $data['totalAssessments'] = count($data['assessments']);
        $data['totalExams'] = count($data['examResults']);
        $data['totalPresences'] = $totalAttendance;
        
        return view('students/subjects/details', $data);
    }
    
    /**
     * Busca disciplinas APENAS da turma do aluno
     */
    private function getStudentSubjects($classId, $enrollmentId, $studentId, $filters = [])
    {
        // Buscar todas as disciplinas da turma
        $subjects = $this->classDisciplineModel
            ->select('
                tbl_class_disciplines.*,
                tbl_disciplines.discipline_name,
                tbl_disciplines.discipline_code,
                tbl_disciplines.discipline_type,
                tbl_disciplines.workload_hours,
                tbl_disciplines.min_grade,
                tbl_disciplines.max_grade,
                tbl_disciplines.approval_grade,
                tbl_users.first_name as teacher_first_name,
                tbl_users.last_name as teacher_last_name,
                CONCAT(tbl_users.first_name, " ", tbl_users.last_name) as teacher_name,
                tbl_classes.class_name,
                tbl_classes.class_code,
                tbl_grade_levels.level_name as grade_level_name,
                tbl_academic_years.year_name as academic_year_name,
                tbl_semesters.semester_name
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_class_disciplines.class_id')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_classes.academic_year_id')
            ->join('tbl_users', 'tbl_users.id = tbl_class_disciplines.teacher_id', 'left')
            ->join('tbl_semesters', 'tbl_semesters.id = tbl_class_disciplines.semester_id', 'left')
            ->where('tbl_class_disciplines.class_id', $classId)
            ->where('tbl_class_disciplines.is_active', 1)
            ->orderBy('tbl_disciplines.discipline_name', 'ASC')
            ->findAll();
        
        if (empty($subjects)) {
            return [];
        }
        
        // Buscar semestre ativo
        $currentSemester = $this->semesterModel->where('is_active', 1)->first();
        $semesterId = $currentSemester ? $currentSemester->id : null;
        
        // Adicionar informações específicas do aluno para cada disciplina
        foreach ($subjects as $subject) {
            // Buscar TODAS as avaliações contínuas
            $assessments = $this->continuousAssessmentModel
                ->where('enrollment_id', $enrollmentId)
                ->where('discipline_id', $subject->discipline_id)
                ->findAll();
            
            $subject->total_assessments = count($assessments);
            
            // Calcular média das avaliações contínuas
            if ($subject->total_assessments > 0) {
                $total = 0;
                foreach ($assessments as $a) {
                    $total += $a->score;
                }
                $subject->continuous_average = round($total / $subject->total_assessments, 1);
            } else {
                $subject->continuous_average = null;
            }
            
            // Buscar TODOS os resultados de exames
            $examResults = $this->examResultModel
                ->select('tbl_exam_results.*')
                ->join('tbl_exams', 'tbl_exams.id = tbl_exam_results.exam_id')
                ->where('tbl_exam_results.enrollment_id', $enrollmentId)
                ->where('tbl_exams.discipline_id', $subject->discipline_id)
                ->findAll();
            
            $subject->total_exams = count($examResults);
            
            // Calcular média dos exames
            if ($subject->total_exams > 0) {
                $total = 0;
                foreach ($examResults as $e) {
                    $total += $e->score;
                }
                $subject->exams_average = round($total / $subject->total_exams, 1);
            } else {
                $subject->exams_average = null;
            }
            
            // Buscar nota final (mais recente)
            $finalGrade = $this->finalGradeModel
                ->select('final_score')
                ->where('enrollment_id', $enrollmentId)
                ->where('discipline_id', $subject->discipline_id)
                ->orderBy('semester_id', 'DESC')
                ->first();
            
            $subject->final_grade = $finalGrade ? $finalGrade->final_score : null;
            $subject->final_grade_formatted = $finalGrade ? number_format($finalGrade->final_score, 1, ',', '.') : '--';
            
            // Buscar percentual de presença
            $totalAttendance = $this->attendanceModel
                ->where('enrollment_id', $enrollmentId)
                ->where('discipline_id', $subject->discipline_id)
                ->countAllResults();
            
            if ($totalAttendance > 0) {
                $presentAttendance = $this->attendanceModel
                    ->where('enrollment_id', $enrollmentId)
                    ->where('discipline_id', $subject->discipline_id)
                    ->whereIn('status', ['Presente', 'Atrasado', 'Falta Justificada'])
                    ->countAllResults();
                
                $subject->attendance_percentage = round(($presentAttendance / $totalAttendance) * 100, 1);
            } else {
                $subject->attendance_percentage = 0;
            }
            
            // Determinar status da disciplina baseado na nota final ou na média
            if ($subject->final_grade) {
                $subject->status = $subject->final_grade >= $subject->approval_grade ? 'Aprovado' : 'Reprovado';
                $subject->status_color = $subject->final_grade >= $subject->approval_grade ? 'success' : 'danger';
            } elseif ($subject->continuous_average || $subject->exams_average) {
                $subject->status = 'Em curso';
                $subject->status_color = 'warning';
            } else {
                $subject->status = 'Sem avaliações';
                $subject->status_color = 'secondary';
            }
            
            // Adicionar informação de workload
            $subject->workload = $subject->class_workload ?? $subject->workload_hours ?? 0;
        }
        
        // Aplicar filtros se existirem
        if (!empty($filters['status'])) {
            $subjects = array_filter($subjects, function($subject) use ($filters) {
                return $subject->status == $filters['status'];
            });
        }
        
        return array_values($subjects);
    }
    
    /**
     * Calcula estatísticas das disciplinas
     */
    private function getSubjectsStats($subjects)
    {
        $total = count($subjects);
        $approved = 0;
        $failed = 0;
        $inProgress = 0;
        $noAssessments = 0;
        $totalWorkload = 0;
        $totalAttendance = 0;
        $totalAssessments = 0;
        $totalExams = 0;
        
        foreach ($subjects as $subject) {
            if ($subject->status == 'Aprovado') {
                $approved++;
            } elseif ($subject->status == 'Reprovado') {
                $failed++;
            } elseif ($subject->status == 'Em curso') {
                $inProgress++;
            } else {
                $noAssessments++;
            }
            
            $totalWorkload += $subject->workload ?? 0;
            $totalAttendance += $subject->attendance_percentage ?? 0;
            $totalAssessments += $subject->total_assessments ?? 0;
            $totalExams += $subject->total_exams ?? 0;
        }
        
        return [
            'total' => $total,
            'approved' => $approved,
            'failed' => $failed,
            'in_progress' => $inProgress,
            'no_assessments' => $noAssessments,
            'total_workload' => $totalWorkload,
            'average_attendance' => $total > 0 ? round($totalAttendance / $total, 1) : 0,
            'approval_rate' => $total > 0 ? round(($approved / $total) * 100, 1) : 0,
            'total_assessments' => $totalAssessments,
            'total_exams' => $totalExams
        ];
    }
}