<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header com estilo do sistema -->
<div class="ci-page-header">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-chalkboard-teacher me-2"></i>Atribuir Professores</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/classes/class-subjects') ?>">Disciplinas por Turma</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Atribuir Professores</li>
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

<!-- Informações da Turma - FIXA (sticky) -->
<div class="ci-card mb-4 sticky-top" style="top: 60px; z-index: 1020; border-left: 4px solid var(--accent);">
    <div class="ci-card-body">
        <div class="d-flex align-items-center">
            <div class="me-3">
                <div class="stat-icon info" style="width: 50px; height: 50px;">
                    <i class="fas fa-school fa-lg"></i>
                </div>
            </div>
            <div>
                <h4 class="mb-1"><?= $class['class_name'] ?> (<?= $class['class_code'] ?>)</h4>
                <p class="mb-0">
                    <span class="badge-ci primary me-2">Curso: <?= $class['course_name'] ?? 'Não definido' ?></span>
                    <span class="badge-ci success me-2">Nível: <?= $class['level_name'] ?></span>
                    <span class="badge-ci info me-2">Turno: <?= $class['class_shift'] ?></span>
                    <span class="badge-ci secondary">Ano Letivo: <?= $class['year_name'] ?? date('Y') ?></span>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Filtro por Período -->
<div class="ci-card mb-4">
    <div class="ci-card-header">
        <div class="ci-card-title">
            <i class="fas fa-filter"></i>
            <span>Filtrar por Período</span>
        </div>
    </div>
    <div class="ci-card-body">
        <div class="row g-3">
            <div class="col-md-4">
                <select class="filter-select" id="periodFilter" onchange="filterByPeriod(this.value)">
                    <option value="">Todos os Períodos</option>
                    <option value="Anual">Anual</option>
                    <option value="1º Semestre">1º Semestre</option>
                    <option value="2º Semestre">2º Semestre</option>
                </select>
            </div>
            <div class="col-md-8 text-end">
                <span class="text-muted">
                    <i class="fas fa-info-circle me-1"></i>
                    Total: <span class="badge-ci primary" id="totalCount"><?= count($disciplines) ?></span> disciplinas
                </span>
            </div>
        </div>
    </div>
</div>

