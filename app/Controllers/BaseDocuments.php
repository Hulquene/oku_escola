<?php
// app/Controllers/BaseDocuments.php

namespace App\Controllers;

use App\Models\DocumentModel;
use App\Models\DocumentTypeModel;
use App\Models\DocumentRequestModel;
use App\Models\RequestableDocumentModel;

abstract class BaseDocuments extends BaseController
{
    protected $documentModel;
    protected $documentTypeModel;
    protected $requestModel;
    protected $requestableModel;
    
    protected $userType; // 'student' ou 'teacher'
    protected $viewPath; // caminho das views
    protected $uploadPath; // caminho para uploads
    
    public function __construct()
    {
        $this->documentModel = new DocumentModel();
        $this->documentTypeModel = new DocumentTypeModel();
        $this->requestModel = new DocumentRequestModel();
        $this->requestableModel = new RequestableDocumentModel();
        
        helper(['auth', 'form', 'filesystem']);
        
        // Definir nas classes filhas
        $this->userType = '';
        $this->viewPath = '';
        $this->uploadPath = '';
    }
    
    /**
     * Documentos do usuário
     */
    public function index()
    {
        $data['title'] = 'Meus Documentos';
        $userId = currentUserId();
        
        $data['documents'] = $this->documentModel->getByUser($userId, $this->userType);
        $data['documentTypes'] = $this->getDocumentTypes();
        $data['stats'] = $this->documentModel->getStatistics($userId);
        $data['userType'] = $this->userType;
        
        return view($this->viewPath . '/index', $data);
    }
    
        /**
     * Solicitações de documentos
     */
    public function requests()
    {
        $data['title'] = 'Minhas Solicitações';
        $userId = currentUserId();
        
        // Buscar solicitações do usuário
        $data['requests'] = $this->requestModel
            ->where('user_id', $userId)
            ->where('user_type', $this->userType)
            ->orderBy('created_at', 'DESC')
            ->findAll();
        
        // Mapear userType para o formato do banco de dados
        $dbUserType = $this->userType;
        if ($this->userType == 'student') {
            $dbUserType = 'students';
        } elseif ($this->userType == 'teacher') {
            $dbUserType = 'teachers';
        }
        
        // Buscar documentos disponíveis para solicitação
        $data['requestableDocs'] = $this->requestableModel
            ->where('is_active', 1)
            ->groupStart()
                ->where('available_for', $dbUserType)
                ->orWhere('available_for', 'all')
            ->groupEnd()
            ->orderBy('category', 'ASC')
            ->orderBy('document_name', 'ASC')
            ->findAll();
        
        return view($this->viewPath . '/requests', $data);
    }
    
    /**
     * Arquivo de documentos antigos
     */
    /**
 * Arquivo de documentos antigos
 */
    public function archive()
    {
        $data['title'] = 'Arquivo de Documentos';
        $userId = currentUserId();
        
        // Documentos com mais de 1 ano
        $oneYearAgo = date('Y-m-d H:i:s', strtotime('-1 year'));
        
        $data['archive'] = $this->documentModel
            ->where('user_id', $userId)
            ->where('user_type', $this->userType)
            ->where('created_at <', $oneYearAgo)
            ->orderBy('created_at', 'DESC')
            ->findAll();
        
        // 🔴 LINHA FALTANDO - Adicione esta linha:
        $data['documentTypes'] = $this->getDocumentTypes();
        
        return view($this->viewPath . '/archive', $data);
    }
    
    /**
     * Upload de documento
     */
    public function upload()
    {
        $userId = currentUserId();
        
        $rules = [
            'document_type' => 'required',
            'document_file' => [
                'uploaded[document_file]',
                'max_size[document_file,5120]', // 5MB
            ]
        ];
        
        $docType = $this->documentTypeModel->getByCode($this->request->getPost('document_type'));
        if ($docType) {
            $allowedExts = explode(',', $docType->allowed_extensions);
            $extRule = 'ext_in[document_file,' . implode(',', $allowedExts) . ']';
            $rules['document_file'][] = $extRule;
            
            // Adicionar regra de tamanho específica do tipo
            if ($docType->max_size) {
                $rules['document_file'][] = "max_size[document_file,{$docType->max_size}]";
            }
        }
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $file = $this->request->getFile('document_file');
        
        if ($file->isValid() && !$file->hasMoved()) {
            // Obter informações do arquivo ANTES de mover
            $clientName = $file->getClientName();
            $fileSize = $file->getSize();
            $fileMime = $file->getMimeType(); // Obter MIME type antes de mover
            
            // Criar diretório se não existir
            $uploadPath = $this->uploadPath . '/' . $userId;
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            
            $newName = $file->getRandomName();
            
            // Mover o arquivo
            $file->move($uploadPath, $newName);
            
            $data = [
                'user_id' => $userId,
                'user_type' => $this->userType,
                'document_type' => $this->request->getPost('document_type'),
                'document_name' => $clientName,
                'document_path' => $uploadPath . '/' . $newName,
                'document_size' => $fileSize,
                'document_mime' => $fileMime, // Usar o MIME obtido antes de mover
                'description' => $this->request->getPost('description'),
                'is_verified' => 0
            ];
            
            if ($this->documentModel->insert($data)) {
                return redirect()->back()->with('success', 'Documento enviado com sucesso! Aguarde verificação.');
            }
        }
        
        return redirect()->back()->with('error', 'Erro ao fazer upload do documento.');
    }
    
