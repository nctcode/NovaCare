<!-- Nurse List -->
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
        <h5><i class="fa-solid fa-user-nurse me-2"></i>Danh sách Y tá (<?= count($nurses) ?>)</h5>
        <a href="index.php?page=nurses&action=create" class="btn-action btn-add">
            <i class="fa-solid fa-plus"></i> Thêm Y tá
        </a>
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
                        <th>Khoa</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($nurses as $i => $nurse): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><strong><?= htmlspecialchars($nurse['name']) ?></strong></td>
                        <td><?= htmlspecialchars($nurse['email']) ?></td>
                        <td><?= htmlspecialchars($nurse['phone'] ?? '') ?></td>
                        <td>
                            <?php if ($nurse['department_name']): ?>
                                <span class="badge-status badge-confirmed"><?= htmlspecialchars($nurse['department_name']) ?></span>
                            <?php else: ?>
                                <span class="badge-status badge-cancelled">Chưa phân khoa</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <a href="index.php?page=nurses&action=edit&id=<?= $nurse['id'] ?>" class="btn-action btn-edit">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <button onclick="confirmDelete('index.php?page=nurses&action=delete&id=<?= $nurse['id'] ?>')" class="btn-action btn-delete">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($nurses)): ?>
                    <tr><td colspan="6" class="text-center text-muted py-4">Chưa có y tá nào.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
