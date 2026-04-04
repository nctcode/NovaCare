<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>NovaCare Smart Hospital - Digital Healthcare for a Better Future</title>
    <meta name="description" content="NovaCare Smart Hospital - Hệ thống bệnh viện thông minh, chăm sóc sức khỏe số hàng đầu">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/animations.css" rel="stylesheet">
</head>
<body>

<!-- ========== NAVBAR ========== -->
<nav class="navbar navbar-expand-lg nav-public">
    <div class="container">
        <a class="navbar-brand" href="index.php?page=home">
            <i class="fa-solid fa-hospital"></i>
            <span>NovaCare</span>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#publicNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="publicNav">
            <ul class="navbar-nav ms-auto me-3">
                <li class="nav-item"><a class="nav-link active" href="index.php?page=home">Trang chủ</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?page=about">Giới thiệu</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?page=services">Dịch vụ</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?page=public-doctors">Bác sĩ</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?page=contact">Liên hệ</a></li>
            </ul>
            <a href="index.php?page=login" class="btn-nav-login">
                <i class="fa-solid fa-right-to-bracket"></i> Đăng nhập
            </a>
        </div>
    </div>
</nav>

<!-- ========== HERO SECTION ========== -->
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-6" data-aos="fade-right">
                <div class="hero-content">
                    <div class="hero-badge">
                        <i class="fa-solid fa-circle-check"></i> Bệnh viện thông minh #1
                    </div>
                    <h1 class="hero-title">
                        <span class="text-gradient">NovaCare</span><br>
                        Smart Hospital
                    </h1>
                    <p class="hero-subtitle">
                        Digital Healthcare for a Better Future — Chăm sóc sức khỏe toàn diện 
                        với công nghệ y tế số hiện đại nhất.
                    </p>
                    <div class="hero-buttons">
                        <a href="index.php?page=book-appointment" class="btn-hero-primary">
                            <i class="fa-solid fa-calendar-check"></i> Đặt lịch khám
                        </a>
                        <a href="index.php?page=about" class="btn-hero-secondary">
                            <i class="fa-solid fa-play"></i> Tìm hiểu thêm
                        </a>
                    </div>
                </div>
            </div>
            <div class="col-lg-6" data-aos="fade-left" data-aos-delay="200">
                <div class="hero-image-wrapper">
                    <img src="assets/images/hero-hospital.png" alt="NovaCare Smart Hospital" 
                         style="width:100%; border-radius:20px;">
                    
                    <!-- Floating Cards -->
                    <div class="hero-float-card card-1" data-aos="zoom-in" data-aos-delay="500">
                        <div class="float-icon bg-primary"><i class="fa-solid fa-user-doctor"></i></div>
                        <div class="float-text">
                            <h6><?= $stats['doctors'] ?>+</h6>
                            <small>Bác sĩ chuyên khoa</small>
                        </div>
                    </div>
                    <div class="hero-float-card card-2" data-aos="zoom-in" data-aos-delay="600">
                        <div class="float-icon bg-secondary"><i class="fa-solid fa-heart-pulse"></i></div>
                        <div class="float-text">
                            <h6><?= $stats['patients'] ?>+</h6>
                            <small>Bệnh nhân tin tưởng</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========== STATS SECTION ========== -->
