<?php

namespace App\Controllers\auth;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\RoleModel;
use App\Models\PermissionModel;
use App\Models\RolePermissionModel;
use App\Models\AcademicYearModel;

class Auth extends BaseController
{
    protected $userModel;
    protected $roleModel;
    protected $permissionModel;
    protected $rolePermissionModel;
    protected $academicYearModel;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
        $this->permissionModel = new PermissionModel();
        $this->rolePermissionModel = new RolePermissionModel();
        $this->academicYearModel = new AcademicYearModel();
    }
    
    /**
     * Show login page
     */
    public function index()
    {
        if ($this->isLoggedIn()) {
            return $this->redirectToDashboard();
        }
        
        return view('auth/login');
    }
    

    /**
     * Process login 
     */
    public function signin()
    {
        $rules = [
            'username' => 'required',
            'password' => 'required'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        $username = $this->request->getPost('username');
        $password = $this->request->getPost('password');
        
        // Find user by username or email
        $user = $this->userModel
            ->where('username', $username)
            ->orWhere('email', $username)
            ->where('is_active', 1)
            ->first();
        
        if (!$user || !password_verify($password, $user['password'])) {
            return redirect()->back()->withInput()
                ->with('error', 'Usuário ou senha inválidos.');
        }
        
        // Get role and ensure user_type is consistent with role_type
        $role = $this->roleModel->find($user['role_id']);
        
        // CORREÇÃO: Verificar se $role é array ou objeto e acessar corretamente
        if ($role) {
            // Obter role_type de forma segura (pode ser array ou objeto)
            $roleType = is_array($role) ? ($role['role_type'] ?? null) : ($role->role_type ?? null);
            
            // IMPORTANTE: Garantir que user_type está consistente com role_type
            // Se role_type for 'admin', user_type deve ser 'admin'
            if ($roleType == 'admin' && ($user['user_type'] ?? '') != 'admin') {
                // Atualizar user_type para admin
                $this->userModel->update($user['id'], ['user_type' => 'admin']);
                $user['user_type'] = 'admin';
            }
        }
        
        // Get permissions for this role
        $permissions = $this->permissionModel
            ->select('permission_key')
            ->join('tbl_role_permissions', 'tbl_role_permissions.permission_id = tbl_permissions.id')
            ->where('tbl_role_permissions.role_id', $user['role_id'])
            ->findAll();
        
        $permissionKeys = array_column($permissions, 'permission_key');
        
        // DEFINIR CONEXÃO COM O BANCO DE DADOS UMA ÚNICA VEZ
        $db = db_connect();
        
        // Buscar dados adicionais baseado no tipo de usuário
        $additionalData = [];
        
        if ($user['user_type'] == 'teacher') {
            // Buscar turmas do professor
            $teacherClasses = $db->table('tbl_class_disciplines')
                ->select('tbl_classes.class_name, tbl_classes.id, tbl_disciplines.discipline_name')
                ->join('tbl_classes', 'tbl_classes.id = tbl_class_disciplines.class_id')
                ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
                ->where('tbl_class_disciplines.teacher_id', $user['id'])
                ->where('tbl_classes.is_active', 1)
                ->get()
                ->getResult();
            
            $additionalData['teacher_classes'] = $teacherClasses;
            $additionalData['total_classes'] = count($teacherClasses);
        }
        
        if ($user['user_type'] == 'student') {
            // Buscar dados do aluno
            $student = $db->table('tbl_students')
                ->where('user_id', $user['id'])
                ->get()
                ->getRow();
            
            if ($student) {
                // CORREÇÃO: Converter objeto para array se necessário
                $studentArray = is_object($student) ? (array)$student : $student;
                
                $additionalData['student_id'] = $studentArray['id'] ?? null;
                $additionalData['student_number'] = $studentArray['student_number'] ?? null;
                
                // Buscar matrícula atual
                $enrollment = $db->table('tbl_enrollments')
                    ->select('tbl_enrollments.*, tbl_classes.class_name, tbl_academic_years.year_name')
                    ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
                    ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_enrollments.academic_year_id')
                    ->where('tbl_enrollments.student_id', $studentArray['id'] ?? null)
                    ->where('tbl_enrollments.status', 'Ativo')
                    ->orderBy('tbl_enrollments.id', 'DESC')
                    ->limit(1)
                    ->get()
                    ->getRow();
                
                if ($enrollment) {
                    $enrollmentArray = is_object($enrollment) ? (array)$enrollment : $enrollment;
                    
                    $additionalData['enrollment_id'] = $enrollmentArray['id'] ?? null;
                    $additionalData['class_id'] = $enrollmentArray['class_id'] ?? null;
                    $additionalData['class_name'] = $enrollmentArray['class_name'] ?? null;
                    $additionalData['academic_year'] = $enrollmentArray['year_name'] ?? null;
                }
            }
        }
        
        if ($user['user_type'] == 'guardian') {
            // Buscar dados do encarregado
            $guardian = $db->table('tbl_guardians')
                ->where('user_id', $user['id'])
                ->get()
                ->getRow();
            
            if ($guardian) {
                $guardianArray = is_object($guardian) ? (array)$guardian : $guardian;
                
                $additionalData['guardian_id'] = $guardianArray['id'] ?? null;
                $additionalData['guardian_type'] = $guardianArray['guardian_type'] ?? null;
                
                // Buscar alunos associados
                $students = $db->table('tbl_student_guardians')
                    ->select('tbl_students.id, tbl_users.first_name, tbl_users.last_name, tbl_students.student_number')
                    ->join('tbl_students', 'tbl_students.id = tbl_student_guardians.student_id')
                    ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
                    ->where('tbl_student_guardians.guardian_id', $guardianArray['id'] ?? null)
                    ->get()
                    ->getResult();
                
                $additionalData['guardian_students'] = $students;
                $additionalData['total_students'] = count($students);
            }
        }
        
        // Se for staff, pode precisar de dados específicos
        if ($user['user_type'] == 'staff') {
            // Buscar permissões específicas ou departamentos
            // $additionalData['department'] = ...
        }

        // Após validar o login
        $academicYearId = setting('current_academic_year');
        $academicYear = $this->academicYearModel->find($academicYearId);
        
        // Obter role_type de forma segura
        $roleType = null;
        if ($role) {
            $roleType = is_array($role) ? ($role['role_type'] ?? null) : ($role->role_type ?? null);
        }
        
        // Set session data COMPLETA
        $sessionData = array_merge([
            'user_id' => $user['id'],
            'username' => $user['username'],
            'name' => ($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? ''),
            'first_name' => $user['first_name'] ?? '',
            'last_name' => $user['last_name'] ?? '',
            'email' => $user['email'] ?? '',
            'role' => ($role ? (is_array($role) ? ($role['role_name'] ?? 'Sem Perfil') : ($role->role_name ?? 'Sem Perfil')) : 'Sem Perfil'),
            'role_id' => $user['role_id'],
            'role_type' => $roleType ?? 'staff', // IMPORTANTE: Adicionar role_type
            'user_type' => $user['user_type'] ?? 'staff',
            'permissions' => $permissionKeys,
            'logged_in' => true,
            'last_login' => date('Y-m-d H:i:s'),

            // Dados acadêmicos
            'academic_year_id' => $academicYearId,
            'academic_year_name' => $academicYear ? ($academicYear['year_name'] ?? null) : null,
            'semester_id' => setting('current_semester')
        ], $additionalData);
        
        $this->session->set($sessionData);
        
        // Update last login
        $this->userModel->update($user['id'], ['last_login' => date('Y-m-d H:i:s')]);
        
        // Registrar log de login (se a função existir)
        if (function_exists('log_login')) {
            log_login($user['id'], $user['username']);
        } else {
            log_message('info', "Login: {$user['username']} ({$user['user_type']})");
        }
        
        return $this->redirectToDashboard();
    }
    
    /**
     * Redirect to appropriate dashboard - MELHORADO
     */
    protected function redirectToDashboard()
    {
        $userType = $this->session->get('user_type');
        
        // Log para debug (remover em produção)
        log_message('debug', 'Redirecionando usuário tipo: ' . $userType);
        
        switch ($userType) {
            case 'admin':
            case 'staff': // STAFF VAI PARA ADMIN DASHBOARD
                return redirect()->to('/admin/dashboard');
            case 'teacher':
                return redirect()->to('/teachers/dashboard');
            case 'student':
                return redirect()->to('/students/dashboard');
            case 'guardian':
                return redirect()->to('/guardians/dashboard');
            default:
                // Se não tiver tipo definido, vai para home
                return redirect()->to('/');
        }
    }
    
    /**
     * Admin login page
     */
    public function adminIndex()
    {
        if ($this->isLoggedIn()) {
            return redirect()->to('/admin/dashboard');
        }
        
        return view('auth/admin-login');
    }
    
    /**
     * Admin login process - usa o mesmo método unificado
     */
    public function adminSignin()
    {
        return $this->signin();
    }
    
    /**
     * Teacher login page
     */
    public function teachersIndex()
    {
        if ($this->isLoggedIn()) {
            return redirect()->to('/teachers/dashboard');
        }
        
        return view('auth/teachers-login');
    }
    
    /**
     * Teacher login process
     */
    public function teachersSignin()
    {
        return $this->signin();
    }
    
    /**
     * Student login page
     */
    public function studentsIndex()
    {
        if ($this->isLoggedIn()) {
            return redirect()->to('/students/dashboard');
        }
        
        return view('auth/students-login');
    }
    
    /**
     * Student login process
     */
    public function studentsSignin()
    {
        return $this->signin();
    }
    
    /**
     * Guardian login page (NOVO)
     */
    public function guardiansIndex()
    {
        if ($this->isLoggedIn()) {
            return redirect()->to('/guardians/dashboard');
        }
        
        return view('auth/guardians-login');
    }
    
    /**
     * Guardian login process
     */
    public function guardiansSignin()
    {
        return $this->signin();
    }
    
    /**
     * Staff login page (opcional)
     */
    public function staffIndex()
    {
        if ($this->isLoggedIn()) {
            return redirect()->to('/admin/dashboard');
        }
        
        return view('auth/staff-login');
    }
    
    /**
     * Staff login process
     */
    public function staffSignin()
    {
        return $this->signin();
    }
    
    /**
     * Logout
     */
    public function logout()
    {
        $userId = $this->session->get('user_id');
        $username = $this->session->get('username');
        
        if ($userId && function_exists('log_logout')) {
            log_logout($userId, $username);
        }
        
        $this->session->destroy();
        return redirect()->to('/auth/login');
    }
    
    /**
     * Forgot password page
     */
    public function forgetpassword()
    {
        return view('auth/forgot-password');
    }
    
    /**
     * Process forgot password
     */
    public function sendResetLink()
    {
        $rules = [
            'email' => 'required|valid_email'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        $email = $this->request->getPost('email');
        
        $user = $this->userModel->where('email', $email)->first();
        
        if (!$user) {
            return redirect()->back()
                ->with('error', 'Email não encontrado.');
        }
        
        // Generate reset token
        $token = bin2hex(random_bytes(32));
        
        // Save token in database (you'll need a password_resets table)
        // $resetModel->save(['email' => $email, 'token' => $token, 'expires_at' => date('Y-m-d H:i:s', strtotime('+1 hour'))]);
        
        // Send email
        // $emailService = service('email');
        // $emailService->setTo($email);
        // $emailService->setSubject('Recuperação de Senha');
        // $emailService->setMessage(view('emails/reset-password', ['token' => $token]));
        // $emailService->send();
        
        return redirect()->to('/auth/login')
            ->with('success', 'Instruções para recuperar a senha foram enviadas para seu email.');
    }
}