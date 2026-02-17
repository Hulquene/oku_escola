<?php

namespace App\Controllers\students;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use App\Models\EnrollmentModel;
use App\Models\ExamResultModel;
use App\Models\StudentFeeModel;
use App\Models\AttendanceModel;

class Dashboard extends BaseController
{
    protected $studentModel;
    protected $enrollmentModel;
    protected $examResultModel;
    protected $studentFeeModel;
    protected $attendanceModel;
    
    public function __construct()
    {
        $this->studentModel = new StudentModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->examResultModel = new ExamResultModel();
        $this->studentFeeModel = new StudentFeeModel();
        $this->attendanceModel = new AttendanceModel();
    }
    
    /**
     * Student dashboard
     */
    public function index()
    {
        $data['title'] = 'Dashboard do Aluno';
        
        $userId = $this->session->get('user_id');
        $student = $this->studentModel->getByUserId($userId);
        
        if (!$student) {
            return redirect()->to('/auth/logout')->with('error', 'Perfil de aluno não encontrado');
        }
        
        $data['student'] = $student;
        
        // INICIALIZAR VARIÁVEIS COM VALORES PADRÃO
        $data['currentEnrollment'] = null;
        $data['recentGrades'] = [];
        $data['pendingFees'] = [];
        $data['attendance'] = null;
        
        // Current enrollment
        $currentEnrollment = $this->studentModel->getCurrentEnrollment($student->id);
        
        if ($currentEnrollment) {
            $data['currentEnrollment'] = $currentEnrollment;
            
            // Recent grades
            $data['recentGrades'] = $this->examResultModel
                ->select('tbl_exam_results.*, tbl_exams.exam_name, tbl_disciplines.discipline_name')
                ->join('tbl_exams', 'tbl_exams.id = tbl_exam_results.exam_id')
                ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exams.discipline_id')
                ->where('tbl_exam_results.enrollment_id', $currentEnrollment->id)
                ->orderBy('tbl_exam_results.recorded_at', 'DESC')
                ->limit(5)
                ->findAll();
            
            // Pending fees
            $data['pendingFees'] = $this->studentFeeModel
                ->where('enrollment_id', $currentEnrollment->id)
                ->whereIn('status', ['Pendente', 'Vencido'])
                ->orderBy('due_date', 'ASC')
                ->findAll();
            
            // Attendance this month
            $data['attendance'] = $this->attendanceModel
                ->select('COUNT(*) as total, SUM(CASE WHEN status = "Presente" THEN 1 ELSE 0 END) as present')
                ->where('enrollment_id', $currentEnrollment->id)
                ->where('attendance_date >=', date('Y-m-01'))
                ->where('attendance_date <=', date('Y-m-t'))
                ->first();
        }
        
        return view('students/dashboard/index', $data);
    }
}