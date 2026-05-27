<!-- Equipment List -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-custom alert-dismissible fade show">
        <i class="fa-solid fa-check-circle"></i> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-custom alert-dismissible fade show">
        <i class="fa-solid fa-triangle-exclamation"></i> <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Stats Overview Dashboard -->
<?php
$totalItems = count($equipmentList);
$availableCount = 0;
$maintenanceCount = 0;
$lowStockCount = 0;

foreach ($equipmentList as $item) {
    if ($item['status'] == 'available') {
        $availableCount++;
    } else {
        $maintenanceCount++;
    }
    if ($item['quantity'] <= 10) {
        $lowStockCount++;
    }
}
?>

<div class="row g-3 mb-4" data-aos="fade-down">
    <div class="col-6 col-lg-3">
        <div class="content-card p-3 border-0 shadow-sm d-flex align-items-center gap-3" style="border-radius: 16px; background: linear-gradient(135deg, #eff6ff, #dbeafe);">
            <div class="d-flex align-items-center justify-content-center text-primary" style="width: 48px; height: 48px; border-radius: 12px; background: #white; font-size: 20px; box-shadow: 0 4px 10px rgba(59, 130, 246, 0.1);">
                <i class="fa-solid fa-boxes-stacked"></i>
            </div>
            <div>
                <small class="text-secondary d-block" style="font-size: 12px; font-weight: 500;">Tổng vật tư</small>
                <h4 class="fw-bold m-0 text-primary"><?= $totalItems ?></h4>
            </div>
        </div>
    </div>
    
    <div class="col-6 col-lg-3">
        <div class="content-card p-3 border-0 shadow-sm d-flex align-items-center gap-3" style="border-radius: 16px; background: linear-gradient(135deg, #ecfdf5, #d1fae5);">
            <div class="d-flex align-items-center justify-content-center text-success" style="width: 48px; height: 48px; border-radius: 12px; background: #white; font-size: 20px; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.1);">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <small class="text-secondary d-block" style="font-size: 12px; font-weight: 500;">Sẵn sàng dùng</small>
                <h4 class="fw-bold m-0 text-success"><?= $availableCount ?></h4>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="content-card p-3 border-0 shadow-sm d-flex align-items-center gap-3" style="border-radius: 16px; background: linear-gradient(135deg, #fef2f2, #fee2e2);">
            <div class="d-flex align-items-center justify-content-center text-danger" style="width: 48px; height: 48px; border-radius: 12px; background: #white; font-size: 20px; box-shadow: 0 4px 10px rgba(239, 68, 68, 0.1);">
                <i class="fa-solid fa-screwdriver-wrench"></i>
            </div>
            <div>
                <small class="text-secondary d-block" style="font-size: 12px; font-weight: 500;">Đang bảo trì</small>
                <h4 class="fw-bold m-0 text-danger"><?= $maintenanceCount ?></h4>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="content-card p-3 border-0 shadow-sm d-flex align-items-center gap-3" style="border-radius: 16px; background: linear-gradient(135deg, #fffbeb, #fef3c7);">
            <div class="d-flex align-items-center justify-content-center text-warning" style="width: 48px; height: 48px; border-radius: 12px; background: #white; font-size: 20px; box-shadow: 0 4px 10px rgba(245, 158, 11, 0.1);">
                <i class="fa-solid fa-triangle-exclamation"></i>
            </div>
            <div>
                <small class="text-secondary d-block" style="font-size: 12px; font-weight: 500;">Sắp hết hàng</small>
                <h4 class="fw-bold m-0 text-warning"><?= $lowStockCount ?></h4>
            </div>
        </div>
    </div>
</div>

