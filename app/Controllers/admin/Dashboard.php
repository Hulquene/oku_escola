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
use App\Models\FeePaymentModel;
use App\Models\GradeLevelModel;
use App\Models\CourseModel;
use App\Models\DisciplineModel;

class Dashboard extends BaseController
{
    protected $studentModel;
    protected $userModel;
    protected $enrollmentModel;
    protected $attendanceModel;
    protected $feeModel;
    protected $feePaymentModel;
    protected $classModel;
    protected $examScheduleModel;
    protected $examResultModel;
    protected $semesterModel;
    protected $academicYearModel;
    protected $gradeLevelModel;
    protected $courseModel;
    protected $disciplineModel;
    
    public function __construct()
    {
        $this->studentModel = new StudentModel();
        $this->userModel = new UserModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->attendanceModel = new AttendanceModel();
        $this->feeModel = new StudentFeeModel();
        $this->feePaymentModel = new FeePaymentModel();
        $this->classModel = new ClassModel();
        $this->examScheduleModel = new ExamScheduleModel();
        $this->examResultModel = new ExamResultModel();
        $this->semesterModel = new SemesterModel();
        $this->academicYearModel = new AcademicYearModel();
        $this->gradeLevelModel = new GradeLevelModel();
        $this->courseModel = new CourseModel();
        $this->disciplineModel = new DisciplineModel();
        
        helper(['permission', 'auth']);
    }
    
