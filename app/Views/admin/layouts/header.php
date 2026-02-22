<nav class="navbar navbar-light bg-white">
    <div class="container-fluid">
        <!-- Sidebar Toggle -->
        <button type="button" id="sidebarCollapse" class="btn btn-link text-dark me-3">
            <i class="fas fa-bars fa-lg"></i>
        </button>
        
        <!-- Breadcrumb Dinâmico -->
        <div class="navbar-text d-none d-md-block">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb bg-transparent p-0 mb-0">
                    <li class="breadcrumb-item">
                        <a href="<?= site_url('admin/dashboard') ?>" class="text-decoration-none">
                            <i class="fas fa-home"></i> Início
                        </a>
                    </li>
                    <?php if (isset($breadcrumb)): ?>
                        <?php foreach ($breadcrumb as $item): ?>
                            <?php if (isset($item['url'])): ?>
                                <li class="breadcrumb-item">
                                    <a href="<?= site_url($item['url']) ?>" class="text-decoration-none">
                                        <?= $item['title'] ?>
                                    </a>
                                </li>
                            <?php else: ?>
                                <li class="breadcrumb-item active" aria-current="page">
                                    <?= $item['title'] ?>
                                </li>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </ol>
            </nav>
        </div>
        
        <div class="ms-auto d-flex align-items-center">
            <!-- Search Bar (opcional) -->
            <div class="dropdown me-3 d-none d-md-block">
                <button class="btn btn-light border rounded-pill px-3" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-search text-muted me-2"></i>
                    <span class="text-muted">Pesquisar...</span>
                </button>
                <div class="dropdown-menu dropdown-menu-end p-3" style="width: 300px;">
                    <form action="<?= site_url('admin/search') ?>" method="get">
                        <div class="input-group">
                            <input type="text" class="form-control" name="q" placeholder="Pesquisar...">
                            <button class="btn btn-primary" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                        <div class="mt-2 small">
                            <a href="#" class="text-decoration-none me-2">Alunos</a>
                            <a href="#" class="text-decoration-none me-2">Professores</a>
                            <a href="#" class="text-decoration-none">Turmas</a>
                        </div>
                    </form>
                </div>
            </div>
            
            <!-- Notifications -->
            <div class="dropdown me-3">
                <button class="btn btn-light position-relative" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-bell"></i>
                    <?php 
                    $notificationModel = new \App\Models\NotificationModel();
                    $unreadCount = $notificationModel->getUnreadCount(currentUserId());
                    if ($unreadCount > 0): 
                    ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?= $unreadCount > 9 ? '9+' : $unreadCount ?>
                        </span>
                    <?php endif; ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" style="width: 350px;">
                    <li class="dropdown-header d-flex justify-content-between align-items-center">
                        <h6 class="mb-0">Notificações</h6>
                        <?php if ($unreadCount > 0): ?>
                            <a href="<?= site_url('admin/notifications/mark-all-read') ?>" class="small text-decoration-none">
                                Marcar todas como lidas
                            </a>
                        <?php endif; ?>
                    </li>
                    
                    <?php 
                    $recentNotifications = $notificationModel->getRecent(currentUserId(), 5);
                    if (!empty($recentNotifications)): 
                    ?>
                        <?php foreach ($recentNotifications as $notif): ?>
                            <li>
                                <a class="dropdown-item <?= !$notif->is_read ? 'bg-light' : '' ?>" 
                                   href="<?= site_url('admin/notifications/read/' . $notif->id) ?>">
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
                    
                    <?php if ($unreadCount > 0): ?>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item text-center" href="<?= site_url('admin/notifications') ?>">
                                Ver todas as notificações
                            </a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <!-- Quick Actions -->
            <div class="dropdown me-3 d-none d-lg-block">
                <button class="btn btn-success position-relative" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-plus"></i> Rápido
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><h6 class="dropdown-header">Ações Rápidas</h6></li>
                    <li><a class="dropdown-item" href="<?= site_url('admin/enrollments/form-add') ?>">
                        <i class="fas fa-user-plus text-success"></i> Nova Matrícula
                    </a></li>
                    <li><a class="dropdown-item" href="<?= site_url('admin/students/form-add') ?>">
                        <i class="fas fa-graduation-cap text-primary"></i> Novo Aluno
                    </a></li>
                    <li><a class="dropdown-item" href="<?= site_url('admin/teachers/form-add') ?>">
                        <i class="fas fa-chalkboard-teacher text-info"></i> Novo Professor
                    </a></li>
                    <li><a class="dropdown-item" href="<?= site_url('admin/classes/form-add') ?>">
                        <i class="fas fa-school text-warning"></i> Nova Turma
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="<?= site_url('admin/exams/form-add') ?>">
                        <i class="fas fa-pencil-alt text-danger"></i> Agendar Exame
                    </a></li>
                </ul>
            </div>
            
            <!-- User Menu -->
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none" data-bs-toggle="dropdown">
                    <img src="<?= session()->get('photo') 
                        ? base_url('uploads/users/' . session()->get('photo')) 
                        : 'https://ui-avatars.com/api/?name='.urlencode(session()->get('name')).'&background=4e73df&color=fff&size=128' ?>" 
                         alt="User" 
                         class="rounded-circle me-2"
                         style="width: 40px; height: 40px; object-fit: cover;">
                    <div class="d-none d-md-block text-start">
                        <div class="fw-bold small"><?= session()->get('name') ?></div>
                        <div class="text-muted small"><?= session()->get('role') ?></div>
                    </div>
                    <i class="fas fa-chevron-down text-muted ms-1 d-none d-md-block"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li>
                        <div class="dropdown-header d-flex align-items-center">
                            <img src="<?= session()->get('photo') 
                                ? base_url('uploads/users/' . session()->get('photo')) 
                                : 'https://ui-avatars.com/api/?name='.urlencode(session()->get('name')).'&background=4e73df&color=fff&size=128' ?>" 
                                 alt="User" 
                                 class="rounded-circle me-2"
                                 style="width: 32px; height: 32px; object-fit: cover;">
                            <div>
                                <div class="fw-bold"><?= session()->get('name') ?></div>
                                <div class="small text-muted"><?= session()->get('email') ?></div>
                            </div>
                        </div>
                    </li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item" href="<?= site_url('admin/users/profile') ?>">
                        <i class="fas fa-user-circle fa-fw text-primary"></i> Meu Perfil
                    </a></li>
                    <li><a class="dropdown-item" href="<?= site_url('admin/settings') ?>">
                        <i class="fas fa-cog fa-fw text-secondary"></i> Configurações
                    </a></li>
                    <li><a class="dropdown-item" href="<?= site_url('admin/help') ?>">
                        <i class="fas fa-question-circle fa-fw text-info"></i> Ajuda
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="<?= site_url('auth/logout') ?>">
                        <i class="fas fa-sign-out-alt fa-fw"></i> Sair
                    </a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>

