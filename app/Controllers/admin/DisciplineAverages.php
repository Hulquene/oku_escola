<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\DisciplineAverageModel;
use App\Models\EnrollmentModel;
use App\Models\ClassModel;
use App\Models\DisciplineModel;
use App\Models\SemesterModel;
use App\Models\StudentModel;
use App\Models\ExamResultModel;
use App\Models\GradeWeightModel;

class DisciplineAverages extends BaseController
{
    protected $disciplineAverageModel;
    protected $enrollmentModel;
    protected $classModel;
    protected $disciplineModel;
    protected $semesterModel;
    protected $studentModel;
    protected $examResultModel;
    protected $gradeWeightModel;

    public function __construct()
    {
        $this->disciplineAverageModel = new DisciplineAverageModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->classModel = new ClassModel();
        $this->disciplineModel = new DisciplineModel();
        $this->semesterModel = new SemesterModel();
        $this->studentModel = new StudentModel();
        $this->examResultModel = new ExamResultModel();
        $this->gradeWeightModel = new GradeWeightModel();
    }

     public function index()
{
    $data['title'] = 'Médias Disciplinares';
    
    // Get current semester
    $currentSemester = $this->semesterModel->getCurrent();
    $semesterId = $this->request->getGet('semester') ?: ($currentSemester->id ?? null);
    
    // Get filters
    $classId = $this->request->getGet('class_id');
    $disciplineId = $this->request->getGet('discipline_id');
    
    $data['semesters'] = $this->semesterModel->getActive();
    $data['classes'] = $this->classModel->where('is_active', 1)->findAll();
    $data['disciplines'] = $this->disciplineModel->where('is_active', 1)->findAll();
    
    $data['selectedSemester'] = $semesterId;
    $data['selectedClass'] = $classId;
    $data['selectedDiscipline'] = $disciplineId;
    
    // Get summary statistics
    $data['totalAverages'] = $this->disciplineAverageModel->countAll();
    $data['averagesThisSemester'] = $semesterId ? 
        $this->disciplineAverageModel->where('semester_id', $semesterId)->countAllResults() : 0;
    
    // Get recent averages
    $data['recentAverages'] = $this->disciplineAverageModel
        ->select('
            tbl_discipline_averages.*,
            tbl_disciplines.discipline_name,
            tbl_users.first_name,
            tbl_users.last_name,
            tbl_students.student_number,
            tbl_classes.class_name,
            tbl_enrollments.id as enrollment_id
        ')
        ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_discipline_averages.enrollment_id')
        ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
        ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
        ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
        ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_discipline_averages.discipline_id')
        ->orderBy('tbl_discipline_averages.id', 'DESC')
        ->limit(20)
        ->findAll();
    
    return view('admin/exams/discipline-averages/index', $data);
}
    /**
     * View averages for a student in a specific semester
     */
    public function student($enrollmentId, $semesterId)
    {
        // Get enrollment details
        $enrollment = $this->enrollmentModel
            ->select('
                tbl_enrollments.*,
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

        // Get averages for this student in this semester
        $averages = $this->disciplineAverageModel
            ->select('
                tbl_discipline_averages.*,
                tbl_disciplines.discipline_name,
                tbl_disciplines.discipline_code,
                tbl_disciplines.workload_hours
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_discipline_averages.discipline_id')
            ->where('tbl_discipline_averages.enrollment_id', $enrollmentId)
            ->where('tbl_discipline_averages.semester_id', $semesterId)
            ->orderBy('tbl_disciplines.discipline_name', 'ASC')
            ->findAll();

        // Calculate statistics
        $stats = $this->calculateStudentStats($averages);

        $data['title'] = 'Médias Disciplinares - ' . $enrollment->first_name . ' ' . $enrollment->last_name;
        $data['enrollment'] = $enrollment;
        $data['semester'] = $semester;
        $data['averages'] = $averages;
        $data['stats'] = $stats;

        return view('admin/exams/discipline-averages/student', $data);
    }

    /**
     * View averages for an entire class in a specific semester
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

        // Get all active students in this class
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

        // Get all disciplines for this class
        $disciplines = $this->getClassDisciplines($classId, $semesterId);

        // Build matrix of averages
        $matrix = [];
        $studentAverages = [];
        $disciplineStats = [];

        foreach ($students as $student) {
            $averages = $this->disciplineAverageModel
                ->where('enrollment_id', $student->enrollment_id)
                ->where('semester_id', $semesterId)
                ->findAll();

            $studentData = [
                'enrollment_id' => $student->enrollment_id,
                'student_name' => $student->first_name . ' ' . $student->last_name,
                'student_number' => $student->student_number,
                'averages' => []
            ];

            $totalScore = 0;
            $count = 0;

            foreach ($averages as $avg) {
                $studentData['averages'][$avg->discipline_id] = [
                    'score' => $avg->final_score,
                    'status' => $avg->status
                ];
                $totalScore += $avg->final_score;
                $count++;
            }

            $studentData['overall_average'] = $count > 0 ? round($totalScore / $count, 2) : 0;
            $studentAverages[] = $studentData;
        }

        // Calculate discipline statistics
        foreach ($disciplines as $discipline) {
            $scores = [];
            $statusCount = ['Aprovado' => 0, 'Recurso' => 0, 'Reprovado' => 0];

            foreach ($studentAverages as $student) {
                if (isset($student['averages'][$discipline->id])) {
                    $score = $student['averages'][$discipline->id]['score'];
                    $scores[] = $score;
                    $statusCount[$student['averages'][$discipline->id]['status']]++;
                }
            }

            $disciplineStats[$discipline->id] = [
                'name' => $discipline->discipline_name,
                'code' => $discipline->discipline_code,
                'avg_score' => !empty($scores) ? round(array_sum($scores) / count($scores), 2) : 0,
                'max_score' => !empty($scores) ? max($scores) : 0,
                'min_score' => !empty($scores) ? min($scores) : 0,
                'total_students' => count($students),
                'approved' => $statusCount['Aprovado'],
                'appeal' => $statusCount['Recurso'],
                'failed' => $statusCount['Reprovado']
            ];
        }

        // Calculate class overall statistics
        $classStats = $this->calculateClassStats($studentAverages);

        $data['title'] = 'Médias da Turma - ' . $class->class_name;
        $data['class'] = $class;
        $data['semester'] = $semester;
        $data['students'] = $studentAverages;
        $data['disciplines'] = $disciplines;
        $data['disciplineStats'] = $disciplineStats;
        $data['classStats'] = $classStats;

        return view('admin/exams/discipline-averages/class', $data);
    }

    /**
     * Export averages to Excel/PDF
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

        // Get data
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

        $disciplines = $this->getClassDisciplines($classId, $semesterId);
        
        $averages = [];
        foreach ($students as $student) {
            $studentAverages = $this->disciplineAverageModel
                ->where('enrollment_id', $student->enrollment_id)
                ->where('semester_id', $semesterId)
                ->findAll();
            
            $averages[$student->enrollment_id] = [];
            foreach ($studentAverages as $avg) {
                $averages[$student->enrollment_id][$avg->discipline_id] = $avg;
            }
        }

        if ($type === 'excel') {
            return $this->exportToExcel($class, $semester, $students, $disciplines, $averages);
        } else {
            return $this->exportToPDF($class, $semester, $students, $disciplines, $averages);
        }
    }

    /**
     * Calculate statistics for a student
     */
    private function calculateStudentStats($averages)
    {
        $stats = [
            'total' => count($averages),
            'approved' => 0,
            'appeal' => 0,
            'failed' => 0,
            'average' => 0,
            'best' => 0,
            'worst' => 20
        ];

        $totalScore = 0;

        foreach ($averages as $avg) {
            $totalScore += $avg->final_score;

            if ($avg->final_score > $stats['best']) {
                $stats['best'] = $avg->final_score;
            }
            if ($avg->final_score < $stats['worst']) {
                $stats['worst'] = $avg->final_score;
            }

            switch ($avg->status) {
                case 'Aprovado':
                    $stats['approved']++;
                    break;
                case 'Recurso':
                    $stats['appeal']++;
                    break;
                case 'Reprovado':
                    $stats['failed']++;
                    break;
            }
        }

        if ($stats['total'] > 0) {
            $stats['average'] = round($totalScore / $stats['total'], 2);
        }

        return $stats;
    }

    /**
     * Calculate class statistics
     */
    private function calculateClassStats($studentAverages)
    {
        $stats = [
            'total_students' => count($studentAverages),
            'overall_average' => 0,
            'approved_students' => 0,
            'appeal_students' => 0,
            'failed_students' => 0,
            'total_average_sum' => 0
        ];

        foreach ($studentAverages as $student) {
            $stats['total_average_sum'] += $student['overall_average'];

            // Determine student's overall status (simplified - needs refinement)
            $hasFailed = false;
            $hasAppeal = false;

            foreach ($student['averages'] as $avg) {
                if ($avg['status'] === 'Reprovado') {
                    $hasFailed = true;
                } elseif ($avg['status'] === 'Recurso') {
                    $hasAppeal = true;
                }
            }

            if ($hasFailed) {
                $stats['failed_students']++;
            } elseif ($hasAppeal) {
                $stats['appeal_students']++;
            } else {
                $stats['approved_students']++;
            }
        }

        if ($stats['total_students'] > 0) {
            $stats['overall_average'] = round($stats['total_average_sum'] / $stats['total_students'], 2);
        }

        return $stats;
    }

    /**
     * Get disciplines for a class in a specific semester
     */
    private function getClassDisciplines($classId, $semesterId)
    {
        $classDisciplineModel = new \App\Models\ClassDisciplineModel();

        return $classDisciplineModel
            ->select('
                tbl_disciplines.id,
                tbl_disciplines.discipline_name,
                tbl_disciplines.discipline_code,
                tbl_disciplines.workload_hours
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
            ->where('tbl_class_disciplines.class_id', $classId)
            ->where('tbl_class_disciplines.semester_id', $semesterId)
            ->where('tbl_class_disciplines.is_active', 1)
            ->orderBy('tbl_disciplines.discipline_name', 'ASC')
            ->findAll();
    }

    /**
     * Export to Excel
     */
    private function exportToExcel($class, $semester, $students, $disciplines, $averages)
    {
        // Load Excel library
        $excel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $excel->getActiveSheet();

        // Set headers
        $sheet->setCellValue('A1', 'Médias Disciplinares - ' . $class->class_name);
        $sheet->setCellValue('A2', $semester->semester_name . ' - ' . $class->year_name);
        $sheet->mergeCells('A1:' . $this->getColumnLetter(count($disciplines) + 3) . '1');
        $sheet->mergeCells('A2:' . $this->getColumnLetter(count($disciplines) + 3) . '2');

        // Headers
        $sheet->setCellValue('A4', 'Nº');
        $sheet->setCellValue('B4', 'Nº Aluno');
        $sheet->setCellValue('C4', 'Nome do Aluno');

        $col = 'D';
        foreach ($disciplines as $discipline) {
            $sheet->setCellValue($col . '4', $discipline->discipline_name);
            $col++;
        }
        $sheet->setCellValue($col . '4', 'Média Geral');

        // Data
        $row = 5;
        foreach ($students as $index => $student) {
            $sheet->setCellValue('A' . $row, $index + 1);
            $sheet->setCellValue('B' . $row, $student->student_number);
            $sheet->setCellValue('C' . $row, $student->first_name . ' ' . $student->last_name);

            $col = 'D';
            $totalScore = 0;
            $count = 0;

            foreach ($disciplines as $discipline) {
                $score = isset($averages[$student->enrollment_id][$discipline->id]) 
                    ? $averages[$student->enrollment_id][$discipline->id]->final_score 
                    : '-';
                
                $sheet->setCellValue($col . $row, $score);
                
                if (is_numeric($score)) {
                    $totalScore += $score;
                    $count++;
                }
                
                $col++;
            }

            $overallAverage = $count > 0 ? round($totalScore / $count, 2) : '-';
            $sheet->setCellValue($col . $row, $overallAverage);
            $row++;
        }

        // Style
        $sheet->getStyle('A1:' . $col . '4')->getFont()->setBold(true);
        $sheet->getStyle('A4:' . $col . ($row - 1))->getBorders()->getAllBorders()->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        // Auto-size columns
        foreach (range('A', $col) as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        // Set filename
        $filename = 'medias_' . $class->class_code . '_' . $semester->semester_name . '.xlsx';

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
    private function exportToPDF($class, $semester, $students, $disciplines, $averages)
    {
        // Load TCPDF library
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

        // Set document information
        $pdf->SetCreator('Sistema Escolar');
        $pdf->SetAuthor('Admin');
        $pdf->SetTitle('Médias da Turma - ' . $class->class_name);

        // Remove header/footer
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);

        // Add a page
        $pdf->AddPage('L', 'A4');

        // Title
        $pdf->SetFont('helvetica', 'B', 16);
        $pdf->Cell(0, 10, 'Médias Disciplinares - ' . $class->class_name, 0, 1, 'C');
        $pdf->SetFont('helvetica', '', 12);
        $pdf->Cell(0, 10, $semester->semester_name . ' - ' . $class->year_name, 0, 1, 'C');
        $pdf->Ln(5);

        // Table headers
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->SetFillColor(240, 240, 240);

        $html = '<table border="1" cellpadding="4">
            <thead>
                <tr style="background-color: #f0f0f0; font-weight: bold;">
                    <th width="5%">Nº</th>
                    <th width="10%">Nº Aluno</th>
                    <th width="25%">Nome do Aluno</th>';

        foreach ($disciplines as $discipline) {
            $html .= '<th width="' . (60 / count($disciplines)) . '%">' . $discipline->discipline_name . '</th>';
        }

        $html .= '<th width="10%">Média</th>
                </tr>
            </thead>
            <tbody>';

        // Data rows
        $pdf->SetFont('helvetica', '', 9);

        foreach ($students as $index => $student) {
            $html .= '<tr>
                <td>' . ($index + 1) . '</td>
                <td>' . $student->student_number . '</td>
                <td>' . $student->first_name . ' ' . $student->last_name . '</td>';

            $totalScore = 0;
            $count = 0;

            foreach ($disciplines as $discipline) {
                $score = isset($averages[$student->enrollment_id][$discipline->id]) 
                    ? number_format($averages[$student->enrollment_id][$discipline->id]->final_score, 1)
                    : '-';
                
                $html .= '<td>' . $score . '</td>';
                
                if (is_numeric($score)) {
                    $totalScore += $score;
                    $count++;
                }
            }

            $overallAverage = $count > 0 ? number_format($totalScore / $count, 2) : '-';
            $html .= '<td><strong>' . $overallAverage . '</strong></td>';
            $html .= '</tr>';
        }

        $html .= '</tbody></table>';

        // Output the HTML
        $pdf->writeHTML($html, true, false, true, false, '');

        // Close and output PDF
        $filename = 'medias_' . $class->class_code . '_' . $semester->semester_name . '.pdf';
        $pdf->Output($filename, 'D');
        exit;
    }

    /**
     * Convert column number to letter (A, B, C, ...)
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
     * API endpoint to get averages for a specific student (AJAX)
     */
    public function getStudentAverages($enrollmentId, $semesterId)
    {
        $averages = $this->disciplineAverageModel
            ->select('
                tbl_discipline_averages.*,
                tbl_disciplines.discipline_name,
                tbl_disciplines.discipline_code
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_discipline_averages.discipline_id')
            ->where('tbl_discipline_averages.enrollment_id', $enrollmentId)
            ->where('tbl_discipline_averages.semester_id', $semesterId)
            ->findAll();

        return $this->response->setJSON([
            'success' => true,
            'data' => $averages
        ]);
    }

    /**
     * API endpoint to recalculate averages for a student (AJAX)
     */
    public function recalculateStudent($enrollmentId, $semesterId)
    {
        // Get all disciplines for this student in this semester
        $enrollment = $this->enrollmentModel->find($enrollmentId);
        
        if (!$enrollment) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Matrícula não encontrada'
            ]);
        }

        $disciplines = $this->getClassDisciplines($enrollment->class_id, $semesterId);
        $calculated = [];

        foreach ($disciplines as $discipline) {
            $result = $this->disciplineAverageModel->calculate(
                $enrollmentId,
                $discipline->id,
                $semesterId,
                session()->get('user_id')
            );

            if ($result) {
                $calculated[] = $discipline->discipline_name;
            }
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => count($calculated) . ' disciplinas recalculadas',
            'disciplines' => $calculated
        ]);
    }
}