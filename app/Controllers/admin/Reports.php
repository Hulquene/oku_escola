<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use App\Models\UserModel;
use App\Models\EnrollmentModel;
use App\Models\AttendanceModel;
use App\Models\ExamModel;
use App\Models\ExamResultModel;
use App\Models\FinalGradeModel;
use App\Models\StudentFeeModel;
use App\Models\FeePaymentModel;
use App\Models\InvoiceModel;
use App\Models\ExpenseModel;
use App\Models\ClassModel;
use App\Models\DisciplineModel;
use App\Models\SemesterModel;
use App\Models\AcademicYearModel;

class Reports extends BaseController
{
    protected $studentModel;
    protected $userModel;
    protected $enrollmentModel;
    protected $attendanceModel;
    protected $examModel;
    protected $examResultModel;
    protected $finalGradeModel;
    protected $studentFeeModel;
    protected $feePaymentModel;
    protected $invoiceModel;
    protected $expenseModel;
    protected $classModel;
    protected $disciplineModel;
    protected $semesterModel;
    protected $academicYearModel;
    
    public function __construct()
    {
        $this->studentModel = new StudentModel();
        $this->userModel = new UserModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->attendanceModel = new AttendanceModel();
        $this->examModel = new ExamModel();
        $this->examResultModel = new ExamResultModel();
        $this->finalGradeModel = new FinalGradeModel();
        $this->studentFeeModel = new StudentFeeModel();
        $this->feePaymentModel = new FeePaymentModel();
        $this->invoiceModel = new InvoiceModel();
        $this->expenseModel = new ExpenseModel();
        $this->classModel = new ClassModel();
        $this->disciplineModel = new DisciplineModel();
        $this->semesterModel = new SemesterModel();
        $this->academicYearModel = new AcademicYearModel();
    }
    
    /**
     * Reports dashboard
     */
    public function index()
    {
        $data['title'] = 'Relatórios';
        
        // Get current year statistics
        $currentYear = $this->academicYearModel->getCurrent();
        $yearId = $currentYear ? $currentYear->id : null;
        
        // Student statistics
        $data['totalStudents'] = $this->studentModel->where('is_active', 1)->countAllResults();
        $data['activeEnrollments'] = $yearId ? $this->enrollmentModel
            ->where('academic_year_id', $yearId)
            ->where('status', 'Ativo')
            ->countAllResults() : 0;
        
        // Financial statistics
        $invoiceStats = $this->invoiceModel->getStatistics();
        $data['totalInvoiced'] = $invoiceStats->total_paid + $invoiceStats->total_pending ?? 0;
        $data['totalPaid'] = $invoiceStats->total_paid ?? 0;
        $data['totalPending'] = $invoiceStats->total_pending ?? 0;
        
        // Expense statistics
        $expenseStats = $this->expenseModel->getSummary();
        $data['totalExpenses'] = $expenseStats->total_amount ?? 0;
        
        // Attendance statistics
        $today = date('Y-m-d');
        $data['attendanceToday'] = $this->attendanceModel
            ->where('attendance_date', $today)
            ->countAllResults();
        
        // Exam statistics
        $data['totalExams'] = $this->examResultModel->countAllResults();
        
        return view('admin/reports/index', $data);
    }
    
    /**
     * Academic reports
     */
    public function academic()
    {
        $data['title'] = 'Relatórios Académicos';
        
        $academicYearId = $this->request->getGet('academic_year') ?: 
            ($this->academicYearModel->getCurrent()->id ?? null);
        $semesterId = $this->request->getGet('semester');
        $classId = $this->request->getGet('class');
        
        $data['academicYears'] = $this->academicYearModel
            ->orderBy('start_date', 'DESC')
            ->findAll();
        
        $data['semesters'] = $academicYearId ? $this->semesterModel
            ->where('academic_year_id', $academicYearId)
            ->where('is_active', 1)
            ->orderBy('start_date', 'ASC')
            ->findAll() : [];
        
        $data['classes'] = $academicYearId ? $this->classModel
            ->select('tbl_classes.*, tbl_grade_levels.level_name')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
            ->where('tbl_classes.academic_year_id', $academicYearId)
            ->where('tbl_classes.is_active', 1)
            ->orderBy('tbl_classes.class_name', 'ASC')
            ->findAll() : [];
        
        $data['selectedYear'] = $academicYearId;
        $data['selectedSemester'] = $semesterId;
        $data['selectedClass'] = $classId;
        
        return view('admin/reports/academic/index', $data);
    }
    
