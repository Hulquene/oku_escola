<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<style>
:root {
    --primary: #1B2B4B;
    --accent: #3B7FE8;
    --border: #E2E8F4;
    --surface: #F5F7FC;
}

.page-header {
    background: linear-gradient(135deg, var(--primary) 0%, #243761 100%);
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

.compose-card {
    background: white;
    border: 1px solid var(--border);
    border-radius: 12px;
    overflow: hidden;
}

.compose-header {
    background: var(--surface);
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--border);
    font-weight: 600;
}

.compose-body {
    padding: 1.5rem;
}

.form-label {
    font-weight: 600;
    color: var(--primary);
    font-size: 0.875rem;
    margin-bottom: 0.5rem;
}

.form-control, .form-select {
    border: 1.5px solid var(--border);
    border-radius: 8px;
    padding: 0.5rem 0.75rem;
}

.form-control:focus, .form-select:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(59,127,232,.1);
}

.recipient-info {
    background: var(--surface);
    border-radius: 8px;
    padding: 1rem;
    margin-bottom: 1rem;
    border-left: 4px solid var(--accent);
}

.variable-badge {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 20px;
    padding: 0.25rem 0.75rem;
    font-size: 0.75rem;
    cursor: pointer;
    transition: all 0.2s;
}

.variable-badge:hover {
    background: var(--accent);
    color: white;
    border-color: var(--accent);
}

.btn-primary {
    background: var(--accent);
    border: none;
    padding: 0.5rem 1.5rem;
    border-radius: 8px;
}

.btn-primary:hover {
    background: #2C6FD4;
}
</style>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="fas fa-pen me-2"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/emails') ?>">Emails</a></li>
                    <li class="breadcrumb-item active">Compor</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<?= view('admin/partials/alerts') ?>

<div class="compose-card">
    <div class="compose-header">
        <i class="fas fa-edit me-2"></i> Compor Nova Mensagem
    </div>
    <div class="compose-body">
        <form action="<?= site_url('admin/emails/send') ?>" method="post" enctype="multipart/form-data" id="composeForm">
            <?= csrf_field() ?>
            
            <?php if (isset($recipient)): ?>
            <input type="hidden" name="recipient_id" value="<?= $recipient['id'] ?>">
            <input type="hidden" name="recipient_type" value="<?= $recipient['type'] ?>">
            
            <div class="recipient-info">
                <div class="row">
                    <div class="col-md-6">
                        <small class="text-muted">Destinatário</small>
                        <div class="fw-bold"><?= $recipient['name'] ?></div>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted">Email</small>
                        <div class="fw-bold"><?= $recipient['email'] ?></div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Nome do Destinatário <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" name="recipient_name" 
                               value="<?= old('recipient_name', $recipient['name'] ?? '') ?>" required>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Email do Destinatário <span class="text-danger">*</span></label>
                        <input type="email" class="form-control" name="recipient_email" 
                               value="<?= old('recipient_email', $recipient['email'] ?? '') ?>" required>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Assunto <span class="text-danger">*</span></label>
                <input type="text" class="form-control" name="subject" 
                       value="<?= old('subject') ?>" required>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Template <span class="text-danger">*</span></label>
                        <select class="form-select" name="template" id="templateSelect" required onchange="loadTemplate()">
                            <option value="">Selecione um template</option>
                            <?php foreach ($templates as $key => $name): ?>
                                <option value="<?= $key ?>" <?= old('template') == $key ? 'selected' : '' ?>>
                                    <?= $name ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Variáveis Disponíveis</label>
                        <div class="d-flex flex-wrap gap-2" id="variablesList">
                            <span class="variable-badge" onclick="insertVariable('{name}')">{name}</span>
                            <span class="variable-badge" onclick="insertVariable('{school_name}')">{school_name}</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Mensagem <span class="text-danger">*</span></label>
                <textarea class="form-control" name="message" id="message" rows="12" required><?= old('message') ?></textarea>
                <small class="text-muted">Você pode usar as variáveis acima clicando nelas</small>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">Texto do Botão (opcional)</label>
                        <input type="text" class="form-control" name="button_text" 
                               value="<?= old('button_text') ?>" placeholder="Clique Aqui">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="mb-3">
                        <label class="form-label">URL do Botão (opcional)</label>
                        <input type="url" class="form-control" name="button_url" 
                               value="<?= old('button_url') ?>" placeholder="https://...">
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Anexos</label>
                <input type="file" class="form-control" name="attachments[]" multiple>
                <small class="text-muted">Máximo 5MB por arquivo. Formatos: PDF, DOC, DOCX, JPG, PNG</small>
            </div>
            
            <hr class="my-4">
            
            <div class="text-end">
                <a href="<?= site_url('admin/emails') ?>" class="btn btn-secondary me-2">
                    <i class="fas fa-times me-1"></i> Cancelar
                </a>
                <button type="button" class="btn btn-info me-2" onclick="previewEmail()">
                    <i class="fas fa-eye me-1"></i> Visualizar
                </button>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane me-1"></i> Enviar Email
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Preview Modal -->
<div class="modal fade" id="previewModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Visualização do Email</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="previewContent">
                Carregando...
            </div>
        </div>
    </div>
