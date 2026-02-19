<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <a href="<?= site_url('admin/courses/form-add') ?>" class="btn btn-primary">
            <i class="fas fa-plus-circle me-2"></i>Novo Curso
        </a>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/academic/years') ?>">Académico</a></li>
            <li class="breadcrumb-item active" aria-current="page">Cursos</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Filtros e Estatísticas -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-light">
                <i class="fas fa-filter me-2"></i>Filtros
            </div>
            <div class="card-body">
                <form method="get" class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Tipo de Curso</label>
                        <select class="form-select" name="type">
                            <option value="">Todos os tipos</option>
                            <?php foreach ($types as $key => $value): ?>
                                <option value="<?= $key ?>" <?= ($selectedType ?? '') == $key ? 'selected' : '' ?>>
                                    <?= $value ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label fw-bold">Status</label>
                        <select class="form-select" name="status">
                            <option value="">Todos</option>
                            <option value="active" <?= ($selectedStatus ?? '') == 'active' ? 'selected' : '' ?>>Ativos</option>
                            <option value="inactive" <?= ($selectedStatus ?? '') == 'inactive' ? 'selected' : '' ?>>Inativos</option>
                        </select>
                    </div>
                    <div class="col-md-4 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter me-2"></i>Filtrar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-white-50">Total de Cursos</h6>
                        <h2 class="mb-0"><?= $stats['total'] ?? 0 ?></h2>
                    </div>
                    <i class="fas fa-layer-group fa-3x text-white-50"></i>
                </div>
                <div class="mt-3 small">
                    <span class="me-3"><i class="fas fa-check-circle"></i> Ativos: <?= $stats['active'] ?? 0 ?></span>
                    <span><i class="fas fa-times-circle"></i> Inativos: <?= $stats['inactive'] ?? 0 ?></span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Cards de Estatísticas por Tipo -->
<?php if (!empty($stats['by_type'])): ?>
<div class="row mb-4">
    <div class="col-12">
        <div class="card">
            <div class="card-header bg-light">
                <i class="fas fa-chart-pie me-2"></i>Distribuição por Tipo
            </div>
            <div class="card-body">
                <div class="row">
                    <?php foreach ($stats['by_type'] as $type): ?>
                        <div class="col-md-2 col-6 mb-2">
                            <div class="text-center p-2 bg-light rounded">
                                <small class="text-muted d-block"><?= $type->course_type ?></small>
                                <span class="badge bg-primary rounded-pill"><?= $type->total ?></span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Lista de Cursos -->
