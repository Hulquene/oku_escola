<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\EnrollmentModel;
use App\Models\ClassModel;
use App\Models\StudentModel;
use App\Models\DisciplineModel;
use App\Models\SemesterModel;
use App\Models\AcademicYearModel;
use App\Models\CourseModel;
use App\Models\GradeLevelModel;
use App\Models\DisciplineAverageModel;
use App\Models\SemesterResultModel;
use App\Models\AcademicHistoryModel;
use App\Models\ExamResultModel; 
use App\Models\ClassDisciplineModel;

class AcademicRecords extends BaseController
{
    protected $enrollmentModel;
    protected $classModel;
    protected $studentModel;
    protected $disciplineModel;
    protected $semesterModel;
    protected $academicYearModel;
    protected $courseModel;
    protected $gradeLevelModel;
    protected $disciplineAverageModel;
    protected $semesterResultModel;
    protected $academicHistoryModel;
    protected $examResultModel;
    protected $classDisciplineModel; 
    
    public function __construct()
    {
        $this->enrollmentModel = new EnrollmentModel();
        $this->classModel = new ClassModel();
        $this->studentModel = new StudentModel();
        $this->disciplineModel = new DisciplineModel();
        $this->semesterModel = new SemesterModel();
        $this->academicYearModel = new AcademicYearModel();
        $this->courseModel = new CourseModel();
        $this->gradeLevelModel = new GradeLevelModel();
        $this->disciplineAverageModel = new DisciplineAverageModel();
        $this->semesterResultModel = new SemesterResultModel();
        $this->academicHistoryModel = new AcademicHistoryModel();
        $this->examResultModel = new ExamResultModel(); 
        $this->classDisciplineModel = new ClassDisciplineModel(); 
        
        helper('auth');
        helper('number');
    }
    
