<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>
<!-- Estilos específicos para a página de anos letivos -->
<style>
/* Ajustes responsivos para o form */
@media (max-width: 768px) {
    .form-card {
        max-width: 100%;
        margin: 0 0.5rem;
    }
    
    .meta-card {
        grid-template-columns: 1fr;
        gap: 0.5rem;
    }
    
    .form-footer {
        flex-direction: column-reverse;
        align-items: stretch;
    }
    
    .btn-back, .btn-save {
        justify-content: center;
    }
}

/* Ajuste para campos de data em mobile */
@media (max-width: 480px) {
    .form-card-body {
        padding: 1.25rem;
    }
    
    input[type="date"] {
        font-size: 0.8rem;
    }
}

/* Animação suave para o toggle */
.toggle-row, .ci-switch-track {
    transition: all 0.22s cubic-bezier(0.4, 0, 0.2, 1);
}

/* Melhor contraste para placeholders */
::placeholder {
    color: var(--text-muted);
    opacity: 0.7;
}

/* Focus visible para acessibilidade */
.form-input-ci:focus-visible {
    outline: 2px solid var(--accent);
    outline-offset: 2px;
}
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
                <input type="hidden" name="id" value="<?= $year['id'] ?>">
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
                               value="<?= old('year_name', $year['year_name'] ?? '') ?>"
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
                               value="<?= old('start_date', $year['start_date'] ?? '') ?>"
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
                               value="<?= old('end_date', $year['end_date'] ?? '') ?>"
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
                        <label class="toggle-row <?= old('is_active', $year['is_active'] ?? true) ? 'checked' : '' ?>"
                               id="toggle-active" for="is_active">
                            <div>
                                <div class="tl-info">Ano Letivo Ativo</div>
                                <div class="tl-sub">Desative para arquivar este ano letivo</div>
                            </div>
                            <label class="ci-switch">
                                <input type="checkbox" id="is_active" name="is_active" value="1"
                                       <?= old('is_active', $year['is_active'] ?? true) ? 'checked' : '' ?>>
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
                        <?php elseif ($year['id'] == current_academic_year()): ?>
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
                        <span class="meta-value"><?= date('d/m/Y H:i', strtotime($year['created_at'])) ?></span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Última Atualização</span>
                        <span class="meta-value"><?= $year['updated_at'] ? date('d/m/Y H:i', strtotime($year['updated_at'])) : '—' ?></span>
                    </div>
                    <div class="meta-item">
                        <span class="meta-label">Estado</span>
                        <span class="meta-value">
                            <?php if ($year['is_active']): ?>
                                <span class="meta-badge mb-active">Ativo</span>
                            <?php else: ?>
                                <span class="meta-badge mb-inactive">Inativo</span>
                            <?php endif; ?>
                            <?php if ($year['id'] == current_academic_year()): ?>
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