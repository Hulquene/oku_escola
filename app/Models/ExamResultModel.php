<?php

namespace App\Models;

class ExamResultModel extends BaseModel
{
    protected $table = 'tbl_exam_results';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'exam_schedule_id',
        'enrollment_id',
        'assessment_type',
        'score',
        'score_percentage',
        'grade',
        'is_absent',
        'is_cheating',
        'observations',
        'recorded_by',
        'verified_by',
        'verified_at'
    ];
    
    protected $validationRules = [
        'exam_schedule_id' => 'required|numeric',
        'enrollment_id' => 'required|numeric',
        'assessment_type' => 'required|in_list[AC,NPP,NPT,E,NEE,NEO,NEP,PAP,NEC]',
        'score' => 'required|numeric'
    ];
    
    protected $validationMessages = [
        'exam_schedule_id' => [
            'required' => 'O ID do agendamento é obrigatório',
            'numeric' => 'O ID do agendamento deve ser numérico'
        ],
        'enrollment_id' => [
            'required' => 'O ID da matrícula é obrigatório',
            'numeric' => 'O ID da matrícula deve ser numérico'
        ],
        'assessment_type' => [
            'required' => 'O tipo de avaliação é obrigatório',
            'in_list' => 'Tipo de avaliação inválido'
        ],
        'score' => [
            'required' => 'A nota é obrigatória',
            'numeric' => 'A nota deve ser numérica'
        ]
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
            // Verificar se já existe
            $existing = $this->where('exam_schedule_id', $result['exam_schedule_id'])
                ->where('enrollment_id', $result['enrollment_id'])
                ->first();
            
            // Buscar o agendamento para obter a nota máxima
            $scheduleModel = new \App\Models\ExamScheduleModel();
            $schedule = $scheduleModel->find($result['exam_schedule_id']);
            
            // Calcular percentual se necessário
            if ($schedule && isset($result['score']) && $schedule->max_score > 0) {
                $result['score_percentage'] = ($result['score'] / $schedule->max_score) * 100;
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
     * Get results by exam schedule
     */
    public function getByExamSchedule($examScheduleId)
    {
        return $this->select('
                tbl_exam_results.*,
                tbl_enrollments.student_id,
                tbl_users.first_name,
                tbl_users.last_name,
                tbl_students.student_number,
                tbl_exam_boards.board_code as assessment_type
            ')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_exam_results.enrollment_id')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->join('tbl_exam_schedules', 'tbl_exam_schedules.id = tbl_exam_results.exam_schedule_id')
            ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exam_schedules.exam_board_id')
            ->where('tbl_exam_results.exam_schedule_id', $examScheduleId)
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
                tbl_exam_schedules.exam_date,
                tbl_disciplines.discipline_name,
                tbl_exam_boards.board_name,
                tbl_exam_boards.board_code as assessment_type,
                tbl_exam_boards.weight
            ')
            ->join('tbl_exam_schedules', 'tbl_exam_schedules.id = tbl_exam_results.exam_schedule_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id')
            ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exam_schedules.exam_board_id')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_exam_results.enrollment_id')
            ->where('tbl_enrollments.student_id', $studentId);
        
        if ($semesterId) {
            $builder->join('tbl_exam_periods', 'tbl_exam_periods.id = tbl_exam_schedules.exam_period_id')
                ->where('tbl_exam_periods.semester_id', $semesterId);
        }
        
        return $builder->orderBy('tbl_exam_schedules.exam_date', 'DESC')
            ->findAll();
    }
    
    /**
     * Get exam statistics
     */
    public function getExamStatistics($examScheduleId)
    {
        // Buscar o agendamento para obter a nota máxima e de aprovação
        $scheduleModel = new \App\Models\ExamScheduleModel();
        $schedule = $scheduleModel->select('tbl_exam_schedules.*, tbl_exam_boards.board_code')
            ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exam_schedules.exam_board_id')
            ->where('tbl_exam_schedules.id', $examScheduleId)
            ->first();
        
        if (!$schedule) {
            return (object)[
                'total' => 0,
                'average' => 0,
                'minimum' => 0,
                'maximum' => 0,
                'approved' => 0,
                'failed' => 0,
                'approval_rate' => 0,
                'assessment_type' => ''
            ];
        }
        
        $maxScore = $schedule->max_score ?? 20;
        $passingScore = $schedule->approval_score ?? 10;
        
        // Consulta SQL otimizada
        $stats = $this->select("
                COUNT(*) as total,
                AVG(score) as average,
                MIN(score) as minimum,
                MAX(score) as maximum,
                SUM(CASE WHEN score >= {$passingScore} AND is_absent = 0 THEN 1 ELSE 0 END) as approved,
                SUM(CASE WHEN score < {$passingScore} AND is_absent = 0 THEN 1 ELSE 0 END) as failed,
                ROUND((SUM(CASE WHEN score >= {$passingScore} AND is_absent = 0 THEN 1 ELSE 0 END) / COUNT(*)) * 100, 1) as approval_rate
            ")
            ->where('exam_schedule_id', $examScheduleId)
            ->first();
        
        if (!$stats || $stats->total == 0) {
            return (object)[
                'total' => 0,
                'average' => 0,
                'minimum' => 0,
                'maximum' => 0,
                'approved' => 0,
                'failed' => 0,
                'approval_rate' => 0,
                'assessment_type' => $schedule->board_code ?? ''
            ];
        }
        
        // Arredondar valores
        $stats->average = round($stats->average, 1);
        $stats->minimum = round($stats->minimum, 1);
        $stats->maximum = round($stats->maximum, 1);
        $stats->assessment_type = $schedule->board_code ?? '';
        
        return $stats;
    }
}