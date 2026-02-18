<?php
// app/Helpers/student_helper.php

use App\Models\ContinuousAssessmentModel;
use App\Models\AttendanceModel;
use App\Models\ExamModel;
use App\Models\StudentFeeModel;
use App\Models\EnrollmentModel;
use App\Models\FinalGradeModel;

// Carrega o auth_helper automaticamente
helper('auth');

if (!function_exists('getStudentAverageGrade')) {
    /**
     * Retorna a média geral de notas do aluno
     * 
     * @param int|null $studentId ID do aluno na tabela tbl_students (se null, busca automaticamente)
     * @param int $precision Número de casas decimais
     * @return string
     */
    function getStudentAverageGrade($studentId = null, $precision = 1)
    {
        // Se não forneceu o studentId, tenta buscar do usuário logado
        if (!$studentId && isStudent()) {
            $studentId = getStudentIdFromUser();
        }
        
        if (!$studentId) {
            return '-';
        }
        
        // Buscar a matrícula atual do aluno
        $enrollmentModel = new EnrollmentModel();
        $enrollment = $enrollmentModel->getCurrentForStudent($studentId);
        
        if (!$enrollment) {
            return '-';
        }
        
        // Buscar o semestre ativo
        $semesterModel = new \App\Models\SemesterModel();
        $currentSemester = $semesterModel->where('is_active', 1)->first();
        
        if (!$currentSemester) {
            return '-';
        }
        
        // Buscar notas finais do aluno
        $finalGradeModel = new FinalGradeModel();
        $grades = $finalGradeModel
            ->select('final_score')
            ->where('enrollment_id', $enrollment->id)
            ->where('semester_id', $currentSemester->id)
            ->where('final_score >', 0)
            ->findAll();
        
        if (empty($grades)) {
            return '-';
        }
        
        $total = 0;
        $count = 0;
        
        foreach ($grades as $grade) {
            $total += $grade->final_score;
            $count++;
        }
        
        $average = $total / $count;
        
        return number_format($average, $precision, ',', '.');
    }
}

if (!function_exists('getStudentAttendancePercentage')) {
    /**
     * Retorna o percentual de presença do aluno baseado no enrollment_id
     * 
     * @param int|null $studentId ID do aluno na tabela tbl_students (se null, busca automaticamente)
     * @param int $precision Número de casas decimais
     * @return string
     */
    function getStudentAttendancePercentage($studentId = null, $precision = 1)
    {
        // Se não forneceu o studentId, tenta buscar do usuário logado
        if (!$studentId && isStudent()) {
            $studentId = getStudentIdFromUser();
        }
        
        if (!$studentId) {
            return '0%';
        }
        
        // Buscar a matrícula atual do aluno
        $enrollmentModel = new EnrollmentModel();
        $enrollment = $enrollmentModel->getCurrentForStudent($studentId);
        
        if (!$enrollment) {
            log_message('info', "ATTENDANCE: Nenhuma matrícula ativa encontrada para o aluno ID: {$studentId}");
            return '0%';
        }
        
        $attendanceModel = new AttendanceModel();
        
        // Buscar usando enrollment_id em vez de student_id
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
     * 
     * @param int|null $studentId ID do aluno
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
        
        // Buscar a matrícula atual do aluno
        $enrollmentModel = new EnrollmentModel();
        $enrollment = $enrollmentModel->getCurrentForStudent($studentId);
        
        if (!$enrollment) {
            return $disciplineId ? 0 : [];
        }
        
        $attendanceModel = new AttendanceModel();
        
        if ($disciplineId) {
            // Retornar percentual para uma disciplina específica
            $total = $attendanceModel
                ->where('enrollment_id', $enrollment->id)
                ->where('discipline_id', $disciplineId)
                ->countAllResults();
            
            if ($total == 0) {
                return 0;
            }
            
            $present = $attendanceModel
                ->where('enrollment_id', $enrollment->id)
                ->where('discipline_id', $disciplineId)
                ->whereIn('status', ['Presente', 'Atrasado', 'Falta Justificada'])
                ->countAllResults();
            
            return round(($present / $total) * 100, 1);
        }
        
        // Retornar array com percentuais por disciplina
        $disciplinas = getStudentDisciplines($studentId);
        $result = [];
        
        foreach ($disciplinas as $disciplina) {
            $total = $attendanceModel
                ->where('enrollment_id', $enrollment->id)
                ->where('discipline_id', $disciplina->id)
                ->countAllResults();
            
            if ($total > 0) {
                $present = $attendanceModel
                    ->where('enrollment_id', $enrollment->id)
                    ->where('discipline_id', $disciplina->id)
                    ->whereIn('status', ['Presente', 'Atrasado', 'Falta Justificada'])
                    ->countAllResults();
                
                $result[$disciplina->id] = [
                    'discipline_name' => $disciplina->discipline_name,
                    'percentage' => round(($present / $total) * 100, 1),
                    'total' => $total,
                    'present' => $present
                ];
            }
        }
        
        return $result;
    }
}

