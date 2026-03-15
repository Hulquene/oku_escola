/**
 * DataTables Central Configuration
 * Sistema Escolar - Configuração unificada para todas as DataTables
 * @version 1.0
 */

(function(global, factory) {
    if (typeof define === 'function' && define.amd) {
        define(['jquery'], factory);
    } else if (typeof exports === 'object') {
        module.exports = factory(require('jquery'));
    } else {
        global.DataTableManager = factory(global.jQuery);
    }
}(this, function($) {
    'use strict';

    /**
     * Configurações padrão do DataTables
     */
    const defaultConfig = {
        language: {
            url: '//cdn.datatables.net/plug-ins/1.13.6/i18n/pt-PT.json',
            decimal: ",",
            thousands: ".",
            lengthMenu: "Mostrar _MENU_ registos por página",
            zeroRecords: "Nenhum registro encontrado",
            info: "Mostrando _START_ a _END_ de _TOTAL_ registos",
            infoEmpty: "Mostrando 0 a 0 de 0 registos",
            infoFiltered: "(filtrado de _MAX_ registos totais)",
            search: "Pesquisar:",
            paginate: {
                first: "Primeiro",
                last: "Último",
                next: "Próximo",
                previous: "Anterior"
            }
        },
        
        // Configurações padrão de paginação
        paging: true,
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100, -1], [10, 25, 50, 100, "Todos"]],
        
        // Configurações de busca
        search: {
            caseInsensitive: true,
            smart: true,
            regex: false,
            searchDelay: 500
        },
        
        // Responsividade
        responsive: true,
        autoWidth: false,
        
        // Salvar estado
        stateSave: true,
        stateDuration: 0, // 0 = session storage, -1 = localStorage
        
        // DOM layout padrão
        dom: '<"row"<"col-sm-12 col-md-6"B><"col-sm-12 col-md-6"f>>' +
             '<"row"<"col-sm-12"tr>>' +
             '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>',
        
        // Botões padrão
        buttons: [
            {
                extend: 'excelHtml5',
                text: '<i class="fas fa-file-excel me-1"></i> Excel',
                className: 'btn-action-sm success me-1',
                titleAttr: 'Exportar para Excel',
                exportOptions: {
                    format: {
                        body: function(data, row, column, node) {
                            return DataTableManager.helpers.cleanCellData(data, column, node);
                        }
                    }
                }
            },
            {
                extend: 'pdfHtml5',
                text: '<i class="fas fa-file-pdf me-1"></i> PDF',
                className: 'btn-action-sm warning me-1',
                titleAttr: 'Exportar para PDF',
                orientation: 'landscape',
                pageSize: 'A4',
                exportOptions: {
                    format: {
                        body: function(data, row, column, node) {
                            return DataTableManager.helpers.cleanCellData(data, column, node);
                        }
                    }
                },
                customize: function(doc) {
                    DataTableManager.helpers.customizePDF(doc);
                }
            },
            {
                extend: 'print',
                text: '<i class="fas fa-print me-1"></i> Imprimir',
                className: 'btn-action-sm primary',
                titleAttr: 'Imprimir',
                exportOptions: {
                    format: {
                        body: function(data, row, column, node) {
                            return DataTableManager.helpers.cleanCellData(data, column, node);
                        }
                    }
                },
                customize: function(win) {
                    DataTableManager.helpers.customizePrint(win);
                }
            }
        ]
    };

    /**
     * Helpers e utilitários
     */
    const helpers = {
        /**
         * Limpa dados de célula para exportação
         */
        cleanCellData: function(data, column, node) {
            // Se for HTML, extrair texto
            if (data && data.indexOf('<') !== -1) {
                return $(data).text().trim();
            }
            return data;
        },

        /**
         * Customiza PDF
         */
        customizePDF: function(doc) {
            doc.styles.title = {
                color: '#1B2B4B',
                fontSize: '18',
                alignment: 'center',
                margin: [0, 0, 0, 20]
            };
            doc.styles.tableHeader = {
                fillColor: '#1B2B4B',
                color: '#FFFFFF',
                alignment: 'center',
                fontSize: 10
            };
            doc.styles.tableBodyEven = {
                fontSize: 9
            };
            doc.styles.tableBodyOdd = {
                fontSize: 9
            };
        },

        /**
         * Customiza impressão
         */
        customizePrint: function(win) {
            $(win.document.body).css('font-family', 'Sora, sans-serif');
            $(win.document.body).find('h1').css({
                'color': '#1B2B4B',
                'text-align': 'center',
                'margin-bottom': '20px'
            });
            $(win.document.body).find('table').addClass('ci-table');
            $(win.document.body).find('th').css({
                'background-color': '#1B2B4B',
                'color': '#fff',
                'padding': '10px',
                'text-align': 'left'
            });
            $(win.document.body).find('td').css({
                'padding': '8px',
                'border-bottom': '1px solid #E2E8F4'
            });
        },

        /**
         * Atualiza tooltips após redraw
         */
        refreshTooltips: function() {
            $('[data-bs-toggle="tooltip"]').tooltip('dispose').tooltip();
        },

        /**
         * Formata número
         */
        formatNumber: function(number) {
            return number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        },

        /**
         * Gera filtro personalizado para selects
         */
        createSelectFilter: function(table, columnIndex, selectId) {
            $(selectId).on('change', function() {
                var val = $.fn.dataTable.util.escapeRegex($(this).val());
                if (val) {
                    table.column(columnIndex).search(val, true, false).draw();
                } else {
                    table.column(columnIndex).search('').draw();
                }
            });
        },

        /**
         * Gera filtro de intervalo de datas
         */
        createDateRangeFilter: function(table, columnIndex, startId, endId) {
            $.fn.dataTable.ext.search.push(
                function(settings, data, dataIndex) {
                    var min = $(startId).val();
                    var max = $(endId).val();
                    var date = data[columnIndex] || '';
                    
                    if (min === '' && max === '') return true;
                    if (min === '' && date <= max) return true;
                    if (max === '' && date >= min) return true;
                    if (date >= min && date <= max) return true;
                    
                    return false;
                }
            );
            
            $(startId + ', ' + endId).on('change', function() {
                table.draw();
            });
        }
    };

    /**
     * Fábrica de DataTables
     */
    const factory = {
        /**
         * Cria uma nova DataTable com configurações personalizadas
         */
        create: function(selector, userConfig = {}) {
            const table = $(selector);
            if (!table.length) return null;

            // Mesclar configurações
            const config = $.extend(true, {}, defaultConfig, userConfig);

            // Configurar botões se houver exclusões
            if (userConfig.hideButtons) {
                config.buttons = config.buttons.filter(btn => 
                    !userConfig.hideButtons.includes(btn.extend.replace('Html5', ''))
                );
            }

            // Configurar colunas
            if (userConfig.columnDefs) {
                config.columnDefs = config.columnDefs || [];
                config.columnDefs = config.columnDefs.concat(userConfig.columnDefs);
            }

            // Callbacks personalizados
            const originalInit = config.initComplete;
            const originalDraw = config.drawCallback;

            config.initComplete = function(settings, json) {
                // Placeholder para pesquisa
                $('.dataTables_filter input').attr('placeholder', userConfig.searchPlaceholder || 'Pesquisar...');
                
                // Estilizar selects
                $('.dataTables_length select').addClass('form-select-ci');
                
                // Tooltips
                helpers.refreshTooltips();
                
                // Callback original
                if (originalInit) originalInit.call(this, settings, json);
                if (userConfig.onInit) userConfig.onInit.call(this, settings, json);
            };

            config.drawCallback = function(settings) {
                // Atualizar tooltips
                helpers.refreshTooltips();
                
                // Atualizar info
                var api = this.api();
                var info = api.page.info();
                $('.dataTables_info').html(
                    'Mostrando ' + (info.start + 1) + ' a ' + info.end + 
                    ' de ' + info.recordsTotal + ' registos' +
                    (info.recordsTotal !== info.recordsDisplay ? ' (filtrado de ' + info.recordsDisplay + ')' : '')
                );
                
                // Callback original
                if (originalDraw) originalDraw.call(this, settings);
                if (userConfig.onDraw) userConfig.onDraw.call(this, settings);
            };

            // Inicializar DataTable
            const dt = table.DataTable(config);

            // Adicionar métodos personalizados
            dt.reloadWithFilters = function() {
                dt.ajax.reload(null, false);
            };

            dt.exportVisible = function(format) {
                const buttonIndex = {
                    'excel': 0,
                    'pdf': 1,
                    'print': 2
                };
                if (format in buttonIndex) {
                    dt.button(buttonIndex[format]).trigger();
                }
            };

            dt.clearFilters = function() {
                dt.search('').columns().search('').draw();
            };

            return dt;
        },

        /**
         * Cria uma DataTable simples (sem botões)
         */
        createSimple: function(selector, userConfig = {}) {
            return this.create(selector, $.extend({
                buttons: [],
                dom: '<"row"<"col-sm-12 col-md-6"f><"col-sm-12 col-md-6"p>>' +
                     '<"row"<"col-sm-12"tr>>' +
                     '<"row"<"col-sm-12 col-md-5"i><"col-sm-12 col-md-7"p>>'
            }, userConfig));
        },

        /**
         * Cria uma DataTable com carregamento AJAX
         */
        createAjax: function(selector, ajaxUrl, userConfig = {}) {
            return this.create(selector, $.extend({
                processing: true,
                serverSide: true,
                ajax: {
                    url: ajaxUrl,
                    type: 'POST',
                    data: function(d) {
                        d.csrf_token = $('meta[name="csrf-token"]').attr('content');
                    }
                }
            }, userConfig));
        }
    };

    /**
     * Estilos padrão (injetados automaticamente)
     */
    const styles = `
        /* DataTables Custom Styles */
        .dataTables_filter input {
            min-width: 250px;
            border: 1.5px solid var(--border);
            border-radius: var(--radius-sm);
            padding: 0.45rem 0.8rem;
            font-size: 0.85rem;
            font-family: var(--font);
            color: var(--text-primary);
            background: var(--surface);
            transition: all 0.18s;
        }
        .dataTables_filter input:focus {
            outline: none;
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(59,127,232,0.12);
            background: #fff;
        }
        .dataTables_length select {
            min-width: 80px;
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
            justify-content: flex-end;
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
            margin: 0 2px;
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
        .dt-buttons {
            margin-bottom: 1rem;
            display: flex;
            gap: 0.5rem;
            flex-wrap: wrap;
        }
        @media (max-width: 768px) {
            .dataTables_filter input {
                min-width: 100%;
            }
            .dataTables_paginate {
                flex-wrap: wrap;
                justify-content: center;
            }
            .dt-buttons {
                justify-content: center;
            }
        }
    `;

    // Injetar estilos automaticamente
    if (typeof document !== 'undefined') {
        const styleSheet = document.createElement('style');
        styleSheet.textContent = styles;
        document.head.appendChild(styleSheet);
    }

    // API pública
    return {
        config: defaultConfig,
        helpers: helpers,
        factory: factory,
        create: factory.create,
        createSimple: factory.createSimple,
        createAjax: factory.createAjax,
        version: '1.0.0'
    };
}));