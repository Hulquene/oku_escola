<?php
// app/Controllers/teachers/Profile.php

namespace App\Controllers\teachers;

use App\Controllers\BaseController;
use App\Models\UserModel;
use App\Models\TeacherModel;
use App\Models\ClassDisciplineModel;
use App\Models\EnrollmentModel;
use App\Models\ExamScheduleModel; // ALTERADO

class Profile extends BaseController
{
    protected $userModel;
    protected $teacherModel;
    protected $classDisciplineModel;
    protected $enrollmentModel;
    protected $examScheduleModel; // ALTERADO
    
    public function __construct()
    {
        helper(['auth', 'form']);
        
        $this->userModel = new UserModel();
        $this->teacherModel = new TeacherModel();
        $this->classDisciplineModel = new ClassDisciplineModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->examScheduleModel = new ExamScheduleModel(); // ALTERADO
    }
    
    /**
     * Teacher profile page
     */
    public function index()
    {
        $data['title'] = 'Meu Perfil';
        
        $userId = currentUserId();
        
        // Buscar dados do usuário
        $data['user'] = $this->userModel->find($userId);
        
        // Buscar dados do professor (se existir na tabela de professores)
        $data['teacher'] = $this->teacherModel->getByUserId($userId);
        
        // Estatísticas do professor
        $data['stats'] = $this->getTeacherStats($userId);
        
        return view('teachers/profile/index', $data);
    }
    
    /**
     * Update profile information
     */
    public function update()
    {
        $userId = currentUserId();
        
        // Regras de validação
        $rules = [
            'first_name' => 'required|min_length[2]|max_length[100]',
            'last_name' => 'required|min_length[2]|max_length[100]',
            'phone' => 'permit_empty|min_length[9]|max_length[20]',
            'address' => 'permit_empty|max_length[255]',
            'birth_date' => 'permit_empty|valid_date',
            'gender' => 'permit_empty|in_list[Masculino,Feminino]',
            'nationality' => 'permit_empty|max_length[100]',
            'identity_document' => 'permit_empty|max_length[50]',
            'identity_type' => 'permit_empty|in_list[BI,Passaporte,Cédula,Outro]',
            'nif' => 'permit_empty|max_length[20]',
            'city' => 'permit_empty|max_length[100]',
            'municipality' => 'permit_empty|max_length[100]',
            'province' => 'permit_empty|max_length[100]',
            'emergency_contact' => 'permit_empty|max_length[20]',
            'emergency_contact_name' => 'permit_empty|max_length[255]',
            'qualifications' => 'permit_empty|max_length[500]',
            'specialization' => 'permit_empty|max_length[255]',
            'admission_date' => 'permit_empty|valid_date',
            'bank_account' => 'permit_empty|max_length[50]',
            'bank_name' => 'permit_empty|max_length[100]'
        ];
        
        // Mensagens de erro personalizadas
        $messages = [
            'first_name' => [
                'required' => 'O primeiro nome é obrigatório',
                'min_length' => 'O primeiro nome deve ter pelo menos 2 caracteres',
                'max_length' => 'O primeiro nome não pode ter mais de 100 caracteres'
            ],
            'last_name' => [
                'required' => 'O último nome é obrigatório',
                'min_length' => 'O último nome deve ter pelo menos 2 caracteres',
                'max_length' => 'O último nome não pode ter mais de 100 caracteres'
            ]
        ];
        
        if (!$this->validate($rules, $messages)) {
            $errors = $this->validator->getErrors();
            log_message('error', 'Erros de validação: ' . json_encode($errors));
            return redirect()->back()->withInput()->with('errors', $errors);
        }
        
        // Atualizar dados do usuário
        $userData = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address')
        ];
        
        // Remover campos vazios
        $userData = array_filter($userData, function($value) {
            return $value !== null && $value !== '';
        });
        
        if (!empty($userData)) {
            $this->userModel->update($userId, $userData);
        }
        
        // Verificar se existe registro na tabela de professores
        $teacher = $this->teacherModel->getByUserId($userId);
        
        $teacherData = [
            'birth_date' => $this->request->getPost('birth_date') ?: null,
            'gender' => $this->request->getPost('gender') ?: null,
            'nationality' => $this->request->getPost('nationality') ?: null,
            'identity_document' => $this->request->getPost('identity_document') ?: null,
            'identity_type' => $this->request->getPost('identity_type') ?: null,
            'nif' => $this->request->getPost('nif') ?: null,
            'city' => $this->request->getPost('city') ?: null,
            'municipality' => $this->request->getPost('municipality') ?: null,
            'province' => $this->request->getPost('province') ?: null,
            'emergency_contact' => $this->request->getPost('emergency_contact') ?: null,
            'emergency_contact_name' => $this->request->getPost('emergency_contact_name') ?: null,
            'qualifications' => $this->request->getPost('qualifications') ?: null,
            'specialization' => $this->request->getPost('specialization') ?: null,
            'admission_date' => $this->request->getPost('admission_date') ?: null,
            'bank_account' => $this->request->getPost('bank_account') ?: null,
            'bank_name' => $this->request->getPost('bank_name') ?: null
        ];
        
