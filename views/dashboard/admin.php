<!-- Dashboard Admin - NovaCare -->

<!-- Tóm tắt thống kê with Trend Indicators -->
<div class="row g-4 mb-4">
    <div class="col-xl-2 col-md-4 col-sm-6" data-aos="fade-up">
        <div class="stat-card card-primary">
            <div class="stat-icon"><i class="fa-solid fa-hospital-user"></i></div>
            <div class="stat-value"><?= $data['totalPatients'] ?? 0 ?></div>
            <div class="stat-label">Tổng bệnh nhân</div>
            <div class="stat-trend up"><i class="fa-solid fa-arrow-trend-up"></i> +5% so tháng trước</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6" data-aos="fade-up" data-aos-delay="50">
        <div class="stat-card card-success">
            <div class="stat-icon"><i class="fa-solid fa-user-doctor"></i></div>
            <div class="stat-value"><?= $data['totalDoctors'] ?? 0 ?></div>
            <div class="stat-label">Tổng Bác sĩ</div>
            <div class="stat-trend neutral"><i class="fa-solid fa-minus"></i> Ổn định</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6" data-aos="fade-up" data-aos-delay="100">
        <div class="stat-card card-info">
            <div class="stat-icon"><i class="fa-solid fa-user-nurse"></i></div>
            <div class="stat-value"><?= $data['totalNurses'] ?? 0 ?></div>
            <div class="stat-label">Tổng Y tá</div>
            <div class="stat-trend neutral"><i class="fa-solid fa-minus"></i> Ổn định</div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6" data-aos="fade-up" data-aos-delay="150">
        <div class="stat-card card-warning">
            <div class="stat-icon"><i class="fa-solid fa-calendar-check"></i></div>
            <div class="stat-value"><?= $data['totalAppointments'] ?? 0 ?></div>
            <div class="stat-label">Lịch hẹn</div>
            <?php $pendingPct = ($data['totalAppointments'] ?? 0) > 0 ? round(($data['pendingAppointments'] ?? 0) / $data['totalAppointments'] * 100) : 0; ?>
            <div class="stat-trend <?= $pendingPct > 30 ? 'down' : 'up' ?>">
                <i class="fa-solid fa-<?= $pendingPct > 30 ? 'arrow-trend-down' : 'arrow-trend-up' ?>"></i>
                <?= $pendingPct ?>% đang chờ
            </div>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6" data-aos="fade-up" data-aos-delay="200">
        <div class="stat-card card-danger">
            <div class="stat-icon"><i class="fa-solid fa-capsules"></i></div>
            <div class="stat-value"><?= $data['totalMedicines'] ?? 0 ?></div>
            <div class="stat-label">Loại thuốc (Kho)</div>
            <?php $lowCount = count($lowStockMedicines ?? []); ?>
            <?php if ($lowCount > 0): ?>
            <div class="stat-trend down"><i class="fa-solid fa-triangle-exclamation"></i> <?= $lowCount ?> loại sắp hết</div>
            <?php else: ?>
            <div class="stat-trend up"><i class="fa-solid fa-check"></i> Tồn kho ổn</div>
            <?php endif; ?>
        </div>
    </div>
    <div class="col-xl-2 col-md-4 col-sm-6" data-aos="fade-up" data-aos-delay="250">
        <div class="stat-card card-secondary">
            <div class="stat-icon"><i class="fa-solid fa-toolbox"></i></div>
            <div class="stat-value"><?= $data['totalEquipment'] ?? 0 ?></div>
            <div class="stat-label">Trang thiết bị</div>
            <div class="stat-trend neutral"><i class="fa-solid fa-minus"></i> Ổn định</div>
        </div>
    </div>
</div>

