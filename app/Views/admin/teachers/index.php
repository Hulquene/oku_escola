<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="page-header">
    <div class="d-flex justify-content-between align-items-center">
        <div>
            <h1 class="mb-2"><?= $title ?></h1>
            <p class="text-muted mb-0">Gerencie todos os professores da instituição</p>
        </div>
        <div>
            <a href="<?= site_url('admin/teachers/export') ?>" class="btn btn-success me-2" title="Exportar lista">
                <i class="fas fa-file-excel"></i> Exportar
            </a>
            <a href="<?= site_url('admin/teachers/form-add') ?>" class="btn btn-primary">
                <i class="fas fa-plus-circle"></i> Novo Professor
            </a>
        </div>
    </div>
    <nav aria-label="breadcrumb" class="mt-3">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">
                <i class="fas fa-home me-1"></i>Dashboard
            </a></li>
            <li class="breadcrumb-item active" aria-current="page">Professores</li>
        </ol>
    </nav>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Filtros Rápidos -->
<div class="card mb-4">
    <div class="card-body">
        <form method="get" class="row g-3">
            <div class="col-md-4">
                <label for="search" class="form-label fw-semibold">Buscar Professor</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="form-control" id="search" name="search" 
                           placeholder="Nome, email ou telefone..."
                           value="<?= $search ?? '' ?>">
                </div>
            </div>
            <div class="col-md-3">
                <label for="status" class="form-label fw-semibold">Status</label>
                <select class="form-select" id="status" name="status">
                    <option value="">Todos</option>
                    <option value="active" <?= ($selectedStatus ?? '') == 'active' ? 'selected' : '' ?>>Ativos</option>
                    <option value="inactive" <?= ($selectedStatus ?? '') == 'inactive' ? 'selected' : '' ?>>Inativos</option>
                </select>
            </div>
            <div class="col-md-3">
                <label for="department" class="form-label fw-semibold">Departamento</label>
                <select class="form-select" id="department" name="department">
                    <option value="">Todos</option>
                    <option value="ciencias" <?= ($selectedDepartment ?? '') == 'ciencias' ? 'selected' : '' ?>>Ciências</option>
                    <option value="humanidades" <?= ($selectedDepartment ?? '') == 'humanidades' ? 'selected' : '' ?>>Humanidades</option>
                    <option value="linguagens" <?= ($selectedDepartment ?? '') == 'linguagens' ? 'selected' : '' ?>>Linguagens</option>
                    <option value="tecnico" <?= ($selectedDepartment ?? '') == 'tecnico' ? 'selected' : '' ?>>Técnico</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary me-2 w-100">
                    <i class="fas fa-filter me-1"></i>Filtrar
                </button>
                <a href="<?= site_url('admin/teachers') ?>" class="btn btn-secondary">
                    <i class="fas fa-undo"></i>
                </a>
            </div>
        </form>
    </div>
</div>

<!-- Cards de Estatísticas -->
<div class="row mb-4">
    <div class="col-md-3">
        <div class="card bg-primary text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Total Professores</h6>
                        <h2 class="mb-0"><?= $totalTeachers ?? 0 ?></h2>
                        <small>Cadastrados no sistema</small>
                    </div>
                    <i class="fas fa-chalkboard-teacher fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-success text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Professores Ativos</h6>
                        <h2 class="mb-0"><?= $activeTeachers ?? 0 ?></h2>
                        <small>Em exercício</small>
                    </div>
                    <i class="fas fa-user-check fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-info text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Turmas Atribuídas</h6>
                        <h2 class="mb-0"><?= $totalAssignments ?? 0 ?></h2>
                        <small>Distribuição de turmas</small>
                    </div>
                    <i class="fas fa-school fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="card bg-warning text-white">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h6 class="text-white-50 mb-1">Carga Horária Total</h6>
                        <h2 class="mb-0"><?= $totalWorkload ?? 0 ?>h</h2>
                        <small>Por semana</small>
                    </div>
                    <i class="fas fa-clock fa-3x text-white-50"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Data Table -->
