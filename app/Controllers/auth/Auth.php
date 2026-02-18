<?php

namespace App\Controllers\auth;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\RoleModel;
use App\Models\PermissionModel;
use App\Models\RolePermissionModel;

class Auth extends BaseController
{
    protected $userModel;
    protected $roleModel;
    protected $permissionModel;
    protected $rolePermissionModel;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->roleModel = new RoleModel();
        $this->permissionModel = new PermissionModel();
        $this->rolePermissionModel = new RolePermissionModel();
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
     * Process login - VERSÃO CORRIGIDA
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
        
        if (!$user || !password_verify($password, $user->password)) {
            return redirect()->back()->withInput()
                ->with('error', 'Usuário ou senha inválidos.');
        }
        
        // Get role
        $role = $this->roleModel->find($user->role_id);
        
        // Get permissions for this role
        $permissions = $this->permissionModel
            ->select('permission_key')
            ->join('tbl_role_permissions', 'tbl_role_permissions.permission_id = tbl_permissions.id')
            ->where('tbl_role_permissions.role_id', $user->role_id)
            ->findAll();
        
        $permissionKeys = array_column($permissions, 'permission_key');
        
        // DEFINIR CONEXÃO COM O BANCO DE DADOS UMA ÚNICA VEZ
        $db = db_connect();
        
        // Buscar dados adicionais baseado no tipo de usuário
        $additionalData = [];
        
        if ($user->user_type == 'teacher') {
            // Buscar turmas do professor
            $teacherClasses = $db->table('tbl_class_disciplines')
                ->select('tbl_classes.class_name, tbl_classes.id, tbl_disciplines.discipline_name')
                ->join('tbl_classes', 'tbl_classes.id = tbl_class_disciplines.class_id')
                ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
                ->where('tbl_class_disciplines.teacher_id', $user->id)
                ->where('tbl_classes.is_active', 1)
                ->get()
                ->getResult();
            
            $additionalData['teacher_classes'] = $teacherClasses;
            $additionalData['total_classes'] = count($teacherClasses);
        }
        
        if ($user->user_type == 'student') {
            // Buscar dados do aluno
            $student = $db->table('tbl_students')
                ->where('user_id', $user->id)
                ->get()
                ->getRow();
            
            if ($student) {
                $additionalData['student_id'] = $student->id;
                $additionalData['student_number'] = $student->student_number;
                
                // Buscar matrícula atual
                $enrollment = $db->table('tbl_enrollments')
                    ->select('tbl_enrollments.*, tbl_classes.class_name, tbl_academic_years.year_name')
                    ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
                    ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_enrollments.academic_year_id')
                    ->where('tbl_enrollments.student_id', $student->id)
                    ->where('tbl_enrollments.status', 'Ativo')
                    ->orderBy('tbl_enrollments.id', 'DESC')
                    ->limit(1)
                    ->get()
                    ->getRow();
                
                if ($enrollment) {
                    $additionalData['enrollment_id'] = $enrollment->id;
                    $additionalData['class_id'] = $enrollment->class_id;
                    $additionalData['class_name'] = $enrollment->class_name;
                    $additionalData['academic_year'] = $enrollment->year_name;
                }
            }
        }
        
        if ($user->user_type == 'guardian') {
            // Buscar dados do encarregado
            $guardian = $db->table('tbl_guardians')
                ->where('user_id', $user->id)
                ->get()
                ->getRow();
            
            if ($guardian) {
                $additionalData['guardian_id'] = $guardian->id;
                $additionalData['guardian_type'] = $guardian->guardian_type;
                
                // Buscar alunos associados
                $students = $db->table('tbl_student_guardians')
                    ->select('tbl_students.id, tbl_users.first_name, tbl_users.last_name, tbl_students.student_number')
                    ->join('tbl_students', 'tbl_students.id = tbl_student_guardians.student_id')
                    ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
                    ->where('tbl_student_guardians.guardian_id', $guardian->id)
                    ->get()
                    ->getResult();
                
                $additionalData['guardian_students'] = $students;
            }
        }
        
        // Se for staff, pode precisar de dados específicos
        if ($user->user_type == 'staff') {
            // Buscar permissões específicas ou departamentos
            // $additionalData['department'] = ...
        }
        
        // Set session data COMPLETA
        $sessionData = array_merge([
            'user_id' => $user->id,
            'username' => $user->username,
            'name' => $user->first_name . ' ' . $user->last_name,
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'email' => $user->email,
            'role' => $role->role_name ?? 'Sem Perfil',
            'role_id' => $user->role_id,
            'user_type' => $user->user_type,
            'permissions' => $permissionKeys,
            'logged_in' => true,
            'last_login' => date('Y-m-d H:i:s')
        ], $additionalData);
        
        $this->session->set($sessionData);
        
        // Update last login
        $this->userModel->update($user->id, ['last_login' => date('Y-m-d H:i:s')]);
        
        // Registrar log de login
        log_login($user->id, $user->username);
        
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
        
        if ($userId) {
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