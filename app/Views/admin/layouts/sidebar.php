<nav class="sidebar" id="mainSidebar">

    <!-- Logo e Cabeçalho -->
    <div class="sidebar-header">
        <div class="d-flex align-items-center gap-2">
            <?= school_logo_html(42, 42, 'sidebar-logo') ?>
            <div class="sidebar-brand-text">
                <div class="sidebar-title"><?= setting('school_acronym') ?: 'Sistema' ?></div>
                <div class="sidebar-subtitle"><?= setting('school_name') ?: 'Escolar' ?></div>
            </div>
        </div>
    </div>

    <ul class="sidebar-nav">

        <!-- Dashboard -->
        <li class="nav-item">
            <a href="<?= site_url('admin/dashboard') ?>" class="nav-link <?= uri_string() == 'admin/dashboard' ? 'active' : '' ?>">
                <span class="nav-icon"><i class="fas fa-tachometer-alt"></i></span>
                <span class="nav-label">Dashboard</span>
            </a>
        </li>

        <!-- Académico -->
        <?php if (has_permission('settings.academic_years') || has_permission('settings.semesters') || has_permission('calendar.view')): ?>
        <?php $academicActive = in_array(uri_string(), ['admin/academic/years', 'admin/academic/semesters', 'admin/academic/calendar']); ?>
        <li class="nav-item">
            <a href="#academicSubmenu" data-bs-toggle="collapse"
               class="nav-link dropdown-toggle <?= $academicActive ? 'active' : '' ?>"
               aria-expanded="<?= $academicActive ? 'true' : 'false' ?>">
                <span class="nav-icon"><i class="fas fa-graduation-cap"></i></span>
                <span class="nav-label">Académico</span>
                <i class="fas fa-chevron-right nav-arrow"></i>
            </a>
            <ul class="collapse submenu <?= $academicActive ? 'show' : '' ?>" id="academicSubmenu">
                <?php if (has_permission('settings.academic_years')): ?>
                <li><a href="<?= site_url('admin/academic/years') ?>" class="<?= uri_string() == 'admin/academic/years' ? 'active' : '' ?>">
                    <i class="fas fa-calendar"></i> Anos Letivos
                </a></li>
                <?php endif; ?>
                <?php if (has_permission('settings.semesters')): ?>
                <li><a href="<?= site_url('admin/academic/semesters') ?>" class="<?= uri_string() == 'admin/academic/semesters' ? 'active' : '' ?>">
                    <i class="fas fa-calendar-alt"></i> Semestres
                </a></li>
                <?php endif; ?>
                <?php if (has_permission('calendar.view')): ?>
                <li><a href="<?= site_url('admin/academic/calendar') ?>" class="<?= uri_string() == 'admin/academic/calendar' ? 'active' : '' ?>">
                    <i class="fas fa-calendar-check"></i> Calendário
                </a></li>
                <?php endif; ?>
            </ul>
        </li>
        <?php endif; ?>

        <!-- Cursos -->
        <?php if (has_permission('courses.list') || has_permission('courses.create')): ?>
        <?php
            $coursesActive = in_array(uri_string(), ['admin/courses', 'admin/courses/curriculum']);
            $courseModel = new \App\Models\CourseModel();
            $totalCourses = $courseModel->where('is_active', 1)->countAllResults();
        ?>
        <li class="nav-item">
            <a href="#coursesSubmenu" data-bs-toggle="collapse"
               class="nav-link dropdown-toggle <?= $coursesActive ? 'active' : '' ?>"
               aria-expanded="<?= $coursesActive ? 'true' : 'false' ?>">
                <span class="nav-icon"><i class="fas fa-layer-group"></i></span>
                <span class="nav-label">Cursos</span>
                <?php if ($totalCourses > 0 && has_permission('courses.list')): ?>
                    <span class="nav-badge"><?= $totalCourses ?></span>
                <?php endif; ?>
                <i class="fas fa-chevron-right nav-arrow"></i>
            </a>
            <ul class="collapse submenu <?= $coursesActive ? 'show' : '' ?>" id="coursesSubmenu">
                <?php if (has_permission('courses.list')): ?>
                <li><a href="<?= site_url('admin/courses') ?>" class="<?= uri_string() == 'admin/courses' ? 'active' : '' ?>">
                    <i class="fas fa-list"></i> Lista de Cursos
                </a></li>
                <?php endif; ?>
                <?php if (has_permission('courses.create')): ?>
                <li><a href="<?= site_url('admin/courses/form-add') ?>" class="<?= uri_string() == 'admin/courses/form-add' ? 'active' : '' ?>">
                    <i class="fas fa-plus-circle"></i> Novo Curso
                </a></li>
                <?php endif; ?>
            </ul>
        </li>
        <?php endif; ?>

        <!-- Classes/Turmas -->
        <?php if (has_permission('classes.list') || has_permission('subjects.list') || has_permission('settings.grade_levels')): ?>
        <?php $classesActive = in_array(uri_string(), ['admin/classes/levels', 'admin/classes/classes', 'admin/classes/subjects', 'admin/classes/class-subjects']); ?>
        <li class="nav-item">
            <a href="#classesSubmenu" data-bs-toggle="collapse"
               class="nav-link dropdown-toggle <?= $classesActive ? 'active' : '' ?>"
               aria-expanded="<?= $classesActive ? 'true' : 'false' ?>">
                <span class="nav-icon"><i class="fas fa-school"></i></span>
                <span class="nav-label">Classes / Turmas</span>
                <i class="fas fa-chevron-right nav-arrow"></i>
            </a>
            <ul class="collapse submenu <?= $classesActive ? 'show' : '' ?>" id="classesSubmenu">
                <?php if (has_permission('settings.grade_levels')): ?>
                <li><a href="<?= site_url('admin/classes/levels') ?>" class="<?= uri_string() == 'admin/classes/levels' ? 'active' : '' ?>">
                    <i class="fas fa-layer-group"></i> Níveis de Ensino
                </a></li>
                <?php endif; ?>
                <?php if (has_permission('classes.list')): ?>
                <li><a href="<?= site_url('admin/classes/classes') ?>" class="<?= uri_string() == 'admin/classes/classes' ? 'active' : '' ?>">
                    <i class="fas fa-users"></i> Turmas
                </a></li>
                <?php endif; ?>
                <?php if (has_permission('classes.schedule')): ?>
                <li><a href="<?= site_url('admin/classes/schedule') ?>" class="<?= uri_string() == 'admin/classes/schedule' ? 'active' : '' ?>">
                    <i class="fas fa-clock"></i> Horários
                </a></li>
                <?php endif; ?>
                <?php if (has_permission('subjects.list')): ?>
                <li><a href="<?= site_url('admin/classes/subjects') ?>" class="<?= uri_string() == 'admin/classes/subjects' ? 'active' : '' ?>">
                    <i class="fas fa-book"></i> Disciplinas
                </a></li>
                <?php endif; ?>
                <?php if (has_permission('subjects.assign_to_classes')): ?>
                <li><a href="<?= site_url('admin/classes/class-subjects') ?>" class="<?= uri_string() == 'admin/classes/class-subjects' ? 'active' : '' ?>">
                    <i class="fas fa-link"></i> Alocação de Disciplinas
                </a></li>
                <?php endif; ?>
            </ul>
        </li>
        <?php endif; ?>

        <!-- Alunos -->
        <?php if (has_permission('students.list') || has_permission('enrollments.list') || has_permission('guardians.list') || has_permission('attendance.list')): ?>
        <?php $studentsActive = in_array(uri_string(), ['admin/students', 'admin/students/enrollments', 'admin/students/guardians', 'admin/students/attendance']); ?>
        <li class="nav-item">
            <a href="#studentsSubmenu" data-bs-toggle="collapse"
               class="nav-link dropdown-toggle <?= $studentsActive ? 'active' : '' ?>"
               aria-expanded="<?= $studentsActive ? 'true' : 'false' ?>">
                <span class="nav-icon"><i class="fas fa-user-graduate"></i></span>
                <span class="nav-label">Alunos</span>
                <i class="fas fa-chevron-right nav-arrow"></i>
            </a>
            <ul class="collapse submenu <?= $studentsActive ? 'show' : '' ?>" id="studentsSubmenu">
                <?php if (has_permission('students.list')): ?>
                <li><a href="<?= site_url('admin/students') ?>" class="<?= uri_string() == 'admin/students' ? 'active' : '' ?>">
                    <i class="fas fa-list"></i> Lista de Alunos
                </a></li>
                <?php endif; ?>
                <?php if (has_permission('students.create')): ?>
                <li><a href="<?= site_url('admin/students/form-add') ?>" class="<?= uri_string() == 'admin/students/form-add' ? 'active' : '' ?>">
                    <i class="fas fa-plus-circle"></i> Novo Aluno
                </a></li>
                <?php endif; ?>
                <?php if (has_permission('enrollments.list')): ?>
                <li><a href="<?= site_url('admin/students/enrollments') ?>" class="<?= uri_string() == 'admin/students/enrollments' ? 'active' : '' ?>">
                    <i class="fas fa-file-signature"></i> Matrículas
                </a></li>
                <?php endif; ?>
                <?php if (has_permission('guardians.list')): ?>
                <li><a href="<?= site_url('admin/students/guardians') ?>" class="<?= uri_string() == 'admin/students/guardians' ? 'active' : '' ?>">
                    <i class="fas fa-users"></i> Encarregados
                </a></li>
                <?php endif; ?>
                <?php if (has_permission('attendance.list')): ?>
                <li><a href="<?= site_url('admin/students/attendance') ?>" class="<?= uri_string() == 'admin/students/attendance' ? 'active' : '' ?>">
                    <i class="fas fa-calendar-check"></i> Presenças
                </a></li>
                <?php endif; ?>
            </ul>
        </li>
        <?php endif; ?>

        <!-- Professores -->
        <?php if (has_permission('teachers.list') || has_permission('teachers.create') || has_permission('teachers.assign_subjects')): ?>
        <?php $teachersActive = in_array(uri_string(), ['admin/teachers', 'admin/teachers/assign-class']); ?>
        <li class="nav-item">
            <a href="#teachersSubmenu" data-bs-toggle="collapse"
               class="nav-link dropdown-toggle <?= $teachersActive ? 'active' : '' ?>"
               aria-expanded="<?= $teachersActive ? 'true' : 'false' ?>">
                <span class="nav-icon"><i class="fas fa-chalkboard-teacher"></i></span>
                <span class="nav-label">Professores</span>
                <i class="fas fa-chevron-right nav-arrow"></i>
            </a>
            <ul class="collapse submenu <?= $teachersActive ? 'show' : '' ?>" id="teachersSubmenu">
                <?php if (has_permission('teachers.list')): ?>
                <li><a href="<?= site_url('admin/teachers') ?>" class="<?= uri_string() == 'admin/teachers' ? 'active' : '' ?>">
                    <i class="fas fa-list"></i> Lista de Professores
                </a></li>
                <?php endif; ?>
                <?php if (has_permission('teachers.create')): ?>
                <li><a href="<?= site_url('admin/teachers/form-add') ?>" class="<?= uri_string() == 'admin/teachers/form-add' ? 'active' : '' ?>">
                    <i class="fas fa-plus-circle"></i> Novo Professor
                </a></li>
                <?php endif; ?>
                <?php if (has_permission('teachers.assign_subjects')): ?>
                <li><a href="<?= site_url('admin/teachers/assign-class') ?>" class="<?= uri_string() == 'admin/teachers/assign-class' ? 'active' : '' ?>">
                    <i class="fas fa-tasks"></i> Atribuir Turmas
                </a></li>
                <?php endif; ?>
            </ul>
        </li>
        <?php endif; ?>

        <!-- Exames e Avaliações -->
        <?php
        $examsUris = ['admin/exams/periods','admin/exams/schedules','admin/exams/boards','admin/exams/results','admin/exams/weights','admin/exams/calculate','admin/exams/appeals'];
        $examsActive = in_array(uri_string(), $examsUris);
        if (has_permission('exams.list') || has_permission('exams.enter_grades') || has_permission('exam_periods.list') || has_permission('exam_schedules.list') || has_permission('results.view')):
            $examScheduleModel = new \App\Models\ExamScheduleModel();
            $pendingExams = $examScheduleModel->where('status', 'Agendado')->where('exam_date >=', date('Y-m-d'))->countAllResults();
        ?>
        <li class="nav-item">
            <a href="#examsMainSubmenu" data-bs-toggle="collapse"
               class="nav-link dropdown-toggle <?= $examsActive ? 'active' : '' ?>"
               aria-expanded="<?= $examsActive ? 'true' : 'false' ?>">
                <span class="nav-icon"><i class="fas fa-pencil-alt"></i></span>
                <span class="nav-label">Exames e Avaliações</span>
                <?php if ($pendingExams > 0 && has_permission('exams.list')): ?>
                    <span class="nav-badge warning"><?= $pendingExams ?></span>
                <?php endif; ?>
                <i class="fas fa-chevron-right nav-arrow"></i>
            </a>
            <ul class="collapse submenu <?= $examsActive ? 'show' : '' ?>" id="examsMainSubmenu">
                <?php if (has_permission('exam_periods.list')): ?>
                <li><a href="<?= site_url('admin/exams/periods') ?>" class="<?= uri_string() == 'admin/exams/periods' ? 'active' : '' ?>">
                    <i class="fas fa-calendar-alt"></i> Períodos de Exame
                </a></li>
                <?php endif; ?>
                <?php if (has_permission('exam_schedules.list')): ?>
                <li><a href="<?= site_url('admin/exams/schedules') ?>" class="<?= uri_string() == 'admin/exams/schedules' ? 'active' : '' ?>">
                    <i class="fas fa-clock"></i> Agendamentos
                </a></li>
                <?php endif; ?>
                <?php if (has_permission('exams.list')): ?>
                <li><a href="<?= site_url('admin/exams/boards') ?>" class="<?= uri_string() == 'admin/exams/boards' ? 'active' : '' ?>">
                    <i class="fas fa-clipboard-list"></i> Tipos de Exames
                </a></li>
                <li><a href="<?= site_url('admin/exams/weights') ?>" class="<?= uri_string() == 'admin/exams/weights' ? 'active' : '' ?>">
                    <i class="fas fa-weight-hanging"></i> Pesos das Avaliações
                </a></li>
                <?php endif; ?>
                <?php if (has_permission('exams.view_grades')): ?>
                <li><a href="<?= site_url('admin/exams/results') ?>" class="<?= uri_string() == 'admin/exams/results' ? 'active' : '' ?>">
                    <i class="fas fa-star"></i> Resultados de Exames
                </a></li>
                <?php endif; ?>
                <?php if (has_permission('discipline_averages.view')): ?>
                <li><a href="<?= site_url('admin/discipline-averages') ?>" class="<?= uri_string() == 'admin/discipline-averages' ? 'active' : '' ?>">
                    <i class="fas fa-chart-line"></i> Médias Disciplinares
                </a></li>
                <?php endif; ?>
                <?php if (has_permission('results.view')): ?>
                <li><a href="<?= site_url('admin/semester-results') ?>" class="<?= uri_string() == 'admin/semester-results' ? 'active' : '' ?>">
                    <i class="fas fa-calendar-check"></i> Resultados Semestrais
                </a></li>
                <?php endif; ?>
                <?php if (has_permission('discipline_averages.calculate')): ?>
                <li><a href="<?= site_url('admin/exams/calculate') ?>" class="<?= uri_string() == 'admin/exams/calculate' ? 'active' : '' ?>">
                    <i class="fas fa-calculator"></i> Calculadora de Médias
                </a></li>
                <?php endif; ?>
                <?php if (has_permission('exams.view_grades')):
                    $disciplineAvgModel = new \App\Models\DisciplineAverageModel();
                    $appealStudents = $disciplineAvgModel->where('status', 'Recurso')->countAllResults();
                ?>
                <li><a href="<?= site_url('admin/exams/appeals') ?>" class="<?= uri_string() == 'admin/exams/appeals' ? 'active' : '' ?>">
                    <i class="fas fa-exclamation-triangle"></i> Exames de Recurso
                    <?php if ($appealStudents > 0): ?>
                        <span class="sub-badge warning"><?= $appealStudents ?></span>
                    <?php endif; ?>
                </a></li>
                <?php endif; ?>
            </ul>
        </li>
        <?php endif; ?>

        <!-- Pautas Académicas -->
        <?php
        $pautasUris = ['admin/academic-records','admin/academic-records/approvals','admin/academic-records/promotion-results'];
        $pautasActive = in_array(uri_string(), $pautasUris);
        if (has_permission('reports.academic') || has_permission('exams.view_results') || has_permission('grades.view_averages') || has_permission('results.approve')):
            
            // Usar o helper em vez do model
            $pendingApprovals = count_pending_approvals();
        ?>
        <li class="nav-item">
            <a href="#pautasSubmenu" data-bs-toggle="collapse"
            class="nav-link dropdown-toggle <?= $pautasActive ? 'active' : '' ?>"
            aria-expanded="<?= $pautasActive ? 'true' : 'false' ?>">
                <span class="nav-icon"><i class="fas fa-chart-line"></i></span>
                <span class="nav-label">Pautas Académicas</span>
                <?php if ($pendingApprovals > 0): ?>
                    <span class="nav-badge warning"><?= $pendingApprovals ?></span>
                <?php endif; ?>
                <i class="fas fa-chevron-right nav-arrow"></i>
            </a>
            <ul class="collapse submenu <?= $pautasActive ? 'show' : '' ?>" id="pautasSubmenu">
                <?php if (has_permission('reports.academic') || is_admin()): ?>
                <li><a href="<?= site_url('admin/academic-records') ?>" class="<?= uri_string() == 'admin/academic-records' ? 'active' : '' ?>">
                    <i class="fas fa-list"></i> Turmas
                </a></li>
                <?php endif; ?>
                
                <?php if (has_permission('exams.view_results') || has_permission('grades.view_averages') || is_admin()): ?>
                <li><a href="<?= site_url('admin/academic-records/trimestral') ?>" class="<?= uri_string() == 'admin/academic-records/trimestral' ? 'active' : '' ?>">
                    <i class="fas fa-calendar-alt"></i> Pauta Trimestral
                </a></li>
                <?php endif; ?>
                
                <?php if (has_permission('grades.view_averages') || is_admin()): ?>
                <li><a href="<?= site_url('admin/academic-records/disciplina') ?>" class="<?= uri_string() == 'admin/academic-records/disciplina' ? 'active' : '' ?>">
                    <i class="fas fa-book-open"></i> Pauta por Disciplina
                </a></li>
                <?php endif; ?>
                
                <!-- NOVOS LINKS PARA APROVAÇÃO E PROGRESSÃO -->
                <?php if (has_permission('results.approve') || is_admin()): ?>
                <li><a href="<?= site_url('admin/academic-records/approvals') ?>" class="<?= uri_string() == 'admin/academic-records/approvals' ? 'active' : '' ?>">
                    <i class="fas fa-check-circle"></i> Processar Aprovações
                    <?php if ($pendingApprovals > 0): ?>
                        <span class="sub-badge warning"><?= $pendingApprovals ?></span>
                    <?php endif; ?>
                </a></li>
                <li><a href="<?= site_url('admin/academic-records/promotion-results') ?>" class="<?= uri_string() == 'admin/academic-records/promotion-results' ? 'active' : '' ?>">
                    <i class="fas fa-arrow-up"></i> Resultados da Progressão
                </a></li>
                <?php endif; ?>
            </ul>
        </li>
        <?php endif; ?>
        <!-- Documentos Acadêmicos - NOVO MENU -->
        <?php
        $docUris = ['admin/academic-documents','admin/academic-documents/eligible-certificates','admin/academic-documents/eligible-declarations','admin/academic-documents/history'];
        $docActive = in_array(uri_string(), $docUris);
        if (has_permission('documents.generate') || is_admin()):
        ?>
        <li class="nav-item">
            <a href="#academicDocsSubmenu" data-bs-toggle="collapse"
            class="nav-link dropdown-toggle <?= $docActive ? 'active' : '' ?>"
            aria-expanded="<?= $docActive ? 'true' : 'false' ?>">
                <span class="nav-icon"><i class="fas fa-file-alt"></i></span>
                <span class="nav-label">Documentos Acadêmicos</span>
                <i class="fas fa-chevron-right nav-arrow"></i>
            </a>
            <ul class="collapse submenu <?= $docActive ? 'show' : '' ?>" id="academicDocsSubmenu">
                <li><a href="<?= site_url('admin/academic-documents/eligible-certificates') ?>" class="<?= uri_string() == 'admin/academic-documents/eligible-certificates' ? 'active' : '' ?>">
                    <i class="fas fa-certificate"></i> Certificados (Fim de Ciclo)
                </a></li>
                <li><a href="<?= site_url('admin/academic-documents/eligible-declarations') ?>" class="<?= uri_string() == 'admin/academic-documents/eligible-declarations' ? 'active' : '' ?>">
                    <i class="fas fa-file-signature"></i> Declarações (Fim de Ano)
                </a></li>
                <li><a href="<?= site_url('admin/academic-documents/history') ?>" class="<?= uri_string() == 'admin/academic-documents/history' ? 'active' : '' ?>">
                    <i class="fas fa-history"></i> Histórico de Documentos
                </a></li>
            </ul>
        </li>
        <?php endif; ?>
        <!-- Propinas e Taxas -->
        <?php if (has_permission('fees.list') || has_permission('fee_types.list')): ?>
        <?php $feesActive = in_array(uri_string(), ['admin/fees/types','admin/fees/structure','admin/fees/payments']); ?>
        <li class="nav-item">
            <a href="#feesSubmenu" data-bs-toggle="collapse"
               class="nav-link dropdown-toggle <?= $feesActive ? 'active' : '' ?>"
               aria-expanded="<?= $feesActive ? 'true' : 'false' ?>">
                <span class="nav-icon"><i class="fas fa-money-bill"></i></span>
                <span class="nav-label">Propinas e Taxas</span>
                <i class="fas fa-chevron-right nav-arrow"></i>
            </a>
            <ul class="collapse submenu <?= $feesActive ? 'show' : '' ?>" id="feesSubmenu">
                <?php if (has_permission('fee_types.list')): ?>
                <li><a href="<?= site_url('admin/fees/types') ?>" class="<?= uri_string() == 'admin/fees/types' ? 'active' : '' ?>">
                    <i class="fas fa-tag"></i> Tipos de Taxas
                </a></li>
                <?php endif; ?>
                <?php if (has_permission('fees.list')): ?>
                <li><a href="<?= site_url('admin/fees/structure') ?>" class="<?= uri_string() == 'admin/fees/structure' ? 'active' : '' ?>">
                    <i class="fas fa-table"></i> Estrutura de Taxas
                </a></li>
                <li><a href="<?= site_url('admin/fees/payments') ?>" class="<?= uri_string() == 'admin/fees/payments' ? 'active' : '' ?>">
                    <i class="fas fa-credit-card"></i> Pagamentos
                </a></li>
                <?php endif; ?>
            </ul>
        </li>
        <?php endif; ?>

        <!-- Financeiro -->
        <?php
        $financialUris = ['admin/financial/invoices','admin/financial/payments','admin/financial/expenses','admin/financial/currencies','admin/financial/taxes'];
        $financialActive = in_array(uri_string(), $financialUris);
        if (has_permission('financial.dashboard') || has_permission('invoices.list') || has_permission('expenses.list') || has_permission('settings.financial')):
        ?>
        <li class="nav-item">
            <a href="#financialSubmenu" data-bs-toggle="collapse"
               class="nav-link dropdown-toggle <?= $financialActive ? 'active' : '' ?>"
               aria-expanded="<?= $financialActive ? 'true' : 'false' ?>">
                <span class="nav-icon"><i class="fas fa-chart-pie"></i></span>
                <span class="nav-label">Financeiro</span>
                <i class="fas fa-chevron-right nav-arrow"></i>
            </a>
            <ul class="collapse submenu <?= $financialActive ? 'show' : '' ?>" id="financialSubmenu">
                <?php if (has_permission('invoices.list')): ?>
                <li><a href="<?= site_url('admin/financial/invoices') ?>" class="<?= uri_string() == 'admin/financial/invoices' ? 'active' : '' ?>">
                    <i class="fas fa-file-invoice"></i> Faturas
                </a></li>
                <li><a href="<?= site_url('admin/financial/payments') ?>" class="<?= uri_string() == 'admin/financial/payments' ? 'active' : '' ?>">
                    <i class="fas fa-money-bill-wave"></i> Pagamentos
                </a></li>
                <?php endif; ?>
                <?php if (has_permission('expenses.list')): ?>
                <li><a href="<?= site_url('admin/financial/expenses') ?>" class="<?= uri_string() == 'admin/financial/expenses' ? 'active' : '' ?>">
                    <i class="fas fa-chart-pie"></i> Despesas
                </a></li>
                <?php endif; ?>
                <?php if (has_permission('settings.financial')): ?>
                <li><a href="<?= site_url('admin/financial/currencies') ?>" class="<?= uri_string() == 'admin/financial/currencies' ? 'active' : '' ?>">
                    <i class="fas fa-coins"></i> Moedas
                </a></li>
                <li><a href="<?= site_url('admin/financial/taxes') ?>" class="<?= uri_string() == 'admin/financial/taxes' ? 'active' : '' ?>">
                    <i class="fas fa-percent"></i> Taxas
                </a></li>
                <?php endif; ?>
            </ul>
        </li>
        <?php endif; ?>

        <!-- Central de Documentos -->
        <?php if (has_permission('documents.list') || has_permission('document_requests.list')):
            $documentModel = new \App\Models\DocumentModel();
            $pendingDocs = $documentModel->where('is_verified', 0)->countAllResults();
            $docsUris = ['admin/documents','admin/documents/pending','admin/documents/verified','admin/documents/requests','admin/documents/reports'];
            $docsActive = in_array(uri_string(), $docsUris);
        ?>
        <li class="nav-item">
            <a href="#adminDocumentsSubmenu" data-bs-toggle="collapse"
               class="nav-link dropdown-toggle <?= $docsActive ? 'active' : '' ?>"
               aria-expanded="<?= $docsActive ? 'true' : 'false' ?>">
                <span class="nav-icon"><i class="fas fa-folder-open"></i></span>
                <span class="nav-label">Central de Documentos</span>
                <?php if ($pendingDocs > 0): ?>
                    <span class="nav-badge warning"><?= $pendingDocs ?></span>
                <?php endif; ?>
                <i class="fas fa-chevron-right nav-arrow"></i>
            </a>
            <ul class="collapse submenu <?= $docsActive ? 'show' : '' ?>" id="adminDocumentsSubmenu">
                <?php if (has_permission('documents.list')): ?>
                <li><a href="<?= site_url('admin/documents') ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="<?= site_url('admin/documents/pending') ?>">
                    <i class="fas fa-clock"></i> Pendentes
                    <?php if ($pendingDocs > 0): ?><span class="sub-badge warning"><?= $pendingDocs ?></span><?php endif; ?>
                </a></li>
                <li><a href="<?= site_url('admin/documents/verified') ?>"><i class="fas fa-check-circle"></i> Verificados</a></li>
                <?php endif; ?>
                <?php if (has_permission('documents.verify')): ?>
                <li><a href="<?= site_url('admin/documents/types') ?>"><i class="fas fa-tags"></i> Tipos de Documentos</a></li>
                <li><a href="<?= site_url('admin/documents/requestable') ?>"><i class="fas fa-file-invoice"></i> Documentos Solicitáveis</a></li>
                <?php endif; ?>
                <?php if (has_permission('document_requests.list')): ?>
                <li><a href="<?= site_url('admin/documents/requests') ?>"><i class="fas fa-file-signature"></i> Solicitações</a></li>
                <li><a href="<?= site_url('admin/documents/reports') ?>"><i class="fas fa-chart-bar"></i> Relatórios</a></li>
                <?php endif; ?>
            </ul>
        </li>
        <?php endif; ?>

        <!-- Gerador de Documentos -->
        <?php if (has_permission('document_requests.list') || has_permission('report_cards.generate')):
            $requestModel = new \App\Models\DocumentRequestModel();
            $pendingRequests = $requestModel->where('status', 'pending')->countAllResults();
            $genUris = ['admin/document-generator','admin/document-generator/pending','admin/document-generator/generated','admin/document-generator/templates'];
            $genActive = in_array(uri_string(), $genUris);
        ?>
        <li class="nav-item">
            <a href="#documentGeneratorSubmenu" data-bs-toggle="collapse"
               class="nav-link dropdown-toggle <?= $genActive ? 'active' : '' ?>"
               aria-expanded="<?= $genActive ? 'true' : 'false' ?>">
                <span class="nav-icon"><i class="fas fa-file-pdf"></i></span>
                <span class="nav-label">Gerador de Documentos</span>
                <?php if ($pendingRequests > 0): ?>
                    <span class="nav-badge warning"><?= $pendingRequests ?></span>
                <?php endif; ?>
                <i class="fas fa-chevron-right nav-arrow"></i>
            </a>
            <ul class="collapse submenu <?= $genActive ? 'show' : '' ?>" id="documentGeneratorSubmenu">
                <?php if (has_permission('document_requests.list')): ?>
                <li><a href="<?= site_url('admin/document-generator') ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
                <li><a href="<?= site_url('admin/document-generator/pending') ?>">
                    <i class="fas fa-clock"></i> Pendentes
                    <?php if ($pendingRequests > 0): ?><span class="sub-badge warning"><?= $pendingRequests ?></span><?php endif; ?>
                </a></li>
                <li><a href="<?= site_url('admin/document-generator/generated') ?>"><i class="fas fa-check-circle"></i> Documentos Gerados</a></li>
                <?php endif; ?>
                <?php if (has_permission('report_cards.generate')): ?>
                <li><a href="<?= site_url('admin/document-generator/templates') ?>"><i class="fas fa-file-alt"></i> Modelos</a></li>
                <?php endif; ?>
            </ul>
        </li>
        <?php endif; ?>

        <!-- Relatórios -->
        <?php
        $reportsUris = ['admin/reports/academic','admin/reports/financial','admin/reports/students','admin/reports/attendance','admin/reports/exams','admin/reports/fees'];
        $reportsActive = in_array(uri_string(), $reportsUris);
        if (has_permission('reports.view')):
        ?>
        <li class="nav-item">
            <a href="#reportsSubmenu" data-bs-toggle="collapse"
               class="nav-link dropdown-toggle <?= $reportsActive ? 'active' : '' ?>"
               aria-expanded="<?= $reportsActive ? 'true' : 'false' ?>">
                <span class="nav-icon"><i class="fas fa-chart-bar"></i></span>
                <span class="nav-label">Relatórios</span>
                <i class="fas fa-chevron-right nav-arrow"></i>
            </a>
            <ul class="collapse submenu <?= $reportsActive ? 'show' : '' ?>" id="reportsSubmenu">
                <?php if (has_permission('reports.academic')): ?>
                <li><a href="<?= site_url('admin/reports/academic') ?>"><i class="fas fa-graduation-cap"></i> Académico</a></li>
                <?php endif; ?>
                <?php if (has_permission('reports.financial')): ?>
                <li><a href="<?= site_url('admin/reports/financial') ?>"><i class="fas fa-chart-line"></i> Financeiro</a></li>
                <?php endif; ?>
                <?php if (has_permission('reports.academic')): ?>
                <li><a href="<?= site_url('admin/reports/students') ?>"><i class="fas fa-users"></i> Alunos</a></li>
                <?php endif; ?>
                <?php if (has_permission('reports.attendance')): ?>
                <li><a href="<?= site_url('admin/reports/attendance') ?>"><i class="fas fa-calendar-check"></i> Presenças</a></li>
                <?php endif; ?>
                <?php if (has_permission('reports.academic')): ?>
                <li><a href="<?= site_url('admin/reports/exams') ?>"><i class="fas fa-pencil-alt"></i> Exames</a></li>
                <?php endif; ?>
                <?php if (has_permission('reports.fees')): ?>
                <li><a href="<?= site_url('admin/reports/fees') ?>"><i class="fas fa-money-bill"></i> Propinas</a></li>
                <?php endif; ?>
            </ul>
        </li>
        <?php endif; ?>

        <!-- Configurações -->
        <?php
        $settingsUris = ['admin/settings','admin/settings/general','admin/settings/school','admin/settings/academic','admin/settings/payment','admin/settings/email','admin/users','admin/roles'];
        $settingsActive = in_array(uri_string(), $settingsUris);
        if (has_permission('settings.view') || has_permission('users.list') || has_permission('roles.list') || has_permission('settings.general')):
        ?>
        <li class="nav-item">
            <a href="#settingsSubmenu" data-bs-toggle="collapse"
               class="nav-link dropdown-toggle <?= $settingsActive ? 'active' : '' ?>"
               aria-expanded="<?= $settingsActive ? 'true' : 'false' ?>">
                <span class="nav-icon"><i class="fas fa-cog"></i></span>
                <span class="nav-label">Configurações</span>
                <i class="fas fa-chevron-right nav-arrow"></i>
            </a>
            <ul class="collapse submenu <?= $settingsActive ? 'show' : '' ?>" id="settingsSubmenu">
                <li><a href="<?= site_url('admin/settings') ?>" class="<?= uri_string() == 'admin/settings' ? 'active' : '' ?>">
                    <i class="fas fa-tachometer-alt"></i> Gerais
                </a></li>
                <?php if (has_permission('settings.general')): ?>
           <!--      <li><a href="<?= site_url('admin/settings/general') ?>" class="<?= uri_string() == 'admin/settings/general' ? 'active' : '' ?>">
                    <i class="fas fa-sliders-h"></i> Gerais
                </a></li> -->
             <!--    <li><a href="<?= site_url('admin/settings/school') ?>" class="<?= uri_string() == 'admin/settings/school' ? 'active' : '' ?>">
                    <i class="fas fa-school"></i> Escola
                </a></li> -->
                <?php endif; ?>
                <?php if (has_permission('settings.payment')): ?>
             <!--    <li><a href="<?= site_url('admin/settings/payment') ?>" class="<?= uri_string() == 'admin/settings/payment' ? 'active' : '' ?>">
                    <i class="fas fa-money-bill-wave"></i> Pagamento
                </a></li> -->
                <?php endif; ?>
                <?php if (has_permission('emails')): ?>
                <li><a href="<?= site_url('admin/email') ?>" class="<?= uri_string() == 'admin/email' ? 'active' : '' ?>">
                    <i class="fas fa-envelope"></i> Email
                </a></li>
                <?php endif; ?>
                <?php if (has_permission('users.list')): ?>
                <li><a href="<?= site_url('admin/users') ?>" class="<?= uri_string() == 'admin/users' ? 'active' : '' ?>">
                    <i class="fas fa-users-cog"></i> Utilizadores
                </a></li>
                <?php endif; ?>
                <?php if (has_permission('roles.list')): ?>
                <li><a href="<?= site_url('admin/roles') ?>" class="<?= uri_string() == 'admin/roles' ? 'active' : '' ?>">
                    <i class="fas fa-shield-alt"></i> Perfis de Acesso
                </a></li>
                <?php endif; ?>
            </ul>
        </li>
        <?php endif; ?>

        <!-- Ferramentas -->
        <?php if (has_permission('logs.view') || has_permission('settings.backup')): ?>
        <li class="nav-item">
            <a href="#toolsSubmenu" data-bs-toggle="collapse"
               class="nav-link dropdown-toggle <?= in_array(uri_string(), ['admin/tools/logs']) ? 'active' : '' ?>"
               aria-expanded="<?= in_array(uri_string(), ['admin/tools/logs']) ? 'true' : 'false' ?>">
                <span class="nav-icon"><i class="fas fa-tools"></i></span>
                <span class="nav-label">Ferramentas</span>
                <i class="fas fa-chevron-right nav-arrow"></i>
            </a>
            <ul class="collapse submenu <?= in_array(uri_string(), ['admin/tools/logs']) ? 'show' : '' ?>" id="toolsSubmenu">
                <?php if (has_permission('logs.view')): ?>
                <li><a href="<?= site_url('admin/tools/logs') ?>"><i class="fas fa-history"></i> Logs do Sistema</a></li>
                <?php endif; ?>
                <li><a href="<?= site_url('admin/settings/clear-cache') ?>" onclick="return confirm('Tem certeza que deseja limpar o cache?')">
                    <i class="fas fa-broom"></i> Limpar Cache
                </a></li>
            </ul>
        </li>
        <?php endif; ?>

    </ul>

    <!-- Footer do Sidebar -->
    <div class="sidebar-footer">
        <div class="sidebar-footer-info">
            <?php if (session()->get('academic_year')): ?>
            <div class="footer-chip">
                <i class="fas fa-database"></i>
                <span><?= session()->get('academic_year') ?></span>
            </div>
            <?php endif; ?>
            <div class="footer-chip">
                <i class="fas fa-calendar-alt"></i>
                <span><?= date('Y') ?></span>
            </div>
        </div>
        <a href="<?= site_url('auth/logout') ?>" class="btn-logout"
           onclick="return confirm('Tem certeza que deseja sair?')">
            <i class="fas fa-sign-out-alt"></i>
            <span>Sair do Sistema</span>
        </a>
    </div>

</nav>

<style>
    .sidebar .nav-link.dropdown-toggle::after,
.sidebar .nav-link.dropdown-toggle::before {
    display: none !important;
    content: none !important;
    border: none !important;
}
</style>