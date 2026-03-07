<?php
// app/Helpers/csrf_helper.php
// Criar este helper para funções CSRF

if (!function_exists('csrf_verify')) {
    function csrf_verify($token) {
        if (empty($token)) {
            return false;
        }
        
        $security = \Config\Services::security();
        $expected = $security->getCSRFHash();
        
        return hash_equals($expected, $token);
    }
}