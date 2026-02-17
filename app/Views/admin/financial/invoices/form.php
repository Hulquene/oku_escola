<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/financial/invoices') ?>">Faturas</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $title ?></li>
        </ol>
    </nav>
</div>

<!-- Form -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-<?= $invoice ? 'edit' : 'plus-circle' ?>"></i> <?= $title ?>
    </div>
    <div class="card-body">
        <form id="invoiceForm" method="post">
            <?= csrf_field() ?>
            <input type="hidden" name="items" id="itemsInput">
            <?php if ($invoice): ?>
                <input type="hidden" name="id" value="<?= $invoice->id ?>">
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="invoice_number" class="form-label">Nº Fatura</label>
                        <input type="text" class="form-control" id="invoice_number" name="invoice_number" 
                               value="<?= old('invoice_number', $invoice->invoice_number ?? $nextNumber) ?>" readonly>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="invoice_date" class="form-label">Data da Fatura <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="invoice_date" name="invoice_date" 
                               value="<?= old('invoice_date', $invoice->invoice_date ?? date('Y-m-d')) ?>" required>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="due_date" class="form-label">Data de Vencimento <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="due_date" name="due_date" 
                               value="<?= old('due_date', $invoice->due_date ?? date('Y-m-d', strtotime('+30 days'))) ?>" required>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="student_id" class="form-label">Aluno</label>
                        <select class="form-select" id="student_id" name="student_id">
                            <option value="">Selecione um aluno...</option>
                            <?php if (!empty($students)): ?>
                                <?php foreach ($students as $student): ?>
                                    <option value="<?= $student->id ?>" <?= old('student_id', $invoice->student_id ?? '') == $student->id ? 'selected' : '' ?>>
                                        <?= $student->first_name ?> <?= $student->last_name ?> (<?= $student->student_number ?>)
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="guardian_id" class="form-label">Encarregado</label>
                        <select class="form-select" id="guardian_id" name="guardian_id">
                            <option value="">Selecione um encarregado...</option>
                            <?php if (!empty($guardians)): ?>
                                <?php foreach ($guardians as $guardian): ?>
                                    <option value="<?= $guardian->id ?>" <?= old('guardian_id', $invoice->guardian_id ?? '') == $guardian->id ? 'selected' : '' ?>>
                                        <?= $guardian->full_name ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="Rascunho" <?= old('status', $invoice->status ?? '') == 'Rascunho' ? 'selected' : '' ?>>Rascunho</option>
                            <option value="Emitida" <?= old('status', $invoice->status ?? '') == 'Emitida' ? 'selected' : '' ?>>Emitida</option>
                            <option value="Paga" <?= old('status', $invoice->status ?? '') == 'Paga' ? 'selected' : '' ?>>Paga</option>
                            <option value="Parcial" <?= old('status', $invoice->status ?? '') == 'Parcial' ? 'selected' : '' ?>>Parcial</option>
                            <option value="Cancelada" <?= old('status', $invoice->status ?? '') == 'Cancelada' ? 'selected' : '' ?>>Cancelada</option>
                        </select>
                    </div>
                </div>
            </div>
            
            <!-- Items Table -->
            <h5 class="mt-4 mb-3">Itens da Fatura</h5>
            <div class="table-responsive">
                <table class="table table-bordered" id="itemsTable">
                    <thead class="table-light">
                        <tr>
                            <th>Descrição</th>
                            <th>Tipo</th>
                            <th>Quantidade</th>
                            <th>Preço Unit.</th>
                            <th>Taxa (%)</th>
                            <th>Desconto (%)</th>
                            <th>Total</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody id="itemsBody">
                        <!-- Items will be added here dynamically -->
                    </tbody>
                    <tfoot>
                        <tr>
                            <td colspan="6" class="text-end"><strong>Subtotal:</strong></td>
                            <td class="text-end" id="subtotal">0.00 Kz</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="6" class="text-end"><strong>IVA Total:</strong></td>
                            <td class="text-end" id="taxTotal">0.00 Kz</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="6" class="text-end"><strong>Desconto Total:</strong></td>
                            <td class="text-end" id="discountTotal">0.00 Kz</td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="6" class="text-end"><strong>Total:</strong></td>
                            <td class="text-end fw-bold" id="grandTotal">0.00 Kz</td>
                            <td></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            
            <!-- Add Item Button -->
            <button type="button" class="btn btn-success mb-3" onclick="addItem()">
                <i class="fas fa-plus-circle"></i> Adicionar Item
            </button>
            
            <!-- Notes -->
            <div class="mb-3">
                <label for="notes" class="form-label">Observações</label>
                <textarea class="form-control" id="notes" name="notes" rows="3"><?= old('notes', $invoice->notes ?? '') ?></textarea>
            </div>
            
            <hr>
            
            <div class="d-flex justify-content-between">
                <a href="<?= site_url('admin/financial/invoices') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Salvar Fatura
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Item Template -->
<template id="itemTemplate">
    <tr>
        <td>
            <input type="text" class="form-control item-description" placeholder="Descrição do item" required>
        </td>
        <td>
            <select class="form-select item-fee-type">
                <option value="">-- Tipo --</option>
                <?php if (!empty($feeTypes)): ?>
                    <?php foreach ($feeTypes as $type): ?>
                        <option value="<?= $type->id ?>"><?= $type->type_name ?></option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </td>
        <td>
            <input type="number" class="form-control item-quantity" value="1" min="1" step="1" required>
        </td>
        <td>
            <input type="number" class="form-control item-price" value="0" min="0" step="0.01" required>
        </td>
        <td>
            <input type="number" class="form-control item-tax" value="0" min="0" max="100" step="0.1">
        </td>
        <td>
            <input type="number" class="form-control item-discount" value="0" min="0" max="100" step="0.1">
        </td>
        <td class="text-end item-total">0.00 Kz</td>
        <td>
            <button type="button" class="btn btn-sm btn-danger" onclick="removeItem(this)">
                <i class="fas fa-trash"></i>
            </button>
        </td>
    </tr>
