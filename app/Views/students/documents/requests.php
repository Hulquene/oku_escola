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
                        <li class="breadcrumb-item active text-white" aria-current="page">Solicitações</li>
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

<?php if (session()->has('error')): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i> <?= session('error') ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (session()->has('errors')): ?>
    <div class="alert alert-danger alert-dismissible fade show shadow-sm" role="alert">
        <i class="fas fa-exclamation-circle me-2"></i> 
        <strong>Por favor, corrija os seguintes erros:</strong>
        <ul class="mb-0 mt-2">
            <?php foreach (session('errors') as $error): ?>
                <li><?= $error ?></li>
            <?php endforeach; ?>
        </ul>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Nova Solicitação -->
<div class="card border-0 shadow-sm mb-4">
    <div class="card-header bg-white py-3">
        <h5 class="card-title mb-0">
            <i class="fas fa-file-signature text-primary me-2"></i>
            Solicitar Documento
        </h5>
    </div>
    <div class="card-body">
        <form action="<?= site_url('students/documents/request') ?>" method="post">
            <?= csrf_field() ?>
            
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="document_code" class="form-label fw-semibold">Tipo de Documento <span class="text-danger">*</span></label>
                    <select class="form-select" id="document_code" name="document_code" required>
                        <option value="">Selecione...</option>
                        <?php foreach ($requestableDocs as $doc): ?>
                            <option value="<?= $doc->document_code ?>" 
                                    data-fee="<?= $doc->fee_amount ?>"
                                    data-days="<?= $doc->processing_days ?>"
                                    <?= old('document_code') == $doc->document_code ? 'selected' : '' ?>>
                                <?= $doc->document_name ?>
                                <?php if ($doc->fee_amount > 0): ?>
                                    (<?= number_format($doc->fee_amount, 2, ',', '.') ?> Kz)
                                <?php endif; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-2">
                    <label for="quantity" class="form-label fw-semibold">Quantidade</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" 
                           value="<?= old('quantity', 1) ?>" min="1" max="10">
                </div>
                <div class="col-md-3">
                    <label for="format" class="form-label fw-semibold">Formato <span class="text-danger">*</span></label>
                    <select class="form-select" id="format" name="format" required>
                        <option value="digital" <?= old('format') == 'digital' ? 'selected' : '' ?>>Digital (PDF)</option>
                        <option value="impresso" <?= old('format') == 'impresso' ? 'selected' : '' ?>>Impresso</option>
                        <option value="ambos" <?= old('format') == 'ambos' ? 'selected' : '' ?>>Digital + Impresso</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="delivery_method" class="form-label fw-semibold">Entrega <span class="text-danger">*</span></label>
                    <select class="form-select" id="delivery_method" name="delivery_method" required>
                        <option value="retirar" <?= old('delivery_method') == 'retirar' ? 'selected' : '' ?>>Retirar na Escola</option>
                        <option value="email" <?= old('delivery_method') == 'email' ? 'selected' : '' ?>>Email</option>
                        <option value="correio" <?= old('delivery_method') == 'correio' ? 'selected' : '' ?>>Correio</option>
                    </select>
                </div>
            </div>
            
            <div class="row g-3 mt-2">
                <div class="col-md-12">
                    <label for="purpose" class="form-label fw-semibold">Finalidade da Solicitação <span class="text-danger">*</span></label>
                    <textarea class="form-control" id="purpose" name="purpose" rows="2" 
                              placeholder="Ex: Para apresentar no local de trabalho, para matrícula, etc." 
                              required><?= old('purpose') ?></textarea>
                    <small class="text-muted">Mínimo de 5 caracteres</small>
                </div>
            </div>
            
            <div class="row g-3 mt-2" id="address-field" style="<?= old('delivery_method') == 'correio' ? 'display: block;' : 'display: none;' ?>">
                <div class="col-md-12">
                    <label for="delivery_address" class="form-label fw-semibold">Endereço para Entrega</label>
                    <textarea class="form-control" id="delivery_address" name="delivery_address" rows="2" 
                              placeholder="Endereço completo"><?= old('delivery_address') ?></textarea>
                </div>
            </div>
            
            <div class="row mt-3">
                <div class="col-md-12">
                    <div class="alert alert-info" id="fee-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <span>Selecione um documento para ver as taxas aplicáveis.</span>
                    </div>
                </div>
            </div>
            
            <div class="text-end mt-3">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane me-1"></i> Enviar Solicitação
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Lista de Solicitações -->
<div class="card border-0 shadow-sm">
    <div class="card-header bg-white py-3">
        <h5 class="card-title mb-0">
            <i class="fas fa-list text-primary me-2"></i>
            Minhas Solicitações
        </h5>
    </div>
    <div class="card-body">
        <?php if (!empty($requests)): ?>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Nº Solicitação</th>
                            <th>Data</th>
                            <th>Tipo</th>
                            <th>Formato</th>
                            <th>Valor</th>
                            <th>Pagamento</th>
                            <th>Status</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($requests as $req): ?>
                            <tr>
                                <td><strong><?= $req->request_number ?></strong></td>
                                <td><?= date('d/m/Y', strtotime($req->created_at)) ?></td>
                                <td><?= $req->document_type ?></td>
                                <td>
                                    <?php
                                    $formatLabels = [
                                        'digital' => 'Digital',
                                        'impresso' => 'Impresso',
                                        'ambos' => 'Digital + Impresso'
                                    ];
                                    echo $formatLabels[$req->format] ?? $req->format;
                                    ?>
                                </td>
                                <td class="fw-bold"><?= number_format($req->fee_amount, 2, ',', '.') ?> Kz</td>
                                <td>
                                    <?php if ($req->payment_status == 'paid'): ?>
                                        <span class="badge bg-success">Pago</span>
                                    <?php elseif ($req->payment_status == 'pending'): ?>
                                        <span class="badge bg-warning">Pendente</span>
                                    <?php else: ?>
                                        <span class="badge bg-secondary">Isento</span>
                                    <?php endif; ?>
                                </td>
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
                                    $status = $statusLabels[$req->status] ?? ['bg-secondary', $req->status];
                                    ?>
                                    <span class="badge <?= $status[0] ?>"><?= $status[1] ?></span>
                                </td>
                                <td class="text-center">
                                    <a href="<?= site_url('students/documents/request/' . $req->id) ?>" 
                                       class="btn btn-sm btn-outline-primary" title="Ver Detalhes">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <?php if ($req->status == 'pending'): ?>
                                        <a href="<?= site_url('students/documents/request/cancel/' . $req->id) ?>" 
                                           class="btn btn-sm btn-outline-danger" 
                                           onclick="return confirm('Tem certeza que deseja cancelar esta solicitação?')"
                                           title="Cancelar">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center py-4">
                <i class="fas fa-info-circle fa-3x mb-3"></i>
                <h5>Nenhuma solicitação encontrada</h5>
                <p>Utilize o formulário acima para solicitar documentos.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
