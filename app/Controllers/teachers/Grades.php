<?php

namespace App\Controllers\teachers;

use App\Controllers\BaseController;
use App\Models\ExamResultModel;           // NOVO (substitui ContinuousAssessment)
use App\Models\ExamBoardModel;             // NOVO
use App\Models\ClassDisciplineModel;
use App\Models\EnrollmentModel;
use App\Models\DisciplineModel;
use App\Models\SemesterModel;

class Grades extends BaseController
{
    protected $examResultModel;             // NOVO
    protected $examBoardModel;               // NOVO
    protected $classDisciplineModel;
    protected $enrollmentModel;
    protected $disciplineModel;
    protected $semesterModel;
    
    public function __construct()
    {
        $this->examResultModel = new ExamResultModel();       // NOVO
        $this->examBoardModel = new ExamBoardModel();         // NOVO
        $this->classDisciplineModel = new ClassDisciplineModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->disciplineModel = new DisciplineModel();
        $this->semesterModel = new SemesterModel();
    }
    
    /**
     * List grades page with filter (Avaliações Contínuas - MAC)
     */
    /**
     * List grades page with filter
     */
    public function index()
    {
        $data['title'] = 'Lançar Notas';
        
        $teacherId = $this->session->get('user_id');
        
        // Get classes for filter
        $data['classes'] = $this->classDisciplineModel
            ->select('tbl_classes.id, tbl_classes.class_name, tbl_classes.class_code')
            ->join('tbl_classes', 'tbl_classes.id = tbl_class_disciplines.class_id')
            ->where('tbl_class_disciplines.teacher_id', $teacherId)
            ->where('tbl_classes.is_active', 1)  // ✅ CORRIGIDO
            ->distinct()
            ->findAll();
        
        $classId = $this->request->getGet('class');
        $disciplineId = $this->request->getGet('discipline');
        
        $data['selectedClass'] = $classId;
        $data['selectedDiscipline'] = $disciplineId;
        
        if ($classId && $disciplineId) {
            // Get students
            $data['students'] = $this->enrollmentModel
                ->select('tbl_enrollments.id as enrollment_id, tbl_students.id, tbl_users.first_name, tbl_users.last_name, tbl_students.student_number')
                ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
                ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
                ->where('tbl_enrollments.class_id', $classId)
                ->where('tbl_enrollments.status', 'Ativo')
                ->orderBy('tbl_users.first_name', 'ASC')
                ->findAll();
            
            // Get current semester
            $currentSemester = $this->semesterModel->getCurrent();
            $data['currentSemester'] = $currentSemester;
            
            // Get AC board ID
            $acBoard = $this->examBoardModel->where('board_code', 'AC')->first();
            $acBoardId = $acBoard ? $acBoard->id : null;
            
            // Get existing AC assessments for each student
            foreach ($data['students'] as $student) {
                $assessments = $this->examResultModel
                    ->select('tbl_exam_results.*, tbl_exam_boards.board_code')
                    ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exam_results.exam_schedule_id')
                    ->where('tbl_exam_results.enrollment_id', $student->enrollment_id)
                    ->where('tbl_exam_boards.board_code', 'AC')
                    ->where('tbl_exam_results.assessment_type', 'AC')
                    ->where('tbl_exam_results.discipline_id', $disciplineId)
                    ->where('tbl_exam_results.semester_id', $currentSemester->id ?? null)
                    ->orderBy('tbl_exam_results.assessment_sequence', 'ASC')
                    ->findAll();
                
                $student->assessments = [];
                foreach ($assessments as $ass) {
                    $seq = $ass->assessment_sequence ?? 1;
                    $student->assessments[$seq] = $ass;
                }
            }
            
            // Número de avaliações contínuas (configurável)
            $data['acCount'] = 6; // Até 6 ACs por trimestre
        }
        
        return view('teachers/grades/index', $data);
    }
    
    /**
     * Get disciplines for a class (AJAX)
     */
    /**
 * Get disciplines for a class (AJAX)
 */
    public function getDisciplines($classId = null)
    {
        if (!$classId) {
            return $this->response->setJSON([]);
        }
        
        $teacherId = $this->session->get('user_id');
        
        $disciplines = $this->classDisciplineModel
            ->select('tbl_disciplines.id, tbl_disciplines.discipline_name, tbl_disciplines.discipline_code')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
            ->where('tbl_class_disciplines.class_id', $classId)
            ->where('tbl_class_disciplines.teacher_id', $teacherId)
            ->orderBy('tbl_disciplines.discipline_name', 'ASC')
            ->findAll();
        
        return $this->response->setJSON($disciplines);
    }
    
