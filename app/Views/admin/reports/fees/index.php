<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/reports') ?>">Relatórios</a></li>
            <li class="breadcrumb-item active" aria-current="page">Propinas</li>
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
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Todos</option>
                    <option value="Pago" <?= $selectedStatus == 'Pago' ? 'selected' : '' ?>>Pago</option>
                    <option value="Pendente" <?= $selectedStatus == 'Pendente' ? 'selected' : '' ?>>Pendente</option>
                    <option value="Vencido" <?= $selectedStatus == 'Vencido' ? 'selected' : '' ?>>Vencido</option>
                </select>
            </div>
            
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                <a href="<?= site_url('admin/reports/fees') ?>" class="btn btn-secondary ms-2">
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
    $totalAmount = array_sum(array_column($summary, 'total_amount'));
    $totalPaid = array_sum(array_column($summary, 'paid_amount'));
    $totalPending = array_sum(array_column($summary, 'pending_amount'));
    $totalOverdue = array_sum(array_column($summary, 'overdue_count'));
    ?>
    
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6 class="card-title">Total de Propinas</h6>
                <h3><?= number_format($totalAmount, 2, ',', '.') ?> Kz</h3>
                <small><?= array_sum(array_column($summary, 'total_fees')) ?> registos</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="card-title">Total Pago</h6>
                <h3><?= number_format($totalPaid, 2, ',', '.') ?> Kz</h3>
                <small><?= $totalAmount > 0 ? round(($totalPaid / $totalAmount) * 100, 1) : 0 ?>% do total</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <h6 class="card-title">Total Pendente</h6>
                <h3><?= number_format($totalPending, 2, ',', '.') ?> Kz</h3>
                <small>Aguardando pagamento</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h6 class="card-title">Propinas Vencidas</h6>
                <h3><?= $totalOverdue ?></h3>
                <small>Registos em atraso</small>
            </div>
        </div>
    </div>
</div>

<!-- Monthly Collection Chart -->
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-chart-line"></i> Cobranças Mensais
    </div>
    <div class="card-body">
        <canvas id="monthlyCollectionChart" style="height: 300px;"></canvas>
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
                        <th class="text-center">Total Propinas</th>
                        <th class="text-center">Total Alunos</th>
                        <th class="text-end">Valor Total</th>
                        <th class="text-end">Valor Pago</th>
                        <th class="text-end">Valor Pendente</th>
                        <th class="text-center">Pagos</th>
                        <th class="text-center">Pendentes</th>
                        <th class="text-center">Vencidos</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($summary)): ?>
                        <?php foreach ($summary as $row): ?>
                            <?php 
                            $paidPercentage = $row->total_amount > 0 ? round(($row->paid_amount / $row->total_amount) * 100, 1) : 0;
                            ?>
                            <tr>
                                <td><?= $row->class_name ?></td>
                                <td class="text-center"><?= $row->total_fees ?></td>
                                <td class="text-center"><?= $row->paid_count + $row->pending_count + $row->overdue_count ?></td>
                                <td class="text-end"><?= number_format($row->total_amount, 2, ',', '.') ?> Kz</td>
                                <td class="text-end text-success"><?= number_format($row->paid_amount, 2, ',', '.') ?> Kz</td>
                                <td class="text-end text-warning"><?= number_format($row->pending_amount, 2, ',', '.') ?> Kz</td>
                                <td class="text-center">
                                    <span class="badge bg-success"><?= $row->paid_count ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-warning"><?= $row->pending_count ?></span>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-danger"><?= $row->overdue_count ?></span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center">Nenhum dado encontrado para o período</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th>Totais</th>
                        <th class="text-center"><?= array_sum(array_column($summary, 'total_fees')) ?></th>
                        <th class="text-center">-</th>
                        <th class="text-end"><?= number_format($totalAmount, 2, ',', '.') ?> Kz</th>
                        <th class="text-end"><?= number_format($totalPaid, 2, ',', '.') ?> Kz</th>
                        <th class="text-end"><?= number_format($totalPending, 2, ',', '.') ?> Kz</th>
                        <th class="text-center"><?= array_sum(array_column($summary, 'paid_count')) ?></th>
                        <th class="text-center"><?= array_sum(array_column($summary, 'pending_count')) ?></th>
                        <th class="text-center"><?= $totalOverdue ?></th>
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
// Monthly Collection Chart
const ctx = document.getElementById('monthlyCollectionChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($chartMonths) ?>,
        datasets: [{
            label: 'Cobranças (Kz)',
            data: <?= json_encode($chartCollections) ?>,
            backgroundColor: '#28a745'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return value.toLocaleString('pt-AO') + ' Kz';
                    }
                }
            }
        }
    }
});
</script>
<?= $this->endSection() ?>