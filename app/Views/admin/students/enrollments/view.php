<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <div>
            <a href="<?= site_url('admin/students/enrollments/history/' . $enrollment->student_id) ?>" class="btn btn-info">
                <i class="fas fa-history"></i> Histórico do Aluno
            </a>
            <a href="<?= site_url('admin/students/view/' . $enrollment->student_id) ?>" class="btn btn-primary">
                <i class="fas fa-user-graduate"></i> Ver Aluno
            </a>
            <a href="<?= site_url('admin/students/enrollments') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/students/enrollments') ?>">Matrículas</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detalhes da Matrícula</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<div class="row">
    <div class="col-md-6">
        <!-- Informações da Matrícula -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-file-signature"></i> Informações da Matrícula
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 200px;">Nº Matrícula</th>
                        <td><span class="badge bg-info fs-6"><?= $enrollment->enrollment_number ?></span></td>
                    </tr>
                    <tr>
                        <th>Data da Matrícula</th>
                        <td><?= date('d/m/Y', strtotime($enrollment->enrollment_date)) ?></td>
                    </tr>
                    <tr>
                        <th>Tipo</th>
                        <td><?= $enrollment->enrollment_type ?></td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <?php
                            $statusClass = [
                                'Ativo' => 'success',
                                'Pendente' => 'warning',
                                'Concluído' => 'info',
                                'Transferido' => 'primary',
                                'Anulado' => 'danger'
                            ][$enrollment->status] ?? 'secondary';
                            ?>
                            <span class="badge bg-<?= $statusClass ?> p-2"><?= $enrollment->status ?></span>
                        </td>
                    </tr>
                    <tr>
                        <th>Ano Letivo</th>
                        <td><?= $enrollment->year_name ?> (<?= date('Y', strtotime($enrollment->start_date)) ?>)</td>
                    </tr>
                    <tr>
                        <th>Registrado por</th>
                        <td><?= $enrollment->created_by_username ?? 'Sistema' ?></td>
                    </tr>
                    <tr>
                        <th>Data de Registro</th>
                        <td><?= date('d/m/Y H:i', strtotime($enrollment->created_at)) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    
    <div class="col-md-6">
        <!-- Informações do Aluno -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-user-graduate"></i> Informações do Aluno
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 150px;">Nome</th>
                        <td><?= $enrollment->first_name ?> <?= $enrollment->last_name ?></td>
                    </tr>
                    <tr>
                        <th>Matrícula</th>
                        <td><?= $enrollment->student_number ?></td>
                    </tr>
                    <tr>
                        <th>Email</th>
                        <td><?= $enrollment->email ?></td>
                    </tr>
                    <tr>
                        <th>Telefone</th>
                        <td><?= $enrollment->phone ?: '-' ?></td>
                    </tr>
                </table>
                
                <div class="mt-3">
                    <a href="<?= site_url('admin/students/view/' . $enrollment->student_id) ?>" 
                       class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-external-link-alt"></i> Ver Perfil Completo
                    </a>
                </div>
            </div>
        </div>
        
        <!-- Informações da Turma -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-school"></i> Informações da Turma
            </div>
            <div class="card-body">
                <table class="table table-bordered">
                    <tr>
                        <th style="width: 150px;">Turma</th>
                        <td><?= $enrollment->class_name ?></td>
                    </tr>
                    <tr>
                        <th>Código</th>
                        <td><?= $enrollment->class_code ?></td>
                    </tr>
                    <tr>
                        <th>Turno</th>
                        <td><?= $enrollment->class_shift ?></td>
                    </tr>
                    <tr>
                        <th>Sala</th>
                        <td><?= $enrollment->class_room ?: '-' ?></td>
                    </tr>
                </table>
                
                <div class="mt-3">
                    <a href="<?= site_url('admin/classes/classes/view/' . $enrollment->class_id) ?>" 
                       class="btn btn-sm btn-outline-success">
                        <i class="fas fa-external-link-alt"></i> Ver Detalhes da Turma
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Taxas da Matrícula -->
<div class="card mt-2">
    <div class="card-header">
        <i class="fas fa-money-bill"></i> Taxas e Propinas
    </div>
    <div class="card-body">
        <?php if (!empty($fees)): ?>
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Referência</th>
                            <th>Tipo</th>
                            <th>Valor</th>
                            <th>Data Vencimento</th>
                            <th>Status</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($fees as $fee): ?>
                            <tr>
                                <td><small><?= $fee->reference_number ?></small></td>
                                <td><?= $fee->type_name ?></td>
                                <td><?= number_format($fee->total_amount, 2, ',', '.') ?> Kz</td>
                                <td><?= date('d/m/Y', strtotime($fee->due_date)) ?></td>
                                <td>
                                    <?php
                                    $feeStatusClass = [
                                        'Pago' => 'success',
                                        'Pendente' => 'warning',
                                        'Vencido' => 'danger',
                                        'Parcial' => 'info'
                                    ][$fee->status] ?? 'secondary';
                                    ?>
                                    <span class="badge bg-<?= $feeStatusClass ?>"><?= $fee->status ?></span>
                                </td>
                                <td>
                                    <button type="button" class="btn btn-sm btn-success" 
                                            onclick="registerPayment(<?= $fee->id ?>)"
                                            <?= $fee->status == 'Pago' ? 'disabled' : '' ?>>
                                        <i class="fas fa-money-bill-wave"></i> Pagar
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted text-center">Nenhuma taxa associada a esta matrícula.</p>
        <?php endif; ?>
    </div>
