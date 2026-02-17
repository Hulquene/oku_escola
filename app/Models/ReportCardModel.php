<?php

namespace App\Models;

class ReportCardModel extends BaseModel
{
    protected $table = 'tbl_report_cards';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'enrollment_id',
        'semester_id',
        'report_number',
        'generated_date',
        'average_score',
        'total_absences',
        'justified_absences',
        'status',
        'observations',
        'generated_by'
    ];
    
    protected $validationRules = [
        'enrollment_id' => 'required|numeric',
        'semester_id' => 'required|numeric',
        'report_number' => 'required|is_unique[tbl_report_cards.report_number,id,{id}]'
    ];
    
    protected $useTimestamps = true;
    
    /**
     * Generate report number
     */
    public function generateReportNumber()
    {
        $year = date('Y');
        $last = $this->select('report_number')
            ->like('report_number', "RPT{$year}", 'after')
            ->orderBy('id', 'DESC')
            ->first();
        
        if ($last) {
            $lastNumber = intval(substr($last->report_number, -4));
            $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);
        } else {
            $newNumber = '0001';
        }
        
        return "RPT{$year}{$newNumber}";
    }
    
    /**
     * Generate report card for student
     */
    public function generateForStudent($enrollmentId, $semesterId)
    {
        // Check if already generated
        $existing = $this->where('enrollment_id', $enrollmentId)
            ->where('semester_id', $semesterId)
            ->first();
        
        if ($existing) {
            return $existing->id;
        }
        
        $finalGradeModel = new FinalGradeModel();
        $attendanceModel = new AttendanceModel();
        
        // Get grades
        $grades = $finalGradeModel->where('enrollment_id', $enrollmentId)
            ->where('semester_id', $semesterId)
            ->findAll();
        
        // Calculate average
        $total = 0;
        foreach ($grades as $grade) {
            $total += $grade->final_score;
        }
        $average = count($grades) > 0 ? $total / count($grades) : 0;
        
        // Get attendance
        $attendance = $attendanceModel->select('
                COUNT(*) as total,
                SUM(CASE WHEN status = "Ausente" THEN 1 ELSE 0 END) as absent,
                SUM(CASE WHEN status = "Falta Justificada" THEN 1 ELSE 0 END) as justified
            ')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_attendance.enrollment_id')
            ->where('tbl_enrollments.id', $enrollmentId)
            ->where('tbl_attendance.semester_id', $semesterId)
            ->first();
        
        // Create report card
        $data = [
            'enrollment_id' => $enrollmentId,
            'semester_id' => $semesterId,
            'report_number' => $this->generateReportNumber(),
            'generated_date' => date('Y-m-d'),
            'average_score' => $average,
            'total_absences' => $attendance->absent ?? 0,
            'justified_absences' => $attendance->justified ?? 0,
            'status' => 'Emitido',
            'generated_by' => session()->get('user_id')
        ];
        
        return $this->insert($data);
    }
    
    /**
     * Get report card with details
     */
    public function getWithDetails($id)
    {
        return $this->select('
                tbl_report_cards.*,
                tbl_enrollments.student_id,
                tbl_students.student_number,
                tbl_users.first_name,
                tbl_users.last_name,
                tbl_users.birth_date,
                tbl_classes.class_name,
                tbl_classes.class_code,
                tbl_semesters.semester_name,
                tbl_semesters.semester_type,
                tbl_academic_years.year_name,
                tbl_users_generated.first_name as generated_by_name,
                tbl_users_generated.last_name as generated_by_lastname
            ')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_report_cards.enrollment_id')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
            ->join('tbl_semesters', 'tbl_semesters.id = tbl_report_cards.semester_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_semesters.academic_year_id')
            ->join('tbl_users as tbl_users_generated', 'tbl_users_generated.id = tbl_report_cards.generated_by', 'left')
            ->where('tbl_report_cards.id', $id)
            ->first();
    }
}