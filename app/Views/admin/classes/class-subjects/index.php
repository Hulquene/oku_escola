<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <a href="<?= site_url('admin/classes/class-subjects/assign?class=' . ($selectedClass ?? '')) ?>" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Nova Alocação
        </a>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/classes') ?>">Classes</a></li>
            <li class="breadcrumb-item active" aria-current="page">Disciplinas por Turma</li>
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
                <label for="class_id" class="form-label">Turma</label>
                <select class="form-select" id="class_id" name="class">
                    <option value="">Todas</option>
                    <?php if (!empty($classes)): ?>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?= $class->id ?>" <?= $selectedClass == $class->id ? 'selected' : '' ?>>
                                <?= $class->class_name ?> (<?= $class->class_code ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                <a href="<?= site_url('admin/classes/class-subjects') ?>" class="btn btn-secondary">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-link"></i> Disciplinas Atribuídas por Turma
    </div>
    <div class="card-body">
        <?php if (!empty($assignments)): ?>
            <?php 
            $currentClass = null;
            foreach ($assignments as $assignment): 
                if ($currentClass != $assignment->class_name):
                    if ($currentClass !== null) echo '</tbody></table>';
                    $currentClass = $assignment->class_name;
            ?>
                <h5 class="mt-4 mb-3">
                    <span class="badge bg-primary p-2">
                        <i class="fas fa-school"></i> <?= $assignment->class_name ?> 
                        (<?= $assignment->class_code ?>) - <?= $assignment->class_shift ?>
                    </span>
                </h5>
                <table class="table table-striped table-hover mb-4">
                    <thead>
                        <tr>
                            <th>Disciplina</th>
                            <th>Código</th>
                            <th>Professor</th>
                            <th>Carga Horária</th>
                            <th>Semestre</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
            <?php endif; ?>
                        <tr>
                            <td><?= $assignment->discipline_name ?></td>
                            <td><span class="badge bg-info"><?= $assignment->discipline_code ?></span></td>
                            <td>
                                <?php if ($assignment->teacher_id): ?>
                                    <?= $assignment->teacher_first_name ?? '' ?> <?= $assignment->teacher_last_name ?? '' ?>
                                <?php else: ?>
                                    <span class="text-muted">Não atribuído</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $assignment->workload_hours ?: '-' ?> h</td>
                            <td><?= $assignment->semester_name ?: '-' ?></td>
                            <td>
                                <?php if ($assignment->is_active): ?>
                                    <span class="badge bg-success">Ativa</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inativa</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= site_url('admin/classes/class-subjects/assign?class=' . $assignment->class_id . '&edit=' . $assignment->id) ?>" 
                                   class="btn btn-sm btn-info" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?= site_url('admin/classes/class-subjects/delete/' . $assignment->id) ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Tem certeza que deseja remover esta disciplina da turma?')"
                                   title="Remover">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
            <?php endforeach; ?>
                    </tbody>
                </table>
        <?php else: ?>
            <p class="text-center text-muted">Nenhuma disciplina atribuída encontrada.</p>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>