<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<style>

.id-chip { font-family:'JetBrains Mono',monospace; font-size:.7rem; font-weight:600; background:rgba(27,43,75,.07); color:var(--primary); padding:.15rem .45rem; border-radius:5px; }
.year-name { font-weight:700; color:var(--text-primary); }
.period-text { font-family:'JetBrains Mono',monospace; font-size:.73rem; color:var(--text-secondary); }

.status-dot { display:inline-flex; align-items:center; gap:.3rem; font-size:.72rem; font-weight:700; }
.sd { width:7px; height:7px; border-radius:50%; flex-shrink:0; }
.sd-active   { background:var(--success); box-shadow:0 0 0 2px rgba(22,168,125,.2); }
.sd-inactive { background:var(--text-muted); }
.st-active   { color:var(--success); }
.st-inactive { color:var(--text-muted); }

.current-badge { font-size:.65rem; font-weight:700; padding:.22rem .6rem; border-radius:50px; background:rgba(59,127,232,.12); color:var(--accent); display:inline-flex; align-items:center; gap:.25rem; }
.btn-set-current { display:inline-flex; align-items:center; gap:.35rem; padding:.22rem .7rem; font-size:.72rem; font-weight:600; border-radius:6px; border:1.5px solid var(--border); background:#fff; color:var(--text-muted); text-decoration:none; cursor:pointer; transition:all .18s; white-space:nowrap; }
.btn-set-current:hover { border-color:var(--accent); background:rgba(59,127,232,.08); color:var(--accent); }

.row-btn { width:28px; height:28px; border-radius:7px; border:1.5px solid var(--border); background:#fff; color:var(--text-secondary); display:inline-flex; align-items:center; justify-content:center; font-size:.72rem; text-decoration:none; cursor:pointer; transition:all .18s; }
.row-btn:hover { color:#fff; border-color:transparent; }
.row-btn.edit:hover { background:var(--accent); }
.row-btn.view:hover { background:var(--success); }
.row-btn.del:hover  { background:var(--danger); }

.ci-empty { text-align:center; padding:3rem; color:var(--text-muted); }
.ci-empty i { font-size:2rem; opacity:.15; display:block; margin-bottom:.6rem; }
.ci-empty p { font-size:.82rem; margin:0; }

.alert { border-radius:var(--radius-sm); border:none; font-size:.875rem; }
.alert-success { background:rgba(22,168,125,.1); color:#0E7A5A; border-left:3px solid var(--success); }
.alert-danger  { background:rgba(232,70,70,.08); color:#B03030; border-left:3px solid var(--danger); }

@media (max-width:767px) {
    .ci-page-header { padding:1.1rem 1.2rem; }
    .ci-page-header h1 { font-size:1.1rem; }
}
</style>

<!-- ── PAGE HEADER ─────────────────────────────────────── -->
<div class="ci-page-header mb-4">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-graduation-cap me-2" style="opacity:.7;font-size:1.1rem;"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Anos Letivos</li>
                </ol>
            </nav>
        </div>
        <a href="<?= site_url('admin/academic/years/form-add') ?>" class="hdr-btn primary">
            <i class="fas fa-plus-circle"></i> Novo Ano Letivo
        </a>
    </div>
</div>

<?= view('admin/partials/alerts') ?>

<!-- ── TABLE ───────────────────────────────────────────── -->
<div class="ci-card">
    <div class="ci-card-header">
        <div class="ci-card-title"><i class="fas fa-list"></i> Lista de Anos Letivos</div>
    </div>
    <div style="overflow-x:auto;">
        <table id="yearsTable" class="ci-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ano Letivo</th>
                    <th>Início</th>
                    <th>Fim</th>
                    <th>Estado</th>
                    <th class="center">Atual</th>
                    <th class="center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($years)): ?>
                    <?php foreach ($years as $year): ?>
                    <tr>
                        <td><span class="id-chip"><?= $year['id'] ?></span></td>
                        <td><span class="year-name"><?= esc($year['year_name']) ?></span></td>
                        <td><span class="period-text"><?= date('d/m/Y', strtotime($year['start_date'])) ?></span></td>
                        <td><span class="period-text"><?= date('d/m/Y', strtotime($year['end_date'])) ?></span></td>
                        <td>
                            <?php if ($year['is_active']): ?>
                                <span class="status-dot st-active"><span class="sd sd-active"></span>Ativo</span>
                            <?php else: ?>
                                <span class="status-dot st-inactive"><span class="sd sd-inactive"></span>Inativo</span>
                            <?php endif; ?>
                        </td>
                        <td class="center">
                            <?php if ($year['id'] == current_academic_year()): ?>
                                <span class="current-badge"><i class="fas fa-star" style="font-size:.6rem;"></i>Atual</span>
                            <?php else: ?>
                                <a href="<?= site_url('admin/academic/years/set-current/' . $year['id']) ?>"
                                   class="btn-set-current"
                                   onclick="return confirm('Definir este ano como atual?')">
                                    <i class="fas fa-check-circle"></i> Definir
                                </a>
                            <?php endif; ?>
                        </td>
                        <td class="center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="<?= site_url('admin/academic/years/form-edit/' . $year['id']) ?>" class="row-btn edit" title="Editar"><i class="fas fa-edit"></i></a>
                                <a href="<?= site_url('admin/academic/years/view/' . $year['id']) ?>"    class="row-btn view" title="Ver Detalhes"><i class="fas fa-eye"></i></a>
                                <?php if (!$year['id'] == current_academic_year()): ?>
                                    <a href="<?= site_url('admin/academic/years/delete/' . $year['id']) ?>"
                                       class="row-btn del" title="Eliminar"
                                       onclick="return confirm('Tem certeza que deseja eliminar este ano letivo?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7"><div class="ci-empty"><i class="fas fa-calendar-times"></i><p>Nenhum ano letivo encontrado</p></div></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function() {
    // Carregar estatísticas iniciais
    loadStats();
    
    // Inicializar DataTable 2
    var table = $('#usersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= site_url('admin/users/get-table-data') ?>',
            type: 'POST',
            data: function(d) {
                // DataTables 2 envia os parâmetros automaticamente
                // Adicionar filtros customizados
                d.filter_role = $('#filter_role').val();
                d.filter_type = $('#filter_type').val();
                d.filter_status = $('#filter_status').val();
                d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
                
                console.log('Enviando dados:', d);
            },
            error: function(xhr, error, thrown) {
                console.log('Erro AJAX:', error);
                console.log('Status:', xhr.status);
                console.log('Resposta:', xhr.responseText);
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
       
        drawCallback: function(settings) {
            var info = settings.json;
            if (info) {
                $('#recordCount').text(info.recordsFiltered + ' registros');
            }
            
            // Re-inicializar tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();
        },
        initComplete: function() {
            console.log('DataTable 2 inicializado com sucesso');
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
            // No DataTables 2, a busca é feita via parâmetro search
            table.search($(this).val()).draw();
        }, 500);
    });
    
    // Limpar filtros
    $('#btnClear').on('click', function() {
        $('#search').val('');
        $('#filter_role').val('');
        $('#filter_type').val('');
        $('#filter_status').val('');
        table.search('').ajax.reload();
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
                        loadStats();
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

// Função para confirmar eliminação
function confirmDelete(id) {
    $('#confirmDeleteBtn').attr('href', '<?= site_url('admin/users/delete/') ?>' + id);
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// Exportar para Excel
function exportToExcel() {
    if (typeof exportTableData === 'function') {
        exportTableData('usersTable', 'excel');
    } else {
        alert('Função de exportação não disponível');
    }
}
</script>
<?= $this->endSection() ?>