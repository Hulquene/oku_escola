<?php
namespace App\Controllers\students;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use App\Models\EnrollmentModel;
use App\Models\AttendanceModel;
use App\Models\SemesterModel;
use App\Models\DisciplineModel;

class Attendance extends BaseController
{
    protected $studentModel;
    protected $enrollmentModel;
    protected $attendanceModel;
    protected $semesterModel;
    protected $disciplineModel;
    
    public function __construct()
    {
        helper(['auth', 'student', 'student_new']);
        
        $this->studentModel = new StudentModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->attendanceModel = new AttendanceModel();
        $this->semesterModel = new SemesterModel();
        $this->disciplineModel = new DisciplineModel();
    }
    
    /**
     * Presenças do mês atual
     */
    public function index()
    {
        $data['title'] = 'Minhas Presenças';
        
        $studentId = getStudentIdFromUser();
        $enrollment = getStudentCurrentEnrollment($studentId);
        
        if (!$enrollment) {
            return redirect()->to('/students/dashboard')
                ->with('error', 'Nenhuma matrícula ativa encontrada');
        }
        
        $month = $this->request->getGet('month') ?: date('m');
        $year = $this->request->getGet('year') ?: date('Y');
        
        $startDate = "$year-$month-01";
        $endDate = date('Y-m-t', strtotime($startDate));
        
        // Buscar semestre atual para filtrar
        $currentSemester = $this->semesterModel->where('is_current', 1)->first();
        $semesterId = $currentSemester ? $currentSemester->id : null;
        
        // Presenças do mês
        $data['attendances'] = $this->attendanceModel
            ->select('
                tbl_attendance.*,
                tbl_disciplines.discipline_name,
                DATE_FORMAT(attendance_date, "%d/%m/%Y") as formatted_date
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_attendance.discipline_id', 'left')
            ->where('tbl_attendance.enrollment_id', $enrollment->id)
            ->where('tbl_attendance.attendance_date >=', $startDate)
            ->where('tbl_attendance.attendance_date <=', $endDate)
            ->orderBy('tbl_attendance.attendance_date', 'DESC')
            ->findAll();
        
        // Calcular estatísticas
        $stats = [
            'total' => 0,
            'present' => 0,
            'absent' => 0,
            'late' => 0,
            'justified' => 0,
            'rate' => 0
        ];
        
        foreach ($data['attendances'] as $att) {
            $stats['total']++;
            switch ($att->status) {
                case 'Presente':
                    $stats['present']++;
                    break;
                case 'Atrasado':
                    $stats['late']++;
                    break;
                case 'Falta Justificada':
                    $stats['justified']++;
                    break;
                case 'Ausente':
                    $stats['absent']++;
                    break;
            }
        }
        
        $stats['effective_present'] = $stats['present'] + $stats['late'] + $stats['justified'];
        $stats['rate'] = $stats['total'] > 0 
            ? round(($stats['effective_present'] / $stats['total']) * 100, 1) 
            : 0;
        
        $data['statistics'] = (object)$stats;
        $data['month'] = $month;
        $data['year'] = $year;
        $data['months'] = $this->getMonths();
        
        return view('students/attendance/index', $data);
    }
    
    /**
     * Histórico completo de presenças
     */
    public function history()
    {
        $data['title'] = 'Histórico de Presenças';
        
        $studentId = getStudentIdFromUser();
        $enrollment = getStudentCurrentEnrollment($studentId);
        
        if (!$enrollment) {
            return redirect()->to('/students/dashboard')
                ->with('error', 'Nenhuma matrícula ativa encontrada');
        }
        
        // Filtros
        $semesterId = $this->request->getGet('semester');
        $disciplineFilter = $this->request->getGet('discipline');
        $statusFilter = $this->request->getGet('status');
        $yearFilter = $this->request->getGet('year');
        
        // Construir query
        $builder = $this->attendanceModel
            ->select('
                tbl_attendance.*,
                tbl_disciplines.discipline_name,
                DATE_FORMAT(attendance_date, "%d/%m/%Y") as formatted_date
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_attendance.discipline_id', 'left')
            ->where('tbl_attendance.enrollment_id', $enrollment->id);
        
        // Aplicar filtros
        if ($semesterId) {
            $builder->where('tbl_attendance.semester_id', $semesterId);
        }
        
        if ($disciplineFilter) {
            $builder->where('tbl_disciplines.discipline_name', $disciplineFilter);
        }
        
        if ($statusFilter) {
            $builder->where('tbl_attendance.status', $statusFilter);
        }
        
        if ($yearFilter) {
            $builder->where('YEAR(tbl_attendance.attendance_date)', $yearFilter);
        }
        
        $data['attendances'] = $builder->orderBy('tbl_attendance.attendance_date', 'DESC')
            ->findAll();
        
        // Semestres para filtro
        $data['semesters'] = $this->semesterModel
            ->orderBy('start_date', 'DESC')
            ->findAll();
        
        $data['selectedSemester'] = $semesterId;
        $data['selectedDiscipline'] = $disciplineFilter;
        $data['selectedStatus'] = $statusFilter;
        $data['selectedYear'] = $yearFilter;
        
        return view('students/attendance/history', $data);
    }
    
    /**
     * Lista de meses
     */
    private function getMonths()
    {
        return [
            '01' => 'Janeiro',
            '02' => 'Fevereiro',
            '03' => 'Março',
            '04' => 'Abril',
            '05' => 'Maio',
            '06' => 'Junho',
            '07' => 'Julho',
            '08' => 'Agosto',
            '09' => 'Setembro',
            '10' => 'Outubro',
            '11' => 'Novembro',
            '12' => 'Dezembro'
        ];
    }
}