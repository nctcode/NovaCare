<!-- Department List -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-custom alert-dismissible fade show mb-4">
        <i class="fa-solid fa-check-circle me-2"></i> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-custom alert-dismissible fade show mb-4">
        <i class="fa-solid fa-triangle-exclamation me-2"></i> <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4" data-aos="fade-up">
    <div>
        <h4 class="mb-1 text-primary fw-bold"><i class="fa-solid fa-building-columns me-2"></i>Quản lý Khoa</h4>
        <p class="text-muted mb-0 small">Hệ thống có tổng cộng <span class="badge bg-primary rounded-pill"><?= count($departments) ?></span> khoa dựa trên bộ lọc.</p>
    </div>
    <?php if ($user['role'] === 'admin'): ?>
    <a href="index.php?page=departments&action=create" class="btn btn-primary px-4 py-2" style="border-radius: 8px; font-weight: 500; box-shadow: 0 4px 10px rgba(13, 110, 253, 0.2);">
        <i class="fa-solid fa-plus me-1"></i> Thêm Khoa Mới
    </a>
    <?php endif; ?>
</div>

<!-- Bộ lọc tìm kiếm -->
<div class="card border-0 shadow-sm mb-4" style="border-radius: 12px; background: rgba(255,255,255,0.85); backdrop-filter: blur(10px);" data-aos="fade-up" data-aos-delay="100">
    <div class="card-body p-4">
        <form method="GET" action="index.php" class="row g-3 align-items-end">
            <input type="hidden" name="page" value="departments">
            
            <div class="col-md-4">
                <label class="form-label text-muted small fw-bold"><i class="fa-solid fa-search me-1"></i>Tìm kiếm</label>
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 ps-0 focus-none" placeholder="Nhập tên hoặc mô tả khoa..." value="<?= htmlspecialchars($_GET['search'] ?? '') ?>" style="box-shadow: none;">
                </div>
            </div>

            <div class="col-md-3">
                <label class="form-label text-muted small fw-bold"><i class="fa-solid fa-sort me-1"></i>Sắp xếp</label>
                <select name="sort" class="form-select" style="box-shadow: none;">
                    <option value="name_asc" <?= ($_GET['sort'] ?? '') === 'name_asc' ? 'selected' : '' ?>>Tên (A-Z)</option>
                    <option value="name_desc" <?= ($_GET['sort'] ?? '') === 'name_desc' ? 'selected' : '' ?>>Tên (Z-A)</option>
                    <option value="doctors_desc" <?= ($_GET['sort'] ?? '') === 'doctors_desc' ? 'selected' : '' ?>>Nhiều bác sĩ nhất</option>
                    <option value="nurses_desc" <?= ($_GET['sort'] ?? '') === 'nurses_desc' ? 'selected' : '' ?>>Nhiều y tá nhất</option>
                </select>
            </div>

            <div class="col-md-3">
                <label class="form-label text-muted small fw-bold mb-3 d-block"><i class="fa-solid fa-filter me-1"></i>Bộ lọc</label>
                <div class="d-flex gap-4">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="has_doctors" value="1" id="has_doctors" <?= !empty($_GET['has_doctors']) ? 'checked' : '' ?>>
                        <label class="form-check-label text-secondary small" for="has_doctors">Có bác sĩ</label>
                    </div>
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="has_nurses" value="1" id="has_nurses" <?= !empty($_GET['has_nurses']) ? 'checked' : '' ?>>
                        <label class="form-check-label text-secondary small" for="has_nurses">Có y tá</label>
                    </div>
                </div>
            </div>

            <div class="col-md-2 text-md-end">
                <button type="submit" class="btn btn-dark w-100" style="border-radius: 8px;">
                    Lọc dữ liệu
                </button>
            </div>
        </form>
    </div>
</div>

