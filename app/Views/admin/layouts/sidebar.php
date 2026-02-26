<nav class="sidebar">


<!-- Logo e Cabeçalho -->
<div class="sidebar-header py-2"> <!-- Reduzido padding vertical -->
    <div class="d-flex align-items-center">
        <?= school_logo_html(50, 50, 'sidebar-logo me-2') ?>
        
        <div class="flex-grow-1">
            <h5 class="sidebar-title mb-0"><?= setting('school_acronym') ?: 'Sistema' ?></h5> <!-- h5 em vez de h4 -->
            <small class="sidebar-subtitle"><?= setting('school_name') ?: 'Escolar' ?></small> <!-- small em vez de p -->
        </div>
    </div>
</div>

<ul class="components mt-1"> <!-- Reduzido margin top -->
    <!-- Dashboard -->
    <li class="nav-item">
        <a href="<?= site_url('admin/dashboard') ?>" class="nav-link py-2 <?= uri_string() == 'admin/dashboard' ? 'active' : '' ?>"> <!-- py-2 reduzido -->
            <i class="fas fa-tachometer-alt"></i>
            <span>Dashboard</span>
        </a>
    </li>
    
    <!-- Académico -->
    <?php if (has_permission('settings.academic_years') || has_permission('settings.semesters') || has_permission('calendar.view')): ?>
    <li class="nav-item">
        <a href="#academicSubmenu" data-bs-toggle="collapse" class="nav-link py-2 dropdown-toggle <?= in_array(uri_string(), ['admin/academic/years', 'admin/academic/semesters', 'admin/academic/calendar']) ? 'active' : '' ?>">
            <i class="fas fa-graduation-cap"></i>
            <span>Académico</span>
            <i class="fas fa-chevron-right dropdown-icon"></i>
        </a>
        <ul class="collapse list-unstyled submenu <?= in_array(uri_string(), ['admin/academic/years', 'admin/academic/semesters', 'admin/academic/calendar']) ? 'show' : '' ?>" id="academicSubmenu">
            <?php if (has_permission('settings.academic_years')): ?>
            <li><a href="<?= site_url('admin/academic/years') ?>" class="py-1 <?= uri_string() == 'admin/academic/years' ? 'active' : '' ?>"><i class="fas fa-calendar"></i> Anos Letivos</a></li> <!-- py-1 reduzido -->
            <?php endif; ?>
            
            <?php if (has_permission('settings.semesters')): ?>
            <li><a href="<?= site_url('admin/academic/semesters') ?>" class="py-1 <?= uri_string() == 'admin/academic/semesters' ? 'active' : '' ?>"><i class="fas fa-calendar-alt"></i> Semestres</a></li>
            <?php endif; ?>
            
            <?php if (has_permission('calendar.view')): ?>
            <li><a href="<?= site_url('admin/academic/calendar') ?>" class="py-1 <?= uri_string() == 'admin/academic/calendar' ? 'active' : '' ?>"><i class="fas fa-calendar-check"></i> Calendário</a></li>
            <?php endif; ?>
        </ul>
    </li>
    <?php endif; ?>
    
    <!-- Cursos (Ensino Médio) -->
    <?php if (has_permission('courses.list') || has_permission('courses.create')): ?>
    <li class="nav-item">
        <a href="#coursesSubmenu" data-bs-toggle="collapse" class="nav-link py-2 dropdown-toggle <?= in_array(uri_string(), ['admin/courses', 'admin/courses/curriculum']) ? 'active' : '' ?>">
            <i class="fas fa-layer-group"></i>
            <span>Cursos</span>
            <?php 
            $courseModel = new \App\Models\CourseModel();
            $totalCourses = $courseModel->where('is_active', 1)->countAllResults();
            if ($totalCourses > 0 && has_permission('courses.list')): 
            ?>
                <span class="badge bg-info ms-auto"><?= $totalCourses ?></span>
            <?php endif; ?>
            <i class="fas fa-chevron-right dropdown-icon"></i>
        </a>
        <ul class="collapse list-unstyled submenu <?= in_array(uri_string(), ['admin/courses', 'admin/courses/curriculum']) ? 'show' : '' ?>" id="coursesSubmenu">
            <?php if (has_permission('courses.list')): ?>
            <li><a href="<?= site_url('admin/courses') ?>" class="py-1 <?= uri_string() == 'admin/courses' ? 'active' : '' ?>"><i class="fas fa-list"></i> Lista de Cursos</a></li>
            <?php endif; ?>
            
            <?php if (has_permission('courses.create')): ?>
            <li><a href="<?= site_url('admin/courses/form-add') ?>" class="py-1 <?= uri_string() == 'admin/courses/form-add' ? 'active' : '' ?>"><i class="fas fa-plus-circle"></i> Novo Curso</a></li>
            <?php endif; ?>
        </ul>
    </li>
    <?php endif; ?>
    
    <!-- Classes/Turmas -->
    <?php if (has_permission('classes.list') || has_permission('subjects.list') || has_permission('settings.grade_levels')): ?>
    <li class="nav-item">
        <a href="#classesSubmenu" data-bs-toggle="collapse" class="nav-link py-2 dropdown-toggle <?= in_array(uri_string(), ['admin/classes/levels', 'admin/classes/classes', 'admin/classes/subjects', 'admin/classes/class-subjects']) ? 'active' : '' ?>">
            <i class="fas fa-school"></i>
            <span>Classes/Turmas</span>
            <i class="fas fa-chevron-right dropdown-icon"></i>
        </a>
        <ul class="collapse list-unstyled submenu <?= in_array(uri_string(), ['admin/classes/levels', 'admin/classes/classes', 'admin/classes/subjects', 'admin/classes/class-subjects']) ? 'show' : '' ?>" id="classesSubmenu">
            <?php if (has_permission('settings.grade_levels')): ?>
            <li><a href="<?= site_url('admin/classes/levels') ?>" class="py-1 <?= uri_string() == 'admin/classes/levels' ? 'active' : '' ?>"><i class="fas fa-layer-group"></i> Níveis de Ensino</a></li>
            <?php endif; ?>
            
            <?php if (has_permission('classes.list')): ?>
            <li><a href="<?= site_url('admin/classes/classes') ?>" class="py-1 <?= uri_string() == 'admin/classes/classes' ? 'active' : '' ?>"><i class="fas fa-users"></i> Turmas</a></li>
            <?php endif; ?>
            
            <?php if (has_permission('subjects.list')): ?>
            <li><a href="<?= site_url('admin/classes/subjects') ?>" class="py-1 <?= uri_string() == 'admin/classes/subjects' ? 'active' : '' ?>"><i class="fas fa-book"></i> Disciplinas</a></li>
            <?php endif; ?>
            
            <?php if (has_permission('subjects.assign_to_classes')): ?>
            <li><a href="<?= site_url('admin/classes/class-subjects') ?>" class="py-1 <?= uri_string() == 'admin/classes/class-subjects' ? 'active' : '' ?>"><i class="fas fa-link"></i> Alocação de Disciplinas</a></li>
            <?php endif; ?>
        </ul>
    </li>
    <?php endif; ?>
    
    <!-- Alunos -->
    <?php if (has_permission('students.list') || has_permission('enrollments.list') || has_permission('guardians.list') || has_permission('attendance.list')): ?>
    <li class="nav-item">
        <a href="#studentsSubmenu" data-bs-toggle="collapse" class="nav-link py-2 dropdown-toggle <?= in_array(uri_string(), ['admin/students', 'admin/students/enrollments', 'admin/students/guardians', 'admin/students/attendance']) ? 'active' : '' ?>">
            <i class="fas fa-user-graduate"></i>
            <span>Alunos</span>
            <i class="fas fa-chevron-right dropdown-icon"></i>
        </a>
        <ul class="collapse list-unstyled submenu <?= in_array(uri_string(), ['admin/students', 'admin/students/enrollments', 'admin/students/guardians', 'admin/students/attendance']) ? 'show' : '' ?>" id="studentsSubmenu">
            <?php if (has_permission('students.list')): ?>
            <li><a href="<?= site_url('admin/students') ?>" class="py-1 <?= uri_string() == 'admin/students' ? 'active' : '' ?>"><i class="fas fa-list"></i> Lista de Alunos</a></li>
            <?php endif; ?>
            
            <?php if (has_permission('students.create')): ?>
            <li><a href="<?= site_url('admin/students/form-add') ?>" class="py-1 <?= uri_string() == 'admin/students/form-add' ? 'active' : '' ?>"><i class="fas fa-plus-circle"></i> Novo Aluno</a></li>
            <?php endif; ?>
            
            <?php if (has_permission('enrollments.list')): ?>
            <li><a href="<?= site_url('admin/students/enrollments') ?>" class="py-1 <?= uri_string() == 'admin/students/enrollments' ? 'active' : '' ?>"><i class="fas fa-file-signature"></i> Matrículas</a></li>
            <?php endif; ?>
            
            <?php if (has_permission('guardians.list')): ?>
            <li><a href="<?= site_url('admin/students/guardians') ?>" class="py-1 <?= uri_string() == 'admin/students/guardians' ? 'active' : '' ?>"><i class="fas fa-users"></i> Encarregados</a></li>
            <?php endif; ?>
            
            <?php if (has_permission('attendance.list')): ?>
            <li><a href="<?= site_url('admin/students/attendance') ?>" class="py-1 <?= uri_string() == 'admin/students/attendance' ? 'active' : '' ?>"><i class="fas fa-calendar-check"></i> Presenças</a></li>
            <?php endif; ?>
        </ul>
    </li>
    <?php endif; ?>
    
    <!-- Professores -->
    <?php if (has_permission('teachers.list') || has_permission('teachers.create') || has_permission('teachers.assign_subjects')): ?>
    <li class="nav-item">
        <a href="#teachersSubmenu" data-bs-toggle="collapse" class="nav-link py-2 dropdown-toggle <?= in_array(uri_string(), ['admin/teachers', 'admin/teachers/assign-class']) ? 'active' : '' ?>">
            <i class="fas fa-chalkboard-teacher"></i>
            <span>Professores</span>
            <i class="fas fa-chevron-right dropdown-icon"></i>
        </a>
        <ul class="collapse list-unstyled submenu <?= in_array(uri_string(), ['admin/teachers', 'admin/teachers/assign-class']) ? 'show' : '' ?>" id="teachersSubmenu">
            <?php if (has_permission('teachers.list')): ?>
            <li><a href="<?= site_url('admin/teachers') ?>" class="py-1 <?= uri_string() == 'admin/teachers' ? 'active' : '' ?>"><i class="fas fa-list"></i> Lista de Professores</a></li>
            <?php endif; ?>
            
            <?php if (has_permission('teachers.create')): ?>
            <li><a href="<?= site_url('admin/teachers/form-add') ?>" class="py-1 <?= uri_string() == 'admin/teachers/form-add' ? 'active' : '' ?>"><i class="fas fa-plus-circle"></i> Novo Professor</a></li>
            <?php endif; ?>
            
            <?php if (has_permission('teachers.assign_subjects')): ?>
            <li><a href="<?= site_url('admin/teachers/assign-class') ?>" class="py-1 <?= uri_string() == 'admin/teachers/assign-class' ? 'active' : '' ?>"><i class="fas fa-tasks"></i> Atribuir Turmas</a></li>
            <?php endif; ?>
        </ul>
    </li>
    <?php endif; ?>
    
    <!-- Exames e Avaliações -->
    <?php if (has_permission('exams.list') || has_permission('exams.enter_grades') || has_permission('exam_periods.list') || has_permission('exam_schedules.list') || has_permission('results.view')): ?>
    <li class="nav-item">
        <a href="#examsMainSubmenu" data-bs-toggle="collapse" class="nav-link py-2 dropdown-toggle <?= 
            in_array(uri_string(), [
                'admin/exams/periods', 
                'admin/exams/schedules', 
                'admin/exams/boards', 
                'admin/exams/results',
                'admin/exams/weights',
                'admin/exams/calculate',
                'admin/exams/appeals'
            ]) ? 'active' : '' ?>">
            <i class="fas fa-pencil-alt"></i>
            <span>Exames e Avaliações</span>
            <?php
            if (has_permission('exams.list')):
                $examScheduleModel = new \App\Models\ExamScheduleModel();
                $pendingExams = $examScheduleModel
                    ->where('status', 'Agendado')
                    ->where('exam_date >=', date('Y-m-d'))
                    ->countAllResults();
                if ($pendingExams > 0):
            ?>
                <span class="badge bg-warning text-dark ms-auto"><?= $pendingExams ?></span>
            <?php 
                endif;
            endif; 
            ?>
            <i class="fas fa-chevron-right dropdown-icon"></i>
        </a>
        <ul class="collapse list-unstyled submenu <?= 
            in_array(uri_string(), [
                'admin/exams/periods', 
                'admin/exams/schedules', 
                'admin/exams/boards', 
                'admin/exams/results',
                'admin/exams/weights',
                'admin/exams/calculate',
                'admin/exams/appeals'
            ]) ? 'show' : '' ?>" id="examsMainSubmenu">
            
            <?php if (has_permission('exam_periods.list')): ?>
            <li><a href="<?= site_url('admin/exams/periods') ?>" class="py-1 <?= uri_string() == 'admin/exams/periods' ? 'active' : '' ?>"><i class="fas fa-calendar-alt"></i> Períodos de Exame</a></li>
            <?php endif; ?>
            
            <?php if (has_permission('exam_schedules.list')): ?>
            <li><a href="<?= site_url('admin/exams/schedules') ?>" class="py-1 <?= uri_string() == 'admin/exams/schedules' ? 'active' : '' ?>"><i class="fas fa-clock"></i> Agendamentos</a></li>
            <?php endif; ?>
            
            <?php if (has_permission('exams.list')): ?>
            <li><a href="<?= site_url('admin/exams/boards') ?>" class="py-1 <?= uri_string() == 'admin/exams/boards' ? 'active' : '' ?>"><i class="fas fa-clipboard-list"></i> Tipos de Exames</a></li>
            <li><a href="<?= site_url('admin/exams/weights') ?>" class="py-1 <?= uri_string() == 'admin/exams/weights' ? 'active' : '' ?>"><i class="fas fa-weight-hanging"></i> Pesos das Avaliações</a></li>
            <?php endif; ?>
            
            <?php if (has_permission('exams.view_grades')): ?>
            <li><a href="<?= site_url('admin/exams/results') ?>" class="py-1 <?= uri_string() == 'admin/exams/results' ? 'active' : '' ?>"><i class="fas fa-star"></i> Resultados de Exames</a></li>
            <?php endif; ?>
            
            <?php if (has_permission('discipline_averages.view')): ?>
            <li><a href="<?= site_url('admin/discipline-averages') ?>" class="py-1 <?= uri_string() == 'admin/discipline-averages' ? 'active' : '' ?>"><i class="fas fa-chart-line"></i> Médias Disciplinares</a></li>
            <?php endif; ?>
            
            <?php if (has_permission('results.view')): ?>
            <li><a href="<?= site_url('admin/semester-results') ?>" class="py-1 <?= uri_string() == 'admin/semester-results' ? 'active' : '' ?>"><i class="fas fa-calendar-check"></i> Resultados Semestrais</a></li>
            <?php endif; ?>
            
            <?php if (has_permission('discipline_averages.calculate')): ?>
            <li><a href="<?= site_url('admin/exams/calculate') ?>" class="py-1 <?= uri_string() == 'admin/exams/calculate' ? 'active' : '' ?>"><i class="fas fa-calculator"></i> Calculadora de Médias</a></li>
            <?php endif; ?>
            
            <?php if (has_permission('exams.view_grades')): ?>
            <li><a href="<?= site_url('admin/exams/appeals') ?>" class="py-1 <?= uri_string() == 'admin/exams/appeals' ? 'active' : '' ?>">
                <i class="fas fa-exclamation-triangle"></i> Exames de Recurso
                <?php
                if (has_permission('results.view')):
                    $disciplineAvgModel = new \App\Models\DisciplineAverageModel();
                    $appealStudents = $disciplineAvgModel
                        ->where('status', 'Recurso')
                        ->countAllResults();
                    if ($appealStudents > 0):
                ?>
                    <span class="badge bg-warning text-dark ms-auto"><?= $appealStudents ?></span>
                <?php 
                    endif;
                endif; 
                ?>
            </a></li>
            <?php endif; ?>
        </ul>
    </li>
    <?php endif; ?>

    <!-- PAUTAS ACADÊMICAS -->
    <?php if (has_permission('reports.academic') || has_permission('exams.view_results') || has_permission('grades.view_averages')): ?>
    <li class="nav-item">
        <a href="#pautasSubmenu" data-bs-toggle="collapse" class="nav-link py-2 dropdown-toggle <?= 
            in_array(uri_string(), [
                'admin/academic-records', 
                'admin/academic-records/trimestral', 
                'admin/academic-records/disciplina'
            ]) ? 'active' : '' ?>">
            <i class="fas fa-chart-line"></i>
            <span>Pautas Acadêmicas</span>
            <?php 
            if (has_permission('enrollments.list')):
                $enrollmentModel = new \App\Models\EnrollmentModel();
                $pendingRecords = $enrollmentModel
                    ->where('final_result', 'Em Andamento')
                    ->where('status', 'Ativo')
                    ->countAllResults();
                if ($pendingRecords > 0): 
            ?>
                <span class="badge bg-warning text-dark ms-auto"><?= $pendingRecords ?></span>
            <?php 
                endif;
            endif; 
            ?>
            <i class="fas fa-chevron-right dropdown-icon"></i>
        </a>
        <ul class="collapse list-unstyled submenu <?= 
            in_array(uri_string(), [
                'admin/academic-records', 
                'admin/academic-records/trimestral', 
                'admin/academic-records/disciplina'
            ]) ? 'show' : '' ?>" id="pautasSubmenu">
            
            <?php if (has_permission('reports.academic')): ?>
            <li>
                <a href="<?= site_url('admin/academic-records') ?>" class="py-1 <?= uri_string() == 'admin/academic-records' ? 'active' : '' ?>">
                    <i class="fas fa-file-alt"></i> Pauta Académica
                </a>
            </li>
            <?php endif; ?>
            
            <?php if (has_permission('exams.view_results') || has_permission('grades.view_averages')): ?>
            <li>
                <a href="<?= site_url('admin/academic-records/trimestral') ?>" class="py-1 <?= uri_string() == 'admin/academic-records/trimestral' ? 'active' : '' ?>">
                    <i class="fas fa-calendar-alt"></i> Pauta Trimestral
                </a>
            </li>
            <?php endif; ?>
            
            <?php if (has_permission('grades.view_averages')): ?>
            <li>
                <a href="<?= site_url('admin/academic-records/disciplina') ?>" class="py-1 <?= uri_string() == 'admin/academic-records/disciplina' ? 'active' : '' ?>">
                    <i class="fas fa-book-open"></i> Pauta por Disciplina
                </a>
            </li>
            <?php endif; ?>
        </ul>
    </li>
    <?php endif; ?>

    <!-- Mini Pautas -->
    <?php if (has_permission('exams.view') || has_permission('grades.view_averages')): ?>
    <li class="nav-item">
        <a href="#miniPautasSubmenu" data-bs-toggle="collapse" class="nav-link py-2 dropdown-toggle <?= in_array(uri_string(), [
            'admin/mini-grade-sheet',
            'admin/mini-grade-sheet/trimestral',
            'admin/mini-grade-sheet/disciplina'
        ]) ? 'active' : '' ?>">
            <i class="fas fa-file-alt"></i>
            <span>Mini Pautas</span>
            <i class="fas fa-chevron-right dropdown-icon"></i>
        </a>
        <ul class="collapse list-unstyled submenu <?= in_array(uri_string(), [
            'admin/mini-grade-sheet',
            'admin/mini-grade-sheet/trimestral',
            'admin/mini-grade-sheet/disciplina'
        ]) ? 'show' : '' ?>" id="miniPautasSubmenu">
            
            <li>
                <a href="<?= site_url('admin/mini-grade-sheet') ?>" class="py-1 <?= uri_string() == 'admin/mini-grade-sheet' ? 'active' : '' ?>">
                    <i class="fas fa-list"></i> Todas as Mini Pautas
                </a>
            </li>
            
            <li>
                <a href="<?= site_url('admin/mini-grade-sheet/trimestral') ?>" class="py-1 <?= uri_string() == 'admin/mini-grade-sheet/trimestral' ? 'active' : '' ?>">
                    <i class="fas fa-calendar-alt"></i> Pautas Trimestrais
                </a>
            </li>
            
            <li>
                <a href="<?= site_url('admin/mini-grade-sheet/disciplina') ?>" class="py-1 <?= uri_string() == 'admin/mini-grade-sheet/disciplina' ? 'active' : '' ?>">
                    <i class="fas fa-book-open"></i> Pautas por Disciplina
                </a>
            </li>
        </ul>
    </li>
    <?php endif; ?>

    <!-- Propinas e Taxas -->
    <?php if (has_permission('fees.list') || has_permission('fee_types.list')): ?>
    <li class="nav-item">
        <a href="#feesSubmenu" data-bs-toggle="collapse" class="nav-link py-2 dropdown-toggle <?= in_array(uri_string(), ['admin/fees/types', 'admin/fees/structure', 'admin/fees/payments']) ? 'active' : '' ?>">
            <i class="fas fa-money-bill"></i>
            <span>Propinas e Taxas</span>
            <i class="fas fa-chevron-right dropdown-icon"></i>
        </a>
        <ul class="collapse list-unstyled submenu <?= in_array(uri_string(), ['admin/fees/types', 'admin/fees/structure', 'admin/fees/payments']) ? 'show' : '' ?>" id="feesSubmenu">
            <?php if (has_permission('fee_types.list')): ?>
            <li><a href="<?= site_url('admin/fees/types') ?>" class="py-1 <?= uri_string() == 'admin/fees/types' ? 'active' : '' ?>"><i class="fas fa-tag"></i> Tipos de Taxas</a></li>
            <?php endif; ?>
            
            <?php if (has_permission('fees.list')): ?>
            <li><a href="<?= site_url('admin/fees/structure') ?>" class="py-1 <?= uri_string() == 'admin/fees/structure' ? 'active' : '' ?>"><i class="fas fa-table"></i> Estrutura de Taxas</a></li>
            <li><a href="<?= site_url('admin/fees/payments') ?>" class="py-1 <?= uri_string() == 'admin/fees/payments' ? 'active' : '' ?>"><i class="fas fa-credit-card"></i> Pagamentos</a></li>
            <?php endif; ?>
        </ul>
    </li>
    <?php endif; ?>
    
    <!-- Financeiro -->
    <?php if (has_permission('financial.dashboard') || has_permission('invoices.list') || has_permission('expenses.list') || has_permission('settings.financial')): ?>
    <li class="nav-item">
        <a href="#financialSubmenu" data-bs-toggle="collapse" class="nav-link py-2 dropdown-toggle <?= in_array(uri_string(), ['admin/financial/invoices', 'admin/financial/payments', 'admin/financial/expenses', 'admin/financial/currencies', 'admin/financial/taxes']) ? 'active' : '' ?>">
            <i class="fas fa-chart-line"></i>
            <span>Financeiro</span>
            <i class="fas fa-chevron-right dropdown-icon"></i>
        </a>
        <ul class="collapse list-unstyled submenu <?= in_array(uri_string(), ['admin/financial/invoices', 'admin/financial/payments', 'admin/financial/expenses', 'admin/financial/currencies', 'admin/financial/taxes']) ? 'show' : '' ?>" id="financialSubmenu">
            <?php if (has_permission('invoices.list')): ?>
            <li><a href="<?= site_url('admin/financial/invoices') ?>" class="py-1 <?= uri_string() == 'admin/financial/invoices' ? 'active' : '' ?>"><i class="fas fa-file-invoice"></i> Faturas</a></li>
            <li><a href="<?= site_url('admin/financial/payments') ?>" class="py-1 <?= uri_string() == 'admin/financial/payments' ? 'active' : '' ?>"><i class="fas fa-money-bill-wave"></i> Pagamentos</a></li>
            <?php endif; ?>
            
            <?php if (has_permission('expenses.list')): ?>
            <li><a href="<?= site_url('admin/financial/expenses') ?>" class="py-1 <?= uri_string() == 'admin/financial/expenses' ? 'active' : '' ?>"><i class="fas fa-chart-pie"></i> Despesas</a></li>
            <?php endif; ?>
            
            <?php if (has_permission('settings.financial')): ?>
            <li><a href="<?= site_url('admin/financial/currencies') ?>" class="py-1 <?= uri_string() == 'admin/financial/currencies' ? 'active' : '' ?>"><i class="fas fa-coins"></i> Moedas</a></li>
            <li><a href="<?= site_url('admin/financial/taxes') ?>" class="py-1 <?= uri_string() == 'admin/financial/taxes' ? 'active' : '' ?>"><i class="fas fa-percent"></i> Taxas</a></li>
            <?php endif; ?>
        </ul>
    </li>
    <?php endif; ?>
    
    <!-- Central de Documentos -->
    <?php if (has_permission('documents.list') || has_permission('document_requests.list')): ?>
    <li class="nav-item">
        <a href="#adminDocumentsSubmenu" data-bs-toggle="collapse" class="nav-link py-2 dropdown-toggle <?= in_array(uri_string(), ['admin/documents', 'admin/documents/pending', 'admin/documents/verified', 'admin/documents/requests', 'admin/documents/reports']) ? 'active' : '' ?>">
            <i class="fas fa-folder-open"></i>
            <span>Central de Documentos</span>
            <?php 
            if (has_permission('documents.list')):
                $documentModel = new \App\Models\DocumentModel();
                $pendingDocs = $documentModel->where('is_verified', 0)->countAllResults();
                if ($pendingDocs > 0): 
            ?>
                <span class="badge bg-warning text-dark ms-auto"><?= $pendingDocs ?></span>
            <?php 
                endif;
            endif; 
            ?>
            <i class="fas fa-chevron-right dropdown-icon"></i>
        </a>
        <ul class="collapse list-unstyled submenu <?= in_array(uri_string(), ['admin/documents', 'admin/documents/pending', 'admin/documents/verified', 'admin/documents/requests', 'admin/documents/reports']) ? 'show' : '' ?>" id="adminDocumentsSubmenu">
            <?php if (has_permission('documents.list')): ?>
            <li><a href="<?= site_url('admin/documents') ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="<?= site_url('admin/documents/pending') ?>"><i class="fas fa-clock"></i> Pendentes <?php if($pendingDocs>0):?><span class="badge bg-warning float-end"><?=$pendingDocs?></span><?php endif;?></a></li>
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
    <?php if (has_permission('document_requests.list') || has_permission('report_cards.generate')): ?>
    <li class="nav-item">
        <a href="#documentGeneratorSubmenu" data-bs-toggle="collapse" class="nav-link py-2 dropdown-toggle <?= in_array(uri_string(), ['admin/document-generator', 'admin/document-generator/pending', 'admin/document-generator/generated', 'admin/document-generator/templates']) ? 'active' : '' ?>">
            <i class="fas fa-file-pdf"></i>
            <span>Gerador de Documentos</span>
            <?php 
            if (has_permission('document_requests.list')):
                $requestModel = new \App\Models\DocumentRequestModel();
                $pendingRequests = $requestModel->where('status', 'pending')->countAllResults();
                if ($pendingRequests > 0): 
            ?>
                <span class="badge bg-warning text-dark ms-auto"><?= $pendingRequests ?></span>
            <?php 
                endif;
            endif; 
            ?>
            <i class="fas fa-chevron-right dropdown-icon"></i>
        </a>
        <ul class="collapse list-unstyled submenu <?= in_array(uri_string(), ['admin/document-generator', 'admin/document-generator/pending', 'admin/document-generator/generated', 'admin/document-generator/templates']) ? 'show' : '' ?>" id="documentGeneratorSubmenu">
            <?php if (has_permission('document_requests.list')): ?>
            <li><a href="<?= site_url('admin/document-generator') ?>"><i class="fas fa-tachometer-alt"></i> Dashboard</a></li>
            <li><a href="<?= site_url('admin/document-generator/pending') ?>"><i class="fas fa-clock"></i> Pendentes <?php if($pendingRequests>0):?><span class="badge bg-warning float-end"><?=$pendingRequests?></span><?php endif;?></a></li>
            <li><a href="<?= site_url('admin/document-generator/generated') ?>"><i class="fas fa-check-circle"></i> Documentos Gerados</a></li>
            <?php endif; ?>
            
            <?php if (has_permission('report_cards.generate')): ?>
            <li><a href="<?= site_url('admin/document-generator/templates') ?>"><i class="fas fa-file-alt"></i> Modelos</a></li>
            <?php endif; ?>
        </ul>
    </li>
    <?php endif; ?>
    
    <!-- Relatórios -->
    <?php if (has_permission('reports.view')): ?>
    <li class="nav-item">
        <a href="#reportsSubmenu" data-bs-toggle="collapse" class="nav-link py-2 dropdown-toggle <?= in_array(uri_string(), ['admin/reports/academic', 'admin/reports/financial', 'admin/reports/students', 'admin/reports/attendance', 'admin/reports/exams', 'admin/reports/fees']) ? 'active' : '' ?>">
            <i class="fas fa-chart-bar"></i>
            <span>Relatórios</span>
            <i class="fas fa-chevron-right dropdown-icon"></i>
        </a>
        <ul class="collapse list-unstyled submenu <?= in_array(uri_string(), ['admin/reports/academic', 'admin/reports/financial', 'admin/reports/students', 'admin/reports/attendance', 'admin/reports/exams', 'admin/reports/fees']) ? 'show' : '' ?>" id="reportsSubmenu">
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
    
    <!-- CONFIGURAÇÕES -->
    <?php if (has_permission('settings.view') || has_permission('users.list') || has_permission('roles.list') || has_permission('settings.general')): ?>
    <li class="nav-item">
        <a href="#settingsSubmenu" data-bs-toggle="collapse" class="nav-link py-2 dropdown-toggle <?= in_array(uri_string(), [
            'admin/settings',
            'admin/settings/general',
            'admin/settings/school',
            'admin/settings/academic',
            'admin/settings/payment',
            'admin/settings/email',
            'admin/users',
            'admin/roles'
        ]) ? 'active' : '' ?>">
            <i class="fas fa-cog"></i>
            <span>Configurações</span>
            <i class="fas fa-chevron-right dropdown-icon"></i>
        </a>
        <ul class="collapse list-unstyled submenu <?= in_array(uri_string(), [
            'admin/settings',
            'admin/settings/general',
            'admin/settings/school',
            'admin/settings/academic',
            'admin/settings/payment',
            'admin/settings/email',
            'admin/users',
            'admin/roles'
        ]) ? 'show' : '' ?>" id="settingsSubmenu">
            
            <li><a href="<?= site_url('admin/settings') ?>" class="py-1 <?= uri_string() == 'admin/settings' ? 'active' : '' ?>">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a></li>
            
            <?php if (has_permission('settings.general')): ?>
            <li><a href="<?= site_url('admin/settings/general') ?>" class="py-1 <?= uri_string() == 'admin/settings/general' ? 'active' : '' ?>">
                <i class="fas fa-sliders-h"></i> Gerais
            </a></li>
            
            <li><a href="<?= site_url('admin/settings/school') ?>" class="py-1 <?= uri_string() == 'admin/settings/school' ? 'active' : '' ?>">
                <i class="fas fa-school"></i> Escola
            </a></li>
            <?php endif; ?>
            
            <?php if (has_permission('settings.payment')): ?>
            <li><a href="<?= site_url('admin/settings/payment') ?>" class="py-1 <?= uri_string() == 'admin/settings/payment' ? 'active' : '' ?>">
                <i class="fas fa-money-bill-wave"></i> Pagamento
            </a></li>
            <?php endif; ?>
            
            <?php if (has_permission('settings.email')): ?>
            <li><a href="<?= site_url('admin/settings/email') ?>" class="py-1 <?= uri_string() == 'admin/settings/email' ? 'active' : '' ?>">
                <i class="fas fa-envelope"></i> Email
            </a></li>
            <?php endif; ?>
            
            <?php if (has_permission('users.list')): ?>
            <li><a href="<?= site_url('admin/users') ?>" class="py-1 <?= uri_string() == 'admin/users' ? 'active' : '' ?>">
                <i class="fas fa-users-cog"></i> Utilizadores
            </a></li>
            <?php endif; ?>
            
            <?php if (has_permission('roles.list')): ?>
            <li><a href="<?= site_url('admin/roles') ?>" class="py-1 <?= uri_string() == 'admin/roles' ? 'active' : '' ?>">
                <i class="fas fa-shield-alt"></i> Perfis de Acesso
            </a></li>
            <?php endif; ?>
        </ul>
    </li>
    <?php endif; ?>
    
    <!-- Ferramentas -->
    <?php if (has_permission('logs.view') || has_permission('settings.backup')): ?>
    <li class="nav-item">
        <a href="#toolsSubmenu" data-bs-toggle="collapse" class="nav-link py-2 dropdown-toggle <?= in_array(uri_string(), ['admin/tools/logs']) ? 'active' : '' ?>">
            <i class="fas fa-tools"></i>
            <span>Ferramentas</span>
            <i class="fas fa-chevron-right dropdown-icon"></i>
        </a>
        <ul class="collapse list-unstyled submenu <?= in_array(uri_string(), ['admin/tools/logs']) ? 'show' : '' ?>" id="toolsSubmenu">
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

<div class="sidebar-footer py-2"> <!-- Reduzido padding vertical -->
    <div class="footer-info py-1">
        <i class="fas fa-calendar-alt"></i>
        <span><?= date('Y') ?></span>
    </div>
    <?php if (session()->get('academic_year')): ?>
    <div class="footer-info py-1">
        <i class="fas fa-database"></i>
        <span><?= session()->get('academic_year') ?></span>
    </div>
    <?php endif; ?>
    
    <div class="logout-btn mt-2"> <!-- Reduzido margin top -->
        <a href="<?= site_url('auth/logout') ?>" class="btn-logout py-1" onclick="return confirm('Tem certeza que deseja sair?')">
            <i class="fas fa-sign-out-alt"></i>
            <span>Sair</span>
        </a>
    </div>
</div>
</nav>
