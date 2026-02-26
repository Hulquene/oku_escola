<?php
// app/Controllers/teachers/MiniGradeSheet.php

namespace App\Controllers\teachers;

use App\Controllers\BaseController;
use App\Models\AcademicYearModel;
use App\Models\CourseModel;
use App\Models\GradeLevelModel;
use App\Models\ClassModel;
use App\Models\DisciplineModel;
use App\Models\ExamScheduleModel;
use App\Models\ExamBoardModel;
use App\Models\ExamPeriodModel;
use App\Models\EnrollmentModel;
use App\Models\ExamResultModel;
use App\Models\ClassDisciplineModel;

// Para exportação Excel
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Font;

class MiniGradeSheet extends BaseController
{
    protected $academicYearModel;
    protected $courseModel;
    protected $gradeLevelModel;
    protected $classModel;
    protected $disciplineModel;
    protected $examScheduleModel;
    protected $examBoardModel;
    protected $examPeriodModel;
    protected $enrollmentModel;
    protected $examResultModel;
    protected $classDisciplineModel;
    
    public function __construct()
    {
        $this->academicYearModel = new AcademicYearModel();
        $this->courseModel = new CourseModel();
        $this->gradeLevelModel = new GradeLevelModel();
        $this->classModel = new ClassModel();
        $this->disciplineModel = new DisciplineModel();
        $this->examScheduleModel = new ExamScheduleModel();
        $this->examBoardModel = new ExamBoardModel();
        $this->examPeriodModel = new ExamPeriodModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->examResultModel = new ExamResultModel();
        $this->classDisciplineModel = new ClassDisciplineModel();
    }
    
