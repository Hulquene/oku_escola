<?php
// app/Controllers/admin/Settings.php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\SettingsModel;
use App\Models\AcademicYearModel;
use App\Models\SemesterModel;
use App\Models\GradeLevelModel;
use App\Models\DisciplineModel;
use App\Models\FeeTypeModel;
use App\Models\CurrencyModel;

class Settings extends BaseController
{
    protected $settingsModel;
    protected $academicYearModel;
    protected $semesterModel;
    protected $gradeLevelModel;
    protected $disciplineModel;
    protected $feeTypeModel;
    protected $currencyModel;
    
    public function __construct()
    {
        $this->settingsModel = new SettingsModel();
        $this->academicYearModel = new AcademicYearModel();
        $this->semesterModel = new SemesterModel();
        $this->gradeLevelModel = new GradeLevelModel();
        $this->disciplineModel = new DisciplineModel();
        $this->feeTypeModel = new FeeTypeModel();
        $this->currencyModel = new CurrencyModel();
        
        helper(['auth', 'settings', 'upload']); // Adicionar helpers necessários
    }

    /**
     * School settings page (main settings page)
     */
    public function index()
    {
        $data['title'] = 'Configurações da Escola';
        
        // Get school settings
        $data['settings'] = $this->settingsModel->getAll();
        
        // Dados para as estatísticas e selects
        $data['gradeLevels'] = $this->gradeLevelModel
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->findAll();
        
        $data['disciplines'] = $this->disciplineModel
            ->where('is_active', 1)
            ->orderBy('discipline_name', 'ASC')
            ->findAll(10);
        
        $data['feeTypes'] = $this->feeTypeModel
            ->where('is_active', 1)
            ->orderBy('type_category', 'ASC')
            ->findAll();
        
        $data['currencies'] = $this->currencyModel
            ->orderBy('is_default', 'DESC')
            ->orderBy('currency_name', 'ASC')
            ->findAll();
        
        // Dados acadêmicos
        $data['academicYears'] = $this->academicYearModel
            ->where('is_active', 1)
            ->orderBy('start_date', 'DESC')
            ->findAll();
        
        $data['semesters'] = $this->semesterModel
            ->whereIn('status', ['ativo', 'processado'])
            ->orderBy('start_date', 'ASC')
            ->findAll();
        
        return view('admin/settings/school', $data);
    }
    
