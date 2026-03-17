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

/* ---- Cores adicionais ---- */
.text-pink { color: #E84683 !important; }
.bg-pink { background-color: #E84683 !important; }
.badge-ci.pink { background: rgba(232,70,131,0.15); color: #E84683; }

/* ---- Linha destacada para exames de hoje ---- */
.row-highlight { background: rgba(232,160,32,0.05); border-left: 3px solid var(--warning); }

/* ---- Ajustes para os cards ---- */
.stat-card { transition: transform 0.2s; }
.stat-card:hover { transform: translateY(-2px); }

/* ---- Progress bars ---- */
.progress { background: var(--border); border-radius: 4px; overflow: hidden; }
.progress-bar { border-radius: 4px; transition: width 0.3s ease; }

/* ---- Teacher initials ---- */
.teacher-initials {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: 600;
    font-size: 1.2rem;
    text-transform: uppercase;
    color: white;
    flex-shrink: 0;
}
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
                <?php if (!empty($academicYear) && is_array($academicYear)): ?>
                    <span class="sep">|</span>
                    <i class="fa-solid fa-book-open"></i>
                    <span><?= esc($academicYear['year_name'] ?? $academicYear['academic_year'] ?? '') ?></span>
                <?php endif; ?>
            </div>
        </div>

        <!-- Filtro Ano Letivo -->
        <?php if (!empty($academicYears) && is_array($academicYears)): ?>
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <form method="get" class="d-flex align-items-center gap-2" id="yearFilterForm">
                <label class="filter-label mb-0" style="white-space:nowrap;color:rgba(255,255,255,.65)">
                    <i class="fa-solid fa-filter fa-xs me-1"></i>Ano Letivo
                </label>
                <select name="academic_year" class="filter-select" style="min-width:130px"
                        onchange="document.getElementById('yearFilterForm').submit()">
                    <?php foreach ($academicYears as $ay): ?>
                        <?php if (is_array($ay)): ?>
                        <option value="<?= $ay['id'] ?? '' ?>" <?= ($selectedYear == ($ay['id'] ?? '')) ? 'selected' : '' ?>>
                            <?= esc($ay['year_name'] ?? $ay['academic_year'] ?? '') ?>
                        </option>
                        <?php endif; ?>
                    <?php endforeach; ?>
                </select>
            </form>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- ============================================================
     ROW 1 — STAT CARDS PRINCIPAIS (CORRIGIDO)
     ============================================================ -->
<div class="stat-grid mb-4">

    <!-- Alunos -->
    <?php if (isset($totalStudents)): ?>
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-user-graduate"></i>
        </div>
        <div>
            <div class="stat-label">Alunos Ativos</div>
            <div class="stat-value"><?= number_format((int)($totalStudents ?? 0)) ?></div>
            <div class="stat-sub">
                <?php if (!empty($genderChart) && is_array($genderChart)): ?>
                    <span class="text-accent"><i class="fas fa-mars"></i> <?= (int)($genderChart['male'] ?? 0) ?></span>
                    <span class="mx-1">|</span>
                    <span class="text-pink"><i class="fas fa-venus"></i> <?= (int)($genderChart['female'] ?? 0) ?></span>
                <?php endif; ?>
                <span>activos</span>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Matrículas -->
    <?php if (isset($activeEnrollments)): ?>
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-id-card"></i>
        </div>
        <div>
            <div class="stat-label">Matrículas Ativas</div>
            <div class="stat-value"><?= number_format((int)($activeEnrollments ?? 0)) ?></div>
            <div class="stat-sub">
                <?php if (!empty($enrollmentStatusChart)): ?>
                    <i class="fas fa-circle-check"></i>Activas neste ano
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Professores -->
    <?php if (isset($totalTeachers)): ?>
    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-chalkboard-teacher"></i>
        </div>
        <div>
            <div class="stat-label">Professores</div>
            <div class="stat-value"><?= number_format((int)($totalTeachers ?? 0)) ?></div>
            <div class="stat-sub">
                <?php if (isset($studentsPerTeacher)): ?>
                    <i class="fas fa-ratio"></i><?= number_format((float)($studentsPerTeacher ?? 0), 1) ?> al./prof.
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Turmas -->
    <?php if (isset($totalClasses)): ?>
    <div class="stat-card">
        <div class="stat-icon purple">
            <i class="fas fa-door-open"></i>
        </div>
        <div>
            <div class="stat-label">Turmas</div>
            <div class="stat-value"><?= number_format((int)($totalClasses ?? 0)) ?></div>
            <div class="stat-sub">
                <?php if (isset($occupancyRate)): ?>
                    <i class="fas fa-chart-pie"></i><?= number_format((float)($occupancyRate ?? 0), 1) ?>% ocupação
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Cursos -->
    <?php if (isset($totalCourses)): ?>
    <div class="stat-card">
        <div class="stat-icon teal">
            <i class="fas fa-book"></i>
        </div>
        <div>
            <div class="stat-label">Cursos</div>
            <div class="stat-value"><?= number_format((int)($totalCourses ?? 0)) ?></div>
            <div class="stat-sub">
                <?php if (isset($totalDisciplines)): ?>
                    <i class="fas fa-layer-group"></i><?= number_format((int)($totalDisciplines ?? 0)) ?> disciplinas
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Financeiro / Presença -->
    <?php if (isset($feeStats)): ?>
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-money-bill-wave"></i>
        </div>
        <div>
            <div class="stat-label">Recebido</div>
            <?php 
            $paidAmount = is_object($feeStats) ? ($feeStats->paid_amount ?? 0) : (is_array($feeStats) ? ($feeStats['paid_amount'] ?? 0) : 0);
            $paidPercentage = is_object($feeStats) ? ($feeStats->paid_percentage ?? 0) : (is_array($feeStats) ? ($feeStats['paid_percentage'] ?? 0) : 0);
            ?>
            <div class="stat-value"><?= number_format((float)$paidAmount, 0, ',', '.') ?></div>
            <div class="stat-sub">
                <i class="fas fa-percent"></i><?= number_format((float)$paidPercentage, 1) ?>% do total
            </div>
        </div>
    </div>
    <?php elseif (isset($attendanceRate)): ?>
    <div class="stat-card">
        <div class="stat-icon teal">
            <i class="fas fa-clipboard-check"></i>
        </div>
        <div>
            <div class="stat-label">Presença Média</div>
            <div class="stat-value"><?= number_format((float)($attendanceRate ?? 0), 1) ?>%</div>
            <div class="stat-sub">
                <i class="fas fa-calendar-alt"></i>últimos 30 dias
            </div>
        </div>
    </div>
    <?php endif; ?>

</div><!-- /stat cards -->

<!-- ============================================================
     ROW 2 — INDICADORES SECUNDÁRIOS
     ============================================================ -->
<?php if (isset($approvalRate) || isset($avgStudentsPerClass) || isset($examsToday) || isset($overduePayments) || isset($totalStaff)): ?>
<div class="row g-3 mb-4">

    <?php if (isset($approvalRate)): ?>
    <div class="col-md-3">
        <div class="ci-card">
            <div class="ci-card-body text-center py-3">
                <div style="font-size:1.6rem;font-weight:700;font-family:var(--font-mono);color:var(--success)"><?= number_format((float)($approvalRate ?? 0), 1) ?>%</div>
                <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-muted);margin-top:.15rem">Taxa de Aprovação</div>
                <?php if (isset($failureRate)): ?>
                <div style="font-size:.72rem;color:var(--danger);margin-top:.3rem"><i class="fa-solid fa-xmark me-1"></i><?= number_format((float)($failureRate ?? 0), 1) ?>% reprovação</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (isset($avgStudentsPerClass)): ?>
    <div class="col-md-3">
        <div class="ci-card">
            <div class="ci-card-body text-center py-3">
                <div style="font-size:1.6rem;font-weight:700;font-family:var(--font-mono);color:var(--accent)"><?= number_format((float)($avgStudentsPerClass ?? 0), 1) ?></div>
                <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-muted);margin-top:.15rem">Alunos / Turma</div>
                <?php if (isset($totalCapacity)): ?>
                <div style="font-size:.72rem;color:var(--text-muted);margin-top:.3rem"><i class="fa-solid fa-users me-1"></i>Cap. <?= number_format((int)($totalCapacity ?? 0)) ?></div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (isset($examsToday)): ?>
    <div class="col-md-3">
        <div class="ci-card">
            <div class="ci-card-body text-center py-3">
                <div style="font-size:1.6rem;font-weight:700;font-family:var(--font-mono);color:var(--purple, #7C4DFF)"><?= (int)($examsToday ?? 0) ?></div>
                <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-muted);margin-top:.15rem">Exames Hoje</div>
                <?php if (isset($examsThisWeek)): ?>
                <div style="font-size:.72rem;color:var(--text-muted);margin-top:.3rem"><i class="fa-regular fa-calendar-week me-1"></i><?= (int)($examsThisWeek ?? 0) ?> esta semana</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (isset($overduePayments)): ?>
    <div class="col-md-3">
        <div class="ci-card">
            <div class="ci-card-body text-center py-3">
                <div style="font-size:1.6rem;font-weight:700;font-family:var(--font-mono);color:var(--danger)"><?= (int)($overduePayments ?? 0) ?></div>
                <div style="font-size:.7rem;font-weight:700;text-transform:uppercase;letter-spacing:.08em;color:var(--text-muted);margin-top:.15rem">Propinas Vencidas</div>
                <?php if (isset($currentMonthPayments)): ?>
                <div style="font-size:.72rem;color:var(--success);margin-top:.3rem"><i class="fa-solid fa-arrow-trend-up me-1"></i><?= number_format((float)($currentMonthPayments ?? 0), 0, ',', '.') ?> Kz este mês</div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php elseif (isset($totalStaff)): ?>
    <div class="col-md-3">
        <div class="ci-card">
            <div class="ci-card-body text-center py-3">
                <div style="font-size:1.6rem;font-weight:700;font-family:var(--font-mono);color:var(--text-secondary)"><?= number_format((int)($totalStaff ?? 0)) ?></div>
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
<div class="row g-3 mb-4">

    <!-- Matrículas (12 meses) -->
    <?php if (!empty($enrollmentChart) && is_array($enrollmentChart)): ?>
    <div class="col-12 col-lg-8">
        <div class="ci-card h-100">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fa-solid fa-chart-line text-accent"></i>Matrículas — Últimos 12 Meses
                </div>
                <span class="badge-ci primary"><?= array_sum($enrollmentChart['counts'] ?? []) ?> total</span>
            </div>
            <div class="ci-card-body">
                <div class="chart-wrap">
                    <canvas id="enrollmentChart" style="height: 250px;"></canvas>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Género -->
    <?php if (!empty($genderChart) && is_array($genderChart) && (($genderChart['male'] ?? 0) + ($genderChart['female'] ?? 0) > 0)): ?>
    <div class="col-12 col-sm-6 col-lg-4">
        <div class="ci-card h-100">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fa-solid fa-venus-mars text-accent"></i>Distribuição por Género
                </div>
                <?php if (!empty($genderRatio)): ?>
                <span class="badge-ci success"><?= $genderRatio ?? '0:0' ?> M:F</span>
                <?php endif; ?>
            </div>
            <div class="ci-card-body">
                <div class="chart-wrap-sm">
                    <canvas id="genderChart" style="height: 200px;"></canvas>
                </div>
                <div class="d-flex justify-content-center gap-3 mt-2">
                    <div style="font-size:.78rem;color:var(--text-secondary)">
                        <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#3B7FE8;margin-right:4px"></span>
                        Masculino <strong><?= (int)($genderChart['male'] ?? 0) ?></strong>
                    </div>
                    <div style="font-size:.78rem;color:var(--text-secondary)">
                        <span style="display:inline-block;width:10px;height:10px;border-radius:50%;background:#E84683;margin-right:4px"></span>
                        Feminino <strong><?= (int)($genderChart['female'] ?? 0) ?></strong>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>

