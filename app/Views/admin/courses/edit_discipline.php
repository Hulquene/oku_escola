<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/courses') ?>">Cursos</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/courses/view/' . $item->course_id) ?>"><?= $item->course_name ?></a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/courses/curriculum/' . $item->course_id) ?>">Currículo</a></li>
            <li class="breadcrumb-item active" aria-current="page">Editar Disciplina</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Formulário de Edição -->
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0">
                    <i class="fas fa-edit me-2"></i>
                    Editar Disciplina no Currículo
                </h5>
            </div>
            <div class="card-body">
                
                <!-- Informações da Disciplina -->
                <div class="alert alert-info mb-4">
                    <div class="row">
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Curso:</strong> <?= $item->course_name ?></p>
                            <p class="mb-1"><strong>Nível:</strong> <?= $item->level_name ?></p>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1"><strong>Disciplina:</strong> <?= $item->discipline_name ?></p>
                            <p class="mb-1"><strong>Código:</strong> <code><?= $item->discipline_code ?></code></p>
                        </div>
                    </div>
                </div>
                
                <form action="<?= site_url('admin/courses/curriculum/update/' . $item->id) ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="workload_hours" class="form-label fw-semibold">Carga Horária</label>
                            <input type="number" 
                                   class="form-control <?= session('errors.workload_hours') ? 'is-invalid' : '' ?>" 
                                   id="workload_hours" 
                                   name="workload_hours" 
                                   value="<?= old('workload_hours', $item->workload_hours ?? $item->default_workload) ?>"
                                   min="0"
                                   max="1000"
                                   placeholder="Carga horária específica">
                            <small class="text-muted">Deixe vazio para usar a carga horária padrão (<?= $item->default_workload ?? 0 ?> horas)</small>
                            <?php if (session('errors.workload_hours')): ?>
                                <div class="invalid-feedback"><?= session('errors.workload_hours') ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="semester" class="form-label fw-semibold">Semestre</label>
                            <select class="form-select <?= session('errors.semester') ? 'is-invalid' : '' ?>" 
                                    id="semester" 
                                    name="semester">
                                <option value="Anual" <?= old('semester', $item->semester) == 'Anual' ? 'selected' : '' ?>>Anual</option>
                                <option value="1" <?= old('semester', $item->semester) == '1' ? 'selected' : '' ?>>1º Semestre</option>
                                <option value="2" <?= old('semester', $item->semester) == '2' ? 'selected' : '' ?>>2º Semestre</option>
                            </select>
                            <?php if (session('errors.semester')): ?>
                                <div class="invalid-feedback"><?= session('errors.semester') ?></div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="col-md-12 mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" 
                                       id="is_mandatory" 
                                       name="is_mandatory" 
                                       value="1"
                                       <?= old('is_mandatory', $item->is_mandatory) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="is_mandatory">
                                    Disciplina Obrigatória
                                </label>
                            </div>
                        </div>
                        
                        <!-- Informações Adicionais -->
                        <div class="col-md-12 mt-3">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title"><i class="fas fa-info-circle me-2"></i>Informações da Disciplina</h6>
                                    <div class="row">
                                        <div class="col-md-4">
                                            <small class="text-muted d-block">Carga Horária Padrão</small>
                                            <strong><?= $item->default_workload ?? 0 ?> horas</strong>
                                        </div>
                                        <?php if (isset($item->approval_grade)): ?>
                                        <div class="col-md-4">
                                            <small class="text-muted d-block">Nota de Aprovação</small>
                                            <strong><?= $item->approval_grade ?? 10 ?> valores</strong>
                                        </div>
                                        <?php endif; ?>
                                        <?php if (isset($item->discipline_type)): ?>
                                        <div class="col-md-4">
                                            <small class="text-muted d-block">Tipo</small>
                                            <strong><?= $item->discipline_type ?? 'Obrigatória' ?></strong>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <hr class="my-4">
                    
                    <div class="d-flex justify-content-between">
                        <a href="<?= site_url('admin/courses/curriculum/' . $item->course_id) ?>" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i>Voltar ao Currículo
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save me-1"></i>Salvar Alterações
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Validar se a carga horária não ultrapassa o limite
    $('#workload_hours').on('input', function() {
        let value = parseInt($(this).val());
        if (value > 1000) {
            $(this).val(1000);
        }
        if (value < 0) {
            $(this).val(0);
        }
    });
});
</script>
<?= $this->endSection() ?>