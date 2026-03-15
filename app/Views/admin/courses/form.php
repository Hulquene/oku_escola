<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<style>


/* ── SECTION DIVIDER ─────────────────────────────────── */
.form-section {
    margin-bottom: 2rem;
}
.form-section-title {
    font-size: 0.72rem;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.1em;
    color: var(--text-muted);
    margin-bottom: 1.25rem;
    display: flex;
    align-items: center;
    gap: 0.6rem;
}
.form-section-title::after {
    content: '';
    flex: 1;
    height: 1px;
    background: var(--border);
}

/* ── FORM CONTROLS ───────────────────────────────────── */
.form-label-ci {
    font-size: 0.78rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.06em;
    color: var(--text-secondary);
    margin-bottom: 0.4rem;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}
.form-label-ci .req { color: var(--danger); font-size: 0.85em; }

.form-input-ci,
.form-select-ci,
.form-textarea-ci {
    width: 100%;
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 0.6rem 0.9rem;
    font-size: 0.875rem;
    font-family: 'Sora', sans-serif;
    color: var(--text-primary);
    background: var(--surface);
    transition: border-color .2s, box-shadow .2s, background .2s;
    appearance: auto;
}
.form-input-ci:focus,
.form-select-ci:focus,
.form-textarea-ci:focus {
    outline: none;
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(59,127,232,0.12);
    background: #fff;
}
.form-input-ci.is-invalid,
.form-select-ci.is-invalid {
    border-color: var(--danger);
    box-shadow: 0 0 0 3px rgba(232,70,70,0.10);
}
.form-textarea-ci { resize: vertical; min-height: 110px; }
.form-hint {
    font-size: 0.75rem;
    color: var(--text-muted);
    margin-top: 0.35rem;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}
.error-msg {
    font-size: 0.78rem;
    color: var(--danger);
    margin-top: 0.3rem;
    display: flex;
    align-items: center;
    gap: 0.3rem;
}

/* ── DURATION DISPLAY ────────────────────────────────── */
.duration-display {
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 0.55rem 0.9rem;
    background: var(--surface);
    display: flex;
    align-items: center;
    gap: 0.75rem;
    min-height: 42px;
    transition: border-color .3s;
}
.duration-display.has-value {
    border-color: var(--accent);
    background: rgba(59,127,232,0.05);
}
.duration-display .dur-value {
    font-family: 'JetBrains Mono', monospace;
    font-size: 1.6rem;
    font-weight: 700;
    color: var(--accent);
    line-height: 1;
    min-width: 2rem;
    text-align: center;
    transition: color .3s;
}
.duration-display .dur-value.empty { color: var(--text-muted); font-size: 1rem; }
.duration-display .dur-label {
    font-size: 0.78rem;
    font-weight: 600;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 0.06em;
}
.duration-display .dur-sub {
    font-size: 0.72rem;
    color: var(--text-muted);
}

/* ── TOGGLE SWITCH ───────────────────────────────────── */
.toggle-row {
    display: flex;
    align-items: center;
    justify-content: space-between;
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 0.7rem 1rem;
    cursor: pointer;
    transition: border-color .2s;
    min-height: 42px;
}
.toggle-row:hover { border-color: var(--accent); }
.toggle-row .toggle-info { display: flex; flex-direction: column; }
.toggle-row .toggle-title { font-size: 0.875rem; font-weight: 600; color: var(--text-primary); }
.toggle-row .toggle-sub   { font-size: 0.74rem; color: var(--text-muted); }

.ci-switch {
    position: relative;
    display: inline-flex;
    width: 42px;
    height: 24px;
    flex-shrink: 0;
}
.ci-switch input { opacity: 0; width: 0; height: 0; }
.ci-switch-track {
    position: absolute;
    inset: 0;
    border-radius: 50px;
    background: var(--border);
    transition: background .25s;
    cursor: pointer;
}
.ci-switch-track::after {
    content: '';
    position: absolute;
    top: 3px; left: 3px;
    width: 18px; height: 18px;
    border-radius: 50%;
    background: #fff;
    box-shadow: 0 1px 4px rgba(0,0,0,0.2);
    transition: transform .25s;
}
.ci-switch input:checked + .ci-switch-track { background: var(--success); }
.ci-switch input:checked + .ci-switch-track::after { transform: translateX(18px); }

