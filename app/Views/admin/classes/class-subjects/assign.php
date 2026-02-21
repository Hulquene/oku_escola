<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/classes/class-subjects') ?>">Disciplinas por Turma</a></li>
            <li class="breadcrumb-item active">Atribuir Disciplinas</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Formulário de Seleção -->
<div class="card mb-4">
    <div class="card-header bg-primary text-white">
        <i class="fas fa-filter me-2"></i>Selecionar Turma
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <label class="form-label fw-bold">Turma <span class="text-danger">*</span></label>
                <select class="form-select" id="class_id">
                    <option value="">-- Selecione uma turma --</option>
                    <?php foreach ($classes as $class): ?>
                        <option value="<?= $class->id ?>" <?= ($selectedClassId == $class->id) ? 'selected' : '' ?>>
                            <?= $class->class_name ?> (<?= $class->class_code ?>) - 
                            <?= $class->class_shift ?> - 
                            <?= $class->level_name ?> - 
                            <?= $class->year_name ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="text-muted">Escolha a turma para atribuir disciplinas</small>
            </div>
            
            <div class="col-md-6">
                <label class="form-label fw-bold">Semestre</label>
                <select class="form-select" id="semester_id">
                    <option value="">-- Todos os semestres --</option>
                    <?php foreach ($semesters as $sem): ?>
                        <option value="<?= $sem->id ?>">
                            <?= $sem->semester_name ?> (<?= date('d/m', strtotime($sem->start_date)) ?> - <?= date('d/m', strtotime($sem->end_date)) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <small class="text-muted">Opcional - filtra disciplinas por semestre</small>
            </div>
        </div>
        
        <div class="mt-3 text-end">
            <button class="btn btn-primary" onclick="loadDisciplines()" id="btnLoadDisciplines" <?= !$selectedClassId ? 'disabled' : '' ?>>
                <i class="fas fa-search me-2"></i>Carregar Disciplinas
            </button>
        </div>
    </div>
</div>

<!-- Lista de Disciplinas -->
<div id="disciplinesContainer" style="display: <?= $selectedClassId ? 'block' : 'none' ?>;">
    <div class="card">
        <div class="card-header bg-success text-white d-flex justify-content-between align-items-center">
            <span><i class="fas fa-book me-2"></i>Disciplinas da Turma</span>
            <span id="selectedClassInfo" class="badge bg-light text-dark">
                <?php if ($selectedClassId): ?>
                    <?php 
                    $selectedClass = array_filter($classes, fn($c) => $c->id == $selectedClassId);
                    $selectedClass = reset($selectedClass);
                    if ($selectedClass): 
                    ?>
                        <?= $selectedClass->class_name ?> | 
                        <span class="text-success">0 atribuídas</span> / 
                        <span class="text-primary">0 totais</span>
                    <?php endif; ?>
                <?php endif; ?>
            </span>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                Marque as disciplinas que esta turma deve ter. Para disciplinas já atribuídas, você pode alterar o professor.
                <br>
                <small class="text-muted">
                    <i class="fas fa-clock me-1"></i> Use os atalhos: <kbd>Ctrl+A</kbd> (selecionar todas), <kbd>Ctrl+D</kbd> (limpar), <kbd>Ctrl+S</kbd> (salvar)
                </small>
            </div>
            
            <!-- Botões de ação rápida -->
            <div class="mb-3">
                <div class="btn-group" role="group">
                    <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                        <i class="fas fa-check-double me-1"></i>Selecionar Todas
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAll()">
                        <i class="fas fa-times me-1"></i>Limpar Todas
                    </button>
                    <button type="button" class="btn btn-sm btn-outline-warning" onclick="selectMandatory()">
                        <i class="fas fa-star me-1"></i>Apenas Obrigatórias
                    </button>
                </div>
                <span class="float-end text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    <span id="selectedCount">0</span> disciplinas selecionadas
                </span>
            </div>
            
            <form id="bulkAssignForm" method="post" action="<?= site_url('admin/classes/class-subjects/save-bulk') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="class_id" id="form_class_id" value="<?= $selectedClassId ?? '' ?>">
                <input type="hidden" name="semester_id" id="form_semester_id" value="">
                
                <div class="table-responsive">
                    <table class="table table-bordered table-hover">
                        <thead class="table-light">
                            <tr>
                                <th width="50" class="text-center">
                                    <input type="checkbox" id="selectAllCheckbox" class="form-check-input">
                                </th>
                                <th>Disciplina</th>
                                <th width="80">Código</th>
                                <th>Professor</th>
                                <th width="120">Carga Horária</th>
                                <th width="100">Status</th>
                            </tr>
                        </thead>
                        <tbody id="disciplinesList">
                            <?php if ($selectedClassId): ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <div class="spinner-border text-primary mb-2" role="status">
                                            <span class="visually-hidden">Carregando...</span>
                                        </div>
                                        <p>Clique em "Carregar Disciplinas" para ver a lista</p>
                                    </td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center py-4">
                                        <i class="fas fa-arrow-up fa-3x text-muted mb-3"></i>
                                        <p class="text-muted">Selecione uma turma no filtro acima</p>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="mt-3 d-flex justify-content-between align-items-center">
                    <div class="text-muted small">
                        <i class="fas fa-clock me-1"></i>
                        Pressione <kbd>Ctrl</kbd> + <kbd>S</kbd> para salvar
                    </div>
                    <button type="submit" class="btn btn-success btn-lg" id="submitBtn" <?= !$selectedClassId ? 'disabled' : '' ?>>
                        <i class="fas fa-save me-2"></i>Salvar Todas as Atribuições
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
let currentClassId = '<?= $selectedClassId ?? '' ?>';
let teachers = <?= json_encode($teachers) ?>;

$(document).ready(function() {
    // Se já temos turma selecionada, habilitar botão
    if (currentClassId) {
        $('#btnLoadDisciplines').prop('disabled', false);
    }
    
    // Quando turma muda
    $('#class_id').change(function() {
        let classId = $(this).val();
        $('#btnLoadDisciplines').prop('disabled', !classId);
        $('#form_class_id').val(classId);
        
        if (classId) {
            currentClassId = classId;
        }
    });
    
    // Quando semestre muda
    $('#semester_id').change(function() {
        let semesterId = $(this).val();
        $('#form_semester_id').val(semesterId);
    });
    
    // Checkbox "Selecionar Todos"
    $('#selectAllCheckbox').change(function() {
        let isChecked = $(this).prop('checked');
        $('.discipline-checkbox').prop('checked', isChecked).trigger('change');
    });
});

// Função para carregar disciplinas
function loadDisciplines() {
    let classId = $('#class_id').val();
    let semesterId = $('#semester_id').val();
    
    if (!classId) {
        alert('Selecione uma turma');
        return;
    }
    
    currentClassId = classId;
    
    $('#form_class_id').val(classId);
    $('#form_semester_id').val(semesterId);
    
    $('#disciplinesContainer').show();
    
    // Mostrar loading
    $('#disciplinesList').html(`
        <tr>
            <td colspan="6" class="text-center py-4">
                <div class="spinner-border text-primary mb-2" role="status">
                    <span class="visually-hidden">Carregando...</span>
                </div>
                <p>Carregando disciplinas...</p>
            </td>
        </tr>
    `);
    
    // Buscar disciplinas
    let url = '<?= site_url('admin/classes/class-subjects/get-class-disciplines/') ?>' + classId;
    if (semesterId) {
        url += '/' + semesterId;
    }
    
    $.ajax({
        url: url,
        type: 'GET',
        dataType: 'json',
        success: function(disciplines) {
            if (disciplines && disciplines.length > 0) {
                renderDisciplines(disciplines);
                updateSelectedClassInfo(disciplines);
            } else {
                $('#disciplinesList').html(`
                    <tr>
                        <td colspan="6" class="text-center py-4">
                            <i class="fas fa-info-circle fa-2x text-info mb-2"></i>
                            <p>Nenhuma disciplina disponível para esta turma.</p>
                            <small class="text-muted">Verifique se existem disciplinas cadastradas no sistema</small>
                        </td>
                    </tr>
                `);
            }
        },
        error: function(xhr, status, error) {
            console.error('Erro:', error);
            let errorMsg = 'Erro ao carregar disciplinas.';
            if (xhr.responseJSON && xhr.responseJSON.error) {
                errorMsg = xhr.responseJSON.error;
            }
            $('#disciplinesList').html(`
                <tr>
                    <td colspan="6" class="text-center py-4 text-danger">
                        <i class="fas fa-exclamation-triangle fa-2x"></i>
                        <p>${errorMsg}</p>
                        <small>${error}</small>
                    </td>
                </tr>
            `);
        }
    });
}

// Atualizar informações da turma selecionada
function updateSelectedClassInfo(disciplines) {
    let totalDisciplinas = disciplines.length;
    let atribuidas = disciplines.filter(d => d.assigned).length;
    let turmaNome = $('#class_id option:selected').text();
    
    $('#selectedClassInfo').html(`
        ${turmaNome} | 
        <span class="text-success">${atribuidas} atribuídas</span> / 
        <span class="text-primary">${totalDisciplinas} totais</span>
    `);
}

// Renderizar lista de disciplinas
function renderDisciplines(disciplines) {
    let html = '';
    
    disciplines.forEach((disc, index) => {
        let teacherOptions = '<option value="">-- Sem professor --</option>';
        teachers.forEach(t => {
            let selected = disc.teacher_id == t.id ? 'selected' : '';
            teacherOptions += `<option value="${t.id}" ${selected}>${t.first_name} ${t.last_name}</option>`;
        });
        
        let checked = disc.assigned ? 'checked' : '';
        let rowClass = disc.assigned ? 'table-success' : '';
        
        // Determinar o placeholder para carga horária
        let workloadPlaceholder = disc.suggested_workload ? 
            `Sugerida: ${disc.suggested_workload}h` : 
            'Carga horária';
        
        html += `
            <tr class="${rowClass}" data-discipline-id="${disc.id}">
                <td class="text-center">
                    <input type="checkbox" name="assignments[${disc.id}][selected]" 
                           value="1" ${checked} class="form-check-input discipline-checkbox">
                    <input type="hidden" name="assignments[${disc.id}][id]" value="${disc.id}">
                </td>
                <td>
                    <strong>${disc.name}</strong>
                    ${disc.is_mandatory ? ' <span class="badge bg-warning">Obrigatória</span>' : ''}
                </td>
                <td><span class="badge bg-info">${disc.code}</span></td>
                <td>
                    <select name="assignments[${disc.id}][teacher_id]" class="form-select form-select-sm">
                        ${teacherOptions}
                    </select>
                </td>
                <td>
                    <input type="number" name="assignments[${disc.id}][workload]" 
                           value="${disc.workload || ''}" class="form-control form-control-sm" 
                           placeholder="${workloadPlaceholder}" style="width: 100px;" min="0" max="999" step="1">
                </td>
                <td>
                    ${disc.assigned ? 
                        '<span class="badge bg-success">Atribuída</span>' : 
                        '<span class="badge bg-secondary">Não atribuída</span>'}
                </td>
            </tr>
        `;
    });
    
    $('#disciplinesList').html(html);
    updateSelectedCount();
    
    // Adicionar evento para mudar a cor da linha quando checkbox muda
    $('.discipline-checkbox').on('change', function() {
        let row = $(this).closest('tr');
        row.toggleClass('table-success', this.checked);
        updateSelectedCount();
    });
    
    // Atualizar select all checkbox
    $('#selectAllCheckbox').prop('checked', false);
}

// Atualizar contador de disciplinas selecionadas
function updateSelectedCount() {
    let count = $('.discipline-checkbox:checked').length;
    $('#selectedCount').text(count);
}

// Funções de seleção rápida
function selectAll() {
    $('.discipline-checkbox').prop('checked', true).trigger('change');
}

function deselectAll() {
    $('.discipline-checkbox').prop('checked', false).trigger('change');
}

function selectMandatory() {
    $('.discipline-checkbox').each(function() {
        let row = $(this).closest('tr');
        let isMandatory = row.find('.badge.bg-warning').length > 0;
        $(this).prop('checked', isMandatory).trigger('change');
    });
}

// Validação antes de enviar o formulário
$('#bulkAssignForm').on('submit', function(e) {
    let selectedCount = $('.discipline-checkbox:checked').length;
    
    if (selectedCount === 0) {
        e.preventDefault();
        alert('Selecione pelo menos uma disciplina para atribuir à turma.');
        return false;
    }
    
    if (!confirm(`Confirmar atribuição de ${selectedCount} disciplinas?`)) {
        e.preventDefault();
        return false;
    }
    
    // Mostrar loading overlay
    $('#loadingOverlay').show();
});

// Atalhos de teclado
document.addEventListener('keydown', function(e) {
    // Ctrl + S para salvar
    if (e.ctrlKey && e.key === 's') {
        e.preventDefault();
        if ($('#disciplinesContainer').is(':visible')) {
            $('#bulkAssignForm').submit();
        }
    }
    
    // Ctrl + A para selecionar todas
    if (e.ctrlKey && e.key === 'a') {
        e.preventDefault();
        if ($('#disciplinesContainer').is(':visible')) {
            selectAll();
        }
    }
    
    // Ctrl + D para desmarcar todas
    if (e.ctrlKey && e.key === 'd') {
        e.preventDefault();
        if ($('#disciplinesContainer').is(':visible')) {
            deselectAll();
        }
    }
});
</script>

<style>
/* Estilo para a tabela */
.table-hover tbody tr:hover {
    background-color: rgba(0,123,255,0.05);
}

/* Estilo para o loading overlay */
#loadingOverlay {
    backdrop-filter: blur(3px);
}

/* Badge de obrigatória */
.badge.bg-warning {
    font-size: 0.7rem;
}

/* Atalhos de teclado */
kbd {
    background-color: #f8f9fa;
    border: 1px solid #dee2e6;
    border-radius: 3px;
    padding: 2px 4px;
    font-size: 0.8rem;
}

/* Animações */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

#disciplinesContainer {
    animation: fadeIn 0.3s ease-out;
}

/* Responsividade */
@media (max-width: 768px) {
    .btn-group {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }
    
    .btn-sm {
        width: 100%;
    }
    
    .float-end {
        float: none !important;
        display: block;
        margin-top: 10px;
        text-align: right;
    }
}
</style>

<?= $this->endSection() ?>