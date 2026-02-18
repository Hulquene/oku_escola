<?php

namespace App\Models;

class AttendanceModel extends BaseModel
{
    protected $table = 'tbl_attendance';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'enrollment_id',
        'class_id',
        'discipline_id',
        'attendance_date',
        'status',
        'justification',
        'marked_by'
    ];
    
    protected $validationRules = [
        'enrollment_id' => 'required|numeric',
        'class_id' => 'required|numeric',
        'attendance_date' => 'required|valid_date',
        'status' => 'required'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at'; // Adicionar updated_at
    
    /**
     * Mark attendance for multiple students - Versão corrigida
     */
    public function markBulk(array $attendances)
    {
        if (empty($attendances)) {
            log_message('error', '📝 ATTENDANCE: Tentativa de registrar presenças com lista vazia');
            return false;
        }
        
        log_message('info', '📝 ATTENDANCE: Iniciando registro de ' . count($attendances) . ' presenças');
        
        $this->transStart();
        
        $successCount = 0;
        
        foreach ($attendances as $attendance) {
            try {
                // Verificar se já existe registro para este aluno nesta data/disciplina
                $existing = $this->where('enrollment_id', $attendance['enrollment_id'])
                    ->where('attendance_date', $attendance['attendance_date']);
                
                // Adicionar condição para discipline_id (pode ser NULL)
                if (isset($attendance['discipline_id']) && !empty($attendance['discipline_id'])) {
                    $existing->where('discipline_id', $attendance['discipline_id']);
                } else {
                    $existing->where('discipline_id', null);
                }
                
                $existingRecord = $existing->first();
                
                $dataToSave = [
                    'enrollment_id' => (int)$attendance['enrollment_id'],
                    'class_id' => (int)$attendance['class_id'],
                    'discipline_id' => !empty($attendance['discipline_id']) ? (int)$attendance['discipline_id'] : null,
                    'attendance_date' => $attendance['attendance_date'],
                    'status' => $attendance['status'],
                    'justification' => !empty($attendance['justification']) ? $attendance['justification'] : null,
                    'marked_by' => (int)$attendance['marked_by']
                ];
                
                if ($existingRecord) {
                    // Atualizar registro existente
                    $result = $this->update($existingRecord->id, $dataToSave);
                    if ($result) {
                        $successCount++;
                        log_message('info', "📝 ATTENDANCE: Atualizado registro ID {$existingRecord->id} para enrollment_id {$attendance['enrollment_id']}");
                    }
                } else {
                    // Inserir novo registro
                    $result = $this->insert($dataToSave);
                    if ($result) {
                        $successCount++;
                        log_message('info', "📝 ATTENDANCE: Inserido novo registro para enrollment_id {$attendance['enrollment_id']}");
                    }
                }
            } catch (\Exception $e) {
                log_message('error', "📝 ATTENDANCE: Erro ao processar enrollment_id {$attendance['enrollment_id']}: " . $e->getMessage());
                $this->transRollback();
                return false;
            }
        }
        
        $this->transComplete();
        
        if ($this->transStatus() && $successCount === count($attendances)) {
            log_message('info', "📝 ATTENDANCE: Todos os {$successCount} registros foram salvos com sucesso!");
            return true;
        } else {
            log_message('error', "📝 ATTENDANCE: Falha ao salvar registros. Sucesso: {$successCount} de " . count($attendances));
            return false;
        }
    }

    /**
     * Get attendance by class and date
     */
    public function getByClassAndDate($classId, $date, $disciplineId = null)
    {
        $builder = $this->select('
                tbl_attendance.*,
                tbl_enrollments.student_id,
                tbl_users.first_name,
                tbl_users.last_name,
                tbl_students.student_number
            ')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_attendance.enrollment_id')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->where('tbl_attendance.class_id', $classId)
            ->where('tbl_attendance.attendance_date', $date);
        
        if ($disciplineId) {
            $builder->where('tbl_attendance.discipline_id', $disciplineId);
        }
        
        return $builder->findAll();
    }
    
    /**
     * Get attendance report for student
     */
    public function getStudentReport($studentId, $startDate = null, $endDate = null)
    {
        $builder = $this->select('
                tbl_attendance.*,
                tbl_classes.class_name,
                tbl_disciplines.discipline_name
            ')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_attendance.enrollment_id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_attendance.class_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_attendance.discipline_id', 'left')
            ->where('tbl_enrollments.student_id', $studentId);
        
        if ($startDate) {
            $builder->where('tbl_attendance.attendance_date >=', $startDate);
        }
        
        if ($endDate) {
            $builder->where('tbl_attendance.attendance_date <=', $endDate);
        }
        
        return $builder->orderBy('tbl_attendance.attendance_date', 'DESC')
            ->findAll();
    }
    
    /**
     * Get attendance statistics
     */
    public function getStatistics($classId = null, $startDate = null, $endDate = null)
    {
        $builder = $this->select('
                COUNT(*) as total,
                SUM(CASE WHEN status = "Presente" THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN status = "Ausente" THEN 1 ELSE 0 END) as absent,
                SUM(CASE WHEN status = "Atrasado" THEN 1 ELSE 0 END) as late,
                SUM(CASE WHEN status = "Falta Justificada" THEN 1 ELSE 0 END) as justified,
                SUM(CASE WHEN status = "Dispensado" THEN 1 ELSE 0 END) as excused
            ');
        
        if ($classId) {
            $builder->where('class_id', $classId);
        }
        
        if ($startDate) {
            $builder->where('attendance_date >=', $startDate);
        }
        
        if ($endDate) {
            $builder->where('attendance_date <=', $endDate);
        }
        
        return $builder->first();
    }
    
    /**
     * Get attendance rate by class
     */
    public function getAttendanceRate($classId, $startDate, $endDate)
    {
        $stats = $this->getStatistics($classId, $startDate, $endDate);
        
        if ($stats && $stats->total > 0) {
            $present = $stats->present + $stats->late + $stats->justified;
            return round(($present / $stats->total) * 100, 2);
        }
        
        return 0;
    }
    /**
 * Get attendance percentage for a student
 * 
 * @param int $enrollmentId ID da matrícula
 * @return float
 */
public function getStudentAttendancePercentage($enrollmentId)
{
    $total = $this->where('enrollment_id', $enrollmentId)
        ->countAllResults();
    
    if ($total == 0) {
        return 0;
    }
    
    $present = $this->where('enrollment_id', $enrollmentId)
        ->whereIn('status', ['Presente', 'Atrasado', 'Falta Justificada'])
        ->countAllResults();
    
    return round(($present / $total) * 100, 2);
}

/**
 * Get attendance by enrollment ID
 * 
 * @param int $enrollmentId ID da matrícula
 * @return array
 */
public function getByEnrollment($enrollmentId)
{
    return $this->select('
            tbl_attendance.*,
            tbl_disciplines.discipline_name
        ')
        ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_attendance.discipline_id', 'left')
        ->where('tbl_attendance.enrollment_id', $enrollmentId)
        ->orderBy('tbl_attendance.attendance_date', 'DESC')
        ->findAll();
}
}