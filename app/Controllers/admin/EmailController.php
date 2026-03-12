<?php
// app/Controllers/admin/EmailController.php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\SettingsModel;
use App\Models\UserModel;
use App\Models\StudentModel;
use App\Models\TeacherModel;
use App\Models\GuardianModel;
use App\Models\EnrollmentModel;
use App\Models\EmailLogModel;

class EmailController extends BaseController
{
    protected $settingsModel;
    protected $usersModel;
    protected $studentsModel;
    protected $teachersModel;
    protected $guardiansModel;
    protected $enrollmentsModel;
    protected $emailLogModel;
    protected $db;
    
    public function __construct()
    {
        $this->settingsModel = new SettingsModel();
        $this->usersModel = new UserModel();
        $this->studentsModel = new StudentModel();
        $this->teachersModel = new TeacherModel();
        $this->guardiansModel = new GuardianModel();
        $this->enrollmentsModel = new EnrollmentModel();
        $this->emailLogModel = new EmailLogModel();
        $this->db = \Config\Database::connect();
        
        helper(['email', 'settings', 'upload', 'auth', 'log']);
        
        // Verificar se está logado
        if (!session()->get('isLoggedIn')) {
            return redirect()->to('/login')->with('error', 'Faça login para acessar o sistema');
        }
        
        // Verificar permissões usando o helper auth
        $this->checkPermissions();
    }
    
    /**
     * Verificar permissões do usuário
     */
    private function checkPermissions()
    {
        $uri = service('uri');
        $method = $uri->getSegment(3) ?? 'index';
        
        // Mapear métodos para permissões
        $permissionsMap = [
            'index' => 'emails.view',
            'compose' => 'emails.send',
            'bulk' => 'emails.bulk',
            'send' => 'emails.send',
            'sendBulk' => 'emails.bulk',
            'templates' => 'emails.templates',
            'previewTemplate' => 'emails.templates',
            'test' => 'emails.test'
        ];
        
        // Admin (user_type = 'admin') tem todas as permissões
        if (session()->get('user_type') == 'admin') {
            return;
        }
        
        // Verificar permissão específica
        if (isset($permissionsMap[$method])) {
            $permission = $permissionsMap[$method];
            
            if (!has_permission($permission)) {
                // Log da tentativa de acesso não autorizado
                log_action(
                    'error',
                    "Tentativa de acesso não autorizado ao EmailController::{$method} - Permissão necessária: {$permission}",
                    null,
                    'email',
                    ['ip' => $this->request->getIPAddress()]
                );
                
                return redirect()->to('/admin/dashboard')
                    ->with('error', 'Você não tem permissão para acessar esta funcionalidade');
            }
        }
    }
    
