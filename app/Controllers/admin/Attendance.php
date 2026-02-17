<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\AttendanceModel;
use App\Models\EnrollmentModel;
use App\Models\ClassModel;
use App\Models\DisciplineModel;
use App\Models\ClassDisciplineModel;
use App\Models\StudentModel;

class Attendance extends BaseController
{
    protected $attendanceModel;
    protected $enrollmentModel;
    protected $classModel;
    protected $disciplineModel;
    protected $classDisciplineModel;
    protected $studentModel;
    
    public function __construct()
    {
        $this->attendanceModel = new AttendanceModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->classModel = new ClassModel();
        $this->disciplineModel = new DisciplineModel();
        $this->classDisciplineModel = new ClassDisciplineModel();
        $this->studentModel = new StudentModel();
    }
    
    /**
     * Attendance page
     */
    public function index()
    {
        $data['title'] = 'Registro de Presenças';
        
        $classId = $this->request->getGet('class_id');
        $disciplineId = $this->request->getGet('discipline_id');
        $attendanceDate = $this->request->getGet('attendance_date') ?: date('Y-m-d');
        
        // Get all active classes for filter
        $data['classes'] = $this->classModel
            ->select('tbl_classes.*, tbl_academic_years.year_name')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_classes.academic_year_id')
            ->where('tbl_classes.is_active', 1)
            ->orderBy('tbl_classes.class_name', 'ASC')
            ->findAll();
        
        $data['selectedClass'] = $classId;
        $data['selectedDiscipline'] = $disciplineId;
        $data['selectedDate'] = $attendanceDate;
        
        // If class selected, get students
        if ($classId) {
            $students = $this->enrollmentModel
                ->select('
                    tbl_enrollments.id as enrollment_id,
                    tbl_students.id,
                    tbl_students.student_number,
                    tbl_users.first_name,
                    tbl_users.last_name,
                    tbl_attendance.status as attendance_status,
                    tbl_attendance.justification as attendance_justification
                ')
                ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
                ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
                ->join('tbl_attendance', 'tbl_attendance.enrollment_id = tbl_enrollments.id AND tbl_attendance.attendance_date = "' . $attendanceDate . '" AND tbl_attendance.discipline_id ' . ($disciplineId ? '= ' . $disciplineId : 'IS NULL'), 'left')
                ->where('tbl_enrollments.class_id', $classId)
                ->where('tbl_enrollments.status', 'Ativo')
                ->orderBy('tbl_users.first_name', 'ASC')
                ->findAll();
            
            $data['students'] = $students;
        }
        
        return view('admin/students/attendance/index', $data);
    }
    
    /**
     * Save attendance (AJAX)
     */
    public function save()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Requisição inválida']);
        }
        
        $classId = $this->request->getPost('class_id');
        $disciplineId = $this->request->getPost('discipline_id') ?: null;
        $attendanceDate = $this->request->getPost('attendance_date');
        $attendances = $this->request->getPost('attendance');
        
