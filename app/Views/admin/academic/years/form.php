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

.ci-page-header { background:linear-gradient(135deg,var(--primary) 0%,var(--primary-light) 60%,#2D4A7A 100%); border-radius:var(--radius); padding:1.5rem 2rem; margin-bottom:1.5rem; position:relative; overflow:hidden; box-shadow:var(--shadow-lg); }
.ci-page-header::before { content:''; position:absolute; top:-60px; right:-60px; width:200px; height:200px; border-radius:50%; background:rgba(255,255,255,.04); pointer-events:none; }
.ci-page-header::after  { content:''; position:absolute; bottom:-40px; right:100px; width:130px; height:130px; border-radius:50%; background:rgba(59,127,232,.15); pointer-events:none; }
.ci-page-header h1 { font-size:1.4rem; font-weight:700; color:#fff; margin:0 0 .2rem; letter-spacing:-.3px; position:relative; z-index:1; }
.ci-page-header .breadcrumb { margin:0; padding:0; background:transparent; position:relative; z-index:1; }
.ci-page-header .breadcrumb-item a { color:rgba(255,255,255,.6); text-decoration:none; font-size:.8rem; transition:color .2s; }
.ci-page-header .breadcrumb-item a:hover { color:#fff; }
.ci-page-header .breadcrumb-item.active,
.ci-page-header .breadcrumb-item + .breadcrumb-item::before { color:rgba(255,255,255,.4); font-size:.8rem; }

.form-card { background:var(--surface-card); border:1px solid var(--border); border-radius:var(--radius); box-shadow:var(--shadow-sm); overflow:hidden; max-width:780px; margin:0 auto; }
.form-card-header { background:var(--primary); padding:1rem 1.5rem; display:flex; align-items:center; gap:.6rem; }
.form-card-header-title { font-size:.9rem; font-weight:700; color:#fff; display:flex; align-items:center; gap:.5rem; }
.form-card-header-title i { opacity:.75; }
.form-card-body { padding:1.75rem; }

.form-section { margin-bottom:1.75rem; }
.form-section-title { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--text-muted); margin-bottom:1rem; padding-bottom:.5rem; border-bottom:1px solid var(--border); display:flex; align-items:center; gap:.4rem; }
.form-section-title i { color:var(--accent); }

.form-label-ci { font-size:.72rem; font-weight:700; text-transform:uppercase; letter-spacing:.07em; color:var(--text-secondary); margin-bottom:.35rem; display:flex; align-items:center; gap:.3rem; }
.form-label-ci .req { color:var(--danger); }
.form-label-ci i { color:var(--accent); font-size:.7rem; }

.form-input-ci { width:100%; border:1.5px solid var(--border); border-radius:var(--radius-sm); padding:.6rem .9rem; font-size:.875rem; font-family:'Sora',sans-serif; color:var(--text-primary); background:var(--surface); outline:none; transition:border-color .2s, box-shadow .2s; }
.form-input-ci:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(59,127,232,.12); background:#fff; }
.form-input-ci.is-invalid { border-color:var(--danger); box-shadow:0 0 0 3px rgba(232,70,70,.1); }
.form-hint  { font-size:.72rem; color:var(--text-muted); margin-top:.3rem; display:flex; align-items:center; gap:.25rem; }
.form-error { font-size:.72rem; color:var(--danger); margin-top:.3rem; display:flex; align-items:center; gap:.3rem; }

/* Duration banner */
.duration-banner { display:none; align-items:center; gap:.75rem; background:rgba(59,127,232,.05); border:1px solid rgba(59,127,232,.15); border-radius:var(--radius-sm); padding:.65rem 1rem; margin-top:.5rem; font-size:.82rem; color:var(--text-secondary); animation:fadeIn .2s ease; }
.duration-banner.show { display:flex; }
@keyframes fadeIn { from{opacity:0;transform:translateY(-4px)} to{opacity:1;transform:translateY(0)} }
.duration-val { font-family:'JetBrains Mono',monospace; font-size:1rem; font-weight:700; color:var(--accent); }

/* Dynamic alert */
.dyn-alert { display:none; border-radius:var(--radius-sm); padding:.7rem 1rem; font-size:.82rem; margin-bottom:1rem; border:none; border-left:3px solid transparent; }
.dyn-alert.show { display:flex; align-items:flex-start; gap:.5rem; }
.dyn-alert.warning { background:rgba(232,160,32,.08); border-color:var(--warning); color:#8A5D00; }
.dyn-alert.danger  { background:rgba(232,70,70,.08);  border-color:var(--danger);  color:#B03030; }
.dyn-alert i { flex-shrink:0; margin-top:.1rem; }

/* Toggle switch */
.toggle-row { display:flex; align-items:center; justify-content:space-between; background:var(--surface); border:1.5px solid var(--border); border-radius:var(--radius-sm); padding:.7rem 1rem; cursor:pointer; transition:border-color .18s; user-select:none; }
.toggle-row:hover { border-color:var(--accent); }
.toggle-row.checked { border-color:rgba(22,168,125,.3); background:rgba(22,168,125,.04); }
.toggle-row.star-checked { border-color:rgba(232,160,32,.35); background:rgba(232,160,32,.05); }
.tl-info { font-size:.875rem; font-weight:600; color:var(--text-primary); }
.tl-sub  { font-size:.72rem; color:var(--text-muted); margin-top:.1rem; }
.ci-switch { position:relative; display:inline-flex; width:42px; height:24px; flex-shrink:0; }
.ci-switch input { opacity:0; width:0; height:0; position:absolute; }
.ci-switch-track { position:absolute; inset:0; border-radius:50px; background:var(--border); transition:background .22s; cursor:pointer; }
.ci-switch-track::after { content:''; position:absolute; top:3px; left:3px; width:18px; height:18px; border-radius:50%; background:#fff; box-shadow:0 1px 3px rgba(0,0,0,.2); transition:transform .22s; }
.ci-switch input:checked + .ci-switch-track { background:var(--success); }
.ci-switch input:checked + .ci-switch-track::after { transform:translateX(18px); }
.ci-switch.star input:checked + .ci-switch-track { background:var(--warning); }

/* Current strip */
.current-strip { display:flex; align-items:center; gap:.5rem; background:rgba(232,160,32,.08); border:1px solid rgba(232,160,32,.2); border-radius:var(--radius-sm); padding:.65rem .9rem; font-size:.82rem; color:#8A5D00; font-weight:600; }

/* Meta card */
.meta-card { background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-sm); padding:.9rem 1.1rem; display:grid; grid-template-columns:repeat(3,1fr); gap:.75rem; }
.meta-item { display:flex; flex-direction:column; gap:.15rem; }
.meta-label { font-size:.65rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--text-muted); }
.meta-value { font-size:.82rem; font-weight:600; color:var(--text-primary); display:flex; align-items:center; gap:.35rem; flex-wrap:wrap; }
.meta-badge { font-size:.65rem; font-weight:700; padding:.18rem .55rem; border-radius:50px; }
.mb-active   { color:var(--success); background:rgba(22,168,125,.1); }
.mb-inactive { color:var(--text-muted); background:rgba(107,122,153,.1); }
.mb-current  { color:var(--accent); background:rgba(59,127,232,.1); }

/* Footer */
.form-footer { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.75rem; padding-top:1.25rem; border-top:1px solid var(--border); margin-top:1.5rem; }
.btn-back { background:transparent; border:1.5px solid var(--border); color:var(--text-secondary); border-radius:var(--radius-sm); padding:.55rem 1.2rem; font-size:.85rem; font-weight:600; display:inline-flex; align-items:center; gap:.45rem; text-decoration:none; transition:all .18s; }
.btn-back:hover { background:var(--surface); color:var(--primary); border-color:var(--primary); }
.btn-save { background:var(--accent); border:none; color:#fff; border-radius:var(--radius-sm); padding:.55rem 1.5rem; font-size:.875rem; font-weight:700; display:inline-flex; align-items:center; gap:.5rem; cursor:pointer; transition:all .18s; font-family:'Sora',sans-serif; box-shadow:0 3px 10px rgba(59,127,232,.3); }
.btn-save:hover { background:var(--accent-hover); transform:translateY(-1px); }

.alert { border-radius:var(--radius-sm); border:none; font-size:.875rem; }
.alert-success { background:rgba(22,168,125,.1); color:#0E7A5A; border-left:3px solid var(--success); }
.alert-danger  { background:rgba(232,70,70,.08); color:#B03030; border-left:3px solid var(--danger); }

@media (max-width:767px) {
    .ci-page-header { padding:1.1rem 1.2rem; }
    .ci-page-header h1 { font-size:1.1rem; }
    .form-card-body { padding:1.25rem; }
    .meta-card { grid-template-columns:1fr 1fr; }
}
@media (max-width:480px) { .meta-card { grid-template-columns:1fr; } }
</style>

<!-- ── PAGE HEADER ─────────────────────────────────────── -->
<div class="ci-page-header mb-4">
    <h1>
        <i class="fas fa-<?= $year ? 'edit' : 'plus-circle' ?> me-2" style="opacity:.7;font-size:1.1rem;"></i>
        <?= $title ?>
    </h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/academic/years') ?>">Anos Letivos</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $year ? 'Editar' : 'Novo' ?></li>
        </ol>
    </nav>
</div>

<?= view('admin/partials/alerts') ?>

<!-- ── FORM CARD ───────────────────────────────────────── -->
<div class="form-card">
    <div class="form-card-header">
        <div class="form-card-header-title">
            <i class="fas fa-<?= $year ? 'edit' : 'plus-circle' ?>"></i>
            <?= $title ?>
        </div>
    </div>

    <div class="form-card-body">
        <form action="<?= site_url('admin/academic/years/save') ?>" method="post" id="yearForm">
            <?= csrf_field() ?>
            <?php if ($year): ?>
                <input type="hidden" name="id" value="<?= $year->id ?>">
            <?php endif; ?>

            <!-- Dynamic alert -->
            <div class="dyn-alert" id="dynAlert"><i class="fas fa-exclamation-triangle"></i><span id="dynAlertMsg"></span></div>

            <!-- ── Section: Identification ─────────────── -->
            <div class="form-section">
                <div class="form-section-title"><i class="fas fa-id-card"></i> Identificação</div>
                <div class="row g-3">

                    <!-- Year name -->
                    <div class="col-md-6">
                        <label class="form-label-ci" for="year_name">
                            <i class="fas fa-tag"></i> Nome do Ano Letivo <span class="req">*</span>
                        </label>
                        <input type="text"
                               class="form-input-ci <?= session('errors.year_name') ? 'is-invalid' : '' ?>"
                               id="year_name" name="year_name"
                               value="<?= old('year_name', $year->year_name ?? '') ?>"
                               placeholder="Ex: 2024 ou 2024/2025"
                               required maxlength="50">
                        <?php if (session('errors.year_name')): ?>
                            <div class="form-error"><i class="fas fa-exclamation-circle"></i><?= session('errors.year_name') ?></div>
                        <?php endif; ?>
                        <div class="form-hint"><i class="fas fa-info-circle"></i> Formatos aceites: 2024, 2024/2025, 2024-2025</div>
                    </div>

                    <!-- Start date -->
                    <div class="col-md-3">
                        <label class="form-label-ci" for="start_date">
                            <i class="fas fa-play-circle"></i> Data de Início <span class="req">*</span>
                        </label>
                        <input type="date"
                               class="form-input-ci <?= session('errors.start_date') ? 'is-invalid' : '' ?>"
                               id="start_date" name="start_date"
                               value="<?= old('start_date', $year->start_date ?? '') ?>"
                               required>
                        <?php if (session('errors.start_date')): ?>
                            <div class="form-error"><i class="fas fa-exclamation-circle"></i><?= session('errors.start_date') ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- End date -->
                    <div class="col-md-3">
                        <label class="form-label-ci" for="end_date">
                            <i class="fas fa-stop-circle"></i> Data de Fim <span class="req">*</span>
                        </label>
                        <input type="date"
                               class="form-input-ci <?= session('errors.end_date') ? 'is-invalid' : '' ?>"
                               id="end_date" name="end_date"
                               value="<?= old('end_date', $year->end_date ?? '') ?>"
                               required>
                        <?php if (session('errors.end_date')): ?>
                            <div class="form-error"><i class="fas fa-exclamation-circle"></i><?= session('errors.end_date') ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Duration banner -->
                    <div class="col-12">
                        <div class="duration-banner" id="durationBanner">
                            <i class="fas fa-clock" style="color:var(--accent);font-size:.9rem;"></i>
                            Duração do ano letivo: <span class="duration-val" id="durationVal"></span> dias
                        </div>
                    </div>
                </div>
            </div>

            <!-- ── Section: Configuration ──────────────── -->
            <div class="form-section">
                <div class="form-section-title"><i class="fas fa-sliders-h"></i> Configurações</div>
                <div class="row g-3">
                    <!-- Active toggle -->
                    <div class="col-md-6">
                        <label class="form-label-ci">Estado</label>
                        <label class="toggle-row <?= old('is_active', $year->is_active ?? true) ? 'checked' : '' ?>"
                               id="toggle-active" for="is_active">
                            <div>
                                <div class="tl-info">Ano Letivo Ativo</div>
                                <div class="tl-sub">Desative para arquivar este ano letivo</div>
                            </div>
                            <label class="ci-switch">
                                <input type="checkbox" id="is_active" name="is_active" value="1"
                                       <?= old('is_active', $year->is_active ?? true) ? 'checked' : '' ?>>
                                <span class="ci-switch-track"></span>
                            </label>
                        </label>
                    </div>

                    <!-- Current toggle (add) or indicator (edit) -->
                    <div class="col-md-6">
                        <?php if (!$year): ?>
                            <label class="form-label-ci">Ano Atual</label>
                            <label class="toggle-row <?= old('is_current') ? 'star-checked' : '' ?>"
                                   id="toggle-current" for="is_current">
                                <div>
                                    <div class="tl-info">Definir como Ano Letivo Atual</div>
                                    <div class="tl-sub">Marque se este for o ano letivo corrente</div>
                                </div>
                                <label class="ci-switch star">
                                    <input type="checkbox" id="is_current" name="is_current" value="1"
                                           <?= old('is_current') ? 'checked' : '' ?>>
                                    <span class="ci-switch-track"></span>
                                </label>
                            </label>
                        <?php elseif ($year->is_current): ?>
                            <label class="form-label-ci">Ano Atual</label>
                            <div class="current-strip">
                                <i class="fas fa-star" style="color:var(--warning);"></i>
                                Este é o ano letivo atual
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- ── Section: Meta (edit only) ──────────── -->
            <?php if ($year): ?>
            <div class="form-section">
                <div class="form-section-title"><i class="fas fa-info-circle"></i> Informações do Registo</div>
                <div class="meta-card">
                    <div class="meta-item">
                        <span class="meta-label">Criado em</span>
                        <span class="meta-value"><?= date('d/m/Y H:i', strtotime($year->created_at)) ?></span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Última Atualização</span>
                        <span class="meta-value"><?= $year->updated_at ? date('d/m/Y H:i', strtotime($year->updated_at)) : '—' ?></span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Estado</span>
                        <span class="meta-value">
                            <?php if ($year->is_active): ?>
                                <span class="meta-badge mb-active">Ativo</span>
                            <?php else: ?>
                                <span class="meta-badge mb-inactive">Inativo</span>
                            <?php endif; ?>
                            <?php if ($year->is_current): ?>
                                <span class="meta-badge mb-current"><i class="fas fa-star" style="font-size:.6rem;"></i> Atual</span>
                            <?php endif; ?>
                        </span>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <!-- ── Footer ─────────────────────────────── -->
            <div class="form-footer">
                <a href="<?= site_url('admin/academic/years') ?>" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i> <?= $year ? 'Atualizar' : 'Guardar' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
(function () {
    'use strict';

    var startIn  = document.getElementById('start_date');
    var endIn    = document.getElementById('end_date');
    var durBan   = document.getElementById('durationBanner');
    var durVal   = document.getElementById('durationVal');
    var dynAlert = document.getElementById('dynAlert');
    var dynMsg   = document.getElementById('dynAlertMsg');

    /* ── Toggle sync ───────────────────────────────────── */
    document.getElementById('is_active')?.addEventListener('change', function () {
        var row = document.getElementById('toggle-active');
        if (row) row.classList.toggle('checked', this.checked);
    });
    document.getElementById('is_current')?.addEventListener('change', function () {
        var row = document.getElementById('toggle-current');
        if (row) row.classList.toggle('star-checked', this.checked);
    });

    /* ── Date validation + duration ───────────────────── */
    function validate() {
        hideAlert();
        var s = startIn.value, e = endIn.value;
        if (s && e) {
            if (e < s) {
                showAlert('A data de fim não pode ser anterior à data de início.', 'danger');
                durBan.classList.remove('show'); return false;
            }
            var days = Math.ceil((new Date(e) - new Date(s)) / 86400000) + 1;
            durVal.textContent = days;
            durBan.classList.add('show');
        } else {
            durBan.classList.remove('show');
        }
        return true;
    }

    startIn?.addEventListener('change', validate);
    endIn?.addEventListener('change', validate);

    /* ── Form submit ───────────────────────────────────── */
    document.getElementById('yearForm')?.addEventListener('submit', function (e) {
        hideAlert();
        if (!startIn.value || !endIn.value) {
            e.preventDefault(); showAlert('Preencha todos os campos obrigatórios.', 'danger'); return;
        }
        if (endIn.value < startIn.value) {
            e.preventDefault(); showAlert('A data de fim não pode ser anterior à data de início.', 'danger');
        }
    });

    function showAlert(msg, type) { dynMsg.textContent = msg; dynAlert.className = 'dyn-alert show ' + type; }
    function hideAlert()          { dynAlert.className = 'dyn-alert'; }

    /* ── Init on edit ──────────────────────────────────── */
    <?php if ($year): ?>window.addEventListener('load', validate);<?php endif; ?>
})();
</script>
<?= $this->endSection() ?>