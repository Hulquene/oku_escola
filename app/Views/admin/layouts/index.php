<!DOCTYPE html>
<html lang="pt">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta name="csrf-token" content="<?= csrf_hash() ?>">
<meta name="csrf-name" content="<?= csrf_token() ?>">

<title><?= $title ?? 'Dashboard' ?> — Sistema Escolar</title>

<!-- 1. CSS VENDOR (Bootstrap primeiro) -->
<link rel="stylesheet" href="<?= base_url('assets/css/vendor/bootstrap.min.css') ?>">

<!-- 2. DATATABLES CORE CSS (OBRIGATÓRIO) -->
<link rel="stylesheet" href="<?= base_url('assets/css/vendor/datatables.min.css') ?>">

<!-- 3. DATATABLES BOOTSTRAP 5 THEME (NOVO - ESSENCIAL PARA O ESTILO) -->
<link rel="stylesheet" href="<?= base_url('assets/css/vendor/dataTables.bootstrap5.min.css') ?>">

<!-- 4. DATATABLES BUTTONS CSS -->
<link rel="stylesheet" href="<?= base_url('assets/css/vendor/buttons.dataTables.min.css') ?>">

<!-- 5. DATATABLES BUTTONS BOOTSTRAP 5 THEME (NOVO - PARA BOTÕES COM ESTILO BS5) -->
<link rel="stylesheet" href="<?= base_url('assets/css/vendor/buttons.bootstrap5.min.css') ?>">

<!-- 6. OUTROS PLUGINS CSS -->
<link rel="stylesheet" href="<?= base_url('assets/css/vendor/select2.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/vendor/select2-bootstrap-5-theme.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/vendor/toastr.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/vendor/sweetalert2.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/vendor/bootstrap-datepicker.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/vendor/bootstrap-timepicker.min.css') ?>">
<link rel="stylesheet" href="<?= base_url('assets/css/vendor/fullcalendar.min.css') ?>">

<!-- 7. FONT AWESOME (CDN) -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

<!-- 8. APP CSS (sempre por último) -->
<link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">

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

<!-- ============================================= -->
<!-- JAVASCRIPT - ORDEM CORRETA                    -->
<!-- ============================================= -->

<!-- 1. JQUERY (sempre primeiro) -->
<script src="<?= base_url('assets/js/vendor/jquery.min.js') ?>"></script>

<!-- 2. BOOTSTRAP (depende do jQuery) -->
<script src="<?= base_url('assets/js/vendor/bootstrap.bundle.min.js') ?>"></script>

<!-- 3. DATATABLES CORE -->
<script src="<?= base_url('assets/js/vendor/datatables.min.js') ?>"></script>

<!-- 4. DATATABLES BOOTSTRAP 5 INTEGRATION (NOVO - ESSENCIAL) -->
<script src="<?= base_url('assets/js/vendor/dataTables.bootstrap5.min.js') ?>"></script>

<!-- 5. DEPENDÊNCIAS PARA EXPORTAÇÃO -->
<script src="<?= base_url('assets/js/vendor/jszip.min.js') ?>"></script>
<script src="<?= base_url('assets/js/vendor/pdfmake.min.js') ?>"></script>
<script src="<?= base_url('assets/js/vendor/vfs_fonts.js') ?>"></script>

<!-- 6. DATATABLES BUTTONS CORE -->
<script src="<?= base_url('assets/js/vendor/dataTables.buttons.min.js') ?>"></script>

<!-- 7. DATATABLES BUTTONS BOOTSTRAP 5 INTEGRATION (NOVO - PARA BOTÕES COM ESTILO BS5) -->
<script src="<?= base_url('assets/js/vendor/buttons.bootstrap5.min.js') ?>"></script>

<!-- 8. DATATABLES BUTTONS EXTENSIONS -->
<script src="<?= base_url('assets/js/vendor/buttons.html5.min.js') ?>"></script>
<script src="<?= base_url('assets/js/vendor/buttons.print.min.js') ?>"></script>
<script src="<?= base_url('assets/js/vendor/buttons.colVis.min.js') ?>"></script>

<!-- 9. FORM PLUGINS -->
<script src="<?= base_url('assets/js/vendor/jquery.mask.min.js') ?>"></script>
<script src="<?= base_url('assets/js/vendor/inputmask.min.js') ?>"></script>
<script src="<?= base_url('assets/js/vendor/jquery.validate.min.js') ?>"></script>
<script src="<?= base_url('assets/js/vendor/messages_pt_BR.min.js') ?>"></script>

<!-- 10. SELECT2 -->
<script src="<?= base_url('assets/js/vendor/select2.min.js') ?>"></script>
<script src="<?= base_url('assets/js/vendor/select2.pt-BR.js') ?>"></script>

<!-- 11. DATE/TIME PICKERS -->
<script src="<?= base_url('assets/js/vendor/bootstrap-datepicker.min.js') ?>"></script>
<script src="<?= base_url('assets/js/vendor/bootstrap-datepicker.pt-BR.min.js') ?>"></script>
<script src="<?= base_url('assets/js/vendor/bootstrap-timepicker.min.js') ?>"></script>

<!-- 12. UI COMPONENTS -->
<script src="<?= base_url('assets/js/vendor/toastr.min.js') ?>"></script>
<script src="<?= base_url('assets/js/vendor/sweetalert2.min.js') ?>"></script>

<!-- 13. CHARTS -->
<script src="<?= base_url('assets/js/vendor/chart.min.js') ?>"></script>

<!-- 14. CALENDAR -->
<script src="<?= base_url('assets/js/vendor/fullcalendar.min.js') ?>"></script>

<!-- 15. CONFIGURAÇÃO CSRF -->
<script>
    window.CONFIG = {
        siteUrl: '<?= base_url() ?>',
        csrfToken: '<?= csrf_hash() ?>',
        csrfName: '<?= csrf_token() ?>',
        debug: <?= ENVIRONMENT === 'development' ? 'true' : 'false' ?>
    };
    
    var base_url = window.CONFIG.siteUrl;
    var csrf_token = window.CONFIG.csrfName;
    var csrf_hash = window.CONFIG.csrfToken;
</script>

<!-- 16. DATATABLES CONFIG -->
<script src="<?= base_url('assets/js/dataTables.config.js') ?>"></script>

<!-- 17. APP JS -->
<script src="<?= base_url('assets/js/app.js') ?>"></script>

<!-- 18. PAGE SPECIFIC SCRIPTS -->
<?= $this->renderSection('scripts') ?>

</body>
</html>