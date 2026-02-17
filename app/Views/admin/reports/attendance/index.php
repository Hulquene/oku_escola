<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/reports') ?>">Relatórios</a></li>
            <li class="breadcrumb-item active" aria-current="page">Presenças</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-3">
                <label for="class" class="form-label">Turma</label>
                <select class="form-select" id="class" name="class">
                    <option value="">Todas</option>
                    <?php if (!empty($classes)): ?>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?= $class->id ?>" <?= $selectedClass == $class->id ? 'selected' : '' ?>>
                                <?= $class->class_name ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="discipline" class="form-label">Disciplina</label>
                <select class="form-select" id="discipline" name="discipline">
                    <option value="">Todas</option>
                    <?php if (!empty($disciplines)): ?>
                        <?php foreach ($disciplines as $disc): ?>
                            <option value="<?= $disc->id ?>" <?= $selectedDiscipline == $disc->id ? 'selected' : '' ?>>
                                <?= $disc->discipline_name ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="start_date" class="form-label">Data Início</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="<?= $startDate ?>">
            </div>
            
            <div class="col-md-2">
                <label for="end_date" class="form-label">Data Fim</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="<?= $endDate ?>">
            </div>
            
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                <a href="<?= site_url('admin/reports/attendance') ?>" class="btn btn-secondary ms-2">
                    <i class="fas fa-undo"></i>
                </a>
                <button class="btn btn-success ms-2" onclick="window.print()">
                    <i class="fas fa-print"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <?php
    $totalPresent = array_sum(array_column($summary, 'present'));
    $totalAbsent = array_sum(array_column($summary, 'absent'));
    $totalLate = array_sum(array_column($summary, 'late'));
    $totalJustified = array_sum(array_column($summary, 'justified'));
    $totalRecords = $totalPresent + $totalAbsent + $totalLate + $totalJustified;
    $attendanceRate = $totalRecords > 0 ? round(($totalPresent / $totalRecords) * 100, 1) : 0;
    ?>
    
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6 class="card-title">Taxa de Presença</h6>
                <h2><?= $attendanceRate ?>%</h2>
                <small><?= $totalPresent ?> presenças</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="card-title">Presentes</h6>
                <h2><?= $totalPresent ?></h2>
                <small><?= $totalRecords > 0 ? round(($totalPresent / $totalRecords) * 100, 1) : 0 ?>%</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h6 class="card-title">Ausentes</h6>
                <h2><?= $totalAbsent ?></h2>
                <small><?= $totalRecords > 0 ? round(($totalAbsent / $totalRecords) * 100, 1) : 0 ?>%</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <h6 class="card-title">Atrasados</h6>
                <h2><?= $totalLate ?></h2>
                <small><?= $totalRecords > 0 ? round(($totalLate / $totalRecords) * 100, 1) : 0 ?>%</small>
            </div>
        </div>
    </div>
</div>

<!-- Daily Chart -->
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-chart-line"></i> Presenças Diárias
    </div>
    <div class="card-body">
        <canvas id="dailyChart" style="height: 300px;"></canvas>
    </div>
</div>

<!-- Summary by Class -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-table"></i> Resumo por Turma
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>Turma</th>
                        <th class="text-center">Total</th>
                        <th class="text-center">Presentes</th>
                        <th class="text-center">Ausentes</th>
                        <th class="text-center">Atrasados</th>
                        <th class="text-center">Justificados</th>
                        <th class="text-center">Taxa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($summary)): ?>
                        <?php foreach ($summary as $row): ?>
                            <?php 
                            $total = $row->present + $row->absent + $row->late + $row->justified;
                            $rate = $total > 0 ? round(($row->present / $total) * 100, 1) : 0;
                            ?>
                            <tr>
                                <td><?= $row->class_name ?></td>
                                <td class="text-center"><?= $total ?></td>
                                <td class="text-center text-success"><?= $row->present ?></td>
                                <td class="text-center text-danger"><?= $row->absent ?></td>
                                <td class="text-center text-warning"><?= $row->late ?></td>
                                <td class="text-center text-info"><?= $row->justified ?></td>
                                <td class="text-center">
                                    <div class="progress" style="height: 20px;">
                                        <div class="progress-bar bg-success" role="progressbar" 
                                             style="width: <?= $rate ?>%" 
                                             aria-valuenow="<?= $rate ?>" 
                                             aria-valuemin="0" 
                                             aria-valuemax="100">
                                            <?= $rate ?>%
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">Nenhum dado encontrado para o período</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Daily Chart
const ctx = document.getElementById('dailyChart').getContext('2d');
new Chart(ctx, {
    type: 'line',
    data: {
        labels: <?= json_encode($chartLabels) ?>,
        datasets: [
            {
                label: 'Presentes',
                data: <?= json_encode($chartPresent) ?>,
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.3
            },
            {
                label: 'Total',
                data: <?= json_encode($chartTotal) ?>,
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                tension: 0.3
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
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