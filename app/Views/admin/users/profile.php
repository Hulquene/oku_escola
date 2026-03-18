<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<?php
// Helper functions for activity display - DEFINIDAS ANTES DE SEREM USADAS
if (!function_exists('getActivityIcon')) {
    function getActivityIcon($action) {
        $action = strtolower($action);
        $icons = [
            'login' => 'fa-sign-in-alt',
            'logout' => 'fa-sign-out-alt',
            'create' => 'fa-plus-circle',
            'insert' => 'fa-plus-circle',
            'update' => 'fa-edit',
            'edit' => 'fa-edit',
            'delete' => 'fa-trash',
            'remove' => 'fa-trash',
            'view' => 'fa-eye',
            'visualizar' => 'fa-eye',
            'export' => 'fa-file-export',
            'import' => 'fa-file-import',
            'download' => 'fa-download',
            'upload' => 'fa-upload',
            'print' => 'fa-print',
            'email' => 'fa-envelope',
            'send' => 'fa-paper-plane',
            'approve' => 'fa-check-circle',
            'reject' => 'fa-times-circle',
            'cancel' => 'fa-ban',
            'archive' => 'fa-archive',
            'restore' => 'fa-trash-restore',
            'password' => 'fa-key',
            'profile' => 'fa-user-circle'
        ];
        
        foreach ($icons as $key => $icon) {
            if (strpos($action, $key) !== false) {
                return $icon;
            }
        }
        
        return 'fa-circle';
    }
}

if (!function_exists('getActivityColor')) {
    function getActivityColor($action) {
        $action = strtolower($action);
        $colors = [
            'login' => 'success',
            'logout' => 'secondary',
            'create' => 'success',
            'insert' => 'success',
            'update' => 'info',
            'edit' => 'info',
            'delete' => 'danger',
            'remove' => 'danger',
            'view' => 'primary',
            'visualizar' => 'primary',
            'export' => 'warning',
            'import' => 'purple',
            'download' => 'primary',
            'upload' => 'success',
            'print' => 'secondary',
            'email' => 'info',
            'send' => 'info',
            'approve' => 'success',
            'reject' => 'danger',
            'cancel' => 'warning',
            'archive' => 'secondary',
            'restore' => 'success',
            'password' => 'warning',
            'profile' => 'primary'
        ];
        
        foreach ($colors as $key => $color) {
            if (strpos($action, $key) !== false) {
                return $color;
            }
        }
        
        return 'secondary';
    }
}
?>

