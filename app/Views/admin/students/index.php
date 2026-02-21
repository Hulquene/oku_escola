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
        <i class="fas fa-filter me-2"></i>Filtros
        <button class="btn btn-sm btn-link float-end" type="button" data-bs-toggle="collapse" data-bs-target="#filterCollapse">
            <i class="fas fa-chevron-down"></i>
        </button>
    </div>
    <div class="collapse show" id="filterCollapse">
        <div class="card-body">
            <form method="get" id="filterForm">
                <div class="row g-3">
                    <!-- Busca geral -->
                    <div class="col-md-12">
                        <div class="input-group">
                            <span class="input-group-text"><i class="fas fa-search"></i></span>
                            <input type="text" class="form-control" name="search" 
                                   placeholder="Buscar por nome, número de matrícula, email, documento ou NIF..." 
                                   value="<?= $search ?? '' ?>">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search me-2"></i>Buscar
                            </button>
                        </div>
                    </div>
                </div>
                
                <div class="row g-3 mt-2">
                    <!-- Filtro por Curso (com Ensino Geral) -->
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Curso</label>
                        <select class="form-select" name="course_id">
                            <option value="">Todos os cursos</option>
                            <option value="0" <?= ($selectedCourse === '0' || $selectedCourse === 0) ? 'selected' : '' ?>>Ensino Geral</option>
                            <?php if (!empty($courses)): ?>
                                <?php foreach ($courses as $course): ?>
                                    <option value="<?= $course->id ?>" <?= ($selectedCourse ?? '') == $course->id ? 'selected' : '' ?>>
                                        <?= esc($course->course_name) ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <!-- Filtro por Status da Matrícula -->
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
                    
                    <!-- Filtro por Gênero -->
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Gênero</label>
                        <select class="form-select" name="gender">
                            <option value="">Todos</option>
                            <option value="Masculino" <?= ($selectedGender ?? '') == 'Masculino' ? 'selected' : '' ?>>Masculino</option>
                            <option value="Feminino" <?= ($selectedGender ?? '') == 'Feminino' ? 'selected' : '' ?>>Feminino</option>
                        </select>
                    </div>
                    
                    <!-- Filtro por Status do Estudante -->
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Status Aluno</label>
                        <select class="form-select" name="status">
                            <option value="">Todos</option>
                            <option value="active" <?= ($selectedStatus ?? '') == 'active' ? 'selected' : '' ?>>Ativo</option>
                            <option value="inactive" <?= ($selectedStatus ?? '') == 'inactive' ? 'selected' : '' ?>>Inativo</option>
                        </select>
                    </div>
                    
                    <!-- NOVO FILTRO: Data de Cadastro -->
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Data Cadastro</label>
                        <input type="date" class="form-control" name="created_date" 
                               value="<?= $selectedCreatedDate ?? '' ?>">
                    </div>
                    
                    <!-- NOVO FILTRO: Mês/Ano de Cadastro -->
                    <div class="col-md-2">
                        <label class="form-label fw-bold">Mês/Ano</label>
                        <input type="month" class="form-control" name="created_month" 
                               value="<?= $selectedCreatedMonth ?? '' ?>">
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
                        
                        <!-- Badges de filtros ativos -->
                        <div class="d-inline-flex gap-2 ms-3 flex-wrap">
                            <?php if (!empty($search)): ?>
                                <span class="badge bg-info p-2">
                                    <i class="fas fa-search me-1"></i>Busca: <?= $search ?>
                                </span>
                            <?php endif; ?>
                            
                            <?php if (!empty($selectedCourse)): ?>
                                <span class="badge bg-info p-2">
                                    <i class="fas fa-graduation-cap me-1"></i>Curso: <?= $selectedCourse == '0' ? 'Ensino Geral' : ($courses[$selectedCourse]->course_name ?? 'Selecionado') ?>
                                </span>
                            <?php endif; ?>
                            
                            <?php if (!empty($selectedEnrollmentStatus)): ?>
                                <span class="badge bg-warning text-dark p-2">
                                    <i class="fas fa-tag me-1"></i>Status Matrícula: <?= $selectedEnrollmentStatus == 'nao_matriculado' ? 'Não Matriculado' : $selectedEnrollmentStatus ?>
                                </span>
                            <?php endif; ?>
                            
                            <?php if (!empty($selectedGender)): ?>
                                <span class="badge bg-secondary p-2">
                                    <i class="fas fa-venus-mars me-1"></i><?= $selectedGender ?>
                                </span>
                            <?php endif; ?>
                            
                            <?php if (!empty($selectedCreatedDate)): ?>
                                <span class="badge bg-primary p-2">
                                    <i class="fas fa-calendar me-1"></i>Data: <?= $selectedCreatedDate ?>
                                </span>
                            <?php endif; ?>
                            
                            <?php if (!empty($selectedCreatedMonth)): ?>
                                <span class="badge bg-primary p-2">
                                    <i class="fas fa-calendar-alt me-1"></i>Mês/Ano: <?= $selectedCreatedMonth ?>
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

