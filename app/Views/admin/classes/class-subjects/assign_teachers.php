<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1>Atribuir Professores</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/classes/class-subjects') ?>">Disciplinas por Turma</a></li>
            <li class="breadcrumb-item active">Atribuir Professores</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Informações da Turma - FIXA (sticky) -->
<div class="sticky-top bg-white shadow-sm mb-4" style="top: 60px; z-index: 1020; border-left: 4px solid #0dcaf0;">
    <div class="p-3">
        <div class="d-flex align-items-center">
            <div class="me-3">
                <i class="fas fa-school fa-2x text-info"></i>
            </div>
            <div>
                <h4 class="mb-1"><?= $class->class_name ?> (<?= $class->class_code ?>)</h4>
                <p class="mb-0">
                    <span class="badge bg-primary me-2">Curso: <?= $class->course_name ?? 'Não definido' ?></span>
                    <span class="badge bg-success me-2">Nível: <?= $class->level_name ?></span>
                    <span class="badge bg-info">Turno: <?= $class->class_shift ?></span>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Formulário -->
<div class="card">
    <div class="card-header bg-primary text-white">
        <i class="fas fa-chalkboard-teacher me-2"></i>Atribuir Professores às Disciplinas
    </div>
    <div class="card-body">
        <form action="<?= site_url('admin/classes/class-subjects/save-teachers') ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="class_id" value="<?= $class->id ?>">
            
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="table-light">
                        <tr>
                            <th>Disciplina</th>
                            <th>Código</th>
                            <th>Carga Horária</th>
                            <th>Professor</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (!empty($disciplines)): ?>
                            <?php foreach ($disciplines as $disc): ?>
                                <tr>
                                    <td><?= $disc->discipline_name ?></td>
                                    <td><span class="badge bg-info"><?= $disc->discipline_code ?></span></td>
                                    <td><?= $disc->workload_hours ?: '-' ?> h</td>
                                    <td>
                                        <select name="assignments[<?= $disc->discipline_id ?>]" class="form-select">
                                            <option value="">-- Selecione um professor --</option>
                                            <?php foreach ($teachers as $teacher): ?>
                                                <option value="<?= $teacher->id ?>" 
                                                    <?= $disc->teacher_id == $teacher->id ? 'selected' : '' ?>>
                                                    <?= $teacher->first_name ?> <?= $teacher->last_name ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="4" class="text-center">
                                    Nenhuma disciplina encontrada. 
                                    <a href="<?= site_url('admin/classes/class-subjects/assign?class=' . $class->id) ?>" class="btn btn-sm btn-primary">
                                        Atribuir Manualmente
                                    </a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <hr>
            
            <div class="d-flex justify-content-between">
                <a href="<?= site_url('admin/classes/class-subjects') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left me-1"></i>Voltar
                </a>
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-save me-1"></i>Salvar Atribuições
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>