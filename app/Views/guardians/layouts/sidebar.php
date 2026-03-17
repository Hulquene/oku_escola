<?php
$currentUrl = service('uri')->getPath();
$userName = session()->get('name') ?? 'Encarregado';
$userType = 'Encarregado de Educação';
?>
<!-- Sidebar -->
<div class="sidebar" id="sidebar">
    <!-- Sidebar Header -->
    <div class="sidebar-header">
        <div class="d-flex align-items-center gap-2">
            <div class="sidebar-logo-placeholder">
                <i class="fas fa-user-tie"></i>
            </div>
            <div class="sidebar-brand-text">
                <div class="sidebar-title"><?= $userName ?></div>
                <span class="sidebar-subtitle"><?= $userType ?></span>
            </div>
        </div>
    </div>
    
    <!-- Sidebar Navigation -->
    <ul class="sidebar-nav">
        <!-- Dashboard -->
        <li class="nav-item">
            <a href="<?= site_url('guardians/dashboard') ?>" 
               class="nav-link <?= strpos($currentUrl, 'guardians/dashboard') !== false ? 'active' : '' ?>">
                <span class="nav-icon"><i class="fas fa-tachometer-alt"></i></span>
                <span class="nav-label">Dashboard</span>
            </a>
        </li>
        
        <!-- Meus Alunos -->
        <li class="nav-item">
            <a href="<?= site_url('guardians/students') ?>" 
               class="nav-link <?= strpos($currentUrl, 'guardians/students') !== false ? 'active' : '' ?>">
                <span class="nav-icon"><i class="fas fa-users"></i></span>
                <span class="nav-label">Meus Alunos</span>
                <?php
                // Contar alunos associados
                $guardianId = getGuardianIdFromUser();
                if ($guardianId) {
                    $db = db_connect();
                    $studentCount = $db->table('tbl_student_guardians')
                        ->where('guardian_id', $guardianId)
                        ->countAllResults();
                    if ($studentCount > 0): ?>
                        <span class="nav-badge"><?= $studentCount ?></span>
                    <?php endif;
                } ?>
            </a>
        </li>
        
        <!-- Notificações -->
        <li class="nav-item">
            <a href="<?= site_url('guardians/notifications') ?>" 
               class="nav-link <?= strpos($currentUrl, 'guardians/notifications') !== false ? 'active' : '' ?>">
                <span class="nav-icon"><i class="fas fa-bell"></i></span>
                <span class="nav-label">Notificações</span>
                <?php
                // Contar notificações não lidas
                $unreadNotifications = $db->table('tbl_notifications')
                    ->where('user_id', currentUserId())
                    ->where('is_read', 0)
                    ->countAllResults();
                if ($unreadNotifications > 0): ?>
                    <span class="nav-badge warning"><?= $unreadNotifications ?></span>
                <?php endif; ?>
            </a>
        </li>
        
        <!-- Documentos -->
        <li class="nav-item">
            <a href="<?= site_url('guardians/documents') ?>" 
               class="nav-link <?= strpos($currentUrl, 'guardians/documents') !== false ? 'active' : '' ?>">
                <span class="nav-icon"><i class="fas fa-file-alt"></i></span>
                <span class="nav-label">Documentos</span>
            </a>
        </li>
        
        <!-- Financeiro com Submenu -->
        <li class="nav-item">
            <a href="#" class="nav-link" data-bs-toggle="collapse" data-bs-target="#financeSubmenu">
                <span class="nav-icon"><i class="fas fa-money-bill"></i></span>
                <span class="nav-label">Financeiro</span>
                <span class="nav-arrow"><i class="fas fa-chevron-right"></i></span>
            </a>
            <div class="collapse <?= strpos($currentUrl, 'guardians/fees') !== false ? 'show' : '' ?>" id="financeSubmenu">
                <ul class="submenu">
                    <li>
                        <a href="<?= site_url('guardians/fees') ?>" 
                           class="<?= $currentUrl == 'guardians/fees' ? 'active' : '' ?>">
                            <i class="fas fa-credit-card"></i>
                            <span>Propinas</span>
                        </a>
                    </li>
                    <li>
                        <a href="<?= site_url('guardians/fees/history') ?>" 
                           class="<?= strpos($currentUrl, 'guardians/fees/history') !== false ? 'active' : '' ?>">
                            <i class="fas fa-history"></i>
                            <span>Histórico</span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>
        
        <!-- Perfil -->
        <li class="nav-item">
            <a href="<?= site_url('guardians/profile') ?>" 
               class="nav-link <?= strpos($currentUrl, 'guardians/profile') !== false ? 'active' : '' ?>">
                <span class="nav-icon"><i class="fas fa-user-circle"></i></span>
                <span class="nav-label">Meu Perfil</span>
            </a>
        </li>
    </ul>
    
    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
        <div class="sidebar-footer-info">
            <div class="footer-chip">
                <i class="fas fa-calendar-alt"></i>
                <span><?= date('d/m/Y') ?></span>
            </div>
        </div>
        <a href="<?= site_url('guardians/auth/logout') ?>" class="btn-logout">
            <i class="fas fa-sign-out-alt"></i>
            <span>Sair</span>
        </a>
    </div>
</div>