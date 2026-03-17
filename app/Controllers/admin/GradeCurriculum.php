<?php
// app/Controllers/admin/GradeCurriculum.php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\GradeLevelModel;
use App\Models\DisciplineModel;
use App\Models\GradeDisciplineModel;

class GradeCurriculum extends BaseController
{
    protected $gradeLevelModel;
    protected $disciplineModel;
    protected $gradeDisciplineModel;
    
    public function __construct()
    {
        $this->gradeLevelModel = new GradeLevelModel();
        $this->disciplineModel = new DisciplineModel();
        $this->gradeDisciplineModel = new GradeDisciplineModel();
    }
    
    /**
     * List curriculum by grade level
     */
    public function index($gradeLevelId = null)
    {
        // Se não for especificado um nível, redirecionar para lista de níveis
        if (!$gradeLevelId) {
            return redirect()->to('/admin/classes/levels')->with('info', 'Selecione um nível de ensino para configurar as disciplinas.');
        }
        
        $gradeLevel = $this->gradeLevelModel->find($gradeLevelId);
      
        if (!$gradeLevel) {
            return redirect()->to('/admin/classes/levels')->with('error', 'Nível de ensino não encontrado');
        }
        
        $data['title'] = 'Disciplinas - ' . $gradeLevel->level_name;
        $data['gradeLevel'] = $gradeLevel;
        
        // Buscar todos os níveis de ensino para o menu lateral
        // Filtrar apenas Ensino Geral (até 9ª classe) ou todos?
        $data['allLevels'] = $this->gradeLevelModel
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->findAll();
        
        // Buscar disciplinas configuradas para este nível
        $data['disciplines'] = $this->gradeDisciplineModel
            ->select('
                tbl_grade_disciplines.*,
                tbl_disciplines.discipline_name,
                tbl_disciplines.discipline_code,
                tbl_disciplines.workload_hours as default_workload,
                tbl_disciplines.approval_grade,
                tbl_disciplines.discipline_type
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_grade_disciplines.discipline_id')
            ->where('tbl_grade_disciplines.grade_level_id', $gradeLevelId)
            ->where('tbl_disciplines.is_active', 1)
            ->orderBy('tbl_disciplines.discipline_name', 'ASC')
            ->findAll();
        
        // Calcular carga horária total
        $totalWorkload = 0;
        foreach ($data['disciplines'] as $d) {
            $totalWorkload += ($d->workload_hours ?? $d->default_workload ?? 0);
        }
        $data['totalWorkload'] = $totalWorkload;
        
   
        return view('admin/grade_levels/curriculum', $data);
    }
    
    /**
     * Add discipline to grade level
     */
    public function addDiscipline()
    {
        $gradeLevelId = $this->request->getPost('grade_level_id');
        $disciplineId = $this->request->getPost('discipline_id');
        
        $rules = [
            'grade_level_id' => 'required|numeric',
            'discipline_id' => 'required|numeric',
            'workload_hours' => 'permit_empty|numeric|greater_than[0]|less_than[1000]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        // Verificar se já existe
        if ($this->gradeDisciplineModel->exists($gradeLevelId, $disciplineId)) {
            return redirect()->back()->withInput()
                ->with('error', 'Esta disciplina já está atribuída a este nível de ensino');
        }
        
        $data = [
            'grade_level_id' => $gradeLevelId,
            'discipline_id' => $disciplineId,
            'workload_hours' => $this->request->getPost('workload_hours') ?: null,
            'is_mandatory' => $this->request->getPost('is_mandatory') ? 1 : 0,
            'semester' => $this->request->getPost('semester') ?: 'Anual'
        ];
        
        if ($this->gradeDisciplineModel->insert($data)) {
            return redirect()->to('/admin/grade-curriculum/' . $gradeLevelId)
                ->with('success', 'Disciplina adicionada ao nível de ensino');
        } else {
            $errors = $this->gradeDisciplineModel->errors();
            return redirect()->back()->withInput()
                ->with('errors', $errors);
        }
    }
    
    /**
     * Edit curriculum discipline
     */
    public function editDiscipline($id)
    {
        $item = $this->gradeDisciplineModel
            ->select('
                tbl_grade_disciplines.*,
                tbl_grade_levels.level_name,
                tbl_disciplines.discipline_name,
                tbl_disciplines.discipline_code,
                tbl_disciplines.workload_hours as default_workload,
                tbl_disciplines.approval_grade,
                tbl_disciplines.discipline_type
            ')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_grade_disciplines.grade_level_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_grade_disciplines.discipline_id')
            ->where('tbl_grade_disciplines.id', $id)
            ->first();
        
        if (!$item) {
            return redirect()->back()->with('error', 'Item não encontrado');
        }
        
        $data['title'] = 'Editar Disciplina no Nível de Ensino';
        $data['item'] = $item;
        
        return view('admin/grade_levels/edit_discipline', $data);
    }
    
    /**
     * Update curriculum discipline
     */
    public function updateDiscipline($id)
    {
        $rules = [
            'workload_hours' => 'permit_empty|numeric|greater_than[0]|less_than[1000]',
            'semester' => 'permit_empty|in_list[Anual,1º Semestre,2º Semestre]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'workload_hours' => $this->request->getPost('workload_hours') ?: null,
            'is_mandatory' => $this->request->getPost('is_mandatory') ? 1 : 0,
            'semester' => $this->request->getPost('semester') ?: 'Anual'
        ];
        
        $item = $this->gradeDisciplineModel->find($id);
        
        if (!$item) {
            return redirect()->back()->with('error', 'Item não encontrado');
        }
        
        if ($this->gradeDisciplineModel->update($id, $data)) {
            return redirect()->to('/admin/grade-curriculum/' . $item->grade_level_id)
                ->with('success', 'Disciplina atualizada no nível de ensino');
        } else {
            $errors = $this->gradeDisciplineModel->errors();
            return redirect()->back()->withInput()
                ->with('errors', $errors);
        }
    }
    
    /**
     * Remove discipline from grade level
     */
    public function removeDiscipline($id)
    {
        $item = $this->gradeDisciplineModel->find($id);
        
        if (!$item) {
            return redirect()->back()->with('error', 'Item não encontrado');
        }
        
        $gradeLevelId = $item->grade_level_id;
        
        $this->gradeDisciplineModel->delete($id);
        
        return redirect()->to('/admin/grade-curriculum/' . $gradeLevelId)
            ->with('success', 'Disciplina removida do nível de ensino');
    }
    
    /**
     * Get available disciplines for grade level (AJAX)
     */
    public function getAvailableDisciplines($gradeLevelId)
    {
        // Disciplinas já atribuídas a este nível
        $assigned = $this->gradeDisciplineModel
            ->where('grade_level_id', $gradeLevelId)
            ->findAll();
        
        $assignedIds = array_column($assigned, 'discipline_id');
        
        // Todas as disciplinas ativas
        $allDisciplines = $this->disciplineModel
            ->where('is_active', 1)
            ->orderBy('discipline_name', 'ASC')
            ->findAll();
        
        // Filtrar as não atribuídas
        $available = array_filter($allDisciplines, function($discipline) use ($assignedIds) {
            return !in_array($discipline['id'], $assignedIds);
        });
        
        return $this->response->setJSON(array_values($available));
    }
}