<?php

namespace App\Models;

class ExamAttendanceModel extends BaseModel
{
    protected $table = 'tbl_exam_attendance';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'exam_schedule_id',
        'enrollment_id',
        'attended',
        'check_in_time',
        'check_in_method',
        'observations',
        'recorded_by'
    ];
    
    protected $validationRules = [
        'exam_schedule_id' => 'required|numeric',
        'enrollment_id' => 'required|numeric'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    
    /**
     * Mark attendance for all students in an exam
     */
    public function markBatch($examScheduleId, $attendanceData, $recordedBy)
    {
        $enrollmentModel = new EnrollmentModel();
        
        // Get exam details
        $scheduleModel = new ExamScheduleModel();
        $exam = $scheduleModel->find($examScheduleId);
        
        if (!$exam) {
            return false;
        }
        
        // Get all active students in class
        $students = $enrollmentModel
            ->where('class_id', $exam->class_id)
            ->where('status', 'Ativo')
            ->findAll();
        
        $attendance = [];
        foreach ($students as $student) {
            $attended = $attendanceData[$student->id]['attended'] ?? 1;
            $checkInTime = $attended ? date('Y-m-d H:i:s') : null;
            
            $attendance[] = [
                'exam_schedule_id' => $examScheduleId,
                'enrollment_id' => $student->id,
                'attended' => $attended,
                'check_in_time' => $checkInTime,
                'check_in_method' => 'Manual',
                'observations' => $attendanceData[$student->id]['observations'] ?? null,
                'recorded_by' => $recordedBy
            ];
        }
        
        return $this->insertBatch($attendance);
    }
    
    /**
     * Get attendance for an exam
     */
    public function getByExam($examScheduleId)
    {
        return $this->select('
                tbl_exam_attendance.*,
                tbl_enrollments.student_id,
                tbl_students.student_number,
                tbl_users.first_name,
                tbl_users.last_name
            ')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_exam_attendance.enrollment_id')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->where('tbl_exam_attendance.exam_schedule_id', $examScheduleId)
            ->findAll();
    }
    
    /**
     * Get attendance statistics for an exam
     */
    public function getStats($examScheduleId)
    {
        $total = $this->where('exam_schedule_id', $examScheduleId)->countAllResults();
        $present = $this->where('exam_schedule_id', $examScheduleId)
            ->where('attended', 1)
            ->countAllResults();
        $absent = $total - $present;
        
        return [
            'total' => $total,
            'present' => $present,
            'absent' => $absent,
            'percentage' => $total > 0 ? round(($present / $total) * 100, 2) : 0
        ];
    }
}