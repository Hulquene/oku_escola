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

if (!function_exists('time_elapsed_string')) {
    function time_elapsed_string($datetime, $full = false) {
        if (empty($datetime)) return '';
        
        $now = new \DateTime();
        $ago = new \DateTime($datetime);
        $diff = $now->diff($ago);
        
        // Calcular semanas sem criar propriedade dinâmica
        $days = $diff->d;
        $weeks = floor($days / 7);
        $remainingDays = $days % 7;
        
        $string = [];
        
        if ($diff->y > 0) {
            $string[] = $diff->y . ' ano' . ($diff->y > 1 ? 's' : '');
        }
        
        if ($diff->m > 0) {
            $string[] = $diff->m . ' mês' . ($diff->m > 1 ? 'es' : '');
        }
        
        if ($weeks > 0) {
            $string[] = $weeks . ' semana' . ($weeks > 1 ? 's' : '');
        }
        
        if ($remainingDays > 0) {
            $string[] = $remainingDays . ' dia' . ($remainingDays > 1 ? 's' : '');
        }
        
        if ($diff->h > 0) {
            $string[] = $diff->h . ' hora' . ($diff->h > 1 ? 's' : '');
        }
        
        if ($diff->i > 0) {
            $string[] = $diff->i . ' minuto' . ($diff->i > 1 ? 's' : '');
        }
        
        if ($diff->s > 0) {
            $string[] = $diff->s . ' segundo' . ($diff->s > 1 ? 's' : '');
        }
        
        if (!$full) {
            $string = array_slice($string, 0, 1);
        }
        
        return $string ? 'há ' . implode(', ', $string) : 'agora mesmo';
    }
}