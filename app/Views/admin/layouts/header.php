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
                    <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                        3
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><h6 class="dropdown-header">Notificações</h6></li>
                    <li><a class="dropdown-item" href="#">
                        <i class="fas fa-user-plus text-success"></i> Novo aluno matriculado
                        <small class="text-muted d-block">há 5 minutos</small>
                    </a></li>
                    <li><a class="dropdown-item" href="#">
                        <i class="fas fa-money-bill text-warning"></i> Pagamento pendente
                        <small class="text-muted d-block">há 1 hora</small>
                    </a></li>
                    <li><a class="dropdown-item" href="#">
                        <i class="fas fa-calendar text-info"></i> Reunião de professores
                        <small class="text-muted d-block">amanhã às 14h</small>
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-center" href="#">Ver todas</a></li>
                </ul>
            </div>
            
            <!-- User Menu -->
            <div class="dropdown">
                <a href="#" class="user-info text-decoration-none" data-bs-toggle="dropdown">
                    <img src="<?= session()->get('photo') ?? 'https://ui-avatars.com/api/?name='.urlencode(session()->get('name')).'&background=4e73df&color=fff' ?>" 
                         alt="User">
                    <div class="d-none d-md-block">
                        <div class="user-name"><?= session()->get('name') ?></div>
                        <div class="user-role"><?= session()->get('role') ?></div>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="<?= site_url('admin/users/profile') ?>">
                        <i class="fas fa-user-circle"></i> Meu Perfil
                    </a></li>
                    <li><a class="dropdown-item" href="<?= site_url('admin/settings') ?>">
                        <i class="fas fa-cog"></i> Configurações
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="<?= site_url('auth/logout') ?>">
                        <i class="fas fa-sign-out-alt"></i> Sair
                    </a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>