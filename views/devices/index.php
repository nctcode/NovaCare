<!-- Device List -->
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
        <h5><i class="bi bi-cpu me-2"></i>Thiết bị Y tế</h5>
        <a href="index.php?page=devices&action=create" class="btn-action btn-add">
            <i class="bi bi-plus-lg"></i> Thêm thiết bị
        </a>
    </div>
    <div class="card-body">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tên thiết bị</th>
                        <th>Mã thiết bị</th>
                        <th>Khoa</th>
                        <th>Ngày mua</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($devices)): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">Không có thiết bị</td></tr>
                    <?php else: ?>
                        <?php foreach ($devices as $idx => $d): ?>
                        <tr>
                            <td><?= $idx + 1 ?></td>
                            <td><strong><?= htmlspecialchars($d['name']) ?></strong></td>
                            <td><code><?= htmlspecialchars($d['device_code'] ?? '') ?></code></td>
                            <td><?= htmlspecialchars($d['department_name'] ?? 'N/A') ?></td>
                            <td><?= $d['purchase_date'] ? date('d/m/Y', strtotime($d['purchase_date'])) : '' ?></td>
                            <td>
                                <span class="badge-status badge-<?= $d['status'] ?>">
                                    <?php
                                        $statusMap = ['available' => 'Sẵn sàng', 'in_use' => 'Đang dùng', 'maintenance' => 'Bảo trì'];
                                        echo $statusMap[$d['status']] ?? $d['status'];
                                    ?>
                                </span>
                            </td>
                            <td>
                                <div class="dropdown d-inline">
                                    <button class="btn-action btn-edit dropdown-toggle" data-bs-toggle="dropdown">
                                        <i class="bi bi-gear"></i>
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="index.php?page=devices&action=updateStatus&id=<?= $d['id'] ?>&status=available">
                                            ✅ Sẵn sàng</a></li>
                                        <li><a class="dropdown-item" href="index.php?page=devices&action=updateStatus&id=<?= $d['id'] ?>&status=in_use">
                                            🔵 Đang dùng</a></li>
                                        <li><a class="dropdown-item" href="index.php?page=devices&action=updateStatus&id=<?= $d['id'] ?>&status=maintenance">
                                            🔧 Bảo trì</a></li>
                                    </ul>
                                </div>
                                <button onclick="confirmDelete('index.php?page=devices&action=delete&id=<?= $d['id'] ?>')" 
                                        class="btn-action btn-delete" title="Xóa">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