<!-- Main Equipment Table Card -->
<div class="content-card" data-aos="fade-up">
    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <h5 class="m-0"><i class="fa-solid fa-toolbox me-2 text-primary"></i>Danh sách Vật tư & Thiết bị</h5>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <!-- Smart Instant Search -->
            <div class="input-group" style="width: 250px;">
                <span class="input-group-text bg-white border-end-0" style="border-radius: 20px 0 0 20px;">
                    <i class="fa-solid fa-magnifying-glass text-muted" style="font-size: 13px;"></i>
                </span>
                <input type="text" id="searchSupplies" class="form-control border-start-0 ps-0" placeholder="Tìm vật tư..." style="border-radius: 0 20px 20px 0; font-size: 13px; outline: none; box-shadow: none;">
            </div>
            <a href="index.php?page=equipment&action=create" class="btn-action btn-add py-2" style="border-radius: 20px;">
                <i class="fa-solid fa-plus me-1"></i> Thêm vật tư mới
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 100px;">Mã vật tư</th>
                        <th>Tên trang thiết bị / Vật tư</th>
                        <th style="width: 180px;">Số lượng</th>
                        <th style="width: 180px;">Trạng thái</th>
                        <th style="width: 120px; text-align: center;">Thao tác</th>
                    </tr>
                </thead>
                <tbody id="suppliesTableBody">
                    <?php foreach ($equipmentList as $item): ?>
                    <tr>
                        <td>
                            <span class="badge bg-light text-secondary border px-2.5 py-1" style="font-size: 11px; font-weight: 600; border-radius: 6px;">
                                VT-<?= str_pad($item['id'], 4, '0', STR_PAD_LEFT) ?>
                            </span>
                        </td>
                        <td>
                            <strong class="supply-name text-dark d-block" style="font-size: 14px;"><?= htmlspecialchars($item['equipment_name']) ?></strong>
                            <span class="supply-desc text-muted d-block text-truncate" style="font-size: 12px; max-width: 450px;" title="<?= htmlspecialchars($item['description'] ?? '') ?>">
                                <?= htmlspecialchars($item['description'] ?: 'Không có mô tả chi tiết') ?>
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2">
                                <span class="fw-bold" style="font-size: 14px; color: var(--dark);"><?= $item['quantity'] ?></span>
                                <?php if ($item['quantity'] == 0): ?>
                                    <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-2 py-0.5" style="font-size: 10px; border-radius: 4px;">Hết hàng</span>
                                <?php elseif ($item['quantity'] <= 10): ?>
                                    <span class="badge bg-warning-subtle text-warning border border-warning-subtle px-2 py-0.5" style="font-size: 10px; border-radius: 4px;">Sắp hết</span>
                                <?php endif; ?>
                            </div>
                            <!-- Mini Progress Stock Level Indicator -->
                            <div class="progress mt-1.5" style="height: 4px; width: 100px; border-radius: 2px; background-color: var(--gray-100);">
                                <?php
                                $percent = min(100, max(0, $item['quantity'] * 2)); // Scale: 50+ is full
                                $barColor = '#10b981'; // Green
                                if ($item['quantity'] == 0) {
                                    $percent = 100;
                                    $barColor = '#ef4444'; // Red
                                } elseif ($item['quantity'] <= 10) {
                                    $barColor = '#f59e0b'; // Orange
                                }
                                ?>
                                <div class="progress-bar" style="width: <?= $percent ?>%; background-color: <?= $barColor ?>;"></div>
                            </div>
                        </td>
                        <td>
                            <?php if ($item['status'] == 'available'): ?>
                                <span class="badge-status badge-confirmed d-inline-flex align-items-center gap-1">
                                    <span class="d-inline-block" style="width: 6px; height: 6px; border-radius: 50%; background-color: #10b981;"></span>
                                    Sẵn sàng dùng
                                </span>
                            <?php else: ?>
                                <span class="badge-status badge-cancelled d-inline-flex align-items-center gap-1">
                                    <span class="d-inline-block" style="width: 6px; height: 6px; border-radius: 50%; background-color: #ef4444;"></span>
                                    Đang bảo trì
                                </span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center align-items-center gap-2">
                                <a href="index.php?page=equipment&action=edit&id=<?= $item['id'] ?>" class="btn-action btn-edit" title="Chỉnh sửa" style="border-radius: 6px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                    <i class="fa-solid fa-pen" style="font-size: 12px;"></i>
                                </a>
                                <button onclick="confirmDelete('index.php?page=equipment&action=delete&id=<?= $item['id'] ?>')" class="btn-action btn-delete" title="Xóa" style="border-radius: 6px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                    <i class="fa-solid fa-trash" style="font-size: 12px;"></i>
                                </button>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($equipmentList)): ?>
                    <tr>
                        <td colspan="5" class="text-center text-muted py-5">
                            <i class="fa-solid fa-inbox d-block mb-2" style="font-size: 24px;"></i>
                            Chưa có trang thiết bị nào được ghi nhận.
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchSupplies');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.trim().toLowerCase();
            const rows = document.querySelectorAll('#suppliesTableBody tr');
            
            rows.forEach(row => {
                const nameEl = row.querySelector('.supply-name');
                const descEl = row.querySelector('.supply-desc');
                
                if (nameEl) {
                    const name = nameEl.textContent.toLowerCase();
                    const desc = descEl ? descEl.textContent.toLowerCase() : '';
                    
                    if (name.includes(query) || desc.includes(query)) {
                        row.style.setProperty('display', '', 'important');
                    } else {
                        row.style.setProperty('display', 'none', 'important');
                    }
                }
            });
        });
    }
});
</script>
