                    </div>
                    <!-- /Content -->

                    <!-- Footer -->
                    <footer class="content-footer footer bg-footer-theme">
                        <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column text-muted">
                            <div class="mb-2 mb-md-0">
                                © 2026, made with 🧠 for <b><?= SCHOOL_NAME ?></b> by <a href="https://www.instagram.com/atadityas_13/" target="_blank" class="footer-link fw-bolder">A.T. Aditya</a>
                            </div>
                        </div>
                    </footer>
                    <!-- /Footer -->

                    <div class="content-backdrop fade"></div>
                </div>
                <!-- /Content wrapper -->
            </div>
            <!-- /Layout page -->
        </div>

        <!-- Overlay -->
        <div class="layout-overlay layout-menu-toggle"></div>
    </div>

    <!-- Core JS -->
    <script src="https://cdn.jsdelivr.net/npm/jquery@3.6.4/dist/jquery.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- DataTables JS -->
    <script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
    
    <!-- ApexCharts -->
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <script>
        $(document).ready(function() {
            // General DataTable Init
            $('.datatable').DataTable({
                "language": {
                    "url": "//cdn.datatables.net/plug-ins/1.13.6/i18n/id.json"
                }
            });

            // Mobile Menu Toggle
            $('#mobileMenuToggle, .layout-overlay').on('click', function(e) {
                e.preventDefault();
                $('.layout-wrapper').toggleClass('layout-menu-expanded');
            });

            // Close menu on link click (mobile)
            $('.menu-link').on('click', function() {
                if ($(window).width() < 1200) {
                    $('.layout-wrapper').removeClass('layout-menu-expanded');
                }
            });
        });
    </script>
</body>
</html>