if (!function_exists('getNextStudentExam')) {
    /**
     * Retorna o próximo exame do aluno
     * 
     * @param int|null $studentId ID do aluno na tabela tbl_students (se null, busca automaticamente)
     * @param string $format Formato da data
     * @return string
     */
    function getNextStudentExam($studentId = null, $format = 'd/m')
    {
        // Se não forneceu o studentId, tenta buscar do usuário logado
        if (!$studentId && isStudent()) {
            $studentId = getStudentIdFromUser();
        }
        
        if (!$studentId) {
            return '-';
        }
        
        // Buscar a matrícula atual do aluno
        $enrollmentModel = new EnrollmentModel();
        $enrollment = $enrollmentModel->getCurrentForStudent($studentId);
        
        if (!$enrollment) {
            return '-';
        }
        
        $examModel = new ExamModel();
        
        // Buscar próximos exames da turma do aluno
        $nextExam = $examModel
            ->select('tbl_exams.*, tbl_disciplines.discipline_name')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exams.discipline_id')
            ->where('tbl_exams.class_id', $enrollment->class_id)
            ->where('tbl_exams.exam_date >=', date('Y-m-d'))
            ->orderBy('tbl_exams.exam_date', 'ASC')
            ->limit(1)
            ->first();
        
        return $nextExam ? date($format, strtotime($nextExam->exam_date)) . ' - ' . $nextExam->discipline_name : '-';
    }
}

if (!function_exists('getStudentPendingFeesCount')) {
    /**
     * Retorna o número de propinas pendentes do aluno
     * 
     * @param int|null $studentId ID do aluno na tabela tbl_students (se null, busca automaticamente)
     * @return string
     */
    function getStudentPendingFeesCount($studentId = null)
    {
        // Se não forneceu o studentId, tenta buscar do usuário logado
        if (!$studentId && isStudent()) {
            $studentId = getStudentIdFromUser();
        }
        
        if (!$studentId) {
            return '0 pendente(s)';
        }
        
        $feeModel = new StudentFeeModel();
        
        // Verificar a estrutura da tabela de propinas
        $db = db_connect();
        $fields = $db->getFieldNames('tbl_student_fees');
        
        $pendingCount = 0;
        
        if (in_array('student_id', $fields)) {
            $pendingCount = $feeModel
                ->where('student_id', $studentId)
                ->where('status', 'Pendente')
                ->countAllResults();
        }
        elseif (in_array('aluno_id', $fields)) {
            $pendingCount = $feeModel
                ->where('aluno_id', $studentId)
                ->where('status', 'Pendente')
                ->countAllResults();
        }
        
        return $pendingCount . ' ' . ($pendingCount == 1 ? 'pendente' : 'pendente(s)');
    }
}

