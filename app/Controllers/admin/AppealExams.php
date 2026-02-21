<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\EnrollmentModel;
use App\Models\ClassModel;
use App\Models\SemesterModel;
use App\Models\DisciplineAverageModel;
use App\Models\SemesterResultModel;
use App\Models\StudentModel;
use App\Models\ExamPeriodModel;
use App\Models\ExamScheduleModel;
use App\Models\ExamBoardModel;
use App\Models\ExamResultModel;
use App\Models\ExamAttendanceModel;

class AppealExams extends BaseController
{
    protected $enrollmentModel;
    protected $classModel;
    protected $semesterModel;
    protected $disciplineAverageModel;
    protected $semesterResultModel;
    protected $studentModel;
    protected $examPeriodModel;
    protected $examScheduleModel;
    protected $examBoardModel;
    protected $examResultModel;
    protected $examAttendanceModel;

    public function __construct()
    {
        $this->enrollmentModel = new EnrollmentModel();
        $this->classModel = new ClassModel();
        $this->semesterModel = new SemesterModel();
        $this->disciplineAverageModel = new DisciplineAverageModel();
        $this->semesterResultModel = new SemesterResultModel();
        $this->studentModel = new StudentModel();
        $this->examPeriodModel = new ExamPeriodModel();
        $this->examScheduleModel = new ExamScheduleModel();
        $this->examBoardModel = new ExamBoardModel();
        $this->examResultModel = new ExamResultModel();
        $this->examAttendanceModel = new ExamAttendanceModel();
    }

