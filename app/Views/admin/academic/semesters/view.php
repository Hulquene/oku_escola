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
.ci-page-header::before { content:''; position:absolute; top:-60px; right:-60px; width:200px; height:200px; border-radius:50%; background:rgba(255,255,255,.04); pointer-events:none; }
.ci-page-header::after  { content:''; position:absolute; bottom:-40px; right:100px; width:130px; height:130px; border-radius:50%; background:rgba(59,127,232,.15); pointer-events:none; }
.ci-page-header-inner   { display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:.75rem; position:relative; z-index:1; }
.ci-page-header h1 { font-size:1.4rem; font-weight:700; color:#fff; margin:0 0 .2rem; letter-spacing:-.3px; }
.ci-page-header .breadcrumb { margin:0; padding:0; background:transparent; position:relative; z-index:1; }
.ci-page-header .breadcrumb-item a { color:rgba(255,255,255,.6); text-decoration:none; font-size:.8rem; transition:color .2s; }
.ci-page-header .breadcrumb-item a:hover { color:#fff; }
.ci-page-header .breadcrumb-item.active,
.ci-page-header .breadcrumb-item + .breadcrumb-item::before { color:rgba(255,255,255,.4); font-size:.8rem; }
.hdr-btn { display:inline-flex; align-items:center; gap:.45rem; border-radius:var(--radius-sm); padding:.45rem 1rem; font-size:.82rem; font-weight:600; text-decoration:none; transition:all .18s; white-space:nowrap; cursor:pointer; border:none; font-family:'Sora',sans-serif; }
.hdr-btn.accent { background:var(--accent); color:#fff; box-shadow:0 3px 10px rgba(59,127,232,.28); }
.hdr-btn.accent:hover { background:var(--accent-hover); color:#fff; transform:translateY(-1px); }
.hdr-btn.outline { background:rgba(255,255,255,.12); border:1.5px solid rgba(255,255,255,.25); color:#fff; }
.hdr-btn.outline:hover { background:rgba(255,255,255,.22); color:#fff; }
.hdr-btn.disabled { opacity:.45; cursor:not-allowed; pointer-events:none; background:rgba(255,255,255,.08); border:1.5px solid rgba(255,255,255,.15); color:#fff; }

/* ── STAT CARDS ──────────────────────────────────────── */
.stat-row { display:grid; grid-template-columns:repeat(3,1fr); gap:1rem; margin-bottom:1.25rem; }
.stat-card-sm {
    background:var(--surface-card); border:1px solid var(--border);
    border-radius:var(--radius); padding:1.1rem 1.25rem;
    box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:1rem;
    transition:box-shadow .18s, transform .18s;
}
.stat-card-sm:hover { box-shadow:var(--shadow-md); transform:translateY(-2px); }
.stat-icon-box { width:44px; height:44px; border-radius:11px; display:flex; align-items:center; justify-content:center; font-size:1.1rem; flex-shrink:0; }
.sib-blue   { background:rgba(59,127,232,.1);  color:var(--accent); }
.sib-green  { background:rgba(22,168,125,.1);  color:var(--success); }
.sib-amber  { background:rgba(232,160,32,.1);  color:#B07800; }
.stat-sm-val { font-family:'JetBrains Mono',monospace; font-size:1.5rem; font-weight:700; color:var(--text-primary); line-height:1.1; }
.stat-sm-label { font-size:.7rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:.07em; font-weight:600; margin-top:.1rem; }

/* ── CARDS ───────────────────────────────────────────── */
.ci-card { background:var(--surface-card); border:1px solid var(--border); border-radius:var(--radius); box-shadow:var(--shadow-sm); overflow:hidden; margin-bottom:1.25rem; }
.ci-card-header { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.5rem; padding:.85rem 1.25rem; background:var(--surface); border-bottom:1px solid var(--border); }
.ci-card-title  { display:flex; align-items:center; gap:.55rem; font-size:.82rem; font-weight:700; color:var(--text-primary); }
.ci-card-title i { color:var(--accent); font-size:.8rem; }
.ci-card-body   { padding:1.25rem; }

/* ── INFO TABLE ──────────────────────────────────────── */
.info-table { width:100%; }
.info-table tr { border-bottom:1px solid var(--border); }
.info-table tr:last-child { border-bottom:none; }
.info-table th { padding:.65rem 1rem .65rem 0; font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--text-muted); width:38%; white-space:nowrap; }
.info-table td { padding:.65rem 0; font-size:.875rem; color:var(--text-primary); font-weight:500; }

/* Type badge */
.type-badge { font-size:.68rem; font-weight:700; padding:.22rem .6rem; border-radius:50px; text-transform:uppercase; letter-spacing:.04em; background:rgba(59,127,232,.1); color:var(--accent); }

/* Status badges */
.status-badge { font-size:.65rem; font-weight:700; padding:.22rem .6rem; border-radius:50px; text-transform:uppercase; letter-spacing:.05em; display:inline-flex; align-items:center; gap:.25rem; }
.status-badge .sd { width:5px; height:5px; border-radius:50%; background:currentColor; flex-shrink:0; }
.sb-ativo      { color:var(--success); background:rgba(22,168,125,.1); }
.sb-inativo    { color:var(--text-muted); background:rgba(107,122,153,.1); }
.sb-processado { color:#8A5D00; background:rgba(232,160,32,.1); }
.sb-concluido  { color:var(--primary); background:rgba(27,43,75,.08); }
.sb-current    { color:var(--accent); background:rgba(59,127,232,.1); }

/* Period row */
.period-row { display:flex; align-items:center; gap:.6rem; font-size:.875rem; font-weight:600; }
.period-date { font-family:'JetBrains Mono',monospace; font-size:.82rem; }
.period-arrow { color:var(--text-muted); font-size:.7rem; }

/* Code tag */
.id-tag { font-family:'JetBrains Mono',monospace; font-size:.73rem; font-weight:600; background:rgba(27,43,75,.07); color:var(--primary); padding:.18rem .5rem; border-radius:5px; }

/* ── EXAMS TABLE ─────────────────────────────────────── */
.ci-table { width:100%; border-collapse:separate; border-spacing:0; }
.ci-table thead tr th { background:var(--surface); color:var(--text-secondary); font-size:.65rem; font-weight:700; text-transform:uppercase; letter-spacing:.09em; padding:.65rem 1rem; border-bottom:1.5px solid var(--border); white-space:nowrap; }
.ci-table tbody tr { border-bottom:1px solid var(--border); transition:background .12s; }
.ci-table tbody tr:last-child { border-bottom:none; }
.ci-table tbody tr:hover { background:#F5F8FF; }
.ci-table tbody td { padding:.65rem 1rem; vertical-align:middle; font-size:.82rem; border:none; }
.ci-table tbody td.center { text-align:center; }

.exam-badge { font-size:.65rem; font-weight:700; padding:.2rem .55rem; border-radius:50px; display:inline-block; text-transform:uppercase; letter-spacing:.04em; }
.eb-today    { background:rgba(22,168,125,.1);  color:var(--success); }
.eb-past     { background:rgba(107,122,153,.1); color:var(--text-secondary); }
.eb-upcoming { background:rgba(59,127,232,.1);  color:var(--accent); }
.exam-type-badge { font-size:.65rem; font-weight:700; padding:.2rem .5rem; border-radius:5px; background:rgba(59,127,232,.08); color:var(--accent); }

.ci-table-empty { text-align:center; padding:3rem 1.5rem; color:var(--text-muted); }
.ci-table-empty i { font-size:2rem; opacity:.18; display:block; margin-bottom:.6rem; }
.ci-table-empty p { font-size:.82rem; margin:0; }

/* ── LOGS TABLE ──────────────────────────────────────── */
.log-row { display:flex; align-items:flex-start; gap:.75rem; padding:.6rem 0; border-bottom:1px solid var(--border); }
.log-row:last-child { border-bottom:none; }
.log-dot { width:8px; height:8px; border-radius:50%; background:var(--accent); margin-top:.3rem; flex-shrink:0; }
.log-time { font-family:'JetBrains Mono',monospace; font-size:.72rem; color:var(--text-muted); white-space:nowrap; flex-shrink:0; min-width:110px; }
.log-user { font-size:.78rem; font-weight:600; color:var(--text-secondary); }
.log-action { font-size:.82rem; color:var(--text-primary); flex:1; }

/* Alerts */
.alert { border-radius:var(--radius-sm); border:none; font-size:.875rem; }
.alert-success { background:rgba(22,168,125,.1); color:#0E7A5A; border-left:3px solid var(--success); }
.alert-danger  { background:rgba(232,70,70,.08); color:#B03030; border-left:3px solid var(--danger); }

/* Responsive */
@media (max-width:767px) {
    .ci-page-header { padding:1.1rem 1.2rem; }
    .ci-page-header h1 { font-size:1.1rem; }
    .stat-row { grid-template-columns:1fr 1fr; }
    .ci-card-body { padding:1rem; }
}
@media (max-width:575px) {
    .stat-row { grid-template-columns:1fr; }
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
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/academic/semesters') ?>">Semestres/Trimestres</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Detalhes</li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <?php if (!isset($semester->has_results) || $semester->has_results == 0): ?>
                <a href="<?= site_url('admin/academic/semesters/form-edit/' . $semester['id']) ?>" class="hdr-btn accent">
                    <i class="fas fa-edit"></i> Editar
                </a>
            <?php else: ?>
                <span class="hdr-btn disabled" title="Não pode editar (já processado)">
                    <i class="fas fa-edit"></i> Editar
                </span>
            <?php endif; ?>
            <a href="<?= site_url('admin/academic/semesters') ?>" class="hdr-btn outline">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
</div>

<?= view('admin/partials/alerts') ?>

<!-- ── STAT CARDS ──────────────────────────────────────── -->
<div class="stat-row">
    <div class="stat-card-sm">
        <div class="stat-icon-box sib-blue"><i class="fas fa-pencil-alt"></i></div>
        <div>
            <div class="stat-sm-val"><?= $total_exams ?? 0 ?></div>
            <div class="stat-sm-label">Total de Exames</div>
        </div>
    </div>
    <div class="stat-card-sm">
        <div class="stat-icon-box sib-green"><i class="fas fa-check-double"></i></div>
        <div>
            <div class="stat-sm-val"><?= $total_results ?? 0 ?></div>
            <div class="stat-sm-label">Resultados Processados</div>
        </div>
    </div>
    <div class="stat-card-sm">
        <div class="stat-icon-box sib-amber"><i class="fas fa-info-circle"></i></div>
        <div>
            <?php
            $stMap = [
                'ativo'      => ['cls'=>'sb-ativo',      'text'=>'Ativo'],
                'inativo'    => ['cls'=>'sb-inativo',    'text'=>'Inativo'],
                'processado' => ['cls'=>'sb-processado', 'text'=>'Processado'],
                'concluido'  => ['cls'=>'sb-concluido',  'text'=>'Concluído'],
            ];
            $st = $semester->status ?? 'ativo';
            $b  = $stMap[$st] ?? ['cls'=>'sb-inativo','text'=>$st];
            ?>
            <div style="display:flex;gap:.4rem;flex-wrap:wrap;align-items:center;">
                <span class="status-badge <?= $b['cls'] ?>"><span class="sd"></span><?= $b['text'] ?></span>
                <?php if ($semester->is_current): ?>
                    <span class="status-badge sb-current"><i class="fas fa-star" style="font-size:.55rem;"></i>Atual</span>
                <?php endif; ?>
            </div>
            <div class="stat-sm-label" style="margin-top:.3rem;">Estado</div>
        </div>
    </div>
</div>

<!-- ── INFO GRID ───────────────────────────────────────── -->
<div class="row g-3">
    <div class="col-md-6">
        <div class="ci-card">
            <div class="ci-card-header">
                <div class="ci-card-title"><i class="fas fa-id-card"></i> Identificação</div>
            </div>
            <div class="ci-card-body">
                <table class="info-table">
                    <tr>
                        <th>ID</th>
                        <td><span class="id-tag"><?= $semester['id'] ?></span></td>
                    </tr>
                    <tr>
                        <th>Nome</th>
                        <td style="font-weight:700;"><?= esc($semester['semester_name']) ?></td>
                    </tr>
                    <tr>
                        <th>Tipo</th>
                        <td><span class="type-badge"><?= esc($semester->semester_type) ?></span></td>
                    </tr>
                    <tr>
                        <th>Ano Letivo</th>
                        <td><?= esc($semester->year_name) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="ci-card">
            <div class="ci-card-header">
                <div class="ci-card-title"><i class="fas fa-calendar-alt"></i> Período e Estado</div>
            </div>
            <div class="ci-card-body">
                <table class="info-table">
                    <tr>
                        <th>Período</th>
                        <td>
                            <div class="period-row">
                                <span class="period-date"><?= date('d/m/Y', strtotime($semester->start_date)) ?></span>
                                <i class="fas fa-arrow-right period-arrow"></i>
                                <span class="period-date"><?= date('d/m/Y', strtotime($semester->end_date)) ?></span>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>Duração</th>
                        <td>
                            <?php
                            $days = (int) ceil((strtotime($semester->end_date) - strtotime($semester->start_date)) / 86400) + 1;
                            ?>
                            <span style="font-family:'JetBrains Mono',monospace;font-weight:700;color:var(--accent);"><?= $days ?></span>
                            <span style="font-size:.8rem;color:var(--text-muted);"> dias</span>
                        </td>
                    </tr>
                    <tr>
                        <th>Status</th>
                        <td>
                            <div style="display:flex;gap:.4rem;flex-wrap:wrap;">
                                <span class="status-badge <?= $b['cls'] ?>"><span class="sd"></span><?= $b['text'] ?></span>
                                <?php if ($semester->is_current): ?>
                                    <span class="status-badge sb-current"><i class="fas fa-star" style="font-size:.55rem;"></i>Atual</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <tr>
                        <th>Criado em</th>
                        <td style="font-size:.8rem;color:var(--text-secondary);"><?= date('d/m/Y H:i', strtotime($semester->created_at)) ?></td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- ── EXAMS TABLE ─────────────────────────────────────── -->
<div class="ci-card">
    <div class="ci-card-header">
        <div class="ci-card-title"><i class="fas fa-pencil-alt"></i> Exames neste Período</div>
        <?php if (!empty($exams)): ?>
            <span style="font-family:'JetBrains Mono',monospace;font-size:.7rem;font-weight:700;padding:.18rem .6rem;border-radius:50px;background:rgba(59,127,232,.1);color:var(--accent);">
                <?= count($exams) ?>
            </span>
        <?php endif; ?>
    </div>
    <div style="overflow-x:auto;">
        <?php if (!empty($exams)): ?>
        <table class="ci-table">
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Hora</th>
                    <th>Exame</th>
                    <th>Tipo</th>
                    <th>Turma</th>
                    <th>Disciplina</th>
                    <th>Sala</th>
                    <th class="center">Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($exams as $exam):
                    $today  = date('Y-m-d');
                    $isToday = $exam['exam_date'] == $today;
                    $isPast  = $exam['exam_date'] <  $today;
                    $ebClass = $isToday ? 'eb-today' : ($isPast ? 'eb-past' : 'eb-upcoming');
                    $ebText  = $isToday ? 'Hoje' : ($isPast ? 'Realizado' : 'Agendado');
                ?>
                <tr>
                    <td style="font-family:'JetBrains Mono',monospace;font-weight:600;font-size:.78rem;"><?= date('d/m/Y', strtotime($exam['exam_date'])) ?></td>
                    <td style="font-family:'JetBrains Mono',monospace;font-size:.78rem;color:var(--text-secondary);"><?= $exam['exam_time'] ? date('H:i', strtotime($exam['exam_time'])) : '—' ?></td>
                    <td style="font-weight:600;"><?= esc($exam->board_name ?? 'Exame') ?></td>
                    <td><span class="exam-type-badge"><?= esc($exam['board_type'] ?? 'Normal') ?></span></td>
                    <td><?= esc($exam['class_name']) ?></td>
                    <td><?= esc($exam['discipline_name']) ?></td>
                    <td style="color:var(--text-muted);font-size:.8rem;"><?= esc($exam['exam_room'] ?: '—') ?></td>
                    <td class="center"><span class="exam-badge <?= $ebClass ?>"><?= $ebText ?></span></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
        <div class="ci-table-empty">
            <i class="fas fa-calendar-times"></i>
            <p>Nenhum exame agendado para este período.</p>
        </div>
        <?php endif; ?>
    </div>
</div>


<?= $this->endSection() ?>