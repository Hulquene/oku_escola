<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?> - <?= $invoice->invoice_number ?></h1>
        <div>
            <?php if ($invoice->status != 'Paga' && $invoice->status != 'Cancelada'): ?>
                <a href="<?= site_url('admin/financial/invoices/mark-paid/' . $invoice->id) ?>" 
                   class="btn btn-success"
                   onclick="return confirm('Marcar esta fatura como paga?')">
                    <i class="fas fa-check-circle"></i> Marcar como Paga
                </a>
            <?php endif; ?>
            <a href="<?= site_url('admin/financial/invoices/print/' . $invoice->id) ?>" 
               class="btn btn-warning" target="_blank">
                <i class="fas fa-print"></i> Imprimir
            </a>
            <a href="<?= site_url('admin/financial/invoices') ?>" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/financial/invoices') ?>">Faturas</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detalhes da Fatura</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<div class="row">
    <div class="col-md-8">
        <!-- Invoice Details -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-file-invoice"></i> Detalhes da Fatura
            </div>
            <div class="card-body">
                <div class="row mb-4">
                    <div class="col-md-6">
                        <h5>Informações da Fatura</h5>
                        <table class="table table-sm">
                            <tr>
                                <th style="width: 150px;">Número:</th>
                                <td><span class="badge bg-secondary"><?= $invoice->invoice_number ?></span></td>
                            </tr>
                            <tr>
                                <th>Data de Emissão:</th>
                                <td><?= date('d/m/Y', strtotime($invoice->invoice_date)) ?></td>
                            </tr>
                            <tr>
                                <th>Data de Vencimento:</th>
                                <td><?= date('d/m/Y', strtotime($invoice->due_date)) ?></td>
                            </tr>
                            <tr>
                                <th>Status:</th>
                                <td>
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
                                    <span class="badge bg-<?= $statusClass ?>"><?= $invoice->status ?></span>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <h5>Cliente</h5>
                        <?php if ($invoice->student_id): ?>
                            <table class="table table-sm">
                                <tr>
                                    <th style="width: 150px;">Aluno:</th>
                                    <td><?= $invoice->student_name ?></td>
                                </tr>
                                <tr>
                                    <th>Nº Matrícula:</th>
                                    <td><?= $invoice->student_number ?></td>
                                </tr>
                            </table>
                        <?php elseif ($invoice->guardian_id): ?>
                            <table class="table table-sm">
                                <tr>
                                    <th style="width: 150px;">Encarregado:</th>
                                    <td><?= $invoice->guardian_name ?></td>
                                </tr>
                            </table>
                        <?php else: ?>
                            <p class="text-muted">Cliente não especificado</p>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Items Table -->
                <h5>Itens da Fatura</h5>
                <div class="table-responsive">
                    <table class="table table-striped table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>Descrição</th>
                                <th class="text-center">Quantidade</th>
                                <th class="text-end">Preço Unit.</th>
                                <th class="text-end">Taxa</th>
                                <th class="text-end">Desconto</th>
                                <th class="text-end">Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (!empty($invoice->items)): ?>
                                <?php foreach ($invoice->items as $item): ?>
                                    <tr>
                                        <td><?= $item->description ?></td>
                                        <td class="text-center"><?= $item->quantity ?></td>
                                        <td class="text-end"><?= number_format($item->unit_price, 2, ',', '.') ?> Kz</td>
                                        <td class="text-end"><?= $item->tax_rate ?>%</td>
                                        <td class="text-end"><?= $item->discount_rate ?>%</td>
                                        <td class="text-end fw-bold"><?= number_format($item->total, 2, ',', '.') ?> Kz</td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                        <tfoot class="table-light">
                            <tr>
                                <td colspan="5" class="text-end"><strong>Subtotal:</strong></td>
                                <td class="text-end"><?= number_format($invoice->subtotal, 2, ',', '.') ?> Kz</td>
                            </tr>
                            <tr>
                                <td colspan="5" class="text-end"><strong>IVA Total:</strong></td>
                                <td class="text-end"><?= number_format($invoice->tax_amount, 2, ',', '.') ?> Kz</td>
                            </tr>
                            <?php if ($invoice->discount_amount > 0): ?>
                            <tr>
                                <td colspan="5" class="text-end"><strong>Desconto Total:</strong></td>
                                <td class="text-end"><?= number_format($invoice->discount_amount, 2, ',', '.') ?> Kz</td>
                            </tr>
                            <?php endif; ?>
                            <tr>
                                <td colspan="5" class="text-end"><strong>Total:</strong></td>
                                <td class="text-end fw-bold"><?= number_format($invoice->total_amount, 2, ',', '.') ?> Kz</td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <?php if ($invoice->notes): ?>
                    <div class="mt-3">
                        <h6>Observações:</h6>
                        <p class="bg-light p-2 rounded"><?= nl2br($invoice->notes) ?></p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Payment History -->
        <div class="card mb-4">
            <div class="card-header">
                <i class="fas fa-history"></i> Histórico de Pagamentos
            </div>
            <div class="card-body">
                <?php
                $paymentModel = new \App\Models\PaymentModel();
                $payments = $paymentModel
                    ->select('tbl_payments.*, CONCAT(tbl_users.first_name, " ", tbl_users.last_name) as received_by_name')
                    ->join('tbl_users', 'tbl_users.id = tbl_payments.received_by', 'left')
                    ->where('invoice_id', $invoice->id)
                    ->orderBy('payment_date', 'DESC')
                    ->findAll();
                ?>
                
                <?php if (!empty($payments)): ?>
                    <div class="list-group">
                        <?php foreach ($payments as $payment): ?>
                            <div class="list-group-item">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <small class="text-muted"><?= date('d/m/Y', strtotime($payment->payment_date)) ?></small>
                                        <h6 class="mb-0"><?= number_format($payment->amount, 2, ',', '.') ?> Kz</h6>
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
                    
                    <?php
                    $totalPaid = array_sum(array_column($payments, 'amount'));
                    $balance = $invoice->total_amount - $totalPaid;
                    ?>
                    
                    <hr>
                    <div class="d-flex justify-content-between">
                        <strong>Total Pago:</strong>
                        <span class="text-success"><?= number_format($totalPaid, 2, ',', '.') ?> Kz</span>
                    </div>
                    <div class="d-flex justify-content-between">
                        <strong>Saldo:</strong>
                        <span class="fw-bold <?= $balance > 0 ? 'text-danger' : 'text-success' ?>">
                            <?= number_format($balance, 2, ',', '.') ?> Kz
                        </span>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center">Nenhum pagamento registado</p>
                <?php endif; ?>
                
                <?php if ($invoice->status != 'Paga' && $invoice->status != 'Cancelada'): ?>
                    <div class="mt-3">
                        <button type="button" class="btn btn-success w-100" data-bs-toggle="modal" data-bs-target="#paymentModal">
                            <i class="fas fa-money-bill-wave"></i> Registar Pagamento
                        </button>
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Company Info -->
        <div class="card">
            <div class="card-header">
                <i class="fas fa-building"></i> Informações da Escola
            </div>
            <div class="card-body">
                <h6><?= $school->name ?></h6>
                <p class="mb-1"><i class="fas fa-map-marker-alt"></i> <?= $school->address ?></p>
                <p class="mb-1"><i class="fas fa-phone"></i> <?= $school->phone ?></p>
                <p class="mb-1"><i class="fas fa-envelope"></i> <?= $school->email ?></p>
                <p class="mb-0"><i class="fas fa-id-card"></i> NIF: <?= $school->nif ?></p>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('admin/financial/payments/save') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="invoice_id" value="<?= $invoice->id ?>">
                
                <div class="modal-header">
                    <h5 class="modal-title">Registar Pagamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="payment_date" class="form-label">Data do Pagamento</label>
                        <input type="date" class="form-control" id="payment_date" name="payment_date" 
                               value="<?= date('Y-m-d') ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="amount" class="form-label">Valor (Kz)</label>
                        <input type="number" class="form-control" id="amount" name="amount" 
                               step="0.01" min="0.01" max="<?= $invoice->total_amount ?>" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Método de Pagamento</label>
                        <select class="form-select" id="payment_method" name="payment_method" required>
                            <option value="">Selecione...</option>
                            <option value="Dinheiro">Dinheiro</option>
                            <option value="Transferência">Transferência</option>
                            <option value="Depósito">Depósito</option>
                            <option value="Multicaixa">Multicaixa</option>
                            <option value="Cheque">Cheque</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_reference" class="form-label">Referência</label>
                        <input type="text" class="form-control" id="payment_reference" name="payment_reference">
                    </div>
                    
                    <div class="mb-3">
                        <label for="notes" class="form-label">Observações</label>
                        <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Registar Pagamento</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>