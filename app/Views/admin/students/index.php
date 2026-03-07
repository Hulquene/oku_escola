<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap');

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
    --shadow-sm:     0 1px 4px rgba(27,43,75,0.07);
    --shadow-md:     0 4px 16px rgba(27,43,75,0.10);
    --shadow-lg:     0 8px 32px rgba(27,43,75,0.14);
    --radius:        12px;
    --radius-sm:     8px;
}

* { font-family: 'Sora', sans-serif; box-sizing: border-box; }
body { background: var(--surface); color: var(--text-primary); }

/* ── PAGE HEADER ─────────────────────────────────────── */
.ci-page-header {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 60%, #2D4A7A 100%);
    border-radius: var(--radius); padding: 1.5rem 2rem; margin-bottom: 1.5rem;
    position: relative; overflow: hidden; box-shadow: var(--shadow-lg);
}
.ci-page-header::before { content:''; position:absolute; top:-60px; right:-60px; width:200px; height:200px; border-radius:50%; background:rgba(255,255,255,0.04); pointer-events:none; }
.ci-page-header::after  { content:''; position:absolute; bottom:-40px; right:100px; width:130px; height:130px; border-radius:50%; background:rgba(59,127,232,0.15); pointer-events:none; }
.ci-page-header-inner   { display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:.75rem; position:relative; z-index:1; }
.ci-page-header h1      { font-size:1.4rem; font-weight:700; color:#fff; margin:0 0 .2rem; letter-spacing:-.3px; }
.ci-page-header .breadcrumb { margin:0; padding:0; background:transparent; position:relative; z-index:1; }
.ci-page-header .breadcrumb-item a { color:rgba(255,255,255,0.6); text-decoration:none; font-size:.8rem; transition:color .2s; }
.ci-page-header .breadcrumb-item a:hover { color:#fff; }
.ci-page-header .breadcrumb-item.active,
.ci-page-header .breadcrumb-item + .breadcrumb-item::before { color:rgba(255,255,255,0.4); font-size:.8rem; }

/* Header action buttons */
.hdr-actions { display:flex; gap:.5rem; flex-wrap:wrap; }
.hdr-btn {
    display:inline-flex; align-items:center; gap:.45rem;
    border-radius:var(--radius-sm); padding:.45rem 1rem;
    font-size:.82rem; font-weight:600; text-decoration:none;
    transition:all .18s; white-space:nowrap; cursor:pointer; border:none;
    font-family:'Sora',sans-serif;
}
.hdr-btn.primary { background:var(--accent); color:#fff; box-shadow:0 3px 10px rgba(59,127,232,.28); }
.hdr-btn.primary:hover { background:var(--accent-hover); color:#fff; transform:translateY(-1px); }
.hdr-btn.success { background:var(--success); color:#fff; box-shadow:0 3px 10px rgba(22,168,125,.25); }
.hdr-btn.success:hover { background:#0E8A64; color:#fff; transform:translateY(-1px); }
.hdr-btn.warning { background:var(--warning); color:#fff; }

/* ── FILTER CARD ──────────────────────────────────────── */
.ci-card {
    background:var(--surface-card); border:1px solid var(--border);
    border-radius:var(--radius); box-shadow:var(--shadow-sm); overflow:hidden; margin-bottom:1.25rem;
}
.ci-card-header {
    display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.5rem;
    padding:.85rem 1.25rem; background:var(--surface); border-bottom:1px solid var(--border);
}
.ci-card-title { display:flex; align-items:center; gap:.55rem; font-size:.82rem; font-weight:700; color:var(--text-primary); }
.ci-card-title i { color:var(--accent); font-size:.8rem; }
.ci-card-body { padding:1.25rem; }

.filter-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: .75rem;
}

.filter-label {
    font-size:.72rem;
    font-weight:600;
    color:var(--text-secondary);
    margin-bottom:.3rem;
    display:block;
}

.filter-select {
    width:100%;
    border:1.5px solid var(--border);
    border-radius:var(--radius-sm);
    padding:.42rem .65rem;
    font-size:.8rem;
    color:var(--text-primary);
    background:var(--surface);
    transition:border-color .18s;
    appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236B7A99' d='M6 8L1 3h10z'/%3E%3C/svg%3E");
    background-repeat: no-repeat;
    background-position: right .6rem center;
    padding-right: 2rem;
}

.filter-select:focus {
    outline:none;
    border-color:var(--accent);
    box-shadow:0 0 0 3px rgba(59,127,232,.12);
    background:#fff;
}

.filter-actions {
    display:flex;
    align-items:center;
    gap:.5rem;
    margin-top: 1rem;
}

.btn-filter {
    display:inline-flex;
    align-items:center;
    gap:.4rem;
    border-radius:var(--radius-sm);
    padding:.5rem 1.1rem;
    font-size:.82rem;
    font-weight:600;
    cursor:pointer;
    transition:all .18s;
    border:none;
    font-family:'Sora',sans-serif;
    text-decoration:none;
}

.btn-filter.apply {
    background:var(--accent);
    color:#fff;
    box-shadow:0 2px 8px rgba(59,127,232,.25);
}

.btn-filter.apply:hover {
    background:var(--accent-hover);
    color:#fff;
}

.btn-filter.clear {
    background:var(--surface);
    border:1.5px solid var(--border);
    color:var(--text-secondary);
}

.btn-filter.clear:hover {
    background:#fff;
    color:var(--primary);
    border-color:var(--primary);
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

/* ── TABLE ────────────────────────────────────────────── */
.ci-table {
    width:100%;
    border-collapse: separate;
    border-spacing: 0;
}
.ci-table thead tr th {
    background:var(--surface);
    color:var(--text-secondary);
    font-size:.65rem;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.09em;
    padding:.65rem 1rem;
    border-bottom:1.5px solid var(--border);
    white-space:nowrap;
}
.ci-table tbody tr {
    border-bottom:1px solid var(--border);
    transition:background .12s;
}
.ci-table tbody tr:last-child {
    border-bottom:none;
}
.ci-table tbody tr:hover {
    background:#F5F8FF;
}
.ci-table tbody td {
    padding:.7rem 1rem;
    vertical-align:middle;
    font-size:.83rem;
    border:none;
}
.ci-table tbody td.center {
    text-align:center;
}

/* Badges */
.student-number {
    font-family:'JetBrains Mono',monospace;
    font-size:.7rem;
    font-weight:600;
    background:rgba(59,127,232,.1);
    color:var(--accent);
    padding:.15rem .45rem;
    border-radius:5px;
    display:inline-block;
}

.course-badge {
    font-size:.65rem;
    font-weight:700;
    padding:.2rem .55rem;
    border-radius:50px;
    display:inline-flex;
    align-items:center;
    gap:.25rem;
    white-space:nowrap;
}
.course-badge.primary { background:rgba(59,127,232,.1); color:var(--accent); }
.course-badge.warning { background:rgba(232,160,32,.1); color:var(--warning); }
.course-badge.secondary { background:rgba(27,43,75,.08); color:var(--primary); }

.level-badge {
    font-size:.68rem;
    font-weight:600;
    padding:.18rem .55rem;
    border-radius:50px;
    display:inline-block;
}
.level-badge.success { background:rgba(22,168,125,.1); color:var(--success); }
.level-badge.warning { background:rgba(232,160,32,.1); color:var(--warning); }

.status-badge {
    font-size:.65rem;
    font-weight:700;
    padding:.22rem .6rem;
    border-radius:50px;
    display:inline-flex;
    align-items:center;
    gap:.25rem;
    white-space:nowrap;
}
.status-badge.active { background:rgba(22,168,125,.1); color:var(--success); }
.status-badge.inactive { background:rgba(232,70,70,.1); color:var(--danger); }

/* Row action buttons */
.action-group {
    display: flex;
    gap: 0.25rem;
    flex-wrap: wrap;
    justify-content: center;
}
.row-btn {
    width:28px;
    height:28px;
    border-radius:7px;
    border:1.5px solid var(--border);
    background:#fff;
    color:var(--text-secondary);
    display:inline-flex;
    align-items:center;
    justify-content:center;
    font-size:.72rem;
    text-decoration:none;
    cursor:pointer;
    transition:all .18s;
}
.row-btn:hover {
    color:#fff;
    border-color:transparent;
    transform:translateY(-1px);
}
.row-btn.view:hover    { background:var(--success); }
.row-btn.edit:hover    { background:var(--accent); }
.row-btn.history:hover { background:var(--primary); }
.row-btn.enroll:hover  { background:var(--warning); }
.row-btn.del:hover     { background:var(--danger); }

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
    border:1.5px solid var(--border);
    border-radius:var(--radius-sm);
    padding:.3rem .6rem;
    font-size:.8rem;
    font-family:'Sora',sans-serif;
    color:var(--text-primary);
    background:var(--surface);
}
.dataTables_wrapper .dataTables_filter input:focus,
.dataTables_wrapper .dataTables_length select:focus {
    outline:none;
    border-color:var(--accent);
    box-shadow:0 0 0 3px rgba(59,127,232,.12);
}
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_filter label,
.dataTables_wrapper .dataTables_length label {
    font-size:.78rem;
    color:var(--text-secondary);
}
.dataTables_wrapper .dataTables_paginate .paginate_button {
    border-radius:6px !important;
    font-size:.78rem !important;
    font-family:'Sora',sans-serif !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button.current,
.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover {
    background:var(--accent) !important;
    color:#fff !important;
    border-color:var(--accent) !important;
}
.dataTables_wrapper .dataTables_paginate .paginate_button:hover {
    background:rgba(59,127,232,.1) !important;
    color:var(--accent) !important;
    border-color:var(--border) !important;
}
div.dataTables_wrapper {
    padding:.85rem 1.25rem 1rem;
}

/* ── MODAL ────────────────────────────────────────────── */
.modal-custom .modal-content {
    border:none;
    border-radius:var(--radius);
    overflow:hidden;
    box-shadow:var(--shadow-lg);
}
.modal-custom .modal-header {
    background:var(--danger);
    color:#fff;
    border:none;
    padding:1rem 1.5rem;
}
.modal-custom .modal-header .btn-close {
    filter:brightness(0) invert(1);
    opacity:.8;
}
.modal-custom .modal-body {
    padding:1.5rem;
}
.modal-custom .modal-footer {
    border-top:1px solid var(--border);
    padding:1rem 1.5rem;
}

/* Responsive */
@media (max-width:767px) {
    .ci-page-header {
        padding:1.1rem 1.2rem;
    }
    .ci-page-header h1 {
        font-size:1.1rem;
    }
    .stat-grid {
        grid-template-columns:1fr 1fr;
    }
    .filter-grid {
        grid-template-columns:1fr;
    }
}
</style>

<!-- Page Header -->
<div class="ci-page-header mb-4">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-user-graduate me-2"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Alunos</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <button class="hdr-btn success" onclick="exportToExcel()">
                <i class="fas fa-file-excel"></i> Exportar
            </button>
            <a href="<?= site_url('admin/students/form-add') ?>" class="hdr-btn primary">
                <i class="fas fa-plus-circle"></i> Novo Aluno
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
            <i class="fas fa-user-graduate"></i>
        </div>
        <div>
            <div class="stat-label">Total Alunos</div>
            <div class="stat-value" id="totalStudents">0</div>
            <div class="stat-sub">Cadastrados</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-check-circle"></i>
        </div>
        <div>
            <div class="stat-label">Matriculados</div>
            <div class="stat-value" id="enrolledStudents">0</div>
            <div class="stat-sub">Ativos</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-clock"></i>
        </div>
        <div>
            <div class="stat-label">Pendentes</div>
            <div class="stat-value" id="pendingEnrollments">0</div>
            <div class="stat-sub">Aguardando</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon navy">
            <i class="fas fa-venus-mars"></i>
        </div>
        <div>
            <div class="stat-label">Masculino/Feminino</div>
            <div class="stat-value" id="genderStats">0/0</div>
            <div class="stat-sub">Distribuição</div>
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
                           placeholder="Nome, nº matrícula, email...">
                </div>
            </div>
            
            <!-- Ano Letivo -->
            <div>
                <label class="filter-label">Ano Letivo</label>
                <select class="filter-select" id="academic_year">
                    <option value="">Todos</option>
                    <?php foreach ($academicYears as $year): ?>
                        <option value="<?= $year->id ?>" <?= current_academic_year() == $year->id ? 'selected' : '' ?>>
                            <?= $year->year_name ?> <?= current_academic_year() == $year->id ? '(Atual)' : '' ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Curso -->
            <div>
                <label class="filter-label">Curso</label>
                <select class="filter-select" id="course_id">
                    <option value="">Todos os cursos</option>
                    <option value="0">Ensino Geral</option>
                    <?php if (!empty($courses)): ?>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?= $course->id ?>" <?= ($selectedCourse ?? '') == $course->id ? 'selected' : '' ?>>
                                <?= esc($course->course_name) ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <!-- Status Matrícula -->
            <div>
                <label class="filter-label">Status Matrícula</label>
                <select class="filter-select" id="enrollment_status">
                    <option value="">Todos</option>
                    <option value="Ativo">Matriculado</option>
                    <option value="Pendente">Pendente</option>
                    <option value="Concluído">Concluído</option>
                    <option value="Transferido">Transferido</option>
                    <option value="nao_matriculado">Não Matriculado</option>
                </select>
            </div>
            
            <!-- Gênero -->
            <div>
                <label class="filter-label">Gênero</label>
                <select class="filter-select" id="gender">
                    <option value="">Todos</option>
                    <option value="Masculino">Masculino</option>
                    <option value="Feminino">Feminino</option>
                </select>
            </div>
            
            <!-- Status Aluno -->
            <div>
                <label class="filter-label">Status Aluno</label>
                <select class="filter-select" id="status">
                    <option value="">Todos</option>
                    <option value="active">Ativo</option>
                    <option value="inactive">Inativo</option>
                </select>
            </div>
            
            <!-- Data Cadastro -->
            <div>
                <label class="filter-label">Data Cadastro</label>
                <input type="date" class="filter-select" id="created_date" value="<?= $selectedCreatedDate ?? '' ?>">
            </div>
            
            <!-- Mês/Ano Cadastro -->
            <div>
                <label class="filter-label">Mês/Ano</label>
                <input type="month" class="filter-select" id="created_month" value="<?= $selectedCreatedMonth ?? '' ?>">
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
        <div class="ci-card-title"><i class="fas fa-list"></i> Lista de Alunos</div>
        <span class="badge bg-primary" id="recordCount">0 registros</span>
    </div>
    <div style="overflow-x:auto; padding: 0 1.25rem 1.25rem;">
        <table id="studentsTable" class="ci-table" style="width:100%">
            <thead>
                <tr>
                    <th>Nº Matrícula</th>
                    <th>Foto</th>
                    <th>Nome Completo / Contacto</th>
                    <th>Curso</th>
                    <th>Nível / Ano Letivo</th>
                    <th>Gênero</th>
                    <th>Status</th>
                    <th>Data Cadastro</th>
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
                <p>Tem certeza que deseja eliminar este aluno?</p>
                <div class="warning-alert" style="background:rgba(232,160,32,.07); border-left:3px solid var(--warning); padding:.7rem 1rem; border-radius:var(--radius-sm);">
                    <i class="fas fa-info-circle me-1" style="color:var(--warning);"></i>
                    Esta ação não pode ser desfeita. O aluno será desativado do sistema.
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
    var table = $('#studentsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= site_url('admin/students/get-table-data') ?>',
            type: 'POST',
            data: function(d) {
                d.academic_year = $('#academic_year').val();
                d.course_id = $('#course_id').val();
                d.enrollment_status = $('#enrollment_status').val();
                d.gender = $('#gender').val();
                d.status = $('#status').val();
                d.created_date = $('#created_date').val();
                d.created_month = $('#created_month').val();
                d.search = $('#search').val();
                d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
            },
            error: function(xhr, error, thrown) {
                console.log('Erro AJAX:', error);
                console.log('Resposta:', xhr.responseText);
            }
        },
        columns: [
            { 
                data: 'student_number',
                render: function(data) {
                    return '<span class="student-number">' + (data || '') + '</span>';
                }
            },
            { data: 'photo_html' },
            { data: 'name_html' },
            { data: 'course_html' },
            { data: 'level_html' },
            { data: 'gender' },
            { data: 'status_badge' },
            { data: 'created_at_html' },
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
    
    // Filtrar ao mudar selects com debounce
    let filterTimer;
    $('#academic_year, #course_id, #enrollment_status, #gender, #status, #created_date, #created_month').on('change', function() {
        clearTimeout(filterTimer);
        filterTimer = setTimeout(() => {
            table.ajax.reload();
        }, 500);
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
        $('#academic_year').val('');
        $('#course_id').val('');
        $('#enrollment_status').val('');
        $('#gender').val('');
        $('#status').val('');
        $('#created_date').val('');
        $('#created_month').val('');
        table.ajax.reload();
    });
    
    // Função para carregar estatísticas
    function loadStats() {
        $.ajax({
            url: '<?= site_url('admin/students/get-stats') ?>',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                $('#totalStudents').text(data.totalStudents || 0);
                $('#enrolledStudents').text(data.enrolledStudents || 0);
                $('#pendingEnrollments').text(data.pendingEnrollments || 0);
                $('#genderStats').text((data.maleCount || 0) + '/' + (data.femaleCount || 0));
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
    $('#confirmDeleteBtn').attr('href', '<?= site_url('admin/students/delete/') ?>' + id);
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

// Exportar para Excel
function exportToExcel() {
    const table = document.getElementById('studentsTable');
    const rows = Array.from(table.querySelectorAll('tr'));
    
    let csv = [];
    
    rows.forEach(row => {
        const cells = Array.from(row.querySelectorAll('th, td'));
        const filteredCells = cells.filter((_, index) => index !== 1 && index !== cells.length - 1);
        const rowData = filteredCells.map(cell => cell.innerText.trim().replace(/,/g, ';'));
        csv.push(rowData.join(','));
    });
    
    const csvContent = csv.join('\n');
    const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    const url = URL.createObjectURL(blob);
    
    link.setAttribute('href', url);
    link.setAttribute('download', 'alunos_' + new Date().toISOString().split('T')[0] + '.csv');
    link.click();
}
</script>
<?= $this->endSection() ?>