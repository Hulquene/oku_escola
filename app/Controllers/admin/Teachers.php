<?php
// app/Controllers/admin/Teachers.php
namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\TeacherModel;
use App\Models\RoleModel;
use App\Models\ClassModel;

class Teachers extends BaseController
{
    protected $userModel;
    protected $teacherModel;
    protected $roleModel;
    protected $classModel;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->teacherModel = new TeacherModel();
        $this->roleModel = new RoleModel();
        $this->classModel = new ClassModel();
        
        helper(['form', 'url']);
    }
    
    /**
     * Lista de professores - View principal
     */
    public function index()
    {
        $data['title'] = 'Professores';
        
        // Parâmetros de filtro (para manter na URL)
        $data['search'] = $this->request->getGet('search');
        $data['selectedStatus'] = $this->request->getGet('status');
        $data['selectedDepartment'] = $this->request->getGet('department');
        
        return view('admin/teachers/index', $data);
    }
    
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
            $orderColumnIndex = 2; // padrão: nome
            $orderDir = 'asc';
            
            if (is_array($order) && isset($order[0])) {
                $orderColumnIndex = (int)($order[0]['column'] ?? 2);
                $orderDir = $order[0]['dir'] ?? 'asc';
            }
            
            // Filtros adicionais
            $status = $request->getPost('status');
            $department = $request->getPost('department');
            $searchInput = $request->getPost('searchInput');
            
            // Colunas para ordenação
            $columns = [
                0 => 'tbl_users.id',
                1 => 'tbl_users.id', // foto (não ordenável)
                2 => 'tbl_users.first_name',
                3 => 'tbl_users.phone',
                4 => 'total_classes',
                5 => 'total_workload',
                6 => 'tbl_users.last_login',
                7 => 'tbl_users.is_active',
            ];
            
            $orderColumn = $columns[$orderColumnIndex] ?? 'tbl_users.first_name';
            
            // Buscar role_id de professor
            $teacherRole = $this->roleModel->where('role_name', 'Professor')->first();
            $teacherRoleId = $teacherRole ? $teacherRole->id : 0;
            
            // Query principal
            $builder = $this->userModel
                ->select('
                    tbl_users.*,
                    tbl_teachers.id as teacher_id,
                    tbl_teachers.qualifications,
                    tbl_teachers.specialization,
                    tbl_teachers.bank_name,
                    tbl_teachers.bank_account,
                    (SELECT COUNT(*) FROM tbl_class_disciplines WHERE teacher_id = tbl_users.id AND is_active = 1) as total_disciplines,
                    (SELECT COUNT(DISTINCT class_id) FROM tbl_class_disciplines WHERE teacher_id = tbl_users.id AND is_active = 1) as total_classes,
                    (SELECT SUM(workload_hours) FROM tbl_class_disciplines WHERE teacher_id = tbl_users.id AND is_active = 1) as total_workload
                ')
                ->join('tbl_teachers', 'tbl_teachers.user_id = tbl_users.id', 'left')
                ->where('tbl_users.role_id', $teacherRoleId)
                ->where('tbl_users.user_type', 'teacher');
            
            // Aplicar filtros
            if (!empty($status)) {
                if ($status == 'active') {
                    $builder->where('tbl_users.is_active', 1);
                } elseif ($status == 'inactive') {
                    $builder->where('tbl_users.is_active', 0);
                }
            }
            
            if (!empty($department)) {
                $builder->where('tbl_teachers.specialization LIKE', "%{$department}%");
            }
            
            // Aplicar busca global
            if (!empty($searchValue)) {
                $builder->groupStart()
                    ->like('tbl_users.first_name', $searchValue)
                    ->orLike('tbl_users.last_name', $searchValue)
                    ->orLike('tbl_users.email', $searchValue)
                    ->orLike('tbl_users.phone', $searchValue)
                    ->orLike('CONCAT(tbl_users.first_name, " ", tbl_users.last_name)', $searchValue)
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
                // Iniciales para foto
                $row->initials = strtoupper(substr($row->first_name, 0, 1) . substr($row->last_name, 0, 1));
                
                // Formatar última acesso
                if ($row->last_login) {
                    $row->last_login_formatted = date('d/m/Y', strtotime($row->last_login));
                    $row->last_login_time = date('H:i', strtotime($row->last_login));
                } else {
                    $row->last_login_formatted = 'Nunca';
                    $row->last_login_time = '';
                }
                
                // Status com badge
                $row->status_badge = $row->is_active 
                    ? '<span class="badge bg-success">Ativo</span>' 
                    : '<span class="badge bg-danger">Inativo</span>';
                
                // Ações
                $row->actions = '
                    <div class="btn-group" role="group">
                        <a href="' . site_url('admin/teachers/view/' . ($row->teacher_id ?: $row->id)) . '" 
                           class="btn btn-sm btn-outline-success" 
                           title="Ver Detalhes">
                            <i class="fas fa-eye"></i>
                        </a>
                        <a href="' . site_url('admin/teachers/form-edit/' . ($row->teacher_id ?: $row->id)) . '" 
                           class="btn btn-sm btn-outline-info" 
                           title="Editar">
                            <i class="fas fa-edit"></i>
                        </a>
                        <a href="' . site_url('admin/teachers/assign-class/' . ($row->teacher_id ?: $row->id)) . '" 
                           class="btn btn-sm btn-outline-primary" 
                           title="Atribuir Turmas">
                            <i class="fas fa-tasks"></i>
                        </a>
                        <a href="' . site_url('admin/teachers/schedule/' . ($row->teacher_id ?: $row->id)) . '" 
                           class="btn btn-sm btn-outline-warning" 
                           title="Ver Horário">
                            <i class="fas fa-calendar-alt"></i>
                        </a>
                        ' . ($row->is_active ? 
                            '<button type="button" 
                                    class="btn btn-sm btn-outline-danger" 
                                    onclick="confirmDeactivate(' . ($row->teacher_id ?: $row->id) . ', \'' . $row->first_name . ' ' . $row->last_name . '\')"
                                    title="Desativar">
                                <i class="fas fa-user-slash"></i>
                            </button>' : 
                            '<a href="' . site_url('admin/teachers/activate/' . ($row->teacher_id ?: $row->id)) . '" 
                               class="btn btn-sm btn-outline-success" 
                               title="Reativar">
                                <i class="fas fa-user-check"></i>
                            </a>') . '
                    </div>
                ';
                
                // Foto
                if ($row->photo) {
                    $row->photo_html = '<img src="' . base_url('uploads/teachers/' . $row->photo) . '" 
                                         alt="Foto" 
                                         class="rounded-circle border"
                                         style="width: 45px; height: 45px; object-fit: cover;">';
                } else {
                    $row->photo_html = '<div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center text-white fw-bold"
                                          style="width: 45px; height: 45px; font-size: 1.2rem;">
                                          ' . $row->initials . '
                                        </div>';
                }
            }
            
            // Total de registros sem filtros
            $totalRecords = $this->userModel
                ->where('role_id', $teacherRoleId)
                ->where('user_type', 'teacher')
                ->countAllResults();
            
            return $this->response->setJSON([
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $recordsFiltered,
                'data' => $data
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Erro no DataTables de professores: ' . $e->getMessage());
            
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
        
        // Buscar role_id de professor
        $teacherRole = $this->roleModel->where('role_name', 'Professor')->first();
        $teacherRoleId = $teacherRole ? $teacherRole->id : 0;
        
        // Total de professores
        $totalTeachers = $this->userModel
            ->where('role_id', $teacherRoleId)
            ->where('user_type', 'teacher')
            ->countAllResults();
        
        // Professores ativos
        $activeTeachers = $this->userModel
            ->where('role_id', $teacherRoleId)
            ->where('user_type', 'teacher')
            ->where('is_active', 1)
            ->countAllResults();
        
        // Total de atribuições
        $db = db_connect();
        $totalAssignments = $db->table('tbl_class_disciplines')
            ->where('is_active', 1)
            ->where('teacher_id IS NOT NULL')
            ->countAllResults();
        
        // Carga horária total
        $totalWorkload = $db->table('tbl_class_disciplines')
            ->selectSum('workload_hours')
            ->where('is_active', 1)
            ->where('teacher_id IS NOT NULL')
            ->get()
            ->getRow()
            ->workload_hours ?? 0;
        
        return $this->response->setJSON([
            'totalTeachers' => $totalTeachers,
            'activeTeachers' => $activeTeachers,
            'inactiveTeachers' => $totalTeachers - $activeTeachers,
            'totalAssignments' => $totalAssignments,
            'totalWorkload' => $totalWorkload
        ]);
    }
    /**
     * Teacher form
     */
    public function form($id = null)
    {
        $data['title'] = $id ? 'Editar Professor' : 'Novo Professor';
        
        if ($id) {
            // Buscar dados completos do professor (user + teacher)
            $data['teacher'] = $this->teacherModel->getWithUser($id);
        } else {
            $data['teacher'] = null;
        }
        
        return view('admin/teachers/form', $data);
    }
    
/**
   /**
 * Save teacher - VERSÃO CORRIGIDA (separando teacher_id e user_id)
 */
public function save()
{
    $teacherId = $this->request->getPost('id'); // ID da tabela tbl_teachers
    $user_id = $this->request->getPost('user_id'); // ID da tabela tbl_users (para edição)
    
    // Preparar dados para tbl_users
    $userData = [
        'first_name' => $this->request->getPost('first_name'),
        'last_name' => $this->request->getPost('last_name'),
        'email' => $this->request->getPost('email'),
        'phone' => $this->request->getPost('phone'),
        'address' => $this->request->getPost('address'),
        'user_type' => 'teacher',
        'role_id' => 4,
        'is_active' => $this->request->getPost('is_active') ? 1 : 0
    ];
    
    $db = db_connect();
    $db->transStart();
    
    if ($teacherId) {
        // === MODO EDIÇÃO ===
        
        // Buscar o teacher para obter o user_id
        $teacher = $this->teacherModel->find($teacherId);
        if (!$teacher) {
            $db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Professor não encontrado');
        }
        
        $userId = $teacher->user_id; // ID real do usuário
        
        // IMPORTANTE: Incluir o ID nos dados para a validação
        $userData['id'] = $userId;
        
        // Se senha foi fornecida, atualizar
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $userData['password'] = password_hash($password, PASSWORD_DEFAULT);
        }
        
        // Atualizar usuário
        if (!$this->userModel->update($userId, $userData)) {
            $db->transRollback();
            return redirect()->back()->withInput()
                ->with('errors', $this->userModel->errors());
        }
        
        // Atualizar dados do professor
        $teacherData = [
            'birth_date' => $this->request->getPost('birth_date') ?: null,
            'gender' => $this->request->getPost('gender'),
            'nationality' => $this->request->getPost('nationality') ?: null,
            'identity_type' => $this->request->getPost('identity_type') ?: null,
            'identity_document' => $this->request->getPost('identity_document') ?: null, // ← AGORA É NULL SE VAZIO
            'nif' => $this->request->getPost('nif') ?: null, // ← AGORA É NULL SE VAZIO
            'province' => $this->request->getPost('province') ?: null,
            'municipality' => $this->request->getPost('municipality') ?: null,
            'city' => $this->request->getPost('city') ?: null,
            'address' => $this->request->getPost('address') ?: null,
            'emergency_contact' => $this->request->getPost('emergency_contact') ?: null,
            'emergency_contact_name' => $this->request->getPost('emergency_contact_name') ?: null,
            'qualifications' => $this->request->getPost('qualifications') ?: null,
            'specialization' => $this->request->getPost('specialization') ?: null,
            'admission_date' => $this->request->getPost('admission_date') ?: null,
            'bank_name' => $this->request->getPost('bank_name') ?: null,
            'bank_account' => $this->request->getPost('bank_account') ?: null
        ];
        
        $this->teacherModel->update($teacherId, $teacherData);
        
        $message = 'Professor atualizado com sucesso';
        
    } else {
        // === MODO CRIAÇÃO ===
        
        // Validar senha
        $password = $this->request->getPost('password');
        if (empty($password)) {
            $db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'A senha é obrigatória para novos professores');
        }
        
        // Preparar dados do usuário
        $userData['username'] = $this->request->getPost('email');
        $userData['password'] = password_hash($password, PASSWORD_DEFAULT);
        
        // Inserir usuário
        if (!$this->userModel->insert($userData)) {
            $db->transRollback();
            return redirect()->back()->withInput()
                ->with('errors', $this->userModel->errors());
        }
        
        $userId = $this->userModel->getInsertID();
        
        // Criar professor
         $teacherData = [
            'user_id' => $userId,
            'birth_date' => $this->request->getPost('birth_date') ?: null,
            'gender' => $this->request->getPost('gender'),
            'nationality' => $this->request->getPost('nationality') ?: null,
            'identity_type' => $this->request->getPost('identity_type') ?: null,
            'identity_document' => $this->request->getPost('identity_document') ?: null, // ← AGORA É NULL SE VAZIO
            'nif' => $this->request->getPost('nif') ?: null, // ← AGORA É NULL SE VAZIO
            'province' => $this->request->getPost('province') ?: null,
            'municipality' => $this->request->getPost('municipality') ?: null,
            'city' => $this->request->getPost('city') ?: null,
            'address' => $this->request->getPost('address') ?: null,
            'emergency_contact' => $this->request->getPost('emergency_contact') ?: null,
            'emergency_contact_name' => $this->request->getPost('emergency_contact_name') ?: null,
            'qualifications' => $this->request->getPost('qualifications') ?: null,
            'specialization' => $this->request->getPost('specialization') ?: null,
            'admission_date' => $this->request->getPost('admission_date') ?: null,
            'bank_name' => $this->request->getPost('bank_name') ?: null,
            'bank_account' => $this->request->getPost('bank_account') ?: null
        ];
        if (!$this->teacherModel->insert($teacherData)) {
            $db->transRollback();
            return redirect()->back()->withInput()
                ->with('errors', $this->teacherModel->errors());
        }
        
        $message = 'Professor criado com sucesso';
    }
    
    $db->transComplete();
    
    if ($db->transStatus()) {
        return redirect()->to('/admin/teachers')->with('success', $message);
    } else {
        return redirect()->back()->withInput()
            ->with('error', 'Erro ao salvar professor');
    }
}
   /**
 * View teacher
 */
