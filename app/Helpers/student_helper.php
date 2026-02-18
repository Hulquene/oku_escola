<?php

use App\Models\GradeModel;
use App\Models\AttendanceModel;
use App\Models\ExamModel;
use App\Models\FeeModel;
use App\Models\EnrollmentModel;

if (!function_exists('getStudentAverageGrade')) {
    /**
     * Retorna a média geral de notas do aluno
     * 
     * @param int|null $studentId ID do aluno (se null, usa da sessão)
     * @param int $precision Número de casas decimais
     * @return string
     */
    function getStudentAverageGrade($studentId = null, $precision = 1)
    {
        $studentId = $studentId ?? session()->get('user_id');
        if (!$studentId) {
            return '-';
        }
        
        $gradeModel = new GradeModel();
        $grades = $gradeModel->getStudentGrades($studentId);
        
        $average = 0;
        $count = 0;
        
        foreach ($grades as $grade) {
            if (isset($grade->final_grade) && $grade->final_grade > 0) {
                $average += $grade->final_grade;
                $count++;
            }
        }
        
        return $count > 0 ? number_format($average / $count, $precision) : '-';
    }
}

if (!function_exists('getStudentAttendancePercentage')) {
    /**
     * Retorna o percentual de presença do aluno
     * 
     * @param int|null $studentId ID do aluno (se null, usa da sessão)
     * @param int $precision Número de casas decimais
     * @return string
     */
    function getStudentAttendancePercentage($studentId = null, $precision = 1)
    {
        $studentId = $studentId ?? session()->get('user_id');
        if (!$studentId) {
            return '0%';
        }
        
        $attendanceModel = new AttendanceModel();
        $percentage = $attendanceModel->getStudentAttendancePercentage($studentId);
        
        return number_format($percentage, $precision) . '%';
    }
}

if (!function_exists('getNextStudentExam')) {
    /**
     * Retorna o próximo exame do aluno
     * 
     * @param int|null $studentId ID do aluno (se null, usa da sessão)
     * @param string $format Formato da data
     * @return string
     */
    function getNextStudentExam($studentId = null, $format = 'd/m')
    {
        $studentId = $studentId ?? session()->get('user_id');
        if (!$studentId) {
            return '-';
        }
        
        $examModel = new ExamModel();
        $nextExam = $examModel->getNextStudentExam($studentId);
        
        return $nextExam ? date($format, strtotime($nextExam->exam_date)) : '-';
    }
}

if (!function_exists('getStudentPendingFeesCount')) {
    /**
     * Retorna o número de propinas pendentes do aluno
     * 
     * @param int|null $studentId ID do aluno (se null, usa da sessão)
     * @return string
     */
    function getStudentPendingFeesCount($studentId = null)
    {
        $studentId = $studentId ?? session()->get('user_id');
        if (!$studentId) {
            return '0 pendente(s)';
        }
        
        $feeModel = new FeeModel();
        $pendingFees = $feeModel->getStudentPendingFees($studentId);
        $count = is_array($pendingFees) ? count($pendingFees) : 0;
        
        return $count . ' ' . ($count == 1 ? 'pendente' : 'pendente(s)');
    }
}

if (!function_exists('getStudentAcademicSummary')) {
    /**
     * Retorna um array com o resumo acadêmico do aluno
     * 
     * @param int|null $studentId ID do aluno (se null, usa da sessão)
     * @return array
     */
    function getStudentAcademicSummary($studentId = null)
    {
        $studentId = $studentId ?? session()->get('user_id');
        
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
     * @param int|null $studentId ID do aluno (se null, usa da sessão)
     * @return array
     */
    function getStudentDisciplines($studentId = null)
    {
        $studentId = $studentId ?? session()->get('user_id');
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
     * @param int|null $studentId ID do aluno (se null, usa da sessão)
     * @return string
     */
    function formatStudentClassInfo($studentId = null)
    {
        $studentId = $studentId ?? session()->get('user_id');
        if (!$studentId) {
            return '';
        }
        
        $enrollmentModel = new EnrollmentModel();
        
        // CORREÇÃO: Usar getCurrentForStudent() em vez de getCurrentEnrollment()
        $enrollment = $enrollmentModel->getCurrentForStudent($studentId);
        
        if (!$enrollment) {
            return '';
        }
        
        return $enrollment->class_name . ' - ' . ($enrollment->class_shift ?? '');
    }
}