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
    content: ''; position: absolute;
    top: -60px; right: -60px;
    width: 200px; height: 200px;
    border-radius: 50%;
    background: rgba(255,255,255,0.04);
    pointer-events: none;
}
.ci-page-header::after {
    content: ''; position: absolute;
    bottom: -40px; right: 100px;
    width: 130px; height: 130px;
    border-radius: 50%;
    background: rgba(59,127,232,0.15);
    pointer-events: none;
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

/* ── GENERIC CARD ────────────────────────────────────── */
.ci-card {
    background: var(--surface-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    margin-bottom: 1.25rem;
}
.ci-card:last-child { margin-bottom: 0; }
.ci-card-header {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 0.6rem;
    padding: 0.85rem 1.4rem;
    background: var(--surface);
    border-bottom: 1px solid var(--border);
}
.ci-card-title {
    display: flex; align-items: center; gap: 0.6rem;
    font-size: 0.85rem; font-weight: 700; color: var(--text-primary);
}
.ci-card-title i { color: var(--accent); font-size: 0.85rem; }
.ci-card-body { padding: 1.4rem; }

/* ── INFO TABLE ──────────────────────────────────────── */
.info-table { width: 100%; border-collapse: collapse; }
.info-table tr { border-bottom: 1px solid var(--border); }
.info-table tr:last-child { border-bottom: none; }
.info-table th {
    padding: 0.6rem 0.75rem 0.6rem 0;
    font-size: 0.72rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.07em;
    color: var(--text-muted);
    width: 38%; vertical-align: top;
    white-space: nowrap;
}
.info-table td {
    padding: 0.6rem 0;
    font-size: 0.875rem; color: var(--text-primary);
    vertical-align: middle;
}

/* Info divider */
.info-divider {
    border: none; border-top: 1px solid var(--border);
    margin: 0.75rem 0;
}
.desc-label {
    font-size: 0.72rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.07em;
    color: var(--text-muted); margin-bottom: 0.5rem; display: block;
}
.desc-text { font-size: 0.875rem; color: var(--text-secondary); line-height: 1.6; }

/* ── BADGES & CHIPS ──────────────────────────────────── */
.code-badge {
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.78rem; font-weight: 600;
    background: var(--primary); color: #fff;
    padding: 0.28rem 0.65rem; border-radius: 7px;
    letter-spacing: 0.04em; display: inline-block;
}
.type-badge {
    font-size: 0.7rem; font-weight: 700; padding: 0.25rem 0.7rem;
    border-radius: 50px; letter-spacing: 0.04em; text-transform: uppercase;
    display: inline-block; white-space: nowrap;
}
.type-primary   { background: rgba(27,43,75,0.10);   color: var(--primary); }
.type-success   { background: rgba(22,168,125,0.12);  color: var(--success); }
.type-warning   { background: rgba(232,160,32,0.14);  color: #C07818; }
.type-info      { background: rgba(59,127,232,0.12);  color: var(--accent); }
.type-secondary { background: rgba(107,122,153,0.12); color: #4A5568; }
.type-dark      { background: rgba(27,43,75,0.15);    color: #2D3748; }

.status-active   { color: var(--success); background: rgba(22,168,125,0.1);  font-size: 0.7rem; font-weight: 700; padding: 0.22rem 0.65rem; border-radius: 50px; text-transform: uppercase; letter-spacing: 0.05em; display: inline-flex; align-items: center; gap: 0.25rem; }
.status-inactive { color: var(--danger);  background: rgba(232,70,70,0.08);   font-size: 0.7rem; font-weight: 700; padding: 0.22rem 0.65rem; border-radius: 50px; text-transform: uppercase; letter-spacing: 0.05em; display: inline-flex; align-items: center; gap: 0.25rem; }
.status-dot { width: 5px; height: 5px; border-radius: 50%; background: currentColor; flex-shrink: 0; }

.num-chip {
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.78rem; font-weight: 700;
    background: var(--surface); border: 1.5px solid var(--border);
    border-radius: 7px; padding: 0.2rem 0.55rem;
    color: var(--text-primary); display: inline-block;
}
.num-chip.blue   { background: rgba(59,127,232,0.08); border-color: rgba(59,127,232,0.2);  color: var(--accent); }
.num-chip.green  { background: rgba(22,168,125,0.08); border-color: rgba(22,168,125,0.2);  color: var(--success); }
.num-chip.orange { background: rgba(232,160,32,0.08); border-color: rgba(232,160,32,0.2);  color: #B07800; }

.level-chip {
    display: inline-flex; align-items: center; gap: 0.3rem;
    background: rgba(27,43,75,0.07); border: 1px solid rgba(27,43,75,0.12);
    border-radius: 6px; padding: 0.2rem 0.6rem;
    font-size: 0.72rem; font-weight: 600; color: var(--primary);
    margin: 0 0.3rem 0.3rem 0;
}

/* ── STAT ITEMS (sidebar) ────────────────────────────── */
.stat-list-item {
    display: flex; align-items: center; justify-content: space-between;
    padding: 0.7rem 0;
    border-bottom: 1px solid var(--border);
    gap: 0.75rem;
}
.stat-list-item:last-child { border-bottom: none; padding-bottom: 0; }
.stat-list-item:first-child { padding-top: 0; }
.stat-list-label {
    display: flex; align-items: center; gap: 0.6rem;
    font-size: 0.82rem; color: var(--text-secondary); font-weight: 500;
}
.stat-list-label .stat-icon {
    width: 28px; height: 28px; border-radius: 7px;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.72rem; flex-shrink: 0;
}
.stat-icon.blue   { background: rgba(59,127,232,0.1);  color: var(--accent); }
.stat-icon.green  { background: rgba(22,168,125,0.1);  color: var(--success); }
.stat-icon.orange { background: rgba(232,160,32,0.1);  color: #B07800; }
.stat-icon.purple { background: rgba(124,77,255,0.1);  color: #7C4DFF; }

/* ── QUICK ACTION BUTTONS ────────────────────────────── */
.qa-btn {
    display: flex; align-items: center; gap: 0.65rem;
    padding: 0.65rem 1rem;
    border-radius: var(--radius-sm);
    font-size: 0.85rem; font-weight: 600;
    text-decoration: none;
    cursor: pointer;
    transition: all .18s;
    border: 1.5px solid transparent;
    width: 100%;
    font-family: 'Sora', sans-serif;
}
.qa-btn .qa-icon {
    width: 30px; height: 30px; border-radius: 8px;
    display: flex; align-items: center; justify-content: center;
    font-size: 0.78rem; flex-shrink: 0;
}
.qa-btn.primary  { background: var(--accent);   border-color: var(--accent);   color: #fff; box-shadow: 0 3px 10px rgba(59,127,232,0.28); }
.qa-btn.primary  .qa-icon { background: rgba(255,255,255,0.2); color: #fff; }
.qa-btn.primary:hover  { background: var(--accent-hover); border-color: var(--accent-hover); transform: translateY(-1px); }

.qa-btn.secondary { background: var(--surface); border-color: var(--border); color: var(--text-primary); }
.qa-btn.secondary .qa-icon { background: rgba(59,127,232,0.1); color: var(--accent); }
.qa-btn.secondary:hover { background: #EEF2FF; border-color: var(--accent); color: var(--primary); }

.qa-btn.danger-outline { background: transparent; border-color: rgba(232,70,70,0.25); color: var(--danger); }
.qa-btn.danger-outline .qa-icon { background: rgba(232,70,70,0.1); color: var(--danger); }
.qa-btn.danger-outline:hover { background: rgba(232,70,70,0.06); border-color: var(--danger); }

/* ── ACCORDION (curriculum) ──────────────────────────── */
.ci-accordion { border: 1px solid var(--border); border-radius: var(--radius-sm); overflow: hidden; }
.ci-accordion-item { border-bottom: 1px solid var(--border); }
.ci-accordion-item:last-child { border-bottom: none; }

.ci-acc-btn {
    width: 100%; display: flex; align-items: center; justify-content: space-between;
    padding: 0.85rem 1.2rem;
    background: var(--surface); border: none; cursor: pointer;
    font-family: 'Sora', sans-serif;
    transition: background .15s;
    gap: 0.75rem;
    text-align: left;
}
.ci-acc-btn:hover { background: #EEF2FF; }
.ci-acc-btn.open  { background: rgba(59,127,232,0.06); }

.ci-acc-btn-left  { display: flex; align-items: center; gap: 0.65rem; }
.ci-acc-level-name { font-size: 0.875rem; font-weight: 700; color: var(--text-primary); }
.ci-acc-disc-count {
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.65rem; font-weight: 700;
    background: var(--accent); color: #fff;
    padding: 0.1rem 0.45rem; border-radius: 50px;
}
.ci-acc-btn-right { display: flex; align-items: center; gap: 0.75rem; }
.ci-acc-workload {
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.72rem; font-weight: 600;
    color: var(--text-muted);
}
.ci-acc-chevron {
    width: 22px; height: 22px; border-radius: 6px;
    background: var(--border); color: var(--text-muted);
    display: flex; align-items: center; justify-content: center;
    font-size: 0.6rem; transition: transform .22s, background .15s;
    flex-shrink: 0;
}
.ci-acc-btn.open .ci-acc-chevron { transform: rotate(90deg); background: var(--accent); color: #fff; }

.ci-acc-body { display: none; }
.ci-acc-body.open { display: block; }

/* Curriculum table */
.curr-table { width: 100%; border-collapse: collapse; }
.curr-table thead tr th {
    background: var(--surface);
    font-size: 0.65rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.09em;
    color: var(--text-secondary);
    padding: 0.55rem 1rem;
    border-bottom: 1px solid var(--border);
    white-space: nowrap;
}
.curr-table tbody tr { border-bottom: 1px solid var(--border); transition: background .12s; }
.curr-table tbody tr:last-child { border-bottom: none; }
.curr-table tbody tr:hover { background: #F8FAFF; }
.curr-table tbody td { padding: 0.6rem 1rem; font-size: 0.82rem; vertical-align: middle; }
.curr-table tbody td.text-center { text-align: center; }
.curr-table tfoot tr td {
    background: var(--surface);
    padding: 0.55rem 1rem;
    font-size: 0.78rem; font-weight: 700; color: var(--text-secondary);
    border-top: 1.5px solid var(--border);
}

.disc-code {
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.7rem; font-weight: 600;
    background: rgba(27,43,75,0.07); color: var(--primary);
    padding: 0.18rem 0.5rem; border-radius: 5px;
    display: inline-block;
}
.workload-chip {
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.73rem; font-weight: 700;
    background: rgba(59,127,232,0.08); border: 1.5px solid rgba(59,127,232,0.18);
    color: var(--accent); padding: 0.18rem 0.5rem; border-radius: 6px; display: inline-block;
}
.workload-chip.total {
    background: rgba(22,168,125,0.1); border-color: rgba(22,168,125,0.2); color: var(--success);
}
.sem-badge {
    font-size: 0.65rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.04em;
    padding: 0.18rem 0.55rem; border-radius: 50px; display: inline-block; white-space: nowrap;
}
.sem-anual { background: rgba(27,43,75,0.07);  color: var(--primary); }
.sem-1     { background: rgba(59,127,232,0.1);  color: var(--accent); }
.sem-2     { background: rgba(22,168,125,0.1);  color: var(--success); }
.sem-other { background: rgba(107,122,153,0.1); color: var(--text-secondary); }

.mand-yes { color: var(--success); font-size: 0.9rem; }
.mand-no  { color: var(--text-muted); font-size: 0.9rem; }

/* Curriculum empty */
.curr-empty {
    text-align: center; padding: 3rem 1.5rem; color: var(--text-muted);
}
.curr-empty i { font-size: 2.2rem; opacity: 0.2; display: block; margin-bottom: 0.6rem; }
.curr-empty strong { display: block; font-size: 0.95rem; font-weight: 600; color: var(--text-secondary); margin-bottom: 0.35rem; }
.curr-empty p { font-size: 0.85rem; margin-bottom: 1rem; }

/* ── MODAL ───────────────────────────────────────────── */
.ci-modal .modal-content { border: none; border-radius: var(--radius); overflow: hidden; box-shadow: var(--shadow-lg); }
.ci-modal .modal-header { background: var(--danger); padding: 0.9rem 1.4rem; }
.ci-modal .modal-title { font-size: 0.9rem; font-weight: 700; color: #fff; display: flex; align-items: center; gap: 0.5rem; }
.ci-modal .btn-close-white { filter: brightness(0) invert(1); opacity: 0.8; }
.ci-modal .modal-body { padding: 1.4rem; }
.ci-modal .modal-body p { font-size: 0.875rem; margin-bottom: 0.75rem; }
.alert-danger-soft { background: rgba(232,70,70,0.07); border-left: 3px solid var(--danger); padding: 0.6rem 0.9rem; border-radius: 0 8px 8px 0; font-size: 0.78rem; color: var(--danger); }
.ci-modal .modal-footer { padding: 0.9rem 1.4rem; border-top: 1px solid var(--border); gap: 0.5rem; }
.btn-cancel { background: var(--surface); border: 1.5px solid var(--border); color: var(--text-secondary); padding: 0.45rem 1.1rem; border-radius: var(--radius-sm); font-size: 0.85rem; font-weight: 600; cursor: pointer; transition: all .18s; font-family: 'Sora', sans-serif; }
.btn-cancel:hover { background: #fff; color: var(--text-primary); }
.btn-danger-ci { background: var(--danger); border: none; color: #fff; padding: 0.45rem 1.2rem; border-radius: var(--radius-sm); font-size: 0.85rem; font-weight: 700; display: inline-flex; align-items: center; gap: 0.45rem; cursor: pointer; transition: all .18s; font-family: 'Sora', sans-serif; text-decoration: none; }
.btn-danger-ci:hover { background: #CC3535; color: #fff; }

/* Alerts */
.alert { border-radius: var(--radius-sm); border: none; font-size: 0.875rem; }
.alert-success { background: rgba(22,168,125,0.1); color: #0E7A5A; border-left: 3px solid var(--success); }
.alert-danger   { background: rgba(232,70,70,0.08); color: #B03030; border-left: 3px solid var(--danger); }

/* ── RESPONSIVE ──────────────────────────────────────── */
@media (max-width: 991px) {
    .view-layout { flex-direction: column; }
    .view-sidebar { width: 100% !important; }
}
@media (max-width: 575px) {
    .ci-page-header { padding: 1.1rem 1.2rem; }
    .ci-page-header h1 { font-size: 1.1rem; }
    .ci-card-body { padding: 1rem; }
    .curr-table thead th:nth-child(4),
    .curr-table tbody td:nth-child(4) { display: none; }
}
</style>

<!-- ── PAGE HEADER ─────────────────────────────────────── -->
<div class="ci-page-header mb-4">
    <h1>
        <i class="fas fa-layer-group me-2" style="opacity:.7;font-size:1.2rem;"></i>
        <?= $title ?>
    </h1>
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb mb-0">
            <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
            <li class="breadcrumb-item"><a href="<?= site_url('admin/courses') ?>">Cursos</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= $course->course_name ?></li>
        </ol>
    </nav>
</div>

<!-- ── ALERTS ──────────────────────────────────────────── -->
<?= view('admin/partials/alerts') ?>

<!-- ── MAIN LAYOUT ─────────────────────────────────────── -->
<div class="d-flex gap-3 flex-wrap flex-lg-nowrap view-layout mb-4">

    <!-- ── LEFT: Course Info ───────────────────────────── -->
    <div class="flex-grow-1 min-w-0">
        <div class="ci-card">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-info-circle"></i>
                    Informações do Curso
                </div>
                <a href="<?= site_url('admin/courses/form-edit/' . $course->id) ?>"
                   style="background:transparent;border:1.5px solid var(--border);color:var(--text-secondary);border-radius:var(--radius-sm);padding:.35rem .85rem;font-size:.78rem;font-weight:600;display:inline-flex;align-items:center;gap:.4rem;text-decoration:none;transition:all .18s;"
                   onmouseover="this.style.background='var(--surface)';this.style.color='var(--primary)';this.style.borderColor='var(--primary)'"
                   onmouseout="this.style.background='transparent';this.style.color='var(--text-secondary)';this.style.borderColor='var(--border)'">
                    <i class="fas fa-edit"></i> Editar
                </a>
            </div>
            <div class="ci-card-body">
                <div class="row g-4">
                    <!-- Left info column -->
                    <div class="col-md-6">
                        <table class="info-table">
                            <tr>
                                <th>Nome</th>
                                <td><strong style="font-size:.9rem;"><?= $course->course_name ?></strong></td>
                            </tr>
                            <tr>
                                <th>Código</th>
                                <td><span class="code-badge"><?= $course->course_code ?></span></td>
                            </tr>
                            <tr>
                                <th>Tipo</th>
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
                            </tr>
                            <tr>
                                <th>Duração</th>
                                <td><span class="num-chip"><?= $course->duration_years ?> ano<?= $course->duration_years != 1 ? 's' : '' ?></span></td>
                            </tr>
                            <tr>
                                <th>Status</th>
                                <td>
                                    <?php if ($course->is_active): ?>
                                        <span class="status-active"><span class="status-dot"></span>Ativo</span>
                                    <?php else: ?>
                                        <span class="status-inactive"><span class="status-dot"></span>Inativo</span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        </table>
                    </div>

                    <!-- Right info column -->
                    <div class="col-md-6">
                        <table class="info-table">
                            <tr>
                                <th>Nível Inicial</th>
                                <td style="font-size:.875rem;font-weight:600;"><?= $course->start_level_name ?></td>
                            </tr>
                            <tr>
                                <th>Nível Final</th>
                                <td style="font-size:.875rem;font-weight:600;"><?= $course->end_level_name ?></td>
                            </tr>
                            <tr>
                                <th>Níveis</th>
                                <td>
                                    <?php
                                        $gradeLevelModel = new \App\Models\GradeLevelModel();
                                        $levels = $gradeLevelModel
                                            ->where('id >=', $course->start_grade_id)
                                            ->where('id <=', $course->end_grade_id)
                                            ->where('is_active', 1)
                                            ->orderBy('sort_order', 'ASC')
                                            ->findAll();
                                    ?>
                                    <div style="display:flex;flex-wrap:wrap;gap:.25rem;">
                                        <?php foreach ($levels as $level): ?>
                                            <span class="level-chip">
                                                <i class="fas fa-layer-group" style="font-size:.6rem;"></i>
                                                <?= $level->level_name ?>
                                            </span>
                                        <?php endforeach; ?>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>Disciplinas</th>
                                <td><span class="num-chip blue"><?= $totalDisciplines ?></span></td>
                            </tr>
                            <tr>
                                <th>Carga Horária</th>
                                <td><span class="num-chip green"><?= $totalWorkload ?>h</span></td>
                            </tr>
                        </table>
                    </div>

                    <!-- Description -->
                    <?php if ($course->description): ?>
                    <div class="col-12">
                        <hr class="info-divider">
                        <span class="desc-label">Descrição</span>
                        <p class="desc-text mb-0"><?= nl2br(esc($course->description)) ?></p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- ── RIGHT: Sidebar ──────────────────────────────── -->
    <div style="width:260px;flex-shrink:0;" class="view-sidebar">

        <!-- Quick Actions -->
        <div class="ci-card mb-3">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-bolt"></i> Ações Rápidas
                </div>
            </div>
            <div class="ci-card-body" style="display:flex;flex-direction:column;gap:.5rem;">
                <a href="<?= site_url('admin/courses/curriculum/' . $course->id) ?>" class="qa-btn primary">
                    <span class="qa-icon"><i class="fas fa-book-open"></i></span>
                    Gerir Currículo
                </a>
                <a href="<?= site_url('admin/courses/form-edit/' . $course->id) ?>" class="qa-btn secondary">
                    <span class="qa-icon"><i class="fas fa-edit"></i></span>
                    Editar Curso
                </a>
                <button type="button" class="qa-btn danger-outline"
                        onclick="confirmDelete(<?= $course->id ?>, '<?= esc($course->course_name, 'js') ?>')">
                    <span class="qa-icon"><i class="fas fa-trash"></i></span>
                    Eliminar Curso
                </button>
            </div>
        </div>

        <!-- Stats -->
        <div class="ci-card">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-chart-pie"></i> Estatísticas
                </div>
            </div>
            <div class="ci-card-body" style="padding-top:.75rem;padding-bottom:.75rem;">
                <?php
                    $classModel = new \App\Models\ClassModel();
                    $totalClasses = $classModel->where('course_id', $course->id)->countAllResults();
                    $enrollmentModel = new \App\Models\EnrollmentModel();
                    $totalEnrollments = $enrollmentModel->where('course_id', $course->id)->countAllResults();
                ?>
                <div class="stat-list-item">
                    <div class="stat-list-label">
                        <span class="stat-icon blue"><i class="fas fa-school"></i></span>
                        Turmas
                    </div>
                    <span class="num-chip blue"><?= $totalClasses ?></span>
                </div>
                <div class="stat-list-item">
                    <div class="stat-list-label">
                        <span class="stat-icon green"><i class="fas fa-user-graduate"></i></span>
                        Alunos Matriculados
                    </div>
                    <span class="num-chip green"><?= $totalEnrollments ?></span>
                </div>
                <div class="stat-list-item">
                    <div class="stat-list-label">
                        <span class="stat-icon purple"><i class="fas fa-book"></i></span>
                        Disciplinas
                    </div>
                    <span class="num-chip"><?= $totalDisciplines ?></span>
                </div>
                <div class="stat-list-item">
                    <div class="stat-list-label">
                        <span class="stat-icon orange"><i class="fas fa-clock"></i></span>
                        Carga Horária
                    </div>
                    <span class="num-chip orange"><?= $totalWorkload ?>h</span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- ── CURRICULUM ACCORDION ────────────────────────────── -->
<div class="ci-card">
    <div class="ci-card-header">
        <div class="ci-card-title">
            <i class="fas fa-book-open"></i>
            Currículo por Nível
        </div>
        <a href="<?= site_url('admin/courses/curriculum/' . $course->id) ?>"
           style="background:var(--accent);color:#fff;border:none;border-radius:var(--radius-sm);padding:.38rem .9rem;font-size:.78rem;font-weight:600;display:inline-flex;align-items:center;gap:.4rem;text-decoration:none;box-shadow:0 3px 8px rgba(59,127,232,.28);transition:all .18s;"
           onmouseover="this.style.background='var(--accent-hover)'"
           onmouseout="this.style.background='var(--accent)'">
            <i class="fas fa-cog"></i> Gerir Currículo
        </a>
    </div>
    <div class="ci-card-body" style="padding:0;">
        <?php if (!empty($groupedCurriculum)): ?>
            <div class="ci-accordion" id="curriculumAccordion">
                <?php $index = 0; foreach ($groupedCurriculum as $levelId => $group): ?>
                <?php
                    $levelWorkload = 0;
                    foreach ($group['disciplines'] as $d) $levelWorkload += ($d->workload_hours ?? $d->default_workload ?? 0);
                    $isOpen = ($index === 0);
                ?>
                <div class="ci-accordion-item">
                    <button class="ci-acc-btn <?= $isOpen ? 'open' : '' ?>"
                            onclick="toggleAccordion('acc-<?= $levelId ?>', this)">
                        <div class="ci-acc-btn-left">
                            <i class="fas fa-layer-group" style="font-size:.8rem;color:var(--accent);"></i>
                            <span class="ci-acc-level-name"><?= $group['level_name'] ?></span>
                            <span class="ci-acc-disc-count"><?= count($group['disciplines']) ?></span>
                        </div>
                        <div class="ci-acc-btn-right">
                            <span class="ci-acc-workload"><?= $levelWorkload ?>h total</span>
                            <span class="ci-acc-chevron"><i class="fas fa-chevron-right"></i></span>
                        </div>
                    </button>
                    <div class="ci-acc-body <?= $isOpen ? 'open' : '' ?>" id="acc-<?= $levelId ?>">
                        <div class="table-responsive">
                            <table class="curr-table">
                                <thead>
                                    <tr>
                                        <th>Código</th>
                                        <th>Disciplina</th>
                                        <th class="text-center">Carga Horária</th>
                                        <th class="text-center">Semestre</th>
                                        <th class="text-center">Obrigatória</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($group['disciplines'] as $discipline): ?>
                                    <tr>
                                        <td><span class="disc-code"><?= $discipline->discipline_code ?></span></td>
                                        <td style="font-weight:500;font-size:.875rem;"><?= $discipline->discipline_name ?></td>
                                        <td class="text-center">
                                            <span class="workload-chip">
                                                <?= $discipline->workload_hours ?: ($discipline->default_workload ?? 0) ?>h
                                            </span>
                                        </td>
                                        <td class="text-center">
                                            <?php
                                                $sem = $discipline->semester ?? 'Anual';
                                                $semCls = match((string)$sem) { '1' => 'sem-1', '2' => 'sem-2', 'Anual' => 'sem-anual', default => 'sem-other' };
                                                $semLbl = match((string)$sem) { '1' => '1º Trim.', '2' => '2º Trim.', default => $sem };
                                            ?>
                                            <span class="sem-badge <?= $semCls ?>"><?= $semLbl ?></span>
                                        </td>
                                        <td class="text-center">
                                            <?php if ($discipline->is_mandatory): ?>
                                                <i class="fas fa-check-circle mand-yes" title="Obrigatória"></i>
                                            <?php else: ?>
                                                <i class="fas fa-minus-circle mand-no" title="Opcional"></i>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                                <tfoot>
                                    <tr>
                                        <td colspan="2" style="color:var(--text-primary);font-weight:700;">Total do Nível</td>
                                        <td class="text-center">
                                            <span class="workload-chip total"><?= $levelWorkload ?>h</span>
                                        </td>
                                        <td colspan="2"></td>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </div>
                <?php $index++; endforeach; ?>
            </div>
        <?php else: ?>
            <div class="curr-empty">
                <i class="fas fa-book-open"></i>
                <strong>Nenhuma disciplina atribuída</strong>
                <p>Este curso ainda não tem disciplinas no currículo.</p>
                <a href="<?= site_url('admin/courses/curriculum/' . $course->id) ?>"
                   class="qa-btn primary" style="width:auto;display:inline-flex;">
                    <span class="qa-icon"><i class="fas fa-plus-circle"></i></span>
                    Adicionar Disciplinas
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- ── DELETE MODAL ────────────────────────────────────── -->
<div class="modal fade ci-modal" id="deleteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="fas fa-exclamation-triangle"></i> Confirmar Eliminação
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
            <div class="modal-footer">
                <button type="button" class="btn-cancel" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="confirmDeleteBtn" class="btn-danger-ci">
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

function toggleAccordion(bodyId, btn) {
    const body = document.getElementById(bodyId);
    const isOpen = body.classList.contains('open');

    // Close all
    document.querySelectorAll('.ci-acc-body').forEach(b => b.classList.remove('open'));
    document.querySelectorAll('.ci-acc-btn').forEach(b => b.classList.remove('open'));

    // Open clicked if it was closed
    if (!isOpen) {
        body.classList.add('open');
        btn.classList.add('open');
    }
}
</script>
<?= $this->endSection() ?>