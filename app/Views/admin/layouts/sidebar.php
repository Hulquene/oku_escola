<nav class="sidebar">
    <div class="sidebar-header">
        <h3><?= session()->get('school_acronym') ?? 'Sistema Escolar' ?></h3>
        <p><?= session()->get('school_name') ?? 'Angola' ?></p>
    </div>
    
    <ul class="components">
        <!-- Dashboard -->
        <li>
            <a href="<?= site_url('admin/dashboard') ?>" class="<?= uri_string() == 'admin/dashboard' ? 'active' : '' ?>">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        </li>
        
        <!-- Académico -->
        <li>
            <a href="#academicSubmenu" data-bs-toggle="collapse" class="dropdown-toggle <?= in_array(uri_string(), ['admin/academic/years', 'admin/academic/semesters', 'admin/academic/calendar']) ? 'active' : '' ?>">
                <i class="fas fa-graduation-cap"></i> Académico
            </a>
            <ul class="collapse list-unstyled <?= in_array(uri_string(), ['admin/academic/years', 'admin/academic/semesters', 'admin/academic/calendar']) ? 'show' : '' ?>" id="academicSubmenu">
                <li><a href="<?= site_url('admin/academic/years') ?>" class="<?= uri_string() == 'admin/academic/years' ? 'active' : '' ?>"><i class="fas fa-calendar"></i> Anos Letivos</a></li>
                <li><a href="<?= site_url('admin/academic/semesters') ?>" class="<?= uri_string() == 'admin/academic/semesters' ? 'active' : '' ?>"><i class="fas fa-calendar-alt"></i> Semestres</a></li>
                <li><a href="<?= site_url('admin/academic/calendar') ?>" class="<?= uri_string() == 'admin/academic/calendar' ? 'active' : '' ?>"><i class="fas fa-calendar-check"></i> Calendário</a></li>
            </ul>
        </li>
        
        <!-- Classes/Turmas -->
        <li>
            <a href="#classesSubmenu" data-bs-toggle="collapse" class="dropdown-toggle <?= in_array(uri_string(), ['admin/classes/levels', 'admin/classes/classes', 'admin/classes/subjects', 'admin/classes/class-subjects']) ? 'active' : '' ?>">
                <i class="fas fa-school"></i> Classes/Turmas
            </a>
            <ul class="collapse list-unstyled <?= in_array(uri_string(), ['admin/classes/levels', 'admin/classes/classes', 'admin/classes/subjects', 'admin/classes/class-subjects']) ? 'show' : '' ?>" id="classesSubmenu">
                <li><a href="<?= site_url('admin/classes/levels') ?>" class="<?= uri_string() == 'admin/classes/levels' ? 'active' : '' ?>"><i class="fas fa-layer-group"></i> Níveis de Ensino</a></li>
                <li><a href="<?= site_url('admin/classes/classes') ?>" class="<?= uri_string() == 'admin/classes/classes' ? 'active' : '' ?>"><i class="fas fa-users"></i> Turmas</a></li>
                <li><a href="<?= site_url('admin/classes/subjects') ?>" class="<?= uri_string() == 'admin/classes/subjects' ? 'active' : '' ?>"><i class="fas fa-book"></i> Disciplinas</a></li>
                <li><a href="<?= site_url('admin/classes/class-subjects') ?>" class="<?= uri_string() == 'admin/classes/class-subjects' ? 'active' : '' ?>"><i class="fas fa-link"></i> Alocação de Disciplinas</a></li>
            </ul>
        </li>
        
        <!-- Alunos -->
        <li>
            <a href="#studentsSubmenu" data-bs-toggle="collapse" class="dropdown-toggle <?= in_array(uri_string(), ['admin/students', 'admin/students/enrollments', 'admin/students/guardians', 'admin/students/attendance']) ? 'active' : '' ?>">
                <i class="fas fa-user-graduate"></i> Alunos
            </a>
            <ul class="collapse list-unstyled <?= in_array(uri_string(), ['admin/students', 'admin/students/enrollments', 'admin/students/guardians', 'admin/students/attendance']) ? 'show' : '' ?>" id="studentsSubmenu">
                <li><a href="<?= site_url('admin/students') ?>" class="<?= uri_string() == 'admin/students' ? 'active' : '' ?>"><i class="fas fa-list"></i> Lista de Alunos</a></li>
                <li><a href="<?= site_url('admin/students/form-add') ?>" class="<?= uri_string() == 'admin/students/form-add' ? 'active' : '' ?>"><i class="fas fa-plus-circle"></i> Novo Aluno</a></li>
                <li><a href="<?= site_url('admin/students/enrollments') ?>" class="<?= uri_string() == 'admin/students/enrollments' ? 'active' : '' ?>"><i class="fas fa-file-signature"></i> Matrículas</a></li>
                <li><a href="<?= site_url('admin/students/guardians') ?>" class="<?= uri_string() == 'admin/students/guardians' ? 'active' : '' ?>"><i class="fas fa-users"></i> Encarregados</a></li>
                <li><a href="<?= site_url('admin/students/attendance') ?>" class="<?= uri_string() == 'admin/students/attendance' ? 'active' : '' ?>"><i class="fas fa-calendar-check"></i> Presenças</a></li>
            </ul>
        </li>
        
        <!-- Professores -->
        <li>
            <a href="#teachersSubmenu" data-bs-toggle="collapse" class="dropdown-toggle <?= in_array(uri_string(), ['admin/teachers', 'admin/teachers/assign-class']) ? 'active' : '' ?>">
                <i class="fas fa-chalkboard-teacher"></i> Professores
            </a>
            <ul class="collapse list-unstyled <?= in_array(uri_string(), ['admin/teachers', 'admin/teachers/assign-class']) ? 'show' : '' ?>" id="teachersSubmenu">
                <li><a href="<?= site_url('admin/teachers') ?>" class="<?= uri_string() == 'admin/teachers' ? 'active' : '' ?>"><i class="fas fa-list"></i> Lista de Professores</a></li>
                <li><a href="<?= site_url('admin/teachers/form-add') ?>" class="<?= uri_string() == 'admin/teachers/form-add' ? 'active' : '' ?>"><i class="fas fa-plus-circle"></i> Novo Professor</a></li>
                <li><a href="<?= site_url('admin/teachers/assign-class') ?>" class="<?= uri_string() == 'admin/teachers/assign-class' ? 'active' : '' ?>"><i class="fas fa-tasks"></i> Atribuir Turmas</a></li>
            </ul>
        </li>
        
        <!-- Propinas e Taxas -->
        <li>
            <a href="#feesSubmenu" data-bs-toggle="collapse" class="dropdown-toggle <?= in_array(uri_string(), ['admin/fees/types', 'admin/fees/structure', 'admin/fees/payments']) ? 'active' : '' ?>">
                <i class="fas fa-money-bill"></i> Propinas e Taxas
            </a>
            <ul class="collapse list-unstyled <?= in_array(uri_string(), ['admin/fees/types', 'admin/fees/structure', 'admin/fees/payments']) ? 'show' : '' ?>" id="feesSubmenu">
                <li><a href="<?= site_url('admin/fees/types') ?>" class="<?= uri_string() == 'admin/fees/types' ? 'active' : '' ?>"><i class="fas fa-tag"></i> Tipos de Taxas</a></li>
                <li><a href="<?= site_url('admin/fees/structure') ?>" class="<?= uri_string() == 'admin/fees/structure' ? 'active' : '' ?>"><i class="fas fa-table"></i> Estrutura de Taxas</a></li>
                <li><a href="<?= site_url('admin/fees/payments') ?>" class="<?= uri_string() == 'admin/fees/payments' ? 'active' : '' ?>"><i class="fas fa-credit-card"></i> Pagamentos</a></li>
            </ul>
        </li>
        
        <!-- Exames e Avaliações -->
        <li>
            <a href="#examsSubmenu" data-bs-toggle="collapse" class="dropdown-toggle <?= in_array(uri_string(), ['admin/exams/boards', 'admin/exams', 'admin/exams/results', 'admin/exams/schedule']) ? 'active' : '' ?>">
                <i class="fas fa-pencil-alt"></i> Exames e Avaliações
            </a>
            <ul class="collapse list-unstyled <?= in_array(uri_string(), ['admin/exams/boards', 'admin/exams', 'admin/exams/results', 'admin/exams/schedule']) ? 'show' : '' ?>" id="examsSubmenu">
                <li><a href="<?= site_url('admin/exams/boards') ?>" class="<?= uri_string() == 'admin/exams/boards' ? 'active' : '' ?>"><i class="fas fa-clipboard-list"></i> Tipos de Exames</a></li>
                <li><a href="<?= site_url('admin/exams') ?>" class="<?= uri_string() == 'admin/exams' ? 'active' : '' ?>"><i class="fas fa-calendar-alt"></i> Exames</a></li>
                <li><a href="<?= site_url('admin/exams/results') ?>" class="<?= uri_string() == 'admin/exams/results' ? 'active' : '' ?>"><i class="fas fa-star"></i> Resultados</a></li>
                <li><a href="<?= site_url('admin/exams/schedule') ?>" class="<?= uri_string() == 'admin/exams/schedule' ? 'active' : '' ?>"><i class="fas fa-clock"></i> Calendário de Exames</a></li>
            </ul>
        </li>
        
        <!-- Financeiro -->
        <li>
            <a href="#financialSubmenu" data-bs-toggle="collapse" class="dropdown-toggle <?= in_array(uri_string(), ['admin/financial/invoices', 'admin/financial/payments', 'admin/financial/expenses', 'admin/financial/currencies', 'admin/financial/taxes']) ? 'active' : '' ?>">
                <i class="fas fa-chart-line"></i> Financeiro
            </a>
            <ul class="collapse list-unstyled <?= in_array(uri_string(), ['admin/financial/invoices', 'admin/financial/payments', 'admin/financial/expenses', 'admin/financial/currencies', 'admin/financial/taxes']) ? 'show' : '' ?>" id="financialSubmenu">
                <li><a href="<?= site_url('admin/financial/invoices') ?>" class="<?= uri_string() == 'admin/financial/invoices' ? 'active' : '' ?>"><i class="fas fa-file-invoice"></i> Faturas</a></li>
                <li><a href="<?= site_url('admin/financial/payments') ?>" class="<?= uri_string() == 'admin/financial/payments' ? 'active' : '' ?>"><i class="fas fa-money-bill-wave"></i> Pagamentos</a></li>
                <li><a href="<?= site_url('admin/financial/expenses') ?>" class="<?= uri_string() == 'admin/financial/expenses' ? 'active' : '' ?>"><i class="fas fa-chart-pie"></i> Despesas</a></li>
                <li><a href="<?= site_url('admin/financial/currencies') ?>" class="<?= uri_string() == 'admin/financial/currencies' ? 'active' : '' ?>"><i class="fas fa-coins"></i> Moedas</a></li>
                <li><a href="<?= site_url('admin/financial/taxes') ?>" class="<?= uri_string() == 'admin/financial/taxes' ? 'active' : '' ?>"><i class="fas fa-percent"></i> Taxas</a></li>
            </ul>
        </li>
        <!-- No sidebar do admin, adicionar na área apropriada -->
        <!-- Central de Documentos -->
        <li>
            <a href="#adminDocumentsSubmenu" data-bs-toggle="collapse" 
            class="dropdown-toggle <?= in_array(uri_string(), ['admin/documents', 'admin/documents/pending', 'admin/documents/verified', 'admin/documents/requests', 'admin/documents/reports']) ? 'active' : '' ?>">
                <i class="fas fa-folder-open"></i> Central de Documentos
                <?php 
                $documentModel = new \App\Models\DocumentModel();
                $pendingDocs = $documentModel->where('is_verified', 0)->countAllResults();
                if ($pendingDocs > 0): 
                ?>
                    <span class="badge bg-warning text-dark float-end"><?= $pendingDocs ?></span>
                <?php endif; ?>
            </a>
            <ul class="collapse list-unstyled <?= in_array(uri_string(), ['admin/documents', 'admin/documents/pending', 'admin/documents/verified', 'admin/documents/requests', 'admin/documents/reports']) ? 'show' : '' ?>" id="adminDocumentsSubmenu">
                <li>
                    <a href="<?= site_url('admin/documents') ?>" class="<?= uri_string() == 'admin/documents' ? 'active' : '' ?>">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('admin/documents/pending') ?>" class="<?= uri_string() == 'admin/documents/pending' ? 'active' : '' ?>">
                        <i class="fas fa-clock"></i> Pendentes
                        <?php if ($pendingDocs > 0): ?>
                            <span class="badge bg-warning float-end"><?= $pendingDocs ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('admin/documents/verified') ?>" class="<?= uri_string() == 'admin/documents/verified' ? 'active' : '' ?>">
                        <i class="fas fa-check-circle"></i> Verificados
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('admin/documents/types') ?>" class="<?= uri_string() == 'admin/documents/types' ? 'active' : '' ?>">
                        <i class="fas fa-tags"></i> Tipos de Documentos
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('admin/documents/requestable') ?>" class="<?= uri_string() == 'admin/documents/requestable' ? 'active' : '' ?>">
                        <i class="fas fa-file-invoice"></i>Tipos de Documentos Solicitáveis
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('admin/documents/requests') ?>" class="<?= uri_string() == 'admin/documents/requests' ? 'active' : '' ?>">
                        <i class="fas fa-file-signature"></i> Solicitações
                        <?php 
                        $requestModel = new \App\Models\DocumentRequestModel();
                        $pendingRequests = $requestModel->where('status', 'pending')->countAllResults();
                        if ($pendingRequests > 0): 
                        ?>
                            <span class="badge bg-warning float-end"><?= $pendingRequests ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('admin/documents/reports') ?>" class="<?= uri_string() == 'admin/documents/reports' ? 'active' : '' ?>">
                        <i class="fas fa-chart-bar"></i> Relatórios
                    </a>
                </li>
            </ul>
        </li>
        <!-- Gerador de Documentos -->
        <li>
            <a href="#documentGeneratorSubmenu" data-bs-toggle="collapse" 
            class="dropdown-toggle <?= in_array(uri_string(), ['admin/document-generator', 'admin/document-generator/pending', 'admin/document-generator/generated', 'admin/document-generator/templates']) ? 'active' : '' ?>">
                <i class="fas fa-file-pdf"></i> Gerador de Documentos
                <?php 
                $requestModel = new \App\Models\DocumentRequestModel();
                $pendingRequests = $requestModel->where('status', 'pending')->countAllResults();
                if ($pendingRequests > 0): 
                ?>
                    <span class="badge bg-warning text-dark float-end"><?= $pendingRequests ?></span>
                <?php endif; ?>
            </a>
            <ul class="collapse list-unstyled <?= in_array(uri_string(), ['admin/document-generator', 'admin/document-generator/pending', 'admin/document-generator/generated', 'admin/document-generator/templates']) ? 'show' : '' ?>" id="documentGeneratorSubmenu">
                <li>
                    <a href="<?= site_url('admin/document-generator') ?>" class="<?= uri_string() == 'admin/document-generator' ? 'active' : '' ?>">
                        <i class="fas fa-tachometer-alt"></i> Dashboard
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('admin/document-generator/pending') ?>" class="<?= uri_string() == 'admin/document-generator/pending' ? 'active' : '' ?>">
                        <i class="fas fa-clock"></i> Pendentes
                        <?php if ($pendingRequests > 0): ?>
                            <span class="badge bg-warning float-end"><?= $pendingRequests ?></span>
                        <?php endif; ?>
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('admin/document-generator/generated') ?>" class="<?= uri_string() == 'admin/document-generator/generated' ? 'active' : '' ?>">
                        <i class="fas fa-check-circle"></i> Documentos Gerados
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('admin/document-generator/templates') ?>" class="<?= uri_string() == 'admin/document-generator/templates' ? 'active' : '' ?>">
                        <i class="fas fa-file-alt"></i> Modelos de Documentos
                    </a>
                </li>
            </ul>
        </li>
        <!-- Relatórios -->
        <li>
            <a href="#reportsSubmenu" data-bs-toggle="collapse" class="dropdown-toggle <?= in_array(uri_string(), ['admin/reports/academic', 'admin/reports/financial', 'admin/reports/students', 'admin/reports/attendance', 'admin/reports/exams', 'admin/reports/fees']) ? 'active' : '' ?>">
                <i class="fas fa-chart-bar"></i> Relatórios
            </a>
            <ul class="collapse list-unstyled <?= in_array(uri_string(), ['admin/reports/academic', 'admin/reports/financial', 'admin/reports/students', 'admin/reports/attendance', 'admin/reports/exams', 'admin/reports/fees']) ? 'show' : '' ?>" id="reportsSubmenu">
                <li><a href="<?= site_url('admin/reports/academic') ?>" class="<?= uri_string() == 'admin/reports/academic' ? 'active' : '' ?>"><i class="fas fa-graduation-cap"></i> Académico</a></li>
                <li><a href="<?= site_url('admin/reports/financial') ?>" class="<?= uri_string() == 'admin/reports/financial' ? 'active' : '' ?>"><i class="fas fa-chart-line"></i> Financeiro</a></li>
                <li><a href="<?= site_url('admin/reports/students') ?>" class="<?= uri_string() == 'admin/reports/students' ? 'active' : '' ?>"><i class="fas fa-users"></i> Alunos</a></li>
                <li><a href="<?= site_url('admin/reports/attendance') ?>" class="<?= uri_string() == 'admin/reports/attendance' ? 'active' : '' ?>"><i class="fas fa-calendar-check"></i> Presenças</a></li>
                <li><a href="<?= site_url('admin/reports/exams') ?>" class="<?= uri_string() == 'admin/reports/exams' ? 'active' : '' ?>"><i class="fas fa-pencil-alt"></i> Exames</a></li>
                <li><a href="<?= site_url('admin/reports/fees') ?>" class="<?= uri_string() == 'admin/reports/fees' ? 'active' : '' ?>"><i class="fas fa-money-bill"></i> Propinas</a></li>
            </ul>
        </li>
        
        <!-- CONFIGURAÇÕES COMPLETAS -->
        <li>
            <a href="#settingsSubmenu" data-bs-toggle="collapse" class="dropdown-toggle <?= in_array(uri_string(), [
                'admin/school-settings', 
                'admin/users', 
                'admin/roles', 
                'admin/settings/general',
                'admin/settings/email',
                'admin/settings/payment',
                'admin/settings/permissions'
            ]) ? 'active' : '' ?>">
                <i class="fas fa-cog"></i> Configurações
            </a>
            <ul class="collapse list-unstyled <?= in_array(uri_string(), [
                'admin/school-settings', 
                'admin/users', 
                'admin/roles', 
                'admin/settings/general',
                'admin/settings/email',
                'admin/settings/payment',
                'admin/settings/permissions'
            ]) ? 'show' : '' ?>" id="settingsSubmenu">
                
                <!-- Configurações da Escola (já existente) -->
                <li><a href="<?= site_url('admin/school-settings') ?>" class="<?= uri_string() == 'admin/school-settings' ? 'active' : '' ?>">
                    <i class="fas fa-school"></i> Configurações da Escola
                </a></li>
                
                <!-- Utilizadores (já existente) -->
                <li><a href="<?= site_url('admin/users') ?>" class="<?= uri_string() == 'admin/users' ? 'active' : '' ?>">
                    <i class="fas fa-users-cog"></i> Utilizadores
                </a></li>
                
                <!-- Perfis de Acesso (já existente) -->
                <li><a href="<?= site_url('admin/roles') ?>" class="<?= uri_string() == 'admin/roles' ? 'active' : '' ?>">
                    <i class="fas fa-shield-alt"></i> Perfis de Acesso
                </a></li>
                
                <!-- SEPARADOR -->
                <li class="sidebar-divider"></li>
                <li class="sidebar-header small text-uppercase px-3 mt-2 mb-1 text-white-50">Configurações do Sistema</li>
                
                <!-- Configurações Gerais -->
                <li><a href="<?= site_url('admin/settings/general') ?>" class="<?= uri_string() == 'admin/settings/general' ? 'active' : '' ?>">
                    <i class="fas fa-sliders-h"></i> Configurações Gerais
                </a></li>
                
                <!-- Configurações de Email (ADICIONADO) -->
                <li><a href="<?= site_url('admin/settings/email') ?>" class="<?= uri_string() == 'admin/settings/email' ? 'active' : '' ?>">
                    <i class="fas fa-envelope"></i> Configurações de Email
                </a></li>
                
                <!-- Configurações de Pagamento (ADICIONADO) -->
                <li><a href="<?= site_url('admin/settings/payment') ?>" class="<?= uri_string() == 'admin/settings/payment' ? 'active' : '' ?>">
                    <i class="fas fa-money-bill-wave"></i> Configurações de Pagamento
                </a></li>
                
                <!-- Permissões (ADICIONADO) -->
                <li><a href="<?= site_url('admin/settings/permissions') ?>" class="<?= uri_string() == 'admin/settings/permissions' ? 'active' : '' ?>">
                    <i class="fas fa-key"></i> Permissões
                </a></li>
            </ul>
        </li>
        
       <!-- Ferramentas -->
        <li>
            <a href="#toolsSubmenu" data-bs-toggle="collapse" class="dropdown-toggle <?= in_array(uri_string(), ['admin/tools/logs']) ? 'active' : '' ?>">
                <i class="fas fa-tools"></i> Ferramentas
            </a>
            <ul class="collapse list-unstyled <?= in_array(uri_string(), ['admin/tools/logs']) ? 'show' : '' ?>" id="toolsSubmenu">
                <li><a href="<?= site_url('admin/tools/logs') ?>" class="<?= uri_string() == 'admin/tools/logs' ? 'active' : '' ?>">
                    <i class="fas fa-history"></i> Logs do Sistema
                </a></li>
                <li><a href="<?= site_url('admin/backup') ?>"><i class="fas fa-database"></i> Backup</a></li>
                <li><a href="<?= site_url('admin/cache/clear') ?>" onclick="return confirm('Limpar cache do sistema?')">
                    <i class="fas fa-eraser"></i> Limpar Cache
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
        
        <!-- Logout Button -->
        <div class="mt-3">
            <a href="<?= site_url('auth/logout') ?>" class="btn btn-outline-light btn-sm w-100" onclick="return confirm('Tem certeza que deseja sair?')">
                <i class="fas fa-sign-out-alt"></i> Sair
            </a>
        </div>
    </div>
</nav>