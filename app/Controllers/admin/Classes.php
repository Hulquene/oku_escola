<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\ClassModel;
use App\Models\GradeLevelModel;
use App\Models\AcademicYearModel;
use App\Models\UserModel;
use App\Models\EnrollmentModel;

class Classes extends BaseController
{
    protected $classModel;
    protected $gradeLevelModel;
    protected $academicYearModel;
    protected $userModel;
    protected $enrollmentModel;
    
    public function __construct()
    {
        $this->classModel = new ClassModel();
        $this->gradeLevelModel = new GradeLevelModel();
        $this->academicYearModel = new AcademicYearModel();
        $this->userModel = new UserModel();
        $this->enrollmentModel = new EnrollmentModel();
    }
   /**
     * List classes
     */
    public function index()
    {
        $data['title'] = 'Turmas';
        
        $academicYearId = $this->request->getGet('academic_year');
        $gradeLevelId = $this->request->getGet('grade_level');
        $shift = $this->request->getGet('shift');
        $status = $this->request->getGet('status');
        
        $builder = $this->classModel
            ->select('tbl_classes.*, tbl_grade_levels.level_name, tbl_academic_years.year_name, tbl_users.first_name as teacher_first_name, tbl_users.last_name as teacher_last_name')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_classes.academic_year_id')
            ->join('tbl_users', 'tbl_users.id = tbl_classes.class_teacher_id', 'left');
        
        if ($academicYearId) {
            $builder->where('tbl_classes.academic_year_id', $academicYearId);
        }
        
        if ($gradeLevelId) {
            $builder->where('tbl_classes.grade_level_id', $gradeLevelId);
        }
        
        if ($shift) {
            $builder->where('tbl_classes.class_shift', $shift);
        }
        
        if ($status == 'active') {
            $builder->where('tbl_classes.is_active', 1);
        } elseif ($status == 'inactive') {
            $builder->where('tbl_classes.is_active', 0);
        }
        
        $data['classes'] = $builder->orderBy('tbl_academic_years.start_date', 'DESC')
            ->orderBy('tbl_classes.class_name', 'ASC')
            ->paginate(10);
        
        $data['pager'] = $this->classModel->pager;
        
        // Filters
        $data['academicYears'] = $this->academicYearModel->findAll();
        $data['gradeLevels'] = $this->gradeLevelModel->getActive();
        $data['selectedYear'] = $academicYearId;
        $data['selectedLevel'] = $gradeLevelId;
        $data['selectedShift'] = $shift;
        $data['selectedStatus'] = $status;
        
        // CAMINHO CORRIGIDO: apontando para a pasta classes/classes/
        return view('admin/classes/classes/index', $data);
    }

    /**
     * Class form
     */
    public function form($id = null)
    {
        $data['title'] = $id ? 'Editar Turma' : 'Nova Turma';
        $data['class'] = $id ? $this->classModel->find($id) : null;
        
        $data['gradeLevels'] = $this->gradeLevelModel->getActive();
        $data['academicYears'] = $this->academicYearModel->getActive();
        $data['teachers'] = $this->userModel->getByType('teacher');
        
        // CAMINHO CORRIGIDO
        return view('admin/classes/classes/form', $data);
    }

