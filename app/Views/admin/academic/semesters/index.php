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
.hdr-btn.warning:hover { background:#d18c1c; }

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

/* ID chip */
.id-chip {
    font-family:'JetBrains Mono',monospace;
    font-size:.7rem;
    font-weight:600;
    background:rgba(27,43,75,.07);
    color:var(--primary);
    padding:.15rem .45rem;
    border-radius:5px;
    display:inline-block;
}

/* Type badge */
.type-badge {
    font-size:.65rem;
    font-weight:700;
    padding:.2rem .55rem;
    border-radius:50px;
    text-transform:uppercase;
    letter-spacing:.04em;
    display:inline-block;
    white-space:nowrap;
    background:rgba(59,127,232,.1);
    color:var(--accent);
}

/* Period text */
.period-text {
    font-family:'JetBrains Mono',monospace;
    font-size:.73rem;
    color:var(--text-secondary);
    white-space:nowrap;
}

/* Count chip */
.count-chip {
    font-family:'JetBrains Mono',monospace;
    font-size:.7rem;
    font-weight:700;
    padding:.18rem .5rem;
    border-radius:6px;
    display:inline-block;
}
.count-chip.neutral {
    background:rgba(107,122,153,.1);
    color:var(--text-secondary);
}
.count-chip.has {
    background:rgba(22,168,125,.1);
    color:var(--success);
}
.count-chip.zero {
    background:rgba(232,160,32,.1);
    color:#B07800;
}

/* Status badges */
.status-badge {
    font-size:.65rem;
    font-weight:700;
    padding:.22rem .6rem;
    border-radius:50px;
    text-transform:uppercase;
    letter-spacing:.05em;
    display:inline-flex;
    align-items:center;
    gap:.25rem;
    white-space:nowrap;
}
.status-badge .sd {
    width:5px;
    height:5px;
    border-radius:50%;
    background:currentColor;
    flex-shrink:0;
}
.sb-ativo      {
    color:var(--success);
    background:rgba(22,168,125,.1);
}
.sb-inativo    {
    color:var(--text-muted);
    background:rgba(107,122,153,.1);
}
.sb-processado {
    color:#B07800;
    background:rgba(232,160,32,.1);
}
.sb-concluido  {
    color:var(--primary);
    background:rgba(27,43,75,.08);
}

/* Current badge */
.current-badge {
    font-size:.65rem;
    font-weight:700;
    padding:.22rem .6rem;
    border-radius:50px;
    background:rgba(59,127,232,.12);
    color:var(--accent);
    display:inline-flex;
    align-items:center;
    gap:.25rem;
    white-space:nowrap;
}
.btn-set-current {
    display:inline-flex;
    align-items:center;
    justify-content:center;
    width:26px;
    height:26px;
    border-radius:6px;
    border:1.5px solid var(--border);
    background:#fff;
    color:var(--text-muted);
    cursor:pointer;
    transition:all .18s;
    text-decoration:none;
    font-size:.72rem;
}
.btn-set-current:hover {
    border-color:var(--accent);
    background:rgba(59,127,232,.08);
    color:var(--accent);
}

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
.row-btn.edit:hover    { background:var(--accent); }
.row-btn.view:hover    { background:var(--success); }
.row-btn.conclude:hover{ background:var(--warning); }
.row-btn.del:hover     { background:var(--danger); }
.row-btn.disabled-btn  { opacity:.38; cursor:not-allowed; pointer-events:none; }

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
.ci-modal .modal-content {
    border:none;
    border-radius:var(--radius);
    overflow:hidden;
    box-shadow:var(--shadow-lg);
}
.ci-modal .modal-header {
    padding:1rem 1.4rem;
}
.ci-modal .modal-header.success-header {
    background:var(--success);
}
.ci-modal .modal-header.danger-header  {
    background:var(--danger);
}
.ci-modal .modal-title  {
    font-size:.9rem;
    font-weight:700;
    color:#fff;
    display:flex;
    align-items:center;
    gap:.5rem;
}
.ci-modal .btn-close-white {
    filter:brightness(0) invert(1);
    opacity:.8;
}
.ci-modal .modal-body  {
    padding:1.4rem;
}
.ci-modal .modal-footer{
    padding:.9rem 1.4rem;
    border-top:1px solid var(--border);
    gap:.5rem;
}

/* Info alert */
.info-alert {
    background:rgba(59,127,232,.06);
    border-left:3px solid var(--accent);
    border-radius:0 var(--radius-sm) var(--radius-sm) 0;
    padding:.7rem 1rem;
    font-size:.82rem;
    color:var(--text-secondary);
    margin-bottom:1rem;
}
.info-alert i {
    color:var(--accent);
    margin-right:.4rem;
}
.warning-alert {
    background:rgba(232,160,32,.07);
    border-left:3px solid var(--warning);
    border-radius:0 var(--radius-sm) var(--radius-sm) 0;
    padding:.7rem 1rem;
    font-size:.82rem;
    color:#8a5d00;
}
.warning-alert i {
    color:var(--warning);
    margin-right:.4rem;
}
.warning-alert ul {
    margin:.5rem 0 0 1.2rem;
    padding:0;
}
.warning-alert ul li {
    margin-bottom:.2rem;
}

/* Form inside modal */
.form-label-ci {
    font-size:.68rem;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.08em;
    color:var(--text-secondary);
    margin-bottom:.3rem;
    display:block;
}
.form-select-ci {
    width:100%;
    border:1.5px solid var(--border);
    border-radius:var(--radius-sm);
    padding:.55rem .85rem;
    font-size:.875rem;
    font-family:'Sora',sans-serif;
    color:var(--text-primary);
    background:var(--surface);
    outline:none;
    transition:border-color .2s, box-shadow .2s;
}
.form-select-ci:focus {
    border-color:var(--accent);
    box-shadow:0 0 0 3px rgba(59,127,232,.12);
    background:#fff;
}
.form-hint {
    font-size:.72rem;
    color:var(--text-muted);
    margin-top:.3rem;
}

/* Process info box */
.process-info-box {
    background:var(--surface);
    border:1px solid var(--border);
    border-radius:var(--radius-sm);
    overflow:hidden;
    margin-top:.75rem;
    display:none;
}
.process-info-box.show {
    display:block;
}
.process-info-header {
    padding:.6rem 1rem;
    background:rgba(59,127,232,.06);
    border-bottom:1px solid var(--border);
    font-size:.72rem;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.07em;
    color:var(--accent);
    display:flex;
    align-items:center;
    gap:.4rem;
}
.process-info-body {
    padding:1rem;
}
.process-info-grid {
    display:grid;
    grid-template-columns:repeat(3,1fr);
    gap:.75rem;
    margin-top:.75rem;
}
.process-stat {
    text-align:center;
    padding:.65rem .5rem;
    background:#fff;
    border:1px solid var(--border);
    border-radius:var(--radius-sm);
}
.process-stat-val {
    font-family:'JetBrains Mono',monospace;
    font-size:1.25rem;
    font-weight:700;
    color:var(--primary);
    display:block;
}
.process-stat-label {
    font-size:.65rem;
    color:var(--text-muted);
    text-transform:uppercase;
    letter-spacing:.06em;
}

/* Options checkboxes */
.opt-row {
    display:flex;
    align-items:flex-start;
    gap:.75rem;
    padding:.65rem .9rem;
    border:1.5px solid var(--border);
    border-radius:var(--radius-sm);
    margin-bottom:.5rem;
    background:var(--surface);
    cursor:pointer;
    transition:border-color .18s;
}
.opt-row:hover {
    border-color:var(--accent);
    background:#f0f5ff;
}
.opt-row input[type=checkbox] {
    margin-top:.15rem;
    flex-shrink:0;
    accent-color:var(--accent);
    width:15px;
    height:15px;
    cursor:pointer;
}
.opt-row-text .ot-title {
    font-size:.85rem;
    font-weight:600;
    color:var(--text-primary);
    display:flex;
    align-items:center;
    gap:.4rem;
}
.opt-row-text .ot-sub {
    font-size:.72rem;
    color:var(--text-muted);
    margin-top:.08rem;
}

/* Modal buttons */
.btn-cancel {
    background:var(--surface);
    border:1.5px solid var(--border);
    color:var(--text-secondary);
    padding:.45rem 1.1rem;
    border-radius:var(--radius-sm);
    font-size:.85rem;
    font-weight:600;
    cursor:pointer;
    transition:all .18s;
    font-family:'Sora',sans-serif;
}
.btn-cancel:hover {
    background:#fff;
    color:var(--text-primary);
}
.btn-success-ci {
    background:var(--success);
    border:none;
    color:#fff;
    padding:.45rem 1.3rem;
    border-radius:var(--radius-sm);
    font-size:.85rem;
    font-weight:700;
    display:inline-flex;
    align-items:center;
    gap:.45rem;
    cursor:pointer;
    transition:all .18s;
    font-family:'Sora',sans-serif;
    box-shadow:0 3px 10px rgba(22,168,125,.28);
}
.btn-success-ci:hover {
    background:#0E8A64;
}
.btn-success-ci:disabled {
    opacity:.55;
    cursor:not-allowed;
}

/* Loading modal */
.loading-body {
    text-align:center;
    padding:3rem 2rem;
}
.ci-spinner {
    width:48px;
    height:48px;
    border:3.5px solid var(--border);
    border-top-color:var(--accent);
    border-radius:50%;
    animation:spin .75s linear infinite;
    margin:0 auto 1rem;
}
@keyframes spin {
    to { transform:rotate(360deg); }
}
.loading-title {
    font-size:1rem;
    font-weight:700;
    color:var(--text-primary);
    margin-bottom:.4rem;
}
.loading-msg {
    font-size:.82rem;
    color:var(--text-secondary);
    margin-bottom:1rem;
}
.ci-loading-bar {
    height:4px;
    background:var(--border);
    border-radius:50px;
    overflow:hidden;
}
.ci-loading-bar-inner {
    height:100%;
    width:100%;
    background:linear-gradient(90deg,var(--accent),var(--success));
    border-radius:50px;
    animation:loadpulse 1.5s ease-in-out infinite;
}
@keyframes loadpulse {
    0%,100%{opacity:1}
    50%{opacity:.4}
}

/* Alerts */
.alert {
    border-radius:var(--radius-sm);
    border:none;
    font-size:.875rem;
}
.alert-success {
    background:rgba(22,168,125,.1);
    color:#0E7A5A;
    border-left:3px solid var(--success);
}
.alert-danger {
    background:rgba(232,70,70,.08);
    color:#B03030;
    border-left:3px solid var(--danger);
}

/* Responsive */
@media (max-width:767px) {
    .ci-page-header {
        padding:1.1rem 1.2rem;
    }
    .ci-page-header h1 {
        font-size:1.1rem;
    }
    .filter-grid {
        grid-template-columns:1fr;
    }
    .stat-grid {
        grid-template-columns:1fr 1fr;
    }
}
</style>

<!-- ── PAGE HEADER ─────────────────────────────────────── -->
<div class="ci-page-header mb-4">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-calendar-week me-2" style="opacity:.7;font-size:1.1rem;"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/academic/years') ?>">Anos Letivos</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Semestres/Trimestres</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <button type="button" class="hdr-btn success" data-bs-toggle="modal" data-bs-target="#processSemesterModal">
                <i class="fas fa-calculator"></i> Processar Semestre
            </button>
            <a href="<?= site_url('admin/academic/semesters/form-add') ?>" class="hdr-btn primary">
                <i class="fas fa-plus-circle"></i> Novo Semestre
            </a>
        </div>
    </div>
</div>

<?= view('admin/partials/alerts') ?>

<!-- Cards de Estatísticas -->
<div class="stat-grid" id="statsContainer">
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-calendar-alt"></i>
        </div>
        <div>
            <div class="stat-label">Total Períodos</div>
            <div class="stat-value" id="totalSemesters">0</div>
            <div class="stat-sub">Cadastrados</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-play-circle"></i>
        </div>
        <div>
            <div class="stat-label">Ativos</div>
            <div class="stat-value" id="activeSemesters">0</div>
            <div class="stat-sub">Em andamento</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-calculator"></i>
        </div>
        <div>
            <div class="stat-label">Processados</div>
            <div class="stat-value" id="processedSemesters">0</div>
            <div class="stat-sub">Com resultados</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon navy">
            <i class="fas fa-check-double"></i>
        </div>
        <div>
            <div class="stat-label">Concluídos</div>
            <div class="stat-value" id="concludedSemesters">0</div>
            <div class="stat-sub">Finalizados</div>
        </div>
    </div>
</div>

<!-- ── FILTER CARD ──────────────────────────────────────── -->
<div class="ci-card">
    <div class="ci-card-header">
        <div class="ci-card-title"><i class="fas fa-filter"></i> Filtros</div>
    </div>
    <div class="ci-card-body">
        <div class="filter-grid">
            <div>
                <label class="filter-label">Ano Letivo</label>
                <select class="filter-select" id="academic_year">
                    <option value="">Todos</option>
                    <?php foreach ($academicYears as $year): ?>
                        <option value="<?= $year->id ?>" <?= $selectedYear == $year->id ? 'selected' : '' ?>>
                            <?= $year->year_name . ($year->is_current ? ' (Atual)' : '') ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div>
                <label class="filter-label">Status</label>
                <select class="filter-select" id="status">
                    <option value="">Todos</option>
                    <option value="ativo">Ativos</option>
                    <option value="inativo">Inativos</option>
                    <option value="processado">Processados</option>
                    <option value="concluido">Concluídos</option>
                </select>
            </div>
        </div>
        <div class="filter-actions">
            <button class="btn-filter apply" id="btnFilter"><i class="fas fa-filter"></i> Filtrar</button>
            <button class="btn-filter clear" id="btnClear"><i class="fas fa-undo"></i> Limpar</button>
        </div>
    </div>
</div>

<!-- ── TABLE ───────────────────────────────────────────── -->
<div class="ci-card">
    <div class="ci-card-header">
        <div class="ci-card-title"><i class="fas fa-list"></i> Lista de Semestres/Trimestres</div>
        <span class="badge bg-primary" id="recordCount">0 registros</span>
    </div>
    <div style="overflow-x:auto; padding: 0 1.25rem 1.25rem;">
        <table id="semestersTable" class="ci-table" style="width:100%">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ano Letivo</th>
                    <th>Nome</th>
                    <th>Tipo</th>
                    <th>Período</th>
                    <th class="center">Exames</th>
                    <th class="center">Resultados</th>
                    <th>Status</th>
                    <th class="center">Atual</th>
                    <th class="center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <!-- DataTables preenche via AJAX -->
            </tbody>
        </table>
    </div>
</div>

<!-- ── MODAL: PROCESSAR SEMESTRE ───────────────────────── -->
<div class="modal fade ci-modal" id="processSemesterModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="processForm">
                <?= csrf_field() ?>
                <div class="modal-header success-header">
                    <h5 class="modal-title">
                        <i class="fas fa-calculator"></i> Processar Resultados do Semestre
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="info-alert">
                        <i class="fas fa-info-circle"></i>
                        Esta operação calcula as médias finais e gera os resultados para todos os alunos do semestre selecionado.
                    </div>

                    <div class="row g-3 mb-2">
                        <div class="col-sm-8">
                            <label class="form-label-ci" for="process_semester_id">Semestre / Trimestre <span style="color:var(--danger);">*</span></label>
                            <select class="form-select-ci" id="process_semester_id" required>
                                <option value="">— Selecione um período —</option>
                                <?php foreach ($semesters ?? [] as $sem): ?>
                                    <option value="<?= $sem->id ?>"
                                        data-year="<?= esc($sem->year_name ?? '') ?>">
                                        <?= esc($sem->semester_name) ?> (<?= esc($sem->year_name ?? '') ?>)
                                        <?= ($sem->is_current ?? false) ? ' — Atual' : '' ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <div class="form-hint">Selecione o período que deseja processar</div>
                        </div>
                        <div class="col-sm-4 d-flex align-items-end">
                            <button type="button" class="btn-filter apply w-100" onclick="checkExams()" style="padding:.57rem 1rem;">
                                <i class="fas fa-search"></i> Verificar Exames
                            </button>
                        </div>
                    </div>

                    <!-- Dynamic process info -->
                    <div class="process-info-box" id="processInfo">
                        <div class="process-info-header"><i class="fas fa-info-circle"></i> Informações do Período</div>
                        <div class="process-info-body" id="processInfoContent">
                            <div style="text-align:center;padding:.75rem;color:var(--text-muted);font-size:.82rem;">
                                <div class="ci-spinner" style="width:24px;height:24px;border-width:2px;margin:0 auto .4rem;"></div>
                                Carregando...
                            </div>
                        </div>
                    </div>

                    <!-- Options -->
                    <div style="margin-top:1.25rem;">
                        <div class="form-label-ci" style="margin-bottom:.6rem;">Opções de Processamento</div>
                        <label class="opt-row">
                            <input type="checkbox" name="generate_report_cards" value="1" checked>
                            <div class="opt-row-text">
                                <div class="ot-title"><i class="fas fa-file-pdf" style="color:var(--danger);"></i> Gerar boletins automaticamente</div>
                                <div class="ot-sub">Os boletins individuais serão gerados após o processamento</div>
                            </div>
                        </label>
                        <label class="opt-row">
                            <input type="checkbox" name="close_semester" value="1">
                            <div class="opt-row-text">
                                <div class="ot-title"><i class="fas fa-lock" style="color:var(--warning);"></i> Fechar semestre</div>
                                <div class="ot-sub">Impede novos lançamentos de notas neste período</div>
                            </div>
                        </label>
                    </div>

                    <!-- Warning -->
                    <div class="warning-alert mt-3">
                        <i class="fas fa-exclamation-triangle"></i>
                        <strong>Atenção!</strong> Esta operação:
                        <ul>
                            <li>Calcula as médias por disciplina para todos os alunos</li>
                            <li>Gera os resultados consolidados do semestre</li>
                            <li>Cria os boletins individuais</li>
                            <li>Não pode ser desfeita automaticamente</li>
                        </ul>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-success-ci" id="processBtn">
                        <i class="fas fa-play"></i> Iniciar Processamento
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── MODAL: LOADING ──────────────────────────────────── -->
<div class="modal fade ci-modal" id="loadingModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered" style="max-width:400px;">
        <div class="modal-content">
            <div class="loading-body">
                <div class="ci-spinner"></div>
                <div class="loading-title">Processando semestre...</div>
                <div class="loading-msg" id="loadingMessage">Calculando médias por disciplina</div>
                <div class="ci-loading-bar"><div class="ci-loading-bar-inner"></div></div>
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
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
$(document).ready(function () {
    // Carregar estatísticas iniciais
    loadStats();
    
    // Inicializar DataTable
    var table = $('#semestersTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= site_url('admin/academic/semesters/get-table-data') ?>',
            type: 'POST',
            data: function(d) {
                d.academic_year = $('#academic_year').val();
                d.status = $('#status').val();
                d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
            },
            error: function(xhr, error, thrown) {
                console.log('Erro AJAX:', error);
                console.log('Resposta:', xhr.responseText);
            }
        },
        columns: [
            { data: 'id' },
            { data: 'year_name' },
            { data: 'semester_name' },
            { data: 'semester_type' },
            { 
                data: null,
                render: function(data) {
                    return '<span class="period-text">' +
                           formatDate(data.start_date) + ' → ' + formatDate(data.end_date) +
                           '</span>';
                }
            },
            { 
                data: 'total_exams',
                render: function(data) {
                    return '<span class="count-chip neutral">' + (data || 0) + '</span>';
                }
            },
            { 
                data: 'has_results',
                render: function(data) {
                    var cls = data > 0 ? 'has' : 'zero';
                    return '<span class="count-chip ' + cls + '">' + (data || 0) + '</span>';
                }
            },
            { data: 'status_badge' },
            { data: 'current_badge' },
            { data: 'actions' }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[1, 'asc'], [4, 'asc']],
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
    $('#academic_year, #status').on('change', function() {
        table.ajax.reload();
    });
    
    // Limpar filtros
    $('#btnClear').on('click', function() {
        $('#academic_year').val('');
        $('#status').val('');
        table.ajax.reload();
    });
    
    // Auto-load info when semester changes
    $('#process_semester_id').on('change', function () {
        var id = $(this).val();
        if (id) loadSemesterInfo(id);
        else $('#processInfo').removeClass('show');
    });

    // Intercept form submit
    $('#processForm').on('submit', function (e) {
        e.preventDefault();
        
        var semesterId = $('#process_semester_id').val();
        if (!semesterId) {
            Swal.fire('Atenção!', 'Selecione um semestre para processar.', 'warning');
            return;
        }
        
        if (!confirm('Tem certeza que deseja processar este semestre? Esta ação não pode ser desfeita.')) return;

        $('#loadingModal').modal('show');
        $('#processBtn').prop('disabled', true);

        var msgs = [
            'Calculando médias por disciplina...',
            'Processando resultados individuais...',
            'Gerando estatísticas da turma...',
            'Preparando boletins...',
            'Finalizando processamento...'
        ];
        var idx = 0;
        var iv  = setInterval(function () {
            if (idx < msgs.length) $('#loadingMessage').text(msgs[idx++]);
        }, 2000);

        $.ajax({
            url: '<?= site_url('admin/academic/semesters/process') ?>',
            type: 'POST',
            data: {
                semester_id: semesterId,
                generate_report_cards: $('input[name="generate_report_cards"]').is(':checked') ? 1 : 0,
                close_semester: $('input[name="close_semester"]').is(':checked') ? 1 : 0,
                '<?= csrf_token() ?>': '<?= csrf_hash() ?>'
            },
            dataType: 'json',
            success: function (r) {
                clearInterval(iv);
                $('#loadingModal').modal('hide');
                if (r.success) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Sucesso!',
                        html: r.message
                    }).then(() => {
                        location.reload();
                    });
                } else {
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: r.message
                    });
                }
            },
            error: function (xhr) {
                clearInterval(iv);
                $('#loadingModal').modal('hide');
                var msg = 'Erro ao processar semestre';
                try {
                    var r = JSON.parse(xhr.responseText);
                    msg = r.message || msg;
                } catch(e) {}
                Swal.fire({
                    icon: 'error',
                    title: 'Erro!',
                    text: msg
                });
            },
            complete: function () {
                $('#processBtn').prop('disabled', false);
            }
        });
    });
    
    // Função para carregar estatísticas
    function loadStats() {
        $.ajax({
            url: '<?= site_url('admin/academic/semesters/get-stats') ?>',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                $('#totalSemesters').text(data.total || 0);
                $('#activeSemesters').text(data.active || 0);
                $('#processedSemesters').text(data.processed || 0);
                $('#concludedSemesters').text(data.concluded || 0);
            },
            error: function(xhr, status, error) {
                console.error('Erro ao carregar estatísticas:', error);
            }
        });
    }
    
    // Função para formatar data
    function formatDate(dateString) {
        if (!dateString) return '';
        var date = new Date(dateString);
        return ('0' + date.getDate()).slice(-2) + '/' +
               ('0' + (date.getMonth() + 1)).slice(-2) + '/' +
               date.getFullYear();
    }
});

