<?php
// app/Controllers/students/Profile.php

namespace App\Controllers\students;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use App\Models\UserModel;
use App\Models\EnrollmentModel;
use App\Models\StudentGuardianModel;
use App\Models\GuardianModel;

class Profile extends BaseController
{
    protected $studentModel;
    protected $userModel;
    protected $enrollmentModel;
    protected $studentGuardianModel;
    protected $guardianModel;
    
    public function __construct()
    {
        helper(['auth', 'student', 'form']);
        
        $this->studentModel = new StudentModel();
        $this->userModel = new UserModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->studentGuardianModel = new StudentGuardianModel();
        $this->guardianModel = new GuardianModel();
    }
    
    /**
     * Student profile page
     */
    public function index()
    {
        $data['title'] = 'Meu Perfil';
        
        $userId = currentUserId();
        $studentId = getStudentIdFromUser();
        
        if (!$studentId) {
            return redirect()->to('/students/dashboard')->with('error', 'Perfil de aluno não encontrado');
        }
        
        // Buscar dados do aluno com usuário
        $data['student'] = $this->studentModel->getWithUser($studentId);
        
        // Buscar dados do usuário
        $data['user'] = $this->userModel->find($userId);
        
        // Buscar matrícula atual
        $data['currentEnrollment'] = $this->enrollmentModel->getCurrentForStudent($studentId);
        
        // Buscar encarregados
        $data['guardians'] = $this->studentGuardianModel->getAuthorizedGuardians($studentId);
        
        // Estatísticas do aluno
        $data['stats'] = $this->getStudentStats($studentId);
        
        return view('students/profile/index', $data);
    }
    
    /**
     * Update profile information
     */
    public function update()
    {
        $userId = currentUserId();
        $studentId = getStudentIdFromUser();
        
        if (!$studentId) {
            return redirect()->to('/students/dashboard')->with('error', 'Perfil de aluno não encontrado');
        }
        
        // Regras de validação
        $rules = [
            'first_name' => 'required|min_length[2]|max_length[100]',
            'last_name' => 'required|min_length[2]|max_length[100]',
            'phone' => 'permit_empty|min_length[9]|max_length[20]',
            'address' => 'permit_empty|max_length[255]',
            'birth_date' => 'permit_empty|valid_date',
            'birth_place' => 'permit_empty|max_length[255]',
            'gender' => 'required|in_list[Masculino,Feminino]',
            'nationality' => 'permit_empty|max_length[100]',
            'identity_document' => 'permit_empty|max_length[50]',
            'identity_type' => 'permit_empty|in_list[BI,Passaporte,Cédula,Outro]',
            'nif' => 'permit_empty|max_length[20]',
            'city' => 'permit_empty|max_length[100]',
            'municipality' => 'permit_empty|max_length[100]',
            'province' => 'permit_empty|max_length[100]',
            'emergency_contact' => 'permit_empty|max_length[20]',
            'emergency_contact_name' => 'permit_empty|max_length[255]',
            'previous_school' => 'permit_empty|max_length[255]',
            'previous_grade' => 'permit_empty|max_length[50]',
            'special_needs' => 'permit_empty|max_length[500]',
            'health_conditions' => 'permit_empty|max_length[500]',
            'blood_type' => 'permit_empty|max_length[5]'
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
            ],
            'gender' => [
                'required' => 'O gênero é obrigatório',
                'in_list' => 'Gênero inválido. Deve ser Masculino ou Feminino'
            ]
        ];
        
        if (!$this->validate($rules, $messages)) {
            $errors = $this->validator->getErrors();
            log_message('error', 'Erros de validação: ' . json_encode($errors));
            return redirect()->back()->withInput()->with('errors', $errors);
        }
        
        // Dados recebidos do formulário
        $postData = $this->request->getPost();
        log_message('debug', 'Dados recebidos: ' . json_encode($postData));
        
        // Atualizar dados do usuário
        $userData = [
            'first_name' => $this->request->getPost('first_name'),
            'last_name' => $this->request->getPost('last_name'),
            'phone' => $this->request->getPost('phone'),
            'address' => $this->request->getPost('address')
        ];
        
        // Remover campos vazios para não sobrescrever com null
        $userData = array_filter($userData, function($value) {
            return $value !== null && $value !== '';
        });
        
