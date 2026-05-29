<?php
// Kiểm tra xem có $editUser được truyền từ controller không
if (!isset($editUser)) {
    echo "Lỗi: Không tìm thấy dữ liệu tài khoản.";
    exit;
}
?>

<!-- Form Sửa Tài khoản -->
<div class="form-section p-4 shadow-sm border-0 bg-white" style="border-radius: 16px;" data-aos="fade-up">
    <div class="d-flex align-items-center gap-3 mb-4">
        <div class="d-flex align-items-center justify-content-center text-primary" style="width: 48px; height: 48px; border-radius: 12px; background: rgba(59, 130, 246, 0.08); font-size: 20px;">
            <i class="fa-solid fa-user-pen"></i>
        </div>
        <div>
            <h5 class="m-0 fw-bold text-dark">Cập nhật Tài khoản #<?= $editUser['id'] ?></h5>
            <small class="text-muted">Thay đổi thông tin liên lạc, thiết lập lại mật khẩu hoặc vai trò của tài khoản</small>
        </div>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-custom alert-dismissible fade show">
            <i class="fa-solid fa-triangle-exclamation"></i> <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form action="index.php?page=users&action=update" method="POST">
        <input type="hidden" name="csrf_token" value="<?= Security::getCsrfToken() ?>">
        <input type="hidden" name="id" value="<?= $editUser['id'] ?>">
        
        <!-- THÔNG TIN TÀI KHOẢN CƠ BẢN -->
        <h6 class="fw-bold mb-3 text-secondary border-bottom pb-2" style="font-size: 14px;"><i class="fa-solid fa-shield-halved me-1.5"></i>Thông tin tài khoản cơ bản</h6>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label fw-semibold text-secondary" style="font-size: 13px;">Họ và tên <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" class="form-control py-2.5" required value="<?= htmlspecialchars($editUser['name']) ?>" style="border-radius: 10px;">
            </div>
            <div class="col-md-6 mb-3">
                <label for="role" class="form-label fw-semibold text-secondary" style="font-size: 13px;">Vai trò hệ thống <span class="text-danger">*</span></label>
                <select name="role" id="role" class="form-select py-2.5" required style="border-radius: 10px;">
                    <?php
                    $roles = [
                        'admin'        => 'Quản trị viên (Admin)',
                        'receptionist' => 'Lễ tân (Receptionist)',
                        'cashier'      => 'Thu ngân (Cashier)',
                        'pharmacist'   => 'Dược sĩ (Pharmacist)',
                        'technician'   => 'Kỹ thuật viên (Technician)',
                        'director'     => 'Ban giám đốc (Director)',
                        'doctor'       => 'Bác sĩ (Doctor)',
                        'nurse'        => 'Điều dưỡng (Nurse)',
                        'patient'      => 'Bệnh nhân (Patient)',
                    ];
                    foreach ($roles as $val => $label) {
                        $selected = ($editUser['role'] === $val) ? 'selected' : '';
                        echo "<option value=\"$val\" $selected>$label</option>";
                    }
                    ?>
                </select>
                <div class="form-text text-warning mt-1.5" style="font-size: 11px;"><i class="fa-solid fa-triangle-exclamation me-1"></i>Đổi vai trò sẽ lập tức ảnh hưởng đến phân quyền truy cập và dữ liệu vai trò đặc thù.</div>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="email" class="form-label fw-semibold text-secondary" style="font-size: 13px;">Địa chỉ Email <span class="text-danger">*</span></label>
                <input type="email" name="email" id="email" class="form-control py-2.5" required value="<?= htmlspecialchars($editUser['email']) ?>" style="border-radius: 10px;">
            </div>
            <div class="col-md-6 mb-3">
                <label for="phone" class="form-label fw-semibold text-secondary" style="font-size: 13px;">Số điện thoại liên hệ</label>
                <input type="text" name="phone" id="phone" class="form-control py-2.5" value="<?= htmlspecialchars($editUser['phone']) ?>" style="border-radius: 10px;">
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 mb-4">
                <label for="password" class="form-label fw-semibold text-secondary" style="font-size: 13px;">Thiết lập lại mật khẩu mới</label>
                <input type="password" name="password" id="password" class="form-control py-2.5" minlength="6" placeholder="Để trống nếu giữ nguyên mật khẩu cũ" style="border-radius: 10px;">
                <div class="form-text mt-1.5"><i class="fa-solid fa-info-circle me-1 text-muted"></i>Chỉ nhập nếu bạn muốn thay đổi mật khẩu đăng nhập cho người dùng này.</div>
            </div>
        </div>

        <!-- THÔNG TIN ĐẶC THÙ THEO VAI TRÒ (DYNAMIC FIELDS) -->
        
        <!-- 1. BÁC SĨ -->
        <div id="doctor-fields" class="role-fields-container mb-4 p-3 bg-light rounded-3" style="display: none;">
            <h6 class="fw-bold mb-3 text-primary border-bottom pb-2" style="font-size: 14px;"><i class="fa-solid fa-user-doctor me-1.5"></i>Thông tin bác sĩ chuyên môn</h6>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="specialty" class="form-label fw-semibold text-secondary" style="font-size: 13px;">Chuyên khoa</label>
                    <input type="text" name="specialty" id="specialty" class="form-control py-2.5" value="<?= htmlspecialchars($roleData['specialty'] ?? '') ?>" style="border-radius: 10px;">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="experience_years" class="form-label fw-semibold text-secondary" style="font-size: 13px;">Số năm kinh nghiệm</label>
                    <input type="number" name="experience_years" id="experience_years" class="form-control py-2.5" min="0" value="<?= intval($roleData['experience_years'] ?? 0) ?>" style="border-radius: 10px;">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold text-secondary" style="font-size: 13px;">Khoa công tác <span class="text-danger">*</span></label>
                <div class="d-flex flex-wrap gap-2 pt-1">
                    <?php foreach ($departments as $dept): ?>
                        <?php
                        $isDocRole = ($editUser['role'] === 'doctor');
                        $checked = ($isDocRole && !empty($roleData['department_ids']) && in_array($dept['id'], $roleData['department_ids'])) ? 'checked' : '';
                        ?>
                        <div>
                            <input type="checkbox" class="btn-check" id="dept_<?= $dept['id'] ?>" name="department_ids[]" value="<?= $dept['id'] ?>" <?= $checked ?>>
                            <label class="btn btn-outline-primary px-3 py-2" for="dept_<?= $dept['id'] ?>" style="border-radius: 8px; font-weight: 500; font-size: 13px; transition: all 0.2s;">
                                <i class="fa-solid fa-hospital-user me-1"></i> <?= htmlspecialchars($dept['name']) ?>
                            </label>
                        </div>
                    <?php endforeach; ?>
                </div>
                <small class="text-muted mt-2 d-block" style="font-size: 11px;">Bác sĩ có thể trực hoặc công tác ở nhiều khoa cùng lúc.</small>
            </div>
        </div>

        <!-- 2. Y TÁ / ĐIỀU DƯỠNG -->
        <div id="nurse-fields" class="role-fields-container mb-4 p-3 bg-light rounded-3" style="display: none;">
            <h6 class="fw-bold mb-3 text-teal border-bottom pb-2" style="font-size: 14px; color: #0d9488;"><i class="fa-solid fa-user-nurse me-1.5"></i>Thông tin khoa trực thuộc</h6>
            <div class="mb-3">
                <label for="nurse_dept" class="form-label fw-semibold text-secondary" style="font-size: 13px;">Khoa công tác <span class="text-danger">*</span></label>
                <select name="department_id" id="nurse_dept" class="form-select py-2.5" style="border-radius: 10px;">
                    <option value="">-- Chọn khoa công tác --</option>
                    <?php foreach ($departments as $dept): ?>
                        <?php
                        $isNurseRole = ($editUser['role'] === 'nurse');
                        $selected = ($isNurseRole && ($roleData['department_id'] ?? null) == $dept['id']) ? 'selected' : '';
                        ?>
                        <option value="<?= $dept['id'] ?>" <?= $selected ?>><?= htmlspecialchars($dept['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
        </div>

        <!-- 3. KỸ THUẬT VIÊN -->
        <div id="tech-fields" class="role-fields-container mb-4 p-3 bg-light rounded-3" style="display: none;">
            <h6 class="fw-bold mb-3 text-violet border-bottom pb-2" style="font-size: 14px; color: #8b5cf6;"><i class="fa-solid fa-flask-vial me-1.5"></i>Thông tin kỹ thuật chuyên khoa</h6>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="tech_specialty" class="form-label fw-semibold text-secondary" style="font-size: 13px;">Chuyên môn kỹ thuật</label>
                    <input type="text" name="specialty" id="tech_specialty" class="form-control py-2.5" value="<?= htmlspecialchars($roleData['specialty'] ?? '') ?>" style="border-radius: 10px;">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="tech_dept" class="form-label fw-semibold text-secondary" style="font-size: 13px;">Khoa công tác <span class="text-danger">*</span></label>
                    <select name="department_id" id="tech_dept" class="form-select py-2.5" style="border-radius: 10px;">
                        <option value="">-- Chọn khoa công tác --</option>
                        <?php foreach ($departments as $dept): ?>
                            <?php
                            $isTechRole = ($editUser['role'] === 'technician');
                            $selected = ($isTechRole && ($roleData['department_id'] ?? null) == $dept['id']) ? 'selected' : '';
                            ?>
                            <option value="<?= $dept['id'] ?>" <?= $selected ?>><?= htmlspecialchars($dept['name']) ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>
        </div>

        <!-- 4. BỆNH NHÂN -->
        <div id="patient-fields" class="role-fields-container mb-4 p-3 bg-light rounded-3" style="display: none;">
            <h6 class="fw-bold mb-3 text-success border-bottom pb-2" style="font-size: 14px;"><i class="fa-solid fa-hospital-user me-1.5"></i>Thông tin bệnh án hành chính</h6>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <label for="date_of_birth" class="form-label fw-semibold text-secondary" style="font-size: 13px;">Ngày sinh <span class="text-danger">*</span></label>
                    <input type="date" name="date_of_birth" id="date_of_birth" class="form-control py-2.5" value="<?= htmlspecialchars($roleData['date_of_birth'] ?? '') ?>" style="border-radius: 10px;">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="gender" class="form-label fw-semibold text-secondary" style="font-size: 13px;">Giới tính <span class="text-danger">*</span></label>
                    <?php
                    $genderVal = $roleData['gender'] ?? 'male';
                    if ($genderVal === 'Nam') $genderVal = 'male';
                    elseif ($genderVal === 'Nữ') $genderVal = 'female';
                    elseif ($genderVal === 'Khác') $genderVal = 'other';
                    ?>
                    <select name="gender" id="gender" class="form-select py-2.5" style="border-radius: 10px;">
                        <option value="male" <?= $genderVal === 'male' ? 'selected' : '' ?>>Nam</option>
                        <option value="female" <?= $genderVal === 'female' ? 'selected' : '' ?>>Nữ</option>
                        <option value="other" <?= $genderVal === 'other' ? 'selected' : '' ?>>Khác</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="blood_type" class="form-label fw-semibold text-secondary" style="font-size: 13px;">Nhóm máu</label>
                    <select name="blood_type" id="blood_type" class="form-select py-2.5" style="border-radius: 10px;">
                        <option value="" <?= empty($roleData['blood_type']) ? 'selected' : '' ?>>Chưa xác định</option>
                        <option value="A" <?= ($roleData['blood_type'] ?? '') === 'A' ? 'selected' : '' ?>>A</option>
                        <option value="B" <?= ($roleData['blood_type'] ?? '') === 'B' ? 'selected' : '' ?>>B</option>
                        <option value="AB" <?= ($roleData['blood_type'] ?? '') === 'AB' ? 'selected' : '' ?>>AB</option>
                        <option value="O" <?= ($roleData['blood_type'] ?? '') === 'O' ? 'selected' : '' ?>>O</option>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label fw-semibold text-secondary" style="font-size: 13px;">Địa chỉ thường trú</label>
                <input type="text" name="address" id="address" class="form-control py-2.5" value="<?= htmlspecialchars($roleData['address'] ?? '') ?>" placeholder="Nhập địa chỉ..." style="border-radius: 10px;">
            </div>
            <div class="mb-3">
                <label for="medical_history" class="form-label fw-semibold text-secondary" style="font-size: 13px;">Tiền sử bệnh lý</label>
                <textarea name="medical_history" id="medical_history" class="form-control py-2" rows="3" placeholder="Nhập tiền sử bệnh lý..." style="border-radius: 10px;"><?= htmlspecialchars($roleData['medical_history'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="d-flex gap-3">
            <button type="submit" class="btn-submit px-4 py-2.5 d-inline-flex align-items-center gap-2" style="border-radius: 10px; font-weight: 600;">
                <i class="fa-solid fa-save"></i>
                Cập nhật tài khoản
            </button>
            <a href="index.php?page=users" class="btn-cancel px-4 py-2.5 d-inline-flex align-items-center gap-2" style="border-radius: 10px; font-weight: 600;">
                <i class="fa-solid fa-arrow-left"></i>
                Quay lại
            </a>
        </div>
    </form>
</div>

<!-- JavaScript to show/hide dynamic fields based on role -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const roleSelect = document.getElementById('role');
    const doctorFields = document.getElementById('doctor-fields');
    const nurseFields = document.getElementById('nurse-fields');
    const techFields = document.getElementById('tech-fields');
    const patientFields = document.getElementById('patient-fields');

    function toggleFields() {
        const role = roleSelect.value;
        
        // Hide all
        doctorFields.style.display = 'none';
        nurseFields.style.display = 'none';
        techFields.style.display = 'none';
        patientFields.style.display = 'none';
        
        // Disable internal inputs of hidden fields so they are not sent or validated as required
        toggleInputs(doctorFields, false);
        toggleInputs(nurseFields, false);
        toggleInputs(techFields, false);
        toggleInputs(patientFields, false);

        // Show selected
        if (role === 'doctor') {
            doctorFields.style.display = 'block';
            toggleInputs(doctorFields, true);
        } else if (role === 'nurse') {
            nurseFields.style.display = 'block';
            toggleInputs(nurseFields, true);
        } else if (role === 'technician') {
            techFields.style.display = 'block';
            toggleInputs(techFields, true);
        } else if (role === 'patient') {
            patientFields.style.display = 'block';
            toggleInputs(patientFields, true);
        }
    }

    function toggleInputs(container, enable) {
        const inputs = container.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            if (enable) {
                input.removeAttribute('disabled');
                if (input.id === 'date_of_birth' || input.id === 'gender' || input.id === 'nurse_dept' || input.id === 'tech_dept') {
                    input.setAttribute('required', 'required');
                }
            } else {
                input.setAttribute('disabled', 'disabled');
                input.removeAttribute('required');
            }
        });
    }

    roleSelect.addEventListener('change', toggleFields);
    toggleFields(); // Run initially
});
</script>

