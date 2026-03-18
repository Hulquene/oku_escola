<header class="ci-topbar" id="mainHeader">
    <div class="topbar-left">
        <button type="button" id="sidebarCollapse" class="topbar-toggle" title="Menu">
            <i class="fas fa-bars"></i>
        </button>
        <div class="topbar-breadcrumb d-none d-md-flex">
            <a href="<?= site_url('admin/dashboard') ?>"><i class="fas fa-home"></i></a>
            <?php if (function_exists('current_academic_year_name') && current_academic_year_name()): ?>
                <span class="bc-sep"><i class="fas fa-chevron-right"></i></span>
                <div class="academic-year-badge">
                    <i class="fas fa-calendar-alt"></i>
                    <span><?= current_academic_year_name() ?></span>
                </div>
            <?php endif; ?>
            <?php if (isset($breadcrumb) && is_array($breadcrumb)): ?>
                <?php foreach ($breadcrumb as $item): ?>
                    <span class="bc-sep"><i class="fas fa-chevron-right"></i></span>
                    <?php if (isset($item['url']) && !empty($item['url'])): ?>
                        <a href="<?= site_url($item['url']) ?>"><?= esc($item['title']) ?></a>
                    <?php else: ?>
                        <span class="bc-active"><?= esc($item['title']) ?></span>
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
                    <a class="topbar-dd-item" href="<?= site_url('admin/students/enrollments/form-add') ?>">
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
                    <a class="topbar-dd-item" href="<?= site_url('admin/classes/classes/form-add') ?>">
                        <span class="dd-icon orange"><i class="fas fa-school"></i></span>
                        <span>Nova Turma</span>
                    </a>
                    <div class="dd-divider"></div>
                    <a class="topbar-dd-item" href="<?= site_url('admin/exams/schedules/create') ?>">
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
                                    <i class="fas <?= $notif['icon'] ?? 'fa-info-circle' ?>"></i>
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
            $user = session()->get();
            $userPhoto = $user['photo'] ?? '';
            $userName  = ($user['first_name'] ?? '') . ' ' . ($user['last_name'] ?? '');
            $userName  = trim($userName) ?: 'Usuário';
            $userRole  = $user['role_name'] ?? 'Administrador';
            $userEmail = $user['email'] ?? '';
            
            $avatarUrl = $userPhoto 
                ? base_url('uploads/users/' . $userPhoto)
                : 'https://ui-avatars.com/api/?name=' . urlencode($userName) . '&background=1B2B4B&color=fff&size=128';
        ?>
        <div class="dropdown topbar-item">
            <button class="user-btn" type="button" data-bs-toggle="dropdown">
                <img src="<?= $avatarUrl ?>" alt="<?= esc($userName) ?>" class="user-avatar" loading="lazy">
                <div class="user-info d-none d-md-block">
                    <div class="user-name"><?= esc($userName) ?></div>
                    <div class="user-role"><?= esc($userRole) ?></div>
                </div>
                <i class="fas fa-chevron-down user-chevron d-none d-md-block"></i>
            </button>
            <div class="dropdown-menu dropdown-menu-end topbar-dropdown user-dropdown">
                <div class="dropdown-inner">
                    <div class="user-dd-header">
                        <img src="<?= $avatarUrl ?>" alt="<?= esc($userName) ?>" class="user-dd-avatar" loading="lazy">
                        <div>
                            <div class="user-dd-name"><?= esc($userName) ?></div>
                            <div class="user-dd-email"><?= esc($userEmail) ?></div>
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

<style>
/* Academic Year Badge */
.academic-year-badge {
    display: inline-flex;
    align-items: center;
    gap: 0.35rem;
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: 50px;
    padding: 0.2rem 0.8rem;
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--text-secondary);
    white-space: nowrap;
}

.academic-year-badge i {
    color: var(--accent);
    font-size: 0.7rem;
}

/* Topbar Dropdowns */
.topbar-dropdown {
    width: 320px;
    padding: 0;
    border: none;
    border-radius: var(--radius);
    box-shadow: var(--shadow-lg);
    margin-top: 0.5rem !important;
}

.topbar-dropdown.user-dropdown {
    width: 280px;
}

.topbar-dropdown.search-dropdown {
    width: 360px;
}

.dropdown-inner {
    padding: 0.75rem 0;
}

.dropdown-section-label {
    font-size: 0.7rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    color: var(--text-muted);
    padding: 0 1rem 0.5rem;
    margin-bottom: 0.25rem;
}

/* Search */
.search-input-wrap {
    position: relative;
    margin: 0 1rem 0.75rem;
}

.search-icon {
    position: absolute;
    left: 12px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
    font-size: 0.8rem;
    z-index: 2;
}

.search-input {
    width: 100%;
    padding: 0.6rem 1rem 0.6rem 2.5rem;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    font-size: 0.85rem;
    font-family: var(--font);
    color: var(--text-primary);
    background: var(--surface);
    transition: all 0.18s;
}

