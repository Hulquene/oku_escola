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
            <li class="breadcrumb-item active" aria-current="page">Recibos</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Receipts Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-file-invoice"></i> Meus Recibos
    </div>
    <div class="card-body">
        <?php if (!empty($receipts)): ?>
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
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($receipts as $receipt): ?>
                            <tr>
                                <td><span class="badge bg-secondary"><?= $receipt->receipt_number ?></span></td>
                                <td><?= date('d/m/Y', strtotime($receipt->payment_date)) ?></td>
                                <td><?= $receipt->reference_number ?></td>
                                <td><?= $receipt->type_name ?></td>
                                <td><?= $receipt->year_name ?></td>
                                <td class="text-end fw-bold"><?= number_format($receipt->amount_paid, 2, ',', '.') ?> Kz</td>
                                <td><?= $receipt->payment_method ?></td>
                                <td>
                                    <a href="<?= site_url('printpdf/receipt/' . $receipt->id) ?>" 
                                       class="btn btn-sm btn-success" target="_blank">
                                        <i class="fas fa-file-pdf"></i> PDF
                                    </a>
                                    <button class="btn btn-sm btn-info" onclick="printReceipt(<?= $receipt->id ?>)">
                                        <i class="fas fa-print"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="text-center py-4">
                <i class="fas fa-receipt fa-3x text-muted mb-3"></i>
                <p class="text-muted">Nenhum recibo encontrado.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function printReceipt(id) {
    // Open receipt in new window and print
    window.open(`<?= site_url('printpdf/receipt/') ?>/${id}`, '_blank');
}
</script>
<?= $this->endSection() ?>