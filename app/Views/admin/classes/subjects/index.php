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
.hdr-btn { display:inline-flex; align-items:center; gap:.45rem; border-radius:var(--radius-sm); padding:.45rem 1rem; font-size:.82rem; font-weight:600; text-decoration:none; transition:all .18s; cursor:pointer; border:none; font-family:'Sora',sans-serif; white-space:nowrap; }
.hdr-btn.primary { background:var(--accent); color:#fff; box-shadow:0 3px 10px rgba(59,127,232,.28); }
.hdr-btn.primary:hover { background:var(--accent-hover); color:#fff; transform:translateY(-1px); }
.hdr-btn.ghost { background:rgba(255,255,255,.12); color:#fff; border:1.5px solid rgba(255,255,255,.2); }
.hdr-btn.ghost:hover { background:rgba(255,255,255,.22); color:#fff; }

/* ── STAT CARDS ──────────────────────────────────────────── */
.stat-row { display:grid; grid-template-columns:repeat(4,1fr); gap:.85rem; margin-bottom:1.25rem; }
@media(max-width:900px){ .stat-row { grid-template-columns:repeat(2,1fr); } }
.stat-card { background:var(--surface-card); border:1px solid var(--border); border-radius:var(--radius); padding:1rem 1.1rem; display:flex; align-items:center; gap:.85rem; box-shadow:var(--shadow-sm); }
.stat-card-icon { width:42px; height:42px; border-radius:10px; flex-shrink:0; display:flex; align-items:center; justify-content:center; font-size:.95rem; }
.sci-blue   { background:rgba(59,127,232,.1);  color:var(--accent); }
.sci-green  { background:rgba(22,168,125,.1);  color:var(--success); }
.sci-amber  { background:rgba(232,160,32,.1);  color:var(--warning); }
.sci-navy   { background:rgba(27,43,75,.1);    color:var(--primary); }
.stat-card-val { font-size:1.5rem; font-weight:700; color:var(--text-primary); line-height:1; font-family:'JetBrains Mono',monospace; }
.stat-card-lbl { font-size:.69rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; margin-top:.15rem; }

/* ── MAIN CARD ───────────────────────────────────────────── */
.ci-card { background:var(--surface-card); border:1px solid var(--border); border-radius:var(--radius); box-shadow:var(--shadow-sm); overflow:hidden; margin-bottom:1.25rem; }
.ci-card-header { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.5rem; padding:.85rem 1.25rem; background:var(--surface); border-bottom:1px solid var(--border); }
.ci-card-title  { display:flex; align-items:center; gap:.55rem; font-size:.82rem; font-weight:700; color:var(--text-primary); }
.ci-card-title i { color:var(--accent); font-size:.8rem; }

/* ── TABLE ───────────────────────────────────────────────── */
.ci-table { width:100%; border-collapse:separate; border-spacing:0; }
.ci-table thead tr th { background:var(--surface); color:var(--text-secondary); font-size:.63rem; font-weight:700; text-transform:uppercase; letter-spacing:.09em; padding:.65rem 1rem; border-bottom:1.5px solid var(--border); white-space:nowrap; }
.ci-table tbody tr { transition:background .12s; }
.ci-table tbody tr:not(:last-child) td { border-bottom:1px solid var(--border); }
.ci-table tbody tr:hover { background:#F5F8FF; }
.ci-table tbody td { padding:.7rem 1rem; vertical-align:middle; font-size:.83rem; }

/* ── CHIPS ───────────────────────────────────────────────── */
.id-chip { font-family:'JetBrains Mono',monospace; font-size:.7rem; font-weight:600; background:rgba(27,43,75,.07); color:var(--primary); padding:.15rem .45rem; border-radius:5px; }
.code-badge { font-family:'JetBrains Mono',monospace; font-size:.72rem; font-weight:600; background:rgba(59,127,232,.1); color:var(--accent); padding:.18rem .55rem; border-radius:6px; border:1px solid rgba(59,127,232,.18); }
.type-badge { display:inline-flex; align-items:center; gap:.3rem; font-size:.69rem; font-weight:700; padding:.2rem .6rem; border-radius:50px; }
.type-obrig { background:rgba(27,43,75,.08);   color:var(--primary); }
.type-opcio { background:rgba(22,168,125,.1);  color:var(--success); }
.type-compl { background:rgba(232,160,32,.1);  color:var(--warning); }
.grade-chip { font-family:'JetBrains Mono',monospace; font-size:.72rem; font-weight:600; color:var(--text-secondary); }
.workload-chip { font-family:'JetBrains Mono',monospace; font-size:.72rem; font-weight:600; background:var(--surface); border:1px solid var(--border); padding:.14rem .45rem; border-radius:5px; color:var(--text-secondary); }
.status-dot { display:inline-flex; align-items:center; gap:.3rem; font-size:.72rem; font-weight:700; }
.sd { width:7px; height:7px; border-radius:50%; flex-shrink:0; }
.sd-active   { background:var(--success); box-shadow:0 0 0 2px rgba(22,168,125,.2); }
.sd-inactive { background:var(--text-muted); }
.st-active   { color:var(--success); }
.st-inactive { color:var(--text-muted); }

/* ── ROW ACTIONS ─────────────────────────────────────────── */
.row-btn { width:28px; height:28px; border-radius:7px; border:1.5px solid var(--border); background:#fff; color:var(--text-secondary); display:inline-flex; align-items:center; justify-content:center; font-size:.72rem; text-decoration:none; cursor:pointer; transition:all .18s; }
.row-btn:hover { color:#fff; border-color:transparent; }
.row-btn.edit:hover  { background:var(--accent); }
.row-btn.del:hover   { background:var(--danger); }
.row-actions { display:flex; gap:.35rem; }

/* ── EMPTY ───────────────────────────────────────────────── */
.ci-empty { text-align:center; padding:3rem; color:var(--text-muted); }
.ci-empty i { font-size:2rem; opacity:.12; display:block; margin-bottom:.6rem; }
.ci-empty p { font-size:.82rem; margin:0; }

/* ── DT OVERRIDES ────────────────────────────────────────── */
div.dataTables_wrapper div.dataTables_filter input,
div.dataTables_wrapper div.dataTables_length select {
    border:1.5px solid var(--border); border-radius:var(--radius-sm);
    padding:.35rem .7rem; font-size:.82rem; font-family:'Sora',sans-serif;
    color:var(--text-primary); background:var(--surface);
    outline:none; transition:border-color .18s;
}
div.dataTables_wrapper div.dataTables_filter input:focus { border-color:var(--accent); }
div.dataTables_wrapper div.dataTables_filter label,
div.dataTables_wrapper div.dataTables_length label { font-size:.78rem; color:var(--text-secondary); }
div.dataTables_wrapper div.dataTables_info { font-size:.76rem; color:var(--text-muted); }
div.dataTables_wrapper div.dataTables_paginate .paginate_button {
    border-radius:var(--radius-sm) !important; font-size:.78rem !important;
    border:1.5px solid var(--border) !important; background:#fff !important;
    color:var(--text-secondary) !important; margin:0 2px; padding:.25rem .6rem !important;
    transition:all .15s;
}
div.dataTables_wrapper div.dataTables_paginate .paginate_button:hover {
    background:rgba(59,127,232,.08) !important; border-color:var(--accent) !important;
    color:var(--accent) !important;
}
div.dataTables_wrapper div.dataTables_paginate .paginate_button.current {
    background:var(--accent) !important; border-color:var(--accent) !important;
    color:#fff !important; font-weight:700 !important;
}
div.dataTables_wrapper div.dataTables_paginate .paginate_button.disabled { opacity:.35 !important; }
div.dataTables_wrapper .dt-buttons { display:flex; gap:.4rem; }

/* ── MODAL REDESIGN ──────────────────────────────────────── */
.modal-content {
    border:none; border-radius:var(--radius);
    box-shadow:var(--shadow-lg); overflow:hidden;
    font-family:'Sora',sans-serif;
}
.modal-header {
    background:linear-gradient(135deg,var(--primary),var(--primary-light));
    border-bottom:none; padding:1.1rem 1.4rem;
    position:relative; overflow:hidden;
}
.modal-header::after {
    content:''; position:absolute; bottom:-20px; right:-20px;
    width:80px; height:80px; border-radius:50%;
    background:rgba(59,127,232,.18); pointer-events:none;
}
.modal-title { font-size:.96rem; font-weight:700; color:#fff; display:flex; align-items:center; gap:.5rem; }
.modal-title i { opacity:.8; }
.btn-close { filter:brightness(0) invert(1) opacity(.6); }
.btn-close:hover { opacity:1 !important; filter:brightness(0) invert(1) opacity(1); }
.modal-body { padding:1.4rem; background:var(--surface-card); }
.modal-footer {
    background:var(--surface); border-top:1px solid var(--border);
    padding:.85rem 1.4rem; gap:.5rem;
}

.f-label { font-size:.72rem; font-weight:600; color:var(--text-secondary); margin-bottom:.32rem; display:block; text-transform:uppercase; letter-spacing:.04em; }
.f-label i { color:var(--accent); margin-right:.25rem; font-size:.68rem; }
.f-label .req { color:var(--danger); margin-left:.15rem; }
.f-input, .f-select, .f-textarea {
    width:100%; border:1.5px solid var(--border); border-radius:var(--radius-sm);
    padding:.52rem .85rem; font-size:.84rem; color:var(--text-primary);
    background:var(--surface); font-family:'Sora',sans-serif;
    transition:border-color .18s, box-shadow .18s; outline:none;
}
.f-input:focus, .f-select:focus, .f-textarea:focus {
    border-color:var(--accent); box-shadow:0 0 0 3px rgba(59,127,232,.1); background:#fff;
}
.f-textarea { resize:vertical; min-height:80px; }
.f-input-group { display:flex; }
.f-input-group .f-input { border-radius:var(--radius-sm) 0 0 var(--radius-sm); }
.f-input-group .f-addon {
    display:flex; align-items:center; padding:0 .75rem;
    background:var(--surface); border:1.5px solid var(--border); border-left:none;
    border-radius:0 var(--radius-sm) var(--radius-sm) 0;
    font-size:.8rem; color:var(--text-muted); white-space:nowrap;
    font-family:'JetBrains Mono',monospace;
}
.f-hint { font-size:.7rem; color:var(--text-muted); margin-top:.25rem; }

/* custom toggle checkbox */
.f-toggle-wrap { display:flex; align-items:center; gap:.65rem; cursor:pointer; padding:.5rem 0; }
.f-toggle-wrap input { display:none; }
.f-toggle {
    width:36px; height:20px; border-radius:10px; background:var(--border);
    position:relative; transition:background .2s; flex-shrink:0;
}
.f-toggle::after {
    content:''; position:absolute; left:3px; top:3px;
    width:14px; height:14px; border-radius:50%; background:#fff;
    transition:transform .2s; box-shadow:0 1px 3px rgba(0,0,0,.2);
}
.f-toggle-wrap input:checked + .f-toggle { background:var(--accent); }
.f-toggle-wrap input:checked + .f-toggle::after { transform:translateX(16px); }
.f-toggle-lbl { font-size:.82rem; color:var(--text-secondary); font-weight:500; }
.f-toggle-lbl strong { color:var(--text-primary); }

.modal-sec-divider { display:flex; align-items:center; gap:.5rem; margin:1rem 0 .75rem; }
.modal-sec-label { font-size:.62rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:var(--text-muted); white-space:nowrap; }
.modal-sec-label i { color:var(--accent); margin-right:.2rem; font-size:.6rem; }
.modal-sec-line { flex:1; height:1px; background:var(--border); }

.btn-save { display:inline-flex; align-items:center; gap:.45rem; padding:.52rem 1.35rem; border-radius:var(--radius-sm); background:var(--accent); color:#fff; border:none; font-size:.84rem; font-weight:600; cursor:pointer; font-family:'Sora',sans-serif; transition:all .18s; box-shadow:0 3px 10px rgba(59,127,232,.28); }
.btn-save:hover { background:var(--accent-hover); transform:translateY(-1px); }
.btn-mcancel { display:inline-flex; align-items:center; gap:.4rem; padding:.5rem 1rem; border-radius:var(--radius-sm); background:#fff; color:var(--text-secondary); border:1.5px solid var(--border); font-size:.82rem; font-weight:600; cursor:pointer; font-family:'Sora',sans-serif; transition:all .18s; }
.btn-mcancel:hover { border-color:var(--text-secondary); color:var(--text-primary); }

/* Alerts */
.alert { border-radius:var(--radius-sm); border:none; font-size:.875rem; }
.alert-success { background:rgba(22,168,125,.1); color:#0E7A5A; border-left:3px solid var(--success); }
.alert-danger   { background:rgba(232,70,70,.08);  color:#B03030; border-left:3px solid var(--danger); }

@media(max-width:767px){
    .ci-page-header { padding:1.1rem 1.2rem; }
    .ci-page-header h1 { font-size:1.1rem; }
    .stat-row { grid-template-columns:1fr 1fr; }
}
</style>

<!-- ── PAGE HEADER ─────────────────────────────────────────── -->
<div class="ci-page-header mb-4">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-book me-2" style="opacity:.7;font-size:1.1rem;"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/classes') ?>">Classes</a></li>
                    <li class="breadcrumb-item active">Disciplinas</li>
                </ol>
            </nav>
        </div>
        <button type="button" class="hdr-btn primary" data-bs-toggle="modal" data-bs-target="#subjectModal" id="btnNewSubject">
            <i class="fas fa-plus"></i> Nova Disciplina
        </button>
    </div>
</div>

<?= view('admin/partials/alerts') ?>

<?php
/* ── Pre-compute stats ── */
$total     = count($subjects ?? []);
$active    = count(array_filter($subjects ?? [], fn($s) => $s['is_active']));
$inactive  = $total - $active;
$obrig     = count(array_filter($subjects ?? [], fn($s) => ($s['discipline_type'] ?? '') === 'Obrigatória'));
?>

<!-- ── STAT CARDS ──────────────────────────────────────────── -->
<div class="stat-row">
    <div class="stat-card">
        <div class="stat-card-icon sci-blue"><i class="fas fa-book"></i></div>
        <div>
            <div class="stat-card-val"><?= $total ?></div>
            <div class="stat-card-lbl">Total de Disciplinas</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon sci-green"><i class="fas fa-check-circle"></i></div>
        <div>
            <div class="stat-card-val"><?= $active ?></div>
            <div class="stat-card-lbl">Ativas</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon sci-navy"><i class="fas fa-lock"></i></div>
        <div>
            <div class="stat-card-val"><?= $obrig ?></div>
            <div class="stat-card-lbl">Obrigatórias</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon sci-amber"><i class="fas fa-times-circle"></i></div>
        <div>
            <div class="stat-card-val"><?= $inactive ?></div>
            <div class="stat-card-lbl">Inativas</div>
        </div>
    </div>
</div>

<!-- ── TABLE CARD ─────────────────────────────────────────── -->
<div class="ci-card">
    <div class="ci-card-header">
        <div class="ci-card-title"><i class="fas fa-list"></i> Lista de Disciplinas</div>
        <span style="font-size:.75rem;color:var(--text-muted);font-family:'JetBrains Mono',monospace;"><?= $total ?> registos</span>
    </div>
    <div style="overflow-x:auto;">
        <table id="subjectsTable" class="ci-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Disciplina</th>
                    <th>Código</th>
                    <th>Tipo</th>
                    <th style="text-align:center;">Carga</th>
                    <th style="text-align:center;">Mín</th>
                    <th style="text-align:center;">Máx</th>
                    <th style="text-align:center;">Aprovação</th>
                    <th style="text-align:center;">Estado</th>
                    <th style="text-align:center;">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($subjects)): ?>
                    <?php foreach ($subjects as $subject): ?>
                    <tr>
                        <td><span class="id-chip">#<?= $subject['id'] ?></span></td>

                        <td>
                            <span style="font-weight:600;color:var(--text-primary);font-size:.83rem;">
                                <?= esc($subject['discipline_name']) ?>
                            </span>
                        </td>

                        <td><span class="code-badge"><?= esc($subject['discipline_code']) ?></span></td>

                        <td>
                            <?php
                            $typeClass = match($subject['discipline_type ']?? '') {
                                'Obrigatória'  => 'type-obrig',
                                'Opcional'     => 'type-opcio',
                                'Complementar' => 'type-compl',
                                default        => 'type-obrig'
                            };
                            $typeIcon = match($subject['discipline_type ']?? '') {
                                'Obrigatória'  => 'fa-lock',
                                'Opcional'     => 'fa-unlock',
                                'Complementar' => 'fa-plus-circle',
                                default        => 'fa-tag'
                            };
                            ?>
                            <span class="type-badge <?= $typeClass ?>">
                                <i class="fas <?= $typeIcon ?>"></i>
                                <?= esc($subject['discipline_type']) ?>
                            </span>
                        </td>

                        <td style="text-align:center;">
                            <span class="workload-chip">
                                <?= $subject['workload_hours'] ? esc($subject['workload_hours']).'h' : '—' ?>
                            </span>
                        </td>

                        <td style="text-align:center;"><span class="grade-chip"><?= esc($subject['min_grade']) ?></span></td>
                        <td style="text-align:center;"><span class="grade-chip"><?= esc($subject['max_grade']) ?></span></td>
                        <td style="text-align:center;">
                            <span class="grade-chip" style="color:var(--accent);font-weight:700;"><?= esc($subject['approval_grade']) ?></span>
                        </td>

                        <td style="text-align:center;">
                            <?php if ($subject['is_active']): ?>
                                <span class="status-dot"><span class="sd sd-active"></span><span class="st-active">Ativa</span></span>
                            <?php else: ?>
                                <span class="status-dot"><span class="sd sd-inactive"></span><span class="st-inactive">Inativa</span></span>
                            <?php endif; ?>
                        </td>

                        <td style="text-align:center;">
                            <div class="row-actions" style="justify-content:center;">
                                <button type="button" class="row-btn edit edit-subject"
                                        title="Editar"
                                        data-id="<?= $subject['id'] ?>"
                                        data-name="<?= esc($subject['discipline_name'], 'attr') ?>"
                                        data-code="<?= esc($subject['discipline_code'], 'attr') ?>"
                                        data-type="<?= esc($subject['discipline_type'], 'attr') ?>"
                                        data-hours="<?= esc($subject['workload_hours'], 'attr') ?>"
                                        data-min="<?= esc($subject['min_grade'], 'attr') ?>"
                                        data-max="<?= esc($subject['max_grade'], 'attr') ?>"
                                        data-approval="<?= esc($subject['approval_grade'], 'attr') ?>"
                                        data-description="<?= esc($subject['description'], 'attr') ?>"
                                        data-active="<?= $subject['is_active'] ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <a href="<?= site_url('admin/classes/subjects/delete/' . $subject['id']) ?>"
                                   class="row-btn del"
                                   title="Eliminar"
                                   onclick="return confirm('Eliminar a disciplina «<?= esc($subject['discipline_name'], 'attr') ?>»? Esta ação não pode ser revertida.')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>

        <?php if (empty($subjects)): ?>
        <div class="ci-empty">
            <i class="fas fa-book-open"></i>
            <p>Nenhuma disciplina encontrada. Crie a primeira disciplina.</p>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ═══════════════════════════════════════════════════════
     MODAL: Nova / Editar Disciplina
═══════════════════════════════════════════════════════ -->
<div class="modal fade" id="subjectModal" tabindex="-1" aria-labelledby="modalTitle" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form action="<?= site_url('admin/classes/subjects/save') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="subjectId">

                <!-- Header -->
                <div class="modal-header">
                    <div class="modal-title">
                        <i class="fas fa-book" id="modalIcon"></i>
                        <span id="modalTitle">Nova Disciplina</span>
                    </div>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Fechar"></button>
                </div>

                <!-- Body -->
                <div class="modal-body">

                    <!-- Nome + Código -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-8">
                            <label class="f-label"><i class="fas fa-font"></i>Nome da Disciplina<span class="req">*</span></label>
                            <input type="text" class="f-input" id="discipline_name" name="discipline_name"
                                   placeholder="Ex: Matemática, Língua Portuguesa..." required>
                        </div>
                        <div class="col-md-4">
                            <label class="f-label"><i class="fas fa-hashtag"></i>Código<span class="req">*</span></label>
                            <input type="text" class="f-input" id="discipline_code" name="discipline_code"
                                   placeholder="Ex: MAT-01" required>
                        </div>
                    </div>

                    <!-- Tipo + Carga -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <label class="f-label"><i class="fas fa-tag"></i>Tipo</label>
                            <select class="f-select" id="discipline_type" name="discipline_type">
                                <option value="Obrigatória">Obrigatória</option>
                                <option value="Opcional">Opcional</option>
                                <option value="Complementar">Complementar</option>
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="f-label"><i class="fas fa-clock"></i>Carga Horária</label>
                            <div class="f-input-group">
                                <input type="number" class="f-input" id="workload_hours" name="workload_hours"
                                       min="0" placeholder="0">
                                <span class="f-addon">horas</span>
                            </div>
                        </div>
                    </div>

                    <!-- Notas -->
                    <div class="modal-sec-divider">
                        <span class="modal-sec-label"><i class="fas fa-star"></i>Sistema de Avaliação</span>
                        <div class="modal-sec-line"></div>
                    </div>

                    <div class="row g-3 mb-3">
                        <div class="col-md-4">
                            <label class="f-label">Nota Mínima</label>
                            <div class="f-input-group">
                                <input type="number" class="f-input" id="min_grade" name="min_grade"
                                       step="0.01" min="0" max="20" value="0">
                                <span class="f-addon">val</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="f-label">Nota Máxima</label>
                            <div class="f-input-group">
                                <input type="number" class="f-input" id="max_grade" name="max_grade"
                                       step="0.01" min="0" max="20" value="20">
                                <span class="f-addon">val</span>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label class="f-label" style="color:var(--accent);">Nota de Aprovação</label>
                            <div class="f-input-group">
                                <input type="number" class="f-input" id="approval_grade" name="approval_grade"
                                       step="0.01" min="0" max="20" value="10"
                                       style="border-color:rgba(59,127,232,.35);">
                                <span class="f-addon">val</span>
                            </div>
                        </div>
                    </div>

                    <!-- Descrição -->
                    <div class="modal-sec-divider">
                        <span class="modal-sec-label"><i class="fas fa-align-left"></i>Informação Adicional</span>
                        <div class="modal-sec-line"></div>
                    </div>

                    <div class="mb-3">
                        <label class="f-label"><i class="fas fa-file-alt"></i>Descrição</label>
                        <textarea class="f-textarea" id="description" name="description"
                                  placeholder="Descrição opcional da disciplina, ementa, objetivos..."></textarea>
                    </div>

                    <!-- Toggle ativo -->
                    <label class="f-toggle-wrap">
                        <input type="checkbox" id="is_active" name="is_active" value="1" checked>
                        <span class="f-toggle"></span>
                        <span class="f-toggle-lbl"><strong>Disciplina ativa</strong> — visível e disponível para atribuição a turmas</span>
                    </label>

                </div>

                <!-- Footer -->
                <div class="modal-footer">
                    <button type="button" class="btn-mcancel" data-bs-dismiss="modal">
                        <i class="fas fa-times"></i> Cancelar
                    </button>
                    <button type="submit" class="btn-save">
                        <i class="fas fa-save"></i> Guardar Disciplina
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function () {

    // ── DataTable ─────────────────────────────────────────────
    $('#subjectsTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json' },
        order: [[1, 'asc']],
        columnDefs: [
            { orderable: false, targets: [9] },
            { searchable: false, targets: [5, 6, 7, 9] }
        ],
        pageLength: 25,
        dom: '<"d-flex align-items-center justify-content-between mb-3 px-0"lf>rt<"d-flex align-items-center justify-content-between mt-3 px-0"ip>',
    });

    // ── Open Edit Modal ───────────────────────────────────────
    $(document).on('click', '.edit-subject', function () {
        const $btn = $(this);

        $('#modalTitle').text('Editar Disciplina');
        $('#modalIcon').attr('class', 'fas fa-edit');
        $('#subjectId').val($btn.data('id'));
        $('#discipline_name').val($btn.data('name'));
        $('#discipline_code').val($btn.data('code'));
        $('#discipline_type').val($btn.data('type'));
        $('#workload_hours').val($btn.data('hours'));
        $('#min_grade').val(parseFloat($btn.data('min'))      || 0);
        $('#max_grade').val(parseFloat($btn.data('max'))      || 20);
        $('#approval_grade').val(parseFloat($btn.data('approval')) || 10);
        $('#description').val($btn.data('description'));
        $('#is_active').prop('checked', parseInt($btn.data('active')) === 1);

        $('#subjectModal').modal('show');
    });

    // ── Open New Modal ────────────────────────────────────────
    $('#btnNewSubject').on('click', function () {
        resetModal();
    });

    // ── Reset on close ────────────────────────────────────────
    $('#subjectModal').on('hidden.bs.modal', function () {
        if (!$('#subjectId').val()) resetModal();
    });

    function resetModal() {
        $('#modalTitle').text('Nova Disciplina');
        $('#modalIcon').attr('class', 'fas fa-book');
        $('#subjectId').val('');
        $('#discipline_name').val('');
        $('#discipline_code').val('');
        $('#discipline_type').val('Obrigatória');
        $('#workload_hours').val('');
        $('#min_grade').val('0');
        $('#max_grade').val('20');
        $('#approval_grade').val('10');
        $('#description').val('');
        $('#is_active').prop('checked', true);
    }
});
</script>
<?= $this->endSection() ?>