    /**
     * Save continuous assessments (ACs)
     */
    public function save()
    {
        $classId = $this->request->getPost('class_id');
        $disciplineId = $this->request->getPost('discipline_id');
        $semesterId = $this->request->getPost('semester_id');
        
        if (!$classId || !$disciplineId || !$semesterId) {
            return redirect()->back()->with('error', 'Dados incompletos');
        }
        
        // Get AC board ID
        $acBoard = $this->examBoardModel->where('board_code', 'AC')->first();
        if (!$acBoard) {
            return redirect()->back()->with('error', 'Tipo de avaliação "AC" não configurado');
        }
        
        // Receber notas por sequência (AC1, AC2, AC3...)
        $acScores = $this->request->getPost('ac') ?? [];
        
        $db = db_connect();
        $db->transStart();
        
        foreach ($acScores as $sequence => $enrollmentScores) {
            foreach ($enrollmentScores as $enrollmentId => $score) {
                if ($score !== '') {
                    $data = [
                        'enrollment_id' => $enrollmentId,
                        'exam_schedule_id' => $acBoard->id,
                        'assessment_type' => 'AC',
                        'assessment_sequence' => $sequence,
                        'discipline_id' => $disciplineId,
                        'semester_id' => $semesterId,
                        'score' => $score,
                        'recorded_by' => $this->session->get('user_id'),
                        'recorded_at' => date('Y-m-d H:i:s')
                    ];
                    
                    // Verificar se já existe
                    $existing = $this->examResultModel
                        ->where('enrollment_id', $enrollmentId)
                        ->where('assessment_type', 'AC')
                        ->where('assessment_sequence', $sequence)
                        ->where('discipline_id', $disciplineId)
                        ->where('semester_id', $semesterId)
                        ->first();
                    
                    if ($existing) {
                        $this->examResultModel->update($existing->id, $data);
                    } else {
                        $this->examResultModel->insert($data);
                    }
                }
            }
        }
        
        $db->transComplete();
        
        if ($db->transStatus()) {
            return redirect()->to('/teachers/grades?class=' . $classId . '&discipline=' . $disciplineId)
                ->with('success', 'Avaliações contínuas salvas com sucesso');
        } else {
            return redirect()->back()->with('error', 'Erro ao salvar avaliações');
        }
    }
    
   /**
 * Report for a specific class/discipline
 */
public function report($classId)
{
    $data['title'] = 'Relatório de Notas';
    
    $teacherId = $this->session->get('user_id');
    $disciplineId = $this->request->getGet('discipline');
    
    if (!$disciplineId) {
        return redirect()->to('/teachers/grades')->with('error', 'Selecione uma disciplina');
    }
    
    // Get class info
    $classModel = new \App\Models\ClassModel();
    $data['class'] = $classModel->find($classId);
    
    // Get discipline info
    $data['discipline'] = $this->disciplineModel->find($disciplineId);
    
    // Get students
    $data['students'] = $this->enrollmentModel
        ->select('tbl_enrollments.id as enrollment_id, tbl_students.id, tbl_users.first_name, tbl_users.last_name, tbl_students.student_number')
        ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
        ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
        ->where('tbl_enrollments.class_id', $classId)
        ->where('tbl_enrollments.status', 'Ativo')
        ->orderBy('tbl_users.first_name', 'ASC')
        ->findAll();
    
    // Get current semester
    $currentSemester = $this->semesterModel->getCurrent();
    $data['currentSemester'] = $currentSemester;
    
    // Get AC assessments for each student
    foreach ($data['students'] as $student) {
        $assessments = $this->examResultModel
            ->select('tbl_exam_results.*')
            ->where('enrollment_id', $student->enrollment_id)
            ->where('discipline_id', $disciplineId)
            ->where('semester_id', $currentSemester->id ?? null)
            ->where('assessment_type', 'AC')
            ->orderBy('assessment_sequence', 'ASC')
            ->findAll();
        
        $scores = [];
        $total = 0;
        $count = 0;
        
        foreach ($assessments as $ass) {
            $seq = $ass->assessment_sequence ?? $count + 1;
            $scores["ac{$seq}"] = $ass->score;
            $total += $ass->score;
            $count++;
        }
        
        $student->ac_scores = $scores;
        $student->ac_average = $count > 0 ? round($total / $count, 1) : 0;
        
        // Preencher até 6 ACs para exibição
        for ($i = 1; $i <= 6; $i++) {
            $key = "ac{$i}";
            if (!isset($scores[$key])) {
                $scores[$key] = '-';
            }
        }
        ksort($scores);
        $student->ac_scores = $scores;
    }
    
    return view('teachers/grades/report', $data);
}
/**
 * Select class for report
 */
public function selectReport()
{
    $data['title'] = 'Selecionar Relatório';
    
    $teacherId = $this->session->get('user_id');
    
    // Get teacher's classes
    $data['classes'] = $this->classDisciplineModel
        ->select('tbl_classes.id, tbl_classes.class_name, tbl_classes.class_code')
        ->join('tbl_classes', 'tbl_classes.id = tbl_class_disciplines.class_id')
        ->where('tbl_class_disciplines.teacher_id', $teacherId)
        ->where('tbl_classes.is_active', 1)  // ✅ CORRETO
        ->distinct()
        ->findAll();
    
    return view('teachers/grades/select_report', $data);
}
}