<!-- app/Views/admin/layouts/index.php -->
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= csrf_hash() ?>">
    <title><?= $title ?? 'Dashboard' ?> — Sistema Escolar</title>

    <!-- CSS Local -->
    <link rel="stylesheet" href="<?= base_url('assets/css/vendor/bootstrap.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/vendor/dataTables.bootstrap5.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/vendor/select2.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/vendor/select2-bootstrap-5-theme.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/vendor/toastr.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/vendor/sweetalert2.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/vendor/bootstrap-datepicker.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/vendor/bootstrap-timepicker.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/vendor/fullcalendar.min.css') ?>">
    
    <!-- App CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/css/app.min.css') ?>">

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">


</head>
<body>
<div class="ci-wrapper">

    <!-- Sidebar -->
    <?= view('admin/layouts/sidebar') ?>

    <!-- Mobile overlay -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Content -->
    <div class="ci-content" id="mainContent">

        <!-- Topbar -->
        <?= view('admin/layouts/header') ?>

        <!-- Page Content -->
        <main class="ci-main">
            <?= $this->renderSection('content') ?>
        </main>

        <!-- Footer -->
        <?= view('admin/layouts/footer') ?>
    </div>
</div>

<!-- Loading Spinner -->
<div class="spinner-overlay" id="spinnerOverlay">
    <div class="spinner-border" style="color:var(--accent);width:2.5rem;height:2.5rem;" role="status">
        <span class="visually-hidden">Carregando...</span>
    </div>
</div>

<!-- Scripts Locais -->
<script src="<?= base_url('assets/js/vendor/jquery.min.js') ?>"></script>
<script src="<?= base_url('assets/js/vendor/bootstrap.bundle.min.js') ?>"></script>
<script src="<?= base_url('assets/js/vendor/jquery.dataTables.min.js') ?>"></script>
<script src="<?= base_url('assets/js/vendor/dataTables.bootstrap5.min.js') ?>"></script>
<script src="<?= base_url('assets/js/vendor/jquery.mask.min.js') ?>"></script>
<script src="<?= base_url('assets/js/vendor/select2.min.js') ?>"></script>
<script src="<?= base_url('assets/js/vendor/select2.pt-BR.js') ?>"></script>
<script src="<?= base_url('assets/js/vendor/toastr.min.js') ?>"></script>
<script src="<?= base_url('assets/js/vendor/sweetalert2.min.js') ?>"></script>
<script src="<?= base_url('assets/js/vendor/chart.min.js') ?>"></script>
<script src="<?= base_url('assets/js/vendor/bootstrap-datepicker.min.js') ?>"></script>
<script src="<?= base_url('assets/js/vendor/bootstrap-datepicker.pt-BR.min.js') ?>"></script>
<script src="<?= base_url('assets/js/vendor/bootstrap-timepicker.min.js') ?>"></script>
<script src="<?= base_url('assets/js/vendor/fullcalendar.min.js') ?>"></script>
<script src="<?= base_url('assets/js/vendor/jquery.mCustomScrollbar.concat.min.js') ?>"></script>
<script src="<?= base_url('assets/js/vendor/inputmask.min.js') ?>"></script>
<script src="<?= base_url('assets/js/vendor/jquery.validate.min.js') ?>"></script>
<script src="<?= base_url('assets/js/vendor/messages_pt_BR.min.js') ?>"></script>
<script src="<?= base_url('assets/js/vendor/highcharts.js') ?>"></script>
<script src="<?= base_url('assets/js/vendor/highcharts-exporting.js') ?>"></script>
<script src="<?= base_url('assets/js/vendor/highcharts-accessibility.js') ?>"></script>

<!-- App JS -->
<script src="<?= base_url('assets/js/app.js') ?>"></script>
<script src="<?= base_url('assets/js/app.min.js') ?>"></script>

<?= $this->renderSection('scripts') ?>
</body>
</html>