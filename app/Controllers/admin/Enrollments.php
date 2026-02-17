<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\EnrollmentModel;
use App\Models\StudentModel;
use App\Models\ClassModel;
use App\Models\AcademicYearModel;
use App\Models\FeeStructureModel;

class Enrollments extends BaseController
{
    protected $enrollmentModel;
    protected $studentModel;
    protected $classModel;
    protected $academicYearModel;
    protected $feeStructureModel;
    
    public function __construct()
    {
        $this->enrollmentModel = new EnrollmentModel();
        $this->studentModel = new StudentModel();
        $this->classModel = new ClassModel();
        $this->academicYearModel = new AcademicYearModel();
        $this->feeStructureModel = new FeeStructureModel();
    }
    
    /**
     * List enrollments - Requer permissão 'view_enrollments'
     */
/**
 * List enrollments - Requer permissão 'view_enrollments'
 */
public function index()
{
    // Verificar permissão
    if (!$this->hasPermission('view_enrollments')) {
        return redirect()->to('/admin/dashboard')->with('error', 'Não tem permissão para ver matrículas');
    }
    
    $data['title'] = 'Matrículas';
    
    // Capturar filtros
    $academicYearId = $this->request->getGet('academic_year');
    $gradeLevelId = $this->request->getGet('grade_level');
    $classId = $this->request->getGet('class_id');
    $status = $this->request->getGet('status');
    
    // Obter ano letivo atual
    $currentYear = $this->academicYearModel->getCurrent();
    $currentYearId = $currentYear ? $currentYear->id : null;
    
    $builder = $this->enrollmentModel
        ->select('
            tbl_enrollments.*, 
            tbl_users.first_name, 
            tbl_users.last_name, 
            tbl_students.student_number, 
            tbl_classes.class_name,
            tbl_classes.class_shift,
            tbl_grade_levels.level_name,
            tbl_academic_years.year_name
        ')
        ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
        ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
        ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id', 'left')
        ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_enrollments.grade_level_id', 'left')
        ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_enrollments.academic_year_id');
    
    if ($academicYearId) {
        $builder->where('tbl_enrollments.academic_year_id', $academicYearId);
    }
    
    if ($gradeLevelId) {
        $builder->where('tbl_enrollments.grade_level_id', $gradeLevelId);
    }
    
    if ($classId) {
        $builder->where('tbl_enrollments.class_id', $classId);
    }
    
    if ($status) {
        $builder->where('tbl_enrollments.status', $status);
    }
    
    $data['enrollments'] = $builder->orderBy('tbl_enrollments.created_at', 'DESC')
        ->paginate(10);
    
    $data['pager'] = $this->enrollmentModel->pager;
    $data['totalFiltered'] = $builder->countAllResults(false);
    
    // Dados para filtros
    $data['academicYears'] = $this->academicYearModel->where('is_active', 1)->findAll();
    $data['gradeLevels'] = (new \App\Models\GradeLevelModel())->where('is_active', 1)->orderBy('sort_order', 'ASC')->findAll();
    $data['classes'] = $this->classModel
        ->select('tbl_classes.*, tbl_academic_years.year_name')
        ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_classes.academic_year_id')
        ->where('tbl_classes.is_active', 1)
        ->orderBy('tbl_academic_years.start_date', 'DESC')
        ->orderBy('tbl_classes.class_name', 'ASC')
        ->findAll();
    
    // Estatísticas
    $data['totalEnrollments'] = $this->enrollmentModel->countAll();
    $data['activeEnrollments'] = $this->enrollmentModel->where('status', 'Ativo')->countAllResults();
    $data['pendingEnrollments'] = $this->enrollmentModel->where('status', 'Pendente')->countAllResults();
    $data['completedEnrollments'] = $this->enrollmentModel->where('status', 'Concluído')->countAllResults();
    
    // Valores selecionados para manter nos filtros
    $data['selectedYear'] = $academicYearId;
    $data['selectedGradeLevel'] = $gradeLevelId;
    $data['selectedClass'] = $classId;
    $data['selectedStatus'] = $status;
    
    return view('admin/students/enrollments/index', $data);
}
    /**
     * Enrollment form - Requer permissão 'create_enrollments' ou 'edit_enrollments'
     */
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
    
    // Buscar alunos ativos
    $data['students'] = $this->studentModel
        ->select('tbl_students.id, tbl_users.first_name, tbl_users.last_name, tbl_students.student_number')
        ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
        ->where('tbl_students.is_active', 1)
        ->orderBy('tbl_users.first_name', 'ASC')
        ->findAll();
    
    // Buscar níveis de ensino
    $gradeLevelModel = new \App\Models\GradeLevelModel();
    $data['gradeLevels'] = $gradeLevelModel
        ->where('is_active', 1)
        ->orderBy('sort_order', 'ASC')
        ->findAll();
    
    // Buscar anos letivos ativos
    $data['academicYears'] = $this->academicYearModel
        ->where('is_active', 1)
        ->orderBy('start_date', 'DESC')
        ->findAll();
    
    $data['currentYear'] = $currentYear;
    
    // VALORES SELECIONADOS PARA OS SELECTS
    // Prioridade: old() > dados da matrícula > valores padrão
    
    // Student ID - se for edição, usar o ID da matrícula
    if ($id && $data['enrollment']) {
        $selectedStudent = $data['enrollment']->student_id;
    } else {
        $selectedStudent = null;
    }
    $data['selectedStudent'] = old('student_id', $selectedStudent);
    
    // Year ID - se for edição, usar o ano da matrícula, senão o ano atual
    if ($id && $data['enrollment']) {
        $selectedYear = $data['enrollment']->academic_year_id;
    } else {
        $selectedYear = $currentYear->id;
    }
    $data['selectedYear'] = old('academic_year_id', $selectedYear);
    
    // Grade Level ID - se for edição, usar o nível da matrícula
    if ($id && $data['enrollment']) {
        $selectedGradeLevel = $data['enrollment']->grade_level_id;
    } else {
        $selectedGradeLevel = null;
    }
    $data['selectedGradeLevel'] = old('grade_level_id', $selectedGradeLevel);
    
    // Class ID - se for edição, usar a turma da matrícula
    if ($id && $data['enrollment']) {
        $selectedClass = $data['enrollment']->class_id;
    } else {
        $selectedClass = null;
    }
    $data['selectedClass'] = old('class_id', $selectedClass);
    
    // Enrollment Type
    if ($id && $data['enrollment']) {
        $selectedEnrollmentType = $data['enrollment']->enrollment_type;
    } else {
        $selectedEnrollmentType = 'Nova';
    }
    $data['selectedEnrollmentType'] = old('enrollment_type', $selectedEnrollmentType);
    
    // Status
    if ($id && $data['enrollment']) {
        $selectedStatus = $data['enrollment']->status;
    } else {
        $selectedStatus = 'Pendente';
    }
    $data['selectedStatus'] = old('status', $selectedStatus);
    
    // Previous Grade ID
    if ($id && $data['enrollment']) {
        $selectedPreviousGrade = $data['enrollment']->previous_grade_id;
    } else {
        $selectedPreviousGrade = null;
    }
    $data['selectedPreviousGrade'] = old('previous_grade_id', $selectedPreviousGrade);
    
    // Buscar turmas disponíveis baseadas no ano e nível selecionados
    $classQuery = $this->classModel
        ->select('
            tbl_classes.*, 
            tbl_grade_levels.level_name,
            (SELECT COUNT(*) FROM tbl_enrollments 
             WHERE class_id = tbl_classes.id 
             AND status = "Ativo") as enrolled_count
        ')
        ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
        ->where('tbl_classes.is_active', 1);
    
    // Filtrar por ano letivo
    if ($data['selectedYear']) {
        $classQuery->where('tbl_classes.academic_year_id', $data['selectedYear']);
    }
    
    // Filtrar por nível se selecionado
    if ($data['selectedGradeLevel']) {
        $classQuery->where('tbl_classes.grade_level_id', $data['selectedGradeLevel']);
    }
    
    $classes = $classQuery->orderBy('tbl_classes.class_name', 'ASC')->findAll();
    
    // Calcular vagas disponíveis para cada turma
    foreach ($classes as $class) {
        $class->enrolled_count = $class->enrolled_count ?? 0;
        $class->available_seats = $class->capacity - $class->enrolled_count;
    }
    
    $data['classes'] = $classes;
    
    // Se for uma matrícula pendente, buscar nome do nível para exibição
    if ($id && $data['enrollment'] && $data['enrollment']->status == 'Pendente' && $data['selectedGradeLevel']) {
        $gradeLevel = $gradeLevelModel->find($data['selectedGradeLevel']);
        if ($gradeLevel) {
            $data['enrollment']->grade_level_name = $gradeLevel->level_name;
        }
    }
    
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
    if ($status == 'Pendente') {
        return redirect()->to('/admin/students/enrollments/pending')
            ->with('success', $message);
    } else {
        return redirect()->to('/admin/students/enrollments')
            ->with('success', $message);
    }
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
    $data['enrollment'] = $this->enrollmentModel->getForView($id); // <-- USAR O NOVO MÉTODO
    
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
        
        $data['title'] = 'Histórico do Aluno: ' . $student->first_name . ' ' . $student->last_name;
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
            tbl_academic_years.year_name
        ')
        ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
        ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
        ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_enrollments.grade_level_id')
        ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_enrollments.academic_year_id')
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
    
    if ($enrollment->status != 'Pendente') {
        return redirect()->back()->with('error', 'Esta matrícula não está pendente');
    }
    
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
    
    return redirect()->to('/admin/students/enrollments/pending')
        ->with('success', 'Matrícula aprovada com sucesso');
}
}