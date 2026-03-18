<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>



<!-- Page Header -->
<div class="ci-page-header mb-4">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-chalkboard-teacher me-2"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Professores</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <a href="<?= site_url('admin/teachers/export') ?>" class="hdr-btn success">
                <i class="fas fa-file-excel"></i> Exportar
            </a>
            <a href="<?= site_url('admin/teachers/form-add') ?>" class="hdr-btn primary">
                <i class="fas fa-plus-circle"></i> Novo Professor
            </a>
        </div>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Filtros -->
<div class="ci-card mb-4">
    <div class="ci-card-header">
        <div class="ci-card-title"><i class="fas fa-filter"></i> Filtros</div>
    </div>
    <div class="ci-card-body">
        <div class="filter-grid">
<!--             <div>
                <label class="filter-label">Buscar Professor</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="filter-select" id="search" 
                           placeholder="Nome, email ou telefone...">
                </div>
            </div> -->
            <div>
                <label class="filter-label">Status</label>
                <select class="filter-select" id="status">
                    <option value="">Todos</option>
                    <option value="active">Ativos</option>
                    <option value="inactive">Inativos</option>
                </select>
            </div>
            <div>
                <label class="filter-label">Departamento</label>
                <select class="filter-select" id="department">
                    <option value="">Todos</option>
                    <option value="ciencias">Ciências</option>
                    <option value="humanidades">Humanidades</option>
                    <option value="linguagens">Linguagens</option>
                    <option value="tecnico">Técnico</option>
                </select>
            </div>
        </div>
        <div class="filter-actions">
            <button class="btn-filter apply" id="btnFilter"><i class="fas fa-filter"></i> Filtrar</button>
            <button class="btn-filter clear" id="btnClear"><i class="fas fa-undo"></i> Limpar</button>
        </div>
    </div>
</div>

<!-- Cards de Estatísticas -->
<div class="stat-grid" id="statsContainer">
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-chalkboard-teacher"></i>
        </div>
        <div>
            <div class="stat-label">Total Professores</div>
            <div class="stat-value" id="totalTeachers">0</div>
            <div class="stat-sub">Cadastrados</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-user-check"></i>
        </div>
        <div>
            <div class="stat-label">Professores Ativos</div>
            <div class="stat-value" id="activeTeachers">0</div>
            <div class="stat-sub">Em exercício</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-tasks"></i>
        </div>
        <div>
            <div class="stat-label">Turmas Atribuídas</div>
            <div class="stat-value" id="totalAssignments">0</div>
            <div class="stat-sub">Distribuição</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon navy">
            <i class="fas fa-clock"></i>
        </div>
        <div>
            <div class="stat-label">Carga Horária</div>
            <div class="stat-value" id="totalWorkload">0h</div>
            <div class="stat-sub">Por semana</div>
        </div>
    </div>
</div>

<!-- Data Table -->
<div class="ci-card">
    <div class="ci-card-header">
        <div class="ci-card-title"><i class="fas fa-list"></i> Lista de Professores</div>
        <span class="badge bg-primary" id="recordCount">0 registros</span>
    </div>
    <div style="overflow-x:auto; padding: 0 1.25rem 1.25rem;">
        <table id="teachersTable" class="ci-table" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Foto</th>
                    <th>Nome Completo</th>
                    <th>Contacto</th>
                    <th>Turmas</th>
                    <th>Carga Hor.</th>
                    <th>Último Acesso</th>
                    <th>Status</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <!-- DataTables preenche via AJAX -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal de Confirmação de Desativação -->
<div class="modal fade" id="deactivateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:var(--radius);border:none;overflow:hidden;">
            <div class="modal-header" style="background:var(--warning);border:none;">
                <h5 class="modal-title text-white">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Confirmar Desativação
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:1.4rem;">
                <p>Tem certeza que deseja desativar o professor <strong id="deactivateTeacherName"></strong>?</p>
                
                <div style="background:rgba(232,160,32,.08);border:1px solid rgba(232,160,32,.25);border-radius:var(--radius-sm);padding:.85rem 1rem;font-size:.8rem;color:var(--text-secondary);">
                    <i class="fas fa-info-circle me-1" style="color:var(--warning);"></i>
                    <strong>Ao desativar:</strong> O professor não poderá aceder ao sistema e as turmas atribuídas ficarão sem professor.
                    <div style="margin-top:.4rem;color:var(--text-muted);font-size:.75rem;"><i class="fas fa-undo me-1"></i>Esta acção pode ser revertida.</div>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid var(--border);padding:.85rem 1.25rem;">
                <button type="button" class="btn-filter clear" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="confirmDeactivateBtn" class="btn-filter" style="background:var(--danger);color:#fff;">
                    <i class="fas fa-user-slash me-1"></i>Desativar
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- DataTables -->
<!-- <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script> -->