    /**
     * Página principal de gerenciamento de emails
     */
    public function index()
    {
        // Verificar permissão
        if (!has_permission('emails.view') && !is_admin()) {
            return redirect()->to('/admin/dashboard')
                ->with('error', 'Você não tem permissão para ver emails');
        }
        
        $data['title'] = 'Gerenciar Emails';
        $data['settings'] = $this->settingsModel->getAll();
        
        // Estatísticas
        $data['total_students'] = $this->studentsModel->where('is_active', 1)->countAllResults();
        $data['total_teachers'] = $this->teachersModel->where('is_active', 1)->countAllResults();
        $data['total_guardians'] = $this->guardiansModel->where('is_active', 1)->countAllResults();
        
        // Estatísticas de emails
        $data['total_emails_sent'] = $this->emailLogModel->countAll();
        $data['emails_today'] = $this->emailLogModel
            ->where('DATE(sent_at)', date('Y-m-d'))
            ->countAllResults();
        
        // Últimos emails enviados
        $data['recent_emails'] = $this->getRecentEmails(10);
        
        // Log da visualização
        log_action(
            'view',
            'Visualizou a página de gerenciamento de emails',
            null,
            'email'
        );
        
        return view('admin/emails/index', $data);
    }

/**
 * Página para enviar email para um usuário específico
 * 
 * @param int|null $userId ID do usuário (opcional)
 * @param string|null $userType Tipo de usuário (student, teacher, guardian)
 */
public function compose($userId = null, $userType = null)
{
    // Verificar permissão
    if (!has_permission('emails.send') && !is_admin()) {
        return redirect()->to('/admin/emails')
            ->with('error', 'Você não tem permissão para enviar emails');
    }
    
    $data['title'] = 'Compor Email';
    $data['templates'] = $this->getEmailTemplates();
    $data['recipient'] = null;
    $data['selected_template'] = $this->request->getGet('template') ?? '';
    
    // Se tiver userId e userType válidos, buscar destinatário
    if ($userId !== null && $userId > 0 && $userType !== null) {
        $user = $this->getUserDetails($userId, $userType);
        if ($user) {
            $data['recipient'] = $user;
        } else {
            // Log do erro
            log_action(
                'error',
                "Tentativa de compor email para {$userType} com ID {$userId} não encontrado",
                null,
                'email'
            );
            
            return redirect()->to('/admin/emails/compose')
                ->with('warning', 'Destinatário não encontrado. Componha o email manualmente.');
        }
    }
    
    // Log da visualização
    if ($data['recipient']) {
        log_action(
            'view',
            "Acessou página de composição de email para: {$data['recipient']['name']} ({$data['recipient']['email']})",
            $userId,
            'email'
        );
    } else {
        log_action(
            'view',
            'Acessou página de composição de email',
            null,
            'email'
        );
    }
    
    return view('admin/emails/compose', $data);
}  
    /**
     * Página para enviar email em massa
     */
    public function bulk()
    {
        // Verificar permissão
        if (!has_permission('emails.bulk') && !is_admin()) {
            return redirect()->to('/admin/emails')
                ->with('error', 'Você não tem permissão para enviar emails em massa');
        }
        
        $data['title'] = 'Enviar Email em Massa';
        $data['templates'] = $this->getEmailTemplates();
        
        // Listas para seleção (apenas ativos)
        $data['students'] = $this->studentsModel
            ->select('tbl_students.id, tbl_users.first_name, tbl_users.last_name, tbl_users.email')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->where('tbl_students.is_active', 1)
            ->orderBy('tbl_users.first_name', 'ASC')
            ->findAll(500);
        
        $data['teachers'] = $this->teachersModel
            ->select('tbl_teachers.id, tbl_users.first_name, tbl_users.last_name, tbl_users.email')
            ->join('tbl_users', 'tbl_users.id = tbl_teachers.user_id')
            ->where('tbl_teachers.is_active', 1)
            ->orderBy('tbl_users.first_name', 'ASC')
            ->findAll(500);
        
        $data['guardians'] = $this->guardiansModel
            ->select('id, full_name as first_name, email')
            ->where('is_active', 1)
            ->orderBy('full_name', 'ASC')
            ->findAll(500);
        
        return view('admin/emails/bulk', $data);
    }
    
