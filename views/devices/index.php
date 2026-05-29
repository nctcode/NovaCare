<!-- Device List -->
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

<!-- Stats Overview Dashboard for Devices -->
<?php
$totalDevices = count($devices);
$availableDevices = 0;
$inUseDevices = 0;
$maintenanceDevices = 0;

foreach ($devices as $d) {
    if ($d['status'] === 'available') {
        $availableDevices++;
    } elseif ($d['status'] === 'in_use') {
        $inUseDevices++;
    } elseif ($d['status'] === 'maintenance') {
        $maintenanceDevices++;
    }
}
?>

<div class="row g-3 mb-4" data-aos="fade-down">
    <div class="col-6 col-lg-3">
        <div class="content-card p-3 border-0 shadow-sm d-flex align-items-center gap-3" style="border-radius: 16px; background: linear-gradient(135deg, #eff6ff, #dbeafe);">
            <div class="d-flex align-items-center justify-content-center text-primary" style="width: 48px; height: 48px; border-radius: 12px; background: #white; font-size: 20px; box-shadow: 0 4px 10px rgba(59, 130, 246, 0.1);">
                <i class="fa-solid fa-laptop-medical"></i>
            </div>
            <div>
                <small class="text-secondary d-block" style="font-size: 12px; font-weight: 500;">Tổng số máy móc</small>
                <h4 class="fw-bold m-0 text-primary"><?= $totalDevices ?></h4>
            </div>
        </div>
    </div>
    
    <div class="col-6 col-lg-3">
        <div class="content-card p-3 border-0 shadow-sm d-flex align-items-center gap-3" style="border-radius: 16px; background: linear-gradient(135deg, #ecfdf5, #d1fae5);">
            <div class="d-flex align-items-center justify-content-center text-success" style="width: 48px; height: 48px; border-radius: 12px; background: #white; font-size: 20px; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.1);">
                <i class="fa-solid fa-circle-check"></i>
            </div>
            <div>
                <small class="text-secondary d-block" style="font-size: 12px; font-weight: 500;">Sẵn sàng</small>
                <h4 class="fw-bold m-0 text-success"><?= $availableDevices ?></h4>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-3">
        <div class="content-card p-3 border-0 shadow-sm d-flex align-items-center gap-3" style="border-radius: 16px; background: linear-gradient(135deg, #e0f2fe, #bae6fd);">
            <div class="d-flex align-items-center justify-content-center text-info" style="width: 48px; height: 48px; border-radius: 12px; background: #white; font-size: 20px; box-shadow: 0 4px 10px rgba(14, 165, 233, 0.1);">
                <i class="fa-solid fa-circle-play"></i>
            </div>
            <div>
                <small class="text-secondary d-block" style="font-size: 12px; font-weight: 500;">Đang sử dụng</small>
                <h4 class="fw-bold m-0 text-info"><?= $inUseDevices ?></h4>
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
                <h4 class="fw-bold m-0 text-danger"><?= $maintenanceDevices ?></h4>
            </div>
        </div>
    </div>
</div>

