<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header com estilo do sistema -->
<div class="ci-page-header">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-book-open me-2"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/classes/class-subjects') ?>">Disciplinas por Turma</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Atribuir Disciplinas</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <a href="<?= site_url('admin/classes/class-subjects') ?>" class="hdr-btn secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Informações da Turma (se já selecionada) -->
<?php if ($selectedClassInfo): ?>
<div class="alert-ci info mb-4">
    <div class="d-flex align-items-center">
        <div class="me-3">
            <i class="fas fa-school fa-2x"></i>
        </div>
        <div>
            <h5 class="mb-1"><?= $selectedClassInfo['class_name'] ?> (<?= $selectedClassInfo['class_code'] ?>)</h5>
            <p class="mb-0">
                <span class="badge-ci primary me-2">Curso: <?= $selectedClassInfo->course_name ?? 'Não definido' ?></span>
                <span class="badge-ci success me-2">Nível: <?= $selectedClassInfo['level_name'] ?></span>
                <span class="badge-ci info me-2">Turno: <?= $selectedClassInfo['class_shift'] ?></span>
                <span class="badge-ci secondary">Ano: <?= $selectedClassInfo['year_name'] ?></span>
            </p>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Formulário de Seleção de Turma -->
<div class="ci-card mb-4">
    <div class="ci-card-header" style="background: var(--primary);">
        <div class="ci-card-title" style="color: #fff;">
            <i class="fas fa-filter"></i>
            <span>Selecionar Turma</span>
        </div>
    </div>
    <div class="ci-card-body">
        <div class="row">
            <div class="col-md-6">
                <label class="filter-label fw-bold">Turma <span class="req">*</span></label>
                <select class="filter-select" id="class_id" onchange="window.location.href='<?= site_url('admin/classes/class-subjects/assign') ?>?class='+this.value">
                    <option value="">-- Selecione uma turma --</option>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?= $class['id'] ?>" <?= ($selectedClassId == $class['id']) ? 'selected' : '' ?>>
                            <?= $class['class_name'] ?> (<?= $class['class_code'] ?>) - 
                            <?= $class['class_shift'] ?> - 
                            <?= $class['level_name'] ?> - 
                            <?= $class['year_name'] ?>
                            <?= $class['course_name'] ? ' - ' . $class['course_name'] : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="form-hint">Selecione a turma para ver as disciplinas disponíveis</div>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Disciplinas -->
