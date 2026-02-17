<?php

namespace App\Controllers\teachers;

use App\Controllers\BaseController;
use App\Models\ClassDisciplineModel;
use App\Models\ExamModel;
use App\Models\AttendanceModel;

class Dashboard extends BaseController
{
    protected $classDisciplineModel;
    protected $examModel;
    protected $attendanceModel;
    
    public function __construct()
    {
        $this->classDisciplineModel = new ClassDisciplineModel();
        $this->examModel = new ExamModel();
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
                tbl_disciplines.discipline_code
            ')
            ->join('tbl_classes', 'tbl_classes.id = tbl_class_disciplines.class_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
            ->where('tbl_class_disciplines.teacher_id', $teacherId)
            ->where('tbl_classes.is_active', 1)
            ->orderBy('tbl_classes.class_name', 'ASC')
            ->orderBy('tbl_disciplines.discipline_name', 'ASC')
            ->findAll();
        
        // Get upcoming exams
        $data['upcomingExams'] = $this->examModel
            ->select('
                tbl_exams.*,
                tbl_classes.class_name,
                tbl_disciplines.discipline_name
            ')
            ->join('tbl_classes', 'tbl_classes.id = tbl_exams.class_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exams.discipline_id')
            ->where('tbl_exams.created_by', $teacherId)
            ->where('tbl_exams.exam_date >=', date('Y-m-d'))
            ->orderBy('tbl_exams.exam_date', 'ASC')
            ->limit(5)
            ->findAll();
        
        // Today's attendance count
        $data['todayAttendance'] = $this->attendanceModel
            ->where('attendance_date', date('Y-m-d'))
            ->countAllResults();
        
        return view('teachers/dashboard/index', $data);
    }
}