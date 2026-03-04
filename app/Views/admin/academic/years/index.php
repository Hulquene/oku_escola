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

.ci-page-header { background:linear-gradient(135deg,var(--primary) 0%,var(--primary-light) 60%,#2D4A7A 100%); border-radius:var(--radius); padding:1.5rem 2rem; margin-bottom:1.5rem; position:relative; overflow:hidden; box-shadow:var(--shadow-lg); }
.ci-page-header::before { content:''; position:absolute; top:-60px; right:-60px; width:200px; height:200px; border-radius:50%; background:rgba(255,255,255,.04); pointer-events:none; }
.ci-page-header::after  { content:''; position:absolute; bottom:-40px; right:100px; width:130px; height:130px; border-radius:50%; background:rgba(59,127,232,.15); pointer-events:none; }
.ci-page-header-inner   { display:flex; align-items:flex-start; justify-content:space-between; flex-wrap:wrap; gap:.75rem; position:relative; z-index:1; }
.ci-page-header h1 { font-size:1.4rem; font-weight:700; color:#fff; margin:0 0 .2rem; letter-spacing:-.3px; }
.ci-page-header .breadcrumb { margin:0; padding:0; background:transparent; position:relative; z-index:1; }
.ci-page-header .breadcrumb-item a { color:rgba(255,255,255,.6); text-decoration:none; font-size:.8rem; transition:color .2s; }
.ci-page-header .breadcrumb-item a:hover { color:#fff; }
.ci-page-header .breadcrumb-item.active,
.ci-page-header .breadcrumb-item + .breadcrumb-item::before { color:rgba(255,255,255,.4); font-size:.8rem; }

.hdr-btn { display:inline-flex; align-items:center; gap:.45rem; border-radius:var(--radius-sm); padding:.45rem 1rem; font-size:.82rem; font-weight:600; text-decoration:none; transition:all .18s; cursor:pointer; border:none; font-family:'Sora',sans-serif; white-space:nowrap; }
.hdr-btn.primary { background:var(--accent); color:#fff; box-shadow:0 3px 10px rgba(59,127,232,.28); }
.hdr-btn.primary:hover { background:var(--accent-hover); color:#fff; transform:translateY(-1px); }

.ci-card { background:var(--surface-card); border:1px solid var(--border); border-radius:var(--radius); box-shadow:var(--shadow-sm); overflow:hidden; margin-bottom:1.25rem; }
.ci-card-header { display:flex; align-items:center; justify-content:space-between; flex-wrap:wrap; gap:.5rem; padding:.85rem 1.25rem; background:var(--surface); border-bottom:1px solid var(--border); }
.ci-card-title  { display:flex; align-items:center; gap:.55rem; font-size:.82rem; font-weight:700; color:var(--text-primary); }
.ci-card-title i { color:var(--accent); font-size:.8rem; }

.ci-table { width:100%; border-collapse:separate; border-spacing:0; }
.ci-table thead tr th { background:var(--surface); color:var(--text-secondary); font-size:.65rem; font-weight:700; text-transform:uppercase; letter-spacing:.09em; padding:.65rem 1rem; border-bottom:1.5px solid var(--border); white-space:nowrap; }
.ci-table tbody tr { border-bottom:1px solid var(--border); transition:background .12s; }
.ci-table tbody tr:last-child { border-bottom:none; }
.ci-table tbody tr:hover { background:#F5F8FF; }
.ci-table tbody td { padding:.7rem 1rem; vertical-align:middle; font-size:.83rem; border:none; }
.ci-table tbody td.center { text-align:center; }

.id-chip { font-family:'JetBrains Mono',monospace; font-size:.7rem; font-weight:600; background:rgba(27,43,75,.07); color:var(--primary); padding:.15rem .45rem; border-radius:5px; }
.year-name { font-weight:700; color:var(--text-primary); }
.period-text { font-family:'JetBrains Mono',monospace; font-size:.73rem; color:var(--text-secondary); }

.status-dot { display:inline-flex; align-items:center; gap:.3rem; font-size:.72rem; font-weight:700; }
.sd { width:7px; height:7px; border-radius:50%; flex-shrink:0; }
.sd-active   { background:var(--success); box-shadow:0 0 0 2px rgba(22,168,125,.2); }
.sd-inactive { background:var(--text-muted); }
.st-active   { color:var(--success); }
.st-inactive { color:var(--text-muted); }

.current-badge { font-size:.65rem; font-weight:700; padding:.22rem .6rem; border-radius:50px; background:rgba(59,127,232,.12); color:var(--accent); display:inline-flex; align-items:center; gap:.25rem; }
.btn-set-current { display:inline-flex; align-items:center; gap:.35rem; padding:.22rem .7rem; font-size:.72rem; font-weight:600; border-radius:6px; border:1.5px solid var(--border); background:#fff; color:var(--text-muted); text-decoration:none; cursor:pointer; transition:all .18s; white-space:nowrap; }
.btn-set-current:hover { border-color:var(--accent); background:rgba(59,127,232,.08); color:var(--accent); }

.row-btn { width:28px; height:28px; border-radius:7px; border:1.5px solid var(--border); background:#fff; color:var(--text-secondary); display:inline-flex; align-items:center; justify-content:center; font-size:.72rem; text-decoration:none; cursor:pointer; transition:all .18s; }
.row-btn:hover { color:#fff; border-color:transparent; }
.row-btn.edit:hover { background:var(--accent); }
.row-btn.view:hover { background:var(--success); }
.row-btn.del:hover  { background:var(--danger); }

.ci-empty { text-align:center; padding:3rem; color:var(--text-muted); }
.ci-empty i { font-size:2rem; opacity:.15; display:block; margin-bottom:.6rem; }
.ci-empty p { font-size:.82rem; margin:0; }

.alert { border-radius:var(--radius-sm); border:none; font-size:.875rem; }
.alert-success { background:rgba(22,168,125,.1); color:#0E7A5A; border-left:3px solid var(--success); }
.alert-danger  { background:rgba(232,70,70,.08); color:#B03030; border-left:3px solid var(--danger); }

@media (max-width:767px) {
    .ci-page-header { padding:1.1rem 1.2rem; }
    .ci-page-header h1 { font-size:1.1rem; }
}
</style>

<!-- ── PAGE HEADER ─────────────────────────────────────── -->
<div class="ci-page-header mb-4">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-graduation-cap me-2" style="opacity:.7;font-size:1.1rem;"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Anos Letivos</li>
                </ol>
            </nav>
        </div>
        <a href="<?= site_url('admin/academic/years/form-add') ?>" class="hdr-btn primary">
            <i class="fas fa-plus-circle"></i> Novo Ano Letivo
        </a>
    </div>
</div>

<?= view('admin/partials/alerts') ?>

<!-- ── TABLE ───────────────────────────────────────────── -->
<div class="ci-card">
    <div class="ci-card-header">
        <div class="ci-card-title"><i class="fas fa-list"></i> Lista de Anos Letivos</div>
    </div>
    <div style="overflow-x:auto;">
        <table id="yearsTable" class="ci-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Ano Letivo</th>
                    <th>Início</th>
                    <th>Fim</th>
                    <th>Estado</th>
                    <th class="center">Atual</th>
                    <th class="center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($years)): ?>
                    <?php foreach ($years as $year): ?>
                    <tr>
                        <td><span class="id-chip"><?= $year->id ?></span></td>
                        <td><span class="year-name"><?= esc($year->year_name) ?></span></td>
                        <td><span class="period-text"><?= date('d/m/Y', strtotime($year->start_date)) ?></span></td>
                        <td><span class="period-text"><?= date('d/m/Y', strtotime($year->end_date)) ?></span></td>
                        <td>
                            <?php if ($year->is_active): ?>
                                <span class="status-dot st-active"><span class="sd sd-active"></span>Ativo</span>
                            <?php else: ?>
                                <span class="status-dot st-inactive"><span class="sd sd-inactive"></span>Inativo</span>
                            <?php endif; ?>
                        </td>
                        <td class="center">
                            <?php if ($year->is_current): ?>
                                <span class="current-badge"><i class="fas fa-star" style="font-size:.6rem;"></i>Atual</span>
                            <?php else: ?>
                                <a href="<?= site_url('admin/academic/years/set-current/' . $year->id) ?>"
                                   class="btn-set-current"
                                   onclick="return confirm('Definir este ano como atual?')">
                                    <i class="fas fa-check-circle"></i> Definir
                                </a>
                            <?php endif; ?>
                        </td>
                        <td class="center">
                            <div class="d-flex justify-content-center gap-1">
                                <a href="<?= site_url('admin/academic/years/form-edit/' . $year->id) ?>" class="row-btn edit" title="Editar"><i class="fas fa-edit"></i></a>
                                <a href="<?= site_url('admin/academic/years/view/' . $year->id) ?>"    class="row-btn view" title="Ver Detalhes"><i class="fas fa-eye"></i></a>
                                <?php if (!$year->is_current): ?>
                                    <a href="<?= site_url('admin/academic/years/delete/' . $year->id) ?>"
                                       class="row-btn del" title="Eliminar"
                                       onclick="return confirm('Tem certeza que deseja eliminar este ano letivo?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="7"><div class="ci-empty"><i class="fas fa-calendar-times"></i><p>Nenhum ano letivo encontrado</p></div></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function () {
    $('#yearsTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json' },
        order: [[1, 'desc']]
    });
});
</script>
<?= $this->endSection() ?>