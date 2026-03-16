<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<style>
/* Apenas ajustes específicos desta página */
@media (max-width: 767px) {
    .header-row { 
        flex-direction: column; 
        align-items: flex-start !important; 
        gap: 0.75rem !important; 
    }
}
</style>

<!-- Page Header com estilo do sistema -->
<div class="ci-page-header mb-4">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-clipboard-list me-2" style="opacity:.7;font-size:1.1rem;"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/exams') ?>">Exames</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tipos de Exames</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <button type="button" class="hdr-btn primary" data-bs-toggle="modal" data-bs-target="#boardModal" id="btnNewBoard">
                <i class="fas fa-plus-circle"></i> Novo Tipo de Exame
            </button>
        </div>
    </div>
</div>

<!-- Alertas (já usa classes do sistema via view partial) -->
<?= view('admin/partials/alerts') ?>

<!-- Card com DataTable -->
<div class="ci-card">
    <div class="ci-card-header">
        <div class="ci-card-title">
            <i class="fas fa-clipboard-list text-accent"></i> Lista de Tipos de Exames
        </div>
        <span class="badge-ci primary" id="recordCount"><?= count($boards) ?> registros</span>
    </div>
    <div class="ci-card-body p0">
        <div class="table-responsive">
            <table id="boardsTable" class="table table-hover align-middle" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Código</th>
                        <th>Tipo</th>
                        <th class="text-center">Peso</th>
                        <th class="text-center">Status</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($boards)): ?>
                        <?php foreach ($boards as $board): ?>
                            <tr>
                                <td><span class="badge-ci code"><?= $board->id ?></span></td>
                                <td><span class="fw-bold"><?= esc($board->board_name) ?></span></td>
                                <td><span class="badge-ci info"><?= esc($board->board_code) ?></span></td>
                                <td>
                                    <?php 
                                    $typeClass = match($board->board_type) {
                                        'Normal' => 'success',
                                        'Recurso' => 'warning',
                                        'Especial' => 'info',
                                        'Final' => 'primary',
                                        'Admissão' => 'danger',
                                        default => 'secondary'
                                    };
                                    ?>
                                    <span class="badge-ci <?= $typeClass ?>"><?= esc($board->board_type) ?></span>
                                </td>
                                <td class="text-center"><span class="font-mono fw-bold"><?= number_format($board->weight, 1) ?></span></td>
                                <td class="text-center">
                                    <?php if ($board->is_active): ?>
                                        <span class="badge-ci success"><span class="status-dot"></span>Ativo</span>
                                    <?php else: ?>
                                        <span class="badge-ci secondary"><span class="status-dot"></span>Inativo</span>
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <div class="action-group">
                                        <!-- Botão Editar -->
                                        <button type="button" class="row-btn edit edit-board" 
                                                data-bs-toggle="tooltip" title="Editar"
                                                data-id="<?= $board->id ?>"
                                                data-name="<?= esc($board->board_name) ?>"
                                                data-code="<?= esc($board->board_code) ?>"
                                                data-type="<?= esc($board->board_type) ?>"
                                                data-weight="<?= $board->weight ?>"
                                                data-active="<?= $board->is_active ?>">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        
                                        <!-- Botão Eliminar -->
                                        <a href="<?= site_url('admin/exams/boards/delete/' . $board->id) ?>" 
                                           class="row-btn del" 
                                           data-bs-toggle="tooltip" title="Eliminar"
                                           onclick="return confirm('Tem certeza que deseja eliminar este tipo de exame?')">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <i class="fas fa-clipboard-list"></i>
                                    <h5>Nenhum tipo de exame encontrado</h5>
                                    <p>Clique em "Novo Tipo de Exame" para começar.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para Adicionar/Editar Tipo de Exame (estilo do sistema) -->
