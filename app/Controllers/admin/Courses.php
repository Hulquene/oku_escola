<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\CourseModel;
use App\Models\GradeLevelModel;
use App\Models\DisciplineModel;
use App\Models\CourseDisciplineModel;
use App\Models\AcademicYearModel;

class Courses extends BaseController
{
    protected $courseModel;
    protected $gradeLevelModel;
    protected $disciplineModel;
    protected $courseDisciplineModel;
    protected $academicYearModel;
    
    public function __construct()
    {
        $this->courseModel = new CourseModel();
        $this->gradeLevelModel = new GradeLevelModel();
        $this->disciplineModel = new DisciplineModel();
        $this->courseDisciplineModel = new CourseDisciplineModel();
        $this->academicYearModel = new AcademicYearModel();
    }
    
    /**
     * List courses - Carrega a view com dados para filtros
     */
    public function index()
    {
        // Verificar permissão
        if (!can('courses.list')) {
            return redirect()->to('/admin/dashboard')->with('error', 'Não tem permissão para ver cursos');
        }
        
        $data['title'] = 'Cursos (Ensino Médio)';
        
        // Dados para filtros
        $data['types'] = [
            'Ciências' => 'Ciências',
            'Humanidades' => 'Humanidades',
            'Económico-Jurídico' => 'Económico-Jurídico',
            'Técnico' => 'Técnico',
            'Profissional' => 'Profissional',
            'Outro' => 'Outro'
        ];
        
        $data['academicYears'] = $this->academicYearModel
            ->where('is_active', 1)
            ->orderBy('start_date', 'DESC')
            ->findAll();
        
        $data['gradeLevels'] = $this->gradeLevelModel
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->findAll();
        
        // Estatísticas iniciais
        $data['stats'] = $this->courseModel->getStatistics();
        
        // Valores selecionados (se vierem da URL)
        $data['selectedType'] = $this->request->getGet('type');
        $data['selectedStatus'] = $this->request->getGet('status');
        $data['selectedAcademicYear'] = $this->request->getGet('academic_year');
        $data['selectedGradeLevel'] = $this->request->getGet('grade_level');
        
        return view('admin/courses/index', $data);
    }
    
  /**
 * Retorna dados para o DataTables (AJAX)
 */
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
        $orderColumnIndex = 2;
        $orderDir = 'asc';

        if (is_array($order) && isset($order[0])) {
            $orderColumnIndex = (int)($order[0]['column'] ?? 2);
            $orderDir = $order[0]['dir'] ?? 'asc';
        }

        // Filtros
        $filterType   = $request->getPost('filter_type');
        $filterStatus = $request->getPost('filter_status');

        $columns = [
            0 => 'tbl_courses.id',
            1 => 'tbl_courses.course_code',
            2 => 'tbl_courses.course_name',
            3 => 'tbl_courses.course_type',
            4 => 'tbl_courses.start_grade_id',
            5 => 'tbl_courses.duration_years',
            6 => 'disciplines_count',
            7 => 'tbl_courses.is_active',
        ];

        $orderColumn = $columns[$orderColumnIndex] ?? 'tbl_courses.created_at';

        /*
        -------------------------------------------------
        QUERY BASE
        -------------------------------------------------
        */

