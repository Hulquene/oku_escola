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
    --shadow-sm:     0 1px 4px rgba(27,43,75,.07);
    --shadow-md:     0 4px 16px rgba(27,43,75,.10);
    --shadow-lg:     0 8px 32px rgba(27,43,75,.14);
    --radius:        12px;
    --radius-sm:     8px;
}
* { font-family:'Sora',sans-serif; box-sizing:border-box; }
body { background:var(--surface); color:var(--text-primary); }

/* ── PAGE HEADER ─────────────────────────────────────────── */
.ci-page-header { background:linear-gradient(135deg,var(--primary) 0%,var(--primary-light) 60%,#2D4A7A 100%); border-radius:var(--radius); padding:1.5rem 2rem; margin-bottom:1.5rem; position:relative; overflow:hidden; box-shadow:var(--shadow-lg); }
.ci-page-header::before { content:''; position:absolute; top:-60px; right:-60px; width:200px; height:200px; border-radius:50%; background:rgba(255,255,255,.04); pointer-events:none; }
.ci-page-header::after  { content:''; position:absolute; bottom:-40px; right:100px; width:130px; height:130px; border-radius:50%; background:rgba(59,127,232,.15); pointer-events:none; }
.ci-page-header-inner   { display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:.75rem; position:relative; z-index:1; }
.ci-page-header h1 { font-size:1.4rem; font-weight:700; color:#fff; margin:0 0 .2rem; letter-spacing:-.3px; }
.ci-page-header .breadcrumb { margin:0; padding:0; background:transparent; }
.ci-page-header .breadcrumb-item a { color:rgba(255,255,255,.6); text-decoration:none; font-size:.8rem; transition:color .2s; }
.ci-page-header .breadcrumb-item a:hover { color:#fff; }
.ci-page-header .breadcrumb-item.active,
.ci-page-header .breadcrumb-item + .breadcrumb-item::before { color:rgba(255,255,255,.4); font-size:.8rem; }
.hdr-actions { display:flex; gap:.5rem; flex-wrap:wrap; }
.hdr-btn { display:inline-flex; align-items:center; gap:.45rem; border-radius:var(--radius-sm); padding:.45rem 1rem; font-size:.82rem; font-weight:600; text-decoration:none; transition:all .18s; cursor:pointer; border:none; font-family:'Sora',sans-serif; white-space:nowrap; }
.hdr-btn.primary   { background:var(--accent); color:#fff; box-shadow:0 3px 10px rgba(59,127,232,.28); }
.hdr-btn.primary:hover { background:var(--accent-hover); color:#fff; transform:translateY(-1px); }
.hdr-btn.secondary { background:rgba(255,255,255,.12); color:#fff; border:1.5px solid rgba(255,255,255,.2); }
.hdr-btn.secondary:hover { background:rgba(255,255,255,.2); color:#fff; transform:translateY(-1px); }
.hdr-btn.success   { background:var(--success); color:#fff; box-shadow:0 3px 10px rgba(22,168,125,.28); }
.hdr-btn.success:hover { background:#12906B; color:#fff; transform:translateY(-1px); }

/* ── STAT CARDS ─────────────────────────────────────────── */
.stat-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:1rem; margin-bottom:1.25rem; }
@media(max-width:900px){ .stat-grid { grid-template-columns:repeat(2,1fr); } }
@media(max-width:480px){ .stat-grid { grid-template-columns:1fr; } }
.stat-card { background:var(--surface-card); border:1px solid var(--border); border-radius:var(--radius); padding:1.1rem 1.25rem; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:1rem; }
.stat-icon { width:44px; height:44px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:.95rem; flex-shrink:0; }
.stat-icon.blue   { background:rgba(59,127,232,.1);  color:var(--accent); }
.stat-icon.green  { background:rgba(22,168,125,.1);  color:var(--success); }
.stat-icon.orange { background:rgba(232,160,32,.1);  color:var(--warning); }
.stat-icon.navy   { background:rgba(27,43,75,.08);   color:var(--primary); }
.stat-label { font-size:.7rem; color:var(--text-muted); font-weight:600; text-transform:uppercase; letter-spacing:.07em; margin-bottom:.15rem; }
.stat-value { font-size:1.45rem; font-weight:700; color:var(--text-primary); font-family:'JetBrains Mono',monospace; line-height:1; }
.stat-sub   { font-size:.68rem; color:var(--text-muted); margin-top:.18rem; }

/* ── FILTER CARD ─────────────────────────────────────────── */
.ci-card { background:var(--surface-card); border:1px solid var(--border); border-radius:var(--radius); box-shadow:var(--shadow-sm); overflow:hidden; margin-bottom:1.25rem; }
.ci-card-header { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.5rem; padding:.85rem 1.25rem; background:var(--surface); border-bottom:1px solid var(--border); }
.ci-card-title  { display:flex; align-items:center; gap:.55rem; font-size:.82rem; font-weight:700; color:var(--text-primary); }
.ci-card-title i { color:var(--accent); font-size:.8rem; }
.ci-card-body   { padding:1.1rem 1.25rem; }

.filter-grid { display:grid; grid-template-columns:repeat(6,1fr); gap:.75rem; }
@media(max-width:1100px){ .filter-grid { grid-template-columns:repeat(3,1fr); } }
@media(max-width:600px) { .filter-grid { grid-template-columns:1fr 1fr; } }

.filter-label { font-size:.72rem; font-weight:600; color:var(--text-secondary); margin-bottom:.3rem; display:block; }
.filter-select { width:100%; border:1.5px solid var(--border); border-radius:var(--radius-sm); padding:.42rem .65rem; font-size:.8rem; color:var(--text-primary); background:var(--surface); font-family:'Sora',sans-serif; transition:border-color .18s; appearance:none; background-image:url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236B7A99' d='M6 8L1 3h10z'/%3E%3C/svg%3E"); background-repeat:no-repeat; background-position:right .6rem center; padding-right:2rem; }
.filter-select:focus { outline:none; border-color:var(--accent); box-shadow:0 0 0 3px rgba(59,127,232,.1); }

.filter-actions { display:flex; align-items:flex-end; gap:.5rem; }
.btn-filter { display:inline-flex; align-items:center; gap:.35rem; padding:.42rem .9rem; border-radius:var(--radius-sm); font-size:.78rem; font-weight:600; border:none; cursor:pointer; font-family:'Sora',sans-serif; transition:all .18s; text-decoration:none; white-space:nowrap; }
.btn-filter.apply  { background:var(--accent); color:#fff; }
.btn-filter.apply:hover  { background:var(--accent-hover); color:#fff; }
.btn-filter.clear  { background:var(--surface); color:var(--text-secondary); border:1.5px solid var(--border); }
.btn-filter.clear:hover  { border-color:var(--accent); color:var(--accent); }

/* Active filter badges */
.active-filters { display:flex; flex-wrap:wrap; gap:.4rem; align-items:center; padding:.65rem 1.25rem; border-top:1px solid var(--border); background:rgba(59,127,232,.03); }
.af-label { font-size:.7rem; color:var(--text-muted); font-weight:600; }
.af-badge { display:inline-flex; align-items:center; gap:.35rem; padding:.22rem .6rem; border-radius:50px; font-size:.7rem; font-weight:600; }
.af-badge.blue   { background:rgba(59,127,232,.1);  color:var(--accent); }
.af-badge.green  { background:rgba(22,168,125,.1);  color:var(--success); }
.af-badge.orange { background:rgba(232,160,32,.1);  color:var(--warning); }
.af-badge.navy   { background:rgba(27,43,75,.08);   color:var(--primary); }
.af-badge a { color:inherit; text-decoration:none; opacity:.6; transition:opacity .15s; margin-left:.15rem; }
.af-badge a:hover { opacity:1; }

/* ── TABLE ───────────────────────────────────────────────── */
.ci-table { width:100%; border-collapse:separate; border-spacing:0; }
.ci-table thead tr th { background:var(--surface); color:var(--text-secondary); font-size:.65rem; font-weight:700; text-transform:uppercase; letter-spacing:.09em; padding:.65rem 1rem; border-bottom:1.5px solid var(--border); white-space:nowrap; }
.ci-table tbody tr { border-bottom:1px solid var(--border); transition:background .12s; }
.ci-table tbody tr:last-child { border-bottom:none; }
.ci-table tbody tr:hover { background:#F5F8FF; }
.ci-table tbody td { padding:.7rem 1rem; vertical-align:middle; font-size:.83rem; border:none; }
.ci-table tbody td.center { text-align:center; }

.id-chip    { font-family:'JetBrains Mono',monospace; font-size:.7rem; font-weight:600; background:rgba(27,43,75,.07); color:var(--primary); padding:.15rem .45rem; border-radius:5px; }
.code-badge { font-family:'JetBrains Mono',monospace; font-size:.68rem; font-weight:600; background:rgba(59,127,232,.1); color:var(--accent); padding:.12rem .42rem; border-radius:5px; }
.class-name { font-weight:700; color:var(--text-primary); display:block; }

.shift-badge { font-size:.65rem; font-weight:700; padding:.2rem .55rem; border-radius:50px; display:inline-flex; align-items:center; gap:.25rem; white-space:nowrap; }
.shift-manha  { background:rgba(59,127,232,.1);  color:var(--accent); }
.shift-tarde  { background:rgba(232,160,32,.1);  color:var(--warning); }
.shift-noite  { background:rgba(27,43,75,.1);    color:var(--primary); }
.shift-integral { background:rgba(22,168,125,.1); color:var(--success); }

.course-pill { font-size:.68rem; font-weight:600; padding:.18rem .55rem; border-radius:50px; background:rgba(59,127,232,.1); color:var(--accent); display:inline-block; }
.general-pill { font-size:.68rem; font-weight:600; padding:.18rem .55rem; border-radius:50px; background:rgba(27,43,75,.07); color:var(--text-muted); display:inline-block; }

.capacity-bar { width:60px; height:5px; border-radius:3px; background:var(--border); overflow:hidden; display:inline-block; vertical-align:middle; margin-right:.35rem; }
.capacity-fill { height:100%; border-radius:3px; transition:width .3s; }
.cap-ok   { background:var(--success); }
.cap-mid  { background:var(--warning); }
.cap-full { background:var(--danger); }
.cap-text  { font-family:'JetBrains Mono',monospace; font-size:.7rem; color:var(--text-secondary); }
.seats-ok   { color:var(--success); font-size:.72rem; font-weight:700; }
.seats-full { color:var(--danger);  font-size:.72rem; font-weight:700; }

.status-dot { display:inline-flex; align-items:center; gap:.3rem; font-size:.72rem; font-weight:700; }
.sd { width:7px; height:7px; border-radius:50%; flex-shrink:0; }
.sd-active   { background:var(--success); box-shadow:0 0 0 2px rgba(22,168,125,.2); }
.sd-inactive { background:var(--text-muted); }
.st-active   { color:var(--success); }
.st-inactive { color:var(--text-muted); }

.teacher-name { font-weight:600; font-size:.8rem; color:var(--text-primary); }
.no-teacher   { font-size:.7rem; font-weight:600; color:var(--warning); background:rgba(232,160,32,.1); padding:.15rem .45rem; border-radius:5px; }

.row-btn { width:28px; height:28px; border-radius:7px; border:1.5px solid var(--border); background:#fff; color:var(--text-secondary); display:inline-flex; align-items:center; justify-content:center; font-size:.72rem; text-decoration:none; cursor:pointer; transition:all .18s; }
.row-btn:hover { color:#fff; border-color:transparent; transform:translateY(-1px); }
.row-btn.view:hover     { background:var(--success); }
.row-btn.edit:hover     { background:var(--accent); }
.row-btn.students:hover { background:var(--primary); }
.row-btn.subjects:hover { background:var(--warning); }
.row-btn.teachers:hover { background:#16A87D; }
.row-btn.schedule:hover { background:#6B7A99; }
.row-btn.del:hover      { background:var(--danger); }
.row-btn.activate:hover { background:var(--success); }

.ci-empty { text-align:center; padding:3rem; color:var(--text-muted); }
.ci-empty i { font-size:2rem; opacity:.15; display:block; margin-bottom:.6rem; }
.ci-empty p { font-size:.82rem; margin:0; }

.alert { border-radius:var(--radius-sm); border:none; font-size:.875rem; }
.alert-success { background:rgba(22,168,125,.1); color:#0E7A5A; border-left:3px solid var(--success); }
.alert-danger  { background:rgba(232,70,70,.08); color:#B03030; border-left:3px solid var(--danger); }

/* DataTables override */
.dataTables_wrapper .dataTables_filter input,
.dataTables_wrapper .dataTables_length select {
    border:1.5px solid var(--border); border-radius:var(--radius-sm); padding:.3rem .6rem; font-size:.8rem; font-family:'Sora',sans-serif; color:var(--text-primary); background:var(--surface);
}
.dataTables_wrapper .dataTables_filter input:focus,
.dataTables_wrapper .dataTables_length select:focus { outline:none; border-color:var(--accent); box-shadow:0 0 0 3px rgba(59,127,232,.1); }
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_filter label,
.dataTables_wrapper .dataTables_length label { font-size:.78rem; color:var(--text-secondary); }
.dataTables_wrapper .dataTables_paginate .paginate_button { border-radius:6px !important; font-size:.78rem !important; font-family:'Sora',sans-serif !important; }
.dataTables_wrapper .dataTables_paginate .paginate_button.current,
.dataTables_wrapper .dataTables_paginate .paginate_button.current:hover { background:var(--accent) !important; color:#fff !important; border-color:var(--accent) !important; }
.dataTables_wrapper .dataTables_paginate .paginate_button:hover { background:rgba(59,127,232,.1) !important; color:var(--accent) !important; border-color:var(--border) !important; }
div.dataTables_wrapper { padding:.85rem 1.25rem 1rem; }

@media(max-width:767px){
    .ci-page-header { padding:1.1rem 1.2rem; }
    .ci-page-header h1 { font-size:1.1rem; }
    .stat-grid { grid-template-columns:1fr 1fr; }
}

/* Ajustes para DataTables */
div.dt-container .dt-layout-row {
    margin: 0 0 1rem 0;
    padding: 0 1.25rem;
}
div.dt-container .dt-layout-cell {
    padding: 0;
}
div.dt-container .dt-search input {
    width: 300px !important;
}
</style>

<!-- ── CSS DATATABLE 2.x ──────────────────────────────────── -->
<link rel="stylesheet" href="https://cdn.datatables.net/2.3.7/css/dataTables.dataTables.min.css">

<!-- ── PAGE HEADER ─────────────────────────────────────── -->
<div class="ci-page-header mb-4">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-school me-2" style="opacity:.7;font-size:1.1rem;"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Turmas</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <a href="<?= site_url('admin/classes/export') ?>" class="hdr-btn success">
                <i class="fas fa-file-excel"></i> Exportar
            </a>
            <a href="<?= site_url('admin/classes/bulk-create') ?>" class="hdr-btn secondary">
                <i class="fas fa-layer-group"></i> Lote
            </a>
            <a href="<?= site_url('admin/classes/classes/form-add') ?>" class="hdr-btn primary">
                <i class="fas fa-plus-circle"></i> Nova Turma
            </a>
        </div>
    </div>
</div>

<?= view('admin/partials/alerts') ?>

<!-- ── FILTROS ────────────────────────────────────────── -->
<div class="ci-card">
    <div class="ci-card-header">
        <div class="ci-card-title"><i class="fas fa-filter"></i> Filtros</div>
    </div>
    <div class="ci-card-body">
        <form method="get" id="filterForm">
            <div class="filter-grid">
                <div>
                    <label class="filter-label">Ano Letivo</label>
                    <select class="filter-select" name="academic_year" id="academic_year">
                        <option value="">Todos</option>
                        <?php foreach ($academicYears ?? [] as $year): ?>
                            <option value="<?= $year['id'] ?>" <?= ($selectedYear == $year['id']) ? 'selected' : '' ?>>
                                <?= esc($year['year_name']) ?><?= $year->is_current ? ' ★' : '' ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="filter-label">Nível de Ensino</label>
                    <select class="filter-select" name="grade_level" id="grade_level">
                        <option value="">Todos</option>
                        <?php foreach ($gradeLevels ?? [] as $level): ?>
                            <option value="<?= $level->id ?>" <?= ($selectedLevel == $level->id) ? 'selected' : '' ?>>
                                <?= esc($level->level_name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div id="courseFilterWrap" style="<?= (isset($selectedLevel) && $selectedLevel >= 13 && $selectedLevel <= 16) ? '' : 'display:none' ?>">
                    <label class="filter-label">Curso</label>
                    <select class="filter-select" name="course" id="course">
                        <option value="">Todos</option>
                        <option value="0" <?= (($selectedCourse === '0' || $selectedCourse === 0)) ? 'selected' : '' ?>>Ensino Geral</option>
                        <?php foreach ($courses ?? [] as $c): ?>
                            <option value="<?= $c->id ?>" <?= ($selectedCourse == $c->id) ? 'selected' : '' ?>>
                                <?= esc($c->course_name) ?> (<?= esc($c->course_code) ?>)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div>
                    <label class="filter-label">Turno</label>
                    <select class="filter-select" name="shift" id="shift">
                        <option value="">Todos</option>
                        <option value="Manhã"    <?= $selectedShift == 'Manhã'    ? 'selected' : '' ?>>Manhã</option>
                        <option value="Tarde"    <?= $selectedShift == 'Tarde'    ? 'selected' : '' ?>>Tarde</option>
                        <option value="Noite"    <?= $selectedShift == 'Noite'    ? 'selected' : '' ?>>Noite</option>
                        <option value="Integral" <?= $selectedShift == 'Integral' ? 'selected' : '' ?>>Integral</option>
                    </select>
                </div>
                <div>
                    <label class="filter-label">Estado</label>
                    <select class="filter-select" name="status" id="status">
                        <option value="">Todos</option>
                        <option value="active"   <?= $selectedStatus == 'active'   ? 'selected' : '' ?>>Ativas</option>
                        <option value="inactive" <?= $selectedStatus == 'inactive' ? 'selected' : '' ?>>Inativas</option>
                    </select>
                </div>
                <div>
                    <label class="filter-label">Professor</label>
                    <select class="filter-select" name="teacher" id="teacher">
                        <option value="">Todos</option>
                        <?php foreach ($teachers ?? [] as $t): ?>
                            <option value="<?= $t->id ?>" <?= ($selectedTeacher == $t->id) ? 'selected' : '' ?>>
                                <?= esc($t->first_name) ?> <?= esc($t->last_name) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
            <div class="filter-actions mt-3">
                <button type="submit" class="btn-filter apply"><i class="fas fa-filter"></i> Filtrar</button>
                <a href="<?= site_url('admin/classes/classes') ?>" class="btn-filter clear"><i class="fas fa-undo"></i> Limpar</a>
            </div>
        </form>
    </div>

    <!-- Active filter badges -->
    <?php if (!empty($selectedYear) || !empty($selectedLevel) || !empty($selectedCourse) || !empty($selectedShift) || !empty($selectedStatus) || !empty($selectedTeacher)): ?>
    <div class="active-filters">
        <span class="af-label"><i class="fas fa-filter me-1"></i>Activos:</span>
        <?php if (!empty($selectedYear)): ?>
            <?php foreach ($academicYears as $y): if ($y->id == $selectedYear): ?>
                <span class="af-badge blue"><i class="fas fa-calendar"></i><?= esc($y->year_name) ?><a href="<?= site_url('admin/classes/classes?remove=year') ?>"><i class="fas fa-times"></i></a></span>
            <?php endif; endforeach; ?>
        <?php endif; ?>
        <?php if (!empty($selectedLevel)): ?>
            <?php foreach ($gradeLevels as $l): if ($l->id == $selectedLevel): ?>
                <span class="af-badge green"><i class="fas fa-layer-group"></i><?= esc($l->level_name) ?><a href="<?= site_url('admin/classes/classes?remove=level') ?>"><i class="fas fa-times"></i></a></span>
            <?php endif; endforeach; ?>
        <?php endif; ?>
        <?php if (!empty($selectedCourse) && $selectedCourse != 0): ?>
            <?php foreach ($courses as $c): if ($c->id == $selectedCourse): ?>
                <span class="af-badge blue"><i class="fas fa-graduation-cap"></i><?= esc($c->course_name) ?><a href="<?= site_url('admin/classes/classes?remove=course') ?>"><i class="fas fa-times"></i></a></span>
            <?php endif; endforeach; ?>
        <?php endif; ?>
        <?php if ($selectedCourse === '0'): ?>
            <span class="af-badge blue"><i class="fas fa-graduation-cap"></i>Ensino Geral<a href="<?= site_url('admin/classes/classes?remove=course') ?>"><i class="fas fa-times"></i></a></span>
        <?php endif; ?>
        <?php if (!empty($selectedShift)): ?>
            <span class="af-badge orange"><i class="fas fa-clock"></i><?= esc($selectedShift) ?><a href="<?= site_url('admin/classes/classes?remove=shift') ?>"><i class="fas fa-times"></i></a></span>
        <?php endif; ?>
        <?php if (!empty($selectedStatus)): ?>
            <span class="af-badge navy"><i class="fas fa-power-off"></i><?= $selectedStatus == 'active' ? 'Ativas' : 'Inativas' ?><a href="<?= site_url('admin/classes/classes?remove=status') ?>"><i class="fas fa-times"></i></a></span>
        <?php endif; ?>
        <?php if (!empty($selectedTeacher)): ?>
            <?php foreach ($teachers as $t): if ($t->id == $selectedTeacher): ?>
                <span class="af-badge navy"><i class="fas fa-user"></i><?= esc($t->first_name) ?> <?= esc($t->last_name) ?><a href="<?= site_url('admin/classes/classes?remove=teacher') ?>"><i class="fas fa-times"></i></a></span>
            <?php endif; endforeach; ?>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>

<!-- ── TABLE ──────────────────────────────────────────── -->
<div class="ci-card">
    <div class="ci-card-header">
        <div class="ci-card-title"><i class="fas fa-list"></i> Lista de Turmas</div>
    </div>
    <div style="overflow-x:auto; padding: 0 1.25rem 1.25rem;">
        <table id="classesTable" class="ci-table" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Turma</th>
                    <th>Nível</th>
                    <th>Curso</th>
                    <th>Ano Letivo</th>
                    <th>Turno</th>
                    <th>Sala</th>
                    <th>Capacidade</th>
                    <th>Alunos</th>
                    <th>Professor</th>
                    <th>Estado</th>
                    <th>Ações</th>
                </tr>
            </thead>
            <tbody>
                <!-- DataTables preencherá via AJAX -->
            </tbody>
        </table>
    </div>
</div>

<!-- ── MODAL DESATIVAÇÃO ──────────────────────────────── -->
<div class="modal fade" id="deactivateModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius:var(--radius);border:none;overflow:hidden;">
            <div class="modal-header" style="background:var(--primary);border:none;">
                <h5 class="modal-title text-white" style="font-size:.95rem;font-weight:700;">
                    <i class="fas fa-exclamation-triangle me-2" style="color:var(--warning);"></i>Confirmar Desativação
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" style="padding:1.4rem;">
                <p style="font-size:.88rem;">Tem certeza que deseja desativar a turma <strong id="deactivateClassName"></strong>?</p>
                <div style="background:rgba(232,160,32,.08);border:1px solid rgba(232,160,32,.25);border-radius:var(--radius-sm);padding:.85rem 1rem;font-size:.8rem;color:var(--text-secondary);">
                    <i class="fas fa-info-circle me-1" style="color:var(--warning);"></i>
                    <strong>Ao desativar:</strong> matrículas bloqueadas, disciplinas desativadas, dados históricos preservados.
                    <div style="margin-top:.4rem;color:var(--text-muted);font-size:.75rem;"><i class="fas fa-undo me-1"></i>Esta acção pode ser revertida.</div>
                </div>
            </div>
            <div class="modal-footer" style="border-top:1px solid var(--border);padding:.85rem 1.25rem;">
                <button type="button" class="btn-filter clear" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="confirmDeactivateBtn" class="btn-filter" style="background:var(--danger);color:#fff;">
                    <i class="fas fa-trash me-1"></i>Desativar
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<!-- ── JS DATATABLE 2.x ───────────────────────────────────── -->
<script src="https://cdn.datatables.net/2.3.7/js/dataTables.min.js"></script>

<script>
$(document).ready(function () {
    // Capturar o token CSRF da meta tag
    const csrfToken = $('meta[name="csrf-token"]').attr('content');
    const csrfName = '<?= csrf_token() ?>'; // Nome do token CSRF
    
    // Capturar parâmetros dos filtros
    const urlParams = new URLSearchParams(window.location.search);
    
    // Inicializar DataTable com AJAX
    let table = new DataTable('#classesTable', {
        processing: true,
        serverSide: true,
        ajax: {
            url: window.location.href,
            type: 'POST',
            data: function(d) {
                // Adicionar CSRF token aos dados
                d[csrfName] = csrfToken;
                
                // Adicionar filtros ao request
                d.academic_year = urlParams.get('academic_year') || '';
                d.grade_level = urlParams.get('grade_level') || '';
                d.course = urlParams.get('course') || '';
                d.shift = urlParams.get('shift') || '';
                d.status = urlParams.get('status') || '';
                d.teacher = urlParams.get('teacher') || '';
            },
            error: function(xhr, error, thrown) {
                console.log('Erro AJAX:', error);
                console.log('Resposta:', xhr.responseText);
                
                // Se for erro CSRF (403), podemos tentar recarregar a página
                if (xhr.status === 403) {
                    // Atualizar o token CSRF e tentar novamente
                    refreshCsrfToken();
                }
            }
        },
        language: {
            url: '//cdn.datatables.net/plug-ins/2.3.7/i18n/pt-PT.json'
        },
        order: [[1, 'asc']],
        columnDefs: [
            { 
                targets: 11, // Coluna de ações
                orderable: false,
                render: function(data, type, row) {
                    // Renderizar botões de ação
                    let buttons = `
                        <div class="d-flex justify-content-center gap-1">
                            <a href="<?= site_url('admin/classes/classes/view/') ?>/${row.id}" class="row-btn view" title="Ver"><i class="fas fa-eye"></i></a>
                            <a href="<?= site_url('admin/classes/classes/form-edit/') ?>/${row.id}" class="row-btn edit" title="Editar"><i class="fas fa-edit"></i></a>
                            <a href="<?= site_url('admin/classes/classes/list-students/') ?>/${row.id}" class="row-btn students" title="Alunos"><i class="fas fa-users"></i></a>
                    `;
                    
                    if (row.is_active == 1) {
                        buttons += `<button class="row-btn del" onclick="confirmDeactivate(${row.id}, '${row.class_name}')" title="Desativar"><i class="fas fa-trash