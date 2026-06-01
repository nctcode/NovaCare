<?php
/**
 * Layout Header - NovaCare Smart Hospital (Admin System)
 */
$user = $_SESSION['user'];
$currentPage = $_GET['page'] ?? 'dashboard';

// Khởi tạo hệ thống thông báo
require_once __DIR__ . '/../../models/Notification.php';
$notificationModel = new Notification();
$dbNotifications = [];
$dynamicAlerts = [];

if (isset($user['id'])) {
    // Sử dụng phương thức hỗ trợ thông báo toàn hệ thống (từ file đã gộp)
    $dbNotifications = $notificationModel->getUnreadForUser($user['id'], 10);
    
    // Khởi tạo DB connection cho cảnh báo động
    $db = new Database();
    $conn = $db->getConnection();
    $role = $user['role'];
    
    // Cảnh báo động theo vai trò (Dynamic Alerts)
    if ($role === 'admin' || $role === 'pharmacist') {
        // Thuốc tồn kho thấp
        $stmt = $conn->query("SELECT id, name, quantity FROM medicines WHERE quantity <= 10 ORDER BY quantity ASC LIMIT 3");
        $lowStock = $stmt->fetchAll();
        foreach ($lowStock as $med) {
            $dynamicAlerts[] = [
                'id' => null,
                'title' => 'Cảnh báo tồn kho',
                'message' => 'Thuốc "' . $med['name'] . '" chỉ còn ' . $med['quantity'] . ' đơn vị.',
                'icon' => 'fa-solid fa-triangle-exclamation',
                'class' => 'type-danger',
                'link' => 'index.php?page=medicines',
                'time' => 'Tồn kho'
            ];
        }
    }
    
    if ($role === 'pharmacist') {
        // Thuốc sắp hết hạn trong 30 ngày
        $stmt = $conn->query("SELECT id, name, expiry_date FROM medicines WHERE expiry_date <= DATE_ADD(CURDATE(), INTERVAL 30 DAY) AND expiry_date >= CURDATE() ORDER BY expiry_date ASC LIMIT 3");
        $expiring = $stmt->fetchAll();
        foreach ($expiring as $med) {
            $dynamicAlerts[] = [
                'id' => null,
                'title' => 'Thuốc sắp hết hạn',
                'message' => 'Thuốc "' . $med['name'] . '" hết hạn ngày ' . date('d/m/Y', strtotime($med['expiry_date'])) . '.',
                'icon' => 'fa-solid fa-hourglass-half',
                'class' => 'type-warning',
                'link' => 'index.php?page=medicines',
                'time' => 'Hạn dùng'
            ];
        }
        
        // Đơn thuốc chờ duyệt/phát thuốc (approved)
        $stmt = $conn->query("SELECT pr.id, pu.name as patient_name FROM prescriptions pr JOIN medical_records mr ON pr.medical_record_id = mr.id JOIN patients p ON mr.patient_id = p.id JOIN users pu ON p.user_id = pu.id WHERE pr.status = 'approved' ORDER BY pr.created_at DESC LIMIT 3");
        $pendingPresc = $stmt->fetchAll();
        foreach ($pendingPresc as $pr) {
            $dynamicAlerts[] = [
                'id' => null,
                'title' => 'Đơn thuốc cần cấp',
                'message' => 'Đơn thuốc #' . $pr['id'] . ' của bệnh nhân ' . $pr['patient_name'] . ' chờ cấp phát.',
                'icon' => 'fa-solid fa-file-prescription',
                'class' => 'type-appointment',
                'link' => 'index.php?page=prescriptions',
                'time' => 'Chờ phát'
            ];
        }
    }
    
    if ($role === 'doctor') {
        // Lịch hẹn hôm nay
        $stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM appointments a JOIN doctors d ON a.doctor_id = d.id WHERE d.user_id = :uid AND DATE(a.appointment_date) = CURDATE() AND a.status IN ('confirmed', 'pending') AND a.deleted_at IS NULL");
        $stmt->execute([':uid' => $user['id']]);
        $todayApptsCount = $stmt->fetch()['cnt'] ?? 0;
        if ($todayApptsCount > 0) {
            $dynamicAlerts[] = [
                'id' => null,
                'title' => 'Lịch khám hôm nay',
                'message' => 'Hôm nay bạn có ' . $todayApptsCount . ' lịch khám bệnh.',
                'icon' => 'fa-solid fa-calendar-day',
                'class' => 'type-appointment',
                'link' => 'index.php?page=appointments',
                'time' => 'Hôm nay'
            ];
        }
        
        // Tư vấn online hôm nay
        $stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM online_meetings om JOIN appointments a ON om.appointment_id = a.id JOIN doctors d ON a.doctor_id = d.id WHERE d.user_id = :uid AND DATE(om.start_time) = CURDATE() AND om.status = 'scheduled'");
        $stmt->execute([':uid' => $user['id']]);
        $todayMeetingsCount = $stmt->fetch()['cnt'] ?? 0;
        if ($todayMeetingsCount > 0) {
            $dynamicAlerts[] = [
                'id' => null,
                'title' => 'Tư vấn online',
                'message' => 'Bạn có ' . $todayMeetingsCount . ' lịch hẹn tư vấn online hôm nay.',
                'icon' => 'fa-solid fa-video',
                'class' => 'type-success',
                'link' => 'index.php?page=consultations',
                'time' => 'Hôm nay'
            ];
        }
    }
    
    if ($role === 'nurse') {
        // Bệnh nhân nội trú đang điều trị
        $stmt = $conn->query("SELECT COUNT(*) as cnt FROM admissions WHERE status = 'active' AND deleted_at IS NULL");
        $activeInpatients = $stmt->fetch()['cnt'] ?? 0;
        if ($activeInpatients > 0) {
            $dynamicAlerts[] = [
                'id' => null,
                'title' => 'Bệnh nhân nội trú',
                'message' => 'Hiện có ' . $activeInpatients . ' bệnh nhân nội trú đang được điều trị.',
                'icon' => 'fa-solid fa-bed-pulse',
                'class' => 'type-warning',
                'link' => 'index.php?page=inpatient',
                'time' => 'Nội trú'
            ];
        }
    }
    
    if ($role === 'technician') {
        // Chỉ định CLS chờ xử lý
        $stmt = $conn->query("SELECT COUNT(*) as cnt FROM lab_orders WHERE status IN ('pending', 'in_progress')");
        $pendingLabs = $stmt->fetch()['cnt'] ?? 0;
        if ($pendingLabs > 0) {
            $dynamicAlerts[] = [
                'id' => null,
                'title' => 'Chỉ định CLS',
                'message' => 'Có ' . $pendingLabs . ' chỉ định cận lâm sàng đang chờ xử lý.',
                'icon' => 'fa-solid fa-flask-vial',
                'class' => 'type-danger',
                'link' => 'index.php?page=lab-orders',
                'time' => 'Chờ xử lý'
            ];
        }
    }
    
    if ($role === 'cashier') {
        // Hóa đơn chờ thanh toán
        $stmt = $conn->query("SELECT COUNT(*) as cnt FROM invoices WHERE status = 'pending' AND deleted_at IS NULL");
        $pendingInvoices = $stmt->fetch()['cnt'] ?? 0;
        if ($pendingInvoices > 0) {
            $dynamicAlerts[] = [
                'id' => null,
                'title' => 'Hóa đơn chờ thu',
                'message' => 'Có ' . $pendingInvoices . ' hóa đơn đang chờ thu tiền.',
                'icon' => 'fa-solid fa-file-invoice-dollar',
                'class' => 'type-warning',
                'link' => 'index.php?page=invoices',
                'time' => 'Chờ thanh toán'
            ];
        }
    }
    
    if ($role === 'receptionist') {
        // Hàng chờ tiếp đón
        $stmt = $conn->query("SELECT COUNT(*) as cnt FROM queue_tickets WHERE status = 'waiting' AND queue_date = CURDATE()");
        $waitingTickets = $stmt->fetch()['cnt'] ?? 0;
        if ($waitingTickets > 0) {
            $dynamicAlerts[] = [
                'id' => null,
                'title' => 'Hàng chờ khám',
                'message' => 'Hiện có ' . $waitingTickets . ' bệnh nhân trong hàng chờ khám.',
                'icon' => 'fa-solid fa-users',
                'class' => 'type-info',
                'link' => 'index.php?page=queue',
                'time' => 'Hàng chờ'
            ];
        }
    }
    
    if ($role === 'patient') {
        // Lấy ID bệnh nhân
        $stmt = $conn->prepare("SELECT id FROM patients WHERE user_id = :uid LIMIT 1");
        $stmt->execute([':uid' => $user['id']]);
        $patientId = $stmt->fetch()['id'] ?? 0;
        
        if ($patientId > 0) {
            // Lịch khám sắp tới
            $stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM appointments WHERE patient_id = :pid AND appointment_date >= NOW() AND status IN ('pending', 'confirmed') AND deleted_at IS NULL");
            $stmt->execute([':pid' => $patientId]);
            $upcomingAppts = $stmt->fetch()['cnt'] ?? 0;
            if ($upcomingAppts > 0) {
                $dynamicAlerts[] = [
                    'id' => null,
                    'title' => 'Lịch khám sắp tới',
                    'message' => 'Bạn có ' . $upcomingAppts . ' lịch hẹn khám chưa diễn ra.',
                    'icon' => 'fa-solid fa-calendar-check',
                    'class' => 'type-appointment',
                    'link' => 'index.php?page=appointments',
                    'time' => 'Sắp tới'
                ];
            }
            
            // Hóa đơn chưa thanh toán
            $stmt = $conn->prepare("SELECT COUNT(*) as cnt FROM invoices WHERE patient_id = :pid AND status = 'pending' AND deleted_at IS NULL");
            $stmt->execute([':pid' => $patientId]);
            $unpaidInvoices = $stmt->fetch()['cnt'] ?? 0;
            if ($unpaidInvoices > 0) {
                $dynamicAlerts[] = [
                    'id' => null,
                    'title' => 'Hóa đơn chưa trả',
                    'message' => 'Bạn có ' . $unpaidInvoices . ' hóa đơn cần được thanh toán.',
                    'icon' => 'fa-solid fa-file-invoice-dollar',
                    'class' => 'type-danger',
                    'link' => 'index.php?page=invoices',
                    'time' => 'Chưa trả'
                ];
            }
        }
    }
    
    if ($role === 'director') {
        // Tỷ lệ sử dụng giường bệnh
        $stmt = $conn->query("SELECT COUNT(*) as total FROM beds");
        $totalBeds = $stmt->fetch()['total'] ?? 0;
        $stmt = $conn->query("SELECT COUNT(*) as occupied FROM beds WHERE status = 'occupied'");
        $occupiedBeds = $stmt->fetch()['occupied'] ?? 0;
        $occupancyRate = $totalBeds > 0 ? ($occupiedBeds / $totalBeds) * 100 : 0;
        if ($occupancyRate > 85) {
            $dynamicAlerts[] = [
                'id' => null,
                'title' => 'Giường bệnh quá tải',
                'message' => 'Tỷ lệ sử dụng giường bệnh đã đạt mức cao: ' . round($occupancyRate, 1) . '%.',
                'icon' => 'fa-solid fa-bed-pulse',
                'class' => 'type-danger',
                'link' => 'index.php?page=reports',
                'time' => 'Cảnh báo'
            ];
        }
    }
}