    /**
     * Processar envio de email individual
     */
    public function send()
    {
        // Verificar permissão
        if (!has_permission('emails.send') && !is_admin()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Você não tem permissão para enviar emails'
            ]);
        }
        
        $rules = [
            'recipient_email' => 'required|valid_email',
            'recipient_name' => 'required',
            'subject' => 'required',
            'template' => 'required',
            'message' => 'required'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'name' => $this->request->getPost('recipient_name'),
            'content' => $this->request->getPost('message'),
            'custom_message' => $this->request->getPost('message'),
            'button_text' => $this->request->getPost('button_text'),
            'button_url' => $this->request->getPost('button_url'),
            'school_name' => setting('school_name'),
            'school_logo' => school_logo_url()
        ];
        
        $options = [];
        
        // Anexos
        $attachments = $this->request->getFileMultiple('attachments');
        if ($attachments) {
            $options['attachments'] = [];
            foreach ($attachments as $attachment) {
                if ($attachment && $attachment->isValid() && !$attachment->hasMoved()) {
                    // Validar tamanho (5MB máximo)
                    if ($attachment->getSize() > 5 * 1024 * 1024) {
                        return redirect()->back()
                            ->with('error', 'Arquivo muito grande. Máximo 5MB.');
                    }
                    
                    // Validar tipo
                    $allowedTypes = ['pdf', 'doc', 'docx', 'jpg', 'jpeg', 'png'];
                    $ext = $attachment->getExtension();
                    if (!in_array(strtolower($ext), $allowedTypes)) {
                        return redirect()->back()
                            ->with('error', 'Tipo de arquivo não permitido. Permitidos: PDF, DOC, DOCX, JPG, PNG');
                    }
                    
                    $newName = $attachment->getRandomName();
                    $attachment->move(FCPATH . 'uploads/temp', $newName);
                    $options['attachments'][] = [
                        'path' => FCPATH . 'uploads/temp/' . $newName,
                        'name' => $attachment->getClientName()
                    ];
                }
            }
        }
        
        $template = $this->request->getPost('template');
        $subject = $this->request->getPost('subject');
        $to = $this->request->getPost('recipient_email');
        $recipientName = $this->request->getPost('recipient_name');
        $recipientId = $this->request->getPost('recipient_id');
        $recipientType = $this->request->getPost('recipient_type');
        
        $result = send_email($to, $subject, $template, $data, $options);
        
        // Limpar arquivos temporários
        if (!empty($options['attachments'])) {
            foreach ($options['attachments'] as $file) {
                if (file_exists($file['path'])) {
                    unlink($file['path']);
                }
            }
        }
        
        if ($result['success']) {
            // Registrar envio no log
            $logId = $this->logEmail([
                'recipient' => $to,
                'recipient_name' => $recipientName,
                'recipient_id' => $recipientId,
                'recipient_type' => $recipientType,
                'subject' => $subject,
                'template' => $template,
                'status' => 'sent',
                'sent_by' => session()->get('user_id')
            ]);
            
            // Log da ação
            log_action(
                'insert',
                "Email enviado para: {$recipientName} ({$to}) - Assunto: {$subject}",
                $logId,
                'email',
                [
                    'recipient' => $to,
                    'recipient_name' => $recipientName,
                    'subject' => $subject,
                    'template' => $template
                ]
            );
            
            return redirect()->to('/admin/emails')
                ->with('success', 'Email enviado com sucesso para ' . $to);
        } else {
            // Log do erro
            log_action(
                'error',
                "Falha ao enviar email para: {$to} - Erro: {$result['message']}",
                null,
                'email',
                ['error' => $result['debug'] ?? $result['message']]
            );
            
            return redirect()->back()
                ->with('error', 'Erro ao enviar email: ' . $result['message']);
        }
    }
    
    /**
     * Processar envio de email em massa
     */
    public function sendBulk()
    {
        // Verificar permissão
        if (!has_permission('emails.bulk') && !is_admin()) {
            return redirect()->back()
                ->with('error', 'Você não tem permissão para enviar emails em massa');
        }
        
        $rules = [
            'subject' => 'required',
            'template' => 'required',
            'message' => 'required',
            'recipient_type' => 'required'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        $recipientType = $this->request->getPost('recipient_type');
        $selectedIds = $this->request->getPost('selected_ids') ?? [];
        
        // Validar seleção
        if (in_array($recipientType, ['selected_students', 'selected_teachers', 'selected_guardians']) && empty($selectedIds)) {
            return redirect()->back()
                ->with('error', 'Selecione pelo menos um destinatário');
        }
        
        // Obter destinatários
        $recipients = $this->getRecipients($recipientType, $selectedIds);
        
        if (empty($recipients)) {
            return redirect()->back()
                ->with('error', 'Nenhum destinatário encontrado');
        }
        
        $template = $this->request->getPost('template');
        $subject = $this->request->getPost('subject');
        $baseMessage = $this->request->getPost('message');
        
        $buttonText = $this->request->getPost('button_text');
        $buttonUrl = $this->request->getPost('button_url');
        
        $successCount = 0;
        $failCount = 0;
        $errors = [];
        $sentEmails = [];
        
        foreach ($recipients as $recipient) {
            $data = [
                'name' => $recipient['name'],
                'content' => $baseMessage,
                'custom_message' => $baseMessage,
                'button_text' => $buttonText,
                'button_url' => $buttonUrl,
                'school_name' => setting('school_name'),
                'school_logo' => school_logo_url()
            ];
            
            $result = send_email($recipient['email'], $subject, $template, $data);
            
            if ($result['success']) {
                $successCount++;
                
                // Registrar envio
                $logId = $this->logEmail([
                    'recipient' => $recipient['email'],
                    'recipient_name' => $recipient['name'],
                    'recipient_id' => $recipient['id'],
                    'recipient_type' => $recipient['type'],
                    'subject' => $subject,
                    'template' => $template,
                    'status' => 'sent',
                    'sent_by' => session()->get('user_id')
                ]);
                
                $sentEmails[] = [
                    'log_id' => $logId,
                    'recipient' => $recipient['email'],
                    'recipient_name' => $recipient['name']
                ];
            } else {
                $failCount++;
                $errors[] = $recipient['email'] . ': ' . $result['message'];
            }
            
            // Pequena pausa para não sobrecarregar o servidor
            if ($successCount % 10 == 0) {
                sleep(1);
            }
        }
        
        // Log da ação em massa
        log_action(
            'insert',
            "Email em massa enviado para {$successCount} destinatários ({$recipientType}) - Template: {$template}",
            null,
            'email',
            [
                'recipient_type' => $recipientType,
                'total' => count($recipients),
                'success' => $successCount,
                'failed' => $failCount,
                'subject' => $subject,
                'template' => $template,
                'sent_emails' => $sentEmails
            ]
        );
        
        $message = "Emails enviados: {$successCount} com sucesso, {$failCount} falhas.";
        
        if (!empty($errors)) {
            log_message('error', 'Erros em email em massa: ' . implode('; ', $errors));
            return redirect()->to('/admin/emails')
                ->with('warning', $message . ' Verifique os logs para mais detalhes.');
        }
        
        return redirect()->to('/admin/emails')
            ->with('success', $message);
    }
    
    /**
     * Página de templates de email
     */
    public function templates()
    {
        // Verificar permissão
        if (!has_permission('emails.templates') && !is_admin()) {
            return redirect()->to('/admin/emails')
                ->with('error', 'Você não tem permissão para ver templates');
        }
        
        $data['title'] = 'Templates de Email';
        $data['templates'] = $this->getEmailTemplatesWithContent();
        
        // Log da visualização
        log_action(
            'view',
            'Visualizou a página de templates de email',
            null,
            'email'
        );
        
        return view('admin/emails/templates', $data);
    }
    
    /**
     * Visualizar template
     */
    public function previewTemplate($template)
    {
        $data = [
            'name' => 'Aluno Exemplo',
            'content' => 'Esta é uma mensagem de exemplo para visualização do template.',
            'custom_message' => 'Mensagem personalizada de exemplo.',
            'button_text' => 'Clique Aqui',
            'button_url' => site_url('/'),
            'school_name' => setting('school_name'),
            'school_logo' => school_logo_url(),
            'student_name' => 'João Silva',
            'username' => 'joao.silva',
            'password' => 'senha123',
            'invoice_number' => 'INV-2024-001',
            'amount' => '15.000,00',
            'payment_date' => date('d/m/Y'),
            'payment_method' => 'Multicaixa',
            'discipline' => 'Matemática',
            'grade' => '16 valores'
        ];
        
        return view('emails/' . $template, $data);
    }
    
    /**
     * Testar configuração de email
     */
    public function test()
    {
        // Verificar permissão
        if (!has_permission('emails.test') && !is_admin()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Você não tem permissão para testar emails'
            ]);
        }
        
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Requisição inválida']);
        }
        
        $testEmail = $this->request->getPost('test_email');
        if (!$testEmail) {
            return $this->response->setJSON(['success' => false, 'message' => 'Email de teste não fornecido']);
        }
        
        $data = [
            'name' => session()->get('name') ?? 'Teste Sistema',
            'content' => 'Este é um email de teste para verificar as configurações do servidor.',
            'school_name' => setting('school_name'),
            'school_logo' => school_logo_url()
        ];
        
        $result = send_email($testEmail, 'Teste de Configuração de Email', 'test', $data);
        
        if ($result['success']) {
            // Log do teste bem-sucedido
            log_action(
                'test',
                "Teste de email enviado com sucesso para: {$testEmail}",
                null,
                'email'
            );
        } else {
            // Log do teste falho
            log_action(
                'error',
                "Teste de email falhou para: {$testEmail} - Erro: {$result['message']}",
                null,
                'email',
                ['debug' => $result['debug'] ?? null]
            );
        }
        
        return $this->response->setJSON($result);
    }
    
    /**
     * Obter detalhes de um usuário
     */
    private function getUserDetails($userId, $userType)
    {
        switch ($userType) {
            case 'student':
                $student = $this->studentsModel
                    ->select('tbl_students.id, tbl_users.first_name, tbl_users.last_name, tbl_users.email')
                    ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
                    ->where('tbl_students.id', $userId)
                    ->first();
                
                if ($student) {
                    return [
                        'id' => $student['id'],
                        'name' => $student['first_name'] . ' ' . $student['last_name'],
                        'email' => $student['email'],
                        'type' => 'student'
                    ];
                }
                break;
                
            case 'teacher':
                $teacher = $this->teachersModel
                    ->select('tbl_teachers.id, tbl_users.first_name, tbl_users.last_name, tbl_users.email')
                    ->join('tbl_users', 'tbl_users.id = tbl_teachers.user_id')
                    ->where('tbl_teachers.id', $userId)
                    ->first();
                
                if ($teacher) {
                    return [
                        'id' => $teacher['id'],
                        'name' => $teacher['first_name'] . ' ' . $teacher['last_name'],
                        'email' => $teacher['email'],
                        'type' => 'teacher'
                    ];
                }
                break;
                
            case 'guardian':
                $guardian = $this->guardiansModel
                    ->select('id, full_name as name, email')
                    ->where('id', $userId)
                    ->first();
                
                if ($guardian) {
                    return [
                        'id' => $guardian['id'],
                        'name' => $guardian['name'],
                        'email' => $guardian['email'],
                        'type' => 'guardian'
                    ];
                }
                break;
        }
        
        return null;
    }
    
    /**
     * Obter destinatários para email em massa
     */
    private function getRecipients($type, $selectedIds = [])
    {
        $recipients = [];
        
        switch ($type) {
            case 'all_students':
                $students = $this->studentsModel
                    ->select('tbl_students.id, tbl_users.first_name, tbl_users.last_name, tbl_users.email')
                    ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
                    ->where('tbl_students.is_active', 1)
                    ->orderBy('tbl_users.first_name', 'ASC')
                    ->findAll();
                
                foreach ($students as $student) {
                    $recipients[] = [
                        'id' => $student['id'],
                        'name' => $student['first_name'] . ' ' . $student['last_name'],
                        'email' => $student['email'],
                        'type' => 'student'
                    ];
                }
                break;
                
            case 'selected_students':
                if (!empty($selectedIds)) {
                    $students = $this->studentsModel
                        ->select('tbl_students.id, tbl_users.first_name, tbl_users.last_name, tbl_users.email')
                        ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
                        ->whereIn('tbl_students.id', $selectedIds)
                        ->where('tbl_students.is_active', 1)
                        ->findAll();
                    
                    foreach ($students as $student) {
                        $recipients[] = [
                            'id' => $student['id'],
                            'name' => $student['first_name'] . ' ' . $student['last_name'],
                            'email' => $student['email'],
                            'type' => 'student'
                        ];
                    }
                }
                break;
                
            case 'all_teachers':
                $teachers = $this->teachersModel
                    ->select('tbl_teachers.id, tbl_users.first_name, tbl_users.last_name, tbl_users.email')
                    ->join('tbl_users', 'tbl_users.id = tbl_teachers.user_id')
                    ->where('tbl_teachers.is_active', 1)
                    ->orderBy('tbl_users.first_name', 'ASC')
                    ->findAll();
                
                foreach ($teachers as $teacher) {
                    $recipients[] = [
                        'id' => $teacher['id'],
                        'name' => $teacher['first_name'] . ' ' . $teacher['last_name'],
                        'email' => $teacher['email'],
                        'type' => 'teacher'
                    ];
                }
                break;
                
            case 'selected_teachers':
                if (!empty($selectedIds)) {
                    $teachers = $this->teachersModel
                        ->select('tbl_teachers.id, tbl_users.first_name, tbl_users.last_name, tbl_users.email')
                        ->join('tbl_users', 'tbl_users.id = tbl_teachers.user_id')
                        ->whereIn('tbl_teachers.id', $selectedIds)
                        ->where('tbl_teachers.is_active', 1)
                        ->findAll();
                    
                    foreach ($teachers as $teacher) {
                        $recipients[] = [
                            'id' => $teacher['id'],
                            'name' => $teacher['first_name'] . ' ' . $teacher['last_name'],
                            'email' => $teacher['email'],
                            'type' => 'teacher'
                        ];
                    }
                }
                break;
                
            case 'all_guardians':
                $guardians = $this->guardiansModel
                    ->select('id, full_name as name, email')
                    ->where('is_active', 1)
                    ->orderBy('full_name', 'ASC')
                    ->findAll();
                
                foreach ($guardians as $guardian) {
                    $recipients[] = [
                        'id' => $guardian['id'],
                        'name' => $guardian['name'],
                        'email' => $guardian['email'],
                        'type' => 'guardian'
                    ];
                }
                break;
                
            case 'selected_guardians':
                if (!empty($selectedIds)) {
                    $guardians = $this->guardiansModel
                        ->select('id, full_name as name, email')
                        ->whereIn('id', $selectedIds)
                        ->where('is_active', 1)
                        ->findAll();
                    
                    foreach ($guardians as $guardian) {
                        $recipients[] = [
                            'id' => $guardian['id'],
                            'name' => $guardian['name'],
                            'email' => $guardian['email'],
                            'type' => 'guardian'
                        ];
                    }
                }
                break;
                
            case 'class_students':
                $classId = $this->request->getPost('class_id');
                if ($classId) {
                    $enrollments = $this->enrollmentsModel
                        ->select('tbl_students.id, tbl_users.first_name, tbl_users.last_name, tbl_users.email')
                        ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
                        ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
                        ->where('tbl_enrollments.class_id', $classId)
                        ->where('tbl_enrollments.status', 'Ativo')
                        ->findAll();
                    
                    foreach ($enrollments as $enrollment) {
                        $recipients[] = [
                            'id' => $enrollment['id'],
                            'name' => $enrollment['first_name'] . ' ' . $enrollment['last_name'],
                            'email' => $enrollment['email'],
                            'type' => 'student'
                        ];
                    }
                }
                break;
        }
        
        return $recipients;
    }
    
    /**
     * Lista de templates disponíveis
     */
    private function getEmailTemplates()
    {
        return [
            'welcome' => 'Boas-vindas',
            'password_reset' => 'Recuperação de Senha',
            'payment_notification' => 'Notificação de Pagamento',
            'deadline_reminder' => 'Aviso de Prazo',
            'grade_posted' => 'Nota Lançada',
            'exam_schedule' => 'Calendário de Exames',
            'enrollment_confirmation' => 'Confirmação de Matrícula',
            'fee_reminder' => 'Lembrete de Propina',
            'test' => 'Teste de Email',
            'custom' => 'Mensagem Personalizada'
        ];
    }
    
    /**
     * Templates com conteúdo para visualização
     */
    private function getEmailTemplatesWithContent()
    {
        return [
            'welcome' => [
                'name' => 'Boas-vindas',
                'description' => 'Enviado quando um novo usuário é cadastrado no sistema',
                'variables' => ['name', 'username', 'password', 'school_name']
            ],
            'password_reset' => [
                'name' => 'Recuperação de Senha',
                'description' => 'Enviado quando um usuário solicita recuperação de senha',
                'variables' => ['name', 'token', 'school_name']
            ],
            'payment_notification' => [
                'name' => 'Notificação de Pagamento',
                'description' => 'Confirmar recebimento de pagamento',
                'variables' => ['student_name', 'invoice_number', 'amount', 'payment_date', 'payment_method']
            ],
            'deadline_reminder' => [
                'name' => 'Aviso de Prazo',
                'description' => 'Lembrar sobre prazos próximos do vencimento',
                'variables' => ['student_name', 'items']
            ],
            'grade_posted' => [
                'name' => 'Nota Lançada',
                'description' => 'Notificar aluno sobre nova nota',
                'variables' => ['student_name', 'discipline', 'grade', 'assessment_type', 'date', 'semester']
            ],
            'exam_schedule' => [
                'name' => 'Calendário de Exames',
                'description' => 'Enviar calendário de exames',
                'variables' => ['student_name', 'exam_list', 'exam_period']
            ],
            'enrollment_confirmation' => [
                'name' => 'Confirmação de Matrícula',
                'description' => 'Confirmar matrícula do aluno',
                'variables' => ['student_name', 'class_name', 'academic_year', 'enrollment_number']
            ],
            'fee_reminder' => [
                'name' => 'Lembrete de Propina',
                'description' => 'Lembrar sobre propinas em atraso',
                'variables' => ['student_name', 'fee_description', 'due_date', 'amount', 'fine_amount']
            ],
            'test' => [
                'name' => 'Teste de Email',
                'description' => 'Template para testar configurações',
                'variables' => ['name', 'school_name']
            ],
            'custom' => [
                'name' => 'Mensagem Personalizada',
                'description' => 'Template para mensagens personalizadas',
                'variables' => ['name', 'custom_message', 'button_text', 'button_url', 'school_name']
            ]
        ];
    }
    
    /**
     * Registrar envio de email
     */
    private function logEmail($data)
    {
        $logData = [
            'recipient' => $data['recipient'],
            'recipient_name' => $data['recipient_name'],
            'recipient_id' => $data['recipient_id'] ?? null,
            'recipient_type' => $data['recipient_type'] ?? null,
            'subject' => $data['subject'],
            'template' => $data['template'],
            'status' => $data['status'],
            'sent_by' => $data['sent_by'] ?? null,
            'sent_at' => date('Y-m-d H:i:s'),
            'ip_address' => $this->request->getIPAddress()
        ];
        
        return $this->emailLogModel->insert($logData);
    }
    
    /**
     * Obter emails recentes
     */
    private function getRecentEmails($limit = 10)
    {
        return $this->emailLogModel
            ->select('tbl_email_logs.*, tbl_users.first_name, tbl_users.last_name')
            ->join('tbl_users', 'tbl_users.id = tbl_email_logs.sent_by', 'left')
            ->orderBy('sent_at', 'DESC')
            ->limit($limit)
            ->findAll();
    }
}