<div id="disciplinesContainer">
    <div class="ci-card">
        <div class="ci-card-header" style="background: var(--success);">
            <div class="ci-card-title" style="color: #fff;">
                <i class="fas fa-book me-2"></i>
                <span>Disciplinas da Turma</span>
            </div>
            <div class="d-flex align-items-center">
                <span class="badge-ci light me-2" id="selectedClassInfo">
                    <?php if ($selectedClassId && $selectedClassInfo): ?>
                        <?= $selectedClassInfo['class_name'] ?> | 
                        <span class="text-success"><?= count(array_filter($disciplines, fn($d) => $d['assigned'] ?? false)) ?></span> atribuídas / 
                        <span class="text-primary"><?= count($disciplines) ?></span> totais
                    <?php endif; ?>
                </span>
                <button type="button" class="hdr-btn secondary" onclick="refreshData()">
                    <i class="fas fa-sync-alt"></i>
                </button>
            </div>
        </div>
        <div class="ci-card-body">
            <div class="alert-ci info mb-3">
                <i class="fas fa-info-circle me-2"></i>
                Marque as disciplinas que esta turma deve ter. Para disciplinas já atribuídas, você pode alterar o professor e a carga horária.
                <br>
                <small class="text-muted">
                    <i class="fas fa-clock me-1"></i> Atalhos: <kbd>Ctrl+A</kbd> (tudo), <kbd>Ctrl+D</kbd> (limpar), <kbd>Ctrl+S</kbd> (salvar)
                </small>
            </div>
            
            <!-- Filtros Rápidos -->
            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <select class="filter-select" id="period_filter" onchange="filterTableByPeriod(this.value)">
                        <option value="">Todos os períodos</option>
                        <option value="Anual">Anual</option>
                        <option value="1º Semestre">1º Semestre</option>
                        <option value="2º Semestre">2º Semestre</option>
                        <option value="null">Sem período definido</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" class="filter-select" id="search_filter" 
                               placeholder="Pesquisar disciplina..." onkeyup="filterTableBySearch(this.value)">
                    </div>
                </div>
                <div class="col-md-4 text-end">
                    <div class="action-group" role="group">
                        <button type="button" class="row-btn edit" onclick="selectAll()" title="Selecionar todas">
                            <i class="fas fa-check-double"></i>
                        </button>
                        <button type="button" class="row-btn del" onclick="deselectAll()" title="Limpar seleção">
                            <i class="fas fa-times"></i>
                        </button>
                        <button type="button" class="row-btn view" onclick="selectMandatory()" title="Obrigatórias">
                            <i class="fas fa-star"></i>
                        </button>
                    </div>
                </div>
            </div>
            
            <form id="bulkAssignForm" method="post" action="<?= site_url('admin/classes/class-subjects/save-bulk') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="class_id" id="form_class_id" value="<?= $selectedClassId ?? '' ?>">
                
                <div class="table-responsive">
                    <table class="ci-table" id="disciplinesTable">
                        <thead>
                            <tr>
                                <th width="50" class="center">
                                    <input type="checkbox" id="selectAllCheckbox" class="form-check-input">
                                </th>
                                <th>Disciplina</th>
                                <th width="80">Código</th>
                                <th width="120">Período</th>
                                <th>Professor</th>
                                <th width="120">Carga Horária</th>
                                <th width="100" class="center">Status</th>
                            </tr>
                        </thead>
                    <tbody id="disciplinesList">
                        <?php if (!empty($disciplines)): ?>
                            <?php foreach ($disciplines as $index => $disc): 
                                // Determinar classe CSS baseada no status
                                $rowClass = isset($disc['assigned']) && $disc['assigned'] ? 'row-assigned' : '';
                                
                                // Badge do período
                                $periodType = $disc['period_type'] ?? 'Anual';
                                
                                $periodInfo = [
                                    'Anual' => ['badge' => 'success', 'icon' => 'fa-calendar-alt', 'text' => 'Anual'],
                                    '1º Semestre' => ['badge' => 'primary', 'icon' => 'fa-sun', 'text' => '1º Semestre'],
                                    '2º Semestre' => ['badge' => 'warning', 'icon' => 'fa-cloud-sun', 'text' => '2º Semestre']
                                ];
                                
                                $info = $periodInfo[$periodType] ?? ['badge' => 'secondary', 'icon' => 'fa-question-circle', 'text' => $periodType];
                            ?>
                                <tr class="<?= $rowClass ?>" 
                                    data-discipline-id="<?= $disc['id'] ?>" 
                                    data-period="<?= $periodType ?>" 
                                    data-name="<?= strtolower($disc['name'] ?? '') ?>"
                                    data-assigned="<?= isset($disc['assigned']) && $disc['assigned'] ? '1' : '0' ?>">
                                    
                                    <td class="center">
                                        <input type="checkbox" name="assignments[<?= $disc['id'] ?>_<?= $index ?>][selected]" 
                                            value="1" <?= isset($disc['assigned']) && $disc['assigned'] ? 'checked' : '' ?> 
                                            class="form-check-input discipline-checkbox">
                                        <input type="hidden" name="assignments[<?= $disc['id'] ?>_<?= $index ?>][discipline_id]" value="<?= $disc['id'] ?>">
                                        <input type="hidden" name="assignments[<?= $disc['id'] ?>_<?= $index ?>][assignment_id]" value="<?= $disc['assignment_id'] ?? '' ?>">
                                        <input type="hidden" name="assignments[<?= $disc['id'] ?>_<?= $index ?>][period_type]" value="<?= $periodType ?>">
                                    </td>
                                    <td>
                                        <strong><?= $disc['name'] ?? '' ?></strong>
                                        <?php if (isset($disc['is_mandatory']) && $disc['is_mandatory']): ?>
                                            <span class="badge-ci warning">Obrigatória</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><span class="code-badge"><?= $disc['code'] ?? '' ?></span></td>
                                   <td>
                                        <span class="badge-ci <?= $info['badge'] ?>">
                                            <i class="fas <?= $info['icon'] ?> me-1"></i><?= $info['text'] ?>
                                        </span>
                                        <?php if ($periodType == 'Anual'): ?>
                                            <br><small class="text-muted">Todos trimestres</small>
                                        <?php elseif ($periodType == '1º Semestre'): ?>
                                            <br><small class="text-muted">1º Trimestre</small>
                                        <?php elseif ($periodType == '2º Semestre'): ?>
                                            <br><small class="text-muted">2º Trimestre</small>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <select name="assignments[<?= $disc['id'] ?>_<?= $index ?>][teacher_id]" class="filter-select" style="min-width: 150px;">
                                            <option value="">-- Sem professor --</option>
                                            <?php foreach ($teachers as $teacher): ?>
                                                <option value="<?= $teacher['id'] ?>" 
                                                    <?= (isset($disc['teacher_id']) && $disc['teacher_id'] == $teacher['id']) ? 'selected' : '' ?>>
                                                    <?= $teacher['first_name'] ?> <?= $teacher['last_name'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="number" name="assignments[<?= $disc['id'] ?>_<?= $index ?>][workload]" 
                                            value="<?= $disc['workload'] ?? '' ?>" class="form-input-ci workload-input" 
                                            placeholder="<?= isset($disc['suggested_workload']) && $disc['suggested_workload'] ? 'Sugerida: ' . $disc['suggested_workload'] . 'h' : 'Carga horária' ?>" 
                                            min="0" max="999" step="1" style="width: 100px;">
                                    </td>
                                    <td class="center">
                                        <?php if (isset($disc['assigned']) && $disc['assigned']): ?>
                                            <span class="badge-ci success">Atribuída</span>
                                        <?php else: ?>
                                            <span class="badge-ci secondary">Não atribuída</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php elseif ($selectedClassId): ?>
                            <tr>
                                <td colspan="7" class="center py-4">
                                    <div class="empty-state">
                                        <i class="fas fa-info-circle"></i>
                                        <p>Nenhuma disciplina disponível para esta turma.</p>
                                        <small class="text-muted">Verifique se existem disciplinas cadastradas no currículo do curso</small>
                                    </div>
                                </td>
                            </tr>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="center py-4">
                                    <div class="empty-state">
                                        <i class="fas fa-arrow-up"></i>
                                        <p class="text-muted">Selecione uma turma no filtro acima</p>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                    </table>
                </div>
                
                <div class="mt-3 d-flex justify-content-between align-items-center">
                    <div>
                        <span class="badge-ci primary me-3">
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
                    <button type="submit" class="btn-ci success btn-lg" id="submitBtn" <?= !$selectedClassId ? 'disabled' : '' ?>>
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
        <div class="ci-card p-4 text-center">
            <i class="fas fa-spinner fa-spin fa-3x text-primary mb-3"></i>
            <h5 class="mb-0">Processando...</h5>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Variáveis globais
let teachers = <?= json_encode($teachers) ?>;

$(document).ready(function() {
    // Inicializar contadores
    updateSelectedCount();
    
    // Checkbox "Selecionar Todos"
    $('#selectAllCheckbox').change(function() {
        let isChecked = $(this).prop('checked');
        $('.discipline-checkbox:visible').prop('checked', isChecked).trigger('change');
    });
    
    // Evento para mudar a cor da linha quando checkbox muda
    $(document).on('change', '.discipline-checkbox', function() {
        let row = $(this).closest('tr');
        if (this.checked) {
            row.addClass('row-assigned');
        } else {
            row.removeClass('row-assigned');
        }
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
    
    // Inicializar switches
    $('.ci-switch-track').each(function() {
        var input = $(this).prev('input');
        if (input.is(':checked')) {
            $(this).addClass('checked');
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
        let isMandatory = row.find('.badge-ci.warning').length > 0;
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
/* Estilos adicionais específicos para esta página */
.row-assigned {
    background: rgba(22,168,125,0.08);
    border-left: 3px solid var(--success);
}

.table-responsive {
    border-radius: var(--radius-sm);
}

.ci-table .form-check-input {
    cursor: pointer;
}

.ci-table .filter-select {
    min-width: 140px;
    padding: 0.3rem 0.6rem;
    font-size: 0.8rem;
}

#loadingOverlay {
    backdrop-filter: blur(3px);
}

kbd {
    background-color: var(--surface);
    border: 1px solid var(--border);
    border-radius: 4px;
    padding: 2px 5px;
    font-size: 0.7rem;
    font-family: var(--font-mono);
    color: var(--text-primary);
}

#disciplinesContainer {
    animation: fadeIn 0.3s ease-out;
}

.workload-input {
    width: 100px !important;
    padding: 0.3rem 0.6rem !important;
    font-size: 0.8rem !important;
}

.workload-input.is-valid {
    border-color: var(--success);
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 8 8'%3e%3cpath fill='%2316A87D' d='M2.3 6.73L.6 4.53c-.4-1.04.46-1.4 1.1-.8l1.1 1.4 3.4-3.8c.6-.63 1.6-.27 1.2.7l-4 4.6c-.43.5-.8.4-1.1.1z'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.workload-input.is-invalid {
    border-color: var(--danger);
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 12 12' width='12' height='12' fill='none' stroke='%23E84646'%3e%3ccircle cx='6' cy='6' r='4.5'/%3e%3cpath stroke-linejoin='round' d='M5.8 3.6h.4L6 6.5z'/%3e%3ccircle cx='6' cy='8.2' r='.6' fill='%23E84646' stroke='none'/%3e%3c/svg%3e");
    background-repeat: no-repeat;
    background-position: right calc(0.375em + 0.1875rem) center;
    background-size: calc(0.75em + 0.375rem) calc(0.75em + 0.375rem);
}

.badge-ci.light {
    background: rgba(255,255,255,0.15);
    color: #fff;
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

@media (max-width: 768px) {
    .action-group {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
        justify-content: flex-end;
    }
    
    .row-btn {
        width: 32px;
        height: 32px;
    }
    
    .ci-table td {
        min-width: 120px;
    }
    
    .workload-input {
        width: 80px !important;
    }
}
</style>
<?= $this->endSection() ?>