<!-- Helper Functions -->
<?php
if (!function_exists('time_elapsed_string')) {
    function time_elapsed_string($datetime, $full = false) {
        $now = new DateTime;
        $ago = new DateTime($datetime);
        $diff = $now->diff($ago);
        
        $diff->w = floor($diff->d / 7);
        $diff->d -= $diff->w * 7;
        
        $string = array(
            'y' => 'ano',
            'm' => 'mês',
            'w' => 'semana',
            'd' => 'dia',
            'h' => 'hora',
            'i' => 'minuto',
            's' => 'segundo',
        );
        
        foreach ($string as $k => &$v) {
            if ($diff->$k) {
                $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
            } else {
                unset($string[$k]);
            }
        }
        
        if (!$full) $string = array_slice($string, 0, 1);
        return $string ? 'há ' . implode(', ', $string) : 'agora mesmo';
    }
}
?>

<style>
/* Navbar Styles */
.navbar {
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    padding: 0.75rem 1.5rem;
    z-index: 1030;
}

#sidebarCollapse {
    transition: transform 0.2s;
}

#sidebarCollapse:hover {
    transform: scale(1.1);
}

/* Dropdown Animations */
.dropdown-menu {
    animation: slideIn 0.2s ease-out;
    border: none;
    box-shadow: 0 5px 20px rgba(0,0,0,0.15);
}

@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Notifications */
.dropdown-item.bg-light {
    background-color: #f8f9fa !important;
}

.dropdown-item:active {
    background-color: #4e73df;
}

/* Quick Actions */
.btn-success .fas {
    transition: transform 0.2s;
}

.btn-success:hover .fas {
    transform: rotate(90deg);
}

/* Breadcrumb */
.breadcrumb {
    font-size: 0.9rem;
}

.breadcrumb-item a {
    color: #6c757d;
}

.breadcrumb-item.active {
    color: #4e73df;
}

/* User Menu */
.user-info {
    transition: opacity 0.2s;
}

.user-info:hover {
    opacity: 0.8;
}

/* Responsive */
@media (max-width: 768px) {
    .navbar {
        padding: 0.5rem 1rem;
    }
    
    #sidebarCollapse {
        margin-right: 10px;
    }
}
</style>