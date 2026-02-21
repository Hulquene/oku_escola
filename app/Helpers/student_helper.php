<?php
// app/Helpers/student_helper.php

use App\Models\EnrollmentModel;
use App\Models\ExamScheduleModel;
use App\Models\ExamResultModel;
use App\Models\AttendanceModel;
use App\Models\DisciplineAverageModel;
use App\Models\StudentFeeModel;

// Carrega o auth_helper automaticamente
helper('auth');

/**
 * ============================================
 * FUNÇÕES PRINCIPAIS (NOVO MODELO)
 * ============================================
 */

if (!function_exists('getStudentCurrentEnrollment')) {
    /**
     * Busca matrícula atual do aluno
     * @param int $studentId ID do aluno
     * @return object|null
     */
    function getStudentCurrentEnrollment($studentId) {
        $enrollmentModel = new EnrollmentModel();
        return $enrollmentModel
            ->select('
                tbl_enrollments.*,
                tbl_classes.class_name,
                tbl_classes.class_shift,
                tbl_classes.grade_level_id,
                tbl_grade_levels.level_name,
                tbl_academic_years.year_name
            ')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id', 'left')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id', 'left')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_enrollments.academic_year_id', 'left')
            ->where('tbl_enrollments.student_id', $studentId)
            ->whereIn('tbl_enrollments.status', ['Ativo', 'Pendente'])
            ->orderBy('tbl_enrollments.created_at', 'DESC')
            ->first();
    }
}

