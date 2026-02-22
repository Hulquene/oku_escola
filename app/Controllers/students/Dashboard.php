<?php
namespace App\Controllers\students;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use App\Models\EnrollmentModel;
use App\Models\AttendanceModel;
use App\Models\StudentFeeModel;
use App\Models\FeePaymentModel;
use App\Models\ExamScheduleModel;
use App\Models\ExamResultModel;

class Dashboard extends BaseController
{
    protected $studentModel;
    protected $enrollmentModel;
    protected $attendanceModel;
    protected $studentFeeModel;
    protected $feePaymentModel;
    protected $examScheduleModel;
    protected $examResultModel;
    
    public function __construct()
    {
        helper(['auth', 'student', 'student_new']);
        
        $this->studentModel = new StudentModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->attendanceModel = new AttendanceModel();
        $this->studentFeeModel = new StudentFeeModel();
        $this->feePaymentModel = new FeePaymentModel();
        $this->examScheduleModel = new ExamScheduleModel();
        $this->examResultModel = new ExamResultModel();
    }
    
    /**
     * Student dashboard
     */
    public function index()
    {
        $data['title'] = 'Dashboard do Aluno';
        
        $userId = currentUserId();
        $studentId = getStudentIdFromUser();
        
        if (!$studentId) {
            return redirect()->to('/auth/logout')->with('error', 'Perfil de aluno não encontrado');
        }
        
        // Buscar dados do aluno
        $student = $this->studentModel->getByUserId($userId);
        $data['student'] = $student;
        
        // Buscar matrícula atual com todas as informações
        $enrollment = $this->enrollmentModel->getCurrentForStudent($studentId);
        
        // Buscar informações completas da matrícula (curso, nível, professor, status)
        if ($enrollment) {
            $data['enrollment'] = $this->enrollmentModel
                ->select('
                    tbl_enrollments.*,
                    tbl_classes.class_name,
                    tbl_classes.class_code,
                    tbl_classes.class_shift,
                    tbl_classes.class_room,
                    tbl_academic_years.year_name,
                    tbl_grade_levels.level_name,
                    tbl_grade_levels.education_level,
                    tbl_courses.course_name,
                    tbl_courses.course_code,
                    tbl_courses.course_type,
                    CONCAT(tbl_teacher.first_name, " ", tbl_teacher.last_name) as teacher_name
                ')
                ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id', 'left')
                ->join('tbl_users as tbl_teacher', 'tbl_teacher.id = tbl_classes.class_teacher_id', 'left')
                ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_enrollments.academic_year_id', 'left')
                ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_enrollments.grade_level_id', 'left')
                ->join('tbl_courses', 'tbl_courses.id = tbl_enrollments.course_id', 'left')
                ->where('tbl_enrollments.id', $enrollment->id)
                ->first();
        } else {
            $data['enrollment'] = null;
        }
        
        if ($data['enrollment']) {
            // ✅ ADICIONADO: Log para debug do status
            log_message('debug', 'Status da matrícula: ' . ($data['enrollment']->status ?? 'NULL'));
            
            // Próximos exames
            $data['upcomingExams'] = $this->examScheduleModel
                ->select('
                    tbl_exam_schedules.*,
                    tbl_disciplines.discipline_name,
                    tbl_exam_boards.board_type,
                    tbl_exam_boards.board_name,
                    DATE_FORMAT(exam_date, "%d/%m/%Y") as formatted_date,
                    DATE_FORMAT(exam_time, "%H:%i") as formatted_time
                ')
                ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id')
                ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exam_schedules.exam_board_id')
                ->where('tbl_exam_schedules.class_id', $data['enrollment']->class_id)
                ->where('tbl_exam_schedules.exam_date >=', date('Y-m-d'))
                ->orderBy('tbl_exam_schedules.exam_date', 'ASC')
                ->limit(5)
                ->findAll();
            
            // Notas recentes
            $recentResults = $this->examResultModel
                ->select('
                    tbl_exam_results.*,
                    tbl_exam_schedules.discipline_id,
                    tbl_disciplines.discipline_name,
                    tbl_exam_boards.board_type,
                    tbl_exam_schedules.exam_date
                ')
                ->join('tbl_exam_schedules', 'tbl_exam_schedules.id = tbl_exam_results.exam_schedule_id')
                ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id')
                ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exam_schedules.exam_board_id')
                ->where('tbl_exam_results.enrollment_id', $data['enrollment']->id)
                ->orderBy('tbl_exam_results.recorded_at', 'DESC')
                ->limit(5)
                ->findAll();
            
            $data['recentGrades'] = [];
            foreach ($recentResults as $result) {
                $data['recentGrades'][] = (object)[
                    'discipline_name' => $result->discipline_name,
                    'score' => $result->score,
                    'board_type' => $result->board_type,
                    'exam_date' => $result->exam_date
                ];
            }
            
            // Propinas pendentes
            $data['pendingFees'] = $this->studentFeeModel
                ->select('
                    tbl_student_fees.*,
                    tbl_fee_types.type_name
                ')
                ->join('tbl_fee_structure', 'tbl_fee_structure.id = tbl_student_fees.fee_structure_id')
                ->join('tbl_fee_types', 'tbl_fee_types.id = tbl_fee_structure.fee_type_id')
                ->where('tbl_student_fees.enrollment_id', $data['enrollment']->id)
                ->whereIn('tbl_student_fees.status', ['Pendente', 'Vencido'])
                ->orderBy('tbl_student_fees.due_date', 'ASC')
                ->findAll();
            
            // Presenças do mês atual
            $attendanceStats = $this->attendanceModel
                ->select('
                    COUNT(*) as total,
                    SUM(CASE WHEN status IN ("Presente", "Atrasado", "Falta Justificada") THEN 1 ELSE 0 END) as present
                ')
                ->where('enrollment_id', $data['enrollment']->id)
                ->where('attendance_date >=', date('Y-m-01'))
                ->where('attendance_date <=', date('Y-m-t'))
                ->first();
            
            $data['attendance'] = (object)[
                'total' => $attendanceStats->total ?? 0,
                'present' => $attendanceStats->present ?? 0
            ];
            
            // Resumo financeiro
            $data['financialSummary'] = $this->getFinancialSummary($data['enrollment']->id);
            
            // Total de disciplinas do aluno
            $db = db_connect();
            $totalDisciplines = $db->table('tbl_class_disciplines')
                ->where('class_id', $data['enrollment']->class_id)
                ->countAllResults();
            $data['totalDisciplines'] = $totalDisciplines;
            
        } else {
            $data['upcomingExams'] = [];
            $data['recentGrades'] = [];
            $data['pendingFees'] = [];
            $data['attendance'] = (object)['total' => 0, 'present' => 0];
            $data['financialSummary'] = (object)[
                'total_due' => 0,
                'total_paid' => 0,
                'pending' => 0,
                'balance' => 0
            ];
            $data['totalDisciplines'] = 0;
        }
        
        // Estatísticas usando helpers
        $data['stats'] = [
            'average_grade' => getStudentAverageGrade($studentId),
            'attendance_percentage' => getStudentAttendancePercentage($studentId),
            'next_exam' => getNextStudentExam($studentId),
            'pending_fees_count' => count($data['pendingFees']) . ' pendente(s)',
            'total_disciplines' => $data['totalDisciplines']
        ];
        
        return view('students/dashboard/index', $data);
    }
    
    /**
     * Resumo financeiro do aluno
     */
    private function getFinancialSummary($enrollmentId)
    {
        // Total devido
        $totalDue = $this->studentFeeModel
            ->select('SUM(total_amount) as total')
            ->where('enrollment_id', $enrollmentId)
            ->first();
        
        // Total pago
        $totalPaid = $this->feePaymentModel
            ->select('SUM(amount_paid) as total')
            ->join('tbl_student_fees', 'tbl_student_fees.id = tbl_fee_payments.student_fee_id')
            ->where('tbl_student_fees.enrollment_id', $enrollmentId)
            ->first();
        
        // Pendências
        $pending = $this->studentFeeModel
            ->where('enrollment_id', $enrollmentId)
            ->whereIn('status', ['Pendente', 'Vencido'])
            ->countAllResults();
        
        return (object)[
            'total_due' => $totalDue->total ?? 0,
            'total_paid' => $totalPaid->total ?? 0,
            'pending' => $pending,
            'balance' => ($totalDue->total ?? 0) - ($totalPaid->total ?? 0)
        ];
    }
}