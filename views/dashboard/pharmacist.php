<!-- Dashboard Dược sĩ (Pharmacist) - NovaCare -->

<!-- Thống kê nhanh -->
<div class="row g-4 mb-4">
    <div class="col-xl-4 col-md-6" data-aos="fade-up">
        <div class="stat-card card-danger">
            <div class="stat-icon"><i class="fa-solid fa-capsules"></i></div>
            <div class="stat-value"><?= $data['totalMedicines'] ?? 0 ?></div>
            <div class="stat-label">Loại thuốc trong kho</div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6" data-aos="fade-up" data-aos-delay="50">
        <div class="stat-card card-warning">
            <div class="stat-icon"><i class="fa-solid fa-triangle-exclamation"></i></div>
            <?php $lowCount = count($lowStockMedicines ?? []); ?>
            <div class="stat-value"><?= $lowCount ?></div>
            <div class="stat-label">Loại thuốc sắp hết</div>
            <?php if ($lowCount > 0): ?>
            <div class="stat-trend down"><i class="fa-solid fa-arrow-trend-down"></i> Cần nhập thêm</div>
            <?php else: ?>
            <div class="stat-trend up"><i class="fa-solid fa-check"></i> Tồn kho ổn định</div>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-xl-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
        <div class="stat-card card-primary">
            <div class="stat-icon"><i class="fa-solid fa-file-prescription"></i></div>
            <div class="stat-value"><?= $data['totalAppointments'] ?? 0 ?></div>
            <div class="stat-label">Tổng đơn thuốc xử lý</div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-3 mb-4">
    <div class="col-12" data-aos="fade-up">
        <div class="d-flex flex-wrap gap-2">
            <a href="index.php?page=medicines" class="btn btn-outline-danger" style="border-radius:20px; padding:8px 20px; font-weight:500; font-size:14px;"><i class="fa-solid fa-capsules me-2"></i>Quản lý Kho thuốc</a>
            <a href="index.php?page=medicines&action=create" class="btn btn-outline-success" style="border-radius:20px; padding:8px 20px; font-weight:500; font-size:14px;"><i class="fa-solid fa-pills me-2"></i>Nhập thuốc mới</a>
            <a href="index.php?page=prescriptions" class="btn btn-outline-primary" style="border-radius:20px; padding:8px 20px; font-weight:500; font-size:14px;"><i class="fa-solid fa-file-prescription me-2"></i>Xem đơn thuốc</a>
        </div>
    </div>
</div>

<!-- Chức năng chính -->
<div class="row g-4">
    <div class="col-md-6" data-aos="fade-up">
        <div class="content-card h-100">
            <div class="card-header">
                <h5 style="font-weight:700; margin:0;"><i class="fa-solid fa-capsules me-2 text-danger"></i>Kho thuốc</h5>
                <a href="index.php?page=medicines" class="btn btn-sm btn-outline-danger" style="border-radius:20px; font-size:13px;">Xem tất cả</a>
            </div>
            <div class="card-body">
                <?php if (!empty($lowStockMedicines)): ?>
                    <div class="alert alert-warning d-flex align-items-center mb-3" style="border-radius:12px;">
                        <i class="fa-solid fa-triangle-exclamation me-2"></i>
                        <strong>Cảnh báo:</strong>&nbsp;Có <?= $lowCount ?> loại thuốc sắp hết hàng!
                    </div>
                    <?php foreach ($lowStockMedicines as $lsm): ?>
                    <div class="d-flex justify-content-between align-items-center p-2 mb-2" style="background:var(--gray-50); border-radius:8px;">
                        <div>
                            <strong><?= htmlspecialchars($lsm['name']) ?></strong>
                        </div>
                        <span class="badge bg-danger" style="border-radius:8px;"><?= $lsm['quantity'] ?> còn lại</span>
                    </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state" style="padding: 24px;">
                        <div class="empty-icon"><i class="fa-solid fa-check-circle" style="color:var(--success);"></i></div>
                        <h6>Tất cả thuốc đều đủ tồn kho</h6>
                        <p>Không có thuốc nào cần nhập thêm.</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <div class="col-md-6" data-aos="fade-up" data-aos-delay="100">
        <div class="content-card h-100">
            <div class="card-header">
                <h5 style="font-weight:700; margin:0;"><i class="fa-solid fa-file-prescription me-2 text-primary"></i>Đơn thuốc gần đây</h5>
                <a href="index.php?page=prescriptions" class="btn btn-sm btn-outline-primary" style="border-radius:20px; font-size:13px;">Xem tất cả</a>
            </div>
            <div class="card-body">
                <div class="text-center p-4">
                    <a href="index.php?page=prescriptions" class="text-decoration-none">
                        <div class="p-3" style="background:var(--primary-light); border-radius:12px; display:inline-block;">
                            <i class="fa-solid fa-file-prescription fa-3x mb-2" style="color:var(--primary);"></i>
                            <div style="font-weight:600; color:var(--gray-700);">Xem & cấp phát đơn thuốc</div>
                            <small class="text-muted">Kiểm tra đơn thuốc mới từ bác sĩ</small>
                        </div>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
