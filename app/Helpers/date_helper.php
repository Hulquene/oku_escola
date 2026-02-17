<?php

if (!function_exists('getDayOfWeek')) {
    /**
     * Retorna o dia da semana em português
     * 
     * @param string $date Data no formato Y-m-d
     * @return string Nome do dia da semana
     */
    function getDayOfWeek($date) {
        $days = [
            'Sunday' => 'Domingo',
            'Monday' => 'Segunda-feira',
            'Tuesday' => 'Terça-feira',
            'Wednesday' => 'Quarta-feira',
            'Thursday' => 'Quinta-feira',
            'Friday' => 'Sexta-feira',
            'Saturday' => 'Sábado'
        ];
        
        $dayName = date('l', strtotime($date));
        return $days[$dayName] ?? $dayName;
    }
}

if (!function_exists('formatDatePt')) {
    /**
     * Formata data para o padrão português
     */
    function formatDatePt($date, $format = 'd/m/Y') {
        return date($format, strtotime($date));
    }
}

if (!function_exists('getMonthName')) {
    /**
     * Retorna o nome do mês em português
     */
    function getMonthName($month) {
        $months = [
            1 => 'Janeiro',
            2 => 'Fevereiro',
            3 => 'Março',
            4 => 'Abril',
            5 => 'Maio',
            6 => 'Junho',
            7 => 'Julho',
            8 => 'Agosto',
            9 => 'Setembro',
            10 => 'Outubro',
            11 => 'Novembro',
            12 => 'Dezembro'
        ];
        return $months[(int)$month] ?? $month;
    }
}