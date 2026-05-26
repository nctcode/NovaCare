<?php
/**
 * Form cập nhật tài khoản
 */
require_once 'views/layout/header.php';

// Kiểm tra xem có $editUser được truyền từ controller không
if (!isset($editUser)) {
    echo "Lỗi: Không tìm thấy dữ liệu tài khoản.";
    exit;
}
?>

<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Cập nhật Tài khoản #<?= $editUser['id'] ?></h5>
                <a href="index.php?page=users" class="btn btn-secondary btn-sm">
                    <i class="fa-solid fa-arrow-left me-1"></i>Quay lại
                </a>
            </div>
            <div class="card-body">
                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <?= htmlspecialchars($_SESSION['error']) ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <form action="index.php?page=users&action=update" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= Security::getCsrfToken() ?>">
                    <input type="hidden" name="id" value="<?= $editUser['id'] ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required value="<?= htmlspecialchars($editUser['name']) ?>">
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($editUser['email']) ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control" value="<?= htmlspecialchars($editUser['phone']) ?>">
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Mật khẩu mới</label>
                            <input type="password" name="password" class="form-control" minlength="6" placeholder="Để trống nếu không đổi">
                            <div class="form-text">Nhập mật khẩu mới nếu muốn thay đổi.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Vai trò <span class="text-danger">*</span></label>
                            <select name="role" class="form-select" required>
                                <?php
                                $roles = [
                                    'admin' => 'Quản trị viên (Admin)',
                                    'receptionist' => 'Lễ tân (Receptionist)',
                                    'pharmacist' => 'Dược sĩ (Pharmacist)',
                                    'doctor' => 'Bác sĩ (Doctor)',
                                    'nurse' => 'Y tá (Nurse)',
                                    'patient' => 'Bệnh nhân (Patient)',
                                ];
                                foreach ($roles as $val => $label) {
                                    $selected = ($editUser['role'] === $val) ? 'selected' : '';
                                    echo "<option value=\"$val\" $selected>$label</option>";
                                }
                                ?>
                            </select>
                            <div class="form-text text-warning"><i class="fa-solid fa-triangle-exclamation me-1"></i>Đổi vai trò có thể ảnh hưởng đến quyền truy cập hiện tại.</div>
                        </div>
                    </div>

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-save me-1"></i>Lưu thay đổi
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once 'views/layout/footer.php'; ?>