document.getElementById('delivery_method').addEventListener('change', function() {
    const addressField = document.getElementById('address-field');
    const addressInput = document.getElementById('delivery_address');
    
    if (this.value === 'correio') {
        addressField.style.display = 'block';
        addressInput.setAttribute('required', 'required');
    } else {
        addressField.style.display = 'none';
        addressInput.removeAttribute('required');
    }
});

document.getElementById('document_code').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const fee = parseFloat(selected.dataset.fee || 0);
    const days = selected.dataset.days || 3;
    
    const feeInfo = document.getElementById('fee-info');
    if (fee > 0) {
        feeInfo.innerHTML = `
            <i class="fas fa-info-circle me-2"></i>
            <strong>Taxa:</strong> ${fee.toFixed(2).replace('.', ',')} Kz | 
            <strong>Prazo:</strong> ${days} dias úteis
        `;
        feeInfo.className = 'alert alert-warning';
    } else {
        feeInfo.innerHTML = `
            <i class="fas fa-info-circle me-2"></i>
            Documento gratuito. Prazo: ${days} dias úteis.
        `;
        feeInfo.className = 'alert alert-info';
    }
});

// Inicializar com old values
document.addEventListener('DOMContentLoaded', function() {
    const deliveryMethod = '<?= old('delivery_method') ?>';
    if (deliveryMethod === 'correio') {
        document.getElementById('address-field').style.display = 'block';
    }
    
    const docCode = '<?= old('document_code') ?>';
    if (docCode) {
        const select = document.getElementById('document_code');
        const event = new Event('change');
        select.dispatchEvent(event);
    }
});
</script>

<?= $this->endSection() ?>