<?php

namespace App\Models;

class ExamResultModel extends BaseModel
{
    protected $table = 'tbl_exam_results';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'exam_id',
        'enrollment_id',
        'score',
        'score_percentage',
        'grade',
        'observations',
        'recorded_by'
    ];
    
    protected $validationRules = [
        'exam_id' => 'required|numeric',
        'enrollment_id' => 'required|numeric',
        'score' => 'required|numeric'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'recorded_at';
    protected $updatedField = 'updated_at';
    
    /**
     * Save multiple results
     */
    public function saveBulk(array $results)
    {
        $this->transStart();
        
        foreach ($results as $result) {
            // Check if already exists
            $existing = $this->where('exam_id', $result['exam_id'])
                ->where('enrollment_id', $result['enrollment_id'])
                ->first();
            
            // Calculate percentage if needed
            if (isset($result['max_score']) && $result['max_score'] > 0) {
                $result['score_percentage'] = ($result['score'] / $result['max_score']) * 100;
            }
            
            // Calculate grade (simplified - can be customized)
            if (isset($result['score_percentage'])) {
                $result['grade'] = $this->calculateGrade($result['score_percentage']);
            }
            
            if ($existing) {
                $this->update($existing->id, $result);
            } else {
                $this->insert($result);
            }
        }
        
        $this->transComplete();
        
        return $this->transStatus();
    }
    
    /**
     * Calculate grade based on percentage (Angolan system 0-20)
     */
    private function calculateGrade($percentage)
    {
        if ($percentage >= 90) return '20';
        if ($percentage >= 85) return '19';
        if ($percentage >= 80) return '18';
        if ($percentage >= 75) return '17';
        if ($percentage >= 70) return '16';
        if ($percentage >= 65) return '15';
        if ($percentage >= 60) return '14';
        if ($percentage >= 55) return '13';
        if ($percentage >= 50) return '12';
        if ($percentage >= 45) return '11';
        if ($percentage >= 40) return '10';
        if ($percentage >= 35) return '09';
        if ($percentage >= 30) return '08';
        if ($percentage >= 25) return '07';
        if ($percentage >= 20) return '06';
        if ($percentage >= 15) return '05';
        if ($percentage >= 10) return '04';
        if ($percentage >= 5) return '03';
        return '00';
    }
    
    /**
     * Get results by exam
     */
    public function getByExam($examId)
    {
        return $this->select('
                tbl_exam_results.*,
                tbl_enrollments.student_id,
                tbl_users.first_name,
                tbl_users.last_name,
                tbl_students.student_number
            ')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_exam_results.enrollment_id')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->where('tbl_exam_results.exam_id', $examId)
            ->orderBy('tbl_users.first_name', 'ASC')
            ->findAll();
    }
    
    /**
     * Get student results
     */
    public function getStudentResults($studentId, $semesterId = null)
    {
        $builder = $this->select('
                tbl_exam_results.*,
                tbl_exams.exam_name,
                tbl_exams.exam_date,
                tbl_disciplines.discipline_name,
                tbl_exam_boards.board_name,
                tbl_exam_boards.board_type
            ')
            ->join('tbl_exams', 'tbl_exams.id = tbl_exam_results.exam_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exams.discipline_id')
            ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exams.exam_board_id')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_exam_results.enrollment_id')
            ->where('tbl_enrollments.student_id', $studentId);
        
        if ($semesterId) {
            $builder->where('tbl_exams.semester_id', $semesterId);
        }
        
        return $builder->orderBy('tbl_exams.exam_date', 'DESC')
            ->findAll();
    }
    
    /**
     * Get exam statistics
     */
    public function getExamStatistics($examId)
    {
        return $this->select('
                COUNT(*) as total,
                AVG(score) as average,
                MIN(score) as minimum,
                MAX(score) as maximum,
                SUM(CASE WHEN score >= 10 THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN score < 10 THEN 1 ELSE 0 END) as failed
            ')
            ->where('exam_id', $examId)
            ->first();
    }
}