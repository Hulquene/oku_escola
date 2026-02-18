<?php
// app/Controllers/admin/AdminDocuments.php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\DocumentModel;
use App\Models\DocumentTypeModel;
use App\Models\DocumentRequestModel;
use App\Models\RequestableDocumentModel;
use App\Models\UserModel;
use App\Models\GeneratedDocumentModel;

class AdminDocuments extends BaseController
{
    protected $documentModel;
    protected $documentTypeModel;
    protected $requestModel;
    protected $requestableModel;
    protected $userModel;
    protected $generatedModel;
    
    public function __construct()
    {
        $this->documentModel = new DocumentModel();
        $this->documentTypeModel = new DocumentTypeModel();
        $this->requestModel = new DocumentRequestModel();
        $this->requestableModel = new RequestableDocumentModel();
        $this->userModel = new UserModel();
        $this->generatedModel = new GeneratedDocumentModel();
        
        helper(['auth', 'form', 'filesystem']);
    }
    
    /**
     * Dashboard da Central de Documentos
     */
    public function index()
    {
        $data['title'] = 'Central de Documentos';
        
        // Estatísticas gerais de documentos
        $data['totalDocuments'] = $this->documentModel->countAll();
        $data['pendingDocuments'] = $this->documentModel->where('is_verified', 0)->countAllResults();
        $data['verifiedDocuments'] = $this->documentModel->where('is_verified', 1)->countAllResults();
        $data['rejectedDocuments'] = $this->documentModel->where('is_verified', 2)->countAllResults();
        
        // Estatísticas de solicitações
        $data['totalRequests'] = $this->requestModel->countAll();
        $data['pendingRequests'] = $this->requestModel->where('status', 'pending')->countAllResults();
        $data['processingRequests'] = $this->requestModel->where('status', 'processing')->countAllResults();
        $data['readyRequests'] = $this->requestModel->where('status', 'ready')->countAllResults();
        $data['deliveredRequests'] = $this->requestModel->where('status', 'delivered')->countAllResults();
        
        // Totais financeiros
        $feesStats = $this->requestModel->getStatistics();
        $data['totalFees'] = $feesStats->total_fees ?? 0;
        $data['collectedFees'] = $feesStats->collected_fees ?? 0;
        
        // Últimos documentos
        $data['recentDocuments'] = $this->documentModel
            ->orderBy('created_at', 'DESC')
            ->limit(10)
            ->findAll();
        
        // Buscar nomes dos usuários para os documentos recentes
        foreach ($data['recentDocuments'] as $doc) {
            $user = $this->userModel->find($doc->user_id);
            $doc->user_name = $user ? $user->first_name . ' ' . $user->last_name : 'N/A';
        }
        
        // Últimas solicitações
        $data['recentRequests'] = $this->requestModel
            ->orderBy('created_at', 'DESC')
            ->limit(10)
            ->findAll();
        
        // Buscar nomes dos usuários para as solicitações recentes
        foreach ($data['recentRequests'] as $req) {
            $user = $this->userModel->find($req->user_id);
            $req->user_name = $user ? $user->first_name . ' ' . $user->last_name : 'N/A';
        }
        
        // Estatísticas mensais
        $data['monthlyStats'] = $this->documentModel->getMonthlyStats();
        
        // Estatísticas por tipo de documento - CORREÇÃO ADICIONADA
        $data['typeStats'] = $this->documentModel
            ->select('document_type, COUNT(*) as total')
            ->groupBy('document_type')
            ->orderBy('total', 'DESC')
            ->findAll();
        
        return view('admin/documents/index', $data);
    }
    /**
 * Visualizar documento (inline)
 */
public function view($id)
{
    $document = $this->documentModel->getWithUser($id);
    
    if (!$document) {
        return redirect()->to('/admin/documents')->with('error', 'Documento não encontrado');
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
     * Documentos pendentes
     */
    public function pending()
    {
        $data['title'] = 'Documentos Pendentes';
        
        $data['documents'] = $this->documentModel
            ->where('is_verified', 0)
            ->orderBy('created_at', 'ASC')
            ->findAll();
        
        return view('admin/documents/pending', $data);
    }
    
    /**
     * Documentos verificados
     */
    public function verified()
    {
        $data['title'] = 'Documentos Verificados';
        
        $data['documents'] = $this->documentModel
            ->where('is_verified', 1)
            ->orderBy('verified_at', 'DESC')
            ->findAll();
        
        return view('admin/documents/verified', $data);
    }
    
    /**
     * Verificar documento
     */
    public function verify($id)
    {
        $data['title'] = 'Verificar Documento';
        $data['document'] = $this->documentModel->getWithUser($id);
        
        if (!$data['document']) {
            return redirect()->to('/admin/documents/pending')->with('error', 'Documento não encontrado');
        }
        
        return view('admin/documents/verify', $data);
    }
    
    /**
     * Salvar verificação
     */
    public function saveVerification($id)
    {
        $rules = [
            'status' => 'required|in_list[approved,rejected]',
            'notes' => 'permit_empty'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $status = $this->request->getPost('status') == 'approved' ? 1 : 2;
        
        $data = [
            'is_verified' => $status,
            'verification_notes' => $this->request->getPost('notes'),
            'verified_by' => currentUserId(),
            'verified_at' => date('Y-m-d H:i:s')
        ];
        
        if ($this->documentModel->update($id, $data)) {
            $message = $status == 1 ? 'Documento aprovado com sucesso' : 'Documento rejeitado';
            return redirect()->to('/admin/documents/pending')->with('success', $message);
        }
        
        return redirect()->back()->with('error', 'Erro ao processar verificação');
    }
    
    /**
     * Tipos de documentos (CRUD)
     */
    public function types()
    {
        $data['title'] = 'Tipos de Documentos';
        $data['types'] = $this->documentTypeModel
            ->orderBy('sort_order', 'ASC')
            ->orderBy('type_name', 'ASC')
            ->findAll();
        
        $data['categories'] = $this->documentTypeModel->getCategories();
        
        return view('admin/documents/types', $data);
    }
    
    /**
     * Salvar tipo de documento
     */
    public function saveType()
    {
        $rules = [
            'id' => 'permit_empty|numeric',
            'type_code' => 'required|max_length[50]',
            'type_name' => 'required|max_length[100]',
            'type_category' => 'required|in_list[identificacao,academico,pessoal,outro]',
            'allowed_extensions' => 'required|max_length[255]',
            'max_size' => 'permit_empty|numeric|greater_than[0]|less_than[20480]',
            'sort_order' => 'permit_empty|numeric'
        ];
        
        $id = $this->request->getPost('id');
        
        // Verificar unique apenas se for novo ou se alterou o código
        if (!$id || $this->request->getPost('type_code') != $this->request->getPost('original_code')) {
            $rules['type_code'] .= '|is_unique[tbl_document_types.type_code,id,{id}]';
        }
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'type_code' => $this->request->getPost('type_code'),
            'type_name' => $this->request->getPost('type_name'),
            'type_category' => $this->request->getPost('type_category'),
            'allowed_extensions' => $this->request->getPost('allowed_extensions'),
            'max_size' => $this->request->getPost('max_size') ?: 5120,
            'is_required' => $this->request->getPost('is_required') ? 1 : 0,
            'for_student' => $this->request->getPost('for_student') ? 1 : 0,
            'for_teacher' => $this->request->getPost('for_teacher') ? 1 : 0,
            'description' => $this->request->getPost('description'),
            'sort_order' => $this->request->getPost('sort_order') ?: 0,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];
        
        if ($id) {
            $this->documentTypeModel->update($id, $data);
            $message = 'Tipo de documento atualizado com sucesso';
        } else {
            $this->documentTypeModel->insert($data);
            $message = 'Tipo de documento criado com sucesso';
        }
        
        return redirect()->to('/admin/documents/types')->with('success', $message);
    }
    
    /**
     * Deletar tipo de documento
     */
    public function deleteType($id)
    {
        $type = $this->documentTypeModel->find($id);
        
        if (!$type) {
            return redirect()->back()->with('error', 'Tipo de documento não encontrado');
        }
        
        // Verificar se há documentos usando este tipo
        $count = $this->documentModel->where('document_type', $type->type_code)->countAllResults();
        
        if ($count > 0) {
            return redirect()->back()->with('error', 
                'Não é possível excluir: existem ' . $count . ' documentos usando este tipo');
        }
        
        $this->documentTypeModel->delete($id);
        return redirect()->to('/admin/documents/types')->with('success', 'Tipo de documento removido');
    }
    
    /**
     * Solicitações de documentos
     */
    public function requests()
    {
        $data['title'] = 'Solicitações de Documentos';
        
        $status = $this->request->getGet('status');
        $userType = $this->request->getGet('user_type');
        
        $builder = $this->requestModel->orderBy('created_at', 'DESC');
        
        if ($status) {
            $builder->where('status', $status);
        }
        
        if ($userType) {
            $builder->where('user_type', $userType);
        }
        
        $data['requests'] = $builder->findAll();
        $data['currentStatus'] = $status;
        $data['currentUserType'] = $userType;
        
        return view('admin/documents/requests', $data);
    }
    
    /**
     * Visualizar solicitação
     */
    public function viewRequest($id)
    {
        $data['title'] = 'Detalhes da Solicitação';
        $data['request'] = $this->requestModel->getWithUser($id);
        
        if (!$data['request']) {
            return redirect()->to('/admin/documents/requests')->with('error', 'Solicitação não encontrada');
        }
        
        // Documentos gerados para esta solicitação
        $data['generatedDocs'] = $this->generatedModel
            ->where('request_id', $id)
            ->findAll();
        
        return view('admin/documents/view_request', $data);
    }
    
    /**
     * Processar solicitação
     */
    public function processRequest($id)
    {
        $request = $this->requestModel->find($id);
        
        if (!$request) {
            return redirect()->back()->with('error', 'Solicitação não encontrada');
        }
        
        if ($this->requestModel->process($id, currentUserId())) {
            return redirect()->to('/admin/documents/requests')->with('success', 'Solicitação em processamento');
        }
        
        return redirect()->back()->with('error', 'Erro ao processar solicitação');
    }
    
    /**
     * Marcar como pronto
     */
    public function markAsReady($id)
    {
        $request = $this->requestModel->find($id);
        
        if (!$request) {
            return redirect()->back()->with('error', 'Solicitação não encontrada');
        }
        
        if ($this->requestModel->markAsReady($id)) {
            return redirect()->to('/admin/documents/requests')->with('success', 'Solicitação pronta para entrega');
        }
        
        return redirect()->back()->with('error', 'Erro ao atualizar solicitação');
    }
    
    /**
     * Rejeitar solicitação
     */
    public function rejectRequest($id)
    {
        $rules = ['rejection_reason' => 'required|min_length[10]'];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $request = $this->requestModel->find($id);
        
        if (!$request) {
            return redirect()->back()->with('error', 'Solicitação não encontrada');
        }
        
        if ($this->requestModel->reject($id, $this->request->getPost('rejection_reason'), currentUserId())) {
            return redirect()->to('/admin/documents/requests')->with('success', 'Solicitação rejeitada');
        }
        
        return redirect()->back()->with('error', 'Erro ao rejeitar solicitação');
    }
    
    /**
     * Registrar entrega
     */
    public function deliverRequest($id)
    {
        $request = $this->requestModel->find($id);
        
        if (!$request) {
            return redirect()->back()->with('error', 'Solicitação não encontrada');
        }
        
        if ($this->requestModel->markAsDelivered($id)) {
            return redirect()->to('/admin/documents/requests')->with('success', 'Entrega registrada com sucesso');
        }
        
        return redirect()->back()->with('error', 'Erro ao registrar entrega');
    }
    
    /**
     * Registrar pagamento
     */
    public function registerPayment($id)
    {
        $rules = [
            'payment_reference' => 'required',
            'payment_date' => 'permit_empty|valid_date'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $request = $this->requestModel->find($id);
        
        if (!$request) {
            return redirect()->back()->with('error', 'Solicitação não encontrada');
        }
        
        $paymentDate = $this->request->getPost('payment_date') ?: date('Y-m-d H:i:s');
        
        if ($this->requestModel->registerPayment($id, $this->request->getPost('payment_reference'), $paymentDate)) {
            return redirect()->to('/admin/documents/view-request/' . $id)->with('success', 'Pagamento registrado');
        }
        
        return redirect()->back()->with('error', 'Erro ao registrar pagamento');
    }
    
    /**
     * Upload de documento gerado
     */
    public function uploadGenerated($requestId)
    {
        $rules = [
            'document_file' => 'uploaded[document_file]|max_size[document_file,10240]|ext_in[document_file,pdf]'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $file = $this->request->getFile('document_file');
        
        if ($file->isValid() && !$file->hasMoved()) {
            $uploadPath = FCPATH . 'uploads/generated_documents';
            if (!is_dir($uploadPath)) {
                mkdir($uploadPath, 0777, true);
            }
            
            $newName = $file->getRandomName();
            $file->move($uploadPath, $newName);
            
            $data = [
                'request_id' => $requestId,
                'document_name' => $file->getClientName(),
                'document_path' => $uploadPath . '/' . $newName,
                'document_size' => $file->getSize(),
                'document_mime' => $file->getMimeType(),
                'generated_by' => currentUserId()
            ];
            
            if ($this->generatedModel->insert($data)) {
                return redirect()->to('/admin/documents/view-request/' . $requestId)
                    ->with('success', 'Documento gerado adicionado com sucesso');
            }
        }
        
        return redirect()->back()->with('error', 'Erro ao fazer upload do documento');
    }
    
    /**
     * Download de documento gerado
     */
    public function downloadGenerated($id)
    {
        $doc = $this->generatedModel->find($id);
        
        if (!$doc) {
            return redirect()->back()->with('error', 'Documento não encontrado');
        }
        
        // Incrementar contador de downloads
        $this->generatedModel->incrementDownloadCount($id);
        
        return $this->response->download($doc->document_path, null);
    }
    
    /**
     * Relatórios
     */
    public function reports()
    {
        $data['title'] = 'Relatórios de Documentos';
        
        $year = $this->request->getGet('year') ?: date('Y');
        $data['selectedYear'] = $year;
        
        // Estatísticas por mês
        $data['monthlyStats'] = $this->documentModel->getMonthlyStats($year);
        $data['requestTrends'] = $this->requestModel->getMonthlyTrends($year);
        
        // Estatísticas por tipo
        $data['typeStats'] = $this->documentModel
            ->select('document_type, COUNT(*) as total, 
                     SUM(CASE WHEN is_verified = 1 THEN 1 ELSE 0 END) as verified')
            ->groupBy('document_type')
            ->orderBy('total', 'DESC')
            ->findAll();
        
        // Estatísticas por usuário
        $data['userStats'] = $this->documentModel
            ->select('user_type, COUNT(*) as total, 
                     SUM(CASE WHEN is_verified = 1 THEN 1 ELSE 0 END) as verified,
                     SUM(CASE WHEN is_verified = 0 THEN 1 ELSE 0 END) as pending')
            ->groupBy('user_type')
            ->findAll();
        
        // Top solicitantes
        $data['topRequesters'] = $this->requestModel
            ->select('user_id, user_type, COUNT(*) as total, SUM(fee_amount) as total_fees')
            ->groupBy('user_id, user_type')
            ->orderBy('total', 'DESC')
            ->limit(10)
            ->findAll();
        
        return view('admin/documents/reports', $data);
    }
    
    /**
     * Exportar relatório
     */
    public function export()
    {
        $type = $this->request->getGet('type') ?: 'documents';
        $format = $this->request->getGet('format') ?: 'csv';
        $year = $this->request->getGet('year') ?: date('Y');
        
        // Lógica de exportação baseada no tipo
        switch ($type) {
            case 'documents':
                $data = $this->documentModel
                    ->where('YEAR(created_at)', $year)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
                $filename = "documentos_{$year}." . ($format == 'csv' ? 'csv' : 'xls');
                break;
                
            case 'requests':
                $data = $this->requestModel
                    ->where('YEAR(created_at)', $year)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
                $filename = "solicitacoes_{$year}." . ($format == 'csv' ? 'csv' : 'xls');
                break;
                
            case 'financial':
                $data = $this->requestModel
                    ->select('
                        request_number, created_at, user_type, document_type, 
                        fee_amount, payment_status, payment_reference, payment_date
                    ')
                    ->where('YEAR(created_at)', $year)
                    ->where('fee_amount >', 0)
                    ->orderBy('created_at', 'DESC')
                    ->findAll();
                $filename = "financeiro_documentos_{$year}." . ($format == 'csv' ? 'csv' : 'xls');
                break;
                
            default:
                return redirect()->back()->with('error', 'Tipo de relatório inválido');
        }
        
        // Gerar arquivo CSV
        if ($format == 'csv') {
            return $this->generateCSV($data, $filename);
        }
        
        // Gerar arquivo Excel (simples)
        return $this->generateExcel($data, $filename);
    }
    
    /**
     * Gerar arquivo CSV
     */
    private function generateCSV($data, $filename)
    {
        $csv = '';
        
        if (!empty($data)) {
            // Cabeçalho
            $headers = array_keys((array)$data[0]);
            $csv .= implode(';', $headers) . "\n";
            
            // Dados
            foreach ($data as $row) {
                $row = (array)$row;
                $csv .= implode(';', $row) . "\n";
            }
        }
        
        return $this->response
            ->setHeader('Content-Type', 'text/csv; charset=utf-8')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($csv);
    }
    
    /**
     * Gerar arquivo Excel (HTML table)
     */
    private function generateExcel($data, $filename)
    {
        $html = '<table border="1">';
        
        if (!empty($data)) {
            // Cabeçalho
            $html .= '<tr>';
            foreach (array_keys((array)$data[0]) as $header) {
                $html .= '<th>' . $header . '</th>';
            }
            $html .= '</tr>';
            
            // Dados
            foreach ($data as $row) {
                $html .= '<tr>';
                foreach ((array)$row as $value) {
                    $html .= '<td>' . $value . '</td>';
                }
                $html .= '</tr>';
            }
        }
        
        $html .= '</table>';
        
        return $this->response
            ->setHeader('Content-Type', 'application/vnd.ms-excel')
            ->setHeader('Content-Disposition', 'attachment; filename="' . $filename . '"')
            ->setBody($html);
    }

    /**
     * Servir arquivo de documento para visualização
     * 
     * @param int $id ID do documento
     * @return ResponseInterface
     */
    public function serve($id)
    {
        $document = $this->documentModel->find($id);
        
        if (!$document) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Documento não encontrado');
        }
        
        $path = $document->document_path;
        
        // Verificar se o arquivo existe
        if (!file_exists($path)) {
            // Tentar caminhos alternativos
            $alternativePath = FCPATH . $path;
            if (file_exists($alternativePath)) {
                $path = $alternativePath;
            } else {
                throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Arquivo não encontrado');
            }
        }
        
        // Determinar o tipo MIME
        $mime = $document->document_mime;
        
        // Se não tiver MIME, tentar detectar
        if (empty($mime)) {
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($finfo, $path);
            finfo_close($finfo);
        }
        
        // Configurar headers para visualização inline
        return $this->response
            ->setHeader('Content-Type', $mime)
            ->setHeader('Content-Disposition', 'inline; filename="' . $document->document_name . '"')
            ->setHeader('Cache-Control', 'public, max-age=86400') // Cache por 1 dia
            ->setBody(file_get_contents($path));
    }
    
    /**
     * Download do documento
     */
    public function download($id)
    {
        $document = $this->documentModel->find($id);
        
        if (!$document) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Documento não encontrado');
        }
        
        $path = $document->document_path;
        
        if (!file_exists($path)) {
            throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound('Arquivo não encontrado');
        }
        
        return $this->response->download($path, null);
    }

     /**
     * Lista de tipos de documentos solicitáveis
     */
    public function requestableTypes()
    {
        $data['title'] = 'Documentos Solicitáveis';
        $data['types'] = $this->requestableModel->getActive();
        $data['categories'] = $this->requestableModel->getCategories();
        $data['availableFor'] = $this->requestableModel->getAvailableForOptions();
        
        return view('admin/documents/requestable_types', $data);
    }
    
    /**
     * Salvar tipo de documento solicitável
     */
    public function saveRequestableType()
    {
        $rules = [
            'id' => 'permit_empty|numeric',
            'document_code' => 'required|max_length[50]',
            'document_name' => 'required|max_length[255]',
            'category' => 'required|in_list[certificado,declaracao,atestado,historico,outro]',
            'fee_amount' => 'permit_empty|numeric|greater_than_equal_to[0]',
            'processing_days' => 'permit_empty|numeric|greater_than[0]|less_than[31]',
            'available_for' => 'required|in_list[all,students,teachers,guardians,staff]'
        ];
        
        $id = $this->request->getPost('id');
        
        // Verificar unique apenas se for novo ou se alterou o código
        if (!$id || $this->request->getPost('document_code') != $this->request->getPost('original_code')) {
            $rules['document_code'] .= '|is_unique[tbl_requestable_documents.document_code,id,{id}]';
        }
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $data = [
            'document_code' => $this->request->getPost('document_code'),
            'document_name' => $this->request->getPost('document_name'),
            'description' => $this->request->getPost('description'),
            'category' => $this->request->getPost('category'),
            'fee_amount' => $this->request->getPost('fee_amount') ?: 0,
            'processing_days' => $this->request->getPost('processing_days') ?: 3,
            'available_for' => $this->request->getPost('available_for'),
            'requires_approval' => $this->request->getPost('requires_approval') ? 1 : 0,
            'is_active' => $this->request->getPost('is_active') ? 1 : 0
        ];
        
        if ($id) {
            $this->requestableModel->update($id, $data);
            $message = 'Tipo de documento solicitável atualizado com sucesso';
        } else {
            $this->requestableModel->insert($data);
            $message = 'Tipo de documento solicitável criado com sucesso';
        }
        
        return redirect()->to('/admin/documents/requestable')->with('success', $message);
    }
    
    /**
     * Deletar tipo de documento solicitável
     */
    public function deleteRequestableType($id)
    {
        $type = $this->requestableModel->find($id);
        
        if (!$type) {
            return redirect()->back()->with('error', 'Tipo de documento não encontrado');
        }
        
        // Verificar se há solicitações usando este tipo
        $count = $this->requestModel->where('document_code', $type->document_code)->countAllResults();
        
        if ($count > 0) {
            return redirect()->back()->with('error', 
                'Não é possível excluir: existem ' . $count . ' solicitações usando este tipo');
        }
        
        $this->requestableModel->delete($id);
        return redirect()->to('/admin/documents/requestable')->with('success', 'Tipo de documento removido');
    }
    
}