<!-- Quick Actions (Admin = Quản trị hệ thống) -->
<div class="row g-3 mb-4">
    <div class="col-12" data-aos="fade-up">
        <div class="d-flex flex-wrap gap-2">
            <a href="index.php?page=doctors&action=create" class="btn btn-outline-primary" style="border-radius:20px; padding:8px 20px; font-weight:500; font-size:14px;"><i class="fa-solid fa-user-doctor me-2"></i>Thêm Bác sĩ</a>
            <a href="index.php?page=nurses&action=create" class="btn btn-outline-success" style="border-radius:20px; padding:8px 20px; font-weight:500; font-size:14px;"><i class="fa-solid fa-user-nurse me-2"></i>Thêm Y tá</a>
            <a href="index.php?page=departments&action=create" class="btn btn-outline-info" style="border-radius:20px; padding:8px 20px; font-weight:500; font-size:14px;"><i class="fa-solid fa-building-columns me-2"></i>Tạo Khoa mới</a>
            <a href="index.php?page=shifts&action=create" class="btn btn-outline-warning" style="border-radius:20px; padding:8px 20px; font-weight:500; font-size:14px;"><i class="fa-solid fa-clock me-2"></i>Tạo Ca trực</a>
        </div>
    </div>
</div>

<!-- Charts & Activity Feed -->
<div class="row g-4 mb-4">
    <div class="col-lg-8" data-aos="fade-up" data-aos-delay="100">
        <div class="chart-card" style="border-radius:16px;">
            <h6 class="mb-4" style="font-weight:700;"><i class="fa-solid fa-chart-column me-2 text-primary"></i>Biểu đồ Lịch hẹn & Bệnh nhân</h6>
            <div class="row">
                <div class="col-md-6">
                    <canvas id="appointmentChart" height="220"></canvas>
                </div>
                <div class="col-md-6">
                    <canvas id="patientChart" height="220"></canvas>
                </div>
            </div>
        </div>
    </div>

    <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
        <div class="content-card h-100" style="border-radius:16px; max-height:380px; overflow-y:auto;">
            <div class="card-header bg-white border-0 pt-4 pb-0">
                <h6 style="font-weight:700;"><i class="fa-solid fa-bolt me-2 text-warning"></i>Hoạt động gần đây</h6>
            </div>
            <div class="card-body">
                <div class="timeline" style="border-left:2px solid var(--gray-200); padding-left:20px; margin-left:10px;">
                    <?php if(!empty($recentPatients)): ?>
                        <?php foreach($recentPatients as $rp): ?>
                        <div class="timeline-item position-relative mb-4">
                            <span class="position-absolute" style="left:-29px; top:0; width:16px; height:16px; background:var(--primary); border-radius:50%; border:3px solid white;"></span>
                            <div style="font-size:14px;"><strong><?= htmlspecialchars($rp['name']) ?></strong> vừa đăng ký hồ sơ bệnh nhân.</div>
                            <div style="font-size:12px; color:var(--gray-500);"><i class="fa-regular fa-clock me-1"></i><?= date('d/m/Y H:i', strtotime($rp['created_at'])) ?></div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if(!empty($recentAppointments)): ?>
                        <?php foreach($recentAppointments as $ra): ?>
                        <div class="timeline-item position-relative mb-4">
                            <span class="position-absolute" style="left:-29px; top:0; width:16px; height:16px; background:var(--success); border-radius:50%; border:3px solid white;"></span>
                            <div style="font-size:14px;">Bệnh nhân <strong><?= htmlspecialchars($ra['patient_name']) ?></strong> đăng ký khám
                                <span class="badge-status badge-<?= $ra['status'] ?>" style="font-size:10px; padding:2px 8px;"><?= ucfirst($ra['status']) ?></span>
                            </div>
                            <div style="font-size:12px; color:var(--gray-500);"><i class="fa-regular fa-calendar me-1"></i><?= date('d/m/Y', strtotime($ra['appointment_date'])) ?></div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if(!empty($lowStockMedicines)): ?>
                        <?php foreach($lowStockMedicines as $lsm): ?>
                        <div class="timeline-item position-relative mb-4">
                            <span class="position-absolute d-flex align-items-center justify-content-center" style="left:-29px; top:0; width:16px; height:16px; background:var(--danger); border-radius:50%; border:3px solid white;"></span>
                            <div style="font-size:14px; font-weight:500;" class="text-danger">Cảnh báo tồn kho: <?= htmlspecialchars($lsm['name']) ?></div>
                            <div style="font-size:12px; color:var(--danger);"><i class="fa-solid fa-triangle-exclamation me-1"></i>Chỉ còn <?= $lsm['quantity'] ?> đơn vị</div>
                        </div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Today's Appointments Widget -->