.search-input:focus {
    outline: none;
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(59,127,232,0.12);
}

.search-submit {
    position: absolute;
    right: 8px;
    top: 50%;
    transform: translateY(-50%);
    background: var(--accent);
    border: none;
    color: #fff;
    width: 28px;
    height: 28px;
    border-radius: 6px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    cursor: pointer;
    transition: background 0.18s;
}

.search-submit:hover {
    background: var(--accent-hover);
}

.search-quick-links {
    display: flex;
    gap: 0.5rem;
    padding: 0 1rem;
    flex-wrap: wrap;
}

.search-quick-links a {
    font-size: 0.7rem;
    color: var(--text-secondary);
    text-decoration: none;
    padding: 0.2rem 0.6rem;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 50px;
    transition: all 0.18s;
}

.search-quick-links a:hover {
    background: var(--accent);
    color: #fff;
    border-color: var(--accent);
}

/* Dropdown Items */
.topbar-dd-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0.6rem 1rem;
    color: var(--text-primary);
    text-decoration: none;
    font-size: 0.85rem;
    transition: background 0.18s;
}

.topbar-dd-item:hover {
    background: rgba(59,127,232,0.05);
    color: var(--accent);
}

.topbar-dd-item.danger:hover {
    background: rgba(232,70,70,0.05);
    color: var(--danger);
}

.dd-icon {
    width: 28px;
    height: 28px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    flex-shrink: 0;
}

.dd-icon.green { background: rgba(22,168,125,0.1); color: var(--success); }
.dd-icon.blue { background: rgba(59,127,232,0.1); color: var(--accent); }
.dd-icon.purple { background: rgba(155,93,229,0.1); color: #9B5DE5; }
.dd-icon.orange { background: rgba(232,160,32,0.1); color: var(--warning); }
.dd-icon.red { background: rgba(232,70,70,0.1); color: var(--danger); }
.dd-icon.grey { background: rgba(107,122,153,0.1); color: var(--text-secondary); }
.dd-icon.teal { background: rgba(56,178,172,0.1); color: #38B2AC; }

.dd-divider {
    height: 1px;
    background: var(--border);
    margin: 0.5rem 0;
}

/* Notifications */
.notif-dropdown .dropdown-inner {
    padding: 0;
}

.dropdown-head {
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0.75rem 1rem;
    border-bottom: 1px solid var(--border);
}

.mark-all-read {
    font-size: 0.7rem;
    color: var(--accent);
    text-decoration: none;
}

.mark-all-read:hover {
    text-decoration: underline;
}

.notif-list {
    max-height: 350px;
    overflow-y: auto;
}

.notif-item {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
    padding: 0.75rem 1rem;
    text-decoration: none;
    border-bottom: 1px solid var(--border);
    transition: background 0.18s;
    position: relative;
}

.notif-item:last-child {
    border-bottom: none;
}

.notif-item:hover {
    background: rgba(59,127,232,0.03);
}

.notif-item.unread {
    background: rgba(59,127,232,0.05);
}

.notif-icon-wrap {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: #fff;
    border: 1px solid var(--border);
    font-size: 0.8rem;
    flex-shrink: 0;
}

.notif-content {
    flex: 1;
    min-width: 0;
}

.notif-title {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.15rem;
}

.notif-msg {
    font-size: 0.75rem;
    color: var(--text-secondary);
    margin-bottom: 0.25rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

.notif-time {
    font-size: 0.65rem;
    color: var(--text-muted);
}

.notif-new-dot {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: var(--accent);
    position: absolute;
    top: 12px;
    right: 12px;
}

.notif-empty {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.5rem;
    padding: 2rem 1rem;
    color: var(--text-muted);
}

.notif-empty i {
    font-size: 2rem;
    opacity: 0.3;
}

.notif-footer {
    display: block;
    text-align: center;
    padding: 0.75rem 1rem;
    border-top: 1px solid var(--border);
    font-size: 0.75rem;
    font-weight: 600;
    color: var(--accent);
    text-decoration: none;
    transition: background 0.18s;
}

.notif-footer:hover {
    background: rgba(59,127,232,0.05);
}

/* User Dropdown */
.user-dd-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    padding: 0 1rem 0.75rem;
}

.user-dd-avatar {
    width: 48px;
    height: 48px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--border);
}

.user-dd-name {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.15rem;
}

.user-dd-email {
    font-size: 0.7rem;
    color: var(--text-muted);
}

/* Responsive */
@media (max-width: 768px) {
    .topbar-breadcrumb {
        display: none !important;
    }
    
    .topbar-btn.accent span {
        display: none !important;
    }
    
    .topbar-btn.accent {
        width: 36px;
        padding: 0;
        justify-content: center;
    }
    
    .topbar-dropdown {
        width: 280px !important;
    }
}
</style>