if (!function_exists('getStudentAcademicSummary')) {
    /**
     * Retorna um array com o resumo acadêmico do aluno
     * 
     * @param int|null $studentId ID do aluno na tabela tbl_students (se null, busca automaticamente)
     * @return array
     */
    function getStudentAcademicSummary($studentId = null)
    {
        // Se não forneceu o studentId, tenta buscar do usuário logado
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

if (!function_exists('getStudentDisciplines')) {
    /**
     * Retorna as disciplinas do aluno
     * 
     * @param int|null $studentId ID do aluno na tabela tbl_students (se null, busca automaticamente)
     * @return array
     */
    function getStudentDisciplines($studentId = null)
    {
        // Se não forneceu o studentId, tenta buscar do usuário logado
        if (!$studentId && isStudent()) {
            $studentId = getStudentIdFromUser();
        }
        
        if (!$studentId) {
            return [];
        }
        
        $enrollmentModel = new EnrollmentModel();
        
        // Usar o método correto: getCurrentForStudent()
        $enrollment = $enrollmentModel->getCurrentForStudent($studentId);
        
        if (!$enrollment) {
            return [];
        }
        
        $classDisciplineModel = new \App\Models\ClassDisciplineModel();
        $disciplines = $classDisciplineModel
            ->select('
                tbl_disciplines.id,
                tbl_disciplines.discipline_name,
                tbl_disciplines.workload_hours,
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
        
        return $disciplines;
    }
}

if (!function_exists('formatStudentClassInfo')) {
    /**
     * Formata informações da turma do aluno
     * 
     * @param int|null $studentId ID do aluno na tabela tbl_students (se null, busca automaticamente)
     * @return string
     */
    function formatStudentClassInfo($studentId = null)
    {
        // Se não forneceu o studentId, tenta buscar do usuário logado
        if (!$studentId && isStudent()) {
            $studentId = getStudentIdFromUser();
        }
        
        if (!$studentId) {
            return '';
        }
        
        $enrollmentModel = new EnrollmentModel();
        
        $enrollment = $enrollmentModel->getCurrentForStudent($studentId);
        
        if (!$enrollment) {
            return '';
        }
        
        $shiftLabels = [
            'morning' => 'Manhã',
            'afternoon' => 'Tarde',
            'evening' => 'Noite',
            'full' => 'Integral'
        ];
        
        $shift = isset($enrollment->class_shift) ? ($shiftLabels[$enrollment->class_shift] ?? $enrollment->class_shift) : '';
        
        return trim($enrollment->class_name . ' - ' . $shift, ' -');
    }
}

if (!function_exists('getStudentGradesBySubject')) {
    /**
     * Retorna as notas do aluno por disciplina
     * 
     * @param int|null $studentId ID do aluno na tabela tbl_students (se null, busca automaticamente)
     * @return array
     */
    function getStudentGradesBySubject($studentId = null)
    {
        // Se não forneceu o studentId, tenta buscar do usuário logado
        if (!$studentId && isStudent()) {
            $studentId = getStudentIdFromUser();
        }
        
        if (!$studentId) {
            return [];
        }
        
        $enrollmentModel = new EnrollmentModel();
        $enrollment = $enrollmentModel->getCurrentForStudent($studentId);
        
        if (!$enrollment) {
            return [];
        }
        
        $finalGradeModel = new FinalGradeModel();
        
        return $finalGradeModel
            ->select('
                tbl_final_grades.*,
                tbl_disciplines.discipline_name,
                tbl_disciplines.workload_hours
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_final_grades.discipline_id')
            ->where('enrollment_id', $enrollment->id)
            ->orderBy('tbl_disciplines.discipline_name', 'ASC')
            ->findAll();
    }
}

if (!function_exists('getStudentEnrollmentId')) {
    /**
     * Retorna o ID da matrícula atual do aluno
     * 
     * @param int|null $studentId ID do aluno
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
        
        $enrollmentModel = new EnrollmentModel();
        $enrollment = $enrollmentModel->getCurrentForStudent($studentId);
        
        return $enrollment ? $enrollment->id : null;
    }
}