<?php

namespace App\Controllers\teachers;

use App\Controllers\BaseController;
use App\Models\ExamModel;
use App\Models\ExamBoardModel;
use App\Models\ClassModel;
use App\Models\DisciplineModel;
use App\Models\EnrollmentModel;
use App\Models\ExamResultModel;
use App\Models\ClassDisciplineModel;
use App\Models\SemesterModel;

class Exams extends BaseController
{
    protected $examModel;
    protected $examBoardModel;
    protected $classModel;
    protected $disciplineModel;
    protected $enrollmentModel;
    protected $examResultModel;
    protected $classDisciplineModel;
    protected $semesterModel;
    
    public function __construct()
    {
        $this->examModel = new ExamModel();
        $this->examBoardModel = new ExamBoardModel();
        $this->classModel = new ClassModel();
        $this->disciplineModel = new DisciplineModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->examResultModel = new ExamResultModel();
        $this->classDisciplineModel = new ClassDisciplineModel();
        $this->semesterModel = new SemesterModel();
    }
    
    /**
     * List exams for the teacher
     */
    public function index()
    {
        $data['title'] = 'Meus Exames';
        
        $teacherId = $this->session->get('user_id');
        
        $filters = [
            'class' => $this->request->getGet('class'),
            'status' => $this->request->getGet('status')
        ];
        
        $builder = $this->examModel
            ->select('tbl_exams.*, tbl_classes.class_name, tbl_disciplines.discipline_name, tbl_exam_boards.board_name')
            ->join('tbl_classes', 'tbl_classes.id = tbl_exams.class_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exams.discipline_id')
            ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exams.exam_board_id')
            ->join('tbl_class_disciplines', 'tbl_class_disciplines.class_id = tbl_exams.class_id AND tbl_class_disciplines.discipline_id = tbl_exams.discipline_id')
            ->where('tbl_class_disciplines.teacher_id', $teacherId);
        
        if (!empty($filters['class'])) {
            $builder->where('tbl_exams.class_id', $filters['class']);
        }
        
        if ($filters['status'] == 'pending') {
            $builder->where('tbl_exams.exam_date >=', date('Y-m-d'));
        } elseif ($filters['status'] == 'completed') {
            $builder->where('tbl_exams.exam_date <', date('Y-m-d'));
        }
        
        $data['exams'] = $builder->orderBy('tbl_exams.exam_date', 'DESC')->findAll();
        
        // Get classes for filter
        $data['classes'] = $this->classDisciplineModel
            ->select('tbl_classes.id, tbl_classes.class_name')
            ->join('tbl_classes', 'tbl_classes.id = tbl_class_disciplines.class_id')
            ->where('tbl_class_disciplines.teacher_id', $teacherId)
            ->distinct()
            ->findAll();

        // PASSAR OS FILTROS PARA A VIEW
        $data['filters'] = $filters;
        $data['selectedClass'] = $filters['class'];
        $data['selectedStatus'] = $filters['status'];
        
        return view('teachers/exams/index', $data);
    }
    
    /**
     * Form to create/edit exam
     */
    public function form($id = null)
    {
        $data['title'] = $id ? 'Editar Exame' : 'Novo Exame';
        $data['exam'] = $id ? $this->examModel->find($id) : null;
        
        $teacherId = $this->session->get('user_id');
        
        // Get teacher's classes
        $data['classes'] = $this->classDisciplineModel
            ->select('tbl_classes.id, tbl_classes.class_name, tbl_classes.class_code')
            ->join('tbl_classes', 'tbl_classes.id = tbl_class_disciplines.class_id')
            ->where('tbl_class_disciplines.teacher_id', $teacherId)
            ->distinct()
            ->findAll();
        
            log_message('info', "Classes do professor {$teacherId}: " . json_encode($data['classes']));

        // Get exam boards
        $data['boards'] = $this->examBoardModel
            ->where('is_active', 1)
            ->findAll();
        
        // Get semesters
        $currentYear = model('App\Models\AcademicYearModel')->getCurrent();
        if ($currentYear) {
            $data['semesters'] = $this->semesterModel
                ->where('academic_year_id', $currentYear->id)
                ->where('is_active', 1)
                ->findAll();
        } else {
            $data['semesters'] = [];
        }
        
        return view('teachers/exams/form', $data);
    }
    
