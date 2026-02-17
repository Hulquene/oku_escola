<?php

namespace App\Controllers\Admin;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\ClassModel;
use App\Models\ClassDisciplineModel;

class Teachers extends BaseController
{
    protected $userModel;
    protected $classModel;
    protected $classDisciplineModel;
    
    public function __construct()
    {
        $this->userModel = new UserModel();
        $this->classModel = new ClassModel();
        $this->classDisciplineModel = new ClassDisciplineModel();
    }
    
    /**
     * List teachers
     */
    public function index()
    {
        $data['title'] = 'Professores';
        $data['teachers'] = $this->userModel
            ->where('user_type', 'teacher')
            ->where('is_active', 1)
            ->orderBy('first_name', 'ASC')
            ->paginate(10);
        
        $data['pager'] = $this->userModel->pager;
        
        return view('admin/teachers/index', $data);
    }
    
    /**
     * Teacher form
     */
    public function form($id = null)
    {
        $data['title'] = $id ? 'Editar Professor' : 'Novo Professor';
        $data['teacher'] = $id ? $this->userModel->find($id) : null;
        
        return view('admin/teachers/form', $data);
    }
    
    /**
     * Save teacher
     */
    public function save()
    {
        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|valid_email|is_unique[tbl_users.email,id,{id}]',
            'phone' => 'required'
        ];
        
        if (!$this->request->getPost('id')) {
            $rules['password'] = 'required|min_length[6]';
        }
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address'),
            'user_type' => 'teacher',
            'role_id' => 4, // Teacher role
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];
        
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }
        
        $id = $this->request->getPost('id');
        
        if ($id) {
            // For update, don't overwrite username
            $this->userModel->update($id, $data);
            $message = 'Professor atualizado com sucesso';
        } else {
            $data['username'] = $this->request->getPost('email');
            $this->userModel->insert($data);
            $message = 'Professor criado com sucesso';
        }
        
        return redirect()->to('/admin/teachers')->with('success', $message);
    }
    
    /**
     * View teacher
     */
    public function view($id)
    {
        $data['title'] = 'Detalhes do Professor';
        $data['teacher'] = $this->userModel->find($id);
        
        if (!$data['teacher']) {
            return redirect()->to('/admin/teachers')->with('error', 'Professor não encontrado');
        }
        
        // Get assigned classes/disciplines
        $data['assignments'] = $this->classDisciplineModel
            ->select('tbl_class_disciplines.*, tbl_classes.class_name, tbl_disciplines.discipline_name')
            ->join('tbl_classes', 'tbl_classes.id = tbl_class_disciplines.class_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
            ->where('tbl_class_disciplines.teacher_id', $id)
            ->where('tbl_classes.is_active', 1)
            ->findAll();
        
        return view('admin/teachers/view', $data);
    }
    
    /**
     * Assign class to teacher
     */
    public function assignClass($id)
    {
        $data['title'] = 'Atribuir Turmas/Disciplinas';
        $data['teacher'] = $this->userModel->find($id);
        
        if (!$data['teacher']) {
            return redirect()->to('/admin/teachers')->with('error', 'Professor não encontrado');
        }
        
        // Get available classes
        $academicYearModel = new \App\Models\AcademicYearModel();
        $currentYear = $academicYearModel->getCurrent();
        
        $data['classes'] = $this->classModel
            ->select('tbl_classes.*, tbl_grade_levels.level_name')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
            ->where('tbl_classes.academic_year_id', $currentYear->id)
            ->where('tbl_classes.is_active', 1)
            ->orderBy('tbl_classes.class_name', 'ASC')
            ->findAll();
        
        // Get current assignments
        $data['assignments'] = $this->classDisciplineModel
            ->where('teacher_id', $id)
            ->findAll();
        
        return view('admin/teachers/assign', $data);
    }
    
    /**
     * Save assignment
     */
    public function saveAssignment()
    {
        $teacherId = $this->request->getPost('teacher_id');
        $assignments = $this->request->getPost('assignments') ?? [];
        
        if (!$teacherId) {
            return redirect()->back()->with('error', 'Professor não especificado');
        }
        
        $db = db_connect();
        $db->transStart();
        
        // Remove existing assignments
        $this->classDisciplineModel->where('teacher_id', $teacherId)->delete();
        
        // Add new assignments
        foreach ($assignments as $assignment) {
            list($classId, $disciplineId) = explode('_', $assignment);
            
            $this->classDisciplineModel->insert([
                'class_id' => $classId,
                'discipline_id' => $disciplineId,
                'teacher_id' => $teacherId,
                'is_active' => 1
            ]);
        }
        
        $db->transComplete();
        
        if ($db->transStatus()) {
            return redirect()->to('/admin/teachers/view/' . $teacherId)
                ->with('success', 'Atribuições atualizadas com sucesso');
        } else {
            return redirect()->back()->with('error', 'Erro ao atualizar atribuições');
        }
    }
    
    /**
     * Delete teacher
     */
    public function delete($id)
    {
        $teacher = $this->userModel->find($id);
        
        if (!$teacher) {
            return redirect()->back()->with('error', 'Professor não encontrado');
        }
        
        // Check if has class assignments
        $assignments = $this->classDisciplineModel
            ->where('teacher_id', $id)
            ->countAllResults();
        
        if ($assignments > 0) {
            return redirect()->back()
                ->with('error', 'Não é possível eliminar professor com turmas atribuídas');
        }
        
        // Soft delete - just deactivate
        $this->userModel->update($id, ['is_active' => 0]);
        
        return redirect()->to('/admin/teachers')->with('success', 'Professor desativado com sucesso');
    }
}