<!-- Director List -->
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
        <h5><i class="fa-solid fa-user-tie me-2"></i>Danh sách Ban giám đốc (<?= count($directors) ?>)</h5>
        <?php if ($_SESSION['user']['role'] === 'admin'): ?>
        <a href="index.php?page=directors&action=create" class="btn-action btn-add">
            <i class="fa-solid fa-plus"></i> Thêm Giám đốc
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
                    <?php foreach ($directors as $i => $dir): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><strong><?= htmlspecialchars($dir['name']) ?></strong></td>
                        <td><?= htmlspecialchars($dir['email']) ?></td>
                        <td><?= htmlspecialchars($dir['phone'] ?? '-') ?></td>
                        <td>
                            <span class="badge-status badge-confirmed">Đang làm việc</span>
                        </td>
                        <td>
                            <a href="index.php?page=directors&action=view&id=<?= $dir['id'] ?>" class="btn-action btn-view text-primary me-2" title="Xem chi tiết" style="font-size: 14px; text-decoration: none;">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <?php if ($_SESSION['user']['role'] === 'admin'): ?>
                            <a href="index.php?page=directors&action=edit&id=<?= $dir['id'] ?>" class="btn-action btn-edit me-2" title="Sửa">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <button onclick="confirmDelete('index.php?page=directors&action=delete&id=<?= $dir['id'] ?>')" class="btn-action btn-delete" title="Xóa">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($directors)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Chưa có giám đốc nào.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
