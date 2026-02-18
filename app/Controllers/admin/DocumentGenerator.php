<?php
// app/Controllers/admin/DocumentGenerator.php

namespace App\Controllers\admin;

use App\Controllers\BaseController;
use App\Models\DocumentRequestModel;
use App\Models\GeneratedDocumentModel;
use App\Models\RequestableDocumentModel;
use App\Models\StudentModel;
use App\Models\TeacherModel;
use App\Models\EnrollmentModel;
use App\Models\FinalGradeModel;
use App\Models\UserModel;

class DocumentGenerator extends BaseController
{
    protected $requestModel;
    protected $generatedModel;
    protected $requestableModel;
    protected $studentModel;
    protected $teacherModel;
    protected $enrollmentModel;
    protected $finalGradeModel;
    protected $userModel;
    
    public function __construct()
    {
        $this->requestModel = new DocumentRequestModel();
        $this->generatedModel = new GeneratedDocumentModel();
        $this->requestableModel = new RequestableDocumentModel();
        $this->studentModel = new StudentModel();
        $this->teacherModel = new TeacherModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->finalGradeModel = new FinalGradeModel();
        $this->userModel = new UserModel();
        
        helper(['auth', 'form', 'filesystem']);
    }
    
    /**
     * Dashboard do Gerador de Documentos
     */
    public function index()
    {
        $data['title'] = 'Gerador de Documentos';
        
        // Estatísticas
        $data['totalPending'] = $this->requestModel->where('status', 'pending')->countAllResults();
        $data['totalProcessing'] = $this->requestModel->where('status', 'processing')->countAllResults();
        $data['totalGenerated'] = $this->requestModel->where('status', 'ready')->countAllResults();
        $data['totalDelivered'] = $this->requestModel->where('status', 'delivered')->countAllResults();
        
        // Documentos por tipo
        $data['documentsByType'] = $this->requestModel
            ->select('document_type, COUNT(*) as total')
            ->groupBy('document_type')
            ->orderBy('total', 'DESC')
            ->findAll();
        
        // Últimas solicitações
        $data['recentRequests'] = $this->requestModel
            ->orderBy('created_at', 'DESC')
            ->limit(10)
            ->findAll();
        
        // Estatísticas mensais
        $data['monthlyStats'] = $this->requestModel->getMonthlyTrends();
        
        return view('admin/document-generator/index', $data);
    }
    
    /**
     * Lista de solicitações pendentes
     */
    public function pending()
    {
        $data['title'] = 'Solicitações Pendentes';
        
        $data['requests'] = $this->requestModel
            ->where('status', 'pending')
            ->orderBy('created_at', 'ASC')
            ->findAll();
        
        // Buscar nomes dos usuários
        foreach ($data['requests'] as $req) {
            $user = $this->userModel->find($req->user_id);
            $req->user_name = $user ? $user->first_name . ' ' . $user->last_name : 'N/A';
        }
        
        return view('admin/document-generator/pending', $data);
    }
    
