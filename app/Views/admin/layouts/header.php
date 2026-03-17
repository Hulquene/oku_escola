<header class="ci-topbar" id="mainHeader">
    <div class="topbar-left">
        <button type="button" id="sidebarCollapse" class="topbar-toggle" title="Menu">
            <i class="fas fa-bars"></i>
        </button>
        <div class="topbar-breadcrumb d-none d-md-flex">
            <a href="<?= site_url('admin/dashboard') ?>"><i class="fas fa-home"></i></a>
            <?php if (current_academic_year()): ?>
                <div class="academic-year-badge">
                    <i class="fas fa-calendar-alt"></i>
                    <span><?= current_academic_year_name() ?></span>
                </div>
            <?php endif; ?>
            <?php if (isset($breadcrumb)): ?>
                <?php foreach ($breadcrumb as $item): ?>
                    <span class="bc-sep"><i class="fas fa-chevron-right"></i></span>
                    <?php if (isset($item['url'])): ?>
                        <a href="<?= site_url($item['url']) ?>"><?= $item['title'] ?></a>
                    <?php else: ?>
                        <span class="bc-active"><?= $item['title'] ?></span>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>

    <div class="topbar-right">

        <!-- Search -->
        <div class="dropdown topbar-item d-none d-md-block">
            <button class="topbar-btn" type="button" data-bs-toggle="dropdown" title="Pesquisar">
                <i class="fas fa-search"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end topbar-dropdown search-dropdown">
                <div class="dropdown-inner">
                    <p class="dropdown-section-label">Pesquisa Rápida</p>
                    <form action="<?= site_url('admin/search') ?>" method="get">
                        <div class="search-input-wrap">
                            <i class="fas fa-search search-icon"></i>
                            <input type="text" class="search-input" name="q" placeholder="Pesquisar alunos, professores...">
                            <button type="submit" class="search-submit"><i class="fas fa-arrow-right"></i></button>
                        </div>
                        <div class="search-quick-links">
                            <a href="<?= site_url('admin/students') ?>">Alunos</a>
                            <a href="<?= site_url('admin/teachers') ?>">Professores</a>
                            <a href="<?= site_url('admin/classes/classes') ?>">Turmas</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div class="dropdown topbar-item d-none d-lg-block">
            <button class="topbar-btn accent" type="button" data-bs-toggle="dropdown" title="Ações Rápidas">
                <i class="fas fa-plus"></i>
                <span class="d-none d-xl-inline ms-1">Novo</span>
            </button>
            <div class="dropdown-menu dropdown-menu-end topbar-dropdown">
                <div class="dropdown-inner">
                    <p class="dropdown-section-label">Ações Rápidas</p>
                    <a class="topbar-dd-item" href="<?= site_url('admin/students/enrollments') ?>">
                        <span class="dd-icon green"><i class="fas fa-user-plus"></i></span>
                        <span>Nova Matrícula</span>
                    </a>
                    <a class="topbar-dd-item" href="<?= site_url('admin/students/form-add') ?>">
                        <span class="dd-icon blue"><i class="fas fa-graduation-cap"></i></span>
                        <span>Novo Aluno</span>
                    </a>
                    <a class="topbar-dd-item" href="<?= site_url('admin/teachers/form-add') ?>">
                        <span class="dd-icon purple"><i class="fas fa-chalkboard-teacher"></i></span>
                        <span>Novo Professor</span>
                    </a>
                    <a class="topbar-dd-item" href="<?= site_url('admin/classes/form-add') ?>">
                        <span class="dd-icon orange"><i class="fas fa-school"></i></span>
                        <span>Nova Turma</span>
                    </a>
                    <div class="dd-divider"></div>
                    <a class="topbar-dd-item" href="<?= site_url('admin/exams/form-add') ?>">
                        <span class="dd-icon red"><i class="fas fa-pencil-alt"></i></span>
                        <span>Agendar Exame</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- Notifications -->
        <?php
            $notificationModel = new \App\Models\NotificationModel();
            $unreadCount       = $notificationModel->getUnreadCount(session()->get('user_id'));
            $recentNotifications = $notificationModel->getRecent(session()->get('user_id'), 5);
        ?>
        <div class="dropdown topbar-item">
            <button class="topbar-btn notif-btn" type="button" data-bs-toggle="dropdown" title="Notificações">
                <i class="fas fa-bell"></i>
                <?php if ($unreadCount > 0): ?>
                    <span class="notif-dot"><?= $unreadCount > 9 ? '9+' : $unreadCount ?></span>
                <?php endif; ?>
            </button>
            <div class="dropdown-menu dropdown-menu-end topbar-dropdown notif-dropdown">
                <div class="dropdown-inner">
                    <div class="dropdown-head">
                        <span class="dropdown-section-label mb-0">Notificações</span>
                        <?php if ($unreadCount > 0): ?>
                            <a href="<?= site_url('admin/notifications/mark-all-read') ?>" class="mark-all-read">
                                Marcar todas como lidas
                            </a>
                        <?php endif; ?>
                    </div>
                    <div class="notif-list">
                        <?php if (!empty($recentNotifications)): ?>
                            <?php foreach ($recentNotifications as $notif): ?>
                            <a class="notif-item <?= !$notif['is_read'] ? 'unread' : '' ?>"
                               href="<?= site_url('admin/notifications/read/' . $notif['id']) ?>">
                                <span class="notif-icon-wrap text-<?= $notif['color'] ?? 'primary' ?>">
                                    <i class="fas <?= $notif->icon ?? 'fa-info-circle' ?>"></i>
                                </span>
                                <div class="notif-content">
                                    <div class="notif-title"><?= esc($notif['title']) ?></div>
                                    <div class="notif-msg"><?= esc($notif['message']) ?></div>
                                    <div class="notif-time"><?= time_elapsed_string($notif['created_at']) ?></div>
                                </div>
                                <?php if (!$notif['is_read']): ?>
                                    <span class="notif-new-dot"></span>
                                <?php endif; ?>
                            </a>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <div class="notif-empty">
                                <i class="fas fa-bell-slash"></i>
                                <span>Nenhuma notificação</span>
                            </div>
                        <?php endif; ?>
                    </div>
                    <a href="<?= site_url('admin/notifications') ?>" class="notif-footer">
                        Ver todas as notificações <i class="fas fa-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>

        <!-- User Menu -->
        <?php
            $userPhoto = session()->get('photo');
            $userName  = session()->get('name') ?: 'Usuário';
            $userRole  = session()->get('role') ?: 'Administrador';
            $avatarUrl = $userPhoto
                ? base_url('uploads/users/' . $userPhoto)
                : 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=1B2B4B&color=fff&size=128';
        ?>
        <div class="dropdown topbar-item">
            <button class="user-btn" type="button" data-bs-toggle="dropdown">
                <img src="<?= $avatarUrl ?>" alt="<?= esc($userName) ?>" class="user-avatar">
                <div class="user-info d-none d-md-block">
                    <div class="user-name"><?= esc($userName) ?></div>
                    <div class="user-role"><?= esc($userRole) ?></div>
                </div>
                <i class="fas fa-chevron-down user-chevron d-none d-md-block"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end topbar-dropdown user-dropdown">
                <div class="dropdown-inner">
                    <div class="user-dd-header">
                        <img src="<?= $avatarUrl ?>" alt="<?= esc($userName) ?>" class="user-dd-avatar">
                        <div>
                            <div class="user-dd-name"><?= esc($userName) ?></div>
                            <div class="user-dd-email"><?= esc(session()->get('email') ?? '') ?></div>
                        </div>
                    </div>
                    <div class="dd-divider"></div>
                    <a class="topbar-dd-item" href="<?= site_url('admin/users/profile') ?>">
                        <span class="dd-icon blue"><i class="fas fa-user-circle"></i></span>
                        <span>Meu Perfil</span>
                    </a>
                    <a class="topbar-dd-item" href="<?= site_url('admin/settings') ?>">
                        <span class="dd-icon grey"><i class="fas fa-cog"></i></span>
                        <span>Configurações</span>
                    </a>
                    <a class="topbar-dd-item" href="<?= site_url('admin/help') ?>">
                        <span class="dd-icon teal"><i class="fas fa-question-circle"></i></span>
                        <span>Ajuda</span>
                    </a>
                    <div class="dd-divider"></div>
                    <a class="topbar-dd-item danger" href="<?= site_url('auth/logout') ?>"
                       onclick="return confirm('Tem certeza que deseja sair?')">
                        <span class="dd-icon red"><i class="fas fa-sign-out-alt"></i></span>
                        <span>Sair do Sistema</span>
                    </a>
                </div>
            </div>
        </div>

    </div>
</header>

<?php
if (!function_exists('time_elapsed_string')) {
    function time_elapsed_string($datetime, $full = false) {
        if (empty($datetime)) return '';
        $now  = new DateTime;
        $ago  = new DateTime($datetime);
        $diff = $now->diff($ago);
        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;
        $string = ['y'=>'ano','m'=>'mês','w'=>'semana','d'=>'dia','h'=>'hora','i'=>'minuto','s'=>'segundo'];
        foreach ($string as $k => &$v) {
            if ($diff->$k) $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            else unset($string[$k]);
        }
        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? 'há ' . implode(', ', $string) : 'agora mesmo';
    }
}
?>