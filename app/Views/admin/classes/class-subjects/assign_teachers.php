<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1>Atribuir Professores</h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/classes/class-subjects') ?>">Disciplinas por Turma</a></li>
            <li class="breadcrumb-item active">Atribuir Professores</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Informações da Turma - FIXA (sticky) -->
<div class="sticky-top bg-white shadow-sm mb-4" style="top: 60px; z-index: 1020; border-left: 4px solid #0dcaf0;">
    <div class="p-3">
        <div class="d-flex align-items-center">
            <div class="me-3">
                <i class="fas fa-school fa-2x text-info"></i>
            </div>
            <div>
                <h4 class="mb-1"><?= $class->class_name ?> (<?= $class->class_code ?>)</h4>
                <p class="mb-0">
                    <span class="badge bg-primary me-2">Curso: <?= $class->course_name ?? 'Não definido' ?></span>
                    <span class="badge bg-success me-2">Nível: <?= $class->level_name ?></span>
                    <span class="badge bg-info me-2">Turno: <?= $class->class_shift ?></span>
                    <span class="badge bg-secondary">Ano Letivo: <?= $class->year_name ?? date('Y') ?></span>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Filtro por Período -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <h5 class="mb-0">
            <i class="fas fa-filter me-2 text-primary"></i>
            Filtrar por Período
        </h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <select class="form-select" id="periodFilter" onchange="filterByPeriod(this.value)">
                    <option value="">Todos os Períodos</option>
                    <option value="Anual">Anual</option>
                    <option value="1º Semestre">1º Semestre</option>
                    <option value="2º Semestre">2º Semestre</option>
                </select>
            </div>
            <div class="col-md-8 text-end">
                <span class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Total: <span id="totalCount"><?= count($disciplines) ?></span> disciplinas
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Formulário -->
<div class="card">
    <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-chalkboard-teacher me-2"></i>Atribuir Professores às Disciplinas
        </div>
        <div>
            <span class="badge bg-light text-dark me-2" id="selectedPeriodBadge">Todos os períodos</span>
        </div>
    </div>
    <div class="card-body">
        <form action="<?= site_url('admin/classes/class-subjects/save-teachers') ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="class_id" value="<?= $class->id ?>">
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Nota:</strong> Disciplinas anuais são válidas para todos os trimestres. 
                Disciplinas semestrais são válidas apenas no período correspondente.
            </div>
            
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-light">
                        <tr>
                            <th width="40">#</th>
                            <th>Disciplina</th>
                            <th>Código</th>
                            <th width="120">Carga Horária</th>
                            <th width="180">Período</th>
                            <th>Professor</th>
                            <th width="80">Status</th>
                        </tr>
                    </thead>
                    <tbody id="disciplinesTableBody">
                        <?php if (!empty($disciplines)): ?>
                            <?php foreach ($disciplines as $index => $disc): 
                                $periodInfo = $disc->period_info ?? [
                                    'badge' => 'secondary',
                                    'icon' => 'fa-question-circle',
                                    'label' => $disc->period_type ?? 'Não definido',
                                    'color' => 'secondary'
                                ];
                            ?>
                                <tr class="period-<?= strtolower(str_replace(' ', '-', $disc->period_type ?? 'undefined')) ?>" 
                                    data-period="<?= $disc->period_type ?? '' ?>">
                                    
                                    <td class="text-center"><?= $index + 1 ?></td>
                                    <td>
                                        <strong><?= $disc->discipline_name ?></strong>
                                    </td>
                                    <td><span class="badge bg-info"><?= $disc->discipline_code ?></span></td>
                                    <td class="text-center">
                                        <?php if ($disc->workload_hours): ?>
                                            <span class="badge bg-secondary"><?= $disc->workload_hours ?>h</span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge bg-<?= $periodInfo['color'] ?>">
                                            <i class="fas <?= $periodInfo['icon'] ?> me-1"></i>
                                            <?= $periodInfo['label'] ?>
                                        </span>
                                        <br>
                                        <small class="text-muted"><?= $periodInfo['description'] ?? '' ?></small>
                                    </td>
                                    <td>
                                        <select name="assignments[<?= $disc->id ?>]" class="form-select form-select-sm">
                                            <option value="">-- Sem professor --</option>
                                            <?php foreach ($teachers as $teacher): ?>
                                                <option value="<?= $teacher->id ?>" 
                                                    <?= $disc->teacher_id == $teacher->id ? 'selected' : '' ?>>
                                                    <?= $teacher->first_name ?> <?= $teacher->last_name ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td class="text-center">
                                        <?php if ($disc->teacher_id): ?>
                                            <span class="badge bg-success">Atribuído</span>
                                        <?php else: ?>
                                            <span class="badge bg-secondary">Pendente</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="text-center py-5">
                                    <i class="fas fa-book-open fa-4x text-muted mb-3"></i>
                                    <h5 class="text-muted">Nenhuma disciplina encontrada</h5>
                                    <p class="text-muted mb-3">
                                        Esta turma ainda não tem disciplinas atribuídas.
                                    </p>
                                    <a href="<?= site_url('admin/classes/class-subjects/assign?class=' . $class->id) ?>" 
                                       class="btn btn-primary">
                                        <i class="fas fa-plus-circle me-2"></i>Atribuir Disciplinas
                                    </a>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if (!empty($disciplines)): ?>
            <hr>
            
            <div class="row mt-3">
                <div class="col-md-6">
                    <div class="card bg-light">
                        <div class="card-body">
                            <h6 class="card-title"><i class="fas fa-chart-pie me-2"></i>Resumo por Período</h6>
                            <?php
                            $stats = [];
                            foreach ($disciplines as $disc) {
                                $period = $disc->period_type ?? 'Não definido';
                                if (!isset($stats[$period])) {
                                    $stats[$period] = ['total' => 0, 'assigned' => 0];
                                }
                                $stats[$period]['total']++;
                                if ($disc->teacher_id) {
                                    $stats[$period]['assigned']++;
                                }
                            }
                            ?>
                            <div class="row">
                                <?php foreach ($stats as $period => $data): ?>
                                    <div class="col-12 mb-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted"><?= $period ?>:</small>
                                            <span class="badge bg-<?= $data['assigned'] == $data['total'] ? 'success' : ($data['assigned'] > 0 ? 'warning' : 'danger') ?>">
                                                <?= $data['assigned'] ?>/<?= $data['total'] ?>
                                            </span>
                                        </div>
                                        <div class="progress mt-1" style="height: 5px;">
                                            <div class="progress-bar bg-<?= $data['assigned'] == $data['total'] ? 'success' : ($data['assigned'] > 0 ? 'warning' : 'danger') ?>" 
                                                 style="width: <?= ($data['assigned'] / $data['total']) * 100 ?>%"></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 d-flex align-items-center justify-content-end">
                    <div class="text-end">
                        <span class="text-muted me-3">
                            <i class="fas fa-check-circle text-success me-1"></i>
                            <span id="assignedCount"><?= count(array_filter($disciplines, fn($d) => $d->teacher_id)) ?></span>/
                            <span id="totalCountFooter"><?= count($disciplines) ?></span> atribuídos
                        </span>
                        <button type="submit" class="btn btn-success btn-lg">
                            <i class="fas fa-save me-2"></i>Salvar Todas as Atribuições
                        </button>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<script>