<script>
$(document).ready(function() {
    // Carregar estatísticas iniciais
    loadStats();
    
    // Inicializar DataTable
    var table = $('#teachersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= site_url('admin/teachers/get-table-data') ?>',
            type: 'POST',
            data: function(d) {
                d.status = $('#status').val();
                d.department = $('#department').val();
                d.searchInput = $('#search').val();
                d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
            },
            error: function(xhr, error, thrown) {
                console.log('Erro AJAX:', error);
                console.log('Resposta:', xhr.responseText);
            }
        },
        columns: [
            { data: 'id' },
            { data: 'photo_html' },
            { 
                data: null,
                render: function(data) {
                    return '<strong>' + data.first_name + ' ' + data.last_name + '</strong><br>' +
                           '<small class="text-muted"><i class="fas fa-envelope me-1"></i>' + (data.email || '') + '</small>';
                }
            },
            { 
                data: null,
                render: function(data) {
                    return data.phone ? '<i class="fas fa-phone me-1 text-success"></i>' + data.phone : '-';
                }
            },
            { data: 'total_classes' },
            { 
                data: 'total_workload',
                render: function(data) {
                    return '<span class="code-badge"><i class="fas fa-clock me-1"></i>' + (data || 0) + 'h</span>';
                }
            },
            { 
                data: null,
                render: function(data) {
                    if (data.last_login) {
                        return '<small>' + data.last_login_formatted + '<br><span class="text-muted">' + data.last_login_time + '</span></small>';
                    }
                    return '<span class="text-muted">Nunca acedeu</span>';
                }
            },
            { data: 'status_badge' },
            { data: 'actions' }
        ],
        
        drawCallback: function(settings) {
            // Atualizar contador de registros
            var info = settings.json;
            if (info) {
                $('#recordCount').text(info.recordsFiltered + ' registros');
            }
            
            // Re-inicializar tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });
    
    // Filtrar ao clicar no botão
    $('#btnFilter').on('click', function() {
        table.ajax.reload();
    });
    
    // Filtrar ao mudar selects
    $('#status, #department').on('change', function() {
        table.ajax.reload();
    });
    
    // Busca com debounce
    let searchTimer;
    $('#search').on('keyup', function() {
        clearTimeout(searchTimer);
        searchTimer = setTimeout(() => {
            table.ajax.reload();
        }, 500);
    });
    
    // Limpar filtros
    $('#btnClear').on('click', function() {
        $('#search').val('');
        $('#status').val('');
        $('#department').val('');
        table.ajax.reload();
    });
    
    // Função para carregar estatísticas
    function loadStats() {
        $.ajax({
            url: '<?= site_url('admin/teachers/get-stats') ?>',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                $('#totalTeachers').text(data.totalTeachers || 0);
                $('#activeTeachers').text(data.activeTeachers || 0);
                $('#totalAssignments').text(data.totalAssignments || 0);
                $('#totalWorkload').text((data.totalWorkload || 0) + 'h');
            },
            error: function(xhr, status, error) {
                console.error('Erro ao carregar estatísticas:', error);
            }
        });
    }
    
    // Atualizar estatísticas periodicamente
    setInterval(loadStats, 30000);
});

// Função para confirmar desativação
function confirmDeactivate(id, name) {
    document.getElementById('deactivateTeacherName').textContent = name;
    document.getElementById('confirmDeactivateBtn').href = '<?= site_url('admin/teachers/delete/') ?>' + id;
    new bootstrap.Modal(document.getElementById('deactivateModal')).show();
}
</script>
<?= $this->endSection() ?>