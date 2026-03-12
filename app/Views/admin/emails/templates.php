<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<style>
:root {
    --primary: #1B2B4B;
    --primary-light: #243761;
    --accent: #3B7FE8;
    --accent-hover: #2C6FD4;
    --success: #16A87D;
    --danger: #E84646;
    --warning: #E8A020;
    --surface: #F5F7FC;
    --surface-card: #FFFFFF;
    --border: #E2E8F4;
    --text-primary: #1A2238;
    --text-secondary: #6B7A99;
    --text-muted: #9AA5BE;
}

.page-header {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
    border-radius: 12px;
    padding: 1.5rem 2rem;
    margin-bottom: 1.5rem;
    color: white;
}

.page-header h1 {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.page-header .breadcrumb {
    background: transparent;
    padding: 0;
    margin: 0;
}

.page-header .breadcrumb-item a {
    color: rgba(255,255,255,0.7);
    text-decoration: none;
}

.page-header .breadcrumb-item.active {
    color: white;
}

.template-card {
    background: var(--surface-card);
    border: 1px solid var(--border);
    border-radius: 12px;
    overflow: hidden;
    transition: all 0.3s ease;
    height: 100%;
}

.template-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(27,43,75,.10);
    border-color: var(--accent);
}

.template-header {
    background: var(--surface);
    padding: 1rem 1.25rem;
    border-bottom: 1px solid var(--border);
}

.template-header h5 {
    color: var(--text-primary);
    font-weight: 600;
    margin: 0;
}

.template-body {
    padding: 1.25rem;
}

.variable-badge {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 0.25rem 0.75rem;
    font-size: 0.75rem;
    color: var(--text-secondary);
    display: inline-block;
    margin: 0.25rem;
}

.btn-template {
    flex: 1;
    margin: 0 0.25rem;
    border-radius: 8px;
    padding: 0.5rem;
    font-size: 0.875rem;
}

.btn-preview {
    background: rgba(59,127,232,.1);
    color: var(--accent);
    border: 1px solid var(--accent);
}

.btn-preview:hover {
    background: var(--accent);
    color: white;
}

.btn-use {
    background: var(--accent);
    color: white;
    border: 1px solid var(--accent);
}

.btn-use:hover {
    background: var(--accent-hover);
}
</style>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="fas fa-file-alt me-2"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/emails') ?>">Emails</a></li>
                    <li class="breadcrumb-item active">Templates</li>
                </ol>
            </nav>
        </div>
        <a href="<?= site_url('admin/emails/compose') ?>" class="btn btn-light">
            <i class="fas fa-pen me-1"></i> Compor Novo Email
        </a>
    </div>
</div>

<?= view('admin/partials/alerts') ?>

<div class="row">
    <?php foreach ($templates as $key => $template): ?>
    <div class="col-md-6 col-lg-4 mb-4">
        <div class="template-card">
            <div class="template-header">
                <h5>
                    <i class="fas fa-file-alt text-primary me-2"></i>
                    <?= $template['name'] ?>
                </h5>
            </div>
            <div class="template-body">
                <p class="text-muted mb-3"><?= $template['description'] ?></p>
                
                <h6 class="fw-bold mb-2">Variáveis disponíveis:</h6>
                <div class="mb-3">
                    <?php foreach ($template['variables'] as $var): ?>
                        <span class="variable-badge">{<?= $var ?>}</span>
                    <?php endforeach; ?>
                </div>
                
                <div class="d-flex gap-2 mt-3">
                    <a href="<?= site_url('admin/emails/preview/' . $key) ?>" 
                       target="_blank" 
                       class="btn btn-template btn-preview flex-fill">
                        <i class="fas fa-eye me-1"></i> Visualizar
                    </a>
                    <a href="<?= site_url('admin/emails/compose?template=' . $key) ?>" 
                       class="btn btn-template btn-use flex-fill">
                        <i class="fas fa-paper-plane me-1"></i> Usar Template
                    </a>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<!-- Modal de ajuda (opcional) -->
<div class="modal fade" id="helpModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Como usar templates</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Os templates de email permitem criar mensagens padronizadas com variáveis que serão substituídas automaticamente.</p>
                
                <h6>Variáveis comuns:</h6>
                <ul>
                    <li><code>{name}</code> - Nome do destinatário</li>
                    <li><code>{school_name}</code> - Nome da escola</li>
                    <li><code>{student_name}</code> - Nome do aluno</li>
                    <li><code>{username}</code> - Nome de usuário</li>
                </ul>
                
                <p class="mb-0 text-muted">As variáveis são substituídas automaticamente no momento do envio.</p>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>
<?= $this->section('scripts') ?>
// Adicione no final do arquivo, dentro da seção de scripts
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Verificar se há template selecionado na URL
    const urlParams = new URLSearchParams(window.location.search);
    const templateParam = urlParams.get('template');
    
    if (templateParam) {
        const templateSelect = document.getElementById('templateSelect');
        if (templateSelect) {
            templateSelect.value = templateParam;
            loadTemplate(); // Função que carrega o template
        }
    }
});

// Função para carregar template (já existente, mas vamos garantir)
function loadTemplate() {
    const template = document.getElementById('templateSelect').value;
    if (!template) return;
    
    // Templates pré-definidos
    const templates = {
        'welcome': 'Olá {name},\n\nSeja bem-vindo ao {school_name}! Estamos muito felizes em tê-lo conosco.\n\nAtenciosamente,\nEquipe {school_name}',
        'password_reset': 'Olá {name},\n\nRecebemos uma solicitação para redefinir sua senha.\n\nClique no link abaixo para continuar:\n{reset_link}\n\nSe não foi você, ignore este email.\n\nAtenciosamente,\n{school_name}',
        'payment_notification': 'Prezado(a) {student_name},\n\nConfirmamos o recebimento do pagamento no valor de {amount} referente à fatura {invoice_number}.\n\nAtenciosamente,\n{school_name}',
        'fee_reminder': 'Prezado(a) {student_name},\n\nLembramos que a propina referente a {fee_description} vence em {due_date}.\n\nValor: {amount}\n\nAtenciosamente,\n{school_name}',
        'exam_schedule': 'Prezado(a) {student_name},\n\nConfira abaixo o calendário de exames:\n\n{exam_list}\n\nPeríodo: {exam_period}\n\nAtenciosamente,\n{school_name}',
        'enrollment_confirmation': 'Prezado(a) {student_name},\n\nSua matrícula foi confirmada para o ano letivo {academic_year} na turma {class_name}.\n\nNúmero de matrícula: {enrollment_number}\n\nAtenciosamente,\n{school_name}',
        'grade_posted': 'Prezado(a) {student_name},\n\nUma nova nota foi lançada em seu boletim.\n\nDisciplina: {discipline}\nNota: {grade}\nTipo: {assessment_type}\nData: {date}\nSemestre: {semester}\n\nAtenciosamente,\n{school_name}',
        'deadline_reminder': 'Prezado(a) {student_name},\n\nIdentificamos os seguintes itens próximos do vencimento:\n\n{items}\n\nAtenciosamente,\n{school_name}',
        'test': 'Olá {name},\n\nEste é um email de teste do sistema {school_name}.\n\nSe você está recebendo esta mensagem, as configurações de email estão funcionando corretamente!\n\nAtenciosamente,\nEquipe {school_name}',
        'custom': '{custom_message}'
    };
    
    if (templates[template]) {
        document.getElementById('message').value = templates[template];
    }
}
</script>
<?= $this->endSection() ?>