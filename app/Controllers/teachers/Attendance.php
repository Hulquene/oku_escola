<?php

namespace App\Controllers\teachers;

use App\Controllers\BaseController;
use App\Models\AttendanceModel;
use App\Models\ClassDisciplineModel;
use App\Models\EnrollmentModel;
use App\Models\DisciplineModel;

class Attendance extends BaseController
{
    protected $attendanceModel;
    protected $classDisciplineModel;
    protected $enrollmentModel;
    protected $disciplineModel;
    
    public function __construct()
    {
        $this->attendanceModel = new AttendanceModel();
        $this->classDisciplineModel = new ClassDisciplineModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->disciplineModel = new DisciplineModel();
    }
    
    /**
     * Attendance page
     */
    public function index()
    {
        $data['title'] = 'Registro de Presenças';
        
        $teacherId = $this->session->get('user_id');
        
        // Get classes for filter
        $data['classes'] = $this->classDisciplineModel
            ->select('tbl_classes.id, tbl_classes.class_name, tbl_classes.class_code')
            ->join('tbl_classes', 'tbl_classes.id = tbl_class_disciplines.class_id')
            ->where('tbl_class_disciplines.teacher_id', $teacherId)
            ->distinct()
            ->findAll();
        
        $classId = $this->request->getGet('class_id');
        $disciplineId = $this->request->getGet('discipline_id');
        $date = $this->request->getGet('date') ?? date('Y-m-d');
        
        // PASSAR OS VALORES PARA A VIEW
        $data['selectedClass'] = $classId;
        $data['selectedDiscipline'] = $disciplineId;
        $data['selectedDate'] = $date;
        
        if ($classId) {
            // Get students in this class
            $data['students'] = $this->enrollmentModel
                ->select('tbl_enrollments.id as enrollment_id, tbl_students.id, tbl_users.first_name, tbl_users.last_name, tbl_students.student_number')
                ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
                ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
                ->where('tbl_enrollments.class_id', $classId)
                ->where('tbl_enrollments.status', 'Ativo')
                ->orderBy('tbl_users.first_name', 'ASC')
                ->findAll();
            
            // Get existing attendances
            $attendances = $this->attendanceModel
                ->where('class_id', $classId)
                ->where('discipline_id', $disciplineId ?: null)
                ->where('attendance_date', $date)
                ->findAll();
            
            $data['attendances'] = [];
            foreach ($attendances as $att) {
                $data['attendances'][$att->enrollment_id] = $att;
            }
        } else {
            $data['students'] = [];
            $data['attendances'] = [];
        }
        
        return view('teachers/attendance/index', $data);
    }
    
    /**
     * Save attendance
     */
    public function save()
    {
        $classId = $this->request->getPost('class_id');
        $disciplineId = $this->request->getPost('discipline_id') ?: null;
        $attendanceDate = $this->request->getPost('attendance_date');
        $attendances = $this->request->getPost('attendance') ?? [];
        
        if (!$classId || !$attendanceDate || empty($attendances)) {
            return redirect()->back()->with('error', 'Dados incompletos');
        }
        
        $data = [];
        foreach ($attendances as $enrollmentId => $att) {
            if (isset($att['status'])) {
                $data[] = [
                    'enrollment_id' => $enrollmentId,
                    'class_id' => $classId,
                    'discipline_id' => $disciplineId,
                    'attendance_date' => $attendanceDate,
                    'status' => $att['status'],
                    'justification' => $att['justification'] ?? null,
                    'marked_by' => $this->session->get('user_id')
                ];
            }
        }
        
        $result = $this->attendanceModel->markBulk($data);
        
        if ($result) {
            return redirect()->to('/teachers/attendance?class_id=' . $classId . '&discipline_id=' . ($disciplineId ?: '') . '&date=' . $attendanceDate)
                ->with('success', 'Presenças registradas com sucesso');
        } else {
            return redirect()->back()->with('error', 'Erro ao registrar presenças');
        }
    }
    
    /**
     * Attendance report
     */
    public function report()
    {
        $data['title'] = 'Relatório de Presenças';
        
        $teacherId = $this->session->get('user_id');
        
        $classId = $this->request->getGet('class');
        $disciplineId = $this->request->getGet('discipline');
        $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?: date('Y-m-t');
        
        // Get classes for filter
        $data['classes'] = $this->classDisciplineModel
            ->select('tbl_classes.id, tbl_classes.class_name')
            ->join('tbl_classes', 'tbl_classes.id = tbl_class_disciplines.class_id')
            ->where('tbl_class_disciplines.teacher_id', $teacherId)
            ->distinct()
            ->findAll();
        
        // Get disciplines for filter
        $data['disciplines'] = $this->classDisciplineModel
            ->select('tbl_disciplines.id, tbl_disciplines.discipline_name')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
            ->where('tbl_class_disciplines.teacher_id', $teacherId)
            ->distinct()
            ->findAll();
        
        if ($classId) {
            // Get attendance summary by student
            $builder = $this->attendanceModel
                ->select('
                    tbl_users.first_name,
                    tbl_users.last_name,
                    tbl_students.student_number,
                    COUNT(*) as total,
                    SUM(CASE WHEN status = "Presente" THEN 1 ELSE 0 END) as present,
                    SUM(CASE WHEN status = "Ausente" THEN 1 ELSE 0 END) as absent,
                    SUM(CASE WHEN status = "Atrasado" THEN 1 ELSE 0 END) as late,
                    SUM(CASE WHEN status = "Falta Justificada" THEN 1 ELSE 0 END) as justified
                ')
                ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_attendance.enrollment_id')
                ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
                ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
                ->where('tbl_attendance.class_id', $classId)
                ->where('tbl_attendance.attendance_date >=', $startDate)
                ->where('tbl_attendance.attendance_date <=', $endDate);
            
            if ($disciplineId) {
                $builder->where('tbl_attendance.discipline_id', $disciplineId);
            }
            
            $data['report'] = $builder->groupBy('tbl_attendance.enrollment_id')
                ->orderBy('tbl_users.first_name', 'ASC')
                ->findAll();
        }
        
        $data['selectedClass'] = $classId;
        $data['selectedDiscipline'] = $disciplineId;
        $data['startDate'] = $startDate;
        $data['endDate'] = $endDate;
        
        return view('teachers/attendance/report', $data);
    }
}