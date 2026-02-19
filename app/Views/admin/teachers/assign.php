<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <div>
            <a href="<?= site_url('admin/teachers/schedule/' . $teacher->id) ?>" class="btn btn-success me-2">
                <i class="fas fa-calendar-alt"></i> Ver Horário
            </a>
            <a href="<?= site_url('admin/teachers/view/' . $teacher->id) ?>" class="btn btn-info me-2">
                <i class="fas fa-eye"></i> Ver Professor
            </a>
            <a href="<?= site_url('admin/teachers') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/teachers') ?>">Professores</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/teachers/view/' . $teacher->id) ?>"><?= $teacher->first_name ?> <?= $teacher->last_name ?></a></li>
            <li class="breadcrumb-item active" aria-current="page">Atribuir Turmas</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Informação do Professor -->
<div class="alert alert-info mb-4">
    <div class="d-flex align-items-center">
        <?php if ($teacher->photo): ?>
            <img src="<?= base_url('uploads/teachers/' . $teacher->photo) ?>" 
                 alt="Foto" 
                 class="rounded-circle me-3"
                 style="width: 60px; height: 60px; object-fit: cover;">
        <?php else: ?>
            <div class="bg-primary rounded-circle d-flex align-items-center justify-content-center text-white me-3 fw-bold"
                 style="width: 60px; height: 60px; font-size: 1.5rem;">
                <?= strtoupper(substr($teacher->first_name, 0, 1) . substr($teacher->last_name, 0, 1)) ?>
            </div>
        <?php endif; ?>
        <div>
            <h4 class="mb-1"><?= $teacher->first_name ?> <?= $teacher->last_name ?></h4>
            <p class="mb-0">
                <i class="fas fa-envelope me-2"></i><?= $teacher->email ?> | 
                <i class="fas fa-phone me-2"></i><?= $teacher->phone ?> |
                <span class="badge bg-<?= $teacher->is_active ? 'success' : 'danger' ?>">
                    <?= $teacher->is_active ? 'Ativo' : 'Inativo' ?>
                </span>
            </p>
        </div>
    </div>
</div>

