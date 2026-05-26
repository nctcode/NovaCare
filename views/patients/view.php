<!-- Patient Detail View with AI Support Right-Drawer & Clinical History -->
<div class="row g-4">
    <!-- Left Column: Patient Profile -->
    <div class="col-lg-4" data-aos="fade-right">
        <!-- Profile Card -->
        <div class="content-card mb-4 text-center p-4">
            <!-- Circular Avatar Container with soft border and background -->
            <div class="d-flex align-items-center justify-content-center mx-auto mb-3" 
                 style="width: 80px; height: 80px; font-size: 32px; border-radius: 50%; background: #e0f2fe; color: #0284c7; border: 3px solid #bae6fd; font-weight: 700; text-transform: uppercase; box-shadow: 0 4px 12px rgba(2, 132, 199, 0.08);">
                <?= mb_substr($patient['name'], 0, 1, 'utf-8') ?>
            </div>
            
            <h5 class="fw-bold mb-1" style="color: var(--dark);"><?= htmlspecialchars($patient['name']) ?></h5>
            <span class="badge-status badge-<?= $patient['gender'] ?? '' ?> mb-3">
                <?php
                    $genderMap = ['male' => 'Nam', 'female' => 'Nữ', 'other' => 'Khác'];
                    echo $genderMap[$patient['gender']] ?? 'N/A';
                ?>
            </span>

            <hr class="my-3">

            <div class="text-start">
                <div class="mb-3">
                    <span class="text-muted d-block" style="font-size: 12px; font-weight: 500;">EMAIL</span>
                    <strong style="color: var(--dark); word-break: break-all;"><?= htmlspecialchars($patient['email']) ?></strong>
                </div>
                <div class="mb-3">
                    <span class="text-muted d-block" style="font-size: 12px; font-weight: 500;">SỐ ĐIỆN THOẠI</span>
                    <strong style="color: var(--dark);"><?= htmlspecialchars($patient['phone'] ?? 'Chưa cập nhật') ?></strong>
                </div>
                <div class="mb-3">
                    <span class="text-muted d-block" style="font-size: 12px; font-weight: 500;">NGÀY SINH</span>
                    <strong style="color: var(--dark);">
                        <?= $patient['date_of_birth'] ? date('d/m/Y', strtotime($patient['date_of_birth'])) : 'N/A' ?>
                    </strong>
                </div>
                <div class="mb-3">
                    <span class="text-muted d-block" style="font-size: 12px; font-weight: 500;">NHÓM MÁU</span>
                    <strong style="color: var(--dark);"><i class="fa-solid fa-droplet text-danger me-1"></i><?= htmlspecialchars($patient['blood_type'] ?? 'N/A') ?></strong>
                </div>
                <div class="mb-2">
                    <span class="text-muted d-block" style="font-size: 12px; font-weight: 500;">ĐỊA CHỈ</span>
                    <strong style="color: var(--dark);"><?= htmlspecialchars($patient['address'] ?? 'N/A') ?></strong>
                </div>
            </div>
        </div>

        <!-- Medical History -->
        <div class="content-card p-4">
            <h6 class="fw-bold mb-3" style="color: var(--dark);"><i class="fa-solid fa-notes-medical text-primary me-2"></i>Tiền sử bệnh lý</h6>
            <div class="p-3 bg-light rounded" style="font-size: 14px; color: var(--gray-700); line-height: 1.6;">
                <?= !empty($patient['medical_history']) ? nl2br(htmlspecialchars($patient['medical_history'])) : 'Không ghi nhận tiền sử bệnh đặc biệt nào khi đăng ký.' ?>
            </div>
        </div>

        <!-- Back Button -->
        <div class="mt-4">
            <a href="index.php?page=patients" class="btn btn-outline-secondary w-100 py-2" style="border-radius: 10px; font-weight: 600;">
                <i class="fa-solid fa-arrow-left me-2"></i>Quay lại danh sách
            </a>
        </div>
    </div>

    <!-- Right Column: Medical History & AI Trigger Banner -->
    <div class="col-lg-8" data-aos="fade-left">
        
        <!-- 1. Sleek AI Trigger Banner (Space-saving, height limit 150px) -->
        <?php if (count($records) > 0 && in_array($_SESSION['user']['role'], ['admin', 'doctor'])): ?>
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
        <div class="content-card">
            <div class="card-header bg-white">
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
                            
                            <div class="card border-0 shadow-sm">
                                <div class="card-header bg-white border-bottom-0 pt-3 pb-0 d-flex justify-content-between align-items-center">
                                    <span class="badge bg-light text-dark border"><i class="fa-regular fa-calendar me-1"></i><?= date('d/m/Y - H:i', strtotime($rec['created_at'])) ?></span>
                                    <!-- Stripped duplicates like "BS. Bác sĩ" to "BS." -->
                                    <span class="text-muted" style="font-size: 13px;">
                                        <i class="fa-solid fa-user-doctor me-1"></i>BS. <?= htmlspecialchars(preg_replace('/^(Bác sĩ|BS\.|Bs\.|Bs|BS)\s+/iu', '', $rec['doctor_name'] ?? 'N/A')) ?>
                                    </span>
                                </div>
                                <div class="card-body">
                                    <h6 class="fw-bold mb-2" style="color: var(--dark);"><span class="text-danger">Chẩn đoán:</span> <?= htmlspecialchars($rec['diagnosis'] ?? '') ?></h6>
                                    
                                    <?php if (!empty($rec['treatment'])): ?>
                                    <div class="mt-2" style="font-size: 14px; color: var(--gray-600); background: #f8fafc; padding: 12px; border-radius: 8px; border-left: 3px solid var(--success);">
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

    </div>
</div>

<!-- 3. AI Assistant Right-Drawer & Overlay (Rendered at layout bottom) -->
<?php if (count($records) > 0 && in_array($_SESSION['user']['role'], ['admin', 'doctor'])): ?>
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
