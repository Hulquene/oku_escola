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
.hdr-btn { display:inline-flex; align-items:center; gap:.45rem; border-radius:var(--radius-sm); padding:.45rem 1rem; font-size:.82rem; font-weight:600; text-decoration:none; transition:all .18s; cursor:pointer; border:none; font-family:'Sora',sans-serif; white-space:nowrap; }
.hdr-btn.ghost { background:rgba(255,255,255,.12); color:#fff; border:1.5px solid rgba(255,255,255,.2); }
.hdr-btn.ghost:hover { background:rgba(255,255,255,.22); color:#fff; }

/* ── LAYOUT: SIDEBAR TABS + CONTENT ─────────────────────── */
.settings-layout { display:grid; grid-template-columns:220px 1fr; gap:1.25rem; align-items:start; }
@media(max-width:900px){ .settings-layout { grid-template-columns:1fr; } }

/* ── VERTICAL TAB NAV ────────────────────────────────────── */
.settings-nav { background:var(--surface-card); border:1px solid var(--border); border-radius:var(--radius); box-shadow:var(--shadow-sm); overflow:hidden; position:sticky; top:80px; }
.settings-nav-header { padding:.85rem 1rem .6rem; border-bottom:1px solid var(--border); background:var(--surface); }
.settings-nav-label { font-size:.62rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:var(--text-muted); }
.settings-nav ul { list-style:none; margin:0; padding:.4rem 0; }
.settings-nav li { margin:0; }
.settings-nav-item {
    display:flex; align-items:center; gap:.6rem;
    padding:.55rem 1rem;
    font-size:.81rem; font-weight:500; color:var(--text-secondary);
    cursor:pointer; text-decoration:none;
    transition:all .15s;
    border:none; background:transparent; width:100%; text-align:left;
    position:relative;
}
.settings-nav-item:hover { background:var(--surface); color:var(--text-primary); }
.settings-nav-item.active { color:var(--accent); font-weight:600; background:rgba(59,127,232,.06); }
.settings-nav-item.active::before {
    content:''; position:absolute; left:0; top:20%; height:60%;
    width:3px; background:var(--accent); border-radius:0 3px 3px 0;
}
.settings-nav-item .nav-ico {
    width:26px; height:26px; border-radius:7px;
    display:flex; align-items:center; justify-content:center;
    font-size:.72rem; flex-shrink:0;
    background:var(--surface); color:var(--text-muted);
    transition:all .15s;
}
.settings-nav-item:hover .nav-ico { background:rgba(59,127,232,.08); color:var(--accent); }
.settings-nav-item.active .nav-ico { background:rgba(59,127,232,.12); color:var(--accent); }
.settings-nav-divider { height:1px; background:var(--border); margin:.3rem .8rem; }

/* ── CARD ────────────────────────────────────────────────── */
.ci-card { background:var(--surface-card); border:1px solid var(--border); border-radius:var(--radius); box-shadow:var(--shadow-sm); overflow:hidden; margin-bottom:1rem; }
.ci-card-header { display:flex; align-items:center; justify-content:space-between; gap:.5rem; padding:.85rem 1.25rem; background:var(--surface); border-bottom:1px solid var(--border); }
.ci-card-title { display:flex; align-items:center; gap:.55rem; font-size:.84rem; font-weight:700; color:var(--text-primary); }
.ci-card-title i { color:var(--accent); font-size:.8rem; }
.ci-card-body { padding:1.25rem; }

/* ── SECTION DIVIDER ─────────────────────────────────────── */
.sec-divider { display:flex; align-items:center; gap:.55rem; margin:1.4rem 0 1rem; }
.sec-divider-label { font-size:.65rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:var(--text-muted); white-space:nowrap; }
.sec-divider-label i { color:var(--accent); margin-right:.25rem; font-size:.62rem; }
.sec-divider-line { flex:1; height:1px; background:var(--border); }

/* ── FORM FIELDS ─────────────────────────────────────────── */
.f-label { font-size:.72rem; font-weight:600; color:var(--text-secondary); margin-bottom:.32rem; display:block; text-transform:uppercase; letter-spacing:.04em; }
.f-label i { color:var(--accent); margin-right:.25rem; font-size:.68rem; }
.f-label .req { color:var(--danger); margin-left:.15rem; }
.f-input, .f-select {
    width:100%; border:1.5px solid var(--border); border-radius:var(--radius-sm);
    padding:.52rem .85rem; font-size:.84rem; color:var(--text-primary);
    background:var(--surface); font-family:'Sora',sans-serif;
    transition:border-color .18s, box-shadow .18s;
    outline:none;
}
.f-input:focus, .f-select:focus {
    border-color:var(--accent);
    box-shadow:0 0 0 3px rgba(59,127,232,.1);
    background:#fff;
}
.f-input.is-invalid, .f-select.is-invalid { border-color:var(--danger); }
.f-input.is-invalid:focus { box-shadow:0 0 0 3px rgba(232,70,70,.1); }
.f-hint { font-size:.7rem; color:var(--text-muted); margin-top:.25rem; }
.invalid-msg { font-size:.72rem; color:var(--danger); margin-top:.25rem; }
.f-input-group { display:flex; }
.f-input-group .f-input { border-radius:var(--radius-sm) 0 0 var(--radius-sm); }
.f-input-group .f-addon {
    display:flex; align-items:center; padding:0 .75rem;
    background:var(--surface); border:1.5px solid var(--border); border-left:none;
    border-radius:0 var(--radius-sm) var(--radius-sm) 0;
    font-size:.8rem; color:var(--text-muted); white-space:nowrap;
}

/* ── LOGO/MEDIA UPLOAD BOXES ─────────────────────────────── */
.media-upload-grid { display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; }
@media(max-width:700px){ .media-upload-grid { grid-template-columns:1fr 1fr; } }

.media-box {
    border:1.5px dashed var(--border); border-radius:var(--radius-sm);
    background:var(--surface); overflow:hidden;
    transition:border-color .2s;
}
.media-box:hover { border-color:var(--accent); }
.media-box.has-image { border-style:solid; border-color:var(--accent); }

.media-preview {
    height:130px; display:flex; align-items:center; justify-content:center;
    position:relative; overflow:hidden;
    cursor:pointer;
}
.media-preview img { max-width:100%; max-height:100%; object-fit:contain; padding:.5rem; }
.media-preview .ph { text-align:center; color:var(--text-muted); }
.media-preview .ph i { font-size:1.8rem; opacity:.2; display:block; margin-bottom:.4rem; }
.media-preview .ph span { font-size:.72rem; }

/* Dark background preview for dark logo */
.media-preview.dark-bg { background:linear-gradient(135deg,#0D1B33,#1B2B4B); }
.media-preview.dark-bg .ph { color:rgba(255,255,255,.3); }
.media-preview.dark-bg .ph i { opacity:.3; }

/* Favicon bg checkered */
.media-preview.favicon-bg {
    background-image: repeating-conic-gradient(#e8eaf0 0% 25%, #f5f7fc 0% 50%);
    background-size: 16px 16px;
}

.media-footer { padding:.6rem .8rem; border-top:1px solid var(--border); display:flex; align-items:center; justify-content:space-between; gap:.4rem; flex-wrap:wrap; }
.media-title { font-size:.72rem; font-weight:700; color:var(--text-primary); }
.media-hint  { font-size:.65rem; color:var(--text-muted); }
.media-actions { display:flex; gap:.35rem; }
.media-btn {
    display:inline-flex; align-items:center; gap:.3rem;
    padding:.22rem .6rem; border-radius:6px; font-size:.7rem; font-weight:600;
    cursor:pointer; text-decoration:none; font-family:'Sora',sans-serif;
    transition:all .15s; border:1.5px solid var(--border);
    background:#fff; color:var(--text-secondary);
}
.media-btn:hover { border-color:var(--accent); color:var(--accent); background:rgba(59,127,232,.05); }
.media-btn.danger:hover { border-color:var(--danger); color:var(--danger); background:rgba(232,70,70,.05); }
input[type="file"].media-file { display:none; }

/* ── MANAGEMENT CARD ─────────────────────────────────────── */
.info-banner {
    display:flex; align-items:flex-start; gap:.75rem;
    background:rgba(59,127,232,.06); border:1px solid rgba(59,127,232,.15);
    border-radius:var(--radius-sm); padding:.85rem 1rem;
    margin-bottom:1.25rem; font-size:.82rem; color:var(--text-secondary);
}
.info-banner i { color:var(--accent); margin-top:.1rem; flex-shrink:0; }

/* ── PERSON CARD ─────────────────────────────────────────── */
.person-card {
    background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-sm);
    padding:1rem 1.1rem; margin-bottom:1rem;
}
.person-card-header { display:flex; align-items:center; gap:.6rem; margin-bottom:.85rem; }
.person-card-icon { width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:.8rem; flex-shrink:0; }
.person-card-icon.blue  { background:rgba(59,127,232,.1);  color:var(--accent); }
.person-card-icon.green { background:rgba(22,168,125,.1);  color:var(--success); }
.person-card-title { font-size:.82rem; font-weight:700; color:var(--text-primary); }
.person-card-sub   { font-size:.7rem; color:var(--text-muted); }

/* ── SIGNATURE PREVIEW ───────────────────────────────────── */
.sig-preview {
    background:linear-gradient(135deg,var(--surface) 0%,#eef1f8 100%);
    border:1px solid var(--border); border-radius:var(--radius-sm);
    padding:1rem 1.25rem;
}
.sig-line { border-top:1.5px solid var(--text-primary); width:180px; margin:1rem 0 .3rem; }
.sig-name { font-weight:700; font-size:.85rem; color:var(--text-primary); }
.sig-role { font-size:.72rem; color:var(--text-muted); }

/* ── STATS MINI CARDS ────────────────────────────────────── */
.stat-mini-grid { display:grid; grid-template-columns:repeat(4,1fr); gap:.75rem; }
@media(max-width:700px){ .stat-mini-grid { grid-template-columns:1fr 1fr; } }
.stat-mini { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-sm); padding:.85rem 1rem; text-align:center; }
.stat-mini-val { font-size:1.5rem; font-weight:700; color:var(--primary); font-family:'JetBrains Mono',monospace; line-height:1; }
.stat-mini-lbl { font-size:.66rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; margin-top:.25rem; }

/* ── SYSTEM INFO TABLE ───────────────────────────────────── */
.sys-table { width:100%; border-collapse:collapse; font-size:.82rem; }
.sys-table td { padding:.5rem .75rem; border-bottom:1px solid var(--border); vertical-align:middle; }
.sys-table tr:last-child td { border-bottom:none; }
.sys-table td:first-child { color:var(--text-secondary); font-weight:600; font-size:.76rem; width:50%; }
.sys-badge { font-family:'JetBrains Mono',monospace; font-size:.7rem; font-weight:600; padding:.15rem .5rem; border-radius:5px; }
.sys-badge.blue  { background:rgba(59,127,232,.1);  color:var(--accent); }
.sys-badge.green { background:rgba(22,168,125,.1);  color:var(--success); }
.sys-badge.amber { background:rgba(232,160,32,.1);  color:var(--warning); }

/* ── ACTION BTNS ─────────────────────────────────────────── */
.sys-action-btn {
    display:flex; align-items:center; gap:.55rem;
    padding:.55rem 1rem; border-radius:var(--radius-sm);
    font-size:.8rem; font-weight:600; cursor:pointer; text-decoration:none;
    transition:all .18s; border:1.5px solid var(--border);
    background:#fff; color:var(--text-secondary); font-family:'Sora',sans-serif;
    width:100%; margin-bottom:.5rem;
}
.sys-action-btn:hover { border-color:var(--accent); color:var(--accent); background:rgba(59,127,232,.05); }
.sys-action-btn.danger:hover  { border-color:var(--danger);  color:var(--danger);  background:rgba(232,70,70,.04); }
.sys-action-btn.warning:hover { border-color:var(--warning); color:var(--warning); background:rgba(232,160,32,.04); }
.sys-action-btn i { width:20px; text-align:center; }

/* ── SAVE BAR ────────────────────────────────────────────── */
.save-bar { display:flex; align-items:center; justify-content:flex-end; gap:.65rem; padding:1rem 1.25rem; background:var(--surface); border-top:1px solid var(--border); margin-top:.5rem; }
.btn-save { display:inline-flex; align-items:center; gap:.45rem; padding:.55rem 1.4rem; border-radius:var(--radius-sm); background:var(--accent); color:#fff; border:none; font-size:.84rem; font-weight:600; cursor:pointer; font-family:'Sora',sans-serif; transition:all .18s; box-shadow:0 3px 10px rgba(59,127,232,.28); }
.btn-save:hover { background:var(--accent-hover); transform:translateY(-1px); }
.btn-cancel { display:inline-flex; align-items:center; gap:.4rem; padding:.52rem 1rem; border-radius:var(--radius-sm); background:#fff; color:var(--text-secondary); border:1.5px solid var(--border); font-size:.82rem; font-weight:600; cursor:pointer; font-family:'Sora',sans-serif; text-decoration:none; transition:all .18s; }
.btn-cancel:hover { border-color:var(--text-secondary); color:var(--text-primary); }

/* ── FORM CHECK (custom) ─────────────────────────────────── */
.f-check { display:flex; align-items:center; gap:.55rem; padding:.5rem 0; cursor:pointer; }
.f-check input[type="checkbox"] { display:none; }
.f-check-box {
    width:18px; height:18px; border-radius:5px; border:1.5px solid var(--border);
    background:#fff; flex-shrink:0; display:flex; align-items:center; justify-content:center;
    transition:all .15s;
}
.f-check input:checked + .f-check-box { background:var(--accent); border-color:var(--accent); }
.f-check-box i { font-size:.6rem; color:#fff; display:none; }
.f-check input:checked + .f-check-box i { display:block; }
.f-check-text { font-size:.8rem; color:var(--text-secondary); }
.f-check-text strong { color:var(--text-primary); }

/* Alerts */
.alert { border-radius:var(--radius-sm); border:none; font-size:.875rem; }
.alert-success { background:rgba(22,168,125,.1); color:#0E7A5A; border-left:3px solid var(--success); }
.alert-danger   { background:rgba(232,70,70,.08);  color:#B03030; border-left:3px solid var(--danger); }
.alert-warning  { background:rgba(232,160,32,.1);  color:#8A5A00; border-left:3px solid var(--warning); }

/* Tab pane */
.tab-pane { display:none; }
.tab-pane.active { display:block; }

@media(max-width:767px){
    .ci-page-header { padding:1.1rem 1.2rem; }
    .ci-page-header h1 { font-size:1.1rem; }
    .media-upload-grid { grid-template-columns:1fr; }
    .stat-mini-grid { grid-template-columns:1fr 1fr; }
}

/* Email settings specific */
.smtp-settings { transition: all 0.3s ease; }
.test-result { font-size: .8rem; padding: .5rem; border-radius: var(--radius-sm); }
.test-result.success { background: rgba(22,168,125,.1); color: #0E7A5A; }
.test-result.error { background: rgba(232,70,70,.08); color: #B03030; }
</style>

<!-- ── PAGE HEADER ─────────────────────────────────────── -->
<div class="ci-page-header mb-4">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-school me-2" style="opacity:.7;font-size:1.1rem;"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/settings') ?>">Configurações</a></li>
                    <li class="breadcrumb-item active">Escola</li>
                </ol>
            </nav>
        </div>
        <a href="<?= site_url('admin/settings') ?>" class="hdr-btn ghost">
            <i class="fas fa-arrow-left"></i> Voltar
        </a>
    </div>
</div>

<?= view('admin/partials/alerts') ?>

<!-- ── SETTINGS LAYOUT ────────────────────────────────── -->
<div class="settings-layout">

    <!-- ── VERTICAL NAV ────────────────────────────────── -->
    <aside class="settings-nav">
        <div class="settings-nav-header">
            <span class="settings-nav-label">Secções</span>
        </div>
        <ul>
            <li>
                <button class="settings-nav-item active" data-tab="general">
                    <span class="nav-ico"><i class="fas fa-building"></i></span>
                    Informações Gerais
                </button>
            </li>
            <li>
                <button class="settings-nav-item" data-tab="branding">
                    <span class="nav-ico"><i class="fas fa-paint-brush"></i></span>
                    Identidade Visual
                </button>
            </li>
            <li>
                <button class="settings-nav-item" data-tab="management">
                    <span class="nav-ico"><i class="fas fa-users-cog"></i></span>
                    Gestão / Direção
                </button>
            </li>
            <div class="settings-nav-divider"></div>
            <li>
                <button class="settings-nav-item" data-tab="academic">
                    <span class="nav-ico"><i class="fas fa-graduation-cap"></i></span>
                    Configurações Académicas
                </button>
            </li>
            <li>
                <button class="settings-nav-item" data-tab="payment">
                    <span class="nav-ico"><i class="fas fa-coins"></i></span>
                    Propinas e Taxas
                </button>
            </li>
            <li>
                <button class="settings-nav-item" data-tab="email">
                    <span class="nav-ico"><i class="fas fa-envelope"></i></span>
                    Configurações de Email
                </button>
            </li>
            <div class="settings-nav-divider"></div>
            <li>
                <button class="settings-nav-item" data-tab="system">
                    <span class="nav-ico"><i class="fas fa-cog"></i></span>
                    Sistema
                </button>
            </li>
        </ul>
    </aside>

    <!-- ── TAB CONTENT ─────────────────────────────────── -->
    <div class="settings-content">

        <!-- ══════════ TAB: GENERAL ══════════ -->
        <div class="tab-pane active" id="tab-general">
            <form action="<?= site_url('admin/settings/save-school') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <div class="ci-card">
                    <div class="ci-card-header">
                        <div class="ci-card-title"><i class="fas fa-building"></i> Informações da Escola</div>
                    </div>
                    <div class="ci-card-body">

                        <!-- Nome + Sigla -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-8">
                                <label class="f-label"><i class="fas fa-school"></i>Nome da Escola<span class="req">*</span></label>
                                <input type="text" class="f-input <?= session('errors.school_name') ? 'is-invalid' : '' ?>"
                                       name="school_name"
                                       value="<?= old('school_name', $settings['school_name'] ?? '') ?>"
                                       placeholder="Ex: Escola do Ensino Primário 1º de Maio" required>
                                <?php if (session('errors.school_name')): ?>
                                    <div class="invalid-msg"><?= session('errors.school_name') ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-4">
                                <label class="f-label"><i class="fas fa-tag"></i>Sigla</label>
                                <input type="text" class="f-input" name="school_acronym"
                                       value="<?= old('school_acronym', $settings['school_acronym'] ?? '') ?>"
                                       placeholder="Ex: EEP1M">
                                <p class="f-hint">Usada em documentos oficiais</p>
                            </div>
                        </div>

                        <!-- Endereço -->
                        <div class="mb-3">
                            <label class="f-label"><i class="fas fa-map-marker-alt"></i>Endereço Completo</label>
                            <input type="text" class="f-input" name="school_address"
                                   value="<?= old('school_address', $settings['school_address'] ?? '') ?>"
                                   placeholder="Rua, Bairro, Nº">
                        </div>

                        <!-- Cidade / Provincia / Fundação -->
                        <div class="row g-3 mb-3">
                            <div class="col-md-4">
                                <label class="f-label"><i class="fas fa-city"></i>Cidade / Município</label>
                                <input type="text" class="f-input" name="school_city"
                                       value="<?= old('school_city', $settings['school_city'] ?? '') ?>"
                                       placeholder="Ex: Luanda">
                            </div>
                            <div class="col-md-4">
                                <label class="f-label"><i class="fas fa-map"></i>Província</label>
                                <input type="text" class="f-input" name="school_province"
                                       value="<?= old('school_province', $settings['school_province'] ?? '') ?>"
                                       placeholder="Ex: Luanda">
                            </div>
                            <div class="col-md-4">
                                <label class="f-label"><i class="fas fa-calendar-alt"></i>Ano de Fundação</label>
                                <input type="number" class="f-input" name="school_founding_year"
                                       value="<?= old('school_founding_year', $settings['school_founding_year'] ?? date('Y')) ?>"
                                       min="1900" max="<?= date('Y') ?>">
                            </div>
                        </div>

                        <div class="sec-divider">
                            <span class="sec-divider-label"><i class="fas fa-phone-alt"></i>Contactos</span>
                            <div class="sec-divider-line"></div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="f-label"><i class="fas fa-phone"></i>Telefone Principal<span class="req">*</span></label>
                                <input type="text" class="f-input <?= session('errors.school_phone') ? 'is-invalid' : '' ?>"
                                       name="school_phone"
                                       value="<?= old('school_phone', $settings['school_phone'] ?? '') ?>"
                                       placeholder="+244 999 999 999" required>
                                <?php if (session('errors.school_phone')): ?>
                                    <div class="invalid-msg"><?= session('errors.school_phone') ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label class="f-label"><i class="fas fa-phone-alt"></i>Telefone Alternativo</label>
                                <input type="text" class="f-input" name="school_alt_phone"
                                       value="<?= old('school_alt_phone', $settings['school_alt_phone'] ?? '') ?>"
                                       placeholder="+244 999 999 998">
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="f-label"><i class="fas fa-envelope"></i>Email Institucional<span class="req">*</span></label>
                                <input type="email" class="f-input <?= session('errors.school_email') ? 'is-invalid' : '' ?>"
                                       name="school_email"
                                       value="<?= old('school_email', $settings['school_email'] ?? '') ?>"
                                       placeholder="info@escola.ao" required>
                                <?php if (session('errors.school_email')): ?>
                                    <div class="invalid-msg"><?= session('errors.school_email') ?></div>
                                <?php endif; ?>
                            </div>
                            <div class="col-md-6">
                                <label class="f-label"><i class="fas fa-globe"></i>Website</label>
                                <input type="url" class="f-input" name="school_website"
                                       value="<?= old('school_website', $settings['school_website'] ?? '') ?>"
                                       placeholder="https://www.escola.ao">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="f-label"><i class="fas fa-id-card"></i>NIF</label>
                            <input type="text" class="f-input" name="school_nif"
                                   value="<?= old('school_nif', $settings['school_nif'] ?? '') ?>"
                                   placeholder="5000000000">
                            <p class="f-hint">Número de Identificação Fiscal</p>
                        </div>

                    </div>
                    <div class="save-bar">
                        <a href="<?= site_url('admin/settings') ?>" class="btn-cancel"><i class="fas fa-times"></i> Cancelar</a>
                        <button type="submit" class="btn-save"><i class="fas fa-save"></i> Guardar Alterações</button>
                    </div>
                </div>
            </form>
        </div><!-- /tab-general -->

        <!-- ══════════ TAB: BRANDING ══════════ -->
        <div class="tab-pane" id="tab-branding">
            <form action="<?= site_url('admin/settings/save-branding') ?>" method="post" enctype="multipart/form-data">
                <?= csrf_field() ?>

                <div class="ci-card">
                    <div class="ci-card-header">
                        <div class="ci-card-title"><i class="fas fa-paint-brush"></i> Identidade Visual</div>
                    </div>
                    <div class="ci-card-body">

                        <div class="info-banner">
                            <i class="fas fa-info-circle"></i>
                            <span>Os logotipos são usados no sidebar, documentos e e-mails do sistema. O <strong>Logo Dark</strong> aplica-se em fundos escuros (sidebar, cabeçalhos navy). O <strong>Favicon</strong> aparece no separador do browser.</span>
                        </div>

                        <div class="media-upload-grid">

                            <!-- Logo Principal -->
                            <div class="media-box <?= !empty($settings['school_logo']) ? 'has-image' : '' ?>" id="box-logo">
                                <div class="media-preview" id="prev-logo" onclick="document.getElementById('file-logo').click()">
                                    <?php if (!empty($settings['school_logo'])): ?>
                                        <img src="<?= base_url('uploads/school/' . $settings['school_logo']) ?>" id="img-logo" alt="Logo">
                                    <?php else: ?>
                                        <div class="ph"><i class="fas fa-image"></i><span>Logo Principal</span></div>
                                    <?php endif; ?>
                                </div>
                                <div class="media-footer">
                                    <div>
                                        <div class="media-title">Logo Principal</div>
                                        <div class="media-hint">PNG / SVG • 512×512px recomendado</div>
                                    </div>
                                    <div class="media-actions">
                                        <button type="button" class="media-btn" onclick="document.getElementById('file-logo').click()">
                                            <i class="fas fa-upload"></i> Upload
                                        </button>
                                        <?php if (!empty($settings['school_logo'])): ?>
                                        <a href="<?= site_url('admin/settings/remove-logo/logo') ?>"
                                           class="media-btn danger"
                                           onclick="return confirm('Remover logo principal?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <input type="file" class="media-file" id="file-logo" name="school_logo" accept="image/*"
                                       onchange="previewMedia(this,'prev-logo','img-logo','box-logo')">
                            </div>

                            <!-- Logo Dark -->
                            <div class="media-box <?= !empty($settings['school_logo_dark']) ? 'has-image' : '' ?>" id="box-logo-dark">
                                <div class="media-preview dark-bg" id="prev-logo-dark" onclick="document.getElementById('file-logo-dark').click()">
                                    <?php if (!empty($settings['school_logo_dark'])): ?>
                                        <img src="<?= base_url('uploads/school/' . $settings['school_logo_dark']) ?>" id="img-logo-dark" alt="Logo Dark">
                                    <?php else: ?>
                                        <div class="ph"><i class="fas fa-moon"></i><span>Logo versão dark</span></div>
                                    <?php endif; ?>
                                </div>
                                <div class="media-footer">
                                    <div>
                                        <div class="media-title">Logo Dark</div>
                                        <div class="media-hint">Para fundos escuros (sidebar)</div>
                                    </div>
                                    <div class="media-actions">
                                        <button type="button" class="media-btn" onclick="document.getElementById('file-logo-dark').click()">
                                            <i class="fas fa-upload"></i> Upload
                                        </button>
                                        <?php if (!empty($settings['school_logo_dark'])): ?>
                                        <a href="<?= site_url('admin/settings/remove-logo/dark') ?>"
                                           class="media-btn danger"
                                           onclick="return confirm('Remover logo dark?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <input type="file" class="media-file" id="file-logo-dark" name="school_logo_dark" accept="image/*"
                                       onchange="previewMedia(this,'prev-logo-dark','img-logo-dark','box-logo-dark')">
                            </div>

                            <!-- Favicon -->
                            <div class="media-box <?= !empty($settings['school_favicon']) ? 'has-image' : '' ?>" id="box-favicon">
                                <div class="media-preview favicon-bg" id="prev-favicon" onclick="document.getElementById('file-favicon').click()">
                                    <?php if (!empty($settings['school_favicon'])): ?>
                                        <img src="<?= base_url('uploads/school/' . $settings['school_favicon']) ?>" id="img-favicon" alt="Favicon" style="max-width:64px;max-height:64px;">
                                    <?php else: ?>
                                        <div class="ph"><i class="fas fa-bookmark"></i><span>Favicon</span></div>
                                    <?php endif; ?>
                                </div>
                                <div class="media-footer">
                                    <div>
                                        <div class="media-title">Favicon</div>
                                        <div class="media-hint">ICO / PNG • 32×32 ou 64×64px</div>
                                    </div>
                                    <div class="media-actions">
                                        <button type="button" class="media-btn" onclick="document.getElementById('file-favicon').click()">
                                            <i class="fas fa-upload"></i> Upload
                                        </button>
                                        <?php if (!empty($settings['school_favicon'])): ?>
                                        <a href="<?= site_url('admin/settings/remove-logo/favicon') ?>"
                                           class="media-btn danger"
                                           onclick="return confirm('Remover favicon?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <input type="file" class="media-file" id="file-favicon" name="school_favicon" accept="image/*,.ico"
                                       onchange="previewMedia(this,'prev-favicon','img-favicon','box-favicon')">
                            </div>

                        </div><!-- /media-upload-grid -->

                    </div>
                    <div class="save-bar">
                        <a href="<?= site_url('admin/settings') ?>" class="btn-cancel"><i class="fas fa-times"></i> Cancelar</a>
                        <button type="submit" class="btn-save"><i class="fas fa-save"></i> Guardar Identidade Visual</button>
                    </div>
                </div>
            </form>
        </div><!-- /tab-branding -->

        <!-- ══════════ TAB: MANAGEMENT ══════════ -->
        <div class="tab-pane" id="tab-management">
            <form action="<?= site_url('admin/settings/save-management') ?>" method="post">
                <?= csrf_field() ?>

                <div class="ci-card">
                    <div class="ci-card-header">
                        <div class="ci-card-title"><i class="fas fa-users-cog"></i> Gestão / Direção</div>
                    </div>
                    <div class="ci-card-body">

                        <div class="info-banner">
                            <i class="fas fa-info-circle"></i>
                            <span>Informações dos responsáveis pela escola. Usadas em assinaturas de documentos oficiais, boletins e certificados.</span>
                        </div>

                        <div class="row g-3 mb-3">
                            <!-- Diretor -->
                            <div class="col-md-6">
                                <div class="person-card">
                                    <div class="person-card-header">
                                        <div class="person-card-icon blue"><i class="fas fa-user-tie"></i></div>
                                        <div>
                                            <div class="person-card-title">Diretor(a) da Escola</div>
                                            <div class="person-card-sub">Responsável máximo da instituição</div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="f-label"><i class="fas fa-user"></i>Nome Completo</label>
                                        <input type="text" class="f-input" name="director_name"
                                               value="<?= old('director_name', $settings['director_name'] ?? '') ?>"
                                               placeholder="Dr. António Manuel Santos">
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <label class="f-label">Título</label>
                                            <select class="f-select" name="director_title">
                                                <?php foreach(['Dr.','Dra.','Prof. Dr.','Prof. Dra.','Professor','Professora'] as $t): ?>
                                                <option value="<?= $t ?>" <?= (old('director_title', $settings['director_title'] ?? '') == $t) ? 'selected' : '' ?>><?= $t ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-6">
                                            <label class="f-label">Grau Académico</label>
                                            <select class="f-select" name="director_degree">
                                                <?php foreach(['Licenciado','Mestre','Doutor','Pós-Graduado'] as $d): ?>
                                                <option value="<?= $d ?>" <?= (old('director_degree', $settings['director_degree'] ?? '') == $d) ? 'selected' : '' ?>><?= $d ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Subdiretor Pedagógico -->
                            <div class="col-md-6">
                                <div class="person-card">
                                    <div class="person-card-header">
                                        <div class="person-card-icon green"><i class="fas fa-chalkboard-teacher"></i></div>
                                        <div>
                                            <div class="person-card-title">Subdiretor(a) Pedagógico(a)</div>
                                            <div class="person-card-sub">Responsável pela área pedagógica</div>
                                        </div>
                                    </div>
                                    <div class="mb-3">
                                        <label class="f-label"><i class="fas fa-user"></i>Nome Completo</label>
                                        <input type="text" class="f-input" name="pedagogical_director_name"
                                               value="<?= old('pedagogical_director_name', $settings['pedagogical_director_name'] ?? '') ?>"
                                               placeholder="Dra. Maria Fernanda Costa">
                                    </div>
                                    <div class="row g-2">
                                        <div class="col-6">
                                            <label class="f-label">Título</label>
                                            <select class="f-select" name="pedagogical_title">
                                                <?php foreach(['Dr.','Dra.','Prof. Dr.','Prof. Dra.','Professor','Professora'] as $t): ?>
                                                <option value="<?= $t ?>" <?= (old('pedagogical_title', $settings['pedagogical_title'] ?? '') == $t) ? 'selected' : '' ?>><?= $t ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                        <div class="col-6">
                                            <label class="f-label">Grau Académico</label>
                                            <select class="f-select" name="pedagogical_degree">
                                                <?php foreach(['Licenciado','Mestre','Doutor','Pós-Graduado'] as $d): ?>
                                                <option value="<?= $d ?>" <?= (old('pedagogical_degree', $settings['pedagogical_degree'] ?? '') == $d) ? 'selected' : '' ?>><?= $d ?></option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="sec-divider">
                            <span class="sec-divider-label"><i class="fas fa-file-alt"></i>Documentos Oficiais</span>
                            <div class="sec-divider-line"></div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label class="f-label"><i class="fas fa-city"></i>Cidade para Documentos</label>
                                <input type="text" class="f-input" name="document_city"
                                       value="<?= old('document_city', $settings['document_city'] ?? $settings['school_city'] ?? '') ?>"
                                       placeholder="Ex: Luanda">
                                <p class="f-hint">Aparece no cabeçalho de documentos</p>
                            </div>
                            <div class="col-md-6">
                                <label class="f-label"><i class="fas fa-signature"></i>Título para Assinaturas</label>
                                <input type="text" class="f-input" name="signature_title"
                                       value="<?= old('signature_title', $settings['signature_title'] ?? 'O Director') ?>">
                                <p class="f-hint">Ex: O Director, A Directora</p>
                            </div>
                        </div>

                        <!-- Signature preview -->
                        <div class="sec-divider">
                            <span class="sec-divider-label"><i class="fas fa-eye"></i>Pré-visualização</span>
                            <div class="sec-divider-line"></div>
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="sig-preview">
                                    <div class="sig-line"></div>
                                    <div class="sig-name" id="sig-director-name">
                                        <?= old('director_title', $settings['director_title'] ?? 'Dr.') ?>
                                        <?= old('director_name', $settings['director_name'] ?? '[Nome do Director]') ?>
                                    </div>
                                    <div class="sig-role" id="sig-director-role">
                                        <?= old('signature_title', $settings['signature_title'] ?? 'O Director') ?>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="sig-preview">
                                    <div class="sig-line"></div>
                                    <div class="sig-name" id="sig-peda-name">
                                        <?= old('pedagogical_title', $settings['pedagogical_title'] ?? 'Dra.') ?>
                                        <?= old('pedagogical_director_name', $settings['pedagogical_director_name'] ?? '[Nome do Subdirector]') ?>
                                    </div>
                                    <div class="sig-role">O Subdirector Pedagógico</div>
                                </div>
                            </div>
                        </div>

                    </div>
                    <div class="save-bar">
                        <a href="<?= site_url('admin/settings') ?>" class="btn-cancel"><i class="fas fa-times"></i> Cancelar</a>
                        <button type="submit" class="btn-save"><i class="fas fa-save"></i> Guardar Gestão</button>
                    </div>
                </div>
            </form>
        </div><!-- /tab-management -->

        <!-- ══════════ TAB: ACADEMIC ══════════ -->
        <div class="tab-pane" id="tab-academic">
            <form action="<?= site_url('admin/settings/save-academic') ?>" method="post">
                <?= csrf_field() ?>

                <div class="ci-card">
                    <div class="ci-card-header">
                        <div class="ci-card-title"><i class="fas fa-graduation-cap"></i> Configurações Académicas</div>
                    </div>
                    <div class="ci-card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="f-label"><i class="fas fa-calendar-alt"></i>Ano Letivo Atual<span class="req">*</span></label>
                                    <select class="f-select <?= session('errors.academic_year_id') ? 'is-invalid' : '' ?>" name="academic_year_id" required>
                                        <option value="">Selecione...</option>
                                        <?php foreach ($academicYears ?? [] as $y): ?>
                                        <option value="<?= $y['id'] ?>" <?= (old('academic_year_id') == $settings['current_academic_year'] ?? '') == $y['id'] ? 'selected' : '' ?>>
                                            <?= esc($y['year_name']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (session('errors.academic_year_id')): ?>
                                        <div class="invalid-msg"><?= session('errors.academic_year_id') ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="mb-3">
                                    <label class="f-label"><i class="fas fa-calendar-week"></i>Semestre Atual<span class="req">*</span></label>
                                    <select class="f-select" name="semester_id" required>
                                        <option value="">Selecione...</option>
                                        <?php foreach ($semesters ?? [] as $s): ?>
                                        <option value="<?= $s['id'] ?>" <?= (old('semester_id', $settings['current_semester'] ?? '') == $s['id']) ? 'selected' : '' ?>>
                                            <?= esc($s['semester_name']) ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="f-label"><i class="fas fa-chart-line"></i>Sistema de Avaliação<span class="req">*</span></label>
                                    <select class="f-select" name="grading_system" required>
                                        <option value="0-20"  <?= (old('grading_system', $settings['grading_system'] ?? '0-20') == '0-20') ? 'selected' : '' ?>>0 a 20 valores</option>
                                        <option value="0-100" <?= (old('grading_system', $settings['grading_system'] ?? '') == '0-100') ? 'selected' : '' ?>>0 a 100%</option>
                                        <option value="A-F"   <?= (old('grading_system', $settings['grading_system'] ?? '') == 'A-F') ? 'selected' : '' ?>>A a F (Letras)</option>
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label class="f-label"><i class="fas fa-calendar-check"></i>Tipo de Calendário</label>
                                    <select class="f-select" name="academic_calendar">
                                        <option value="semester"  <?= (old('academic_calendar', $settings['academic_calendar'] ?? 'semester') == 'semester') ? 'selected' : '' ?>>Semestral</option>
                                        <option value="trimester" <?= (old('academic_calendar', $settings['academic_calendar'] ?? '') == 'trimester') ? 'selected' : '' ?>>Trimestral</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row g-2 mb-3">
                                    <div class="col-4">
                                        <label class="f-label">Nota Mínima</label>
                                        <div class="f-input-group">
                                            <input type="number" class="f-input" name="min_grade" step="0.1"
                                                   value="<?= old('min_grade', $settings['min_grade'] ?? 0) ?>">
                                            <span class="f-addon">val</span>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <label class="f-label">Nota Máxima</label>
                                        <div class="f-input-group">
                                            <input type="number" class="f-input" name="max_grade" step="0.1"
                                                   value="<?= old('max_grade', $settings['max_grade'] ?? 20) ?>">
                                            <span class="f-addon">val</span>
                                        </div>
                                    </div>
                                    <div class="col-4">
                                        <label class="f-label">Aprovação</label>
                                        <div class="f-input-group">
                                            <input type="number" class="f-input" name="approval_grade" step="0.1"
                                                   value="<?= old('approval_grade', $settings['approval_grade'] ?? 10) ?>">
                                            <span class="f-addon">val</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="f-label"><i class="fas fa-percent"></i>Presenças Mínimas</label>
                                    <div class="f-input-group">
                                        <input type="number" class="f-input" name="attendance_threshold" min="0" max="100"
                                               value="<?= old('attendance_threshold', $settings['attendance_threshold'] ?? 75) ?>">
                                        <span class="f-addon">%</span>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="f-label"><i class="fas fa-calendar"></i>Prazo de Matrícula</label>
                                    <input type="date" class="f-input" name="enrollment_deadline"
                                           value="<?= old('enrollment_deadline', $settings['enrollment_deadline'] ?? '') ?>">
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="save-bar">
                        <a href="<?= site_url('admin/settings') ?>" class="btn-cancel"><i class="fas fa-times"></i> Cancelar</a>
                        <button type="submit" class="btn-save"><i class="fas fa-save"></i> Guardar Configurações Académicas</button>
                    </div>
                </div>
            </form>
        </div><!-- /tab-academic -->

        <!-- ══════════ TAB: PAYMENT ══════════ -->
        <div class="tab-pane" id="tab-payment">
            <form action="<?= site_url('admin/settings/save-payment') ?>" method="post">
                <?= csrf_field() ?>

                <div class="ci-card">
                    <div class="ci-card-header">
                        <div class="ci-card-title"><i class="fas fa-coins"></i> Propinas e Taxas</div>
                    </div>
                    <div class="ci-card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="f-label"><i class="fas fa-money-bill-wave"></i>Moeda Padrão<span class="req">*</span></label>
                                    <select class="f-select <?= session('errors.default_currency') ? 'is-invalid' : '' ?>" name="default_currency" required>
                                        <option value="">Selecione...</option>
                                        <?php foreach ($currencies ?? [] as $c): ?>
                                        <option value="<?= $c['id'] ?>" <?= (old('default_currency', $settings['default_currency'] ?? '') == $c['id']) ? 'selected' : '' ?>>
                                            <?= esc($c['currency_name']) ?> (<?= esc($c['currency_code']) ?>)
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <?php if (session('errors.default_currency')): ?>
                                        <div class="invalid-msg"><?= session('errors.default_currency') ?></div>
                                    <?php endif; ?>
                                </div>
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="f-label">Multa por Atraso</label>
                                        <div class="f-input-group">
                                            <input type="number" class="f-input" name="late_fee_percentage" step="0.1" min="0" max="100"
                                                   value="<?= old('late_fee_percentage', $settings['late_fee_percentage'] ?? 0) ?>">
                                            <span class="f-addon">%</span>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <label class="f-label">Desconto Antecipado</label>
                                        <div class="f-input-group">
                                            <input type="number" class="f-input" name="discount_early_payment" step="0.1" min="0" max="100"
                                                   value="<?= old('discount_early_payment', $settings['discount_early_payment'] ?? 0) ?>">
                                            <span class="f-addon">%</span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="f-label"><i class="fas fa-clock"></i>Dias para Vencimento</label>
                                    <input type="number" class="f-input" name="payment_deadline_days" min="1"
                                           value="<?= old('payment_deadline_days', $settings['payment_deadline_days'] ?? 30) ?>">
                                    <p class="f-hint">Dias após emissão da fatura</p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="row g-2 mb-3">
                                    <div class="col-6">
                                        <label class="f-label">Prefixo Fatura</label>
                                        <input type="text" class="f-input" name="payment_invoice_prefix"
                                               value="<?= old('payment_invoice_prefix', $settings['payment_invoice_prefix'] ?? 'INV') ?>">
                                    </div>
                                    <div class="col-6">
                                        <label class="f-label">Prefixo Recibo</label>
                                        <input type="text" class="f-input" name="payment_receipt_prefix"
                                               value="<?= old('payment_receipt_prefix', $settings['payment_receipt_prefix'] ?? 'REC') ?>">
                                    </div>
                                </div>

                                <div class="sec-divider" style="margin-top:0">
                                    <span class="sec-divider-label"><i class="fas fa-toggle-on"></i>Opções</span>
                                    <div class="sec-divider-line"></div>
                                </div>

                                <label class="f-check">
                                    <input type="checkbox" name="payment_enable_multiple" value="1"
                                           <?= (old('payment_enable_multiple', $settings['payment_enable_multiple'] ?? 0) == 1) ? 'checked' : '' ?>>
                                    <span class="f-check-box"><i class="fas fa-check"></i></span>
                                    <span class="f-check-text"><strong>Pagamentos múltiplos</strong> por fatura</span>
                                </label>
                                <label class="f-check">
                                    <input type="checkbox" name="payment_enable_partial" value="1"
                                           <?= (old('payment_enable_partial', $settings['payment_enable_partial'] ?? 0) == 1) ? 'checked' : '' ?>>
                                    <span class="f-check-box"><i class="fas fa-check"></i></span>
                                    <span class="f-check-text"><strong>Pagamentos parciais</strong> permitidos</span>
                                </label>
                                <label class="f-check">
                                    <input type="checkbox" name="payment_enable_fines" value="1"
                                           <?= (old('payment_enable_fines', $settings['payment_enable_fines'] ?? 0) == 1) ? 'checked' : '' ?>>
                                    <span class="f-check-box"><i class="fas fa-check"></i></span>
                                    <span class="f-check-text"><strong>Multas automáticas</strong> por atraso</span>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="save-bar">
                        <a href="<?= site_url('admin/settings') ?>" class="btn-cancel"><i class="fas fa-times"></i> Cancelar</a>
                        <button type="submit" class="btn-save"><i class="fas fa-save"></i> Guardar Propinas</button>
                    </div>
                </div>
            </form>
        </div><!-- /tab-payment -->

        <!-- ══════════ TAB: EMAIL ══════════ -->
        <div class="tab-pane" id="tab-email">
            <form action="<?= site_url('admin/settings/save-email') ?>" method="post" id="emailForm">
                <?= csrf_field() ?>

                <div class="ci-card">
                    <div class="ci-card-header">
                        <div class="ci-card-title"><i class="fas fa-envelope"></i> Configurações do Servidor de Email</div>
                    </div>
                    <div class="ci-card-body">
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="f-label">Protocolo <span class="req">*</span></label>
                                    <select class="f-select" name="email_protocol" id="email_protocol" required>
                                        <option value="smtp" <?= ($settings['email_protocol'] ?? 'smtp') == 'smtp' ? 'selected' : '' ?>>SMTP</option>
                                        <option value="sendmail" <?= ($settings['email_protocol'] ?? '') == 'sendmail' ? 'selected' : '' ?>>Sendmail</option>
                                        <option value="mail" <?= ($settings['email_protocol'] ?? '') == 'mail' ? 'selected' : '' ?>>PHP Mail</option>
                                    </select>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="f-label">Criptografia</label>
                                    <select class="f-select" name="email_smtp_crypto" id="email_smtp_crypto">
                                        <option value="">Nenhuma</option>
                                        <option value="ssl" <?= ($settings['email_smtp_crypto'] ?? '') == 'ssl' ? 'selected' : '' ?>>SSL</option>
                                        <option value="tls" <?= ($settings['email_smtp_crypto'] ?? '') == 'tls' ? 'selected' : '' ?>>TLS</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        
                        <div class="smtp-settings" id="smtpSettings">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="f-label">Servidor SMTP</label>
                                        <input type="text" class="f-input" name="email_smtp_host" 
                                               value="<?= $settings['email_smtp_host'] ?? '' ?>" placeholder="smtp.gmail.com">
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="f-label">Porta SMTP</label>
                                        <input type="number" class="f-input" name="email_smtp_port" 
                                               value="<?= $settings['email_smtp_port'] ?? '587' ?>" placeholder="587">
                                    </div>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="f-label">Usuário SMTP</label>
                                        <input type="text" class="f-input" name="email_smtp_user" 
                                               value="<?= $settings['email_smtp_user'] ?? '' ?>" placeholder="seu@email.com">
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="f-label">Senha SMTP</label>
                                        <div class="f-input-group">
                                            <input type="password" class="f-input" id="email_smtp_pass" name="email_smtp_pass" 
                                                   value="<?= $settings['email_smtp_pass'] ?? '' ?>">
                                            <button type="button" class="f-addon" onclick="togglePassword()" style="cursor:pointer;">
                                                <i class="fas fa-eye" id="toggleIcon"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="sendmail-settings" id="sendmailSettings" style="display: none;">
                            <div class="mb-3">
                                <label class="f-label">Caminho do Sendmail</label>
                                <input type="text" class="f-input" name="email_sendmail_path" 
                                       value="<?= $settings['email_sendmail_path'] ?? '/usr/sbin/sendmail' ?>">
                                <p class="f-hint">Caminho completo para o executável do Sendmail</p>
                            </div>
                        </div>
                        
                        <div class="sec-divider">
                            <span class="sec-divider-label"><i class="fas fa-user"></i>Remetente</span>
                            <div class="sec-divider-line"></div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="f-label">Email do Remetente <span class="req">*</span></label>
                                    <input type="email" class="f-input" name="email_from" 
                                           value="<?= $settings['email_from'] ?? '' ?>" required>
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label class="f-label">Nome do Remetente <span class="req">*</span></label>
                                    <input type="text" class="f-input" name="email_from_name" 
                                           value="<?= $settings['email_from_name'] ?? '' ?>" required>
                                </div>
                            </div>
                        </div>
                        
                        <div class="sec-divider">
                            <span class="sec-divider-label"><i class="fas fa-envelope-open-text"></i>Teste</span>
                            <div class="sec-divider-line"></div>
                        </div>
                        
                        <div class="row">
                            <div class="col-md-8">
                                <div class="mb-3">
                                    <label class="f-label">Email para Teste</label>
                                    <div class="f-input-group">
                                        <input type="email" class="f-input" id="test_email" placeholder="teste@exemplo.com">
                                        <button type="button" class="f-addon" onclick="sendTestEmail()" style="cursor:pointer; background:var(--accent); color:#fff;">
                                            <i class="fas fa-paper-plane"></i> Enviar Teste
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div id="testResult" class="info-banner" style="display: none;"></div>
                        
                    </div>
                    <div class="save-bar">
                        <a href="<?= site_url('admin/settings') ?>" class="btn-cancel"><i class="fas fa-times"></i> Cancelar</a>
                        <button type="submit" class="btn-save"><i class="fas fa-save"></i> Guardar Configurações de Email</button>
                    </div>
                </div>
            </form>
        </div><!-- /tab-email -->

        <!-- ══════════ TAB: SYSTEM ══════════ -->
        <div class="tab-pane" id="tab-system">
            <div class="row g-3">
                <div class="col-md-5">
                    <div class="ci-card">
                        <div class="ci-card-header">
                            <div class="ci-card-title"><i class="fas fa-info-circle"></i> Informações do Sistema</div>
                        </div>
                        <div class="ci-card-body" style="padding:0">
                            <table class="sys-table">
                                <tr><td>Versão</td><td><span class="sys-badge blue">v1.0.0</span></td></tr>
                                <tr><td>CodeIgniter</td><td><span class="sys-badge blue"><?= \CodeIgniter\CodeIgniter::CI_VERSION ?></span></td></tr>
                                <tr><td>PHP</td><td><span class="sys-badge blue"><?= PHP_VERSION ?></span></td></tr>
                                <tr><td>Base de Dados</td><td><span class="sys-badge blue">MySQL</span></td></tr>
                                <tr><td>Ambiente</td>
                                    <td><span class="sys-badge <?= ENVIRONMENT == 'production' ? 'green' : 'amber' ?>"><?= ENVIRONMENT ?></span></td>
                                </tr>
                            </table>
                        </div>
                    </div>

                    <div class="ci-card">
                        <div class="ci-card-header">
                            <div class="ci-card-title"><i class="fas fa-chart-pie"></i> Estatísticas</div>
                        </div>
                        <div class="ci-card-body">
                            <div class="stat-mini-grid">
                                <div class="stat-mini">
                                    <div class="stat-mini-val"><?= count($gradeLevels ?? []) ?></div>
                                    <div class="stat-mini-lbl">Níveis</div>
                                </div>
                                <div class="stat-mini">
                                    <div class="stat-mini-val"><?= count($disciplines ?? []) ?></div>
                                    <div class="stat-mini-lbl">Disciplinas</div>
                                </div>
                                <div class="stat-mini">
                                    <div class="stat-mini-val"><?= count($feeTypes ?? []) ?></div>
                                    <div class="stat-mini-lbl">Taxas</div>
                                </div>
                                <div class="stat-mini">
                                    <div class="stat-mini-val"><?= count($currencies ?? []) ?></div>
                                    <div class="stat-mini-lbl">Moedas</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-md-7">
                    <div class="ci-card">
                        <div class="ci-card-header">
                            <div class="ci-card-title"><i class="fas fa-tools"></i> Ações do Sistema</div>
                        </div>
                        <div class="ci-card-body">
                            <a href="<?= site_url('admin/settings/clear-cache') ?>" class="sys-action-btn warning"
                               onclick="return confirm('Limpar todos os caches do sistema?')">
                                <i class="fas fa-broom"></i> Limpar Cache do Sistema
                            </a>
                            <a href="<?= site_url('admin/settings/backup') ?>" class="sys-action-btn danger"
                               onclick="return confirm('Gerar backup da base de dados?')">
                                <i class="fas fa-database"></i> Backup da Base de Dados
                            </a>
                            <button class="sys-action-btn" onclick="return confirm('Restaurar configurações padrão? Esta ação não pode ser revertida.')">
                                <i class="fas fa-undo"></i> Restaurar Configurações Padrão
                            </button>

                            <div class="alert alert-warning mt-3" style="font-size:.8rem;">
                                <i class="fas fa-exclamation-triangle me-1"></i>
                                <strong>Atenção:</strong> As ações acima afetam todo o sistema. Proceda com cautela.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div><!-- /tab-system -->

    </div><!-- /settings-content -->
</div><!-- /settings-layout -->

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
// ── Vertical Tab Navigation ───────────────────────────────
document.querySelectorAll('.settings-nav-item').forEach(btn => {
    btn.addEventListener('click', function() {
        const target = this.dataset.tab;

        // Nav active state
        document.querySelectorAll('.settings-nav-item').forEach(b => b.classList.remove('active'));
        this.classList.add('active');

        // Pane visibility
        document.querySelectorAll('.tab-pane').forEach(p => p.classList.remove('active'));
        document.getElementById('tab-' + target)?.classList.add('active');

        // Update URL hash without scroll
        history.replaceState(null, '', '#' + target);
    });
});

// Restore tab from URL hash
const hash = location.hash.replace('#', '');
if (hash) {
    const btn = document.querySelector(`.settings-nav-item[data-tab="${hash}"]`);
    if (btn) btn.click();
}

// ── Media Upload Preview ──────────────────────────────────
function previewMedia(input, previewId, imgId, boxId) {
    const file = input.files[0];
    if (!file) return;

    if (file.size > 3 * 1024 * 1024) {
        alert('Ficheiro muito grande. Máximo: 3MB');
        input.value = '';
        return;
    }

    const reader = new FileReader();
    reader.onload = function(e) {
        const prev  = document.getElementById(previewId);
        const box   = document.getElementById(boxId);
        let img = document.getElementById(imgId);

        if (!img) {
            prev.innerHTML = `<img id="${imgId}" src="${e.target.result}" alt="Preview">`;
            if (boxId === 'box-favicon') {
                document.getElementById(imgId).style.cssText = 'max-width:64px;max-height:64px;';
            }
        } else {
            img.src = e.target.result;
        }
        box.classList.add('has-image');
    };
    reader.readAsDataURL(file);
}

// ── Signature Preview Live Update ────────────────────────
function updateSigPreview() {
    const dTitle = document.querySelector('[name="director_title"]')?.value || '';
    const dName  = document.querySelector('[name="director_name"]')?.value  || '[Nome do Director]';
    const pTitle = document.querySelector('[name="pedagogical_title"]')?.value || '';
    const pName  = document.querySelector('[name="pedagogical_director_name"]')?.value || '[Nome do Subdirector]';
    const sRole  = document.querySelector('[name="signature_title"]')?.value || 'O Director';

    const dn = document.getElementById('sig-director-name');
    const dr = document.getElementById('sig-director-role');
    const pn = document.getElementById('sig-peda-name');

    if (dn) dn.textContent = dTitle + ' ' + dName;
    if (dr) dr.textContent = sRole;
    if (pn) pn.textContent = pTitle + ' ' + pName;
}

// ── Email Settings ────────────────────────────────────────
function togglePassword() {
    const password = document.getElementById('email_smtp_pass');
    const icon = document.getElementById('toggleIcon');
    
    if (password.type === 'password') {
        password.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        password.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

// Show/hide email settings based on protocol
document.getElementById('email_protocol')?.addEventListener('change', function() {
    const smtpSettings = document.getElementById('smtpSettings');
    const sendmailSettings = document.getElementById('sendmailSettings');
    
    if (this.value === 'smtp') {
        smtpSettings.style.display = 'block';
        sendmailSettings.style.display = 'none';
    } else if (this.value === 'sendmail') {
        smtpSettings.style.display = 'none';
        sendmailSettings.style.display = 'block';
    } else {
        smtpSettings.style.display = 'none';
        sendmailSettings.style.display = 'none';
    }
});

// Send test email
function sendTestEmail() {
    const email = document.getElementById('test_email').value;
    if (!email) {
        showTestResult('Por favor, insira um email para teste', 'error');
        return;
    }
    
    const form = document.getElementById('emailForm');
    const formData = new FormData(form);
    formData.append('test_email', email);
    
    showTestResult('Enviando...', 'info');
    
    fetch('<?= site_url('admin/settings/test-email') ?>', {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showTestResult('Email de teste enviado com sucesso!', 'success');
        } else {
            showTestResult('Erro: ' + data.message, 'error');
        }
    })
    .catch(error => {
        showTestResult('Erro ao enviar email de teste', 'error');
    });
}

function showTestResult(message, type) {
    const resultDiv = document.getElementById('testResult');
    resultDiv.style.display = 'flex';
    resultDiv.innerHTML = '<i class="fas fa-' + 
        (type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle') + 
        '"></i> ' + message;
    
    resultDiv.className = 'info-banner';
    if (type === 'success') {
        resultDiv.style.background = 'rgba(22,168,125,.06)';
        resultDiv.style.borderColor = 'rgba(22,168,125,.15)';
    } else if (type === 'error') {
        resultDiv.style.background = 'rgba(232,70,70,.06)';
        resultDiv.style.borderColor = 'rgba(232,70,70,.15)';
    }
}

// Add event listeners for signature preview
['director_name','director_title','pedagogical_director_name','pedagogical_title','signature_title']
    .forEach(n => document.querySelector(`[name="${n}"]`)?.addEventListener('input', updateSigPreview));
    
['director_title','pedagogical_title']
    .forEach(n => document.querySelector(`[name="${n}"]`)?.addEventListener('change', updateSigPreview));

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    updateSigPreview();
    
    // Initialize email protocol display
    const protocol = document.getElementById('email_protocol');
    if (protocol) {
        const smtpSettings = document.getElementById('smtpSettings');
        const sendmailSettings = document.getElementById('sendmailSettings');
        
        if (protocol.value === 'smtp') {
            smtpSettings.style.display = 'block';
            sendmailSettings.style.display = 'none';
        } else if (protocol.value === 'sendmail') {
            smtpSettings.style.display = 'none';
            sendmailSettings.style.display = 'block';
        } else {
            smtpSettings.style.display = 'none';
            sendmailSettings.style.display = 'none';
        }
    }
});

// ── Unsaved changes warning ───────────────────────────────
let _dirty = false;
document.querySelectorAll('form').forEach(f => {
    f.addEventListener('change', () => _dirty = true);
    f.addEventListener('submit', () => _dirty = false);
});
window.addEventListener('beforeunload', e => {
    if (_dirty) { e.preventDefault(); e.returnValue = ''; }
});
</script>
<?= $this->endSection() ?>