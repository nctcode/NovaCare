<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - NovaCare Smart Hospital</title>
    <meta name="description" content="Đăng nhập vào hệ thống quản lý NovaCare Smart Hospital">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
</head>
<body>
    <div class="login-page">
        <div class="login-card">
            <div class="logo">
                <i class="fa-solid fa-hospital"></i>
                <h2>NovaCare Hospital</h2>
                <p>Smart Hospital Management System</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-custom alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="POST" action="index.php?page=login" id="loginForm">
                            <?php echo Security::csrfField(); ?>
                <div class="mb-3">
                    <label for="email" class="form-label fw-semibold">Email</label>
                    <div class="input-group">
                        <span class="input-group-text" style="border-radius:10px 0 0 10px;border:2px solid var(--gray-200);border-right:0;background:var(--gray-50);">
                            <i class="fa-solid fa-envelope" style="color:var(--gray-400);"></i>
                        </span>
                        <input type="email" class="form-control" id="email" name="email" 
                               placeholder="Nhập email..." required
                               value="<?= htmlspecialchars($_POST['email'] ?? '') ?>"
                               style="border-left:0;">
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label fw-semibold">Mật khẩu</label>
                    <div class="input-group">
                        <span class="input-group-text" style="border-radius:10px 0 0 10px;border:2px solid var(--gray-200);border-right:0;background:var(--gray-50);">
                            <i class="fa-solid fa-lock" style="color:var(--gray-400);"></i>
                        </span>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Nhập mật khẩu..." required
                               style="border-left:0;">
                    </div>
                </div>

                <button type="submit" class="btn-login" id="btnLogin">
                    <i class="fa-solid fa-right-to-bracket"></i> Đăng nhập
                </button>
            </form>

            <div class="mt-4 text-center">
                <small style="color:var(--gray-400); line-height: 1.8;">
                    <strong>Tài khoản mẫu (Mật khẩu chung: 123456):</strong><br>
                    <span class="badge bg-danger">Admin</span> admin@benhvien.com<br>
                    <span class="badge bg-primary">Bác sĩ</span> doctor1@benhvien.com<br>
                    <span class="badge bg-info text-dark">Y tá</span> nurse1@benhvien.com<br>
                    <span class="badge bg-warning text-dark">Lễ tân</span> letan@benhvien.com<br>
                    <span class="badge bg-success">Dược sĩ</span> duocsi@benhvien.com<br>
                    <span class="badge bg-secondary">Bệnh nhân</span> benhnhan1@gmail.com<br>
                    <span class="badge text-white" style="background-color: #6f42c1;">Kỹ thuật viên</span> ktv@benhvien.com<br>
                    <span class="badge text-white" style="background-color: #e83e8c;">Thu ngân</span> thungan@benhvien.com<br>
                    <span class="badge text-white" style="background-color: #fd7e14;">Ban giám đốc</span> giamdoc@benhvien.com
                </small>
            </div>

            <div class="mt-3 text-center">
                <a href="index.php?page=home" style="color:var(--primary);font-size:14px;font-weight:500;">
                    <i class="fa-solid fa-arrow-left"></i> Về trang chủ
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

