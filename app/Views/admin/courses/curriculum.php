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
    --shadow-md:      0 4px 16px rgba(27,43,75,0.10);
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
    content: ''; position: absolute; top: -60px; right: -60px;
    width: 200px; height: 200px; border-radius: 50%;
    background: rgba(255,255,255,0.04); pointer-events: none;
}
.ci-page-header::after {
    content: ''; position: absolute; bottom: -40px; right: 100px;
    width: 130px; height: 130px; border-radius: 50%;
    background: rgba(59,127,232,0.15); pointer-events: none;
}
.ci-page-header h1 {
    font-size: 1.4rem; font-weight: 700; color: #fff;
    margin: 0 0 0.2rem; letter-spacing: -0.3px; position: relative; z-index: 1;
}
.ci-page-header .breadcrumb { margin: 0; padding: 0; background: transparent; position: relative; z-index: 1; }
.ci-page-header .breadcrumb-item a { color: rgba(255,255,255,0.6); text-decoration: none; font-size: 0.8rem; transition: color .2s; }
.ci-page-header .breadcrumb-item a:hover { color: #fff; }
.ci-page-header .breadcrumb-item.active,
.ci-page-header .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,0.4); font-size: 0.8rem; }

/* ── COURSE INFO BANNER ──────────────────────────────── */
.course-banner {
    background: var(--surface-card); border: 1px solid var(--border); border-radius: var(--radius);
    padding: 1rem 1.4rem; margin-bottom: 1.5rem;
    display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap;
    gap: 0.75rem; box-shadow: var(--shadow-sm);
}
.course-banner-info { display: flex; align-items: center; gap: 0.85rem; flex-wrap: wrap; }
.course-banner-icon {
    width: 42px; height: 42px; background: rgba(59,127,232,0.1); border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    color: var(--accent); font-size: 1rem; flex-shrink: 0;
}
.course-banner-name { font-weight: 700; font-size: 0.95rem; color: var(--text-primary); }
.course-banner-code {
    font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; font-weight: 600;
    background: var(--primary); color: #fff; padding: 0.2rem 0.55rem;
    border-radius: 6px; letter-spacing: 0.04em; display: inline-block;
}
.status-pill-active   { color: var(--success); background: rgba(22,168,125,0.1);  font-size: 0.7rem; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 50px; text-transform: uppercase; letter-spacing: 0.05em; display: inline-flex; align-items: center; gap: 0.25rem; }
.status-pill-inactive { color: var(--danger);  background: rgba(232,70,70,0.08);  font-size: 0.7rem; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 50px; text-transform: uppercase; letter-spacing: 0.05em; display: inline-flex; align-items: center; gap: 0.25rem; }
.status-dot { width: 5px; height: 5px; border-radius: 50%; background: currentColor; flex-shrink: 0; }

.btn-back {
    background: transparent; border: 1.5px solid var(--border); color: var(--text-secondary);
    border-radius: var(--radius-sm); padding: 0.45rem 1.1rem; font-size: 0.82rem; font-weight: 600;
    display: inline-flex; align-items: center; gap: 0.45rem;
    text-decoration: none; transition: all .18s; white-space: nowrap;
}
.btn-back:hover { background: var(--surface); color: var(--primary); border-color: var(--primary); }

/* ── LAYOUT GRID ─────────────────────────────────────── */
.curriculum-layout { display: grid; grid-template-columns: 240px 1fr; gap: 1.25rem; align-items: start; }

/* ── SIDEBAR PANEL ───────────────────────────────────── */
.levels-panel {
    background: var(--surface-card); border: 1px solid var(--border);
    border-radius: var(--radius); box-shadow: var(--shadow-sm);
    overflow: hidden; position: sticky; top: 80px;
}
.levels-panel-header {
    background: var(--primary); padding: 0.85rem 1.1rem;
    display: flex; align-items: center; gap: 0.5rem;
    font-size: 0.78rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.08em; color: rgba(255,255,255,0.85);
}
.level-nav-item {
    display: flex; align-items: center; justify-content: space-between;
    padding: 0.65rem 1.1rem; border-bottom: 1px solid var(--border);
    text-decoration: none; color: var(--text-secondary);
    font-size: 0.82rem; font-weight: 500;
    transition: background .15s, color .15s; gap: 0.5rem;
}
.level-nav-item:last-child { border-bottom: none; }
.level-nav-item:hover { background: var(--surface); color: var(--primary); }
.level-nav-item.active-level { background: rgba(59,127,232,0.07); color: var(--accent); font-weight: 600; border-left: 3px solid var(--accent); }
.level-nav-item i { font-size: 0.75rem; color: var(--text-muted); flex-shrink: 0; }
.level-nav-item:hover i, .level-nav-item.active-level i { color: var(--accent); }
.level-count {
    font-family: 'JetBrains Mono', monospace; font-size: 0.65rem; font-weight: 700;
    padding: 0.1rem 0.45rem; border-radius: 50px; background: var(--accent); color: #fff; flex-shrink: 0;
}
.level-count.zero { background: var(--border); color: var(--text-muted); }
.levels-panel-footer {
    background: var(--surface); border-top: 1px solid var(--border);
    padding: 0.75rem 1.1rem; display: flex; align-items: center; justify-content: space-between;
}
.levels-panel-footer .footer-label { font-size: 0.72rem; color: var(--text-muted); font-weight: 500; }
.levels-panel-footer .footer-value { font-family: 'JetBrains Mono', monospace; font-size: 0.85rem; font-weight: 700; color: var(--primary); }

