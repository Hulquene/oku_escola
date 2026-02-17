<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/students/attendance') ?>">Presenças</a></li>
            <li class="breadcrumb-item active" aria-current="page">Relatório</li>
        </ol>
    </nav>
</div>

<!-- Filter Form -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-3">
                <label for="student_id" class="form-label">Aluno</label>
                <select class="form-select" id="student_id" name="student_id">
                    <option value="">Todos</option>
                    <?php if (!empty($students)): ?>
                        <?php foreach ($students as $student): ?>
                            <option value="<?= $student->id ?>" <?= $selectedStudent == $student->id ? 'selected' : '' ?>>
                                <?= $student->first_name ?> <?= $student->last_name ?> (<?= $student->student_number ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="start_date" class="form-label">Data Início</label>
                <input type="date" class="form-control" id="start_date" name="start_date" 
                       value="<?= $startDate ?? date('Y-m-01') ?>">
            </div>
            
            <div class="col-md-2">
                <label for="end_date" class="form-label">Data Fim</label>
                <input type="date" class="form-control" id="end_date" name="end_date" 
                       value="<?= $endDate ?? date('Y-m-t') ?>">
            </div>
            
            <div class="col-md-3">
                <label for="class_id" class="form-label">Turma</label>
                <select class="form-select" id="class_id" name="class_id">
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
            
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-file-pdf"></i> Gerar Relatório
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6 class="card-title">Total de Registros</h6>
                <h3><?= $summary->total ?? 0 ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="card-title">Presentes</h6>
                <h3><?= $summary->present ?? 0 ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h6 class="card-title">Ausentes</h6>
                <h3><?= $summary->absent ?? 0 ?></h3>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6 class="card-title">Taxa de Presença</h6>
                <h3>
                    <?php 
                    $rate = $summary->total > 0 ? round(($summary->present / $summary->total) * 100, 1) : 0;
                    echo $rate . '%';
                    ?>
                </h3>
            </div>
        </div>
    </div>
</div>

<!-- Attendance Chart -->
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-chart-bar"></i> Gráfico de Presenças
    </div>
    <div class="card-body">
        <canvas id="attendanceChart" style="height: 300px;"></canvas>
    </div>
</div>

<!-- Attendance Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-table"></i> Detalhamento de Presenças
    </div>
    <div class="card-body">
        <table id="attendanceReportTable" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Aluno</th>
                    <th>Turma</th>
                    <th>Disciplina</th>
                    <th>Status</th>
                    <th>Justificação</th>
                    <th>Registrado por</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($attendances)): ?>
                    <?php foreach ($attendances as $attendance): ?>
                        <tr>
                            <td><?= date('d/m/Y', strtotime($attendance->attendance_date)) ?></td>
                            <td><?= $attendance->first_name ?> <?= $attendance->last_name ?></td>
                            <td><?= $attendance->class_name ?></td>
                            <td><?= $attendance->discipline_name ?? '-' ?></td>
                            <td>
                                <?php
                                $statusClass = [
                                    'Presente' => 'success',
                                    'Ausente' => 'danger',
                                    'Atrasado' => 'warning',
                                    'Falta Justificada' => 'info',
                                    'Dispensado' => 'secondary'
                                ][$attendance->status] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?= $statusClass ?>"><?= $attendance->status ?></span>
                            </td>
                            <td><?= $attendance->justification ?: '-' ?></td>
                            <td><?= $attendance->marked_by_name ?? 'Sistema' ?></td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">Nenhum registro encontrado</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
$(document).ready(function() {
    $('#attendanceReportTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[0, 'desc']],
        pageLength: 50
    });
});

// Attendance Chart
const ctx = document.getElementById('attendanceChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($chartData['labels'] ?? []) ?>,
        datasets: [
            {
                label: 'Presentes',
                data: <?= json_encode($chartData['present'] ?? []) ?>,
                backgroundColor: '#28a745'
            },
            {
                label: 'Ausentes',
                data: <?= json_encode($chartData['absent'] ?? []) ?>,
                backgroundColor: '#dc3545'
            },
            {
                label: 'Atrasados',
                data: <?= json_encode($chartData['late'] ?? []) ?>,
                backgroundColor: '#ffc107'
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