<section class="stats-section">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-3 col-6" data-aos="fade-up">
                <div class="stat-counter-card">
                    <div class="stat-counter-icon" style="background:var(--primary-light);color:var(--primary);">
                        <i class="fa-solid fa-user-doctor"></i>
                    </div>
                    <div class="stat-counter-value">
                        <span class="counter" data-target="<?= $stats['doctors'] ?>"><?= $stats['doctors'] ?></span>+
                    </div>
                    <div class="stat-counter-label">Bác sĩ</div>
                </div>
            </div>
            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="100">
                <div class="stat-counter-card">
                    <div class="stat-counter-icon" style="background:var(--secondary-light);color:var(--secondary);">
                        <i class="fa-solid fa-hospital-user"></i>
                    </div>
                    <div class="stat-counter-value">
                        <span class="counter" data-target="<?= $stats['patients'] ?>"><?= $stats['patients'] ?></span>+
                    </div>
                    <div class="stat-counter-label">Bệnh nhân</div>
                </div>
            </div>
            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="200">
                <div class="stat-counter-card">
                    <div class="stat-counter-icon" style="background:#fef3c7;color:var(--warning);">
                        <i class="fa-solid fa-building-columns"></i>
                    </div>
                    <div class="stat-counter-value">
                        <span class="counter" data-target="<?= $stats['departments'] ?>"><?= $stats['departments'] ?></span>
                    </div>
                    <div class="stat-counter-label">Chuyên khoa</div>
                </div>
            </div>
            <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="300">
                <div class="stat-counter-card">
                    <div class="stat-counter-icon" style="background:var(--accent-light);color:var(--accent);">
                        <i class="fa-solid fa-calendar-check"></i>
                    </div>
                    <div class="stat-counter-value">
                        <span class="counter" data-target="<?= $stats['appointments'] ?>"><?= $stats['appointments'] ?></span>+
                    </div>
                    <div class="stat-counter-label">Lượt khám</div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========== SERVICES SECTION ========== -->
<section class="section-padding" style="background:var(--gray-50);">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <div class="section-badge"><i class="fa-solid fa-stethoscope"></i> Dịch vụ y tế</div>
            <h2 class="section-title">Dịch vụ của chúng tôi</h2>
            <p class="section-subtitle">Cung cấp các dịch vụ chăm sóc sức khỏe toàn diện với trang thiết bị hiện đại nhất</p>
        </div>
        <div class="row g-4">
            <?php 
            $icons = ['fa-vial', 'fa-x-ray', 'fa-brain'];
            $colors = [
                ['bg' => 'var(--primary-light)', 'color' => 'var(--primary)'],
                ['bg' => 'var(--secondary-light)', 'color' => 'var(--secondary)'],
                ['bg' => 'var(--accent-light)', 'color' => 'var(--accent)'],
            ];
            foreach ($services as $idx => $svc): 
                $icon = $icons[$idx % count($icons)];
                $c = $colors[$idx % count($colors)];
            ?>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="<?= $idx * 100 ?>">
                <div class="service-card">
                    <div class="service-icon" style="background:<?= $c['bg'] ?>;color:<?= $c['color'] ?>;">
                        <i class="fa-solid <?= $icon ?>"></i>
                    </div>
                    <h5><?= htmlspecialchars($svc['service_name']) ?></h5>
                    <p><?= htmlspecialchars($svc['description'] ?? '') ?></p>
                    <p class="mt-2"><strong style="color:var(--primary);"><?= number_format($svc['price'], 0, ',', '.') ?> VNĐ</strong></p>
                </div>
            </div>
            <?php endforeach; ?>
            <!-- Extra service cards for visual balance -->
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="300">
                <div class="service-card">
                    <div class="service-icon" style="background:#fef3c7;color:var(--warning);">
                        <i class="fa-solid fa-heart-pulse"></i>
                    </div>
                    <h5>Khám tổng quát</h5>
                    <p>Kiểm tra sức khỏe toàn diện, phát hiện sớm các bệnh lý tiềm ẩn.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="400">
                <div class="service-card">
                    <div class="service-icon" style="background:#fee2e2;color:var(--danger);">
                        <i class="fa-solid fa-syringe"></i>
                    </div>
                    <h5>Tiêm chủng</h5>
                    <p>Dịch vụ tiêm chủng đầy đủ cho trẻ em và người lớn theo lịch trình chuẩn quốc tế.</p>
                </div>
            </div>
            <div class="col-md-4" data-aos="fade-up" data-aos-delay="500">
                <div class="service-card">
                    <div class="service-icon" style="background:#cffafe;color:var(--info);">
                        <i class="fa-solid fa-video"></i>
                    </div>
                    <h5>Tư vấn Online</h5>
                    <p>Kết nối với bác sĩ qua video call, tiện lợi và nhanh chóng ngay tại nhà.</p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ========== DOCTORS SECTION ========== -->
