<?php

namespace App\Controllers;

use App\Models\CourseModel;
use App\Models\GradeLevelModel;
use App\Models\SettingsModel;

class PublicSite extends BaseController
{
    public function index()
    {
        $courseModel = new CourseModel();
        $settingsModel = new SettingsModel();
        
        // Buscar cursos ativos
        $courses = $courseModel->getActive();
        
        // Converter para array se necessário
        $coursesArray = [];
        foreach($courses as $course) {
            if(is_object($course)) {
                $coursesArray[] = (array) $course;
            } else {
                $coursesArray[] = $course;
            }
        }
        
        // Informações da escola
        $schoolInfo = [
            'name' => $settingsModel->get('school_name', 'Escola Angolana Modelo'),
            'address' => $settingsModel->get('school_address', 'Rua da Educação, 123 - Luanda'),
            'phone' => $settingsModel->get('school_phone', '+244 999 999 999'),
            'email' => $settingsModel->get('school_email', 'info@escola.ao'),
            'founded' => $settingsModel->get('school_founded', '2010'),
            'students' => $settingsModel->get('school_students', '1.500+'),
            'teachers' => $settingsModel->get('school_teachers', '80+'),
            'classes' => $settingsModel->get('school_classes', '45+'),
            'mission' => $settingsModel->get('school_mission', 'Formar cidadãos críticos, criativos e comprometidos com o desenvolvimento de Angola.'),
            'vision' => $settingsModel->get('school_vision', 'Ser referência nacional em educação de qualidade, inovação e inclusão social.'),
            'values' => $settingsModel->get('school_values', 'Excelência, Ética, Inovação, Inclusão, Compromisso Social'),
            'history' => $settingsModel->get('school_history', 'Fundada em 2010, a Escola Angolana Modelo nasceu com o propósito de oferecer educação de qualidade alinhada às necessidades do século XXI.')
        ];
        
        $data = [
            'title' => 'Escola Angolana Modelo - Educação de Qualidade',
            'courses' => $coursesArray,
            'schoolInfo' => $schoolInfo,
            'featuredCourses' => array_slice($coursesArray, 0, 3)
        ];
        
        return view('public/home', $data);
    }
    
    public function courses()
    {
        $courseModel = new CourseModel();
        
        // Buscar cursos ativos - CORRIGIDO
        $courses = $courseModel->getActive();
        
        // Converter para array se necessário
        $coursesArray = [];
        foreach($courses as $course) {
            if(is_object($course)) {
                $coursesArray[] = (array) $course;
            } else {
                $coursesArray[] = $course;
            }
        }
        
        $data = [
            'title' => 'Nossos Cursos - Escola Angolana Modelo',
            'courses' => $coursesArray
        ];
        
        return view('public/courses', $data);
    }
    
    public function courseDetail($id)
    {
        $courseModel = new CourseModel();
        
        $course = $courseModel->getWithGradeLevels($id);
        
        if(!$course) {
            return redirect()->to('/cursos')->with('error', 'Curso não encontrado');
        }
        
        // Converter para array se for objeto
        if(is_object($course)) {
            $course = (array) $course;
        }
        
        $data = [
            'title' => $course['course_name'] . ' - Escola Angolana Modelo',
            'course' => $course
        ];
        
        return view('public/course_detail', $data);
    }
    
    public function about()
    {
        $settingsModel = new SettingsModel();
        
        $schoolInfo = [
            'name' => $settingsModel->get('school_name', 'Escola Angolana Modelo'),
            'mission' => $settingsModel->get('school_mission', 'Formar cidadãos críticos, criativos e comprometidos com o desenvolvimento de Angola.'),
            'vision' => $settingsModel->get('school_vision', 'Ser referência nacional em educação de qualidade, inovação e inclusão social.'),
            'values' => $settingsModel->get('school_values', 'Excelência, Ética, Inovação, Inclusão, Compromisso Social'),
            'history' => $settingsModel->get('school_history', 'Fundada em 2010, a Escola Angolana Modelo nasceu com o propósito de oferecer educação de qualidade alinhada às necessidades do século XXI.')
        ];
        
        $data = [
            'title' => 'Sobre Nós - Escola Angolana Modelo',
            'schoolInfo' => $schoolInfo
        ];
        
        return view('public/about', $data);
    }
    
    public function contact()
    {
        $data = [
            'title' => 'Contato - Escola Angolana Modelo'
        ];
        
        return view('public/contact', $data);
    }
    
    public function sendContact()
    {
        $rules = [
            'name' => 'required|min_length[3]',
            'email' => 'required|valid_email',
            'phone' => 'required',
            'message' => 'required|min_length[10]'
        ];
        
        if(!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        // Aqui você pode enviar email ou salvar no banco de dados
        
        return redirect()->to('/contato')->with('success', 'Mensagem enviada com sucesso! Entraremos em contato em breve.');
    }
}