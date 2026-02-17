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
                        2
                    </span>
                </button>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><h6 class="dropdown-header">Notificações</h6></li>
                    <li><a class="dropdown-item" href="#">
                        <i class="fas fa-calendar text-info"></i> Reunião de professores
                        <small class="text-muted d-block">hoje às 14h</small>
                    </a></li>
                    <li><a class="dropdown-item" href="#">
                        <i class="fas fa-pencil-alt text-warning"></i> Prazo para lançamento de notas
                        <small class="text-muted d-block">termina amanhã</small>
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-center" href="#">Ver todas</a></li>
                </ul>
            </div>
            
            <!-- User Menu -->
            <div class="dropdown">
                <a href="#" class="user-info text-decoration-none" data-bs-toggle="dropdown">
                    <?php if (session()->get('photo')): ?>
                        <img src="<?= base_url('uploads/users/' . session()->get('photo')) ?>" alt="User">
                    <?php else: ?>
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode(session()->get('name')) ?>&background=28a745&color=fff" 
                             alt="User">
                    <?php endif; ?>
                    <div class="d-none d-md-block">
                        <div class="user-name"><?= session()->get('name') ?></div>
                        <div class="user-role">Professor</div>
                    </div>
                </a>
                <ul class="dropdown-menu dropdown-menu-end">
                    <li><a class="dropdown-item" href="<?= site_url('teachers/profile') ?>">
                        <i class="fas fa-user-circle"></i> Meu Perfil
                    </a></li>
                    <li><hr class="dropdown-divider"></li>
                    <li><a class="dropdown-item text-danger" href="<?= site_url('teachers/auth/logout') ?>">
                        <i class="fas fa-sign-out-alt"></i> Sair
                    </a></li>
                </ul>
            </div>
        </div>
    </div>
</nav>