<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đặt lịch khám - NovaCare Smart Hospital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/animations.css" rel="stylesheet">
</head>
<body>
<nav class="navbar navbar-expand-lg nav-public">
    <div class="container">
        <a class="navbar-brand" href="index.php?page=home"><i class="fa-solid fa-hospital"></i><span>NovaCare</span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#publicNav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="publicNav">
            <ul class="navbar-nav ms-auto me-3">
                <li class="nav-item"><a class="nav-link" href="index.php?page=home">Trang chủ</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?page=about">Giới thiệu</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?page=services">Dịch vụ</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?page=public-doctors">Bác sĩ</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?page=contact">Liên hệ</a></li>
            </ul>
            <a href="index.php?page=login" class="btn-nav-login"><i class="fa-solid fa-right-to-bracket"></i> Đăng nhập</a>
        </div>
    </div>
</nav>

<section style="padding:140px 0 80px; background:linear-gradient(135deg, var(--primary-50), #ecfdf5);">
    <div class="container text-center" data-aos="fade-up">
        <div class="section-badge"><i class="fa-solid fa-calendar-check"></i> Đặt lịch</div>
        <h1 class="section-title" style="font-size:42px;">Đặt lịch khám bệnh</h1>
        <p class="section-subtitle">Vui lòng đăng nhập để sử dụng hệ thống đặt lịch trực tuyến</p>
    </div>
</section>

<section class="section-padding">
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-lg-6" data-aos="fade-up">
                <div class="form-section" style="max-width:100%;text-align:center;">
                    <div style="font-size:64px;color:var(--primary);margin-bottom:20px;">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                    <h4 style="font-weight:700;margin-bottom:12px;">Đặt lịch khám online</h4>
                    <p style="color:var(--gray-500);margin-bottom:30px;">
                        Để đặt lịch khám, bạn cần đăng nhập vào hệ thống bằng tài khoản bệnh nhân. 
                        Nếu chưa có tài khoản, vui lòng liên hệ quầy tiếp nhận.
                    </p>
                    <a href="index.php?page=login" class="btn-hero-primary" style="display:inline-flex;">
                        <i class="fa-solid fa-right-to-bracket"></i> Đăng nhập để đặt lịch
                    </a>
                    <div class="mt-4">
                        <p style="font-size:13px;color:var(--gray-400);">
                            Hoặc gọi hotline: <strong style="color:var(--primary);">(028) 1234 5678</strong>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<footer class="footer-public">
    <div class="container"><div class="footer-bottom" style="border:none;margin:0;padding:30px 0;">&copy; 2026 NovaCare Smart Hospital. All rights reserved.</div></div>
</footer>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/dashboard.js"></script>
</body>
</html>
