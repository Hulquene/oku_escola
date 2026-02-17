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
        
        $search = $this->request->getGet('search');
        
        $builder = $this->studentModel
            ->select('tbl_students.*, tbl_users.first_name, tbl_users.last_name, tbl_users.email, tbl_users.phone as user_phone')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id');
        
        if ($search) {
            $builder->groupStart()
                ->like('tbl_users.first_name', $search)
                ->orLike('tbl_users.last_name', $search)
                ->orLike('tbl_students.student_number', $search)
                ->orLike('tbl_students.identity_document', $search)
                ->groupEnd();
        }
        
        $data['students'] = $builder->orderBy('tbl_users.first_name', 'ASC')
            ->paginate(10);
        
        $data['pager'] = $this->studentModel->pager;
        $data['search'] = $search;
        
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
        $data['classes'] = $this->classModel->getByAcademicYear(
            $this->academicYearModel->getCurrent()->id ?? 0
        );
        $data['guardians'] = $id ? $this->guardianModel->getByStudent($id) : [];
        
        return view('admin/students/form', $data);
    }
    
    /**
     * Save student
     */
    public function saveStudent()
    {
        $rules = [
            'first_name' => 'required',
            'last_name' => 'required',
            'email' => 'required|valid_email|is_unique[tbl_users.email,id,{user_id}]',
            'birth_date' => 'required|valid_date',
            'gender' => 'required'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        $db = db_connect();
        $db->transStart();
        
        $userId = $this->request->getPost('user_id');
        
        // Prepare user data
        $userData = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'email' => $this->request->getPost('email'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address'),
            'user_type' => 'student',
            'role_id' => 5, // Student role
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];
        
        if ($userId) {
            // Update existing user
            $this->userModel->update($userId, $userData);
        } else {
            // Create new user
            $userData['username'] = $this->request->getPost('email');
            $userData['password'] = password_hash('123456', PASSWORD_DEFAULT); // Default password
            $userId = $this->userModel->insert($userData);
        }
        
        // Prepare student data
        $studentData = [
            'user_id' => $userId,
            'birth_date' => $this->request->getPost('birth_date'),
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
            'previous_grade' => $this->request->getPost('previous_grade'),
            'special_needs' => $this->request->getPost('special_needs'),
            'health_conditions' => $this->request->getPost('health_conditions'),
            'blood_type' => $this->request->getPost('blood_type')
        ];
        
        $studentId = $this->request->getPost('id');
        
        if ($studentId) {
            $this->studentModel->update($studentId, $studentData);
        } else {
            $studentData['student_number'] = $this->studentModel->generateStudentNumber();
            $studentId = $this->studentModel->insert($studentData);
        }
        
        // Handle enrollment if class selected
        $classId = $this->request->getPost('class_id');
        if ($classId) {
            $currentYear = $this->academicYearModel->getCurrent();
            
            // Check if already enrolled
            $existingEnrollment = $this->enrollmentModel
                ->where('student_id', $studentId)
                ->where('academic_year_id', $currentYear->id)
                ->where('status', 'Ativo')
                ->first();
            
            if (!$existingEnrollment) {
                $enrollmentData = [
                    'student_id' => $studentId,
                    'class_id' => $classId,
                    'academic_year_id' => $currentYear->id,
                    'enrollment_date' => date('Y-m-d'),
                    'enrollment_number' => $this->enrollmentModel->generateEnrollmentNumber(),
                    'enrollment_type' => 'Nova',
                    'status' => 'Ativo',
                    'created_by' => $this->session->get('user_id')
                ];
                
                $this->enrollmentModel->insert($enrollmentData);
            }
        }
        
        $db->transComplete();
        
        if ($db->transStatus()) {
            return redirect()->to('/admin/students')
                ->with('success', 'Aluno salvo com sucesso');
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