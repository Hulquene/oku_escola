<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\GuardianModel;
use App\Models\StudentGuardianModel;
use App\Models\StudentModel;

class StudentGuardians extends BaseController
{
    protected $guardianModel;
    protected $studentGuardianModel;
    protected $studentModel;
    
    public function __construct()
    {
        $this->guardianModel = new GuardianModel();
        $this->studentGuardianModel = new StudentGuardianModel();
        $this->studentModel = new StudentModel();
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
        
        return view('admin/students/guardians/index', $data);
    }
    
    /**
     * Save guardian
     */
    public function save()
    {
        $rules = [
            'full_name' => 'required',
            'guardian_type' => 'required',
            'phone' => 'required'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'full_name' => $this->request->getPost('full_name'),
            'guardian_type' => $this->request->getPost('guardian_type'),
            'phone' => $this->request->getPost('phone'),
            'email' => $this->request->getPost('email'),
            'profession' => $this->request->getPost('profession'),
            'workplace' => $this->request->getPost('workplace'),
            'identity_type' => $this->request->getPost('identity_type'),
            'identity_document' => $this->request->getPost('identity_document'),
            'nif' => $this->request->getPost('nif'),
            'address' => $this->request->getPost('address'),
            'city' => $this->request->getPost('city'),
            'municipality' => $this->request->getPost('municipality'),
            'province' => $this->request->getPost('province'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];
        
        $id = $this->request->getPost('id');
        
        if ($id) {
            $this->guardianModel->update($id, $data);
            $message = 'Encarregado atualizado com sucesso';
        } else {
            $this->guardianModel->insert($data);
            $message = 'Encarregado criado com sucesso';
        }
        
        // If student_id is provided, link guardian to student
        $studentId = $this->request->getPost('student_id');
        if ($studentId) {
            $guardianId = $id ?: $this->guardianModel->getInsertID();
            
            // Check if already linked
            $existing = $this->studentGuardianModel
                ->where('student_id', $studentId)
                ->where('guardian_id', $guardianId)
                ->first();
            
            if (!$existing) {
                $this->studentGuardianModel->insert([
                    'student_id' => $studentId,
                    'guardian_id' => $guardianId,
                    'relationship' => $this->request->getPost('relationship'),
                    'is_authorized' => 1
                ]);
            }
            
            return redirect()->to('/admin/students/view/' . $studentId)
                ->with('success', 'Encarregado associado com sucesso');
        }
        
        return redirect()->to('/admin/students/guardians')->with('success', $message);
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
        $relationship = $this->request->getPost('relationship');
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
        
        return view('admin/students/guardians/view', $data);
    }
}