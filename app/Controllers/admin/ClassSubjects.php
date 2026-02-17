<?php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\ClassDisciplineModel;
use App\Models\ClassModel;
use App\Models\DisciplineModel;
use App\Models\UserModel;
use App\Models\SemesterModel;
use App\Models\AcademicYearModel;

class ClassSubjects extends BaseController
{
    protected $classDisciplineModel;
    protected $classModel;
    protected $disciplineModel;
    protected $userModel;
    protected $semesterModel;
    protected $academicYearModel;
    
    public function __construct()
    {
        $this->classDisciplineModel = new ClassDisciplineModel();
        $this->classModel = new ClassModel();
        $this->disciplineModel = new DisciplineModel();
        $this->userModel = new UserModel();
        $this->semesterModel = new SemesterModel();
        $this->academicYearModel = new AcademicYearModel();
    }
    
    /**
     * List class subjects assignments
     */
    public function index()
    {
        $data['title'] = 'Disciplinas por Turma';
        
        $academicYearId = $this->request->getGet('academic_year');
        $classId = $this->request->getGet('class');
        
        $builder = $this->classDisciplineModel
            ->select('
                tbl_class_disciplines.*,
                tbl_classes.class_name,
                tbl_classes.class_code,
                tbl_classes.class_shift,
                tbl_disciplines.discipline_name,
                tbl_disciplines.discipline_code,
                tbl_users.first_name as teacher_first_name,
                tbl_users.last_name as teacher_last_name,
                tbl_semesters.semester_name
            ')
            ->join('tbl_classes', 'tbl_classes.id = tbl_class_disciplines.class_id')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
            ->join('tbl_users', 'tbl_users.id = tbl_class_disciplines.teacher_id', 'left')
            ->join('tbl_semesters', 'tbl_semesters.id = tbl_class_disciplines.semester_id', 'left');
        
        if ($academicYearId) {
            $builder->where('tbl_classes.academic_year_id', $academicYearId);
        }
        
        if ($classId) {
            $builder->where('tbl_class_disciplines.class_id', $classId);
        }
        
        $data['assignments'] = $builder->orderBy('tbl_classes.class_name', 'ASC')
            ->orderBy('tbl_disciplines.discipline_name', 'ASC')
            ->findAll();
        
        // Filters
        $data['academicYears'] = $this->academicYearModel->where('is_active', 1)->findAll();
        $data['classes'] = $this->classModel
            ->select('tbl_classes.*, tbl_academic_years.year_name')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_classes.academic_year_id')
            ->where('tbl_classes.is_active', 1)
            ->orderBy('tbl_academic_years.start_date', 'DESC')
            ->orderBy('tbl_classes.class_name', 'ASC')
            ->findAll();
        
        $data['selectedYear'] = $academicYearId;
        $data['selectedClass'] = $classId;
        
        return view('admin/classes/class-subjects/index', $data);
    }
    
    /**
     * Assignment form
     */
    public function assign()
    {
        $data['title'] = 'Atribuir Disciplina à Turma';
        
        $assignmentId = $this->request->getGet('edit');
        $classId = $this->request->getGet('class');
        
        if ($assignmentId) {
            $data['assignment'] = $this->classDisciplineModel
                ->select('tbl_class_disciplines.*, tbl_classes.class_name')
                ->join('tbl_classes', 'tbl_classes.id = tbl_class_disciplines.class_id')
                ->where('tbl_class_disciplines.id', $assignmentId)
                ->first();
        } else {
            $data['assignment'] = null;
        }
        
        // Get active classes
        $data['classes'] = $this->classModel
            ->select('tbl_classes.*, tbl_academic_years.year_name')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_classes.academic_year_id')
            ->where('tbl_classes.is_active', 1)
            ->orderBy('tbl_academic_years.start_date', 'DESC')
            ->orderBy('tbl_classes.class_name', 'ASC')
            ->findAll();
        
        // Get all active disciplines
        $data['disciplines'] = $this->disciplineModel
            ->where('is_active', 1)
            ->orderBy('discipline_name', 'ASC')
            ->findAll();
        
        // Get teachers
        $data['teachers'] = $this->userModel
            ->where('user_type', 'teacher')
            ->where('is_active', 1)
            ->orderBy('first_name', 'ASC')
            ->findAll();
        
        // Get semesters
        $currentYear = $this->academicYearModel->getCurrent();
        if ($currentYear) {
            $data['semesters'] = $this->semesterModel
                ->where('academic_year_id', $currentYear->id)
                ->where('is_active', 1)
                ->orderBy('start_date', 'ASC')
                ->findAll();
        } else {
            $data['semesters'] = [];
        }
        
        $data['selectedClass'] = $classId;
        
        return view('admin/classes/class-subjects/assign', $data);
    }
    
    /**
     * Save assignment
     */
    public function assignSave()
    {
        $rules = [
            'class_id' => 'required|numeric',
            'discipline_id' => 'required|numeric'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        $classId = $this->request->getPost('class_id');
        $disciplineId = $this->request->getPost('discipline_id');
        $semesterId = $this->request->getPost('semester_id');
        
        // Check if already assigned
        $existing = $this->classDisciplineModel
            ->where('class_id', $classId)
            ->where('discipline_id', $disciplineId)
            ->where('semester_id', $semesterId ?: null)
            ->first();
        
        if ($existing && !$this->request->getPost('id')) {
            return redirect()->back()->withInput()
                ->with('error', 'Esta disciplina já está atribuída a esta turma' . 
                       ($semesterId ? ' para o período selecionado' : ''));
        }
        
        $data = [
            'class_id' => $classId,
            'discipline_id' => $disciplineId,
            'teacher_id' => $this->request->getPost('teacher_id') ?: null,
            'workload_hours' => $this->request->getPost('workload_hours') ?: null,
            'semester_id' => $semesterId ?: null,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];
        
        $id = $this->request->getPost('id');
        
        if ($id) {
            $this->classDisciplineModel->update($id, $data);
            $message = 'Atribuição atualizada com sucesso';
        } else {
            $this->classDisciplineModel->insert($data);
            $message = 'Disciplina atribuída à turma com sucesso';
        }
        
        return redirect()->to('/admin/classes/class-subjects?class=' . $classId)
            ->with('success', $message);
    }
    
    /**
     * Get assignments by class (AJAX)
     */
    public function getByClass($classId)
    {
        $assignments = $this->classDisciplineModel
            ->select('
                tbl_class_disciplines.*,
                tbl_disciplines.discipline_name,
                tbl_disciplines.discipline_code,
                tbl_users.first_name as teacher_first_name,
                tbl_users.last_name as teacher_last_name
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
            ->join('tbl_users', 'tbl_users.id = tbl_class_disciplines.teacher_id', 'left')
            ->where('tbl_class_disciplines.class_id', $classId)
            ->where('tbl_class_disciplines.is_active', 1)
            ->orderBy('tbl_disciplines.discipline_name', 'ASC')
            ->findAll();
        
        return $this->response->setJSON($assignments);
    }
    
    /**
     * Delete assignment
     */
    public function delete($id)
    {
        $assignment = $this->classDisciplineModel->find($id);
        
        if (!$assignment) {
            return redirect()->back()->with('error', 'Atribuição não encontrada');
        }
        
        $this->classDisciplineModel->delete($id);
        
        return redirect()->to('/admin/classes/class-subjects?class=' . $assignment->class_id)
            ->with('success', 'Disciplina removida da turma com sucesso');
    }
}