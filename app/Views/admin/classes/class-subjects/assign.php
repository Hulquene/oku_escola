<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2"><?= $title ?></h1>
            <p class="text-muted mb-0">Atribua disciplinas à turma selecionada</p>
        </div>
        <div>
            <a href="<?= site_url('admin/classes/class-subjects') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/classes/class-subjects') ?>">Disciplinas por Turma</a></li>
            <li class="breadcrumb-item active">Atribuir Disciplinas</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Informações da Turma (se já selecionada) -->
<?php if ($selectedClassInfo): ?>
<div class="alert alert-info mb-4">
    <div class="d-flex align-items-center">
        <div class="me-3">
            <i class="fas fa-school fa-2x"></i>
        </div>
        <div>
            <h5 class="mb-1"><?= $selectedClassInfo->class_name ?> (<?= $selectedClassInfo->class_code ?>)</h5>
            <p class="mb-0">
                <span class="badge bg-primary me-2">Curso: <?= $selectedClassInfo->course_name ?? 'Não definido' ?></span>
                <span class="badge bg-success me-2">Nível: <?= $selectedClassInfo->level_name ?></span>
                <span class="badge bg-info me-2">Turno: <?= $selectedClassInfo->class_shift ?></span>
                <span class="badge bg-secondary">Ano: <?= $selectedClassInfo->year_name ?></span>
            </p>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Formulário de Seleção de Turma -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <i class="fas fa-filter me-2"></i>Selecionar Turma
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <label class="form-label fw-bold">Turma <span class="text-danger">*</span></label>
                <select class="form-select" id="class_id" onchange="window.location.href='<?= site_url('admin/classes/class-subjects/assign') ?>?class='+this.value">
                    <option value="">-- Selecione uma turma --</option>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?= $class->id ?>" <?= ($selectedClassId == $class->id) ? 'selected' : '' ?>>
                            <?= $class->class_name ?> (<?= $class->class_code ?>) - 
                            <?= $class->class_shift ?> - 
                            <?= $class->level_name ?> - 
                            <?= $class->year_name ?>
                            <?= $class->course_name ? ' - ' . $class->course_name : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="text-muted">Selecione a turma para ver as disciplinas disponíveis</small>
            </div>
            
            <div class="col-md-6">
                <label class="form-label fw-bold">Semestre/Período</label>
                <select class="form-select" id="semester_filter">
                    <option value="">Todos os períodos</option>
                    <?php foreach ($semesters as $sem): ?>
                        <option value="<?= $sem->id ?>">
                            <?= $sem->semester_name ?> 
                            (<?= date('d/m', strtotime($sem->start_date)) ?> - <?= date('d/m', strtotime($sem->end_date)) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="text-muted">Filtrar disciplinas por período (opcional)</small>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Disciplinas -->
<div id="disciplinesContainer">
    <div class="card">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-book me-2"></i>Disciplinas da Turma
            </div>
            <div class="d-flex align-items-center">
                <span class="badge bg-light text-dark me-2" id="selectedClassInfo">
                    <?php if ($selectedClassId && $selectedClassInfo): ?>
                        <?= $selectedClassInfo->class_name ?> | 
                        <span class="text-success"><?= count(array_filter($disciplines, fn($d) => $d['assigned'] ?? false)) ?></span> atribuídas / 
                        <span class="text-primary"><?= count($disciplines) ?></span> totais
                    <?php endif; ?>
                </span>
                <button type="button" class="btn btn-sm btn-light" onclick="refreshData()">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Marque as disciplinas que esta turma deve ter. Para disciplinas já atribuídas, você pode alterar o professor e a carga horária.
                <br>
                <small class="text-muted">
                    <i class="fas fa-clock me-1"></i> Atalhos: <kbd>Ctrl+A</kbd> (tudo), <kbd>Ctrl+D</kbd> (limpar), <kbd>Ctrl+S</kbd> (salvar)
                </small>
            </div>
            
            <!-- Filtros Rápidos -->
            <div class="row mb-3">
                <div class="col-md-4">
                    <select class="form-select form-select-sm" id="period_filter" onchange="filterTableByPeriod(this.value)">
                        <option value="">Todos os períodos</option>
                        <option value="1º">1º Semestre/Trimestre</option>
                        <option value="2º">2º Semestre/Trimestre</option>
                        <option value="3º">3º Trimestre</option>
                        <option value="Anual">Anual</option>
                        <option value="null">Sem período definido</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control form-control-sm" id="search_filter" 
                           placeholder="Pesquisar disciplina..." onkeyup="filterTableBySearch(this.value)">
                </div>
                <div class="col-md-4 text-end">
                    <div class="btn-group btn-group-sm" role="group">
                        <button type="button" class="btn btn-outline-primary" onclick="selectAll()">
                            <i class="fas fa-check-double me-1"></i>Todas
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="deselectAll()">
                            <i class="fas fa-times me-1"></i>Limpar
                        </button>
                        <button type="button" class="btn btn-outline-warning" onclick="selectMandatory()">
                            <i class="fas fa-star me-1"></i>Obrigatórias
                        </button>
                    </div>
                </div>
            </div>
            
            <form id="bulkAssignForm" method="post" action="<?= site_url('admin/classes/class-subjects/save-bulk') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="class_id" id="form_class_id" value="<?= $selectedClassId ?? '' ?>">
                <input type="hidden" name="semester_id" id="form_semester_id" value="">
                
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="disciplinesTable">
                        <thead class="table-light">
                            <tr>
                                <th width="50" class="text-center">
                                    <input type="checkbox" id="selectAllCheckbox" class="form-check-input">
                                </th>
                                <th>Disciplina</th>
                                <th width="80">Código</th>
                                <th width="100">Período (Currículo)</th>
                                <th>Professor</th>
                                <th width="120">Carga Horária</th>
                                <th width="100">Status</th>
                            </tr>
                        </thead>
                    <tbody id="disciplinesList">
                        <?php if (!empty($disciplines)): ?>
                            <?php foreach ($disciplines as $index => $disc): 
                                // Determinar classe CSS baseada no status
                                $rowClass = isset($disc['assigned']) && $disc['assigned'] ? 'table-success' : '';
                                
                                // Verificar se tem semester_id (está atribuída a um semestre específico)
                                $hasSemesterId = isset($disc['semester_id']) && !empty($disc['semester_id']);
                                $periodClass = $hasSemesterId ? '' : 'table-warning';
                                
                                // Badge do período (baseado no suggested_semester do currículo)
                                $periodBadge = '';
                                $suggestedSemester = $disc['suggested_semester'] ?? 'Anual';
                                
                                if ($suggestedSemester == 'Anual') {
                                    $periodBadge = '<span class="badge bg-success">Anual</span>';
                                } elseif ($suggestedSemester == '1º') {
                                    $periodBadge = '<span class="badge bg-primary">1º Semestre</span>';
                                } elseif ($suggestedSemester == '2º') {
                                    $periodBadge = '<span class="badge bg-warning text-dark">2º Semestre</span>';
                                } elseif ($suggestedSemester == '3º') {
                                    $periodBadge = '<span class="badge bg-info">3º Trimestre</span>';
                                } else {
                                    $periodBadge = '<span class="badge bg-secondary">' . $suggestedSemester . '</span>';
                                }
                            ?>
                                <tr class="<?= $rowClass ?> <?= $periodClass ?>" 
                                    data-discipline-id="<?= $disc['id'] ?>" 
                                    data-period="<?= $suggestedSemester ?>" 
                                    data-name="<?= strtolower($disc['name'] ?? '') ?>"
                                    data-assigned="<?= isset($disc['assigned']) && $disc['assigned'] ? '1' : '0' ?>">
                                    
                                    <td class="text-center">
                                        <input type="checkbox" name="assignments[<?= $disc['id'] ?>_<?= $index ?>][selected]" 
                                            value="1" <?= isset($disc['assigned']) && $disc['assigned'] ? 'checked' : '' ?> 
                                            class="form-check-input discipline-checkbox">
                                        <input type="hidden" name="assignments[<?= $disc['id'] ?>_<?= $index ?>][discipline_id]" value="<?= $disc['id'] ?>">
                                        <input type="hidden" name="assignments[<?= $disc['id'] ?>_<?= $index ?>][assignment_id]" value="<?= $disc['assignment_id'] ?? '' ?>">
                                    </td>
                                    <td>
                                        <strong><?= $disc['name'] ?? '' ?></strong>
                                        <?php if (isset($disc['is_mandatory']) && $disc['is_mandatory']): ?>
                                            <span class="badge bg-warning">Obrigatória</span>
                                        <?php endif; ?>
                                        <?php if ($suggestedSemester == 'Anual'): ?>
                                            <br><small class="text-muted">Disciplina Anual</small>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="badge bg-info"><?= $disc['code'] ?? '' ?></span></td>
                                   <td>
                                        <?= $periodBadge ?>
                                        <br>
                                        <?php if ($hasSemesterId): ?>
                                            <small class="text-muted"><?= $disc['display_semester'] ?? 'Período definido' ?></small>
                                        <?php else: ?>
                                            <small class="text-danger"><i class="fas fa-exclamation-triangle"></i> Período não definido</small>
                                        <?php endif; ?>
                                        
                                        <?php if (!$hasSemesterId && $disc['assigned']): ?>
                                            <br><small class="text-warning"><i class="fas fa-clock"></i> Selecione um semestre</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <select name="assignments[<?= $disc['id'] ?>_<?= $index ?>][teacher_id]" class="form-select form-select-sm">
                                            <option value="">-- Sem professor --</option>
                                            <?php foreach ($teachers as $teacher): ?>
                                                <option value="<?= $teacher->id ?>" 
                                                    <?= (isset($disc['teacher_id']) && $disc['teacher_id'] == $teacher->id) ? 'selected' : '' ?>>
                                                    <?= $teacher->first_name ?> <?= $teacher->last_name ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="assignments[<?= $disc['id'] ?>_<?= $index ?>][workload]" 
                                            value="<?= $disc['workload'] ?? '' ?>" class="form-control form-control-sm workload-input" 
                                            placeholder="<?= isset($disc['suggested_workload']) && $disc['suggested_workload'] ? 'Sugerida: ' . $disc['suggested_workload'] . 'h' : 'Carga horária' ?>" 
                                            min="0" max="999" step="1">
                                        
                                      <?php if ($suggestedSemester == 'Anual' && !$hasSemesterId): ?>
                                        <select name="assignments[<?= $disc['id'] ?>_<?= $index ?>][semester_id]" class="form-select form-select-sm mt-1" >
                                            <option value="">-- Selecione o período --</option>
                                            <?php foreach ($semesters as $semester): ?>
                                                <option value="<?= $semester->id ?>" 
                                                    <?= (isset($disc['semester_id']) && $disc['semester_id'] == $semester->id) ? 'selected' : '' ?>>
                                                    <?= $semester->semester_name ?> 
                                                    (<?= date('d/m', strtotime($semester->start_date)) ?> - <?= date('d/m', strtotime($semester->end_date)) ?>)
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <small class="text-warning"><i class="fas fa-exclamation-triangle"></i> Período obrigatório</small>
                                    <?php elseif ($hasSemesterId): ?>
                                        <input type="hidden" name="assignments[<?= $disc['id'] ?>_<?= $index ?>][semester_id]" value="<?= $disc['semester_id'] ?>">
                                        <span class="badge bg-info mt-1 d-block">
                                            <i class="fas fa-calendar-check"></i> Período: <?= $disc['semester_name'] ?? '' ?>
                                        </span>
                                    <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if (isset($disc['assigned']) && $disc['assigned']): ?>
                                            <span class="badge bg-success">Atribuída</span>
                                            <?php if ($hasSemesterId): ?>
                                                <br><small class="text-success"><i class="fas fa-check-circle"></i> Período definido</small>
                                            <?php else: ?>
                                                <br><small class="text-warning"><i class="fas fa-exclamation-triangle"></i> Período pendente</small>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Não atribuída</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php elseif ($selectedClassId): ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-info-circle fa-2x text-info mb-2"></i>
                                    <p>Nenhuma disciplina disponível para esta turma.</p>
                                    <small class="text-muted">Verifique se existem disciplinas cadastradas no currículo do curso</small>
                                </td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-4">
                                    <i class="fas fa-arrow-up fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">Selecione uma turma no filtro acima</p>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    </table>
                </div>
                
                <div class="mt-3 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="text-muted me-3">
                            <i class="fas fa-check-circle text-success me-1"></i>
                            <span id="selectedCount">
                                <?= count(array_filter($disciplines, fn($d) => $d['assigned'] ?? false)) ?>
                            </span> disciplinas selecionadas
                        </span>
                        <span class="text-muted">
                            <i class="fas fa-clock me-1"></i>
                            <kbd>Ctrl</kbd> + <kbd>S</kbd> para salvar
                        </span>
                    </div>
                    <button type="submit" class="btn btn-success btn-lg" id="submitBtn" <?= !$selectedClassId ? 'disabled' : '' ?>>
                        <i class="fas fa-save me-2"></i>Salvar Atribuições
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loadingOverlay" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0,0,0,0.5); z-index: 9999;">
    <div class="d-flex justify-content-center align-items-center h-100">
        <div class="bg-white p-4 rounded shadow text-center">
            <i class="fas fa-spinner fa-spin fa-3x text-primary mb-3"></i>
            <h5>Processando...</h5>
        </div>
    </div>
</div>

<script>
// Variáveis globais
let teachers = <?= json_encode($teachers) ?>;

$(document).ready(function() {
    // Inicializar contadores
    updateSelectedCount();
    
    // Quando semestre muda
    $('#semester_filter').change(function() {
        let semesterId = $(this).val();
        $('#form_semester_id').val(semesterId);
    });
    
    // Checkbox "Selecionar Todos"
    $('#selectAllCheckbox').change(function() {
        let isChecked = $(this).prop('checked');
        $('.discipline-checkbox:visible').prop('checked', isChecked).trigger('change');
    });
    
    // Evento para mudar a cor da linha quando checkbox muda
    $(document).on('change', '.discipline-checkbox', function() {
        let row = $(this).closest('tr');
        row.toggleClass('table-success', this.checked);
        updateSelectedCount();
    });
    
    // Auto-calcular carga horária (opcional)
    $(document).on('blur', '.workload-input', function() {
        let val = parseInt($(this).val());
        if (val > 0) {
            $(this).removeClass('is-invalid').addClass('is-valid');
        } else if ($(this).val() === '') {
            $(this).removeClass('is-invalid is-valid');
        } else {
            $(this).addClass('is-invalid').removeClass('is-valid');
        }
    });
});

// Função para atualizar contador de disciplinas selecionadas
function updateSelectedCount() {
    let count = $('.discipline-checkbox:visible:checked').length;
    $('#selectedCount').text(count);
}
// Validação antes de enviar o formulário
$('#bulkAssignForm').on('submit', function(e) {
    let selectedCount = $('.discipline-checkbox:checked').length;
    
    if (selectedCount === 0) {
        e.preventDefault();
        Swal.fire({
            icon: 'warning',
            title: 'Nenhuma disciplina selecionada',
            text: 'Selecione pelo menos uma disciplina para atribuir à turma.',
            confirmButtonColor: '#3085d6'
        });
        return false;
    }
    
    // Validar apenas disciplinas selecionadas
    let missingSemester = [];
    
    $('.discipline-checkbox:checked').each(function() {
        let row = $(this).closest('tr');
        // CORREÇÃO: Usar seletor correto para os selects de semestre
        let semesterSelect = row.find('select[name*="[semester_id]"]');
        let isAnnual = row.find('.badge.bg-success:contains("Anual")').length > 0;
        let disciplineName = row.find('td:eq(1)').text().trim();
        
        // Se for anual e tem select de semestre (só aparece quando não tem semester_id)
        if (semesterSelect.length > 0 && isAnnual && !semesterSelect.val()) {
            missingSemester.push(disciplineName);
            row.addClass('table-danger');
        } else {
            row.removeClass('table-danger');
        }
    });
    
    if (missingSemester.length > 0) {
        e.preventDefault();
        
        let message = 'As seguintes disciplinas anuais precisam ter o período definido:<br><br>';
        missingSemester.forEach(function(discipline) {
            message += '• ' + discipline + '<br>';
        });
        message += '<br>Selecione um período para cada disciplina antes de salvar.';
        
        Swal.fire({
            icon: 'error',
            title: 'Período obrigatório não definido',
            html: message,
            confirmButtonColor: '#3085d6'
        });
        return false;
    }
    
    // Confirmar salvamento
    e.preventDefault(); // Previne envio imediato
    
    Swal.fire({
        title: 'Confirmar atribuição',
        html: `Deseja atribuir <strong>${selectedCount}</strong> disciplinas?`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#28a745',
        cancelButtonColor: '#6c757d',
        confirmButtonText: '<i class="fas fa-save me-1"></i>Sim, salvar',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            $('#loadingOverlay').show();
            // Envia o formulário
            $('#bulkAssignForm')[0].submit();
        }
    });
});
// Filtrar tabela por período
function filterTableByPeriod(period) {
    let rows = $('#disciplinesList tr');
    
    rows.each(function() {
        let rowPeriod = $(this).data('period');
        
        if (period === '') {
            $(this).show();
        } else if (period === 'null' && (!rowPeriod || rowPeriod === '')) {
            $(this).show();
        } else if (rowPeriod === period) {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
    
    updateSelectedCount();
}

// Filtrar tabela por texto
function filterTableBySearch(text) {
    let searchTerm = text.toLowerCase();
    let rows = $('#disciplinesList tr');
    
    rows.each(function() {
        let disciplineName = $(this).data('name') || '';
        
        if (disciplineName.includes(searchTerm) || searchTerm === '') {
            $(this).show();
        } else {
            $(this).hide();
        }
    });
    
    updateSelectedCount();
}

// Funções de seleção rápida
function selectAll() {
    $('.discipline-checkbox:visible').prop('checked', true).trigger('change');
}

function deselectAll() {
    $('.discipline-checkbox:visible').prop('checked', false).trigger('change');
}

function selectMandatory() {
    $('.discipline-checkbox:visible').each(function() {
        let row = $(this).closest('tr');
        let isMandatory = row.find('.badge.bg-warning').length > 0;
        $(this).prop('checked', isMandatory).trigger('change');
    });
}

// Refresh data (pode ser usado para recarregar via AJAX se necessário)
function refreshData() {
    location.reload();
}



// Atalhos de teclado
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.key === 's') {
        e.preventDefault();
        if ($('#disciplinesContainer').is(':visible')) {
            $('#bulkAssignForm').submit();
        }
    }
    
    if (e.ctrlKey && e.key === 'a') {
        e.preventDefault();
        selectAll();
    }
    
    if (e.ctrlKey && e.key === 'd') {
        e.preventDefault();
        deselectAll();
    }
});
</script>

<style>
/* Estilos adicionais */
.table-hover tbody tr:hover {
    background-color: rgba(0,123,255,0.05);
}

#loadingOverlay {
    backdrop-filter: blur(3px);
}

.badge.bg-warning {
    font-size: 0.7rem;
}

kbd {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 3px;
    padding: 2px 4px;
    font-size: 0.8rem;
}

#disciplinesContainer {
    animation: fadeIn 0.3s ease-out;
}

.table-warning {
    background-color: #fff3cd !important;
}

.table-warning:hover {
    background-color: #ffe69c !important;
}

.workload-input {
    width: 100px;
    display: inline-block;
}

.workload-input.is-valid {
    border-color: #28a745;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2328a745' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.workload-input.is-invalid {
    border-color: #dc3545;
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23dc3545'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23dc3545' stroke='none'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

@media (max-width: 768px) {
    .btn-group {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }
    
    .btn-sm {
        width: 100%;
    }
}
</style>

<?= $this->endSection() ?>