<!-- Page Header -->
<div class="ci-page-header">
    <div class="ci-page-header-inner">
        <div>
            <h1 class="h3 mb-1"><?= $title ?? 'Meu Perfil' ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Meu Perfil</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <a href="<?= site_url('admin/users/form-edit/' . $user['id']) ?>" class="hdr-btn primary">
                <i class="fas fa-edit"></i> Editar Perfil
            </a>
        </div>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<div class="row g-4">
    <div class="col-md-4">
        <!-- Profile Card -->
        <div class="ci-card">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-user-circle"></i>
                    <span>Minha Foto</span>
                </div>
            </div>
            <div class="ci-card-body text-center">
                <?php if (!empty($user['photo'])): ?>
                    <img src="<?= base_url('uploads/users/' . $user['photo']) ?>" 
                         alt="Foto" 
                         class="rounded-circle mb-3 profile-avatar"
                         style="width: 150px; height: 150px; object-fit: cover; border: 3px solid var(--border);">
                <?php else: ?>
                    <div class="profile-avatar-placeholder mb-3">
                        <?= strtoupper(substr($user['first_name'] ?? '', 0, 1) . substr($user['last_name'] ?? '', 0, 1)) ?>
                    </div>
                <?php endif; ?>
                
                <h4 class="mb-1"><?= ($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '') ?></h4>
                <p class="text-muted mb-3"><?= $user['email'] ?? '' ?></p>
                
                <div class="d-flex gap-2 justify-content-center">
                    <span class="badge-ci primary">
                        <i class="fas fa-tag me-1"></i><?= $user['role_name'] ?? session()->get('role') ?>
                    </span>
                    <span class="badge-ci success">
                        <i class="fas fa-circle me-1" style="font-size: 6px;"></i>Ativo
                    </span>
                </div>
                
                <hr class="my-3">
                
                <button type="button" class="btn-ci outline w-100" data-bs-toggle="modal" data-bs-target="#photoModal">
                    <i class="fas fa-camera"></i> Alterar Foto
                </button>
            </div>
        </div>
        
        <!-- Quick Links -->
        <div class="ci-card mt-4">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-link"></i>
                    <span>Ações Rápidas</span>
                </div>
            </div>
            <div class="list-group list-group-flush">
                <a href="<?= site_url('admin/users/form-edit/' . $user['id']) ?>" class="topbar-dd-item">
                    <span class="dd-icon blue"><i class="fas fa-edit"></i></span>
                    <span>Editar Perfil</span>
                </a>
                <a href="#" class="topbar-dd-item" data-bs-toggle="modal" data-bs-target="#passwordModal">
                    <span class="dd-icon orange"><i class="fas fa-key"></i></span>
                    <span>Alterar Senha</span>
                </a>
                <a href="<?= site_url('admin/dashboard') ?>" class="topbar-dd-item">
                    <span class="dd-icon green"><i class="fas fa-tachometer-alt"></i></span>
                    <span>Ir para Dashboard</span>
                </a>
                <div class="dd-divider"></div>
                <a href="<?= site_url('admin/notifications') ?>" class="topbar-dd-item">
                    <span class="dd-icon purple"><i class="fas fa-bell"></i></span>
                    <span>Ver Notificações</span>
                </a>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <!-- Personal Info -->
        <div class="ci-card">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-id-card"></i>
                    <span>Informações Pessoais</span>
                </div>
                <span class="badge-ci code">ID: #<?= str_pad($user['id'] ?? 0, 4, '0', STR_PAD_LEFT) ?></span>
            </div>
            <div class="ci-card-body">
                <div class="row g-4">
                    <div class="col-md-6">
                        <div class="info-group">
                            <label class="info-label">Nome Completo</label>
                            <div class="info-value"><?= ($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '') ?></div>
                        </div>
                        
                        <div class="info-group">
                            <label class="info-label">Username</label>
                            <div class="info-value"><?= $user['username'] ?? '' ?></div>
                        </div>
                        
                        <div class="info-group">
                            <label class="info-label">Email</label>
                            <div class="info-value">
                                <a href="mailto:<?= $user['email'] ?? '' ?>"><?= $user['email'] ?? '' ?></a>
                            </div>
                        </div>
                        
                        <div class="info-group">
                            <label class="info-label">Telefone</label>
                            <div class="info-value">
                                <?php if (!empty($user['phone'])): ?>
                                    <a href="tel:<?= $user['phone'] ?>"><?= $user['phone'] ?></a>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="col-md-6">
                        <div class="info-group">
                            <label class="info-label">Perfil</label>
                            <div class="info-value">
                                <span class="badge-ci primary"><?= $user['role_name'] ?? session()->get('role') ?></span>
                            </div>
                        </div>
                        
                        <div class="info-group">
                            <label class="info-label">Tipo</label>
                            <div class="info-value">
                                <span class="badge-ci info"><?= ucfirst($user['user_type'] ?? 'staff') ?></span>
                            </div>
                        </div>
                        
                        <div class="info-group">
                            <label class="info-label">Membro desde</label>
                            <div class="info-value"><?= !empty($user['created_at']) ? date('d/m/Y', strtotime($user['created_at'])) : '-' ?></div>
                        </div>
                        
                        <div class="info-group">
                            <label class="info-label">Último acesso</label>
                            <div class="info-value">
                                <?php if (!empty($user['last_login'])): ?>
                                    <i class="fas fa-clock text-muted me-1"></i>
                                    <?= date('d/m/Y H:i', strtotime($user['last_login'])) ?>
                                <?php else: ?>
                                    <span class="text-muted">—</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php if (!empty($user['address'])): ?>
                    <hr class="my-3">
                    <div class="info-group">
                        <label class="info-label">Endereço</label>
                        <div class="info-value"><?= nl2br($user['address']) ?></div>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Recent Activity -->
        <div class="ci-card mt-4">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-history"></i>
                    <span>Minhas Atividades Recentes</span>
                </div>
                <span class="badge-ci code">Últimas 10</span>
            </div>
            <div class="ci-card-body p0">
                <?php
                $logModel = new \App\Models\UserLogModel();
                $logs = $logModel->where('user_id', $user['id'] ?? 0)
                                 ->orderBy('created_at', 'DESC')
                                 ->limit(10)
                                 ->findAll();
                ?>
                
                <?php if (!empty($logs)): ?>
                    <div class="activity-timeline">
                        <?php foreach ($logs as $log): ?>
                            <div class="activity-item">
                                <div class="activity-icon <?= getActivityColor($log['action'] ?? '') ?>">
                                    <i class="fas <?= getActivityIcon($log['action'] ?? '') ?>"></i>
                                </div>
                                <div class="activity-content">
                                    <div class="activity-title">
                                        <?= ucfirst($log['action'] ?? '') ?>
                                        <?php if (!empty($log['target_type'])): ?>
                                            <span class="badge-ci secondary ms-2"><?= $log['target_type'] ?></span>
                                        <?php endif; ?>
                                    </div>
                                    <?php if (!empty($log['description'])): ?>
                                        <div class="activity-desc"><?= $log['description'] ?></div>
                                    <?php endif; ?>
                                    <div class="activity-meta">
                                        <span><i class="fas fa-globe"></i> IP: <?= $log['ip_address'] ?? 'Desconhecido' ?></span>
                                        <span><i class="fas fa-clock"></i> <?= time_elapsed_string($log['created_at'] ?? '') ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    
                    <?php if (count($logs) >= 10): ?>
                        <div class="text-center p-3 border-top">
                            <a href="<?= site_url('admin/tools/logs?user_id=' . ($user['id'] ?? 0)) ?>" class="btn-filter clear">
                                Ver todas as atividades <i class="fas fa-arrow-right ms-1"></i>
                            </a>
                        </div>
                    <?php endif; ?>
                <?php else: ?>
                    <div class="empty-state py-4">
                        <i class="fas fa-history fa-3x mb-3"></i>
                        <h5>Nenhuma atividade registada</h5>
                        <p class="text-muted">Suas ações aparecerão aqui</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Alterar Foto -->
