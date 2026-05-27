<!-- Form Thêm Tài khoản Mới -->
<div class="form-section p-4 shadow-sm border-0 bg-white" style="border-radius: 16px;" data-aos="fade-up">
    <div class="d-flex align-items-center gap-3 mb-4">
        <div class="d-flex align-items-center justify-content-center text-primary" style="width: 48px; height: 48px; border-radius: 12px; background: rgba(59, 130, 246, 0.08); font-size: 20px;">
            <i class="fa-solid fa-user-plus"></i>
        </div>
        <div>
            <h5 class="m-0 fw-bold text-dark">Thêm Tài khoản Người dùng Mới</h5>
            <small class="text-muted">Cấp phát tài khoản đăng nhập cho nhân sự mới của hệ thống</small>
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
                    <option value="doctor" disabled>Bác sĩ (Thêm ở trang Quản lý Bác sĩ)</option>
                    <option value="nurse" disabled>Điều dưỡng (Thêm ở trang Quản lý Điều dưỡng)</option>
                    <option value="patient" disabled>Bệnh nhân (Thêm ở trang Quản lý Bệnh nhân)</option>
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
