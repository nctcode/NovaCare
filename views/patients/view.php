<?php
// Calculate patient's age
$age = 'Chưa rõ tuổi';
if (!empty($patient['date_of_birth'])) {
    $birthDate = new DateTime($patient['date_of_birth']);
    $today = new DateTime();
    $age = $today->diff($birthDate)->y . ' tuổi';
}

$genderMap = ['male' => 'Nam', 'female' => 'Nữ', 'other' => 'Khác'];
$isDoctor = ($_SESSION['user']['role'] === 'doctor');
?>

<!-- Premium EMR Custom Styling for Superior UX & Readability -->
<style>
    .emr-header-light {
        border-radius: 20px;
        background: linear-gradient(135deg, #f0f9ff 0%, #ffffff 100%);
        border: 1px solid #e0f2fe;
        box-shadow: 0 4px 20px -2px rgba(14, 165, 233, 0.05);
        position: relative;
        overflow: hidden;
    }
    .emr-header-light::before {
        content: "";
        position: absolute;
        top: -30%;
        right: -10%;
        width: 250px;
        height: 250px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(14, 165, 233, 0.08) 0%, transparent 70%);
        pointer-events: none;
    }
    .emr-avatar-light {
        width: 80px;
        height: 80px;
        font-size: 32px;
        font-weight: 800;
        text-transform: uppercase;
        border-radius: 20px;
        background: linear-gradient(135deg, #38bdf8, #0284c7);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 16px rgba(2, 132, 199, 0.12);
        border: 3px solid #ffffff;
        flex-shrink: 0;
    }
    .emr-tag {
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
        box-shadow: 0 2px 4px rgba(0,0,0,0.02);
    }
    /* Tab controls custom design */
    .emr-nav-tabs {
        border-bottom: 2px solid #e2e8f0;
        gap: 16px;
    }
    .emr-nav-tabs .nav-link {
        font-weight: 600;
        font-size: 14.5px;
        color: #64748b;
        border: none;
        border-bottom: 3px solid transparent;
        padding: 10px 16px;
        border-radius: 0;
        transition: all 0.25s ease;
        background: transparent;
    }
    .emr-nav-tabs .nav-link:hover {
        color: #0ea5e9;
        border-bottom-color: #bae6fd;
    }
    .emr-nav-tabs .nav-link.active {
        color: #0284c7;
        border-bottom-color: #0284c7;
        background: transparent;
    }
    .info-card-item {
        transition: all 0.2s ease;
        border: 1px solid #f1f5f9;
        background-color: #f8fafc;
        border-radius: 14px;
    }
    .info-card-item:hover {
        background-color: #ffffff;
        border-color: #e2e8f0;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
        transform: translateY(-1px);
    }
    .info-icon-wrapper {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }
    /* Timeline style */
    .timeline-container {
        position: relative;
        padding-left: 32px;
    }
    .timeline-container::before {
        content: '';
        position: absolute;
        left: 11px;
        top: 8px;
        bottom: 8px;
        width: 2px;
        background: #e2e8f0;
    }
    .timeline-visit-card {
        position: relative;
        margin-bottom: 24px;
    }
    .timeline-visit-card:last-child {
        margin-bottom: 0;
    }
    .timeline-visit-bullet {
        position: absolute;
        left: -32px;
        top: 14px;
        width: 24px;
        height: 24px;
        border-radius: 50%;
        background: #ffffff;
        border: 5px solid #0ea5e9;
        box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1);
        z-index: 2;
        transition: all 0.2s ease;
    }
    .timeline-visit-card:hover .timeline-visit-bullet {
        background: #0ea5e9;
        transform: scale(1.1);
    }
    .timeline-visit-body {
        border: 1px solid #e2e8f0;
        border-radius: 16px;
        background: #ffffff;
        box-shadow: 0 2px 8px rgba(0,0,0,0.01);
        transition: all 0.25s ease;
    }
    .timeline-visit-body:hover {
        border-color: #cbd5e1;
        box-shadow: 0 8px 20px rgba(0,0,0,0.03);
    }
    .prescription-block {
        border-left: 4px solid #10b981;
        background-color: #f0fdf4;
        border-radius: 8px;
    }
</style>

