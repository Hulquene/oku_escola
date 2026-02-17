<?php

namespace App\Controllers\students;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use App\Models\EnrollmentModel;
use App\Models\AttendanceModel;
use App\Models\DisciplineModel;

class Attendance extends BaseController
{
    protected $studentModel;
    protected $enrollmentModel;
    protected $attendanceModel;
    protected $disciplineModel;
    
    public function __construct()
    {
        $this->studentModel = new StudentModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->attendanceModel = new AttendanceModel();
        $this->disciplineModel = new DisciplineModel();
        
        // Check if user is student
/*         if ($this->session->get('user_type') !== 'student') {
            return redirect()->to('/auth/login')->with('error', 'Acesso não autorizado');
        } */
    }
    
    /**
     * My attendance
     */
    public function index()
    {
        $data['title'] = 'Minhas Presenças';
           var_dump("teste 11");die;
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
        
        // Get attendance for the month
        $data['attendances'] = $this->attendanceModel
            ->select('tbl_attendance.*, tbl_disciplines.discipline_name')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_attendance.discipline_id', 'left')
            ->where('tbl_attendance.enrollment_id', $enrollment->id)
            ->where('tbl_attendance.attendance_date >=', $startDate)
            ->where('tbl_attendance.attendance_date <=', $endDate)
            ->orderBy('tbl_attendance.attendance_date', 'DESC')
            ->findAll();
        
        // Calculate statistics
        $total = count($data['attendances']);
        $present = 0;
        $absent = 0;
        $late = 0;
        $justified = 0;
        
        foreach ($data['attendances'] as $att) {
            switch ($att->status) {
                case 'Presente':
                    $present++;
                    break;
                case 'Ausente':
                    $absent++;
                    break;
                case 'Atrasado':
                    $late++;
                    break;
                case 'Falta Justificada':
                    $justified++;
                    break;
            }
        }
        
        $data['statistics'] = (object)[
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'late' => $late,
            'justified' => $justified,
            'rate' => $total > 0 ? round(($present / $total) * 100, 1) : 0
        ];
        
        $data['month'] = $month;
        $data['year'] = $year;
        $data['months'] = [
            '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março', '04' => 'Abril',
            '05' => 'Maio', '06' => 'Junho', '07' => 'Julho', '08' => 'Agosto',
            '09' => 'Setembro', '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
        ];
        
        return view('students/attendance/index', $data);
    }
    
    /**
     * Attendance history
     */
    public function history()
    {
        $data['title'] = 'Histórico de Presenças';
        
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
        
        $semesterId = $this->request->getGet('semester');
        
        // Get attendance by semester
        $builder = $this->attendanceModel
            ->select('tbl_attendance.*, tbl_disciplines.discipline_name')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_attendance.discipline_id', 'left')
            ->where('tbl_attendance.enrollment_id', $enrollment->id);
        
        if ($semesterId) {
            $builder->where('tbl_attendance.semester_id', $semesterId);
        }
        
        $data['attendances'] = $builder->orderBy('tbl_attendance.attendance_date', 'DESC')
            ->findAll();
        
        // Get semesters for filter
        $semesterModel = new \App\Models\SemesterModel();
        $data['semesters'] = $semesterModel
            ->where('academic_year_id', $enrollment->academic_year_id)
            ->orderBy('start_date', 'ASC')
            ->findAll();
        
        $data['selectedSemester'] = $semesterId;
        
        return view('students/attendance/history', $data);
    }
}