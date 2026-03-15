<?= $this->extend('admin/layouts/index') ?>

<?= $this->section('content') ?>

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
        <div class="hdr-actions">
            <a href="<?= site_url('admin/academic/years/form-add') ?>" class="hdr-btn primary">
                <i class="fas fa-plus-circle"></i> Novo Ano Letivo
            </a>
        </div>
    </div>
</div>

<?= view('admin/partials/alerts') ?>

<!-- ── TABLE ───────────────────────────────────────────── -->
<div class="ci-card">
    <div class="ci-card-header">
        <div class="ci-card-title">
            <i class="fas fa-list"></i> Lista de Anos Letivos
        </div>
        <div class="d-flex gap-2">
            <button type="button" class="hdr-btn success" id="exportExcel">
                <i class="fas fa-file-excel"></i> Excel
            </button>
            <button type="button" class="hdr-btn warning" id="exportPDF">
                <i class="fas fa-file-pdf"></i> PDF
            </button>
            <button type="button" class="hdr-btn primary" id="exportPrint">
                <i class="fas fa-print"></i> Imprimir
            </button>
        </div>
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
                        <td><span class="id-chip"><?= $year['id'] ?></span></td>
                        <td><span class="year-name"><?= esc($year['year_name']) ?></span></td>
                        <td><span class="period-text"><?= date('d/m/Y', strtotime($year['start_date'])) ?></span></td>
                        <td><span class="period-text"><?= date('d/m/Y', strtotime($year['end_date'])) ?></span></td>
                        <td>
                            <?php if ($year['is_active']): ?>
                                <span class="status-dot st-active"><span class="sd sd-active"></span>Ativo</span>
                            <?php else: ?>
                                <span class="status-dot st-inactive"><span class="sd sd-inactive"></span>Inativo</span>
                            <?php endif; ?>
                        </td>
                        <td class="center">
                            <?php if ($year['id'] == current_academic_year()): ?>
                                <span class="current-badge"><i class="fas fa-star" style="font-size:.6rem;"></i>Atual</span>
                            <?php else: ?>
                                <a href="<?= site_url('admin/academic/years/set-current/' . $year['id']) ?>"
                                   class="btn-set-current"
                                   onclick="return confirm('Definir este ano como atual?')">
                                    <i class="fas fa-check-circle"></i> Definir
                                </a>
                            <?php endif; ?>
                        </td>
                        <td class="center">
                            <div class="action-group">
                                <a href="<?= site_url('admin/academic/years/form-edit/' . $year['id']) ?>" 
                                   class="row-btn edit" 
                                   title="Editar">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <a href="<?= site_url('admin/academic/years/view/' . $year['id']) ?>"    
                                   class="row-btn view" 
                                   title="Ver Detalhes">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <?php if ($year['id'] != current_academic_year()): ?>
                                    <a href="<?= site_url('admin/academic/years/delete/' . $year['id']) ?>"
                                       class="row-btn del" 
                                       title="Eliminar"
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
    // Inicializar DataTable com botões de exportação
    var table = $('#yearsTable').DataTable({
        language: { 
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json',
            decimal: ",",
            thousands: "."
        },
        order: [[1, 'desc']],
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
        responsive: true,
        dom: '<"d-flex justify-content-between align-items-center mb-3"<"d-flex gap-3"B><"d-flex"f>>rt<"d-flex justify-content-between align-items-center mt-3"<"d-flex"i><"d-flex"p>>',
        buttons: [
            {
                extend: 'excel',
                text: '<i class="fas fa-file-excel me-1"></i> Excel',
                className: 'hdr-btn success',
                title: 'Anos Letivos',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5],
                    format: {
                        body: function(data, row, column, node) {
                            // Limpar dados para exportação
                            if (column === 4) {
                                // Para a coluna de estado, extrair apenas o texto
                                return $(data).text().trim();
                            }
                            if (column === 5) {
                                // Para a coluna "Atual", extrair apenas o texto
                                return $(data).text().trim();
                            }
                            return data;
                        }
                    }
                }
            },
            {
                extend: 'pdf',
                text: '<i class="fas fa-file-pdf me-1"></i> PDF',
                className: 'hdr-btn warning',
                title: 'Anos Letivos',
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5],
                    format: {
                        body: function(data, row, column, node) {
                            if (column === 4) {
                                return $(data).text().trim();
                            }
                            if (column === 5) {
                                return $(data).text().trim();
                            }
                            return data;
                        }
                    }
                },
                customize: function(doc) {
                    // Customizar o PDF
                    doc.styles.title = {
                        color: '#1B2B4B',
                        fontSize: '18',
                        alignment: 'center'
                    };
                    doc.styles.tableHeader = {
                        fillColor: '#3B7FE8',
                        color: '#FFFFFF',
                        alignment: 'center'
                    };
                }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print me-1"></i> Imprimir',
                className: 'hdr-btn primary',
                title: 'Anos Letivos',
                exportOptions: {
                    columns: [0, 1, 2, 3, 4, 5],
                    format: {
                        body: function(data, row, column, node) {
                            if (column === 4) {
                                return $(data).text().trim();
                            }
                            if (column === 5) {
                                return $(data).text().trim();
                            }
                            return data;
                        }
                    }
                },
                customize: function(win) {
                    // Customizar a impressão
                    $(win.document.body).css('font-family', 'Sora, sans-serif');
                    $(win.document.body).find('h1').css('color', '#1B2B4B');
                }
            }
        ],
        columnDefs: [
            { targets: 6, orderable: false, searchable: false }, // Coluna de ações não ordenável
            { targets: 0, width: '5%' }, // ID
            { targets: 1, width: '25%' }, // Ano Letivo
            { targets: 2, width: '15%' }, // Início
            { targets: 3, width: '15%' }, // Fim
            { targets: 4, width: '10%' }, // Estado
            { targets: 5, width: '10%' }, // Atual
            { targets: 6, width: '20%' }  // Ações
        ],
        initComplete: function() {
            // Aplicar estilos após inicialização
            $('.dataTables_filter input').attr('placeholder', 'Pesquisar...');
            $('.dataTables_length select').addClass('form-select-ci');
        },
        drawCallback: function() {
            // Reaplicar tooltips após redraw
            $('[title]').tooltip();
        }
    });

    // Atalhos para os botões de exportação (caso os botões padrão não funcionem)
    $('#exportExcel').click(function() {
        table.button(0).trigger();
    });

    $('#exportPDF').click(function() {
        table.button(1).trigger();
    });

    $('#exportPrint').click(function() {
        table.button(2).trigger();
    });

    // Adicionar informações de total de registros
    table.on('draw', function() {
        var info = table.page.info();
        $('.dataTables_info').html(
            'Mostrando ' + (info.start + 1) + ' a ' + info.end + 
            ' de ' + info.recordsTotal + ' registros'
        );
    });

    // Filtro personalizado (opcional - se quiser adicionar um filtro global melhorado)
    $('.dataTables_filter input').unbind().bind('input', function(e) {
        if(this.value.length >= 2 || this.value.length === 0) {
            table.search(this.value).draw();
        }
    });

    // Inicializar tooltips
    $('[title]').tooltip();
});

