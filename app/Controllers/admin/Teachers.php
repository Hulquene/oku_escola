<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\TeacherModel; // <-- ADICIONAR ESTE IMPORT
use App\Models\ClassModel;
use App\Models\ClassDisciplineModel;
use App\Models\AcademicYearModel;

class Teachers extends BaseController
{
    protected $userModel;
    protected $teacherModel; // <-- NOVO
    protected $classModel;
    protected $classDisciplineModel;
    protected $academicYearModel;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->teacherModel = new TeacherModel(); // <-- NOVO
        $this->classModel = new ClassModel();
        $this->classDisciplineModel = new ClassDisciplineModel();
        $this->academicYearModel = new AcademicYearModel();
    }
    
    /**
     * List teachers with statistics
     */
/**
 * List teachers with statistics
 */
public function index()
{
    $data['title'] = 'Professores';
    
    // Capturar filtros
    $search = $this->request->getGet('search');
    $status = $this->request->getGet('status');
    $department = $this->request->getGet('department');
    
    // Construir query base com dados do professor e totais
    $builder = $this->teacherModel
        ->select('
            tbl_teachers.id as teacher_id,
            tbl_teachers.*,
            tbl_users.username,
            tbl_users.email,
            tbl_users.first_name,
            tbl_users.last_name,
            tbl_users.phone,
            tbl_users.photo,
            tbl_users.is_active,
            tbl_users.last_login,
            (SELECT COUNT(*) FROM tbl_class_disciplines 
             WHERE teacher_id = tbl_teachers.user_id 
             AND is_active = 1) as total_classes,
            (SELECT SUM(workload_hours) FROM tbl_class_disciplines 
             WHERE teacher_id = tbl_teachers.user_id 
             AND is_active = 1) as total_workload
        ')
        ->join('tbl_users', 'tbl_users.id = tbl_teachers.user_id')
        ->where('tbl_users.user_type', 'teacher');
    
    // Aplicar filtros
    if ($search) {
        $builder->groupStart()
            ->like('tbl_users.first_name', $search)
            ->orLike('tbl_users.last_name', $search)
            ->orLike('tbl_users.email', $search)
            ->orLike('tbl_users.phone', $search)
            ->groupEnd();
    }
    
    if ($status == 'active') {
        $builder->where('tbl_users.is_active', 1);
    } elseif ($status == 'inactive') {
        $builder->where('tbl_users.is_active', 0);
    }
    
    // Obter professores paginados
    $data['teachers'] = $builder
        ->orderBy('tbl_users.first_name', 'ASC')
        ->paginate(10);
    
    $data['pager'] = $this->teacherModel->pager;
    
    // --- ESTATÍSTICAS PARA OS CARDS ---
    
    // Total de professores (todos)
    $data['totalTeachers'] = $this->teacherModel
        ->join('tbl_users', 'tbl_users.id = tbl_teachers.user_id')
        ->where('tbl_users.user_type', 'teacher')
        ->countAllResults();
    
    // Total de professores ativos
    $data['activeTeachers'] = $this->teacherModel
        ->join('tbl_users', 'tbl_users.id = tbl_teachers.user_id')
        ->where('tbl_users.user_type', 'teacher')
        ->where('tbl_users.is_active', 1)
        ->countAllResults();
    
    // Ano letivo atual para estatísticas
    $currentYear = $this->academicYearModel->getCurrent();
    $currentYearId = $currentYear ? $currentYear->id : null;
    
    // Total de atribuições (turmas + disciplinas) no ano atual
    if ($currentYearId) {
        $data['totalAssignments'] = $this->classDisciplineModel
            ->select('COUNT(*) as total')
            ->join('tbl_classes', 'tbl_classes.id = tbl_class_disciplines.class_id')
            ->where('tbl_classes.academic_year_id', $currentYearId)
            ->where('tbl_class_disciplines.teacher_id IS NOT NULL')
            ->where('tbl_class_disciplines.is_active', 1)
            ->countAllResults();
        
        // Carga horária total no ano atual
        $workloadResult = $this->classDisciplineModel
            ->select('SUM(tbl_class_disciplines.workload_hours) as total_workload')
            ->join('tbl_classes', 'tbl_classes.id = tbl_class_disciplines.class_id')
            ->where('tbl_classes.academic_year_id', $currentYearId)
            ->where('tbl_class_disciplines.teacher_id IS NOT NULL')
            ->where('tbl_class_disciplines.is_active', 1)
            ->first();
        
        $data['totalWorkload'] = $workloadResult ? (int)$workloadResult->total_workload : 0;
    } else {
        $data['totalAssignments'] = 0;
        $data['totalWorkload'] = 0;
    }
    
    // Estatísticas adicionais para o filtro
    $data['totalFiltered'] = $builder->countAllResults(false);
    
    // Manter valores dos filtros na view
    $data['search'] = $search;
    $data['selectedStatus'] = $status;
    $data['selectedDepartment'] = $department;
    
    // Distribuição por gênero
    $data['maleTeachers'] = $this->teacherModel
        ->join('tbl_users', 'tbl_users.id = tbl_teachers.user_id')
        ->where('tbl_users.user_type', 'teacher')
        ->where('tbl_users.is_active', 1)
        ->where('tbl_teachers.gender', 'Masculino')
        ->countAllResults();
    
    $data['femaleTeachers'] = $this->teacherModel
        ->join('tbl_users', 'tbl_users.id = tbl_teachers.user_id')
        ->where('tbl_users.user_type', 'teacher')
        ->where('tbl_users.is_active', 1)
        ->where('tbl_teachers.gender', 'Feminino')
        ->countAllResults();
    
    return view('admin/teachers/index', $data);
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
    public function getStats()
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
    }
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