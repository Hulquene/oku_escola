<?php

namespace App\Controllers\teachers;

use App\Controllers\BaseController;
use App\Models\ClassDisciplineModel;
use App\Models\EnrollmentModel;
use App\Models\AttendanceModel;

class Classes extends BaseController
{
    protected $classDisciplineModel;
    protected $enrollmentModel;
    protected $attendanceModel;
    
    public function __construct()
    {
        $this->classDisciplineModel = new ClassDisciplineModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->attendanceModel = new AttendanceModel();
    }
    
    /**
     * My classes list
     */
    public function index()
    {
        $data['title'] = 'Minhas Turmas';
        
        $teacherId = $this->session->get('user_id');
        
        $data['classes'] = $this->classDisciplineModel
            ->select('
                tbl_class_disciplines.*, 
                tbl_classes.id as class_id,
                tbl_classes.class_name, 
                tbl_classes.class_code,
                tbl_classes.class_shift, 
                tbl_classes.class_room,
                tbl_classes.capacity,
                tbl_disciplines.discipline_name,
                tbl_disciplines.discipline_code,
                tbl_disciplines.workload_hours
            ')
            ->join('tbl_classes', 'tbl_classes.id = tbl_class_disciplines.class_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
            ->where('tbl_class_disciplines.teacher_id', $teacherId)
            ->where('tbl_classes.is_active', 1) // VOLTAR PARA is_active
            ->orderBy('tbl_classes.class_name', 'ASC')
            ->findAll();
        
        // Calcular total de alunos e carga horária
        $enrollmentModel = new \App\Models\EnrollmentModel();
        $totalStudents = 0;
        $totalWorkload = 0;
        $processedClasses = [];
        
        foreach ($data['classes'] as $class) {
            if (!in_array($class->class_id, $processedClasses)) {
                $processedClasses[] = $class->class_id;
                $totalStudents += $enrollmentModel
                    ->where('class_id', $class->class_id)
                    ->where('status', 'Ativo')
                    ->countAllResults();
            }
            $totalWorkload += $class->workload_hours ?? 0;
        }
        
        $data['totalStudents'] = $totalStudents;
        $data['totalWorkload'] = $totalWorkload;
        
        return view('teachers/classes/index', $data);
    }
    
    /**
     * View class students
     */
    public function students($classId)
    {
        $data['class'] = $this->classDisciplineModel
            ->select('
                tbl_class_disciplines.*, 
                tbl_classes.id as class_id,
                tbl_classes.class_name, 
                tbl_classes.class_code,
                tbl_classes.class_shift,
                tbl_classes.class_room,
                tbl_disciplines.discipline_name,
                tbl_disciplines.id as discipline_id
            ')
            ->join('tbl_classes', 'tbl_classes.id = tbl_class_disciplines.class_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
            ->where('tbl_class_disciplines.class_id', $classId)
            ->where('tbl_classes.is_active', 1) // VOLTAR PARA is_active
            ->first();
        
        if (!$data['class']) {
            return redirect()->to('/teachers/classes')->with('error', 'Turma não encontrada');
        }
        
        $data['students'] = $this->enrollmentModel
            ->select('
                tbl_enrollments.id as enrollment_id,
                tbl_students.id,
                tbl_users.first_name,
                tbl_users.last_name,
                tbl_students.student_number,
                tbl_users.email,
                tbl_users.phone
            ')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->where('tbl_enrollments.class_id', $classId)
            ->where('tbl_enrollments.status', 'Ativo')
            ->orderBy('tbl_users.first_name', 'ASC')
            ->findAll();
        
        return view('teachers/classes/students', $data);
    }
}