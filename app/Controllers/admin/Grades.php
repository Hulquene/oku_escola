<?php
// app/Controllers/admin/Grades.php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\ContinuousAssessmentModel;
use App\Models\ClassModel;
use App\Models\DisciplineModel;
use App\Models\EnrollmentModel;
use App\Models\StudentModel;
use App\Models\SemesterModel;
use App\Models\AcademicYearModel;

class Grades extends BaseController
{
    protected $continuousModel;
    protected $classModel;
    protected $disciplineModel;
    protected $enrollmentModel;
    protected $studentModel;
    protected $semesterModel;
    protected $academicYearModel;
    
    public function __construct()
    {
        $this->continuousModel = new ContinuousAssessmentModel();
        $this->classModel = new ClassModel();
        $this->disciplineModel = new DisciplineModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->studentModel = new StudentModel();
        $this->semesterModel = new SemesterModel();
        $this->academicYearModel = new AcademicYearModel();
    }
    
    /**
     * Visão geral das notas por turma
     */
    public function index()
    {
        $data['title'] = 'Notas e Avaliações';
        
        // Filtros
        $academicYearId = $this->request->getGet('academic_year');
        $classId = $this->request->getGet('class');
        
        // Buscar turmas
        $builder = $this->classModel
            ->select('tbl_classes.*, tbl_grade_levels.level_name, tbl_academic_years.year_name')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_classes.academic_year_id')
            ->where('tbl_classes.is_active', 1);
        
        if ($academicYearId) {
            $builder->where('tbl_classes.academic_year_id', $academicYearId);
        }
        
        $data['classes'] = $builder->orderBy('tbl_classes.class_name', 'ASC')->findAll();
        
        // Se selecionou uma turma, buscar notas
        if ($classId) {
            $data['class'] = $this->classModel->getWithTeacher($classId);
            $data['disciplines'] = $this->disciplineModel->getByClass($classId);
            
            $currentSemester = $this->semesterModel->getCurrent();
            $data['currentSemester'] = $currentSemester;
            
            // Buscar alunos com notas
            $students = $this->enrollmentModel
                ->select('tbl_enrollments.id as enrollment_id, tbl_students.id, tbl_users.first_name, tbl_users.last_name, tbl_students.student_number')
                ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
                ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
                ->where('tbl_enrollments.class_id', $classId)
                ->where('tbl_enrollments.status', 'Ativo')
                ->orderBy('tbl_users.first_name', 'ASC')
                ->findAll();
            
            foreach ($students as $student) {
                foreach ($data['disciplines'] as $discipline) {
                    $assessments = $this->continuousModel
                        ->where('enrollment_id', $student->enrollment_id)
                        ->where('discipline_id', $discipline->id)
                        ->where('semester_id', $currentSemester->id ?? null)
                        ->findAll();
                    
                    $scores = [];
                    foreach ($assessments as $a) {
                        $scores[$a->assessment_type] = $a->score;
                    }
                    
                    $student->grades[$discipline->id]['ac1'] = $scores['AC1'] ?? null;
                    $student->grades[$discipline->id]['ac2'] = $scores['AC2'] ?? null;
                    $student->grades[$discipline->id]['ac3'] = $scores['AC3'] ?? null;
                }
            }
            
            $data['students'] = $students;
        }
        
        // Dados para filtros
        $data['academicYears'] = $this->academicYearModel->where('is_active', 1)->findAll();
        $data['selectedYear'] = $academicYearId;
        $data['selectedClass'] = $classId;
        
        return view('admin/grades/index', $data);
    }
    
    /**
     * Notas por turma (detalhado)
     */
    public function class($classId)
    {
        $data['title'] = 'Notas da Turma';
        
        // Buscar informações da turma
        $data['class'] = $this->classModel->getWithTeacher($classId);
        
        if (!$data['class']) {
            return redirect()->to('/admin/grades')->with('error', 'Turma não encontrada');
        }
        
        // Filtros adicionais
        $selectedDiscipline = $this->request->getGet('discipline');
        $selectedSemester = $this->request->getGet('semester');
        $selectedStatus = $this->request->getGet('status');
        
        // Buscar disciplinas da turma
        $data['disciplines'] = $this->disciplineModel->getByClass($classId);
        
        // Buscar semestres do ano letivo
        $data['semesters'] = $this->semesterModel
            ->where('academic_year_id', $data['class']->academic_year_id)
            ->where('is_active', 1)
            ->orderBy('start_date', 'ASC')
            ->findAll();
        
        // Buscar alunos da turma
        $students = $this->enrollmentModel
            ->select('tbl_enrollments.id as enrollment_id, tbl_students.id, tbl_users.first_name, tbl_users.last_name, tbl_students.student_number')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->where('tbl_enrollments.class_id', $classId)
            ->where('tbl_enrollments.status', 'Ativo')
            ->orderBy('tbl_users.first_name', 'ASC')
            ->findAll();
        
        // Buscar notas por semestre
        $semesterId = $selectedSemester ?: ($this->semesterModel->getCurrent()->id ?? null);
        
        foreach ($students as $student) {
            foreach ($data['disciplines'] as $discipline) {
                $assessments = $this->continuousModel
                    ->where('enrollment_id', $student->enrollment_id)
                    ->where('discipline_id', $discipline->id)
                    ->where('semester_id', $semesterId)
                    ->findAll();
                
                $scores = [];
                foreach ($assessments as $a) {
                    $scores[$a->assessment_type] = $a->score;
                }
                
                $student->grades[$discipline->id]['ac1'] = $scores['AC1'] ?? null;
                $student->grades[$discipline->id]['ac2'] = $scores['AC2'] ?? null;
                $student->grades[$discipline->id]['ac3'] = $scores['AC3'] ?? null;
            }
        }
        
        $data['students'] = $students;
        $data['selectedDiscipline'] = $selectedDiscipline;
        $data['selectedSemester'] = $semesterId;
        $data['selectedStatus'] = $selectedStatus;
        
        return view('admin/grades/class', $data);
    }
    