// Função para filtrar por período
function filterByPeriod(period) {
    const rows = document.querySelectorAll('#disciplinesTableBody tr');
    let visibleCount = 0;
    let assignedCount = 0;
    
    rows.forEach(row => {
        const rowPeriod = row.dataset.period;
        const shouldShow = period === '' || rowPeriod === period;
        
        if (shouldShow) {
            row.style.display = '';
            visibleCount++;
            
            // Verificar se tem professor atribuído
            const select = row.querySelector('select');
            if (select && select.value !== '') {
                assignedCount++;
            }
        } else {
            row.style.display = 'none';
        }
    });
    
    // Atualizar badges e contadores
    const badge = document.getElementById('selectedPeriodBadge');
    if (badge) {
        if (period === '') {
            badge.textContent = 'Todos os períodos';
        } else {
            badge.textContent = period;
        }
    }
    
    // Atualizar contadores
    const totalSpan = document.getElementById('totalCount');
    if (totalSpan) totalSpan.textContent = visibleCount;
    
    const assignedSpan = document.getElementById('assignedCount');
    if (assignedSpan) assignedSpan.textContent = assignedCount;
    
    const totalFooterSpan = document.getElementById('totalCountFooter');
    if (totalFooterSpan) totalFooterSpan.textContent = visibleCount;
}

// Inicializar contadores
document.addEventListener('DOMContentLoaded', function() {
    filterByPeriod('');
});

// Atualizar contador quando seleção de professor muda
document.querySelectorAll('select[name^="assignments"]').forEach(select => {
    select.addEventListener('change', function() {
        const period = document.getElementById('periodFilter')?.value || '';
        filterByPeriod(period);
    });
});

// Estilo para linhas por período
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('tr.period-anual').forEach(row => {
        row.style.borderLeft = '3px solid #28a745';
    });
    document.querySelectorAll('tr.period-1º-semestre').forEach(row => {
        row.style.borderLeft = '3px solid #007bff';
    });
    document.querySelectorAll('tr.period-2º-semestre').forEach(row => {
        row.style.borderLeft = '3px solid #ffc107';
    });
});
</script>

<style>
.sticky-top {
    z-index: 1020;
}
.table td {
    vertical-align: middle;
}
.progress {
    border-radius: 10px;
}
.badge {
    font-size: 0.85rem;
}
/* Estilo para linhas por período */
tr.period-anual:hover {
    background-color: rgba(40, 167, 69, 0.1) !important;
}
tr.period-1º-semestre:hover {
    background-color: rgba(0, 123, 255, 0.1) !important;
}
tr.period-2º-semestre:hover {
    background-color: rgba(255, 193, 7, 0.1) !important;
}
</style>

<?= $this->endSection() ?>