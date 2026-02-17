<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\SemesterModel;
use App\Models\AcademicYearModel;
use App\Models\ExamModel;

class Semesters extends BaseController
{
    protected $semesterModel;
    protected $academicYearModel;
    protected $examModel;
    
    public function __construct()
    {
        $this->semesterModel = new SemesterModel();
        $this->academicYearModel = new AcademicYearModel();
        $this->examModel = new ExamModel();
        
        // Carregar helper de log (certifique-se que o arquivo existe)
        helper('log');
    }
    
    /**
     * List semesters
     */
    public function index()
    {
        $data['title'] = 'Semestres/Trimestres';
        
        // Filters
        $academicYearId = $this->request->getGet('academic_year');
        $status = $this->request->getGet('status');
        
        $builder = $this->semesterModel
            ->select('tbl_semesters.*, tbl_academic_years.year_name')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_semesters.academic_year_id');
        
        if ($academicYearId) {
            $builder->where('tbl_semesters.academic_year_id', $academicYearId);
        }
        
        if ($status == 'active') {
            $builder->where('tbl_semesters.is_active', 1);
        } elseif ($status == 'inactive') {
            $builder->where('tbl_semesters.is_active', 0);
        }
        
        $data['semesters'] = $builder->orderBy('tbl_academic_years.start_date', 'DESC')
            ->orderBy('tbl_semesters.start_date', 'ASC')
            ->findAll();
        
        $data['academicYears'] = $this->academicYearModel->where('is_active', 1)->findAll();
        $data['selectedYear'] = $academicYearId;
        $data['selectedStatus'] = $status;
        
        return view('admin/academic/semesters/index', $data);
    }
    
    /**
     * Semester form
     */
    public function form($id = null)
    {
        $data['title'] = $id ? 'Editar Semestre/Trimestre' : 'Novo Semestre/Trimestre';
        $data['semester'] = $id ? $this->semesterModel->find($id) : null;
        
        $data['academicYears'] = $this->academicYearModel->where('is_active', 1)->findAll();
        
        return view('admin/academic/semesters/form', $data);
    }
    