<!-- Form de Atribuição -->
<div class="card">
    <div class="card-header bg-primary text-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-tasks me-2"></i>Atribuir Turmas e Disciplinas
            </h5>
            <div>
                <button type="button" class="btn btn-sm btn-light" onclick="expandAll()">
                    <i class="fas fa-expand-alt"></i> Expandir Todos
                </button>
                <button type="button" class="btn btn-sm btn-light" onclick="collapseAll()">
                    <i class="fas fa-compress-alt"></i> Recolher Todos
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <form action="<?= site_url('admin/teachers/save-assignment') ?>" method="post" id="assignmentForm">
            <?= csrf_field() ?>
            <input type="hidden" name="teacher_id" value="<?= $teacher->id ?>">
            
            <div class="alert alert-warning d-flex align-items-center">
                <i class="fas fa-info-circle fa-2x me-3"></i>
                <div>
                    <strong>Instruções:</strong> Selecione as disciplinas que este professor irá lecionar.
                    <br>
                    <small class="text-muted">✓ Disciplinas atualmente atribuídas aparecem pré-selecionadas.</small>
                </div>
            </div>
            
            <!-- Informação do Ano Letivo Atual -->
            <div class="alert alert-info d-flex align-items-center mb-3">
                <i class="fas fa-calendar-alt fa-2x me-3"></i>
                <div>
                    <strong>Ano Letivo Atual:</strong> 
                    <span class="badge bg-primary fs-6 p-2"><?= $currentYear->year_name ?? 'Não definido' ?></span>
                    <?php if (isset($currentYear) && $currentYear): ?>
                        <br>
                        <small class="text-muted">
                            <?= date('d/m/Y', strtotime($currentYear->start_date)) ?> - 
                            <?= date('d/m/Y', strtotime($currentYear->end_date)) ?>
                        </small>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Resumo Rápido -->
            <div class="row mb-3">
                <div class="col-md-3">
                    <div class="border rounded p-2 text-center bg-light">
                        <small class="text-muted">Total de Turmas</small>
                        <h5 class="mb-0"><?= count($classes) ?></h5>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-2 text-center bg-light">
                        <small class="text-muted">Total de Disciplinas</small>
                        <h5 class="mb-0" id="totalDisciplinas">0</h5>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-2 text-center bg-light">
                        <small class="text-muted">Atribuições Atuais</small>
                        <h5 class="mb-0" id="totalAtribuicoes"><?= count($assignments) ?></h5>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="border rounded p-2 text-center bg-light">
                        <small class="text-muted">Ano Letivo</small>
                        <h5 class="mb-0 text-primary"><?= $currentYear->year_name ?? '-' ?></h5>
                    </div>
                </div>
            </div>
            
            <?php if (!empty($classes)): ?>
                <div class="accordion" id="classesAccordion">
                    <?php foreach ($classes as $index => $class): ?>
                        <div class="accordion-item mb-3 border">
                            <h2 class="accordion-header" id="heading<?= $class->id ?>">
                                <div class="accordion-button <?= $index === 0 ? '' : 'collapsed' ?>" 
                                     type="button" 
                                     data-bs-toggle="collapse" 
                                     data-bs-target="#collapse<?= $class->id ?>">
                                    <div class="d-flex justify-content-between align-items-center w-100 me-3">
                                        <div>
                                            <i class="fas fa-school text-primary me-2"></i>
                                            <strong><?= $class->class_name ?></strong>
                                            <span class="badge bg-secondary ms-2"><?= $class->class_code ?></span>
                                            <span class="badge bg-info ms-2"><?= $class->class_shift ?></span>
                                            
                                            <!-- Ano Letivo da Turma -->
                                            <?php if (isset($class->year_name)): ?>
                                                <span class="badge bg-dark ms-2">
                                                    <i class="fas fa-calendar me-1"></i><?= $class->year_name ?>
                                                </span>
                                            <?php endif; ?>
                                            
                                            <small class="text-muted ms-2"><?= $class->level_name ?></small>
                                            
                                            <!-- Curso da Turma -->
                                            <?php if (isset($class->course_name) && $class->course_name): ?>
                                                <span class="badge bg-primary ms-2">
                                                    <i class="fas fa-graduation-cap me-1"></i><?= $class->course_name ?>
                                                </span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary ms-2">Ensino Geral</span>
                                            <?php endif; ?>
                                        </div>
                                        <div>
                                            <span class="badge bg-success class-count-<?= $class->id ?>">0</span>
                                            <span class="text-muted mx-2">selecionadas</span>
                                            <button type="button" class="btn btn-sm btn-outline-primary me-1" onclick="selectAll('<?= $class->id ?>', event)">
                                                <i class="fas fa-check-square"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAll('<?= $class->id ?>', event)">
                                                <i class="fas fa-square"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </h2>
                            <div id="collapse<?= $class->id ?>" 
                                 class="accordion-collapse collapse <?= $index === 0 ? 'show' : '' ?>" 
                                 aria-labelledby="heading<?= $class->id ?>" 
                                 data-bs-parent="#classesAccordion">
                                <div class="accordion-body">
                                    <?php
                                    $disciplineModel = new \App\Models\DisciplineModel();
                                    $disciplines = $disciplineModel->getByClass($class->id);
                                    ?>
                                    
                                    <?php if (!empty($disciplines)): ?>
                                        <div class="row">
                                            <?php foreach ($disciplines as $discipline): 
                                                $key = $class->id . '_' . $discipline->id;
                                                $isAssigned = isset($assignmentMap[$key]);
                                                $isActive = $isAssigned && $assignmentMap[$key]->is_active;
                                            ?>
                                                <div class="col-md-4 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input class-<?= $class->id ?> discipline-checkbox" 
                                                               type="checkbox" 
                                                               name="assignments[]" 
                                                               value="<?= $key ?>"
                                                               id="assign_<?= $key ?>"
                                                               data-class="<?= $class->id ?>"
                                                               <?= $isActive ? 'checked' : '' ?>
                                                               <?= $isAssigned && !$isActive ? 'title="Atribuição anterior (inativa)"' : '' ?>>
                                                        <label class="form-check-label <?= $isAssigned && !$isActive ? 'text-muted' : '' ?>" 
                                                               for="assign_<?= $key ?>"
                                                               title="<?= $isAssigned && !$isActive ? 'Esta disciplina já foi atribuída anteriormente mas está inativa' : '' ?>">
                                                            <?= $discipline->discipline_name ?>
                                                            <small class="text-muted">(<?= $discipline->discipline_code ?>)</small>
                                                            <?php if ($discipline->workload_hours): ?>
                                                                <br>
                                                                <small class="text-info">
                                                                    <i class="fas fa-clock"></i> <?= $discipline->workload_hours ?>h
                                                                </small>
                                                            <?php endif; ?>
                                                            <?php if ($isAssigned && !$isActive): ?>
                                                                <span class="badge bg-warning text-dark ms-1">Inativa</span>
                                                            <?php endif; ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                        
                                        <!-- Informações adicionais da turma -->
                                        <div class="mt-3 pt-2 border-top">
                                            <small class="text-muted">
                                                <i class="fas fa-calendar me-1"></i> Ano Letivo: <strong><?= $class->year_name ?? $currentYear->year_name ?? 'N/A' ?></strong> |
                                                <i class="fas fa-users ms-2 me-1"></i> Capacidade: <?= $class->capacity ?> alunos |
                                                <i class="fas fa-door-open ms-2 me-1"></i> Sala: <?= $class->class_room ?: 'Não definida' ?>
                                            </small>
                                        </div>
                                    <?php else: ?>
                                        <div class="alert alert-warning mb-0">
                                            <i class="fas fa-exclamation-triangle me-2"></i>
                                            Nenhuma disciplina cadastrada para esta turma.
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-warning text-center py-4">
                    <i class="fas fa-exclamation-triangle fa-3x mb-3"></i>
                    <h5>Nenhuma turma encontrada</h5>
                    <p class="mb-0">Não existem turmas ativas para o ano letivo atual.</p>
                </div>
            <?php endif; ?>
            
            <hr class="my-4">
            
            <!-- Ações em Massa -->
            <div class="row mb-4">
                <div class="col-md-12">
                    <div class="btn-group" role="group">
                        <button type="button" class="btn btn-outline-primary" onclick="selectAllVisible()">
                            <i class="fas fa-check-double me-2"></i>Selecionar Todos Visíveis
                        </button>
                        <button type="button" class="btn btn-outline-secondary" onclick="deselectAllVisible()">
                            <i class="fas fa-times me-2"></i>Limpar Todos
                        </button>
                        <button type="button" class="btn btn-outline-info" onclick="invertSelection()">
                            <i class="fas fa-adjust me-2"></i>Inverter Seleção
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Botões de Ação -->
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted" id="selectionCounter">Nenhuma disciplina selecionada</span>
                </div>
                <div>
                    <a href="<?= site_url('admin/teachers/view/' . $teacher->id) ?>" class="btn btn-secondary me-2">
                        <i class="fas fa-arrow-left me-1"></i>Cancelar
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-1"></i>Salvar Atribuições
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Atualizar contadores quando a página carrega
document.addEventListener('DOMContentLoaded', function() {
    updateAllCounters();
});