    /**
     * Save exam
     */
    public function save()
    {
        $rules = [
            'exam_name' => 'required',
            'class_id' => 'required|numeric',
            'discipline_id' => 'required|numeric',
            'exam_board_id' => 'required|numeric',
            'semester_id' => 'required|numeric',
            'exam_date' => 'required|valid_date'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'exam_name' => $this->request->getPost('exam_name'),
            'class_id' => $this->request->getPost('class_id'),
            'discipline_id' => $this->request->getPost('discipline_id'),
            'semester_id' => $this->request->getPost('semester_id'),
            'exam_board_id' => $this->request->getPost('exam_board_id'),
            'exam_date' => $this->request->getPost('exam_date'),
            'exam_time' => $this->request->getPost('exam_time'),
            'exam_room' => $this->request->getPost('exam_room'),
            'max_score' => $this->request->getPost('max_score') ?: 20,
            'description' => $this->request->getPost('description'),
            'created_by' => $this->session->get('user_id')
        ];
        
        $id = $this->request->getPost('id');
        
        if ($id) {
            $this->examModel->update($id, $data);
            $message = 'Exame atualizado com sucesso';
        } else {
            $this->examModel->insert($data);
            $message = 'Exame criado com sucesso';
        }
        
        return redirect()->to('/teachers/exams')->with('success', $message);
    }
    
    /**
     * Grade exam - show form to enter grades
     */
    public function grade($examId)
    {
        $data['exam'] = $this->examModel
            ->select('tbl_exams.*, tbl_classes.class_name, tbl_disciplines.discipline_name')
            ->join('tbl_classes', 'tbl_classes.id = tbl_exams.class_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exams.discipline_id')
            ->where('tbl_exams.id', $examId)
            ->first();
        
        if (!$data['exam']) {
            return redirect()->to('/teachers/exams')->with('error', 'Exame não encontrado');
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
            ->where('exam_id', $examId)
            ->findAll();
        
        $data['results'] = [];
        foreach ($results as $result) {
            $data['results'][$result->enrollment_id] = $result;
        }
        
        return view('teachers/exams/grade', $data);
    }
    
    /**
     * Save grades
     */
    public function saveGrades()
    {
        $examId = $this->request->getPost('exam_id');
        $scores = $this->request->getPost('scores') ?? [];
        
        if (!$examId || empty($scores)) {
            return redirect()->back()->with('error', 'Dados incompletos');
        }
        
        $exam = $this->examModel->find($examId);
        if (!$exam) {
            return redirect()->back()->with('error', 'Exame não encontrado');
        }
        
        $data = [];
        foreach ($scores as $enrollmentId => $score) {
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
            return redirect()->to('/teachers/exams/grade/' . $examId)
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
    // Verificar se o classId foi fornecido
    if (!$classId) {
        log_message('error', 'getDisciplines chamado sem classId');
        return $this->response->setJSON([]);
    }
    
    $teacherId = $this->session->get('user_id');
    
    $disciplines = $this->classDisciplineModel
        ->select('tbl_disciplines.id, tbl_disciplines.discipline_name')
        ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
        ->where('tbl_class_disciplines.class_id', $classId)
        ->where('tbl_class_disciplines.teacher_id', $teacherId)
        ->findAll();
    
    log_message('info', "Disciplinas para turma {$classId}: " . json_encode($disciplines));
    
    return $this->response->setJSON($disciplines);
}
/**
 * View exam results
 * 
 * @param int $examId ID do exame
 * @return View
 */
/**
 * View exam results
 */
public function results($examId)
{
    $data['title'] = 'Resultados do Exame';
    
    // Buscar dados do exame
    $data['exam'] = $this->examModel
        ->select('
            tbl_exams.*, 
            tbl_classes.class_name, 
            tbl_disciplines.discipline_name,
            tbl_exam_boards.board_name
        ')
        ->join('tbl_classes', 'tbl_classes.id = tbl_exams.class_id')
        ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exams.discipline_id')
        ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exams.exam_board_id')
        ->where('tbl_exams.id', $examId)
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
        ->where('tbl_exam_results.exam_id', $examId)
        ->orderBy('tbl_users.first_name', 'ASC')
        ->findAll();
    
    // Estatísticas
    $data['statistics'] = $this->examResultModel->getExamStatistics($examId);
    
    // Total de alunos na turma
    $data['totalStudents'] = $this->enrollmentModel
        ->where('class_id', $data['exam']->class_id)
        ->where('status', 'Ativo')
        ->countAllResults();
    
    return view('teachers/exams/results', $data);
}
}