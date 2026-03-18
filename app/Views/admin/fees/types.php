<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<!-- Page Header com estilo do sistema -->
<div class="ci-page-header">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-tags me-2" style="color: var(--accent);"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= site_url('admin/fees') ?>">Propinas</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Tipos de Taxas</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <button class="hdr-btn success" onclick="exportToExcel()">
                <i class="fas fa-file-excel"></i> Exportar
            </button>
            <button type="button" class="hdr-btn primary" data-bs-toggle="modal" data-bs-target="#typeModal">
                <i class="fas fa-plus-circle"></i> Novo Tipo de Taxa
            </button>
        </div>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Cards de Estatísticas -->
<div class="stat-grid mb-4">
    <?php
    $totalTypes = count($types ?? []);
    $activeTypes = array_filter($types ?? [], fn($t) => ($t['is_active'] ?? 0) == 1);
    $recurringTypes = array_filter($types ?? [], fn($t) => ($t['is_recurring'] ?? 0) == 1);
    $feeTypes = array_filter($types ?? [], fn($t) => ($t['type_category'] ?? '') == 'Propina');
    ?>
    
    <div class="stat-card">
        <div class="stat-icon navy">
            <i class="fas fa-tags"></i>
        </div>
        <div>
            <div class="stat-label">Total</div>
            <div class="stat-value"><?= $totalTypes ?></div>
            <div class="stat-sub">Tipos de taxas</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-check-circle"></i>
        </div>
        <div>
            <div class="stat-label">Ativos</div>
            <div class="stat-value"><?= count($activeTypes) ?></div>
            <div class="stat-sub"><?= $totalTypes > 0 ? round((count($activeTypes)/$totalTypes)*100) : 0 ?>% do total</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-sync-alt"></i>
        </div>
        <div>
            <div class="stat-label">Recorrentes</div>
            <div class="stat-value"><?= count($recurringTypes) ?></div>
            <div class="stat-sub">Taxas periódicas</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-money-bill-wave"></i>
        </div>
        <div>
            <div class="stat-label">Propinas</div>
            <div class="stat-value"><?= count($feeTypes) ?></div>
            <div class="stat-sub">Tipos de propina</div>
        </div>
    </div>
</div>

<!-- Filtros Avançados -->
<div class="ci-card mb-4">
    <div class="ci-card-header">
        <div class="ci-card-title">
            <i class="fas fa-filter"></i>
            <span>Filtros</span>
        </div>
        <span class="badge-ci primary" id="filterCount">0 filtros ativos</span>
    </div>
    
    <div class="ci-card-body">
        <div class="filter-grid">
            <!-- Categoria -->
            <div>
                <label class="filter-label">Categoria</label>
                <select class="filter-select" id="filterCategory">
                    <option value="">Todas</option>
                    <option value="Propina">Propina</option>
                    <option value="Matrícula">Matrícula</option>
                    <option value="Inscrição">Inscrição</option>
                    <option value="Taxa de Exame">Taxa de Exame</option>
                    <option value="Taxa Administrativa">Taxa Administrativa</option>
                    <option value="Multa">Multa</option>
                    <option value="Outro">Outro</option>
                </select>
            </div>
            
            <!-- Recorrência -->
            <div>
                <label class="filter-label">Recorrência</label>
                <select class="filter-select" id="filterRecurring">
                    <option value="">Todas</option>
                    <option value="1">Recorrentes</option>
                    <option value="0">Não recorrentes</option>
                </select>
            </div>
            
            <!-- Status -->
            <div>
                <label class="filter-label">Status</label>
                <select class="filter-select" id="filterStatus">
                    <option value="">Todos</option>
                    <option value="1">Ativos</option>
                    <option value="0">Inativos</option>
                </select>
            </div>
            
            <!-- Busca -->
            <div>
                <label class="filter-label">Buscar</label>
                <div class="input-group">
                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                    <input type="text" class="filter-select" id="filterSearch" placeholder="Nome ou código...">
                </div>
            </div>
        </div>
        
        <div class="filter-actions">
            <button type="button" class="btn-filter apply" id="btnFilter">
                <i class="fas fa-search me-1"></i>Filtrar
            </button>
            <button type="button" class="btn-filter clear" id="btnClear">
                <i class="fas fa-undo me-1"></i>Limpar
            </button>
        </div>
    </div>
