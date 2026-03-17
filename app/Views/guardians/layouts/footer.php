<!-- Footer -->
<footer class="ci-footer">
    <div class="d-flex align-items-center gap-3">
        <span>&copy; <?= date('Y') ?> Sistema de Gestão Escolar</span>
        <span class="footer-version">v2.0.0</span>
    </div>
    <div>
        <span class="text-muted small">
            <i class="fas fa-clock me-1"></i>
            <?= date('H:i') ?> - <?= date('d/m/Y') ?>
        </span>
    </div>
</footer>

<!-- Spinner Overlay -->
<div class="spinner-overlay" id="spinnerOverlay">
    <div class="spinner-border text-primary" style="width:2.5rem;height:2.5rem" role="status">
        <span class="visually-hidden">Carregando...</span>
    </div>
</div>

<!-- Toggle Sidebar Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggle = document.getElementById('sidebarToggle');
    
    if (toggle) {
        toggle.addEventListener('click', function() {
            sidebar.classList.toggle('mobile-open');
            overlay.classList.toggle('show');
        });
    }
    
    if (overlay) {
        overlay.addEventListener('click', function() {
            sidebar.classList.remove('mobile-open');
            overlay.classList.remove('show');
        });
    }
});
</script>