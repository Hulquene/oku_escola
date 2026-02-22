<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/academic/semesters') ?>">Semestres/Trimestres</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $title ?></li>
        </ol>
    </nav>
</div>

<!-- Alertas Melhorados -->
<?= view('admin/partials/alerts') ?>

<!-- Form -->
<div class="card">
    <div class="card-header <?= $semester ? 'bg-info text-white' : 'bg-primary text-white' ?>">
        <i class="fas fa-<?= $semester ? 'edit' : 'plus-circle' ?>"></i> <?= $title ?>
    </div>
    <div class="card-body">
        <form action="<?= site_url('admin/academic/semesters/save') ?>" method="post" id="semesterForm">
            <?= csrf_field() ?>
            
            <?php if ($semester): ?>
                <input type="hidden" name="id" value="<?= $semester->id ?>">
            <?php endif; ?>
            
            <!-- Alertas dinâmicos via JavaScript -->
            <div id="dynamicAlert" style="display: none;"></div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="academic_year_id" class="form-label">
                            <i class="fas fa-calendar-alt"></i> Ano Letivo <span class="text-danger">*</span>
                        </label>
                        <select class="form-select <?= session('errors.academic_year_id') ? 'is-invalid' : '' ?>" 
                                id="academic_year_id" 
                                name="academic_year_id" 
                                required>
                            <option value="">Selecione...</option>
                            <?php if (!empty($academicYears)): ?>
                                <?php foreach ($academicYears as $year): ?>
                                    <option value="<?= $year->id ?>" 
                                        data-start="<?= $year->start_date ?>"
                                        data-end="<?= $year->end_date ?>"
                                        <?= (old('academic_year_id', $semester->academic_year_id ?? '') == $year->id) ? 'selected' : '' ?>>
                                        <?= $year->year_name ?> 
                                        (<?= date('d/m/Y', strtotime($year->start_date)) ?> - 
                                         <?= date('d/m/Y', strtotime($year->end_date)) ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        <?php if (session('errors.academic_year_id')): ?>
                            <div class="invalid-feedback"><?= session('errors.academic_year_id') ?></div>
                        <?php endif; ?>
                        <small class="text-muted" id="yearInfo"></small>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="semester_name" class="form-label">
                            <i class="fas fa-tag"></i> Nome do Período <span class="text-danger">*</span>
                        </label>
                        <input type="text" 
                               class="form-control <?= session('errors.semester_name') ? 'is-invalid' : '' ?>" 
                               id="semester_name" 
                               name="semester_name" 
                               value="<?= old('semester_name', $semester->semester_name ?? '') ?>"
                               placeholder="Ex: 1º Semestre 2024"
                               required
                               maxlength="100">
                        <?php if (session('errors.semester_name')): ?>
                            <div class="invalid-feedback"><?= session('errors.semester_name') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="semester_type" class="form-label">
                            <i class="fas fa-layer-group"></i> Tipo de Período <span class="text-danger">*</span>
                        </label>
                        <select class="form-select <?= session('errors.semester_type') ? 'is-invalid' : '' ?>" 
                                id="semester_type" 
                                name="semester_type" 
                                required>
                            <option value="">Selecione...</option>
                            <optgroup label="Trimestres">
                                <option value="1º Trimestre" <?= old('semester_type', $semester->semester_type ?? '') == '1º Trimestre' ? 'selected' : '' ?>>1º Trimestre</option>
                                <option value="2º Trimestre" <?= old('semester_type', $semester->semester_type ?? '') == '2º Trimestre' ? 'selected' : '' ?>>2º Trimestre</option>
                                <option value="3º Trimestre" <?= old('semester_type', $semester->semester_type ?? '') == '3º Trimestre' ? 'selected' : '' ?>>3º Trimestre</option>
                            </optgroup>
                            <optgroup label="Semestres">
                                <option value="1º Semestre" <?= old('semester_type', $semester->semester_type ?? '') == '1º Semestre' ? 'selected' : '' ?>>1º Semestre</option>
                                <option value="2º Semestre" <?= old('semester_type', $semester->semester_type ?? '') == '2º Semestre' ? 'selected' : '' ?>>2º Semestre</option>
                            </optgroup>
                        </select>
                        <?php if (session('errors.semester_type')): ?>
                            <div class="invalid-feedback"><?= session('errors.semester_type') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="start_date" class="form-label">
                            <i class="fas fa-play-circle"></i> Data de Início <span class="text-danger">*</span>
                        </label>
                        <input type="date" 
                               class="form-control <?= session('errors.start_date') ? 'is-invalid' : '' ?>" 
                               id="start_date" 
                               name="start_date" 
                               value="<?= old('start_date', $semester->start_date ?? '') ?>"
                               required
                               min="<?= $semester ? '' : '' ?>">
                        <?php if (session('errors.start_date')): ?>
                            <div class="invalid-feedback"><?= session('errors.start_date') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="end_date" class="form-label">
                            <i class="fas fa-stop-circle"></i> Data de Fim <span class="text-danger">*</span>
                        </label>
                        <input type="date" 
                               class="form-control <?= session('errors.end_date') ? 'is-invalid' : '' ?>" 
                               id="end_date" 
                               name="end_date" 
                               value="<?= old('end_date', $semester->end_date ?? '') ?>"
                               required>
                        <?php if (session('errors.end_date')): ?>
                            <div class="invalid-feedback"><?= session('errors.end_date') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Informações do período (calculadas) -->
            <div class="row mb-3" id="periodInfo" style="display: none;">
                <div class="col-md-12">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> 
                        <strong>Duração do período:</strong> <span id="durationDays"></span> dias
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
                                   <?= (old('is_active', $semester->is_active ?? true)) ? 'checked' : '' ?>>
                            <label class="form-check-label" for="is_active">
                                <i class="fas fa-toggle-on text-success"></i> Período Ativo
                            </label>
                        </div>
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> 
                            Desative para arquivar este período
                        </small>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <?php if (!$semester): ?>
                        <div class="mb-3">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" 
                                       id="is_current" 
                                       name="is_current" 
                                       value="1">
                                <label class="form-check-label" for="is_current">
                                    <i class="fas fa-star text-warning"></i> Definir como Período Atual
                                </label>
                            </div>
                            <small class="text-muted">
                                <i class="fas fa-info-circle"></i> 
                                Marque se este for o período corrente
                            </small>
                        </div>
                    <?php else: ?>
                        <?php if ($semester->is_current): ?>
                            <div class="alert alert-warning py-2">
                                <i class="fas fa-star"></i> 
                                <strong>Este é o período atual</strong>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Informações adicionais quando em edição -->
            <?php if ($semester): ?>
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="alert alert-light border">
                        <div class="row">
                            <div class="col-md-4">
                                <small class="text-muted d-block">Criado em:</small>
                                <strong><?= date('d/m/Y H:i', strtotime($semester->created_at)) ?></strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Última atualização:</small>
                                <strong><?= $semester->updated_at ? date('d/m/Y H:i', strtotime($semester->updated_at)) : 'Nunca' ?></strong>
                            </div>
                            <div class="col-md-4">
                                <small class="text-muted d-block">Status:</small>
                                <?php 
                                // Mapeamento de status para cores e labels
                                $statusMap = [
                                    'ativo' => ['class' => 'bg-success', 'label' => 'Ativo'],
                                    'inativo' => ['class' => 'bg-danger', 'label' => 'Inativo'],
                                    'processado' => ['class' => 'bg-info', 'label' => 'Processado'],
                                    'concluido' => ['class' => 'bg-secondary', 'label' => 'Concluído']
                                ];
                                
                                $currentStatus = $semester->status ?? 'inativo';
                                $statusInfo = $statusMap[$currentStatus] ?? ['class' => 'bg-secondary', 'label' => ucfirst($currentStatus)];
                                ?>
                                <span class="badge <?= $statusInfo['class'] ?>"><?= $statusInfo['label'] ?></span>
                                
                                <?php if ($semester->is_current): ?>
                                    <span class="badge bg-warning text-dark">Período Atual</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>
            
            <hr>
            
            <div class="d-flex justify-content-between">
                <a href="<?= site_url('admin/academic/semesters') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <button type="submit" class="btn btn-<?= $semester ? 'info' : 'primary' ?>">
                    <i class="fas fa-save"></i> <?= $semester ? 'Atualizar' : 'Salvar' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Elementos do DOM
const academicYearSelect = document.getElementById('academic_year_id');
const startDateInput = document.getElementById('start_date');
const endDateInput = document.getElementById('end_date');
const yearInfo = document.getElementById('yearInfo');
const periodInfo = document.getElementById('periodInfo');
const durationDays = document.getElementById('durationDays');
const dynamicAlert = document.getElementById('dynamicAlert');

// Dados do ano letivo selecionado
let selectedYearData = null;

// Função para mostrar alerta dinâmico
function showAlert(message, type = 'warning') {
    dynamicAlert.style.display = 'block';
    dynamicAlert.className = `alert alert-${type} alert-dismissible fade show`;
    dynamicAlert.innerHTML = `
        <i class="fas fa-exclamation-triangle"></i> ${message}
        <button type="button" class="btn-close" onclick="this.parentElement.style.display='none'"></button>
    `;
}

// Função para esconder alerta
function hideAlert() {
    dynamicAlert.style.display = 'none';
}

// Função para validar datas
function validateDates() {
    const yearId = academicYearSelect.value;
    const startDate = startDateInput.value;
    const endDate = endDateInput.value;
    
    hideAlert();
    
    if (yearId && startDate && endDate) {
        // Validar ordem das datas
        if (endDate < startDate) {
            showAlert('A data de fim não pode ser anterior à data de início.', 'danger');
            return false;
        }
        
        // Buscar datas do ano letivo via API
        fetch(`<?= site_url('admin/academic/years/check-dates/') ?>/${yearId}?start=${startDate}&end=${endDate}`)
            .then(response => response.json())
            .then(data => {
                if (!data.valid) {
                    showAlert(data.message, 'warning');
                }
            })
            .catch(error => console.error('Erro:', error));
        
        // Calcular duração do período
        const start = new Date(startDate);
        const end = new Date(endDate);
        const diffTime = Math.abs(end - start);
        const diffDays = Math.ceil(diffTime / (1000 * 60 * 60 * 24)) + 1;
        
        periodInfo.style.display = 'block';
        durationDays.textContent = diffDays;
        
        return true;
    }
    
    periodInfo.style.display = 'none';
    return true;
}

// Quando selecionar ano letivo
academicYearSelect.addEventListener('change', function() {
    const selectedOption = this.options[this.selectedIndex];
    
    if (this.value) {
        const startDate = selectedOption.dataset.start;
        const endDate = selectedOption.dataset.end;
        
        yearInfo.innerHTML = `<i class="fas fa-calendar"></i> Período do ano letivo: ${formatDate(startDate)} a ${formatDate(endDate)}`;
        
        // Sugerir datas baseadas no ano letivo (opcional)
        if (!startDateInput.value) {
            startDateInput.value = startDate;
        }
        if (!endDateInput.value) {
            endDateInput.value = endDate;
        }
    } else {
        yearInfo.innerHTML = '';
    }
    
    validateDates();
});

// Validar ao mudar as datas
startDateInput.addEventListener('change', validateDates);
endDateInput.addEventListener('change', validateDates);

// Validar ao submeter o formulário
document.getElementById('semesterForm').addEventListener('submit', function(e) {
    const yearId = academicYearSelect.value;
    const startDate = startDateInput.value;
    const endDate = endDateInput.value;
    
    if (!yearId || !startDate || !endDate) {
        e.preventDefault();
        showAlert('Preencha todos os campos obrigatórios.', 'danger');
        return false;
    }
    
    if (endDate < startDate) {
        e.preventDefault();
        showAlert('A data de fim não pode ser anterior à data de início.', 'danger');
        return false;
    }
    
    // Validação adicional via AJAX síncrona? (opcional)
    // Por enquanto, deixamos passar e o servidor valida
    
    return true;
});

// Função auxiliar para formatar data
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('pt-PT');
}

// Se estiver editando, validar ao carregar a página
<?php if ($semester): ?>
window.addEventListener('load', function() {
    validateDates();
});
<?php endif; ?>

// Auto-formatação do nome do período (opcional)
document.getElementById('semester_name').addEventListener('blur', function() {
    if (!this.value && academicYearSelect.value) {
        const yearName = academicYearSelect.options[academicYearSelect.selectedIndex].text.split(' ')[0];
        this.value = `1º Semestre ${yearName}`;
    }
});
</script>
<?= $this->endSection() ?>