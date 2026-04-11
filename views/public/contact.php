<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Liên hệ - NovaCare Smart Hospital</title>
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
                <li class="nav-item"><a class="nav-link active" href="index.php?page=contact">Liên hệ</a></li>
            </ul>
            <a href="index.php?page=login" class="btn-nav-login"><i class="fa-solid fa-right-to-bracket"></i> Đăng nhập</a>
        </div>
    </div>
</nav>

<section style="padding:140px 0 80px; background:linear-gradient(135deg, var(--primary-50), #ecfdf5);">
    <div class="container text-center" data-aos="fade-up">
        <div class="section-badge"><i class="fa-solid fa-envelope"></i> Liên hệ</div>
        <h1 class="section-title" style="font-size:42px;">Liên hệ với chúng tôi</h1>
        <p class="section-subtitle">Chúng tôi luôn sẵn sàng lắng nghe và hỗ trợ bạn</p>
    </div>
</section>

<section class="section-padding">
    <div class="container">
        <div class="row g-5">
            <div class="col-lg-5" data-aos="fade-right">
                <h4 style="font-weight:700;margin-bottom:24px;">Thông tin liên hệ</h4>
                <div class="service-card mb-3" style="height:auto;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="service-icon" style="background:var(--primary-light);color:var(--primary);width:48px;height:48px;margin:0;">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <div>
                            <h6 style="font-weight:600;margin:0;">Địa chỉ</h6>
                            <p style="margin:0;color:var(--gray-500);font-size:14px;">123 Đường Nguyễn Văn Linh, Quận 7, TP. HCM</p>
                        </div>
                    </div>
                </div>
                <div class="service-card mb-3" style="height:auto;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="service-icon" style="background:var(--secondary-light);color:var(--secondary);width:48px;height:48px;margin:0;">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        <div>
                            <h6 style="font-weight:600;margin:0;">Điện thoại</h6>
                            <p style="margin:0;color:var(--gray-500);font-size:14px;">(028) 1234 5678</p>
                        </div>
                    </div>
                </div>
                <div class="service-card mb-3" style="height:auto;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="service-icon" style="background:var(--accent-light);color:var(--accent);width:48px;height:48px;margin:0;">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <div>
                            <h6 style="font-weight:600;margin:0;">Email</h6>
                            <p style="margin:0;color:var(--gray-500);font-size:14px;">info@novacare.vn</p>
                        </div>
                    </div>
                </div>
                <div class="service-card" style="height:auto;">
                    <div class="d-flex align-items-center gap-3">
                        <div class="service-icon" style="background:#fef3c7;color:var(--warning);width:48px;height:48px;margin:0;">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div>
                            <h6 style="font-weight:600;margin:0;">Giờ làm việc</h6>
                            <p style="margin:0;color:var(--gray-500);font-size:14px;">T2 - T7: 7:00 - 17:00 | CN: 7:00 - 12:00</p>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-7" data-aos="fade-left">
                <div class="form-section" style="max-width:100%;">
                    <h5 class="mb-4"><i class="fa-solid fa-paper-plane me-2"></i>Gửi tin nhắn</h5>
                    <form>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Họ tên</label>
                                <input type="text" class="form-control" placeholder="Nhập họ tên...">
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="form-label">Email</label>
                                <input type="email" class="form-control" placeholder="Nhập email...">
                            </div>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Chủ đề</label>
                            <input type="text" class="form-control" placeholder="Nhập chủ đề...">
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Nội dung</label>
                            <textarea class="form-control" rows="5" placeholder="Nhập nội dung tin nhắn..."></textarea>
                        </div>
                        <button type="button" class="btn-submit" onclick="alert('Cảm ơn bạn! Tin nhắn đã được gửi (giả lập).');">
                            <i class="fa-solid fa-paper-plane"></i> Gửi tin nhắn
                        </button>
                    </form>
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
