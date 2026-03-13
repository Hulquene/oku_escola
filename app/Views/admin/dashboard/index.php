<?php
// app/Views/admin/dashboard/index.php
$this->extend('admin/layouts/index');
$this->section('content');
?>

<!-- ============================================================
     ESTILOS EXCLUSIVOS DO DASHBOARD (evitar conflito global)
     ============================================================ -->
<style>
/* ---- Tabela de estatísticas escolares ---- */
.school-stats-table { width:100%; border-collapse:separate; border-spacing:0; }
.school-stats-table thead th {
    background:var(--surface);
    color:var(--text-secondary);
    font-size:.6rem;
    font-weight:700;
    text-transform:uppercase;
    letter-spacing:.08em;
    padding:.5rem .65rem;
    border-bottom:1.5px solid var(--border);
    white-space:nowrap;
    text-align:center;
}
.school-stats-table thead th:first-child { text-align:left; }
.school-stats-table tbody td {
    padding:.48rem .65rem;
    font-size:.78rem;
    text-align:center;
    border-bottom:1px solid var(--border);
    vertical-align:middle;
}
.school-stats-table tbody td:first-child { text-align:left; }
.school-stats-table tbody tr:last-child td { border-bottom:none; }
.school-stats-table tbody tr:hover { background:#F5F8FF; }
.school-stats-table tfoot td {
    padding:.5rem .65rem;
    font-size:.75rem;
    font-weight:700;
    text-align:center;
    background:var(--surface);
    border-top:1.5px solid var(--border);
    color:var(--text-primary);
}
.school-stats-table tfoot td:first-child { text-align:left; }

/* ---- Wrap de scroll horizontal para tabela ---- */
.table-scroll-wrap { overflow-x:auto; -webkit-overflow-scrolling:touch; }
.table-scroll-wrap::-webkit-scrollbar { height:4px; }
.table-scroll-wrap::-webkit-scrollbar-thumb { background:var(--border); border-radius:4px; }

/* ---- Separador de grupo na tabela ---- */
.stats-group-header td {
    background:rgba(59,127,232,.05);
    font-size:.68rem;
    font-weight:700;
    color:var(--accent);
    text-transform:uppercase;
    letter-spacing:.07em;
    padding:.35rem .65rem;
    border-bottom:1px solid var(--border);
    text-align:left !important;
}

/* ---- KPI inline (dentro da tabela de stats) ---- */
.num-m  { color:var(--accent); }
.num-f  { color:#E84683; }
.num-t  { color:var(--text-primary); font-weight:700; }

/* ---- Turno chips ---- */
.shift-chip {
    display:inline-flex; align-items:center; gap:.3rem;
    padding:.2rem .6rem;
    border-radius:50px;
    font-size:.7rem; font-weight:600;
    white-space:nowrap;
}
.shift-chip.manha  { background:rgba(59,127,232,.1);  color:var(--accent); }
.shift-chip.tarde  { background:rgba(232,160,32,.1);  color:#B07800; }
.shift-chip.noite  { background:rgba(124,77,255,.1);  color:#7C4DFF; }
.shift-chip.integral{ background:rgba(22,168,125,.1); color:var(--success); }

/* ---- Responsividade extra para stat cards ---- */
@media(max-width:575px){
    .sc-value { font-size:1.45rem; }
    .qa-grid  { grid-template-columns:repeat(3,1fr); }
}

/* ---- Live clock ---- */
#liveClock { font-variant-numeric:tabular-nums; }

/* ---- Chart heights específicos ---- */
.chart-wrap-sm { position:relative; height:220px; }
.chart-wrap-xs { position:relative; height:180px; }
</style>

<!-- ============================================================
     PAGE HEADER
     ============================================================ -->
<div class="ci-page-header">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fa-solid fa-gauge-high me-2" style="opacity:.85"></i>Dashboard</h1>
            <div class="ci-datetime">
                <i class="fa-regular fa-calendar"></i>
                <span id="liveDate"><?= date('l, d \d\e F \d\e Y') ?></span>
                <span class="sep">|</span>
                <i class="fa-regular fa-clock"></i>
                <span id="liveClock"><?= date('H:i') ?></span>
                <?php if (!empty($academicYear)): ?>
                    <span class="sep">|</span>
                    <i class="fa-solid fa-book-open"></i>
                    <span><?= esc($academicYear->year_name ?? $academicYear->academic_year) ?></span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Filtro Ano Letivo -->
        <?php if (!empty($academicYears)): ?>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <form method="get" class="d-flex align-items-center gap-2" id="yearFilterForm">
                <label class="filter-label mb-0" style="white-space:nowrap;color:rgba(255,255,255,.65)">
                    <i class="fa-solid fa-filter fa-xs me-1"></i>Ano Letivo
                </label>
                <select name="academic_year" class="filter-select" style="min-width:130px"
                        onchange="document.getElementById('yearFilterForm').submit()">
                    <?php foreach ($academicYears as $ay): ?>
                        <option value="<?= $ay['id'] ?>" <?= ($selectedYear == $ay['id']) ? 'selected' : '' ?>>
                            <?= esc($ay['year_name'] ?? $ay['academic_year']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ============================================================
     ROW 1 — STAT CARDS PRINCIPAIS
     ============================================================ -->
<div class="row g-3 mb-0">

    <!-- Alunos -->
    <?php if (isset($totalStudents)): ?>
    <div class="col-6 col-sm-4 col-lg-2">
        <div class="stat-card sc-blue">
            <div class="sc-watermark"><i class="fa-solid fa-user-graduate"></i></div>
            <div class="sc-label"><i class="fa-solid fa-user-graduate me-1"></i>Alunos</div>
            <div class="sc-value"><?= number_format($totalStudents) ?></div>
            <div class="sc-sub">
                <?php if (!empty($genderChart)): ?>
                    <i class="fa-solid fa-mars"></i><?= $genderChart['male'] ?>
                    <span style="opacity:.4">|</span>
                    <i class="fa-solid fa-venus"></i><?= $genderChart['female'] ?>
                <?php endif; ?>
                <span>activos</span>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Matrículas -->
    <?php if (isset($activeEnrollments)): ?>
    <div class="col-6 col-sm-4 col-lg-2">
        <div class="stat-card sc-green">
            <div class="sc-watermark"><i class="fa-solid fa-id-card"></i></div>
            <div class="sc-label"><i class="fa-solid fa-id-card me-1"></i>Matrículas</div>
            <div class="sc-value"><?= number_format($activeEnrollments) ?></div>
            <div class="sc-sub">
                <?php if (!empty($enrollmentStatusChart)): ?>
                    <i class="fa-solid fa-circle-check"></i>Activas neste ano
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Professores -->
    <?php if (isset($totalTeachers)): ?>
    <div class="col-6 col-sm-4 col-lg-2">
        <div class="stat-card sc-teal">
            <div class="sc-watermark"><i class="fa-solid fa-chalkboard-user"></i></div>
            <div class="sc-label"><i class="fa-solid fa-chalkboard-user me-1"></i>Professores</div>
            <div class="sc-value"><?= number_format($totalTeachers) ?></div>
            <div class="sc-sub">
                <?php if (isset($studentsPerTeacher)): ?>
                    <i class="fa-solid fa-ratio"></i><?= $studentsPerTeacher ?> al./prof.
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Turmas -->
    <?php if (isset($totalClasses)): ?>
    <div class="col-6 col-sm-4 col-lg-2">
        <div class="stat-card sc-purple">
            <div class="sc-watermark"><i class="fa-solid fa-door-open"></i></div>
            <div class="sc-label"><i class="fa-solid fa-door-open me-1"></i>Turmas</div>
            <div class="sc-value"><?= number_format($totalClasses) ?></div>
            <div class="sc-sub">
                <?php if (isset($occupancyRate)): ?>
                    <i class="fa-solid fa-chart-pie"></i><?= $occupancyRate ?>% ocupação
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Cursos -->
    <?php if (isset($totalCourses)): ?>
    <div class="col-6 col-sm-4 col-lg-2">
        <div class="stat-card sc-amber">
            <div class="sc-watermark"><i class="fa-solid fa-book"></i></div>
            <div class="sc-label"><i class="fa-solid fa-book me-1"></i>Cursos</div>
            <div class="sc-value"><?= number_format($totalCourses) ?></div>
            <div class="sc-sub">
                <?php if (isset($totalDisciplines)): ?>
                    <i class="fa-solid fa-layer-group"></i><?= $totalDisciplines ?> disciplinas
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Financeiro / Presença -->
    <?php if (isset($feeStats)): ?>
    <div class="col-6 col-sm-4 col-lg-2">
        <div class="stat-card sc-emerald">
            <div class="sc-watermark"><i class="fa-solid fa-money-bill-wave"></i></div>
            <div class="sc-label"><i class="fa-solid fa-money-bill-wave me-1"></i>Recebido</div>
            <div class="sc-value" style="font-size:1.2rem"><?= number_format($feeStats->paid_amount, 0, ',', '.') ?></div>
            <div class="sc-sub">
                <i class="fa-solid fa-percent"></i><?= $feeStats->paid_percentage ?>% do total
            </div>
        </div>
    </div>
    <?php elseif (isset($attendanceRate)): ?>
    <div class="col-6 col-sm-4 col-lg-2">
        <div class="stat-card sc-emerald">
            <div class="sc-watermark"><i class="fa-solid fa-clipboard-check"></i></div>
            <div class="sc-label"><i class="fa-solid fa-clipboard-check me-1"></i>Presença</div>
            <div class="sc-value"><?= $attendanceRate ?>%</div>
            <div class="sc-sub">
                <i class="fa-regular fa-calendar"></i>este mês
            </div>
        </div>
    </div>
    <?php endif; ?>

</div><!-- /row stat cards -->

<!-- ============================================================
     ROW 2 — INDICADORES SECUNDÁRIOS
     ============================================================ -->
<?php if (isset($approvalRate) || isset($avgStudentsPerClass) || isset($examsToday) || isset($overduePayments) || isset($staffStats) || isset($totalStaff)): ?>
<div class="row g-3 mt-0 mb-3">

    <?php if (isset($approvalRate)): ?>
    <div class="col-6 col-md-3">
        <div class="ci-card h-100 mb-0">
            <div class="ci-card-body text-center py-3">
                <div style="font-size:1.6rem;font-weight:700;font-family:var(--font-mono);color:var(--success)"><?= $approvalRate ?>%</div>
                <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-muted);margin-top:.15rem">Taxa de Aprovação</div>
                <?php if (isset($failureRate)): ?>
                <div style="font-size:.72rem;color:var(--danger);margin-top:.3rem"><i class="fa-solid fa-xmark me-1"></i><?= $failureRate ?>% reprovação</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (isset($avgStudentsPerClass)): ?>
    <div class="col-6 col-md-3">
        <div class="ci-card h-100 mb-0">
            <div class="ci-card-body text-center py-3">
                <div style="font-size:1.6rem;font-weight:700;font-family:var(--font-mono);color:var(--accent)"><?= $avgStudentsPerClass ?></div>
                <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-muted);margin-top:.15rem">Alunos / Turma</div>
                <?php if (isset($totalCapacity)): ?>
                <div style="font-size:.72rem;color:var(--text-muted);margin-top:.3rem"><i class="fa-solid fa-users me-1"></i>Cap. <?= number_format($totalCapacity) ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (isset($examsToday)): ?>
    <div class="col-6 col-md-3">
        <div class="ci-card h-100 mb-0">
            <div class="ci-card-body text-center py-3">
                <div style="font-size:1.6rem;font-weight:700;font-family:var(--font-mono);color:#7C4DFF"><?= $examsToday ?></div>
                <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-muted);margin-top:.15rem">Exames Hoje</div>
                <?php if (isset($examsThisWeek)): ?>
                <div style="font-size:.72rem;color:var(--text-muted);margin-top:.3rem"><i class="fa-regular fa-calendar-week me-1"></i><?= $examsThisWeek ?> esta semana</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (isset($overduePayments)): ?>
    <div class="col-6 col-md-3">
        <div class="ci-card h-100 mb-0">
            <div class="ci-card-body text-center py-3">
                <div style="font-size:1.6rem;font-weight:700;font-family:var(--font-mono);color:var(--danger)"><?= $overduePayments ?></div>
                <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-muted);margin-top:.15rem">Propinas Vencidas</div>
                <?php if (isset($currentMonthPayments)): ?>
                <div style="font-size:.72rem;color:var(--success);margin-top:.3rem"><i class="fa-solid fa-arrow-trend-up me-1"></i><?= number_format($currentMonthPayments, 0, ',', '.') ?> Kz este mês</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php elseif (isset($totalStaff)): ?>
    <div class="col-6 col-md-3">
        <div class="ci-card h-100 mb-0">
            <div class="ci-card-body text-center py-3">
                <div style="font-size:1.6rem;font-weight:700;font-family:var(--font-mono);color:var(--text-secondary)"><?= $totalStaff ?></div>
                <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-muted);margin-top:.15rem">Funcionários</div>
                <div style="font-size:.72rem;color:var(--text-muted);margin-top:.3rem"><i class="fa-solid fa-briefcase me-1"></i>Equipe administrativa</div>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>
