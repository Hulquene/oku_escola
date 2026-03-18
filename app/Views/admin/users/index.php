<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header -->
<div class="ci-page-header mb-4">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-users me-2"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Utilizadores</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <button class="hdr-btn success" onclick="exportTableData('usersTable', 'excel')">
                <i class="fas fa-file-excel"></i> Exportar
            </button>
            <a href="<?= site_url('admin/users/form-add') ?>" class="hdr-btn primary">
                <i class="fas fa-plus-circle"></i> Novo Utilizador
            </a>
        </div>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Cards de Estatísticas -->
<div class="stat-grid" id="statsContainer">
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-users"></i>
        </div>
        <div>
            <div class="stat-label">Total Utilizadores</div>
            <div class="stat-value" id="totalUsers">0</div>
            <div class="stat-sub">Cadastrados</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-check-circle"></i>
        </div>
        <div>
            <div class="stat-label">Ativos</div>
            <div class="stat-value" id="activeUsers">0</div>
            <div class="stat-sub">Utilizadores</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-user-tie"></i>
        </div>
        <div>
            <div class="stat-label">Administradores</div>
            <div class="stat-value" id="adminUsers">0</div>
            <div class="stat-sub">Perfil admin</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon teal">
            <i class="fas fa-chalkboard-teacher"></i>
        </div>
        <div>
            <div class="stat-label">Professores</div>
            <div class="stat-value" id="teacherUsers">0</div>
            <div class="stat-sub">Docentes</div>
        </div>
    </div>
</div>

<!-- Filtros Avançados -->
<div class="ci-card mb-4">
    <div class="ci-card-header">
        <div class="ci-card-title"><i class="fas fa-filter"></i> Filtros</div>
    </div>
    <div class="ci-card-body">
        <div class="filter-grid">
            <!-- Busca geral -->
            <div>
                <label class="filter-label">Buscar</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" class="filter-select border-start-0" id="search" 
                           placeholder="Nome, username, email...">
                </div>
            </div>
            
            <!-- Perfil -->
            <div>
                <label class="filter-label">Perfil</label>
                <select class="filter-select" id="filter_role">
                    <option value="">Todos os perfis</option>
                    <?php if (!empty($roles)): ?>
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= esc($role['role_name']) ?>"><?= esc($role['role_name']) ?></option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <!-- Tipo -->
            <div>
                <label class="filter-label">Tipo</label>
                <select class="filter-select" id="filter_type">
                    <option value="">Todos</option>
                    <option value="admin">Administrador</option>
                    <option value="teacher">Professor</option>
                    <option value="student">Aluno</option>
                    <option value="guardian">Encarregado</option>
                    <option value="staff">Funcionário</option>
                </select>
            </div>
            
            <!-- Status -->
            <div>
                <label class="filter-label">Status</label>
                <select class="filter-select" id="filter_status">
                    <option value="">Todos</option>
                    <option value="1">Ativo</option>
                    <option value="0">Inativo</option>
                </select>
            </div>
        </div>
        
        <div class="filter-actions">
            <button class="btn-filter apply" id="btnFilter"><i class="fas fa-filter"></i> Filtrar</button>
            <button class="btn-filter clear" id="btnClear"><i class="fas fa-undo"></i> Limpar</button>
        </div>
    </div>
</div>

<!-- Data Table -->
<div class="ci-card">
    <div class="ci-card-header">
        <div class="ci-card-title"><i class="fas fa-list"></i> Lista de Utilizadores</div>
        <span class="badge bg-primary" id="recordCount">0 registros</span>
    </div>
    <div style="overflow-x:auto; padding: 0 1.25rem 1.25rem;">
        <table id="usersTable" class="ci-table" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Foto</th>
                    <th>Nome</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Perfil</th>
                    <th>Tipo</th>
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

<!-- Modal de Confirmação de Eliminação -->
<div class="modal fade modal-custom" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: var(--danger);">
                <h5 class="modal-title text-white">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Eliminação
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja eliminar este utilizador?</p>
                <div class="warning-alert" style="background:rgba(232,160,32,.07); border-left:3px solid var(--warning); padding:.7rem 1rem; border-radius:var(--radius-sm);">
                    <i class="fas fa-info-circle me-1" style="color:var(--warning);"></i>
                    Esta ação não pode ser desfeita.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-filter clear" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="confirmDeleteBtn" class="btn-filter" style="background:var(--danger); color:#fff;">
                    <i class="fas fa-trash me-1"></i>Eliminar
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Modal Reset Password -->
<div class="modal fade modal-custom" id="resetPasswordModal" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <div class="modal-header" style="background: var(--warning);">
                <h5 class="modal-title">
                    <i class="fas fa-key me-2"></i>Redefinir Senha
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body">
                <div class="alert-ci info mb-3">
                    <i class="fas fa-info-circle me-2"></i>
                    <span id="resetUserInfo"></span>
                </div>
                
                <div class="mb-3">
                    <label class="form-label-ci">Nova Senha</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                        <input type="text" class="form-input-ci" id="newPassword" value="" readonly>
                    </div>
                    <div class="form-hint">
                        <i class="fas fa-info-circle"></i>
                        Esta senha será gerada automaticamente
                    </div>
                </div>
                
                <div class="alert-ci warning">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Após redefinir, o utilizador deverá usar esta nova senha no próximo login.
                </div>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn-filter clear" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn-filter apply" id="confirmReset">
                    <i class="fas fa-save me-2"></i>Redefinir Senha
                </button>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Carregar estatísticas iniciais
    loadStats();
    
    // Inicializar DataTable com a configuração global
