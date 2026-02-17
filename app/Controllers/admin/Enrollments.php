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
    public function index()
    {
        // Verificar permissão
        if (!$this->hasPermission('view_enrollments')) {
            return redirect()->to('/admin/dashboard')->with('error', 'Não tem permissão para ver matrículas');
        }
        
        $data['title'] = 'Matrículas';
        
        // Capturar filtros
        $academicYearId = $this->request->getGet('academic_year');
        $classId = $this->request->getGet('class');
        $status = $this->request->getGet('status');
        
        $builder = $this->enrollmentModel
            ->select('tbl_enrollments.*, tbl_users.first_name, tbl_users.last_name, tbl_students.student_number, tbl_classes.class_name, tbl_academic_years.year_name')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_enrollments.academic_year_id');
        
        if ($academicYearId) {
            $builder->where('tbl_enrollments.academic_year_id', $academicYearId);
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
        
        // Filters data
        $data['academicYears'] = $this->academicYearModel->findAll();
        $data['classes'] = $this->classModel->findAll();
        
        // Valores selecionados para manter nos filtros
        $data['selectedYear'] = $academicYearId;
        $data['selectedClass'] = $classId;
        $data['selectedStatus'] = $status;
        
        return view('admin/students/enrollments/index', $data);
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
        
        $data['students'] = $this->studentModel
            ->select('tbl_students.id, tbl_users.first_name, tbl_users.last_name, tbl_students.student_number')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->where('tbl_students.is_active', 1)
            ->orderBy('tbl_users.first_name', 'ASC')
            ->findAll();
        
        $data['classes'] = $this->classModel
            ->select('tbl_classes.*, tbl_grade_levels.level_name')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
            ->where('tbl_classes.academic_year_id', $currentYear->id)
            ->where('tbl_classes.is_active', 1)
            ->orderBy('tbl_classes.class_name', 'ASC')
            ->findAll();
        
        $data['academicYears'] = $this->academicYearModel->getActive();
        
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
            // Atualizando matrícula existente
            if (!$this->hasPermission('edit_enrollments')) {
                return redirect()->to('/admin/students/enrollments')->with('error', 'Não tem permissão para editar matrículas');
            }
        } else {
            // Criando nova matrícula
            if (!$this->hasPermission('create_enrollments')) {
                return redirect()->to('/admin/students/enrollments')->with('error', 'Não tem permissão para criar matrículas');
            }
        }
        
        $rules = [
            'student_id' => 'required|numeric',
            'class_id' => 'required|numeric',
            'academic_year_id' => 'required|numeric',
            'enrollment_date' => 'required|valid_date',
            'enrollment_type' => 'required'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        $studentId = $this->request->getPost('student_id');
        $classId = $this->request->getPost('class_id');
        $academicYearId = $this->request->getPost('academic_year_id');
        
        // Check if already enrolled
        $existing = $this->enrollmentModel
            ->where('student_id', $studentId)
            ->where('academic_year_id', $academicYearId)
            ->where('status', 'Ativo')
            ->first();
        
        if ($existing && !$id) {
            return redirect()->back()->withInput()
                ->with('error', 'Aluno já possui matrícula ativa neste ano letivo');
        }
        
        // Check class capacity
        $availableSeats = $this->classModel->getAvailableSeats($classId);
        if ($availableSeats <= 0) {
            return redirect()->back()->withInput()
                ->with('error', 'Turma sem vagas disponíveis');
        }
        
        $data = [
            'student_id' => $studentId,
            'class_id' => $classId,
            'academic_year_id' => $academicYearId,
            'enrollment_date' => $this->request->getPost('enrollment_date'),
            'enrollment_number' => $this->enrollmentModel->generateEnrollmentNumber(),
            'enrollment_type' => $this->request->getPost('enrollment_type'),
            'previous_class_id' => $this->request->getPost('previous_class_id') ?: null,
            'status' => $this->request->getPost('status') ?: 'Ativo',
            'observations' => $this->request->getPost('observations'),
            'created_by' => $this->session->get('user_id')
        ];
        
        if ($id) {
            // Don't change enrollment number on update
            unset($data['enrollment_number']);
            $this->enrollmentModel->update($id, $data);
            $message = 'Matrícula atualizada com sucesso';
        } else {
            $this->enrollmentModel->insert($data);
            $message = 'Matrícula realizada com sucesso';
        }
        
        return redirect()->to('/admin/students/enrollments')->with('success', $message);
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
        $data['enrollment'] = $this->enrollmentModel->getWithDetails($id);
        
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
}