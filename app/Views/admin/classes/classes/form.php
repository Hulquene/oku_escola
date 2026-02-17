<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/classes/classes') ?>">Turmas</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $title ?></li>
        </ol>
    </nav>
</div>

<!-- Form -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-<?= $class ? 'edit' : 'plus-circle' ?>"></i> <?= $title ?>
    </div>
    <div class="card-body">
        <form action="<?= site_url('admin/classes/classes/save') ?>" method="post">
            <?= csrf_field() ?>
            
            <?php if ($class): ?>
                <input type="hidden" name="id" value="<?= $class->id ?>">
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="class_name" class="form-label">Nome da Turma <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control <?= session('errors.class_name') ? 'is-invalid' : '' ?>" 
                               id="class_name" 
                               name="class_name" 
                               value="<?= old('class_name', $class->class_name ?? '') ?>"
                               placeholder="Ex: 10ª A"
                               required>
                        <?php if (session('errors.class_name')): ?>
                            <div class="invalid-feedback"><?= session('errors.class_name') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="class_code" class="form-label">Código da Turma <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control <?= session('errors.class_code') ? 'is-invalid' : '' ?>" 
                               id="class_code" 
                               name="class_code" 
                               value="<?= old('class_code', $class->class_code ?? '') ?>"
                               placeholder="Ex: 10A-MAN"
                               required>
                        <?php if (session('errors.class_code')): ?>
                            <div class="invalid-feedback"><?= session('errors.class_code') ?></div>
                        <?php endif; ?>
                        <small class="text-muted">Código único para identificação</small>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="grade_level_id" class="form-label">Nível de Ensino <span class="text-danger">*</span></label>
                        <select class="form-select <?= session('errors.grade_level_id') ? 'is-invalid' : '' ?>" 
                                id="grade_level_id" 
                                name="grade_level_id" 
                                required>
                            <option value="">Selecione...</option>
                            <?php if (!empty($gradeLevels)): ?>
                                <?php foreach ($gradeLevels as $level): ?>
                                    <option value="<?= $level->id ?>" 
                                        <?= (old('grade_level_id', $class->grade_level_id ?? '') == $level->id) ? 'selected' : '' ?>>
                                        <?= $level->level_name ?> (<?= $level->education_level ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <?php if (session('errors.grade_level_id')): ?>
                            <div class="invalid-feedback"><?= session('errors.grade_level_id') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="academic_year_id" class="form-label">Ano Letivo <span class="text-danger">*</span></label>
                        <select class="form-select <?= session('errors.academic_year_id') ? 'is-invalid' : '' ?>" 
                                id="academic_year_id" 
                                name="academic_year_id" 
                                required>
                            <option value="">Selecione...</option>
                            <?php if (!empty($academicYears)): ?>
                                <?php foreach ($academicYears as $year): ?>
                                    <option value="<?= $year->id ?>" 
                                        <?= (old('academic_year_id', $class->academic_year_id ?? '') == $year->id) ? 'selected' : '' ?>>
                                        <?= $year->year_name ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <?php if (session('errors.academic_year_id')): ?>
                            <div class="invalid-feedback"><?= session('errors.academic_year_id') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="class_shift" class="form-label">Turno <span class="text-danger">*</span></label>
                        <select class="form-select <?= session('errors.class_shift') ? 'is-invalid' : '' ?>" 
                                id="class_shift" 
                                name="class_shift" 
                                required>
                            <option value="">Selecione...</option>
                            <option value="Manhã" <?= old('class_shift', $class->class_shift ?? '') == 'Manhã' ? 'selected' : '' ?>>Manhã</option>
                            <option value="Tarde" <?= old('class_shift', $class->class_shift ?? '') == 'Tarde' ? 'selected' : '' ?>>Tarde</option>
                            <option value="Noite" <?= old('class_shift', $class->class_shift ?? '') == 'Noite' ? 'selected' : '' ?>>Noite</option>
                            <option value="Integral" <?= old('class_shift', $class->class_shift ?? '') == 'Integral' ? 'selected' : '' ?>>Integral</option>
                        </select>
                        <?php if (session('errors.class_shift')): ?>
                            <div class="invalid-feedback"><?= session('errors.class_shift') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="class_room" class="form-label">Sala</label>
                        <input type="text" 
                               class="form-control" 
                               id="class_room" 
                               name="class_room" 
                               value="<?= old('class_room', $class->class_room ?? '') ?>"
                               placeholder="Ex: Sala 101">
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="capacity" class="form-label">Capacidade</label>
                        <input type="number" 
                               class="form-control" 
                               id="capacity" 
                               name="capacity" 
                               value="<?= old('capacity', $class->capacity ?? 30) ?>"
                               min="1"
                               max="100">
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="class_teacher_id" class="form-label">Professor Responsável</label>
                        <select class="form-select" id="class_teacher_id" name="class_teacher_id">
                            <option value="">Selecione...</option>
                            <?php if (!empty($teachers)): ?>
                                <?php foreach ($teachers as $teacher): ?>
                                    <option value="<?= $teacher->id ?>" 
                                        <?= (old('class_teacher_id', $class->class_teacher_id ?? '') == $teacher->id) ? 'selected' : '' ?>>
                                        <?= $teacher->first_name ?> <?= $teacher->last_name ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1"
                                   <?= (old('is_active', $class->is_active ?? true)) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_active">Turma Ativa</label>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr>
            
            <div class="d-flex justify-content-between">
                <a href="<?= site_url('admin/classes/classes') ?>" class="btn btn-secondary">
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