 /**
 * View class details
 */
public function view($id)
{
    $data['title'] = 'Detalhes da Turma';
    $data['class'] = $this->classModel->getWithTeacherAndAcademicYear($id);
    
    if (!$data['class']) {
        return redirect()->to('/admin/classes')->with('error', 'Turma não encontrada');
    }
    
    // Get students in this class
    $data['students'] = $this->enrollmentModel
        ->select('tbl_enrollments.*, tbl_students.student_number, tbl_users.first_name, tbl_users.last_name')
        ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
        ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
        ->where('tbl_enrollments.class_id', $id)
        ->where('tbl_enrollments.status', 'Ativo')
        ->orderBy('tbl_users.first_name', 'ASC')
        ->findAll();
    
    $data['availableSeats'] = $this->classModel->getAvailableSeats($id);
    
    return view('admin/classes/classes/view', $data);
}
public function listStudents($id)
{
    $class = $this->classModel->getWithAcademicYear($id);
    
    if (!$class) {
        return redirect()->to('/admin/classes')->with('error', 'Turma não encontrada');
    }
    
    // resto do código igual
    $data['title'] = 'Alunos da Turma ' . $class->class_name;
    $data['class'] = $class;
    $data['students'] = $this->enrollmentModel
        ->select('tbl_enrollments.*, tbl_students.student_number, tbl_users.first_name, tbl_users.last_name, tbl_users.email, tbl_users.phone')
        ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
        ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
        ->where('tbl_enrollments.class_id', $id)
        ->where('tbl_enrollments.status', 'Ativo')
        ->orderBy('tbl_users.first_name', 'ASC')
        ->findAll();
    
    return view('admin/classes/classes/students', $data);
}
   /**
 * Save class (Create or Update)
 */
public function save()
{
    $id = $this->request->getPost('id');
    
    // Preparar dados
    $data = [
        'class_name' => $this->request->getPost('class_name'),
        'class_code' => $this->request->getPost('class_code'),
        'grade_level_id' => $this->request->getPost('grade_level_id'),
        'academic_year_id' => $this->request->getPost('academic_year_id'),
        'class_shift' => $this->request->getPost('class_shift'),
        'class_room' => $this->request->getPost('class_room'),
        'capacity' => $this->request->getPost('capacity') ?: null,
        'class_teacher_id' => $this->request->getPost('class_teacher_id') ?: null,
        'is_active' => $this->request->getPost('is_active') ? 1 : 0
    ];
    
    // Se for atualização, incluir o ID nos dados
    if ($id) {
        $data['id'] = $id;
    }
    
    // Validação adicional: verificar se já existe turma com mesmo nome no mesmo ano letivo
    $existingClass = $this->classModel
        ->where('class_name', $data['class_name'])
        ->where('academic_year_id', $data['academic_year_id'])
        ->where('id !=', $id ?: 0)
        ->first();
    
    if ($existingClass) {
        return redirect()->back()->withInput()
            ->with('error', 'Já existe uma turma com este nome no mesmo ano letivo.');
    }
    
    // Validação adicional: código único
    $existingCode = $this->classModel
        ->where('class_code', $data['class_code'])
        ->where('id !=', $id ?: 0)
        ->first();
    
    if ($existingCode) {
        return redirect()->back()->withInput()
            ->with('error', 'Este código de turma já está em uso.');
    }
    
    // Usar o método save() do model que já lida com placeholders
    if ($this->classModel->save($data)) {
        $action = $id ? 'atualizada' : 'criada';
        $message = "Turma '{$data['class_name']}' {$action} com sucesso!";
        
        log_message('info', "Turma ID " . ($id ?: $this->classModel->getInsertID()) . " {$action} por usuário " . session()->get('user_id'));
        
        return redirect()->to('/admin/classes/classes')
            ->with('success', $message);
    } else {
        $errors = $this->classModel->errors();
        if (!empty($errors)) {
            return redirect()->back()->withInput()
                ->with('errors', $errors);
        }
        return redirect()->back()->withInput()
            ->with('error', 'Erro ao ' . ($id ? 'atualizar' : 'criar') . ' turma.');
    }
}
    /**
 * Delete class
 */
public function delete($id)
{
    $class = $this->classModel->find($id);
    
    if (!$class) {
        return redirect()->back()->with('error', 'Turma não encontrada.');
    }
    
    // Verificar se existem alunos matriculados
    $enrollmentsCount = $this->enrollmentModel
        ->where('class_id', $id)
        ->where('status', 'Ativo')
        ->countAllResults();
    
    if ($enrollmentsCount > 0) {
        return redirect()->back()
            ->with('error', "Não é possível eliminar esta turma porque existem {$enrollmentsCount} alunos matriculados.");
    }
    
    // Soft delete ou delete real? Vamos fazer soft delete atualizando status
    $data = ['is_active' => 0];
    
    if ($this->classModel->update($id, $data)) {
        log_message('info', "Turma ID {$id} desativada por usuário " . session()->get('user_id'));
        return redirect()->to('/admin/classes/classes')
            ->with('success', 'Turma desativada com sucesso.');
    } else {
        return redirect()->back()->with('error', 'Erro ao desativar turma.');
    }
}

/**
 * Activate class
 */
public function activate($id)
{
    $class = $this->classModel->find($id);
    
    if (!$class) {
        return redirect()->back()->with('error', 'Turma não encontrada.');
    }
    
    $data = ['is_active' => 1];
    
    if ($this->classModel->update($id, $data)) {
        log_message('info', "Turma ID {$id} ativada por usuário " . session()->get('user_id'));
        return redirect()->to('/admin/classes/classes')
            ->with('success', 'Turma ativada com sucesso.');
    } else {
        return redirect()->back()->with('error', 'Erro ao ativar turma.');
    }
}
}