</template>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
let items = [];

<?php if ($invoice && !empty($invoice->items)): ?>
    // Load existing items
    items = <?= json_encode($invoice->items) ?>;
<?php endif; ?>

function addItem(data = {}) {
    const template = document.getElementById('itemTemplate');
    const clone = template.content.cloneNode(true);
    const row = clone.querySelector('tr');
    
    const tbody = document.getElementById('itemsBody');
    tbody.appendChild(clone);
    
    if (data.description) {
        row.querySelector('.item-description').value = data.description;
    }
    if (data.fee_type_id) {
        row.querySelector('.item-fee-type').value = data.fee_type_id;
    }
    if (data.quantity) {
        row.querySelector('.item-quantity').value = data.quantity;
    }
    if (data.unit_price) {
        row.querySelector('.item-price').value = data.unit_price;
    }
    if (data.tax_rate) {
        row.querySelector('.item-tax').value = data.tax_rate;
    }
    if (data.discount_rate) {
        row.querySelector('.item-discount').value = data.discount_rate;
    }
    
    attachItemEvents(row);
    calculateRowTotal(row);
}

function attachItemEvents(row) {
    const inputs = row.querySelectorAll('input, select');
    inputs.forEach(input => {
        input.addEventListener('change', () => calculateRowTotal(row));
        input.addEventListener('keyup', () => calculateRowTotal(row));
    });
}

function calculateRowTotal(row) {
    const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
    const price = parseFloat(row.querySelector('.item-price').value) || 0;
    const tax = parseFloat(row.querySelector('.item-tax').value) || 0;
    const discount = parseFloat(row.querySelector('.item-discount').value) || 0;
    
    const subtotal = quantity * price;
    const taxAmount = subtotal * (tax / 100);
    const discountAmount = subtotal * (discount / 100);
    const total = subtotal + taxAmount - discountAmount;
    
    row.querySelector('.item-total').textContent = total.toFixed(2).replace('.', ',') + ' Kz';
    
    calculateTotals();
}

function calculateTotals() {
    let subtotal = 0;
    let taxTotal = 0;
    let discountTotal = 0;
    
    document.querySelectorAll('#itemsBody tr').forEach(row => {
        const quantity = parseFloat(row.querySelector('.item-quantity').value) || 0;
        const price = parseFloat(row.querySelector('.item-price').value) || 0;
        const tax = parseFloat(row.querySelector('.item-tax').value) || 0;
        const discount = parseFloat(row.querySelector('.item-discount').value) || 0;
        
        const rowSubtotal = quantity * price;
        subtotal += rowSubtotal;
        taxTotal += rowSubtotal * (tax / 100);
        discountTotal += rowSubtotal * (discount / 100);
    });
    
    const grandTotal = subtotal + taxTotal - discountTotal;
    
    document.getElementById('subtotal').textContent = subtotal.toFixed(2).replace('.', ',') + ' Kz';
    document.getElementById('taxTotal').textContent = taxTotal.toFixed(2).replace('.', ',') + ' Kz';
    document.getElementById('discountTotal').textContent = discountTotal.toFixed(2).replace('.', ',') + ' Kz';
    document.getElementById('grandTotal').textContent = grandTotal.toFixed(2).replace('.', ',') + ' Kz';
}

function removeItem(button) {
    if (confirm('Remover este item?')) {
        button.closest('tr').remove();
        calculateTotals();
    }
}

// Load existing items
window.addEventListener('load', function() {
    items.forEach(item => addItem(item));
});

// Form submission
document.getElementById('invoiceForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const items = [];
    document.querySelectorAll('#itemsBody tr').forEach(row => {
        items.push({
            description: row.querySelector('.item-description').value,
            fee_type_id: row.querySelector('.item-fee-type').value || null,
            quantity: parseFloat(row.querySelector('.item-quantity').value) || 1,
            unit_price: parseFloat(row.querySelector('.item-price').value) || 0,
            tax_rate: parseFloat(row.querySelector('.item-tax').value) || 0,
            discount_rate: parseFloat(row.querySelector('.item-discount').value) || 0
        });
    });
    
    document.getElementById('itemsInput').value = JSON.stringify(items);
    this.submit();
});
</script>
<?= $this->endSection() ?>