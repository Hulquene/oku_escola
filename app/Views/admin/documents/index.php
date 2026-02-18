<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Início</a></li>
            <li class="breadcrumb-item active" aria-current="page">Central de Documentos</li>
        </ol>
    </nav>
</div>

<!-- Estatísticas Gerais -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Total Documentos</h6>
                        <h2 class="mb-0"><?= $totalDocuments ?></h2>
                    </div>
                    <i class="fas fa-file-alt fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Pendentes</h6>
                        <h2 class="mb-0"><?= $pendingDocuments ?></h2>
                    </div>
                    <i class="fas fa-clock fa-3x opacity-50"></i>
                </div>
                <a href="<?= site_url('admin/documents/pending') ?>" class="text-white small">Ver todos <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Verificados</h6>
                        <h2 class="mb-0"><?= $verifiedDocuments ?></h2>
                    </div>
                    <i class="fas fa-check-circle fa-3x opacity-50"></i>
                </div>
                <a href="<?= site_url('admin/documents/verified') ?>" class="text-white small">Ver todos <i class="fas fa-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-danger text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Rejeitados</h6>
                        <h2 class="mb-0"><?= $rejectedDocuments ?></h2>
                    </div>
                    <i class="fas fa-times-circle fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Estatísticas de Solicitações -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Total Solicitações</h6>
                        <h2 class="mb-0"><?= $totalRequests ?></h2>
                    </div>
                    <i class="fas fa-file-signature fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Pendentes</h6>
                        <h2 class="mb-0"><?= $pendingRequests ?></h2>
                    </div>
                    <i class="fas fa-clock fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card border-0 shadow-sm bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Processando</h6>
                        <h2 class="mb-0"><?= $processingRequests ?></h2>
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
                        <h6 class="text-white-50 mb-1">Prontos</h6>
                        <h2 class="mb-0"><?= $readyRequests ?></h2>
                    </div>
                    <i class="fas fa-check fa-3x opacity-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Resumo Financeiro -->
<div class="row g-3 mb-4">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-pie text-primary me-2"></i>
                    Resumo Financeiro
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <div class="text-center p-3 bg-light rounded">
                            <h6 class="text-muted mb-2">Total em Taxas</h6>
                            <h3 class="text-primary mb-0"><?= number_format($totalFees, 2, ',', '.') ?> Kz</h3>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="text-center p-3 bg-light rounded">
                            <h6 class="text-muted mb-2">Valor Arrecadado</h6>
                            <h3 class="text-success mb-0"><?= number_format($collectedFees, 2, ',', '.') ?> Kz</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-line text-primary me-2"></i>
                    Ações Rápidas
                </h5>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-6">
                        <a href="<?= site_url('admin/documents/pending') ?>" class="btn btn-warning w-100">
                            <i class="fas fa-clock me-2"></i> Pendentes
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="<?= site_url('admin/documents/requests') ?>" class="btn btn-info w-100">
                            <i class="fas fa-file-signature me-2"></i> Solicitações
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="<?= site_url('admin/documents/types') ?>" class="btn btn-primary w-100">
                            <i class="fas fa-tags me-2"></i> Tipos
                        </a>
                    </div>
                    <div class="col-6">
                        <a href="<?= site_url('admin/documents/reports') ?>" class="btn btn-secondary w-100">
                            <i class="fas fa-chart-bar me-2"></i> Relatórios
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Gráficos e Tendências -->
<div class="row">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-bar text-primary me-2"></i>
                    Tendências Mensais
                </h5>
                <select class="form-select form-select-sm w-auto" id="yearSelect">
                    <?php for ($y = date('Y'); $y >= date('Y')-3; $y--): ?>
                        <option value="<?= $y ?>" <?= $y == date('Y') ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="card-body">
                <canvas id="monthlyChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">
                    <i class="fas fa-chart-pie text-primary me-2"></i>
                    Distribuição por Tipo
                </h5>
            </div>
            <div class="card-body">
                <canvas id="typeChart" style="height: 250px;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Últimos Documentos -->