/* ── LEVEL CARDS ─────────────────────────────────────── */
.level-card {
    background: var(--surface-card); border: 1px solid var(--border);
    border-radius: var(--radius); box-shadow: var(--shadow-sm);
    overflow: hidden; margin-bottom: 1.1rem; scroll-margin-top: 90px;
}
.level-card:last-child { margin-bottom: 0; }
.level-card-header {
    background: var(--primary); padding: 0.85rem 1.25rem;
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 0.6rem;
}
.level-card-header.alt { background: var(--primary-light); }
.level-card-title { display: flex; align-items: center; gap: 0.6rem; font-size: 0.9rem; font-weight: 700; color: #fff; }
.level-card-title i { opacity: 0.7; font-size: 0.85rem; }
.btn-add-discipline {
    background: rgba(255,255,255,0.15); border: 1.5px solid rgba(255,255,255,0.25); color: #fff;
    border-radius: var(--radius-sm); padding: 0.35rem 0.9rem; font-size: 0.78rem; font-weight: 600;
    display: inline-flex; align-items: center; gap: 0.4rem;
    cursor: pointer; transition: background .18s, border-color .18s; white-space: nowrap;
}
.btn-add-discipline:hover { background: rgba(255,255,255,0.25); border-color: rgba(255,255,255,0.4); color: #fff; }

/* ── CURRICULUM TABLE ────────────────────────────────── */
.ci-table { width: 100%; border-collapse: separate; border-spacing: 0; }
.ci-table thead tr th {
    background: var(--surface); color: var(--text-secondary);
    font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.09em;
    padding: 0.65rem 1rem; border-bottom: 1.5px solid var(--border); border-top: none; white-space: nowrap;
}
.ci-table tbody tr { border-bottom: 1px solid var(--border); transition: background .15s; }
.ci-table tbody tr:last-child { border-bottom: none; }
.ci-table tbody tr:hover { background: #F0F4FF; }
.ci-table tbody td { padding: 0.75rem 1rem; vertical-align: middle; font-size: 0.85rem; border: none; }
.ci-table tbody td.text-center { text-align: center; }
.ci-table tfoot tr td { background: var(--surface); padding: 0.6rem 1rem; font-size: 0.78rem; font-weight: 700; color: var(--text-secondary); border-top: 1.5px solid var(--border); border-bottom: none; }

.disc-code { font-family: 'JetBrains Mono', monospace; font-size: 0.73rem; font-weight: 600; background: rgba(27,43,75,0.08); color: var(--primary); padding: 0.22rem 0.55rem; border-radius: 6px; display: inline-block; letter-spacing: 0.03em; }
.disc-name { font-weight: 600; font-size: 0.875rem; color: var(--text-primary); }
.workload-chip { font-family: 'JetBrains Mono', monospace; font-size: 0.75rem; font-weight: 700; background: rgba(59,127,232,0.08); border: 1.5px solid rgba(59,127,232,0.18); color: var(--accent); padding: 0.2rem 0.55rem; border-radius: 7px; display: inline-block; }
.workload-chip.total { background: rgba(22,168,125,0.1); border-color: rgba(22,168,125,0.2); color: var(--success); font-size: 0.8rem; }
.workload-default { font-size: 0.7rem; color: var(--text-muted); margin-top: 0.1rem; }
.sem-badge { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em; padding: 0.22rem 0.6rem; border-radius: 50px; display: inline-block; white-space: nowrap; }
.sem-anual { background: rgba(27,43,75,0.08);  color: var(--primary); }
.sem-1     { background: rgba(59,127,232,0.10); color: var(--accent); }
.sem-2     { background: rgba(22,168,125,0.10); color: var(--success); }
.sem-other { background: rgba(107,122,153,0.1); color: var(--text-secondary); }
.mand-yes { color: var(--success); background: rgba(22,168,125,0.1); font-size: 0.7rem; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 50px; text-transform: uppercase; letter-spacing: 0.04em; display: inline-flex; align-items: center; gap: 0.25rem; }
.mand-no  { color: var(--warning); background: rgba(232,160,32,0.1);  font-size: 0.7rem; font-weight: 700; padding: 0.2rem 0.6rem; border-radius: 50px; text-transform: uppercase; letter-spacing: 0.04em; display: inline-flex; align-items: center; gap: 0.25rem; }
.row-btn { width: 30px; height: 30px; border-radius: 7px; border: 1.5px solid var(--border); background: #fff; color: var(--text-secondary); display: inline-flex; align-items: center; justify-content: center; font-size: 0.73rem; text-decoration: none; cursor: pointer; transition: all .18s; }
.row-btn:hover { color: #fff; border-color: transparent; }
.row-btn.edit:hover { background: var(--accent); }
.row-btn.del  { border-color: rgba(232,70,70,0.2); color: rgba(232,70,70,0.65); }
.row-btn.del:hover { background: var(--danger); }
.level-empty { text-align: center; padding: 2.5rem 1.5rem; color: var(--text-muted); }
.level-empty i { font-size: 2rem; opacity: 0.2; display: block; margin-bottom: 0.6rem; }
.level-empty p { font-size: 0.85rem; margin: 0; }
.level-empty strong { color: var(--text-secondary); font-size: 0.9rem; display: block; margin-bottom: 0.3rem; }

/* ── MODAL ───────────────────────────────────────────── */
.ci-modal .modal-content { border: none; border-radius: var(--radius); overflow: hidden; box-shadow: var(--shadow-lg); }
.ci-modal .modal-header { padding: 1rem 1.4rem; }
.ci-modal .modal-header.primary-header { background: var(--primary); }
.ci-modal .modal-header.danger-header  { background: var(--danger); }
.ci-modal .modal-title { font-size: 0.9rem; font-weight: 700; color: #fff; display: flex; align-items: center; gap: 0.5rem; }
.ci-modal .btn-close-white { filter: brightness(0) invert(1); opacity: 0.8; }
.ci-modal .modal-body { padding: 1.4rem; }
.ci-modal .modal-footer { padding: 0.9rem 1.4rem; border-top: 1px solid var(--border); gap: 0.5rem; }

.form-label-ci { font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: var(--text-secondary); margin-bottom: 0.35rem; display: block; }
.form-input-ci, .form-select-ci {
    width: 100%; border: 1.5px solid var(--border); border-radius: var(--radius-sm);
    padding: 0.55rem 0.85rem; font-size: 0.875rem; font-family: 'Sora', sans-serif;
    color: var(--text-primary); background: var(--surface); transition: border-color .2s, box-shadow .2s;
}
.form-input-ci:focus, .form-select-ci:focus {
    outline: none; border-color: var(--accent); box-shadow: 0 0 0 3px rgba(59,127,232,0.12); background: #fff;
}
.form-hint { font-size: 0.72rem; color: var(--text-muted); margin-top: 0.3rem; }

/* Toggle switch */
.toggle-row { display: flex; align-items: center; justify-content: space-between; background: var(--surface); border: 1.5px solid var(--border); border-radius: var(--radius-sm); padding: 0.65rem 0.9rem; cursor: pointer; transition: border-color .18s; }
.toggle-row:hover { border-color: var(--accent); }
.toggle-row .tl-info { font-size: 0.875rem; font-weight: 600; color: var(--text-primary); }
.toggle-row .tl-sub  { font-size: 0.72rem; color: var(--text-muted); margin-top: 0.1rem; }
.ci-switch { position: relative; display: inline-flex; width: 40px; height: 22px; flex-shrink: 0; }
.ci-switch input { opacity: 0; width: 0; height: 0; }
.ci-switch-track { position: absolute; inset: 0; border-radius: 50px; background: var(--border); transition: background .22s; cursor: pointer; }
.ci-switch-track::after { content: ''; position: absolute; top: 3px; left: 3px; width: 16px; height: 16px; border-radius: 50%; background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.2); transition: transform .22s; }
.ci-switch input:checked + .ci-switch-track { background: var(--success); }
.ci-switch input:checked + .ci-switch-track::after { transform: translateX(18px); }

/* ── DISCIPLINE INFO PANEL ───────────────────────────── */
.disc-info-panel {
    background: rgba(59,127,232,0.05);
    border: 1px solid rgba(59,127,232,0.15);
    border-left: 3px solid var(--accent);
    border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
    padding: 0.75rem 1rem;
    margin-top: 0.6rem;
    display: none;
    animation: infoPanelIn .18s ease;
}
.disc-info-panel.show { display: block; }
@keyframes infoPanelIn {
    from { opacity: 0; transform: translateY(-5px); }
    to   { opacity: 1; transform: translateY(0); }
}
.disc-info-name {
    display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;
    font-size: 0.85rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;
}
.disc-info-code-tag {
    font-family: 'JetBrains Mono', monospace; font-size: 0.68rem; font-weight: 600;
    background: rgba(27,43,75,0.08); color: var(--primary);
    padding: 0.15rem 0.45rem; border-radius: 5px; letter-spacing: 0.03em;
}
.disc-info-meta { display: flex; gap: 1.25rem; flex-wrap: wrap; }
.disc-info-meta-item { display: flex; align-items: center; gap: 0.4rem; font-size: 0.78rem; color: var(--text-secondary); }
.disc-info-meta-item strong { color: var(--text-primary); font-weight: 700; }

/* ── SELECT2 CUSTOM THEME ────────────────────────────── */
/* Make it always full width inside modal */
#addDisciplineModal .select2-container { width: 100% !important; }

/* Closed selection box */
#addDisciplineModal .select2-container--bootstrap-5 .select2-selection {
    border: 1.5px solid var(--border) !important;
    border-radius: var(--radius-sm) !important;
    background: var(--surface) !important;
    min-height: 42px !important;
    display: flex !important;
    align-items: center !important;
    padding: 0 2.2rem 0 0.85rem !important;
    font-family: 'Sora', sans-serif !important;
    font-size: 0.875rem !important;
    box-shadow: none !important;
    transition: border-color .2s, box-shadow .2s !important;
    cursor: pointer !important;
    position: relative !important;
}
#addDisciplineModal .select2-container--bootstrap-5.select2-container--open .select2-selection,
#addDisciplineModal .select2-container--bootstrap-5.select2-container--focus .select2-selection {
    border-color: var(--accent) !important;
    box-shadow: 0 0 0 3px rgba(59,127,232,0.12) !important;
    background: #fff !important;
}
#addDisciplineModal .select2-container--bootstrap-5 .select2-selection--single .select2-selection__rendered {
    color: var(--text-primary) !important;
    font-size: 0.875rem !important;
    font-family: 'Sora', sans-serif !important;
    padding: 0 !important; line-height: 1.5 !important;
}
#addDisciplineModal .select2-container--bootstrap-5 .select2-selection--single .select2-selection__placeholder {
    color: var(--text-muted) !important;
}
#addDisciplineModal .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow {
    position: absolute !important; top: 50% !important; right: 0.75rem !important;
    transform: translateY(-50%) !important; width: auto !important; height: auto !important;
}
#addDisciplineModal .select2-container--bootstrap-5 .select2-selection--single .select2-selection__arrow b {
    border-color: var(--text-muted) transparent transparent transparent !important;
    border-width: 5px 4px 0 4px !important; display: block !important;
}
#addDisciplineModal .select2-container--bootstrap-5.select2-container--open .select2-selection--single .select2-selection__arrow b {
    border-color: transparent transparent var(--accent) transparent !important;
    border-width: 0 4px 5px 4px !important;
}

