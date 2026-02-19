<?php
// app/Controllers/admin/CourseCurriculum.php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\CourseModel;
use App\Models\DisciplineModel;
use App\Models\GradeLevelModel;
use App\Models\CourseDisciplineModel;

class CourseCurriculum extends BaseController
{
    protected $courseModel;
    protected $disciplineModel;
    protected $gradeLevelModel;
    protected $courseDisciplineModel;
    
    public function __construct()
    {
        $this->courseModel = new CourseModel();
        $this->disciplineModel = new DisciplineModel();
        $this->gradeLevelModel = new GradeLevelModel();
        $this->courseDisciplineModel = new CourseDisciplineModel();
    }
    
    /**
     * List curriculum by course
     */
    public function index($courseId)
    {
        $course = $this->courseModel->find($courseId);
        
        if (!$course) {
            return redirect()->to('/admin/courses')->with('error', 'Curso não encontrado');
        }
        
        $data['title'] = 'Currículo - ' . $course->course_name;
        $data['course'] = $course;
        
        // Buscar níveis do curso
        $levels = $this->gradeLevelModel
            ->where('id >=', $course->start_grade_id)
            ->where('id <=', $course->end_grade_id)
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->findAll();
        
        $data['levels'] = $levels;
        
        // Buscar disciplinas por nível
        $curriculum = [];
        foreach ($levels as $level) {
            $disciplines = $this->courseDisciplineModel
                ->select('
                    tbl_course_disciplines.*,
                    tbl_disciplines.discipline_name,
                    tbl_disciplines.discipline_code,
                    tbl_disciplines.workload_hours as default_workload
                ')
                ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_course_disciplines.discipline_id')
                ->where('tbl_course_disciplines.course_id', $courseId)
                ->where('tbl_course_disciplines.grade_level_id', $level->id)
                ->where('tbl_disciplines.is_active', 1)
                ->orderBy('tbl_disciplines.discipline_name', 'ASC')
                ->findAll();
            
            $curriculum[$level->id] = [
                'level' => $level,
                'disciplines' => $disciplines
            ];
        }
        
        $data['curriculum'] = $curriculum;
        
        return view('admin/courses/curriculum', $data);
    }
    
    /**
     * Add discipline to course level
     */
public function addDiscipline()
{
    $courseId = $this->request->getPost('course_id');
    $levelId = $this->request->getPost('grade_level_id');
    $disciplineId = $this->request->getPost('discipline_id');
    
    $rules = [
        'course_id' => 'required|numeric',
        'grade_level_id' => 'required|numeric',
        'discipline_id' => 'required|numeric',
        'workload_hours' => 'permit_empty|numeric|greater_than[0]|less_than[1000]'
    ];
    
    if (!$this->validate($rules)) {
        return redirect()->back()->withInput()
            ->with('errors', $this->validator->getErrors());
    }
    
    // Verificar se já existe
    if ($this->courseDisciplineModel->isAssigned($courseId, $disciplineId, $levelId)) {
        return redirect()->back()->withInput()
            ->with('error', 'Esta disciplina já está atribuída a este nível do curso');
    }
    
    $data = [
        'course_id' => $courseId,
        'discipline_id' => $disciplineId,
        'grade_level_id' => $levelId,
        'workload_hours' => $this->request->getPost('workload_hours') ?: null,
        'is_mandatory' => $this->request->getPost('is_mandatory') ? 1 : 0,
        'semester' => $this->request->getPost('semester') ?: 'Anual'
    ];
    
    // DEBUG: Ver o que está sendo inserido
    log_message('error', 'Dados para inserção: ' . print_r($data, true));
    
    // DEBUG: Forçar o Model a mostrar a query
    $db = \Config\Database::connect();
    $builder = $db->table('tbl_course_disciplines');
    $sql = $builder->set($data)->getCompiledInsert();
    log_message('error', 'SQL gerado: ' . $sql);
    
    if ($this->courseDisciplineModel->insert($data)) {
        return redirect()->to('/admin/courses/curriculum/' . $courseId)
            ->with('success', 'Disciplina adicionada ao currículo');
    } else {
        $errors = $this->courseDisciplineModel->errors();
        return redirect()->back()->withInput()
            ->with('errors', $errors);
    }
}
    
    /**
     * Edit curriculum discipline
     */
/**
 * Edit curriculum discipline
 */
public function editDiscipline($id)
{
    $item = $this->courseDisciplineModel
        ->select('
            tbl_course_disciplines.*,
            tbl_courses.course_name,
            tbl_grade_levels.level_name,
            tbl_disciplines.discipline_name,
            tbl_disciplines.discipline_code,
            tbl_disciplines.workload_hours as default_workload,  
            tbl_disciplines.approval_grade,
            tbl_disciplines.discipline_type
        ')
        ->join('tbl_courses', 'tbl_courses.id = tbl_course_disciplines.course_id')
        ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_course_disciplines.grade_level_id')
        ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_course_disciplines.discipline_id')
        ->where('tbl_course_disciplines.id', $id)
        ->first();
    
    if (!$item) {
        return redirect()->back()->with('error', 'Item não encontrado');
    }
    
    $data['title'] = 'Editar Disciplina no Currículo';
    $data['item'] = $item;
    
    return view('admin/courses/edit_discipline', $data);
}
    
    /**
     * Update curriculum discipline
     */
    public function updateDiscipline($id)
    {
        $rules = [
            'workload_hours' => 'permit_empty|numeric|greater_than[0]|less_than[1000]',
            'semester' => 'permit_empty|in_list[1º,2º,Anual]'
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
        
        $item = $this->courseDisciplineModel->find($id);
        
        if (!$item) {
            return redirect()->back()->with('error', 'Item não encontrado');
        }
        
        if ($this->courseDisciplineModel->update($id, $data)) {
            return redirect()->to('/admin/courses/curriculum/' . $item->course_id)
                ->with('success', 'Disciplina atualizada no currículo');
        } else {
            $errors = $this->courseDisciplineModel->errors();
            return redirect()->back()->withInput()
                ->with('errors', $errors);
        }
    }
    
    /**
     * Remove discipline from curriculum
     */
    public function removeDiscipline($id)
    {
        $item = $this->courseDisciplineModel->find($id);
        
        if (!$item) {
            return redirect()->back()->with('error', 'Item não encontrado');
        }
        
        $courseId = $item->course_id;
        
        $this->courseDisciplineModel->delete($id);
        
        return redirect()->to('/admin/courses/curriculum/' . $courseId)
            ->with('success', 'Disciplina removida do currículo');
    }
    
    /**
     * Get available disciplines for course level (AJAX)
     */
    public function getAvailableDisciplines($courseId, $levelId)
    {
        // Disciplinas já atribuídas
        $assigned = $this->courseDisciplineModel
            ->where('course_id', $courseId)
            ->where('grade_level_id', $levelId)
            ->findAll();
        
        $assignedIds = array_column($assigned, 'discipline_id');
        
        // Todas as disciplinas ativas
        $allDisciplines = $this->disciplineModel
            ->where('is_active', 1)
            ->orderBy('discipline_name', 'ASC')
            ->findAll();
        
        // Filtrar as não atribuídas
        $available = array_filter($allDisciplines, function($discipline) use ($assignedIds) {
            return !in_array($discipline->id, $assignedIds);
        });
        
        return $this->response->setJSON(array_values($available));
    }
}