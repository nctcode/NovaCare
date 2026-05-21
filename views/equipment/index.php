<!-- Equipment List -->
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
        <h5><i class="fa-solid fa-bed-pulse me-2"></i>Trang thiết bị y tế (<?= count($equipmentList) ?>)</h5>
        <a href="index.php?page=equipment&action=create" class="btn-action btn-add">
            <i class="fa-solid fa-plus"></i> Thêm Thiết bị
        </a>
    </div>
    <div class="card-body">
        <div class="row g-4">
            <?php foreach ($equipmentList as $item): ?>
            <div class="col-md-6 col-lg-4" data-aos="fade-up">
                <div class="stat-card" style="border-left:4px solid <?= $item['status'] == 'available' ? 'var(--secondary)' : 'var(--danger)' ?>;">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stat-icon" style="background:var(--gray-100);color:var(--gray-700);">
                            <i class="fa-solid fa-toolbox"></i>
                        </div>
                        <div class="d-flex gap-1" style="background:var(--white);padding:4px;border-radius:20px;box-shadow:0 2px 10px rgba(0,0,0,0.05);">
                            <a href="index.php?page=equipment&action=edit&id=<?= $item['id'] ?>" class="btn-action btn-edit" style="width:28px;height:28px;border-radius:50%;"><i class="fa-solid fa-pen" style="font-size:12px;"></i></a>
                            <button onclick="confirmDelete('index.php?page=equipment&action=delete&id=<?= $item['id'] ?>')" class="btn-action btn-delete" style="width:28px;height:28px;border-radius:50%;"><i class="fa-solid fa-trash" style="font-size:12px;"></i></button>
                        </div>
                    </div>
                    <h5 style="font-weight:700;font-size:16px;" class="text-truncate" title="<?= htmlspecialchars($item['equipment_name']) ?>"><?= htmlspecialchars($item['equipment_name']) ?></h5>
                    <p style="font-size:13px;color:var(--gray-500);margin-bottom:12px;height:40px;overflow:hidden;"><?= htmlspecialchars($item['description'] ?? '') ?></p>
                    <div class="d-flex justify-content-between align-items-center border-top pt-3 mt-3">
                        <span style="font-size:13px;">
                            <i class="fa-solid fa-boxes-stacked text-muted me-1"></i> Số lượng: <strong><?= $item['quantity'] ?></strong>
                        </span>
                        <span class="badge-status badge-<?= $item['status'] == 'available' ? 'confirmed' : 'cancelled' ?>">
                            <?= $item['status'] == 'available' ? 'Sẵn sàng' : 'Đang bảo trì' ?>
                        </span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            
            <?php if (empty($equipmentList)): ?>
            <div class="col-12 py-5 text-center text-muted">Chưa có trang thiết bị nào được ghi nhận.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
