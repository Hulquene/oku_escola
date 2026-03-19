<?php
// app/Controllers/admin/MiniGradeSheet.php
namespace App\Controllers\admin;

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
use App\Models\SemesterModel;

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
    protected $semesterModel;
    
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
        $this->semesterModel = new SemesterModel();
        
        helper('auth');
        helper('log');
    }
    
    /**
     * Lista todas as mini pautas do sistema (para admin)
     */
    public function index()
    {
        $data['title'] = 'Mini Pautas - Administração';
        
        // Filtros
        $filters = [
            'ano_letivo' => $this->request->getGet('ano_letivo'),
            'curso' => $this->request->getGet('curso'),
            'level' => $this->request->getGet('level'),
            'turma' => $this->request->getGet('turma'),
            'disciplina' => $this->request->getGet('disciplina'),
            'status' => $this->request->getGet('status')
        ];
        
        // Buscar todas as mini pautas (exam_schedules) do sistema
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
                tbl_users.first_name as teacher_first_name,
                tbl_users.last_name as teacher_last_name,
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
                    AND tbl_class_disciplines.discipline_id = tbl_exam_schedules.discipline_id', 'left')
            ->join('tbl_users', 'tbl_users.id = tbl_class_disciplines.teacher_id', 'left');
        
        // Aplicar filtros
        if (!empty($filters['ano_letivo'])) {
            $builder->where('tbl_academic_years.id', $filters['ano_letivo']);
        }
        
        if (!empty($filters['curso'])) {
            if ($filters['curso'] == '0') {
                $builder->where('tbl_classes.course_id IS NULL');
            } else {
                $builder->where('tbl_courses.id', $filters['curso']);
            }
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
        
        if (!empty($filters['status'])) {
            $builder->where('tbl_exam_schedules.status', $filters['status']);
        }
        
        $miniPautas = $builder->orderBy('tbl_exam_schedules.created_at', 'DESC')
            ->paginate(20);
        
        // Para cada mini pauta, buscar dados completos
        foreach ($miniPautas as &$pauta) {
            $dadosCompletos = $this->getMiniPautaData($pauta['id']);
            if ($dadosCompletos) {
                $pauta['alunos'] = $dadosCompletos['alunos'];
                $pauta['medias'] = $dadosCompletos['medias'];
                $pauta['estatisticas'] = $dadosCompletos['estatisticas'];
            } else {
                $pauta['alunos'] = [];
                $pauta['medias'] = [];
                $pauta['estatisticas'] = [
                    'totalAlunos' => 0,
                    'aprovados' => 0,
                    'recurso' => 0,
                    'reprovados' => 0,
                    'pendentes' => 0,
                    'mediaTurma' => '—',
                    'taxaAprovacao' => 0,
                    'aprovacaoPorDisciplina' => []
                ];
            }
        }
        
        $data['miniPautas'] = $miniPautas;
        $data['pager'] = $this->examScheduleModel->pager;
        
        // Dados para os filtros
        $data['anosLetivos'] = $this->academicYearModel
            ->where('is_active', 1)
            ->orderBy('year_name', 'DESC')
            ->findAll();
        
        $data['cursos'] = $this->courseModel
            ->where('is_active', 1)
            ->orderBy('course_name', 'ASC')
            ->findAll();
        
        $data['levels'] = $this->gradeLevelModel
            ->where('is_active', 1)
            ->orderBy('sort_order', 'ASC')
            ->findAll();
        
        // Todas as turmas ativas
        $data['turmas'] = $this->classModel
            ->select('tbl_classes.id, tbl_classes.class_name, tbl_academic_years.year_name')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_classes.academic_year_id')
            ->where('tbl_classes.is_active', 1)
            ->orderBy('tbl_classes.class_name', 'ASC')
            ->findAll();
        
        // Todas as disciplinas ativas
        $data['disciplinas'] = $this->disciplineModel
            ->where('is_active', 1)
            ->orderBy('discipline_name', 'ASC')
            ->findAll();
        
        // Status disponíveis
        $data['statusOptions'] = [
            '' => 'Todos',
            'Agendado' => 'Agendado',
            'Realizado' => 'Realizado',
            'Cancelado' => 'Cancelado',
            'Adiado' => 'Adiado'
        ];
        
        $data['filters'] = $filters;
        
        log_view('mini_grade_sheet', null, 'Visualizou lista de mini pautas');
        
        return view('admin/mini_grade_sheet/index', $data);
    }
    
    /**
     * Visualizar mini pauta detalhada
     */
    public function view($id)
    {
        $data['title'] = 'Visualizar Mini Pauta';
        
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
                tbl_academic_years.year_name,
                tbl_users.first_name as teacher_first_name,
                tbl_users.last_name as teacher_last_name
            ')
            ->join('tbl_classes', 'tbl_classes.id = tbl_exam_schedules.class_id')
            ->join('tbl_courses', 'tbl_courses.id = tbl_classes.course_id', 'left')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id')
            ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exam_schedules.exam_board_id')
            ->join('tbl_exam_periods', 'tbl_exam_periods.id = tbl_exam_schedules.exam_period_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_exam_periods.academic_year_id')
            ->join('tbl_class_disciplines', 'tbl_class_disciplines.class_id = tbl_exam_schedules.class_id 
                    AND tbl_class_disciplines.discipline_id = tbl_exam_schedules.discipline_id', 'left')
            ->join('tbl_users', 'tbl_users.id = tbl_class_disciplines.teacher_id', 'left')
            ->where('tbl_exam_schedules.id', $id)
            ->first();
        
        if (!$data['miniPauta']) {
            log_error('Tentativa de visualizar mini pauta inexistente', ['id' => $id]);
            return redirect()->to('/admin/mini-grade-sheet')
                ->with('error', 'Mini pauta não encontrada.');
        }
        
        // Buscar dados completos da mini pauta
        $dadosCompletos = $this->getMiniPautaData($id);
        
        if ($dadosCompletos) {
            $data['alunos'] = $dadosCompletos['alunos'];
            $data['medias'] = $dadosCompletos['medias'];
            $data['estatisticas'] = $dadosCompletos['estatisticas'];
        } else {
            $data['alunos'] = [];
            $data['medias'] = [];
            $data['estatisticas'] = [
                'totalAlunos' => 0,
                'aprovados' => 0,
                'recurso' => 0,
                'reprovados' => 0,
                'pendentes' => 0,
                'mediaTurma' => '—',
                'taxaAprovacao' => 0,
                'aprovacaoPorDisciplina' => []
            ];
        }
        
        log_view('mini_grade_sheet', $id, 'Visualizou mini pauta: ' . $data['miniPauta']['discipline_name']);
        
        return view('admin/mini_grade_sheet/view', $data);
    }
    
    /**
     * Imprimir mini pauta
     */
    public function print($id)
    {
        // Buscar dados completos da mini pauta
        $dados = $this->getMiniPautaData($id);
        
        if (!$dados) {
            log_error('Tentativa de imprimir mini pauta inexistente', ['id' => $id]);
            return redirect()->to('/admin/mini-grade-sheet')
                ->with('error', 'Mini pauta não encontrada.');
        }
        
        $data = [
            'schedule' => $dados['schedule'],
            'alunos' => $dados['alunos'],
            'medias' => $dados['medias'],
            'estatisticas' => $dados['estatisticas'],
            'data_impressao' => date('d/m/Y H:i:s')
        ];
        
        log_view('mini_grade_sheet_print', $id, 'Imprimiu mini pauta');
        
        return view('admin/mini_grade_sheet/print', $data);
    }
    
    /**
     * Exportar mini pauta para Excel
     */
    public function export($id)
    {
        // Buscar dados completos da mini pauta
        $dados = $this->getMiniPautaData($id);
        
        if (!$dados) {
            log_error('Tentativa de exportar mini pauta inexistente', ['id' => $id]);
            return redirect()->to('/admin/mini-grade-sheet')
                ->with('error', 'Mini pauta não encontrada.');
        }
        
        log_export('mini_grade_sheet', "Exportou mini pauta ID: {$id}", ['id' => $id]);
        
        return $this->gerarExcelMiniPauta($dados);
    }
    
    /**
     * Gerar arquivo Excel da mini pauta
     */
    private function gerarExcelMiniPauta($dados)
    {
        $schedule = $dados['schedule'];
        $alunos = $dados['alunos'];
        $medias = $dados['medias'];
        $estatisticas = $dados['estatisticas'];
        
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
        $sheet->setCellValue('A2', 'DISCIPLINA: ' . $schedule['discipline_name']);
        $sheet->mergeCells('A2:C2');
        $sheet->setCellValue('D2', 'CURSO: ' . ($schedule['course_name'] ?? 'N/A'));
        $sheet->mergeCells('D2:F2');
        
        $sheet->setCellValue('A3', 'TURMA: ' . $schedule['class_name']);
        $sheet->mergeCells('A3:C3');
        $sheet->setCellValue('D3', 'CLASSE: ' . ($schedule['level_name'] ?? 'N/A'));
        $sheet->mergeCells('D3:F3');
        
        $sheet->setCellValue('A4', 'ANO LETIVO: ' . $schedule['year_name']);
        $sheet->mergeCells('A4:C4');
        $sheet->setCellValue('D4', 'PERÍODO: ' . $schedule['period_name']);
        $sheet->mergeCells('D4:F4');
        
        // Professor
        $sheet->setCellValue('A5', 'PROFESSOR: ' . ($schedule['teacher_first_name'] ?? 'Não atribuído') . ' ' . ($schedule['teacher_last_name'] ?? ''));
        $sheet->mergeCells('A5:F5');
        
        // Cabeçalho da tabela
        $sheet->setCellValue('A6', 'Nº');
        $sheet->setCellValue('B6', 'Nome do Aluno');
        $sheet->mergeCells('B6:B7');
        
        // 1º Período
        $sheet->setCellValue('C6', '1º PERÍODO');
        $sheet->mergeCells('C6:F6');
        $sheet->setCellValue('C7', 'MAC');
        $sheet->setCellValue('D7', 'NPP');
        $sheet->setCellValue('E7', 'NPT');
        $sheet->setCellValue('F7', 'MT');
        
        // 2º Período
        $sheet->setCellValue('G6', '2º PERÍODO');
        $sheet->mergeCells('G6:J6');
        $sheet->setCellValue('G7', 'MAC');
        $sheet->setCellValue('H7', 'NPP');
        $sheet->setCellValue('I7', 'NPT');
        $sheet->setCellValue('J7', 'MT');
        
        // 3º Período (se existir)
        $tem3Periodo = isset($medias[array_key_first($medias)]['trimestres'][3]);
        
        if ($tem3Periodo) {
            $sheet->setCellValue('K6', '3º PERÍODO');
            $sheet->mergeCells('K6:N6');
            $sheet->setCellValue('K7', 'MAC');
            $sheet->setCellValue('L7', 'NPP');
            $sheet->setCellValue('M7', 'NPT');
            $sheet->setCellValue('N7', 'MT');
            $mfdCol = 'O';
            $situacaoCol = 'P';
            $processoCol = 'Q';
        } else {
            $mfdCol = 'K';
            $situacaoCol = 'L';
            $processoCol = 'M';
        }
        
        // MDF e Situação
        $sheet->setCellValue($mfdCol . '6', 'MDF');
        $sheet->mergeCells($mfdCol . '6:' . $mfdCol . '7');
        $sheet->setCellValue($situacaoCol . '6', 'Situação');
        $sheet->mergeCells($situacaoCol . '6:' . $situacaoCol . '7');
        $sheet->setCellValue($processoCol . '6', 'Nº Processo');
        $sheet->mergeCells($processoCol . '6:' . $processoCol . '7');
        
        // Aplicar estilos ao cabeçalho
        $sheet->getStyle('A6:' . $processoCol . '7')->applyFromArray($subHeaderStyle);
        
        // Preencher dados dos alunos
        $row = 8;
        $counter = 1;
        
        foreach ($alunos as $aluno) {
            $mediasAluno = $medias[$aluno['enrollment_id']] ?? [];
            
            $sheet->setCellValue('A' . $row, $counter++);
            $sheet->setCellValue('B' . $row, $aluno['full_name'] ?? $aluno['first_name'] . ' ' . $aluno['last_name']);
            $sheet->setCellValue($processoCol . $row, $aluno['student_number'] ?? '—');
            
            // 1º Período
            $sheet->setCellValue('C' . $row, $mediasAluno['trimestres'][1]['AC'] ?? '—');
            $sheet->setCellValue('D' . $row, $mediasAluno['trimestres'][1]['NPP'] ?? '—');
            $sheet->setCellValue('E' . $row, $mediasAluno['trimestres'][1]['NPT'] ?? '—');
            $sheet->setCellValue('F' . $row, $mediasAluno['trimestres'][1]['MT'] ?? '—');
            
            // 2º Período
            $sheet->setCellValue('G' . $row, $mediasAluno['trimestres'][2]['AC'] ?? '—');
            $sheet->setCellValue('H' . $row, $mediasAluno['trimestres'][2]['NPP'] ?? '—');
            $sheet->setCellValue('I' . $row, $mediasAluno['trimestres'][2]['NPT'] ?? '—');
            $sheet->setCellValue('J' . $row, $mediasAluno['trimestres'][2]['MT'] ?? '—');
            
            // 3º Período (se existir)
            if ($tem3Periodo) {
                $sheet->setCellValue('K' . $row, $mediasAluno['trimestres'][3]['AC'] ?? '—');
                $sheet->setCellValue('L' . $row, $mediasAluno['trimestres'][3]['NPP'] ?? '—');
                $sheet->setCellValue('M' . $row, $mediasAluno['trimestres'][3]['NPT'] ?? '—');
                $sheet->setCellValue('N' . $row, $mediasAluno['trimestres'][3]['MT'] ?? '—');
            }
            
            // MDF
            $mdf = $mediasAluno['MDF'] ?? '—';
            $sheet->setCellValue($mfdCol . $row, $mdf);
            
            // Situação
            $situacao = $mediasAluno['situacao'] ?? 'Pendente';
            $sheet->setCellValue($situacaoCol . $row, $situacao);
            
            // Formatar células
            $sheet->getStyle('A' . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('C' . $row . ':' . $mfdCol . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle($processoCol . $row)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            
            // Destacar MDF
            if ($mdf !== '—') {
                if ($mdf >= 10) {
                    $sheet->getStyle($mfdCol . $row)->getFont()->getColor()->setRGB('008000');
                } elseif ($mdf >= 7) {
                    $sheet->getStyle($mfdCol . $row)->getFont()->getColor()->setRGB('FFA500');
                } else {
                    $sheet->getStyle($mfdCol . $row)->getFont()->getColor()->setRGB('FF0000');
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
        
        // Estatísticas por disciplina (se houver)
        if (!empty($estatisticas['aprovacaoPorDisciplina'])) {
            $row += 2;
            $sheet->setCellValue('A' . $row, 'DESEMPENHO POR DISCIPLINA:');
            $sheet->mergeCells('A' . $row . ':C' . $row);
            $sheet->getStyle('A' . $row)->getFont()->setBold(true);
            
            $row++;
            $sheet->setCellValue('A' . $row, 'Disciplina');
            $sheet->setCellValue('B' . $row, 'Total');
            $sheet->setCellValue('C' . $row, 'Aprovados');
            $sheet->setCellValue('D' . $row, 'Recurso');
            $sheet->setCellValue('E' . $row, 'Reprovados');
            $sheet->getStyle('A' . $row . ':E' . $row)->getFont()->setBold(true);
            
            $row++;
            foreach ($estatisticas['aprovacaoPorDisciplina'] as $disc) {
                $sheet->setCellValue('A' . $row, $disc['nome']);
                $sheet->setCellValue('B' . $row, $disc['total']);
                $sheet->setCellValue('C' . $row, $disc['aprovados']);
                $sheet->setCellValue('D' . $row, $disc['recurso']);
                $sheet->setCellValue('E' . $row, $disc['reprovados']);
                $row++;
            }
        }
        
        // Rodapé com legendas
        $row += 3;
        $sheet->setCellValue('A' . $row, 'Legenda:');
        $sheet->getStyle('A' . $row)->getFont()->setBold(true);
        
        $row++;
        $sheet->setCellValue('A' . $row, 'MAC = Média das Avaliações Contínuas');
        $sheet->setCellValue('D' . $row, 'NPP = Nota da Prova do Professor');
        $sheet->setCellValue('G' . $row, 'NPT = Nota da Prova Trimestral');
        
        $row++;
        $sheet->setCellValue('A' . $row, 'MT = Média do Período (MAC + NPP + NPT) / 3');
        $sheet->setCellValue('D' . $row, 'MDF = Média Final da Disciplina');
        
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
        foreach (range('A', $processoCol) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }
        
        // Nome do arquivo
        $filename = 'mini_pauta_' . $schedule['discipline_code'] . '_' . $schedule['class_code'] . '_' . date('Ymd_His') . '.xlsx';
        
        // Configurar resposta HTTP
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        
        $writer = new Xlsx($spreadsheet);
        $writer->save('php://output');
        exit;
    }
    
    /**
     * Página principal de pautas trimestrais
     */
    public function trimestral()
    {
        $data['title'] = 'Pautas Trimestrais';
        
        // Filtros
        $academicYearId = $this->request->getGet('academic_year');
        $semesterId = $this->request->getGet('semester');
        $classId = $this->request->getGet('class');
        
        $currentYear = $this->academicYearModel->getCurrent();
        
        // Se não selecionou ano letivo, usar o atual
        if (!$academicYearId && $currentYear) {
            $academicYearId = $currentYear['id'] ?? null;
        }
        
        // Dados para filtros
        $data['academicYears'] = $this->academicYearModel
            ->where('is_active', 1)
            ->orderBy('year_name', 'DESC')
            ->findAll();
        
        // Buscar semestres/períodos do ano letivo selecionado
        if ($academicYearId) {
            $data['semesters'] = $this->semesterModel
                ->where('academic_year_id', $academicYearId)
                ->whereIn('status', ['ativo', 'processado'])
                ->orderBy('start_date', 'ASC')
                ->findAll();
        } else {
            $data['semesters'] = [];
        }

        // Buscar todas as turmas ativas
        $data['classes'] = $this->classModel
            ->select('tbl_classes.*, tbl_academic_years.year_name')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_classes.academic_year_id')
            ->where('tbl_classes.is_active', 1)
            ->orderBy('tbl_classes.class_name', 'ASC')
            ->findAll();
        
        // Se tiver turma e período selecionados, buscar os dados
        if ($classId && $semesterId) {
            return $this->trimestralClass($classId, $semesterId);
        }
        
        $data['selectedYear'] = $academicYearId;
        $data['selectedSemester'] = $semesterId;
        $data['selectedClass'] = $classId;
       
        return view('admin/mini_grade_sheet/trimestral', $data);
    }
    
    /**
     * Visualizar pauta trimestral de uma turma
     */
    public function trimestralClass($classId, $semesterId = null)
    {
        // Se não veio pela URL, pegar do GET
        if (!$semesterId) {
            $semesterId = $this->request->getGet('semester');
        }
        
        if (!$semesterId) {
            return redirect()->to('/admin/mini-grade-sheet/trimestral')
                ->with('error', 'Selecione um período (trimestre/semestre)');
        }
        
        $data['title'] = 'Pauta por Período';
        
        // Buscar informações da turma
        $data['class'] = $this->classModel
            ->select('tbl_classes.*, tbl_academic_years.year_name, tbl_grade_levels.level_name')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_classes.academic_year_id')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
            ->find($classId);
        
        if (!$data['class']) {
            log_error('Tentativa de visualizar pauta de turma inexistente', ['class_id' => $classId]);
            return redirect()->to('/admin/mini-grade-sheet/trimestral')
                ->with('error', 'Turma não encontrada');
        }
        
        $data['semester'] = $this->semesterModel->find($semesterId);
        
        // Buscar alunos da turma
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
            ->where('tbl_enrollments.class_id', $classId)
            ->where('tbl_enrollments.status', 'Ativo')
            ->orderBy('tbl_users.first_name', 'ASC')
            ->findAll();
        
        // Buscar disciplinas da turma
        $disciplinas = $this->classDisciplineModel
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
        
                    
 /*        echo "<pre>";
        var_dump($disciplinas);die; */

        
        // Processar alunos e suas notas
        $alunosProcessados = [];
        $somaNotasTurma = 0;
        $alunosComNotas = 0;
        $aprovados = 0;
        $recurso = 0;
        $reprovados = 0;
        
        foreach ($alunos as $aluno) {
            $alunoArray = (array)$aluno;
            $alunoArray['notas'] = [];
            $somaNotas = 0;
            $disciplinasCount = 0;
            
            foreach ($disciplinas as $disciplina) {
                // Buscar notas para esta disciplina no período selecionado
                $resultados = $this->examResultModel
                    ->select('tbl_exam_results.*')
                    ->join('tbl_exam_schedules', 'tbl_exam_schedules.id = tbl_exam_results.exam_schedule_id')
                    ->join('tbl_exam_periods', 'tbl_exam_periods.id = tbl_exam_schedules.exam_period_id')
                    ->where('tbl_exam_results.enrollment_id', $aluno['enrollment_id'])
                    ->where('tbl_exam_schedules.discipline_id', $disciplina['discipline_id'])
                    ->where('tbl_exam_periods.semester_id', $semesterId)
                    ->findAll();
                
                // Calcular média das notas
                if (!empty($resultados)) {
                    $soma = 0;
                    foreach ($resultados as $r) {
                        $soma += $r['score'];
                    }
                    $nota = round($soma / count($resultados), 1);
                    $alunoArray['notas'][$disciplina['discipline_id']] = $nota;
                    $somaNotas += $nota;
                    $disciplinasCount++;
                } else {
                    $alunoArray['notas'][$disciplina['discipline_id']] = '—';
                }
            }
            
            // Calcular média do aluno neste período
            if ($disciplinasCount > 0) {
                $mediaAluno = round($somaNotas / $disciplinasCount, 1);
                $alunoArray['media_periodo'] = $mediaAluno;
                $somaNotasTurma += $mediaAluno;
                $alunosComNotas++;
                
                // Classificar aluno
                if ($mediaAluno >= 10) {
                    $aprovados++;
                } elseif ($mediaAluno >= 7) {
                    $recurso++;
                } else {
                    $reprovados++;
                }
            } else {
                $alunoArray['media_periodo'] = '—';
            }
            
            $alunosProcessados[] = $alunoArray;
        }
        
        $data['alunos'] = $alunosProcessados;
        $data['disciplinas'] = $disciplinas;
        
        // Estatísticas da turma
        $data['estatisticas'] = [
            'total' => count($alunos),
            'com_notas' => $alunosComNotas,
            'aprovados' => $aprovados,
            'recurso' => $recurso,
            'reprovados' => $reprovados,
            'media_turma' => $alunosComNotas > 0 ? round($somaNotasTurma / $alunosComNotas, 1) : '—',
            'taxa_aprovacao' => $alunosComNotas > 0 ? round(($aprovados / $alunosComNotas) * 100, 1) : 0
        ];
        
        log_view('mini_grade_sheet_trimestral', $classId, 'Visualizou pauta do período para turma: ' . $data['class']['class_name']);
        
        return view('admin/mini_grade_sheet/trimestral_class', $data);
    }
    
    /**
     * Página principal de pautas por disciplina
     */
    public function disciplina()
    {
        $data['title'] = 'Pautas por Disciplina';
        
        // Filtros
        $academicYearId = $this->request->getGet('academic_year');
        $classId = $this->request->getGet('class');
        $disciplineId = $this->request->getGet('discipline');
        
        $currentYear = $this->academicYearModel->getCurrent();
        
        // Dados para filtros
        $data['academicYears'] = $this->academicYearModel
            ->where('is_active', 1)
            ->orderBy('year_name', 'DESC')
            ->findAll();
        
        $data['classes'] = $this->classModel
            ->select('tbl_classes.*, tbl_academic_years.year_name')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_classes.academic_year_id')
            ->where('tbl_classes.is_active', 1)
            ->orderBy('tbl_classes.class_name', 'ASC')
            ->findAll();
        
        // Se uma turma foi selecionada, buscar disciplinas disponíveis
        if ($classId) {
            $data['disciplines'] = $this->classDisciplineModel
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
        } else {
            $data['disciplines'] = [];
        }
        
        $data['selectedYear'] = $academicYearId;
        $data['selectedClass'] = $classId;
        $data['selectedDiscipline'] = $disciplineId;
        
        // Se todos os filtros estiverem selecionados, redirecionar para a view de visualização
        if ($classId && $disciplineId) {
            return $this->disciplinaView($classId, $disciplineId);
        }
        
        return view('admin/mini_grade_sheet/disciplina', $data);
    }
    
    /**
     * Visualizar pauta de uma disciplina específica
     */
    public function disciplinaView($classId, $disciplineId)
    {
        $data['title'] = 'Pauta da Disciplina';
        
        // Buscar informações da turma
        $data['class'] = $this->classModel
            ->select('tbl_classes.*, tbl_academic_years.year_name, tbl_grade_levels.level_name')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_classes.academic_year_id')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
            ->find($classId);
        
        // Buscar informações da disciplina
        $data['discipline'] = $this->classDisciplineModel
            ->select('
                tbl_class_disciplines.*,
                tbl_disciplines.discipline_name,
                tbl_disciplines.discipline_code,
                tbl_users.first_name as teacher_first_name,
                tbl_users.last_name as teacher_last_name
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
            ->join('tbl_users', 'tbl_users.id = tbl_class_disciplines.teacher_id', 'left')
            ->where('tbl_class_disciplines.id', $disciplineId)
            ->where('tbl_class_disciplines.class_id', $classId)
            ->where('tbl_class_disciplines.is_active', 1)
            ->first();
        
        if (!$data['class'] || !$data['discipline']) {
            log_error('Tentativa de visualizar disciplina inexistente', ['class_id' => $classId, 'discipline_id' => $disciplineId]);
            return redirect()->to('/admin/mini-grade-sheet/disciplina')
                ->with('error', 'Turma ou disciplina não encontrada');
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
            ->where('tbl_enrollments.class_id', $classId)
            ->where('tbl_enrollments.status', 'Ativo')
            ->orderBy('tbl_users.first_name', 'ASC')
            ->findAll();
        
        // Buscar resultados da disciplina
        $resultados = $this->examResultModel
            ->select('
                tbl_exam_results.*,
                tbl_exam_schedules.exam_date,
                tbl_exam_schedules.exam_period_id,
                tbl_exam_periods.period_name,
                tbl_exam_periods.semester_id,
                tbl_semesters.semester_type
            ')
            ->join('tbl_exam_schedules', 'tbl_exam_schedules.id = tbl_exam_results.exam_schedule_id')
            ->join('tbl_exam_periods', 'tbl_exam_periods.id = tbl_exam_schedules.exam_period_id')
            ->join('tbl_semesters', 'tbl_semesters.id = tbl_exam_periods.semester_id')
            ->where('tbl_exam_schedules.class_id', $classId)
            ->where('tbl_exam_schedules.discipline_id', $data['discipline']['discipline_id'])
            ->orderBy('tbl_semesters.start_date', 'ASC')
            ->findAll();
        
        // Mapear tipos de período para números
        $periodMap = [
            '1º Trimestre' => 1,
            '2º Trimestre' => 2,
            '3º Trimestre' => 3,
            '1º Semestre' => 1,
            '2º Semestre' => 2
        ];
        
        // Determinar quantos períodos existem
        $periodTypes = array_unique(array_column($resultados, 'semester_type'));
        $maxPeriodos = 3;
        foreach ($periodTypes as $type) {
            if (strpos($type, 'Semestre') !== false) {
                $maxPeriodos = 2;
                break;
            }
        }
        
        // Organizar resultados por aluno e período
        $data['resultados'] = [];
        
        foreach ($resultados as $result) {
            $enrollmentId = $result['enrollment_id'];
            $periodo = $periodMap[$result['semester_type']] ?? 1;
            
            if (!isset($data['resultados'][$enrollmentId])) {
                $data['resultados'][$enrollmentId] = [];
                for ($p = 1; $p <= $maxPeriodos; $p++) {
                    $data['resultados'][$enrollmentId][$p] = ['AC' => [], 'NPP' => [], 'NPT' => []];
                }
            }
            
            $tipo = $result['assessment_type'];
            if (in_array($tipo, ['AC', 'NPP', 'NPT'])) {
                $data['resultados'][$enrollmentId][$periodo][$tipo][] = $result['score'];
            }
        }
        
        // Calcular médias
        $data['medias'] = [];
        $totalMFD = 0;
        $alunosComMFD = 0;
        $aprovados = 0;
        $recurso = 0;
        $reprovados = 0;
        
        foreach ($data['alunos'] as $aluno) {
            $enrollmentId = $aluno['enrollment_id'];
            $alunoMedias = [
                'aluno' => $aluno,
                'periodos' => []
            ];
            
            $somaMT = 0;
            $periodosCompletos = 0;
            
            for ($p = 1; $p <= $maxPeriodos; $p++) {
                $notas = $data['resultados'][$enrollmentId][$p] ?? [];
                
                $ac = !empty($notas['AC']) ? round(array_sum($notas['AC']) / count($notas['AC']), 1) : '—';
                $npp = !empty($notas['NPP']) ? round(array_sum($notas['NPP']) / count($notas['NPP']), 1) : '—';
                $npt = !empty($notas['NPT']) ? round(array_sum($notas['NPT']) / count($notas['NPT']), 1) : '—';
                
                $notasValidas = [];
                if ($ac !== '—') $notasValidas[] = $ac;
                if ($npp !== '—') $notasValidas[] = $npp;
                if ($npt !== '—') $notasValidas[] = $npt;
                
                $mt = count($notasValidas) == 3 ? round(array_sum($notasValidas) / 3, 1) : '—';
                
                if ($mt !== '—') {
                    $somaMT += $mt;
                    $periodosCompletos++;
                }
                
                $alunoMedias['periodos'][$p] = [
                    'AC' => $ac,
                    'NPP' => $npp,
                    'NPT' => $npt,
                    'MT' => $mt
                ];
            }
            
            $mdf = ($periodosCompletos == $maxPeriodos) ? round($somaMT / $maxPeriodos, 1) : '—';
            $alunoMedias['MDF'] = $mdf;
            
            if ($mdf !== '—') {
                $totalMFD += $mdf;
                $alunosComMFD++;
                
                if ($mdf >= 10) {
                    $alunoMedias['situacao'] = 'Aprovado';
                    $alunoMedias['situacaoClass'] = 'success';
                    $aprovados++;
                } elseif ($mdf >= 7) {
                    $alunoMedias['situacao'] = 'Recurso';
                    $alunoMedias['situacaoClass'] = 'warning';
                    $recurso++;
                } else {
                    $alunoMedias['situacao'] = 'Reprovado';
                    $alunoMedias['situacaoClass'] = 'danger';
                    $reprovados++;
                }
            } else {
                $alunoMedias['situacao'] = 'Pendente';
                $alunoMedias['situacaoClass'] = 'secondary';
            }
            
            $data['medias'][$enrollmentId] = $alunoMedias;
        }
        
        // Estatísticas da disciplina
        $data['estatisticas'] = [
            'total' => count($data['alunos']),
            'aprovados' => $aprovados,
            'recurso' => $recurso,
            'reprovados' => $reprovados,
            'media_geral' => $alunosComMFD > 0 ? round($totalMFD / $alunosComMFD, 1) : '—',
            'taxa_aprovacao' => count($data['alunos']) > 0 ? round(($aprovados / count($data['alunos'])) * 100, 1) : 0
        ];
        
        log_view('mini_grade_sheet_disciplina', $disciplineId, 'Visualizou pauta da disciplina: ' . $data['discipline']['discipline_name']);
        
        return view('admin/mini_grade_sheet/disciplina_view', $data);
    }
    
    /**
     * Get mini pauta data with all results grouped by assessment type
     * @param int $examScheduleId ID do agendamento
     * @return array|null Dados completos da mini pauta
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
                tbl_academic_years.id as academic_year_id,
                tbl_users.first_name as teacher_first_name,
                tbl_users.last_name as teacher_last_name
            ')
            ->join('tbl_classes', 'tbl_classes.id = tbl_exam_schedules.class_id')
            ->join('tbl_courses', 'tbl_courses.id = tbl_classes.course_id', 'left')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id')
            ->join('tbl_exam_periods', 'tbl_exam_periods.id = tbl_exam_schedules.exam_period_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_exam_periods.academic_year_id')
            ->join('tbl_class_disciplines', 'tbl_class_disciplines.class_id = tbl_exam_schedules.class_id 
                    AND tbl_class_disciplines.discipline_id = tbl_exam_schedules.discipline_id', 'left')
            ->join('tbl_users', 'tbl_users.id = tbl_class_disciplines.teacher_id', 'left')
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
            ->where('tbl_enrollments.class_id', $schedule['class_id'])
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
            ->where('tbl_exam_schedules.class_id', $schedule['class_id'])
            ->where('tbl_exam_schedules.discipline_id', $schedule['discipline_id'])
            ->where('tbl_exam_periods.academic_year_id', $schedule['academic_year_id'])
            ->orderBy('tbl_semesters.start_date', 'ASC')
            ->orderBy('tbl_exam_results.assessment_type', 'ASC')
            ->findAll();
        
        // Mapear períodos
        $periodMap = [
            '1º Trimestre' => 1,
            '2º Trimestre' => 2,
            '3º Trimestre' => 3,
            '1º Semestre' => 1,
            '2º Semestre' => 2
        ];
        
        // Determinar quantos períodos existem
        $periodTypes = array_unique(array_column($resultados, 'semester_type'));
        $maxPeriodos = 3;
        foreach ($periodTypes as $type) {
            if (strpos($type, 'Semestre') !== false) {
                $maxPeriodos = 2;
                break;
            }
        }
        
        // Organizar notas por aluno, período e tipo
        $notasPorAluno = [];
        
        foreach ($alunos as $aluno) {
            $enrollmentId = $aluno['enrollment_id'];
            $notasPorAluno[$enrollmentId] = [
                'aluno' => $aluno,
                'notas' => []
            ];
            for ($p = 1; $p <= $maxPeriodos; $p++) {
                $notasPorAluno[$enrollmentId]['notas'][$p] = ['AC' => [], 'NPP' => [], 'NPT' => []];
            }
        }
        
        // Agrupar resultados por aluno, período e tipo
        foreach ($resultados as $resultado) {
            $enrollmentId = $resultado['enrollment_id'];
            $periodo = $periodMap[$resultado['semester_type']] ?? 1;
            $tipo = $resultado['assessment_type'];
            
            if (isset($notasPorAluno[$enrollmentId]) && in_array($tipo, ['AC', 'NPP', 'NPT'])) {
                $notasPorAluno[$enrollmentId]['notas'][$periodo][$tipo][] = $resultado['score'];
            }
        }
        
        // Calcular médias
        $mediasPorAluno = [];
        $totalMFD = 0;
        $alunosComMFD = 0;
        $aprovados = 0;
        $recurso = 0;
        $reprovados = 0;
        
        foreach ($notasPorAluno as $enrollmentId => $dados) {
            $aluno = $dados['aluno'];
            $notasPeriodos = $dados['notas'];
            
            $medias = [
                'aluno' => $aluno,
                'trimestres' => []
            ];
            
            $somaMT = 0;
            $periodosCompletos = 0;
            
            for ($p = 1; $p <= $maxPeriodos; $p++) {
                $notasPeriodo = $notasPeriodos[$p];
                
                $mediaAC = !empty($notasPeriodo['AC']) ? round(array_sum($notasPeriodo['AC']) / count($notasPeriodo['AC']), 1) : '—';
                $mediaNPP = !empty($notasPeriodo['NPP']) ? round(array_sum($notasPeriodo['NPP']) / count($notasPeriodo['NPP']), 1) : '—';
                $mediaNPT = !empty($notasPeriodo['NPT']) ? round(array_sum($notasPeriodo['NPT']) / count($notasPeriodo['NPT']), 1) : '—';
                
                $notasValidas = [];
                if ($mediaAC !== '—') $notasValidas[] = $mediaAC;
                if ($mediaNPP !== '—') $notasValidas[] = $mediaNPP;
                if ($mediaNPT !== '—') $notasValidas[] = $mediaNPT;
                
                $mt = count($notasValidas) == 3 ? round(array_sum($notasValidas) / 3, 1) : '—';
                
                if ($mt !== '—') {
                    $somaMT += $mt;
                    $periodosCompletos++;
                }
                
                $medias['trimestres'][$p] = [
                    'AC' => $mediaAC,
                    'NPP' => $mediaNPP,
                    'NPT' => $mediaNPT,
                    'MT' => $mt
                ];
            }
            
            $mdf = ($periodosCompletos == $maxPeriodos) ? round($somaMT / $maxPeriodos, 1) : '—';
            $medias['MDF'] = $mdf;
            
            if ($mdf !== '—') {
                $totalMFD += $mdf;
                $alunosComMFD++;
                
                if ($mdf >= 10) {
                    $medias['situacao'] = 'Aprovado';
                    $medias['situacaoClass'] = 'success';
                    $aprovados++;
                } elseif ($mdf >= 7) {
                    $medias['situacao'] = 'Recurso';
                    $medias['situacaoClass'] = 'warning';
                    $recurso++;
                } else {
                    $medias['situacao'] = 'Reprovado';
                    $medias['situacaoClass'] = 'danger';
                    $reprovados++;
                }
            } else {
                $medias['situacao'] = 'Pendente';
                $medias['situacaoClass'] = 'secondary';
            }
            
            $mediasPorAluno[$enrollmentId] = $medias;
        }
        
        // Calcular estatísticas
        $estatisticas = [
            'totalAlunos' => count($alunos),
            'aprovados' => $aprovados,
            'recurso' => $recurso,
            'reprovados' => $reprovados,
            'pendentes' => count($alunos) - ($aprovados + $recurso + $reprovados),
            'mediaTurma' => $alunosComMFD > 0 ? round($totalMFD / $alunosComMFD, 1) : '—',
            'taxaAprovacao' => count($alunos) > 0 ? round(($aprovados / count($alunos)) * 100, 1) : 0
        ];
        
        return [
            'schedule' => $schedule,
            'alunos' => $alunos,
            'medias' => $mediasPorAluno,
            'estatisticas' => $estatisticas
        ];
    }
    
    /**
     * Exportar pauta trimestral para Excel
     */
    public function exportTrimestral($classId)
    {
        $semesterId = $this->request->getGet('semester');
        
        if (!$semesterId) {
            return redirect()->back()->with('error', 'Selecione um período (trimestre/semestre)');
        }
        
        // Buscar dados da turma e período
        $class = $this->classModel->find($classId);
        $semester = $this->semesterModel->find($semesterId);
        
        if (!$class || !$semester) {
            log_error('Dados não encontrados para exportação trimestral', ['class_id' => $classId, 'semester_id' => $semesterId]);
            return redirect()->back()->with('error', 'Dados não encontrados');
        }
        
        // Buscar dados da pauta trimestral
        $data = $this->trimestralClass($classId, $semesterId);
        
        if (!$data) {
            return redirect()->back()->with('error', 'Erro ao gerar dados para exportação');
        }
        
        log_export('trimestral', "Exportou pauta trimestral da turma: {$class['class_name']}", ['class_id' => $classId, 'semester_id' => $semesterId]);
        
        // Gerar Excel (implementar depois)
        return redirect()->back()->with('info', 'Exportação em desenvolvimento');
    }
    
    /**
     * Exportar pauta de disciplina para Excel
     */
    public function exportDisciplina($classId, $disciplineId)
    {
        // Buscar dados da disciplina
        $class = $this->classModel->find($classId);
        $discipline = $this->disciplineModel->find($disciplineId);
        
        if (!$class || !$discipline) {
            log_error('Dados não encontrados para exportação de disciplina', ['class_id' => $classId, 'discipline_id' => $disciplineId]);
            return redirect()->back()->with('error', 'Dados não encontrados');
        }
        
        // Buscar dados da pauta da disciplina
        $data = $this->disciplinaView($classId, $disciplineId);
        
        if (!$data) {
            return redirect()->back()->with('error', 'Erro ao gerar dados para exportação');
        }
        
        log_export('disciplina', "Exportou pauta da disciplina: {$discipline['discipline_name']}", ['class_id' => $classId, 'discipline_id' => $disciplineId]);
        
        // Gerar Excel (implementar depois)
        return redirect()->back()->with('info', 'Exportação em desenvolvimento');
    }
}