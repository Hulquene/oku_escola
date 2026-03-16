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
     * List academic years - Dados carregados diretamente no PHP
     */
    public function index()
    {
        // Verificar permissão
        if (!has_permission('settings.academic_years') && !is_admin()) {
            return redirect()->to('/admin/dashboard')->with('error', 'Não tem permissão para aceder a esta página');
        }
        
        $data['title'] = 'Anos Letivos';
        
        // Buscar todos os anos letivos ordenados por data de início (mais recente primeiro)
        $years = $this->academicYearModel
            ->orderBy('start_date', 'DESC')
            ->findAll();
        
        // Para cada ano, adicionar contagens de semestres e matrículas
        foreach ($years as &$year) {
            // Contar semestres
            $year['total_semesters'] = $this->semesterModel
                ->where('academic_year_id', $year['id'])
                ->countAllResults();
            
            // Contar matrículas
            $year['total_enrollments'] = $this->enrollmentModel
                ->where('academic_year_id', $year['id'])
                ->countAllResults();
        }
        
        $data['years'] = $years;
        $data['currentYearId'] = current_academic_year();
        
        return view('admin/academic/years/index', $data);
    }
    /**
     * List academic years - Versão simplificada (apenas carrega a view)
     */
 /*    public function index()
    {
        // Verificar permissão
        if (!has_permission('settings.academic_years') && !is_admin()) {
            return redirect()->to('/admin/dashboard')->with('error', 'Não tem permissão para aceder a esta página');
        }
        
        $data['title'] = 'Anos Letivos';
        
        return view('admin/academic/years/index', $data);
    } */
    
