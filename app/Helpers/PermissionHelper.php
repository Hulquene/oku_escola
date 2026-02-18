<?php

if (!function_exists('has_permission')) {
    /**
     * Check if current user has permission
     */
    function has_permission(string $permission): bool
    {
        $session = service('session');
        
        // Root (ID 1) tem todas as permissões
        if ($session->get('user_id') == 1) {
            return true;
        }
        
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

if (!function_exists('is_staff')) {
    /**
     * Check if current user is staff or admin
     */
    function is_staff(): bool
    {
        $session = service('session');
        $userType = $session->get('user_type');
        return in_array($userType, ['admin', 'staff']);
    }
}

if (!function_exists('is_admin')) {
    /**
     * Check if current user is admin (root)
     */
    function is_admin(): bool
    {
        return service('session')->get('user_id') == 1;
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