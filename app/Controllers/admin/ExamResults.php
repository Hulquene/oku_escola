<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\ExamResultModel;
use App\Models\ExamScheduleModel;
use App\Models\EnrollmentModel;
use App\Models\StudentModel;
use App\Models\ClassModel;
use App\Models\DisciplineModel;
use App\Models\SemesterModel;
use App\Models\DisciplineAverageModel;
use App\Models\SemesterResultModel;
use App\Models\AcademicHistoryModel;

class ExamResults extends BaseController
{
    protected $examResultModel;
    protected $examScheduleModel;
    protected $enrollmentModel;
    protected $studentModel;
    protected $classModel;
    protected $disciplineModel;
    protected $semesterModel;
    protected $disciplineAverageModel;
    protected $semesterResultModel;
    protected $academicHistoryModel;
    
    public function __construct()
    {
        $this->examResultModel = new ExamResultModel();
        $this->examScheduleModel = new ExamScheduleModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->studentModel = new StudentModel();
        $this->classModel = new ClassModel();
        $this->disciplineModel = new DisciplineModel();
        $this->semesterModel = new SemesterModel();
        $this->disciplineAverageModel = new DisciplineAverageModel();
        $this->semesterResultModel = new SemesterResultModel();
        $this->academicHistoryModel = new AcademicHistoryModel();
    }
    
