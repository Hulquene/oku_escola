<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <a href="<?= site_url('admin/financial/payments') ?>" class="btn btn-secondary">
            <i class="fas fa-arrow-left"></i> Voltar aos Pagamentos
        </a>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/financial/payments') ?>">Pagamentos</a></li>
            <li class="breadcrumb-item active" aria-current="page">Recibos</li>
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
                <label for="student" class="form-label">Aluno</label>
                <select class="form-select" id="student" name="student">
                    <option value="">Todos</option>
                    <?php if (!empty($students)): ?>
                        <?php foreach ($students as $student): ?>
                            <option value="<?= $student->id ?>" <?= $selectedStudent == $student->id ? 'selected' : '' ?>>
                                <?= $student->first_name ?> <?= $student->last_name ?> (<?= $student->student_number ?>)
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
                <a href="<?= site_url('admin/financial/payments/receipts') ?>" class="btn btn-secondary">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Receipts Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-receipt"></i> Lista de Recibos
    </div>
    <div class="card-body">
        <table id="receiptsTable" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Nº Recibo</th>
                    <th>Data</th>
                    <th>Fatura</th>
                    <th>Cliente</th>
                    <th>Valor</th>
                    <th>Método</th>
                    <th>Recebido por</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($receipts)): ?>
                    <?php foreach ($receipts as $receipt): ?>
                        <tr>
                            <td><span class="badge bg-secondary"><?= $receipt->receipt_number ?></span></td>
                            <td><?= date('d/m/Y', strtotime($receipt->payment_date)) ?></td>
                            <td>
                                <a href="<?= site_url('admin/financial/invoices/view/' . $receipt->invoice_id) ?>">
                                    <?= $receipt->invoice_number ?>
                                </a>
                            </td>
                            <td><?= $receipt->student_name ?: '-' ?></td>
                            <td class="text-end fw-bold"><?= number_format($receipt->amount, 2, ',', '.') ?> Kz</td>
                            <td><?= $receipt->payment_method ?></td>
                            <td><?= $receipt->received_by_name ?? 'Sistema' ?></td>
                            <td>
                                <a href="<?= site_url('admin/financial/payments/receipt/' . $receipt->id) ?>" 
                                   class="btn btn-sm btn-success" target="_blank" title="Ver Recibo">
                                    <i class="fas fa-file-pdf"></i> Ver
                                </a>
                                <a href="<?= site_url('admin/financial/payments/receipt/' . $receipt->id) ?>" 
                                   class="btn btn-sm btn-warning" target="_blank" title="Imprimir">
                                    <i class="fas fa-print"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">Nenhum recibo encontrado</td>
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
    $('#receiptsTable').DataTable({
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