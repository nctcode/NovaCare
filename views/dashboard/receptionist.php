<!-- Dashboard Lễ tân (Receptionist) - NovaCare -->

<!-- Thống kê nhanh hàng chờ & lịch hẹn -->
<div class="row g-4 mb-4">
    <div class="col-xl-3 col-md-6" data-aos="fade-up">
        <div class="stat-card card-warning">
            <div class="stat-icon"><i class="fa-solid fa-hourglass-half"></i></div>
            <div class="stat-value"><?= $data['queueStats']['waiting'] ?? 0 ?></div>
            <div class="stat-label">Bệnh nhân đang chờ khám</div>
            <div class="stat-trend neutral">
                Đang gọi: <?= $data['queueStats']['called'] ?? 0 ?>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6" data-aos="fade-up" data-aos-delay="50">
        <div class="stat-card card-primary">
            <div class="stat-icon"><i class="fa-solid fa-stethoscope"></i></div>
            <div class="stat-value"><?= $data['queueStats']['in_progress'] ?? 0 ?></div>
            <div class="stat-label">Bệnh nhân đang khám</div>
            <div class="stat-trend neutral">
                Đã xong hôm nay: <?= $data['queueStats']['completed'] ?? 0 ?>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6" data-aos="fade-up" data-aos-delay="100">
        <div class="stat-card card-success">
            <div class="stat-icon"><i class="fa-solid fa-calendar-check"></i></div>
            <div class="stat-value"><?= $data['todayAppointmentsCount'] ?? 0 ?></div>
            <div class="stat-label">Lịch hẹn hôm nay</div>
            <div class="stat-trend neutral">
                Đã xác nhận: <?= $data['confirmedAppointments'] ?? 0 ?>
            </div>
        </div>
    </div>
    <div class="col-xl-3 col-md-6" data-aos="fade-up" data-aos-delay="150">
        <div class="stat-card card-info">
            <div class="stat-icon"><i class="fa-solid fa-bed-pulse"></i></div>
            <div class="stat-value"><?= $data['activeAdmissions'] ?? 0 ?></div>
            <div class="stat-label">Bệnh nhân nội trú active</div>
        </div>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-3 mb-4">
    <div class="col-12" data-aos="fade-up">
        <div class="content-card">
            <div class="card-header">
                <h5 class="mb-0"><i class="fa-solid fa-bolt me-2 text-primary"></i>Thao tác nhanh Lễ tân</h5>
            </div>
            <div class="card-body">
                <div class="d-flex flex-wrap gap-2">
                    <a href="index.php?page=queue&action=checkin" class="btn btn-primary rounded-pill px-4">
                        <i class="fa-solid fa-ticket me-2"></i>Cấp số thứ tự mới
                    </a>
                    <a href="index.php?page=patients&action=create" class="btn btn-outline-primary rounded-pill px-4">
                        <i class="fa-solid fa-user-plus me-2"></i>Thêm Bệnh nhân mới
                    </a>
                    <a href="index.php?page=appointments&action=create" class="btn btn-outline-success rounded-pill px-4">
                        <i class="fa-solid fa-calendar-plus me-2"></i>Đặt lịch hẹn mới
                    </a>
                    <a href="index.php?page=queue" class="btn btn-outline-info rounded-pill px-4">
                        <i class="fa-solid fa-list-ol me-2"></i>Xem hàng chờ hôm nay
                    </a>
                    <a href="index.php?page=queue&action=display" target="_blank" class="btn btn-outline-secondary rounded-pill px-4">
                        <i class="fa-solid fa-desktop me-2"></i>Mở màn hình Gọi số (TV)
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Top Bệnh nhân đang chờ khám -->
    <div class="col-lg-6" data-aos="fade-up">
        <div class="content-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fa-solid fa-clock-rotate-left me-2 text-warning"></i>Bệnh nhân chờ gọi tiếp theo</h5>
                <a href="index.php?page=queue" class="text-sm text-decoration-none">Xem tất cả hàng chờ</a>
            </div>
            <div class="card-body">
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th style="width:70px;">Số TT</th>
                                <th>Bệnh nhân</th>
                                <th>Khoa / Phòng</th>
                                <th>Ưu tiên</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($data['waitingTickets'])): ?>
                                <tr>
                                    <td colspan="4" class="text-center text-muted py-4">Không có bệnh nhân nào đang chờ</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($data['waitingTickets'] as $t): ?>
                                    <tr>
                                        <td>
                                            <span class="fw-bold text-primary fs-6">
                                                <?= str_pad($t['ticket_number'], 4, '0', STR_PAD_LEFT) ?>
                                            </span>
                                        </td>
                                        <td>
                                            <strong><?= htmlspecialchars($t['patient_name']) ?></strong>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($t['department_name'] ?? 'Đa khoa') ?>
                                            <?php if ($t['room_name']): ?>
                                                <br><small class="text-muted"><i class="fa-solid fa-door-open me-1"></i><?= htmlspecialchars($t['room_name']) ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($t['priority'] === 'emergency'): ?>
                                                <span class="badge bg-danger">Cấp cứu</span>
                                            <?php elseif ($t['priority'] === 'priority'): ?>
                                                <span class="badge bg-warning text-dark">Ưu tiên</span>
                                            <?php else: ?>
                                                <span class="badge bg-light text-dark border">Thường</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Lịch hẹn hôm nay -->
    <div class="col-lg-6" data-aos="fade-up" data-aos-delay="100">
        <div class="content-card h-100">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fa-solid fa-calendar-day me-2 text-success"></i>Lịch hẹn hôm nay</h5>
                <a href="index.php?page=appointments" class="text-sm text-decoration-none">Xem tất cả lịch</a>
            </div>
            <div class="card-body">
                <div class="table-wrapper">
                    <table class="data-table">
                        <thead>
                            <tr>
                                <th>Bệnh nhân</th>
                                <th>Giờ hẹn</th>
                                <th>Bác sĩ khám</th>
                                <th>Trạng thái</th>
                                <th style="width: 100px">Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if (empty($data['todayAppointments'])): ?>
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">Hôm nay chưa có lịch hẹn nào</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach (array_slice($data['todayAppointments'], 0, 5) as $a): ?>
                                    <tr>
                                        <td>
                                            <strong><?= htmlspecialchars($a['patient_name'] ?? 'Bệnh nhân') ?></strong>
                                        </td>
                                        <td>
                                            <?= date('H:i', strtotime($a['appointment_date'])) ?>
                                        </td>
                                        <td>
                                            <?= htmlspecialchars($a['doctor_name'] ?? 'Chung') ?>
                                        </td>
                                        <td>
                                            <?php if ($a['status'] === 'confirmed'): ?>
                                                <span class="badge bg-success">Đã xác nhận</span>
                                            <?php elseif ($a['status'] === 'pending'): ?>
                                                <span class="badge bg-warning text-dark">Chờ duyệt</span>
                                            <?php else: ?>
                                                <span class="badge bg-secondary"><?= htmlspecialchars($a['status']) ?></span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($a['status'] === 'confirmed'): ?>
                                                <a href="index.php?page=queue&action=checkin&appointment_id=<?= $a['id'] ?>" class="btn btn-xs btn-primary py-1 px-2" style="font-size: 11px;">
                                                    <i class="fa-solid fa-ticket me-1"></i>Cấp số
                                                </a>
                                            <?php else: ?>
                                                <span class="text-muted text-xs">-</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
