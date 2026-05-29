<!-- Cashier List -->
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

<div class="content-card" data-aos="fade-up">
    <div class="card-header">
        <h5><i class="fa-solid fa-coins me-2"></i>Danh sách Thu ngân (<?= count($cashiers) ?>)</h5>
        <?php if ($_SESSION['user']['role'] === 'admin'): ?>
        <a href="index.php?page=cashiers&action=create" class="btn-action btn-add">
            <i class="fa-solid fa-plus"></i> Thêm Thu ngân
        </a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Họ tên</th>
                        <th>Email</th>
                        <th>Điện thoại</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($cashiers as $i => $cs): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><strong><?= htmlspecialchars($cs['name']) ?></strong></td>
                        <td><?= htmlspecialchars($cs['email']) ?></td>
                        <td><?= htmlspecialchars($cs['phone'] ?? '-') ?></td>
                        <td>
                            <span class="badge-status badge-confirmed">Đang làm việc</span>
                        </td>
                        <td>
                            <a href="index.php?page=cashiers&action=view&id=<?= $cs['id'] ?>" class="btn-action btn-view text-primary me-2" title="Xem chi tiết" style="font-size: 14px; text-decoration: none;">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                            <a href="index.php?page=cashiers&action=edit&id=<?= $cs['id'] ?>" class="btn-action btn-edit me-2" title="Sửa">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <button onclick="confirmDelete('index.php?page=cashiers&action=delete&id=<?= $cs['id'] ?>')" class="btn-action btn-delete" title="Xóa">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($cashiers)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Chưa có thu ngân nào.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
