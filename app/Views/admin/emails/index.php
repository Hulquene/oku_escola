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

.stats-card {
    background: var(--surface-card);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 1.5rem;
    transition: all 0.3s ease;
}

.stats-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(27,43,75,.10);
}

.stats-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin-bottom: 1rem;
}

.stats-icon.primary { background: rgba(59,127,232,.1); color: var(--accent); }
.stats-icon.success { background: rgba(22,168,125,.1); color: var(--success); }
.stats-icon.warning { background: rgba(232,160,32,.1); color: var(--warning); }

.stats-number {
    font-size: 2rem;
    font-weight: 700;
    color: var(--text-primary);
    margin-bottom: 0.25rem;
}

.stats-label {
    color: var(--text-secondary);
    font-size: 0.875rem;
}

.quick-action-card {
    background: var(--surface-card);
    border: 1px solid var(--border);
    border-radius: 12px;
    padding: 1.5rem;
    text-align: center;
    transition: all 0.3s ease;
    cursor: pointer;
    text-decoration: none;
    color: var(--text-primary);
    display: block;
}

.quick-action-card:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 16px rgba(59,127,232,.15);
    border-color: var(--accent);
    text-decoration: none;
    color: var(--text-primary);
}

.quick-action-icon {
    width: 64px;
    height: 64px;
    border-radius: 16px;
    background: rgba(59,127,232,.1);
    color: var(--accent);
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.5rem;
    margin: 0 auto 1rem;
}

.quick-action-title {
    font-weight: 600;
    margin-bottom: 0.25rem;
}

.quick-action-desc {
    color: var(--text-secondary);
    font-size: 0.875rem;
}

.table-emails {
    width: 100%;
    border-collapse: collapse;
}

.table-emails th {
    background: var(--surface);
    padding: 0.75rem 1rem;
    text-align: left;
    font-weight: 600;
    color: var(--text-secondary);
    font-size: 0.75rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    border-bottom: 1px solid var(--border);
}

.table-emails td {
    padding: 1rem;
    border-bottom: 1px solid var(--border);
}

.status-badge {
    padding: 0.25rem 0.5rem;
    border-radius: 20px;
    font-size: 0.75rem;
    font-weight: 600;
}

.status-badge.sent {
    background: rgba(22,168,125,.1);
    color: var(--success);
}

.template-item {
    padding: 0.75rem 1rem;
    border: 1px solid var(--border);
    border-radius: 8px;
    margin-bottom: 0.5rem;
    transition: all 0.2s ease;
    cursor: pointer;
}

.template-item:hover {
    border-color: var(--accent);
    background: rgba(59,127,232,.05);
}

.template-name {
    font-weight: 600;
    color: var(--text-primary);
}

.template-desc {
    font-size: 0.75rem;
    color: var(--text-secondary);
}
</style>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1><i class="fas fa-envelope me-2"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active">Emails</li>
                </ol>
            </nav>
        </div>
    </div>
</div>

<?= view('admin/partials/alerts') ?>

<!-- Estatísticas -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="stats-card">
            <div class="stats-icon primary">
                <i class="fas fa-user-graduate"></i>
            </div>
            <div class="stats-number"><?= number_format($total_students) ?></div>
            <div class="stats-label">Alunos Ativos</div>
            <a href="<?= site_url('admin/emails/bulk?type=students') ?>" class="btn btn-sm btn-outline-primary mt-3">
                <i class="fas fa-paper-plane me-1"></i> Enviar para Alunos
            </a>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="stats-card">
            <div class="stats-icon success">
                <i class="fas fa-chalkboard-teacher"></i>
            </div>
            <div class="stats-number"><?= number_format($total_teachers) ?></div>
            <div class="stats-label">Professores Ativos</div>
            <a href="<?= site_url('admin/emails/bulk?type=teachers') ?>" class="btn btn-sm btn-outline-success mt-3">
                <i class="fas fa-paper-plane me-1"></i> Enviar para Professores
            </a>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="stats-card">
            <div class="stats-icon warning">
                <i class="fas fa-users"></i>
            </div>
            <div class="stats-number"><?= number_format($total_guardians) ?></div>
            <div class="stats-label">Encarregados Ativos</div>
            <a href="<?= site_url('admin/emails/bulk?type=guardians') ?>" class="btn btn-sm btn-outline-warning mt-3">
                <i class="fas fa-paper-plane me-1"></i> Enviar para Encarregados
            </a>
        </div>
    </div>
</div>

