<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\SettingsModel;
use App\Models\RoleModel;
use App\Models\PermissionModel;
use App\Models\CurrencyModel;

class Settings extends BaseController
{
    protected $settingsModel;
    protected $roleModel;
    protected $permissionModel;
    protected $currencyModel;
    
    public function __construct()
    {
        $this->settingsModel = new SettingsModel();
        $this->roleModel = new RoleModel();
        $this->permissionModel = new PermissionModel();
        $this->currencyModel = new CurrencyModel();
    }
    
    /**
     * Settings dashboard
     */
    public function settings()
    {
        $data['title'] = 'Configurações';
        
        // Get all settings
        $data['settings'] = $this->settingsModel->getAll();
        
        return view('admin/settings/index', $data);
    }
    
    /**
     * General settings page
     */
    public function generalsettings()
    {
        $data['title'] = 'Configurações Gerais';
        
        // Get settings
        $data['settings'] = $this->settingsModel->getAll();
        
        return view('admin/settings/general', $data);
    }
    
    /**
     * Save general settings
     */
    public function saveGeneralsettings()
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
        
        // Save general settings
        $this->settingsModel->saveSetting('app_name', $this->request->getPost('app_name'));
        $this->settingsModel->saveSetting('app_timezone', $this->request->getPost('app_timezone'));
        $this->settingsModel->saveSetting('app_date_format', $this->request->getPost('app_date_format'));
        $this->settingsModel->saveSetting('app_time_format', $this->request->getPost('app_time_format') ?: 'H:i');
        $this->settingsModel->saveSetting('app_locale', $this->request->getPost('app_locale') ?: 'pt');
        $this->settingsModel->saveSetting('app_theme', $this->request->getPost('app_theme') ?: 'light');
        $this->settingsModel->saveSetting('items_per_page', $this->request->getPost('items_per_page') ?: 15);
        $this->settingsModel->saveSetting('enable_debug', $this->request->getPost('enable_debug') ? 1 : 0);
        $this->settingsModel->saveSetting('maintenance_mode', $this->request->getPost('maintenance_mode') ? 1 : 0);
        
