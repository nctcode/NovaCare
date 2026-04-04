<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đội ngũ Bác sĩ - NovaCare Smart Hospital</title>
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
                <li class="nav-item"><a class="nav-link active" href="index.php?page=public-doctors">Bác sĩ</a></li>
                <li class="nav-item"><a class="nav-link" href="index.php?page=contact">Liên hệ</a></li>
            </ul>
            <a href="index.php?page=login" class="btn-nav-login"><i class="fa-solid fa-right-to-bracket"></i> Đăng nhập</a>
        </div>
    </div>
</nav>

<section style="padding:140px 0 80px; background:linear-gradient(135deg, var(--primary-50), #ecfdf5);">
    <div class="container text-center" data-aos="fade-up">
        <div class="section-badge"><i class="fa-solid fa-user-doctor"></i> Bác sĩ</div>
        <h1 class="section-title" style="font-size:42px;">Đội ngũ Bác sĩ</h1>
        <p class="section-subtitle">Các chuyên gia y tế hàng đầu luôn sẵn sàng phục vụ bạn</p>
    </div>
</section>

<section class="section-padding">
    <div class="container">
        <div class="row g-4">
            <?php foreach ($doctors as $idx => $doc): ?>
            <div class="col-lg-4 col-md-6" data-aos="fade-up" data-aos-delay="<?= $idx * 100 ?>">
                <div class="doctor-pub-card">
                    <div class="doctor-img" style="background: linear-gradient(135deg, var(--primary-light), var(--secondary-light)); display:flex; align-items:center; justify-content:center;">
                        <i class="fa-solid fa-user-doctor" style="font-size: 80px; color: var(--primary); opacity:0.5;"></i>
                    </div>
                    <div class="doctor-info">
                        <h5><?= htmlspecialchars($doc['name']) ?></h5>
                        <div class="dept-badge"><?= htmlspecialchars($doc['department_name'] ?? 'N/A') ?></div>
                        <div class="specialty"><?= htmlspecialchars($doc['specialty'] ?? '') ?></div>
                        <p class="mt-2" style="font-size:13px; color:var(--gray-500);"><?= $doc['experience_years'] ?? 0 ?> năm kinh nghiệm</p>
                        <a href="index.php?page=book-appointment" class="btn-action btn-add mt-2" style="font-size:12px;padding:8px 16px;">
                            <i class="fa-solid fa-calendar-check"></i> Đặt lịch
                        </a>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
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
