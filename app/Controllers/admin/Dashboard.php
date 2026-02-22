<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use App\Models\UserModel;
use App\Models\EnrollmentModel;
use App\Models\AttendanceModel;
use App\Models\StudentFeeModel;
use App\Models\ClassModel;
use App\Models\ExamScheduleModel;
use App\Models\ExamResultModel;
use App\Models\SemesterModel;
use App\Models\AcademicYearModel;

class Dashboard extends BaseController
{
    protected $studentModel;
    protected $userModel;
    protected $enrollmentModel;
    protected $attendanceModel;
    protected $feeModel;
    protected $classModel;
    protected $examScheduleModel;
    protected $examResultModel;
    protected $semesterModel;
    protected $academicYearModel;
    
    public function __construct()
    {
        $this->studentModel = new StudentModel();
        $this->userModel = new UserModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->attendanceModel = new AttendanceModel();
        $this->feeModel = new StudentFeeModel();
        $this->classModel = new ClassModel();
        $this->examScheduleModel = new ExamScheduleModel();
        $this->examResultModel = new ExamResultModel();
        $this->semesterModel = new SemesterModel();
        $this->academicYearModel = new AcademicYearModel();
    }
    
    /**
     * Admin dashboard
     */
    public function index()
    {
        $data['title'] = 'Dashboard';
        
        // ===== ESTATÍSTICAS PRINCIPAIS =====
        $data['totalStudents'] = $this->studentModel->where('is_active', 1)->countAllResults();
        $data['totalTeachers'] = $this->userModel->where('user_type', 'teacher')->where('is_active', 1)->countAllResults();
        $data['totalStaff'] = $this->userModel->where('user_type', 'staff')->where('is_active', 1)->countAllResults();
        
        // ===== MATRÍCULAS =====
        $currentYear = $this->academicYearModel->getCurrent();
        $data['currentYear'] = $currentYear;
        
        if ($currentYear) {
            $data['activeEnrollments'] = $this->enrollmentModel
                ->where('academic_year_id', $currentYear->id)
                ->where('status', 'Ativo')
                ->countAllResults();
            
            // Matrículas por gênero
            $data['maleStudents'] = $this->enrollmentModel
                ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
                ->where('tbl_enrollments.academic_year_id', $currentYear->id)
                ->where('tbl_enrollments.status', 'Ativo')
                ->where('tbl_students.gender', 'Masculino')
                ->countAllResults();
            
            $data['femaleStudents'] = $this->enrollmentModel
                ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
                ->where('tbl_enrollments.academic_year_id', $currentYear->id)
                ->where('tbl_enrollments.status', 'Ativo')
                ->where('tbl_students.gender', 'Feminino')
                ->countAllResults();
        } else {
            $data['activeEnrollments'] = 0;
            $data['maleStudents'] = 0;
            $data['femaleStudents'] = 0;
        }
        
        // ===== MATRÍCULAS RECENTES =====
        $data['recentEnrollments'] = $this->enrollmentModel
            ->select('tbl_enrollments.*, tbl_users.first_name, tbl_users.last_name, tbl_classes.class_name, tbl_grade_levels.level_name')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
            ->orderBy('tbl_enrollments.created_at', 'DESC')
            ->limit(10)
            ->findAll();
        
        // ===== ESTATÍSTICAS FINANCEIRAS =====
        $feeStats = $this->getFeeStatistics();
        $data['feeStats'] = $feeStats;
        
        // Pagamentos do mês atual
        $data['currentMonthPayments'] = $this->feeModel
            ->select('SUM(amount_paid) as total')
            ->join('tbl_fee_payments', 'tbl_fee_payments.student_fee_id = tbl_student_fees.id')
            ->where('MONTH(tbl_fee_payments.payment_date)', date('m'))
            ->where('YEAR(tbl_fee_payments.payment_date)', date('Y'))
            ->get()
            ->getRow()
            ->total ?? 0;
        
        // Pagamentos pendentes (vencidos)
        $data['overduePayments'] = $this->feeModel
            ->where('status', 'Pendente')
            ->where('due_date <', date('Y-m-d'))
            ->countAllResults();
        
        // ===== PRESENÇAS =====
        $data['attendanceToday'] = $this->attendanceModel
            ->where('attendance_date', date('Y-m-d'))
            ->countAllResults();
        
        // Taxa de presença do mês
        $data['attendanceRate'] = $this->getAttendanceRate();
        
        // ===== EXAMES =====
        $data['upcomingExams'] = $this->examScheduleModel
            ->select('tbl_exam_schedules.*, tbl_classes.class_name, tbl_disciplines.discipline_name, tbl_exam_boards.board_name')
            ->join('tbl_classes', 'tbl_classes.id = tbl_exam_schedules.class_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id')
            ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exam_schedules.exam_board_id')
            ->where('tbl_exam_schedules.exam_date >=', date('Y-m-d'))
            ->orderBy('tbl_exam_schedules.exam_date', 'ASC')
            ->limit(10)
            ->findAll();
        
        $data['examsToday'] = $this->examScheduleModel
            ->where('exam_date', date('Y-m-d'))
            ->countAllResults();
        
        $data['examsThisWeek'] = $this->examScheduleModel
            ->where('exam_date >=', date('Y-m-d'))
            ->where('exam_date <=', date('Y-m-d', strtotime('+7 days')))
            ->countAllResults();
        
        // ===== TURMAS =====
        $data['totalClasses'] = $this->classModel->where('is_active', 1)->countAllResults();
        $data['classesByShift'] = $this->getClassesByShift();
        
        // ===== GRÁFICOS =====
        $data['enrollmentChart'] = $this->getEnrollmentChartData();
        $data['feeChart'] = $this->getFeeChartData();
        $data['attendanceChart'] = $this->getAttendanceChartData();
        $data['genderChart'] = $this->getGenderChartData();
        $data['performanceChart'] = $this->getPerformanceChartData();
        
        return view('admin/dashboard/index', $data);
    }
    
