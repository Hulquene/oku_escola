<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\DisciplineModel;

class Disciplines extends BaseController
{
    protected $disciplineModel;
    
    public function __construct()
    {
        $this->disciplineModel = new DisciplineModel();
    }
    
    /**
     * List disciplines
     */
    public function index()
    {
        $data['title'] = 'Disciplinas';
        $data['subjects'] = $this->disciplineModel
            ->orderBy('discipline_name', 'ASC')
            ->findAll();
        
        return view('admin/classes/subjects/index', $data);
    }
    
/**
 * Save discipline
 */
/**
 * Save discipline
 */
public function save()
{
    $id = $this->request->getPost('id');
    
    // Log dos dados recebidos
    log_message('info', '=== SAVE DISCIPLINE ===');
    log_message('info', 'POST data: ' . json_encode($this->request->getPost()));
    
    // Converter valores para números
    $minGrade = $this->request->getPost('min_grade') ? (float) $this->request->getPost('min_grade') : 0;
    $maxGrade = $this->request->getPost('max_grade') ? (float) $this->request->getPost('max_grade') : 20;
    $approvalGrade = $this->request->getPost('approval_grade') ? (float) $this->request->getPost('approval_grade') : 10;
    
    log_message('info', "Valores convertidos - min: $minGrade, max: $maxGrade, approval: $approvalGrade");
    
    // Validação manual no controller
    if ($approvalGrade < $minGrade) {
        log_message('error', "ERRO: approval ($approvalGrade) < min ($minGrade)");
        return redirect()->back()->withInput()
            ->with('error', 'A nota de aprovação não pode ser menor que a nota mínima.');
    }
    
    if ($approvalGrade > $maxGrade) {
        log_message('error', "ERRO: approval ($approvalGrade) > max ($maxGrade)");
        return redirect()->back()->withInput()
            ->with('error', 'A nota de aprovação não pode ser maior que a nota máxima.');
    }
    
    if ($minGrade > $maxGrade) {
        log_message('error', "ERRO: min ($minGrade) > max ($maxGrade)");
        return redirect()->back()->withInput()
            ->with('error', 'A nota mínima não pode ser maior que a nota máxima.');
    }
    
    $data = [
        'discipline_name' => $this->request->getPost('discipline_name'),
        'discipline_code' => $this->request->getPost('discipline_code'),
        'discipline_type' => $this->request->getPost('discipline_type') ?: 'Obrigatória',
        'workload_hours' => $this->request->getPost('workload_hours') ? (int) $this->request->getPost('workload_hours') : null,
        'min_grade' => $minGrade,
        'max_grade' => $maxGrade,
        'approval_grade' => $approvalGrade,
        'description' => $this->request->getPost('description'),
        'is_active' => $this->request->getPost('is_active') ? 1 : 0
    ];
    
    log_message('info', 'Dados para salvar: ' . json_encode($data));
    
    if ($id) {
        $data['id'] = $id;
    }
    
    if ($this->disciplineModel->save($data)) {
        log_message('info', 'SUCESSO: Disciplina salva com ID: ' . ($id ?: $this->disciplineModel->getInsertID()));
        $message = $id ? 'Disciplina atualizada com sucesso' : 'Disciplina criada com sucesso';
        return redirect()->to('/admin/classes/subjects')->with('success', $message);
    } else {
        $errors = $this->disciplineModel->errors();
        log_message('error', 'ERROS DO MODEL: ' . json_encode($errors));
        return redirect()->back()->withInput()
            ->with('errors', $errors);
    }
}
    
    /**
     * Delete discipline
     */
    public function delete($id)
    {
        $discipline = $this->disciplineModel->find($id);
        
        if (!$discipline) {
            return redirect()->back()->with('error', 'Disciplina não encontrada');
        }
        
        // Check if discipline is assigned to any class
        $classDisciplineModel = new \App\Models\ClassDisciplineModel();
        $assignments = $classDisciplineModel->where('discipline_id', $id)->countAllResults();
        
        if ($assignments > 0) {
            return redirect()->back()->with('error', 'Não é possível eliminar uma disciplina que está atribuída a turmas');
        }
        
        $this->disciplineModel->delete($id);
        
        return redirect()->to('/admin/classes/subjects')->with('success', 'Disciplina eliminada com sucesso');
    }
    
    /**
     * Get disciplines by class (AJAX)
     */
    public function getByClass($classId)
    {
        $disciplines = $this->disciplineModel->getByClass($classId);
        
        return $this->response->setJSON($disciplines);
    }
    