    /**
     * Admin dashboard
     */
    public function index()
    {
        $data['title'] = 'Dashboard';
        
        // ===== FILTRO POR ANO LETIVO =====
        $selectedYear = $this->request->getGet('academic_year') ?? current_academic_year();
        $data['selectedYear'] = $selectedYear;
        
        // Buscar anos letivos para o filtro
        $data['academicYears'] = $this->academicYearModel
            ->where('is_active', 1)
            ->orderBy('start_date', 'DESC')
            ->findAll();
        
        // Buscar dados do ano letivo selecionado
        $academicYear = $this->academicYearModel->find($selectedYear);
        $data['academicYear'] = $academicYear;
        
        // ===== ESTATÍSTICAS GERAIS DA ESCOLA =====
        if (can('students.list')) {
            $data['schoolStats'] = $this->getSchoolStatistics($selectedYear);
        }
        
        // ===== ESTATÍSTICAS PRINCIPAIS (com permissões) =====
        if (can('students.list')) {
            $data['totalStudents'] = $this->studentModel->where('is_active', 1)->countAllResults();
            $data['genderChart'] = $this->getGenderChartData($selectedYear);
        }
        
        if (can('teachers.list')) {
            $data['totalTeachers'] = $this->userModel
                ->where('user_type', 'teacher')
                ->where('is_active', 1)
                ->countAllResults();
        }
        
        if (can('users.list')) {
            $data['totalStaff'] = $this->userModel
                ->where('user_type', 'staff')
                ->where('is_active', 1)
                ->countAllResults();
        }
        
        // ===== MATRÍCULAS (com permissões) =====
        if (can('enrollments.list')) {
            $currentYear = $this->academicYearModel->getCurrent();
            $data['currentYear'] = $currentYear;
            
            if ($selectedYear) {
                $data['activeEnrollments'] = $this->enrollmentModel
                    ->where('academic_year_id', $selectedYear)
                    ->where('status', 'Ativo')
                    ->countAllResults();
            } else {
                $data['activeEnrollments'] = 0;
            }
            
            // Matrículas recentes
            $data['recentEnrollments'] = $this->enrollmentModel
                ->select('tbl_enrollments.*, tbl_users.first_name, tbl_users.last_name, tbl_classes.class_name, tbl_grade_levels.level_name')
                ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
                ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
                ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id', 'left')
                ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id', 'left')
                ->orderBy('tbl_enrollments.created_at', 'DESC')
                ->limit(10)
                ->findAll();
        }
        
        // ===== ESTATÍSTICAS FINANCEIRAS (com permissões) =====
        if (can('financial.dashboard')) {
            $data['feeStats'] = $this->getFeeStatistics();
            $data['currentMonthPayments'] = $this->getCurrentMonthPayments();
            $data['overduePayments'] = $this->getOverduePayments();
            $data['feeChart'] = $this->getFeeChartData();
        }
        
        // ===== PRESENÇAS (com permissões) =====
        if (can('attendance.statistics')) {
            $data['attendanceToday'] = $this->attendanceModel
                ->where('attendance_date', date('Y-m-d'))
                ->countAllResults();
            
            $data['attendanceRate'] = $this->getAttendanceRate();
            $data['attendanceChart'] = $this->getAttendanceChartData();
        }
        
        // ===== EXAMES (com permissões) =====
        if (can('exams.calendar')) {
            $data['upcomingExams'] = $this->examScheduleModel
                ->select('tbl_exam_schedules.*, tbl_classes.class_name, tbl_disciplines.discipline_name, tbl_exam_boards.board_name')
                ->join('tbl_classes', 'tbl_classes.id = tbl_exam_schedules.class_id', 'left')
                ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id', 'left')
                ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exam_schedules.exam_board_id', 'left')
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
        }
        
        // ===== TURMAS (com permissões) =====
        if (can('classes.list')) {
            $data['totalClasses'] = $this->classModel->where('is_active', 1)->countAllResults();
            $data['classesByShift'] = $this->getClassesByShift();
        }
        
        // ===== DESEMPENHO ACADÊMICO (com permissões) =====
        if (can('grades.view_averages')) {
            $data['performanceChart'] = $this->getPerformanceChartData();
        }
        
        // ===== GRÁFICOS COMUNS =====
        $data['enrollmentChart'] = $this->getEnrollmentChartData();

        // ===== NOVOS GRÁFICOS =====
if (can('courses.list')) {
    $data['totalCourses'] = $this->courseModel->where('is_active', 1)->countAllResults();
    $data['totalCourseTypes'] = $this->courseModel->countDistinct('course_type');
    $data['courseTypeChart'] = $this->getCourseTypeChartData();
    $data['courseLevelChart'] = $this->getCourseLevelChartData($selectedYear);
}

if (can('subjects.list')) {
    $data['totalDisciplines'] = $this->disciplineModel->where('is_active', 1)->countAllResults();
    $data['avgWorkload'] = $this->disciplineModel->selectAvg('workload_hours')->first()->workload_hours ?? 0;
    $data['totalWorkload'] = $this->disciplineModel->selectSum('workload_hours')->first()->workload_hours ?? 0;
    $data['disciplineLevelChart'] = $this->getDisciplineLevelChartData();
    $data['workloadChart'] = $this->getWorkloadChartData();
    $data['topDisciplinesChart'] = $this->getTopDisciplinesChartData();
}

if (can('enrollments.list')) {
    $data['totalEnrollments'] = $this->enrollmentModel->where('status', 'active')->countAllResults();
    $data['enrollmentStatusChart'] = $this->getEnrollmentStatusChartData($selectedYear);
}

if (can('classes.list')) {
    $data['totalCapacity'] = $this->classModel->selectSum('capacity')->first()->capacity ?? 0;
    $data['classesWithTeacher'] = $this->classModel->where('class_teacher_id IS NOT NULL')->countAllResults();
    $data['classLevelChart'] = $this->getClassLevelChartData($selectedYear);
    
    // Cálculo da média de alunos por turma
    $totalStudents = $data['activeEnrollments'] ?? 0;
    $totalClasses = $data['totalClasses'] ?? 1;
    $data['avgStudentsPerClass'] = $totalClasses > 0 ? round($totalStudents / $totalClasses, 1) : 0;
    
    // Taxa de ocupação
    $totalCapacity = $data['totalCapacity'] ?? 1;
    $data['occupancyRate'] = $totalCapacity > 0 ? round(($totalStudents / $totalCapacity) * 100, 1) : 0;
}

if (can('grades.view_averages')) {
    $data['semesterResultsChart'] = $this->getSemesterResultsChartData();
}

// Proporção de gênero
if (!empty($data['genderChart'])) {
    $male = $data['genderChart']['male'] ?? 0;
    $female = $data['genderChart']['female'] ?? 1;
    $ratio = $female > 0 ? round($male / $female, 1) : 1;
    $data['genderRatio'] = $ratio . ':' . '1';
}

// Taxas de aprovação/reprovação
if (!empty($data['schoolStats']['totals'])) {
    $totalAvaliados = $data['schoolStats']['totals']['avaliados_total'] ?? 1;
    $totalAprovados = $data['schoolStats']['totals']['aprovados_total'] ?? 0;
    $totalReprovados = $data['schoolStats']['totals']['reprovados_total'] ?? 0;
    
    $data['approvalRate'] = $totalAvaliados > 0 ? round(($totalAprovados / $totalAvaliados) * 100, 1) : 0;
    $data['failureRate'] = $totalAvaliados > 0 ? round(($totalReprovados / $totalAvaliados) * 100, 1) : 0;
}

// Média alunos por professor
$data['studentsPerTeacher'] = ($data['totalTeachers'] ?? 0) > 0 
    ? round(($data['totalStudents'] ?? 0) / ($data['totalTeachers'] ?? 1), 1) 
    : 0;
        
        return view('admin/dashboard/index', $data);
    }
    
