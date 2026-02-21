<?php

namespace App\Models;

class ExamPeriodModel extends BaseModel
{
    protected $table = 'tbl_exam_periods';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'period_name',
        'academic_year_id',
        'semester_id',
        'period_type',
        'start_date',
        'end_date',
        'description',
        'status',
        'is_published',
        'created_by'
    ];
    
    protected $validationRules = [
        'period_name' => 'required|min_length[3]|max_length[255]',
        'academic_year_id' => 'required|numeric',
        'semester_id' => 'required|numeric',
        'period_type' => 'required|in_list[Normal,Recurso,Especial,Final,Admissão]',
        'start_date' => 'required|valid_date',
        'end_date' => 'required|valid_date'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    /**
     * Get active exam periods for current academic year
     */
    public function getActivePeriods($academicYearId = null)
    {
        if (!$academicYearId) {
            $academicYearModel = new AcademicYearModel();
            $currentYear = $academicYearModel->getCurrent();
            $academicYearId = $currentYear->id ?? null;
        }
        
        return $this->select('
                tbl_exam_periods.*,
                tbl_academic_years.year_name,
                tbl_semesters.semester_name,
                (SELECT COUNT(*) FROM tbl_exam_schedules WHERE exam_period_id = tbl_exam_periods.id) as total_exams
            ')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_exam_periods.academic_year_id')
            ->join('tbl_semesters', 'tbl_semesters.id = tbl_exam_periods.semester_id')
            ->where('tbl_exam_periods.academic_year_id', $academicYearId)
            ->orderBy('tbl_exam_periods.start_date', 'DESC')
            ->findAll();
    }
    
    /**
     * Get current/ongoing exam period
     */
    public function getCurrentPeriod()
    {
        $today = date('Y-m-d');
        
        return $this->where('start_date <=', $today)
            ->where('end_date >=', $today)
            ->where('status !=', 'Cancelado')
            ->first();
    }
    
    /**
     * Get exam period with full details
     */
    public function getWithDetails($id)
    {
        return $this->select('
                tbl_exam_periods.*,
                tbl_academic_years.year_name,
                tbl_semesters.semester_name,
                tbl_users.first_name as created_by_name,
                tbl_users.last_name as created_by_lastname
            ')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_exam_periods.academic_year_id')
            ->join('tbl_semesters', 'tbl_semesters.id = tbl_exam_periods.semester_id')
            ->join('tbl_users', 'tbl_users.id = tbl_exam_periods.created_by', 'left')
            ->find($id);
    }
    
    /**
     * Get statistics for a period
     */
    public function getPeriodStats($periodId)
    {
        $db = db_connect();
        
        $query = $db->query("
            SELECT 
                COUNT(DISTINCT es.id) as total_exams,
                COUNT(DISTINCT es.class_id) as total_classes,
                COUNT(DISTINCT es.discipline_id) as total_disciplines,
                SUM(CASE WHEN es.status = 'Realizado' THEN 1 ELSE 0 END) as completed_exams,
                SUM(CASE WHEN es.status = 'Agendado' THEN 1 ELSE 0 END) as pending_exams,
                COUNT(DISTINCT er.id) as total_results,
                AVG(er.score) as average_score
            FROM tbl_exam_schedules es
            LEFT JOIN tbl_exam_results er ON er.exam_schedule_id = es.id
            WHERE es.exam_period_id = ?
        ", [$periodId])->getRow();
        
        return $query;
    }
}