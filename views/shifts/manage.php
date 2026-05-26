<!-- Shift Management (Approval Page) -->
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

<div class="mb-4">
    <a href="index.php?page=shifts" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Quay lại Danh sách</a>
</div>

<div class="content-card mb-4" data-aos="fade-up">
    <div class="card-header bg-primary text-white">
        <h5><i class="fa-solid fa-info-circle me-2"></i>Chi tiết ca trực</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6 mb-3">
                <p class="mb-1 text-muted">Tên ca trực:</p>
                <h5><strong><?= htmlspecialchars($shift['name'] ?? 'Chưa đặt tên') ?></strong></h5>
            </div>
            <div class="col-md-6 mb-3">
                <p class="mb-1 text-muted">Ngày trực:</p>
                <h5>
                    <strong><?= date('d/m/Y', strtotime($shift['shift_date'])) ?></strong>
                    <small class="text-muted">(<?php 
                        $dayOfWeek = date('N', strtotime($shift['shift_date']));
                        $days = ['', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7', 'CN'];
                        echo $days[$dayOfWeek];
                    ?>)</small>
                </h5>
            </div>
            <div class="col-md-6 mb-3">
                <p class="mb-1 text-muted">Thời gian:</p>
                <h5><strong><?= date('H:i', strtotime($shift['start_time'])) ?> - <?= date('H:i', strtotime($shift['end_time'])) ?></strong></h5>
            </div>
            <div class="col-md-6 mb-3">
                <p class="mb-1 text-muted">Loại ca:</p>
                <span class="badge-status badge-<?= $shift['shift_type'] ?> d-inline-block py-1 px-3">
                    <?= $shift['shift_type'] === 'day' ? '☀️ Ca ngày' : '🌙 Ca đêm' ?>
                </span>
            </div>
            <div class="col-md-6 mb-3">
                <p class="mb-1 text-muted">Yêu cầu nhân sự:</p>
                <span>Bác sĩ: <strong><?= $shift['required_doctors'] ?></strong> | Y tá/ĐD: <strong><?= $shift['required_nurses'] ?></strong></span>
            </div>
            <div class="col-md-6 mb-3">
                <p class="mb-1 text-muted">Ghi chú:</p>
                <p class="text-muted mb-0"><?= nl2br(htmlspecialchars($shift['notes'] ?? 'Không có ghi chú')) ?></p>
            </div>
        </div>
    </div>
</div>

<div class="content-card" data-aos="fade-up" data-aos-delay="100">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5><i class="fa-solid fa-users me-2"></i>Danh sách đăng ký trực</h5>
    </div>
    <div class="card-body">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nhân viên</th>
                        <th>Vai trò</th>
                        <th>Số điện thoại</th>
                        <th>Chuyên môn / Ghi chú</th>
                        <th>Trạng thái</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($registrations)): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">Chưa có ai đăng ký ca trực này.</td></tr>
                    <?php else: ?>
                        <?php foreach ($registrations as $idx => $reg): ?>
                        <tr>
                            <td><?= $idx + 1 ?></td>
                            <td><strong><?= htmlspecialchars($reg['name']) ?></strong></td>
                            <td>
                                <?php if ($reg['role'] === 'doctor'): ?>
                                    <span class="badge bg-primary text-white"><i class="fa-solid fa-user-doctor me-1"></i>Bác sĩ</span>
                                <?php else: ?>
                                    <span class="badge bg-info text-dark"><i class="fa-solid fa-user-nurse me-1"></i>Y tá/ĐD</span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($reg['phone'] ?? '-') ?></td>
                            <td><small class="text-muted"><?= htmlspecialchars($reg['specialty'] ?? '') ?></small></td>
                            <td>
                                <?php if ($reg['status'] === 'approved'): ?>
                                    <span class="badge bg-success"><i class="fa-solid fa-circle-check me-1"></i>Đã duyệt</span>
                                <?php elseif ($reg['status'] === 'rejected'): ?>
                                    <span class="badge bg-danger"><i class="fa-solid fa-circle-xmark me-1"></i>Từ chối</span>
                                <?php else: ?>
                                    <span class="badge bg-warning text-dark"><i class="fa-solid fa-clock me-1"></i>Chờ duyệt</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($reg['status'] === 'pending'): ?>
                                    <div class="d-flex gap-1">
                                        <form method="POST" action="index.php?page=shifts&action=approve" style="display:inline;">
                                            <?= Security::csrfField() ?>
                                            <input type="hidden" name="shift_id" value="<?= $shift['id'] ?>">
                                            <input type="hidden" name="registration_id" value="<?= $reg['registration_id'] ?>">
                                            <input type="hidden" name="role" value="<?= $reg['role'] ?>">
                                            <button type="submit" class="btn btn-sm btn-success text-white" style="font-size: 12px; padding: 4px 8px;">
                                                <i class="fa-solid fa-check"></i> Duyệt
                                            </button>
                                        </form>

                                        <form method="POST" action="index.php?page=shifts&action=reject" style="display:inline;">
                                            <?= Security::csrfField() ?>
                                            <input type="hidden" name="shift_id" value="<?= $shift['id'] ?>">
                                            <input type="hidden" name="registration_id" value="<?= $reg['registration_id'] ?>">
                                            <input type="hidden" name="role" value="<?= $reg['role'] ?>">
                                            <button type="submit" class="btn btn-sm btn-danger text-white" style="font-size: 12px; padding: 4px 8px;">
                                                <i class="fa-solid fa-x"></i> Từ chối
                                            </button>
                                        </form>
                                    </div>
                                <?php else: ?>
                                    <span class="text-muted"><i class="fa-solid fa-check-double"></i> Đã xử lý</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
