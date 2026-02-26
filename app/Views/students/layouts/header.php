<?php
// app/Views/students/layouts/header.php

// Carregar notificações usando helpers
$unreadNotifications = get_unread_notifications_count();
$recentNotifications = get_recent_notifications(5);
?>

<nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm">
    <div class="container-fluid">
        <button type="button" id="sidebarCollapse" class="btn btn-link text-secondary">
            <i class="fas fa-bars fa-lg"></i>
        </button>
        
        <a class="navbar-brand ms-2" href="<?= site_url('students/dashboard') ?>">
            <img src="<?= base_url('assets/images/logo-sm.png') ?>" alt="Logo" height="30">
            <span class="d-none d-md-inline ms-2">Sistema Escolar</span>
        </a>
        
        <div class="ms-auto d-flex align-items-center">
            <!-- Notifications Dropdown -->
            <div class="dropdown me-3">
                <button class="btn btn-light position-relative" type="button" id="notificationDropdown" 
                        data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fas fa-bell"></i>
                    <?php if ($unreadNotifications > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?= $unreadNotifications > 9 ? '9+' : $unreadNotifications ?>
                        </span>
                    <?php endif; ?>
                </button>
                
                <ul class="dropdown-menu dropdown-menu-end shadow" aria-labelledby="notificationDropdown" 
                    style="width: 350px; max-height: 500px; overflow-y: auto;">
                    
                    <li class="dropdown-header bg-light py-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <h6 class="mb-0 fw-bold">Notificações</h6>
                            <?php if ($unreadNotifications > 0): ?>
                                <a href="<?= site_url('students/notifications/markAllRead') ?>" 
                                   class="small text-decoration-none">
                                    Marcar todas como lidas
                                </a>
                            <?php endif; ?>
                        </div>
                    </li>
                    
                    <?php if (!empty($recentNotifications)): ?>
                        <?php foreach ($recentNotifications as $notif): ?>
                            <li>
                                <a class="dropdown-item <?= !$notif->is_read ? 'bg-light' : '' ?>" 
                                   href="<?= site_url('students/notifications/read/' . $notif->id) ?>">
                                    <div class="d-flex align-items-start">
                                        <div class="me-3">
                                            <?php 
                                            // Usar helpers para ícone e cor
                                            $icon = $notif->icon ?? get_notification_icon($notif->type ?? 'default');
                                            $color = $notif->color ?? get_notification_color($notif->type ?? 'default');
                                            ?>
                                            <i class="fas <?= $icon ?> text-<?= $color ?> fa-lg"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="small fw-bold"><?= esc($notif->title) ?></div>
                                            <div class="small text-muted"><?= esc($notif->message) ?></div>
                                            <small class="text-muted d-block mt-1">
                                                <i class="far fa-clock me-1"></i>
                                                <?= format_notification_time($notif->created_at) ?>
                                            </small>
                                        </div>
                                        <?php if (!$notif->is_read): ?>
                                            <span class="badge bg-primary ms-2">Nova</span>
                                        <?php endif; ?>
                                    </div>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="text-center py-4">
                            <i class="fas fa-bell-slash fa-3x text-muted mb-3"></i>
                            <p class="text-muted mb-0">Nenhuma notificação</p>
                        </li>
                    <?php endif; ?>
                    
                    <li><hr class="dropdown-divider my-1"></li>
                    <li>
                        <a class="dropdown-item text-center py-2" href="<?= site_url('students/notifications') ?>">
                            <i class="fas fa-list me-1"></i> Ver todas as notificações
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- User Menu -->
            <div class="dropdown">
                <a href="#" class="text-decoration-none d-flex align-items-center" data-bs-toggle="dropdown">
                    <?php if (session()->get('photo')): ?>
                        <img src="<?= base_url('uploads/users/' . session()->get('photo')) ?>" 
                             alt="User" class="rounded-circle" width="40" height="40" style="object-fit: cover;">
                    <?php else: ?>
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode(session()->get('name')) ?>&background=0d6efd&color=fff&size=40" 
                             alt="User" class="rounded-circle">
                    <?php endif; ?>
                    <div class="d-none d-md-block ms-2">
                        <div class="fw-bold small"><?= esc(session()->get('name')) ?></div>
                        <div class="text-muted small">Aluno</div>
                    </div>
                </a>
                
                <ul class="dropdown-menu dropdown-menu-end shadow">
                    <li>
                        <a class="dropdown-item" href="<?= site_url('students/profile') ?>">
                            <i class="fas fa-user-circle me-2"></i> Meu Perfil
                        </a>
                    </li>
                    <li>
                        <a class="dropdown-item" href="<?= site_url('students/settings') ?>">
                            <i class="fas fa-cog me-2"></i> Configurações
                        </a>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-danger" href="<?= site_url('students/auth/logout') ?>">
                            <i class="fas fa-sign-out-alt me-2"></i> Sair
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<script>
// Atualizar contador de notificações via AJAX a cada 60 segundos
setInterval(function() {
    fetch('<?= site_url('students/notifications/getUnreadCount') ?>')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const badge = document.querySelector('#notificationDropdown .badge');
                if (data.count > 0) {
                    if (badge) {
                        badge.textContent = data.count > 9 ? '9+' : data.count;
                    } else {
                        const button = document.querySelector('#notificationDropdown');
                        const newBadge = document.createElement('span');
                        newBadge.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger';
                        newBadge.textContent = data.count > 9 ? '9+' : data.count;
                        button.appendChild(newBadge);
                    }
                } else {
                    if (badge) badge.remove();
                }
            }
        })
        .catch(error => console.error('Erro ao atualizar notificações:', error));
}, 60000);
</script>