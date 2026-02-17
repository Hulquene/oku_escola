<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <div>
            <a href="<?= site_url('admin/financial/payments/modes') ?>" class="btn btn-info me-2">
                <i class="fas fa-credit-card"></i> Métodos de Pagamento
            </a>
            <a href="<?= site_url('admin/financial/payments/receipts') ?>" class="btn btn-success">
                <i class="fas fa-receipt"></i> Recibos
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/financial') ?>">Financeiro</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pagamentos</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Summary Cards -->
<div class="row mb-4">
    <?php
    $totalAmount = 0;
    $totalPayments = count($payments);
    foreach ($payments as $payment) {
        $totalAmount += $payment->amount;
    }
    ?>
    
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6 class="card-title">Total de Pagamentos</h6>
                <h3><?= number_format($totalAmount, 2, ',', '.') ?> Kz</h3>
                <small><?= $totalPayments ?> transações</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="card-title">Média por Pagamento</h6>
                <h3><?= $totalPayments > 0 ? number_format($totalAmount / $totalPayments, 2, ',', '.') : 0 ?> Kz</h3>
                <small>Valor médio</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <h6 class="card-title">Pagamentos Hoje</h6>
                <h3>
                    <?php
                    $today = array_filter($payments, function($p) {
                        return date('Y-m-d', strtotime($p->payment_date)) == date('Y-m-d');
                    });
                    echo count($today);
                    ?>
                </h3>
                <small><?= number_format(array_sum(array_column($today, 'amount')), 2, ',', '.') ?> Kz</small>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-3">
                <label for="method" class="form-label">Método de Pagamento</label>
                <select class="form-select" id="method" name="method">
                    <option value="">Todos</option>
                    <?php if (!empty($paymentMethods)): ?>
                        <?php foreach ($paymentMethods as $method): ?>
                            <option value="<?= $method->mode_code ?>" <?= $selectedMethod == $method->mode_code ? 'selected' : '' ?>>
                                <?= $method->mode_name ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="start_date" class="form-label">Data Início</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="<?= $selectedStartDate ?>">
            </div>
            
            <div class="col-md-3">
                <label for="end_date" class="form-label">Data Fim</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="<?= $selectedEndDate ?>">
            </div>
            
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                <a href="<?= site_url('admin/financial/payments') ?>" class="btn btn-secondary">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Payments Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-money-bill-wave"></i> Lista de Pagamentos
    </div>
    <div class="card-body">
        <table id="paymentsTable" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Data</th>
                    <th>Fatura</th>
                    <th>Cliente</th>
                    <th>Valor</th>
                    <th>Método</th>
                    <th>Referência</th>
                    <th>Recibo</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($payments)): ?>
                    <?php foreach ($payments as $payment): ?>
                        <tr>
                            <td><?= $payment->id ?></td>
                            <td><?= date('d/m/Y', strtotime($payment->payment_date)) ?></td>
                            <td>
                                <a href="<?= site_url('admin/financial/invoices/view/' . $payment->invoice_id) ?>">
                                    <?= $payment->invoice_number ?>
                                </a>
                            </td>
                            <td><?= $payment->student_name ?: '-' ?></td>
                            <td class="text-end fw-bold"><?= number_format($payment->amount, 2, ',', '.') ?> Kz</td>
                            <td><?= $payment->payment_method ?></td>
                            <td><?= $payment->payment_reference ?: '-' ?></td>
                            <td>
                                <?php if ($payment->receipt_number): ?>
                                    <a href="<?= site_url('admin/financial/payments/receipt/' . $payment->id) ?>" 
                                       class="btn btn-sm btn-success" target="_blank">
                                        <i class="fas fa-file-pdf"></i> Recibo
                                    </a>
                                <?php else: ?>
                                    <a href="<?= site_url('admin/financial/payments/generate-receipt/' . $payment->id) ?>" 
                                       class="btn btn-sm btn-warning">
                                        <i class="fas fa-plus-circle"></i> Gerar
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="<?= site_url('admin/financial/payments/receipt/' . $payment->id) ?>" 
                                   class="btn btn-sm btn-info" target="_blank" title="Ver Recibo">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">Nenhum pagamento encontrado</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <?= $pager->links() ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#paymentsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[1, 'desc']],
        pageLength: 25,
        searching: false
    });
});
</script>
<?= $this->endSection() ?>