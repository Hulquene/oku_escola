<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2"><?= $title ?></h1>
            <p class="text-muted mb-0">Gerencie os perfis de acesso e suas permissões</p>
        </div>
        <!-- Botão para adicionar novo perfil (se existir) -->
        <?php if (has_permission('roles.create')): ?>
        <div>
            <a href="<?= site_url('admin/roles/form') ?>" class="btn btn-primary">
                <i class="fas fa-plus-circle me-1"></i> Novo Perfil
            </a>
        </div>
        <?php endif; ?>
    </div>
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">
                <i class="fas fa-home me-1"></i>Dashboard
            </a></li>
            <li class="breadcrumb-item active" aria-current="page">Perfis de Acesso</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Cards de Estatísticas -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Total de Perfis</h6>
                        <h2 class="mb-0"><?= count($roles) ?></h2>
                    </div>
                    <i class="fas fa-shield-alt fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Com Permissões</h6>
                        <h2 class="mb-0">-</h2>
                    </div>
                    <i class="fas fa-key fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Utilizadores</h6>
                        <h2 class="mb-0">-</h2>
                    </div>
                    <i class="fas fa-users fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-header bg-white">
        <h5 class="mb-0">
            <i class="fas fa-list me-2 text-primary"></i>
            Lista de Perfis de Acesso
            <span class="badge bg-secondary ms-2"><?= count($roles) ?> registros</span>
        </h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="rolesTable" class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th width="80">ID</th>
                        <th>Perfil</th>
                        <th>Descrição</th>
                        <th width="120">Data Criação</th>
                        <th width="80">Utilizadores</th>
                        <th width="200">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($roles)): ?>
                        <?php foreach ($roles as $role): ?>
                            <tr>
                                <td><span class="badge bg-secondary"><?= $role->id ?></span></td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="bg-primary rounded-circle text-white d-flex align-items-center justify-content-center me-2"
                                            style="width: 40px; height: 40px; font-size: 1rem;">
                                            <i class="fas fa-shield-alt"></i>
                                        </div>
                                        <div>
                                            <strong><?= $role->role_name ?></strong>
                                            
                                            <?php if ($role->role_type == 'admin'): ?>
                                                <span class="badge bg-warning ms-2">Administrador</span>
                                            <?php elseif ($role->role_type == 'teacher'): ?>
                                                <span class="badge bg-info ms-2">Professor</span>
                                            <?php elseif ($role->role_type == 'student'): ?>
                                                <span class="badge bg-success ms-2">Aluno</span>
                                            <?php endif; ?>
                                            
                                            <?php if (in_array($role->role_type, ['admin', 'teacher', 'student'])): ?>
                                                <span class="badge bg-secondary ms-1">Sistema</span>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </td>
                                <td><?= $role->role_description ?: '<span class="text-muted">Sem descrição</span>' ?></td>
                                <td>
                                    <i class="far fa-calendar-alt text-muted me-1"></i>
                                    <?= date('d/m/Y', strtotime($role->created_at)) ?>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info">0</span>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= site_url('admin/roles/permission/' . $role->id) ?>" 
                                        class="btn btn-sm btn-primary" 
                                        title="Gerir Permissões"
                                        data-bs-toggle="tooltip">
                                            <i class="fas fa-key me-1"></i> Permissões
                                        </a>
                                        
                                        <!-- Proteger edição: não permitir editar perfis de sistema (admin, teacher, student) -->
                                        <?php if (!in_array($role->role_type, ['admin', 'teacher', 'student']) && has_permission('roles.edit')): ?>
                                            <a href="<?= site_url('admin/roles/form/' . $role->id) ?>" 
                                            class="btn btn-sm btn-outline-info" 
                                            title="Editar"
                                            data-bs-toggle="tooltip">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        <?php endif; ?>
                                        
                                        <!-- Proteger exclusão: não permitir excluir perfis de sistema -->
                                        <?php if (!in_array($role->role_type, ['admin', 'teacher', 'student']) && has_permission('roles.delete')): ?>
                                            <form action="<?= site_url('admin/roles/delete/' . $role->id) ?>" method="post" style="display: inline;">
                                                <?= csrf_field() ?>
                                                <button type="submit" 
                                                        class="btn btn-sm btn-outline-danger" 
                                                        onclick="return confirm('Tem certeza que deseja eliminar o perfil <?= $role->role_name ?>?')"
                                                        title="Eliminar"
                                                        data-bs-toggle="tooltip">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center py-5">
                                <i class="fas fa-shield-alt fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">Nenhum perfil encontrado</h5>
                                <p class="text-muted mb-3">Comece criando o primeiro perfil de acesso.</p>
                                <a href="<?= site_url('admin/roles/form-add') ?>" class="btn btn-primary">
                                    <i class="fas fa-plus-circle me-2"></i>Novo Perfil
                                </a>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Eliminação -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Eliminação
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja eliminar o perfil <strong id="deleteRoleName"></strong>?</p>
                <p class="text-muted small">
                    <i class="fas fa-info-circle me-1"></i>
                    Esta ação não pode ser desfeita. Utilizadores com este perfil serão afetados.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="confirmDeleteBtn" class="btn btn-danger">
                    <i class="fas fa-trash me-1"></i>Eliminar
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- DataTables CSS and JS -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<!-- Localização em Português (fallback local caso o CDN falhe) -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Inicializar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });
    
    // Inicializar DataTable
    $('#rolesTable').DataTable({
        language: {
            "sEmptyTable": "Nenhum dado disponível na tabela",
            "sInfo": "Mostrando _START_ até _END_ de _TOTAL_ registos",
            "sInfoEmpty": "Mostrando 0 até 0 de 0 registos",
            "sInfoFiltered": "(filtrado de _MAX_ registos no total)",
            "sInfoPostFix": "",
            "sInfoThousands": ",",
            "sLengthMenu": "Mostrar _MENU_ registos por página",
            "sLoadingRecords": "Carregando...",
            "sProcessing": "Processando...",
            "sSearch": "Pesquisar:",
            "sZeroRecords": "Nenhum registo encontrado",
            "oPaginate": {
                "sFirst": "Primeiro",
                "sLast": "Último",
                "sNext": "Seguinte",
                "sPrevious": "Anterior"
            },
            "oAria": {
                "sSortAscending": ": ativar para ordenar a coluna de forma ascendente",
                "sSortDescending": ": ativar para ordenar a coluna de forma descendente"
            }
        },
        order: [[1, 'asc']],
        pageLength: 10,
        responsive: true,
        columnDefs: [
            { orderable: false, targets: [5] } // Coluna de ações não ordenável
        ]
    });
});

// Função para confirmar eliminação
function confirmDelete(id, name) {
    document.getElementById('deleteRoleName').textContent = name;
    document.getElementById('confirmDeleteBtn').href = '<?= site_url('admin/roles/delete/') ?>' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// Atalho de teclado: Ctrl+F para focar na pesquisa
document.addEventListener('keydown', function(e) {
    if (e.ctrlKey && e.key === 'f') {
        e.preventDefault();
        document.querySelector('input[type="search"]')?.focus();
    }
});
</script>

<style>
/* Estilos adicionais */
.table-hover tbody tr:hover {
    background-color: rgba(0,123,255,0.05);
}

.btn-group .btn {
    transition: all 0.2s;
}

.btn-group .btn:hover {
    transform: translateY(-2px);
}

.badge {
    font-weight: 500;
    padding: 0.5em 0.8em;
}

/* Avatar dos perfis */
.bg-primary.rounded-circle {
    background: linear-gradient(135deg, #007bff, #0056b3) !important;
}

/* Animação de entrada */
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.card {
    animation: fadeIn 0.3s ease-out;
}

/* Responsividade */
@media (max-width: 768px) {
    .btn-group {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }
    
    .btn-sm {
        width: 100%;
    }
}
</style>
<?= $this->endSection() ?>