   /**
 * List results (exam results view)
 */
public function index()
{
    $data['title'] = 'Resultados de Exames';
    
    $examId = $this->request->getGet('exam');
    $classId = $this->request->getGet('class');
    $disciplineId = $this->request->getGet('discipline');
    $semesterId = $this->request->getGet('semester') ?: 
        ($this->semesterModel->getCurrent()->id ?? null);
    
    $builder = $this->examResultModel
        ->select('
            tbl_exam_results.*,
            tbl_exam_schedules.exam_date,
            tbl_exam_schedules.exam_time,
            tbl_exam_schedules.exam_room,
            tbl_exam_boards.board_name,
            tbl_exam_boards.board_type,
            tbl_classes.class_name,
            tbl_disciplines.discipline_name,
            tbl_users.first_name,
            tbl_users.last_name,
            tbl_students.student_number,
            tbl_students.id as student_id,
            tbl_exam_periods.semester_id
        ')
        ->join('tbl_exam_schedules', 'tbl_exam_schedules.id = tbl_exam_results.exam_schedule_id')
        ->join('tbl_exam_periods', 'tbl_exam_periods.id = tbl_exam_schedules.exam_period_id') // NOVO JOIN
        ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exam_schedules.exam_board_id')
        ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_exam_results.enrollment_id')
        ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
        ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
        ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
        ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id');
    
    if ($examId) {
        $builder->where('tbl_exam_schedules.id', $examId);
    }
    
    if ($classId) {
        $builder->where('tbl_enrollments.class_id', $classId);
    }
    
    if ($disciplineId) {
        $builder->where('tbl_exam_schedules.discipline_id', $disciplineId);
    }
    
    if ($semesterId) {
        // CORREÇÃO: filtrar pelo semester_id da tabela exam_periods
        $builder->where('tbl_exam_periods.semester_id', $semesterId);
    }
    
    $data['results'] = $builder->orderBy('tbl_exam_schedules.exam_date', 'DESC')
        ->orderBy('tbl_users.first_name', 'ASC')
        ->paginate(20);
    
    $data['pager'] = $this->examResultModel->pager;
    
    // Filters
    $data['exams'] = $this->examScheduleModel
        ->select('
            tbl_exam_schedules.id, 
            tbl_exam_schedules.exam_date,
            tbl_disciplines.discipline_name
        ')
        ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id')
        ->orderBy('tbl_exam_schedules.exam_date', 'DESC')
        ->findAll(50);
    
    $data['classes'] = $this->classModel
        ->where('is_active', 1)
        ->orderBy('class_name', 'ASC')
        ->findAll();
    
    $data['disciplines'] = $this->disciplineModel
        ->where('is_active', 1)
        ->orderBy('discipline_name', 'ASC')
        ->findAll();
    
    $data['semesters'] = $this->semesterModel->getActive();
    
    $data['selectedExam'] = $examId;
    $data['selectedClass'] = $classId;
    $data['selectedDiscipline'] = $disciplineId;
    $data['selectedSemester'] = $semesterId;
    
    return view('admin/exams/results/index', $data);
}
    
    /**
     * View class results (pauta da turma)
     */
    public function classResults($classId)
    {
        $class = $this->classModel
            ->select('
                tbl_classes.*,
                tbl_grade_levels.level_name,
                tbl_academic_years.year_name
            ')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_classes.academic_year_id')
            ->find($classId);
        
        if (!$class) {
            return redirect()->to('/admin/classes')
                ->with('error', 'Turma não encontrada.');
        }
        
        $semesterId = $this->request->getGet('semester') ?: 
            ($this->semesterModel->getCurrent()->id ?? null);
        
        $semester = $this->semesterModel->find($semesterId);
        
        $data['title'] = 'Pauta da Turma - ' . $class->class_name;
        $data['class'] = $class;
        $data['semester'] = $semester;
        $data['semesters'] = $this->semesterModel->getActive();
        $data['selectedSemester'] = $semesterId;
        
        // Get all active students
        $students = $this->enrollmentModel
            ->select('
                tbl_enrollments.id as enrollment_id,
                tbl_students.id as student_id,
                tbl_students.student_number,
                tbl_users.first_name,
                tbl_users.last_name
            ')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->where('tbl_enrollments.class_id', $classId)
            ->where('tbl_enrollments.status', 'Ativo')
            ->orderBy('tbl_users.first_name', 'ASC')
            ->findAll();
        
        // Get all disciplines for this class
        $classDisciplineModel = new \App\Models\ClassDisciplineModel();
        $disciplines = $classDisciplineModel
            ->select('
                tbl_disciplines.id,
                tbl_disciplines.discipline_name,
                tbl_disciplines.discipline_code
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
            ->where('tbl_class_disciplines.class_id', $classId)
            ->where('tbl_class_disciplines.semester_id', $semesterId)
            ->where('tbl_class_disciplines.is_active', 1)
            ->orderBy('tbl_disciplines.discipline_name', 'ASC')
            ->findAll();
        
        // Get semester results for all students
        $results = [];
        $semesterResults = [];
        
        foreach ($students as $student) {
            $semesterResult = $this->semesterResultModel
                ->where('enrollment_id', $student->enrollment_id)
                ->where('semester_id', $semesterId)
                ->first();
            
            if ($semesterResult) {
                $semesterResults[$student->enrollment_id] = $semesterResult;
            }
            
            // Get discipline averages for this student
            $averages = $this->disciplineAverageModel
                ->where('enrollment_id', $student->enrollment_id)
                ->where('semester_id', $semesterId)
                ->findAll();
            
            $studentData = [
                'enrollment_id' => $student->enrollment_id,
                'student_name' => $student->first_name . ' ' . $student->last_name,
                'student_number' => $student->student_number,
                'averages' => []
            ];
            
            foreach ($averages as $avg) {
                $studentData['averages'][$avg->discipline_id] = [
                    'score' => $avg->final_score,
                    'status' => $avg->status
                ];
            }
            
            $results[] = $studentData;
        }
        
        $data['students'] = $results;
        $data['disciplines'] = $disciplines;
        $data['semesterResults'] = $semesterResults;
        
        return view('admin/exams/results/class_results', $data);
    }
    
    /**
     * Generate report card for student
     */
    public function reportCard($studentId)
    {
        $student = $this->studentModel->getWithUser($studentId);
        
        if (!$student) {
            return redirect()->to('/admin/students')->with('error', 'Aluno não encontrado');
        }
        
        $semesterId = $this->request->getGet('semester') ?: 
            ($this->semesterModel->getCurrent()->id ?? null);
        
        $data['title'] = 'Boletim de Notas - ' . $student->first_name . ' ' . $student->last_name;
        $data['student'] = $student;
        $data['semester'] = $this->semesterModel->find($semesterId);
        $data['semesters'] = $this->semesterModel->getActive();
        $data['selectedSemester'] = $semesterId;
        
        // Get current enrollment
        $enrollment = $this->enrollmentModel
            ->select('
                tbl_enrollments.*, 
                tbl_classes.class_name, 
                tbl_classes.class_code, 
                tbl_academic_years.year_name
            ')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_enrollments.academic_year_id')
            ->where('tbl_enrollments.student_id', $studentId)
            ->where('tbl_enrollments.status', 'Ativo')
            ->orderBy('tbl_enrollments.id', 'DESC')
            ->first();
        
        $data['enrollment'] = $enrollment;
        
        if ($enrollment && $semesterId) {
            // Get discipline averages
            $data['disciplineAverages'] = $this->disciplineAverageModel
                ->select('
                    tbl_discipline_averages.*,
                    tbl_disciplines.discipline_name,
                    tbl_disciplines.discipline_code
                ')
                ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_discipline_averages.discipline_id')
                ->where('tbl_discipline_averages.enrollment_id', $enrollment->id)
                ->where('tbl_discipline_averages.semester_id', $semesterId)
                ->orderBy('tbl_disciplines.discipline_name', 'ASC')
                ->findAll();
            
            // Get semester result
            $data['semesterResult'] = $this->semesterResultModel
                ->where('enrollment_id', $enrollment->id)
                ->where('semester_id', $semesterId)
                ->first();
            
            // Calculate statistics
            $total = 0;
            $count = 0;
            $approved = 0;
            $appeal = 0;
            $failed = 0;
            
            foreach ($data['disciplineAverages'] as $avg) {
                if ($avg->final_score) {
                    $total += $avg->final_score;
                    $count++;
                    
                    switch ($avg->status) {
                        case 'Aprovado': $approved++; break;
                        case 'Recurso': $appeal++; break;
                        case 'Reprovado': $failed++; break;
                    }
                }
            }
            
            $data['average'] = $count > 0 ? round($total / $count, 2) : 0;
            $data['stats'] = [
                'total' => $count,
                'approved' => $approved,
                'appeal' => $appeal,
                'failed' => $failed
            ];
        }
        
        return view('admin/exams/results/report_card', $data);
    }
    
    /**
     * Generate academic transcript
     */
    public function transcript($studentId)
    {
        $student = $this->studentModel->getWithUser($studentId);
        
        if (!$student) {
            return redirect()->to('/admin/students')->with('error', 'Aluno não encontrado');
        }
        
        $data['title'] = 'Histórico Escolar - ' . $student->first_name . ' ' . $student->last_name;
        $data['student'] = $student;
        
        // Get all enrollments
        $enrollments = $this->enrollmentModel
            ->select('
                tbl_enrollments.*,
                tbl_classes.class_name,
                tbl_classes.class_code,
                tbl_academic_years.year_name,
                tbl_academic_years.start_date,
                tbl_academic_years.end_date
            ')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_enrollments.academic_year_id')
            ->where('tbl_enrollments.student_id', $studentId)
            ->where('tbl_enrollments.status !=', 'Pendente')
            ->orderBy('tbl_academic_years.start_date', 'DESC')
            ->findAll();
        
        $data['enrollments'] = $enrollments;
        
        // Get all semesters
        $semesters = $this->semesterModel
            ->orderBy('start_date', 'ASC')
            ->findAll();
        
        $data['semesters'] = $semesters;
        
        // Build transcript data
        $transcript = [];
        foreach ($enrollments as $enrollment) {
            $yearData = [
                'year' => $enrollment,
                'semesters' => []
            ];
            
            foreach ($semesters as $semester) {
                if ($semester->academic_year_id == $enrollment->academic_year_id) {
                    $averages = $this->disciplineAverageModel
                        ->select('
                            tbl_discipline_averages.*,
                            tbl_disciplines.discipline_name
                        ')
                        ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_discipline_averages.discipline_id')
                        ->where('tbl_discipline_averages.enrollment_id', $enrollment->id)
                        ->where('tbl_discipline_averages.semester_id', $semester->id)
                        ->orderBy('tbl_disciplines.discipline_name', 'ASC')
                        ->findAll();
                    
                    $semesterResult = $this->semesterResultModel
                        ->where('enrollment_id', $enrollment->id)
                        ->where('semester_id', $semester->id)
                        ->first();
                    
                    $yearData['semesters'][$semester->id] = [
                        'semester' => $semester,
                        'averages' => $averages,
                        'result' => $semesterResult
                    ];
                }
            }
            
            // Get yearly result from academic history
            $yearlyResult = $this->academicHistoryModel
                ->where('student_id', $studentId)
                ->where('academic_year_id', $enrollment->academic_year_id)
                ->first();
            
            $yearData['yearly_result'] = $yearlyResult;
            
            $transcript[] = $yearData;
        }
        
        $data['transcript'] = $transcript;
        
        return view('admin/exams/results/transcript', $data);
    }
    
  /**
 * Save results (bulk) - Updated for new structure
 */
public function save()
{
    $examScheduleId = $this->request->getPost('exam_schedule_id');
    $results = $this->request->getPost('results') ?? [];
    
    if (!$examScheduleId || empty($results)) {
        return redirect()->back()->with('error', 'Dados incompletos');
    }
    
    // Buscar o exame com JOIN para obter o semester_id via exam_periods
    $exam = $this->examScheduleModel
        ->select('
            tbl_exam_schedules.*,
            tbl_exam_periods.semester_id,
            tbl_exam_schedules.discipline_id
        ')
        ->join('tbl_exam_periods', 'tbl_exam_periods.id = tbl_exam_schedules.exam_period_id')
        ->find($examScheduleId);
    
    if (!$exam) {
        return redirect()->back()->with('error', 'Exame não encontrado');
    }
    
    // Delete existing results
    $this->examResultModel->where('exam_schedule_id', $examScheduleId)->delete();
    
    // Insert new results
    $data = [];
    foreach ($results as $enrollmentId => $score) {
        if ($score !== '') {
            $data[] = [
                'exam_schedule_id' => $examScheduleId,
                'enrollment_id' => $enrollmentId,
                'score' => $score,
                'is_absent' => 0,
                'recorded_by' => session()->get('user_id')
            ];
        }
    }
    
    if (!empty($data)) {
        $result = $this->examResultModel->insertBatch($data);
        
        if ($result) {
            // Recalculate averages for affected students
            $semesterId = $exam->semester_id; // AGORA VEM DO JOIN
            $disciplineId = $exam->discipline_id;
            
            foreach ($data as $item) {
                $this->disciplineAverageModel->calculate(
                    $item['enrollment_id'],
                    $disciplineId,
                    $semesterId,
                    session()->get('user_id')
                );
            }
            
            return redirect()->to('/admin/exams/results?exam=' . $examScheduleId)
                ->with('success', 'Resultados guardados com sucesso');
        }
    }
    
    return redirect()->back()->with('error', 'Erro ao guardar resultados');
}
    /**
     * Export results to CSV
     */
    public function export($examScheduleId)
    {
        $exam = $this->examScheduleModel
            ->select('
                tbl_exam_schedules.*,
                tbl_disciplines.discipline_name,
                tbl_classes.class_name
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_exam_schedules.class_id')
            ->find($examScheduleId);
        
        if (!$exam) {
            return redirect()->back()->with('error', 'Exame não encontrado');
        }
        
        $results = $this->examResultModel
            ->select('
                tbl_students.student_number,
                tbl_users.first_name,
                tbl_users.last_name,
                tbl_exam_results.score
            ')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_exam_results.enrollment_id')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->where('tbl_exam_results.exam_schedule_id', $examScheduleId)
            ->orderBy('tbl_users.first_name', 'ASC')
            ->findAll();
        
        $filename = 'resultados_' . $exam->discipline_name . '_' . date('Ymd') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Nº Matrícula', 'Nome', 'Nota'], ';');
        
        foreach ($results as $row) {
            fputcsv($output, [
                $row->student_number,
                $row->first_name . ' ' . $row->last_name,
                $row->score
            ], ';');
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Print class results (pauta)
     */
    public function printClass($classId, $semesterId)
    {
        $class = $this->classModel
            ->select('
                tbl_classes.*,
                tbl_grade_levels.level_name,
                tbl_academic_years.year_name
            ')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_classes.academic_year_id')
            ->find($classId);
        
        $semester = $this->semesterModel->find($semesterId);
        
        $data['class'] = $class;
        $data['semester'] = $semester;
        
        // Get data (similar to classResults method)
        $students = $this->enrollmentModel
            ->select('
                tbl_enrollments.id as enrollment_id,
                tbl_students.student_number,
                tbl_users.first_name,
                tbl_users.last_name
            ')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->where('tbl_enrollments.class_id', $classId)
            ->where('tbl_enrollments.status', 'Ativo')
            ->orderBy('tbl_users.first_name', 'ASC')
            ->findAll();
        
        $disciplines = $this->disciplineModel
            ->select('
                tbl_disciplines.id,
                tbl_disciplines.discipline_name
            ')
            ->join('tbl_class_disciplines', 'tbl_class_disciplines.discipline_id = tbl_disciplines.id')
            ->where('tbl_class_disciplines.class_id', $classId)
            ->where('tbl_class_disciplines.semester_id', $semesterId)
            ->orderBy('tbl_disciplines.discipline_name', 'ASC')
            ->findAll();
        
        $results = [];
        foreach ($students as $student) {
            $averages = $this->disciplineAverageModel
                ->where('enrollment_id', $student->enrollment_id)
                ->where('semester_id', $semesterId)
                ->findAll();
            
            $studentData = [
                'student_number' => $student->student_number,
                'student_name' => $student->first_name . ' ' . $student->last_name,
                'averages' => []
            ];
            
            foreach ($averages as $avg) {
                $studentData['averages'][$avg->discipline_id] = $avg->final_score;
            }
            
            $results[] = $studentData;
        }
        
        $data['students'] = $results;
        $data['disciplines'] = $disciplines;
        
        return view('admin/exams/results/print_class', $data);
    }
}