</div>

<!-- Data Table -->
<div class="ci-card">
    <div class="ci-card-header">
        <div class="ci-card-title">
            <i class="fas fa-tags"></i>
            <span>Lista de Tipos de Taxas</span>
        </div>
        <span class="badge-ci primary" id="recordCount">
            <i class="fas fa-database me-1"></i> <?= count($types) ?> registros
        </span>
    </div>
    
    <div class="ci-card-body p0">
        <div class="table-responsive">
            <table id="typesTable" class="ci-table">
                <thead>
                    <tr>
                        <th width="60">ID</th>
                        <th>Nome</th>
                        <th>Código</th>
                        <th>Categoria</th>
                        <th>Recorrente</th>
                        <th>Período</th>
                        <th>Descrição</th>
                        <th class="center">Status</th>
                        <th class="center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!empty($types)): ?>
                        <?php foreach ($types as $type): ?>
                            <tr>
                                <td><span class="id-chip">#<?= str_pad($type['id'], 4, '0', STR_PAD_LEFT) ?></span></td>
                                <td>
                                    <div class="fw-600"><?= esc($type['type_name']) ?></div>
                                </td>
                                <td><span class="code-badge"><?= esc($type['type_code']) ?></span></td>
                                <td>
                                    <?php
                                    $categoryClass = match($type['type_category']) {
                                        'Propina' => 'primary',
                                        'Matrícula' => 'success',
                                        'Inscrição' => 'info',
                                        'Taxa de Exame' => 'warning',
                                        'Taxa Administrativa' => 'secondary',
                                        'Multa' => 'danger',
                                        default => 'dark'
                                    };
                                    ?>
                                    <span class="badge-ci <?= $categoryClass ?>">
                                        <?= esc($type['type_category']) ?>
                                    </span>
                                </td>
                                <td class="center">
                                    <?php if ($type['is_recurring']): ?>
                                        <span class="status-badge active">
                                            <i class="fas fa-sync-alt me-1"></i>Sim
                                        </span>
                                    <?php else: ?>
                                        <span class="status-badge inactive">
                                            <i class="fas fa-times me-1"></i>Não
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($type['recurrence_period']): ?>
                                        <span class="badge-ci info">
                                            <i class="fas fa-calendar-alt me-1"></i>
                                            <?= esc($type['recurrence_period']) ?>
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if (!empty($type['description'])): ?>
                                        <span data-bs-toggle="tooltip" title="<?= esc($type['description']) ?>">
                                            <?= esc(substr($type['description'], 0, 30)) ?>...
                                        </span>
                                    <?php else: ?>
                                        <span class="text-muted">—</span>
                                    <?php endif; ?>
                                </td>
                                <td class="center">
                                    <?php if ($type['is_active']): ?>
                                        <span class="status-badge active">
                                            <i class="fas fa-circle me-1" style="font-size: 6px;"></i>Ativo
                                        </span>
                                    <?php else: ?>
                                        <span class="status-badge inactive">
                                            <i class="fas fa-circle me-1" style="font-size: 6px;"></i>Inativo
                                        </span>
                                    <?php endif; ?>
                                </td>
                                <td class="center">
                                    <div class="action-group">
                                        <button type="button" class="row-btn edit edit-type" 
                                                data-id="<?= $type['id'] ?>"
                                                data-name="<?= esc($type['type_name']) ?>"
                                                data-code="<?= esc($type['type_code']) ?>"
                                                data-category="<?= esc($type['type_category']) ?>"
                                                data-recurring="<?= $type['is_recurring'] ?>"
                                                data-period="<?= esc($type['recurrence_period']) ?>"
                                                data-description="<?= esc($type['description']) ?>"
                                                data-active="<?= $type['is_active'] ?>"
                                                data-bs-toggle="tooltip" 
                                                title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </button>
                                        <?php if ($type['is_active']): ?>
                                            <button type="button" class="row-btn toggle-status" 
                                                    data-id="<?= $type['id'] ?>"
                                                    data-name="<?= esc($type['type_name']) ?>"
                                                    data-status="1"
                                                    data-bs-toggle="tooltip" 
                                                    title="Desativar">
                                                <i class="fas fa-ban"></i>
                                            </button>
                                        <?php else: ?>
                                            <button type="button" class="row-btn toggle-status" 
                                                    data-id="<?= $type['id'] ?>"
                                                    data-name="<?= esc($type['type_name']) ?>"
                                                    data-status="0"
                                                    data-bs-toggle="tooltip" 
                                                    title="Ativar">
                                                <i class="fas fa-check-circle"></i>
                                            </button>
                                        <?php endif; ?>
                                        <a href="<?= site_url('admin/fees/types/delete/' . $type['id']) ?>" 
                                           class="row-btn del" 
                                           onclick="return confirm('Tem certeza que deseja eliminar este tipo de taxa?')"
                                           data-bs-toggle="tooltip" 
                                           title="Eliminar">
                                            <i class="fas fa-trash"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                <div class="empty-state">
                                    <i class="fas fa-tags fa-3x mb-3"></i>
                                    <h5>Nenhum tipo de taxa encontrado</h5>
                                    <p class="text-muted">Clique no botão "Novo Tipo de Taxa" para adicionar.</p>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal para Adicionar/Editar Tipo (estilo do sistema) -->
