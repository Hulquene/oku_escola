<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2">Resumo dos Exames de Recurso</h1>
            <p class="text-muted mb-0">
                <i class="fas fa-chart-bar me-1"></i>
                <?= $semester->semester_name ?? '' ?>
            </p>
        </div>
        <div>
            <a href="<?= site_url('admin/exams/appeals') ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/exams/appeals') ?>">Recursos</a></li>
            <li class="breadcrumb-item active">Resumo</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Cards de Estatísticas -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted mb-1">Total de Exames</h6>
                <h2 class="mb-0"><?= $stats['total_exams'] ?></h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted mb-1">Total de Alunos</h6>
                <h2 class="mb-0"><?= $stats['total_students'] ?></h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted mb-1">Aprovados no Recurso</h6>
                <h2 class="mb-0 text-success"><?= $stats['total_approved'] ?></h2>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card">
            <div class="card-body">
                <h6 class="text-muted mb-1">Reprovados no Recurso</h6>
                <h2 class="mb-0 text-danger"><?= $stats['total_failed'] ?></h2>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-chart-pie me-2 text-success"></i>
                    Resultados dos Recursos
                </h5>
            </div>
            <div class="card-body">
                <canvas id="resultsChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header bg-white py-3">
                <h5 class="mb-0 fw-semibold">
                    <i class="fas fa-chart-bar me-2 text-info"></i>
                    Desempenho por Exame
                </h5>
            </div>
            <div class="card-body">
                <canvas id="examsChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Tabela de Exames -->
<div class="card">
    <div class="card-header bg-white py-3">
        <h5 class="mb-0 fw-semibold">
            <i class="fas fa-table me-2 text-primary"></i>
            Detalhamento por Exame
        </h5>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="border-0 py-3 ps-3">Data</th>
                        <th class="border-0 py-3">Turma</th>
                        <th class="border-0 py-3">Disciplina</th>
                        <th class="border-0 py-3 text-center">Alunos</th>
                        <th class="border-0 py-3 text-center">Presentes</th>
                        <th class="border-0 py-3 text-center">Média</th>
                        <th class="border-0 py-3 text-center">Aprovados</th>
                        <th class="border-0 py-3 text-center pe-3">Reprovados</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($appealExams as $exam): ?>
                        <?php
                        // Get results for this exam
                        $results = $this->examResultModel
                            ->where('exam_schedule_id', $exam->id)
                            ->findAll();
                        
                        $approved = count(array_filter($results, fn($r) => $r->score >= 10));
                        $failed = count($results) - $approved;
                        ?>
                        <tr>
                            <td class="ps-3"><?= date('d/m/Y', strtotime($exam->exam_date)) ?></td>
                            <td><?= $exam->class_name ?></td>
                            <td><?= $exam->discipline_name ?></td>
                            <td class="text-center"><?= $exam->total_students ?></td>
                            <td class="text-center"><?= $exam->results_count ?></td>
                            <td class="text-center">
                                <span class="fw-bold">
                                    <?= number_format($exam->average_score, 1) ?>
                                </span>
                            </td>
                            <td class="text-center text-success"><?= $approved ?></td>
                            <td class="text-center pe-3 text-danger"><?= $failed ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
// Gráfico de Resultados
const ctx1 = document.getElementById('resultsChart').getContext('2d');
new Chart(ctx1, {
    type: 'pie',
    data: {
        labels: ['Aprovados no Recurso', 'Reprovados no Recurso'],
        datasets: [{
            data: [<?= $stats['total_approved'] ?>, <?= $stats['total_failed'] ?>],
            backgroundColor: ['#198754', '#dc3545'],
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Gráfico de Exames
const ctx2 = document.getElementById('examsChart').getContext('2d');
new Chart(ctx2, {
    type: 'bar',
    data: {
        labels: [<?php foreach ($appealExams as $exam): ?>'<?= $exam->discipline_name ?>', <?php endforeach; ?>],
        datasets: [{
            label: 'Média das Notas',
            data: [<?php foreach ($appealExams as $exam): ?><?= $exam->average_score ?>, <?php endforeach; ?>],
            backgroundColor: '#0d6efd',
            borderRadius: 5
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                max: 20
            }
        }
    }
});
</script>

<?= $this->endSection() ?>