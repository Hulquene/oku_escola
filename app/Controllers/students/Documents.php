<?php
// app/Controllers/students/Documents.php

namespace App\Controllers\students;

use App\Controllers\BaseDocuments;
use App\Models\EnrollmentModel;

class Documents extends BaseDocuments
{
    public function __construct()
    {
        parent::__construct();
        $this->userType = 'student';
        $this->viewPath = 'students/documents';
        $this->uploadPath = FCPATH . 'uploads/documents/students';
    }
    
    /**
     * Obter tipos de documento específicos para estudantes
     */
    protected function getDocumentTypes()
    {
        return $this->documentTypeModel->getForStudent();
    }
    
    /**
     * Documentos obrigatórios para estudantes
     */
    public function required()
    {
        $data['title'] = 'Documentos Obrigatórios';
        $data['requiredDocs'] = $this->documentTypeModel->getRequired('student');
        
        // Verificar quais já foram enviados
        $userId = currentUserId();
        $userDocs = $this->documentModel->getVerifiedByUser($userId, 'student');
        
        $submittedDocs = [];
        foreach ($userDocs as $doc) {
            $submittedDocs[] = $doc->document_type;
        }
        
        $allSubmitted = true;
        foreach ($data['requiredDocs'] as $doc) {
            $doc->is_submitted = in_array($doc->type_code, $submittedDocs);
            if (!$doc->is_submitted) {
                $allSubmitted = false;
            }
        }
        
        $data['all_submitted'] = $allSubmitted;
        $data['stats'] = $this->getUserStats();
        
        return view($this->viewPath . '/required', $data);
    }
    
    /**
     * Documentos para matrícula
     */
    public function enrollment()
    {
        $data['title'] = 'Documentos para Matrícula';
        
        $userId = currentUserId();
        $studentId = getStudentIdFromUser();
        
        $enrollmentModel = new EnrollmentModel();
        $enrollment = $enrollmentModel->getCurrentForStudent($studentId);
        $data['enrollment'] = $enrollment;
        
        // Documentos específicos para matrícula
        $data['enrollmentDocs'] = $this->documentModel
            ->where('user_id', $userId)
            ->where('user_type', 'student')
            ->whereIn('document_type', ['BI', 'CERTIDAO_NASCIMENTO', 'FOTO', 'CERTIFICADO_CLASSES'])
            ->orderBy('created_at', 'DESC')
            ->findAll();
        
        // Verificar status da matrícula
        if ($enrollment) {
            $data['can_enroll'] = $this->checkEnrollmentDocuments($userId);
        }
        
        return view($this->viewPath . '/enrollment', $data);
    }
    
    /**
     * Verificar se tem todos documentos para matrícula
     */
    private function checkEnrollmentDocuments($userId)
    {
        $requiredForEnrollment = ['BI', 'CERTIDAO_NASCIMENTO', 'FOTO', 'CERTIFICADO_CLASSES'];
        $userDocs = $this->documentModel->getVerifiedByUser($userId, 'student');
        
        $submitted = [];
        foreach ($userDocs as $doc) {
            $submitted[] = $doc->document_type;
        }
        
        foreach ($requiredForEnrollment as $req) {
            if (!in_array($req, $submitted)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Histórico de documentos (todos os documentos do aluno)
     */
    public function history()
    {
        $data['title'] = 'Histórico de Documentos';
        
        $userId = currentUserId();
        
        $data['documents'] = $this->documentModel
            ->where('user_id', $userId)
            ->where('user_type', 'student')
            ->orderBy('created_at', 'DESC')
            ->findAll();
        
        $data['stats'] = $this->documentModel->getStatistics($userId);
        
        return view($this->viewPath . '/history', $data);
    }
}