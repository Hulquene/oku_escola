(() => {
    'use strict';

    /* ============================================================================
       CONFIGURAÇÃO GLOBAL
       ========================================================================= */
    window.CONFIG = {
        siteUrl: window.location.origin,
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.content || '',
        csrfName: document.querySelector('meta[name="csrf-name"]')?.content || 'csrf_token'
    };

    /* ============================================================================
       UTILITÁRIOS
       ========================================================================= */
    const $ = (s, scope = document) => scope.querySelector(s);
    const $$ = (s, scope = document) => [...scope.querySelectorAll(s)];

    /* ============================================================================
       LOADER
       ========================================================================= */
    const loader = {
        show() { $('#spinnerOverlay')?.classList.add('active'); },
        hide() { $('#spinnerOverlay')?.classList.remove('active'); }
    };
    window.loader = loader;

    /* ============================================================================
       SIDEBAR
       ========================================================================= */
    function initSidebar() {
        const sidebar = $('#mainSidebar');
        const content = $('#mainContent');
        const toggle = $('#sidebarCollapse');
        const overlay = $('#sidebarOverlay');

        if (!sidebar || !toggle) return;

        const isMobile = () => window.innerWidth <= 768;

        toggle.addEventListener('click', () => {
            if (isMobile()) {
                sidebar.classList.toggle('mobile-open');
                overlay?.classList.toggle('show');
            } else {
                const collapsed = sidebar.classList.toggle('collapsed');
                content?.classList.toggle('expanded', collapsed);
                try {
                    localStorage.setItem('sidebarCollapsed', collapsed);
                } catch (e) {}
            }
        });

        overlay?.addEventListener('click', () => {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('show');
        });

        // Restaurar estado
        try {
            if (!isMobile() && localStorage.getItem('sidebarCollapsed') === 'true') {
                sidebar.classList.add('collapsed');
                content?.classList.add('expanded');
            }
        } catch (e) {}
    }

    /* ============================================================================
       TOOLTIPS
       ========================================================================= */
    function initTooltips() {
        $$('[data-bs-toggle="tooltip"]').forEach(el => {
            try {
                new bootstrap.Tooltip(el);
            } catch (e) {}
        });
    }

    /* ============================================================================
       SELECT2
       ========================================================================= */
    function initSelect2() {
        if (typeof $?.fn?.select2 !== 'function') return;
        
        $$('.select2').forEach(el => {
            try {
                $(el).select2({
                    theme: 'bootstrap-5',
                    width: '100%',
                    language: 'pt-BR'
                });
            } catch (e) {}
        });
    }

    /* ============================================================================
       DATEPICKER
       ========================================================================= */
    function initDatepickers() {
        if (typeof $?.fn?.datepicker !== 'function') return;

        $$('.datepicker').forEach(el => {
            try {
                $(el).datepicker({
                    format: 'dd/mm/yyyy',
                    language: 'pt-BR',
                    autoclose: true,
                    todayHighlight: true
                });
            } catch (e) {}
        });
    }

    /* ============================================================================
       TIMEPICKER
       ========================================================================= */
    function initTimepickers() {
        if (typeof $?.fn?.timepicker !== 'function') return;

        $$('.timepicker').forEach(el => {
            try {
                $(el).timepicker({
                    showMeridian: false,
                    defaultTime: 'current',
                    minuteStep: 5
                });
            } catch (e) {}
        });
    }

    /* ============================================================================
       INPUT MASKS
       ========================================================================= */
    function initMasks() {
        if (typeof $?.fn?.inputmask !== 'function') return;

        $('.mask-cpf')?.inputmask('999.999.999-99');
        $('.mask-phone')?.inputmask('(99) 99999-9999');
        $('.mask-date')?.inputmask('99/99/9999');
        $('.mask-cep')?.inputmask('99999-999');
        $('.mask-money')?.inputmask('currency', {
            prefix: 'R$ ',
            groupSeparator: '.',
            alias: 'numeric',
            digits: 2,
            digitsOptional: false,
            placeholder: '0,00'
        });
    }

    /* ============================================================================
       ALERTAS AUTOMÁTICOS
       ========================================================================= */
    function initAlerts() {
        setTimeout(() => {
            $$('.alert:not(.alert-permanent)').forEach(el => {
                el.style.transition = 'opacity 0.5s';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 500);
            });
        }, 5000);
    }

    /* ============================================================================
       NOTIFICAÇÕES
       ========================================================================= */
    function initNotificationCounter() {
        const checkNotifications = async () => {
            try {
                const response = await fetch(CONFIG.siteUrl + '/admin/notifications/unread-count', {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const data = await response.json();

                if (!data.success) return;

                const btn = $('.notif-btn');
                let dot = $('.notif-dot');

                if (data.count > 0) {
                    if (!dot) {
                        dot = document.createElement('span');
                        dot.className = 'notif-dot';
                        btn?.appendChild(dot);
                    }
                    dot.textContent = data.count > 9 ? '9+' : data.count;
                } else {
                    dot?.remove();
                }
            } catch (e) {}
        };

        // Checar a cada minuto
        checkNotifications();
        setInterval(checkNotifications, 60000);
    }

    /* ============================================================================
       TOAST
       ========================================================================= */
    window.toast = {
        success: (msg) => toastr?.success(msg),
        error: (msg) => toastr?.error(msg),
        warning: (msg) => toastr?.warning(msg),
        info: (msg) => toastr?.info(msg)
    };

    /* ============================================================================
       SWEETALERT
       ========================================================================= */
    window.swal = {
        confirm: async (opts = {}) => {
            if (typeof Swal === 'undefined') return;

            const result = await Swal.fire({
                icon: opts.icon || 'warning',
                title: opts.title || 'Tem certeza?',
                text: opts.text || 'Esta ação não pode ser desfeita',
                showCancelButton: true,
                confirmButtonColor: opts.confirmColor || '#E84646',
                cancelButtonColor: '#6B7A99',
                confirmButtonText: opts.confirmText || 'Sim, confirmar',
                cancelButtonText: 'Cancelar'
            });

            if (result.isConfirmed && opts.callback) {
                opts.callback();
            }
        }
    };

    /* ============================================================================
       API
       ========================================================================= */
    window.api = {
        async get(url) {
            loader.show();
            try {
                const response = await fetch(url, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': CONFIG.csrfToken
                    }
                });
                return await response.json();
            } catch (error) {
                toast.error('Erro na requisição: ' + error.message);
                throw error;
            } finally {
                loader.hide();
            }
        },

        async post(url, data) {
            loader.show();
            try {
                const response = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': CONFIG.csrfToken
                    },
                    body: JSON.stringify(data)
                });
                return await response.json();
            } catch (error) {
                toast.error('Erro na requisição: ' + error.message);
                throw error;
            } finally {
                loader.hide();
            }
        }
    };

    /* ============================================================================
       CRUD MODAL
       ========================================================================= */
    window.crud = {
        modal: null,

        init() {
            const modalEl = $('#crudModal');
            if (modalEl) {
                try {
                    this.modal = new bootstrap.Modal(modalEl);
                } catch (e) {}
            }
        },

        async open(url) {
            if (!this.modal) return;

            loader.show();
            try {
                const response = await fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                const html = await response.text();

                const modalContent = $('#crudModal .modal-content');
                if (modalContent) {
                    modalContent.innerHTML = html;
                    this.modal.show();

                    // Re-inicializar componentes no modal
                    initSelect2();
                    initDatepickers();
                    initTimepickers();
                    initMasks();
                }
            } catch (error) {
                toast.error('Erro ao abrir formulário');
            } finally {
                loader.hide();
            }
        },

        close() {
            this.modal?.hide();
        }
    };

    /* ============================================================================
       INICIALIZAÇÃO
       ========================================================================= */
    function init() {
        // Componentes principais
        initSidebar();
        initTooltips();
        initSelect2();
        initDatepickers();
        initTimepickers();
        initMasks();
        initAlerts();
        initNotificationCounter();

        // CRUD Modal
        crud.init();

        // Configurar CSRF para requisições AJAX
        if (typeof $?.ajaxSetup === 'function') {
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': CONFIG.csrfToken
                }
            });
        }
    }

    // Aguardar DOM pronto
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }

})();