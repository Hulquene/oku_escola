<?php
// app/Helpers/number_helper.php

if (!function_exists('format_number')) {
    /**
     * Format a number with thousands separator
     */
    function format_number($number, $decimals = 0)
    {
        if ($number === null || $number === '') {
            return '0';
        }
        return number_format((float)$number, $decimals, ',', '.');
    }
}

if (!function_exists('format_decimal')) {
    /**
     * Format a decimal number (for grades, percentages, etc)
     */
    function format_decimal($number, $decimals = 1)
    {
        if ($number === null || $number === '') {
            return '0,0';
        }
        return number_format((float)$number, $decimals, ',', '.');
    }
}

if (!function_exists('format_percentage')) {
    /**
     * Format a percentage
     */
    function format_percentage($number, $decimals = 1)
    {
        if ($number === null || $number === '') {
            return '0%';
        }
        return number_format((float)$number, $decimals, ',', '.') . '%';
    }
}

if (!function_exists('format_money')) {
    /**
     * Format money in AOA (Kwanza)
     */
    function format_money($value)
    {
        if ($value === null || $value === '') {
            return '0,00 Kz';
        }
        return number_format((float)$value, 2, ',', '.') . ' Kz';
    }
}

if (!function_exists('format_money_short')) {
    /**
     * Format money in short format (Kz 1.500,00)
     */
    function format_money_short($value)
    {
        if ($value === null || $value === '') {
            return 'Kz 0,00';
        }
        return 'Kz ' . number_format((float)$value, 2, ',', '.');
    }
}

if (!function_exists('format_money_usd')) {
    /**
     * Format money in USD
     */
    function format_money_usd($value)
    {
        if ($value === null || $value === '') {
            return '$0.00';
        }
        return '$' . number_format((float)$value, 2, '.', ',');
    }
}

if (!function_exists('format_grade')) {
    /**
     * Format grade (0-20 scale)
     */
    function format_grade($grade, $decimals = 1)
    {
        if ($grade === null || $grade === '') {
            return '-';
        }
        return number_format((float)$grade, $decimals, ',', '.');
    }
}

if (!function_exists('format_average')) {
    /**
     * Format average with color indicator (for reports)
     */
    function format_average($average)
    {
        if ($average === null || $average === '') {
            return '<span class="text-muted">-</span>';
        }
        
        $formatted = number_format((float)$average, 1, ',', '.');
        
        if ($average >= 14) {
            return '<span class="text-success fw-bold">' . $formatted . '</span>';
        } elseif ($average >= 10) {
            return '<span class="text-primary fw-bold">' . $formatted . '</span>';
        } elseif ($average >= 8) {
            return '<span class="text-warning fw-bold">' . $formatted . '</span>';
        } else {
            return '<span class="text-danger fw-bold">' . $formatted . '</span>';
        }
    }
}

if (!function_exists('format_attendance')) {
    /**
     * Format attendance percentage
     */
    function format_attendance($attended, $total)
    {
        if ($total <= 0) {
            return '0%';
        }
        
        $percentage = ($attended / $total) * 100;
        return number_format($percentage, 1, ',', '.') . '%';
    }
}

if (!function_exists('format_attendance_with_color')) {
    /**
     * Format attendance with color indicator
     */
    function format_attendance_with_color($attended, $total)
    {
        if ($total <= 0) {
            return '<span class="text-muted">0%</span>';
        }
        
        $percentage = ($attended / $total) * 100;
        $formatted = number_format($percentage, 1, ',', '.') . '%';
        
        if ($percentage >= 90) {
            return '<span class="text-success fw-bold">' . $formatted . '</span>';
        } elseif ($percentage >= 75) {
            return '<span class="text-primary fw-bold">' . $formatted . '</span>';
        } elseif ($percentage >= 50) {
            return '<span class="text-warning fw-bold">' . $formatted . '</span>';
        } else {
            return '<span class="text-danger fw-bold">' . $formatted . '</span>';
        }
    }
}