        // Remover campos vazios
        $teacherData = array_filter($teacherData, function($value) {
            return $value !== null && $value !== '';
        });
        
        if (!empty($teacherData)) {
            if ($teacher) {
                // Atualizar registro existente
                $this->teacherModel->update($teacher->id, $teacherData);
            } else {
                // Criar novo registro
                $teacherData['user_id'] = $userId;
                $this->teacherModel->insert($teacherData);
            }
        }
        
        // Registrar ação
        log_action('update', 'teacher', $userId, 'Professor atualizou o perfil');
        
        return redirect()->to('/teachers/profile')->with('success', 'Perfil atualizado com sucesso');
    }
    
    /**
     * Update profile photo
     */
    public function updatePhoto()
    {
        $userId = currentUserId();
        
        $rules = [
            'photo' => 'uploaded[photo]|max_size[photo,2048]|is_image[photo]|mime_in[photo,image/jpg,image/jpeg,image/png]'
        ];
        
        if (!$this->validate($rules)) {
            $errors = $this->validator->getErrors();
            return redirect()->back()->withInput()->with('errors', $errors);
        }
        
        $file = $this->request->getFile('photo');
        
        if ($file->isValid() && !$file->hasMoved()) {
            // Criar diretório se não existir
            $uploadPath = 'uploads/teachers';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            
            $newName = $file->getRandomName();
            $file->move($uploadPath, $newName);
            
            // Remover foto antiga se existir
            $user = $this->userModel->find($userId);
            if ($user && $user->photo && file_exists($uploadPath . '/' . $user->photo)) {
                unlink($uploadPath . '/' . $user->photo);
            }
            
            // Atualizar na tabela de usuários
            $this->userModel->update($userId, ['photo' => $newName]);
            
            return redirect()->to('/teachers/profile')->with('success', 'Foto atualizada com sucesso');
        }
        
        return redirect()->back()->with('error', 'Erro ao fazer upload da foto');
    }
    
    /**
     * Get teacher statistics
     */
    private function getTeacherStats($userId)
    {
        // Buscar turmas do professor
        $classes = $this->classDisciplineModel
            ->select('tbl_class_disciplines.class_id, tbl_classes.class_name')
            ->join('tbl_classes', 'tbl_classes.id = tbl_class_disciplines.class_id')
            ->where('tbl_class_disciplines.teacher_id', $userId)
            ->where('tbl_class_disciplines.is_active', 1)
            ->groupBy('tbl_class_disciplines.class_id')
            ->findAll();
        
        $totalClasses = count($classes);
        
        // Buscar disciplinas que leciona
        $disciplines = $this->classDisciplineModel
            ->select('tbl_disciplines.discipline_name')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_class_disciplines.discipline_id')
            ->where('tbl_class_disciplines.teacher_id', $userId)
            ->where('tbl_class_disciplines.is_active', 1)
            ->groupBy('tbl_class_disciplines.discipline_id')
            ->findAll();
        
        $totalDisciplines = count($disciplines);
        
        // Calcular total de alunos
        $totalStudents = 0;
        foreach ($classes as $class) {
            $students = $this->enrollmentModel
                ->where('class_id', $class->class_id)
                ->where('status', 'Ativo')
                ->countAllResults();
            $totalStudents += $students;
        }
        
        // ✅ CORRIGIDO: Buscar próximos exames usando ExamScheduleModel
        $upcomingExams = $this->examScheduleModel
            ->select('
                tbl_exam_schedules.*,
                tbl_disciplines.discipline_name,
                tbl_classes.class_name,
                tbl_exam_boards.board_name,
                tbl_exam_boards.board_type
            ')
            ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_exam_schedules.discipline_id')
            ->join('tbl_classes', 'tbl_classes.id = tbl_exam_schedules.class_id')
            ->join('tbl_exam_boards', 'tbl_exam_boards.id = tbl_exam_schedules.exam_board_id')
            ->join('tbl_class_disciplines', 'tbl_class_disciplines.class_id = tbl_exam_schedules.class_id AND tbl_class_disciplines.discipline_id = tbl_exam_schedules.discipline_id')
            ->where('tbl_class_disciplines.teacher_id', $userId)
            ->where('tbl_exam_schedules.exam_date >=', date('Y-m-d'))
            ->where('tbl_exam_schedules.status', 'Agendado')
            ->orderBy('tbl_exam_schedules.exam_date', 'ASC')
            ->limit(5)
            ->findAll();
        
        return [
            'total_classes' => $totalClasses,
            'total_disciplines' => $totalDisciplines,
            'total_students' => $totalStudents,
            'upcoming_exams' => $upcomingExams,
            'upcoming_exams_count' => count($upcomingExams),
            'disciplines' => $disciplines
        ];
    }
}