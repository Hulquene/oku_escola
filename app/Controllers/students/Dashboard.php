<?php

namespace App\Controllers\students;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use App\Models\EnrollmentModel;
use App\Models\AttendanceModel;
use App\Models\FinalGradeModel;
use App\Models\StudentFeeModel;
use App\Models\FeePaymentModel;

class Dashboard extends BaseController
{
    protected $studentModel;
    protected $enrollmentModel;
    protected $attendanceModel;
    protected $finalGradeModel;
    protected $studentFeeModel;
    protected $feePaymentModel;
    
    public function __construct()
    {
        // Carregar helpers necessários
        helper(['auth', 'student']);
        
        $this->studentModel = new StudentModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->attendanceModel = new AttendanceModel();
        $this->finalGradeModel = new FinalGradeModel();
        $this->studentFeeModel = new StudentFeeModel();
        $this->feePaymentModel = new FeePaymentModel();
    }
    
    /**
     * Student dashboard
     */
    public function index()
    {
        $data['title'] = 'Dashboard do Aluno';
        
        // Usar os helpers para obter dados do usuário
        $userId = currentUserId();
        $studentId = getStudentIdFromUser();
        $enrollmentId = getStudentEnrollmentId($studentId);
        
        if (!$studentId) {
            return redirect()->to('/auth/logout')->with('error', 'Perfil de aluno não encontrado');
        }
        
        // Buscar dados do aluno
        $student = $this->studentModel->getByUserId($userId);
        $data['student'] = $student;
        
        // Buscar matrícula atual
        $data['currentEnrollment'] = $this->enrollmentModel->getCurrentForStudent($studentId);
        
        // Buscar semestre ativo
        $semesterModel = new \App\Models\SemesterModel();
        $currentSemester = $semesterModel->where('is_active', 1)->first();
        
        if ($data['currentEnrollment']) {
            // Notas recentes
            $data['recentGrades'] = $this->finalGradeModel
                ->select('
                    tbl_final_grades.final_score as score,
                    tbl_final_grades.status,
                    tbl_final_grades.calculated_at,
                    tbl_disciplines.discipline_name
                ')
                ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_final_grades.discipline_id')
                ->where('tbl_final_grades.enrollment_id', $data['currentEnrollment']->id)
                ->where('tbl_final_grades.semester_id', $currentSemester->id ?? 0)
                ->orderBy('tbl_final_grades.calculated_at', 'DESC')
                ->limit(5)
                ->findAll();
            
            // Propinas pendentes usando StudentFeeModel
            $data['pendingFees'] = $this->studentFeeModel
                ->select('
                    tbl_student_fees.*,
                    tbl_fee_types.type_name,
                    tbl_fee_types.type_category
                ')
                ->join('tbl_fee_structure', 'tbl_fee_structure.id = tbl_student_fees.fee_structure_id')
                ->join('tbl_fee_types', 'tbl_fee_types.id = tbl_fee_structure.fee_type_id')
                ->where('tbl_student_fees.enrollment_id', $data['currentEnrollment']->id)
                ->whereIn('tbl_student_fees.status', ['Pendente', 'Vencido'])
                ->orderBy('tbl_student_fees.due_date', 'ASC')
                ->findAll();
            
            // Últimos pagamentos
            $data['recentPayments'] = $this->feePaymentModel
                ->select('
                    tbl_fee_payments.*,
                    tbl_student_fees.reference_number,
                    tbl_fee_types.type_name
                ')
                ->join('tbl_student_fees', 'tbl_student_fees.id = tbl_fee_payments.student_fee_id')
                ->join('tbl_fee_structure', 'tbl_fee_structure.id = tbl_student_fees.fee_structure_id')
                ->join('tbl_fee_types', 'tbl_fee_types.id = tbl_fee_structure.fee_type_id')
                ->where('tbl_student_fees.enrollment_id', $data['currentEnrollment']->id)
                ->orderBy('tbl_fee_payments.payment_date', 'DESC')
                ->limit(3)
                ->findAll();
            
            // Presenças do mês atual
            $data['attendance'] = $this->attendanceModel
                ->select('
                    COUNT(*) as total,
                    SUM(CASE WHEN status IN ("Presente", "Atrasado", "Falta Justificada") THEN 1 ELSE 0 END) as present
                ')
                ->where('enrollment_id', $data['currentEnrollment']->id)
                ->where('attendance_date >=', date('Y-m-01'))
                ->where('attendance_date <=', date('Y-m-t'))
                ->first();
            
            // Resumo financeiro
            $data['financialSummary'] = $this->getFinancialSummary($data['currentEnrollment']->id);
            
            // Calcular média real das notas
            $data['averageGrade'] = $this->calculateAverageGrade($data['currentEnrollment']->id, $currentSemester->id ?? null);
        } else {
            $data['recentGrades'] = [];
            $data['pendingFees'] = [];
            $data['recentPayments'] = [];
            $data['attendance'] = (object)['total' => 0, 'present' => 0];
            $data['financialSummary'] = (object)['total_due' => 0, 'total_paid' => 0, 'pending' => 0];
            $data['averageGrade'] = '-';
        }
        
        // Estatísticas usando os helpers (fallback)
        $data['stats'] = [
            'average_grade' => $data['averageGrade'],
            'attendance_percentage' => getStudentAttendancePercentage($studentId),
            'next_exam' => getNextStudentExam($studentId),
            'pending_fees_count' => count($data['pendingFees']) . ' pendente(s)'
        ];
        
        // Disciplinas do aluno
        $data['disciplines'] = getStudentDisciplines($studentId);
        
        // Últimas atividades
        $data['recentActivities'] = $this->getRecentActivities(
            $studentId, 
            $data['currentEnrollment']->id ?? null,
            $data['recentGrades'],
            $data['recentPayments']
        );
        
        return view('students/dashboard/index', $data);
    }
    
    /**
     * Calcular média real das notas
     */
    private function calculateAverageGrade($enrollmentId, $semesterId = null)
    {
        $builder = $this->finalGradeModel
            ->select('AVG(final_score) as average')
            ->where('enrollment_id', $enrollmentId);
        
        if ($semesterId) {
            $builder->where('semester_id', $semesterId);
        }
        
        $result = $builder->first();
        
        if ($result && $result->average > 0) {
            return number_format($result->average, 1, ',', '.');
        }
        
        return '-';
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
    
    /**
     * Busca atividades recentes do aluno
     */
    private function getRecentActivities($studentId, $enrollmentId = null, $recentGrades = [], $recentPayments = [])
    {
        $activities = [];
        
        // Atividades de notas
        foreach ($recentGrades as $grade) {
            $activities[] = [
                'type' => 'grade',
                'date' => $grade->calculated_at,
                'title' => 'Nova nota publicada',
                'description' => $grade->discipline_name,
                'value' => number_format($grade->score, 1, ',', '.'),
                'icon' => 'fa-star',
                'color' => $grade->score >= 10 ? 'success' : 'danger'
            ];
        }
        
        // Atividades de pagamentos
        foreach ($recentPayments as $payment) {
            $activities[] = [
                'type' => 'payment',
                'date' => $payment->payment_date,
                'title' => 'Pagamento registrado',
                'description' => $payment->type_name . ' - ' . $payment->reference_number,
                'value' => number_format($payment->amount_paid, 2, ',', '.') . ' Kz',
                'icon' => 'fa-money-bill',
                'color' => 'success'
            ];
        }
        
        // Presenças recentes (se houver enrollment)
        if ($enrollmentId) {
            try {
                $attendances = $this->attendanceModel
                    ->select('
                        attendance_date as date,
                        status,
                        tbl_disciplines.discipline_name as description
                    ')
                    ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_attendance.discipline_id', 'left')
                    ->where('enrollment_id', $enrollmentId)
                    ->orderBy('attendance_date', 'DESC')
                    ->limit(3)
                    ->findAll();
                
                foreach ($attendances as $attendance) {
                    $activities[] = [
                        'type' => 'attendance',
                        'date' => $attendance->date,
                        'title' => 'Presença registrada',
                        'description' => $attendance->description ?: 'Aula',
                        'status' => $attendance->status,
                        'icon' => 'fa-calendar-check',
                        'color' => $attendance->status == 'Presente' ? 'success' : 'warning'
                    ];
                }
            } catch (\Exception $e) {
                log_message('debug', 'Erro ao buscar presenças: ' . $e->getMessage());
            }
        }
        
        // Ordenar por data (mais recente primeiro)
        usort($activities, function($a, $b) {
            return strtotime($b['date']) - strtotime($a['date']);
        });
        
        return array_slice($activities, 0, 8);
    }
}