<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <a href="<?= site_url('admin/students/form-add') ?>" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i>Novo Aluno
        </a>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Alunos</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Filtros Avançados -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <i class="fas fa-filter me-2"></i>Filtros Avançados
        <button class="btn btn-sm btn-link float-end" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
            <i class="fas fa-chevron-down"></i>
        </button>
    </div>
    <div class="collapse show" id="filterCollapse">
        <div class="card-body">
            <form method="get" id="filterForm">
                <div class="row g-3">
                    <!-- Linha 1: Busca geral -->
                    <div class="col-md-12">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control form-control-lg" name="search" 
                                   placeholder="Buscar por nome, número de matrícula, email, documento ou NIF..." 
                                   value="<?= $search ?? '' ?>">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Buscar
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="row g-3 mt-2">
                    <!-- Linha 2: Filtros específicos -->
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Ano Letivo</label>
                        <select class="form-select" name="academic_year">
                            <option value="">Todos os anos</option>
                            <?php if (!empty($academicYears)): ?>
                                <?php 
                                // Encontrar o ano atual
                                $currentYearId = null;
                                foreach ($academicYears as $year) {
                                    if (!empty($year->is_current) && $year->is_current == 1) {
                                        $currentYearId = $year->id;
                                        break;
                                    }
                                }
                                ?>
                                <?php foreach ($academicYears as $year): ?>
                                    <?php 
                                    // Verificar se é o ano atual OU se está selecionado
                                    $isSelected = ($selectedYear == $year->id) || 
                                                 (empty($selectedYear) && $year->id == $currentYearId && $selectedEnrollmentStatus != 'nao_matriculado');
                                    ?>
                                    <option value="<?= $year->id ?>" <?= $isSelected ? 'selected' : '' ?>>
                                        <?= $year->year_name ?> <?= !empty($year->is_current) && $year->is_current ? ' (Atual)' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <small class="text-muted">Padrão: ano atual</small>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Nível/Turma</label>
                        <select class="form-select" name="class_id" id="classFilter">
                            <option value="">Todos os níveis/turmas</option>
                            <?php if (!empty($classes)): ?>
                                <?php foreach ($classes as $class): ?>
                                    <option value="<?= $class->id ?>" <?= ($selectedClass ?? '') == $class->id ? 'selected' : '' ?>>
                                        <?= $class->class_name ?> (<?= $class->class_code ?>) - <?= $class->class_shift ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <!-- FILTRO DE CURSO -->
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Curso</label>
                        <select class="form-select" name="course_id">
                            <option value="">Todos os cursos</option>
                            <?php if (!empty($courses)): ?>
                                <?php foreach ($courses as $course): ?>
                                    <option value="<?= $course->id ?>" <?= ($selectedCourse ?? '') == $course->id ? 'selected' : '' ?>>
                                        <?= esc($course->course_name) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Status Matrícula</label>
                        <select class="form-select" name="enrollment_status">
                            <option value="">Todos</option>
                            <option value="Ativo" <?= ($selectedEnrollmentStatus ?? '') == 'Ativo' ? 'selected' : '' ?>>Matriculado</option>
                            <option value="Pendente" <?= ($selectedEnrollmentStatus ?? '') == 'Pendente' ? 'selected' : '' ?>>Pendente</option>
                            <option value="Concluído" <?= ($selectedEnrollmentStatus ?? '') == 'Concluído' ? 'selected' : '' ?>>Concluído</option>
                            <option value="Transferido" <?= ($selectedEnrollmentStatus ?? '') == 'Transferido' ? 'selected' : '' ?>>Transferido</option>
                            <option value="nao_matriculado" <?= ($selectedEnrollmentStatus ?? '') == 'nao_matriculado' ? 'selected' : '' ?>>Não Matriculado</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Gênero</label>
                        <select class="form-select" name="gender">
                            <option value="">Todos</option>
                            <option value="Masculino" <?= ($selectedGender ?? '') == 'Masculino' ? 'selected' : '' ?>>Masculino</option>
                            <option value="Feminino" <?= ($selectedGender ?? '') == 'Feminino' ? 'selected' : '' ?>>Feminino</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Status</label>
                        <select class="form-select" name="status">
                            <option value="">Todos</option>
                            <option value="active" <?= ($selectedStatus ?? '') == 'active' ? 'selected' : '' ?>>Ativo</option>
                            <option value="inactive" <?= ($selectedStatus ?? '') == 'inactive' ? 'selected' : '' ?>>Inativo</option>
                        </select>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-filter me-2"></i>Aplicar Filtros
                        </button>
                        <a href="<?= site_url('admin/students') ?>" class="btn btn-secondary">
                            <i class="fas fa-undo me-2"></i>Limpar Filtros
                        </a>
                        
                        <!-- Badges com resumo dos filtros ativos -->
                        <div class="d-inline-flex gap-2 ms-3">
                            <?php if (!empty($search)): ?>
                                <span class="badge bg-info p-2">
                                    <i class="fas fa-search me-1"></i>Busca: <?= $search ?>
                                </span>
                            <?php endif; ?>
                            
                            <?php if (!empty($selectedYear)): ?>
                                <?php foreach ($academicYears as $year): ?>
                                    <?php if ($year->id == $selectedYear): ?>
                                        <span class="badge bg-primary p-2">
                                            <i class="fas fa-calendar me-1"></i>Ano: <?= $year->year_name ?>
                                        </span>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            
                            <?php if (!empty($selectedClass)): ?>
                                <?php foreach ($classes as $class): ?>
                                    <?php if ($class->id == $selectedClass): ?>
                                        <span class="badge bg-success p-2">
                                            <i class="fas fa-school me-1"></i>Turma: <?= $class->class_name ?>
                                        </span>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            
                            <?php if (!empty($selectedCourse)): ?>
                                <?php foreach ($courses as $course): ?>
                                    <?php if ($course->id == $selectedCourse): ?>
                                        <span class="badge bg-info p-2">
                                            <i class="fas fa-graduation-cap me-1"></i>Curso: <?= $course->course_name ?>
                                        </span>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            
                            <?php if (!empty($selectedEnrollmentStatus)): ?>
                                <span class="badge bg-warning text-dark p-2">
                                    <i class="fas fa-tag me-1"></i>Status: <?= $selectedEnrollmentStatus == 'nao_matriculado' ? 'Não Matriculado' : $selectedEnrollmentStatus ?>
                                </span>
                            <?php endif; ?>
                            
                            <?php if (!empty($selectedGender)): ?>
                                <span class="badge bg-secondary p-2">
                                    <i class="fas fa-venus-mars me-1"></i><?= $selectedGender ?>
                                </span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Cards de Estatísticas Rápidas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-white-50">Total Alunos</h6>
                        <h2 class="mb-0"><?= $totalStudents ?? 0 ?></h2>
                    </div>
                    <i class="fas fa-user-graduate fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-white-50">Matriculados</h6>
                        <h2 class="mb-0"><?= $enrolledStudents ?? 0 ?></h2>
                    </div>
                    <i class="fas fa-check-circle fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-white-50">Pendentes</h6>
                        <h2 class="mb-0"><?= $pendingEnrollments ?? 0 ?></h2>
                    </div>
                    <i class="fas fa-clock fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-white-50">Masculino/Feminino</h6>
                        <h2 class="mb-0"><?= $maleCount ?? 0 ?>/<?= $femaleCount ?? 0 ?></h2>
                    </div>
                    <i class="fas fa-venus-mars fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Data Table Avançada -->
