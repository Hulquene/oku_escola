<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2">Gerar Calendário de Exames</h1>
            <p class="text-muted mb-0">
                <i class="fas fa-calendar-alt me-1"></i>
                <?= $period->period_name ?> • <?= $period->period_type ?>
            </p>
        </div>
        <div>
            <a href="<?= site_url('admin/exams/periods/view/' . $period->id) ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/exams/periods') ?>">Períodos</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/exams/periods/view/' . $period->id) ?>"><?= $period->period_name ?></a></li>
            <li class="breadcrumb-item active">Gerar Calendário</li>
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
                    <i class="fas fa-calendar-plus me-2 text-primary"></i>
                    Configurar Geração Automática
                </h5>
            </div>
            <div class="card-body">
              <form method="post" action="<?= site_url('admin/exams/periods/save-schedule/' . $period->id) ?>">
                    <?= csrf_field() ?>
                    
                    <div class="alert alert-info mb-4">
                        <i class="fas fa-info-circle me-2"></i>
                        O sistema irá distribuir automaticamente os exames para as turmas selecionadas,
                        respeitando as datas do período (<?= date('d/m/Y', strtotime($period->start_date)) ?> a <?= date('d/m/Y', strtotime($period->end_date)) ?>).
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Selecionar Turmas</label>
                        <div class="border rounded p-3" style="max-height: 300px; overflow-y: auto;">
                            <div class="form-check mb-2">
                                <input class="form-check-input" type="checkbox" id="selectAllClasses">
                                <label class="form-check-label fw-bold" for="selectAllClasses">
                                    Selecionar Todas as Turmas
                                </label>
                            </div>
                            <?php if (!empty($classes)): ?>
                                <?php foreach ($classes as $class): ?>
                                    <div class="form-check ms-3">
                                        <input class="form-check-input class-checkbox" 
                                               type="checkbox" 
                                               name="class_ids[]" 
                                               value="<?= $class->id ?>" 
                                               id="class_<?= $class->id ?>">
                                        <label class="form-check-label" for="class_<?= $class->id ?>">
                                            <?= $class->class_name ?> (<?= $class->class_code ?>)
                                            <small class="text-muted ms-2"><?= $class->level_name ?></small>
                                        </label>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted text-center py-3">Nenhuma turma disponível para este período</p>
                            <?php endif; ?>
                        </div>
                        <div class="form-text">Selecione as turmas que terão exames neste período</div>
                    </div>
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Tipo de Exame</label>
                        <select class="form-select" name="exam_board_id" required>
                            <option value="">Selecione o tipo de exame</option>
                            <?php foreach ($examBoards as $board): ?>
                                <option value="<?= $board->id ?>" 
                                        <?= ($period->period_type == 'Recurso' && $board->board_type == 'Recurso') ? 'selected' : '' ?>
                                        <?= ($period->period_type == 'Normal' && $board->board_type == 'Normal') ? 'selected' : '' ?>>
                                    <?= $board->board_name ?> (<?= $board->board_type ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-text">Tipo de exame a ser agendado para todas as disciplinas</div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Horário Padrão</label>
                                <input type="time" class="form-control" name="default_time" value="08:00">
                                <div class="form-text">Horário padrão para os exames (pode ser ajustado depois)</div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label class="form-label fw-semibold">Sala Padrão</label>
                                <input type="text" class="form-control" name="default_room" placeholder="Ex: Sala 101">
                                <div class="form-text">Opcional - pode ser definido por exame depois</div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle me-2"></i>
                        <strong>Atenção:</strong> Esta operação irá gerar exames para todas as disciplinas das turmas selecionadas.
                        Se já existirem exames agendados para este período, eles não serão duplicados.
                    </div>
                    
                    <hr>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="<?= site_url('admin/exams/periods/view/' . $period->id) ?>" class="btn btn-light">
                            Cancelar
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-calendar-plus me-2"></i> Gerar Calendário
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Card de Informações -->
        <div class="card">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-info-circle me-2 text-info"></i>
                    Informações
                </h5>
            </div>
            <div class="card-body">
                <h6 class="fw-semibold">Período:</h6>
                <p><?= $period->period_name ?></p>
                
                <h6 class="fw-semibold">Duração:</h6>
                <p>
                    <?= date('d/m/Y', strtotime($period->start_date)) ?> a 
                    <?= date('d/m/Y', strtotime($period->end_date)) ?>
                </p>
                
                <?php
                $start = new DateTime($period->start_date);
                $end = new DateTime($period->end_date);
                $interval = $start->diff($end);
                $daysTotal = $interval->days + 1;
                
                // Count working days
                $workDays = 0;
                $current = clone $start;
                while ($current <= $end) {
                    if ($current->format('N') < 6) { // Monday to Friday
                        $workDays++;
                    }
                    $current->modify('+1 day');
                }
                ?>
                
                <div class="border-top pt-3 mt-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total de dias:</span>
                        <span class="fw-semibold"><?= $daysTotal ?> dias</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Dias úteis:</span>
                        <span class="fw-semibold text-success"><?= $workDays ?> dias</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Fins de semana:</span>
                        <span class="fw-semibold text-warning"><?= $daysTotal - $workDays ?> dias</span>
                    </div>
                </div>
                
                <div class="alert alert-light mt-3 mb-0">
                    <i class="fas fa-lightbulb me-2"></i>
                    <strong>Como funciona:</strong>
                    <p class="small mt-2 mb-0">
                        O sistema irá distribuir os exames ao longo dos dias úteis do período,
                        evitando que uma turma tenha mais de um exame por dia.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Select all classes functionality
document.getElementById('selectAllClasses')?.addEventListener('change', function(e) {
    const checkboxes = document.querySelectorAll('.class-checkbox');
    checkboxes.forEach(cb => cb.checked = e.target.checked);
});

// Update select all checkbox when individual checkboxes change
document.querySelectorAll('.class-checkbox').forEach(cb => {
    cb.addEventListener('change', function() {
        const allChecked = document.querySelectorAll('.class-checkbox:checked').length === 
                          document.querySelectorAll('.class-checkbox').length;
        document.getElementById('selectAllClasses').checked = allChecked;
    });
});

// Validate at least one class selected on form submit
document.querySelector('form').addEventListener('submit', function(e) {
    const selectedClasses = document.querySelectorAll('.class-checkbox:checked').length;
    
    if (selectedClasses === 0) {
        e.preventDefault();
        alert('Selecione pelo menos uma turma para gerar o calendário.');
        return false;
    }
    
    return true;
});
</script>

<?= $this->endSection() ?>