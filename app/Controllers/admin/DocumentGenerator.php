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
use App\Models\SemesterResultModel;  // ✅ CORRIGIDO: usar SemesterResultModel
use App\Models\UserModel;
use App\Models\DisciplineAverageModel; // Adicionar para buscar médias por disciplina

class DocumentGenerator extends BaseController
{
    protected $requestModel;
    protected $generatedModel;
    protected $requestableModel;
    protected $studentModel;
    protected $teacherModel;
    protected $enrollmentModel;
    protected $semesterResultModel;  // ✅ CORRIGIDO
    protected $userModel;
    protected $disciplineAvgModel;
    
    public function __construct()
    {
        $this->requestModel = new DocumentRequestModel();
        $this->generatedModel = new GeneratedDocumentModel();
        $this->requestableModel = new RequestableDocumentModel();
        $this->studentModel = new StudentModel();
        $this->teacherModel = new TeacherModel();
        $this->enrollmentModel = new EnrollmentModel();
        $this->semesterResultModel = new SemesterResultModel();  // ✅ CORRIGIDO
        $this->userModel = new UserModel();
        $this->disciplineAvgModel = new DisciplineAverageModel();
        
        helper(['auth', 'form', 'filesystem']);
    }
    
    /**
     * Dashboard do Gerador de Documentos
     */
    public function index()
    {
        // Verificar permissão
        if (!$this->hasPermission('document_requests.list')) {
            return redirect()->to('/admin/dashboard')->with('error', 'Não tem permissão para aceder a esta página');
        }
        
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
        // Verificar permissão
        if (!$this->hasPermission('document_requests.list')) {
            return redirect()->to('/admin/dashboard')->with('error', 'Não tem permissão para aceder a esta página');
        }
        
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
        // Verificar permissão
        if (!$this->hasPermission('document_requests.view')) {
            return redirect()->to('/admin/dashboard')->with('error', 'Não tem permissão para aceder a esta página');
        }
        
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
        // Verificar permissão
        if (!$this->hasPermission('documents.verify')) {
            return redirect()->to('/admin/dashboard')->with('error', 'Não tem permissão para aceder a esta página');
        }
        
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
        // Verificar permissão
        if (!$this->hasPermission('document_requests.process')) {
            return redirect()->back()->with('error', 'Não tem permissão para processar solicitações');
        }
        
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
            'generated_by' => session()->get('user_id'),
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
        // Verificar permissão
        if (!$this->hasPermission('document_requests.process')) {
            return redirect()->back()->with('error', 'Não tem permissão para processar solicitações');
        }
        
        $ids = $this->request->getPost('ids');
        
        if (empty($ids)) {
            return redirect()->back()->with('error', 'Nenhuma solicitação selecionada');
        }
        
        $success = 0;
        $errors = 0;
        
        foreach ($ids as $id) {
            try {
                $this->generate($id);
                $success++;
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
        // Verificar permissão
        if (!$this->hasPermission('document_requests.view')) {
            return redirect()->back()->with('error', 'Não tem permissão para aceder a esta página');
        }
        
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
        
        // Verificar permissão (dono do documento ou admin)
        $userId = session()->get('user_id');
        if ($doc->user_id != $userId && !$this->hasPermission('document_requests.view')) {
            return redirect()->back()->with('error', 'Não tem permissão para baixar este documento');
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
        // Verificar permissão
        if (!$this->hasPermission('documents.verify')) {
            return redirect()->back()->with('error', 'Não tem permissão para editar modelos');
        }
        
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
        
        if (!$student) {
            log_message('error', 'Aluno não encontrado para o user_id: ' . $request->user_id);
            return false;
        }
        
        $enrollment = $this->enrollmentModel->getCurrentForStudent($student->id);
        
        if (!$enrollment) {
            log_message('error', 'Matrícula atual não encontrada para o student_id: ' . $student->id);
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
        
        if (!$student) {
            log_message('error', 'Aluno não encontrado para o user_id: ' . $request->user_id);
            return false;
        }
        
        $enrollment = $this->enrollmentModel->getCurrentForStudent($student->id);
        
        if (!$enrollment) {
            log_message('error', 'Matrícula atual não encontrada para o student_id: ' . $student->id);
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
     * Gerar Histórico de Notas - VERSÃO CORRIGIDA usando SemesterResultModel
     */
    private function generateHistoricoNotas($request)
    {
        $student = $this->studentModel->getWithUser($request->user_id);
        
        if (!$student) {
            log_message('error', 'Aluno não encontrado para o user_id: ' . $request->user_id);
            return false;
        }
        
        // Buscar histórico acadêmico completo usando SemesterResultModel
        $historico = $this->semesterResultModel->getStudentHistory($student->id);
        
        // Para cada período, buscar também as médias por disciplina
        foreach ($historico as $periodo) {
            $disciplinas = $this->disciplineAvgModel
                ->select('
                    tbl_discipline_averages.*,
                    tbl_disciplines.discipline_name,
                    tbl_disciplines.discipline_code
                ')
                ->join('tbl_disciplines', 'tbl_disciplines.id = tbl_discipline_averages.discipline_id')
                ->where('tbl_discipline_averages.enrollment_id', $periodo->enrollment_id)
                ->where('tbl_discipline_averages.semester_id', $periodo->semester_id)
                ->findAll();
            
            $periodo->disciplinas = $disciplinas;
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
     * Gerar Certificado de Conclusão
     */
    private function generateCertificadoConclusao($request)
    {
        $student = $this->studentModel->getWithUser($request->user_id);
        
        if (!$student) {
            return false;
        }
        
        // Buscar histórico para verificar conclusão
        $historico = $this->semesterResultModel->getStudentHistory($student->id);
        
        // Verificar se tem resultados concluídos
        $concluded = false;
        $lastYear = null;
        
        foreach ($historico as $periodo) {
            if ($periodo->status != 'Em Andamento') {
                $concluded = true;
                $lastYear = $periodo->year_name;
            }
        }
        
        $data = [
            'titulo' => 'CERTIFICADO DE CONCLUSÃO',
            'subtitulo' => 'CERTIFICADO DE CONCLUSÃO',
            'numero' => $request->request_number,
            'data' => date('d/m/Y'),
            'aluno' => $student->full_name ?? $student->first_name . ' ' . $student->last_name,
            'nome_aluno' => $student->full_name ?? $student->first_name . ' ' . $student->last_name,
            'bi' => $student->identity_document ?? 'N/A',
            'data_nascimento' => $student->birth_date ? date('d/m/Y', strtotime($student->birth_date)) : 'N/A',
            'ano_conclusao' => $lastYear ?? 'N/A',
            'concluido' => $concluded
        ];
        
        return $this->generatePDF('certificado_conclusao', $data, $this->generateFileName($request));
    }
    
    /**
     * Gerar Declaração de Aproveitamento
     */
    private function generateDeclaracaoAproveitamento($request)
    {
        $student = $this->studentModel->getWithUser($request->user_id);
        
        if (!$student) {
            return false;
        }
        
        $enrollment = $this->enrollmentModel->getCurrentForStudent($student->id);
        
        if (!$enrollment) {
            return false;
        }
        
        // Buscar resultados do semestre atual
        $currentSemester = $this->semesterResultModel
            ->select('tbl_semester_results.*, tbl_semesters.semester_name')
            ->join('tbl_semesters', 'tbl_semesters.id = tbl_semester_results.semester_id')
            ->where('tbl_semester_results.enrollment_id', $enrollment->id)
            ->where('tbl_semesters.is_current', 1)
            ->first();
        
        $data = [
            'titulo' => 'DECLARAÇÃO DE APROVEITAMENTO',
            'subtitulo' => 'DECLARAÇÃO DE APROVEITAMENTO',
            'numero' => $request->request_number,
            'data' => date('d/m/Y'),
            'aluno' => $student->full_name ?? $student->first_name . ' ' . $student->last_name,
            'classe' => $enrollment->class_name,
            'ano_letivo' => $enrollment->year_name,
            'periodo' => $currentSemester->semester_name ?? 'Período Atual',
            'media' => $currentSemester->overall_average ?? 'N/A',
            'total_disciplinas' => $currentSemester->total_disciplines ?? 'N/A',
            'aprovadas' => $currentSemester->approved_disciplines ?? 'N/A',
            'reprovadas' => $currentSemester->failed_disciplines ?? 'N/A',
            'status' => $currentSemester->status ?? 'Em Andamento'
        ];
        
        return $this->generatePDF('declaracao_aproveitamento', $data, $this->generateFileName($request));
    }
    
    /**
     * Gerar Atestado de Matrícula
     */
    private function generateAtestadoMatricula($request)
    {
        // Similar ao certificado, mas mais simples
        return $this->generateCertificadoMatricula($request);
    }
    
    /**
     * Gerar Declaração de Serviço (para professores)
     */
    private function generateDeclaracaoServico($request)
    {
        $teacher = $this->teacherModel->getWithUser($request->user_id);
        
        if (!$teacher) {
            return false;
        }
        
        $data = [
            'titulo' => 'DECLARAÇÃO DE SERVIÇO',
            'subtitulo' => 'DECLARAÇÃO DE SERVIÇO',
            'numero' => $request->request_number,
            'data' => date('d/m/Y'),
            'professor' => $teacher->full_name ?? $teacher->first_name . ' ' . $teacher->last_name,
            'bi' => $teacher->identity_document ?? 'N/A',
            'data_admissao' => date('d/m/Y', strtotime($teacher->admission_date)),
            'qualificacoes' => $teacher->qualifications ?? 'N/A',
            'finalidade' => $request->purpose
        ];
        
        return $this->generatePDF('declaracao_servico', $data, $this->generateFileName($request));
    }
    
    /**
     * Gerar Certificado de Trabalho (para professores)
     */
    private function generateCertificadoTrabalho($request)
    {
        return $this->generateDeclaracaoServico($request);
    }
    
    /**
     * Gerar Declaração de Vencimento (para professores)
     */
    private function generateDeclaracaoVencimento($request)
    {
        $teacher = $this->teacherModel->getWithUser($request->user_id);
        
        if (!$teacher) {
            return false;
        }
        
        $data = [
            'titulo' => 'DECLARAÇÃO DE VENCIMENTO',
            'subtitulo' => 'DECLARAÇÃO DE VENCIMENTO',
            'numero' => $request->request_number,
            'data' => date('d/m/Y'),
            'professor' => $teacher->full_name ?? $teacher->first_name . ' ' . $teacher->last_name,
            'bi' => $teacher->identity_document ?? 'N/A',
            'cargo' => 'Professor',
            'finalidade' => $request->purpose
        ];
        
        return $this->generatePDF('declaracao_vencimento', $data, $this->generateFileName($request));
    }
    
    /**
     * Gerar PDF usando biblioteca DOMPDF
     */
    private function generatePDF($view, $data, $filename)
    {
        // Verificar se a pasta existe
        $pdfDir = FCPATH . 'uploads/generated_documents/';
        if (!is_dir($pdfDir)) {
            mkdir($pdfDir, 0777, true);
        }
        
        // Carregar a view do documento
        $html = view('admin/document-generator/templates/' . $view, $data);
        
        // Usar DOMPDF para gerar PDF
        $dompdf = new \Dompdf\Dompdf();
        $dompdf->loadHtml($html);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        
        // Salvar PDF
        $pdfPath = $pdfDir . $filename;
        file_put_contents($pdfPath, $dompdf->output());
        
        return $pdfPath;
    }
    /**
 * Preview template by code
 */
public function previewTemplate($code)
{
    // Verificar permissão
    if (!$this->hasPermission('documents.verify')) {
        return redirect()->to('/admin/dashboard')->with('error', 'Não tem permissão para aceder a esta página');
    }
    
    $template = $this->requestableModel->where('document_code', $code)->first();
    
    if (!$template) {
        return redirect()->back()->with('error', 'Modelo não encontrado');
    }
    
    // Dados de exemplo para pré-visualização
    $data = [
        'titulo' => 'PRÉ-VISUALIZAÇÃO',
        'subtitulo' => 'PRÉ-VISUALIZAÇÃO DO MODELO',
        'numero' => 'REQ' . date('Ymd') . '0001',
        'data' => date('d/m/Y'),
        'aluno' => 'João Manuel Silva',
        'nome_aluno' => 'João Manuel Silva',
        'bi' => '006789234LA045',
        'nif' => '541728394',
        'classe' => '10ª Classe',
        'ano_letivo' => '2025',
        'turno' => 'Manhã',
        'data_matricula' => '10/02/2025',
        'numero_matricula' => 'MAT20250001',
        'data_nascimento' => '15/05/2010',
        'naturalidade' => 'Luanda',
        'morada' => 'Rua da Paz, 123 - Luanda',
        'professor' => 'António Ferreira',
        'data_admissao' => '05/03/2020',
        'qualificacoes' => 'Licenciatura em Matemática',
        'finalidade' => 'Comprovativo de residência',
        'escola' => session()->get('school_name') ?? 'Escola Nacional',
        'logotipo' => base_url('assets/img/logo.png')
    ];
    
    // Mapear código do template para a view
    $viewMap = [
        'CERT_MATRICULA' => 'certificado_matricula',
        'DECL_FREQUENCIA' => 'declaracao_frequencia',
        'HISTORICO_NOTAS' => 'historico_notas',
        'CERT_CONCLUSAO' => 'certificado_conclusao',
        'DECL_APROVEITAMENTO' => 'declaracao_aproveitamento',
        'ATESTADO_MATRICULA' => 'atestado_matricula',
        'DECL_SERVICO' => 'declaracao_servico',
        'CERT_TRABALHO' => 'certificado_trabalho',
        'DECL_VENCIMENTO' => 'declaracao_vencimento'
    ];
    
    $viewName = $viewMap[$code] ?? 'certificado_matricula';
    
    // Para pré-visualização, podemos mostrar HTML em vez de PDF
    return view('admin/document-generator/templates/' . $viewName, $data);
}
}