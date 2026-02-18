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
    
    public function __construct()
    {
        $this->studentModel = new StudentModel();
        $this->userModel = new UserModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->classModel = new ClassModel();
        $this->academicYearModel = new AcademicYearModel();
        $this->guardianModel = new GuardianModel();
        $this->studentGuardianModel = new StudentGuardianModel();
    }
    
    /**
     * Students list (alias for index)
     */
    public function students()
    {
        return $this->index();
    }
/**
 * Students list
 */
public function index()
{
    $data['title'] = 'Alunos';
    
    // Capturar filtros
    $search = $this->request->getGet('search');
    $academicYearId = $this->request->getGet('academic_year');
    $classId = $this->request->getGet('class_id');
    $enrollmentStatus = $this->request->getGet('enrollment_status');
    $gender = $this->request->getGet('gender');
    $status = $this->request->getGet('status');
    
    // Obter ano letivo atual
    $currentYear = $this->academicYearModel->getCurrent();
    $currentYearId = $currentYear ? $currentYear->id : null;
    
    // Se não selecionou ano letivo E não está filtrando por "não matriculado", usar o atual por padrão
    if (!$academicYearId && $currentYearId && $enrollmentStatus != 'nao_matriculado') {
        $academicYearId = $currentYearId;
    }
    
    $builder = $this->studentModel
        ->select('
            tbl_students.*, 
            tbl_users.first_name, 
            tbl_users.last_name, 
            tbl_users.email, 
            tbl_users.phone,
            active_enroll.class_name as current_class,
            active_enroll.class_shift as current_shift,
            active_enroll.year_name as current_year,
            active_enroll.level_name as current_level_name,
            active_enroll.status as enrollment_status,
            active_enroll.id as enrollment_id,
            pending_enroll.academic_year_id as pending_year_id,
            pending_enroll.grade_level_id as pending_level_id,
            pending_year.year_name as pending_year_name,
            pending_level.level_name as pending_level_name
        ')
        ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
        
        // LEFT JOIN para matrícula ativa
        ->join('(
            SELECT e.*, 
                   c.class_name, 
                   c.class_shift,
                   ay.year_name,
                   gl.level_name
            FROM tbl_enrollments e
            LEFT JOIN tbl_classes c ON c.id = e.class_id
            LEFT JOIN tbl_academic_years ay ON ay.id = e.academic_year_id
            LEFT JOIN tbl_grade_levels gl ON gl.id = e.grade_level_id
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
        ->join('tbl_grade_levels as pending_level', 'pending_level.id = pending_enroll.grade_level_id', 'left');
    
    // Aplicar filtros
    if ($search) {
        $builder->groupStart()
            ->like('tbl_users.first_name', $search)
            ->orLike('tbl_users.last_name', $search)
            ->orLike('tbl_students.student_number', $search)
            ->orLike('tbl_students.identity_document', $search)
            ->orLike('tbl_students.nif', $search)
            ->orLike('tbl_users.email', $search)
            ->groupEnd();
    }
    
    // FILTRO DE ANO LETIVO - considerar tanto ativo quanto pendente
    if ($academicYearId) {
        if ($enrollmentStatus == 'nao_matriculado') {
            // Se está filtrando por não matriculado, NÃO aplica filtro de ano
        } else {
            $builder->where('(active_enroll.academic_year_id = ' . $academicYearId . ' OR pending_enroll.academic_year_id = ' . $academicYearId . ')');
        }
    }
    
    if ($classId) {
        $builder->where('active_enroll.class_id', $classId);
    }
    
    if ($enrollmentStatus) {
        if ($enrollmentStatus == 'nao_matriculado') {
            $builder->where('active_enroll.id IS NULL AND pending_enroll.id IS NULL');
        } elseif ($enrollmentStatus == 'Pendente') {
            $builder->where('active_enroll.id IS NULL AND pending_enroll.id IS NOT NULL');
        } elseif ($enrollmentStatus == 'Ativo') {
            $builder->where('active_enroll.id IS NOT NULL');
        }
    }
    
    if ($gender) {
        $builder->where('tbl_students.gender', $gender);
    }
    
    if ($status == 'active') {
        $builder->where('tbl_students.is_active', 1);
    } elseif ($status == 'inactive') {
        $builder->where('tbl_students.is_active', 0);
    }
    
    $data['students'] = $builder->orderBy('tbl_users.first_name', 'ASC')
        ->paginate(10);
    
    $data['pager'] = $this->studentModel->pager;
    $data['search'] = $search;
    $data['selectedYear'] = $academicYearId;
    $data['selectedClass'] = $classId;
    $data['selectedEnrollmentStatus'] = $enrollmentStatus;
    $data['selectedGender'] = $gender;
    $data['selectedStatus'] = $status;
    
    // Dados para filtros
    $data['academicYears'] = $this->academicYearModel->where('is_active', 1)->findAll();
    $data['classes'] = $this->classModel
        ->select('tbl_classes.*, tbl_academic_years.year_name')
        ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_classes.academic_year_id')
        ->where('tbl_classes.is_active', 1)
        ->orderBy('tbl_academic_years.start_date', 'DESC')
        ->orderBy('tbl_classes.class_name', 'ASC')
        ->findAll();
    
    // Estatísticas
    $data['totalStudents'] = $this->studentModel->countAll();
    
    // Matriculados (independente do ano)
    $data['enrolledStudents'] = $this->enrollmentModel
        ->where('status', 'Ativo')
        ->countAllResults();
    
    // Pendentes
    $data['pendingEnrollments'] = $this->enrollmentModel
        ->where('status', 'Pendente')
        ->countAllResults();
    
    // Não matriculados (alunos sem nenhuma matrícula ativa ou pendente)
    $enrolledIds = $this->enrollmentModel
        ->select('student_id')
        ->whereIn('status', ['Ativo', 'Pendente'])
        ->findAll();
    $enrolledIds = array_column($enrolledIds, 'student_id');
    
    $data['nonEnrolledStudents'] = $this->studentModel
        ->whereNotIn('id', $enrolledIds)
        ->countAllResults();
    
    $data['maleCount'] = $this->studentModel->where('gender', 'Masculino')->countAllResults();
    $data['femaleCount'] = $this->studentModel->where('gender', 'Feminino')->countAllResults();
    $data['totalFiltered'] = $builder->countAllResults(false);
    
    return view('admin/students/index', $data);
}
    /**
     * Get students for datatable
     */
    public function get_students_table()
    {
        if (!$this->request->isAJAX()) {
            return $this->respondWithError('Requisição inválida');
        }
        
        $datatable = $this->getDatatableRequest();
        
        $columns = ['id', 'student_number', 'first_name', 'last_name', 'email', 'phone', 'is_active'];
        $searchable = ['student_number', 'first_name', 'last_name', 'email'];
        
        $builder = $this->studentModel
            ->select('tbl_students.*, tbl_users.first_name, tbl_users.last_name, tbl_users.email, tbl_users.phone')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id');
        
        // Apply search
        if (!empty($datatable['search'])) {
            $builder->groupStart();
            foreach ($searchable as $field) {
                if (in_array($field, ['first_name', 'last_name', 'email', 'phone'])) {
                    $builder->orLike('tbl_users.' . $field, $datatable['search']);
                } else {
                    $builder->orLike('tbl_students.' . $field, $datatable['search']);
                }
            }
            $builder->groupEnd();
        }
        
        $recordsTotal = $this->studentModel->countAllResults();
        $recordsFiltered = $builder->countAllResults(false);
        
        // Apply order
        if (!empty($datatable['order'])) {
            $order = $datatable['order'][0];
            $columnIndex = $order['column'];
            $columnName = $columns[$columnIndex] ?? 'id';
            $dir = $order['dir'];
            
            if (in_array($columnName, ['first_name', 'last_name', 'email', 'phone'])) {
                $builder->orderBy('tbl_users.' . $columnName, $dir);
            } else {
                $builder->orderBy('tbl_students.' . $columnName, $dir);
            }
        } else {
            $builder->orderBy('tbl_users.first_name', 'ASC');
        }
        
        $builder->limit($datatable['length'], $datatable['start']);
        
        $data = $builder->get()->getResult();
        
        return $this->respondWithJson([
            'draw' => $datatable['draw'],
            'recordsTotal' => $recordsTotal,
            'recordsFiltered' => $recordsFiltered,
            'data' => $data
        ]);
    }
    
    /**
     * Student form (add/edit)
     */
    public function form($id = null)
    {
        $data['title'] = $id ? 'Editar Aluno' : 'Novo Aluno';
        $data['student'] = $id ? $this->studentModel->getWithUser($id) : null;
        
        // For student form
        if ($this->request->getGet('type') == 'student') {
            return $this->studentForm($id);
        }
        
        // For regular clients (if needed)
        $data['isStudent'] = true;
        
        return view('admin/students/form', $data);
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
    
    // Inicializar variáveis
    $data['selectedYear'] = $currentYear->id ?? null;
    $data['selectedLevel'] = null;
    $data['selectedEnrollmentType'] = 'Nova';
    $data['selectedPreviousGrade'] = null;
    
    if ($id && $data['student']) {
        // Primeiro, buscar matrícula pendente
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
                $data['selectedEnrollmentType'] = $activeEnrollment->enrollment_type;
                $data['selectedPreviousGrade'] = $activeEnrollment->previous_grade_id;
            }
        }
    }
    
    // Guardiões do aluno
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
    
    // Verificar idade mínima (única validação que fica no controller por ser regra de negócio)
    $birthDate = $this->request->getPost('birth_date');
    $age = date_diff(date_create($birthDate), date_create('today'))->y;
    if ($age < 5) {
        return redirect()->back()->withInput()
            ->with('error', 'O aluno deve ter pelo menos 5 anos de idade');
    }
    
    // Preparar dados do usuário
    $userData = [
        'first_name' => $this->request->getPost('first_name'),
        'last_name' => $this->request->getPost('last_name'),
        'email' => $this->request->getPost('email'),
        'phone' => $this->request->getPost('phone'),
        'address' => $this->request->getPost('address'),
        'user_type' => 'student',
        'role_id' => 5,
        'is_active' => $this->request->getPost('is_active') ? 1 : 0
    ];
    
    if ($userId) {
        // Update existing user
        $existingUser = $this->userModel->find($userId);
        if (!$existingUser) {
            $db->transRollback();
            return redirect()->back()->withInput()
                ->with('error', 'Usuário associado não encontrado');
        }
        
        if (!$this->userModel->update($userId, $userData)) {
            $db->transRollback();
            return redirect()->back()->withInput()
                ->with('errors', $this->userModel->errors());
        }
    } else {
        // Create new user
        $userData['username'] = $this->request->getPost('email');
        $userData['password'] = password_hash('123456', PASSWORD_DEFAULT);
        $userId = $this->userModel->insert($userData);
        
        if (!$userId) {
            $db->transRollback();
            return redirect()->back()->withInput()
                ->with('errors', $this->userModel->errors());
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
        // IMPORTANTE: Adicionar o ID para a validação
        $studentData['id'] = $studentId; 

        if (!$this->studentModel->update($studentId, $studentData)) {
            $db->transRollback();
            return redirect()->back()->withInput()
                ->with('errors', $this->studentModel->errors());
        }
    } else {
        $studentData['student_number'] = $this->studentModel->generateStudentNumber();
        $studentId = $this->studentModel->insert($studentData);
        
        if (!$studentId) {
            $db->transRollback();
            return redirect()->back()->withInput()
                ->with('errors', $this->studentModel->errors());
        }
    }
    
    // Criar matrícula pendente
    if ($studentId) {
        $existingEnrollment = $this->enrollmentModel
            ->where('student_id', $studentId)
            ->where('academic_year_id', $this->request->getPost('academic_year_id'))
            ->whereIn('status', ['Pendente', 'Ativo'])
            ->first();
        
        if (!$existingEnrollment) {
            $enrollmentData = [
                'student_id' => $studentId,
                'academic_year_id' => $this->request->getPost('academic_year_id'),
                'grade_level_id' => $this->request->getPost('grade_level_id'),
                'enrollment_date' => date('Y-m-d'),
                'enrollment_number' => $this->enrollmentModel->generateEnrollmentNumber(),
                'enrollment_type' => $this->request->getPost('enrollment_type'),
                'status' => 'Pendente',
                'created_by' => $this->session->get('user_id')
            ];
            
            if (!$this->enrollmentModel->insert($enrollmentData)) {
                $db->transRollback();
                return redirect()->back()->withInput()
                    ->with('errors', $this->enrollmentModel->errors());
            }
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
        
        // Current enrollment
        $data['currentEnrollment'] = $this->studentModel->getCurrentEnrollment($id);
        
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
}