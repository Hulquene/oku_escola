<?php
// app/Views/students/notifications/index.php
?>

<?= $this->extend('students/layouts/index') ?>

<?= $this->section('content') ?>

<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3">Minhas Notificações</h1>
        <?php if ($unreadCount > 0): ?>
            <a href="<?= site_url('students/notifications/markAllRead') ?>" class="btn btn-outline-primary">
                <i class="fas fa-check-double me-2"></i>Marcar todas como lidas
            </a>
        <?php endif; ?>
    </div>
    
    <?php if (!empty($notifications)): ?>
        <div class="card shadow-sm">
            <div class="list-group list-group-flush">
                <?php foreach ($notifications as $notification): ?>
                    <div class="list-group-item list-group-item-action <?= !$notification->is_read ? 'bg-light' : '' ?>">
                        <div class="d-flex align-items-start">
                            <div class="me-3">
                                <?php 
                                $icon = $notification->icon ?? 'fa-info-circle';
                                $color = $notification->color ?? 'primary';
                                ?>
                                <i class="fas <?= $icon ?> fa-2x text-<?= $color ?>"></i>
                            </div>
                            
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-center">
                                    <h5 class="mb-1"><?= esc($notification->title) ?></h5>
                                    <small class="text-muted">
                                        <?= date('d/m/Y H:i', strtotime($notification->created_at)) ?>
                                    </small>
                                </div>
                                <p class="mb-1"><?= esc($notification->message) ?></p>
                                <small class="text-muted">
                                    <i class="far fa-clock me-1"></i>
                                    <?= time_elapsed_string($notification->created_at) ?>
                                </small>
                            </div>
                            
                            <div class="ms-3">
                                <?php if (!empty($notification->link)): ?>
                                    <a href="<?= site_url('students/notifications/read/' . $notification->id) ?>" 
                                       class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                <?php endif; ?>
                                <a href="<?= site_url('students/notifications/delete/' . $notification->id) ?>" 
                                   class="btn btn-sm btn-outline-danger"
                                   onclick="return confirm('Tem certeza que deseja remover esta notificação?')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
        
        <?php if ($pager): ?>
            <div class="mt-4">
                <?= $pager->links() ?>
            </div>
        <?php endif; ?>
        
    <?php else: ?>
        <div class="card shadow-sm">
            <div class="card-body text-center py-5">
                <i class="fas fa-bell-slash fa-4x text-muted mb-3"></i>
                <h5>Nenhuma notificação</h5>
                <p class="text-muted">Você não tem notificações no momento.</p>
            </div>
        </div>
    <?php endif; ?>
</div>

<?= $this->endSection() ?>