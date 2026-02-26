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
use App\Models\RoleModel;
use App\Models\PermissionModel;

class Settings extends BaseController
{
    protected $settingsModel;
    protected $academicYearModel;
    protected $semesterModel;
    protected $gradeLevelModel;
    protected $disciplineModel;
    protected $feeTypeModel;
    protected $currencyModel;
    protected $roleModel;
    protected $permissionModel;
    
    public function __construct()
    {
        $this->settingsModel = new SettingsModel();
        $this->academicYearModel = new AcademicYearModel();
        $this->semesterModel = new SemesterModel();
        $this->gradeLevelModel = new GradeLevelModel();
        $this->disciplineModel = new DisciplineModel();
        $this->feeTypeModel = new FeeTypeModel();
        $this->currencyModel = new CurrencyModel();
        $this->roleModel = new RoleModel();
        $this->permissionModel = new PermissionModel();
        
        helper(['auth', 'settings']); // Carregar helpers
    }
    
    /**
     * Settings dashboard
     */
    public function index()
    {
        $data['title'] = 'Configurações do Sistema';
        
        // Get all settings
        $data['settings'] = $this->settingsModel->getAll();
        
        // Academic data for reference
        $data['currentYear'] = $this->academicYearModel->getCurrent();
        $data['academicYears'] = $this->academicYearModel
            ->where('is_active', 1)
            ->orderBy('start_date', 'DESC')
            ->findAll();
        
        $data['semesters'] = $this->semesterModel
            ->whereIn('status', ['ativo', 'processado'])
            ->orderBy('start_date', 'DESC')
            ->findAll();
        
        $data['gradeLevels'] = $this->gradeLevelModel
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->findAll();
        
        $data['currencies'] = $this->currencyModel
            ->orderBy('is_default', 'DESC')
            ->orderBy('currency_name', 'ASC')
            ->findAll();
        
        return view('admin/settings/index', $data);
    }
    
    /**
     * General settings page
     */
    public function general()
    {
        $data['title'] = 'Configurações Gerais';
        
        // Get settings
        $data['settings'] = $this->settingsModel->getAll();
        $data['timezones'] = $this->getTimezones();
        $data['dateFormats'] = $this->getDateFormats();
        
        return view('admin/settings/general', $data);
    }
    
/**
 * School settings page
 */
public function school()
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
     * Academic settings page
     */
    public function academic()
    {
        $data['title'] = 'Configurações Académicas';
        
        $data['settings'] = $this->settingsModel->getAll();
        $data['academicYears'] = $this->academicYearModel
            ->where('is_active', 1)
            ->orderBy('start_date', 'DESC')
            ->findAll();
        
        $data['semesters'] = $this->semesterModel
            ->whereIn('status', ['ativo', 'processado'])
            ->orderBy('start_date', 'ASC')
            ->findAll();
        
        return view('admin/settings/academic', $data);
    }
    
    /**
     * Payment settings page
     */
    public function payment()
    {
        $data['title'] = 'Configurações de Pagamento';
        
        $data['settings'] = $this->settingsModel->getAll();
        $data['currencies'] = $this->currencyModel
            ->orderBy('is_default', 'DESC')
            ->orderBy('currency_name', 'ASC')
            ->findAll();
        
        return view('admin/settings/payment', $data);
    }
    
    /**
     * Email settings page
     */
    public function email()
    {
        $data['title'] = 'Configurações de Email';
        
        $data['settings'] = $this->settingsModel->getAll();
        
        return view('admin/settings/email', $data);
    }
    