<section class="section-padding">
    <div class="container">
        <div class="section-header" data-aos="fade-up">
            <div class="section-badge"><i class="fa-solid fa-user-doctor"></i> Đội ngũ bác sĩ</div>
            <h2 class="section-title">Bác sĩ giỏi & tận tâm</h2>
            <p class="section-subtitle">Đội ngũ bác sĩ chuyên khoa giàu kinh nghiệm, luôn sẵn sàng chăm sóc sức khỏe cho bạn</p>
        </div>
        <div class="row g-4 justify-content-center">
            <?php foreach ($doctors as $idx => $doc): ?>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?= $idx * 100 ?>">
                <div class="doctor-pub-card">
                    <div class="doctor-img" style="background: linear-gradient(135deg, var(--primary-light), var(--secondary-light)); display:flex; align-items:center; justify-content:center;">
                        <i class="fa-solid fa-user-doctor" style="font-size: 80px; color: var(--primary); opacity:0.5;"></i>
                    </div>
                    <div class="doctor-info">
                        <h5><?= htmlspecialchars($doc['name']) ?></h5>
                        <div class="dept-badge"><?= htmlspecialchars($doc['department_name'] ?? 'N/A') ?></div>
                        <div class="specialty"><?= htmlspecialchars($doc['specialty'] ?? '') ?> • <?= $doc['experience_years'] ?? 0 ?> năm kinh nghiệm</div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ========== CTA SECTION ========== -->
<section class="cta-section" data-aos="fade-up">
    <div class="container text-center position-relative" style="z-index:2;">
        <h2>Đặt lịch khám ngay hôm nay</h2>
        <p>Hệ thống đặt lịch online 24/7, nhanh chóng và tiện lợi. Không cần chờ đợi!</p>
        <a href="index.php?page=book-appointment" class="btn-cta">
            <i class="fa-solid fa-calendar-check"></i> Đặt lịch khám
        </a>
    </div>
</section>

<!-- ========== FOOTER ========== -->
<footer class="footer-public">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-4">
                <div class="footer-brand">
                    <i class="fa-solid fa-hospital"></i> NovaCare Hospital
                </div>
                <p class="footer-desc">
                    NovaCare Smart Hospital — Hệ thống bệnh viện thông minh hàng đầu, 
                    ứng dụng công nghệ số vào y tế để mang lại trải nghiệm chăm sóc sức khỏe tốt nhất.
                </p>
            </div>
            <div class="col-lg-2 col-md-4">
                <h6>Liên kết</h6>
                <ul class="footer-links">
                    <li><a href="index.php?page=home">Trang chủ</a></li>
                    <li><a href="index.php?page=about">Giới thiệu</a></li>
                    <li><a href="index.php?page=services">Dịch vụ</a></li>
                    <li><a href="index.php?page=public-doctors">Bác sĩ</a></li>
                </ul>
            </div>
            <div class="col-lg-2 col-md-4">
                <h6>Dịch vụ</h6>
                <ul class="footer-links">
                    <li><a href="#">Xét nghiệm máu</a></li>
                    <li><a href="#">Chụp X-quang</a></li>
                    <li><a href="#">Chụp MRI</a></li>
                    <li><a href="#">Tư vấn online</a></li>
                </ul>
            </div>
            <div class="col-lg-4 col-md-4">
                <h6>Liên hệ</h6>
                <div class="footer-contact-item">
                    <i class="fa-solid fa-location-dot"></i>
                    <span>123 Đường Nguyễn Văn Linh, Quận 7, TP. Hồ Chí Minh</span>
                </div>
                <div class="footer-contact-item">
                    <i class="fa-solid fa-phone"></i>
                    <span>(028) 1234 5678</span>
                </div>
                <div class="footer-contact-item">
                    <i class="fa-solid fa-envelope"></i>
                    <span>info@novacare.vn</span>
                </div>
            </div>
        </div>
        <div class="footer-bottom">
            &copy; 2026 NovaCare Smart Hospital. All rights reserved.
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="assets/js/dashboard.js"></script>
</body>
</html>
