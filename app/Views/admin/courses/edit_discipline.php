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

/* ── FORM CARD ───────────────────────────────────────── */
.form-card {
    background: var(--surface-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    max-width: 720px;
    margin: 0 auto;
}
.form-card-header {
    background: var(--primary);
    padding: 1rem 1.5rem;
    display: flex; align-items: center; gap: 0.6rem;
}
.form-card-header-title {
    font-size: 0.9rem; font-weight: 700; color: #fff;
    display: flex; align-items: center; gap: 0.5rem;
}
.form-card-header-title i { opacity: 0.75; }
.form-card-body { padding: 1.75rem; }

/* ── CONTEXT BANNER ──────────────────────────────────── */
.context-banner {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 1rem 1.25rem;
    margin-bottom: 1.75rem;
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 0.5rem 1.5rem;
}
.ctx-item { display: flex; flex-direction: column; gap: 0.18rem; }
.ctx-label {
    font-size: 0.68rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.08em;
    color: var(--text-muted);
}
.ctx-value {
    font-size: 0.875rem; font-weight: 600; color: var(--text-primary);
    display: flex; align-items: center; gap: 0.4rem;
}
.ctx-code {
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.72rem; font-weight: 600;
    background: var(--primary); color: #fff;
    padding: 0.15rem 0.5rem; border-radius: 5px;
    letter-spacing: 0.03em;
}

/* ── FORM FIELDS ─────────────────────────────────────── */
.form-section { margin-bottom: 1.5rem; }
.form-section-title {
    font-size: 0.72rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.08em;
    color: var(--text-muted);
    margin-bottom: 1rem;
    padding-bottom: 0.5rem;
    border-bottom: 1px solid var(--border);
    display: flex; align-items: center; gap: 0.4rem;
}

.form-label-ci {
    font-size: 0.72rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.07em;
    color: var(--text-secondary);
    margin-bottom: 0.35rem; display: block;
}
.form-label-ci .req { color: var(--danger); margin-left: 2px; }

.form-input-ci, .form-select-ci {
    width: 100%;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 0.6rem 0.9rem;
    font-size: 0.875rem;
    font-family: 'Sora', sans-serif;
    color: var(--text-primary);
    background: var(--surface);
    transition: border-color .2s, box-shadow .2s;
    outline: none;
}
.form-input-ci:focus, .form-select-ci:focus {
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(59,127,232,0.12);
    background: #fff;
}
.form-input-ci.is-invalid, .form-select-ci.is-invalid {
    border-color: var(--danger);
    box-shadow: 0 0 0 3px rgba(232,70,70,0.1);
}
.form-hint { font-size: 0.72rem; color: var(--text-muted); margin-top: 0.3rem; }
.form-error { font-size: 0.72rem; color: var(--danger); margin-top: 0.3rem; display: flex; align-items: center; gap: 0.3rem; }

/* Input with unit suffix */
.input-group-ci {
    position: relative; display: flex; align-items: center;
}
.input-group-ci .form-input-ci { padding-right: 3rem; }
.input-suffix {
    position: absolute; right: 0.9rem;
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.78rem; font-weight: 600;
    color: var(--text-muted);
    pointer-events: none;
}

/* ── TOGGLE SWITCH ───────────────────────────────────── */
.toggle-row {
    display: flex; align-items: center; justify-content: space-between;
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 0.7rem 1rem;
    cursor: pointer;
    transition: border-color .18s;
    user-select: none;
}
.toggle-row:hover { border-color: var(--accent); }
.toggle-row.checked { border-color: rgba(22,168,125,0.3); background: rgba(22,168,125,0.04); }
.tl-info { font-size: 0.875rem; font-weight: 600; color: var(--text-primary); }
.tl-sub  { font-size: 0.72rem; color: var(--text-muted); margin-top: 0.12rem; }
.ci-switch { position: relative; display: inline-flex; width: 42px; height: 24px; flex-shrink: 0; }
.ci-switch input { opacity: 0; width: 0; height: 0; position: absolute; }
.ci-switch-track {
    position: absolute; inset: 0; border-radius: 50px;
    background: var(--border); transition: background .22s; cursor: pointer;
}
.ci-switch-track::after {
    content: ''; position: absolute; top: 3px; left: 3px;
    width: 18px; height: 18px; border-radius: 50%;
    background: #fff; box-shadow: 0 1px 3px rgba(0,0,0,0.2);
    transition: transform .22s;
}
.ci-switch input:checked + .ci-switch-track { background: var(--success); }
.ci-switch input:checked + .ci-switch-track::after { transform: translateX(18px); }

/* ── DISCIPLINE INFO CARD ────────────────────────────── */
.discipline-info-card {
    background: var(--surface);
    border: 1px solid var(--border);
    border-radius: var(--radius-sm);
    overflow: hidden;
}
.discipline-info-card-header {
    padding: 0.65rem 1rem;
    background: rgba(59,127,232,0.06);
    border-bottom: 1px solid var(--border);
    display: flex; align-items: center; gap: 0.45rem;
    font-size: 0.72rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.07em;
    color: var(--accent);
}
.discipline-info-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 0;
}
.discipline-info-item {
    padding: 0.75rem 1rem;
    border-right: 1px solid var(--border);
}
.discipline-info-item:last-child { border-right: none; }
.di-label {
    font-size: 0.68rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.07em;
    color: var(--text-muted); margin-bottom: 0.25rem;
}
.di-value {
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.9rem; font-weight: 700;
    color: var(--text-primary);
}
.di-value.accent { color: var(--accent); }
.di-value.success { color: var(--success); }

