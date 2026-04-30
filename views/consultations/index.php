<!-- Online Consultations -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-custom alert-dismissible fade show">
        <i class="fa-solid fa-check-circle"></i> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-custom alert-dismissible fade show">
        <i class="fa-solid fa-triangle-exclamation"></i> <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($user['role'] === 'admin'): ?>
<!-- Tạo phòng tư vấn mới (Admin) -->
<div class="content-card mb-4" data-aos="fade-up">
    <div class="card-header">
        <h5><i class="fa-solid fa-video me-2"></i>Tạo phòng Tư vấn trực tuyến</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="index.php?page=consultations&action=store" class="row align-items-end">
                            <?php echo Security::csrfField(); ?>
            <div class="col-md-8 mb-3 mb-md-0">
                <label for="appointment_id" class="form-label">Chọn lịch hẹn cần tư vấn online</label>
                <select class="form-select" id="appointment_id" name="appointment_id" required>
                    <option value="">-- Lịch hẹn khả dụng --</option>
                    <?php foreach ($availableAppointments as $apt): ?>
                        <option value="<?= $apt['id'] ?>">Ngày: <?= date('d/m/Y H:i', strtotime($apt['appointment_date'])) ?> | Bác sĩ: <?= htmlspecialchars($apt['doctor_name']) ?> | Bệnh nhân: <?= htmlspecialchars($apt['patient_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn-submit w-100"><i class="fa-solid fa-plus me-2"></i>Tạo phòng Meeting</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Danh sách cuộc họp -->
<div class="content-card" data-aos="fade-up" data-aos-delay="100">
    <div class="card-header">
        <h5><i class="fa-solid fa-list me-2"></i>Lịch Tư vấn trực tuyến (<?= count($meetings) ?>)</h5>
    </div>
    <div class="card-body">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Bệnh nhân</th>
                        <th>Bác sĩ</th>
                        <th>Thời gian</th>
                        <th>Lý do khám</th>
                        <th>Trạng thái</th>
                        <th>Phòng họp</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($meetings as $m): ?>
                    <tr>
                        <td><strong><?= htmlspecialchars($m['patient_name']) ?></strong></td>
                        <td><?= htmlspecialchars($m['doctor_name']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($m['start_time'])) ?></td>
                        <td><?= htmlspecialchars($m['reason']) ?></td>
                        <td><span class="badge-status badge-<?= $m['status'] == 'completed' ? 'completed' : 'pending' ?>"><?= ucfirst($m['status']) ?></span></td>
                        <td>
                            <?php if ($m['status'] == 'scheduled' || $m['status'] == 'pending'): ?>
                                <a href="<?= htmlspecialchars($m['meeting_link']) ?>" target="_blank" class="btn-action btn-add" style="border-radius:20px; padding: 4px 12px; font-size:13px; text-decoration:none;">
                                    <i class="fa-solid fa-video"></i> Tham gia
                                </a>
                            <?php else: ?>
                                <span class="text-muted">Đã đóng</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($meetings)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Chưa có lịch tư vấn online nào.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

