<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/reports') ?>">Relatórios</a></li>
            <li class="breadcrumb-item active" aria-current="page">Professores</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-4">
                <label for="academic_year" class="form-label">Ano Letivo</label>
                <select class="form-select" id="academic_year" name="academic_year" onchange="this.form.submit()">
                    <?php if (!empty($academicYears)): ?>
                        <?php foreach ($academicYears as $year): ?>
                            <option value="<?= $year->id ?>" <?= $selectedYear == $year->id ? 'selected' : '' ?>>
                                <?= $year->year_name ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="col-md-8 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                <a href="<?= site_url('admin/reports/teachers') ?>" class="btn btn-secondary ms-2">
                    <i class="fas fa-undo"></i>
                </a>
                <button class="btn btn-success ms-2" onclick="window.print()">
                    <i class="fas fa-print"></i>
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Chart -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-chart-bar"></i> Distribuição de Turmas por Professor
            </div>
            <div class="card-body">
                <canvas id="teachersChart" style="height: 400px;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Teachers Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-chalkboard-teacher"></i> Carga Horária dos Professores
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped table-hover" id="teachersTable">
                <thead>
                    <tr>
                        <th>Professor</th>
                        <th>Email</th>
                        <th>Contacto</th>
                        <th class="text-center">Turmas</th>
                        <th class="text-center">Disciplinas</th>
                        <th class="text-center">Carga Horária</th>
                        <th class="text-center">Média h/turma</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($teachers)): ?>
                        <?php foreach ($teachers as $teacher): ?>
                            <?php $avgHours = $teacher->total_classes > 0 ? round($teacher->total_hours / $teacher->total_classes, 1) : 0; ?>
                            <tr>
                                <td><?= $teacher->first_name ?> <?= $teacher->last_name ?></td>
                                <td><?= $teacher->email ?></td>
                                <td><?= $teacher->phone ?: '-' ?></td>
                                <td class="text-center"><?= $teacher->total_classes ?></td>
                                <td class="text-center"><?= $teacher->total_disciplines ?></td>
                                <td class="text-center"><?= $teacher->total_hours ?> h</td>
                                <td class="text-center"><?= $avgHours ?> h</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7" class="text-center">Nenhum professor encontrado</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot class="table-light">
                    <?php
                    $totalClasses = array_sum(array_column($teachers, 'total_classes'));
                    $totalHours = array_sum(array_column($teachers, 'total_hours'));
                    ?>
                    <tr>
                        <th colspan="3">Totais</th>
                        <th class="text-center"><?= $totalClasses ?></th>
                        <th class="text-center"><?= array_sum(array_column($teachers, 'total_disciplines')) ?></th>
                        <th class="text-center"><?= $totalHours ?> h</th>
                        <th class="text-center"><?= count($teachers) > 0 ? round($totalHours / count($teachers), 1) : 0 ?> h</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
$(document).ready(function() {
    $('#teachersTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[0, 'asc']],
        pageLength: 25
    });
});

// Teachers Chart
const ctx = document.getElementById('teachersChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($chartTeachers) ?>,
        datasets: [
            {
                label: 'Turmas',
                data: <?= json_encode($chartClasses) ?>,
                backgroundColor: '#4e73df'
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
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
</script>
<?= $this->endSection() ?>