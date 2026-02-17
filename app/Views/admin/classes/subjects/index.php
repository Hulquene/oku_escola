<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#subjectModal">
            <i class="fas fa-plus-circle"></i> Nova Disciplina
        </button>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/classes') ?>">Classes</a></li>
            <li class="breadcrumb-item active" aria-current="page">Disciplinas</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Data Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-book"></i> Lista de Disciplinas
    </div>
    <div class="card-body">
        <table id="subjectsTable" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Disciplina</th>
                    <th>Código</th>
                    <th>Tipo</th>
                    <th>Carga Horária</th>
                    <th>Nota Mínima</th>
                    <th>Nota Máxima</th>
                    <th>Aprovação</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($subjects)): ?>
                    <?php foreach ($subjects as $subject): ?>
                        <tr>
                            <td><?= $subject->id ?></td>
                            <td><?= $subject->discipline_name ?></td>
                            <td><span class="badge bg-info"><?= $subject->discipline_code ?></span></td>
                            <td><?= $subject->discipline_type ?></td>
                            <td><?= $subject->workload_hours ?: '-' ?> h</td>
                            <td><?= $subject->min_grade ?></td>
                            <td><?= $subject->max_grade ?></td>
                            <td><?= $subject->approval_grade ?></td>
                            <td>
                                <?php if ($subject->is_active): ?>
                                    <span class="badge bg-success">Ativa</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inativa</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info edit-subject" 
                                        data-id="<?= $subject->id ?>"
                                        data-name="<?= $subject->discipline_name ?>"
                                        data-code="<?= $subject->discipline_code ?>"
                                        data-type="<?= $subject->discipline_type ?>"
                                        data-hours="<?= $subject->workload_hours ?>"
                                        data-min="<?= $subject->min_grade ?>"
                                        data-max="<?= $subject->max_grade ?>"
                                        data-approval="<?= $subject->approval_grade ?>"
                                        data-description="<?= $subject->description ?>"
                                        data-active="<?= $subject->is_active ?>"
                                        title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="<?= site_url('admin/classes/subjects/delete/' . $subject->id) ?>" 
                                   class="btn btn-sm btn-danger" 
                                   onclick="return confirm('Tem certeza que deseja eliminar esta disciplina?')"
                                   title="Eliminar">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="10" class="text-center">Nenhuma disciplina encontrada</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para Adicionar/Editar Disciplina -->
<div class="modal fade" id="subjectModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?= site_url('admin/classes/subjects/save') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="subjectId">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Nova Disciplina</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-8">
                            <div class="mb-3">
                                <label for="discipline_name" class="form-label">Nome da Disciplina <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="discipline_name" name="discipline_name" required>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="discipline_code" class="form-label">Código <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="discipline_code" name="discipline_code" required>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="discipline_type" class="form-label">Tipo</label>
                                <select class="form-select" id="discipline_type" name="discipline_type">
                                    <option value="Obrigatória">Obrigatória</option>
                                    <option value="Opcional">Opcional</option>
                                    <option value="Complementar">Complementar</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="workload_hours" class="form-label">Carga Horária (horas)</label>
                                <input type="number" class="form-control" id="workload_hours" name="workload_hours" min="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="min_grade" class="form-label">Nota Mínima</label>
                                <input type="number" class="form-control" id="min_grade" name="min_grade" 
                                       step="0.01" min="0" max="20" value="0">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="max_grade" class="form-label">Nota Máxima</label>
                                <input type="number" class="form-control" id="max_grade" name="max_grade" 
                                       step="0.01" min="0" max="20" value="20">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="approval_grade" class="form-label">Nota de Aprovação</label>
                                <input type="number" class="form-control" id="approval_grade" name="approval_grade" 
                                       step="0.01" min="0" max="20" value="10">
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
                            <label class="form-check-label" for="is_active">Disciplina Ativa</label>
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
    $('#subjectsTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[1, 'asc']]
    });
    
    // Edit subject
    $('.edit-subject').click(function() {
        $('#modalTitle').text('Editar Disciplina');
        $('#subjectId').val($(this).data('id'));
        $('#discipline_name').val($(this).data('name'));
        $('#discipline_code').val($(this).data('code'));
        $('#discipline_type').val($(this).data('type'));
        $('#workload_hours').val($(this).data('hours'));
        $('#min_grade').val($(this).data('min'));
        $('#max_grade').val($(this).data('max'));
        $('#approval_grade').val($(this).data('approval'));
        $('#description').val($(this).data('description'));
        $('#is_active').prop('checked', $(this).data('active') == 1);
        
        $('#subjectModal').modal('show');
    });
    
    // Reset modal on new
    $('#subjectModal').on('hidden.bs.modal', function() {
        if (!$('#subjectId').val()) {
            $(this).find('form')[0].reset();
            $('#min_grade').val('0');
            $('#max_grade').val('20');
            $('#approval_grade').val('10');
            $('#is_active').prop('checked', true);
        }
    });
    
    // New subject button
    $('[data-bs-target="#subjectModal"]').click(function() {
        $('#modalTitle').text('Nova Disciplina');
        $('#subjectId').val('');
        $(this).find('form')[0].reset();
        $('#min_grade').val('0');
        $('#max_grade').val('20');
        $('#approval_grade').val('10');
        $('#is_active').prop('checked', true);
    });
});
</script>
<?= $this->endSection() ?>