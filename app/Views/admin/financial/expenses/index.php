<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <div>
            <a href="<?= site_url('admin/financial/expenses/summary') ?>" class="btn btn-info me-2">
                <i class="fas fa-chart-pie"></i> Resumo
            </a>
            <a href="<?= site_url('admin/financial/expenses/form-add') ?>" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Nova Despesa
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/financial') ?>">Financeiro</a></li>
            <li class="breadcrumb-item active" aria-current="page">Despesas</li>
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
    
    foreach ($expenses as $expense) {
        $totalAmount += $expense->amount;
        if ($expense->payment_status == 'Pago') {
            $totalPaid += $expense->amount;
        } else {
            $totalPending += $expense->amount;
        }
    }
    ?>
    
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6 class="card-title">Total de Despesas</h6>
                <h3><?= number_format($totalAmount, 2, ',', '.') ?> Kz</h3>
                <small><?= count($expenses) ?> registos</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <h6 class="card-title">Total Pago</h6>
                <h3><?= number_format($totalPaid, 2, ',', '.') ?> Kz</h3>
                <small><?= $totalAmount > 0 ? round(($totalPaid / $totalAmount) * 100, 1) : 0 ?>% do total</small>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card bg-warning text-dark">
            <div class="card-body">
                <h6 class="card-title">Total Pendente</h6>
                <h3><?= number_format($totalPending, 2, ',', '.') ?> Kz</h3>
                <small>Aguardar pagamento</small>
            </div>
        </div>
    </div>
</div>

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-2">
                <label for="category" class="form-label">Categoria</label>
                <select class="form-select" id="category" name="category">
                    <option value="">Todas</option>
                    <?php if (!empty($categories)): ?>
                        <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat->id ?>" <?= $selectedCategory == $cat->id ? 'selected' : '' ?>>
                                <?= $cat->category_name ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Todos</option>
                    <option value="Pendente" <?= $selectedStatus == 'Pendente' ? 'selected' : '' ?>>Pendente</option>
                    <option value="Pago" <?= $selectedStatus == 'Pago' ? 'selected' : '' ?>>Pago</option>
                    <option value="Parcial" <?= $selectedStatus == 'Parcial' ? 'selected' : '' ?>>Parcial</option>
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
            
            <div class="col-md-4 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                <a href="<?= site_url('admin/financial/expenses') ?>" class="btn btn-secondary">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Expenses Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-chart-line"></i> Lista de Despesas
    </div>
    <div class="card-body">
        <table id="expensesTable" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Nº</th>
                    <th>Data</th>
                    <th>Categoria</th>
                    <th>Descrição</th>
                    <th>Fornecedor</th>
                    <th>Valor</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($expenses)): ?>
                    <?php foreach ($expenses as $expense): ?>
                        <tr>
                            <td><span class="badge bg-secondary"><?= $expense->expense_number ?></span></td>
                            <td><?= date('d/m/Y', strtotime($expense->expense_date)) ?></td>
                            <td><?= $expense->category_name ?></td>
                            <td><?= substr($expense->description, 0, 50) ?><?= strlen($expense->description) > 50 ? '...' : '' ?></td>
                            <td><?= $expense->supplier_name ?: '-' ?></td>
                            <td class="text-end fw-bold"><?= number_format($expense->amount, 2, ',', '.') ?> Kz</td>
                            <td>
                                <?php
                                $statusClass = [
                                    'Pago' => 'success',
                                    'Pendente' => 'warning',
                                    'Parcial' => 'info'
                                ][$expense->payment_status] ?? 'secondary';
                                ?>
                                <span class="badge bg-<?= $statusClass ?>"><?= $expense->payment_status ?></span>
                            </td>
                            <td>
                                <a href="<?= site_url('admin/financial/expenses/edit/' . $expense->id) ?>" 
                                   class="btn btn-sm btn-info" title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if ($expense->payment_status != 'Pago'): ?>
                                    <a href="<?= site_url('admin/financial/expenses/mark-paid/' . $expense->id) ?>" 
                                       class="btn btn-sm btn-success" 
                                       onclick="return confirm('Marcar esta despesa como paga?')"
                                       title="Marcar como Paga">
                                        <i class="fas fa-check-circle"></i>
                                    </a>
                                <?php endif; ?>
                                <a href="<?= site_url('admin/financial/expenses/delete/' . $expense->id) ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Tem certeza que deseja eliminar esta despesa?')"
                                   title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">Nenhuma despesa encontrada</td>
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
    $('#expensesTable').DataTable({
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