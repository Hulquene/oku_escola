<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/courses') ?>">Cursos</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $title ?></li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Formulário -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-<?= $course ? 'edit' : 'plus-circle' ?>"></i> <?= $title ?>
    </div>
    <div class="card-body">
        <form action="<?= site_url('admin/courses/save') ?>" method="post">
            <?= csrf_field() ?>
            
            <?php if ($course): ?>
                <input type="hidden" name="id" value="<?= $course->id ?>">
            <?php endif; ?>
            
            <div class="row">
                <!-- Informações Básicas -->
                <div class="col-md-12">
                    <h5 class="mb-3">Informações Básicas</h5>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="course_name" class="form-label">Nome do Curso <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control <?= session('errors.course_name') ? 'is-invalid' : '' ?>" 
                               id="course_name" 
                               name="course_name" 
                               value="<?= old('course_name', $course->course_name ?? '') ?>"
                               required
                               maxlength="100">
                        <?php if (session('errors.course_name')): ?>
                            <div class="invalid-feedback"><?= session('errors.course_name') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="course_code" class="form-label">Código do Curso <span class="text-danger">*</span></label>
                        <input type="text" 
                               class="form-control <?= session('errors.course_code') ? 'is-invalid' : '' ?>" 
                               id="course_code" 
                               name="course_code" 
                               value="<?= old('course_code', $course->course_code ?? '') ?>"
                               required
                               maxlength="20"
                               placeholder="Ex: CFB">
                        <small class="text-muted">Código único (ex: CFB, CEJ, CH)</small>
                        <?php if (session('errors.course_code')): ?>
                            <div class="invalid-feedback"><?= session('errors.course_code') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="course_type" class="form-label">Tipo de Curso <span class="text-danger">*</span></label>
                        <select class="form-select <?= session('errors.course_type') ? 'is-invalid' : '' ?>" 
                                id="course_type" 
                                name="course_type" 
                                required>
                            <option value="">Selecione...</option>
                            <option value="Ciências" <?= old('course_type', $course->course_type ?? '') == 'Ciências' ? 'selected' : '' ?>>Ciências</option>
                            <option value="Humanidades" <?= old('course_type', $course->course_type ?? '') == 'Humanidades' ? 'selected' : '' ?>>Humanidades</option>
                            <option value="Económico-Jurídico" <?= old('course_type', $course->course_type ?? '') == 'Económico-Jurídico' ? 'selected' : '' ?>>Económico-Jurídico</option>
                            <option value="Técnico" <?= old('course_type', $course->course_type ?? '') == 'Técnico' ? 'selected' : '' ?>>Técnico</option>
                            <option value="Profissional" <?= old('course_type', $course->course_type ?? '') == 'Profissional' ? 'selected' : '' ?>>Profissional</option>
                            <option value="Outro" <?= old('course_type', $course->course_type ?? '') == 'Outro' ? 'selected' : '' ?>>Outro</option>
                        </select>
                        <?php if (session('errors.course_type')): ?>
                            <div class="invalid-feedback"><?= session('errors.course_type') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Níveis do Curso -->
                <div class="col-md-12 mt-3">
                    <h5 class="mb-3">Níveis do Curso</h5>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="start_grade_id" class="form-label">Nível Inicial <span class="text-danger">*</span></label>
                        <select class="form-select <?= session('errors.start_grade_id') ? 'is-invalid' : '' ?>" 
                                id="start_grade_id" 
                                name="start_grade_id" 
                                required>
                            <option value="">Selecione...</option>
                            <?php foreach ($highSchoolLevels as $level): ?>
                                <option value="<?= $level->id ?>" 
                                    <?= old('start_grade_id', $course->start_grade_id ?? '') == $level->id ? 'selected' : '' ?>>
                                    <?= $level->level_name ?> (<?= $level->education_level ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Classe de início do curso (normalmente 10ª Classe)</small>
                        <?php if (session('errors.start_grade_id')): ?>
                            <div class="invalid-feedback"><?= session('errors.start_grade_id') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="end_grade_id" class="form-label">Nível Final <span class="text-danger">*</span></label>
                        <select class="form-select <?= session('errors.end_grade_id') ? 'is-invalid' : '' ?>" 
                                id="end_grade_id" 
                                name="end_grade_id" 
                                required>
                            <option value="">Selecione...</option>
                            <?php foreach ($highSchoolLevels as $level): ?>
                                <option value="<?= $level->id ?>" 
                                    <?= old('end_grade_id', $course->end_grade_id ?? '') == $level->id ? 'selected' : '' ?>>
                                    <?= $level->level_name ?> (<?= $level->education_level ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <small class="text-muted">Classe de conclusão do curso (12ª ou 13ª Classe)</small>
                        <?php if (session('errors.end_grade_id')): ?>
                            <div class="invalid-feedback"><?= session('errors.end_grade_id') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="duration_years" class="form-label">Duração (anos)</label>
                        <input type="number" 
                               class="form-control <?= session('errors.duration_years') ? 'is-invalid' : '' ?>" 
                               id="duration_years" 
                               name="duration_years" 
                               value="<?= old('duration_years', $course->duration_years ?? 3) ?>"
                               min="1"
                               max="5">
                        <small class="text-muted">Número de anos do curso (normalmente 3)</small>
                        <?php if (session('errors.duration_years')): ?>
                            <div class="invalid-feedback"><?= session('errors.duration_years') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <div class="form-check form-switch mt-4">
                            <input class="form-check-input" type="checkbox" 
                                   id="is_active" 
                                   name="is_active" 
                                   value="1"
                                   <?= (old('is_active', $course->is_active ?? true)) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_active">Curso Ativo</label>
                        </div>
                    </div>
                </div>
                
                <!-- Descrição -->
                <div class="col-md-12 mt-3">
                    <h5 class="mb-3">Descrição</h5>
                </div>
                
                <div class="col-md-12">
                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição do Curso</label>
                        <textarea class="form-control" 
                                  id="description" 
                                  name="description" 
                                  rows="4"><?= old('description', $course->description ?? '') ?></textarea>
                        <small class="text-muted">Descrição detalhada do curso, objectivos, áreas de atuação, etc.</small>
                    </div>
                </div>
            </div>
            
            <!-- Informação Adicional -->
            <div class="alert alert-info mt-3">
                <i class="fas fa-info-circle"></i>
                <strong>Nota:</strong> Após criar o curso, poderá adicionar as disciplinas por nível no 
                <strong>Currículo do Curso</strong>.
            </div>
            
            <hr>
            
            <div class="d-flex justify-content-between">
                <a href="<?= site_url('admin/courses') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> <?= $course ? 'Salvar Alterações' : 'Salvar Curso' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const startSelect = document.getElementById('start_grade_id');
    const endSelect = document.getElementById('end_grade_id');
    
    function validateGrades() {
        const startValue = parseInt(startSelect.value);
        const endValue = parseInt(endSelect.value);
        
        if (startValue && endValue) {
            if (endValue <= startValue) {
                alert('O nível final deve ser maior que o nível inicial!');
                endSelect.value = '';
            }
        }
    }
    
    startSelect.addEventListener('change', validateGrades);
    endSelect.addEventListener('change', validateGrades);
});
</script>
<?= $this->endSection() ?>