        if (!empty($userData)) {
            $this->userModel->update($userId, $userData);
        }
        
        // Atualizar dados do aluno
        $studentData = [
            'birth_date' => $this->request->getPost('birth_date') ?: null,
            'birth_place' => $this->request->getPost('birth_place') ?: null,
            'gender' => $this->request->getPost('gender'),
            'nationality' => $this->request->getPost('nationality') ?: 'Angolana',
            'identity_document' => $this->request->getPost('identity_document') ?: null,
            'identity_type' => $this->request->getPost('identity_type') ?: null,
            'nif' => $this->request->getPost('nif') ?: null,
            'city' => $this->request->getPost('city') ?: null,
            'municipality' => $this->request->getPost('municipality') ?: null,
            'province' => $this->request->getPost('province') ?: null,
            'emergency_contact' => $this->request->getPost('emergency_contact') ?: null,
            'emergency_contact_name' => $this->request->getPost('emergency_contact_name') ?: null,
            'previous_school' => $this->request->getPost('previous_school') ?: null,
            'previous_grade' => $this->request->getPost('previous_grade') ?: null,
            'special_needs' => $this->request->getPost('special_needs') ?: null,
            'health_conditions' => $this->request->getPost('health_conditions') ?: null,
            'blood_type' => $this->request->getPost('blood_type') ?: null
        ];
        
        // Remover campos que não foram enviados no formulário atual
        // Isso é importante porque o formulário tem várias abas
        $allowedFields = array_keys($rules);
        $studentData = array_intersect_key($studentData, array_flip($allowedFields));
        
        // Remover campos vazios para não sobrescrever com null
        $studentData = array_filter($studentData, function($value) {
            return $value !== null && $value !== '';
        });
        
        if (!empty($studentData)) {
            $this->studentModel->update($studentId, $studentData);
        }
        
        // Registrar ação
        log_action('update', 'student', $studentId, 'Aluno atualizou o perfil');
        