<div class="card">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-user-graduate me-2"></i>Lista de Alunos
            <span class="badge bg-secondary ms-2"><?= $totalFiltered ?? count($students) ?> registros</span>
        </div>
        <div>
            <button class="btn btn-sm btn-outline-primary" onclick="exportTableToExcel()">
                <i class="fas fa-file-excel me-1"></i>Exportar
            </button>
            <button class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                <i class="fas fa-print me-1"></i>Imprimir
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="studentsTable" class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Nº Matrícula</th>
                        <th>Foto</th>
                        <th>Nome</th>
                        <th>Contacto</th>
                        <th>Turma / Status</th>
                        <th>Curso</th>
                        <th>Nível</th>
                        <th>Ano Letivo</th>
                        <th>Gênero</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($students)): ?>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td><?= $student->id ?></td>
                                <td>
                                    <span class="badge bg-info p-2"><?= $student->student_number ?></span>
                                </td>
                                <td>
                                    <?php if ($student->photo): ?>
                                        <img src="<?= base_url('uploads/students/' . $student->photo) ?>" 
                                             alt="Foto" class="rounded-circle" width="40" height="40"
                                             style="object-fit: cover;">
                                    <?php else: ?>
                                        <div class="rounded-circle bg-secondary text-white d-flex align-items-center justify-content-center" 
                                             style="width: 40px; height: 40px;">
                                            <?= strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= $student->first_name ?> <?= $student->last_name ?></strong>
                                    <?php if ($student->emergency_contact): ?>
                                        <br><small class="text-muted">
                                            <i class="fas fa-phone-alt me-1"></i>Emergência: <?= $student->emergency_contact ?>
                                        </small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($student->phone): ?>
                                        <i class="fas fa-phone me-1"></i><?= $student->phone ?><br>
                                    <?php endif; ?>
                                    <small><i class="fas fa-envelope me-1"></i><?= $student->email ?></small>
                                </td>
                                
                                <!-- Coluna Turma / Status Combinada -->
                                <td>
                                    <?php 
                                    $enrollmentStatus = $student->enrollment_status ?? 'nao_matriculado';
                                    
                                    if ($enrollmentStatus == 'Ativo' && !empty($student->current_class)): 
                                    ?>
                                        <div class="d-flex flex-column">
                                            <span class="badge bg-success p-2 mb-1">
                                                <i class="fas fa-school me-1"></i><?= $student->current_class ?>
                                            </span>
                                            <small class="text-muted">
                                                <i class="fas fa-clock me-1"></i><?= $student->current_shift ?? '' ?>
                                            </small>
                                            <span class="badge bg-success mt-1" style="font-size: 0.7rem;">
                                                <i class="fas fa-check-circle"></i> Matriculado
                                            </span>
                                        </div>
                                    <?php elseif ($enrollmentStatus == 'Pendente'): ?>
                                        <div class="d-flex flex-column">
                                            <span class="badge bg-warning p-2">
                                                <i class="fas fa-clock me-1"></i>Matrícula Pendente
                                            </span>
                                            <?php if (!empty($student->pending_level_name)): ?>
                                                <small class="text-muted mt-1">Nível: <?= $student->pending_level_name ?></small>
                                            <?php endif; ?>
                                        </div>
                                    <?php elseif ($enrollmentStatus == 'Concluído'): ?>
                                        <div class="d-flex flex-column">
                                            <span class="badge bg-info p-2">
                                                <i class="fas fa-check-double me-1"></i>Concluído
                                            </span>
                                            <?php if (!empty($student->current_class)): ?>
                                                <small class="text-muted mt-1">Última turma: <?= $student->current_class ?></small>
                                            <?php endif; ?>
                                        </div>
                                    <?php elseif ($enrollmentStatus == 'Transferido'): ?>
                                        <span class="badge bg-primary p-2">
                                            <i class="fas fa-exchange-alt me-1"></i>Transferido
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary p-2">
                                            <i class="fas fa-user-slash me-1"></i>Não matriculado
                                        </span>
                                    <?php endif; ?>
                                </td>
                                
                                <!-- COLUNA CURSO CORRIGIDA -->
                                <td>
                                    <?php 
                                    // Prioridade: matrícula ativa > matrícula pendente
                                    if (!empty($student->current_course_name)): ?>
                                        <span class="badge bg-primary p-2" title="<?= $student->current_course_type ?? '' ?>">
                                            <i class="fas fa-graduation-cap me-1"></i><?= esc($student->current_course_name) ?>
                                        </span>
                                        <?php if (!empty($student->current_course_code)): ?>
                                            <br><small class="text-muted"><?= esc($student->current_course_code) ?></small>
                                        <?php endif; ?>
                                    <?php elseif (!empty($student->pending_course_name)): ?>
                                        <span class="badge bg-warning p-2">
                                            <i class="fas fa-clock me-1"></i><?= esc($student->pending_course_name) ?>
                                        </span>
                                        <?php if (!empty($student->pending_course_code)): ?>
                                            <br><small class="text-warning">(Pendente)</small>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                
                                <!-- COLUNA NÍVEL CORRIGIDA -->
                                <td>
                                    <?php 
                                    // Prioridade: matrícula ativa > pendente
                                    if (!empty($student->current_level_name)): ?>
                                        <span class="badge bg-success p-2"><?= $student->current_level_name ?></span>
                                    <?php elseif (!empty($student->pending_level_name)): ?>
                                        <span class="badge bg-warning p-2"><?= $student->pending_level_name ?></span>
                                        <br><small class="text-warning">(Pendente)</small>
                                    <?php elseif (!empty($student->level_name)): ?>
                                        <span class="badge bg-info p-2"><?= $student->level_name ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                
                                <!-- COLUNA ANO LETIVO CORRIGIDA -->
                                <td>
                                    <?php 
                                    if (!empty($student->current_year)): ?>
                                        <span class="badge bg-success p-2"><?= $student->current_year ?></span>
                                    <?php elseif (!empty($student->pending_year_name)): ?>
                                        <span class="badge bg-warning p-2"><?= $student->pending_year_name ?></span>
                                        <br><small class="text-warning">(Pendente)</small>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                
                                <td><?= $student->gender ?? '-' ?></td>
                                <td>
                                    <?php if ($student->is_active): ?>
                                        <span class="badge bg-success">Ativo</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inativo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= site_url('admin/students/view/' . $student->id) ?>" 
                                           class="btn btn-sm btn-success" title="Ver Detalhes">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= site_url('admin/students/form-edit/' . $student->id) ?>" 
                                           class="btn btn-sm btn-info" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= site_url('admin/students/enrollments/history/' . $student->id) ?>" 
                                           class="btn btn-sm btn-primary" title="Histórico">
                                            <i class="fas fa-history"></i>
                                        </a>
                                       <?php if (!isset($student->enrollment_status) || $student->enrollment_status != 'Ativo'): ?>
                                            <a href="<?= site_url('admin/students/enrollments/form-add?student=' . $student->id) ?>" 
                                            class="btn btn-sm btn-warning" title="Matricular">
                                                <i class="fas fa-user-plus"></i>
                                            </a>
                                        <?php endif; ?>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger" 
                                                onclick="confirmDelete(<?= $student->id ?>)"
                                                title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="12" class="text-center py-4">
                                <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Nenhum aluno encontrado</h5>
                                <p>Tente ajustar os filtros ou <a href="<?= site_url('admin/students/form-add') ?>">cadastrar um novo aluno</a></p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Paginação com contador -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted">
                Mostrando <?= count($students) ?> de <?= $totalFiltered ?? 0 ?> registros
            </div>
            <div>
                <?= $pager->links() ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Eliminação -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Eliminação
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja eliminar este aluno?</p>
                <p class="text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    Esta ação não pode ser desfeita. O aluno será desativado do sistema.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-danger">
                    <i class="fas fa-trash me-1"></i>Eliminar
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    // Inicializar DataTable
    $('#studentsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[3, 'asc']], // Ordenar por nome
        pageLength: 25,
        searching: false,
        responsive: true,
        columnDefs: [
            { orderable: false, targets: [2, 11] }
        ]
    });
    
    // Atualizar classe de turma quando ano letivo mudar
    $('select[name="academic_year"]').change(function() {
        const yearId = $(this).val();
        const classSelect = $('select[name="class_id"]');
        
        if (yearId) {
            $.get('<?= site_url('admin/classes/get-by-year/') ?>' + yearId, function(classes) {
                classSelect.empty().append('<option value="">Todos os níveis/turmas</option>');
                $.each(classes, function(i, cls) {
                    classSelect.append('<option value="' + cls.id + '">' + cls.class_name + ' (' + cls.class_code + ') - ' + cls.class_shift + '</option>');
                });
            });
        }
    });
    
});

