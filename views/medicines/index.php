<!-- Medicine List -->
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
        <h5><i class="bi bi-capsule me-2"></i>Danh sách Thuốc</h5>
        <a href="index.php?page=medicines&action=create" class="btn-action btn-add">
            <i class="bi bi-plus-lg"></i> Thêm thuốc
        </a>
    </div>
    <div class="card-body">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tên thuốc</th>
                        <th>Mô tả</th>
                        <th>Tồn kho</th>
                        <th>Hạn dùng</th>
                        <th>Giá (VNĐ)</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($medicines)): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">Không có dữ liệu</td></tr>
                    <?php else: ?>
                        <?php foreach ($medicines as $idx => $m): ?>
                        <tr>
                            <td><?= $idx + 1 ?></td>
                            <td><strong><?= htmlspecialchars($m['name']) ?></strong></td>
                            <td><?= htmlspecialchars($m['description'] ?? '') ?></td>
                            <td>
                                <?php if ($m['quantity'] <= 10): ?>
                                    <span class="badge bg-danger"><?= $m['quantity'] ?> ⚠️</span>
                                <?php else: ?>
                                    <span class="badge bg-success"><?= $m['quantity'] ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php 
                                    $expiry = strtotime($m['expiry_date']);
                                    $now = time();
                                    $daysLeft = ($expiry - $now) / 86400;
                                    if ($daysLeft < 0):
                                ?>
                                    <span class="text-danger fw-bold"><?= date('d/m/Y', $expiry) ?> (Hết hạn)</span>
                                <?php elseif ($daysLeft < 30): ?>
                                    <span class="text-warning fw-bold"><?= date('d/m/Y', $expiry) ?> (Sắp hết)</span>
                                <?php else: ?>
                                    <?= date('d/m/Y', $expiry) ?>
                                <?php endif; ?>
                            </td>
                            <td><?= number_format($m['price'], 0, ',', '.') ?></td>
                            <td>
                                <a href="index.php?page=medicines&action=edit&id=<?= $m['id'] ?>" 
                                   class="btn-action btn-edit" title="Sửa">
                                    <i class="bi bi-pencil-square"></i>
                                </a>
                                <button onclick="confirmDelete('index.php?page=medicines&action=delete&id=<?= $m['id'] ?>')" 
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
