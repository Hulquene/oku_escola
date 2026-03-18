/**
 * CONFIGURAÇÃO GLOBAL DO DATATABLES
 * Sistema de Gestão Escolar
 * 
 * @author Sistema Escolar
 * @version 2.0
 */

(function() {
    'use strict';

    // Verificar se DataTable está disponível
    if (typeof DataTable === 'undefined') {
        console.warn('DataTables não carregado. Verifique os assets.');
        return;
    }

    /* ======================================================
       IDIOMA PORTUGUÊS (PT-BR) - EMBUTIDO
       ====================================================== */
    const ptBrLanguage = {
        "sEmptyTable": "Nenhum registro encontrado",
        "sInfo": "Mostrando de _START_ até _END_ de _TOTAL_ registros",
        "sInfoEmpty": "Mostrando 0 até 0 de 0 registros",
        "sInfoFiltered": "(filtrado de _MAX_ registros no total)",
        "sInfoPostFix": "",
        "sInfoThousands": ".",
        "sLengthMenu": "Mostrar _MENU_ registros por página",
        "sLoadingRecords": "Carregando...",
        "sProcessing": "Processando...",
        "sZeroRecords": "Nenhum registro encontrado",
        "sSearch": "Pesquisar:",
        "oPaginate": {
            "sNext": "Próximo",
            "sPrevious": "Anterior",
            "sFirst": "Primeiro",
            "sLast": "Último"
        },
        "oAria": {
            "sSortAscending": ": Ordenar colunas de forma ascendente",
            "sSortDescending": ": Ordenar colunas de forma descendente"
        },
        "select": {
            "rows": {
                "_": "Selecionado %d linhas",
                "1": "Selecionado 1 linha"
            }
        },
        "buttons": {
            "copy": "Copiar",
            "copyTitle": "Cópia realizada",
            "copySuccess": {
                "_": "%d linhas copiadas para a área de transferência",
                "1": "1 linha copiada para a área de transferência"
            },
            "excel": "Excel",
            "pdf": "PDF",
            "print": "Imprimir",
            "colvis": "Colunas",
            "colvisRestore": "Restaurar padrão"
        }
    };

    /* ======================================================
       CONFIGURAÇÕES PADRÃO
       ====================================================== */
    // Verificar se DataTable.defaults existe
    if (!DataTable.defaults) {
        DataTable.defaults = {};
    }

    // Configurações básicas
    DataTable.defaults.responsive = true;
    DataTable.defaults.pageLength = 25;
    DataTable.defaults.lengthMenu = [10, 25, 50, 100, 250];
    DataTable.defaults.ordering = true;
    DataTable.defaults.searching = true;
    DataTable.defaults.processing = true;
    
    // Idioma (usando versão embutida para não depender de CDN)
    DataTable.defaults.language = ptBrLanguage;
    
    // Layout usando classes Bootstrap (adaptado para DataTables 2)
    DataTable.defaults.layout = {
        topStart: {
            pageLength: {
                menu: [10, 25, 50, 100, 250] // Pode personalizar o menu aqui
            },
            buttons: ['copy', 'excel', 'pdf', 'print']
        },
        topEnd: 'search',
        bottomStart: 'info',
        bottomEnd: 'paging'
    };

    // Botões com classes Bootstrap (se o botões estiver disponível)
    if (DataTable.Buttons) {
        DataTable.defaults.buttons = [
            {
                extend: 'copy',
                text: '<i class="fa-regular fa-copy"></i> Copiar',
                className: 'btn btn-outline-secondary btn-sm',
                titleAttr: 'Copiar para área de transferência'
            },
            {
                extend: 'excel',
                text: '<i class="fa-regular fa-file-excel"></i> Excel',
                className: 'btn btn-success btn-sm',
                titleAttr: 'Exportar para Excel'
            },
            {
                extend: 'pdf',
                text: '<i class="fa-regular fa-file-pdf"></i> PDF',
                className: 'btn btn-danger btn-sm',
                titleAttr: 'Exportar para PDF'
            },
            {
                extend: 'print',
                text: '<i class="fa-regular fa-print"></i> Imprimir',
                className: 'btn btn-info btn-sm',
                titleAttr: 'Imprimir tabela'
            }
        ];
    }

    // Classes CSS personalizadas
    DataTable.defaults.classes = {
        container: 'dt-container',
        layout: 'dt-layout-row',
        search: 'form-control form-control-sm',
        length: 'form-select form-select-sm',
        processing: 'dt-processing'
    };

    // Configurações de responsividade (verificar se o plugin existe)
    if ($.fn.DataTable && $.fn.DataTable.Responsive) {
        DataTable.defaults.responsive = {
            details: {
                display: $.fn.DataTable.Responsive.display.modal({
                    header: function(row) {
                        return 'Detalhes do registro';
                    }
                }),
                renderer: $.fn.DataTable.Responsive.renderer.tableAll({
                    tableClass: 'table table-sm table-borderless'
                })
            }
        };
    }

    // Configurações de colunas
    DataTable.defaults.columnDefs = [
        {
            targets: 'no-sort',
            orderable: false
        },
        {
            targets: 'no-search',
            searchable: false
        },
        {
            targets: 'text-center',
            className: 'text-center'
        }
    ];

    // Callbacks padrão
    DataTable.defaults.drawCallback = function(settings) {
        // Re-inicializar tooltips
        if (typeof bootstrap !== 'undefined' && bootstrap.Tooltip) {
            $('[data-bs-toggle="tooltip"]').each(function() {
                try {
                    const tooltip = bootstrap.Tooltip.getInstance(this);
                    if (tooltip) {
                        tooltip.dispose();
                    }
                    new bootstrap.Tooltip(this);
                } catch (e) {
                    console.warn('Erro ao inicializar tooltip:', e);
                }
            });
        }

        // Re-inicializar popovers
        if (typeof bootstrap !== 'undefined' && bootstrap.Popover) {
            $('[data-bs-toggle="popover"]').each(function() {
                try {
                    const popover = bootstrap.Popover.getInstance(this);
                    if (popover) {
                        popover.dispose();
                    }
                    new bootstrap.Popover(this);
                } catch (e) {}
            });
        }

        // Log de debug (remover em produção)
        if (window.CONFIG?.debug) {
            console.log('DataTables redesenhou', settings);
        }
    };

    // Inicialização
    DataTable.defaults.initComplete = function(settings, json) {
        console.log('DataTables inicializado com sucesso');
    };

    /* ======================================================
       MÉTODOS AUXILIARES
       ====================================================== */

    /**
     * Helper para inicializar DataTables com configurações otimizadas
     * 
     * @param {string|object} selector - Seletor jQuery ou elemento DOM
     * @param {object} options - Opções específicas da tabela
     * @returns {object} Instância do DataTables
     */
    window.initDataTable = function(selector, options = {}) {
        const element = $(selector);
        
        if (!element || !element.length) {
            console.error('Elemento não encontrado:', selector);
            return null;
        }

        if (!$.fn.DataTable) {
            console.error('DataTables não está disponível');
            return null;
        }

        // Configuração base - copiar apenas propriedades existentes
        const config = {};
        
        // Copiar configurações padrão
        for (let key in DataTable.defaults) {
            if (DataTable.defaults.hasOwnProperty(key)) {
                config[key] = DataTable.defaults[key];
            }
        }
        
        // Sobrescrever com opções personalizadas
        for (let key in options) {
            if (options.hasOwnProperty(key)) {
                config[key] = options[key];
            }
        }

        // Se for tabela pequena, desabilitar serverSide
        if (options.serverSide === undefined) {
            // Auto-detectar se deve usar serverSide
            const rowCount = element.find('tbody tr').length;
            config.serverSide = rowCount > 100; // ServerSide para mais de 100 registros
        }

        // Garantir que buttons seja um array
        if (config.buttons && !Array.isArray(config.buttons)) {
            config.buttons = [config.buttons];
        }

        // Inicializar e retornar instância
        try {
            const table = element.DataTable(config);
            
            // Armazenar referência global
            window.dataTables = window.dataTables || {};
            const key = selector.replace(/[^a-zA-Z0-9]/g, '');
            window.dataTables[key] = table;
            
            return table;
        } catch (e) {
            console.error('Erro ao inicializar DataTable:', e);
            return null;
        }
    };

    /**
     * Helper para recarregar todas as tabelas
     */
    window.reloadAllTables = function() {
        if (window.dataTables) {
            Object.values(window.dataTables).forEach(table => {
                if (table && typeof table.ajax?.reload === 'function') {
                    table.ajax.reload(null, false);
                }
            });
        }
    };

    /**
     * Helper para exportar dados em diferentes formatos
     */
    window.exportTableData = function(tableId, format = 'excel') {
        let table;
        
        // Tentar encontrar a tabela
        if (window.dataTables && window.dataTables[tableId]) {
            table = window.dataTables[tableId];
        } else {
            table = $(tableId).DataTable();
        }
        
        if (!table) {
            console.error('Tabela não encontrada:', tableId);
            return;
        }

        // Verificar se os botões estão disponíveis
        if (!table.button) {
            console.error('Botões do DataTables não disponíveis');
            return;
        }

        switch (format) {
            case 'excel':
                table.button('.buttons-excel').trigger();
                break;
            case 'pdf':
                table.button('.buttons-pdf').trigger();
                break;
            case 'print':
                table.button('.buttons-print').trigger();
                break;
            case 'copy':
                table.button('.buttons-copy').trigger();
                break;
            default:
                console.warn('Formato não suportado:', format);
        }
    };

    /* ======================================================
       VERSÃO DO IDIOMA EM FORMATO JSON (para referência)
       ====================================================== */
    window.DT_LANGUAGE_PT_BR = ptBrLanguage;

    // Log de inicialização
    console.log('✅ DataTables configurado com sucesso');
    if (DataTable && DataTable.version) {
        console.log('📊 Versão:', DataTable.version);
    }
    console.log('🌐 Idioma: Português (Brasil)');

})();

// Exportar para módulos (se estiver usando ES6)
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        initDataTable: window.initDataTable,
        language: window.DT_LANGUAGE_PT_BR
    };
}