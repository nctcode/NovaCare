<?php
/**
 * Layout Header - NovaCare Smart Hospital (Admin System)
 */
$user = $_SESSION['user'];
$currentPage = $_GET['page'] ?? 'dashboard';

// Page titles
$pageTitles = [
    'dashboard'     => 'Dashboard',
    'patients'      => 'Quản lý Bệnh nhân',
    'doctors'       => 'Quản lý Bác sĩ',
    'appointments'  => 'Lịch hẹn',
    'prescriptions' => 'Đơn thuốc',
    'medicines'     => 'Kho thuốc',
    'shifts'        => 'Ca trực',
    'devices'       => 'Máy móc y tế',
    'consultations' => 'Tư vấn Online',
    'nurses'        => 'Quản lý Y tá',
    'departments'   => 'Quản lý Khoa',
    'services-admin'=> 'Dịch vụ Y tế',
    'ai-assistant'  => 'Trợ lý AI',
    'equipment'     => 'Vật tư y tế',
    'records'       => 'Hồ sơ Bệnh án',
    'invoices'      => 'Hóa đơn & Thanh toán',
    'inpatient'     => 'Quản lý Nội trú',
    'users'         => 'Quản lý Tài khoản',
    'ai-admin'      => 'AI Quản trị & Vận hành',
    'lab-orders'    => 'Chỉ định CLS',
    'reports'       => 'Báo cáo & Thống kê',
];

// Section groups for breadcrumb
$sectionGroups = [
    'dashboard'     => 'Tổng quan',
    'departments'   => 'Cơ cấu tổ chức',
    'patients'      => 'Quản lý Con người',
    'doctors'       => 'Quản lý Con người',
    'nurses'        => 'Quản lý Con người',
    'appointments'  => 'Hoạt động Khám chữa bệnh',
    'prescriptions' => 'Hoạt động Khám chữa bệnh',
    'services-admin'=> 'Hoạt động Khám chữa bệnh',
    'invoices'      => 'Hoạt động Khám chữa bệnh',
    'inpatient'     => 'Hoạt động Khám chữa bệnh',
    'records'       => 'Hoạt động Khám chữa bệnh',
    'lab-orders'    => 'Hoạt động Khám chữa bệnh',
    'medicines'     => 'Kho & Cơ sở vật chất',
    'devices'       => 'Kho & Cơ sở vật chất',
    'equipment'     => 'Kho & Cơ sở vật chất',
    'users'         => 'Bảo mật & Hệ thống',
    'audit_logs'    => 'Bảo mật & Hệ thống',
    'shifts'        => 'Khác',
    'consultations' => 'Khác',
    'ai-assistant'  => 'Khác',
    'reports'       => 'Báo cáo',
    'ai-admin'      => 'Bảo mật & Hệ thống',
];

$roleNames = [
    'admin'         => 'Quản trị viên',
    'doctor'        => 'Bác sĩ',
    'nurse'         => 'Điều dưỡng',
    'patient'       => 'Bệnh nhân',
    'receptionist'  => 'Lễ tân',
    'pharmacist'    => 'Dược sĩ',
    'technician'    => 'Kỹ thuật viên',
    'director'      => 'Ban giám đốc',
    'cashier'       => 'Thu ngân',
];

$pageTitle   = $pageTitles[$currentPage] ?? 'NovaCare';
$roleName    = $roleNames[$user['role']] ?? $user['role'];
$breadcrumb  = $sectionGroups[$currentPage] ?? 'NovaCare';
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="<?= Security::getCsrfToken() ?>">
    <title><?= $pageTitle ?> - NovaCare Hospital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css?v=<?= time() ?>" rel="stylesheet">
    <link href="assets/css/animations.css" rel="stylesheet">
    <link href="assets/css/ux-enhancements.css?v=<?= time() ?>" rel="stylesheet">
    <link href="assets/css/darkmode.css?v=<?= time() ?>" rel="stylesheet">
    <!-- Prevent dark mode flash -->
    <script>
        (function(){
            var theme = localStorage.getItem('nc-theme');
            if (theme === 'dark') document.documentElement.setAttribute('data-theme','dark');
        })();
    </script>
</head>
<body>

