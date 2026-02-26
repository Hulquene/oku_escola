<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Dashboard' ?> - Sistema Escolar</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
   
    <!-- Select2 para selects melhorados -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" rel="stylesheet" />

    <!-- Toastr para notificações -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">

    <!-- SweetAlert2 para modais bonitos -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">

    <!-- DatePicker para calendários -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css">

    <!-- TimePicker para horas -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/css/bootstrap-timepicker.min.css">

    <!-- FullCalendar para agenda escolar -->
    <link href="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.css" rel="stylesheet">

    <!-- Custom Scrollbar -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.min.css">

    <!-- CSS Consolidado -->
    <style>
        :root {
            --primary-color: #4e73df;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
            --sidebar-width: 260px;
        }
        
        body {
            background-color: #f8f9fc;
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            overflow-x: hidden;
        }
        
        .wrapper {
            display: flex;
            width: 100%;
            min-height: 100vh;
        }
        
        /* ========== SIDEBAR ========== */
        .sidebar {
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #1e2b4a 0%, #2c3e6e 100%);
            color: rgba(255, 255, 255, 0.8);
            position: fixed;
            left: 0;
            top: 0;
            height: 100vh;
            overflow-y: auto;
            overflow-x: hidden;
            transition: all 0.3s ease;
            z-index: 1030;
            box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
        }
        
        .sidebar.active {
            margin-left: calc(-1 * var(--sidebar-width));
        }
        
        /* Scrollbar personalizada */
        .sidebar::-webkit-scrollbar {
            width: 3px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: rgba(255, 255, 255, 0.05);
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: rgba(255, 255, 255, 0.15);
            border-radius: 3px;
        }
        
        /* Cabeçalho do Sidebar */
        .sidebar-header {
            padding: 0.75rem 1rem;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
            margin-bottom: 0.5rem;
        }
        
        .sidebar-logo {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            object-fit: contain;
            background: white;
            padding: 5px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        
        .sidebar-logo-placeholder {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.15);
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: rgba(255, 255, 255, 0.8);
            font-size: 1.5rem;
        }
        
        .sidebar-title {
            font-size: 1.1rem;
            font-weight: 600;
            color: white;
            line-height: 1.2;
        }
        
        .sidebar-subtitle {
            font-size: 0.7rem;
            color: rgba(255, 255, 255, 0.6);
            letter-spacing: 0.3px;
        }
        
        /* Navegação */
        .nav-item {
            margin: 1px 8px;
        }
        
        .nav-link {
            display: flex;
            align-items: center;
            padding: 0.4rem 0.8rem;
            color: rgba(255, 255, 255, 0.7);
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.2s ease;
            font-size: 0.85rem;
        }
        
        .nav-link i:first-child {
            width: 20px;
            font-size: 1rem;
            margin-right: 8px;
            color: rgba(255, 255, 255, 0.5);
            transition: all 0.2s ease;
        }
        
        .nav-link span {
            flex: 1;
        }
        
        .nav-link .dropdown-icon {
            width: auto;
            font-size: 0.7rem;
            transition: transform 0.2s ease;
        }
        
        .nav-link[aria-expanded="true"] .dropdown-icon {
            transform: rotate(90deg);
        }
        
        .nav-link:hover,
        .nav-link.active {
            background: rgba(255, 255, 255, 0.15);
            color: white;
        }
        
        .nav-link:hover i:first-child,
        .nav-link.active i:first-child {
            color: white;
        }
        
        /* Submenu */
        .submenu {
            margin-left: 28px;
            margin-top: 2px;
            margin-bottom: 2px;
        }
        
        .submenu a {
            display: flex;
            align-items: center;
            padding: 0.3rem 0.8rem;
            color: rgba(255, 255, 255, 0.6);
            text-decoration: none;
            border-radius: 5px;
            font-size: 0.8rem;
            transition: all 0.2s ease;
        }
        
        .submenu a i {
            width: 18px;
            font-size: 0.8rem;
            margin-right: 6px;
            color: rgba(255, 255, 255, 0.4);
        }
        
        .submenu a:hover,
        .submenu a.active {
            background: rgba(255, 255, 255, 0.08);
            color: white;
        }
        
        /* Footer do Sidebar */
        .sidebar-footer {
            padding: 0.5rem 1rem;
            margin-top: 0.5rem;
            border-top: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .footer-info {
            display: flex;
            align-items: center;
            padding: 0.2rem 0.5rem;
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.75rem;
        }
        
        .footer-info i {
            width: 20px;
            font-size: 0.8rem;
            margin-right: 8px;
        }
        
        .btn-logout {
            display: flex;
            align-items: center;
            padding: 0.3rem 0.8rem;
            background: rgba(220, 53, 69, 0.15);
            color: #ff8a92;
            text-decoration: none;
            border-radius: 6px;
            transition: all 0.2s ease;
            font-size: 0.8rem;
        }
        
        .btn-logout:hover {
            background: #dc3545;
            color: white;
        }
        
        /* ========== CONTENT ========== */
        .content {
            flex: 1;
            margin-left: var(--sidebar-width);
            transition: all 0.3s ease;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        
        .content.active {
            margin-left: 0;
        }
        
        /* ========== HEADER ========== */
        .navbar {
            background: #fff;
            box-shadow: 0 2px 15px rgba(0,0,0,0.08);
            padding: 0.6rem 1.5rem;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            position: sticky;
            top: 0;
            z-index: 1020;
        }
        
        #sidebarCollapse {
            transition: transform 0.2s;
            padding: 0.25rem 0.5rem;
            border-radius: 8px;
        }
        
        #sidebarCollapse:hover {
            transform: scale(1.1);
            background-color: rgba(78, 115, 223, 0.1);
        }
        
        /* Dropdown Animations */
        .dropdown-menu {
            animation: slideIn 0.2s ease-out;
            border: none;
            box-shadow: 0 5px 25px rgba(0,0,0,0.15);
            border-radius: 10px;
        }
        
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        /* Notifications */
        .dropdown-item.bg-light {
            background-color: #f8f9fa !important;
        }
        
        .dropdown-item:active {
            background-color: #4e73df;
            color: white;
        }
        
        /* Quick Actions */
        .btn-success {
            background: linear-gradient(45deg, #1cc88a, #17a673);
            border: none;
            padding: 0.4rem 1rem;
            border-radius: 50px;
        }
        
        .btn-success .fas {
            transition: transform 0.2s;
        }
        
        .btn-success:hover .fas {
            transform: rotate(90deg);
        }
        
        /* Breadcrumb */
        .breadcrumb {
            font-size: 0.9rem;
            background: transparent;
            padding: 0.25rem 0;
        }
        
        .breadcrumb-item a {
            color: #6c757d;
            transition: color 0.2s;
        }
        
        .breadcrumb-item a:hover {
            color: #4e73df;
        }
        
        .breadcrumb-item.active {
            color: #4e73df;
            font-weight: 500;
        }
        
        /* Badge de notificações */
        .badge.bg-danger {
            background: #e74a3b !important;
            font-size: 0.65rem;
            padding: 0.2rem 0.4rem;
        }
        
        /* ========== MAIN CONTENT ========== */
        .main-content {
            flex: 1;
            padding: 30px;
            background-color: #f8f9fc;
        }
        
        /* ========== FOOTER ========== */
        footer {
            background: white;
            padding: 1rem 1.5rem;
            border-top: 1px solid #e3e6f0;
            margin-top: auto;
        }
        
        /* Badges */
        .badge {
            padding: 0.2rem 0.4rem;
            font-size: 0.65rem;
            font-weight: 500;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            margin-left: 5px;
        }
        
        .badge.bg-warning {
            background: #ffc107 !important;
            color: #1e2b4a !important;
        }
        
        .badge.bg-info {
            background: #17a2b8 !important;
        }
        
        /* ========== RESPONSIVE ========== */
        @media (max-width: 768px) {
            .sidebar {
                margin-left: calc(-1 * var(--sidebar-width));
            }
            
            .sidebar.active {
                margin-left: 0;
            }
            
            .content {
                margin-left: 0;
            }
            
            .content.active {
                margin-left: var(--sidebar-width);
            }
            
            .navbar {
                padding: 0.5rem 1rem;
            }
            
            .main-content {
                padding: 20px;
            }
        }
        
        /* Toastr Customização */
        .toast-success { background-color: var(--success-color) !important; }
        .toast-error { background-color: var(--danger-color) !important; }
        .toast-info { background-color: var(--info-color) !important; }
        .toast-warning { background-color: var(--warning-color) !important; }
        
        /* Select2 Customização */
        .select2-container--bootstrap-5 .select2-selection {
            min-height: 38px;
        }
        
        /* Loading spinner */
        .spinner-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.8);
            z-index: 9999;
            display: none;
            justify-content: center;
            align-items: center;
        }
        
        .spinner-overlay.active {
            display: flex;
        }
        
        /* Cards de estatísticas */
        .stat-card {
            background: linear-gradient(45deg, var(--primary-color), #224abe);
            color: #fff;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: relative;
            overflow: hidden;
        }
        
        .stat-card .stat-icon {
            font-size: 3rem;
            opacity: 0.3;
            position: absolute;
            right: 20px;
            top: 20px;
        }
        
        .stat-card.primary { background: linear-gradient(45deg, var(--primary-color), #224abe); }
        .stat-card.success { background: linear-gradient(45deg, var(--success-color), #17a673); }
        .stat-card.info { background: linear-gradient(45deg, var(--info-color), #2c9faf); }
        .stat-card.warning { background: linear-gradient(45deg, var(--warning-color), #dda20a); }
        .stat-card.danger { background: linear-gradient(45deg, var(--danger-color), #be2617); }
        
        /* Animações */
        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateX(-5px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .nav-link, .submenu a {
            animation: slideIn 0.25s ease;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar (vindo do arquivo separado) -->
        <?= view('admin/layouts/sidebar') ?>
        
        <!-- Content -->
        <div class="content">
            <!-- Navbar (vindo do arquivo separado) -->
            <?= view('admin/layouts/header') ?>
            
            <!-- Main Content -->
            <main class="main-content">
                <?= $this->renderSection('content') ?>
            </main>
            
            <!-- Footer (vindo do arquivo separado) -->
            <?= view('admin/layouts/footer') ?>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <!-- jQuery Mask -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>

    <!-- Select2 -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/pt-BR.js"></script>

    <!-- Toastr -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- DatePicker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/locales/bootstrap-datepicker.pt-BR.min.js"></script>

    <!-- TimePicker -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-timepicker/0.5.2/js/bootstrap-timepicker.min.js"></script>

    <!-- FullCalendar -->
    <script src="https://cdn.jsdelivr.net/npm/fullcalendar@6.1.8/index.global.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@fullcalendar/bootstrap5@6.1.8/index.global.min.js"></script>

    <!-- Custom Scrollbar -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/malihu-custom-scrollbar-plugin/3.1.5/jquery.mCustomScrollbar.concat.min.js"></script>

    <!-- InputMask -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/5.0.8/jquery.inputmask.min.js"></script>

    <!-- jQuery Validation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/localization/messages_pt_BR.min.js"></script>

    <!-- Highcharts -->
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    
    <script>
        // Toggle sidebar
        $('#sidebarCollapse').on('click', function() {
            $('.sidebar, .content').toggleClass('active');
            
            // Salvar estado no localStorage
            localStorage.setItem('sidebarCollapsed', $('.sidebar').hasClass('active'));
        });
        
        // Restaurar estado do sidebar
        $(document).ready(function() {
            const collapsed = localStorage.getItem('sidebarCollapsed') === 'true';
            if (collapsed) {
                $('.sidebar, .content').addClass('active');
            }
        });
        
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
        
        // Atualizar contador de notificações via AJAX
        setInterval(function() {
            fetch('<?= site_url('admin/notifications/getUnreadCount') ?>')
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const badge = document.querySelector('.btn-light.position-relative .badge');
                        if (data.count > 0) {
                            if (badge) {
                                badge.textContent = data.count > 9 ? '9+' : data.count;
                            } else {
                                const button = document.querySelector('.btn-light.position-relative');
                                const newBadge = document.createElement('span');
                                newBadge.className = 'position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger';
                                newBadge.textContent = data.count > 9 ? '9+' : data.count;
                                button.appendChild(newBadge);
                            }
                        } else {
                            if (badge) badge.remove();
                        }
                    }
                })
                .catch(error => console.error('Erro ao atualizar notificações:', error));
        }, 60000);
        
        // Fechar dropdowns ao clicar fora
        document.addEventListener('click', function(e) {
            const dropdowns = document.querySelectorAll('.dropdown-menu');
            dropdowns.forEach(dropdown => {
                if (!dropdown.parentElement.contains(e.target)) {
                    const bsDropdown = bootstrap.Dropdown.getInstance(dropdown.parentElement.querySelector('[data-bs-toggle="dropdown"]'));
                    if (bsDropdown) bsDropdown.hide();
                }
            });
        });
    </script>
    
    <?= $this->renderSection('scripts') ?>
</body>
</html>