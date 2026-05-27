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
                        'admin' => 'Quản trị viên (Admin)',
                        'receptionist' => 'Lễ tân (Receptionist)',
                        'cashier' => 'Thu ngân (Cashier)',
                        'pharmacist' => 'Dược sĩ (Pharmacist)',
                        'technician' => 'Kỹ thuật viên (Technician)',
                        'director' => 'Ban giám đốc (Director)',
                        'doctor' => 'Bác sĩ (Doctor)',
                        'nurse' => 'Điều dưỡng (Nurse)',
                        'patient' => 'Bệnh nhân (Patient)',
                    ];
                    foreach ($roles as $val => $label) {
                        $selected = ($editUser['role'] === $val) ? 'selected' : '';
                        echo "<option value=\"$val\" $selected>$label</option>";
                    }
                    ?>
                </select>
                <div class="form-text text-warning mt-1.5" style="font-size: 11px;"><i class="fa-solid fa-triangle-exclamation me-1"></i>Đổi vai trò sẽ lập tức ảnh hưởng đến phân quyền truy cập của tài khoản này.</div>
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
