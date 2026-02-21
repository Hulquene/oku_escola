<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\ClassDisciplineModel;
use App\Models\ClassModel;
use App\Models\DisciplineModel;
use App\Models\UserModel;
use App\Models\SemesterModel;
use App\Models\AcademicYearModel;

class ClassSubjects extends BaseController
{
    protected $classDisciplineModel;
    protected $classModel;
    protected $disciplineModel;
    protected $userModel;
    protected $semesterModel;
    protected $academicYearModel;
    
    public function __construct()
    {
        $this->classDisciplineModel = new ClassDisciplineModel();
        $this->classModel = new ClassModel();
        $this->disciplineModel = new DisciplineModel();
        $this->userModel = new UserModel();
        $this->semesterModel = new SemesterModel();
        $this->academicYearModel = new AcademicYearModel();
    }
    
/**
 * List class subjects assignments
 */
public function index()
{
    $data['title'] = 'Disciplinas por Turma';
    
    // Capturar filtros
    $academicYearId = $this->request->getGet('academic_year');
    $classId = $this->request->getGet('class');
    $courseId = $this->request->getGet('course');
    $disciplineId = $this->request->getGet('discipline');
    $teacherId = $this->request->getGet('teacher');
    
    // Construir query base
    $builder = $this->classDisciplineModel
        ->select('
            tbl_class_disciplines.*,
            tbl_classes.class_name,
            tbl_classes.class_code,
            tbl_classes.class_shift,
            tbl_classes.course_id,
            tbl_disciplines.discipline_name,
            tbl_disciplines.discipline_code,
            tbl_users.first_name as teacher_first_name,
            tbl_users.last_name as teacher_last_name,
            tbl_semesters.semester_name,
            tbl_courses.course_name,
            tbl_courses.course_code
        ')
        ->join('tbl_classes', 'tbl_classes.id = tbl_class_disciplines.class_id')
        ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
        ->join('tbl_users', 'tbl_users.id = tbl_class_disciplines.teacher_id', 'left')
        ->join('tbl_semesters', 'tbl_semesters.id = tbl_class_disciplines.semester_id', 'left')
        ->join('tbl_courses', 'tbl_courses.id = tbl_classes.course_id', 'left');
    
    // Aplicar filtros
    if ($academicYearId) {
        $builder->where('tbl_classes.academic_year_id', $academicYearId);
    }
    
    if ($classId) {
        $builder->where('tbl_class_disciplines.class_id', $classId);
    }
    
    // NOVO FILTRO: Curso
    if ($courseId !== null && $courseId !== '') {
        if ($courseId == '0') {
            // Ensino Geral (cursos nulos)
            $builder->where('tbl_classes.course_id IS NULL');
        } else {
            $builder->where('tbl_classes.course_id', $courseId);
        }
    }
    
    // NOVO FILTRO: Disciplina
    if ($disciplineId) {
        $builder->where('tbl_class_disciplines.discipline_id', $disciplineId);
    }
    
    // NOVO FILTRO: Professor
    if ($teacherId) {
        if ($teacherId == 'without') {
            $builder->where('tbl_class_disciplines.teacher_id IS NULL');
        } else {
            $builder->where('tbl_class_disciplines.teacher_id', $teacherId);
        }
    }
    
    $data['assignments'] = $builder->orderBy('tbl_classes.class_name', 'ASC')
        ->orderBy('tbl_disciplines.discipline_name', 'ASC')
        ->findAll();
    
    // Calcular estatísticas
    $totalAssignments = count($data['assignments']);
    $withTeacherCount = 0;
    $withoutTeacherCount = 0;
    $uniqueClasses = [];
    
    foreach ($data['assignments'] as $assignment) {
        if ($assignment->teacher_id) {
            $withTeacherCount++;
        } else {
            $withoutTeacherCount++;
        }
        $uniqueClasses[$assignment->class_id] = true;
    }
    
    $data['totalAssignments'] = $totalAssignments;
    $data['withTeacherCount'] = $withTeacherCount;
    $data['withoutTeacherCount'] = $withoutTeacherCount;
    $data['totalClasses'] = count($uniqueClasses);
    $data['totalFiltered'] = $totalAssignments;
    
    // Filters data
    $data['academicYears'] = $this->academicYearModel->where('is_active', 1)->findAll();
    
    $data['classes'] = $this->classModel
        ->select('tbl_classes.*, tbl_academic_years.year_name')
        ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_classes.academic_year_id')
        ->where('tbl_classes.is_active', 1)
        ->orderBy('tbl_academic_years.start_date', 'DESC')
        ->orderBy('tbl_classes.class_name', 'ASC')
        ->findAll();
    
    // NOVO: Buscar cursos para o filtro
    $courseModel = new \App\Models\CourseModel();
    $data['courses'] = $courseModel
        ->where('is_active', 1)
        ->orderBy('course_name', 'ASC')
        ->findAll();
    
    // NOVO: Buscar disciplinas para o filtro
    $data['disciplines'] = $this->disciplineModel
        ->where('is_active', 1)
        ->orderBy('discipline_name', 'ASC')
        ->findAll();
    
    // NOVO: Buscar professores para o filtro
    $data['teachers'] = $this->userModel
        ->where('user_type', 'teacher')
        ->where('is_active', 1)
        ->orderBy('first_name', 'ASC')
        ->findAll();
    
    // Valores selecionados
    $data['selectedYear'] = $academicYearId;
    $data['selectedClass'] = $classId;
    $data['selectedCourse'] = $courseId; // NOVO
    $data['selectedDiscipline'] = $disciplineId; // NOVO
    $data['selectedTeacher'] = $teacherId; // NOVO
    
    // Pager
    $data['pager'] = $this->classDisciplineModel->pager;
    
    return view('admin/classes/class-subjects/index', $data);
}
 /**
 * Assignment form - VERSÃO SIMPLIFICADA (apenas turma e semestre)
 */
public function assign()
{
    $data['title'] = 'Atribuir Disciplina à Turma';
    
    $assignmentId = $this->request->getGet('edit');
    $classId = $this->request->getGet('class');
    
    // Inicializar variáveis
    $data['assignment'] = null;
    $data['selectedDisciplineId'] = null;
    $data['selectedClassId'] = $classId;
    
    // Se temos um ID de atribuição (modo edição)
    if ($assignmentId) {
        $data['assignment'] = $this->classDisciplineModel
            ->select('
                tbl_class_disciplines.*, 
                tbl_classes.class_name, 
                tbl_classes.id as class_id,
                tbl_disciplines.id as discipline_id,
                tbl_disciplines.discipline_name
            ')
            ->join('tbl_classes', 'tbl_classes.id = tbl_class_disciplines.class_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
            ->where('tbl_class_disciplines.id', $assignmentId)
            ->first();
        
        if ($data['assignment']) {
            $data['selectedDisciplineId'] = $data['assignment']->discipline_id;
            $data['selectedClassId'] = $data['assignment']->class_id;
        }
    }
    
    // Buscar todas as turmas ativas
    $data['classes'] = $this->classModel
        ->select('tbl_classes.*, tbl_academic_years.year_name, tbl_grade_levels.level_name')
        ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_classes.academic_year_id')
        ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
        ->where('tbl_classes.is_active', 1)
        ->orderBy('tbl_academic_years.start_date', 'DESC')
        ->orderBy('tbl_classes.class_name', 'ASC')
        ->findAll();
    
    // Buscar professores
    $data['teachers'] = $this->userModel
        ->where('user_type', 'teacher')
        ->where('is_active', 1)
        ->orderBy('first_name', 'ASC')
        ->findAll();
    
    // Buscar semestres
    $currentYear = $this->academicYearModel->getCurrent();
    if ($currentYear) {
        $data['semesters'] = $this->semesterModel
            ->where('academic_year_id', $currentYear->id)
            ->where('is_active', 1)
            ->orderBy('start_date', 'ASC')
            ->findAll();
    } else {
        $data['semesters'] = [];
    }
    
    return view('admin/classes/class-subjects/assign', $data);
}
    
    /**
     * Save assignment
     */
    public function assignSave()
    {
        $rules = [
            'class_id' => 'required|numeric',
            'discipline_id' => 'required|numeric'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        $classId = $this->request->getPost('class_id');
        $disciplineId = $this->request->getPost('discipline_id');
        $semesterId = $this->request->getPost('semester_id');
        
        // Check if already assigned
        $existing = $this->classDisciplineModel
            ->where('class_id', $classId)
            ->where('discipline_id', $disciplineId)
            ->where('semester_id', $semesterId ?: null)
            ->first();
        
        if ($existing && !$this->request->getPost('id')) {
            return redirect()->back()->withInput()
                ->with('error', 'Esta disciplina já está atribuída a esta turma' . 
                       ($semesterId ? ' para o período selecionado' : ''));
        }
        
        $data = [
            'class_id' => $classId,
            'discipline_id' => $disciplineId,
            'teacher_id' => $this->request->getPost('teacher_id') ?: null,
            'workload_hours' => $this->request->getPost('workload_hours') ?: null,
            'semester_id' => $semesterId ?: null,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];
        
        $id = $this->request->getPost('id');
        
        if ($id) {
            $this->classDisciplineModel->update($id, $data);
            $message = 'Atribuição atualizada com sucesso';
        } else {
            $this->classDisciplineModel->insert($data);
            $message = 'Disciplina atribuída à turma com sucesso';
        }
        
        return redirect()->to('/admin/classes/class-subjects?class=' . $classId)
            ->with('success', $message);
    }
    
    /**
     * Get assignments by class (AJAX)
     */
    public function getByClass($classId)
    {
        $assignments = $this->classDisciplineModel
            ->select('
                tbl_class_disciplines.*,
                tbl_disciplines.discipline_name,
                tbl_disciplines.discipline_code,
                tbl_users.first_name as teacher_first_name,
                tbl_users.last_name as teacher_last_name
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
            ->join('tbl_users', 'tbl_users.id = tbl_class_disciplines.teacher_id', 'left')
            ->where('tbl_class_disciplines.class_id', $classId)
            ->where('tbl_class_disciplines.is_active', 1)
            ->orderBy('tbl_disciplines.discipline_name', 'ASC')
            ->findAll();
        
        return $this->response->setJSON($assignments);
    }
    
    /**
     * Delete assignment
     */
    public function delete($id)
    {
        $assignment = $this->classDisciplineModel->find($id);
        
        if (!$assignment) {
            return redirect()->back()->with('error', 'Atribuição não encontrada');
        }
        
        $this->classDisciplineModel->delete($id);
        
        return redirect()->to('/admin/classes/class-subjects?class=' . $assignment->class_id)
            ->with('success', 'Disciplina removida da turma com sucesso');
    }

    /**
 * Página para atribuir professores às disciplinas da turma
 */
public function assignTeachers($classId)
{
    $data['title'] = 'Atribuir Professores à Turma';
    
    // Buscar informações da turma
    $classModel = new \App\Models\ClassModel();
    $data['class'] = $classModel
        ->select('tbl_classes.*, tbl_courses.course_name, tbl_grade_levels.level_name')
        ->join('tbl_courses', 'tbl_courses.id = tbl_classes.course_id', 'left')
        ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
        ->where('tbl_classes.id', $classId)
        ->first();
    
    if (!$data['class']) {
        return redirect()->to('/admin/classes/class-subjects')
            ->with('error', 'Turma não encontrada');
    }
    
    // Buscar disciplinas já atribuídas a esta turma (sugeridas)
    $data['disciplines'] = $this->classDisciplineModel
        ->select('
            tbl_class_disciplines.*,
            tbl_disciplines.discipline_name,
            tbl_disciplines.discipline_code
        ')
        ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
        ->where('tbl_class_disciplines.class_id', $classId)
        ->where('tbl_class_disciplines.is_active', 1)
        ->orderBy('tbl_disciplines.discipline_name', 'ASC')
        ->findAll();
    
    // Buscar professores disponíveis
    $userModel = new \App\Models\UserModel();
    $data['teachers'] = $userModel
        ->where('user_type', 'teacher')
        ->where('is_active', 1)
        ->orderBy('first_name', 'ASC')
        ->findAll();
    
    return view('admin/classes/class-subjects/assign_teachers', $data);
}

/**
 * Salvar atribuições de professores
 */
public function saveTeachers()
{
    $classId = $this->request->getPost('class_id');
    $assignments = $this->request->getPost('assignments'); // Array [discipline_id => teacher_id]
    
    if (!$classId || !$assignments) {
        return redirect()->back()->with('error', 'Dados inválidos');
    }
    
    $updated = 0;
    
    foreach ($assignments as $disciplineId => $teacherId) {
        // Atualizar apenas se teacher_id foi selecionado
        if (!empty($teacherId)) {
            $this->classDisciplineModel
                ->where('class_id', $classId)
                ->where('discipline_id', $disciplineId)
                ->set(['teacher_id' => $teacherId])
                ->update();
            $updated++;
        }
    }
    
    return redirect()->to('/admin/classes/class-subjects?class=' . $classId)
        ->with('success', "{$updated} professores atribuídos com sucesso!");
}

    /**
 * Get grade levels by course (AJAX)
 */
public function getLevelsByCourse($courseId = null)
{
    $gradeLevelModel = new \App\Models\GradeLevelModel();
    
    if ($courseId && $courseId != 0) {
        // Para um curso específico, buscar níveis dentro do range do curso
        $courseModel = new \App\Models\CourseModel();
        $course = $courseModel->find($courseId);
        
        if ($course) {
            $levels = $gradeLevelModel
                ->where('id >=', $course->start_grade_id)
                ->where('id <=', $course->end_grade_id)
                ->where('is_active', 1)
                ->orderBy('sort_order', 'ASC')
                ->findAll();
        } else {
            $levels = [];
        }
    } else {
        // Para Ensino Geral (todos os níveis)
        $levels = $gradeLevelModel
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->findAll();
    }
    
    return $this->response->setJSON($levels);
}

/**
 * Get classes by course and level (AJAX)
 */
public function getClassesByCourseAndLevel($courseId = null, $levelId = null)
{
    if (!$levelId) {
        return $this->response->setJSON([]);
    }
    
    $builder = $this->classModel
        ->select('tbl_classes.*, tbl_academic_years.year_name')
        ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_classes.academic_year_id')
        ->where('tbl_classes.is_active', 1)
        ->where('tbl_classes.grade_level_id', $levelId);
    
    if ($courseId && $courseId != 0) {
        $builder->where('tbl_classes.course_id', $courseId);
    } else {
        $builder->where('tbl_classes.course_id IS NULL');
    }
    
    $classes = $builder
        ->orderBy('tbl_academic_years.start_date', 'DESC')
        ->orderBy('tbl_classes.class_name', 'ASC')
        ->findAll();
    
    return $this->response->setJSON($classes);
}

/**
 * Get available disciplines for class (based on course and level)
 */
public function getAvailableDisciplinesForClass($classId)
{
    // Buscar informações da turma
    $class = $this->classModel
        ->select('tbl_classes.*, tbl_courses.id as course_id')
        ->join('tbl_courses', 'tbl_courses.id = tbl_classes.course_id', 'left')
        ->where('tbl_classes.id', $classId)
        ->first();
    
    if (!$class) {
        return $this->response->setJSON([]);
    }
    
    // Disciplinas já atribuídas a esta turma
    $assigned = $this->classDisciplineModel
        ->where('class_id', $classId)
        ->findAll();
    
    $assignedIds = array_column($assigned, 'discipline_id');
    
    // Se a turma tem curso, buscar disciplinas do currículo
    if ($class->course_id) {
        $courseDisciplineModel = new \App\Models\CourseDisciplineModel();
        
        $disciplines = $courseDisciplineModel
            ->select('
                tbl_disciplines.*,
                tbl_course_disciplines.workload_hours as suggested_workload,
                tbl_course_disciplines.semester as suggested_semester,
                tbl_course_disciplines.is_mandatory
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_course_disciplines.discipline_id')
            ->where('tbl_course_disciplines.course_id', $class->course_id)
            ->where('tbl_course_disciplines.grade_level_id', $class->grade_level_id)
            ->where('tbl_disciplines.is_active', 1)
            ->orderBy('tbl_disciplines.discipline_name', 'ASC')
            ->findAll();
    } else {
        // Para Ensino Geral, mostrar todas as disciplinas ativas
        $disciplines = $this->disciplineModel
            ->where('is_active', 1)
            ->orderBy('discipline_name', 'ASC')
            ->findAll();
    }
    
    // Filtrar apenas disciplinas não atribuídas
    $available = array_filter($disciplines, function($d) use ($assignedIds) {
        return !in_array($d->id, $assignedIds);
    });
    
    return $this->response->setJSON(array_values($available));
}
/**
 * API: Buscar disciplinas da turma (TODAS as disciplinas do sistema)
 * 
 * @param int $classId ID da turma
 * @param int|null $semesterId ID do semestre (opcional)
 * @return JSON
 */
public function getClassDisciplinesWithAssignments($classId, $semesterId = null)
{
    // Validar se a turma existe
    $class = $this->classModel->find($classId);
    
    if (!$class) {
        return $this->response->setJSON([
            'error' => 'Turma não encontrada'
        ])->setStatusCode(404);
    }
    
    // Buscar TODAS as disciplinas ativas do sistema
    $allDisciplines = $this->disciplineModel
        ->where('is_active', 1)
        ->orderBy('discipline_name', 'ASC')
        ->findAll();
    
    // Buscar atribuições existentes para esta turma
    $builder = $this->classDisciplineModel
        ->select('
            tbl_class_disciplines.*,
            tbl_users.first_name as teacher_first_name,
            tbl_users.last_name as teacher_last_name
        ')
        ->join('tbl_users', 'tbl_users.id = tbl_class_disciplines.teacher_id', 'left')
        ->where('tbl_class_disciplines.class_id', $classId);
    
    // Filtrar por semestre se especificado
    if ($semesterId && $semesterId != '') {
        $builder->where('tbl_class_disciplines.semester_id', $semesterId);
    }
    
    $assignments = $builder->findAll();
    
    // Mapear atribuições por discipline_id
    $assignmentMap = [];
    foreach ($assignments as $ass) {
        $assignmentMap[$ass->discipline_id] = $ass;
    }
    
    // Construir resultado
    $result = [];
    foreach ($allDisciplines as $disc) {
        $assigned = isset($assignmentMap[$disc->id]);
        $assignment = $assigned ? $assignmentMap[$disc->id] : null;
        
        $result[] = [
            'id' => $disc->id,
            'name' => $disc->discipline_name,
            'code' => $disc->discipline_code,
            'assigned' => $assigned,
            'assignment_id' => $assignment ? $assignment->id : null,
            'teacher_id' => $assignment ? $assignment->teacher_id : null,
            'teacher_name' => $assignment ? ($assignment->teacher_first_name . ' ' . $assignment->teacher_last_name) : null,
            'workload' => $assignment ? $assignment->workload_hours : null,
            'suggested_workload' => null,
            'is_mandatory' => false,
            'is_active' => $assignment ? $assignment->is_active : false
        ];
    }
    
    return $this->response->setJSON($result);
}

}