</div>

<!-- Observações -->
<?php if ($enrollment->observations): ?>
<div class="card mt-3">
    <div class="card-header">
        <i class="fas fa-sticky-note"></i> Observações
    </div>
    <div class="card-body">
        <?= nl2br($enrollment->observations) ?>
    </div>
</div>
<?php endif; ?>

<!-- Modal de Pagamento -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="paymentForm" method="post" action="<?= site_url('admin/fees/payments/process') ?>">
                <?= csrf_field() ?>
                <input type="hidden" name="student_fee_id" id="payment_fee_id">
                
                <div class="modal-header">
                    <h5 class="modal-title">Registrar Pagamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="payment_date" class="form-label">Data do Pagamento</label>
                        <input type="date" class="form-control" id="payment_date" name="payment_date" 
                               value="<?= date('Y-m-d') ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="amount_paid" class="form-label">Valor Pago (Kz)</label>
                        <input type="number" class="form-control" id="amount_paid" name="amount_paid" 
                               step="0.01" min="0" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Método de Pagamento</label>
                        <select class="form-select" id="payment_method" name="payment_method" required>
                            <option value="">Selecione...</option>
                            <option value="Dinheiro">Dinheiro</option>
                            <option value="Transferência">Transferência Bancária</option>
                            <option value="Multicaixa">Multicaixa</option>
                            <option value="Depósito">Depósito</option>
                            <option value="Cheque">Cheque</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_reference" class="form-label">Referência</label>
                        <input type="text" class="form-control" id="payment_reference" name="payment_reference">
                        <small class="text-muted">Nº de referência da transação</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="observations" class="form-label">Observações</label>
                        <textarea class="form-control" id="observations" name="observations" rows="2"></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Registrar Pagamento</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function registerPayment(feeId) {
    document.getElementById('payment_fee_id').value = feeId;
    
    // Buscar informações da taxa
    fetch(`<?= site_url('admin/fees/get-fee/') ?>/${feeId}`)
        .then(response => response.json())
        .then(data => {
            document.getElementById('amount_paid').value = data.total_amount;
            document.getElementById('amount_paid').max = data.total_amount;
        });
    
    new bootstrap.Modal(document.getElementById('paymentModal')).show();
}

document.getElementById('paymentForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    fetch(this.action, {
        method: 'POST',
        body: new FormData(this)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
            location.reload();
        } else {
            alert('Erro ao registrar pagamento: ' + data.message);
        }
    });
});
</script>
<?= $this->endSection() ?>