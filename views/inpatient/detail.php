<!-- Inpatient Medical Record Detail View -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-custom alert-dismissible fade show">
        <i class="fa-solid fa-check-circle"></i> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php
$sClasses = [
    'pending' => 'badge bg-warning-subtle text-warning border border-warning-subtle',
    'active' => 'badge bg-success-subtle text-success border border-success-subtle',
    'discharged' => 'badge bg-secondary-subtle text-secondary border',
    'transferred' => 'badge bg-warning-subtle text-warning border border-warning-subtle'
];
$sLabels = [
    'pending' => 'Chờ xếp giường',
    'active' => 'Đang nằm viện',
    'discharged' => 'Đã xuất viện',
    'transferred' => 'Chuyển khoa'
];
$typeLabels = ['standard'=>'Thường','vip'=>'VIP','icu'=>'ICU'];
$genderLabels = ['male'=>'Nam','female'=>'Nữ','other'=>'Khác'];
?>

<!-- Premium Clinical EMR Custom Styling for Inpatient details -->
<style>
    .inpatient-header-banner {
        border-radius: 20px;
        background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%);
        border: 1px solid #d1fae5;
        box-shadow: 0 4px 20px -2px rgba(16, 185, 129, 0.05);
        position: relative;
        overflow: hidden;
    }
    .inpatient-header-banner::before {
        content: "";
        position: absolute;
        top: -30%;
        right: -10%;
        width: 250px;
        height: 250px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(16, 185, 129, 0.08) 0%, transparent 70%);
        pointer-events: none;
    }
    .inpatient-avatar {
        width: 80px;
        height: 80px;
        font-size: 32px;
        font-weight: 800;
        text-transform: uppercase;
        border-radius: 20px;
        background: linear-gradient(135deg, #34d399, #059669);
        color: #ffffff;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 8px 16px rgba(5, 150, 105, 0.12);
        border: 3px solid #ffffff;
        flex-shrink: 0;
    }
    .inpatient-tag {
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
        box-shadow: 0 2px 4px rgba(0,0,0,0.01);
    }
    /* Tab controls custom design */
    .inpatient-nav-tabs {
        border-bottom: 2px solid #e2e8f0;
        gap: 16px;
    }
    .inpatient-nav-tabs .nav-link {
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
    .inpatient-nav-tabs .nav-link:hover {
        color: #059669;
        border-bottom-color: #a7f3d0;
    }
    .inpatient-nav-tabs .nav-link.active {
        color: #059669;
        border-bottom-color: #059669;
        background: transparent;
    }
    .grid-info-box {
        transition: all 0.2s ease;
        border: 1px solid #f1f5f9;
        background-color: #f8fafc;
        border-radius: 14px;
        height: 100%;
    }
    .grid-info-box:hover {
        background-color: #ffffff;
        border-color: #e2e8f0;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.02);
        transform: translateY(-1px);
    }
    .info-icon-circle {
        width: 40px;
        height: 40px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 16px;
        flex-shrink: 0;
    }
</style>

<!-- Inpatient EMR Header Banner -->
<div class="row mb-4">
    <div class="col-12" data-aos="fade-down">
        <div class="inpatient-header-banner p-4">
            <div class="d-flex flex-column flex-md-row align-items-center justify-content-between gap-4">
                <div class="d-flex flex-column flex-md-row align-items-center gap-4 w-100">
                    <!-- Patient Avatar -->
                    <div class="inpatient-avatar">
                        <?= mb_substr($admission['patient_name'], 0, 1, 'utf-8') ?>
                    </div>
                    
                    <!-- Patient & Room information -->
                    <div class="text-center text-md-start flex-grow-1">
                        <div class="d-flex align-items-center justify-content-center justify-content-md-start flex-wrap gap-2.5 mb-2.5">
                            <h3 class="fw-bold m-0 text-dark" style="font-size: 23px; letter-spacing: -0.5px;"><?= htmlspecialchars($admission['patient_name']) ?></h3>
                            <span class="badge text-uppercase <?= $sClasses[$admission['status']] ?? '' ?>" style="font-size: 11px; padding: 4px 10px; font-weight: 700;">
                                <i class="fa-solid fa-circle-dot me-1"></i> <?= $sLabels[$admission['status']] ?? $admission['status'] ?>
                            </span>
                            <span class="badge bg-secondary-subtle text-secondary px-2 py-1" style="font-size: 11px; font-weight: 600; border-radius: 4px;">
                                HSBA: #<?= $admission['id'] ?>
                            </span>
                        </div>
                        
                        <div class="d-flex align-items-center justify-content-center justify-content-md-start flex-wrap gap-2">
                            <span class="inpatient-tag">
                                <i class="fa-solid fa-bed text-success"></i>
                                Phòng: <strong><?= $admission['room_number'] ? $admission['room_number'] . ' (Giường ' . $admission['bed_number'] . ')' : 'Chờ xếp giường' ?></strong>
                            </span>
                            <span class="inpatient-tag">
                                <i class="fa-solid fa-building text-primary"></i>
                                Khoa: <strong><?= htmlspecialchars($admission['department_name'] ?? 'N/A') ?></strong>
                            </span>
                            <span class="inpatient-tag">
                                <i class="fa-solid fa-calendar text-warning"></i>
                                <?= $admission['status'] === 'pending' ? 'Chỉ định:' : 'Nhập viện:' ?> <strong><?= date('d/m/Y H:i', strtotime($admission['admission_date'])) ?></strong>
                            </span>
                        </div>
                    </div>

                    <!-- Action Back -->
                    <div class="text-center text-md-end flex-shrink-0">
                        <a href="index.php?page=inpatient" class="btn btn-sm btn-outline-secondary px-3.5 py-2 fw-semibold" style="border-radius: 8px; font-size: 13px;">
                            <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Tab Navigation Controls -->
<div class="row mb-4">
    <div class="col-12" data-aos="fade-up">
        <ul class="nav nav-tabs inpatient-nav-tabs" id="inpatientTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="vitals-tab" data-bs-toggle="tab" data-bs-target="#vitals-pane" type="button" role="tab">
                    <i class="fa-solid fa-heart-pulse me-2"></i>Chỉ số sinh hiệu & Chăm sóc
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="record-tab" data-bs-toggle="tab" data-bs-target="#record-pane" type="button" role="tab">
                    <i class="fa-solid fa-clipboard-user me-2"></i>Bệnh án & Chẩn đoán
                </button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="cost-tab" data-bs-toggle="tab" data-bs-target="#cost-pane" type="button" role="tab">
                    <i class="fa-solid fa-receipt me-2"></i>Chi phí & Xuất viện
                </button>
            </li>
        </ul>
    </div>
</div>

<!-- Tab Panes Content -->
<div class="tab-content" id="inpatientTabsContent" data-aos="fade-up" data-aos-delay="100">
    
    <!-- Tab 1: Vitals & Nursing Care -->
    <div class="tab-pane fade show active" id="vitals-pane" role="tabpanel" aria-labelledby="vitals-tab">
        <div class="content-card border-0 shadow-sm p-4 bg-white" style="border-radius: 16px;">
            <?php
            require_once __DIR__ . '/../../models/NursingRecord.php';
            $nrModel = new NursingRecord();
            $nursingRecords = $nrModel->getByAdmissionId($admission['id']);
            $vitalData = $nrModel->getVitalChartData($admission['id'], 10);
            ?>
            
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h6 class="fw-bold mb-1 text-dark"><i class="fa-solid fa-heart-pulse me-2 text-danger"></i>Theo dõi sinh hiệu liên tục</h6>
                    <small class="text-muted">Biểu đồ đo lường Nhiệt độ, Huyết áp, Nhịp tim & SpO2 gần đây nhất</small>
                </div>
                <?php if ($admission['status'] === 'active' && in_array($_SESSION['user']['role'], ['admin', 'nurse'])): ?>
                <a href="index.php?page=inpatient&action=addNursingRecord&id=<?= $admission['id'] ?>" class="btn btn-sm btn-primary px-3 py-1.5" style="border-radius: 10px; font-weight: 600; font-size: 13px;">
                    <i class="fa-solid fa-plus me-1"></i> Ghi nhận sinh hiệu mới
                </a>
                <?php endif; ?>
            </div>

            <!-- Vital Signs Graph -->
            <?php if (!empty($vitalData)): ?>
            <div class="p-3 mb-4 rounded-3 border" style="background: #fafaf9;">
                <canvas id="vitalSignsChart" height="150" style="max-height: 220px;"></canvas>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const vd = <?= json_encode($vitalData) ?>;
                const labels = vd.map(r => {
                    const d = new Date(r.record_time);
                    return d.getDate()+'/'+(d.getMonth()+1) + ' ' + d.getHours()+':'+String(d.getMinutes()).padStart(2,'0');
                });
                new Chart(document.getElementById('vitalSignsChart'), {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            { label: 'Nhiệt độ (°C)', data: vd.map(r => r.temperature), borderColor: '#ef4444', backgroundColor: 'rgba(239, 68, 68, 0.1)', borderWidth: 2.5, tension: 0.3.5, fill: false, pointRadius: 4, pointHoverRadius: 6 },
                            { label: 'Nhịp tim (bpm)', data: vd.map(r => r.heart_rate), borderColor: '#3b82f6', backgroundColor: 'rgba(59, 130, 246, 0.1)', borderWidth: 2.5, tension: 0.3.5, fill: false, pointRadius: 4, pointHoverRadius: 6 },
                            { label: 'SpO2 (%)', data: vd.map(r => r.spo2), borderColor: '#10b981', backgroundColor: 'rgba(16, 185, 129, 0.1)', borderWidth: 2.5, tension: 0.3.5, fill: false, pointRadius: 4, pointHoverRadius: 6 }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { position: 'bottom', labels: { usePointStyle: true, font: { weight: '600' } } } },
                        scales: { 
                            y: { beginAtZero: false, grid: { color: '#f1f5f9' } }, 
                            x: { grid: { display: false } } 
                        }
                    }
                });
            });
            </script>
            <?php endif; ?>

            <!-- Vital Logs Table -->
            <h6 class="fw-bold mb-3 text-dark mt-4">Nhật ký Chăm sóc & Theo dõi của Điều dưỡng</h6>
            <?php if (!empty($nursingRecords)): ?>
            <div class="table-wrapper">
                <table class="data-table" style="font-size:13.5px;">
                    <thead>
                        <tr>
                            <th>Thời gian ghi</th>
                            <th>Điều dưỡng phụ trách</th>
                            <th>Nhiệt độ</th>
                            <th>Huyết áp</th>
                            <th>Nhịp tim</th>
                            <th>SpO2</th>
                            <th>Ghi chú chăm sóc</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($nursingRecords as $nr): ?>
                        <tr>
                            <td><strong class="text-primary"><?= date('d/m/Y H:i', strtotime($nr['record_time'])) ?></strong></td>
                            <td><strong class="text-dark"><?= htmlspecialchars($nr['nurse_name']) ?></strong></td>
                            <td><span class="badge bg-danger-subtle text-danger" style="font-size:12px;"><?= $nr['temperature'] ? $nr['temperature'].'°C' : '-' ?></span></td>
                            <td><span class="badge bg-primary-subtle text-primary" style="font-size:12px;"><?= ($nr['blood_pressure_sys'] && $nr['blood_pressure_dia']) ? $nr['blood_pressure_sys'].'/'.$nr['blood_pressure_dia'].' mmHg' : '-' ?></span></td>
                            <td><strong><?= $nr['heart_rate'] ? $nr['heart_rate'].' bpm' : '-' ?></strong></td>
                            <td><span class="badge bg-success-subtle text-success" style="font-size:12px;"><?= $nr['spo2'] ? $nr['spo2'].'%' : '-' ?></span></td>
                            <td><small class="text-secondary"><?= htmlspecialchars($nr['care_notes'] ?? '-') ?></small></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="empty-state py-5 text-center text-muted border rounded-3">
                <div class="empty-icon fs-2 text-secondary opacity-25 mb-2"><i class="fa-solid fa-clipboard-list"></i></div>
                <h6 class="fw-bold text-secondary">Chưa có hồ sơ theo dõi sinh hiệu</h6>
                <p class="small text-muted mb-0">Hệ thống chưa ghi nhận chỉ số chăm sóc nào cho đợt điều trị nội trú này.</p>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Tab 2: Admission & Clinical Diagnosis -->
    <div class="tab-pane fade" id="record-pane" role="tabpanel" aria-labelledby="record-tab">
        <div class="content-card border-0 shadow-sm p-4 bg-white" style="border-radius: 16px;">
            <div class="row g-4">
                
                <!-- Patient Info Grid -->
                <div class="col-lg-6">
                    <div class="p-3.5 border rounded-4" style="background: #f8fafc;">
                        <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-id-card text-primary me-2"></i>Hành chính Bệnh nhân</h6>
                        <div class="row g-3">
                            <div class="col-6">
                                <small class="text-muted d-block">HỌ TÊN</small>
                                <strong class="text-dark"><?= htmlspecialchars($admission['patient_name']) ?></strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">GIỚI TÍNH</small>
                                <strong class="text-dark"><?= $genderLabels[$admission['gender'] ?? ''] ?? '-' ?></strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">NGÀY SINH</small>
                                <strong class="text-dark"><?= $admission['date_of_birth'] ? date('d/m/Y', strtotime($admission['date_of_birth'])) : '-' ?></strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">NHÓM MÁU</small>
                                <strong class="text-danger"><?= $admission['blood_type'] ?? '-' ?></strong>
                            </div>
                            <div class="col-12">
                                <small class="text-muted d-block">SỐ ĐIỆN THOẠI</small>
                                <strong class="text-dark"><?= htmlspecialchars($admission['patient_phone'] ?? '-') ?></strong>
                            </div>
                            <div class="col-12">
                                <small class="text-muted d-block">ĐỊA CHỈ</small>
                                <strong class="text-dark" style="font-size: 13px; line-height: 1.4;"><?= htmlspecialchars($admission['patient_address'] ?? '-') ?></strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Hospital Allocation Grid -->
                <div class="col-lg-6">
                    <!-- Bed allocation -->
                    <div class="p-3.5 border rounded-4 mb-3" style="background: #f8fafc;">
                        <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-bed text-success me-2"></i>Bố trí phòng & giường bệnh</h6>
                        <?php if ($admission['status'] === 'pending'): ?>
                            <div class="p-3 bg-warning-subtle text-warning border border-warning-subtle rounded-3 text-center">
                                <i class="fa-solid fa-clock me-1"></i> Bệnh nhân đang chờ xếp phòng và giường bệnh.
                                <?php if (in_array($_SESSION['user']['role'], ['admin', 'nurse', 'receptionist'])): ?>
                                <div class="mt-2">
                                    <a href="index.php?page=inpatient&action=assignBed&id=<?= $admission['id'] ?>" class="btn btn-sm btn-warning fw-bold text-dark" style="border-radius: 8px;">
                                        <i class="fa-solid fa-bed me-1"></i> Xếp giường ngay
                                    </a>
                                </div>
                                <?php endif; ?>
                            </div>
                        <?php else: ?>
                        <div class="row g-3">
                            <div class="col-6">
                                <small class="text-muted d-block">PHÒNG KHÁM/BUỒNG</small>
                                <strong class="text-dark">Phòng <?= $admission['room_number'] ?> (<?= $typeLabels[$admission['room_type']] ?? '' ?>)</strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">GIƯỜNG BỆNH</small>
                                <strong class="text-dark">Giường <?= $admission['bed_number'] ?></strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">KHOA NỘI TRÚ</small>
                                <strong class="text-dark"><?= htmlspecialchars($admission['department_name'] ?? '-') ?></strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">ĐƠN GIÁ GIƯỜNG</small>
                                <strong class="text-dark"><?= number_format($admission['price_per_day'] ?? 0, 0, ',', '.') ?>đ/ngày</strong>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Doctor in charge -->
                    <div class="p-3.5 border rounded-4" style="background: #f8fafc;">
                        <h6 class="fw-bold text-dark mb-3"><i class="fa-solid fa-user-doctor text-info me-2"></i>Đội ngũ y tế phụ trách</h6>
                        <div class="row g-3">
                            <div class="col-6">
                                <small class="text-muted d-block">BÁC SĨ ĐIỀU TRỊ CHÍNH</small>
                                <strong class="text-dark">BS. <?= htmlspecialchars($admission['doctor_name']) ?></strong>
                            </div>
                            <div class="col-6">
                                <small class="text-muted d-block">CHUYÊN KHOA</small>
                                <strong class="text-dark"><?= htmlspecialchars($admission['specialty'] ?? '-') ?></strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Diagnosis & Clinical Notes -->
                <div class="col-md-6 mt-4">
                    <div class="p-4 border rounded-4 bg-white" style="border-color: #e2e8f0 !important; border-left: 5px solid #ef4444 !important; height: 100%; box-shadow: 0 2px 8px rgba(0,0,0,0.01);">
                        <h6 class="fw-bold mb-3 text-dark"><i class="fa-solid fa-stethoscope me-1.5 text-danger"></i>Chẩn đoán y khoa lúc nhập viện:</h6>
                        <p class="mb-0 fw-bold text-dark" style="font-size: 14.5px; line-height: 1.6; white-space: pre-line;"><?= htmlspecialchars($admission['diagnosis'] ?? '-') ?></p>
                    </div>
                </div>

                <div class="col-md-6 mt-4">
                    <div class="p-4 border rounded-4 d-flex flex-column bg-white" style="border-color: #e2e8f0 !important; border-left: 5px solid #0ea5e9 !important; height: 100%; box-shadow: 0 2px 8px rgba(0,0,0,0.01);">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold mb-0 text-dark"><i class="fa-solid fa-note-sticky me-1.5 text-primary"></i>Ghi chú chăm sóc và theo dõi:</h6>
                            <?php if ($admission['status'] === 'active' && in_array($_SESSION['user']['role'], ['admin', 'doctor', 'nurse'])): ?>
                            <button type="button" class="btn btn-sm btn-outline-primary px-3 py-1" data-bs-toggle="modal" data-bs-target="#addNoteModal" style="border-radius: 8px; font-weight: 600; font-size: 12px;">
                                <i class="fa-solid fa-plus me-1"></i>Thêm ghi chú
                            </button>
                            <?php endif; ?>
                        </div>
                        <div class="notes-content flex-grow-1 p-3 bg-white text-secondary rounded-3 border" style="max-height: 200px; overflow-y: auto; font-size: 13.5px; line-height: 1.6;">
                            <?= !empty($admission['notes']) ? nl2br(htmlspecialchars($admission['notes'])) : 'Chưa ghi nhận chỉ định chăm sóc đặc biệt nào.' ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Tab 3: Billing & Discharge -->
    <div class="tab-pane fade" id="cost-pane" role="tabpanel" aria-labelledby="cost-tab">
        <div class="content-card border-0 shadow-sm p-4 bg-white" style="border-radius: 16px;">
            <h6 class="fw-bold mb-4 text-dark"><i class="fa-solid fa-receipt text-primary me-2"></i>Bảng tạm tính chi phí nằm viện nội trú</h6>
            
            <!-- Price blocks -->
            <div class="p-4 mb-4 rounded-4" style="background: linear-gradient(135deg, #f0fdf4 0%, #e6fcf5 100%); border: 1px solid #c3fae8;">
                <div class="row align-items-center g-4">
                    <div class="col-md-4 text-center border-end border-success border-opacity-25">
                        <small class="text-muted d-block font-weight-bold mb-1" style="font-size: 12px; letter-spacing: 0.5px;">SỐ NGÀY ĐÃ NẰM VIỆN</small>
                        <h3 class="m-0 fw-bold text-success" style="font-size: 28px;"><?= $days ?> ngày</h3>
                    </div>
                    <div class="col-md-4 text-center border-end border-success border-opacity-25">
                        <small class="text-muted d-block font-weight-bold mb-1" style="font-size: 12px; letter-spacing: 0.5px;">ĐƠN GIÁ GIƯỜNG / NGÀY</small>
                        <h3 class="m-0 fw-bold text-dark" style="font-size: 24px;"><?= number_format($admission['price_per_day'] ?? 0, 0, ',', '.') ?>đ</h3>
                    </div>
                    <div class="col-md-4 text-center">
                        <small class="text-muted d-block font-weight-bold mb-1" style="font-size: 12px; letter-spacing: 0.5px;">TỔNG PHÍ GIƯỜNG (TẠM TÍNH)</small>
                        <h3 class="m-0 fw-bold text-danger" style="font-size: 28px;"><?= number_format($roomCost, 0, ',', '.') ?>đ</h3>
                    </div>
                </div>
            </div>

            <!-- Discharge confirmation logs if already discharged -->
            <?php if ($admission['discharge_date']): ?>
            <div class="alert alert-success border-0 p-3.5 mb-4 rounded-3 d-flex align-items-center gap-3">
                <div class="d-flex align-items-center justify-content-center bg-success text-white rounded-circle" style="width: 32px; height: 32px;">
                    <i class="fa-solid fa-check"></i>
                </div>
                <div>
                    <h6 class="fw-bold mb-0.5" style="font-size: 14.5px;">Bệnh nhân đã làm thủ tục xuất viện hoàn tất</h6>
                    <small style="font-size: 12.5px;">Thời gian xuất viện chính thức: <strong class="text-dark"><?= date('d/m/Y H:i', strtotime($admission['discharge_date'])) ?></strong></small>
                </div>
            </div>
            <?php endif; ?>

            <!-- Discharge control button for active inpatient admissions -->
            <div class="mt-2">
                <?php if ($admission['status'] === 'active' && in_array($_SESSION['user']['role'], ['admin', 'nurse', 'receptionist', 'doctor'])): ?>
                <div class="p-3 border rounded-3 bg-light d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3">
                    <div>
                        <h6 class="fw-bold text-dark mb-1" style="font-size: 14px;">Thao tác chỉ định Xuất viện</h6>
                        <small class="text-muted">Nhấn nút bên dưới để hoàn tất bệnh án nội trú này và trả tự do cho giường bệnh.</small>
                    </div>
                    <a href="javascript:void(0)" class="btn btn-success px-4 py-2" style="border-radius:10px; font-weight:600; font-size:13.5px; background-color: #10b981; border-color: #10b981; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.15);" onclick="if(confirm('Bạn có chắc chắn muốn làm thủ tục xuất viện cho bệnh nhân này?')) postAction('index.php?page=inpatient&action=discharge&id=<?= $admission['id'] ?>')">
                        <i class="fa-solid fa-right-from-bracket me-1.5"></i> Xác nhận Xuất viện
                    </a>
                </div>
                <?php else: ?>
                <p class="text-muted small"><i class="fa-solid fa-circle-info me-1"></i>Chức năng xuất viện chỉ khả dụng cho ca bệnh nhân đang điều trị nội trú tích cực và được thực hiện bởi quản trị viên, lễ tân hoặc điều dưỡng.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal Thêm ghi chú chăm sóc -->
<div class="modal fade" id="addNoteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.1);">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold text-dark"><i class="fa-solid fa-plus-circle text-primary me-2"></i>Thêm ghi chú chăm sóc lâm sàng</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?page=inpatient&action=updateNotes" method="POST">
                <div class="modal-body">
                    <?php echo Security::csrfField(); ?>
                    <input type="hidden" name="id" value="<?= $admission['id'] ?>">
                    <div class="mb-3">
                        <label class="form-label text-muted" style="font-size: 13px;">Nội dung chỉ định hoặc diễn biến bệnh lý hàng ngày</label>
                        <textarea name="notes" class="form-control" rows="5" required placeholder="Nhập ghi chú sinh hiệu, chỉ định truyền dịch, tiêm thuốc, theo dõi diễn biến..." style="border-radius: 10px; font-size:13.5px;"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 10px; font-weight: 600; font-size:13px;">Hủy bỏ</button>
                    <button type="submit" class="btn btn-primary" style="border-radius: 10px; font-weight: 600; font-size:13px;">Lưu ghi chú</button>
                </div>
            </form>
        </div>
    </div>
</div>