    /**
     * Lista de documentos gerados
     */
    public function generated()
    {
        $data['title'] = 'Documentos Gerados';
        
        $data['documents'] = $this->generatedModel
            ->select('
                tbl_generated_documents.*,
                tbl_document_requests.request_number,
                tbl_document_requests.document_type,
                tbl_document_requests.user_id,
                tbl_document_requests.user_type,
                tbl_users.first_name,
                tbl_users.last_name,
                CONCAT(tbl_users.first_name, " ", tbl_users.last_name) as user_name
            ')
            ->join('tbl_document_requests', 'tbl_document_requests.id = tbl_generated_documents.request_id')
            ->join('tbl_users', 'tbl_users.id = tbl_document_requests.user_id')
            ->orderBy('tbl_generated_documents.generated_at', 'DESC')
            ->findAll();
        
        return view('admin/document-generator/generated', $data);
    }
    
    /**
     * Gerenciar modelos de documentos
     */
    public function templates()
    {
        $data['title'] = 'Modelos de Documentos';
        
        $data['templates'] = $this->requestableModel
            ->where('is_active', 1)
            ->orderBy('category', 'ASC')
            ->orderBy('document_name', 'ASC')
            ->findAll();
        
        $data['categories'] = $this->requestableModel->getCategories();
        
        return view('admin/document-generator/templates', $data);
    }
    
    /**
     * Gerar documento
     */
    public function generate($requestId)
    {
        $request = $this->requestModel->getWithUser($requestId);
        
        if (!$request) {
            return redirect()->back()->with('error', 'Solicitação não encontrada');
        }
        
        // Verificar se já foi gerado
        $existing = $this->generatedModel->where('request_id', $requestId)->first();
        if ($existing) {
            return redirect()->to('/admin/document-generator/download/' . $existing->id)
                ->with('info', 'Documento já foi gerado anteriormente');
        }
        
        // Mapear código do documento para método de geração
        $generators = [
            'CERT_MATRICULA' => 'generateCertificadoMatricula',
            'DECL_FREQUENCIA' => 'generateDeclaracaoFrequencia',
            'HISTORICO_NOTAS' => 'generateHistoricoNotas',
            'CERT_CONCLUSAO' => 'generateCertificadoConclusao',
            'DECL_APROVEITAMENTO' => 'generateDeclaracaoAproveitamento',
            'ATESTADO_MATRICULA' => 'generateAtestadoMatricula',
            'DECL_SERVICO' => 'generateDeclaracaoServico',
            'CERT_TRABALHO' => 'generateCertificadoTrabalho',
            'DECL_VENCIMENTO' => 'generateDeclaracaoVencimento'
        ];
        
        $docCode = $request->document_code;
        
        if (!isset($generators[$docCode])) {
            return redirect()->back()->with('error', 'Tipo de documento não suportado');
        }
        
        $method = $generators[$docCode];
        
        // Marcar como processando
        $this->requestModel->update($requestId, ['status' => 'processing']);
        
        // Chamar o método específico para gerar o documento
        $pdfPath = $this->$method($request);
        
        if (!$pdfPath) {
            $this->requestModel->update($requestId, ['status' => 'pending']);
            return redirect()->back()->with('error', 'Erro ao gerar documento. Verifique os dados do solicitante.');
        }
        
        // Salvar documento gerado
        $data = [
            'request_id' => $requestId,
            'document_path' => $pdfPath,
            'document_name' => $this->generateFileName($request),
            'document_size' => filesize($pdfPath),
            'document_mime' => 'application/pdf',
            'generated_by' => currentUserId(),
            'download_count' => 0
        ];
        
        if ($this->generatedModel->insert($data)) {
            // Atualizar status da solicitação para 'ready'
            $this->requestModel->update($requestId, [
                'status' => 'ready',
                'ready_at' => date('Y-m-d H:i:s')
            ]);
            
            return redirect()->to('/admin/document-generator/download/' . $this->generatedModel->getInsertID())
                ->with('success', 'Documento gerado com sucesso!');
        }
        
        return redirect()->back()->with('error', 'Erro ao salvar documento gerado');
    }
    
    /**
     * Geração em lote
     */
    public function generateBulk()
    {
        $ids = $this->request->getPost('ids');
        
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Nenhuma solicitação selecionada');
        }
        
        $success = 0;
        $errors = 0;
        
        foreach ($ids as $id) {
            try {
                $result = $this->generate($id);
                if ($result) $success++;
            } catch (\Exception $e) {
                $errors++;
                log_message('error', 'Erro ao gerar documento em lote: ' . $e->getMessage());
            }
        }
        
        $message = "Documentos gerados: {$success} com sucesso, {$errors} com erro.";
        return redirect()->to('/admin/document-generator/pending')->with('info', $message);
    }
    
    /**
     * Pré-visualizar documento
     */
    public function preview($requestId)
    {
        $request = $this->requestModel->getWithUser($requestId);
        
        if (!$request) {
            return redirect()->back()->with('error', 'Solicitação não encontrada');
        }
        
        // Gerar HTML para pré-visualização
        $data = [
            'request' => $request,
            'preview' => true
        ];
        
        return view('admin/document-generator/preview', $data);
    }
    
    /**
     * Download de documento gerado
     */
    public function download($id)
    {
        $doc = $this->generatedModel->getWithGenerator($id);
        
        if (!$doc) {
            return redirect()->back()->with('error', 'Documento não encontrado');
        }
        
        // Incrementar contador de downloads
        $this->generatedModel->incrementDownloadCount($id);
        
        return $this->response->download($doc->document_path, null);
    }
    
    /**
     * Salvar modelo personalizado
     */
    public function saveTemplate()
    {
        $rules = [
            'template_id' => 'required|numeric',
            'template_content' => 'required'
        ];
        
        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }
        
        $templateId = $this->request->getPost('template_id');
        $content = $this->request->getPost('template_content');
        
        // Salvar template personalizado (implementar conforme necessidade)
        