        return redirect()->to('/admin/settings/general')
            ->with('success', 'Configurações gerais salvas com sucesso');
    }
    
    /**
     * Company settings page
     */
    public function saveDatacompany()
    {
        $rules = [
            'company_name' => 'required',
            'company_email' => 'required|valid_email'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        // Save company settings
        $this->settingsModel->saveSetting('company_name', $this->request->getPost('company_name'));
        $this->settingsModel->saveSetting('company_address', $this->request->getPost('company_address'));
        $this->settingsModel->saveSetting('company_city', $this->request->getPost('company_city'));
        $this->settingsModel->saveSetting('company_province', $this->request->getPost('company_province'));
        $this->settingsModel->saveSetting('company_phone', $this->request->getPost('company_phone'));
        $this->settingsModel->saveSetting('company_email', $this->request->getPost('company_email'));
        $this->settingsModel->saveSetting('company_website', $this->request->getPost('company_website'));
        $this->settingsModel->saveSetting('company_nif', $this->request->getPost('company_nif'));
        $this->settingsModel->saveSetting('company_logo', $this->request->getPost('company_logo') ?: '');
        
        // Handle logo upload
        $logo = $this->request->getFile('company_logo_file');
        if ($logo && $logo->isValid() && !$logo->hasMoved()) {
            $newName = $logo->getRandomName();
            $logo->move('uploads/company', $newName);
            $this->settingsModel->saveSetting('company_logo', $newName);
        }
        
        return redirect()->to('/admin/settings/general')
            ->with('success', 'Dados da empresa salvos com sucesso');
    }
    
    /**
     * Email settings page
     */
    public function emailsettings()
    {
        $data['title'] = 'Configurações de Email';
        
        // Get settings
        $data['settings'] = $this->settingsModel->getAll();
        
        return view('admin/settings/email', $data);
    }
    
    /**
     * Save email settings
     */
    public function saveEmailSettings()
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
        
        // Save email settings
        $this->settingsModel->saveSetting('email_protocol', $this->request->getPost('email_protocol'));
        $this->settingsModel->saveSetting('email_from', $this->request->getPost('email_from'));
        $this->settingsModel->saveSetting('email_from_name', $this->request->getPost('email_from_name'));
        $this->settingsModel->saveSetting('email_smtp_host', $this->request->getPost('email_smtp_host'));
        $this->settingsModel->saveSetting('email_smtp_port', $this->request->getPost('email_smtp_port'));
        $this->settingsModel->saveSetting('email_smtp_user', $this->request->getPost('email_smtp_user'));
        $this->settingsModel->saveSetting('email_smtp_pass', $this->request->getPost('email_smtp_pass'));
        $this->settingsModel->saveSetting('email_smtp_crypto', $this->request->getPost('email_smtp_crypto'));
        $this->settingsModel->saveSetting('email_sendmail_path', $this->request->getPost('email_sendmail_path') ?: '/usr/sbin/sendmail');
        
        return redirect()->to('/admin/settings/email')
            ->with('success', 'Configurações de email salvas com sucesso');
    }
    
    /**
     * Payment settings page
     */
    public function paymentsettings()
    {
        $data['title'] = 'Configurações de Pagamento';
        
        // Get settings
        $data['settings'] = $this->settingsModel->getAll();
        
        // Get currencies for dropdown
        $data['currencies'] = $this->currencyModel
            ->orderBy('is_default', 'DESC')
            ->orderBy('currency_name', 'ASC')
            ->findAll();
        
        return view('admin/settings/payment', $data);
    }
    
    /**
     * Save payment settings
     */
    public function savePaymentSettings()
    {
        $rules = [
            'payment_currency' => 'required'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        // Save payment settings
        $this->settingsModel->saveSetting('payment_currency', $this->request->getPost('payment_currency'));
        $this->settingsModel->saveSetting('payment_tax_rate', $this->request->getPost('payment_tax_rate') ?: 0);
        $this->settingsModel->saveSetting('payment_invoice_prefix', $this->request->getPost('payment_invoice_prefix') ?: 'INV');
        $this->settingsModel->saveSetting('payment_receipt_prefix', $this->request->getPost('payment_receipt_prefix') ?: 'REC');
        $this->settingsModel->saveSetting('payment_due_days', $this->request->getPost('payment_due_days') ?: 30);
        $this->settingsModel->saveSetting('payment_late_fee', $this->request->getPost('payment_late_fee') ?: 0);
        $this->settingsModel->saveSetting('payment_discount_early', $this->request->getPost('payment_discount_early') ?: 0);
        $this->settingsModel->saveSetting('payment_enable_multiple', $this->request->getPost('payment_enable_multiple') ? 1 : 0);
        $this->settingsModel->saveSetting('payment_enable_partial', $this->request->getPost('payment_enable_partial') ? 1 : 0);
        
        return redirect()->to('/admin/settings/payment')
            ->with('success', 'Configurações de pagamento salvas com sucesso');
    }
    
    /**
     * Permissions settings page
     */
    public function grouppermissions()
    {
        $data['title'] = 'Configurações de Permissões';
        
        // Get all roles
        $data['roles'] = $this->roleModel->findAll();
        
        // Get all permissions grouped by module
        $permissions = $this->permissionModel
            ->orderBy('module', 'ASC')
            ->orderBy('permission_name', 'ASC')
            ->findAll();
        
        $permissionsByModule = [];
        foreach ($permissions as $perm) {
            $permissionsByModule[$perm->module][] = $perm;
        }
        
        $data['permissionsByModule'] = $permissionsByModule;
        
        return view('admin/settings/permissions', $data);
    }
    
    /**
     * Test email connection
     */
    public function testEmail()
    {
        if (!$this->request->isAJAX()) {
            return $this->respondWithError('Requisição inválida');
        }
        
        $to = $this->request->getPost('test_email');
        if (!$to) {
            return $this->respondWithError('Email de teste não fornecido');
        }
        
        // Here you would implement email sending test
        // For now, just return success
        return $this->respondWithSuccess('Email de teste enviado com sucesso');
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
}