<div class="modal fade modal-custom" id="photoModal" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form action="<?= site_url('admin/users/update-photo') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-camera me-2"></i>
                        Alterar Foto de Perfil
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="text-center mb-4">
                        <div id="photoPreviewContainer" style="display: none;">
                            <img id="photoPreview" src="#" alt="Preview" class="rounded-circle preview-image">
                        </div>
                        <div id="photoPlaceholder" class="profile-avatar-placeholder mx-auto">
                            <?= strtoupper(substr($user['first_name'] ?? '', 0, 1) . substr($user['last_name'] ?? '', 0, 1)) ?>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="photo" class="form-label-ci">
                            <i class="fas fa-image"></i> Selecionar nova foto
                        </label>
                        <input type="file" class="form-input-ci" id="photo" name="photo" accept="image/*" required>
                        <div class="form-hint">
                            <i class="fas fa-info-circle"></i>
                            Formatos: JPG, PNG. Máx: 2MB
                        </div>
                    </div>
                    
                    <div class="alert-ci info">
                        <i class="fas fa-lightbulb me-2"></i>
                        Para melhores resultados, use uma imagem quadrada de pelo menos 200x200 pixels.
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn-filter clear" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn-filter apply" id="submitPhoto">
                        <i class="fas fa-upload me-2"></i>Alterar Foto
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Alterar Senha -->
<div class="modal fade modal-custom" id="passwordModal" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form action="<?= site_url('admin/users/change-password') ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="modal-header" style="background: var(--warning);">
                    <h5 class="modal-title">
                        <i class="fas fa-key me-2"></i>
                        Alterar Senha
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="current_password" class="form-label-ci">
                            <i class="fas fa-lock"></i> Senha Atual
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                            <input type="password" class="form-input-ci" id="current_password" 
                                   name="current_password" required>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="new_password" class="form-label-ci">
                            <i class="fas fa-lock"></i> Nova Senha
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-key"></i></span>
                            <input type="password" class="form-input-ci" id="new_password" 
                                   name="new_password" minlength="6" required>
                        </div>
                        <div class="form-hint">
                            <i class="fas fa-info-circle"></i>
                            Mínimo de 6 caracteres
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label-ci">
                            <i class="fas fa-lock"></i> Confirmar Nova Senha
                        </label>
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
                            <input type="password" class="form-input-ci" id="confirm_password" 
                                   name="confirm_password" minlength="6" required>
                        </div>
                    </div>
                    
                    <div id="passwordMatchMessage" class="alert-ci info" style="display: none;">
                        <i class="fas fa-check-circle me-2"></i>
                        As senhas coincidem
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn-filter clear" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn-filter apply" id="submitPassword" disabled>
                        <i class="fas fa-save me-2"></i>Alterar Senha
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
// Preview foto antes do upload
document.getElementById('photo').addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        // Verificar tamanho (2MB)
        if (file.size > 2 * 1024 * 1024) {
            alert('A imagem não pode ter mais de 2MB');
            this.value = '';
            return;
        }
        
        // Verificar tipo
        if (!file.type.match('image.*')) {
            alert('Por favor, selecione uma imagem válida');
            this.value = '';
            return;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            document.getElementById('photoPlaceholder').style.display = 'none';
            document.getElementById('photoPreviewContainer').style.display = 'block';
            document.getElementById('photoPreview').src = e.target.result;
        }
        reader.readAsDataURL(file);
    }
});

