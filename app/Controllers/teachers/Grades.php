<?php

namespace App\Controllers\teachers;

use App\Controllers\BaseController;
use App\Models\ContinuousAssessmentModel;
use App\Models\ClassDisciplineModel;
use App\Models\EnrollmentModel;
use App\Models\DisciplineModel;
use App\Models\SemesterModel;

class Grades extends BaseController
{
    protected $continuousModel;
    protected $classDisciplineModel;
    protected $enrollmentModel;
    protected $disciplineModel;
    protected $semesterModel;
    
    public function __construct()
    {
        $this->continuousModel = new ContinuousAssessmentModel();
        $this->classDisciplineModel = new ClassDisciplineModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->disciplineModel = new DisciplineModel();
        $this->semesterModel = new SemesterModel();
    }
    
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
            ->distinct()
            ->findAll();
        
        $classId = $this->request->getGet('class');
        $disciplineId = $this->request->getGet('discipline');
        
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
            
            // Get existing assessments for each student
            foreach ($data['students'] as $student) {
                $assessments = $this->continuousModel
                    ->where('enrollment_id', $student->enrollment_id)
                    ->where('discipline_id', $disciplineId)
                    ->where('semester_id', $currentSemester->id ?? null)
                    ->findAll();
                
                $student->assessments = [];
                foreach ($assessments as $ass) {
                    $student->assessments[$ass->assessment_type] = $ass;
                }
            }
        }
        
        $data['selectedClass'] = $classId;
        $data['selectedDiscipline'] = $disciplineId;
        
        return view('teachers/grades/index', $data);
    }
    
    /**
     * Get disciplines for a class (AJAX) - específico para notas
     * 
     * @param int $classId ID da turma
     * @return JSON
     */
public function getDisciplines($classId = null)
{

    
    // Ignorar verificação AJAX por enquanto (para testes)
    // if (!$this->request->isAJAX()) {
    //     return $this->response->setJSON([]);
    // }
    
    if (!$classId) {
        return $this->response->setJSON(['error' => 'classId não fornecido']);
    }
    
    // Tentar obter o user_id de diferentes formas
    $teacherId = currentUserId(); // Usando o helper
    
    // Fallback: tentar da sessão diretamente
    if (!$teacherId) {
        $teacherId = $this->session->get('user_id');
    }
    
    // Se ainda não encontrou, retornar erro
    if (!$teacherId) {
        return $this->response->setJSON([
            'error' => 'Usuário não autenticado',
            'session_data' => $_SESSION ?? []
        ]);
    }
    
    // Buscar disciplinas
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
     * Save continuous assessments
     */
    public function save()
    {
        $classId = $this->request->getPost('class_id');
        $disciplineId = $this->request->getPost('discipline_id');
        $semesterId = $this->request->getPost('semester_id');
        
        if (!$classId || !$disciplineId || !$semesterId) {
            return redirect()->back()->with('error', 'Dados incompletos');
        }
        
        $ac1 = $this->request->getPost('ac1') ?? [];
        $ac2 = $this->request->getPost('ac2') ?? [];
        $ac3 = $this->request->getPost('ac3') ?? [];
        
        $db = db_connect();
        $db->transStart();
        
        foreach ($ac1 as $enrollmentId => $score) {
            if (!empty($score)) {
                $this->saveAssessment($enrollmentId, $disciplineId, $semesterId, 'AC1', $score);
            }
        }
        
        foreach ($ac2 as $enrollmentId => $score) {
            if (!empty($score)) {
                $this->saveAssessment($enrollmentId, $disciplineId, $semesterId, 'AC2', $score);
            }
        }
        
        foreach ($ac3 as $enrollmentId => $score) {
            if (!empty($score)) {
                $this->saveAssessment($enrollmentId, $disciplineId, $semesterId, 'AC3', $score);
            }
        }
        
        $db->transComplete();
        
        if ($db->transStatus()) {
            return redirect()->to('/teachers/grades?class=' . $classId . '&discipline=' . $disciplineId)
                ->with('success', 'Avaliações salvas com sucesso');
        } else {
            return redirect()->back()->with('error', 'Erro ao salvar avaliações');
        }
    }
    
    /**
     * Helper to save individual assessment
     */
    private function saveAssessment($enrollmentId, $disciplineId, $semesterId, $type, $score)
    {
        $existing = $this->continuousModel
            ->where('enrollment_id', $enrollmentId)
            ->where('discipline_id', $disciplineId)
            ->where('semester_id', $semesterId)
            ->where('assessment_type', $type)
            ->first();
        
        $data = [
            'enrollment_id' => $enrollmentId,
            'discipline_id' => $disciplineId,
            'semester_id' => $semesterId,
            'assessment_type' => $type,
            'score' => $score,
            'assessment_date' => date('Y-m-d'),
            'recorded_by' => $this->session->get('user_id')
        ];
        
        if ($existing) {
            return $this->continuousModel->update($existing->id, $data);
        } else {
            return $this->continuousModel->insert($data);
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
        
        // Get assessments for each student
        foreach ($data['students'] as $student) {
            $assessments = $this->continuousModel
                ->where('enrollment_id', $student->enrollment_id)
                ->where('discipline_id', $disciplineId)
                ->where('semester_id', $currentSemester->id ?? null)
                ->findAll();
            
            $scores = [];
            foreach ($assessments as $ass) {
                $scores[$ass->assessment_type] = $ass->score;
            }
            
            $student->ac1 = $scores['AC1'] ?? null;
            $student->ac2 = $scores['AC2'] ?? null;
            $student->ac3 = $scores['AC3'] ?? null;
            
            $validScores = array_filter([$student->ac1, $student->ac2, $student->ac3]);
            $student->average = !empty($validScores) ? round(array_sum($validScores) / count($validScores), 1) : 0;
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
            ->distinct()
            ->findAll();
        
        return view('teachers/grades/select_report', $data);
    }
}