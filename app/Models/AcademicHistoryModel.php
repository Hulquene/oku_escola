<?php

namespace App\Models;

class AcademicHistoryModel extends BaseModel
{
    protected $table = 'tbl_academic_history';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'student_id',
        'academic_year_id',
        'class_id',
        'final_status',
        'final_average',
        'total_days',
        'total_absences',
        'observations'
    ];
    
    protected $validationRules = [
        'student_id' => 'required|numeric',
        'academic_year_id' => 'required|numeric',
        'class_id' => 'required|numeric',
        'final_status' => 'required'
    ];
    
    protected $useTimestamps = true;
    protected $updatedField = null;
    
    /**
     * Add to history from enrollment
     */
    public function addFromEnrollment($enrollmentId)
    {
        $enrollmentModel = new EnrollmentModel();
        $enrollment = $enrollmentModel->find($enrollmentId);
        
        if (!$enrollment) {
            return false;
        }
        
        // Check if already exists
        $exists = $this->where('student_id', $enrollment->student_id)
            ->where('academic_year_id', $enrollment->academic_year_id)
            ->first();
        
        if ($exists) {
            return false;
        }
        
        $finalGradeModel = new FinalGradeModel();
        
        // Get final grades
        $grades = $finalGradeModel->where('enrollment_id', $enrollmentId)
            ->findAll();
        
        // Calculate average
        $total = 0;
        foreach ($grades as $grade) {
            $total += $grade->final_score;
        }
        $average = count($grades) > 0 ? $total / count($grades) : 0;
        
        // Determine final status
        $failed = 0;
        foreach ($grades as $grade) {
            if ($grade->status == 'Reprovado') {
                $failed++;
            }
        }
        
        $status = $failed > 0 ? 'Reprovado' : 'Aprovado';
        
        // Get attendance
        $attendanceModel = new AttendanceModel();
        $attendance = $attendanceModel->where('enrollment_id', $enrollmentId)
            ->countAllResults();
        
        $absences = $attendanceModel->where('enrollment_id', $enrollmentId)
            ->where('status', 'Ausente')
            ->countAllResults();
        
        return $this->insert([
            'student_id' => $enrollment->student_id,
            'academic_year_id' => $enrollment->academic_year_id,
            'class_id' => $enrollment->class_id,
            'final_status' => $status,
            'final_average' => $average,
            'total_days' => $attendance,
            'total_absences' => $absences,
            'observations' => "Transição automática do ano letivo"
        ]);
    }
    
    /**
     * Get student history
     */
    public function getStudentHistory($studentId)
    {
        return $this->select('
                tbl_academic_history.*,
                tbl_classes.class_name,
                tbl_academic_years.year_name,
                tbl_academic_years.start_date,
                tbl_academic_years.end_date
            ')
            ->join('tbl_classes', 'tbl_classes.id = tbl_academic_history.class_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_academic_history.academic_year_id')
            ->where('tbl_academic_history.student_id', $studentId)
            ->orderBy('tbl_academic_years.start_date', 'DESC')
            ->findAll();
    }
    
    /**
     * Get promotion statistics
     */
    public function getPromotionStats($academicYearId)
    {
        return $this->select('
                tbl_classes.id as class_id,
                tbl_classes.class_name,
                COUNT(*) as total_students,
                SUM(CASE WHEN final_status = "Aprovado" THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN final_status = "Reprovado" THEN 1 ELSE 0 END) as failed,
                SUM(CASE WHEN final_status = "Transferido" THEN 1 ELSE 0 END) as transferred,
                AVG(final_average) as average_score
            ')
            ->join('tbl_classes', 'tbl_classes.id = tbl_academic_history.class_id')
            ->where('tbl_academic_history.academic_year_id', $academicYearId)
            ->groupBy('tbl_academic_history.class_id')
            ->orderBy('tbl_classes.class_name', 'ASC')
            ->findAll();
    }
}