    /**
     * Notas por aluno (histórico completo)
     */
    public function student($studentId)
    {
        $data['title'] = 'Histórico de Notas do Aluno';
        
        $student = $this->studentModel->getWithUser($studentId);
        if (!$student) {
            return redirect()->back()->with('error', 'Aluno não encontrado');
        }
        
        $data['student'] = $student;
        
        // Buscar todas matrículas do aluno
        $enrollments = $this->enrollmentModel
            ->select('
                tbl_enrollments.*,
                tbl_classes.class_name,
                tbl_academic_years.year_name,
                tbl_grade_levels.level_name
            ')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_enrollments.academic_year_id')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_enrollments.grade_level_id')
            ->where('tbl_enrollments.student_id', $studentId)
            ->orderBy('tbl_academic_years.start_date', 'DESC')
            ->findAll();
        
        foreach ($enrollments as $enrollment) {
            $disciplines = $this->disciplineModel->getByClass($enrollment->class_id);
            
            foreach ($disciplines as $discipline) {
                $semesters = $this->semesterModel
                    ->where('academic_year_id', $enrollment->academic_year_id)
                    ->findAll();
                
                foreach ($semesters as $semester) {
                    $assessments = $this->continuousModel
                        ->where('enrollment_id', $enrollment->id)
                        ->where('discipline_id', $discipline->id)
                        ->where('semester_id', $semester->id)
                        ->findAll();
                    
                    $enrollment->grades[$discipline->id][$semester->id] = $assessments;
                }
            }
        }
        
        $data['enrollments'] = $enrollments;
        
        return view('admin/grades/student', $data);
    }
    
    /**
     * Relatórios consolidados
     */
    public function report()
    {
        $data['title'] = 'Relatórios de Notas';
        
        // Estatísticas gerais (você pode implementar a lógica aqui)
        $data['totalAlunos'] = $this->studentModel->countAll();
        $data['aprovados'] = 0; // Implementar lógica
        $data['recurso'] = 0;    // Implementar lógica
        $data['reprovados'] = 0; // Implementar lógica
        
        $data['academicYears'] = $this->academicYearModel->where('is_active', 1)->findAll();
        
        return view('admin/grades/report', $data);
    }
    