/* ── INFO BANNER ─────────────────────────────────────── */
.info-banner {
    background: rgba(59,127,232,0.07);
    border-left: 3px solid var(--accent);
    border-radius: 0 var(--radius-sm) var(--radius-sm) 0;
    padding: 0.85rem 1.1rem;
    font-size: 0.82rem;
    color: var(--text-secondary);
    display: flex;
    align-items: flex-start;
    gap: 0.65rem;
}
.info-banner i { color: var(--accent); flex-shrink: 0; margin-top: 0.1rem; }
.info-banner strong { color: var(--text-primary); }

/* ── FOOTER ACTIONS ──────────────────────────────────── */
.form-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 1.5rem;
    border-top: 1px solid var(--border);
    margin-top: 1rem;
    flex-wrap: wrap;
    gap: 0.75rem;
}
.btn-back {
    background: transparent;
    border: 1.5px solid var(--border);
    color: var(--text-secondary);
    border-radius: var(--radius-sm);
    padding: 0.6rem 1.4rem;
    font-size: 0.875rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    text-decoration: none;
    transition: all .2s;
    cursor: pointer;
}
.btn-back:hover { background: var(--surface); color: var(--text-primary); border-color: var(--primary); }

.btn-save {
    background: var(--accent);
    color: #fff;
    border: none;
    border-radius: var(--radius-sm);
    padding: 0.6rem 1.6rem;
    font-size: 0.875rem;
    font-weight: 700;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
    box-shadow: 0 4px 14px rgba(59,127,232,0.35);
    transition: background .2s, transform .15s, box-shadow .2s;
}
.btn-save:hover {
    background: var(--accent-hover);
    transform: translateY(-1px);
    box-shadow: 0 6px 18px rgba(59,127,232,0.45);
}

