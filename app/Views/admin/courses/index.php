<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<style>
@import url('https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&family=JetBrains+Mono:wght@400;500&display=swap');

:root {
    --primary:        #1B2B4B;
    --primary-light:  #243761;
    --accent:         #3B7FE8;
    --accent-hover:   #2C6FD4;
    --success:        #16A87D;
    --danger:         #E84646;
    --warning:        #E8A020;
    --surface:        #F5F7FC;
    --surface-card:   #FFFFFF;
    --border:         #E2E8F4;
    --text-primary:   #1A2238;
    --text-secondary: #6B7A99;
    --text-muted:     #9AA5BE;
    --shadow-sm:      0 1px 4px rgba(27,43,75,0.07);
    --shadow-lg:      0 8px 32px rgba(27,43,75,0.14);
    --radius:         12px;
    --radius-sm:      8px;
}

* { font-family: 'Sora', sans-serif; box-sizing: border-box; }
body { background: var(--surface); color: var(--text-primary); }

/* ── PAGE HEADER ─────────────────────────────────────── */
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
    top: -60px; right: -60px;
    width: 200px; height: 200px;
    border-radius: 50%;
    background: rgba(255,255,255,0.04);
    pointer-events: none;
}
.ci-page-header::after {
    content: '';
    position: absolute;
    bottom: -40px; right: 100px;
    width: 130px; height: 130px;
    border-radius: 50%;
    background: rgba(59,127,232,0.15);
    pointer-events: none;
}
.ci-page-header h1 {
    font-size: 1.4rem;
    font-weight: 700;
    color: #fff;
    margin: 0 0 0.2rem;
    letter-spacing: -0.3px;
    position: relative;
    z-index: 1;
}
.ci-page-header .breadcrumb { margin: 0; padding: 0; background: transparent; position: relative; z-index: 1; }
.ci-page-header .breadcrumb-item a {
    color: rgba(255,255,255,0.6);
    text-decoration: none;
    font-size: 0.8rem;
    transition: color .2s;
}
.ci-page-header .breadcrumb-item a:hover { color: #fff; }
.ci-page-header .breadcrumb-item.active,
.ci-page-header .breadcrumb-item + .breadcrumb-item::before {
    color: rgba(255,255,255,0.45);
    font-size: 0.8rem;
}
.btn-header-new {
    background: var(--accent);
    color: #fff;
    border: none;
    border-radius: var(--radius-sm);
    padding: 0.55rem 1.2rem;
    font-size: 0.85rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    text-decoration: none;
    box-shadow: 0 4px 14px rgba(59,127,232,0.4);
    transition: background .2s, transform .15s, box-shadow .2s;
    position: relative;
    z-index: 1;
    white-space: nowrap;
    flex-shrink: 0;
}
.btn-header-new:hover {
    background: var(--accent-hover);
    color: #fff;
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(59,127,232,0.5);
}

/* ── FILTER + STAT ROW ───────────────────────────────── */
.filter-card {
    background: var(--surface-card);
    border-radius: var(--radius);
    border: 1px solid var(--border);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}
.card-header-custom {
    background: var(--surface);
    border-bottom: 1px solid var(--border);
    padding: 0.75rem 1.25rem;
    font-size: 0.75rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.08em;
    color: var(--text-secondary);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.card-body-custom { padding: 1.1rem 1.25rem; }

.form-label-ci {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.07em;
    color: var(--text-secondary);
    margin-bottom: 0.35rem;
    display: block;
}
.form-select-ci {
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 0.5rem 0.85rem;
    font-size: 0.85rem;
    color: var(--text-primary);
    background: var(--surface);
    transition: border-color .2s, box-shadow .2s;
    width: 100%;
    font-family: 'Sora', sans-serif;
}
.form-select-ci:focus {
    outline: none;
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(59,127,232,0.12);
    background: #fff;
}
.btn-filter {
    background: var(--primary);
    color: #fff;
    border: none;
    border-radius: var(--radius-sm);
    padding: 0.5rem 1.2rem;
    font-size: 0.85rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.45rem;
    width: 100%;
    justify-content: center;
    cursor: pointer;
    transition: background .2s, transform .15s;
    font-family: 'Sora', sans-serif;
}
.btn-filter:hover { background: var(--primary-light); transform: translateY(-1px); }

/* Stat accent card */
.stat-card-accent {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-light) 100%);
    border-radius: var(--radius);
    padding: 1.25rem 1.4rem;
    color: #fff;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
}
.stat-label { font-size: 0.72rem; font-weight: 600; text-transform: uppercase; letter-spacing: 0.08em; color: rgba(255,255,255,0.55); margin-bottom: 0.3rem; }
.stat-value { font-size: 2rem; font-weight: 700; color: #fff; font-family: 'JetBrains Mono', monospace; line-height: 1; }
.stat-sub { font-size: 0.75rem; color: rgba(255,255,255,0.6); display: flex; gap: 0.75rem; flex-wrap: wrap; margin-top: 0.5rem; }
.stat-icon-wrap { width: 42px; height: 42px; border-radius: 10px; background: rgba(255,255,255,0.12); color: rgba(255,255,255,0.8); display: flex; align-items: center; justify-content: center; font-size: 1.1rem; flex-shrink: 0; }

/* Distribution chips */
.dist-chip {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: 50px;
    padding: 0.3rem 0.85rem 0.3rem 0.6rem;
    font-size: 0.75rem;
    color: var(--text-secondary);
    font-weight: 500;
}
.dist-chip .chip-count {
    background: var(--accent);
    color: #fff;
    border-radius: 50px;
    padding: 0.08rem 0.5rem;
    font-size: 0.68rem;
    font-weight: 700;
    font-family: 'JetBrains Mono', monospace;
}

/* ── MAIN TABLE CARD ─────────────────────────────────── */
.main-card {
    background: var(--surface-card);
    border-radius: var(--radius);
    border: 1px solid var(--border);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
}
.main-card-header {
    background: var(--surface);
    border-bottom: 1px solid var(--border);
    padding: 0.85rem 1.25rem;
    display: flex;
    align-items: center;
    justify-content: space-between;
    flex-wrap: wrap;
    gap: 0.6rem;
}
.main-card-title {
    font-size: 0.85rem;
    font-weight: 700;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 0.5rem;
}
.count-pill {
    background: var(--primary);
    color: #fff;
    border-radius: 50px;
    padding: 0.12rem 0.6rem;
    font-size: 0.68rem;
    font-weight: 700;
    font-family: 'JetBrains Mono', monospace;
}
.btn-action-sm {
    background: transparent;
    border: 1.5px solid var(--border);
    color: var(--text-secondary);
    border-radius: var(--radius-sm);
    padding: 0.35rem 0.8rem;
    font-size: 0.75rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.4rem;
    cursor: pointer;
    text-decoration: none;
    transition: all .18s;
    font-family: 'Sora', sans-serif;
}
.btn-action-sm:hover { background: var(--primary); color: #fff; border-color: var(--primary); }
.btn-action-sm.success:hover { background: var(--success); border-color: var(--success); color: #fff; }

/* ── TABLE ───────────────────────────────────────────── */
.ci-table { width: 100%; border-collapse: separate; border-spacing: 0; }
.ci-table thead tr th {
    background: var(--primary);
    color: rgba(255,255,255,0.75);
    font-size: 0.68rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.09em;
    padding: 0.8rem 1rem;
    border: none;
    white-space: nowrap;
}
.ci-table tbody tr { border-bottom: 1px solid var(--border); transition: background .15s; }
.ci-table tbody tr:last-child { border-bottom: none; }
.ci-table tbody tr:hover { background: #F0F4FF; }
.ci-table tbody td { padding: 0.8rem 1rem; vertical-align: middle; font-size: 0.85rem; color: var(--text-primary); border: none; }
.ci-table tbody td.text-center { text-align: center; }

.row-id { font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; color: var(--text-muted); font-weight: 500; }
.code-badge { font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; font-weight: 600; background: var(--primary); color: #fff; padding: 0.25rem 0.6rem; border-radius: 6px; letter-spacing: 0.04em; display: inline-block; }
.course-name { font-weight: 600; font-size: 0.875rem; color: var(--text-primary); }
.course-desc { font-size: 0.75rem; color: var(--text-muted); margin-top: 0.12rem; }

.type-badge { font-size: 0.68rem; font-weight: 700; padding: 0.25rem 0.65rem; border-radius: 50px; letter-spacing: 0.04em; text-transform: uppercase; display: inline-block; white-space: nowrap; }
.type-primary   { background: rgba(27,43,75,0.10);   color: var(--primary); }
.type-success   { background: rgba(22,168,125,0.12);  color: var(--success); }
.type-warning   { background: rgba(232,160,32,0.14);  color: #C07818; }
.type-info      { background: rgba(59,127,232,0.12);  color: var(--accent); }
.type-secondary { background: rgba(107,122,153,0.12); color: #4A5568; }
.type-dark      { background: rgba(27,43,75,0.15);    color: #2D3748; }

.level-range { font-size: 0.8rem; color: var(--text-primary); font-weight: 500; }
.level-sub   { font-size: 0.72rem; color: var(--text-muted); }

.num-chip { font-family: 'JetBrains Mono', monospace; font-size: 0.78rem; font-weight: 700; background: var(--surface); border: 1.5px solid var(--border); border-radius: 7px; padding: 0.2rem 0.55rem; color: var(--text-primary); display: inline-block; }
.num-chip.blue { background: rgba(59,127,232,0.08); border-color: rgba(59,127,232,0.2); color: var(--accent); }

.status-active   { color: var(--success); background: rgba(22,168,125,0.1);  font-size: 0.72rem; font-weight: 700; padding: 0.25rem 0.65rem; border-radius: 50px; letter-spacing: 0.05em; text-transform: uppercase; display: inline-flex; align-items: center; gap: 0.3rem; white-space: nowrap; }
.status-inactive { color: var(--danger);  background: rgba(232,70,70,0.08);  font-size: 0.72rem; font-weight: 700; padding: 0.25rem 0.65rem; border-radius: 50px; letter-spacing: 0.05em; text-transform: uppercase; display: inline-flex; align-items: center; gap: 0.3rem; white-space: nowrap; }
.status-dot { width: 6px; height: 6px; border-radius: 50%; background: currentColor; display: inline-block; flex-shrink: 0; }

.row-btn { width: 30px; height: 30px; border-radius: 7px; border: 1.5px solid var(--border); background: #fff; color: var(--text-secondary); display: inline-flex; align-items: center; justify-content: center; font-size: 0.75rem; text-decoration: none; cursor: pointer; transition: all .18s; }
.row-btn:hover { color: #fff; border-color: transparent; }
.row-btn.view:hover { background: var(--success); }
.row-btn.edit:hover { background: var(--accent); }
.row-btn.curr:hover { background: var(--primary); }
.row-btn.del        { border-color: rgba(232,70,70,0.2); color: rgba(232,70,70,0.7); }
.row-btn.del:hover  { background: var(--danger); }

.empty-state { text-align: center; padding: 3.5rem 1.5rem; color: var(--text-muted); }
.empty-state i { font-size: 2.5rem; opacity: 0.2; margin-bottom: 0.75rem; display: block; }
.empty-state h5 { font-size: 0.95rem; font-weight: 600; color: var(--text-secondary); }
.empty-state p { font-size: 0.85rem; }

/* ── PAGINATION — CI4 override ──────────────────────── */
.pager-wrap {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.85rem 1.25rem;
    border-top: 1px solid var(--border);
    flex-wrap: wrap;
    gap: 0.75rem;
}
.pager-info { font-size: 0.78rem; color: var(--text-muted); }
.pager-info strong { color: var(--text-primary); }

/* Override Bootstrap pagination rendered by CI4 pager->links() */
.ci-pager .pagination {
    margin: 0;
    gap: 3px;
    display: flex;
    flex-wrap: wrap;
}
.ci-pager .pagination .page-item .page-link {
    border: 1.5px solid var(--border) !important;
    color: var(--text-secondary) !important;
    border-radius: 8px !important;
    font-size: 0.78rem !important;
    font-weight: 600 !important;
    font-family: 'JetBrains Mono', monospace !important;
    padding: 0.32rem 0.65rem !important;
    line-height: 1.4 !important;
    background: var(--surface-card) !important;
    transition: all .18s !important;
    box-shadow: none !important;
    min-width: 32px;
    text-align: center;
}
.ci-pager .pagination .page-item .page-link:hover {
    background: var(--surface) !important;
    border-color: var(--accent) !important;
    color: var(--accent) !important;
}
.ci-pager .pagination .page-item.active .page-link {
    background: var(--primary) !important;
    border-color: var(--primary) !important;
    color: #fff !important;
    box-shadow: 0 3px 8px rgba(27,43,75,0.25) !important;
}
.ci-pager .pagination .page-item.disabled .page-link {
    opacity: 0.4 !important;
    cursor: not-allowed !important;
    background: var(--surface) !important;
}
/* CI4 also renders plain <a> links without list items */
.ci-pager nav {
    display: flex;
    align-items: center;
}

/* ── MODAL ───────────────────────────────────────────── */
.ci-modal .modal-content { border: none; border-radius: var(--radius); overflow: hidden; box-shadow: var(--shadow-lg); }
.ci-modal .modal-header { background: var(--danger); padding: 0.9rem 1.4rem; }
.ci-modal .modal-title { font-size: 0.9rem; font-weight: 700; color: #fff; }
.ci-modal .btn-close-white { filter: brightness(0) invert(1); opacity: 0.8; }
.ci-modal .modal-body { padding: 1.4rem; }
.ci-modal .modal-body p { font-size: 0.875rem; margin-bottom: 0.75rem; }
.alert-danger-soft { background: rgba(232,70,70,0.07); border-left: 3px solid var(--danger); padding: 0.6rem 0.9rem; border-radius: 0 8px 8px 0; font-size: 0.78rem; color: var(--danger); }
.ci-modal .modal-footer { padding: 0.9rem 1.4rem; border-top: 1px solid var(--border); }
.btn-cancel { background: var(--surface); border: 1.5px solid var(--border); color: var(--text-secondary); padding: 0.45rem 1.1rem; border-radius: var(--radius-sm); font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: all .18s; font-family: 'Sora', sans-serif; }
.btn-cancel:hover { background: #fff; color: var(--text-primary); }
.btn-delete-confirm { background: var(--danger); border: none; color: #fff; padding: 0.45rem 1.2rem; border-radius: var(--radius-sm); font-size: 0.85rem; font-weight: 600; display: inline-flex; align-items: center; gap: 0.4rem; cursor: pointer; transition: all .18s; text-decoration: none; font-family: 'Sora', sans-serif; }
.btn-delete-confirm:hover { background: #CC3535; color: #fff; }

/* Alerts */
.alert { border-radius: var(--radius-sm); border: none; font-size: 0.875rem; }
.alert-success { background: rgba(22,168,125,0.1); color: #0E7A5A; border-left: 3px solid var(--success); }
.alert-danger   { background: rgba(232,70,70,0.08); color: #B03030; border-left: 3px solid var(--danger); }

/* ── RESPONSIVE ──────────────────────────────────────── */
@media (max-width: 991px) {
    .ci-page-header { padding: 1.25rem 1.5rem; }
    .ci-page-header h1 { font-size: 1.2rem; }
}

@media (max-width: 767px) {
    .ci-page-header { padding: 1.1rem 1.2rem; }
    .ci-page-header h1 { font-size: 1.1rem; }

    /* Stack header title + button */
    .header-row { flex-direction: column; align-items: flex-start !important; gap: 0.75rem !important; }

    /* Stats row: single column */
    .stats-col-filter, .stats-col-card { width: 100% !important; flex: 0 0 100% !important; max-width: 100% !important; }
    .stat-card-accent { padding: 1rem 1.1rem; }

    /* Table: hide less important columns on mobile */
    .ci-table thead th:nth-child(4),  /* Tipo */
    .ci-table thead th:nth-child(5),  /* Níveis */
    .ci-table thead th:nth-child(6),  /* Duração */
    .ci-table thead th:nth-child(7),  /* Disciplinas */
    .ci-table tbody td:nth-child(4),
    .ci-table tbody td:nth-child(5),
    .ci-table tbody td:nth-child(6),
    .ci-table tbody td:nth-child(7) { display: none; }

    /* Compact padding */
    .ci-table thead tr th,
    .ci-table tbody td { padding: 0.65rem 0.75rem; }

    .main-card-header { padding: 0.75rem 1rem; }
    .card-body-custom { padding: 0.9rem 1rem; }

    /* Pager: stack */
    .pager-wrap { flex-direction: column; align-items: center; text-align: center; }
    .ci-pager .pagination .page-link { padding: 0.3rem 0.55rem !important; font-size: 0.72rem !important; min-width: 28px; }

    /* Filter: stack selects */
    .filter-row > div { flex: 0 0 100%; max-width: 100%; }

    /* Distribution chips: wrap nicely */
    .dist-chips-wrap { gap: 0.4rem; }
}

@media (max-width: 480px) {
    /* On very small screens only show: #, Code, Name, Status, Actions */
    .ci-table thead th:nth-child(4),
    .ci-table thead th:nth-child(5),
    .ci-table thead th:nth-child(6),
    .ci-table thead th:nth-child(7),
    .ci-table thead th:nth-child(8),
    .ci-table tbody td:nth-child(4),
    .ci-table tbody td:nth-child(5),
    .ci-table tbody td:nth-child(6),
    .ci-table tbody td:nth-child(7),
    .ci-table tbody td:nth-child(8) { display: none; }

    .row-btn { width: 28px; height: 28px; font-size: 0.7rem; }
}

@media print {
    .btn-header-new, .filter-card, .btn-action-sm, .row-btn.del, .pager-wrap { display: none !important; }
    .ci-page-header { background: #1B2B4B !important; -webkit-print-color-adjust: exact; }
}
</style>

<!-- ── PAGE HEADER ─────────────────────────────────────── -->
<div class="ci-page-header mb-4">
    <div class="d-flex justify-content-between align-items-start flex-wrap gap-2 header-row">
        <div>
            <h1><i class="fas fa-layer-group me-2" style="opacity:.7;font-size:1.2rem;"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/academic/years') ?>">Académico</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Cursos</li>
                </ol>
            </nav>
        </div>
        <a href="<?= site_url('admin/courses/form-add') ?>" class="btn-header-new">
            <i class="fas fa-plus"></i> Novo Curso
        </a>
    </div>
</div>

<!-- ── ALERTS ──────────────────────────────────────────── -->
<?= view('admin/partials/alerts') ?>

<!-- ── STATS + FILTER ROW ─────────────────────────────── -->
<div class="row g-3 mb-3">
    <div class="col-lg-8 stats-col-filter">
        <div class="filter-card h-100">
            <div class="card-header-custom">
                <i class="fas fa-sliders-h"></i> Filtros
            </div>
            <div class="card-body-custom">
                <form method="get">
                    <div class="row g-2 filter-row">
                        <div class="col-sm-4">
                            <label class="form-label-ci">Tipo de Curso</label>
                            <select class="form-select-ci" name="type">
                                <option value="">Todos os tipos</option>
                                <?php foreach ($types as $key => $value): ?>
                                    <option value="<?= $key ?>" <?= ($selectedType ?? '') == $key ? 'selected' : '' ?>><?= $value ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <label class="form-label-ci">Status</label>
                            <select class="form-select-ci" name="status">
                                <option value="">Todos</option>
                                <option value="active"   <?= ($selectedStatus ?? '') == 'active'   ? 'selected' : '' ?>>Ativos</option>
                                <option value="inactive" <?= ($selectedStatus ?? '') == 'inactive' ? 'selected' : '' ?>>Inativos</option>
                            </select>
                        </div>
                        <div class="col-sm-4 d-flex align-items-end">
                            <button type="submit" class="btn-filter">
                                <i class="fas fa-filter"></i> Filtrar
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-lg-4 stats-col-card">
        <div class="stat-card-accent">
            <div>
                <div class="stat-label">Total de Cursos</div>
                <div class="stat-value"><?= $stats['total'] ?? 0 ?></div>
                <div class="stat-sub">
                    <span><i class="fas fa-check-circle me-1"></i><?= $stats['active'] ?? 0 ?> ativos</span>
                    <span><i class="fas fa-times-circle me-1"></i><?= $stats['inactive'] ?? 0 ?> inativos</span>
                </div>
            </div>
            <div class="stat-icon-wrap">
                <i class="fas fa-layer-group fa-lg"></i>
            </div>
        </div>
    </div>
</div>

<!-- ── DISTRIBUTION ────────────────────────────────────── -->
<?php if (!empty($stats['by_type'])): ?>
<div class="filter-card mb-3">
    <div class="card-header-custom">
        <i class="fas fa-chart-pie"></i> Distribuição por Tipo
    </div>
    <div class="card-body-custom">
        <div class="d-flex flex-wrap gap-2 dist-chips-wrap">
            <?php foreach ($stats['by_type'] as $type): ?>
                <div class="dist-chip">
                    <span><?= $type->course_type ?></span>
                    <span class="chip-count"><?= $type->total ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- ── MAIN TABLE ──────────────────────────────────────── -->
<div class="main-card">
    <div class="main-card-header">
        <div class="main-card-title">
            <i class="fas fa-layer-group" style="color:var(--accent);"></i>
            Lista de Cursos
            <span class="count-pill"><?= $pager->getTotal() ?? 0 ?></span>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <button class="btn-action-sm success" onclick="exportTableToExcel()">
                <i class="fas fa-file-csv"></i>
                <span class="d-none d-sm-inline">Exportar</span>
            </button>
            <button class="btn-action-sm" onclick="window.print()">
                <i class="fas fa-print"></i>
                <span class="d-none d-sm-inline">Imprimir</span>
            </button>
        </div>
    </div>

    <div class="table-responsive">
        <table class="ci-table" id="coursesTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Código</th>
                    <th>Nome do Curso</th>
                    <th>Tipo</th>
                    <th>Níveis</th>
                    <th class="text-center">Duração</th>
                    <th class="text-center">Disciplinas</th>
                    <th>Status</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($courses)): ?>
                    <?php foreach ($courses as $course): ?>
                        <?php
                            $courseDisciplineModel = new \App\Models\CourseDisciplineModel();
                            $totalDisciplines = $courseDisciplineModel->where('course_id', $course->id)->countAllResults();
                        ?>
                        <tr>
                            <td><span class="row-id"><?= $course->id ?></span></td>
                            <td><span class="code-badge"><?= $course->course_code ?></span></td>
                            <td>
                                <div class="course-name"><?= $course->course_name ?></div>
                                <?php if ($course->description): ?>
                                    <div class="course-desc"><?= character_limiter($course->description, 50) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php
                                    $typeMap = [
                                        'Ciências'           => 'type-primary',
                                        'Humanidades'        => 'type-success',
                                        'Económico-Jurídico' => 'type-warning',
                                        'Técnico'            => 'type-info',
                                        'Profissional'       => 'type-secondary',
                                        'Outro'              => 'type-dark',
                                    ];
                                    $cls = $typeMap[$course->course_type] ?? 'type-secondary';
                                ?>
                                <span class="type-badge <?= $cls ?>"><?= $course->course_type ?></span>
                            </td>
                            <td>
                                <?php
                                    $gradeLevelModel = new \App\Models\GradeLevelModel();
                                    $startLevel = $gradeLevelModel->find($course->start_grade_id);
                                    $endLevel   = $gradeLevelModel->find($course->end_grade_id);
                                ?>
                                <div class="level-range"><?= $startLevel->level_name ?? 'N/A' ?></div>
                                <div class="level-sub">até <?= $endLevel->level_name ?? 'N/A' ?></div>
                            </td>
                            <td class="text-center">
                                <span class="num-chip"><?= $course->duration_years ?>a</span>
                            </td>
                            <td class="text-center">
                                <span class="num-chip blue"><?= $totalDisciplines ?></span>
                            </td>
                            <td>
                                <?php if ($course->is_active): ?>
                                    <span class="status-active"><span class="status-dot"></span>Ativo</span>
                                <?php else: ?>
                                    <span class="status-inactive"><span class="status-dot"></span>Inativo</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="d-flex justify-content-center gap-1">
                                    <a href="<?= site_url('admin/courses/view/' . $course->id) ?>"
                                       class="row-btn view" title="Ver Detalhes">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="<?= site_url('admin/courses/form-edit/' . $course->id) ?>"
                                       class="row-btn edit" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <a href="<?= site_url('admin/courses/curriculum/' . $course->id) ?>"
                                       class="row-btn curr" title="Currículo">
                                        <i class="fas fa-book-open"></i>
                                    </a>
                                    <button type="button"
                                            class="row-btn del"
                                            onclick="confirmDelete(<?= $course->id ?>, '<?= esc($course->course_name, 'js') ?>')"
                                            title="Eliminar">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="9">
                            <div class="empty-state">
                                <i class="fas fa-layer-group"></i>
                                <h5>Nenhum curso encontrado</h5>
                                <p>
                                    <?php if (!empty($selectedType) || !empty($selectedStatus)): ?>
                                        Tente ajustar os filtros ou
                                        <a href="<?= site_url('admin/courses') ?>" style="color:var(--accent);">limpar filtros</a>
                                    <?php else: ?>
                                        <a href="<?= site_url('admin/courses/form-add') ?>" class="btn-header-new" style="display:inline-flex;">
                                            <i class="fas fa-plus"></i> Criar Primeiro Curso
                                        </a>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>

    <!-- ── PAGINATION ─────────────────────────────────── -->
    <div class="pager-wrap">
        <div class="pager-info">
            Mostrando <strong><?= count($courses) ?></strong> de <strong><?= $pager->getTotal() ?? 0 ?></strong> registros
        </div>
        <div class="ci-pager">
            <?= $pager->links() ?>
        </div>
    </div>
</div>

<!-- ── DELETE MODAL ────────────────────────────────────── -->
<div class="modal fade ci-modal" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle me-2" style="opacity:.8;"></i>Confirmar Eliminação
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja eliminar o curso <strong id="deleteCourseName"></strong>?</p>
                <div class="alert-danger-soft">
                    <i class="fas fa-info-circle me-1"></i>
                    Esta ação não pode ser desfeita. Todas as disciplinas associadas serão removidas.
                </div>
            </div>
            <div class="modal-footer gap-2">
                <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="confirmDeleteBtn" class="btn-delete-confirm">
                    <i class="fas fa-trash"></i> Eliminar
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
function confirmDelete(id, name) {
    document.getElementById('deleteCourseName').textContent = name;
    document.getElementById('confirmDeleteBtn').href = '<?= site_url('admin/courses/delete/') ?>' + id;
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}

function exportTableToExcel() {
    const table = document.getElementById('coursesTable');
    const rows  = Array.from(table.querySelectorAll('tr'));
    const csv   = [];
    rows.forEach(row => {
        const cells = Array.from(row.querySelectorAll('th, td'))
            .filter((_, i) => i !== row.querySelectorAll('th, td').length - 1);
        csv.push(cells.map(c => c.innerText.trim().replace(/,/g, ';')).join(','));
    });
    const blob = new Blob([csv.join('\n')], { type: 'text/csv;charset=utf-8;' });
    const link = document.createElement('a');
    link.href = URL.createObjectURL(blob);
    link.download = 'cursos_' + new Date().toISOString().split('T')[0] + '.csv';
    link.style.display = 'none';
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>
<?= $this->endSection() ?>