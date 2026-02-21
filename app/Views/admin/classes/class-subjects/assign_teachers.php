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

<!-- Filtro por Semestre -->
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
                <select class="form-select" id="semesterFilter" onchange="filterBySemester(this.value)">
                    <option value="">Todos os Períodos</option>
                    <?php
                    // Verificar se há disciplinas sem semestre atribuído
                    $hasUnassigned = false;
                    foreach ($disciplines as $disc) {
                        if (empty($disc->semester_id)) {
                            $hasUnassigned = true;
                            break;
                        }
                    }
                    if ($hasUnassigned):
                    ?>
                        <option value="unassigned">⚠️ Sem Período Definido</option>
                    <?php endif; ?>
                    
                    <?php
                    // Agrupar disciplinas por semestre para criar opções de filtro
                    $semesters = [];
                    foreach ($disciplines as $disc) {
                        if (!empty($disc->semester_id) && !isset($semesters[$disc->semester_id])) {
                            $semesters[$disc->semester_id] = $disc->semester_name ?? 'Período ' . $disc->semester_id;
                        }
                    }
                    foreach ($semesters as $id => $name):
                    ?>
                        <option value="<?= $id ?>"><?= $name ?></option>
                    <?php endforeach; ?>
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
            <span class="badge bg-light text-dark me-2" id="selectedSemesterBadge">Todos os períodos</span>
        </div>
    </div>
    <div class="card-body">
        <form action="<?= site_url('admin/classes/class-subjects/save-teachers') ?>" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="class_id" value="<?= $class->id ?>">
            
            <div class="alert alert-info">
                <i class="fas fa-info-circle me-2"></i>
                <strong>Nota:</strong> Disciplinas anuais aparecem para cada semestre separadamente, 
                podendo ter professores diferentes em cada período.
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
                                // Determinar classe CSS e badge baseada no período
                                $periodClass = '';
                                $periodLabel = '';
                                $periodStatus = '';
                                $dataSemester = '';
                                
                                if (empty($disc->semester_id)) {
                                    // Sem semestre atribuído
                                    $periodClass = 'table-secondary';
                                    $periodLabel = '<span class="badge bg-secondary" style="background-color: #6c757d !important;"><i class="fas fa-question-circle me-1"></i>Sem Período</span>';
                                    $periodStatus = 'warning';
                                    $dataSemester = 'unassigned';
                                } elseif (!empty($disc->semester_name)) {
                                    $dataSemester = $disc->semester_id;
                                    
                                    if (strpos($disc->semester_name, '1º') !== false) {
                                        $periodClass = 'table-primary';
                                        $periodLabel = '<span class="badge bg-primary"><i class="fas fa-sun me-1"></i>' . $disc->semester_name . '</span>';
                                    } elseif (strpos($disc->semester_name, '2º') !== false) {
                                        $periodClass = 'table-warning';
                                        $periodLabel = '<span class="badge bg-warning text-dark"><i class="fas fa-cloud-sun me-1"></i>' . $disc->semester_name . '</span>';
                                    } elseif (strpos($disc->semester_name, '3º') !== false) {
                                        $periodClass = 'table-info';
                                        $periodLabel = '<span class="badge bg-info"><i class="fas fa-cloud-rain me-1"></i>' . $disc->semester_name . '</span>';
                                    } else {
                                        $periodClass = 'table-light';
                                        $periodLabel = '<span class="badge bg-secondary">' . $disc->semester_name . '</span>';
                                    }
                                } else {
                                    // Tem semester_id mas não tem nome (inconsistência)
                                    $periodClass = 'table-danger';
                                    $periodLabel = '<span class="badge bg-danger"><i class="fas fa-exclamation-triangle me-1"></i>Período Inválido (ID: ' . $disc->semester_id . ')</span>';
                                    $periodStatus = 'danger';
                                    $dataSemester = $disc->semester_id;
                                }
                            ?>
                                <tr class="<?= $periodClass ?>" data-semester="<?= $dataSemester ?>">
                                    <td class="text-center"><?= $index + 1 ?></td>
                                    <td>
                                        <strong><?= $disc->discipline_name ?></strong>
                                        <?php if (isset($disc->is_mandatory) && $disc->is_mandatory): ?>
                                            <span class="badge bg-warning ms-2" title="Obrigatória">Obrig.</span>
                                        <?php endif; ?>
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
                                        <?= $periodLabel ?>
                                        <?php if (!empty($disc->semester_id) && !empty($disc->semester_name)): ?>
                                            <br><small class="text-muted"><i class="far fa-calendar-alt me-1"></i>ID: <?= $disc->semester_id ?></small>
                                            <?php if (!empty($disc->semester_start) && !empty($disc->semester_end)): ?>
                                                <br><small class="text-muted"><i class="far fa-clock me-1"></i>
                                                    <?= date('d/m', strtotime($disc->semester_start)) ?> - <?= date('d/m', strtotime($disc->semester_end)) ?>
                                                </small>
                                            <?php endif; ?>
                                        <?php elseif (empty($disc->semester_id)): ?>
                                            <br><small class="text-warning"><i class="fas fa-exclamation-triangle me-1"></i>Período não definido</small>
                                            <br><small class="text-muted">Configure o período da disciplina</small>
                                        <?php endif; ?>
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
                            $unassignedCount = 0;
                            $unassignedAssigned = 0;
                            
                            foreach ($disciplines as $disc) {
                                if (empty($disc->semester_id)) {
                                    $unassignedCount++;
                                    if ($disc->teacher_id) {
                                        $unassignedAssigned++;
                                    }
                                } else {
                                    $key = $disc->semester_name ?? 'Período ' . $disc->semester_id;
                                    if (!isset($stats[$key])) {
                                        $stats[$key] = ['total' => 0, 'assigned' => 0, 'id' => $disc->semester_id];
                                    }
                                    $stats[$key]['total']++;
                                    if ($disc->teacher_id) {
                                        $stats[$key]['assigned']++;
                                    }
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
                                
                                <?php if ($unassignedCount > 0): ?>
                                    <div class="col-12 mb-2 mt-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-warning"><i class="fas fa-exclamation-triangle me-1"></i>Sem Período:</small>
                                            <span class="badge bg-<?= $unassignedAssigned == $unassignedCount ? 'success' : ($unassignedAssigned > 0 ? 'warning' : 'danger') ?>">
                                                <?= $unassignedAssigned ?>/<?= $unassignedCount ?>
                                            </span>
                                        </div>
                                        <div class="progress mt-1" style="height: 5px;">
                                            <div class="progress-bar bg-<?= $unassignedAssigned == $unassignedCount ? 'success' : ($unassignedAssigned > 0 ? 'warning' : 'danger') ?>" 
                                                 style="width: <?= ($unassignedAssigned / $unassignedCount) * 100 ?>%"></div>
                                        </div>
                                    </div>
                                <?php endif; ?>
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
// Função para filtrar por semestre
function filterBySemester(semesterId) {
    const rows = document.querySelectorAll('#disciplinesTableBody tr');
    let visibleCount = 0;
    let assignedCount = 0;
    
    rows.forEach(row => {
        let shouldShow = false;
        
        if (semesterId === '') {
            shouldShow = true;
        } else if (semesterId === 'unassigned' && row.dataset.semester === '') {
            shouldShow = true;
        } else if (row.dataset.semester === semesterId) {
            shouldShow = true;
        }
        
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
    const badge = document.getElementById('selectedSemesterBadge');
    if (badge) {
        if (semesterId === '') {
            badge.textContent = 'Todos os períodos';
        } else if (semesterId === 'unassigned') {
            badge.textContent = 'Disciplinas sem período';
        } else {
            const option = document.querySelector(`#semesterFilter option[value="${semesterId}"]`);
            badge.textContent = option ? option.textContent : 'Período selecionado';
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
    filterBySemester('');
});

// Atualizar contador quando seleção de professor muda
document.querySelectorAll('select[name^="assignments"]').forEach(select => {
    select.addEventListener('change', function() {
        const semesterId = document.getElementById('semesterFilter')?.value || '';
        filterBySemester(semesterId);
    });
});

// Destacar linhas com problemas
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('tr[data-semester="unassigned"]').forEach(row => {
        // Pode adicionar um tooltip ou destaque adicional
        const periodCell = row.querySelector('td:nth-child(5)');
        if (periodCell) {
            periodCell.style.backgroundColor = '#fff3cd';
        }
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
/* Estilo para disciplinas sem período */
tr[data-semester="unassigned"] {
    opacity: 0.9;
}
tr[data-semester="unassigned"]:hover {
    background-color: #fff3cd !important;
}
/* Tooltip personalizado */
.period-warning {
    cursor: help;
    border-bottom: 1px dashed #ffc107;
}
</style>

<?= $this->endSection() ?>