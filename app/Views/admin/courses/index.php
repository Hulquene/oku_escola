<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

<style>
/* Ajustes específicos para esta página */
@media (max-width: 767px) {
    .header-row { 
        flex-direction: column; 
        align-items: flex-start !important; 
        gap: 0.75rem !important; 
    }
    .stats-col-filter, .stats-col-card { 
        width: 100% !important; 
        flex: 0 0 100% !important; 
        max-width: 100% !important; 
    }
    .filter-row > div { 
        flex: 0 0 100%; 
        max-width: 100%; 
    }
}

@media print {
    .btn-header-new, .filter-card, .btn-action-sm, .row-btn.del,
    .dataTables_filter, .dataTables_length, .dt-buttons { 
        display: none !important; 
    }
    .ci-page-header { 
        background: #1B2B4B !important; 
        -webkit-print-color-adjust: exact; 
    }
}

/* Estilo para badges de tipo */
.type-badge {
    font-size: 0.65rem;
    font-weight: 700;
    padding: 0.22rem 0.6rem;
    border-radius: 50px;
    display: inline-flex;
    align-items: center;
    gap: 0.25rem;
    white-space: nowrap;
}

.type-badge.primary { background: rgba(27,43,75,0.1); color: var(--primary); }
.type-badge.success { background: rgba(22,168,125,0.1); color: var(--success); }
.type-badge.warning { background: rgba(232,160,32,0.1); color: var(--warning); }
.type-badge.info { background: rgba(59,127,232,0.1); color: var(--accent); }
.type-badge.secondary { background: rgba(107,122,153,0.1); color: var(--text-secondary); }
.type-badge.dark { background: rgba(27,43,75,0.15); color: #2D3748; }

/* Estilo para chips de duração e disciplinas */
.num-chip {
    font-family: var(--font-mono);
    font-size: 0.75rem;
    font-weight: 600;
    background: var(--surface);
    border: 1.5px solid var(--border);
    border-radius: 7px;
    padding: 0.2rem 0.5rem;
    display: inline-block;
}

.num-chip.blue {
    background: rgba(59,127,232,0.08);
    border-color: rgba(59,127,232,0.2);
    color: var(--accent);
}

.num-chip.green {
    background: rgba(22,168,125,0.08);
    border-color: rgba(22,168,125,0.2);
    color: var(--success);
}

/* Estilo para código do curso */
.code-badge {
    font-family: var(--font-mono);
    font-size: 0.7rem;
    font-weight: 600;
    background: rgba(27,43,75,0.07);
    color: var(--primary);
    padding: 0.15rem 0.45rem;
    border-radius: 5px;
    display: inline-block;
}

/* Status badges */
.status-active {
    color: var(--success);
    background: rgba(22,168,125,0.1);
    font-size: 0.7rem;
    font-weight: 700;
    padding: 0.22rem 0.6rem;
    border-radius: 50px;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    white-space: nowrap;
}

.status-inactive {
    color: var(--danger);
    background: rgba(232,70,70,0.08);
    font-size: 0.7rem;
    font-weight: 700;
    padding: 0.22rem 0.6rem;
    border-radius: 50px;
    letter-spacing: 0.05em;
    text-transform: uppercase;
    display: inline-flex;
    align-items: center;
    gap: 0.3rem;
    white-space: nowrap;
}

.status-dot {
    width: 6px;
    height: 6px;
    border-radius: 50%;
    background: currentColor;
    display: inline-block;
    flex-shrink: 0;
}

/* Row ID */
.row-id {
    font-family: var(--font-mono);
    font-size: 0.7rem;
    color: var(--text-muted);
    font-weight: 500;
}

/* Níveis */
.level-range {
    font-size: 0.8rem;
    font-weight: 600;
    color: var(--text-primary);
}

.level-sub {
    font-size: 0.7rem;
    color: var(--text-muted);
}
</style>

<!-- Page Header -->
<div class="ci-page-header mb-4">
    <div class="ci-page-header-inner">
        <div>
            <h1><i class="fas fa-layer-group me-2"></i><?= $title ?></h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="<?= route_to('admin.dashboard') ?>">Dashboard</a></li>
                    <li class="breadcrumb-item"><a href="<?= route_to('academic.years') ?>">Académico</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Cursos</li>
                </ol>
            </nav>
        </div>
        <div class="hdr-actions">
            <a href="<?= route_to('admin.courses.form') ?>" class="hdr-btn primary">
                <i class="fas fa-plus-circle"></i> Novo Curso
            </a>
        </div>
    </div>
</div>

<!-- Alertas -->
<?= view('admin/partials/alerts') ?>

<!-- Cards de Estatísticas -->
<div class="stat-grid" id="statsContainer">
    <div class="stat-card">
        <div class="stat-icon blue">
            <i class="fas fa-layer-group"></i>
        </div>
        <div>
            <div class="stat-label">Total</div>
            <div class="stat-value" id="totalCourses"><?= $stats['total'] ?? 0 ?></div>
            <div class="stat-sub">Cursos</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon green">
            <i class="fas fa-check-circle"></i>
        </div>
        <div>
            <div class="stat-label">Ativos</div>
            <div class="stat-value" id="activeCourses"><?= $stats['active'] ?? 0 ?></div>
            <div class="stat-sub">Em andamento</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon orange">
            <i class="fas fa-times-circle"></i>
        </div>
        <div>
            <div class="stat-label">Inativos</div>
            <div class="stat-value" id="inactiveCourses"><?= $stats['inactive'] ?? 0 ?></div>
            <div class="stat-sub">Arquivados</div>
        </div>
    </div>
    
    <div class="stat-card">
        <div class="stat-icon navy">
            <i class="fas fa-chart-pie"></i>
        </div>
        <div>
            <div class="stat-label">Tipos</div>
            <div class="stat-value" id="totalTypes"><?= count($stats['by_type'] ?? []) ?></div>
            <div class="stat-sub">Categorias</div>
        </div>
    </div>
</div>

<!-- Filtros Avançados -->
<div class="ci-card mb-4">
    <div class="ci-card-header">
        <div class="ci-card-title"><i class="fas fa-filter"></i> Filtros</div>
    </div>
    <div class="ci-card-body">
        <div class="filter-grid">
            <!-- Tipo de Curso -->
            <div>
                <label class="filter-label">Tipo de Curso</label>
                <select class="filter-select" id="filterType">
                    <option value="">Todos os tipos</option>
                    <?php foreach ($types as $key => $value): ?>
                        <option value="<?= $key ?>" <?= ($selectedType ?? '') == $key ? 'selected' : '' ?>><?= $value ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <!-- Status -->
            <div>
                <label class="filter-label">Status</label>
                <select class="filter-select" id="filterStatus">
                    <option value="">Todos</option>
                    <option value="active" <?= ($selectedStatus ?? '') == 'active' ? 'selected' : '' ?>>Ativos</option>
                    <option value="inactive" <?= ($selectedStatus ?? '') == 'inactive' ? 'selected' : '' ?>>Inativos</option>
                </select>
            </div>
            
            <!-- Campo vazio para manter layout (removido ano letivo) -->
            <div></div>
        </div>
        
        <div class="filter-actions">
            <button class="btn-filter apply" id="btnFilter"><i class="fas fa-filter"></i> Filtrar</button>
            <button class="btn-filter clear" id="btnClear"><i class="fas fa-undo"></i> Limpar</button>
        </div>
    </div>
</div>

<!-- Distribution Chips (se houver dados) -->
<?php if (!empty($stats['by_type'])): ?>
<div class="ci-card mb-3">
    <div class="ci-card-header">
        <div class="ci-card-title"><i class="fas fa-chart-pie"></i> Distribuição por Tipo</div>
    </div>
    <div class="ci-card-body">
        <div class="d-flex flex-wrap gap-2">
            <?php foreach ($stats['by_type'] as $type): ?>
                <div class="dist-chip">
                    <span><?= $type->course_type ?></span>
                    <span class="chip-count"><?= $type->total ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Data Table -->
<div class="ci-card">
    <div class="ci-card-header">
        <div class="ci-card-title"><i class="fas fa-list"></i> Lista de Cursos</div>
        <span class="badge bg-primary" id="recordCount">0 registros</span>
    </div>
    <div style="overflow-x:auto; padding: 0 1.25rem 1.25rem;">
        <table id="coursesTable" class="ci-table" style="width:100%">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Código</th>
                    <th>Nome do Curso</th>
                    <th>Tipo</th>
                    <th>Níveis</th>
                    <th class="text-center">Duração</th>
                    <th class="text-center">Disciplinas</th>
                    <th>Status</th>
                    <th class="text-center">Ações</th>
                </tr>
            </thead>
            <tbody>
                <!-- DataTables preenche via AJAX -->
            </tbody>
        </table>
    </div>
</div>

<!-- Modal de Confirmação de Eliminação -->
<div class="modal fade modal-custom" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: var(--danger);">
                <h5 class="modal-title text-white">
                    <i class="fas fa-exclamation-triangle me-2"></i>Confirmar Eliminação
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja eliminar o curso <strong id="deleteCourseName"></strong>?</p>
                <div class="warning-alert">
                    <i class="fas fa-info-circle me-1"></i>
                    Esta ação não pode ser desfeita. Todas as disciplinas associadas serão removidas.
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn-filter clear" data-bs-dismiss="modal">Cancelar</button>
                <a href="#" id="confirmDeleteBtn" class="btn-filter" style="background:var(--danger); color:#fff;">
                    <i class="fas fa-trash me-1"></i>Eliminar
                </a>
            </div>
        </div>
    </div>
</div>

<?= $this->endSection() ?>

<?= $this->section('scripts') ?>

<script>
    $(document).ready(function() {
    // Carregar estatísticas iniciais
    loadStats();
    
    // Inicializar DataTable com server-side
    var table = $('#coursesTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '<?= route_to('admin.courses.get-table-data') ?>', 
            type: 'POST',
            data: function(d) {
                d.filter_type = $('#filterType').val();
                d.filter_status = $('#filterStatus').val();
                d['<?= csrf_token() ?>'] = '<?= csrf_hash() ?>';
            },
            error: function(xhr, error, thrown) {
                console.log('Erro AJAX:', error);
                console.log('Resposta:', xhr.responseText);
            }
        },
        columns: [
            { data: 'id_html' },
            { data: 'code_html' },
            { data: 'name_html' },
            { data: 'type_html' },
            { data: 'levels_html' },
            { data: 'duration_html', className: 'text-center' },
            { data: 'disciplines_html', className: 'text-center' },
            { data: 'status_html' },
            { data: 'actions', className: 'text-center' }
        ],
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json'
        },
        order: [[0, 'desc']],
        pageLength: 25,
        lengthMenu: [10, 25, 50, 100],
        drawCallback: function(settings) {
            // Atualizar contador de registros
            var info = settings.json;
            if (info) {
                $('#recordCount').text(info.recordsFiltered + ' registros');
            }
            
            // Re-inicializar tooltips
            $('[data-bs-toggle="tooltip"]').tooltip();
        }
    });
    
    // Filtrar ao clicar no botão
    $('#btnFilter').on('click', function() {
        table.ajax.reload();
    });
    
    // Filtrar ao mudar selects
    $('#filterType, #filterStatus').on('change', function() {
        table.ajax.reload();
    });
    
    // Limpar filtros
    $('#btnClear').on('click', function() {
        $('#filterType').val('');
        $('#filterStatus').val('');
        table.ajax.reload();
    });
    
    // Função para carregar estatísticas
    function loadStats() {
        $.ajax({
            url: '<?= route_to('admin.courses.get-stats') ?>',
            method: 'GET',
            dataType: 'json',
            success: function(data) {
                $('#totalCourses').text(data.total || 0);
                $('#activeCourses').text(data.active || 0);
                $('#inactiveCourses').text(data.inactive || 0);
                $('#totalTypes').text(data.types_count || 0);
            },
            error: function(xhr, status, error) {
                console.error('Erro ao carregar estatísticas:', error);
            }
        });
    }
    
    // Atualizar estatísticas periodicamente
    setInterval(loadStats, 30000);
});

// Função para confirmar eliminação - CORRIGIDA
function confirmDelete(id, name) {
    $('#deleteCourseName').text(name);
    // Usar site_url é mais seguro para URLs com parâmetros dinâmicos
    $('#confirmDeleteBtn').attr('href', '<?= site_url('admin/courses/delete/') ?>' + id);
    new bootstrap.Modal(document.getElementById('deleteModal')).show();
}
</script>
<?= $this->endSection() ?>