    /**
     * Página principal de pautas - listagem de turmas
     */
    public function index()
    {
        $data['title'] = 'Pautas Acadêmicas';
        
        // Capturar filtros com validação
        $academicYearId = $this->request->getGet('academic_year');
        $courseId = $this->request->getGet('course');
        $levelId = $this->request->getGet('level');
        $shift = $this->request->getGet('shift');
        $status = $this->request->getGet('status');
        $search = $this->request->getGet('search');
        
        // Capturar ação de remoção de filtro
        $removeFilter = $this->request->getGet('remove');
        
        // Processar remoção de filtro individual
        if ($removeFilter) {
            $params = $_GET;
            unset($params['remove']);
            unset($params[$removeFilter]);
            return redirect()->to(current_url() . '?' . http_build_query($params));
        }
        
        // Obter ano letivo atual
        $currentYear = $this->academicYearModel->getCurrent();
        
        // Se não selecionou ano, usa o atual como padrão
        if (!$academicYearId && $currentYear) {
            $academicYearId = $currentYear->id;
        }
        
        // Dados para filtros
        $data['academicYears'] = $this->academicYearModel
            ->where('is_active', 1)
            ->orderBy('year_name', 'DESC')
            ->findAll();
        
        $data['courses'] = $this->courseModel
            ->where('is_active', 1)
            ->orderBy('course_name', 'ASC')
            ->findAll();
        
        $data['gradeLevels'] = $this->gradeLevelModel
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->findAll();
        
        $data['currentYear'] = $currentYear;
        
        // Construir query base com subqueries para performance
        $classBuilder = $this->classModel
            ->select('
                tbl_classes.*,
                tbl_grade_levels.level_name,
                tbl_grade_levels.sort_order as level_sort,
                tbl_courses.course_name,
                tbl_courses.course_code,
                tbl_academic_years.year_name,
                tbl_academic_years.is_current as year_is_current,
                tbl_users.first_name as teacher_first_name,
                tbl_users.last_name as teacher_last_name,
                (
                    SELECT COUNT(*) 
                    FROM tbl_enrollments 
                    WHERE class_id = tbl_classes.id 
                    AND status = "Ativo"
                ) as total_alunos,
                (
                    SELECT COUNT(*) 
                    FROM tbl_semester_results sr
                    JOIN tbl_enrollments e ON e.id = sr.enrollment_id
                    WHERE e.class_id = tbl_classes.id 
                    AND sr.status != "Em Andamento"
                ) as alunos_com_resultado
            ')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
            ->join('tbl_courses', 'tbl_courses.id = tbl_classes.course_id', 'left')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_classes.academic_year_id')
            ->join('tbl_users', 'tbl_users.id = tbl_classes.class_teacher_id', 'left')
            ->where('tbl_classes.is_active', 1);
        
        // Aplicar filtros
        if ($academicYearId) {
            $classBuilder->where('tbl_classes.academic_year_id', $academicYearId);
        }
        
        if ($courseId !== null && $courseId !== '') {
            if ($courseId === '0') {
                $classBuilder->where('tbl_classes.course_id IS NULL');
            } else {
                $classBuilder->where('tbl_classes.course_id', $courseId);
            }
        }
        
        if ($levelId) {
            $classBuilder->where('tbl_classes.grade_level_id', $levelId);
        }
        
        if ($shift) {
            $classBuilder->where('tbl_classes.class_shift', $shift);
        }
        
        if ($search) {
            $classBuilder->groupStart()
                ->like('tbl_classes.class_name', $search)
                ->orLike('tbl_classes.class_code', $search)
                ->groupEnd();
        }
        
        // Aplicar filtro de status
        if ($status) {
            if ($status === 'finalized') {
                $classBuilder->having('total_alunos > 0')
                            ->having('alunos_com_resultado = total_alunos');
            } elseif ($status === 'pending') {
                $classBuilder->having('total_alunos > 0')
                            ->having('alunos_com_resultado < total_alunos');
            } elseif ($status === 'empty') {
                $classBuilder->having('total_alunos = 0');
            }
        }
        
        // Ordenação
        $orderBy = $this->request->getGet('order_by') ?? 'class_name';
        $orderDir = $this->request->getGet('order_dir') ?? 'ASC';
        
        $allowedOrders = ['class_name', 'level_sort', 'year_name', 'total_alunos'];
        if (!in_array($orderBy, $allowedOrders)) {
            $orderBy = 'class_name';
        }
        $orderDir = strtoupper($orderDir) === 'DESC' ? 'DESC' : 'ASC';
        
        $classBuilder->orderBy($orderBy, $orderDir);
        
        // Paginação
        $perPage = $this->request->getGet('per_page') ?? 12;
        $classes = $classBuilder->paginate($perPage);
        
        $data['pager'] = $this->classModel->pager;
        
        // Processar resultados
        foreach ($classes as $class) {
            $class->is_finalized = ($class->total_alunos > 0 && 
                                     $class->alunos_com_resultado == $class->total_alunos);
            
            if ($class->total_alunos > 0) {
                $class->progress_percentage = round(($class->alunos_com_resultado / $class->total_alunos) * 100);
            } else {
                $class->progress_percentage = 0;
                $class->is_finalized = false;
            }
            
            $class->status_label = $this->getClassStatusLabel($class);
            $class->status_color = $this->getClassStatusColor($class);
        }
        
        $data['classes'] = $classes;
        
        // Estatísticas gerais
        $stats = $this->getGeneralStats($academicYearId);
        $data['totalClasses'] = $stats['total'];
        $data['finalizedClasses'] = $stats['finalized'];
        $data['pendingClasses'] = $stats['pending'];
        $data['emptyClasses'] = $stats['empty'];
        $data['totalStudents'] = $stats['students'];
        $data['averageProgress'] = $stats['average_progress'];
        
        // Manter valores dos filtros
        $data['selectedYear'] = $academicYearId;
        $data['selectedCourse'] = $courseId;
        $data['selectedLevel'] = $levelId;
        $data['selectedShift'] = $shift;
        $data['selectedStatus'] = $status;
        $data['search'] = $search;
        $data['orderBy'] = $orderBy;
        $data['orderDir'] = $orderDir;
        $data['perPage'] = $perPage;
        
        $data['totalFiltered'] = $classBuilder->countAllResults(false);
        
        // Contar filtros ativos
        $activeFilters = 0;
        if ($academicYearId) $activeFilters++;
        if ($courseId !== null && $courseId !== '') $activeFilters++;
        if ($levelId) $activeFilters++;
        if ($shift) $activeFilters++;
        if ($status) $activeFilters++;
        if ($search) $activeFilters++;
        $data['activeFilters'] = $activeFilters;
        
        $data['orderOptions'] = [
            'class_name' => 'Nome da Turma',
            'level_sort' => 'Nível',
            'year_name' => 'Ano Letivo',
            'total_alunos' => 'Total de Alunos'
        ];
        
        $data['perPageOptions'] = [12, 24, 36, 48, 96];
        
        return view('admin/academic-records/index', $data);
    }
    
/**
 * Visualizar pauta de uma turma específica
 */
/* public function class($classId)
{
    $data['title'] = 'Pauta da Turma';
    
    // Buscar informações da turma
    $data['class'] = $this->classModel
        ->select('
            tbl_classes.*,
            tbl_grade_levels.level_name,
            tbl_courses.course_name,
            tbl_courses.course_code,
            tbl_academic_years.year_name,
            tbl_users.first_name as teacher_first_name,
            tbl_users.last_name as teacher_last_name
        ')
        ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
        ->join('tbl_courses', 'tbl_courses.id = tbl_classes.course_id', 'left')
        ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_classes.academic_year_id')
        ->join('tbl_users', 'tbl_users.id = tbl_classes.class_teacher_id', 'left')
        ->where('tbl_classes.id', $classId)
        ->first();
    
    if (!$data['class']) {
        return redirect()->to('/admin/academic-records')
            ->with('error', 'Turma não encontrada');
    }
    
    // Buscar semestre atual ou o selecionado
    $semesterId = $this->request->getGet('semester');
    if (!$semesterId) {
        $currentSemester = $this->semesterModel->getCurrent();
        $semesterId = $currentSemester ? $currentSemester->id : null;
    }
    
    $data['selectedSemester'] = $semesterId;
    
    // Buscar semestres do ano letivo
    $data['semesters'] = $this->semesterModel
        ->where('academic_year_id', $data['class']->academic_year_id)
        ->whereIn('status', ['ativo', 'processado'])
        ->orderBy('start_date', 'ASC')
        ->findAll();
    
    // Buscar informações do semestre selecionado - CORREÇÃO AQUI
    $selectedSemester = $this->semesterModel->find($semesterId);
    
    // Determinar o period_type baseado no semestre selecionado
    $periodType = 'Anual'; // valor padrão
    
    if ($selectedSemester) {
        // Converter para objeto se for array
        $semesterObj = is_array($selectedSemester) ? (object)$selectedSemester : $selectedSemester;
        
        $semesterType = $semesterObj->semester_type ?? '';
        
        $periodTypeMap = [
            '1º Trimestre' => '1º Semestre',
            '2º Trimestre' => '2º Semestre',
            '3º Trimestre' => 'Anual',
            '1º Semestre' => '1º Semestre',
            '2º Semestre' => '2º Semestre'
        ];
        
        $periodType = $periodTypeMap[$semesterType] ?? 'Anual';
    }
    
    // Buscar disciplinas da turma para este período
    $classDisciplineModel = new \App\Models\ClassDisciplineModel();
    $data['disciplines'] = $classDisciplineModel
        ->select('
            tbl_disciplines.id,
            tbl_disciplines.discipline_name,
            tbl_disciplines.discipline_code
        ')
        ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
        ->where('tbl_class_disciplines.class_id', $classId)
        ->where('tbl_class_disciplines.is_active', 1)
        ->groupStart()
            ->where('tbl_class_disciplines.period_type', $periodType)
            ->orWhere('tbl_class_disciplines.period_type', 'Anual')
        ->groupEnd()
        ->orderBy('tbl_disciplines.discipline_name', 'ASC')
        ->findAll();
    
    // Buscar alunos da turma
    $enrollments = $this->enrollmentModel
        ->select('
            tbl_enrollments.id as enrollment_id,
            tbl_students.id as student_id,
            tbl_users.first_name,
            tbl_users.last_name,
            tbl_users.email,
            tbl_students.student_number,
            tbl_enrollments.final_result,
            tbl_enrollments.final_average
        ')
        ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
        ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
        ->where('tbl_enrollments.class_id', $classId)
        ->where('tbl_enrollments.status', 'Ativo')
        ->orderBy('tbl_users.first_name', 'ASC')
        ->findAll();
    
    // Para cada aluno, buscar médias disciplinares
    foreach ($enrollments as $student) {
        $student->grades = [];
        $totalMedia = 0;
        $disciplinasCount = 0;
        
        foreach ($data['disciplines'] as $discipline) {
            // Buscar média da disciplina usando o novo model
            $avg = $this->disciplineAverageModel
                ->where('enrollment_id', $student->enrollment_id)
                ->where('discipline_id', $discipline->id)
                ->where('semester_id', $semesterId)
                ->first();
            
            if ($avg) {
                $student->grades[$discipline->id] = [
                    'ac_score' => $avg->ac_score,
                    'exam_score' => $avg->exam_score,
                    'final_score' => $avg->final_score,
                    'status' => $avg->status
                ];
                
                $totalMedia += $avg->final_score;
                $disciplinasCount++;
            } else {
                $student->grades[$discipline->id] = null;
            }
        }
        
        $student->overall_average = $disciplinasCount > 0 ? 
            round($totalMedia / $disciplinasCount, 2) : 0;
        
        // Buscar resultado semestral
        $semesterResult = $this->semesterResultModel
            ->where('enrollment_id', $student->enrollment_id)
            ->where('semester_id', $semesterId)
            ->first();
        
        if ($semesterResult) {
            $student->final_result = $semesterResult->status;
            $student->overall_average = $semesterResult->overall_average;
        } else {
            $student->final_result = 'Em Andamento';
        }
    }
    
    $data['students'] = $enrollments;
    
    // Calcular estatísticas da turma
    $totalAprovados = 0;
    $totalRecurso = 0;
    $totalReprovados = 0;
    $totalAlunos = count($enrollments);
    
    foreach ($enrollments as $student) {
        if ($student->final_result == 'Aprovado') $totalAprovados++;
        elseif ($student->final_result == 'Recurso') $totalRecurso++;
        elseif ($student->final_result == 'Reprovado') $totalReprovados++;
    }
    
    $data['stats'] = [
        'total' => $totalAlunos,
        'aprovados' => $totalAprovados,
        'recurso' => $totalRecurso,
        'reprovados' => $totalReprovados,
        'aprovacao' => $totalAlunos > 0 ? 
            round(($totalAprovados / $totalAlunos) * 100, 1) : 0
    ];
    
    return view('admin/academic-records/class', $data);
} */

    /**
 * Visualizar pauta de uma turma específica (modelo pauta final)
 */
public function class($classId)
{
    $data['title'] = 'Pauta da Turma';
    
    // Buscar informações da turma
    $data['class'] = $this->classModel
        ->select('
            tbl_classes.*,
            tbl_grade_levels.level_name,
            tbl_courses.course_name,
            tbl_courses.course_code,
            tbl_academic_years.year_name,
            tbl_users.first_name as teacher_first_name,
            tbl_users.last_name as teacher_last_name
        ')
        ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
        ->join('tbl_courses', 'tbl_courses.id = tbl_classes.course_id', 'left')
        ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_classes.academic_year_id')
        ->join('tbl_users', 'tbl_users.id = tbl_classes.class_teacher_id', 'left')
        ->where('tbl_classes.id', $classId)
        ->first();
    
    if (!$data['class']) {
        return redirect()->to('/admin/academic-records')
            ->with('error', 'Turma não encontrada');
    }
    
    // Buscar todas as disciplinas da turma (para exibir no cabeçalho)
    $classDisciplineModel = new \App\Models\ClassDisciplineModel();
    $data['disciplines'] = $classDisciplineModel
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
    
    // Buscar alunos da turma
    $enrollments = $this->enrollmentModel
        ->select('
            tbl_enrollments.id as enrollment_id,
            tbl_enrollments.final_result,
            tbl_enrollments.final_average,
            tbl_students.id as student_id,
            tbl_students.student_number,
            tbl_users.first_name,
            tbl_users.last_name,
            CONCAT(tbl_users.first_name, " ", tbl_users.last_name) as full_name
        ')
        ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
        ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
        ->where('tbl_enrollments.class_id', $classId)
        ->where('tbl_enrollments.status', 'Ativo')
        ->orderBy('tbl_users.first_name', 'ASC')
        ->findAll();
    
    // Para cada aluno, buscar médias por disciplina e trimestre
    foreach ($enrollments as $student) {
        $student->disciplinas = [];
        $totalFinalScore = 0;
        $disciplinasCount = 0;
        
        foreach ($data['disciplines'] as $discipline) {
            // Buscar notas da disciplina por trimestre
            $notas = $this->getDisciplineTrimestralScores($student->enrollment_id, $discipline->id);
            
            $student->disciplinas[$discipline->id] = $notas;
            
            // Somar para média geral
            if ($notas['mfd'] !== null && $notas['mfd'] > 0) {
                $totalFinalScore += $notas['mfd'];
                $disciplinasCount++;
            }
        }
        
        // Calcular média final do aluno (MFD geral)
        $student->media_final_geral = $disciplinasCount > 0 ? 
            round($totalFinalScore / $disciplinasCount, 2) : 0;
        
        // Determinar resultado final (Transita ou Não Transita)
        // No sistema angolano, normalmente precisa ter média >= 10 em todas as disciplinas
        // ou critérios específicos definidos pela escola
        $student->resultado_final = $this->determinarResultadoFinal($student->disciplinas);
    }
    
    $data['students'] = $enrollments;
    
    return view('admin/academic-records/class', $data);
}

/**
 * Buscar notas de uma disciplina por trimestre
 */
private function getDisciplineTrimestralScores($enrollmentId, $disciplineId)
{
    // Buscar resultados da disciplina para todos os trimestres
    $results = $this->examResultModel
        ->select('
            tbl_exam_results.*,
            tbl_exam_schedules.exam_date,
            tbl_exam_periods.semester_id,
            tbl_semesters.semester_type,
            tbl_semesters.semester_name,
            tbl_exam_boards.board_code
        ')
        ->join('tbl_exam_schedules', 'tbl_exam_schedules.id = tbl_exam_results.exam_schedule_id')
        ->join('tbl_exam_periods', 'tbl_exam_periods.id = tbl_exam_schedules.exam_period_id')
        ->join('tbl_semesters', 'tbl_semesters.id = tbl_exam_periods.semester_id')
        ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exam_schedules.exam_board_id')
        ->where('tbl_exam_results.enrollment_id', $enrollmentId)
        ->where('tbl_exam_schedules.discipline_id', $disciplineId)
        ->orderBy('tbl_semesters.start_date', 'ASC')
        ->findAll();
    
    // Mapear trimestres
    $trimestres = [
        1 => ['notas' => [], 'mt' => null],
        2 => ['notas' => [], 'mt' => null],
        3 => ['notas' => [], 'mt' => null]
    ];
    
    $semesterMap = [
        '1º Trimestre' => 1,
        '2º Trimestre' => 2,
        '3º Trimestre' => 3,
        '1º Semestre' => 1,
        '2º Semestre' => 2
    ];
    
    // Agrupar notas por trimestre
    foreach ($results as $result) {
        $trimestre = $semesterMap[$result->semester_type] ?? 1;
        
        if (!isset($trimestres[$trimestre]['notas'][$result->board_code])) {
            $trimestres[$trimestre]['notas'][$result->board_code] = [];
        }
        
        $trimestres[$trimestre]['notas'][$result->board_code][] = $result->score;
    }
    
    // Calcular médias por trimestre
    $somaMFD = 0;
    $trimestresComMateria = 0;
    
    for ($t = 1; $t <= 3; $t++) {
        $notas = $trimestres[$t]['notas'];
        
        // Calcular média do trimestre (MT)
        $notasTrimestre = [];
        
        if (isset($notas['AC']) && !empty($notas['AC'])) {
            $trimestres[$t]['ac'] = round(array_sum($notas['AC']) / count($notas['AC']), 1);
            $notasTrimestre[] = $trimestres[$t]['ac'];
        } else {
            $trimestres[$t]['ac'] = null;
        }
        
        if (isset($notas['NPP']) && !empty($notas['NPP'])) {
            $trimestres[$t]['npp'] = round(array_sum($notas['NPP']) / count($notas['NPP']), 1);
            $notasTrimestre[] = $trimestres[$t]['npp'];
        } else {
            $trimestres[$t]['npp'] = null;
        }
        
        if (isset($notas['NPT']) && !empty($notas['NPT'])) {
            $trimestres[$t]['npt'] = round(array_sum($notas['NPT']) / count($notas['NPT']), 1);
            $notasTrimestre[] = $trimestres[$t]['npt'];
        } else {
            $trimestres[$t]['npt'] = null;
        }
        
        // Calcular MT (média do trimestre) - só considera se tiver as 3 notas
        if (count($notasTrimestre) == 3) {
            $trimestres[$t]['mt'] = round(array_sum($notasTrimestre) / 3, 1);
            $somaMFD += $trimestres[$t]['mt'];
            $trimestresComMateria++;
        } else {
            $trimestres[$t]['mt'] = null;
        }
    }
    
    // Calcular MFD (Média Final da Disciplina)
    $mfd = $trimestresComMateria == 3 ? round($somaMFD / 3, 1) : null;
    
    return [
        'trimestre1' => [
            'ac' => $trimestres[1]['ac'],
            'npp' => $trimestres[1]['npp'],
            'npt' => $trimestres[1]['npt'],
            'mt' => $trimestres[1]['mt']
        ],
        'trimestre2' => [
            'ac' => $trimestres[2]['ac'],
            'npp' => $trimestres[2]['npp'],
            'npt' => $trimestres[2]['npt'],
            'mt' => $trimestres[2]['mt']
        ],
        'trimestre3' => [
            'ac' => $trimestres[3]['ac'],
            'npp' => $trimestres[3]['npp'],
            'npt' => $trimestres[3]['npt'],
            'mt' => $trimestres[3]['mt']
        ],
        'mfd' => $mfd
    ];
}

/**
 * Determinar resultado final do aluno (Transita/Não Transita)
 */
private function determinarResultadoFinal($disciplinas)
{
    if (empty($disciplinas)) {
        return 'Sem dados';
    }
    
    $disciplinasAprovadas = 0;
    $totalDisciplinas = count($disciplinas);
    
    foreach ($disciplinas as $discId => $notas) {
        // Considera aprovado se MFD >= 10
        if (isset($notas['mfd']) && $notas['mfd'] >= 10) {
            $disciplinasAprovadas++;
        }
    }
    
    // Critério: precisa ter MFD >= 10 em todas as disciplinas
    if ($disciplinasAprovadas == $totalDisciplinas) {
        return 'Transita';
    } else {
        return 'Não Transita';
    }
}
/**
 * Exportar pauta final para Excel
 */
public function exportFinal($classId)
{
    // Buscar dados da turma
    $class = $this->classModel
        ->select('
            tbl_classes.*,
            tbl_grade_levels.level_name,
            tbl_courses.course_name,
            tbl_academic_years.year_name
        ')
        ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
        ->join('tbl_courses', 'tbl_courses.id = tbl_classes.course_id', 'left')
        ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_classes.academic_year_id')
        ->find($classId);
    
    if (!$class) {
        return redirect()->back()->with('error', 'Turma não encontrada');
    }
    
    // Buscar disciplinas
    $classDisciplineModel = new \App\Models\ClassDisciplineModel();
    $disciplines = $classDisciplineModel
        ->select('
            tbl_disciplines.id,
            tbl_disciplines.discipline_name,
            tbl_disciplines.discipline_code
        ')
        ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
        ->where('tbl_class_disciplines.class_id', $classId)
        ->where('tbl_class_disciplines.is_active', 1)
        ->orderBy('tbl_disciplines.discipline_name', 'ASC')
        ->findAll();
    
    // Buscar alunos e notas
    $enrollments = $this->enrollmentModel
        ->select('
            tbl_enrollments.id as enrollment_id,
            tbl_students.student_number,
            tbl_users.first_name,
            tbl_users.last_name
        ')
        ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
        ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
        ->where('tbl_enrollments.class_id', $classId)
        ->where('tbl_enrollments.status', 'Ativo')
        ->orderBy('tbl_users.first_name', 'ASC')
        ->findAll();
    
    // Preparar dados para Excel
    $dados = [];
    foreach ($enrollments as $student) {
        $row = [
            'nome' => $student->first_name . ' ' . $student->last_name,
            'numero' => $student->student_number
        ];
        
        foreach ($disciplines as $disc) {
            $notas = $this->getDisciplineTrimestralScores($student->enrollment_id, $disc->id);
            
            $row['disc_' . $disc->id . '_m1'] = $notas['trimestre1']['mt'] ?? '';
            $row['disc_' . $disc->id . '_mt2'] = $notas['trimestre2']['mt'] ?? '';
            $row['disc_' . $disc->id . '_mt3'] = $notas['trimestre3']['mt'] ?? '';
            $row['disc_' . $disc->id . '_mfd'] = $notas['mfd'] ?? '';
        }
        
        $row['resultado'] = $this->determinarResultadoFinal([$student->enrollment_id => []]); // Simplificado
        
        $dados[] = $row;
    }
    
    // Gerar Excel
    return $this->gerarExcelFinal($class, $disciplines, $dados);
}

/**
 * Gerar arquivo Excel da pauta final
 */
private function gerarExcelFinal($class, $disciplines, $dados)
{
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    
    // Título
    $sheet->setCellValue('A1', 'MAPA DE AVALIAÇÃO FINAL - ' . strtoupper($class->year_name));
    $sheet->mergeCells('A1:' . $this->getColumnLetter(2 + (count($disciplines) * 4)) . '1');
    $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
    
    // Informações
    $sheet->setCellValue('A2', 'Curso: ' . ($class->course_name ?? 'Ensino Geral'));
    $sheet->setCellValue('A3', 'Classe: ' . $class->level_name . ' - Turma: ' . $class->class_name);
    
    // Cabeçalho
    $sheet->setCellValue('A5', 'Nº');
    $sheet->setCellValue('B5', 'NOME COMPLETO');
    
    $col = 'C';
    foreach ($disciplines as $disc) {
        $sheet->setCellValue($col . '5', $disc->discipline_name);
        $sheet->mergeCells($col . '5:' . $this->getColumnLetter($this->getColumnNumber($col) + 3) . '5');
        
        $sheet->setCellValue($col . '6', 'M1');
        $col = $this->getNextColumn($col);
        $sheet->setCellValue($col . '6', 'MT2');
        $col = $this->getNextColumn($col);
        $sheet->setCellValue($col . '6', 'MT3');
        $col = $this->getNextColumn($col);
        $sheet->setCellValue($col . '6', 'MFD');
        $col = $this->getNextColumn($col);
    }
    
    $sheet->setCellValue($col . '5', 'RESULTADO');
    $sheet->mergeCells($col . '5:' . $col . '6');
    
    // Dados
    $row = 7;
    $counter = 1;
    foreach ($dados as $item) {
        $sheet->setCellValue('A' . $row, $counter++);
        $sheet->setCellValue('B' . $row, $item['nome']);
        
        $col = 'C';
        foreach ($disciplines as $disc) {
            $sheet->setCellValue($col++ . $row, $item['disc_' . $disc->id . '_m1']);
            $sheet->setCellValue($col++ . $row, $item['disc_' . $disc->id . '_mt2']);
            $sheet->setCellValue($col++ . $row, $item['disc_' . $disc->id . '_mt3']);
            $sheet->setCellValue($col++ . $row, $item['disc_' . $disc->id . '_mfd']);
        }
        
        $sheet->setCellValue($col++ . $row, $item['resultado']);
        $row++;
    }
    
    // Estilo
    $sheet->getStyle('A5:' . $col . '6')->getFont()->setBold(true);
    $sheet->getStyle('A5:' . $col . ($row - 1))->getBorders()->getAllBorders()
        ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
    
    // Ajustar largura
    foreach (range('A', $col) as $columnID) {
        $sheet->getColumnDimension($columnID)->setAutoSize(true);
    }
    
    // Download
    $filename = 'pauta_final_' . $class->class_code . '_' . date('Ymd') . '.xlsx';
    
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    
    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

// Funções auxiliares para Excel
private function getColumnNumber($col)
{
    return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($col);
}

private function getNextColumn($col)
{
    return \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(
        \PhpOffice\PhpSpreadsheet\Cell\Coordinate::columnIndexFromString($col) + 1
    );
}
    /**
     * Visualizar histórico de um aluno
     */
    public function student($studentId)
    {
        $data['title'] = 'Histórico do Aluno';
        
        $student = $this->studentModel->getWithUser($studentId);
        
        if (!$student) {
            return redirect()->to('/admin/academic-records')
                ->with('error', 'Aluno não encontrado');
        }
        
        $data['student'] = $student;
        
        // Buscar todas as matrículas do aluno
        $enrollments = $this->enrollmentModel
            ->select('
                tbl_enrollments.*,
                tbl_classes.class_name,
                tbl_classes.class_code,
                tbl_academic_years.year_name,
                tbl_academic_years.start_date,
                tbl_academic_years.end_date,
                tbl_grade_levels.level_name
            ')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_enrollments.academic_year_id')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
            ->where('tbl_enrollments.student_id', $studentId)
            ->orderBy('tbl_academic_years.start_date', 'DESC')
            ->findAll();
        
        // Para cada matrícula, buscar resultados semestrais e anual
        foreach ($enrollments as $enrollment) {
            $enrollment->semesters = $this->semesterResultModel
                ->select('
                    tbl_semester_results.*,
                    tbl_semesters.semester_name,
                    tbl_semesters.semester_type
                ')
                ->join('tbl_semesters', 'tbl_semesters.id = tbl_semester_results.semester_id')
                ->where('tbl_semester_results.enrollment_id', $enrollment->id)
                ->orderBy('tbl_semesters.start_date', 'ASC')
                ->findAll();
            
            $enrollment->yearly = $this->academicHistoryModel
                ->where('student_id', $studentId)
                ->where('academic_year_id', $enrollment->academic_year_id)
                ->first();
        }
        
        $data['enrollments'] = $enrollments;
        
        return view('admin/academic-records/student', $data);
    }
    
    /**
     * Finalizar ano/semestre - calcular e salvar resultados
     */
    public function finalizeYear()
    {
        $classId = $this->request->getPost('class_id');
        $semesterId = $this->request->getPost('semester_id');
        
        if (!$classId || !$semesterId) {
            return redirect()->back()
                ->with('error', 'Turma e semestre são obrigatórios');
        }
        
        $db = db_connect();
        $db->transStart();
        
        // Buscar todos alunos ativos da turma
        $enrollments = $this->enrollmentModel
            ->select('id, student_id')
            ->where('class_id', $classId)
            ->where('status', 'Ativo')
            ->findAll();
        
        if (empty($enrollments)) {
            $db->transRollback();
            return redirect()->back()
                ->with('error', 'Não há alunos ativos nesta turma');
        }
        
        $updated = 0;
        $errors = [];
        
        foreach ($enrollments as $enrollment) {
            try {
                // Calcular resultados semestrais para cada aluno
                $resultId = $this->semesterResultModel->calculate(
                    $enrollment->id,
                    $semesterId,
                    session()->get('user_id')
                );
                
                if ($resultId) {
                    $updated++;
                    
                    // Verificar se é o último semestre do ano
                    $semester = $this->semesterModel->find($semesterId);
                    $academicYear = $this->academicYearModel->find($semester->academic_year_id);
                    
                    // Buscar total de semestres do ano
                    $totalSemesters = $this->semesterModel
                        ->where('academic_year_id', $academicYear->id)
                        ->countAllResults();
                    
                    // Buscar semestres já calculados para este aluno
                    $calculatedSemesters = $this->semesterResultModel
                        ->where('enrollment_id', $enrollment->id)
                        ->where('semester_id IN (SELECT id FROM tbl_semesters WHERE academic_year_id = ?)', [$academicYear->id])
                        ->countAllResults();
                    
                    // Se todos os semestres foram calculados, gerar histórico anual
                    if ($calculatedSemesters == $totalSemesters) {
                        $this->generateYearlyHistory($enrollment->id, $enrollment->student_id, $academicYear->id);
                    }
                }
            } catch (\Exception $e) {
                $errors[] = "Erro ao processar aluno ID: {$enrollment->id} - " . $e->getMessage();
            }
        }
        
        $db->transComplete();
        
        if ($db->transStatus()) {
            $message = "{$updated} alunos finalizados com sucesso!";
            if (!empty($errors)) {
                $message .= " Erros: " . implode(', ', $errors);
            }
            
            return redirect()->to('/admin/academic-records/class/' . $classId . '?semester=' . $semesterId)
                ->with('success', $message);
        } else {
            return redirect()->back()
                ->with('error', 'Erro ao finalizar semestre');
        }
    }
    
    /**
     * Gerar histórico anual para um aluno
     */
    private function generateYearlyHistory($enrollmentId, $studentId, $academicYearId)
    {
        // Buscar todos os resultados semestrais do aluno neste ano
        $semesterResults = $this->semesterResultModel
            ->select('
                tbl_semester_results.*,
                tbl_semesters.semester_type
            ')
            ->join('tbl_semesters', 'tbl_semesters.id = tbl_semester_results.semester_id')
            ->where('tbl_semester_results.enrollment_id', $enrollmentId)
            ->where('tbl_semesters.academic_year_id', $academicYearId)
            ->findAll();
        
        if (empty($semesterResults)) {
            return false;
        }
        
        // Calcular média anual
        $totalAverage = 0;
        $hasFailed = false;
        $hasAppeal = false;
        
        foreach ($semesterResults as $result) {
            $totalAverage += $result->overall_average;
            
            if ($result->status == 'Reprovado') {
                $hasFailed = true;
            } elseif ($result->status == 'Recurso') {
                $hasAppeal = true;
            }
        }
        
        $yearlyAverage = round($totalAverage / count($semesterResults), 2);
        
        // Determinar status final do ano
        if ($hasFailed) {
            $finalStatus = 'Reprovado';
        } elseif ($hasAppeal) {
            $finalStatus = 'Recurso';
        } else {
            $finalStatus = 'Aprovado';
        }
        
        // Buscar informações da matrícula
        $enrollment = $this->enrollmentModel->find($enrollmentId);
        
        // Criar ou atualizar histórico anual
        $existing = $this->academicHistoryModel
            ->where('student_id', $studentId)
            ->where('academic_year_id', $academicYearId)
            ->first();
        
        $historyData = [
            'student_id' => $studentId,
            'academic_year_id' => $academicYearId,
            'class_id' => $enrollment->class_id,
            'final_status' => $finalStatus,
            'final_average' => $yearlyAverage,
            'observations' => 'Histórico gerado automaticamente'
        ];
        
        if ($existing) {
            return $this->academicHistoryModel->update($existing->id, $historyData);
        } else {
            return $this->academicHistoryModel->insert($historyData);
        }
    }
    
    /**
     * Gerar certificado/conclusão para aluno
     */
    public function certificate($studentId)
    {
        $student = $this->studentModel->getWithUser($studentId);
        
        if (!$student) {
            return redirect()->to('/admin/academic-records')
                ->with('error', 'Aluno não encontrado');
        }
        
        // Buscar último ano concluído
        $lastHistory = $this->academicHistoryModel
            ->select('
                tbl_academic_history.*,
                tbl_academic_years.year_name,
                tbl_classes.class_name,
                tbl_classes.class_code,
                tbl_grade_levels.level_name,
                tbl_courses.course_name
            ')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_academic_history.academic_year_id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_academic_history.class_id')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
            ->join('tbl_courses', 'tbl_courses.id = tbl_classes.course_id', 'left')
            ->where('tbl_academic_history.student_id', $studentId)
            ->where('tbl_academic_history.final_status', 'Aprovado')
            ->orderBy('tbl_academic_years.end_date', 'DESC')
            ->first();
        
        if (!$lastHistory) {
            return redirect()->back()
                ->with('error', 'Aluno não possui nenhum ano concluído com aprovação');
        }
        
        $data['title'] = 'Certificado de Conclusão';
        $data['student'] = $student;
        $data['history'] = $lastHistory;
        
        return view('admin/academic-records/certificate', $data);
    }
    
/**
 * Exportar pauta para Excel
 */
public function export()
{
    $classId = $this->request->getGet('class');
    $semesterId = $this->request->getGet('semester');
    
    if (!$classId || !$semesterId) {
        return redirect()->back()
            ->with('error', 'Turma e semestre são obrigatórios para exportação');
    }
    
    // Buscar informações da turma
    $class = $this->classModel
        ->select('
            tbl_classes.*,
            tbl_grade_levels.level_name,
            tbl_academic_years.year_name
        ')
        ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
        ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_classes.academic_year_id')
        ->where('tbl_classes.id', $classId)
        ->first();
    
    if (!$class) {
        return redirect()->back()->with('error', 'Turma não encontrada');
    }
    
    $semester = $this->semesterModel->find($semesterId);
    if (!$semester) {
        return redirect()->back()->with('error', 'Semestre não encontrado');
    }
    
    // CORREÇÃO: Converter para objeto se for array
    $semesterObj = is_array($semester) ? (object)$semester : $semester;
    
    // Mapear semester_type para period_type
    $periodTypeMap = [
        '1º Trimestre' => '1º Semestre',
        '2º Trimestre' => '2º Semestre',
        '3º Trimestre' => 'Anual',
        '1º Semestre' => '1º Semestre',
        '2º Semestre' => '2º Semestre'
    ];
    
    $periodType = $periodTypeMap[$semesterObj->semester_type] ?? 'Anual';
    
    // Buscar disciplinas da turma para este período
    $classDisciplineModel = new \App\Models\ClassDisciplineModel();
    $disciplines = $classDisciplineModel
        ->select('
            tbl_disciplines.id,
            tbl_disciplines.discipline_name,
            tbl_disciplines.discipline_code
        ')
        ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
        ->where('tbl_class_disciplines.class_id', $classId)
        ->where('tbl_class_disciplines.is_active', 1)
        ->groupStart()
            ->where('tbl_class_disciplines.period_type', $periodType)
            ->orWhere('tbl_class_disciplines.period_type', 'Anual')
        ->groupEnd()
        ->orderBy('tbl_disciplines.discipline_name', 'ASC')
        ->findAll();
    
    // Buscar alunos e suas médias
    $students = $this->enrollmentModel
        ->select('
            tbl_enrollments.id as enrollment_id,
            tbl_students.student_number,
            tbl_users.first_name,
            tbl_users.last_name
        ')
        ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
        ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
        ->where('tbl_enrollments.class_id', $classId)
        ->where('tbl_enrollments.status', 'Ativo')
        ->orderBy('tbl_users.first_name', 'ASC')
        ->findAll();
    
    $data = [];
    foreach ($students as $student) {
        $row = [
            'student_number' => $student->student_number,
            'student_name' => $student->first_name . ' ' . $student->last_name
        ];
        
        foreach ($disciplines as $disc) {
            $avg = $this->disciplineAverageModel
                ->where('enrollment_id', $student->enrollment_id)
                ->where('discipline_id', $disc->id)
                ->where('semester_id', $semesterId)
                ->first();
            
            $row['disc_' . $disc->id] = $avg ? $avg->final_score : '';
        }
        
        $result = $this->semesterResultModel
            ->where('enrollment_id', $student->enrollment_id)
            ->where('semester_id', $semesterId)
            ->first();
        
        $row['average'] = $result ? $result->overall_average : 0;
        $row['status'] = $result ? $result->status : 'Em Andamento';
        
        $data[] = $row;
    }
    
    return $this->exportToExcel($class, $semesterObj, $disciplines, $data);
}
    /**
     * Exportar para Excel
     */
    private function exportToExcel($class, $semester, $disciplines, $data)
    {
        $excel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $excel->getActiveSheet();
        
        // Título
        $sheet->setCellValue('A1', 'Pauta de Avaliações - ' . $class->class_name);
        $sheet->setCellValue('A2', $semester->semester_name . ' - ' . $class->year_name);
        $sheet->mergeCells('A1:' . $this->getColumnLetter(3 + count($disciplines)) . '1');
        $sheet->mergeCells('A2:' . $this->getColumnLetter(3 + count($disciplines)) . '2');
        
        // Cabeçalhos
        $sheet->setCellValue('A4', 'Nº');
        $sheet->setCellValue('B4', 'Matrícula');
        $sheet->setCellValue('C4', 'Nome do Aluno');
        
        $col = 'D';
        foreach ($disciplines as $disc) {
            $sheet->setCellValue($col . '4', $disc->discipline_code);
            $col++;
        }
        $sheet->setCellValue($col . '4', 'Média');
        $col++;
        $sheet->setCellValue($col . '4', 'Resultado');
        
        // Dados
        $row = 5;
        foreach ($data as $index => $item) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $item['student_number']);
            $sheet->setCellValue('C' . $row, $item['student_name']);
            
            $col = 'D';
            foreach ($disciplines as $disc) {
                $sheet->setCellValue($col . $row, $item['disc_' . $disc->id] ?? '');
                $col++;
            }
            $sheet->setCellValue($col . $row, $item['average']);
            $col++;
            $sheet->setCellValue($col . $row, $item['status']);
            $row++;
        }
        
        // Estilo
        $sheet->getStyle('A1:' . $col . '4')->getFont()->setBold(true);
        $sheet->getStyle('A4:' . $col . ($row - 1))->getBorders()
            ->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);
        
        foreach (range('A', $col) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }
        
        $filename = 'pauta_' . $class->class_code . '_' . $semester->semester_name . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($excel);
        $writer->save('php://output');
        exit;
    }
    
    /**
     * Converter número da coluna para letra
     */
    private function getColumnLetter($columnNumber)
    {
        $letter = '';
        while ($columnNumber > 0) {
            $modulo = ($columnNumber - 1) % 26;
            $letter = chr(65 + $modulo) . $letter;
            $columnNumber = floor(($columnNumber - $modulo) / 26);
        }
        return $letter;
    }
    
    /**
     * Obtém estatísticas gerais
     */
    private function getGeneralStats($academicYearId = null)
    {
        $db = db_connect();
        
        $classStatsQuery = "
            SELECT 
                COUNT(DISTINCT c.id) as total_classes,
                SUM(CASE 
                    WHEN e.total_alunos > 0 AND e.alunos_com_resultado = e.total_alunos THEN 1 
                    ELSE 0 
                END) as finalized_classes,
                SUM(CASE 
                    WHEN e.total_alunos > 0 AND e.alunos_com_resultado < e.total_alunos THEN 1 
                    ELSE 0 
                END) as pending_classes,
                SUM(CASE 
                    WHEN e.total_alunos = 0 THEN 1 
                    ELSE 0 
                END) as empty_classes,
                AVG(CASE 
                    WHEN e.total_alunos > 0 THEN (e.alunos_com_resultado / e.total_alunos) * 100 
                    ELSE 0 
                END) as avg_progress
            FROM tbl_classes c
            LEFT JOIN (
                SELECT 
                    e.class_id,
                    COUNT(DISTINCT e.id) as total_alunos,
                    COUNT(DISTINCT CASE WHEN sr.id IS NOT NULL THEN e.id END) as alunos_com_resultado
                FROM tbl_enrollments e
                LEFT JOIN tbl_semester_results sr ON sr.enrollment_id = e.id
                WHERE e.status = 'Ativo'
                GROUP BY e.class_id
            ) e ON e.class_id = c.id
            WHERE c.is_active = 1
        ";
        
        $params = [];
        if ($academicYearId) {
            $classStatsQuery .= " AND c.academic_year_id = ?";
            $params[] = $academicYearId;
        }
        
        $classStats = $db->query($classStatsQuery, $params)->getRow();
        
        $studentQuery = "
            SELECT COUNT(*) as total
            FROM tbl_enrollments e
            JOIN tbl_classes c ON c.id = e.class_id
            WHERE e.status = 'Ativo' AND c.is_active = 1
        ";
        
        if ($academicYearId) {
            $studentQuery .= " AND c.academic_year_id = ?";
        }
        
        $totalStudents = $db->query($studentQuery, $params)->getRow()->total ?? 0;
        
        return [
            'total' => $classStats->total_classes ?? 0,
            'finalized' => $classStats->finalized_classes ?? 0,
            'pending' => $classStats->pending_classes ?? 0,
            'empty' => $classStats->empty_classes ?? 0,
            'students' => $totalStudents,
            'average_progress' => round($classStats->avg_progress ?? 0)
        ];
    }
    
    /**
     * Retorna o label do status da turma
     */
    private function getClassStatusLabel($class)
    {
        if ($class->total_alunos == 0) {
            return 'Sem Alunos';
        }
        
        if ($class->is_finalized) {
            return 'Finalizada';
        }
        
        if ($class->progress_percentage == 0) {
            return 'Não Iniciada';
        }
        
        if ($class->progress_percentage < 50) {
            return 'Em Andamento';
        }
        
        if ($class->progress_percentage < 100) {
            return 'Quase Pronta';
        }
        
        return 'Pendente';
    }
    
    /**
     * Retorna a cor do status da turma
     */
    private function getClassStatusColor($class)
    {
        if ($class->total_alunos == 0) {
            return 'secondary';
        }
        
        if ($class->is_finalized) {
            return 'success';
        }
        
        if ($class->progress_percentage == 0) {
            return 'danger';
        }
        
        if ($class->progress_percentage < 50) {
            return 'warning';
        }
        
        if ($class->progress_percentage < 100) {
            return 'info';
        }
        
        return 'primary';
    }
}