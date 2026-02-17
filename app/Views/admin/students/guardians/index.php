<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <h1><?= $title ?></h1>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#guardianModal">
            <i class="fas fa-plus-circle"></i> Novo Encarregado
        </button>
    </div>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/students') ?>">Alunos</a></li>
            <li class="breadcrumb-item active" aria-current="page">Encarregados</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Data Table -->
<div class="card">
    <div class="card-header">
        <i class="fas fa-users"></i> Lista de Encarregados
    </div>
    <div class="card-body">
        <table id="guardiansTable" class="table table-striped table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>Tipo</th>
                    <th>Telefone</th>
                    <th>Email</th>
                    <th>Profissão</th>
                    <th>Nº Alunos</th>
                    <th>Status</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($guardians)): ?>
                    <?php foreach ($guardians as $guardian): ?>
                        <tr>
                            <td><?= $guardian->id ?></td>
                            <td><?= $guardian->full_name ?></td>
                            <td><span class="badge bg-info"><?= $guardian->guardian_type ?></span></td>
                            <td><?= $guardian->phone ?></td>
                            <td><?= $guardian->email ?: '-' ?></td>
                            <td><?= $guardian->profession ?: '-' ?></td>
                            <td>
                                <span class="badge bg-primary"><?= $guardian->student_count ?? 0 ?></span>
                            </td>
                            <td>
                                <?php if ($guardian->is_active): ?>
                                    <span class="badge bg-success">Ativo</span>
                                <?php else: ?>
                                    <span class="badge bg-danger">Inativo</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <button type="button" class="btn btn-sm btn-info edit-guardian" 
                                        data-id="<?= $guardian->id ?>"
                                        data-name="<?= $guardian->full_name ?>"
                                        data-type="<?= $guardian->guardian_type ?>"
                                        data-phone="<?= $guardian->phone ?>"
                                        data-email="<?= $guardian->email ?>"
                                        data-profession="<?= $guardian->profession ?>"
                                        data-workplace="<?= $guardian->workplace ?>"
                                        data-document="<?= $guardian->identity_document ?>"
                                        data-document-type="<?= $guardian->identity_type ?>"
                                        data-address="<?= $guardian->address ?>"
                                        data-active="<?= $guardian->is_active ?>"
                                        title="Editar">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-success" 
                                        onclick="viewGuardian(<?= $guardian->id ?>)"
                                        title="Ver Detalhes">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9" class="text-center">Nenhum encarregado encontrado</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- Modal para Adicionar/Editar Encarregado -->
<div class="modal fade" id="guardianModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form action="<?= site_url('admin/students/guardians/save') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="guardianId">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Novo Encarregado</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="full_name" class="form-label">Nome Completo <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="full_name" name="full_name" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="guardian_type" class="form-label">Tipo <span class="text-danger">*</span></label>
                                <select class="form-select" id="guardian_type" name="guardian_type" required>
                                    <option value="">Selecione...</option>
                                    <option value="Pai">Pai</option>
                                    <option value="Mãe">Mãe</option>
                                    <option value="Tutor">Tutor</option>
                                    <option value="Encarregado">Encarregado</option>
                                    <option value="Outro">Outro</option>
                                </select>
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="phone" class="form-label">Telefone <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="phone" name="phone" required>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="profession" class="form-label">Profissão</label>
                                <input type="text" class="form-control" id="profession" name="profession">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="workplace" class="form-label">Local de Trabalho</label>
                                <input type="text" class="form-control" id="workplace" name="workplace">
                            </div>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="identity_type" class="form-label">Tipo Doc.</label>
                                <select class="form-select" id="identity_type" name="identity_type">
                                    <option value="">Selecione...</option>
                                    <option value="BI">BI</option>
                                    <option value="Passaporte">Passaporte</option>
                                    <option value="Cédula">Cédula</option>
                                </select>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="identity_document" class="form-label">Nº Documento</label>
                                <input type="text" class="form-control" id="identity_document" name="identity_document">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="nif" class="form-label">NIF</label>
                                <input type="text" class="form-control" id="nif" name="nif">
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="address" class="form-label">Endereço</label>
                        <textarea class="form-control" id="address" name="address" rows="2"></textarea>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="city" class="form-label">Cidade</label>
                                <input type="text" class="form-control" id="city" name="city">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="municipality" class="form-label">Município</label>
                                <input type="text" class="form-control" id="municipality" name="municipality">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <div class="mb-3">
                                <label for="province" class="form-label">Província</label>
                                <input type="text" class="form-control" id="province" name="province">
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
    $('#guardiansTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[1, 'asc']]
    });
    
    // Edit guardian
    $('.edit-guardian').click(function() {
        $('#modalTitle').text('Editar Encarregado');
        $('#guardianId').val($(this).data('id'));
        $('#full_name').val($(this).data('name'));
        $('#guardian_type').val($(this).data('type'));
        $('#phone').val($(this).data('phone'));
        $('#email').val($(this).data('email'));
        $('#profession').val($(this).data('profession'));
        $('#workplace').val($(this).data('workplace'));
        $('#identity_type').val($(this).data('document-type'));
        $('#identity_document').val($(this).data('document'));
        $('#address').val($(this).data('address'));
        $('#is_active').prop('checked', $(this).data('active') == 1);
        
        $('#guardianModal').modal('show');
    });
    
    // Reset modal on new
    $('#guardianModal').on('hidden.bs.modal', function() {
        if (!$('#guardianId').val()) {
            $(this).find('form')[0].reset();
            $('#is_active').prop('checked', true);
        }
    });
    
    // New guardian button
    $('[data-bs-target="#guardianModal"]').click(function() {
        $('#modalTitle').text('Novo Encarregado');
        $('#guardianId').val('');
        $(this).find('form')[0].reset();
        $('#is_active').prop('checked', true);
    });
});

function viewGuardian(id) {
    window.location.href = '<?= site_url('admin/students/guardians/view/') ?>' + id;
}
</script>
<?= $this->endSection() ?>