  /**
 * Criar solicitação de documento
 */
public function createRequest()
{
    $userId = currentUserId();
    
    $rules = [
        'document_code' => 'required',
        'purpose' => 'required|min_length[5]|max_length[500]', // Reduzido para 5 caracteres
        'quantity' => 'permit_empty|numeric|greater_than[0]|less_than[11]',
        'format' => 'required|in_list[digital,impresso,ambos]',
        'delivery_method' => 'required|in_list[retirar,email,correio]',
        'delivery_address' => 'permit_empty|max_length[500]'
    ];
    
    if ($this->request->getPost('delivery_method') == 'correio') {
        $rules['delivery_address'] = 'required|max_length[500]';
    }
    
    if (!$this->validate($rules)) {
        $errors = $this->validator->getErrors();
        log_message('error', 'Erros de validação na solicitação: ' . json_encode($errors));
        return redirect()->back()->withInput()->with('errors', $errors);
    }
    
    $docType = $this->requestableModel->where('document_code', $this->request->getPost('document_code'))->first();
    
    if (!$docType) {
        log_message('error', 'Tipo de documento inválido: ' . $this->request->getPost('document_code'));
        return redirect()->back()->withInput()->with('error', 'Tipo de documento inválido');
    }
    
    // Verificar se usuário tem documentos obrigatórios
    if ($docType->requires_approval) {
        $requiredDocs = $this->documentTypeModel->getRequired($this->userType);
        $userDocs = $this->documentModel->getVerifiedByUser($userId, $this->userType);
        
        $submittedTypes = [];
        foreach ($userDocs as $doc) {
            $submittedTypes[] = $doc->document_type;
        }
        
        $missingDocs = [];
        foreach ($requiredDocs as $req) {
            if (!in_array($req->type_code, $submittedTypes)) {
                $missingDocs[] = $req->type_name;
            }
        }
        
        if (!empty($missingDocs)) {
            return redirect()->back()->withInput()->with('error', 
                'Para solicitar este documento, precisa primeiro enviar: ' . implode(', ', $missingDocs));
        }
    }
    
    // Gerar número de solicitação único
    $requestNumber = $this->generateUniqueRequestNumber();
    
    // Dados para inserção - REMOVIDO document_code
    $data = [
        'user_id' => $userId,
        'user_type' => $this->userType,
        'request_number' => $requestNumber,
        'document_type' => $docType->document_name,  // Apenas o nome do documento
        'document_code' => $this->request->getPost('document_code'),    
        'purpose' => $this->request->getPost('purpose'),
        'quantity' => $this->request->getPost('quantity') ?: 1,
        'format' => $this->request->getPost('format'),
        'delivery_method' => $this->request->getPost('delivery_method'),
        'delivery_address' => $this->request->getPost('delivery_address'),
        'fee_amount' => $docType->fee_amount,
        'payment_status' => $docType->fee_amount > 0 ? 'pending' : 'waived',
        'status' => 'pending'
    ];
    
    log_message('debug', 'Tentando inserir solicitação: ' . json_encode($data));
    
    if ($this->requestModel->insert($data)) {
        $message = 'Solicitação criada com sucesso! Nº: ' . $requestNumber;
        if ($docType->fee_amount > 0) {
            $message .= ' - Valor a pagar: ' . number_format($docType->fee_amount, 2, ',', '.') . ' Kz';
        }
        return redirect()->to('/' . $this->userType . 's/documents/requests')
            ->with('success', $message);
    } else {
        $errors = $this->requestModel->errors();
        log_message('error', 'Erro ao inserir solicitação: ' . json_encode($errors));
        return redirect()->back()->withInput()->with('error', 'Erro ao criar solicitação: ' . implode(', ', $errors));
    }
}

/**
 * Gerar número de solicitação único
 */
private function generateUniqueRequestNumber()
{
    $prefix = 'REQ';
    $date = date('Ymd');
    $attempts = 0;
    $maxAttempts = 10;
    
    do {
        $suffix = str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
        $requestNumber = $prefix . $date . $suffix;
        
        $existing = $this->requestModel->where('request_number', $requestNumber)->first();
        $attempts++;
        
        if ($attempts > $maxAttempts) {
            // Se falhar, usar timestamp
            $requestNumber = $prefix . $date . time();
            break;
        }
    } while ($existing);
    
    return $requestNumber;
}
    