function loadSemesterInfo(id) {
    $('#processInfoContent').html(
        '<div style="text-align:center;padding:.75rem;color:var(--text-muted);font-size:.82rem;">' +
        '<div class="ci-spinner" style="width:24px;height:24px;border-width:2px;margin:0 auto .4rem;"></div>' +
        'Carregando...</div>'
    );
    $('#processInfo').addClass('show');

    $.getJSON('<?= site_url("admin/academic/semesters/info") ?>/' + id, function (r) {
        if (!r.success) {
            $('#processInfoContent').html('<div style="color:var(--danger);font-size:.82rem;padding:.5rem;">' + r.message + '</div>');
            return;
        }

        var d = r.data;
        var s = r.stats;
        var html = '<div class="row g-2" style="font-size:.82rem;">' +
            '<div class="col-sm-6"><strong>Período:</strong> ' + d.semester_name + '</div>' +
            '<div class="col-sm-6"><strong>Tipo:</strong> ' + d.semester_type + '</div>' +
            '<div class="col-sm-6"><strong>Ano Letivo:</strong> ' + d.year_name + '</div>' +
            '<div class="col-sm-6"><strong>Início:</strong> ' + d.start_date + ' → <strong>Fim:</strong> ' + d.end_date + '</div>' +
            '</div>' +
            '<div class="process-info-grid">' +
            '<div class="process-stat"><span class="process-stat-val">' + (s.total_exams || 0) + '</span><span class="process-stat-label">Exames</span></div>' +
            '<div class="process-stat"><span class="process-stat-val">' + (s.total_students || 0) + '</span><span class="process-stat-label">Alunos</span></div>' +
            '<div class="process-stat"><span class="process-stat-val">' + (s.total_results || 0) + '</span><span class="process-stat-label">Resultados</span></div>' +
            '</div>';

        if (s.total_results > 0) {
            html += '<div class="warning-alert mt-2" style="font-size:.78rem;"><i class="fas fa-exclamation-triangle"></i> Este semestre já possui resultados. Processar novamente irá sobrescrever os dados existentes.</div>';
        }

        $('#processInfoContent').html(html);
    }).fail(function () {
        $('#processInfoContent').html('<div style="color:var(--danger);font-size:.82rem;padding:.5rem;">Erro ao carregar informações.</div>');
    });
}

function checkExams() {
    var id = $('#process_semester_id').val();
    if (!id) {
        Swal.fire('Atenção!', 'Selecione um semestre primeiro!', 'warning');
        return;
    }
    window.open('<?= site_url("admin/exams?semester_id=") ?>' + id, '_blank');
}
</script>
<?= $this->endSection() ?>