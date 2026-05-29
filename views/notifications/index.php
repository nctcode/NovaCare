<?php
/**
 * View Notifications Index - Lịch sử thông báo người dùng
 */
?>
<div class="container-fluid py-4">
    <!-- Header Page Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h5 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-list-check me-2 text-primary"></i>Lịch sử Thông báo</h5>
            <p class="text-muted mb-0 small">Xem và quản lý tất cả các thông báo, cảnh báo từ hệ thống NovaCare gửi riêng cho bạn.</p>
        </div>
        <div class="d-flex gap-2">
            <?php if (count($notifications) > 0): ?>
            <form action="index.php?page=notifications&action=markAllAsRead" method="POST" class="d-inline">
                <?= Security::csrfField() ?>
                <button type="submit" class="btn btn-outline-primary btn-sm rounded-pill px-3">
                    <i class="fa-solid fa-check-double me-1"></i>Đánh dấu đọc tất cả
                </button>
            </form>
            <?php endif; ?>
            <a href="index.php?page=dashboard" class="btn btn-light btn-sm rounded-pill px-3 text-secondary">
                <i class="fa-solid fa-arrow-left me-1"></i>Quay lại Dashboard
            </a>
        </div>
    </div>

    <!-- Status Alerts -->
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i><?= $_SESSION['success'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show rounded-3 mb-4" role="alert">
            <i class="fa-solid fa-circle-exclamation me-2"></i><?= $_SESSION['error'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>

    <!-- Main Content Area -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-0 py-3">
            <div class="d-flex justify-content-between align-items-center">
                <!-- Tabs filters -->
                <ul class="nav nav-pills nav-pills-custom" id="pills-tab" role="tablist">
                    <li class="nav-item" role="presentation">
                        <button class="nav-link active" id="pills-all-tab" data-bs-toggle="pill" data-bs-target="#pills-all" type="button" role="tab" aria-selected="true">
                            Tất cả <span class="badge bg-secondary ms-1"><?= count($notifications) ?></span>
                        </button>
                    </li>
                    <li class="nav-item" role="presentation">
                        <button class="nav-link" id="pills-unread-tab" data-bs-toggle="pill" data-bs-target="#pills-unread" type="button" role="tab" aria-selected="false">
                            Chưa đọc 
                            <?php 
                            $unreadCount = count(array_filter($notifications, function($n) { return $n['status'] === 'unread'; }));
                            ?>
                            <span class="badge bg-danger ms-1"><?= $unreadCount ?></span>
                        </button>
                    </li>
                </ul>
                <!-- Search input -->
                <div class="position-relative" style="width: 250px;">
                    <i class="fa-solid fa-magnifying-glass position-absolute top-50 start-0 translate-middle-y ms-3 text-muted"></i>
                    <input type="text" class="form-control form-control-sm ps-5 rounded-pill border-light bg-light" id="notifSearch" placeholder="Tìm thông báo...">
                </div>
            </div>
        </div>

        <div class="tab-content" id="pills-tabContent">
            <!-- Tab ALL -->
            <div class="tab-pane fade show active" id="pills-all" role="tabpanel" aria-labelledby="pills-all-tab">
                <div class="list-group list-group-flush" id="allNotifList">
                    <?php if (count($notifications) > 0): ?>
                        <?php foreach ($notifications as $n): 
                            $icon = 'fa-solid fa-bell';
                            $iconClass = 'bg-primary-subtle text-primary';
                            
                            $titleLower = mb_strtolower($n['title'], 'UTF-8');
                            if (strpos($titleLower, 'lịch') !== false || strpos($titleLower, 'hẹn') !== false || strpos($titleLower, 'khám') !== false) {
                                $icon = 'fa-solid fa-calendar-check';
                                $iconClass = 'bg-info-subtle text-info';
                            } elseif (strpos($titleLower, 'xét nghiệm') !== false || strpos($titleLower, 'cận lâm sàng') !== false || strpos($titleLower, 'kết quả') !== false) {
                                $icon = 'fa-solid fa-flask-vial';
                                $iconClass = 'bg-danger-subtle text-danger';
                            } elseif (strpos($titleLower, 'đơn thuốc') !== false || strpos($titleLower, 'thuốc') !== false) {
                                $icon = 'fa-solid fa-capsules';
                                $iconClass = 'bg-success-subtle text-success';
                            } elseif (strpos($titleLower, 'hóa đơn') !== false || strpos($titleLower, 'thanh toán') !== false || strpos($titleLower, 'tiền') !== false) {
                                $icon = 'fa-solid fa-file-invoice-dollar';
                                $iconClass = 'bg-warning-subtle text-warning';
                            }
                        ?>
                            <div class="list-group-item list-group-item-action d-flex align-items-center py-3 border-0 border-bottom justify-content-between notif-row-item <?= $n['status'] === 'unread' ? 'bg-light-unread' : '' ?>" data-id="<?= $n['id'] ?>">
                                <div class="d-flex align-items-center flex-grow-1">
                                    <!-- Icon -->
                                    <div class="flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 42px; height: 42px; font-size: 18px; <?= $iconClass ?>">
                                        <i class="<?= $icon ?>"></i>
                                    </div>
                                    <!-- Body -->
                                    <div class="me-3">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <h6 class="mb-0 fw-semibold text-dark text-searchable"><?= htmlspecialchars($n['title']) ?></h6>
                                            <?php if ($n['status'] === 'unread'): ?>
                                                <span class="badge bg-danger rounded-pill dot-unread" style="font-size: 8px; padding: 4px;">Chưa đọc</span>
                                            <?php endif; ?>
                                        </div>
                                        <p class="mb-0 text-muted small text-searchable"><?= htmlspecialchars($n['message']) ?></p>
                                        <small class="text-muted-time text-xs mt-1 d-block">
                                            <i class="fa-regular fa-clock me-1"></i><?= date('H:i - d/m/Y', strtotime($n['created_at'])) ?>
                                        </small>
                                    </div>
                                </div>
                                <!-- Actions -->
                                <div class="d-flex align-items-center gap-1 action-buttons">
                                    <?php if ($n['status'] === 'unread'): ?>
                                        <form action="index.php?page=notifications&action=markAsRead" method="POST" class="d-inline mark-read-form">
                                            <?= Security::csrfField() ?>
                                            <input type="hidden" name="id" value="<?= $n['id'] ?>">
                                            <button type="submit" class="btn btn-link p-2 text-primary" title="Đánh dấu đã đọc">
                                                <i class="fa-solid fa-check fs-5"></i>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                    <form action="index.php?page=notifications&action=delete" method="POST" class="d-inline delete-form" onsubmit="return confirm('Bạn có chắc chắn muốn xóa thông báo này?');">
                                        <?= Security::csrfField() ?>
                                        <input type="hidden" name="id" value="<?= $n['id'] ?>">
                                        <button type="submit" class="btn btn-link p-2 text-danger" title="Xóa thông báo">
                                            <i class="fa-solid fa-trash-can fs-6"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fa-regular fa-bell-slash text-muted mb-3" style="font-size: 48px;"></i>
                            <h6 class="text-muted">Không có thông báo nào</h6>
                            <p class="text-muted small mb-0">Hộp thư thông báo của bạn trống trơn.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Tab UNREAD -->
            <div class="tab-pane fade" id="pills-unread" role="tabpanel" aria-labelledby="pills-unread-tab">
                <div class="list-group list-group-flush" id="unreadNotifList">
                    <?php 
                    $unreadNotifs = array_filter($notifications, function($n) { return $n['status'] === 'unread'; });
                    if (count($unreadNotifs) > 0): 
                        foreach ($unreadNotifs as $n): 
                            $icon = 'fa-solid fa-bell';
                            $iconClass = 'bg-primary-subtle text-primary';
                            
                            $titleLower = mb_strtolower($n['title'], 'UTF-8');
                            if (strpos($titleLower, 'lịch') !== false || strpos($titleLower, 'hẹn') !== false || strpos($titleLower, 'khám') !== false) {
                                $icon = 'fa-solid fa-calendar-check';
                                $iconClass = 'bg-info-subtle text-info';
                            } elseif (strpos($titleLower, 'xét nghiệm') !== false || strpos($titleLower, 'cận lâm sàng') !== false || strpos($titleLower, 'kết quả') !== false) {
                                $icon = 'fa-solid fa-flask-vial';
                                $iconClass = 'bg-danger-subtle text-danger';
                            } elseif (strpos($titleLower, 'đơn thuốc') !== false || strpos($titleLower, 'thuốc') !== false) {
                                $icon = 'fa-solid fa-capsules';
                                $iconClass = 'bg-success-subtle text-success';
                            } elseif (strpos($titleLower, 'hóa đơn') !== false || strpos($titleLower, 'thanh toán') !== false || strpos($titleLower, 'tiền') !== false) {
                                $icon = 'fa-solid fa-file-invoice-dollar';
                                $iconClass = 'bg-warning-subtle text-warning';
                            }
                        ?>
                            <div class="list-group-item list-group-item-action d-flex align-items-center py-3 border-0 border-bottom justify-content-between notif-row-item bg-light-unread" data-id="<?= $n['id'] ?>">
                                <div class="d-flex align-items-center flex-grow-1">
                                    <!-- Icon -->
                                    <div class="flex-shrink-0 rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 42px; height: 42px; font-size: 18px; <?= $iconClass ?>">
                                        <i class="<?= $icon ?>"></i>
                                    </div>
                                    <!-- Body -->
                                    <div class="me-3">
                                        <div class="d-flex align-items-center gap-2 mb-1">
                                            <h6 class="mb-0 fw-semibold text-dark text-searchable"><?= htmlspecialchars($n['title']) ?></h6>
                                            <span class="badge bg-danger rounded-pill dot-unread" style="font-size: 8px; padding: 4px;">Chưa đọc</span>
                                        </div>
                                        <p class="mb-0 text-muted small text-searchable"><?= htmlspecialchars($n['message']) ?></p>
                                        <small class="text-muted-time text-xs mt-1 d-block">
                                            <i class="fa-regular fa-clock me-1"></i><?= date('H:i - d/m/Y', strtotime($n['created_at'])) ?>
                                        </small>
                                    </div>
                                </div>
                                <!-- Actions -->
                                <div class="d-flex align-items-center gap-1 action-buttons">
                                    <form action="index.php?page=notifications&action=markAsRead" method="POST" class="d-inline mark-read-form">
                                        <?= Security::csrfField() ?>
                                        <input type="hidden" name="id" value="<?= $n['id'] ?>">
                                        <button type="submit" class="btn btn-link p-2 text-primary" title="Đánh dấu đã đọc">
                                            <i class="fa-solid fa-check fs-5"></i>
                                        </button>
                                    </form>
                                    <form action="index.php?page=notifications&action=delete" method="POST" class="d-inline delete-form" onsubmit="return confirm('Bạn có chắc chắn muốn xóa thông báo này?');">
                                        <?= Security::csrfField() ?>
                                        <input type="hidden" name="id" value="<?= $n['id'] ?>">
                                        <button type="submit" class="btn btn-link p-2 text-danger" title="Xóa thông báo">
                                            <i class="fa-solid fa-trash-can fs-6"></i>
                                        </button>
                                    </form>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fa-solid fa-check-double text-muted mb-3" style="font-size: 48px;"></i>
                            <h6 class="text-muted">Không có thông báo chưa đọc</h6>
                            <p class="text-muted small mb-0">Tuyệt vời! Bạn đã đọc toàn bộ các thông báo.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-light-unread {
        background-color: rgba(14, 165, 233, 0.03) !important;
        border-left: 3px solid #0ea5e9 !important;
    }
    .text-xs {
        font-size: 11px;
    }
    .nav-pills-custom .nav-link {
        color: #64748b;
        font-weight: 500;
        border-radius: 20px;
        padding: 6px 16px;
        transition: all 0.2s;
    }
    .nav-pills-custom .nav-link.active {
        background-color: #0ea5e9;
        color: #fff;
    }
    .notif-row-item {
        transition: all 0.2s ease;
    }
    .notif-row-item:hover {
        background-color: rgba(0, 0, 0, 0.01) !important;
    }
    [data-theme="dark"] .notif-row-item:hover {
        background-color: rgba(255, 255, 255, 0.02) !important;
    }
    [data-theme="dark"] .bg-light-unread {
        background-color: rgba(14, 165, 233, 0.07) !important;
    }
    .text-muted-time {
        color: #94a3b8;
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Tìm kiếm thông báo
    const searchInput = document.getElementById('notifSearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            const items = document.querySelectorAll('.notif-row-item');
            items.forEach(item => {
                const searchTexts = item.querySelectorAll('.text-searchable');
                let found = false;
                searchTexts.forEach(el => {
                    if (el.textContent.toLowerCase().includes(query)) {
                        found = true;
                    }
                });
                item.style.setProperty('display', found ? 'flex' : 'none', 'important');
            });
        });
    }

    // Ajaxify marking as read from the history page
    document.querySelectorAll('.mark-read-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const idInput = this.querySelector('input[name="id"]');
            if (!idInput) return;
            const id = idInput.value;
            const csrfToken = this.querySelector('input[name="csrf_token"]').value;

            const formData = new FormData();
            formData.append('id', id);
            formData.append('csrf_token', csrfToken);

            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Update layout
                    const row = document.querySelector(`.notif-row-item[data-id="${id}"]`);
                    if (row) {
                        row.classList.remove('bg-light-unread');
                        row.style.borderLeft = 'none';
                        const badge = row.querySelector('.dot-unread');
                        if (badge) badge.remove();
                        const checkBtn = row.querySelector('.mark-read-form');
                        if (checkBtn) checkBtn.remove();
                        
                        // Update header count badge (calls window function from footer)
                        if (typeof updateNotifBadgeCount === 'function') {
                            const headerItem = document.querySelector(`.notif-panel .notif-item[data-id="${id}"]`);
                            if (headerItem) {
                                headerItem.remove();
                                updateNotifBadgeCount();
                            }
                        }
                    }
                }
            })
            .catch(err => console.error('Lỗi khi cập nhật đã đọc:', err));
        });
    });

    // Ajaxify deleting from the history page
    document.querySelectorAll('.delete-form').forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            if (!confirm('Bạn có chắc chắn muốn xóa thông báo này?')) return;
            
            const idInput = this.querySelector('input[name="id"]');
            if (!idInput) return;
            const id = idInput.value;
            const csrfToken = this.querySelector('input[name="csrf_token"]').value;

            const formData = new FormData();
            formData.append('id', id);
            formData.append('csrf_token', csrfToken);

            fetch(this.action, {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    const row = document.querySelectorAll(`.notif-row-item[data-id="${id}"]`);
                    row.forEach(r => {
                        r.style.opacity = '0';
                        r.style.transform = 'translateY(15px)';
                        setTimeout(() => {
                            r.remove();
                            // Update badge count
                            if (typeof updateNotifBadgeCount === 'function') {
                                const headerItem = document.querySelector(`.notif-panel .notif-item[data-id="${id}"]`);
                                if (headerItem) {
                                    headerItem.remove();
                                    updateNotifBadgeCount();
                                }
                            }
                        }, 300);
                    });
                }
            })
            .catch(err => console.error('Lỗi khi xóa thông báo:', err));
        });
    });
});
</script>