<?php endif; ?>

<!-- ============================================================
     ROW 3 — GRÁFICOS PRINCIPAIS
     ============================================================ -->
<div class="row g-3 mb-3">

    <!-- Matrículas (12 meses) -->
    <?php if (!empty($enrollmentChart)): ?>
    <div class="col-12 col-lg-8">
        <div class="ci-card h-100 mb-0">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fa-solid fa-chart-line text-accent"></i>Matrículas — Últimos 12 Meses
                </div>
                <span class="ci-card-badge blue"><?= array_sum($enrollmentChart['counts']) ?> total</span>
            </div>
            <div class="ci-card-body">
                <div class="chart-wrap">
                    <canvas id="enrollmentChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Género -->
    <?php if (!empty($genderChart) && ($genderChart['male'] + $genderChart['female'] > 0)): ?>
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="ci-card h-100 mb-0">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fa-solid fa-venus-mars text-accent"></i>Distribuição por Género
                </div>
                <?php if (!empty($genderRatio)): ?>
                <span class="ci-card-badge green"><?= $genderRatio ?> M:F</span>
                <?php endif; ?>
            </div>
            <div class="ci-card-body">
                <div class="chart-wrap-sm">
                    <canvas id="genderChart"></canvas>
                </div>
                <div class="d-flex justify-content-center gap-3 mt-2">
                    <div style="font-size:.78rem;color:var(--text-secondary)">
                        <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#3B7FE8;margin-right:4px"></span>
                        Masculino <strong><?= $genderChart['male'] ?></strong>
                    </div>
                    <div style="font-size:.78rem;color:var(--text-secondary)">
                        <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#E84683;margin-right:4px"></span>
                        Feminino <strong><?= $genderChart['female'] ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>

