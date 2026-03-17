<?php
$currentUrl = service('uri')->getPath();
$userName = session()->get('name') ?? 'Encarregado';
$userPhoto = session()->get('photo') ?? null;

// Contar notificações não lidas
$db = db_connect();
$unreadNotifications = $db->table('tbl_notifications')
    ->where('user_id', currentUserId())
    ->where('is_read', 0)
    ->countAllResults();
?>
<!-- Topbar -->
<header class="ci-topbar">
    <div class="topbar-left">
        <!-- Mobile Menu Toggle -->
        <button class="topbar-toggle d-lg-none" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        
        <!-- Breadcrumb -->
        <div class="topbar-breadcrumb">
            <a href="<?= site_url('guardians/dashboard') ?>">Início</a>
            <span class="bc-sep"><i class="fas fa-chevron-right"></i></span>
            <span class="bc-active"><?= $title ?? 'Dashboard' ?></span>
        </div>
    </div>
    
    <div class="topbar-right">
        <!-- Notifications -->
        <div class="dropdown">
            <button class="topbar-btn notif-btn" type="button" data-bs-toggle="dropdown">
                <i class="fas fa-bell"></i>
                <?php if ($unreadNotifications > 0): ?>
                    <span class="notif-dot"><?= $unreadNotifications ?></span>
                <?php endif; ?>
            </button>
            <div class="dropdown-menu dropdown-menu-end">
                <div class="dropdown-header d-flex justify-content-between align-items-center">
                    <span>Notificações</span>
                    <?php if ($unreadNotifications > 0): ?>
                        <a href="<?= site_url('guardians/notifications/mark-all-read') ?>" class="small text-primary">
                            Marcar todas
                        </a>
                    <?php endif; ?>
                </div>
                <?php
                $recentNotifications = $db->table('tbl_notifications')
                    ->where('user_id', currentUserId())
                    ->orderBy('created_at', 'DESC')
                    ->limit(5)
                    ->get()
                    ->getResultArray();
                
                if (!empty($recentNotifications)): ?>
                    <div class="list-group list-group-flush">
                        <?php foreach ($recentNotifications as $notif): ?>
                            <a href="<?= site_url('guardians/notifications/read/' . $notif['id']) ?>" 
                               class="list-group-item list-group-item-action <?= !$notif['is_read'] ? 'bg-light' : '' ?>">
                                <div class="d-flex gap-2">
                                    <div class="flex-shrink-0">
                                        <i class="fas fa-<?= $notif['type'] ?? 'info' ?>-circle text-<?= $notif['type'] ?? 'info' ?>"></i>
                                    </div>
                                    <div class="flex-grow-1">
                                        <small class="fw-semibold"><?= $notif['title'] ?></small>
                                        <small class="text-muted d-block"><?= date('d/m H:i', strtotime($notif['created_at'])) ?></small>
                                    </div>
                                </div>
                            </a>
                        <?php endforeach; ?>
                    </div>
                    <div class="dropdown-footer text-center">
                        <a href="<?= site_url('guardians/notifications') ?>" class="small">Ver todas</a>
                    </div>
                <?php else: ?>
                    <div class="dropdown-item text-muted text-center py-3">
                        <small>Nenhuma notificação</small>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- User Menu -->
        <div class="dropdown">
            <button class="user-btn" type="button" data-bs-toggle="dropdown">
                <?php if ($userPhoto): ?>
                    <img src="<?= base_url($userPhoto) ?>" alt="User" class="user-avatar">
                <?php else: ?>
                    <div class="user-avatar bg-primary text-white d-flex align-items-center justify-content-center">
                        <?= strtoupper(substr($userName, 0, 1)) ?>
                    </div>
                <?php endif; ?>
                <div class="user-info d-none d-md-block">
                    <div class="user-name"><?= $userName ?></div>
                    <div class="user-role">Encarregado</div>
                </div>
            </button>
            <div class="dropdown-menu dropdown-menu-end">
                <a class="dropdown-item" href="<?= site_url('guardians/profile') ?>">
                    <i class="fas fa-user-circle me-2"></i> Meu Perfil
                </a>
                <div class="dropdown-divider"></div>
                <a class="dropdown-item text-danger" href="<?= site_url('guardians/auth/logout') ?>">
                    <i class="fas fa-sign-out-alt me-2"></i> Sair
                </a>
            </div>
        </div>
    </div>
</header>