        $builder = $this->courseModel
            ->select('
                tbl_courses.id,
                tbl_courses.course_name,
                tbl_courses.course_code,
                tbl_courses.course_type,
                tbl_courses.duration_years,
                tbl_courses.start_grade_id,
                tbl_courses.end_grade_id,
                tbl_courses.description,
                tbl_courses.is_active,
                COUNT(DISTINCT tbl_course_disciplines.id) as disciplines_count
            ')
            ->join(
                'tbl_course_disciplines',
                'tbl_course_disciplines.course_id = tbl_courses.id',
                'left'
            )
            ->groupBy('tbl_courses.id');

        /*
        -------------------------------------------------
        FILTROS
        -------------------------------------------------
        */

        if (!empty($filterType)) {
            $builder->where('tbl_courses.course_type', $filterType);
        }

        if ($filterStatus === 'active') {
            $builder->where('tbl_courses.is_active', 1);
        } elseif ($filterStatus === 'inactive') {
            $builder->where('tbl_courses.is_active', 0);
        }

        if (!empty($searchValue)) {
            $builder->groupStart()
                ->like('tbl_courses.course_name', $searchValue)
                ->orLike('tbl_courses.course_code', $searchValue)
                ->orLike('tbl_courses.course_type', $searchValue)
                ->orLike('tbl_courses.description', $searchValue)
                ->groupEnd();
        }

        /*
        -------------------------------------------------
        EXECUTAR QUERY (COMO ARRAY)
        -------------------------------------------------
        */

        $results = $builder->get()->getResultArray(); // Mudei para getResultArray()

        /*
        -------------------------------------------------
        CONTAGEM FILTRADA
        -------------------------------------------------
        */

        $recordsFiltered = count($results);

        /*
        -------------------------------------------------
        TOTAL SEM FILTRO
        -------------------------------------------------
        */

        $totalRecords = $this->courseModel->countAll();

        /*
        -------------------------------------------------
        ORDENAÇÃO EM MEMÓRIA (adaptada para array)
        -------------------------------------------------
        */

        usort($results, function ($a, $b) use ($orderColumn, $orderDir) {

            $field = str_replace('tbl_courses.', '', $orderColumn);
            
            // Acessar como array
            $valA = $a[$field] ?? null;
            $valB = $b[$field] ?? null;

            if ($valA == $valB) return 0;

            if ($orderDir === 'asc') {
                return ($valA < $valB) ? -1 : 1;
            } else {
                return ($valA > $valB) ? -1 : 1;
            }
        });

        /*
        -------------------------------------------------
        PAGINAÇÃO EM MEMÓRIA
        -------------------------------------------------
        */

        $courses = array_slice($results, $start, $length);

        /*
        -------------------------------------------------
        FORMATAR RESULTADOS - AGORA COMO ARRAY
        -------------------------------------------------
        */

        $data = [];
        
        // Cache para níveis (evita múltiplas consultas)
        $levelCache = [];
        
        // Mapeamento de cores para tipos
        $typeClassMap = [
            'Ciências' => 'type-primary',
            'Humanidades' => 'type-success',
            'Económico-Jurídico' => 'type-warning',
            'Técnico' => 'type-info',
            'Profissional' => 'type-secondary',
            'Outro' => 'type-dark',
        ];

        foreach ($courses as $course) {
            
            // Buscar níveis com cache - ACESSANDO COMO ARRAY
            $startLevel = $this->getCachedLevel($this->gradeLevelModel, $levelCache, $course['start_grade_id']);
            $endLevel = $this->getCachedLevel($this->gradeLevelModel, $levelCache, $course['end_grade_id']);
            
            $row = [];

            // ID - ACESSANDO COMO ARRAY
            $row['id_html'] = '<span class="id-chip">#' . str_pad($course['id'], 4, '0', STR_PAD_LEFT) . '</span>';

            // Código - ACESSANDO COMO ARRAY
            $row['code_html'] = '<span class="code-badge">' . esc($course['course_code']) . '</span>';

            // Nome do Curso - ACESSANDO COMO ARRAY
            $nameHtml = '<div class="fw-600">' . esc($course['course_name']) . '</div>';
            if (!empty($course['description'])) {
                $nameHtml .= '<div class="small text-muted">' . esc(character_limiter($course['description'], 50)) . '</div>';
            }
            $row['name_html'] = $nameHtml;

            // Tipo com cor - ACESSANDO COMO ARRAY
            $typeClass = $typeClassMap[$course['course_type']] ?? 'type-secondary';
            $typeIcon = $this->getTypeIcon($course['course_type']);
            $row['type_html'] = '<span class="badge-ci ' . $typeClass . '"><i class="fas ' . $typeIcon . ' me-1"></i>' . esc($course['course_type']) . '</span>';

            // Níveis com nomes - ACESSANDO COMO ARRAY
            $startLevelName = $startLevel ? $startLevel['level_name'] : 'N/A';
            $endLevelName = $endLevel ? $endLevel['level_name'] : 'N/A';
            
            $row['levels_html'] = '<span class="fw-semibold">' . esc($startLevelName) . '</span><br><small class="text-muted">até ' . esc($endLevelName) . '</small>';

            // Duração - ACESSANDO COMO ARRAY
            $row['duration_html'] = '<span class="code-badge">' . (int)$course['duration_years'] . ' anos</span>';

            // Disciplinas - ACESSANDO COMO ARRAY
            $row['disciplines_html'] = '<span class="badge-ci info">' . (int)($course['disciplines_count'] ?? 0) . ' disc.</span>';

            // Status - ACESSANDO COMO ARRAY
            if ($course['is_active']) {
                $row['status_html'] = '<span class="status-badge active"><i class="fas fa-circle me-1" style="font-size: 6px;"></i>Ativo</span>';
            } else {
                $row['status_html'] = '<span class="status-badge inactive"><i class="fas fa-circle me-1" style="font-size: 6px;"></i>Inativo</span>';
            }

            // Ações - ACESSANDO COMO ARRAY
            $actions  = '<div class="action-group">';
            $actions .= '<a href="' . site_url('admin/academic/courses/view/' . $course['id']) . '" class="row-btn view" title="Ver Detalhes" data-bs-toggle="tooltip"><i class="fas fa-eye"></i></a>';
            $actions .= '<a href="' . site_url('admin/academic/courses/form-edit/' . $course['id']) . '" class="row-btn edit" title="Editar" data-bs-toggle="tooltip"><i class="fas fa-edit"></i></a>';
            $actions .= '<a href="' . site_url('admin/academic/courses/curriculum/' . $course['id']) . '" class="row-btn curr" title="Currículo" data-bs-toggle="tooltip"><i class="fas fa-book-open"></i></a>';
            
            if ($course['is_active']) {
                $actions .= '<button type="button" class="row-btn toggle-status" data-id="' . $course['id'] . '" data-status="1" data-name="' . esc($course['course_name'], 'js') . '" title="Desativar" data-bs-toggle="tooltip"><i class="fas fa-ban"></i></button>';
            } else {
                $actions .= '<button type="button" class="row-btn toggle-status" data-id="' . $course['id'] . '" data-status="0" data-name="' . esc($course['course_name'], 'js') . '" title="Ativar" data-bs-toggle="tooltip"><i class="fas fa-check-circle"></i></button>';
            }
            
            $actions .= '<button type="button" class="row-btn del" onclick="confirmDelete(' . $course['id'] . ', \'' . esc($course['course_name'], 'js') . '\')" title="Eliminar" data-bs-toggle="tooltip"><i class="fas fa-trash"></i></button>';
            $actions .= '</div>';

            $row['actions'] = $actions;

            $data[] = $row;
        }

        /*
        -------------------------------------------------
        RESPOSTA FINAL
        -------------------------------------------------
        */

        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ]);

    } catch (\Exception $e) {

        log_message('error', 'Erro DataTables Courses: ' . $e->getMessage() . ' on line ' . $e->getLine() . ' in ' . $e->getFile());

        return $this->response->setStatusCode(500)->setJSON([
            'error' => true,
            'message' => 'Erro ao carregar dados: ' . $e->getMessage()
        ]);
    }
}

