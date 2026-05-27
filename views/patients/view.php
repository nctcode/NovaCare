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
$leftColClass = $isDoctor ? 'col-lg-4' : 'col-md-6';
$rightColClass = $isDoctor ? 'col-lg-8' : 'col-md-6';
?>

<!-- Patient Header Card (Full Width) -->
<div class="row mb-4">
    <div class="col-12" data-aos="fade-down">
        <div class="content-card p-4 border-0 shadow-sm" style="border-radius: 16px; background: linear-gradient(135deg, #ffffff, #f8fafc);">
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-4">
                <div class="d-flex flex-column flex-md-row align-items-center gap-4">
                    <!-- Avatar -->
                    <div class="d-flex align-items-center justify-content-center text-white" 
                         style="width: 80px; height: 80px; font-size: 32px; border-radius: 50%; background: linear-gradient(135deg, #0284c7, #0ea5e9); font-weight: 700; text-transform: uppercase; box-shadow: 0 4px 15px rgba(2, 132, 199, 0.2); flex-shrink: 0; border: 3px solid white;">
                        <?= mb_substr($patient['name'], 0, 1, 'utf-8') ?>
                    </div>
                    <!-- Identity Info -->
                    <div class="text-center text-md-start">
                        <div class="d-flex align-items-center justify-content-center justify-content-md-start flex-wrap gap-2 mb-2">
                            <h3 class="fw-bold m-0" style="color: var(--dark);"><?= htmlspecialchars($patient['name']) ?></h3>
                            <span class="badge bg-primary-light text-primary px-2.5 py-1" style="font-size: 11px; font-weight: 600; border-radius: 6px;">
                                BN<?= str_pad($patient['id'], 5, '0', STR_PAD_LEFT) ?>
                            </span>
                        </div>
                        <div class="d-flex align-items-center justify-content-center justify-content-md-start flex-wrap gap-2">
                            <span class="badge bg-light text-secondary border px-2.5 py-1.5" style="font-size: 12px; border-radius: 6px;">
                                <i class="fa-solid fa-venus-mars me-1 text-primary"></i>
                                <?= $genderMap[$patient['gender']] ?? 'N/A' ?>
                            </span>
                            <span class="badge bg-light text-secondary border px-2.5 py-1.5" style="font-size: 12px; border-radius: 6px;">
                                <i class="fa-solid fa-hourglass-half me-1 text-warning"></i>
                                <?= $age ?>
                            </span>
                            <span class="badge bg-light text-secondary border px-2.5 py-1.5" style="font-size: 12px; border-radius: 6px;">
                                <i class="fa-solid fa-droplet me-1 text-danger"></i>
                                <?= htmlspecialchars($patient['blood_type'] ?? 'N/A') ?>
                            </span>
                        </div>
                    </div>
                </div>
                <!-- Registration Date -->
                <?php if (!empty($patient['registered_at'])): ?>
                <div class="text-center text-md-end">
                    <span class="text-muted d-block mb-1" style="font-size: 10px; font-weight: 600; letter-spacing: 0.5px;">NGÀY ĐĂNG KÝ HỆ THỐNG</span>
                    <strong class="text-dark" style="font-size: 14px;">
                        <?= date('d/m/Y - H:i', strtotime($patient['registered_at'])) ?>
                    </strong>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Bottom Details Section -->
