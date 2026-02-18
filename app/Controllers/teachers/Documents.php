<?php
// app/Controllers/teachers/Documents.php

namespace App\Controllers\teachers;

use App\Controllers\BaseDocuments;

class Documents extends BaseDocuments
{
    public function __construct()
    {
        parent::__construct();
        $this->userType = 'teacher';
        $this->viewPath = 'teachers/documents';
        $this->uploadPath = FCPATH . 'uploads/documents/teachers';
    }
    
    /**
     * Obter tipos de documento específicos para professores
     */
    protected function getDocumentTypes()
    {
        return $this->documentTypeModel->getForTeacher();
    }
    
    /**
     * Documentos contratuais
     */
    public function contract()
    {
        $data['title'] = 'Documentos Contratuais';
        
        $userId = currentUserId();
        
        $data['contractDocs'] = $this->documentModel
            ->where('user_id', $userId)
            ->where('user_type', 'teacher')
            ->whereIn('document_type', ['BI', 'DIPLOMA', 'CERTIFICADO_HABILITACOES', 'CURRICULO'])
            ->orderBy('created_at', 'DESC')
            ->findAll();
        
        // Verificar documentos obrigatórios para contrato
        $requiredForContract = ['BI', 'DIPLOMA', 'CERTIFICADO_HABILITACOES'];
        $userDocs = $this->documentModel->getVerifiedByUser($userId, 'teacher');
        
        $submitted = [];
        foreach ($userDocs as $doc) {
            $submitted[] = $doc->document_type;
        }
        
        $data['missing_docs'] = [];
        foreach ($requiredForContract as $req) {
            if (!in_array($req, $submitted)) {
                $typeInfo = $this->documentTypeModel->getByCode($req);
                if ($typeInfo) {
                    $data['missing_docs'][] = $typeInfo->type_name;
                }
            }
        }
        
        $data['stats'] = $this->getUserStats();
        
        return view($this->viewPath . '/contract', $data);
    }
    
    /**
     * Documentos para concurso
     */
    public function contest()
    {
        $data['title'] = 'Documentos para Concurso';
        
        $userId = currentUserId();
        
        $data['contestDocs'] = $this->documentModel
            ->where('user_id', $userId)
            ->where('user_type', 'teacher')
            ->whereIn('document_type', ['BI', 'DIPLOMA', 'CERTIFICADO_HABILITACOES', 'CURRICULO', 'DECLARACAO_TRABALHO'])
            ->orderBy('created_at', 'DESC')
            ->findAll();
        
        return view($this->viewPath . '/contest', $data);
    }
    
    /**
     * Histórico de documentos
     */
    public function history()
    {
        $data['title'] = 'Histórico de Documentos';
        
        $userId = currentUserId();
        
        $data['documents'] = $this->documentModel
            ->where('user_id', $userId)
            ->where('user_type', 'teacher')
            ->orderBy('created_at', 'DESC')
            ->findAll();
        
        $data['stats'] = $this->documentModel->getStatistics($userId);
        
        return view($this->viewPath . '/history', $data);
    }
}