<!-- ========== SIDEBAR OVERLAY (mobile) ========== -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- ========== SIDEBAR ========== -->
<aside class="sidebar" id="sidebar">
    <div class="sidebar-brand">
        <i class="fa-solid fa-hospital"></i>
        <div>
            <h4>NovaCare</h4>
            <small>Smart Hospital</small>
        </div>
    </div>

    <ul class="sidebar-menu">
        <li class="menu-label">Menu chính</li>
        <li>
            <a href="index.php?page=dashboard" class="<?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                <i class="fa-solid fa-chart-pie"></i> <span>Dashboard</span>
            </a>
        </li>

        <?php if ($user['role'] === 'admin'): ?>
        <li class="menu-label">Cơ cấu tổ chức</li>
        <li>
            <a href="index.php?page=departments" class="<?= $currentPage === 'departments' ? 'active' : '' ?>">
                <i class="fa-solid fa-building-columns"></i> <span>Danh sách Khoa</span>
            </a>
        </li>
        <li class="menu-label">Quản lý Bệnh nhân</li>
        <li>
            <a href="index.php?page=patients" class="<?= $currentPage === 'patients' ? 'active' : '' ?>">
                <i class="fa-solid fa-hospital-user"></i> <span>Danh sách Bệnh nhân</span>
            </a>
        <li class="menu-label">Quản lý Nhân sự</li>
        <li>
            <a href="index.php?page=doctors" class="<?= $currentPage === 'doctors' ? 'active' : '' ?>">
                <i class="fa-solid fa-user-doctor"></i> <span>Bác sĩ</span>
            </a>
        </li>
        <li>
            <a href="index.php?page=nurses" class="<?= $currentPage === 'nurses' ? 'active' : '' ?>">
                <i class="fa-solid fa-user-nurse"></i> <span>Y tá / Điều dưỡng</span>
            </a>
        </li>
        <li>
            <a href="index.php?page=technicians" class="<?= $currentPage === 'technicians' ? 'active' : '' ?>">
                <i class="fa-solid fa-flask-vial"></i> <span>Kỹ thuật viên</span>
            </a>
        </li>
        <li>
            <a href="index.php?page=receptionists" class="<?= $currentPage === 'receptionists' ? 'active' : '' ?>">
                <i class="fa-solid fa-address-book"></i> <span>Lễ tân</span>
            </a>
        </li>
        <li>
            <a href="index.php?page=pharmacists" class="<?= $currentPage === 'pharmacists' ? 'active' : '' ?>">
                <i class="fa-solid fa-pills"></i> <span>Dược sĩ</span>
            </a>
        </li>
        <li>
            <a href="index.php?page=cashiers" class="<?= $currentPage === 'cashiers' ? 'active' : '' ?>">
                <i class="fa-solid fa-coins"></i> <span>Thu ngân</span>
            </a>
        </li>
        <li>
            <a href="index.php?page=directors" class="<?= $currentPage === 'directors' ? 'active' : '' ?>">
                <i class="fa-solid fa-user-tie"></i> <span>Ban giám đốc</span>
            </a>
        </li>
        <li class="menu-label">Cơ sở vật chất</li>
        <li>
            <a href="index.php?page=devices" class="<?= $currentPage === 'devices' ? 'active' : '' ?>">
                <i class="fa-solid fa-laptop-medical"></i> <span>Máy móc y tế</span>
            </a>
        </li>
        <li>
            <a href="index.php?page=equipment" class="<?= $currentPage === 'equipment' ? 'active' : '' ?>">
                <i class="fa-solid fa-toolbox"></i> <span>Vật tư y tế</span>
            </a>
        </li>
        <li class="menu-label">Cấu hình</li>
        <li>
            <a href="index.php?page=services-admin" class="<?= $currentPage === 'services-admin' ? 'active' : '' ?>">
                <i class="fa-solid fa-stethoscope"></i> <span>Dịch vụ Y tế</span>
            </a>
        </li>

        <li>
            <a href="index.php?page=ai-admin" class="<?= $currentPage === 'ai-admin' ? 'active' : '' ?>">
                <i class="fa-solid fa-brain"></i> <span>AI Quản trị</span>
            </a>
        </li>
        <li class="menu-label">Bảo mật & Hệ thống</li>
        <li>
            <a href="index.php?page=users" class="<?= $currentPage === 'users' ? 'active' : '' ?>">
                <i class="fa-solid fa-users-gear"></i> <span>Quản lý Tài khoản</span>
            </a>
        </li>
        <li>
            <a href="index.php?page=audit_logs" class="<?= $currentPage === 'audit_logs' ? 'active' : '' ?>">
                <i class="fa-solid fa-shield-halved"></i> <span>Audit Logs</span>
            </a>
        </li>

        <?php elseif ($user['role'] === 'receptionist'): ?>
        <li class="menu-label">Tiếp đón</li>
        <li>
            <a href="index.php?page=queue" class="<?= $currentPage === 'queue' ? 'active' : '' ?>">
                <i class="fa-solid fa-list-ol"></i> <span>Hàng chờ khám</span>
            </a>
        </li>
        <li>
            <a href="index.php?page=patients" class="<?= $currentPage === 'patients' ? 'active' : '' ?>">
                <i class="fa-solid fa-hospital-user"></i> <span>Bệnh nhân</span>
            </a>
        </li>
        <li>
            <a href="index.php?page=appointments" class="<?= $currentPage === 'appointments' ? 'active' : '' ?>">
                <i class="fa-solid fa-calendar-check"></i> <span>Lịch hẹn</span>
            </a>
        </li>
        <li class="menu-label">Nội trú</li>
        <li>
            <a href="index.php?page=inpatient" class="<?= $currentPage === 'inpatient' ? 'active' : '' ?>">
                <i class="fa-solid fa-bed-pulse"></i> <span>Quản lý Nội trú</span>
            </a>
        </li>
        <li class="menu-label">Nhân sự</li>
        <li>
            <a href="index.php?page=doctors" class="<?= $currentPage === 'doctors' ? 'active' : '' ?>">
                <i class="fa-solid fa-user-doctor"></i> <span>Bác sĩ</span>
            </a>
        </li>
        <li>
            <a href="index.php?page=nurses" class="<?= $currentPage === 'nurses' ? 'active' : '' ?>">
                <i class="fa-solid fa-user-nurse"></i> <span>Y tá</span>
            </a>
        </li>

        <?php elseif ($user['role'] === 'cashier'): ?>
        <li class="menu-label">Tài chính</li>
        <li>
            <a href="index.php?page=invoices" class="<?= $currentPage === 'invoices' ? 'active' : '' ?>">
                <i class="fa-solid fa-file-invoice-dollar"></i> <span>Hóa đơn & Thanh toán</span>
            </a>
        </li>


        <?php elseif ($user['role'] === 'pharmacist'): ?>
        <li class="menu-label">Dược phẩm</li>
        <li>
            <a href="index.php?page=medicines" class="<?= $currentPage === 'medicines' ? 'active' : '' ?>">
                <i class="fa-solid fa-capsules"></i> <span>Kho thuốc</span>
            </a>
        </li>
        <li>
            <a href="index.php?page=prescriptions" class="<?= $currentPage === 'prescriptions' ? 'active' : '' ?>">
                <i class="fa-solid fa-file-prescription"></i> <span>Đơn thuốc</span>
            </a>
        </li>

        <?php elseif ($user['role'] === 'doctor'): ?>
        <li class="menu-label">Công việc</li>
        <li>
            <a href="index.php?page=queue" class="<?= $currentPage === 'queue' ? 'active' : '' ?>">
                <i class="fa-solid fa-list-ol"></i> <span>Hàng chờ khám</span>
            </a>
        </li>
        <li>
            <a href="index.php?page=patients" class="<?= $currentPage === 'patients' ? 'active' : '' ?>">
                <i class="fa-solid fa-hospital-user"></i> <span>Quản lý Bệnh nhân</span>
            </a>
        </li>
        <li>
            <a href="index.php?page=appointments" class="<?= $currentPage === 'appointments' ? 'active' : '' ?>">
                <i class="fa-solid fa-calendar-check"></i> <span>Lịch khám</span>
            </a>
        </li>
        <li>
            <a href="index.php?page=records" class="<?= $currentPage === 'records' ? 'active' : '' ?>">
                <i class="fa-solid fa-file-medical"></i> <span>Bệnh án nội trú</span>
            </a>
        </li>
        <li>
            <a href="index.php?page=prescriptions" class="<?= $currentPage === 'prescriptions' ? 'active' : '' ?>">
                <i class="fa-solid fa-file-prescription"></i> <span>Kê đơn thuốc</span>
            </a>
        </li>
        <li>
            <a href="index.php?page=lab-orders" class="<?= $currentPage === 'lab-orders' ? 'active' : '' ?>">
                <i class="fa-solid fa-flask-vial"></i> <span>Chỉ định CLS</span>
            </a>
        </li>
        <li>
            <a href="index.php?page=shifts" class="<?= $currentPage === 'shifts' ? 'active' : '' ?>">
                <i class="fa-solid fa-clock"></i> <span>Ca trực</span>
            </a>
        </li>
        <li>
            <a href="index.php?page=consultations" class="<?= $currentPage === 'consultations' ? 'active' : '' ?>">
                <i class="fa-solid fa-video"></i> <span>Tư vấn Online</span>
            </a>
        </li>

        <?php elseif ($user['role'] === 'nurse'): ?>
        <li class="menu-label">Công việc</li>
        <li>
            <a href="index.php?page=inpatient" class="<?= $currentPage === 'inpatient' ? 'active' : '' ?>">
                <i class="fa-solid fa-bed-pulse"></i> <span>Bệnh nhân nội trú</span>
            </a>
        </li>
        <li>
            <a href="index.php?page=shifts" class="<?= $currentPage === 'shifts' ? 'active' : '' ?>">
                <i class="fa-solid fa-clock"></i> <span>Ca trực</span>
            </a>
        </li>
        <li>
            <a href="index.php?page=appointments" class="<?= $currentPage === 'appointments' ? 'active' : '' ?>">
                <i class="fa-solid fa-calendar-check"></i> <span>Lịch hẹn hôm nay</span>
            </a>
        </li>


        <?php elseif ($user['role'] === 'technician'): ?>
        <li class="menu-label">Cận lâm sàng</li>
        <li>
            <a href="index.php?page=lab-orders" class="<?= $currentPage === 'lab-orders' ? 'active' : '' ?>">
                <i class="fa-solid fa-flask-vial"></i> <span>Chỉ định CLS</span>
            </a>
        </li>

        <?php elseif ($user['role'] === 'director'): ?>
        <li class="menu-label">Quản lý</li>
        <li>
            <a href="index.php?page=reports" class="<?= $currentPage === 'reports' ? 'active' : '' ?>">
                <i class="fa-solid fa-chart-bar"></i> <span>Báo cáo & Thống kê</span>
            </a>
        </li>

        <?php elseif ($user['role'] === 'patient'): ?>
        <li class="menu-label">Sức khỏe & Khám bệnh</li>
        <li>
            <a href="index.php?page=appointments" class="<?= $currentPage === 'appointments' ? 'active' : '' ?>">
                <i class="fa-solid fa-calendar-check"></i> <span>Lịch hẹn</span>
            </a>
        </li>
        <li>
            <a href="index.php?page=records" class="<?= $currentPage === 'records' ? 'active' : '' ?>">
                <i class="fa-solid fa-file-medical"></i> <span>Hồ sơ Bệnh án</span>
            </a>
        </li>
        <li>
            <a href="index.php?page=prescriptions" class="<?= $currentPage === 'prescriptions' ? 'active' : '' ?>">
                <i class="fa-solid fa-file-prescription"></i> <span>Đơn thuốc</span>
            </a>
        </li>
        <li>
            <a href="index.php?page=consultations" class="<?= $currentPage === 'consultations' ? 'active' : '' ?>">
                <i class="fa-solid fa-video"></i> <span>Tư vấn Online</span>
            </a>
        </li>
        <li>
            <a href="index.php?page=ai-assistant" class="<?= $currentPage === 'ai-assistant' ? 'active' : '' ?>">
                <i class="fa-solid fa-robot"></i> <span>Trợ lý AI</span>
            </a>
        </li>
        <li>
            <a href="index.php?page=invoices" class="<?= $currentPage === 'invoices' ? 'active' : '' ?>">
                <i class="fa-solid fa-file-invoice-dollar"></i> <span>Hóa đơn của tôi</span>
            </a>
        </li>
        <?php endif; ?>

        <li class="menu-label">Tài khoản</li>
        <?php if ($user['role'] === 'patient'): ?>
        <li>
            <a href="index.php?page=patients&action=myProfile" class="<?= ($currentPage === 'patients' && ($_GET['action'] ?? '') === 'myProfile') ? 'active' : '' ?>">
                <i class="fa-solid fa-user-pen"></i> <span>Tài khoản của tôi</span>
            </a>
        </li>
        <?php endif; ?>
        <li>
            <a href="index.php?page=logout" class="logout-link">
                <i class="fa-solid fa-right-from-bracket"></i> <span>Đăng xuất</span>
            </a>
        </li>
    </ul>
