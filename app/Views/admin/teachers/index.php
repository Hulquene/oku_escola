<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<style>

:root {
    --primary:       #1B2B4B;
    --primary-light: #243761;
    --accent:        #3B7FE8;
    --accent-hover:  #2C6FD4;
    --success:       #16A87D;
    --danger:        #E84646;
    --warning:       #E8A020;
    --surface:       #F5F7FC;
    --surface-card:  #FFFFFF;
    --border:        #E2E8F4;
    --text-primary:  #1A2238;
    --text-secondary:#6B7A99;
    --text-muted:    #9AA5BE;
    --shadow-sm:     0 1px 4px rgba(27,43,75,.07);
    --shadow-md:     0 4px 16px rgba(27,43,75,.10);
    --shadow-lg:     0 8px 32px rgba(27,43,75,.14);
    --radius:        12px;
    --radius-sm:     8px;
}

* { font-family:'Sora',sans-serif; box-sizing:border-box; }
body { background:var(--surface); color:var(--text-primary); }

/* ── PAGE HEADER ─────────────────────────────────────────── */
.ci-page-header {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 60%, #2D4A7A 100%);
    border-radius: var(--radius);
    padding: 1.5rem 2rem;
    margin-bottom: 1.5rem;
    position: relative;
    overflow: hidden;
    box-shadow: var(--shadow-lg);
}

.ci-page-header::before {
    content: '';
    position: absolute;
    top: -60px;
    right: -60px;
    width: 200px;
    height: 200px;
    border-radius: 50%;
    background: rgba(255,255,255,.04);
    pointer-events: none;
}

.ci-page-header::after {
    content: '';
    position: absolute;
    bottom: -40px;
    right: 100px;
    width: 130px;
    height: 130px;
    border-radius: 50%;
    background: rgba(59,127,232,.15);
    pointer-events: none;
}

.ci-page-header-inner {
    display: flex;
    align-items: flex-start;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: .75rem;
    position: relative;
    z-index: 1;
}

.ci-page-header h1 {
    font-size: 1.4rem;
    font-weight: 700;
    color: #fff;
    margin: 0 0 .2rem;
    letter-spacing: -.3px;
}

.ci-page-header .breadcrumb {
    margin: 0;
    padding: 0;
    background: transparent;
}

.ci-page-header .breadcrumb-item a {
    color: rgba(255,255,255,.6);
    text-decoration: none;
    font-size: .8rem;
    transition: color .2s;
}

.ci-page-header .breadcrumb-item a:hover {
    color: #fff;
}

.ci-page-header .breadcrumb-item.active,
.ci-page-header .breadcrumb-item + .breadcrumb-item::before {
    color: rgba(255,255,255,.4);
    font-size: .8rem;
}

.hdr-actions {
    display: flex;
    gap: .5rem;
    flex-wrap: wrap;
}

.hdr-btn {
    display: inline-flex;
    align-items: center;
    gap: .45rem;
    border-radius: var(--radius-sm);
    padding: .45rem 1rem;
    font-size: .82rem;
    font-weight: 600;
    text-decoration: none;
    transition: all .18s;
    cursor: pointer;
    border: none;
    font-family: 'Sora', sans-serif;
    white-space: nowrap;
}

.hdr-btn.primary {
    background: var(--accent);
    color: #fff;
    box-shadow: 0 3px 10px rgba(59,127,232,.28);
}

.hdr-btn.primary:hover {
    background: var(--accent-hover);
    color: #fff;
    transform: translateY(-1px);
}

.hdr-btn.secondary {
    background: rgba(255,255,255,.12);
    color: #fff;
    border: 1.5px solid rgba(255,255,255,.2);
}

.hdr-btn.secondary:hover {
    background: rgba(255,255,255,.2);
    color: #fff;
    transform: translateY(-1px);
}

.hdr-btn.success {
    background: var(--success);
    color: #fff;
    box-shadow: 0 3px 10px rgba(22,168,125,.28);
}

.hdr-btn.success:hover {
    background: #12906B;
    color: #fff;
    transform: translateY(-1px);
}

/* ── STAT CARDS ─────────────────────────────────────────── */
.stat-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 1rem;
    margin-bottom: 1.25rem;
}

