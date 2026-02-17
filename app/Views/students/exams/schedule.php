<?= $this->extend('students/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <div>
            <a href="<?= site_url('students/exams') ?>" class="btn btn-primary">
                <i class="fas fa-list"></i> Lista de Exames
            </a>
            <a href="<?= site_url('students/exams/results') ?>" class="btn btn-info ms-2">
                <i class="fas fa-star"></i> Resultados
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('students/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('students/exams') ?>">Exames</a></li>
            <li class="breadcrumb-item active" aria-current="page">Calendário</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Filtro de Mês e Ano -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <i class="fas fa-filter me-2"></i>Filtros do Calendário
    </div>
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-4">
                <label for="month" class="form-label fw-bold">Mês</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                    <select class="form-select" id="month" name="month" onchange="this.form.submit()">
                        <?php foreach ($months as $num => $name): ?>
                            <option value="<?= $num ?>" <?= $month == $num ? 'selected' : '' ?>>
                                <?= $name ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <label for="year" class="form-label fw-bold">Ano</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                    <select class="form-select" id="year" name="year" onchange="this.form.submit()">
                        <?php for ($y = date('Y'); $y <= date('Y') + 2; $y++): ?>
                            <option value="<?= $y ?>" <?= $year == $y ? 'selected' : '' ?>>
                                <?= $y ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
            <div class="col-md-4 d-flex align-items-end">
                <p class="text-muted mb-0">
                    <i class="fas fa-info-circle me-2"></i>
                    Total de <?= count($exams) ?> exame(s) neste período
                </p>
            </div>
        </form>
    </div>
</div>

<!-- Cards de Resumo -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-white-50">Total de Exames</h6>
                        <h2 class="mb-0"><?= count($exams) ?></h2>
                    </div>
                    <i class="fas fa-pencil-alt fa-3x opacity-50"></i>
                </div>
                <div class="mt-3">
                    <small><i class="fas fa-calendar me-1"></i> <?= $months[$month] ?> <?= $year ?></small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-success text-white stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-white-50">Próximos 7 dias</h6>
                        <?php 
                        $nextWeek = date('Y-m-d', strtotime('+7 days'));
                        $upcoming = array_filter($exams, function($e) use ($nextWeek) {
                            return $e->exam_date <= $nextWeek && $e->exam_date >= date('Y-m-d');
                        });
                        ?>
                        <h2 class="mb-0"><?= count($upcoming) ?></h2>
                    </div>
                    <i class="fas fa-clock fa-3x opacity-50"></i>
                </div>
                <div class="mt-3">
                    <small><i class="fas fa-hourglass-half me-1"></i> Exames nos próximos dias</small>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-info text-white stat-card">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="card-title text-white-50">Disciplinas</h6>
                        <?php 
                        $disciplines = array_unique(array_column($exams, 'discipline_name'));
                        ?>
                        <h2 class="mb-0"><?= count($disciplines) ?></h2>
                    </div>
                    <i class="fas fa-book fa-3x opacity-50"></i>
                </div>
                <div class="mt-3">
                    <small><i class="fas fa-graduation-cap me-1"></i> Diferentes disciplinas</small>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Calendário Visual -->
<div class="card">
    <div class="card-header bg-light d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-calendar-alt me-2"></i>Calendário de Exames
            <span class="badge bg-secondary ms-2"><?= $months[$month] ?> <?= $year ?></span>
        </div>
        <div class="btn-group">
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="exportToExcel()">
                <i class="fas fa-file-excel"></i> Exportar
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                <i class="fas fa-print"></i> Imprimir
            </button>
        </div>
    </div>
    <div class="card-body">
        <?php if (!empty($exams)): ?>
            <div class="row mb-3">
                <div class="col-md-12">
                    <div class="d-flex flex-wrap gap-3">
                        <span class="badge bg-primary p-2">
                            <i class="fas fa-circle me-1"></i> Exame Agendado
                        </span>
                        <span class="badge bg-success p-2">
                            <i class="fas fa-circle me-1"></i> Hoje
                        </span>
                        <span class="badge bg-warning p-2">
                            <i class="fas fa-circle me-1"></i> Amanhã
                        </span>
                        <span class="badge bg-secondary p-2">
                            <i class="fas fa-circle me-1"></i> Já Realizado
                        </span>
                    </div>
                </div>
            </div>
            
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-dark">
                        <tr>
                            <th width="120">Data</th>
                            <th width="80">Hora</th>
                            <th>Exame</th>
                            <th>Disciplina</th>
                            <th>Sala</th>
                            <th class="text-center">Status</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $today = date('Y-m-d');
                        $tomorrow = date('Y-m-d', strtotime('+1 day'));
                        
                        foreach ($exams as $exam): 
                            $examDate = $exam->exam_date;
                            $isToday = $examDate == $today;
                            $isTomorrow = $examDate == $tomorrow;
                            $isPast = $examDate < $today;
                            
                            $rowClass = $isToday ? 'table-warning' : ($isTomorrow ? 'table-info' : ($isPast ? 'table-secondary' : ''));
                            $statusClass = $isToday ? 'success' : ($isTomorrow ? 'warning' : ($isPast ? 'secondary' : 'primary'));
                            $statusText = $isToday ? 'Hoje' : ($isTomorrow ? 'Amanhã' : ($isPast ? 'Realizado' : 'Agendado'));
                        ?>
                            <tr class="<?= $rowClass ?>">
                                <td>
                                    <span class="fw-bold"><?= date('d/m/Y', strtotime($exam->exam_date)) ?></span>
                                    <br>
                                    <small class="text-muted"><?= getDayOfWeek($exam->exam_date) ?></small>
                                </td>
                                <td>
                                    <?php if ($exam->exam_time): ?>
                                        <span class="badge bg-secondary p-2">
                                            <i class="fas fa-clock me-1"></i>
                                            <?= date('H:i', strtotime($exam->exam_time)) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= $exam->exam_name ?></strong>
                                    <?php if ($exam->description): ?>
                                        <br>
                                        <small class="text-muted"><?= substr($exam->description, 0, 50) ?>...</small>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-info p-2">
                                        <?= $exam->discipline_name ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($exam->exam_room): ?>
                                        <span class="badge bg-secondary p-2">
                                            <i class="fas fa-door-open me-1"></i>
                                            <?= $exam->exam_room ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-<?= $statusClass ?> p-2">
                                        <i class="fas fa-<?= $isToday ? 'calendar-check' : ($isTomorrow ? 'clock' : ($isPast ? 'history' : 'calendar')) ?> me-1"></i>
                                        <?= $statusText ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button type="button" class="btn btn-sm btn-outline-info" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#examModal<?= $exam->id ?>"
                                            title="Ver detalhes">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Legenda de Cores -->
            <div class="row mt-4">
                <div class="col-md-12">
                    <div class="alert alert-light mb-0">
                        <div class="d-flex flex-wrap gap-4">
                            <div>
                                <span class="badge bg-primary">&nbsp;&nbsp;&nbsp;</span>
                                <span class="ms-2">Agendado</span>
                            </div>
                            <div>
                                <span class="badge bg-success">&nbsp;&nbsp;&nbsp;</span>
                                <span class="ms-2">Hoje</span>
                            </div>
                            <div>
                                <span class="badge bg-warning">&nbsp;&nbsp;&nbsp;</span>
                                <span class="ms-2">Amanhã</span>
                            </div>
                            <div>
                                <span class="badge bg-secondary">&nbsp;&nbsp;&nbsp;</span>
                                <span class="ms-2">Realizado</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhum exame agendado</h5>
                <p class="text-muted mb-4">Não há exames programados para <?= $months[$month] ?> de <?= $year ?>.</p>
                <div class="d-flex justify-content-center gap-2">
                    <button type="button" class="btn btn-outline-primary" onclick="location.href='?month=<?= date('m') ?>&year=<?= date('Y') ?>'">
                        <i class="fas fa-calendar-alt me-2"></i>Mês Atual
                    </button>
                    <button type="button" class="btn btn-outline-secondary" onclick="location.href='?month=<?= date('m', strtotime('+1 month')) ?>&year=<?= date('Y', strtotime('+1 month')) ?>'">
                        <i class="fas fa-arrow-right me-2"></i>Próximo Mês
                    </button>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modals de Detalhes dos Exames -->
<?php if (!empty($exams)): ?>
    <?php foreach ($exams as $exam): ?>
        <div class="modal fade" id="examModal<?= $exam->id ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-info-circle me-2"></i>Detalhes do Exame
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-4">
                            <div class="rounded-circle bg-primary d-inline-flex p-3 mb-3">
                                <i class="fas fa-pencil-alt fa-2x text-white"></i>
                            </div>
                            <h4><?= $exam->exam_name ?></h4>
                            <span class="badge bg-info"><?= $exam->board_name ?? 'Exame' ?></span>
                        </div>
                        
                        <table class="table table-bordered">
                            <tr>
                                <th style="width: 40%">Disciplina</th>
                                <td><?= $exam->discipline_name ?></td>
                            </tr>
                            <tr>
                                <th>Data</th>
                                <td>
                                    <?= date('d/m/Y', strtotime($exam->exam_date)) ?> 
                                    (<?= getDayOfWeek($exam->exam_date) ?>)
                                </td>
                            </tr>
                            <tr>
                                <th>Hora</th>
                                <td><?= $exam->exam_time ? date('H:i', strtotime($exam->exam_time)) : 'Não definida' ?></td>
                            </tr>
                            <tr>
                                <th>Sala</th>
                                <td><?= $exam->exam_room ?: 'Não definida' ?></td>
                            </tr>
                            <tr>
                                <th>Duração</th>
                                <td>-</td>
                            </tr>
                            <?php if ($exam->description): ?>
                            <tr>
                                <th>Descrição</th>
                                <td><?= nl2br($exam->description) ?></td>
                            </tr>
                            <?php endif; ?>
                        </table>
                        
                        <?php 
                        $examDate = strtotime($exam->exam_date);
                        $today = time();
                        $daysDiff = round(($examDate - $today) / (60 * 60 * 24));
                        ?>
                        
                        <?php if ($daysDiff > 0): ?>
                            <div class="alert alert-info mt-3">
                                <i class="fas fa-clock me-2"></i>
                                <strong>Faltam <?= $daysDiff ?> dia(s)</strong> para este exame.
                            </div>
                        <?php elseif ($daysDiff == 0): ?>
                            <div class="alert alert-success mt-3">
                                <i class="fas fa-calendar-check me-2"></i>
                                <strong>Este exame é hoje!</strong> Boa sorte!
                            </div>
                        <?php elseif ($daysDiff < 0): ?>
                            <div class="alert alert-secondary mt-3">
                                <i class="fas fa-history me-2"></i>
                                <strong>Este exame já foi realizado.</strong>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function exportToExcel() {
    const table = document.querySelector('table');
    const rows = Array.from(table.querySelectorAll('tr'));
    
    let csv = [];
    
    rows.forEach(row => {
        const cells = Array.from(row.querySelectorAll('th, td'));
        const rowData = cells.map(cell => {
            // Remover ícones e HTML
            let text = cell.innerText.trim().replace(/,/g, ';');
            // Remover múltiplos espaços
            text = text.replace(/\s+/g, ' ');
            return text;
        });
        csv.push(rowData.join(','));
    });
    
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', 'calendario_exames_<?= $year . $month ?>.csv');
    link.click();
}
</script>

<style>
.stat-card {
    transition: transform 0.2s, box-shadow 0.2s;
    border: none;
    height: 100%;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 5px 20px rgba(0,0,0,0.2);
}

.table td {
    vertical-align: middle;
}

.badge {
    font-size: 0.85em;
    padding: 0.5em 0.75em;
}

.progress {
    border-radius: 10px;
    background-color: #f0f0f0;
}

.progress-bar {
    border-radius: 10px;
    transition: width 0.5s ease;
}

/* Animações */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.card {
    animation: fadeIn 0.3s ease-out;
}

/* Estilo para impressão */
@media print {
    .btn, .page-header .btn, .breadcrumb, .navbar, footer, .modal-footer {
        display: none !important;
    }
    .card {
        border: none !important;
        box-shadow: none !important;
    }
    .table {
        border: 1px solid #ddd !important;
    }
}
</style>
<?= $this->endSection() ?>