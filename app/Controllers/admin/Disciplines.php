<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\DisciplineModel;

class Disciplines extends BaseController
{
    protected $disciplineModel;
    
    public function __construct()
    {
        $this->disciplineModel = new DisciplineModel();
    }
    
    /**
     * List disciplines
     */
    public function index()
    {
        $data['title'] = 'Disciplinas';
        $data['subjects'] = $this->disciplineModel
            ->orderBy('discipline_name', 'ASC')
            ->findAll();
        
        return view('admin/classes/subjects/index', $data);
    }
    
    /**
     * Save discipline
     */
    public function save()
    {
        $rules = [
            'discipline_name' => 'required',
            'discipline_code' => 'required|is_unique[tbl_disciplines.discipline_code,id,{id}]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'discipline_name' => $this->request->getPost('discipline_name'),
            'discipline_code' => $this->request->getPost('discipline_code'),
            'discipline_type' => $this->request->getPost('discipline_type') ?: 'Obrigatória',
            'workload_hours' => $this->request->getPost('workload_hours') ?: null,
            'min_grade' => $this->request->getPost('min_grade') ?: 0,
            'max_grade' => $this->request->getPost('max_grade') ?: 20,
            'approval_grade' => $this->request->getPost('approval_grade') ?: 10,
            'description' => $this->request->getPost('description'),
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];
        
        $id = $this->request->getPost('id');
        
        if ($id) {
            $this->disciplineModel->update($id, $data);
            $message = 'Disciplina atualizada com sucesso';
        } else {
            $this->disciplineModel->insert($data);
            $message = 'Disciplina criada com sucesso';
        }
        
        return redirect()->to('/admin/classes/subjects')->with('success', $message);
    }
    
    /**
     * Delete discipline
     */
    public function delete($id)
    {
        $discipline = $this->disciplineModel->find($id);
        
        if (!$discipline) {
            return redirect()->back()->with('error', 'Disciplina não encontrada');
        }
        
        // Check if discipline is assigned to any class
        $classDisciplineModel = new \App\Models\ClassDisciplineModel();
        $assignments = $classDisciplineModel->where('discipline_id', $id)->countAllResults();
        
        if ($assignments > 0) {
            return redirect()->back()->with('error', 'Não é possível eliminar uma disciplina que está atribuída a turmas');
        }
        
        $this->disciplineModel->delete($id);
        
        return redirect()->to('/admin/classes/subjects')->with('success', 'Disciplina eliminada com sucesso');
    }
    
    /**
     * Get disciplines by class (AJAX)
     */
    public function getByClass($classId)
    {
        $disciplines = $this->disciplineModel->getByClass($classId);
        
        return $this->response->setJSON($disciplines);
    }
    
    /**
     * Get available disciplines for class (AJAX)
     */
    public function getAvailableForClass($classId)
    {
        $classModel = new \App\Models\ClassModel();
        $class = $classModel->find($classId);
        
        if (!$class) {
            return $this->response->setJSON([]);
        }
        
        // Get disciplines already assigned
        $classDisciplineModel = new \App\Models\ClassDisciplineModel();
        $assigned = $classDisciplineModel
            ->where('class_id', $classId)
            ->findAll();
        
        $assignedIds = array_column($assigned, 'discipline_id');
        
        // Get all active disciplines
        $allDisciplines = $this->disciplineModel
            ->where('is_active', 1)
            ->orderBy('discipline_name', 'ASC')
            ->findAll();
        
        // Filter out assigned ones
        $available = array_filter($allDisciplines, function($discipline) use ($assignedIds) {
            return !in_array($discipline->id, $assignedIds);
        });
        
        return $this->response->setJSON(array_values($available));
    }
}