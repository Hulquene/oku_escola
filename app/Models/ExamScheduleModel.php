<?php

namespace App\Models;

class ExamScheduleModel extends BaseModel
{
    protected $table = 'tbl_exam_schedules';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'exam_period_id',
        'class_id',
        'discipline_id',
        'exam_board_id',
        'exam_date',
        'exam_time',
        'exam_room',
        'duration_minutes',
        'max_score',
        'min_score',
        'approval_score',
        'observations',
        'status'
    ];
    
    protected $validationRules = [
        'exam_period_id' => 'required|numeric',
        'class_id' => 'required|numeric',
        'discipline_id' => 'required|numeric',
        'exam_board_id' => 'required|numeric',
        'exam_date' => 'required|valid_date',
        'max_score' => 'permit_empty|numeric'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    /**
     * Get exams for a period with full details
     */
    public function getByPeriod($periodId)
    {
        return $this->select('
                tbl_exam_schedules.*,
                tbl_classes.class_name,
                tbl_classes.class_code,
                tbl_disciplines.discipline_name,
                tbl_disciplines.discipline_code,
                tbl_exam_boards.board_name,
                tbl_exam_boards.board_type,
                tbl_exam_boards.weight,
                (SELECT COUNT(*) FROM tbl_enrollments WHERE class_id = tbl_exam_schedules.class_id AND status = "Ativo") as total_students,
                (SELECT COUNT(*) FROM tbl_exam_results WHERE exam_schedule_id = tbl_exam_schedules.id) as results_count
            ')
            ->join('tbl_classes', 'tbl_classes.id = tbl_exam_schedules.class_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id')
            ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exam_schedules.exam_board_id')
            ->where('tbl_exam_schedules.exam_period_id', $periodId)
            ->orderBy('tbl_exam_schedules.exam_date', 'ASC')
            ->orderBy('tbl_exam_schedules.exam_time', 'ASC')
            ->findAll();
    }
    
    /**
     * Get upcoming exams for a teacher
     */
    public function getUpcomingByTeacher($teacherId, $limit = 10)
    {
        return $this->select('
                tbl_exam_schedules.*,
                tbl_classes.class_name,
                tbl_disciplines.discipline_name,
                tbl_exam_periods.period_name
            ')
            ->join('tbl_class_disciplines', 'tbl_class_disciplines.class_id = tbl_exam_schedules.class_id 
                    AND tbl_class_disciplines.discipline_id = tbl_exam_schedules.discipline_id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_exam_schedules.class_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id')
            ->join('tbl_exam_periods', 'tbl_exam_periods.id = tbl_exam_schedules.exam_period_id')
            ->where('tbl_class_disciplines.teacher_id', $teacherId)
            ->where('tbl_exam_schedules.exam_date >=', date('Y-m-d'))
            ->where('tbl_exam_schedules.status', 'Agendado')
            ->orderBy('tbl_exam_schedules.exam_date', 'ASC')
            ->orderBy('tbl_exam_schedules.exam_time', 'ASC')
            ->limit($limit)
            ->findAll();
    }
    
    /**
     * Get exams for a class
     */
    public function getByClass($classId, $semesterId = null)
    {
        $builder = $this->select('
                tbl_exam_schedules.*,
                tbl_disciplines.discipline_name,
                tbl_exam_boards.board_name,
                tbl_exam_boards.board_type,
                tbl_exam_periods.period_name
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id')
            ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exam_schedules.exam_board_id')
            ->join('tbl_exam_periods', 'tbl_exam_periods.id = tbl_exam_schedules.exam_period_id')
            ->where('tbl_exam_schedules.class_id', $classId);
            
        if ($semesterId) {
            $builder->where('tbl_exam_periods.semester_id', $semesterId);
        }
        
        return $builder->orderBy('tbl_exam_schedules.exam_date', 'ASC')->findAll();
    }
/**
 * Generate schedule for a period automatically
 */
public function generateForPeriod($periodId, $classIds = [], $examBoardId = null)
{
    log_message('debug', '--- INÍCIO generateForPeriod (Model) ---');
    log_message('debug', 'Period ID: ' . $periodId);
    log_message('debug', 'Class IDs: ' . json_encode($classIds));
    log_message('debug', 'Exam Board ID: ' . ($examBoardId ?? 'null'));
    
    $periodModel = new ExamPeriodModel();
    $period = $periodModel->find($periodId);
    
    if (!$period) {
        log_message('error', 'Período não encontrado no model: ' . $periodId);
        return false;
    }
    
    log_message('debug', 'Período - start_date: ' . $period->start_date . ', end_date: ' . $period->end_date . ', semester_id: ' . $period->semester_id);
    
    $classModel = new ClassModel();
    $classDisciplineModel = new ClassDisciplineModel();
    
    // Get classes
    if (empty($classIds)) {
        log_message('debug', 'Buscando todas as turmas do ano letivo: ' . $period->academic_year_id);
        $classes = $classModel->where('academic_year_id', $period->academic_year_id)
            ->where('is_active', 1)
            ->findAll();
        $classIds = array_column($classes, 'id');
        log_message('debug', 'Turmas encontradas: ' . json_encode($classIds));
    }
    
    // Default exam board based on period type
    if (!$examBoardId) {
        $boardModel = new ExamBoardModel();
        $boardType = $period->period_type == 'Recurso' ? 'Recurso' : 
                    ($period->period_type == 'Final' ? 'Final' : 'Normal');
        log_message('debug', 'Buscando exam board do tipo: ' . $boardType);
        $board = $boardModel->where('board_type', $boardType)->first();
        $examBoardId = $board->id ?? 6;
        log_message('debug', 'Exam Board ID selecionado: ' . $examBoardId);
    }
    
    $schedules = [];
    $daysBetween = (strtotime($period->end_date) - strtotime($period->start_date)) / (60 * 60 * 24);
    log_message('debug', 'Dias entre start e end: ' . $daysBetween);
    
    $totalSchedules = 0;
    
    foreach ($classIds as $classId) {
        log_message('debug', 'Processando turma ID: ' . $classId);
        
        // Get disciplines for this class
        $disciplines = $classDisciplineModel
            ->where('class_id', $classId)
            ->where('semester_id', $period->semester_id)
            ->where('is_active', 1)
            ->findAll();
        
        $disciplineCount = count($disciplines);
        log_message('debug', 'Disciplinas encontradas para turma ' . $classId . ': ' . $disciplineCount);
        
        if ($disciplineCount == 0) {
            log_message('warning', 'Turma ' . $classId . ' não tem disciplinas - ignorando');
            continue;
        }
        
        $interval = $disciplineCount > 0 ? floor($daysBetween / $disciplineCount) : 1;
        log_message('debug', 'Intervalo entre exames: ' . $interval . ' dias');
        
        foreach ($disciplines as $index => $cd) {
            $examDate = date('Y-m-d', strtotime($period->start_date . ' + ' . ($index * $interval) . ' days'));
            
            // Verificar se a data não ultrapassa o fim do período
            if (strtotime($examDate) > strtotime($period->end_date)) {
                log_message('warning', 'Data do exame ' . $examDate . ' ultrapassa o fim do período - ajustando');
                $examDate = $period->end_date;
            }
            
            log_message('debug', 'Agendando exame: disciplina_id=' . $cd->discipline_id . ', data=' . $examDate);
            
            $schedules[] = [
                'exam_period_id' => $periodId,
                'class_id' => $classId,
                'discipline_id' => $cd->discipline_id,
                'exam_board_id' => $examBoardId,
                'exam_date' => $examDate,
                'exam_time' => '08:00:00',
                'duration_minutes' => 120,
                'status' => 'Agendado'
            ];
            $totalSchedules++;
        }
    }
    
    log_message('debug', 'Total de schedules a inserir: ' . $totalSchedules);
    
    if (!empty($schedules)) {
        log_message('debug', 'Inserindo ' . count($schedules) . ' registros no banco');
        $result = $this->insertBatch($schedules);
        log_message('debug', 'Resultado da inserção: ' . ($result ? 'true' : 'false'));
        log_message('debug', '--- FIM generateForPeriod (Model) ---');
        return $result;
    }
    
    log_message('warning', 'Nenhum schedule gerado (schedules vazio)');
    log_message('debug', '--- FIM generateForPeriod (Model) ---');
    return false;
}
    
    /**
     * Check for scheduling conflicts
     */
    public function checkConflicts($classId, $examDate, $examTime = null, $excludeId = null)
    {
        $builder = $this->where('class_id', $classId)
            ->where('exam_date', $examDate)
            ->where('status !=', 'Cancelado');
            
        if ($examTime) {
            $builder->where('exam_time', $examTime);
        }
        
        if ($excludeId) {
            $builder->where('id !=', $excludeId);
        }
        
        return $builder->countAllResults() > 0;
    }

/**
 * Update exam status
 * 
 * @param int $id ID do agendamento
 * @param string $status Novo status (Agendado, Realizado, Cancelado, Adiado)
 * @param int $userId ID do usuário que está alterando (opcional)
 * @return bool
 */
public function updateStatus($id, $status, $userId = null)
{
    // Validar status permitidos
    $allowedStatus = ['Agendado', 'Realizado', 'Cancelado', 'Adiado'];
    if (!in_array($status, $allowedStatus)) {
        log_message('error', "Tentativa de atualizar status para valor inválido: {$status}");
        return false;
    }
    
    // Buscar o exame para verificar se existe
    $exam = $this->find($id);
    if (!$exam) {
        log_message('error', "Tentativa de atualizar status de exame inexistente ID: {$id}");
        return false;
    }
    
    // Verificar se já está com o mesmo status
    if ($exam->status === $status) {
        log_message('info', "Exame ID {$id} já está com status {$status}");
        return true; // Nada a fazer, mas consideramos sucesso
    }
    
    // Registrar status anterior para log
    $oldStatus = $exam->status;
    
    // Atualizar status
    $result = $this->update($id, ['status' => $status]);
    
    if ($result) {
        log_message('info', "Exame ID {$id} status alterado de {$oldStatus} para {$status}" . 
                    ($userId ? " pelo usuário {$userId}" : ""));
        
        // Disparar evento ou trigger se necessário
        $this->afterStatusChange($id, $oldStatus, $status, $userId);
    }
    
    return $result;
}

}