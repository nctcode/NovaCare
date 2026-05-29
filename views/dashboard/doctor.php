<!-- Dashboard Doctor - NovaCare Smart Hospital 4.0 -->

<!-- Welcome and Doctor Specialty Banner -->
<div class="row g-4 mb-4" data-aos="fade-down">
    <div class="col-12">
        <div class="p-4 shadow-sm border-0 position-relative overflow-hidden" 
             style="border-radius: 16px; background: linear-gradient(135deg, #0f766e, #115e59); color: #ffffff;">
            <!-- Decorative light circles -->
            <div class="position-absolute" style="width: 250px; height: 250px; border-radius: 50%; background: rgba(20, 184, 166, 0.15); top: -100px; right: -50px; filter: blur(30px); pointer-events: none;"></div>
            <div class="position-absolute" style="width: 120px; height: 120px; border-radius: 50%; background: rgba(13, 148, 136, 0.2); bottom: -40px; right: 80px; filter: blur(20px); pointer-events: none;"></div>
            
            <div class="row align-items-center position-relative">
                <div class="col-lg-8">
                    <div class="d-flex align-items-center gap-2 mb-2">
                        <span class="badge bg-teal-subtle text-teal border border-teal-subtle px-2.5 py-1" style="font-size: 11px; border-radius: 8px; background: rgba(20, 184, 166, 0.2) !important; color: #2dd4bf !important;">
                            <i class="fa-solid fa-user-doctor me-1"></i> PHÂN HỆ BÁC SĨ
                        </span>
                        <span class="badge bg-light bg-opacity-25 text-white px-2.5 py-1" style="font-size: 11px; border-radius: 8px;">
                            <i class="fa-solid fa-stethoscope me-1"></i> Chuyên khoa: <?= htmlspecialchars($data['doctorInfo']['specialty'] ?? 'Đang cập nhật') ?>
                        </span>
                    </div>
                    <h3 class="fw-bold mb-2">Bác sĩ: <?= htmlspecialchars($user['name']) ?></h3>
                    <p class="text-white-50 mb-0" style="font-size: 14px; max-width: 650px;">
                        Chào mừng bạn trở lại ca làm việc. Hệ thống thông tin lâm sàng đã đồng bộ hóa lịch hẹn khám của bệnh nhân, bệnh án điện tử và các cuộc tư vấn video trực tuyến.
                    </p>
                </div>
                
                <div class="col-lg-4 mt-3 mt-lg-0">
                    <div class="p-3 border border-secondary border-opacity-25 shadow-inner" style="border-radius: 12px; background: rgba(255, 255, 255, 0.05); backdrop-filter: blur(5px);">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <span class="text-white-50" style="font-size: 12.5px;">Khoa công tác</span>
                        </div>
                        <h4 class="fw-bold mb-0 text-white" style="font-size: 18px;">
                            <i class="fa-solid fa-hospital text-teal-400 me-2" style="color: #2dd4bf;"></i>
                            <?= htmlspecialchars($data['doctorInfo']['department_name'] ?? 'Khoa Lâm Sàng') ?>
                        </h4>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Doctor Metrics Statistics with Counters -->
<?php
$todayAppointmentsCount = count(array_filter($data['myAppointments'] ?? [], fn($a) => date('Y-m-d', strtotime($a['appointment_date'])) === date('Y-m-d')));
$onlineConsultationsCount = count($data['myConsultations'] ?? []);
$weeklyShiftsCount = count($data['myShifts'] ?? []);
?>
<div class="row g-3 mb-4">
    <!-- Today's Appointments -->
    <div class="col-md-4">
        <div class="stat-card p-3 shadow-sm border-0 d-flex flex-column h-100" style="border-radius: 14px; background: #ffffff;">
            <div class="d-flex justify-content-between align-items-center mb-2.5">
                <span class="text-muted fw-semibold" style="font-size: 13px;">Lịch khám hôm nay</span>
                <div class="d-flex align-items-center justify-content-center text-primary" style="width: 34px; height: 34px; border-radius: 8px; background: rgba(59, 130, 246, 0.08); font-size: 16px;">
                    <i class="fa-solid fa-calendar-day"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1 text-dark counter" data-target="<?= $todayAppointmentsCount ?>">0</h3>
            <div class="mt-auto" style="font-size: 11px; color: #16a34a; font-weight: 500;">
                <i class="fa-solid fa-clock-rotate-left"></i> Đã lên lịch tiếp đón bệnh nhân
            </div>
        </div>
    </div>

    <!-- Online Consultations -->
    <div class="col-md-4">
        <div class="stat-card p-3 shadow-sm border-0 d-flex flex-column h-100" style="border-radius: 14px; background: #ffffff;">
            <div class="d-flex justify-content-between align-items-center mb-2.5">
                <span class="text-muted fw-semibold" style="font-size: 13px;">Tư vấn trực tuyến (Online)</span>
                <div class="d-flex align-items-center justify-content-center text-success" style="width: 34px; height: 34px; border-radius: 8px; background: rgba(34, 197, 94, 0.08); font-size: 16px;">
                    <i class="fa-solid fa-video"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1 text-dark counter" data-target="<?= $onlineConsultationsCount ?>">0</h3>
            <div class="mt-auto" style="font-size: 11px; color: #64748b; font-weight: 500;">
                <i class="fa-solid fa-circle-check text-success"></i> Sẵn sàng kết nối phòng hội chẩn
            </div>
        </div>
    </div>

    <!-- Weekly Shifts -->
    <div class="col-md-4">
        <div class="stat-card p-3 shadow-sm border-0 d-flex flex-column h-100" style="border-radius: 14px; background: #ffffff;">
            <div class="d-flex justify-content-between align-items-center mb-2.5">
                <span class="text-muted fw-semibold" style="font-size: 13px;">Ca trực tuần này</span>
                <div class="d-flex align-items-center justify-content-center text-info" style="width: 34px; height: 34px; border-radius: 8px; background: rgba(14, 165, 233, 0.08); font-size: 16px;">
                    <i class="fa-solid fa-clock"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1 text-dark counter" data-target="<?= $weeklyShiftsCount ?>">0</h3>
            <div class="mt-auto" style="font-size: 11px; color: #64748b; font-weight: 500;">
                <i class="fa-solid fa-calendar-check"></i> Đã được phê duyệt lịch
            </div>
        </div>
    </div>