// Função para atualizar contadores por turma
function updateClassCounter(classId) {
    const checkboxes = document.querySelectorAll('.class-' + classId);
    const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;
    const counter = document.querySelector('.class-count-' + classId);
    if (counter) {
        counter.textContent = checkedCount;
        counter.className = `badge class-count-${classId} ${checkedCount > 0 ? 'bg-success' : 'bg-secondary'}`;
    }
}

// Função para atualizar todos os contadores
function updateAllCounters() {
    const classes = <?= json_encode(array_column($classes, 'id')) ?>;
    classes.forEach(classId => {
        updateClassCounter(classId);
    });
    updateTotalCounter();
}

// Função para atualizar contador total
function updateTotalCounter() {
    const totalChecked = document.querySelectorAll('.discipline-checkbox:checked').length;
    const totalDisciplinas = document.querySelectorAll('.discipline-checkbox').length;
    document.getElementById('totalDisciplinas').textContent = totalDisciplinas;
    
    const counter = document.getElementById('selectionCounter');
    if (totalChecked === 0) {
        counter.textContent = 'Nenhuma disciplina selecionada';
        counter.className = 'text-muted';
    } else {
        counter.textContent = totalChecked + ' disciplina(s) selecionada(s)';
        counter.className = 'text-success fw-bold';
    }
}

