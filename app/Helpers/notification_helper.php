<?php
// app/Helpers/notification_helper.php

use App\Models\NotificationModel;

if (!function_exists('get_unread_notifications_count')) {
    /**
     * Get unread notifications count for current user
     */
    function get_unread_notifications_count()
    {
        $userId = session()->get('user_id');
        if (!$userId) return 0;
        
        $notificationModel = new NotificationModel();
        return $notificationModel->getUnreadCount($userId);
    }
}

if (!function_exists('get_recent_notifications')) {
    /**
     * Get recent notifications for current user
     */
    function get_recent_notifications($limit = 5)
    {
        $userId = session()->get('user_id');
        if (!$userId) return [];
        
        $notificationModel = new NotificationModel();
        return $notificationModel->getRecent($userId, $limit);
    }
}

if (!function_exists('time_elapsed_string')) {
    /**
     * Format time elapsed
     */
    function time_elapsed_string($datetime) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);
        
        if ($diff->y > 0) return $diff->y . ' ano' . ($diff->y > 1 ? 's' : '') . ' atrás';
        if ($diff->m > 0) return $diff->m . ' mês' . ($diff->m > 1 ? 'es' : '') . ' atrás';
        if ($diff->d > 0) return $diff->d . ' dia' . ($diff->d > 1 ? 's' : '') . ' atrás';
        if ($diff->h > 0) return $diff->h . ' hora' . ($diff->h > 1 ? 's' : '') . ' atrás';
        if ($diff->i > 0) return $diff->i . ' minuto' . ($diff->i > 1 ? 's' : '') . ' atrás';
        return 'agora mesmo';
    }
}

if (!function_exists('notify_user')) {
    /**
     * Send notification to a single user
     */
    function notify_user($userId, $userType, $data)
    {
        $notificationModel = new NotificationModel();
        return $notificationModel->createForUser($userId, $userType, $data);
    }
}

if (!function_exists('notify_user_type')) {
    /**
     * Send notification to all users of a type
     */
    function notify_user_type($userType, $data, $userIds = [])
    {
        $notificationModel = new NotificationModel();
        return $notificationModel->createForUserType($userType, $data, $userIds);
    }
}

if (!function_exists('notify_all')) {
    /**
     * Send notification to all active users
     */
    function notify_all($data)
    {
        $notificationModel = new NotificationModel();
        return $notificationModel->createGlobal($data);
    }
}