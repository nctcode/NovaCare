    </div><!-- /page-content -->
</div><!-- /main-content -->

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>
<script src="assets/js/dashboard.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {

    // ── Auto dismiss alerts after 5 seconds ──
    setTimeout(() => {
        document.querySelectorAll('.alert-dismissible').forEach(alert => {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            bsAlert.close();
        });
    }, 5000);

    // ── Mobile sidebar toggle ──
    const sidebar   = document.getElementById('sidebar');
    const overlay   = document.getElementById('sidebarOverlay');
    const hamburger = document.getElementById('btnHamburger');

    function openSidebar() {
        sidebar.classList.add('open');
        overlay.classList.add('active');
        hamburger.classList.add('open');
        hamburger.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
    }

    function closeSidebar() {
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
        hamburger.classList.remove('open');
        hamburger.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
    }

    if (hamburger) hamburger.addEventListener('click', () =>
        sidebar.classList.contains('open') ? closeSidebar() : openSidebar()
    );

    if (overlay) overlay.addEventListener('click', closeSidebar);

    window.addEventListener('resize', () => { if (window.innerWidth > 768) closeSidebar(); });

    // ── Dark Mode Toggle ──
    const btnDark    = document.getElementById('btnDarkMode');
    const darkIcon   = document.getElementById('darkModeIcon');
    const root       = document.documentElement;

    function applyTheme(theme) {
        root.setAttribute('data-theme', theme);
        localStorage.setItem('nc-theme', theme);
        if (darkIcon) {
            darkIcon.className = theme === 'dark' ? 'fa-solid fa-sun' : 'fa-solid fa-moon';
        }
        // Refresh Chart.js charts for dark mode colours
        if (window._ncCharts) {
            window._ncCharts.forEach(c => {
                if (!c) return;
                const isDark = theme === 'dark';
                c.options.plugins.legend.labels.color = isDark ? '#94a3b8' : '#64748b';
                if (c.options.scales) {
                    ['x','y'].forEach(ax => {
                        if (c.options.scales[ax]) {
                            c.options.scales[ax].ticks = c.options.scales[ax].ticks || {};
                            c.options.scales[ax].ticks.color = isDark ? '#94a3b8' : '#64748b';
                            if (c.options.scales[ax].grid) {
                                c.options.scales[ax].grid.color = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.04)';
                            }
                        }
                    });
                }
                c.update();
            });
        }
    }

    // Apply on load (in case refreshed while dark)
    const savedTheme = localStorage.getItem('nc-theme') || 'light';
    applyTheme(savedTheme);

    if (btnDark) {
        btnDark.addEventListener('click', () => {
            const current = root.getAttribute('data-theme') || 'light';
            applyTheme(current === 'dark' ? 'light' : 'dark');
        });
    }

    // ── Notification Panel Toggle ──
    const btnNotif   = document.getElementById('btnNotif');
    const notifPanel = document.getElementById('notifPanel');

    if (btnNotif && notifPanel) {
        btnNotif.addEventListener('click', (e) => {
            e.stopPropagation();
            notifPanel.classList.toggle('open');
        });

        document.addEventListener('click', (e) => {
            if (!notifPanel.contains(e.target) && e.target !== btnNotif) {
                notifPanel.classList.remove('open');
            }
        });
    }

    // ── Table live search ──
    document.querySelectorAll('.table-search-js').forEach(input => {
        input.addEventListener('input', function () {
            const query   = this.value.toLowerCase().trim();
            const tableId = this.dataset.table;
            const table   = document.getElementById(tableId);
            if (!table) return;
            table.querySelectorAll('tbody tr').forEach(row => {
                row.style.display = row.textContent.toLowerCase().includes(query) ? '' : 'none';
            });
        });
    });

    // ── Advanced Appointment Filter ──
    const filterStatus = document.getElementById('filterStatus');
    const filterFrom   = document.getElementById('filterFrom');
    const filterTo     = document.getElementById('filterTo');
    const filterClear  = document.getElementById('filterClear');
    const filterTable  = document.getElementById('appointmentsTable');

    function applyFilter() {
        if (!filterTable) return;
        const status = filterStatus ? filterStatus.value.toLowerCase() : '';
        const from   = filterFrom  ? filterFrom.value  : '';
        const to     = filterTo    ? filterTo.value    : '';

        filterTable.querySelectorAll('tbody tr').forEach(row => {
            const rowStatus = (row.dataset.status || '').toLowerCase();
            const rowDate   = row.dataset.date || '';

            let show = true;
            if (status && rowStatus !== status) show = false;
            if (from   && rowDate < from)        show = false;
            if (to     && rowDate > to)           show = false;

            row.style.display = show ? '' : 'none';
        });
    }

    if (filterStatus) filterStatus.addEventListener('change', applyFilter);
    if (filterFrom)   filterFrom.addEventListener('change', applyFilter);
    if (filterTo)     filterTo.addEventListener('change', applyFilter);
    if (filterClear) {
        filterClear.addEventListener('click', () => {
            if (filterStatus) filterStatus.value = '';
            if (filterFrom)   filterFrom.value   = '';
            if (filterTo)     filterTo.value     = '';
            applyFilter();
        });
    }

    // ── Button loading state on form submit ──
    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function () {
            const btn = form.querySelector('[type="submit"]');
            if (btn) {
                btn.classList.add('btn-loading');
                btn.disabled = true;
            }
        });
    });

});

// ── Confirm delete (improved) ──
function confirmDelete(url, itemName) {
    const msg = itemName
        ? `Bạn có chắc chắn muốn xóa "${itemName}"?\nHành động này không thể khôi phục.`
        : 'Bạn có chắc chắn muốn xóa mục này?\nHành động này không thể khôi phục.';
    if (confirm(msg)) window.location.href = url;
}
</script>
</body>
</html>
