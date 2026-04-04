<!-- Appointment List -->
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

<div class="content-card">
    <div class="card-header">
        <h5><i class="bi bi-calendar-check-fill me-2"></i>Danh sách Lịch hẹn</h5>
        <?php if ($_SESSION['user']['role'] === 'patient'): ?>
        <a href="index.php?page=appointments&action=create" class="btn-action btn-add">
            <i class="bi bi-plus-lg"></i> Đặt lịch khám
        </a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <?php if ($_SESSION['user']['role'] !== 'patient'): ?>
                        <th>Bệnh nhân</th>
                        <?php endif; ?>
                        <?php if ($_SESSION['user']['role'] !== 'doctor'): ?>
                        <th>Bác sĩ</th>
                        <?php endif; ?>
                        <th>Ngày khám</th>
                        <th>Lý do</th>
                        <th>Trạng thái</th>
                        <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                        <th>Cập nhật</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($appointments)): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">Không có lịch hẹn</td></tr>
                    <?php else: ?>
                        <?php foreach ($appointments as $idx => $a): ?>
                        <tr>
                            <td><?= $idx + 1 ?></td>
                            <?php if ($_SESSION['user']['role'] !== 'patient'): ?>
                            <td><strong><?= htmlspecialchars($a['patient_name'] ?? '') ?></strong></td>
                            <?php endif; ?>
                            <?php if ($_SESSION['user']['role'] !== 'doctor'): ?>
                            <td><?= htmlspecialchars($a['doctor_name'] ?? '') ?></td>
                            <?php endif; ?>
                            <td><?= date('d/m/Y H:i', strtotime($a['appointment_date'])) ?></td>
                            <td><?= htmlspecialchars($a['reason'] ?? '') ?></td>
                            <td><span class="badge-status badge-<?= $a['status'] ?>"><?= ucfirst($a['status']) ?></span></td>
                            <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        Cập nhật
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="index.php?page=appointments&action=updateStatus&id=<?= $a['id'] ?>&status=confirmed">
                                            <i class="bi bi-check-circle text-primary"></i> Xác nhận</a></li>
                                        <li><a class="dropdown-item" href="index.php?page=appointments&action=updateStatus&id=<?= $a['id'] ?>&status=completed">
                                            <i class="bi bi-check-all text-success"></i> Hoàn thành</a></li>
                                        <li><a class="dropdown-item" href="index.php?page=appointments&action=updateStatus&id=<?= $a['id'] ?>&status=cancelled">
                                            <i class="bi bi-x-circle text-danger"></i> Hủy</a></li>
                                    </ul>
                                </div>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