    /**
     * Get school statistics by academic year
     */
    protected function getSchoolStatistics($academicYearId)
    {
        $db = db_connect();
        
        // Buscar todos os cursos
        $courses = $this->courseModel->getActive();
        $stats = [];
        
        foreach ($courses as $course) {
            // Buscar turmas por nível para cada curso
            $levels = $this->gradeLevelModel
                ->where('education_level', 'Ensino Médio')
                ->orderBy('grade_number', 'ASC')
                ->findAll();
            
            foreach ($levels as $level) {
                // Buscar turmas deste curso e nível
                $classes = $this->classModel
                    ->where('course_id', $course->id)
                    ->where('grade_level_id', $level->id)
                    ->where('academic_year_id', $academicYearId)
                    ->where('is_active', 1)
                    ->findAll();
                
                $totalClasses = count($classes);
                
                // Inicializar contadores
                $totalMale = 0;
                $totalFemale = 0;
                $desistentesMale = 0;
                $desistentesFemale = 0;
                $aprovadosMale = 0;
                $aprovadosFemale = 0;
                $reprovadosMale = 0;
                $reprovadosFemale = 0;
                
                foreach ($classes as $class) {
                    // Buscar matrículas ativas na turma
                    $enrollments = $this->enrollmentModel
                        ->select('tbl_enrollments.*, tbl_students.gender')
                        ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
                        ->where('tbl_enrollments.class_id', $class->id)
                        ->where('tbl_enrollments.status', 'Ativo')
                        ->findAll();
                    
                    foreach ($enrollments as $enroll) {
                        if ($enroll->gender == 'Masculino') {
                            $totalMale++;
                        } else {
                            $totalFemale++;
                        }
                    }
                    
                    // Buscar desistentes (status diferente de Ativo e Pendente)
                    $desistentes = $this->enrollmentModel
                        ->select('tbl_enrollments.*, tbl_students.gender')
                        ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
                        ->where('tbl_enrollments.class_id', $class->id)
                        ->whereNotIn('tbl_enrollments.status', ['Ativo', 'Pendente'])
                        ->findAll();
                    
                    foreach ($desistentes as $des) {
                        if ($des->gender == 'Masculino') {
                            $desistentesMale++;
                        } else {
                            $desistentesFemale++;
                        }
                    }
                    
                    // Para resultados, precisaríamos de uma tabela de resultados
                    // Por enquanto, vamos simular ou deixar vazio
                    // Isso seria substituído por dados reais de tbl_semester_results
                }
                
                // Avaliados = total - desistentes
                $avaliadosMale = $totalMale - $desistentesMale;
                $avaliadosFemale = $totalFemale - $desistentesFemale;
                
                $stats[] = [
                    'course_name' => $course->course_name,
                    'course_code' => $course->course_code,
                    'level_name' => $level->level_name,
                    'grade_number' => $level->grade_number,
                    'total_classes' => $totalClasses,
                    'total_male' => $totalMale,
                    'total_female' => $totalFemale,
                    'total' => $totalMale + $totalFemale,
                    'desistentes_male' => $desistentesMale,
                    'desistentes_female' => $desistentesFemale,
                    'desistentes_total' => $desistentesMale + $desistentesFemale,
                    'avaliados_male' => $avaliadosMale,
                    'avaliados_female' => $avaliadosFemale,
                    'avaliados_total' => $avaliadosMale + $avaliadosFemale,
                    'aprovados_male' => 0, // Placeholder
                    'aprovados_female' => 0, // Placeholder
                    'aprovados_total' => 0, // Placeholder
                    'reprovados_male' => 0, // Placeholder
                    'reprovados_female' => 0, // Placeholder
                    'reprovados_total' => 0, // Placeholder
                ];
            }
        }
        
        // Totais gerais
        $totals = [
            'total_classes' => array_sum(array_column($stats, 'total_classes')),
            'total_male' => array_sum(array_column($stats, 'total_male')),
            'total_female' => array_sum(array_column($stats, 'total_female')),
            'total' => array_sum(array_column($stats, 'total')),
            'desistentes_male' => array_sum(array_column($stats, 'desistentes_male')),
            'desistentes_female' => array_sum(array_column($stats, 'desistentes_female')),
            'desistentes_total' => array_sum(array_column($stats, 'desistentes_total')),
            'avaliados_male' => array_sum(array_column($stats, 'avaliados_male')),
            'avaliados_female' => array_sum(array_column($stats, 'avaliados_female')),
            'avaliados_total' => array_sum(array_column($stats, 'avaliados_total')),
        ];
        
        return [
            'details' => $stats,
            'totals' => $totals
        ];
    }
    
