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

if (!function_exists('get_notification_icon')) {
    /**
     * Get icon for notification type
     */
    function get_notification_icon($type = 'default')
    {
        $icons = [
            'exam_schedule' => 'fa-calendar-check',
            'exam_result' => 'fa-star',
            'attendance' => 'fa-user-check',
            'grade' => 'fa-chart-line',
            'payment' => 'fa-money-bill',
            'invoice' => 'fa-file-invoice',
            'message' => 'fa-envelope',
            'alert' => 'fa-exclamation-triangle',
            'info' => 'fa-info-circle',
            'success' => 'fa-check-circle',
            'warning' => 'fa-exclamation-circle',
            'default' => 'fa-bell'
        ];
        
        return $icons[$type] ?? $icons['default'];
    }
}

if (!function_exists('get_notification_color')) {
    /**
     * Get color for notification type
     */
    function get_notification_color($type = 'default')
    {
        $colors = [
            'exam_schedule' => 'primary',
            'exam_result' => 'success',
            'attendance' => 'info',
            'grade' => 'warning',
            'payment' => 'success',
            'invoice' => 'secondary',
            'message' => 'info',
            'alert' => 'danger',
            'info' => 'primary',
            'success' => 'success',
            'warning' => 'warning',
            'default' => 'secondary'
        ];
        
        return $colors[$type] ?? $colors['default'];
    }
}

if (!function_exists('format_notification_time')) {
    /**
     * Format notification time
     */
    function format_notification_time($datetime)
    {
        if (empty($datetime)) return '';
        
        $now = new DateTime();
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);
        
        if ($diff->y > 0) {
            return $diff->y . ' ano' . ($diff->y > 1 ? 's' : '') . ' atrás';
        }
        if ($diff->m > 0) {
            return $diff->m . ' mês' . ($diff->m > 1 ? 'es' : '') . ' atrás';
        }
        if ($diff->d > 0) {
            if ($diff->d == 1) return 'ontem';
            return $diff->d . ' dias atrás';
        }
        if ($diff->h > 0) {
            return $diff->h . ' hora' . ($diff->h > 1 ? 's' : '') . ' atrás';
        }
        if ($diff->i > 0) {
            return $diff->i . ' minuto' . ($diff->i > 1 ? 's' : '') . ' atrás';
        }
        return 'agora mesmo';
    }
}

if (!function_exists('time_elapsed_string')) {
    /**
     * Alias for format_notification_time (backward compatibility)
     */
    function time_elapsed_string($datetime) {
        return format_notification_time($datetime);
    }
}

if (!function_exists('notify_user')) {
    /**
     * Send notification to a single user
     * 
     * @param int $userId User ID
     * @param string $userType User type (student, teacher, parent, etc)
     * @param array $data Notification data (title, message, type, link, icon, color)
     * @return bool|int
     */
    function notify_user($userId, $userType, $data)
    {
        if (empty($userId) || empty($userType) || empty($data)) {
            return false;
        }
        
        $notificationModel = new NotificationModel();
        
        // Set default values if not provided
        $notificationData = [
            'user_id' => $userId,
            'user_type' => $userType,
            'title' => $data['title'] ?? 'Notificação',
            'message' => $data['message'] ?? '',
            'type' => $data['type'] ?? 'info',
            'icon' => $data['icon'] ?? get_notification_icon($data['type'] ?? 'default'),
            'color' => $data['color'] ?? get_notification_color($data['type'] ?? 'default'),
            'link' => $data['link'] ?? null,
            'is_read' => 0,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $notificationModel->insert($notificationData);
    }
}

if (!function_exists('notify_user_type')) {
    /**
     * Send notification to all users of a type
     * 
     * @param string $userType User type (student, teacher, parent, etc)
     * @param array $data Notification data
     * @param array $userIds Specific user IDs (optional)
     * @return bool|int
     */
    function notify_user_type($userType, $data, $userIds = [])
    {
        if (empty($userType) || empty($data)) {
            return false;
        }
        
        $notificationModel = new NotificationModel();
        
        // Set default values if not provided
        $notificationData = [
            'user_type' => $userType,
            'title' => $data['title'] ?? 'Notificação',
            'message' => $data['message'] ?? '',
            'type' => $data['type'] ?? 'info',
            'icon' => $data['icon'] ?? get_notification_icon($data['type'] ?? 'default'),
            'color' => $data['color'] ?? get_notification_color($data['type'] ?? 'default'),
            'link' => $data['link'] ?? null,
            'is_read' => 0
        ];
        
        if (!empty($userIds)) {
            return $notificationModel->createForSpecificUsers($userType, $notificationData, $userIds);
        }
        
        return $notificationModel->createForUserType($userType, $notificationData);
    }
}

if (!function_exists('notify_all')) {
    /**
     * Send notification to all active users
     * 
     * @param array $data Notification data
     * @return bool|int
     */
    function notify_all($data)
    {
        if (empty($data)) {
            return false;
        }
        
        $notificationModel = new NotificationModel();
        
        // Set default values if not provided
        $notificationData = [
            'title' => $data['title'] ?? 'Notificação Geral',
            'message' => $data['message'] ?? '',
            'type' => $data['type'] ?? 'info',
            'icon' => $data['icon'] ?? get_notification_icon($data['type'] ?? 'default'),
            'color' => $data['color'] ?? get_notification_color($data['type'] ?? 'default'),
            'link' => $data['link'] ?? null,
            'is_read' => 0
        ];
        
        return $notificationModel->createGlobal($notificationData);
    }
}

if (!function_exists('mark_notification_as_read')) {
    /**
     * Mark notification as read
     */
    function mark_notification_as_read($notificationId, $userId)
    {
        $notificationModel = new NotificationModel();
        return $notificationModel->markAsRead($notificationId, $userId);
    }
}

if (!function_exists('mark_all_notifications_as_read')) {
    /**
     * Mark all notifications as read for a user
     */
    function mark_all_notifications_as_read($userId)
    {
        $notificationModel = new NotificationModel();
        return $notificationModel->markAllAsRead($userId);
    }
}

if (!function_exists('delete_notification')) {
    /**
     * Delete a notification
     */
    function delete_notification($notificationId, $userId)
    {
        $notificationModel = new NotificationModel();
        return $notificationModel->where('id', $notificationId)
            ->where('user_id', $userId)
            ->delete();
    }
}