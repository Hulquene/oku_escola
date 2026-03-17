<?php
// app/Helpers/auth_helper.php

use App\Models\UserModel;
use App\Models\StudentModel;
use App\Models\TeacherModel;
use App\Models\GuardianModel;

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
        
        $studentModel = new StudentModel();
        
        $student = $studentModel
            ->select('id')
            ->where('user_id', $userId)
            ->first();
        
        return $student ? ($student['id'] ?? null) : null;
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
        
        $teacherModel = new TeacherModel();
        
        $teacher = $teacherModel
            ->select('id')
            ->where('user_id', $userId)
            ->first();
        
        return $teacher ? ($teacher['id'] ?? null) : null;
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
        
        $guardianModel = new GuardianModel();
        
        $guardian = $guardianModel
            ->select('id')
            ->where('user_id', $userId)
            ->first();
        
        return $guardian ? ($guardian['id'] ?? null) : null;
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
        
        $userModel = new UserModel();
        
        $user = $userModel
            ->select('first_name, last_name')
            ->find($userId);
        
        if ($user) {
            return ($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '');
        }
        
        return null;
    }
}

if (!function_exists('getUserData')) {
    /**
     * Retorna todos os dados do usuário pelo ID
     * @return array|null
     */
    function getUserData($userId): ?array
    {
        if (!$userId) {
            return null;
        }
        
        $userModel = new UserModel();
        
        return $userModel
            ->select('id, username, email, first_name, last_name, phone, user_type, photo')
            ->find($userId);
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
        
        $userModel = new UserModel();
        
        $user = $userModel
            ->select('email')
            ->find($userId);
        
        return $user ? ($user['email'] ?? null) : null;
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
        
        $userModel = new UserModel();
        
        $user = $userModel
            ->select('user_type')
            ->find($userId);
        
        return $user ? ($user['user_type'] ?? null) : null;
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
        
        return $types[$userType] ?? ucfirst((string)$userType);
    }
}

if (!function_exists('getUserRoleName')) {
    /**
     * Retorna o nome do perfil do usuário pelo ID
     */
    function getUserRoleName($userId): ?string
    {
        if (!$userId) {
            return null;
        }
        
        $db = db_connect();
        
        $user = $db->table('tbl_users u')
            ->select('r.role_name')
            ->join('tbl_roles r', 'r.id = u.role_id', 'left')
            ->where('u.id', $userId)
            ->get()
            ->getRow();
        
        return $user ? ($user->role_name ?? null) : null;
    }
}

if (!function_exists('getUserRoleId')) {
    /**
     * Retorna o ID do perfil do usuário pelo ID
     */
    function getUserRoleId($userId): ?int
    {
        if (!$userId) {
            return null;
        }
        
        $userModel = new UserModel();
        
        $user = $userModel
            ->select('role_id')
            ->find($userId);
        
        return $user ? ($user['role_id'] ?? null) : null;
    }
}

if (!function_exists('hasPermission')) {
    /**
     * Verifica se o usuário atual tem uma determinada permissão
     */
    function hasPermission(string $permissionKey): bool
    {
        $permissions = session()->get('permissions') ?? [];
        return in_array($permissionKey, $permissions) || isAdmin();
    }
}

if (!function_exists('can')) {
    /**
     * Alias para hasPermission
     */
    function can(string $permissionKey): bool
    {
        return hasPermission($permissionKey);
    }
}

if (!function_exists('getUserPermissions')) {
    /**
     * Retorna as permissões do usuário atual
     */
    function getUserPermissions(): array
    {
        return session()->get('permissions') ?? [];
    }
}