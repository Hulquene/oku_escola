<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#boardModal">
            <i class="fas fa-plus-circle"></i> Novo Tipo de Exame
        </button>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/exams') ?>">Exames</a></li>
            <li class="breadcrumb-item active" aria-current="page">Tipos de Exames</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Data Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-clipboard-list"></i> Lista de Tipos de Exames
    </div>
    <div class="card-body">
        <table id="boardsTable" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Código</th>
                    <th>Tipo</th>
                    <th>Peso</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($boards)): ?>
                    <?php foreach ($boards as $board): ?>
                        <tr>
                            <td><?= $board->id ?></td>
                            <td><?= $board->board_name ?></td>
                            <td><span class="badge bg-info"><?= $board->board_code ?></span></td>
                            <td><?= $board->board_type ?></td>
                            <td><?= number_format($board->weight, 1) ?></td>
                            <td>
                                <?php if ($board->is_active): ?>
                                    <span class="badge bg-success">Ativo</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inativo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info edit-board" 
                                        data-id="<?= $board->id ?>"
                                        data-name="<?= $board->board_name ?>"
                                        data-code="<?= $board->board_code ?>"
                                        data-type="<?= $board->board_type ?>"
                                        data-weight="<?= $board->weight ?>"
                                        data-active="<?= $board->is_active ?>"
                                        title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="<?= site_url('admin/exams/boards/delete/' . $board->id) ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Tem certeza que deseja eliminar este tipo de exame?')"
                                   title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="7" class="text-center">Nenhum tipo de exame encontrado</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para Adicionar/Editar Tipo de Exame -->
<div class="modal fade" id="boardModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('admin/exams/boards/save') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="boardId">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Novo Tipo de Exame</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="board_name" class="form-label">Nome <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="board_name" name="board_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="board_code" class="form-label">Código <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="board_code" name="board_code" 
                               placeholder="Ex: AC1, EX-FIN" required>
                        <small class="text-muted">Código único para identificação</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="board_type" class="form-label">Tipo <span class="text-danger">*</span></label>
                        <select class="form-select" id="board_type" name="board_type" required>
                            <option value="">Selecione...</option>
                            <option value="Normal">Normal</option>
                            <option value="Recurso">Recurso</option>
                            <option value="Especial">Especial</option>
                            <option value="Final">Final</option>
                            <option value="Admissão">Admissão</option>
                        </select>
                    </div>
                    
                    <div class="mb-3">
                        <label for="weight" class="form-label">Peso</label>
                        <input type="number" class="form-control" id="weight" name="weight" 
                               step="0.1" min="0" max="10" value="1.0">
                        <small class="text-muted">Peso para cálculo da média final</small>
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
    $('#boardsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[0, 'asc']]
    });
    
    // Edit board
    $('.edit-board').click(function() {
        $('#modalTitle').text('Editar Tipo de Exame');
        $('#boardId').val($(this).data('id'));
        $('#board_name').val($(this).data('name'));
        $('#board_code').val($(this).data('code'));
        $('#board_type').val($(this).data('type'));
        $('#weight').val($(this).data('weight'));
        $('#is_active').prop('checked', $(this).data('active') == 1);
        
        $('#boardModal').modal('show');
    });
    
    // Reset modal on new
    $('#boardModal').on('hidden.bs.modal', function() {
        if (!$('#boardId').val()) {
            $(this).find('form')[0].reset();
            $('#weight').val('1.0');
            $('#is_active').prop('checked', true);
        }
    });
    
    // New board button
    $('[data-bs-target="#boardModal"]').click(function() {
        $('#modalTitle').text('Novo Tipo de Exame');
        $('#boardId').val('');
        $(this).find('form')[0].reset();
        $('#weight').val('1.0');
        $('#is_active').prop('checked', true);
    });
});
</script>
<?= $this->endSection() ?>