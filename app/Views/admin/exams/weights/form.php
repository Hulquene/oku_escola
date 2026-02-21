<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2"><?= $title ?></h1>
            <p class="text-muted mb-0">
                <i class="fas fa-balance-scale me-1"></i>
                Configure o peso percentual para o tipo de avaliação
            </p>
        </div>
        <div>
            <a href="<?= site_url('admin/exams/weights') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/exams/weights') ?>">Pesos</a></li>
            <li class="breadcrumb-item active"><?= isset($weight) ? 'Editar' : 'Novo' ?> Peso</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-<?= isset($weight) ? 'edit' : 'plus' ?> me-2 text-primary"></i>
                    <?= isset($weight) ? 'Editar' : 'Novo' ?> Peso de Avaliação
                </h5>
            </div>
            <div class="card-body">
                <form method="post" action="<?= site_url('admin/exams/weights/save') ?>">
                    <?= csrf_field() ?>
                    
                    <?php if (isset($weight)): ?>
                        <input type="hidden" name="id" value="<?= $weight->id ?? ''; ?>"">
                    <?php endif; ?>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Ano Letivo <span class="text-danger">*</span></label>
                                <select class="form-select <?= session('errors.academic_year_id') ? 'is-invalid' : '' ?>" 
                                        name="academic_year_id" required>
                                    <option value="">Selecione</option>
                                    <?php foreach ($academicYears as $year): ?>
                                        <option value="<?= $year->id ?>" 
                                            <?= (old('academic_year_id') ?: ($weight->academic_year_id ?? '')) == $year->id ? 'selected' : '' ?>>
                                            <?= $year->year_name ?> <?= $year->is_current ? '(Atual)' : '' ?>
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (session('errors.academic_year_id')): ?>
                                    <div class="invalid-feedback"><?= session('errors.academic_year_id') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Nível de Ensino <span class="text-danger">*</span></label>
                                <select class="form-select <?= session('errors.grade_level_id') ? 'is-invalid' : '' ?>" 
                                        name="grade_level_id" required id="gradeLevel">
                                    <option value="">Selecione</option>
                                    <?php foreach ($gradeLevels as $level): ?>
                                        <option value="<?= $level->id ?>" 
                                            <?= (old('grade_level_id') ?: ($weight->grade_level_id ?? '')) == $level->id ? 'selected' : '' ?>>
                                            <?= $level->level_name ?> (<?= $level->education_level ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (session('errors.grade_level_id')): ?>
                                    <div class="invalid-feedback"><?= session('errors.grade_level_id') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Tipo de Avaliação <span class="text-danger">*</span></label>
                                <select class="form-select <?= session('errors.exam_board_id') ? 'is-invalid' : '' ?>" 
                                        name="exam_board_id" required>
                                    <option value="">Selecione</option>
                                    <?php foreach ($examBoards as $board): ?>
                                        <option value="<?= $board->id ?>" 
                                            data-type="<?= $board->board_type ?>"
                                            <?= (old('exam_board_id') ?: ($weight->exam_board_id ?? '')) == $board->id ? 'selected' : '' ?>>
                                            <?= $board->board_name ?> (<?= $board->board_code ?> - <?= $board->board_type ?>)
                                        </option>
                                    <?php endforeach; ?>
                                </select>
                                <?php if (session('errors.exam_board_id')): ?>
                                    <div class="invalid-feedback"><?= session('errors.exam_board_id') ?></div>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Peso (%) <span class="text-danger">*</span></label>
                                <div class="input-group">
                                    <input type="number" 
                                           class="form-control <?= session('errors.weight_percentage') ? 'is-invalid' : '' ?>" 
                                           name="weight_percentage" 
                                           value="<?= old('weight_percentage') ?: ($weight->weight_percentage ?? '') ?>"
                                           step="0.1" min="0.1" max="99.9" required>
                                    <span class="input-group-text">%</span>
                                </div>
                                <?php if (session('errors.weight_percentage')): ?>
                                    <div class="invalid-feedback d-block"><?= session('errors.weight_percentage') ?></div>
                                <?php endif; ?>
                                <div class="form-text">Valor entre 0.1% e 99.9%</div>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <div class="mb-3">
                                <div class="form-check form-switch">
                                    <input class="form-check-input" type="checkbox" 
                                           name="is_mandatory" value="1" id="isMandatory"
                                           <?= (old('is_mandatory') ?: ($weight->is_mandatory ?? 1)) ? 'checked' : '' ?>>
                                    <label class="form-check-label fw-semibold" for="isMandatory">
                                        Avaliação Obrigatória
                                    </label>
                                </div>
                                <small class="text-muted">Se marcado, esta avaliação é obrigatória para todos os alunos</small>
                            </div>
                        </div>
                        
                        <div class="col-12">
                            <hr>
                            <div class="d-flex justify-content-end gap-2">
                                <a href="<?= site_url('admin/exams/weights') ?>" class="btn btn-light">
                                    Cancelar
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>
                                    <?= isset($weight) ? 'Atualizar' : 'Salvar' ?> Peso
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Card de Ajuda -->
        <div class="card">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-info-circle me-2 text-info"></i>
                    Informações
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <h6 class="fw-semibold">Sobre os Pesos:</h6>
                    <ul class="list-unstyled">
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Cada tipo de avaliação tem um peso percentual
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            A soma dos pesos para um nível deve ser 100%
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-check-circle text-success me-2"></i>
                            Pesos maiores indicam maior importância
                        </li>
                    </ul>
                </div>
                
                <div class="mb-3">
                    <h6 class="fw-semibold">Tipos de Avaliação:</h6>
                    <div class="border rounded p-3 bg-light">
                        <div class="d-flex justify-content-between mb-2">
                            <span><strong>AC1, AC2, AC3:</strong></span>
                            <span>Avaliações Contínuas</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span><strong>EX-TRI:</strong></span>
                            <span>Exame Trimestral</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span><strong>EX-SEM:</strong></span>
                            <span>Exame Semestral</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span><strong>EX-FIN:</strong></span>
                            <span>Exame Final</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span><strong>EX-REC:</strong></span>
                            <span>Exame de Recurso</span>
                        </div>
                    </div>
                </div>
                
                <div class="alert alert-warning mb-0">
                    <i class="fas fa-lightbulb me-2"></i>
                    <strong>Exemplo:</strong><br>
                    AC1 (10%) + AC2 (10%) + AC3 (10%) + EX-TRI (30%) + EX-FIN (40%) = 100%
                </div>
            </div>
        </div>
        
        <!-- Card de Total -->
        <div class="card mt-3" id="totalCard" style="display: none;">
            <div class="card-body text-center">
                <h6 class="text-muted mb-2">Total de Pesos para este Nível</h6>
                <h2 class="mb-0" id="currentTotal">0%</h2>
                <small class="text-muted" id="totalStatus"></small>
            </div>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let selectedGradeLevel = $('#gradeLevel').val();
    let selectedYear = $('select[name="academic_year_id"]').val();
    let excludeId = <?= isset($weight) ? $weight->id : 'null' ?>;
    
    // Show total card if level and year selected
    if (selectedGradeLevel && selectedYear) {
        loadTotal(selectedGradeLevel, selectedYear, excludeId);
    }
    
    // Load total when level or year changes
    $('#gradeLevel, select[name="academic_year_id"]').change(function() {
        let levelId = $('#gradeLevel').val();
        let yearId = $('select[name="academic_year_id"]').val();
        
        if (levelId && yearId) {
            loadTotal(levelId, yearId, excludeId);
        } else {
            $('#totalCard').hide();
        }
    });
    
    function loadTotal(levelId, yearId, excludeId) {
        $.get('/admin/exams/weights/by-level/' + levelId, { academic_year: yearId }, function(response) {
            if (response.success) {
                let currentWeight = <?= isset($weight) ? $weight->weight_percentage : 0 ?>;
                let total = response.total;
                
                $('#currentTotal').text(total.toFixed(1) + '%');
                
                if (total > 100) {
                    $('#currentTotal').addClass('text-danger').removeClass('text-success');
                    $('#totalStatus').html('<span class="text-danger">Ultrapassa 100%</span>');
                } else if (total === 100) {
                    $('#currentTotal').addClass('text-success').removeClass('text-danger');
                    $('#totalStatus').html('<span class="text-success">Completo (100%)</span>');
                } else {
                    $('#currentTotal').removeClass('text-success text-danger');
                    let remaining = (100 - total).toFixed(1);
                    $('#totalStatus').html('Restante: ' + remaining + '%');
                }
                
                $('#totalCard').show();
            }
        });
    }
    
    // Validate form before submit
    $('form').submit(function(e) {
        let weight = parseFloat($('input[name="weight_percentage"]').val()) || 0;
        let levelId = $('#gradeLevel').val();
        let yearId = $('select[name="academic_year_id"]').val();
        
        if (!levelId || !yearId) return true;
        
        let isValid = false;
        $.ajax({
            url: '/admin/exams/weights/by-level/' + levelId,
            data: { academic_year: yearId },
            async: false,
            success: function(response) {
                if (response.success) {
                    let total = response.total;
                    let currentWeight = <?= isset($weight) ? $weight->weight_percentage : 0 ?>;
                    
                    // If editing, subtract current weight from total
                    if (<?= isset($weight) ? 'true' : 'false' ?>) {
                        total = total - currentWeight;
                    }
                    
                    if (total + weight > 100) {
                        alert('O total de pesos para este nível não pode ultrapassar 100%.\nTotal atual (sem este): ' + total.toFixed(1) + '%');
                        e.preventDefault();
                        isValid = false;
                    } else {
                        isValid = true;
                    }
                }
            }
        });
        
        return isValid;
    });
});
</script>

<?= $this->endSection() ?>