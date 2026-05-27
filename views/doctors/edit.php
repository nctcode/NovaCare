<!-- Form Sửa Bác sĩ -->
<div class="form-section">
    <h5 class="mb-4"><i class="bi bi-pencil-square me-2"></i>Sửa thông tin Bác sĩ</h5>

    <form method="POST" action="index.php?page=doctors&action=update" id="editDoctorForm">
                            <?php echo Security::csrfField(); ?>
        <input type="hidden" name="id" value="<?= $doctor['id'] ?>">

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label">Họ tên <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" required 
                       value="<?= htmlspecialchars($doctor['name']) ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" required 
                       value="<?= htmlspecialchars($doctor['email']) ?>">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="phone" class="form-label">Số điện thoại</label>
                <input type="text" class="form-control" id="phone" name="phone" 
                       value="<?= htmlspecialchars($doctor['phone'] ?? '') ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label for="experience_years" class="form-label">Kinh nghiệm (năm)</label>
                <input type="number" class="form-control" id="experience_years" name="experience_years" 
                       min="0" value="<?= $doctor['experience_years'] ?? 0 ?>">
            </div>
        </div>

        <div class="mb-3">
            <label class="form-label d-block fw-semibold mb-2">Khoa công tác <span class="text-danger">*</span></label>
            <div class="d-flex flex-wrap gap-2">
                <?php foreach ($departments as $dept): ?>
                    <div>
                        <input type="checkbox" class="btn-check" id="dept_<?= $dept['id'] ?>" name="department_ids[]" value="<?= $dept['id'] ?>"
                               <?= in_array($dept['id'], $doctor['department_ids'] ?? []) ? 'checked' : '' ?>>
                        <label class="btn btn-outline-primary px-3 py-2" for="dept_<?= $dept['id'] ?>" style="border-radius: 8px; font-weight: 500; font-size: 13px; transition: all 0.2s;">
                            <i class="fa-solid fa-hospital-user me-1"></i> <?= htmlspecialchars($dept['name']) ?>
                        </label>
                    </div>
                <?php endforeach; ?>
            </div>
            <small class="text-muted mt-1 d-block" style="font-size: 11px;">Bác sĩ có thể trực hoặc công tác ở nhiều khoa cùng lúc.</small>
        </div>

        <div class="mb-4">
            <label for="specialty" class="form-label">Chuyên khoa</label>
            <input type="text" class="form-control" id="specialty" name="specialty" 
                   value="<?= htmlspecialchars($doctor['specialty'] ?? '') ?>">
        </div>

        <div class="d-flex gap-3">
            <button type="submit" class="btn-submit">
                <i class="bi bi-check-lg"></i> Cập nhật
            </button>
            <a href="index.php?page=doctors" class="btn-cancel">
                <i class="bi bi-x-lg"></i> Hủy
            </a>
        </div>
    </form>
</div>