@media(max-width:900px) {
    .stat-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media(max-width:480px) {
    .stat-grid {
        grid-template-columns: 1fr;
    }
}

.stat-card {
    background: var(--surface-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    padding: 1.1rem 1.25rem;
    box-shadow: var(--shadow-sm);
    display: flex;
    align-items: center;
    gap: 1rem;
}

.stat-icon {
    width: 44px;
    height: 44px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: .95rem;
    flex-shrink: 0;
}

.stat-icon.blue {
    background: rgba(59,127,232,.1);
    color: var(--accent);
}

.stat-icon.green {
    background: rgba(22,168,125,.1);
    color: var(--success);
}

.stat-icon.orange {
    background: rgba(232,160,32,.1);
    color: var(--warning);
}

.stat-icon.navy {
    background: rgba(27,43,75,.08);
    color: var(--primary);
}

.stat-label {
    font-size: .7rem;
    color: var(--text-muted);
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: .07em;
    margin-bottom: .15rem;
}

.stat-value {
    font-size: 1.45rem;
    font-weight: 700;
    color: var(--text-primary);
    font-family: 'JetBrains Mono', monospace;
    line-height: 1;
}

.stat-sub {
    font-size: .68rem;
    color: var(--text-muted);
    margin-top: .18rem;
}

/* ── FILTER CARD ─────────────────────────────────────────── */
.ci-card {
    background: var(--surface-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    margin-bottom: 1.25rem;
}

.ci-card-header {
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: .5rem;
    padding: .85rem 1.25rem;
    background: var(--surface);
    border-bottom: 1px solid var(--border);
}

.ci-card-title {
    display: flex;
    align-items: center;
    gap: .55rem;
    font-size: .82rem;
    font-weight: 700;
    color: var(--text-primary);
}

.ci-card-title i {
    color: var(--accent);
    font-size: .8rem;
}

.ci-card-body {
    padding: 1.1rem 1.25rem;
}

.filter-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: .75rem;
}

@media(max-width:1100px) {
    .filter-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media(max-width:600px) {
    .filter-grid {
        grid-template-columns: 1fr;
    }
}

.filter-label {
    font-size: .72rem;
    font-weight: 600;
    color: var(--text-secondary);
    margin-bottom: .3rem;
    display: block;
}

.filter-select {
    width: 100%;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    padding: .42rem .65rem;
    font-size: .8rem;
    color: var(--text-primary);
    background: var(--surface);
    font-family: 'Sora', sans-serif;
    transition: border-color .18s;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236B7A99' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right .6rem center;
    padding-right: 2rem;
}

.filter-select:focus {
    outline: none;
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(59,127,232,.1);
}

/* Input group para busca */
.input-group {
    display: flex;
    width: 100%;
}

.input-group .input-group-text {
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-right: none;
    border-radius: var(--radius-sm) 0 0 var(--radius-sm);
    color: var(--text-muted);
    padding: 0 .65rem;
    display: flex;
    align-items: center;
}

.input-group .filter-select {
    border-left: none;
    border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
}

.filter-actions {
    display: flex;
    align-items: center;
    gap: .5rem;
    margin-top: 1rem;
}

.btn-filter {
    display: inline-flex;
    align-items: center;
    gap: .35rem;
    padding: .42rem .9rem;
    border-radius: var(--radius-sm);
    font-size: .78rem;
    font-weight: 600;
    border: none;
    cursor: pointer;
    font-family: 'Sora', sans-serif;
    transition: all .18s;
    text-decoration: none;
    white-space: nowrap;
}

.btn-filter.apply {
    background: var(--accent);
    color: #fff;
}

.btn-filter.apply:hover {
    background: var(--accent-hover);
    color: #fff;
}

.btn-filter.clear {
    background: var(--surface);
    color: var(--text-secondary);
    border: 1.5px solid var(--border);
}

.btn-filter.clear:hover {
    border-color: var(--accent);
    color: var(--accent);
}

/* ── TABLE ───────────────────────────────────────────────── */
.ci-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.ci-table thead tr th {
    background: var(--surface);
    color: var(--text-secondary);
    font-size: .65rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: .09em;
    padding: .65rem 1rem;
    border-bottom: 1.5px solid var(--border);
    white-space: nowrap;
}

.ci-table tbody tr {
    border-bottom: 1px solid var(--border);
    transition: background .12s;
}

.ci-table tbody tr:last-child {
    border-bottom: none;
}

.ci-table tbody tr:hover {
    background: #F5F8FF;
}

.ci-table tbody td {
    padding: .7rem 1rem;
    vertical-align: middle;
    font-size: .83rem;
    border: none;
}

.ci-table tbody td.center {
    text-align: center;
}

/* Badges e chips */
.id-chip {
    font-family: 'JetBrains Mono', monospace;
    font-size: .7rem;
    font-weight: 600;
    background: rgba(27,43,75,.07);
    color: var(--primary);
    padding: .15rem .45rem;
    border-radius: 5px;
}

.code-badge {
    font-family: 'JetBrains Mono', monospace;
    font-size: .68rem;
    font-weight: 600;
    background: rgba(59,127,232,.1);
    color: var(--accent);
    padding: .12rem .42rem;
    border-radius: 5px;
}

/* Status badges */
.status-badge {
    font-size: .68rem;
    font-weight: 600;
    padding: .25rem .55rem;
    border-radius: 50px;
    display: inline-block;
}

.status-badge.success {
    background: rgba(22,168,125,.1);
    color: var(--success);
}

.status-badge.warning {
    background: rgba(232,160,32,.1);
    color: var(--warning);
}

.status-badge.danger {
    background: rgba(232,70,70,.1);
    color: var(--danger);
}

/* Foto do professor */
.teacher-photo {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    object-fit: cover;
    border: 2px solid var(--border);
}

.teacher-initials {
    width: 45px;
    height: 45px;
    border-radius: 50%;
    background: var(--accent);
    color: white;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1.2rem;
    text-transform: uppercase;
}

/* Botões de ação */
.action-group {
    display: flex;
    gap: 0.25rem;
    flex-wrap: wrap;
    justify-content: center;
}

.row-btn {
    width: 32px;
    height: 32px;
    border-radius: 8px;
    border: 1.5px solid var(--border);
    background: #fff;
    color: var(--text-secondary);
    display: inline-flex;
    align-items: center;
    justify-content: center;
    font-size: .75rem;
    text-decoration: none;
    cursor: pointer;
    transition: all .18s;
    position: relative;
}

.row-btn:hover {
    color: #fff;
    border-color: transparent;
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.15);
}

.row-btn.view:hover { background: var(--success); }
.row-btn.edit:hover { background: var(--accent); }
.row-btn.assign:hover { background: var(--primary); }
.row-btn.schedule:hover { background: var(--warning); }
.row-btn.del:hover { background: var(--danger); }
.row-btn.activate:hover { background: var(--success); }

/* Tooltips personalizados */
.row-btn[title] {
    position: relative;
}

.row-btn[title]:hover::after {
    content: attr(title);
    position: absolute;
    bottom: -30px;
    left: 50%;
    transform: translateX(-50%);
    background: var(--primary);
    color: white;
    font-size: .7rem;
    padding: 4px 8px;
    border-radius: 4px;
    white-space: nowrap;
    z-index: 1000;
    pointer-events: none;
    box-shadow: 0 2px 8px rgba(0,0,0,0.2);
}

/* DataTables override */
.dataTables_wrapper .dataTables_filter input,
.dataTables_wrapper .dataTables_length select {
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    padding: .3rem .6rem;
    font-size: .8rem;
    font-family: 'Sora', sans-serif;
    color: var(--text-primary);
    background: var(--surface);
}

.dataTables_wrapper .dataTables_filter input:focus,
.dataTables_wrapper .dataTables_length select:focus {
    outline: none;
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(59,127,232,.1);
}

.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_filter label,
.dataTables_wrapper .dataTables_length label {
    font-size: .78rem;
    color: var(--text-secondary);
}

.dataTables_wrapper .dataTables_paginate .paginate_button {
    border-radius: 6px !important;
    font-size: .78rem !important;
    font-family: 'Sora', sans-serif !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button.current,
.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
    background: var(--accent) !important;
    color: #fff !important;
    border-color: var(--accent) !important;
}

.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background: rgba(59,127,232,.1) !important;
    color: var(--accent) !important;
    border-color: var(--border) !important;
}

div.dataTables_wrapper {
    padding: .85rem 1.25rem 1rem;
}

/* Modal */
.modal-custom .modal-content {
    border-radius: var(--radius);
    border: none;
    overflow: hidden;
}

.modal-custom .modal-header {
    background: var(--primary);
    color: white;
    border: none;
    padding: 1rem 1.5rem;
}

.modal-custom .modal-header .btn-close {
    filter: brightness(0) invert(1);
}

.modal-custom .modal-body {
    padding: 1.5rem;
}

.modal-custom .modal-footer {
    border-top: 1px solid var(--border);
    padding: 1rem 1.5rem;
}

/* Responsive */
@media(max-width:767px) {
    .ci-page-header {
        padding: 1.1rem 1.2rem;
    }
    
    .ci-page-header h1 {
        font-size: 1.1rem;
    }
    
    .stat-grid {
        grid-template-columns: 1fr 1fr;
    }
    
    .filter-grid {
        grid-template-columns: 1fr;
    }
}
</style>

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
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

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
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[2, 'asc']],
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100],
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