    /**
     * Get available disciplines for class (AJAX)
     */
    public function getAvailableForClass($classId)
    {
        $classModel = new \App\Models\ClassModel();
        $class = $classModel->find($classId);
        
        if (!$class) {
            return $this->response->setJSON([]);
        }
        
        // Get disciplines already assigned
        $classDisciplineModel = new \App\Models\ClassDisciplineModel();
        $assigned = $classDisciplineModel
            ->where('class_id', $classId)
            ->findAll();
        
        $assignedIds = array_column($assigned, 'discipline_id');
        
        // Get all active disciplines
        $allDisciplines = $this->disciplineModel
            ->where('is_active', 1)
            ->orderBy('discipline_name', 'ASC')
            ->findAll();
        
        // Filter out assigned ones
        $available = array_filter($allDisciplines, function($discipline) use ($assignedIds) {
            return !in_array($discipline['id'], $assignedIds);
        });
        
        return $this->response->setJSON(array_values($available));
    }
    /**
 * Search disciplines by term (AJAX)
 * 
 * @param string|null $term Termo de busca (opcional, pode vir da query string)
 * @return JSON
 */
public function search($term = null)
{
    // Pega o termo da URL ou da query string
    if ($term === null) {
        $term = $this->request->getGet('q') ?? $this->request->getGet('term') ?? '';
    }
    
    // Log para debug
    log_message('debug', 'Search disciplines - Term: ' . $term);
    
    // Validação básica
    $term = trim($term);
    if (empty($term)) {
        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Termo de busca não informado',
            'data' => []
        ]);
    }
    
    // Se o termo for muito curto, retorna vazio
    if (strlen($term) < 2) {
        return $this->response->setJSON([
            'status' => 'success',
            'message' => 'Digite pelo menos 2 caracteres',
            'data' => []
        ]);
    }
    
    // Busca por LIKE em múltiplos campos
    $disciplines = $this->disciplineModel
        ->select('
            id,
            discipline_name,
            discipline_code,
            discipline_type,
            workload_hours,
            min_grade,
            max_grade,
            approval_grade,
            description,
            is_active,
            created_at,
            updated_at
        ')
        ->groupStart()
            ->like('discipline_name', $term)
            ->orLike('discipline_code', $term)
            ->orLike('discipline_type', $term)
            ->orLike('description', $term)
        ->groupEnd()
        ->orderBy('discipline_name', 'ASC')
        ->limit(50) // Limite para evitar sobrecarga
        ->findAll();
    
    // Log do resultado
    log_message('debug', 'Search found ' . count($disciplines) . ' disciplines');
    
    // Se quiser incluir contagem total sem limite
    $totalCount = $this->disciplineModel
        ->groupStart()
            ->like('discipline_name', $term)
            ->orLike('discipline_code', $term)
            ->orLike('discipline_type', $term)
            ->orLike('description', $term)
        ->groupEnd()
        ->countAllResults();
    
    // Formata os resultados para incluir informações úteis
    $formattedDisciplines = array_map(function($discipline) {
        return [
            'id' => $discipline['id'],
            'name' => $discipline['discipline_name'],
            'code' => $discipline['discipline_code'],
            'type' => $discipline->discipline_type,
            'workload' => $discipline->workload_hours,
            'min_grade' => $discipline->min_grade,
            'max_grade' => $discipline->max_grade,
            'approval_grade' => $discipline->approval_grade,
            'description' => $discipline->description,
            'is_active' => $discipline->is_active,
            'formatted' => $discipline['discipline_code'] . ' - ' . $discipline['discipline_name'],
            'badge' => [
                'type' => $discipline->is_active ? 'success' : 'secondary',
                'text' => $discipline->is_active ? 'Ativa' : 'Inativa'
            ]
        ];
    }, $disciplines);
    
    return $this->response->setJSON([
        'status' => 'success',
        'term' => $term,
        'total' => count($disciplines),
        'total_count' => $totalCount,
        'has_more' => $totalCount > count($disciplines),
        'data' => $formattedDisciplines
    ]);
}
/**
 * Advanced search disciplines with filters
 * 
 * @return JSON
 */
public function advancedSearch()
{
    // Pega os parâmetros da requisição
    $term = $this->request->getGet('q') ?? '';
    $type = $this->request->getGet('type') ?? '';
    $activeOnly = $this->request->getGet('active_only') === 'true';
    $minWorkload = $this->request->getGet('min_workload') ? (int)$this->request->getGet('min_workload') : null;
    $maxWorkload = $this->request->getGet('max_workload') ? (int)$this->request->getGet('max_workload') : null;
    $page = $this->request->getGet('page') ? (int)$this->request->getGet('page') : 1;
    $limit = $this->request->getGet('limit') ? (int)$this->request->getGet('limit') : 20;
    $offset = ($page - 1) * $limit;
    
    $builder = $this->disciplineModel;
    
    // Filtro por termo
    if (!empty($term)) {
        $builder->groupStart()
            ->like('discipline_name', $term)
            ->orLike('discipline_code', $term)
            ->orLike('description', $term)
            ->groupEnd();
    }
    
    // Filtro por tipo
    if (!empty($type)) {
        $builder->where('discipline_type', $type);
    }
    
    // Filtro por status
    if ($activeOnly) {
        $builder->where('is_active', 1);
    }
    
    // Filtro por carga horária
    if ($minWorkload !== null) {
        $builder->where('workload_hours >=', $minWorkload);
    }
    if ($maxWorkload !== null) {
        $builder->where('workload_hours <=', $maxWorkload);
    }
    
    // Contagem total
    $totalCount = $builder->countAllResults(false);
    
    // Paginação
    $disciplines = $builder
        ->orderBy('discipline_name', 'ASC')
        ->limit($limit, $offset)
        ->findAll();
    
    return $this->response->setJSON([
        'status' => 'success',
        'pagination' => [
            'page' => $page,
            'limit' => $limit,
            'total' => $totalCount,
            'pages' => ceil($totalCount / $limit)
        ],
        'filters' => [
            'term' => $term,
            'type' => $type,
            'active_only' => $activeOnly,
            'min_workload' => $minWorkload,
            'max_workload' => $maxWorkload
        ],
        'data' => $disciplines
    ]);
}
}