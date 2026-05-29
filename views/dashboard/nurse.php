<!-- Dashboard Điều dưỡng / Y tá -->

<!-- Lớp 1: Cảnh báo quota ca đêm (Mỹ thuật cao) -->
<?php if (isset($data['nightShiftsThisWeek']) && $data['nightShiftsThisWeek'] < 2): ?>
<div class="row g-4 mb-4" data-aos="fade-up">
    <div class="col-12">
        <div class="alert shadow-sm border-0 position-relative p-4 mb-0 overflow-hidden" 
             style="border-radius: 16px; background: linear-gradient(135deg, #fffbeb, #fef3c7); border-left: 5px solid #d97706 !important;">
            <div class="d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center" 
                     style="width: 48px; height: 48px; border-radius: 12px; background: #fef3c7; color: #d97706; font-size: 20px; box-shadow: 0 4px 10px rgba(217, 119, 6, 0.1);">
                    <i class="fa-solid fa-moon"></i>
                </div>
                <div class="flex-grow-1">
                    <h5 class="fw-bold mb-1 text-amber-900" style="color: #78350f;">⚠️ CHƯA ĐỦ CHỈ TIÊU TRỰC ĐÊM</h5>
                    <p class="mb-0 text-amber-800" style="color: #92400e; font-size: 13.5px;">
                        Bạn mới đăng ký <strong><?= $data['nightShiftsThisWeek'] ?>/2</strong> ca trực đêm cho tuần này. 
                        Theo quy định, mỗi nhân viên bắt buộc trực tối thiểu <strong>2 ca đêm/tuần</strong>. 
                        Vui lòng hoàn thành đăng ký trước khi hết hạn để tránh bị Điều dưỡng trưởng tự động chỉ định trực.
                    </p>
                </div>
                <div>
                    <a href="index.php?page=shifts" class="btn btn-warning fw-semibold px-4 py-2" 
                       style="border-radius: 10px; background-color: #d97706; border-color: #d97706; color: white; box-shadow: 0 4px 12px rgba(217, 119, 6, 0.2);">
                        <i class="fa-solid fa-calendar-plus me-1.5"></i> Đăng ký ngay
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Lớp 3: Thống kê Quota ca trực dành cho Điều Dưỡng Trưởng (Nếu có) -->
<?php if (isset($data['isHead']) && $data['isHead'] && isset($data['shiftQuotaStats'])): ?>
<div class="row g-4 mb-4" data-aos="fade-up">
    <div class="col-12">
        <div class="content-card p-4 border-0 shadow-sm bg-white" style="border-radius: 16px;">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0 text-dark d-flex align-items-center gap-2">
                    <i class="fa-solid fa-chart-line text-primary"></i>
                    Bảng Giám sát Chỉ tiêu Trực Đêm của Khoa (Điều dưỡng trưởng)
                </h6>
                <a href="index.php?page=shifts&action=staffQuota" class="btn btn-sm btn-primary px-3 py-1.5" style="border-radius: 8px; background-color: #0284c7; border-color: #0284c7; font-weight: 600; font-size: 12.5px;">
                    <i class="fa-solid fa-circle-info me-1"></i> Xem chi tiết & Chỉ định trực
                </a>
            </div>
            
            <div class="row g-3">
                <div class="col-md-3">
                    <div class="p-3 border border-light-subtle rounded-3 bg-light bg-opacity-50">
                        <div class="text-muted mb-1" style="font-size: 12px;">Đạt chỉ tiêu (≥2 ca đêm)</div>
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 class="fw-bold text-success mb-0"><?= $data['shiftQuotaStats']['sufficient'] ?> nhân sự</h4>
                            <span class="badge bg-success-subtle text-success border border-success-subtle"><i class="fa-solid fa-check"></i> Đạt</span>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-3">
                    <div class="p-3 border border-light-subtle rounded-3 bg-light bg-opacity-50">
                        <div class="text-muted mb-1" style="font-size: 12px;">Chưa đạt chỉ tiêu (Thiếu ca đêm)</div>
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 class="fw-bold text-danger mb-0"><?= $data['shiftQuotaStats']['insufficient'] ?> nhân sự</h4>
                            <span class="badge bg-danger-subtle text-danger border border-danger-subtle"><i class="fa-solid fa-exclamation"></i> Thiếu</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="p-3 border border-light-subtle rounded-3 bg-light bg-opacity-50">
                        <div class="text-muted mb-1" style="font-size: 12px;">Tổng nhân viên trong khoa</div>
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 class="fw-bold text-dark mb-0"><?= $data['shiftQuotaStats']['total_staff'] ?> nhân sự</h4>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle"><i class="fa-solid fa-users"></i> Tổng</span>
                        </div>
                    </div>
                </div>

                <div class="col-md-3">
                    <div class="p-3 border border-light-subtle rounded-3 bg-light bg-opacity-50">
                        <div class="text-muted mb-1" style="font-size: 12px;">Đăng ký đang chờ duyệt</div>
                        <div class="d-flex align-items-center justify-content-between">
                            <h4 class="fw-bold text-warning mb-0"><?= $data['shiftQuotaStats']['pending_count'] ?> đăng ký</h4>
                            <span class="badge bg-warning-subtle text-warning border border-warning-subtle"><i class="fa-solid fa-clock"></i> Chờ duyệt</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="row g-4 mb-4">
    <div class="col-md-4" data-aos="fade-up">
        <div class="stat-card card-success">
            <div class="stat-icon"><i class="fa-solid fa-heart-pulse"></i></div>
            <div class="stat-label">Xin chào, <?= htmlspecialchars($user['name']) ?></div>
            <div class="stat-value mt-2" style="font-size:16px">Điều dưỡng</div>
        </div>
    </div>
    <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
        <a href="index.php?page=inpatient" class="text-decoration-none">
            <div class="stat-card card-primary" style="cursor:pointer;">
                <div class="stat-icon"><i class="fa-solid fa-bed-pulse"></i></div>
                <div class="stat-value"><?= $data['activeAdmissions'] ?? 0 ?></div>
                <div class="stat-label">BN Nội trú hiện tại</div>
            </div>
        </a>
    </div>
    <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
        <a href="index.php?page=shifts" class="text-decoration-none">
            <div class="stat-card card-warning" style="cursor:pointer;">
                <div class="stat-icon"><i class="fa-solid fa-clock"></i></div>
                <div class="stat-value" style="font-size:16px">Đăng ký Ca trực</div>
                <div class="stat-label">Quản lý lịch làm việc</div>
            </div>
        </a>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-3 mb-4" data-aos="fade-up">
    <div class="col-12">
        <div class="d-flex flex-wrap gap-2">
            <a href="index.php?page=inpatient&action=admit" class="btn btn-outline-primary" style="border-radius:20px; padding:8px 20px; font-weight:500; font-size:14px;"><i class="fa-solid fa-bed-pulse me-2"></i>Nhập viện bệnh nhân</a>
            <a href="index.php?page=inpatient&action=rooms" class="btn btn-outline-success" style="border-radius:20px; padding:8px 20px; font-weight:500; font-size:14px;"><i class="fa-solid fa-hospital me-2"></i>Sơ đồ phòng bệnh</a>
            <a href="index.php?page=inpatient" class="btn btn-outline-info" style="border-radius:20px; padding:8px 20px; font-weight:500; font-size:14px;"><i class="fa-solid fa-list-check me-2"></i>Danh sách Nội trú</a>
        </div>
    </div>
