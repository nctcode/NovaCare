
<div class="row">
    <div class="col-md-8 mx-auto">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Thêm Tài khoản Mới</h5>
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

                <form action="index.php?page=users&action=store" method="POST">
                    <input type="hidden" name="csrf_token" value="<?= Security::getCsrfToken() ?>">
                    
                    <div class="mb-3">
                        <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" required placeholder="Ví dụ: Nguyễn Văn A">
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" required placeholder="email@example.com">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Số điện thoại</label>
                            <input type="text" name="phone" class="form-control" placeholder="09xxxxxxx">
                        </div>
                    </div>

                    <div class="row mb-4">
                        <div class="col-md-6">
                            <label class="form-label">Mật khẩu <span class="text-danger">*</span></label>
                            <input type="password" name="password" class="form-control" required minlength="6" placeholder="Ít nhất 6 ký tự">
                            <div class="form-text">Khuyên dùng mật khẩu mạnh bao gồm chữ và số.</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Vai trò <span class="text-danger">*</span></label>
                            <select name="role" class="form-select" required>
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

                    <div class="text-end">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-save me-1"></i>Lưu tài khoản
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