<!-- ============================================================
     ROW 4 — FINANCEIRO + PRESENÇAS + TURMAS
     ============================================================ -->
<div class="row g-3 mb-4">

    <!-- Gráfico Financeiro (6 meses) -->
    <?php if (!empty($feeChart) && is_array($feeChart)): ?>
    <div class="col-lg-4">
        <div class="ci-card h-100">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fa-solid fa-chart-bar text-accent"></i>Propinas — 6 Meses
                </div>
                <span class="badge-ci success"><?= number_format(array_sum($feeChart['collected'] ?? []), 0, ',', '.') ?> Kz</span>
            </div>
            <div class="ci-card-body">
                <div class="chart-wrap">
                    <canvas id="feeChart" style="height: 200px;"></canvas>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Presenças 7 dias -->
    <?php if (!empty($attendanceChart) && is_array($attendanceChart)): ?>
    <div class="col-lg-4">
        <div class="ci-card h-100">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fa-solid fa-clipboard-check text-accent"></i>Presenças — 7 Dias
                </div>
                <?php if (isset($attendanceRate)): ?>
                <span class="badge-ci success"><?= number_format((float)($attendanceRate ?? 0), 1) ?>% este mês</span>
                <?php endif; ?>
            </div>
            <div class="ci-card-body">
                <div class="chart-wrap">
                    <canvas id="attendanceChart" style="height: 200px;"></canvas>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Turmas por Turno -->
    <?php if (!empty($classesByShift) && is_array($classesByShift)): ?>
    <div class="col-lg-4">
        <div class="ci-card h-100">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fa-solid fa-clock text-accent"></i>Turmas por Turno
                </div>
                <span class="badge-ci info"><?= array_sum($classesByShift) ?> turmas</span>
            </div>
            <div class="ci-card-body">
                <?php
                $shiftColors = [
                    'Manhã' => 'blue',
                    'Tarde' => 'orange',
                    'Noite' => 'purple',
                    'Integral' => 'teal'
                ];
                $totalShifts = max(array_sum($classesByShift), 1);
                foreach ($classesByShift as $shift => $count): 
                    $color = $shiftColors[$shift] ?? 'secondary';
                ?>
                <div class="d-flex align-items-center mb-3">
                    <span class="badge-ci <?= $color ?>" style="width: 80px;"><?= $shift ?></span>
                    <div class="flex-grow-1 mx-2">
                        <div class="progress" style="height: 8px;">
                            <div class="progress-bar bg-<?= $color ?>" style="width: <?= ((int)$count / $totalShifts) * 100 ?>%"></div>
                        </div>
                    </div>
                    <span class="fw-bold"><?= (int)$count ?></span>
                </div>
                <?php endforeach; ?>
                
                <?php if (isset($classesWithTeacher) && isset($totalClasses)): ?>
                <div class="mt-3 pt-3 border-top">
                    <div class="d-flex justify-content-between">
                        <span class="text-muted">Com diretor de turma:</span>
                        <span class="fw-bold"><?= (int)($classesWithTeacher ?? 0) ?> / <?= (int)($totalClasses ?? 0) ?></span>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>

