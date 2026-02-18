<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Início</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/documents') ?>">Central de Documentos</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/documents/requests') ?>">Solicitações</a></li>
            <li class="breadcrumb-item active" aria-current="page">#<?= $request->request_number ?></li>
        </ol>
    </nav>
</div>

<?php if (session()->has('success')): ?>
    <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-check-circle me-2"></i> <?= session('success') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Informações da Solicitação -->
<div class="row">
    <div class="col-md-8">
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0">
                    <i class="fas fa-info-circle text-primary me-2"></i>
                    Detalhes da Solicitação
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
                                <th>Data Solicitação:</th>
                                <td><?= date('d/m/Y H:i', strtotime($request->created_at)) ?></td>
                            </tr>
                            <tr>
                                <th>Solicitante:</th>
                                <td>
                                    <span class="fw-semibold"><?= $request->user_fullname ?></span><br>
                                    <small class="text-muted"><?= $request->email ?></small>
                                </td>
                            </tr>
                            <tr>
                                <th>Tipo:</th>
                                <td><?= $request->user_type == 'student' ? 'Aluno' : 'Professor' ?></td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="table table-borderless">
                            <tr>
                                <th width="40%">Documento:</th>
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
                            <tr>
                                <th>Entrega:</th>
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
                
                <?php if ($request->notes): ?>
                <div class="row mt-3">
                    <div class="col-12">
                        <h6 class="fw-semibold">Observações:</h6>
                        <p class="text-muted"><?= $request->notes ?></p>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Documentos Gerados -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3 d-flex justify-content-between align-items-center">
                <h5 class="card-title mb-0">
                    <i class="fas fa-file-pdf text-primary me-2"></i>
                    Documentos Gerados
                </h5>
                <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#uploadDocModal">
                    <i class="fas fa-upload me-1"></i> Adicionar Documento
                </button>
            </div>
            <div class="card-body">
                <?php if (!empty($generatedDocs)): ?>
                    <div class="list-group">
                        <?php foreach ($generatedDocs as $doc): ?>
                            <div class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
                                <div>
                                    <i class="fas fa-file-pdf text-danger me-2"></i>
                                    <?= $doc->document_name ?>
                                    <br>
                                    <small class="text-muted">
                                        Gerado em: <?= date('d/m/Y H:i', strtotime($doc->generated_at)) ?> | 
                                        Tamanho: <?= round($doc->document_size / 1024, 1) ?> KB |
                                        Downloads: <?= $doc->download_count ?? 0 ?>
                                    </small>
                                </div>
                                <div class="btn-group">
                                    <a href="<?= site_url('admin/documents/download-generated/' . $doc->id) ?>" 
                                       class="btn btn-sm btn-outline-success" title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <p class="text-muted text-center py-3">Nenhum documento gerado ainda.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="col-md-4">
        <!-- Status e Ações -->
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-header bg-white py-3">
                <h5 class="card-title mb-0">
                    <i class="fas fa-tasks text-primary me-2"></i>
                    Status da Solicitação
                </h5>
            </div>
            <div class="card-body">
                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-semibold">Status:</span>
                        <?php
                        $statusLabels = [
                            'pending' => ['warning', 'Pendente'],
                            'processing' => ['info', 'Processando'],
                            'ready' => ['success', 'Pronto'],
                            'delivered' => ['secondary', 'Entregue'],
                            'cancelled' => ['danger', 'Cancelado'],
                            'rejected' => ['danger', 'Rejeitado']
                        ];
                        $status = $statusLabels[$request->status] ?? ['secondary', $request->status];
                        ?>
                        <span class="badge bg-<?= $status[0] ?> p-2"><?= $status[1] ?></span>
                    </div>
                    
                    <div class="d-flex justify-content-between mb-2">
                        <span class="fw-semibold">Pagamento:</span>
                        <?php if ($request->payment_status == 'paid'): ?>
                            <span class="badge bg-success">Pago</span>
                        <?php elseif ($request->payment_status == 'pending' && $request->fee_amount > 0): ?>
                            <span class="badge bg-warning">Pendente</span>
                        <?php else: ?>
                            <span class="badge bg-secondary">Isento</span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="d-flex justify-content-between">
                        <span class="fw-semibold">Valor:</span>
                        <span class="fw-bold"><?= number_format($request->fee_amount, 2, ',', '.') ?> Kz</span>
                    </div>
                </div>
                
                <hr>
                
                <div class="mb-4">
                    <h6 class="fw-semibold mb-3">Ações</h6>
                    
                    <?php if ($request->status == 'pending'): ?>
                        <form action="<?= site_url('admin/documents/request/process/' . $request->id) ?>" method="post" class="mb-2">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-info w-100">
                                <i class="fas fa-cog me-2"></i> Iniciar Processamento
                            </button>
                        </form>
                    <?php endif; ?>
                    
                    <?php if ($request->status == 'processing'): ?>
                        <form action="<?= site_url('admin/documents/request/ready/' . $request->id) ?>" method="post" class="mb-2">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-success w-100">
                                <i class="fas fa-check me-2"></i> Marcar como Pronto
                            </button>
                        </form>
                    <?php endif; ?>
                    
                    <?php if ($request->status == 'ready'): ?>
                        <form action="<?= site_url('admin/documents/request/deliver/' . $request->id) ?>" method="post" class="mb-2">
                            <?= csrf_field() ?>
                            <button type="submit" class="btn btn-secondary w-100">
                                <i class="fas fa-check-double me-2"></i> Registrar Entrega
                            </button>
                        </form>
                    <?php endif; ?>
                    
                    <?php if ($request->status == 'pending' && $request->payment_status == 'pending' && $request->fee_amount > 0): ?>
                        <button type="button" class="btn btn-warning w-100 mb-2" data-bs-toggle="modal" data-bs-target="#paymentModal">
                            <i class="fas fa-credit-card me-2"></i> Registrar Pagamento
                        </button>
                    <?php endif; ?>
                    
                    <?php if ($request->status == 'pending' || $request->status == 'processing'): ?>
                        <button type="button" class="btn btn-danger w-100 mb-2" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="fas fa-times me-2"></i> Rejeitar Solicitação
                        </button>
                    <?php endif; ?>
                </div>
                
                <hr>
                
                <!-- Timeline -->
                <h6 class="fw-semibold mb-3">Linha do Tempo</h6>
                <ul class="timeline">
                    <li class="timeline-item">
                        <div class="timeline-badge bg-primary">
                            <i class="fas fa-plus"></i>
                        </div>
                        <div class="timeline-content">
                            <span class="timeline-date"><?= date('d/m/Y H:i', strtotime($request->created_at)) ?></span>
                            <h6 class="timeline-title">Solicitação Criada</h6>
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
                            <p class="timeline-text">Por: <?= $request->processed_by_name ?? 'Sistema' ?></p>
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
                        </div>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Pagamento -->
