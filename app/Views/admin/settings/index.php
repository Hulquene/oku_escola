<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1>Configurações do Sistema</h1>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Configurações</li>
        </ol>
    </nav>
</div>

<!-- Cards de Configurações -->
<div class="row g-4">
    <!-- Configurações Gerais -->
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="bg-primary bg-opacity-10 p-3 rounded-circle d-inline-block mb-3">
                    <i class="fas fa-sliders-h fa-3x text-primary"></i>
                </div>
                <h5 class="card-title">Configurações Gerais</h5>
                <p class="card-text text-muted">Configure nome da aplicação, timezone, formato de data e outras preferências gerais.</p>
                <a href="<?= site_url('admin/settings/general') ?>" class="btn btn-outline-primary">
                    <i class="fas fa-edit me-2"></i>Gerenciar
                </a>
            </div>
        </div>
    </div>
    
    <!-- Configurações da Escola -->
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="bg-success bg-opacity-10 p-3 rounded-circle d-inline-block mb-3">
                    <i class="fas fa-school fa-3x text-success"></i>
                </div>
                <h5 class="card-title">Configurações da Escola</h5>
                <p class="card-text text-muted">Dados da instituição, logo, contactos, endereço e informações da escola.</p>
                <a href="<?= site_url('admin/settings/school') ?>" class="btn btn-outline-success">
                    <i class="fas fa-edit me-2"></i>Gerenciar
                </a>
            </div>
        </div>
    </div>
    
    <!-- Configurações Académicas -->
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="bg-info bg-opacity-10 p-3 rounded-circle d-inline-block mb-3">
                    <i class="fas fa-graduation-cap fa-3x text-info"></i>
                </div>
                <h5 class="card-title">Configurações Académicas</h5>
                <p class="card-text text-muted">Ano letivo atual, semestre, sistema de avaliação, notas mínimas e máximas.</p>
                <a href="<?= site_url('admin/settings/academic') ?>" class="btn btn-outline-info">
                    <i class="fas fa-edit me-2"></i>Gerenciar
                </a>
            </div>
        </div>
    </div>
    
    <!-- Configurações de Pagamento -->
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="bg-warning bg-opacity-10 p-3 rounded-circle d-inline-block mb-3">
                    <i class="fas fa-money-bill-wave fa-3x text-warning"></i>
                </div>
                <h5 class="card-title">Configurações de Pagamento</h5>
                <p class="card-text text-muted">Moeda padrão, taxas, multas, prazos de pagamento e configurações de faturas.</p>
                <a href="<?= site_url('admin/settings/payment') ?>" class="btn btn-outline-warning">
                    <i class="fas fa-edit me-2"></i>Gerenciar
                </a>
            </div>
        </div>
    </div>
    
    <!-- Configurações de Email -->
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="bg-secondary bg-opacity-10 p-3 rounded-circle d-inline-block mb-3">
                    <i class="fas fa-envelope fa-3x text-secondary"></i>
                </div>
                <h5 class="card-title">Configurações de Email</h5>
                <p class="card-text text-muted">Servidor SMTP, configurações de envio, emails de notificação e templates.</p>
                <a href="<?= site_url('admin/settings/email') ?>" class="btn btn-outline-secondary">
                    <i class="fas fa-edit me-2"></i>Gerenciar
                </a>
            </div>
        </div>
    </div>
    
    <!-- Gestão de Utilizadores -->
    <div class="col-md-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <div class="bg-danger bg-opacity-10 p-3 rounded-circle d-inline-block mb-3">
                    <i class="fas fa-users-cog fa-3x text-danger"></i>
                </div>
                <h5 class="card-title">Gestão de Utilizadores</h5>
                <p class="card-text text-muted">Gerenciar utilizadores, perfis de acesso e permissões do sistema.</p>
                <div class="d-flex justify-content-center gap-2">
                    <a href="<?= site_url('admin/users') ?>" class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-users"></i> Utilizadores
                    </a>
                    <a href="<?= site_url('admin/roles') ?>" class="btn btn-outline-danger btn-sm">
                        <i class="fas fa-shield-alt"></i> Perfis
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Informações do Sistema -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>Informações do Sistema</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <th width="200">Versão do Sistema:</th>
                                <td><?= config('App')->appVersion ?? '1.0.0' ?></td>
                            </tr>
                            <tr>
                                <th>Ambiente:</th>
                                <td>
                                    <?php if (ENVIRONMENT === 'production'): ?>
                                        <span class="badge bg-success">Produção</span>
                                    <?php elseif (ENVIRONMENT === 'development'): ?>
                                        <span class="badge bg-warning">Desenvolvimento</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Teste</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Timezone:</th>
                                <td><?= date_default_timezone_get() ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <th width="200">Total de Settings:</th>
                                <td><?= count($settings) ?></td>
                            </tr>
                            <tr>
                                <th>Ano Letivo Atual:</th>
                                <td><?= $currentYear ? $currentYear->year_name : 'Não definido' ?></td>
                            </tr>
                            <tr>
                                <th>Semestre Atual:</th>
                                <td><?= count($semesters) ?> semestres ativos</td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Ações Rápidas -->
<div class="row mt-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-light">
                <h5 class="mb-0"><i class="fas fa-bolt me-2"></i>Ações Rápidas</h5>
            </div>
            <div class="card-body">
                <div class="d-flex gap-2">
                    <a href="<?= site_url('admin/settings/clear-cache') ?>" class="btn btn-outline-warning" onclick="return confirm('Limpar cache?')">
                        <i class="fas fa-broom me-2"></i>Limpar Cache
                    </a>
                    <button class="btn btn-outline-secondary" onclick="window.location.reload()">
                        <i class="fas fa-sync-alt me-2"></i>Recarregar
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>