<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#typeModal">
            <i class="fas fa-plus-circle"></i> Novo Tipo de Taxa
        </button>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/fees') ?>">Propinas</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tipos de Taxas</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Data Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-tags"></i> Lista de Tipos de Taxas
    </div>
    <div class="card-body">
        <table id="typesTable" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Código</th>
                    <th>Categoria</th>
                    <th>Recorrente</th>
                    <th>Período</th>
                    <th>Descrição</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($types)): ?>
                    <?php foreach ($types as $type): ?>
                        <tr>
                            <td><?= $type->id ?></td>
                            <td><?= $type->type_name ?></td>
                            <td><span class="badge bg-info"><?= $type->type_code ?></span></td>
                            <td><?= $type->type_category ?></td>
                            <td>
                                <?php if ($type->is_recurring): ?>
                                    <span class="badge bg-success">Sim</span>
                                <?php else: ?>
                                    <span class="badge bg-secondary">Não</span>
                                <?php endif; ?>
                            </td>
                            <td><?= $type->recurrence_period ?: '-' ?></td>
                            <td><?= $type->description ? substr($type->description, 0, 50) . '...' : '-' ?></td>
                            <td>
                                <?php if ($type->is_active): ?>
                                    <span class="badge bg-success">Ativo</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inativo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info edit-type" 
                                        data-id="<?= $type->id ?>"
                                        data-name="<?= $type->type_name ?>"
                                        data-code="<?= $type->type_code ?>"
                                        data-category="<?= $type->type_category ?>"
                                        data-recurring="<?= $type->is_recurring ?>"
                                        data-period="<?= $type->recurrence_period ?>"
                                        data-description="<?= $type->description ?>"
                                        data-active="<?= $type->is_active ?>"
                                        title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="<?= site_url('admin/fees/types/delete/' . $type->id) ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Tem certeza que deseja eliminar este tipo de taxa?')"
                                   title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">Nenhum tipo de taxa encontrado</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para Adicionar/Editar Tipo -->
<div class="modal fade" id="typeModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('admin/fees/types/save') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="typeId">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Novo Tipo de Taxa</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="type_name" class="form-label">Nome <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="type_name" name="type_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="type_code" class="form-label">Código <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="type_code" name="type_code" 
                               placeholder="Ex: FEE-MONTHLY" required>
                        <small class="text-muted">Código único para identificação</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="type_category" class="form-label">Categoria <span class="text-danger">*</span></label>
                        <select class="form-select" id="type_category" name="type_category" required>
                            <option value="">Selecione...</option>
                            <option value="Propina">Propina</option>
                            <option value="Matrícula">Matrícula</option>
                            <option value="Inscrição">Inscrição</option>
                            <option value="Taxa de Exame">Taxa de Exame</option>
                            <option value="Taxa Administrativa">Taxa Administrativa</option>
                            <option value="Multa">Multa</option>
                            <option value="Outro">Outro</option>
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" id="is_recurring" name="is_recurring" value="1">
                                    <label class="form-check-label" for="is_recurring">
                                        Taxa Recorrente
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="recurrence_period" class="form-label">Período de Recorrência</label>
                                <select class="form-select" id="recurrence_period" name="recurrence_period" disabled>
                                    <option value="">Selecione...</option>
                                    <option value="Mensal">Mensal</option>
                                    <option value="Trimestral">Trimestral</option>
                                    <option value="Semestral">Semestral</option>
                                    <option value="Anual">Anual</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="description" class="form-label">Descrição</label>
                        <textarea class="form-control" id="description" name="description" rows="3"></textarea>
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
    $('#typesTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[1, 'asc']]
    });
    
    // Toggle recurrence period
    $('#is_recurring').change(function() {
        $('#recurrence_period').prop('disabled', !this.checked);
    });
    
    // Edit type
    $('.edit-type').click(function() {
        $('#modalTitle').text('Editar Tipo de Taxa');
        $('#typeId').val($(this).data('id'));
        $('#type_name').val($(this).data('name'));
        $('#type_code').val($(this).data('code'));
        $('#type_category').val($(this).data('category'));
        
        const isRecurring = $(this).data('recurring') == 1;
        $('#is_recurring').prop('checked', isRecurring);
        $('#recurrence_period').prop('disabled', !isRecurring);
        $('#recurrence_period').val($(this).data('period'));
        
        $('#description').val($(this).data('description'));
        $('#is_active').prop('checked', $(this).data('active') == 1);
        
        $('#typeModal').modal('show');
    });
    
    // Reset modal on new
    $('#typeModal').on('hidden.bs.modal', function() {
        if (!$('#typeId').val()) {
            $(this).find('form')[0].reset();
            $('#is_recurring').prop('checked', false);
            $('#recurrence_period').prop('disabled', true);
            $('#is_active').prop('checked', true);
        }
    });
    
    // New type button
    $('[data-bs-target="#typeModal"]').click(function() {
        $('#modalTitle').text('Novo Tipo de Taxa');
        $('#typeId').val('');
        $(this).find('form')[0].reset();
        $('#is_recurring').prop('checked', false);
        $('#recurrence_period').prop('disabled', true);
        $('#is_active').prop('checked', true);
    });
});
</script>
<?= $this->endSection() ?>