<!-- Main Devices Card -->
<div class="content-card" data-aos="fade-up">
    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <h5 class="m-0"><i class="fa-solid fa-laptop-medical me-2 text-primary"></i>Quản lý Thiết bị & Máy móc Y tế</h5>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <!-- Search bar -->
            <div class="input-group" style="width: 250px;">
                <span class="input-group-text bg-white border-end-0" style="border-radius: 20px 0 0 20px;">
                    <i class="fa-solid fa-magnifying-glass text-muted" style="font-size: 13px;"></i>
                </span>
                <input type="text" id="searchDevices" class="form-control border-start-0 ps-0" placeholder="Tìm thiết bị..." style="border-radius: 0 20px 20px 0; font-size: 13px; outline: none; box-shadow: none;">
            </div>
            <?php if ($user['role'] === 'admin'): ?>
            <a href="index.php?page=devices&action=create" class="btn-action btn-add py-2" style="border-radius: 20px;">
                <i class="fa-solid fa-plus me-1"></i> Thêm thiết bị mới
            </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">#</th>
                        <th>Tên thiết bị</th>
                        <th>Mã thiết bị</th>
                        <th>Khoa sử dụng</th>
                        <th>Ngày mua</th>
                        <th>Trạng thái</th>
                        <?php if ($user['role'] === 'admin'): ?>
                        <th style="width: 150px; text-align: center;">Thao tác</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody id="devicesTableBody">
                    <?php if (empty($devices)): ?>
                    <tr>
                        <td colspan="7" class="text-center text-muted py-5">
                            <i class="fa-solid fa-inbox d-block mb-2" style="font-size: 24px;"></i>
                            Chưa có thiết bị nào được ghi nhận.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($devices as $idx => $d): ?>
                    <tr>
                        <td><?= $idx + 1 ?></td>
                        <td>
                            <strong class="device-name text-dark" style="font-size: 14px;"><?= htmlspecialchars($d['name']) ?></strong>
                        </td>
                        <td>
                            <span class="badge bg-light text-secondary border px-2.5 py-1" style="font-size: 11px; font-weight: 600; border-radius: 6px;">
                                <?= htmlspecialchars($d['device_code'] ?? 'N/A') ?>
                            </span>
                        </td>
                        <td>
                            <span class="device-dept text-secondary" style="font-size: 13px;">
                                <i class="fa-solid fa-building-columns text-muted me-1.5" style="font-size: 11px;"></i>
                                <?= htmlspecialchars($d['department_name'] ?? 'Chưa phân khoa') ?>
                            </span>
                        </td>
                        <td>
                            <span class="text-secondary" style="font-size: 13px;">
                                <?= $d['purchase_date'] ? date('d/m/Y', strtotime($d['purchase_date'])) : '-' ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($d['status'] === 'available'): ?>
                                <span class="badge-status badge-confirmed d-inline-flex align-items-center gap-1">
                                    <span class="d-inline-block" style="width: 6px; height: 6px; border-radius: 50%; background-color: #10b981;"></span>
                                    Sẵn sàng
                                </span>
                            <?php elseif ($d['status'] === 'in_use'): ?>
                                <span class="badge bg-info-subtle text-info border border-info-subtle px-2.5 py-1 d-inline-flex align-items-center gap-1" style="font-size: 12px; border-radius: 12px; font-weight: 500;">
                                    <span class="d-inline-block" style="width: 6px; height: 6px; border-radius: 50%; background-color: #0ea5e9;"></span>
                                    Đang dùng
                                </span>
                            <?php else: ?>
                                <span class="badge-status badge-cancelled d-inline-flex align-items-center gap-1">
                                    <span class="d-inline-block" style="width: 6px; height: 6px; border-radius: 50%; background-color: #ef4444;"></span>
                                    Bảo trì
                                </span>
                            <?php endif; ?>
                        </td>
                        <?php if ($user['role'] === 'admin'): ?>
                        <td>
                            <div class="d-flex justify-content-center align-items-center gap-2">
                                <div class="dropdown d-inline">
                                    <button class="btn btn-outline-secondary dropdown-toggle py-1 px-2.5 d-inline-flex align-items-center gap-1" data-bs-toggle="dropdown" style="font-size: 12px; border-radius: 8px; font-weight: 500; border-color: var(--gray-200);">
                                        <i class="fa-solid fa-gear" style="font-size: 11px;"></i> Cập nhật trạng thái
                                    </button>
                                    <ul class="dropdown-menu dropdown-menu-end border-0 shadow" style="border-radius: 12px; font-size: 13px;">
                                        <li>
                                            <a class="dropdown-item py-2 px-3 d-flex align-items-center gap-2" href="javascript:void(0)" onclick="postAction('index.php?page=devices&action=updateStatus&id=<?= $d['id'] ?>&status=available')">
                                                <span style="width: 8px; height: 8px; border-radius: 50%; background-color: #10b981;"></span> Sẵn sàng
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-2 px-3 d-flex align-items-center gap-2" href="javascript:void(0)" onclick="postAction('index.php?page=devices&action=updateStatus&id=<?= $d['id'] ?>&status=in_use')">
                                                <span style="width: 8px; height: 8px; border-radius: 50%; background-color: #0ea5e9;"></span> Đang dùng
                                            </a>
                                        </li>
                                        <li>
                                            <a class="dropdown-item py-2 px-3 d-flex align-items-center gap-2" href="javascript:void(0)" onclick="postAction('index.php?page=devices&action=updateStatus&id=<?= $d['id'] ?>&status=maintenance')">
                                                <span style="width: 8px; height: 8px; border-radius: 50%; background-color: #ef4444;"></span> Bảo trì
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                                <button onclick="confirmDelete('index.php?page=devices&action=delete&id=<?= $d['id'] ?>', '<?= htmlspecialchars($d['name']) ?>')" 
                                        class="btn-action btn-delete" title="Xóa thiết bị" style="border-radius: 8px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                    <i class="fa-solid fa-trash" style="font-size: 12px;"></i>
                                </button>
                            </div>
                        </td>
                        <?php endif; ?>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('searchDevices');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.trim().toLowerCase();
            const rows = document.querySelectorAll('#devicesTableBody tr');
            
            rows.forEach(row => {
                const nameEl = row.querySelector('.device-name');
                const deptEl = row.querySelector('.device-dept');
                
                if (nameEl) {
                    const name = nameEl.textContent.toLowerCase();
                    const dept = deptEl ? deptEl.textContent.toLowerCase() : '';
                    
                    if (name.includes(query) || dept.includes(query)) {
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
