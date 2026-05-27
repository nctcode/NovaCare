<!-- Director Header Card -->
<div class="row mb-4">
    <div class="col-12" data-aos="fade-down">
        <div class="content-card p-4 border-0 shadow-sm" style="border-radius: 16px; background: linear-gradient(135deg, #ffffff, #f8fafc);">
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-4">
                <div class="d-flex flex-column flex-md-row align-items-center gap-4">
                    <!-- Avatar -->
                    <div class="d-flex align-items-center justify-content-center text-white" 
                         style="width: 80px; height: 80px; font-size: 32px; border-radius: 50%; background: linear-gradient(135deg, #64748b, #475569); font-weight: 700; text-transform: uppercase; box-shadow: 0 4px 15px rgba(100, 116, 139, 0.2); flex-shrink: 0; border: 3px solid white;">
                        <i class="fa-solid fa-user-tie"></i>
                    </div>
                    <!-- Identity Info -->
                    <div class="text-center text-md-start">
                        <div class="d-flex align-items-center justify-content-center justify-content-md-start flex-wrap gap-2 mb-2">
                            <h3 class="fw-bold m-0" style="color: var(--dark);"><?= htmlspecialchars($director['name']) ?></h3>
                            <span class="badge bg-secondary text-white px-2.5 py-1" style="font-size: 11px; font-weight: 600; border-radius: 6px; background-color: #64748b !important;">
                                GD<?= str_pad($director['id'], 5, '0', STR_PAD_LEFT) ?>
                            </span>
                        </div>
                        <div class="d-flex align-items-center justify-content-center justify-content-md-start flex-wrap gap-2">
                            <span class="badge bg-light text-secondary border px-2.5 py-1.5" style="font-size: 12px; border-radius: 6px;">
                                <i class="fa-solid fa-id-card-clip me-1 text-secondary"></i>
                                Ban Giám Đốc / Ban Quản Trị
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <div class="col-md-6" data-aos="fade-right">
        <div class="content-card p-4 border-0 shadow-sm mb-4" style="border-radius: 16px; min-height: 250px;">
            <h6 class="fw-bold mb-4" style="color: var(--dark); font-size: 14px;"><i class="fa-solid fa-id-card text-secondary me-2"></i>Thông tin liên hệ</h6>
            <div class="row g-3">
                <div class="col-12 col-sm-6">
                    <div class="p-3" style="background: var(--gray-50); border-radius: 12px; border: 1px solid var(--gray-100);">
                        <small class="text-muted d-block mb-1" style="font-size: 10px; font-weight: 500; letter-spacing: 0.3px;">SỐ ĐIỆN THOẠI</small>
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-phone text-secondary" style="font-size: 13px;"></i>
                            <strong class="text-dark" style="font-size: 14px;"><?= htmlspecialchars($director['phone'] ?? 'Chưa cập nhật') ?></strong>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6">
                    <div class="p-3" style="background: var(--gray-50); border-radius: 12px; border: 1px solid var(--gray-100);">
                        <small class="text-muted d-block mb-1" style="font-size: 10px; font-weight: 500; letter-spacing: 0.3px;">EMAIL</small>
                        <div class="d-flex align-items-center gap-2 overflow-hidden">
                            <i class="fa-solid fa-envelope text-secondary" style="font-size: 13px;"></i>
                            <strong class="text-dark d-block text-truncate" style="font-size: 14px;" title="<?= htmlspecialchars($director['email']) ?>"><?= htmlspecialchars($director['email']) ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <a href="index.php?page=directors" class="btn btn-outline-secondary w-100 py-2.5" style="border-radius: 12px; font-weight: 600; transition: all 0.2s;">
                <i class="fa-solid fa-arrow-left me-2"></i>Quay lại danh sách
            </a>
        </div>
    </div>

    <div class="col-md-6" data-aos="fade-left">
        <div class="content-card p-4 border-0 shadow-sm" style="border-radius: 16px; min-height: 250px;">
            <h6 class="fw-bold mb-4" style="color: var(--dark); font-size: 14px;"><i class="fa-solid fa-circle-check text-secondary me-2"></i>Mô tả chức năng</h6>
            <div class="alert alert-secondary border-0 shadow-none d-flex align-items-start gap-3" style="border-radius: 12px; background-color: rgba(100,116,139,0.06); color: #475569;">
                <i class="fa-solid fa-circle-info mt-1" style="font-size: 16px;"></i>
                <div style="font-size: 13px; line-height: 1.5; color: var(--gray-700);">
                    <strong>Ban Giám Đốc:</strong> Quản trị tối cao toàn bộ hệ thống bệnh viện thông minh NovaCare; giám sát các chỉ số vận hành lâm sàng, tài chính, nhân sự; phê duyệt các quyết định lớn liên quan đến phân bổ phòng ban, dịch vụ và định hướng phát triển.
                </div>
            </div>
        </div>
    </div>
</div>
