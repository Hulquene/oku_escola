<?= $this->extend('students/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <h1 class="display-5 fw-bold mb-0">
            <i class="fas fa-calendar-alt me-3 text-primary"></i><?= $title ?? 'Calendário de Exames' ?>
        </h1>
        <div class="mt-2 mt-md-0">
            <a href="<?= site_url('students/exams') ?>" class="btn btn-outline-primary me-2">
                <i class="fas fa-list"></i> Lista
            </a>
            <a href="<?= site_url('students/exams/results') ?>" class="btn btn-outline-info">
                <i class="fas fa-star"></i> Resultados
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('students/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('students/exams') ?>">Exames</a></li>
            <li class="breadcrumb-item active" aria-current="page">Calendário</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Informação da Matrícula -->
<?php if (isset($enrollment) && $enrollment): ?>
<div class="alert alert-info border-0 shadow-sm mb-4">
    <div class="d-flex align-items-center">
        <div class="bg-info bg-opacity-25 rounded-circle p-3 me-3">
            <i class="fas fa-graduation-cap fa-2x text-info"></i>
        </div>
        <div>
            <h5 class="alert-heading mb-1">Matrícula Ativa</h5>
            <p class="mb-0">
                <span class="fw-bold">Turma:</span> <?= $enrollment->class_name ?? 'N/A' ?> | 
                <span class="fw-bold">Ano Letivo:</span> <?= $enrollment->year_name ?? date('Y') ?>
            </p>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Filtros Modernos -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="get" class="row g-4">
            <div class="col-md-5">
                <label class="form-label fw-semibold text-muted small text-uppercase">
                    <i class="fas fa-calendar me-2"></i>MÊS
                </label>
                <div class="d-flex gap-2 flex-wrap">
                    <?php 
                    $currentMonth = $month ?? date('m');
                    foreach ($months as $num => $name): 
                        $isActive = $currentMonth == $num;
                    ?>
                        <button type="submit" name="month" value="<?= $num ?>" 
                                class="btn month-btn <?= $isActive ? 'btn-primary active' : 'btn-outline-secondary' ?>">
                            <?= substr($name, 0, 3) ?>
                            <span class="d-none d-md-inline"><?= substr($name, 3) ?></span>
                        </button>
                    <?php endforeach; ?>
                </div>
            </div>
            
            <div class="col-md-4">
                <label class="form-label fw-semibold text-muted small text-uppercase">
                    <i class="fas fa-calendar-alt me-2"></i>ANO
                </label>
                <div class="d-flex gap-2 flex-wrap">
                    <?php for ($y = date('Y'); $y <= date('Y') + 2; $y++): 
                        $isActive = ($year ?? date('Y')) == $y;
                    ?>
                        <button type="submit" name="year" value="<?= $y ?>" 
                                class="btn year-btn <?= $isActive ? 'btn-primary active' : 'btn-outline-secondary' ?>">
                            <?= $y ?>
                        </button>
                    <?php endfor; ?>
                </div>
            </div>
            
            <div class="col-md-3 d-flex align-items-end">
                <div class="bg-light rounded-3 p-3 w-100">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary rounded-circle p-2 me-3">
                            <i class="fas fa-calendar-check text-white"></i>
                        </div>
                        <div>
                            <span class="text-muted small">Total no período</span>
                            <h3 class="mb-0 fw-bold"><?= count($exams ?? []) ?> exame(s)</h3>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Stats Cards Modernos -->
<div class="row g-4 mb-5">
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-pencil-alt fa-2x text-primary"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Total de Exames</h6>
                        <h2 class="mb-0 fw-bold"><?= count($exams ?? []) ?></h2>
                        <small class="text-muted">
                            <i class="fas fa-calendar me-1"></i><?= $months[$month ?? date('m')] ?> <?= $year ?? date('Y') ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-clock fa-2x text-success"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Próximos 7 dias</h6>
                        <?php 
                        $nextWeek = date('Y-m-d', strtotime('+7 days'));
                        $upcoming = array_filter($exams ?? [], function($e) use ($nextWeek) {
                            return $e->exam_date <= $nextWeek && $e->exam_date >= date('Y-m-d');
                        });
                        ?>
                        <h2 class="mb-0 fw-bold"><?= count($upcoming) ?></h2>
                        <small class="text-muted">
                            <i class="fas fa-hourglass-half me-1"></i>Exames nos próximos dias
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-info bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-book fa-2x text-info"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Disciplinas</h6>
                        <?php 
                        $disciplines = array_unique(array_column($exams ?? [], 'discipline_name'));
                        ?>
                        <h2 class="mb-0 fw-bold"><?= count($disciplines) ?></h2>
                        <small class="text-muted">
                            <i class="fas fa-graduation-cap me-1"></i>Diferentes disciplinas
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Mini Calendário Visual -->
<div class="row g-4 mb-5">
    <div class="col-lg-8">
        <!-- Calendário em Grid -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-transparent border-0 pt-4">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">
                        <i class="fas fa-calendar-alt me-2 text-primary"></i>
                        <?= $months[$month ?? date('m')] ?> <?= $year ?? date('Y') ?>
                    </h5>
                    <div>
                        <span class="badge bg-primary me-2">Agendado</span>
                        <span class="badge bg-success me-2">Hoje</span>
                        <span class="badge bg-warning me-2">Amanhã</span>
                        <span class="badge bg-secondary">Realizado</span>
                    </div>
                </div>
            </div>
            <div class="card-body">
                <?php
                // Gerar dias do mês
                $firstDay = mktime(0, 0, 0, $month ?? date('m'), 1, $year ?? date('Y'));
                $daysInMonth = date('t', $firstDay);
                $firstDayOfWeek = date('w', $firstDay);
                $dayNames = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
                
                // Agrupar exames por dia
                $examsByDay = [];
                foreach ($exams as $exam) {
                    $day = (int)date('d', strtotime($exam->exam_date));
                    if (!isset($examsByDay[$day])) {
                        $examsByDay[$day] = [];
                    }
                    $examsByDay[$day][] = $exam;
                }
                ?>
                
                <!-- Dias da semana -->
                <div class="row g-2 mb-3">
                    <?php foreach ($dayNames as $name): ?>
                        <div class="col text-center fw-semibold text-muted"><?= $name ?></div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Dias do mês -->
                <div class="row g-2">
                    <?php 
                    // Dias vazios no início
                    for ($i = 0; $i < $firstDayOfWeek; $i++): 
                    ?>
                        <div class="col">
                            <div class="calendar-day empty"></div>
                        </div>
                    <?php endfor; ?>
                    
                    <?php for ($day = 1; $day <= $daysInMonth; $day++): 
                        $dateStr = sprintf('%04d-%02d-%02d', $year ?? date('Y'), $month ?? date('m'), $day);
                        $isToday = $dateStr == date('Y-m-d');
                        $hasExams = isset($examsByDay[$day]);
                        $examCount = $hasExams ? count($examsByDay[$day]) : 0;
                    ?>
                        <div class="col">
                            <div class="calendar-day <?= $isToday ? 'today' : '' ?> <?= $hasExams ? 'has-exams' : '' ?>"
                                 data-bs-toggle="modal" 
                                 data-bs-target="#dayModal<?= $day ?>"
                                 style="cursor: pointer;">
                                <span class="day-number"><?= $day ?></span>
                                <?php if ($hasExams): ?>
                                    <span class="exam-badge"><?= $examCount ?> exame(s)</span>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endfor; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Próximos Exames em Card -->
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-transparent border-0 pt-4">
                <h5 class="mb-0">
                    <i class="fas fa-clock me-2 text-warning"></i>
                    Próximos Exames
                </h5>
            </div>
            <div class="card-body">
                <?php 
                $upcomingExams = array_filter($exams ?? [], function($e) {
                    return $e->exam_date >= date('Y-m-d');
                });
                usort($upcomingExams, function($a, $b) {
                    return strtotime($a->exam_date) - strtotime($b->exam_date);
                });
                $upcomingExams = array_slice($upcomingExams, 0, 5);
                ?>
                
                <?php if (!empty($upcomingExams)): ?>
                    <div class="timeline">
                        <?php foreach ($upcomingExams as $exam): 
                            $daysLeft = floor((strtotime($exam->exam_date) - time()) / (60 * 60 * 24));
                            $urgencyClass = $daysLeft <= 1 ? 'urgent' : ($daysLeft <= 3 ? 'warning' : 'normal');
                        ?>
                            <div class="timeline-item">
                                <div class="timeline-badge bg-<?= $daysLeft <= 1 ? 'danger' : ($daysLeft <= 3 ? 'warning' : 'primary') ?>">
                                    <i class="fas fa-pencil-alt"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <h6 class="mb-1 fw-bold"><?= $exam->discipline_name ?></h6>
                                        <span class="badge bg-<?= $daysLeft <= 1 ? 'danger' : ($daysLeft <= 3 ? 'warning' : 'primary') ?>">
                                            <?= $daysLeft == 0 ? 'Hoje' : ($daysLeft == 1 ? 'Amanhã' : $daysLeft . ' dias') ?>
                                        </span>
                                    </div>
                                    <p class="mb-1 small text-muted">
                                        <i class="fas fa-calendar me-1"></i><?= date('d/m/Y', strtotime($exam->exam_date)) ?>
                                        <i class="fas fa-clock ms-2 me-1"></i><?= $exam->exam_time ? date('H:i', strtotime($exam->exam_time)) : '--:--' ?>
                                    </p>
                                    <p class="mb-0 small">
                                        <span class="badge bg-info"><?= $exam->board_type ?? 'Normal' ?></span>
                                        <?php if ($exam->exam_room): ?>
                                            <span class="badge bg-secondary"><i class="fas fa-door-open me-1"></i><?= $exam->exam_room ?></span>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-calendar-check fa-3x text-muted mb-3"></i>
                        <p class="text-muted">Nenhum exame próximo</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Lista de Exames do Mês -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent border-0 pt-4">
        <h5 class="mb-0">
            <i class="fas fa-list me-2 text-primary"></i>
            Lista de Exames - <?= $months[$month ?? date('m')] ?> <?= $year ?? date('Y') ?>
        </h5>
    </div>
    <div class="card-body">
        <?php if (!empty($exams)): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Data</th>
                            <th>Hora</th>
                            <th>Disciplina</th>
                            <th>Exame</th>
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
                            
                            $statusClass = $isToday ? 'success' : ($isTomorrow ? 'warning' : ($isPast ? 'secondary' : 'primary'));
                            $statusText = $isToday ? 'Hoje' : ($isTomorrow ? 'Amanhã' : ($isPast ? 'Realizado' : 'Agendado'));
                        ?>
                            <tr>
                                <td>
                                    <span class="fw-bold"><?= date('d/m/Y', strtotime($exam->exam_date)) ?></span>
                                    <br>
                                    <small class="text-muted"><?= getDayOfWeek($exam->exam_date) ?></small>
                                </td>
                                <td>
                                    <?php if ($exam->exam_time): ?>
                                        <span class="badge bg-light text-dark p-2">
                                            <i class="far fa-clock me-1"></i>
                                            <?= date('H:i', strtotime($exam->exam_time)) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="fw-bold"><?= $exam->discipline_name ?></span>
                                </td>
                                <td>
                                    <span class="badge bg-info bg-opacity-10 text-info p-2">
                                        <?= $exam->board_type ?? 'Normal' ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($exam->exam_room): ?>
                                        <span class="badge bg-light text-dark p-2">
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
                                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#examModal<?= $exam->id ?>">
                                        <i class="fas fa-eye me-1"></i>Detalhes
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-calendar-times fa-4x text-muted mb-3"></i>
                <h5 class="text-muted">Nenhum exame agendado</h5>
                <p class="text-muted mb-4">Não há exames programados para este período.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modals por Dia (para o calendário) -->
<?php if (!empty($examsByDay)): ?>
    <?php foreach ($examsByDay as $day => $dayExams): ?>
        <div class="modal fade" id="dayModal<?= $day ?>" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-primary text-white">
                        <h5 class="modal-title">
                            <i class="fas fa-calendar-day me-2"></i>
                            Exames - <?= sprintf('%02d/%02d/%04d', $day, $month ?? date('m'), $year ?? date('Y')) ?>
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <?php foreach ($dayExams as $exam): ?>
                            <div class="card mb-3 border-0 bg-light">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h6 class="mb-1 fw-bold"><?= $exam->discipline_name ?></h6>
                                            <p class="mb-2">
                                                <span class="badge bg-info me-2"><?= $exam->board_type ?? 'Normal' ?></span>
                                                <?php if ($exam->exam_time): ?>
                                                    <span class="badge bg-secondary"><i class="far fa-clock me-1"></i><?= date('H:i', strtotime($exam->exam_time)) ?></span>
                                                <?php endif; ?>
                                            </p>
                                            <?php if ($exam->exam_room): ?>
                                                <p class="mb-1"><i class="fas fa-door-open me-2 text-muted"></i>Sala: <?= $exam->exam_room ?></p>
                                            <?php endif; ?>
                                        </div>
                                        <button type="button" class="btn btn-sm btn-outline-primary" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#examModal<?= $exam->id ?>"
                                                data-bs-dismiss="modal">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    </div>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

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
                            <div class="bg-primary bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                                <i class="fas fa-pencil-alt fa-2x text-primary"></i>
                            </div>
                            <h4><?= $exam->discipline_name ?></h4>
                            <span class="badge bg-info"><?= $exam->board_type ?? 'Normal' ?></span>
                        </div>
                        
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="bg-light rounded-3 p-3 text-center">
                                    <small class="text-muted d-block">Data</small>
                                    <strong><?= date('d/m/Y', strtotime($exam->exam_date)) ?></strong>
                                    <br>
                                    <small class="text-muted"><?= getDayOfWeek($exam->exam_date) ?></small>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-light rounded-3 p-3 text-center">
                                    <small class="text-muted d-block">Hora</small>
                                    <strong><?= $exam->exam_time ? date('H:i', strtotime($exam->exam_time)) : '--:--' ?></strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-light rounded-3 p-3 text-center">
                                    <small class="text-muted d-block">Sala</small>
                                    <strong><?= $exam->exam_room ?: 'Não definida' ?></strong>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="bg-light rounded-3 p-3 text-center">
                                    <small class="text-muted d-block">Duração</small>
                                    <strong><?= $exam->duration_minutes ?? 120 ?> min</strong>
                                </div>
                            </div>
                        </div>
                        
                        <?php 
                        $examDate = strtotime($exam->exam_date);
                        $today = time();
                        $daysDiff = round(($examDate - $today) / (60 * 60 * 24));
                        ?>
                        
                        <?php if ($daysDiff > 0): ?>
                            <div class="alert alert-info mt-4">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-clock fa-2x me-3"></i>
                                    <div>
                                        <strong>Faltam <?= $daysDiff ?> dia(s)</strong>
                                        <p class="mb-0 small">Prepare-se para o exame!</p>
                                    </div>
                                </div>
                            </div>
                        <?php elseif ($daysDiff == 0): ?>
                            <div class="alert alert-success mt-4">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-calendar-check fa-2x me-3"></i>
                                    <div>
                                        <strong>Este exame é hoje!</strong>
                                        <p class="mb-0 small">Boa sorte!</p>
                                    </div>
                                </div>
                            </div>
                        <?php elseif ($daysDiff < 0): ?>
                            <div class="alert alert-secondary mt-4">
                                <div class="d-flex align-items-center">
                                    <i class="fas fa-history fa-2x me-3"></i>
                                    <div>
                                        <strong>Este exame já foi realizado.</strong>
                                        <p class="mb-0 small">Consulte seus resultados.</p>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>
                        
                        <?php if ($exam->observations ?? false): ?>
                            <div class="mt-4">
                                <strong>Observações:</strong>
                                <p class="text-muted mt-2"><?= nl2br($exam->observations) ?></p>
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
        // Remover coluna de ações (última)
        const filteredCells = cells.filter((_, index) => index !== cells.length - 1);
        const rowData = filteredCells.map(cell => {
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
    link.setAttribute('download', 'calendario_exames_<?= $year ?? date('Y') . ($month ?? date('m')) ?>.csv');
    link.click();
}
</script>

<style>
/* Estilos Modernos */
.month-btn, .year-btn {
    min-width: 60px;
    transition: all 0.3s ease;
}
.month-btn.active, .year-btn.active {
    transform: scale(1.05);
    box-shadow: 0 4px 10px rgba(0,123,255,0.3);
}

/* Calendário */
.calendar-day {
    aspect-ratio: 1;
    background: #f8f9fa;
    border-radius: 10px;
    padding: 8px;
    position: relative;
    transition: all 0.3s ease;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;
}

.calendar-day:hover {
    background: #e9ecef;
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

.calendar-day.today {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
}

.calendar-day.has-exams {
    border: 2px solid #28a745;
}

.calendar-day .day-number {
    font-weight: bold;
    font-size: 1.1rem;
}

.calendar-day .exam-badge {
    font-size: 0.7rem;
    background: rgba(40, 167, 69, 0.2);
    padding: 2px 6px;
    border-radius: 12px;
    margin-top: 4px;
}

.calendar-day.today .exam-badge {
    background: rgba(255,255,255,0.2);
    color: white;
}

.calendar-day.empty {
    background: transparent;
    border: 1px dashed #dee2e6;
}

/* Timeline */
.timeline {
    position: relative;
    padding: 10px 0;
}

.timeline-item {
    position: relative;
    padding-left: 50px;
    margin-bottom: 20px;
}

.timeline-badge {
    position: absolute;
    left: 0;
    top: 0;
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1rem;
    box-shadow: 0 3px 10px rgba(0,0,0,0.2);
}

.timeline-content {
    background: #f8f9fa;
    padding: 12px 15px;
    border-radius: 10px;
    transition: all 0.3s ease;
}

.timeline-content:hover {
    background: #e9ecef;
    transform: translateX(5px);
}

.timeline-item.urgent .timeline-content {
    border-left: 3px solid #dc3545;
}

.timeline-item.warning .timeline-content {
    border-left: 3px solid #ffc107;
}

.timeline-item.normal .timeline-content {
    border-left: 3px solid #007bff;
}

/* Cards */
.stat-card {
    transition: all 0.3s ease;
    border: none;
    border-radius: 15px;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
}

/* Animações */
@keyframes slideIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.card {
    animation: slideIn 0.5s ease-out;
}

/* Responsividade */
@media (max-width: 768px) {
    .month-btn, .year-btn {
        min-width: 45px;
        font-size: 0.8rem;
    }
    
    .calendar-day .day-number {
        font-size: 0.9rem;
    }
    
    .calendar-day .exam-badge {
        font-size: 0.6rem;
    }
}
</style>

<?= $this->endSection() ?>