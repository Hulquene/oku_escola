<?php
// app/Helpers/settings_helper.php

use App\Models\SettingsModel;
use App\Models\AcademicYearModel;
use App\Models\CurrencyModel;

if (!function_exists('setting')) {
    /**
     * Get a setting value
     * 
     * @param string $key Setting key
     * @param mixed $default Default value if setting not found
     * @return mixed
     */
    function setting($key, $default = null)
    {
        $settingsModel = new SettingsModel();
        return $settingsModel->get($key, $default);
    }
}

if (!function_exists('set_setting')) {
    /**
     * Save a setting
     * 
     * @param string $key Setting key
     * @param mixed $value Setting value
     * @return bool
     */
    function set_setting($key, $value)
    {
        $settingsModel = new SettingsModel();
        return $settingsModel->saveSetting($key, $value);
    }
}

if (!function_exists('school_name')) {
    /**
     * Get school name
     */
    function school_name()
    {
        return setting('school_name', 'Minha Escola');
    }
}

if (!function_exists('school_logo')) {
    /**
     * Get school logo URL
     */
    function school_logo()
    {
        $logo = setting('school_logo');
        if ($logo) {
            return base_url('uploads/school/' . $logo);
        }
        return base_url('assets/images/default-logo.png');
    }
}

if (!function_exists('school_logo_url')) {
    /**
     * Get school logo URL with cache
     */
    function school_logo_url()
    {
        $cache = service('cache');
        $logo = $cache->get('school_logo');
        
        if ($logo === null) {
            $logo = setting('school_logo');
            $cache->save('school_logo', $logo, 3600); // cache de 1 hora
        }
        
        if ($logo && file_exists('uploads/school/' . $logo)) {
            return base_url('uploads/school/' . $logo);
        }
        return base_url('assets/images/default-logo.png');
    }
}

if (!function_exists('school_logo_html')) {
    /**
     * Get school logo HTML with specified dimensions
     */
    function school_logo_html($width = 40, $height = 40, $class = '')
    {
        $logoUrl = school_logo_url();
        $acronym = setting('school_acronym') ?: 'Escola';
        
        if ($logoUrl && $logoUrl !== base_url('assets/images/default-logo.png')) {
            return '<img src="' . $logoUrl . '" alt="' . $acronym . '" width="' . $width . '" height="' . $height . '" class="' . $class . '" style="object-fit: contain;">';
        }
        
        return '<div class="school-logo-placeholder ' . $class . '" style="width: ' . $width . 'px; height: ' . $height . 'px; display:flex; align-items:center; justify-content:center; background:#f0f2f5; border-radius:8px;"><i class="fas fa-graduation-cap" style="font-size:1.2rem; color:#6c757d;"></i></div>';
    }
}

            
if (!function_exists('set_current_academic_year')) {
    /**
     * Set current academic year ID in session and settings
     */
    function set_current_academic_year($id)
    {
        // Buscar o nome do ano letivo para guardar na sessão
         $academicYearModel = new AcademicYearModel();
            $academicYear = $academicYearModel->find($id);
          // Guardar na sessão
            session()->set([
                'academic_year_id' => $id,
                'academic_year_name' => $academicYear ? $academicYear->year_name : null,
            ]);
        return set_setting('current_academic_year', $id);
    }
}

if (!function_exists('current_academic_year')) {
    /**
     * Get current academic year ID from session or settings
     */
    function current_academic_year()
    {
        return session()->get('academic_year_id') ?: setting('current_academic_year');
    }
}

if (!function_exists('current_academic_year')) {
    /**
     * Get current academic year ID from session or settings
     */
    function current_academic_year()
    {
        return session()->get('academic_year_id') ?: setting('current_academic_year');
    }
}

if (!function_exists('current_academic_year_name')) {
    /**
     * Get current academic year name from session
     */
    function current_academic_year_name()
    {
        $yearName = session()->get('academic_year_name');
        if (!$yearName) {
            $academicYearId = current_academic_year();
            if ($academicYearId) {
                $academicYearModel = new AcademicYearModel();
                $academicYear = $academicYearModel->find($academicYearId);
                // 🔴 ACESSO COMO ARRAY
                $yearName = $academicYear ? $academicYear['year_name'] : null;
                
                // Guardar na sessão para próxima vez
                if ($yearName) {
                    session()->set('academic_year_name', $yearName);
                }
            }
        }
        
        return $yearName ?: 'Ano não definido';
    }
}

if (!function_exists('current_semester')) {
    /**
     * Get current semester from session
     */
    function current_semester()
    {
        return session()->get('semester_id') ?: setting('current_semester');
    }
}

if (!function_exists('grading_system')) {
    /**
     * Get grading system info
     */
    function grading_system()
    {
        return [
            'min_grade' => setting('min_grade', 0),
            'max_grade' => setting('max_grade', 20),
            'approval_grade' => setting('approval_grade', 10),
            'grading_system' => setting('grading_system', '0-20')
        ];
    }
}

if (!function_exists('default_currency')) {
    /**
     * Get default currency
     */
    function default_currency()
    {
        $currencyId = setting('default_currency', 1);
        
        $currencyModel = new CurrencyModel();
        $currency = $currencyModel->find($currencyId);
        
        if ($currency) {
            // 🔴 ACESSO COMO ARRAY
            return (object)[
                'id' => $currency['id'],
                'currency_name' => $currency['currency_name'],
                'currency_code' => $currency['currency_code'],
                'currency_symbol' => $currency['currency_symbol'],
                'is_default' => $currency['is_default'] ?? false
            ];
        }
        
        return (object)[
            'currency_name' => 'Kwanza',
            'currency_code' => 'AOA',
            'currency_symbol' => 'Kz',
            'is_default' => true
        ];
    }
}

if (!function_exists('format_money')) {
    /**
     * Format money with default currency
     */
    function format_money($amount, $showSymbol = true)
    {
        $currency = default_currency();
        $formatted = number_format($amount, 2, ',', '.');
        
        if ($showSymbol) {
            return $formatted . ' ' . $currency->currency_symbol;
        }
        
        return $formatted;
    }
}