    /**
     * Exportar notas para Excel (opcional)
     */
    public function export($classId = null)
    {
        if (!$classId) {
            return redirect()->back()->with('error', 'Selecione uma turma para exportar');
        }
        
        // Implementar lógica de exportação
        // Pode usar a biblioteca PhpSpreadsheet
        
        return redirect()->back()->with('info', 'Funcionalidade em desenvolvimento');
    }
    /**
     * Estatísticas gerais de notas
     */
    /**
     /**
 * Estatísticas gerais de notas
 */
public function statistics()
{
    $data['title'] = 'Estatísticas de Notas';
    
    // Filtros
    $academicYearId = $this->request->getGet('academic_year');
    $courseId = $this->request->getGet('course');
    
    // Dados para filtros
    $data['academicYears'] = $this->academicYearModel->where('is_active', 1)->findAll();
    $data['cursos'] = model('App\Models\CourseModel')->where('is_active', 1)->findAll();
    $data['selectedYear'] = $academicYearId;
    $data['selectedCourse'] = $courseId;
    
    // Estatísticas básicas
    $data['totalAlunos'] = $this->studentModel->countAll();
    $data['totalTurmas'] = $this->classModel->where('is_active', 1)->countAllResults();
    $data['totalDisciplinas'] = $this->disciplineModel->where('is_active', 1)->countAllResults();
    
    // Buscar turmas com estatísticas
    $builder = $this->classModel
        ->select('
            tbl_classes.id,
            tbl_classes.class_name,
            tbl_classes.class_code,
            tbl_academic_years.year_name,
            tbl_academic_years.id as academic_year_id,
            COUNT(DISTINCT tbl_enrollments.student_id) as total_alunos
        ')
        ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_classes.academic_year_id')
        ->join('tbl_enrollments', 'tbl_enrollments.class_id = tbl_classes.id', 'left')
        ->where('tbl_classes.is_active', 1)
        ->groupBy('tbl_classes.id');
    
    if ($academicYearId) {
        $builder->where('tbl_classes.academic_year_id', $academicYearId);
    }
    
    if ($courseId) {
        $builder->where('tbl_classes.course_id', $courseId);
    }
    
    $turmas = $builder->orderBy('tbl_classes.class_name', 'ASC')->findAll();
    
    // Calcular estatísticas REAIS para cada turma
    $chartLabels = [];
    $chartData = [];
    $totalAprovados = 0;
    $totalRecurso = 0;
    $totalReprovados = 0;
    $totalAlunosComNota = 0;
    
    foreach ($turmas as $turma) {
        // Buscar alunos da turma com suas notas
        $alunos = $this->enrollmentModel
            ->select('
                tbl_enrollments.id as enrollment_id,
                tbl_students.id as student_id
            ')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->where('tbl_enrollments.class_id', $turma->id)
            ->where('tbl_enrollments.status', 'Ativo')
            ->findAll();
        
        $totalAlunosTurma = count($alunos);
        $aprovadosTurma = 0;
        $recursoTurma = 0;
        $reprovadosTurma = 0;
        $somaMedias = 0;
        $alunosComMedia = 0;
        
        // Buscar semestre atual
        $currentSemester = $this->semesterModel->getCurrent();
        $semesterId = $currentSemester ? $currentSemester->id : null;
        
        foreach ($alunos as $aluno) {
            // Buscar disciplinas da turma
            $disciplinas = $this->disciplineModel->getByClass($turma->id);
            
            $somaNotasAluno = 0;
            $disciplinasComNota = 0;
            
            foreach ($disciplinas as $disciplina) {
                // Buscar notas do aluno nesta disciplina
                $notas = $this->continuousModel
                    ->select('score')
                    ->where('enrollment_id', $aluno->enrollment_id)
                    ->where('discipline_id', $disciplina->id)
                    ->where('semester_id', $semesterId)
                    ->findAll();
                
                if (!empty($notas)) {
                    $scores = array_column($notas, 'score');
                    $mediaDisciplina = array_sum($scores) / count($scores);
                    $somaNotasAluno += $mediaDisciplina;
                    $disciplinasComNota++;
                }
            }
            
            if ($disciplinasComNota > 0) {
                $mediaAluno = $somaNotasAluno / $disciplinasComNota;
                $somaMedias += $mediaAluno;
                $alunosComMedia++;
                
                // Classificar aluno
                if ($mediaAluno >= 10) {
                    $aprovadosTurma++;
                    $totalAprovados++;
                } elseif ($mediaAluno >= 7) {
                    $recursoTurma++;
                    $totalRecurso++;
                } else {
                    $reprovadosTurma++;
                    $totalReprovados++;
                }
            }
        }
        
        // Calcular média da turma
        $turma->aprovados = $aprovadosTurma;
        $turma->recurso = $recursoTurma;
        $turma->reprovados = $reprovadosTurma;
        $turma->media = $alunosComMedia > 0 ? round($somaMedias / $alunosComMedia, 1) : 0;
        $turma->total_alunos = $totalAlunosTurma;
        
        $chartLabels[] = $turma->class_name;
        $chartData[] = $turma->media;
    }
    
    $data['turmas'] = $turmas;
    $data['chartLabels'] = $chartLabels;
    $data['chartData'] = $chartData;
    $data['aprovados'] = $totalAprovados;
    $data['recurso'] = $totalRecurso;
    $data['reprovados'] = $totalReprovados;
    
    // Média geral
    $data['mediaGeral'] = count($chartData) > 0 
        ? round(array_sum($chartData) / count($chartData), 1) 
        : 0;
    
    // Log para debug
    log_message('info', 'Estatísticas calculadas: ' . json_encode([
        'total_turmas' => count($turmas),
        'total_aprovados' => $totalAprovados,
        'total_recurso' => $totalRecurso,
        'total_reprovados' => $totalReprovados,
        'media_geral' => $data['mediaGeral']
    ]));
    
    return view('admin/grades/statistics', $data);
}
/**
 * Relatório por período (semestre/trimestre)
 */
public function period()
{
    $academicYearId = $this->request->getGet('academic_year');
    $semesterId = $this->request->getGet('semester');
    
    if (!$academicYearId || !$semesterId) {
        return redirect()->to('/admin/grades/report')
            ->with('error', 'Selecione o ano letivo e o período');
    }
    
    // Buscar informações do período
    $semester = $this->semesterModel->find($semesterId);
    $academicYear = $this->academicYearModel->find($academicYearId);
    
    if (!$semester || !$academicYear) {
        return redirect()->to('/admin/grades/report')
            ->with('error', 'Período ou ano letivo não encontrado');
    }
    
    $data['title'] = 'Relatório por Período - ' . $semester->semester_name;
    $data['semester'] = $semester;
    $data['academicYear'] = $academicYear;
    
    // Buscar todas as turmas do ano letivo
    $turmas = $this->classModel
        ->select('
            tbl_classes.id,
            tbl_classes.class_name,
            tbl_classes.class_code
        ')
        ->where('tbl_classes.academic_year_id', $academicYearId)
        ->where('tbl_classes.is_active', 1)
        ->orderBy('tbl_classes.class_name', 'ASC')
        ->findAll();
    
    $relatorio = [];
    $totalAprovados = 0;
    $totalRecurso = 0;
    $totalReprovados = 0;
    $totalAlunos = 0;
    
    foreach ($turmas as $turma) {
        // Buscar alunos da turma
        $alunos = $this->enrollmentModel
            ->select('
                tbl_enrollments.id as enrollment_id,
                tbl_students.id as student_id,
                tbl_users.first_name,
                tbl_users.last_name,
                tbl_students.student_number
            ')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->where('tbl_enrollments.class_id', $turma->id)
            ->where('tbl_enrollments.status', 'Ativo')
            ->findAll();
        
        $dadosTurma = [
            'turma' => $turma,
            'alunos' => [],
            'total_alunos' => count($alunos),
            'aprovados' => 0,
            'recurso' => 0,
            'reprovados' => 0,
            'media_turma' => 0
        ];
        
        $somaMedias = 0;
        $alunosComMedia = 0;
        
        foreach ($alunos as $aluno) {
            // Buscar disciplinas da turma
            $disciplinas = $this->disciplineModel->getByClass($turma->id);
            
            $somaNotasAluno = 0;
            $disciplinasComNota = 0;
            $notasPorDisciplina = [];
            
            foreach ($disciplinas as $disciplina) {
                $notas = $this->continuousModel
                    ->select('score')
                    ->where('enrollment_id', $aluno->enrollment_id)
                    ->where('discipline_id', $disciplina->id)
                    ->where('semester_id', $semesterId)
                    ->findAll();
                
                if (!empty($notas)) {
                    $scores = array_column($notas, 'score');
                    $mediaDisciplina = array_sum($scores) / count($scores);
                    $somaNotasAluno += $mediaDisciplina;
                    $disciplinasComNota++;
                    
                    $notasPorDisciplina[$disciplina->id] = [
                        'disciplina' => $disciplina->discipline_name,
                        'media' => round($mediaDisciplina, 1)
                    ];
                }
            }
            
            $alunoInfo = [
                'nome' => $aluno->first_name . ' ' . $aluno->last_name,
                'numero' => $aluno->student_number,
                'notas' => $notasPorDisciplina
            ];
            
            if ($disciplinasComNota > 0) {
                $mediaAluno = $somaNotasAluno / $disciplinasComNota;
                $alunoInfo['media'] = round($mediaAluno, 1);
                $somaMedias += $mediaAluno;
                $alunosComMedia++;
                
                // Classificar aluno
                if ($mediaAluno >= 10) {
                    $alunoInfo['status'] = 'Aprovado';
                    $dadosTurma['aprovados']++;
                    $totalAprovados++;
                } elseif ($mediaAluno >= 7) {
                    $alunoInfo['status'] = 'Recurso';
                    $dadosTurma['recurso']++;
                    $totalRecurso++;
                } else {
                    $alunoInfo['status'] = 'Reprovado';
                    $dadosTurma['reprovados']++;
                    $totalReprovados++;
                }
            } else {
                $alunoInfo['media'] = null;
                $alunoInfo['status'] = 'Sem notas';
            }
            
            $dadosTurma['alunos'][] = $alunoInfo;
        }
        
        if ($alunosComMedia > 0) {
            $dadosTurma['media_turma'] = round($somaMedias / $alunosComMedia, 1);
        }
        
        $relatorio[] = $dadosTurma;
        $totalAlunos += count($alunos);
    }
    
    $data['relatorio'] = $relatorio;
    $data['resumo'] = [
        'total_alunos' => $totalAlunos,
        'aprovados' => $totalAprovados,
        'recurso' => $totalRecurso,
        'reprovados' => $totalReprovados
    ];
    
    return view('admin/grades/period', $data);
}
}