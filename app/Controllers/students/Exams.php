<?php

namespace App\Controllers\students;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use App\Models\EnrollmentModel;
use App\Models\ExamModel;
use App\Models\ExamResultModel;
use App\Models\SemesterModel;

class Exams extends BaseController
{
    protected $studentModel;
    protected $enrollmentModel;
    protected $examModel;
    protected $examResultModel;
    protected $semesterModel;
    
    public function __construct()
    {
        $this->studentModel = new StudentModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->examModel = new ExamModel();
        $this->examResultModel = new ExamResultModel();
        $this->semesterModel = new SemesterModel();
        
        // Check if user is student
       /*  if ($this->session->get('user_type') !== 'student') {
            return redirect()->to('/auth/login')->with('error', 'Acesso não autorizado');
        } */
    }
    
    /**
     * List my exams
     */
    public function index()
    {
        $data['title'] = 'Meus Exames';
        
        $userId = $this->session->get('user_id');
        $student = $this->studentModel->getByUserId($userId);
        
        if (!$student) {
            return redirect()->to('/students/dashboard')->with('error', 'Perfil não encontrado');
        }
        
        // Get current enrollment
        $enrollment = $this->studentModel->getCurrentEnrollment($student->id);
        
        if (!$enrollment) {
            return redirect()->to('/students/dashboard')->with('error', 'Nenhuma matrícula ativa encontrada');
        }
        
        // Get upcoming exams for this class
        $data['upcomingExams'] = $this->examModel
            ->select('tbl_exams.*, tbl_disciplines.discipline_name, tbl_exam_boards.board_name')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exams.discipline_id')
            ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exams.exam_board_id')
            ->where('tbl_exams.class_id', $enrollment->class_id)
            ->where('tbl_exams.exam_date >=', date('Y-m-d'))
            ->orderBy('tbl_exams.exam_date', 'ASC')
            ->orderBy('tbl_exams.exam_time', 'ASC')
            ->findAll();
        
        // Get past exams
        $data['pastExams'] = $this->examModel
            ->select('tbl_exams.*, tbl_disciplines.discipline_name, tbl_exam_boards.board_name')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exams.discipline_id')
            ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exams.exam_board_id')
            ->where('tbl_exams.class_id', $enrollment->class_id)
            ->where('tbl_exams.exam_date <', date('Y-m-d'))
            ->orderBy('tbl_exams.exam_date', 'DESC')
            ->findAll();
        
        // Get results for past exams
        $examIds = array_column($data['pastExams'], 'id');
        if (!empty($examIds)) {
            $results = $this->examResultModel
                ->whereIn('exam_id', $examIds)
                ->where('enrollment_id', $enrollment->id)
                ->findAll();
            
            $data['results'] = [];
            foreach ($results as $result) {
                $data['results'][$result->exam_id] = $result;
            }
        } else {
            $data['results'] = [];
        }
        
        $data['enrollment'] = $enrollment;
        $data['student'] = $student;
        
        return view('students/exams/index', $data);
    }
    
    /**
     * Exam schedule
     */
    public function schedule()
    {
        $data['title'] = 'Calendário de Exames';
        
        $userId = $this->session->get('user_id');
        $student = $this->studentModel->getByUserId($userId);
        
        if (!$student) {
            return redirect()->to('/students/dashboard')->with('error', 'Perfil não encontrado');
        }
        
        // Get current enrollment
        $enrollment = $this->studentModel->getCurrentEnrollment($student->id);
        
        if (!$enrollment) {
            return redirect()->to('/students/dashboard')->with('error', 'Nenhuma matrícula ativa encontrada');
        }
        
        $month = $this->request->getGet('month') ?: date('m');
        $year = $this->request->getGet('year') ?: date('Y');
        
        $startDate = "$year-$month-01";
        $endDate = date('Y-m-t', strtotime($startDate));
        
        // Get exams for the month
        $data['exams'] = $this->examModel
            ->select('tbl_exams.*, tbl_disciplines.discipline_name, tbl_exam_boards.board_name')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exams.discipline_id')
            ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exams.exam_board_id')
            ->where('tbl_exams.class_id', $enrollment->class_id)
            ->where('tbl_exams.exam_date >=', $startDate)
            ->where('tbl_exams.exam_date <=', $endDate)
            ->orderBy('tbl_exams.exam_date', 'ASC')
            ->orderBy('tbl_exams.exam_time', 'ASC')
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
     * Exam results
     */
    public function results()
    {
        $data['title'] = 'Meus Resultados';
        
        $userId = $this->session->get('user_id');
        $student = $this->studentModel->getByUserId($userId);
        
        if (!$student) {
            return redirect()->to('/students/dashboard')->with('error', 'Perfil não encontrado');
        }
        
        $semesterId = $this->request->getGet('semester') ?: 
            ($this->semesterModel->getCurrent()->id ?? null);
        
        // Get current enrollment
        $enrollment = $this->studentModel->getCurrentEnrollment($student->id);
        
        if (!$enrollment) {
            return redirect()->to('/students/dashboard')->with('error', 'Nenhuma matrícula ativa encontrada');
        }
        
        // Get exam results
        $data['results'] = $this->examResultModel
            ->select('
                tbl_exam_results.*,
                tbl_exams.exam_name,
                tbl_exams.exam_date,
                tbl_disciplines.discipline_name,
                tbl_exam_boards.board_name,
                tbl_exam_boards.board_type
            ')
            ->join('tbl_exams', 'tbl_exams.id = tbl_exam_results.exam_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exams.discipline_id')
            ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exams.exam_board_id')
            ->where('tbl_exam_results.enrollment_id', $enrollment->id)
            ->where('tbl_exams.semester_id', $semesterId)
            ->orderBy('tbl_exams.exam_date', 'DESC')
            ->findAll();
        
        // Calculate statistics
        $total = 0;
        $count = 0;
        $approved = 0;
        $failed = 0;
        
        foreach ($data['results'] as $result) {
            $total += $result->score;
            $count++;
            if ($result->score >= 10) {
                $approved++;
            } else {
                $failed++;
            }
        }
        
        $data['average'] = $count > 0 ? round($total / $count, 1) : 0;
        $data['approved'] = $approved;
        $data['failed'] = $failed;
        $data['total'] = $count;
        
        $data['semesters'] = $this->semesterModel->getActive();
        $data['selectedSemester'] = $semesterId;
        
        return view('students/exams/results', $data);
    }
}