</aside>

<!-- ========== MAIN CONTENT ========== -->
<div class="main-content">
    <!-- Top Navbar -->
    <div class="top-navbar">
        <div class="topbar-left">
            <!-- Hamburger (mobile only) -->
            <button class="btn-hamburger" id="btnHamburger" aria-label="Toggle menu" aria-expanded="false">
                <div class="hamburger-bars">
                    <span class="bar-top"></span>
                    <span class="bar-mid"></span>
                    <span class="bar-bot"></span>
                </div>
            </button>
            <!-- Page title + breadcrumb -->
            <div class="page-title-group">
                <h4 class="page-title"><?= $pageTitle ?></h4>
                <?php if ($currentPage !== 'dashboard'): ?>
                <div class="breadcrumb-strip">
                    <span>NovaCare</span>
                    <i class="fa-solid fa-chevron-right"></i>
                    <span><?= $breadcrumb ?></span>
                    <i class="fa-solid fa-chevron-right"></i>
                    <span><?= $pageTitle ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>
        <div class="topbar-right">
            <!-- Dark Mode Toggle -->
            <button class="btn-darkmode" id="btnDarkMode" title="Đổi giao diện" aria-label="Chế độ tối">
                <i class="fa-solid fa-moon" id="darkModeIcon"></i>
            </button>

            <!-- Notification Bell + Panel -->
            <div class="notif-wrapper">
                <button class="btn-notif" id="btnNotif" title="Thông báo" aria-label="Thông báo">
                    <i class="fa-regular fa-bell"></i>
                    <?php
                    // Count notifications
                    $notifCount = 0;
                    if (!empty($lowStockMedicines)) $notifCount += count($lowStockMedicines);
                    if (!empty($recentAppointments)) {
                        foreach ($recentAppointments as $ra) {
                            if (isset($ra['appointment_date'])) {
                                $apptTs = strtotime($ra['appointment_date']);
                                $now = time();
                                if ($apptTs >= $now && $apptTs <= $now + 86400) $notifCount++;
                            }
                        }
                    }
                    ?>
                    <?php if ($notifCount > 0): ?>
                    <span class="notif-count-badge"><?= $notifCount ?></span>
                    <?php else: ?>
                    <span class="notif-dot"></span>
                    <?php endif; ?>
                </button>

                <!-- Notification Panel -->
                <div class="notif-panel" id="notifPanel">
                    <div class="notif-header">
                        <h6><i class="fa-solid fa-bell me-2"></i>Thông báo</h6>
                        <?php if ($notifCount > 0): ?>
                        <span class="notif-count"><?= $notifCount ?> mới</span>
                        <?php endif; ?>
                    </div>
                    <div class="notif-list">
                        <?php
                        $hasNotif = false;

                        // Low stock warnings
                        if (!empty($lowStockMedicines)):
                            $hasNotif = true;
                            foreach ($lowStockMedicines as $lsm):
                        ?>
                        <div class="notif-item">
                            <div class="notif-icon type-danger"><i class="fa-solid fa-triangle-exclamation"></i></div>
                            <div class="notif-body">
                                <div class="notif-text">Cảnh báo tồn kho thấp: <strong><?= htmlspecialchars($lsm['name']) ?></strong></div>
                                <div class="notif-time"><i class="fa-solid fa-box me-1"></i>Chỉ còn <?= $lsm['quantity'] ?> đơn vị</div>
                            </div>
                        </div>
                        <?php endforeach; endif; ?>

                        <?php
                        // Upcoming appointments (next 24h)
                        if (!empty($recentAppointments)):
                            foreach ($recentAppointments as $ra):
                                if (!isset($ra['appointment_date'])) continue;
                                $apptTs = strtotime($ra['appointment_date']);
                                $now = time();
                                if ($apptTs < $now || $apptTs > $now + 86400) continue;
                                $hasNotif = true;
                        ?>
                        <div class="notif-item">
                            <div class="notif-icon type-appointment"><i class="fa-solid fa-calendar-check"></i></div>
                            <div class="notif-body">
                                <div class="notif-text">Lịch khám sắp tới: <strong><?= htmlspecialchars($ra['patient_name'] ?? 'Bệnh nhân') ?></strong></div>
                                <div class="notif-time"><i class="fa-regular fa-clock me-1"></i><?= date('H:i d/m/Y', $apptTs) ?></div>
                            </div>
                        </div>
                        <?php endforeach; endif; ?>

                        <?php if (!$hasNotif): ?>
                        <div class="notif-empty">
                            <i class="fa-regular fa-bell-slash"></i>
                            Không có thông báo mới
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="notif-footer">
                        <a href="index.php?page=appointments"><i class="fa-solid fa-arrow-right me-1"></i>Xem tất cả lịch hẹn</a>
                    </div>
                </div>
            </div>

            <!-- User info -->
            <?php $cleanName = preg_replace('/^(Bác sĩ|BS\.|Bs\.|Bs|BS)\s+/iu', '', $user['name']); ?>
            <div class="nav-user">
                <div class="user-info">
                    <div class="name"><?= htmlspecialchars($cleanName) ?></div>
                    <div class="role">
                        <span class="badge-status badge-<?= $user['role'] ?>" style="padding:2px 10px;font-size:11px;"><?= $roleName ?></span>
                    </div>
                </div>
                <div class="user-avatar" title="<?= htmlspecialchars($cleanName) ?>">
                    <?= mb_strtoupper(mb_substr($cleanName, 0, 1, 'utf-8')) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- Page Content -->
    <div class="page-content">
