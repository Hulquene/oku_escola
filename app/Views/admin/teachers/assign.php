<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <div>
            <a href="<?= site_url('admin/teachers/view/' . $teacher->id) ?>" class="btn btn-info">
                <i class="fas fa-eye"></i> Ver Professor
            </a>
            <a href="<?= site_url('admin/teachers') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/teachers') ?>">Professores</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/teachers/view/' . $teacher->id) ?>"><?= $teacher->first_name ?> <?= $teacher->last_name ?></a></li>
            <li class="breadcrumb-item active" aria-current="page">Atribuir Turmas</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Informação do Professor -->
<div class="alert alert-info mb-4">
    <div class="d-flex align-items-center">
        <?php if ($teacher->photo): ?>
            <img src="<?= base_url('uploads/teachers/' . $teacher->photo) ?>" 
                 alt="Foto" 
                 class="rounded-circle me-3"
                 style="width: 50px; height: 50px; object-fit: cover;">
        <?php else: ?>
            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white me-3"
                 style="width: 50px; height: 50px;">
                <?= strtoupper(substr($teacher->first_name, 0, 1) . substr($teacher->last_name, 0, 1)) ?>
            </div>
        <?php endif; ?>
        <div>
            <h5 class="mb-0"><?= $teacher->first_name ?> <?= $teacher->last_name ?></h5>
            <small><?= $teacher->email ?> | <?= $teacher->phone ?></small>
        </div>
    </div>
</div>

<!-- Form de Atribuição -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-tasks"></i> Atribuir Turmas e Disciplinas
    </div>
    <div class="card-body">
        <form action="<?= site_url('admin/teachers/save-assignment') ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="teacher_id" value="<?= $teacher->id ?>">
            
            <div class="alert alert-warning">
                <i class="fas fa-info-circle"></i>
                Selecione as disciplinas que este professor irá lecionar. As disciplinas disponíveis são baseadas nas turmas do ano letivo atual.
            </div>
            
            <?php if (!empty($classes)): ?>
                <?php foreach ($classes as $class): ?>
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <div class="d-flex justify-content-between align-items-center">
                                <h6 class="mb-0">
                                    <i class="fas fa-school"></i> 
                                    <?= $class->class_name ?> (<?= $class->class_code ?>) - <?= $class->class_shift ?>
                                    <small class="text-muted ms-2"><?= $class->level_name ?></small>
                                </h6>
                                <div>
                                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAll('<?= $class->id ?>')">
                                        Selecionar Todos
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAll('<?= $class->id ?>')">
                                        Limpar
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <?php
                                $disciplineModel = new \App\Models\DisciplineModel();
                                $disciplines = $disciplineModel->getByClass($class->id);
                                ?>
                                
                                <?php if (!empty($disciplines)): ?>
                                    <?php foreach ($disciplines as $discipline): ?>
                                        <?php
                                        $isAssigned = false;
                                        foreach ($assignments as $assignment) {
                                            if ($assignment->class_id == $class->id && $assignment->discipline_id == $discipline->id) {
                                                $isAssigned = true;
                                                break;
                                            }
                                        }
                                        ?>
                                        <div class="col-md-4 mb-2">
                                            <div class="form-check">
                                                <input class="form-check-input class-<?= $class->id ?>" 
                                                       type="checkbox" 
                                                       name="assignments[]" 
                                                       value="<?= $class->id ?>_<?= $discipline->id ?>"
                                                       id="assign_<?= $class->id ?>_<?= $discipline->id ?>"
                                                       <?= $isAssigned ? 'checked' : '' ?>>
                                                <label class="form-check-label" for="assign_<?= $class->id ?>_<?= $discipline->id ?>">
                                                    <?= $discipline->discipline_name ?>
                                                    <small class="text-muted">(<?= $discipline->discipline_code ?>)</small>
                                                </label>
                                            </div>
                                        </div>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <div class="col-12">
                                        <p class="text-muted mb-0">Nenhuma disciplina cadastrada para esta turma.</p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    Nenhuma turma encontrada para o ano letivo atual.
                </div>
            <?php endif; ?>
            
            <hr>
            
            <div class="d-flex justify-content-between">
                <a href="<?= site_url('admin/teachers/view/' . $teacher->id) ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Salvar Atribuições
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function selectAll(classId) {
    document.querySelectorAll('.class-' + classId).forEach(checkbox => {
        checkbox.checked = true;
    });
}

function deselectAll(classId) {
    document.querySelectorAll('.class-' + classId).forEach(checkbox => {
        checkbox.checked = false;
    });
}

// Expandir/collapsar todos
document.addEventListener('keydown', function(e) {
    // Ctrl + A para selecionar todos visíveis
    if (e.ctrlKey && e.key === 'a') {
        e.preventDefault();
        document.querySelectorAll('input[type="checkbox"]:not(:disabled)').forEach(checkbox => {
            checkbox.checked = true;
        });
    }
});
</script>
<?= $this->endSection() ?>