<nav class="navbar">
    <div class="container-fluid">
        <button type="button" id="sidebarCollapse" class="btn-toggle">
            <i class="fas fa-bars"></i>
        </button>
        
        <div class="ms-auto d-flex align-items-center">
            <!-- Notifications -->
            <div class="dropdown me-3">
                <button class="btn btn-light position-relative" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-bell"></i>
                    <?php if (isset($unreadNotifications) && $unreadNotifications > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?= $unreadNotifications > 9 ? '9+' : $unreadNotifications ?>
                        </span>
                    <?php endif; ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" style="width: 350px;">
                    <li class="dropdown-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Notificações</h6>
                        <?php if (isset($unreadNotifications) && $unreadNotifications > 0): ?>
                            <a href="<?= site_url('student/notifications/mark-all-read') ?>" class="small text-decoration-none">
                                Marcar todas como lidas
                            </a>
                        <?php endif; ?>
                    </li>
                    
                    <?php if (isset($recentNotifications) && !empty($recentNotifications)): ?>
                        <?php foreach ($recentNotifications as $notif): ?>
                            <li>
                                <a class="dropdown-item <?= !$notif->is_read ? 'bg-light' : '' ?>" 
                                   href="<?= site_url('student/notifications/read/' . $notif->id) ?>">
                                    <div class="d-flex align-items-start">
                                        <div class="me-3">
                                            <i class="fas <?= $notif->icon ?? 'fa-info-circle' ?> 
                                                  text-<?= $notif->color ?? 'primary' ?>"></i>
                                        </div>
                                        <div class="flex-grow-1">
                                            <div class="small fw-bold"><?= $notif->title ?></div>
                                            <div class="small text-muted"><?= $notif->message ?></div>
                                            <small class="text-muted d-block mt-1">
                                                <?= time_elapsed_string($notif->created_at) ?>
                                            </small>
                                        </div>
                                    </div>
                                </a>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="text-center py-3">
                            <i class="fas fa-bell-slash fa-2x text-muted mb-2"></i>
                            <p class="small text-muted mb-0">Nenhuma notificação</p>
                        </li>
                    <?php endif; ?>
                    
                    <li><hr class="dropdown-divider"></li>
                    <li>
                        <a class="dropdown-item text-center" href="<?= site_url('student/notifications') ?>">
                            Ver todas as notificações
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- User Menu -->
            <div class="dropdown">
                <a href="#" class="user-info text-decoration-none" data-bs-toggle="dropdown">
                    <?php if (session()->get('photo')): ?>
                        <img src="<?= base_url('uploads/users/' . session()->get('photo')) ?>" alt="User">
                    <?php else: ?>
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode(session()->get('name')) ?>&background=f093fb&color=fff" 
                             alt="User">
                    <?php endif; ?>
                    <div class="d-none d-md-block">
                        <div class="user-name"><?= session()->get('name') ?></div>
                        <div class="user-role">Aluno</div>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="<?= site_url('student/profile') ?>">
                        <i class="fas fa-user-circle"></i> Meu Perfil
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="<?= site_url('student/auth/logout') ?>">
                        <i class="fas fa-sign-out-alt"></i> Sair
                    </a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<?php
// Helper function (adicione no final de cada header)
if (!function_exists('time_elapsed_string')) {
    function time_elapsed_string($datetime) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);
        
        if ($diff->y > 0) return $diff->y . ' ano' . ($diff->y > 1 ? 's' : '') . ' atrás';
        if ($diff->m > 0) return $diff->m . ' mês' . ($diff->m > 1 ? 'es' : '') . ' atrás';
        if ($diff->d > 0) return $diff->d . ' dia' . ($diff->d > 1 ? 's' : '') . ' atrás';
        if ($diff->h > 0) return $diff->h . ' hora' . ($diff->h > 1 ? 's' : '') . ' atrás';
        if ($diff->i > 0) return $diff->i . ' minuto' . ($diff->i > 1 ? 's' : '') . ' atrás';
        return 'agora mesmo';
    }
}
?>