<!-- Form Thêm Tài khoản Người dùng Mới -->
<div class="form-section p-4 shadow-sm border-0 bg-white" style="border-radius: 16px;" data-aos="fade-up">
    <div class="d-flex align-items-center gap-3 mb-4">
        <div class="d-flex align-items-center justify-content-center text-primary" style="width: 48px; height: 48px; border-radius: 12px; background: rgba(59, 130, 246, 0.08); font-size: 20px;">
            <i class="fa-solid fa-user-plus"></i>
        </div>
        <div>
            <h5 class="m-0 fw-bold text-dark">Thêm Người dùng Mới</h5>
            <small class="text-muted">Cấp phát tài khoản và cấu hình thông tin chuyên môn/hành chính theo vai trò</small>
        </div>
    </div>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger alert-custom alert-dismissible fade show">
            <i class="fa-solid fa-triangle-exclamation"></i> <?= $_SESSION['error']; unset($_SESSION['error']); ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <form action="index.php?page=users&action=store" method="POST">
        <input type="hidden" name="csrf_token" value="<?= Security::getCsrfToken() ?>">
        
        <!-- THÔNG TIN TÀI KHOẢN CƠ BẢN -->
        <h6 class="fw-bold mb-3 text-secondary border-bottom pb-2" style="font-size: 14px;"><i class="fa-solid fa-shield-halved me-1.5"></i>Thông tin tài khoản cơ bản</h6>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label fw-semibold text-secondary" style="font-size: 13px;">Họ và tên <span class="text-danger">*</span></label>
                <input type="text" name="name" id="name" class="form-control py-2.5" required placeholder="Ví dụ: Nguyễn Văn A" style="border-radius: 10px;">
            </div>
            <div class="col-md-6 mb-3">
                <label for="role" class="form-label fw-semibold text-secondary" style="font-size: 13px;">Vai trò hệ thống <span class="text-danger">*</span></label>
                <select name="role" id="role" class="form-select py-2.5" required style="border-radius: 10px;">
                    <option value="">-- Chọn vai trò --</option>
                    <option value="admin">Quản trị viên (Admin)</option>
                    <option value="receptionist">Lễ tân (Receptionist)</option>
                    <option value="cashier">Thu ngân (Cashier)</option>
                    <option value="pharmacist">Dược sĩ (Pharmacist)</option>
                    <option value="technician">Kỹ thuật viên (Technician)</option>
                    <option value="director">Ban giám đốc (Director)</option>
                    <option value="doctor">Bác sĩ (Doctor)</option>
                    <option value="nurse">Điều dưỡng (Nurse)</option>
                    <option value="patient">Bệnh nhân (Patient)</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="email" class="form-label fw-semibold text-secondary" style="font-size: 13px;">Địa chỉ Email <span class="text-danger">*</span></label>
                <input type="email" name="email" id="email" class="form-control py-2.5" required placeholder="email@example.com" style="border-radius: 10px;">
            </div>
            <div class="col-md-6 mb-3">
                <label for="phone" class="form-label fw-semibold text-secondary" style="font-size: 13px;">Số điện thoại liên hệ</label>
                <input type="text" name="phone" id="phone" class="form-control py-2.5" placeholder="09xxxxxxx" style="border-radius: 10px;">
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 mb-4">
                <label for="password" class="form-label fw-semibold text-secondary" style="font-size: 13px;">Mật khẩu tài khoản <span class="text-danger">*</span></label>
                <input type="password" name="password" id="password" class="form-control py-2.5" required minlength="6" placeholder="Ít nhất 6 ký tự..." style="border-radius: 10px;">
                <div class="form-text mt-1.5"><i class="fa-solid fa-info-circle me-1 text-muted"></i>Khuyên dùng mật khẩu mạnh bao gồm chữ viết hoa, chữ thường và chữ số để nâng cao bảo mật.</div>
            </div>
        </div>

        <!-- THÔNG TIN ĐẶC THÙ THEO VAI TRÒ (DYNAMIC FIELDS) -->
        
        <!-- 1. BÁC SĨ -->
        <div id="doctor-fields" class="role-fields-container mb-4 p-3 bg-light rounded-3" style="display: none;">
            <h6 class="fw-bold mb-3 text-primary border-bottom pb-2" style="font-size: 14px;"><i class="fa-solid fa-user-doctor me-1.5"></i>Thông tin bác sĩ chuyên môn</h6>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label for="specialty" class="form-label fw-semibold text-secondary" style="font-size: 13px;">Chuyên khoa</label>
                    <input type="text" name="specialty" id="specialty" class="form-control py-2.5" placeholder="VD: Tim mạch, Tai mũi họng" style="border-radius: 10px;">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="experience_years" class="form-label fw-semibold text-secondary" style="font-size: 13px;">Số năm kinh nghiệm</label>
                    <input type="number" name="experience_years" id="experience_years" class="form-control py-2.5" min="0" value="0" style="border-radius: 10px;">
                </div>
            </div>
            <div class="mb-3">
                <label class="form-label fw-semibold text-secondary" style="font-size: 13px;">Khoa công tác <span class="text-danger">*</span></label>
                <div class="d-flex flex-wrap gap-2 pt-1">
                    <?php foreach ($departments as $dept): ?>
                        <div>
                            <input type="checkbox" class="btn-check" id="dept_<?= $dept['id'] ?>" name="department_ids[]" value="<?= $dept['id'] ?>">
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
                        <option value="<?= $dept['id'] ?>"><?= htmlspecialchars($dept['name']) ?></option>
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
                    <input type="text" name="specialty" id="tech_specialty" class="form-control py-2.5" placeholder="VD: Xét nghiệm máu, Chụp X-Quang" style="border-radius: 10px;">
                </div>
                <div class="col-md-6 mb-3">
                    <label for="tech_dept" class="form-label fw-semibold text-secondary" style="font-size: 13px;">Khoa công tác <span class="text-danger">*</span></label>
                    <select name="department_id" id="tech_dept" class="form-select py-2.5" style="border-radius: 10px;">
                        <option value="">-- Chọn khoa công tác --</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?= $dept['id'] ?>"><?= htmlspecialchars($dept['name']) ?></option>
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
                    <input type="date" name="date_of_birth" id="date_of_birth" class="form-control py-2.5" style="border-radius: 10px;">
                </div>
                <div class="col-md-4 mb-3">
                    <label for="gender" class="form-label fw-semibold text-secondary" style="font-size: 13px;">Giới tính <span class="text-danger">*</span></label>
                    <select name="gender" id="gender" class="form-select py-2.5" style="border-radius: 10px;">
                        <option value="male">Nam</option>
                        <option value="female">Nữ</option>
                        <option value="other">Khác</option>
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label for="blood_type" class="form-label fw-semibold text-secondary" style="font-size: 13px;">Nhóm máu</label>
                    <select name="blood_type" id="blood_type" class="form-select py-2.5" style="border-radius: 10px;">
                        <option value="">Chưa xác định</option>
                        <option value="A">A</option>
                        <option value="B">B</option>
                        <option value="AB">AB</option>
                        <option value="O">O</option>
                    </select>
                </div>
            </div>
            <div class="mb-3">
                <label for="address" class="form-label fw-semibold text-secondary" style="font-size: 13px;">Địa chỉ thường trú</label>
                <input type="text" name="address" id="address" class="form-control py-2.5" placeholder="Nhập số nhà, tên đường, phường/xã, quận/huyện..." style="border-radius: 10px;">
            </div>
            <div class="mb-3">
                <label for="medical_history" class="form-label fw-semibold text-secondary" style="font-size: 13px;">Tiền sử bệnh lý</label>
                <textarea name="medical_history" id="medical_history" class="form-control py-2" rows="3" placeholder="Nhập tiền sử dị ứng thuốc, bệnh lý mãn tính, phẫu thuật trước đó..." style="border-radius: 10px;"></textarea>
            </div>
        </div>


        <div class="d-flex gap-3">
            <button type="submit" class="btn-submit px-4 py-2.5 d-inline-flex align-items-center gap-2" style="border-radius: 10px; font-weight: 600;">
                <i class="fa-solid fa-save"></i>
                Lưu tài khoản
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
                // Re-add required fields dynamically if needed
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