<?php
$todayAppts = [];
if (!empty($recentAppointments)) {
    $today = date('Y-m-d');
    foreach ($recentAppointments as $ra) {
        if (isset($ra['appointment_date']) && date('Y-m-d', strtotime($ra['appointment_date'])) === $today) {
            $todayAppts[] = $ra;
        }
    }
}
?>
<div class="row g-4 mb-4">
    <div class="col-12" data-aos="fade-up">
        <div class="content-card">
            <div class="card-header">
                <h5 style="font-weight:700; margin:0;"><i class="fa-regular fa-calendar-day me-2 text-primary"></i>Lịch hẹn hôm nay
                    <span class="badge-status badge-primary ms-2" style="background:var(--primary-light); color:var(--primary); font-size:12px;"><?= date('d/m/Y') ?></span>
                </h5>
                <a href="index.php?page=appointments" class="btn btn-sm btn-outline-primary" style="border-radius:20px; font-size:13px;">Xem tất cả</a>
            </div>
            <div class="card-body today-appointments-widget">
                <?php if (empty($todayAppts)): ?>
                    <div class="empty-state" style="padding: 24px;">
                        <div class="empty-icon"><i class="fa-regular fa-calendar-check"></i></div>
                        <h6>Không có lịch hẹn hôm nay</h6>
                        <p>Chưa có cuộc hẹn nào được lên lịch cho ngày <?= date('d/m/Y') ?>.</p>
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach (array_slice($todayAppts, 0, 6) as $ta): ?>
                        <div class="col-md-4">
                            <div class="appointment-row">
                                <div class="appt-time"><?= date('H:i', strtotime($ta['appointment_date'])) ?></div>
                                <div class="appt-info">
                                    <div class="appt-name"><?= htmlspecialchars($ta['patient_name'] ?? 'Bệnh nhân') ?></div>
                                    <div class="appt-doctor"><i class="fa-solid fa-user-doctor me-1"></i><?= htmlspecialchars($ta['doctor_name'] ?? '') ?></div>
                                </div>
                                <span class="badge-status badge-<?= $ta['status'] ?>" style="font-size:10px; padding:2px 8px; flex-shrink:0;"><?= ucfirst($ta['status']) ?></span>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Chart init script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const isDark = document.documentElement.getAttribute('data-theme') === 'dark';
    const tickColor = isDark ? '#94a3b8' : '#64748b';
    const gridColor = isDark ? 'rgba(255,255,255,0.05)' : 'rgba(0,0,0,0.04)';

    const c1 = initAppointmentChart(
        <?= $data['pendingAppointments'] ?? 0 ?>,
        <?= $data['confirmedAppointments'] ?? 0 ?>,
        <?= $data['completedAppointments'] ?? 0 ?>,
        <?= $data['cancelledAppointments'] ?? 0 ?>
    );

    const c2 = initPatientChart(
        ['T1','T2','T3','T4','T5','T6','T7','T8','T9','T10','T11','T12'],
        [24,30,18,25,32,28,45,40,<?= $data['totalPatients'] ?? 50 ?>,0,0,0]
    );

    // Register charts globally for dark mode toggling
    window._ncCharts = [c1, c2];
});
</script>
