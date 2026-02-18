<?php

namespace App\Controllers\teachers;

use App\Controllers\BaseController;
use App\Models\AttendanceModel;
use App\Models\ClassDisciplineModel;
use App\Models\EnrollmentModel;
use App\Models\DisciplineModel;

class Attendance extends BaseController
{
    protected $attendanceModel;
    protected $classDisciplineModel;
    protected $enrollmentModel;
    protected $disciplineModel;
    
    public function __construct()
    {
        $this->attendanceModel = new AttendanceModel();
        $this->classDisciplineModel = new ClassDisciplineModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->disciplineModel = new DisciplineModel();
    }
    
    /**
     * Attendance page
     */
  public function index()
{
    $data['title'] = 'Registro de Presenças';
    
    $teacherId = $this->session->get('user_id');
    
    // Get classes for filter
    $data['classes'] = $this->classDisciplineModel
        ->select('tbl_classes.id, tbl_classes.class_name, tbl_classes.class_code')
        ->join('tbl_classes', 'tbl_classes.id = tbl_class_disciplines.class_id')
        ->where('tbl_class_disciplines.teacher_id', $teacherId)
        ->distinct()
        ->findAll();
    
    $classId = $this->request->getGet('class_id');
    $disciplineId = $this->request->getGet('discipline_id');
    $date = $this->request->getGet('date') ?? date('Y-m-d');
    
    // PASSAR OS VALORES PARA A VIEW
    $data['selectedClass'] = $classId;
    $data['selectedDisciplineId'] = $disciplineId; // MUDAR NOME PARA SER MAIS CLARO
    $data['selectedDate'] = $date;
    
    // Buscar nome da disciplina se tiver ID
    $data['selectedDisciplineName'] = '';
    if ($disciplineId) {
        $discipline = $this->disciplineModel->find($disciplineId);
        $data['selectedDisciplineName'] = $discipline ? $discipline->discipline_name : '';
    }
    
    if ($classId && $disciplineId) { // PRECISA DOS DOIS PARA BUSCAR ALUNOS
        // Buscar alunos matriculados na turma E que têm esta disciplina
        // Importante: Os alunos podem estar na turma, mas precisamos verificar
        // se a disciplina é ministrada para eles através da tabela class_disciplines
        
        $data['students'] = $this->enrollmentModel
            ->select('
                tbl_enrollments.id as enrollment_id, 
                tbl_students.id, 
                tbl_users.first_name, 
                tbl_users.last_name, 
                tbl_students.student_number
            ')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->where('tbl_enrollments.class_id', $classId)
            ->where('tbl_enrollments.status', 'Ativo')
            ->orderBy('tbl_users.first_name', 'ASC')
            ->findAll();
        
        // Get existing attendances para esta disciplina específica
        $attendances = $this->attendanceModel
            ->where('class_id', $classId)
            ->where('discipline_id', $disciplineId) // FILTRAR POR DISCIPLINA
            ->where('attendance_date', $date)
            ->findAll();
        
        $data['attendances'] = [];
        foreach ($attendances as $att) {
            $data['attendances'][$att->enrollment_id] = $att;
        }
    } else {
        $data['students'] = [];
        $data['attendances'] = [];
    }
    
    return view('teachers/attendance/index', $data);
}

    /**
 * Save attendance
 */
public function save()
{
    $classId = $this->request->getPost('class_id');
    $disciplineId = $this->request->getPost('discipline_id') ?: null;
    $attendanceDate = $this->request->getPost('attendance_date');
    $attendances = $this->request->getPost('attendance') ?? [];
    
    // Validação inicial
    if (!$classId || !$attendanceDate || empty($attendances)) {
        $message = '❌ Não foi possível registrar as presenças. ';
        $message .= 'Certifique-se de que selecionou a turma, disciplina e data corretamente.';
        return redirect()->back()->withInput()->with('error', $message);
    }
    
    // Buscar alunos da turma para contagem
    $students = $this->enrollmentModel
        ->where('class_id', $classId)
        ->where('status', 'Ativo')
        ->findAll();
    
    $totalAlunos = count($students);
    $data = [];
    $studentsWithStatus = 0;
    
    foreach ($attendances as $enrollmentId => $att) {
        if (isset($att['status']) && !empty($att['status'])) {
            $studentsWithStatus++;
            
            $data[] = [
                'enrollment_id' => (int)$enrollmentId,
                'class_id' => (int)$classId,
                'discipline_id' => $disciplineId, // Já tratado acima
                'attendance_date' => $attendanceDate,
                'status' => $att['status'],
                'justification' => !empty($att['justification']) ? trim($att['justification']) : null,
                'marked_by' => (int)$this->session->get('user_id')
            ];
        }
    }
    
    // Verificar se pelo menos um status foi selecionado
    if (empty($data)) {
        $message = '⚠️ Nenhuma presença foi registrada. ';
        $message .= 'Por favor, selecione o status para pelo menos um aluno antes de salvar.';
        return redirect()->back()->withInput()->with('warning', $message);
    }
    
    // Verificar se houve alterações
    $existingAttendances = $this->attendanceModel
        ->where('class_id', $classId)
        ->where('attendance_date', $attendanceDate);
    
    if ($disciplineId) {
        $existingAttendances->where('discipline_id', $disciplineId);
    } else {
        $existingAttendances->where('discipline_id', null);
    }
    
    $existingAttendances = $existingAttendances->findAll();
    
    $hasChanges = $this->checkForChanges($data, $existingAttendances);
    
    if (!$hasChanges) {
        $message = 'ℹ️ Nenhuma alteração foi detectada nas presenças. ';
        $message .= 'Os dados já estavam atualizados.';
        
        // CORREÇÃO: Usar site_url() para construir a URL corretamente
        $redirectUrl = site_url('teachers/attendance') . '?class_id=' . $classId . 
                      '&discipline_id=' . ($disciplineId ?: '') . 
                      '&date=' . $attendanceDate;
        
        return redirect()->to($redirectUrl)->with('info', $message);
    }
    
    // Salvar presenças
    $result = $this->attendanceModel->markBulk($data);
    
    if ($result) {
        $percentual = $totalAlunos > 0 ? round(($studentsWithStatus / $totalAlunos) * 100) : 0;
        
        $message = '✅ Presenças registradas com sucesso! ';
        $message .= "Foram atualizados {$studentsWithStatus} de {$totalAlunos} alunos. ";
        $message .= "Taxa de preenchimento: {$percentual}%.";
        
        // CORREÇÃO: Usar site_url() para construir a URL corretamente
        $redirectUrl = site_url('teachers/attendance') . '?class_id=' . $classId . 
                      '&discipline_id=' . ($disciplineId ?: '') . 
                      '&date=' . $attendanceDate;
        
        return redirect()->to($redirectUrl)->with('success', $message);
    } else {
        $message = '❌ Ocorreu um erro ao registrar as presenças. ';
        $message .= 'Por favor, tente novamente.';
        return redirect()->back()->withInput()->with('error', $message);
    }
}

    /**
     * Check if there are changes between submitted and existing data
     */
    private function checkForChanges($newData, $existingData)
    {
        if (count($newData) !== count($existingData)) {
            return true;
        }
        
        $existingMap = [];
        foreach ($existingData as $existing) {
            $existingMap[$existing->enrollment_id] = $existing;
        }
        
        foreach ($newData as $new) {
            $enrollmentId = $new['enrollment_id'];
            
            if (!isset($existingMap[$enrollmentId])) {
                return true; // Novo registro
            }
            
            $existing = $existingMap[$enrollmentId];
            if ($existing->status !== $new['status'] || $existing->justification !== $new['justification']) {
                return true; // Status ou justificativa mudou
            }
        }
        
        return false; // Nenhuma mudança
    }
    
    /**
     * Attendance report
     */
    public function report()
    {
        $data['title'] = 'Relatório de Presenças';
        
        $teacherId = $this->session->get('user_id');
        
        $classId = $this->request->getGet('class');
        $disciplineId = $this->request->getGet('discipline');
        $startDate = $this->request->getGet('start_date') ?: date('Y-m-01');
        $endDate = $this->request->getGet('end_date') ?: date('Y-m-t');
        
        // Get classes for filter
        $data['classes'] = $this->classDisciplineModel
            ->select('tbl_classes.id, tbl_classes.class_name')
            ->join('tbl_classes', 'tbl_classes.id = tbl_class_disciplines.class_id')
            ->where('tbl_class_disciplines.teacher_id', $teacherId)
            ->distinct()
            ->findAll();
        
        // Get disciplines for filter
        $data['disciplines'] = $this->classDisciplineModel
            ->select('tbl_disciplines.id, tbl_disciplines.discipline_name')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
            ->where('tbl_class_disciplines.teacher_id', $teacherId)
            ->distinct()
            ->findAll();
        
        if ($classId) {
            // Get attendance summary by student
            $builder = $this->attendanceModel
                ->select('
                    tbl_users.first_name,
                    tbl_users.last_name,
                    tbl_students.student_number,
                    COUNT(*) as total,
                    SUM(CASE WHEN status = "Presente" THEN 1 ELSE 0 END) as present,
                    SUM(CASE WHEN status = "Ausente" THEN 1 ELSE 0 END) as absent,
                    SUM(CASE WHEN status = "Atrasado" THEN 1 ELSE 0 END) as late,
                    SUM(CASE WHEN status = "Falta Justificada" THEN 1 ELSE 0 END) as justified
                ')
                ->join('tbl_enrollments', 'tbl_enrollments.id = tbl_attendance.enrollment_id')
                ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
                ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
                ->where('tbl_attendance.class_id', $classId)
                ->where('tbl_attendance.attendance_date >=', $startDate)
                ->where('tbl_attendance.attendance_date <=', $endDate);
            
            if ($disciplineId) {
                $builder->where('tbl_attendance.discipline_id', $disciplineId);
            }
            
            $data['report'] = $builder->groupBy('tbl_attendance.enrollment_id')
                ->orderBy('tbl_users.first_name', 'ASC')
                ->findAll();
        }
        
        $data['selectedClass'] = $classId;
        $data['selectedDiscipline'] = $disciplineId;
        $data['startDate'] = $startDate;
        $data['endDate'] = $endDate;
        
        return view('teachers/attendance/report', $data);
    }
    
    /**
     * Get disciplines for a class (AJAX)
     */
    public function getDisciplines($classId)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([]);
        }
        
        $teacherId = $this->session->get('user_id');
        
        $disciplines = $this->classDisciplineModel
            ->select('tbl_disciplines.id, tbl_disciplines.discipline_name')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
            ->where('tbl_class_disciplines.class_id', $classId)
            ->where('tbl_class_disciplines.teacher_id', $teacherId)
            ->orderBy('tbl_disciplines.discipline_name', 'ASC')
            ->findAll();
        
        return $this->response->setJSON($disciplines);
    }
}