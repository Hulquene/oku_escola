<?php

// app/Controllers/admin/AcademicRecords.php
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
        helper('log'); // Para logs
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
        $currentYear = current_academic_year();
        
        // Se não selecionou ano, usa o atual como padrão
        if (!$academicYearId && $currentYear) {
            $academicYearId = $currentYear;
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
            $class['is_finalized'] = ($class['total_alunos'] > 0 && 
                                     $class['alunos_com_resultado'] == $class['total_alunos']);
            
            if ($class['total_alunos'] > 0) {
                $class['progress_percentage'] = round(($class['alunos_com_resultado'] / $class['total_alunos']) * 100);
            } else {
                $class['progress_percentage'] = 0;
                $class['is_finalized'] = false;
            }
            
            $class['status_label'] = $this->getClassStatusLabel($class);
            $class['status_color'] = $this->getClassStatusColor($class);
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
        
        log_view('academic_records', $academicYearId, 'Visualizou listagem de pautas');
        
        return view('admin/academic-records/index', $data);
    }
    
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
            log_error('Tentativa de acesso a turma inexistente', ['class_id' => $classId]);
            return redirect()->to('/admin/academic-records')
                ->with('error', 'Turma não encontrada');
        }
        
        // Buscar todas as disciplinas da turma
        $disciplines = $this->classDisciplineModel
            ->select('
                tbl_class_disciplines.*,
                tbl_disciplines.discipline_name,
                tbl_disciplines.discipline_code,
                tbl_disciplines.workload_hours
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
            ->where('tbl_class_disciplines.class_id', $classId)
            ->where('tbl_class_disciplines.is_active', 1)
            ->orderBy('tbl_disciplines.discipline_name', 'ASC')
            ->findAll();
        
        $data['disciplines'] = $disciplines;
        
        // Buscar alunos da turma
        $enrollments = $this->enrollmentModel
            ->select('
                tbl_enrollments.id as enrollment_id,
                tbl_enrollments.final_result,
                tbl_enrollments.final_average,
                tbl_enrollments.is_approved,
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
        
        // Estatísticas gerais
        $estatisticas = [
            'totalAlunos' => count($enrollments),
            'aprovados' => 0,
            'recurso' => 0,
            'reprovados' => 0,
            'pendentes' => 0,
            'mediaTurma' => 0,
            'taxaAprovacao' => 0,
            'aprovacaoPorDisciplina' => []
        ];
        
        // Inicializar estatísticas por disciplina
        foreach ($disciplines as $discipline) {
            $estatisticas['aprovacaoPorDisciplina'][$discipline['id']] = [
                'disciplina' => $discipline['discipline_name'],
                'codigo' => $discipline['discipline_code'],
                'total' => 0,
                'aprovados' => 0,
                'recurso' => 0,
                'reprovados' => 0,
                'media' => 0,
                'soma' => 0,
                'disciplinas_recurso' => []
            ];
        }
        
        // Processar cada aluno
        $students = [];
        $somaMedias = 0;
        $alunosComMedia = 0;
        
        foreach ($enrollments as $enrollment) {
            $student = (array)$enrollment;
            $student['disciplinas'] = [];
            $totalMFD = 0;
            $disciplinasCount = 0;
            $disciplinasRecurso = [];
            $disciplinasReprovadas = [];
            
            foreach ($disciplines as $discipline) {
                // Buscar notas da disciplina por trimestre
                $notas = $this->getDisciplineFinalScores($student['enrollment_id'], $discipline['id']);
                $student['disciplinas'][$discipline['id']] = $notas;
                
                if ($notas['mfd'] !== null && $notas['mfd'] > 0) {
                    $totalMFD += $notas['mfd'];
                    $disciplinasCount++;
                    
                    // Atualizar estatísticas por disciplina
                    $estatisticas['aprovacaoPorDisciplina'][$discipline['id']]['total']++;
                    $estatisticas['aprovacaoPorDisciplina'][$discipline['id']]['soma'] += $notas['mfd'];
                    
                    // Contar aprovações/reprovações por disciplina
                    if ($notas['mfd'] >= 10) {
                        $estatisticas['aprovacaoPorDisciplina'][$discipline['id']]['aprovados']++;
                    } elseif ($notas['mfd'] >= 7) {
                        $estatisticas['aprovacaoPorDisciplina'][$discipline['id']]['recurso']++;
                        $disciplinasRecurso[] = $discipline['discipline_name'];
                    } elseif ($notas['mfd'] > 0) {
                        $estatisticas['aprovacaoPorDisciplina'][$discipline['id']]['reprovados']++;
                        $disciplinasReprovadas[] = $discipline['discipline_name'];
                    }
                }
            }
            
            // Calcular média final do aluno (MFD geral)
            $student['media_final_geral'] = $disciplinasCount > 0 ? 
                round($totalMFD / $disciplinasCount, 2) : 0;
            
            // Determinar resultado final
            $student['resultado_final'] = $this->determinarResultadoFinal(
                $student['disciplinas'], 
                $student['media_final_geral'],
                $disciplinasReprovadas,
                $disciplinasRecurso
            );
            
            // Contar estatísticas
            if ($student['resultado_final']['transita']) {
                $estatisticas['aprovados']++;
            } elseif ($student['resultado_final']['recurso']) {
                $estatisticas['recurso']++;
            } elseif ($student['resultado_final']['reprovado']) {
                $estatisticas['reprovados']++;
            } else {
                $estatisticas['pendentes']++;
            }
            
            // Somar para média da turma
            if ($student['media_final_geral'] > 0) {
                $somaMedias += $student['media_final_geral'];
                $alunosComMedia++;
            }
            
            $students[] = $student;
        }
        
        // Calcular médias por disciplina
        foreach ($estatisticas['aprovacaoPorDisciplina'] as $id => $disc) {
            if ($disc['total'] > 0) {
                $estatisticas['aprovacaoPorDisciplina'][$id]['media'] = round($disc['soma'] / $disc['total'], 2);
            }
        }
        
        // Calcular média geral da turma
        $estatisticas['mediaTurma'] = $alunosComMedia > 0 ? 
            round($somaMedias / $alunosComMedia, 2) : 0;
        
        // Calcular taxa de aprovação
        $estatisticas['taxaAprovacao'] = $estatisticas['totalAlunos'] > 0 ? 
            round(($estatisticas['aprovados'] / $estatisticas['totalAlunos']) * 100, 1) : 0;
        
        $data['students'] = $students;
        $data['estatisticas'] = $estatisticas;
        
        log_view('academic_records_class', $classId, 'Visualizou pauta da turma: ' . $data['class']['class_name']);
        
        return view('admin/academic-records/class', $data);
    }
    
    /**
     * Buscar notas finais de uma disciplina para um aluno (versão corrigida)
     * @param int $enrollmentId ID da matrícula
     * @param int $disciplineId ID da disciplina
     * @return array Notas organizadas por trimestre/semestre
     */
    private function getDisciplineFinalScores($enrollmentId, $disciplineId)
    {
        // Buscar todos os resultados da disciplina
        $results = $this->examResultModel
            ->select('
                tbl_exam_results.*,
                tbl_exam_schedules.exam_date,
                tbl_exam_periods.semester_id,
                tbl_semesters.semester_type,
                tbl_semesters.semester_name,
                tbl_exam_boards.board_code,
                tbl_exam_boards.board_type,
                tbl_exam_boards.weight
            ')
            ->join('tbl_exam_schedules', 'tbl_exam_schedules.id = tbl_exam_results.exam_schedule_id')
            ->join('tbl_exam_periods', 'tbl_exam_periods.id = tbl_exam_schedules.exam_period_id')
            ->join('tbl_semesters', 'tbl_semesters.id = tbl_exam_periods.semester_id')
            ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exam_schedules.exam_board_id')
            ->where('tbl_exam_results.enrollment_id', $enrollmentId)
            ->where('tbl_exam_schedules.discipline_id', $disciplineId)
            ->where('tbl_exam_results.is_absent', 0)
            ->where('tbl_exam_results.score >', 0)
            ->orderBy('tbl_semesters.start_date', 'ASC')
            ->findAll();
        
        // Mapear tipos de período para números de trimestre/semestre
        $periodMap = [
            '1º Trimestre' => 1,
            '2º Trimestre' => 2,
            '3º Trimestre' => 3,
            '1º Semestre' => 1,
            '2º Semestre' => 2
        ];
        
        // Determinar quantos períodos existem
        $periodTypes = array_unique(array_column($results, 'semester_type'));
        $maxPeriods = 3; // padrão trimestres
        
        foreach ($periodTypes as $type) {
            if (strpos($type, 'Semestre') !== false) {
                $maxPeriods = 2; // sistema de semestres
                break;
            }
        }
        
        // Inicializar array de períodos
        $periodos = [];
        for ($i = 1; $i <= $maxPeriods; $i++) {
            $periodos[$i] = [
                'ac' => [],   // Avaliações Contínuas
                'npp' => [],  // Provas do Professor
                'npt' => [],  // Provas Trimestrais
                'exame' => null,
                'mt' => null  // Média do Trimestre
            ];
        }
        
        // Agrupar notas por período e tipo de avaliação
        foreach ($results as $result) {
            $periodo = $periodMap[$result['semester_type']] ?? 1;
            $boardCode = $result['board_code'];
            
            if (strpos($boardCode, 'AC') !== false || strpos($boardCode, 'MAC') !== false) {
                $periodos[$periodo]['ac'][] = $result['score'];
            } elseif (strpos($boardCode, 'NPP') !== false) {
                $periodos[$periodo]['npp'][] = $result['score'];
            } elseif (strpos($boardCode, 'NPT') !== false) {
                $periodos[$periodo]['npt'][] = $result['score'];
            } elseif (strpos($boardCode, 'E') !== false || strpos($boardCode, 'EX-FIN') !== false) {
                $periodos[$periodo]['exame'] = $result['score'];
            }
        }
        
        // Calcular médias por período
        $somaMT = 0;
        $periodosCompletos = 0;
        
        for ($i = 1; $i <= $maxPeriods; $i++) {
            $notasValidas = [];
            
            // Calcular médias para cada tipo
            $mediaAC = !empty($periodos[$i]['ac']) ? round(array_sum($periodos[$i]['ac']) / count($periodos[$i]['ac']), 1) : null;
            $mediaNPP = !empty($periodos[$i]['npp']) ? round(array_sum($periodos[$i]['npp']) / count($periodos[$i]['npp']), 1) : null;
            $mediaNPT = !empty($periodos[$i]['npt']) ? round(array_sum($periodos[$i]['npt']) / count($periodos[$i]['npt']), 1) : null;
            
            if ($mediaAC !== null) $notasValidas[] = $mediaAC;
            if ($mediaNPP !== null) $notasValidas[] = $mediaNPP;
            if ($mediaNPT !== null) $notasValidas[] = $mediaNPT;
            
            // Calcular Média do Trimestre (MT)
            if (count($notasValidas) >= 2) { // Pode ter AC + NPP + NPT
                $mt = round(array_sum($notasValidas) / count($notasValidas), 1);
                $periodos[$i]['mt'] = $mt;
                $somaMT += $mt;
                $periodosCompletos++;
            }
            
            $periodos[$i]['medias'] = [
                'ac' => $mediaAC,
                'npp' => $mediaNPP,
                'npt' => $mediaNPT
            ];
        }
        
        // Calcular Média Trimestral Definida (MTD)
        $mtd = $periodosCompletos > 0 ? round($somaMT / $periodosCompletos, 1) : null;
        
        // Verificar se tem exame final
        $exameFinal = null;
        foreach ($periodos as $p) {
            if ($p['exame'] !== null) {
                $exameFinal = $p['exame'];
                break;
            }
        }
        
        // Calcular Média Final Definida (MFD)
        $mfd = null;
        if ($mtd !== null && $exameFinal !== null) {
            $mfd = round(($mtd + $exameFinal) / 2, 1);
        } elseif ($mtd !== null) {
            $mfd = $mtd;
        } elseif ($exameFinal !== null) {
            $mfd = $exameFinal;
        }
        
        // Formatar retorno
        $resultado = [
            'mtd' => $mtd,
            'mfd' => $mfd,
            'exame_final' => $exameFinal,
            'periodos_completos' => $periodosCompletos
        ];
        
        for ($i = 1; $i <= $maxPeriods; $i++) {
            $resultado["periodo{$i}"] = [
                'ac' => $periodos[$i]['medias']['ac'],
                'npp' => $periodos[$i]['medias']['npp'],
                'npt' => $periodos[$i]['medias']['npt'],
                'mt' => $periodos[$i]['mt'],
                'exame' => $periodos[$i]['exame']
            ];
        }
        
        return $resultado;
    }
    
    /**
     * Determinar resultado final do aluno (versão unificada)
     * @param array $disciplinas Notas das disciplinas
     * @param float $mediaGeral Média geral do aluno
     * @param array $disciplinasReprovadas Lista de disciplinas reprovadas (<7)
     * @param array $disciplinasRecurso Lista de disciplinas em recurso (7-9.9)
     * @return array Resultado com flags e detalhes
     */
    private function determinarResultadoFinal($disciplinas, $mediaGeral = null, $disciplinasReprovadas = [], $disciplinasRecurso = [])
    {
        $resultado = [
            'transita' => true,
            'recurso' => false,
            'reprovado' => false,
            'pendente' => false,
            'disciplinas_recurso' => [],
            'disciplinas_reprovadas' => [],
            'total_disciplinas' => count($disciplinas),
            'media_geral' => $mediaGeral
        ];
        
        // Se não temos disciplinas, está pendente
        if (empty($disciplinas)) {
            $resultado['transita'] = false;
            $resultado['pendente'] = true;
            return $resultado;
        }
        
        foreach ($disciplinas as $discId => $notas) {
            if (isset($notas['mfd']) && $notas['mfd'] > 0) {
                if ($notas['mfd'] < 7) {
                    $resultado['transita'] = false;
                    $resultado['reprovado'] = true;
                    $resultado['disciplinas_reprovadas'][] = $discId;
                } elseif ($notas['mfd'] < 10) {
                    $resultado['transita'] = false;
                    $resultado['recurso'] = true;
                    $resultado['disciplinas_recurso'][] = $discId;
                }
            } else {
                $resultado['transita'] = false;
                $resultado['pendente'] = true;
            }
        }
        
        return $resultado;
    }
    
    // ... (resto dos métodos mantidos iguais) ...
    
    /**
     * Processar aprovação dos alunos (transita/não transita)
     */
    public function processApprovals($classId)
    {
        // Verificar permissão
        if (!has_permission('results.approve') && !is_admin()) {
            return redirect()->to('/admin/dashboard')
                ->with('error', 'Não tem permissão para aprovar resultados');
        }
        
        $data['title'] = 'Processar Aprovações - Pauta Final';
        
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
        
        // Buscar disciplinas da turma
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
        
        // Buscar alunos da turma com seus resultados
        $enrollments = $this->enrollmentModel
            ->select('
                tbl_enrollments.id as enrollment_id,
                tbl_enrollments.final_result,
                tbl_enrollments.final_average,
                tbl_enrollments.is_approved,
                tbl_enrollments.approved_at,
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
        
        // Converter para array para consistência
        $students = [];
        foreach ($enrollments as $enrollment) {
            $student = (array)$enrollment;
            $student['disciplinas'] = [];
            $totalMFD = 0;
            $disciplinasCount = 0;
            $disciplinasRecurso = [];
            $disciplinasReprovadas = [];
            
            foreach ($data['disciplines'] as $discipline) {
                // Usar o método corrigido
                $notas = $this->getDisciplineFinalScores($student['enrollment_id'], $discipline['id']);
                $student['disciplinas'][$discipline['id']] = $notas;
                
                if ($notas['mfd'] !== null) {
                    $totalMFD += $notas['mfd'];
                    $disciplinasCount++;
                    
                    if ($notas['mfd'] < 7) {
                        $disciplinasReprovadas[] = $discipline['discipline_name'];
                    } elseif ($notas['mfd'] < 10) {
                        $disciplinasRecurso[] = $discipline['discipline_name'];
                    }
                }
            }
            
            // Calcular média final geral
            $student['media_final_geral'] = $disciplinasCount > 0 ? 
                round($totalMFD / $disciplinasCount, 2) : 0;
            
            // Determinar resultado final - USANDO A MESMA FUNÇÃO
            $student['resultado_final'] = $this->determinarResultadoFinal(
                $student['disciplinas'], 
                $student['media_final_geral'],
                $disciplinasReprovadas,
                $disciplinasRecurso
            );
            
            // Verificar se já foi aprovado
            $student['ja_aprovado'] = ($student['is_approved'] == 1);
            
            $students[] = $student;
        }
        
        $data['students'] = $students;
        
        // Estatísticas da turma
        $total = count($students);
        $transitam = 0;
        $nao_transitam = 0;
        $recurso = 0;
        $pendentes = 0;
        
        foreach ($students as $s) {
            if ($s['resultado_final']['transita']) {
                $transitam++;
            } elseif ($s['resultado_final']['recurso']) {
                $recurso++;
            } elseif ($s['resultado_final']['reprovado']) {
                $nao_transitam++;
            } else {
                $pendentes++;
            }
        }
        
        $data['stats'] = [
            'total' => $total,
            'transitam' => $transitam,
            'recurso' => $recurso,
            'nao_transitam' => $nao_transitam,
            'pendentes' => $pendentes,
            'taxa_aprovacao' => $total > 0 ? round(($transitam / $total) * 100, 1) : 0
        ];
        
        return view('admin/academic-records/approvals', $data);
    }


    /**
     * Buscar notas de uma disciplina por trimestre
     * @param int $enrollmentId ID da matrícula
     * @param int $disciplineId ID da disciplina
     * @return array Notas organizadas por trimestre
     */
    private function getDisciplineTrimestralScores($enrollmentId, $disciplineId)
    {
        // Buscar resultados da disciplina para todos os períodos
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
        
        // Mapear tipos de período para números de trimestre/semestre
        // Suporta ambos: trimestres (1,2,3) e semestres (1,2)
        $periodMap = [
            '1º Trimestre' => 1,
            '2º Trimestre' => 2,
            '3º Trimestre' => 3,
            '1º Semestre' => 1,
            '2º Semestre' => 2
        ];
        
        // Determinar quantos períodos existem (3 para trimestres, 2 para semestres)
        $periodTypes = array_unique(array_column($results, 'semester_type'));
        $maxPeriods = 3; // padrão trimestres
        
        foreach ($periodTypes as $type) {
            if (strpos($type, 'Semestre') !== false) {
                $maxPeriods = 2; // sistema de semestres
                break;
            }
        }
        
        // Inicializar array de períodos
        $periodos = [];
        for ($i = 1; $i <= $maxPeriods; $i++) {
            $periodos[$i] = [
                'notas' => ['AC' => [], 'NPP' => [], 'NPT' => []],
                'mt' => null
            ];
        }
        
        // Agrupar notas por período e tipo de avaliação
        foreach ($results as $result) {
            $periodo = $periodMap[$result['semester_type']] ?? 1;
            
            // Garantir que o período existe no array
            if (!isset($periodos[$periodo])) {
                $periodos[$periodo] = ['notas' => ['AC' => [], 'NPP' => [], 'NPT' => []], 'mt' => null];
            }
            
            $boardCode = $result['board_code'];
            if (in_array($boardCode, ['AC', 'NPP', 'NPT'])) {
                $periodos[$periodo]['notas'][$boardCode][] = $result['score'];
            }
        }
        
        // Calcular médias por período
        $somaMT = 0;
        $periodosCompletos = 0;
        
        foreach ($periodos as $p => $dados) {
            $notasPeriodo = $dados['notas'];
            $notasValidas = [];
            
            // Calcular médias para cada tipo de avaliação
            $mediaAC = !empty($notasPeriodo['AC']) ? round(array_sum($notasPeriodo['AC']) / count($notasPeriodo['AC']), 1) : null;
            $mediaNPP = !empty($notasPeriodo['NPP']) ? round(array_sum($notasPeriodo['NPP']) / count($notasPeriodo['NPP']), 1) : null;
            $mediaNPT = !empty($notasPeriodo['NPT']) ? round(array_sum($notasPeriodo['NPT']) / count($notasPeriodo['NPT']), 1) : null;
            
            // Coletar notas válidas para cálculo da MT
            if ($mediaAC !== null) $notasValidas[] = $mediaAC;
            if ($mediaNPP !== null) $notasValidas[] = $mediaNPP;
            if ($mediaNPT !== null) $notasValidas[] = $mediaNPT;
            
            // Calcular MT (Média do Período) - precisa das 3 notas
            if (count($notasValidas) == 3) {
                $mt = round(array_sum($notasValidas) / 3, 1);
                $periodos[$p]['mt'] = $mt;
                $somaMT += $mt;
                $periodosCompletos++;
            } else {
                $periodos[$p]['mt'] = null;
            }
            
            // Armazenar médias individuais
            $periodos[$p]['media'] = [
                'ac' => $mediaAC,
                'npp' => $mediaNPP,
                'npt' => $mediaNPT
            ];
        }
        
        // Calcular MFD (Média Final da Disciplina) - apenas se todos os períodos estiverem completos
        $mfd = ($periodosCompletos == $maxPeriods) ? round($somaMT / $maxPeriods, 1) : null;
        
        // Formatar retorno
        $resultado = ['mfd' => $mfd];
        
        for ($i = 1; $i <= $maxPeriods; $i++) {
            $resultado["periodo{$i}"] = [
                'ac' => $periodos[$i]['media']['ac'] ?? null,
                'npp' => $periodos[$i]['media']['npp'] ?? null,
                'npt' => $periodos[$i]['media']['npt'] ?? null,
                'mt' => $periodos[$i]['mt'] ?? null
            ];
        }
        
        return $resultado;
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
            log_error('Tentativa de exportar pauta de turma inexistente', ['class_id' => $classId]);
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
                'nome' => $student['first_name'] . ' ' . $student['last_name'],
                'numero' => $student['student_number']
            ];
            
            foreach ($disciplines as $disc) {
                $notas = $this->getDisciplineTrimestralScores($student['enrollment_id'], $disc['id']);
                
                $row['disc_' . $disc['id'] . '_m1'] = $notas['periodo1']['mt'] ?? '';
                $row['disc_' . $disc['id'] . '_mt2'] = $notas['periodo2']['mt'] ?? '';
                $row['disc_' . $disc['id'] . '_mt3'] = $notas['periodo3']['mt'] ?? '';
                $row['disc_' . $disc['id'] . '_mfd'] = $notas['mfd'] ?? '';
            }
            
            $row['resultado'] = $this->determinarResultadoFinal([$student['enrollment_id'] => []]); // Simplificado
            
            $dados[] = $row;
        }
        
        log_export('pauta_final', "Exportou pauta final da turma: {$class['class_name']}", ['class_id' => $classId]);
        
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
        $sheet->setCellValue('A1', 'MAPA DE AVALIAÇÃO FINAL - ' . strtoupper($class['year_name']));
        $sheet->mergeCells('A1:' . $this->getColumnLetter(2 + (count($disciplines) * 4)) . '1');
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        
        // Informações
        $sheet->setCellValue('A2', 'Curso: ' . ($class['course_name'] ?? 'Ensino Geral'));
        $sheet->setCellValue('A3', 'Classe: ' . $class['level_name'] . ' - Turma: ' . $class['class_name']);
        
        // Cabeçalho
        $sheet->setCellValue('A5', 'Nº');
        $sheet->setCellValue('B5', 'NOME COMPLETO');
        
        $col = 'C';
        foreach ($disciplines as $disc) {
            $sheet->setCellValue($col . '5', $disc['discipline_name']);
            $sheet->mergeCells($col . '5:' . $this->getColumnLetter($this->getColumnNumber($col) + 3) . '5');
            
            $sheet->setCellValue($col . '6', 'M1');
            $col = $this->getNextColumn($col);
            $sheet->setCellValue($col . '6', 'M2');
            $col = $this->getNextColumn($col);
            $sheet->setCellValue($col . '6', 'M3');
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
                $sheet->setCellValue($col++ . $row, $item['disc_' . $disc['id'] . '_m1']);
                $sheet->setCellValue($col++ . $row, $item['disc_' . $disc['id'] . '_mt2']);
                $sheet->setCellValue($col++ . $row, $item['disc_' . $disc['id'] . '_mt3']);
                $sheet->setCellValue($col++ . $row, $item['disc_' . $disc['id'] . '_mfd']);
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
        $filename = 'pauta_final_' . $class['class_code'] . '_' . date('Ymd') . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    
    /**
     * Determinar resultado final conforme sistema angolano
     * @param array $disciplinas Notas por disciplina
     * @param float $mediaGeral Média geral do aluno
     * @param array $reprovadas Disciplinas reprovadas (<7)
     * @param array $recurso Disciplinas em recurso (7-9.9)
     * @return string Resultado final
     */
    private function determinarResultadoFinalAngolano($disciplinas, $mediaGeral, $reprovadas = [], $recurso = [])
    {
        if (empty($disciplinas)) {
            return 'Sem dados';
        }
        
        $disciplinasAprovadas = 0;
        $totalDisciplinas = count($disciplinas);
        
        foreach ($disciplinas as $discId => $notas) {
            if (isset($notas['mfd']) && $notas['mfd'] >= 10) {
                $disciplinasAprovadas++;
            }
        }
        
        // Critério 1: Aprovado em todas as disciplinas
        if ($disciplinasAprovadas == $totalDisciplinas) {
            return 'Transita';
        } 
        
        // Critério 2: Verificar possibilidade de recurso (máximo 2 disciplinas)
        $maxRecurso = 2; // Configurável depois via settings
        $disciplinasEmRecurso = count($recurso);
        
        if ($disciplinasEmRecurso > 0 && $disciplinasEmRecurso <= $maxRecurso && count($reprovadas) == 0) {
            return 'Recurso';
        }
        
        // Critério 3: Reprovado
        return 'Não Transita';
    }
    
    /**
     * Salvar aprovações em lote
     */
    public function saveApprovals()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false, 'message' => 'Requisição inválida']);
        }
        
        $classId = $this->request->getPost('class_id');
        $approvals = $this->request->getPost('approvals');
        
        if (!$classId || empty($approvals)) {
            log_error('Dados inválidos ao salvar aprovações', ['class_id' => $classId]);
            return $this->response->setJSON(['success' => false, 'message' => 'Dados inválidos']);
        }
        
        $db = db_connect();
        $db->transStart();
        
        $approved = 0;
        $failed = 0;
        $errors = [];
        
        foreach ($approvals as $enrollmentId => $data) {
            $updateData = [
                'final_result' => $data['resultado'],
                'final_average' => $data['media_final']
            ];
            
            if ($data['resultado'] == 'Transita') {
                $updateData['is_approved'] = 1;
                $updateData['approved_at'] = date('Y-m-d H:i:s');
                $updateData['approved_by'] = session()->get('user_id');
                $updateData['promotion_status'] = 'pending';
            } elseif ($data['resultado'] == 'Não Transita') {
                $updateData['is_approved'] = 2;
                $updateData['promotion_status'] = 'retained';
            } elseif ($data['resultado'] == 'Recurso') {
                $updateData['is_approved'] = 0;
                $updateData['promotion_status'] = 'pending';
            }
            
            if ($this->enrollmentModel->update($enrollmentId, $updateData)) {
                $approved++;
                
                // Se aprovado, criar histórico acadêmico
                if ($data['resultado'] == 'Transita') {
                    $this->createAcademicHistory($enrollmentId);
                }
            } else {
                $failed++;
                $errors[] = "Erro no aluno ID: {$enrollmentId}";
            }
        }
        
        $db->transComplete();
        
        if ($db->transStatus()) {
            log_action('update', "Salvou aprovações em lote: {$approved} aprovados, {$failed} falhas", $classId, 'class');
            
            return $this->response->setJSON([
                'success' => true,
                'message' => "{$approved} alunos processados com sucesso. {$failed} falhas.",
                'approved' => $approved,
                'failed' => $failed
            ]);
        } else {
            log_error('Erro ao processar aprovações', ['errors' => $errors]);
            
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao processar aprovações',
                'errors' => $errors
            ]);
        }
    }
    
    /**
     * Criar histórico acadêmico para aluno aprovado
     */
    private function createAcademicHistory($enrollmentId)
    {
        $enrollment = $this->enrollmentModel
            ->select('
                tbl_enrollments.*,
                tbl_classes.class_id,
                tbl_classes.class_name,
                tbl_classes.grade_level_id,
                tbl_grade_levels.level_name
            ')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
            ->where('tbl_enrollments.id', $enrollmentId)
            ->first();
        
        if (!$enrollment) {
            log_error('Erro ao criar histórico: matrícula não encontrada', ['enrollment_id' => $enrollmentId]);
            return false;
        }
        
        // Verificar se já existe histórico para este ano
        $existing = $this->academicHistoryModel
            ->where('student_id', $enrollment['student_id'])
            ->where('academic_year_id', $enrollment->academic_year_id)
            ->first();
        
        if ($existing) {
            return $existing->id;
        }
        
        $historyData = [
            'student_id' => $enrollment['student_id'],
            'academic_year_id' => $enrollment->academic_year_id,
            'class_id' => $enrollment['class_id'],
            'final_status' => $enrollment->final_result ?? 'Aprovado',
            'final_average' => $enrollment->final_average ?? 0,
            'observations' => 'Aprovado - Gerado automaticamente',
            'created_at' => date('Y-m-d H:i:s')
        ];
        
        $result = $this->academicHistoryModel->insert($historyData);
        
        if ($result) {
            log_insert('academic_history', $result, "Criou histórico acadêmico para aluno ID: {$enrollment['student_id']}");
        }
        
        return $result;
    }
    
    /**
     * Listar turmas disponíveis para processar aprovações
     */
    public function approvalClasses()
    {
        // Verificar permissão
        if (!has_permission('results.approve') && !is_admin()) {
            log_error('Tentativa de acesso sem permissão', ['method' => 'approvalClasses']);
            return redirect()->to('/admin/dashboard')
                ->with('error', 'Não tem permissão para aprovar resultados');
        }
        
        $data['title'] = 'Turmas para Aprovação';
        
        // Capturar filtros
        $academicYearId = $this->request->getGet('academic_year') ?? current_academic_year();
        
        // Dados para filtros
        $data['academicYears'] = $this->academicYearModel
            ->where('is_active', 1)
            ->orderBy('year_name', 'DESC')
            ->findAll();
        
        // Buscar turmas que têm alunos ativos
        $classes = $this->classModel
            ->select('
                tbl_classes.*,
                tbl_grade_levels.level_name,
                tbl_courses.course_name,
                tbl_academic_years.year_name,
                (
                    SELECT COUNT(*) 
                    FROM tbl_enrollments 
                    WHERE class_id = tbl_classes.id 
                    AND status = "Ativo"
                ) as total_alunos,
                (
                    SELECT COUNT(*) 
                    FROM tbl_enrollments 
                    WHERE class_id = tbl_classes.id 
                    AND status = "Ativo"
                    AND final_result IS NOT NULL
                ) as alunos_com_notas
            ')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
            ->join('tbl_courses', 'tbl_courses.id = tbl_classes.course_id', 'left')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_classes.academic_year_id')
            ->where('tbl_classes.academic_year_id', $academicYearId)
            ->where('tbl_classes.is_active', 1)
            ->having('total_alunos > 0')
            ->orderBy('tbl_grade_levels.sort_order', 'ASC')
            ->orderBy('tbl_classes.class_name', 'ASC')
            ->findAll();
        
        $data['classes'] = $classes;
        $data['selectedYear'] = $academicYearId;
        
        log_view('approval_classes', $academicYearId, 'Visualizou lista de turmas para aprovação');
        
        return view('admin/academic-records/approval_classes', $data);
    }

   /**
     * Funções auxiliares para Excel
     */
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
                ->where('tbl_semester_results.enrollment_id', $enrollment['id'])
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
                    $enrollment['id'],
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
                        ->where('academic_year_id', $academicyear['id'])
                        ->countAllResults();
                    
                    // Buscar semestres já calculados para este aluno
                    $calculatedSemesters = $this->semesterResultModel
                        ->where('enrollment_id', $enrollment['id'])
                        ->where('semester_id IN (SELECT id FROM tbl_semesters WHERE academic_year_id = ?)', [$academicyear['id']])
                        ->countAllResults();
                    
                    // Se todos os semestres foram calculados, gerar histórico anual
                    if ($calculatedSemesters == $totalSemesters) {
                        $this->generateYearlyHistory($enrollment['id'], $enrollment['student_id'], $academicYear->id);
                    }
                }
            } catch (\Exception $e) {
                $errors[] = "Erro ao processar aluno ID: {$enrollment['id']} - " . $e->getMessage();
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
            'class_id' => $enrollment['class_id'],
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
            'student_number' => $student['student_number'],
            'student_name' => $student['first_name'] . ' ' . $student['last_name']
        ];
        
        foreach ($disciplines as $disc) {
            $avg = $this->disciplineAverageModel
                ->where('enrollment_id', $student['enrollment_id'])
                ->where('discipline_id', $disc['id'])
                ->where('semester_id', $semesterId)
                ->first();
            
            $row['disc_' . $disc['id']] = $avg ? $avg->final_score : '';
        }
        
        $result = $this->semesterResultModel
            ->where('enrollment_id', $student['enrollment_id'])
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
        $sheet->setCellValue('A1', 'Pauta de Avaliações - ' . $class['class_name']);
        $sheet->setCellValue('A2', $semester['semester_name'] . ' - ' . $class['year_name']);
        $sheet->mergeCells('A1:' . $this->getColumnLetter(3 + count($disciplines)) . '1');
        $sheet->mergeCells('A2:' . $this->getColumnLetter(3 + count($disciplines)) . '2');
        
        // Cabeçalhos
        $sheet->setCellValue('A4', 'Nº');
        $sheet->setCellValue('B4', 'Matrícula');
        $sheet->setCellValue('C4', 'Nome do Aluno');
        
        $col = 'D';
        foreach ($disciplines as $disc) {
            $sheet->setCellValue($col . '4', $disc['discipline_code']);
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
                $sheet->setCellValue($col . $row, $item['disc_' . $disc['id']] ?? '');
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
        
        $filename = 'pauta_' . $class['class_code'] . '_' . $semester['semester_name'] . '.xlsx';
        
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($excel);
        $writer->save('php://output');
        exit;
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
        if ($class['total_alunos'] == 0) {
            return 'Sem Alunos';
        }
        
        if ($class['is_finalized']) {
            return 'Finalizada';
        }
        
        if ($class['progress_percentage'] == 0) {
            return 'Não Iniciada';
        }
        
        if ($class['progress_percentage'] < 50) {
            return 'Em Andamento';
        }
        
        if ($class['progress_percentage'] < 100) {
            return 'Quase Pronta';
        }
        
        return 'Pendente';
    }
    
    /**
     * Retorna a cor do status da turma
     */
    private function getClassStatusColor($class)
    {
        if ($class['total_alunos'] == 0) {
            return 'secondary';
        }
        
        if ($class['is_finalized']) {
            return 'success';
        }
        
        if ($class['progress_percentage'] == 0) {
            return 'danger';
        }
        
        if ($class['progress_percentage'] < 50) {
            return 'warning';
        }
        
        if ($class['progress_percentage'] < 100) {
            return 'info';
        }
        
        return 'primary';
    }


    /**
     * Processar progressão dos alunos aprovados para o próximo ano letivo
     */
    public function promoteStudents()
    {
        if (!has_permission('results.approve') && !is_admin()) {
            return redirect()->to('/admin/dashboard')
                ->with('error', 'Não tem permissão para processar progressão');
        }
        
        $academicYearId = $this->request->getPost('academic_year_id');
        $nextAcademicYearId = $this->request->getPost('next_academic_year_id');
        
        if (!$academicYearId || !$nextAcademicYearId) {
            return redirect()->back()
                ->with('error', 'Anos letivos não especificados');
        }
        
        // Verificar se o próximo ano letivo existe
        $nextYear = $this->academicYearModel->find($nextAcademicYearId);
        if (!$nextYear) {
            return redirect()->back()
                ->with('error', 'Ano letivo de destino não encontrado');
        }
        
        // Buscar alunos aprovados no ano letivo atual
        $approvedStudents = $this->enrollmentModel
            ->select('
                tbl_enrollments.*,
                tbl_classes.class_id,
                tbl_classes.class_name,
                tbl_classes.grade_level_id,
                tbl_classes.class_shift,
                tbl_grade_levels.level_name,
                tbl_grade_levels.sort_order
            ')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
            ->where('tbl_enrollments.academic_year_id', $academicYearId)
            ->where('tbl_enrollments.status', 'Ativo')
            ->where('tbl_enrollments.final_result', 'Aprovado')
            ->where('tbl_enrollments.is_approved', 1)
            ->where('tbl_enrollments.promotion_status', 'pending')
            ->findAll();
        
        if (empty($approvedStudents)) {
            return redirect()->back()
                ->with('warning', 'Nenhum aluno aprovado encontrado para progressão');
        }
        
        $db = db_connect();
        $db->transStart();
        
        $promotedCount = 0;
        $retainedCount = 0;
        $graduatedCount = 0;
        $errors = [];
        
        foreach ($approvedStudents as $enrollment) {
            // Buscar próximo nível
            $nextLevel = $this->gradeLevelModel->getNextLevel($enrollment['grade_level_id']);
            
            // Se não há próximo nível (aluno concluiu o ciclo)
            if (!$nextLevel) {
                // Marcar como graduado
                $this->enrollmentModel->update($enrollment['id'], [
                    'status' => 'Concluído',
                    'final_result' => 'Aprovado',
                    'promotion_status' => 'graduated'
                ]);
                
                $graduatedCount++;
                continue;
            }
            
            // Verificar se o próximo nível faz parte de um curso
            $isCourseLevel = $this->gradeLevelModel->isCourseLevel($nextLevel->id);
            
            // Buscar turma para o próximo nível
            $nextClass = $this->classModel
                ->select('tbl_classes.*, tbl_grade_levels.level_name')
                ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
                ->where('tbl_classes.grade_level_id', $nextLevel->id)
                ->where('tbl_classes.academic_year_id', $nextAcademicYearId)
                ->where('tbl_classes.class_shift', $enrollment->class_shift)
                ->first();
            
            // Se não encontrar turma, tentar sem turno específico
            if (!$nextClass) {
                $nextClass = $this->classModel
                    ->select('tbl_classes.*, tbl_grade_levels.level_name')
                    ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
                    ->where('tbl_classes.grade_level_id', $nextLevel->id)
                    ->where('tbl_classes.academic_year_id', $nextAcademicYearId)
                    ->first();
            }
            
            // Se ainda não encontrou turma, marcar como pendente
            if (!$nextClass) {
                $this->enrollmentModel->update($enrollment['id'], [
                    'promotion_status' => 'pending'
                ]);
                $errors[] = "Aluno {$enrollment['first_name']} {$enrollment['last_name']}: Nenhuma turma encontrada para o próximo nível";
                $retainedCount++;
                continue;
            }
            
            // Verificar vagas
            $availableSeats = $this->classModel->getAvailableSeats($nextClass->id);
            if ($availableSeats <= 0) {
                $this->enrollmentModel->update($enrollment['id'], [
                    'promotion_status' => 'pending'
                ]);
                $errors[] = "Aluno {$enrollment['first_name']} {$enrollment['last_name']}: Turma sem vagas";
                $retainedCount++;
                continue;
            }
            
            // Criar nova matrícula para o próximo ano
            $newEnrollmentData = [
                'student_id' => $enrollment['student_id'],
                'class_id' => $nextClass->id,
                'academic_year_id' => $nextAcademicYearId,
                'grade_level_id' => $nextLevel->id,
                'course_id' => $isCourseLevel ? $enrollment['course_id'] : null,
                'enrollment_date' => date('Y-m-d'),
                'enrollment_number' => $this->enrollmentModel->generateEnrollmentNumber(),
                'enrollment_type' => 'Renovação',
                'status' => 'Pendente', // Pendente para aprovação manual
                'previous_class_id' => $enrollment['class_id'],
                'previous_grade_id' => $enrollment['grade_level_id'],
                'created_by' => session()->get('user_id')
            ];
            
            $newEnrollmentId = $this->enrollmentModel->insert($newEnrollmentData);
            
            if ($newEnrollmentId) {
                // Atualizar matrícula antiga
                $this->enrollmentModel->update($enrollment['id'], [
                    'status' => 'Concluído',
                    'promoted_to_enrollment_id' => $newEnrollmentId,
                    'promotion_status' => 'promoted'
                ]);
                
                $promotedCount++;
            } else {
                $errors[] = "Erro ao criar matrícula para aluno {$enrollment['first_name']} {$enrollment['last_name']}";
                $retainedCount++;
            }
        }
        
        $db->transComplete();
        
        if ($db->transStatus()) {
            $message = "Progressão concluída: {$promotedCount} promovidos, {$graduatedCount} graduados, {$retainedCount} retidos.";
            if (!empty($errors)) {
                $message .= " Erros: " . implode('; ', array_slice($errors, 0, 5));
            }
            
            return redirect()->to('/admin/academic-records/promotion-results')
                ->with('success', $message);
        } else {
            return redirect()->back()
                ->with('error', 'Erro ao processar progressão');
        }
    }
    
    /**
     * Página de resultados da progressão
     */
    public function promotionResults()
    {
        $data['title'] = 'Resultados da Progressão';
        
        $academicYearId = $this->request->getGet('academic_year') ?? current_academic_year();
        
        $data['academicYearId'] = $academicYearId;
        
        // Buscar alunos que foram promovidos
        $promotedQuery = $this->enrollmentModel
            ->select('
                tbl_enrollments.*,
                tbl_students.student_number,
                tbl_users.first_name,
                tbl_users.last_name,
                tbl_old.class_name as old_class,
                tbl_old.class_code as old_class_code,
                tbl_new.class_name as new_class,
                tbl_new.class_code as new_class_code,
                tbl_new_enroll.enrollment_number as new_enrollment_number
            ')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->join('tbl_classes as tbl_old', 'tbl_old.id = tbl_enrollments.class_id')
            ->join('tbl_enrollments as tbl_new_enroll', 'tbl_new_enroll.id = tbl_enrollments.promoted_to_enrollment_id', 'left')
            ->join('tbl_classes as tbl_new', 'tbl_new.id = tbl_new_enroll.class_id', 'left')
            ->where('tbl_enrollments.promotion_status', 'promoted')
            ->where('tbl_enrollments.academic_year_id', $academicYearId)
            ->orderBy('tbl_users.first_name', 'ASC');
        
        $data['promoted'] = $promotedQuery->findAll();
        
        // Buscar alunos que graduaram
        $data['graduated'] = $this->enrollmentModel
            ->select('
                tbl_enrollments.*,
                tbl_students.student_number,
                tbl_users.first_name,
                tbl_users.last_name,
                tbl_classes.class_name,
                tbl_classes.class_code
            ')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
            ->where('tbl_enrollments.promotion_status', 'graduated')
            ->where('tbl_enrollments.academic_year_id', $academicYearId)
            ->orderBy('tbl_users.first_name', 'ASC')
            ->findAll();
        
        // Buscar alunos retidos
        $data['retained'] = $this->enrollmentModel
            ->select('
                tbl_enrollments.*,
                tbl_students.student_number,
                tbl_users.first_name,
                tbl_users.last_name,
                tbl_classes.class_name,
                tbl_classes.class_code
            ')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
            ->where('tbl_enrollments.promotion_status', 'retained')
            ->where('tbl_enrollments.academic_year_id', $academicYearId)
            ->orderBy('tbl_users.first_name', 'ASC')
            ->findAll();
        
        // Buscar alunos pendentes
        $data['pending'] = $this->enrollmentModel
            ->select('
                tbl_enrollments.*,
                tbl_students.student_number,
                tbl_users.first_name,
                tbl_users.last_name,
                tbl_classes.class_name,
                tbl_classes.class_code
            ')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
            ->where('tbl_enrollments.promotion_status', 'pending')
            ->where('tbl_enrollments.status', 'Ativo')
            ->where('tbl_enrollments.academic_year_id', $academicYearId)
            ->where('tbl_enrollments.final_result', 'Aprovado')
            ->orderBy('tbl_users.first_name', 'ASC')
            ->findAll();
        
        return view('admin/academic-records/promotion_results', $data);
    }
    /**
     * Verificar se aluno pode ser matriculado no próximo ano
     */
    public function checkEligibility($studentId, $nextAcademicYearId)
    {
        // Buscar matrícula do ano anterior
        $previousYear = $this->academicYearModel->getPreviousYear($nextAcademicYearId);
        
        if (!$previousYear) {
            return $this->response->setJSON([
                'eligible' => true,
                'message' => 'Primeiro ano letivo - pode matricular'
            ]);
        }
        
        $previousEnrollment = $this->enrollmentModel
            ->where('student_id', $studentId)
            ->where('academic_year_id', $previousYear->id)
            ->where('status', 'Ativo')
            ->first();
        
        // Se não tem matrícula no ano anterior (novo aluno)
        if (!$previousEnrollment) {
            return $this->response->setJSON([
                'eligible' => true,
                'message' => 'Novo aluno - pode matricular'
            ]);
        }
        
        // Verificar se foi aprovado
        if ($previousEnrollment->final_result == 'Aprovado') {
            return $this->response->setJSON([
                'eligible' => true,
                'message' => 'Aluno aprovado - pode matricular'
            ]);
        } elseif ($previousEnrollment->final_result == 'Reprovado') {
            return $this->response->setJSON([
                'eligible' => false,
                'message' => 'Aluno reprovado no ano anterior - não pode matricular'
            ]);
        } elseif ($previousEnrollment->final_result == 'Recurso') {
            return $this->response->setJSON([
                'eligible' => false,
                'message' => 'Aluno em recurso - aguardando resultados'
            ]);
        } else {
            return $this->response->setJSON([
                'eligible' => false,
                'message' => 'Resultados do ano anterior não processados'
            ]);
        }
    }

}