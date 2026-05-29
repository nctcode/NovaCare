<!-- Danh sách Hàng chờ Khám bệnh -->
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

<!-- Thống kê nhanh hôm nay -->
<div class="row g-3 mb-4">
    <div class="col-md-2 col-6">
        <div class="stat-card card-warning">
            <div class="stat-icon"><i class="fa-solid fa-clock"></i></div>
            <div class="stat-value"><?= $stats['waiting'] ?? 0 ?></div>
            <div class="stat-label">Đang chờ</div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="stat-card card-info">
            <div class="stat-icon"><i class="fa-solid fa-bullhorn"></i></div>
            <div class="stat-value"><?= $stats['called'] ?? 0 ?></div>
            <div class="stat-label">Đã gọi</div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="stat-card card-primary">
            <div class="stat-icon"><i class="fa-solid fa-stethoscope"></i></div>
            <div class="stat-value"><?= $stats['in_progress'] ?? 0 ?></div>
            <div class="stat-label">Đang khám</div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="stat-card card-success">
            <div class="stat-icon"><i class="fa-solid fa-circle-check"></i></div>
            <div class="stat-value"><?= $stats['completed'] ?? 0 ?></div>
            <div class="stat-label">Đã xong</div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="stat-card card-danger">
            <div class="stat-icon"><i class="fa-solid fa-circle-xmark"></i></div>
            <div class="stat-value"><?= $stats['cancelled'] ?? 0 ?></div>
            <div class="stat-label">Đã hủy</div>
        </div>
    </div>
    <div class="col-md-2 col-6">
        <div class="stat-card card-secondary">
            <div class="stat-icon"><i class="fa-solid fa-users"></i></div>
            <div class="stat-value"><?= $stats['total'] ?? 0 ?></div>
            <div class="stat-label">Tổng lượt</div>
        </div>
    </div>
</div>

<!-- Lọc hàng chờ -->
<div class="content-card mb-4">
    <div class="card-body">
        <form method="GET" action="index.php" class="row g-3 align-items-end">
            <input type="hidden" name="page" value="queue">
            
            <div class="col-md-3">
                <label for="status" class="form-label text-sm fw-bold">Trạng thái</label>
                <select class="form-select" id="status" name="status">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="waiting" <?= ($_GET['status'] ?? '') === 'waiting' ? 'selected' : '' ?>>Đang chờ</option>
                    <option value="called" <?= ($_GET['status'] ?? '') === 'called' ? 'selected' : '' ?>>Đã gọi</option>
                    <option value="in_progress" <?= ($_GET['status'] ?? '') === 'in_progress' ? 'selected' : '' ?>>Đang khám</option>
                    <option value="completed" <?= ($_GET['status'] ?? '') === 'completed' ? 'selected' : '' ?>>Đã xong</option>
                    <option value="cancelled" <?= ($_GET['status'] ?? '') === 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                </select>
            </div>
            
            <div class="col-md-4">
                <label for="department_id" class="form-label text-sm fw-bold">Khoa khám</label>
                <select class="form-select" id="department_id" name="department_id">
                    <option value="">-- Tất cả khoa --</option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?= $dept['id'] ?>" <?= ($_GET['department_id'] ?? '') == $dept['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($dept['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-3 d-flex gap-2">
                <button type="submit" class="btn btn-primary w-100"><i class="fa-solid fa-filter me-2"></i>Lọc</button>
                <a href="index.php?page=queue" class="btn btn-outline-secondary w-100">Xóa bộ lọc</a>
            </div>
        </form>
    </div>
</div>

