<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <div>
            <a href="<?= site_url('admin/users/form-edit/' . $user->id) ?>" class="btn btn-info">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="<?= site_url('admin/users') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/users') ?>">Utilizadores</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detalhes do Utilizador</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<div class="row">
    <div class="col-md-4">
        <!-- Profile Card -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-user-circle"></i> Perfil
            </div>
            <div class="card-body text-center">
                <?php if ($user->photo): ?>
                    <img src="<?= base_url('uploads/users/' . $user->photo) ?>" 
                         alt="Foto" 
                         class="rounded-circle img-fluid mb-3"
                         style="width: 150px; height: 150px; object-fit: cover;">
                <?php else: ?>
                    <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center text-white mb-3"
                         style="width: 150px; height: 150px; font-size: 3rem;">
                        <?= strtoupper(substr($user->first_name, 0, 1) . substr($user->last_name, 0, 1)) ?>
                    </div>
                <?php endif; ?>
                
                <h4><?= $user->first_name ?> <?= $user->last_name ?></h4>
                <p class="text-muted"><?= $user->email ?></p>
                
                <div class="mt-3">
                    <?php if ($user->is_active): ?>
                        <span class="badge bg-success p-2">Ativo</span>
                    <?php else: ?>
                        <span class="badge bg-danger p-2">Inativo</span>
                    <?php endif; ?>
                    
                    <span class="badge bg-info p-2 ms-2"><?= $user->role_name ?></span>
                    
                    <?php
                    $typeClass = [
                        'admin' => 'danger',
                        'teacher' => 'success',
                        'student' => 'primary',
                        'guardian' => 'warning',
                        'staff' => 'secondary'
                    ][$user->user_type] ?? 'secondary';
                    ?>
                    <span class="badge bg-<?= $typeClass ?> p-2 ms-2"><?= ucfirst($user->user_type) ?></span>
                </div>
            </div>
        </div>
        
        <!-- Login Info -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-clock"></i> Informações de Acesso
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <th>Username:</th>
                        <td><?= $user->username ?></td>
                    </tr>
                    <tr>
                        <th>Último Acesso:</th>
                        <td><?= $user->last_login ? date('d/m/Y H:i', strtotime($user->last_login)) : 'Nunca' ?></td>
                    </tr>
                    <tr>
                        <th>Criado em:</th>
                        <td><?= date('d/m/Y H:i', strtotime($user->created_at)) ?></td>
                    </tr>
                    <tr>
                        <th>Atualizado em:</th>
                        <td><?= date('d/m/Y H:i', strtotime($user->updated_at)) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <!-- Personal Info -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-id-card"></i> Informações Pessoais
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <th style="width: 120px;">Nome:</th>
                                <td><?= $user->first_name ?> <?= $user->last_name ?></td>
                            </tr>
                            <tr>
                                <th>Email:</th>
                                <td><?= $user->email ?></td>
                            </tr>
                            <tr>
                                <th>Telefone:</th>
                                <td><?= $user->phone ?: '-' ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-sm">
                            <tr>
                                <th style="width: 120px;">Endereço:</th>
                                <td><?= $user->address ?: '-' ?></td>
                            </tr>
                            <tr>
                                <th>Perfil:</th>
                                <td><?= $user->role_name ?></td>
                            </tr>
                            <tr>
                                <th>Tipo:</th>
                                <td><?= ucfirst($user->user_type) ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Activity Log -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-history"></i> Atividades Recentes
            </div>
            <div class="card-body">
                <?php
                $logModel = new \App\Models\UserLogModel();
                $logs = $logModel->getUserLogs($user->id, 20);
                ?>
                
                <?php if (!empty($logs)): ?>
                    <div class="list-group">
                        <?php foreach ($logs as $log): ?>
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <strong><?= ucfirst($log->action) ?></strong>
                                        <p class="mb-0 small text-muted">
                                            <i class="fas fa-globe"></i> IP: <?= $log->ip_address ?: 'Desconhecido' ?>
                                        </p>
                                    </div>
                                    <small class="text-muted">
                                        <?= date('d/m/Y H:i', strtotime($log->created_at)) ?>
                                    </small>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">Nenhuma atividade registada</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>