// Validar confirmação de senha
document.getElementById('confirm_password').addEventListener('keyup', function() {
    const password = document.getElementById('new_password').value;
    const confirm = this.value;
    const message = document.getElementById('passwordMatchMessage');
    const submitBtn = document.getElementById('submitPassword');
    
    if (password && confirm) {
        if (password === confirm) {
            message.className = 'alert-ci success';
            message.innerHTML = '<i class="fas fa-check-circle me-2"></i>As senhas coincidem';
            message.style.display = 'block';
            submitBtn.disabled = false;
        } else {
            message.className = 'alert-ci danger';
            message.innerHTML = '<i class="fas fa-exclamation-circle me-2"></i>As senhas não coincidem';
            message.style.display = 'block';
            submitBtn.disabled = true;
        }
    } else {
        message.style.display = 'none';
        submitBtn.disabled = true;
    }
});

// Validar força da senha
document.getElementById('new_password').addEventListener('keyup', function() {
    const password = this.value;
    const confirm = document.getElementById('confirm_password').value;
    
    if (password.length < 6) {
        this.setCustomValidity('A senha deve ter pelo menos 6 caracteres');
    } else {
        this.setCustomValidity('');
    }
    
    // Trigger confirm validation
    if (confirm) {
        document.getElementById('confirm_password').dispatchEvent(new Event('keyup'));
    }
});