/* ── FORM DIVIDER ────────────────────────────────────── */
.form-divider {
    border: none; border-top: 1px solid var(--border); margin: 1.5rem 0;
}

/* ── FORM FOOTER ─────────────────────────────────────── */
.form-footer {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 0.75rem;
    padding-top: 1.25rem;
    border-top: 1px solid var(--border);
    margin-top: 1.25rem;
}

.btn-back {
    background: transparent;
    border: 1.5px solid var(--border);
    color: var(--text-secondary);
    border-radius: var(--radius-sm);
    padding: 0.55rem 1.2rem;
    font-size: 0.85rem; font-weight: 600;
    display: inline-flex; align-items: center; gap: 0.45rem;
    text-decoration: none;
    transition: all .18s;
}
.btn-back:hover { background: var(--surface); color: var(--primary); border-color: var(--primary); }

.btn-save {
    background: var(--accent);
    border: none; color: #fff;
    border-radius: var(--radius-sm);
    padding: 0.55rem 1.5rem;
    font-size: 0.875rem; font-weight: 700;
    display: inline-flex; align-items: center; gap: 0.5rem;
    cursor: pointer;
    transition: all .18s;
    font-family: 'Sora', sans-serif;
    box-shadow: 0 3px 10px rgba(59,127,232,0.3);
}
.btn-save:hover { background: var(--accent-hover); transform: translateY(-1px); box-shadow: 0 5px 14px rgba(59,127,232,0.4); }
.btn-save:active { transform: translateY(0); }

