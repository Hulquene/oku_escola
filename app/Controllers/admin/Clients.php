<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use App\Models\UserModel;
use App\Models\EnrollmentModel;
use App\Models\ClassModel;
use App\Models\AcademicYearModel;
use App\Models\GuardianModel;
use App\Models\StudentGuardianModel;
use App\Models\CourseModel; 

//Students Controller (App\Controllers\Admin\Clients.php - Adaptado para Students)
class Clients extends BaseController
{
    protected $studentModel;
    protected $userModel;
    protected $enrollmentModel;
    protected $classModel;
    protected $academicYearModel;
    protected $guardianModel;
    protected $studentGuardianModel;
      protected $courseModel;
    
    public function __construct()
    {
        $this->studentModel = new StudentModel();
        $this->userModel = new UserModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->classModel = new ClassModel();
        $this->academicYearModel = new AcademicYearModel();
        $this->guardianModel = new GuardianModel();
        $this->studentGuardianModel = new StudentGuardianModel();
        $this->courseModel = new CourseModel();
    }
    
 
    /**
     * Students list (alias for index)
     */
    public function students()
    {
        return $this->index();
    }
    
    /**
     * Students list - Versão simplificada (apenas carrega a view)
     */
    public function index()
    {
        $data['title'] = 'Alunos';
        
        // Buscar dados para filtros
        $data['courses'] = $this->courseModel->getActive();
        $data['academicYears'] = $this->academicYearModel
            ->where('is_active', 1)
            ->orderBy('start_date', 'DESC')
            ->findAll();
        
        // Buscar ano letivo atual para o filtro padrão
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
        $data['selectedCourse'] = $this->request->getGet('course_id');
        $data['selectedEnrollmentStatus'] = $this->request->getGet('enrollment_status');
        $data['selectedGender'] = $this->request->getGet('gender');
        $data['selectedStatus'] = $this->request->getGet('status');
        $data['selectedCreatedDate'] = $this->request->getGet('created_date');
        $data['selectedCreatedMonth'] = $this->request->getGet('created_month');
        $data['search'] = $this->request->getGet('search');
        
        return view('admin/students/index', $data);
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
            $orderColumnIndex = 2; // padrão: nome
            $orderDir = 'asc';
            
            if (is_array($order) && isset($order[0])) {
                $orderColumnIndex = (int)($order[0]['column'] ?? 2);
                $orderDir = $order[0]['dir'] ?? 'asc';
            }
            
            // Filtros adicionais
            $academicYearId = $request->getPost('academic_year');
            $courseId = $request->getPost('course_id');
            $enrollmentStatus = $request->getPost('enrollment_status');
            $gender = $request->getPost('gender');
            $status = $request->getPost('status');
            $createdDate = $request->getPost('created_date');
            $createdMonth = $request->getPost('created_month');
            
            // Colunas para ordenação
            $columns = [
                0 => 'tbl_students.student_number',
                1 => 'tbl_students.photo',
                2 => 'tbl_users.first_name',
                3 => 'current_course_name',
                4 => 'current_level_name',
                5 => 'tbl_students.gender',
                6 => 'tbl_students.is_active',
                7 => 'tbl_students.created_at',
            ];
            
            $orderColumn = $columns[$orderColumnIndex] ?? 'tbl_users.first_name';
            
            // Query principal
            $builder = $this->studentModel
                ->select('
                    tbl_students.*,
                    tbl_users.first_name,
                    tbl_users.last_name,
                    tbl_users.email,
                    tbl_users.photo,
                    tbl_users.phone,
                    tbl_users.is_active as user_active,
                    active_enroll.class_name as current_class,
                    active_enroll.class_shift as current_shift,
                    active_enroll.year_name as current_year,
                    active_enroll.level_name as current_level_name,
                    active_enroll.status as enrollment_status,
                    active_enroll.id as enrollment_id,
                    active_enroll.course_id as current_course_id,
                    active_enroll.course_name as current_course_name,
                    active_enroll.course_code as current_course_code,
                    pending_enroll.academic_year_id as pending_year_id,
                    pending_enroll.grade_level_id as pending_level_id,
                    pending_enroll.course_id as pending_course_id,
                    pending_year.year_name as pending_year_name,
                    pending_level.level_name as pending_level_name,
                    pending_course.course_name as pending_course_name,
                    pending_course.course_code as pending_course_code,
                    COALESCE(active_enroll.course_name, pending_course.course_name, "Ensino Geral") as display_course
                ')
                ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
                
                // LEFT JOIN para matrícula ativa
                ->join('(
                    SELECT e.*,
                           c.class_name,
                           c.class_shift,
                           ay.year_name,
                           gl.level_name,
                           co.course_name,
                           co.course_code
                    FROM tbl_enrollments e
                    LEFT JOIN tbl_classes c ON c.id = e.class_id
                    LEFT JOIN tbl_academic_years ay ON ay.id = e.academic_year_id
                    LEFT JOIN tbl_grade_levels gl ON gl.id = e.grade_level_id
                    LEFT JOIN tbl_courses co ON co.id = e.course_id
                    WHERE e.status = "Ativo"
                ) as active_enroll', 'active_enroll.student_id = tbl_students.id', 'left')
                
                // LEFT JOIN para matrícula pendente (mais recente)
                ->join('(
                    SELECT e.*,
                           ay.year_name,
                           gl.level_name
                    FROM tbl_enrollments e
                    LEFT JOIN tbl_academic_years ay ON ay.id = e.academic_year_id
                    LEFT JOIN tbl_grade_levels gl ON gl.id = e.grade_level_id
                    WHERE e.status = "Pendente"
                    AND e.id IN (
                        SELECT MAX(id)
                        FROM tbl_enrollments
                        WHERE status = "Pendente"
                        GROUP BY student_id
                    )
                ) as pending_enroll', 'pending_enroll.student_id = tbl_students.id', 'left')
                
                ->join('tbl_academic_years as pending_year', 'pending_year.id = pending_enroll.academic_year_id', 'left')
                ->join('tbl_grade_levels as pending_level', 'pending_level.id = pending_enroll.grade_level_id', 'left')
                ->join('tbl_courses as pending_course', 'pending_course.id = pending_enroll.course_id', 'left')
                ->where('tbl_students.is_active', 1);
            
            // Aplicar filtros
            if (!empty($searchValue)) {
                $builder->groupStart()
                    ->like('tbl_users.first_name', $searchValue)
                    ->orLike('tbl_users.last_name', $searchValue)
                    ->orLike('tbl_students.student_number', $searchValue)
                    ->orLike('tbl_students.identity_document', $searchValue)
                    ->orLike('tbl_students.nif', $searchValue)
                    ->orLike('tbl_users.email', $searchValue)
                    ->groupEnd();
            }
            
            // FILTRO DE CURSO (incluindo Ensino Geral)
            if ($courseId !== null && $courseId !== '') {
                if ($courseId == '0') {
                    $builder->where('active_enroll.course_id IS NULL AND pending_enroll.course_id IS NULL');
                } else {
                    $builder->where('(active_enroll.course_id = ' . $courseId . ' OR pending_enroll.course_id = ' . $courseId . ')');
                }
            }
            
            // FILTRO DE STATUS DA MATRÍCULA
            if ($enrollmentStatus) {
                if ($enrollmentStatus == 'nao_matriculado') {
                    $builder->where('active_enroll.id IS NULL AND pending_enroll.id IS NULL');
                } elseif ($enrollmentStatus == 'Pendente') {
                    $builder->where('active_enroll.id IS NULL AND pending_enroll.id IS NOT NULL');
                } elseif ($enrollmentStatus == 'Ativo') {
                    $builder->where('active_enroll.id IS NOT NULL');
                } elseif ($enrollmentStatus == 'Concluído' || $enrollmentStatus == 'Transferido') {
                    // Para status concluído e transferido, buscar via histórico
                    $builder->join('(
                        SELECT student_id, status
                        FROM tbl_enrollments e2
                        WHERE status = "' . $enrollmentStatus . '"
                        GROUP BY student_id
                    ) as history_enroll', 'history_enroll.student_id = tbl_students.id', 'inner');
                }
            }
            
            // FILTRO DE GÊNERO
            if ($gender) {
                $builder->where('tbl_students.gender', $gender);
            }
            
            // FILTRO DE STATUS DO ESTUDANTE
            if ($status == 'active') {
                $builder->where('tbl_students.is_active', 1);
            } elseif ($status == 'inactive') {
                $builder->where('tbl_students.is_active', 0);
            }
            
            // FILTRO: Data específica de cadastro
            if ($createdDate) {
                $builder->where('DATE(tbl_students.created_at)', $createdDate);
            }
            
            // FILTRO: Mês/Ano de cadastro
            if ($createdMonth) {
                $builder->where('DATE_FORMAT(tbl_students.created_at, "%Y-%m")', $createdMonth);
            }
            
            // FILTRO DE ANO LETIVO (afeta matrículas exibidas)
            if (!empty($academicYearId)) {
                $builder->where('(active_enroll.academic_year_id = ' . $academicYearId . ' OR pending_enroll.academic_year_id = ' . $academicYearId . ')');
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
                // Iniciais para foto
                $row->initials = strtoupper(substr($row->first_name, 0, 1) . substr($row->last_name, 0, 1));
                
                // Foto HTML
                if ($row->photo) {
                    $row->photo_html = '<img src="' . base_url('uploads/students/' . $row->photo) . '" ' .
                                       'alt="Foto" class="rounded-circle border" ' .
                                       'style="width: 45px; height: 45px; object-fit: cover;">';
                } else {
                    $row->photo_html = '<div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center text-white fw-bold" ' .
                                      'style="width: 45px; height: 45px; font-size: 1.2rem;">' .
                                      $row->initials . '</div>';
                }
                
                // Nome completo com email/telefone
                $row->name_html = '<strong>' . $row->first_name . ' ' . $row->last_name . '</strong><br>' .
                                  '<small class="text-muted">' .
                                  '<i class="fas fa-envelope me-1"></i>' . ($row->email ?? '') . '<br>' .
                                  '<i class="fas fa-phone me-1"></i>' . ($row->phone ?? '-') .
                                  '</small>';
                
                // Curso HTML
                if (!empty($row->current_course_name)) {
                    $row->course_html = '<span class="badge bg-primary p-2">' .
                                        '<i class="fas fa-graduation-cap me-1"></i>' . esc($row->current_course_name) .
                                        '</span>';
                } elseif (!empty($row->pending_course_name)) {
                    $row->course_html = '<span class="badge bg-warning p-2">' .
                                        '<i class="fas fa-clock me-1"></i>' . esc($row->pending_course_name) . ' (Pendente)' .
                                        '</span>';
                } else {
                    $row->course_html = '<span class="badge bg-secondary p-2">' .
                                        '<i class="fas fa-school me-1"></i>Ensino Geral' .
                                        '</span>';
                }
                
                // Nível HTML
                $niveis = [];
                if (!empty($row->current_level_name) && !empty($row->current_year)) {
                    $niveis[] = '<span class="badge bg-success me-1 mb-1">' . 
                                 $row->current_level_name . ' (' . $row->current_year . ')' . 
                                 '</span>';
                }
                if (!empty($row->pending_level_name) && !empty($row->pending_year_name)) {
                    $niveis[] = '<span class="badge bg-warning me-1 mb-1">' . 
                                 $row->pending_level_name . ' (' . $row->pending_year_name . ') [Pendente]' . 
                                 '</span>';
                }
                $row->level_html = empty($niveis) ? '<span class="text-muted">-</span>' : implode('<br>', $niveis);
                
                // Status badge
                $row->status_badge = $row->is_active ? 
                    '<span class="badge bg-success">Ativo</span>' : 
                    '<span class="badge bg-danger">Inativo</span>';
                
                // Data de cadastro
                $row->created_at_html = date('d/m/Y', strtotime($row->created_at)) . 
                                        '<br><small class="text-muted">' . 
                                        date('H:i', strtotime($row->created_at)) . 
                                        '</small>';
                
                // Ações
                $actions = '<div class="btn-group" role="group">';
                $actions .= '<a href="' . site_url('admin/students/view/' . $row->id) . '" ' .
                           'class="btn btn-sm btn-outline-success" title="Ver Detalhes">' .
                           '<i class="fas fa-eye"></i></a>';
                $actions .= '<a href="' . site_url('admin/students/form-edit/' . $row->id) . '" ' .
                           'class="btn btn-sm btn-outline-info" title="Editar">' .
                           '<i class="fas fa-edit"></i></a>';
                $actions .= '<a href="' . site_url('admin/students/enrollments/history/' . $row->id) . '" ' .
                           'class="btn btn-sm btn-outline-primary" title="Histórico">' .
                           '<i class="fas fa-history"></i></a>';
                
                if (!empty($row->current_class) || $row->enrollment_status == 'Ativo') {
                    $actions .= '<a href="' . site_url('admin/students/enrollments/view/' . $row->enrollment_id) . '" ' .
                               'class="btn btn-sm btn-outline-warning" title="Ver Matrícula Atual">' .
                               '<i class="fas fa-file-signature"></i></a>';
                }
                
                $actions .= '<button type="button" class="btn btn-sm btn-outline-danger" ' .
                           'onclick="confirmDelete(' . $row->id . ')" title="Eliminar">' .
                           '<i class="fas fa-trash"></i></button>';
                $actions .= '</div>';
                $row->actions = $actions;
            }
            
            // Total de registros sem filtros
            $totalRecords = $this->studentModel
                ->where('is_active', 1)
                ->countAllResults();
            
            return $this->response->setJSON([
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $recordsFiltered,
                'data' => $data
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Erro no DataTables de alunos: ' . $e->getMessage());
            log_message('error', 'Linha: ' . $e->getLine());
            log_message('error', 'Arquivo: ' . $e->getFile());
            
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
        
        $totalStudents = $this->studentModel->countAll();
        $enrolledStudents = $this->enrollmentModel->where('status', 'Ativo')->countAllResults();
        $pendingEnrollments = $this->enrollmentModel->where('status', 'Pendente')->countAllResults();
        
        // CORREÇÃO: Verificar se há IDs antes de usar whereNotIn
        $enrolledStudentsList = $this->enrollmentModel
            ->select('student_id')
            ->whereIn('status', ['Ativo', 'Pendente'])
            ->findAll();
        
        $enrolledIds = array_column($enrolledStudentsList, 'student_id');
        
        if (!empty($enrolledIds)) {
            $nonEnrolledStudents = $this->studentModel
                ->whereNotIn('id', $enrolledIds)
                ->countAllResults();
        } else {
            // Se não há alunos matriculados, todos os alunos são não matriculados
            $nonEnrolledStudents = $this->studentModel->countAllResults();
        }
        
        $maleCount = $this->studentModel->where('gender', 'Masculino')->countAllResults();
        $femaleCount = $this->studentModel->where('gender', 'Feminino')->countAllResults();
        
        return $this->response->setJSON([
            'totalStudents' => $totalStudents,
            'enrolledStudents' => $enrolledStudents,
            'pendingEnrollments' => $pendingEnrollments,
            'nonEnrolledStudents' => $nonEnrolledStudents,
            'maleCount' => $maleCount,
            'femaleCount' => $femaleCount
        ]);
    }
/**
 * Student specific form
 */
public function studentForm($id = null)
{
    $data['title'] = $id ? 'Editar Aluno' : 'Novo Aluno';
    $data['student'] = $id ? $this->studentModel->getWithUser($id) : null;
    
    // Obter ano letivo atual
    $currentYear = $this->academicYearModel->getCurrent();
    $data['currentYear'] = $currentYear;
    
    // Anos letivos ativos
    $data['academicYears'] = $this->academicYearModel
        ->where('is_active', 1)
        ->orderBy('start_date', 'DESC')
        ->findAll();
    
    // Níveis de ensino
    $gradeLevelModel = new \App\Models\GradeLevelModel();
    $data['gradeLevels'] = $gradeLevelModel
        ->where('is_active', 1)
        ->orderBy('sort_order', 'ASC')
        ->findAll();
    
    // ADICIONAR CURSOS
    $data['courses'] = $this->courseModel->getActive();
    
    // Inicializar variáveis
    $data['selectedYear'] = $currentYear->id ?? null;
    $data['selectedLevel'] = null;
    $data['selectedCourse'] = null; // NOVO
    $data['selectedEnrollmentType'] = 'Nova';
    $data['selectedPreviousGrade'] = null;
    
    if ($id && $data['student']) {
        // Buscar matrícula pendente primeiro
        $enrollment = $this->enrollmentModel
            ->where('student_id', $id)
            ->where('academic_year_id', $currentYear->id ?? 0)
            ->where('status', 'Pendente')
            ->orderBy('created_at', 'DESC')
            ->first();
        
        if ($enrollment) {
            // Se tem pendente, usar dados da pendente
            $data['selectedYear'] = $enrollment->academic_year_id;
            $data['selectedLevel'] = $enrollment->grade_level_id;
            $data['selectedCourse'] = $enrollment->course_id; // NOVO
            $data['selectedEnrollmentType'] = $enrollment->enrollment_type;
            $data['selectedPreviousGrade'] = $enrollment->previous_grade_id;
        } else {
            // Se não tem pendente, buscar matrícula ativa
            $activeEnrollment = $this->enrollmentModel
                ->where('student_id', $id)
                ->where('status', 'Ativo')
                ->orderBy('id', 'DESC')
                ->first();
            
            if ($activeEnrollment) {
                $data['selectedYear'] = $activeEnrollment->academic_year_id;
                $data['selectedLevel'] = $activeEnrollment->grade_level_id;
                $data['selectedCourse'] = $activeEnrollment->course_id; // NOVO
                $data['selectedEnrollmentType'] = $activeEnrollment->enrollment_type;
                $data['selectedPreviousGrade'] = $activeEnrollment->previous_grade_id;
            }
        }
    }
    
    $data['guardians'] = $id ? $this->guardianModel->getByStudent($id) : [];
    
    return view('admin/students/form', $data);
}
/**
 * Save student
 */
public function saveStudent()
{
    log_message('info', '=== INÍCIO saveStudent ===');
    log_message('info', 'POST dados: ' . json_encode($_POST));
    
    $db = db_connect();
    $db->transStart();
    
    $userId = $this->request->getPost('user_id');
    $studentId = $this->request->getPost('id');
    $email = $this->request->getPost('email');
    
    //var_dump($userId,$studentId);die;
    // Verificar idade mínima
    $birthDate = $this->request->getPost('birth_date');
    $age = date_diff(date_create($birthDate), date_create('today'))->y;
    if ($age < 5) {
        $db->transRollback();
        return redirect()->back()->withInput()
            ->with('error', 'O aluno deve ter pelo menos 5 anos de idade');
    }
    
    // VALIDAÇÃO MANUAL DE EMAIL ÚNICO (para garantir)
    if ($email) {
        $existingUser = $this->userModel
            ->where('email', $email)
            ->where('id !=', $userId) // Ignorar o próprio usuário se for edição
            ->first();

        if ($existingUser) {
            $db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Este email já está em uso por outro usuário teste.');
        }
    }
    
    // Preparar dados do usuário
    $userData = [
        'first_name' => $this->request->getPost('first_name'),
        'last_name' => $this->request->getPost('last_name'),
        'email' => $email,
        'phone' => $this->request->getPost('phone'),
        'address' => $this->request->getPost('address'),
        'user_type' => 'student',
        'role_id' => 5,
        'is_active' => $this->request->getPost('is_active') ? 1 : 0
    ];
    
    if ($userId) {
        $existingUser = $this->userModel->find($userId);
        if (!$existingUser) {
            $db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Usuário associado não encontrado');
        }
        
        if (!$this->userModel->update($userId, $userData)) {
            $db->transRollback();
            $errors = $this->userModel->errors();
            log_message('error', 'Erro ao atualizar usuário: ' . json_encode($errors));
            return redirect()->back()->withInput()
                ->with('errors', $errors);
        }
    } else {
        $userData['username'] = $email;
        $userData['password'] = password_hash('123456', PASSWORD_DEFAULT);
        $userId = $this->userModel->insert($userData);
        
        if (!$userId) {
            $db->transRollback();
            $errors = $this->userModel->errors();
            log_message('error', 'Erro ao inserir usuário: ' . json_encode($errors));
            return redirect()->back()->withInput()
                ->with('errors', $errors);
        }
    }
    
    // Preparar dados do aluno
    $studentData = [
        'user_id' => $userId,
        'birth_date' => $birthDate,
        'birth_place' => $this->request->getPost('birth_place'),
        'gender' => $this->request->getPost('gender'),
        'nationality' => $this->request->getPost('nationality') ?: 'Angolana',
        'identity_document' => $this->request->getPost('identity_document'),
        'identity_type' => $this->request->getPost('identity_type') ?: 'BI',
        'nif' => $this->request->getPost('nif'),
        'city' => $this->request->getPost('city'),
        'municipality' => $this->request->getPost('municipality'),
        'province' => $this->request->getPost('province'),
        'emergency_contact' => $this->request->getPost('emergency_contact'),
        'emergency_contact_name' => $this->request->getPost('emergency_contact_name'),
        'previous_school' => $this->request->getPost('previous_school'),
        'special_needs' => $this->request->getPost('special_needs'),
        'health_conditions' => $this->request->getPost('health_conditions'),
        'blood_type' => $this->request->getPost('blood_type')
    ];
    
    if ($studentId) {
        $studentData['id'] = $studentId;
        
        if (!$this->studentModel->update($studentId, $studentData)) {
            $db->transRollback();
            $errors = $this->studentModel->errors();
            log_message('error', 'Erro ao atualizar aluno: ' . json_encode($errors));
            return redirect()->back()->withInput()
                ->with('errors', $errors);
        }
    } else {
        $studentData['student_number'] = $this->studentModel->generateStudentNumber();
        $studentId = $this->studentModel->insert($studentData);
        
        if (!$studentId) {
            $db->transRollback();
            $errors = $this->studentModel->errors();
            log_message('error', 'Erro ao inserir aluno: ' . json_encode($errors));
            return redirect()->back()->withInput()
                ->with('errors', $errors);
        }
    }
    
    // ===== CRIAÇÃO DA MATRÍCULA PENDENTE =====
    if ($studentId) {
        $academicYearId = $this->request->getPost('academic_year_id');
        $gradeLevelId = $this->request->getPost('grade_level_id');
        $courseId = $this->request->getPost('course_id');
        
        // Verificar se já existe matrícula
        $existingEnrollment = $this->enrollmentModel
            ->where('student_id', $studentId)
            ->where('academic_year_id', $academicYearId)
            ->whereIn('status', ['Pendente', 'Ativo'])
            ->first();
        
        if (!$existingEnrollment) {
            
            // Tratar course_id adequadamente
            if ($courseId === '' || $courseId === null || $courseId === 'null') {
                $courseId = null;
            }
            
            // Buscar uma turma automaticamente (opcional - pode ser null)
            $classId = null;
            
            $enrollmentData = [
                'student_id' => $studentId,
                'class_id' => $classId,
                'academic_year_id' => $academicYearId,
                'grade_level_id' => $gradeLevelId,
                'course_id' => $courseId,
                'enrollment_date' => date('Y-m-d'),
                'enrollment_number' => $this->enrollmentModel->generateEnrollmentNumber(),
                'enrollment_type' => $this->request->getPost('enrollment_type') ?: 'Nova',
                'status' => 'Pendente',
                'created_by' => $this->session->get('user_id')
            ];
            
            log_message('info', 'Tentando inserir matrícula: ' . json_encode($enrollmentData));
            
            if (!$this->enrollmentModel->insert($enrollmentData)) {
                $db->transRollback();
                $errors = $this->enrollmentModel->errors();
                log_message('error', 'Erro ao inserir matrícula: ' . json_encode($errors));
                return redirect()->back()->withInput()
                    ->with('errors', $errors);
            }
            
            log_message('info', 'Matrícula criada com sucesso para o aluno ID: ' . $studentId);
        }
    }
    
    // Upload da foto
    $photo = $this->request->getFile('photo');
    if ($photo && $photo->isValid() && !$photo->hasMoved()) {
        $newName = $photo->getRandomName();
        $photo->move('uploads/students', $newName);
        $this->studentModel->update($studentId, ['photo' => $newName]);
    }
    
    $db->transComplete();
    
    if ($db->transStatus()) {
        session()->setFlashdata('success', 'Aluno registrado com sucesso!');
        session()->setFlashdata('info', 'Uma matrícula pendente foi criada. <a href="' . site_url('admin/students/enrollments') . '" class="alert-link">Clique aqui</a> para concluir a matrícula.');
        
        return redirect()->to('/admin/students');
    } else {
        return redirect()->back()->withInput()
            ->with('error', 'Erro ao salvar aluno');
    }
}
    /**
     * Save (alias for saveStudent)
     */
    public function save()
    {
        return $this->saveStudent();
    }
    
  /**
 * View student
 */
public function viewStudent($id)
{
    $data['title'] = 'Detalhes do Aluno';
    $data['student'] = $this->studentModel->getWithUser($id);
    
    if (!$data['student']) {
        return redirect()->to('/admin/students')->with('error', 'Aluno não encontrado');
    }
    
    // Current enrollment - JÁ INCLUI CURSO PELO EnrollmentModel::getCurrentForStudent
    $data['currentEnrollment'] = $this->studentModel->getCurrentEnrollment($id);
    
    // Se quiser garantir que o curso venha, pode buscar diretamente
    if ($data['currentEnrollment']) {
        $enrollmentDetails = $this->enrollmentModel->getWithDetails($data['currentEnrollment']->id);
        $data['currentEnrollment']->course_name = $enrollmentDetails->course_name ?? null;
        $data['currentEnrollment']->course_code = $enrollmentDetails->course_code ?? null;
    }
    
    // Academic history
    $data['history'] = $this->studentModel->getAcademicHistory($id);
    
    // Guardians
    $data['guardians'] = $this->guardianModel->getByStudent($id);
    
    // Fees summary
    $data['feesSummary'] = $this->studentModel->getFeesSummary($id);
    
    // Recent payments
    $feePaymentModel = new \App\Models\FeePaymentModel();
    $data['recentPayments'] = $feePaymentModel->getByStudent($id);
    
    return view('admin/students/view', $data);
}
    
    /**
     * View (alias for viewStudent)
     */
    public function view($id)
    {
        return $this->viewStudent($id);
    }
    
    /**
     * Delete student
     */
    public function delete($id)
    {
        $student = $this->studentModel->find($id);
        
        if (!$student) {
            return redirect()->back()->with('error', 'Aluno não encontrado');
        }
        
        // Check for active enrollments
        $activeEnrollments = $this->enrollmentModel
            ->where('student_id', $id)
            ->where('status', 'Ativo')
            ->countAllResults();
        
        if ($activeEnrollments > 0) {
            return redirect()->back()
                ->with('error', 'Não é possível eliminar aluno com matrículas ativas');
        }
        
        $db = db_connect();
        $db->transStart();
        
        // Delete student
        $this->studentModel->delete($id);
        
        // Deactivate user (don't delete completely)
        $this->userModel->update($student->user_id, ['is_active' => 0]);
        
        $db->transComplete();
        
        if ($db->transStatus()) {
            return redirect()->to('/admin/students')
                ->with('success', 'Aluno eliminado com sucesso');
        } else {
            return redirect()->back()->with('error', 'Erro ao eliminar aluno');
        }
    }
    
    /**
     * Change student status
     */
    public function chace_satatus($id)
    {
        $student = $this->studentModel->find($id);
        
        if (!$student) {
            return $this->respondWithError('Aluno não encontrado');
        }
        
        $newStatus = $student->is_active ? 0 : 1;
        
        $this->studentModel->update($id, ['is_active' => $newStatus]);
        $this->userModel->update($student->user_id, ['is_active' => $newStatus]);
        
        return $this->respondWithSuccess(
            $newStatus ? 'Aluno ativado com sucesso' : 'Aluno desativado com sucesso'
        );
    }
    /**
     * Search students (AJAX)
     * 
     * @return JSON
     */
    public function search()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([]);
        }
        
        $query = $this->request->getGet('q');
        
        if (strlen($query) < 3) {
            return $this->response->setJSON([]);
        }
        
        $students = $this->studentModel
            ->select('tbl_students.id, tbl_users.first_name, tbl_users.last_name, tbl_students.student_number')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->groupStart()
                ->like('tbl_users.first_name', $query)
                ->orLike('tbl_users.last_name', $query)
                ->orLike('tbl_students.student_number', $query)
            ->groupEnd()
            ->where('tbl_students.is_active', 1)
            ->orderBy('tbl_users.first_name', 'ASC')
            ->limit(20)
            ->findAll();
        
        return $this->response->setJSON($students);
    }
}