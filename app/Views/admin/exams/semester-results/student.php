<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2">Resultado Semestral</h1>
            <p class="text-muted mb-0">
                <i class="fas fa-graduation-cap me-1"></i>
                <?= $enrollment->first_name ?> <?= $enrollment->last_name ?> • <?= $enrollment->class_name ?>
            </p>
        </div>
        <div>
            <a href="<?= site_url('admin/discipline-averages/student/' . $enrollment->id . '/' . $semester->id) ?>" class="btn btn-outline-info me-2">
                <i class="fas fa-chart-line me-1"></i> Ver Médias
            </a>
            <a href="<?= site_url('admin/students/view/' . $enrollment->student_id) ?>" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/students') ?>">Alunos</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/students/view/' . $enrollment->student_id) ?>"><?= $enrollment->first_name ?> <?= $enrollment->last_name ?></a></li>
            <li class="breadcrumb-item active">Resultado - <?= $semester->semester_name ?></li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<?php if ($result): ?>
    <?php
    $statusColors = [
        'Aprovado' => 'success',
        'Recurso' => 'warning',
        'Reprovado' => 'danger',
        'Em Andamento' => 'secondary'
    ];
    $statusColor = $statusColors[$result->status] ?? 'secondary';
    ?>

    <!-- Cards de Resultado -->
    <div class="row g-3 mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 p-3 rounded">
                                <i class="fas fa-calculator fa-2x text-primary"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Média Geral</h6>
                            <h2 class="mb-0 <?= $result->overall_average >= 10 ? 'text-success' : 'text-warning' ?>">
                                <?= number_format($result->overall_average, 2) ?>
                            </h2>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-info bg-opacity-10 p-3 rounded">
                                <i class="fas fa-book-open fa-2x text-info"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Disciplinas</h6>
                            <h2 class="mb-0"><?= $result->total_disciplines ?></h2>
                            <small>
                                Aprov: <?= $result->approved_disciplines ?> | 
                                Rec: <?= $result->appeal_disciplines ?> | 
                                Rep: <?= $result->failed_disciplines ?>
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-<?= $statusColor ?> bg-opacity-10 p-3 rounded">
                                <i class="fas fa-flag-checkered fa-2x text-<?= $statusColor ?>"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">Resultado Final</h6>
                            <h3 class="mb-0">
                                <span class="badge bg-<?= $statusColor ?> p-3">
                                    <?= $result->status ?>
                                </span>
                            </h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Detalhamento por Disciplina -->
    <div class="card mb-4">
        <div class="card-header bg-white py-3">
            <h5 class="mb-0 fw-semibold">
                <i class="fas fa-table me-2 text-primary"></i>
                Desempenho por Disciplina
            </h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0 py-3 ps-3" width="50">#</th>
                            <th class="border-0 py-3">Disciplina</th>
                            <th class="border-0 py-3 text-center">Código</th>
                            <th class="border-0 py-3 text-center">Média AC</th>
                            <th class="border-0 py-3 text-center">Exame</th>
                            <th class="border-0 py-3 text-center">Nota Final</th>
                            <th class="border-0 py-3 text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php $i = 1; ?>
                        <?php foreach ($disciplineAverages as $avg): ?>
                            <?php
                            $discStatusColor = [
                                'Aprovado' => 'success',
                                'Recurso' => 'warning',
                                'Reprovado' => 'danger'
                            ][$avg->status] ?? 'secondary';
                            ?>
                            <tr>
                                <td class="ps-3"><?= $i++ ?></td>
                                <td class="fw-semibold"><?= $avg->discipline_name ?></td>
                                <td class="text-center">
                                    <span class="badge bg-secondary"><?= $avg->discipline_code ?></span>
                                </td>
                                <td class="text-center">
                                    <?php if ($avg->ac_score > 0): ?>
                                        <span class="badge bg-primary bg-opacity-10 text-primary p-2">
                                            <?= number_format($avg->ac_score, 1) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <?php if ($avg->exam_score > 0): ?>
                                        <span class="badge bg-info bg-opacity-10 text-info p-2">
                                            <?= number_format($avg->exam_score, 1) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="fw-bold fs-5 <?= $avg->final_score >= 10 ? 'text-success' : 'text-warning' ?>">
                                        <?= number_format($avg->final_score, 1) ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-<?= $discStatusColor ?> p-2">
                                        <?= $avg->status ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Gráficos -->
    <div class="row">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-chart-pie me-2 text-success"></i>
                        Distribuição das Disciplinas
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="disciplinesChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0 fw-semibold">
                        <i class="fas fa-chart-bar me-2 text-info"></i>
                        Comparativo de Notas
                    </h5>
                </div>
                <div class="card-body">
                    <canvas id="gradesChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

<?php else: ?>
    <div class="empty-state">
        <i class="fas fa-graduation-cap empty-state-icon"></i>
        <h5 class="empty-state-title">Resultado não disponível</h5>
        <p class="empty-state-text">
            Ainda não existem notas suficientes para calcular o resultado deste semestre.
        </p>
        <button class="btn btn-primary" onclick="calculateResult()">
            <i class="fas fa-calculator me-2"></i>Calcular Resultado
        </button>
    </div>
<?php endif; ?>

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<style>
.card {
    border: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.05);
}

.table th {
    font-weight: 600;
    font-size: 0.85rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    color: #6c757d;
}

.table td {
    vertical-align: middle;
}

.badge {
    font-weight: 500;
    padding: 0.5em 0.8em;
}

.empty-state {
    text-align: center;
    padding: 4rem 2rem;
    background: #fff;
    border-radius: 8px;
    border: 1px solid #e9ecef;
}

.empty-state-icon {
    font-size: 4rem;
    color: #dee2e6;
    margin-bottom: 1rem;
}

.empty-state-title {
    color: #495057;
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.empty-state-text {
    color: #6c757d;
    margin-bottom: 1.5rem;
}
</style>

<script>
<?php if ($result): ?>
// Gráfico de Distribuição
const ctx1 = document.getElementById('disciplinesChart').getContext('2d');
new Chart(ctx1, {
    type: 'pie',
    data: {
        labels: ['Aprovadas', 'Recurso', 'Reprovadas'],
        datasets: [{
            data: [
                <?= $result->approved_disciplines ?>,
                <?= $result->appeal_disciplines ?>,
                <?= $result->failed_disciplines ?>
            ],
            backgroundColor: ['#198754', '#ffc107', '#dc3545'],
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

// Gráfico de Comparativo
const ctx2 = document.getElementById('gradesChart').getContext('2d');
new Chart(ctx2, {
    type: 'bar',
    data: {
        labels: [<?php foreach ($disciplineAverages as $avg): ?>'<?= $avg->discipline_code ?>', <?php endforeach; ?>],
        datasets: [{
            label: 'Nota Final',
            data: [<?php foreach ($disciplineAverages as $avg): ?><?= $avg->final_score ?>, <?php endforeach; ?>],
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
                max: 20,
                grid: {
                    display: true
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
<?php endif; ?>

function calculateResult() {
    $.ajax({
        url: '<?= site_url('admin/semester-results/calculate/' . $enrollment->id . '/' . $semester->id) ?>',
        method: 'POST',
        data: {
            <?= csrf_token() ?>: '<?= csrf_hash() ?>'
        },
        success: function(response) {
            if (response.success) {
                toastr.success('Resultado calculado com sucesso!');
                setTimeout(() => {
                    location.reload();
                }, 1500);
            } else {
                toastr.error(response.message);
            }
        }
    });
}
</script>

<?= $this->endSection() ?>