<!-- ============================================================
     ROW 5 — DESEMPENHO + CURSOS + STATUS
     ============================================================ -->
<div class="row g-3 mb-4">

    <!-- Desempenho Acadêmico -->
    <?php if (!empty($performanceChart) && is_array($performanceChart)): ?>
    <div class="col-lg-4">
        <div class="ci-card h-100">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fa-solid fa-medal text-accent"></i>Desempenho Académico
                </div>
            </div>
            <div class="ci-card-body">
                <div class="chart-wrap-sm">
                    <canvas id="performanceChart" style="height: 200px;"></canvas>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Cursos por Tipo -->
    <?php if (!empty($courseTypeChart) && is_array($courseTypeChart)): ?>
    <div class="col-lg-4">
        <div class="ci-card h-100">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fa-solid fa-book text-accent"></i>Cursos por Tipo
                </div>
                <?php if (isset($totalCourses)): ?>
                <span class="badge-ci amber"><?= number_format((int)($totalCourses ?? 0)) ?> cursos</span>
                <?php endif; ?>
            </div>
            <div class="ci-card-body">
                <div class="chart-wrap-sm">
                    <canvas id="courseTypeChart" style="height: 200px;"></canvas>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Status das Matrículas -->
    <?php if (!empty($enrollmentStatusChart) && is_array($enrollmentStatusChart)): ?>
    <div class="col-lg-4">
        <div class="ci-card h-100">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fa-solid fa-circle-half-stroke text-accent"></i>Status das Matrículas
                </div>
            </div>
            <div class="ci-card-body">
                <div class="chart-wrap-sm">
                    <canvas id="enrollmentStatusChart" style="height: 200px;"></canvas>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

