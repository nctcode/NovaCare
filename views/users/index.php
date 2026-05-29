<!-- Account Management List -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-custom alert-dismissible fade show">
        <i class="fa-solid fa-check-circle"></i> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-custom alert-dismissible fade show">
        <i class="fa-solid fa-triangle-exclamation"></i> <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php
// Trợ lý hiển thị dữ liệu
if (!function_exists('getAvatarBg')) {
    function getAvatarBg($role) {
        switch ($role) {
            case 'admin': return 'linear-gradient(135deg, #ef4444, #b91c1c)';
            case 'director': return 'linear-gradient(135deg, #64748b, #475569)';
            case 'doctor': return 'linear-gradient(135deg, #0ea5e9, #0369a1)';
            case 'nurse': return 'linear-gradient(135deg, #14b8a6, #0f766e)';
            case 'pharmacist': return 'linear-gradient(135deg, #ec4899, #be185d)';
            case 'cashier': return 'linear-gradient(135deg, #eab308, #ca8a04)';
            case 'receptionist': return 'linear-gradient(135deg, #f97316, #d97706)';
            case 'technician': return 'linear-gradient(135deg, #8b5cf6, #6d28d9)';
            case 'patient': return 'linear-gradient(135deg, #22c55e, #15803d)';
            default: return 'linear-gradient(135deg, #3b82f6, #1d4ed8)';
        }
    }
}

if (!function_exists('getRoleBgStyle')) {
    function getRoleBgStyle($role) {
        switch ($role) {
            case 'admin': 
                return 'background-color: rgba(239, 68, 68, 0.1) !important; color: #ef4444 !important; border: 1px solid rgba(239, 68, 68, 0.2);';
            case 'doctor': 
                return 'background-color: rgba(14, 165, 233, 0.1) !important; color: #0ea5e9 !important; border: 1px solid rgba(14, 165, 233, 0.2);';
            case 'nurse': 
                return 'background-color: rgba(20, 184, 166, 0.1) !important; color: #14b8a6 !important; border: 1px solid rgba(20, 184, 166, 0.2);';
            case 'patient': 
                return 'background-color: rgba(34, 197, 94, 0.1) !important; color: #22c55e !important; border: 1px solid rgba(34, 197, 94, 0.2);';
            case 'receptionist': 
                return 'background-color: rgba(249, 115, 22, 0.1) !important; color: #f97316 !important; border: 1px solid rgba(249, 115, 22, 0.2);';
            case 'pharmacist': 
                return 'background-color: rgba(236, 72, 153, 0.1) !important; color: #ec4899 !important; border: 1px solid rgba(236, 72, 153, 0.2);';
            case 'technician': 
                return 'background-color: rgba(139, 92, 246, 0.1) !important; color: #8b5cf6 !important; border: 1px solid rgba(139, 92, 246, 0.2);';
            case 'director': 
                return 'background-color: rgba(100, 116, 139, 0.1) !important; color: #64748b !important; border: 1px solid rgba(100, 116, 139, 0.2);';
            case 'cashier': 
                return 'background-color: rgba(234, 179, 8, 0.1) !important; color: #ca8a04 !important; border: 1px solid rgba(234, 179, 8, 0.2);';
            default:
                return 'background-color: rgba(100, 116, 139, 0.1) !important; color: #64748b !important;';
        }
    }
}

if (!function_exists('getRoleNameVietnamese')) {
    function getRoleNameVietnamese($role) {
        switch ($role) {
            case 'admin': return 'Quản trị viên';
            case 'doctor': return 'Bác sĩ';
            case 'nurse': return 'Y tá / Điều dưỡng';
            case 'patient': return 'Bệnh nhân';
            case 'receptionist': return 'Lễ tân';
            case 'pharmacist': return 'Dược sĩ';
            case 'technician': return 'Kỹ thuật viên';
            case 'director': return 'Ban giám đốc';
            case 'cashier': return 'Thu ngân';
            default: return $role;
        }
    }
}

