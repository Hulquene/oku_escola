<?php

namespace App\Models;

class FinalGradeModel extends BaseModel
{
    protected $table = 'tbl_final_grades';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'enrollment_id',
        'discipline_id',
        'semester_id',
        'average_score',
        'final_score',
        'exam_score',
        'status',
        'observations',
        'calculated_by'
    ];
    
    protected $validationRules = [
        'enrollment_id' => 'required|numeric',
        'discipline_id' => 'required|numeric',
        'semester_id' => 'required|numeric',
        'average_score' => 'required|numeric'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'calculated_at';
    
    /**
     * Calculate final grades for a class
     */
    public function calculateClassGrades($classId, $semesterId)
    {
        $enrollmentModel = new EnrollmentModel();
        $examResultModel = new ExamResultModel();
        $continuousModel = new ContinuousAssessmentModel();
        
        $enrollments = $enrollmentModel->where('class_id', $classId)
            ->where('status', 'Ativo')
            ->findAll();
        
        $disciplineModel = new DisciplineModel();
        $disciplines = $disciplineModel->getByClass($classId);
        
        $this->transStart();
        
        foreach ($enrollments as $enrollment) {
            foreach ($disciplines as $discipline) {
                // Get continuous assessment average
                $continuousAvg = $continuousModel->getAverage(
                    $enrollment->student_id,
                    $discipline->id,
                    $semesterId
                );
                
                // Get exam results
                $exams = $examResultModel->select('
                        tbl_exam_results.score,
                        tbl_exam_boards.weight
                    ')
                    ->join('tbl_exams', 'tbl_exams.id = tbl_exam_results.exam_id')
                    ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exams.exam_board_id')
                    ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_exam_results.enrollment_id')
                    ->where('tbl_enrollments.id', $enrollment->id)
                    ->where('tbl_exams.discipline_id', $discipline->id)
                    ->where('tbl_exams.semester_id', $semesterId)
                    ->findAll();
                
                // Calculate weighted exam average
                $examTotal = 0;
                $weightTotal = 0;
                foreach ($exams as $exam) {
                    $examTotal += $exam->score * $exam->weight;
                    $weightTotal += $exam->weight;
                }
                
                $examAverage = $weightTotal > 0 ? $examTotal / $weightTotal : 0;
                
                // Calculate final average (60% continuous, 40% exams - configurable)
                $finalAverage = ($continuousAvg['average'] * 0.6) + ($examAverage * 0.4);
                
                // Determine status
                $status = $finalAverage >= 10 ? 'Aprovado' : 'Reprovado';
                
                // Check if needs exam recurso
                if ($finalAverage >= 8 && $finalAverage < 10) {
                    $status = 'Recurso';
                }
                
                // Save or update
                $existing = $this->where('enrollment_id', $enrollment->id)
                    ->where('discipline_id', $discipline->id)
                    ->where('semester_id', $semesterId)
                    ->first();
                
                $data = [
                    'enrollment_id' => $enrollment->id,
                    'discipline_id' => $discipline->id,
                    'semester_id' => $semesterId,
                    'average_score' => $continuousAvg['average'],
                    'exam_score' => $examAverage,
                    'final_score' => $finalAverage,
                    'status' => $status,
                    'calculated_by' => session()->get('user_id')
                ];
                
                if ($existing) {
                    $this->update($existing->id, $data);
                } else {
                    $this->insert($data);
                }
            }
        }
        
        $this->transComplete();
        
        return $this->transStatus();
    }
    
    /**
     * Get student report card
     */
    public function getReportCard($studentId, $semesterId)
    {
        return $this->select('
                tbl_final_grades.*,
                tbl_disciplines.discipline_name,
                tbl_disciplines.workload_hours
            ')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_final_grades.enrollment_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_final_grades.discipline_id')
            ->where('tbl_enrollments.student_id', $studentId)
            ->where('tbl_final_grades.semester_id', $semesterId)
            ->orderBy('tbl_disciplines.discipline_name', 'ASC')
            ->findAll();
    }
    
    /**
     * Get class performance summary
     */
    public function getClassPerformance($classId, $semesterId)
    {
        return $this->select('
                tbl_final_grades.discipline_id,
                tbl_disciplines.discipline_name,
                AVG(tbl_final_grades.final_score) as average,
                COUNT(*) as total_students,
                SUM(CASE WHEN tbl_final_grades.status = "Aprovado" THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN tbl_final_grades.status = "Reprovado" THEN 1 ELSE 0 END) as failed,
                SUM(CASE WHEN tbl_final_grades.status = "Recurso" THEN 1 ELSE 0 END) as recourse
            ')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_final_grades.enrollment_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_final_grades.discipline_id')
            ->where('tbl_enrollments.class_id', $classId)
            ->where('tbl_final_grades.semester_id', $semesterId)
            ->groupBy('tbl_final_grades.discipline_id')
            ->orderBy('tbl_disciplines.discipline_name', 'ASC')
            ->findAll();
    }
}