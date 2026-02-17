<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use App\Models\UserModel;
use App\Models\EnrollmentModel;
use App\Models\AttendanceModel;
use App\Models\StudentFeeModel;

class Dashboard extends BaseController
{
    protected $studentModel;
    protected $userModel;
    protected $enrollmentModel;
    protected $attendanceModel;
    protected $feeModel;
    
    public function __construct()
    {
        $this->studentModel = new StudentModel();
        $this->userModel = new UserModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->attendanceModel = new AttendanceModel();
        $this->feeModel = new StudentFeeModel();
    }
    
    /**
     * Admin dashboard
     */
    public function index()
    {
        $data['title'] = 'Dashboard';
        
        // Statistics
        $data['totalStudents'] = $this->studentModel->where('is_active', 1)->countAllResults();
        $data['totalTeachers'] = $this->userModel->where('user_type', 'teacher')->where('is_active', 1)->countAllResults();
        $data['totalStaff'] = $this->userModel->where('user_type', 'staff')->where('is_active', 1)->countAllResults();
        
        // Enrollments
        $currentYear = model('App\Models\AcademicYearModel')->getCurrent();
        if ($currentYear) {
            $data['activeEnrollments'] = $this->enrollmentModel
                ->where('academic_year_id', $currentYear->id)
                ->where('status', 'Ativo')
                ->countAllResults();
        } else {
            $data['activeEnrollments'] = 0;
        }
        
        // Recent enrollments
        $data['recentEnrollments'] = $this->enrollmentModel
            ->select('tbl_enrollments.*, tbl_users.first_name, tbl_users.last_name, tbl_classes.class_name')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
            ->orderBy('tbl_enrollments.created_at', 'DESC')
            ->limit(10)
            ->findAll();
        
        // Fee summary
        $feeStats = $this->feeModel->getStatistics($currentYear->id ?? null);
        $data['feeStats'] = $feeStats ?? (object)[
            'total_amount' => 0,
            'paid_amount' => 0,
            'pending_amount' => 0
        ];
        
        // Attendance today
        $data['attendanceToday'] = $this->attendanceModel
            ->where('attendance_date', date('Y-m-d'))
            ->countAllResults();
        
        // Charts data
        $data['enrollmentChart'] = $this->getEnrollmentChartData();
        $data['feeChart'] = $this->getFeeChartData();
        
        return view('admin/dashboard/index', $data);
    }
    
    /**
     * Get enrollment chart data
     */
    protected function getEnrollmentChartData()
    {
        $months = [];
        $counts = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $months[] = date('M/Y', strtotime("-$i months"));
            
            $count = $this->enrollmentModel
                ->where('enrollment_date >=', date('Y-m-01', strtotime($month)))
                ->where('enrollment_date <=', date('Y-m-t', strtotime($month)))
                ->countAllResults();
            
            $counts[] = $count;
        }
        
        return [
            'months' => $months,
            'counts' => $counts
        ];
    }
    
    /**
     * Get fee chart data
     */
    protected function getFeeChartData()
    {
        $months = [];
        $collected = [];
        $pending = [];
        
        for ($i = 5; $i >= 0; $i--) {
            $month = date('Y-m', strtotime("-$i months"));
            $months[] = date('M/Y', strtotime("-$i months"));
            
            // This would need proper implementation based on your fee payment structure
            $collected[] = rand(100000, 500000);
            $pending[] = rand(50000, 200000);
        }
        
        return [
            'months' => $months,
            'collected' => $collected,
            'pending' => $pending
        ];
    }
}