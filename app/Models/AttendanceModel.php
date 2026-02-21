<?php

namespace App\Models;

use CodeIgniter\Model;

class AttendanceModel extends Model
{
    protected $table = 'tbl_attendance';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'enrollment_id',
        'class_id',
        'discipline_id',
        'semester_id',  // ADICIONADO
        'attendance_date',
        'status',
        'justification',
        'marked_by'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
    
    // Status válidos
    const STATUS_PRESENTE = 'Presente';
    const STATUS_AUSENTE = 'Ausente';
    const STATUS_ATRASADO = 'Atrasado';
    const STATUS_DISPENSADO = 'Dispensado';
    const STATUS_JUSTIFICADO = 'Falta Justificada';
    
    /**
     * Busca presenças por período
     */
    public function getByPeriod($enrollmentId, $startDate, $endDate)
    {
        return $this->select('
                tbl_attendance.*,
                tbl_disciplines.discipline_name
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_attendance.discipline_id', 'left')
            ->where('tbl_attendance.enrollment_id', $enrollmentId)
            ->where('tbl_attendance.attendance_date >=', $startDate)
            ->where('tbl_attendance.attendance_date <=', $endDate)
            ->orderBy('tbl_attendance.attendance_date', 'DESC')
            ->findAll();
    }
    
    /**
     * Estatísticas de presença por semestre
     */
    public function getStatsBySemester($enrollmentId, $semesterId = null)
    {
        $builder = $this->select('
                COUNT(*) as total,
                SUM(CASE WHEN status = "Ausente" THEN 1 ELSE 0 END) as total_absences,
                SUM(CASE WHEN status = "Falta Justificada" THEN 1 ELSE 0 END) as justified_absences,
                SUM(CASE WHEN status IN ("Presente", "Atrasado", "Falta Justificada") THEN 1 ELSE 0 END) as present
            ')
            ->where('enrollment_id', $enrollmentId);
        
        if ($semesterId) {
            $builder->where('semester_id', $semesterId);
        }
        
        return $builder->first();
    }
    
    /**
     * Estatísticas por disciplina
     */
    public function getStatsByDiscipline($enrollmentId, $semesterId = null)
    {
        $builder = $this->select('
                tbl_attendance.discipline_id,
                tbl_disciplines.discipline_name,
                COUNT(*) as total,
                SUM(CASE WHEN tbl_attendance.status IN ("Presente", "Atrasado", "Falta Justificada") THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN tbl_attendance.status = "Ausente" THEN 1 ELSE 0 END) as absent
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_attendance.discipline_id')
            ->where('tbl_attendance.enrollment_id', $enrollmentId)
            ->groupBy('tbl_attendance.discipline_id');
        
        if ($semesterId) {
            $builder->where('tbl_attendance.semester_id', $semesterId);
        }
        
        return $builder->findAll();
    }
    
    /**
     * Percentual de presença
     */
    public function getAttendancePercentage($enrollmentId, $semesterId = null)
    {
        $stats = $this->getStatsBySemester($enrollmentId, $semesterId);
        
        if (!$stats || $stats->total == 0) {
            return 0;
        }
        
        return round(($stats->present / $stats->total) * 100, 1);
    }
    
    /**
     * Busca presenças de uma disciplina
     */
    public function getByDiscipline($enrollmentId, $disciplineId, $semesterId = null)
    {
        $builder = $this->where('enrollment_id', $enrollmentId)
            ->where('discipline_id', $disciplineId);
        
        if ($semesterId) {
            $builder->where('semester_id', $semesterId);
        }
        
        return $builder->orderBy('attendance_date', 'DESC')
            ->findAll();
    }
    
    /**
     * Marca presença em lote
     */
    public function saveBulk(array $attendances)
    {
        $db = db_connect();
        $db->transStart();
        
        foreach ($attendances as $attendance) {
            // Verificar se já existe
            $existing = $this->where('enrollment_id', $attendance['enrollment_id'])
                ->where('class_id', $attendance['class_id'])
                ->where('attendance_date', $attendance['attendance_date'])
                ->where('discipline_id', $attendance['discipline_id'] ?? null)
                ->first();
            
            if ($existing) {
                $this->update($existing->id, $attendance);
            } else {
                $this->insert($attendance);
            }
        }
        
        $db->transComplete();
        
        return $db->transStatus();
    }
}