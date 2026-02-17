<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\SettingsModel;
use App\Models\AcademicYearModel;
use App\Models\SemesterModel;
use App\Models\GradeLevelModel;
use App\Models\DisciplineModel;
use App\Models\FeeTypeModel;
use App\Models\CurrencyModel;

class SchoolSettings extends BaseController
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
    }
    
    /**
     * School settings dashboard
     */
    public function index()
    {
        $data['title'] = 'Configurações da Escola';
        
        // Get all settings
        $data['settings'] = $this->settingsModel->getAll();
        
        // Get academic data for reference
        $data['currentYear'] = $this->academicYearModel->getCurrent();
        $data['academicYears'] = $this->academicYearModel
            ->where('is_active', 1)
            ->orderBy('start_date', 'DESC')
            ->findAll(5);
        
        $data['semesters'] = $this->semesterModel
            ->where('is_active', 1)
            ->orderBy('start_date', 'DESC')
            ->findAll(5);
        
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
        
        return view('admin/school-settings/index', $data);
    }
    
    /**
     * Save general settings
     */
    public function saveGeneral()
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
        
        // Save general settings
        $this->settingsModel->saveSetting('school_name', $this->request->getPost('school_name'));
        $this->settingsModel->saveSetting('school_acronym', $this->request->getPost('school_acronym'));
        $this->settingsModel->saveSetting('school_address', $this->request->getPost('school_address'));
        $this->settingsModel->saveSetting('school_city', $this->request->getPost('school_city'));
        $this->settingsModel->saveSetting('school_province', $this->request->getPost('school_province'));
        $this->settingsModel->saveSetting('school_phone', $this->request->getPost('school_phone'));
        $this->settingsModel->saveSetting('school_alt_phone', $this->request->getPost('school_alt_phone'));
        $this->settingsModel->saveSetting('school_email', $this->request->getPost('school_email'));
        $this->settingsModel->saveSetting('school_website', $this->request->getPost('school_website'));
        $this->settingsModel->saveSetting('school_nif', $this->request->getPost('school_nif'));
        $this->settingsModel->saveSetting('school_founding_year', $this->request->getPost('school_founding_year'));
        
        // Handle logo upload
        $logo = $this->request->getFile('school_logo');
        if ($logo && $logo->isValid() && !$logo->hasMoved()) {
            $newName = $logo->getRandomName();
            $logo->move('uploads/school', $newName);
            $this->settingsModel->saveSetting('school_logo', $newName);
        }
        
        return redirect()->to('/admin/school-settings')
            ->with('success', 'Configurações gerais salvas com sucesso');
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
        
        // Save academic settings
        $this->settingsModel->saveSetting('current_academic_year', $this->request->getPost('academic_year_id'));
        $this->settingsModel->saveSetting('current_semester', $this->request->getPost('semester_id'));
        $this->settingsModel->saveSetting('grading_system', $this->request->getPost('grading_system'));
        $this->settingsModel->saveSetting('min_grade', $this->request->getPost('min_grade') ?: 0);
        $this->settingsModel->saveSetting('max_grade', $this->request->getPost('max_grade') ?: 20);
        $this->settingsModel->saveSetting('approval_grade', $this->request->getPost('approval_grade') ?: 10);
        $this->settingsModel->saveSetting('attendance_threshold', $this->request->getPost('attendance_threshold') ?: 75);
        $this->settingsModel->saveSetting('enrollment_deadline', $this->request->getPost('enrollment_deadline'));
        $this->settingsModel->saveSetting('academic_calendar', $this->request->getPost('academic_calendar'));
        
        return redirect()->to('/admin/school-settings')
            ->with('success', 'Configurações académicas salvas com sucesso');
    }
    
    /**
     * Save fees settings
     */
    public function saveFees()
    {
        $rules = [
            'default_currency' => 'required|numeric'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        // Save fees settings
        $this->settingsModel->saveSetting('default_currency', $this->request->getPost('default_currency'));
        $this->settingsModel->saveSetting('late_fee_percentage', $this->request->getPost('late_fee_percentage') ?: 0);
        $this->settingsModel->saveSetting('discount_early_payment', $this->request->getPost('discount_early_payment') ?: 0);
        $this->settingsModel->saveSetting('payment_deadline_days', $this->request->getPost('payment_deadline_days') ?: 30);
        $this->settingsModel->saveSetting('invoice_prefix', $this->request->getPost('invoice_prefix') ?: 'INV');
        $this->settingsModel->saveSetting('receipt_prefix', $this->request->getPost('receipt_prefix') ?: 'REC');
        $this->settingsModel->saveSetting('enable_multiple_payments', $this->request->getPost('enable_multiple_payments') ? 1 : 0);
        $this->settingsModel->saveSetting('enable_partial_payments', $this->request->getPost('enable_partial_payments') ? 1 : 0);
        $this->settingsModel->saveSetting('enable_fines', $this->request->getPost('enable_fines') ? 1 : 0);
        
        return redirect()->to('/admin/school-settings')
            ->with('success', 'Configurações de propinas salvas com sucesso');
    }
    
    /**
     * Upload school logo (AJAX)
     */
    public function uploadLogo()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Requisição inválida']);
        }
        
        $logo = $this->request->getFile('logo');
        
        if (!$logo || !$logo->isValid()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Arquivo inválido']);
        }
        
        if ($logo->getSize() > 2048 * 1024) { // 2MB
            return $this->response->setJSON(['success' => false, 'message' => 'Arquivo muito grande. Máximo 2MB']);
        }
        
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        if (!in_array($logo->getMimeType(), $allowedTypes)) {
            return $this->response->setJSON(['success' => false, 'message' => 'Tipo de arquivo não permitido. Use JPG, PNG, GIF ou WEBP']);
        }
        
        $newName = $logo->getRandomName();
        $logo->move('uploads/school', $newName);
        
        // Delete old logo if exists
        $oldLogo = $this->settingsModel->get('school_logo');
        if ($oldLogo && file_exists('uploads/school/' . $oldLogo)) {
            unlink('uploads/school/' . $oldLogo);
        }
        
        $this->settingsModel->saveSetting('school_logo', $newName);
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Logo atualizado com sucesso',
            'filename' => $newName,
            'url' => base_url('uploads/school/' . $newName)
        ]);
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
     * Reset to default settings
     */
    public function resetDefaults()
    {
        // This would reset to installation defaults
        // Be careful with this method - maybe require confirmation
        
        return redirect()->back()->with('warning', 'Função não implementada por segurança');
    }
}