</div>

<!-- ============================================================
     ROW 6 — RESULTADOS SEMESTRAIS + TOP DISCIPLINAS
     ============================================================ -->
<?php if (!empty($semesterResultsChart) && is_array($semesterResultsChart) || !empty($topDisciplinesChart) && is_array($topDisciplinesChart)): ?>
<div class="row g-3 mb-4">

    <?php if (!empty($semesterResultsChart) && is_array($semesterResultsChart)): ?>
    <div class="col-lg-7">
        <div class="ci-card">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fa-solid fa-chart-column text-accent"></i>Resultados por Semestre
                </div>
            </div>
            <div class="ci-card-body">
                <div class="chart-wrap">
                    <canvas id="semesterResultsChart" style="height: 250px;"></canvas>
                </div>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <?php if (!empty($topDisciplinesChart) && is_array($topDisciplinesChart)): ?>
    <div class="col-lg-5">
        <div class="ci-card">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fa-solid fa-list-ol text-accent"></i>Top Disciplinas — Carga Horária
                </div>
                <?php if (isset($totalWorkload)): ?>
                <span class="badge-ci info"><?= number_format((float)($totalWorkload ?? 0)) ?>h total</span>
                <?php endif; ?>
            </div>
            <div class="ci-card-body">
                <div class="chart-wrap">
                    <canvas id="topDisciplinesChart" style="height: 250px;"></canvas>
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
<div class="row g-3 mb-4">

