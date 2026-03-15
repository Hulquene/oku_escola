<?php
// app/Controllers/admin/AcademicYears.php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\AcademicYearModel;
use App\Models\SemesterModel;
use App\Models\EnrollmentModel;
use App\Models\ClassModel;

class AcademicYears extends BaseController
{
    protected $academicYearModel;
    protected $semesterModel;
    protected $enrollmentModel;
    protected $classModel;
    
    public function __construct()
    {
        $this->academicYearModel = new AcademicYearModel();
        $this->semesterModel = new SemesterModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->classModel = new ClassModel();
        
        helper(['auth', 'settings']);
    }
    
    /**
     * List academic years
     */
    public function index()
    {
        // Verificar permissão
        if (!has_permission('settings.academic_years') && !is_admin()) {
            return redirect()->to('/admin/dashboard')->with('error', 'Não tem permissão para aceder a esta página');
        }
        
        $data['title'] = 'Anos Letivos';
        
        // Buscar anos letivos ordenados por data de início (mais recente primeiro)
        $years = $this->academicYearModel
            ->orderBy('start_date', 'DESC')
            ->findAll();
        
        // Converter para objetos para manter compatibilidade com a view
        $data['years'] =$years;
        
        return view('admin/academic/years/index', $data);
    }
    
    /**
     * Academic year form
     */
    public function form($id = null)
    {
        $data['title'] = $id ? 'Editar Ano Letivo' : 'Novo Ano Letivo';
        
        // Verificar permissões específicas
        if (!has_permission('settings.academic_years') && !is_admin()) {
            return redirect()->to('/admin/academic/years')->with('error', 'Não tem permissão para esta ação');
        }
        
        if ($id) {
            // Buscar ano letivo para edição
            $year = $this->academicYearModel->find($id);
            if (!$year) {
                return redirect()->to('/admin/academic/years')->with('error', 'Ano letivo não encontrado');
            }
     
            $data['year'] = $year;
        } else {
            $data['year'] = null;
        }
        
        return view('admin/academic/years/form', $data);
    }
    
