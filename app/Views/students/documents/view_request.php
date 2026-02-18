<?= $this->extend('students/layouts/index') ?>

<?= $this->section('content') ?>

<div class="page-header bg-gradient-primary py-3 mb-4 rounded-3">
    <div class="container-fluid">
        <div class="row align-items-center">
            <div class="col">
                <h1 class="text-white mb-1"><?= $title ?></h1>
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb text-white-50 mb-0">
                        <li class="breadcrumb-item"><a href="<?= site_url('students/dashboard') ?>" class="text-white">Início</a></li>
                        <li class="breadcrumb-item"><a href="<?= site_url('students/documents') ?>" class="text-white">Documentos</a></li>
                        <li class="breadcrumb-item"><a href="<?= site_url('students/documents/requests') ?>" class="text-white">Solicitações</a></li>
                        <li class="breadcrumb-item active text-white" aria-current="page">#<?= $request->request_number ?></li>
                    </ol>
                </nav>
            </div>
        </div>
    </div>
</div>

<?php if (session()->has('success')): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-check-circle me-2"></i> <?= session('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Detalhes da Solicitação -->
<div class="row">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle text-primary me-2"></i>
                    Informações da Solicitação
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Nº Solicitação:</th>
                                <td><strong><?= $request->request_number ?></strong></td>
                            </tr>
                            <tr>
                                <th>Data:</th>
                                <td><?= date('d/m/Y H:i', strtotime($request->created_at)) ?></td>
                            </tr>
                            <tr>
                                <th>Tipo Documento:</th>
                                <td><?= $request->document_type ?></td>
                            </tr>
                            <tr>
                                <th>Quantidade:</th>
                                <td><?= $request->quantity ?></td>
                            </tr>
                            <tr>
                                <th>Formato:</th>
                                <td>
                                    <?php
                                    $formatLabels = [
                                        'digital' => 'Digital (PDF)',
                                        'impresso' => 'Impresso',
                                        'ambos' => 'Digital + Impresso'
                                    ];
                                    echo $formatLabels[$request->format] ?? $request->format;
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Status:</th>
                                <td>
                                    <?php
                                    $statusLabels = [
                                        'pending' => ['bg-warning', 'Pendente'],
                                        'processing' => ['bg-info', 'Processando'],
                                        'ready' => ['bg-success', 'Pronto'],
                                        'delivered' => ['bg-secondary', 'Entregue'],
                                        'cancelled' => ['bg-danger', 'Cancelado'],
                                        'rejected' => ['bg-danger', 'Rejeitado']
                                    ];
                                    $status = $statusLabels[$request->status] ?? ['bg-secondary', $request->status];
                                    ?>
                                    <span class="badge <?= $status[0] ?> p-2"><?= $status[1] ?></span>
                                </td>
                            </tr>
                            <tr>
                                <th>Valor:</th>
                                <td class="fw-bold <?= $request->fee_amount > 0 ? 'text-danger' : '' ?>">
                                    <?= number_format($request->fee_amount, 2, ',', '.') ?> Kz
                                </td>
                            </tr>
                            <tr>
                                <th>Pagamento:</th>
                                <td>
                                    <?php if ($request->payment_status == 'paid'): ?>
                                        <span class="badge bg-success">Pago</span>
                                        <small class="d-block text-muted">
                                            Ref: <?= $request->payment_reference ?><br>
                                            Data: <?= date('d/m/Y', strtotime($request->payment_date)) ?>
                                        </small>
                                    <?php elseif ($request->payment_status == 'pending' && $request->fee_amount > 0): ?>
                                        <span class="badge bg-warning">Aguardando Pagamento</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Isento</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <th>Método Entrega:</th>
                                <td>
                                    <?php
                                    $deliveryLabels = [
                                        'retirar' => 'Retirar na Escola',
                                        'email' => 'Email',
                                        'correio' => 'Correio'
                                    ];
                                    echo $deliveryLabels[$request->delivery_method] ?? $request->delivery_method;
                                    ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
                
                <hr>
                
                <div class="row">
                    <div class="col-12">
                        <h6 class="fw-semibold">Finalidade:</h6>
                        <p class="text-muted"><?= $request->purpose ?></p>
                    </div>
                </div>
                
                <?php if ($request->delivery_address): ?>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6 class="fw-semibold">Endereço de Entrega:</h6>
                        <p class="text-muted"><?= $request->delivery_address ?></p>
                    </div>
                </div>
                <?php endif; ?>
                
                <?php if ($request->rejection_reason): ?>
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="alert alert-danger">
                            <h6 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> Motivo da Rejeição:</h6>
                            <p class="mb-0"><?= $request->rejection_reason ?></p>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Timeline de Status -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0">
                    <i class="fas fa-clock text-primary me-2"></i>
                    Linha do Tempo
                </h5>
            </div>
            <div class="card-body">
                <ul class="timeline">
                    <li class="timeline-item">
                        <div class="timeline-badge bg-primary">
                            <i class="fas fa-plus"></i>
                        </div>
                        <div class="timeline-content">
                            <span class="timeline-date"><?= date('d/m/Y H:i', strtotime($request->created_at)) ?></span>
                            <h6 class="timeline-title">Solicitação Criada</h6>
                            <p class="timeline-text">Nº <?= $request->request_number ?></p>
                        </div>
                    </li>
                    
                    <?php if ($request->processed_at): ?>
                    <li class="timeline-item">
                        <div class="timeline-badge bg-info">
                            <i class="fas fa-cog"></i>
                        </div>
                        <div class="timeline-content">
                            <span class="timeline-date"><?= date('d/m/Y H:i', strtotime($request->processed_at)) ?></span>
                            <h6 class="timeline-title">Em Processamento</h6>
                            <p class="timeline-text">Solicitação sendo processada</p>
                        </div>
                    </li>
                    <?php endif; ?>
                    
                    <?php if ($request->ready_at): ?>
                    <li class="timeline-item">
                        <div class="timeline-badge bg-success">
                            <i class="fas fa-check"></i>
                        </div>
                        <div class="timeline-content">
                            <span class="timeline-date"><?= date('d/m/Y H:i', strtotime($request->ready_at)) ?></span>
                            <h6 class="timeline-title">Pronto</h6>
                            <p class="timeline-text">Documento disponível</p>
                        </div>
                    </li>
                    <?php endif; ?>
                    
                    <?php if ($request->delivered_at): ?>
                    <li class="timeline-item">
                        <div class="timeline-badge bg-secondary">
                            <i class="fas fa-check-double"></i>
                        </div>
                        <div class="timeline-content">
                            <span class="timeline-date"><?= date('d/m/Y H:i', strtotime($request->delivered_at)) ?></span>
                            <h6 class="timeline-title">Entregue</h6>
                            <p class="timeline-text">Documento entregue</p>
                        </div>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
        
        <!-- Ações -->
        <div class="card border-0 shadow-sm">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0">
                    <i class="fas fa-tasks text-primary me-2"></i>
                    Ações
                </h5>
            </div>
            <div class="card-body">
                <?php if ($request->status == 'pending' && $request->fee_amount > 0): ?>
                    <a href="#" class="btn btn-warning w-100 mb-2" data-bs-toggle="modal" data-bs-target="#paymentModal">
                        <i class="fas fa-credit-card me-2"></i> Efetuar Pagamento
                    </a>
                <?php endif; ?>
                
                <?php if ($request->status == 'pending'): ?>
                    <a href="<?= site_url('students/documents/request/cancel/' . $request->id) ?>" 
                       class="btn btn-outline-danger w-100"
                       onclick="return confirm('Tem certeza que deseja cancelar esta solicitação?')">
                        <i class="fas fa-times me-2"></i> Cancelar Solicitação
                    </a>
                <?php endif; ?>
                
                <a href="<?= site_url('students/documents/requests') ?>" class="btn btn-outline-secondary w-100 mt-2">
                    <i class="fas fa-arrow-left me-2"></i> Voltar
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Pagamento -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('students/documents/payment/' . $request->id) ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-credit-card text-primary me-2"></i>
                        Pagamento
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <h6 class="alert-heading">Detalhes do Pagamento:</h6>
                        <p class="mb-0">
                            <strong>Solicitação:</strong> <?= $request->request_number ?><br>
                            <strong>Valor:</strong> <?= number_format($request->fee_amount, 2, ',', '.') ?> Kz
                        </p>
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_method" class="form-label fw-semibold">Método de Pagamento</label>
                        <select class="form-select" id="payment_method" name="payment_method" required>
                            <option value="">Selecione...</option>
                            <option value="dinheiro">Dinheiro (na Secretaria)</option>
                            <option value="transferencia">Transferência Bancária</option>
                            <option value="multicaixa">Multicaixa</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_reference" class="form-label fw-semibold">Referência (opcional)</label>
                        <input type="text" class="form-control" id="payment_reference" name="payment_reference" 
                               placeholder="Nº do comprovante">
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Confirmar Pagamento</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding-left: 30px;
}
.timeline-item {
    position: relative;
    padding-bottom: 20px;
}
.timeline-badge {
    position: absolute;
    left: -30px;
    top: 0;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
}
.timeline-content {
    padding-left: 10px;
}
.timeline-date {
    font-size: 0.8rem;
    color: #6c757d;
}
.timeline-title {
    margin: 0;
    font-size: 1rem;
}
.timeline-text {
    margin: 0;
    font-size: 0.9rem;
    color: #6c757d;
}
</style>

<?= $this->endSection() ?>