$totalUsers = $rolesCount['all'] ?? 0;
$staffCount = ($rolesCount['doctor'] ?? 0) + ($rolesCount['nurse'] ?? 0) + ($rolesCount['technician'] ?? 0) + ($rolesCount['receptionist'] ?? 0) + ($rolesCount['pharmacist'] ?? 0) + ($rolesCount['cashier'] ?? 0);
$patientCount = $rolesCount['patient'] ?? 0;
?>

<style>
    .role-pills {
        gap: 8px;
        flex-wrap: wrap;
    }
    .role-pills .nav-link {
        font-size: 13px;
        font-weight: 600;
        color: #64748b;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        border-radius: 20px;
        padding: 8px 16px;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .role-pills .nav-link:hover {
        background: #f1f5f9;
        color: #0f172a;
        border-color: #cbd5e1;
    }
    .role-pills .nav-link.active {
        background: #3b82f6;
        color: #ffffff;
        border-color: #3b82f6;
        box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);
    }
    .role-badge {
        font-size: 10.5px;
        font-weight: 700;
        padding: 2px 7px;
        border-radius: 10px;
        background: rgba(0, 0, 0, 0.06);
        color: inherit;
    }
    .role-pills .nav-link.active .role-badge {
        background: rgba(255, 255, 255, 0.2);
    }
    .nav-user-view {
        border-radius: 16px;
    }
</style>

<!-- Stats Overview for Accounts -->
<div class="row g-3 mb-4" data-aos="fade-down">
    <div class="col-6 col-lg-4">
        <div class="content-card p-3 border-0 shadow-sm d-flex align-items-center gap-3" style="border-radius: 16px; background: linear-gradient(135deg, #eff6ff, #dbeafe);">
            <div class="d-flex align-items-center justify-content-center text-primary" style="width: 48px; height: 48px; border-radius: 12px; background: #ffffff; font-size: 20px; box-shadow: 0 4px 10px rgba(59, 130, 246, 0.1);">
                <i class="fa-solid fa-users-gear"></i>
            </div>
            <div>
                <small class="text-secondary d-block" style="font-size: 12px; font-weight: 500;">Tổng số người dùng</small>
                <h4 class="fw-bold m-0 text-primary"><?= $totalUsers ?></h4>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-4">
        <div class="content-card p-3 border-0 shadow-sm d-flex align-items-center gap-3" style="border-radius: 16px; background: linear-gradient(135deg, #f0fdf4, #dcfce7);">
            <div class="d-flex align-items-center justify-content-center text-success" style="width: 48px; height: 48px; border-radius: 12px; background: #ffffff; font-size: 20px; box-shadow: 0 4px 10px rgba(22, 163, 74, 0.1);">
                <i class="fa-solid fa-user-doctor"></i>
            </div>
            <div>
                <small class="text-secondary d-block" style="font-size: 12px; font-weight: 500;">Nhân viên & Y tế</small>
                <h4 class="fw-bold m-0 text-success"><?= $staffCount + ($rolesCount['admin'] ?? 0) + ($rolesCount['director'] ?? 0) ?></h4>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-4">
        <div class="content-card p-3 border-0 shadow-sm d-flex align-items-center gap-3" style="border-radius: 16px; background: linear-gradient(135deg, #fdf2f8, #fce7f3);">
            <div class="d-flex align-items-center justify-content-center" style="width: 48px; height: 48px; border-radius: 12px; background: #ffffff; font-size: 20px; box-shadow: 0 4px 10px rgba(219, 39, 119, 0.1); color: #db2777;">
                <i class="fa-solid fa-hospital-user"></i>
            </div>
            <div>
                <small class="text-secondary d-block" style="font-size: 12px; font-weight: 500;">Bệnh nhân đăng ký</small>
                <h4 class="fw-bold m-0" style="color: #db2777;"><?= $patientCount ?></h4>
            </div>
        </div>
    </div>
</div>

