<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\ClassModel;
use App\Models\GradeLevelModel;
use App\Models\AcademicYearModel;
use App\Models\UserModel;
use App\Models\EnrollmentModel;

class Classes extends BaseController
{
    protected $classModel;
    protected $gradeLevelModel;
    protected $academicYearModel;
    protected $userModel;
    protected $enrollmentModel;
    
    public function __construct()
    {
        $this->classModel = new ClassModel();
        $this->gradeLevelModel = new GradeLevelModel();
        $this->academicYearModel = new AcademicYearModel();
        $this->userModel = new UserModel();
        $this->enrollmentModel = new EnrollmentModel();
    }
   /**
     * List classes
     */
    public function index()
    {
        $data['title'] = 'Turmas';
        
        $academicYearId = $this->request->getGet('academic_year');
        $gradeLevelId = $this->request->getGet('grade_level');
        $shift = $this->request->getGet('shift');
        $status = $this->request->getGet('status');
        
        $builder = $this->classModel
            ->select('tbl_classes.*, tbl_grade_levels.level_name, tbl_academic_years.year_name, tbl_users.first_name as teacher_first_name, tbl_users.last_name as teacher_last_name')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_classes.academic_year_id')
            ->join('tbl_users', 'tbl_users.id = tbl_classes.class_teacher_id', 'left');
        
        if ($academicYearId) {
            $builder->where('tbl_classes.academic_year_id', $academicYearId);
        }
        
        if ($gradeLevelId) {
            $builder->where('tbl_classes.grade_level_id', $gradeLevelId);
        }
        
        if ($shift) {
            $builder->where('tbl_classes.class_shift', $shift);
        }
        
        if ($status == 'active') {
            $builder->where('tbl_classes.is_active', 1);
        } elseif ($status == 'inactive') {
            $builder->where('tbl_classes.is_active', 0);
        }
        
        $data['classes'] = $builder->orderBy('tbl_academic_years.start_date', 'DESC')
            ->orderBy('tbl_classes.class_name', 'ASC')
            ->paginate(10);
        
        $data['pager'] = $this->classModel->pager;
        
        // Filters
        $data['academicYears'] = $this->academicYearModel->findAll();
        $data['gradeLevels'] = $this->gradeLevelModel->getActive();
        $data['selectedYear'] = $academicYearId;
        $data['selectedLevel'] = $gradeLevelId;
        $data['selectedShift'] = $shift;
        $data['selectedStatus'] = $status;
        
        // CAMINHO CORRIGIDO: apontando para a pasta classes/classes/
        return view('admin/classes/classes/index', $data);
    }

    /**
     * Class form
     */
    public function form($id = null)
    {
        $data['title'] = $id ? 'Editar Turma' : 'Nova Turma';
        $data['class'] = $id ? $this->classModel->find($id) : null;
        
        $data['gradeLevels'] = $this->gradeLevelModel->getActive();
        $data['academicYears'] = $this->academicYearModel->getActive();
        $data['teachers'] = $this->userModel->getByType('teacher');
        
        // CAMINHO CORRIGIDO
        return view('admin/classes/classes/form', $data);
    }

    /**
     * View class details
     */
    public function view($id)
    {
        $data['title'] = 'Detalhes da Turma';
        $data['class'] = $this->classModel->getWithTeacher($id);
        
        if (!$data['class']) {
            return redirect()->to('/admin/classes')->with('error', 'Turma não encontrada');
        }
        
        // Get students in this class
        $data['students'] = $this->enrollmentModel
            ->select('tbl_enrollments.*, tbl_students.student_number, tbl_users.first_name, tbl_users.last_name')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->where('tbl_enrollments.class_id', $id)
            ->where('tbl_enrollments.status', 'Ativo')
            ->orderBy('tbl_users.first_name', 'ASC')
            ->findAll();
        
        $data['availableSeats'] = $this->classModel->getAvailableSeats($id);
        
        // CAMINHO CORRIGIDO
        return view('admin/classes/classes/view', $data);
    }

    /**
     * List students in class
     */
    public function listStudents($id)
    {
        $class = $this->classModel->find($id);
        
        if (!$class) {
            return redirect()->to('/admin/classes')->with('error', 'Turma não encontrada');
        }
        
        $data['title'] = 'Alunos da Turma ' . $class->class_name;
        $data['class'] = $class;
        $data['students'] = $this->enrollmentModel
            ->select('tbl_enrollments.*, tbl_students.student_number, tbl_users.first_name, tbl_users.last_name, tbl_users.email, tbl_users.phone')
            ->join('tbl_students', 'tbl_students.id = tbl_enrollments.student_id')
            ->join('tbl_users', 'tbl_users.id = tbl_students.user_id')
            ->where('tbl_enrollments.class_id', $id)
            ->where('tbl_enrollments.status', 'Ativo')
            ->orderBy('tbl_users.first_name', 'ASC')
            ->findAll();
        
        // CAMINHO CORRIGIDO
        return view('admin/classes/classes/students', $data);
    }
}