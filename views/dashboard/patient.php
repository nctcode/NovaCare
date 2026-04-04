<!-- Dashboard Patient -->
<div class="row g-4 mb-4">
    <div class="col-md-4" data-aos="fade-up">
        <div class="stat-card card-primary">
            <div class="stat-icon"><i class="fa-solid fa-user"></i></div>
            <div class="stat-label">Xin chào, <?= htmlspecialchars($user['name']) ?></div>
            <div class="stat-value mt-2" style="font-size:14px">
                <?= isset($data['patientInfo']) ? 'Nhóm máu: ' . htmlspecialchars($data['patientInfo']['blood_type'] ?? 'N/A') : '' ?>
            </div>
        </div>
    </div>
    <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
        <div class="stat-card card-warning">
            <div class="stat-icon"><i class="fa-solid fa-calendar-check"></i></div>
            <div class="stat-value"><?= count($data['myAppointments'] ?? []) ?></div>
            <div class="stat-label">Lịch hẹn của tôi</div>
        </div>
    </div>
    <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
        <a href="index.php?page=appointments&action=create" class="text-decoration-none">
            <div class="stat-card card-success" style="cursor:pointer;">
                <div class="stat-icon"><i class="fa-solid fa-plus"></i></div>
                <div class="stat-label" style="font-weight:600;">Đặt lịch khám mới</div>
            </div>
        </a>
    </div>
</div>

<div class="content-card" data-aos="fade-up" data-aos-delay="300">
    <div class="card-header">
        <h5><i class="fa-solid fa-calendar-days me-2"></i>Lịch hẹn gần đây</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($data['myAppointments'])): ?>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Bác sĩ</th>
                        <th>Chuyên khoa</th>
                        <th>Ngày khám</th>
                        <th>Lý do</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['myAppointments'] as $apt): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($apt['doctor_name']) ?></strong></td>
                        <td><?= htmlspecialchars($apt['specialty'] ?? '') ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($apt['appointment_date'])) ?></td>
                        <td><?= htmlspecialchars($apt['reason']) ?></td>
                        <td><span class="badge-status badge-<?= $apt['status'] ?>"><?= ucfirst($apt['status']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <p class="text-muted">Bạn chưa có lịch hẹn nào. <a href="index.php?page=appointments&action=create" style="color:var(--primary);">Đặt lịch ngay!</a></p>
        <?php endif; ?>
    </div>
</div>
