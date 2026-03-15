<?php
// app/Controllers/admin/Courses.php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\CourseModel;
use App\Models\GradeLevelModel;
use App\Models\DisciplineModel;
use App\Models\CourseDisciplineModel;

class Courses extends BaseController
{
    protected $courseModel;
    protected $gradeLevelModel;
    protected $disciplineModel;
    protected $courseDisciplineModel;
    
    public function __construct()
    {
        $this->courseModel = new CourseModel();
        $this->gradeLevelModel = new GradeLevelModel();
        $this->disciplineModel = new DisciplineModel();
        $this->courseDisciplineModel = new CourseDisciplineModel();
    }
    
    /**
     * List courses - Versão para DataTable server-side
     */
    public function index()
    {
        $data['title'] = 'Cursos (Ensino Médio)';
        
        // Carregar dados para filtros
        $data['types'] = [
            'Ciências' => 'Ciências',
            'Humanidades' => 'Humanidades',
            'Económico-Jurídico' => 'Económico-Jurídico',
            'Técnico' => 'Técnico',
            'Profissional' => 'Profissional',
            'Outro' => 'Outro'
        ];
        
        $data['academicYears'] = model('App\Models\AcademicYearModel')
            ->orderBy('year_name', 'DESC')
            ->findAll();
        
        return view('admin/courses/index', $data);
    }
    
    /**
     * GET DATA FOR DATATABLE (SERVER-SIDE)
     */
    public function getTableData()
    {
        $request = service('request');
        
        // Parâmetros do DataTable
        $draw = $request->getPost('draw');
        $start = $request->getPost('start') ?: 0;
        $length = $request->getPost('length') ?: 25;
        $search = $request->getPost('search')['value'] ?? '';
        
        // Parâmetros de ordenação
        $orderColumnIndex = $request->getPost('order')[0]['column'] ?? 0;
        $orderDir = $request->getPost('order')[0]['dir'] ?? 'asc';
        
        // Mapeamento de colunas
        $columns = [
            0 => 'id',
            1 => 'course_code',
            2 => 'course_name',
            3 => 'course_type',
            4 => 'start_grade_id',  // Será tratado no JOIN
            5 => 'duration_years',
            6 => 'disciplines_count',
            7 => 'is_active',
        ];
        
        $orderColumn = $columns[$orderColumnIndex] ?? 'id';
        
        // Filtros customizados
        $filterType = $request->getPost('filter_type');
        $filterStatus = $request->getPost('filter_status');
        $academicYear = $request->getPost('academic_year');
        
        // Query builder
        $builder = $this->courseModel
            ->select('courses.*')
            ->select('COUNT(DISTINCT course_disciplines.id) as disciplines_count')
            ->join('course_disciplines', 'course_disciplines.course_id = courses.id', 'left')
            ->groupBy('courses.id');
        
        // Aplicar filtros customizados
        if (!empty($filterType)) {
            $builder->where('courses.course_type', $filterType);
        }
        
        if ($filterStatus === 'active') {
            $builder->where('courses.is_active', 1);
        } elseif ($filterStatus === 'inactive') {
            $builder->where('courses.is_active', 0);
        }
        
        // Filtro por ano letivo (se necessário)
        if (!empty($academicYear)) {
            // Exemplo: filtrar cursos que têm turmas neste ano letivo
            // Adapte conforme sua lógica de negócio
        }
        
        // Aplicar busca global
        if (!empty($search)) {
            $builder->groupStart()
                ->like('courses.course_name', $search)
                ->orLike('courses.course_code', $search)
                ->orLike('courses.course_type', $search)
                ->orLike('courses.description', $search)
                ->groupEnd();
        }
        
        // Contar total de registros sem filtros
        $totalRecords = $this->courseModel->countAllResults();
        
        // Contar registros filtrados
        $filteredRecords = $builder->countAllResults(false);
        
        // Aplicar ordenação
        if ($orderColumn == 'disciplines_count') {
            $builder->orderBy('disciplines_count', $orderDir);
        } else {
            $builder->orderBy('courses.' . $orderColumn, $orderDir);
        }
        
        // Aplicar paginação
        $courses = $builder->findAll($length, $start);
        
        // Preparar dados para o DataTable
        $data = [];
        $gradeLevelModel = new GradeLevelModel();
        $courseDisciplineModel = new CourseDisciplineModel();
        
        foreach ($courses as $course) {
            // Buscar níveis
            $startLevel = $gradeLevelModel->find($course->start_grade_id);
            $endLevel = $gradeLevelModel->find($course->end_grade_id);
            
            // Total de disciplinas (já vem do COUNT, mas podemos confirmar)
            $totalDisciplines = $course->disciplines_count;
            
            // Definir classe do tipo
            $typeMap = [
                'Ciências'           => 'type-primary',
                'Humanidades'        => 'type-success',
                'Económico-Jurídico' => 'type-warning',
                'Técnico'            => 'type-info',
                'Profissional'       => 'type-secondary',
                'Outro'              => 'type-dark',
            ];
            $typeClass = $typeMap[$course->course_type] ?? 'type-secondary';
            
            // Montar array de dados
            $row = [];
            
            // ID
            $row['id_html'] = '<span class="row-id">' . $course->id . '</span>';
            
            // Código
            $row['code_html'] = '<span class="code-badge">' . esc($course->course_code) . '</span>';
            
            // Nome do Curso
            $nameHtml = '<div class="course-name">' . esc($course->course_name) . '</div>';
            if ($course->description) {
                $nameHtml .= '<div class="course-desc">' . character_limiter(esc($course->description), 50) . '</div>';
            }
            $row['name_html'] = $nameHtml;
            
            // Tipo
            $row['type_html'] = '<span class="type-badge ' . $typeClass . '">' . $course->course_type . '</span>';
            
            // Níveis
            $levelsHtml = '<div class="level-range">' . ($startLevel->level_name ?? 'N/A') . '</div>';
            $levelsHtml .= '<div class="level-sub">até ' . ($endLevel->level_name ?? 'N/A') . '</div>';
            $row['levels_html'] = $levelsHtml;
            
            // Duração
            $row['duration_html'] = '<span class="num-chip">' . $course->duration_years . 'a</span>';
            
            // Disciplinas
            $row['disciplines_html'] = '<span class="num-chip blue">' . $totalDisciplines . '</span>';
            
            // Status
            if ($course->is_active) {
                $row['status_html'] = '<span class="status-active"><span class="status-dot"></span>Ativo</span>';
            } else {
                $row['status_html'] = '<span class="status-inactive"><span class="status-dot"></span>Inativo</span>';
            }
            
            // Ações
            $actions = '<div class="action-group">';
            $actions .= '<a href="' . site_url('admin/courses/view/' . $course->id) . '" class="row-btn view" title="Ver Detalhes" data-bs-toggle="tooltip"><i class="fas fa-eye"></i></a>';
            $actions .= '<a href="' . site_url('admin/courses/form-edit/' . $course->id) . '" class="row-btn edit" title="Editar" data-bs-toggle="tooltip"><i class="fas fa-edit"></i></a>';
            $actions .= '<a href="' . site_url('admin/courses/curriculum/' . $course->id) . '" class="row-btn curr" title="Currículo" data-bs-toggle="tooltip"><i class="fas fa-book-open"></i></a>';
            $actions .= '<button type="button" class="row-btn del" onclick="confirmDelete(' . $course->id . ', \'' . esc($course->course_name, 'js') . '\')" title="Eliminar" data-bs-toggle="tooltip"><i class="fas fa-trash"></i></button>';
            $actions .= '</div>';
            
            $row['actions'] = $actions;
            
            // Dados brutos para ordenação
            $row['id'] = $course->id;
            $row['course_code'] = $course->course_code;
            $row['course_name'] = $course->course_name;
            $row['course_type'] = $course->course_type;
            $row['is_active'] = $course->is_active;
            $row['duration_years'] = $course->duration_years;
            $row['disciplines_count'] = $totalDisciplines;
            
            $data[] = $row;
        }
        
        // Retornar resposta JSON
        return $this->response->setJSON([
            'draw' => $draw,
            'recordsTotal' => $totalRecords,
            'recordsFiltered' => $filteredRecords,
            'data' => $data
        ]);
    }
    