<!-- ============================================================
     ROW 4 — FINANCEIRO + PRESENÇAS
     ============================================================ -->
<?php if (isset($feeStats) || !empty($attendanceChart)): ?>
<div class="row g-3 mb-3">

    <!-- Financeiro -->
    <?php if (isset($feeStats)): ?>
    <div class="col-12 col-lg-4">
        <div class="ci-card h-100 mb-0">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fa-solid fa-coins text-accent"></i>Resumo Financeiro
                </div>
                <span class="ci-card-badge green"><?= $feeStats->paid_percentage ?>% pago</span>
            </div>
            <div class="ci-card-body">
                <div class="fin-total-label">Total a Receber</div>
                <div class="fin-total-value"><?= number_format($feeStats->total_amount, 0, ',', '.') ?> Kz</div>
                <div class="progress-legend">
                    <div class="progress-legend-item">
                        <div class="progress-legend-dot" style="background:var(--success)"></div>
                        Pago (<?= $feeStats->paid_percentage ?>%)
                    </div>
                    <div class="progress-legend-item">
                        <div class="progress-legend-dot" style="background:var(--warning)"></div>
                        Pendente (<?= $feeStats->pending_percentage ?>%)
                    </div>
                </div>
                <div class="ci-progress">
                    <div class="ci-progress-bar paid" style="width:<?= $feeStats->paid_percentage ?>%"></div>
                    <div class="ci-progress-bar pending" style="width:<?= $feeStats->pending_percentage ?>%"></div>
                </div>
                <div class="fin-mini-grid mt-3">
                    <div class="fin-mini">
                        <div class="fin-mini-icon text-success"><i class="fa-solid fa-circle-check"></i></div>
                        <div class="fin-mini-val"><?= number_format($feeStats->paid_amount, 0, ',', '.') ?></div>
                        <div class="fin-mini-label">Recebido (Kz)</div>
                    </div>
                    <div class="fin-mini">
                        <div class="fin-mini-icon text-warning"><i class="fa-solid fa-clock"></i></div>
                        <div class="fin-mini-val"><?= number_format($feeStats->pending_amount, 0, ',', '.') ?></div>
                        <div class="fin-mini-label">Pendente (Kz)</div>
                    </div>
                </div>
                <?php if (can('financial.dashboard')): ?>
                <a href="<?= base_url('admin/financial') ?>" class="btn-ci-primary mt-2">
                    <i class="fa-solid fa-arrow-right"></i>Ver Financeiro
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Gráfico Financeiro (6 meses) -->
    <?php if (!empty($feeChart)): ?>
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="ci-card h-100 mb-0">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fa-solid fa-chart-bar text-accent"></i>Propinas — 6 Meses
                </div>
            </div>
            <div class="ci-card-body">
                <div class="chart-wrap">
                    <canvas id="feeChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Presenças 7 dias -->
    <?php if (!empty($attendanceChart)): ?>
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="ci-card h-100 mb-0">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fa-solid fa-clipboard-check text-accent"></i>Presenças — 7 Dias
                </div>
                <?php if (isset($attendanceRate)): ?>
                <span class="ci-card-badge green"><?= $attendanceRate ?>% este mês</span>
                <?php endif; ?>
            </div>
            <div class="ci-card-body">
                <div class="chart-wrap">
                    <canvas id="attendanceChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>
