<?php

namespace App\Models;

class SemesterResultModel extends BaseModel
{
    protected $table = 'tbl_semester_results';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'enrollment_id',
        'semester_id',
        'overall_average',
        'total_disciplines',
        'approved_disciplines',
        'failed_disciplines',
        'appeal_disciplines',
        'status',
        'observations',
        'calculated_by'
    ];
    
    protected $validationRules = [
        'enrollment_id' => 'required|numeric',
        'semester_id' => 'required|numeric'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'calculated_at';
    protected $updatedField = false;
    
    /**
     * Calculate semester result for a student
     */
    public function calculate($enrollmentId, $semesterId, $calculatedBy = null)
    {
        $disciplineAvgModel = new DisciplineAverageModel();
        
        // Get all discipline averages for this student in this semester
        $averages = $disciplineAvgModel
            ->where('enrollment_id', $enrollmentId)
            ->where('semester_id', $semesterId)
            ->findAll();
        
        if (empty($averages)) {
            return false;
        }
        
        $total = count($averages);
        $approved = 0;
        $failed = 0;
        $appeal = 0;
        $sumScores = 0;
        
        foreach ($averages as $avg) {
            $sumScores += $avg->final_score;
            
            switch ($avg->status) {
                case 'Aprovado':
                    $approved++;
                    break;
                case 'Reprovado':
                    $failed++;
                    break;
                case 'Recurso':
                    $appeal++;
                    break;
            }
        }
        
        $overallAverage = $sumScores / $total;
        
        // Determine final status
        $status = 'Em Andamento';
        if ($appeal > 0) {
            $status = 'Recurso';
        } elseif ($failed > 0) {
            $status = 'Reprovado';
        } elseif ($approved == $total) {
            $status = 'Aprovado';
        }
        
        // Save or update
        $existing = $this->where('enrollment_id', $enrollmentId)
            ->where('semester_id', $semesterId)
            ->first();
        
        $data = [
            'enrollment_id' => $enrollmentId,
            'semester_id' => $semesterId,
            'overall_average' => round($overallAverage, 2),
            'total_disciplines' => $total,
            'approved_disciplines' => $approved,
            'failed_disciplines' => $failed,
            'appeal_disciplines' => $appeal,
            'status' => $status,
            'calculated_by' => $calculatedBy
        ];
        
        if ($existing) {
            $this->update($existing->id, $data);
            return $existing->id;
        } else {
            return $this->insert($data);
        }
    }
    
    /**
     * Calculate results for entire class
     */
    public function calculateClassResults($classId, $semesterId, $calculatedBy = null)
    {
        $enrollmentModel = new EnrollmentModel();
        
        $enrollments = $enrollmentModel
            ->where('class_id', $classId)
            ->where('status', 'Ativo')
            ->findAll();
        
        $results = [];
        foreach ($enrollments as $enrollment) {
            $resultId = $this->calculate($enrollment->id, $semesterId, $calculatedBy);
            if ($resultId) {
                $results[] = $resultId;
            }
        }
        
        return $results;
    }
    
    /**
     * Get class semester summary
     */
    public function getClassSummary($classId, $semesterId)
    {
        return $this->select('
                COUNT(*) as total_students,
                AVG(overall_average) as class_average,
                SUM(CASE WHEN status = "Aprovado" THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN status = "Reprovado" THEN 1 ELSE 0 END) as failed,
                SUM(CASE WHEN status = "Recurso" THEN 1 ELSE 0 END) as appeal,
                SUM(CASE WHEN status = "Em Andamento" THEN 1 ELSE 0 END) as pending
            ')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_semester_results.enrollment_id')
            ->where('tbl_enrollments.class_id', $classId)
            ->where('tbl_semester_results.semester_id', $semesterId)
            ->first();
    }
    
    /**
     * Get student's academic history
     */
    public function getStudentHistory($studentId)
    {
        return $this->select('
                tbl_semester_results.*,
                tbl_semesters.semester_name,
                tbl_semesters.semester_type,
                tbl_academic_years.year_name,
                tbl_classes.class_name
            ')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_semester_results.enrollment_id')
            ->join('tbl_semesters', 'tbl_semesters.id = tbl_semester_results.semester_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_semesters.academic_year_id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
            ->where('tbl_enrollments.student_id', $studentId)
            ->orderBy('tbl_academic_years.year_name', 'DESC')
            ->orderBy('tbl_semesters.id', 'ASC')
            ->findAll();
    }
}