<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#currencyModal">
            <i class="fas fa-plus-circle"></i> Nova Moeda
        </button>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/financial') ?>">Financeiro</a></li>
            <li class="breadcrumb-item active" aria-current="page">Moedas</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Data Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-coins"></i> Lista de Moedas
    </div>
    <div class="card-body">
        <table id="currenciesTable" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Código</th>
                    <th>Símbolo</th>
                    <th>Padrão</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($currencies)): ?>
                    <?php foreach ($currencies as $currency): ?>
                        <tr>
                            <td><?= $currency->id ?></td>
                            <td><?= $currency->currency_name ?></td>
                            <td><span class="badge bg-info"><?= $currency->currency_code ?></span></td>
                            <td><span class="fw-bold"><?= $currency->currency_symbol ?></span></td>
                            <td>
                                <?php if ($currency->is_default): ?>
                                    <span class="badge bg-success">Padrão</span>
                                <?php else: ?>
                                    <a href="<?= site_url('admin/financial/currencies/set-default/' . $currency->id) ?>" 
                                       class="btn btn-sm btn-outline-primary"
                                       onclick="return confirm('Definir esta moeda como padrão?')">
                                        Definir Padrão
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info edit-currency" 
                                        data-id="<?= $currency->id ?>"
                                        data-name="<?= $currency->currency_name ?>"
                                        data-code="<?= $currency->currency_code ?>"
                                        data-symbol="<?= $currency->currency_symbol ?>"
                                        data-default="<?= $currency->is_default ?>"
                                        title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <?php if (!$currency->is_default): ?>
                                    <a href="<?= site_url('admin/financial/currencies/delete/' . $currency->id) ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Tem certeza que deseja eliminar esta moeda?')"
                                       title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">Nenhuma moeda encontrada</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para Adicionar/Editar Moeda -->
<div class="modal fade" id="currencyModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('admin/financial/currencies/save') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="currencyId">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Nova Moeda</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="currency_name" class="form-label">Nome <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="currency_name" name="currency_name" required>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="currency_code" class="form-label">Código <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="currency_code" name="currency_code" 
                                       maxlength="3" placeholder="Ex: AOA" required>
                                <small class="text-muted">Código ISO (3 letras)</small>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="currency_symbol" class="form-label">Símbolo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="currency_symbol" name="currency_symbol" 
                                       placeholder="Ex: Kz" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_default" name="is_default" value="1">
                            <label class="form-check-label" for="is_default">
                                Definir como moeda padrão
                            </label>
                        </div>
                        <small class="text-muted">Apenas uma moeda pode ser padrão</small>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-primary">Salvar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    $('#currenciesTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[0, 'asc']]
    });
    
    // Edit currency
    $('.edit-currency').click(function() {
        $('#modalTitle').text('Editar Moeda');
        $('#currencyId').val($(this).data('id'));
        $('#currency_name').val($(this).data('name'));
        $('#currency_code').val($(this).data('code'));
        $('#currency_symbol').val($(this).data('symbol'));
        $('#is_default').prop('checked', $(this).data('default') == 1);
        
        $('#currencyModal').modal('show');
    });
    
    // Reset modal on new
    $('#currencyModal').on('hidden.bs.modal', function() {
        if (!$('#currencyId').val()) {
            $(this).find('form')[0].reset();
            $('#is_default').prop('checked', false);
        }
    });
    
    // New currency button
    $('[data-bs-target="#currencyModal"]').click(function() {
        $('#modalTitle').text('Nova Moeda');
        $('#currencyId').val('');
        $(this).find('form')[0].reset();
        $('#is_default').prop('checked', false);
    });
});
</script>
<?= $this->endSection() ?>