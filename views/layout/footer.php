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

    // ── Notification AJAX Handlers ──
    window.markSingleAsRead = function(event, id) {
        if (event) {
            event.stopPropagation();
            event.preventDefault();
        }
        
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        if (!csrfMeta) return;
        const csrfToken = csrfMeta.content;
        
        const formData = new FormData();
        formData.append('id', id);
        formData.append('csrf_token', csrfToken);
        
        fetch('index.php?page=notifications&action=markAsRead', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const item = document.querySelector(`.notif-item[data-id="${id}"]`);
                if (item) {
                    item.style.opacity = '0';
                    item.style.transform = 'translateX(50px)';
                    setTimeout(() => {
                        item.remove();
                        updateNotifBadgeCount();
                    }, 300);
                }
            }
        })
        .catch(err => console.error('Lỗi khi đánh dấu đã đọc:', err));
    };

    window.markAllNotificationsAsRead = function(event) {
        if (event) {
            event.stopPropagation();
            event.preventDefault();
        }
        
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        if (!csrfMeta) return;
        const csrfToken = csrfMeta.content;
        
        const formData = new FormData();
        formData.append('csrf_token', csrfToken);
        
        fetch('index.php?page=notifications&action=markAllAsRead', {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const dbItems = document.querySelectorAll('.notif-item.db-notif');
                dbItems.forEach(item => {
                    item.style.opacity = '0';
                    item.style.transform = 'translateX(50px)';
                    setTimeout(() => {
                        item.remove();
                        updateNotifBadgeCount();
                    }, 300);
                });
                
                const readAllBtn = document.querySelector('.notif-footer a[onclick*="markAllNotificationsAsRead"]');
                if (readAllBtn) {
                    readAllBtn.style.display = 'none';
                }
            }
        })
        .catch(err => console.error('Lỗi khi đánh dấu đọc tất cả:', err));
    };

    function updateNotifBadgeCount() {
        const badge = document.querySelector('.notif-count-badge');
        const headerCount = document.querySelector('.notif-panel .notif-count');
        const list = document.querySelector('.notif-panel .notif-list');
        
        const remainingItems = document.querySelectorAll('.notif-panel .notif-item').length;
        
        if (remainingItems > 0) {
            if (badge) badge.textContent = remainingItems;
            if (headerCount) headerCount.textContent = remainingItems + ' mới';
        } else {
            if (badge) badge.remove();
            if (headerCount) headerCount.remove();
            if (list) {
                list.innerHTML = `
                    <div class="notif-empty">
                        <i class="fa-regular fa-bell-slash"></i>
                        Không có thông báo mới
                    </div>
                `;
            }
        }
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
    const filterSearch = document.getElementById('filterSearch');
    const filterFrom   = document.getElementById('filterFrom');
    const filterTo     = document.getElementById('filterTo');
    const filterClear  = document.getElementById('filterClear');
    const filterTable  = document.getElementById('appointmentsTable');

    function applyFilter() {
        if (!filterTable) return;
        const status = filterStatus ? filterStatus.value.toLowerCase() : '';
        const search = filterSearch ? filterSearch.value.toLowerCase().trim() : '';
        const from   = filterFrom  ? filterFrom.value  : '';
        const to     = filterTo    ? filterTo.value    : '';

        filterTable.querySelectorAll('tbody tr').forEach(row => {
            const rowStatus = (row.dataset.status || '').toLowerCase();
            const rowDate   = row.dataset.date || '';
            const rowText   = row.textContent.toLowerCase();

            let show = true;
            if (status && rowStatus !== status) show = false;
            if (from   && rowDate < from)       show = false;
            if (to     && rowDate > to)         show = false;
            if (search && !rowText.includes(search)) show = false;

            row.style.display = show ? '' : 'none';
        });
    }

    if (filterStatus) filterStatus.addEventListener('change', applyFilter);
    if (filterSearch) filterSearch.addEventListener('input', applyFilter);
    if (filterFrom)   filterFrom.addEventListener('change', applyFilter);
    if (filterTo)     filterTo.addEventListener('change', applyFilter);
    if (filterClear) {
        filterClear.addEventListener('click', () => {
            if (filterStatus) filterStatus.value = '';
            if (filterSearch) filterSearch.value = '';
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

// ── Confirm delete (improved with POST + CSRF) ──
function confirmDelete(url, itemName) {
    const msg = itemName
        ? `Bạn có chắc chắn muốn xóa "${itemName}"?\nHành động này không thể khôi phục.`
        : 'Bạn có chắc chắn muốn xóa mục này?\nHành động này không thể khôi phục.';
    if (confirm(msg)) {
        // Create a form to POST the request
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = url;
        
        // Extract URL parameters and convert to hidden inputs for the POST body
        const urlObj = new URL(url, window.location.origin);
        for (const [key, value] of urlObj.searchParams.entries()) {
            if (key === 'page' || key === 'action') continue; // keep in URL query
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = key;
            input.value = value;
            form.appendChild(input);
        }
        
        // Add CSRF token
        const csrfMeta = document.querySelector('meta[name="csrf-token"]');
        if (csrfMeta) {
            const csrfInput = document.createElement('input');
            csrfInput.type = 'hidden';
            csrfInput.name = 'csrf_token';
            csrfInput.value = csrfMeta.content;
            form.appendChild(csrfInput);
        }
        
        document.body.appendChild(form);
        form.submit();
    }
}

// ── Execute POST action from link (for status updates, etc) ──
function postAction(url) {
    // Create a form to POST the request
    const form = document.createElement('form');
    form.method = 'POST';
    form.action = url;
    
    // Extract URL parameters and convert to hidden inputs for the POST body
    const urlObj = new URL(url, window.location.origin);
    for (const [key, value] of urlObj.searchParams.entries()) {
        if (key === 'page' || key === 'action') continue; // keep in URL query
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = key;
        input.value = value;
        form.appendChild(input);
    }
    
    // Add CSRF token
    const csrfMeta = document.querySelector('meta[name="csrf-token"]');
    if (csrfMeta) {
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = 'csrf_token';
        csrfInput.value = csrfMeta.content;
        form.appendChild(csrfInput);
    }
    
    document.body.appendChild(form);
    form.submit();
}

// ── Voice Recognition (Speech to Text) ──
document.addEventListener('DOMContentLoaded', function() {
    const SpeechRecognition = window.SpeechRecognition || window.webkitSpeechRecognition;
    if (!SpeechRecognition) {
        // Trình duyệt không hỗ trợ, ẩn các nút micro
        document.querySelectorAll('.voice-input-btn').forEach(btn => btn.style.display = 'none');
        return;
    }

    document.querySelectorAll('.voice-input-btn').forEach(btn => {
        const targetId = btn.getAttribute('data-target');
        const targetInput = document.getElementById(targetId);
        if (!targetInput) return;

        const recognition = new SpeechRecognition();
        recognition.lang = 'vi-VN';
        recognition.interimResults = true;
        recognition.continuous = false;

        let isRecording = false;
        let originalHtml = btn.innerHTML;
        let finalTranscript = '';

        btn.addEventListener('click', (e) => {
            e.preventDefault();
            if (isRecording) {
                recognition.stop();
            } else {
                finalTranscript = '';
                recognition.start();
            }
        });

        recognition.onstart = function() {
            isRecording = true;
            btn.classList.remove('btn-outline-primary', 'text-muted');
            btn.classList.add('btn-danger', 'text-white');
            btn.innerHTML = '<i class="fa-solid fa-microphone-lines fa-fade me-1"></i> Đang nghe...';
            targetInput.setAttribute('placeholder', 'Hệ thống đang nghe... hãy nói tiếng Việt');
        };

        recognition.onresult = function(event) {
            let interimTranscript = '';
            for (let i = event.resultIndex; i < event.results.length; ++i) {
                if (event.results[i].isFinal) {
                    finalTranscript += event.results[i][0].transcript;
                } else {
                    interimTranscript += event.results[i][0].transcript;
                }
            }
            
            let cleanTranscript = (finalTranscript + interimTranscript).trim();
            if(cleanTranscript) {
                // Điền tạm thời vào input, nối với dữ liệu cũ
                let currentValue = targetInput.getAttribute('data-original-val');
                if (currentValue === null) {
                    currentValue = targetInput.value;
                    targetInput.setAttribute('data-original-val', currentValue);
                }
                
                const separator = currentValue.trim().length > 0 ? '. ' : '';
                targetInput.value = currentValue + separator + cleanTranscript;
            }
        };

        recognition.onerror = function(event) {
            console.error('Lỗi nhận diện giọng nói:', event.error);
            stopRecordingUI();
            if (event.error !== 'no-speech') {
                alert('Không thể nhận diện giọng nói. Lỗi: ' + event.error);
            }
        };

        recognition.onend = function() {
            stopRecordingUI();
            targetInput.removeAttribute('data-original-val');
        };

        function stopRecordingUI() {
            isRecording = false;
            btn.classList.remove('btn-danger', 'text-white');
            btn.classList.add('btn-outline-primary');
            btn.innerHTML = originalHtml;
            targetInput.setAttribute('placeholder', 'Mô tả tiền sử bệnh, dị ứng thuốc (nếu có)...');
        }
    });
});
</script>
</body>
</html>
