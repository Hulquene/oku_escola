<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/fees/payments') ?>">Pagamentos</a></li>
            <li class="breadcrumb-item active" aria-current="page">Registrar Pagamento</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<div class="row">
    <div class="col-md-4">
        <!-- Fee Info Card -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-info-circle"></i> Informações da Propina
            </div>
            <div class="card-body">
                <table class="table table-sm">
                    <tr>
                        <th>Referência:</th>
                        <td><?= $fee->reference_number ?></td>
                    </tr>
                    <tr>
                        <th>Aluno:</th>
                        <td><?= $fee->first_name ?> <?= $fee->last_name ?></td>
                    </tr>
                    <tr>
                        <th>Nº Matrícula:</th>
                        <td><?= $fee->student_number ?></td>
                    </tr>
                    <tr>
                        <th>Turma:</th>
                        <td><?= $fee->class_name ?></td>
                    </tr>
                    <tr>
                        <th>Tipo:</th>
                        <td><?= $fee->type_name ?> (<?= $fee->type_category ?>)</td>
                    </tr>
                    <tr>
                        <th>Valor Total:</th>
                        <td class="fw-bold"><?= number_format($fee->total_amount, 2, ',', '.') ?> Kz</td>
                    </tr>
                    <tr>
                        <th>Total Pago:</th>
                        <td class="text-success"><?= number_format($fee->total_paid ?? 0, 2, ',', '.') ?> Kz</td>
                    </tr>
                    <tr>
                        <th>Saldo:</th>
                        <td class="fw-bold text-<?= $balance > 0 ? 'danger' : 'success' ?>">
                            <?= number_format($balance, 2, ',', '.') ?> Kz
                        </td>
                    </tr>
                    <tr>
                        <th>Vencimento:</th>
                        <td><?= date('d/m/Y', strtotime($fee->due_date)) ?></td>
                    </tr>
                </table>
            </div>
        </div>
        
        <!-- Payment History -->
        <?php if (!empty($payments)): ?>
        <div class="card">
            <div class="card-header">
                <i class="fas fa-history"></i> Histórico de Pagamentos
            </div>
            <div class="card-body p-0">
                <div class="list-group list-group-flush">
                    <?php foreach ($payments as $payment): ?>
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between align-items-center">
                                <div>
                                    <small class="text-muted"><?= date('d/m/Y', strtotime($payment->payment_date)) ?></small>
                                    <div class="fw-bold"><?= number_format($payment->amount_paid, 2, ',', '.') ?> Kz</div>
                                    <small><?= $payment->payment_method ?></small>
                                </div>
                                <span class="badge bg-success">Pago</span>
                            </div>
                            <?php if ($payment->payment_reference): ?>
                                <small class="text-muted">Ref: <?= $payment->payment_reference ?></small>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
    
    <div class="col-md-8">
        <!-- Payment Form -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-money-bill-wave"></i> Registrar Pagamento
            </div>
            <div class="card-body">
                <?php if ($balance <= 0): ?>
                    <div class="alert alert-success">
                        <i class="fas fa-check-circle"></i>
                        Esta propina já está totalmente paga.
                    </div>
                    <div class="text-center">
                        <a href="<?= site_url('admin/fees/payments') ?>" class="btn btn-primary">
                            <i class="fas fa-arrow-left"></i> Voltar
                        </a>
                    </div>
                <?php else: ?>
                    <form action="<?= site_url('admin/fees/payments/save') ?>" method="post">
                        <?= csrf_field() ?>
                        <input type="hidden" name="student_fee_id" value="<?= $fee->id ?>">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="payment_date" class="form-label">Data do Pagamento <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="payment_date" name="payment_date" 
                                           value="<?= date('Y-m-d') ?>" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="amount_paid" class="form-label">Valor a Pagar <span class="text-danger">*</span></label>
                                    <div class="input-group">
                                        <input type="number" class="form-control" id="amount_paid" name="amount_paid" 
                                               step="0.01" min="0.01" max="<?= $balance ?>" 
                                               value="<?= $balance ?>" required>
                                        <span class="input-group-text">Kz</span>
                                    </div>
                                    <small class="text-muted">Máximo: <?= number_format($balance, 2, ',', '.') ?> Kz</small>
                                </div>
                            </div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="payment_method" class="form-label">Método de Pagamento <span class="text-danger">*</span></label>
                                    <select class="form-select" id="payment_method" name="payment_method" required>
                                        <option value="">Selecione...</option>
                                        <option value="Dinheiro">Dinheiro</option>
                                        <option value="Transferência">Transferência Bancária</option>
                                        <option value="Depósito">Depósito</option>
                                        <option value="Multicaixa">Multicaixa</option>
                                        <option value="Cheque">Cheque</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="payment_reference" class="form-label">Referência</label>
                                    <input type="text" class="form-control" id="payment_reference" name="payment_reference"
                                           placeholder="Nº de referência da transação">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row" id="bankFields" style="display: none;">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="bank_name" class="form-label">Banco</label>
                                    <input type="text" class="form-control" id="bank_name" name="bank_name">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="bank_account" class="form-label">Nº de Conta</label>
                                    <input type="text" class="form-control" id="bank_account" name="bank_account">
                                </div>
                            </div>
                        </div>
                        
                        <div class="row" id="checkFields" style="display: none;">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="check_number" class="form-label">Nº do Cheque</label>
                                    <input type="text" class="form-control" id="check_number" name="check_number">
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="observations" class="form-label">Observações</label>
                            <textarea class="form-control" id="observations" name="observations" rows="3"></textarea>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between">
                            <a href="<?= site_url('admin/fees/payments') ?>" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Cancelar
                            </a>
                            <button type="submit" class="btn btn-success">
                                <i class="fas fa-save"></i> Registrar Pagamento
                            </button>
                        </div>
                    </form>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// Show/hide fields based on payment method
document.getElementById('payment_method').addEventListener('change', function() {
    const method = this.value;
    
    document.getElementById('bankFields').style.display = 'none';
    document.getElementById('checkFields').style.display = 'none';
    
    if (method === 'Transferência' || method === 'Depósito') {
        document.getElementById('bankFields').style.display = 'flex';
    } else if (method === 'Cheque') {
        document.getElementById('checkFields').style.display = 'flex';
    }
});

// Validate amount
document.getElementById('amount_paid').addEventListener('change', function() {
    const max = parseFloat(this.max);
    const value = parseFloat(this.value);
    
    if (value > max) {
        this.value = max;
        alert('O valor não pode ser maior que o saldo devedor');
    }
});
</script>
<?= $this->endSection() ?>