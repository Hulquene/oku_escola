<?php

if (!function_exists('map_semester_to_period_type')) {
    /**
     * Mapeia o tipo de semestre para o tipo de período usado em class_disciplines
     * 
     * @param string $semesterType Tipo do semestre (1º Trimestre, 2º Trimestre, etc)
     * @return string
     */
    function map_semester_to_period_type($semesterType)
    {
        $map = [
            '1º Trimestre' => '1º Semestre',
            '2º Trimestre' => '2º Semestre',
            '3º Trimestre' => 'Anual',
            '1º Semestre' => '1º Semestre',
            '2º Semestre' => '2º Semestre'
        ];
        
        return $map[$semesterType] ?? 'Anual';
    }
}

if (!function_exists('map_course_semester_to_period_type')) {
    /**
     * Mapeia o valor do semestre do curso para o tipo de período
     * 
     * @param string|int $courseSemester Valor do semester em course_disciplines (1, 2, Anual)
     * @return string
     */
    function map_course_semester_to_period_type($courseSemester)
    {
        $map = [
            '1' => '1º Semestre',
            '2' => '2º Semestre',
            'Anual' => 'Anual',
            1 => '1º Semestre',
            2 => '2º Semestre'
        ];
        
        return $map[$courseSemester] ?? 'Anual';
    }
}

if (!function_exists('get_period_info')) {
    /**
     * Retorna informações formatadas sobre um período
     * 
     * @param string $periodType
     * @return array
     */
    function get_period_info($periodType)
    {
        $info = [
            'Anual' => [
                'label' => 'Anual',
                'badge' => 'success',
                'icon' => 'fa-calendar-alt',
                'description' => 'Disciplina anual (todos os trimestres)',
                'color' => 'success',
                'trimesters' => [1, 2, 3]
            ],
            '1º Semestre' => [
                'label' => '1º Semestre',
                'badge' => 'primary',
                'icon' => 'fa-sun',
                'description' => 'Apenas 1º Trimestre',
                'color' => 'primary',
                'trimesters' => [1]
            ],
            '2º Semestre' => [
                'label' => '2º Semestre',
                'badge' => 'warning',
                'icon' => 'fa-cloud-sun',
                'description' => 'Apenas 2º Trimestre',
                'color' => 'warning',
                'trimesters' => [2]
            ]
        ];
        
        return $info[$periodType] ?? $info['Anual'];
    }
}

use App\Models\EnrollmentModel;

if (!function_exists('count_pending_approvals')) {
    /**
     * Contar alunos aprovados aguardando aprovação final
     */
    function count_pending_approvals()
    {
        $enrollmentModel = new EnrollmentModel();
        
        return $enrollmentModel
            ->where('status', 'Ativo')
            ->where('final_result', 'Aprovado')
            ->where('is_approved', 0)
            ->countAllResults();
    }
}

if (!function_exists('count_pending_promotions')) {
    /**
     * Contar alunos prontos para progressão
     */
    function count_pending_promotions()
    {
        $enrollmentModel = new EnrollmentModel();
        
        return $enrollmentModel
            ->where('status', 'Ativo')
            ->where('final_result', 'Aprovado')
            ->where('is_approved', 1)
            ->where('promotion_status', 'pending')
            ->countAllResults();
    }
}

if (!function_exists('count_pending_enrollments')) {
    /**
     * Contar matrículas pendentes
     */
    function count_pending_enrollments()
    {
        $enrollmentModel = new EnrollmentModel();
        
        return $enrollmentModel
            ->where('status', 'Pendente')
            ->countAllResults();
    }
}

if (!function_exists('count_students_by_status')) {
    /**
     * Contar alunos por status
     */
    function count_students_by_status($status = 'Ativo')
    {
        $enrollmentModel = new EnrollmentModel();
        
        return $enrollmentModel
            ->where('status', $status)
            ->countAllResults();
    }
}

if (!function_exists('count_exams_pending')) {
    /**
     * Contar exames pendentes
     */
    function count_exams_pending()
    {
        $db = db_connect();
        
        $result = $db->query("
            SELECT COUNT(*) as total 
            FROM tbl_exam_schedules 
            WHERE status = 'Agendado' 
            AND exam_date >= CURDATE()
        ")->getRow();
        
        return $result->total ?? 0;
    }
}