if (!function_exists('format_student_count')) {
    /**
     * Format student count with capacity
     */
    function format_student_count($enrolled, $capacity)
    {
        $percentage = ($enrolled / $capacity) * 100;
        
        if ($percentage >= 100) {
            return '<span class="badge bg-danger">' . $enrolled . '/' . $capacity . ' (Cheia)</span>';
        } elseif ($percentage >= 80) {
            return '<span class="badge bg-warning text-dark">' . $enrolled . '/' . $capacity . '</span>';
        } else {
            return '<span class="badge bg-success">' . $enrolled . '/' . $capacity . '</span>';
        }
    }
}

if (!function_exists('format_file_size')) {
    /**
     * Format file size in human readable format
     */
    function format_file_size($bytes)
    {
        if ($bytes === null || $bytes === '') {
            return '0 B';
        }
        
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $i = 0;
        while ($bytes >= 1024 && $i < count($units) - 1) {
            $bytes /= 1024;
            $i++;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}

if (!function_exists('format_duration')) {
    /**
     * Format duration in minutes to hours and minutes
     */
    function format_duration($minutes)
    {
        if ($minutes === null || $minutes === '') {
            return '-';
        }
        
        $hours = floor($minutes / 60);
        $mins = $minutes % 60;
        
        if ($hours > 0) {
            return $hours . 'h ' . $mins . 'min';
        } else {
            return $mins . 'min';
        }
    }
}

if (!function_exists('calculate_average')) {
    /**
     * Calculate average from array of numbers
     */
    function calculate_average($numbers)
    {
        $filtered = array_filter($numbers, function($value) {
            return $value !== null && $value !== '';
        });
        
        if (empty($filtered)) {
            return null;
        }
        
        return array_sum($filtered) / count($filtered);
    }
}

if (!function_exists('round_grade')) {
    /**
     * Round grade according to school rules (half_up, half_down, ceil, floor)
     */
    function round_grade($grade, $method = 'half_up')
    {
        if ($grade === null || $grade === '') {
            return null;
        }
        
        switch ($method) {
            case 'half_up':
                return round($grade, 1);
            case 'half_down':
                $floor = floor($grade * 10) / 10;
                $diff = $grade - $floor;
                if ($diff == 0.05) {
                    return $floor;
                }
                return round($grade, 1);
            case 'ceil':
                return ceil($grade * 10) / 10;
            case 'floor':
                return floor($grade * 10) / 10;
            default:
                return round($grade, 1);
        }
    }
}

if (!function_exists('number_to_words')) {
    /**
     * Convert number to words in Portuguese
     * Simplified version for common numbers
     */
    function number_to_words($number)
    {
        $words = [
            0 => 'zero', 1 => 'um', 2 => 'dois', 3 => 'três', 4 => 'quatro',
            5 => 'cinco', 6 => 'seis', 7 => 'sete', 8 => 'oito', 9 => 'nove',
            10 => 'dez', 11 => 'onze', 12 => 'doze', 13 => 'treze', 14 => 'catorze',
            15 => 'quinze', 16 => 'dezasseis', 17 => 'dezassete', 18 => 'dezoito',
            19 => 'dezanove', 20 => 'vinte'
        ];
        
        if (isset($words[$number])) {
            return $words[$number];
        }
        
        return $number;
    }
}

if (!function_exists('get_grade_status')) {
    /**
     * Get grade status with label and color
     */
    function get_grade_status($grade)
    {
        if ($grade === null || $grade === '') {
            return ['label' => 'Pendente', 'color' => 'secondary'];
        }
        
        if ($grade >= 14) {
            return ['label' => 'Excelente', 'color' => 'success'];
        } elseif ($grade >= 10) {
            return ['label' => 'Suficiente', 'color' => 'primary'];
        } elseif ($grade >= 8) {
            return ['label' => 'Recurso', 'color' => 'warning'];
        } else {
            return ['label' => 'Insuficiente', 'color' => 'danger'];
        }
    }
}