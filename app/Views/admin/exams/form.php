<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/exams') ?>">Exames</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $title ?></li>
        </ol>
    </nav>
</div>

<!-- Form -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-<?= $exam ? 'edit' : 'plus-circle' ?>"></i> <?= $title ?>
    </div>
    <div class="card-body">
        <form action="<?= site_url('admin/exams/save') ?>" method="post">
            <?= csrf_field() ?>
            
            <?php if ($exam): ?>
                <input type="hidden" name="id" value="<?= $exam->id ?>">
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="exam_name" class="form-label">Nome do Exame <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control <?= session('errors.exam_name') ? 'is-invalid' : '' ?>" 
                               id="exam_name" 
                               name="exam_name" 
                               value="<?= old('exam_name', $exam->exam_name ?? '') ?>"
                               required>
                        <?php if (session('errors.exam_name')): ?>
                            <div class="invalid-feedback"><?= session('errors.exam_name') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="exam_board_id" class="form-label">Tipo de Exame <span class="text-danger">*</span></label>
                        <select class="form-select <?= session('errors.exam_board_id') ? 'is-invalid' : '' ?>" 
                                id="exam_board_id" 
                                name="exam_board_id" 
                                required>
                            <option value="">Selecione...</option>
                            <?php if (!empty($examBoards)): ?>
                                <?php foreach ($examBoards as $board): ?>
                                    <option value="<?= $board->id ?>" 
                                        <?= (old('exam_board_id', $exam->exam_board_id ?? '') == $board->id) ? 'selected' : '' ?>>
                                        <?= $board->board_name ?> (Peso: <?= $board->weight ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <?php if (session('errors.exam_board_id')): ?>
                            <div class="invalid-feedback"><?= session('errors.exam_board_id') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
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
                                        <?= (old('class_id', $exam->class_id ?? '') == $class->id) ? 'selected' : '' ?>>
                                        <?= $class->class_name ?> - <?= $class->level_name ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <?php if (session('errors.class_id')): ?>
                            <div class="invalid-feedback"><?= session('errors.class_id') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-4">
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
                                        <?= (old('discipline_id', $exam->discipline_id ?? '') == $discipline->id) ? 'selected' : '' ?>>
                                        <?= $discipline->discipline_name ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <?php if (session('errors.discipline_id')): ?>
                            <div class="invalid-feedback"><?= session('errors.discipline_id') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="semester_id" class="form-label">Semestre <span class="text-danger">*</span></label>
                        <select class="form-select <?= session('errors.semester_id') ? 'is-invalid' : '' ?>" 
                                id="semester_id" 
                                name="semester_id" 
                                required>
                            <option value="">Selecione...</option>
                            <?php if (!empty($semesters)): ?>
                                <?php foreach ($semesters as $semester): ?>
                                    <option value="<?= $semester->id ?>" 
                                        <?= (old('semester_id', $exam->semester_id ?? '') == $semester->id) ? 'selected' : '' ?>>
                                        <?= $semester->semester_name ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <?php if (session('errors.semester_id')): ?>
                            <div class="invalid-feedback"><?= session('errors.semester_id') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="exam_date" class="form-label">Data <span class="text-danger">*</span></label>
                        <input type="date" 
                               class="form-control <?= session('errors.exam_date') ? 'is-invalid' : '' ?>" 
                               id="exam_date" 
                               name="exam_date" 
                               value="<?= old('exam_date', $exam->exam_date ?? '') ?>"
                               required>
                        <?php if (session('errors.exam_date')): ?>
                            <div class="invalid-feedback"><?= session('errors.exam_date') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="exam_time" class="form-label">Hora</label>
                        <input type="time" 
                               class="form-control" 
                               id="exam_time" 
                               name="exam_time" 
                               value="<?= old('exam_time', $exam->exam_time ?? '') ?>">
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="exam_room" class="form-label">Sala</label>
                        <input type="text" 
                               class="form-control" 
                               id="exam_room" 
                               name="exam_room" 
                               value="<?= old('exam_room', $exam->exam_room ?? '') ?>">
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="max_score" class="form-label">Valor Máximo</label>
                        <input type="number" 
                               class="form-control" 
                               id="max_score" 
                               name="max_score" 
                               step="0.01" 
                               min="0" 
                               value="<?= old('max_score', $exam->max_score ?? 20) ?>">
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Descrição / Instruções</label>
                <textarea class="form-control" id="description" name="description" rows="3"><?= old('description', $exam->description ?? '') ?></textarea>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" 
                                   id="is_published" 
                                   name="is_published" 
                                   value="1"
                                   <?= (old('is_published', $exam->is_published ?? false)) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_published">
                                Publicar resultados imediatamente
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr>
            
            <div class="d-flex justify-content-between">
                <a href="<?= site_url('admin/exams') ?>" class="btn btn-secondary">
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
// Load disciplines based on class
document.getElementById('class_id').addEventListener('change', function() {
    const classId = this.value;
    const disciplineSelect = document.getElementById('discipline_id');
    
    if (classId) {
        fetch(`<?= site_url('admin/classes/class-subjects/get-by-class/') ?>/${classId}`)
            .then(response => response.json())
            .then(data => {
                disciplineSelect.innerHTML = '<option value="">Selecione...</option>';
                data.forEach(item => {
                    disciplineSelect.innerHTML += `<option value="${item.discipline_id}">${item.discipline_name}</option>`;
                });
            });
    }
});
</script>
<?= $this->endSection() ?>