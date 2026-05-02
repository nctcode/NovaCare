<!-- Dashboard Doctor - NovaCare -->

<div class="row g-4 mb-4">
    <!-- Quick Stats -->
    <div class="col-xl-4 col-md-6" data-aos="fade-up">
        <div class="stat-card card-primary" style="border-radius:16px; box-shadow:0 10px 30px rgba(14,165,233,0.1);">
            <div class="stat-icon" style="width:45px;height:45px;background:rgba(255,255,255,0.25);"><i class="fa-solid fa-calendar-day"></i></div>
            <div class="stat-value" style="font-size:24px;margin:8px 0;">
                <?= count(array_filter($data['myAppointments'] ?? [], fn($a) => date('Y-m-d', strtotime($a['appointment_date'])) === date('Y-m-d'))) ?>
            </div>
            <div class="stat-label">Lịch khám hôm nay</div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
        <div class="stat-card card-success" style="border-radius:16px; box-shadow:0 10px 30px rgba(34,197,94,0.1);">
            <div class="stat-icon" style="width:45px;height:45px;background:rgba(255,255,255,0.25);"><i class="fa-solid fa-video"></i></div>
            <div class="stat-value" style="font-size:24px;margin:8px 0;"><?= count($data['myConsultations'] ?? []) ?></div>
            <div class="stat-label">Tư vấn trực tuyến</div>
        </div>
    </div>
    <div class="col-xl-4 col-md-12" data-aos="fade-up" data-aos-delay="200">
        <div class="stat-card card-info" style="border-radius:16px; box-shadow:0 10px 30px rgba(6,182,212,0.1);">
            <div class="stat-icon" style="width:45px;height:45px;background:rgba(255,255,255,0.25);"><i class="fa-solid fa-clock"></i></div>
            <div class="stat-value" style="font-size:24px;margin:8px 0;"><?= count($data['myShifts'] ?? []) ?></div>
            <div class="stat-label">Ca trực tuần này</div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8" data-aos="fade-up" data-aos-delay="100">
        <div class="content-card h-100" style="border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.03);">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="m-0" style="font-weight:700;"><i class="fa-solid fa-list-check me-2 text-primary"></i>Lịch khám & Tư vấn sắp tới</h6>
                <a href="index.php?page=appointments" class="btn btn-sm btn-outline-primary" style="border-radius:20px; font-size:12px;">Xem tất cả</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size:14px;">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Thời gian</th>
                                <th>Bệnh nhân</th>
                                <th>Loại hình</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $upcoming = array_slice($data['myAppointments'] ?? [], 0, 5);
                            if(empty($upcoming)): ?>
                                <tr><td colspan="4" class="text-center py-4 text-muted">Không có lịch khám nào sắp tới.</td></tr>
                            <?php else: foreach($upcoming as $apt): ?>
                                <tr>
                                    <td class="ps-4 text-primary" style="font-weight:500;"><?= date('H:i - d/m/Y', strtotime($apt['appointment_date'])) ?></td>
                                    <td><strong><?= htmlspecialchars($apt['patient_name']) ?></strong></td>
                                    <td>
                                        <span class="badge bg-light text-dark border"><i class="fa-solid fa-building me-1"></i> Khám trực tiếp</span>
                                    </td>
                                    <td><span class="badge-status badge-<?= $apt['status'] ?>"><?= ucfirst($apt['status']) ?></span></td>
                                </tr>
                            <?php endforeach; endif; ?>
                            
                            <?php 
                            $consults = array_slice($data['myConsultations'] ?? [], 0, 3);
                            foreach($consults as $c): ?>
                                <tr>
                                    <td class="ps-4 text-success" style="font-weight:500;"><?= date('H:i - d/m/Y', strtotime($c['start_time'])) ?></td>
                                    <td><strong><?= htmlspecialchars($c['patient_name']) ?></strong></td>
                                    <td>
                                        <span class="badge bg-success bg-opacity-10 text-success border-success border-opacity-25"><i class="fa-solid fa-video me-1"></i> Tư vấn Online</span>
                                    </td>
                                    <td>
                                        <a href="index.php?page=consultations&action=room&id=<?= htmlspecialchars($c['meeting_id']) ?>" class="btn btn-sm btn-success" style="border-radius:12px; font-size:11px;">Tham gia</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
        <div class="content-card h-100" style="border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.03);">
            <div class="card-header">
                <h6 class="m-0" style="font-weight:700;"><i class="fa-solid fa-business-time me-2 text-info"></i>Lịch trực của tôi</h6>
            </div>
            <div class="card-body">
                <?php if(empty($data['myShifts'])): ?>
                    <p class="text-muted text-center pt-3">Bạn chưa đăng ký ca trực nào.</p>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach(array_slice($data['myShifts'], 0, 5) as $shift): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-3">
                            <div>
                                <div style="font-weight:600; color:var(--dark);"><?= date('l, d/m/Y', strtotime($shift['shift_date'])) ?></div>
                                <div style="font-size:12px;" class="text-muted"><i class="fa-regular fa-calendar me-1"></i>Đã xác nhận</div>
                            </div>
                            <span class="badge-status badge-<?= $shift['shift_type'] ?>" style="padding:6px 12px; font-size:12px;">
                                <?= $shift['shift_type'] === 'day' ? '☀️ Ca ngày' : '🌙 Ca đêm' ?>
                            </span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                    <div class="text-center mt-3">
                        <a href="index.php?page=shifts" class="btn btn-sm btn-outline-info" style="border-radius:20px;">Xem / Đăng ký thêm</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