    /**
     * Get statistics for dashboard cards
     */
    public function getStats()
    {
        $stats = $this->courseModel->getStatistics();
        
        // Adicionar contagem de tipos
        $builder = $this->courseModel->select('course_type, COUNT(*) as total')
            ->groupBy('course_type');
        
        $byType = $builder->findAll();
        
        return $this->response->setJSON([
            'success' => true,
            'total' => $stats['total'] ?? 0,
            'active' => $stats['active'] ?? 0,
            'inactive' => $stats['inactive'] ?? 0,
            'types_count' => count($byType),
            'by_type' => $byType
        ]);
    }
    
    /**
     * Course form (add/edit) - Mantido igual
     */
    public function form($id = null)
    {
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
     * Save course - Mantido igual
     */
    public function save()
    {
        $id = $this->request->getPost('id');
        
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
        
        // Validar que end_grade_id é maior que start_grade_id (regra de negócio)
        $startGrade = $data['start_grade_id'];
        $endGrade = $data['end_grade_id'];
        
        if ($endGrade <= $startGrade) {
            return redirect()->back()->withInput()
                ->with('error', 'O nível final deve ser maior que o nível inicial');
        }
        
        // Usar o model para salvar (ele fará a validação automaticamente)
        if ($this->courseModel->save($data)) {
            $message = $id ? 'Curso atualizado com sucesso' : 'Curso criado com sucesso';
            return redirect()->to('/admin/courses')->with('success', $message);
        } else {
            $errors = $this->courseModel->errors();
            return redirect()->back()->withInput()
                ->with('errors', $errors);
        }
    }
    
    /**
     * View course details with curriculum - Mantido igual
     */
    public function view($id)
    {
        $data['title'] = 'Detalhes do Curso';
        $data['course'] = $this->courseModel->getWithGradeLevels($id);
        
        if (!$data['course']) {
            return redirect()->to('/admin/courses')->with('error', 'Curso não encontrado');
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
     * Delete course - Mantido igual
     */
    public function delete($id)
    {
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
        
        return redirect()->to('/admin/courses')
            ->with('success', 'Curso eliminado com sucesso');
    }
}