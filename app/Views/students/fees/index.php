<?= $this->extend('students/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <div>
            <a href="<?= site_url('students/fees/history') ?>" class="btn btn-info me-2">
                <i class="fas fa-history"></i> Histórico
            </a>
            <a href="<?= site_url('students/fees/receipts') ?>" class="btn btn-success">
                <i class="fas fa-file-invoice"></i> Meus Recibos
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('students/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item active" aria-current="page">Minhas Propinas</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Summary Cards -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6 class="card-title">Total a Pagar</h6>
                <h3><?= number_format($totals->total, 2, ',', '.') ?> Kz</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="card-title">Total Pago</h6>
                <h3><?= number_format($totals->paid, 2, ',', '.') ?> Kz</h3>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <h6 class="card-title">Saldo Pendente</h6>
                <h3><?= number_format($totals->pending, 2, ',', '.') ?> Kz</h3>
            </div>
        </div>
    </div>
</div>

<!-- Fees Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-money-bill"></i> Lista de Propinas
    </div>
    <div class="card-body">
        <?php if (!empty($fees)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Referência</th>
                            <th>Tipo</th>
                            <th>Valor</th>
                            <th>Vencimento</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($fees as $fee): 
                            $paid = $fee->paid_amount ?? 0;
                            $balance = $fee->total_amount - $paid;
                            $statusClass = [
                                'Pago' => 'success',
                                'Pendente' => 'warning',
                                'Vencido' => 'danger',
                                'Parcial' => 'info',
                                'Cancelado' => 'secondary'
                            ][$fee->status] ?? 'secondary';
                        ?>
                            <tr>
                                <td><small><?= $fee->reference_number ?></small></td>
                                <td><?= $fee->type_name ?></td>
                                <td class="fw-bold"><?= number_format($fee->total_amount, 2, ',', '.') ?> Kz</td>
                                <td>
                                    <?= date('d/m/Y', strtotime($fee->due_date)) ?>
                                    <?php if ($fee->due_date < date('Y-m-d') && $fee->status != 'Pago'): ?>
                                        <span class="badge bg-danger">Vencido</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="badge bg-<?= $statusClass ?>"><?= $fee->status ?></span>
                                </td>
                                <td>
                                    <?php if ($balance > 0): ?>
                                        <a href="<?= site_url('students/fees/pay/' . $fee->id) ?>" 
                                           class="btn btn-sm btn-success">
                                            <i class="fas fa-money-bill-wave"></i> Pagar
                                        </a>
                                    <?php endif; ?>
                                    <?php if ($paid > 0): ?>
                                        <button type="button" class="btn btn-sm btn-info" 
                                                onclick="showPaymentDetails(<?= $fee->id ?>)">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted text-center">Nenhuma propina encontrada.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Payment Details Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Detalhes do Pagamento</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="paymentDetails">
                Carregando...
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function showPaymentDetails(feeId) {
    const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
    const details = document.getElementById('paymentDetails');
    
    details.innerHTML = 'Carregando...';
    modal.show();
    
    fetch(`<?= site_url('students/fees/get-payments/') ?>/${feeId}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                let html = '<div class="list-group">';
                data.payments.forEach(payment => {
                    html += `
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <strong>${payment.payment_number}</strong>
                                    <br>
                                    <small>${payment.payment_date}</small>
                                </div>
                                <div class="text-end">
                                    <span class="fw-bold">${payment.amount_paid} Kz</span>
                                    <br>
                                    <small>${payment.payment_method}</small>
                                </div>
                            </div>
                        </div>
                    `;
                });
                html += '</div>';
                details.innerHTML = html;
            } else {
                details.innerHTML = '<p class="text-muted">Nenhum pagamento encontrado.</p>';
            }
        })
        .catch(error => {
            details.innerHTML = '<p class="text-danger">Erro ao carregar detalhes.</p>';
        });
}
</script>
<?= $this->endSection() ?>