<!-- Tabs Pill Role Filter Selector -->
<div class="mb-4" data-aos="fade-down">
    <?php
    $roleTabs = [
        'all'          => ['label' => 'Tất cả', 'icon' => 'fa-users'],
        'doctor'       => ['label' => 'Bác sĩ', 'icon' => 'fa-user-doctor'],
        'nurse'        => ['label' => 'Y tá / Điều dưỡng', 'icon' => 'fa-user-nurse'],
        'technician'   => ['label' => 'Kỹ thuật viên', 'icon' => 'fa-flask-vial'],
        'receptionist' => ['label' => 'Lễ tân', 'icon' => 'fa-address-book'],
        'pharmacist'   => ['label' => 'Dược sĩ', 'icon' => 'fa-pills'],
        'cashier'      => ['label' => 'Thu ngân', 'icon' => 'fa-coins'],
        'director'     => ['label' => 'Ban giám đốc', 'icon' => 'fa-user-tie'],
        'patient'      => ['label' => 'Bệnh nhân', 'icon' => 'fa-hospital-user'],
        'admin'        => ['label' => 'Quản trị viên', 'icon' => 'fa-user-shield'],
    ];
    ?>
    <div class="nav nav-pills role-pills bg-white p-2.5 shadow-sm rounded-4 border border-light">
        <?php foreach ($roleTabs as $rKey => $rTab): ?>
            <a href="index.php?page=users&role=<?= $rKey ?>" 
               class="nav-link <?= $activeRole === $rKey ? 'active' : '' ?>">
                <i class="fa-solid <?= $rTab['icon'] ?>"></i>
                <?= $rTab['label'] ?>
                <span class="role-badge"><?= $rolesCount[$rKey] ?? 0 ?></span>
            </a>
        <?php endforeach; ?>
    </div>
</div>

