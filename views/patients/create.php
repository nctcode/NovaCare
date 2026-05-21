<!-- Form Thêm Bệnh nhân -->
<div class="form-section">
    <h5 class="mb-4"><i class="bi bi-person-plus-fill me-2"></i>Thêm Bệnh nhân mới</h5>

    <form method="POST" action="index.php?page=patients&action=store" id="createPatientForm">
                            <?php echo Security::csrfField(); ?>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label">Họ tên <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" required placeholder="Nhập họ tên...">
            </div>
            <div class="col-md-6 mb-3">
                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" required placeholder="Nhập email...">
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="phone" class="form-label">Số điện thoại</label>
                <input type="text" class="form-control" id="phone" name="phone" placeholder="09xxxxxxxx">
            </div>
            <div class="col-md-4 mb-3">
                <label for="date_of_birth" class="form-label">Ngày sinh</label>
                <input type="date" class="form-control" id="date_of_birth" name="date_of_birth">
            </div>
            <div class="col-md-4 mb-3">
                <label for="gender" class="form-label">Giới tính</label>
                <select class="form-select" id="gender" name="gender">
                    <option value="">-- Chọn --</option>
                    <option value="male">Nam</option>
                    <option value="female">Nữ</option>
                    <option value="other">Khác</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="blood_type" class="form-label">Nhóm máu</label>
                <select class="form-select" id="blood_type" name="blood_type">
                    <option value="">-- Chọn --</option>
                    <option value="A+">A+</option>
                    <option value="A-">A-</option>
                    <option value="B+">B+</option>
                    <option value="B-">B-</option>
                    <option value="O+">O+</option>
                    <option value="O-">O-</option>
                    <option value="AB+">AB+</option>
                    <option value="AB-">AB-</option>
                </select>
            </div>
            <div class="col-md-8 mb-3">
                <label for="address" class="form-label">Địa chỉ</label>
                <input type="text" class="form-control" id="address" name="address" placeholder="Nhập địa chỉ...">
            </div>
        </div>

        <div class="mb-4">
            <label for="medical_history" class="form-label">Tiền sử bệnh</label>
            <textarea class="form-control" id="medical_history" name="medical_history" rows="3" placeholder="Mô tả tiền sử bệnh (nếu có)..."></textarea>
        </div>

        <div class="d-flex gap-3">
            <button type="submit" class="btn-submit" id="btnSubmitPatient">
                <i class="bi bi-check-lg"></i> Lưu
            </button>
            <a href="index.php?page=patients" class="btn-cancel">
                <i class="bi bi-x-lg"></i> Hủy
            </a>
        </div>
    </form>
</div>