    /**
     * Financial reports
     */
    public function financial()
    {
        $data['title'] = 'Relatórios Financeiros';
        
        $year = $this->request->getGet('year') ?: date('Y');
        $month = $this->request->getGet('month');
        
        // Invoice summary
        $invoiceStats = $this->invoiceModel->getStatistics($year);
        $monthlyInvoices = $this->invoiceModel->getMonthlyTotals($year);
        
        // Payment summary
        $startDate = $month ? "$year-$month-01" : "$year-01-01";
        $endDate = $month ? date('Y-m-t', strtotime($startDate)) : "$year-12-31";
        
        $paymentMethods = $this->feePaymentModel
            ->select('payment_method, COUNT(*) as count, SUM(amount_paid) as total')
            ->where('payment_date >=', $startDate)
            ->where('payment_date <=', $endDate)
            ->groupBy('payment_method')
            ->findAll();
        
        // Expense summary
        $expenseStats = $this->expenseModel->getSummary($year);
        $expensesByCategory = $this->expenseModel
            ->select('tbl_expense_categories.category_name, SUM(tbl_expenses.amount) as total')
            ->join('tbl_expense_categories', 'tbl_expense_categories.id = tbl_expenses.expense_category_id')
            ->where('YEAR(tbl_expenses.expense_date)', $year)
            ->groupBy('tbl_expenses.expense_category_id')
            ->orderBy('total', 'DESC')
            ->findAll();
        
        $data['invoiceStats'] = $invoiceStats;
        $data['monthlyInvoices'] = $monthlyInvoices;
        $data['paymentMethods'] = $paymentMethods;
        $data['expenseStats'] = $expenseStats;
        $data['expensesByCategory'] = $expensesByCategory;
        
        $data['years'] = range(date('Y') - 5, date('Y'));
        $data['selectedYear'] = $year;
        $data['selectedMonth'] = $month;
        
        return view('admin/reports/financial/index', $data);
    }
    