// Selecionar todas as disciplinas de uma turma
function selectAll(classId, event) {
    if (event) {
        event.stopPropagation();
    }
    document.querySelectorAll('.class-' + classId).forEach(checkbox => {
        checkbox.checked = true;
    });
    updateClassCounter(classId);
    updateTotalCounter();
}

// Desmarcar todas as disciplinas de uma turma
function deselectAll(classId, event) {
    if (event) {
        event.stopPropagation();
    }
    document.querySelectorAll('.class-' + classId).forEach(checkbox => {
        checkbox.checked = false;
    });
    updateClassCounter(classId);
    updateTotalCounter();
}

// Selecionar todas as disciplinas visíveis (nas abas expandidas)
function selectAllVisible() {
    document.querySelectorAll('.accordion-collapse.show .discipline-checkbox').forEach(checkbox => {
        checkbox.checked = true;
    });
    updateAllCounters();
}

// Desmarcar todas as disciplinas
function deselectAllVisible() {
    document.querySelectorAll('.discipline-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    updateAllCounters();
}

// Inverter seleção atual
function invertSelection() {
    document.querySelectorAll('.discipline-checkbox').forEach(checkbox => {
        checkbox.checked = !checkbox.checked;
    });
    updateAllCounters();
}

// Expandir todos os accordions
function expandAll() {
    document.querySelectorAll('.accordion-collapse').forEach(collapse => {
        collapse.classList.add('show');
    });
    document.querySelectorAll('.accordion-button').forEach(button => {
        button.classList.remove('collapsed');
    });
}

// Recolher todos os accordions
function collapseAll() {
    document.querySelectorAll('.accordion-collapse').forEach(collapse => {
        collapse.classList.remove('show');
    });
    document.querySelectorAll('.accordion-button').forEach(button => {
        button.classList.add('collapsed');
    });
}

// Atualizar contadores quando checkboxes são clicados
document.querySelectorAll('.discipline-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        const classId = this.dataset.class;
        updateClassCounter(classId);
        updateTotalCounter();
    });
});

// Teclas de atalho
document.addEventListener('keydown', function(e) {
    // Ctrl + A para selecionar todos visíveis
    if (e.ctrlKey && e.key === 'a') {
        e.preventDefault();
        selectAllVisible();
    }
    // Ctrl + Shift + A para limpar todos
    if (e.ctrlKey && e.shiftKey && e.key === 'A') {
        e.preventDefault();
        deselectAllVisible();
    }
    // Ctrl + I para inverter seleção
    if (e.ctrlKey && e.key === 'i') {
        e.preventDefault();
        invertSelection();
    }
});

// Confirmar antes de sair com alterações não salvas
let formChanged = false;
document.querySelectorAll('.discipline-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', function() {
        formChanged = true;
    });
});

window.addEventListener('beforeunload', function(e) {
    if (formChanged) {
        e.preventDefault();
        e.returnValue = 'Existem alterações não salvas. Deseja realmente sair?';
    }
});

document.getElementById('assignmentForm').addEventListener('submit', function() {
    formChanged = false;
});
</script>

<style>
/* Estilo para melhor visualização */
.accordion-button:not(.collapsed) {
    background-color: #e7f1ff;
    color: #0d6efd;
}

.accordion-button .badge {
    transition: all 0.3s ease;
}

.form-check-input:checked {
    background-color: #198754;
    border-color: #198754;
}

/* Animação para os contadores */
@keyframes pulse {
    0% { transform: scale(1); }
    50% { transform: scale(1.1); }
    100% { transform: scale(1); }
}

.badge.bg-success {
    animation: pulse 0.3s ease;
}

/* Hover nos cards */
.accordion-item {
    transition: all 0.2s;
}

.accordion-item:hover {
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
}

/* Tooltip personalizado */
.form-check-label[title] {
    cursor: help;
    border-bottom: 1px dotted #6c757d;
}

/* Destaque para o ano letivo */
.badge.bg-dark {
    background-color: #343a40 !important;
}
</style>
<?= $this->endSection() ?>