<?php

if (!function_exists('getActivityIcon')) {
    /**
     * Retorna o ícone FontAwesome baseado no tipo de ação
     */
    function getActivityIcon($action) {
        $action = strtolower($action);
        $icons = [
            'login' => 'fa-sign-in-alt',
            'logout' => 'fa-sign-out-alt',
            'create' => 'fa-plus-circle',
            'insert' => 'fa-plus-circle',
            'update' => 'fa-edit',
            'edit' => 'fa-edit',
            'delete' => 'fa-trash',
            'remove' => 'fa-trash',
            'view' => 'fa-eye',
            'visualizar' => 'fa-eye',
            'export' => 'fa-file-export',
            'import' => 'fa-file-import',
            'download' => 'fa-download',
            'upload' => 'fa-upload',
            'print' => 'fa-print',
            'email' => 'fa-envelope',
            'send' => 'fa-paper-plane',
            'approve' => 'fa-check-circle',
            'reject' => 'fa-times-circle',
            'cancel' => 'fa-ban',
            'archive' => 'fa-archive',
            'restore' => 'fa-trash-restore',
            'password' => 'fa-key',
            'profile' => 'fa-user-circle'
        ];
        
        foreach ($icons as $key => $icon) {
            if (strpos($action, $key) !== false) {
                return $icon;
            }
        }
        
        return 'fa-circle';
    }
}

if (!function_exists('getActivityColor')) {
    /**
     * Retorna a cor baseada no tipo de ação
     */
    function getActivityColor($action) {
        $action = strtolower($action);
        $colors = [
            'login' => 'success',
            'logout' => 'secondary',
            'create' => 'success',
            'insert' => 'success',
            'update' => 'info',
            'edit' => 'info',
            'delete' => 'danger',
            'remove' => 'danger',
            'view' => 'primary',
            'visualizar' => 'primary',
            'export' => 'warning',
            'import' => 'purple',
            'download' => 'primary',
            'upload' => 'success',
            'print' => 'secondary',
            'email' => 'info',
            'send' => 'info',
            'approve' => 'success',
            'reject' => 'danger',
            'cancel' => 'warning',
            'archive' => 'secondary',
            'restore' => 'success',
            'password' => 'warning',
            'profile' => 'primary'
        ];
        
        foreach ($colors as $key => $color) {
            if (strpos($action, $key) !== false) {
                return $color;
            }
        }
        
        return 'secondary';
    }
}
