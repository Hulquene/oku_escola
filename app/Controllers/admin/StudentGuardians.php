<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\GuardianModel;
use App\Models\StudentGuardianModel;
use App\Models\StudentModel;
use App\Models\ClassModel;

class StudentGuardians extends BaseController
{
    protected $guardianModel;
    protected $studentGuardianModel;
    protected $studentModel;
    protected $classModel;
    
    public function __construct()
    {
        $this->guardianModel = new GuardianModel();
        $this->studentGuardianModel = new StudentGuardianModel();
        $this->studentModel = new StudentModel();
        $this->classModel = new ClassModel();
        
        // Verificar permissões
        if (!has_permission('students.guardians')) {
            return redirect()->to('/admin/dashboard')->with('error', 'Sem permissão para acessar esta página');
        }
    }
    
    /**
     * List all guardians
     */
    public function index()
    {
        $data['title'] = 'Encarregados de Educação';
        
        // Get all guardians with student count
        $guardians = $this->guardianModel
            ->select('tbl_guardians.*, COUNT(tbl_student_guardians.student_id) as student_count')
            ->join('tbl_student_guardians', 'tbl_student_guardians.guardian_id = tbl_guardians.id', 'left')
            ->groupBy('tbl_guardians.id')
            ->orderBy('tbl_guardians.full_name', 'ASC')
            ->findAll();
        
        $data['guardians'] = $guardians;
        $data['totalGuardians'] = count($guardians);
        
        // Get all students for association modal
        $data['students'] = $this->studentModel
            ->select('tbl_students.*, tbl_classes.class_name, tbl_enrollments.class_id')
            ->join('tbl_enrollments', 'tbl_enrollments.student_id = tbl_students.id AND tbl_enrollments.status = "Ativo"', 'left')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id', 'left')
            ->orderBy('tbl_students.first_name', 'ASC')
            ->findAll();
        
        // Get all classes for filter
        $data['classes'] = $this->classModel
            ->where('is_active', 1)
            ->orderBy('class_name', 'ASC')
            ->findAll();
        
        // Get guardian-student associations for each guardian
        $guardianStudents = [];
        foreach ($guardians as $guardian) {
            $guardianStudents[$guardian->id] = $this->studentGuardianModel
                ->where('guardian_id', $guardian->id)
                ->findColumn('student_id') ?? [];
        }
        $data['guardianStudents'] = $guardianStudents;
        
        return view('admin/students/guardians/index', $data);
    }
    
