<?php

namespace App\Models;

use CodeIgniter\Model;

class ReportCardModel extends Model
{
    protected $table = 'tbl_report_cards';
    protected $primaryKey = 'id';
    
    protected $allowedFields = [
        'enrollment_id',
        'semester_id',
        'report_number',
        'generated_date',
        'average_score',
        'total_absences',
        'justified_absences',
        'status',
        'observations',
        'generated_by'
    ];
    
    // Se você adicionou a coluna updated_at, mantenha true
    // Se não adicionou, mude para false
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at'; // Comente esta linha se não tiver a coluna
    
    /**
     * Gera um novo boletim para o aluno
     */
    public function generateForStudent($enrollmentId, $semesterId)
    {
        // Verificar se já existe
        $existing = $this->where('enrollment_id', $enrollmentId)
            ->where('semester_id', $semesterId)
            ->first();
        
        if ($existing) {
            return $existing->id;
        }
        
        // Usar os novos modelos
        $disciplineAverageModel = new DisciplineAverageModel();
        $attendanceModel = new AttendanceModel();
        $semesterModel = new SemesterModel();
        $enrollmentModel = new EnrollmentModel();
        
        // Buscar informações da matrícula
        $enrollment = $enrollmentModel
            ->select('
                tbl_enrollments.*,
                tbl_students.student_number,
                tbl_users.first_name,
                tbl_users.last_name,
                tbl_classes.class_name,
                tbl_academic_years.year_name
            ')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_enrollments.academic_year_id')
            ->find($enrollmentId);
        
        // Buscar médias por disciplina
        $averages = $disciplineAverageModel
            ->select('
                tbl_discipline_averages.*,
                tbl_disciplines.discipline_name
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_discipline_averages.discipline_id')
            ->where('tbl_discipline_averages.enrollment_id', $enrollmentId)
            ->where('tbl_discipline_averages.semester_id', $semesterId)
            ->findAll();
        
        // Calcular média geral
        $totalScore = 0;
        $count = 0;
        foreach ($averages as $avg) {
            if ($avg->final_score) {
                $totalScore += $avg->final_score;
                $count++;
            }
        }
        $averageScore = $count > 0 ? round($totalScore / $count, 2) : 0;
        
        // Buscar presenças
        $attendanceStats = $attendanceModel->getStatsBySemester($enrollmentId, $semesterId);
        
        // Buscar informações do semestre
        $semester = $semesterModel->find($semesterId);
        
        // Gerar número do boletim
        $reportNumber = 'REL-' . date('Y') . '-' . str_pad($enrollmentId, 5, '0', STR_PAD_LEFT) . '-' . $semesterId;
        
        // Inserir novo boletim
        $data = [
            'enrollment_id' => $enrollmentId,
            'semester_id' => $semesterId,
            'report_number' => $reportNumber,
            'generated_date' => date('Y-m-d'),
            'average_score' => $averageScore,
            'total_absences' => $attendanceStats->total_absences ?? 0,
            'justified_absences' => $attendanceStats->justified_absences ?? 0,
            'status' => 'Emitido',
            'generated_by' => session()->get('user_id')
        ];
        
        $this->insert($data);
        
        return $this->getInsertID();
    }
    
    /**
     * Busca boletim com todos os detalhes
     */
    /**
 * Busca boletim com todos os detalhes
 */
    public function getWithDetails($id)
    {
        $result = $this->select('
                tbl_report_cards.*,
                tbl_enrollments.student_id,
                tbl_enrollments.class_id,
                tbl_enrollments.enrollment_number,
                tbl_students.student_number,
                tbl_users.first_name,
                tbl_users.last_name,
                tbl_classes.class_name,
                tbl_classes.class_shift,
                tbl_academic_years.year_name,
                tbl_semesters.semester_name,
                tbl_semesters.semester_type,
                tbl_semesters.start_date as semester_start,
                tbl_semesters.end_date as semester_end
            ')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_report_cards.enrollment_id')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_enrollments.academic_year_id')
            ->join('tbl_semesters', 'tbl_semesters.id = tbl_report_cards.semester_id')
            ->where('tbl_report_cards.id', $id)
            ->first();
        
        // Garantir que retorna como objeto
        return is_array($result) ? (object)$result : $result;
    }
        
    /**
     * Busca boletins do aluno
     */
    public function getStudentReportCards($studentId)
    {
        return $this->select('
                tbl_report_cards.*,
                tbl_semesters.semester_name,
                tbl_semesters.semester_type,
                tbl_academic_years.year_name
            ')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_report_cards.enrollment_id')
            ->join('tbl_semesters', 'tbl_semesters.id = tbl_report_cards.semester_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_enrollments.academic_year_id')
            ->where('tbl_enrollments.student_id', $studentId)
            ->orderBy('tbl_semesters.start_date', 'DESC')
            ->findAll();
    }
}