<div class="modal fade" id="paymentModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('admin/documents/request/payment/' . $request->id) ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-credit-card text-warning me-2"></i>
                        Registrar Pagamento
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <strong>Solicitação:</strong> <?= $request->request_number ?><br>
                        <strong>Valor:</strong> <?= number_format($request->fee_amount, 2, ',', '.') ?> Kz
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_reference" class="form-label fw-semibold">Referência do Pagamento</label>
                        <input type="text" class="form-control" id="payment_reference" name="payment_reference" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="payment_date" class="form-label fw-semibold">Data do Pagamento</label>
                        <input type="date" class="form-control" id="payment_date" name="payment_date" 
                               value="<?= date('Y-m-d') ?>" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-warning">Confirmar Pagamento</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Rejeição -->
<div class="modal fade" id="rejectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('admin/documents/request/reject/' . $request->id) ?>" method="post">
                <?= csrf_field() ?>
                
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-times-circle text-danger me-2"></i>
                        Rejeitar Solicitação
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label fw-semibold">Motivo da Rejeição <span class="text-danger">*</span></label>
                        <textarea class="form-control" id="rejection_reason" name="rejection_reason" rows="4" required></textarea>
                        <small class="text-muted">Explique o motivo da rejeição para o solicitante</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Rejeitar Solicitação</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Upload de Documento -->
<div class="modal fade" id="uploadDocModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('admin/documents/upload-generated/' . $request->id) ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>
                
                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="fas fa-upload text-primary me-2"></i>
                        Adicionar Documento Gerado
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="document_file" class="form-label fw-semibold">Arquivo (PDF) <span class="text-danger">*</span></label>
                        <input type="file" class="form-control" id="document_file" name="document_file" accept=".pdf" required>
                        <small class="text-muted">Apenas arquivos PDF. Máximo: 10MB</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Upload</button>
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
    font-size: 0.8rem;
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
    font-size: 0.9rem;
    font-weight: 600;
}
.timeline-text {
    margin: 0;
    font-size: 0.8rem;
    color: #6c757d;
}
</style>

<?= $this->endSection() ?>