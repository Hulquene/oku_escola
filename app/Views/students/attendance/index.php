<?= $this->extend('students/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center flex-wrap">
        <h1 class="display-5 fw-bold mb-0">
            <i class="fas fa-calendar-check me-3 text-primary"></i><?= $title ?? 'Minhas Presenças' ?>
        </h1>
        <div class="mt-2 mt-md-0">
            <a href="<?= site_url('students/attendance/history') ?>" class="btn btn-outline-primary">
                <i class="fas fa-history"></i> Histórico Completo
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('students/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Presenças</li>
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

<!-- Filtro de Mês -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-4">
                <label for="month" class="form-label fw-semibold text-muted small text-uppercase">
                    <i class="fas fa-calendar me-2"></i>MÊS
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-0">
                        <i class="fas fa-calendar-alt text-primary"></i>
                    </span>
                    <select class="form-select border-0 bg-light" id="month" name="month" onchange="this.form.submit()">
                        <?php foreach ($months as $num => $name): ?>
                            <option value="<?= $num ?>" <?= $month == $num ? 'selected' : '' ?>>
                                <?= $name ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            
            <div class="col-md-4">
                <label for="year" class="form-label fw-semibold text-muted small text-uppercase">
                    <i class="fas fa-calendar-alt me-2"></i>ANO
                </label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-0">
                        <i class="fas fa-calendar text-primary"></i>
                    </span>
                    <select class="form-select border-0 bg-light" id="year" name="year" onchange="this.form.submit()">
                        <?php for ($y = date('Y'); $y >= date('Y') - 2; $y--): ?>
                            <option value="<?= $y ?>" <?= $year == $y ? 'selected' : '' ?>>
                                <?= $y ?>
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
            </div>
            
            <div class="col-md-4 d-flex align-items-end">
                <div class="bg-light rounded-3 p-3 w-100">
                    <div class="d-flex align-items-center">
                        <div class="bg-primary rounded-circle p-2 me-3">
                            <i class="fas fa-percentage text-white"></i>
                        </div>
                        <div>
                            <span class="text-muted small">Taxa de Presença</span>
                            <h3 class="mb-0 fw-bold"><?= $statistics->rate ?>%</h3>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Cards de Estatísticas -->
<div class="row g-4 mb-5">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-primary bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-calendar-day fa-2x text-primary"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Total de Dias</h6>
                        <h2 class="mb-0 fw-bold"><?= $statistics->total ?></h2>
                        <small class="text-muted">
                            <i class="fas fa-calendar me-1"></i><?= $months[$month] ?> <?= $year ?>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-success bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-user-check fa-2x text-success"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Presentes</h6>
                        <h2 class="mb-0 fw-bold"><?= $statistics->present ?></h2>
                        <small class="text-success">
                            <i class="fas fa-arrow-up me-1"></i><?= $statistics->total > 0 ? round(($statistics->present / $statistics->total) * 100, 1) : 0 ?>%
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-warning bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-clock fa-2x text-warning"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Atrasos</h6>
                        <h2 class="mb-0 fw-bold"><?= $statistics->late ?></h2>
                        <small class="text-warning">
                            <i class="fas fa-clock me-1"></i><?= $statistics->total > 0 ? round(($statistics->late / $statistics->total) * 100, 1) : 0 ?>%
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-body">
                <div class="d-flex align-items-center">
                    <div class="flex-shrink-0">
                        <div class="bg-danger bg-opacity-10 rounded-circle p-3">
                            <i class="fas fa-user-times fa-2x text-danger"></i>
                        </div>
                    </div>
                    <div class="flex-grow-1 ms-3">
                        <h6 class="text-muted mb-1">Ausências</h6>
                        <h2 class="mb-0 fw-bold"><?= $statistics->absent ?></h2>
                        <small class="text-danger">
                            <i class="fas fa-arrow-down me-1"></i><?= $statistics->total > 0 ? round(($statistics->absent / $statistics->total) * 100, 1) : 0 ?>%
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gráfico de Presenças -->
<div class="card border-0 shadow-sm mb-5">
    <div class="card-header bg-transparent border-0 pt-4">
        <h5 class="mb-0">
            <i class="fas fa-chart-line me-2 text-primary"></i>
            Evolução Diária - <?= $months[$month] ?> <?= $year ?>
        </h5>
    </div>
    <div class="card-body">
        <canvas id="attendanceChart" style="height: 350px; width: 100%;"></canvas>
    </div>
</div>

<!-- Calendário de Presenças -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-transparent border-0 pt-4">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-calendar-alt me-2 text-primary"></i>
                Calendário de Presenças
            </h5>
            <div>
                <span class="badge bg-success me-2">Presente</span>
                <span class="badge bg-warning me-2">Atrasado</span>
                <span class="badge bg-info me-2">Justificado</span>
                <span class="badge bg-danger">Ausente</span>
            </div>
        </div>
    </div>
    <div class="card-body">
        <?php
        // Gerar dias do mês
        $firstDay = mktime(0, 0, 0, $month, 1, $year);
        $daysInMonth = date('t', $firstDay);
        $firstDayOfWeek = date('w', $firstDay);
        $dayNames = ['Dom', 'Seg', 'Ter', 'Qua', 'Qui', 'Sex', 'Sáb'];
        
        // Organizar presenças por dia
        $attendanceByDay = [];
        foreach ($attendances as $att) {
            $day = (int)date('d', strtotime($att->attendance_date));
            $attendanceByDay[$day] = $att;
        }
        ?>
        
        <!-- Dias da semana -->
        <div class="row g-2 mb-3">
            <?php foreach ($dayNames as $name): ?>
                <div class="col text-center fw-semibold text-muted"><?= $name ?></div>
            <?php endforeach; ?>
        </div>
        
        <!-- Grid do calendário -->
        <div class="calendar-grid">
            <?php
            // Dias vazios no início
            for ($i = 0; $i < $firstDayOfWeek; $i++): 
            ?>
                <div class="calendar-day empty"></div>
            <?php endfor; ?>
            
            <?php for ($day = 1; $day <= $daysInMonth; $day++): 
                $dateStr = sprintf('%04d-%02d-%02d', $year, $month, $day);
                $isToday = $dateStr == date('Y-m-d');
                $hasAttendance = isset($attendanceByDay[$day]);
                
                if ($hasAttendance) {
                    $att = $attendanceByDay[$day];
                    $status = $att->status;
                    
                    $statusClass = [
                        'Presente' => 'present',
                        'Atrasado' => 'late',
                        'Falta Justificada' => 'justified',
                        'Ausente' => 'absent'
                    ][$status] ?? '';
                    
                    $statusIcon = [
                        'Presente' => 'check-circle',
                        'Atrasado' => 'clock',
                        'Falta Justificada' => 'file-alt',
                        'Ausente' => 'times-circle'
                    ][$status] ?? 'circle';
                }
            ?>
                <div class="calendar-day <?= $isToday ? 'today' : '' ?> <?= $hasAttendance ? $statusClass : '' ?>"
                     <?php if ($hasAttendance): ?>
                     data-bs-toggle="modal" 
                     data-bs-target="#attendanceModal"
                     onclick="showAttendanceDetails(<?= htmlspecialchars(json_encode($att)) ?>)"
                     style="cursor: pointer;"
                     <?php endif; ?>>
                    <span class="day-number"><?= $day ?></span>
                    <?php if ($hasAttendance): ?>
                        <div class="attendance-indicator">
                            <i class="fas fa-<?= $statusIcon ?>"></i>
                            <span class="status-label"><?= $status ?></span>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endfor; ?>
        </div>
    </div>
</div>

<!-- Modal Único de Detalhes -->
<div class="modal fade" id="attendanceModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-calendar-check me-2"></i>
                    Detalhes da Presença
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="attendanceModalBody">
                <!-- Conteúdo preenchido via JavaScript -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Dados para o gráfico
const chartData = {
    labels: <?= json_encode(array_map(function($att) { 
        return date('d/m', strtotime($att->attendance_date)); 
    }, $attendances)) ?>,
    
    present: <?= json_encode(array_map(function($att) { 
        return $att->status == 'Presente' ? 1 : 0; 
    }, $attendances)) ?>,
    
    late: <?= json_encode(array_map(function($att) { 
        return $att->status == 'Atrasado' ? 1 : 0; 
    }, $attendances)) ?>,
    
    justified: <?= json_encode(array_map(function($att) { 
        return $att->status == 'Falta Justificada' ? 1 : 0; 
    }, $attendances)) ?>,
    
    absent: <?= json_encode(array_map(function($att) { 
        return $att->status == 'Ausente' ? 1 : 0; 
    }, $attendances)) ?>
};

// Criar gráfico
const ctx = document.getElementById('attendanceChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: chartData.labels,
        datasets: [
            {
                label: 'Presente',
                data: chartData.present,
                backgroundColor: '#28a745',
                borderRadius: 4
            },
            {
                label: 'Atrasado',
                data: chartData.late,
                backgroundColor: '#ffc107',
                borderRadius: 4
            },
            {
                label: 'Justificado',
                data: chartData.justified,
                backgroundColor: '#17a2b8',
                borderRadius: 4
            },
            {
                label: 'Ausente',
                data: chartData.absent,
                backgroundColor: '#dc3545',
                borderRadius: 4
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            x: {
                stacked: true,
                grid: { display: false }
            },
            y: {
                stacked: true,
                beginAtZero: true,
                ticks: { stepSize: 1 }
            }
        },
        plugins: {
            legend: { position: 'bottom' }
        }
    }
});

// Função para mostrar detalhes no modal
function showAttendanceDetails(attendance) {
    const statusColors = {
        'Presente': 'success',
        'Atrasado': 'warning',
        'Falta Justificada': 'info',
        'Ausente': 'danger'
    };
    
    const statusIcons = {
        'Presente': 'check-circle',
        'Atrasado': 'clock',
        'Falta Justificada': 'file-alt',
        'Ausente': 'times-circle'
    };
    
    const html = `
        <div class="text-center mb-4">
            <div class="bg-${statusColors[attendance.status]} bg-opacity-10 rounded-circle d-inline-flex p-3 mb-3">
                <i class="fas fa-${statusIcons[attendance.status]} fa-2x text-${statusColors[attendance.status]}"></i>
            </div>
            <h4>${attendance.formatted_date || new Date(attendance.attendance_date).toLocaleDateString('pt-PT')}</h4>
        </div>
        
        <div class="row g-3">
            <div class="col-6">
                <div class="bg-light rounded-3 p-3 text-center">
                    <small class="text-muted d-block">Status</small>
                    <span class="badge bg-${statusColors[attendance.status]} p-2">
                        <i class="fas fa-${statusIcons[attendance.status]} me-1"></i>
                        ${attendance.status}
                    </span>
                </div>
            </div>
            
            <div class="col-6">
                <div class="bg-light rounded-3 p-3 text-center">
                    <small class="text-muted d-block">Disciplina</small>
                    <strong>${attendance.discipline_name || 'Geral'}</strong>
                </div>
            </div>
            
            ${attendance.justification ? `
            <div class="col-12">
                <div class="bg-light rounded-3 p-3">
                    <small class="text-muted d-block">Justificação</small>
                    <p class="mb-0">${attendance.justification}</p>
                </div>
            </div>
            ` : ''}
            
            <div class="col-12">
                <div class="bg-light rounded-3 p-3">
                    <small class="text-muted d-block">Informações Adicionais</small>
                    <p class="mb-1 small">
                        <i class="fas fa-clock text-muted me-2"></i>
                        Registado em: ${new Date(attendance.created_at).toLocaleString('pt-PT')}
                    </p>
                    ${attendance.marked_by ? `
                    <p class="mb-0 small">
                        <i class="fas fa-user text-muted me-2"></i>
                        Registado por: Professor
                    </p>
                    ` : ''}
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('attendanceModalBody').innerHTML = html;
}
</script>

<style>
/* Calendário */
.calendar-grid {
    display: grid;
    grid-template-columns: repeat(7, 1fr);
    gap: 10px;
}

.calendar-day {
    background: #f8f9fa;
    border-radius: 12px;
    padding: 15px;
    min-height: 100px;
    display: flex;
    flex-direction: column;
    transition: all 0.3s ease;
    position: relative;
    border: 2px solid transparent;
}

.calendar-day:hover:not(.empty) {
    transform: translateY(-3px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1);
}

.calendar-day.today {
    border-color: #007bff;
    background: #e3f2fd;
}

.calendar-day.present {
    background: #d4edda;
    border-color: #28a745;
}

.calendar-day.late {
    background: #fff3cd;
    border-color: #ffc107;
}

.calendar-day.justified {
    background: #d1ecf1;
    border-color: #17a2b8;
}

.calendar-day.absent {
    background: #f8d7da;
    border-color: #dc3545;
}

.calendar-day.empty {
    background: transparent;
    border: 2px dashed #dee2e6;
    cursor: default;
}

.day-number {
    font-size: 1.3rem;
    font-weight: bold;
    margin-bottom: 5px;
}

.attendance-indicator {
    margin-top: 5px;
    font-size: 0.85rem;
}

.attendance-indicator i {
    margin-right: 5px;
}

.status-label {
    font-size: 0.75rem;
    text-transform: uppercase;
}

/* Cards de estatísticas */
.stat-card {
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15) !important;
}

/* Responsividade */
@media (max-width: 768px) {
    .calendar-grid {
        gap: 5px;
    }
    
    .calendar-day {
        min-height: 70px;
        padding: 8px;
    }
    
    .day-number {
        font-size: 1rem;
    }
    
    .attendance-indicator {
        font-size: 0.7rem;
    }
    
    .status-label {
        display: none;
    }
}
</style>
<?= $this->endSection() ?>