/* Alerts */
.alert { border-radius: var(--radius-sm); border: none; font-size: 0.875rem; }
.alert-success { background: rgba(22,168,125,0.1);  color: #0E7A5A; border-left: 3px solid var(--success); }
.alert-danger   { background: rgba(232,70,70,0.08);  color: #B03030; border-left: 3px solid var(--danger); }

@media (max-width: 768px) {
    .ci-page-header { padding: 1.5rem; }
    .ci-page-header h1 { font-size: 1.3rem; }
    .form-card-body { padding: 1.25rem; }
}
</style>

<!-- ── PAGE HEADER ─────────────────────────────────────── -->
<div class="ci-page-header mb-4">
    <div>
        <h1>
            <i class="fas fa-<?= $course ? 'edit' : 'plus-circle' ?> me-2" style="opacity:.7;font-size:1.3rem;"></i>
            <?= $title ?>
        </h1>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a href="<?= route_to('admin.dashboard') ?>">Dashboard</a></li>
                <li class="breadcrumb-item"><a href="<?= route_to('admin.courses') ?>">Cursos</a></li>
                <li class="breadcrumb-item active" aria-current="page"><?= $title ?></li>
            </ol>
        </nav>
    </div>
</div>

<!-- ── ALERTS ──────────────────────────────────────────── -->
<?= view('admin/partials/alerts') ?>

<!-- ── FORM CARD ───────────────────────────────────────── -->
<div class="form-card">
    <div class="form-card-header">
        <i class="fas fa-<?= $course ? 'edit' : 'plus-circle' ?>"></i>
        <?= $title ?>
    </div>
    <div class="form-card-body">
        <form action="<?=  site_url('admin/academic/courses/save') ?>" method="post">
            <?= csrf_field() ?>
            <?php if ($course): ?>
                <input type="hidden" name="id" value="<?= $course->id ?>">
            <?php endif; ?>

            <!-- ── SECTION: Informações Básicas ── -->
            <div class="form-section">
                <div class="form-section-title"><i class="fas fa-info-circle" style="color:var(--accent);"></i> Informações Básicas</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label-ci" for="course_name">Nome do Curso <span class="req">*</span></label>
                        <input type="text"
                               class="form-input-ci <?= session('errors.course_name') ? 'is-invalid' : '' ?>"
                               id="course_name" name="course_name"
                               value="<?= old('course_name', $course->course_name ?? '') ?>"
                               required maxlength="100"
                               placeholder="Ex: Ciências Físico-Biológicas">
                        <?php if (session('errors.course_name')): ?>
                            <div class="error-msg"><i class="fas fa-exclamation-circle"></i><?= session('errors.course_name') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label-ci" for="course_code">Código <span class="req">*</span></label>
                        <input type="text"
                               class="form-input-ci <?= session('errors.course_code') ? 'is-invalid' : '' ?>"
                               id="course_code" name="course_code"
                               value="<?= old('course_code', $course->course_code ?? '') ?>"
                               required maxlength="20"
                               placeholder="Ex: CFB"
                               style="font-family:'JetBrains Mono',monospace; letter-spacing:.05em; text-transform:uppercase;">
                        <div class="form-hint"><i class="fas fa-tag" style="font-size:.65rem;"></i> Código único identificador</div>
                        <?php if (session('errors.course_code')): ?>
                            <div class="error-msg"><i class="fas fa-exclamation-circle"></i><?= session('errors.course_code') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label-ci" for="course_type">Tipo <span class="req">*</span></label>
                        <select class="form-select-ci <?= session('errors.course_type') ? 'is-invalid' : '' ?>"
                                id="course_type" name="course_type" required>
                            <option value="">Selecione...</option>
                            <?php foreach (['Ciências','Humanidades','Económico-Jurídico','Técnico','Profissional','Outro'] as $t): ?>
                                <option value="<?= $t ?>" <?= old('course_type', $course->course_type ?? '') == $t ? 'selected' : '' ?>><?= $t ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?php if (session('errors.course_type')): ?>
                            <div class="error-msg"><i class="fas fa-exclamation-circle"></i><?= session('errors.course_type') ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- ── SECTION: Níveis do Curso ── -->
            <div class="form-section">
                <div class="form-section-title"><i class="fas fa-layer-group" style="color:var(--accent);"></i> Níveis do Curso</div>
                <div class="row g-3 align-items-start">

                    <div class="col-md-4">
                        <label class="form-label-ci" for="start_grade_id">Nível Inicial <span class="req">*</span></label>
                        <select class="form-select-ci <?= session('errors.start_grade_id') ? 'is-invalid' : '' ?>"
                                id="start_grade_id" name="start_grade_id" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($highSchoolLevels as $level): ?>
                                <option value="<?= $level->id ?>"
                                        data-order="<?= $level->id ?>"
                                    <?= old('start_grade_id', $course->start_grade_id ?? '') == $level->id ? 'selected' : '' ?>>
                                    <?= $level->level_name ?> (<?= $level->education_level ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-hint"><i class="fas fa-play-circle" style="font-size:.65rem;"></i> Normalmente 10ª Classe</div>
                        <?php if (session('errors.start_grade_id')): ?>
                            <div class="error-msg"><i class="fas fa-exclamation-circle"></i><?= session('errors.start_grade_id') ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="col-md-4">
                        <label class="form-label-ci" for="end_grade_id">Nível Final <span class="req">*</span></label>
                        <select class="form-select-ci <?= session('errors.end_grade_id') ? 'is-invalid' : '' ?>"
                                id="end_grade_id" name="end_grade_id" required>
                            <option value="">Selecione...</option>
                            <?php foreach ($highSchoolLevels as $level): ?>
                                <option value="<?= $level->id ?>"
                                        data-order="<?= $level->id ?>"
                                    <?= old('end_grade_id', $course->end_grade_id ?? '') == $level->id ? 'selected' : '' ?>>
                                    <?= $level->level_name ?> (<?= $level->education_level ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <div class="form-hint"><i class="fas fa-flag-checkered" style="font-size:.65rem;"></i> Normalmente 12ª ou 13ª Classe</div>
                        <?php if (session('errors.end_grade_id')): ?>
                            <div class="error-msg"><i class="fas fa-exclamation-circle"></i><?= session('errors.end_grade_id') ?></div>
                        <?php endif; ?>
                    </div>

                    <!-- Duração calculada automaticamente -->
                    <div class="col-md-4">
                        <label class="form-label-ci">Duração <span style="color:var(--accent);font-size:.7rem;font-weight:700;background:rgba(59,127,232,.1);padding:.1rem .5rem;border-radius:50px;letter-spacing:.03em;">AUTO</span></label>
                        <input type="hidden" id="duration_years" name="duration_years"
                               value="<?= old('duration_years', $course->duration_years ?? '') ?>">
                        <div class="duration-display" id="durationDisplay">
                            <div class="dur-value empty" id="durValue">—</div>
                            <div>
                                <div class="dur-label" id="durLabel">anos de curso</div>
                                <div class="dur-sub" id="durSub">Selecione os níveis</div>
                            </div>
                        </div>
                        <div class="form-hint"><i class="fas fa-magic" style="font-size:.65rem;"></i> Calculado automaticamente pelos níveis</div>
                    </div>
                </div>
            </div>

            <!-- ── SECTION: Configurações ── -->
            <div class="form-section">
                <div class="form-section-title"><i class="fas fa-cog" style="color:var(--accent);"></i> Configurações</div>
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label-ci">Estado do Curso</label>
                        <label class="toggle-row" for="is_active">
                            <div class="toggle-info">
                                <span class="toggle-title">Curso Ativo</span>
                                <span class="toggle-sub">Curso disponível para matrículas e listagens</span>
                            </div>
                            <label class="ci-switch">
                                <input type="checkbox" id="is_active" name="is_active" value="1"
                                       <?= (old('is_active', $course->is_active ?? true)) ? 'checked' : '' ?>>
                                <span class="ci-switch-track"></span>
                            </label>
                        </label>
                    </div>
                </div>
            </div>

            <!-- ── SECTION: Descrição ── -->
            <div class="form-section">
                <div class="form-section-title"><i class="fas fa-align-left" style="color:var(--accent);"></i> Descrição</div>
                <div class="row g-3">
                    <div class="col-md-12">
                        <label class="form-label-ci" for="description">Descrição do Curso</label>
                        <textarea class="form-textarea-ci"
                                  id="description" name="description"
                                  placeholder="Descreva os objectivos, áreas de atuação e características do curso..."><?= old('description', $course->description ?? '') ?></textarea>
                        <div class="form-hint"><i class="fas fa-lightbulb" style="font-size:.65rem;"></i> Objectivos, áreas de atuação, perfil do aluno, etc.</div>
                    </div>
                </div>
            </div>

            <!-- Info banner -->
            <div class="info-banner mb-4">
                <i class="fas fa-info-circle"></i>
                <span><strong>Nota:</strong> Após criar o curso, poderá adicionar as disciplinas por nível no <strong>Currículo do Curso</strong>.</span>
            </div>

            <!-- Footer -->
            <div class="form-footer">
                <a href="<?= route_to('admin.courses') ?>" class="btn-back">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
                <button type="submit" class="btn-save">
                    <i class="fas fa-save"></i>
                    <?= $course ? 'Salvar Alterações' : 'Salvar Curso' ?>
                </button>
            </div>
        </form>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
document.addEventListener('DOMContentLoaded', function () {
    const startSelect   = document.getElementById('start_grade_id');
    const endSelect     = document.getElementById('end_grade_id');
    const durationInput = document.getElementById('duration_years');
    const display       = document.getElementById('durationDisplay');
    const durValue      = document.getElementById('durValue');
    const durLabel      = document.getElementById('durLabel');
    const durSub        = document.getElementById('durSub');

    // Extrair o número da classe a partir do texto da option
    function extractGradeNumber(selectEl) {
        const opt = selectEl.options[selectEl.selectedIndex];
        if (!opt || !opt.value) return null;
        const match = opt.text.match(/(\d+)/);
        return match ? parseInt(match[1]) : null;
    }

    function updateDuration() {
        const startGrade = extractGradeNumber(startSelect);
        const endGrade   = extractGradeNumber(endSelect);

        if (startGrade && endGrade) {
            if (endGrade <= startGrade) {
                durValue.textContent = '!';
                durValue.className   = 'dur-value';
                durValue.style.color = 'var(--danger)';
                durLabel.textContent = 'Nível inválido';
                durSub.textContent   = 'O nível final deve ser maior';
                display.classList.remove('has-value');
                display.style.borderColor = 'var(--danger)';
                display.style.background  = 'rgba(232,70,70,0.05)';
                durationInput.value = '';
                return;
            }

            const years = (endGrade - startGrade) + 1;
            durationInput.value = years;

            durValue.textContent = years;
            durValue.className   = 'dur-value';
            durValue.style.color = '';
            durLabel.textContent = years === 1 ? 'ano de curso' : 'anos de curso';
            durSub.textContent   = startGrade + 'ª até ' + endGrade + 'ª Classe';
            display.classList.add('has-value');
            display.style.borderColor = '';
            display.style.background  = '';
        } else {
            durationInput.value = '';
            durValue.textContent = '—';
            durValue.className   = 'dur-value empty';
            durValue.style.color = '';
            durLabel.textContent = 'anos de curso';
            durSub.textContent   = 'Selecione os níveis';
            display.classList.remove('has-value');
            display.style.borderColor = '';
            display.style.background  = '';
        }
    }

    startSelect.addEventListener('change', updateDuration);
    endSelect.addEventListener('change', updateDuration);

    // Calcular na carga da página se já tiver valores (modo edição)
    if (startSelect.value && endSelect.value) updateDuration();
});
</script>
<?= $this->endSection() ?>