// No script da view, modifique a inicialização do DataTables:

var table = initDataTable('#usersTable', {
    processing: true,
    serverSide: true,
    ajax: {
        url: '<?= site_url('admin/users/get-table-data') ?>',
        type: 'POST',
        data: function(d) {
            d.filter_role = $('#filter_role').val();
            d.filter_type = $('#filter_type').val();
            d.filter_status = $('#filter_status').val();
            d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
        }
    },
    columns: [
        { data: 'id' },
        { data: 'photo_html' },
        { data: 'name_html' },
        { data: 'username' },
        { data: 'email' },
        { data: 'role_badge' },
        { data: 'type_badge' },
        { data: 'last_login_html' },
        { data: 'status_badge' },
        { data: 'actions' }
    ],
    order: [[2, 'asc']],
    pageLength: 25,
    lengthMenu: [10, 25, 50, 100],
    // REMOVA searching: false OU mude para true
    searching: true, // Para usar a busca padrão do DataTables
    // OU remova completamente a linha para usar o padrão (true)
    drawCallback: function(settings) {
        var info = settings.json;
        if (info) {
            $('#recordCount').text(info.recordsFiltered + ' registros');
        }
    }
});
    
    // Filtrar ao clicar no botão
    $('#btnFilter').on('click', function() {
        table.ajax.reload();
    });
    
    // Filtrar ao mudar selects
    $('#filter_role, #filter_type, #filter_status').on('change', function() {
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
        $('#filter_role').val('');
        $('#filter_type').val('');
        $('#filter_status').val('');
        table.ajax.reload();
    });
    
    // Toggle user status
    $(document).on('click', '.toggle-status', function() {
        const btn = $(this);
        const userId = btn.data('id');
        const userName = btn.data('name');
        const currentStatus = btn.data('status');
        const action = currentStatus ? 'desativar' : 'ativar';
        
        if (confirm(`Tem certeza que deseja ${action} o utilizador ${userName}?`)) {
            $.ajax({
                url: '<?= site_url('admin/users/toggle-active/') ?>' + userId,
                type: 'POST',
                data: {
                    '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
                },
                success: function(response) {
                    if (response.success) {
                        table.ajax.reload(null, false);
                        loadStats(); // Recarregar estatísticas
                    } else {
                        alert(response.message || 'Erro ao alterar status');
                    }
                },
                error: function() {
                    alert('Erro ao comunicar com o servidor');
                }
            });
        }
    });
    
    // Reset password
    let resetUserId = null;
    
    $(document).on('click', '.reset-password', function() {
        resetUserId = $(this).data('id');
        const userName = $(this).data('name');
        
        const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        let password = '';
        for (let i = 0; i < 8; i++) {
            password += chars.charAt(Math.floor(Math.random() * chars.length));
        }
        
        $('#newPassword').val(password);
        $('#resetUserInfo').html(`Redefinir senha para <strong>${userName}</strong>`);
        $('#resetPasswordModal').modal('show');
    });
    
    $('#confirmReset').on('click', function() {
        if (!resetUserId) return;
        
        const newPassword = $('#newPassword').val();
        
        $.ajax({
            url: '<?= site_url('admin/users/reset-password/') ?>' + resetUserId,
            type: 'POST',
            data: {
                password: newPassword,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            success: function(response) {
                if (response.success) {
                    $('#resetPasswordModal').modal('hide');
                    alert('Senha redefinida com sucesso!');
                } else {
                    alert(response.message || 'Erro ao redefinir senha');
                }
            },
            error: function() {
                alert('Erro ao comunicar com o servidor');
            }
        });
    });
    
    // Função para carregar estatísticas
    function loadStats() {
        $.ajax({
            url: '<?= site_url('admin/users/get-stats') ?>',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                $('#totalUsers').text(data.totalUsers || 0);
                $('#activeUsers').text(data.activeUsers || 0);
                $('#adminUsers').text(data.adminUsers || 0);
                $('#teacherUsers').text(data.teacherUsers || 0);
            },
            error: function(xhr, status, error) {
                console.error('Erro ao carregar estatísticas:', error);
            }
        });
    }
    
    // Atualizar estatísticas periodicamente
    setInterval(loadStats, 30000);
});

// Função para confirmar eliminação
function confirmDelete(id) {
    $('#confirmDeleteBtn').attr('href', '<?= site_url('admin/users/delete/') ?>' + id);
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// Exportar para Excel (usando o helper global)
function exportToExcel() {
    exportTableData('usersTable', 'excel');
}
</script>

<style>
/* Estilos específicos para a página de utilizadores */
.user-avatar-table {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--border);
}

.user-initials {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: var(--accent);
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 0.9rem;
    border: 2px solid var(--border);
}

.fw-600 { font-weight: 600; }

/* Ajustes DataTables */
div.dataTables_wrapper {
    padding: 0 !important;
}

.dataTables_length,
.dataTables_filter {
    padding: 1rem 1.25rem 0 !important;
}

.dataTables_info,
.dataTables_paginate {
    padding: 1rem 1.25rem !important;
}

.dt-buttons {
    padding: 1rem 1.25rem 0 !important;
}
</style>
<?= $this->endSection() ?>