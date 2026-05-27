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

<!-- Stats Overview for Accounts -->
<?php
$totalUsers = count($users);
$adminCount = 0;
$staffCount = 0;
$patientCount = 0;

foreach ($users as $u) {
    if ($u['role'] === 'admin') {
        $adminCount++;
    } elseif ($u['role'] === 'patient') {
        $patientCount++;
    } else {
        $staffCount++;
    }
}
?>

<div class="row g-3 mb-4" data-aos="fade-down">
    <div class="col-6 col-lg-4">
        <div class="content-card p-3 border-0 shadow-sm d-flex align-items-center gap-3" style="border-radius: 16px; background: linear-gradient(135deg, #eff6ff, #dbeafe);">
            <div class="d-flex align-items-center justify-content-center text-primary" style="width: 48px; height: 48px; border-radius: 12px; background: #white; font-size: 20px; box-shadow: 0 4px 10px rgba(59, 130, 246, 0.1);">
                <i class="fa-solid fa-users-gear"></i>
            </div>
            <div>
                <small class="text-secondary d-block" style="font-size: 12px; font-weight: 500;">Tổng số tài khoản</small>
                <h4 class="fw-bold m-0 text-primary"><?= $totalUsers ?></h4>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-4">
        <div class="content-card p-3 border-0 shadow-sm d-flex align-items-center gap-3" style="border-radius: 16px; background: linear-gradient(135deg, #f0fdf4, #dcfce7);">
            <div class="d-flex align-items-center justify-content-center text-success" style="width: 48px; height: 48px; border-radius: 12px; background: #white; font-size: 20px; box-shadow: 0 4px 10px rgba(22, 163, 74, 0.1);">
                <i class="fa-solid fa-user-shield"></i>
            </div>
            <div>
                <small class="text-secondary d-block" style="font-size: 12px; font-weight: 500;">Quản trị & Giám đốc</small>
                <h4 class="fw-bold m-0 text-success"><?= $adminCount + (isset($rolesCount['director']) ? $rolesCount['director'] : 0) ?></h4>
            </div>
        </div>
    </div>

    <div class="col-6 col-lg-4">
        <div class="content-card p-3 border-0 shadow-sm d-flex align-items-center gap-3" style="border-radius: 16px; background: linear-gradient(135deg, #fdf2f8, #fce7f3);">
            <div class="d-flex align-items-center justify-content-center text-pink" style="width: 48px; height: 48px; border-radius: 12px; background: #white; font-size: 20px; box-shadow: 0 4px 10px rgba(219, 39, 119, 0.1); color: #db2777;">
                <i class="fa-solid fa-user-nurse"></i>
            </div>
            <div>
                <small class="text-secondary d-block" style="font-size: 12px; font-weight: 500;">Nhân viên y tế & Lễ tân</small>
                <h4 class="fw-bold m-0" style="color: #db2777;"><?= $staffCount ?></h4>
            </div>
        </div>
    </div>
</div>