    /**
     * Save semester - VERSÃO CORRIGIDA COM LOGS
     */
    public function save()
    {
        $id = $this->request->getPost('id');
        
        // Regras de validação no controller (incluindo id)
        $rules = [
            'id' => 'permit_empty|is_natural_no_zero',
            'academic_year_id' => 'required|numeric',
            'semester_name' => 'required|min_length[3]|max_length[100]',
            'semester_type' => 'required|in_list[1º Trimestre,2º Trimestre,3º Trimestre,1º Semestre,2º Semestre]',
            'start_date' => 'required|valid_date',
            'end_date' => 'required|valid_date'
        ];
        
        $messages = [
            'academic_year_id' => [
                'required' => 'O ano letivo é obrigatório.',
                'numeric' => 'O ano letivo selecionado é inválido.'
            ],
            'semester_name' => [
                'required' => 'O nome do período é obrigatório.',
                'min_length' => 'O nome do período deve ter pelo menos 3 caracteres.',
                'max_length' => 'O nome do período não pode ter mais de 100 caracteres.'
            ],
            'semester_type' => [
                'required' => 'O tipo de período é obrigatório.',
                'in_list' => 'O tipo de período selecionado é inválido.'
            ],
            'start_date' => [
                'required' => 'A data de início é obrigatória.',
                'valid_date' => 'A data de início não é válida.'
            ],
            'end_date' => [
                'required' => 'A data de fim é obrigatória.',
                'valid_date' => 'A data de fim não é válida.'
            ]
        ];
        
        if (!$this->validate($rules, $messages)) {
            log_message('error', 'Erro de validação: ' . json_encode($this->validator->getErrors()));
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        $academicYearId = $this->request->getPost('academic_year_id');
        $semesterName = $this->request->getPost('semester_name');
        $semesterType = $this->request->getPost('semester_type');
        $startDate = $this->request->getPost('start_date');
        $endDate = $this->request->getPost('end_date');
        $isActive = $this->request->getPost('is_active') ? 1 : 0;
        $isCurrent = $this->request->getPost('is_current');
        
        // Validar ordem das datas
        if (strtotime($endDate) < strtotime($startDate)) {
            log_message('warning', "Tentativa de salvar período com data fim anterior à data início");
            return redirect()->back()->withInput()
                ->with('error', 'A data de fim não pode ser anterior à data de início.');
        }
        
        // Verificar se as datas estão dentro do ano letivo
        $academicYear = $this->academicYearModel->find($academicYearId);
        if (!$academicYear) {
            log_message('error', "Ano letivo ID {$academicYearId} não encontrado");
            return redirect()->back()->withInput()
                ->with('error', 'Ano letivo não encontrado.');
        }
        
        if ($startDate < $academicYear->start_date || $endDate > $academicYear->end_date) {
            log_message('warning', "Período fora do intervalo do ano letivo");
            return redirect()->back()->withInput()
                ->with('error', 'As datas do período devem estar dentro do ano letivo selecionado.');
        }
        
        // Verificar sobreposição
        $existing = $this->semesterModel
            ->where('academic_year_id', $academicYearId)
            ->where('id !=', $id ?: 0)
            ->groupStart()
                ->where('start_date <=', $endDate)
                ->where('end_date >=', $startDate)
            ->groupEnd()
            ->first();
        
        if ($existing) {
            log_message('warning', "Tentativa de criar período sobreposto com ID {$existing->id}");
            return redirect()->back()->withInput()
                ->with('error', 'Já existe um período que sobrepõe estas datas.');
        }
        
        $data = [
            'academic_year_id' => $academicYearId,
            'semester_name' => $semesterName,
            'semester_type' => $semesterType,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'is_active' => $isActive
        ];
        
        if ($id) {
            // ATUALIZAÇÃO
            log_message('info', "Tentando atualizar período ID: {$id}");
            
            if ($this->semesterModel->update($id, $data)) {
                // Se marcou como atual
                if ($isCurrent) {
                    $this->semesterModel->setCurrent($id);
                }
                
                // Tentar registrar log (se o helper existir)
                if (function_exists('log_update')) {
                    log_update('semester', $id, "Período '{$semesterName}' atualizado");
                } else {
                    log_message('info', "Período '{$semesterName}' atualizado com sucesso (ID: {$id})");
                }
                
                return redirect()->to('/admin/academic/semesters')
                    ->with('success', "Período '{$semesterName}' atualizado com sucesso!");
            } else {
                // Capturar erros do model
                $errors = $this->semesterModel->errors();
                if (!empty($errors)) {
                    log_message('error', 'Erros do model: ' . json_encode($errors));
                    return redirect()->back()->withInput()
                        ->with('errors', $errors);
                }
                
                log_message('error', "Erro desconhecido ao atualizar período ID {$id}");
                return redirect()->back()->withInput()
                    ->with('error', 'Erro ao atualizar período.');
            }
        } else {
            // INSERÇÃO
            log_message('info', "Tentando criar novo período: {$semesterName}");
            
            $newId = $this->semesterModel->insert($data);
            
            if ($newId) {
                // Se marcou como atual
                if ($isCurrent) {
                    $this->semesterModel->setCurrent($newId);
                }
                
                // Tentar registrar log (se o helper existir)
                if (function_exists('log_insert')) {
                    log_insert('semester', $newId, "Novo período criado: {$semesterName}");
                } else {
                    log_message('info', "Novo período criado: {$semesterName} (ID: {$newId})");
                }
                
                return redirect()->to('/admin/academic/semesters')
                    ->with('success', "Período '{$semesterName}' criado com sucesso!");
            } else {
                // Capturar erros do model
                $errors = $this->semesterModel->errors();
                if (!empty($errors)) {
                    log_message('error', 'Erros do model: ' . json_encode($errors));
                    return redirect()->back()->withInput()
                        ->with('errors', $errors);
                }
                
                log_message('error', "Erro desconhecido ao criar período");
                return redirect()->back()->withInput()
                    ->with('error', 'Erro ao criar período.');
            }
        }
    }
    
    /**
     * Set current semester
     */
    public function setCurrent($id)
    {
        $semester = $this->semesterModel->find($id);
        
        if (!$semester) {
            log_message('warning', "Tentativa de definir período inexistente como atual: ID {$id}");
            return redirect()->back()->with('error', 'Período não encontrado.');
        }
        
        // Verificar se o período está ativo
        if (!$semester->is_active) {
            log_message('warning', "Tentativa de definir período inativo como atual: ID {$id}");
            return redirect()->back()->with('error', 'Não é possível definir um período inativo como atual.');
        }
        
        // Guardar o período atual anterior para log
        $currentSemester = $this->semesterModel->getCurrent();
        
        if ($this->semesterModel->setCurrent($id)) {
            if (function_exists('log_action')) {
                log_action('update', "Período '{$semester->semester_name}' definido como atual", $id, 'semester', [
                    'previous_current_id' => $currentSemester ? $currentSemester->id : null,
                    'previous_current_name' => $currentSemester ? $currentSemester->semester_name : null
                ]);
            } else {
                log_message('info', "Período '{$semester->semester_name}' definido como atual");
            }
            
            return redirect()->back()->with('success', "Período '{$semester->semester_name}' definido como atual.");
        } else {
            log_message('error', "Erro ao definir período ID {$id} como atual");
            return redirect()->back()->with('error', 'Erro ao definir período como atual.');
        }
    }
    
    /**
     * View semester details
     */
    public function view($id)
    {
        $data['title'] = 'Detalhes do Período';
        $data['semester'] = $this->semesterModel
            ->select('tbl_semesters.*, tbl_academic_years.year_name, tbl_academic_years.start_date as year_start, tbl_academic_years.end_date as year_end')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_semesters.academic_year_id')
            ->where('tbl_semesters.id', $id)
            ->first();
        
        if (!$data['semester']) {
            log_message('warning', "Tentativa de visualizar período inexistente: ID {$id}");
            return redirect()->to('/admin/academic/semesters')->with('error', 'Período não encontrado.');
        }
        
        // Buscar exames deste período
        $data['exams'] = $this->examModel
            ->select('tbl_exams.*, tbl_classes.class_name, tbl_disciplines.discipline_name')
            ->join('tbl_classes', 'tbl_classes.id = tbl_exams.class_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exams.discipline_id')
            ->where('tbl_exams.semester_id', $id)
            ->orderBy('tbl_exams.exam_date', 'ASC')
            ->findAll();
        
        // Buscar logs relacionados a este período (se a tabela existir)
        if (class_exists('\App\Models\UserLogModel')) {
            $logModel = new \App\Models\UserLogModel();
            $data['logs'] = $logModel->where('target_type', 'semester')
                ->where('target_id', $id)
                ->orderBy('created_at', 'DESC')
                ->limit(20)
                ->findAll();
        } else {
            $data['logs'] = [];
        }
        
        if (function_exists('log_view')) {
            log_view('semester', $id, "Visualizou detalhes do período '{$data['semester']->semester_name}'");
        }
        
        return view('admin/academic/semesters/view', $data);
    }
    
    /**
     * Delete semester
     */
    public function delete($id)
    {
        $semester = $this->semesterModel->find($id);
        
        if (!$semester) {
            log_message('warning', "Tentativa de eliminar período inexistente: ID {$id}");
            return redirect()->back()->with('error', 'Período não encontrado.');
        }
        
        if ($semester->is_current) {
            log_message('warning', "Tentativa de eliminar período atual: ID {$id}");
            return redirect()->back()->with('error', 'Não é possível eliminar o período atual. Defina outro período como atual primeiro.');
        }
        
        // Check if has exams
        $exams = $this->examModel->where('semester_id', $id)->countAllResults();
        
        if ($exams > 0) {
            log_message('warning', "Tentativa de eliminar período com {$exams} exames associados: ID {$id}");
            return redirect()->back()->with('error', "Não é possível eliminar este período porque existem {$exams} exame(s) associados a ele.");
        }
        
        if ($this->semesterModel->delete($id)) {
            if (function_exists('log_delete')) {
                log_delete('semester', $id, "Período eliminado: {$semester->semester_name}");
            } else {
                log_message('info', "Período eliminado: {$semester->semester_name} (ID: {$id})");
            }
            
            return redirect()->to('/admin/academic/semesters')
                ->with('success', "Período '{$semester->semester_name}' eliminado com sucesso.");
        } else {
            log_message('error', "Erro ao eliminar período ID {$id}");
            return redirect()->back()->with('error', 'Erro ao eliminar período.');
        }
    }
    
    /**
     * Get semester dates (AJAX)
     */
    public function getDates($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Requisição inválida']);
        }
        
        $semester = $this->semesterModel->find($id);
        
        if (!$semester) {
            log_message('warning', "AJAX: Período não encontrado ID {$id}");
            return $this->response->setJSON(['error' => 'Período não encontrado']);
        }
        
        return $this->response->setJSON([
            'success' => true,
            'start_date' => $semester->start_date,
            'end_date' => $semester->end_date,
            'academic_year_id' => $semester->academic_year_id
        ]);
    }
}