<!-- Form Sửa Bệnh nhân -->
<div class="form-section">
    <h5 class="mb-4"><i class="bi bi-pencil-square me-2"></i>Sửa Bệnh nhân</h5>

    <form method="POST" action="index.php?page=patients&action=update" id="editPatientForm">
        <input type="hidden" name="id" value="<?= $patient['id'] ?>">

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label">Họ tên <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" required 
                       value="<?= htmlspecialchars($patient['name']) ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" required 
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
            <label for="medical_history" class="form-label">Tiền sử bệnh</label>
            <textarea class="form-control" id="medical_history" name="medical_history" rows="3"><?= htmlspecialchars($patient['medical_history'] ?? '') ?></textarea>
        </div>

        <div class="d-flex gap-3">
            <button type="submit" class="btn-submit">
                <i class="bi bi-check-lg"></i> Cập nhật
            </button>
            <a href="index.php?page=patients" class="btn-cancel">
                <i class="bi bi-x-lg"></i> Hủy
            </a>
        </div>
    </form>
</div>
