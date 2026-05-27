<!-- Dashboard Lễ tân (Receptionist) - NovaCare -->

<!-- Thống kê nhanh -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6" data-aos="fade-up">
        <div class="stat-card card-primary">
            <div class="stat-icon"><i class="fa-solid fa-hospital-user"></i></div>
            <div class="stat-value"><?= $data['totalPatients'] ?? 0 ?></div>
            <div class="stat-label">Tổng bệnh nhân</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6" data-aos="fade-up" data-aos-delay="50">
        <div class="stat-card card-warning">
            <div class="stat-icon"><i class="fa-solid fa-calendar-check"></i></div>
            <div class="stat-value"><?= $data['totalAppointments'] ?? 0 ?></div>
            <div class="stat-label">Tổng lịch hẹn</div>
            <?php $pendingPct = ($data['totalAppointments'] ?? 0) > 0 ? round(($data['pendingAppointments'] ?? 0) / $data['totalAppointments'] * 100) : 0; ?>
            <div class="stat-trend <?= $pendingPct > 30 ? 'down' : 'up' ?>">
                <i class="fa-solid fa-hourglass-half"></i> <?= $data['pendingAppointments'] ?? 0 ?> đang chờ xử lý
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
        <div class="stat-card card-success">
            <div class="stat-icon"><i class="fa-solid fa-check-circle"></i></div>
            <div class="stat-value"><?= $data['confirmedAppointments'] ?? 0 ?></div>
            <div class="stat-label">Đã xác nhận</div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6" data-aos="fade-up" data-aos-delay="150">
        <div class="stat-card card-info">
            <div class="stat-icon"><i class="fa-solid fa-user-doctor"></i></div>
            <div class="stat-value"><?= $data['totalDoctors'] ?? 0 ?></div>
            <div class="stat-label">Bác sĩ đang làm</div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-3 mb-4">
    <div class="col-12" data-aos="fade-up">
        <div class="d-flex flex-wrap gap-2">
            <a href="index.php?page=patients&action=create" class="btn btn-outline-primary" style="border-radius:20px; padding:8px 20px; font-weight:500; font-size:14px;"><i class="fa-solid fa-user-plus me-2"></i>Thêm Bệnh nhân</a>
            <a href="index.php?page=appointments&action=create" class="btn btn-outline-success" style="border-radius:20px; padding:8px 20px; font-weight:500; font-size:14px;"><i class="fa-solid fa-calendar-plus me-2"></i>Đặt lịch hẹn</a>
        </div>
    </div>
</div>

<!-- Thông báo nhanh -->
<div class="row g-4">
    <div class="col-12" data-aos="fade-up">
        <div class="content-card">
            <div class="card-header">
                <h5 style="font-weight:700; margin:0;"><i class="fa-solid fa-clipboard-list me-2 text-primary"></i>Chức năng chính của Lễ tân</h5>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <a href="index.php?page=patients" class="text-decoration-none">
                            <div class="p-3 text-center" style="background:var(--primary-light); border-radius:12px;">
                                <i class="fa-solid fa-hospital-user fa-2x mb-2" style="color:var(--primary);"></i>
                                <div style="font-weight:600; color:var(--gray-700);">Quản lý Bệnh nhân</div>
                                <small class="text-muted">Thêm, sửa, xem hồ sơ</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="index.php?page=appointments" class="text-decoration-none">
                            <div class="p-3 text-center" style="background:rgba(16,185,129,0.1); border-radius:12px;">
                                <i class="fa-solid fa-calendar-check fa-2x mb-2" style="color:#10b981;"></i>
                                <div style="font-weight:600; color:var(--gray-700);">Lịch hẹn</div>
                                <small class="text-muted">Đặt lịch, xác nhận</small>
                            </div>
                        </a>
                    </div>
                    <div class="col-md-4">
                        <a href="index.php?page=inpatient" class="text-decoration-none">
                            <div class="p-3 text-center" style="background:rgba(99,102,241,0.1); border-radius:12px;">
                                <i class="fa-solid fa-bed-pulse fa-2x mb-2" style="color:#6366f1;"></i>
                                <div style="font-weight:600; color:var(--gray-700);">Xem Nội trú</div>
                                <small class="text-muted">Xem buồng bệnh & sơ đồ giường</small>
                            </div>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
