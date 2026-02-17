<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/settings') ?>">Configurações</a></li>
            <li class="breadcrumb-item active" aria-current="page">Pagamento</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Payment Settings Form -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-money-bill-wave"></i> Configurações de Pagamento
    </div>
    <div class="card-body">
        <form action="<?= site_url('admin/settings/save-payment') ?>" method="post">
            <?= csrf_field() ?>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="payment_currency" class="form-label">Moeda Padrão <span class="text-danger">*</span></label>
                        <select class="form-select" id="payment_currency" name="payment_currency" required>
                            <option value="">Selecione...</option>
                            <?php if (!empty($currencies)): ?>
                                <?php foreach ($currencies as $currency): ?>
                                    <option value="<?= $currency->id ?>" 
                                        <?= ($settings['payment_currency'] ?? '') == $currency->id ? 'selected' : '' ?>>
                                        <?= $currency->currency_name ?> (<?= $currency->currency_code ?> - <?= $currency->currency_symbol ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="payment_tax_rate" class="form-label">Taxa de Imposto Padrão (%)</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="payment_tax_rate" name="payment_tax_rate" 
                                   step="0.01" min="0" max="100" value="<?= $settings['payment_tax_rate'] ?? 0 ?>">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="payment_invoice_prefix" class="form-label">Prefixo da Fatura</label>
                        <input type="text" class="form-control" id="payment_invoice_prefix" name="payment_invoice_prefix" 
                               value="<?= $settings['payment_invoice_prefix'] ?? 'INV' ?>">
                        <small class="text-muted">Ex: INV-2024-0001</small>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="payment_receipt_prefix" class="form-label">Prefixo do Recibo</label>
                        <input type="text" class="form-control" id="payment_receipt_prefix" name="payment_receipt_prefix" 
                               value="<?= $settings['payment_receipt_prefix'] ?? 'REC' ?>">
                        <small class="text-muted">Ex: REC-2024-0001</small>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="payment_due_days" class="form-label">Dias para Vencimento</label>
                        <input type="number" class="form-control" id="payment_due_days" name="payment_due_days" 
                               min="1" value="<?= $settings['payment_due_days'] ?? 30 ?>">
                        <small class="text-muted">Número de dias após emissão</small>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="payment_late_fee" class="form-label">Multa por Atraso (%)</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="payment_late_fee" name="payment_late_fee" 
                                   step="0.1" min="0" value="<?= $settings['payment_late_fee'] ?? 0 ?>">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="payment_discount_early" class="form-label">Desconto p/ Pagamento Antecipado (%)</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="payment_discount_early" name="payment_discount_early" 
                                   step="0.1" min="0" value="<?= $settings['payment_discount_early'] ?? 0 ?>">
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card bg-light mb-3">
                <div class="card-body">
                    <h6 class="mb-3">Opções de Pagamento</h6>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="payment_enable_multiple" name="payment_enable_multiple" value="1"
                                       <?= ($settings['payment_enable_multiple'] ?? 0) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="payment_enable_multiple">
                                    Permitir pagamentos múltiplos por fatura
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="payment_enable_partial" name="payment_enable_partial" value="1"
                                       <?= ($settings['payment_enable_partial'] ?? 0) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="payment_enable_partial">
                                    Permitir pagamentos parciais
                                </label>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="form-check form-switch mb-2">
                                <input class="form-check-input" type="checkbox" id="payment_enable_fines" name="payment_enable_fines" value="1"
                                       <?= ($settings['payment_enable_fines'] ?? 0) ? 'checked' : '' ?>>
                                <label class="form-check-label" for="payment_enable_fines">
                                    Aplicar multas automaticamente
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <hr>
            
            <div class="text-end">
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Salvar Configurações de Pagamento
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>