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


    <!-- Custom CSS -->
    <style>
        :root {
            --primary-color: #4e73df;
            --success-color: #1cc88a;
            --info-color: #36b9cc;
            --warning-color: #f6c23e;
            --danger-color: #e74a3b;
        }
        
        body {
            background-color: #f8f9fc;
            font-family: 'Nunito', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }
        
        .wrapper {
            display: flex;
            width: 100%;
            align-items: stretch;
        }
        
        /* Sidebar */
        .sidebar {
            min-width: 250px;
            max-width: 250px;
            background: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
            color: #fff;
            transition: all 0.3s;
            position: fixed;
            height: 100vh;
            overflow-y: auto;
            z-index: 1000;
        }
        
        .sidebar.active {
            margin-left: -250px;
        }
        
        .sidebar .sidebar-header {
            padding: 20px;
            background: rgba(0, 0, 0, 0.1);
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }
        
        .sidebar .sidebar-header h3 {
            color: #fff;
            font-size: 1.5rem;
            margin: 0;
        }
        
        .sidebar .sidebar-header p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 0.8rem;
            margin: 5px 0 0;
        }
        
        .sidebar ul.components {
            padding: 20px 0;
        }
        
        .sidebar ul li a {
            padding: 12px 20px;
            display: block;
            color: rgba(255, 255, 255, 0.8);
            text-decoration: none;
            transition: all 0.3s;
            border-left: 3px solid transparent;
        }
        
        .sidebar ul li a:hover,
        .sidebar ul li a.active {
            background: rgba(255, 255, 255, 0.1);
            color: #fff;
            border-left-color: #fff;
        }
        
        .sidebar ul li a i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        .sidebar ul li ul {
            padding-left: 30px;
            background: rgba(0, 0, 0, 0.1);
        }
        
        /* Content */
        .content {
            width: 100%;
            margin-left: 250px;
            transition: all 0.3s;
            min-height: 100vh;
        }
        
        .content.active {
            margin-left: 0;
        }
        
        /* Navbar */
        .navbar {
            background: #fff;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            padding: 15px 30px;
        }
        
        .navbar .btn-toggle {
            background: transparent;
            border: none;
            color: #4e73df;
            font-size: 1.2rem;
            cursor: pointer;
        }
        
        .navbar .user-info {
            display: flex;
            align-items: center;
        }
        
        .navbar .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            object-fit: cover;
        }
        
        .navbar .user-info .user-name {
            font-weight: 600;
            color: #333;
        }
        
        .navbar .user-info .user-role {
            font-size: 0.8rem;
            color: #666;
        }
        
        /* Main Content */
        .main-content {
            padding: 30px;
        }
        
        .page-header {
            margin-bottom: 30px;
        }
        
        .page-header h1 {
            font-size: 1.8rem;
            font-weight: 300;
            color: #333;
            margin: 0;
        }
        
        .page-header .breadcrumb {
            background: transparent;
            padding: 0;
            margin: 10px 0 0;
        }
        
        /* Cards */
        .card {
            border: none;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }
        
        .card-header {
            background: #fff;
            border-bottom: 1px solid #e3e6f0;
            padding: 15px 20px;
            font-weight: 600;
            color: #4e73df;
        }
        
        .card-header i {
            margin-right: 10px;
        }
        
        .stat-card {
            background: linear-gradient(45deg, var(--primary-color), #224abe);
            color: #fff;
            border-radius: 10px;
            padding: 25px;
            margin-bottom: 30px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .stat-card .stat-icon {
            font-size: 3rem;
            opacity: 0.3;
            position: absolute;
            right: 20px;
            top: 20px;
        }
        
        .stat-card .stat-label {
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
            opacity: 0.8;
            margin-bottom: 10px;
        }
        
        .stat-card .stat-value {
            font-size: 2rem;
            font-weight: 700;
        }
        
        .stat-card .stat-change {
            font-size: 0.8rem;
            opacity: 0.8;
            margin-top: 10px;
        }
        
        /* Responsive */
        @media (max-width: 768px) {
            .sidebar {
                margin-left: -250px;
            }
            .sidebar.active {
                margin-left: 0;
            }
            .content {
                margin-left: 0;
            }
            .content.active {
                margin-left: 250px;
            }
        }
        /* Estilos Adicionais para Funcionalidades */

        /* Select2 Customização */
        .select2-container--bootstrap-5 .select2-selection {
            min-height: 38px;
        }

        /* Toastr Customização */
        .toast-success {
            background-color: var(--success-color) !important;
        }
        .toast-error {
            background-color: var(--danger-color) !important;
        }
        .toast-info {
            background-color: var(--info-color) !important;
        }
        .toast-warning {
            background-color: var(--warning-color) !important;
        }

        /* Estilos para tabelas responsivas */
        .table-responsive {
            border-radius: 10px;
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

        /* Cards de estatísticas com cores diferentes */
        .stat-card.primary { background: linear-gradient(45deg, var(--primary-color), #224abe); }
        .stat-card.success { background: linear-gradient(45deg, var(--success-color), #17a673); }
        .stat-card.info { background: linear-gradient(45deg, var(--info-color), #2c9faf); }
        .stat-card.warning { background: linear-gradient(45deg, var(--warning-color), #dda20a); }
        .stat-card.danger { background: linear-gradient(45deg, var(--danger-color), #be2617); }

        /* Animações */
        .fade-in {
            animation: fadeIn 0.5s;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        /* Formulários */
        .form-group {
            margin-bottom: 1rem;
        }

        /* Badges personalizados */
        .badge {
            padding: 0.5em 0.75em;
            font-weight: 500;
        }

        /* Progress bars personalizados */
        .progress {
            height: 10px;
            border-radius: 5px;
        }

        /* Timeline para atividades */
        .timeline {
            position: relative;
            padding: 20px 0;
        }

        .timeline-item {
            position: relative;
            padding-left: 40px;
            margin-bottom: 20px;
        }

        .timeline-item:before {
            content: '';
            position: absolute;
            left: 15px;
            top: 0;
            bottom: -20px;
            width: 2px;
            background: #e3e6f0;
        }

        .timeline-item:last-child:before {
            display: none;
        }

        .timeline-badge {
            position: absolute;
            left: 0;
            width: 30px;
            height: 30px;
            border-radius: 50%;
            background: var(--primary-color);
            color: #fff;
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 1;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <!-- Sidebar -->
        <?= view('admin/layouts/sidebar') ?>
        
        <!-- Content -->
        <div class="content">
            <!-- Navbar -->
            <?= view('admin/layouts/header') ?>
            
            <!-- Main Content -->
            <main class="main-content">
                <?= $this->renderSection('content') ?>
            </main>
            
            <!-- Footer -->
            <?= view('admin/layouts/footer') ?>
        </div>
    </div>
    
    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>

    <!-- jQuery Mask para máscaras de input -->
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

    <!-- InputMask para telefone/CPF -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/inputmask/5.0.8/jquery.inputmask.min.js"></script>

    <!-- jQuery Validation para formulários -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/jquery.validate.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery-validate/1.19.5/localization/messages_pt_BR.min.js"></script>

    <!-- Highcharts para gráficos avançados -->
    <script src="https://code.highcharts.com/highcharts.js"></script>
    <script src="https://code.highcharts.com/modules/exporting.js"></script>
    <script src="https://code.highcharts.com/modules/accessibility.js"></script>
    
    <script>
        // Toggle sidebar
        $('#sidebarCollapse').on('click', function() {
            $('.sidebar, .content').toggleClass('active');
        });
        
        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            $('.alert').fadeOut('slow');
        }, 5000);
    </script>
    
    <?= $this->renderSection('scripts') ?>
</body>
</html>