/* Dropdown */
.select2-container--bootstrap-5 .select2-dropdown {
    border: 1.5px solid var(--border) !important;
    border-radius: var(--radius-sm) !important;
    box-shadow: 0 8px 32px rgba(27,43,75,0.16) !important;
    overflow: hidden !important;
    margin-top: 3px !important;
    background: #fff !important;
    z-index: 9999 !important;
}

/* Search field */
.select2-container--bootstrap-5 .select2-search--dropdown {
    padding: 0.6rem 0.7rem !important;
    background: var(--surface) !important;
    border-bottom: 1px solid var(--border) !important;
}
.select2-container--bootstrap-5 .select2-search--dropdown .select2-search__field {
    border: 1.5px solid var(--border) !important;
    border-radius: var(--radius-sm) !important;
    /* magnifying glass icon */
    padding: 0.45rem 0.75rem 0.45rem 2.1rem !important;
    background: #fff url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3E%3Cpath fill='%239AA5BE' d='M11.742 10.344a6.5 6.5 0 1 0-1.397 1.398h-.001c.03.04.062.078.098.115l3.85 3.85a1 1 0 0 0 1.415-1.414l-3.85-3.85a1.007 1.007 0 0 0-.115-.099zM6.5 12a5.5 5.5 0 1 1 0-11 5.5 5.5 0 0 1 0 11z'/%3E%3C/svg%3E") no-repeat 0.65rem center / 13px !important;
    font-family: 'Sora', sans-serif !important;
    font-size: 0.85rem !important;
    color: var(--text-primary) !important;
    outline: none !important;
    width: 100% !important;
    transition: border-color .2s, box-shadow .2s !important;
}
.select2-container--bootstrap-5 .select2-search--dropdown .select2-search__field:focus {
    border-color: var(--accent) !important;
    box-shadow: 0 0 0 3px rgba(59,127,232,0.12) !important;
}
.select2-container--bootstrap-5 .select2-search--dropdown .select2-search__field::placeholder {
    color: var(--text-muted) !important; font-size: 0.82rem !important;
}

