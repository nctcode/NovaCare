<!-- Department List -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-custom alert-dismissible fade show">
        <i class="fa-solid fa-check-circle"></i> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="content-card" data-aos="fade-up">
    <div class="card-header">
        <h5><i class="fa-solid fa-building-columns me-2"></i>Quản lý Khoa (<?= count($departments) ?>)</h5>
        <a href="index.php?page=departments&action=create" class="btn-action btn-add">
            <i class="fa-solid fa-plus"></i> Thêm Khoa
        </a>
    </div>
    <div class="card-body">
        <div class="row g-4">
            <?php foreach ($departments as $dept): ?>
            <div class="col-md-6 col-lg-4" data-aos="fade-up">
                <div class="stat-card" style="border-left:4px solid var(--primary);">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div class="stat-icon" style="background:var(--primary-light);color:var(--primary);">
                            <i class="fa-solid fa-building-columns"></i>
                        </div>
                        <div class="d-flex gap-1">
                            <a href="index.php?page=departments&action=view&id=<?= $dept['id'] ?>" class="btn-action btn-view" title="Chi tiết">
                                <i class="fa-solid fa-eye"></i>
                            </a>
                            <a href="index.php?page=departments&action=edit&id=<?= $dept['id'] ?>" class="btn-action btn-edit" title="Sửa">
                                <i class="fa-solid fa-pen"></i>
                            </a>
                            <button onclick="confirmDelete('index.php?page=departments&action=delete&id=<?= $dept['id'] ?>')" class="btn-action btn-delete" title="Xóa">
                                <i class="fa-solid fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <h5 style="font-weight:700;font-size:16px;"><?= htmlspecialchars($dept['name']) ?></h5>
                    <p style="font-size:13px;color:var(--gray-500);margin-bottom:12px;"><?= htmlspecialchars($dept['description'] ?? '') ?></p>
                    <div class="d-flex gap-3">
                        <span style="font-size:12px;color:var(--primary);font-weight:600;">
                            <i class="fa-solid fa-user-doctor"></i> <?= $dept['doctor_count'] ?? 0 ?> Bác sĩ
                        </span>
                        <span style="font-size:12px;color:var(--secondary);font-weight:600;">
                            <i class="fa-solid fa-user-nurse"></i> <?= $dept['nurse_count'] ?? 0 ?> Y tá
                        </span>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
            <?php if (empty($departments)): ?>
            <div class="col-12 text-center py-4 text-muted">Chưa có khoa nào.</div>
            <?php endif; ?>
        </div>
    </div>
</div>
