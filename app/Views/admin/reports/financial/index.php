<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/reports') ?>">Relatórios</a></li>
            <li class="breadcrumb-item active" aria-current="page">Financeiro</li>
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
                <label for="year" class="form-label">Ano</label>
                <select class="form-select" id="year" name="year" onchange="this.form.submit()">
                    <?php foreach ($years as $y): ?>
                        <option value="<?= $y ?>" <?= $selectedYear == $y ? 'selected' : '' ?>>
                            <?= $y ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="month" class="form-label">Mês</label>
                <select class="form-select" id="month" name="month">
                    <option value="">Todos</option>
                    <option value="1" <?= $selectedMonth == 1 ? 'selected' : '' ?>>Janeiro</option>
                    <option value="2" <?= $selectedMonth == 2 ? 'selected' : '' ?>>Fevereiro</option>
                    <option value="3" <?= $selectedMonth == 3 ? 'selected' : '' ?>>Março</option>
                    <option value="4" <?= $selectedMonth == 4 ? 'selected' : '' ?>>Abril</option>
                    <option value="5" <?= $selectedMonth == 5 ? 'selected' : '' ?>>Maio</option>
                    <option value="6" <?= $selectedMonth == 6 ? 'selected' : '' ?>>Junho</option>
                    <option value="7" <?= $selectedMonth == 7 ? 'selected' : '' ?>>Julho</option>
                    <option value="8" <?= $selectedMonth == 8 ? 'selected' : '' ?>>Agosto</option>
                    <option value="9" <?= $selectedMonth == 9 ? 'selected' : '' ?>>Setembro</option>
                    <option value="10" <?= $selectedMonth == 10 ? 'selected' : '' ?>>Outubro</option>
                    <option value="11" <?= $selectedMonth == 11 ? 'selected' : '' ?>>Novembro</option>
                    <option value="12" <?= $selectedMonth == 12 ? 'selected' : '' ?>>Dezembro</option>
                </select>
            </div>
            
            <div class="col-md-6 d-flex align-items-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                <a href="<?= site_url('admin/reports/financial') ?>" class="btn btn-secondary ms-2">
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
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6 class="card-title">Total Faturado</h6>
                <h3><?= number_format($invoiceStats->total_paid + $invoiceStats->total_pending, 0, ',', '.') ?> Kz</h3>
                <small><?= $invoiceStats->total_invoices ?? 0 ?> faturas</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="card-title">Total Recebido</h6>
                <h3><?= number_format($invoiceStats->total_paid ?? 0, 0, ',', '.') ?> Kz</h3>
               <small>
                  <?php 
                  $total = ($invoiceStats->total_paid ?? 0) + ($invoiceStats->total_pending ?? 0);
                  $percentage = $total > 0 ? round(($invoiceStats->total_paid / $total) * 100, 1) : 0;
                  echo $percentage; ?>% do total
              </small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <h6 class="card-title">Total Pendente</h6>
                <h3><?= number_format($invoiceStats->total_pending ?? 0, 0, ',', '.') ?> Kz</h3>
                <small>Valor a receber</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-danger text-white">
            <div class="card-body">
                <h6 class="card-title">Total Despesas</h6>
                <h3><?= number_format($expenseStats->total_amount ?? 0, 0, ',', '.') ?> Kz</h3>
                <small><?= $expenseStats->total_count ?? 0 ?> despesas</small>
            </div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="row mb-4">
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-chart-line"></i> Faturamento Mensal
            </div>
            <div class="card-body">
                <canvas id="monthlyChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <i class="fas fa-chart-pie"></i> Métodos de Pagamento
            </div>
            <div class="card-body">
                <canvas id="paymentMethodsChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
</div>

<!-- Expenses by Category -->
<div class="card mb-4">
    <div class="card-header">
        <i class="fas fa-chart-bar"></i> Despesas por Categoria
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>Categoria</th>
                        <th class="text-end">Valor</th>
                        <th class="text-end">Percentagem</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $totalExpenses = array_sum(array_column($expensesByCategory, 'total'));
                    foreach ($expensesByCategory as $expense): 
                        $percentage = $totalExpenses > 0 ? ($expense->total / $totalExpenses) * 100 : 0;
                    ?>
                    <tr>
                        <td><?= $expense->category_name ?></td>
                        <td class="text-end"><?= number_format($expense->total, 2, ',', '.') ?> Kz</td>
                        <td class="text-end"><?= number_format($percentage, 1) ?>%</td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th>Total</th>
                        <th class="text-end"><?= number_format($totalExpenses, 2, ',', '.') ?> Kz</th>
                        <th class="text-end">100%</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Profit/Loss Summary -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-balance-scale"></i> Resumo de Resultados
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-4">
                <div class="alert alert-success">
                    <h5>Receitas</h5>
                    <h3><?= number_format($invoiceStats->total_paid ?? 0, 2, ',', '.') ?> Kz</h3>
                </div>
            </div>
            <div class="col-md-4">
                <div class="alert alert-danger">
                    <h5>Despesas</h5>
                    <h3><?= number_format($expenseStats->total_amount ?? 0, 2, ',', '.') ?> Kz</h3>
                </div>
            </div>
            <div class="col-md-4">
                <?php 
                $balance = ($invoiceStats->total_paid ?? 0) - ($expenseStats->total_amount ?? 0);
                $balanceClass = $balance >= 0 ? 'success' : 'danger';
                ?>
                <div class="alert alert-<?= $balanceClass ?>">
                    <h5>Saldo</h5>
                    <h3><?= number_format($balance, 2, ',', '.') ?> Kz</h3>
                </div>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Monthly Chart
const monthlyCtx = document.getElementById('monthlyChart').getContext('2d');
new Chart(monthlyCtx, {
    type: 'bar',
    data: {
        labels: <?= json_encode(array_column($monthlyInvoices, 'month')) ?>,
        datasets: [{
            label: 'Faturamento (Kz)',
            data: <?= json_encode(array_column($monthlyInvoices, 'total')) ?>,
            backgroundColor: '#4e73df'
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

// Payment Methods Chart
const methodsCtx = document.getElementById('paymentMethodsChart').getContext('2d');
new Chart(methodsCtx, {
    type: 'pie',
    data: {
        labels: <?= json_encode(array_column($paymentMethods, 'payment_method')) ?>,
        datasets: [{
            data: <?= json_encode(array_column($paymentMethods, 'total')) ?>,
            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc', '#f6c23e', '#e74a3b']
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        let label = context.label || '';
                        let value = context.raw || 0;
                        let total = context.dataset.data.reduce((a, b) => a + b, 0);
                        let percentage = Math.round((value / total) * 100);
                        return `${label}: ${value.toLocaleString('pt-AO')} Kz (${percentage}%)`;
                    }
                }
            }
        }
    }
});
</script>
<?= $this->endSection() ?>