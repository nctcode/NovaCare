<?php
// Database connections
$db = new Database();
$conn = $db->getConnection();

// Lấy thông tin hành chính bệnh nhân
$stmtPatient = $conn->prepare("SELECT p.*, u.name, u.email, u.phone 
                               FROM patients p 
                               JOIN users u ON p.user_id = u.id 
                               WHERE p.id = ?");
$stmtPatient->execute([$record['patient_id']]);
$patientInfo = $stmtPatient->fetch(PDO::FETCH_ASSOC);

// Lấy chi tiết ICD-10 nếu có
$icdDetails = null;
if (!empty($record['icd10_code'])) {
    $stmtIcd = $conn->prepare("SELECT name, name_en, category FROM icd10_codes WHERE code = ?");
    $stmtIcd->execute([$record['icd10_code']]);
    $icdDetails = $stmtIcd->fetch(PDO::FETCH_ASSOC);
}
?>

<!-- Custom Premium CSS for EMR Console - Light Mode -->
<style>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap');

.emr-console-wrapper {
    font-family: 'Outfit', sans-serif;
    background: #f8fafc;
    color: #1e293b;
    border-radius: 24px;
    padding: 2.5rem;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
    border: 1px solid #e2e8f0;
    margin-bottom: 2rem;
    position: relative;
    overflow: hidden;
}

.emr-console-wrapper::before {
    content: '';
    position: absolute;
    top: -50%;
    left: -50%;
    width: 200%;
    height: 200%;
    background: radial-gradient(circle, rgba(99, 102, 241, 0.02) 0%, transparent 60%);
    pointer-events: none;
}

.emr-header-bar {
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #e2e8f0;
    padding-bottom: 1.5rem;
    margin-bottom: 2rem;
}

.emr-header-title h4 {
    font-weight: 800;
    background: linear-gradient(135deg, #0284c7 0%, #4f46e5 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    letter-spacing: -0.5px;
    margin: 0;
}

.status-indicator {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    background: rgba(16, 185, 129, 0.08);
    color: #059669;
    border: 1px solid rgba(16, 185, 129, 0.15);
    padding: 0.35rem 0.75rem;
    border-radius: 9999px;
    font-size: 11px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.status-indicator.secure {
    background: rgba(14, 165, 233, 0.08);
    color: #0284c7;
    border-color: rgba(14, 165, 233, 0.15);
}

.pulse-dot {
    width: 6px;
    height: 6px;
    background-color: currentColor;
    border-radius: 50%;
    animation: pulseGlow 1.5s infinite alternate;
}

@keyframes pulseGlow {
    0% { transform: scale(0.8); opacity: 0.5; }
    100% { transform: scale(1.3); opacity: 1; box-shadow: 0 0 6px currentColor; }
}

.glass-panel {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 20px;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.01), 0 2px 4px -1px rgba(0, 0, 0, 0.01);
}

.glass-panel:hover {
    border-color: rgba(99, 102, 241, 0.25);
    box-shadow: 0 10px 25px rgba(99, 102, 241, 0.05);
}

.patient-avatar-wrapper {
    position: relative;
    display: inline-block;
}

.patient-avatar-circle {
    width: 80px;
    height: 80px;
    border-radius: 24px;
    background: linear-gradient(135deg, #0ea5e9 0%, #6366f1 100%);
    font-size: 32px;
    font-weight: 800;
    color: #fff;
    display: flex;
    align-items: center;
    justify-content: center;
    box-shadow: 0 8px 20px rgba(14, 165, 233, 0.15);
}

.patient-avatar-badge {
    position: absolute;
    bottom: -5px;
    right: -5px;
    background: #ef4444;
    color: white;
    font-size: 11px;
    font-weight: 800;
    padding: 0.25rem 0.5rem;
    border-radius: 8px;
    border: 2px solid #ffffff;
}

.info-label {
    font-size: 11px;
    color: #64748b;
    text-transform: uppercase;
    font-weight: 600;
    letter-spacing: 0.5px;
}

.info-val {
    font-size: 14px;
    color: #1e293b;
    font-weight: 600;
}

.clinical-card {
    position: relative;
    border-radius: 16px;
    padding: 1.5rem;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    margin-bottom: 1.5rem;
    transition: all 0.3s ease;
}

.clinical-card:hover {
    background: #fbfcfd;
    border-color: #cbd5e1;
}

.clinical-card.diagnosis-card {
    border-left: 4px solid #f43f5e;
}
.clinical-card.treatment-card {
    border-left: 4px solid #10b981;
}
.clinical-card.notes-card {
    border-left: 4px solid #f59e0b;
}

.clinical-card-header {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 1rem;
}

.clinical-card-icon {
    width: 36px;
    height: 36px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 16px;
}

.diagnosis-card .clinical-card-icon {
    background: rgba(244, 63, 94, 0.08);
    color: #f43f5e;
}
.treatment-card .clinical-card-icon {
    background: rgba(16, 185, 129, 0.08);
    color: #10b981;
}
.notes-card .clinical-card-icon {
    background: rgba(245, 158, 11, 0.08);
    color: #f59e0b;
}

.clinical-text-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 12px;
    padding: 1.25rem;
    color: #334155;
    font-size: 14.5px;
    line-height: 1.7;
    white-space: pre-line;
}

.mono-text {
    font-family: 'JetBrains Mono', monospace;
    font-size: 13.5px;
    color: #475569;
}

.digital-signature-box {
    border: 1px solid rgba(16, 185, 129, 0.25);
    background: rgba(16, 185, 129, 0.02);
    border-radius: 16px;
    padding: 1.25rem;
}

.action-btn {
    border-radius: 12px;
    padding: 0.75rem 1.5rem;
    font-weight: 600;
    font-size: 13.5px;
    transition: all 0.2s ease;
}

.btn-primary-gradient {
    background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);
    color: white;
    border: none;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
}

.btn-primary-gradient:hover {
    background: linear-gradient(135deg, #38bdf8 0%, #2563eb 100%);
    transform: translateY(-2px);
    box-shadow: 0 6px 16px rgba(37, 99, 235, 0.25);
}

.btn-secondary-outline {
    background: #ffffff;
    border: 1px solid #cbd5e1;
    color: #475569;
}

.btn-secondary-outline:hover {
    background: #f1f5f9;
    color: #0f172a;
    border-color: #94a3b8;
}

/* Timeline elements */
.flow-step {
    position: relative;
    padding-left: 2rem;
}
.flow-step::before {
    content: '';
    position: absolute;
    left: 7px;
    top: 24px;
    bottom: -24px;
    width: 2px;
    background: #e2e8f0;
}
.flow-step:last-child::before {
    display: none;
}
.flow-step-dot {
    position: absolute;
    left: 0;
    top: 4px;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    background: #ffffff;
    border: 3px solid #0ea5e9;
    display: flex;
    align-items: center;
    justify-content: center;
}

@media print {
    .emr-console-wrapper {
        background: #fff !important;
        color: #000 !important;
        border: none !important;
        box-shadow: none !important;
        padding: 0 !important;
    }
    .glass-panel {
        background: #fff !important;
        border: none !important;
        box-shadow: none !important;
    }
    .info-label { color: #555 !important; }
    .info-val { color: #000 !important; }
    .clinical-card {
        background: #fff !important;
        border: 1px solid #ddd !important;
        color: #000 !important;
    }
    .clinical-text-box {
        background: #fff !important;
        border: 1px solid #eee !important;
        color: #000 !important;
    }
    .btn, .action-btn, aside, .top-navbar, .no-print {
        display: none !important;
    }
    .emr-header-title h4 {
        -webkit-text-fill-color: #000 !important;
        color: #000 !important;
    }
}
</style>

<div class="emr-console-wrapper" data-aos="fade-up">
    <!-- Console Control Header -->
    <div class="emr-header-bar">
        <div class="emr-header-title d-flex align-items-center gap-3">
            <div class="clinical-card-icon bg-opacity-10 d-flex justify-content-center align-items-center" style="background: rgba(14, 165, 233, 0.08); color: #0ea5e9; width: 44px; height: 44px; border-radius: 12px;">
                <i class="fa-solid fa-file-waveform fs-5"></i>
            </div>
            <div>
                <h4>BỆNH ÁN CHI TIẾT HỆ THỐNG EMR</h4>
                <small class="text-secondary mono-text">MÃ LƯU TRỮ: #MR-<?= str_pad($record['id'], 5, '0', STR_PAD_LEFT) ?></small>
            </div>
        </div>
        
        <div class="d-flex align-items-center gap-3 no-print">
            <span class="status-indicator secure">
                <span class="pulse-dot"></span> ĐÃ XÁC MINH AN TOÀN
            </span>
            <button onclick="window.print()" class="btn btn-sm btn-secondary-outline action-btn py-2 px-3">
                <i class="fa-solid fa-print me-1.5"></i> IN HỒ SƠ Y KHOA
            </button>
        </div>
    </div>

    <!-- Main Workspace Grid -->
    <div class="row g-4">
        <!-- Patient Clinical Sidebar (Left) -->
        <div class="col-lg-4">
            <!-- Patient Demographics Card -->
            <div class="card glass-panel border-0 mb-4">
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="patient-avatar-wrapper mb-3">
                            <div class="patient-avatar-circle mx-auto">
                                <?= mb_substr($patientInfo['name'], 0, 1, 'utf-8') ?>
                            </div>
                            <span class="patient-avatar-badge"><?= htmlspecialchars($patientInfo['blood_type'] ?: 'N/A') ?></span>
                        </div>
                        <h5 class="fw-bold text-dark mb-1"><?= htmlspecialchars($patientInfo['name']) ?></h5>
                        <span class="badge bg-primary-subtle text-primary border-0 px-3 py-1.5 rounded-pill small" style="font-weight: 600; font-size: 11px;">
                            <i class="fa-solid fa-id-card me-1"></i> PAT-<?= str_pad($record['patient_id'], 4, '0', STR_PAD_LEFT) ?>
                        </span>
                    </div>

                    <!-- Personal Metrics Info -->
                    <div class="row g-3 py-3 border-top border-bottom border-light border-opacity-10 mb-4" style="background: #f8fafc; border-radius: 12px; margin: 0 1px;">
                        <div class="col-6 text-center border-end border-light-subtle">
                            <span class="info-label d-block mb-1">Ngày sinh</span>
                            <span class="info-val d-block text-dark"><?= $patientInfo['date_of_birth'] ? date('d/m/Y', strtotime($patientInfo['date_of_birth'])) : 'Chưa nhập' ?></span>
                        </div>
                        <div class="col-6 text-center">
                            <span class="info-label d-block mb-1">Giới tính</span>
                            <span class="info-val d-block text-dark">
                                <?php if ($patientInfo['gender'] === 'male'): ?>
                                    <i class="fa-solid fa-mars text-primary me-1"></i> Nam
                                <?php elseif ($patientInfo['gender'] === 'female'): ?>
                                    <i class="fa-solid fa-venus text-danger me-1"></i> Nữ
                                <?php else: ?>
                                    <i class="fa-solid fa-genderless text-muted me-1"></i> Khác
                                <?php endif; ?>
                            </span>
                        </div>
                    </div>

                    <!-- Contact & Health Background Info -->
                    <div class="d-flex flex-column gap-3">
                        <div>
                            <span class="info-label d-block mb-1"><i class="fa-solid fa-phone me-1.5 text-primary"></i> Số điện thoại</span>
                            <span class="info-val text-dark"><?= htmlspecialchars($patientInfo['phone'] ?: 'Chưa cập nhật') ?></span>
                        </div>
                        <div>
                            <span class="info-label d-block mb-1"><i class="fa-solid fa-envelope me-1.5 text-warning"></i> Email liên lạc</span>
                            <span class="info-val text-dark text-truncate d-block" title="<?= htmlspecialchars($patientInfo['email']) ?>"><?= htmlspecialchars($patientInfo['email'] ?: 'Chưa cập nhật') ?></span>
                        </div>
                        <div>
                            <span class="info-label d-block mb-1"><i class="fa-solid fa-location-dot me-1.5 text-danger"></i> Địa chỉ thường trú</span>
                            <span class="info-val text-dark" style="font-size: 13px; line-height: 1.4; display: block;"><?= htmlspecialchars($patientInfo['address'] ?: 'Chưa cập nhật') ?></span>
                        </div>
                        <div>
                            <span class="info-label d-block mb-1"><i class="fa-solid fa-clock-history me-1.5 text-success"></i> Tiền sử bệnh lý</span>
                            <span class="info-val bg-light p-2.5 d-block rounded-3 border" style="font-size: 12.5px; line-height: 1.4; color: #475569;">
                                <?= htmlspecialchars($patientInfo['medical_history'] ?: 'Không ghi nhận tiền sử y khoa đặc biệt.') ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Doctor In-Charge Details Card -->
            <div class="card glass-panel border-0 mb-4">
                <div class="card-body p-4">
                    <h6 class="text-uppercase text-muted fw-bold mb-3" style="font-size: 11px; letter-spacing: 0.5px;"><i class="fa-solid fa-user-doctor text-success me-1.5"></i>Bác sĩ lập hồ sơ</h6>
                    <div class="d-flex align-items-center">
                        <div class="text-white d-flex justify-content-center align-items-center" 
                             style="width: 44px; height: 44px; border-radius: 12px; font-size: 16px; font-weight: 800; background: linear-gradient(135deg, #10b981, #059669); flex-shrink: 0; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.15);">
                            <i class="fa-solid fa-user-md"></i>
                        </div>
                        <div class="ms-3">
                            <h6 class="m-0 fw-bold text-success" style="font-size: 14.5px;">BS. <?= htmlspecialchars($record['doctor_name']) ?></h6>
                            <small class="text-muted d-block mt-0.5" style="font-size: 12px;">Khoa Khám Lâm Sàng - NovaCare</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- EMR Interactive Sheet (Right) -->
        <div class="col-lg-8">
            <div class="card glass-panel border-0 h-100">
                <div class="card-body p-4">
                    <!-- Section Header -->
                    <div class="d-flex justify-content-between align-items-center mb-4 border-bottom border-light-subtle pb-3">
                        <h5 class="fw-bold text-dark mb-0 d-flex align-items-center gap-2">
                            <i class="fa-solid fa-folder-open text-primary"></i> CHI TIẾT KHÁM LÂM SÀNG
                        </h5>
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 px-2.5 py-1.5 rounded-3" style="font-size: 11px;">
                            ● ĐÃ LƯU HỆ THỐNG
                        </span>
                    </div>

                    <!-- Flow of Clinical Record -->
                    <div class="d-flex flex-column gap-2">
                        <!-- Step 1: Diagnosis & ICD-10 -->
                        <div class="flow-step">
                            <div class="flow-step-dot" style="border-color: #f43f5e;"></div>
                            <div class="clinical-card diagnosis-card">
                                <div class="clinical-card-header">
                                    <div class="clinical-card-icon">
                                        <i class="fa-solid fa-stethoscope"></i>
                                    </div>
                                    <h6 class="mb-0 fw-bold text-dark">1. Chẩn đoán & Mã bệnh ICD-10</h6>
                                </div>
                                
                                <?php if ($icdDetails): ?>
                                <div class="mb-3 p-3 rounded-3 d-flex flex-wrap align-items-center gap-2" style="font-size: 13px; background: rgba(244, 63, 94, 0.05); border: 1px solid rgba(244, 63, 94, 0.15);">
                                    <span class="badge bg-danger text-white px-2 py-1" style="font-size: 10.5px; font-weight: 700;">
                                        ICD-10: <?= htmlspecialchars($record['icd10_code']) ?>
                                    </span>
                                    <strong class="text-dark"><?= htmlspecialchars($icdDetails['name'] ?? '') ?></strong>
                                    <?php if (!empty($icdDetails['name_en'])): ?>
                                        <span class="text-secondary">| <?= htmlspecialchars($icdDetails['name_en']) ?></span>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>

                                <div class="clinical-text-box text-dark fw-bold" style="border-left: 3px solid #f43f5e;">
                                    <?= nl2br(htmlspecialchars($record['diagnosis'])) ?>
                                </div>
                            </div>
                        </div>

                        <!-- Step 2: Treatment Plan -->
                        <div class="flow-step">
                            <div class="flow-step-dot" style="border-color: #10b981;"></div>
                            <div class="clinical-card treatment-card">
                                <div class="clinical-card-header">
                                    <div class="clinical-card-icon">
                                        <i class="fa-solid fa-prescription"></i>
                                    </div>
                                    <h6 class="mb-0 fw-bold text-dark">2. Phác đồ & Thuốc chỉ định</h6>
                                </div>

                                <div class="clinical-text-box text-dark" style="border-left: 3px solid #10b981;">
                                    <?php if (empty($record['treatment'])): ?>
                                        <span class="text-muted fst-italic"><i class="fa-solid fa-circle-info me-2 text-opacity-50"></i>Không có hướng điều trị đặc hiệu được ghi nhận thêm.</span>
                                    <?php else: ?>
                                        <?= htmlspecialchars($record['treatment']) ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <!-- Step 3: Physician Warning Notes -->
                        <div class="flow-step">
                            <div class="flow-step-dot" style="border-color: #f59e0b;"></div>
                            <div class="clinical-card notes-card">
                                <div class="clinical-card-header">
                                    <div class="clinical-card-icon">
                                        <i class="fa-solid fa-clipboard-list"></i>
                                    </div>
                                    <h6 class="mb-0 fw-bold text-dark">3. Dặn dò y khoa & Hướng dẫn phục hồi</h6>
                                </div>

                                <div class="clinical-text-box text-dark" style="border-left: 3px solid #f59e0b;">
                                    <?php if (empty($record['notes'])): ?>
                                        <span class="text-muted"><i class="fa-solid fa-comment-slash me-2 text-opacity-50"></i>Không có dặn dò bổ sung từ bác sĩ phụ trách.</span>
                                    <?php else: ?>
                                        <?= htmlspecialchars($record['notes']) ?>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Linked Encounter Details -->
                    <?php if (!empty($record['appointment_id'])): ?>
                    <div class="mt-4 p-3 rounded-4" style="background: rgba(14, 165, 233, 0.02); border: 1px dashed rgba(14, 165, 233, 0.25); font-size: 13px;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-secondary"><i class="fa-solid fa-link me-1.5 text-primary text-opacity-75"></i> Lượt khám bệnh qua lịch hẹn</span>
                            <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25 px-2">#APP-<?= str_pad($record['appointment_id'], 4, '0', STR_PAD_LEFT) ?></span>
                        </div>
                        <strong class="text-dark d-block mb-1.5"><?= date('H:i - d/m/Y', strtotime($record['appointment_date'])) ?></strong>
                        <div class="text-secondary" style="font-size: 12px; line-height: 1.4;">Lý do khám bệnh ban đầu: <em class="text-dark text-opacity-75">"<?= htmlspecialchars($record['reason']) ?>"</em></div>
                    </div>
                    <?php endif; ?>

                    <!-- Digital Signature Authentication Block -->
                    <div class="digital-signature-box mt-4 d-flex flex-wrap align-items-center justify-content-between gap-3">
                        <div class="d-flex align-items-center gap-3">
                            <div class="bg-success bg-opacity-10 text-success rounded-circle d-flex align-items-center justify-content-center" style="width: 44px; height: 44px; border: 1px solid rgba(16, 185, 129, 0.25);">
                                <i class="fa-solid fa-shield-halved fs-5"></i>
                            </div>
                            <div>
                                <h6 class="mb-0 fw-bold text-dark" style="font-size: 14px;">KÝ BỆNH ÁN ĐIỆN TỬ ĐÃ XÁC THỰC</h6>
                                <small class="text-secondary mono-text" style="font-size: 11.5px;">MÃ HÓA HỆ THỐNG: <?= hash('sha256', $record['id'] . $record['created_at']) ?></small>
                            </div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-success text-white px-3 py-1.5 rounded-pill small" style="font-weight: 700; font-size: 11.5px; box-shadow: 0 4px 10px rgba(16,185,129,0.15);">
                                <i class="fa-solid fa-signature me-1.5"></i> BS. <?= htmlspecialchars($record['doctor_name']) ?>
                            </span>
                            <small class="text-muted d-block mt-1" style="font-size: 11px;">Đã ký số: <?= date('d/m/Y H:i', strtotime($record['created_at'])) ?></small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Navigation & Bottom Actions Panel -->
    <div class="mt-4 pt-3 border-top border-light-subtle d-flex justify-content-between align-items-center flex-wrap gap-3 no-print">
        <a href="index.php?page=records" class="btn btn-secondary-outline action-btn">
            <i class="fa-solid fa-arrow-left-long me-2"></i> Trở về Danh sách
        </a>
        <div class="d-flex gap-2">
            <button onclick="window.print()" class="btn btn-primary-gradient action-btn">
                <i class="fa-solid fa-print me-1.5"></i> IN HỒ SƠ Y KHOA
            </button>
        </div>
    </div>
</div>
