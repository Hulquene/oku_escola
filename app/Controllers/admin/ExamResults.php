<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\ExamResultModel;
use App\Models\ExamModel;
use App\Models\EnrollmentModel;
use App\Models\StudentModel;
use App\Models\ClassModel;
use App\Models\DisciplineModel;
use App\Models\SemesterModel;
use App\Models\FinalGradeModel;

class ExamResults extends BaseController
{
    protected $examResultModel;
    protected $examModel;
    protected $enrollmentModel;
    protected $studentModel;
    protected $classModel;
    protected $disciplineModel;
    protected $semesterModel;
    protected $finalGradeModel;
    
    public function __construct()
    {
        $this->examResultModel = new ExamResultModel();
        $this->examModel = new ExamModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->studentModel = new StudentModel();
        $this->classModel = new ClassModel();
        $this->disciplineModel = new DisciplineModel();
        $this->semesterModel = new SemesterModel();
        $this->finalGradeModel = new FinalGradeModel();
    }
    
    /**
     * List results
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
                tbl_exams.exam_name,
                tbl_exams.exam_date,
                tbl_classes.class_name,
                tbl_disciplines.discipline_name,
                tbl_users.first_name,
                tbl_users.last_name,
                tbl_students.student_number
            ')
            ->join('tbl_exams', 'tbl_exams.id = tbl_exam_results.exam_id')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_exam_results.enrollment_id')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exams.discipline_id');
        
        if ($examId) {
            $builder->where('tbl_exam_results.exam_id', $examId);
        }
        
        if ($classId) {
            $builder->where('tbl_enrollments.class_id', $classId);
        }
        
        if ($disciplineId) {
            $builder->where('tbl_exams.discipline_id', $disciplineId);
        }
        
        if ($semesterId) {
            $builder->where('tbl_exams.semester_id', $semesterId);
        }
        
        $data['results'] = $builder->orderBy('tbl_exams.exam_date', 'DESC')
            ->orderBy('tbl_users.first_name', 'ASC')
            ->paginate(20);
        
        $data['pager'] = $this->examResultModel->pager;
        
        // Filters
        $data['exams'] = $this->examModel
            ->select('tbl_exams.id, tbl_exams.exam_name, tbl_exams.exam_date')
            ->orderBy('tbl_exams.exam_date', 'DESC')
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
     * Save results (bulk)
     */
    public function save()
    {
        $examId = $this->request->getPost('exam_id');
        $results = $this->request->getPost('results') ?? [];
        
        if (!$examId || empty($results)) {
            return redirect()->back()->with('error', 'Dados incompletos');
        }
        
        $exam = $this->examModel->find($examId);
        if (!$exam) {
            return redirect()->back()->with('error', 'Exame não encontrado');
        }
        
        $data = [];
        foreach ($results as $enrollmentId => $score) {
            if ($score !== '') {
                $data[] = [
                    'exam_id' => $examId,
                    'enrollment_id' => $enrollmentId,
                    'score' => $score,
                    'score_percentage' => ($score / $exam->max_score) * 100,
                    'recorded_by' => $this->session->get('user_id')
                ];
            }
        }
        
        $result = $this->examResultModel->saveBulk($data);
        
        if ($result) {
            return redirect()->to('/admin/exams/results?exam=' . $examId)
                ->with('success', 'Resultados guardados com sucesso');
        } else {
            return redirect()->back()->with('error', 'Erro ao guardar resultados');
        }
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
            ->select('tbl_enrollments.*, tbl_classes.class_name, tbl_classes.class_code, tbl_academic_years.year_name')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_enrollments.academic_year_id')
            ->where('tbl_enrollments.student_id', $studentId)
            ->where('tbl_enrollments.status', 'Ativo')
            ->orderBy('tbl_enrollments.id', 'DESC')
            ->first();
        
        $data['enrollment'] = $enrollment;
        
        if ($enrollment && $semesterId) {
            // Get exam results
            $data['examResults'] = $this->examResultModel
                ->select('
                    tbl_exam_results.*,
                    tbl_exams.exam_name,
                    tbl_exams.exam_date,
                    tbl_exam_boards.board_name,
                    tbl_exam_boards.board_type,
                    tbl_exam_boards.weight,
                    tbl_disciplines.discipline_name
                ')
                ->join('tbl_exams', 'tbl_exams.id = tbl_exam_results.exam_id')
                ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exams.exam_board_id')
                ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exams.discipline_id')
                ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_exam_results.enrollment_id')
                ->where('tbl_enrollments.id', $enrollment->id)
                ->where('tbl_exams.semester_id', $semesterId)
                ->orderBy('tbl_disciplines.discipline_name', 'ASC')
                ->orderBy('tbl_exams.exam_date', 'ASC')
                ->findAll();
            
            // Get final grades
            $data['finalGrades'] = $this->finalGradeModel
                ->select('
                    tbl_final_grades.*,
                    tbl_disciplines.discipline_name
                ')
                ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_final_grades.discipline_id')
                ->where('tbl_final_grades.enrollment_id', $enrollment->id)
                ->where('tbl_final_grades.semester_id', $semesterId)
                ->orderBy('tbl_disciplines.discipline_name', 'ASC')
                ->findAll();
            
            // Calculate averages
            $total = 0;
            $count = 0;
            foreach ($data['finalGrades'] as $grade) {
                if ($grade->final_score) {
                    $total += $grade->final_score;
                    $count++;
                }
            }
            $data['average'] = $count > 0 ? round($total / $count, 2) : 0;
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
                    $grades = $this->finalGradeModel
                        ->select('
                            tbl_final_grades.*,
                            tbl_disciplines.discipline_name
                        ')
                        ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_final_grades.discipline_id')
                        ->where('tbl_final_grades.enrollment_id', $enrollment->id)
                        ->where('tbl_final_grades.semester_id', $semester->id)
                        ->orderBy('tbl_disciplines.discipline_name', 'ASC')
                        ->findAll();
                    
                    $yearData['semesters'][$semester->id] = [
                        'semester' => $semester,
                        'grades' => $grades
                    ];
                }
            }
            
            $transcript[] = $yearData;
        }
        
        $data['transcript'] = $transcript;
        
        return view('admin/exams/results/transcript', $data);
    }
    
    /**
     * Export results to CSV
     */
    public function export($examId)
    {
        $exam = $this->examModel->getWithDetails($examId);
        
        if (!$exam) {
            return redirect()->back()->with('error', 'Exame não encontrado');
        }
        
        $results = $this->examResultModel
            ->select('
                tbl_students.student_number,
                tbl_users.first_name,
                tbl_users.last_name,
                tbl_exam_results.score,
                tbl_exam_results.score_percentage,
                tbl_exam_results.grade
            ')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_exam_results.enrollment_id')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->where('tbl_exam_results.exam_id', $examId)
            ->orderBy('tbl_users.first_name', 'ASC')
            ->findAll();
        
        $filename = 'resultados_' . $exam->exam_name . '_' . date('Ymd') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        fputcsv($output, ['Nº Matrícula', 'Nome', 'Nota', 'Percentagem', 'Classificação'], ';');
        
        foreach ($results as $row) {
            fputcsv($output, [
                $row->student_number,
                $row->first_name . ' ' . $row->last_name,
                $row->score,
                number_format($row->score_percentage, 1) . '%',
                $row->grade
            ], ';');
        }
        
        fclose($output);
        exit;
    }
}