    /**
     * Students reports
     */
    public function students()
    {
        $data['title'] = 'Relatórios de Alunos';
        
        $classId = $this->request->getGet('class');
        $status = $this->request->getGet('status') ?: 'Ativo';
        $academicYearId = $this->request->getGet('academic_year') ?: 
            ($this->academicYearModel->getCurrent()->id ?? null);
        
        // Get students list
        $builder = $this->enrollmentModel
            ->select('
                tbl_enrollments.*,
                tbl_students.student_number,
                tbl_users.first_name,
                tbl_users.last_name,
                tbl_users.email,
                tbl_users.phone,
                tbl_students.birth_date,
                tbl_students.gender,
                tbl_classes.class_name,
                tbl_classes.class_code
            ')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
            ->where('tbl_enrollments.academic_year_id', $academicYearId)
            ->where('tbl_enrollments.status', $status);
        
        if ($classId) {
            $builder->where('tbl_enrollments.class_id', $classId);
        }
        
        $data['students'] = $builder->orderBy('tbl_classes.class_name', 'ASC')
            ->orderBy('tbl_users.first_name', 'ASC')
            ->findAll();
        
        // Statistics
        $data['totalMale'] = $this->studentModel
            ->join('tbl_enrollments', 'tbl_enrollments.student_id = tbl_students.id')
            ->where('tbl_enrollments.academic_year_id', $academicYearId)
            ->where('tbl_students.gender', 'Masculino')
            ->countAllResults();
        
        $data['totalFemale'] = $this->studentModel
            ->join('tbl_enrollments', 'tbl_enrollments.student_id = tbl_students.id')
            ->where('tbl_enrollments.academic_year_id', $academicYearId)
            ->where('tbl_students.gender', 'Feminino')
            ->countAllResults();
        
        $data['classes'] = $this->classModel
            ->where('academic_year_id', $academicYearId)
            ->where('is_active', 1)
            ->orderBy('class_name', 'ASC')
            ->findAll();
        
        $data['academicYears'] = $this->academicYearModel
            ->orderBy('start_date', 'DESC')
            ->findAll();
        
        $data['selectedClass'] = $classId;
        $data['selectedStatus'] = $status;
        $data['selectedYear'] = $academicYearId;
        
        return view('admin/reports/students/index', $data);
    }
    
    /**
     * Attendance reports
     */
    public function attendance()
    {
        $data['title'] = 'Relatórios de Presenças';
        
        $classId = $this->request->getGet('class');
        $disciplineId = $this->request->getGet('discipline');
        $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?: date('Y-m-t');
        
        // Get attendance summary by class
        $builder = $this->attendanceModel
            ->select('
                tbl_classes.class_name,
                COUNT(*) as total_records,
                SUM(CASE WHEN status = "Presente" THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN status = "Ausente" THEN 1 ELSE 0 END) as absent,
                SUM(CASE WHEN status = "Atrasado" THEN 1 ELSE 0 END) as late,
                SUM(CASE WHEN status = "Falta Justificada" THEN 1 ELSE 0 END) as justified
            ')
            ->join('tbl_classes', 'tbl_classes.id = tbl_attendance.class_id')
            ->where('attendance_date >=', $startDate)
            ->where('attendance_date <=', $endDate);
        
        if ($classId) {
            $builder->where('tbl_attendance.class_id', $classId);
        }
        
        if ($disciplineId) {
            $builder->where('tbl_attendance.discipline_id', $disciplineId);
        }
        
        $data['summary'] = $builder->groupBy('tbl_attendance.class_id')
            ->orderBy('tbl_classes.class_name', 'ASC')
            ->findAll();
        
        // Daily attendance chart data
        $dailyData = $this->attendanceModel
            ->select('
                attendance_date,
                COUNT(*) as total,
                SUM(CASE WHEN status = "Presente" THEN 1 ELSE 0 END) as present
            ')
            ->where('attendance_date >=', $startDate)
            ->where('attendance_date <=', $endDate)
            ->groupBy('attendance_date')
            ->orderBy('attendance_date', 'ASC')
            ->findAll();
        
        $data['chartLabels'] = array_map(function($item) {
            return date('d/m', strtotime($item->attendance_date));
        }, $dailyData);
        
        $data['chartPresent'] = array_column($dailyData, 'present');
        $data['chartTotal'] = array_column($dailyData, 'total');
        
        // Filters
        $data['classes'] = $this->classModel
            ->where('is_active', 1)
            ->orderBy('class_name', 'ASC')
            ->findAll();
        
        $data['disciplines'] = $this->disciplineModel
            ->where('is_active', 1)
            ->orderBy('discipline_name', 'ASC')
            ->findAll();
        
        $data['selectedClass'] = $classId;
        $data['selectedDiscipline'] = $disciplineId;
        $data['startDate'] = $startDate;
        $data['endDate'] = $endDate;
        
        return view('admin/reports/attendance/index', $data);
    }
    
    /**
     * Exams reports
     */
    public function exams()
    {
        $data['title'] = 'Relatórios de Exames';
        
        $examId = $this->request->getGet('exam');
        $classId = $this->request->getGet('class');
        $disciplineId = $this->request->getGet('discipline');
        $semesterId = $this->request->getGet('semester') ?: 
            ($this->semesterModel->getCurrent()->id ?? null);
        
        // Exam results summary
        $builder = $this->examResultModel
            ->select('
                tbl_exams.exam_name,
                tbl_exams.exam_date,
                tbl_classes.class_name,
                tbl_disciplines.discipline_name,
                COUNT(*) as total_students,
                AVG(score) as average_score,
                MIN(score) as min_score,
                MAX(score) as max_score,
                SUM(CASE WHEN score >= 10 THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN score < 10 THEN 1 ELSE 0 END) as failed
            ')
            ->join('tbl_exams', 'tbl_exams.id = tbl_exam_results.exam_id')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_exam_results.enrollment_id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exams.discipline_id')
            ->groupBy('tbl_exam_results.exam_id');
        
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
            ->findAll();
        
        // Filters
        $data['exams'] = $this->examModel
            ->orderBy('exam_date', 'DESC')
            ->findAll(50);
        
        $data['classes'] = $this->classModel
            ->where('is_active', 1)
            ->orderBy('class_name', 'ASC')
            ->findAll();
        
        $data['disciplines'] = $this->disciplineModel
            ->where('is_active', 1)
            ->orderBy('discipline_name', 'ASC')
            ->findAll();
        
        $data['semesters'] = $this->semesterModel
            ->where('is_active', 1)
            ->orderBy('start_date', 'DESC')
            ->findAll();
        
        $data['selectedExam'] = $examId;
        $data['selectedClass'] = $classId;
        $data['selectedDiscipline'] = $disciplineId;
        $data['selectedSemester'] = $semesterId;
        
        return view('admin/reports/exams/index', $data);
    }
    
    /**
     * Teachers reports
     */
    public function teachers()
    {
        $data['title'] = 'Relatórios de Professores';
        
        $academicYearId = $this->request->getGet('academic_year') ?: 
            ($this->academicYearModel->getCurrent()->id ?? null);
        
        // Teachers workload
        $teachers = $this->userModel
            ->select('
                tbl_users.id,
                tbl_users.first_name,
                tbl_users.last_name,
                tbl_users.email,
                tbl_users.phone,
                COUNT(DISTINCT tbl_class_disciplines.class_id) as total_classes,
                COUNT(tbl_class_disciplines.id) as total_disciplines,
                SUM(tbl_class_disciplines.workload_hours) as total_hours
            ')
            ->join('tbl_class_disciplines', 'tbl_class_disciplines.teacher_id = tbl_users.id', 'left')
            ->join('tbl_classes', 'tbl_classes.id = tbl_class_disciplines.class_id', 'left')
            ->where('tbl_users.user_type', 'teacher')
            ->where('tbl_users.is_active', 1)
            ->groupBy('tbl_users.id')
            ->orderBy('tbl_users.first_name', 'ASC')
            ->findAll();
        
        $data['teachers'] = $teachers;
        
        // Classes per teacher chart
        $chartData = [];
        foreach ($teachers as $teacher) {
            $chartData[] = [
                'teacher' => $teacher->first_name . ' ' . $teacher->last_name,
                'classes' => $teacher->total_classes
            ];
        }
        
        $data['chartTeachers'] = array_column($chartData, 'teacher');
        $data['chartClasses'] = array_column($chartData, 'classes');
        
        // Academic years for filter
        $data['academicYears'] = $this->academicYearModel
            ->orderBy('start_date', 'DESC')
            ->findAll();
        
        $data['selectedYear'] = $academicYearId;
        
        return view('admin/reports/teachers/index', $data);
    }
    
   /**
     * Fees reports - CORRIGIDO
     */
    public function fees()
    {
        $data['title'] = 'Relatórios de Propinas';
        
        $academicYearId = $this->request->getGet('academic_year') ?: 
            ($this->academicYearModel->getCurrent()->id ?? null);
        $classId = $this->request->getGet('class');
        $status = $this->request->getGet('status');
        
        // Fees summary by class
        $builder = $this->studentFeeModel
            ->select('
                tbl_classes.class_name,
                COUNT(*) as total_fees,
                SUM(tbl_student_fees.total_amount) as total_amount,
                SUM(CASE WHEN tbl_student_fees.status = "Pago" THEN tbl_student_fees.total_amount ELSE 0 END) as paid_amount,
                SUM(CASE WHEN tbl_student_fees.status IN ("Pendente", "Vencido") THEN tbl_student_fees.total_amount ELSE 0 END) as pending_amount,
                COUNT(CASE WHEN tbl_student_fees.status = "Pago" THEN 1 END) as paid_count,
                COUNT(CASE WHEN tbl_student_fees.status = "Pendente" THEN 1 END) as pending_count,
                COUNT(CASE WHEN tbl_student_fees.status = "Vencido" THEN 1 END) as overdue_count
            ')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_student_fees.enrollment_id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
            ->where('tbl_enrollments.academic_year_id', $academicYearId);
        
        if ($classId) {
            $builder->where('tbl_enrollments.class_id', $classId);
        }
        
        if ($status) {
            $builder->where('tbl_student_fees.status', $status);
        }
        
        $data['summary'] = $builder->groupBy('tbl_enrollments.class_id')
            ->orderBy('tbl_classes.class_name', 'ASC')
            ->findAll();
        
        // Monthly collection chart
        $monthlyData = $this->feePaymentModel
            ->select('
                MONTH(payment_date) as month,
                SUM(amount_paid) as total
            ')
            ->where('YEAR(payment_date)', date('Y'))
            ->groupBy('MONTH(payment_date)')
            ->orderBy('month', 'ASC')
            ->findAll();
        
        $months = ['Jan', 'Fev', 'Mar', 'Abr', 'Mai', 'Jun', 'Jul', 'Ago', 'Set', 'Out', 'Nov', 'Dez'];
        $collections = array_fill(0, 12, 0);
        
        foreach ($monthlyData as $item) {
            $collections[$item->month - 1] = $item->total;
        }
        
        $data['chartMonths'] = $months;
        $data['chartCollections'] = $collections;
        
        // Filters
        $data['academicYears'] = $this->academicYearModel
            ->orderBy('start_date', 'DESC')
            ->findAll();
        
        $data['classes'] = $this->classModel
            ->where('academic_year_id', $academicYearId)
            ->where('is_active', 1)
            ->orderBy('class_name', 'ASC')
            ->findAll();
        
        $data['selectedYear'] = $academicYearId;
        $data['selectedClass'] = $classId;
        $data['selectedStatus'] = $status;
        
        return view('admin/reports/fees/index', $data);
    }
}