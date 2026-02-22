<?php

namespace App\Controllers\teachers;

use App\Controllers\BaseController;
use App\Models\ClassDisciplineModel;
use App\Models\ExamScheduleModel;
use App\Models\ExamPeriodModel;
use App\Models\AttendanceModel;

class Dashboard extends BaseController
{
    protected $classDisciplineModel;
    protected $examScheduleModel;
    protected $examPeriodModel;
    protected $attendanceModel;
    
    public function __construct()
    {
        $this->classDisciplineModel = new ClassDisciplineModel();
        $this->examScheduleModel = new ExamScheduleModel();
        $this->examPeriodModel = new ExamPeriodModel();
        $this->attendanceModel = new AttendanceModel();
    }
    
    /**
     * Teacher dashboard
     */
    public function index()
    {
        $data['title'] = 'Dashboard do Professor';
        $teacherId = $this->session->get('user_id');
        
        // Get my classes with all needed fields
        $data['myClasses'] = $this->classDisciplineModel
            ->select('
                tbl_class_disciplines.*,
                tbl_classes.id as class_id,
                tbl_classes.class_name,
                tbl_classes.class_code,
                tbl_classes.class_room,
                tbl_classes.class_shift,
                tbl_classes.capacity,
                tbl_disciplines.id as discipline_id,
                tbl_disciplines.discipline_name,
                tbl_disciplines.discipline_code,
                tbl_grade_levels.level_name
            ')
            ->join('tbl_classes', 'tbl_classes.id = tbl_class_disciplines.class_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id', 'left')
            ->where('tbl_class_disciplines.teacher_id', $teacherId)
            ->where('tbl_classes.is_active', 1) // VOLTAR PARA is_active
            ->orderBy('tbl_classes.class_name', 'ASC')
            ->orderBy('tbl_disciplines.discipline_name', 'ASC')
            ->findAll();
        
        // Calcular total de alunos
        $totalStudents = 0;
        $enrollmentModel = new \App\Models\EnrollmentModel();
        $processedClasses = [];
        
        foreach ($data['myClasses'] as $class) {
            if (!in_array($class->class_id, $processedClasses)) {
                $processedClasses[] = $class->class_id;
                $totalStudents += $enrollmentModel
                    ->where('class_id', $class->class_id)
                    ->where('status', 'Ativo')
                    ->countAllResults();
            }
        }
        $data['totalStudents'] = $totalStudents;
        
        // Get upcoming exams (via exam_schedules)
        $data['upcomingExams'] = $this->examScheduleModel
            ->select('
                tbl_exam_schedules.*,
                tbl_classes.class_name,
                tbl_disciplines.discipline_name,
                tbl_exam_boards.board_name,
                tbl_exam_boards.board_type
            ')
            ->join('tbl_classes', 'tbl_classes.id = tbl_exam_schedules.class_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id')
            ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exam_schedules.exam_board_id')
            ->join('tbl_class_disciplines', 'tbl_class_disciplines.class_id = tbl_exam_schedules.class_id AND tbl_class_disciplines.discipline_id = tbl_exam_schedules.discipline_id')
            ->where('tbl_class_disciplines.teacher_id', $teacherId)
            ->where('tbl_exam_schedules.exam_date >=', date('Y-m-d'))
            ->where('tbl_exam_schedules.status', 'Agendado')
            ->orderBy('tbl_exam_schedules.exam_date', 'ASC')
            ->limit(5)
            ->findAll();
        
        // Today's attendance count
        $data['todayAttendance'] = $this->attendanceModel
            ->where('attendance_date', date('Y-m-d'))
            ->countAllResults();
        
        return view('teachers/dashboard/index', $data);
    }
}