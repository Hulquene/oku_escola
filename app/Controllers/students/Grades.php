<?php

namespace App\Controllers\Students;

use App\Controllers\BaseController;
use App\Models\StudentModel;
use App\Models\ExamResultModel;
use App\Models\FinalGradeModel;
use App\Models\SemesterModel;

class Grades extends BaseController
{
    protected $studentModel;
    protected $examResultModel;
    protected $finalGradeModel;
    protected $semesterModel;
    
    public function __construct()
    {
        $this->studentModel = new StudentModel();
        $this->examResultModel = new ExamResultModel();
        $this->finalGradeModel = new FinalGradeModel();
        $this->semesterModel = new SemesterModel();
    }
    
    /**
     * My grades
     */
    public function index()
    {
        $data['title'] = 'Minhas Notas';
        
        $userId = $this->session->get('user_id');
        $student = $this->studentModel->getByUserId($userId);
        
        if (!$student) {
            return redirect()->to('/students/dashboard')->with('error', 'Perfil não encontrado');
        }
        
        $semesterId = $this->request->getGet('semester') ?: 
            ($this->semesterModel->getCurrent()->id ?? null);
        
        // Get exam results
        $data['examResults'] = $this->examResultModel->getStudentResults($student->id, $semesterId);
        
        // Get final grades
        $data['finalGrades'] = $this->finalGradeModel->getReportCard($student->id, $semesterId);
        
        // Calculate averages
        $total = 0;
        foreach ($data['finalGrades'] as $grade) {
            $total += $grade->final_score;
        }
        $data['average'] = count($data['finalGrades']) > 0 ? 
            round($total / count($data['finalGrades']), 2) : 0;
        
        $data['semesters'] = $this->semesterModel->getActive();
        $data['selectedSemester'] = $semesterId;
        
        return view('students/grades/index', $data);
    }
    
    /**
     * Report card
     */
    public function reportCard()
    {
        $data['title'] = 'Boletim de Notas';
        
        $userId = $this->session->get('user_id');
        $student = $this->studentModel->getByUserId($userId);
        
        if (!$student) {
            return redirect()->to('/students/dashboard')->with('error', 'Perfil não encontrado');
        }
        
        $semesterId = $this->request->getGet('semester') ?: 
            ($this->semesterModel->getCurrent()->id ?? null);
        
        $reportCardModel = new \App\Models\ReportCardModel();
        
        // Generate or get existing report card
        $currentEnrollment = $this->studentModel->getCurrentEnrollment($student->id);
        
        if ($currentEnrollment) {
            $reportCardId = $reportCardModel->generateForStudent($currentEnrollment->id, $semesterId);
            $data['reportCard'] = $reportCardModel->getWithDetails($reportCardId);
            $data['grades'] = $this->finalGradeModel->getReportCard($student->id, $semesterId);
        }
        
        $data['semesters'] = $this->semesterModel->getActive();
        $data['selectedSemester'] = $semesterId;
        
        return view('students/grades/report_card', $data);
    }
}