<?php endif; ?>

<!-- ============================================================
     ROW 5 — TURMAS POR TURNO + DESEMPENHO + CURSOS
     ============================================================ -->
<div class="row g-3 mb-3">

    <!-- Turmas por Turno -->
    <?php if (!empty($classesByShift)): ?>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="ci-card h-100 mb-0">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fa-solid fa-sun text-accent"></i>Turmas por Turno
                </div>
                <?php if (isset($totalClasses)): ?>
                <span class="ci-card-badge blue"><?= $totalClasses ?></span>
                <?php endif; ?>
            </div>
            <div class="ci-card-body pb-2">
                <?php
                $shiftConfig = [
                    'Manhã'    => ['class'=>'manha',   'icon'=>'fa-sun'],
                    'Tarde'    => ['class'=>'tarde',   'icon'=>'fa-cloud-sun'],
                    'Noite'    => ['class'=>'noite',   'icon'=>'fa-moon'],
                    'Integral' => ['class'=>'integral','icon'=>'fa-circle'],
                ];
                $totalShift = array_sum($classesByShift) ?: 1;
                foreach ($classesByShift as $shift => $count):
                    $cfg = $shiftConfig[$shift] ?? ['class'=>'manha','icon'=>'fa-circle'];
                    $pct = round(($count / $totalShift) * 100);
                ?>
                <div class="mb-3">
                    <div class="d-flex align-items-center justify-content-between mb-1">
                        <span class="shift-chip <?= $cfg['class'] ?>">
                            <i class="fa-solid <?= $cfg['icon'] ?>"></i><?= $shift ?>
                        </span>
                        <span style="font-family:var(--font-mono);font-size:.8rem;font-weight:700;color:var(--text-primary)"><?= $count ?></span>
                    </div>
                    <div style="height:5px;background:var(--border);border-radius:50px;overflow:hidden">
                        <div style="height:100%;width:<?= $pct ?>%;background:var(--accent);border-radius:50px;transition:width .6s ease"></div>
                    </div>
                </div>
                <?php endforeach; ?>

                <?php if (isset($classesWithTeacher) && isset($totalClasses) && $totalClasses > 0): ?>
                <div style="font-size:.72rem;color:var(--text-muted);border-top:1px solid var(--border);padding-top:.6rem;margin-top:.4rem">
                    <i class="fa-solid fa-chalkboard-user me-1"></i>
                    <?= $classesWithTeacher ?> / <?= $totalClasses ?> com director de turma
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Desempenho Académico -->
    <?php if (!empty($performanceChart)): ?>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="ci-card h-100 mb-0">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fa-solid fa-medal text-accent"></i>Desempenho Académico
                </div>
            </div>
            <div class="ci-card-body">
                <div class="chart-wrap-sm">
                    <canvas id="performanceChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Cursos por Tipo -->
    <?php if (!empty($courseTypeChart)): ?>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="ci-card h-100 mb-0">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fa-solid fa-book text-accent"></i>Cursos por Tipo
                </div>
                <?php if (isset($totalCourses)): ?>
                <span class="ci-card-badge amber"><?= $totalCourses ?> cursos</span>
                <?php endif; ?>
            </div>
            <div class="ci-card-body">
                <div class="chart-wrap-sm">
                    <canvas id="courseTypeChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Status de Matrículas -->
    <?php if (!empty($enrollmentStatusChart)): ?>
    <div class="col-12 col-sm-6 col-lg-3">
        <div class="ci-card h-100 mb-0">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fa-solid fa-circle-half-stroke text-accent"></i>Status das Matrículas
                </div>
            </div>
            <div class="ci-card-body">
                <div class="chart-wrap-sm">
                    <canvas id="enrollmentStatusChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>