</div>

<script>
function insertVariable(variable) {
    const message = document.getElementById('message');
    const start = message.selectionStart;
    const end = message.selectionEnd;
    const text = message.value;
    
    message.value = text.substring(0, start) + variable + text.substring(end);
    message.focus();
    message.setSelectionRange(start + variable.length, start + variable.length);
}

function loadTemplate() {
    const template = document.getElementById('templateSelect').value;
    if (!template) return;
    
    // Aqui você pode carregar um template pré-definido
    // Por enquanto, apenas mostra um exemplo
    const templates = {
        'welcome': 'Olá {name},\n\nSeja bem-vindo ao {school_name}! Estamos muito felizes em tê-lo conosco.\n\nAtenciosamente,\nEquipe {school_name}',
        'payment_notification': 'Prezado(a) {student_name},\n\nConfirmamos o recebimento do pagamento no valor de {amount} referente à fatura {invoice_number}.\n\nAtenciosamente,\n{school_name}'
    };
    
    if (templates[template]) {
        document.getElementById('message').value = templates[template];
    }
}

function previewEmail() {
    const subject = document.querySelector('[name="subject"]').value;
    const message = document.querySelector('[name="message"]').value;
    const buttonText = document.querySelector('[name="button_text"]').value;
    const buttonUrl = document.querySelector('[name="button_url"]').value;
    const recipientName = document.querySelector('[name="recipient_name"]').value;
    const schoolName = '<?= setting('school_name') ?>';
    
    // Substituir variáveis
    let previewHtml = message
        .replace(/{name}/g, recipientName)
        .replace(/{school_name}/g, schoolName)
        .replace(/\n/g, '<br>');
    
    const html = `
        <div style="font-family: 'Sora', Arial, sans-serif; padding: 20px;">
            <h3 style="color: #1B2B4B;">Assunto: ${subject}</h3>
            <hr style="border: 1px solid #E2E8F4;">
            <div style="background: #F5F7FC; padding: 20px; border-radius: 8px; margin-top: 20px;">
                <p><strong>Para:</strong> ${recipientName}</p>
                <div style="margin-top: 20px;">
                    ${previewHtml}
                </div>
                ${buttonText && buttonUrl ? `
                    <div style="text-align: center; margin: 30px 0 20px;">
                        <a href="${buttonUrl}" style="background: #3B7FE8; color: white; padding: 12px 30px; text-decoration: none; border-radius: 8px; font-weight: 600; display: inline-block;">
                            ${buttonText}
                        </a>
                    </div>
                ` : ''}
            </div>
        </div>
    `;
    
    document.getElementById('previewContent').innerHTML = html;
    
    var modal = new bootstrap.Modal(document.getElementById('previewModal'));
    modal.show();
}

// Validar formulário antes de enviar
document.getElementById('composeForm').addEventListener('submit', function(e) {
    const email = document.querySelector('[name="recipient_email"]').value;
    const subject = document.querySelector('[name="subject"]').value;
    const message = document.querySelector('[name="message"]').value;
    
    if (!email || !subject || !message) {
        e.preventDefault();
        alert('Por favor, preencha todos os campos obrigatórios');
    }
});
</script>

<?= $this->endSection() ?>