<!-- EMR Header (Full Width) -->
<div class="row mb-4">
    <div class="col-12" data-aos="fade-down">
        <div class="emr-header-light p-4">
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-4">
                <div class="d-flex flex-column flex-md-row align-items-center gap-4 w-100">
                    <!-- Avatar box -->
                    <div class="emr-avatar-light">
                        <?= mb_substr($patient['name'], 0, 1, 'utf-8') ?>
                    </div>
                    
                    <!-- Identity Core Infos -->
                    <div class="text-center text-md-start flex-grow-1">
                        <div class="d-flex align-items-center justify-content-center justify-content-md-start flex-wrap gap-2 mb-2.5">
                            <h3 class="fw-bold m-0 text-dark" style="font-size: 24px; letter-spacing: -0.5px;"><?= htmlspecialchars($patient['name']) ?></h3>
                            <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1" style="font-size: 11.5px; font-weight: 700; border-radius: 6px; background-color: rgba(14, 165, 233, 0.1) !important;">
                                BN<?= str_pad($patient['id'], 5, '0', STR_PAD_LEFT) ?>
                            </span>
                        </div>
                        
                        <div class="d-flex align-items-center justify-content-center justify-content-md-start flex-wrap gap-2">
                            <span class="emr-tag">
                                <i class="fa-solid fa-venus-mars text-primary"></i>
                                Giới tính: <strong><?= $genderMap[$patient['gender']] ?? 'N/A' ?></strong>
                            </span>
                            <span class="emr-tag">
                                <i class="fa-solid fa-hourglass-half text-warning"></i>
                                Tuổi: <strong><?= $age ?></strong>
                            </span>
                            <span class="emr-tag">
                                <i class="fa-solid fa-droplet text-danger"></i>
                                Nhóm máu: <strong class="text-danger"><?= htmlspecialchars($patient['blood_type'] ?? 'N/A') ?></strong>
                            </span>
                        </div>
                    </div>

                    <!-- Actions and Back -->
                    <div class="text-center text-md-end flex-shrink-0 d-flex flex-column gap-2 align-items-md-end">
                        <?php if ($isDoctor && count($records) > 0): ?>
                        <button type="button" class="btn btn-primary btn-sm px-3.5 py-2 fw-semibold d-flex align-items-center gap-1.5" id="btnTriggerAI" style="border-radius: 8px; font-size: 13px; background: #0d9488; border-color: #0d9488; box-shadow: 0 4px 12px rgba(13, 148, 136, 0.15);">
                            <i class="fa-solid fa-brain"></i> Xem Tóm tắt AI
                        </button>
                        <?php endif; ?>
                        <a href="index.php?page=patients" class="btn btn-sm btn-outline-secondary px-3" style="border-radius: 8px; font-size: 12.5px; font-weight: 600;">
                            <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tab Controls for Superior Organization -->
<div class="row mb-4">
    <div class="col-12" data-aos="fade-up">
        <ul class="nav nav-tabs emr-nav-tabs" id="emrTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="records-tab" data-bs-toggle="tab" data-bs-target="#records-pane" type="button" role="tab"><i class="fa-solid fa-notes-medical me-2"></i>Lịch sử khám bệnh & Điều trị</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="admin-tab" data-bs-toggle="tab" data-bs-target="#admin-pane" type="button" role="tab"><i class="fa-solid fa-id-card me-2"></i>Thông tin Hành chính</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="history-tab" data-bs-toggle="tab" data-bs-target="#history-pane" type="button" role="tab"><i class="fa-solid fa-file-invoice-dollar me-2"></i>Tiền sử bệnh lý</button>
            </li>
        </ul>
    </div>
</div>

