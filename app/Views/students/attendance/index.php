<?= $this->extend('students/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('students/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Presenças</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Month Filter -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-3">
                <label for="month" class="form-label">Mês</label>
                <select class="form-select" id="month" name="month" onchange="this.form.submit()">
                    <?php foreach ($months as $num => $name): ?>
                        <option value="<?= $num ?>" <?= $month == $num ? 'selected' : '' ?>>
                            <?= $name ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label for="year" class="form-label">Ano</label>
                <select class="form-select" id="year" name="year" onchange="this.form.submit()">
                    <?php for ($y = date('Y'); $y >= date('Y') - 2; $y--): ?>
                        <option value="<?= $y ?>" <?= $year == $y ? 'selected' : '' ?>>
                            <?= $y ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
        </form>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6 class="card-title">Total de Dias</h6>
                <h3><?= $statistics->total ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="card-title">Presentes</h6>
                <h3><?= $statistics->present ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h6 class="card-title">Ausentes</h6>
                <h3><?= $statistics->absent ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6 class="card-title">Taxa de Presença</h6>
                <h3><?= $statistics->rate ?>%</h3>
            </div>
        </div>
    </div>
</div>

<!-- Attendance Chart -->
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-chart-line"></i> Evolução Diária
    </div>
    <div class="card-body">
        <canvas id="attendanceChart" style="height: 300px;"></canvas>
    </div>
</div>

<!-- Attendance Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-calendar-check"></i> Registro Diário
    </div>
    <div class="card-body">
        <?php if (!empty($attendances)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Data</th>
                            <th>Disciplina</th>
                            <th>Status</th>
                            <th>Justificação</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($attendances as $att): ?>
                            <tr>
                                <td><?= date('d/m/Y', strtotime($att->attendance_date)) ?></td>
                                <td><?= $att->discipline_name ?? 'Geral' ?></td>
                                <td>
                                    <?php
                                    $statusClass = [
                                        'Presente' => 'success',
                                        'Ausente' => 'danger',
                                        'Atrasado' => 'warning',
                                        'Falta Justificada' => 'info',
                                        'Dispensado' => 'secondary'
                                    ][$att->status] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $statusClass ?>"><?= $att->status ?></span>
                                </td>
                                <td><?= $att->justification ?: '-' ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted text-center">Nenhum registro de presença para este mês.</p>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Prepare chart data
const dates = <?= json_encode(array_map(function($att) { return date('d/m', strtotime($att->attendance_date)); }, $attendances)) ?>;
const statuses = <?= json_encode(array_column($attendances, 'status')) ?>;

// Count occurrences by date
const chartData = {};
dates.forEach((date, index) => {
    if (!chartData[date]) {
        chartData[date] = { Presente: 0, Ausente: 0, Atrasado: 0, Justificado: 0 };
    }
    
    const status = statuses[index];
    if (status === 'Presente') chartData[date].Presente++;
    else if (status === 'Ausente') chartData[date].Ausente++;
    else if (status === 'Atrasado') chartData[date].Atrasado++;
    else if (status === 'Falta Justificada') chartData[date].Justificado++;
});

const labels = Object.keys(chartData);
const presentData = labels.map(date => chartData[date].Presente);
const absentData = labels.map(date => chartData[date].Ausente);
const lateData = labels.map(date => chartData[date].Atrasado);
const justifiedData = labels.map(date => chartData[date].Justificado);

// Create chart
const ctx = document.getElementById('attendanceChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: labels,
        datasets: [
            {
                label: 'Presentes',
                data: presentData,
                backgroundColor: '#28a745'
            },
            {
                label: 'Ausentes',
                data: absentData,
                backgroundColor: '#dc3545'
            },
            {
                label: 'Atrasados',
                data: lateData,
                backgroundColor: '#ffc107'
            },
            {
                label: 'Justificados',
                data: justifiedData,
                backgroundColor: '#17a2b8'
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            x: {
                stacked: true
            },
            y: {
                stacked: true,
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});
</script>
<?= $this->endSection() ?>