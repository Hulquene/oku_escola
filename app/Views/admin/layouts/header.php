<nav class="navbar navbar-light bg-white fixed-top shadow-sm">
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
                    $unreadCount = $notificationModel->getUnreadCount(session()->get('user_id')); // CORRIGIDO: currentUserId() removido
                    if ($unreadCount > 0): 
                    ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                            <?= $unreadCount > 9 ? '9+' : $unreadCount ?>
                        </span>
                    <?php endif; ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end" style="width: 350px; max-height: 500px; overflow-y: auto;">
                    <li class="dropdown-header d-flex justify-content-between align-items-center bg-light py-2">
                        <h6 class="mb-0 fw-bold">Notificações</h6>
                        <?php if ($unreadCount > 0): ?>
                            <a href="<?= site_url('admin/notifications/mark-all-read') ?>" class="small text-decoration-none">
                                Marcar todas como lidas
                            </a>
                        <?php endif; ?>
                    </li>
                    
                    <?php 
                    $recentNotifications = $notificationModel->getRecent(session()->get('user_id'), 5); // CORRIGIDO
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
                                            <div class="small fw-bold"><?= esc($notif->title) ?></div>
                                            <div class="small text-muted"><?= esc($notif->message) ?></div>
                                            <small class="text-muted d-block mt-1">
                                                <?= time_elapsed_string($notif->created_at) ?>
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
                        <a class="dropdown-item text-center py-2" href="<?= site_url('admin/notifications') ?>">
                            <i class="fas fa-list me-1"></i> Ver todas as notificações
                        </a>
                    </li>
                </ul>
            </div>
            
            <!-- Quick Actions -->
            <div class="dropdown me-3 d-none d-lg-block">
                <button class="btn btn-success position-relative" type="button" data-bs-toggle="dropdown">
                    <i class="fas fa-plus me-1"></i> Rápido
                </button>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li><h6 class="dropdown-header fw-bold">Ações Rápidas</h6></li>
                    <li><a class="dropdown-item py-2" href="<?= site_url('admin/enrollments/form-add') ?>">
                        <i class="fas fa-user-plus text-success me-2"></i> Nova Matrícula
                    </a></li>
                    <li><a class="dropdown-item py-2" href="<?= site_url('admin/students/form-add') ?>">
                        <i class="fas fa-graduation-cap text-primary me-2"></i> Novo Aluno
                    </a></li>
                    <li><a class="dropdown-item py-2" href="<?= site_url('admin/teachers/form-add') ?>">
                        <i class="fas fa-chalkboard-teacher text-info me-2"></i> Novo Professor
                    </a></li>
                    <li><a class="dropdown-item py-2" href="<?= site_url('admin/classes/form-add') ?>">
                        <i class="fas fa-school text-warning me-2"></i> Nova Turma
                    </a></li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li><a class="dropdown-item py-2" href="<?= site_url('admin/exams/form-add') ?>">
                        <i class="fas fa-pencil-alt text-danger me-2"></i> Agendar Exame
                    </a></li>
                </ul>
            </div>
            
            <!-- User Menu -->
            <div class="dropdown">
                <a href="#" class="d-flex align-items-center text-decoration-none" data-bs-toggle="dropdown">
                    <?php 
                    $userPhoto = session()->get('photo');
                    $userName = session()->get('name') ?: 'Usuário';
                    ?>
                    <img src="<?= $userPhoto 
                        ? base_url('uploads/users/' . $userPhoto) 
                        : 'https://ui-avatars.com/api/?name='.urlencode($userName).'&background=4e73df&color=fff&size=128' ?>" 
                         alt="User" 
                         class="rounded-circle me-2"
                         style="width: 40px; height: 40px; object-fit: cover;">
                    <div class="d-none d-md-block text-start">
                        <div class="fw-bold small"><?= esc($userName) ?></div>
                        <div class="text-muted small"><?= session()->get('role') ?: 'Administrador' ?></div>
                    </div>
                    <i class="fas fa-chevron-down text-muted ms-1 d-none d-md-block"></i>
                </a>
                <ul class="dropdown-menu dropdown-menu-end shadow-sm">
                    <li>
                        <div class="dropdown-header d-flex align-items-center py-3">
                            <img src="<?= $userPhoto 
                                ? base_url('uploads/users/' . $userPhoto) 
                                : 'https://ui-avatars.com/api/?name='.urlencode($userName).'&background=4e73df&color=fff&size=64' ?>" 
                                 alt="User" 
                                 class="rounded-circle me-2"
                                 style="width: 40px; height: 40px; object-fit: cover;">
                            <div>
                                <div class="fw-bold"><?= esc($userName) ?></div>
                                <div class="small text-muted"><?= session()->get('email') ?></div>
                            </div>
                        </div>
                    </li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li><a class="dropdown-item py-2" href="<?= site_url('admin/users/profile') ?>">
                        <i class="fas fa-user-circle fa-fw text-primary me-2"></i> Meu Perfil
                    </a></li>
                    <li><a class="dropdown-item py-2" href="<?= site_url('admin/settings') ?>">
                        <i class="fas fa-cog fa-fw text-secondary me-2"></i> Configurações
                    </a></li>
                    <li><a class="dropdown-item py-2" href="<?= site_url('admin/help') ?>">
                        <i class="fas fa-question-circle fa-fw text-info me-2"></i> Ajuda
                    </a></li>
                    <li><hr class="dropdown-divider my-1"></li>
                    <li><a class="dropdown-item py-2 text-danger" href="<?= site_url('auth/logout') ?>" onclick="return confirm('Tem certeza que deseja sair?')">
                        <i class="fas fa-sign-out-alt fa-fw me-2"></i> Sair
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
        if (empty($datetime)) return '';
        
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

<script>
// Atualizar contador de notificações via AJAX a cada 60 segundos
setInterval(function() {
    fetch('<?= site_url('admin/notifications/getUnreadCount') ?>')
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const badge = document.querySelector('.btn-light.position-relative .badge');
                if (data.count > 0) {
                    if (badge) {
                        badge.textContent = data.count > 9 ? '9+' : data.count;
                    } else {
                        const button = document.querySelector('.btn-light.position-relative');
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

// Fechar dropdowns ao clicar fora
document.addEventListener('click', function(e) {
    const dropdowns = document.querySelectorAll('.dropdown-menu');
    dropdowns.forEach(dropdown => {
        if (!dropdown.parentElement.contains(e.target)) {
            const bsDropdown = bootstrap.Dropdown.getInstance(dropdown.parentElement.querySelector('[data-bs-toggle="dropdown"]'));
            if (bsDropdown) bsDropdown.hide();
        }
    });
});
</script>