</div>

<!-- Ca trực sắp tới -->
<?php if (!empty($data['myShifts'])): ?>
<div class="content-card mb-4" data-aos="fade-up" data-aos-delay="300">
    <div class="card-header">
        <h5><i class="fa-solid fa-calendar-days me-2"></i>Ca trực sắp tới</h5>
        <a href="index.php?page=shifts" class="btn-action btn-view">Xem tất cả <i class="fa-solid fa-arrow-right ms-1"></i></a>
    </div>
    <div class="card-body">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Ca trực</th>
                        <th>Ngày</th>
                        <th>Giờ</th>
                        <th>Khoa</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $upcoming = array_filter($data['myShifts'], function($s) {
                        return strtotime($s['shift_date']) >= strtotime(date('Y-m-d'));
                    });
                    $upcoming = array_slice($upcoming, 0, 5);
                    ?>
                    <?php if (empty($upcoming)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-3">Không có ca trực sắp tới</td></tr>
                    <?php else: ?>
                    <?php foreach ($upcoming as $s): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($s['name'] ?? 'Ca trực') ?></strong></td>
                        <td><?= date('d/m/Y', strtotime($s['shift_date'])) ?></td>
                        <td>
                            <?php if (!empty($s['start_time']) && !empty($s['end_time'])): ?>
                            <?= date('H:i', strtotime($s['start_time'])) ?> - <?= date('H:i', strtotime($s['end_time'])) ?>
                            <?php else: ?>
                            —
                            <?php endif; ?>
                        </td>
                        <td><?= htmlspecialchars($s['department_name'] ?? 'N/A') ?></td>
                        <td>
                            <?php
                            $regStatus = $s['registration_status'] ?? 'pending';
                            if ($regStatus === 'approved') echo '<span class="badge bg-success">Đã duyệt</span>';
                            elseif ($regStatus === 'rejected') echo '<span class="badge bg-danger">Từ chối</span>';
                            else echo '<span class="badge bg-warning text-dark">Chờ duyệt</span>';
                            ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="content-card" data-aos="fade-up" data-aos-delay="400">
    <div class="card-header">
        <h5><i class="fa-solid fa-circle-info me-2"></i>Hướng dẫn sử dụng</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <h6 class="mb-2"><i class="fa-solid fa-list-check me-2 text-primary"></i>Công việc hàng ngày</h6>
                <ul class="list-unstyled ps-3">
                    <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i>Ghi nhận <strong>dấu hiệu sinh tồn</strong> cho BN nội trú</li>
                    <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i>Theo dõi <strong>lịch hẹn</strong> hôm nay</li>
                    <li class="mb-2"><i class="fa-solid fa-check text-success me-2"></i>Đăng ký & kiểm tra <strong>ca trực</strong></li>
                </ul>
            </div>
            <div class="col-md-6">
                <h6 class="mb-2"><i class="fa-solid fa-lightbulb me-2 text-warning"></i>Mẹo nhanh</h6>
                <ul class="list-unstyled ps-3">
                    <li class="mb-2"><i class="fa-solid fa-arrow-right text-muted me-2"></i>Vào <a href="index.php?page=inpatient">Nội trú</a> → chọn BN → Ghi nhận chăm sóc</li>
                    <li class="mb-2"><i class="fa-solid fa-arrow-right text-muted me-2"></i>Dấu hiệu sinh tồn sẽ tự động vẽ biểu đồ</li>
                </ul>
            </div>
        </div>
    </div>
</div>