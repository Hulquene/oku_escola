<?php

use App\Models\UserLogModel;

if (!function_exists('log_action')) {
    /**
     * Registra uma ação do usuário nos logs
     * 
     * @param string $action Ação realizada (insert, update, delete, login, logout, view, etc)
     * @param string $description Descrição detalhada da ação
     * @param int|null $targetId ID do registro afetado (opcional)
     * @param string|null $targetType Tipo do registro afetado (ex: 'user', 'student', 'teacher')
     * @param array|null $details Detalhes adicionais em formato array
     * @return bool
     */
    function log_action($action, $description = null, $targetId = null, $targetType = null, $details = null)
    {
        $session = service('session');
        $request = service('request');
        
        $logModel = new UserLogModel();
        
        $data = [
            'user_id' => $session->get('user_id') ?? 0,
            'action' => $action,
            'description' => $description,
            'target_id' => $targetId,
            'target_type' => $targetType,
            'ip_address' => $request->getIPAddress(),
            'user_agent' => $request->getUserAgent()->getAgentString(),
            'request_method' => $request->getMethod(),
            'request_url' => current_url(),
            'details' => $details ? json_encode($details) : null,
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        return $logModel->insert($data);
    }
}

if (!function_exists('log_insert')) {
    /**
     * Registra uma inserção
     */
    function log_insert($targetType, $targetId, $description, $details = null)
    {
        return log_action('insert', $description, $targetId, $targetType, $details);
    }
}

if (!function_exists('log_update')) {
    /**
     * Registra uma atualização
     */
    function log_update($targetType, $targetId, $description, $details = null)
    {
        return log_action('update', $description, $targetId, $targetType, $details);
    }
}

if (!function_exists('log_delete')) {
    /**
     * Registra uma eliminação
     */
    function log_delete($targetType, $targetId, $description, $details = null)
    {
        return log_action('delete', $description, $targetId, $targetType, $details);
    }
}

if (!function_exists('log_view')) {
    /**
     * Registra uma visualização
     */
    function log_view($targetType, $targetId, $description)
    {
        return log_action('view', $description, $targetId, $targetType);
    }
}

if (!function_exists('log_login')) {
    /**
     * Registra um login
     */
    function log_login($userId, $username, $status = 'success')
    {
        return log_action('login', "Login do usuário: {$username} - Status: {$status}", $userId, 'user');
    }
}

if (!function_exists('log_logout')) {
    /**
     * Registra um logout
     */
    function log_logout($userId, $username)
    {
        return log_action('logout', "Logout do usuário: {$username}", $userId, 'user');
    }
}

if (!function_exists('log_error')) {
    /**
     * Registra um erro
     */
    function log_error($error, $details = null)
    {
        return log_action('error', $error, null, null, $details);
    }
}

if (!function_exists('log_export')) {
    /**
     * Registra uma exportação
     */
    function log_export($targetType, $description, $details = null)
    {
        return log_action('export', $description, null, $targetType, $details);
    }
}

if (!function_exists('log_import')) {
    /**
     * Registra uma importação
     */
    function log_import($targetType, $description, $count, $details = null)
    {
        $data = array_merge(['imported_count' => $count], $details ?? []);
        return log_action('import', $description, null, $targetType, $data);
    }
}

if (!function_exists('get_user_actions')) {
    /**
     * Retorna lista de ações para dropdown
     */
    function get_user_actions()
    {
        return [
            'insert' => 'Inserção',
            'update' => 'Atualização',
            'delete' => 'Eliminação',
            'view' => 'Visualização',
            'login' => 'Login',
            'logout' => 'Logout',
            'export' => 'Exportação',
            'import' => 'Importação',
            'error' => 'Erro'
        ];
    }
}

if (!function_exists('get_action_badge')) {
    /**
     * Retorna badge HTML para a ação
     */
    function get_action_badge($action)
    {
        $badges = [
            'insert' => 'success',
            'update' => 'info',
            'delete' => 'danger',
            'view' => 'secondary',
            'login' => 'primary',
            'logout' => 'warning',
            'export' => 'dark',
            'import' => 'dark',
            'error' => 'danger'
        ];
        
        $class = $badges[$action] ?? 'secondary';
        $label = get_user_actions()[$action] ?? ucfirst($action);
        
        return "<span class='badge bg-{$class}'>{$label}</span>";
    }
}