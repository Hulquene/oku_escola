<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <a href="<?= site_url('admin/financial/invoices/form-add') ?>" class="btn btn-primary">
            <i class="fas fa-plus-circle"></i> Nova Fatura
        </a>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/financial') ?>">Financeiro</a></li>
            <li class="breadcrumb-item active" aria-current="page">Faturas</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Summary Cards -->
<div class="row mb-4">
    <?php
    $totalAmount = 0;
    $totalPaid = 0;
    $totalPending = 0;
    $totalOverdue = 0;
    
    foreach ($invoices as $invoice) {
        $totalAmount += $invoice->total_amount;
        if ($invoice->status == 'Paga') {
            $totalPaid += $invoice->total_amount;
        } elseif ($invoice->status == 'Pendente' || $invoice->status == 'Vencida') {
            $totalPending += $invoice->total_amount;
        }
        if ($invoice->status == 'Vencida') {
            $totalOverdue++;
        }
    }
    ?>
    
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6 class="card-title">Total Faturado</h6>
                <h3><?= number_format($totalAmount, 2, ',', '.') ?> Kz</h3>
                <small><?= count($invoices) ?> faturas</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="card-title">Total Recebido</h6>
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
                <h6 class="card-title">Faturas Vencidas</h6>
                <h3><?= $totalOverdue ?></h3>
                <small>Em atraso</small>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Todos</option>
                    <option value="Rascunho" <?= $selectedStatus == 'Rascunho' ? 'selected' : '' ?>>Rascunho</option>
                    <option value="Emitida" <?= $selectedStatus == 'Emitida' ? 'selected' : '' ?>>Emitida</option>
                    <option value="Paga" <?= $selectedStatus == 'Paga' ? 'selected' : '' ?>>Paga</option>
                    <option value="Parcial" <?= $selectedStatus == 'Parcial' ? 'selected' : '' ?>>Parcial</option>
                    <option value="Vencida" <?= $selectedStatus == 'Vencida' ? 'selected' : '' ?>>Vencida</option>
                    <option value="Cancelada" <?= $selectedStatus == 'Cancelada' ? 'selected' : '' ?>>Cancelada</option>
                </select>
            </div>
            
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
            
            <div class="col-md-2">
                <label for="start_date" class="form-label">Data Início</label>
                <input type="date" class="form-control" id="start_date" name="start_date" value="<?= $selectedStartDate ?>">
            </div>
            
            <div class="col-md-2">
                <label for="end_date" class="form-label">Data Fim</label>
                <input type="date" class="form-control" id="end_date" name="end_date" value="<?= $selectedEndDate ?>">
            </div>
            
            <div class="col-md-3 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                <a href="<?= site_url('admin/financial/invoices') ?>" class="btn btn-secondary">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Invoices Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-file-invoice"></i> Lista de Faturas
    </div>
    <div class="card-body">
        <table id="invoicesTable" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Nº Fatura</th>
                    <th>Cliente</th>
                    <th>Data</th>
                    <th>Vencimento</th>
                    <th>Valor</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($invoices)): ?>
                    <?php foreach ($invoices as $invoice): ?>
                        <?php
                        $statusClass = [
                            'Rascunho' => 'secondary',
                            'Emitida' => 'primary',
                            'Paga' => 'success',
                            'Parcial' => 'info',
                            'Vencida' => 'danger',
                            'Cancelada' => 'dark'
                        ][$invoice->status] ?? 'secondary';
                        ?>
                        <tr>
                            <td><span class="badge bg-secondary"><?= $invoice->invoice_number ?></span></td>
                            <td>
                                <?= $invoice->student_name ?: $invoice->guardian_name ?: '-' ?>
                                <?php if ($invoice->student_number): ?>
                                    <br><small class="text-muted"><?= $invoice->student_number ?></small>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d/m/Y', strtotime($invoice->invoice_date)) ?></td>
                            <td><?= date('d/m/Y', strtotime($invoice->due_date)) ?></td>
                            <td class="text-end fw-bold"><?= number_format($invoice->total_amount, 2, ',', '.') ?> Kz</td>
                            <td><span class="badge bg-<?= $statusClass ?>"><?= $invoice->status ?></span></td>
                            <td>
                                <a href="<?= site_url('admin/financial/invoices/view/' . $invoice->id) ?>" 
                                   class="btn btn-sm btn-success" title="Ver Detalhes">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if ($invoice->status != 'Paga'): ?>
                                    <a href="<?= site_url('admin/financial/invoices/edit/' . $invoice->id) ?>" 
                                       class="btn btn-sm btn-info" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                <?php endif; ?>
                                <a href="<?= site_url('admin/financial/invoices/print/' . $invoice->id) ?>" 
                                   class="btn btn-sm btn-warning" target="_blank" title="Imprimir">
                                    <i class="fas fa-print"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">Nenhuma fatura encontrada</td>
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
    $('#invoicesTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[2, 'desc']],
        pageLength: 25,
        searching: false
    });
});
</script>
<?= $this->endSection() ?>