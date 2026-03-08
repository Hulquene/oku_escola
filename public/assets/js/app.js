/* assets/js/app.js */

(function() {
    'use strict';

    // ============================================================================
    // VARIÁVEIS GLOBAIS E CONFIGURAÇÕES
    // ============================================================================
    const CONFIG = {
        siteUrl: window.location.origin,
        csrfToken: document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
        toastrOptions: {
            closeButton: true,
            progressBar: true,
            positionClass: 'toast-top-right',
            timeOut: 5000,
            extendedTimeOut: 2000,
            preventDuplicates: true
        },
        swalOptions: {
            confirmButtonColor: '#3B7FE8',
            cancelButtonColor: '#E84646',
            confirmButtonText: 'Sim',
            cancelButtonText: 'Cancelar',
            reverseButtons: true
        },
        datepickerOptions: {
            format: 'dd/mm/yyyy',
            language: 'pt-BR',
            autoclose: true,
            todayHighlight: true,
            orientation: 'bottom'
        },
        timepickerOptions: {
            showMeridian: false,
            defaultTime: '07:00',
            minuteStep: 5,
            showInputs: true,
            disableFocus: true
        }
    };

    // ============================================================================
    // INICIALIZAÇÃO GLOBAL
    // ============================================================================
    function initApp() {
        initSidebar();
        initTooltips();
        initSelect2();
        initDatepickers();
        initTimepickers();
        initMasks();
        initFormValidation();
        initAutoHideAlerts();
        initNotificationCounter();
        initDataTables();
    }

    // ============================================================================
    // SIDEBAR
    // ============================================================================
    function initSidebar() {
        const sidebar = document.getElementById('mainSidebar');
        const content = document.getElementById('mainContent');
        const overlay = document.getElementById('sidebarOverlay');
        const toggleBtn = document.getElementById('sidebarCollapse');

        if (!sidebar || !content || !toggleBtn) return;

        const isMobile = () => window.innerWidth <= 768;

        // Restore desktop state
        if (!isMobile() && localStorage.getItem('sidebarCollapsed') === 'true') {
            sidebar.classList.add('collapsed');
            content.classList.add('expanded');
        }

        toggleBtn.addEventListener('click', function() {
            if (isMobile()) {
                sidebar.classList.toggle('mobile-open');
                if (overlay) overlay.classList.toggle('show');
            } else {
                const collapsed = sidebar.classList.toggle('collapsed');
                content.classList.toggle('expanded', collapsed);
                localStorage.setItem('sidebarCollapsed', collapsed);
            }
        });

        if (overlay) {
            overlay.addEventListener('click', function() {
                sidebar.classList.remove('mobile-open');
                overlay.classList.remove('show');
            });
        }

        window.addEventListener('resize', function() {
            if (!isMobile()) {
                sidebar.classList.remove('mobile-open');
                if (overlay) overlay.classList.remove('show');
            }
        });
    }

    // ============================================================================
    // TOOLTIPS
    // ============================================================================
    function initTooltips() {
        const tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        tooltipTriggerList.map(function(tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }

    // ============================================================================
    // SELECT2
    // ============================================================================
    function initSelect2() {
        if (typeof $.fn.select2 === 'undefined') return;

        $('.select2').select2({
            theme: 'bootstrap-5',
            language: 'pt-BR',
            width: '100%',
            dropdownParent: $('body')
        });

        $('.select2-tags').select2({
            theme: 'bootstrap-5',
            language: 'pt-BR',
            tags: true,
            width: '100%'
        });
    }

    // ============================================================================
    // DATEPICKERS
    // ============================================================================
    function initDatepickers() {
        if (typeof $.fn.datepicker === 'undefined') return;

        $('.datepicker').datepicker(CONFIG.datepickerOptions);

        $('.datepicker-month').datepicker({
            format: 'mm/yyyy',
            language: 'pt-BR',
            autoclose: true,
            minViewMode: 1,
            todayHighlight: true
        });

        $('.datepicker-year').datepicker({
            format: 'yyyy',
            language: 'pt-BR',
            autoclose: true,
            minViewMode: 2,
            todayHighlight: true
        });
    }

    // ============================================================================
    // TIMEPICKERS
    // ============================================================================
    function initTimepickers() {
        if (typeof $.fn.timepicker === 'undefined') return;
        $('.timepicker').timepicker(CONFIG.timepickerOptions);
    }

    // ============================================================================
    // MASKS
    // ============================================================================
    function initMasks() {
        if (typeof $.fn.mask === 'undefined' && typeof $.fn.inputmask === 'undefined') return;

        if ($.fn.mask) {
            // jQuery Mask
            $('.mask-cpf').mask('000.000.000-00');
            $('.mask-cnpj').mask('00.000.000/0000-00');
            $('.mask-phone').mask('(00) 00000-0000');
            $('.mask-phone-fixed').mask('(00) 0000-0000');
            $('.mask-cep').mask('00000-000');
            $('.mask-date').mask('00/00/0000');
            $('.mask-time').mask('00:00');
            $('.mask-money').mask('#.##0,00', { reverse: true });
            $('.mask-percent').mask('##0,00%', { reverse: true });
        } else if ($.fn.inputmask) {
            // Inputmask
            $('.mask-cpf').inputmask('999.999.999-99');
            $('.mask-cnpj').inputmask('99.999.999/9999-99');
            $('.mask-phone').inputmask('(99) 99999-9999');
            $('.mask-phone-fixed').inputmask('(99) 9999-9999');
            $('.mask-cep').inputmask('99999-999');
            $('.mask-date').inputmask('99/99/9999');
            $('.mask-time').inputmask('99:99');
            $('.mask-money').inputmask('currency', {
                prefix: 'R$ ',
                groupSeparator: '.',
                radixPoint: ',',
                digits: 2,
                autoGroup: true
            });
        }
    }

    // ============================================================================
    // FORM VALIDATION
    // ============================================================================
    function initFormValidation() {
        if (typeof $.validator === 'undefined') return;

        $.validator.setDefaults({
            errorClass: 'is-invalid',
            validClass: 'is-valid',
            errorElement: 'div',
            errorPlacement: function(error, element) {
                error.addClass('invalid-feedback');
                element.closest('.form-group, .mb-3, .col').append(error);
            },
            highlight: function(element) {
                $(element).addClass('is-invalid').removeClass('is-valid');
            },
            unhighlight: function(element) {
                $(element).removeClass('is-invalid').addClass('is-valid');
            }
        });

        // Portuguese messages
        if ($.validator) {
            $.extend($.validator.messages, {
                required: "Este campo é obrigatório.",
                remote: "Por favor, corrija este campo.",
                email: "Por favor, insira um endereço de email válido.",
                url: "Por favor, insira uma URL válida.",
                date: "Por favor, insira uma data válida.",
                dateISO: "Por favor, insira uma data válida (ISO).",
                number: "Por favor, insira um número válido.",
                digits: "Por favor, insira apenas dígitos.",
                creditcard: "Por favor, insira um número de cartão de crédito válido.",
                equalTo: "Por favor, insira o mesmo valor novamente.",
                accept: "Por favor, insira um arquivo com uma extensão válida.",
                maxlength: $.validator.format("Por favor, não insira mais de {0} caracteres."),
                minlength: $.validator.format("Por favor, insira pelo menos {0} caracteres."),
                rangelength: $.validator.format("Por favor, insira entre {0} e {1} caracteres."),
                range: $.validator.format("Por favor, insira um valor entre {0} e {1}."),
                max: $.validator.format("Por favor, insira um valor menor ou igual a {0}."),
                min: $.validator.format("Por favor, insira um valor maior ou igual a {0}.")
            });
        }
    }

    // ============================================================================
    // AUTO-HIDE ALERTS
    // ============================================================================
    function initAutoHideAlerts() {
        setTimeout(() => {
            document.querySelectorAll('.alert:not(.alert-permanent)').forEach(el => {
                el.style.transition = 'opacity .5s';
                el.style.opacity = '0';
                setTimeout(() => el.remove(), 500);
            });
        }, 5000);
    }

    // ============================================================================
    // NOTIFICATION COUNTER
    // ============================================================================
    function initNotificationCounter() {
        setInterval(function() {
            const url = CONFIG.siteUrl + '/admin/notifications/getUnreadCount';
            fetch(url)
                .then(r => r.json())
                .then(data => {
                    if (!data.success) return;
                    const dot = document.querySelector('.notif-dot');
                    const btn = document.querySelector('.notif-btn');
                    
                    if (data.count > 0) {
                        if (dot) {
                            dot.textContent = data.count > 9 ? '9+' : data.count;
                        } else {
                            const d = document.createElement('span');
                            d.className = 'notif-dot';
                            d.textContent = data.count > 9 ? '9+' : data.count;
                            btn.appendChild(d);
                        }
                    } else {
                        if (dot) dot.remove();
                    }
                })
                .catch(() => {});
        }, 60000);
    }

    // ============================================================================
    // DATATABLES
    // ============================================================================
    function initDataTables() {
        if (typeof $.fn.DataTable === 'undefined') return;

        $.extend(true, $.fn.DataTable.defaults, {
            language: {
                url: CONFIG.siteUrl + '/assets/js/vendor/dataTables.pt-BR.json'
            },
            pageLength: 25,
            lengthMenu: [10, 25, 50, 100],
            responsive: true,
            processing: true,
            serverSide: false,
            stateSave: true,
            stateDuration: 0,
            dom: "<'row'<'col-sm-12 col-md-6'l><'col-sm-12 col-md-6'f>>" +
                 "<'row'<'col-sm-12'tr>>" +
                 "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>"
        });
    }

    // ============================================================================
    // TOASTR
    // ============================================================================
    window.toast = {
        success: function(message, title) {
            toastr.success(message, title || 'Sucesso!', CONFIG.toastrOptions);
        },
        error: function(message, title) {
            toastr.error(message, title || 'Erro!', CONFIG.toastrOptions);
        },
        warning: function(message, title) {
            toastr.warning(message, title || 'Aviso!', CONFIG.toastrOptions);
        },
        info: function(message, title) {
            toastr.info(message, title || 'Informação', CONFIG.toastrOptions);
        }
    };

    // ============================================================================
    // SWEETALERT2
    // ============================================================================
    window.swal = {
        confirm: function(options) {
            return Swal.fire({
                icon: 'warning',
                title: options.title || 'Tem certeza?',
                text: options.text || 'Esta ação não poderá ser desfeita!',
                showCancelButton: true,
                confirmButtonColor: CONFIG.swalOptions.confirmButtonColor,
                cancelButtonColor: CONFIG.swalOptions.cancelButtonColor,
                confirmButtonText: options.confirmText || 'Sim, confirmar!',
                cancelButtonText: 'Cancelar',
                reverseButtons: CONFIG.swalOptions.reverseButtons
            }).then((result) => {
                if (result.isConfirmed && options.callback) {
                    options.callback();
                }
                return result;
            });
        },
        success: function(message) {
            return Swal.fire({
                icon: 'success',
                title: 'Sucesso!',
                text: message || 'Operação realizada com sucesso!',
                confirmButtonColor: CONFIG.swalOptions.confirmButtonColor
            });
        },
        error: function(message) {
            return Swal.fire({
                icon: 'error',
                title: 'Erro!',
                text: message || 'Ocorreu um erro ao processar a requisição.',
                confirmButtonColor: CONFIG.swalOptions.confirmButtonColor
            });
        },
        warning: function(message) {
            return Swal.fire({
                icon: 'warning',
                title: 'Atenção!',
                text: message || 'Verifique os dados antes de prosseguir.',
                confirmButtonColor: CONFIG.swalOptions.confirmButtonColor
            });
        },
        info: function(message) {
            return Swal.fire({
                icon: 'info',
                title: 'Informação',
                text: message || '',
                confirmButtonColor: CONFIG.swalOptions.confirmButtonColor
            });
        }
    };

    // ============================================================================
    // AJAX SETUP
    // ============================================================================
    function initAjaxSetup() {
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': CONFIG.csrfToken,
                'X-Requested-With': 'XMLHttpRequest'
            },
            beforeSend: function() {
                $('#spinnerOverlay').addClass('active');
            },
            complete: function() {
                $('#spinnerOverlay').removeClass('active');
            },
            error: function(xhr, status, error) {
                if (xhr.status === 403) {
                    window.toast.error('Sessão expirada. Recarregue a página.');
                    setTimeout(() => location.reload(), 2000);
                } else if (xhr.status === 500) {
                    window.toast.error('Erro interno do servidor.');
                } else {
                    window.toast.error('Erro na requisição: ' + error);
                }
                console.error('AJAX Error:', xhr.responseText);
            }
        });
    }

    // ============================================================================
    // LOADER UTILITY
    // ============================================================================
    window.loader = {
        show: function() {
            $('#spinnerOverlay').addClass('active');
        },
        hide: function() {
            $('#spinnerOverlay').removeClass('active');
        },
        with: async function(callback) {
            try {
                this.show();
                return await callback();
            } finally {
                this.hide();
            }
        }
    };

    // ============================================================================
    // INICIALIZAÇÃO
    // ============================================================================
    $(document).ready(function() {
        initApp();
        initAjaxSetup();
        
        // Re-initialize components after AJAX content load
        $(document).ajaxComplete(function() {
            initTooltips();
            initSelect2();
            initDatepickers();
            initTimepickers();
            initMasks();
        });
    });

})();