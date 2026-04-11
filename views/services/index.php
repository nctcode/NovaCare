<!-- Service List -->
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
        <h5><i class="fa-solid fa-stethoscope me-2"></i>Dịch vụ Y tế (<?= count($services) ?>)</h5>
        <a href="index.php?page=services-admin&action=create" class="btn-action btn-add">
            <i class="fa-solid fa-plus"></i> Thêm Dịch vụ
        </a>
    </div>
    <div class="card-body">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tên Dịch vụ</th>
                        <th>Mô tả</th>
                        <th>Giá (VNĐ)</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($services as $i => $service): ?>
                    <tr>
                        <td><?= $i + 1 ?></td>
                        <td><strong><?= htmlspecialchars($service['service_name']) ?></strong></td>
                        <td><?= htmlspecialchars($service['description'] ?? '') ?></td>
                        <td><span class="badge-status badge-confirmed"><?= number_format($service['price'], 0, ',', '.') ?> đ</span></td>
                        <td>
                            <a href="index.php?page=services-admin&action=edit&id=<?= $service['id'] ?>" class="btn-action btn-edit">
                                <i class="fa-solid fa-pen-to-square"></i>
                            </a>
                            <button onclick="confirmDelete('index.php?page=services-admin&action=delete&id=<?= $service['id'] ?>')" class="btn-action btn-delete">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($services)): ?>
                    <tr><td colspan="5" class="text-center text-muted py-4">Chưa có dịch vụ nào.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