    /**
     * Save school settings
     */
    public function saveSchool()
    {
        $rules = [
            'school_name' => 'required',
            'school_email' => 'required|valid_email',
            'school_phone' => 'required'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        $settings = [
            'school_name' => $this->request->getPost('school_name'),
            'school_acronym' => $this->request->getPost('school_acronym'),
            'school_address' => $this->request->getPost('school_address'),
            'school_city' => $this->request->getPost('school_city'),
            'school_province' => $this->request->getPost('school_province'),
            'school_phone' => $this->request->getPost('school_phone'),
            'school_alt_phone' => $this->request->getPost('school_alt_phone'),
            'school_email' => $this->request->getPost('school_email'),
            'school_website' => $this->request->getPost('school_website'),
            'school_nif' => $this->request->getPost('school_nif'),
            'school_founding_year' => $this->request->getPost('school_founding_year')
        ];
        
        if ($this->settingsModel->saveSettings($settings)) {
            return redirect()->to('/admin/settings')
                ->with('success', 'Configurações da escola salvas com sucesso');
        } else {
            return redirect()->back()->with('error', 'Erro ao salvar configurações');
        }
    }
    
    /**
     * Save branding settings (logos and favicon)
     */
    public function saveBranding()
    {
        $settings = [];
        $uploadErrors = [];
        
        // Handle logo upload
        $logo = $this->request->getFile('school_logo');
        if ($logo && $logo->isValid() && !$logo->hasMoved()) {
            $uploadResult = upload_media($logo, 'school', [
                'allowed_types' => ['jpg', 'jpeg', 'png', 'svg', 'gif', 'webp'],
                'max_size' => 3072, // 3MB
                'width' => 512,
                'height' => 512,
                'create_thumb' => false,
                'quality' => 90
            ]);
            
            if ($uploadResult['success']) {
                // Delete old logo
                $oldLogo = $this->settingsModel->get('school_logo');
                if ($oldLogo) {
                    delete_media('school/' . $oldLogo);
                }
                $settings['school_logo'] = $uploadResult['file_name'];
            } else {
                $uploadErrors[] = 'Logo principal: ' . $uploadResult['error'];
            }
        }
        
        // Handle dark logo upload
        $logoDark = $this->request->getFile('school_logo_dark');
        if ($logoDark && $logoDark->isValid() && !$logoDark->hasMoved()) {
            $uploadResult = upload_media($logoDark, 'school', [
                'allowed_types' => ['jpg', 'jpeg', 'png', 'svg', 'gif', 'webp'],
                'max_size' => 3072,
                'width' => 512,
                'height' => 512,
                'create_thumb' => false,
                'quality' => 90
            ]);
            
            if ($uploadResult['success']) {
                // Delete old dark logo
                $oldLogoDark = $this->settingsModel->get('school_logo_dark');
                if ($oldLogoDark) {
                    delete_media('school/' . $oldLogoDark);
                }
                $settings['school_logo_dark'] = $uploadResult['file_name'];
            } else {
                $uploadErrors[] = 'Logo dark: ' . $uploadResult['error'];
            }
        }
        
        // Handle favicon upload
        $favicon = $this->request->getFile('school_favicon');
        if ($favicon && $favicon->isValid() && !$favicon->hasMoved()) {
            $uploadResult = upload_media($favicon, 'school', [
                'allowed_types' => ['ico', 'png', 'jpg', 'jpeg', 'svg'],
                'max_size' => 1024, // 1MB
                'width' => 64,
                'height' => 64,
                'min_width' => 16,
                'min_height' => 16,
                'create_thumb' => false,
                'is_favicon' => true
            ]);
            
            if ($uploadResult['success']) {
                // Delete old favicon
                $oldFavicon = $this->settingsModel->get('school_favicon');
                if ($oldFavicon) {
                    delete_media('school/' . $oldFavicon);
                }
                $settings['school_favicon'] = $uploadResult['file_name'];
            } else {
                $uploadErrors[] = 'Favicon: ' . $uploadResult['error'];
            }
        }
        
        // Save color settings
        $settings['branding_primary_color'] = $this->request->getPost('branding_primary_color') ?: '#1B2B4B';
        $settings['branding_accent_color'] = $this->request->getPost('branding_accent_color') ?: '#3B7FE8';
        
        if (!empty($settings) && $this->settingsModel->saveSettings($settings)) {
            // Clear logo cache
            cache()->delete('school_logo');
            cache()->delete('school_logo_dark');
            cache()->delete('school_favicon');
            
            $message = 'Identidade visual salva com sucesso';
            if (!empty($uploadErrors)) {
                $message .= ' | Avisos: ' . implode('; ', $uploadErrors);
                return redirect()->to('/admin/settings')
                    ->with('warning', $message);
            }
            
            return redirect()->to('/admin/settings')
                ->with('success', $message);
        }
        
        if (!empty($uploadErrors)) {
            return redirect()->back()->withInput()
                ->with('error', 'Erros no upload: ' . implode('; ', $uploadErrors));
        }
        
        return redirect()->back()->with('warning', 'Nenhuma alteração foi feita');
    }
    
    /**
     * Save management settings (directors)
     */
    public function saveManagement()
    {
        $settings = [
            'director_name' => $this->request->getPost('director_name'),
            'director_title' => $this->request->getPost('director_title'),
            'director_degree' => $this->request->getPost('director_degree'),
            'pedagogical_director_name' => $this->request->getPost('pedagogical_director_name'),
            'pedagogical_title' => $this->request->getPost('pedagogical_title'),
            'pedagogical_degree' => $this->request->getPost('pedagogical_degree'),
            'document_city' => $this->request->getPost('document_city'),
            'signature_title' => $this->request->getPost('signature_title')
        ];
        
        if ($this->settingsModel->saveSettings($settings)) {
            return redirect()->to('/admin/settings')
                ->with('success', 'Informações de gestão salvas com sucesso');
        }
        
        return redirect()->back()->with('error', 'Erro ao salvar informações de gestão');
    }
    
    /**
     * Save academic settings
     */
    public function saveAcademic()
    {
        $rules = [
            'academic_year_id' => 'required|numeric',
            'semester_id' => 'required|numeric',
            'grading_system' => 'required'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        $academicYearId = $this->request->getPost('academic_year_id');
        $semesterId = $this->request->getPost('semester_id');
        
        $settings = [
            'current_academic_year' => $academicYearId,
            'current_semester' => $semesterId,
            'grading_system' => $this->request->getPost('grading_system'),
            'min_grade' => $this->request->getPost('min_grade') ?: 0,
            'max_grade' => $this->request->getPost('max_grade') ?: 20,
            'approval_grade' => $this->request->getPost('approval_grade') ?: 10,
            'attendance_threshold' => $this->request->getPost('attendance_threshold') ?: 75,
            'enrollment_deadline' => $this->request->getPost('enrollment_deadline'),
            'academic_calendar' => $this->request->getPost('academic_calendar')
        ];
        
        if ($this->settingsModel->saveSettings($settings)) {
            
            // Buscar o nome do ano letivo para guardar na sessão
            $academicYear = $this->academicYearModel->find($academicYearId);
            
            // Guardar na sessão
            session()->set([
                'academic_year_id' => $academicYearId,
                'academic_year_name' => $academicYear ? $academicYear['year_name'] : null,
                'semester_id' => $semesterId
            ]);
            
            // Limpar caches
            cache()->delete('school_logo');
            cache()->delete('current_academic_year');
            
            return redirect()->to('/admin/settings')
                ->with('success', 'Configurações académicas salvas com sucesso');
        } else {
            return redirect()->back()->with('error', 'Erro ao salvar configurações');
        }
    }
    
    /**
     * Save payment settings
     */
    public function savePayment()
    {
        $rules = [
            'default_currency' => 'required|numeric'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        $settings = [
            'default_currency' => $this->request->getPost('default_currency'),
            'payment_tax_rate' => $this->request->getPost('payment_tax_rate') ?: 0,
            'payment_invoice_prefix' => $this->request->getPost('payment_invoice_prefix') ?: 'INV',
            'payment_receipt_prefix' => $this->request->getPost('payment_receipt_prefix') ?: 'REC',
            'payment_due_days' => $this->request->getPost('payment_due_days') ?: 30,
            'payment_late_fee' => $this->request->getPost('payment_late_fee') ?: 0,
            'payment_discount_early' => $this->request->getPost('payment_discount_early') ?: 0,
            'payment_enable_multiple' => $this->request->getPost('payment_enable_multiple') ? 1 : 0,
            'payment_enable_partial' => $this->request->getPost('payment_enable_partial') ? 1 : 0,
            'payment_enable_fines' => $this->request->getPost('payment_enable_fines') ? 1 : 0,
            'late_fee_percentage' => $this->request->getPost('late_fee_percentage') ?: 0,
            'discount_early_payment' => $this->request->getPost('discount_early_payment') ?: 0,
            'payment_deadline_days' => $this->request->getPost('payment_deadline_days') ?: 30
        ];
        
        if ($this->settingsModel->saveSettings($settings)) {
            return redirect()->to('/admin/settings')
                ->with('success', 'Configurações de pagamento salvas com sucesso');
        } else {
            return redirect()->back()->with('error', 'Erro ao salvar configurações');
        }
    }
    
    /**
     * Save email settings
     */
    public function saveEmail()
    {
        $rules = [
            'email_protocol' => 'required',
            'email_from' => 'required|valid_email',
            'email_from_name' => 'required'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        // Se for SMTP, validar campos obrigatórios
        $protocol = $this->request->getPost('email_protocol');
        if ($protocol === 'smtp') {
            $rules = [
                'email_smtp_host' => 'required',
                'email_smtp_port' => 'required|numeric',
                'email_smtp_user' => 'required',
                'email_smtp_pass' => 'required'
            ];
            
            if (!$this->validate($rules)) {
                return redirect()->back()->withInput()
                    ->with('errors', $this->validator->getErrors());
            }
        }
        
        $settings = [
            'email_protocol' => $protocol,
            'email_from' => $this->request->getPost('email_from'),
            'email_from_name' => $this->request->getPost('email_from_name'),
            'email_smtp_host' => $this->request->getPost('email_smtp_host'),
            'email_smtp_port' => $this->request->getPost('email_smtp_port'),
            'email_smtp_user' => $this->request->getPost('email_smtp_user'),
            'email_smtp_pass' => $this->request->getPost('email_smtp_pass'),
            'email_smtp_crypto' => $this->request->getPost('email_smtp_crypto'),
            'email_sendmail_path' => $this->request->getPost('email_sendmail_path') ?: '/usr/sbin/sendmail'
        ];
        
        if ($this->settingsModel->saveSettings($settings)) {
            return redirect()->to('/admin/settings')
                ->with('success', 'Configurações de email salvas com sucesso');
        } else {
            return redirect()->back()->with('error', 'Erro ao salvar configurações de email');
        }
    }
    
    /**
     * Test email configuration
     */
    public function testEmail()
    {
        // Verificar se é uma requisição AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Requisição inválida'
            ]);
        }
        
        $testEmail = $this->request->getPost('test_email');
        if (!$testEmail) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Email de teste não fornecido'
            ]);
        }
        
        // Carregar configurações atuais
        $settings = $this->settingsModel->getAll();
        
        // Configurar email
        $email = \Config\Services::email();
        
        // Configurar protocolo
        switch ($settings['email_protocol'] ?? 'smtp') {
            case 'smtp':
                $config['protocol'] = 'smtp';
                $config['SMTPHost'] = $settings['email_smtp_host'] ?? '';
                $config['SMTPPort'] = $settings['email_smtp_port'] ?? 587;
                $config['SMTPUser'] = $settings['email_smtp_user'] ?? '';
                $config['SMTPPass'] = $settings['email_smtp_pass'] ?? '';
                $config['SMTPCrypto'] = $settings['email_smtp_crypto'] ?? 'tls';
                break;
                
            case 'sendmail':
                $config['protocol'] = 'sendmail';
                $config['mailPath'] = $settings['email_sendmail_path'] ?? '/usr/sbin/sendmail';
                break;
                
            default:
                $config['protocol'] = 'mail';
                break;
        }
        
        // Configurações comuns
        $config['mailType'] = 'html';
        $config['charset'] = 'utf-8';
        $config['wordWrap'] = true;
        $config['validate'] = true;
        
        $email->initialize($config);
        
        // Configurar remetente e destinatário
        $email->setFrom($settings['email_from'] ?? 'noreply@escola.ao', $settings['email_from_name'] ?? 'Sistema Escolar');
        $email->setTo($testEmail);
        
        // Assunto e mensagem
        $email->setSubject('Teste de Configuração de Email - Sistema Escolar');
        $email->setMessage('
            <!DOCTYPE html>
            <html>
            <head>
                <title>Teste de Email</title>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background: #1B2B4B; color: white; padding: 10px; text-align: center; }
                    .content { padding: 20px; background: #f9f9f9; }
                    .footer { text-align: center; padding: 10px; font-size: 12px; color: #666; }
                    .success { color: #16A87D; font-weight: bold; }
                </style>
            </head>
            <body>
                <div class="container">
                    <div class="header">
                        <h2>Teste de Configuração de Email</h2>
                    </div>
                    <div class="content">
                        <p>Olá,</p>
                        <p>Este é um email de teste enviado pelo Sistema de Gestão Escolar.</p>
                        <p>Se você está recebendo este email, significa que as configurações de email estão funcionando corretamente!</p>
                        <p class="success">✓ Configuração testada com sucesso</p>
                        <p><strong>Informações do teste:</strong></p>
                        <ul>
                            <li>Data: ' . date('d/m/Y H:i:s') . '</li>
                            <li>Protocolo: ' . strtoupper($settings['email_protocol'] ?? 'smtp') . '</li>
                            <li>Servidor: ' . ($settings['email_smtp_host'] ?? 'N/A') . '</li>
                        </ul>
                    </div>
                    <div class="footer">
                        <p>Este é um email automático, por favor não responda.</p>
                        <p>&copy; ' . date('Y') . ' ' . ($settings['school_name'] ?? 'Sistema Escolar') . '</p>
                    </div>
                </div>
            </body>
            </html>
        ');
        
        // Enviar email
        if ($email->send()) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Email de teste enviado com sucesso para ' . $testEmail
            ]);
        } else {
            // Obter erro detalhado
            $error = $email->printDebugger(['headers']);
            if (is_array($error)) {
                $error = implode(' ', $error);
            }
            
            log_message('error', 'Falha no teste de email: ' . $error);
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Falha ao enviar email de teste. Verifique as configurações e tente novamente.'
            ]);
        }
    }
    
    /**
     * Remove school logo by type
     * 
     * @param string $type Logo type: logo, dark, favicon
     */
    public function removeLogo($type = 'logo')
    {
        $key = '';
        $displayName = '';
        
        switch ($type) {
            case 'dark':
                $key = 'school_logo_dark';
                $displayName = 'Logo dark';
                break;
            case 'favicon':
                $key = 'school_favicon';
                $displayName = 'Favicon';
                break;
            default:
                $key = 'school_logo';
                $displayName = 'Logo principal';
                break;
        }
        
        $oldFile = $this->settingsModel->get($key);
        if ($oldFile) {
            delete_media('school/' . $oldFile);
        }
        
        $this->settingsModel->saveSetting($key, '');
        
        // Clear cache
        cache()->delete($key);
        
        return redirect()->back()->with('success', $displayName . ' removido com sucesso');
    }
    
    /**
     * Clear system cache
     */
    public function clearCache()
    {
        $cache = service('cache');
        $cache->clean();
        
        // Clear specific setting caches
        $cache->delete('school_logo');
        $cache->delete('school_logo_dark');
        $cache->delete('school_favicon');
        $cache->delete('current_academic_year');
        
        return redirect()->back()->with('success', 'Cache limpo com sucesso');
    }
    
    /**
     * Backup database (simplified)
     */
    public function backup()
    {
        // This would be implemented with actual DB backup logic
        // For now, just show a message
        return redirect()->back()->with('info', 'Funcionalidade de backup em desenvolvimento');
    }
}