    /**
     * Get fee statistics
     */
    protected function getFeeStatistics()
    {
        $db = db_connect();
        
        $totalResult = $db->query("
            SELECT SUM(total_amount) as total 
            FROM tbl_student_fees 
            WHERE status != 'Cancelado'
        ")->getRow();
        
        $paidResult = $db->query("
            SELECT SUM(amount_paid) as total 
            FROM tbl_fee_payments fp
            JOIN tbl_student_fees sf ON sf.id = fp.student_fee_id
            WHERE sf.status != 'Cancelado'
        ")->getRow();
        
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
     * Get current month payments
     */
    protected function getCurrentMonthPayments()
    {
        $result = $this->feePaymentModel
            ->select('SUM(amount_paid) as total')
            ->where('MONTH(payment_date)', date('m'))
            ->where('YEAR(payment_date)', date('Y'))
            ->get()
            ->getRow();
        
        return $result->total ?? 0;
    }
    
    /**
     * Get overdue payments count
     */
    protected function getOverduePayments()
    {
        return $this->feeModel
            ->where('status', 'Pendente')
            ->where('due_date <', date('Y-m-d'))
            ->countAllResults();
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
            
            $collectedResult = $this->feePaymentModel
                ->select('SUM(amount_paid) as total')
                ->where('MONTH(payment_date)', date('m', strtotime($month)))
                ->where('YEAR(payment_date)', date('Y', strtotime($month)))
                ->get()
                ->getRow();
            
            $collected[] = $collectedResult->total ?? 0;
            
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
    protected function getGenderChartData($academicYearId = null)
    {
        if (!$academicYearId) {
            $currentYear = $this->academicYearModel->getCurrent();
            $academicYearId = $currentYear ? $currentYear['id'] : null;
        }
        
        if (!$academicYearId) {
            return [
                'male' => 0,
                'female' => 0
            ];
        }
        
        $male = $this->enrollmentModel
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->where('tbl_enrollments.academic_year_id', $academicYearId)
            ->where('tbl_enrollments.status', 'Ativo')
            ->where('tbl_students.gender', 'Masculino')
            ->countAllResults();
        
        $female = $this->enrollmentModel
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->where('tbl_enrollments.academic_year_id', $academicYearId)
            ->where('tbl_enrollments.status', 'Ativo')
            ->where('tbl_students.gender', 'Feminino')
            ->countAllResults();
        
        return [
            'male' => $male,
            'female' => $female
        ];
    }
    
    /**
     * Get student performance chart data
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
                SUM(CASE WHEN overall_average >= 17 THEN 1 ELSE 0 END) as excelente,
                SUM(CASE WHEN overall_average >= 14 AND overall_average < 17 THEN 1 ELSE 0 END) as bom,
                SUM(CASE WHEN overall_average >= 10 AND overall_average < 14 THEN 1 ELSE 0 END) as satisfatorio,
                SUM(CASE WHEN overall_average < 10 THEN 1 ELSE 0 END) as insuficiente
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
    // ===== NOVOS MÉTODOS PARA GRÁFICOS =====

/**
 * Get course type distribution chart data
 */
protected function getCourseTypeChartData()
{
    $db = db_connect();
    
    $result = $db->query("
        SELECT 
            course_type,
            COUNT(*) as total
        FROM tbl_courses
        WHERE is_active = 1
        GROUP BY course_type
        ORDER BY total DESC
    ")->getResult();
    
    $labels = [];
    $counts = [];
    
    foreach ($result as $row) {
        $labels[] = $row->course_type;
        $counts[] = $row->total;
    }
    
    return [
        'labels' => $labels,
        'counts' => $counts
    ];
}

/**
 * Get disciplines by education level chart data
 */
protected function getDisciplineLevelChartData()
{
    $db = db_connect();
    
    $result = $db->query("
        SELECT 
            gl.education_level,
            COUNT(DISTINCT cd.discipline_id) as total
        FROM tbl_grade_levels gl
        LEFT JOIN tbl_grade_disciplines cd ON cd.grade_level_id = gl.id
        WHERE gl.is_active = 1
        GROUP BY gl.education_level
        ORDER BY FIELD(gl.education_level, 'Iniciação', 'Primário', '1º Ciclo', '2º Ciclo', 'Ensino Médio')
    ")->getResult();
    
    $labels = [];
    $counts = [];
    
    foreach ($result as $row) {
        $labels[] = $row->education_level;
        $counts[] = $row->total;
    }
    
    return [
        'labels' => $labels,
        'counts' => $counts
    ];
}

/**
 * Get classes by education level chart data
 */
protected function getClassLevelChartData($academicYearId = null)
{
    $db = db_connect();
    
    $sql = "
        SELECT 
            gl.education_level,
            COUNT(DISTINCT c.id) as total
        FROM tbl_grade_levels gl
        LEFT JOIN tbl_classes c ON c.grade_level_id = gl.id
        WHERE gl.is_active = 1
    ";
    
    if ($academicYearId) {
        $sql .= " AND c.academic_year_id = " . $academicYearId;
    }
    
    $sql .= " GROUP BY gl.education_level
              ORDER BY FIELD(gl.education_level, 'Iniciação', 'Primário', '1º Ciclo', '2º Ciclo', 'Ensino Médio')";
    
    $result = $db->query($sql)->getResult();
    
    $labels = [];
    $counts = [];
    
    foreach ($result as $row) {
        $labels[] = $row->education_level;
        $counts[] = $row->total;
    }
    
    return [
        'labels' => $labels,
        'counts' => $counts
    ];
}

/**
 * Get workload distribution chart data
 */
protected function getWorkloadChartData()
{
    $db = db_connect();
    
    $result = $db->query("
        SELECT 
            CASE 
                WHEN workload_hours <= 60 THEN 'Até 60h'
                WHEN workload_hours <= 90 THEN '61-90h'
                WHEN workload_hours <= 120 THEN '91-120h'
                ELSE 'Mais de 120h'
            END as workload_range,
            COUNT(*) as total
        FROM tbl_disciplines
        WHERE is_active = 1
        GROUP BY workload_range
        ORDER BY workload_range
    ")->getResult();
    
    $labels = [];
    $counts = [];
    
    foreach ($result as $row) {
        $labels[] = $row->workload_range;
        $counts[] = $row->total;
    }
    
    return [
        'labels' => $labels,
        'data' => $counts
    ];
}

/**
 * Get top 10 disciplines by workload
 */
protected function getTopDisciplinesChartData($limit = 10)
{
    $db = db_connect();
    
    $result = $db->query("
        SELECT 
            discipline_name,
            workload_hours
        FROM tbl_disciplines
        WHERE is_active = 1
        ORDER BY workload_hours DESC
        LIMIT $limit
    ")->getResult();
    
    $labels = [];
    $data = [];
    
    foreach ($result as $row) {
        $labels[] = $row->discipline_name;
        $data[] = $row->workload_hours;
    }
    
    return [
        'labels' => $labels,
        'data' => $data
    ];
}

/**
 * Get semester results chart data
 */
protected function getSemesterResultsChartData()
{
    $db = db_connect();
    
    $result = $db->query("
        SELECT 
            s.semester_name,
            s.semester_type,
            SUM(CASE WHEN sr.status = 'Aprovado' THEN 1 ELSE 0 END) as approved,
            SUM(CASE WHEN sr.status = 'Reprovado' THEN 1 ELSE 0 END) as failed,
            SUM(CASE WHEN sr.status = 'Recurso' THEN 1 ELSE 0 END) as appeal
        FROM tbl_semesters s
        LEFT JOIN tbl_semester_results sr ON sr.semester_id = s.id
        WHERE s.status = 'Ativo'
        GROUP BY s.id
        ORDER BY s.start_date DESC
        LIMIT 6
    ")->getResult();
    
    $labels = [];
    $approved = [];
    $failed = [];
    $appeal = [];
    
    foreach ($result as $row) {
        $labels[] = $row->semester_name;
        $approved[] = $row->approved;
        $failed[] = $row->failed;
        $appeal[] = $row->appeal;
    }
    
    return [
        'labels' => $labels,
        'approved' => $approved,
        'failed' => $failed,
        'appeal' => $appeal
    ];
}

/**
 * Get enrollment status distribution chart data
 */
protected function getEnrollmentStatusChartData($academicYearId = null)
{
    $db = db_connect();
    
    $sql = "
        SELECT 
            status,
            COUNT(*) as total
        FROM tbl_enrollments
        WHERE 1=1
    ";
    
    if ($academicYearId) {
        $sql .= " AND academic_year_id = " . $academicYearId;
    }
    
    $sql .= " GROUP BY status";
    
    $result = $db->query($sql)->getResult();
    
    $labels = [];
    $data = [];
    
    foreach ($result as $row) {
        $labels[] = $row->status;
        $data[] = $row->total;
    }
    
    return [
        'labels' => $labels,
        'data' => $data
    ];
}

/**
 * Get course level distribution chart data
 */
protected function getCourseLevelChartData($academicYearId = null)
{
    $db = db_connect();
    
    $sql = "
        SELECT 
            c.course_name,
            gl.level_name,
            SUM(CASE WHEN s.gender = 'Masculino' THEN 1 ELSE 0 END) as male,
            SUM(CASE WHEN s.gender = 'Feminino' THEN 1 ELSE 0 END) as female
        FROM tbl_courses c
        LEFT JOIN tbl_enrollments e ON e.course_id = c.id
        LEFT JOIN tbl_students s ON s.id = e.student_id
        LEFT JOIN tbl_grade_levels gl ON gl.id = e.grade_level_id
        WHERE c.is_active = 1
    ";
    
    if ($academicYearId) {
        $sql .= " AND e.academic_year_id = " . $academicYearId;
    }
    
    $sql .= " GROUP BY c.id, gl.id
              ORDER BY c.course_name, gl.grade_number";
    
    $result = $db->query($sql)->getResult();
    
    $labels = [];
    $male = [];
    $female = [];
    
    foreach ($result as $row) {
        $labels[] = $row->course_name . ' - ' . $row->level_name;
        $male[] = $row->male;
        $female[] = $row->female;
    }
    
    return [
        'labels' => $labels,
        'male' => $male,
        'female' => $female
    ];
}
}