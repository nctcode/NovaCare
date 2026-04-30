<!-- Form Sửa Y tá -->
<div class="form-section" data-aos="fade-up">
    <h5 class="mb-4"><i class="fa-solid fa-pen-to-square me-2"></i>Sửa thông tin Y tá</h5>

    <form method="POST" action="index.php?page=nurses&action=update">
                            <?php echo Security::csrfField(); ?>
        <input type="hidden" name="id" value="<?= $nurse['id'] ?>">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label">Họ tên <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" required value="<?= htmlspecialchars($nurse['name']) ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" required value="<?= htmlspecialchars($nurse['email']) ?>">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="phone" class="form-label">Số điện thoại</label>
                <input type="text" class="form-control" id="phone" name="phone" value="<?= htmlspecialchars($nurse['phone'] ?? '') ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label for="department_id" class="form-label">Phân khoa</label>
                <select class="form-select" id="department_id" name="department_id">
                    <option value="">-- Chọn khoa --</option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?= $dept['id'] ?>" <?= ($nurse['department_id'] == $dept['id']) ? 'selected' : '' ?>><?= htmlspecialchars($dept['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="d-flex gap-3 mt-3">
            <button type="submit" class="btn-submit"><i class="fa-solid fa-save me-2"></i>Cập nhật</button>
            <a href="index.php?page=nurses" class="btn-cancel"><i class="fa-solid fa-arrow-left me-2"></i>Quay lại</a>
        </div>
    </form>
</div>

