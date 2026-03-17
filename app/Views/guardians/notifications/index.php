<?= $this->extend('guardians/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="ci-page-header">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-bell me-2" style="color: var(--warning);"></i>Notificações</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('guardians/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Notificações</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <?php if ($unread_count > 0): ?>
                <a href="<?= site_url('guardians/notifications/mark-all-read') ?>" class="hdr-btn success">
                    <i class="fas fa-check-double me-1"></i> Marcar todas como lidas
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Lista de Notificações -->
<div class="ci-card">
    <div class="ci-card-header">
        <div class="ci-card-title">
            <i class="fas fa-list"></i>
            <span>Central de Notificações</span>
        </div>
        <span class="badge-ci <?= $unread_count > 0 ? 'warning' : 'secondary' ?>">
            <i class="fas fa-bell me-1"></i> <?= $unread_count ?> não lida(s)
        </span>
    </div>
    
    <div class="ci-card-body p0">
        <?php if (!empty($notifications)): ?>
            <div class="list-group list-group-flush">
                <?php foreach ($notifications as $notif): ?>
                    <div class="list-group-item p-3 <?= !$notif['is_read'] ? 'bg-light' : '' ?>" 
                         data-id="<?= $notif['id'] ?>">
                        <div class="d-flex align-items-start gap-3">
                            <div class="flex-shrink-0">
                                <div class="notification-icon <?= $notif['type'] ?? 'info' ?>">
                                    <?php
                                    $icon = match($notif['type'] ?? 'info') {
                                        'success' => 'fa-check-circle',
                                        'warning' => 'fa-exclamation-triangle',
                                        'danger' => 'fa-times-circle',
                                        default => 'fa-info-circle'
                                    };
                                    ?>
                                    <i class="fas <?= $icon ?>"></i>
                                </div>
                            </div>
                            
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="mb-1 fw-semibold <?= !$notif['is_read'] ? 'text-primary' : '' ?>">
                                            <?= $notif['title'] ?>
                                            <?php if (!$notif['is_read']): ?>
                                                <span class="badge-ci primary ms-2">Nova</span>
                                            <?php endif; ?>
                                        </h6>
                                        <p class="mb-1 text-muted"><?= $notif['message'] ?></p>
                                        <small class="text-muted">
                                            <i class="fas fa-clock me-1"></i>
                                            <?= date('d/m/Y H:i', strtotime($notif['created_at'])) ?>
                                        </small>
                                    </div>
                                    
                                    <div class="action-group">
                                        <?php if (!$notif['is_read']): ?>
                                            <button type="button" class="row-btn success" 
                                                    onclick="markAsRead(<?= $notif['id'] ?>)"
                                                    title="Marcar como lida">
                                                <i class="fas fa-check"></i>
                                            </button>
                                        <?php endif; ?>
                                        <button type="button" class="row-btn del" 
                                                onclick="deleteNotification(<?= $notif['id'] ?>)"
                                                title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="fas fa-bell-slash"></i>
                <h5>Nenhuma notificação</h5>
                <p class="text-muted">Você não possui notificações no momento.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function markAsRead(id) {
    $.ajax({
        url: '<?= site_url('guardians/notifications/read/') ?>' + id,
        type: 'GET',
        dataType: 'json',
        success: function() {
            location.reload();
        },
        error: function() {
            Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: 'Erro ao marcar notificação como lida.'
            });
        }
    });
}

function deleteNotification(id) {
    Swal.fire({
        title: 'Confirmar eliminação',
        text: 'Tem certeza que deseja eliminar esta notificação?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: 'var(--danger)',
        cancelButtonColor: 'var(--border)',
        confirmButtonText: '<i class="fas fa-trash me-2"></i>Eliminar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = '<?= site_url('guardians/notifications/delete/') ?>' + id;
        }
    });
}
</script>

<style>
.notification-icon {
    width: 40px;
    height: 40px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.2rem;
}

.notification-icon.info {
    background: rgba(59,127,232,0.15);
    color: var(--accent);
}

.notification-icon.success {
    background: rgba(22,168,125,0.15);
    color: var(--success);
}

.notification-icon.warning {
    background: rgba(232,160,32,0.15);
    color: var(--warning);
}

.notification-icon.danger {
    background: rgba(232,70,70,0.15);
    color: var(--danger);
}
</style>
<?= $this->endSection() ?>