<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/classes/class-subjects') ?>">Disciplinas por Turma</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $title ?></li>
        </ol>
    </nav>
</div>

<!-- Form -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-<?= $assignment ? 'edit' : 'plus-circle' ?>"></i> <?= $title ?>
    </div>
    <div class="card-body">
        <form action="<?= site_url('admin/classes/class-subjects/assign') ?>" method="post">
            <?= csrf_field() ?>
            
            <?php if ($assignment): ?>
                <input type="hidden" name="id" value="<?= $assignment->id ?>">
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="class_id" class="form-label">Turma <span class="text-danger">*</span></label>
                        <select class="form-select <?= session('errors.class_id') ? 'is-invalid' : '' ?>" 
                                id="class_id" 
                                name="class_id" 
                                required>
                            <option value="">Selecione...</option>
                            <?php if (!empty($classes)): ?>
                                <?php foreach ($classes as $class): ?>
                                    <option value="<?= $class->id ?>" 
                                        <?= (old('class_id', $assignment->class_id ?? $selectedClass ?? '') == $class->id) ? 'selected' : '' ?>>
                                        <?= $class->class_name ?> (<?= $class->class_code ?>) - <?= $class->class_shift ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <?php if (session('errors.class_id')): ?>
                            <div class="invalid-feedback"><?= session('errors.class_id') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="discipline_id" class="form-label">Disciplina <span class="text-danger">*</span></label>
                        <select class="form-select <?= session('errors.discipline_id') ? 'is-invalid' : '' ?>" 
                                id="discipline_id" 
                                name="discipline_id" 
                                required>
                            <option value="">Selecione...</option>
                            <?php if (!empty($disciplines)): ?>
                                <?php foreach ($disciplines as $discipline): ?>
                                    <option value="<?= $discipline->id ?>" 
                                        <?= (old('discipline_id', $assignment->discipline_id ?? '') == $discipline->id) ? 'selected' : '' ?>>
                                        <?= $discipline->discipline_name ?> (<?= $discipline->discipline_code ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <?php if (session('errors.discipline_id')): ?>
                            <div class="invalid-feedback"><?= session('errors.discipline_id') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="teacher_id" class="form-label">Professor</label>
                        <select class="form-select" id="teacher_id" name="teacher_id">
                            <option value="">Selecione...</option>
                            <?php if (!empty($teachers)): ?>
                                <?php foreach ($teachers as $teacher): ?>
                                    <option value="<?= $teacher->id ?>" 
                                        <?= (old('teacher_id', $assignment->teacher_id ?? '') == $teacher->id) ? 'selected' : '' ?>>
                                        <?= $teacher->first_name ?> <?= $teacher->last_name ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="workload_hours" class="form-label">Carga Horária</label>
                        <input type="number" class="form-control" id="workload_hours" name="workload_hours" 
                               value="<?= old('workload_hours', $assignment->workload_hours ?? '') ?>"
                               min="0">
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="semester_id" class="form-label">Semestre</label>
                        <select class="form-select" id="semester_id" name="semester_id">
                            <option value="">Todos</option>
                            <?php if (!empty($semesters)): ?>
                                <?php foreach ($semesters as $semester): ?>
                                    <option value="<?= $semester->id ?>" 
                                        <?= (old('semester_id', $assignment->semester_id ?? '') == $semester->id) ? 'selected' : '' ?>>
                                        <?= $semester->semester_name ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" 
                           id="is_active" 
                           name="is_active" 
                           value="1"
                           <?= (old('is_active', $assignment->is_active ?? true)) ? 'checked' : '' ?>>
                    <label class="form-check-label" for="is_active">Ativo</label>
                </div>
            </div>
            
            <hr>
            
            <div class="d-flex justify-content-between">
                <a href="<?= site_url('admin/classes/class-subjects') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Salvar
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// When class is selected, load available subjects
document.getElementById('class_id').addEventListener('change', function() {
    const classId = this.value;
    const disciplineSelect = document.getElementById('discipline_id');
    
    if (classId) {
        // You can implement AJAX to filter disciplines by grade level
        fetch(`<?= site_url('admin/classes/class-subjects/get-by-class/') ?>/${classId}`)
            .then(response => response.json())
            .then(data => {
                // Update discipline options based on class
                console.log(data);
            });
    }
});
</script>
<?= $this->endSection() ?>