<!-- Danh sách Chỉ định Cận lâm sàng -->
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

<?php $user = $_SESSION['user']; ?>

<!-- Thống kê nhanh -->
<div class="row g-3 mb-4">
    <?php
    $pendingCount = 0; $progressCount = 0; $completedCount = 0;
    foreach ($orders as $o) {
        if ($o['status'] === 'pending') $pendingCount++;
        elseif ($o['status'] === 'in_progress') $progressCount++;
        elseif ($o['status'] === 'completed') $completedCount++;
    }
    ?>
    <div class="col-md-3 col-6">
        <div class="stat-card card-warning">
            <div class="stat-icon"><i class="fa-solid fa-clock"></i></div>
            <div class="stat-value"><?= $pendingCount ?></div>
            <div class="stat-label">Chờ xử lý</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-card card-primary">
            <div class="stat-icon"><i class="fa-solid fa-spinner"></i></div>
            <div class="stat-value"><?= $progressCount ?></div>
            <div class="stat-label">Đang thực hiện</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-card card-success">
            <div class="stat-icon"><i class="fa-solid fa-check-circle"></i></div>
            <div class="stat-value"><?= $completedCount ?></div>
            <div class="stat-label">Hoàn thành</div>
        </div>
    </div>
    <div class="col-md-3 col-6">
        <div class="stat-card card-info">
            <div class="stat-icon"><i class="fa-solid fa-flask-vial"></i></div>
            <div class="stat-value"><?= count($orders) ?></div>
            <div class="stat-label">Tổng chỉ định</div>
        </div>
    </div>
</div>

<div class="content-card">
    <div class="card-header">
        <h5><i class="fa-solid fa-flask-vial me-2"></i>Danh sách Chỉ định CLS</h5>
        <?php if (in_array($user['role'], ['doctor', 'admin'])): ?>
        <a href="index.php?page=lab-orders&action=create" class="btn-action btn-create">
            <i class="fa-solid fa-plus"></i> Tạo chỉ định
        </a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <div class="table-wrapper">
            <table class="data-table" id="labOrdersTable">
                <thead>
                    <tr>
                        <th style="width:45px">#</th>
                        <th>Bệnh nhân</th>
                        <th>Loại</th>
                        <th>Tên xét nghiệm/chụp</th>
                        <th>Ưu tiên</th>
                        <th>Trạng thái</th>
                        <?php if ($user['role'] !== 'technician'): ?>
                        <th>Bác sĩ CĐ</th>
                        <?php endif; ?>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($orders)): ?>
                    <tr><td colspan="9" class="text-center text-muted py-4">Không có chỉ định nào</td></tr>
                    <?php else: ?>
                    <?php foreach ($orders as $idx => $o): ?>
                    <tr>
                        <td><?= $idx + 1 ?></td>
                        <td>
                            <strong><?= htmlspecialchars($o['patient_name']) ?></strong>
                            <br><small class="text-muted"><?= htmlspecialchars($o['patient_phone'] ?? '') ?></small>
                        </td>
                        <td>
                            <?php if ($o['order_type'] === 'lab_test'): ?>
                                <span class="badge bg-info text-dark"><i class="fa-solid fa-vial me-1"></i>Xét nghiệm</span>
                            <?php else: ?>
                                <span class="badge bg-secondary"><i class="fa-solid fa-x-ray me-1"></i>CĐHA</span>
                            <?php endif; ?>
                        </td>
                        <td><strong><?= htmlspecialchars($o['test_name']) ?></strong></td>
                        <td>
                            <?php if ($o['priority'] === 'urgent'): ?>
                                <span class="badge bg-danger"><i class="fa-solid fa-bolt me-1"></i>Cấp cứu</span>
                            <?php else: ?>
                                <span class="badge bg-light text-dark border">Bình thường</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php
                            $statusMap = [
                                'pending' => ['bg-warning text-dark', 'fa-clock', 'Chờ xử lý'],
                                'in_progress' => ['bg-primary', 'fa-spinner fa-spin', 'Đang thực hiện'],
                                'completed' => ['bg-success', 'fa-check-circle', 'Hoàn thành'],
                                'cancelled' => ['bg-danger', 'fa-times-circle', 'Đã hủy'],
                            ];
                            $st = $statusMap[$o['status']] ?? ['bg-secondary', 'fa-question', $o['status']];
                            ?>
                            <span class="badge <?= $st[0] ?>"><i class="fa-solid <?= $st[1] ?> me-1"></i><?= $st[2] ?></span>
                        </td>
                        <?php if ($user['role'] !== 'technician'): ?>
                        <td><?= htmlspecialchars($o['doctor_name']) ?></td>
                        <?php endif; ?>
                        <td><small><?= date('d/m/Y H:i', strtotime($o['created_at'])) ?></small></td>
                        <td>
                            <div class="d-flex gap-1 flex-nowrap">
                                <a href="index.php?page=lab-orders&action=view&id=<?= $o['id'] ?>" class="btn-action btn-view" title="Xem">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                                <?php if ($user['role'] === 'technician' && in_array($o['status'], ['pending', 'in_progress'])): ?>
                                <a href="index.php?page=lab-orders&action=result&id=<?= $o['id'] ?>" class="btn-action btn-edit" title="Nhập kết quả">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <?php endif; ?>
                                <?php if (in_array($user['role'], ['doctor', 'admin']) && $o['status'] === 'pending'): ?>
                                <form method="POST" action="index.php?page=lab-orders&action=cancel" style="display:inline" onsubmit="return confirm('Hủy chỉ định này?')">
                                    <?= Security::csrfField() ?>
                                    <input type="hidden" name="id" value="<?= $o['id'] ?>">
                                    <button type="submit" class="btn-action btn-delete" title="Hủy"><i class="fa-solid fa-xmark"></i></button>
                                </form>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