<!-- Tab Panes Content -->
<div class="tab-content" id="emrTabsContent" data-aos="fade-up" data-aos-delay="100">
    <!-- Tab 1: Timeline Visits -->
    <div class="tab-pane fade show active" id="records-pane" role="tabpanel" tabindex="0">
        <div class="content-card border-0 shadow-sm p-4 bg-white" style="border-radius: 16px;">
            <?php if (empty($records)): ?>
                <div class="empty-state py-5 text-center text-muted">
                    <div class="empty-icon fs-2 text-secondary opacity-25 mb-2"><i class="fa-solid fa-clipboard-question"></i></div>
                    <h6 class="fw-bold text-secondary">Chưa có lịch sử bệnh án</h6>
                    <p class="small text-muted mb-0">Bệnh nhân này hiện chưa phát sinh đợt khám bệnh nào tại hệ thống phòng khám.</p>
                </div>
            <?php else: ?>
                <div class="timeline-container">
                    <?php foreach ($records as $rec): ?>
                    <div class="timeline-visit-card">
                        <!-- Left Bullet node -->
                        <div class="timeline-visit-bullet"></div>
                        
                        <!-- Visit Body Card -->
                        <div class="timeline-visit-body p-4">
                            <div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center border-bottom pb-2.5 mb-3 gap-2">
                                <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1.5" style="font-size: 11.5px; border-radius: 6px; font-weight: 600; background-color: rgba(14, 165, 233, 0.08) !important;">
                                    <i class="fa-regular fa-calendar me-1.5"></i>
                                    Ngày khám: <?= date('d/m/Y - H:i', strtotime($rec['created_at'])) ?>
                                </span>
                                <span class="text-secondary" style="font-size: 13px; font-weight: 500;">
                                    <i class="fa-solid fa-user-doctor text-primary me-1.5"></i>
                                    Bác sĩ khám: <strong class="text-dark">BS. <?= htmlspecialchars(preg_replace('/^(Bác sĩ|BS\.|Bs\.|Bs|BS)\s+/iu', '', $rec['doctor_name'] ?? 'N/A')) ?></strong>
                                </span>
                            </div>
                            
                            <div style="font-size: 14.5px;" class="mb-3">
                                <span class="text-danger fw-bold"><i class="fa-solid fa-stethoscope me-1.5"></i>Chẩn đoán bệnh lý:</span> 
                                <strong class="text-dark"><?= htmlspecialchars($rec['diagnosis'] ?? 'Không rõ') ?></strong>
                            </div>
                            
                            <?php if (!empty($rec['treatment'])): ?>
                            <div class="prescription-block p-3 mb-3">
                                <strong style="color: #10b981; display: block; margin-bottom: 5px; font-size: 12.5px; font-weight: 700;">
                                    <i class="fa-solid fa-file-prescription me-1.5"></i>Phương án Điều trị & Kê đơn thuốc:
                                </strong>
                                <div style="line-height: 1.5; color: #047857; font-weight: 500; font-size: 13.5px; white-space: pre-line;"><?= htmlspecialchars($rec['treatment']) ?></div>
                            </div>
                            <?php endif; ?>

                            <?php if (!empty($rec['notes'])): ?>
                            <div class="text-muted bg-light p-2.5 rounded-3 border-dashed" style="font-size: 12.5px; border: 1px dashed #e2e8f0;">
                                <i class="fa-regular fa-comment-dots me-1 text-secondary"></i><strong>Ghi chú lâm sàng:</strong> <?= htmlspecialchars($rec['notes']) ?>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tab 2: Administrative Details -->
    <div class="tab-pane fade" id="admin-pane" role="tabpanel" tabindex="0">
        <div class="content-card border-0 shadow-sm p-4 bg-white" style="border-radius: 16px;">
            <h6 class="fw-bold mb-4 text-dark" style="font-size: 15px;"><i class="fa-solid fa-address-card text-primary me-2"></i>Hồ sơ Hành chính Bệnh nhân</h6>
            
            <div class="row g-3">
                <!-- Phone -->
                <div class="col-md-6">
                    <div class="p-3 info-card-item d-flex align-items-center gap-3">
                        <div class="info-icon-wrapper bg-primary-subtle text-primary" style="background: rgba(59, 130, 246, 0.08);">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block" style="font-size: 11px; font-weight: 500;">SỐ ĐIỆN THOẠI</small>
                            <strong class="text-dark" style="font-size: 14px;"><?= htmlspecialchars($patient['phone'] ?? 'Chưa cập nhật') ?></strong>
                        </div>
                    </div>
                </div>

                <!-- Email -->
                <div class="col-md-6">
                    <div class="p-3 info-card-item d-flex align-items-center gap-3">
                        <div class="info-icon-wrapper bg-info-subtle text-info" style="background: rgba(14, 165, 233, 0.08);">
                            <i class="fa-solid fa-envelope"></i>
                        </div>
                        <div class="overflow-hidden">
                            <small class="text-muted d-block" style="font-size: 11px; font-weight: 500;">ĐỊA CHỈ EMAIL</small>
                            <strong class="text-dark d-block text-truncate" style="font-size: 14px;" title="<?= htmlspecialchars($patient['email']) ?>"><?= htmlspecialchars($patient['email']) ?></strong>
                        </div>
                    </div>
                </div>

                <!-- DOB -->
                <div class="col-md-6">
                    <div class="p-3 info-card-item d-flex align-items-center gap-3">
                        <div class="info-icon-wrapper bg-success-subtle text-success" style="background: rgba(34, 197, 94, 0.08);">
                            <i class="fa-solid fa-calendar-day"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block" style="font-size: 11px; font-weight: 500;">NGÀY SINH</small>
                            <strong class="text-dark" style="font-size: 14px;">
                                <?= $patient['date_of_birth'] ? date('d/m/Y', strtotime($patient['date_of_birth'])) : 'Chưa cập nhật' ?>
                            </strong>
                        </div>
                    </div>
                </div>

                <!-- Registration Timestamp -->
                <div class="col-md-6">
                    <div class="p-3 info-card-item d-flex align-items-center gap-3">
                        <div class="info-icon-wrapper bg-warning-subtle text-warning" style="background: rgba(245, 158, 11, 0.08);">
                            <i class="fa-solid fa-clock"></i>
                        </div>
                        <div>
                            <small class="text-muted d-block" style="font-size: 11px; font-weight: 500;">NGÀY ĐĂNG KÝ HỆ THỐNG</small>
                            <strong class="text-dark" style="font-size: 14px;">
                                <?= !empty($patient['registered_at']) ? date('d/m/Y - H:i', strtotime($patient['registered_at'])) : 'N/A' ?>
                            </strong>
                        </div>
                    </div>
                </div>

                <!-- Address -->
                <div class="col-12">
                    <div class="p-3 info-card-item d-flex align-items-start gap-3">
                        <div class="info-icon-wrapper bg-secondary-subtle text-secondary" style="background: rgba(100, 116, 139, 0.08);">
                            <i class="fa-solid fa-location-dot"></i>
                        </div>
                        <div class="flex-grow-1">
                            <small class="text-muted d-block" style="font-size: 11px; font-weight: 500;">ĐỊA CHỈ THƯỜNG TRÚ</small>
                            <strong class="text-dark d-block" style="font-size: 13.5px; line-height: 1.5; word-break: break-word;"><?= htmlspecialchars($patient['address'] ?? 'N/A') ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab 3: Medical History -->
    <div class="tab-pane fade" id="history-pane" role="tabpanel" tabindex="0">
        <div class="content-card border-0 shadow-sm p-4 bg-white" style="border-radius: 16px;">
            <h6 class="fw-bold mb-3 text-dark" style="font-size: 15px;"><i class="fa-solid fa-notes-medical text-teal me-2" style="color: #0d9488;"></i>Tiền sử bệnh lý EMR</h6>
            <div class="p-4 rounded-4" style="font-size: 14.5px; color: #0f766e; line-height: 1.7; border-left: 5px solid #0d9488; background-color: #f0fdfa; font-weight: 500;">
                <?= !empty($patient['medical_history']) ? nl2br(htmlspecialchars($patient['medical_history'])) : 'Không ghi nhận tiền sử bệnh đặc biệt nào khi đăng ký.' ?>
            </div>
        </div>
    </div>
