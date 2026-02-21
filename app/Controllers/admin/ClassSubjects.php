<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\ClassDisciplineModel;
use App\Models\ClassModel;
use App\Models\DisciplineModel;
use App\Models\UserModel;
use App\Models\SemesterModel;
use App\Models\AcademicYearModel;
use App\Models\CourseDisciplineModel;

class ClassSubjects extends BaseController
{
    protected $classDisciplineModel;
    protected $classModel;
    protected $disciplineModel;
    protected $userModel;
    protected $semesterModel;
    protected $academicYearModel;
    protected $courseDisciplineModel;
    
    public function __construct()
    {
        $this->classDisciplineModel = new ClassDisciplineModel();
        $this->classModel = new ClassModel();
        $this->disciplineModel = new DisciplineModel();
        $this->userModel = new UserModel();
        $this->semesterModel = new SemesterModel();
        $this->academicYearModel = new AcademicYearModel();
        $this->courseDisciplineModel = new CourseDisciplineModel(); 
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
 * Assignment form - Carrega automaticamente as disciplinas da turma
 */
public function assign()
{
    $data['title'] = 'Atribuir Disciplinas à Turma';
    
    $assignmentId = $this->request->getGet('edit');
    $classId = $this->request->getGet('class');
    
    // Inicializar variáveis
    $data['assignment'] = null;
    $data['selectedDisciplineId'] = null;
    $data['selectedClassId'] = $classId;
    $data['selectedClassInfo'] = null;
    $data['disciplines'] = []; // Array para armazenar as disciplinas combinadas
    
    // Se temos um ID de atribuição (modo edição de uma específica)
    if ($assignmentId) {
        $data['assignment'] = $this->classDisciplineModel
            ->select('
                tbl_class_disciplines.*, 
                tbl_classes.class_name, 
                tbl_classes.class_code,
                tbl_classes.class_shift,
                tbl_classes.grade_level_id,
                tbl_classes.course_id,
                tbl_classes.academic_year_id,
                tbl_classes.id as class_id,
                tbl_disciplines.id as discipline_id,
                tbl_disciplines.discipline_name,
                tbl_disciplines.discipline_code,
                tbl_semesters.semester_name,
                tbl_semesters.semester_type,
                CONCAT(tbl_users.first_name, " ", tbl_users.last_name) as teacher_name
            ')
            ->join('tbl_classes', 'tbl_classes.id = tbl_class_disciplines.class_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
            ->join('tbl_semesters', 'tbl_semesters.id = tbl_class_disciplines.semester_id', 'left')
            ->join('tbl_users', 'tbl_users.id = tbl_class_disciplines.teacher_id', 'left')
            ->where('tbl_class_disciplines.id', $assignmentId)
            ->first();
        
        if ($data['assignment']) {
            $data['selectedDisciplineId'] = $data['assignment']->discipline_id;
            $data['selectedClassId'] = $data['assignment']->class_id;
            $classId = $data['assignment']->class_id;
        }
    }
    
    // Se temos uma turma selecionada (via GET)
    if ($classId) {
        // Buscar informações completas da turma
        $data['selectedClassInfo'] = $this->classModel
            ->select('tbl_classes.*, tbl_courses.course_name, tbl_grade_levels.level_name, tbl_academic_years.year_name')
            ->join('tbl_courses', 'tbl_courses.id = tbl_classes.course_id', 'left')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_classes.academic_year_id')
            ->where('tbl_classes.id', $classId)
            ->first();
        
        if ($data['selectedClassInfo']) {
            // Buscar TODAS as disciplinas do currículo do curso
            $courseDisciplines = [];
            
            if ($data['selectedClassInfo']->course_id) {
                // Tem curso definido - buscar disciplinas do currículo
                $courseDisciplines = $this->courseDisciplineModel
                    ->select('
                        tbl_disciplines.id,
                        tbl_disciplines.discipline_name,
                        tbl_disciplines.discipline_code,
                        tbl_course_disciplines.workload_hours as suggested_workload,
                        tbl_course_disciplines.semester as suggested_semester,
                        tbl_course_disciplines.is_mandatory
                    ')
                    ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_course_disciplines.discipline_id')
                    ->where('tbl_course_disciplines.course_id', $data['selectedClassInfo']->course_id)
                    ->where('tbl_course_disciplines.grade_level_id', $data['selectedClassInfo']->grade_level_id)
                    ->where('tbl_disciplines.is_active', 1)
                    ->orderBy('tbl_disciplines.discipline_name', 'ASC')
                    ->findAll();
            } else {
                // Ensino Geral - buscar todas as disciplinas ativas
                $courseDisciplines = $this->disciplineModel
                    ->select('
                        id,
                        discipline_name,
                        discipline_code,
                        workload_hours as suggested_workload,
                        "Anual" as suggested_semester,
                        1 as is_mandatory
                    ')
                    ->where('is_active', 1)
                    ->orderBy('discipline_name', 'ASC')
                    ->findAll();
            }
            
            // Buscar todos os semestres do ano letivo
            $allSemesters = $this->semesterModel
                ->where('academic_year_id', $data['selectedClassInfo']->academic_year_id)
                ->where('is_active', 1)
                ->orderBy('start_date', 'ASC')
                ->findAll();
            
            // Buscar atribuições existentes desta turma (TODOS os registros)
            $existingAssignments = $this->classDisciplineModel
                ->select('
                    tbl_class_disciplines.*,
                    tbl_users.first_name as teacher_first_name,
                    tbl_users.last_name as teacher_last_name,
                    tbl_semesters.semester_name,
                    tbl_semesters.semester_type
                ')
                ->join('tbl_users', 'tbl_users.id = tbl_class_disciplines.teacher_id', 'left')
                ->join('tbl_semesters', 'tbl_semesters.id = tbl_class_disciplines.semester_id', 'left')
                ->where('tbl_class_disciplines.class_id', $classId)
                ->where('tbl_class_disciplines.is_active', 1)
                ->findAll();
            
            // Mapear atribuições por chave composta: discipline_id + semester_id
            $assignmentMap = [];
            foreach ($existingAssignments as $ass) {
                $key = $ass->discipline_id . '_' . ($ass->semester_id ?? 'null');
                $assignmentMap[$key] = $ass;
            }
            
            // Combinar disciplinas do currículo com as atribuições existentes
            // Para disciplinas anuais, criar registros separados por semestre
            $data['disciplines'] = [];
            
            foreach ($courseDisciplines as $disc) {
                if ($disc->suggested_semester == 'Anual') {
                    // Disciplina anual - criar um registro para cada semestre
                    foreach ($allSemesters as $semester) {
                        $key = $disc->id . '_' . $semester->id;
                        $assignment = $assignmentMap[$key] ?? null;
                        $assigned = $assignment !== null;
                        
                        $data['disciplines'][] = [
                            'id' => $disc->id,
                            'name' => $disc->discipline_name,
                            'code' => $disc->discipline_code,
                            'assigned' => $assigned,
                            'assignment_id' => $assignment ? $assignment->id : null,
                            'teacher_id' => $assignment ? $assignment->teacher_id : null,
                            'teacher_name' => $assignment ? ($assignment->teacher_first_name . ' ' . $assignment->teacher_last_name) : null,
                            'workload' => $assignment ? $assignment->workload_hours : null,
                            'suggested_workload' => $disc->suggested_workload ?? null,
                            'suggested_semester' => $disc->suggested_semester ?? 'Anual',
                            'semester_id' => $assignment ? $assignment->semester_id : null,
                            'semester_name' => $assignment ? $assignment->semester_name : $semester->semester_name,
                            'semester_type' => $assignment ? $assignment->semester_type : $semester->semester_type,
                            'is_mandatory' => $disc->is_mandatory ?? false,
                            'is_active' => $assignment ? $assignment->is_active : false,
                            'display_semester' => $semester->semester_name // Para exibição
                        ];
                    }
                } else {
                    // Disciplina semestral - apenas um registro
                    $key = $disc->id . '_null';
                    $assignment = $assignmentMap[$key] ?? null;
                    $assigned = $assignment !== null;
                    
                    $data['disciplines'][] = [
                        'id' => $disc->id,
                        'name' => $disc->discipline_name,
                        'code' => $disc->discipline_code,
                        'assigned' => $assigned,
                        'assignment_id' => $assignment ? $assignment->id : null,
                        'teacher_id' => $assignment ? $assignment->teacher_id : null,
                        'teacher_name' => $assignment ? ($assignment->teacher_first_name . ' ' . $assignment->teacher_last_name) : null,
                        'workload' => $assignment ? $assignment->workload_hours : null,
                        'suggested_workload' => $disc->suggested_workload ?? null,
                        'suggested_semester' => $disc->suggested_semester ?? 'Anual',
                        'semester_id' => $assignment ? $assignment->semester_id : null,
                        'semester_name' => $assignment ? $assignment->semester_name : null,
                        'semester_type' => $assignment ? $assignment->semester_type : null,
                        'is_mandatory' => $disc->is_mandatory ?? false,
                        'is_active' => $assignment ? $assignment->is_active : false,
                        'display_semester' => $disc->suggested_semester // Para exibição
                    ];
                }
            }
            
            // Buscar semestres do ano letivo da turma para o filtro
            $data['semesters'] = $allSemesters;
        }
    } else {
        // Se não tem turma selecionada, buscar semestres do ano atual como fallback
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
    }
    
    // Buscar todas as turmas ativas para o select
    $data['classes'] = $this->classModel
        ->select('tbl_classes.*, tbl_academic_years.year_name, tbl_grade_levels.level_name, tbl_courses.course_name')
        ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_classes.academic_year_id')
        ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
        ->join('tbl_courses', 'tbl_courses.id = tbl_classes.course_id', 'left')
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
    
    return view('admin/classes/class-subjects/assign', $data);
}
/**
 * Save assignment (individual)
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
    $assignmentId = $this->request->getPost('id');
    $isActive = $this->request->getPost('is_active') ? 1 : 0;
    
    // Buscar disciplina para verificar se é anual
    $discipline = $this->disciplineModel->find($disciplineId);
    
    if (!$discipline) {
        return redirect()->back()->withInput()
            ->with('error', 'Disciplina não encontrada.');
    }
    
    // Verificar se a disciplina é anual
    $isAnnual = false;
    if (property_exists($discipline, 'suggested_semester')) {
        $isAnnual = ($discipline->suggested_semester ?? '') == 'Anual';
    } elseif (isset($discipline->suggested_semester)) {
        $isAnnual = $discipline->suggested_semester == 'Anual';
    }
    
    // VALIDAÇÃO: Se a disciplina está ativa (marcada) e é anual, precisa de semestre
    if ($isActive && $isAnnual && empty($semesterId)) {
        return redirect()->back()->withInput()
            ->with('error', 'Disciplinas anuais precisam ter um semestre definido. Selecione o período para esta disciplina.');
    }
    
    // Se não está ativa (desmarcada), ignorar validações adicionais
    if (!$isActive) {
        // Se tem ID, apenas desativa
        if ($assignmentId) {
            $this->classDisciplineModel->update($assignmentId, ['is_active' => 0]);
            return redirect()->to('/admin/classes/class-subjects?class=' . $classId)
                ->with('success', 'Atribuição removida com sucesso');
        }
        
        // Se não tem ID e não está ativo, não faz nada
        return redirect()->to('/admin/classes/class-subjects?class=' . $classId)
            ->with('info', 'Nenhuma alteração realizada.');
    }
    
    // Verificar se já existe uma atribuição ativa para esta combinação
    $existing = $this->classDisciplineModel
        ->where('class_id', $classId)
        ->where('discipline_id', $disciplineId)
        ->where('is_active', 1);
    
    // Se tem semestre, incluir na verificação
    if ($semesterId) {
        $existing->where('semester_id', $semesterId);
    } else {
        $existing->where('semester_id', null);
    }
    
    $existingRecord = $existing->first();
    
    // Se encontrou um registro existente e não é o mesmo que estamos editando
    if ($existingRecord && $existingRecord->id != $assignmentId) {
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
        'is_active' => 1
    ];
    
    if ($assignmentId) {
        // Atualizar existente
        if ($this->classDisciplineModel->update($assignmentId, $data)) {
            $message = 'Atribuição atualizada com sucesso';
        } else {
            return redirect()->back()->withInput()
                ->with('error', 'Erro ao atualizar atribuição.');
        }
    } else {
        // Inserir novo
        if ($this->classDisciplineModel->insert($data)) {
            $message = 'Disciplina atribuída à turma com sucesso';
        } else {
            return redirect()->back()->withInput()
                ->with('error', 'Erro ao atribuir disciplina.');
        }
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
    
    // Buscar informações da turma (com ano letivo)
    $classModel = new \App\Models\ClassModel();
    $data['class'] = $classModel
        ->select('
            tbl_classes.*, 
            tbl_courses.course_name, 
            tbl_grade_levels.level_name,
            tbl_academic_years.year_name
        ')
        ->join('tbl_courses', 'tbl_courses.id = tbl_classes.course_id', 'left')
        ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
        ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_classes.academic_year_id')
        ->where('tbl_classes.id', $classId)
        ->first();
    
    if (!$data['class']) {
        return redirect()->to('/admin/classes/class-subjects')
            ->with('error', 'Turma não encontrada');
    }
    
    // Buscar disciplinas já atribuídas a esta turma COM INFORMAÇÕES DO SEMESTRE
    // NOTA: is_mandatory não está na tabela tbl_disciplines, então foi removido
    $data['disciplines'] = $this->classDisciplineModel
        ->select('
            tbl_class_disciplines.*,
            tbl_disciplines.discipline_name,
            tbl_disciplines.discipline_code,
            tbl_semesters.semester_name,
            tbl_semesters.semester_type,
            tbl_semesters.start_date as semester_start,
            tbl_semesters.end_date as semester_end
        ')
        ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
        ->join('tbl_semesters', 'tbl_semesters.id = tbl_class_disciplines.semester_id', 'left')
        ->where('tbl_class_disciplines.class_id', $classId)
        ->where('tbl_class_disciplines.is_active', 1)
        ->orderBy('tbl_semesters.start_date', 'ASC')
        ->orderBy('tbl_disciplines.discipline_name', 'ASC')
        ->findAll();
    
    // DEBUG: Log para verificar se os dados do semestre estão vindo
    log_message('debug', 'Disciplinas encontradas: ' . count($data['disciplines']));
    foreach ($data['disciplines'] as $d) {
        log_message('debug', "Disciplina: {$d->discipline_name}, Semester ID: {$d->semester_id}, Semester Name: {$d->semester_name}");
    }
    
    // Buscar todos os semestres do ano letivo para referência
    $data['all_semesters'] = $this->semesterModel
        ->where('academic_year_id', $data['class']->academic_year_id)
        ->where('is_active', 1)
        ->orderBy('start_date', 'ASC')
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
   /*  public function saveTeachers()
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
    } */
/**
 * Salvar atribuições de professores
 */
public function saveTeachers()
{
    $classId = $this->request->getPost('class_id');
    $assignments = $this->request->getPost('assignments'); // Array [class_discipline_id => teacher_id]
    
    if (!$classId || !$assignments) {
        return redirect()->back()->with('error', 'Dados inválidos');
    }
    
    $updated = 0;
    
    foreach ($assignments as $classDisciplineId => $teacherId) {
        // Atualizar apenas se teacher_id foi selecionado
        if (!empty($teacherId)) {
            $this->classDisciplineModel
                ->where('id', $classDisciplineId)  // Usar ID da tabela class_disciplines
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
public function saveBulkAssignments()
{
    // Validação do request
    if (!$this->validate([
        'class_id' => 'required|is_natural_no_zero'
    ])) {
        return redirect()->back()->withInput()->with('error', 'Dados inválidos.');
    }
    
    $classId = $this->request->getPost('class_id');
    $assignments = $this->request->getPost('assignments');
    
    if (empty($assignments)) {
        return redirect()->back()->with('warning', 'Nenhuma disciplina selecionada.');
    }
    
    // Buscar todas as disciplinas de uma vez
    $disciplineIds = [];
    foreach ($assignments as $data) {
        if (isset($data['selected']) && $data['selected'] == 1 && !empty($data['discipline_id'])) {
            $disciplineIds[] = $data['discipline_id'];
        }
    }
    
    $disciplines = [];
    if (!empty($disciplineIds)) {
        $disciplinesData = $this->disciplineModel
            ->whereIn('id', array_unique($disciplineIds))
            ->findAll();
        
        foreach ($disciplinesData as $disc) {
            $disciplines[$disc->id] = $disc;
        }
    }
    
    // Iniciar transação
    $this->classDisciplineModel->db->transStart();
    
    try {
        $saved = 0;
        $errors = 0;
        $missingSemester = [];
        $keptAssignmentIds = [];
        $processedKeys = []; // Para evitar duplicatas
        
        foreach ($assignments as $key => $data) {
            // Verificar se a disciplina foi selecionada
            if (!isset($data['selected']) || $data['selected'] != 1) {
                continue;
            }
            
            $disciplineId = $data['discipline_id'] ?? 0;
            $assignmentId = $data['assignment_id'] ?? null;
            $teacherId = !empty($data['teacher_id']) ? $data['teacher_id'] : null;
            $workload = !empty($data['workload']) ? $data['workload'] : null;
            $semesterId = !empty($data['semester_id']) ? $data['semester_id'] : null;
            
            if (!$disciplineId) {
                $errors++;
                continue;
            }
            
            // Criar chave única para evitar processar mesma disciplina múltiplas vezes
            $uniqueKey = $disciplineId . '_' . ($semesterId ?? 'null');
            if (in_array($uniqueKey, $processedKeys)) {
                continue;
            }
            $processedKeys[] = $uniqueKey;
            
            // Verificar se a disciplina é anual e requer semestre
            $discipline = $disciplines[$disciplineId] ?? null;
            
            if ($discipline) {
                $isAnnual = false;
                if (property_exists($discipline, 'suggested_semester')) {
                    $isAnnual = ($discipline->suggested_semester ?? '') == 'Anual';
                }
                
                // Se for anual e não tem semestre definido, registrar erro
                if ($isAnnual && empty($semesterId)) {
                    $missingSemester[] = $discipline->discipline_name ?? "Disciplina ID: $disciplineId";
                    $errors++;
                    continue;
                }
            }
            
            // Verificar se já existe
            $existingQuery = $this->classDisciplineModel
                ->where('class_id', $classId)
                ->where('discipline_id', $disciplineId)
                ->where('is_active', 1);
            
            if ($semesterId) {
                $existingQuery->where('semester_id', $semesterId);
            } else {
                $existingQuery->where('semester_id', null);
            }
            
            $existingAssignment = $existingQuery->first();
            
            if ($existingAssignment && !$assignmentId) {
                // Já existe, manter
                $keptAssignmentIds[] = $existingAssignment->id;
                $saved++;
                continue;
            }
            
            $assignmentData = [
                'class_id' => $classId,
                'discipline_id' => $disciplineId,
                'teacher_id' => $teacherId,
                'workload_hours' => $workload,
                'semester_id' => $semesterId,
                'is_active' => 1
            ];
            
            if ($assignmentId) {
                // Atualizar existente
                $this->classDisciplineModel->update($assignmentId, $assignmentData);
                $keptAssignmentIds[] = $assignmentId;
                $saved++;
            } else {
                // Inserir novo
                $newId = $this->classDisciplineModel->insert($assignmentData);
                if ($newId) {
                    $keptAssignmentIds[] = $newId;
                    $saved++;
                } else {
                    $errors++;
                }
            }
        }
        
        // Desativar assignments que não foram mantidos
        if (!empty($keptAssignmentIds)) {
            $this->classDisciplineModel
                ->where('class_id', $classId)
                ->whereNotIn('id', $keptAssignmentIds)
                ->set(['is_active' => 0])
                ->update();
        } else {
            // Se não manteve nenhum, desativa todos
            $this->classDisciplineModel
                ->where('class_id', $classId)
                ->set(['is_active' => 0])
                ->update();
        }
        
        $this->classDisciplineModel->db->transComplete();
        
        if ($this->classDisciplineModel->db->transStatus() === false) {
            throw new \Exception('Erro na transação');
        }
        
        // Verificar se realmente salvou alguma coisa
        if ($saved === 0) {
            if (!empty($missingSemester)) {
                $message = "Nenhuma disciplina foi atribuída. ";
                $message .= "As seguintes disciplinas anuais precisam de período: <br>";
                $message .= "<ul class='mb-0 mt-1'>";
                foreach ($missingSemester as $disc) {
                    $message .= "<li><strong>$disc</strong></li>";
                }
                $message .= "</ul>";
                
                return redirect()->to('admin/classes/class-subjects/assign?class=' . $classId)
                    ->with('error', $message);
            }
            
            return redirect()->to('admin/classes/class-subjects/assign?class=' . $classId)
                ->with('warning', 'Nenhuma disciplina foi salva. Verifique os dados.');
        }
        
        // Se houver disciplinas anuais sem semestre, mostrar aviso parcial
        if (!empty($missingSemester)) {
            $message = "$saved disciplinas atribuídas com sucesso. ";
            $message .= "As seguintes disciplinas anuais NÃO foram atribuídas (período não definido): <br>";
            $message .= "<ul class='mb-0 mt-1'>";
            foreach ($missingSemester as $disc) {
                $message .= "<li><strong>$disc</strong></li>";
            }
            $message .= "</ul>";
            
            return redirect()->to('admin/classes/class-subjects/assign?class=' . $classId)
                ->with('warning', $message);
        }
        
        return redirect()->to('admin/classes/class-subjects/assign?class=' . $classId)
            ->with('success', "$saved disciplinas atribuídas com sucesso.");
            
    } catch (\Exception $e) {
        $this->classDisciplineModel->db->transRollback();
        log_message('error', 'Erro ao salvar atribuições: ' . $e->getMessage());
        
        return redirect()->back()
            ->withInput()
            ->with('error', 'Erro ao salvar atribuições: ' . $e->getMessage());
    }
}

}