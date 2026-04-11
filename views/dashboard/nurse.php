<!-- Dashboard Nurse -->
<div class="row g-4 mb-4">
    <div class="col-md-6" data-aos="fade-up">
        <div class="stat-card card-success">
            <div class="stat-icon"><i class="fa-solid fa-heart-pulse"></i></div>
            <div class="stat-label">Xin chào, <?= htmlspecialchars($user['name']) ?></div>
            <div class="stat-value mt-2" style="font-size:16px">Y tá</div>
        </div>
    </div>
    <div class="col-md-6" data-aos="fade-up" data-aos-delay="100">
        <a href="index.php?page=shifts" class="text-decoration-none">
            <div class="stat-card card-warning" style="cursor:pointer;">
                <div class="stat-icon"><i class="fa-solid fa-clock"></i></div>
                <div class="stat-label" style="font-weight:600;">Đăng ký Ca trực</div>
            </div>
        </a>
    </div>
</div>

<div class="content-card" data-aos="fade-up" data-aos-delay="200">
    <div class="card-header">
        <h5><i class="fa-solid fa-circle-info me-2"></i>Thông tin</h5>
    </div>
    <div class="card-body">
        <p>Chào mừng bạn đến với hệ thống quản lý NovaCare Smart Hospital.</p>
        <ul>
            <li>Xem và đăng ký <strong>ca trực</strong></li>
            <li>Hỗ trợ quản lý <strong>bệnh nhân</strong></li>
        </ul>
    </div>
</div>