<div class="row g-4 mb-5">
    <?php foreach ($departments as $index => $dept): ?>
    <div class="col-md-6 col-lg-4" data-aos="fade-up" data-aos-delay="<?= ($index % 3) * 100 + 100 ?>">
        <div class="card h-100 border-0 department-card" style="border-radius: 16px; transition: all 0.3s ease; box-shadow: 0 5px 15px rgba(0,0,0,0.05); overflow: hidden;">
            <div class="card-header border-0 bg-transparent pt-4 pb-0 px-4 d-flex justify-content-between align-items-start">
                <div class="icon-box rounded-circle d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: linear-gradient(135deg, rgba(13,110,253,0.1), rgba(13,110,253,0.2)); color: var(--bs-primary);">
                    <i class="fa-solid fa-hospital-user fs-4"></i>
                </div>
                <div class="dropdown">
                    <button class="btn btn-sm btn-light rounded-circle" type="button" data-bs-toggle="dropdown" style="width: 32px; height: 32px; box-shadow: none;">
                        <i class="fa-solid fa-ellipsis-vertical text-muted"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end shadow-sm border-0" style="border-radius: 8px;">
                        <li><a class="dropdown-item py-2 text-primary" href="index.php?page=departments&action=view&id=<?= $dept['id'] ?>"><i class="fa-solid fa-eye me-2 w-15px"></i> Chi tiết</a></li>
                        <?php if ($user['role'] === 'admin'): ?>
                        <li><a class="dropdown-item py-2 text-warning" href="index.php?page=departments&action=edit&id=<?= $dept['id'] ?>"><i class="fa-solid fa-pen me-2 w-15px"></i> Chỉnh sửa</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <button class="dropdown-item py-2 text-danger" onclick="confirmDelete('index.php?page=departments&action=delete&id=<?= $dept['id'] ?>')">
                                <i class="fa-solid fa-trash me-2 w-15px"></i> Xóa khoa
                            </button>
                        </li>
                        <?php endif; ?>
                    </ul>
                </div>
            </div>
            
            <div class="card-body px-4 pt-3 pb-4">
                <h5 class="card-title fw-bold text-dark mb-2" style="font-size: 1.1rem; line-height: 1.4;"><?= htmlspecialchars($dept['name']) ?></h5>
                <p class="card-text text-muted small" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; height: 40px; margin-bottom: 20px;">
                    <?= htmlspecialchars($dept['description'] ?: 'Chưa có mô tả cho khoa này.') ?>
                </p>
                
                <div class="d-flex gap-2">
                    <span class="badge" style="background: rgba(25, 135, 84, 0.1); color: #198754; font-weight: 500; padding: 8px 12px; border-radius: 6px;">
                        <i class="fa-solid fa-user-doctor me-1"></i> <?= $dept['doctor_count'] ?? 0 ?> Bác sĩ
                    </span>
                    <span class="badge" style="background: rgba(13, 202, 240, 0.1); color: #0dcaf0; font-weight: 500; padding: 8px 12px; border-radius: 6px;">
                        <i class="fa-solid fa-user-nurse me-1"></i> <?= $dept['nurse_count'] ?? 0 ?> Y tá
                    </span>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
    
    <?php if (empty($departments)): ?>
    <div class="col-12" data-aos="fade-up">
        <div class="text-center py-5 bg-white shadow-sm" style="border-radius: 16px; border: 1px dashed #dee2e6;">
            <div class="mb-3 text-muted">
                <i class="fa-regular fa-folder-open" style="font-size: 4rem; opacity: 0.5;"></i>
            </div>
            <h5 class="text-secondary fw-bold">Không tìm thấy khoa nào</h5>
            <p class="text-muted small mb-4">Vui lòng thử lại với từ khóa khác hoặc xóa bộ lọc.</p>
            <a href="index.php?page=departments" class="btn btn-outline-primary btn-sm rounded-pill px-4">Xóa bộ lọc</a>
        </div>
    </div>
    <?php endif; ?>
</div>

<style>
.department-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
}
.w-15px { width: 15px; text-align: center; }
.focus-none:focus { box-shadow: none !important; border-color: #dee2e6 !important; }
.form-select:focus { border-color: #dee2e6 !important; box-shadow: none !important; }
.form-check-input:checked { background-color: var(--bs-primary); border-color: var(--bs-primary); }
</style>