// Reset modal ao fechar
document.getElementById('passwordModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('new_password').value = '';
    document.getElementById('confirm_password').value = '';
    document.getElementById('current_password').value = '';
    document.getElementById('passwordMatchMessage').style.display = 'none';
    document.getElementById('submitPassword').disabled = true;
});

document.getElementById('photoModal').addEventListener('hidden.bs.modal', function() {
    document.getElementById('photo').value = '';
    document.getElementById('photoPlaceholder').style.display = 'block';
    document.getElementById('photoPreviewContainer').style.display = 'none';
});
</script>

<?php
// Função time_elapsed_string
if (!function_exists('time_elapsed_string')) {
    function time_elapsed_string($datetime, $full = false) {
        if (empty($datetime)) return '';
        
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);
        
        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;
        
        $string = [
            'y' => 'ano',
            'm' => 'mês',
            'w' => 'semana',
            'd' => 'dia',
            'h' => 'hora',
            'i' => 'minuto',
            's' => 'segundo'
        ];
        
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }
        
        if (!$full) {
            $string = array_slice($string, 0, 1);
        }
        
        return $string ? 'há ' . implode(', ', $string) : 'agora mesmo';
    }
}
?>
<?= $this->endSection() ?>

<style>
/* Profile Avatar */
.profile-avatar {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid var(--border);
    transition: transform 0.3s;
}

.profile-avatar:hover {
    transform: scale(1.05);
}

.profile-avatar-placeholder {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    background: var(--accent);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 3rem;
    font-weight: 600;
    margin: 0 auto;
    border: 3px solid var(--border);
    box-shadow: var(--shadow-sm);
}

/* Info Groups */
.info-group {
    margin-bottom: 1rem;
}

.info-label {
    font-size: 0.7rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    color: var(--text-muted);
    display: block;
    margin-bottom: 0.25rem;
}

.info-value {
    font-size: 0.95rem;
    color: var(--text-primary);
    font-weight: 500;
}

.info-value a {
    color: var(--accent);
    text-decoration: none;
}

.info-value a:hover {
    text-decoration: underline;
}

/* Activity Timeline */
.activity-timeline {
    padding: 0.5rem 0;
}

.activity-item {
    display: flex;
    align-items: flex-start;
    gap: 1rem;
    padding: 0.75rem 1.25rem;
    border-bottom: 1px solid var(--border);
    transition: background 0.18s;
}

.activity-item:last-child {
    border-bottom: none;
}

.activity-item:hover {
    background: rgba(59,127,232,0.02);
}

.activity-icon {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 0.8rem;
}

.activity-icon.success { background: rgba(22,168,125,0.1); color: var(--success); }
.activity-icon.info { background: rgba(59,127,232,0.1); color: var(--accent); }
.activity-icon.warning { background: rgba(232,160,32,0.1); color: var(--warning); }
.activity-icon.danger { background: rgba(232,70,70,0.1); color: var(--danger); }
.activity-icon.primary { background: rgba(27,43,75,0.1); color: var(--primary); }
.activity-icon.secondary { background: rgba(107,122,153,0.1); color: var(--text-secondary); }
.activity-icon.purple { background: rgba(155,93,229,0.1); color: #9B5DE5; }

.activity-content {
    flex: 1;
    min-width: 0;
}

.activity-title {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.15rem;
}

.activity-desc {
    font-size: 0.8rem;
    color: var(--text-secondary);
    margin-bottom: 0.35rem;
}

.activity-meta {
    display: flex;
    gap: 1rem;
    font-size: 0.65rem;
    color: var(--text-muted);
}

.activity-meta span {
    display: flex;
    align-items: center;
    gap: 0.25rem;
}

.activity-meta i {
    font-size: 0.6rem;
}

/* Preview Image */
.preview-image {
    width: 120px;
    height: 120px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid var(--border);
    margin: 0 auto;
}

/* Responsive */
@media (max-width: 768px) {
    .activity-meta {
        flex-direction: column;
        gap: 0.25rem;
    }
}
</style>