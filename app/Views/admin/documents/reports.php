<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<div class="page-header d-flex justify-content-between align-items-center">
    <h1><?= $title ?></h1>
    <div>
        <a href="<?= site_url('admin/documents/export?type=documents&format=csv&year=' . ($selectedYear ?? date('Y'))) ?>" 
           class="btn btn-success me-2">
            <i class="fas fa-file-csv me-2"></i> Exportar CSV
        </a>
        <a href="<?= site_url('admin/documents/export?type=documents&format=excel&year=' . ($selectedYear ?? date('Y'))) ?>" 
           class="btn btn-success">
            <i class="fas fa-file-excel me-2"></i> Exportar Excel
        </a>
    </div>
</div>

<!-- Filtro de Ano -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-2">
                <label for="year" class="form-label">Ano</label>
                <select class="form-select" id="year" name="year" onchange="this.form.submit()">
                    <?php for ($y = date('Y'); $y >= date('Y')-3; $y--): ?>
                        <option value="<?= $y ?>" <?= $y == ($selectedYear ?? date('Y')) ? 'selected' : '' ?>>
                            <?= $y ?>
                        </option>
                    <?php endfor; ?>
                </select>
            </div>
        </form>
    </div>
</div>

<!-- Estatísticas Gerais -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body">
                <h6 class="text-white-50 mb-2">Total Documentos</h6>
                <h2 class="mb-0"><?= array_sum(array_column($monthlyStats, 'total')) ?></h2>
                <small><?= $selectedYear ?></small>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body">
                <h6 class="text-white-50 mb-2">Documentos Verificados</h6>
                <h2 class="mb-0"><?= array_sum(array_column($monthlyStats, 'verified')) ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-warning text-white">
            <div class="card-body">
                <h6 class="text-white-50 mb-2">Documentos Pendentes</h6>
                <h2 class="mb-0"><?= array_sum(array_column($monthlyStats, 'pending')) ?></h2>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-info text-white">
            <div class="card-body">
                <h6 class="text-white-50 mb-2">Total Solicitações</h6>
                <h2 class="mb-0"><?= array_sum(array_column($requestTrends, 'total')) ?></h2>
            </div>
        </div>
    </div>
</div>

