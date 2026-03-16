<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="h3 mb-0"><?= $title ?? 'Encarregados de Educação' ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= route_to('admin.dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= route_to('students.index') ?>">Alunos</a></li>
                    <li class="breadcrumb-item active">Encarregados</li>
                </ol>
            </nav>
        </div>
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#guardianModal">
            <i class="fas fa-plus-circle me-2"></i>Novo Encarregado
        </button>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="card-stats">
            <div class="card-stats-icon bg-primary">
                <i class="fas fa-users"></i>
            </div>
            <div class="card-stats-content">
                <h3><?= $totalGuardians ?? count($guardians) ?></h3>
                <p>Total de Encarregados</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card-stats">
            <div class="card-stats-icon bg-success">
                <i class="fas fa-user-tie"></i>
            </div>
            <div class="card-stats-content">
                <?php
                $pais = array_filter($guardians ?? [], function($g) { return $g->guardian_type == 'Pai' || $g->guardian_type == 'Mãe'; });
                ?>
                <h3><?= count($pais) ?></h3>
                <p>Pais/Mães</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card-stats">
            <div class="card-stats-icon bg-info">
                <i class="fas fa-user-tag"></i>
            </div>
            <div class="card-stats-content">
                <?php
                $tutores = array_filter($guardians ?? [], function($g) { return $g->guardian_type == 'Tutor' || $g->guardian_type == 'Encarregado'; });
                ?>
                <h3><?= count($tutores) ?></h3>
                <p>Tutores</p>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card-stats">
            <div class="card-stats-icon bg-warning">
                <i class="fas fa-child"></i>
            </div>
            <div class="card-stats-content">
                <?php
                $totalAlunos = array_sum(array_column($guardians ?? [], 'student_count'));
                ?>
                <h3><?= $totalAlunos ?></h3>
                <p>Alunos Vinculados</p>
            </div>
        </div>
    </div>
</div>

<!-- Filters Card -->
<div class="card mb-4">
    <div class="card-body">
        <form id="filterForm" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Tipo</label>
                <select class="form-select" id="filterType">
                    <option value="">Todos</option>
                    <option value="Pai">Pai</option>
                    <option value="Mãe">Mãe</option>
                    <option value="Tutor">Tutor</option>
                    <option value="Encarregado">Encarregado</option>
                </select>
            </div>
            
            <div class="col-md-3">
                <label class="form-label">Status</label>
                <select class="form-select" id="filterStatus">
                    <option value="">Todos</option>
                    <option value="1">Ativos</option>
                    <option value="0">Inativos</option>
                </select>
            </div>
            
            <div class="col-md-4">
                <label class="form-label">Buscar</label>
                <input type="text" class="form-control" id="filterSearch" placeholder="Nome, telefone ou email...">
            </div>
            
            <div class="col-md-2 d-flex align-items-end">
                <button type="button" class="btn btn-secondary w-100" id="clearFilters">
                    <i class="fas fa-times me-2"></i>Limpar
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <div>
            <i class="fas fa-users me-2"></i>
            Lista de Encarregados
        </div>
        <button class="btn btn-sm btn-outline-primary" onclick="exportToExcel()">
            <i class="fas fa-file-excel me-1"></i>Exportar
        </button>
    </div>
    
    <div class="card-body">
        <div class="table-responsive">
            <table id="guardiansTable" class="table table-hover align-middle">
                <thead>
                    <tr>
                        <th width="50">#</th>
                        <th>Nome</th>
                        <th>Tipo</th>
                        <th>Contacto</th>
                        <th>Email</th>
                        <th>Profissão</th>
                        <th class="text-center">Alunos</th>
                        <th class="text-center">Status</th>
                        <th width="120">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($guardians)): ?>
                        <?php foreach ($guardians as $i => $guardian): ?>
                            <tr>
                                <td><?= $i + 1 ?></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-circle bg-primary me-2">
                                            <?= strtoupper(substr($guardian->full_name, 0, 1)) ?>
                                        </div>
                                        <div>
                                            <strong><?= $guardian->full_name ?></strong>
                                            <?php if ($guardian->identity_document): ?>
                                                <br><small class="text-muted">Doc: <?= $guardian->identity_document ?></small>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php
                                    $typeClass = match($guardian->guardian_type) {
                                        'Pai' => 'primary',
                                        'Mãe' => 'success',
                                        'Tutor' => 'info',
                                        default => 'secondary'
                                    };
                                    ?>
                                    <span class="badge bg-<?= $typeClass ?> bg-opacity-10 text-<?= $typeClass ?> px-3 py-2">
                                        <i class="fas fa-user-tie me-1"></i><?= $guardian->guardian_type ?>
                                    </span>
                                </td>
                                <td>
                                    <?php if ($guardian->phone): ?>
                                        <a href="tel:<?= $guardian->phone ?>" class="text-decoration-none">
                                            <i class="fas fa-phone-alt text-primary me-1"></i><?= $guardian->phone ?>
                                        </a>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($guardian->email): ?>
                                        <a href="mailto:<?= $guardian->email ?>" class="text-decoration-none">
                                            <i class="fas fa-envelope text-info me-1"></i><?= $guardian->email ?>
                                        </a>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($guardian->profession): ?>
                                        <i class="fas fa-briefcase text-secondary me-1"></i><?= $guardian->profession ?>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-primary rounded-pill px-3">
                                        <?= $guardian->student_count ?? 0 ?>
                                    </span>
                                </td>
                                <td class="text-center">
                                    <?php if ($guardian->is_active): ?>
                                        <span class="badge bg-success bg-opacity-10 text-success px-3 py-2">
                                            <i class="fas fa-circle me-1" style="font-size: 8px;"></i>Ativo
                                        </span>
                                    <?php else: ?>
                                        <span class="badge bg-danger bg-opacity-10 text-danger px-3 py-2">
                                            <i class="fas fa-circle me-1" style="font-size: 8px;"></i>Inativo
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <button type="button" class="btn btn-sm btn-outline-info edit-guardian" 
                                                data-id="<?= $guardian->id ?>"
                                                data-name="<?= $guardian->full_name ?>"
                                                data-type="<?= $guardian->guardian_type ?>"
                                                data-phone="<?= $guardian->phone ?>"
                                                data-email="<?= $guardian->email ?>"
                                                data-profession="<?= $guardian->profession ?>"
                                                data-workplace="<?= $guardian->workplace ?>"
                                                data-document="<?= $guardian->identity_document ?>"
                                                data-document-type="<?= $guardian->identity_type ?>"
                                                data-nif="<?= $guardian->nif ?>"
                                                data-address="<?= $guardian->address ?>"
                                                data-city="<?= $guardian->city ?>"
                                                data-municipality="<?= $guardian->municipality ?>"
                                                data-province="<?= $guardian->province ?>"
                                                data-active="<?= $guardian->is_active ?>"
                                                title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <button type="button" class="btn btn-sm btn-outline-success" 
                                                onclick="viewGuardian(<?= $guardian->id ?>)"
                                                title="Ver Detalhes">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        <?php if (has_permission('students.delete')): ?>
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="deleteGuardian(<?= $guardian->id ?>)"
                                                title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="empty-state">
                                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                                    <h5>Nenhum encarregado encontrado</h5>
                                    <p class="text-muted">Clique no botão "Novo Encarregado" para adicionar.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para Adicionar/Editar Encarregado -->
