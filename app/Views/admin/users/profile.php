<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Meu Perfil</li>
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
                <i class="fas fa-user-circle"></i> Minha Foto
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
                
                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#photoModal">
                    <i class="fas fa-camera"></i> Alterar Foto
                </button>
            </div>
        </div>
        
        <!-- Quick Links -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-link"></i> Ações Rápidas
            </div>
            <div class="list-group list-group-flush">
                <a href="<?= site_url('admin/users/form-edit/' . $user->id) ?>" class="list-group-item list-group-item-action">
                    <i class="fas fa-edit text-primary"></i> Editar Perfil
                </a>
                <a href="#" class="list-group-item list-group-item-action" data-bs-toggle="modal" data-bs-target="#passwordModal">
                    <i class="fas fa-key text-warning"></i> Alterar Senha
                </a>
                <a href="<?= site_url('admin/dashboard') ?>" class="list-group-item list-group-item-action">
                    <i class="fas fa-tachometer-alt text-success"></i> Ir para Dashboard
                </a>
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
                                <th>Username:</th>
                                <td><?= $user->username ?></td>
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
                                <th style="width: 120px;">Perfil:</th>
                                <td><span class="badge bg-info"><?= session()->get('role') ?></span></td>
                            </tr>
                            <tr>
                                <th>Tipo:</th>
                                <td><span class="badge bg-primary"><?= ucfirst($user->user_type) ?></span></td>
                            </tr>
                            <tr>
                                <th>Membro desde:</th>
                                <td><?= date('d/m/Y', strtotime($user->created_at)) ?></td>
                            </tr>
                            <tr>
                                <th>Último acesso:</th>
                                <td><?= $user->last_login ? date('d/m/Y H:i', strtotime($user->last_login)) : '-' ?></td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <?php if ($user->address): ?>
                    <div class="mt-3">
                        <h6>Endereço:</h6>
                        <p><?= $user->address ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Recent Activity -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-history"></i> Minhas Atividades Recentes
            </div>
            <div class="card-body">
                <?php
                $logModel = new \App\Models\UserLogModel();
                $logs = $logModel->getUserLogs($user->id, 10);
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

<!-- Modal Alterar Foto -->
<div class="modal fade" id="photoModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('admin/users/update-photo') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                
                <div class="modal-header">
                    <h5 class="modal-title">Alterar Foto de Perfil</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="photo" class="form-label">Selecionar nova foto</label>
                        <input type="file" class="form-control" id="photo" name="photo" accept="image/*" required>
                        <small class="text-muted">Formatos: JPG, PNG. Máx: 2MB</small>
                    </div>
                    
                    <div id="photoPreview" class="text-center mt-3" style="display: none;">
                        <img src="#" alt="Preview" class="img-fluid rounded-circle" style="max-width: 150px;">
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Alterar Foto</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Alterar Senha -->
<div class="modal fade" id="passwordModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('admin/users/change-password') ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="modal-header">
                    <h5 class="modal-title">Alterar Senha</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="current_password" class="form-label">Senha Atual</label>
                        <input type="password" class="form-control" id="current_password" name="current_password" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="new_password" class="form-label">Nova Senha</label>
                        <input type="password" class="form-control" id="new_password" name="new_password" 
                               minlength="6" required>
                        <small class="text-muted">Mínimo de 6 caracteres</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirmar Nova Senha</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                               minlength="6" required>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Alterar Senha</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Preview foto antes do upload
document.getElementById('photo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(e) {
            const preview = document.getElementById('photoPreview');
            preview.style.display = 'block';
            preview.querySelector('img').src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
});

// Validar confirmação de senha
document.getElementById('confirm_password').addEventListener('keyup', function() {
    const password = document.getElementById('new_password').value;
    const confirm = this.value;
    
    if (password !== confirm) {
        this.setCustomValidity('As senhas não coincidem');
    } else {
        this.setCustomValidity('');
    }
});
</script>
<?= $this->endSection() ?>