<!-- Formulário -->
<div class="ci-card">
    <div class="ci-card-header" style="background: var(--primary);">
        <div class="ci-card-title" style="color: #fff;">
            <i class="fas fa-chalkboard-teacher me-2"></i>
            <span>Atribuir Professores às Disciplinas</span>
        </div>
        <div>
            <span class="badge-ci light me-2" id="selectedPeriodBadge">Todos os períodos</span>
        </div>
    </div>
    <div class="ci-card-body">
        <form action="<?= site_url('admin/classes/class-subjects/save-teachers') ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="class_id" value="<?= $class['id'] ?>">
            
            <div class="alert-ci info mb-3">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Nota:</strong> Disciplinas anuais são válidas para todos os trimestres. 
                Disciplinas semestrais são válidas apenas no período correspondente.
            </div>
            
            <div class="table-responsive">
                <table class="ci-table">
                    <thead>
                        <tr>
                            <th width="40">#</th>
                            <th>Disciplina</th>
                            <th>Código</th>
                            <th width="120" class="center">Carga Horária</th>
                            <th width="180">Período</th>
                            <th>Professor</th>
                            <th width="80" class="center">Status</th>
                        </tr>
                    </thead>
                    <tbody id="disciplinesTableBody">
                        <?php if (!empty($disciplines)): ?>
                            <?php foreach ($disciplines as $index => $disc): 
                                $periodInfo = $disc['period_info'] ?? [
                                    'badge' => 'secondary',
                                    'icon' => 'fa-question-circle',
                                    'label' => $disc['period_type'] ?? 'Não definido',
                                    'color' => 'secondary'
                                ];
                            ?>
                                <tr class="period-<?= strtolower(str_replace(' ', '-', $disc['period_type'] ?? 'undefined')) ?>" 
                                    data-period="<?= $disc['period_type'] ?? '' ?>">
                                    
                                    <td class="center"><?= str_pad($index + 1, 2, '0', STR_PAD_LEFT) ?></td>
                                    <td>
                                        <strong><?= $disc['discipline_name'] ?></strong>
                                    </td>
                                    <td><span class="code-badge"><?= $disc['discipline_code'] ?></span></td>
                                    <td class="center">
                                        <?php if ($disc['workload_hours']): ?>
                                            <span class="num-chip"><?= $disc['workload_hours'] ?>h</span>
                                        <?php else: ?>
                                            <span class="text-muted">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="badge-ci <?= $periodInfo['color'] ?>">
                                            <i class="fas <?= $periodInfo['icon'] ?> me-1"></i>
                                            <?= $periodInfo['label'] ?>
                                        </span>
                                        <br>
                                        <small class="text-muted"><?= $periodInfo['description'] ?? '' ?></small>
                                    </td>
                                    <td>
                                        <select name="assignments[<?= $disc['id'] ?>]" class="filter-select" style="min-width: 180px;">
                                            <option value="">-- Sem professor --</option>
                                            <?php foreach ($teachers as $teacher): ?>
                                                <option value="<?= $teacher['id'] ?>" 
                                                    <?= $disc['teacher_id'] == $teacher['id'] ? 'selected' : '' ?>>
                                                    <?= $teacher['first_name'] ?> <?= $teacher['last_name'] ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </td>
                                    <td class="center">
                                        <?php if ($disc['teacher_id']): ?>
                                            <span class="badge-ci success">Atribuído</span>
                                        <?php else: ?>
                                            <span class="badge-ci secondary">Pendente</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr>
                                <td colspan="7" class="center py-5">
                                    <div class="empty-state">
                                        <i class="fas fa-book-open"></i>
                                        <h5 class="mt-3">Nenhuma disciplina encontrada</h5>
                                        <p class="text-muted mb-3">
                                            Esta turma ainda não tem disciplinas atribuídas.
                                        </p>
                                        <a href="<?= site_url('admin/classes/class-subjects/assign?class=' . $class['id']) ?>" 
                                           class="btn-ci primary">
                                            <i class="fas fa-plus-circle me-2"></i>Atribuir Disciplinas
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
            
            <?php if (!empty($disciplines)): ?>
            <hr class="my-4">
            
            <div class="row g-3 mt-3">
                <div class="col-md-6">
                    <div class="ci-card">
                        <div class="ci-card-header">
                            <div class="ci-card-title">
                                <i class="fas fa-chart-pie me-2"></i>
                                <span>Resumo por Período</span>
                            </div>
                        </div>
                        <div class="ci-card-body">
                            <?php
                            $stats = [];
                            foreach ($disciplines as $disc) {
                                $period = $disc['period_type'] ?? 'Não definido';
                                if (!isset($stats[$period])) {
                                    $stats[$period] = ['total' => 0, 'assigned' => 0];
                                }
                                $stats[$period]['total']++;
                                if ($disc['teacher_id']) {
                                    $stats[$period]['assigned']++;
                                }
                            }
                            ?>
                            <div class="row">
                                <?php foreach ($stats as $period => $data): ?>
                                    <div class="col-12 mb-3">
                                        <div class="d-flex justify-content-between align-items-center mb-1">
                                            <small class="filter-label"><?= $period ?>:</small>
                                            <span class="badge-ci <?= $data['assigned'] == $data['total'] ? 'success' : ($data['assigned'] > 0 ? 'warning' : 'danger') ?>">
                                                <?= $data['assigned'] ?>/<?= $data['total'] ?>
                                            </span>
                                        </div>
                                        <div class="progress" style="height: 6px; background: var(--surface);">
                                            <div class="progress-bar bg-<?= $data['assigned'] == $data['total'] ? 'success' : ($data['assigned'] > 0 ? 'warning' : 'danger') ?>" 
                                                 style="width: <?= ($data['assigned'] / $data['total']) * 100 ?>%; height: 6px; border-radius: 3px;"></div>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6 d-flex align-items-center justify-content-end">
                    <div class="text-end">
                        <span class="badge-ci primary me-3 p-3">
                            <i class="fas fa-check-circle text-success me-1"></i>
                            <span id="assignedCount"><?= count(array_filter($disciplines, fn($d) => $d['teacher_id'])) ?></span>/
                            <span id="totalCountFooter"><?= count($disciplines) ?></span> atribuídos
                        </span>
                        <button type="submit" class="btn-ci success btn-lg">
                            <i class="fas fa-save me-2"></i>Salvar Todas as Atribuições
                        </button>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
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
    
    // Inicializar switches
    document.querySelectorAll('.ci-switch-track').forEach(track => {
        const input = track.previousElementSibling;
        if (input && input.type === 'checkbox' && input.checked) {
            track.classList.add('checked');
        }
    });
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
    // Anual - Verde
    document.querySelectorAll('tr.period-anual').forEach(row => {
        row.style.borderLeft = '3px solid var(--success)';
    });
    // 1º Semestre - Azul
    document.querySelectorAll('tr.period-1º-semestre').forEach(row => {
        row.style.borderLeft = '3px solid var(--accent)';
    });
    // 2º Semestre - Laranja
    document.querySelectorAll('tr.period-2º-semestre').forEach(row => {
        row.style.borderLeft = '3px solid var(--warning)';
    });
});
</script>

<style>
/* Estilos adicionais específicos para esta página */
.sticky-top {
    z-index: 1020;
}

.ci-table td {
    vertical-align: middle;
}

/* Progress bar customizado */
.progress {
    background: var(--surface);
    border-radius: 3px;
    overflow: hidden;
}

.progress-bar {
    border-radius: 3px;
    transition: width 0.3s ease;
}

.progress-bar.bg-success {
    background: var(--success) !important;
}

.progress-bar.bg-warning {
    background: var(--warning) !important;
}

.progress-bar.bg-danger {
    background: var(--danger) !important;
}

/* Badge light para fundos escuros */
.badge-ci.light {
    background: rgba(255,255,255,0.15);
    color: #fff;
}

/* Estilo para linhas por período */
tr.period-anual:hover {
    background: rgba(22,168,125,0.05) !important;
}

tr.period-1º-semestre:hover {
    background: rgba(59,127,232,0.05) !important;
}

tr.period-2º-semestre:hover {
    background: rgba(232,160,32,0.05) !important;
}

/* Animação para o card sticky */
.ci-card.sticky-top {
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        transform: translateY(-20px);
        opacity: 0;
    }
    to {
        transform: translateY(0);
        opacity: 1;
    }
}

/* Responsividade */
@media (max-width: 768px) {
    .ci-table td {
        min-width: 150px;
    }
    
    .ci-table td:first-child {
        min-width: auto;
    }
    
    .stat-icon {
        width: 40px;
        height: 40px;
    }
    
    .badge-ci.p-3 {
        display: inline-block;
        margin-bottom: 10px;
    }
    
    .btn-ci {
        width: 100%;
    }
}
</style>
<?= $this->endSection() ?>