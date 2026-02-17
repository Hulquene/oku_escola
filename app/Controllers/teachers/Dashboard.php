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
        
        // Get my classes
        $data['myClasses'] = $this->classDisciplineModel
            ->select('tbl_class_disciplines.*, tbl_classes.class_name, tbl_disciplines.discipline_name, tbl_classes.class_shift')
            ->join('tbl_classes', 'tbl_classes.id = tbl_class_disciplines.class_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
            ->where('tbl_class_disciplines.teacher_id', $teacherId)
            ->where('tbl_classes.is_active', 1)
            ->orderBy('tbl_classes.class_name', 'ASC')
            ->findAll();
        
        // Get upcoming exams
        $data['upcomingExams'] = $this->examModel->getByTeacher($teacherId);
        
        // Today's attendance
        $data['todayAttendance'] = $this->attendanceModel
            ->where('attendance_date', date('Y-m-d'))
            ->countAllResults();
        
        return view('teachers/dashboard/index', $data);
    }
}