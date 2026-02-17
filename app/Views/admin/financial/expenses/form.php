<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <h1><?= $title ?></h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/financial/expenses') ?>">Despesas</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $title ?></li>
        </ol>
    </nav>
</div>

<!-- Form -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-<?= $expense ? 'edit' : 'plus-circle' ?>"></i> <?= $title ?>
    </div>
    <div class="card-body">
        <form action="<?= site_url('admin/financial/expenses/save') ?>" method="post">
            <?= csrf_field() ?>
            
            <?php if ($expense): ?>
                <input type="hidden" name="id" value="<?= $expense->id ?>">
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="expense_number" class="form-label">Nº Despesa</label>
                        <input type="text" class="form-control" id="expense_number" name="expense_number" 
                               value="<?= old('expense_number', $expense->expense_number ?? $nextNumber) ?>" readonly>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="expense_date" class="form-label">Data <span class="text-danger">*</span></label>
                        <input type="date" class="form-control" id="expense_date" name="expense_date" 
                               value="<?= old('expense_date', $expense->expense_date ?? date('Y-m-d')) ?>" required>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="expense_category_id" class="form-label">Categoria <span class="text-danger">*</span></label>
                        <select class="form-select" id="expense_category_id" name="expense_category_id" required>
                            <option value="">Selecione...</option>
                            <?php if (!empty($categories)): ?>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat->id ?>" <?= old('expense_category_id', $expense->expense_category_id ?? '') == $cat->id ? 'selected' : '' ?>>
                                        <?= $cat->category_name ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="mb-3">
                        <label for="amount" class="form-label">Valor <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="amount" name="amount" 
                                   step="0.01" min="0.01" 
                                   value="<?= old('amount', $expense->amount ?? '') ?>" required>
                            <span class="input-group-text">Kz</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="description" class="form-label">Descrição <span class="text-danger">*</span></label>
                <textarea class="form-control" id="description" name="description" rows="3" required><?= old('description', $expense->description ?? '') ?></textarea>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="supplier_name" class="form-label">Fornecedor</label>
                        <input type="text" class="form-control" id="supplier_name" name="supplier_name" 
                               value="<?= old('supplier_name', $expense->supplier_name ?? '') ?>">
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="supplier_nif" class="form-label">NIF do Fornecedor</label>
                        <input type="text" class="form-control" id="supplier_nif" name="supplier_nif" 
                               value="<?= old('supplier_nif', $expense->supplier_nif ?? '') ?>">
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="invoice_reference" class="form-label">Referência da Fatura</label>
                        <input type="text" class="form-control" id="invoice_reference" name="invoice_reference" 
                               value="<?= old('invoice_reference', $expense->invoice_reference ?? '') ?>">
                    </div>
                </div>
            </div>
            
            <div class="row">
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="payment_method" class="form-label">Método de Pagamento</label>
                        <select class="form-select" id="payment_method" name="payment_method">
                            <option value="">Selecione...</option>
                            <option value="Dinheiro" <?= old('payment_method', $expense->payment_method ?? '') == 'Dinheiro' ? 'selected' : '' ?>>Dinheiro</option>
                            <option value="Transferência" <?= old('payment_method', $expense->payment_method ?? '') == 'Transferência' ? 'selected' : '' ?>>Transferência</option>
                            <option value="Depósito" <?= old('payment_method', $expense->payment_method ?? '') == 'Depósito' ? 'selected' : '' ?>>Depósito</option>
                            <option value="Cheque" <?= old('payment_method', $expense->payment_method ?? '') == 'Cheque' ? 'selected' : '' ?>>Cheque</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="payment_status" class="form-label">Status do Pagamento</label>
                        <select class="form-select" id="payment_status" name="payment_status">
                            <option value="Pendente" <?= old('payment_status', $expense->payment_status ?? 'Pendente') == 'Pendente' ? 'selected' : '' ?>>Pendente</option>
                            <option value="Pago" <?= old('payment_status', $expense->payment_status ?? '') == 'Pago' ? 'selected' : '' ?>>Pago</option>
                            <option value="Parcial" <?= old('payment_status', $expense->payment_status ?? '') == 'Parcial' ? 'selected' : '' ?>>Parcial</option>
                        </select>
                    </div>
                </div>
                
                <div class="col-md-4">
                    <div class="mb-3">
                        <label for="approved_by" class="form-label">Aprovado por</label>
                        <select class="form-select" id="approved_by" name="approved_by">
                            <option value="">Selecione...</option>
                            <?php if (!empty($users)): ?>
                                <?php foreach ($users as $user): ?>
                                    <option value="<?= $user->id ?>" <?= old('approved_by', $expense->approved_by ?? '') == $user->id ? 'selected' : '' ?>>
                                        <?= $user->first_name ?> <?= $user->last_name ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
            </div>
            
            <div class="mb-3">
                <label for="notes" class="form-label">Observações</label>
                <textarea class="form-control" id="notes" name="notes" rows="2"><?= old('notes', $expense->notes ?? '') ?></textarea>
            </div>
            
            <hr>
            
            <div class="d-flex justify-content-between">
                <a href="<?= site_url('admin/financial/expenses') ?>" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Cancelar
                </a>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-save"></i> Salvar Despesa
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>