<div class="modal fade" id="guardianModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <form action="<?= route_to('students.guardians.save') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="guardianId">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">
                        <i class="fas fa-user-tie me-2"></i>
                        <span>Novo Encarregado</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <!-- Informações Pessoais -->
                    <div class="section-title mb-3">
                        <i class="fas fa-user me-2"></i>Informações Pessoais
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="full_name" class="form-label">Nome Completo <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-user"></i></span>
                                <input type="text" class="form-control" id="full_name" name="full_name" required>
                            </div>
                        </div>
                        
                        <div class="col-md-3">
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
                        
                        <div class="col-md-3">
                            <label for="is_active" class="form-label">&nbsp;</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" checked>
                                <label class="form-check-label" for="is_active">Ativo</label>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Contactos -->
                    <div class="section-title mb-3">
                        <i class="fas fa-address-book me-2"></i>Contactos
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-4">
                            <label for="phone" class="form-label">Telefone <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone"></i></span>
                                <input type="text" class="form-control" id="phone" name="phone" required>
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <label for="phone2" class="form-label">Telefone Alternativo</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-phone-alt"></i></span>
                                <input type="text" class="form-control" id="phone2" name="phone2">
                            </div>
                        </div>
                        
                        <div class="col-md-4">
                            <label for="email" class="form-label">Email</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                                <input type="email" class="form-control" id="email" name="email">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Profissão -->
                    <div class="section-title mb-3">
                        <i class="fas fa-briefcase me-2"></i>Profissão
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="profession" class="form-label">Profissão</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-briefcase"></i></span>
                                <input type="text" class="form-control" id="profession" name="profession">
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="workplace" class="form-label">Local de Trabalho</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-building"></i></span>
                                <input type="text" class="form-control" id="workplace" name="workplace">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Documentos -->
                    <div class="section-title mb-3">
                        <i class="fas fa-id-card me-2"></i>Documentos
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-3">
                            <label for="identity_type" class="form-label">Tipo Documento</label>
                            <select class="form-select" id="identity_type" name="identity_type">
                                <option value="">Selecione...</option>
                                <option value="BI">BI</option>
                                <option value="Passaporte">Passaporte</option>
                                <option value="Cédula">Cédula</option>
                            </select>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="identity_document" class="form-label">Nº Documento</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                <input type="text" class="form-control" id="identity_document" name="identity_document">
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="nif" class="form-label">NIF</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                                <input type="text" class="form-control" id="nif" name="nif">
                            </div>
                        </div>
                        
                        <div class="col-md-3">
                            <label for="birth_date" class="form-label">Data Nascimento</label>
                            <div class="input-group">
                                <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                                <input type="date" class="form-control" id="birth_date" name="birth_date">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Endereço -->
                    <div class="section-title mb-3">
                        <i class="fas fa-map-marker-alt me-2"></i>Endereço
                    </div>
                    
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <label for="address" class="form-label">Endereço</label>
                            <textarea class="form-control" id="address" name="address" rows="2"></textarea>
                        </div>
                        
                        <div class="col-md-6">
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <label for="city" class="form-label">Cidade</label>
                                    <input type="text" class="form-control" id="city" name="city">
                                </div>
                                
                                <div class="col-md-4">
                                    <label for="municipality" class="form-label">Município</label>
                                    <input type="text" class="form-control" id="municipality" name="municipality">
                                </div>
                                
                                <div class="col-md-4">
                                    <label for="province" class="form-label">Província</label>
                                    <input type="text" class="form-control" id="province" name="province">
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Observações -->
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label for="notes" class="form-label">Observações</label>
                            <textarea class="form-control" id="notes" name="notes" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save me-2"></i>Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Initialize DataTable
    var table = $('#guardiansTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[1, 'asc']],
        pageLength: 25,
        dom: 'rtip',
        buttons: []
    });
    
    // Filtros customizados
    $('#filterType').on('change', function() {
        var type = $(this).val();
        if (type) {
            table.column(2).search('^' + type + '$', true, false).draw();
        } else {
            table.column(2).search('').draw();
        }
    });
    
    $('#filterStatus').on('change', function() {
        var status = $(this).val();
        if (status === '1') {
            table.column(7).search('Ativo').draw();
        } else if (status === '0') {
            table.column(7).search('Inativo').draw();
        } else {
            table.column(7).search('').draw();
        }
    });
    
    $('#filterSearch').on('keyup', function() {
        table.search($(this).val()).draw();
    });
    
    $('#clearFilters').on('click', function() {
        $('#filterType').val('');
        $('#filterStatus').val('');
        $('#filterSearch').val('');
        table.search('').columns().search('').draw();
    });
    
    // Edit guardian
    $('.edit-guardian').click(function() {
        $('#modalTitle span').text('Editar Encarregado');
        $('#guardianId').val($(this).data('id'));
        $('#full_name').val($(this).data('name'));
        $('#guardian_type').val($(this).data('type'));
        $('#phone').val($(this).data('phone'));
        $('#phone2').val($(this).data('phone2'));
        $('#email').val($(this).data('email'));
        $('#profession').val($(this).data('profession'));
        $('#workplace').val($(this).data('workplace'));
        $('#identity_type').val($(this).data('document-type'));
        $('#identity_document').val($(this).data('document'));
        $('#nif').val($(this).data('nif'));
        $('#address').val($(this).data('address'));
        $('#city').val($(this).data('city'));
        $('#municipality').val($(this).data('municipality'));
        $('#province').val($(this).data('province'));
        $('#birth_date').val($(this).data('birth-date'));
        $('#notes').val($(this).data('notes'));
        $('#is_active').prop('checked', $(this).data('active') == 1);
        
        $('#guardianModal').modal('show');
    });
    
    // New guardian button
    $('[data-bs-target="#guardianModal"]').click(function() {
        $('#modalTitle span').text('Novo Encarregado');
        $('#guardianId').val('');
        $('#guardianModal form')[0].reset();
        $('#is_active').prop('checked', true);
    });
    
    // Reset form on modal close
    $('#guardianModal').on('hidden.bs.modal', function() {
        if (!$('#guardianId').val()) {
            $(this).find('form')[0].reset();
            $('#is_active').prop('checked', true);
        }
    });
});

function viewGuardian(id) {
    window.location.href = '<?= route_to('students.guardians.view') ?>' + id;
}

function deleteGuardian(id) {
    if (confirm('Tem certeza que deseja eliminar este encarregado?')) {
        window.location.href = '<?= site_url('admin/students/guardians/delete/') ?>' + id;
    }
}

function exportToExcel() {
    var table = document.getElementById('guardiansTable');
    var wb = XLSX.utils.table_to_book(table, {sheet: "Encarregados"});
    XLSX.writeFile(wb, 'encarregados_' + new Date().toISOString().slice(0,10) + '.xlsx');
}
</script>

<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<?= $this->endSection() ?>