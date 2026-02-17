<?php

namespace App\Models;

class AttendanceModel extends BaseModel
{
    protected $table = 'tbl_attendance';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'enrollment_id',
        'class_id',
        'discipline_id',
        'attendance_date',
        'status',
        'justification',
        'marked_by'
    ];
    
    protected $validationRules = [
        'enrollment_id' => 'required|numeric',
        'class_id' => 'required|numeric',
        'attendance_date' => 'required|valid_date',
        'status' => 'required'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = null;
    
    /**
     * Mark attendance for multiple students
     */
    public function markBulk(array $attendances)
    {
        $this->transStart();
        
        foreach ($attendances as $attendance) {
            // Check if already marked for this date
            $existing = $this->where('enrollment_id', $attendance['enrollment_id'])
                ->where('attendance_date', $attendance['attendance_date'])
                ->where('discipline_id', $attendance['discipline_id'] ?? null)
                ->first();
            
            if ($existing) {
                $this->update($existing->id, $attendance);
            } else {
                $this->insert($attendance);
            }
        }
        
        $this->transComplete();
        
        return $this->transStatus();
    }
    
    /**
     * Get attendance by class and date
     */
    public function getByClassAndDate($classId, $date, $disciplineId = null)
    {
        $builder = $this->select('
                tbl_attendance.*,
                tbl_enrollments.student_id,
                tbl_users.first_name,
                tbl_users.last_name,
                tbl_students.student_number
            ')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_attendance.enrollment_id')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->where('tbl_attendance.class_id', $classId)
            ->where('tbl_attendance.attendance_date', $date);
        
        if ($disciplineId) {
            $builder->where('tbl_attendance.discipline_id', $disciplineId);
        }
        
        return $builder->findAll();
    }
    
    /**
     * Get attendance report for student
     */
    public function getStudentReport($studentId, $startDate = null, $endDate = null)
    {
        $builder = $this->select('
                tbl_attendance.*,
                tbl_classes.class_name,
                tbl_disciplines.discipline_name
            ')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_attendance.enrollment_id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_attendance.class_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_attendance.discipline_id', 'left')
            ->where('tbl_enrollments.student_id', $studentId);
        
        if ($startDate) {
            $builder->where('tbl_attendance.attendance_date >=', $startDate);
        }
        
        if ($endDate) {
            $builder->where('tbl_attendance.attendance_date <=', $endDate);
        }
        
        return $builder->orderBy('tbl_attendance.attendance_date', 'DESC')
            ->findAll();
    }
    
    /**
     * Get attendance statistics
     */
    public function getStatistics($classId = null, $startDate = null, $endDate = null)
    {
        $builder = $this->select('
                COUNT(*) as total,
                SUM(CASE WHEN status = "Presente" THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN status = "Ausente" THEN 1 ELSE 0 END) as absent,
                SUM(CASE WHEN status = "Atrasado" THEN 1 ELSE 0 END) as late,
                SUM(CASE WHEN status = "Falta Justificada" THEN 1 ELSE 0 END) as justified,
                SUM(CASE WHEN status = "Dispensado" THEN 1 ELSE 0 END) as excused
            ');
        
        if ($classId) {
            $builder->where('class_id', $classId);
        }
        
        if ($startDate) {
            $builder->where('attendance_date >=', $startDate);
        }
        
        if ($endDate) {
            $builder->where('attendance_date <=', $endDate);
        }
        
        return $builder->first();
    }
    
    /**
     * Get attendance rate by class
     */
    public function getAttendanceRate($classId, $startDate, $endDate)
    {
        $stats = $this->getStatistics($classId, $startDate, $endDate);
        
        if ($stats->total > 0) {
            $present = $stats->present + $stats->late + $stats->justified;
            return round(($present / $stats->total) * 100, 2);
        }
        
        return 0;
    }
}