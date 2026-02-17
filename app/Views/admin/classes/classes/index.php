<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <a href="<?= site_url('admin/classes/classes/form-add') ?>" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Nova Turma
        </a>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Turmas</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-3">
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
            <div class="col-md-3">
                <label for="grade_level" class="form-label">Nível de Ensino</label>
                <select class="form-select" id="grade_level" name="grade_level">
                    <option value="">Todos</option>
                    <?php if (!empty($gradeLevels)): ?>
                        <?php foreach ($gradeLevels as $level): ?>
                            <option value="<?= $level->id ?>" <?= $selectedLevel == $level->id ? 'selected' : '' ?>>
                                <?= $level->level_name ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-2">
                <label for="shift" class="form-label">Turno</label>
                <select class="form-select" id="shift" name="shift">
                    <option value="">Todos</option>
                    <option value="Manhã" <?= $selectedShift == 'Manhã' ? 'selected' : '' ?>>Manhã</option>
                    <option value="Tarde" <?= $selectedShift == 'Tarde' ? 'selected' : '' ?>>Tarde</option>
                    <option value="Noite" <?= $selectedShift == 'Noite' ? 'selected' : '' ?>>Noite</option>
                    <option value="Integral" <?= $selectedShift == 'Integral' ? 'selected' : '' ?>>Integral</option>
                </select>
            </div>
            <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Todos</option>
                    <option value="active" <?= $selectedStatus == 'active' ? 'selected' : '' ?>>Ativas</option>
                    <option value="inactive" <?= $selectedStatus == 'inactive' ? 'selected' : '' ?>>Inativas</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                <a href="<?= site_url('admin/classes/classes') ?>" class="btn btn-secondary">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-school"></i> Lista de Turmas
    </div>
    <div class="card-body">
        <table id="classesTable" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Turma</th>
                    <th>Código</th>
                    <th>Nível</th>
                    <th>Ano Letivo</th>
                    <th>Turno</th>
                    <th>Sala</th>
                    <th>Capacidade</th>
                    <th>Professor</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($classes)): ?>
                    <?php foreach ($classes as $class): ?>
                        <tr>
                            <td><?= $class->id ?></td>
                            <td><?= $class->class_name ?></td>
                            <td><span class="badge bg-info"><?= $class->class_code ?></span></td>
                            <td><?= $class->level_name ?></td>
                            <td><?= $class->year_name ?></td>
                            <td><?= $class->class_shift ?></td>
                            <td><?= $class->class_room ?: '-' ?></td>
                            <td><?= $class->capacity ?></td>
                            <td>
                                <?php if ($class->class_teacher_id): ?>
                                    <?= $class->teacher_first_name ?> <?= $class->teacher_last_name ?>
                                <?php else: ?>
                                    <span class="text-muted">Não atribuído</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($class->is_active): ?>
                                    <span class="badge bg-success">Ativa</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inativa</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= site_url('admin/classes/classes/view/' . $class->id) ?>" 
                                   class="btn btn-sm btn-success" title="Ver Detalhes">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="<?= site_url('admin/classes/classes/form-edit/' . $class->id) ?>" 
                                   class="btn btn-sm btn-info" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?= site_url('admin/classes/classes/list-students/' . $class->id) ?>" 
                                   class="btn btn-sm btn-primary" title="Listar Alunos">
                                    <i class="fas fa-users"></i>
                                </a>
                                <a href="<?= site_url('admin/classes/class-subjects?class=' . $class->id) ?>" 
                                   class="btn btn-sm btn-warning" title="Disciplinas">
                                    <i class="fas fa-book"></i>
                                </a>
                                <?php if ($class->is_active): ?>
                                    <a href="<?= site_url('admin/classes/classes/delete/' . $class->id) ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Tem certeza que deseja eliminar esta turma?')"
                                       title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="11" class="text-center">Nenhuma turma encontrada</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <?= $pager->links() ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#classesTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[1, 'asc']],
        pageLength: 25
    });
});
</script>
<?= $this->endSection() ?>