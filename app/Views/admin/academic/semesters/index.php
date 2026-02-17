<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <a href="<?= site_url('admin/academic/semesters/form-add') ?>" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Novo Semestre/Trimestre
        </a>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/academic/years') ?>">Anos Letivos</a></li>
            <li class="breadcrumb-item active" aria-current="page">Semestres/Trimestres</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-4">
                <label for="academic_year" class="form-label">Ano Letivo</label>
                <select class="form-select" id="academic_year" name="academic_year">
                    <option value="">Todos</option>
                    <?php if (!empty($academicYears)): ?>
                        <?php foreach ($academicYears as $year): ?>
                            <option value="<?= $year->id ?>" <?= $selectedYear == $year->id ? 'selected' : '' ?>>
                                <?= $year->year_name ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-4">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Todos</option>
                    <option value="active" <?= $selectedStatus == 'active' ? 'selected' : '' ?>>Ativos</option>
                    <option value="inactive" <?= $selectedStatus == 'inactive' ? 'selected' : '' ?>>Inativos</option>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                <a href="<?= site_url('admin/academic/semesters') ?>" class="btn btn-secondary">
                    <i class="fas fa-undo"></i> Limpar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-list"></i> Lista de Semestres/Trimestres
    </div>
    <div class="card-body">
        <table id="semestersTable" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ano Letivo</th>
                    <th>Nome</th>
                    <th>Tipo</th>
                    <th>Período</th>
                    <th>Status</th>
                    <th>Atual</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($semesters)): ?>
                    <?php foreach ($semesters as $semester): ?>
                        <tr>
                            <td><?= $semester->id ?></td>
                            <td><?= $semester->year_name ?></td>
                            <td><?= $semester->semester_name ?></td>
                            <td>
                                <span class="badge bg-info"><?= $semester->semester_type ?></span>
                            </td>
                            <td>
                                <?= date('d/m/Y', strtotime($semester->start_date)) ?> - 
                                <?= date('d/m/Y', strtotime($semester->end_date)) ?>
                            </td>
                            <td>
                                <?php if ($semester->is_active): ?>
                                    <span class="badge bg-success">Ativo</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inativo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($semester->is_current): ?>
                                    <span class="badge bg-primary">Atual</span>
                                <?php else: ?>
                                    <a href="<?= site_url('admin/academic/semesters/set-current/' . $semester->id) ?>" 
                                       class="btn btn-sm btn-outline-primary"
                                       onclick="return confirm('Definir este período como atual?')">
                                        <i class="fas fa-check-circle"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= site_url('admin/academic/semesters/form-edit/' . $semester->id) ?>" 
                                   class="btn btn-sm btn-info" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?= site_url('admin/academic/semesters/view/' . $semester->id) ?>" 
                                   class="btn btn-sm btn-success" title="Ver Detalhes">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if (!$semester->is_current): ?>
                                    <a href="<?= site_url('admin/academic/semesters/delete/' . $semester->id) ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Tem certeza que deseja eliminar este período?')"
                                       title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">Nenhum período encontrado</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#semestersTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[1, 'asc'], [4, 'asc']]
    });
});
</script>
<?= $this->endSection() ?>