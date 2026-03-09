<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\SemesterModel;
use App\Models\AcademicYearModel;

class Semesters extends BaseController
{
    protected $semesterModel;
    protected $academicYearModel;
    
    public function __construct()
    {
        $this->semesterModel = new SemesterModel();
        $this->academicYearModel = new AcademicYearModel();
        
        // Carregar helper de log (certifique-se que o arquivo existe)
        helper('log');
    }
/**
 * List semesters - Versão simplificada (apenas carrega a view)
 */
public function index()
{
    // Verificar permissão
    if (!has_permission('settings.semesters')) {
        return redirect()->to('/admin/dashboard')->with('error', 'Não tem permissão para aceder a esta página');
    }
    
    $data['title'] = 'Semestres/Trimestres';
    
    // Buscar anos letivos ativos
    $data['academicYears'] = $this->academicYearModel
        ->where('is_active', 1)
        ->orderBy('start_date', 'DESC')
        ->findAll();
    
    // Buscar o ano letivo atual
    $currentYear = $this->academicYearModel
        ->where('id', current_academic_year())
        ->where('is_active', 1)
        ->first();
    
    // Se não houver ano selecionado na URL, usar o ano atual
    $selectedYear = $this->request->getGet('academic_year');
    if (empty($selectedYear) && $currentYear) {
        $selectedYear = $currentYear->id;
    }
    
    $data['selectedYear'] = $selectedYear;
    $data['selectedStatus'] = $this->request->getGet('status');
    
    return view('admin/academic/semesters/index', $data);
}
    // Adicione estes métodos no controller Semesters

/**
 * Retorna dados para o DataTables (AJAX)
 */
public function getTableData()
{
    try {
        $request = service('request');
        
        // Parâmetros do DataTables
        $draw = (int)($request->getPost('draw') ?? 0);
        $start = (int)($request->getPost('start') ?? 0);
        $length = (int)($request->getPost('length') ?? 25);
        
        // Search
        $search = $request->getPost('search');
        $searchValue = is_array($search) ? ($search['value'] ?? '') : '';
        
        // Order
        $order = $request->getPost('order');
        $orderColumnIndex = 1; // padrão: ano letivo
        $orderDir = 'asc';
        
        if (is_array($order) && isset($order[0])) {
            $orderColumnIndex = (int)($order[0]['column'] ?? 1);
            $orderDir = $order[0]['dir'] ?? 'asc';
        }
        
        // Filtros adicionais
        $academicYearId = $request->getPost('academic_year');
        $status = $request->getPost('status');
        
        // Colunas para ordenação
        $columns = [
            0 => 'tbl_semesters.id',
            1 => 'tbl_academic_years.year_name',
            2 => 'tbl_semesters.semester_name',
            3 => 'tbl_semesters.semester_type',
            4 => 'tbl_semesters.start_date',
        ];
        
        $orderColumn = $columns[$orderColumnIndex] ?? 'tbl_academic_years.year_name';
        
        // Query principal
        $builder = $this->semesterModel
            ->select('
                tbl_semesters.*,
                tbl_academic_years.year_name,
                (SELECT COUNT(*) FROM tbl_exam_schedules es 
                 JOIN tbl_exam_periods ep ON ep.id = es.exam_period_id 
                 WHERE ep.semester_id = tbl_semesters.id) as total_exams,
                (SELECT COUNT(*) FROM tbl_semester_results WHERE semester_id = tbl_semesters.id) as has_results
            ')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_semesters.academic_year_id')
            ->where('tbl_semesters.status', 'ativo');
        
        // Aplicar filtros
        if (!empty($academicYearId)) {
            $builder->where('tbl_semesters.academic_year_id', $academicYearId);
        }
        
        if (!empty($status)) {
            $builder->where('tbl_semesters.status', $status);
        }
        
        // Aplicar busca
        if (!empty($searchValue)) {
            $builder->groupStart()
                ->like('tbl_semesters.semester_name', $searchValue)
                ->orLike('tbl_semesters.semester_type', $searchValue)
                ->orLike('tbl_academic_years.year_name', $searchValue)
                ->groupEnd();
        }
        
        // Contar total de registros filtrados
        $recordsFiltered = $builder->countAllResults(false);
        
        // Aplicar ordenação e paginação
        $data = $builder->orderBy($orderColumn, $orderDir)
            ->limit($length, $start)
            ->get()
            ->getResult();
        
        // Processar dados para o DataTables
        foreach ($data as &$row) {
            // Status badge
            $statusBadgeMap = [
                'ativo'      => ['cls' => 'sb-ativo', 'icon' => 'fa-play', 'text' => 'Ativo'],
                'inativo'    => ['cls' => 'sb-inativo', 'icon' => 'fa-stop', 'text' => 'Inativo'],
                'processado' => ['cls' => 'sb-processado', 'icon' => 'fa-calculator', 'text' => 'Processado'],
                'concluido'  => ['cls' => 'sb-concluido', 'icon' => 'fa-check-double', 'text' => 'Concluído'],
            ];
            
            $status = $row->status ?? 'ativo';
            $badge = $statusBadgeMap[$status] ?? ['cls' => 'sb-inativo', 'text' => $status];
            
            $row->status_badge = '<span class="status-badge ' . $badge['cls'] . '">' .
                                  '<span class="sd"></span> ' . $badge['text'] . '</span>';
            
            // Current badge
            if ($row->is_current) {
                $row->current_badge = '<span class="current-badge"><i class="fas fa-star" style="font-size:.6rem;"></i> Atual</span>';
            } else {
                $row->current_badge = '<a href="' . site_url('admin/academic/semesters/set-current/' . $row->id) . '" ' .
                                      'class="btn-set-current" title="Definir como atual" ' .
                                      'onclick="return confirm(\'Definir este período como atual?\')">' .
                                      '<i class="fas fa-check-circle"></i></a>';
            }
            
            // Actions
            $canEdit = in_array($status, ['ativo', 'inativo']);
            $canDelete = !$row->is_current && in_array($status, ['ativo', 'inativo']);
            
            $actions = '<div class="action-group">';
            
            // Edit button
            if ($canEdit) {
                $actions .= '<a href="' . site_url('admin/academic/semesters/form-edit/' . $row->id) . '" ' .
                           'class="row-btn edit" title="Editar"><i class="fas fa-edit"></i></a>';
            } else {
                $actions .= '<span class="row-btn disabled-btn" title="Não pode editar"><i class="fas fa-edit"></i></span>';
            }
            
            // View button
            $actions .= '<a href="' . site_url('admin/academic/semesters/view/' . $row->id) . '" ' .
                       'class="row-btn view" title="Ver Detalhes"><i class="fas fa-eye"></i></a>';
            
            // Conclude button (only for processed)
            if ($status === 'processado') {
                $actions .= '<a href="' . site_url('admin/academic/semesters/conclude/' . $row->id) . '" ' .
                           'class="row-btn conclude" title="Concluir Período" ' .
                           'onclick="return confirm(\'Concluir este período? Esta ação irá fechar o semestre permanentemente.\')">' .
                           '<i class="fas fa-check-double"></i></a>';
            }
            
            // Delete button
            if ($canDelete) {
                $actions .= '<a href="' . site_url('admin/academic/semesters/delete/' . $row->id) . '" ' .
                           'class="row-btn del" title="Eliminar" ' .
                           'onclick="return confirm(\'Tem certeza que deseja eliminar este período?\')">' .
                           '<i class="fas fa-trash"></i></a>';
            } else {
                $actions .= '<span class="row-btn disabled-btn" title="Não pode eliminar"><i class="fas fa-trash"></i></span>';
            }
            
            $actions .= '</div>';
            $row->actions = $actions;
        }
        
        // Total de registros sem filtros
        $totalRecords = $this->semesterModel
            ->where('status', 'ativo')
            ->countAllResults();
        
        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ]);
        
    } catch (\Exception $e) {
        log_message('error', 'Erro no DataTables de semestres: ' . $e->getMessage());
        
        return $this->response->setStatusCode(500)->setJSON([
            'error' => 'Erro interno: ' . $e->getMessage(),
            'draw' => (int)($request->getPost('draw') ?? 0),
            'recordsTotal' => 0,
            'recordsFiltered' => 0,
            'data' => []
        ]);
    }
}

