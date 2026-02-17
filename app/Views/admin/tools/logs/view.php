<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <a href="<?= site_url('admin/tools/logs') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/tools/logs') ?>">Logs</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detalhes</li>
        </ol>
    </nav>
</div>

<!-- Log Details -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-info-circle"></i> Detalhes do Registo
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 150px;">ID</th>
                        <td><?= $log->id ?></td>
                    </tr>
                    <tr>
                        <th>Data/Hora</th>
                        <td><?= date('d/m/Y H:i:s', strtotime($log->created_at)) ?></td>
                    </tr>
                    <tr>
                        <th>Ação</th>
                        <td>
                            <span class="badge bg-<?= getActionColor($log->action) ?> p-2">
                                <?= ucfirst($log->action) ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <th>IP Address</th>
                        <td><?= $log->ip_address ?: '-' ?></td>
                    </tr>
                </table>
            </div>
            
            <div class="col-md-6">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 150px;">Usuário</th>
                        <td>
                            <div class="d-flex align-items-center">
                                <?php if ($log->photo): ?>
                                    <img src="<?= base_url('uploads/users/' . $log->photo) ?>" 
                                         class="rounded-circle me-2" style="width: 40px; height: 40px; object-fit: cover;">
                                <?php else: ?>
                                    <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center text-white me-2"
                                         style="width: 40px; height: 40px;">
                                        <?= strtoupper(substr($log->first_name, 0, 1)) ?>
                                    </div>
                                <?php endif; ?>
                                <div>
                                    <strong><?= $log->first_name ?> <?= $log->last_name ?></strong>
                                    <br><small><?= $log->username ?></small>
                                    <br><small><?= $log->email ?></small>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-light">
                        <h6 class="mb-0">User Agent</h6>
                    </div>
                    <div class="card-body">
                        <p class="mb-0" style="word-break: break-all;"><?= $log->user_agent ?: '-' ?></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('functions') ?>
<?php
function getActionColor($action) {
    $colors = [
        'insert' => 'success',
        'update' => 'info',
        'delete' => 'danger',
        'login' => 'primary',
        'logout' => 'secondary',
        'view' => 'light'
    ];
    return $colors[$action] ?? 'secondary';
}
?>
<?= $this->endSection() ?>