<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\ExamModel;
use App\Models\ExamBoardModel;
use App\Models\ExamResultModel;
use App\Models\ClassModel;
use App\Models\DisciplineModel;
use App\Models\SemesterModel;
use App\Models\EnrollmentModel;

class Exams extends BaseController
{
    protected $examModel;
    protected $examBoardModel;
    protected $examResultModel;
    protected $classModel;
    protected $disciplineModel;
    protected $semesterModel;
    protected $enrollmentModel;
    
    public function __construct()
    {
        $this->examModel = new ExamModel();
        $this->examBoardModel = new ExamBoardModel();
        $this->examResultModel = new ExamResultModel();
        $this->classModel = new ClassModel();
        $this->disciplineModel = new DisciplineModel();
        $this->semesterModel = new SemesterModel();
        $this->enrollmentModel = new EnrollmentModel();
    }
    
    /**
     * Exams list
     */
    public function index()
    {
        $data['title'] = 'Exames';
        
        $classId = $this->request->getGet('class');
        $semesterId = $this->request->getGet('semester') ?: 
            ($this->semesterModel->getCurrent()->id ?? null);
        
        $builder = $this->examModel
            ->select('tbl_exams.*, tbl_classes.class_name, tbl_disciplines.discipline_name, tbl_semesters.semester_name, tbl_exam_boards.board_name')
            ->join('tbl_classes', 'tbl_classes.id = tbl_exams.class_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exams.discipline_id')
            ->join('tbl_semesters', 'tbl_semesters.id = tbl_exams.semester_id')
            ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exams.exam_board_id');
        
        if ($classId) {
            $builder->where('tbl_exams.class_id', $classId);
        }
        
        if ($semesterId) {
            $builder->where('tbl_exams.semester_id', $semesterId);
        }
        
        $data['exams'] = $builder->orderBy('tbl_exams.exam_date', 'DESC')
            ->paginate(10);
        
        $data['pager'] = $this->examModel->pager;
        $data['classes'] = $this->classModel->findAll();
        $data['semesters'] = $this->semesterModel->findAll();
        
        return view('admin/exams/index', $data);
    }
    
    /**
     * Exam form
     */
    public function form($id = null)
    {
        $data['title'] = $id ? 'Editar Exame' : 'Novo Exame';
        $data['exam'] = $id ? $this->examModel->getWithDetails($id) : null;
        
        $currentSemester = $this->semesterModel->getCurrent();
        
        $data['classes'] = $this->classModel
            ->select('tbl_classes.*, tbl_grade_levels.level_name')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
            ->where('tbl_classes.is_active', 1)
            ->orderBy('tbl_classes.class_name', 'ASC')
            ->findAll();
        
        $data['disciplines'] = $this->disciplineModel->getActive();
        $data['semesters'] = $this->semesterModel->getActive();
        $data['examBoards'] = $this->examBoardModel->getActive();
        
        return view('admin/exams/form', $data);
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
            'semester_id' => 'required|numeric',
            'exam_board_id' => 'required|numeric',
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
            'is_published' => $this->request->getPost('is_published') ? 1 : 0,
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
        
        return redirect()->to('/admin/exams')->with('success', $message);
    }
    
    /**
     * View exam
     */
    public function view($id)
    {
        $data['title'] = 'Detalhes do Exame';
        $data['exam'] = $this->examModel->getWithDetails($id);
        
        if (!$data['exam']) {
            return redirect()->to('/admin/exams')->with('error', 'Exame não encontrado');
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
        $results = $this->examResultModel->getByExam($id);
        $data['results'] = [];
        foreach ($results as $result) {
            $data['results'][$result->enrollment_id] = $result;
        }
        
        // Statistics
        $data['statistics'] = $this->examResultModel->getExamStatistics($id);
        
        return view('admin/exams/view', $data);
    }
    
    /**
     * Save exam results
     */
    public function saveResults()
    {
        $examId = $this->request->getPost('exam_id');
        
        if (!$examId) {
            return redirect()->back()->with('error', 'Exame não especificado');
        }
        
        $results = [];
        $scores = $this->request->getPost('score') ?? [];
        
        foreach ($scores as $enrollmentId => $score) {
            if ($score !== '') {
                $results[] = [
                    'exam_id' => $examId,
                    'enrollment_id' => $enrollmentId,
                    'score' => $score,
                    'recorded_by' => $this->session->get('user_id')
                ];
            }
        }
        
        if (empty($results)) {
            return redirect()->back()->with('error', 'Nenhum resultado para guardar');
        }
        
        $result = $this->examResultModel->saveBulk($results);
        
        if ($result) {
            return redirect()->to('/admin/exams/view/' . $examId)
                ->with('success', 'Resultados guardados com sucesso');
        } else {
            return redirect()->back()->with('error', 'Erro ao guardar resultados');
        }
    }
    
    /**
     * Publish exam results
     */
    public function publish($id)
    {
        $this->examModel->update($id, ['is_published' => 1]);
        
        return redirect()->back()->with('success', 'Resultados publicados com sucesso');
    }
    
    /**
     * Delete exam
     */
    public function delete($id)
    {
        // Check if has results
        $results = $this->examResultModel->where('exam_id', $id)->countAllResults();
        
        if ($results > 0) {
            return redirect()->back()
                ->with('error', 'Não é possível eliminar exame com resultados associados');
        }
        
        $this->examModel->delete($id);
        
        return redirect()->to('/admin/exams')->with('success', 'Exame eliminado com sucesso');
    }
    
   
    /**
     * Exam schedule
     */
    public function schedule()
    {
        $data['title'] = 'Calendário de Exames';
        
        $classId = $this->request->getGet('class');
        $startDate = $this->request->getGet('start_date') ?: date('Y-m-d');
        $endDate = $this->request->getGet('end_date') ?: date('Y-m-d', strtotime('+30 days'));
        
        $builder = $this->examModel
            ->select('tbl_exams.*, tbl_classes.class_name, tbl_disciplines.discipline_name, tbl_exam_boards.board_name')
            ->join('tbl_classes', 'tbl_classes.id = tbl_exams.class_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exams.discipline_id')
            ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exams.exam_board_id')
            ->where('tbl_exams.exam_date >=', $startDate)
            ->where('tbl_exams.exam_date <=', $endDate);
        
        if ($classId) {
            $builder->where('tbl_exams.class_id', $classId);
        }
        
        $data['exams'] = $builder->orderBy('tbl_exams.exam_date', 'ASC')
            ->orderBy('tbl_exams.exam_time', 'ASC')
            ->findAll();
        
        $data['classes'] = $this->classModel->findAll();
        
        // PASSAR OS VALORES PARA A VIEW
        $data['selectedClass'] = $classId;
        $data['selectedStartDate'] = $startDate;
        $data['selectedEndDate'] = $endDate;
        
        return view('admin/exams/schedule', $data);
    }
}