<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'Portal do Encarregado' ?> - Sistema Escolar</title>
    
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- DataTables -->
    <link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
    
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <!-- App CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/css/app.css') ?>">
</head>
<body>
    <div class="ci-wrapper">
        <!-- Sidebar -->
        <?= view('guardians/layouts/sidebar') ?>
        
        <div class="sidebar-overlay" id="sidebarOverlay"></div>
        
        <!-- Content -->
        <div class="ci-content" id="mainContent">
            <?= view('guardians/layouts/header') ?>
            
            <main class="ci-main">
                <?= $this->renderSection('content') ?>
            </main>
            
            <?= view('guardians/layouts/footer') ?>
        </div>
    </div>
    
    <!-- Loader -->
    <div class="spinner-overlay" id="spinnerOverlay">
        <div class="spinner-border text-primary" style="width:2.5rem;height:2.5rem" role="status">
            <span class="visually-hidden">Carregando...</span>
        </div>
    </div>
    
    <!-- Vendor JS -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <!-- App JS -->
    <script src="<?= base_url('assets/js/app.js') ?>"></script>
    
    <!-- Page Specific Scripts -->
    <?= $this->renderSection('scripts') ?>
</body>
</html>