</div>

<!-- AI Clinical Summary Drawer & Overlay (Doctor-only) -->
<?php if ($isDoctor && count($records) > 0): ?>
<div class="ai-drawer-overlay" id="aiDrawerOverlay"></div>
<div class="ai-drawer" id="aiDrawer">
    <div class="ai-drawer-header">
        <div class="d-flex align-items-center">
            <div id="aiIconBox" style="width:40px;height:40px;border-radius:10px;background:var(--gray-200);color:var(--gray-500);display:flex;align-items:center;justify-content:center;font-size:18px;transition:all 0.5s;">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
            </div>
            <div class="ms-3">
                <h6 class="m-0 fw-bold" id="aiStatusTitle" style="color: var(--dark);">Hỗ trợ lâm sàng AI</h6>
                <span class="text-muted d-block" style="font-size: 11px; margin-top: 2px !important;">Tổng hợp từ <?= count($records) ?> đợt điều trị</span>
            </div>
        </div>
        <button type="button" class="btn-close" id="btnOverlayClose" aria-label="Close" style="font-size: 14px; outline: none; box-shadow: none;"></button>
    </div>
    
    <div class="ai-drawer-body">
        <!-- Spinner Loader -->
        <div id="aiSkeletonLoader" style="display:block;">
            <div class="d-flex align-items-center mb-3">
                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                <span class="text-primary font-weight-bold" style="font-size: 13px;">AI đang tổng hợp hồ sơ...</span>
            </div>
            <div class="skeleton-line" style="width: 100%; height: 14px; background: var(--gray-200); border-radius: 4px; margin-bottom: 10px; animation: pulse 1.5s infinite;"></div>
            <div class="skeleton-line" style="width: 90%; height: 14px; background: var(--gray-200); border-radius: 4px; margin-bottom: 10px; animation: pulse 1.5s infinite 0.1s;"></div>
            <div class="skeleton-line" style="width: 95%; height: 14px; background: var(--gray-200); border-radius: 4px; margin-bottom: 10px; animation: pulse 1.5s infinite 0.2s;"></div>
            <div class="skeleton-line" style="width: 60%; height: 14px; background: var(--gray-200); border-radius: 4px; margin-bottom: 10px; animation: pulse 1.5s infinite 0.3s;"></div>
        </div>

        <!-- EMR AI Summary Output -->
        <div id="aiSummaryRendered" class="markdown-body" style="display:none; font-size: 14px;"></div>
    </div>
    
    <div class="ai-drawer-footer">
        <p class="m-0 text-muted" style="font-size: 11.5px; line-height: 1.4;">
            <i class="fa-solid fa-circle-info me-1 text-info"></i>
            <strong>Khuyến cáo:</strong> Kết quả phân tích lâm sàng tự động được sinh bởi trí tuệ nhân tạo dùng để tham khảo chuyên môn lâm sàng.
        </p>
    </div>
