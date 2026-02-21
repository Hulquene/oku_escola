<?php

namespace App\Models;

class DisciplineAverageModel extends BaseModel
{
    protected $table = 'tbl_discipline_averages';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'enrollment_id',
        'discipline_id',
        'semester_id',
        'ac_score',
        'exam_score',
        'final_score',
        'status',
        'observations',
        'calculated_by'
    ];
    
    protected $validationRules = [
        'enrollment_id' => 'required|numeric',
        'discipline_id' => 'required|numeric',
        'semester_id' => 'required|numeric',
        'final_score' => 'required|numeric'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'calculated_at';
    protected $updatedField = false;
    
    /**
     * Calculate average for a student in a discipline
     */
    public function calculate($enrollmentId, $disciplineId, $semesterId, $calculatedBy = null)
    {
        $examResultModel = new ExamResultModel();
        $gradeWeightModel = new GradeWeightModel();
        $enrollmentModel = new EnrollmentModel();
        
        // Get student's grade level
        $enrollment = $enrollmentModel->select('tbl_enrollments.*, tbl_classes.grade_level_id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
            ->find($enrollmentId);
        
        if (!$enrollment) {
            return false;
        }
        
        // Get all exam results for this student/discipline/semester
        $results = $examResultModel->select('
                tbl_exam_results.*,
                tbl_exam_schedules.exam_board_id,
                tbl_exam_boards.board_type,
                tbl_exam_boards.board_code
            ')
            ->join('tbl_exam_schedules', 'tbl_exam_schedules.id = tbl_exam_results.exam_schedule_id')
            ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exam_schedules.exam_board_id')
            ->where('tbl_exam_results.enrollment_id', $enrollmentId)
            ->where('tbl_exam_schedules.discipline_id', $disciplineId)
            ->where('tbl_exam_schedules.semester_id', $semesterId)
            ->findAll();
        
        if (empty($results)) {
            return false;
        }
        
        // Get weight configuration
        $weights = $gradeWeightModel->getByGradeLevel($enrollment->grade_level_id);
        
        // Group results by exam board
        $grouped = [];
        foreach ($results as $result) {
            $boardId = $result->exam_board_id;
            if (!isset($grouped[$boardId])) {
                $grouped[$boardId] = [
                    'scores' => [],
                    'weight' => 0
                ];
            }
            $grouped[$boardId]['scores'][] = $result->score;
            
            // Find weight for this board
            foreach ($weights as $weight) {
                if ($weight->exam_board_id == $boardId) {
                    $grouped[$boardId]['weight'] = $weight->weight_percentage;
                    break;
                }
            }
        }
        
        // Calculate weighted average
        $totalWeighted = 0;
        $totalWeight = 0;
        $acScores = [];
        $examScores = [];
        
        foreach ($grouped as $boardId => $data) {
            // Calculate average for this board type
            $boardAvg = array_sum($data['scores']) / count($data['scores']);
            $totalWeighted += $boardAvg * $data['weight'];
            $totalWeight += $data['weight'];
            
            // Separate AC and Exam scores
            $boardType = $this->getBoardType($boardId);
            if (strpos($boardType, 'AC') !== false) {
                $acScores[] = $boardAvg;
            } else {
                $examScores[] = $boardAvg;
            }
        }
        
        $finalScore = $totalWeight > 0 ? $totalWeighted / $totalWeight : 0;
        $acAverage = !empty($acScores) ? array_sum($acScores) / count($acScores) : 0;
        $examAverage = !empty($examScores) ? array_sum($examScores) / count($examScores) : 0;
        
        // Determine status
        $status = $this->determineStatus($finalScore);
        
        // Save or update
        $existing = $this->where('enrollment_id', $enrollmentId)
            ->where('discipline_id', $disciplineId)
            ->where('semester_id', $semesterId)
            ->first();
        
        $data = [
            'enrollment_id' => $enrollmentId,
            'discipline_id' => $disciplineId,
            'semester_id' => $semesterId,
            'ac_score' => round($acAverage, 2),
            'exam_score' => round($examAverage, 2),
            'final_score' => round($finalScore, 2),
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
     * Get board type by ID
     */
    private function getBoardType($boardId)
    {
        $boardModel = new ExamBoardModel();
        $board = $boardModel->find($boardId);
        return $board ? $board->board_code : '';
    }
    
    /**
     * Determine status based on final score
     */
    private function determineStatus($score)
    {
        if ($score >= 10) {
            return 'Aprovado';
        } elseif ($score >= 7) {
            return 'Recurso';
        } else {
            return 'Reprovado';
        }
    }
    
    /**
     * Get student's results for a semester
     */
    public function getStudentSemesterResults($enrollmentId, $semesterId)
    {
        return $this->select('
                tbl_discipline_averages.*,
                tbl_disciplines.discipline_name,
                tbl_disciplines.discipline_code,
                tbl_disciplines.workload_hours
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_discipline_averages.discipline_id')
            ->where('tbl_discipline_averages.enrollment_id', $enrollmentId)
            ->where('tbl_discipline_averages.semester_id', $semesterId)
            ->orderBy('tbl_disciplines.discipline_name', 'ASC')
            ->findAll();
    }
    
    /**
     * Get class performance summary
     */
    public function getClassPerformance($classId, $semesterId)
    {
        return $this->select('
                tbl_discipline_averages.discipline_id,
                tbl_disciplines.discipline_name,
                COUNT(*) as total_students,
                AVG(tbl_discipline_averages.final_score) as average_score,
                SUM(CASE WHEN tbl_discipline_averages.status = "Aprovado" THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN tbl_discipline_averages.status = "Reprovado" THEN 1 ELSE 0 END) as failed,
                SUM(CASE WHEN tbl_discipline_averages.status = "Recurso" THEN 1 ELSE 0 END) as appeal
            ')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_discipline_averages.enrollment_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_discipline_averages.discipline_id')
            ->where('tbl_enrollments.class_id', $classId)
            ->where('tbl_discipline_averages.semester_id', $semesterId)
            ->groupBy('tbl_discipline_averages.discipline_id')
            ->orderBy('tbl_disciplines.discipline_name', 'ASC')
            ->findAll();
    }
}