<div class="row g-4">
    <!-- Cột trái: Thông tin liên hệ & hành chính -->
    <div class="<?= $leftColClass ?>" data-aos="fade-right">
        <div class="content-card p-4 border-0 shadow-sm mb-4" style="border-radius: 16px; min-height: 350px;">
            <h6 class="fw-bold mb-4" style="color: var(--dark); font-size: 14px;"><i class="fa-solid fa-id-card text-primary me-2"></i>Thông tin hành chính</h6>
            <div class="row g-3">
                <div class="col-12 col-sm-6 <?= $isDoctor ? 'col-sm-12' : '' ?>">
                    <div class="p-3" style="background: var(--gray-50); border-radius: 12px; border: 1px solid var(--gray-100);">
                        <small class="text-muted d-block mb-1" style="font-size: 10px; font-weight: 500; letter-spacing: 0.3px;">SỐ ĐIỆN THOẠI</small>
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-phone text-primary" style="font-size: 13px;"></i>
                            <strong class="text-dark" style="font-size: 14px;"><?= htmlspecialchars($patient['phone'] ?? 'Chưa cập nhật') ?></strong>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 <?= $isDoctor ? 'col-sm-12' : '' ?>">
                    <div class="p-3" style="background: var(--gray-50); border-radius: 12px; border: 1px solid var(--gray-100);">
                        <small class="text-muted d-block mb-1" style="font-size: 10px; font-weight: 500; letter-spacing: 0.3px;">EMAIL</small>
                        <div class="d-flex align-items-center gap-2 overflow-hidden">
                            <i class="fa-solid fa-envelope text-primary" style="font-size: 13px;"></i>
                            <strong class="text-dark d-block text-truncate" style="font-size: 14px;" title="<?= htmlspecialchars($patient['email']) ?>"><?= htmlspecialchars($patient['email']) ?></strong>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 <?= $isDoctor ? 'col-sm-12' : '' ?>">
                    <div class="p-3" style="background: var(--gray-50); border-radius: 12px; border: 1px solid var(--gray-100);">
                        <small class="text-muted d-block mb-1" style="font-size: 10px; font-weight: 500; letter-spacing: 0.3px;">NGÀY SINH</small>
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-calendar-days text-primary" style="font-size: 13px;"></i>
                            <strong class="text-dark" style="font-size: 14px;">
                                <?= $patient['date_of_birth'] ? date('d/m/Y', strtotime($patient['date_of_birth'])) : 'N/A' ?>
                            </strong>
                        </div>
                    </div>
                </div>
                <div class="col-12 col-sm-6 <?= $isDoctor ? 'col-sm-12' : '' ?>">
                    <div class="p-3" style="background: var(--gray-50); border-radius: 12px; border: 1px solid var(--gray-100);">
                        <small class="text-muted d-block mb-1" style="font-size: 10px; font-weight: 500; letter-spacing: 0.3px;">NHÓM MÁU</small>
                        <div class="d-flex align-items-center gap-2">
                            <i class="fa-solid fa-droplet text-danger" style="font-size: 13px;"></i>
                            <strong class="text-dark" style="font-size: 14px;"><?= htmlspecialchars($patient['blood_type'] ?? 'N/A') ?></strong>
                        </div>
                    </div>
                </div>
                <div class="col-12">
                    <div class="p-3" style="background: var(--gray-50); border-radius: 12px; border: 1px solid var(--gray-100);">
                        <small class="text-muted d-block mb-1" style="font-size: 10px; font-weight: 500; letter-spacing: 0.3px;">ĐỊA CHỈ</small>
                        <div class="d-flex align-items-start gap-2">
                            <i class="fa-solid fa-location-dot text-primary mt-1" style="font-size: 13px;"></i>
                            <strong class="text-dark" style="font-size: 14px; word-break: break-word;"><?= htmlspecialchars($patient['address'] ?? 'N/A') ?></strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if ($isDoctor): ?>
            <!-- Tiền sử bệnh lý (Nằm ở cột trái đối với bác sĩ) -->
            <div class="content-card p-4 border-0 shadow-sm" style="border-radius: 16px;">
                <h6 class="fw-bold mb-3" style="color: var(--dark); font-size: 14px;"><i class="fa-solid fa-notes-medical text-primary me-2"></i>Tiền sử bệnh lý</h6>
                <div class="p-3 rounded-3" style="font-size: 14px; color: var(--gray-700); line-height: 1.6; border-left: 4px solid var(--primary); background-color: var(--gray-50) !important;">
                    <?= !empty($patient['medical_history']) ? nl2br(htmlspecialchars($patient['medical_history'])) : 'Không ghi nhận tiền sử bệnh đặc biệt nào khi đăng ký.' ?>
                </div>
            </div>
        <?php endif; ?>

        <!-- Quay lại danh sách -->
        <div class="mt-4">
            <a href="index.php?page=patients" class="btn btn-outline-secondary w-100 py-2.5" style="border-radius: 12px; font-weight: 600; transition: all 0.2s;">
                <i class="fa-solid fa-arrow-left me-2"></i>Quay lại danh sách
            </a>
        </div>
    </div>

    <!-- Cột phải: Bệnh án chi tiết (Doctor) hoặc Tiền sử bệnh lý (Admin) -->
    <div class="<?= $rightColClass ?>" data-aos="fade-left">
        <?php if (!$isDoctor): ?>
            <!-- Tiền sử bệnh lý (Nằm ở cột phải đối với Admin/Lễ tân để cân đối bố cục) -->
            <div class="content-card p-4 border-0 shadow-sm" style="border-radius: 16px; min-height: 350px;">
                <h6 class="fw-bold mb-3" style="color: var(--dark); font-size: 15px;"><i class="fa-solid fa-notes-medical text-primary me-2"></i>Tiền sử bệnh lý của bệnh nhân</h6>
                <div class="p-3 rounded-3" style="font-size: 14px; color: var(--gray-700); line-height: 1.7; border-left: 4px solid var(--primary); background-color: var(--gray-50) !important;">
                    <?= !empty($patient['medical_history']) ? nl2br(htmlspecialchars($patient['medical_history'])) : 'Không ghi nhận tiền sử bệnh đặc biệt nào khi đăng ký.' ?>
                </div>
            </div>
        <?php else: ?>
            <!-- Bác sĩ: AI + Timeline -->
            
            <!-- 1. Sleek AI Trigger Banner (Space-saving, height limit 150px) -->
            <?php if (count($records) > 0): ?>
            <div class="d-flex align-items-center justify-content-between p-3 mb-4 rounded-4 shadow-sm" 
                 style="background: linear-gradient(135deg, rgba(14, 165, 233, 0.08), rgba(99, 102, 241, 0.08)); border: 1px solid rgba(14, 165, 233, 0.15); max-height: 150px;">
                <div class="d-flex align-items-center">
                    <div class="d-flex align-items-center justify-content-center bg-white text-primary rounded-3 shadow-sm" style="width: 42px; height: 42px; font-size: 18px;">
                        <i class="fa-solid fa-wand-magic-sparkles text-primary"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="m-0 fw-bold" style="color: var(--dark); font-size: 14px;">Trợ lý Y khoa AI & Phân tích Lâm sàng</h6>
                        <p class="m-0 text-muted d-none d-sm-block" style="font-size: 12px; margin-top: 2px !important;">Tóm tắt nhanh bệnh án, phân tích xu hướng và cảnh báo tái khám.</p>
                    </div>
                </div>
                <button type="button" class="btn btn-primary btn-sm px-3 py-2 fw-semibold" id="btnTriggerAI" style="border-radius: 8px; font-size: 13px; background: linear-gradient(135deg, var(--primary), var(--primary-dark)); border: none; box-shadow: 0 4px 12px rgba(14, 165, 233, 0.2);">
                    <i class="fa-solid fa-brain me-1"></i> Xem Tóm tắt AI
                </button>
            </div>
            <?php endif; ?>

            <!-- 2. Medical Records Timeline -->
            <div class="content-card border-0 shadow-sm" style="border-radius: 16px;">
                <div class="card-header bg-white border-0 pt-4 pb-2">
                    <h5 class="fw-bold m-0" style="color: var(--dark);"><i class="fa-solid fa-clock-rotate-left text-warning me-2"></i>Lịch sử khám bệnh & Điều trị</h5>
                </div>
                <div class="card-body">
                    <?php if (empty($records)): ?>
                        <div class="text-center py-5 text-muted">
                            <i class="fa-solid fa-file-medical-flag mb-3" style="font-size: 40px; color: var(--gray-300);"></i>
                            <h6>Chưa có lịch sử bệnh án</h6>
                            <p class="m-0" style="font-size:14px;">Bệnh nhân này chưa thực hiện lượt khám bệnh nào tại bệnh viện.</p>
                        </div>
                    <?php else: ?>
                        <div class="timeline medical-timeline">
                            <?php foreach ($records as $i => $rec): ?>
                            <div class="timeline-item">
                                <!-- Circular timeline icon centered on line -->
                                <div class="timeline-icon">
                                    <i class="fa-solid fa-notes-medical"></i>
                                </div>
                                
                                <div class="card border-0 shadow-sm" style="border-radius: 12px; background: var(--gray-50);">
                                    <div class="card-header bg-transparent border-bottom-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
                                        <span class="badge bg-white text-dark border px-2.5 py-1.5" style="border-radius: 6px;"><i class="fa-regular fa-calendar me-1 text-primary"></i><?= date('d/m/Y - H:i', strtotime($rec['created_at'])) ?></span>
                                        <span class="text-muted" style="font-size: 13px;">
                                            <i class="fa-solid fa-user-doctor me-1 text-secondary"></i>BS. <?= htmlspecialchars(preg_replace('/^(Bác sĩ|BS\.|Bs\.|Bs|BS)\s+/iu', '', $rec['doctor_name'] ?? 'N/A')) ?>
                                        </span>
                                    </div>
                                    <div class="card-body">
                                        <h6 class="fw-bold mb-2" style="color: var(--dark);"><span class="text-danger">Chẩn đoán:</span> <?= htmlspecialchars($rec['diagnosis'] ?? '') ?></h6>
                                        
                                        <?php if (!empty($rec['treatment'])): ?>
                                        <div class="mt-2" style="font-size: 14px; color: var(--gray-600); background: white; padding: 12px; border-radius: 8px; border-left: 3px solid var(--success); border: 1px solid var(--gray-200); border-left-width: 4px; border-left-color: var(--success);">
                                            <strong style="color: var(--success); display: block; margin-bottom: 5px; font-size: 13px;">Hướng điều trị / Đơn thuốc:</strong>
                                            <?= nl2br(htmlspecialchars($rec['treatment'])) ?>
                                        </div>
                                        <?php endif; ?>

                                        <?php if (!empty($rec['notes'])): ?>
                                        <div class="mt-2" style="font-size: 13px; color: var(--gray-500);">
                                            <i class="fa-solid fa-comment-medical me-1"></i><strong>Ghi chú thêm:</strong> <?= htmlspecialchars($rec['notes']) ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<!-- 3. AI Assistant Right-Drawer & Overlay (Doctor-only) -->
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
        <!-- Skeleton Loader (Spinner) -->
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

        <!-- Rendered Content Area -->
        <div id="aiSummaryRendered" class="markdown-body" style="display:none; font-size: 14px;">
            <!-- Content parsed from AI -->
        </div>
    </div>
    <div class="ai-drawer-footer">
        <p class="m-0 text-muted" style="font-size: 11px; line-height: 1.4;"><i class="fa-solid fa-circle-info me-1"></i><strong>Thông tin AI:</strong> Tóm tắt hoàn toàn tự động, mang tính chất hỗ trợ tham khảo chuyên môn.</p>
    </div>