    /**
     * List all students in appeal situation
     */
    public function index()
    {
        $data['title'] = 'Alunos em Recurso';

        $classId = $this->request->getGet('class_id');
        $semesterId = $this->request->getGet('semester') ?: 
            ($this->semesterModel->getCurrent()->id ?? null);

        $semester = $this->semesterModel->find($semesterId);

        // Base query for students in appeal
        $builder = $this->disciplineAverageModel
            ->select('
                tbl_discipline_averages.*,
                tbl_enrollments.student_id,
                tbl_enrollments.class_id,
                tbl_students.student_number,
                tbl_users.first_name,
                tbl_users.last_name,
                tbl_disciplines.discipline_name,
                tbl_disciplines.discipline_code,
                tbl_classes.class_name
            ')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_discipline_averages.enrollment_id')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_discipline_averages.discipline_id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
            ->where('tbl_discipline_averages.semester_id', $semesterId)
            ->where('tbl_discipline_averages.status', 'Recurso');

        if ($classId) {
            $builder->where('tbl_enrollments.class_id', $classId);
        }

        $data['appealStudents'] = $builder->orderBy('tbl_classes.class_name', 'ASC')
            ->orderBy('tbl_users.first_name', 'ASC')
            ->findAll();

        // Group by class for better organization
        $grouped = [];
        foreach ($data['appealStudents'] as $item) {
            $key = $item->class_id . '_' . $item->class_name;
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'class_id' => $item->class_id,
                    'class_name' => $item->class_name,
                    'students' => []
                ];
            }
            $grouped[$key]['students'][] = $item;
        }
        $data['groupedAppeals'] = $grouped;

        // Get classes for filter
        $data['classes'] = $this->classModel
            ->where('is_active', 1)
            ->orderBy('class_name', 'ASC')
            ->findAll();

        $data['semesters'] = $this->semesterModel->getActive();
        $data['selectedClass'] = $classId;
        $data['selectedSemester'] = $semesterId;
        $data['semester'] = $semester;

        // Statistics
        $data['stats'] = [
            'total_students' => count(array_unique(array_column($data['appealStudents'], 'student_id'))),
            'total_disciplines' => count($data['appealStudents']),
            'total_classes' => count($grouped)
        ];

        return view('admin/exams/appeals/index', $data);
    }

    /**
     * Generate appeal exams automatically
     */
    public function generateAppealExams()
    {
        if ($this->request->getMethod() !== 'post') {
            return redirect()->to('/admin/exams/appeals')
                ->with('error', 'Método não permitido.');
        }

        $semesterId = $this->request->getPost('semester_id');
        $classIds = $this->request->getPost('class_ids') ?: [];
        $examDate = $this->request->getPost('exam_date');
        $examPeriodId = $this->request->getPost('exam_period_id');

        if (!$semesterId || !$examDate) {
            return redirect()->back()
                ->with('error', 'Semestre e data do exame são obrigatórios.');
        }

        // Get all students in appeal for selected classes
        $builder = $this->disciplineAverageModel
            ->select('
                tbl_discipline_averages.enrollment_id,
                tbl_discipline_averages.discipline_id,
                tbl_enrollments.class_id,
                tbl_enrollments.student_id
            ')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_discipline_averages.enrollment_id')
            ->where('tbl_discipline_averages.semester_id', $semesterId)
            ->where('tbl_discipline_averages.status', 'Recurso');

        if (!empty($classIds)) {
            $builder->whereIn('tbl_enrollments.class_id', $classIds);
        }

        $appeals = $builder->findAll();

        if (empty($appeals)) {
            return redirect()->back()
                ->with('warning', 'Nenhum aluno em situação de recurso encontrado.');
        }

        // Group by class and discipline
        $grouped = [];
        foreach ($appeals as $appeal) {
            $key = $appeal->class_id . '_' . $appeal->discipline_id;
            if (!isset($grouped[$key])) {
                $grouped[$key] = [
                    'class_id' => $appeal->class_id,
                    'discipline_id' => $appeal->discipline_id,
                    'enrollments' => []
                ];
            }
            $grouped[$key]['enrollments'][] = $appeal->enrollment_id;
        }

        // Get exam board for appeal exams
        $appealBoard = $this->examBoardModel
            ->where('board_type', 'Recurso')
            ->where('is_active', 1)
            ->first();

        if (!$appealBoard) {
            return redirect()->back()
                ->with('error', 'Tipo de exame de recurso não configurado.');
        }

        // Create exam period if not provided
        if (!$examPeriodId) {
            $semester = $this->semesterModel->find($semesterId);
            $periodData = [
                'period_name' => 'Exames de Recurso - ' . $semester->semester_name,
                'academic_year_id' => $semester->academic_year_id,
                'semester_id' => $semesterId,
                'period_type' => 'Recurso',
                'start_date' => $examDate,
                'end_date' => date('Y-m-d', strtotime($examDate . ' +7 days')),
                'status' => 'Planejado',
                'created_by' => session()->get('user_id')
            ];
            $examPeriodId = $this->examPeriodModel->insert($periodData);
        }

        // Create exam schedules
        $schedules = [];
        foreach ($grouped as $item) {
            // Check if exam already exists
            $existing = $this->examScheduleModel
                ->where('exam_period_id', $examPeriodId)
                ->where('class_id', $item['class_id'])
                ->where('discipline_id', $item['discipline_id'])
                ->first();

            if (!$existing) {
                $schedules[] = [
                    'exam_period_id' => $examPeriodId,
                    'class_id' => $item['class_id'],
                    'discipline_id' => $item['discipline_id'],
                    'exam_board_id' => $appealBoard->id,
                    'exam_date' => $examDate,
                    'exam_time' => '08:00:00',
                    'status' => 'Agendado'
                ];
            }
        }

        if (!empty($schedules)) {
            $this->examScheduleModel->insertBatch($schedules);
        }

        return redirect()->to('/admin/exams/appeals/scheduled/' . $semesterId)
            ->with('success', count($schedules) . ' exames de recurso gerados com sucesso!');
    }

    /**
     * View scheduled appeal exams
     */
    public function scheduled($semesterId)
    {
        $data['title'] = 'Exames de Recurso Agendados';
        
        $semester = $this->semesterModel->find($semesterId);
        $data['semester'] = $semester;

        // Get appeal exam periods
        $data['periods'] = $this->examPeriodModel
            ->where('semester_id', $semesterId)
            ->where('period_type', 'Recurso')
            ->orderBy('created_at', 'DESC')
            ->findAll();

        // Get all appeal exams
        $data['exams'] = $this->examScheduleModel
            ->select('
                tbl_exam_schedules.*,
                tbl_exam_periods.period_name,
                tbl_classes.class_name,
                tbl_disciplines.discipline_name,
                COUNT(tbl_exam_attendance.id) as total_students,
                COUNT(tbl_exam_results.id) as results_count
            ')
            ->join('tbl_exam_periods', 'tbl_exam_periods.id = tbl_exam_schedules.exam_period_id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_exam_schedules.class_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id')
            ->join('tbl_exam_attendance', 'tbl_exam_attendance.exam_schedule_id = tbl_exam_schedules.id', 'left')
            ->join('tbl_exam_results', 'tbl_exam_results.exam_schedule_id = tbl_exam_schedules.id', 'left')
            ->where('tbl_exam_periods.semester_id', $semesterId)
            ->where('tbl_exam_periods.period_type', 'Recurso')
            ->groupBy('tbl_exam_schedules.id')
            ->orderBy('tbl_exam_schedules.exam_date', 'ASC')
            ->findAll();

        return view('admin/exams/appeals/scheduled', $data);
    }

    /**
     * View students for a specific appeal exam
     */
    public function examStudents($examScheduleId)
    {
        $exam = $this->examScheduleModel
            ->select('
                tbl_exam_schedules.*,
                tbl_classes.class_name,
                tbl_disciplines.discipline_name,
                tbl_exam_periods.period_name
            ')
            ->join('tbl_classes', 'tbl_classes.id = tbl_exam_schedules.class_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id')
            ->join('tbl_exam_periods', 'tbl_exam_periods.id = tbl_exam_schedules.exam_period_id')
            ->find($examScheduleId);

        if (!$exam) {
            return redirect()->to('/admin/exams/appeals')
                ->with('error', 'Exame não encontrado.');
        }

        // Get students in appeal for this class and discipline
        $students = $this->disciplineAverageModel
            ->select('
                tbl_discipline_averages.enrollment_id,
                tbl_discipline_averages.final_score as previous_score,
                tbl_students.student_number,
                tbl_users.first_name,
                tbl_users.last_name
            ')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_discipline_averages.enrollment_id')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->where('tbl_enrollments.class_id', $exam->class_id)
            ->where('tbl_discipline_averages.discipline_id', $exam->discipline_id)
            ->where('tbl_discipline_averages.semester_id', $exam->semester_id)
            ->where('tbl_discipline_averages.status', 'Recurso')
            ->orderBy('tbl_users.first_name', 'ASC')
            ->findAll();

        // Get existing attendance if any
        $attendance = $this->examAttendanceModel
            ->where('exam_schedule_id', $examScheduleId)
            ->findAll();
        
        $attendanceMap = [];
        foreach ($attendance as $att) {
            $attendanceMap[$att->enrollment_id] = $att;
        }

        // Get existing results if any
        $results = $this->examResultModel
            ->where('exam_schedule_id', $examScheduleId)
            ->findAll();
        
        $resultsMap = [];
        foreach ($results as $res) {
            $resultsMap[$res->enrollment_id] = $res;
        }

        $data['title'] = 'Alunos em Recurso - ' . $exam->discipline_name;
        $data['exam'] = $exam;
        $data['students'] = $students;
        $data['attendance'] = $attendanceMap;
        $data['results'] = $resultsMap;

        return view('admin/exams/appeals/exam_students', $data);
    }

    /**
     * Register attendance for appeal exam
     */
    public function registerAttendance($examScheduleId)
    {
        $exam = $this->examScheduleModel->find($examScheduleId);

        if (!$exam) {
            return redirect()->to('/admin/exams/appeals')
                ->with('error', 'Exame não encontrado.');
        }

        if ($this->request->getMethod() === 'post') {
            $attendance = $this->request->getPost('attendance') ?: [];

            // Delete existing attendance
            $this->examAttendanceModel->where('exam_schedule_id', $examScheduleId)->delete();

            // Insert new attendance
            $attendanceData = [];
            foreach ($attendance as $enrollmentId => $value) {
                $attended = isset($value['attended']) ? 1 : 0;
                $attendanceData[] = [
                    'exam_schedule_id' => $examScheduleId,
                    'enrollment_id' => $enrollmentId,
                    'attended' => $attended,
                    'check_in_time' => $attended ? date('Y-m-d H:i:s') : null,
                    'check_in_method' => 'Manual',
                    'observations' => $value['observations'] ?? null,
                    'recorded_by' => session()->get('user_id')
                ];
            }

            if (!empty($attendanceData)) {
                $this->examAttendanceModel->insertBatch($attendanceData);
                
                return redirect()->to('/admin/exams/appeals/exam-students/' . $examScheduleId)
                    ->with('success', 'Presenças registadas com sucesso!');
            }
        }

        return redirect()->back()->with('error', 'Nenhum dado recebido.');
    }

    /**
     * Register results for appeal exam
     */
    public function registerResults($examScheduleId)
    {
        $exam = $this->examScheduleModel
            ->select('
                tbl_exam_schedules.*,
                tbl_exam_periods.semester_id
            ')
            ->join('tbl_exam_periods', 'tbl_exam_periods.id = tbl_exam_schedules.exam_period_id')
            ->find($examScheduleId);

        if (!$exam) {
            return redirect()->to('/admin/exams/appeals')
                ->with('error', 'Exame não encontrado.');
        }

        if ($this->request->getMethod() === 'post') {
            $scores = $this->request->getPost('scores') ?: [];

            // Delete existing results
            $this->examResultModel->where('exam_schedule_id', $examScheduleId)->delete();

            // Insert new results
            $resultsData = [];
            foreach ($scores as $enrollmentId => $score) {
                if ($score !== '' && is_numeric($score)) {
                    $resultsData[] = [
                        'exam_schedule_id' => $examScheduleId,
                        'enrollment_id' => $enrollmentId,
                        'score' => $score,
                        'is_absent' => 0,
                        'recorded_by' => session()->get('user_id')
                    ];
                }
            }

            if (!empty($resultsData)) {
                $this->examResultModel->insertBatch($resultsData);

                // Recalculate averages for each student
                foreach ($resultsData as $result) {
                    $this->recalculateAfterAppeal(
                        $result['enrollment_id'],
                        $exam->discipline_id,
                        $exam->semester_id,
                        $result['score']
                    );
                }

                return redirect()->to('/admin/exams/appeals/exam-students/' . $examScheduleId)
                    ->with('success', 'Notas registadas e médias recalculadas com sucesso!');
            }
        }

        return redirect()->back()->with('error', 'Nenhuma nota válida recebida.');
    }

    /**
     * Recalculate averages after appeal exam
     */
    private function recalculateAfterAppeal($enrollmentId, $disciplineId, $semesterId, $appealScore)
    {
        // Get previous average
        $previousAvg = $this->disciplineAverageModel
            ->where('enrollment_id', $enrollmentId)
            ->where('discipline_id', $disciplineId)
            ->where('semester_id', $semesterId)
            ->first();

        if (!$previousAvg) {
            return false;
        }

        // Calculate new final score (appeal replaces previous or averages?)
        // Rule: Appeal score replaces previous exam score, keeping AC scores
        $newFinalScore = ($previousAvg->ac_score + $appealScore) / 2;

        // Determine new status
        $newStatus = $newFinalScore >= 10 ? 'Aprovado' : 'Reprovado';

        // Update discipline average
        $this->disciplineAverageModel->update($previousAvg->id, [
            'exam_score' => $appealScore,
            'final_score' => $newFinalScore,
            'status' => $newStatus,
            'observations' => 'Após exame de recurso'
        ]);

        // Recalculate semester result for this student
        $this->semesterResultModel->calculate(
            $enrollmentId,
            $semesterId,
            session()->get('user_id')
        );

        return true;
    }

    /**
     * Summary of appeal results
     */
    public function summary($semesterId)
    {
        $data['title'] = 'Resumo dos Exames de Recurso';
        
        $semester = $this->semesterModel->find($semesterId);
        $data['semester'] = $semester;

        // Get all appeal exams
        $appealExams = $this->examScheduleModel
            ->select('
                tbl_exam_schedules.*,
                tbl_exam_periods.period_name,
                tbl_classes.class_name,
                tbl_disciplines.discipline_name,
                COUNT(tbl_exam_attendance.id) as total_students,
                COUNT(tbl_exam_results.id) as results_count,
                AVG(tbl_exam_results.score) as average_score
            ')
            ->join('tbl_exam_periods', 'tbl_exam_periods.id = tbl_exam_schedules.exam_period_id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_exam_schedules.class_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id')
            ->join('tbl_exam_attendance', 'tbl_exam_attendance.exam_schedule_id = tbl_exam_schedules.id', 'left')
            ->join('tbl_exam_results', 'tbl_exam_results.exam_schedule_id = tbl_exam_schedules.id', 'left')
            ->where('tbl_exam_periods.semester_id', $semesterId)
            ->where('tbl_exam_periods.period_type', 'Recurso')
            ->groupBy('tbl_exam_schedules.id')
            ->orderBy('tbl_exam_schedules.exam_date', 'ASC')
            ->findAll();

        // Statistics
        $stats = [
            'total_exams' => count($appealExams),
            'total_students' => 0,
            'total_present' => 0,
            'total_approved' => 0,
            'total_failed' => 0
        ];

        foreach ($appealExams as $exam) {
            $stats['total_students'] += $exam->total_students;
            $stats['total_present'] += $exam->results_count;

            // Get results for this exam
            $results = $this->examResultModel
                ->where('exam_schedule_id', $exam->id)
                ->findAll();

            foreach ($results as $result) {
                if ($result->score >= 10) {
                    $stats['total_approved']++;
                } else {
                    $stats['total_failed']++;
                }
            }
        }

        $data['appealExams'] = $appealExams;
        $data['stats'] = $stats;

        return view('admin/exams/appeals/summary', $data);
    }

    /**
     * Export appeal results
     */
    public function export($semesterId)
    {
        $type = $this->request->getGet('type') ?? 'excel';

        $semester = $this->semesterModel->find($semesterId);

        // Get all appeal results
        $results = $this->disciplineAverageModel
            ->select('
                tbl_discipline_averages.*,
                tbl_enrollments.student_id,
                tbl_students.student_number,
                tbl_users.first_name,
                tbl_users.last_name,
                tbl_disciplines.discipline_name,
                tbl_classes.class_name,
                tbl_semesters.semester_name
            ')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_discipline_averages.enrollment_id')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_discipline_averages.discipline_id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
            ->join('tbl_semesters', 'tbl_semesters.id = tbl_discipline_averages.semester_id')
            ->where('tbl_discipline_averages.semester_id', $semesterId)
            ->where('tbl_discipline_averages.status', 'Recurso')
            ->orderBy('tbl_classes.class_name', 'ASC')
            ->orderBy('tbl_users.first_name', 'ASC')
            ->findAll();

        if ($type === 'excel') {
            return $this->exportToExcel($semester, $results);
        } else {
            return $this->exportToPDF($semester, $results);
        }
    }

    /**
     * Export to Excel
     */
    private function exportToExcel($semester, $results)
    {
        $excel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $excel->getActiveSheet();

        // Headers
        $sheet->setCellValue('A1', 'Alunos em Recurso - ' . $semester->semester_name);
        $sheet->mergeCells('A1:G1');
        $sheet->getStyle('A1')->getFont()->setBold(true);

        $sheet->setCellValue('A3', 'Turma');
        $sheet->setCellValue('B3', 'Nº Aluno');
        $sheet->setCellValue('C3', 'Nome do Aluno');
        $sheet->setCellValue('D3', 'Disciplina');
        $sheet->setCellValue('E3', 'Nota Anterior');
        $sheet->setCellValue('F3', 'Situação');

        $row = 4;
        foreach ($results as $result) {
            $sheet->setCellValue('A' . $row, $result->class_name);
            $sheet->setCellValue('B' . $row, $result->student_number);
            $sheet->setCellValue('C' . $row, $result->first_name . ' ' . $result->last_name);
            $sheet->setCellValue('D' . $row, $result->discipline_name);
            $sheet->setCellValue('E' . $row, number_format($result->final_score, 1));
            $sheet->setCellValue('F' . $row, $result->status);
            $row++;
        }

        $sheet->getStyle('A3:F' . ($row - 1))->getBorders()->getAllBorders()
            ->setBorderStyle(\PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN);

        foreach (range('A', 'F') as $column) {
            $sheet->getColumnDimension($column)->setAutoSize(true);
        }

        $filename = 'alunos_recurso_' . $semester->semester_name . '_' . date('Ymd') . '.xlsx';

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
    private function exportToPDF($semester, $results)
    {
        $pdf = new \TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
        $pdf->setPrintHeader(false);
        $pdf->setPrintFooter(false);
        $pdf->AddPage();

        $html = '<h2>Alunos em Recurso - ' . $semester->semester_name . '</h2>
            <table border="1" cellpadding="5">
                <thead>
                    <tr style="background-color: #f0f0f0;">
                        <th>Turma</th>
                        <th>Nº Aluno</th>
                        <th>Nome</th>
                        <th>Disciplina</th>
                        <th>Nota</th>
                        <th>Situação</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($results as $result) {
            $html .= '<tr>
                <td>' . $result->class_name . '</td>
                <td>' . $result->student_number . '</td>
                <td>' . $result->first_name . ' ' . $result->last_name . '</td>
                <td>' . $result->discipline_name . '</td>
                <td align="center">' . number_format($result->final_score, 1) . '</td>
                <td align="center">' . $result->status . '</td>
            </tr>';
        }

        $html .= '</tbody></table>';

        $pdf->writeHTML($html, true, false, true, false, '');
        $pdf->Output('alunos_recurso_' . $semester->semester_name . '.pdf', 'D');
        exit;
    }

    /**
     * Get statistics for dashboard
     */
    public function getStats($semesterId)
    {
        $totalAppeals = $this->disciplineAverageModel
            ->where('semester_id', $semesterId)
            ->where('status', 'Recurso')
            ->countAllResults();

        $scheduledExams = $this->examScheduleModel
            ->join('tbl_exam_periods', 'tbl_exam_periods.id = tbl_exam_schedules.exam_period_id')
            ->where('tbl_exam_periods.semester_id', $semesterId)
            ->where('tbl_exam_periods.period_type', 'Recurso')
            ->countAllResults();

        $completedExams = $this->examScheduleModel
            ->join('tbl_exam_periods', 'tbl_exam_periods.id = tbl_exam_schedules.exam_period_id')
            ->where('tbl_exam_periods.semester_id', $semesterId)
            ->where('tbl_exam_periods.period_type', 'Recurso')
            ->where('tbl_exam_schedules.status', 'Realizado')
            ->countAllResults();

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'total_appeals' => $totalAppeals,
                'scheduled_exams' => $scheduledExams,
                'completed_exams' => $completedExams
            ]
        ]);
    }
}