</div>

<!-- Load Marked.js for rendering AI summary markdown inside EMR -->
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const patientId = <?= intval($patient['id']) ?>;
    const btnTrigger = document.getElementById('btnTriggerAI');
    const drawer = document.getElementById('aiDrawer');
    const overlay = document.getElementById('aiDrawerOverlay');
    const btnClose = document.getElementById('btnOverlayClose');
    
    let isLoaded = false;

    if (btnTrigger) {
        btnTrigger.addEventListener('click', function() {
            drawer.classList.add('active');
            overlay.classList.add('active');
            
            if (!isLoaded) {
                const title = document.getElementById('aiStatusTitle');
                title.textContent = 'Đang phân tích lâm sàng...';
                title.style.color = 'var(--primary)';
                
                fetch('index.php?page=records&action=summarize&patient_id=' + patientId + '&ajax=1')
                    .then(response => response.json())
                    .then(data => {
                        document.getElementById('aiSkeletonLoader').style.display = 'none';
                        
                        if (data.success) {
                            const iconBox = document.getElementById('aiIconBox');
                            iconBox.style.background = 'linear-gradient(135deg, #0d9488, #0f766e)';
                            iconBox.style.color = 'white';
                            iconBox.style.boxShadow = '0 4px 15px rgba(13, 148, 136, 0.3)';
                            
                            title.textContent = 'Phân tích lâm sàng AI' + (data.fallback ? ' (Dự phòng)' : '');
                            title.style.color = '#0d9488';
                            
                            const renderedEl = document.getElementById('aiSummaryRendered');
                            renderedEl.innerHTML = marked.parse(data.summary);
                            renderedEl.style.display = 'block';
                            isLoaded = true;
                        } else {
                            showError(data.error);
                        }
                    })
                    .catch(err => {
                        console.error('Fetch error:', err);
                        document.getElementById('aiSkeletonLoader').style.display = 'none';
                        showError('Máy chủ AI không phản hồi kịp hoặc lỗi kết nối.');
                    });
            }
        });
    }

    function closeDrawer() {
        drawer.classList.remove('active');
        overlay.classList.remove('active');
    }

    if (btnClose) btnClose.addEventListener('click', closeDrawer);
    if (overlay) overlay.addEventListener('click', closeDrawer);
        
    function showError(msg) {
        document.getElementById('aiStatusTitle').textContent = 'Lỗi tóm tắt AI';
        document.getElementById('aiStatusTitle').style.color = '#ef4444';
        const renderedEl = document.getElementById('aiSummaryRendered');
        renderedEl.innerHTML = `<div class="alert alert-warning m-0" style="border-radius: 8px;"><i class="fa-solid fa-triangle-exclamation me-2"></i>${msg}</div>`;
        renderedEl.style.display = 'block';
    }
});
</script>
<?php endif; ?>
