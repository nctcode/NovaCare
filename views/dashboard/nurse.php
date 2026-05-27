<!-- Dashboard Điều dưỡng / Y tá -->
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