    /**
     * List mini pautas (exam schedules) for the teacher
     */
    public function index()
    {
        $data['title'] = 'Mini Pautas';
        
        $teacherId = session()->get('user_id');
        
        // Filtros
        $filters = [
            'ano_letivo' => $this->request->getGet('ano_letivo'),
            'curso' => $this->request->getGet('curso'),
            'level' => $this->request->getGet('level'),
            'turma' => $this->request->getGet('turma'),
            'disciplina' => $this->request->getGet('disciplina')
        ];
        
        // Buscar mini pautas (exam_schedules) do professor
        $builder = $this->examScheduleModel
            ->select('
                tbl_exam_schedules.*,
                tbl_classes.class_name,
                tbl_classes.class_code,
                tbl_courses.course_name,
                tbl_courses.course_code,
                tbl_grade_levels.level_name,
                tbl_disciplines.discipline_name,
                tbl_disciplines.discipline_code,
                tbl_exam_periods.period_name,
                tbl_exam_periods.period_type,
                tbl_academic_years.year_name,
                tbl_academic_years.id as academic_year_id,
                (SELECT COUNT(*) FROM tbl_enrollments WHERE class_id = tbl_exam_schedules.class_id AND status = "Ativo") as total_students,
                (SELECT COUNT(*) FROM tbl_exam_results WHERE exam_schedule_id = tbl_exam_schedules.id) as results_count
            ')
            ->join('tbl_classes', 'tbl_classes.id = tbl_exam_schedules.class_id')
            ->join('tbl_courses', 'tbl_courses.id = tbl_classes.course_id', 'left')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id')
            ->join('tbl_exam_periods', 'tbl_exam_periods.id = tbl_exam_schedules.exam_period_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_exam_periods.academic_year_id')
            ->join('tbl_class_disciplines', 'tbl_class_disciplines.class_id = tbl_exam_schedules.class_id 
                    AND tbl_class_disciplines.discipline_id = tbl_exam_schedules.discipline_id')
            ->where('tbl_class_disciplines.teacher_id', $teacherId);
        
        // Aplicar filtros
        if (!empty($filters['ano_letivo'])) {
            $builder->where('tbl_academic_years.id', $filters['ano_letivo']);
        }
        
        if (!empty($filters['curso'])) {
            $builder->where('tbl_courses.id', $filters['curso']);
        }
        
        if (!empty($filters['level'])) {
            $builder->where('tbl_grade_levels.id', $filters['level']);
        }
        
        if (!empty($filters['turma'])) {
            $builder->where('tbl_classes.id', $filters['turma']);
        }
        
        if (!empty($filters['disciplina'])) {
            $builder->where('tbl_disciplines.id', $filters['disciplina']);
        }
        
        $miniPautas = $builder->orderBy('tbl_exam_schedules.created_at', 'DESC')
            ->findAll();
        
        // Para cada mini pauta, buscar dados completos
        foreach ($miniPautas as $pauta) {
            $dadosCompletos = $this->getMiniPautaData($pauta->id);
            if ($dadosCompletos) {
                $pauta->alunos = $dadosCompletos['alunos'];
                $pauta->medias = $dadosCompletos['medias'];
                $pauta->estatisticas = $dadosCompletos['estatisticas'];
            } else {
                $pauta->alunos = [];
                $pauta->medias = [];
                $pauta->estatisticas = [
                    'totalAlunos' => 0,
                    'aprovados' => 0,
                    'recurso' => 0,
                    'reprovados' => 0,
                    'pendentes' => 0,
                    'mediaTurma' => '—',
                    'taxaAprovacao' => 0
                ];
            }
        }
        
        $data['miniPautas'] = $miniPautas;
        $data['pager'] = $this->examScheduleModel->pager;
        
        // Dados para os filtros
        $data['anosLetivos'] = $this->academicYearModel->orderBy('year_name', 'DESC')->findAll();
        $data['cursos'] = $this->courseModel->where('is_active', 1)->findAll();
        $data['levels'] = $this->gradeLevelModel->where('is_active', 1)->findAll();
        
        // Turmas do professor para filtro
        $data['turmas'] = $this->classDisciplineModel
            ->select('tbl_classes.id, tbl_classes.class_name')
            ->join('tbl_classes', 'tbl_classes.id = tbl_class_disciplines.class_id')
            ->where('tbl_class_disciplines.teacher_id', $teacherId)
            ->where('tbl_classes.is_active', 1)
            ->distinct()
            ->findAll();
        
        // Disciplinas do professor para filtro
        $data['disciplinas'] = $this->classDisciplineModel
            ->select('tbl_disciplines.id, tbl_disciplines.discipline_name')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
            ->where('tbl_class_disciplines.teacher_id', $teacherId)
            ->distinct()
            ->findAll();
        
        $data['filters'] = $filters;
        
        return view('teachers/mini_grade_sheet/index', $data);
    }
    
    /**
     * Filter mini pautas (AJAX)
     */
    public function filter()
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }
        
        $teacherId = session()->get('user_id');
        
        $filters = [
            'ano_letivo' => $this->request->getGet('ano_letivo'),
            'curso' => $this->request->getGet('curso'),
            'level' => $this->request->getGet('level'),
            'turma' => $this->request->getGet('turma'),
            'disciplina' => $this->request->getGet('disciplina')
        ];
        
        $builder = $this->examScheduleModel
            ->select('
                tbl_exam_schedules.*,
                tbl_classes.class_name,
                tbl_courses.course_name,
                tbl_grade_levels.level_name,
                tbl_disciplines.discipline_name,
                tbl_exam_periods.period_name,
                tbl_academic_years.year_name,
                (SELECT COUNT(*) FROM tbl_enrollments WHERE class_id = tbl_exam_schedules.class_id AND status = "Ativo") as total_students,
                (SELECT COUNT(*) FROM tbl_exam_results WHERE exam_schedule_id = tbl_exam_schedules.id) as results_count
            ')
            ->join('tbl_classes', 'tbl_classes.id = tbl_exam_schedules.class_id')
            ->join('tbl_courses', 'tbl_courses.id = tbl_classes.course_id')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id')
            ->join('tbl_exam_periods', 'tbl_exam_periods.id = tbl_exam_schedules.exam_period_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_exam_periods.academic_year_id')
            ->join('tbl_class_disciplines', 'tbl_class_disciplines.class_id = tbl_exam_schedules.class_id 
                    AND tbl_class_disciplines.discipline_id = tbl_exam_schedules.discipline_id')
            ->where('tbl_class_disciplines.teacher_id', $teacherId);
        
        if (!empty($filters['ano_letivo'])) {
            $builder->where('tbl_academic_years.id', $filters['ano_letivo']);
        }
        
        if (!empty($filters['curso'])) {
            $builder->where('tbl_courses.id', $filters['curso']);
        }
        
        if (!empty($filters['level'])) {
            $builder->where('tbl_grade_levels.id', $filters['level']);
        }
        
        if (!empty($filters['turma'])) {
            $builder->where('tbl_classes.id', $filters['turma']);
        }
        
        if (!empty($filters['disciplina'])) {
            $builder->where('tbl_disciplines.id', $filters['disciplina']);
        }
        
        $miniPautas = $builder->orderBy('tbl_exam_schedules.created_at', 'DESC')
            ->findAll();
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $miniPautas
        ]);
    }
 