    /**
     * Save guardian (create or update)
     */
    public function save()
    {
        $rules = [
            'full_name' => 'required|min_length[3]|max_length[255]',
            'guardian_type' => 'required|in_list[Pai,Mãe,Tutor,Encarregado,Outro]',
            'phone' => 'required|min_length[9]|max_length[20]',
            'email' => 'permit_empty|valid_email|max_length[255]',
            'identity_document' => 'permit_empty|max_length[50]',
            'nif' => 'permit_empty|max_length[20]',
        ];
        
        $messages = [
            'full_name' => [
                'required' => 'O nome completo é obrigatório',
                'min_length' => 'O nome deve ter pelo menos 3 caracteres'
            ],
            'guardian_type' => [
                'required' => 'O tipo de encarregado é obrigatório',
                'in_list' => 'Tipo de encarregado inválido'
            ],
            'phone' => [
                'required' => 'O telefone é obrigatório',
                'min_length' => 'Telefone deve ter pelo menos 9 dígitos'
            ],
            'email' => [
                'valid_email' => 'Email inválido'
            ]
        ];
        
        if (!$this->validate($rules, $messages)) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'errors' => $this->validator->getErrors()
                ]);
            }
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'full_name' => $this->request->getPost('full_name'),
            'guardian_type' => $this->request->getPost('guardian_type'),
            'phone' => $this->request->getPost('phone'),
            'phone2' => $this->request->getPost('phone2'),
            'email' => $this->request->getPost('email'),
            'profession' => $this->request->getPost('profession'),
            'workplace' => $this->request->getPost('workplace'),
            'identity_type' => $this->request->getPost('identity_type'),
            'identity_document' => $this->request->getPost('identity_document'),
            'nif' => $this->request->getPost('nif'),
            'birth_date' => $this->request->getPost('birth_date'),
            'address' => $this->request->getPost('address'),
            'city' => $this->request->getPost('city'),
            'municipality' => $this->request->getPost('municipality'),
            'province' => $this->request->getPost('province'),
            'notes' => $this->request->getPost('notes'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];
        
        $id = $this->request->getPost('id');
        
        try {
            if ($id) {
                $this->guardianModel->update($id, $data);
                $message = 'Encarregado atualizado com sucesso';
            } else {
                $this->guardianModel->insert($data);
                $id = $this->guardianModel->getInsertID();
                $message = 'Encarregado criado com sucesso';
            }
            
            // If student_id is provided, link guardian to student
            $studentId = $this->request->getPost('student_id');
            if ($studentId) {
                $this->associateGuardianToStudent($studentId, $id);
                return redirect()->to('/admin/students/view/' . $studentId)
                    ->with('success', 'Encarregado associado com sucesso');
            }
            
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => $message,
                    'id' => $id,
                    'guardian' => $this->guardianModel->find($id)
                ]);
            }
            
            return redirect()->to('/admin/students/guardians')->with('success', $message);
            
        } catch (\Exception $e) {
            if ($this->request->isAJAX()) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Erro ao salvar: ' . $e->getMessage()
                ]);
            }
            return redirect()->back()->withInput()->with('error', 'Erro ao salvar: ' . $e->getMessage());
        }
    }
    
    /**
     * Associate multiple students to guardian
     */
    public function associateStudents()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Requisição inválida']);
        }
        
        $guardianId = $this->request->getPost('guardian_id');
        $studentIds = $this->request->getPost('student_ids') ?? [];
        $relationship = $this->request->getPost('relationship') ?? 'Encarregado';
        
        if (!$guardianId) {
            return $this->response->setJSON(['success' => false, 'message' => 'ID do encarregado não fornecido']);
        }
        
        try {
            // Get existing associations
            $existing = $this->studentGuardianModel
                ->where('guardian_id', $guardianId)
                ->findColumn('student_id') ?? [];
            
            // Remove associations that are not in the new list
            $toRemove = array_diff($existing, $studentIds);
            if (!empty($toRemove)) {
                $this->studentGuardianModel
                    ->where('guardian_id', $guardianId)
                    ->whereIn('student_id', $toRemove)
                    ->delete();
            }
            
            // Add new associations
            foreach ($studentIds as $studentId) {
                if (!in_array($studentId, $existing)) {
                    $this->studentGuardianModel->insert([
                        'student_id' => $studentId,
                        'guardian_id' => $guardianId,
                        'relationship' => $relationship,
                        'is_authorized' => 1
                    ]);
                }
            }
            
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Alunos associados com sucesso',
                'total' => count($studentIds)
            ]);
            
        } catch (\Exception $e) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao associar alunos: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Get guardians by student (AJAX)
     */
    public function getByStudent($studentId)
    {
        $guardians = $this->guardianModel->getByStudent($studentId);
        
        return $this->response->setJSON($guardians);
    }
    
    /**
     * Get available guardians for student (AJAX)
     */
    public function getAvailable($studentId)
    {
        // Get guardians already linked to this student
        $linkedGuardians = $this->studentGuardianModel
            ->where('student_id', $studentId)
            ->findAll();
        
        $linkedIds = array_column($linkedGuardians, 'guardian_id');
        
        // Get all active guardians not linked
        $guardians = $this->guardianModel
            ->where('is_active', 1)
            ->orderBy('full_name', 'ASC')
            ->findAll();
        
        // Filter out linked guardians
        $available = array_filter($guardians, function($guardian) use ($linkedIds) {
            return !in_array($guardian->id, $linkedIds);
        });
        
        return $this->response->setJSON(array_values($available));
    }
    
    /**
     * Link guardian to student (AJAX)
     */
    public function linkToStudent()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Requisição inválida']);
        }
        
        $studentId = $this->request->getPost('student_id');
        $guardianId = $this->request->getPost('guardian_id');
        $relationship = $this->request->getPost('relationship') ?? 'Encarregado';
        $isPrimary = $this->request->getPost('is_primary') ? 1 : 0;
        
        // Check if already linked
        $existing = $this->studentGuardianModel
            ->where('student_id', $studentId)
            ->where('guardian_id', $guardianId)
            ->first();
        
        if ($existing) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Este encarregado já está associado a este aluno'
            ]);
        }
        
        // If setting as primary, remove primary from others
        if ($isPrimary) {
            $this->studentGuardianModel
                ->where('student_id', $studentId)
                ->set(['is_primary' => 0])
                ->update();
        }
        
        $this->studentGuardianModel->insert([
            'student_id' => $studentId,
            'guardian_id' => $guardianId,
            'relationship' => $relationship,
            'is_primary' => $isPrimary,
            'is_authorized' => 1
        ]);
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Encarregado associado com sucesso'
        ]);
    }
    
    /**
     * Remove guardian from student (AJAX)
     */
    public function removeFromStudent()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Requisição inválida']);
        }
        
        $studentId = $this->request->getPost('student_id');
        $guardianId = $this->request->getPost('guardian_id');
        
        $this->studentGuardianModel
            ->where('student_id', $studentId)
            ->where('guardian_id', $guardianId)
            ->delete();
        
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Encarregado removido com sucesso'
        ]);
    }
    
    /**
     * View guardian details
     */
    public function view($id)
    {
        $data['title'] = 'Detalhes do Encarregado';
        $data['guardian'] = $this->guardianModel->find($id);
        
        if (!$data['guardian']) {
            return redirect()->to('/admin/students/guardians')->with('error', 'Encarregado não encontrado');
        }
        
        // Get students linked to this guardian
        $data['students'] = $this->guardianModel->getStudents($id);
        
        // Get association count
        $data['totalStudents'] = count($data['students']);
        
        return view('admin/students/guardians/view', $data);
    }
    
    /**
     * Delete guardian
     */
    public function delete($id)
    {
        try {
            // Check if guardian has students associated
            $hasStudents = $this->studentGuardianModel
                ->where('guardian_id', $id)
                ->countAllResults();
            
            if ($hasStudents > 0) {
                return redirect()->back()->with('error', 'Não é possível eliminar porque este encarregado possui alunos associados');
            }
            
            $this->guardianModel->delete($id);
            
            return redirect()->to('/admin/students/guardians')->with('success', 'Encarregado eliminado com sucesso');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Erro ao eliminar: ' . $e->getMessage());
        }
    }
    
    /**
     * Toggle guardian status (activate/deactivate)
     */
    public function toggleStatus($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Requisição inválida']);
        }
        
        $guardian = $this->guardianModel->find($id);
        if (!$guardian) {
            return $this->response->setJSON(['success' => false, 'message' => 'Encarregado não encontrado']);
        }
        
        $newStatus = $guardian->is_active ? 0 : 1;
        $this->guardianModel->update($id, ['is_active' => $newStatus]);
        
        return $this->response->setJSON([
            'success' => true,
            'is_active' => $newStatus,
            'message' => $newStatus ? 'Encarregado ativado' : 'Encarregado desativado'
        ]);
    }
    
    /**
     * Export guardians to CSV
     */
    public function export()
    {
        $guardians = $this->guardianModel
            ->select('tbl_guardians.*, COUNT(tbl_student_guardians.student_id) as student_count')
            ->join('tbl_student_guardians', 'tbl_student_guardians.guardian_id = tbl_guardians.id', 'left')
            ->groupBy('tbl_guardians.id')
            ->orderBy('tbl_guardians.full_name', 'ASC')
            ->findAll();
        
        $filename = 'encarregados_' . date('Y-m-d') . '.csv';
        
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        
        $output = fopen('php://output', 'w');
        
        // Headers
        fputcsv($output, [
            'ID',
            'Nome Completo',
            'Tipo',
            'Telefone',
            'Email',
            'Profissão',
            'Local Trabalho',
            'Documento',
            'NIF',
            'Endereço',
            'Cidade',
            'Província',
            'Total Alunos',
            'Status',
            'Data Cadastro'
        ]);
        
        // Data
        foreach ($guardians as $g) {
            fputcsv($output, [
                $g->id,
                $g->full_name,
                $g->guardian_type,
                $g->phone,
                $g->email,
                $g->profession,
                $g->workplace,
                $g->identity_document ? $g->identity_type . ': ' . $g->identity_document : '-',
                $g->nif ?? '-',
                $g->address ?? '-',
                $g->city ?? '-',
                $g->province ?? '-',
                $g->student_count ?? 0,
                $g->is_active ? 'Ativo' : 'Inativo',
                date('d/m/Y', strtotime($g->created_at))
            ]);
        }
        
        fclose($output);
        exit;
    }
    
    /**
     * Helper method to associate guardian to student
     */
    private function associateGuardianToStudent($studentId, $guardianId)
    {
        $existing = $this->studentGuardianModel
            ->where('student_id', $studentId)
            ->where('guardian_id', $guardianId)
            ->first();
        
        if (!$existing) {
            $this->studentGuardianModel->insert([
                'student_id' => $studentId,
                'guardian_id' => $guardianId,
                'relationship' => $this->request->getPost('relationship') ?? 'Encarregado',
                'is_authorized' => 1
            ]);
        }
    }
    
    /**
     * Get guardian data for edit (AJAX)
     */
    public function getGuardian($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Requisição inválida']);
        }
        
        $guardian = $this->guardianModel->find($id);
        
        if (!$guardian) {
            return $this->response->setJSON(['success' => false, 'message' => 'Encarregado não encontrado']);
        }
        
        return $this->response->setJSON([
            'success' => true,
            'guardian' => $guardian
        ]);
    }
    
    /**
     * Get students by class (AJAX for filtering)
     */
    public function getStudentsByClass($classId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Requisição inválida']);
        }
        
        $students = $this->studentModel
            ->select('tbl_students.*, tbl_enrollments.class_id')
            ->join('tbl_enrollments', 'tbl_enrollments.student_id = tbl_students.id AND tbl_enrollments.status = "Ativo"', 'inner')
            ->where('tbl_enrollments.class_id', $classId)
            ->orderBy('tbl_students.first_name', 'ASC')
            ->findAll();
        
        return $this->response->setJSON($students);
    }
}