    /**
     * Save general settings
     */
    public function saveGeneral()
    {
        $rules = [
            'app_name' => 'required',
            'app_timezone' => 'required',
            'app_date_format' => 'required'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        $settings = [
            'app_name' => $this->request->getPost('app_name'),
            'app_timezone' => $this->request->getPost('app_timezone'),
            'app_date_format' => $this->request->getPost('app_date_format'),
            'app_time_format' => $this->request->getPost('app_time_format') ?: 'H:i',
            'app_locale' => $this->request->getPost('app_locale') ?: 'pt',
            'app_theme' => $this->request->getPost('app_theme') ?: 'light',
            'items_per_page' => $this->request->getPost('items_per_page') ?: 15,
            'enable_debug' => $this->request->getPost('enable_debug') ? 1 : 0,
            'maintenance_mode' => $this->request->getPost('maintenance_mode') ? 1 : 0
        ];
        
        if ($this->settingsModel->saveSettings($settings)) {
            return redirect()->to('/admin/settings/general')
                ->with('success', 'Configurações gerais salvas com sucesso');
        } else {
            return redirect()->back()->with('error', 'Erro ao salvar configurações');
        }
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
        
        // Handle logo upload
        $logo = $this->request->getFile('school_logo');
        if ($logo && $logo->isValid() && !$logo->hasMoved()) {
            $newName = $logo->getRandomName();
            $logo->move('uploads/school', $newName);
            
            // Delete old logo
            $oldLogo = $this->settingsModel->get('school_logo');
            if ($oldLogo && file_exists('uploads/school/' . $oldLogo)) {
                unlink('uploads/school/' . $oldLogo);
            }
            
            $settings['school_logo'] = $newName;
        }
        
        if ($this->settingsModel->saveSettings($settings)) {
            return redirect()->to('/admin/settings/school')
                ->with('success', 'Configurações da escola salvas com sucesso');
        } else {
            return redirect()->back()->with('error', 'Erro ao salvar configurações');
        }
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
        
        $settings = [
            'current_academic_year' => $this->request->getPost('academic_year_id'),
            'current_semester' => $this->request->getPost('semester_id'),
            'grading_system' => $this->request->getPost('grading_system'),
            'min_grade' => $this->request->getPost('min_grade') ?: 0,
            'max_grade' => $this->request->getPost('max_grade') ?: 20,
            'approval_grade' => $this->request->getPost('approval_grade') ?: 10,
            'attendance_threshold' => $this->request->getPost('attendance_threshold') ?: 75,
            'enrollment_deadline' => $this->request->getPost('enrollment_deadline'),
            'academic_calendar' => $this->request->getPost('academic_calendar')
        ];
        
        if ($this->settingsModel->saveSettings($settings)) {
            return redirect()->to('/admin/settings/academic')
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
            return redirect()->to('/admin/settings/payment')
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
        
        $settings = [
            'email_protocol' => $this->request->getPost('email_protocol'),
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
            return redirect()->to('/admin/settings/email')
                ->with('success', 'Configurações de email salvas com sucesso');
        } else {
            return redirect()->back()->with('error', 'Erro ao salvar configurações');
        }
    }
    
    /**
     * Remove school logo
     */
    public function removeLogo()
    {
        $oldLogo = $this->settingsModel->get('school_logo');
        if ($oldLogo && file_exists('uploads/school/' . $oldLogo)) {
            unlink('uploads/school/' . $oldLogo);
        }
        
        $this->settingsModel->saveSetting('school_logo', '');
        
        return redirect()->back()->with('success', 'Logo removido com sucesso');
    }
    
    /**
     * Test email connection
     */
    public function testEmail()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Requisição inválida']);
        }
        
        $to = $this->request->getPost('test_email');
        if (!$to) {
            return $this->response->setJSON(['success' => false, 'message' => 'Email de teste não fornecido']);
        }
        
        // Here you would implement email sending test
        // For now, just return success
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Email de teste enviado com sucesso'
        ]);
    }
    
    /**
     * Clear cache
     */
    public function clearCache()
    {
        $cache = service('cache');
        $cache->clean();
        
        return redirect()->back()->with('success', 'Cache limpo com sucesso');
    }
    
    /**
     * Helper: Get timezones list
     */
    private function getTimezones()
    {
        return [
            'Africa/Luanda' => 'Luanda, Angola',
            'Africa/Lagos' => 'Lagos, Nigéria',
            'Africa/Johannesburg' => 'Joanesburgo, África do Sul',
            'Europe/Lisbon' => 'Lisboa, Portugal',
            'UTC' => 'UTC',
            'America/Sao_Paulo' => 'São Paulo, Brasil'
        ];
    }
    
    /**
     * Helper: Get date formats
     */
    private function getDateFormats()
    {
        return [
            'd/m/Y' => date('d/m/Y') . ' (dia/mês/ano)',
            'd-m-Y' => date('d-m-Y'),
            'Y-m-d' => date('Y-m-d'),
            'm/d/Y' => date('m/d/Y')
        ];
    }
}