</div>

<!-- Clinician Quick Actions Command Center -->
<div class="row g-3 mb-4" data-aos="fade-up">
    <div class="col-12">
        <div class="content-card p-4 border-0 shadow-sm" style="border-radius: 16px; background: #ffffff;">
            <h6 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2">
                <i class="fa-solid fa-briefcase-medical text-teal"></i>
                Trung tâm thao tác lâm sàng nhanh (Clinician Command)
            </h6>
            <div class="d-flex flex-wrap gap-2">
                <a href="index.php?page=patients" class="btn btn-outline-primary px-3 py-2" style="border-radius: 10px; font-weight: 600; font-size: 13.5px;">
                    <i class="fa-solid fa-users me-1.5"></i> Tiếp đón & Xem Bệnh nhân
                </a>
                <a href="index.php?page=prescriptions&action=create" class="btn btn-outline-success px-3 py-2" style="border-radius: 10px; font-weight: 600; font-size: 13.5px;">
                    <i class="fa-solid fa-file-prescription me-1.5"></i> Kê đơn thuốc điện tử
                </a>
                <a href="index.php?page=records" class="btn btn-outline-info" style="border-radius: 10px; padding: 8px 16px; font-weight: 600; font-size: 13.5px;">
                    <i class="fa-solid fa-book-medical me-1.5"></i> Tra cứu Bệnh án
                </a>
                <a href="index.php?page=lab-orders&action=create" class="btn btn-outline-warning" style="border-radius: 10px; padding: 8px 16px; font-weight: 600; font-size: 13.5px;">
                    <i class="fa-solid fa-flask-vial me-1.5"></i> Chỉ định Cận lâm sàng
                </a>
                <a href="index.php?page=ai-assistant" class="btn btn-primary px-4 py-2 text-white" style="border-radius: 10px; font-weight: 600; font-size: 13.5px; background: #0d9488 !important; border-color: #0d9488 !important;">
                    <i class="fa-solid fa-brain me-1.5"></i> Trợ lý Chẩn đoán AI
                </a>
            </div>
        </div>
    </div>
</div>

