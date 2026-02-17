<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#levelModal">
            <i class="fas fa-plus-circle"></i> Novo Nível
        </button>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/classes') ?>">Classes</a></li>
            <li class="breadcrumb-item active" aria-current="page">Níveis de Ensino</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Data Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-layer-group"></i> Lista de Níveis de Ensino
    </div>
    <div class="card-body">
        <table id="levelsTable" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nível</th>
                    <th>Código</th>
                    <th>Ciclo de Ensino</th>
                    <th>Classe</th>
                    <th>Ordem</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($levels)): ?>
                    <?php foreach ($levels as $level): ?>
                        <tr>
                            <td><?= $level->id ?></td>
                            <td><?= $level->level_name ?></td>
                            <td><span class="badge bg-info"><?= $level->level_code ?></span></td>
                            <td><?= $level->education_level ?></td>
                            <td><?= $level->grade_number ?>ª Classe</td>
                            <td><?= $level->sort_order ?></td>
                            <td>
                                <?php if ($level->is_active): ?>
                                    <span class="badge bg-success">Ativo</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inativo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info edit-level" 
                                        data-id="<?= $level->id ?>"
                                        data-name="<?= $level->level_name ?>"
                                        data-code="<?= $level->level_code ?>"
                                        data-education="<?= $level->education_level ?>"
                                        data-grade="<?= $level->grade_number ?>"
                                        data-order="<?= $level->sort_order ?>"
                                        data-active="<?= $level->is_active ?>"
                                        title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <?php if ($level->is_active): ?>
                                    <a href="<?= site_url('admin/classes/levels/delete/' . $level->id) ?>" 
                                       class="btn btn-sm btn-danger" 
                                       onclick="return confirm('Tem certeza que deseja eliminar este nível?')"
                                       title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">Nenhum nível de ensino encontrado</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para Adicionar/Editar Nível -->
<div class="modal fade" id="levelModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="<?= site_url('admin/classes/levels/save') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="levelId">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Novo Nível de Ensino</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="level_name" class="form-label">Nome do Nível <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="level_name" name="level_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="level_code" class="form-label">Código <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="level_code" name="level_code" 
                               placeholder="Ex: PRI-01" required>
                        <small class="text-muted">Código único para identificação</small>
                    </div>
                    
                    <div class="mb-3">
                        <label for="education_level" class="form-label">Ciclo de Ensino <span class="text-danger">*</span></label>
                        <select class="form-select" id="education_level" name="education_level" required>
                            <option value="">Selecione...</option>
                            <option value="Iniciação">Iniciação</option>
                            <option value="Primário">Primário</option>
                            <option value="1º Ciclo">1º Ciclo</option>
                            <option value="2º Ciclo">2º Ciclo</option>
                            <option value="Ensino Médio">Ensino Médio</option>
                        </select>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="grade_number" class="form-label">Classe <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="grade_number" name="grade_number" 
                                       min="1" max="13" required>
                                <small class="text-muted">Número da classe (1-13)</small>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="sort_order" class="form-label">Ordem</label>
                                <input type="number" class="form-control" id="sort_order" name="sort_order" value="0">
                                <small class="text-muted">Ordem de exibição</small>
                            </div>
                        </div>
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
    $('#levelsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[5, 'asc']]
    });
    
    // Edit level
    $('.edit-level').click(function() {
        $('#modalTitle').text('Editar Nível de Ensino');
        $('#levelId').val($(this).data('id'));
        $('#level_name').val($(this).data('name'));
        $('#level_code').val($(this).data('code'));
        $('#education_level').val($(this).data('education'));
        $('#grade_number').val($(this).data('grade'));
        $('#sort_order').val($(this).data('order'));
        $('#is_active').prop('checked', $(this).data('active') == 1);
        
        $('#levelModal').modal('show');
    });
    
    // Reset modal on new
    $('#levelModal').on('hidden.bs.modal', function() {
        if (!$('#levelId').val()) {
            $(this).find('form')[0].reset();
            $('#is_active').prop('checked', true);
        }
    });
    
    // New level button
    $('[data-bs-target="#levelModal"]').click(function() {
        $('#modalTitle').text('Novo Nível de Ensino');
        $('#levelId').val('');
        $(this).find('form')[0].reset();
        $('#is_active').prop('checked', true);
    });
});
</script>
<?= $this->endSection() ?>