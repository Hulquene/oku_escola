<?php

if (!function_exists('has_permission')) {
    /**
     * Check if current user has permission
     */
    function has_permission(string $permission): bool
    {
        $session = service('session');
        $permissions = $session->get('permissions') ?? [];
        return in_array($permission, $permissions);
    }
}

if (!function_exists('can')) {
    /**
     * Alias for has_permission
     */
    function can(string $permission): bool
    {
        return has_permission($permission);
    }
}

if (!function_exists('current_user')) {
    /**
     * Get current user data
     */
    function current_user(string $key = null)
    {
        $session = service('session');
        
        if (!$session->has('user_id')) {
            return null;
        }
        
        if ($key) {
            return $session->get($key);
        }
        
        return (object)[
            'id' => $session->get('user_id'),
            'username' => $session->get('username'),
            'name' => $session->get('name'),
            'email' => $session->get('email'),
            'role' => $session->get('role'),
            'user_type' => $session->get('user_type')
        ];
    }
}

if (!function_exists('format_money')) {
    /**
     * Format money in AOA
     */
    function format_money($value)
    {
        return number_format($value, 2, ',', '.') . ' Kz';
    }
}

if (!function_exists('format_date')) {
    /**
     * Format date to Brazilian/Portuguese style
     */
    function format_date($date, $format = 'd/m/Y')
    {
        if (empty($date)) {
            return '';
        }
        return date($format, strtotime($date));
    }
}