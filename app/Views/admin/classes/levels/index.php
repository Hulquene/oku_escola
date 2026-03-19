<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>


<!-- PAGE HEADER -->
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

<!-- TABLE CARD -->
<div class="ci-card">
    <div class="ci-card-header">
        <div class="ci-card-title"><i class="fas fa-layer-group"></i> Lista de Níveis de Ensino</div>
        <span class="badge-ci primary" id="recordCount"><?= count($levels) ?> registros</span>
    </div>
    <div class="ci-card-body p0">
        <div style="overflow-x:auto; padding: 0 1.25rem 1.25rem;">
            <table id="teachersTable" class="ci-table" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nível</th>
                        <th>Código</th>
                        <th>Ciclo de Ensino</th>
                        <th class="text-center">Classe</th>
                        <th class="text-center">Ordem</th>
                        <th>Estado</th>
                        <th class="text-center">Currículo / Cursos</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($levels)): ?>
                        <?php foreach ($levels as $level): ?>
                            <?php 
                            // Determinar se é Ensino Médio (níveis que usam cursos)
                            $isHighSchool = in_array($level['education_level'], ['2º Ciclo', 'Ensino Médio']);
                            
                            // Contar disciplinas do nível (apenas para Ensino Geral)
                            $disciplineCount = 0;
                            if (!$isHighSchool) {
                                $gradeDisciplineModel = new \App\Models\GradeDisciplineModel();
                                $disciplineCount = $gradeDisciplineModel
                                    ->where('grade_level_id', $level['id'])
                                    ->countAllResults();
                            }
                            
                            // Contar cursos disponíveis para este nível (se for Ensino Médio)
                            $coursesCount = 0;
                            if ($isHighSchool) {
                                $courseModel = new \App\Models\CourseModel();
                                $coursesCount = $courseModel
                                    ->where('start_grade_id <=', $level['id'])
                                    ->where('end_grade_id >=', $level['id'])
                                    ->where('is_active', 1)
                                    ->countAllResults();
                            }
                            ?>
                        <tr>
                            <td><span class="badge-ci code"><?= $level['id'] ?></span></td>
                            <td><span class="fw-bold"><?= esc($level['level_name']) ?></span></td>
                            <td><span class="badge-ci code"><?= esc($level['level_code']) ?></span></td>
                            <td>
                                <span class="badge-ci <?= $isHighSchool ? 'warning' : 'info' ?>">
                                    <?= esc($level['education_level']) ?>
                                </span>
                            </td>
                            <td class="text-center">
                                <span class="fw-bold"><?= $level['grade_number']?><span class="text-muted">ª Classe</span></span>
                            </td>
                            <td class="text-center"><span class="font-mono"><?= $level['sort_order']?></span></td>
                            <td>
                                <?php if ($level['is_active']): ?>
                                    <span class="badge-ci success"><span class="status-dot"></span>Ativo</span>
                                <?php else: ?>
                                    <span class="badge-ci secondary"><span class="status-dot"></span>Inativo</span>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <?php if ($isHighSchool): ?>
                                    <!-- Para 2º Ciclo e Ensino Médio - link para cursos -->
                                    <a href="<?= site_url('admin/courses?grade_level_id=' . $level['id']) ?>" 
                                       class="badge-ci warning text-decoration-none"
                                       data-bs-toggle="tooltip" title="Ver cursos deste nível">
                                        <i class="fas fa-graduation-cap"></i>
                                        <?= $coursesCount ?> Curso(s)
                                    </a>
                                <?php else: ?>
                                    <!-- Para Ensino Geral - botão ver currículo -->
                                    <a href="<?= site_url('admin/academic/grade-curriculum/' . $level['id']) ?>" 
                                       class="badge-ci info text-decoration-none"
                                       data-bs-toggle="tooltip" title="Ver disciplinas do nível">
                                        <i class="fas fa-book-open"></i>
                                        <?= $disciplineCount ?> Disciplina(s)
                                    </a>
                                <?php endif; ?>
                            </td>
                            <td class="text-center">
                                <div class="action-group">
                                    
                                    <!-- Botão Ver Currículo/Cursos (contextual) -->
                                    <?php if ($isHighSchool): ?>
                                        <a href="<?= site_url('admin/academic/courses?grade_level_id=' . $level['id']) ?>" 
                                           class="row-btn" 
                                           data-bs-toggle="tooltip" title="Ver Cursos">
                                            <i class="fas fa-graduation-cap"></i>
                                        </a>
                                    <?php else: ?>
                                        <a href="<?= site_url('admin/academic/grade-curriculum/' . $level['id']) ?>" 
                                           class="row-btn" 
                                           data-bs-toggle="tooltip" title="Ver Disciplinas">
                                            <i class="fas fa-book-open"></i>
                                        </a>
                                    <?php endif; ?>
                                    
                                    <!-- Botão Editar (sempre disponível) -->
                                    <button type="button" class="row-btn edit edit-level" data-bs-toggle="tooltip" title="Editar"
                                            data-id="<?= $level['id'] ?>"
                                            data-name="<?= esc($level['level_name']) ?>"
                                            data-code="<?= esc($level['level_code']) ?>"
                                            data-education="<?= esc($level['education_level']) ?>"
                                            data-grade="<?= $level['grade_number']?>"
                                            data-order="<?= $level['sort_order']?>"
                                            data-active="<?= $level['is_active'] ?>">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    
                                    <!-- Botão Eliminar (apenas para ativos) -->
                                    <?php if ($level['is_active']): ?>
                                        <a href="<?= site_url('admin/classes/levels/delete/' . $level['id']) ?>"
                                           class="row-btn del" data-bs-toggle="tooltip" title="Eliminar"
                                           onclick="return confirm('Tem certeza que deseja eliminar este nível?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9">
                                <div class="empty-state">
                                    <i class="fas fa-layer-group"></i>
                                    <h5>Nenhum nível de ensino encontrado</h5>
                                    <p>Clique em "Novo Nível" para começar.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- MODAL: ADD / EDIT -->
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
                    <button type="button" class="btn-ci outline" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn-ci primary">
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

    /* ======================================================
       DATATABLE - Usando o helper global initDataTable
       ====================================================== */
    if (typeof initDataTable !== 'undefined') {
        window.levelsTable = initDataTable('#levelsTable', {
            order: [[5, 'asc']], // Ordenar pela coluna "Ordem" (índice 5)
            pageLength: 25,
            columnDefs: [
                { targets: 0, width: '60px', className: 'text-center' }, // ID
                { targets: 4, className: 'text-center' }, // Classe
                { targets: 5, className: 'text-center' }, // Ordem
                { targets: 7, className: 'text-center' }, // Currículo/Cursos
                { targets: 8, className: 'text-center', orderable: false } // Ações
            ],
            drawCallback: function() {
                // Re-inicializar tooltips
                $('[data-bs-toggle="tooltip"]').each(function() {
                    try {
                        const tooltip = bootstrap.Tooltip.getInstance(this);
                        if (tooltip) {
                            tooltip.dispose();
                        }
                        new bootstrap.Tooltip(this);
                    } catch(e) {}
                });
                
                // Atualizar contador de registros
                const info = this.api().page.info();
                $('#recordCount').text(info.recordsDisplay + ' registros');
            }
        });
    } else {
        // Fallback caso o helper não exista
        $('#levelsTable').DataTable({
            
            order: [[5, 'asc']],
            pageLength: 25,
            columnDefs: [
                { targets: 0, width: '60px', className: 'text-center' },
                { targets: 4, className: 'text-center' },
                { targets: 5, className: 'text-center' },
                { targets: 7, className: 'text-center' },
                { targets: 8, className: 'text-center', orderable: false }
            ]
        });
    }

    /* ======================================================
       TOGGLE SYNC
       ====================================================== */
    $('#is_active').on('change', function () {
        $('#toggleRow').toggleClass('checked', this.checked);
    });

    /* ======================================================
       MODAL FUNCTIONS
       ====================================================== */
    function resetModal() {
        $('#modalTitleText').text('Novo Nível de Ensino');
        $('#modalIcon').removeClass('fa-edit').addClass('fa-plus-circle');
        $('#btnSaveText').text('Guardar');
        $('#levelId').val('');
        $('#levelForm')[0].reset();
        $('#is_active').prop('checked', true).trigger('change');
        $('#sort_order').val(0);
    }

    // New level button
    $('#btnNewLevel').on('click', resetModal);

    // Edit level
    $(document).on('click', '.edit-level', function () {
        const $btn = $(this);
        const isActive = $btn.data('active') == 1;

        $('#modalTitleText').text('Editar Nível de Ensino');
        $('#modalIcon').removeClass('fa-plus-circle').addClass('fa-edit');
        $('#btnSaveText').text('Atualizar');

        $('#levelId').val($btn.data('id'));
        $('#level_name').val($btn.data('name'));
        $('#level_code').val($btn.data('code'));
        $('#education_level').val($btn.data('education'));
        $('#grade_number').val($btn.data('grade'));
        $('#sort_order').val($btn.data('order'));
        $('#is_active').prop('checked', isActive).trigger('change');

        $('#levelModal').modal('show');
    });

    // Reset on modal close (if in add mode)
    $('#levelModal').on('hidden.bs.modal', function () {
        if (!$('#levelId').val()) {
            resetModal();
        }
    });

    // Validação personalizada do formulário
    $('#levelForm').on('submit', function(e) {
        const gradeNumber = parseInt($('#grade_number').val());
        if (gradeNumber < 1 || gradeNumber > 13) {
            e.preventDefault();
            toastr?.error('A classe deve estar entre 1 e 13');
        }
    });

});
</script>
<?= $this->endSection() ?>