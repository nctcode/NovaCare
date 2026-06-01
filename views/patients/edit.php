<!-- Form Sửa Bệnh nhân -->
<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4">
        <h4 class="text-primary fw-bold mb-0"><i class="fa-solid fa-user-pen me-2"></i>Sửa Hồ Sơ Bệnh Nhân</h4>
        <p class="text-muted small mt-1 mb-0">Cập nhật thông tin chi tiết cho bệnh nhân.</p>
    </div>
    <div class="card-body p-4">
        <form method="POST" action="index.php?page=patients&action=update" id="editPatientForm">
            <?php echo Security::csrfField(); ?>
            <input type="hidden" name="id" value="<?= $patient['id'] ?>">

            <h6 class="text-uppercase text-secondary fw-bold mb-3 mt-2" style="font-size: 0.85rem; letter-spacing: 0.5px;">Thông tin cá nhân</h6>
            
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label for="name" class="form-label fw-medium text-dark">Họ tên <span class="text-danger">*</span></label>
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-regular fa-user"></i></span>
                        <input type="text" class="form-control border-start-0 ps-0" id="name" name="name" required 
                            value="<?= htmlspecialchars($patient['name']) ?>" placeholder="Nhập họ tên...">
                    </div>
                </div>
                <div class="col-md-6">
                    <label for="email" class="form-label fw-medium text-dark">Email <span class="text-muted fw-normal">(Tùy chọn)</span></label>
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-regular fa-envelope"></i></span>
                        <input type="email" class="form-control border-start-0 ps-0" id="email" name="email" 
                            value="<?= htmlspecialchars($patient['email']) ?>" placeholder="Nhập email nếu có...">
                    </div>
                </div>
                
                <div class="col-md-4">
                    <label for="phone" class="form-label fw-medium text-dark">Số điện thoại</label>
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-phone"></i></span>
                        <input type="text" class="form-control border-start-0 ps-0" id="phone" name="phone" 
                            value="<?= htmlspecialchars($patient['phone'] ?? '') ?>" placeholder="09xxxxxxxx">
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="date_of_birth" class="form-label fw-medium text-dark">Ngày sinh</label>
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-regular fa-calendar"></i></span>
                        <input type="date" class="form-control border-start-0 ps-0" id="date_of_birth" name="date_of_birth" 
                            value="<?= $patient['date_of_birth'] ?? '' ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <label for="gender" class="form-label fw-medium text-dark">Giới tính</label>
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-venus-mars"></i></span>
                        <select class="form-select border-start-0 ps-0" id="gender" name="gender">
                            <option value="">-- Chọn --</option>
                            <option value="male" <?= ($patient['gender'] ?? '') === 'male' ? 'selected' : '' ?>>Nam</option>
                            <option value="female" <?= ($patient['gender'] ?? '') === 'female' ? 'selected' : '' ?>>Nữ</option>
                            <option value="other" <?= ($patient['gender'] ?? '') === 'other' ? 'selected' : '' ?>>Khác</option>
                        </select>
                    </div>
                </div>
            </div>

            <h6 class="text-uppercase text-secondary fw-bold mb-3 mt-4" style="font-size: 0.85rem; letter-spacing: 0.5px;">Thông tin y tế & Liên hệ</h6>
            
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label for="blood_type" class="form-label fw-medium text-dark">Nhóm máu</label>
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0 text-danger"><i class="fa-solid fa-droplet"></i></span>
                        <select class="form-select border-start-0 ps-0" id="blood_type" name="blood_type">
                            <option value="">-- Chọn --</option>
                            <option value="" <?= empty($patient['blood_type']) ? 'selected' : '' ?>>Không rõ</option>
                            <?php 
                            $bloodTypes = ['A+','A-','B+','B-','O+','O-','AB+','AB-'];
                            foreach ($bloodTypes as $bt): ?>
                                <option value="<?= $bt ?>" <?= ($patient['blood_type'] ?? '') === $bt ? 'selected' : '' ?>>
                                    <?= $bt ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-8">
                    <label for="address" class="form-label fw-medium text-dark">Địa chỉ</label>
                    <div class="input-group shadow-sm">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-map-location-dot"></i></span>
                        <input type="text" class="form-control border-start-0 ps-0" id="address" name="address" 
                            value="<?= htmlspecialchars($patient['address'] ?? '') ?>" placeholder="Nhập địa chỉ thường trú...">
                    </div>
                </div>
                
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <label for="medical_history" class="form-label fw-medium text-dark mb-0">Tiền sử bệnh</label>
                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 voice-input-btn shadow-sm fw-medium" data-target="medical_history" title="Nhập liệu bằng giọng nói">
                            <i class="fa-solid fa-microphone me-1"></i> Đọc chính tả
                        </button>
                    </div>
                    <textarea class="form-control shadow-sm" id="medical_history" name="medical_history" rows="3" placeholder="Mô tả tiền sử bệnh, dị ứng thuốc (nếu có)..."><?= htmlspecialchars($patient['medical_history'] ?? '') ?></textarea>
                </div>
            </div>

            <hr class="my-4 text-muted">

            <div class="d-flex justify-content-end gap-3">
                <a href="index.php?page=patients" class="btn btn-light rounded-pill px-4 shadow-sm border">
                    <i class="fa-solid fa-xmark me-2"></i> Hủy bỏ
                </a>
                <button type="submit" class="btn btn-primary rounded-pill px-5 shadow">
                    <i class="fa-solid fa-check me-2"></i> Cập nhật Hồ Sơ
                </button>
            </div>
        </form>
    </div> <!-- end card-body -->
</div> <!-- end card -->

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-generate email if empty before form submit
    const editPatientForm = document.getElementById('editPatientForm');
    if (editPatientForm) {
        editPatientForm.addEventListener('submit', function(e) {
            const emailInput = document.getElementById('email');
            if (!emailInput.value.trim()) {
                const phone = document.getElementById('phone').value.trim();
                // Generate a dummy email to satisfy backend validation if required
                emailInput.value = (phone ? phone : 'bn_' + Date.now()) + '@novacare.local';
            }
        });
    }
});
</script>