    /**
     * Get fee statistics
     */
    protected function getFeeStatistics()
    {
        $db = db_connect();
        
        // Total de propinas (valor total)
        $totalResult = $db->query("
            SELECT SUM(total_amount) as total 
            FROM tbl_student_fees 
            WHERE status != 'Cancelado'
        ")->getRow();
        
        // Total pago
        $paidResult = $db->query("
            SELECT SUM(amount_paid) as total 
            FROM tbl_fee_payments fp
            JOIN tbl_student_fees sf ON sf.id = fp.student_fee_id
            WHERE sf.status != 'Cancelado'
        ")->getRow();
        
        // Pendente (total - pago)
        $total = $totalResult->total ?? 0;
        $paid = $paidResult->total ?? 0;
        $pending = $total - $paid;
        
        return (object)[
            'total_amount' => $total,
            'paid_amount' => $paid,
            'pending_amount' => $pending,
            'paid_percentage' => $total > 0 ? round(($paid / $total) * 100, 1) : 0,
            'pending_percentage' => $total > 0 ? round(($pending / $total) * 100, 1) : 0
        ];
    }
    
    /**
     * Get attendance rate for current month
     */
    protected function getAttendanceRate()
    {
        $db = db_connect();
        
        $result = $db->query("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'Presente' THEN 1 ELSE 0 END) as present
            FROM tbl_attendance
            WHERE MONTH(attendance_date) = ? 
            AND YEAR(attendance_date) = ?
        ", [date('m'), date('Y')])->getRow();
        
        if ($result && $result->total > 0) {
            return round(($result->present / $result->total) * 100, 1);
        }
        
        return 0;
    }
    
    /**
     * Get classes by shift
     */
    protected function getClassesByShift()
    {
        $db = db_connect();
        
        $result = $db->query("
            SELECT 
                class_shift,
                COUNT(*) as total
            FROM tbl_classes
            WHERE is_active = 1
            GROUP BY class_shift
        ")->getResult();
        
        $data = [
            'Manhã' => 0,
            'Tarde' => 0,
            'Noite' => 0,
            'Integral' => 0
        ];
        
        foreach ($result as $row) {
            $data[$row->class_shift] = $row->total;
        }
        
        return $data;
    }
    
    /**
     * Get enrollment chart data (últimos 12 meses)
     */
    protected function getEnrollmentChartData()
    {
        $months = [];
        $counts = [];
        $labels = [];
        
        for ($i = 11; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $months[] = $month;
            $labels[] = date('M/Y', strtotime("-$i months"));
            
            $count = $this->enrollmentModel
                ->where('enrollment_date >=', date('Y-m-01', strtotime($month)))
                ->where('enrollment_date <=', date('Y-m-t', strtotime($month)))
                ->countAllResults();
            
            $counts[] = $count;
        }
        
        return [
            'months' => $labels,
            'counts' => $counts
        ];
    }
    
    /**
     * Get fee chart data (últimos 6 meses)
     */
    protected function getFeeChartData()
    {
        $months = [];
        $collected = [];
        $pending = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $months[] = date('M/Y', strtotime("-$i months"));
            
            // Total recebido no mês
            $collectedResult = $this->feeModel
                ->select('SUM(tbl_fee_payments.amount_paid) as total')
                ->join('tbl_fee_payments', 'tbl_fee_payments.student_fee_id = tbl_student_fees.id')
                ->where('MONTH(tbl_fee_payments.payment_date)', date('m', strtotime($month)))
                ->where('YEAR(tbl_fee_payments.payment_date)', date('Y', strtotime($month)))
                ->get()
                ->getRow();
            
            $collected[] = $collectedResult->total ?? 0;
            
            // Total pendente gerado no mês
            $pendingResult = $this->feeModel
                ->where('MONTH(due_date)', date('m', strtotime($month)))
                ->where('YEAR(due_date)', date('Y', strtotime($month)))
                ->where('status', 'Pendente')
                ->select('SUM(amount) as total')
                ->get()
                ->getRow();
            
            $pending[] = $pendingResult->total ?? 0;
        }
        
        return [
            'months' => $months,
            'collected' => $collected,
            'pending' => $pending
        ];
    }
    
    /**
     * Get attendance chart data (últimos 7 dias)
     */
    protected function getAttendanceChartData()
    {
        $days = [];
        $present = [];
        $absent = [];
        $labels = [];
        
        for ($i = 6; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days"));
            $labels[] = date('d/m', strtotime("-$i days"));
            $days[] = $date;
            
            $result = $this->attendanceModel
                ->select('
                    COUNT(*) as total,
                    SUM(CASE WHEN status = "Presente" THEN 1 ELSE 0 END) as present_count
                ')
                ->where('attendance_date', $date)
                ->get()
                ->getRow();
            
            $present[] = $result->present_count ?? 0;
            $absent[] = ($result->total ?? 0) - ($result->present_count ?? 0);
        }
        
        return [
            'days' => $labels,
            'present' => $present,
            'absent' => $absent
        ];
    }
    
    /**
     * Get gender distribution chart data
     */
    protected function getGenderChartData()
    {
        $currentYear = $this->academicYearModel->getCurrent();
        
        if (!$currentYear) {
            return [
                'male' => 0,
                'female' => 0
            ];
        }
        
        $male = $this->enrollmentModel
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->where('tbl_enrollments.academic_year_id', $currentYear->id)
            ->where('tbl_enrollments.status', 'Ativo')
            ->where('tbl_students.gender', 'Masculino')
            ->countAllResults();
        
        $female = $this->enrollmentModel
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->where('tbl_enrollments.academic_year_id', $currentYear->id)
            ->where('tbl_enrollments.status', 'Ativo')
            ->where('tbl_students.gender', 'Feminino')
            ->countAllResults();
        
        return [
            'male' => $male,
            'female' => $female
        ];
    }
    
    /**
     * Get student performance chart data (distribuição de notas)
     */
    protected function getPerformanceChartData()
    {
        $currentSemester = $this->semesterModel->getCurrent();
        
        if (!$currentSemester) {
            return [
                'excelente' => 0,
                'bom' => 0,
                'satisfatorio' => 0,
                'insuficiente' => 0
            ];
        }
        
        $db = db_connect();
        
        $result = $db->query("
            SELECT 
                SUM(CASE WHEN final_score >= 17 THEN 1 ELSE 0 END) as excelente,
                SUM(CASE WHEN final_score >= 14 AND final_score < 17 THEN 1 ELSE 0 END) as bom,
                SUM(CASE WHEN final_score >= 10 AND final_score < 14 THEN 1 ELSE 0 END) as satisfatorio,
                SUM(CASE WHEN final_score < 10 THEN 1 ELSE 0 END) as insuficiente
            FROM tbl_semester_results
            WHERE semester_id = ?
        ", [$currentSemester->id])->getRow();
        
        return [
            'excelente' => $result->excelente ?? 0,
            'bom' => $result->bom ?? 0,
            'satisfatorio' => $result->satisfatorio ?? 0,
            'insuficiente' => $result->insuficiente ?? 0
        ];
    }
}