<!-- Ações Rápidas -->
<div class="row mb-4">
    <div class="col-md-3">
        <a href="<?= site_url('admin/emails/compose') ?>" class="quick-action-card">
            <div class="quick-action-icon">
                <i class="fas fa-pen"></i>
            </div>
            <div class="quick-action-title">Compor Email</div>
            <div class="quick-action-desc">Enviar email personalizado</div>
        </a>
    </div>
    
    <div class="col-md-3">
        <a href="<?= site_url('admin/emails/bulk') ?>" class="quick-action-card">
            <div class="quick-action-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="quick-action-title">Email em Massa</div>
            <div class="quick-action-desc">Enviar para múltiplos destinatários</div>
        </a>
    </div>
    
    <div class="col-md-3">
        <a href="<?= site_url('admin/emails/templates') ?>" class="quick-action-card">
            <div class="quick-action-icon">
                <i class="fas fa-file-alt"></i>
            </div>
            <div class="quick-action-title">Templates</div>
            <div class="quick-action-desc">Gerir modelos de email</div>
        </a>
    </div>
    
    <div class="col-md-3">
        <a href="<?= site_url('admin/settings') ?>#email" class="quick-action-card">
            <div class="quick-action-icon">
                <i class="fas fa-cog"></i>
            </div>
            <div class="quick-action-title">Configurações</div>
            <div class="quick-action-desc">Configurar servidor de email</div>
        </a>
    </div>
</div>

<div class="row">
    <!-- Últimos Emails -->
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-history me-2"></i> Últimos Emails Enviados
            </div>
            <div class="card-body">
                <?php if (empty($recent_emails)): ?>
                    <p class="text-muted text-center py-4">Nenhum email enviado ainda.</p>
                <?php else: ?>
                    <table class="table-emails">
                        <thead>
                            <tr>
                                <th>Data/Hora</th>
                                <th>Destinatário</th>
                                <th>Assunto</th>
                                <th>Template</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recent_emails as $email): ?>
                            <tr>
                                <td><?= date('d/m/Y H:i', strtotime($email['sent_at'])) ?></td>
                                <td>
                                    <strong><?= $email['recipient_name'] ?></strong><br>
                                    <small class="text-muted"><?= $email['recipient'] ?></small>
                                </td>
                                <td><?= $email['subject'] ?></td>
                                <td>
                                    <span class="badge bg-light text-dark"><?= $email['template'] ?? 'personalizado' ?></span>
                                </td>
                                <td>
                                    <span class="status-badge sent">Enviado</span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Testar Configuração e Templates -->
    <div class="col-md-4">
        <!-- Testar Configuração -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-vial me-2"></i> Testar Configuração
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label for="test_email" class="form-label">Email para Teste</label>
                    <div class="input-group">
                        <input type="email" class="form-control" id="test_email" 
                               placeholder="teste@exemplo.com"
                               value="<?= session()->get('email') ?>">
                        <button class="btn btn-primary" type="button" onclick="sendTestEmail()">
                            <i class="fas fa-paper-plane"></i>
                        </button>
                    </div>
                </div>
                <div id="test_result" class="alert" style="display: none;"></div>
            </div>
        </div>
        
        <!-- Templates Rápidos -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-file-alt me-2"></i> Templates Rápidos
            </div>
            <div class="card-body">
                <?php 
                $quickTemplates = [
                    'welcome' => ['icon' => 'fa-hand-peace', 'color' => 'success', 'name' => 'Boas-vindas'],
                    'password_reset' => ['icon' => 'fa-key', 'color' => 'warning', 'name' => 'Recuperar Senha'],
                    'payment_notification' => ['icon' => 'fa-money-bill-wave', 'color' => 'info', 'name' => 'Pagamento'],
                    'deadline_reminder' => ['icon' => 'fa-clock', 'color' => 'danger', 'name' => 'Prazo Próximo']
                ];
                ?>
                
                <?php foreach ($quickTemplates as $key => $template): ?>
                <div class="template-item" onclick="window.location.href='<?= site_url('admin/emails/preview/'.$key) ?>'">
                    <div class="d-flex align-items-center">
                        <div class="me-3">
                            <i class="fas <?= $template['icon'] ?> text-<?= $template['color'] ?>"></i>
                        </div>
                        <div>
                            <div class="template-name"><?= $template['name'] ?></div>
                            <div class="template-desc">Clique para visualizar</div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <a href="<?= site_url('admin/emails/templates') ?>" class="btn btn-outline-primary w-100 mt-3">
                    <i class="fas fa-list me-1"></i> Ver Todos os Templates
                </a>
            </div>
        </div>
    </div>
</div>

<script>
function sendTestEmail() {
    const email = document.getElementById('test_email').value;
    if (!email) {
        alert('Por favor, insira um email para teste');
        return;
    }
    
    const resultDiv = document.getElementById('test_result');
    resultDiv.style.display = 'block';
    resultDiv.className = 'alert alert-info';
    resultDiv.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i> Enviando...';
    
    fetch('<?= site_url('admin/emails/test') ?>', {
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
            resultDiv.innerHTML = '<i class="fas fa-check-circle me-2"></i> ' + data.message;
        } else {
            resultDiv.className = 'alert alert-danger';
            resultDiv.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i> ' + data.message;
        }
    })
    .catch(error => {
        resultDiv.className = 'alert alert-danger';
        resultDiv.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i> Erro ao testar';
    });
}
</script>

<?= $this->endSection() ?>