<div class="card">
    <div class="card-header bg-white">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="mb-0">
                <i class="fas fa-list me-2 text-primary"></i>
                Lista de Professores
                <span class="badge bg-secondary ms-2"><?= $totalFiltered ?? count($teachers) ?> registros</span>
            </h5>
            <div>
                <button class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                    <i class="fas fa-print me-1"></i>Imprimir
                </button>
            </div>
        </div>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table id="teachersTable" class="table table-striped table-hover align-middle">
                <thead class="table-dark">
                    <tr>
                        <th>#</th>
                        <th>Foto</th>
                        <th>Nome Completo</th>
                        <th>Contacto</th>
                        <th>Turmas</th>
                        <th>Carga Horária</th>
                        <th>Último Acesso</th>
                        <th>Status</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($teachers)): ?>
                        <?php foreach ($teachers as $teacher): 
                            // Calcular totais
                            $totalClasses = $teacher->total_classes ?? 0;
                            $totalWorkload = $teacher->total_workload ?? 0;
                        ?>
                            <tr>
                                <td><span class="badge bg-secondary"><?= $teacher->teacher_id ?? $teacher->id ?></span></td>
                                <td>
                                    <?php if ($teacher->photo): ?>
                                        <img src="<?= base_url('uploads/teachers/' . $teacher->photo) ?>" 
                                             alt="Foto" 
                                             class="rounded-circle border"
                                             style="width: 45px; height: 45px; object-fit: cover;">
                                    <?php else: ?>
                                        <div class="bg-primary rounded-circle d-inline-flex align-items-center justify-content-center text-white fw-bold"
                                             style="width: 45px; height: 45px; font-size: 1.2rem;">
                                            <?= strtoupper(substr($teacher->first_name, 0, 1) . substr($teacher->last_name, 0, 1)) ?>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <strong><?= $teacher->first_name ?> <?= $teacher->last_name ?></strong>
                                    <br>
                                    <small class="text-muted">
                                        <i class="fas fa-envelope me-1"></i><?= $teacher->email ?>
                                    </small>
                                </td>
                                <td>
                                    <i class="fas fa-phone me-1 text-success"></i><?= $teacher->phone ?: '-' ?>
                                </td>
                                <td>
                                    <span class="badge bg-info p-2" title="Turmas atribuídas">
                                        <i class="fas fa-school me-1"></i><?= $totalClasses ?> turmas
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary p-2">
                                        <i class="fas fa-clock me-1"></i><?= $totalWorkload ?>h
                                    </span>
                                </td>
                                <td>
                                    <?php if ($teacher->last_login): ?>
                                        <small>
                                            <?= date('d/m/Y', strtotime($teacher->last_login)) ?>
                                            <br>
                                            <span class="text-muted"><?= date('H:i', strtotime($teacher->last_login)) ?></span>
                                        </small>
                                    <?php else: ?>
                                        <span class="text-muted">Nunca acedeu</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($teacher->is_active): ?>
                                        <span class="badge bg-success">Ativo</span>
                                    <?php else: ?>
                                        <span class="badge bg-danger">Inativo</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="<?= site_url('admin/teachers/view/' . $teacher->teacher_id) ?>" 
                                           class="btn btn-sm btn-outline-success" 
                                           title="Ver Detalhes"
                                           data-bs-toggle="tooltip">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="<?= site_url('admin/teachers/form-edit/' . $teacher->teacher_id) ?>" 
                                           class="btn btn-sm btn-outline-info" 
                                           title="Editar"
                                           data-bs-toggle="tooltip">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= site_url('admin/teachers/assign-class/' . $teacher->teacher_id) ?>" 
                                           class="btn btn-sm btn-outline-primary" 
                                           title="Atribuir Turmas"
                                           data-bs-toggle="tooltip">
                                            <i class="fas fa-tasks"></i>
                                        </a>
                                        <a href="<?= site_url('admin/teachers/schedule/' . $teacher->teacher_id) ?>" 
                                           class="btn btn-sm btn-outline-warning" 
                                           title="Ver Horário"
                                           data-bs-toggle="tooltip">
                                            <i class="fas fa-calendar-alt"></i>
                                        </a>
                                        <?php if ($teacher->is_active): ?>
                                            <button type="button" 
                                                    class="btn btn-sm btn-outline-danger" 
                                                    onclick="confirmDeactivate(<?= $teacher->teacher_id ?>, '<?= $teacher->first_name ?> <?= $teacher->last_name ?>')"
                                                    title="Desativar"
                                                    data-bs-toggle="tooltip">
                                                <i class="fas fa-user-slash"></i>
                                            </button>
                                        <?php else: ?>
                                            <a href="<?= site_url('admin/teachers/activate/' . $teacher->teacher_id) ?>" 
                                               class="btn btn-sm btn-outline-success" 
                                               title="Reativar"
                                               data-bs-toggle="tooltip"
                                               onclick="return confirm('Tem certeza que deseja reativar este professor?')">
                                                <i class="fas fa-user-check"></i>
                                            </a>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center py-5">
                                <i class="fas fa-chalkboard-teacher fa-4x text-muted mb-3"></i>
                                <h5 class="text-muted">Nenhum professor encontrado</h5>
                                <p class="text-muted mb-3">
                                    <?= isset($search) ? 'Tente ajustar os filtros de busca' : 'Comece cadastrando o primeiro professor' ?>
                                </p>
                                <?php if (!isset($search)): ?>
                                    <a href="<?= site_url('admin/teachers/form-add') ?>" class="btn btn-primary">
                                        <i class="fas fa-plus-circle me-2"></i>Cadastrar Professor
                                    </a>
                                <?php else: ?>
                                    <a href="<?= site_url('admin/teachers') ?>" class="btn btn-secondary">
                                        <i class="fas fa-undo me-2"></i>Limpar Filtros
                                    </a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
        
        <!-- Paginação -->
        <?php if (!empty($teachers) && isset($pager)): ?>
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Mostrando <?= count($teachers) ?> de <?= $totalFiltered ?? $pager->getTotal() ?> registros
                </div>
                <div>
                    <?= $pager->links() ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- Modal de Confirmação de Desativação -->
