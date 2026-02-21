<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\SemesterResultModel;
use App\Models\EnrollmentModel;
use App\Models\ClassModel;
use App\Models\SemesterModel;
use App\Models\StudentModel;
use App\Models\DisciplineAverageModel;

class SemesterResults extends BaseController
{
    protected $semesterResultModel;
    protected $enrollmentModel;
    protected $classModel;
    protected $semesterModel;
    protected $studentModel;
    protected $disciplineAverageModel;

    public function __construct()
    {
        $this->semesterResultModel = new SemesterResultModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->classModel = new ClassModel();
        $this->semesterModel = new SemesterModel();
        $this->studentModel = new StudentModel();
        $this->disciplineAverageModel = new DisciplineAverageModel();
    }

    /**
 * Index page - Dashboard de resultados semestrais
 */
public function index()
{
    $data['title'] = 'Resultados Semestrais';
    
    // Get current semester
    $currentSemester = $this->semesterModel->getCurrent();
    $semesterId = $this->request->getGet('semester') ?: ($currentSemester->id ?? null);
    
    // Get filters
    $classId = $this->request->getGet('class_id');
    
    $data['semesters'] = $this->semesterModel->getActive();
    $data['classes'] = $this->classModel->where('is_active', 1)->findAll();
    
    $data['selectedSemester'] = $semesterId;
    $data['selectedClass'] = $classId;
    
    // Get summary statistics
    $data['totalResults'] = $this->semesterResultModel->countAll();
    $data['resultsThisSemester'] = $semesterId ? 
        $this->semesterResultModel->where('semester_id', $semesterId)->countAllResults() : 0;
    
    // Get recent results
    $builder = $this->semesterResultModel
        ->select('
            tbl_semester_results.*,
            tbl_users.first_name,
            tbl_users.last_name,
            tbl_students.student_number,
            tbl_classes.class_name,
            tbl_semesters.semester_name
        ')
        ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_semester_results.enrollment_id')
        ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
        ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
        ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
        ->join('tbl_semesters', 'tbl_semesters.id = tbl_semester_results.semester_id');
    
    if ($semesterId) {
        $builder->where('tbl_semester_results.semester_id', $semesterId);
    }
    
    if ($classId) {
        $builder->where('tbl_enrollments.class_id', $classId);
    }
    
    $data['recentResults'] = $builder
        ->orderBy('tbl_semester_results.id', 'DESC')
        ->limit(20)
        ->findAll();
    
    return view('admin/exams/semester-results/index', $data);
}
    /**
     * View semester results for a specific student
     */
    public function student($enrollmentId, $semesterId)
    {
        // Get enrollment details
        $enrollment = $this->enrollmentModel
            ->select('
                tbl_enrollments.*,
                tbl_students.id as student_id,
                tbl_students.student_number,
                tbl_users.first_name,
                tbl_users.last_name,
                tbl_classes.class_name,
                tbl_classes.grade_level_id,
                tbl_academic_years.year_name
            ')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_enrollments.academic_year_id')
            ->find($enrollmentId);

        if (!$enrollment) {
            return redirect()->to('/admin/dashboard')
                ->with('error', 'Matrícula não encontrada.');
        }

        // Get semester details
        $semester = $this->semesterModel->find($semesterId);
        if (!$semester) {
            return redirect()->to('/admin/dashboard')
                ->with('error', 'Semestre não encontrado.');
        }

        // Get or calculate semester result
        $result = $this->semesterResultModel
            ->where('enrollment_id', $enrollmentId)
            ->where('semester_id', $semesterId)
            ->first();

        // If no result exists, calculate it
        if (!$result) {
            $resultId = $this->semesterResultModel->calculate(
                $enrollmentId,
                $semesterId,
                session()->get('user_id')
            );
            
            if ($resultId) {
                $result = $this->semesterResultModel->find($resultId);
            }
        }

        // Get discipline averages for detailed view
        $disciplineAverages = $this->disciplineAverageModel
            ->select('
                tbl_discipline_averages.*,
                tbl_disciplines.discipline_name,
                tbl_disciplines.discipline_code
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_discipline_averages.discipline_id')
            ->where('tbl_discipline_averages.enrollment_id', $enrollmentId)
            ->where('tbl_discipline_averages.semester_id', $semesterId)
            ->orderBy('tbl_disciplines.discipline_name', 'ASC')
            ->findAll();

        $data['title'] = 'Resultado Semestral - ' . $enrollment->first_name . ' ' . $enrollment->last_name;
        $data['enrollment'] = $enrollment;
        $data['semester'] = $semester;
        $data['result'] = $result;
        $data['disciplineAverages'] = $disciplineAverages;

        return view('admin/exams/semester-results/student', $data);
    }

    /**
     * View semester results for an entire class
     */
    public function class($classId, $semesterId)
    {
        // Get class details
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
            return redirect()->to('/admin/classes')
                ->with('error', 'Turma não encontrada.');
        }

        // Get semester details
        $semester = $this->semesterModel->find($semesterId);
        if (!$semester) {
            return redirect()->to('/admin/dashboard')
                ->with('error', 'Semestre não encontrado.');
        }

        // Calculate results for all students in class if not already done
        $this->semesterResultModel->calculateClassResults($classId, $semesterId, session()->get('user_id'));

        // Get all active students with their results
        $students = $this->enrollmentModel
            ->select('
                tbl_enrollments.id as enrollment_id,
                tbl_students.id as student_id,
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

        $results = [];
        foreach ($students as $student) {
            $result = $this->semesterResultModel
                ->where('enrollment_id', $student->enrollment_id)
                ->where('semester_id', $semesterId)
                ->first();

            if ($result) {
                $results[] = [
                    'enrollment_id' => $student->enrollment_id,
                    'student_number' => $student->student_number,
                    'student_name' => $student->first_name . ' ' . $student->last_name,
                    'overall_average' => $result->overall_average,
                    'total_disciplines' => $result->total_disciplines,
                    'approved' => $result->approved_disciplines,
                    'failed' => $result->failed_disciplines,
                    'appeal' => $result->appeal_disciplines,
                    'status' => $result->status
                ];
            }
        }

        // Get class summary
        $summary = $this->semesterResultModel->getClassSummary($classId, $semesterId);

        $data['title'] = 'Resultados Semestrais - ' . $class->class_name;
        $data['class'] = $class;
        $data['semester'] = $semester;
        $data['results'] = $results;
        $data['summary'] = $summary;

        return view('admin/exams/semester-results/class', $data);
    }

    /**
     * View summary statistics for a class/semester
     */
    public function summary($classId, $semesterId)
    {
        // Get class details
        $class = $this->classModel
            ->select('
                tbl_classes.*,
                tbl_grade_levels.level_name,
                tbl_academic_years.year_name
            ')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_classes.academic_year_id')
            ->find($classId);

        if (!$class) {
            return $this->response->setJSON(['success' => false, 'message' => 'Turma não encontrada']);
        }

        // Get semester
        $semester = $this->semesterModel->find($semesterId);
        if (!$semester) {
            return $this->response->setJSON(['success' => false, 'message' => 'Semestre não encontrado']);
        }

        // Get summary
        $summary = $this->semesterResultModel->getClassSummary($classId, $semesterId);

        // Get performance by discipline
        $disciplinePerformance = $this->disciplineAverageModel->getClassPerformance($classId, $semesterId);

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'class' => $class,
                'semester' => $semester,
                'summary' => $summary,
                'discipline_performance' => $disciplinePerformance
            ]
        ]);
    }

    /**
     * Export semester results
     */
    public function export($classId, $semesterId)
    {
        $type = $this->request->getGet('type') ?? 'excel'; // excel or pdf
        
        // Get class details
        $class = $this->classModel
            ->select('
                tbl_classes.*,
                tbl_grade_levels.level_name,
                tbl_academic_years.year_name
            ')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_classes.academic_year_id')
            ->find($classId);

        // Get semester
        $semester = $this->semesterModel->find($semesterId);

        // Get results
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
            ->findAll();

        $results = [];
        foreach ($students as $student) {
            $result = $this->semesterResultModel
                ->where('enrollment_id', $student->enrollment_id)
                ->where('semester_id', $semesterId)
                ->first();

            if ($result) {
                $results[] = [
                    'student_number' => $student->student_number,
                    'student_name' => $student->first_name . ' ' . $student->last_name,
                    'overall_average' => $result->overall_average,
                    'total_disciplines' => $result->total_disciplines,
                    'approved' => $result->approved_disciplines,
                    'failed' => $result->failed_disciplines,
                    'appeal' => $result->appeal_disciplines,
                    'status' => $result->status
                ];
            }
        }

        if ($type === 'excel') {
            return $this->exportToExcel($class, $semester, $results);
        } else {
            return $this->exportToPDF($class, $semester, $results);
        }
    }

    /**
     * Export to Excel
     */
    private function exportToExcel($class, $semester, $results)
    {
        // Load Excel library
        $excel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $excel->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'Resultados Semestrais - ' . $class->class_name);
        $sheet->setCellValue('A2', $semester->semester_name . ' - ' . $class->year_name);
        $sheet->mergeCells('A1:G1');
        $sheet->mergeCells('A2:G2');

        // Column headers
        $sheet->setCellValue('A4', 'Nº');
        $sheet->setCellValue('B4', 'Nº Aluno');
        $sheet->setCellValue('C4', 'Nome do Aluno');
        $sheet->setCellValue('D4', 'Média Geral');
        $sheet->setCellValue('E4', 'Aprovadas');
        $sheet->setCellValue('F4', 'Recurso');
        $sheet->setCellValue('G4', 'Reprovadas');
        $sheet->setCellValue('H4', 'Status');

        // Data rows
        $row = 5;
        foreach ($results as $index => $result) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $result['student_number']);
            $sheet->setCellValue('C' . $row, $result['student_name']);
            $sheet->setCellValue('D' . $row, number_format($result['overall_average'], 2));
            $sheet->setCellValue('E' . $row, $result['approved']);
            $sheet->setCellValue('F' . $row, $result['appeal']);
            $sheet->setCellValue('G' . $row, $result['failed']);
            $sheet->setCellValue('H' . $row, $result['status']);
            $row++;
        }

        // Summary row
        $totalStudents = count($results);
        $approvedCount = count(array_filter($results, fn($r) => $r['status'] === 'Aprovado'));
        $appealCount = count(array_filter($results, fn($r) => $r['status'] === 'Recurso'));
        $failedCount = count(array_filter($results, fn($r) => $r['status'] === 'Reprovado'));

        $row++;
        $sheet->setCellValue('C' . $row, 'RESUMO:');
        $sheet->setCellValue('D' . $row, 'Total: ' . $totalStudents);
        $sheet->setCellValue('E' . $row, 'Aprovados: ' . $approvedCount);
        $sheet->setCellValue('F' . $row, 'Recurso: ' . $appealCount);
        $sheet->setCellValue('G' . $row, 'Reprovados: ' . $failedCount);

        // Style
        $sheet->getStyle('A1:H4')->getFont()->setBold(true);
        $sheet->getStyle('A4:H' . ($row - 1))->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Auto-size columns
        foreach (range('A', 'H') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Set filename
        $filename = 'resultados_' . $class->class_code . '_' . $semester->semester_name . '.xlsx';

        // Output
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($excel);
        $writer->save('php://output');
        exit;
    }

    /**
     * Export to PDF
     */
    private function exportToPDF($class, $semester, $results)
    {
        // Load TCPDF library
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator('Sistema Escolar');
        $pdf->SetAuthor('Admin');
        $pdf->SetTitle('Resultados Semestrais - ' . $class->class_name);

        // Remove header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Add a page
        $pdf->AddPage('L', 'A4');

        // Title
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'Resultados Semestrais - ' . $class->class_name, 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, $semester->semester_name . ' - ' . $class->year_name, 0, 1, 'C');
        $pdf->Ln(5);

        // Table
        $html = '<table border="1" cellpadding="5">
            <thead>
                <tr style="background-color: #f0f0f0; font-weight: bold;">
                    <th width="5%">Nº</th>
                    <th width="10%">Nº Aluno</th>
                    <th width="25%">Nome do Aluno</th>
                    <th width="10%">Média</th>
                    <th width="10%">Aprov.</th>
                    <th width="10%">Recurso</th>
                    <th width="10%">Reprov.</th>
                    <th width="20%">Status</th>
                </tr>
            </thead>
            <tbody>';

        foreach ($results as $index => $result) {
            $statusColor = $result['status'] === 'Aprovado' ? '#d4edda' : 
                          ($result['status'] === 'Recurso' ? '#fff3cd' : '#f8d7da');
            
            $html .= '<tr>
                <td>' . ($index + 1) . '</td>
                <td>' . $result['student_number'] . '</td>
                <td>' . $result['student_name'] . '</td>
                <td align="center"><strong>' . number_format($result['overall_average'], 2) . '</strong></td>
                <td align="center">' . $result['approved'] . '</td>
                <td align="center">' . $result['appeal'] . '</td>
                <td align="center">' . $result['failed'] . '</td>
                <td align="center" style="background-color: ' . $statusColor . ';">' . $result['status'] . '</td>
            </tr>';
        }

        $html .= '</tbody></table>';

        // Summary
        $totalStudents = count($results);
        $approvedCount = count(array_filter($results, fn($r) => $r['status'] === 'Aprovado'));
        $appealCount = count(array_filter($results, fn($r) => $r['status'] === 'Recurso'));
        $failedCount = count(array_filter($results, fn($r) => $r['status'] === 'Reprovado'));

        $html .= '<br><br>
            <table border="0" cellpadding="5">
                <tr>
                    <td width="20%"><strong>RESUMO:</strong></td>
                    <td width="20%">Total de Alunos: ' . $totalStudents . '</td>
                    <td width="20%">Aprovados: ' . $approvedCount . '</td>
                    <td width="20%">Recurso: ' . $appealCount . '</td>
                    <td width="20%">Reprovados: ' . $failedCount . '</td>
                </tr>
            </table>';

        // Output the HTML
        $pdf->writeHTML($html, true, false, true, false, '');

        // Close and output PDF
        $filename = 'resultados_' . $class->class_code . '_' . $semester->semester_name . '.pdf';
        $pdf->Output($filename, 'D');
        exit;
    }

    /**
     * Generate report cards for all students in a class
     */
    public function generateReportCards($classId, $semesterId)
    {
        // Get class details
        $class = $this->classModel->find($classId);
        
        // Get all students with results
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
            ->findAll();

        // Generate PDF with all report cards
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        foreach ($students as $index => $student) {
            if ($index > 0) {
                $pdf->AddPage();
            }

            // Get student's results
            $result = $this->semesterResultModel
                ->where('enrollment_id', $student->enrollment_id)
                ->where('semester_id', $semesterId)
                ->first();

            $disciplines = $this->disciplineAverageModel
                ->select('
                    tbl_discipline_averages.*,
                    tbl_disciplines.discipline_name
                ')
                ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_discipline_averages.discipline_id')
                ->where('tbl_discipline_averages.enrollment_id', $student->enrollment_id)
                ->where('tbl_discipline_averages.semester_id', $semesterId)
                ->findAll();

            // Build report card HTML
            $html = $this->buildReportCardHTML($class, $student, $result, $disciplines);
            $pdf->writeHTML($html, true, false, true, false, '');
        }

        // Output PDF
        $filename = 'pautas_' . $class->class_code . '_' . $semesterId . '.pdf';
        $pdf->Output($filename, 'D');
        exit;
    }

    /**
     * Build report card HTML
     */
    private function buildReportCardHTML($class, $student, $result, $disciplines)
    {
        $html = '
        <style>
            .report-card { font-family: helvetica; width: 100%; }
            .header { text-align: center; margin-bottom: 20px; }
            .header h1 { font-size: 18px; margin: 0; }
            .header h2 { font-size: 14px; margin: 5px 0; color: #666; }
            .student-info { margin-bottom: 20px; }
            .student-info table { width: 100%; border-collapse: collapse; }
            .student-info td { padding: 5px; border: 1px solid #ddd; }
            .grades-table { width: 100%; border-collapse: collapse; margin: 20px 0; }
            .grades-table th { background-color: #f0f0f0; padding: 8px; border: 1px solid #ddd; }
            .grades-table td { padding: 8px; border: 1px solid #ddd; text-align: center; }
            .summary { margin-top: 20px; }
            .summary table { width: 50%; border-collapse: collapse; float: right; }
            .summary td { padding: 8px; border: 1px solid #ddd; }
            .footer { margin-top: 30px; text-align: center; font-size: 12px; color: #666; }
        </style>
        
        <div class="report-card">
            <div class="header">
                <h1>Pauta de Avaliação</h1>
                <h2>' . $class->class_name . ' - ' . $class->year_name . '</h2>
            </div>
            
            <div class="student-info">
                <table>
                    <tr>
                        <td width="20%"><strong>Aluno:</strong></td>
                        <td width="50%">' . $student->first_name . ' ' . $student->last_name . '</td>
                        <td width="15%"><strong>Nº:</strong></td>
                        <td width="15%">' . $student->student_number . '</td>
                    </tr>
                </table>
            </div>
            
            <table class="grades-table">
                <thead>
                    <tr>
                        <th width="50%">Disciplina</th>
                        <th width="20%">Nota Final</th>
                        <th width="30%">Status</th>
                    </tr>
                </thead>
                <tbody>';
        
        foreach ($disciplines as $disc) {
            $statusColor = $disc->status === 'Aprovado' ? '#d4edda' : 
                          ($disc->status === 'Recurso' ? '#fff3cd' : '#f8d7da');
            
            $html .= '
                    <tr>
                        <td align="left">' . $disc->discipline_name . '</td>
                        <td><strong>' . number_format($disc->final_score, 1) . '</strong></td>
                        <td style="background-color: ' . $statusColor . ';">' . $disc->status . '</td>
                    </tr>';
        }
        
        $html .= '
                </tbody>
            </table>
            
            <div class="summary">
                <table>
                    <tr>
                        <td><strong>Média Geral:</strong></td>
                        <td><strong>' . number_format($result->overall_average ?? 0, 2) . '</strong></td>
                    </tr>
                    <tr>
                        <td>Total de Disciplinas:</td>
                        <td>' . ($result->total_disciplines ?? 0) . '</td>
                    </tr>
                    <tr>
                        <td>Aprovadas:</td>
                        <td>' . ($result->approved_disciplines ?? 0) . '</td>
                    </tr>
                    <tr>
                        <td>Recurso:</td>
                        <td>' . ($result->appeal_disciplines ?? 0) . '</td>
                    </tr>
                    <tr>
                        <td>Reprovadas:</td>
                        <td>' . ($result->failed_disciplines ?? 0) . '</td>
                    </tr>
                    <tr>
                        <td><strong>Resultado Final:</strong></td>
                        <td><strong>' . ($result->status ?? 'Em Andamento') . '</strong></td>
                    </tr>
                </table>
            </div>
            
            <div class="footer">
                <p>Documento gerado em ' . date('d/m/Y H:i') . ' pelo Sistema de Gestão Escolar</p>
            </div>
        </div>';
        
        return $html;
    }
}