<div class="content-card">
    <div class="card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h5 class="mb-0"><i class="fa-solid fa-list-ol me-2"></i>Hàng chờ khám bệnh hôm nay (<?= date('d/m/Y') ?>)</h5>
        <div class="d-flex gap-2">
            <a href="index.php?page=queue&action=display" target="_blank" class="btn btn-outline-info">
                <i class="fa-solid fa-desktop me-2"></i>Màn hình tivi
            </a>
            <?php if (in_array($user['role'], ['receptionist'])): ?>
                <a href="index.php?page=queue&action=checkin" class="btn btn-primary">
                    <i class="fa-solid fa-plus me-2"></i>Cấp số thứ tự
                </a>
            <?php endif; ?>
            <?php if (in_array($user['role'], ['doctor'])): ?>
                <form method="POST" action="index.php?page=queue&action=callNext" style="display:inline;">
                    <?= Security::csrfField() ?>
                    <input type="hidden" name="department_id" value="<?= $_GET['department_id'] ?? '' ?>">
                    <button type="submit" class="btn btn-success">
                        <i class="fa-solid fa-forward me-2"></i>Gọi BN tiếp theo
                    </button>
                </form>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="card-body">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th style="width: 80px">Số TT</th>
                        <th>Bệnh nhân</th>
                        <th>Khoa / Phòng khám</th>
                        <th>Ưu tiên</th>
                        <th>Trạng thái</th>
                        <th>Check-in</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($tickets)): ?>
                        <tr>
                            <td colspan="7" class="text-center text-muted py-4">Hôm nay không có bệnh nhân nào trong hàng chờ</td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($tickets as $t): ?>
                            <tr class="<?= $t['priority'] === 'emergency' ? 'table-danger' : ($t['priority'] === 'priority' ? 'table-warning' : '') ?>">
                                <td>
                                    <span class="fs-5 fw-bold text-primary">
                                        <?= str_pad($t['ticket_number'], 4, '0', STR_PAD_LEFT) ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="index.php?page=patients&action=view&id=<?= $t['patient_id'] ?>" class="text-decoration-none fw-bold text-dark hover-primary" style="transition: color 0.2s;">
                                        <?= htmlspecialchars($t['patient_name']) ?>
                                    </a>
                                    <?php if ($t['insurance_verified']): ?>
                                        <span class="badge bg-success ms-1" style="font-size:10px;"><i class="fa-solid fa-shield-halved"></i> BHYT</span>
                                    <?php endif; ?>
                                    <br><small class="text-muted">SĐT: <?= htmlspecialchars($t['patient_phone'] ?? 'Chưa có') ?></small>
                                </td>
                                <td>
                                    <strong><?= htmlspecialchars($t['department_name'] ?? 'Đa khoa') ?></strong>
                                    <?php if ($t['room_name']): ?>
                                        <br><span class="badge bg-secondary"><i class="fa-solid fa-door-open me-1"></i><?= htmlspecialchars($t['room_name']) ?></span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php if ($t['priority'] === 'emergency'): ?>
                                        <span class="badge bg-danger animate__animated animate__flash animate__infinite"><i class="fa-solid fa-triangle-exclamation me-1"></i>Cấp cứu</span>
                                    <?php elseif ($t['priority'] === 'priority'): ?>
                                        <span class="badge bg-warning text-dark"><i class="fa-solid fa-star me-1"></i>Ưu tiên</span>
                                    <?php else: ?>
                                        <span class="badge bg-light text-dark border">Thường</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $statusLabels = [
                                        'waiting' => ['bg-warning text-dark', 'fa-clock', 'Chờ khám'],
                                        'called' => ['bg-info text-dark', 'fa-bullhorn', 'Đã gọi'],
                                        'in_progress' => ['bg-primary', 'fa-stethoscope', 'Đang khám'],
                                        'completed' => ['bg-success', 'fa-circle-check', 'Đã khám xong'],
                                        'cancelled' => ['bg-danger', 'fa-circle-xmark', 'Đã hủy']
                                    ];
                                    $label = $statusLabels[$t['status']] ?? ['bg-secondary', 'fa-question', $t['status']];
                                    ?>
                                    <span class="badge <?= $label[0] ?>"><i class="fa-solid <?= $label[1] ?> me-1"></i><?= $label[2] ?></span>
                                </td>
                                <td>
                                    <small><?= date('H:i', strtotime($t['check_in_at'])) ?></small>
                                </td>
                                <td>
                                    <div class="d-flex gap-1">
                                        <?php if ($t['status'] === 'waiting' && in_array($user['role'], ['doctor'])): ?>
                                            <form method="POST" action="index.php?page=queue&action=updateStatus">
                                                <?= Security::csrfField() ?>
                                                <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                                <input type="hidden" name="status" value="called">
                                                <button type="submit" class="btn btn-sm btn-info" title="Gọi bệnh nhân">
                                                    <i class="fa-solid fa-bullhorn"></i> Gọi
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <?php if ($t['status'] === 'called' && in_array($user['role'], ['doctor'])): ?>
                                            <form method="POST" action="index.php?page=queue&action=updateStatus">
                                                <?= Security::csrfField() ?>
                                                <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                                <input type="hidden" name="status" value="in_progress">
                                                <button type="submit" class="btn btn-sm btn-primary" title="Bắt đầu khám">
                                                    <i class="fa-solid fa-stethoscope"></i> Khám
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <?php if ($t['status'] === 'in_progress' && in_array($user['role'], ['doctor'])): ?>
                                            <!-- Bác sĩ tạo bệnh án hoặc trực tiếp click xong -->
                                            <form method="POST" action="index.php?page=queue&action=updateStatus">
                                                <?= Security::csrfField() ?>
                                                <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                                <input type="hidden" name="status" value="completed">
                                                <button type="submit" class="btn btn-sm btn-success" title="Hoàn thành khám">
                                                    <i class="fa-solid fa-circle-check"></i> Xong
                                                </button>
                                            </form>
                                        <?php endif; ?>

                                        <?php if (in_array($t['status'], ['waiting', 'called']) && in_array($user['role'], ['receptionist'])): ?>
                                            <form method="POST" action="index.php?page=queue&action=updateStatus" onsubmit="return confirm('Bạn có chắc muốn hủy lượt khám này?')">
                                                <?= Security::csrfField() ?>
                                                <input type="hidden" name="id" value="<?= $t['id'] ?>">
                                                <input type="hidden" name="status" value="cancelled">
                                                <button type="submit" class="btn btn-sm btn-danger" title="Hủy lượt khám">
                                                    <i class="fa-solid fa-trash"></i> Hủy
                                                </button>
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
