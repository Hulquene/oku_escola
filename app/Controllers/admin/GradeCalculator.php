<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\DisciplineAverageModel;
use App\Models\SemesterResultModel;
use App\Models\EnrollmentModel;
use App\Models\ClassModel;
use App\Models\SemesterModel;
use App\Models\DisciplineModel;
use App\Models\ExamResultModel;
use App\Models\GradeWeightModel;
use App\Models\ExamScheduleModel;

class GradeCalculator extends BaseController
{
    protected $disciplineAverageModel;
    protected $semesterResultModel;
    protected $enrollmentModel;
    protected $classModel;
    protected $semesterModel;
    protected $disciplineModel;
    protected $examResultModel;
    protected $gradeWeightModel;
    protected $examScheduleModel;

    public function __construct()
    {
        $this->disciplineAverageModel = new DisciplineAverageModel();
        $this->semesterResultModel = new SemesterResultModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->classModel = new ClassModel();
        $this->semesterModel = new SemesterModel();
        $this->disciplineModel = new DisciplineModel();
        $this->examResultModel = new ExamResultModel();
        $this->gradeWeightModel = new GradeWeightModel();
        $this->examScheduleModel = new ExamScheduleModel();
    }

    /**
     * Calculate average for a specific discipline for a student
     */
    public function discipline($enrollmentId, $disciplineId, $semesterId)
    {
        // Check if enrollment exists
        $enrollment = $this->enrollmentModel->find($enrollmentId);
        if (!$enrollment) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Matrícula não encontrada'
            ]);
        }

        // Calculate the average
        $result = $this->disciplineAverageModel->calculate(
            $enrollmentId,
            $disciplineId,
            $semesterId,
            session()->get('user_id')
        );

        if ($result) {
            // Get the calculated average
            $average = $this->disciplineAverageModel
                ->select('
                    tbl_discipline_averages.*,
                    tbl_disciplines.discipline_name
                ')
                ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_discipline_averages.discipline_id')
                ->where('tbl_discipline_averages.id', $result)
                ->first();

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Média calculada com sucesso!',
                'data' => $average
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Erro ao calcular média. Verifique se existem notas registadas.'
        ]);
    }

    /**
     * Calculate all averages for a student in a semester
     */
    public function semester($enrollmentId, $semesterId)
    {
        // Get enrollment details
        $enrollment = $this->enrollmentModel
            ->select('
                tbl_enrollments.*,
                tbl_classes.grade_level_id,
                tbl_students.first_name,
                tbl_students.last_name
            ')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->find($enrollmentId);

        if (!$enrollment) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Matrícula não encontrada'
            ]);
        }

        // Get all disciplines for this class in this semester
        $classDisciplineModel = new \App\Models\ClassDisciplineModel();
        $disciplines = $classDisciplineModel
            ->where('class_id', $enrollment->class_id)
            ->where('semester_id', $semesterId)
            ->where('is_active', 1)
            ->findAll();

        $calculated = [];
        $errors = [];

        // Calculate average for each discipline
        foreach ($disciplines as $cd) {
            $result = $this->disciplineAverageModel->calculate(
                $enrollmentId,
                $cd->discipline_id,
                $semesterId,
                session()->get('user_id')
            );

            if ($result) {
                $discipline = $this->disciplineModel->find($cd->discipline_id);
                $calculated[] = $discipline->discipline_name;
            } else {
                $discipline = $this->disciplineModel->find($cd->discipline_id);
                $errors[] = $discipline->discipline_name;
            }
        }

        // Calculate semester result
        $semesterResult = $this->semesterResultModel->calculate(
            $enrollmentId,
            $semesterId,
            session()->get('user_id')
        );

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Cálculo concluído!',
            'data' => [
                'calculated' => $calculated,
                'errors' => $errors,
                'total_calculated' => count($calculated),
                'total_errors' => count($errors),
                'semester_result' => $semesterResult ? true : false
            ]
        ]);
    }

    /**
     * Calculate all averages for an entire class
     */
    public function class($classId, $semesterId)
    {
        // Get class details
        $class = $this->classModel->find($classId);
        if (!$class) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Turma não encontrada'
            ]);
        }

        // Get all active students in the class
        $enrollments = $this->enrollmentModel
            ->select('
                tbl_enrollments.id as enrollment_id,
                tbl_students.first_name,
                tbl_students.last_name,
                tbl_students.student_number
            ')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->where('tbl_enrollments.class_id', $classId)
            ->where('tbl_enrollments.status', 'Ativo')
            ->findAll();

        // Get all disciplines for this class
        $classDisciplineModel = new \App\Models\ClassDisciplineModel();
        $disciplines = $classDisciplineModel
            ->where('class_id', $classId)
            ->where('semester_id', $semesterId)
            ->where('is_active', 1)
            ->findAll();

        $results = [
            'total_students' => count($enrollments),
            'total_disciplines' => count($disciplines),
            'students_processed' => 0,
            'averages_calculated' => 0,
            'semester_results' => 0,
            'details' => []
        ];

        // Process each student
        foreach ($enrollments as $enrollment) {
            $studentResults = [
                'student' => $enrollment->first_name . ' ' . $enrollment->last_name,
                'student_number' => $enrollment->student_number,
                'disciplines_calculated' => 0,
                'disciplines_failed' => 0,
                'semester_result' => false
            ];

            // Calculate averages for each discipline
            foreach ($disciplines as $cd) {
                $result = $this->disciplineAverageModel->calculate(
                    $enrollment->enrollment_id,
                    $cd->discipline_id,
                    $semesterId,
                    session()->get('user_id')
                );

                if ($result) {
                    $studentResults['disciplines_calculated']++;
                    $results['averages_calculated']++;
                } else {
                    $studentResults['disciplines_failed']++;
                }
            }

            // Calculate semester result
            $semesterResult = $this->semesterResultModel->calculate(
                $enrollment->enrollment_id,
                $semesterId,
                session()->get('user_id')
            );

            if ($semesterResult) {
                $studentResults['semester_result'] = true;
                $results['semester_results']++;
            }

            $results['students_processed']++;
            $results['details'][] = $studentResults;
        }

        return $this->response->setJSON([
            'success' => true,
            'message' => 'Cálculo da turma concluído!',
            'data' => $results
        ]);
    }

    /**
     * Run custom calculation based on POST parameters
     */
    public function runCalculation()
    {
        if ($this->request->getMethod() !== 'post') {
            return redirect()->to('/admin/dashboard')
                ->with('error', 'Método não permitido.');
        }

        $calculationType = $this->request->getPost('calculation_type');
        $classId = $this->request->getPost('class_id');
        $semesterId = $this->request->getPost('semester_id');
        $enrollmentId = $this->request->getPost('enrollment_id');
        $disciplineId = $this->request->getPost('discipline_id');

        switch ($calculationType) {
            case 'discipline':
                if (!$enrollmentId || !$disciplineId || !$semesterId) {
                    return redirect()->back()
                        ->with('error', 'Parâmetros insuficientes para cálculo de disciplina.');
                }
                
                $result = $this->disciplineAverageModel->calculate(
                    $enrollmentId,
                    $disciplineId,
                    $semesterId,
                    session()->get('user_id')
                );

                if ($result) {
                    return redirect()->back()
                        ->with('success', 'Média da disciplina calculada com sucesso!');
                } else {
                    return redirect()->back()
                        ->with('error', 'Erro ao calcular média da disciplina. Verifique as notas.');
                }
                break;

            case 'student':
                if (!$enrollmentId || !$semesterId) {
                    return redirect()->back()
                        ->with('error', 'Parâmetros insuficientes para cálculo do aluno.');
                }

                // Redirect to semester calculation for this student
                return redirect()->to("/admin/exams/calculate/semester/{$enrollmentId}/{$semesterId}");
                break;

            case 'class':
                if (!$classId || !$semesterId) {
                    return redirect()->back()
                        ->with('error', 'Parâmetros insuficientes para cálculo da turma.');
                }

                // Redirect to class calculation
                return redirect()->to("/admin/exams/calculate/class/{$classId}/{$semesterId}");
                break;

            default:
                return redirect()->back()
                    ->with('error', 'Tipo de cálculo inválido.');
        }
    }

    /**
     * Show calculation dashboard
     */
    public function index()
    {
        $data['title'] = 'Calculadora de Médias';

        // Get current academic year
        $academicYearModel = new \App\Models\AcademicYearModel();
        $currentYear = $academicYearModel->getCurrent();
        
        // Get semesters for current year
        $semesters = $this->semesterModel
            ->where('academic_year_id', $currentYear->id ?? 0)
            ->where('is_active', 1)
            ->findAll();

        // Get classes for current year
        $classes = $this->classModel
            ->select('
                tbl_classes.*,
                tbl_grade_levels.level_name
            ')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
            ->where('tbl_classes.academic_year_id', $currentYear->id ?? 0)
            ->where('tbl_classes.is_active', 1)
            ->orderBy('tbl_classes.class_name', 'ASC')
            ->findAll();

        $data['semesters'] = $semesters;
        $data['classes'] = $classes;
        $data['currentYear'] = $currentYear;

        return view('admin/exams/grade-calculator/index', $data);
    }

    /**
     * Get students for a class (AJAX)
     */
    public function getStudentsByClass($classId)
    {
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

        return $this->response->setJSON($students);
    }

    /**
     * Get disciplines for a class (AJAX)
     */
    public function getDisciplinesByClass($classId, $semesterId)
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
            ->where('tbl_class_disciplines.semester_id', $semesterId)
            ->where('tbl_class_disciplines.is_active', 1)
            ->orderBy('tbl_disciplines.discipline_name', 'ASC')
            ->findAll();

        return $this->response->setJSON($disciplines);
    }

    /**
     * Get calculation status for a class
     */
    public function getCalculationStatus($classId, $semesterId)
    {
        // Get total students
        $totalStudents = $this->enrollmentModel
            ->where('class_id', $classId)
            ->where('status', 'Ativo')
            ->countAllResults();

        // Get students with calculated semester results
        $studentsWithResults = $this->semesterResultModel
            ->select('DISTINCT enrollment_id')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_semester_results.enrollment_id')
            ->where('tbl_enrollments.class_id', $classId)
            ->where('tbl_semester_results.semester_id', $semesterId)
            ->countAllResults();

        // Get total disciplines for this class
        $classDisciplineModel = new \App\Models\ClassDisciplineModel();
        $totalDisciplines = $classDisciplineModel
            ->where('class_id', $classId)
            ->where('semester_id', $semesterId)
            ->where('is_active', 1)
            ->countAllResults();

        // Get disciplines with averages calculated
        $disciplinesWithAverages = $this->disciplineAverageModel
            ->select('DISTINCT discipline_id')
            ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_discipline_averages.enrollment_id')
            ->where('tbl_enrollments.class_id', $classId)
            ->where('tbl_discipline_averages.semester_id', $semesterId)
            ->countAllResults();

        $progress = $totalStudents > 0 ? round(($studentsWithResults / $totalStudents) * 100) : 0;

        return $this->response->setJSON([
            'success' => true,
            'data' => [
                'total_students' => $totalStudents,
                'students_with_results' => $studentsWithResults,
                'total_disciplines' => $totalDisciplines,
                'disciplines_with_averages' => $disciplinesWithAverages,
                'progress' => $progress,
                'status' => $progress == 100 ? 'complete' : ($progress > 0 ? 'in_progress' : 'pending')
            ]
        ]);
    }

    /**
     * Validate all weights before calculation
     */
    public function validateWeights($classId, $semesterId)
    {
        // Get class details to find grade level
        $class = $this->classModel->find($classId);
        
        // Get weights for this grade level
        $weights = $this->gradeWeightModel->getByGradeLevel($class->grade_level_id);
        
        $totalWeight = 0;
        $missingWeights = [];

        foreach ($weights as $weight) {
            $totalWeight += $weight->weight_percentage;
        }

        // Check if total is 100%
        $isValid = abs($totalWeight - 100) < 0.01;

        if (!$isValid) {
            $missingWeights[] = "Total atual: {$totalWeight}%. Deve ser 100%";
        }

        // Check if all exam boards have weights
        $examBoardModel = new \App\Models\ExamBoardModel();
        $allBoards = $examBoardModel->where('is_active', 1)->findAll();
        
        $boardsWithWeights = array_column($weights, 'exam_board_id');
        
        foreach ($allBoards as $board) {
            if (!in_array($board->id, $boardsWithWeights)) {
                $missingWeights[] = "Tipo de avaliação '{$board->board_name}' sem peso configurado";
            }
        }

        return $this->response->setJSON([
            'success' => $isValid && empty($missingWeights),
            'message' => $isValid ? 'Pesos validados com sucesso!' : 'Problemas encontrados na configuração de pesos',
            'data' => [
                'total_weight' => $totalWeight,
                'is_valid' => $isValid,
                'missing_weights' => $missingWeights
            ]
        ]);
    }
}