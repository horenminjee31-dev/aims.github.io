            </div><!-- End Main Content Container -->
            
            <!-- Footer -->
            <footer class="footer mt-auto py-3 bg-light">
                <div class="container text-center">
                    <span class="text-muted">&copy; <?php echo date('Y'); ?> <?php echo $settings['school_name'] ?? SITE_NAME; ?>. All rights reserved.</span>
                </div>
            </footer>
        </div><!-- End Page Content -->
    </div><!-- End Wrapper -->
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <!-- Custom JS -->
    <script src="<?php echo SITE_URL; ?>/assets/js/main.js"></script>
    
    <script>
        // Sidebar toggle
        document.addEventListener('DOMContentLoaded', function() {
            const sidebarCollapse = document.getElementById('sidebarCollapse');
            const sidebar = document.getElementById('sidebar');
            const content = document.getElementById('content');
            const overlay = document.getElementById('sidebarOverlay');

            function closeSidebar() {
                if (!sidebar || !content) return;
                sidebar.classList.remove('active', 'open');
                content.classList.remove('sidebar-open');
                if (overlay) overlay.classList.remove('active');
                if (sidebarCollapse) sidebarCollapse.setAttribute('aria-expanded', 'false');
            }

            function toggleSidebar() {
                if (!sidebar || !content) return;
                if (window.innerWidth <= 768) {
                    const isOpen = sidebar.classList.toggle('open');
                    sidebar.classList.toggle('active', isOpen);
                    if (overlay) overlay.classList.toggle('active', isOpen);
                    sidebarCollapse.setAttribute('aria-expanded', String(isOpen));
                } else {
                    const isCollapsed = sidebar.classList.toggle('active');
                    content.classList.toggle('full-width', isCollapsed);
                    sidebarCollapse.setAttribute('aria-expanded', String(!isCollapsed));
                }
            }

            if (sidebarCollapse && sidebar && content) {
                sidebarCollapse.setAttribute('aria-controls', 'sidebar');
                sidebarCollapse.setAttribute('aria-expanded', window.innerWidth <= 768 ? 'false' : 'true');
                sidebarCollapse.addEventListener('click', toggleSidebar);
            }
            if (overlay) overlay.addEventListener('click', closeSidebar);
            
            // Auto-hide alerts after 5 seconds
            const alerts = document.querySelectorAll('.alert-dismissible');
            alerts.forEach(function(alert) {
                setTimeout(function() {
                    const closeBtn = alert.querySelector('.btn-close');
                    if (closeBtn) {
                        closeBtn.click();
                    }
                }, 5000);
            });
        });
    </script>
</body>
</html>
