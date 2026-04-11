<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giới thiệu - NovaCare Smart Hospital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/animations.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg nav-public">
    <div class="container">
        <a class="navbar-brand" href="index.php?page=home"><i class="fa-solid fa-hospital"></i><span>NovaCare</span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#publicNav"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="publicNav">
            <ul class="navbar-nav ms-auto me-3">
                <li class="nav-item"><a class="nav-link" href="index.php?page=home">Trang chủ</a></li>
                <li class="nav-item"><a class="nav-link active" href="index.php?page=about">Giới thiệu</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?page=services">Dịch vụ</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?page=public-doctors">Bác sĩ</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?page=contact">Liên hệ</a></li>
            </ul>
            <a href="index.php?page=login" class="btn-nav-login"><i class="fa-solid fa-right-to-bracket"></i> Đăng nhập</a>
        </div>
    </div>
</nav>

<!-- Hero About -->
<section style="padding:140px 0 80px; background:linear-gradient(135deg, var(--primary-50), #ecfdf5);">
    <div class="container text-center" data-aos="fade-up">
        <div class="section-badge"><i class="fa-solid fa-hospital"></i> Về chúng tôi</div>
        <h1 class="section-title" style="font-size:42px;">NovaCare Smart Hospital</h1>
        <p class="section-subtitle">Bệnh viện thông minh hàng đầu, ứng dụng công nghệ Y Tế Số 4.0</p>
    </div>
</section>

<!-- About Content -->
<section class="section-padding">
    <div class="container">
        <div class="row g-5 align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
                <img src="assets/images/hero-hospital.png" alt="NovaCare Hospital" style="border-radius:var(--radius-xl); box-shadow:var(--shadow-lg); width:100%;">
            </div>
            <div class="col-lg-6" data-aos="fade-left">
                <h3 style="font-weight:800; margin-bottom:20px;">Sứ mệnh của chúng tôi</h3>
                <p style="color:var(--gray-600); line-height:1.8; margin-bottom:20px;">
                    NovaCare Smart Hospital được thành lập với sứ mệnh mang đến dịch vụ chăm sóc sức khỏe 
                    chất lượng cao, ứng dụng công nghệ số hiện đại nhằm nâng cao trải nghiệm cho bệnh nhân.
                </p>
                <p style="color:var(--gray-600); line-height:1.8; margin-bottom:30px;">
                    Với đội ngũ bác sĩ chuyên khoa giàu kinh nghiệm và hệ thống trang thiết bị y tế tiên tiến, 
                    chúng tôi cam kết đồng hành cùng bạn trên hành trình bảo vệ sức khỏe.
                </p>
                <div class="row g-3">
                    <div class="col-6">
                        <div style="background:var(--primary-light);padding:20px;border-radius:var(--radius);text-align:center;">
                            <div style="font-size:30px;font-weight:800;color:var(--primary);"><?= $stats['doctors'] ?>+</div>
                            <div style="font-size:13px;color:var(--gray-600);">Bác sĩ</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div style="background:var(--secondary-light);padding:20px;border-radius:var(--radius);text-align:center;">
                            <div style="font-size:30px;font-weight:800;color:var(--secondary);"><?= $stats['departments'] ?></div>
                            <div style="font-size:13px;color:var(--gray-600);">Chuyên khoa</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Values -->
<section class="section-padding" style="background:var(--gray-50);">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <h2 class="section-title">Giá trị cốt lõi</h2>
        </div>
        <div class="row g-4">
            <div class="col-md-4" data-aos="fade-up">
                <div class="service-card text-center">
                    <div class="service-icon mx-auto" style="background:var(--primary-light);color:var(--primary);">
                        <i class="fa-solid fa-shield-heart"></i>
                    </div>
                    <h5>An toàn</h5>
                    <p>Đặt sự an toàn của bệnh nhân lên hàng đầu trong mọi quy trình điều trị.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
                <div class="service-card text-center">
                    <div class="service-icon mx-auto" style="background:var(--secondary-light);color:var(--secondary);">
                        <i class="fa-solid fa-handshake"></i>
                    </div>
                    <h5>Tận tâm</h5>
                    <p>Phục vụ bệnh nhân bằng cả tấm lòng, tận tâm và chuyên nghiệp.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
                <div class="service-card text-center">
                    <div class="service-icon mx-auto" style="background:var(--accent-light);color:var(--accent);">
                        <i class="fa-solid fa-microchip"></i>
                    </div>
                    <h5>Công nghệ</h5>
                    <p>Ứng dụng công nghệ hiện đại nhất vào chẩn đoán và điều trị.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Footer -->
<footer class="footer-public">
    <div class="container">
        <div class="footer-bottom" style="border:none;margin:0;padding:30px 0;">
            &copy; 2026 NovaCare Smart Hospital. All rights reserved.
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/dashboard.js"></script>
</body>
</html>