<div class="modal fade modal-custom" id="typeModal" tabindex="-1">
    <div class="modal-dialog modal-md">
        <div class="modal-content">
            <form action="<?= site_url('admin/fees/types/save') ?>" method="post">
                <?= csrf_field() ?>
                <input type="hidden" name="id" id="typeId">
                
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">
                        <i class="fas fa-tag me-2"></i>
                        <span>Novo Tipo de Taxa</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                
                <div class="modal-body">
                    <!-- Informações Básicas -->
                    <div class="form-section-title">
                        <i class="fas fa-info-circle"></i> Informações Básicas
                    </div>
                    
                    <div class="mb-3">
                        <label for="type_name" class="form-label-ci">
                            <i class="fas fa-tag"></i> Nome <span class="req">*</span>
                        </label>
                        <input type="text" class="form-input-ci" id="type_name" name="type_name" required>
                    </div>
                    
                    <div class="mb-3">
                        <label for="type_code" class="form-label-ci">
                            <i class="fas fa-code"></i> Código <span class="req">*</span>
                        </label>
                        <input type="text" class="form-input-ci" id="type_code" name="type_code" 
                               placeholder="Ex: FEE-MONTHLY" required>
                        <div class="form-hint">
                            <i class="fas fa-info-circle"></i>
                            Código único para identificação (ex: PROP-MENSAL, TAXA-MATRICULA)
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label for="type_category" class="form-label-ci">
                            <i class="fas fa-folder"></i> Categoria <span class="req">*</span>
                        </label>
                        <select class="form-select-ci" id="type_category" name="type_category" required>
                            <option value="">Selecione...</option>
                            <option value="Propina">Propina</option>
                            <option value="Matrícula">Matrícula</option>
                            <option value="Inscrição">Inscrição</option>
                            <option value="Taxa de Exame">Taxa de Exame</option>
                            <option value="Taxa Administrativa">Taxa Administrativa</option>
                            <option value="Multa">Multa</option>
                            <option value="Outro">Outro</option>
                        </select>
                    </div>
                    
                    <!-- Recorrência -->
                    <div class="form-section-title">
                        <i class="fas fa-sync-alt"></i> Recorrência
                    </div>
                    
                    <div class="row g-3">
                        <div class="col-md-6">
                            <div class="toggle-row" id="recurringToggle">
                                <div>
                                    <div class="tl-info">Taxa Recorrente</div>
                                    <div class="tl-sub">Se ativada, será cobrada periodicamente</div>
                                </div>
                                <div class="ci-switch">
                                    <input type="checkbox" id="is_recurring" name="is_recurring" value="1">
                                    <label class="ci-switch-track" for="is_recurring"></label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="col-md-6">
                            <label for="recurrence_period" class="form-label-ci">
                                <i class="fas fa-calendar-alt"></i> Período de Recorrência
                            </label>
                            <select class="form-select-ci" id="recurrence_period" name="recurrence_period" disabled>
                                <option value="">Selecione...</option>
                                <option value="Mensal">Mensal</option>
                                <option value="Trimestral">Trimestral</option>
                                <option value="Semestral">Semestral</option>
                                <option value="Anual">Anual</option>
                            </select>
                        </div>
                    </div>
                    
                    <!-- Descrição -->
                    <div class="form-section-title">
                        <i class="fas fa-align-left"></i> Descrição
                    </div>
                    
                    <div class="mb-3">
                        <textarea class="form-input-ci" id="description" name="description" rows="3" 
                                  placeholder="Descrição detalhada da taxa..."></textarea>
                    </div>
                    
                    <!-- Status -->
                    <div class="form-section-title">
                        <i class="fas fa-toggle-on"></i> Status
                    </div>
                    
                    <div class="toggle-row" id="statusToggle">
                        <div>
                            <div class="tl-info">Ativo</div>
                            <div class="tl-sub">Disponível para uso no sistema</div>
                        </div>
                        <div class="ci-switch">
                            <input type="checkbox" id="is_active" name="is_active" value="1" checked>
                            <label class="ci-switch-track" for="is_active"></label>
                        </div>
                    </div>
                </div>
                
                <div class="modal-footer">
                    <button type="button" class="btn-filter clear" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>Cancelar
                    </button>
                    <button type="submit" class="btn-filter apply">
                        <i class="fas fa-save me-2"></i>Salvar
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Status -->
<div class="modal fade modal-custom" id="statusModal" tabindex="-1">
    <div class="modal-dialog modal-sm">
        <div class="modal-content">
            <div class="modal-header" style="background: var(--warning);">
                <h5 class="modal-title">
                    <i class="fas fa-exchange-alt me-2"></i>
                    Alterar Status
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            
            <div class="modal-body text-center">
                <p>Deseja <span id="statusAction"></span> o tipo de taxa <br>
                <strong id="statusItemName"></strong>?</p>
            </div>
            
            <div class="modal-footer">
                <button type="button" class="btn-filter clear" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="confirmStatusBtn" class="btn-filter" style="background: var(--warning); color:#fff;">
                    <i class="fas fa-check me-1"></i>Confirmar
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
<script src="https://cdn.jsdelivr.net/npm/xlsx@0.18.5/dist/xlsx.full.min.js"></script>
<script>
$(document).ready(function() {
    // Inicializar DataTable com configuração global
    var table = $('#typesTable').DataTable({
        order: [[1, 'asc']],
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100],
        language: {
            url: '<?= base_url('assets/js/vendor/datatables.pt-br.json') ?>'
        },
        columnDefs: [
            { targets: [8], orderable: false }, // Ações não ordenáveis
            { targets: [4,5,7], className: 'text-center' }
        ],
        drawCallback: function() {
            // Re-inicializar tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });
    
    // Atualizar contador de registros
    $('#recordCount').html('<i class="fas fa-database me-1"></i> ' + table.page.info().recordsDisplay + ' registros');
    
    // Filtros
    $('#btnFilter').on('click', function() {
        var category = $('#filterCategory').val();
        var recurring = $('#filterRecurring').val();
        var status = $('#filterStatus').val();
        var search = $('#filterSearch').val();
        
        table.columns().search('');
        
        if (category) table.column(3).search(category).draw();
        if (recurring !== '') table.column(4).search(recurring === '1' ? 'Sim' : 'Não').draw();
        if (status !== '') table.column(7).search(status === '1' ? 'Ativo' : 'Inativo').draw();
        if (search) table.search(search).draw();
        
        updateFilterCount();
    });
    
    // Limpar filtros
    $('#btnClear').on('click', function() {
        $('#filterCategory, #filterRecurring, #filterStatus').val('');
        $('#filterSearch').val('');
        table.search('').columns().search('').draw();
        updateFilterCount();
    });
    
    function updateFilterCount() {
        var count = 0;
        if ($('#filterCategory').val()) count++;
        if ($('#filterRecurring').val()) count++;
        if ($('#filterStatus').val()) count++;
        $('#filterCount').text(count + ' filtro' + (count !== 1 ? 's' : '') + ' ativo' + (count !== 1 ? 's' : ''));
    }
    
    // Toggle recurrence period
    $('#is_recurring').change(function() {
        $('#recurrence_period').prop('disabled', !this.checked);
        $('.ci-switch-track').toggleClass('checked', this.checked);
    });
    
    // Toggle switch styling
    $('#is_active').change(function() {
        $('.ci-switch-track').toggleClass('checked', this.checked);
    });
    
    // Edit type
    $('.edit-type').click(function() {
        $('#modalTitle span').text('Editar Tipo de Taxa');
        $('#typeId').val($(this).data('id'));
        $('#type_name').val($(this).data('name'));
        $('#type_code').val($(this).data('code'));
        $('#type_category').val($(this).data('category'));
        
        const isRecurring = $(this).data('recurring') == 1;
        $('#is_recurring').prop('checked', isRecurring);
        $('.ci-switch-track').toggleClass('checked', isRecurring);
        $('#recurrence_period').prop('disabled', !isRecurring);
        $('#recurrence_period').val($(this).data('period'));
        
        $('#description').val($(this).data('description'));
        
        const isActive = $(this).data('active') == 1;
        $('#is_active').prop('checked', isActive);
        $('.ci-switch-track').toggleClass('checked', isActive);
        
        $('#typeModal').modal('show');
    });
    
    // New type button
    $('[data-bs-target="#typeModal"]').click(function() {
        $('#modalTitle span').text('Novo Tipo de Taxa');
        $('#typeId').val('');
        $('#typeModal form')[0].reset();
        $('#is_recurring').prop('checked', false);
        $('#recurrence_period').prop('disabled', true);
        $('#is_active').prop('checked', true);
        $('.ci-switch-track').removeClass('checked');
    });
    
    // Toggle status
    $('.toggle-status').on('click', function() {
        const id = $(this).data('id');
        const name = $(this).data('name');
        const currentStatus = $(this).data('status');
        const action = currentStatus ? 'desativar' : 'ativar';
        
        $('#statusAction').text(action);
        $('#statusItemName').text(name);
        $('#confirmStatusBtn').attr('href', '<?= site_url('admin/fees/types/toggle-status/') ?>' + id);
        new bootstrap.Modal(document.getElementById('statusModal')).show();
    });
    
    // Reset modal on close
    $('#typeModal').on('hidden.bs.modal', function() {
        if (!$('#typeId').val()) {
            $(this).find('form')[0].reset();
            $('#is_recurring').prop('checked', false);
            $('#recurrence_period').prop('disabled', true);
            $('#is_active').prop('checked', true);
            $('.ci-switch-track').removeClass('checked');
        }
    });
});

// Exportar para Excel
function exportToExcel() {
    const data = [];
    const headers = [];
    
    $('#typesTable thead th').each(function(index) {
        if (index < 8) headers.push($(this).text().trim());
    });
    data.push(headers);
    
    $('#typesTable tbody tr').each(function() {
        const row = [];
        $(this).find('td').each(function(index) {
            if (index < 8) {
                let cellText = $(this).text().trim();
                row.push(cellText);
            }
        });
        if (row.length) data.push(row);
    });
    
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.aoa_to_sheet(data);
    XLSX.utils.book_append_sheet(wb, ws, 'Tipos de Taxas');
    XLSX.writeFile(wb, `tipos_taxas_${new Date().toISOString().slice(0,10)}.xlsx`);
}
</script>
<?= $this->endSection() ?>