/* Results list */
.select2-container--bootstrap-5 .select2-results__options {
    max-height: 220px !important; overflow-y: auto !important; padding: 0.3rem 0 !important;
}
.select2-container--bootstrap-5 .select2-results__options::-webkit-scrollbar { width: 4px; }
.select2-container--bootstrap-5 .select2-results__options::-webkit-scrollbar-track { background: transparent; }
.select2-container--bootstrap-5 .select2-results__options::-webkit-scrollbar-thumb { background: var(--border); border-radius: 4px; }
.select2-container--bootstrap-5 .select2-results__options::-webkit-scrollbar-thumb:hover { background: var(--text-muted); }

/* Option items */
.select2-container--bootstrap-5 .select2-results__option {
    padding: 0.5rem 0.85rem !important;
    font-size: 0.85rem !important; font-family: 'Sora', sans-serif !important;
    color: var(--text-secondary) !important;
    transition: background .12s !important; cursor: pointer !important;
}
.select2-container--bootstrap-5 .select2-results__option:hover,
.select2-container--bootstrap-5 .select2-results__option--highlighted[aria-selected] {
    background: rgba(59,127,232,0.07) !important; color: var(--primary) !important;
}
.select2-container--bootstrap-5 .select2-results__option[aria-selected=true] {
    background: rgba(59,127,232,0.1) !important; color: var(--accent) !important; font-weight: 600 !important;
}
.select2-container--bootstrap-5 .select2-results__message {
    text-align: center !important; color: var(--text-muted) !important;
    font-size: 0.82rem !important; padding: 1rem !important;
}