<!-- ============================================================
     ROW 6 — RESULTADOS SEMESTRAIS + DISCIPLINAS
     ============================================================ -->
<?php if (!empty($semesterResultsChart) || !empty($topDisciplinesChart)): ?>
<div class="row g-3 mb-3">

    <?php if (!empty($semesterResultsChart)): ?>
    <div class="col-12 col-lg-7">
        <div class="ci-card mb-0">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fa-solid fa-chart-column text-accent"></i>Resultados por Semestre
                </div>
            </div>
            <div class="ci-card-body">
                <div class="chart-wrap">
                    <canvas id="semesterResultsChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($topDisciplinesChart)): ?>
    <div class="col-12 col-lg-5">
        <div class="ci-card mb-0">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fa-solid fa-list-ol text-accent"></i>Top Disciplinas — Carga Horária
                </div>
                <?php if (isset($totalWorkload)): ?>
                <span class="ci-card-badge teal"><?= number_format($totalWorkload) ?>h total</span>
                <?php endif; ?>
            </div>
            <div class="ci-card-body">
                <div class="chart-wrap">
                    <canvas id="topDisciplinesChart"></canvas>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>
<?php endif; ?>

<!-- ============================================================
     ROW 7 — LISTAS: MATRÍCULAS RECENTES + PRÓXIMOS EXAMES
     ============================================================ -->
<div class="row g-3 mb-3">

    <!-- Matrículas Recentes -->
    <?php if (!empty($recentEnrollments)): ?>
    <div class="col-12 col-lg-7">
        <div class="ci-card mb-0">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fa-solid fa-clock-rotate-left text-accent"></i>Matrículas Recentes
                </div>
                <?php if (can('enrollments.list')): ?>
                <a href="<?= base_url('admin/enrollments') ?>" style="font-size:.75rem;color:var(--accent);text-decoration:none;font-weight:600">
                    Ver todas <i class="fa-solid fa-arrow-right fa-xs"></i>
                </a>
                <?php endif; ?>
            </div>
            <div class="ci-card-body p0">
                <ul class="ci-list">
                    <?php foreach (array_slice($recentEnrollments, 0, 8) as $enroll): ?>
                    <li class="ci-list-item">
                        <div style="min-width:0;flex:1">
                            <div class="ci-list-item-name">
                                <?= esc($enroll->first_name . ' ' . $enroll->last_name) ?>
                            </div>
                            <div class="ci-list-item-sub">
                                <?php if (!empty($enroll->class_name)): ?>
                                    <i class="fa-solid fa-door-open fa-xs"></i><?= esc($enroll->class_name) ?>
                                <?php endif; ?>
                                <?php if (!empty($enroll->level_name)): ?>
                                    <span style="opacity:.4">·</span><?= esc($enroll->level_name) ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="text-end flex-shrink-0">
                            <?php
                                $statusMap = [
                                    'Ativo'    => ['class'=>'success','label'=>'Ativo'],
                                    'Pendente' => ['class'=>'warning','label'=>'Pendente'],
                                    'Inativo'  => ['class'=>'danger', 'label'=>'Inativo'],
                                ];
                                $st = $statusMap[$enroll->status] ?? ['class'=>'secondary','label'=>$enroll->status];
                            ?>
                            <span class="badge bg-<?= $st['class'] ?>-subtle text-<?= $st['class'] ?>" style="font-size:.68rem"><?= $st['label'] ?></span>
                            <div class="ci-list-date"><?= date('d/m/Y', strtotime($enroll->created_at)) ?></div>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Próximos Exames -->
    <?php if (!empty($upcomingExams)): ?>
    <div class="col-12 col-lg-5">
        <div class="ci-card mb-0">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fa-solid fa-calendar-check text-accent"></i>Próximos Exames
                </div>
                <?php if (can('exams.calendar')): ?>
                <a href="<?= base_url('admin/exams') ?>" style="font-size:.75rem;color:var(--accent);text-decoration:none;font-weight:600">
                    Ver todos <i class="fa-solid fa-arrow-right fa-xs"></i>
                </a>
                <?php endif; ?>
            </div>
            <div class="ci-card-body p0">
                <ul class="ci-list">
                    <?php foreach (array_slice($upcomingExams, 0, 8) as $exam):
                        $isToday = (date('Y-m-d', strtotime($exam->exam_date)) === date('Y-m-d'));
                    ?>
                    <li class="ci-list-item">
                        <div style="min-width:0;flex:1">
                            <div class="ci-list-item-name"><?= esc($exam->discipline_name ?? '—') ?></div>
                            <div class="ci-list-item-sub">
                                <i class="fa-solid fa-door-open fa-xs"></i><?= esc($exam->class_name ?? '—') ?>
                                <?php if (!empty($exam->board_name)): ?>
                                    <span style="opacity:.4">·</span><?= esc($exam->board_name) ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="text-end flex-shrink-0">
                            <span class="ci-list-badge <?= $isToday ? 'today' : 'future' ?>">
                                <?= $isToday ? 'Hoje' : date('d/m', strtotime($exam->exam_date)) ?>
                            </span>
                            <?php if (!empty($exam->start_time)): ?>
                            <div class="ci-list-date"><?= date('H:i', strtotime($exam->start_time)) ?></div>
                            <?php endif; ?>
                        </div>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>

