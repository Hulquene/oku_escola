<?php
// app/Helpers/teacher_helper.php

if (!function_exists('get_teacher_info')) {
    function get_teacher_info($key = null)
    {
        $session = session();
        if ($key) {
            return $session->get($key);
        }
        return [
            'id' => $session->get('user_id'),
            'name' => $session->get('name'),
            'email' => $session->get('email'),
            'photo' => $session->get('photo')
        ];
    }
}

if (!function_exists('is_teacher_page')) {
    function is_teacher_page($page)
    {
        return uri_string() == $page;
    }
}

if (!function_exists('is_teacher_page_active')) {
    function is_teacher_page_active($pages)
    {
        $current = uri_string();
        foreach ($pages as $page) {
            if (strpos($current, $page) === 0) {
                return true;
            }
        }
        return false;
    }
}