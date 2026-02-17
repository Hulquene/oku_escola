<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <a href="<?= site_url('admin/financial/expenses') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/financial/expenses') ?>">Despesas</a></li>
            <li class="breadcrumb-item active" aria-current="page">Resumo</li>
        </ol>
    </nav>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-3">
                <label for="year" class="form-label">Ano</label>
                <select class="form-select" id="year" name="year" onchange="this.form.submit()">
                    <?php if (!empty($years)): ?>
                        <?php foreach ($years as $y): ?>
                            <option value="<?= $y->year ?>" <?= $selectedYear == $y->year ? 'selected' : '' ?>>
                                <?= $y->year ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="month" class="form-label">Mês</label>
                <select class="form-select" id="month" name="month" onchange="this.form.submit()">
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
        </form>
    </div>
</div>

<div class="row">
    <!-- Chart -->
    <div class="col-md-8">
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-chart-line"></i> Evolução Mensal de Despesas
            </div>
            <div class="card-body">
                <canvas id="expensesChart" style="height: 300px;"></canvas>
            </div>
        </div>
    </div>
    
    <!-- Totals -->
    <div class="col-md-4">
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-calculator"></i> Totais
            </div>
            <div class="card-body">
                <?php
                $totalYear = array_sum($chartTotals);
                $averageMonth = $totalYear / 12;
                ?>
                <table class="table table-sm">
                    <tr>
                        <th>Total do Ano:</th>
                        <td class="text-end fw-bold"><?= number_format($totalYear, 2, ',', '.') ?> Kz</td>
                    </tr>
                    <tr>
                        <th>Média Mensal:</th>
                        <td class="text-end"><?= number_format($averageMonth, 2, ',', '.') ?> Kz</td>
                    </tr>
                    <tr>
                        <th>Mês com Maior Despesa:</th>
                        <td class="text-end">
                            <?php
                            $maxMonth = array_search(max($chartTotals), $chartTotals);
                            echo $chartMonths[$maxMonth] ?? '-';
                            ?>
                        </td>
                    </tr>
                    <tr>
                        <th>Mês com Menor Despesa:</th>
                        <td class="text-end">
                            <?php
                            $minMonth = array_search(min($chartTotals), $chartTotals);
                            echo $chartMonths[$minMonth] ?? '-';
                            ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Category Summary -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-chart-pie"></i> Resumo por Categoria
    </div>
    <div class="card-body">
        <?php if (!empty($categorySummary)): ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Categoria</th>
                            <th class="text-center">Quantidade</th>
                            <th class="text-end">Total</th>
                            <th class="text-end">Percentagem</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $grandTotal = array_sum(array_column($categorySummary, 'total_amount'));
                        foreach ($categorySummary as $cat):
                            $percentage = $grandTotal > 0 ? ($cat->total_amount / $grandTotal) * 100 : 0;
                        ?>
                        <tr>
                            <td><?= $cat->category_name ?></td>
                            <td class="text-center"><?= $cat->total_count ?></td>
                            <td class="text-end"><?= number_format($cat->total_amount, 2, ',', '.') ?> Kz</td>
                            <td class="text-end">
                                <div class="progress" style="height: 20px;">
                                    <div class="progress-bar bg-primary" role="progressbar" 
                                         style="width: <?= $percentage ?>%" 
                                         aria-valuenow="<?= $percentage ?>" 
                                         aria-valuemin="0" 
                                         aria-valuemax="100">
                                        <?= number_format($percentage, 1) ?>%
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="2" class="text-end">Total Geral:</th>
                            <th class="text-end"><?= number_format($grandTotal, 2, ',', '.') ?> Kz</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted text-center">Nenhuma despesa encontrada para o período selecionado.</p>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>

<script>
// Expenses Chart
const ctx = document.getElementById('expensesChart').getContext('2d');
new Chart(ctx, {
    type: 'bar',
    data: {
        labels: <?= json_encode($chartMonths) ?>,
        datasets: [{
            label: 'Despesas (Kz)',
            data: <?= json_encode($chartTotals) ?>,
            backgroundColor: '#dc3545',
            borderColor: '#c82333',
            borderWidth: 1
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
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'Despesas: ' + context.raw.toLocaleString('pt-AO') + ' Kz';
                    }
                }
            }
        }
    }
});
</script>
<?= $this->endSection() ?>