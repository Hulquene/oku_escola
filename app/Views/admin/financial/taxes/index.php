<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#taxModal">
            <i class="fas fa-plus-circle"></i> Nova Taxa
        </button>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/financial') ?>">Financeiro</a></li>
            <li class="breadcrumb-item active" aria-current="page">Taxas</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Data Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-percent"></i> Lista de Taxas e Impostos
    </div>
    <div class="card-body">
        <table id="taxesTable" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Código</th>
                    <th>Taxa (%)</th>
                    <th>Descrição</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($taxes)): ?>
                    <?php foreach ($taxes as $tax): ?>
                        <tr>
                            <td><?= $tax->id ?></td>
                            <td><?= $tax->tax_name ?></td>
                            <td><span class="badge bg-info"><?= $tax->tax_code ?></span></td>
                            <td class="text-center fw-bold"><?= number_format($tax->tax_rate, 1) ?>%</td>
                            <td><?= $tax->description ?: '-' ?></td>
                            <td>
                                <div class="form-check form-switch">
                                    <input class="form-check-input toggle-status" type="checkbox" 
                                           data-id="<?= $tax->id ?>"
                                           <?= $tax->is_active ? 'checked' : '' ?>>
                                </div>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info edit-tax" 
                                        data-id="<?= $tax->id ?>"
                                        data-name="<?= $tax->tax_name ?>"
                                        data-code="<?= $tax->tax_code ?>"
                                        data-rate="<?= $tax->tax_rate ?>"
                                        data-description="<?= $tax->description ?>"
                                        data-active="<?= $tax->is_active ?>"
                                        title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">Nenhuma taxa encontrada</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para Adicionar/Editar Taxa -->
<div class="modal fade" id="taxModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('admin/financial/taxes/save') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="taxId">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Nova Taxa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="tax_name" class="form-label">Nome <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="tax_name" name="tax_name" required>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="tax_code" class="form-label">Código <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="tax_code" name="tax_code" 
                                       placeholder="Ex: IVA" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="tax_rate" class="form-label">Taxa (%) <span class="text-danger">*</span></label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="tax_rate" name="tax_rate" 
                                   step="0.1" min="0" max="100" required>
                            <span class="input-group-text">%</span>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição</label>
                        <textarea class="form-control" id="description" name="description" rows="2"></textarea>
                    </div>
                    
                    <div class="mb-3">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                            <label class="form-check-label" for="is_active">Ativo</label>
                        </div>
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
    $('#taxesTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[3, 'asc']]
    });
    
    // Toggle status
    $('.toggle-status').change(function() {
        const id = $(this).data('id');
        const isActive = $(this).is(':checked') ? 1 : 0;
        
        fetch('<?= site_url('admin/financial/taxes/toggle-status') ?>/' + id, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Do nothing
            } else {
                alert('Erro ao alterar status');
                $(this).prop('checked', !isActive);
            }
        });
    });
    
    // Edit tax
    $('.edit-tax').click(function() {
        $('#modalTitle').text('Editar Taxa');
        $('#taxId').val($(this).data('id'));
        $('#tax_name').val($(this).data('name'));
        $('#tax_code').val($(this).data('code'));
        $('#tax_rate').val($(this).data('rate'));
        $('#description').val($(this).data('description'));
        $('#is_active').prop('checked', $(this).data('active') == 1);
        
        $('#taxModal').modal('show');
    });
    
    // Reset modal on new
    $('#taxModal').on('hidden.bs.modal', function() {
        if (!$('#taxId').val()) {
            $(this).find('form')[0].reset();
            $('#is_active').prop('checked', true);
        }
    });
    
    // New tax button
    $('[data-bs-target="#taxModal"]').click(function() {
        $('#modalTitle').text('Nova Taxa');
        $('#taxId').val('');
        $(this).find('form')[0].reset();
        $('#is_active').prop('checked', true);
    });
});
</script>
<?= $this->endSection() ?>