<div class="card">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-layer-group me-2"></i>Lista de Cursos
            <span class="badge bg-secondary ms-2"><?= $pager->getTotal() ?? 0 ?> registros</span>
        </div>
        <div class="btn-group">
            <button class="btn btn-sm btn-outline-success" onclick="exportTableToExcel()">
                <i class="fas fa-file-excel me-1"></i>Exportar
            </button>
            <button class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                <i class="fas fa-print me-1"></i>Imprimir
            </button>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover align-middle" id="coursesTable">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Código</th>
                        <th>Nome do Curso</th>
                        <th>Tipo</th>
                        <th>Níveis</th>
                        <th>Duração</th>
                        <th>Disciplinas</th>
                        <th>Status</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($courses)): ?>
                        <?php foreach ($courses as $course): ?>
                            <?php
                            // Contar disciplinas do curso
                            $courseDisciplineModel = new \App\Models\CourseDisciplineModel();
                            $totalDisciplines = $courseDisciplineModel
                                ->where('course_id', $course->id)
                                ->countAllResults();
                            ?>
                            <tr>
                                <td><?= $course->id ?></td>
                                <td><span class="badge bg-info p-2"><?= $course->course_code ?></span></td>
                                <td>
                                    <strong><?= $course->course_name ?></strong>
                                    <?php if ($course->description): ?>
                                        <br><small class="text-muted"><?= character_limiter($course->description, 50) ?></small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $typeColors = [
                                        'Ciências' => 'primary',
                                        'Humanidades' => 'success',
                                        'Económico-Jurídico' => 'warning',
                                        'Técnico' => 'info',
                                        'Profissional' => 'secondary',
                                        'Outro' => 'dark'
                                    ];
                                    $color = $typeColors[$course->course_type] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $color ?>"><?= $course->course_type ?></span>
                                </td>
                                <td>
                                    <?php
                                    $gradeLevelModel = new \App\Models\GradeLevelModel();
                                    $startLevel = $gradeLevelModel->find($course->start_grade_id);
                                    $endLevel = $gradeLevelModel->find($course->end_grade_id);
                                    ?>
                                    <?= $startLevel->level_name ?? 'N/A' ?><br>
                                    <small class="text-muted">até <?= $endLevel->level_name ?? 'N/A' ?></small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info"><?= $course->duration_years ?> ano(s)</span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary"><?= $totalDisciplines ?></span>
                                </td>
                                <td>
                                    <?php if ($course->is_active): ?>
                                        <span class="badge bg-success">Ativo</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inativo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a href="<?= site_url('admin/courses/view/' . $course->id) ?>" 
                                           class="btn btn-sm btn-success" title="Ver Detalhes">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= site_url('admin/courses/form-edit/' . $course->id) ?>" 
                                           class="btn btn-sm btn-info" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= site_url('admin/courses/curriculum/' . $course->id) ?>" 
                                           class="btn btn-sm btn-primary" title="Currículo">
                                            <i class="fas fa-book-open"></i>
                                        </a>
                                        <button type="button" 
                                                class="btn btn-sm btn-danger" 
                                                onclick="confirmDelete(<?= $course->id ?>, '<?= $course->course_name ?>')"
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
                                <i class="fas fa-layer-group fa-3x text-muted mb-3"></i>
                                <h5 class="text-muted">Nenhum curso encontrado</h5>
                                <p>
                                    <?php if (!empty($selectedType) || !empty($selectedStatus)): ?>
                                        Tente ajustar os filtros ou 
                                        <a href="<?= site_url('admin/courses') ?>">limpar filtros</a>
                                    <?php else: ?>
                                        <a href="<?= site_url('admin/courses/form-add') ?>" class="btn btn-primary">
                                            <i class="fas fa-plus-circle me-2"></i>Criar Primeiro Curso
                                        </a>
                                    <?php endif; ?>
                                </p>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Paginação -->
        <div class="d-flex justify-content-between align-items-center mt-3">
            <div class="text-muted">
                Mostrando <?= count($courses) ?> de <?= $pager->getTotal() ?? 0 ?> registros
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
                <p>Tem certeza que deseja eliminar o curso <strong id="deleteCourseName"></strong>?</p>
                <p class="text-danger small">
                    <i class="fas fa-info-circle me-1"></i>
                    Esta ação não pode ser desfeita. Todas as disciplinas associadas serão removidas.
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
function confirmDelete(id, name) {
    document.getElementById('deleteCourseName').textContent = name;
    document.getElementById('confirmDeleteBtn').href = '<?= site_url('admin/courses/delete/') ?>' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

function exportTableToExcel() {
    const table = document.getElementById('coursesTable');
    const rows = Array.from(table.querySelectorAll('tr'));
    
    let csv = [];
    
    rows.forEach(row => {
        const cells = Array.from(row.querySelectorAll('th, td'));
        // Remover coluna de ações (última)
        const filteredCells = cells.filter((_, index) => index !== cells.length - 1);
        const rowData = filteredCells.map(cell => cell.innerText.trim().replace(/,/g, ';'));
        csv.push(rowData.join(','));
    });
    
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', 'cursos_' + new Date().toISOString().split('T')[0] + '.csv');
    link.style.visibility = 'hidden';
    
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>

<style>
.table td {
    vertical-align: middle;
}
.badge {
    font-size: 0.85em;
    padding: 0.5em 0.75em;
}
.btn-group .btn {
    margin-right: 2px;
}
</style>

<?= $this->endSection() ?>