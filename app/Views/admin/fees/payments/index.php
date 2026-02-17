<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/fees') ?>">Propinas</a></li>
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
    $totalPaid = 0;
    $totalPending = 0;
    $totalOverdue = 0;
    
    foreach ($payments as $fee) {
        $totalAmount += $fee->total_amount;
        $paid = $fee->total_paid ?? 0;
        $totalPaid += $paid;
        if ($fee->status == 'Pendente' || $fee->status == 'Vencido') {
            $totalPending += $fee->total_amount - $paid;
        }
        if ($fee->status == 'Vencido') {
            $totalOverdue++;
        }
    }
    ?>
    
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <h6 class="card-title">Total de Propinas</h6>
                <h3><?= number_format($totalAmount, 2, ',', '.') ?> Kz</h3>
                <small><?= count($payments) ?> registos</small>
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

<!-- Filters -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-2">
                <label for="status" class="form-label">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Todos</option>
                    <option value="Pendente" <?= $selectedStatus == 'Pendente' ? 'selected' : '' ?>>Pendente</option>
                    <option value="Pago" <?= $selectedStatus == 'Pago' ? 'selected' : '' ?>>Pago</option>
                    <option value="Parcial" <?= $selectedStatus == 'Parcial' ? 'selected' : '' ?>>Parcial</option>
                    <option value="Vencido" <?= $selectedStatus == 'Vencido' ? 'selected' : '' ?>>Vencido</option>
                </select>
            </div>
            
            <div class="col-md-3">
                <label for="class" class="form-label">Turma</label>
                <select class="form-select" id="class" name="class">
                    <option value="">Todas</option>
                    <?php if (!empty($classes)): ?>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?= $class->id ?>" <?= $selectedClass == $class->id ? 'selected' : '' ?>>
                                <?= $class->class_name ?> (<?= $class->class_code ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
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
            
            <div class="col-12">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> Filtrar
                </button>
                <a href="<?= site_url('admin/fees/payments') ?>" class="btn btn-secondary">
                    <i class="fas fa-undo"></i> Limpar
                </a>
                <button type="button" class="btn btn-success float-end" onclick="generateFees()">
                    <i class="fas fa-sync-alt"></i> Gerar Propinas
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-credit-card"></i> Lista de Propinas
    </div>
    <div class="card-body">
        <table id="paymentsTable" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>Referência</th>
                    <th>Aluno</th>
                    <th>Turma</th>
                    <th>Tipo</th>
                    <th>Valor Total</th>
                    <th>Valor Pago</th>
                    <th>Saldo</th>
                    <th>Vencimento</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($payments)): ?>
                    <?php foreach ($payments as $fee): ?>
                        <?php 
                        $paid = $fee->total_paid ?? 0;
                        $balance = $fee->total_amount - $paid;
                        $statusClass = [
                            'Pago' => 'success',
                            'Pendente' => 'warning',
                            'Parcial' => 'info',
                            'Vencido' => 'danger',
                            'Cancelado' => 'secondary'
                        ][$fee->status] ?? 'secondary';
                        ?>
                        <tr>
                            <td><small><?= $fee->reference_number ?></small></td>
                            <td><?= $fee->first_name ?> <?= $fee->last_name ?></td>
                            <td><?= $fee->class_name ?></td>
                            <td><?= $fee->type_name ?></td>
                            <td class="text-end"><?= number_format($fee->total_amount, 2, ',', '.') ?> Kz</td>
                            <td class="text-end"><?= number_format($paid, 2, ',', '.') ?> Kz</td>
                            <td class="text-end <?= $balance > 0 ? 'text-danger' : 'text-success' ?>">
                                <?= number_format($balance, 2, ',', '.') ?> Kz
                            </td>
                            <td class="text-center <?= $fee->due_date < date('Y-m-d') && $balance > 0 ? 'text-danger fw-bold' : '' ?>">
                                <?= date('d/m/Y', strtotime($fee->due_date)) ?>
                            </td>
                            <td>
                                <span class="badge bg-<?= $statusClass ?>"><?= $fee->status ?></span>
                            </td>
                            <td>
                                <?php if ($balance > 0): ?>
                                    <a href="<?= site_url('admin/fees/payments/form/' . $fee->id) ?>" 
                                       class="btn btn-sm btn-success" title="Registrar Pagamento">
                                        <i class="fas fa-money-bill-wave"></i>
                                    </a>
                                <?php endif; ?>
                                
                                <?php if ($paid > 0): ?>
                                    <a href="<?= site_url('admin/fees/payments/history/' . $fee->student_id) ?>" 
                                       class="btn btn-sm btn-info" title="Histórico de Pagamentos">
                                        <i class="fas fa-history"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="text-center">Nenhuma propina encontrada</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        
        <?= $pager->links() ?>
    </div>
</div>

<!-- Modal Gerar Propinas -->
<div class="modal fade" id="generateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('admin/fees/generate-fees') ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="modal-header">
                    <h5 class="modal-title">Gerar Propinas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="alert alert-warning">
                        <i class="fas fa-exclamation-triangle"></i>
                        Esta operação irá gerar propinas para todos os alunos matriculados com base na estrutura de taxas definida.
                    </div>
                    
                    <div class="mb-3">
                        <label for="generate_academic_year" class="form-label">Ano Letivo</label>
                        <select class="form-select" id="generate_academic_year" name="academic_year_id" required>
                            <option value="">Selecione...</option>
                            <?php if (!empty($academicYears)): ?>
                                <?php foreach ($academicYears as $year): ?>
                                    <option value="<?= $year->id ?>">
                                        <?= $year->year_name ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Gerar Propinas</button>
                </div>
            </form>
        </div>
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
        order: [[7, 'asc']],
        pageLength: 25,
        searching: false
    });
});

function generateFees() {
    $('#generateModal').modal('show');
}
</script>
<?= $this->endSection() ?>