        return redirect()->to('/admin/document-generator/templates')
            ->with('success', 'Template atualizado com sucesso');
    }
    
    /**
     * Gerar nome do arquivo
     */
    private function generateFileName($request)
    {
        $userName = preg_replace('/[^a-zA-Z0-9]/', '_', $request->user_fullname);
        $date = date('Ymd_His');
        $code = $request->document_code;
        
        return "{$code}_{$userName}_{$date}.pdf";
    }
    
    /**
     * Gerar Certificado de Matrícula
     */
    private function generateCertificadoMatricula($request)
    {
        $student = $this->studentModel->getWithUser($request->user_id);
        $enrollment = $this->enrollmentModel->getCurrentForStudent($student->id);
        
        if (!$enrollment) {
            return false;
        }
        
        // Dados para o PDF
        $data = [
            'titulo' => 'CERTIFICADO DE MATRÍCULA',
            'subtitulo' => 'CERTIFICADO DE MATRÍCULA',
            'numero' => $request->request_number,
            'data' => date('d/m/Y'),
            'aluno' => $student->full_name ?? $student->first_name . ' ' . $student->last_name,
            'nome_aluno' => $student->full_name ?? $student->first_name . ' ' . $student->last_name,
            'bi' => $student->identity_document ?? 'N/A',
            'nif' => $student->nif ?? 'N/A',
            'classe' => $enrollment->class_name,
            'ano_letivo' => $enrollment->year_name,
            'turno' => $enrollment->class_shift,
            'data_matricula' => date('d/m/Y', strtotime($enrollment->enrollment_date)),
            'numero_matricula' => $enrollment->enrollment_number,
            'data_nascimento' => $student->birth_date ? date('d/m/Y', strtotime($student->birth_date)) : 'N/A',
            'naturalidade' => $student->birth_place ?? 'N/A',
            'filiacao' => 'A confirmar', // Buscar dos encarregados
            'morada' => $student->address ?? 'N/A',
            'quantidade' => $request->quantity,
            'formato' => $request->format
        ];
        
        return $this->generatePDF('certificado_matricula', $data, $this->generateFileName($request));
    }
    
    /**
     * Gerar Declaração de Frequência
     */
    private function generateDeclaracaoFrequencia($request)
    {
        $student = $this->studentModel->getWithUser($request->user_id);
        $enrollment = $this->enrollmentModel->getCurrentForStudent($student->id);
        
        if (!$enrollment) {
            return false;
        }
        
        $data = [
            'titulo' => 'DECLARAÇÃO DE FREQUÊNCIA',
            'subtitulo' => 'DECLARAÇÃO DE FREQUÊNCIA',
            'numero' => $request->request_number,
            'data' => date('d/m/Y'),
            'aluno' => $student->full_name ?? $student->first_name . ' ' . $student->last_name,
            'nome_aluno' => $student->full_name ?? $student->first_name . ' ' . $student->last_name,
            'bi' => $student->identity_document ?? 'N/A',
            'classe' => $enrollment->class_name,
            'ano_letivo' => $enrollment->year_name,
            'turno' => $enrollment->class_shift,
            'finalidade' => $request->purpose,
            'data_nascimento' => $student->birth_date ? date('d/m/Y', strtotime($student->birth_date)) : 'N/A'
        ];
        
        return $this->generatePDF('declaracao_frequencia', $data, $this->generateFileName($request));
    }
    
    /**
     * Gerar Histórico de Notas
     */
    private function generateHistoricoNotas($request)
    {
        $student = $this->studentModel->getWithUser($request->user_id);
        
        // Buscar todas as matrículas do aluno
        $enrollments = $this->enrollmentModel->getStudentHistory($student->id);
        
        $historico = [];
        foreach ($enrollments as $enrollment) {
            $grades = $this->finalGradeModel
                ->select('
                    tbl_final_grades.*,
                    tbl_disciplines.discipline_name,
                    tbl_semesters.semester_name
                ')
                ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_final_grades.discipline_id')
                ->join('tbl_semesters', 'tbl_semesters.id = tbl_final_grades.semester_id')
                ->where('tbl_final_grades.enrollment_id', $enrollment->id)
                ->findAll();
            
            $historico[] = [
                'ano_letivo' => $enrollment->year_name,
                'classe' => $enrollment->class_name,
                'disciplinas' => $grades
            ];
        }
        
        $data = [
            'titulo' => 'HISTÓRICO DE NOTAS',
            'subtitulo' => 'HISTÓRICO DE NOTAS',
            'numero' => $request->request_number,
            'data' => date('d/m/Y'),
            'aluno' => $student->full_name ?? $student->first_name . ' ' . $student->last_name,
            'nome_aluno' => $student->full_name ?? $student->first_name . ' ' . $student->last_name,
            'bi' => $student->identity_document ?? 'N/A',
            'data_nascimento' => $student->birth_date ? date('d/m/Y', strtotime($student->birth_date)) : 'N/A',
            'historico' => $historico
        ];
        
        return $this->generatePDF('historico_notas', $data, $this->generateFileName($request));
    }
    
    /**
     * Gerar PDF usando biblioteca DOMPDF
     */
    private function generatePDF($view, $data, $filename)
    {
        // Carregar a view do documento
        $html = view('admin/document-generator/templates/' . $view, $data);
        
        // Usar DOMPDF para gerar PDF
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        // Salvar PDF
        $pdfPath = FCPATH . 'uploads/generated_documents/' . $filename;
        
        // Criar diretório se não existir
        $dir = dirname($pdfPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        
        file_put_contents($pdfPath, $dompdf->output());
        
        return $pdfPath;
    }
}