<div class="row">
    <div class="col-md-6">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-file-alt text-primary me-2"></i>
                    Últimos Documentos
                </h5>
                <a href="<?= site_url('admin/documents/pending') ?>" class="btn btn-sm btn-outline-primary">Ver todos</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Usuário</th>
                                <th>Tipo</th>
                                <th>Data</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentDocuments as $doc): ?>
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-primary rounded-circle me-2">
                                                <?= strtoupper(substr($doc->user_type, 0, 1)) ?>
                                            </div>
                                            <div>
                                                <small class="d-block fw-semibold"><?= $doc->user_name ?? 'N/A' ?></small>
                                                <small class="text-muted"><?= $doc->user_type ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><?= $doc->document_type ?></td>
                                    <td><?= date('d/m/Y', strtotime($doc->created_at)) ?></td>
                                    <td>
                                        <?php if ($doc->is_verified == 0): ?>
                                            <span class="badge bg-warning">Pendente</span>
                                        <?php elseif ($doc->is_verified == 1): ?>
                                            <span class="badge bg-success">Verificado</span>
                                        <?php else: ?>
                                            <span class="badge bg-danger">Rejeitado</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <a href="<?= site_url('admin/documents/verify/' . $doc->id) ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white d-flex justify-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-file-signature text-primary me-2"></i>
                    Últimas Solicitações
                </h5>
                <a href="<?= site_url('admin/documents/requests') ?>" class="btn btn-sm btn-outline-primary">Ver todos</a>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Nº</th>
                                <th>Usuário</th>
                                <th>Tipo</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($recentRequests as $req): ?>
                                <tr>
                                    <td><small><?= $req->request_number ?></small></td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            <div class="avatar avatar-sm bg-info rounded-circle me-2">
                                                <?= strtoupper(substr($req->user_type, 0, 1)) ?>
                                            </div>
                                            <div>
                                                <small class="d-block fw-semibold"><?= $req->user_name ?? 'N/A' ?></small>
                                                <small class="text-muted"><?= $req->user_type ?></small>
                                            </div>
                                        </div>
                                    </td>
                                    <td><small><?= $req->document_type ?></small></td>
                                    <td>
                                        <?php
                                        $statusLabels = [
                                            'pending' => ['warning', 'Pendente'],
                                            'processing' => ['info', 'Processando'],
                                            'ready' => ['success', 'Pronto'],
                                            'delivered' => ['secondary', 'Entregue'],
                                            'cancelled' => ['danger', 'Cancelado'],
                                            'rejected' => ['danger', 'Rejeitado']
                                        ];
                                        $status = $statusLabels[$req->status] ?? ['secondary', $req->status];
                                        ?>
                                        <span class="badge bg-<?= $status[0] ?>"><?= $status[1] ?></span>
                                    </td>
                                    <td>
                                        <a href="<?= site_url('admin/documents/request/view/' . $req->id) ?>" class="btn btn-sm btn-outline-primary">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Gráfico de Tendências Mensais
const ctx1 = document.getElementById('monthlyChart').getContext('2d');
new Chart(ctx1, {
    type: 'line',
    data: {
        labels: <?= json_encode(array_column($monthlyStats, 'month')) ?>,
        datasets: [{
            label: 'Documentos',
            data: <?= json_encode(array_column($monthlyStats, 'total')) ?>,
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.2)',
            tension: 0.1
        }, {
            label: 'Verificados',
            data: <?= json_encode(array_column($monthlyStats, 'verified')) ?>,
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
        labels: <?= json_encode(array_column($typeStats, 'document_type')) ?>,
        datasets: [{
            data: <?= json_encode(array_column($typeStats, 'total')) ?>,
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

<style>
.avatar {
    width: 30px;
    height: 30px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.8rem;
}
</style>

<?= $this->endSection() ?>