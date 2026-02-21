<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2"><?= $title ?></h1>
            <p class="text-muted mb-0">
                <i class="fas fa-balance-scale me-1"></i>
                Configure os pesos percentuais de cada tipo de avaliação por nível de ensino
            </p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-outline-primary" data-bs-toggle="modal" data-bs-target="#quickConfigModal">
                <i class="fas fa-magic me-1"></i> Configuração Rápida
            </button>
            <button class="btn btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#copyModal">
                <i class="fas fa-copy me-1"></i> Copiar de Ano Anterior
            </button>
            <a href="<?= site_url('admin/exams/weights/create') ?>" class="btn btn-primary">
                <i class="fas fa-plus me-1"></i> Novo Peso
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/exams') ?>">Exames</a></li>
            <li class="breadcrumb-item active">Pesos</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Filtros -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-5">
                <label class="form-label fw-semibold">Ano Letivo</label>
                <select class="form-select" name="academic_year" onchange="this.form.submit()">
                    <option value="">Todos</option>
                    <?php foreach ($academicYears as $year): ?>
                        <option value="<?= $year->id ?>" <?= $selectedYear == $year->id ? 'selected' : '' ?>>
                            <?= $year->year_name ?> <?= $year->is_current ? '(Atual)' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-5">
                <label class="form-label fw-semibold">Nível de Ensino</label>
                <select class="form-select" name="grade_level" onchange="this.form.submit()">
                    <option value="">Todos</option>
                    <?php foreach ($gradeLevels as $level): ?>
                        <option value="<?= $level->id ?>" <?= $selectedLevel == $level->id ? 'selected' : '' ?>>
                            <?= $level->level_name ?> (<?= $level->education_level ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <a href="<?= site_url('admin/exams/weights') ?>" class="btn btn-secondary w-100">
                    <i class="fas fa-undo me-1"></i> Limpar
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Tabela de Pesos -->
<div class="card">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-semibold">
            <i class="fas fa-table me-2 text-primary"></i>
            Configuração de Pesos por Nível
        </h5>
    </div>
    <div class="card-body p-0">
        <?php if (!empty($groupedWeights)): ?>
            <div class="accordion" id="weightsAccordion">
                <?php $index = 0; ?>
                <?php foreach ($groupedWeights as $key => $group): ?>
                    <?php 
                    $total = $totals[$key] ?? 0;
                    $isValid = abs($total - 100) < 0.01;
                    $index++; 
                    ?>
                    <div class="accordion-item">
                        <h2 class="accordion-header" id="heading<?= $index ?>">
                            <button class="accordion-button <?= $index > 1 ? 'collapsed' : '' ?>" 
                                    type="button" data-bs-toggle="collapse" 
                                    data-bs-target="#collapse<?= $index ?>" 
                                    aria-expanded="<?= $index == 1 ? 'true' : 'false' ?>">
                                <div class="d-flex align-items-center w-100 me-3">
                                    <div class="flex-grow-1">
                                        <i class="fas fa-layer-group me-2 text-primary"></i>
                                        <strong><?= $group['level_name'] ?></strong>
                                        <small class="text-muted ms-2">(<?= $group['education_level'] ?>)</small>
                                    </div>
                                    <div class="me-4">
                                        <span class="badge <?= $isValid ? 'bg-success' : 'bg-warning text-dark' ?> p-2">
                                            Total: <?= number_format($total, 1) ?>%
                                            <?php if (!$isValid): ?>
                                                <i class="fas fa-exclamation-triangle ms-1" 
                                                   title="O total deve ser 100%"></i>
                                            <?php endif; ?>
                                        </span>
                                    </div>
                                    <div>
                                        <a href="<?= site_url('admin/exams/weights/create?grade_level=' . $group['grade_level_id']) ?>" 
                                           class="btn btn-sm btn-outline-primary me-2"
                                           onclick="event.stopPropagation()">
                                            <i class="fas fa-plus"></i>
                                        </a>
                                    </div>
                                </div>
                            </button>
                        </h2>
                        <div id="collapse<?= $index ?>" 
                             class="accordion-collapse collapse <?= $index == 1 ? 'show' : '' ?>" 
                             aria-labelledby="heading<?= $index ?>" 
                             data-bs-parent="#weightsAccordion">
                            <div class="accordion-body p-0">
                                <table class="table table-hover mb-0">
                                    <thead class="bg-light">
                                        <tr>
                                            <th class="border-0 py-3 ps-4">Tipo de Avaliação</th>
                                            <th class="border-0 py-3 text-center">Código</th>
                                            <th class="border-0 py-3 text-center">Categoria</th>
                                            <th class="border-0 py-3 text-center">Peso (%)</th>
                                            <th class="border-0 py-3 text-center">Obrigatório</th>
                                            <th class="border-0 py-3 text-center pe-4">Ações</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($group['weights'] as $weight): ?>
                                            <tr>
                                                <td class="ps-4">
                                                    <div class="fw-medium"><?= $weight->board_name ?></div>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-secondary"><?= $weight->board_code ?></span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="badge bg-info"><?= $weight->board_type ?></span>
                                                </td>
                                                <td class="text-center">
                                                    <span class="fw-bold fs-5 <?= $weight->weight_percentage >= 30 ? 'text-primary' : '' ?>">
                                                        <?= number_format($weight->weight_percentage, 1) ?>%
                                                    </span>
                                                </td>
                                                <td class="text-center">
                                                    <?php if ($weight->is_mandatory): ?>
                                                        <span class="badge bg-success">Sim</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-secondary">Não</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td class="text-center pe-4">
                                                    <div class="btn-group btn-group-sm">
                                                        <a href="<?= site_url('admin/exams/weights/edit/' . $weight->id) ?>" 
                                                           class="btn btn-outline-primary" title="Editar">
                                                            <i class="fas fa-edit"></i>
                                                        </a>
                                                        <button type="button" 
                                                                class="btn btn-outline-danger" 
                                                                onclick="confirmDelete(<?= $weight->id ?>, '<?= $weight->board_name ?>')"
                                                                title="Eliminar">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-balance-scale fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhum peso configurado</h5>
                <p class="text-muted mb-3">
                    Comece por adicionar pesos para os tipos de avaliação<br>
                    ou utilize a configuração rápida.
                </p>
                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#quickConfigModal">
                    <i class="fas fa-magic me-2"></i>Configuração Rápida
                </button>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal de Configuração Rápida -->
<div class="modal fade" id="quickConfigModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-magic me-2"></i>
                    Configuração Rápida de Pesos
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= site_url('admin/exams/weights/quickConfigure') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <p class="mb-3">Configure rapidamente os pesos padrão para os níveis selecionados:</p>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Ano Letivo *</label>
                        <select class="form-select" name="academic_year_id" required>
                            <option value="">Selecione</option>
                            <?php foreach ($academicYears as $year): ?>
                                <option value="<?= $year->id ?>" <?= $year->is_current ? 'selected' : '' ?>>
                                    <?= $year->year_name ?> <?= $year->is_current ? '(Atual)' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Níveis de Ensino *</label>
                        <div class="border rounded p-3" style="max-height: 250px; overflow-y: auto;">
                            <?php foreach ($gradeLevels as $level): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" 
                                           name="grade_level_ids[]" value="<?= $level->id ?>" 
                                           id="level_<?= $level->id ?>">
                                    <label class="form-check-label" for="level_<?= $level->id ?>">
                                        <?= $level->level_name ?> (<?= $level->education_level ?>)
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Pesos padrão:</strong>
                        <ul class="mt-2 mb-0">
                            <li>AC1: 10%</li>
                            <li>AC2: 10%</li>
                            <li>AC3: 10%</li>
                            <li>EX-TRI: 30%</li>
                            <li>EX-FIN: 40%</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-check me-2"></i>Configurar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Copiar de Ano Anterior -->
<div class="modal fade" id="copyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-secondary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-copy me-2"></i>
                    Copiar Pesos de Ano Anterior
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="<?= site_url('admin/exams/weights/copy') ?>" method="post">
                <?= csrf_field() ?>
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Copiar do Ano *</label>
                        <select class="form-select" name="source_academic_year_id" required>
                            <option value="">Selecione</option>
                            <?php foreach ($academicYears as $year): ?>
                                <option value="<?= $year->id ?>">
                                    <?= $year->year_name ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Para o Ano *</label>
                        <select class="form-select" name="target_academic_year_id" required>
                            <option value="">Selecione</option>
                            <?php foreach ($academicYears as $year): ?>
                                <option value="<?= $year->id ?>" <?= $year->is_current ? 'selected' : '' ?>>
                                    <?= $year->year_name ?> <?= $year->is_current ? '(Atual)' : '' ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Níveis (opcional)</label>
                        <div class="border rounded p-3" style="max-height: 200px; overflow-y: auto;">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="selectAllLevels">
                                <label class="form-check-label fw-bold" for="selectAllLevels">
                                    Selecionar Todos
                                </label>
                            </div>
                            <?php foreach ($gradeLevels as $level): ?>
                                <div class="form-check ms-3">
                                    <input class="form-check-input level-checkbox" type="checkbox" 
                                           name="grade_level_ids[]" value="<?= $level->id ?>" 
                                           id="copy_level_<?= $level->id ?>">
                                    <label class="form-check-label" for="copy_level_<?= $level->id ?>">
                                        <?= $level->level_name ?>
                                    </label>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <small class="text-muted">Deixe em branco para copiar todos os níveis</small>
                    </div>
                </div>
                <div class="modal-footer bg-light">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-secondary">
                        <i class="fas fa-copy me-2"></i>Copiar Pesos
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Delete -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirmar Eliminação
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja eliminar o peso da avaliação <strong id="deleteItemName"></strong>?</p>
                <p class="text-danger mb-0">
                    <i class="fas fa-info-circle me-1"></i>
                    Esta ação não pode ser desfeita.
                </p>
            </div>
            <div class="modal-footer bg-light">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="deleteConfirmBtn" class="btn btn-danger">
                    <i class="fas fa-trash me-2"></i>Eliminar
                </a>
            </div>
        </div>
    </div>
</div>

<style>
.accordion-button:not(.collapsed) {
    background-color: #f8f9fa;
    color: #0d6efd;
}

.accordion-button:focus {
    box-shadow: none;
    border-color: rgba(0,0,0,.125);
}

.table th {
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6c757d;
}

.table td {
    vertical-align: middle;
}

.badge {
    font-weight: 500;
    padding: 0.5em 0.8em;
}
</style>

<script>
function confirmDelete(id, name) {
    document.getElementById('deleteItemName').textContent = name;
    document.getElementById('deleteConfirmBtn').href = '<?= site_url('admin/exams/weights/delete') ?>/' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// Select all levels in copy modal
document.getElementById('selectAllLevels')?.addEventListener('change', function(e) {
    const checkboxes = document.querySelectorAll('.level-checkbox');
    checkboxes.forEach(cb => cb.checked = e.target.checked);
});

// Auto-check select all if all are checked
document.querySelectorAll('.level-checkbox').forEach(cb => {
    cb.addEventListener('change', function() {
        const allChecked = document.querySelectorAll('.level-checkbox:checked').length === 
                          document.querySelectorAll('.level-checkbox').length;
        document.getElementById('selectAllLevels').checked = allChecked;
    });
});
</script>

<?= $this->endSection() ?>