/**
 * Retorna estatísticas para os cards (AJAX)
 */
public function getStats()
{
    if (!$this->request->isAJAX()) {
        return $this->response->setJSON([]);
    }
    
    $db = db_connect();
    
    $total = $this->semesterModel->countAllResults();
    $active = $this->semesterModel->where('status', 'ativo')->countAllResults();
    $processed = $this->semesterModel->where('status', 'processado')->countAllResults();
    $concluded = $this->semesterModel->where('status', 'concluido')->countAllResults();
    
    return $this->response->setJSON([
        'total' => $total,
        'active' => $active,
        'processed' => $processed,
        'concluded' => $concluded
    ]);
}
    /**
     * Semester form
     */
    public function form($id = null)
    {
        // Verificar permissão
        if (!has_permission('settings.semesters')) {
            return redirect()->to('/admin/academic/semesters')->with('error', 'Não tem permissão para aceder a esta página');
        }
        
        $data['title'] = $id ? 'Editar Semestre/Trimestre' : 'Novo Semestre/Trimestre';
        $data['semester'] = $id ? $this->semesterModel->find($id) : null;
        
        // VERIFICAR SE PODE EDITAR
        $data['canEdit'] = true;
        $data['editMessage'] = '';
        
        if ($id && $data['semester']) {
            $db = db_connect();
            
            // Verificar se já existem resultados processados
            $hasResults = $db->table('tbl_semester_results')
                ->where('semester_id', $id)
                ->countAllResults();
            
            if ($hasResults > 0) {
                // BLOQUEAR EDIÇÃO
                $data['canEdit'] = false;
                $data['editMessage'] = '❌ Este semestre já possui resultados processados e não pode ser editado.';
                
                // Redirecionar com erro (se quiser)
                return redirect()->back()->with('error', 'Este semestre já possui resultados processados e não pode ser editado.');
            }
            
            // Verificar se já existem notas lançadas
            $hasGrades = $db->table('tbl_exam_results er')
                ->join('tbl_exam_schedules es', 'es.id = er.exam_schedule_id')
                ->join('tbl_exam_periods ep', 'ep.id = es.exam_period_id')
                ->where('ep.semester_id', $id)
                ->countAllResults();
            
            if ($hasGrades > 0 && $data['canEdit']) {
                // Se tem notas, pode editar mas com aviso
                $data['editMessage'] = '⚠️ Este semestre possui notas lançadas. Alterações podem afetar os cálculos.';
            }
        }
        
        $data['academicYears'] = $this->academicYearModel->where('is_active', 1)->findAll();
        
        return view('admin/academic/semesters/form', $data);
    }
    
    /**
     * Save semester - VERSÃO CORRIGIDA COM LOGS
     */
    public function save()
    {
        // Verificar permissão
        if (!has_permission('settings.semesters')) {
            return redirect()->to('/admin/academic/semesters')->with('error', 'Não tem permissão para esta ação');
        }
        
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
        $status = $this->request->getPost('is_active') ? 'ativo' : 'inativo';
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
            'status' => $status
        ];
        
        if ($id) {
            // ATUALIZAÇÃO
            log_message('info', "Tentando atualizar período ID: {$id}");
            
            // Verificar se o semestre existe
            $semester = $this->semesterModel->find($id);
            if (!$semester) {
                return redirect()->back()->withInput()
                    ->with('error', 'Período não encontrado.');
            }
            
            // Verificar se pode editar (não tem resultados processados)
            $db = db_connect();
            $hasResults = $db->table('tbl_semester_results')
                ->where('semester_id', $id)
                ->countAllResults();
            
            if ($hasResults > 0) {
                return redirect()->back()->withInput()
                    ->with('error', 'Não é possível editar um período que já possui resultados processados.');
            }
            
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
        // Verificar permissão
        if (!has_permission('settings.semesters')) {
            return redirect()->back()->with('error', 'Não tem permissão para esta ação');
        }
        
        $semester = $this->semesterModel->find($id);
        
        if (!$semester) {
            log_message('warning', "Tentativa de definir período inexistente como atual: ID {$id}");
            return redirect()->back()->with('error', 'Período não encontrado.');
        }
        
        // Verificar se o período está ativo
        if ($semester->status !== 'ativo') {
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
        // Verificar permissão
        if (!has_permission('settings.semesters')) {
            return redirect()->to('/admin/dashboard')->with('error', 'Não tem permissão para aceder a esta página');
        }
        
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
        
        // Buscar exames deste período (usando tbl_exam_schedules em vez de tbl_exams)
        $db = db_connect();
        $exams = $db->table('tbl_exam_schedules es')
            ->select('
                es.*, 
                c.class_name, 
                d.discipline_name,
                ep.period_name,
                eb.board_name,
                eb.board_type
            ')
            ->join('tbl_classes c', 'c.id = es.class_id')
            ->join('tbl_disciplines d', 'd.id = es.discipline_id')
            ->join('tbl_exam_periods ep', 'ep.id = es.exam_period_id')
            ->join('tbl_exam_boards eb', 'eb.id = es.exam_board_id')
            ->where('ep.semester_id', $id)
            ->orderBy('es.exam_date', 'ASC')
            ->get()
            ->getResult();
        
        $data['exams'] = $exams;
        
        // Estatísticas adicionais
        $data['total_exams'] = count($exams);
        $data['total_results'] = $db->table('tbl_semester_results')
            ->where('semester_id', $id)
            ->countAllResults();
        
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
        // Verificar permissão
        if (!has_permission('settings.semesters')) {
            return redirect()->back()->with('error', 'Não tem permissão para eliminar períodos');
        }
        
        $semester = $this->semesterModel->find($id);
        
        if (!$semester) {
            log_message('warning', "Tentativa de eliminar período inexistente: ID {$id}");
            return redirect()->back()->with('error', 'Período não encontrado.');
        }
        
        if ($semester->is_current) {
            log_message('warning', "Tentativa de eliminar período atual: ID {$id}");
            return redirect()->back()->with('error', 'Não é possível eliminar o período atual. Defina outro período como atual primeiro.');
        }
        
        // VERIFICAR SE JÁ EXISTEM RESULTADOS PROCESSADOS
        $db = db_connect();
        $hasResults = $db->table('tbl_semester_results')
            ->where('semester_id', $id)
            ->countAllResults();
        
        if ($hasResults > 0) {
            log_message('warning', "Tentativa de eliminar período com {$hasResults} resultados processados: ID {$id}");
            return redirect()->back()->with('error', "Não é possível eliminar este período porque existem {$hasResults} resultados já processados.");
        }
        
        // Check if has exams
        $examScheduleModel = new \App\Models\ExamScheduleModel();
        $exams = $examScheduleModel
            ->join('tbl_exam_periods', 'tbl_exam_periods.id = tbl_exam_schedules.exam_period_id')
            ->where('tbl_exam_periods.semester_id', $id)
            ->countAllResults();
        
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
        // Permissão: Qualquer um com acesso à página pode fazer esta consulta AJAX
        if (!has_permission('settings.semesters')) {
            return $this->response->setJSON(['error' => 'Sem permissão']);
        }
        
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
    
    /**
     * Get semesters by academic year (AJAX)
     * 
     * @param int $yearId ID do ano letivo
     * @return JSON
     */
    public function getByYear($yearId = null)
    {
        // Permissão: Qualquer um com acesso à página pode fazer esta consulta AJAX
        if (!has_permission('settings.semesters')) {
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Sem permissão'
            ]);
        }
        
        // Log para debug
        log_message('debug', "=== Semesters::getByYear chamado ===");
        log_message('debug', "Parâmetro yearId: " . ($yearId ?? 'null'));
        
        // Verificar se é requisição AJAX
        if (!$this->request->isAJAX()) {
            log_message('warning', "Tentativa de acesso não-AJAX ao método getByYear");
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Requisição inválida'
            ]);
        }
        
        // Validar parâmetro
        if (!$yearId) {
            log_message('error', "ID de ano letivo não fornecido");
            return $this->response->setJSON([
                'success' => false,
                'error' => 'ID do ano letivo é obrigatório'
            ]);
        }
        
        if (!is_numeric($yearId)) {
            log_message('error', "ID de ano letivo inválido: {$yearId}");
            return $this->response->setJSON([
                'success' => false,
                'error' => 'ID deve ser numérico'
            ]);
        }
        
        // Verificar se o ano letivo existe
        $academicYear = $this->academicYearModel->find($yearId);
        if (!$academicYear) {
            log_message('warning', "Ano letivo não encontrado: ID {$yearId}");
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Ano letivo não encontrado'
            ]);
        }
        
        // Buscar semestres do ano letivo
        $semesters = $this->semesterModel
            ->select('id, semester_name, semester_type, start_date, end_date, is_current')
            ->where('academic_year_id', $yearId)
            ->where('status', 'ativo')
            ->orderBy('start_date', 'ASC')
            ->findAll();
        
        log_message('debug', "Encontrados " . count($semesters) . " semestres para o ano {$yearId}");
        
        // Formatar datas para o formato ISO
        foreach ($semesters as $semester) {
            $semester->start_date_formatted = date('d/m/Y', strtotime($semester->start_date));
            $semester->end_date_formatted = date('d/m/Y', strtotime($semester->end_date));
        }
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $semesters,
            'total' => count($semesters)
        ]);
    }
    
    /**
     * Get semester information for processing modal (AJAX)
     */
    public function info($semesterId)
    {
        // Permissão: Qualquer um com acesso à página pode fazer esta consulta AJAX
        if (!has_permission('settings.semesters')) {
            return $this->response->setJSON(['success' => false, 'message' => 'Sem permissão']);
        }
        
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Requisição inválida']);
        }
        
        $semester = $this->semesterModel
            ->select('tbl_semesters.*, tbl_academic_years.year_name')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_semesters.academic_year_id')
            ->find($semesterId);
        
        if (!$semester) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Semestre não encontrado'
            ]);
        }
        
        // Buscar estatísticas
        $db = db_connect();
        
        // 1. Total de exames no semestre
        $totalExams = $db->table('tbl_exam_schedules es')
            ->join('tbl_exam_periods ep', 'ep.id = es.exam_period_id')
            ->where('ep.semester_id', $semesterId)
            ->countAllResults();
        
        // 2. Total de alunos ÚNICOS matriculados (CORRIGIDO)
        $query = $db->table('tbl_enrollments e')
            ->select('COUNT(DISTINCT e.id) as total')
            ->join('tbl_classes c', 'c.id = e.class_id')
            ->join('tbl_class_disciplines cd', 'cd.class_id = c.id')
            ->join('tbl_semesters s', 's.id = cd.semester_id')
            ->where('s.id', $semesterId)
            ->where('e.status', 'Ativo')
            ->get();
        
        $totalStudents = $query->getRow()->total;
        
        // 3. Resultados já processados
        $totalResults = $db->table('tbl_semester_results')
            ->where('semester_id', $semesterId)
            ->countAllResults();
        
        // 4. Total de disciplinas com notas lançadas
        $disciplinesWithGrades = $db->table('tbl_exam_results er')
            ->join('tbl_exam_schedules es', 'es.id = er.exam_schedule_id')
            ->join('tbl_exam_periods ep', 'ep.id = es.exam_period_id')
            ->where('ep.semester_id', $semesterId)
            ->select('COUNT(DISTINCT es.discipline_id) as total')
            ->get()
            ->getRow()
            ->total;
        
        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'id' => $semester->id,
                'semester_name' => $semester->semester_name,
                'semester_type' => $semester->semester_type,
                'year_name' => $semester->year_name,
                'start_date' => date('d/m/Y', strtotime($semester->start_date)),
                'end_date' => date('d/m/Y', strtotime($semester->end_date)),
                'is_current' => $semester->is_current
            ],
            'stats' => [
                'total_exams' => $totalExams,
                'total_students' => $totalStudents, // Agora mostra 2, não 6!
                'total_results' => $totalResults,
                'disciplines_with_grades' => $disciplinesWithGrades
            ]
        ]);
    }
    
    /**
     * Process semester results (calculate averages and generate report cards)
     */
    public function process()
    {
        // Verificar permissão - esta é uma ação administrativa importante
        if (!has_permission('settings.semesters')) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sem permissão para processar resultados'
            ]);
        }
        
        // Verificar se é AJAX
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Requisição inválida'
            ]);
        }
        
        $semesterId = $this->request->getPost('semester_id');
        $generateReportCards = (bool) $this->request->getPost('generate_report_cards');
        $closeSemester = (bool) $this->request->getPost('close_semester');
        
        if (!$semesterId) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'ID do semestre não fornecido.'
            ]);
        }
        
        // Verificar se o semestre existe
        $semester = $this->semesterModel->find($semesterId);
        if (!$semester) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Semestre não encontrado.'
            ]);
        }
        
        $db = db_connect();
        
        // Verificar se existem notas para processar
        $hasGrades = $db->table('tbl_exam_results er')
            ->join('tbl_exam_schedules es', 'es.id = er.exam_schedule_id')
            ->join('tbl_exam_periods ep', 'ep.id = es.exam_period_id')
            ->where('ep.semester_id', $semesterId)
            ->countAllResults();
        
        if ($hasGrades == 0) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Não existem notas lançadas neste semestre para processar.'
            ]);
        }
        
        $db->transStart();
        
        try {
            // PASSO 1: Calcular médias por disciplina
            $this->calculateDisciplineAverages($semesterId);
            
            // PASSO 2: Calcular resultados do semestre
            $resultsCount = $this->calculateSemesterResults($semesterId);
            
            // PASSO 3: Gerar boletins (se solicitado)
            $reportCardsCount = 0;
            if ($generateReportCards) {
                $reportCardsCount = $this->generateReportCards($semesterId);
            }
            
            // ATUALIZAR STATUS DO SEMESTRE PARA "processado"
            $this->semesterModel->update($semesterId, [
                'status' => 'processado'
            ]);
            
            // PASSO 4: Fechar semestre (se solicitado)
            if ($closeSemester) {
                $this->closeSemester($semesterId);
                // Se fechou, status passa a ser "concluido"
                $this->semesterModel->update($semesterId, [
                    'status' => 'concluido'
                ]);
            }
            
            $db->transComplete();
            
            if ($db->transStatus()) {
                log_message('info', "Semestre ID {$semesterId} processado. Resultados: {$resultsCount}, Boletins: {$reportCardsCount}");
                
                return $this->response->setJSON([
                    'success' => true,
                    'message' => "✅ Semestre processado com sucesso!<br>
                                  📊 Resultados calculados: {$resultsCount}<br>
                                  📄 Boletins gerados: {$reportCardsCount}" . 
                                  ($closeSemester ? "<br>🔒 Semestre fechado!" : "")
                ]);
            }
            
        } catch (\Exception $e) {
            $db->transRollback();
            log_message('error', 'Erro no processamento: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'message' => '❌ Erro ao processar: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Calculate discipline averages for a semester
     */
    private function calculateDisciplineAverages($semesterId)
    {
        $db = db_connect();
        
        // Buscar todos os exames do semestre
        $sql = "INSERT INTO tbl_discipline_averages 
                (enrollment_id, discipline_id, semester_id, final_score, status, calculated_at)
                SELECT 
                    er.enrollment_id,
                    es.discipline_id,
                    ep.semester_id,
                    AVG(er.score) as final_score,
                    CASE 
                        WHEN AVG(er.score) >= 10 THEN 'Aprovado'
                        WHEN AVG(er.score) >= 7 THEN 'Recurso'
                        ELSE 'Reprovado'
                    END as status,
                    NOW() as calculated_at
                FROM tbl_exam_results er
                JOIN tbl_exam_schedules es ON es.id = er.exam_schedule_id
                JOIN tbl_exam_periods ep ON ep.id = es.exam_period_id
                WHERE ep.semester_id = {$semesterId}
                GROUP BY er.enrollment_id, es.discipline_id, ep.semester_id
                ON DUPLICATE KEY UPDATE
                    final_score = VALUES(final_score),
                    status = VALUES(status),
                    calculated_at = NOW()";
        
        return $db->query($sql);
    }
    
    /**
     * Calculate semester results
     */
    private function calculateSemesterResults($semesterId)
    {
        $db = db_connect();
        
        // Buscar todas as médias por disciplina e consolidar
        $sql = "INSERT INTO tbl_semester_results 
                (enrollment_id, semester_id, overall_average, total_disciplines,
                 approved_disciplines, failed_disciplines, appeal_disciplines,
                 status, calculated_at)
                SELECT 
                    da.enrollment_id,
                    da.semester_id,
                    AVG(da.final_score) as overall_average,
                    COUNT(*) as total_disciplines,
                    SUM(CASE WHEN da.status = 'Aprovado' THEN 1 ELSE 0 END) as approved_disciplines,
                    SUM(CASE WHEN da.status = 'Reprovado' THEN 1 ELSE 0 END) as failed_disciplines,
                    SUM(CASE WHEN da.status = 'Recurso' THEN 1 ELSE 0 END) as appeal_disciplines,
                    CASE 
                        WHEN SUM(CASE WHEN da.status = 'Reprovado' THEN 1 ELSE 0 END) > 0 THEN 'Reprovado'
                        WHEN SUM(CASE WHEN da.status = 'Recurso' THEN 1 ELSE 0 END) > 0 THEN 'Recurso'
                        ELSE 'Aprovado'
                    END as status,
                    NOW() as calculated_at
                FROM tbl_discipline_averages da
                WHERE da.semester_id = {$semesterId}
                GROUP BY da.enrollment_id, da.semester_id
                ON DUPLICATE KEY UPDATE
                    overall_average = VALUES(overall_average),
                    total_disciplines = VALUES(total_disciplines),
                    approved_disciplines = VALUES(approved_disciplines),
                    failed_disciplines = VALUES(failed_disciplines),
                    appeal_disciplines = VALUES(appeal_disciplines),
                    status = VALUES(status),
                    calculated_at = NOW()";
        
        $db->query($sql);
        
        // Retornar número de resultados calculados
        $result = $db->query("SELECT COUNT(*) as total FROM tbl_semester_results WHERE semester_id = {$semesterId}")->getRow();
        return $result->total;
    }
    
    /**
     * Generate report cards for all students in the semester
     */
    private function generateReportCards($semesterId)
    {
        $reportCardModel = new \App\Models\ReportCardModel();
        
        // Buscar todos os resultados do semestre
        $db = db_connect();
        $results = $db->table('tbl_semester_results')
            ->where('semester_id', $semesterId)
            ->get()
            ->getResult();
        
        $count = 0;
        foreach ($results as $result) {
            try {
                // Verificar se já existe boletim
                $existing = $db->table('tbl_report_cards')
                    ->where('enrollment_id', $result->enrollment_id)
                    ->where('semester_id', $semesterId)
                    ->get()
                    ->getRow();
                
                if (!$existing) {
                    // Gerar novo boletim
                    $reportCardId = $reportCardModel->generateForStudent(
                        $result->enrollment_id, 
                        $semesterId
                    );
                    if ($reportCardId) $count++;
                }
            } catch (\Exception $e) {
                log_message('error', 'Erro ao gerar boletim para matrícula ' . $result->enrollment_id . ': ' . $e->getMessage());
            }
        }
        
        return $count;
    }
    
    /**
     * Close semester (prevent new entries)
     */
    private function closeSemester($semesterId)
    {
        $db = db_connect();
        
        // Desativar o semestre
        $db->table('tbl_semesters')
            ->where('id', $semesterId)
            ->update(['status' => 'inativo']);
        
        // Se este era o semestre atual, remover flag
        $db->table('tbl_semesters')
            ->where('id', $semesterId)
            ->where('is_current', 1)
            ->update(['is_current' => 0]);
        
        // Opcional: arquivar exames ou outras ações
        
        return true;
    }
    
    /**
     * Mark semester as concluded
     */
    public function conclude($id)
    {
        // Verificar permissão
        if (!has_permission('settings.semesters')) {
            return redirect()->back()->with('error', 'Não tem permissão para concluir períodos');
        }
        
        $semester = $this->semesterModel->find($id);
        
        if (!$semester) {
            return redirect()->back()->with('error', 'Período não encontrado.');
        }
        
        // Verificar se já tem resultados
        $db = db_connect();
        $hasResults = $db->table('tbl_semester_results')
            ->where('semester_id', $id)
            ->countAllResults();
        
        if ($hasResults == 0) {
            return redirect()->back()->with('error', 'Não é possível concluir um semestre sem resultados processados.');
        }
        
        $this->semesterModel->update($id, [
            'status' => 'concluido',
            'is_current' => 0
        ]);
        
        log_message('info', "Semestre ID {$id} marcado como concluído por usuário ID: " . session()->get('user_id'));
        
        return redirect()->back()->with('success', 'Semestre concluído com sucesso!');
    }
}