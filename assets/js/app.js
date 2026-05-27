document.addEventListener('DOMContentLoaded', function () {
    const sidebar = document.getElementById('sidebar');
    const overlay = document.getElementById('sidebarOverlay');
    const toggleBtn = document.getElementById('sidebarToggle');
    var deferredPrompt = null;

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
            link.addEventListener('click', function (e) {
                if (window.innerWidth >= 992) return;
                var isCollapse = this.getAttribute('data-bs-toggle') === 'collapse';
                if (!isCollapse) {
                    closeSidebar();
                }
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

    window.addEventListener('beforeinstallprompt', function (e) {
        e.preventDefault();
        deferredPrompt = e;
        var btn = document.getElementById('installAppBtn');
        if (btn) btn.style.display = 'block';
    });

    var installBtn = document.getElementById('installAppBtn');
    if (installBtn) {
        installBtn.addEventListener('click', function () {
            if (deferredPrompt) {
                deferredPrompt.prompt();
                deferredPrompt.userChoice.then(function () {
                    deferredPrompt = null;
                    installBtn.style.display = 'none';
                });
            }
        });
    }

    window.addEventListener('appinstalled', function () {
        var btn = document.getElementById('installAppBtn');
        if (btn) btn.style.display = 'none';
    });
});
