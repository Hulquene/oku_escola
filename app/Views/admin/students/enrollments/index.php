<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <div>
            <a href="<?= site_url('admin/students/enrollments/pending') ?>" class="btn btn-warning me-2">
                <i class="fas fa-clock"></i> Pendentes
            </a>
            <a href="<?= site_url('admin/students/enrollments/form-add') ?>" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Nova Matrícula
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/students') ?>">Alunos</a></li>
            <li class="breadcrumb-item active" aria-current="page">Matrículas</li>
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
            <form method="get" id="filterForm" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Ano Letivo</label>
                    <select class="form-select" name="academic_year">
                        <option value="">Todos os anos</option>
                        <?php if (!empty($academicYears)): ?>
                            <?php foreach ($academicYears as $year): ?>
                                <option value="<?= $year->id ?>" <?= ($selectedYear ?? '') == $year->id ? 'selected' : '' ?>>
                                    <?= $year->year_name ?> <?= $year->is_current ? ' (Atual)' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label fw-bold">Nível</label>
                    <select class="form-select" name="grade_level">
                        <option value="">Todos os níveis</option>
                        <?php if (!empty($gradeLevels)): ?>
                            <?php foreach ($gradeLevels as $level): ?>
                                <option value="<?= $level->id ?>" <?= ($selectedGradeLevel ?? '') == $level->id ? 'selected' : '' ?>>
                                    <?= $level->level_name ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label fw-bold">Turma</label>
                    <select class="form-select" name="class_id">
                        <option value="">Todas as turmas</option>
                        <?php if (!empty($classes)): ?>
                            <?php foreach ($classes as $class): ?>
                                <option value="<?= $class->id ?>" <?= ($selectedClass ?? '') == $class->id ? 'selected' : '' ?>>
                                    <?= $class->class_name ?> (<?= $class->class_code ?>) - <?= $class->class_shift ?>
                                </option>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label class="form-label fw-bold">Status</label>
                    <select class="form-select" name="status">
                        <option value="">Todos</option>
                        <option value="Ativo" <?= ($selectedStatus ?? '') == 'Ativo' ? 'selected' : '' ?>>Ativo</option>
                        <option value="Pendente" <?= ($selectedStatus ?? '') == 'Pendente' ? 'selected' : '' ?>>Pendente</option>
                        <option value="Concluído" <?= ($selectedStatus ?? '') == 'Concluído' ? 'selected' : '' ?>>Concluído</option>
                        <option value="Transferido" <?= ($selectedStatus ?? '') == 'Transferido' ? 'selected' : '' ?>>Transferido</option>
                        <option value="Cancelado" <?= ($selectedStatus ?? '') == 'Cancelado' ? 'selected' : '' ?>>Cancelado</option>
                    </select>
                </div>
                
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter me-2"></i>Aplicar Filtros
                    </button>
                    <a href="<?= site_url('admin/students/enrollments') ?>" class="btn btn-secondary">
                        <i class="fas fa-undo me-2"></i>Limpar Filtros
                    </a>
                    
                    <!-- Badges com resumo dos filtros ativos -->
                    <div class="d-inline-flex gap-2 ms-3">
                        <?php if (!empty($selectedYear)): ?>
                            <span class="badge bg-primary p-2">Ano selecionado</span>
                        <?php endif; ?>
                        <?php if (!empty($selectedGradeLevel)): ?>
                            <span class="badge bg-info p-2">Nível selecionado</span>
                        <?php endif; ?>
                        <?php if (!empty($selectedClass)): ?>
                            <span class="badge bg-success p-2">Turma selecionada</span>
                        <?php endif; ?>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Estatísticas Rápidas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-white-50">Total</h6>
                        <h2 class="mb-0"><?= $totalEnrollments ?? 0 ?></h2>
                    </div>
                    <i class="fas fa-file-signature fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-white-50">Ativas</h6>
                        <h2 class="mb-0"><?= $activeEnrollments ?? 0 ?></h2>
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
                        <h6 class="card-title text-white-50">Concluídas</h6>
                        <h2 class="mb-0"><?= $completedEnrollments ?? 0 ?></h2>
                    </div>
                    <i class="fas fa-graduation-cap fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-file-signature"></i> Lista de Matrículas
            <span class="badge bg-secondary ms-2"><?= $totalFiltered ?? count($enrollments) ?> registros</span>
        </div>
        <div>
            <button class="btn btn-sm btn-outline-primary" onclick="exportTableToExcel()">
                <i class="fas fa-file-excel"></i> Exportar
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="enrollmentsTable" class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>Nº Matrícula</th>
                        <th>Aluno</th>
                        <th>Nível</th>
                        <th>Turma</th>
                        <th>Ano Letivo</th>
                        <th>Data</th>
                        <th>Tipo</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($enrollments)): ?>
                        <?php foreach ($enrollments as $enrollment): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-secondary p-2"><?= $enrollment->enrollment_number ?></span>
                                </td>
                                <td>
                                    <strong><?= $enrollment->first_name ?> <?= $enrollment->last_name ?></strong>
                                    <br>
                                    <small class="text-muted"><?= $enrollment->student_number ?></small>
                                </td>
                                <td>
                                    <?php if (!empty($enrollment->level_name)): ?>
                                        <span class="badge bg-info"><?= $enrollment->level_name ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($enrollment->class_name): ?>
                                        <span class="badge bg-success"><?= $enrollment->class_name ?></span>
                                        <br>
                                        <small class="text-muted"><?= $enrollment->class_shift ?? '' ?></small>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Não atribuída</span>
                                    <?php endif; ?>
                                </td>
                                <td><?= $enrollment->year_name ?></td>
                                <td><?= date('d/m/Y', strtotime($enrollment->enrollment_date)) ?></td>
                                <td>
                                    <span class="badge bg-secondary"><?= $enrollment->enrollment_type ?></span>
                                </td>
                                <td>
                                    <?php
                                    $statusClass = [
                                        'Ativo' => 'success',
                                        'Pendente' => 'warning',
                                        'Concluído' => 'info',
                                        'Transferido' => 'primary',
                                        'Cancelado' => 'danger'
                                    ][$enrollment->status] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $statusClass ?> p-2">
                                        <i class="fas fa-<?= $enrollment->status == 'Ativo' ? 'check-circle' : 
                                            ($enrollment->status == 'Pendente' ? 'clock' : 
                                            ($enrollment->status == 'Concluído' ? 'check-double' : 
                                            ($enrollment->status == 'Transferido' ? 'exchange-alt' : 'ban'))) ?> me-1"></i>
                                        <?= $enrollment->status ?>
                                    </span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= site_url('admin/students/enrollments/view/' . $enrollment->id) ?>" 
                                           class="btn btn-sm btn-success" title="Ver Detalhes">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= site_url('admin/students/enrollments/form-edit/' . $enrollment->id) ?>" 
                                           class="btn btn-sm btn-info" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= site_url('admin/students/view/' . $enrollment->student_id) ?>" 
                                           class="btn btn-sm btn-primary" title="Ver Aluno">
                                            <i class="fas fa-user-graduate"></i>
                                        </a>
                                        <?php if ($enrollment->status == 'Pendente'): ?>
                                            <a href="<?= site_url('admin/students/enrollments/approve/' . $enrollment->id) ?>" 
                                               class="btn btn-sm btn-warning" title="Aprovar Matrícula"
                                               onclick="return confirm('Confirmar aprovação desta matrícula?')">
                                                <i class="fas fa-check-circle"></i>
                                            </a>
                                        <?php endif; ?>
                                        <?php if ($enrollment->status != 'Ativo' && $enrollment->status != 'Concluído'): ?>
                                            <button type="button" 
                                                    class="btn btn-sm btn-danger" 
                                                    onclick="confirmDelete(<?= $enrollment->id ?>)"
                                                    title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <i class="fas fa-file-signature fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Nenhuma matrícula encontrada</h5>
                                <p>Tente ajustar os filtros ou <a href="<?= site_url('admin/students/enrollments/form-add') ?>">criar uma nova matrícula</a></p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Paginação com contador -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted">
                Mostrando <?= count($enrollments) ?> de <?= $totalFiltered ?? 0 ?> registros
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
                <p>Tem certeza que deseja eliminar esta matrícula?</p>
                <p class="text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    Esta ação não pode ser desfeita.
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
    $('#enrollmentsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[5, 'desc']], // Ordenar por data
        pageLength: 25,
        searching: false,
        responsive: true,
        columnDefs: [
            { orderable: false, targets: [8] } // Desabilitar ordenação para ações
        ]
    });
    
    // Atualizar turmas quando ano letivo mudar
    $('select[name="academic_year"]').change(function() {
        const yearId = $(this).val();
        const classSelect = $('select[name="class_id"]');
        
        if (yearId) {
            $.get('<?= site_url('admin/classes/get-by-year/') ?>' + yearId, function(classes) {
                classSelect.empty().append('<option value="">Todas as turmas</option>');
                $.each(classes, function(i, cls) {
                    classSelect.append('<option value="' + cls.id + '">' + cls.class_name + ' (' + cls.class_code + ') - ' + cls.class_shift + '</option>');
                });
            });
        }
    });
});

// Confirmação de eliminação
function confirmDelete(id) {
    $('#confirmDeleteBtn').attr('href', '<?= site_url('admin/students/enrollments/delete/') ?>' + id);
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// Exportar para Excel
function exportTableToExcel() {
    const table = document.getElementById('enrollmentsTable');
    const rows = Array.from(table.querySelectorAll('tr'));
    
    let csv = [];
    
    rows.forEach(row => {
        const cells = Array.from(row.querySelectorAll('th, td'));
        const filteredCells = cells.filter((_, index) => index !== cells.length - 1);
        const rowData = filteredCells.map(cell => cell.innerText.trim().replace(/,/g, ';'));
        csv.push(rowData.join(','));
    });
    
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', 'matriculas_' + new Date().toISOString().split('T')[0] + '.csv');
    link.click();
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

.btn-group .btn {
    margin-right: 2px;
}

.table td {
    vertical-align: middle;
}
</style>

<?= $this->endSection() ?>