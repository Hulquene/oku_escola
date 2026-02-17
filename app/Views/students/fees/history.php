<?= $this->extend('students/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <a href="<?= site_url('students/fees') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('students/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('students/fees') ?>">Propinas</a></li>
            <li class="breadcrumb-item active" aria-current="page">Histórico</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Payments Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-history"></i> Histórico de Pagamentos
    </div>
    <div class="card-body">
        <?php if (!empty($payments)): ?>
            <div class="table-responsive">
                <table class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <th>Nº Recibo</th>
                            <th>Data</th>
                            <th>Referência</th>
                            <th>Tipo</th>
                            <th>Ano Letivo</th>
                            <th class="text-end">Valor</th>
                            <th>Método</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $totalAmount = 0;
                        foreach ($payments as $payment): 
                            $totalAmount += $payment->amount_paid;
                        ?>
                            <tr>
                                <td><span class="badge bg-secondary"><?= $payment->payment_number ?></span></td>
                                <td><?= date('d/m/Y', strtotime($payment->payment_date)) ?></td>
                                <td><?= $payment->reference_number ?></td>
                                <td><?= $payment->type_name ?></td>
                                <td><?= $payment->year_name ?></td>
                                <td class="text-end fw-bold"><?= number_format($payment->amount_paid, 2, ',', '.') ?> Kz</td>
                                <td><?= $payment->payment_method ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <th colspan="5" class="text-end">Total Pago:</th>
                            <th class="text-end"><?= number_format($totalAmount, 2, ',', '.') ?> Kz</th>
                            <th></th>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <!-- Summary -->
            <div class="row mt-4">
                <div class="col-md-6">
                    <div class="alert alert-success">
                        <h5>Total de Pagamentos</h5>
                        <h3><?= count($payments) ?></h3>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="alert alert-info">
                        <h5>Valor Total Pago</h5>
                        <h3><?= number_format($totalAmount, 2, ',', '.') ?> Kz</h3>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <p class="text-muted text-center">Nenhum pagamento encontrado.</p>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>