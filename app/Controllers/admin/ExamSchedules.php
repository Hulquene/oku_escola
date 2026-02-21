<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\ExamScheduleModel;
use App\Models\ExamPeriodModel;
use App\Models\ClassModel;
use App\Models\DisciplineModel;
use App\Models\ExamBoardModel;
use App\Models\ExamAttendanceModel;
use App\Models\ExamResultModel;
use App\Models\EnrollmentModel;

class ExamSchedules extends BaseController
{
    protected $examScheduleModel;
    protected $examPeriodModel;
    protected $classModel;
    protected $disciplineModel;
    protected $examBoardModel;
    protected $examAttendanceModel;
    protected $examResultModel;
    protected $enrollmentModel;
    
    public function __construct()
    {
        $this->examScheduleModel = new ExamScheduleModel();
        $this->examPeriodModel = new ExamPeriodModel();
        $this->classModel = new ClassModel();
        $this->disciplineModel = new DisciplineModel();
        $this->examBoardModel = new ExamBoardModel();
        $this->examAttendanceModel = new ExamAttendanceModel();
        $this->examResultModel = new ExamResultModel();
        $this->enrollmentModel = new EnrollmentModel();
    }
    
    /**
     * List all exam schedules
     */
    public function index()
    {
        $data['title'] = 'Calendário de Exames';
        
        $periodId = $this->request->getGet('period_id');
        $classId = $this->request->getGet('class_id');
        $date = $this->request->getGet('date');
        
        $builder = $this->examScheduleModel
            ->select('
                tbl_exam_schedules.*,
                tbl_exam_periods.period_name,
                tbl_classes.class_name,
                tbl_disciplines.discipline_name,
                tbl_exam_boards.board_name,
                tbl_exam_boards.board_type
            ')
            ->join('tbl_exam_periods', 'tbl_exam_periods.id = tbl_exam_schedules.exam_period_id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_exam_schedules.class_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id')
            ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exam_schedules.exam_board_id');
        
        if ($periodId) {
            $builder->where('tbl_exam_schedules.exam_period_id', $periodId);
        }
        
        if ($classId) {
            $builder->where('tbl_exam_schedules.class_id', $classId);
        }
        
        if ($date) {
            $builder->where('tbl_exam_schedules.exam_date', $date);
        }
        
        $data['schedules'] = $builder->orderBy('exam_date', 'ASC')
            ->orderBy('exam_time', 'ASC')
            ->paginate(20);
        
        $data['pager'] = $this->examScheduleModel->pager;
        $data['periods'] = $this->examPeriodModel->findAll();
        $data['classes'] = $this->classModel->where('is_active', 1)->findAll();
        
        return view('admin/exams/schedules/index', $data);
    }
    
    /**
     * Create new exam schedule
     */
    public function create()
    {
        $data['title'] = 'Agendar Exame';
        
        if ($this->request->getMethod() === 'post') {
            $rules = [
                'exam_period_id' => 'required|numeric',
                'class_id' => 'required|numeric',
                'discipline_id' => 'required|numeric',
                'exam_board_id' => 'required|numeric',
                'exam_date' => 'required|valid_date',
                'exam_time' => 'permit_empty'
            ];
            
            // Check for conflicts
            $classId = $this->request->getPost('class_id');
            $examDate = $this->request->getPost('exam_date');
            $examTime = $this->request->getPost('exam_time');
            
            if ($this->examScheduleModel->checkConflicts($classId, $examDate, $examTime)) {
                return redirect()->back()
                    ->with('error', 'Já existe um exame agendado para esta turma nesta data/horário.')
                    ->withInput();
            }
            
            if ($this->validate($rules)) {
                $data = [
                    'exam_period_id' => $this->request->getPost('exam_period_id'),
                    'class_id' => $classId,
                    'discipline_id' => $this->request->getPost('discipline_id'),
                    'exam_board_id' => $this->request->getPost('exam_board_id'),
                    'exam_date' => $examDate,
                    'exam_time' => $examTime,
                    'exam_room' => $this->request->getPost('exam_room'),
                    'duration_minutes' => $this->request->getPost('duration_minutes') ?: 120,
                    'observations' => $this->request->getPost('observations'),
                    'status' => 'Agendado'
                ];
                
                if ($this->examScheduleModel->insert($data)) {
                    return redirect()->to('/admin/exam-schedules')
                        ->with('success', 'Exame agendado com sucesso!');
                } else {
                    return redirect()->back()
                        ->with('error', 'Erro ao agendar exame.')
                        ->withInput();
                }
            } else {
                return redirect()->back()
                    ->with('errors', $this->validator->getErrors())
                    ->withInput();
            }
        }
        
        $data['periods'] = $this->examPeriodModel->where('status !=', 'Concluído')->findAll();
        $data['classes'] = $this->classModel->where('is_active', 1)->findAll();
        $data['examBoards'] = $this->examBoardModel->where('is_active', 1)->findAll();
        
        return view('admin/exams/schedules/form', $data);
    }
   /**
 * Edit exam schedule
 */
public function edit($id)
{
    $data['schedule'] = $this->examScheduleModel
        ->select('
            tbl_exam_schedules.*,
            tbl_disciplines.discipline_name,
            tbl_disciplines.discipline_code
        ')
        ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id', 'left')
        ->find($id);
    
    if (!$data['schedule']) {
        return redirect()->to('/admin/exams/schedules')
            ->with('error', 'Agendamento não encontrado.');
    }
    
    $data['title'] = 'Editar Exame: ' . $data['schedule']->discipline_name;
    
    if ($this->request->getMethod() === 'post') {
        $rules = [
            'exam_period_id' => 'required|numeric',
            'class_id' => 'required|numeric',
            'discipline_id' => 'required|numeric',
            'exam_board_id' => 'required|numeric',
            'exam_date' => 'required|valid_date',
            'status' => 'required|in_list[Agendado,Realizado,Cancelado,Adiado]'
        ];
        
        // Check for conflicts (excluding current)
        $classId = $this->request->getPost('class_id');
        $examDate = $this->request->getPost('exam_date');
        $examTime = $this->request->getPost('exam_time');
        
        if ($this->examScheduleModel->checkConflicts($classId, $examDate, $examTime, $id)) {
            return redirect()->back()
                ->with('error', 'Já existe um exame agendado para esta turma nesta data/horário.')
                ->withInput();
        }
        
        if ($this->validate($rules)) {
            $updateData = [
                'exam_period_id' => $this->request->getPost('exam_period_id'),
                'class_id' => $classId,
                'discipline_id' => $this->request->getPost('discipline_id'),
                'exam_board_id' => $this->request->getPost('exam_board_id'),
                'exam_date' => $examDate,
                'exam_time' => $examTime,
                'exam_room' => $this->request->getPost('exam_room'),
                'duration_minutes' => $this->request->getPost('duration_minutes'),
                'observations' => $this->request->getPost('observations'),
                'status' => $this->request->getPost('status')
            ];
            
            if ($this->examScheduleModel->update($id, $updateData)) {
                return redirect()->to('/admin/exams/schedules')
                    ->with('success', 'Agendamento atualizado com sucesso!');
            } else {
                return redirect()->back()
                    ->with('error', 'Erro ao atualizar agendamento.')
                    ->withInput();
            }
        } else {
            return redirect()->back()
                ->with('errors', $this->validator->getErrors())
                ->withInput();
        }
    }
    
    $data['periods'] = $this->examPeriodModel->findAll();
    $data['classes'] = $this->classModel->where('is_active', 1)->findAll();
    $data['examBoards'] = $this->examBoardModel->where('is_active', 1)->findAll();
    
    return view('admin/exams/schedules/form', $data);
}
    
    /**
     * Manage exam attendance
     */
    public function attendance($id)
    {
        $data['schedule'] = $this->examScheduleModel
            ->select('
                tbl_exam_schedules.*,
                tbl_classes.class_name,
                tbl_disciplines.discipline_name,
                tbl_exam_periods.period_name
            ')
            ->join('tbl_classes', 'tbl_classes.id = tbl_exam_schedules.class_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id')
            ->join('tbl_exam_periods', 'tbl_exam_periods.id = tbl_exam_schedules.exam_period_id')
            ->find($id);
        
        if (!$data['schedule']) {
            return redirect()->to('/admin/exam-schedules')
                ->with('error', 'Agendamento não encontrado.');
        }
        
        $data['title'] = 'Registar Presenças - ' . $data['schedule']->discipline_name;
        
        if ($this->request->getMethod() === 'post') {
            $attendance = $this->request->getPost('attendance');
            
            // Delete existing attendance
            $this->examAttendanceModel->where('exam_schedule_id', $id)->delete();
            
            // Insert new attendance
            $attendanceData = [];
            foreach ($attendance as $enrollmentId => $value) {
                $attended = isset($value['attended']) ? 1 : 0;
                $attendanceData[] = [
                    'exam_schedule_id' => $id,
                    'enrollment_id' => $enrollmentId,
                    'attended' => $attended,
                    'check_in_time' => $attended ? date('Y-m-d H:i:s') : null,
                    'check_in_method' => 'Manual',
                    'observations' => $value['observations'] ?? null,
                    'recorded_by' => session()->get('user_id')
                ];
            }
            
            if ($this->examAttendanceModel->insertBatch($attendanceData)) {
                // Update exam status if all attendance recorded
                $totalStudents = count($attendanceData);
                $presentCount = count(array_filter($attendanceData, function($a) { return $a['attended']; }));
                
                if ($presentCount > 0) {
                    $this->examScheduleModel->update($id, ['status' => 'Realizado']);
                }
                
                return redirect()->to('/admin/exam-schedules/view/' . $id)
                    ->with('success', 'Presenças registadas com sucesso!');
            }
        }
        
        // Get students for this class
        $data['students'] = $this->enrollmentModel
            ->select('
                tbl_enrollments.id as enrollment_id,
                tbl_students.student_number,
                tbl_users.first_name,
                tbl_users.last_name
            ')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->where('tbl_enrollments.class_id', $data['schedule']->class_id)
            ->where('tbl_enrollments.status', 'Ativo')
            ->findAll();
        
        // Get existing attendance if any
        $data['existingAttendance'] = $this->examAttendanceModel
            ->where('exam_schedule_id', $id)
            ->findAll();
        
        return view('admin/exams/schedules/attendance', $data);
    }
    
    /**
     * Record exam results
     */
    public function results($id)
    {
        $data['schedule'] = $this->examScheduleModel
            ->select('
                tbl_exam_schedules.*,
                tbl_classes.class_name,
                tbl_disciplines.discipline_name,
                tbl_exam_boards.board_name,
                tbl_exam_boards.board_type,
                tbl_exam_boards.weight,
                tbl_exam_periods.period_name
            ')
            ->join('tbl_classes', 'tbl_classes.id = tbl_exam_schedules.class_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id')
            ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exam_schedules.exam_board_id')
            ->join('tbl_exam_periods', 'tbl_exam_periods.id = tbl_exam_schedules.exam_period_id')
            ->find($id);
        
        if (!$data['schedule']) {
            return redirect()->to('/admin/exam-schedules')
                ->with('error', 'Agendamento não encontrado.');
        }
        
        $data['title'] = 'Registar Notas - ' . $data['schedule']->discipline_name;
        
        if ($this->request->getMethod() === 'post') {
            $scores = $this->request->getPost('scores');
            
            // Delete existing results
            $this->examResultModel->where('exam_schedule_id', $id)->delete();
            
            // Insert new results
            $resultsData = [];
            foreach ($scores as $enrollmentId => $score) {
                if ($score !== '') {
                    $resultsData[] = [
                        'exam_schedule_id' => $id,
                        'exam_id' => null, // We'll handle this migration later
                        'enrollment_id' => $enrollmentId,
                        'score' => $score,
                        'is_absent' => $score === 'A' ? 1 : 0,
                        'recorded_by' => session()->get('user_id')
                    ];
                }
            }
            
            if (!empty($resultsData)) {
                $this->examResultModel->insertBatch($resultsData);
                
                // Recalculate averages for affected students
                $disciplineAvgModel = new \App\Models\DisciplineAverageModel();
                $semesterId = $this->examPeriodModel->find($data['schedule']->exam_period_id)->semester_id;
                
                foreach ($resultsData as $result) {
                    $disciplineAvgModel->calculate(
                        $result['enrollment_id'],
                        $data['schedule']->discipline_id,
                        $semesterId,
                        session()->get('user_id')
                    );
                }
                
                return redirect()->to('/admin/exam-schedules/view/' . $id)
                    ->with('success', 'Notas registadas com sucesso!');
            }
        }
        
        // Get students with attendance
        $data['students'] = $this->examAttendanceModel
            ->select('
                tbl_exam_attendance.*,
                tbl_enrollments.student_id,
                tbl_students.student_number,
                tbl_users.first_name,
                tbl_users.last_name
            ')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_exam_attendance.enrollment_id')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->where('tbl_exam_attendance.exam_schedule_id', $id)
            ->where('tbl_exam_attendance.attended', 1)
            ->findAll();
        
        // Get existing results
        $existingResults = $this->examResultModel
            ->where('exam_schedule_id', $id)
            ->findAll();
        
        $data['existingScores'] = [];
        foreach ($existingResults as $result) {
            $data['existingScores'][$result->enrollment_id] = $result->score;
        }
        
        return view('admin/exams/schedules/results', $data);
    }
    
    /**
     * View exam schedule details
     */
    public function view($id)
    {
        $data['schedule'] = $this->examScheduleModel
            ->select('
                tbl_exam_schedules.*,
                tbl_classes.class_name,
                tbl_classes.class_code,
                tbl_disciplines.discipline_name,
                tbl_disciplines.discipline_code,
                tbl_exam_boards.board_name,
                tbl_exam_boards.board_type,
                tbl_exam_boards.weight,
                tbl_exam_periods.period_name,
                tbl_exam_periods.period_type,
                tbl_academic_years.year_name,
                tbl_semesters.semester_name
            ')
            ->join('tbl_classes', 'tbl_classes.id = tbl_exam_schedules.class_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id')
            ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exam_schedules.exam_board_id')
            ->join('tbl_exam_periods', 'tbl_exam_periods.id = tbl_exam_schedules.exam_period_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_exam_periods.academic_year_id')
            ->join('tbl_semesters', 'tbl_semesters.id = tbl_exam_periods.semester_id')
            ->find($id);
        
        if (!$data['schedule']) {
            return redirect()->to('/admin/exam-schedules')
                ->with('error', 'Agendamento não encontrado.');
        }
        
        $data['title'] = 'Detalhes do Exame';
        
        // Get attendance
        $data['attendance'] = $this->examAttendanceModel->getByExam($id);
        $data['attendanceStats'] = $this->examAttendanceModel->getStats($id);
        
        // Get results
        $data['results'] = $this->examResultModel
            ->select('
                tbl_exam_results.*,
                tbl_enrollments.student_id,
                tbl_students.student_number,
                tbl_users.first_name,
                tbl_users.last_name
            ')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_exam_results.enrollment_id')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->where('tbl_exam_results.exam_schedule_id', $id)
            ->findAll();
        
        return view('admin/exams/schedules/view', $data);
    }
    
    /**
     * Delete exam schedule
     */
    public function delete($id)
    {
        // Check if there are results or attendance
        $hasResults = $this->examResultModel->where('exam_schedule_id', $id)->countAllResults() > 0;
        $hasAttendance = $this->examAttendanceModel->where('exam_schedule_id', $id)->countAllResults() > 0;
        
        if ($hasResults || $hasAttendance) {
            return redirect()->back()
                ->with('error', 'Não é possível remover um exame que já possui presenças ou notas registadas.');
        }
        
        if ($this->examScheduleModel->delete($id)) {
            return redirect()->to('/admin/exam-schedules')
                ->with('success', 'Exame removido com sucesso!');
        } else {
            return redirect()->to('/admin/exam-schedules')
                ->with('error', 'Erro ao remover exame.');
        }
    }
    
    /**
     * Get disciplines by class (AJAX)
     */
    public function getDisciplinesByClass($classId)
    {
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
            ->findAll();
        
        return $this->response->setJSON($disciplines);
    }

    /**
 * Save new exam schedule
 */
public function save()
{
    $rules = [
        'exam_period_id' => 'required|numeric',
        'class_id' => 'required|numeric',
        'discipline_id' => 'required|numeric',
        'exam_board_id' => 'required|numeric',
        'exam_date' => 'required|valid_date',
        'exam_time' => 'permit_empty'
    ];
    
    // Check for conflicts
    $classId = $this->request->getPost('class_id');
    $examDate = $this->request->getPost('exam_date');
    $examTime = $this->request->getPost('exam_time');
    
    if ($this->examScheduleModel->checkConflicts($classId, $examDate, $examTime)) {
        return redirect()->back()
            ->with('error', 'Já existe um exame agendado para esta turma nesta data/horário.')
            ->withInput();
    }
    
    if ($this->validate($rules)) {
        $data = [
            'exam_period_id' => $this->request->getPost('exam_period_id'),
            'class_id' => $classId,
            'discipline_id' => $this->request->getPost('discipline_id'),
            'exam_board_id' => $this->request->getPost('exam_board_id'),
            'exam_date' => $examDate,
            'exam_time' => $examTime,
            'exam_room' => $this->request->getPost('exam_room'),
            'duration_minutes' => $this->request->getPost('duration_minutes') ?: 120,
            'observations' => $this->request->getPost('observations'),
            'status' => 'Agendado'
        ];
        
        if ($this->examScheduleModel->insert($data)) {
            return redirect()->to('/admin/exams/schedules')
                ->with('success', 'Exame agendado com sucesso!');
        } else {
            return redirect()->back()
                ->with('error', 'Erro ao agendar exame.')
                ->withInput();
        }
    } else {
        return redirect()->back()
            ->with('errors', $this->validator->getErrors())
            ->withInput();
    }
}

/**
 * Update exam schedule
 */
public function update($id)
{
    $schedule = $this->examScheduleModel->find($id);
    
    if (!$schedule) {
        return redirect()->to('/admin/exams/schedules')
            ->with('error', 'Agendamento não encontrado.');
    }
    
    $rules = [
        'exam_period_id' => 'required|numeric',
        'class_id' => 'required|numeric',
        'discipline_id' => 'required|numeric',
        'exam_board_id' => 'required|numeric',
        'exam_date' => 'required|valid_date',
        'status' => 'required|in_list[Agendado,Realizado,Cancelado,Adiado]'
    ];
    
    // Check for conflicts (excluding current)
    $classId = $this->request->getPost('class_id');
    $examDate = $this->request->getPost('exam_date');
    $examTime = $this->request->getPost('exam_time');
    
    if ($this->examScheduleModel->checkConflicts($classId, $examDate, $examTime, $id)) {
        return redirect()->back()
            ->with('error', 'Já existe um exame agendado para esta turma nesta data/horário.')
            ->withInput();
    }
    
    if ($this->validate($rules)) {
        $updateData = [
            'exam_period_id' => $this->request->getPost('exam_period_id'),
            'class_id' => $classId,
            'discipline_id' => $this->request->getPost('discipline_id'),
            'exam_board_id' => $this->request->getPost('exam_board_id'),
            'exam_date' => $examDate,
            'exam_time' => $examTime,
            'exam_room' => $this->request->getPost('exam_room'),
            'duration_minutes' => $this->request->getPost('duration_minutes'),
            'observations' => $this->request->getPost('observations'),
            'status' => $this->request->getPost('status')
        ];
        
        if ($this->examScheduleModel->update($id, $updateData)) {
            return redirect()->to('/admin/exams/schedules')
                ->with('success', 'Agendamento atualizado com sucesso!');
        } else {
            return redirect()->back()
                ->with('error', 'Erro ao atualizar agendamento.')
                ->withInput();
        }
    } else {
        return redirect()->back()
            ->with('errors', $this->validator->getErrors())
            ->withInput();
    }
}
}