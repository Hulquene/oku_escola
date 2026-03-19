<?php
// app/Controllers/admin/Classes.php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\ClassModel;
use App\Models\GradeLevelModel;
use App\Models\AcademicYearModel;
use App\Models\UserModel;
use App\Models\EnrollmentModel;
use App\Models\CourseModel;

class Classes extends BaseController
{
    protected $classModel;
    protected $gradeLevelModel;
    protected $academicYearModel;
    protected $userModel;
    protected $enrollmentModel;
    protected $courseModel;
    
    public function __construct()
    {
        $this->classModel = new ClassModel();
        $this->gradeLevelModel = new GradeLevelModel();
        $this->academicYearModel = new AcademicYearModel();
        $this->userModel = new UserModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->courseModel = new CourseModel();
        
        helper(['form', 'url']);
    }
    
    /**
     * List classes - método único
     */
    public function index()
    {
        // Se for requisição AJAX, retorna JSON (GET ou POST)
        if ($this->request->isAJAX()) {
            return $this->getDataTablesData();
        }
        
        // Se não for AJAX, carrega a view
        return $this->loadIndexView();
    }
    
    /**
     * Carrega a view com os filtros
     */
    private function loadIndexView()
    {
        $data['title'] = 'Turmas';
        
        // Dados para os filtros
        $data['academicYears'] = $this->academicYearModel->findAll();
        $data['gradeLevels'] = $this->gradeLevelModel->getActive();
        $data['courses'] = $this->courseModel->getHighSchoolCourses();
        
        // Valores selecionados nos filtros (via GET)
        $data['selectedYear'] = $this->request->getGet('academic_year');
        $data['selectedLevel'] = $this->request->getGet('grade_level');
        $data['selectedCourse'] = $this->request->getGet('course');
        $data['selectedShift'] = $this->request->getGet('shift');
        $data['selectedStatus'] = $this->request->getGet('status');
        
        // Professores para o filtro
        $data['teachers'] = $this->getTeachers();
        
        $data['selectedTeacher'] = $this->request->getGet('teacher');
        
        return view('admin/classes/classes/index', $data);
    }
    