/* Custom option template */
.s2-opt {
    display: flex; align-items: center; gap: 0.55rem; width: 100%;
}
.s2-opt-code {
    font-family: 'JetBrains Mono', monospace; font-size: 0.68rem; font-weight: 600;
    background: rgba(27,43,75,0.08); color: var(--primary);
    padding: 0.12rem 0.42rem; border-radius: 4px; flex-shrink: 0;
    letter-spacing: 0.03em; white-space: nowrap;
}
.s2-opt-name { flex: 1; color: var(--text-primary); font-size: 0.85rem; }
.select2-results__option--highlighted .s2-opt-code { background: rgba(59,127,232,0.12); color: var(--accent); }
.select2-results__option[aria-selected=true] .s2-opt-code { background: rgba(59,127,232,0.15); color: var(--accent); }
.select2-results__option[aria-selected=true] .s2-opt-name { color: var(--accent); }

/* Remove modal */
.alert-danger-soft { background: rgba(232,70,70,0.07); border-left: 3px solid var(--danger); padding: 0.6rem 0.9rem; border-radius: 0 8px 8px 0; font-size: 0.78rem; color: var(--danger); }

/* Buttons */
.btn-cancel { background: var(--surface); border: 1.5px solid var(--border); color: var(--text-secondary); padding: 0.45rem 1.1rem; border-radius: var(--radius-sm); font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: all .18s; font-family: 'Sora', sans-serif; }
.btn-cancel:hover { background: #fff; color: var(--text-primary); }
.btn-primary-ci { background: var(--accent); border: none; color: #fff; padding: 0.45rem 1.3rem; border-radius: var(--radius-sm); font-size: 0.85rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.45rem; cursor: pointer; transition: all .18s; font-family: 'Sora', sans-serif; box-shadow: 0 3px 10px rgba(59,127,232,0.3); }
.btn-primary-ci:hover { background: var(--accent-hover); transform: translateY(-1px); }
.btn-danger-ci  { background: var(--danger); border: none; color: #fff; padding: 0.45rem 1.2rem; border-radius: var(--radius-sm); font-size: 0.85rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.45rem; cursor: pointer; transition: all .18s; font-family: 'Sora', sans-serif; text-decoration: none; }
.btn-danger-ci:hover { background: #CC3535; color: #fff; }

/* Alerts */
.alert { border-radius: var(--radius-sm); border: none; font-size: 0.875rem; }
.alert-success { background: rgba(22,168,125,0.1); color: #0E7A5A; border-left: 3px solid var(--success); }
.alert-danger   { background: rgba(232,70,70,0.08); color: #B03030; border-left: 3px solid var(--danger); }

/* ── RESPONSIVE ──────────────────────────────────────── */
@media (max-width: 991px) {
    .curriculum-layout { grid-template-columns: 1fr; }
    .levels-panel { position: static; margin-bottom: 1rem; }
    .levels-panel-body { display: flex; flex-wrap: wrap; gap: 0.4rem; padding: 0.75rem; }
    .level-nav-item { border-bottom: none; border-radius: var(--radius-sm); border: 1.5px solid var(--border); padding: 0.4rem 0.85rem; flex: 0 0 auto; }
    .level-nav-item:hover { border-color: var(--accent); }
}
@media (max-width: 575px) {
    .ci-page-header { padding: 1.1rem 1.2rem; }
    .ci-page-header h1 { font-size: 1.1rem; }
    .course-banner { flex-direction: column; align-items: flex-start; }
    .level-card-header { flex-direction: column; align-items: flex-start; }
    .ci-table thead th:nth-child(4), .ci-table tbody td:nth-child(4) { display: none; }
}
</style>

<!-- ── PAGE HEADER ─────────────────────────────────────── -->
<div class="ci-page-header mb-4">
    <h1>
        <i class="fas fa-book-open me-2" style="opacity:.7;font-size:1.2rem;"></i>
        <?= $title ?>
    </h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/courses') ?>">Cursos</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/courses/view/' . $course['id']) ?>"><?= $course['course_name'] ?></a></li>
            <li class="breadcrumb-item active" aria-current="page">Currículo</li>
        </ol>
    </nav>
</div>

<!-- ── ALERTS ──────────────────────────────────────────── -->
<?= view('admin/partials/alerts') ?>

<!-- ── COURSE BANNER ───────────────────────────────────── -->
<div class="course-banner">
    <div class="course-banner-info">
        <div class="course-banner-icon"><i class="fas fa-layer-group"></i></div>
        <div>
            <div class="course-banner-name"><?= $course['course_name'] ?></div>
            <div class="d-flex align-items-center gap-2 mt-1 flex-wrap">
                <span class="course-banner-code"><?= $course['course_code'] ?></span>
                <?php if ($course->is_active): ?>
                    <span class="status-pill-active"><span class="status-dot"></span>Ativo</span>
                <?php else: ?>
                    <span class="status-pill-inactive"><span class="status-dot"></span>Inativo</span>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <a href="<?= site_url('admin/courses/view/' . $course['id']) ?>" class="btn-back">
        <i class="fas fa-arrow-left"></i> Voltar ao Curso
    </a>
</div>

<!-- ── CURRICULUM LAYOUT ────────────────────────────────── -->
<div class="curriculum-layout">

    <!-- ── LEVELS PANEL ────────────────────────────────── -->
    <div class="levels-panel">
        <div class="levels-panel-header">
            <i class="fas fa-list" style="opacity:.7;"></i> Níveis do Curso
        </div>
        <div class="levels-panel-body">
            <?php
            $grandTotal = 0;
            foreach ($curriculum as $c) $grandTotal += count($c['disciplines']);
            ?>
            <?php foreach ($levels as $level): ?>
                <?php $count = isset($curriculum[$level['id']]) ? count($curriculum[$level['id']]['disciplines']) : 0; ?>
                <a href="#level-<?= $level['id'] ?>" class="level-nav-item">
                    <span style="display:flex;align-items:center;gap:0.5rem;">
                        <i class="fas fa-layer-group"></i> <?= $level['level_name'] ?>
                    </span>
                    <span class="level-count <?= $count === 0 ? 'zero' : '' ?>"><?= $count ?></span>
                </a>
            <?php endforeach; ?>
        </div>
        <div class="levels-panel-footer">
            <span class="footer-label">Total de Disciplinas</span>
            <span class="footer-value"><?= $grandTotal ?></span>
        </div>
    </div>

    <!-- ── LEVEL CARDS ─────────────────────────────────── -->
    <div>
        <?php foreach ($levels as $index => $level): ?>
        <div class="level-card" id="level-<?= $level['id'] ?>">
            <div class="level-card-header <?= $index % 2 !== 0 ? 'alt' : '' ?>">
                <div class="level-card-title">
                    <i class="fas fa-layer-group"></i> <?= $level['level_name'] ?>
                </div>
                <button type="button" class="btn-add-discipline"
                        data-bs-toggle="modal" data-bs-target="#addDisciplineModal"
                        onclick="setCourseLevel(<?= $course['id'] ?>, <?= $level['id'] ?>)">
                    <i class="fas fa-plus"></i> Adicionar Disciplina
                </button>
            </div>

            <?php if (isset($curriculum[$level['id']]) && !empty($curriculum[$level['id']]['disciplines'])): ?>
                <?php
                    $levelWorkload = 0;
                    foreach ($curriculum[$level['id']]['disciplines'] as $d)
                        $levelWorkload += ($d->workload_hours ?? $d->default_workload ?? 0);
                ?>
                <div class="table-responsive">
                    <table class="ci-table">
                        <thead>
                            <tr>
                                <th>Código</th><th>Disciplina</th>
                                <th class="text-center">Carga Horária</th>
                                <th class="text-center">Semestre</th>
                                <th class="text-center">Obrigatória</th>
                                <th class="text-center">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($curriculum[$level['id']]['disciplines'] as $discipline):
                                $workload = $discipline->workload_hours ?? $discipline->default_workload ?? 0;
                                $sem      = $discipline->semester ?? 'Anual';
                                $semClass = match((string)$sem) { '1' => 'sem-1', '2' => 'sem-2', 'Anual' => 'sem-anual', default => 'sem-other' };
                                $semLabel = match((string)$sem) { '1' => '1º Trimestre', '2' => '2º Trimestre', default => $sem };
                            ?>
                            <tr>
                                <td><span class="disc-code"><?= $discipline['discipline_code'] ?></span></td>
                                <td><span class="disc-name"><?= $discipline['discipline_name'] ?></span></td>
                                <td class="text-center">
                                    <span class="workload-chip"><?= $workload ?>h</span>
                                    <?php if ($discipline->workload_hours && $discipline->workload_hours != $discipline->default_workload): ?>
                                        <div class="workload-default">Padrão: <?= $discipline->default_workload ?>h</div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center"><span class="sem-badge <?= $semClass ?>"><?= $semLabel ?></span></td>
                                <td class="text-center">
                                    <?php if ($discipline->is_mandatory): ?>
                                        <span class="mand-yes"><i class="fas fa-check"></i> Sim</span>
                                    <?php else: ?>
                                        <span class="mand-no"><i class="fas fa-minus"></i> Não</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="d-flex justify-content-center gap-1">
                                        <a href="<?= site_url('admin/courses/curriculum/edit/' . $discipline['id']) ?>" class="row-btn edit" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <button type="button" class="row-btn del"
                                                onclick="confirmRemoveDiscipline(<?= $discipline['id'] ?>, '<?= esc($discipline['discipline_name'], 'js') ?>')"
                                                title="Remover">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="2" style="font-weight:700;color:var(--text-primary);">Total do Nível</td>
                                <td class="text-center"><span class="workload-chip total"><?= $levelWorkload ?>h</span></td>
                                <td colspan="3"></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            <?php else: ?>
                <div class="level-empty">
                    <i class="fas fa-book"></i>
                    <strong>Nenhuma disciplina adicionada</strong>
                    <p>Clique em "Adicionar Disciplina" para construir o currículo deste nível.</p>
                </div>
            <?php endif; ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>

<!-- ── MODAL: ADICIONAR DISCIPLINA ─────────────────────── -->
<div class="modal fade ci-modal" id="addDisciplineModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="<?= site_url('admin/courses/curriculum/add-discipline') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="course_id"      id="modal_course_id">
                <input type="hidden" name="grade_level_id" id="modal_grade_level_id">

                <div class="modal-header primary-header">
                    <h5 class="modal-title">
                        <i class="fas fa-plus-circle"></i> Adicionar Disciplina ao Currículo
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">

                        <!-- ── DISCIPLINE SELECT WITH SEARCH ───── -->
                        <div class="col-12">
                            <label class="form-label-ci" for="discipline_id">
                                Disciplina <span style="color:var(--danger);">*</span>
                            </label>

                            <select id="discipline_id" name="discipline_id" required style="width:100%;">
                                <option value=""></option>
                                <?php
                                    $disciplineModel = new \App\Models\DisciplineModel();
                                    $allDisciplines  = $disciplineModel
                                        ->where('is_active', 1)
                                        ->orderBy('discipline_name', 'ASC')
                                        ->findAll();
                                    foreach ($allDisciplines as $d):
                                ?>
                                    <option value="<?= $d->id ?>"
                                            data-code="<?= esc($d->discipline_code) ?>"
                                            data-workload="<?= (int)($d->workload_hours ?? 0) ?>"
                                            data-approval="<?= (int)($d->approval_grade ?? 10) ?>">
                                        <?= esc($d->discipline_name) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <!-- Info panel — visible after a selection is made -->
                            <div class="disc-info-panel" id="disciplineInfo">
                                <div class="disc-info-name">
                                    <i class="fas fa-book-open" style="color:var(--accent);font-size:.8rem;"></i>
                                    <span id="infoName"></span>
                                    <span class="disc-info-code-tag" id="infoCode"></span>
                                </div>
                                <div class="disc-info-meta">
                                    <div class="disc-info-meta-item">
                                        <i class="fas fa-clock" style="color:var(--accent);"></i>
                                        Carga padrão: <strong id="infoWorkload"></strong>
                                    </div>
                                    <div class="disc-info-meta-item">
                                        <i class="fas fa-star" style="color:var(--warning);"></i>
                                        Nota de aprovação: <strong id="infoApproval"></strong>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- ── WORKLOAD + SEMESTER ──────────────── -->
                        <div class="col-sm-6">
                            <label class="form-label-ci" for="workload_hours">Carga Horária</label>
                            <input type="number" class="form-input-ci" id="workload_hours"
                                   name="workload_hours" placeholder="Usar padrão da disciplina" min="0">
                            <div class="form-hint" id="workload_hint">Deixe em branco para usar o valor padrão</div>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label-ci" for="semester">Semestre / Trimestre</label>
                            <select class="form-select-ci" id="semester" name="semester">
                                <option value="Anual">Anual</option>
                                <option value="1">1º Trimestre</option>
                                <option value="2">2º Trimestre</option>
                            </select>
                        </div>

                        <!-- ── MANDATORY TOGGLE ─────────────────── -->
                        <div class="col-12">
                            <label class="form-label-ci">Obrigatoriedade</label>
                            <label class="toggle-row" for="is_mandatory">
                                <div>
                                    <div class="tl-info">Disciplina Obrigatória</div>
                                    <div class="tl-sub">Disciplina exigida para todos os alunos deste nível</div>
                                </div>
                                <label class="ci-switch">
                                    <input type="checkbox" id="is_mandatory" name="is_mandatory" value="1" checked>
                                    <span class="ci-switch-track"></span>
                                </label>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-primary-ci">
                        <i class="fas fa-save"></i> Adicionar ao Currículo
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- ── MODAL: REMOVER DISCIPLINA ───────────────────────── -->
<div class="modal fade ci-modal" id="removeDisciplineModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header danger-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle"></i> Confirmar Remoção
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p style="font-size:.875rem;margin-bottom:.75rem;">
                    Tem certeza que deseja remover <strong id="removeDisciplineName"></strong> do currículo?
                </p>
                <div class="alert-danger-soft">
                    <i class="fas fa-info-circle me-1"></i>
                    Esta ação não pode ser desfeita. A disciplina será removida apenas deste nível.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="confirmRemoveBtn" class="btn-danger-ci">
                    <i class="fas fa-trash"></i> Remover
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function () {
    'use strict';

    /* ─────────────────────────────────────────────────────
       SELECT2 — custom renderers
    ───────────────────────────────────────────────────── */
    function renderOption(data) {
        if (!data.id) {
            // Placeholder — shown before typing
            return $('<span style="color:var(--text-muted);font-size:.82rem;">Pesquisar disciplina...</span>');
        }
        var code = data.element ? data.element.dataset.code : '';
        return $(
            '<div class="s2-opt">' +
                '<span class="s2-opt-code">' + code + '</span>' +
                '<span class="s2-opt-name">'  + data.text + '</span>' +
            '</div>'
        );
    }

    function renderSelection(data) {
        if (!data.id) return data.text || 'Pesquisar disciplina...';
        var code = data.element ? data.element.dataset.code : '';
        return $(
            '<span>' + data.text +
            ' <span style="font-family:\'JetBrains Mono\',monospace;font-size:.7rem;color:var(--text-muted);">(' + code + ')</span>' +
            '</span>'
        );
    }

    /* ─────────────────────────────────────────────────────
       SELECT2 — init (called once the modal first opens)
    ───────────────────────────────────────────────────── */
    var s2Inited = false;

    function initSelect2() {
        if (s2Inited || typeof $.fn.select2 === 'undefined') return;
        s2Inited = true;

        $('#discipline_id').select2({
            dropdownParent:    $('#addDisciplineModal'),
            placeholder:       'Pesquisar pelo nome ou código...',
            allowClear:        true,
            width:             '100%',
            templateResult:    renderOption,
            templateSelection: renderSelection,
            language: {
                noResults: function () { return 'Nenhuma disciplina encontrada'; },
                searching: function () { return 'Pesquisando...'; }
            }
        });

        // Wire up the change event AFTER Select2 is applied
        $('#discipline_id').on('select2:select', function (e) {
            var el = e.params.data.element;
            showInfo(el);
        }).on('select2:clear', clearInfo);
    }

    /* ─────────────────────────────────────────────────────
       Info panel helpers
    ───────────────────────────────────────────────────── */
    function showInfo(el) {
        if (!el || !el.value) { clearInfo(); return; }
        document.getElementById('infoName').textContent     = el.text || el.innerText || '';
        document.getElementById('infoCode').textContent     = el.dataset.code     || '—';
        document.getElementById('infoWorkload').textContent = (el.dataset.workload || 0) + ' horas';
        document.getElementById('infoApproval').textContent = (el.dataset.approval || 10) + ' valores';
        document.getElementById('workload_hint').textContent = 'Padrão: ' + (el.dataset.workload || 0) + ' horas';
        document.getElementById('disciplineInfo').classList.add('show');
    }

    function clearInfo() {
        document.getElementById('disciplineInfo').classList.remove('show');
        document.getElementById('workload_hint').textContent = 'Deixe em branco para usar o valor padrão';
    }

    /* ─────────────────────────────────────────────────────
       Modal events
    ───────────────────────────────────────────────────── */
    var addModal = document.getElementById('addDisciplineModal');
    if (addModal) {
        addModal.addEventListener('shown.bs.modal', function () {
            initSelect2();
            // Auto-focus the search field each time the modal opens
            setTimeout(function () {
                var field = addModal.querySelector('.select2-search__field');
                if (field) field.focus();
            }, 120);
        });
    }

    /* ─────────────────────────────────────────────────────
       setCourseLevel — called from each level's button
    ───────────────────────────────────────────────────── */
    window.setCourseLevel = function (courseId, levelId) {
        document.getElementById('modal_course_id').value      = courseId;
        document.getElementById('modal_grade_level_id').value = levelId;

        // Reset Select2 selection
        if (typeof $.fn.select2 !== 'undefined' && $('#discipline_id').data('select2')) {
            $('#discipline_id').val(null).trigger('change');
        } else {
            document.getElementById('discipline_id').value = '';
        }

        document.getElementById('workload_hours').value = '';
        document.getElementById('semester').value       = 'Anual';
        document.getElementById('is_mandatory').checked = true;
        clearInfo();
    };

    /* ─────────────────────────────────────────────────────
       Remove discipline confirmation
    ───────────────────────────────────────────────────── */
    window.confirmRemoveDiscipline = function (id, name) {
        document.getElementById('removeDisciplineName').textContent = name;
        document.getElementById('confirmRemoveBtn').href =
            '<?= site_url('admin/courses/curriculum/remove/') ?>' + id;
        new bootstrap.Modal(document.getElementById('removeDisciplineModal')).show();
    };

    /* ─────────────────────────────────────────────────────
       Level nav active highlight on click
    ───────────────────────────────────────────────────── */
    document.querySelectorAll('.level-nav-item').forEach(function (link) {
        link.addEventListener('click', function () {
            document.querySelectorAll('.level-nav-item').forEach(function (l) {
                l.classList.remove('active-level');
            });
            this.classList.add('active-level');
        });
    });

    /* ─────────────────────────────────────────────────────
       AJAX — reload discipline list when level changes
    ───────────────────────────────────────────────────── */
    var gradeLevelSelect = document.getElementById('grade_level_id');
    if (gradeLevelSelect) {
        gradeLevelSelect.addEventListener('change', function () {
            var courseId = document.getElementById('modal_course_id').value;
            var levelId  = this.value;
            if (!courseId || !levelId) return;

            fetch('<?= site_url('admin/courses/curriculum/get-available/') ?>' + courseId + '/' + levelId, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(function (r) { return r.json(); })
            .then(function (list) {
                var sel = document.getElementById('discipline_id');
                sel.innerHTML = '<option value=""></option>';
                list.forEach(function (d) {
                    var opt = new Option(d.discipline_name, d.id, false, false);
                    opt.dataset.code     = d.discipline_code;
                    opt.dataset.workload = d.workload_hours;
                    opt.dataset.approval = d.approval_grade;
                    sel.appendChild(opt);
                });
                if (typeof $.fn.select2 !== 'undefined' && $('#discipline_id').data('select2')) {
                    $('#discipline_id').trigger('change');
                }
            })
            .catch(function (err) { console.error('Erro ao carregar disciplinas:', err); });
        });
    }

})();
</script>
<?= $this->endSection() ?>