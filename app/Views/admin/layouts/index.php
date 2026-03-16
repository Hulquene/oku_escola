<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="<?= csrf_hash() ?>">
<meta name="csrf-name" content="<?= csrf_token() ?>">

<title><?= $title ?? 'Dashboard' ?> — Sistema Escolar</title>

<!-- Vendor CSS -->
<link rel="stylesheet" href="<?= base_url('assets/css/vendor/bootstrap.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/vendor/datatables.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/vendor/buttons.dataTables.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/vendor/select2.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/vendor/select2-bootstrap-5-theme.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/vendor/toastr.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/vendor/sweetalert2.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/vendor/bootstrap-datepicker.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/vendor/bootstrap-timepicker.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/vendor/fullcalendar.min.css') ?>">

<!-- App CSS -->
<link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">

<!-- Font Awesome -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

</head>
<body>

<div class="ci-wrapper">

    <!-- Sidebar -->
    <?= view('admin/layouts/sidebar') ?>

    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- CRUD Modal Global -->
    <div class="modal fade" id="crudModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content"></div>
        </div>
    </div>

    <!-- Content -->
    <div class="ci-content" id="mainContent">

        <?= view('admin/layouts/header') ?>

        <main class="ci-main">
            <?= $this->renderSection('content') ?>
        </main>

        <?= view('admin/layouts/footer') ?>

    </div>

</div>

<!-- Loader -->
<div class="spinner-overlay" id="spinnerOverlay">
    <div class="spinner-border text-primary" style="width:2.5rem;height:2.5rem" role="status">
        <span class="visually-hidden">Carregando...</span>
    </div>
</div>

<!-- Vendor JS - Ordem correta -->
<script src="<?= base_url('assets/js/vendor/jquery.min.js') ?>"></script>
<script src="<?= base_url('assets/js/vendor/bootstrap.bundle.min.js') ?>"></script>

<!-- DataTables Core -->
<script src="<?= base_url('assets/js/vendor/datatables.min.js') ?>"></script>

<!-- DataTables Buttons -->
<script src="<?= base_url('assets/js/vendor/dataTables.buttons.min.js') ?>"></script>
<script src="<?= base_url('assets/js/vendor/buttons.html5.min.js') ?>"></script>
<script src="<?= base_url('assets/js/vendor/buttons.print.min.js') ?>"></script>

<!-- PDF/Excel export -->
<script src="<?= base_url('assets/js/vendor/jszip.min.js') ?>"></script>
<script src="<?= base_url('assets/js/vendor/pdfmake.min.js') ?>"></script>
<script src="<?= base_url('assets/js/vendor/vfs_fonts.js') ?>"></script>

<!-- Form Plugins -->
<script src="<?= base_url('assets/js/vendor/jquery.mask.min.js') ?>"></script>
<script src="<?= base_url('assets/js/vendor/inputmask.min.js') ?>"></script>
<script src="<?= base_url('assets/js/vendor/jquery.validate.min.js') ?>"></script>
<script src="<?= base_url('assets/js/vendor/messages_pt_BR.min.js') ?>"></script>

<!-- Select2 -->
<script src="<?= base_url('assets/js/vendor/select2.min.js') ?>"></script>
<script src="<?= base_url('assets/js/vendor/select2.pt-BR.js') ?>"></script>

<!-- Date/Time Pickers -->
<script src="<?= base_url('assets/js/vendor/bootstrap-datepicker.min.js') ?>"></script>
<script src="<?= base_url('assets/js/vendor/bootstrap-datepicker.pt-BR.min.js') ?>"></script>
<script src="<?= base_url('assets/js/vendor/bootstrap-timepicker.min.js') ?>"></script>

<!-- UI Components -->
<script src="<?= base_url('assets/js/vendor/toastr.min.js') ?>"></script>
<script src="<?= base_url('assets/js/vendor/sweetalert2.min.js') ?>"></script>

<!-- Charts -->
<script src="<?= base_url('assets/js/vendor/chart.min.js') ?>"></script>

<!-- Calendar -->
<script src="<?= base_url('assets/js/vendor/fullcalendar.min.js') ?>"></script>

<!-- Configuração CSRF -->
<script>
    window.CONFIG = {
        siteUrl: '<?= base_url() ?>',
        csrfToken: '<?= csrf_hash() ?>',
        csrfName: '<?= csrf_token() ?>'
    };
    
    // Mantido para compatibilidade com código legado
    var base_url = window.CONFIG.siteUrl;
    var csrf_token = window.CONFIG.csrfName;
    var csrf_hash = window.CONFIG.csrfToken;
</script>

<!-- App JS -->
<script src="<?= base_url('assets/js/app.js') ?>"></script>

<!-- DataTables Config -->
<script src="<?= base_url('assets/js/dataTables.config.js') ?>"></script>

<!-- Page Specific Scripts -->
<?= $this->renderSection('scripts') ?>

</body>
</html>