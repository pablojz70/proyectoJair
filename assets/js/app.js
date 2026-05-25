document.addEventListener('DOMContentLoaded', function () {
    const toggleBtn = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');

    function toggleSidebar() {
        sidebar.classList.toggle('show');
        if (overlay) overlay.classList.toggle('show');
        document.body.classList.toggle('overflow-hidden');
    }

    function closeSidebar() {
        sidebar.classList.remove('show');
        if (overlay) overlay.classList.remove('show');
        document.body.classList.remove('overflow-hidden');
    }

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', toggleSidebar);
        sidebar.querySelectorAll('.nav-link').forEach(function (link) {
            link.addEventListener('click', function () {
                if (window.innerWidth < 992) closeSidebar();
            });
        });
    }

    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }

    var autoAlerts = document.querySelectorAll('.alert-dismissible');
    autoAlerts.forEach(function (alert) {
        setTimeout(function () {
            var bsAlert = new bootstrap.Alert(alert);
            bsAlert.close();
        }, 5000);
    });

    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    tooltipTriggerList.forEach(function (el) {
        return new bootstrap.Tooltip(el);
    });
});