// Gộp thông báo DB và thông báo động
$allNotifications = [];
foreach ($dbNotifications as $dn) {
    $icon = 'fa-solid fa-bell';
    $class = 'type-warning';
    $link = 'index.php?page=notifications';
    
    $titleLower = mb_strtolower($dn['title'], 'UTF-8');
    if (strpos($titleLower, 'lịch') !== false || strpos($titleLower, 'hẹn') !== false || strpos($titleLower, 'khám') !== false) {
        $icon = 'fa-solid fa-calendar-check';
        $class = 'type-appointment';
        $link = 'index.php?page=appointments';
    } elseif (strpos($titleLower, 'xét nghiệm') !== false || strpos($titleLower, 'cận lâm sàng') !== false || strpos($titleLower, 'kết quả') !== false) {
        $icon = 'fa-solid fa-flask-vial';
        $class = 'type-danger';
        $link = 'index.php?page=lab-orders';
    } elseif (strpos($titleLower, 'đơn thuốc') !== false || strpos($titleLower, 'thuốc') !== false) {
        $icon = 'fa-solid fa-capsules';
        $class = 'type-success';
        $link = 'index.php?page=prescriptions';
    } elseif (strpos($titleLower, 'hóa đơn') !== false || strpos($titleLower, 'thanh toán') !== false || strpos($titleLower, 'tiền') !== false) {
        $icon = 'fa-solid fa-file-invoice-dollar';
        $class = 'type-warning';
        $link = 'index.php?page=invoices';
    } elseif (strpos($titleLower, 'ca trực') !== false || strpos($titleLower, 'trực đêm') !== false) {
        $icon = 'fa-solid fa-clock';
        $class = 'type-info';
        $link = 'index.php?page=shifts';
    }
    
    $allNotifications[] = [
        'id' => $dn['id'],
        'title' => $dn['title'],
        'message' => $dn['message'],
        'icon' => $icon,
        'class' => $class,
        'link' => $link,
        'time' => date('H:i d/m/Y', strtotime($dn['created_at'])),
        'is_db' => true
    ];
}