</div>

<!-- Load Marked.js for markdown rendering -->
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const patientId = <?= intval($patient['id']) ?>;
    const btnTrigger = document.getElementById('btnTriggerAI');
    const drawer = document.getElementById('aiDrawer');
    const overlay = document.getElementById('aiDrawerOverlay');
    const btnClose = document.getElementById('btnOverlayClose');
    
    let isLoaded = false; // State to prevent duplicate calls

    // Toggle Drawer Open
    if (btnTrigger) {
        btnTrigger.addEventListener('click', function() {
            drawer.classList.add('active');
            overlay.classList.add('active');
            
            // Only trigger API if not loaded
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
                            iconBox.style.background = 'linear-gradient(135deg, var(--primary), var(--primary-dark))';
                            iconBox.style.color = 'white';
                            iconBox.style.boxShadow = '0 4px 15px rgba(14, 165, 233, 0.3)';
                            
                            title.textContent = 'Phân tích lâm sàng AI' + (data.fallback ? ' (Dự phòng)' : '');
                            title.style.color = 'var(--primary)';
                            
                            const renderedEl = document.getElementById('aiSummaryRendered');
                            renderedEl.innerHTML = marked.parse(data.summary);
                            renderedEl.style.display = 'block';
                            isLoaded = true; // Mark as successfully cached
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

    // Toggle Drawer Close
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
