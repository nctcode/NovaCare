<!-- Form Sửa Hồ sơ cá nhân -->
<div class="form-section">
    <h5 class="mb-4"><i class="fa-solid fa-user-pen me-2"></i>Tài khoản của tôi</h5>

    <form method="POST" action="index.php?page=patients&action=updateMyProfile" id="editProfileForm">
        <?php echo Security::csrfField(); ?>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label">Họ tên <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" required 
                       value="<?= htmlspecialchars($patient['name']) ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label for="email" class="form-label">Email (Dùng để đăng nhập)</label>
                <input type="email" class="form-control" id="email" name="email" readonly disabled
                       value="<?= htmlspecialchars($patient['email']) ?>">
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="phone" class="form-label">Số điện thoại</label>
                <input type="text" class="form-control" id="phone" name="phone" 
                       value="<?= htmlspecialchars($patient['phone'] ?? '') ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label for="date_of_birth" class="form-label">Ngày sinh</label>
                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth" 
                       value="<?= $patient['date_of_birth'] ?? '' ?>">
            </div>
            <div class="col-md-4 mb-3">
                <label for="gender" class="form-label">Giới tính</label>
                <select class="form-select" id="gender" name="gender">
                    <option value="">-- Chọn --</option>
                    <option value="male" <?= ($patient['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Nam</option>
                    <option value="female" <?= ($patient['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Nữ</option>
                    <option value="other" <?= ($patient['gender'] ?? '') === 'other' ? 'selected' : '' ?>>Khác</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="blood_type" class="form-label">Nhóm máu</label>
                <select class="form-select" id="blood_type" name="blood_type">
                    <option value="">-- Chọn --</option>
                    <?php 
                    $bloodTypes = ['A+','A-','B+','B-','O+','O-','AB+','AB-'];
                    foreach ($bloodTypes as $bt): ?>
                        <option value="<?= $bt ?>" <?= ($patient['blood_type'] ?? '') === $bt ? 'selected' : '' ?>>
                            <?= $bt ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-8 mb-3">
                <label for="address" class="form-label">Địa chỉ</label>
                <input type="text" class="form-control" id="address" name="address" 
                       value="<?= htmlspecialchars($patient['address'] ?? '') ?>">
            </div>
        </div>

        <div class="mb-4">
            <label for="medical_history" class="form-label">Tiền sử bệnh (Ghi chú từ Bác sĩ)</label>
            <textarea class="form-control" id="medical_history" name="medical_history" rows="3" readonly disabled><?= htmlspecialchars($patient['medical_history'] ?? '') ?></textarea>
            <small class="text-muted"><i class="fa-solid fa-lock me-1"></i>Chỉ bác sĩ mới có thể cập nhật thông tin này.</small>
        </div>

        <div class="d-flex gap-3">
            <button type="submit" class="btn btn-primary" style="border-radius:20px; padding:8px 24px;">
                <i class="fa-solid fa-check"></i> Lưu thay đổi
            </button>
            <a href="index.php?page=dashboard" class="btn btn-outline-secondary" style="border-radius:20px; padding:8px 24px;">
                <i class="fa-solid fa-xmark"></i> Hủy
            </a>
        </div>
    </form>
</div>