public function getTableData()
{
    if (!$this->request->isAJAX()) {
        return $this->response->setJSON(['error' => 'Requisição inválida']);
    }

    try {

        $request = service('request');

        $draw   = (int)($request->getPost('draw') ?? 0);
        $start  = (int)($request->getPost('start') ?? 0);
        $length = (int)($request->getPost('length') ?? 25);

        // Search
        $search = $request->getPost('search');
        $searchValue = is_array($search) ? ($search['value'] ?? '') : '';

        // Order
        $order = $request->getPost('order');
        $orderColumnIndex = 1;
        $orderDir = 'asc';

        if (is_array($order) && isset($order[0])) {
            $orderColumnIndex = (int)($order[0]['column'] ?? 1);
            $orderDir = $order[0]['dir'] ?? 'asc';
        }

        $columns = [
            0 => 'id',
            1 => 'year_name',
            2 => 'start_date',
            3 => 'end_date',
            4 => 'is_active'
        ];

        $orderColumn = $columns[$orderColumnIndex] ?? 'year_name';

        /*
        ------------------------------------------
        QUERY BASE
        ------------------------------------------
        */

        $builder = $this->academicYearModel
            ->select('
                id,
                year_name,
                start_date,
                end_date,
                is_active,
                (SELECT COUNT(*) FROM tbl_semesters WHERE academic_year_id = tbl_academic_years.id) as total_semesters,
                (SELECT COUNT(*) FROM tbl_enrollments WHERE academic_year_id = tbl_academic_years.id) as total_enrollments
            '); // ✅ Fechamento da string corrigido

        /*
        ------------------------------------------
        BUSCA
        ------------------------------------------
        */

        if (!empty($searchValue)) {
            $builder->groupStart()
                ->like('year_name', $searchValue)
                ->groupEnd();
        }

        // Contar total de registros filtrados (ANTES da paginação)
        $recordsFiltered = $builder->countAllResults(false);

        // Total de registros sem filtros
        $totalRecords = $this->academicYearModel->countAll();

        // Aplicar ordenação e paginação NO BANCO (mais eficiente)
        $years = $builder->orderBy($orderColumn, $orderDir)
            ->limit($length, $start)
            ->get()
            ->getResult();

        /*
        ------------------------------------------
        FORMATAR RESULTADOS
        ------------------------------------------
        */

        $data = [];
        $currentYearId = current_academic_year();

        foreach ($years as $row) {

            $formattedRow = [];

            $formattedRow['id_html'] =
                '<span class="row-id">'.$row->id.'</span>';

            $formattedRow['year_html'] =
                '<span class="year-name">'.$row->year_name.'</span>';

            $formattedRow['start_html'] =
                '<span class="period-text">'.date('d/m/Y', strtotime($row->start_date)).'</span>';

            $formattedRow['end_html'] =
                '<span class="period-text">'.date('d/m/Y', strtotime($row->end_date)).'</span>';

            $semestersCount = $row->total_semesters ?? 0;
            $formattedRow['semesters_html'] =
                '<span class="count-chip neutral">'.$semestersCount.'</span>';

            $enrollmentsCount = $row->total_enrollments ?? 0;
            $formattedRow['enrollments_html'] =
                '<span class="count-chip '.($enrollmentsCount > 0 ? 'has' : 'neutral').'">'.$enrollmentsCount.'</span>';

            if ($row->is_active) {
                $formattedRow['status_html'] =
                    '<span class="status-badge ativo"><span class="status-dot"></span> Ativo</span>';
            } else {
                $formattedRow['status_html'] =
                    '<span class="status-badge inativo"><span class="status-dot"></span> Inativo</span>';
            }

            if ($row->id == $currentYearId) {
                $formattedRow['current_html'] =
                    '<span class="current-badge"><i class="fas fa-star" style="font-size:.6rem;"></i> Atual</span>';
            } else {
                $formattedRow['current_html'] =
                    '<a href="'.route_to('academic.years.setCurrent', $row->id).'" ' .
                    'class="btn-set-current" title="Definir como atual" ' .
                    'onclick="return confirm(\'Definir este ano como atual?\')">' .
                    '<i class="fas fa-check-circle"></i></a>';
            }

            // AÇÕES - Verificar se a rota de edição existe
            $editRoute = route_to('academic.years.form.edit', $row->id);
            if (empty($editRoute)) {
                // Fallback para URL direta se a rota não existir
                $editRoute = site_url('admin/academic/years/form-edit/' . $row->id);
            }

            $actions = '<div class="action-group">';
            $actions .= '<a href="'.route_to('academic.years.view', $row->id).'" ' .
                       'class="row-btn view" title="Ver Detalhes" data-bs-toggle="tooltip">' .
                       '<i class="fas fa-eye"></i></a>';
            $actions .= '<a href="'.$editRoute.'" ' .
                       'class="row-btn edit" title="Editar" data-bs-toggle="tooltip">' .
                       '<i class="fas fa-edit"></i></a>';

            if ($row->id != $currentYearId) {
                $actions .= '<a href="'.route_to('academic.years.delete', $row->id).'" ' .
                           'class="row-btn del" title="Eliminar" data-bs-toggle="tooltip" ' .
                           'onclick="return confirm(\'Tem certeza que deseja eliminar este ano letivo?\')">' .
                           '<i class="fas fa-trash"></i></a>';
            } else {
                $actions .= '<span class="row-btn disabled-btn" title="Não pode eliminar o ano atual" data-bs-toggle="tooltip">' .
                           '<i class="fas fa-trash"></i></span>';
            }

            $actions .= '</div>';

            $formattedRow['actions'] = $actions;

            $data[] = $formattedRow;
        }

        /*
        ------------------------------------------
        RESPOSTA FINAL
        ------------------------------------------
        */

        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ]);

    } catch (\Exception $e) {

        log_message('error', 'Erro no DataTables de anos letivos: '.$e->getMessage());
        log_message('error', 'Stack trace: '.$e->getTraceAsString());

        return $this->response->setStatusCode(500)->setJSON([
            'error' => 'Erro interno: ' . $e->getMessage(),
            'draw' => (int)($this->request->getPost('draw') ?? 0),
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
    
    $total = $this->academicYearModel->countAll();
    $active = $this->academicYearModel->where('is_active', 1)->countAll();
    $inactive = $this->academicYearModel->where('is_active', 0)->countAll();
    
    return $this->response->setJSON([
        'total' => $total,
        'active' => $active,
        'inactive' => $inactive
    ]);
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
     * Save academic year
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