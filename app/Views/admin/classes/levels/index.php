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
.ci-page-header h1 { font-size:1.4rem; font-weight:700; color:#fff; margin:0 0 .2rem; letter-spacing:-.3px; }
.ci-page-header .breadcrumb { margin:0; padding:0; background:transparent; position:relative; z-index:1; }
.ci-page-header .breadcrumb-item a { color:rgba(255,255,255,.6); text-decoration:none; font-size:.8rem; transition:color .2s; }
.ci-page-header .breadcrumb-item a:hover { color:#fff; }
.ci-page-header .breadcrumb-item.active,
.ci-page-header .breadcrumb-item + .breadcrumb-item::before { color:rgba(255,255,255,.4); font-size:.8rem; }

.hdr-btn { display:inline-flex; align-items:center; gap:.45rem; border-radius:var(--radius-sm); padding:.45rem 1rem; font-size:.82rem; font-weight:600; text-decoration:none; transition:all .18s; cursor:pointer; border:none; font-family:'Sora',sans-serif; white-space:nowrap; }
.hdr-btn.primary { background:var(--accent); color:#fff; box-shadow:0 3px 10px rgba(59,127,232,.28); }
.hdr-btn.primary:hover { background:var(--accent-hover); color:#fff; transform:translateY(-1px); }

/* ── TABLE CARD ───────────────────────────────────────── */
.ci-card { background:var(--surface-card); border:1px solid var(--border); border-radius:var(--radius); box-shadow:var(--shadow-sm); overflow:hidden; }
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

.id-chip     { font-family:'JetBrains Mono',monospace; font-size:.7rem; font-weight:600; background:rgba(27,43,75,.07); color:var(--primary); padding:.15rem .45rem; border-radius:5px; }
.level-name  { font-weight:700; color:var(--text-primary); }
.code-badge  { font-family:'JetBrains Mono',monospace; font-size:.7rem; font-weight:700; background:var(--primary); color:#fff; padding:.18rem .55rem; border-radius:5px; letter-spacing:.03em; }
.edu-badge   { font-size:.68rem; font-weight:700; padding:.2rem .6rem; border-radius:50px; background:rgba(59,127,232,.1); color:var(--accent); white-space:nowrap; }
.grade-chip  { font-family:'JetBrains Mono',monospace; font-size:.78rem; font-weight:700; color:var(--text-primary); }
.grade-chip span { font-size:.68rem; color:var(--text-muted); font-family:'Sora',sans-serif; }
.order-num   { font-family:'JetBrains Mono',monospace; font-size:.78rem; color:var(--text-secondary); text-align:center; }

.status-dot  { display:inline-flex; align-items:center; gap:.3rem; font-size:.72rem; font-weight:700; }
.sd  { width:7px; height:7px; border-radius:50%; flex-shrink:0; }
.sd-active   { background:var(--success); box-shadow:0 0 0 2px rgba(22,168,125,.2); }
.sd-inactive { background:var(--text-muted); }
.st-active   { color:var(--success); }
.st-inactive { color:var(--text-muted); }

.row-btn { width:28px; height:28px; border-radius:7px; border:1.5px solid var(--border); background:#fff; color:var(--text-secondary); display:inline-flex; align-items:center; justify-content:center; font-size:.72rem; text-decoration:none; cursor:pointer; transition:all .18s; }
.row-btn:hover { color:#fff; border-color:transparent; }
.row-btn.edit:hover { background:var(--accent); }
.row-btn.del:hover  { background:var(--danger); }

.ci-empty { text-align:center; padding:3rem; color:var(--text-muted); }
.ci-empty i { font-size:2rem; opacity:.15; display:block; margin-bottom:.6rem; }
.ci-empty p { font-size:.82rem; margin:0; }

/* ── MODAL ────────────────────────────────────────────── */
.ci-modal .modal-content { border:none; border-radius:var(--radius); overflow:hidden; box-shadow:var(--shadow-lg); }
.ci-modal .modal-header { background:var(--primary); padding:1rem 1.4rem; border-bottom:none; }
.ci-modal .modal-title  { font-size:.9rem; font-weight:700; color:#fff; display:flex; align-items:center; gap:.5rem; }
.ci-modal .modal-title i { opacity:.75; }
.ci-modal .btn-close    { filter:brightness(0) invert(1); opacity:.7; }
.ci-modal .modal-body   { padding:1.5rem; }
.ci-modal .modal-footer { padding:.9rem 1.4rem; border-top:1px solid var(--border); gap:.5rem; }

/* Form controls inside modal */
.form-label-ci { font-size:.68rem; font-weight:700; text-transform:uppercase; letter-spacing:.08em; color:var(--text-secondary); margin-bottom:.3rem; display:flex; align-items:center; gap:.3rem; }
.form-label-ci .req { color:var(--danger); }
.form-label-ci i { color:var(--accent); font-size:.65rem; }
.form-input-ci, .form-select-ci {
    width:100%; border:1.5px solid var(--border); border-radius:var(--radius-sm);
    padding:.55rem .85rem; font-size:.875rem; font-family:'Sora',sans-serif;
    color:var(--text-primary); background:var(--surface); outline:none;
    transition:border-color .2s, box-shadow .2s;
}
.form-input-ci:focus, .form-select-ci:focus { border-color:var(--accent); box-shadow:0 0 0 3px rgba(59,127,232,.12); background:#fff; }
.form-hint { font-size:.7rem; color:var(--text-muted); margin-top:.28rem; display:flex; align-items:center; gap:.22rem; }

/* Toggle inside modal */
.toggle-row { display:flex; align-items:center; justify-content:space-between; background:var(--surface); border:1.5px solid var(--border); border-radius:var(--radius-sm); padding:.65rem .9rem; cursor:pointer; transition:border-color .18s; user-select:none; }
.toggle-row:hover { border-color:var(--accent); }
.toggle-row.checked { border-color:rgba(22,168,125,.3); background:rgba(22,168,125,.04); }
.tl-info { font-size:.85rem; font-weight:600; color:var(--text-primary); }
.tl-sub  { font-size:.7rem; color:var(--text-muted); margin-top:.08rem; }
.ci-switch { position:relative; display:inline-flex; width:40px; height:22px; flex-shrink:0; }
.ci-switch input { opacity:0; width:0; height:0; position:absolute; }
.ci-switch-track { position:absolute; inset:0; border-radius:50px; background:var(--border); transition:background .22s; cursor:pointer; }
.ci-switch-track::after { content:''; position:absolute; top:3px; left:3px; width:16px; height:16px; border-radius:50%; background:#fff; box-shadow:0 1px 3px rgba(0,0,0,.2); transition:transform .22s; }
.ci-switch input:checked + .ci-switch-track { background:var(--success); }
.ci-switch input:checked + .ci-switch-track::after { transform:translateX(18px); }

/* Modal buttons */
.btn-modal-cancel { background:var(--surface); border:1.5px solid var(--border); color:var(--text-secondary); padding:.45rem 1.1rem; border-radius:var(--radius-sm); font-size:.85rem; font-weight:600; cursor:pointer; transition:all .18s; font-family:'Sora',sans-serif; }
.btn-modal-cancel:hover { background:#fff; color:var(--primary); }
.btn-modal-save { background:var(--accent); border:none; color:#fff; padding:.45rem 1.4rem; border-radius:var(--radius-sm); font-size:.85rem; font-weight:700; display:inline-flex; align-items:center; gap:.45rem; cursor:pointer; transition:all .18s; font-family:'Sora',sans-serif; box-shadow:0 3px 10px rgba(59,127,232,.28); }
.btn-modal-save:hover { background:var(--accent-hover); }

/* Alerts */
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
            <h1><i class="fas fa-layer-group me-2" style="opacity:.7;font-size:1.1rem;"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/classes') ?>">Classes</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Níveis de Ensino</li>
                </ol>
            </nav>
        </div>
        <button type="button" class="hdr-btn primary" data-bs-toggle="modal" data-bs-target="#levelModal" id="btnNewLevel">
            <i class="fas fa-plus-circle"></i> Novo Nível
        </button>
    </div>
</div>

<?= view('admin/partials/alerts') ?>

<!-- ── TABLE ───────────────────────────────────────────── -->
<div class="ci-card">
    <div class="ci-card-header">
        <div class="ci-card-title"><i class="fas fa-layer-group"></i> Lista de Níveis de Ensino</div>
    </div>
    <div style="overflow-x:auto;">
        <table id="levelsTable" class="ci-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nível</th>
                    <th>Código</th>
                    <th>Ciclo de Ensino</th>
                    <th class="center">Classe</th>
                    <th class="center">Ordem</th>
                    <th>Estado</th>
                    <th class="center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($levels)): ?>
                    <?php foreach ($levels as $level): ?>
                    <tr>
                        <td><span class="id-chip"><?= $level->id ?></span></td>
                        <td><span class="level-name"><?= esc($level->level_name) ?></span></td>
                        <td><span class="code-badge"><?= esc($level->level_code) ?></span></td>
                        <td><span class="edu-badge"><?= esc($level->education_level) ?></span></td>
                        <td class="center">
                            <span class="grade-chip"><?= $level->grade_number ?><span>ª Classe</span></span>
                        </td>
                        <td class="center"><span class="order-num"><?= $level->sort_order ?></span></td>
                        <td>
                            <?php if ($level->is_active): ?>
                                <span class="status-dot st-active"><span class="sd sd-active"></span>Ativo</span>
                            <?php else: ?>
                                <span class="status-dot st-inactive"><span class="sd sd-inactive"></span>Inativo</span>
                            <?php endif; ?>
                        </td>
                        <td class="center">
                            <div class="d-flex justify-content-center gap-1">
                                  <!-- BOTÃO VER CURRÍCULO (NOVO) -->
                                <a href="<?= site_url('admin/grade-curriculum/' . $level->id) ?>" 
                                   class="row-btn" 
                                   title="Ver Currículo"
                                   style="background: rgba(59,127,232,0.1); border-color: rgba(59,127,232,0.2); color: var(--accent);">
                                    <i class="fas fa-book-open"></i>
                                </a>
                                <!-- Botao de edição -->
                                <button type="button" class="row-btn edit edit-level" title="Editar"
                                        data-id="<?= $level->id ?>"
                                        data-name="<?= esc($level->level_name) ?>"
                                        data-code="<?= esc($level->level_code) ?>"
                                        data-education="<?= esc($level->education_level) ?>"
                                        data-grade="<?= $level->grade_number ?>"
                                        data-order="<?= $level->sort_order ?>"
                                        data-active="<?= $level->is_active ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <?php if ($level->is_active): ?>
                                    <a href="<?= site_url('admin/classes/levels/delete/' . $level->id) ?>"
                                       class="row-btn del" title="Eliminar"
                                       onclick="return confirm('Tem certeza que deseja eliminar este nível?')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr><td colspan="8"><div class="ci-empty"><i class="fas fa-layer-group"></i><p>Nenhum nível de ensino encontrado</p></div></td></tr>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<!-- ── MODAL: ADD / EDIT ───────────────────────────────── -->
<div class="modal fade ci-modal" id="levelModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="<?= site_url('admin/classes/levels/save') ?>" method="post" id="levelForm">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="levelId">

                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">
                        <i class="fas fa-plus-circle" id="modalIcon"></i>
                        <span id="modalTitleText">Novo Nível de Ensino</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">

                        <!-- Level name -->
                        <div class="col-12">
                            <label class="form-label-ci" for="level_name">
                                <i class="fas fa-tag"></i> Nome do Nível <span class="req">*</span>
                            </label>
                            <input type="text" class="form-input-ci" id="level_name"
                                   name="level_name" placeholder="Ex: 1ª Classe" required>
                        </div>

                        <!-- Code -->
                        <div class="col-12">
                            <label class="form-label-ci" for="level_code">
                                <i class="fas fa-barcode"></i> Código <span class="req">*</span>
                            </label>
                            <input type="text" class="form-input-ci" id="level_code"
                                   name="level_code" placeholder="Ex: PRI-01" required>
                            <div class="form-hint"><i class="fas fa-info-circle"></i> Código único para identificação do nível</div>
                        </div>

                        <!-- Education cycle -->
                        <div class="col-12">
                            <label class="form-label-ci" for="education_level">
                                <i class="fas fa-school"></i> Ciclo de Ensino <span class="req">*</span>
                            </label>
                            <select class="form-select-ci" id="education_level" name="education_level" required>
                                <option value="">Selecione o ciclo...</option>
                                <option value="Iniciação">Iniciação</option>
                                <option value="Primário">Primário</option>
                                <option value="1º Ciclo">1º Ciclo</option>
                                <option value="2º Ciclo">2º Ciclo</option>
                                <option value="Ensino Médio">Ensino Médio</option>
                            </select>
                        </div>

                        <!-- Grade + Order -->
                        <div class="col-6">
                            <label class="form-label-ci" for="grade_number">
                                <i class="fas fa-sort-numeric-up"></i> Classe <span class="req">*</span>
                            </label>
                            <input type="number" class="form-input-ci" id="grade_number"
                                   name="grade_number" min="1" max="13"
                                   placeholder="1–13" required>
                            <div class="form-hint"><i class="fas fa-info-circle"></i> Número da classe (1–13)</div>
                        </div>
                        <div class="col-6">
                            <label class="form-label-ci" for="sort_order">
                                <i class="fas fa-sort"></i> Ordem de Exibição
                            </label>
                            <input type="number" class="form-input-ci" id="sort_order"
                                   name="sort_order" value="0" placeholder="0">
                            <div class="form-hint"><i class="fas fa-info-circle"></i> Sequência na lista</div>
                        </div>

                        <!-- Active toggle -->
                        <div class="col-12">
                            <label class="form-label-ci">Estado</label>
                            <label class="toggle-row checked" id="toggleRow" for="is_active">
                                <div>
                                    <div class="tl-info">Nível Ativo</div>
                                    <div class="tl-sub">Desative para ocultar este nível das listagens</div>
                                </div>
                                <label class="ci-switch">
                                    <input type="checkbox" id="is_active" name="is_active" value="1" checked>
                                    <span class="ci-switch-track"></span>
                                </label>
                            </label>
                        </div>

                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-modal-cancel" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-modal-save">
                        <i class="fas fa-save"></i> <span id="btnSaveText">Guardar</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script>
$(document).ready(function () {

    /* ── DataTable ─────────────────────────────────────── */
    $('#levelsTable').DataTable({
        language: { url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json' },
        order: [[5, 'asc']]
    });

    /* ── Toggle sync ───────────────────────────────────── */
    $('#is_active').on('change', function () {
        $('#toggleRow').toggleClass('checked', this.checked);
    });

    /* ── Reset modal to "new" state ────────────────────── */
    function resetModal() {
        $('#modalTitleText').text('Novo Nível de Ensino');
        $('#modalIcon').removeClass('fa-edit').addClass('fa-plus-circle');
        $('#btnSaveText').text('Guardar');
        $('#levelId').val('');
        $('#levelForm')[0].reset();
        $('#is_active').prop('checked', true);
        $('#toggleRow').addClass('checked');
        $('#sort_order').val(0);
    }

    /* ── New level button ──────────────────────────────── */
    $('#btnNewLevel').on('click', resetModal);

    /* ── Edit level ────────────────────────────────────── */
    $(document).on('click', '.edit-level', function () {
        var $btn = $(this);
        var isActive = $btn.data('active') == 1;

        $('#modalTitleText').text('Editar Nível de Ensino');
        $('#modalIcon').removeClass('fa-plus-circle').addClass('fa-edit');
        $('#btnSaveText').text('Atualizar');

        $('#levelId').val($btn.data('id'));
        $('#level_name').val($btn.data('name'));
        $('#level_code').val($btn.data('code'));
        $('#education_level').val($btn.data('education'));
        $('#grade_number').val($btn.data('grade'));
        $('#sort_order').val($btn.data('order'));
        $('#is_active').prop('checked', isActive);
        $('#toggleRow').toggleClass('checked', isActive);

        $('#levelModal').modal('show');
    });

    /* ── Reset on modal close (if add mode) ────────────── */
    $('#levelModal').on('hidden.bs.modal', function () {
        if (!$('#levelId').val()) resetModal();
    });

});
</script>
<?= $this->endSection() ?>