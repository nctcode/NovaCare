<?php
/**
 * Layout Header - NovaCare Smart Hospital (Admin System)
 */
$user = $_SESSION['user'];
$currentPage = $_GET['page'] ?? 'dashboard';

// Page titles
$pageTitles = [
    'dashboard' => 'Dashboard',
    'patients' => 'Quản lý Bệnh nhân',
    'doctors' => 'Quản lý Bác sĩ',
    'appointments' => 'Lịch hẹn',
    'prescriptions' => 'Đơn thuốc',
    'medicines' => 'Kho thuốc',
    'shifts' => 'Ca trực',
    'devices' => 'Thiết bị Y tế',
    'meetings' => 'Tư vấn Online (Cũ)',
    'consultations' => 'Tư vấn Online',
    'nurses' => 'Quản lý Y tá',
    'departments' => 'Quản lý Khoa',
    'services-admin' => 'Dịch vụ Y tế',
    'ai-assistant' => 'Trợ lý AI',
    'equipment' => 'Trang Thiết bị',
];

$roleNames = [
    'admin' => 'Quản trị viên',
    'doctor' => 'Bác sĩ',
    'nurse' => 'Y tá',
    'patient' => 'Bệnh nhân',
];

$pageTitle = $pageTitles[$currentPage] ?? 'NovaCare';
$roleName = $roleNames[$user['role']] ?? $user['role'];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?> - NovaCare Hospital</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" rel="stylesheet">
    <link href="assets/css/style.css" rel="stylesheet">
    <link href="assets/css/animations.css" rel="stylesheet">
</head>
<body>

