<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\GradeLevelModel;

class GradeLevels extends BaseController
{
    protected $gradeLevelModel;
    
    public function __construct()
    {
        $this->gradeLevelModel = new GradeLevelModel();
    }
    
    /**
     * List grade levels
     */
    public function index()
    {
        $data['title'] = 'Níveis de Ensino';
        $data['levels'] = $this->gradeLevelModel
            ->orderBy('sort_order', 'ASC')
            ->findAll();
        
        return view('admin/classes/levels/index', $data);
    }
    
    /**
     * Save grade level
     */
    public function save()
    {
        $rules = [
            'level_name' => 'required',
            'level_code' => 'required|is_unique[tbl_grade_levels.level_code,id,{id}]',
            'education_level' => 'required',
            'grade_number' => 'required|numeric|min_length[1]|max_length[2]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'level_name' => $this->request->getPost('level_name'),
            'level_code' => $this->request->getPost('level_code'),
            'education_level' => $this->request->getPost('education_level'),
            'grade_number' => $this->request->getPost('grade_number'),
            'sort_order' => $this->request->getPost('sort_order') ?: 0,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];
        
        $id = $this->request->getPost('id');
        
        if ($id) {
            $this->gradeLevelModel->update($id, $data);
            $message = 'Nível de ensino atualizado com sucesso';
        } else {
            $this->gradeLevelModel->insert($data);
            $message = 'Nível de ensino criado com sucesso';
        }
        
        return redirect()->to('/admin/classes/levels')->with('success', $message);
    }
    
    /**
     * Delete grade level
     */
    public function delete($id)
    {
        $level = $this->gradeLevelModel->find($id);
        
        if (!$level) {
            return redirect()->back()->with('error', 'Nível de ensino não encontrado');
        }
        
        // Check if level has classes
        $classModel = new \App\Models\ClassModel();
        $classes = $classModel->where('grade_level_id', $id)->countAllResults();
        
        if ($classes > 0) {
            return redirect()->back()->with('error', 'Não é possível eliminar um nível que possui turmas associadas');
        }
        
        $this->gradeLevelModel->delete($id);
        
        return redirect()->to('/admin/classes/levels')->with('success', 'Nível de ensino eliminado com sucesso');
    }
    
    /**
     * Get grade levels by education level (AJAX)
     */
    public function getByEducationLevel($educationLevel)
    {
        $levels = $this->gradeLevelModel
            ->where('education_level', $educationLevel)
            ->where('is_active', 1)
            ->orderBy('grade_number', 'ASC')
            ->findAll();
        
        return $this->response->setJSON($levels);
    }
}