// Garantir que os tooltips funcionem após qualquer atualização AJAX
$(document).ajaxComplete(function() {
    $('[title]').tooltip();
});
</script>

<!-- Estilos adicionais específicos para esta página -->
<style>
/* Ajustes para os botões de exportação */
.hdr-btn.success {
    background: var(--success);
    color: #fff;
    box-shadow: 0 3px 10px rgba(22,168,125,0.25);
}
.hdr-btn.success:hover {
    background: #0E8A64;
    color: #fff;
    transform: translateY(-1px);
}

/* Ajustes para o DataTables */
.dataTables_filter {
    margin-bottom: 0;
}

.dataTables_filter label {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-weight: 500;
    color: var(--text-secondary);
}

.dataTables_filter input {
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 0.45rem 0.8rem;
    font-size: 0.85rem;
    font-family: var(--font);
    color: var(--text-primary);
    background: var(--surface);
    transition: all 0.18s;
    min-width: 250px;
}

.dataTables_filter input:focus {
    outline: none;
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(59,127,232,0.12);
    background: #fff;
}

.dataTables_length {
    margin-right: 1rem;
}

.dataTables_length select {
    border: 1.5px solid var(--border);
    border-radius: var(--radius-sm);
    padding: 0.4rem 1.8rem 0.4rem 0.6rem;
    font-size: 0.85rem;
    font-family: var(--font);
    color: var(--text-primary);
    background: var(--surface) url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' viewBox='0 0 12 12'%3E%3Cpath fill='%236B7A99' d='M6 8L1 3h10z'/%3E%3C/svg%3E") no-repeat right 0.6rem center;
    appearance: none;
    cursor: pointer;
}

.dataTables_length select:focus {
    outline: none;
    border-color: var(--accent);
    box-shadow: 0 0 0 3px rgba(59,127,232,0.12);
    background-color: #fff;
}

.dataTables_info {
    font-size: 0.85rem;
    color: var(--text-secondary);
    padding: 0.5rem 0;
}

.dataTables_paginate {
    display: flex;
    gap: 0.25rem;
}

.dataTables_paginate .paginate_button {
    padding: 0.4rem 0.8rem;
    border-radius: 6px;
    border: 1.5px solid var(--border);
    background: #fff;
    color: var(--text-secondary) !important;
    font-size: 0.85rem;
    cursor: pointer;
    transition: all 0.18s;
    text-decoration: none;
}

.dataTables_paginate .paginate_button:hover {
    background: rgba(59,127,232,0.08);
    border-color: var(--accent);
    color: var(--accent) !important;
}

.dataTables_paginate .paginate_button.current,
.dataTables_paginate .paginate_button.current:hover {
    background: var(--accent);
    border-color: var(--accent);
    color: #fff !important;
}

.dataTables_paginate .paginate_button.disabled,
.dataTables_paginate .paginate_button.disabled:hover {
    opacity: 0.4;
    cursor: not-allowed;
    background: #fff;
    border-color: var(--border);
    color: var(--text-muted) !important;
}

/* Responsividade */
@media (max-width: 768px) {
    .dt-buttons {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }
    
    .dataTables_filter input {
        min-width: 150px;
    }
}
</style>
<?= $this->endSection() ?>