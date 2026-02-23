<?php

namespace App\Controllers;

use App\Models\GradeLevelModel;
use App\Models\CourseModel;

class PublicEnrollment extends BaseController
{
    public function index()
    {
        $gradeModel = new GradeLevelModel();
        $courseModel = new CourseModel();
        
        // Buscar níveis de ensino ativos - método correto
        $gradeLevels = $gradeModel->where('is_active', 1)
                                  ->orderBy('sort_order', 'ASC')
                                  ->findAll();
        
        // Buscar cursos ativos - método correto
        $courses = $courseModel->where('is_active', 1)
                               ->orderBy('course_name', 'ASC')
                               ->findAll();
        
        // Converter para array se necessário
        $gradeLevelsArray = [];
        foreach($gradeLevels as $level) {
            if(is_object($level)) {
                $gradeLevelsArray[] = (array) $level;
            } else {
                $gradeLevelsArray[] = $level;
            }
        }
        
        $coursesArray = [];
        foreach($courses as $course) {
            if(is_object($course)) {
                $coursesArray[] = (array) $course;
            } else {
                $coursesArray[] = $course;
            }
        }
        
        $data = [
            'title' => 'Inscrição de Estudante - Escola Angolana Modelo',
            'gradeLevels' => $gradeLevelsArray,
            'courses' => $coursesArray
        ];
        
        return view('public/enrollment', $data);
    }
    
    public function submit()
    {
        // Validação dos dados
        $rules = [
            'full_name' => 'required|min_length[3]|max_length[255]',
            'birth_date' => 'required|valid_date',
            'gender' => 'required|in_list[Masculino,Feminino]',
            'identity_document' => 'required|min_length[5]|max_length[50]',
            'address' => 'required|min_length[5]|max_length[500]',
            'phone' => 'required|min_length[9]|max_length[20]',
            'email' => 'required|valid_email|max_length[255]',
            'grade_level' => 'required|numeric',
            'emergency_name' => 'required|min_length[3]|max_length[255]',
            'emergency_contact' => 'required|min_length[9]|max_length[20]',
            'terms' => 'required'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()
                ->withInput()
                ->with('errors', $this->validator->getErrors());
        }
        
        // Dados do formulário
        $data = [
            'full_name' => $this->request->getPost('full_name'),
            'birth_date' => $this->request->getPost('birth_date'),
            'gender' => $this->request->getPost('gender'),
            'identity_document' => $this->request->getPost('identity_document'),
            'address' => $this->request->getPost('address'),
            'phone' => $this->request->getPost('phone'),
            'email' => $this->request->getPost('email'),
            'grade_level' => $this->request->getPost('grade_level'),
            'course' => $this->request->getPost('course'),
            'previous_school' => $this->request->getPost('previous_school'),
            'emergency_name' => $this->request->getPost('emergency_name'),
            'emergency_contact' => $this->request->getPost('emergency_contact')
        ];
        
        // TODO: Salvar no banco de dados
        // Por enquanto, apenas simula o envio
        
        // Enviar email de confirmação (opcional)
        // $this->sendConfirmationEmail($data);
        
        return redirect()->to('/inscricao')
            ->with('success', 'Inscrição realizada com sucesso! Entraremos em contato em breve.');
    }
    
    private function sendConfirmationEmail($data)
    {
        $email = \Config\Services::email();
        
        $email->setTo($data['email']);
        $email->setSubject('Confirmação de Inscrição - Escola Angolana Modelo');
        
        $message = "Olá {$data['full_name']},\n\n";
        $message .= "Recebemos sua inscrição na Escola Angolana Modelo. ";
        $message .= "Em breve nossa equipe entrará em contato para dar continuidade ao processo.\n\n";
        $message .= "Dados da inscrição:\n";
        $message .= "- Nome: {$data['full_name']}\n";
        $message .= "- Telefone: {$data['phone']}\n";
        $message .= "- Email: {$data['email']}\n\n";
        $message .= "Atenciosamente,\n";
        $message .= "Equipe Escola Angolana Modelo";
        
        $email->setMessage($message);
        $email->send();
    }
}