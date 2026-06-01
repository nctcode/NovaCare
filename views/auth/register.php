<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng ký tài khoản - NovaCare Smart Hospital</title>
    <meta name="description" content="Đăng ký tài khoản bệnh nhân hệ thống NovaCare">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <style>
        .login-card {
            max-width: 500px; /* Wider for register form */
            padding: 30px 40px;
        }
    </style>
</head>
<body>
    <div class="login-page">
        <div class="login-card">
            <div class="logo">
                <i class="fa-solid fa-hospital"></i>
                <h2>NovaCare Hospital</h2>
                <p>Tạo tài khoản Bệnh nhân mới</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger alert-custom alert-dismissible fade show" role="alert">
                    <i class="fa-solid fa-triangle-exclamation"></i>
                    <?= htmlspecialchars($error) ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="POST" action="index.php?page=register" id="registerForm">
                <?php echo Security::csrfField(); ?>
                
                <div class="mb-3">
                    <label for="name" class="form-label fw-semibold">Họ và Tên</label>
                    <div class="input-group">
                        <span class="input-group-text" style="border-radius:10px 0 0 10px;border:2px solid var(--gray-200);border-right:0;background:var(--gray-50);">
                            <i class="fa-solid fa-user" style="color:var(--gray-400);"></i>
                        </span>
                        <input type="text" class="form-control" id="name" name="name" 
                               placeholder="Nhập họ và tên..." required
                               value="<?= htmlspecialchars($_POST['name'] ?? '') ?>"
                               style="border-left:0;">
                    </div>
                </div>

                <div class="mb-3">
                    <label for="phone" class="form-label fw-semibold">Số điện thoại</label>
                    <div class="input-group">
                        <span class="input-group-text" style="border-radius:10px 0 0 10px;border:2px solid var(--gray-200);border-right:0;background:var(--gray-50);">
                            <i class="fa-solid fa-phone" style="color:var(--gray-400);"></i>
                        </span>
                        <input type="tel" class="form-control" id="phone" name="phone" 
                               placeholder="Nhập số điện thoại..." required
                               value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>"
                               style="border-left:0;">
                    </div>
                </div>

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

                <div class="row">
                    <div class="col-md-6 mb-4">
                        <label for="password" class="form-label fw-semibold">Mật khẩu</label>
                        <div class="input-group">
                            <span class="input-group-text" style="border-radius:10px 0 0 10px;border:2px solid var(--gray-200);border-right:0;background:var(--gray-50);">
                                <i class="fa-solid fa-lock" style="color:var(--gray-400);"></i>
                            </span>
                            <input type="password" class="form-control" id="password" name="password" 
                                   placeholder="Tối thiểu 6 ký tự" required minlength="6"
                                   style="border-left:0;">
                        </div>
                    </div>

                    <div class="col-md-6 mb-4">
                        <label for="confirm_password" class="form-label fw-semibold">Xác nhận MK</label>
                        <div class="input-group">
                            <span class="input-group-text" style="border-radius:10px 0 0 10px;border:2px solid var(--gray-200);border-right:0;background:var(--gray-50);">
                                <i class="fa-solid fa-lock" style="color:var(--gray-400);"></i>
                            </span>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                                   placeholder="Nhập lại mật khẩu" required minlength="6"
                                   style="border-left:0;">
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-login" id="btnRegister">
                    <i class="fa-solid fa-user-plus"></i> Đăng ký ngay
                </button>
            </form>

            <div class="mt-4 text-center">
                <span style="color:var(--gray-400);">Đã có tài khoản?</span>
                <a href="index.php?page=login" style="color:var(--primary);font-weight:600;margin-left:5px;">
                    Đăng nhập
                </a>
            </div>

            <div class="mt-3 text-center">
                <a href="index.php?page=home" style="color:var(--gray-400);font-size:14px;font-weight:500;">
                    <i class="fa-solid fa-arrow-left"></i> Về trang chủ
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('registerForm').addEventListener('submit', function(e) {
            const pass = document.getElementById('password').value;
            const confirm = document.getElementById('confirm_password').value;
            if (pass !== confirm) {
                e.preventDefault();
                alert('Mật khẩu và xác nhận mật khẩu không khớp!');
            }
        });
    </script>
</body>
</html>
