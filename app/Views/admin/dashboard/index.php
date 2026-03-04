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
    --purple:        #7C4DFF;
    --teal:          #00BCD4;
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
.ci-page-header-inner {
    display: flex; align-items: flex-start; justify-content: space-between;
    flex-wrap: wrap; gap: 0.75rem; position: relative; z-index: 1;
}
.ci-page-header h1 {
    font-size: 1.4rem; font-weight: 700; color: #fff;
    margin: 0 0 0.2rem; letter-spacing: -0.3px;
}
.ci-datetime {
    display: flex; align-items: center; gap: 0.5rem;
    font-size: 0.8rem; color: rgba(255,255,255,0.65);
    background: rgba(255,255,255,0.08);
    border: 1px solid rgba(255,255,255,0.12);
    border-radius: var(--radius-sm);
    padding: 0.35rem 0.8rem;
    white-space: nowrap;
}
.ci-datetime i { font-size: 0.72rem; opacity: 0.7; }
.ci-datetime .sep { opacity: 0.3; }
.ci-page-header .breadcrumb { margin: 0; padding: 0; background: transparent; position: relative; z-index: 1; }
.ci-page-header .breadcrumb-item a { color: rgba(255,255,255,0.6); text-decoration: none; font-size: 0.8rem; transition: color .2s; }
.ci-page-header .breadcrumb-item a:hover { color: #fff; }
.ci-page-header .breadcrumb-item.active,
.ci-page-header .breadcrumb-item + .breadcrumb-item::before { color: rgba(255,255,255,0.4); font-size: 0.8rem; }

/* ── STAT CARDS ──────────────────────────────────────── */
.stat-card {
    border-radius: var(--radius);
    padding: 1.25rem 1.4rem;
    position: relative;
    overflow: hidden;
    transition: transform .22s, box-shadow .22s;
    box-shadow: var(--shadow-md);
    color: #fff;
    margin-bottom: 1.25rem;
    cursor: default;
}
.stat-card:hover { transform: translateY(-3px); box-shadow: var(--shadow-lg); }

/* Decorative watermark icon */
.stat-card .sc-watermark {
    position: absolute; right: -8px; bottom: -8px;
    font-size: 5rem; opacity: 0.08; pointer-events: none;
    line-height: 1;
}
/* Decorative circle */
.stat-card::before {
    content: ''; position: absolute; top: -30px; right: -30px;
    width: 100px; height: 100px; border-radius: 50%;
    background: rgba(255,255,255,0.06); pointer-events: none;
}

.sc-label {
    font-size: 0.68rem; font-weight: 700;
    text-transform: uppercase; letter-spacing: 0.09em;
    opacity: 0.82; margin-bottom: 0.4rem;
}
.sc-value {
    font-family: 'JetBrains Mono', monospace;
    font-size: 1.9rem; font-weight: 700;
    letter-spacing: -0.03em; line-height: 1.1;
    margin-bottom: 0.5rem;
}
.sc-sub {
    font-size: 0.73rem; opacity: 0.75;
    border-top: 1px solid rgba(255,255,255,0.18);
    padding-top: 0.5rem; margin-top: 0.1rem;
    display: flex; align-items: center; gap: 0.35rem; flex-wrap: wrap;
}
.sc-sub i { font-size: 0.68rem; }

/* Color variants */
.sc-blue    { background: linear-gradient(135deg, #2563EB 0%, #1B4FD8 100%); }
.sc-green   { background: linear-gradient(135deg, #059669 0%, #047857 100%); }
.sc-teal    { background: linear-gradient(135deg, #0891B2 0%, #0E7490 100%); }
.sc-amber   { background: linear-gradient(135deg, #D97706 0%, #B45309 100%); }
.sc-red     { background: linear-gradient(135deg, #DC2626 0%, #B91C1C 100%); }
.sc-orange  { background: linear-gradient(135deg, #EA580C 0%, #C2410C 100%); }
.sc-emerald { background: linear-gradient(135deg, #10B981 0%, #059669 100%); }
.sc-purple  { background: linear-gradient(135deg, #7C3AED 0%, #6D28D9 100%); }

/* ── CHART CARDS ─────────────────────────────────────── */
.ci-card {
    background: var(--surface-card);
    border: 1px solid var(--border);
    border-radius: var(--radius);
    box-shadow: var(--shadow-sm);
    overflow: hidden;
    margin-bottom: 1.25rem;
}
.ci-card-header {
    display: flex; align-items: center; justify-content: space-between;
    flex-wrap: wrap; gap: 0.5rem;
    padding: 0.85rem 1.25rem;
    background: var(--surface);
    border-bottom: 1px solid var(--border);
}
.ci-card-title {
    display: flex; align-items: center; gap: 0.55rem;
    font-size: 0.82rem; font-weight: 700; color: var(--text-primary);
}
.ci-card-title i { font-size: 0.8rem; }
.ci-card-badge {
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.68rem; font-weight: 700;
    padding: 0.18rem 0.6rem; border-radius: 50px;
    display: inline-block;
}
.ci-card-badge.blue   { background: rgba(59,127,232,0.1);  color: var(--accent); }
.ci-card-badge.green  { background: rgba(22,168,125,0.1);  color: var(--success); }
.ci-card-badge.amber  { background: rgba(232,160,32,0.1);  color: #B07800; }
.ci-card-badge.teal   { background: rgba(0,188,212,0.1);   color: #007B9A; }
.ci-card-body { padding: 1.25rem; }
.ci-card-body.p0 { padding: 0; }

/* Chart container */
.chart-wrap { position: relative; height: 280px; }

/* ── FINANCIAL SUMMARY ───────────────────────────────── */
.fin-total-label { font-size: 0.68rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: var(--text-muted); margin-bottom: 0.25rem; }
.fin-total-value {
    font-family: 'JetBrains Mono', monospace;
    font-size: 1.5rem; font-weight: 700; color: var(--accent);
    letter-spacing: -0.03em; line-height: 1.1; margin-bottom: 1rem;
}

/* Progress bar */
.ci-progress {
    width: 100%; height: 10px; border-radius: 50px;
    background: var(--border); overflow: hidden; margin-bottom: 0.5rem;
    display: flex;
}
.ci-progress-bar {
    height: 100%; border-radius: 50px 0 0 50px;
    transition: width .6s cubic-bezier(.4,0,.2,1);
}
.ci-progress-bar.paid    { background: var(--success); }
.ci-progress-bar.pending { background: var(--warning); border-radius: 0 50px 50px 0; }
.progress-legend { display: flex; gap: 1rem; margin-bottom: 1.25rem; }
.progress-legend-item { display: flex; align-items: center; gap: 0.35rem; font-size: 0.72rem; color: var(--text-secondary); }
.progress-legend-dot { width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0; }

/* Finance mini boxes */
.fin-mini-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 0.75rem; margin-bottom: 1rem; }
.fin-mini {
    text-align: center; padding: 0.85rem 0.5rem;
    border: 1px solid var(--border); border-radius: var(--radius-sm);
    background: var(--surface);
}
.fin-mini-icon { font-size: 1.1rem; margin-bottom: 0.35rem; }
.fin-mini-val {
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.82rem; font-weight: 700; color: var(--text-primary);
    line-height: 1.2; margin-bottom: 0.15rem;
}
.fin-mini-label { font-size: 0.65rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.06em; }

.btn-ci-primary {
    width: 100%; background: var(--accent); border: none; color: #fff;
    border-radius: var(--radius-sm); padding: 0.55rem 1rem;
    font-size: 0.85rem; font-weight: 700;
    display: flex; align-items: center; justify-content: center; gap: 0.45rem;
    text-decoration: none; transition: all .18s; font-family: 'Sora', sans-serif;
    box-shadow: 0 3px 10px rgba(59,127,232,0.28);
}
.btn-ci-primary:hover { background: var(--accent-hover); color: #fff; transform: translateY(-1px); }

.btn-ci-info, .btn-ci-success {
    width: 100%; border: none; color: #fff;
    border-radius: var(--radius-sm); padding: 0.45rem 1rem;
    font-size: 0.82rem; font-weight: 600;
    display: flex; align-items: center; justify-content: center; gap: 0.4rem;
    text-decoration: none; transition: all .18s; font-family: 'Sora', sans-serif;
    margin: 0.75rem 0.75rem 0.75rem;
    width: calc(100% - 1.5rem);
}
.btn-ci-info    { background: #0891B2; box-shadow: 0 2px 8px rgba(8,145,178,.25); }
.btn-ci-info:hover    { background: #0E7490; color: #fff; }
.btn-ci-success { background: var(--success); box-shadow: 0 2px 8px rgba(22,168,125,.25); }
.btn-ci-success:hover { background: #0E8A64; color: #fff; }

/* ── LIST ITEMS (exams, enrollments) ─────────────────── */
.ci-list { list-style: none; padding: 0; margin: 0; }
.ci-list-item {
    display: flex; align-items: flex-start; justify-content: space-between;
    padding: 0.75rem 1.25rem;
    border-bottom: 1px solid var(--border);
    gap: 0.75rem; transition: background .12s;
}
.ci-list-item:last-child { border-bottom: none; }
.ci-list-item:hover { background: var(--surface); }
.ci-list-item-name {
    font-size: 0.85rem; font-weight: 600; color: var(--text-primary);
    margin-bottom: 0.15rem; line-height: 1.3;
}
.ci-list-item-sub { font-size: 0.73rem; color: var(--text-muted); display: flex; align-items: center; gap: 0.3rem; }
.ci-list-badge {
    font-family: 'JetBrains Mono', monospace;
    font-size: 0.65rem; font-weight: 700;
    padding: 0.18rem 0.55rem; border-radius: 6px;
    white-space: nowrap; flex-shrink: 0;
    display: inline-flex; align-items: center;
}
.ci-list-badge.today  { background: rgba(22,168,125,0.1);  color: var(--success); }
.ci-list-badge.future { background: rgba(59,127,232,0.1);  color: var(--accent); }
.ci-list-date { font-size: 0.72rem; color: var(--text-muted); white-space: nowrap; flex-shrink: 0; margin-top: 0.1rem; }

.ci-list-empty {
    text-align: center; padding: 2.5rem 1.5rem; color: var(--text-muted);
}
.ci-list-empty i { font-size: 1.8rem; opacity: 0.18; display: block; margin-bottom: 0.5rem; }
.ci-list-empty p { font-size: 0.82rem; margin: 0; }

/* ── QUICK ACTIONS ───────────────────────────────────── */
.qa-grid {
    display: grid;
    grid-template-columns: repeat(6, 1fr);
    gap: 0.75rem;
}
.qa-item {
    display: flex; flex-direction: column; align-items: center;
    text-align: center; gap: 0.5rem;
    padding: 1.1rem 0.5rem;
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    text-decoration: none; color: var(--text-primary);
    transition: all .2s;
}
.qa-item:hover {
    border-color: var(--accent);
    background: #EEF4FF;
    color: var(--primary);
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}
.qa-icon {
    width: 44px; height: 44px; border-radius: 11px;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.1rem; flex-shrink: 0;
    transition: transform .2s;
}
.qa-item:hover .qa-icon { transform: scale(1.08); }
.qa-label { font-size: 0.8rem; font-weight: 700; line-height: 1.2; }
.qa-sub   { font-size: 0.65rem; color: var(--text-muted); }

/* Icon color classes */
.ic-blue   { background: rgba(59,127,232,0.1);  color: var(--accent); }
.ic-green  { background: rgba(22,168,125,0.1);  color: var(--success); }
.ic-teal   { background: rgba(8,145,178,0.1);   color: #0891B2; }
.ic-amber  { background: rgba(232,160,32,0.1);  color: #B07800; }
.ic-red    { background: rgba(232,70,70,0.1);   color: var(--danger); }
.ic-slate  { background: rgba(107,122,153,0.1); color: var(--text-secondary); }

/* Alerts */
.alert { border-radius: var(--radius-sm); border: none; font-size: 0.875rem; }
.alert-success { background: rgba(22,168,125,0.1); color: #0E7A5A; border-left: 3px solid var(--success); }
.alert-danger   { background: rgba(232,70,70,0.08); color: #B03030; border-left: 3px solid var(--danger); }

/* ── RESPONSIVE ──────────────────────────────────────── */
@media (max-width: 991px) {
    .qa-grid { grid-template-columns: repeat(3, 1fr); }
}
@media (max-width: 767px) {
    .ci-page-header { padding: 1.1rem 1.2rem; }
    .ci-page-header h1 { font-size: 1.1rem; }
    .qa-grid { grid-template-columns: repeat(2, 1fr); }
    .fin-total-value { font-size: 1.25rem; }
}
</style>

<!-- ── PAGE HEADER ─────────────────────────────────────── -->
<div class="ci-page-header mb-4">
    <div class="ci-page-header-inner">
        <div>
            <h1>
                <i class="fas fa-th-large me-2" style="opacity:.7;font-size:1.1rem;"></i>
                <?= $title ?>
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Início</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
                </ol>
            </nav>
        </div>
        <div class="ci-datetime">
            <i class="fas fa-calendar-alt"></i>
            <?= date('d/m/Y') ?>
            <span class="sep">|</span>
            <i class="fas fa-clock"></i>
            <?= date('H:i') ?>
        </div>
    </div>
</div>

<!-- ── ALERTS ──────────────────────────────────────────── -->
<?= view('admin/partials/alerts') ?>

<!-- ── STATS ROW 1 ─────────────────────────────────────── -->
<div class="row g-3 mb-1">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card sc-blue">
            <div class="sc-label">Total de Alunos</div>
            <div class="sc-value"><?= number_format($totalStudents) ?></div>
            <div class="sc-sub">
                <i class="fas fa-male"></i> <?= $genderChart['male'] ?? 0 ?> Masc.
                &nbsp;·&nbsp;
                <i class="fas fa-female"></i> <?= $genderChart['female'] ?? 0 ?> Fem.
            </div>
            <i class="fas fa-user-graduate sc-watermark"></i>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card sc-green">
            <div class="sc-label">Professores</div>
            <div class="sc-value"><?= number_format($totalTeachers) ?></div>
            <div class="sc-sub">
                <i class="fas fa-clock"></i> <?= $examsToday ?? 0 ?> exames hoje
            </div>
            <i class="fas fa-chalkboard-teacher sc-watermark"></i>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card sc-teal">
            <div class="sc-label">Funcionários</div>
            <div class="sc-value"><?= number_format($totalStaff) ?></div>
            <div class="sc-sub">
                <i class="fas fa-briefcase"></i> Staff ativo
            </div>
            <i class="fas fa-users sc-watermark"></i>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card sc-amber">
            <div class="sc-label">Matrículas Ativas</div>
            <div class="sc-value"><?= number_format($activeEnrollments) ?></div>
            <div class="sc-sub">
                <i class="fas fa-calendar"></i> <?= $currentYear->year_name ?? 'N/A' ?>
            </div>
            <i class="fas fa-file-signature sc-watermark"></i>
        </div>
    </div>
</div>

<!-- ── STATS ROW 2 ─────────────────────────────────────── -->
<div class="row g-3 mb-2">
    <div class="col-xl-3 col-md-6">
        <div class="stat-card sc-red">
            <div class="sc-label">Receita Mensal</div>
            <div class="sc-value" style="font-size:1.35rem;"><?= number_format($currentMonthPayments, 2, ',', '.') ?> Kz</div>
            <div class="sc-sub">
                <i class="fas fa-calendar-check"></i> Pagamentos do mês
            </div>
            <i class="fas fa-money-bill-wave sc-watermark"></i>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card sc-orange">
            <div class="sc-label">Pagamentos Vencidos</div>
            <div class="sc-value"><?= number_format($overduePayments) ?></div>
            <div class="sc-sub">
                <i class="fas fa-clock"></i> Pendentes com atraso
            </div>
            <i class="fas fa-exclamation-triangle sc-watermark"></i>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card sc-emerald">
            <div class="sc-label">Presenças Hoje</div>
            <div class="sc-value"><?= number_format($attendanceToday) ?></div>
            <div class="sc-sub">
                <i class="fas fa-chart-line"></i> Taxa: <?= $attendanceRate ?>%
            </div>
            <i class="fas fa-user-check sc-watermark"></i>
        </div>
    </div>
    <div class="col-xl-3 col-md-6">
        <div class="stat-card sc-purple">
            <div class="sc-label">Exames da Semana</div>
            <div class="sc-value"><?= $examsThisWeek ?></div>
            <div class="sc-sub">
                <i class="fas fa-calendar"></i> <?= $examsToday ?> hoje
            </div>
            <i class="fas fa-pencil-alt sc-watermark"></i>
        </div>
    </div>
</div>

<!-- ── CHARTS ROW 1 ────────────────────────────────────── -->
<div class="row g-3">
    <div class="col-xl-6">
        <div class="ci-card">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-chart-line" style="color:var(--accent);"></i>
                    Evolução de Matrículas (12 meses)
                </div>
                <span class="ci-card-badge blue"><?= array_sum($enrollmentChart['counts']) ?> total</span>
            </div>
            <div class="ci-card-body">
                <div class="chart-wrap"><canvas id="enrollmentChart"></canvas></div>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="ci-card">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-venus-mars" style="color:var(--success);"></i>
                    Distribuição por Género
                </div>
                <span class="ci-card-badge green"><?= $genderChart['male'] + $genderChart['female'] ?> alunos</span>
            </div>
            <div class="ci-card-body">
                <div class="chart-wrap"><canvas id="genderChart"></canvas></div>
            </div>
        </div>
    </div>
</div>

<!-- ── CHARTS ROW 2 ────────────────────────────────────── -->
<div class="row g-3">
    <div class="col-xl-6">
        <div class="ci-card">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-chart-bar" style="color:var(--warning);"></i>
                    Receitas vs Pendentes (6 meses)
                </div>
                <span class="ci-card-badge green"><?= number_format($feeStats->paid_amount, 0, ',', '.') ?> Kz recebido</span>
            </div>
            <div class="ci-card-body">
                <div class="chart-wrap"><canvas id="feeChart"></canvas></div>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="ci-card">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-chart-area" style="color:var(--teal);"></i>
                    Presenças (7 dias)
                </div>
                <span class="ci-card-badge teal">Taxa: <?= $attendanceRate ?>%</span>
            </div>
            <div class="ci-card-body">
                <div class="chart-wrap"><canvas id="attendanceChart"></canvas></div>
            </div>
        </div>
    </div>
</div>

<!-- ── CHARTS ROW 3 ────────────────────────────────────── -->
<div class="row g-3">
    <div class="col-xl-6">
        <div class="ci-card">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-graduation-cap" style="color:var(--danger);"></i>
                    Desempenho Acadêmico
                </div>
            </div>
            <div class="ci-card-body">
                <div class="chart-wrap"><canvas id="performanceChart"></canvas></div>
            </div>
        </div>
    </div>
    <div class="col-xl-6">
        <div class="ci-card">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-sun" style="color:var(--text-secondary);"></i>
                    Turmas por Turno
                </div>
            </div>
            <div class="ci-card-body">
                <div class="chart-wrap"><canvas id="shiftChart"></canvas></div>
            </div>
        </div>
    </div>
</div>

<!-- ── BOTTOM ROW ──────────────────────────────────────── -->
<div class="row g-3">

    <!-- Financial Summary -->
    <div class="col-xl-4">
        <div class="ci-card" style="height:100%;">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-coins" style="color:var(--warning);"></i>
                    Resumo Financeiro
                </div>
            </div>
            <div class="ci-card-body">
                <div class="fin-total-label">Total de Propinas</div>
                <div class="fin-total-value"><?= number_format($feeStats->total_amount, 2, ',', '.') ?> Kz</div>

                <div class="ci-progress">
                    <div class="ci-progress-bar paid"    style="width:<?= $feeStats->paid_percentage ?>%;"></div>
                    <div class="ci-progress-bar pending" style="width:<?= $feeStats->pending_percentage ?>%;"></div>
                </div>
                <div class="progress-legend">
                    <div class="progress-legend-item">
                        <span class="progress-legend-dot" style="background:var(--success);"></span>
                        Pago <?= $feeStats->paid_percentage ?>%
                    </div>
                    <div class="progress-legend-item">
                        <span class="progress-legend-dot" style="background:var(--warning);"></span>
                        Pendente <?= $feeStats->pending_percentage ?>%
                    </div>
                </div>

                <div class="fin-mini-grid">
                    <div class="fin-mini">
                        <div class="fin-mini-icon" style="color:var(--success);"><i class="fas fa-check-circle"></i></div>
                        <div class="fin-mini-val"><?= number_format($feeStats->paid_amount, 2, ',', '.') ?> Kz</div>
                        <div class="fin-mini-label">Recebido</div>
                    </div>
                    <div class="fin-mini">
                        <div class="fin-mini-icon" style="color:var(--warning);"><i class="fas fa-clock"></i></div>
                        <div class="fin-mini-val"><?= number_format($feeStats->pending_amount, 2, ',', '.') ?> Kz</div>
                        <div class="fin-mini-label">Pendente</div>
                    </div>
                </div>

                <a href="<?= site_url('admin/fees/payments') ?>" class="btn-ci-primary">
                    <i class="fas fa-arrow-right"></i> Ver Detalhes Financeiros
                </a>
            </div>
        </div>
    </div>

    <!-- Upcoming Exams -->
    <div class="col-xl-4">
        <div class="ci-card" style="height:100%;display:flex;flex-direction:column;">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-calendar-alt" style="color:var(--teal);"></i>
                    Próximos Exames
                </div>
            </div>
            <div style="flex:1;overflow:hidden;">
                <?php if (!empty($upcomingExams)): ?>
                    <ul class="ci-list">
                        <?php foreach ($upcomingExams as $exam): ?>
                        <li class="ci-list-item">
                            <div style="min-width:0;">
                                <div class="ci-list-item-name"><?= esc($exam->board_name) ?> — <?= esc($exam->discipline_name) ?></div>
                                <div class="ci-list-item-sub">
                                    <i class="fas fa-school" style="font-size:.65rem;"></i>
                                    <?= esc($exam->class_name) ?>
                                </div>
                            </div>
                            <span class="ci-list-badge <?= $exam->exam_date == date('Y-m-d') ? 'today' : 'future' ?>">
                                <?= date('d/m', strtotime($exam->exam_date)) ?>
                            </span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="ci-list-empty">
                        <i class="fas fa-calendar-times"></i>
                        <p>Nenhum exame agendado</p>
                    </div>
                <?php endif; ?>
            </div>
            <div style="padding:0;">
                <a href="<?= site_url('admin/exams/schedules') ?>" class="btn-ci-info">
                    <i class="fas fa-calendar"></i> Ver Calendário de Exames
                </a>
            </div>
        </div>
    </div>

    <!-- Recent Enrollments -->
    <div class="col-xl-4">
        <div class="ci-card" style="height:100%;display:flex;flex-direction:column;">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fas fa-user-plus" style="color:var(--success);"></i>
                    Matrículas Recentes
                </div>
            </div>
            <div style="flex:1;overflow:hidden;">
                <?php if (!empty($recentEnrollments)): ?>
                    <ul class="ci-list">
                        <?php foreach ($recentEnrollments as $enrollment): ?>
                        <li class="ci-list-item">
                            <div style="min-width:0;">
                                <div class="ci-list-item-name">
                                    <?= esc($enrollment->first_name) ?> <?= esc($enrollment->last_name) ?>
                                </div>
                                <div class="ci-list-item-sub">
                                    <i class="fas fa-school" style="font-size:.65rem;"></i>
                                    <?= esc($enrollment->class_name) ?>
                                    (<?= esc($enrollment->level_name) ?>)
                                </div>
                            </div>
                            <span class="ci-list-date"><?= date('d/m', strtotime($enrollment->created_at)) ?></span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="ci-list-empty">
                        <i class="fas fa-user-slash"></i>
                        <p>Nenhuma matrícula recente</p>
                    </div>
                <?php endif; ?>
            </div>
            <div style="padding:0;">
                <a href="<?= site_url('admin/students/enrollments') ?>" class="btn-ci-success">
                    <i class="fas fa-plus"></i> Nova Matrícula
                </a>
            </div>
        </div>
    </div>
</div>

<!-- ── QUICK ACTIONS ───────────────────────────────────── -->
<div class="ci-card mt-1">
    <div class="ci-card-header">
        <div class="ci-card-title">
            <i class="fas fa-bolt" style="color:var(--warning);"></i>
            Ações Rápidas
        </div>
    </div>
    <div class="ci-card-body">
        <div class="qa-grid">
            <a href="<?= site_url('admin/students/form-add') ?>" class="qa-item">
                <div class="qa-icon ic-blue"><i class="fas fa-user-plus"></i></div>
                <div class="qa-label">Novo Aluno</div>
                <div class="qa-sub">Cadastrar aluno</div>
            </a>
            <a href="<?= site_url('admin/teachers/form-add') ?>" class="qa-item">
                <div class="qa-icon ic-green"><i class="fas fa-chalkboard-teacher"></i></div>
                <div class="qa-label">Novo Professor</div>
                <div class="qa-sub">Cadastrar professor</div>
            </a>
            <a href="<?= site_url('admin/students/enrollments') ?>" class="qa-item">
                <div class="qa-icon ic-teal"><i class="fas fa-file-signature"></i></div>
                <div class="qa-label">Nova Matrícula</div>
                <div class="qa-sub">Matricular aluno</div>
            </a>
            <a href="<?= site_url('admin/fees/payments/add') ?>" class="qa-item">
                <div class="qa-icon ic-amber"><i class="fas fa-money-bill-wave"></i></div>
                <div class="qa-label">Registrar Pagamento</div>
                <div class="qa-sub">Receber propina</div>
            </a>
            <a href="<?= site_url('admin/exams/schedules') ?>" class="qa-item">
                <div class="qa-icon ic-red"><i class="fas fa-pencil-alt"></i></div>
                <div class="qa-label">Agendar Exame</div>
                <div class="qa-sub">Criar exame</div>
            </a>
            <a href="<?= site_url('admin/reports') ?>" class="qa-item">
                <div class="qa-icon ic-slate"><i class="fas fa-chart-bar"></i></div>
                <div class="qa-label">Relatórios</div>
                <div class="qa-sub">Gerar relatórios</div>
            </a>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ── Shared chart defaults ─────────────────────────── */
    Chart.defaults.font.family = "'Sora', sans-serif";
    Chart.defaults.font.size   = 11;
    Chart.defaults.color       = '#6B7A99';

    const grid = {
        color: 'rgba(226,232,244,0.6)',
        drawBorder: false
    };

    /* ── 1. Enrollment line chart ──────────────────────── */
    new Chart(document.getElementById('enrollmentChart'), {
        type: 'line',
        data: {
            labels: <?= json_encode($enrollmentChart['months']) ?>,
            datasets: [{
                label: 'Matrículas',
                data:  <?= json_encode($enrollmentChart['counts']) ?>,
                borderColor: '#3B7FE8',
                backgroundColor: 'rgba(59,127,232,0.08)',
                borderWidth: 2.5,
                pointBackgroundColor: '#3B7FE8',
                pointBorderColor: '#fff',
                pointBorderWidth: 2,
                pointRadius: 4,
                tension: 0.35,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                x: { grid },
                y: { beginAtZero: true, grid, ticks: { stepSize: 1 } }
            }
        }
    });

    /* ── 2. Gender doughnut ────────────────────────────── */
    new Chart(document.getElementById('genderChart'), {
        type: 'doughnut',
        data: {
            labels: ['Masculino', 'Feminino'],
            datasets: [{
                data: [<?= $genderChart['male'] ?>, <?= $genderChart['female'] ?>],
                backgroundColor: ['#3B7FE8', '#E8A020'],
                hoverBackgroundColor: ['#2C6FD4', '#C07818'],
                borderWidth: 0,
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: { legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true, pointStyleWidth: 10 } } }
        }
    });

    /* ── 3. Fee bar chart ──────────────────────────────── */
    new Chart(document.getElementById('feeChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode($feeChart['months']) ?>,
            datasets: [
                {
                    label: 'Recebido',
                    data: <?= json_encode($feeChart['collected']) ?>,
                    backgroundColor: '#16A87D',
                    borderRadius: 5,
                    borderSkipped: false
                },
                {
                    label: 'Pendente',
                    data: <?= json_encode($feeChart['pending']) ?>,
                    backgroundColor: '#E8A020',
                    borderRadius: 5,
                    borderSkipped: false
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { grid: { display: false } },
                y: {
                    beginAtZero: true, grid,
                    ticks: { callback: v => v.toLocaleString('pt-AO') + ' Kz' }
                }
            },
            plugins: {
                legend: { position: 'bottom', labels: { usePointStyle: true, pointStyleWidth: 10, padding: 16 } },
                tooltip: { callbacks: { label: ctx => ctx.dataset.label + ': ' + ctx.raw.toLocaleString('pt-AO') + ' Kz' } }
            }
        }
    });

    /* ── 4. Attendance line chart ──────────────────────── */
    new Chart(document.getElementById('attendanceChart'), {
        type: 'line',
        data: {
            labels: <?= json_encode($attendanceChart['days']) ?>,
            datasets: [
                {
                    label: 'Presentes',
                    data: <?= json_encode($attendanceChart['present']) ?>,
                    borderColor: '#16A87D',
                    backgroundColor: 'rgba(22,168,125,0.08)',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#16A87D',
                    pointBorderColor: '#fff', pointBorderWidth: 2, pointRadius: 4,
                    tension: 0.35, fill: true
                },
                {
                    label: 'Ausentes',
                    data: <?= json_encode($attendanceChart['absent']) ?>,
                    borderColor: '#E84646',
                    backgroundColor: 'rgba(232,70,70,0.06)',
                    borderWidth: 2.5,
                    pointBackgroundColor: '#E84646',
                    pointBorderColor: '#fff', pointBorderWidth: 2, pointRadius: 4,
                    tension: 0.35, fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: { grid },
                y: { beginAtZero: true, grid, ticks: { stepSize: 1 } }
            },
            plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, pointStyleWidth: 10, padding: 16 } } }
        }
    });

    /* ── 5. Performance pie ────────────────────────────── */
    new Chart(document.getElementById('performanceChart'), {
        type: 'pie',
        data: {
            labels: ['Excelente (17-20)', 'Bom (14-16)', 'Satisfatório (10-13)', 'Insuficiente (<10)'],
            datasets: [{
                data: [
                    <?= $performanceChart['excelente'] ?>,
                    <?= $performanceChart['bom'] ?>,
                    <?= $performanceChart['satisfatorio'] ?>,
                    <?= $performanceChart['insuficiente'] ?>
                ],
                backgroundColor: ['#16A87D', '#3B7FE8', '#E8A020', '#E84646'],
                borderWidth: 0,
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true, pointStyleWidth: 10 } } }
        }
    });

    /* ── 6. Shift doughnut ─────────────────────────────── */
    new Chart(document.getElementById('shiftChart'), {
        type: 'doughnut',
        data: {
            labels: ['Manhã', 'Tarde', 'Noite', 'Integral'],
            datasets: [{
                data: [
                    <?= $classesByShift['Manhã'] ?>,
                    <?= $classesByShift['Tarde'] ?>,
                    <?= $classesByShift['Noite'] ?>,
                    <?= $classesByShift['Integral'] ?>
                ],
                backgroundColor: ['#3B7FE8', '#16A87D', '#E8A020', '#E84646'],
                borderWidth: 0,
                hoverOffset: 6
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            cutout: '65%',
            plugins: { legend: { position: 'bottom', labels: { padding: 16, usePointStyle: true, pointStyleWidth: 10 } } }
        }
    });

});
</script>
<?= $this->endSection() ?>