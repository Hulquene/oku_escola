<?php
namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\GradeLevelModel;
use App\Models\ClassModel;
use App\Models\CourseModel;
use App\Models\GradeDisciplineModel;

class GradeLevels extends BaseController
{
    protected $gradeLevelModel;
    
    public function __construct()
    {
        $this->gradeLevelModel = new GradeLevelModel();
        helper(['auth', 'settings']);
    }
    
    /**
     * List grade levels
     */
    public function index()
    {
        // Verificar permissão
        if (!has_permission('classes.levels') && !is_admin()) {
            return redirect()->to('/admin/dashboard')
                ->with('error', 'Não tem permissão para aceder a esta página');
        }
        
        $data['title'] = 'Níveis de Ensino';
        $data['levels'] = $this->gradeLevelModel
            ->orderBy('sort_order', 'ASC')
            ->orderBy('grade_number', 'ASC')
            ->findAll();
        
        return view('admin/classes/levels/index', $data);
    }
    
    /**
     * Save grade level (AJAX)
     */
    public function save()
    {
        // Verificar permissão
        if (!has_permission('classes.levels') && !is_admin()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sem permissão para esta ação'
            ]);
        }

        $rules = [
            'level_name' => 'required|min_length[3]|max_length[100]',
            'level_code' => 'required|is_unique[tbl_grade_levels.level_code,id,{id}]',
            'education_level' => 'required|in_list[Iniciação,Primário,1º Ciclo,2º Ciclo,Ensino Médio]',
            'grade_number' => 'required|numeric|greater_than[0]|less_than[14]'
        ];

        if (!$this->validate($rules)) {
            return $this->response->setJSON([
                'success' => false,
                'errors' => $this->validator->getErrors()
            ]);
        }

        $data = [
            'level_name' => $this->request->getPost('level_name'),
            'level_code' => $this->request->getPost('level_code'),
            'education_level' => $this->request->getPost('education_level'),
            'grade_number' => $this->request->getPost('grade_number'),
            'sort_order' => (int)($this->request->getPost('sort_order') ?: 0),
            'is_course_start' => $this->request->getPost('is_course_start') ? 1 : 0,
            'is_course_end' => $this->request->getPost('is_course_end') ? 1 : 0,
            'course_type' => $this->request->getPost('course_type') ?: null,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];

        $id = $this->request->getPost('id');

        try {
            if ($id) {
                $this->gradeLevelModel->update($id, $data);
                $message = 'Nível de ensino atualizado com sucesso';
                log_message('info', "Nível ID {$id} atualizado por usuário ID: " . session()->get('user_id'));
            } else {
                $id = $this->gradeLevelModel->insert($data);
                $message = 'Nível de ensino criado com sucesso';
                log_message('info', "Novo nível criado ID {$id} por usuário ID: " . session()->get('user_id'));
            }

            return $this->response->setJSON([
                'success' => true,
                'message' => $message,
                'id' => $id
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Erro ao salvar nível: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao salvar: ' . $e->getMessage()
            ]);
        }
    }
    
    /**
     * Delete grade level
     */
    public function delete($id)
    {
        // Verificar permissão
        if (!has_permission('classes.levels') && !is_admin()) {
            return redirect()->back()->with('error', 'Não tem permissão para esta ação');
        }

        $level = $this->gradeLevelModel->find($id);
        
        if (!$level) {
            return redirect()->back()->with('error', 'Nível de ensino não encontrado');
        }
        
        // Check if level has classes
        $classModel = new ClassModel();
        $classes = $classModel->where('grade_level_id', $id)->countAllResults();
        
        if ($classes > 0) {
            return redirect()->back()->with('error', 'Não é possível eliminar um nível que possui turmas associadas');
        }
        
        // Check if level has disciplines
        $disciplineModel = new GradeDisciplineModel();
        $disciplines = $disciplineModel->where('grade_level_id', $id)->countAllResults();
        
        if ($disciplines > 0) {
            return redirect()->back()->with('error', 'Não é possível eliminar um nível que possui disciplinas associadas');
        }
        
        // Check if level is used in courses
        $courseModel = new CourseModel();
        $courses = $courseModel->where('start_grade_id', $id)
            ->orWhere('end_grade_id', $id)
            ->countAllResults();
        
        if ($courses > 0) {
            return redirect()->back()->with('error', 'Não é possível eliminar um nível que está associado a cursos');
        }
        
        try {
            $this->gradeLevelModel->delete($id);
            log_message('info', "Nível ID {$id} eliminado por usuário ID: " . session()->get('user_id'));
            
            return redirect()->to('/admin/classes/levels')
                ->with('success', 'Nível de ensino eliminado com sucesso');
                
        } catch (\Exception $e) {
            log_message('error', 'Erro ao eliminar nível: ' . $e->getMessage());
            return redirect()->back()
                ->with('error', 'Erro ao eliminar: ' . $e->getMessage());
        }
    }
    
    /**
     * Get grade levels by education level (AJAX)
     */
    public function getByEducationLevel($educationLevel)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON([]);
        }

        try {
            $levels = $this->gradeLevelModel
                ->where('education_level', $educationLevel)
                ->where('is_active', 1)
                ->orderBy('grade_number', 'ASC')
                ->findAll();
            
            return $this->response->setJSON([
                'success' => true,
                'data' => $levels
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Erro ao buscar níveis: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao buscar níveis'
            ]);
        }
    }
    
    /**
     * Toggle active status (AJAX)
     */
    public function toggleActive($id)
    {
        if (!$this->request->isAJAX()) {
            return $this->response->setJSON(['success' => false]);
        }

        if (!has_permission('classes.levels') && !is_admin()) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Sem permissão'
            ]);
        }

        $level = $this->gradeLevelModel->find($id);
        
        if (!$level) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Nível não encontrado'
            ]);
        }

        $newStatus = $level->is_active ? 0 : 1;
        
        try {
            $this->gradeLevelModel->update($id, ['is_active' => $newStatus]);
            
            return $this->response->setJSON([
                'success' => true,
                'is_active' => $newStatus,
                'message' => 'Status atualizado com sucesso'
            ]);
            
        } catch (\Exception $e) {
            log_message('error', 'Erro ao alterar status: ' . $e->getMessage());
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erro ao alterar status'
            ]);
        }
    }
}