<!-- ============================================================
     TABELA ESTATÍSTICA ESCOLAR
     ============================================================ -->
<?php if (!empty($schoolStats['details'])): ?>
<div class="ci-card mb-3">
    <div class="ci-card-header">
        <div class="ci-card-title">
            <i class="fa-solid fa-table text-accent"></i>Estatística Geral da Escola
        </div>
        <span class="ci-card-badge blue"><?= $academicYear->year_name ?? ($academicYear->academic_year ?? '') ?></span>
    </div>
    <div class="ci-card-body p0">
        <div class="table-scroll-wrap">
            <table class="school-stats-table">
                <thead>
                    <tr>
                        <th rowspan="2" style="min-width:120px;vertical-align:middle">Curso</th>
                        <th rowspan="2" style="min-width:90px;vertical-align:middle">Nível</th>
                        <th rowspan="2" style="vertical-align:middle">Turmas</th>
                        <th colspan="3">Efectivo</th>
                        <th colspan="3">Desistentes</th>
                        <th colspan="3">Avaliados</th>
                        <th colspan="3">Aprovados</th>
                        <th colspan="3">Reprovados</th>
                    </tr>
                    <tr>
                        <?php foreach (['Efectivo','Desistentes','Avaliados','Aprovados','Reprovados'] as $_): ?>
                            <th>M</th><th>F</th><th>T</th>
                        <?php endforeach; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $currentCourse = null;
                    foreach ($schoolStats['details'] as $row):
                        if ($currentCourse !== $row['course_name']):
                            $currentCourse = $row['course_name'];
                    ?>
                    <tr class="stats-group-header">
                        <td colspan="18"><i class="fa-solid fa-book fa-xs me-1"></i><?= esc($row['course_name']) ?> — <?= esc($row['course_code'] ?? '') ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td><?= esc($row['course_name']) ?></td>
                        <td><?= esc($row['level_name']) ?></td>
                        <td><strong><?= $row['total_classes'] ?></strong></td>
                        <td class="num-m"><?= $row['total_male'] ?></td>
                        <td class="num-f"><?= $row['total_female'] ?></td>
                        <td class="num-t"><?= $row['total'] ?></td>
                        <td class="num-m"><?= $row['desistentes_male'] ?></td>
                        <td class="num-f"><?= $row['desistentes_female'] ?></td>
                        <td class="num-t"><?= $row['desistentes_total'] ?></td>
                        <td class="num-m"><?= $row['avaliados_male'] ?></td>
                        <td class="num-f"><?= $row['avaliados_female'] ?></td>
                        <td class="num-t"><?= $row['avaliados_total'] ?></td>
                        <td class="num-m"><?= $row['aprovados_male'] ?></td>
                        <td class="num-f"><?= $row['aprovados_female'] ?></td>
                        <td class="num-t"><?= $row['aprovados_total'] ?></td>
                        <td class="num-m"><?= $row['reprovados_male'] ?></td>
                        <td class="num-f"><?= $row['reprovados_female'] ?></td>
                        <td class="num-t"><?= $row['reprovados_total'] ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <?php if (!empty($schoolStats['totals'])): $t = $schoolStats['totals']; ?>
                <tfoot>
                    <tr>
                        <td colspan="2" style="text-align:left"><strong>TOTAIS GERAIS</strong></td>
                        <td><strong><?= $t['total_classes'] ?></strong></td>
                        <td><?= $t['total_male'] ?></td>
                        <td><?= $t['total_female'] ?></td>
                        <td><strong><?= $t['total'] ?></strong></td>
                        <td><?= $t['desistentes_male'] ?></td>
                        <td><?= $t['desistentes_female'] ?></td>
                        <td><strong><?= $t['desistentes_total'] ?></strong></td>
                        <td><?= $t['avaliados_male'] ?></td>
                        <td><?= $t['avaliados_female'] ?></td>
                        <td><strong><?= $t['avaliados_total'] ?></strong></td>
                        <td>—</td><td>—</td><td>—</td>
                        <td>—</td><td>—</td><td>—</td>
                    </tr>
                </tfoot>
                <?php endif; ?>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<?php $this->endSection(); ?>

<!-- ============================================================
     SCRIPTS DO DASHBOARD
     ============================================================ -->