<!-- Matrículas Recentes -->
<?php if (!empty($recentEnrollments) && is_array($recentEnrollments)): ?>
<div class="col-lg-7">
    <div class="ci-card">
        <div class="ci-card-header">
            <div class="ci-card-title">
                <i class="fas fa-clock-rotate-left"></i>
                <span>Matrículas Recentes</span>
            </div>
            <?php if (function_exists('can') && can('enrollments.list')): ?>
            <a href="<?= base_url('admin/enrollments') ?>" class="btn-ci outline btn-sm">
                Ver todas <i class="fas fa-arrow-right ms-1"></i>
            </a>
            <?php endif; ?>
        </div>
        <div class="ci-card-body p0">
            <div class="table-responsive">
                <table class="ci-table">
                    <thead>
                        <tr>
                            <th>Aluno</th>
                            <th>Turma/Nível</th>
                            <th>Data</th>
                            <th class="center">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach (array_slice($recentEnrollments, 0, 8) as $enroll): ?>
                            <?php if (is_array($enroll)): ?>
                            <tr>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="teacher-initials me-2" style="width: 35px; height: 35px; font-size: 0.9rem;">
                                            <?= strtoupper(substr(($enroll['first_name'] ?? ''), 0, 1) . substr(($enroll['last_name'] ?? ''), 0, 1)) ?>
                                        </div>
                                        <div>
                                            <strong><?= esc(($enroll['first_name'] ?? '') . ' ' . ($enroll['last_name'] ?? '')) ?></strong>
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <?php if (!empty($enroll['class_name'])): ?>
                                        <span class="fw-semibold"><?= esc($enroll['class_name']) ?></span>
                                        <?php if (!empty($enroll['level_name'])): ?>
                                            <br><small class="text-muted"><?= esc($enroll['level_name']) ?></small>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <span class="code-badge">
                                        <?= isset($enroll['created_at']) ? date('d/m/Y', strtotime($enroll['created_at'])) : '-' ?>
                                    </span>
                                </td>
                                <td class="center">
                                    <?php
                                    $statusMap = [
                                        'Ativo' => 'success',
                                        'Pendente' => 'warning',
                                        'Concluído' => 'info',
                                        'Transferido' => 'primary',
                                        'Cancelado' => 'danger'
                                    ];
                                    $status = $enroll['status'] ?? '';
                                    $statusClass = $statusMap[$status] ?? 'secondary';
                                    ?>
                                    <span class="badge-ci <?= $statusClass ?>">
                                        <?= $status ?: '-' ?>
                                    </span>
                                </td>
                            </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        
                        <?php if (count($recentEnrollments) == 0): ?>
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <div class="empty-state">
                                    <i class="fas fa-users"></i>
                                    <p class="text-muted">Nenhuma matrícula recente</p>
                                </div>
                            </td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
    <!-- Próximos Exames -->
    <?php if (!empty($upcomingExams) && is_array($upcomingExams)): ?>
    <div class="col-lg-5">
        <div class="ci-card">
            <div class="ci-card-header">
                <div class="ci-card-title">
                    <i class="fa-solid fa-calendar-check text-accent"></i>Próximos Exames
                </div>
                <?php if (function_exists('can') && can('exams.calendar')): ?>
                <a href="<?= base_url('admin/exams') ?>" class="btn-ci outline btn-sm">
                    Ver todos <i class="fas fa-arrow-right ms-1"></i>
                </a>
                <?php endif; ?>
            </div>
            <div class="ci-card-body p0">
                <ul class="ci-list">
                    <?php foreach (array_slice($upcomingExams, 0, 8) as $exam):
                        if (!is_array($exam)) continue;
                        $examDate = $exam['exam_date'] ?? '';
                        $isToday = ($examDate === date('Y-m-d'));
                    ?>
                    <li class="ci-list-item">
                        <div style="min-width:0;flex:1">
                            <div class="ci-list-item-name"><?= esc($exam['discipline_name'] ?? '—') ?></div>
                            <div class="ci-list-item-sub">
                                <i class="fa-solid fa-door-open fa-xs"></i><?= esc($exam['class_name'] ?? '—') ?>
                                <?php if (!empty($exam['board_name'])): ?>
                                    <span style="opacity:.4">·</span><?= esc($exam['board_name']) ?>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="text-end flex-shrink-0">
                            <span class="ci-list-badge <?= $isToday ? 'today' : 'future' ?>">
                                <?= $isToday ? 'Hoje' : (isset($examDate) ? date('d/m', strtotime($examDate)) : '-') ?>
                            </span>
                            <?php if (!empty($exam['start_time'])): ?>
                            <div class="ci-list-date"><?= date('H:i', strtotime($exam['start_time'])) ?></div>
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
<?php if (!empty($schoolStats['details']) && is_array($schoolStats['details'])): ?>
<div class="ci-card mb-4">
    <div class="ci-card-header">
        <div class="ci-card-title">
            <i class="fa-solid fa-table text-accent"></i>Estatística Geral da Escola
        </div>
        <span class="badge-ci info"><?= is_array($academicYear) ? ($academicYear['year_name'] ?? ($academicYear['academic_year'] ?? '')) : '' ?></span>
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
                        if (!is_array($row)) continue;
                        if ($currentCourse !== ($row['course_name'] ?? '')):
                            $currentCourse = $row['course_name'] ?? '';
                    ?>
                    <tr class="stats-group-header">
                        <td colspan="18"><i class="fa-solid fa-book fa-xs me-1"></i><?= esc($row['course_name'] ?? '') ?> — <?= esc($row['course_code'] ?? '') ?></td>
                    </tr>
                    <?php endif; ?>
                    <tr>
                        <td><?= esc($row['course_name'] ?? '') ?></td>
                        <td><?= esc($row['level_name'] ?? '') ?></td>
                        <td><strong><?= (int)($row['total_classes'] ?? 0) ?></strong></td>
                        <td class="num-m"><?= (int)($row['total_male'] ?? 0) ?></td>
                        <td class="num-f"><?= (int)($row['total_female'] ?? 0) ?></td>
                        <td class="num-t"><?= (int)($row['total'] ?? 0) ?></td>
                        <td class="num-m"><?= (int)($row['desistentes_male'] ?? 0) ?></td>
                        <td class="num-f"><?= (int)($row['desistentes_female'] ?? 0) ?></td>
                        <td class="num-t"><?= (int)($row['desistentes_total'] ?? 0) ?></td>
                        <td class="num-m"><?= (int)($row['avaliados_male'] ?? 0) ?></td>
                        <td class="num-f"><?= (int)($row['avaliados_female'] ?? 0) ?></td>
                        <td class="num-t"><?= (int)($row['avaliados_total'] ?? 0) ?></td>
                        <td class="num-m"><?= (int)($row['aprovados_male'] ?? 0) ?></td>
                        <td class="num-f"><?= (int)($row['aprovados_female'] ?? 0) ?></td>
                        <td class="num-t"><?= (int)($row['aprovados_total'] ?? 0) ?></td>
                        <td class="num-m"><?= (int)($row['reprovados_male'] ?? 0) ?></td>
                        <td class="num-f"><?= (int)($row['reprovados_female'] ?? 0) ?></td>
                        <td class="num-t"><?= (int)($row['reprovados_total'] ?? 0) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
                <?php if (!empty($schoolStats['totals']) && is_array($schoolStats['totals'])): $t = $schoolStats['totals']; ?>
                <tfoot>
                    <tr>
                        <td colspan="2" style="text-align:left"><strong>TOTAIS GERAIS</strong></td>
                        <td><strong><?= (int)($t['total_classes'] ?? 0) ?></strong></td>
                        <td><?= (int)($t['total_male'] ?? 0) ?></td>
                        <td><?= (int)($t['total_female'] ?? 0) ?></td>
                        <td><strong><?= (int)($t['total'] ?? 0) ?></strong></td>
                        <td><?= (int)($t['desistentes_male'] ?? 0) ?></td>
                        <td><?= (int)($t['desistentes_female'] ?? 0) ?></td>
                        <td><strong><?= (int)($t['desistentes_total'] ?? 0) ?></strong></td>
                        <td><?= (int)($t['avaliados_male'] ?? 0) ?></td>
                        <td><?= (int)($t['avaliados_female'] ?? 0) ?></td>
                        <td><strong><?= (int)($t['avaliados_total'] ?? 0) ?></strong></td>
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