/* Alerts */
.alert { border-radius: var(--radius-sm); border: none; font-size: 0.875rem; }
.alert-success { background: rgba(22,168,125,0.1); color: #0E7A5A; border-left: 3px solid var(--success); }
.alert-danger   { background: rgba(232,70,70,0.08); color: #B03030; border-left: 3px solid var(--danger); }

/* ── RESPONSIVE ──────────────────────────────────────── */
@media (max-width: 575px) {
    .ci-page-header { padding: 1.1rem 1.2rem; }
    .ci-page-header h1 { font-size: 1.1rem; }
    .form-card-body { padding: 1.25rem; }
    .context-banner { grid-template-columns: 1fr; }
    .discipline-info-grid { grid-template-columns: 1fr 1fr; }
    .discipline-info-item:nth-child(2) { border-right: none; }
    .discipline-info-item:nth-child(3) { border-top: 1px solid var(--border); grid-column: span 2; }
}
</style>

<!-- ── PAGE HEADER ─────────────────────────────────────── -->
<div class="ci-page-header mb-4">
    <h1>
        <i class="fas fa-edit me-2" style="opacity:.7;font-size:1.2rem;"></i>
        <?= $title ?>
    </h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/courses') ?>">Cursos</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/courses/view/' . $item->course_id) ?>"><?= $item->course_name ?></a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/courses/curriculum/' . $item->course_id) ?>">Currículo</a></li>
            <li class="breadcrumb-item active" aria-current="page">Editar Disciplina</li>
        </ol>
    </nav>
</div>

<!-- ── ALERTS ──────────────────────────────────────────── -->
<?= view('admin/partials/alerts') ?>

<!-- ── FORM CARD ───────────────────────────────────────── -->
<div class="form-card">
    <div class="form-card-header">
        <div class="form-card-header-title">
            <i class="fas fa-edit"></i>
            Editar Disciplina no Currículo
        </div>
    </div>

    <div class="form-card-body">

        <!-- ── CONTEXT BANNER ──────────────────────────── -->
        <div class="context-banner">
            <div class="ctx-item">
                <span class="ctx-label">Curso</span>
                <span class="ctx-value"><?= esc($item->course_name) ?></span>
            </div>
            <div class="ctx-item">
                <span class="ctx-label">Nível</span>
                <span class="ctx-value">
                    <i class="fas fa-layer-group" style="font-size:.75rem;color:var(--accent);"></i>
                    <?= esc($item->level_name) ?>
                </span>
            </div>
            <div class="ctx-item">
                <span class="ctx-label">Disciplina</span>
                <span class="ctx-value"><?= esc($item->discipline_name) ?></span>
            </div>
            <div class="ctx-item">
                <span class="ctx-label">Código</span>
                <span class="ctx-value">
                    <span class="ctx-code"><?= esc($item->discipline_code) ?></span>
                </span>
            </div>
        </div>

        <!-- ── FORM ────────────────────────────────────── -->
        <form action="<?= site_url('admin/courses/curriculum/update/' . $item->id) ?>" method="post" id="editForm">
            <?= csrf_field() ?>

            <!-- ── FIELDS SECTION ──────────────────────── -->
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-sliders-h" style="color:var(--accent);"></i>
                    Configurações do Currículo
                </div>

                <div class="row g-3 mb-3">

                    <!-- Workload -->
                    <div class="col-sm-6">
                        <label class="form-label-ci" for="workload_hours">Carga Horária</label>
                        <div class="input-group-ci">
                            <input type="number"
                                   class="form-input-ci <?= session('errors.workload_hours') ? 'is-invalid' : '' ?>"
                                   id="workload_hours"
                                   name="workload_hours"
                                   value="<?= old('workload_hours', $item->workload_hours ?? $item->default_workload) ?>"
                                   min="0" max="1000"
                                   placeholder="<?= $item->default_workload ?? 0 ?>">
                            <span class="input-suffix">h</span>
                        </div>
                        <div class="form-hint">
                            Deixe em branco para usar o padrão
                            (<strong style="color:var(--text-primary);"><?= $item->default_workload ?? 0 ?>h</strong>)
                        </div>
                        <?php if (session('errors.workload_hours')): ?>
                            <div class="form-error">
                                <i class="fas fa-exclamation-circle"></i>
                                <?= session('errors.workload_hours') ?>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Semester -->
                    <div class="col-sm-6">
                        <label class="form-label-ci" for="semester">Semestre / Trimestre</label>
                        <select class="form-select-ci <?= session('errors.semester') ? 'is-invalid' : '' ?>"
                                id="semester" name="semester">
                            <option value="Anual" <?= old('semester', $item->semester) == 'Anual' ? 'selected' : '' ?>>Anual</option>
                            <option value="1"     <?= old('semester', $item->semester) == '1'     ? 'selected' : '' ?>>1º Trimestre</option>
                            <option value="2"     <?= old('semester', $item->semester) == '2'     ? 'selected' : '' ?>>2º Trimestre</option>
                        </select>
                        <?php if (session('errors.semester')): ?>
                            <div class="form-error">
                                <i class="fas fa-exclamation-circle"></i>
                                <?= session('errors.semester') ?>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Mandatory toggle -->
                <div class="col-12">
                    <label class="form-label-ci">Obrigatoriedade</label>
                    <label class="toggle-row <?= old('is_mandatory', $item->is_mandatory) ? 'checked' : '' ?>"
                           id="toggle-mandatory" for="is_mandatory">
                        <div>
                            <div class="tl-info">Disciplina Obrigatória</div>
                            <div class="tl-sub">Exigida para todos os alunos matriculados neste nível</div>
                        </div>
                        <label class="ci-switch">
                            <input type="checkbox"
                                   id="is_mandatory"
                                   name="is_mandatory"
                                   value="1"
                                   <?= old('is_mandatory', $item->is_mandatory) ? 'checked' : '' ?>>
                            <span class="ci-switch-track"></span>
                        </label>
                    </label>
                </div>
            </div>

            <!-- ── DISCIPLINE INFO ──────────────────────── -->
            <div class="form-section">
                <div class="form-section-title">
                    <i class="fas fa-info-circle" style="color:var(--accent);"></i>
                    Informações da Disciplina
                </div>
                <div class="discipline-info-card">
                    <div class="discipline-info-card-header">
                        <i class="fas fa-book"></i>
                        <?= esc($item->discipline_name) ?>
                    </div>
                    <div class="discipline-info-grid">
                        <div class="discipline-info-item">
                            <div class="di-label">Carga Padrão</div>
                            <div class="di-value accent"><?= $item->default_workload ?? 0 ?>h</div>
                        </div>
                        <?php if (isset($item->approval_grade)): ?>
                        <div class="discipline-info-item">
                            <div class="di-label">Nota Aprovação</div>
                            <div class="di-value success"><?= $item->approval_grade ?> val.</div>
                        </div>
                        <?php endif; ?>
                        <?php if (isset($item->discipline_type)): ?>
                        <div class="discipline-info-item">
                            <div class="di-label">Tipo</div>
                            <div class="di-value"><?= esc($item->discipline_type) ?></div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- ── FOOTER BUTTONS ──────────────────────── -->
            <div class="form-footer">
                <a href="<?= site_url('admin/courses/curriculum/' . $item->course_id) ?>" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Voltar ao Currículo
                </a>
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i> Salvar Alterações
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

    /* ── Workload input clamp ──────────────────────────── */
    var wlInput = document.getElementById('workload_hours');
    if (wlInput) {
        wlInput.addEventListener('input', function () {
            var v = parseInt(this.value, 10);
            if (!isNaN(v)) {
                if (v > 1000) this.value = 1000;
                if (v < 0)    this.value = 0;
            }
        });
    }

    /* ── Toggle row visual state sync ─────────────────── */
    var mandCheck  = document.getElementById('is_mandatory');
    var toggleRow  = document.getElementById('toggle-mandatory');
    if (mandCheck && toggleRow) {
        mandCheck.addEventListener('change', function () {
            toggleRow.classList.toggle('checked', this.checked);
        });
    }

})();
</script>
<?= $this->endSection() ?>