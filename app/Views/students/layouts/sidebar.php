<nav class="sidebar">
    <div class="sidebar-header">
        <h3>Portal do Aluno</h3>
        <p><?= currentUserName() ?></p>
    </div>
    
    <!-- Informações do Aluno -->
    <div class="student-info p-3 text-center border-bottom border-secondary">
        <?php 
        $photo = currentUserPhoto();
        if ($photo): 
        ?>
            <img src="<?= base_url('uploads/students/' . $photo) ?>" 
                 alt="Aluno" class="rounded-circle mb-2" style="width: 70px; height: 70px; object-fit: cover;">
        <?php else: ?>
            <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center text-white mb-2"
                 style="width: 70px; height: 70px; font-size: 1.8rem;">
                <?= strtoupper(substr(session()->get('first_name'), 0, 1) . substr(session()->get('last_name'), 0, 1)) ?>
            </div>
        <?php endif; ?>
        <div class="text-white">
            <strong><?= currentUserName() ?></strong>
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
            <a href="<?= site_url('students/dashboard') ?>" 
               class="<?= uri_string() == 'students/dashboard' ? 'active' : '' ?>">
                <i class="fas fa-tachometer-alt"></i> Dashboard
                <?php if (uri_string() == 'students/dashboard'): ?>
                    <span class="badge bg-light text-primary float-end mt-1">Atual</span>
                <?php endif; ?>
            </a>
        </li>
        
        <!-- Perfil -->
        <li>
            <a href="<?= site_url('students/profile') ?>" 
               class="<?= uri_string() == 'students/profile' ? 'active' : '' ?>">
                <i class="fas fa-user"></i> Meu Perfil
                <?php if (uri_string() == 'students/profile'): ?>
                    <span class="badge bg-light text-primary float-end mt-1">Atual</span>
                <?php endif; ?>
            </a>
        </li>
        
        <!-- Minhas Disciplinas -->
        <li>
            <a href="<?= site_url('students/subjects') ?>" 
               class="<?= uri_string() == 'students/subjects' ? 'active' : '' ?>">
                <i class="fas fa-book"></i> Minhas Disciplinas
                <?php 
                $disciplines = getStudentDisciplines();
                $totalDisciplinas = count($disciplines);
                if ($totalDisciplinas > 0): 
                ?>
                    <span class="badge bg-warning text-dark float-end"><?= $totalDisciplinas ?></span>
                <?php endif; ?>
            </a>
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
        <!-- No sidebar do aluno, adicionar após Propinas -->
        <!-- Documentos -->
        <li>
            <a href="#documentsSubmenu" data-bs-toggle="collapse" 
            class="dropdown-toggle <?= in_array(uri_string(), ['students/documents', 'students/documents/requests', 'students/documents/archive']) ? 'active' : '' ?>">
                <i class="fas fa-folder-open"></i> Documentos
                <?php 
                $documentModel = new \App\Models\DocumentModel();
                $pendingDocs = $documentModel->where('user_id', currentUserId())
                    ->where('user_type', 'student')
                    ->where('is_verified', 0)
                    ->countAllResults();
                if ($pendingDocs > 0): 
                ?>
                    <span class="badge bg-warning text-dark float-end"><?= $pendingDocs ?></span>
                <?php endif; ?>
            </a>
            <ul class="collapse list-unstyled <?= in_array(uri_string(), ['students/documents', 'students/documents/requests', 'students/documents/archive']) ? 'show' : '' ?>" id="documentsSubmenu">
                <li>
                    <a href="<?= site_url('students/documents') ?>" class="<?= uri_string() == 'students/documents' ? 'active' : '' ?>">
                        <i class="fas fa-upload"></i> Meus Documentos
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('students/documents/requests') ?>" class="<?= uri_string() == 'students/documents/requests' ? 'active' : '' ?>">
                        <i class="fas fa-file-signature"></i> Solicitar Documento
                        <?php 
                        $requestModel = new \App\Models\DocumentRequestModel();
                        $pendingRequests = $requestModel->where('user_id', currentUserId())
                            ->where('user_type', 'student')
                            ->where('status', 'pending')
                            ->countAllResults();
                        if ($pendingRequests > 0): 
                        ?>
                            <span class="badge bg-warning text-dark float-end"><?= $pendingRequests ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('students/documents/archive') ?>" class="<?= uri_string() == 'students/documents/archive' ? 'active' : '' ?>">
                        <i class="fas fa-archive"></i> Arquivo
                    </a>
                </li>
            </ul>
        </li>
        <!-- Separador -->
        <li class="sidebar-divider"></li>
        
        <!-- Links Rápidos Adicionais -->
        <li class="sidebar-header small text-uppercase px-3 mt-2 mb-1 text-white-50">Links Rápidos</li>
        
        <li>
            <a href="<?= site_url('students/profile/academic-history') ?>" 
               class="<?= uri_string() == 'students/profile/academic-history' ? 'active' : '' ?>">
                <i class="fas fa-history"></i> Histórico Acadêmico
            </a>
        </li>
        
        <li>
            <a href="<?= site_url('students/profile/guardians') ?>" 
               class="<?= uri_string() == 'students/profile/guardians' ? 'active' : '' ?>">
                <i class="fas fa-users"></i> Meus Encarregados
            </a>
        </li>
        
        <!-- Estatísticas Rápidas (visíveis apenas no dashboard) -->
        <?php if (uri_string() == 'students/dashboard'): ?>
        <li class="sidebar-header small text-uppercase px-3 mt-3 mb-1 text-white-50">Resumo Acadêmico</li>
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
                <i class="fas fa-money-bill"></i> Propinas Pendentes: 
                <span class="float-end text-white fw-bold"><?= getStudentPendingFeesCount() ?></span>
            </div>
        </li>
        <?php endif; ?>
    </ul>
    
    <div class="sidebar-footer p-3">
        <div class="small text-white-50">
            <i class="fas fa-calendar"></i> <?= date('Y') ?>
            <?php 
            $enrollmentId = getStudentEnrollmentId();
            if ($enrollmentId): 
            ?>
                <br><i class="fas fa-id-card"></i> Matrícula Ativa
            <?php endif; ?>
        </div>
        
        <!-- Informações do Sistema -->
        <div class="small text-white-50 mt-2">
            <i class="fas fa-code-branch"></i> v1.0.0
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