        return redirect()->to('/students/profile')->with('success', 'Perfil atualizado com sucesso');
    }
    
    /**
     * Update profile photo
     */
    public function updatePhoto()
    {
        $studentId = getStudentIdFromUser();
        
        if (!$studentId) {
            return redirect()->to('/students/dashboard')->with('error', 'Perfil de aluno não encontrado');
        }
        
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
            $uploadPath = 'uploads/students';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            
            $newName = $file->getRandomName();
            $file->move($uploadPath, $newName);
            
            // Remover foto antiga se existir
            $student = $this->studentModel->find($studentId);
            if ($student && $student->photo && file_exists($uploadPath . '/' . $student->photo)) {
                unlink($uploadPath . '/' . $student->photo);
            }
            
            // Atualizar na tabela de estudantes
            $this->studentModel->update($studentId, ['photo' => $newName]);
            
            // Atualizar também na tabela de usuários
            $userId = currentUserId();
            $this->userModel->update($userId, ['photo' => $newName]);
            
            return redirect()->to('/students/profile')->with('success', 'Foto atualizada com sucesso');
        }
        
        return redirect()->back()->with('error', 'Erro ao fazer upload da foto');
    }
    
    /**
     * View guardians
     */
    public function guardians()
    {
        $data['title'] = 'Meus Encarregados';
        
        $studentId = getStudentIdFromUser();
        
        if (!$studentId) {
            return redirect()->to('/students/dashboard')->with('error', 'Perfil de aluno não encontrado');
        }
        
        $data['guardians'] = $this->studentGuardianModel
            ->select('
                tbl_student_guardians.*,
                tbl_guardians.full_name,
                tbl_guardians.guardian_type,
                tbl_guardians.phone,
                tbl_guardians.email,
                tbl_guardians.profession,
                tbl_guardians.workplace,
                tbl_guardians.address,
                tbl_guardians.city,
                tbl_guardians.province
            ')
            ->join('tbl_guardians', 'tbl_guardians.id = tbl_student_guardians.guardian_id')
            ->where('tbl_student_guardians.student_id', $studentId)
            ->where('tbl_student_guardians.is_authorized', 1)
            ->findAll();
        
        return view('students/profile/guardians', $data);
    }
    
    /**
     * Academic history
     */
    public function academicHistory()
    {
        $data['title'] = 'Histórico Acadêmico';
        
        $studentId = getStudentIdFromUser();
        
        if (!$studentId) {
            return redirect()->to('/students/dashboard')->with('error', 'Perfil de aluno não encontrado');
        }
        
        // Buscar histórico de matrículas
        $data['enrollments'] = $this->enrollmentModel
            ->select('
                tbl_enrollments.*,
                tbl_classes.class_name,
                tbl_classes.class_code,
                tbl_classes.class_shift,
                tbl_academic_years.year_name,
                tbl_academic_years.start_date as year_start,
                tbl_academic_years.end_date as year_end,
                tbl_grade_levels.level_name as grade_level_name
            ')
            ->join('tbl_classes', 'tbl_classes.id = tbl_enrollments.class_id')
            ->join('tbl_academic_years', 'tbl_academic_years.id = tbl_enrollments.academic_year_id')
            ->join('tbl_grade_levels', 'tbl_grade_levels.id = tbl_classes.grade_level_id', 'left')
            ->where('tbl_enrollments.student_id', $studentId)
            ->orderBy('tbl_academic_years.start_date', 'DESC')
            ->findAll();
        
        // Buscar notas finais por ano/semestre
        $finalGradeModel = new \App\Models\FinalGradeModel();
        $semesterModel = new \App\Models\SemesterModel();
        
        $data['academicRecords'] = [];
        
        foreach ($data['enrollments'] as $enrollment) {
            $semesters = $semesterModel->where('academic_year_id', $enrollment->academic_year_id)->findAll();
            
            $yearRecords = [
                'enrollment' => $enrollment,
                'semesters' => []
            ];
            
            foreach ($semesters as $semester) {
                $grades = $finalGradeModel
                    ->select('
                        tbl_final_grades.*,
                        tbl_disciplines.discipline_name
                    ')
                    ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_final_grades.discipline_id')
                    ->where('tbl_final_grades.enrollment_id', $enrollment->id)
                    ->where('tbl_final_grades.semester_id', $semester->id)
                    ->findAll();
                
                if (!empty($grades)) {
                    $total = 0;
                    foreach ($grades as $grade) {
                        $total += $grade->final_score;
                    }
                    $average = $total / count($grades);
                    
                    $yearRecords['semesters'][] = [
                        'semester' => $semester,
                        'grades' => $grades,
                        'average' => round($average, 1),
                        'total_disciplines' => count($grades)
                    ];
                }
            }
            
            $data['academicRecords'][] = $yearRecords;
        }
        
        return view('students/profile/academic_history', $data);
    }
    
    /**
     * Get student statistics
     */
    private function getStudentStats($studentId)
    {
        // Contar disciplinas
        $enrollment = $this->enrollmentModel->getCurrentForStudent($studentId);
        $disciplines = 0;
        
        if ($enrollment) {
            $classDisciplineModel = new \App\Models\ClassDisciplineModel();
            $disciplines = $classDisciplineModel
                ->where('class_id', $enrollment->class_id)
                ->where('is_active', 1)
                ->countAllResults();
        }
        
        // Contar presenças
        $attendanceModel = new \App\Models\AttendanceModel();
        $totalAttendance = 0;
        $presentAttendance = 0;
        $attendanceRate = 0;
        
        if ($enrollment) {
            $totalAttendance = $attendanceModel
                ->where('enrollment_id', $enrollment->id)
                ->countAllResults();
            
            $presentAttendance = $attendanceModel
                ->where('enrollment_id', $enrollment->id)
                ->whereIn('status', ['Presente', 'Atrasado', 'Falta Justificada'])
                ->countAllResults();
            
            $attendanceRate = $totalAttendance > 0 ? round(($presentAttendance / $totalAttendance) * 100, 1) : 0;
        }
        
        // Resumo financeiro
        $studentFeeModel = new \App\Models\StudentFeeModel();
        $fees = [];
        $totalFees = 0;
        
        if ($enrollment) {
            $fees = $studentFeeModel
                ->where('enrollment_id', $enrollment->id)
                ->whereIn('status', ['Pendente', 'Vencido'])
                ->findAll();
            
            foreach ($fees as $fee) {
                $totalFees += $fee->total_amount;
            }
        }
        
        return [
            'total_disciplines' => $disciplines,
            'attendance_rate' => $attendanceRate,
            'total_attendance' => $totalAttendance,
            'pending_fees' => count($fees),
            'pending_fees_amount' => $totalFees
        ];
    }
}