<!-- Dashboard Patient - NovaCare -->

<div class="row g-4 mb-4">
    <!-- Quick Stats -->
    <div class="col-xl-4 col-md-6" data-aos="fade-up">
        <div class="stat-card card-primary" style="border-radius:16px; box-shadow:0 10px 30px rgba(14,165,233,0.1);">
            <div class="stat-icon" style="width:45px;height:45px;background:rgba(255,255,255,0.25);"><i class="fa-solid fa-calendar-check"></i></div>
            <div class="stat-value" style="font-size:24px;margin:8px 0;"><?= $data['totalAppointments'] ?? 0 ?></div>
            <div class="stat-label">Tổng Lịch hẹn</div>
        </div>
    </div>
    <div class="col-xl-4 col-md-6" data-aos="fade-up" data-aos-delay="100">
        <div class="stat-card card-success" style="border-radius:16px; box-shadow:0 10px 30px rgba(34,197,94,0.1);">
            <div class="stat-icon" style="width:45px;height:45px;background:rgba(255,255,255,0.25);"><i class="fa-solid fa-video"></i></div>
            <div class="stat-value" style="font-size:24px;margin:8px 0;"><?= $data['totalConsultations'] ?? 0 ?></div>
            <div class="stat-label">Tư vấn Online</div>
        </div>
    </div>
    <div class="col-xl-4 col-md-12" data-aos="fade-up" data-aos-delay="200">
        <div class="stat-card card-warning" style="border-radius:16px; box-shadow:0 10px 30px rgba(245,158,11,0.1);">
            <div class="stat-icon" style="width:45px;height:45px;background:rgba(255,255,255,0.25);"><i class="fa-solid fa-notes-medical"></i></div>
            <div class="stat-value" style="font-size:24px;margin:8px 0;"><?= $data['totalRecords'] ?? 0 ?></div>
            <div class="stat-label">Hồ sơ Bệnh án</div>
        </div>
    </div>
</div>

<div class="row g-3 mb-4">
    <div class="col-12" data-aos="fade-up">
        <div class="d-flex flex-wrap gap-2">
            <a href="index.php?page=appointments&action=create" class="btn btn-primary" style="border-radius:20px; padding:8px 24px; font-weight:500; font-size:14px;"><i class="fa-solid fa-calendar-plus me-2"></i>Đặt lịch khám mới</a>
            <a href="index.php?page=queue&action=myTicket" class="btn btn-outline-primary" style="border-radius:20px; padding:8px 24px; font-weight:500; font-size:14px;"><i class="fa-solid fa-ticket me-2"></i>Theo dõi số thứ tự</a>
            <a href="index.php?page=ai-assistant" class="btn btn-outline-info" style="border-radius:20px; padding:8px 24px; font-weight:500; font-size:14px;"><i class="fa-solid fa-robot me-2"></i>Trợ lý AI Diagnosis</a>
        </div>
    </div>
</div>

<!-- Widget: Số thứ tự hôm nay (Real-time) -->
<div class="row mb-4" data-aos="fade-up" id="queue-widget-row" style="display:none!important;">
    <div class="col-12">
        <div id="queue-widget" class="d-flex align-items-center justify-content-between p-3 px-4"
             style="border-radius:16px; border:none; background:linear-gradient(135deg,#0f172a,#1e3a5f); color:#fff; box-shadow:0 4px 20px rgba(14,77,146,0.3);">
            <div class="d-flex align-items-center gap-3">
                <div style="font-size:2.2rem; font-weight:900; color:#38bdf8; font-variant-numeric:tabular-nums; min-width:70px; text-align:center;" id="qdash-number">----</div>
                <div>
                    <div style="font-weight:700; font-size:15px;">Số thứ tự của bạn hôm nay</div>
                    <div id="qdash-status" style="font-size:12px; color:#94a3b8; margin-top:2px;">Đang tải...</div>
                </div>
            </div>
            <div class="d-flex align-items-center gap-3">
                <div class="text-center" id="qdash-ahead-block">
                    <div style="font-size:1.4rem; font-weight:900; color:#fbbf24;" id="qdash-ahead">-</div>
                    <div style="font-size:11px; color:#94a3b8; text-transform:uppercase; letter-spacing:1px;">Người trước</div>
                </div>
                <a href="index.php?page=queue&action=myTicket"
                   class="btn btn-sm btn-light" style="border-radius:20px; font-weight:600; font-size:13px;">
                    <i class="fa-solid fa-arrow-right me-1"></i>Chi tiết
                </a>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($data['pendingInvoices'])): ?>
<div class="row mb-4" data-aos="fade-up">
    <div class="col-12">
        <div class="alert alert-danger d-flex align-items-center mb-0" style="border-radius:16px; border:none; box-shadow:0 4px 15px rgba(220,53,69,0.1);">
            <i class="fa-solid fa-circle-exclamation fs-3 me-3"></i>
            <div class="flex-grow-1">
                <h6 class="mb-1" style="font-weight:700;">Bạn có <?= count($data['pendingInvoices']) ?> hóa đơn chưa thanh toán!</h6>
                <p class="mb-0 text-muted" style="font-size:14px;">Vui lòng kiểm tra và thanh toán để hoàn tất thủ tục khám chữa bệnh.</p>
            </div>
            <a href="index.php?page=invoices" class="btn btn-danger" style="border-radius:20px; padding:8px 24px; font-weight:500; font-size:14px;">Xem hóa đơn</a>
        </div>
    </div>