<!-- ========== SIDEBAR ========== -->
<aside class="sidebar">
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
                <i class="fa-solid fa-chart-pie"></i> Dashboard
            </a>
        </li>

        <?php if ($user['role'] === 'admin'): ?>
        <li class="menu-label">Cơ cấu tổ chức</li>
        <li>
            <a href="index.php?page=departments" class="<?= $currentPage === 'departments' ? 'active' : '' ?>">
                <i class="fa-solid fa-building-columns"></i> Danh sách Khoa
            </a>
        </li>
        <li class="menu-label">Quản lý Con người</li>
        <li>
            <a href="index.php?page=patients" class="<?= $currentPage === 'patients' ? 'active' : '' ?>">
                <i class="fa-solid fa-hospital-user"></i> Bệnh nhân
            </a>
        </li>
        <li>
            <a href="index.php?page=doctors" class="<?= $currentPage === 'doctors' ? 'active' : '' ?>">
                <i class="fa-solid fa-user-doctor"></i> Bác sĩ
            </a>
        </li>
        <li>
            <a href="index.php?page=nurses" class="<?= $currentPage === 'nurses' ? 'active' : '' ?>">
                <i class="fa-solid fa-user-nurse"></i> Y tá
            </a>
        </li>
        <li class="menu-label">Hoạt động Khám chữa bệnh</li>
        <li>
            <a href="index.php?page=appointments" class="<?= $currentPage === 'appointments' ? 'active' : '' ?>">
                <i class="fa-solid fa-calendar-check"></i> Lịch hẹn
            </a>
        </li>
        <li>
            <a href="index.php?page=prescriptions" class="<?= $currentPage === 'prescriptions' ? 'active' : '' ?>">
                <i class="fa-solid fa-file-prescription"></i> Đơn thuốc
            </a>
        </li>
        <li>
            <a href="index.php?page=services-admin" class="<?= $currentPage === 'services-admin' ? 'active' : '' ?>">
                <i class="fa-solid fa-stethoscope"></i> Dịch vụ Y tế
            </a>
        </li>
        <li class="menu-label">Kho & Cơ sở vật chất</li>
        <li>
            <a href="index.php?page=medicines" class="<?= $currentPage === 'medicines' ? 'active' : '' ?>">
                <i class="fa-solid fa-capsules"></i> Kho thuốc
            </a>
        </li>
        <li>
            <a href="index.php?page=devices" class="<?= $currentPage === 'devices' ? 'active' : '' ?>">
                <i class="fa-solid fa-laptop-medical"></i> Thiết bị Khám
            </a>
        </li>
        <li>
            <a href="index.php?page=equipment" class="<?= $currentPage === 'equipment' ? 'active' : '' ?>">
                <i class="fa-solid fa-bed-pulse"></i> Trang thiết bị
            </a>
        </li>
        <li class="menu-label">Khác</li>
        <li>
            <a href="index.php?page=shifts" class="<?= $currentPage === 'shifts' ? 'active' : '' ?>">
                <i class="fa-solid fa-clock"></i> Ca trực
            </a>
        </li>
        <li>
            <a href="index.php?page=consultations" class="<?= $currentPage === 'consultations' ? 'active' : '' ?>">
                <i class="fa-solid fa-video"></i> Tư vấn Online
            </a>
        </li>
        <li>
            <a href="index.php?page=ai-assistant" class="<?= $currentPage === 'ai-assistant' ? 'active' : '' ?>">
                <i class="fa-solid fa-robot"></i> Trợ lý AI
            </a>
        </li>

        <?php elseif ($user['role'] === 'doctor'): ?>
        <li class="menu-label">Công việc</li>
        <li>
            <a href="index.php?page=appointments" class="<?= $currentPage === 'appointments' ? 'active' : '' ?>">
                <i class="fa-solid fa-calendar-check"></i> Lịch khám
            </a>
        </li>
        <li>
            <a href="index.php?page=prescriptions" class="<?= $currentPage === 'prescriptions' ? 'active' : '' ?>">
                <i class="fa-solid fa-file-prescription"></i> Đơn thuốc
            </a>
        </li>
        <li>
            <a href="index.php?page=shifts" class="<?= $currentPage === 'shifts' ? 'active' : '' ?>">
                <i class="fa-solid fa-clock"></i> Ca trực
            </a>
        </li>
        <li>
            <a href="index.php?page=meetings" class="<?= $currentPage === 'meetings' ? 'active' : '' ?>">
                <i class="fa-solid fa-video"></i> Tư vấn Online
            </a>
        </li>

        <?php elseif ($user['role'] === 'nurse'): ?>
        <li class="menu-label">Công việc</li>
        <li>
            <a href="index.php?page=shifts" class="<?= $currentPage === 'shifts' ? 'active' : '' ?>">
                <i class="fa-solid fa-clock"></i> Ca trực
            </a>
        </li>

        <?php elseif ($user['role'] === 'patient'): ?>
        <li class="menu-label">Sức khỏe & Khám bệnh</li>
        <li>
            <a href="index.php?page=appointments" class="<?= $currentPage === 'appointments' ? 'active' : '' ?>">
                <i class="fa-solid fa-calendar-check"></i> Lịch hẹn
            </a>
        </li>
        <li>
            <a href="index.php?page=prescriptions" class="<?= $currentPage === 'prescriptions' ? 'active' : '' ?>">
                <i class="fa-solid fa-file-prescription"></i> Đơn thuốc
            </a>
        </li>
        <li>
            <a href="index.php?page=consultations" class="<?= $currentPage === 'consultations' ? 'active' : '' ?>">
                <i class="fa-solid fa-video"></i> Tư vấn Online
            </a>
        </li>
        <li>
            <a href="index.php?page=ai-assistant" class="<?= $currentPage === 'ai-assistant' ? 'active' : '' ?>">
                <i class="fa-solid fa-robot"></i> Trợ lý AI (Mới)
            </a>
        </li>
        <li>
            <a href="index.php?page=prescriptions" class="<?= $currentPage === 'prescriptions' ? 'active' : '' ?>">
                <i class="fa-solid fa-file-prescription"></i> Đơn thuốc
            </a>
        </li>
        <li>
            <a href="index.php?page=meetings" class="<?= $currentPage === 'meetings' ? 'active' : '' ?>">
                <i class="fa-solid fa-video"></i> Tư vấn Online
            </a>
        </li>
        <?php endif; ?>

        <li class="menu-label">Tài khoản</li>
        <li>
            <a href="index.php?page=logout" style="color:var(--danger);">
                <i class="fa-solid fa-right-from-bracket"></i> Đăng xuất
            </a>
        </li>
    </ul>
</aside>

<!-- ========== MAIN CONTENT ========== -->
<div class="main-content">
    <!-- Top Navbar -->
    <div class="top-navbar">
        <h4 class="page-title"><?= $pageTitle ?></h4>
        <div class="nav-user">
            <div class="user-info">
                <div class="name"><?= htmlspecialchars($user['name']) ?></div>
                <div class="role">
                    <span class="badge-status badge-<?= $user['role'] ?>" style="padding:2px 10px;font-size:11px;"><?= $roleName ?></span>
                </div>
            </div>
            <div class="user-avatar">
                <?= strtoupper(substr($user['name'], 0, 1)) ?>
            </div>
        </div>
    </div>

    <!-- Page Content -->
    <div class="page-content">
