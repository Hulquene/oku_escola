<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/settings') ?>">Configurações</a></li>
            <li class="breadcrumb-item active" aria-current="page">Email</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<div class="row">
    <div class="col-md-8">
        <!-- Email Settings Form -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-envelope"></i> Configurações do Servidor de Email
            </div>
            <div class="card-body">
                <form action="<?= site_url('admin/settings/save-email') ?>" method="post" id="emailForm">
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email_protocol" class="form-label">Protocolo <span class="text-danger">*</span></label>
                                <select class="form-select" id="email_protocol" name="email_protocol" required>
                                    <option value="smtp" <?= ($settings['email_protocol'] ?? 'smtp') == 'smtp' ? 'selected' : '' ?>>SMTP</option>
                                    <option value="sendmail" <?= ($settings['email_protocol'] ?? '') == 'sendmail' ? 'selected' : '' ?>>Sendmail</option>
                                    <option value="mail" <?= ($settings['email_protocol'] ?? '') == 'mail' ? 'selected' : '' ?>>PHP Mail</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email_smtp_crypto" class="form-label">Criptografia</label>
                                <select class="form-select" id="email_smtp_crypto" name="email_smtp_crypto">
                                    <option value="">Nenhuma</option>
                                    <option value="ssl" <?= ($settings['email_smtp_crypto'] ?? '') == 'ssl' ? 'selected' : '' ?>>SSL</option>
                                    <option value="tls" <?= ($settings['email_smtp_crypto'] ?? '') == 'tls' ? 'selected' : '' ?>>TLS</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email_smtp_host" class="form-label">Servidor SMTP</label>
                                <input type="text" class="form-control" id="email_smtp_host" name="email_smtp_host" 
                                       value="<?= $settings['email_smtp_host'] ?? '' ?>" placeholder="smtp.gmail.com">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email_smtp_port" class="form-label">Porta SMTP</label>
                                <input type="number" class="form-control" id="email_smtp_port" name="email_smtp_port" 
                                       value="<?= $settings['email_smtp_port'] ?? '587' ?>" placeholder="587">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email_smtp_user" class="form-label">Usuário SMTP</label>
                                <input type="text" class="form-control" id="email_smtp_user" name="email_smtp_user" 
                                       value="<?= $settings['email_smtp_user'] ?? '' ?>" placeholder="seu@email.com">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email_smtp_pass" class="form-label">Senha SMTP</label>
                                <div class="input-group">
                                    <input type="password" class="form-control" id="email_smtp_pass" name="email_smtp_pass" 
                                           value="<?= $settings['email_smtp_pass'] ?? '' ?>">
                                    <button class="btn btn-outline-secondary" type="button" onclick="togglePassword()">
                                        <i class="fas fa-eye" id="toggleIcon"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3" id="sendmailPath" style="display: none;">
                        <label for="email_sendmail_path" class="form-label">Caminho do Sendmail</label>
                        <input type="text" class="form-control" id="email_sendmail_path" name="email_sendmail_path" 
                               value="<?= $settings['email_sendmail_path'] ?? '/usr/sbin/sendmail' ?>">
                    </div>
                    
                    <hr>
                    
                    <h5 class="mb-3">Configurações do Remetente</h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email_from" class="form-label">Email de Remetente <span class="text-danger">*</span></label>
                                <input type="email" class="form-control" id="email_from" name="email_from" 
                                       value="<?= $settings['email_from'] ?? '' ?>" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email_from_name" class="form-label">Nome do Remetente <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="email_from_name" name="email_from_name" 
                                       value="<?= $settings['email_from_name'] ?? '' ?>" required>
                            </div>
                        </div>
                    </div>
                    
                    <hr>
                    
                    <div class="text-end">
                        <button type="button" class="btn btn-info me-2" onclick="testEmail()">
                            <i class="fas fa-paper-plane"></i> Testar Configuração
                        </button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Salvar Configurações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Info Card -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-info-circle"></i> Informações
            </div>
            <div class="card-body">
                <h6>Protocolos Suportados:</h6>
                <ul class="small">
                    <li><strong>SMTP</strong> - Recomendado para a maioria dos casos</li>
                    <li><strong>Sendmail</strong> - Servidores Linux com Sendmail instalado</li>
                    <li><strong>PHP Mail</strong> - Função mail() do PHP (menos seguro)</li>
                </ul>
                
                <h6 class="mt-3">Portas Comuns:</h6>
                <ul class="small">
                    <li><strong>25</strong> - SMTP (sem criptografia)</li>
                    <li><strong>465</strong> - SMTP com SSL</li>
                    <li><strong>587</strong> - SMTP com TLS (recomendado)</li>
                </ul>
                
                <h6 class="mt-3">Servidores Populares:</h6>
                <ul class="small">
                    <li><strong>Gmail</strong>: smtp.gmail.com (Porta 587, TLS)</li>
                    <li><strong>Outlook</strong>: smtp-mail.outlook.com (Porta 587, TLS)</li>
                    <li><strong>Yahoo</strong>: smtp.mail.yahoo.com (Porta 465, SSL)</li>
                </ul>
            </div>
        </div>
        
        <!-- Test Email Modal -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-envelope-open-text"></i> Enviar Email de Teste
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="test_email" class="form-label">Email para Teste</label>
                    <div class="input-group">
                        <input type="email" class="form-control" id="test_email" placeholder="seu@email.com">
                        <button class="btn btn-success" type="button" onclick="sendTestEmail()">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
                <div id="testResult" class="mt-2" style="display: none;"></div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Toggle password visibility
function togglePassword() {
    const password = document.getElementById('email_smtp_pass');
    const icon = document.getElementById('toggleIcon');
    
    if (password.type === 'password') {
        password.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        password.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Show/hide sendmail path based on protocol
document.getElementById('email_protocol').addEventListener('change', function() {
    const sendmailPath = document.getElementById('sendmailPath');
    sendmailPath.style.display = this.value === 'sendmail' ? 'block' : 'none';
});

// Initialize on page load
window.addEventListener('load', function() {
    const protocol = document.getElementById('email_protocol').value;
    document.getElementById('sendmailPath').style.display = protocol === 'sendmail' ? 'block' : 'none';
});

// Test email configuration
function testEmail() {
    const form = document.getElementById('emailForm');
    const formData = new FormData(form);
    
    fetch('<?= site_url('admin/settings/test-email') ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Configuração testada com sucesso!');
        } else {
            alert('Erro no teste: ' + data.message);
        }
    });
}

// Send test email
function sendTestEmail() {
    const email = document.getElementById('test_email').value;
    if (!email) {
        alert('Por favor, insira um email para teste');
        return;
    }
    
    const resultDiv = document.getElementById('testResult');
    resultDiv.style.display = 'block';
    resultDiv.className = 'alert alert-info';
    resultDiv.innerHTML = 'Enviando...';
    
    fetch('<?= site_url('admin/settings/send-test-email') ?>', {
        method: 'POST',
        body: new URLSearchParams({ test_email: email }),
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            resultDiv.className = 'alert alert-success';
            resultDiv.innerHTML = '<i class="fas fa-check-circle"></i> ' + data.message;
        } else {
            resultDiv.className = 'alert alert-danger';
            resultDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> ' + data.message;
        }
    })
    .catch(error => {
        resultDiv.className = 'alert alert-danger';
        resultDiv.innerHTML = '<i class="fas fa-exclamation-circle"></i> Erro ao enviar email';
    });
}
</script>
<?= $this->endSection() ?>