</div>
<?php endif; ?>
<?php if (!empty($data['latestPrescription'])): ?>
<div class="row mb-4" data-aos="fade-up">
    <div class="col-12">
        <div class="alert alert-info d-flex align-items-center mb-0" style="border-radius:16px; border:none; box-shadow:0 4px 15px rgba(13,202,240,0.1);">
            <i class="fa-solid fa-file-prescription fs-3 me-3"></i>
            <div class="flex-grow-1">
                <h6 class="mb-1" style="font-weight:700;">Bạn có đơn thuốc mới! (BS. <?= htmlspecialchars($data['latestPrescription']['doctor_name']) ?>)</h6>
                <p class="mb-0 text-muted" style="font-size:14px;">Được kê vào ngày <?= date('d/m/Y', strtotime($data['latestPrescription']['created_at'])) ?>. Vui lòng xem chi tiết để xem loại thuốc cần mua.</p>
            </div>
            <a href="index.php?page=prescriptions&action=view&id=<?= $data['latestPrescription']['id'] ?>" class="btn btn-info text-white" style="border-radius:20px; padding:8px 24px; font-weight:500; font-size:14px;">Xem đơn thuốc</a>
        </div>
    </div>
</div>
<?php endif; ?>

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
                            $upcoming = $data['myAppointments'] ?? [];
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
                            $consults = $data['myConsultations'] ?? [];
                            foreach($consults as $c): ?>
                                <tr>
                                    <td class="ps-4 text-success" style="font-weight:500;"><?= date('H:i - d/m/Y', strtotime($c['start_time'])) ?></td>
                                    <td><strong>BS. <?= htmlspecialchars($c['doctor_name']) ?></strong></td>
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
                        $recentRecords = $data['myMedicalRecords'] ?? [];
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
</div>

<script>
(function () {
    const STATUS_LABELS = {
        waiting:     'Đang chờ',
        called:      '🔔 Đang được gọi! Đến phòng khám ngay!',
        in_progress: '🩺 Đang khám',
        completed:   'Đã hoàn thành',
    };

    function pad(n, size = 4) {
        let s = String(n);
        while (s.length < size) s = '0' + s;
        return s;
    }

    async function pollQueueWidget() {
        try {
            const res = await fetch('index.php?page=queue&action=myTicketData');
            if (!res.ok) return;
            const data = await res.json();

            const row    = document.getElementById('queue-widget-row');
            const numEl  = document.getElementById('qdash-number');
            const statEl = document.getElementById('qdash-status');
            const ahdEl  = document.getElementById('qdash-ahead');
            const ahdBlk = document.getElementById('qdash-ahead-block');

            if (!data.ticket) {
                // No active ticket today — hide widget
                if (row) row.style.setProperty('display', 'none', 'important');
                return;
            }

            // Show widget
            if (row) row.style.removeProperty('display');

            const t = data.ticket;
            numEl.textContent  = pad(t.ticket_number);
            statEl.textContent = STATUS_LABELS[t.status] || t.status;

            // Highlight widget red/blue when called
            const widget = document.getElementById('queue-widget');
            if (t.status === 'called' || t.status === 'in_progress') {
                widget.style.background = 'linear-gradient(135deg,#0c4a6e,#0284c7)';
                widget.style.boxShadow  = '0 4px 24px rgba(56,189,248,0.45)';
                statEl.style.color = '#38bdf8';
                statEl.style.fontWeight = '700';

                // Browser Notification
                if ('Notification' in window && Notification.permission === 'granted') {
                    if (!window._queueNotifSent) {
                        window._queueNotifSent = true;
                        new Notification('🔔 NovaCare – Đến lượt của bạn!', {
                            body: `Số ${pad(t.ticket_number)} – Vui lòng đến ${t.room_name || 'phòng khám'} ngay.`,
                        });
                    }
                }
            } else {
                widget.style.background = 'linear-gradient(135deg,#0f172a,#1e3a5f)';
                widget.style.boxShadow  = '0 4px 20px rgba(14,77,146,0.3)';
                statEl.style.color = '#94a3b8';
                statEl.style.fontWeight = '400';
                window._queueNotifSent = false;
            }

            // Ahead count
            if (t.status === 'waiting') {
                ahdBlk.style.display = 'block';
                ahdEl.textContent = data.ahead_count ?? '-';
            } else {
                ahdBlk.style.display = 'none';
            }

        } catch (e) {
            console.warn('[NovaCare] Queue widget poll error:', e);
        }
    }

    // Request notification permission on page load
    if ('Notification' in window && Notification.permission === 'default') {
        Notification.requestPermission();
    }

    // Poll immediately then every 10 seconds
    pollQueueWidget();
    setInterval(pollQueueWidget, 10000);
})();
</script>