foreach ($dynamicAlerts as $da) {
    $da['is_db'] = false;
    $da['id'] = null;
    $allNotifications[] = $da;
}

// Sắp xếp thông báo theo thời gian (thông báo DB có time cụ thể, dynamic alerts xếp sau)
usort($allNotifications, function($a, $b) {
    if ($a['is_db'] && !$b['is_db']) return -1;
    if (!$a['is_db'] && $b['is_db']) return 1;
    return 0;
});

$notifCount = count($allNotifications);

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
    'queue'         => 'Hàng chờ khám',
    'audit_logs'    => 'Audit Logs',
    'notifications' => 'Thông báo',
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
    'queue'         => 'Hoạt động Khám chữa bệnh',
    'notifications' => 'Thông báo',
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
    <style>
        .notif-item {
            position: relative;
            display: flex;
            align-items: center;
            padding: 12px 16px;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            transition: background-color 0.2s ease;
        }
        .notif-item:hover {
            background-color: rgba(0,0,0,0.02);
        }
        [data-theme="dark"] .notif-item:hover {
            background-color: rgba(255,255,255,0.02);
        }
        .notif-body {
            flex: 1;
            padding-right: 28px;
        }
        .btn-mark-read-single {
            position: absolute;
            right: 12px;
            top: 50%;
            transform: translateY(-50%);
            border: none;
            background: transparent;
            color: #0ea5e9;
            opacity: 0;
            transition: opacity 0.2s, transform 0.2s;
            cursor: pointer;
            width: 26px;
            height: 26px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
        }
        .notif-item:hover .btn-mark-read-single {
            opacity: 0.6;
        }
        .btn-mark-read-single:hover {
            opacity: 1 !important;
            background-color: rgba(14, 165, 233, 0.1);
            transform: translateY(-50%) scale(1.1);
        }
        [data-theme="dark"] .btn-mark-read-single:hover {
            background-color: rgba(14, 165, 233, 0.2);
        }
        [data-theme="dark"] .notif-item {
            border-bottom: 1px solid rgba(255,255,255,0.05);
        }
        .type-danger { background: rgba(239, 68, 68, 0.1); color: #ef4444; }
        .type-warning { background: rgba(245, 158, 11, 0.1); color: #f59e0b; }
        .type-success { background: rgba(34, 197, 94, 0.1); color: #22c55e; }
        .type-info { background: rgba(14, 165, 233, 0.1); color: #0ea5e9; }
        .type-appointment { background: rgba(168, 85, 247, 0.1); color: #a855f7; }
        .notif-icon {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 12px;
            flex-shrink: 0;
        }
        .notif-icon i { font-size: 16px; }
        .notif-time {
            font-size: 11px;
            color: #6b7280;
            margin-top: 4px;
        }
        .notif-text {
            font-size: 13px;
            line-height: 1.4;
        }
        .notif-header {
            padding: 12px 16px;
            border-bottom: 1px solid rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .notif-footer {
            padding: 8px 16px;
        }
        .notif-empty {
            padding: 40px 20px;
            text-align: center;
            color: #9ca3af;
        }
        .notif-empty i {
            font-size: 32px;
            margin-bottom: 8px;
            display: block;
        }
    </style>
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
        <li class="menu-label">Quản lý Con người</li>
        <li>
            <a href="index.php?page=users" class="<?= $currentPage === 'users' ? 'active' : '' ?>">
                <i class="fa-solid fa-users-gear"></i> <span>Quản lý Người dùng</span>
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
        <li>
            <a href="index.php?page=shifts" class="<?= $currentPage === 'shifts' ? 'active' : '' ?>">
                <i class="fa-solid fa-clock"></i> <span>Lịch trực ca</span>
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
                <i class="fa-solid fa-file-medical"></i> <span>Bệnh án</span>
            </a>
        </li>
        <li>
            <a href="index.php?page=inpatient" class="<?= $currentPage === 'inpatient' ? 'active' : '' ?>">
                <i class="fa-solid fa-bed-pulse"></i> <span>Chỉ định nội trú</span>
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
        <li class="menu-label">Thiết bị & Máy móc</li>
        <li>
            <a href="index.php?page=devices" class="<?= $currentPage === 'devices' ? 'active' : '' ?>">
                <i class="fa-solid fa-laptop-medical"></i> <span>Quản lý thiết bị</span>
            </a>
        </li>

        <?php elseif ($user['role'] === 'director'): ?>
        <li class="menu-label">Điều hành & Giám sát</li>
        <li>
            <a href="index.php?page=reports" class="<?= $currentPage === 'reports' ? 'active' : '' ?>">
                <i class="fa-solid fa-chart-bar"></i> <span>Báo cáo & Thống kê</span>
            </a>
        </li>
        <li>
            <a href="index.php?page=departments" class="<?= $currentPage === 'departments' ? 'active' : '' ?>">
                <i class="fa-solid fa-building-columns"></i> <span>Cơ cấu khoa/phòng</span>
            </a>
        </li>
        <li>
            <a href="index.php?page=devices" class="<?= $currentPage === 'devices' ? 'active' : '' ?>">
                <i class="fa-solid fa-laptop-medical"></i> <span>Máy móc y tế</span>
            </a>
        </li>
        <li>
            <a href="index.php?page=audit_logs" class="<?= $currentPage === 'audit_logs' ? 'active' : '' ?>">
                <i class="fa-solid fa-shield-halved"></i> <span>Audit Logs</span>
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
                    <div class="notif-list" style="max-height: 400px; overflow-y: auto;">
                        <?php if ($notifCount > 0): ?>
                            <?php foreach ($allNotifications as $n): ?>
                            <div class="notif-item <?= $n['is_db'] ? 'db-notif' : 'dyn-notif' ?>" data-id="<?= $n['id'] ?? '' ?>">
                                <div class="notif-icon <?= $n['class'] ?>"><i class="<?= $n['icon'] ?>"></i></div>
                                <div class="notif-body" onclick="window.location.href='<?= $n['link'] ?>'" style="cursor:pointer;">
                                    <div class="notif-text"><strong><?= htmlspecialchars($n['title']) ?></strong><br><?= htmlspecialchars($n['message']) ?></div>
                                    <div class="notif-time"><i class="fa-regular fa-clock me-1"></i><?= $n['time'] ?></div>
                                </div>
                                <?php if ($n['is_db'] && $n['id']): ?>
                                <button class="btn-mark-read-single" title="Đánh dấu đã đọc" onclick="markSingleAsRead(event, <?= $n['id'] ?>)">
                                    <i class="fa-solid fa-check"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        <?php else: ?>
                        <div class="notif-empty">
                            <i class="fa-regular fa-bell-slash"></i>
                            Không có thông báo mới
                        </div>
                        <?php endif; ?>
                    </div>
                    <div class="notif-footer d-flex justify-content-between px-3 py-2 border-top">
                        <?php if (count($dbNotifications) > 0): ?>
                        <a href="#" onclick="markAllNotificationsAsRead(event)" class="text-decoration-none text-muted small"><i class="fa-solid fa-check-double me-1"></i>Đọc tất cả</a>
                        <?php else: ?>
                        <span></span>
                        <?php endif; ?>
                        <a href="index.php?page=notifications" class="text-decoration-none text-primary small"><i class="fa-solid fa-list me-1"></i>Xem tất cả</a>
                    </div>
                </div>
            </div>

            <script>
            function markSingleAsRead(event, id) {
                event.preventDefault();
                event.stopPropagation();
                fetch('index.php?page=shifts&action=markNotifRead&id=' + id, {
                    method: 'GET',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                }).then(response => response.json()).then(data => {
                    if (data.success) {
                        location.reload();
                    }
                }).catch(() => location.reload());
            }
            
            function markAllNotificationsAsRead(event) {
                event.preventDefault();
                fetch('index.php?page=shifts&action=markAllNotifsRead', {
                    method: 'GET',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                }).then(() => location.reload());
            }
            </script>

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