    /**
     * Visualizar solicitação
     */
    public function viewRequest($id)
    {
        $userId = currentUserId();
        
        $request = $this->requestModel->find($id);
        
        if (!$request || $request->user_id != $userId || $request->user_type != $this->userType) {
            return redirect()->back()->with('error', 'Solicitação não encontrada');
        }
        
        $data['title'] = 'Solicitação #' . $request->request_number;
        $data['request'] = $request;
        
        return view($this->viewPath . '/view_request', $data);
    }
    
    /**
     * Cancelar solicitação
     */
    public function cancelRequest($id)
    {
        $userId = currentUserId();
        
        $request = $this->requestModel->find($id);
        
        if (!$request || $request->user_id != $userId || $request->user_type != $this->userType) {
            return redirect()->back()->with('error', 'Solicitação não encontrada');
        }
        
        if ($request->status != 'pending') {
            return redirect()->back()->with('error', 'Apenas solicitações pendentes podem ser canceladas');
        }
        
        $this->requestModel->update($id, ['status' => 'cancelled']);
        
        return redirect()->to('/' . $this->userType . 's/documents/requests')
            ->with('success', 'Solicitação cancelada com sucesso');
    }
    
    /**
     * Download de documento
     */
    public function download($id)
    {
        $userId = currentUserId();
        $document = $this->documentModel->find($id);
        
        if (!$document || $document->user_id != $userId || $document->user_type != $this->userType) {
            return redirect()->back()->with('error', 'Documento não encontrado');
        }
        
        if (!file_exists($document->document_path)) {
            return redirect()->back()->with('error', 'Arquivo não encontrado no servidor');
        }
        
        return $this->response->download($document->document_path, null);
    }
    
    /**
     * Visualizar documento (inline)
     */
    public function view($id)
    {
        $userId = currentUserId();
        $document = $this->documentModel->find($id);
        
        if (!$document || $document->user_id != $userId || $document->user_type != $this->userType) {
            return redirect()->back()->with('error', 'Documento não encontrado');
        }
        
        if (!file_exists($document->document_path)) {
            return redirect()->back()->with('error', 'Arquivo não encontrado no servidor');
        }
        
        $mime = $document->document_mime;
        $data = file_get_contents($document->document_path);
        
        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Disposition', 'inline; filename="' . $document->document_name . '"')
            ->setBody($data);
    }
    
    /**
     * Deletar documento (soft delete)
     */
    public function delete($id)
    {
        $userId = currentUserId();
        $document = $this->documentModel->find($id);
        
        if (!$document || $document->user_id != $userId || $document->user_type != $this->userType) {
            return redirect()->back()->with('error', 'Documento não encontrado');
        }
        
        // Soft delete
        $this->documentModel->delete($id);
        
        return redirect()->back()->with('success', 'Documento removido com sucesso');
    }
    
    /**
     * Método abstrato para obter tipos de documento específicos do perfil
     */
    abstract protected function getDocumentTypes();
    
    /**
     * Obter estatísticas do usuário
     */
    protected function getUserStats()
    {
        $userId = currentUserId();
        
        return [
            'total_documents' => $this->documentModel->where('user_id', $userId)
                ->where('user_type', $this->userType)
                ->countAllResults(),
            'pending_documents' => $this->documentModel->where('user_id', $userId)
                ->where('user_type', $this->userType)
                ->where('is_verified', 0)
                ->countAllResults(),
            'verified_documents' => $this->documentModel->where('user_id', $userId)
                ->where('user_type', $this->userType)
                ->where('is_verified', 1)
                ->countAllResults(),
            'pending_requests' => $this->requestModel->where('user_id', $userId)
                ->where('user_type', $this->userType)
                ->where('status', 'pending')
                ->countAllResults()
        ];
    }
}