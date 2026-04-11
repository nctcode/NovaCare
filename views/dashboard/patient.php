<!-- Dashboard Patient - NovaCare -->

<div class="row g-4 mb-4">
    <!-- Quick Stats -->
    <div class="col-xl-4 col-md-6" data-aos="fade-up">
        <div class="stat-card card-primary" style="border-radius:16px; box-shadow:0 10px 30px rgba(14,165,233,0.1);">
            <div class="stat-icon" style="width:45px;height:45px;background:rgba(255,255,255,0.25);"><i class="fa-solid fa-calendar-check"></i></div>
            <div class="stat-value" style="font-size:24px;margin:8px 0;"><?= count($data['myAppointments'] ?? []) ?></div>
            <div class="stat-label">Tổng Lịch hẹn</div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
        <div class="stat-card card-success" style="border-radius:16px; box-shadow:0 10px 30px rgba(34,197,94,0.1);">
            <div class="stat-icon" style="width:45px;height:45px;background:rgba(255,255,255,0.25);"><i class="fa-solid fa-video"></i></div>
            <div class="stat-value" style="font-size:24px;margin:8px 0;"><?= count($data['myConsultations'] ?? []) ?></div>
            <div class="stat-label">Tư vấn Online</div>
        </div>
    </div>
    <div class="col-xl-4 col-md-12" data-aos="fade-up" data-aos-delay="200">
        <div class="stat-card card-warning" style="border-radius:16px; box-shadow:0 10px 30px rgba(245,158,11,0.1);">
            <div class="stat-icon" style="width:45px;height:45px;background:rgba(255,255,255,0.25);"><i class="fa-solid fa-notes-medical"></i></div>
            <div class="stat-value" style="font-size:24px;margin:8px 0;"><?= count($data['myMedicalRecords'] ?? []) ?></div>
            <div class="stat-label">Hồ sơ Bệnh án</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-12" data-aos="fade-up">
        <div class="d-flex flex-wrap gap-2">
            <a href="index.php?page=appointments&action=create" class="btn btn-primary" style="border-radius:20px; padding:8px 24px; font-weight:500; font-size:14px;"><i class="fa-solid fa-calendar-plus me-2"></i>Đặt lịch khám mới</a>
            <a href="index.php?page=ai-assistant" class="btn btn-outline-info" style="border-radius:20px; padding:8px 24px; font-weight:500; font-size:14px;"><i class="fa-solid fa-robot me-2"></i>Trợ lý AI Diagnosis</a>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-8" data-aos="fade-up" data-aos-delay="100">
        <div class="content-card h-100" style="border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.03);">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="m-0" style="font-weight:700;"><i class="fa-solid fa-calendar me-2 text-primary"></i>Lịch trình của bạn</h6>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0" style="font-size:14px;">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Thời gian</th>
                                <th>Bác sĩ</th>
                                <th>Loại hình</th>
                                <th>Trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $upcoming = array_slice($data['myAppointments'] ?? [], 0, 4);
                            if(empty($upcoming) && empty($data['myConsultations'])): ?>
                                <tr><td colspan="4" class="text-center py-4 text-muted">Bạn chưa có lịch khám nào.</td></tr>
                            <?php else: foreach($upcoming as $apt): ?>
                                <tr>
                                    <td class="ps-4 text-primary" style="font-weight:500;"><?= date('H:i - d/m/Y', strtotime($apt['appointment_date'])) ?></td>
                                    <td><strong>BS. <?= htmlspecialchars($apt['doctor_name']) ?></strong></td>
                                    <td>
                                        <span class="badge bg-light text-dark border"><i class="fa-solid fa-building me-1"></i> Tại viện</span>
                                    </td>
                                    <td><span class="badge-status badge-<?= $apt['status'] ?>"><?= ucfirst($apt['status']) ?></span></td>
                                </tr>
                            <?php endforeach; endif; ?>
                            
                            <?php 
                            $consults = array_slice($data['myConsultations'] ?? [], 0, 2);
                            foreach($consults as $c): ?>
                                <tr>
                                    <td class="ps-4 text-success" style="font-weight:500;"><?= date('H:i - d/m/Y', strtotime($c['start_time'])) ?></td>
                                    <td><strong>BS. <?= htmlspecialchars($c['doctor_name']) ?></strong></td>
                                    <td>
                                        <span class="badge bg-success bg-opacity-10 text-success border-success border-opacity-25"><i class="fa-solid fa-video me-1"></i> Tư vấn Online</span>
                                    </td>
                                    <td>
                                        <a href="<?= htmlspecialchars($c['meeting_link']) ?>" target="_blank" class="btn btn-sm btn-success" style="border-radius:12px; font-size:11px;">Tham gia</a>
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
                <h6 class="m-0" style="font-weight:700;"><i class="fa-solid fa-file-medical me-2 text-warning"></i>Hồ sơ Bệnh án gần đây</h6>
            </div>
            <div class="card-body pt-3">
                <?php if(empty($data['myMedicalRecords'])): ?>
                    <div class="text-center pt-3 pb-4">
                        <div style="font-size:40px; color:var(--gray-300); margin-bottom:15px;"><i class="fa-solid fa-folder-open"></i></div>
                        <p class="text-muted" style="font-size:14px;">Hệ thống chưa ghi nhận hồ sơ bệnh án nào của bạn.</p>
                    </div>
                <?php else: ?>
                    <div class="timeline" style="border-left:2px solid var(--primary-light); padding-left:15px; margin-left:10px;">
                        <?php 
                        $recentRecords = array_slice($data['myMedicalRecords'], 0, 3);
                        foreach($recentRecords as $r): ?>
                        <div class="timeline-item position-relative mb-4">
                            <span class="position-absolute" style="left:-25px; top:0; width:16px; height:16px; background:var(--primary); border-radius:50%; border:3px solid white;"></span>
                            <div style="font-size:14px;"><strong><?= htmlspecialchars($r['diagnosis']) ?></strong></div>
                            <div style="font-size:12px; color:var(--gray-500); mb-1"><i class="fa-regular fa-clock me-1"></i><?= date('d/m/Y', strtotime($r['created_at'])) ?> | BS. <?= htmlspecialchars($r['doctor_name']) ?></div>
                            <a href="index.php?page=records&action=view&id=<?= $r['id'] ?>" class="text-primary text-decoration-none" style="font-size:12px;">Xem chi tiết ⟶</a>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
            <?php if(!empty($data['myMedicalRecords'])): ?>
            <div class="card-footer bg-white border-top p-3 text-center">
                <a href="index.php?page=records" class="btn btn-sm btn-outline-warning text-dark" style="border-radius:20px;">Xem toàn bộ hồ sơ</a>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>
