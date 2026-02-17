<?php

namespace App\Models;

class ContinuousAssessmentModel extends BaseModel
{
    protected $table = 'tbl_continuous_assessment';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'enrollment_id',
        'discipline_id',
        'semester_id',
        'assessment_type',
        'score',
        'assessment_date',
        'recorded_by'
    ];
    
    protected $validationRules = [
        'enrollment_id' => 'required|numeric',
        'discipline_id' => 'required|numeric',
        'semester_id' => 'required|numeric',
        'score' => 'required|numeric'
    ];
    
    protected $useTimestamps = true;
    
    /**
     * Get student assessments
     */
    public function getStudentAssessments($studentId, $semesterId = null, $disciplineId = null)
    {
        $builder = $this->select('
                tbl_continuous_assessment.*,
                tbl_disciplines.discipline_name
            ')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_continuous_assessment.enrollment_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_continuous_assessment.discipline_id')
            ->where('tbl_enrollments.student_id', $studentId);
        
        if ($semesterId) {
            $builder->where('tbl_continuous_assessment.semester_id', $semesterId);
        }
        
        if ($disciplineId) {
            $builder->where('tbl_continuous_assessment.discipline_id', $disciplineId);
        }
        
        return $builder->orderBy('tbl_continuous_assessment.assessment_date', 'DESC')
            ->findAll();
    }
    
    /**
     * Get average by student and discipline
     */
    public function getAverage($studentId, $disciplineId, $semesterId)
    {
        $result = $this->select('AVG(score) as average, COUNT(*) as total')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_continuous_assessment.enrollment_id')
            ->where('tbl_enrollments.student_id', $studentId)
            ->where('tbl_continuous_assessment.discipline_id', $disciplineId)
            ->where('tbl_continuous_assessment.semester_id', $semesterId)
            ->first();
        
        return [
            'average' => $result->average ?? 0,
            'total' => $result->total ?? 0
        ];
    }
}