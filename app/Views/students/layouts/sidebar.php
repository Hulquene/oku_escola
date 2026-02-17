<nav class="sidebar">
    <div class="sidebar-header">
        <h3>Portal do Aluno</h3>
        <p><?= session()->get('name') ?></p>
    </div>
    
    <ul class="components">
        <li>
            <a href="<?= site_url('students/dashboard') ?>" class="<?= uri_string() == 'students/dashboard' ? 'active' : '' ?>">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        </li>
        
        <li>
            <a href="#gradesSubmenu" data-bs-toggle="collapse" class="dropdown-toggle <?= in_array(uri_string(), ['students/grades', 'students/grades/report-card']) ? 'active' : '' ?>">
                <i class="fas fa-star"></i> Notas
            </a>
            <ul class="collapse list-unstyled <?= in_array(uri_string(), ['students/grades', 'students/grades/report-card']) ? 'show' : '' ?>" id="gradesSubmenu">
                <li><a href="<?= site_url('students/grades') ?>" class="<?= uri_string() == 'students/grades' ? 'active' : '' ?>">
                    <i class="fas fa-list"></i> Minhas Notas
                </a></li>
                <li><a href="<?= site_url('students/grades/report-card') ?>" class="<?= uri_string() == 'students/grades/report-card' ? 'active' : '' ?>">
                    <i class="fas fa-file-alt"></i> Boletim
                </a></li>
            </ul>
        </li>
        
        <li>
            <a href="#examsSubmenu" data-bs-toggle="collapse" class="dropdown-toggle <?= in_array(uri_string(), ['students/exams', 'students/exams/schedule', 'students/exams/results']) ? 'active' : '' ?>">
                <i class="fas fa-pencil-alt"></i> Exames
            </a>
            <ul class="collapse list-unstyled <?= in_array(uri_string(), ['students/exams', 'students/exams/schedule', 'students/exams/results']) ? 'show' : '' ?>" id="examsSubmenu">
                <li><a href="<?= site_url('students/exams') ?>" class="<?= uri_string() == 'students/exams' ? 'active' : '' ?>">
                    <i class="fas fa-list"></i> Meus Exames
                </a></li>
                <li><a href="<?= site_url('students/exams/schedule') ?>" class="<?= uri_string() == 'students/exams/schedule' ? 'active' : '' ?>">
                    <i class="fas fa-calendar"></i> Calendário
                </a></li>
                <li><a href="<?= site_url('students/exams/results') ?>" class="<?= uri_string() == 'students/exams/results' ? 'active' : '' ?>">
                    <i class="fas fa-star"></i> Resultados
                </a></li>
            </ul>
        </li>
        
        <li>
            <a href="#attendanceSubmenu" data-bs-toggle="collapse" class="dropdown-toggle <?= in_array(uri_string(), ['students/attendance', 'students/attendance/history']) ? 'active' : '' ?>">
                <i class="fas fa-calendar-check"></i> Presenças
            </a>
            <ul class="collapse list-unstyled <?= in_array(uri_string(), ['students/attendance', 'students/attendance/history']) ? 'show' : '' ?>" id="attendanceSubmenu">
                <li><a href="<?= site_url('students/attendance') ?>" class="<?= uri_string() == 'students/attendance' ? 'active' : '' ?>">
                    <i class="fas fa-calendar-day"></i> Presenças Mensais
                </a></li>
                <li><a href="<?= site_url('students/attendance/history') ?>" class="<?= uri_string() == 'students/attendance/history' ? 'active' : '' ?>">
                    <i class="fas fa-history"></i> Histórico
                </a></li>
            </ul>
        </li>
        
        <li>
            <a href="#feesSubmenu" data-bs-toggle="collapse" class="dropdown-toggle <?= in_array(uri_string(), ['students/fees', 'students/fees/history', 'students/fees/receipts']) ? 'active' : '' ?>">
                <i class="fas fa-money-bill"></i> Propinas
            </a>
            <ul class="collapse list-unstyled <?= in_array(uri_string(), ['students/fees', 'students/fees/history', 'students/fees/receipts']) ? 'show' : '' ?>" id="feesSubmenu">
                <li><a href="<?= site_url('students/fees') ?>" class="<?= uri_string() == 'students/fees' ? 'active' : '' ?>">
                    <i class="fas fa-list"></i> Minhas Propinas
                </a></li>
                <li><a href="<?= site_url('students/fees/history') ?>" class="<?= uri_string() == 'students/fees/history' ? 'active' : '' ?>">
                    <i class="fas fa-history"></i> Histórico de Pagamentos
                </a></li>
                <li><a href="<?= site_url('students/fees/receipts') ?>" class="<?= uri_string() == 'students/fees/receipts' ? 'active' : '' ?>">
                    <i class="fas fa-file-invoice"></i> Meus Recibos
                </a></li>
            </ul>
        </li>
    </ul>
    
    <div class="sidebar-footer p-3">
        <div class="small text-white-50">
            <i class="fas fa-calendar"></i> <?= date('Y') ?>
            <?php if (session()->get('academic_year')): ?>
                <br><i class="fas fa-database"></i> <?= session()->get('academic_year') ?>
            <?php endif; ?>
        </div>
    </div>
</nav>