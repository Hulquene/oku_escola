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

/* ── HEADER ───────────────────────────────────────────── */
.ci-page-header { background:linear-gradient(135deg,var(--primary) 0%,var(--primary-light) 60%,#2D4A7A 100%); border-radius:var(--radius); padding:1.5rem 2rem; margin-bottom:1.5rem; position:relative; overflow:hidden; box-shadow:var(--shadow-lg); }
.ci-page-header::before { content:''; position:absolute; top:-60px; right:-60px; width:200px; height:200px; border-radius:50%; background:rgba(255,255,255,.04); pointer-events:none; }
.ci-page-header::after  { content:''; position:absolute; bottom:-40px; right:100px; width:130px; height:130px; border-radius:50%; background:rgba(59,127,232,.15); pointer-events:none; }
.ci-page-header-inner   { display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:.75rem; position:relative; z-index:1; }
.ci-page-header h1 { font-size:1.4rem; font-weight:700; color:#fff; margin:0 0 .15rem; letter-spacing:-.3px; }
.ci-page-header p  { font-size:.82rem; color:rgba(255,255,255,.55); margin:0; }
.ci-page-header .breadcrumb { margin:0; padding:0; background:transparent; position:relative; z-index:1; }
.ci-page-header .breadcrumb-item a { color:rgba(255,255,255,.6); text-decoration:none; font-size:.8rem; transition:color .2s; }
.ci-page-header .breadcrumb-item a:hover { color:#fff; }
.ci-page-header .breadcrumb-item.active,
.ci-page-header .breadcrumb-item + .breadcrumb-item::before { color:rgba(255,255,255,.4); font-size:.8rem; }

.hdr-btn { display:inline-flex; align-items:center; gap:.45rem; border-radius:var(--radius-sm); padding:.45rem 1rem; font-size:.82rem; font-weight:600; text-decoration:none; transition:all .18s; cursor:pointer; border:none; font-family:'Sora',sans-serif; white-space:nowrap; }
.hdr-btn.accent  { background:var(--accent); color:#fff; box-shadow:0 3px 10px rgba(59,127,232,.28); }
.hdr-btn.accent:hover  { background:var(--accent-hover); color:#fff; transform:translateY(-1px); }
.hdr-btn.outline { background:rgba(255,255,255,.12); border:1.5px solid rgba(255,255,255,.25); color:#fff; }
.hdr-btn.outline:hover { background:rgba(255,255,255,.22); color:#fff; }

/* ── STAT SIDEBAR ─────────────────────────────────────── */
.stat-sidebar { display:flex; flex-direction:column; gap:.85rem; }
.stat-mini { background:var(--surface-card); border:1px solid var(--border); border-radius:var(--radius); padding:1rem 1.2rem; box-shadow:var(--shadow-sm); display:flex; align-items:center; gap:.9rem; transition:box-shadow .18s, transform .18s; }
.stat-mini:hover { box-shadow:var(--shadow-md); transform:translateY(-2px); }
.stat-mini-icon { width:42px; height:42px; border-radius:10px; display:flex; align-items:center; justify-content:center; font-size:1rem; flex-shrink:0; }
.smi-blue   { background:rgba(59,127,232,.1);  color:var(--accent); }
.smi-green  { background:rgba(22,168,125,.1);  color:var(--success); }
.smi-teal   { background:rgba(8,145,178,.1);   color:#0891B2; }
.smi-amber  { background:rgba(232,160,32,.1);  color:#B07800; }
.stat-mini-val   { font-family:'JetBrains Mono',monospace; font-size:1.4rem; font-weight:700; color:var(--text-primary); line-height:1; }
.stat-mini-label { font-size:.68rem; color:var(--text-muted); text-transform:uppercase; letter-spacing:.07em; font-weight:600; margin-top:.15rem; }

/* ── QUICK ACTIONS CARD ───────────────────────────────── */
.qa-card { background:var(--surface-card); border:1px solid var(--border); border-radius:var(--radius); box-shadow:var(--shadow-sm); overflow:hidden; }
.qa-card-header { padding:.8rem 1.1rem; background:var(--surface); border-bottom:1px solid var(--border); font-size:.75rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--text-muted); display:flex; align-items:center; gap:.4rem; }
.qa-card-header i { color:var(--warning); }
.qa-card-body { padding:1rem; display:flex; flex-direction:column; gap:.5rem; }
.qa-action { display:flex; align-items:center; gap:.6rem; padding:.6rem .85rem; border-radius:var(--radius-sm); font-size:.82rem; font-weight:600; text-decoration:none; cursor:pointer; transition:all .18s; border:none; font-family:'Sora',sans-serif; width:100%; background:var(--surface); border:1.5px solid var(--border); color:var(--text-secondary); }
.qa-action:hover { color:var(--primary); border-color:var(--accent); background:#EEF4FF; }
.qa-action.warning-btn { color:#8A5D00; border-color:rgba(232,160,32,.3); background:rgba(232,160,32,.05); }
.qa-action.warning-btn:hover { border-color:var(--warning); background:rgba(232,160,32,.1); }
.qa-action.success-btn { color:var(--success); border-color:rgba(22,168,125,.25); background:rgba(22,168,125,.04); }
.qa-action.success-btn:hover { border-color:var(--success); background:rgba(22,168,125,.09); }
.qa-action.danger-btn  { color:var(--danger); border-color:rgba(232,70,70,.25); background:rgba(232,70,70,.04); }
.qa-action.danger-btn:hover { border-color:var(--danger); background:rgba(232,70,70,.08); }
.qa-action i { font-size:.8rem; flex-shrink:0; }
.qa-info { padding:.65rem .85rem; border-radius:var(--radius-sm); font-size:.78rem; border:1px solid var(--border); background:var(--surface); color:var(--text-muted); display:flex; align-items:center; gap:.45rem; }
.qa-info i { flex-shrink:0; }

/* ── CONTENT CARDS ────────────────────────────────────── */
.ci-card { background:var(--surface-card); border:1px solid var(--border); border-radius:var(--radius); box-shadow:var(--shadow-sm); overflow:hidden; margin-bottom:1.25rem; }
.ci-card-header { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.5rem; padding:.85rem 1.25rem; background:var(--surface); border-bottom:1px solid var(--border); }
.ci-card-title  { display:flex; align-items:center; gap:.55rem; font-size:.82rem; font-weight:700; color:var(--text-primary); }
.ci-card-title i { color:var(--accent); font-size:.8rem; }
.ci-card-body   { padding:1.25rem; }

/* Info table */
.info-table { width:100%; }
.info-table tr { border-bottom:1px solid var(--border); }
.info-table tr:last-child { border-bottom:none; }
.info-table th { padding:.6rem .85rem .6rem 0; font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--text-muted); width:38%; }
.info-table td { padding:.6rem 0; font-size:.875rem; font-weight:500; }

.period-row { display:flex; align-items:center; gap:.5rem; }
.period-date { font-family:'JetBrains Mono',monospace; font-size:.82rem; font-weight:700; }
.period-arrow { color:var(--text-muted); font-size:.7rem; }
.dur-chip { font-family:'JetBrains Mono',monospace; font-size:.78rem; font-weight:700; color:var(--accent); }
.dur-unit { font-size:.75rem; color:var(--text-muted); }

.status-badge { font-size:.65rem; font-weight:700; padding:.22rem .6rem; border-radius:50px; display:inline-flex; align-items:center; gap:.25rem; text-transform:uppercase; letter-spacing:.04em; }
.sb-active   { color:var(--success); background:rgba(22,168,125,.1); }
.sb-inactive { color:var(--text-muted); background:rgba(107,122,153,.1); }
.sb-current  { color:var(--accent); background:rgba(59,127,232,.1); }

/* Semesters / classes mini table */
.ci-table { width:100%; border-collapse:separate; border-spacing:0; }
.ci-table thead tr th { background:var(--surface); color:var(--text-secondary); font-size:.65rem; font-weight:700; text-transform:uppercase; letter-spacing:.09em; padding:.6rem 1rem; border-bottom:1.5px solid var(--border); white-space:nowrap; }
.ci-table tbody tr { border-bottom:1px solid var(--border); transition:background .12s; }
.ci-table tbody tr:last-child { border-bottom:none; }
.ci-table tbody tr:hover { background:#F5F8FF; }
.ci-table tbody td { padding:.6rem 1rem; vertical-align:middle; font-size:.82rem; border:none; }
.ci-table tbody td.center { text-align:center; }

.type-badge  { font-size:.65rem; font-weight:700; padding:.2rem .55rem; border-radius:50px; background:rgba(59,127,232,.1); color:var(--accent); text-transform:uppercase; letter-spacing:.04em; }
.shift-badge { font-size:.65rem; font-weight:700; padding:.2rem .55rem; border-radius:50px; background:rgba(22,168,125,.1); color:var(--success); }
.count-chip  { font-family:'JetBrains Mono',monospace; font-size:.72rem; font-weight:700; padding:.18rem .5rem; border-radius:6px; background:rgba(59,127,232,.08); color:var(--accent); display:inline-block; }

.view-btn { display:inline-flex; align-items:center; justify-content:center; width:26px; height:26px; border-radius:6px; border:1.5px solid var(--border); background:#fff; color:var(--text-muted); text-decoration:none; font-size:.72rem; transition:all .18s; }
.view-btn:hover { background:var(--success); color:#fff; border-color:var(--success); }

.ci-empty { text-align:center; padding:2.5rem 1.5rem; color:var(--text-muted); }
.ci-empty i { font-size:1.8rem; opacity:.15; display:block; margin-bottom:.5rem; }
.ci-empty p { font-size:.82rem; margin:0; }

.add-btn { display:inline-flex; align-items:center; gap:.4rem; border-radius:var(--radius-sm); padding:.28rem .75rem; font-size:.72rem; font-weight:600; background:var(--accent); color:#fff; text-decoration:none; transition:all .18s; border:none; }
.add-btn:hover { background:var(--accent-hover); color:#fff; }

.alert { border-radius:var(--radius-sm); border:none; font-size:.875rem; }
.alert-success { background:rgba(22,168,125,.1); color:#0E7A5A; border-left:3px solid var(--success); }
.alert-danger  { background:rgba(232,70,70,.08); color:#B03030; border-left:3px solid var(--danger); }

@media (max-width:991px) {
    .ci-page-header { padding:1.1rem 1.2rem; }
    .ci-page-header h1 { font-size:1.1rem; }
}
</style>

<!-- ── PAGE HEADER ─────────────────────────────────────── -->
<div class="ci-page-header mb-4">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-graduation-cap me-2" style="opacity:.7;font-size:1.1rem;"></i><?= $title ?></h1>
            <p>Visualize os detalhes do ano letivo</p>
            <nav aria-label="breadcrumb" class="mt-2">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/academic/years') ?>">Anos Letivos</a></li>
                    <li class="breadcrumb-item active" aria-current="page"><?= esc($year['year_name']) ?></li>
                </ol>
            </nav>
        </div>
        <div class="d-flex gap-2 flex-wrap">
            <?php if (has_permission('settings.academic_years')): ?>
                <a href="<?= site_url('admin/academic/years/form/' . $year['id']) ?>" class="hdr-btn accent">
                    <i class="fas fa-edit"></i> Editar
                </a>
            <?php endif; ?>
            <a href="<?= site_url('admin/academic/years') ?>" class="hdr-btn outline">
                <i class="fas fa-arrow-left"></i> Voltar
            </a>
        </div>
    </div>
</div>

<?= view('admin/partials/alerts') ?>

<!-- ── MAIN LAYOUT ─────────────────────────────────────── -->
<div class="row g-3">

    <!-- ── LEFT COLUMN ──────────────────────────────────── -->
    <div class="col-xl-8">

        <!-- Info card -->
        <div class="ci-card">
            <div class="ci-card-header">
                <div class="ci-card-title"><i class="fas fa-calendar-alt"></i> Informações Gerais</div>
            </div>
            <div class="ci-card-body">
                <div class="row g-3">
                    <div class="col-md-6">
                        <table class="info-table">
                            <tr>
                                <th>Ano Letivo</th>
                                <td style="font-weight:800;font-size:1rem;letter-spacing:-.2px;"><?= esc($year['year_name']) ?></td>
                            </tr>
                            <tr>
                                <th>Estado</th>
                                <td>
                                    <div style="display:flex;gap:.35rem;flex-wrap:wrap;">
                                        <?php if ($year['is_active']): ?>
                                            <span class="status-badge sb-active"><span style="width:5px;height:5px;border-radius:50%;background:currentColor;flex-shrink:0;"></span>Ativo</span>
                                        <?php else: ?>
                                            <span class="status-badge sb-inactive">Inativo</span>
                                        <?php endif; ?>
                                        <?php if ($year['id'] == current_academic_year()): ?>
                                            <span class="status-badge sb-current"><i class="fas fa-star" style="font-size:.55rem;"></i>Atual</span>
                                        <?php endif; ?>
                                    </div>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-md-6">
                        <table class="info-table">
                            <tr>
                                <th>Período</th>
                                <td>
                                    <div class="period-row">
                                        <span class="period-date"><?= date('d/m/Y', strtotime($year['start_date'])) ?></span>
                                        <i class="fas fa-arrow-right period-arrow"></i>
                                        <span class="period-date"><?= date('d/m/Y', strtotime($year['end_date'])) ?></span>
                                    </div>
                                </td>
                            </tr>
                            <tr>
                                <th>Duração</th>
                                <td>
                                    <?php $days = (int) ceil((strtotime($year['end_date']) - strtotime($year['start_date'])) / 86400) + 1; ?>
                                    <span class="dur-chip"><?= $days ?></span> <span class="dur-unit">dias</span>
                                </td>
                            </tr>
                            <tr>
                                <th>Criado em</th>
                                <td style="font-size:.78rem;color:var(--text-secondary);"><?= date('d/m/Y H:i', strtotime($year['created_at'])) ?></td>
                            </tr>
                            <?php if ($year['updated_at'] && $year['updated_at'] != $year['created_at']): ?>
                            <tr>
                                <th>Atualizado</th>
                                <td style="font-size:.78rem;color:var(--text-secondary);"><?= date('d/m/Y H:i', strtotime($year['updated_at'])) ?></td>
                            </tr>
                            <?php endif; ?>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Semesters -->
        <div class="ci-card">
            <div class="ci-card-header">
                <div class="ci-card-title"><i class="fas fa-layer-group"></i> Semestres / Trimestres</div>
                <?php if (has_permission('settings.semesters')): ?>
                    <a href="<?= site_url('admin/academic/semesters/form?year_id=' . $year['id']) ?>" class="add-btn">
                        <i class="fas fa-plus"></i> Novo Semestre
                    </a>
                <?php endif; ?>
            </div>
            <div style="overflow-x:auto;">
                <?php if (!empty($semesters)): ?>
                <table class="ci-table">
                    <thead>
                        <tr>
                            <th>Nome</th>
                            <th>Tipo</th>
                            <th>Período</th>
                            <th>Estado</th>
                            <th class="center">Ver</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($semesters as $sem):
                            $stMap = ['ativo'=>'sb-active','inativo'=>'sb-inactive','processado'=>'','concluido'=>''];
                            $stLabel = ['ativo'=>'Ativo','inativo'=>'Inativo','processado'=>'Processado','concluido'=>'Concluído'];
                            $sc = $sem->status ?? 'ativo';
                        ?>
                        <tr>
                            <td style="font-weight:600;"><?= esc($sem['semester_name']) ?></td>
                            <td><span class="type-badge"><?= esc($sem->semester_type) ?></span></td>
                            <td style="font-family:'JetBrains Mono',monospace;font-size:.73rem;color:var(--text-secondary);">
                                <?= date('d/m/Y', strtotime($sem['start_date'])) ?> → <?= date('d/m/Y', strtotime($sem['end_date'])) ?>
                            </td>
                            <td>
                                <div style="display:flex;gap:.3rem;flex-wrap:wrap;">
                                    <?php if ($sem['is_current']): ?>
                                        <span class="status-badge sb-current"><i class="fas fa-star" style="font-size:.55rem;"></i>Atual</span>
                                    <?php endif; ?>
                                    <span class="status-badge <?= $stMap[$sc] ?? 'sb-inactive' ?>">
                                        <?= $stLabel[$sc] ?? ucfirst($sc) ?>
                                    </span>
                                </div>
                            </td>
                            <td class="center">
                                <a href="<?= site_url('admin/academic/semesters/view/' . $sem['id']) ?>" class="view-btn" title="Ver">
                                    <i class="fas fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                    <div class="ci-empty"><i class="fas fa-layer-group"></i><p>Nenhum semestre cadastrado para este ano letivo.</p></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Classes -->
        <div class="ci-card">
            <div class="ci-card-header">
                <div class="ci-card-title"><i class="fas fa-users"></i> Turmas</div>
            </div>
            <div style="overflow-x:auto;">
                <?php if (!empty($classes)): ?>
                <table class="ci-table">
                    <thead>
                        <tr>
                            <th>Turma</th>
                            <th>Código</th>
                            <th>Turno</th>
                            <th class="center">Alunos</th>
                            <th class="center">Capacidade</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($classes as $class): ?>
                        <tr>
                            <td style="font-weight:600;"><?= esc($class['class_name']) ?></td>
                            <td style="font-family:'JetBrains Mono',monospace;font-size:.75rem;color:var(--text-secondary);"><?= esc($class['class_code']) ?></td>
                            <td><span class="shift-badge"><?= esc($class['class_shift']) ?></span></td>
                            <td class="center"><span class="count-chip"><?= $class->student_count ?? 0 ?></span></td>
                            <td class="center" style="font-size:.8rem;color:var(--text-secondary);"><?= $class['capacity']  ?? '—' ?></td>
                            <td>
                                <?php if ($class['is_active']): ?>
                                    <span class="status-badge sb-active">Ativo</span>
                                <?php else: ?>
                                    <span class="status-badge sb-inactive">Inativo</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <?php else: ?>
                    <div class="ci-empty"><i class="fas fa-door-open"></i><p>Nenhuma turma cadastrada para este ano letivo.</p></div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ── RIGHT SIDEBAR ────────────────────────────────── -->
    <div class="col-xl-4">

        <!-- Stats -->
        <div class="stat-sidebar mb-3">
            <div class="stat-mini">
                <div class="stat-mini-icon smi-blue"><i class="fas fa-user-graduate"></i></div>
                <div>
                    <div class="stat-mini-val"><?= $total_enrollments ?></div>
                    <div class="stat-mini-label">Total de Matrículas</div>
                </div>
            </div>
            <div class="stat-mini">
                <div class="stat-mini-icon smi-green"><i class="fas fa-check-circle"></i></div>
                <div>
                    <div class="stat-mini-val"><?= $active_enrollments ?></div>
                    <div class="stat-mini-label">Matrículas Ativas</div>
                </div>
            </div>
            <div class="stat-mini">
                <div class="stat-mini-icon smi-teal"><i class="fas fa-door-open"></i></div>
                <div>
                    <div class="stat-mini-val"><?= count($classes) ?></div>
                    <div class="stat-mini-label">Total de Turmas</div>
                </div>
            </div>
            <div class="stat-mini">
                <div class="stat-mini-icon smi-amber"><i class="fas fa-layer-group"></i></div>
                <div>
                    <div class="stat-mini-val"><?= count($semesters) ?></div>
                    <div class="stat-mini-label">Semestres</div>
                </div>
            </div>
        </div>

        <!-- Quick actions -->
        <div class="qa-card">
            <div class="qa-card-header"><i class="fas fa-bolt"></i> Ações Rápidas</div>
            <div class="qa-card-body">

                <?php if (!$year['id'] == current_academic_year() && has_permission('settings.academic_years')): ?>
                    <a href="<?= site_url('admin/academic/years/setCurrent/' . $year['id']) ?>"
                       class="qa-action"
                       onclick="return confirm('Tem certeza que deseja definir <?= esc($year['year_name']) ?> como ano letivo atual?')">
                        <i class="fas fa-star" style="color:var(--warning);"></i> Definir como Atual
                    </a>
                <?php elseif ($year['id'] == current_academic_year()): ?>
                    <div class="qa-info"><i class="fas fa-star" style="color:var(--warning);"></i> Este é o ano letivo atual</div>
                <?php endif; ?>

                <a href="<?= site_url('admin/reports/academic?year_id=' . $year['id']) ?>" class="qa-action success-btn">
                    <i class="fas fa-chart-bar"></i> Gerar Relatório
                </a>

                <?php if (!$year['id'] == current_academic_year() && $year['is_active'] && has_permission('settings.academic_years')): ?>
                    <form action="<?= site_url('admin/academic/years/toggle-active/' . $year['id']) ?>" method="post"
                          onsubmit="return confirm('ATENÇÃO: Desativar este ano letivo irá afetar todas as turmas, matrículas e semestres associados. Tem certeza?')">
                        <?= csrf_field() ?>
                        <button type="submit" class="qa-action warning-btn">
                            <i class="fas fa-ban"></i> Desativar Ano Letivo
                        </button>
                    </form>
                <?php elseif ($year['id'] == current_academic_year()): ?>
                    <div class="qa-info"><i class="fas fa-info-circle"></i> O ano letivo atual não pode ser desativado.</div>
                <?php elseif (!$year['is_active']): ?>
                    <div class="qa-info"><i class="fas fa-info-circle"></i> Este ano letivo já está desativado.</div>
                <?php endif; ?>

                <?php if (!$year['is_active'] && has_permission('settings.academic_years')): ?>
                    <form action="<?= site_url('admin/academic/years/toggle-active/' . $year['id']) ?>" method="post"
                          onsubmit="return confirm('Tem certeza que deseja ativar <?= esc($year['year_name']) ?>?')">
                        <?= csrf_field() ?>
                        <button type="submit" class="qa-action success-btn">
                            <i class="fas fa-check"></i> Ativar Ano Letivo
                        </button>
                    </form>
                <?php endif; ?>

            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>