public function view($id)
{
    $data['title'] = 'Detalhes do Professor';
    
    // Buscar dados completos do professor
    $data['teacher'] = $this->teacherModel->getWithUser($id);
    
    if (!$data['teacher']) {
        return redirect()->to('/admin/teachers')->with('error', 'Professor não encontrado');
    }
    
    // Buscar todas as atribuições com informações completas
    $allAssignments = $this->classDisciplineModel
        ->select('
            tbl_class_disciplines.*,
            tbl_classes.class_name,
            tbl_classes.class_code,
            tbl_classes.class_shift,
            tbl_classes.class_room,
            tbl_classes.academic_year_id,
            tbl_classes.course_id,
            tbl_disciplines.discipline_name,
            tbl_disciplines.discipline_code,
            tbl_semesters.semester_name,
            tbl_semesters.semester_type,
            tbl_academic_years.year_name,
            tbl_courses.course_name,
            tbl_courses.course_code
        ')
        ->join('tbl_classes', 'tbl_classes.id = tbl_class_disciplines.class_id')
        ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
        ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_classes.academic_year_id', 'left')
        ->join('tbl_courses', 'tbl_courses.id = tbl_classes.course_id', 'left')
        ->join('tbl_semesters', 'tbl_semesters.id = tbl_class_disciplines.semester_id', 'left')
        ->where('tbl_class_disciplines.teacher_id', $data['teacher']->user_id)
        ->where('tbl_class_disciplines.is_active', 1)
        ->orderBy('tbl_academic_years.start_date', 'DESC')
        ->orderBy('tbl_classes.class_name', 'ASC')
        ->orderBy('tbl_disciplines.discipline_name', 'ASC')
        ->findAll();
    
    // Organizar por ano letivo e curso
    $assignmentsByYear = [];
    $totalClasses = [];
    $totalWorkload = 0;
    $totalStudents = 0;
    
    foreach ($allAssignments as $assignment) {
        $yearId = $assignment->academic_year_id;
        $courseId = $assignment->course_id ?? 0;
        
        // Inicializar ano se não existir
        if (!isset($assignmentsByYear[$yearId])) {
            $assignmentsByYear[$yearId] = [
                'year_name' => $assignment->year_name ?? 'Ano Desconhecido',
                'total' => 0,
                'total_classes' => 0,
                'total_workload' => 0,
                'byCourse' => []
            ];
        }
        
        // Inicializar curso se não existir
        if (!isset($assignmentsByYear[$yearId]['byCourse'][$courseId])) {
            $assignmentsByYear[$yearId]['byCourse'][$courseId] = [
                'course_name' => $assignment->course_name ?? 'Ensino Geral',
                'course_code' => $assignment->course_code ?? '',
                'items' => []
            ];
        }
        
        // Adicionar item
        $assignmentsByYear[$yearId]['byCourse'][$courseId]['items'][] = $assignment;
        $assignmentsByYear[$yearId]['total']++;
        $assignmentsByYear[$yearId]['total_workload'] += $assignment->workload_hours ?? 0;
        
        // Contar turmas únicas
        $classKey = $assignment->class_id . '_' . $yearId;
        if (!in_array($classKey, $totalClasses)) {
            $totalClasses[] = $classKey;
            $assignmentsByYear[$yearId]['total_classes']++;
        }
        
        // Calcular total de alunos
        $enrollmentModel = new \App\Models\EnrollmentModel();
        $studentsCount = $enrollmentModel
            ->where('class_id', $assignment->class_id)
            ->where('status', 'Ativo')
            ->countAllResults();
        $totalStudents += $studentsCount;
        
        // Calcular carga horária total
        $totalWorkload += $assignment->workload_hours ?? 0;
    }
    
    $data['assignmentsByYear'] = $assignmentsByYear;
    $data['totalAssignments'] = count($allAssignments);
    $data['totalClasses'] = count($totalClasses);
    $data['totalWorkload'] = $totalWorkload;
    $data['totalStudents'] = $totalStudents;
    
    return view('admin/teachers/view', $data);
}
    
    /**
     * Assign class to teacher
     */
    /**
 * Assign class to teacher - VERSÃO MELHORADA
 */
/**
 * Assign class to teacher - CORRIGIDO COM ANO LETIVO
 */
public function assignClass($id)
{
    $data['title'] = 'Atribuir Turmas/Disciplinas';
    $data['teacher'] = $this->teacherModel->getWithUser($id);
    
    if (!$data['teacher']) {
        return redirect()->to('/admin/teachers')->with('error', 'Professor não encontrado');
    }
    
    $currentYear =  $this->academicYearModel ->getCurrent();
    
    // Enviar ano letivo atual para a view
    $data['currentYear'] = $currentYear;
    
    $data['classes'] = $this->classModel
        ->select('
            tbl_classes.*, 
            tbl_grade_levels.level_name,
            tbl_courses.course_name,
            tbl_courses.course_code,
            tbl_academic_years.year_name,
            tbl_academic_years.start_date,
            tbl_academic_years.end_date
        ')
        ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
        ->join('tbl_courses', 'tbl_courses.id = tbl_classes.course_id', 'left')
        ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_classes.academic_year_id')
        ->where('tbl_classes.academic_year_id', $currentYear->id)
        ->where('tbl_classes.is_active', 1)
        ->orderBy('tbl_classes.class_name', 'ASC')
        ->findAll();
    
    // Get current assignments (ativas e inativas)
    $data['assignments'] = $this->classDisciplineModel
        ->select('
            tbl_class_disciplines.*, 
            tbl_disciplines.discipline_name,
            tbl_disciplines.discipline_code,
            tbl_disciplines.workload_hours as default_workload
        ')
        ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
        ->where('teacher_id', $data['teacher']->user_id)
        ->findAll();
    
    // Organizar assignments por chave para fácil acesso
    $data['assignmentMap'] = [];
    foreach ($data['assignments'] as $assignment) {
        $key = $assignment->class_id . '_' . $assignment->discipline_id;
        $data['assignmentMap'][$key] = $assignment;
    }
    
    // Debug opcional - pode remover em produção
    log_message('debug', 'Current Year: ' . print_r($currentYear, true));
    log_message('debug', 'Classes count: ' . count($data['classes']));
    
    return view('admin/teachers/assign', $data);
}
    /**
     * Save assignment
     */
public function saveAssignment()
{
    $teacherId = $this->request->getPost('teacher_id');
    $submittedAssignments = $this->request->getPost('assignments') ?? [];
    
    if (!$teacherId) {
        return redirect()->back()->with('error', 'Professor não especificado');
    }
    
    // Buscar o user_id do professor
    $teacher = $this->teacherModel->getWithUser($teacherId);
    if (!$teacher) {
        return redirect()->back()->with('error', 'Professor não encontrado');
    }
    
    $userId = $teacher->user_id;
    
    $db = db_connect();
    $db->transStart();
    
    // Buscar atribuições atuais do professor
    $currentAssignments = $this->classDisciplineModel
        ->where('teacher_id', $userId)
        ->findAll();
    
    // Criar array com chaves no formato "classId_disciplineId" para fácil comparação
    $currentKeys = [];
    foreach ($currentAssignments as $assignment) {
        $key = $assignment->class_id . '_' . $assignment->discipline_id;
        $currentKeys[$key] = $assignment;
    }
    
    // Processar novas atribuições
    $submittedKeys = [];
    foreach ($submittedAssignments as $assignmentKey) {
        list($classId, $disciplineId) = explode('_', $assignmentKey);
        $key = $classId . '_' . $disciplineId;
        $submittedKeys[$key] = ['class_id' => $classId, 'discipline_id' => $disciplineId];
        
        // Se já existe, apenas garantir que está ativo
        if (isset($currentKeys[$key])) {
            // Já existe, verificar se precisa reativar
            if ($currentKeys[$key]->is_active == 0) {
                $this->classDisciplineModel->update($currentKeys[$key]->id, [
                  //  'is_active' => 1,
                    'teacher_id' => $userId
                ]);
            }
        } else {
            // Não existe, criar nova atribuição
            $this->classDisciplineModel->insert([
                'class_id' => $classId,
                'discipline_id' => $disciplineId,
                'teacher_id' => $userId,
               // 'workload_hours' => null, // Pode ser definido depois
               // 'semester_id' => null,     // Pode ser definido depois
              //  'is_active' => 1
            ]);
        }
    }
    
    // Desativar atribuições que não foram selecionadas
    foreach ($currentKeys as $key => $assignment) {
        if (!isset($submittedKeys[$key])) {
            // Desativar em vez de deletar (preserva histórico)
            $this->classDisciplineModel->update($assignment->id, [
                'teacher_id' => null
            ]);
        }
    }
    
    $db->transComplete();
    
    if ($db->transStatus()) {
        $count = count($submittedKeys);
        return redirect()->to('/admin/teachers/view/' . $teacherId)
            ->with('success', "{$count} disciplinas atribuídas ao professor com sucesso!");
    } else {
        return redirect()->back()->with('error', 'Erro ao atualizar atribuições');
    }
}
    /**
     * Delete teacher
     */
    public function delete($id)
    {
        $teacher = $this->teacherModel->getWithUser($id);
        
        if (!$teacher) {
            return redirect()->back()->with('error', 'Professor não encontrado');
        }
        
        // Check if has class assignments
        $assignments = $this->classDisciplineModel
            ->where('teacher_id', $teacher->user_id)
            ->countAllResults();
        
        if ($assignments > 0) {
            return redirect()->back()
                ->with('error', 'Não é possível eliminar professor com turmas atribuídas');
        }
        
        // Soft delete - just deactivate user
        $this->userModel->update($teacher->user_id, ['is_active' => 0]);
        
        return redirect()->to('/admin/teachers')->with('success', 'Professor desativado com sucesso');
    }
    
    /**
     * Get statistics via AJAX
     */
/*     public function getStats()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }
        
        $currentYear = $this->academicYearModel->getCurrent();
        $currentYearId = $currentYear ? $currentYear->id : null;
        
        $stats = [
            'totalTeachers' => $this->userModel->where('user_type', 'teacher')->countAllResults(),
            'activeTeachers' => $this->userModel->where('user_type', 'teacher')->where('is_active', 1)->countAllResults(),
            'totalAssignments' => 0,
            'totalWorkload' => 0
        ];
        
        if ($currentYearId) {
            $stats['totalAssignments'] = $this->classDisciplineModel
                ->join('tbl_classes', 'tbl_classes.id = tbl_class_disciplines.class_id')
                ->where('tbl_classes.academic_year_id', $currentYearId)
                ->where('tbl_class_disciplines.teacher_id IS NOT NULL')
                ->where('tbl_class_disciplines.is_active', 1)
                ->countAllResults();
            
            $workloadResult = $this->classDisciplineModel
                ->select('SUM(tbl_class_disciplines.workload_hours) as total_workload')
                ->join('tbl_classes', 'tbl_classes.id = tbl_class_disciplines.class_id')
                ->where('tbl_classes.academic_year_id', $currentYearId)
                ->where('tbl_class_disciplines.teacher_id IS NOT NULL')
                ->where('tbl_class_disciplines.is_active', 1)
                ->first();
            
            $stats['totalWorkload'] = $workloadResult ? (int)$workloadResult->total_workload : 0;
        }
        
        // Adicionar estatísticas de gênero ao AJAX
        $stats['maleTeachers'] = $this->teacherModel
            ->join('tbl_users', 'tbl_users.id = tbl_teachers.user_id')
            ->where('tbl_users.user_type', 'teacher')
            ->where('tbl_users.is_active', 1)
            ->where('tbl_teachers.gender', 'Masculino')
            ->countAllResults();
        
        $stats['femaleTeachers'] = $this->teacherModel
            ->join('tbl_users', 'tbl_users.id = tbl_teachers.user_id')
            ->where('tbl_users.user_type', 'teacher')
            ->where('tbl_users.is_active', 1)
            ->where('tbl_teachers.gender', 'Feminino')
            ->countAllResults();
        
        return $this->response->setJSON($stats);
    } */
    /**
 * View teacher schedule/timetable
 */
public function schedule($id)
{
    $data['title'] = 'Horário do Professor';
    
    // Buscar dados do professor
    $data['teacher'] = $this->teacherModel->getWithUser($id);
    
    if (!$data['teacher']) {
        return redirect()->to('/admin/teachers')->with('error', 'Professor não encontrado');
    }
    
    // Obter o ano letivo atual
    $currentYear = $this->academicYearModel->getCurrent();
    $currentYearId = $currentYear ? $currentYear->id : null;
    
    // Buscar todas as atribuições do professor
    $data['assignments'] = $this->classDisciplineModel
        ->select('
            tbl_class_disciplines.*,
            tbl_classes.class_name,
            tbl_classes.class_code,
            tbl_classes.class_shift,
            tbl_classes.class_room,
            tbl_disciplines.discipline_name,
            tbl_disciplines.discipline_code,
            tbl_semesters.semester_name,
            tbl_semesters.semester_type
        ')
        ->join('tbl_classes', 'tbl_classes.id = tbl_class_disciplines.class_id')
        ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
        ->join('tbl_semesters', 'tbl_semesters.id = tbl_class_disciplines.semester_id', 'left')
        ->where('tbl_class_disciplines.teacher_id', $data['teacher']->user_id)
        ->where('tbl_class_disciplines.is_active', 1);
    
    // Filtrar por ano letivo se disponível
    if ($currentYearId) {
        $data['assignments'] = $data['assignments']
            ->where('tbl_classes.academic_year_id', $currentYearId);
    }
    
    $data['assignments'] = $data['assignments']
        ->orderBy('tbl_classes.class_name', 'ASC')
        ->orderBy('tbl_disciplines.discipline_name', 'ASC')
        ->findAll();
    
    // Organizar por turno para melhor visualização
    $data['byShift'] = [
        'Manhã' => [],
        'Tarde' => [],
        'Noite' => [],
        'Integral' => []
    ];
    
    foreach ($data['assignments'] as $assignment) {
        $shift = $assignment->class_shift ?? 'Integral';
        $data['byShift'][$shift][] = $assignment;
    }
    
    return view('admin/teachers/schedule', $data);
}
}