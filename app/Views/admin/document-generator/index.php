<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<div class="page-header d-flex justify-content-between align-items-center">
    <h1><?= $title ?></h1>
    <div>
        <a href="<?= site_url('admin/document-generator/pending') ?>" class="btn btn-warning">
            <i class="fas fa-clock me-2"></i> Pendentes (<?= $totalPending ?>)
        </a>
    </div>
</div>

<!-- Cards de Estatísticas -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Pendentes</h6>
                        <h2 class="mb-0"><?= $totalPending ?></h2>
                    </div>
                    <i class="fas fa-clock fa-3x opacity-50"></i>
                </div>
                <a href="<?= site_url('admin/document-generator/pending') ?>" class="text-white small">Processar <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Processando</h6>
                        <h2 class="mb-0"><?= $totalProcessing ?></h2>
                    </div>
                    <i class="fas fa-cog fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Gerados</h6>
                        <h2 class="mb-0"><?= $totalGenerated ?></h2>
                    </div>
                    <i class="fas fa-file-pdf fa-3x opacity-50"></i>
                </div>
                <a href="<?= site_url('admin/document-generator/generated') ?>" class="text-white small">Ver todos <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-secondary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Entregues</h6>
                        <h2 class="mb-0"><?= $totalDelivered ?></h2>
                    </div>
                    <i class="fas fa-check-double fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gráfico de Tendências -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-line text-primary me-2"></i>
                    Tendências de Solicitações
                </h5>
            </div>
            <div class="card-body">
                <canvas id="trendsChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Últimas Solicitações -->
<div class="row">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-list text-primary me-2"></i>
                    Últimas Solicitações
                </h5>
                <a href="<?= site_url('admin/document-generator/pending') ?>" class="btn btn-sm btn-outline-primary">Ver todas</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nº</th>
                                <th>Solicitante</th>
                                <th>Documento</th>
                                <th>Data</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentRequests as $req): ?>
                                <tr>
                                    <td><small><?= $req->request_number ?></small></td>
                                    <td>
                                        <?php 
                                        // Usar helper para obter nome do usuário
                                        $userName = getUserName($req->user_id);
                                        echo $userName ?: 'N/A';
                                        ?>
                                        <br>
                                        <small class="text-muted"><?= $req->user_type == 'student' ? 'Aluno' : 'Professor' ?></small>
                                    </td>
                                    <td><?= $req->document_type ?></td>
                                    <td><?= date('d/m/Y', strtotime($req->created_at)) ?></td>
                                    <td>
                                        <?php
                                        $statusLabels = [
                                            'pending' => ['warning', 'Pendente'],
                                            'processing' => ['info', 'Processando'],
                                            'ready' => ['success', 'Pronto'],
                                            'delivered' => ['secondary', 'Entregue']
                                        ];
                                        $status = $statusLabels[$req->status] ?? ['secondary', $req->status];
                                        ?>
                                        <span class="badge bg-<?= $status[0] ?>"><?= $status[1] ?></span>
                                    </td>
                                    <td>
                                        <?php if ($req->status == 'pending'): ?>
                                            <a href="<?= site_url('admin/document-generator/generate/' . $req->id) ?>" 
                                               class="btn btn-sm btn-success" title="Gerar">
                                                <i class="fas fa-file-pdf"></i>
                                            </a>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-pie text-primary me-2"></i>
                    Documentos por Tipo
                </h5>
            </div>
            <div class="card-body">
                <canvas id="typeChart" style="height: 250px;"></canvas>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Gráfico de Tendências Mensais
const ctx1 = document.getElementById('trendsChart').getContext('2d');
new Chart(ctx1, {
    type: 'line',
    data: {
        labels: <?= json_encode(array_column($monthlyStats, 'month')) ?>,
        datasets: [{
            label: 'Solicitações',
            data: <?= json_encode(array_column($monthlyStats, 'total')) ?>,
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }, {
            label: 'Concluídas',
            data: <?= json_encode(array_column($monthlyStats, 'completed')) ?>,
            borderColor: 'rgb(40, 167, 69)',
            backgroundColor: 'rgba(40, 167, 69, 0.2)',
            tension: 0.1
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

// Gráfico de Distribuição por Tipo
const ctx2 = document.getElementById('typeChart').getContext('2d');
new Chart(ctx2, {
    type: 'doughnut',
    data: {
        labels: <?= json_encode(array_column($documentsByType, 'document_type')) ?>,
        datasets: [{
            data: <?= json_encode(array_column($documentsByType, 'total')) ?>,
            backgroundColor: [
                'rgb(255, 99, 132)',
                'rgb(54, 162, 235)',
                'rgb(255, 205, 86)',
                'rgb(75, 192, 192)',
                'rgb(153, 102, 255)'
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
</script>

<?= $this->endSection() ?>