<div class="modal fade ci-modal" id="boardModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form action="<?= site_url('admin/exams/boards/save') ?>" method="post" id="boardForm">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="boardId">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">
                        <i class="fas fa-plus-circle" id="modalIcon"></i>
                        <span id="modalTitleText">Novo Tipo de Exame</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <div class="row g-3">
                        
                        <!-- Nome -->
                        <div class="col-12">
                            <label class="form-label-ci" for="board_name">
                                <i class="fas fa-tag"></i> Nome <span class="req">*</span>
                            </label>
                            <input type="text" class="form-input-ci" id="board_name" 
                                   name="board_name" placeholder="Ex: Avaliação Contínua 1" required>
                        </div>
                        
                        <!-- Código -->
                        <div class="col-12">
                            <label class="form-label-ci" for="board_code">
                                <i class="fas fa-barcode"></i> Código <span class="req">*</span>
                            </label>
                            <input type="text" class="form-input-ci" id="board_code" 
                                   name="board_code" placeholder="Ex: AC1" required>
                            <div class="form-hint"><i class="fas fa-info-circle"></i> Código único para identificação</div>
                        </div>
                        
                        <!-- Tipo -->
                        <div class="col-12">
                            <label class="form-label-ci" for="board_type">
                                <i class="fas fa-layer-group"></i> Tipo <span class="req">*</span>
                            </label>
                            <select class="form-select-ci" id="board_type" name="board_type" required>
                                <option value="">Selecione...</option>
                                <option value="Normal">Normal</option>
                                <option value="Recurso">Recurso</option>
                                <option value="Especial">Especial</option>
                                <option value="Final">Final</option>
                                <option value="Admissão">Admissão</option>
                            </select>
                        </div>
                        
                        <!-- Peso -->
                        <div class="col-12">
                            <label class="form-label-ci" for="weight">
                                <i class="fas fa-weight"></i> Peso
                            </label>
                            <input type="number" class="form-input-ci" id="weight" name="weight" 
                                   step="0.1" min="0" max="10" value="1.0">
                            <div class="form-hint"><i class="fas fa-info-circle"></i> Peso para cálculo da média final</div>
                        </div>
                        
                        <!-- Status (toggle switch) -->
                        <div class="col-12">
                            <label class="form-label-ci">Estado</label>
                            <label class="toggle-row checked" id="toggleRow" for="is_active">
                                <div>
                                    <div class="tl-info">Tipo Ativo</div>
                                    <div class="tl-sub">Desative para ocultar este tipo das listagens</div>
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
$(document).ready(function() {
    
    /* ======================================================
       DATATABLE - Usando o helper global initDataTable
       ====================================================== */
    if (typeof initDataTable !== 'undefined') {
        window.boardsTable = initDataTable('#boardsTable', {
            serverSide: false, // Tabela pequena, sem serverSide
            order: [[0, 'asc']],
            columnDefs: [
                { targets: 0, width: '60px', className: 'text-center' }, // ID
                { targets: 4, className: 'text-center' }, // Peso
                { targets: 5, className: 'text-center' }, // Status
                { targets: 6, className: 'text-center', orderable: false } // Ações
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
        $('#boardsTable').DataTable({
            language: {
                url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-BR.json'
            },
            order: [[0, 'asc']],
            columnDefs: [
                { targets: 0, className: 'text-center' },
                { targets: 4, className: 'text-center' },
                { targets: 5, className: 'text-center' },
                { targets: 6, className: 'text-center', orderable: false }
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
    
    // Função para resetar o modal para estado "novo"
    function resetModal() {
        $('#modalTitleText').text('Novo Tipo de Exame');
        $('#modalIcon').removeClass('fa-edit').addClass('fa-plus-circle');
        $('#btnSaveText').text('Guardar');
        $('#boardId').val('');
        $('#boardForm')[0].reset();
        $('#weight').val('1.0');
        $('#is_active').prop('checked', true).trigger('change');
    }
    
    // Novo tipo de exame
    $('#btnNewBoard').on('click', resetModal);
    
    // Editar tipo de exame
    $(document).on('click', '.edit-board', function() {
        const $btn = $(this);
        const isActive = $btn.data('active') == 1;
        
        $('#modalTitleText').text('Editar Tipo de Exame');
        $('#modalIcon').removeClass('fa-plus-circle').addClass('fa-edit');
        $('#btnSaveText').text('Atualizar');
        
        $('#boardId').val($btn.data('id'));
        $('#board_name').val($btn.data('name'));
        $('#board_code').val($btn.data('code'));
        $('#board_type').val($btn.data('type'));
        $('#weight').val($btn.data('weight'));
        $('#is_active').prop('checked', isActive).trigger('change');
        
        $('#boardModal').modal('show');
    });
    
    // Reset ao fechar modal (se for modo adicionar)
    $('#boardModal').on('hidden.bs.modal', function() {
        if (!$('#boardId').val()) {
            resetModal();
        }
    });
    
    /* ======================================================
       VALIDAÇÃO DO FORMULÁRIO
       ====================================================== */
    $('#boardForm').on('submit', function(e) {
        const weight = parseFloat($('#weight').val());
        if (weight < 0 || weight > 10) {
            e.preventDefault();
            if (typeof toastr !== 'undefined') {
                toastr.error('O peso deve estar entre 0 e 10');
            } else {
                alert('O peso deve estar entre 0 e 10');
            }
        }
    });

});
</script>
<?= $this->endSection() ?>