<!-- Main Doctor Schedule Panel -->
<div class="row g-4 mb-4">
    <!-- Next Appointments & Telehealth Room -->
    <div class="col-lg-8" data-aos="fade-up" data-aos-delay="100">
        <div class="content-card h-100 p-4 border-0 shadow-sm bg-white" style="border-radius: 16px;">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h6 class="m-0 fw-bold text-dark"><i class="fa-solid fa-calendar-check me-2 text-primary"></i>Lịch hẹn khám lâm sàng & Tư vấn sắp tới</h6>
                <a href="index.php?page=appointments" class="btn btn-sm btn-outline-primary px-3" style="border-radius: 8px; font-weight: 600; font-size: 12.5px;">Xem tất cả</a>
            </div>
            
            <div class="table-wrapper">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Thời gian</th>
                            <th>Bệnh nhân</th>
                            <th>Phân loại hình thức</th>
                            <th>Trạng thái/Thao tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                        $upcoming = array_slice($data['myAppointments'] ?? [], 0, 5);
                        $consults = array_slice($data['myConsultations'] ?? [], 0, 3);
                        
                        if(empty($upcoming) && empty($consults)): ?>
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-calendar-xmark d-block mb-2" style="font-size: 24px;"></i>
                                    Không có lịch hẹn khám sắp tới trong ngày.
                                </td>
                            </tr>
                        <?php else: ?>
                            <!-- Direct Consultation rows -->
                            <?php foreach($upcoming as $apt): ?>
                            <tr>
                                <td>
                                    <span class="text-primary fw-semibold" style="font-size: 13.5px;">
                                        <i class="fa-regular fa-clock me-1"></i>
                                        <?= date('H:i - d/m/Y', strtotime($apt['appointment_date'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <strong class="text-dark"><?= htmlspecialchars($apt['patient_name']) ?></strong>
                                </td>
                                <td>
                                    <span class="badge bg-light text-dark border px-2 py-1" style="font-size: 11.5px; border-radius: 6px;">
                                        <i class="fa-solid fa-hospital me-1 text-primary"></i> Khám trực tiếp
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $aptStatusClass = 'bg-secondary-subtle text-secondary';
                                    if ($apt['status'] === 'confirmed') $aptStatusClass = 'bg-primary-subtle text-primary border border-primary-subtle';
                                    elseif ($apt['status'] === 'completed') $aptStatusClass = 'bg-success-subtle text-success border border-success-subtle';
                                    elseif ($apt['status'] === 'cancelled') $aptStatusClass = 'bg-danger-subtle text-danger border border-danger-subtle';
                                    ?>
                                    <span class="badge text-uppercase <?= $aptStatusClass ?>" style="font-size: 9.5px; font-weight: 600; padding: 4px 6px; border-radius: 4px;"><?= ucfirst($apt['status']) ?></span>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                            
                            <!-- Telehealth/Online Consultation rows -->
                            <?php foreach($consults as $c): ?>
                            <tr>
                                <td>
                                    <span class="text-success fw-semibold" style="font-size: 13.5px;">
                                        <i class="fa-regular fa-clock me-1"></i>
                                        <?= date('H:i - d/m/Y', strtotime($c['start_time'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <strong class="text-dark"><?= htmlspecialchars($c['patient_name']) ?></strong>
                                </td>
                                <td>
                                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size: 11.5px; border-radius: 6px; background-color: rgba(34,197,94,0.08) !important;">
                                        <i class="fa-solid fa-video me-1"></i> Tư vấn Online
                                    </span>
                                </td>
                                <td>
                                    <a href="index.php?page=consultations&action=room&id=<?= htmlspecialchars($c['meeting_id']) ?>" class="btn btn-sm btn-success px-3.5 py-1" style="border-radius: 6px; font-size: 11.5px; font-weight: 600; background-color: #16a34a !important; border-color: #16a34a !important;">
                                        <i class="fa-solid fa-headset me-1"></i> Kết nối video
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    
    <!-- My Shift Schedule list -->
    <div class="col-lg-4" data-aos="fade-up" data-aos-delay="200">
        <div class="content-card h-100 p-4 border-0 shadow-sm bg-white d-flex flex-column" style="border-radius: 16px;">
            <div class="card-header bg-white border-0 p-0 mb-3">
                <h6 class="m-0 fw-bold text-dark"><i class="fa-solid fa-business-time me-2 text-info"></i>Lịch trực của tôi</h6>
            </div>
            
            <div class="flex-grow-1 overflow-y-auto px-1" style="max-height: 260px; scrollbar-width: thin;">
                <?php if(empty($data['myShifts'])): ?>
                    <div class="empty-state py-4 text-center text-muted">
                        <i class="fa-solid fa-calendar-minus d-block mb-2" style="font-size: 20px;"></i>
                        <p class="small text-muted mb-0">Bạn chưa được xếp lịch ca trực nào trong tuần này.</p>
                    </div>
                <?php else: ?>
                    <ul class="list-group list-group-flush">
                        <?php foreach(array_slice($data['myShifts'], 0, 5) as $shift): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0 py-3 border-bottom">
                            <div>
                                <div class="text-dark fw-bold" style="font-size: 13.5px;"><?= date('l, d/m/Y', strtotime($shift['shift_date'])) ?></div>
                                <small class="text-muted" style="font-size: 11px;"><i class="fa-solid fa-circle-check text-success me-1"></i>Đã phê duyệt ca trực</small>
                            </div>
                            <?php
                            $shiftBadgeColor = 'background-color: rgba(59, 130, 246, 0.1); color: #3b82f6; border: 1px solid rgba(59, 130, 246, 0.2);';
                            if ($shift['shift_type'] === 'night') {
                                $shiftBadgeColor = 'background-color: rgba(99, 102, 241, 0.1); color: #6366f1; border: 1px solid rgba(99, 102, 241, 0.2);';
                            }
                            ?>
                            <span class="badge" style="font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 6px; <?= $shiftBadgeColor ?>">
                                <?= $shift['shift_type'] === 'day' ? '☀️ Ca ngày' : '🌙 Ca đêm' ?>
                            </span>
                        </li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </div>
            
            <div class="text-center mt-3 pt-3 border-top">
                <a href="index.php?page=shifts" class="btn btn-sm btn-outline-info w-100 py-2" style="border-radius: 8px; font-weight: 600; font-size: 12.5px;">
                    <i class="fa-solid fa-calendar-days me-1"></i> Xem lịch & Đăng ký thêm ca trực
                </a>
            </div>
        </div>
    </div>
</div>
