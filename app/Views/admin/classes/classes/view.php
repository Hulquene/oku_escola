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

.hdr-btns { display:flex; gap:.5rem; flex-wrap:wrap; }
.hdr-btn { display:inline-flex; align-items:center; gap:.45rem; border-radius:var(--radius-sm); padding:.45rem 1rem; font-size:.82rem; font-weight:600; text-decoration:none; transition:all .18s; cursor:pointer; border:none; font-family:'Sora',sans-serif; white-space:nowrap; }
.hdr-btn.primary  { background:var(--accent); color:#fff; box-shadow:0 3px 10px rgba(59,127,232,.28); }
.hdr-btn.primary:hover  { background:var(--accent-hover); color:#fff; transform:translateY(-1px); }
.hdr-btn.success  { background:var(--success); color:#fff; box-shadow:0 3px 10px rgba(22,168,125,.25); }
.hdr-btn.success:hover  { background:#12936d; color:#fff; transform:translateY(-1px); }
.hdr-btn.ghost    { background:rgba(255,255,255,.12); color:#fff; border:1.5px solid rgba(255,255,255,.2); }
.hdr-btn.ghost:hover    { background:rgba(255,255,255,.22); color:#fff; }

/* ── STAT CARDS ──────────────────────────────────────────── */
.stat-row { display:grid; grid-template-columns:repeat(4,1fr); gap:.85rem; margin-bottom:1.25rem; }
@media(max-width:900px){ .stat-row { grid-template-columns:repeat(2,1fr); } }
@media(max-width:500px){ .stat-row { grid-template-columns:1fr 1fr; } }

.stat-card {
    background:var(--surface-card); border:1px solid var(--border);
    border-radius:var(--radius); padding:1rem 1.1rem;
    display:flex; align-items:center; gap:.85rem;
    box-shadow:var(--shadow-sm); transition:box-shadow .18s;
}
.stat-card:hover { box-shadow:var(--shadow-md); }
.stat-card-icon {
    width:42px; height:42px; border-radius:10px; flex-shrink:0;
    display:flex; align-items:center; justify-content:center; font-size:.95rem;
}
.sci-blue   { background:rgba(59,127,232,.1);  color:var(--accent); }
.sci-green  { background:rgba(22,168,125,.1);  color:var(--success); }
.sci-amber  { background:rgba(232,160,32,.1);  color:var(--warning); }
.sci-navy   { background:rgba(27,43,75,.1);    color:var(--primary); }
.sci-red    { background:rgba(232,70,70,.1);   color:var(--danger); }
.stat-card-val  { font-size:1.5rem; font-weight:700; color:var(--text-primary); line-height:1; font-family:'JetBrains Mono',monospace; }
.stat-card-lbl  { font-size:.69rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:.06em; margin-top:.15rem; }

/* ── CARD ────────────────────────────────────────────────── */
.ci-card { background:var(--surface-card); border:1px solid var(--border); border-radius:var(--radius); box-shadow:var(--shadow-sm); overflow:hidden; margin-bottom:1.25rem; }
.ci-card-header { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.5rem; padding:.85rem 1.25rem; background:var(--surface); border-bottom:1px solid var(--border); }
.ci-card-title  { display:flex; align-items:center; gap:.55rem; font-size:.82rem; font-weight:700; color:var(--text-primary); }
.ci-card-title i { color:var(--accent); font-size:.8rem; }
.ci-card-body { padding:1.1rem 1.25rem; }

/* ── INFO TABLE ──────────────────────────────────────────── */
.info-table { width:100%; border-collapse:collapse; }
.info-table tr { border-bottom:1px solid var(--border); }
.info-table tr:last-child { border-bottom:none; }
.info-table td { padding:.6rem .25rem; vertical-align:middle; }
.info-table td:first-child {
    font-size:.7rem; font-weight:700; text-transform:uppercase; letter-spacing:.06em;
    color:var(--text-muted); width:42%; padding-right:.75rem; white-space:nowrap;
}
.info-table td:last-child { font-size:.84rem; color:var(--text-primary); font-weight:500; }

/* ── CHIPS & BADGES ──────────────────────────────────────── */
.id-chip { font-family:'JetBrains Mono',monospace; font-size:.7rem; font-weight:600; background:rgba(27,43,75,.07); color:var(--primary); padding:.15rem .45rem; border-radius:5px; }
.code-badge { font-family:'JetBrains Mono',monospace; font-size:.72rem; font-weight:600; background:rgba(59,127,232,.1); color:var(--accent); padding:.18rem .55rem; border-radius:6px; border:1px solid rgba(59,127,232,.18); }
.shift-badge { display:inline-flex; align-items:center; gap:.3rem; font-size:.72rem; font-weight:700; padding:.2rem .65rem; border-radius:50px; }
.shift-manha  { background:rgba(59,127,232,.1);  color:var(--accent); }
.shift-tarde  { background:rgba(232,160,32,.12); color:var(--warning); }
.shift-noite  { background:rgba(27,43,75,.12);   color:var(--primary); }
.shift-integral { background:rgba(22,168,125,.1); color:var(--success); }

.status-dot { display:inline-flex; align-items:center; gap:.35rem; font-size:.74rem; font-weight:700; }
.sd { width:7px; height:7px; border-radius:50%; flex-shrink:0; }
.sd-active   { background:var(--success); box-shadow:0 0 0 2px rgba(22,168,125,.2); }
.sd-inactive { background:var(--text-muted); }
.st-active   { color:var(--success); }
.st-inactive { color:var(--text-muted); }

.seats-full { color:var(--danger); font-weight:700; }
.seats-ok   { color:var(--success); font-weight:700; }

/* Teacher chip */
.teacher-chip { display:inline-flex; align-items:center; gap:.5rem; }
.teacher-avatar {
    width:26px; height:26px; border-radius:7px;
    background:rgba(59,127,232,.12); color:var(--accent);
    display:flex; align-items:center; justify-content:center; font-size:.65rem; flex-shrink:0;
}
.teacher-name { font-size:.83rem; font-weight:600; color:var(--text-primary); }
.teacher-email { font-size:.7rem; color:var(--text-muted); }
.no-teacher { font-size:.78rem; color:var(--text-muted); font-style:italic; }

/* ── DISCIPLINES TABLE ───────────────────────────────────── */
.ci-table { width:100%; border-collapse:separate; border-spacing:0; }
.ci-table thead tr th { background:var(--surface); color:var(--text-secondary); font-size:.63rem; font-weight:700; text-transform:uppercase; letter-spacing:.09em; padding:.6rem 1rem; border-bottom:1.5px solid var(--border); white-space:nowrap; }
.ci-table tbody tr { transition:background .12s; }
.ci-table tbody tr:not(:last-child) td { border-bottom:1px solid var(--border); }
.ci-table tbody tr:hover { background:#F5F8FF; }
.ci-table tbody td { padding:.65rem 1rem; vertical-align:middle; font-size:.82rem; }

.disc-name { font-weight:600; color:var(--text-primary); font-size:.82rem; }
.workload-chip { font-family:'JetBrains Mono',monospace; font-size:.7rem; font-weight:600; color:var(--text-secondary); }

.row-btn { width:28px; height:28px; border-radius:7px; border:1.5px solid var(--border); background:#fff; color:var(--text-secondary); display:inline-flex; align-items:center; justify-content:center; font-size:.72rem; text-decoration:none; cursor:pointer; transition:all .18s; }
.row-btn:hover { color:#fff; border-color:transparent; }
.row-btn.manage:hover { background:var(--accent); }

.ci-empty { text-align:center; padding:2.5rem; color:var(--text-muted); }
.ci-empty i { font-size:1.8rem; opacity:.12; display:block; margin-bottom:.5rem; }
.ci-empty p { font-size:.8rem; margin:0; }

/* ── CAPACITY BAR ────────────────────────────────────────── */
.cap-bar-wrap { margin-top:.15rem; }
.cap-bar-track { height:6px; background:var(--border); border-radius:3px; overflow:hidden; width:100%; }
.cap-bar-fill  { height:100%; border-radius:3px; transition:width .3s; }
.cap-pct { font-family:'JetBrains Mono',monospace; font-size:.68rem; color:var(--text-muted); margin-top:.2rem; }

/* ── SECTION DIVIDER ─────────────────────────────────────── */
.sec-divider { display:flex; align-items:center; gap:.55rem; margin:1.1rem 0 .8rem; }
.sec-divider-label { font-size:.62rem; font-weight:700; text-transform:uppercase; letter-spacing:.1em; color:var(--text-muted); white-space:nowrap; }
.sec-divider-label i { color:var(--accent); margin-right:.2rem; font-size:.6rem; }
.sec-divider-line { flex:1; height:1px; background:var(--border); }

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
            <h1>
                <i class="fas fa-school me-2" style="opacity:.7;font-size:1.1rem;"></i>
                <?= esc($class->class_name) ?>
                <span class="code-badge ms-2" style="font-size:.75rem;vertical-align:middle;"><?= esc($class->class_code) ?></span>
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/classes/classes') ?>">Turmas</a></li>
                    <li class="breadcrumb-item active">Detalhes</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-btns">
            <a href="<?= site_url('admin/classes/classes/form-edit/' . $class->id) ?>" class="hdr-btn success">
                <i class="fas fa-edit"></i> Editar
            </a>
            <a href="<?= site_url('admin/classes/classes/list-students/' . $class->id) ?>" class="hdr-btn primary">
                <i class="fas fa-users"></i> Ver Alunos
            </a>
            <a href="<?= site_url('admin/classes/classes') ?>" class="hdr-btn ghost">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
</div>

<?= view('admin/partials/alerts') ?>

<?php
/* ── Pre-compute values ── */
$enrolled      = count($students ?? []);
$totalDisc     = count($disciplines ?? []);
$capacity      = (int)($class->capacity ?? 0);
$capPct        = $capacity > 0 ? min(100, round($enrolled / $capacity * 100)) : 0;
$capColor      = $capPct >= 100 ? 'var(--danger)' : ($capPct >= 75 ? 'var(--warning)' : 'var(--success)');

$shiftMap  = ['Manhã'=>'shift-manha','Tarde'=>'shift-tarde','Noite'=>'shift-noite','Integral'=>'shift-integral'];
$shiftClass = $shiftMap[$class->class_shift] ?? 'shift-manha';
$shiftIcon  = ['Manhã'=>'fa-sun','Tarde'=>'fa-cloud-sun','Noite'=>'fa-moon','Integral'=>'fa-clock'][$class->class_shift] ?? 'fa-clock';
?>

<!-- ── STAT CARDS ──────────────────────────────────────────── -->
<div class="stat-row">
    <div class="stat-card">
        <div class="stat-card-icon sci-blue"><i class="fas fa-users"></i></div>
        <div>
            <div class="stat-card-val"><?= $enrolled ?></div>
            <div class="stat-card-lbl">Alunos Matriculados</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon sci-green"><i class="fas fa-book"></i></div>
        <div>
            <div class="stat-card-val"><?= $totalDisc ?></div>
            <div class="stat-card-lbl">Disciplinas</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon <?= $availableSeats > 0 ? 'sci-green' : 'sci-red' ?>">
            <i class="fas fa-chair"></i>
        </div>
        <div>
            <div class="stat-card-val"><?= $availableSeats ?></div>
            <div class="stat-card-lbl">Vagas Disponíveis</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-card-icon sci-navy"><i class="fas fa-users-cog"></i></div>
        <div>
            <div class="stat-card-val"><?= $capacity ?></div>
            <div class="stat-card-lbl">Capacidade Total</div>
        </div>
    </div>
</div>

<!-- ── MAIN ROW ────────────────────────────────────────────── -->
<div class="row g-3">

    <!-- LEFT: Class Info -->
    <div class="col-lg-5">
        <div class="ci-card">
            <div class="ci-card-header">
                <div class="ci-card-title"><i class="fas fa-info-circle"></i> Informações da Turma</div>
                <?php if ($class->is_active): ?>
                    <span class="status-dot"><span class="sd sd-active"></span><span class="st-active">Ativa</span></span>
                <?php else: ?>
                    <span class="status-dot"><span class="sd sd-inactive"></span><span class="st-inactive">Inativa</span></span>
                <?php endif; ?>
            </div>
            <div class="ci-card-body">
                <table class="info-table">
                    <tr>
                        <td>ID</td>
                        <td><span class="id-chip">#<?= $class->id ?></span></td>
                    </tr>
                    <tr>
                        <td>Nome</td>
                        <td style="font-weight:700;"><?= esc($class->class_name) ?></td>
                    </tr>
                    <tr>
                        <td>Código</td>
                        <td><span class="code-badge"><?= esc($class->class_code) ?></span></td>
                    </tr>
                    <tr>
                        <td>Nível de Ensino</td>
                        <td><?= esc($class->level_name) ?></td>
                    </tr>
                    <?php if (!empty($class->course_name)): ?>
                    <tr>
                        <td>Curso</td>
                        <td><?= esc($class->course_name) ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td>Ano Letivo</td>
                        <td><?= esc($class->year_name) ?></td>
                    </tr>
                    <tr>
                        <td>Turno</td>
                        <td>
                            <span class="shift-badge <?= $shiftClass ?>">
                                <i class="fas <?= $shiftIcon ?>"></i>
                                <?= esc($class->class_shift) ?>
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>Sala</td>
                        <td><?= $class->class_room ? esc($class->class_room) : '<span style="color:var(--text-muted)">—</span>' ?></td>
                    </tr>
                    <tr>
                        <td>Ocupação</td>
                        <td>
                            <div><?= $enrolled ?> / <?= $capacity ?> alunos</div>
                            <div class="cap-bar-wrap">
                                <div class="cap-bar-track">
                                    <div class="cap-bar-fill" style="width:<?= $capPct ?>%;background:<?= $capColor ?>;"></div>
                                </div>
                                <div class="cap-pct"><?= $capPct ?>% ocupado
                                    <?php if ($availableSeats <= 0): ?>
                                        · <span style="color:var(--danger);font-weight:700;">Lotada</span>
                                    <?php else: ?>
                                        · <span style="color:var(--success);font-weight:700;"><?= $availableSeats ?> vagas</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <td>Professor Responsável</td>
                        <td>
                            <?php if ($class->class_teacher_id): ?>
                            <div class="teacher-chip">
                                <div class="teacher-avatar"><i class="fas fa-chalkboard-teacher"></i></div>
                                <div>
                                    <div class="teacher-name"><?= esc($class->teacher_first_name) ?> <?= esc($class->teacher_last_name) ?></div>
                                    <div class="teacher-email"><?= esc($class->teacher_email) ?></div>
                                </div>
                            </div>
                            <?php else: ?>
                            <span class="no-teacher"><i class="fas fa-user-slash me-1" style="opacity:.4;"></i>Não atribuído</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <tr>
                        <td>Criado em</td>
                        <td style="font-family:'JetBrains Mono',monospace;font-size:.75rem;color:var(--text-muted);">
                            <?= date('d/m/Y H:i', strtotime($class->created_at)) ?>
                        </td>
                    </tr>
                </table>

                <div class="sec-divider mt-3">
                    <span class="sec-divider-label"><i class="fas fa-bolt"></i>Ações Rápidas</span>
                    <div class="sec-divider-line"></div>
                </div>
                <div style="display:flex;gap:.5rem;flex-wrap:wrap;">
                    <a href="<?= site_url('admin/classes/classes/form-edit/' . $class->id) ?>"
                       class="hdr-btn success" style="flex:1;justify-content:center;background:rgba(22,168,125,.1);color:var(--success);box-shadow:none;border:1.5px solid rgba(22,168,125,.2);">
                        <i class="fas fa-edit"></i> Editar
                    </a>
                    <a href="<?= site_url('admin/classes/classes/list-students/' . $class->id) ?>"
                       class="hdr-btn primary" style="flex:1;justify-content:center;background:rgba(59,127,232,.1);color:var(--accent);box-shadow:none;border:1.5px solid rgba(59,127,232,.2);">
                        <i class="fas fa-users"></i> Alunos
                    </a>
                    <a href="<?= site_url('admin/classes/class-subjects?class=' . $class->id) ?>"
                       class="hdr-btn" style="flex:1;justify-content:center;background:rgba(232,160,32,.1);color:var(--warning);border:1.5px solid rgba(232,160,32,.2);box-shadow:none;">
                        <i class="fas fa-book"></i> Disciplinas
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT: Disciplines + Schedule -->
    <div class="col-lg-7">

        <!-- Disciplines -->
        <div class="ci-card">
            <div class="ci-card-header">
                <div class="ci-card-title"><i class="fas fa-book"></i> Disciplinas da Turma</div>
                <a href="<?= site_url('admin/classes/class-subjects?class=' . $class->id) ?>"
                   class="row-btn manage" title="Gerir Disciplinas" style="width:auto;padding:0 .65rem;font-size:.72rem;gap:.3rem;color:var(--text-secondary);">
                    <i class="fas fa-cog"></i> Gerir
                </a>
            </div>

            <?php
            $disciplineModel = new \App\Models\DisciplineModel();
            $disciplines     = $disciplineModel->getByClass($class->id);
            ?>

            <?php if (!empty($disciplines)): ?>
            <div style="overflow-x:auto;">
                <table class="ci-table">
                    <thead>
                        <tr>
                            <th>Disciplina</th>
                            <th>Código</th>
                            <th>Professor</th>
                            <th style="text-align:center;">Carga</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($disciplines as $disc): ?>
                        <tr>
                            <td>
                                <span class="disc-name"><?= esc($disc->discipline_name) ?></span>
                            </td>
                            <td>
                                <span class="code-badge"><?= esc($disc->discipline_code) ?></span>
                            </td>
                            <td>
                                <?php if (!empty($disc->teacher_id)): ?>
                                <?php
                                    $teacherModel = new \App\Models\UserModel();
                                    $teacher = $teacherModel->find($disc->teacher_id);
                                ?>
                                <?php if ($teacher): ?>
                                <div class="teacher-chip">
                                    <div class="teacher-avatar"><i class="fas fa-chalkboard-teacher"></i></div>
                                    <span class="teacher-name"><?= esc($teacher->first_name) ?> <?= esc($teacher->last_name) ?></span>
                                </div>
                                <?php else: ?>
                                <span class="no-teacher">—</span>
                                <?php endif; ?>
                                <?php else: ?>
                                <span class="no-teacher"><i class="fas fa-user-slash me-1" style="opacity:.35;"></i>Não atribuído</span>
                                <?php endif; ?>
                            </td>
                            <td style="text-align:center;">
                                <span class="workload-chip">
                                    <?= $disc->workload_hours ? esc($disc->workload_hours).'h' : '—' ?>
                                </span>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="ci-empty">
                <i class="fas fa-book-open"></i>
                <p>Nenhuma disciplina atribuída a esta turma.</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Capacity Visual -->
        <div class="ci-card">
            <div class="ci-card-header">
                <div class="ci-card-title"><i class="fas fa-chart-pie"></i> Ocupação da Turma</div>
                <span style="font-family:'JetBrains Mono',monospace;font-size:.72rem;color:var(--text-muted);">
                    <?= $enrolled ?> / <?= $capacity ?> alunos
                </span>
            </div>
            <div class="ci-card-body">
                <div style="display:flex;align-items:center;gap:1.5rem;flex-wrap:wrap;">

                    <!-- Donut SVG -->
                    <div style="position:relative;flex-shrink:0;">
                        <?php
                        $r   = 44;
                        $cx  = 54; $cy = 54;
                        $circ = 2 * M_PI * $r;
                        $dash = round($capPct / 100 * $circ, 2);
                        $gap  = round($circ - $dash, 2);
                        ?>
                        <svg width="108" height="108" viewBox="0 0 108 108">
                            <circle cx="<?= $cx ?>" cy="<?= $cy ?>" r="<?= $r ?>"
                                    fill="none" stroke="var(--border)" stroke-width="10"/>
                            <circle cx="<?= $cx ?>" cy="<?= $cy ?>" r="<?= $r ?>"
                                    fill="none" stroke="<?= $capColor ?>" stroke-width="10"
                                    stroke-dasharray="<?= $dash ?> <?= $gap ?>"
                                    stroke-dashoffset="<?= round($circ / 4, 2) ?>"
                                    stroke-linecap="round"/>
                        </svg>
                        <div style="position:absolute;inset:0;display:flex;flex-direction:column;align-items:center;justify-content:center;">
                            <span style="font-family:'JetBrains Mono',monospace;font-size:1.1rem;font-weight:700;color:var(--text-primary);line-height:1;"><?= $capPct ?>%</span>
                            <span style="font-size:.6rem;color:var(--text-muted);letter-spacing:.05em;text-transform:uppercase;">ocupado</span>
                        </div>
                    </div>

                    <!-- Legend -->
                    <div style="flex:1;min-width:160px;">
                        <div style="display:flex;flex-direction:column;gap:.6rem;">
                            <div style="display:flex;align-items:center;justify-content:space-between;gap:.5rem;">
                                <div style="display:flex;align-items:center;gap:.5rem;">
                                    <span style="width:10px;height:10px;border-radius:2px;background:var(--accent);flex-shrink:0;"></span>
                                    <span style="font-size:.78rem;color:var(--text-secondary);">Matriculados</span>
                                </div>
                                <span style="font-family:'JetBrains Mono',monospace;font-size:.78rem;font-weight:700;color:var(--text-primary);"><?= $enrolled ?></span>
                            </div>
                            <div style="display:flex;align-items:center;justify-content:space-between;gap:.5rem;">
                                <div style="display:flex;align-items:center;gap:.5rem;">
                                    <span style="width:10px;height:10px;border-radius:2px;background:var(--success);flex-shrink:0;"></span>
                                    <span style="font-size:.78rem;color:var(--text-secondary);">Vagas Livres</span>
                                </div>
                                <span style="font-family:'JetBrains Mono',monospace;font-size:.78rem;font-weight:700;color:var(--success);"><?= $availableSeats ?></span>
                            </div>
                            <div style="display:flex;align-items:center;justify-content:space-between;gap:.5rem;">
                                <div style="display:flex;align-items:center;gap:.5rem;">
                                    <span style="width:10px;height:10px;border-radius:2px;background:var(--border);flex-shrink:0;"></span>
                                    <span style="font-size:.78rem;color:var(--text-secondary);">Capacidade</span>
                                </div>
                                <span style="font-family:'JetBrains Mono',monospace;font-size:.78rem;font-weight:700;color:var(--text-primary);"><?= $capacity ?></span>
                            </div>
                        </div>

                        <div class="cap-bar-track mt-3" style="height:8px;">
                            <div class="cap-bar-fill" style="width:<?= $capPct ?>%;background:<?= $capColor ?>;"></div>
                        </div>
                        <div style="font-size:.7rem;color:var(--text-muted);margin-top:.3rem;">
                            <?php if ($capPct >= 100): ?>
                                <span style="color:var(--danger);font-weight:700;"><i class="fas fa-exclamation-circle me-1"></i>Turma lotada</span>
                            <?php elseif ($capPct >= 75): ?>
                                <span style="color:var(--warning);font-weight:700;"><i class="fas fa-exclamation-triangle me-1"></i>Quase cheia (<?= $availableSeats ?> vaga<?= $availableSeats != 1 ? 's' : '' ?>)</span>
                            <?php else: ?>
                                <span style="color:var(--success);font-weight:700;"><i class="fas fa-check-circle me-1"></i><?= $availableSeats ?> vaga<?= $availableSeats != 1 ? 's' : '' ?> disponível<?= $availableSeats != 1 ? 'is' : '' ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                </div>
            </div>
        </div>

    </div>
</div>

<?= $this->endSection() ?>