/**
 * Helper para obter nível em cache (retorna array)
 */
private function getCachedLevel($model, &$cache, $id)
{
    if (empty($id)) {
        return null;
    }
    
    if (!isset($cache[$id])) {
        $cache[$id] = $model->find($id); // Isso já retorna array
    }
    
    return $cache[$id];
}

/**
 * Helper para obter ícone do tipo de curso
 */
private function getTypeIcon($type)
{
    $icons = [
        'Ciências' => 'fa-flask',
        'Humanidades' => 'fa-book',
        'Económico-Jurídico' => 'fa-scale-balanced',
        'Técnico' => 'fa-gear',
        'Profissional' => 'fa-briefcase',
        'Outro' => 'fa-ellipsis',
    ];
    
    return $icons[$type] ?? 'fa-book';
}

   

    /**
     * Retorna estatísticas para os cards (AJAX)
     */
    public function getStats()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([]);
        }
        
        try {
            $stats = $this->courseModel->getStatistics();
            
            // Distribuição por tipo
            $byType = $this->courseModel
                ->select('course_type, COUNT(*) as total')
                ->groupBy('course_type')
                ->findAll();
            
            return $this->response->setJSON([
                'success' => true,
                'total' => (int)($stats['total'] ?? 0),
                'active' => (int)($stats['active'] ?? 0),
                'inactive' => (int)($stats['inactive'] ?? 0),
                'types_count' => count($byType),
                'by_type' => $byType
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Erro ao carregar estatísticas de cursos: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'total' => 0,
                'active' => 0,
                'inactive' => 0,
                'types_count' => 0,
                'by_type' => []
            ]);
        }
    }
    
    /**
     * Course form (add/edit)
     */
    public function form($id = null)
    {
        // Verificar permissão
        if (!can($id ? 'courses.edit' : 'courses.create')) {
            return redirect()->back()->with('error', 'Sem permissão para esta ação');
        }
        
        $data['title'] = $id ? 'Editar Curso' : 'Novo Curso';
        $data['course'] = $id ? $this->courseModel->find($id) : null;
        
        // Níveis de ensino para seleção
        $data['gradeLevels'] = $this->gradeLevelModel
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->findAll();
        
        // Níveis de ensino médio (para início/fim)
        $data['highSchoolLevels'] = $this->gradeLevelModel
            ->whereIn('education_level', ['2º Ciclo', 'Ensino Médio'])
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->findAll();
        
        return view('admin/courses/form', $data);
    }
    
    /**
     * Save course
     */
    public function save()
    {
        $id = $this->request->getPost('id');
        
        // Verificar permissão
        if (!can($id ? 'courses.edit' : 'courses.create')) {
            return redirect()->back()->with('error', 'Sem permissão para esta ação');
        }
        
        // Coletar dados do formulário
        $data = [
            'course_name' => $this->request->getPost('course_name'),
            'course_code' => $this->request->getPost('course_code'),
            'course_type' => $this->request->getPost('course_type'),
            'description' => $this->request->getPost('description'),
            'duration_years' => $this->request->getPost('duration_years') ?: 3,
            'start_grade_id' => $this->request->getPost('start_grade_id'),
            'end_grade_id' => $this->request->getPost('end_grade_id'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];
        
        // Se for edição, incluir o ID
        if ($id) {
            $data['id'] = $id;
        }
        
        // Validar que end_grade_id é maior que start_grade_id
        $startGrade = $data['start_grade_id'];
        $endGrade = $data['end_grade_id'];
        
        if ($endGrade <= $startGrade) {
            return redirect()->back()->withInput()
                ->with('error', 'O nível final deve ser maior que o nível inicial');
        }
        
        // Usar o model para salvar
        if ($this->courseModel->save($data)) {
            $message = $id ? 'Curso atualizado com sucesso' : 'Curso criado com sucesso';
            return redirect()->to('/admin/academic/courses')->with('success', $message);
        } else {
            $errors = $this->courseModel->errors();
            return redirect()->back()->withInput()
                ->with('errors', $errors);
        }
    }
    
    /**
     * View course details with curriculum
     */
    public function view($id)
    {
        if (!can('courses.view')) {
            return redirect()->back()->with('error', 'Sem permissão para ver detalhes do curso');
        }
        
        $data['title'] = 'Detalhes do Curso';
        $data['course'] = $this->courseModel->getWithGradeLevels($id);
        
        if (!$data['course']) {
            return redirect()->to('/admin/academic/courses')->with('error', 'Curso não encontrado');
        }
        
        // Buscar currículo completo do curso
        $data['curriculum'] = $this->courseDisciplineModel->getFullCourseCurriculum($id);
        
        // Agrupar por nível
        $groupedCurriculum = [];
        foreach ($data['curriculum'] as $item) {
            $levelId = $item->grade_level_id;
            if (!isset($groupedCurriculum[$levelId])) {
                $groupedCurriculum[$levelId] = [
                    'level_name' => $item->level_name,
                    'disciplines' => []
                ];
            }
            $groupedCurriculum[$levelId]['disciplines'][] = $item;
        }
        $data['groupedCurriculum'] = $groupedCurriculum;
        
        // Estatísticas
        $totalWorkload = 0;
        foreach ($data['curriculum'] as $item) {
            $totalWorkload += $item->workload_hours ?? 0;
        }
        $data['totalWorkload'] = $totalWorkload;
        $data['totalDisciplines'] = count($data['curriculum']);
        
        return view('admin/courses/view', $data);
    }
    
    /**
     * Delete course
     */
    public function delete($id)
    {
        if (!can('courses.delete')) {
            return redirect()->back()->with('error', 'Sem permissão para eliminar cursos');
        }
        
        $course = $this->courseModel->find($id);
        
        if (!$course) {
            return redirect()->back()->with('error', 'Curso não encontrado');
        }
        
        // Verificar se há turmas usando este curso
        $classModel = new \App\Models\ClassModel();
        $classes = $classModel->where('course_id', $id)->countAllResults();
        
        if ($classes > 0) {
            return redirect()->back()
                ->with('error', "Não é possível eliminar este curso porque existem {$classes} turmas associadas");
        }
        
        // Verificar se há matrículas usando este curso
        $enrollmentModel = new \App\Models\EnrollmentModel();
        $enrollments = $enrollmentModel->where('course_id', $id)->countAllResults();
        
        if ($enrollments > 0) {
            return redirect()->back()
                ->with('error', "Não é possível eliminar este curso porque existem {$enrollments} matrículas associadas");
        }
        
        $this->courseModel->delete($id);
        
        return redirect()->to('/admin/academic/courses')
            ->with('success', 'Curso eliminado com sucesso');
    }
}