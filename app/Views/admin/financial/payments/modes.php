<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modeModal">
            <i class="fas fa-plus-circle"></i> Novo Método
        </button>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/financial/payments') ?>">Pagamentos</a></li>
            <li class="breadcrumb-item active" aria-current="page">Métodos de Pagamento</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Data Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-credit-card"></i> Lista de Métodos de Pagamento
    </div>
    <div class="card-body">
        <table id="modesTable" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Código</th>
                    <th>Descrição</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($modes)): ?>
                    <?php foreach ($modes as $mode): ?>
                        <tr>
                            <td><?= $mode->id ?></td>
                            <td><?= $mode->mode_name ?></td>
                            <td><span class="badge bg-info"><?= $mode->mode_code ?></span></td>
                            <td><?= $mode->description ?: '-' ?></td>
                            <td>
                                <?php if ($mode->is_active): ?>
                                    <span class="badge bg-success">Ativo</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inativo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info edit-mode" 
                                        data-id="<?= $mode->id ?>"
                                        data-name="<?= $mode->mode_name ?>"
                                        data-code="<?= $mode->mode_code ?>"
                                        data-description="<?= $mode->description ?>"
                                        data-active="<?= $mode->is_active ?>"
                                        title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="6" class="text-center">Nenhum método de pagamento encontrado</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para Adicionar/Editar Método -->
<div class="modal fade" id="modeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('admin/financial/payments/modes') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="modeId">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Novo Método de Pagamento</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="mode_name" class="form-label">Nome <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="mode_name" name="mode_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="mode_code" class="form-label">Código <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="mode_code" name="mode_code" 
                               placeholder="Ex: CASH, TRANSFER" required>
                        <small class="text-muted">Código único para identificação</small>
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
    $('#modesTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[1, 'asc']]
    });
    
    // Edit mode
    $('.edit-mode').click(function() {
        $('#modalTitle').text('Editar Método de Pagamento');
        $('#modeId').val($(this).data('id'));
        $('#mode_name').val($(this).data('name'));
        $('#mode_code').val($(this).data('code'));
        $('#description').val($(this).data('description'));
        $('#is_active').prop('checked', $(this).data('active') == 1);
        
        $('#modeModal').modal('show');
    });
    
    // Reset modal on new
    $('#modeModal').on('hidden.bs.modal', function() {
        if (!$('#modeId').val()) {
            $(this).find('form')[0].reset();
            $('#is_active').prop('checked', true);
        }
    });
    
    // New mode button
    $('[data-bs-target="#modeModal"]').click(function() {
        $('#modalTitle').text('Novo Método de Pagamento');
        $('#modeId').val('');
        $(this).find('form')[0].reset();
        $('#is_active').prop('checked', true);
    });
});
</script>
<?= $this->endSection() ?>