<!-- Data Table -->
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
                        <th>Nº Matrícula</th>
                        <th>Foto</th>
                        <th>Nome Completo / Contacto</th>
                        <th>Curso</th>
                        <th>Nível / Ano Letivo</th>
                        <th>Gênero</th>
                        <th>Status</th>
                        <th>Data Cadastro</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($students)): ?>
                        <?php foreach ($students as $student): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-info p-2"><?= $student->student_number ?></span>
                                </td>
                                <td>
                                    <?php if ($student->photo): ?>
                                        <img src="<?= base_url('uploads/students/' . $student->photo) ?>" 
                                             alt="Foto" class="rounded-circle" width="40" height="40"
                                             style="object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-secondary rounded-circle d-flex align-items-center justify-content-center text-white" 
                                             style="width: 40px; height: 40px;">
                                            <?= strtoupper(substr($student->first_name, 0, 1) . substr($student->last_name, 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= $student->first_name ?> <?= $student->last_name ?></strong>
                                    <br>
                                    <small class="text-muted">
                                        <i class="fas fa-envelope me-1"></i><?= $student->email ?><br>
                                        <i class="fas fa-phone me-1"></i><?= $student->phone ?: '-'
                                        ?>
                                    </small>
                                </td>
                                <td>
                                    <?php 
                                    if (!empty($student->current_course_name)): ?>
                                        <span class="badge bg-primary p-2">
                                            <i class="fas fa-graduation-cap me-1"></i><?= esc($student->current_course_name) ?>
                                        </span>
                                    <?php elseif (!empty($student->pending_course_name)): ?>
                                        <span class="badge bg-warning p-2">
                                            <i class="fas fa-clock me-1"></i><?= esc($student->pending_course_name) ?> (Pendente)
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary p-2">
                                            <i class="fas fa-school me-1"></i>Ensino Geral
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php 
                                    // Construir histórico de níveis
                                    $niveis = [];
                                    
                                    if (!empty($student->current_level_name) && !empty($student->current_year)) {
                                        $niveis[] = '<span class="badge bg-success me-1 mb-1">' . $student->current_level_name . ' (' . $student->current_year . ')</span>';
                                    }
                                    
                                    if (!empty($student->pending_level_name) && !empty($student->pending_year_name)) {
                                        $niveis[] = '<span class="badge bg-warning me-1 mb-1">' . $student->pending_level_name . ' (' . $student->pending_year_name . ') [Pendente]</span>';
                                    }
                                    
                                    if (empty($niveis)) {
                                        echo '<span class="text-muted">-</span>';
                                    } else {
                                        echo implode('<br>', $niveis);
                                    }
                                    ?>
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
                                    <?= date('d/m/Y', strtotime($student->created_at)) ?>
                                    <br>
                                    <small class="text-muted"><?= date('H:i', strtotime($student->created_at)) ?></small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= site_url('admin/students/view/' . $student->id) ?>" 
                                           class="btn btn-sm btn-outline-success" title="Ver Detalhes">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= site_url('admin/students/form-edit/' . $student->id) ?>" 
                                           class="btn btn-sm btn-outline-info" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= site_url('admin/students/enrollments/history/' . $student->id) ?>" 
                                           class="btn btn-sm btn-outline-primary" title="Histórico">
                                            <i class="fas fa-history"></i>
                                        </a>
                                        
                                        <?php if (!empty($student->current_class) || !empty($student->enrollment_status == 'Ativo')): ?>
                                            <a href="<?= site_url('admin/students/enrollments/view/' . $student->enrollment_id) ?>" 
                                               class="btn btn-sm btn-outline-warning" title="Ver Matrícula Atual">
                                                <i class="fas fa-file-signature"></i>
                                            </a>
                                        <?php endif; ?>
                                        
                                        <button type="button" 
                                                class="btn btn-sm btn-outline-danger" 
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
                            <td colspan="9" class="text-center py-4">
                                <i class="fas fa-user-slash fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Nenhum aluno encontrado</h5>
                                <p>Tente ajustar os filtros ou <a href="<?= site_url('admin/students/form-add') ?>">cadastrar um novo aluno</a></p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Paginação -->
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
<script>
$(document).ready(function() {
    // Inicializar DataTable
    $('#studentsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[2, 'asc']], // Ordenar por nome
        pageLength: 25,
        searching: false,
        responsive: true,
        columnDefs: [
            { orderable: false, targets: [1, 8] }
        ]
    });
});

// Confirmação de eliminação
function confirmDelete(id) {
    $('#confirmDeleteBtn').attr('href', '<?= site_url('admin/students/delete/') ?>' + id);
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// Auto-submit quando filtros mudarem
let filterTimeout;
$('#filterForm select, #filterForm input[type="date"], #filterForm input[type="month"]').on('change', function() {
    clearTimeout(filterTimeout);
    filterTimeout = setTimeout(() => $('#filterForm').submit(), 500);
});

// Exportar para Excel
function exportTableToExcel() {
    const table = document.getElementById('studentsTable');
    const rows = Array.from(table.querySelectorAll('tr'));
    
    let csv = [];
    
    rows.forEach(row => {
        const cells = Array.from(row.querySelectorAll('th, td'));
        const filteredCells = cells.filter((_, index) => index !== 1 && index !== cells.length - 1);
        const rowData = filteredCells.map(cell => cell.innerText.trim().replace(/,/g, ';'));
        csv.push(rowData.join(','));
    });
    
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', 'alunos_' + new Date().toISOString().split('T')[0] + '.csv');
    link.click();
}
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
</style>
<?= $this->endSection() ?>