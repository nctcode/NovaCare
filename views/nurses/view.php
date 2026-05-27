<!-- Nurse Header Card (Full Width) -->
<div class="row mb-4">
    <div class="col-12" data-aos="fade-down">
        <div class="content-card p-4 border-0 shadow-sm" style="border-radius: 16px; background: linear-gradient(135deg, #ffffff, #f8fafc);">
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-4">
                <div class="d-flex flex-column flex-md-row align-items-center gap-4">
                    <!-- Avatar -->
                    <div class="d-flex align-items-center justify-content-center text-white" 
                         style="width: 80px; height: 80px; font-size: 32px; border-radius: 50%; background: linear-gradient(135deg, #10b981, #34d399); font-weight: 700; text-transform: uppercase; box-shadow: 0 4px 15px rgba(16, 185, 129, 0.2); flex-shrink: 0; border: 3px solid white;">
                        <i class="fa-solid fa-user-nurse"></i>
                    </div>
                    <!-- Identity Info -->
                    <div class="text-center text-md-start">
                        <div class="d-flex align-items-center justify-content-center justify-content-md-start flex-wrap gap-2 mb-2">
                            <h3 class="fw-bold m-0" style="color: var(--dark);"><?= htmlspecialchars($nurse['name']) ?></h3>
                            <span class="badge bg-success text-white px-2.5 py-1" style="font-size: 11px; font-weight: 600; border-radius: 6px; background-color: #10b981 !important;">
                                YT<?= str_pad($nurse['id'], 5, '0', STR_PAD_LEFT) ?>
                            </span>
                        </div>
                        <div class="d-flex align-items-center justify-content-center justify-content-md-start flex-wrap gap-2">
                            <span class="badge bg-light text-secondary border px-2.5 py-1.5" style="font-size: 12px; border-radius: 6px;">
                                <i class="fa-solid fa-id-card-clip me-1 text-success"></i>
                                Nhân viên Điều dưỡng / Y tá
                            </span>
                            <?php if ($nurse['department_name']): ?>
                                <span class="badge bg-light text-secondary border px-2.5 py-1.5" style="font-size: 12px; border-radius: 6px;">
                                    <i class="fa-solid fa-building-columns me-1 text-primary"></i>
                                    <?= htmlspecialchars($nurse['department_name']) ?>
                                </span>
                            <?php endif; ?>
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
        <div class="content-card p-4 border-0 shadow-sm mb-4" style="border-radius: 16px; min-height: 280px;">
            <h6 class="fw-bold mb-4" style="color: var(--dark); font-size: 14px;"><i class="fa-solid fa-id-card text-success me-2"></i>Thông tin liên hệ & Công việc</h6>
            <div class="row g-3">
                <div class="col-12 col-sm-6">
                    <div class="p-3" style="background: var(--gray-50); border-radius: 12px; border: 1px solid var(--gray-100);">
                        <small class="text-muted d-block mb-1" style="font-size: 10px; font-weight: 500; letter-spacing: 0.3px;">SỐ ĐIỆN THOẠI</small>
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-phone text-success" style="font-size: 13px;"></i>
                            <strong class="text-dark" style="font-size: 14px;"><?= htmlspecialchars($nurse['phone'] ?? 'Chưa cập nhật') ?></strong>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <div class="p-3" style="background: var(--gray-50); border-radius: 12px; border: 1px solid var(--gray-100);">
                        <small class="text-muted d-block mb-1" style="font-size: 10px; font-weight: 500; letter-spacing: 0.3px;">EMAIL</small>
                        <div class="d-flex align-items-center gap-2 overflow-hidden">
                            <i class="fa-solid fa-envelope text-success" style="font-size: 13px;"></i>
                            <strong class="text-dark d-block text-truncate" style="font-size: 14px;" title="<?= htmlspecialchars($nurse['email']) ?>"><?= htmlspecialchars($nurse['email']) ?></strong>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="p-3" style="background: var(--gray-50); border-radius: 12px; border: 1px solid var(--gray-100);">
                        <small class="text-muted d-block mb-1" style="font-size: 10px; font-weight: 500; letter-spacing: 0.3px;">KHOA LÂM SÀNG TRỰC THUỘC</small>
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-hospital text-success" style="font-size: 13px;"></i>
                            <strong class="text-dark" style="font-size: 14px;"><?= htmlspecialchars($nurse['department_name'] ?? 'Chưa phân khoa') ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Quay lại danh sách -->
        <div class="mt-4">
            <a href="index.php?page=nurses" class="btn btn-outline-secondary w-100 py-2.5" style="border-radius: 12px; font-weight: 600; transition: all 0.2s;">
                <i class="fa-solid fa-arrow-left me-2"></i>Quay lại danh sách
            </a>
        </div>
    </div>

    <!-- Cột phải: Nhiệm vụ điều dưỡng & Hoạt động -->
    <div class="col-md-6" data-aos="fade-left">
        <div class="content-card p-4 border-0 shadow-sm" style="border-radius: 16px; min-height: 280px;">
            <h6 class="fw-bold mb-4" style="color: var(--dark); font-size: 14px;"><i class="fa-solid fa-circle-check text-success me-2"></i>Hoạt động & Trực khoa</h6>
            
            <?php if (!$nurse['department_id']): ?>
                <div class="text-center py-5 text-muted">
                    <i class="fa-solid fa-hospital-user mb-3" style="font-size: 40px; color: var(--gray-300);"></i>
                    <h6>Chưa phân khoa hoạt động</h6>
                    <p class="m-0" style="font-size: 13px;">Y tá này chưa được phân bổ về khoa cụ thể.</p>
                </div>
            <?php else: ?>
                <div class="d-flex align-items-center p-3 mb-4" style="background: var(--gray-50); border: 1px solid var(--gray-200); border-radius: 12px;">
                    <div class="d-flex align-items-center justify-content-center bg-success-light text-success rounded-3 me-3" style="width: 40px; height: 40px; font-size: 16px;">
                        <i class="fa-solid fa-briefcase-medical"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block mb-0.5" style="font-size: 10px; font-weight: 500;">TRẠNG THÁI HOẠT ĐỘNG</small>
                        <strong class="text-success" style="font-size: 14px;">Đang hoạt động tại khoa <?= htmlspecialchars($nurse['department_name']) ?></strong>
                    </div>
                </div>
                
                <div class="alert alert-success border-0 shadow-none d-flex align-items-start gap-3" style="border-radius: 12px; background-color: rgba(16,185,129,0.06); color: #10b981;">
                    <i class="fa-solid fa-circle-info mt-1" style="font-size: 16px;"></i>
                    <div style="font-size: 13px; line-height: 1.5; color: var(--gray-700);">
                        <strong>Vai trò Điều dưỡng:</strong> Chịu trách nhiệm tiếp đón bệnh nhân, hỗ trợ bác sĩ lâm sàng thực hiện các y lệnh điều trị, theo dõi sinh hiệu, chăm sóc người bệnh nội trú và cấp phát thuốc theo đúng quy định chuyên môn tại khoa.
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>
