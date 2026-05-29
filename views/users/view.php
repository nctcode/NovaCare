<?php
if (!isset($viewUser)) {
    echo "Lỗi: Không tìm thấy dữ liệu người dùng.";
    exit;
}

// Tính tuổi nếu là bệnh nhân
$age = 'Chưa rõ';
if ($viewUser['role'] === 'patient' && !empty($roleData['date_of_birth'])) {
    $birthDate = new DateTime($roleData['date_of_birth']);
    $today = new DateTime();
    $age = $today->diff($birthDate)->y . ' tuổi';
}

$genderMap = ['male' => 'Nam', 'female' => 'Nữ', 'other' => 'Khác', 'Nam' => 'Nam', 'Nữ' => 'Nữ', 'Khác' => 'Khác'];

// Hàm lấy gradient avatar dựa trên vai trò
if (!function_exists('getRoleViewGradient')) {
    function getRoleViewGradient($role) {
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

if (!function_exists('getRoleBgStyleLocal')) {
    function getRoleBgStyleLocal($role) {
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

if (!function_exists('getRoleNameLocal')) {
    function getRoleNameLocal($role) {
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
?>

<style>
    .profile-header-card {
        border-radius: 20px;
        background: linear-gradient(135deg, #f8fafc 0%, #ffffff 100%);
        border: 1px solid #e2e8f0;
        box-shadow: 0 4px 20px -2px rgba(0, 0, 0, 0.03);
        position: relative;
        overflow: hidden;
    }
    .profile-avatar-large {
        width: 90px;
        height: 90px;
        font-size: 38px;
        font-weight: 800;
        border-radius: 24px;
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 16px rgba(0,0,0,0.08);
        border: 3px solid #ffffff;
        flex-shrink: 0;
    }
    .profile-tag {
        font-size: 13px;
        font-weight: 600;
        border-radius: 10px;
        padding: 6px 14px;
        background: #ffffff;
        border: 1px solid #e2e8f0;
        color: #475569;
        display: inline-flex;
        align-items: center;
        gap: 6px;
    }
    .profile-info-item {
        transition: all 0.2s ease;
        border: 1px solid #f1f5f9;
        background-color: #f8fafc;
        border-radius: 14px;
        height: 100%;
    }
    .profile-info-item:hover {
        background-color: #ffffff;
        border-color: #e2e8f0;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
        transform: translateY(-1px);
    }
    .profile-icon-box {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 18px;
        flex-shrink: 0;
    }
</style>

<!-- Profile Header Banner -->
<div class="row mb-4" data-aos="fade-down">
    <div class="col-12">
        <div class="profile-header-card p-4">
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-4">
                <div class="d-flex flex-column flex-md-row align-items-center gap-4 w-100">
                    <!-- Avatar box -->
                    <div class="profile-avatar-large" style="background: <?= getRoleViewGradient($viewUser['role']) ?>;">
                        <?= mb_strtoupper(mb_substr($viewUser['name'], 0, 1, 'utf-8')) ?>
                    </div>
                    
                    <!-- Core Identifiers -->
                    <div class="text-center text-md-start flex-grow-1">
                        <div class="d-flex align-items-center justify-content-center justify-content-md-start flex-wrap gap-2.5 mb-2">
                            <h3 class="fw-bold m-0 text-dark" style="font-size: 24px; letter-spacing: -0.5px;"><?= htmlspecialchars($viewUser['name']) ?></h3>
                            <span class="badge px-3 py-1.5 text-uppercase" style="font-size: 11px; font-weight: 700; border-radius: 6px; <?= getRoleBgStyleLocal($viewUser['role']) ?>">
                                <?= getRoleNameLocal($viewUser['role']) ?>
                            </span>
                        </div>
                        
                        <div class="d-flex align-items-center justify-content-center justify-content-md-start flex-wrap gap-2">
                            <span class="profile-tag">
                                <i class="fa-solid fa-hashtag text-primary"></i>
                                ID: <strong>US-<?= str_pad($viewUser['id'], 4, '0', STR_PAD_LEFT) ?></strong>
                            </span>
                            <span class="profile-tag">
                                <i class="fa-solid fa-calendar-check text-muted"></i>
                                Đăng ký ngày: <strong><?= date('d/m/Y H:i', strtotime($viewUser['created_at'])) ?></strong>
                            </span>
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="text-center text-md-end flex-shrink-0 d-flex flex-column gap-2 align-items-md-end">
                        <a href="index.php?page=users&action=edit&id=<?= $viewUser['id'] ?>" class="btn btn-primary btn-sm px-3.5 py-2 fw-semibold d-flex align-items-center gap-1.5" style="border-radius: 8px; font-size: 13px; box-shadow: 0 4px 12px rgba(59, 130, 246, 0.15);">
                            <i class="fa-solid fa-user-pen"></i> Chỉnh sửa thông tin
                        </a>
                        <a href="index.php?page=users&role=<?= $viewUser['role'] ?>" class="btn btn-sm btn-outline-secondary px-3.5" style="border-radius: 8px; font-size: 12.5px; font-weight: 600;">
                            <i class="fa-solid fa-arrow-left me-1"></i> Quay lại danh sách
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4" data-aos="fade-up">
    <!-- CỘT 1: THÔNG TIN TÀI KHOẢN HÀNH CHÍNH -->
    <div class="col-md-6">
        <div class="content-card border-0 shadow-sm p-4 bg-white h-100" style="border-radius: 16px;">
            <h6 class="fw-bold mb-4 text-dark" style="font-size: 15px;"><i class="fa-solid fa-address-card text-primary me-2"></i>Hồ sơ tài khoản & liên lạc</h6>
            
            <div class="row g-3">
                <!-- Phone -->
                <div class="col-12">
                    <div class="p-3 profile-info-item d-flex align-items-center gap-3">
                        <div class="profile-icon-box bg-primary-subtle text-primary" style="background: rgba(59, 130, 246, 0.08);">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block" style="font-size: 11px; font-weight: 500;">SỐ ĐIỆN THOẠI</small>
                            <strong class="text-dark" style="font-size: 14.5px;"><?= htmlspecialchars($viewUser['phone'] ?: 'Chưa cập nhật') ?></strong>
                        </div>
                    </div>
                </div>

                <!-- Email -->
                <div class="col-12">
                    <div class="p-3 profile-info-item d-flex align-items-center gap-3">
                        <div class="profile-icon-box bg-info-subtle text-info" style="background: rgba(14, 165, 233, 0.08);">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <div class="overflow-hidden">
                            <small class="text-muted d-block" style="font-size: 11px; font-weight: 500;">ĐỊA CHỈ EMAIL</small>
                            <strong class="text-dark d-block text-truncate" style="font-size: 14.5px;" title="<?= htmlspecialchars($viewUser['email']) ?>"><?= htmlspecialchars($viewUser['email']) ?></strong>
                        </div>
                    </div>
                </div>

                <!-- Status -->
                <div class="col-12">
                    <div class="p-3 profile-info-item d-flex align-items-center gap-3">
                        <div class="profile-icon-box bg-success-subtle text-success" style="background: rgba(34, 197, 94, 0.08);">
                            <i class="fa-solid fa-circle-check"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block" style="font-size: 11px; font-weight: 500;">TRẠNG THÁI HOẠT ĐỘNG</small>
                            <span class="badge bg-success-subtle text-success border border-success-subtle px-2 py-1" style="font-size: 11px; font-weight: 600;">
                                Đang hoạt động
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- CỘT 2: THÔNG TIN CHI TIẾT THEO VAI TRÒ -->
    <div class="col-md-6">
        <div class="content-card border-0 shadow-sm p-4 bg-white h-100" style="border-radius: 16px;">
            <h6 class="fw-bold mb-4 text-dark" style="font-size: 15px;"><i class="fa-solid fa-stethoscope text-primary me-2"></i>Dữ liệu nghiệp vụ chi tiết</h6>

            <!-- NẾU CHƯA CÓ DỮ LIỆU ĐẶC THÙ -->
            <?php if (empty($roleData)): ?>
                <div class="d-flex flex-column align-items-center justify-content-center py-5 text-center text-muted">
                    <i class="fa-solid fa-user-gear mb-3" style="font-size: 32px; opacity: 0.3;"></i>
                    <h6 class="fw-bold" style="font-size: 14px;">Không có thông tin bổ sung</h6>
                    <p class="small mb-0" style="max-width: 250px;">Tài khoản này thuộc vai trò hành chính cơ bản và không cần lưu thông tin chuyên môn.</p>
                </div>
            <?php else: ?>
                
                <!-- BÁC SĨ -->
                <?php if ($viewUser['role'] === 'doctor'): ?>
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="p-3 profile-info-item d-flex align-items-center gap-3">
                                <div class="profile-icon-box bg-primary-subtle text-primary" style="background: rgba(59, 130, 246, 0.08);">
                                    <i class="fa-solid fa-graduation-cap"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block" style="font-size: 11px; font-weight: 500;">CHUYÊN KHOA</small>
                                    <strong class="text-dark" style="font-size: 14.5px;"><?= htmlspecialchars($roleData['specialty'] ?: 'Chưa xác định') ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 profile-info-item d-flex align-items-center gap-3">
                                <div class="profile-icon-box bg-warning-subtle text-warning" style="background: rgba(245, 158, 11, 0.08);">
                                    <i class="fa-solid fa-briefcase"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block" style="font-size: 11px; font-weight: 500;">KINH NGHIỆM LÂM SÀNG</small>
                                    <strong class="text-dark" style="font-size: 14.5px;"><?= intval($roleData['experience_years']) ?> năm công tác</strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 profile-info-item d-flex align-items-start gap-3">
                                <div class="profile-icon-box bg-danger-subtle text-danger" style="background: rgba(239, 68, 68, 0.08);">
                                    <i class="fa-solid fa-hospital"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block" style="font-size: 11px; font-weight: 500;">KHOA LÀM VIỆC CHÍNH</small>
                                    <strong class="text-dark" style="font-size: 13.5px; line-height: 1.5;"><?= htmlspecialchars($roleData['department_name'] ?: 'Chưa phân khoa') ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>

                <!-- Y TÁ / ĐIỀU DƯỠNG -->
                <?php elseif ($viewUser['role'] === 'nurse'): ?>
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="p-3 profile-info-item d-flex align-items-start gap-3">
                                <div class="profile-icon-box bg-teal-subtle text-teal" style="background: rgba(20, 184, 166, 0.08); color: #0f766e;">
                                    <i class="fa-solid fa-hospital"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block" style="font-size: 11px; font-weight: 500;">KHOA LÀM VIỆC CHÍNH</small>
                                    <strong class="text-dark" style="font-size: 14.5px;"><?= htmlspecialchars($roleData['department_name'] ?: 'Chưa phân khoa') ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>

                <!-- KỸ THUẬT VIÊN -->
                <?php elseif ($viewUser['role'] === 'technician'): ?>
                    <div class="row g-3">
                        <div class="col-12">
                            <div class="p-3 profile-info-item d-flex align-items-center gap-3">
                                <div class="profile-icon-box bg-violet-subtle text-violet" style="background: rgba(139, 92, 246, 0.08); color: #6d28d9;">
                                    <i class="fa-solid fa-flask-vial"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block" style="font-size: 11px; font-weight: 500;">CHUYÊN MÔN KỸ THUẬT</small>
                                    <strong class="text-dark" style="font-size: 14.5px;"><?= htmlspecialchars($roleData['specialty'] ?: 'Chưa xác định') ?></strong>
                                </div>
                            </div>
                        </div>
                        <div class="col-12">
                            <div class="p-3 profile-info-item d-flex align-items-start gap-3">
                                <div class="profile-icon-box bg-violet-subtle text-violet" style="background: rgba(139, 92, 246, 0.08); color: #6d28d9;">
                                    <i class="fa-solid fa-hospital"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block" style="font-size: 11px; font-weight: 500;">KHOA LÀM VIỆC</small>
                                    <strong class="text-dark" style="font-size: 14.5px;"><?= htmlspecialchars($roleData['department_name'] ?: 'Chưa phân khoa') ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>

                <!-- BỆNH NHÂN -->
                <?php elseif ($viewUser['role'] === 'patient'): ?>
                    <div class="row g-3 mb-3">
                        <!-- Ngày sinh -->
                        <div class="col-sm-6">
                            <div class="p-3 profile-info-item d-flex align-items-center gap-3">
                                <div class="profile-icon-box bg-success-subtle text-success" style="background: rgba(34, 197, 94, 0.08);">
                                    <i class="fa-solid fa-calendar-day"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block" style="font-size: 11px; font-weight: 500;">NGÀY SINH & TUỔI</small>
                                    <strong class="text-dark" style="font-size: 13.5px;"><?= $roleData['date_of_birth'] ? date('d/m/Y', strtotime($roleData['date_of_birth'])) : 'Chưa nhập' ?> (<?= $age ?>)</strong>
                                </div>
                            </div>
                        </div>
                        <!-- Giới tính -->
                        <div class="col-sm-6">
                            <div class="p-3 profile-info-item d-flex align-items-center gap-3">
                                <div class="profile-icon-box bg-success-subtle text-success" style="background: rgba(34, 197, 94, 0.08);">
                                    <i class="fa-solid fa-venus-mars"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block" style="font-size: 11px; font-weight: 500;">GIỚI TÍNH</small>
                                    <strong class="text-dark" style="font-size: 13.5px;"><?= $genderMap[$roleData['gender']] ?? 'Chưa xác định' ?></strong>
                                </div>
                            </div>
                        </div>
                        <!-- Nhóm máu -->
                        <div class="col-12">
                            <div class="p-3 profile-info-item d-flex align-items-center gap-3">
                                <div class="profile-icon-box bg-danger-subtle text-danger" style="background: rgba(239, 68, 68, 0.08);">
                                    <i class="fa-solid fa-droplet"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block" style="font-size: 11px; font-weight: 500;">NHÓM MÁU</small>
                                    <strong class="text-danger" style="font-size: 14.5px;"><?= htmlspecialchars($roleData['blood_type'] ?: 'Chưa kiểm nghiệm') ?></strong>
                                </div>
                            </div>
                        </div>
                        <!-- Địa chỉ -->
                        <div class="col-12">
                            <div class="p-3 profile-info-item d-flex align-items-start gap-3">
                                <div class="profile-icon-box bg-secondary-subtle text-secondary" style="background: rgba(100, 116, 139, 0.08);">
                                    <i class="fa-solid fa-location-dot"></i>
                                </div>
                                <div>
                                    <small class="text-muted d-block" style="font-size: 11px; font-weight: 500;">ĐỊA CHỈ THƯỜNG TRÚ</small>
                                    <strong class="text-dark" style="font-size: 13.5px; line-height: 1.5;"><?= htmlspecialchars($roleData['address'] ?: 'Chưa cập nhật địa chỉ') ?></strong>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tiền sử bệnh lý -->
                    <div>
                        <small class="text-muted d-block fw-bold mb-2" style="font-size: 11px; letter-spacing: 0.5px;">TIỀN SỬ BỆNH LÝ LÂM SÀNG (EMR)</small>
                        <div class="p-3 rounded-3" style="font-size: 13.5px; color: #0f766e; line-height: 1.6; border-left: 4px solid #0d9488; background-color: #f0fdfa; font-weight: 500; white-space: pre-line;">
                            <?= !empty($roleData['medical_history']) ? htmlspecialchars($roleData['medical_history']) : 'Không ghi nhận tiền sử bệnh đặc biệt khi đăng ký hồ sơ.' ?>
                        </div>
                    </div>
                <?php endif; ?>

            <?php endif; ?>
        </div>
    </div>
</div>
