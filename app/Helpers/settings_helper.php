<?php
// app/Helpers/settings_helper.php

use App\Models\SettingsModel;

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

if (!function_exists('current_academic_year')) {
    /**
     * Get current academic year
     */
    function current_academic_year()
    {
        return setting('current_academic_year');
    }
}

if (!function_exists('current_semester')) {
    /**
     * Get current semester
     */
    function current_semester()
    {
        return setting('current_semester');
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
        
        $currencyModel = new \App\Models\CurrencyModel();
        $currency = $currencyModel->find($currencyId);
        
        return $currency ?: (object)[
            'currency_name' => 'Kwanza',
            'currency_code' => 'AOA',
            'currency_symbol' => 'Kz'
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