    /**
     * Save academic year - VERSÃO COM MENSAGENS DETALHADAS
     */
    public function save()
    {
        $id = $this->request->getPost('id');
        
        // Verificar permissão base
        if (!has_permission('settings.academic_years') && !is_admin()) {
            return redirect()->to('/admin/academic/years')->with('error', 'Não tem permissão para esta ação');
        }
        
        // Validação
        $rules = [
            'year_name' => 'required',
            'start_date' => 'required|valid_date',
            'end_date' => 'required|valid_date'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'year_name' => $this->request->getPost('year_name'),
            'start_date' => $this->request->getPost('start_date'),
            'end_date' => $this->request->getPost('end_date'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];
        
        // Validação adicional: data de fim não pode ser anterior à data de início
        if (strtotime($data['end_date']) < strtotime($data['start_date'])) {
            return redirect()->back()->withInput()
                ->with('error', 'A data de fim não pode ser anterior à data de início.');
        }
        
        $isCurrent = $this->request->getPost('is_current');
        
        if ($id) {
            // ATUALIZAÇÃO
            if ($this->academicYearModel->update($id, $data)) {
                if ($isCurrent) {
                    $this->academicYearModel->setCurrent($id);
                }
                log_message('info', 'Ano letivo atualizado: ' . $data['year_name'] . ' por usuário ID: ' . session()->get('user_id'));
                return redirect()->to('/admin/academic/years')
                    ->with('success', "Ano letivo '{$data['year_name']}' atualizado com sucesso!");
            } else {
                $errors = $this->academicYearModel->errors();
                if (!empty($errors)) {
                    return redirect()->back()->withInput()
                        ->with('errors', $errors);
                }
                return redirect()->back()->withInput()
                    ->with('error', 'Erro ao atualizar ano letivo');
            }
        } else {
            // INSERÇÃO
            $newId = $this->academicYearModel->insert($data);
            
            if ($newId) {
                if ($isCurrent) {
                    $this->academicYearModel->setCurrent($newId);
                }
                log_message('info', 'Novo ano letivo criado: ' . $data['year_name'] . ' por usuário ID: ' . session()->get('user_id'));
                return redirect()->to('/admin/academic/years')
                    ->with('success', "Ano letivo '{$data['year_name']}' criado com sucesso!");
            } else {
                $errors = $this->academicYearModel->errors();
                if (!empty($errors)) {
                    return redirect()->back()->withInput()
                        ->with('errors', $errors);
                }
                return redirect()->back()->withInput()
                    ->with('error', 'Erro ao criar ano letivo');
            }
        }
    }
    
    /**
     * Set current academic year
     */
    public function setCurrent($id)
    {
        // Verificar permissão
        if (!has_permission('settings.academic_years') && !is_admin()) {
            return redirect()->back()->with('error', 'Não tem permissão para definir o ano letivo atual');
        }
        
        $year = $this->academicYearModel->find($id);
        
        if (!$year) {
            return redirect()->back()->with('error', 'Ano letivo não encontrado.');
        }
        
        // 🔴 CORREÇÃO: Acesso como array
        $yearName = $year['year_name'];
        
        if ($this->academicYearModel->setCurrent($id)) {
            log_message('info', "Ano letivo ID {$id} ('{$yearName}') definido como atual por usuário ID: " . session()->get('user_id'));
            return redirect()->back()->with('success', "Ano letivo '{$yearName}' definido como atual.");
        } else {
            $errors = $this->academicYearModel->errors();
            if (!empty($errors)) {
                return redirect()->back()->with('error', 'Erro ao definir ano letivo: ' . implode(', ', $errors));
            }
            return redirect()->back()->with('error', 'Erro ao definir ano letivo.');
        }
    }
    
    /**
     * Delete academic year
     */
    public function delete($id)
    {
        // Verificar permissão
        if (!has_permission('settings.academic_years') && !is_admin()) {
            return redirect()->back()->with('error', 'Não tem permissão para eliminar anos letivos');
        }
        
        $year = $this->academicYearModel->find($id);
        
        if (!$year) {
            return redirect()->back()->with('error', 'Ano letivo não encontrado.');
        }
        
        // 🔴 CORREÇÃO: Acesso como array e comparação
        $currentYearId = current_academic_year();
        
        if ($year['id'] == $currentYearId) {
            return redirect()->back()->with('error', 'Não é possível eliminar o ano letivo atual. Defina outro ano como atual primeiro.');
        }
        
        // Verificar se existem matrículas associadas
        $enrollmentsCount = $this->enrollmentModel
            ->where('academic_year_id', $id)
            ->countAllResults();
        
        if ($enrollmentsCount > 0) {
            return redirect()->back()->with('error', "Não é possível eliminar este ano letivo porque existem {$enrollmentsCount} matrículas associadas a ele.");
        }
        
        // Verificar se existem semestres associados
        $semestersCount = $this->semesterModel
            ->where('academic_year_id', $id)
            ->countAllResults();
        
        if ($semestersCount > 0) {
            return redirect()->back()->with('error', "Não é possível eliminar este ano letivo porque existem {$semestersCount} semestres associados a ele. Elimine os semestres primeiro.");
        }
        
        // Verificar se existem turmas associadas
        $classesCount = $this->classModel
            ->where('academic_year_id', $id)
            ->countAllResults();
        
        if ($classesCount > 0) {
            return redirect()->back()->with('error', "Não é possível eliminar este ano letivo porque existem {$classesCount} turmas associadas a ele. Elimine as turmas primeiro.");
        }
        
        if ($this->academicYearModel->delete($id)) {
            log_message('info', "Ano letivo ID {$id} ('{$year['year_name']}') eliminado com sucesso por usuário ID: " . session()->get('user_id'));
            return redirect()->to('/admin/academic/years')->with('success', "Ano letivo '{$year['year_name']}' eliminado com sucesso.");
        } else {
            $errors = $this->academicYearModel->errors();
            if (!empty($errors)) {
                return redirect()->back()->with('error', 'Erro ao eliminar ano letivo: ' . implode(', ', $errors));
            }
            return redirect()->back()->with('error', 'Erro ao eliminar ano letivo.');
        }
    }
    
    /**
     * Check if dates are within academic year
     */
    public function checkDates($id)
    {
        $year = $this->academicYearModel->find($id);
        
        if (!$year) {
            return $this->response->setJSON(['valid' => false, 'message' => 'Ano letivo não encontrado']);
        }
        
        $startDate = $this->request->getGet('start');
        $endDate = $this->request->getGet('end');
        
        if (!$startDate || !$endDate) {
            return $this->response->setJSON(['valid' => true]);
        }
        
        $valid = true;
        $message = '';
        
        // 🔴 CORREÇÃO: Acesso como array
        if ($startDate < $year['start_date']) {
            $valid = false;
            $message = 'A data de início não pode ser anterior ao início do ano letivo (' . date('d/m/Y', strtotime($year['start_date'])) . ')';
        } elseif ($endDate > $year['end_date']) {
            $valid = false;
            $message = 'A data de fim não pode ser posterior ao fim do ano letivo (' . date('d/m/Y', strtotime($year['end_date'])) . ')';
        }
        
        return $this->response->setJSON([
            'valid' => $valid,
            'message' => $message
        ]);
    }
    
    /**
     * Toggle active status
     */
    public function toggleActive($id)
    {
        // Verificar permissão
        if (!has_permission('settings.academic_years') && !is_admin()) {
            return redirect()->back()->with('error', 'Não tem permissão para alterar o status do ano letivo');
        }
        
        $year = $this->academicYearModel->find($id);
        
        if (!$year) {
            return redirect()->back()->with('error', 'Ano letivo não encontrado.');
        }
        
        // 🔴 CORREÇÃO: Acesso como array
        $newStatus = $year['is_active'] ? 0 : 1;
        
        if ($this->academicYearModel->update($id, ['is_active' => $newStatus])) {
            $statusText = $newStatus ? 'ativado' : 'desativado';
            log_message('info', "Ano letivo ID {$id} ('{$year['year_name']}') {$statusText} por usuário ID: " . session()->get('user_id'));
            return redirect()->back()->with('success', "Ano letivo '{$year['year_name']}' {$statusText} com sucesso.");
        } else {
            return redirect()->back()->with('error', 'Erro ao alterar status do ano letivo.');
        }
    }
    
    /**
     * View academic year details
     */
    public function view($id = null)
    {
        // Verificar permissão
        if (!has_permission('settings.academic_years') && !is_admin()) {
            return redirect()->to('/admin/dashboard')->with('error', 'Não tem permissão para aceder a esta página');
        }
        
        if (!$id) {
            return redirect()->to('/admin/academic/years')->with('error', 'Ano letivo não especificado');
        }
        
        $year = $this->academicYearModel->find($id);
        
        if (!$year) {
            return redirect()->to('/admin/academic/years')->with('error', 'Ano letivo não encontrado');
        }
        
        // Converter para objeto para manter compatibilidade com a view
        $data['year'] = $year;
        
        // Buscar semestres associados
        $semesters = $this->semesterModel
            ->where('academic_year_id', $id)
            ->orderBy('start_date', 'ASC')
            ->findAll();
        
        // Converter semestres para objetos
        $data['semesters'] = array_map(function($semester) {
            return (object)$semester;
        }, $semesters);
        
        // Buscar estatísticas de matrículas
        $data['total_enrollments'] = $this->enrollmentModel
            ->where('academic_year_id', $id)
            ->countAllResults();
        
        $data['active_enrollments'] = $this->enrollmentModel
            ->where('academic_year_id', $id)
            ->where('status', 'Ativo')
            ->countAllResults();
        
        // Buscar turmas do ano letivo
        $data['classes'] = $this->classModel
            ->select('tbl_classes.*, COUNT(tbl_enrollments.id) as student_count')
            ->join('tbl_enrollments', 'tbl_enrollments.class_id = tbl_classes.id AND tbl_enrollments.academic_year_id = ' . $id, 'left')
            ->where('tbl_classes.academic_year_id', $id)
            ->groupBy('tbl_classes.id')
            ->orderBy('tbl_classes.class_name', 'ASC')
            ->findAll();
                
        $data['title'] = 'Detalhes do Ano Letivo: ' . $year['year_name'];
        
        return view('admin/academic/years/view', $data);
    }
}