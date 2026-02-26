<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1>Configurações Académicas</h1>
        <a href="<?= site_url('admin/settings') ?>" class="btn btn-info">
            <i class="fas fa-arrow-left me-2"></i>Voltar
        </a>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/settings') ?>">Configurações</a></li>
            <li class="breadcrumb-item active" aria-current="page">Configurações Académicas</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<div class="card">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-graduation-cap me-2"></i>Configurações Académicas</h5>
    </div>
    <div class="card-body">
        <form action="<?= site_url('admin/settings/save-academic') ?>" method="post">
            <?= csrf_field() ?>
            
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Ano Letivo Atual <span class="text-danger">*</span></label>
                    <select name="academic_year_id" class="form-select <?= session('errors.academic_year_id') ? 'is-invalid' : '' ?>" required>
                        <option value="">Selecione</option>
                        <?php foreach ($academicYears as $year): ?>
                            <option value="<?= $year->id ?>" <?= (old('academic_year_id', $settings['current_academic_year'] ?? '') == $year->id) ? 'selected' : '' ?>>
                                <?= $year->year_name ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (session('errors.academic_year_id')): ?>
                        <div class="invalid-feedback"><?= session('errors.academic_year_id') ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="col-md-6 mb-3">
                    <label class="form-label">Semestre Atual <span class="text-danger">*</span></label>
                    <select name="semester_id" class="form-select <?= session('errors.semester_id') ? 'is-invalid' : '' ?>" required>
                        <option value="">Selecione</option>
                        <?php foreach ($semesters as $sem): ?>
                            <option value="<?= $sem->id ?>" <?= (old('semester_id', $settings['current_semester'] ?? '') == $sem->id) ? 'selected' : '' ?>>
                                <?= $sem->semester_name ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?php if (session('errors.semester_id')): ?>
                        <div class="invalid-feedback"><?= session('errors.semester_id') ?></div>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Sistema de Avaliação <span class="text-danger">*</span></label>
                    <select name="grading_system" class="form-select <?= session('errors.grading_system') ? 'is-invalid' : '' ?>" required>
                        <option value="0-20" <?= (old('grading_system', $settings['grading_system'] ?? '0-20') == '0-20') ? 'selected' : '' ?>>0-20 valores</option>
                        <option value="0-100" <?= (old('grading_system', $settings['grading_system'] ?? '0-20') == '0-100') ? 'selected' : '' ?>>0-100%</option>
                        <option value="A-F" <?= (old('grading_system', $settings['grading_system'] ?? '0-20') == 'A-F') ? 'selected' : '' ?>>Letras (A-F)</option>
                    </select>
                    <?php if (session('errors.grading_system')): ?>
                        <div class="invalid-feedback"><?= session('errors.grading_system') ?></div>
                    <?php endif; ?>
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Nota Mínima</label>
                    <input type="number" name="min_grade" class="form-control" min="0" max="20" step="0.5"
                           value="<?= old('min_grade', $settings['min_grade'] ?? 0) ?>">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Nota Máxima</label>
                    <input type="number" name="max_grade" class="form-control" min="0" max="20" step="0.5"
                           value="<?= old('max_grade', $settings['max_grade'] ?? 20) ?>">
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="form-label">Nota de Aprovação</label>
                    <input type="number" name="approval_grade" class="form-control" min="0" max="20" step="0.5"
                           value="<?= old('approval_grade', $settings['approval_grade'] ?? 10) ?>">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Frequência Mínima (%)</label>
                    <input type="number" name="attendance_threshold" class="form-control" min="0" max="100"
                           value="<?= old('attendance_threshold', $settings['attendance_threshold'] ?? 75) ?>">
                </div>
                
                <div class="col-md-4 mb-3">
                    <label class="form-label">Data Limite Matrícula</label>
                    <input type="date" name="enrollment_deadline" class="form-control"
                           value="<?= old('enrollment_deadline', $settings['enrollment_deadline'] ?? '') ?>">
                </div>
            </div>
            
            <div class="mb-3">
                <label class="form-label">Calendário Académico</label>
                <textarea name="academic_calendar" class="form-control" rows="3"><?= old('academic_calendar', $settings['academic_calendar'] ?? '') ?></textarea>
                <small class="text-muted">Informações sobre o calendário escolar</small>
            </div>
            
            <hr>
            
            <div class="d-flex justify-content-between">
                <a href="<?= site_url('admin/settings') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Salvar Configurações
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>