<!-- Scripts dos Gráficos -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
(function() {
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
    <?php if(!empty($enrollmentChart) && is_array($enrollmentChart)): ?>
    (function(){
        const ctx = document.getElementById('enrollmentChart');
        if(!ctx) return;
        const c = ctx.getContext('2d');
        new Chart(ctx, {
            type:'line',
            data:{
                labels: <?= json_encode($enrollmentChart['months'] ?? []) ?>,
                datasets:[{
                    label:'Matrículas',
                    data: <?= json_encode($enrollmentChart['counts'] ?? []) ?>,
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
    <?php if(!empty($genderChart) && is_array($genderChart) && (($genderChart['male'] ?? 0)+($genderChart['female'] ?? 0)>0)): ?>
    (function(){
        const ctx = document.getElementById('genderChart');
        if(!ctx) return;
        new Chart(ctx,{
            type:'doughnut',
            data:{
                labels:['Masculino','Feminino'],
                datasets:[{
                    data:[<?= (int)($genderChart['male'] ?? 0) ?>, <?= (int)($genderChart['female'] ?? 0) ?>],
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
    <?php if(!empty($feeChart) && is_array($feeChart)): ?>
    (function(){
        const ctx = document.getElementById('feeChart');
        if(!ctx) return;
        new Chart(ctx,{
            type:'bar',
            data:{
                labels: <?= json_encode($feeChart['months'] ?? []) ?>,
                datasets:[
                    {label:'Recebido', data:<?= json_encode($feeChart['collected'] ?? []) ?>, backgroundColor:C.alpha(C.green,.8), borderRadius:5},
                    {label:'Pendente', data:<?= json_encode($feeChart['pending'] ?? []) ?>, backgroundColor:C.alpha(C.amber,.7), borderRadius:5}
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
    <?php if(!empty($attendanceChart) && is_array($attendanceChart)): ?>
    (function(){
        const ctx = document.getElementById('attendanceChart');
        if(!ctx) return;
        new Chart(ctx,{
            type:'bar',
            data:{
                labels: <?= json_encode($attendanceChart['days'] ?? []) ?>,
                datasets:[
                    {label:'Presente', data:<?= json_encode($attendanceChart['present'] ?? []) ?>, backgroundColor:C.alpha(C.green,.82), borderRadius:4},
                    {label:'Falta',    data:<?= json_encode($attendanceChart['absent'] ?? []) ?>, backgroundColor:C.alpha(C.red,.65),   borderRadius:4}
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
    <?php if(!empty($performanceChart) && is_array($performanceChart)): ?>
    (function(){
        const ctx = document.getElementById('performanceChart');
        if(!ctx) return;
        new Chart(ctx,{
            type:'polarArea',
            data:{
                labels:['Excelente (≥17)','Bom (14-16)','Satisfatório (10-13)','Insuficiente (<10)'],
                datasets:[{
                    data:[
                        <?= (int)($performanceChart['excelente'] ?? 0) ?>,
                        <?= (int)($performanceChart['bom'] ?? 0) ?>,
                        <?= (int)($performanceChart['satisfatorio'] ?? 0) ?>,
                        <?= (int)($performanceChart['insuficiente'] ?? 0) ?>
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
    <?php if(!empty($courseTypeChart) && is_array($courseTypeChart)): ?>
    (function(){
        const ctx = document.getElementById('courseTypeChart');
        if(!ctx) return;
        const palette = [C.blue, C.green, C.amber, C.teal, C.purple, C.orange, C.red, C.slate];
        new Chart(ctx,{
            type:'pie',
            data:{
                labels: <?= json_encode($courseTypeChart['labels'] ?? []) ?>,
                datasets:[{
                    data: <?= json_encode($courseTypeChart['counts'] ?? []) ?>,
                    backgroundColor: palette.slice(0, <?= count($courseTypeChart['labels'] ?? []) ?>),
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
    <?php if(!empty($enrollmentStatusChart) && is_array($enrollmentStatusChart)): ?>
    (function(){
        const ctx = document.getElementById('enrollmentStatusChart');
        if(!ctx) return;
        const palette = [C.green, C.amber, C.red, C.blue, C.slate, C.teal];
        new Chart(ctx,{
            type:'doughnut',
            data:{
                labels: <?= json_encode($enrollmentStatusChart['labels'] ?? []) ?>,
                datasets:[{
                    data: <?= json_encode($enrollmentStatusChart['data'] ?? []) ?>,
                    backgroundColor: palette.slice(0, <?= count($enrollmentStatusChart['labels'] ?? []) ?>),
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
    <?php if(!empty($semesterResultsChart) && is_array($semesterResultsChart)): ?>
    (function(){
        const ctx = document.getElementById('semesterResultsChart');
        if(!ctx) return;
        new Chart(ctx,{
            type:'bar',
            data:{
                labels: <?= json_encode($semesterResultsChart['labels'] ?? []) ?>,
                datasets:[
                    {label:'Aprovados', data:<?= json_encode($semesterResultsChart['approved'] ?? []) ?>, backgroundColor:C.alpha(C.green,.8), borderRadius:5},
                    {label:'Reprovados',data:<?= json_encode($semesterResultsChart['failed'] ?? []) ?>, backgroundColor:C.alpha(C.red,.7),   borderRadius:5},
                    {label:'Recurso',   data:<?= json_encode($semesterResultsChart['appeal'] ?? []) ?>, backgroundColor:C.alpha(C.amber,.75),borderRadius:5}
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
    <?php if(!empty($topDisciplinesChart) && is_array($topDisciplinesChart)): ?>
    (function(){
        const ctx = document.getElementById('topDisciplinesChart');
        if(!ctx) return;
        new Chart(ctx,{
            type:'bar',
            data:{
                labels: <?= json_encode($topDisciplinesChart['labels'] ?? []) ?>,
                datasets:[{
                    label:'Horas',
                    data: <?= json_encode($topDisciplinesChart['data'] ?? []) ?>,
                    backgroundColor: (<?= json_encode($topDisciplinesChart['labels'] ?? []) ?>).map((_,i)=>{
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