<?php
// app/Helpers/auth_helper.php

if (!function_exists('currentUserId')) {
    /**
     * Retorna o ID do usuário logado
     */
    function currentUserId(): ?int
    {
        return session()->get('user_id');
    }
}

if (!function_exists('currentUserName')) {
    /**
     * Retorna o nome completo do usuário logado
     */
    function currentUserName(): ?string
    {
        return session()->get('name');
    }
}

if (!function_exists('currentUserType')) {
    /**
     * Retorna o tipo do usuário logado (admin, staff, teacher, student, guardian)
     */
    function currentUserType(): ?string
    {
        return session()->get('user_type');
    }
}

if (!function_exists('currentUserRole')) {
    /**
     * Retorna o perfil/função do usuário logado
     */
    function currentUserRole(): ?string
    {
        return session()->get('role');
    }
}

if (!function_exists('currentUserEmail')) {
    /**
     * Retorna o email do usuário logado
     */
    function currentUserEmail(): ?string
    {
        return session()->get('email');
    }
}

if (!function_exists('currentUserPhoto')) {
    /**
     * Retorna a foto do usuário logado
     */
    function currentUserPhoto(): ?string
    {
        return session()->get('photo');
    }
}

if (!function_exists('isAdmin')) {
    /**
     * Verifica se o usuário logado é admin (root - ID 1)
     */
    function isAdmin(): bool
    {
        return session()->get('user_id') == 1;
    }
}

if (!function_exists('isStaff')) {
    /**
     * Verifica se o usuário logado é staff ou admin
     */
    function isStaff(): bool
    {
        return in_array(session()->get('user_type'), ['admin', 'staff']);
    }
}

if (!function_exists('isTeacher')) {
    /**
     * Verifica se o usuário logado é professor
     */
    function isTeacher(): bool
    {
        return session()->get('user_type') == 'teacher';
    }
}

if (!function_exists('isStudent')) {
    /**
     * Verifica se o usuário logado é aluno
     */
    function isStudent(): bool
    {
        return session()->get('user_type') == 'student';
    }
}

if (!function_exists('isGuardian')) {
    /**
     * Verifica se o usuário logado é encarregado
     */
    function isGuardian(): bool
    {
        return session()->get('user_type') == 'guardian';
    }
}

if (!function_exists('getStudentIdFromUser')) {
    /**
     * Retorna o ID do estudante a partir do ID do usuário logado
     * (para alunos)
     */
    function getStudentIdFromUser(?int $userId = null): ?int
    {
        $userId = $userId ?? currentUserId();
        if (!$userId) {
            return null;
        }
        
        $db = db_connect();
        $student = $db->table('tbl_students')
            ->select('id')
            ->where('user_id', $userId)
            ->get()
            ->getRow();
        
        return $student ? $student->id : null;
    }
}

if (!function_exists('getTeacherIdFromUser')) {
    /**
     * Retorna o ID do professor a partir do ID do usuário logado
     * (para professores)
     */
    function getTeacherIdFromUser(?int $userId = null): ?int
    {
        $userId = $userId ?? currentUserId();
        if (!$userId) {
            return null;
        }
        
        $db = db_connect();
        $teacher = $db->table('tbl_teachers')
            ->select('id')
            ->where('user_id', $userId)
            ->get()
            ->getRow();
        
        return $teacher ? $teacher->id : null;
    }
}

if (!function_exists('getGuardianIdFromUser')) {
    /**
     * Retorna o ID do encarregado a partir do ID do usuário logado
     * (para encarregados)
     */
    function getGuardianIdFromUser(?int $userId = null): ?int
    {
        $userId = $userId ?? currentUserId();
        if (!$userId) {
            return null;
        }
        
        $db = db_connect();
        $guardian = $db->table('tbl_guardians')
            ->select('id')
            ->where('user_id', $userId)
            ->get()
            ->getRow();
        
        return $guardian ? $guardian->id : null;
    }
}
if (!function_exists('getUserName')) {
    /**
     * Retorna o nome completo do usuário pelo ID
     */
    function getUserName($userId): ?string
    {
        if (!$userId) {
            return null;
        }
        
        $db = db_connect();
        $user = $db->table('tbl_users')
            ->select('first_name, last_name')
            ->where('id', $userId)
            ->get()
            ->getRow();
        
        if ($user) {
            return $user->first_name . ' ' . $user->last_name;
        }
        
        return null;
    }
}

if (!function_exists('getUserData')) {
    /**
     * Retorna todos os dados do usuário pelo ID
     */
    function getUserData($userId): ?object
    {
        if (!$userId) {
            return null;
        }
        
        $db = db_connect();
        return $db->table('tbl_users')
            ->select('id, username, email, first_name, last_name, phone, user_type, photo')
            ->where('id', $userId)
            ->get()
            ->getRow();
    }
}

if (!function_exists('getUserEmail')) {
    /**
     * Retorna o email do usuário pelo ID
     */
    function getUserEmail($userId): ?string
    {
        if (!$userId) {
            return null;
        }
        
        $db = db_connect();
        $user = $db->table('tbl_users')
            ->select('email')
            ->where('id', $userId)
            ->get()
            ->getRow();
        
        return $user ? $user->email : null;
    }
}

if (!function_exists('getUserType')) {
    /**
     * Retorna o tipo do usuário pelo ID
     */
    function getUserType($userId): ?string
    {
        if (!$userId) {
            return null;
        }
        
        $db = db_connect();
        $user = $db->table('tbl_users')
            ->select('user_type')
            ->where('id', $userId)
            ->get()
            ->getRow();
        
        return $user ? $user->user_type : null;
    }
}

if (!function_exists('formatUserType')) {
    /**
     * Formata o tipo de usuário para exibição
     */
    function formatUserType($userType): string
    {
        $types = [
            'admin' => 'Administrador',
            'staff' => 'Funcionário',
            'teacher' => 'Professor',
            'student' => 'Aluno',
            'guardian' => 'Encarregado'
        ];
        
        return $types[$userType] ?? ucfirst($userType);
    }
}