// Confirmação de eliminação
function confirmDelete(id) {
    $('#confirmDeleteBtn').attr('href', '<?= site_url('admin/students/delete/') ?>' + id);
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// Exportar para Excel
function exportTableToExcel() {
    const table = document.getElementById('studentsTable');
    const rows = Array.from(table.querySelectorAll('tr'));
    
    let csv = [];
    
    rows.forEach(row => {
        const cells = Array.from(row.querySelectorAll('th, td'));
        const filteredCells = cells.filter((_, index) => index !== 2 && index !== cells.length - 1);
        const rowData = filteredCells.map(cell => cell.innerText.trim().replace(/,/g, ';'));
        csv.push(rowData.join(','));
    });
    
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', 'alunos_' + new Date().toISOString().split('T')[0] + '.csv');
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}

// Auto-submit quando filtros mudarem
let filterTimeout;
$('#filterForm select').change(function() {
    clearTimeout(filterTimeout);
    filterTimeout = setTimeout(() => $('#filterForm').submit(), 500);
});

</script>

<style>
.card {
    border: none;
    box-shadow: 0 0 10px rgba(0,0,0,0.1);
}

.table-hover tbody tr:hover {
    background-color: rgba(0,123,255,0.05);
}

.badge {
    font-size: 0.85em;
    padding: 0.5em 0.75em;
}

.page-header {
    margin-bottom: 1.5rem;
}

.btn-group .btn {
    margin-right: 2px;
}

.table td {
    vertical-align: middle;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.card {
    animation: fadeIn 0.3s ease-out;
}

td .badge {
    transition: all 0.2s;
}

td .badge:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}
</style>

<?= $this->endSection() ?>