<!-- Main User Management Card -->
<div class="content-card" data-aos="fade-up">
    <div class="card-header d-flex flex-column flex-md-row justify-content-between align-items-md-center gap-3">
        <h5 class="m-0"><i class="fa-solid fa-users me-2 text-primary"></i>Danh sách Tài khoản người dùng</h5>
        <div class="d-flex flex-wrap align-items-center gap-2">
            <!-- Search bar -->
            <div class="input-group" style="width: 250px;">
                <span class="input-group-text bg-white border-end-0" style="border-radius: 20px 0 0 20px;">
                    <i class="fa-solid fa-magnifying-glass text-muted" style="font-size: 13px;"></i>
                </span>
                <input type="text" class="form-control border-start-0 ps-0 table-search-js" data-table="usersTable" placeholder="Tìm tài khoản..." style="border-radius: 0 20px 20px 0; font-size: 13px; outline: none; box-shadow: none;">
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
                        <th style="width: 70px;">Mã</th>
                        <th>Họ tên</th>
                        <th>Thông tin liên hệ</th>
                        <th>Vai trò</th>
                        <th>Ngày tạo</th>
                        <th style="width: 120px; text-align: center;">Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($users)): ?>
                    <tr>
                        <td colspan="6" class="text-center text-muted py-5">
                            <i class="fa-solid fa-inbox d-block mb-2" style="font-size: 24px;"></i>
                            Không có tài khoản nào được đăng ký.
                        </td>
                    </tr>
                    <?php else: ?>
                    <?php foreach ($users as $u): ?>
                    <tr>
                        <td>
                            <span class="badge bg-light text-secondary border px-2.5 py-1" style="font-size: 11px; font-weight: 600; border-radius: 6px;">
                                US-<?= str_pad($u['id'], 4, '0', STR_PAD_LEFT) ?>
                            </span>
                        </td>
                        <td>
                            <div class="d-flex align-items-center gap-2.5">
                                <?php
                                $firstChar = strtoupper(substr($u['name'], 0, 1));
                                // Different color avatar based on role
                                $avatarBg = 'linear-gradient(135deg, #3b82f6, #1d4ed8)'; // Default blue
                                if ($u['role'] === 'admin') $avatarBg = 'linear-gradient(135deg, #ef4444, #b91c1c)';
                                elseif ($u['role'] === 'director') $avatarBg = 'linear-gradient(135deg, #64748b, #475569)';
                                elseif ($u['role'] === 'doctor') $avatarBg = 'linear-gradient(135deg, #0ea5e9, #0369a1)';
                                elseif ($u['role'] === 'nurse') $avatarBg = 'linear-gradient(135deg, #14b8a6, #0f766e)';
                                elseif ($u['role'] === 'pharmacist') $avatarBg = 'linear-gradient(135deg, #ec4899, #be185d)';
                                elseif ($u['role'] === 'cashier') $avatarBg = 'linear-gradient(135deg, #eab308, #ca8a04)';
                                ?>
                                <div class="d-flex align-items-center justify-content-center text-white fw-bold" 
                                     style="width: 34px; height: 34px; border-radius: 50%; font-size: 13px; background: <?= $avatarBg ?>;">
                                    <?= $firstChar ?>
                                </div>
                                <strong class="text-dark" style="font-size: 14.5px;"><?= htmlspecialchars($u['name']) ?></strong>
                            </div>
                        </td>
                        <td>
                            <span class="text-dark d-block" style="font-size: 13.5px; font-weight: 500;"><?= htmlspecialchars($u['email']) ?></span>
                            <span class="text-muted d-block" style="font-size: 12px;"><i class="fa-solid fa-phone me-1" style="font-size: 10px;"></i><?= htmlspecialchars($u['phone'] ?: '-') ?></span>
                        </td>
                        <td>
                            <?php
                            $roleClass = 'bg-secondary';
                            $roleName = $u['role'];
                            $roleBg = '';
                            switch ($u['role']) {
                                case 'admin': 
                                    $roleBg = 'background-color: rgba(239, 68, 68, 0.1) !important; color: #ef4444 !important; border: 1px solid rgba(239, 68, 68, 0.2);'; 
                                    $roleName = 'Quản trị viên'; 
                                    break;
                                case 'doctor': 
                                    $roleBg = 'background-color: rgba(14, 165, 233, 0.1) !important; color: #0ea5e9 !important; border: 1px solid rgba(14, 165, 233, 0.2);'; 
                                    $roleName = 'Bác sĩ'; 
                                    break;
                                case 'nurse': 
                                    $roleBg = 'background-color: rgba(20, 184, 166, 0.1) !important; color: #14b8a6 !important; border: 1px solid rgba(20, 184, 166, 0.2);'; 
                                    $roleName = 'Y tá / Điều dưỡng'; 
                                    break;
                                case 'patient': 
                                    $roleBg = 'background-color: rgba(34, 197, 94, 0.1) !important; color: #22c55e !important; border: 1px solid rgba(34, 197, 94, 0.2);'; 
                                    $roleName = 'Bệnh nhân'; 
                                    break;
                                case 'receptionist': 
                                    $roleBg = 'background-color: rgba(249, 115, 22, 0.1) !important; color: #f97316 !important; border: 1px solid rgba(249, 115, 22, 0.2);'; 
                                    $roleName = 'Lễ tân'; 
                                    break;
                                case 'pharmacist': 
                                    $roleBg = 'background-color: rgba(236, 72, 153, 0.1) !important; color: #ec4899 !important; border: 1px solid rgba(236, 72, 153, 0.2);'; 
                                    $roleName = 'Dược sĩ'; 
                                    break;
                                case 'technician': 
                                    $roleBg = 'background-color: rgba(139, 92, 246, 0.1) !important; color: #8b5cf6 !important; border: 1px solid rgba(139, 92, 246, 0.2);'; 
                                    $roleName = 'Kỹ thuật viên'; 
                                    break;
                                case 'director': 
                                    $roleBg = 'background-color: rgba(100, 116, 139, 0.1) !important; color: #64748b !important; border: 1px solid rgba(100, 116, 139, 0.2);'; 
                                    $roleName = 'Ban giám đốc'; 
                                    break;
                                case 'cashier': 
                                    $roleBg = 'background-color: rgba(234, 179, 8, 0.1) !important; color: #ca8a04 !important; border: 1px solid rgba(234, 179, 8, 0.2);'; 
                                    $roleName = 'Thu ngân'; 
                                    break;
                            }
                            ?>
                            <span class="badge text-uppercase" style="font-size: 11px; font-weight: 600; padding: 5px 10px; border-radius: 6px; <?= $roleBg ?>"><?= htmlspecialchars($roleName) ?></span>
                        </td>
                        <td>
                            <span class="text-secondary" style="font-size: 13px;">
                                <?= date('d/m/Y H:i', strtotime($u['created_at'])) ?>
                            </span>
                        </td>
                        <td>
                            <div class="d-flex justify-content-center align-items-center gap-2">
                                <a href="index.php?page=users&action=edit&id=<?= $u['id'] ?>" class="btn-action btn-edit" title="Chỉnh sửa tài khoản" style="border-radius: 8px; width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;">
                                    <i class="fa-solid fa-pen-to-square" style="font-size: 12px;"></i>
                                </a>
                                <?php if ($u['id'] != $_SESSION['user']['id']): ?>
                                <button onclick="confirmDelete('index.php?page=users&action=delete&id=<?= $u['id'] ?>', '<?= htmlspecialchars($u['name']) ?>')" 
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