<!-- Gráfico de Tendências Mensais -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-line text-primary me-2"></i>
                    Tendências Mensais - <?= $selectedYear ?>
                </h5>
            </div>
            <div class="card-body">
                <canvas id="monthlyTrendsChart" style="height: 400px;"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row">
    <!-- Distribuição por Tipo -->
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-pie text-primary me-2"></i>
                    Documentos por Tipo
                </h5>
            </div>
            <div class="card-body">
                <canvas id="typeDistributionChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Distribuição por Usuário -->
    <div class="col-md-6 mb-4">
        <div class="card border-0 shadow-sm h-100">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-users text-primary me-2"></i>
                    Documentos por Tipo de Usuário
                </h5>
            </div>
            <div class="card-body">
                <canvas id="userDistributionChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Tabela de Estatísticas Mensais -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white">
        <h5 class="card-title mb-0">
            <i class="fas fa-table text-primary me-2"></i>
            Estatísticas Mensais Detalhadas
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>Mês</th>
                        <th class="text-center">Total Documentos</th>
                        <th class="text-center">Verificados</th>
                        <th class="text-center">Pendentes</th>
                        <th class="text-center">Alunos</th>
                        <th class="text-center">Professores</th>
                        <th class="text-center">Solicitações</th>
                        <th class="text-center">Concluídas</th>
                        <th class="text-end">Taxas (Kz)</th>
                        <th class="text-end">Arrecadado (Kz)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $months = [
                        '01' => 'Janeiro', '02' => 'Fevereiro', '03' => 'Março',
                        '04' => 'Abril', '05' => 'Maio', '06' => 'Junho',
                        '07' => 'Julho', '08' => 'Agosto', '09' => 'Setembro',
                        '10' => 'Outubro', '11' => 'Novembro', '12' => 'Dezembro'
                    ];
                    
                    foreach ($monthlyStats as $stat):
                        $monthNum = substr($stat->month, 5, 2);
                        $monthName = $months[$monthNum] ?? $stat->month;
                        
                        // Encontrar dados correspondentes de solicitações
                        $requestData = null;
                        foreach ($requestTrends as $rt) {
                            if ($rt->month == $stat->month) {
                                $requestData = $rt;
                                break;
                            }
                        }
                    ?>
                        <tr>
                            <td><strong><?= $monthName ?> <?= $selectedYear ?></strong></td>
                            <td class="text-center"><?= $stat->total ?></td>
                            <td class="text-center"><?= $stat->verified ?></td>
                            <td class="text-center"><?= $stat->pending ?></td>
                            <td class="text-center"><?= $stat->from_students ?></td>
                            <td class="text-center"><?= $stat->from_teachers ?></td>
                            <td class="text-center"><?= $requestData->total ?? 0 ?></td>
                            <td class="text-center"><?= $requestData->completed ?? 0 ?></td>
                            <td class="text-end"><?= number_format($requestData->total_fees ?? 0, 2, ',', '.') ?></td>
                            <td class="text-end"><?= number_format($requestData->collected_fees ?? 0, 2, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th><strong>Total</strong></th>
                        <th class="text-center"><?= array_sum(array_column($monthlyStats, 'total')) ?></th>
                        <th class="text-center"><?= array_sum(array_column($monthlyStats, 'verified')) ?></th>
                        <th class="text-center"><?= array_sum(array_column($monthlyStats, 'pending')) ?></th>
                        <th class="text-center"><?= array_sum(array_column($monthlyStats, 'from_students')) ?></th>
                        <th class="text-center"><?= array_sum(array_column($monthlyStats, 'from_teachers')) ?></th>
                        <th class="text-center"><?= array_sum(array_column($requestTrends, 'total')) ?></th>
                        <th class="text-center"><?= array_sum(array_column($requestTrends, 'completed')) ?></th>
                        <th class="text-end"><?= number_format(array_sum(array_column($requestTrends, 'total_fees')), 2, ',', '.') ?></th>
                        <th class="text-end"><?= number_format(array_sum(array_column($requestTrends, 'collected_fees')), 2, ',', '.') ?></th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Top Solicitantes -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white">
        <h5 class="card-title mb-0">
            <i class="fas fa-trophy text-primary me-2"></i>
            Top Solicitantes - <?= $selectedYear ?>
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-hover">
                <thead class="table-light">
                    <tr>
                        <th>#</th>
                        <th>Usuário</th>
                        <th>Tipo</th>
                        <th class="text-center">Total Solicitações</th>
                        <th class="text-end">Total Taxas (Kz)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($topRequesters as $index => $requester): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td>
                                <span class="fw-semibold"><?= $requester->user_name ?? 'N/A' ?></span>
                                <br>
                                <small class="text-muted"><?= $requester->email ?? '' ?></small>
                            </td>
                            <td>
                                <?php if ($requester->user_type == 'student'): ?>
                                    <span class="badge bg-primary">Aluno</span>
                                <?php else: ?>
                                    <span class="badge bg-success">Professor</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center"><?= $requester->total ?></td>
                            <td class="text-end"><?= number_format($requester->total_fees ?? 0, 2, ',', '.') ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Gráfico de Tendências Mensais
const ctx1 = document.getElementById('monthlyTrendsChart').getContext('2d');
new Chart(ctx1, {
    type: 'line',
    data: {
        labels: <?= json_encode(array_column($monthlyStats, 'month')) ?>,
        datasets: [
            {
                label: 'Total Documentos',
                data: <?= json_encode(array_column($monthlyStats, 'total')) ?>,
                borderColor: 'rgb(54, 162, 235)',
                backgroundColor: 'rgba(54, 162, 235, 0.1)',
                tension: 0.1,
                fill: true
            },
            {
                label: 'Verificados',
                data: <?= json_encode(array_column($monthlyStats, 'verified')) ?>,
                borderColor: 'rgb(40, 167, 69)',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.1,
                fill: true
            },
            {
                label: 'Solicitações',
                data: <?= json_encode(array_column($requestTrends, 'total')) ?>,
                borderColor: 'rgb(255, 193, 7)',
                backgroundColor: 'rgba(255, 193, 7, 0.1)',
                tension: 0.1,
                fill: true
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 5
                }
            }
        }
    }
});

// Gráfico de Distribuição por Tipo
const ctx2 = document.getElementById('typeDistributionChart').getContext('2d');
new Chart(ctx2, {
    type: 'pie',
    data: {
        labels: <?= json_encode(array_column($typeStats, 'document_type')) ?>,
        datasets: [{
            data: <?= json_encode(array_column($typeStats, 'total')) ?>,
            backgroundColor: [
                'rgb(255, 99, 132)',
                'rgb(54, 162, 235)',
                'rgb(255, 205, 86)',
                'rgb(75, 192, 192)',
                'rgb(153, 102, 255)',
                'rgb(255, 159, 64)'
            ]
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

// Gráfico de Distribuição por Usuário
const ctx3 = document.getElementById('userDistributionChart').getContext('2d');
new Chart(ctx3, {
    type: 'doughnut',
    data: {
        labels: ['Alunos', 'Professores'],
        datasets: [{
            data: [
                <?= array_sum(array_column($monthlyStats, 'from_students')) ?>,
                <?= array_sum(array_column($monthlyStats, 'from_teachers')) ?>
            ],
            backgroundColor: ['rgb(54, 162, 235)', 'rgb(40, 167, 69)']
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
</script>

<?= $this->endSection() ?>