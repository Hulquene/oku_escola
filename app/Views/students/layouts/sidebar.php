<nav class="sidebar">
    <div class="sidebar-header">
        <h3>Portal do Aluno</h3>
        <p><?= session()->get('name') ?></p>
    </div>
    
    <!-- Informações do Aluno -->
    <div class="student-info p-3 text-center border-bottom border-secondary">
        <?php if (session()->get('photo')): ?>
            <img src="<?= base_url('uploads/students/' . session()->get('photo')) ?>" 
                 alt="Aluno" class="rounded-circle mb-2" style="width: 70px; height: 70px; object-fit: cover;">
        <?php else: ?>
            <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center text-white mb-2"
                 style="width: 70px; height: 70px; font-size: 1.8rem;">
                <?= strtoupper(substr(session()->get('first_name'), 0, 1) . substr(session()->get('last_name'), 0, 1)) ?>
            </div>
        <?php endif; ?>
        <div class="text-white">
            <strong><?= session()->get('name') ?></strong>
            <small class="d-block text-white-50">
                <i class="fas fa-graduation-cap"></i> Aluno
                <?php 
                $classInfo = formatStudentClassInfo();
                if ($classInfo): 
                ?>
                    <br><span class="badge bg-light text-primary mt-1">Turma: <?= $classInfo ?></span>
                <?php endif; ?>
            </small>
        </div>
    </div>
    
    <ul class="components">
        <!-- Dashboard -->
        <li>
            <a href="<?= site_url('students/dashboard') ?>" class="<?= uri_string() == 'students/dashboard' ? 'active' : '' ?>">
                <i class="fas fa-tachometer-alt"></i> Dashboard
                <?php if (uri_string() == 'students/dashboard'): ?>
                    <span class="badge bg-light text-primary float-end mt-1">Atual</span>
                <?php endif; ?>
            </a>
        </li>
        
        <!-- Minhas Disciplinas -->
        <li>
            <a href="#subjectsSubmenu" data-bs-toggle="collapse" 
               class="dropdown-toggle <?= in_array(uri_string(), ['students/subjects', 'students/subjects/details']) ? 'active' : '' ?>">
                <i class="fas fa-book"></i> Minhas Disciplinas
                <?php 
                $disciplines = getStudentDisciplines();
                $totalDisciplinas = count($disciplines);
                if ($totalDisciplinas > 0): 
                ?>
                    <span class="badge bg-warning text-dark float-end"><?= $totalDisciplinas ?></span>
                <?php endif; ?>
            </a>
            <ul class="collapse list-unstyled <?= in_array(uri_string(), ['students/subjects', 'students/subjects/details']) ? 'show' : '' ?>" id="subjectsSubmenu">
                <li>
                    <a href="<?= site_url('students/subjects') ?>" class="<?= uri_string() == 'students/subjects' ? 'active' : '' ?>">
                        <i class="fas fa-list"></i> Lista de Disciplinas
                    </a>
                </li>
                
                <!-- Lista dinâmica de disciplinas -->
                <?php if (!empty($disciplines)): ?>
                    <?php foreach ($disciplines as $subject): ?>
                        <li>
                            <a href="<?= site_url('students/subjects/details/' . $subject->id) ?>" 
                               class="<?= uri_string() == 'students/subjects/details/' . $subject->id ? 'active' : '' ?>">
                                <i class="fas fa-chart-line"></i> 
                                <?= $subject->discipline_name ?> 
                                <small class="d-block text-white-50">Professor: <?= $subject->teacher_name ?></small>
                            </a>
                        </li>
                    <?php endforeach; ?>
                <?php endif; ?>
            </ul>
        </li>
        
        <!-- Notas -->
        <li>
            <a href="#gradesSubmenu" data-bs-toggle="collapse" 
               class="dropdown-toggle <?= in_array(uri_string(), ['students/grades', 'students/grades/report-card']) ? 'active' : '' ?>">
                <i class="fas fa-star"></i> Notas
            </a>
            <ul class="collapse list-unstyled <?= in_array(uri_string(), ['students/grades', 'students/grades/report-card']) ? 'show' : '' ?>" id="gradesSubmenu">
                <li>
                    <a href="<?= site_url('students/grades') ?>" class="<?= uri_string() == 'students/grades' ? 'active' : '' ?>">
                        <i class="fas fa-list"></i> Minhas Notas
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('students/grades/report-card') ?>" class="<?= uri_string() == 'students/grades/report-card' ? 'active' : '' ?>">
                        <i class="fas fa-file-alt"></i> Boletim
                    </a>
                </li>
                <li class="sidebar-divider"></li>
                <li class="sidebar-header small text-uppercase px-3 mt-2 mb-1 text-white-50">Médias</li>
                <li>
                    <a href="<?= site_url('students/grades?filter=ac1') ?>">
                        <i class="fas fa-calculator"></i> AC1
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('students/grades?filter=ac2') ?>">
                        <i class="fas fa-calculator"></i> AC2
                    </a>
                </li>
            </ul>
        </li>
        
        <!-- Exames -->
        <li>
            <a href="#examsSubmenu" data-bs-toggle="collapse" 
               class="dropdown-toggle <?= in_array(uri_string(), ['students/exams', 'students/exams/schedule', 'students/exams/results']) ? 'active' : '' ?>">
                <i class="fas fa-pencil-alt"></i> Exames
            </a>
            <ul class="collapse list-unstyled <?= in_array(uri_string(), ['students/exams', 'students/exams/schedule', 'students/exams/results']) ? 'show' : '' ?>" id="examsSubmenu">
                <li>
                    <a href="<?= site_url('students/exams') ?>" class="<?= uri_string() == 'students/exams' ? 'active' : '' ?>">
                        <i class="fas fa-list"></i> Meus Exames
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('students/exams/schedule') ?>" class="<?= uri_string() == 'students/exams/schedule' ? 'active' : '' ?>">
                        <i class="fas fa-calendar"></i> Calendário
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('students/exams/results') ?>" class="<?= uri_string() == 'students/exams/results' ? 'active' : '' ?>">
                        <i class="fas fa-star"></i> Resultados
                    </a>
                </li>
                <li class="sidebar-divider"></li>
                <li class="sidebar-header small text-uppercase px-3 mt-2 mb-1 text-white-50">Status</li>
                <li>
                    <a href="<?= site_url('students/exams?status=pending') ?>">
                        <i class="fas fa-clock"></i> Próximos Exames
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('students/exams?status=completed') ?>">
                        <i class="fas fa-check-circle"></i> Exames Realizados
                    </a>
                </li>
            </ul>
        </li>
        
        <!-- Presenças -->
        <li>
            <a href="#attendanceSubmenu" data-bs-toggle="collapse" 
               class="dropdown-toggle <?= in_array(uri_string(), ['students/attendance', 'students/attendance/history']) ? 'active' : '' ?>">
                <i class="fas fa-calendar-check"></i> Presenças
            </a>
            <ul class="collapse list-unstyled <?= in_array(uri_string(), ['students/attendance', 'students/attendance/history']) ? 'show' : '' ?>" id="attendanceSubmenu">
                <li>
                    <a href="<?= site_url('students/attendance') ?>" class="<?= uri_string() == 'students/attendance' ? 'active' : '' ?>">
                        <i class="fas fa-calendar-day"></i> Presenças Mensais
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('students/attendance/history') ?>" class="<?= uri_string() == 'students/attendance/history' ? 'active' : '' ?>">
                        <i class="fas fa-history"></i> Histórico
                    </a>
                </li>
            </ul>
        </li>
        
        <!-- Propinas -->
        <li>
            <a href="#feesSubmenu" data-bs-toggle="collapse" 
               class="dropdown-toggle <?= in_array(uri_string(), ['students/fees', 'students/fees/history', 'students/fees/receipts']) ? 'active' : '' ?>">
                <i class="fas fa-money-bill"></i> Propinas
            </a>
            <ul class="collapse list-unstyled <?= in_array(uri_string(), ['students/fees', 'students/fees/history', 'students/fees/receipts']) ? 'show' : '' ?>" id="feesSubmenu">
                <li>
                    <a href="<?= site_url('students/fees') ?>" class="<?= uri_string() == 'students/fees' ? 'active' : '' ?>">
                        <i class="fas fa-list"></i> Minhas Propinas
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('students/fees/history') ?>" class="<?= uri_string() == 'students/fees/history' ? 'active' : '' ?>">
                        <i class="fas fa-history"></i> Histórico de Pagamentos
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('students/fees/receipts') ?>" class="<?= uri_string() == 'students/fees/receipts' ? 'active' : '' ?>">
                        <i class="fas fa-file-invoice"></i> Meus Recibos
                    </a>
                </li>
            </ul>
        </li>
        
        <!-- Separador -->
        <li class="sidebar-divider"></li>
        
        <!-- Estatísticas Rápidas (visíveis apenas no dashboard) -->
        <?php if (uri_string() == 'students/dashboard'): ?>
        <li class="sidebar-header small text-uppercase px-3 mt-2 mb-1 text-white-50">Resumo Acadêmico</li>
        <li class="px-3 mb-2">
            <div class="small text-white-50">
                <i class="fas fa-book"></i> Média Geral: 
                <span class="float-end text-white fw-bold"><?= getStudentAverageGrade() ?></span>
            </div>
        </li>
        <li class="px-3 mb-2">
            <div class="small text-white-50">
                <i class="fas fa-calendar-check"></i> Presença: 
                <span class="float-end text-white fw-bold"><?= getStudentAttendancePercentage() ?></span>
            </div>
        </li>
        <li class="px-3 mb-2">
            <div class="small text-white-50">
                <i class="fas fa-pencil-alt"></i> Próximo Exame: 
                <span class="float-end text-white fw-bold"><?= getNextStudentExam() ?></span>
            </div>
        </li>
        <li class="px-3 mb-2">
            <div class="small text-white-50">
                <i class="fas fa-money-bill"></i> Propinas: 
                <span class="float-end text-white fw-bold"><?= getStudentPendingFeesCount() ?></span>
            </div>
        </li>
        <?php endif; ?>
    </ul>
    
    <div class="sidebar-footer p-3">
        <div class="small text-white-50">
            <i class="fas fa-calendar"></i> <?= date('Y') ?>
            <?php if (session()->has('academic_year')): ?>
                <br><i class="fas fa-database"></i> Ano Letivo: <?= session()->get('academic_year') ?>
            <?php endif; ?>
            <?php if (session()->has('enrollment_date')): ?>
                <br><i class="fas fa-clock"></i> Matrícula: <?= date('d/m/Y', strtotime(session()->get('enrollment_date'))) ?>
            <?php endif; ?>
        </div>
        
        <!-- Logout Button -->
        <div class="mt-3">
            <a href="<?= site_url('students/auth/logout') ?>" class="btn btn-outline-light btn-sm w-100" 
               onclick="return confirm('Tem certeza que deseja sair?')">
                <i class="fas fa-sign-out-alt"></i> Sair
            </a>
        </div>
    </div>
</nav>