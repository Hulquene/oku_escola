<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Configurações</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Settings Cards -->
<div class="row">
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-sliders-h fa-4x text-primary mb-3"></i>
                <h5 class="card-title">Configurações Gerais</h5>
                <p class="card-text text-muted">Nome da aplicação, timezone, formato de data e outras configurações básicas.</p>
                <a href="<?= site_url('admin/settings/general') ?>" class="btn btn-outline-primary">
                    <i class="fas fa-cog"></i> Configurar
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-building fa-4x text-success mb-3"></i>
                <h5 class="card-title">Dados da Empresa</h5>
                <p class="card-text text-muted">Informações da instituição, contacto, endereço e logo.</p>
                <a href="<?= site_url('admin/settings/general#company') ?>" class="btn btn-outline-success">
                    <i class="fas fa-building"></i> Configurar
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-envelope fa-4x text-info mb-3"></i>
                <h5 class="card-title">Configurações de Email</h5>
                <p class="card-text text-muted">Servidor SMTP, remetente, protocolo e outras configurações de email.</p>
                <a href="<?= site_url('admin/settings/email') ?>" class="btn btn-outline-info">
                    <i class="fas fa-envelope"></i> Configurar
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-money-bill-wave fa-4x text-warning mb-3"></i>
                <h5 class="card-title">Configurações de Pagamento</h5>
                <p class="card-text text-muted">Moeda, taxas, prazos de vencimento e opções de pagamento.</p>
                <a href="<?= site_url('admin/settings/payment') ?>" class="btn btn-outline-warning">
                    <i class="fas fa-money-bill-wave"></i> Configurar
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-key fa-4x text-danger mb-3"></i>
                <h5 class="card-title">Permissões</h5>
                <p class="card-text text-muted">Gerenciar permissões dos perfis de acesso.</p>
                <a href="<?= site_url('admin/settings/permissions') ?>" class="btn btn-outline-danger">
                    <i class="fas fa-key"></i> Configurar
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-4 mb-4">
        <div class="card h-100">
            <div class="card-body text-center">
                <i class="fas fa-eraser fa-4x text-secondary mb-3"></i>
                <h5 class="card-title">Manutenção</h5>
                <p class="card-text text-muted">Limpar cache, backups e outras tarefas de manutenção.</p>
                <a href="<?= site_url('admin/settings/clear-cache') ?>" class="btn btn-outline-secondary" onclick="return confirm('Limpar cache do sistema?')">
                    <i class="fas fa-eraser"></i> Limpar Cache
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>