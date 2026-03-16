<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\EnrollmentModel;
use App\Models\StudentModel;
use App\Models\ClassModel;
use App\Models\AcademicYearModel;
use App\Models\FeeStructureModel;
use App\Models\GradeLevelModel;
use App\Models\CourseModel;
use App\Models\StudentFeeModel;

class Enrollments extends BaseController
{
    protected $enrollmentModel;
    protected $studentModel;
    protected $classModel;
    protected $academicYearModel;
    protected $feeStructureModel;
    protected $gradeLevelModel;
    protected $courseModel;
    
    public function __construct()
    {
        $this->enrollmentModel = new EnrollmentModel();
        $this->studentModel = new StudentModel();
        $this->classModel = new ClassModel();
        $this->academicYearModel = new AcademicYearModel();
        $this->feeStructureModel = new FeeStructureModel();
        $this->gradeLevelModel = new GradeLevelModel();
        $this->courseModel = new CourseModel();        
    }
    
    /**
     * List enrollments - Versão simplificada (apenas carrega a view)
     */
    public function index()
    {
        // Verificar permissão
        if (!$this->hasPermission('view_enrollments')) {
            return redirect()->to('/admin/dashboard')->with('error', 'Não tem permissão para ver matrículas');
        }
        
        $data['title'] = 'Matrículas';
        
        // Dados para filtros
        $data['academicYears'] = $this->academicYearModel
            ->where('is_active', 1)
            ->orderBy('start_date', 'DESC')
            ->findAll();
        
        $data['gradeLevels'] = $this->gradeLevelModel
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->findAll();
        
        $data['courses'] = $this->courseModel->getHighSchoolCourses();
        
        // Buscar ano letivo atual para o filtro padrão
       /*  $currentYear = $this->academicYearModel
            ->where('id', current_academic_year())
            ->where('is_active', 1)
            ->first(); */
        
        // Se não houver ano selecionado na URL, usar o ano atual
        $selectedYear = $this->request->getGet('academic_year') ?? current_academic_year();
       /*  if (empty($selectedYear) && $currentYear) {
            $selectedYear = $currentYear['id'];
        } */
        
        $data['selectedYear'] = $selectedYear;
        $data['selectedGradeLevel'] = $this->request->getGet('grade_level');
        $data['selectedCourse'] = $this->request->getGet('course');
        $data['selectedClass'] = $this->request->getGet('class_id');
        $data['selectedStatus'] = $this->request->getGet('status');
        
        return view('admin/students/enrollments/index', $data);
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
            
            // Parâmetros do DataTables
            $draw = (int)($request->getPost('draw') ?? 0);
            $start = (int)($request->getPost('start') ?? 0);
            $length = (int)($request->getPost('length') ?? 10);
            
            // Search
            $search = $request->getPost('search');
            $searchValue = is_array($search) ? ($search['value'] ?? '') : '';
            
            // Order
            $order = $request->getPost('order');
            $orderColumnIndex = 6; // padrão: data de matrícula
            $orderDir = 'desc';
            
            if (is_array($order) && isset($order[0])) {
                $orderColumnIndex = (int)($order[0]['column'] ?? 6);
                $orderDir = $order[0]['dir'] ?? 'desc';
            }
            
            // Filtros adicionais
            $academicYearId = $request->getPost('academic_year');
            $gradeLevelId = $request->getPost('grade_level');
            $classId = $request->getPost('class_id');
            $status = $request->getPost('status');
            $courseId = $request->getPost('course');
            
            // Colunas para ordenação
            $columns = [
                0 => 'tbl_enrollments.enrollment_number',
                1 => 'tbl_users.first_name',
                2 => 'tbl_grade_levels.level_name',
                3 => 'tbl_courses.course_name',
                4 => 'tbl_classes.class_name',
                5 => 'tbl_academic_years.year_name',
                6 => 'tbl_enrollments.enrollment_date',
                7 => 'tbl_enrollments.enrollment_type',
                8 => 'tbl_enrollments.status',
            ];
            
            $orderColumn = $columns[$orderColumnIndex] ?? 'tbl_enrollments.created_at';
            
            // Query principal
            $builder = $this->enrollmentModel
                ->select('
                    tbl_enrollments.*,
                    tbl_users.first_name,
                    tbl_users.last_name,
                    tbl_students.student_number,
                    tbl_students.photo as student_photo,
                    tbl_classes.class_name,
                    tbl_classes.class_shift,
                    tbl_grade_levels.level_name,
                    tbl_academic_years.year_name,
                    tbl_courses.id as course_id,
                    tbl_courses.course_name,
                    tbl_courses.course_code,
                    tbl_courses.course_type,
                    CONCAT(tbl_users.first_name, " ", tbl_users.last_name) as student_name
                ')
                ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
                ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
                ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id', 'left')
                ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_enrollments.grade_level_id', 'left')
                ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_enrollments.academic_year_id')
                ->join('tbl_courses', 'tbl_courses.id = tbl_enrollments.course_id', 'left');
            
            // Aplicar filtros
            if (!empty($academicYearId)) {
                $builder->where('tbl_enrollments.academic_year_id', $academicYearId);
            }
            
            if (!empty($gradeLevelId)) {
                $builder->where('tbl_enrollments.grade_level_id', $gradeLevelId);
            }
            
            if (!empty($classId)) {
                $builder->where('tbl_enrollments.class_id', $classId);
            }
            
            if (!empty($status)) {
                $builder->where('tbl_enrollments.status', $status);
            }
            
            if ($courseId !== null && $courseId !== '') {
                if ($courseId === '0') {
                    $builder->where('tbl_enrollments.course_id IS NULL');
                } else {
                    $builder->where('tbl_enrollments.course_id', $courseId);
                }
            }
            
            // Aplicar busca
            if (!empty($searchValue)) {
                $builder->groupStart()
                    ->like('tbl_enrollments.enrollment_number', $searchValue)
                    ->orLike('tbl_users.first_name', $searchValue)
                    ->orLike('tbl_users.last_name', $searchValue)
                    ->orLike('tbl_students.student_number', $searchValue)
                    ->orLike('tbl_classes.class_name', $searchValue)
                    ->orLike('tbl_courses.course_name', $searchValue)
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
                // Número da matrícula
                $row->enrollment_number_html = '<span class="student-number">' . ($row->enrollment_number ?? '') . '</span>';
                
                // Nome do aluno
                $row->student_name_html = '<strong>' . $row->first_name . ' ' . $row->last_name . '</strong>' .
                                          '<br><small class="text-muted">' . $row->student_number . '</small>';
                
                // Nível
                $row->level_html = !empty($row->level_name) ? 
                    '<span class="badge bg-info">' . $row->level_name . '</span>' : 
                    '<span class="text-muted">-</span>';
                
                // Curso
                if (!empty($row->course_name)) {
                    $row->course_html = '<span class="badge bg-primary">' . $row->course_name . '</span>' .
                                        '<br><small class="text-muted">' . ($row->course_code ?? '') . '</small>';
                } else {
                    $row->course_html = '<span class="badge bg-secondary">Ensino Geral</span>';
                }
                
                // Turma
                if (!empty($row->class_name)) {
                    $row->class_html = '<span class="badge bg-success">' . $row->class_name . '</span>' .
                                       '<br><small class="text-muted">' . ($row->class_shift ?? '') . '</small>';
                } else {
                    $row->class_html = '<span class="badge bg-secondary">Não atribuída</span>';
                }
                
                // Data
                $row->enrollment_date_html = date('d/m/Y', strtotime($row->enrollment_date));
                
                // Tipo
                $row->enrollment_type_html = '<span class="badge bg-secondary">' . $row->enrollment_type . '</span>';
                
                // Status
                $statusClass = [
                    'Ativo' => 'success',
                    'Pendente' => 'warning',
                    'Concluído' => 'info',
                    'Transferido' => 'primary',
                    'Cancelado' => 'danger'
                ][$row->status] ?? 'secondary';
                
                $statusIcon = [
                    'Ativo' => 'check-circle',
                    'Pendente' => 'clock',
                    'Concluído' => 'check-double',
                    'Transferido' => 'exchange-alt',
                    'Cancelado' => 'ban'
                ][$row->status] ?? 'circle';
                
                $row->status_html = '<span class="badge bg-' . $statusClass . ' p-2">' .
                                    '<i class="fas fa-' . $statusIcon . ' me-1"></i>' . $row->status . '</span>';
                
                // Ações
                $actions = '<div class="action-group">';
                $actions .= '<a href="' . site_url('admin/students/enrollments/view/' . $row->id) . '" ' .
                           'class="row-btn view" title="Ver Detalhes"><i class="fas fa-eye"></i></a>';
                $actions .= '<a href="' . site_url('admin/students/enrollments/form-edit/' . $row->id) . '" ' .
                           'class="row-btn edit" title="Editar"><i class="fas fa-edit"></i></a>';
                $actions .= '<a href="' . site_url('admin/students/view/' . $row->student_id) . '" ' .
                           'class="row-btn view" title="Ver Aluno"><i class="fas fa-user-graduate"></i></a>';
                
                if ($row->status == 'Pendente') {
                    $actions .= '<a href="' . site_url('admin/students/enrollments/approve/' . $row->id) . '" ' .
                               'class="row-btn edit" title="Aprovar Matrícula" ' .
                               'onclick="return confirm(\'Confirmar aprovação desta matrícula?\')">' .
                               '<i class="fas fa-check-circle"></i></a>';
                }
                
                if (!in_array($row->status, ['Ativo', 'Concluído'])) {
                    $actions .= '<button type="button" class="row-btn del" ' .
                               'onclick="confirmDelete(' . $row->id . ')" title="Eliminar">' .
                               '<i class="fas fa-trash"></i></button>';
                }
                
                $actions .= '</div>';
                $row->actions = $actions;
            }
            
            // Total de registros sem filtros
            $totalRecords = $this->enrollmentModel
                ->where('status', 'Ativo')
                ->countAllResults();
            
            return $this->response->setJSON([
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $recordsFiltered,
                'data' => $data
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Erro no DataTables de matrículas: ' . $e->getMessage());
            
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
        
        $totalEnrollments = $this->enrollmentModel->countAll();
        $activeEnrollments = $this->enrollmentModel->where('status', 'Ativo')->countAllResults();
        $pendingEnrollments = $this->enrollmentModel->where('status', 'Pendente')->countAllResults();
        $completedEnrollments = $this->enrollmentModel->where('status', 'Concluído')->countAllResults();
        
        return $this->response->setJSON([
            'totalEnrollments' => $totalEnrollments,
            'activeEnrollments' => $activeEnrollments,
            'pendingEnrollments' => $pendingEnrollments,
            'completedEnrollments' => $completedEnrollments
        ]);
    }
    
    /**
     * Retorna lista de turmas por ano letivo (AJAX)
     */
    public function getClassesByYear($yearId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([]);
        }
        
        $classes = $this->classModel
            ->select('tbl_classes.id, tbl_classes.class_name, tbl_classes.class_code, tbl_classes.class_shift')
            ->where('tbl_classes.academic_year_id', $yearId)
            ->where('tbl_classes.is_active', 1)
            ->orderBy('tbl_classes.class_name', 'ASC')
            ->findAll();
        
        return $this->response->setJSON($classes);
    }


/**
 * Enrollment form - Requer permissão 'create_enrollments' ou 'edit_enrollments'
 */
public function form($id = null)
{
    // Verificar permissão específica baseada na ação
    if ($id) {
        // Editando matrícula existente
        if (!$this->hasPermission('edit_enrollments')) {
            return redirect()->to('/admin/students/enrollments')->with('error', 'Não tem permissão para editar matrículas');
        }
    } else {
        // Criando nova matrícula
        if (!$this->hasPermission('create_enrollments')) {
            return redirect()->to('/admin/students/enrollments')->with('error', 'Não tem permissão para criar matrículas');
        }
    }
    
    $data['title'] = $id ? 'Editar Matrícula' : 'Nova Matrícula';
    $data['enrollment'] = $id ? $this->enrollmentModel->getWithDetails($id) : null;
    
    
    $currentYear = $this->academicYearModel->getCurrent();
    
    // Verificar se existe ano letivo atual
    if (!$currentYear) {
        return redirect()->to('/admin/academic/years')
            ->with('error', 'É necessário definir um ano letivo atual antes de fazer matrículas.');
    }
    
    if ($id) {
        // Buscar alunos ativos
        $data['students'] = $this->studentModel
            ->select('tbl_students.id, tbl_users.first_name, tbl_users.last_name, tbl_students.student_number')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->where('tbl_students.is_active', 1)
            ->where('tbl_students.id', $data['enrollment']['student_id'])
            ->first();
    } else {
        // Buscar alunos ativos
        $data['students'] = $this->studentModel
            ->select('tbl_students.id, tbl_users.first_name, tbl_users.last_name, tbl_students.student_number')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->where('tbl_students.is_active', 1)
            ->orderBy('tbl_users.first_name', 'ASC')
            ->findAll();
    }

    $data['gradeLevels'] = $this->gradeLevelModel
        ->where('is_active', 1)
        ->orderBy('sort_order', 'ASC')
        ->findAll();
    
    // Buscar anos letivos ativos
    $data['academicYears'] = $this->academicYearModel
        ->where('is_active', 1)
        ->orderBy('start_date', 'DESC')
        ->findAll();
    
    $data['classes'] = $this->classModel
        ->where('is_active', 1)
        ->findAll();

    // Buscar cursos para ensino médio
    $data['courses'] = $this->courseModel->getHighSchoolCourses(); 

    $data['currentYear'] = $currentYear;
    
    // Buscar turmas disponíveis baseadas no ano e nível selecionados
    $classQuery = $this->classModel
        ->select('
            tbl_classes.*, 
            tbl_grade_levels.level_name,
            tbl_courses.course_name,
            tbl_courses.course_code,
            (SELECT COUNT(*) FROM tbl_enrollments 
             WHERE class_id = tbl_classes.id 
             AND status = "Ativo") as enrolled_count
        ')
        ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
        ->join('tbl_courses', 'tbl_courses.id = tbl_classes.course_id', 'left')
        ->where('tbl_classes.is_active', 1);
    
    $classes = $classQuery->orderBy('tbl_classes.class_name', 'ASC')->findAll();
    
    // Calcular vagas disponíveis para cada turma
    foreach ($classes as $class) {
        $class['enrolled_count']  = $class['enrolled_count']  ?? 0;
        $class['available_seats']  = $class['capacity']  - $class['enrolled_count'] ;
    }
    
    $data['classes_enrolled_count'] = $classes;
    
    return view('admin/students/enrollments/form', $data);
}

/**
 * Save enrollment - Requer permissão 'create_enrollments' ou 'edit_enrollments'
 */
public function save()
{
    $id = $this->request->getPost('id');
    
    // Verificar permissão baseada na ação
    if ($id) {
        if (!$this->hasPermission('edit_enrollments')) {
            return redirect()->to('/admin/students/enrollments')->with('error', 'Não tem permissão para editar matrículas');
        }
    } else {
        if (!$this->hasPermission('create_enrollments')) {
            return redirect()->to('/admin/students/enrollments')->with('error', 'Não tem permissão para criar matrículas');
        }
    }
    
    // Regras de validação
    $rules = [
        'student_id' => 'required|numeric',
        'academic_year_id' => 'required|numeric',
        'grade_level_id' => 'required|numeric',
        'enrollment_date' => 'required|valid_date',
        'enrollment_type' => 'required'
    ];
    
    $status = $this->request->getPost('status');
    $classId = $this->request->getPost('class_id');
    
    // Se for matrícula ativa, class_id é obrigatório
    if ($status == 'Ativo') {
        $rules['class_id'] = 'required|numeric';
    }
    
    if (!$this->validate($rules)) {
        return redirect()->back()->withInput()
            ->with('errors', $this->validator->getErrors());
    }
    
    $studentId = $this->request->getPost('student_id');
    $academicYearId = $this->request->getPost('academic_year_id');
    $status = $this->request->getPost('status') ?: 'Pendente';
    
    // Se for matrícula ativa, verificar capacidade da turma
    if ($status == 'Ativo' && $classId) {
        $availableSeats = $this->classModel->getAvailableSeats($classId);
        if ($availableSeats <= 0) {
            return redirect()->back()->withInput()
                ->with('error', 'Turma sem vagas disponíveis');
        }
    }
    
    // Verificar se já existe matrícula ativa (exceto para a mesma matrícula em edição)
    if ($status == 'Ativo') {
        $existing = $this->enrollmentModel
            ->where('student_id', $studentId)
            ->where('academic_year_id', $academicYearId)
            ->where('status', 'Ativo');
        
        if ($id) {
            $existing->where('id !=', $id);
        }
        
        if ($existing->first()) {
            return redirect()->back()->withInput()
                ->with('error', 'Aluno já possui matrícula ativa neste ano letivo');
        }
    }
    
    $data = [
        'student_id' => $studentId,
        'class_id' => $classId ?: null,
        'academic_year_id' => $academicYearId,
        'grade_level_id' => $this->request->getPost('grade_level_id'),
        'course_id' => $this->request->getPost('course_id') ?: null,
        'enrollment_date' => $this->request->getPost('enrollment_date'),
        'enrollment_type' => $this->request->getPost('enrollment_type'),
        'previous_grade_id' => $this->request->getPost('previous_grade_id') ?: null,
        'status' => $status,
        'observations' => $this->request->getPost('observations'),
        'created_by' => $this->session->get('user_id')
    ];
    
    if ($id) {
        // Não alterar número de matrícula na edição
        unset($data['enrollment_number']);
        
        if ($this->enrollmentModel->update($id, $data)) {
            $message = 'Matrícula atualizada com sucesso';
            
            // Log da ação
            log_message('info', "Matrícula ID {$id} atualizada para status {$status}");
        } else {
            $errors = $this->enrollmentModel->errors();
            return redirect()->back()->withInput()
                ->with('errors', $errors);
        }
    } else {
        $data['enrollment_number'] = $this->enrollmentModel->generateEnrollmentNumber();
        
        $newId = $this->enrollmentModel->insert($data);
        if ($newId) {
            $message = 'Matrícula realizada com sucesso';
            
            // Log da ação
            log_message('info', "Nova matrícula ID {$newId} criada para aluno ID {$studentId}");
        } else {
            $errors = $this->enrollmentModel->errors();
            return redirect()->back()->withInput()
                ->with('errors', $errors);
        }
    }
    
    // Redirecionar baseado no status
    return redirect()->to('/admin/students/enrollments')
        ->with('success', $message);
}

/**
 * View enrollment - Requer permissão 'view_enrollments'
 */
public function view($id)
{
    if (!$this->hasPermission('view_enrollments')) {
        return redirect()->to('/admin/students/enrollments')->with('error', 'Não tem permissão para ver detalhes da matrícula');
    }
    
    $data['title'] = 'Detalhes da Matrícula';
    $data['enrollment'] = $this->enrollmentModel->getForView($id);
    
    if (!$data['enrollment']) {
        return redirect()->to('/admin/students/enrollments')->with('error', 'Matrícula não encontrada');
    }
    
    // Get student fees
    $studentFeeModel = new \App\Models\StudentFeeModel();
    $data['fees'] = $studentFeeModel
        ->select('tbl_student_fees.*, tbl_fee_types.type_name')
        ->join('tbl_fee_structure', 'tbl_fee_structure.id = tbl_student_fees.fee_structure_id')
        ->join('tbl_fee_types', 'tbl_fee_types.id = tbl_fee_structure.fee_type_id')
        ->where('tbl_student_fees.enrollment_id', $id)
        ->orderBy('tbl_student_fees.due_date', 'ASC')
        ->findAll();
    
    return view('admin/students/enrollments/view', $data);
}

/**
 * Student history - Requer permissão 'view_history'
 */
public function history($studentId)
{
    if (!$this->hasPermission('view_history')) {
        return redirect()->to('/admin/students/enrollments')->with('error', 'Não tem permissão para ver histórico do aluno');
    }
    
    $student = $this->studentModel->getWithUser($studentId);
    
    if (!$student) {
        return redirect()->to('/admin/students')->with('error', 'Aluno não encontrado');
    }
    
    $data['title'] = 'Histórico do Aluno: ' . $student['first_name'] . ' ' . $student['last_name'];
    $data['student'] = $student;
    $data['history'] = $this->enrollmentModel->getStudentHistory($studentId);
    
    return view('admin/students/enrollments/history', $data);
}

/**
 * Delete enrollment - Requer permissão 'delete_enrollments'
 */
public function delete($id)
{
    if (!$this->hasPermission('delete_enrollments')) {
        return redirect()->to('/admin/students/enrollments')->with('error', 'Não tem permissão para eliminar matrículas');
    }
    
    $enrollment = $this->enrollmentModel->find($id);
    
    if (!$enrollment) {
        return redirect()->back()->with('error', 'Matrícula não encontrada');
    }
    
    // Check if has fees or payments
    $studentFeeModel = new \App\Models\StudentFeeModel();
    $fees = $studentFeeModel->where('enrollment_id', $id)->countAllResults();
    
    if ($fees > 0) {
        return redirect()->back()
            ->with('error', 'Não é possível eliminar matrícula com taxas associadas');
    }
    
    $this->enrollmentModel->delete($id);
    
    return redirect()->to('/admin/students/enrollments')->with('success', 'Matrícula eliminada com sucesso');
}

/**
 * Pending enrollments list
 */
public function pending()
{
    $data['title'] = 'Matrículas Pendentes';
    
    $data['enrollments'] = $this->enrollmentModel
        ->select('
            tbl_enrollments.*,
            tbl_students.student_number,
            tbl_users.first_name,
            tbl_users.last_name,
            tbl_grade_levels.level_name,
            tbl_academic_years.year_name,
            tbl_courses.course_name,
            tbl_courses.course_code
        ')
        ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
        ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
        ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_enrollments.grade_level_id')
        ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_enrollments.academic_year_id')
        ->join('tbl_courses', 'tbl_courses.id = tbl_enrollments.course_id', 'left')
        ->where('tbl_enrollments.status', 'Pendente')
        ->orderBy('tbl_enrollments.created_at', 'DESC')
        ->findAll();
    
    return view('admin/students/enrollments/pending', $data);
}

/**
 * Approve pending enrollment
 */
public function approve($id)
{
    if (!$this->hasPermission('edit_enrollments')) {
        return redirect()->to('/admin/students/enrollments')->with('error', 'Não tem permissão para aprovar matrículas');
    }
    
    $enrollment = $this->enrollmentModel->find($id);
    
    if (!$enrollment) {
        return redirect()->back()->with('error', 'Matrícula não encontrada');
    }
    
   /*  if ($enrollment['status'] != 'Pendente') {
        return redirect()->back()->with('error', 'Esta matrícula não está pendente');
    } */
    
    // Verificar se tem turma atribuída
    if (!$enrollment->class_id) {
        return redirect()->back()->with('error', 'É necessário atribuir uma turma antes de aprovar a matrícula');
    }
    
    // Verificar vagas
    $availableSeats = $this->classModel->getAvailableSeats($enrollment->class_id);
    if ($availableSeats <= 0) {
        return redirect()->back()->with('error', 'Turma sem vagas disponíveis');
    }
    
    $this->enrollmentModel->update($id, ['status' => 'Ativo']);
    
    return redirect()->to('/admin/students/enrollments')
        ->with('success', 'Matrícula aprovada com sucesso');
}
}