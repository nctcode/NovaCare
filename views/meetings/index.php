<!-- Online Meetings -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-custom alert-dismissible fade show">
        <i class="bi bi-check-circle-fill"></i> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-custom alert-dismissible fade show">
        <i class="bi bi-exclamation-triangle-fill"></i> <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Form tạo meeting -->
<?php if (!empty($availableAppointments) && $_SESSION['user']['role'] === 'admin'): ?>
<div class="content-card mb-4">
    <div class="card-header">
        <h5><i class="bi bi-plus-circle me-2"></i>Tạo phòng tư vấn online</h5>
    </div>
    <div class="card-body">
        <form method="POST" action="index.php?page=meetings&action=store" class="row align-items-end g-3">
                            <?php echo Security::csrfField(); ?>
            <div class="col-md-8">
                <label for="appointment_id" class="form-label">Chọn lịch hẹn</label>
                <select class="form-select" id="appointment_id" name="appointment_id" required>
                    <option value="">-- Chọn lịch hẹn --</option>
                    <?php foreach ($availableAppointments as $a): ?>
                        <option value="<?= $a['id'] ?>">
                            <?= htmlspecialchars($a['patient_name']) ?> → <?= htmlspecialchars($a['doctor_name']) ?>
                            (<?= date('d/m/Y H:i', strtotime($a['appointment_date'])) ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn-submit w-100">
                    <i class="bi bi-camera-video"></i> Tạo phòng họp
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Danh sách meetings -->
<div class="content-card">
    <div class="card-header">
        <h5><i class="bi bi-camera-video-fill me-2"></i>Danh sách Phòng tư vấn Online</h5>
    </div>
    <div class="card-body">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Meeting ID</th>
                        <th>Bệnh nhân</th>
                        <th>Bác sĩ</th>
                        <th>Thời gian</th>
                        <th>Link</th>
                        <th>Trạng thái</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($meetings)): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">Không có phòng tư vấn</td></tr>
                    <?php else: ?>
                        <?php foreach ($meetings as $idx => $m): ?>
                        <tr>
                            <td><?= $idx + 1 ?></td>
                            <td><code><?= htmlspecialchars($m['meeting_id']) ?></code></td>
                            <td><strong><?= htmlspecialchars($m['patient_name']) ?></strong></td>
                            <td><?= htmlspecialchars($m['doctor_name']) ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($m['start_time'])) ?></td>
                            <td>
                                <a href="<?= htmlspecialchars($m['meeting_link']) ?>" target="_blank" 
                                   class="btn-action btn-view">
                                    <i class="bi bi-box-arrow-up-right"></i> Tham gia
                                </a>
                            </td>
                            <td><span class="badge-status badge-<?= $m['status'] ?>"><?= ucfirst($m['status']) ?></span></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

