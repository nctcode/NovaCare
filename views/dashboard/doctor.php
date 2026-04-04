<!-- Dashboard Doctor -->
<div class="row g-4 mb-4">
    <div class="col-md-4" data-aos="fade-up">
        <div class="stat-card card-primary">
            <div class="stat-icon"><i class="fa-solid fa-user-doctor"></i></div>
            <div class="stat-label">Xin chào, <?= htmlspecialchars($data['doctorInfo']['name'] ?? $user['name']) ?></div>
            <div class="stat-value mt-2" style="font-size:16px"><?= htmlspecialchars($data['doctorInfo']['specialty'] ?? '') ?></div>
        </div>
    </div>
    <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
        <div class="stat-card card-success">
            <div class="stat-icon"><i class="fa-solid fa-building"></i></div>
            <div class="stat-value" style="font-size:18px"><?= htmlspecialchars($data['doctorInfo']['department_name'] ?? 'N/A') ?></div>
            <div class="stat-label">Khoa</div>
        </div>
    </div>
    <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
        <div class="stat-card card-warning">
            <div class="stat-icon"><i class="fa-solid fa-calendar-check"></i></div>
            <div class="stat-value"><?= count($data['myAppointments'] ?? []) ?></div>
            <div class="stat-label">Lịch khám</div>
        </div>
    </div>
</div>

<div class="content-card" data-aos="fade-up" data-aos-delay="300">
    <div class="card-header">
        <h5><i class="fa-solid fa-calendar-days me-2"></i>Lịch khám sắp tới</h5>
    </div>
    <div class="card-body">
        <?php if (!empty($data['myAppointments'])): ?>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Bệnh nhân</th>
                        <th>Ngày khám</th>
                        <th>Lý do</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($data['myAppointments'] as $apt): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($apt['patient_name']) ?></strong></td>
                        <td><?= date('d/m/Y H:i', strtotime($apt['appointment_date'])) ?></td>
                        <td><?= htmlspecialchars($apt['reason']) ?></td>
                        <td><span class="badge-status badge-<?= $apt['status'] ?>"><?= ucfirst($apt['status']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
            <p class="text-muted">Không có lịch khám nào.</p>
        <?php endif; ?>
    </div>
</div>
