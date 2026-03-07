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
.hdr-btn.warning { background:var(--warning); color:#fff; }
.hdr-btn.warning:hover { background:#d18c1c; }

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
.status-badge.success { background:rgba(22,168,125,.1); color:var(--success); }
.status-badge.warning { background:rgba(232,160,32,.1); color:var(--warning); }
.status-badge.info { background:rgba(59,127,232,.1); color:var(--accent); }
.status-badge.primary { background:rgba(27,43,75,.08); color:var(--primary); }
.status-badge.danger { background:rgba(232,70,70,.1); color:var(--danger); }

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
            <h1><i class="fas fa-file-signature me-2"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/students') ?>">Alunos</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Matrículas</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <a href="<?= site_url('admin/students/enrollments/pending') ?>" class="hdr-btn warning">
                <i class="fas fa-clock"></i> Pendentes
            </a>
            <a href="<?= site_url('admin/students/enrollments/form-add') ?>" class="hdr-btn primary">
                <i class="fas fa-plus-circle"></i> Nova Matrícula
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
            <i class="fas fa-file-signature"></i>
        </div>
        <div>
            <div class="stat-label">Total</div>
            <div class="stat-value" id="totalEnrollments">0</div>
            <div class="stat-sub">Matrículas</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-check-circle"></i>
        </div>
        <div>
            <div class="stat-label">Ativas</div>
            <div class="stat-value" id="activeEnrollments">0</div>
            <div class="stat-sub">Em andamento</div>
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
            <i class="fas fa-graduation-cap"></i>
        </div>
        <div>
            <div class="stat-label">Concluídas</div>
            <div class="stat-value" id="completedEnrollments">0</div>
            <div class="stat-sub">Finalizadas</div>
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
            <!-- Ano Letivo -->
            <div>
                <label class="filter-label">Ano Letivo</label>
                <select class="filter-select" id="academic_year">
                    <option value="">Todos os anos</option>
                    <?php if (!empty($academicYears)): ?>
                        <?php foreach ($academicYears as $year): ?>
                            <option value="<?= $year->id ?>" <?= ($selectedYear ?? '') == $year->id ? 'selected' : '' ?>>
                                <?= $year->year_name ?> <?= $year->is_current ? '(Atual)' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <!-- Nível -->
            <div>
                <label class="filter-label">Nível</label>
                <select class="filter-select" id="grade_level">
                    <option value="">Todos os níveis</option>
                    <?php if (!empty($gradeLevels)): ?>
                        <?php foreach ($gradeLevels as $level): ?>
                            <option value="<?= $level->id ?>" <?= ($selectedGradeLevel ?? '') == $level->id ? 'selected' : '' ?>>
                                <?= $level->level_name ?>
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <!-- Curso -->
            <div>
                <label class="filter-label">Curso</label>
                <select class="filter-select" id="course">
                    <option value="">Todos os cursos</option>
                    <option value="0">Ensino Geral</option>
                    <?php if (!empty($courses)): ?>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?= $course->id ?>" <?= ($selectedCourse ?? '') == $course->id ? 'selected' : '' ?>>
                                <?= $course->course_name ?> (<?= $course->course_code ?>)
                            </option>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </select>
            </div>
            
            <!-- Turma -->
            <div>
                <label class="filter-label">Turma</label>
                <select class="filter-select" id="class_id">
                    <option value="">Todas as turmas</option>
                </select>
            </div>
            
            <!-- Status -->
            <div>
                <label class="filter-label">Status</label>
                <select class="filter-select" id="status">
                    <option value="">Todos</option>
                    <option value="Ativo">Ativo</option>
                    <option value="Pendente">Pendente</option>
                    <option value="Concluído">Concluído</option>
                    <option value="Transferido">Transferido</option>
                    <option value="Cancelado">Cancelado</option>
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
        <div class="ci-card-title"><i class="fas fa-list"></i> Lista de Matrículas</div>
        <span class="badge bg-primary" id="recordCount">0 registros</span>
    </div>
    <div style="overflow-x:auto; padding: 0 1.25rem 1.25rem;">
        <table id="enrollmentsTable" class="ci-table" style="width:100%">
            <thead>
                <tr>
                    <th>Nº Matrícula</th>
                    <th>Aluno</th>
                    <th>Nível</th>
                    <th>Curso</th>
                    <th>Turma</th>
                    <th>Ano Letivo</th>
                    <th>Data</th>
                    <th>Tipo</th>
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
                <p>Tem certeza que deseja eliminar esta matrícula?</p>
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
    
    // Carregar turmas baseado no ano letivo selecionado
    loadClasses($('#academic_year').val());
    
    // Inicializar DataTable
    var table = $('#enrollmentsTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= site_url('admin/students/enrollments/get-table-data') ?>',
            type: 'POST',
            data: function(d) {
                d.academic_year = $('#academic_year').val();
                d.grade_level = $('#grade_level').val();
                d.course = $('#course').val();
                d.class_id = $('#class_id').val();
                d.status = $('#status').val();
                d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
            },
            error: function(xhr, error, thrown) {
                console.log('Erro AJAX:', error);
                console.log('Resposta:', xhr.responseText);
            }
        },
        columns: [
            { data: 'enrollment_number_html' },
            { data: 'student_name_html' },
            { data: 'level_html' },
            { data: 'course_html' },
            { data: 'class_html' },
            { data: 'year_name' },
            { data: 'enrollment_date_html' },
            { data: 'enrollment_type_html' },
            { data: 'status_html' },
            { data: 'actions' }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[6, 'desc']],
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
    $('#academic_year, #grade_level, #course, #class_id, #status').on('change', function() {
        table.ajax.reload();
    });
    
    // Atualizar turmas quando ano letivo mudar
    $('#academic_year').change(function() {
        loadClasses($(this).val());
    });
    
    // Limpar filtros
    $('#btnClear').on('click', function() {
        $('#academic_year').val('');
        $('#grade_level').val('');
        $('#course').val('');
        $('#class_id').empty().append('<option value="">Todas as turmas</option>');
        $('#status').val('');
        table.ajax.reload();
    });
    
    // Função para carregar turmas
    function loadClasses(yearId) {
        var classSelect = $('#class_id');
        classSelect.empty().append('<option value="">Carregando...</option>');
        
        if (yearId) {
            $.get('<?= site_url('admin/students/enrollments/get-classes-by-year/') ?>' + yearId, function(classes) {
                classSelect.empty().append('<option value="">Todas as turmas</option>');
                $.each(classes, function(i, cls) {
                    classSelect.append('<option value="' + cls.id + '">' + cls.class_name + ' (' + cls.class_code + ') - ' + cls.class_shift + '</option>');
                });
            }).fail(function() {
                classSelect.empty().append('<option value="">Erro ao carregar turmas</option>');
            });
        } else {
            classSelect.empty().append('<option value="">Todas as turmas</option>');
        }
    }
    
    // Função para carregar estatísticas
    function loadStats() {
        $.ajax({
            url: '<?= site_url('admin/students/enrollments/get-stats') ?>',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                $('#totalEnrollments').text(data.totalEnrollments || 0);
                $('#activeEnrollments').text(data.activeEnrollments || 0);
                $('#pendingEnrollments').text(data.pendingEnrollments || 0);
                $('#completedEnrollments').text(data.completedEnrollments || 0);
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
    $('#confirmDeleteBtn').attr('href', '<?= site_url('admin/students/enrollments/delete/') ?>' + id);
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
<?= $this->endSection() ?>