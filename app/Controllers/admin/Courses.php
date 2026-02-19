<?php
// app/Controllers/admin/Courses.php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\CourseModel;
use App\Models\GradeLevelModel;
use App\Models\DisciplineModel;
use App\Models\CourseDisciplineModel;

class Courses extends BaseController
{
    protected $courseModel;
    protected $gradeLevelModel;
    protected $disciplineModel;
    protected $courseDisciplineModel;
    
    public function __construct()
    {
        $this->courseModel = new CourseModel();
        $this->gradeLevelModel = new GradeLevelModel();
        $this->disciplineModel = new DisciplineModel();
        $this->courseDisciplineModel = new CourseDisciplineModel();
    }
    
    /**
     * List courses
     */
    public function index()
    {
        $data['title'] = 'Cursos (Ensino Médio)';
        
        $type = $this->request->getGet('type');
        $status = $this->request->getGet('status');
        
        $builder = $this->courseModel;
        
        if ($type) {
            $builder->where('course_type', $type);
        }
        
        if ($status == 'active') {
            $builder->where('is_active', 1);
        } elseif ($status == 'inactive') {
            $builder->where('is_active', 0);
        }
        
        $data['courses'] = $builder->orderBy('course_name', 'ASC')
            ->paginate(10);
        
        $data['pager'] = $this->courseModel->pager;
        
        // Estatísticas
        $data['stats'] = $this->courseModel->getStatistics();
        
        // Filtros
        $data['types'] = [
            'Ciências' => 'Ciências',
            'Humanidades' => 'Humanidades',
            'Económico-Jurídico' => 'Económico-Jurídico',
            'Técnico' => 'Técnico',
            'Profissional' => 'Profissional',
            'Outro' => 'Outro'
        ];
        
        $data['selectedType'] = $type;
        $data['selectedStatus'] = $status;
        
        return view('admin/courses/index', $data);
    }
    
    /**
     * Course form (add/edit)
     */
    public function form($id = null)
    {
        $data['title'] = $id ? 'Editar Curso' : 'Novo Curso';
        $data['course'] = $id ? $this->courseModel->find($id) : null;
        
        // Níveis de ensino para seleção
        $data['gradeLevels'] = $this->gradeLevelModel
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->findAll();
        
        // Níveis de ensino médio (para início/fim)
        $data['highSchoolLevels'] = $this->gradeLevelModel
            ->whereIn('education_level', ['2º Ciclo', 'Ensino Médio'])
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->findAll();
        
        return view('admin/courses/form', $data);
    }
    
 
    /**
 * Save course
 */
public function save()
{
    $id = $this->request->getPost('id');
    
    // Coletar dados do formulário
    $data = [
        'course_name' => $this->request->getPost('course_name'),
        'course_code' => $this->request->getPost('course_code'),
        'course_type' => $this->request->getPost('course_type'),
        'description' => $this->request->getPost('description'),
        'duration_years' => $this->request->getPost('duration_years') ?: 3,
        'start_grade_id' => $this->request->getPost('start_grade_id'),
        'end_grade_id' => $this->request->getPost('end_grade_id'),
        'is_active' => $this->request->getPost('is_active') ? 1 : 0
    ];
    
    // Se for edição, incluir o ID
    if ($id) {
        $data['id'] = $id;
    }
    
    // Validar que end_grade_id é maior que start_grade_id (regra de negócio)
    $startGrade = $data['start_grade_id'];
    $endGrade = $data['end_grade_id'];
    
    if ($endGrade <= $startGrade) {
        return redirect()->back()->withInput()
            ->with('error', 'O nível final deve ser maior que o nível inicial');
    }
    
    // Usar o model para salvar (ele fará a validação automaticamente)
    if ($this->courseModel->save($data)) {
        $message = $id ? 'Curso atualizado com sucesso' : 'Curso criado com sucesso';
        return redirect()->to('/admin/courses')->with('success', $message);
    } else {
        $errors = $this->courseModel->errors();
        return redirect()->back()->withInput()
            ->with('errors', $errors);
    }
}
    
    /**
     * View course details with curriculum
     */
    public function view($id)
    {
        $data['title'] = 'Detalhes do Curso';
        $data['course'] = $this->courseModel->getWithGradeLevels($id);
        
        if (!$data['course']) {
            return redirect()->to('/admin/courses')->with('error', 'Curso não encontrado');
        }
        
        // Buscar currículo completo do curso
        $data['curriculum'] = $this->courseDisciplineModel->getFullCourseCurriculum($id);
        
        // Agrupar por nível
        $groupedCurriculum = [];
        foreach ($data['curriculum'] as $item) {
            $levelId = $item->grade_level_id;
            if (!isset($groupedCurriculum[$levelId])) {
                $groupedCurriculum[$levelId] = [
                    'level_name' => $item->level_name,
                    'disciplines' => []
                ];
            }
            $groupedCurriculum[$levelId]['disciplines'][] = $item;
        }
        $data['groupedCurriculum'] = $groupedCurriculum;
        
        // Estatísticas
        $totalWorkload = 0;
        foreach ($data['curriculum'] as $item) {
            $totalWorkload += $item->workload_hours ?? 0;
        }
        $data['totalWorkload'] = $totalWorkload;
        $data['totalDisciplines'] = count($data['curriculum']);
        
        return view('admin/courses/view', $data);
    }
    
    /**
     * Delete course
     */
    public function delete($id)
    {
        $course = $this->courseModel->find($id);
        
        if (!$course) {
            return redirect()->back()->with('error', 'Curso não encontrado');
        }
        
        // Verificar se há turmas usando este curso
        $classModel = new \App\Models\ClassModel();
        $classes = $classModel->where('course_id', $id)->countAllResults();
        
        if ($classes > 0) {
            return redirect()->back()
                ->with('error', "Não é possível eliminar este curso porque existem {$classes} turmas associadas");
        }
        
        // Verificar se há matrículas usando este curso
        $enrollmentModel = new \App\Models\EnrollmentModel();
        $enrollments = $enrollmentModel->where('course_id', $id)->countAllResults();
        
        if ($enrollments > 0) {
            return redirect()->back()
                ->with('error', "Não é possível eliminar este curso porque existem {$enrollments} matrículas associadas");
        }
        
        $this->courseModel->delete($id);
        
        return redirect()->to('/admin/courses')
            ->with('success', 'Curso eliminado com sucesso');
    }
}