    /**
     * View mini pauta (com alunos e notas)
     */
    public function view($id)
    {
        $data['title'] = 'Visualizar Mini Pauta';
        
        $teacherId = session()->get('user_id');
        
        // Buscar mini pauta
        $data['miniPauta'] = $this->examScheduleModel
            ->select('
                tbl_exam_schedules.*,
                tbl_classes.class_name,
                tbl_classes.class_code,
                tbl_courses.course_name,
                tbl_courses.course_code,
                tbl_grade_levels.level_name,
                tbl_disciplines.discipline_name,
                tbl_disciplines.discipline_code,
                tbl_exam_boards.board_name,
                tbl_exam_boards.board_code,
                tbl_exam_boards.board_type,
                tbl_exam_periods.period_name,
                tbl_exam_periods.period_type,
                tbl_academic_years.year_name
            ')
            ->join('tbl_classes', 'tbl_classes.id = tbl_exam_schedules.class_id')
            ->join('tbl_courses', 'tbl_courses.id = tbl_classes.course_id')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id')
            ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exam_schedules.exam_board_id')
            ->join('tbl_exam_periods', 'tbl_exam_periods.id = tbl_exam_schedules.exam_period_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_exam_periods.academic_year_id')
            ->where('tbl_exam_schedules.id', $id)
            ->first();
        
        if (!$data['miniPauta']) {
            return redirect()->to('/teachers/mini-pauta')
                ->with('error', 'Mini pauta não encontrada.');
        }
        
        // Buscar alunos da turma
        $data['alunos'] = $this->enrollmentModel
            ->select('
                tbl_enrollments.id as enrollment_id,
                tbl_students.id as student_id,
                tbl_students.student_number,
                tbl_users.first_name,
                tbl_users.last_name,
                CONCAT(tbl_users.first_name, " ", tbl_users.last_name) as full_name
            ')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->where('tbl_enrollments.class_id', $data['miniPauta']->class_id)
            ->whereIn('tbl_enrollments.status', ['Ativo', 'Pendente'])
            ->orderBy('tbl_users.first_name', 'ASC')
            ->orderBy('tbl_users.last_name', 'ASC')
            ->findAll();
        
        // Buscar resultados existentes
        $results = $this->examResultModel
            ->where('exam_schedule_id', $id)
            ->findAll();
        
        $data['resultados'] = [];
        foreach ($results as $result) {
            $data['resultados'][$result->enrollment_id] = $result;
        }
        
        // Buscar presenças
        $attendanceModel = new \App\Models\ExamAttendanceModel();
        $attendances = $attendanceModel->getByExam($id);
        
        $data['presencas'] = [];
        foreach ($attendances as $attendance) {
            $data['presencas'][$attendance->enrollment_id] = $attendance;
        }
        
        // Estatísticas
        $data['totalAlunos'] = count($data['alunos']);
        $data['totalResultados'] = count($results);
        $data['estatisticas'] = $this->examResultModel->getExamStatistics($id);
        
        return view('teachers/mini_grade_sheet/view', $data);
    }
    
    /**
     * Print mini pauta - Versão melhorada com todas as notas
     */
    public function print($id)
    {
        $teacherId = session()->get('user_id');
        
        // Buscar dados completos da mini pauta
        $dados = $this->getMiniPautaData($id);
        
        if (!$dados) {
            return redirect()->to('/teachers/mini-grade-sheet')
                ->with('error', 'Mini pauta não encontrada.');
        }
        
        $data = [
            'schedule' => $dados['schedule'],
            'alunos' => $dados['alunos'],
            'medias' => $dados['medias'],
            'estatisticas' => $dados['estatisticas'],
            'data_impressao' => date('d/m/Y H:i:s')
        ];
        
        return view('teachers/mini_grade_sheet/print', $data);
    }
    
    /**
     * Export mini pauta to Excel
     */
    public function export($id)
    {
        // Buscar dados completos da mini pauta
        $dados = $this->getMiniPautaData($id);
        
        if (!$dados) {
            return redirect()->to('/teachers/mini-grade-sheet')
                ->with('error', 'Mini pauta não encontrada.');
        }
        
        $schedule = $dados['schedule'];
        $alunos = $dados['alunos'];
        $medias = $dados['medias'];
        $estatisticas = $dados['estatisticas'];
        
        // Criar nova planilha
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();
        $sheet->setTitle('Mini Pauta');
        
        // Estilos
        $headerStyle = [
            'font' => ['bold' => true, 'size' => 12],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '4472C4']],
            'font' => ['color' => ['rgb' => 'FFFFFF'], 'bold' => true]
        ];
        
        $subHeaderStyle = [
            'font' => ['bold' => true],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER],
            'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D9E1F2']]
        ];
        
        $titleStyle = [
            'font' => ['bold' => true, 'size' => 14],
            'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER]
        ];
        
        // Título
        $sheet->setCellValue('A1', 'MINI PAUTA DE AVALIAÇÃO');
        $sheet->mergeCells('A1:Q1');
        $sheet->getStyle('A1')->applyFromArray($titleStyle);
        
        // Informações da pauta
        $sheet->setCellValue('A2', 'DISCIPLINA: ' . $schedule->discipline_name);
        $sheet->mergeCells('A2:C2');
        $sheet->setCellValue('D2', 'CURSO: ' . ($schedule->course_name ?? 'N/A'));
        $sheet->mergeCells('D2:F2');
        
        $sheet->setCellValue('A3', 'TURMA: ' . $schedule->class_name);
        $sheet->mergeCells('A3:C3');
        $sheet->setCellValue('D3', 'CLASSE: ' . ($schedule->level_name ?? 'N/A'));
        $sheet->mergeCells('D3:F3');
        
        $sheet->setCellValue('A4', 'ANO LETIVO: ' . $schedule->year_name);
        $sheet->mergeCells('A4:C4');
        $sheet->setCellValue('D4', 'PERÍODO: ' . $schedule->period_name);
        $sheet->mergeCells('D4:F4');
        
        // Cabeçalho da tabela
        $sheet->setCellValue('A6', 'Nº');
        $sheet->setCellValue('B6', 'Nome do Aluno');
        $sheet->mergeCells('B6:B7');
        
        // 1º Trimestre
        $sheet->setCellValue('C6', '1º TRIMESTRE');
        $sheet->mergeCells('C6:F6');
        $sheet->setCellValue('C7', 'MAC');
        $sheet->setCellValue('D7', 'NPP');
        $sheet->setCellValue('E7', 'NPT');
        $sheet->setCellValue('F7', 'MT');
        
        // 2º Trimestre
        $sheet->setCellValue('G6', '2º TRIMESTRE');
        $sheet->mergeCells('G6:J6');
        $sheet->setCellValue('G7', 'MAC');
        $sheet->setCellValue('H7', 'NPP');
        $sheet->setCellValue('I7', 'NPT');
        $sheet->setCellValue('J7', 'MT');
        
        // 3º Trimestre
        $sheet->setCellValue('K6', '3º TRIMESTRE');
        $sheet->mergeCells('K6:N6');
        $sheet->setCellValue('K7', 'MAC');
        $sheet->setCellValue('L7', 'NPP');
        $sheet->setCellValue('M7', 'NPT');
        $sheet->setCellValue('N7', 'MT');
        
        // MDF e Situação
        $sheet->setCellValue('O6', 'MDF');
        $sheet->mergeCells('O6:O7');
        $sheet->setCellValue('P6', 'Situação');
        $sheet->mergeCells('P6:P7');
        $sheet->setCellValue('Q6', 'Nº Processo');
        $sheet->mergeCells('Q6:Q7');
        
        // Aplicar estilos ao cabeçalho
        $sheet->getStyle('A6:Q7')->applyFromArray($subHeaderStyle);
        $sheet->getStyle('C6:N6')->applyFromArray($headerStyle);
        $sheet->getStyle('O6:Q7')->applyFromArray($headerStyle);
        
        // Preencher dados dos alunos
        $row = 8;
        $counter = 1;
        
        foreach ($alunos as $aluno) {
            $mediasAluno = $medias[$aluno->enrollment_id] ?? [];
            
            $sheet->setCellValue('A' . $row, $counter++);
            $sheet->setCellValue('B' . $row, $aluno->full_name ?? $aluno->first_name . ' ' . $aluno->last_name);
            $sheet->setCellValue('Q' . $row, $aluno->student_number ?? '—');
            
            // 1º Trimestre
            $sheet->setCellValue('C' . $row, $mediasAluno['trimestres'][1]['AC'] ?? '—');
            $sheet->setCellValue('D' . $row, $mediasAluno['trimestres'][1]['NPP'] ?? '—');
            $sheet->setCellValue('E' . $row, $mediasAluno['trimestres'][1]['NPT'] ?? '—');
            $sheet->setCellValue('F' . $row, $mediasAluno['trimestres'][1]['MT'] ?? '—');
            
            // 2º Trimestre
            $sheet->setCellValue('G' . $row, $mediasAluno['trimestres'][2]['AC'] ?? '—');
            $sheet->setCellValue('H' . $row, $mediasAluno['trimestres'][2]['NPP'] ?? '—');
            $sheet->setCellValue('I' . $row, $mediasAluno['trimestres'][2]['NPT'] ?? '—');
            $sheet->setCellValue('J' . $row, $mediasAluno['trimestres'][2]['MT'] ?? '—');
            
            // 3º Trimestre
            $sheet->setCellValue('K' . $row, $mediasAluno['trimestres'][3]['AC'] ?? '—');
            $sheet->setCellValue('L' . $row, $mediasAluno['trimestres'][3]['NPP'] ?? '—');
            $sheet->setCellValue('M' . $row, $mediasAluno['trimestres'][3]['NPT'] ?? '—');
            $sheet->setCellValue('N' . $row, $mediasAluno['trimestres'][3]['MT'] ?? '—');
            
            // MDF
            $mdf = $mediasAluno['MDF'] ?? '—';
            $sheet->setCellValue('O' . $row, $mdf);
            
            // Situação
            $situacao = $mediasAluno['situacao'] ?? 'Pendente';
            $sheet->setCellValue('P' . $row, $situacao);
            
            // Formatar células
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $row . ':O' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('Q' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            // Destacar MDF
            if ($mdf !== '—') {
                if ($mdf >= 10) {
                    $sheet->getStyle('O' . $row)->getFont()->getColor()->setRGB('008000');
                } elseif ($mdf >= 7) {
                    $sheet->getStyle('O' . $row)->getFont()->getColor()->setRGB('FFA500');
                } else {
                    $sheet->getStyle('O' . $row)->getFont()->getColor()->setRGB('FF0000');
                }
            }
            
            $row++;
        }
        
        // Estatísticas
        $row += 2;
        $sheet->setCellValue('A' . $row, 'RESUMO DA TURMA:');
        $sheet->mergeCells('A' . $row . ':C' . $row);
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        
        $row++;
        $sheet->setCellValue('A' . $row, 'Total de Alunos: ' . $estatisticas['totalAlunos']);
        $sheet->setCellValue('C' . $row, 'Aprovados: ' . $estatisticas['aprovados']);
        $sheet->setCellValue('E' . $row, 'Recurso: ' . $estatisticas['recurso']);
        $sheet->setCellValue('G' . $row, 'Reprovados: ' . $estatisticas['reprovados']);
        
        $row++;
        $sheet->setCellValue('A' . $row, 'Média da Turma: ' . $estatisticas['mediaTurma']);
        $sheet->setCellValue('C' . $row, 'Taxa de Aprovação: ' . $estatisticas['taxaAprovacao'] . '%');
        
        // Rodapé com legendas
        $row += 3;
        $sheet->setCellValue('A' . $row, 'Legenda:');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        
        $row++;
        $sheet->setCellValue('A' . $row, 'MAC = Média das Avaliações Contínuas');
        $sheet->setCellValue('D' . $row, 'NPP = Nota da Prova do Professor');
        $sheet->setCellValue('G' . $row, 'NPT = Nota da Prova Trimestral');
        
        $row++;
        $sheet->setCellValue('A' . $row, 'MT = Média Trimestral (MAC + NPP + NPT) / 3');
        $sheet->setCellValue('D' . $row, 'MDF = Média Final da Disciplina (MT1 + MT2 + MT3) / 3');
        
        $row++;
        $sheet->setCellValue('A' . $row, 'Aprovado: MDF ≥ 10 | Recurso: MDF 7-9 | Reprovado: MDF < 7');
        
        // Assinaturas
        $row += 3;
        $sheet->setCellValue('A' . $row, 'O Professor');
        $sheet->setCellValue('E' . $row, 'O Coordenador');
        $sheet->setCellValue('I' . $row, 'O Director');
        
        $row++;
        $sheet->setCellValue('A' . $row, '__________________________');
        $sheet->setCellValue('E' . $row, '__________________________');
        $sheet->setCellValue('I' . $row, '__________________________');
        
        $row++;
        $sheet->setCellValue('A' . $row, 'Data: ' . date('d/m/Y'));
        $sheet->setCellValue('I' . $row, 'Data: ' . date('d/m/Y'));
        
        // Ajustar largura das colunas
        foreach (range('A', 'Q') as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Criar arquivo Excel
        $writer = new Xlsx($spreadsheet);
        
        // Nome do arquivo
        $filename = 'mini_pauta_' . $schedule->discipline_code . '_' . $schedule->class_code . '_' . date('Ymd_His') . '.xlsx';
        
        // Configurar resposta HTTP
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer->save('php://output');
        exit;
    }
    
    /**
     * Get disciplinas by class (AJAX)
     */
    public function getDisciplinas($classId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['error' => 'Invalid request']);
        }
        
        $teacherId = session()->get('user_id');
        
        $disciplinas = $this->classDisciplineModel
            ->select('
                tbl_disciplines.id,
                tbl_disciplines.discipline_name,
                tbl_disciplines.discipline_code
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
            ->where('tbl_class_disciplines.class_id', $classId)
            ->where('tbl_class_disciplines.teacher_id', $teacherId)
            ->where('tbl_class_disciplines.is_active', 1)
            ->findAll();
        
        return $this->response->setJSON([
            'success' => true,
            'data' => $disciplinas
        ]);
    }

    /**
     * Get mini pauta data with all results grouped by assessment type
     */
    private function getMiniPautaData($examScheduleId)
    {
        // Buscar o agendamento do exame
        $schedule = $this->examScheduleModel
            ->select('
                tbl_exam_schedules.*,
                tbl_classes.class_name,
                tbl_classes.class_code,
                tbl_courses.course_name,
                tbl_courses.course_code,
                tbl_grade_levels.level_name,
                tbl_disciplines.discipline_name,
                tbl_disciplines.discipline_code,
                tbl_exam_periods.period_name,
                tbl_exam_periods.period_type,
                tbl_academic_years.year_name,
                tbl_academic_years.id as academic_year_id
            ')
            ->join('tbl_classes', 'tbl_classes.id = tbl_exam_schedules.class_id')
            ->join('tbl_courses', 'tbl_courses.id = tbl_classes.course_id', 'left')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id')
            ->join('tbl_exam_periods', 'tbl_exam_periods.id = tbl_exam_schedules.exam_period_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_exam_periods.academic_year_id')
            ->where('tbl_exam_schedules.id', $examScheduleId)
            ->first();
        
        if (!$schedule) {
            return null;
        }
        
        // Buscar todos os alunos da turma
        $alunos = $this->enrollmentModel
            ->select('
                tbl_enrollments.id as enrollment_id,
                tbl_students.id as student_id,
                tbl_students.student_number,
                tbl_users.first_name,
                tbl_users.last_name,
                CONCAT(tbl_users.first_name, " ", tbl_users.last_name) as full_name
            ')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->where('tbl_enrollments.class_id', $schedule->class_id)
            ->whereIn('tbl_enrollments.status', ['Ativo', 'Pendente'])
            ->orderBy('tbl_users.first_name', 'ASC')
            ->findAll();
        
        // Buscar todos os resultados de exames para esta disciplina e turma
        $resultados = $this->examResultModel
            ->select('
                tbl_exam_results.*,
                tbl_exam_schedules.exam_date,
                tbl_exam_schedules.exam_period_id,
                tbl_exam_periods.period_type,
                tbl_exam_periods.semester_id,
                tbl_semesters.semester_type,
                tbl_exam_boards.board_code as assessment_type
            ')
            ->join('tbl_exam_schedules', 'tbl_exam_schedules.id = tbl_exam_results.exam_schedule_id')
            ->join('tbl_exam_periods', 'tbl_exam_periods.id = tbl_exam_schedules.exam_period_id')
            ->join('tbl_semesters', 'tbl_semesters.id = tbl_exam_periods.semester_id')
            ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exam_schedules.exam_board_id')
            ->where('tbl_exam_schedules.class_id', $schedule->class_id)
            ->where('tbl_exam_schedules.discipline_id', $schedule->discipline_id)
            ->where('tbl_exam_periods.academic_year_id', $schedule->academic_year_id)
            ->orderBy('tbl_semesters.semester_type', 'ASC')
            ->orderBy('tbl_exam_results.assessment_type', 'ASC')
            ->findAll();
        
        // Mapear semestres/trimestres
        $semesterMap = [
            '1º Trimestre' => 1,
            '2º Trimestre' => 2,
            '3º Trimestre' => 3,
            '1º Semestre' => 1,
            '2º Semestre' => 2
        ];
        
        // Mapear tipos de avaliação
        $assessmentTypes = ['AC', 'NPP', 'NPT'];
        
        // Organizar notas por aluno, trimestre e tipo
        $notasPorAluno = [];
        
        foreach ($alunos as $aluno) {
            $enrollmentId = $aluno->enrollment_id;
            $notasPorAluno[$enrollmentId] = [
                'aluno' => $aluno,
                'notas' => [
                    1 => ['AC' => [], 'NPP' => [], 'NPT' => []],
                    2 => ['AC' => [], 'NPP' => [], 'NPT' => []],
                    3 => ['AC' => [], 'NPP' => [], 'NPT' => []]
                ]
            ];
        }
        
        // Agrupar resultados por aluno, trimestre e tipo
        foreach ($resultados as $resultado) {
            $enrollmentId = $resultado->enrollment_id;
            
            // Determinar o trimestre
            $trimestre = $semesterMap[$resultado->semester_type] ?? 1;
            
            // Determinar o tipo de avaliação
            $tipo = $resultado->assessment_type;
            
            if (in_array($tipo, $assessmentTypes) && isset($notasPorAluno[$enrollmentId])) {
                $notasPorAluno[$enrollmentId]['notas'][$trimestre][$tipo][] = $resultado->score;
            }
        }
        
        // Calcular médias por tipo e trimestre
        $mediasPorAluno = [];
        
        foreach ($notasPorAluno as $enrollmentId => $dados) {
            $aluno = $dados['aluno'];
            $notasTrimestres = $dados['notas'];
            
            $medias = [
                'aluno' => $aluno,
                'trimestres' => []
            ];
            
            $somaMT = 0;
            $trimestresCompletos = 0;
            
            for ($t = 1; $t <= 3; $t++) {
                $notasTrimestre = $notasTrimestres[$t];
                
                $mediaAC = !empty($notasTrimestre['AC']) ? round(array_sum($notasTrimestre['AC']) / count($notasTrimestre['AC']), 1) : '—';
                $mediaNPP = !empty($notasTrimestre['NPP']) ? round(array_sum($notasTrimestre['NPP']) / count($notasTrimestre['NPP']), 1) : '—';
                $mediaNPT = !empty($notasTrimestre['NPT']) ? round(array_sum($notasTrimestre['NPT']) / count($notasTrimestre['NPT']), 1) : '—';
                
                $notasValidas = [];
                if ($mediaAC !== '—') $notasValidas[] = $mediaAC;
                if ($mediaNPP !== '—') $notasValidas[] = $mediaNPP;
                if ($mediaNPT !== '—') $notasValidas[] = $mediaNPT;
                
                if (count($notasValidas) == 3) {
                    $mt = round(array_sum($notasValidas) / 3, 1);
                    $somaMT += $mt;
                    $trimestresCompletos++;
                } else {
                    $mt = '—';
                }
                
                $medias['trimestres'][$t] = [
                    'AC' => $mediaAC,
                    'NPP' => $mediaNPP,
                    'NPT' => $mediaNPT,
                    'MT' => $mt
                ];
            }
            
            if ($trimestresCompletos == 3) {
                $medias['MDF'] = round($somaMT / 3, 1);
                
                if ($medias['MDF'] >= 10) {
                    $medias['situacao'] = 'Aprovado';
                    $medias['situacaoClass'] = 'success';
                } elseif ($medias['MDF'] >= 7) {
                    $medias['situacao'] = 'Recurso';
                    $medias['situacaoClass'] = 'warning';
                } else {
                    $medias['situacao'] = 'Reprovado';
                    $medias['situacaoClass'] = 'danger';
                }
            } else {
                $medias['MDF'] = '—';
                $medias['situacao'] = 'Pendente';
                $medias['situacaoClass'] = 'secondary';
            }
            
            $mediasPorAluno[$enrollmentId] = $medias;
        }
        
        $estatisticas = $this->calcularEstatisticasTurma($mediasPorAluno);
        
        return [
            'schedule' => $schedule,
            'alunos' => $alunos,
            'medias' => $mediasPorAluno,
            'estatisticas' => $estatisticas
        ];
    }

    /**
     * Calculate class statistics
     */
    private function calcularEstatisticasTurma($mediasPorAluno)
    {
        $totalAlunos = count($mediasPorAluno);
        $aprovados = 0;
        $recurso = 0;
        $reprovados = 0;
        $pendentes = 0;
        $somaMDF = 0;
        $alunosComMDF = 0;
        
        foreach ($mediasPorAluno as $medias) {
            if ($medias['MDF'] !== '—') {
                $somaMDF += $medias['MDF'];
                $alunosComMDF++;
                
                if ($medias['MDF'] >= 10) {
                    $aprovados++;
                } elseif ($medias['MDF'] >= 7) {
                    $recurso++;
                } else {
                    $reprovados++;
                }
            } else {
                $pendentes++;
            }
        }
        
        return [
            'totalAlunos' => $totalAlunos,
            'aprovados' => $aprovados,
            'recurso' => $recurso,
            'reprovados' => $reprovados,
            'pendentes' => $pendentes,
            'mediaTurma' => $alunosComMDF > 0 ? round($somaMDF / $alunosComMDF, 1) : '—',
            'taxaAprovacao' => $totalAlunos > 0 ? round(($aprovados / $totalAlunos) * 100, 1) : 0
        ];
    }
}