        if (!$classId || !$attendanceDate || empty($attendances)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Dados incompletos'
            ]);
        }
        
        $data = [];
        foreach ($attendances as $enrollmentId => $attendance) {
            if (isset($attendance['status'])) {
                $data[] = [
                    'enrollment_id' => $enrollmentId,
                    'class_id' => $classId,
                    'discipline_id' => $disciplineId,
                    'attendance_date' => $attendanceDate,
                    'status' => $attendance['status'],
                    'justification' => $attendance['justification'] ?? null,
                    'marked_by' => $this->session->get('user_id')
                ];
            }
        }
        
        $result = $this->attendanceModel->markBulk($data);
        
        if ($result) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Presenças registradas com sucesso'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao registrar presenças'
            ]);
        }
    }
    
    /**
     * Attendance report
     */
    public function report()
    {
        $data['title'] = 'Relatório de Presenças';
        
        $studentId = $this->request->getGet('student_id');
        $classId = $this->request->getGet('class_id');
        $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?: date('Y-m-t');
        
        // Get all students for filter
        $data['students'] = $this->studentModel
            ->select('tbl_students.id, tbl_users.first_name, tbl_users.last_name, tbl_students.student_number')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->where('tbl_students.is_active', 1)
            ->orderBy('tbl_users.first_name', 'ASC')
            ->findAll();
        
        // Get all classes for filter
        $data['classes'] = $this->classModel
            ->select('tbl_classes.id, tbl_classes.class_name, tbl_classes.class_code')
            ->where('tbl_classes.is_active', 1)
            ->orderBy('tbl_classes.class_name', 'ASC')
            ->findAll();
        
        $data['selectedStudent'] = $studentId;
        $data['selectedClass'] = $classId;
        $data['startDate'] = $startDate;
        $data['endDate'] = $endDate;
        
        // Build attendance query
        $builder = $this->attendanceModel
            ->select('
                tbl_attendance.*,
                tbl_users.first_name,
                tbl_users.last_name,
                tbl_students.student_number,
                tbl_classes.class_name,
                tbl_disciplines.discipline_name,
                CONCAT(tbl_marked.first_name, " ", tbl_marked.last_name) as marked_by_name
            ')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_attendance.enrollment_id')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_attendance.class_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_attendance.discipline_id', 'left')
            ->join('tbl_users as tbl_marked', 'tbl_marked.id = tbl_attendance.marked_by', 'left')
            ->where('tbl_attendance.attendance_date >=', $startDate)
            ->where('tbl_attendance.attendance_date <=', $endDate);
        
        if ($studentId) {
            $builder->where('tbl_students.id', $studentId);
        }
        
        if ($classId) {
            $builder->where('tbl_attendance.class_id', $classId);
        }
        
        $data['attendances'] = $builder->orderBy('tbl_attendance.attendance_date', 'DESC')
            ->orderBy('tbl_users.first_name', 'ASC')
            ->findAll();
        
        // Calculate summary
        $summary = $this->attendanceModel
            ->select('
                COUNT(*) as total,
                SUM(CASE WHEN status = "Presente" THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN status = "Ausente" THEN 1 ELSE 0 END) as absent,
                SUM(CASE WHEN status = "Atrasado" THEN 1 ELSE 0 END) as late,
                SUM(CASE WHEN status = "Falta Justificada" THEN 1 ELSE 0 END) as justified,
                SUM(CASE WHEN status = "Dispensado" THEN 1 ELSE 0 END) as excused
            ')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_attendance.enrollment_id')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->where('tbl_attendance.attendance_date >=', $startDate)
            ->where('tbl_attendance.attendance_date <=', $endDate);
        
        if ($studentId) {
            $summary->where('tbl_students.id', $studentId);
        }
        
        if ($classId) {
            $summary->where('tbl_attendance.class_id', $classId);
        }
        
        $data['summary'] = $summary->first();
        
        // Chart data
        $chartData = $this->getChartData($startDate, $endDate, $studentId, $classId);
        $data['chartData'] = $chartData;
        
        return view('admin/students/attendance/report', $data);
    }
    
    /**
     * Get chart data for report
     */
    private function getChartData($startDate, $endDate, $studentId = null, $classId = null)
    {
        $builder = $this->attendanceModel
            ->select('
                DATE(attendance_date) as date,
                SUM(CASE WHEN status = "Presente" THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN status = "Ausente" THEN 1 ELSE 0 END) as absent,
                SUM(CASE WHEN status = "Atrasado" THEN 1 ELSE 0 END) as late
            ')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_attendance.enrollment_id')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->where('attendance_date >=', $startDate)
            ->where('attendance_date <=', $endDate)
            ->groupBy('DATE(attendance_date)')
            ->orderBy('attendance_date', 'ASC');
        
        if ($studentId) {
            $builder->where('tbl_students.id', $studentId);
        }
        
        if ($classId) {
            $builder->where('tbl_attendance.class_id', $classId);
        }
        
        $results = $builder->findAll();
        
        $labels = [];
        $present = [];
        $absent = [];
        $late = [];
        
        foreach ($results as $row) {
            $labels[] = date('d/m', strtotime($row->date));
            $present[] = (int)$row->present;
            $absent[] = (int)$row->absent;
            $late[] = (int)$row->late;
        }
        
        return [
            'labels' => $labels,
            'present' => $present,
            'absent' => $absent,
            'late' => $late
        ];
    }
}