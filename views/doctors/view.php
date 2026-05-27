<?php
// Split department names into an array
$depts = !empty($doctor['department_name']) ? explode(', ', $doctor['department_name']) : [];
?>

<!-- Doctor Header Card (Full Width) -->
<div class="row mb-4">
    <div class="col-12" data-aos="fade-down">
        <div class="content-card p-4 border-0 shadow-sm" style="border-radius: 16px; background: linear-gradient(135deg, #ffffff, #f8fafc);">
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-4">
                <div class="d-flex flex-column flex-md-row align-items-center gap-4">
                    <!-- Avatar -->
                    <div class="d-flex align-items-center justify-content-center text-white" 
                         style="width: 80px; height: 80px; font-size: 32px; border-radius: 50%; background: linear-gradient(135deg, #0284c7, #0ea5e9); font-weight: 700; text-transform: uppercase; box-shadow: 0 4px 15px rgba(2, 132, 199, 0.2); flex-shrink: 0; border: 3px solid white;">
                        <i class="fa-solid fa-user-doctor"></i>
                    </div>
                    <!-- Identity Info -->
                    <div class="text-center text-md-start">
                        <div class="d-flex align-items-center justify-content-center justify-content-md-start flex-wrap gap-2 mb-2">
                            <h3 class="fw-bold m-0" style="color: var(--dark);"><?= htmlspecialchars($doctor['name']) ?></h3>
                            <span class="badge bg-primary-light text-primary px-2.5 py-1" style="font-size: 11px; font-weight: 600; border-radius: 6px;">
                                BS<?= str_pad($doctor['id'], 5, '0', STR_PAD_LEFT) ?>
                            </span>
                        </div>
                        <div class="d-flex align-items-center justify-content-center justify-content-md-start flex-wrap gap-2">
                            <span class="badge bg-light text-secondary border px-2.5 py-1.5" style="font-size: 12px; border-radius: 6px;">
                                <i class="fa-solid fa-graduation-cap me-1 text-primary"></i>
                                <?= htmlspecialchars($doctor['specialty'] ?? 'Chưa cập nhật chuyên khoa') ?>
                            </span>
                            <span class="badge bg-light text-secondary border px-2.5 py-1.5" style="font-size: 12px; border-radius: 6px;">
                                <i class="fa-solid fa-star-of-life me-1 text-warning"></i>
                                <?= ($doctor['experience_years'] ?? 0) ?> năm kinh nghiệm
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Details Section -->
<div class="row g-4">
    <!-- Cột trái: Thông tin hành chính & liên hệ -->
    <div class="col-md-6" data-aos="fade-right">
        <div class="content-card p-4 border-0 shadow-sm mb-4" style="border-radius: 16px; min-height: 300px;">
            <h6 class="fw-bold mb-4" style="color: var(--dark); font-size: 14px;"><i class="fa-solid fa-id-card text-primary me-2"></i>Thông tin liên hệ & Công việc</h6>
            <div class="row g-3">
                <div class="col-12 col-sm-6">
                    <div class="p-3" style="background: var(--gray-50); border-radius: 12px; border: 1px solid var(--gray-100);">
                        <small class="text-muted d-block mb-1" style="font-size: 10px; font-weight: 500; letter-spacing: 0.3px;">SỐ ĐIỆN THOẠI</small>
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-phone text-primary" style="font-size: 13px;"></i>
                            <strong class="text-dark" style="font-size: 14px;"><?= htmlspecialchars($doctor['phone'] ?? 'Chưa cập nhật') ?></strong>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <div class="p-3" style="background: var(--gray-50); border-radius: 12px; border: 1px solid var(--gray-100);">
                        <small class="text-muted d-block mb-1" style="font-size: 10px; font-weight: 500; letter-spacing: 0.3px;">EMAIL</small>
                        <div class="d-flex align-items-center gap-2 overflow-hidden">
                            <i class="fa-solid fa-envelope text-primary" style="font-size: 13px;"></i>
                            <strong class="text-dark d-block text-truncate" style="font-size: 14px;" title="<?= htmlspecialchars($doctor['email']) ?>"><?= htmlspecialchars($doctor['email']) ?></strong>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <div class="p-3" style="background: var(--gray-50); border-radius: 12px; border: 1px solid var(--gray-100);">
                        <small class="text-muted d-block mb-1" style="font-size: 10px; font-weight: 500; letter-spacing: 0.3px;">CHUYÊN KHOA</small>
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-stethoscope text-primary" style="font-size: 13px;"></i>
                            <strong class="text-dark" style="font-size: 14px;"><?= htmlspecialchars($doctor['specialty'] ?? 'Chưa cập nhật') ?></strong>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <div class="p-3" style="background: var(--gray-50); border-radius: 12px; border: 1px solid var(--gray-100);">
                        <small class="text-muted d-block mb-1" style="font-size: 10px; font-weight: 500; letter-spacing: 0.3px;">KINH NGHIỆM</small>
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-business-time text-primary" style="font-size: 13px;"></i>
                            <strong class="text-dark" style="font-size: 14px;"><?= ($doctor['experience_years'] ?? 0) ?> năm công tác</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quay lại danh sách -->
        <div class="mt-4">
            <a href="index.php?page=doctors" class="btn btn-outline-secondary w-100 py-2.5" style="border-radius: 12px; font-weight: 600; transition: all 0.2s;">
                <i class="fa-solid fa-arrow-left me-2"></i>Quay lại danh sách
            </a>
        </div>
    </div>

    <!-- Cột phải: Khoa công tác -->
    <div class="col-md-6" data-aos="fade-left">
        <div class="content-card p-4 border-0 shadow-sm" style="border-radius: 16px; min-height: 300px;">
            <h6 class="fw-bold mb-4" style="color: var(--dark); font-size: 14px;"><i class="fa-solid fa-building-columns text-primary me-2"></i>Khoa công tác hoạt động</h6>
            
            <?php if (empty($depts)): ?>
                <div class="text-center py-5 text-muted">
                    <i class="fa-solid fa-hospital-user mb-3" style="font-size: 40px; color: var(--gray-300);"></i>
                    <h6>Chưa phân khoa công tác</h6>
                    <p class="m-0" style="font-size: 13px;">Bác sĩ này chưa được phân bổ về bất kỳ khoa lâm sàng nào.</p>
                </div>
            <?php else: ?>
                <div class="d-flex flex-wrap gap-2 mb-4">
                    <?php foreach ($depts as $deptName): ?>
                        <div class="d-flex align-items-center p-3" style="background: var(--gray-50); border: 1px solid var(--gray-200); border-radius: 12px; min-width: 200px; flex: 1 1 calc(50% - 10px);">
                            <div class="d-flex align-items-center justify-content-center bg-primary-light text-primary rounded-3 me-3" style="width: 40px; height: 40px; font-size: 16px;">
                                <i class="fa-solid fa-hospital-user"></i>
                            </div>
                            <div>
                                <small class="text-muted d-block mb-0.5" style="font-size: 10px; font-weight: 500;">KHOA/PHÒNG BAN</small>
                                <strong class="text-dark" style="font-size: 14px;"><?= htmlspecialchars($deptName) ?></strong>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                
                <div class="alert alert-info border-0 shadow-none d-flex align-items-start gap-3" style="border-radius: 12px; background-color: var(--primary-light); color: var(--primary);">
                    <i class="fa-solid fa-circle-info mt-1" style="font-size: 16px;"></i>
                    <div style="font-size: 13px; line-height: 1.5;">
                        <strong>Quy tắc hoạt động liên khoa:</strong> Bác sĩ trực thuộc các khoa nêu trên được phép thăm khám, kê đơn thuốc, nhập viện nội trú và xử lý hồ sơ lâm sàng của bệnh nhân đăng ký tại các chuyên khoa này.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