<div class="modal fade" id="deactivateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title text-white">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirmar Desativação
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja desativar o professor <strong id="deactivateTeacherName"></strong>?</p>
                
                <div class="alert alert-warning">
                    <i class="fas fa-info-circle me-2"></i>
                    <strong>Ao desativar um professor:</strong>
                    <ul class="mt-2 mb-0">
                        <li>O professor não poderá aceder ao sistema</li>
                        <li>As turmas atribuídas ficarão sem professor</li>
                        <li>Os dados históricos serão preservados</li>
                    </ul>
                </div>
                
                <p class="text-muted small mt-3">
                    <i class="fas fa-clock me-1"></i>
                    Esta ação pode ser revertida posteriormente.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="confirmDeactivateBtn" class="btn btn-warning">
                    <i class="fas fa-user-slash me-1"></i>Desativar Professor
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- DataTables -->
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

<script>
$(document).ready(function() {
    // Inicializar tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'))
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl)
    });
    
    // Inicializar DataTable
    $('#teachersTable').DataTable({
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[2, 'asc']],
        pageLength: 25,
        searching: true,
        paging: false,
        info: false,
        columnDefs: [
            { orderable: false, targets: [1, 8] }
        ]
    });
});

// Função para confirmar desativação
function confirmDeactivate(id, name) {
    document.getElementById('deactivateTeacherName').textContent = name;
    document.getElementById('confirmDeactivateBtn').href = '<?= site_url('admin/teachers/delete/') ?>' + id;
    new bootstrap.Modal(document.getElementById('deactivateModal')).show();
}

// Auto-submit dos filtros
let filterTimeout;
$('#search, #status, #department').on('change keyup', function() {
    clearTimeout(filterTimeout);
    filterTimeout = setTimeout(() => {
        $(this).closest('form').submit();
    }, 500);
});

// Atualizar estatísticas via AJAX (opcional)
function refreshStats() {
    $.get('<?= site_url('admin/teachers/get-stats') ?>', function(data) {
        // Atualizar cards de estatísticas
    });
}
</script>

<style>
/* Animações */
.page-header {
    animation: fadeIn 0.3s ease-out;
}

.card {
    transition: all 0.2s;
}

.card:hover {
    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
}

/* Estilo para os botões de ação */
.btn-group .btn {
    transition: all 0.2s;
}

.btn-group .btn:hover {
    transform: translateY(-2px);
}

/* Badges personalizados */
.badge {
    font-weight: 500;
    padding: 0.5em 0.8em;
}

/* Tabela responsiva */
.table td {
    vertical-align: middle;
}

/* Loading state */
.loading {
    opacity: 0.5;
    pointer-events: none;
}
</style>
<?= $this->endSection() ?>