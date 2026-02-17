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
    protected $updatedField = null;
    
    /**
     * Mark attendance for multiple students - Versão final
     */
   /**
 * Mark attendance for multiple students - Versão com registro em lote
 */
/**
 * Mark attendance for multiple students - Versão com Batch Processing (CI4 Oficial)
 */
public function markBulk(array $attendances)
{
    if (empty($attendances)) {
        log_message('error', '📝 ATTENDANCE: Tentativa de registrar presenças com lista vazia');
        return false;
    }
    
    log_message('info', '📝 ATTENDANCE: Iniciando registro de ' . count($attendances) . ' presenças');
    
    $this->transStart();
    
    // Separar registros para INSERT e UPDATE
    $inserts = [];
    $updates = [];
    
    foreach ($attendances as $attendance) {
        $enrollmentId = (int)$attendance['enrollment_id'];
        $attendanceDate = $attendance['attendance_date'];
        $disciplineId = !empty($attendance['discipline_id']) ? (int)$attendance['discipline_id'] : null;
        
        // Verificar se já existe usando SQL direto (mais confiável)
        $sql = "SELECT id FROM {$this->table} 
                WHERE enrollment_id = {$enrollmentId} 
                AND attendance_date = " . $this->db->escape($attendanceDate);
        
        if ($disciplineId !== null) {
            $sql .= " AND discipline_id = {$disciplineId}";
        } else {
            $sql .= " AND discipline_id IS NULL";
        }
        
        $query = $this->db->query($sql);
        $existing = $query->getRow();
        
        $dataToSave = [
            'enrollment_id' => $enrollmentId,
            'class_id' => (int)$attendance['class_id'],
            'discipline_id' => $disciplineId,
            'attendance_date' => $attendanceDate,
            'status' => $attendance['status'],
            'justification' => !empty($attendance['justification']) ? $attendance['justification'] : null,
            'marked_by' => (int)$attendance['marked_by']
        ];
        
        if ($existing) {
            $dataToSave['id'] = $existing->id;
            $updates[] = $dataToSave;
        } else {
            $inserts[] = $dataToSave;
        }
    }
    
    // Processar INSERTs em lote
    $insertCount = 0;
    if (!empty($inserts)) {
        try {
            $insertCount = $this->insertBatch($inserts);
            log_message('info', "📝 ATTENDANCE: Inseridos {$insertCount} novos registros em lote");
        } catch (\Exception $e) {
            log_message('error', "📝 ATTENDANCE: Erro no insertBatch: " . $e->getMessage());
            $this->transRollback();
            return false;
        }
    }
    
    // Processar UPDATEs individualmente (não há updateBatch nativo no CI4)
    // Mas podemos fazer updates em lote com SQL direto
    $updateCount = 0;
    if (!empty($updates)) {
        // Opção 1: Updates individuais (mais seguro)
        foreach ($updates as $update) {
            $updateData = [
                'status' => $update['status'],
                'justification' => $update['justification'],
                'marked_by' => $update['marked_by']
            ];
            
            if ($this->update($update['id'], $updateData)) {
                $updateCount++;
            }
        }
        
        // Opção 2: Updates em lote com CASE WHEN (mais rápido para muitos registros)
        // Descomente se quiser usar esta opção
        /*
        if (count($updates) > 5) { // Usar batch apenas se houver mais de 5 registros
            $caseSql = "UPDATE {$this->table} SET 
                        status = CASE id ";
            foreach ($updates as $update) {
                $caseSql .= " WHEN {$update['id']} THEN " . $this->db->escape($update['status']);
            }
            $caseSql .= " END,
                        justification = CASE id ";
            foreach ($updates as $update) {
                $caseSql .= " WHEN {$update['id']} THEN " . ($update['justification'] !== null ? $this->db->escape($update['justification']) : 'NULL');
            }
            $caseSql .= " END,
                        marked_by = CASE id ";
            foreach ($updates as $update) {
                $caseSql .= " WHEN {$update['id']} THEN {$update['marked_by']}";
            }
            $caseSql .= " END
                        WHERE id IN (" . implode(',', array_column($updates, 'id')) . ")";
            
            $this->db->query($caseSql);
            $updateCount = count($updates);
        } else {
            foreach ($updates as $update) {
                $updateData = [
                    'status' => $update['status'],
                    'justification' => $update['justification'],
                    'marked_by' => $update['marked_by']
                ];
                if ($this->update($update['id'], $updateData)) {
                    $updateCount++;
                }
            }
        }
        */
        
        log_message('info', "📝 ATTENDANCE: Atualizados {$updateCount} registros existentes");
    }
    
    $this->transComplete();
    
    $status = $this->transStatus();
    
    if ($status) {
        log_message('info', "📝 ATTENDANCE: Operação concluída com sucesso! {$insertCount} inserções, {$updateCount} atualizações.");
    } else {
        log_message('error', '📝 ATTENDANCE: Falha na operação. Transação não foi concluída com sucesso.');
    }
    
    return $status;
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
}