if (!function_exists('getStudentExams')) {
    /**
     * Busca exames do aluno (próximos)
     * @param int $enrollmentId ID da matrícula
     * @param int|null $limit Limite de resultados
     * @return array
     */
    function getStudentExams($enrollmentId, $limit = null) {
        $examScheduleModel = new ExamScheduleModel();
        
        // Primeiro buscar a matrícula para pegar a class_id
        $enrollmentModel = new EnrollmentModel();
        $enrollment = $enrollmentModel->find($enrollmentId);
        
        if (!$enrollment || !$enrollment->class_id) {
            return [];
        }
        
        $builder = $examScheduleModel
            ->select('
                tbl_exam_schedules.*,
                tbl_disciplines.discipline_name,
                tbl_disciplines.discipline_code,
                tbl_exam_boards.board_name,
                tbl_exam_boards.board_type,
                tbl_exam_boards.weight,
                tbl_exam_periods.period_name,
                DATE_FORMAT(tbl_exam_schedules.exam_date, "%d/%m/%Y") as formatted_date,
                DATE_FORMAT(tbl_exam_schedules.exam_time, "%H:%i") as formatted_time
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id')
            ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exam_schedules.exam_board_id')
            ->join('tbl_exam_periods', 'tbl_exam_periods.id = tbl_exam_schedules.exam_period_id')
            ->where('tbl_exam_schedules.class_id', $enrollment->class_id)
            ->where('tbl_exam_schedules.exam_date >=', date('Y-m-d'));
        
        if ($limit) {
            $builder->limit($limit);
        }
        
        return $builder->orderBy('tbl_exam_schedules.exam_date', 'ASC')
            ->orderBy('tbl_exam_schedules.exam_time', 'ASC')
            ->findAll();
    }
}

if (!function_exists('getStudentGrades')) {
    /**
     * Busca notas do aluno
     * @param int $enrollmentId ID da matrícula
     * @param int|null $semesterId ID do semestre (opcional)
     * @return array
     */
    function getStudentGrades($enrollmentId, $semesterId = null) {
        $examResultModel = new ExamResultModel();
        
        $builder = $examResultModel
            ->select('
                tbl_exam_results.*,
                tbl_exam_schedules.discipline_id,
                tbl_exam_schedules.exam_date,
                tbl_exam_schedules.max_score,
                tbl_exam_schedules.approval_score,
                tbl_disciplines.discipline_name,
                tbl_disciplines.discipline_code,
                tbl_exam_boards.board_name,
                tbl_exam_boards.board_type,
                tbl_exam_boards.weight,
                tbl_exam_periods.period_name,
                tbl_exam_periods.semester_id,
                DATE_FORMAT(tbl_exam_schedules.exam_date, "%d/%m/%Y") as formatted_date
            ')
            ->join('tbl_exam_schedules', 'tbl_exam_schedules.id = tbl_exam_results.exam_schedule_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id')
            ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exam_schedules.exam_board_id')
            ->join('tbl_exam_periods', 'tbl_exam_periods.id = tbl_exam_schedules.exam_period_id')
            ->where('tbl_exam_results.enrollment_id', $enrollmentId);
        
        if ($semesterId) {
            $builder->where('tbl_exam_periods.semester_id', $semesterId);
        }
        
        return $builder->orderBy('tbl_exam_schedules.exam_date', 'DESC')
            ->findAll();
    }
}

if (!function_exists('getStudentGradesByDiscipline')) {
    /**
     * Agrupa notas por disciplina
     * @param int $enrollmentId ID da matrícula
     * @param int|null $semesterId ID do semestre (opcional)
     * @return array
     */
    function getStudentGradesByDiscipline($enrollmentId, $semesterId = null) {
        $grades = getStudentGrades($enrollmentId, $semesterId);
        
        $disciplines = [];
        foreach ($grades as $grade) {
            $disciplineId = $grade->discipline_id;
            
            if (!isset($disciplines[$disciplineId])) {
                $disciplines[$disciplineId] = [
                    'discipline_id' => $disciplineId,
                    'discipline_name' => $grade->discipline_name,
                    'discipline_code' => $grade->discipline_code,
                    'grades' => [],
                    'average' => 0,
                    'total' => 0,
                    'approved' => 0,
                    'failed' => 0
                ];
            }
            
            $disciplines[$disciplineId]['grades'][] = $grade;
        }
        
        // Calcular estatísticas por disciplina
        foreach ($disciplines as &$discipline) {
            $total = 0;
            $count = 0;
            $approved = 0;
            
            foreach ($discipline['grades'] as $grade) {
                $total += $grade->score;
                $count++;
                if ($grade->score >= ($grade->approval_score ?? 10)) {
                    $approved++;
                }
            }
            
            $discipline['average'] = $count > 0 ? round($total / $count, 1) : 0;
            $discipline['total'] = $count;
            $discipline['approved'] = $approved;
            $discipline['failed'] = $count - $approved;
            $discipline['approval_rate'] = $count > 0 ? round(($approved / $count) * 100, 1) : 0;
        }
        
        return $disciplines;
    }
}

if (!function_exists('getStudentAttendanceStats')) {
    /**
     * Estatísticas de presença por disciplina
     * @param int $enrollmentId ID da matrícula
     * @param int|null $semesterId ID do semestre (opcional)
     * @return array
     */
    function getStudentAttendanceStats($enrollmentId, $semesterId = null) {
        $attendanceModel = new AttendanceModel();
        
        $builder = $attendanceModel
            ->select('
                tbl_attendance.discipline_id,
                tbl_disciplines.discipline_name,
                COUNT(*) as total,
                SUM(CASE WHEN tbl_attendance.status IN ("Presente", "Atrasado", "Falta Justificada") THEN 1 ELSE 0 END) as present
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_attendance.discipline_id', 'left')
            ->where('tbl_attendance.enrollment_id', $enrollmentId);
        
        if ($semesterId) {
            $builder->where('tbl_attendance.semester_id', $semesterId);
        }
        
        $results = $builder->groupBy('tbl_attendance.discipline_id')
            ->findAll();
        
        // Calcular percentuais
        foreach ($results as &$result) {
            $result->percentage = $result->total > 0 
                ? round(($result->present / $result->total) * 100, 1) 
                : 0;
            $result->absent = $result->total - $result->present;
        }
        
        return $results;
    }
}

if (!function_exists('getStudentDisciplines')) {
    /**
     * Retorna as disciplinas do aluno com informações completas
     * @param int|null $studentId ID do aluno (opcional)
     * @return array
     */
    function getStudentDisciplines($studentId = null) {
        if (!$studentId && isStudent()) {
            $studentId = getStudentIdFromUser();
        }
        
        if (!$studentId) {
            return [];
        }
        
        $enrollment = getStudentCurrentEnrollment($studentId);
        
        if (!$enrollment || !$enrollment->class_id) {
            return [];
        }
        
        $classDisciplineModel = new \App\Models\ClassDisciplineModel();
        
        $disciplines = $classDisciplineModel
            ->select('
                tbl_disciplines.id,
                tbl_disciplines.discipline_name,
                tbl_disciplines.discipline_code,
                tbl_disciplines.workload_hours,
                tbl_disciplines.approval_grade,
                tbl_class_disciplines.teacher_id,
                tbl_users.first_name as teacher_first_name,
                tbl_users.last_name as teacher_last_name,
                CONCAT(tbl_users.first_name, " ", tbl_users.last_name) as teacher_name
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
            ->join('tbl_users', 'tbl_users.id = tbl_class_disciplines.teacher_id', 'left')
            ->where('tbl_class_disciplines.class_id', $enrollment->class_id)
            ->where('tbl_class_disciplines.is_active', 1)
            ->orderBy('tbl_disciplines.discipline_name', 'ASC')
            ->findAll();
        
        // Adicionar estatísticas para cada disciplina
        foreach ($disciplines as $discipline) {
            // Notas da disciplina
            $grades = getStudentGrades($enrollment->id, null);
            $disciplineGrades = array_filter($grades, function($g) use ($discipline) {
                return $g->discipline_id == $discipline->id;
            });
            
            if (!empty($disciplineGrades)) {
                $total = 0;
                foreach ($disciplineGrades as $g) {
                    $total += $g->score;
                }
                $discipline->average = round($total / count($disciplineGrades), 1);
                $discipline->total_grades = count($disciplineGrades);
            } else {
                $discipline->average = null;
                $discipline->total_grades = 0;
            }
            
            // Presenças da disciplina
            $attendance = getStudentAttendanceStats($enrollment->id);
            $disciplineAttendance = array_filter($attendance, function($a) use ($discipline) {
                return $a->discipline_id == $discipline->id;
            });
            
            if (!empty($disciplineAttendance)) {
                $att = reset($disciplineAttendance);
                $discipline->attendance_percentage = $att->percentage;
                $discipline->attendance_total = $att->total;
                $discipline->attendance_present = $att->present;
            } else {
                $discipline->attendance_percentage = 0;
                $discipline->attendance_total = 0;
                $discipline->attendance_present = 0;
            }
            
            // Média final da disciplina
            $averageModel = new DisciplineAverageModel();
            $finalAverage = $averageModel
                ->where('enrollment_id', $enrollment->id)
                ->where('discipline_id', $discipline->id)
                ->orderBy('semester_id', 'DESC')
                ->first();
            
            $discipline->final_grade = $finalAverage ? $finalAverage->final_score : null;
        }
        
        return $disciplines;
    }
}

/**
 * ============================================
 * FUNÇÕES DE MÉTRICAS E ESTATÍSTICAS
 * ============================================
 */

if (!function_exists('getStudentAverageGrade')) {
    /**
     * Retorna a média geral de notas do aluno
     * @param int|null $studentId ID do aluno (opcional)
     * @param int $precision Casas decimais
     * @return string
     */
    function getStudentAverageGrade($studentId = null, $precision = 1)
    {
        if (!$studentId && isStudent()) {
            $studentId = getStudentIdFromUser();
        }
        
        if (!$studentId) {
            return '-';
        }
        
        $enrollment = getStudentCurrentEnrollment($studentId);
        
        if (!$enrollment) {
            return '-';
        }
        
        $grades = getStudentGrades($enrollment->id);
        
        if (empty($grades)) {
            return '-';
        }
        
        $total = 0;
        $count = 0;
        
        foreach ($grades as $grade) {
            $total += $grade->score;
            $count++;
        }
        
        $average = $total / $count;
        
        return number_format($average, $precision, ',', '.');
    }
}

if (!function_exists('getStudentAttendancePercentage')) {
    /**
     * Retorna o percentual geral de presença do aluno
     * @param int|null $studentId ID do aluno (opcional)
     * @param int $precision Casas decimais
     * @return string
     */
    function getStudentAttendancePercentage($studentId = null, $precision = 1)
    {
        if (!$studentId && isStudent()) {
            $studentId = getStudentIdFromUser();
        }
        
        if (!$studentId) {
            return '0%';
        }
        
        $enrollment = getStudentCurrentEnrollment($studentId);
        
        if (!$enrollment) {
            return '0%';
        }
        
        $attendanceModel = new AttendanceModel();
        
        $total = $attendanceModel
            ->where('enrollment_id', $enrollment->id)
            ->countAllResults();
        
        if ($total == 0) {
            return '0%';
        }
        
        $present = $attendanceModel
            ->where('enrollment_id', $enrollment->id)
            ->whereIn('status', ['Presente', 'Atrasado', 'Falta Justificada'])
            ->countAllResults();
        
        $percentage = ($present / $total) * 100;
        
        return number_format($percentage, $precision, ',', '.') . '%';
    }
}

if (!function_exists('getStudentAttendanceBySubject')) {
    /**
     * Retorna o percentual de presença por disciplina
     * @param int|null $studentId ID do aluno (opcional)
     * @param int|null $disciplineId ID da disciplina (opcional)
     * @return array|float
     */
    function getStudentAttendanceBySubject($studentId = null, $disciplineId = null)
    {
        if (!$studentId && isStudent()) {
            $studentId = getStudentIdFromUser();
        }
        
        if (!$studentId) {
            return $disciplineId ? 0 : [];
        }
        
        $enrollment = getStudentCurrentEnrollment($studentId);
        
        if (!$enrollment) {
            return $disciplineId ? 0 : [];
        }
        
        $attendanceStats = getStudentAttendanceStats($enrollment->id);
        
        if ($disciplineId) {
            foreach ($attendanceStats as $stat) {
                if ($stat->discipline_id == $disciplineId) {
                    return $stat->percentage;
                }
            }
            return 0;
        }
        
        $result = [];
        foreach ($attendanceStats as $stat) {
            $result[$stat->discipline_id] = [
                'discipline_name' => $stat->discipline_name,
                'percentage' => $stat->percentage,
                'total' => $stat->total,
                'present' => $stat->present
            ];
        }
        
        return $result;
    }
}

if (!function_exists('getNextStudentExam')) {
    /**
     * Retorna o próximo exame do aluno
     * @param int|null $studentId ID do aluno (opcional)
     * @param string $format Formato da data
     * @return string
     */
    function getNextStudentExam($studentId = null, $format = 'd/m')
    {
        if (!$studentId && isStudent()) {
            $studentId = getStudentIdFromUser();
        }
        
        if (!$studentId) {
            return '-';
        }
        
        $enrollment = getStudentCurrentEnrollment($studentId);
        
        if (!$enrollment) {
            return '-';
        }
        
        $exams = getStudentExams($enrollment->id, 1);
        
        if (empty($exams)) {
            return '-';
        }
        
        $nextExam = $exams[0];
        
        return date($format, strtotime($nextExam->exam_date)) . ' - ' . $nextExam->discipline_name;
    }
}

if (!function_exists('getStudentPendingFeesCount')) {
    /**
     * Retorna o número de propinas pendentes do aluno
     * @param int|null $studentId ID do aluno (opcional)
     * @return string
     */
    function getStudentPendingFeesCount($studentId = null)
    {
        if (!$studentId && isStudent()) {
            $studentId = getStudentIdFromUser();
        }
        
        if (!$studentId) {
            return '0 pendente(s)';
        }
        
        $enrollment = getStudentCurrentEnrollment($studentId);
        
        if (!$enrollment) {
            return '0 pendente(s)';
        }
        
        $feeModel = new StudentFeeModel();
        
        $pendingCount = $feeModel
            ->where('enrollment_id', $enrollment->id)
            ->whereIn('status', ['Pendente', 'Vencido'])
            ->countAllResults();
        
        return $pendingCount . ' ' . ($pendingCount == 1 ? 'pendente' : 'pendente(s)');
    }
}

if (!function_exists('getStudentAcademicSummary')) {
    /**
     * Retorna um array com o resumo acadêmico do aluno
     * @param int|null $studentId ID do aluno (opcional)
     * @return array
     */
    function getStudentAcademicSummary($studentId = null)
    {
        if (!$studentId && isStudent()) {
            $studentId = getStudentIdFromUser();
        }
        
        return [
            'average_grade' => getStudentAverageGrade($studentId),
            'attendance' => getStudentAttendancePercentage($studentId),
            'next_exam' => getNextStudentExam($studentId),
            'pending_fees' => getStudentPendingFeesCount($studentId)
        ];
    }
}

/**
 * ============================================
 * FUNÇÕES DE FORMATAÇÃO E UTILIDADES
 * ============================================
 */

if (!function_exists('formatStudentClassInfo')) {
    /**
     * Formata informações da turma do aluno
     * @param int|null $studentId ID do aluno (opcional)
     * @return string
     */
    function formatStudentClassInfo($studentId = null)
    {
        if (!$studentId && isStudent()) {
            $studentId = getStudentIdFromUser();
        }
        
        if (!$studentId) {
            return '';
        }
        
        $enrollment = getStudentCurrentEnrollment($studentId);
        
        if (!$enrollment) {
            return '';
        }
        
        $shiftLabels = [
            'Manhã' => 'Manhã',
            'Tarde' => 'Tarde',
            'Noite' => 'Noite',
            'Integral' => 'Integral'
        ];
        
        $shift = $shiftLabels[$enrollment->class_shift] ?? $enrollment->class_shift ?? '';
        
        $parts = [];
        if ($enrollment->class_name) $parts[] = $enrollment->class_name;
        if ($shift) $parts[] = $shift;
        if ($enrollment->level_name) $parts[] = '(' . $enrollment->level_name . ')';
        
        return implode(' - ', $parts);
    }
}

if (!function_exists('getStudentEnrollmentId')) {
    /**
     * Retorna o ID da matrícula atual do aluno
     * @param int|null $studentId ID do aluno (opcional)
     * @return int|null
     */
    function getStudentEnrollmentId($studentId = null)
    {
        if (!$studentId && isStudent()) {
            $studentId = getStudentIdFromUser();
        }
        
        if (!$studentId) {
            return null;
        }
        
        $enrollment = getStudentCurrentEnrollment($studentId);
        
        return $enrollment ? $enrollment->id : null;
    }
}

if (!function_exists('getStudentIdFromEnrollment')) {
    /**
     * Retorna o ID do aluno a partir do ID da matrícula
     * @param int $enrollmentId ID da matrícula
     * @return int|null
     */
    function getStudentIdFromEnrollment($enrollmentId)
    {
        $enrollmentModel = new EnrollmentModel();
        $enrollment = $enrollmentModel->find($enrollmentId);
        
        return $enrollment ? $enrollment->student_id : null;
    }
}

if (!function_exists('getStudentGradesBySubject')) {
    /**
     * Retorna as notas do aluno por disciplina (formato antigo, para compatibilidade)
     * @param int|null $studentId ID do aluno (opcional)
     * @return array
     */
    function getStudentGradesBySubject($studentId = null)
    {
        if (!$studentId && isStudent()) {
            $studentId = getStudentIdFromUser();
        }
        
        if (!$studentId) {
            return [];
        }
        
        $enrollment = getStudentCurrentEnrollment($studentId);
        
        if (!$enrollment) {
            return [];
        }
        
        $grades = getStudentGrades($enrollment->id);
        
        // Agrupar por disciplina no formato antigo
        $result = [];
        foreach ($grades as $grade) {
            $result[] = (object)[
                'discipline_name' => $grade->discipline_name,
                'final_score' => $grade->score,
                'status' => $grade->score >= ($grade->approval_score ?? 10) ? 'Aprovado' : 'Em curso',
                'exam_date' => $grade->exam_date
            ];
        }
        
        return $result;
    }
}