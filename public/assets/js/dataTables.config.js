/**
 * CONFIGURAÇÃO GLOBAL DO DATATABLES
 * Sistema de Gestão Escolar
 * 
 * Adaptado para usar os mesmos estilos do sistema (.form-input-ci, .btn-ci, etc.)
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
       IDIOMA PORTUGUÊS (PT-BR)
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
    
    // Idioma
    DataTable.defaults.language = ptBrLanguage;

    /* ======================================================
       PERSONALIZAÇÃO COM CLASSES DO SISTEMA
       ====================================================== */
    
    // Classes personalizadas para elementos do DataTables
    DataTable.defaults.classes = {
        // Container principal
        container: 'dt-container',
        
        // Layout rows
        layout: 'dt-layout-row',
        
        // Elementos de filtro e paginação
        search: {
            input: 'form-input-ci', // Usando a classe do seu sistema
            container: 'dt-search'
        },
        length: {
            select: 'form-select-ci', // Usando a classe do seu sistema
            container: 'dt-length'
        },
        processing: 'dt-processing',
        
        // Paginação
        paging: {
            button: 'btn-ci small outline', // Usando seus botões
            container: 'dt-paging',
            active: 'active'
        },
        
        // Informação
        info: 'dt-info'
    };

    /* ======================================================
       CONFIGURAÇÃO DOS BOTÕES COM ESTILO DO SISTEMA
       ====================================================== */
    
    // Botões com as classes do seu sistema (.btn-ci)
    if (DataTable.Buttons) {
        DataTable.defaults.buttons = [
            {
                extend: 'copy',
                text: '<i class="fa-regular fa-copy"></i> Copiar',
                className: 'btn-ci primary small', // Usando btn-ci primary small
                titleAttr: 'Copiar para área de transferência',
                init: function(dt, node, config) {
                    // Forçar a classe btn-ci primary small
                    $(node).removeClass('dt-button buttons-copy').addClass('btn-ci primary small');
                }
            },
            {
                extend: 'excel',
                text: '<i class="fa-regular fa-file-excel"></i> Excel',
                className: 'btn-ci success small', // Usando btn-ci success small
                titleAttr: 'Exportar para Excel',
                init: function(dt, node, config) {
                    $(node).removeClass('dt-button buttons-excel').addClass('btn-ci success small');
                }
            },
            {
                extend: 'pdf',
                text: '<i class="fa-regular fa-file-pdf"></i> PDF',
                className: 'btn-ci warning small', // Usando btn-ci warning small
                titleAttr: 'Exportar para PDF',
                init: function(dt, node, config) {
                    $(node).removeClass('dt-button buttons-pdf').addClass('btn-ci warning small');
                }
            },
            {
                extend: 'print',
                text: '<i class="fa-regular fa-print"></i> Imprimir',
                className: 'btn-ci outline small', // Usando btn-ci outline small
                titleAttr: 'Imprimir tabela',
                init: function(dt, node, config) {
                    $(node).removeClass('dt-button buttons-print').addClass('btn-ci outline small');
                }
            },
            {
                extend: 'colvis',
                text: '<i class="fa-regular fa-columns"></i> Colunas',
                className: 'btn-ci outline small', // Usando btn-ci outline small
                titleAttr: 'Visibilidade das colunas',
                init: function(dt, node, config) {
                    $(node).removeClass('dt-button buttons-colvis').addClass('btn-ci outline small');
                }
            }
        ];
    }

    /* ======================================================
       LAYOUT PERSONALIZADO
       ====================================================== */
    
    // Layout usando os elementos do sistema
    DataTable.defaults.layout = {
        topStart: {
            buttons: ['copy', 'excel', 'pdf', 'print', 'colvis'],
            pageLength: {
                menu: [10, 25, 50, 100, 250],
                text: 'Mostrar _MENU_ por página' // Texto personalizado
            }
        },
        topEnd: 'search',
        bottomStart: 'info',
        bottomEnd: 'paging'
    };

    /* ======================================================
       CONFIGURAÇÕES DE COLUNAS
       ====================================================== */
    
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
        },
        {
            targets: 'text-right',
            className: 'text-right'
        }
    ];

    /* ======================================================
       CALLBACKS PERSONALIZADOS
       ====================================================== */
    
    // Callback após desenho da tabela
    DataTable.defaults.drawCallback = function(settings) {
        // Adicionar classe ci-table à tabela
        $(settings.nTable).addClass('ci-table');
        
        // Re-inicializar tooltips do Bootstrap
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

        // Log de debug
        if (window.CONFIG?.debug) {
            console.log('DataTables redesenhou', settings);
        }
    };

    // Callback de inicialização
    DataTable.defaults.initComplete = function(settings, json) {
        const table = this;
        
        // Adicionar classe ci-table se não tiver
        if (!table.table().node().classList.contains('ci-table')) {
            table.table().node().classList.add('ci-table');
        }
        
        // Ajustar selects de página para usar form-select-ci
        setTimeout(function() {
            $('.dt-length select').addClass('form-select-ci').removeClass('dt-input');
            $('.dt-search input').addClass('form-input-ci').removeClass('dt-input');
        }, 100);
        
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

        // Configuração base
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
            const rowCount = element.find('tbody tr').length;
            config.serverSide = rowCount > 100;
        }

        // Garantir que buttons seja um array
        if (config.buttons && !Array.isArray(config.buttons)) {
            config.buttons = [config.buttons];
        }

        // Inicializar
        try {
            const table = element.DataTable(config);
            
            // Armazenar referência global
            window.dataTables = window.dataTables || {};
            const key = selector.replace(/[^a-zA-Z0-9]/g, '');
            window.dataTables[key] = table;
            
            // Aplicar classes personalizadas
            setTimeout(function() {
                // Aplicar classe aos selects de length
                $('.dt-length select').addClass('form-select-ci');
                
                // Aplicar classe aos inputs de search
                $('.dt-search input').addClass('form-input-ci');
                
                // Aplicar classe aos botões
                $('.dt-buttons .dt-button').each(function() {
                    const $btn = $(this);
                    if ($btn.hasClass('buttons-copy')) {
                        $btn.addClass('btn-ci primary small');
                    } else if ($btn.hasClass('buttons-excel')) {
                        $btn.addClass('btn-ci success small');
                    } else if ($btn.hasClass('buttons-pdf')) {
                        $btn.addClass('btn-ci warning small');
                    } else if ($btn.hasClass('buttons-print')) {
                        $btn.addClass('btn-ci outline small');
                    } else if ($btn.hasClass('buttons-colvis')) {
                        $btn.addClass('btn-ci outline small');
                    }
                    $btn.removeClass('dt-button');
                });
            }, 200);
            
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
     * Helper para exportar dados
     */
    window.exportTableData = function(tableId, format = 'excel') {
        let table;
        
        if (window.dataTables && window.dataTables[tableId]) {
            table = window.dataTables[tableId];
        } else {
            table = $(tableId).DataTable();
        }
        
        if (!table) {
            console.error('Tabela não encontrada:', tableId);
            return;
        }

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

    // Log de inicialização
    console.log('✅ DataTables configurado com sucesso (usando classes do sistema)');
    if (DataTable && DataTable.version) {
        console.log('📊 Versão:', DataTable.version);
    }
    console.log('🌐 Idioma: Português (Brasil)');

})();

// Exportar para módulos
if (typeof module !== 'undefined' && module.exports) {
    module.exports = {
        initDataTable: window.initDataTable,
        language: window.DT_LANGUAGE_PT_BR
    };
}