    /**
     * NOVO MÉTODO: Processa a requisição AJAX do DataTables via POST
     */
    public function getTableData()
    {
        try {
            $request = service('request');
            
            // Parâmetros do DataTables - via POST
            $draw = (int)($request->getPost('draw') ?? 0);
            $start = (int)($request->getPost('start') ?? 0);
            $length = (int)($request->getPost('length') ?? 25);
            
            // TRATAMENTO CORRETO DO SEARCH
            $search = $request->getPost('search');
            $searchValue = is_array($search) ? ($search['value'] ?? '') : '';
            
            // TRATAMENTO CORRETO DO ORDER
            $order = $request->getPost('order');
            $orderColumnIndex = 1; // padrão
            $orderDir = 'asc'; // padrão
            
            if (is_array($order) && isset($order[0])) {
                $orderColumnIndex = (int)($order[0]['column'] ?? 1);
                $orderDir = $order[0]['dir'] ?? 'asc';
            }
            
            // Filtros adicionais (via POST)
            $academicYearId = $request->getPost('academic_year');
            $gradeLevelId = $request->getPost('grade_level');
            $courseId = $request->getPost('course');
            $shift = $request->getPost('shift');
            $status = $request->getPost('status');
            $teacherId = $request->getPost('teacher');
            
            // Mapeamento de colunas
            $columns = [
                0 => 'tbl_classes.id',
                1 => 'tbl_classes.class_name',
                2 => 'tbl_grade_levels.level_name',
                3 => 'tbl_courses.course_name',
                4 => 'tbl_academic_years.year_name',
                5 => 'tbl_classes.class_shift',
                6 => 'tbl_classes.class_room',
                7 => 'tbl_classes.capacity',
                8 => 'enrolled_count',
                9 => 'teacher_name',
                10 => 'tbl_classes.is_active',
            ];
            
            $orderColumn = $columns[$orderColumnIndex] ?? 'tbl_classes.class_name';
            
            // Subconsulta para contar alunos matriculados
            $enrollmentSubquery = '(SELECT COUNT(*) FROM tbl_enrollments WHERE class_id = tbl_classes.id AND status = "Ativo") as enrolled_count';
            
            $builder = $this->classModel
                ->select('
                    tbl_classes.*, 
                    tbl_grade_levels.level_name, 
                    tbl_grade_levels.education_level,
                    tbl_academic_years.year_name,
                    tbl_academic_years.start_date,
                    tbl_users.first_name as teacher_first_name, 
                    tbl_users.last_name as teacher_last_name,
                    tbl_courses.course_name,
                    tbl_courses.course_code,
                    tbl_courses.course_type,
                    CONCAT(tbl_users.first_name, " ", tbl_users.last_name) as teacher_name,
                    ' . $enrollmentSubquery . '
                ')
                ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
                ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_classes.academic_year_id')
                ->join('tbl_users', 'tbl_users.id = tbl_classes.class_teacher_id', 'left')
                ->join('tbl_courses', 'tbl_courses.id = tbl_classes.course_id', 'left');
            
            // Aplicar filtros
            if (!empty($academicYearId)) {
                $builder->where('tbl_classes.academic_year_id', $academicYearId);
            }
            
            if (!empty($gradeLevelId)) {
                $builder->where('tbl_classes.grade_level_id', $gradeLevelId);
            }
            
            if (isset($courseId) && $courseId !== '') {
                if ($courseId === '0') {
                    $builder->where('tbl_classes.course_id', null);
                } else {
                    $builder->where('tbl_classes.course_id', $courseId);
                }
            }
            
            if (!empty($shift)) {
                $builder->where('tbl_classes.class_shift', $shift);
            }
            
            if (!empty($teacherId)) {
                $builder->where('tbl_classes.class_teacher_id', $teacherId);
            }
            
            if ($status == 'active') {
                $builder->where('tbl_classes.is_active', 1);
            } elseif ($status == 'inactive') {
                $builder->where('tbl_classes.is_active', 0);
            }
            
            // Aplicar busca global
            if (!empty($searchValue)) {
                $builder->groupStart()
                    ->like('tbl_classes.class_name', $searchValue)
                    ->orLike('tbl_classes.class_code', $searchValue)
                    ->orLike('tbl_grade_levels.level_name', $searchValue)
                    ->orLike('tbl_courses.course_name', $searchValue)
                    ->orLike('tbl_academic_years.year_name', $searchValue)
                    ->orLike('tbl_classes.class_shift', $searchValue)
                    ->orLike('tbl_classes.class_room', $searchValue)
                    ->orLike('CONCAT(tbl_users.first_name, " ", tbl_users.last_name)', $searchValue)
                    ->groupEnd();
            }
            
            // Contar total de registros filtrados
            $recordsFiltered = $builder->countAllResults(false);
            
            // Aplicar ordenação e paginação
            $builder->orderBy($orderColumn, $orderDir);
            
            $data = $builder->limit($length, $start)
                ->get()
                ->getResult();
            
            // Contar total de registros sem filtros
            $totalRecords = $this->classModel->countAll();
            
            return $this->response->setJSON([
                'draw' => $draw,
                'recordsTotal' => $totalRecords,
                'recordsFiltered' => $recordsFiltered,
                'data' => $data
            ]);
            
        } catch (\Exception $e) {
            // Log do erro para debug
            log_message('error', 'Erro no DataTables: ' . $e->getMessage());
            log_message('error', 'Linha: ' . $e->getLine());
            log_message('error', 'Arquivo: ' . $e->getFile());
            
            // Retornar erro amigável
            return $this->response->setStatusCode(500)->setJSON([
                'error' => 'Erro interno: ' . $e->getMessage(),
                'draw' => (int)($request->getPost('draw') ?? 0),
                'recordsTotal' => 0,
                'recordsFiltered' => 0,
                'data' => []
            ]);
        }
    }
    
    /**
     * Mantido para compatibilidade com GET requests (opcional)
     */
    private function getDataTablesData()
    {
        return $this->getTableData();
    }
    
    /**
     * Helper para buscar professores
     */
    private function getTeachers()
    {
        return $this->userModel
            ->select('tbl_users.id, tbl_users.first_name, tbl_users.last_name')
            ->join('tbl_roles', 'tbl_roles.id = tbl_users.role_id')
            ->where('tbl_roles.role_name', 'Professor')
            ->where('tbl_users.is_active', 1)
            ->orderBy('tbl_users.first_name', 'asc')
            ->findAll();
    }
    /**
     * Class form - Atualizado para incluir cursos
     */
    public function form($id = null)
    {
        $data['title'] = $id ? 'Editar Turma' : 'Nova Turma';
        $data['class'] = $id ? $this->classModel->find($id) : null;
        
        $data['gradeLevels'] = $this->gradeLevelModel->getActive();
        $data['academicYears'] = $this->academicYearModel->getActive();
        $data['teachers'] = $this->userModel->getByType('teacher');
        $data['courses'] = $this->courseModel->getHighSchoolCourses(); // <-- NOVO
        
        return view('admin/classes/classes/form', $data);
    }

    /**
     * Save class - Atualizado para incluir course_id
     */
    /**
     * Save class - COM SUGESTÃO AUTOMÁTICA DE DISCIPLINAS
     */
    public function save()
    {
        $id = $this->request->getPost('id');
        
        // Preparar dados
        $data = [
            'class_name' => $this->request->getPost('class_name'),
            'class_code' => $this->request->getPost('class_code'),
            'grade_level_id' => $this->request->getPost('grade_level_id'),
            'course_id' => $this->request->getPost('course_id') ?: null, // NOVO
            'academic_year_id' => $this->request->getPost('academic_year_id'),
            'class_shift' => $this->request->getPost('class_shift'),
            'class_room' => $this->request->getPost('class_room'),
            'capacity' => $this->request->getPost('capacity') ?: null,
            'class_teacher_id' => $this->request->getPost('class_teacher_id') ?: null,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];
        
        // Se for atualização, incluir o ID
        if ($id) {
            $data['id'] = $id;
        }
        
        // Validações existentes...
        $existingClass = $this->classModel
            ->where('class_name', $data['class_name'])
            ->where('academic_year_id', $data['academic_year_id'])
            ->where('id !=', $id ?: 0)
            ->first();
        
        if ($existingClass) {
            return redirect()->back()->withInput()
                ->with('error', 'Já existe uma turma com este nome no mesmo ano letivo.');
        }
        
        $existingCode = $this->classModel
            ->where('class_code', $data['class_code'])
            ->where('id !=', $id ?: 0)
            ->first();
        
        if ($existingCode) {
            return redirect()->back()->withInput()
                ->with('error', 'Este código de turma já está em uso.');
        }
        
        // Salvar turma
        if ($this->classModel->save($data)) {
            $classId = $id ?: $this->classModel->getInsertID();
            $action = $id ? 'atualizada' : 'criada';
            
            // --- NOVA LÓGICA: Sugerir disciplinas do currículo ---
            if (!$id){
                // Se for uma turma NOVA e tem curso definido
                if (!empty($data['course_id'])) {
                    $this->suggestDisciplinesFromCurriculum($classId, $data['course_id'], $data['grade_level_id']);
                }else {
                    // Se for turma nova sem curso, sugerir disciplinas do nível de ensino (Ensino Geral)
                    $this->suggestDisciplinesFromGradeLevel($classId, $data['grade_level_id']);
                }
            }
            // ----------------------------------------------------
            
            $message = "Turma '{$data['class_name']}' {$action} com sucesso!";
            
            // Redirecionar para página de alocação de professores se for turma nova com curso
            if (!$id && !empty($data['course_id'] || !empty($data['grade_level_id']))) {
                session()->setFlashdata('info', 'Sugestão: Atribua os professores às disciplinas sugeridas.');
                return redirect()->to('/admin/classes/class-subjects/assign-teachers/' . $classId)
                    ->with('success', $message);
            }
            
            return redirect()->to('/admin/classes/classes')
                ->with('success', $message);
        } else {
            $errors = $this->classModel->errors();
            if (!empty($errors)) {
                return redirect()->back()->withInput()
                    ->with('errors', $errors);
            }
            return redirect()->back()->withInput()
                ->with('error', 'Erro ao ' . ($id ? 'atualizar' : 'criar') . ' turma.');
        }
    }
    /**
     * Método auxiliar para sugerir disciplinas do currículo (VERSÃO CORRIGIDA)
     */
    private function suggestDisciplinesFromCurriculum($classId, $courseId, $gradeLevelId)
    {
   /*     var_dump($classId, $courseId, $gradeLevelId);die; */
        // Carregar models necessários
        $courseDisciplineModel = new \App\Models\CourseDisciplineModel();
        $classDisciplineModel = new \App\Models\ClassDisciplineModel();
        $semesterModel = new \App\Models\SemesterModel();
        
        // Buscar a turma para saber o ano letivo
        $class = $this->classModel->find($classId);
        if (!$class) {
            log_message('error', "Turma ID {$classId} não encontrada para sugerir disciplinas");
            return 0;
        }
        
        // Buscar TODOS os semestres do ano letivo da turma
        $semesters = $semesterModel
            ->where('academic_year_id', $class->academic_year_id)
            ->whereIn('status', ['ativo', 'processado'])  // ✅ CAMPO NOVO
            ->orderBy('start_date', 'ASC')
            ->findAll();
        
        if (empty($semesters)) {
            log_message('warning', "Nenhum semestre ativo encontrado para o ano letivo da turma ID {$classId}");
            return 0;
        }
        
        // Buscar disciplinas do currículo para este curso e nível
        $curriculumDisciplines = $courseDisciplineModel
            ->select('
                tbl_course_disciplines.*,
                tbl_disciplines.discipline_name
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_course_disciplines.discipline_id')
            ->where('tbl_course_disciplines.course_id', $courseId)
            ->where('tbl_course_disciplines.grade_level_id', $gradeLevelId)
            ->where('tbl_course_disciplines.is_mandatory', 1) // Só obrigatórias por padrão
            ->findAll();
        
        $suggestedCount = 0;
        $insertData = [];
        
        foreach ($curriculumDisciplines as $cd) {
            $semesterType = $cd->semester ?? 'Anual'; // Pega do currículo (Anual, 1, 2)
            
            // Converter valores do formulário para o ENUM correto
            $periodMap = [
                'Anual' => 'Anual',
                '1' => '1º Semestre',  // 1º Trimestre vira 1º Semestre
                '2' => '2º Semestre',  // 2º Trimestre vira 2º Semestre
                '3' => 'Anual'         // 3º Trimestre geralmente é Anual
            ];

            $periodType = $periodMap[$semesterType] ?? 'Anual';
             // Verificar se já existe para esta turma, disciplina e período
            $exists = $classDisciplineModel
                ->where('class_id', $classId)
                ->where('discipline_id', $cd->discipline_id)
                ->where('period_type', $periodType)
                ->first();
            
            if (!$exists) {
                $insertData[] = [
                    'class_id' => $classId,
                    'discipline_id' => $cd->discipline_id,
                    'workload_hours' => $cd->workload_hours,
                    'period_type' => $periodType,
                    'teacher_id' => null,
                    'is_active' => 1,
                    'created_at' => date('Y-m-d H:i:s')
                ];
                $suggestedCount++;
            }
        }
        
        // Inserir em lote para melhor performance
        if (!empty($insertData)) {
            $db = db_connect();
            $db->table('tbl_class_disciplines')->insertBatch($insertData);
            log_message('info', "Sugeridas {$suggestedCount} disciplinas do currículo para turma ID {$classId}");
        }
        
        return $suggestedCount;
    }
    /**
 * Sugerir disciplinas do nível de ensino (Ensino Geral - 1ª à 9ª classe)
 * 
 * @param int $classId ID da turma
 * @param int $gradeLevelId ID do nível de ensino
 * @return int Número de disciplinas sugeridas
 */
private function suggestDisciplinesFromGradeLevel($classId, $gradeLevelId)
{
    // Carregar models necessários
    $gradeDisciplineModel = new \App\Models\GradeDisciplineModel();
    $classDisciplineModel = new \App\Models\ClassDisciplineModel();
    $semesterModel = new \App\Models\SemesterModel();
    
    // Buscar a turma para saber o ano letivo
    $class = $this->classModel->find($classId);
    if (!$class) {
        log_message('error', "Turma ID {$classId} não encontrada para sugerir disciplinas do nível de ensino");
        return 0;
    }
    
    // Buscar TODOS os semestres do ano letivo da turma
    $semesters = $semesterModel
        ->where('academic_year_id', $class->academic_year_id)
        ->whereIn('status', ['ativo', 'processado'])
        ->orderBy('start_date', 'ASC')
        ->findAll();
    
    if (empty($semesters)) {
        log_message('warning', "Nenhum semestre ativo encontrado para o ano letivo da turma ID {$classId}");
        return 0;
    }
    
    // Buscar disciplinas configuradas para este nível de ensino
    $gradeDisciplines = $gradeDisciplineModel
        ->select('
            tbl_grade_disciplines.*,
            tbl_disciplines.discipline_name,
            tbl_disciplines.workload_hours as default_workload
        ')
        ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_grade_disciplines.discipline_id')
        ->where('tbl_grade_disciplines.grade_level_id', $gradeLevelId)
        ->where('tbl_grade_disciplines.is_mandatory', 1) // Só obrigatórias por padrão
        ->where('tbl_disciplines.is_active', 1)
        ->findAll();
    
    if (empty($gradeDisciplines)) {
        log_message('info', "Nenhuma disciplina configurada para o nível de ensino ID {$gradeLevelId}");
        return 0;
    }
    
    $suggestedCount = 0;
    $insertData = [];
    
    foreach ($gradeDisciplines as $gd) {
        // O período já está no formato correto (Anual, 1º Semestre, 2º Semestre)
        $periodType = $gd->semester ?? 'Anual';
        
        // Verificar se já existe para esta turma, disciplina e período
        $exists = $classDisciplineModel
            ->where('class_id', $classId)
            ->where('discipline_id', $gd->discipline_id)
            ->where('period_type', $periodType)
            ->first();
        
        if (!$exists) {
            // Usar workload configurado ou o padrão da disciplina
            $workload = $gd->workload_hours ?? $gd->default_workload ?? 0;
            
            $insertData[] = [
                'class_id' => $classId,
                'discipline_id' => $gd->discipline_id,
                'workload_hours' => $workload,
                'period_type' => $periodType,
                'teacher_id' => null,
                'is_active' => 1,
                'created_at' => date('Y-m-d H:i:s')
            ];
            $suggestedCount++;
        }
    }
    
    // Inserir em lote para melhor performance
    if (!empty($insertData)) {
        $db = db_connect();
        $db->table('tbl_class_disciplines')->insertBatch($insertData);
        log_message('info', "Sugeridas {$suggestedCount} disciplinas do nível de ensino para turma ID {$classId} (Nível: {$gradeLevelId})");
    }
    
    return $suggestedCount;
}
    /**
     * View class details - Atualizado para incluir curso
     */
    public function view($id)
    {
        $data['title'] = 'Detalhes da Turma';
        $data['class'] = $this->classModel->getWithTeacherAndAcademicYear($id);
        
        if (!$data['class']) {
            return redirect()->to('/admin/classes')->with('error', 'Turma não encontrada');
        }
        
        // Buscar informações do curso
        if ($data['class']['course_id']) {
            $data['course'] = $this->courseModel->find($data['class']['course_id']);
        } else {
            $data['course'] = null;
        }
        
        // Get students in this class
        $data['students'] = $this->enrollmentModel
            ->select('tbl_enrollments.*, tbl_students.student_number, tbl_users.first_name, tbl_users.last_name')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->where('tbl_enrollments.class_id', $id)
            ->where('tbl_enrollments.status', 'Ativo')
            ->orderBy('tbl_users.first_name', 'ASC')
            ->findAll();
        
        $data['availableSeats'] = $this->classModel->getAvailableSeats($id);
        
        return view('admin/classes/classes/view', $data);
    }

    public function listStudents($id)
    {
        $class = $this->classModel->getWithAcademicYear($id);
        
        if (!$class) {
            return redirect()->to('/admin/classes')->with('error', 'Turma não encontrada');
        }
        
        // resto do código igual
        $data['title'] = 'Alunos da Turma ' . $class['class_name'];
        $data['class'] = $class;
        $data['students'] = $this->enrollmentModel
            ->select('tbl_enrollments.*, tbl_students.student_number, tbl_users.first_name, tbl_users.last_name, tbl_users.email, tbl_users.phone')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->where('tbl_enrollments.class_id', $id)
            ->where('tbl_enrollments.status', 'Ativo')
            ->orderBy('tbl_users.first_name', 'ASC')
            ->findAll();
        
        return view('admin/classes/classes/students', $data);
    }
    /**
     * Save class (Create or Update)
     */
    /* public function save()
    {
        $id = $this->request->getPost('id');
        
        // Preparar dados
        $data = [
            'class_name' => $this->request->getPost('class_name'),
            'class_code' => $this->request->getPost('class_code'),
            'grade_level_id' => $this->request->getPost('grade_level_id'),
            'academic_year_id' => $this->request->getPost('academic_year_id'),
            'class_shift' => $this->request->getPost('class_shift'),
            'class_room' => $this->request->getPost('class_room'),
            'capacity' => $this->request->getPost('capacity') ?: null,
            'class_teacher_id' => $this->request->getPost('class_teacher_id') ?: null,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];
        
        // Se for atualização, incluir o ID nos dados
        if ($id) {
            $data['id'] = $id;
        }
        
        // Validação adicional: verificar se já existe turma com mesmo nome no mesmo ano letivo
        $existingClass = $this->classModel
            ->where('class_name', $data['class_name'])
            ->where('academic_year_id', $data['academic_year_id'])
            ->where('id !=', $id ?: 0)
            ->first();
        
        if ($existingClass) {
            return redirect()->back()->withInput()
                ->with('error', 'Já existe uma turma com este nome no mesmo ano letivo.');
        }
        
        // Validação adicional: código único
        $existingCode = $this->classModel
            ->where('class_code', $data['class_code'])
            ->where('id !=', $id ?: 0)
            ->first();
        
        if ($existingCode) {
            return redirect()->back()->withInput()
                ->with('error', 'Este código de turma já está em uso.');
        }
        
        // Usar o método save() do model que já lida com placeholders
        if ($this->classModel->save($data)) {
            $action = $id ? 'atualizada' : 'criada';
            $message = "Turma '{$data['class_name']}' {$action} com sucesso!";
            
            log_message('info', "Turma ID " . ($id ?: $this->classModel->getInsertID()) . " {$action} por usuário " . session()->get('user_id'));
            
            return redirect()->to('/admin/classes/classes')
                ->with('success', $message);
        } else {
            $errors = $this->classModel->errors();
            if (!empty($errors)) {
                return redirect()->back()->withInput()
                    ->with('errors', $errors);
            }
            return redirect()->back()->withInput()
                ->with('error', 'Erro ao ' . ($id ? 'atualizar' : 'criar') . ' turma.');
        }
    } */
        /**
     * Delete class
     */
    public function delete($id)
    {
        $class = $this->classModel->find($id);
        
        if (!$class) {
            return redirect()->back()->with('error', 'Turma não encontrada.');
        }
        
        // Verificar se existem alunos matriculados
        $enrollmentsCount = $this->enrollmentModel
            ->where('class_id', $id)
            ->where('status', 'Ativo')
            ->countAllResults();
        
        if ($enrollmentsCount > 0) {
            return redirect()->back()
                ->with('error', "Não é possível eliminar esta turma porque existem {$enrollmentsCount} alunos matriculados.");
        }
        
        // Soft delete ou delete real? Vamos fazer soft delete atualizando status
        $data = ['is_active' => 0];
        
        if ($this->classModel->update($id, $data)) {
            log_message('info', "Turma ID {$id} desativada por usuário " . session()->get('user_id'));
            return redirect()->to('/admin/classes/classes')
                ->with('success', 'Turma desativada com sucesso.');
        } else {
            return redirect()->back()->with('error', 'Erro ao desativar turma.');
        }
    }

    /**
     * Activate class
     */
    public function activate($id)
    {
        $class = $this->classModel->find($id);
        
        if (!$class) {
            return redirect()->back()->with('error', 'Turma não encontrada.');
        }
        
        $data = ['is_active' => 1];
        
        if ($this->classModel->update($id, $data)) {
            log_message('info', "Turma ID {$id} ativada por usuário " . session()->get('user_id'));
            return redirect()->to('/admin/classes/classes')
                ->with('success', 'Turma ativada com sucesso.');
        } else {
            return redirect()->back()->with('error', 'Erro ao ativar turma.');
        }
    }
   /**
 * Get classes by grade level and academic year (AJAX)
 * 
 * @param int $levelId ID do nível de ensino
 * @param int $yearId ID do ano letivo
 * @return JSON
 */
public function getByLevelAndYear($levelId, $yearId)
{
    // Log para debug
    log_message('debug', "getByLevelAndYear chamado: levelId={$levelId}, yearId={$yearId}");
    
    // Verificar se é uma requisição AJAX (opcional, pode remover para teste)
    // if (!$this->request->isAJAX()) {
    //     return $this->response->setJSON([]);
    // }
    
    // Validar parâmetros
    if (!$levelId || !$yearId) {
        return $this->response->setJSON([]);
    }
    
    // Buscar turmas usando o método do model
    $classes = $this->classModel->getByLevelAndYearWithStats($levelId, $yearId);
    
    // Log para debug
    log_message('debug', 'Turmas encontradas: ' . count($classes));
    
    // Calcular vagas disponíveis para cada turma
    foreach ($classes as $class) {
        $class['enrolled_count']  = $class['enrolled_count']  ?? 0;
        $class['available_seats']  = $class['capacity']  - $class['enrolled_count'] ;
        
        // Garantir que level_name esteja presente
        if (!isset($class['level_name'])) {
            $class['level_name'] = '';
        }
    }
    
    return $this->response->setJSON($classes);
}
        /**
     * Check class availability (AJAX)
     */
    public function checkAvailability($classId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['available' => 0]);
        }
        
        $available = $this->classModel->getAvailableSeats($classId);
        
        return $this->response->setJSON(['available' => $available]);
    }
    /**
     * Get classes by academic year (AJAX)
     * 
     * @param int $yearId ID do ano letivo
     * @return JSON
     */
    public function getByYear($yearId)
    {
        // Log para debug
        log_message('debug', "getByYear chamado: yearId={$yearId}");
        
        // Validar parâmetros
        if (!$yearId) {
            return $this->response->setJSON([]);
        }
        
        // Buscar turmas do ano letivo
        $classes = $this->classModel
            ->select('tbl_classes.id, tbl_classes.class_name, tbl_classes.class_code, tbl_classes.class_shift, tbl_grade_levels.level_name')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
            ->where('tbl_classes.academic_year_id', $yearId)
            ->where('tbl_classes.is_active', 1)
            ->orderBy('tbl_classes.class_name', 'ASC')
            ->findAll();
        
        log_message('debug', 'Turmas encontradas: ' . count($classes));
        
        return $this->response->setJSON($classes);
    }
  
}