<?php $this->section('scripts'); ?>
<script>
(function(){
    'use strict';

    /* ---- Live Clock ---- */
    (function tickClock(){
        const el = document.getElementById('liveClock');
        if(!el) return;
        const now = new Date();
        el.textContent = now.toLocaleTimeString('pt-AO',{hour:'2-digit',minute:'2-digit',hour12:false});
        setTimeout(tickClock, 30000);
    })();

    /* ---- Paleta de cores ---- */
    const C = {
        blue   : '#3B7FE8',
        green  : '#16A87D',
        red    : '#E84646',
        amber  : '#E8A020',
        purple : '#7C4DFF',
        teal   : '#0891B2',
        pink   : '#E84683',
        orange : '#EA580C',
        slate  : '#6B7A99',
        border : '#E2E8F4',
        surface: '#F5F7FC',
        alpha  : (hex, a) => hex + Math.round(a*255).toString(16).padStart(2,'0')
    };

    /* ---- Helper: criar gradiente vertical ---- */
    function vGrad(ctx, top, bottom){
        const g = ctx.createLinearGradient(0,0,0,ctx.canvas.offsetHeight||280);
        g.addColorStop(0, top);
        g.addColorStop(1, bottom);
        return g;
    }

    /* ---- Defaults globais Chart.js ---- */
    Chart.defaults.font.family = "'Sora', sans-serif";
    Chart.defaults.font.size   = 11;
    Chart.defaults.color       = '#6B7A99';
    Chart.defaults.plugins.legend.labels.boxWidth = 10;
    Chart.defaults.plugins.legend.labels.padding  = 14;

    /* ==========================================================
       1. MATRÍCULAS — últimos 12 meses (Line)
       ========================================================== */
    <?php if(!empty($enrollmentChart)): ?>
    (function(){
        const ctx = document.getElementById('enrollmentChart');
        if(!ctx) return;
        const c = ctx.getContext('2d');
        new Chart(ctx, {
            type:'line',
            data:{
                labels: <?= json_encode($enrollmentChart['months']) ?>,
                datasets:[{
                    label:'Matrículas',
                    data: <?= json_encode($enrollmentChart['counts']) ?>,
                    borderColor: C.blue,
                    backgroundColor: vGrad(c, C.alpha(C.blue,.22), C.alpha(C.blue,.02)),
                    borderWidth:2.5,
                    pointRadius:3,
                    pointHoverRadius:6,
                    pointBackgroundColor:'#fff',
                    pointBorderColor: C.blue,
                    pointBorderWidth:2,
                    fill:true,
                    tension:.4
                }]
            },
            options:{
                responsive:true, maintainAspectRatio:false,
                plugins:{legend:{display:false}, tooltip:{mode:'index',intersect:false}},
                scales:{
                    x:{ grid:{color:C.border,drawBorder:false}, ticks:{maxRotation:45,minRotation:0} },
                    y:{ grid:{color:C.border,drawBorder:false}, beginAtZero:true, ticks:{precision:0} }
                }
            }
        });
    })();
    <?php endif; ?>

    /* ==========================================================
       2. GÉNERO (Doughnut)
       ========================================================== */
    <?php if(!empty($genderChart) && ($genderChart['male']+$genderChart['female']>0)): ?>
    (function(){
        const ctx = document.getElementById('genderChart');
        if(!ctx) return;
        new Chart(ctx,{
            type:'doughnut',
            data:{
                labels:['Masculino','Feminino'],
                datasets:[{
                    data:[<?= $genderChart['male'] ?>, <?= $genderChart['female'] ?>],
                    backgroundColor:[C.blue, C.pink],
                    borderColor:'#fff', borderWidth:3,
                    hoverOffset:6
                }]
            },
            options:{
                responsive:true, maintainAspectRatio:false,
                cutout:'72%',
                plugins:{
                    legend:{position:'bottom'},
                    tooltip:{callbacks:{label:function(c){
                        const t=c.dataset.data.reduce((a,b)=>a+b,0);
                        return ` ${c.label}: ${c.raw} (${Math.round(c.raw/t*100)}%)`;
                    }}}
                }
            }
        });
    })();
    <?php endif; ?>

    /* ==========================================================
       3. PROPINAS — 6 meses (Bar)
       ========================================================== */
    <?php if(!empty($feeChart)): ?>
    (function(){
        const ctx = document.getElementById('feeChart');
        if(!ctx) return;
        new Chart(ctx,{
            type:'bar',
            data:{
                labels: <?= json_encode($feeChart['months']) ?>,
                datasets:[
                    {label:'Recebido', data:<?= json_encode($feeChart['collected']) ?>, backgroundColor:C.alpha(C.green,.8), borderRadius:5},
                    {label:'Pendente', data:<?= json_encode($feeChart['pending'])   ?>, backgroundColor:C.alpha(C.amber,.7), borderRadius:5}
                ]
            },
            options:{
                responsive:true, maintainAspectRatio:false,
                plugins:{legend:{position:'bottom'}, tooltip:{mode:'index',intersect:false}},
                scales:{
                    x:{grid:{display:false}},
                    y:{grid:{color:C.border,drawBorder:false}, beginAtZero:true, ticks:{precision:0}}
                }
            }
        });
    })();
    <?php endif; ?>

    /* ==========================================================
       4. PRESENÇAS — 7 dias (Bar empilhado)
       ========================================================== */
    <?php if(!empty($attendanceChart)): ?>
    (function(){
        const ctx = document.getElementById('attendanceChart');
        if(!ctx) return;
        new Chart(ctx,{
            type:'bar',
            data:{
                labels: <?= json_encode($attendanceChart['days']) ?>,
                datasets:[
                    {label:'Presente', data:<?= json_encode($attendanceChart['present']) ?>, backgroundColor:C.alpha(C.green,.82), borderRadius:4},
                    {label:'Falta',    data:<?= json_encode($attendanceChart['absent'])  ?>, backgroundColor:C.alpha(C.red,.65),   borderRadius:4}
                ]
            },
            options:{
                responsive:true, maintainAspectRatio:false,
                plugins:{legend:{position:'bottom'}, tooltip:{mode:'index',intersect:false}},
                scales:{
                    x:{stacked:true, grid:{display:false}},
                    y:{stacked:true, grid:{color:C.border,drawBorder:false}, beginAtZero:true, ticks:{precision:0}}
                }
            }
        });
    })();
    <?php endif; ?>

    /* ==========================================================
       5. DESEMPENHO ACADÉMICO (Polar Area)
       ========================================================== */
    <?php if(!empty($performanceChart)): ?>
    (function(){
        const ctx = document.getElementById('performanceChart');
        if(!ctx) return;
        new Chart(ctx,{
            type:'polarArea',
            data:{
                labels:['Excelente (≥17)','Bom (14-16)','Satisfatório (10-13)','Insuficiente (<10)'],
                datasets:[{
                    data:[
                        <?= $performanceChart['excelente'] ?>,
                        <?= $performanceChart['bom'] ?>,
                        <?= $performanceChart['satisfatorio'] ?>,
                        <?= $performanceChart['insuficiente'] ?>
                    ],
                    backgroundColor:[
                        C.alpha(C.green,.75),
                        C.alpha(C.blue,.75),
                        C.alpha(C.amber,.75),
                        C.alpha(C.red,.75)
                    ],
                    borderColor:'#fff', borderWidth:2
                }]
            },
            options:{
                responsive:true, maintainAspectRatio:false,
                plugins:{legend:{position:'bottom',labels:{font:{size:10}}}}
            }
        });
    })();
    <?php endif; ?>

    /* ==========================================================
       6. CURSOS POR TIPO (Pie)
       ========================================================== */
    <?php if(!empty($courseTypeChart)): ?>
    (function(){
        const ctx = document.getElementById('courseTypeChart');
        if(!ctx) return;
        const palette = [C.blue, C.green, C.amber, C.teal, C.purple, C.orange, C.red, C.slate];
        new Chart(ctx,{
            type:'pie',
            data:{
                labels: <?= json_encode($courseTypeChart['labels']) ?>,
                datasets:[{
                    data: <?= json_encode($courseTypeChart['counts']) ?>,
                    backgroundColor: palette.slice(0, <?= count($courseTypeChart['labels']) ?>),
                    borderColor:'#fff', borderWidth:2, hoverOffset:6
                }]
            },
            options:{
                responsive:true, maintainAspectRatio:false,
                plugins:{legend:{position:'bottom',labels:{font:{size:10}}}}
            }
        });
    })();
    <?php endif; ?>

    /* ==========================================================
       7. STATUS DE MATRÍCULAS (Doughnut)
       ========================================================== */
    <?php if(!empty($enrollmentStatusChart)): ?>
    (function(){
        const ctx = document.getElementById('enrollmentStatusChart');
        if(!ctx) return;
        const palette = [C.green, C.amber, C.red, C.blue, C.slate, C.teal];
        new Chart(ctx,{
            type:'doughnut',
            data:{
                labels: <?= json_encode($enrollmentStatusChart['labels']) ?>,
                datasets:[{
                    data: <?= json_encode($enrollmentStatusChart['data']) ?>,
                    backgroundColor: palette.slice(0, <?= count($enrollmentStatusChart['labels']) ?>),
                    borderColor:'#fff', borderWidth:3, hoverOffset:6
                }]
            },
            options:{
                responsive:true, maintainAspectRatio:false,
                cutout:'68%',
                plugins:{legend:{position:'bottom',labels:{font:{size:10}}}}
            }
        });
    })();
    <?php endif; ?>

    /* ==========================================================
       8. RESULTADOS POR SEMESTRE (Bar agrupado)
       ========================================================== */
    <?php if(!empty($semesterResultsChart)): ?>
    (function(){
        const ctx = document.getElementById('semesterResultsChart');
        if(!ctx) return;
        new Chart(ctx,{
            type:'bar',
            data:{
                labels: <?= json_encode($semesterResultsChart['labels']) ?>,
                datasets:[
                    {label:'Aprovados', data:<?= json_encode($semesterResultsChart['approved']) ?>, backgroundColor:C.alpha(C.green,.8), borderRadius:5},
                    {label:'Reprovados',data:<?= json_encode($semesterResultsChart['failed'])   ?>, backgroundColor:C.alpha(C.red,.7),   borderRadius:5},
                    {label:'Recurso',   data:<?= json_encode($semesterResultsChart['appeal'])   ?>, backgroundColor:C.alpha(C.amber,.75),borderRadius:5}
                ]
            },
            options:{
                responsive:true, maintainAspectRatio:false,
                plugins:{legend:{position:'bottom'}, tooltip:{mode:'index',intersect:false}},
                scales:{
                    x:{grid:{display:false}},
                    y:{grid:{color:C.border,drawBorder:false}, beginAtZero:true, ticks:{precision:0}}
                }
            }
        });
    })();
    <?php endif; ?>

    /* ==========================================================
       9. TOP DISCIPLINAS — Carga Horária (Bar horizontal)
       ========================================================== */
    <?php if(!empty($topDisciplinesChart)): ?>
    (function(){
        const ctx = document.getElementById('topDisciplinesChart');
        if(!ctx) return;
        new Chart(ctx,{
            type:'bar',
            data:{
                labels: <?= json_encode($topDisciplinesChart['labels']) ?>,
                datasets:[{
                    label:'Horas',
                    data: <?= json_encode($topDisciplinesChart['data']) ?>,
                    backgroundColor: <?= json_encode($topDisciplinesChart['labels']) ?>.map((_,i)=>{
                        const cols=[C.blue,C.teal,C.green,C.amber,C.purple,C.orange,C.red,C.slate,C.pink,C.blue];
                        return cols[i%cols.length]+'CC';
                    }),
                    borderRadius:5
                }]
            },
            options:{
                indexAxis:'y',
                responsive:true, maintainAspectRatio:false,
                plugins:{legend:{display:false}},
                scales:{
                    x:{grid:{color:C.border,drawBorder:false}, beginAtZero:true},
                    y:{grid:{display:false}, ticks:{font:{size:10}}}
                }
            }
        });
    })();
    <?php endif; ?>

})();
</script>
<?php $this->endSection(); ?>