<!-- Main User Management Card -->
<div class="content-card" data-aos="fade-up">
    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <h5 class="m-0"><i class="fa-solid fa-users me-2 text-primary"></i>Danh sách: <?= $roleTabs[$activeRole]['label'] ?></h5>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <!-- Search bar -->
            <div class="input-group" style="width: 250px;">
                <span class="input-group-text bg-white border-end-0" style="border-radius: 20px 0 0 20px;">
                    <i class="fa-solid fa-magnifying-glass text-muted" style="font-size: 13px;"></i>
                </span>
                <input type="text" class="form-control border-start-0 ps-0 table-search-js" data-table="usersTable" placeholder="Tìm người dùng..." style="border-radius: 0 20px 20px 0; font-size: 13px; outline: none; box-shadow: none;">
            </div>
            <a href="index.php?page=users&action=create" class="btn-action btn-add py-2" style="border-radius: 20px;">
                <i class="fa-solid fa-plus me-1"></i> Thêm tài khoản
            </a>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-wrapper">
            <table class="data-table" id="usersTable">
                <thead>
                    <tr>
                        <?php if ($activeRole === 'all'): ?>
                            <th style="width: 70px;">Mã</th>
                            <th>Họ tên</th>
                            <th>Thông tin liên hệ</th>
                            <th>Vai trò</th>
                            <th>Ngày tạo</th>
                            <th style="width: 140px; text-align: center;">Thao tác</th>
                        <?php elseif ($activeRole === 'doctor'): ?>
                            <th style="width: 80px;">Mã BS</th>
                            <th>Họ tên</th>
                            <th>Chuyên khoa</th>
                            <th>Kinh nghiệm</th>
                            <th>Khoa công tác</th>
                            <th>Liên hệ</th>
                            <th style="width: 140px; text-align: center;">Thao tác</th>
                        <?php elseif ($activeRole === 'nurse'): ?>
                            <th style="width: 80px;">Mã YT</th>
                            <th>Họ tên</th>
                            <th>Khoa công tác</th>
                            <th>Liên hệ</th>
                            <th style="width: 140px; text-align: center;">Thao tác</th>
                        <?php elseif ($activeRole === 'technician'): ?>
                            <th style="width: 90px;">Mã KTV</th>
                            <th>Họ tên</th>
                            <th>Chuyên môn</th>
                            <th>Khoa công tác</th>
                            <th>Liên hệ</th>
                            <th style="width: 140px; text-align: center;">Thao tác</th>
                        <?php elseif ($activeRole === 'patient'): ?>
                            <th style="width: 90px;">Mã BN</th>
                            <th>Họ tên</th>
                            <th>Ngày sinh</th>
                            <th>Giới tính</th>
                            <th>Nhóm máu</th>
                            <th>Địa chỉ</th>
                            <th>Số điện thoại</th>
                            <th style="width: 140px; text-align: center;">Thao tác</th>
                        <?php else: // admin, director, receptionist, pharmacist, cashier ?>
                            <th style="width: 70px;">Mã</th>
                            <th>Họ tên</th>
                            <th>Email</th>
                            <th>Số điện thoại</th>
                            <th>Ngày đăng ký</th>
                            <th style="width: 140px; text-align: center;">Thao tác</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="10" class="text-center text-muted py-5">
                            <i class="fa-solid fa-inbox d-block mb-2" style="font-size: 24px;"></i>
                            Không có tài khoản nào được đăng ký trong vai trò này.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($users as $u): ?>
                        <?php 
                        $uId = $u['user_id'] ?? $u['id']; 
                        $genderMap = ['male' => 'Nam', 'female' => 'Nữ', 'other' => 'Khác', 'Nam' => 'Nam', 'Nữ' => 'Nữ', 'Khác' => 'Khác'];
                        ?>
                    <tr>
                        <?php if ($activeRole === 'all'): ?>
                            <td>
                                <span class="badge bg-light text-secondary border px-2.5 py-1" style="font-size: 11px; font-weight: 600; border-radius: 6px;">
                                    US-<?= str_pad($uId, 4, '0', STR_PAD_LEFT) ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="d-flex align-items-center justify-content-center text-white fw-bold" 
                                         style="width: 34px; height: 34px; border-radius: 50%; font-size: 13px; background: <?= getAvatarBg($u['role']) ?>;">
                                        <?= mb_strtoupper(mb_substr($u['name'], 0, 1, 'utf-8')) ?>
                                    </div>
                                    <strong class="text-dark" style="font-size: 14.5px;"><?= htmlspecialchars($u['name']) ?></strong>
                                </div>
                            </td>
                            <td>
                                <span class="text-dark d-block" style="font-size: 13.5px; font-weight: 500;"><?= htmlspecialchars($u['email']) ?></span>
                                <span class="text-muted d-block" style="font-size: 12px;"><i class="fa-solid fa-phone me-1" style="font-size: 10px;"></i><?= htmlspecialchars($u['phone'] ?: '-') ?></span>
                            </td>
                            <td>
                                <span class="badge text-uppercase" style="font-size: 11px; font-weight: 600; padding: 5px 10px; border-radius: 6px; <?= getRoleBgStyle($u['role']) ?>"><?= getRoleNameVietnamese($u['role']) ?></span>
                            </td>
                            <td>
                                <span class="text-secondary" style="font-size: 13px;">
                                    <?= date('d/m/Y H:i', strtotime($u['created_at'])) ?>
                                </span>
                            </td>

                        <?php elseif ($activeRole === 'doctor'): ?>
                            <td>
                                <span class="badge bg-light text-primary border px-2.5 py-1" style="font-size: 11px; font-weight: 600; border-radius: 6px;">
                                    BS-<?= str_pad($u['id'], 4, '0', STR_PAD_LEFT) ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="d-flex align-items-center justify-content-center text-white fw-bold" 
                                         style="width: 34px; height: 34px; border-radius: 50%; font-size: 13px; background: linear-gradient(135deg, #0ea5e9, #0369a1);">
                                        <?= mb_strtoupper(mb_substr($u['name'], 0, 1, 'utf-8')) ?>
                                    </div>
                                    <strong class="text-dark" style="font-size: 14.5px;">BS. <?= htmlspecialchars($u['name']) ?></strong>
                                </div>
                            </td>
                            <td><span class="text-dark fw-semibold" style="font-size: 13.5px;"><?= htmlspecialchars($u['specialty'] ?: '-') ?></span></td>
                            <td><span class="badge bg-info-subtle text-info px-2.5 py-1" style="font-size: 11.5px; border-radius: 6px; font-weight: 600; background-color: rgba(14, 165, 233, 0.08) !important;"><?= htmlspecialchars($u['experience_years']) ?> năm</span></td>
                            <td><span class="text-secondary" style="font-size: 13px;"><i class="fa-solid fa-hospital text-muted me-1"></i><?= htmlspecialchars($u['department_name'] ?: 'Chưa gán') ?></span></td>
                            <td>
                                <span class="text-dark d-block" style="font-size: 13px; font-weight: 500;"><?= htmlspecialchars($u['email']) ?></span>
                                <span class="text-muted d-block" style="font-size: 11.5px;"><i class="fa-solid fa-phone me-1"></i><?= htmlspecialchars($u['phone'] ?: '-') ?></span>
                            </td>

                        <?php elseif ($activeRole === 'nurse'): ?>
                            <td>
                                <span class="badge bg-light text-teal border px-2.5 py-1" style="font-size: 11px; font-weight: 600; border-radius: 6px; color: #14b8a6 !important;">
                                    YT-<?= str_pad($u['id'], 4, '0', STR_PAD_LEFT) ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="d-flex align-items-center justify-content-center text-white fw-bold" 
                                         style="width: 34px; height: 34px; border-radius: 50%; font-size: 13px; background: linear-gradient(135deg, #14b8a6, #0f766e);">
                                        <?= mb_strtoupper(mb_substr($u['name'], 0, 1, 'utf-8')) ?>
                                    </div>
                                    <strong class="text-dark" style="font-size: 14.5px;"><?= htmlspecialchars($u['name']) ?></strong>
                                </div>
                            </td>
                            <td><span class="text-secondary" style="font-size: 13px;"><i class="fa-solid fa-hospital text-muted me-1"></i><?= htmlspecialchars($u['department_name'] ?: 'Chưa gán') ?></span></td>
                            <td>
                                <span class="text-dark d-block" style="font-size: 13px; font-weight: 500;"><?= htmlspecialchars($u['email']) ?></span>
                                <span class="text-muted d-block" style="font-size: 11.5px;"><i class="fa-solid fa-phone me-1"></i><?= htmlspecialchars($u['phone'] ?: '-') ?></span>
                            </td>

                        <?php elseif ($activeRole === 'technician'): ?>
                            <td>
                                <span class="badge bg-light border px-2.5 py-1" style="font-size: 11px; font-weight: 600; border-radius: 6px; color: #8b5cf6 !important; border-color: rgba(139, 92, 246, 0.2) !important;">
                                    KTV-<?= str_pad($u['id'], 4, '0', STR_PAD_LEFT) ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="d-flex align-items-center justify-content-center text-white fw-bold" 
                                         style="width: 34px; height: 34px; border-radius: 50%; font-size: 13px; background: linear-gradient(135deg, #8b5cf6, #6d28d9);">
                                        <?= mb_strtoupper(mb_substr($u['name'], 0, 1, 'utf-8')) ?>
                                    </div>
                                    <strong class="text-dark" style="font-size: 14.5px;"><?= htmlspecialchars($u['name']) ?></strong>
                                </div>
                            </td>
                            <td><span class="text-dark fw-semibold" style="font-size: 13.5px;"><?= htmlspecialchars($u['specialty'] ?: '-') ?></span></td>
                            <td><span class="text-secondary" style="font-size: 13px;"><i class="fa-solid fa-hospital text-muted me-1"></i><?= htmlspecialchars($u['department_name'] ?: 'Chưa gán') ?></span></td>
                            <td>
                                <span class="text-dark d-block" style="font-size: 13px; font-weight: 500;"><?= htmlspecialchars($u['email']) ?></span>
                                <span class="text-muted d-block" style="font-size: 11.5px;"><i class="fa-solid fa-phone me-1"></i><?= htmlspecialchars($u['phone'] ?: '-') ?></span>
                            </td>

                        <?php elseif ($activeRole === 'patient'): ?>
                            <td>
                                <span class="badge bg-light text-success border px-2.5 py-1" style="font-size: 11px; font-weight: 600; border-radius: 6px;">
                                    BN-<?= str_pad($u['id'], 5, '0', STR_PAD_LEFT) ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="d-flex align-items-center justify-content-center text-white fw-bold" 
                                         style="width: 34px; height: 34px; border-radius: 50%; font-size: 13px; background: linear-gradient(135deg, #22c55e, #15803d);">
                                        <?= mb_strtoupper(mb_substr($u['name'], 0, 1, 'utf-8')) ?>
                                    </div>
                                    <strong class="text-dark" style="font-size: 14.5px;"><?= htmlspecialchars($u['name']) ?></strong>
                                </div>
                            </td>
                            <td><span class="text-dark" style="font-size: 13px;"><?= $u['date_of_birth'] ? date('d/m/Y', strtotime($u['date_of_birth'])) : '-' ?></span></td>
                            <td><span class="badge bg-light text-dark border px-2.5 py-1" style="font-size: 12px;"><?= $genderMap[$u['gender']] ?? 'N/A' ?></span></td>
                            <td><span class="badge bg-danger-subtle text-danger px-2.5 py-1" style="font-size: 12px; background-color: rgba(239, 68, 68, 0.08) !important;"><?= htmlspecialchars($u['blood_type'] ?: '-') ?></span></td>
                            <td><span class="text-secondary text-truncate d-block" style="max-width: 180px; font-size: 13px;" title="<?= htmlspecialchars($u['address']) ?>"><?= htmlspecialchars($u['address'] ?: '-') ?></span></td>
                            <td><span class="text-dark fw-semibold" style="font-size: 13px;"><?= htmlspecialchars($u['phone'] ?: '-') ?></span></td>

                        <?php else: // admin, director, receptionist, pharmacist, cashier ?>
                            <td>
                                <span class="badge bg-light text-secondary border px-2.5 py-1" style="font-size: 11px; font-weight: 600; border-radius: 6px;">
                                    US-<?= str_pad($uId, 4, '0', STR_PAD_LEFT) ?>
                                </span>
                            </td>
                            <td>
                                <div class="d-flex align-items-center gap-2.5">
                                    <div class="d-flex align-items-center justify-content-center text-white fw-bold" 
                                         style="width: 34px; height: 34px; border-radius: 50%; font-size: 13px; background: <?= getAvatarBg($activeRole) ?>;">
                                        <?= mb_strtoupper(mb_substr($u['name'], 0, 1, 'utf-8')) ?>
                                    </div>
                                    <strong class="text-dark" style="font-size: 14.5px;"><?= htmlspecialchars($u['name']) ?></strong>
                                </div>
                            </td>
                            <td><span class="text-dark" style="font-size: 13.5px;"><?= htmlspecialchars($u['email']) ?></span></td>
                            <td><span class="text-dark fw-medium" style="font-size: 13px;"><?= htmlspecialchars($u['phone'] ?: '-') ?></span></td>
                            <td><span class="text-secondary" style="font-size: 13px;"><?= date('d/m/Y', strtotime($u['created_at'])) ?></span></td>
                        <?php endif; ?>

                        <!-- Thao tác -->
                        <td>
                            <div class="d-flex justify-content-center align-items-center gap-2">
                                <a href="index.php?page=users&action=view&id=<?= $uId ?>" class="btn-action btn-view" title="Xem thông tin chi tiết" style="border-radius: 8px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; background: rgba(59, 130, 246, 0.08); color: #3b82f6; border: none;">
                                    <i class="fa-solid fa-eye" style="font-size: 12px;"></i>
                                </a>
                                <a href="index.php?page=users&action=edit&id=<?= $uId ?>" class="btn-action btn-edit" title="Chỉnh sửa tài khoản" style="border-radius: 8px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                    <i class="fa-solid fa-pen-to-square" style="font-size: 12px;"></i>
                                </a>
                                <?php if ($uId != $_SESSION['user']['id']): ?>
                                <button onclick="confirmDelete('index.php?page=users&action=delete&id=<?= $uId ?>', '<?= htmlspecialchars($u['name']) ?>')" 
                                        class="btn-action btn-delete" title="Xóa tài khoản" style="border-radius: 8px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                    <i class="fa-solid fa-trash" style="font-size: 12px;"></i>
                                </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

