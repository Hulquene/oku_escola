<nav class="sidebar">
    <div class="sidebar-header">
        <h3>Área do Professor</h3>
        <p><?= session()->get('name') ?></p>
    </div>
    
    <!-- Informações do Professor -->
    <div class="teacher-info p-3 text-center border-bottom border-secondary">
        <?php if (session()->get('photo')): ?>
            <img src="<?= base_url('uploads/users/' . session()->get('photo')) ?>" 
                 alt="Professor" class="rounded-circle mb-2" style="width: 70px; height: 70px; object-fit: cover;">
        <?php else: ?>
            <div class="bg-success rounded-circle d-inline-flex align-items-center justify-content-center text-white mb-2"
                 style="width: 70px; height: 70px; font-size: 1.8rem;">
                <?= strtoupper(substr(session()->get('first_name'), 0, 1) . substr(session()->get('last_name'), 0, 1)) ?>
            </div>
        <?php endif; ?>
        <div class="text-white">
            <strong><?= session()->get('name') ?></strong>
            <small class="d-block text-white-50">
                <i class="fas fa-chalkboard-teacher"></i> Professor
            </small>
        </div>
    </div>
    
    <ul class="components">
        <!-- Dashboard -->
        <li>
            <a href="<?= site_url('teachers/dashboard') ?>" class="<?= uri_string() == 'teachers/dashboard' ? 'active' : '' ?>">
                <i class="fas fa-tachometer-alt"></i> Dashboard
                <?php if (uri_string() == 'teachers/dashboard'): ?>
                    <span class="badge bg-light text-success float-end mt-1">Atual</span>
                <?php endif; ?>
            </a>
        </li>
        
        <!-- Perfil -->
        <li>
            <a href="<?= site_url('teachers/profile') ?>" class="<?= uri_string() == 'teachers/profile' ? 'active' : '' ?>">
                <i class="fas fa-user"></i> Meu Perfil
                <?php if (uri_string() == 'teachers/profile'): ?>
                    <span class="badge bg-light text-success float-end mt-1">Atual</span>
                <?php endif; ?>
            </a>
        </li>
        
        <!-- Minhas Turmas -->
        <li>
            <a href="#classesSubmenu" data-bs-toggle="collapse" 
               class="dropdown-toggle <?= in_array(uri_string(), ['teachers/classes', 'teachers/classes/students']) ? 'active' : '' ?>">
                <i class="fas fa-school"></i> Minhas Turmas
            </a>
            <ul class="collapse list-unstyled <?= in_array(uri_string(), ['teachers/classes', 'teachers/classes/students']) ? 'show' : '' ?>" id="classesSubmenu">
                <li>
                    <a href="<?= site_url('teachers/classes') ?>" class="<?= uri_string() == 'teachers/classes' ? 'active' : '' ?>">
                        <i class="fas fa-list"></i> Lista de Turmas
                    </a>
                </li>
            </ul>
        </li>
        
        <!-- Avaliações/Exames -->
        <li>
            <a href="#examsSubmenu" data-bs-toggle="collapse" 
               class="dropdown-toggle <?= in_array(uri_string(), ['teachers/exams', 'teachers/exams/form-add', 'teachers/exams/grade']) ? 'active' : '' ?>">
                <i class="fas fa-pencil-alt"></i> Avaliações e Notas
            </a>
            <ul class="collapse list-unstyled <?= in_array(uri_string(), ['teachers/exams', 'teachers/exams/form-add', 'teachers/exams/grade']) ? 'show' : '' ?>" id="examsSubmenu">
                <li>
                    <a href="<?= site_url('teachers/exams') ?>" class="<?= uri_string() == 'teachers/exams' ? 'active' : '' ?>">
                        <i class="fas fa-list"></i> Meus Exames
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('teachers/exams/form-add') ?>" class="<?= uri_string() == 'teachers/exams/form-add' ? 'active' : '' ?>">
                        <i class="fas fa-plus-circle"></i> Novo Exame
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('teachers/exams?status=completed') ?>">
                        <i class="fas fa-check-circle"></i> Exames Realizados
                    </a>
                </li>
            </ul>
        </li>
        
        <!-- Notas (Avaliações Contínuas) -->
        <li>
            <a href="#gradesSubmenu" data-bs-toggle="collapse" 
               class="dropdown-toggle <?= in_array(uri_string(), ['teachers/grades', 'teachers/grades/report', 'teachers/grades/report/select']) ? 'active' : '' ?>">
                <i class="fas fa-star"></i> Notas
            </a>
            <ul class="collapse list-unstyled <?= in_array(uri_string(), ['teachers/grades', 'teachers/grades/report', 'teachers/grades/report/select']) ? 'show' : '' ?>" id="gradesSubmenu">
                <li>
                    <a href="<?= site_url('teachers/grades') ?>" class="<?= uri_string() == 'teachers/grades' ? 'active' : '' ?>">
                        <i class="fas fa-edit"></i> Lançar Notas (AC)
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('teachers/grades/report/select') ?>" class="<?= uri_string() == 'teachers/grades/report/select' ? 'active' : '' ?>">
                        <i class="fas fa-chart-bar"></i> Relatório de Notas
                    </a>
                </li>
            </ul>
        </li>
        
        <!-- Presenças -->
        <li>
            <a href="#attendanceSubmenu" data-bs-toggle="collapse" 
               class="dropdown-toggle <?= in_array(uri_string(), ['teachers/attendance', 'teachers/attendance/report']) ? 'active' : '' ?>">
                <i class="fas fa-calendar-check"></i> Presenças
            </a>
            <ul class="collapse list-unstyled <?= in_array(uri_string(), ['teachers/attendance', 'teachers/attendance/report']) ? 'show' : '' ?>" id="attendanceSubmenu">
                <li>
                    <a href="<?= site_url('teachers/attendance') ?>" class="<?= uri_string() == 'teachers/attendance' ? 'active' : '' ?>">
                        <i class="fas fa-check-circle"></i> Registrar Presenças
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('teachers/attendance/report') ?>" class="<?= uri_string() == 'teachers/attendance/report' ? 'active' : '' ?>">
                        <i class="fas fa-chart-line"></i> Relatório de Presenças
                    </a>
                </li>
            </ul>
        </li>
        <!-- Mini Pauta -->
        <li class="nav-item <?= uri_string() == 'teachers/mini-grade-sheet' || strpos(uri_string(), 'teachers/mini-grade-sheet') === 0 ? 'active' : '' ?>">
            <a class="nav-link" href="<?= site_url('teachers/mini-grade-sheet') ?>">
                <i class="fas fa-file-alt"></i>
                <span>Mini Pautas</span>
            </a>
        </li>
        <!-- Documentos -->
        <li>
            <a href="#documentsSubmenu" data-bs-toggle="collapse" 
               class="dropdown-toggle <?= in_array(uri_string(), ['teachers/documents', 'teachers/documents/requests', 'teachers/documents/archive']) ? 'active' : '' ?>">
                <i class="fas fa-folder-open"></i> Documentos
            </a>
            <ul class="collapse list-unstyled <?= in_array(uri_string(), ['teachers/documents', 'teachers/documents/requests', 'teachers/documents/archive']) ? 'show' : '' ?>" id="documentsSubmenu">
                <li>
                    <a href="<?= site_url('teachers/documents') ?>" class="<?= uri_string() == 'teachers/documents' ? 'active' : '' ?>">
                        <i class="fas fa-upload"></i> Meus Documentos
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('teachers/documents/requests') ?>" class="<?= uri_string() == 'teachers/documents/requests' ? 'active' : '' ?>">
                        <i class="fas fa-file-signature"></i> Solicitar Documento
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('teachers/documents/archive') ?>" class="<?= uri_string() == 'teachers/documents/archive' ? 'active' : '' ?>">
                        <i class="fas fa-archive"></i> Arquivo
                    </a>
                </li>
            </ul>
        </li>
        
        <!-- Separador -->
        <li class="sidebar-divider"></li>
        
        <!-- Informações do Sistema -->
        <li class="sidebar-header small text-uppercase px-3 mt-2 mb-1 text-white-50">Sistema</li>
        <li class="px-3 mb-2">
            <div class="small text-white-50">
                <i class="fas fa-calendar"></i> <?= date('Y') ?>
                <span class="float-end text-white"><?= date('d/m/Y') ?></span>
            </div>
        </li>
    </ul>
    
    <div class="sidebar-footer p-3">
        <!-- Logout Button -->
        <div class="mt-3">
            <a href="<?= site_url('teachers/auth/logout') ?>" class="btn btn-outline-light btn-sm w-100" 
               onclick="return confirm('Tem certeza que deseja sair?')">
                <i class="fas fa-sign-out-alt"></i> Sair
            </a>
        </div>
    </div>
</nav>