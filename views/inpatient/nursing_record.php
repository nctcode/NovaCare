<!-- Form Ghi nhận Chăm sóc Điều dưỡng + AI Vitals Alert 4.0 -->
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

<div class="content-card" style="max-width: 800px; margin: 0 auto;">
    <div class="card-header">
        <h5><i class="fa-solid fa-heart-pulse me-2"></i>Ghi nhận Chăm sóc Điều dưỡng</h5>
        <a href="index.php?page=inpatient&action=detail&id=<?= $admission['id'] ?>" class="btn-action btn-edit">
            <i class="fa-solid fa-arrow-left"></i> Quay lại
        </a>
    </div>
    <div class="card-body">
        <!-- Thông tin BN -->
        <div class="row mb-4 p-3 rounded" style="background: var(--bg-secondary, #f8f9fa);">
            <div class="col-md-6">
                <p class="mb-1"><strong>Bệnh nhân:</strong> <?= htmlspecialchars($admission['patient_name'] ?? 'N/A') ?></p>
                <p class="mb-0"><strong>Phòng:</strong> <?= htmlspecialchars($admission['room_number'] ?? 'N/A') ?> — Giường: <?= htmlspecialchars($admission['bed_number'] ?? 'N/A') ?></p>
            </div>
            <div class="col-md-6">
                <p class="mb-1"><strong>Ngày nhập viện:</strong> <?= !empty($admission['admission_date']) ? date('d/m/Y', strtotime($admission['admission_date'])) : 'N/A' ?></p>
                <p class="mb-0"><strong>Chẩn đoán:</strong> <?= htmlspecialchars($admission['diagnosis'] ?? 'N/A') ?></p>
            </div>
        </div>

        <form method="POST" action="index.php?page=inpatient&action=saveNursingRecord" id="formNursingRecord">
            <?= Security::csrfField() ?>
            <input type="hidden" name="admission_id" id="admission_id" value="<?= $admission['id'] ?>">

            <!-- Dấu hiệu sinh tồn -->
            <h6 class="mb-3 pb-2 border-bottom d-flex align-items-center justify-content-between">
                <span><i class="fa-solid fa-thermometer-half me-2"></i>Dấu hiệu sinh tồn</span>
                <button type="button" class="btn btn-sm btn-outline-primary" id="btnAiAnalyzeVitals" style="border-radius:15px; font-size:12px; font-weight:600;">
                    <i class="fa-solid fa-brain me-1"></i> AI Phân tích Sinh hiệu
                </button>
            </h6>

            <!-- AI Vitals Alert Result Section -->
            <div id="aiVitalsAlertBox" class="mb-4 p-3 rounded-3" style="display:none; background: linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%); border: 1px solid #a7f3d0;">
                <div class="d-flex align-items-center mb-2 justify-content-between">
                    <div class="d-flex align-items-center gap-2">
                        <span class="badge" id="aiVitalsAlertLevelBadge">Alert Level</span>
                        <span class="badge" id="aiVitalsTrendBadge">Trend</span>
                        <strong class="text-success"><i class="fa-solid fa-robot"></i> Trợ lý AI Cảnh báo</strong>
                    </div>
                    <button type="button" class="btn-close" id="btnMapCloseVitals" style="font-size:10px;"></button>
                </div>
                <div class="small mb-2" id="aiVitalsAbnormalWrap" style="display:none;">
                    <span class="text-danger fw-bold"><i class="fa-solid fa-triangle-exclamation me-1"></i>Chỉ số bất thường:</span>
                    <span id="aiVitalsAbnormalText" class="ms-1"></span>
                </div>
                <div class="small mb-2">
                    <span class="fw-bold"><i class="fa-solid fa-chart-line me-1"></i>Phân tích xu hướng:</span>
                    <span id="aiVitalsAnalysisText"></span>
                </div>
                <div class="small mb-2">
                    <span class="fw-bold text-primary"><i class="fa-solid fa-hand-holding-medical me-1"></i>Khuyến nghị điều trị/chăm sóc:</span>
                    <span id="aiVitalsRecommendationText"></span>
                </div>
                <button type="button" class="btn btn-xs btn-outline-success mt-2 py-1 px-2" id="btnApplyAiVitalsNotes" style="font-size:11px; border-radius:10px;">
                    <i class="fa-solid fa-copy me-1"></i>Chép khuyến nghị vào Ghi chú chăm sóc
                </button>
            </div>

            <div class="row g-3 mb-4">
                <div class="col-md-4 col-6">
                    <label class="form-label fw-semibold">Nhiệt độ (°C)</label>
                    <input type="number" name="temperature" id="vital_temp" class="form-control" step="0.1" min="34" max="43" placeholder="36.5">
                </div>
                <div class="col-md-4 col-6">
                    <label class="form-label fw-semibold">HA Tâm thu (mmHg)</label>
                    <input type="number" name="blood_pressure_sys" id="vital_bp_sys" class="form-control" min="60" max="250" placeholder="120">
                </div>
                <div class="col-md-4 col-6">
                    <label class="form-label fw-semibold">HA Tâm trương (mmHg)</label>
                    <input type="number" name="blood_pressure_dia" id="vital_bp_dia" class="form-control" min="40" max="150" placeholder="80">
                </div>
                <div class="col-md-4 col-6">
                    <label class="form-label fw-semibold">Nhịp tim (bpm)</label>
                    <input type="number" name="heart_rate" id="vital_hr" class="form-control" min="30" max="250" placeholder="72">
                </div>
                <div class="col-md-4 col-6">
                    <label class="form-label fw-semibold">Nhịp thở (/phút)</label>
                    <input type="number" name="respiratory_rate" id="vital_rr" class="form-control" min="8" max="50" placeholder="18">
                </div>
                <div class="col-md-4 col-6">
                    <label class="form-label fw-semibold">SpO2 (%)</label>
                    <input type="number" name="spo2" id="vital_spo2" class="form-control" min="50" max="100" placeholder="98">
                </div>
            </div>

            <!-- Chăm sóc -->
            <h6 class="mb-3 pb-2 border-bottom"><i class="fa-solid fa-notes-medical me-2"></i>Ghi nhận chăm sóc</h6>
            <div class="mb-3">
                <label class="form-label fw-semibold">Ghi chú chăm sóc</label>
                <textarea name="care_notes" id="care_notes" class="form-control" rows="3" placeholder="Tình trạng bệnh nhân, các can thiệp đã thực hiện..."></textarea>
            </div>
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Thuốc đã cho</label>
                    <textarea name="medication_given" class="form-control" rows="2" placeholder="Liệt kê thuốc đã cho bệnh nhân..."></textarea>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Chế độ ăn</label>
                    <input type="text" name="diet_notes" class="form-control" placeholder="VD: Ăn mềm, kiêng mặn...">
                </div>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="index.php?page=inpatient&action=detail&id=<?= $admission['id'] ?>" class="btn btn-outline-secondary px-4" style="border-radius:20px">Hủy</a>
                <button type="submit" class="btn btn-primary px-4" style="border-radius:20px">
                    <i class="fa-solid fa-save me-2"></i>Lưu hồ sơ
                </button>
            </div>
        </form>
    </div>
</div>

<!-- AJAX AI Vitals Script -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnAiAnalyze = document.getElementById('btnAiAnalyzeVitals');
    const alertBox = document.getElementById('aiVitalsAlertBox');
    const btnCloseAlert = document.getElementById('btnMapCloseVitals');
    const btnApplyNotes = document.getElementById('btnApplyAiVitalsNotes');
    
    // Close alert box
    btnCloseAlert.addEventListener('click', function() {
        alertBox.style.display = 'none';
    });

    btnAiAnalyze.addEventListener('click', async function() {
        const admissionId = document.getElementById('admission_id').value;
        
        // Gồm các chỉ số sinh hiệu
        const temp = document.getElementById('vital_temp').value;
        const bp_sys = document.getElementById('vital_bp_sys').value;
        const bp_dia = document.getElementById('vital_bp_dia').value;
        const hr = document.getElementById('vital_hr').value;
        const rr = document.getElementById('vital_rr').value;
        const spo2 = document.getElementById('vital_spo2').value;

        if (!temp && !bp_sys && !bp_dia && !hr && !rr && !spo2) {
            alert('Vui lòng nhập ít nhất một chỉ số sinh hiệu để phân tích.');
            return;
        }

        btnAiAnalyze.disabled = true;
        btnAiAnalyze.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-1"></i> Đang phân tích...';
        alertBox.style.display = 'none';

        try {
            const response = await fetch('index.php?page=inpatient&action=aiVitalsAlert', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({
                    admission_id: admissionId,
                    temperature: temp ? parseFloat(temp) : null,
                    blood_pressure_sys: bp_sys ? parseInt(bp_sys) : null,
                    blood_pressure_dia: bp_dia ? parseInt(bp_dia) : null,
                    heart_rate: hr ? parseInt(hr) : null,
                    respiratory_rate: rr ? parseInt(rr) : null,
                    spo2: spo2 ? parseInt(spo2) : null
                })
            });

            const res = await response.json();

            if (res.success && res.data) {
                const d = res.data;
                
                // Set text
                document.getElementById('aiVitalsAnalysisText').textContent = d.analysis || 'Không có phân tích.';
                document.getElementById('aiVitalsRecommendationText').textContent = d.recommendation || 'Không có khuyến nghị.';
                
                // Abnormal items
                const abnormalWrap = document.getElementById('aiVitalsAbnormalWrap');
                if (d.abnormal_items && d.abnormal_items.length > 0) {
                    abnormalWrap.style.display = 'block';
                    document.getElementById('aiVitalsAbnormalText').textContent = d.abnormal_items.join(', ');
                } else {
                    abnormalWrap.style.display = 'none';
                }

                // Alert Level Badge
                const levelBadge = document.getElementById('aiVitalsAlertLevelBadge');
                const levelColors = {
                    normal: { text: 'Bình thường', bg: '#10b981' },
                    warning: { text: 'Cảnh báo', bg: '#f59e0b' },
                    danger: { text: 'Nguy hiểm', bg: '#ef4444' },
                    critical: { text: 'Nguy cấp', bg: '#dc2626' }
                };
                const lvl = levelColors[d.alert_level] || { text: d.alert_level, bg: '#6b7280' };
                levelBadge.textContent = lvl.text;
                levelBadge.style.backgroundColor = lvl.bg;
                levelBadge.style.color = '#fff';

                // Trend Badge
                const trendBadge = document.getElementById('aiVitalsTrendBadge');
                const trendColors = {
                    improving: { text: 'Đang tiến triển tốt', bg: '#3b82f6' },
                    stable: { text: 'Ổn định', bg: '#10b981' },
                    deteriorating: { text: 'Đang xấu đi ⚠️', bg: '#dc2626' }
                };
                const trd = trendColors[d.trend] || { text: d.trend, bg: '#6b7280' };
                trendBadge.textContent = trd.text;
                trendBadge.style.backgroundColor = trd.bg;
                trendBadge.style.color = '#fff';

                // Thay đổi background của alertBox dựa theo mức độ nguy hiểm
                if (d.alert_level === 'danger' || d.alert_level === 'critical') {
                    alertBox.style.background = 'linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%)';
                    alertBox.style.borderColor = '#fca5a5';
                } else if (d.alert_level === 'warning') {
                    alertBox.style.background = 'linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%)';
                    alertBox.style.borderColor = '#fcd34d';
                } else {
                    alertBox.style.background = 'linear-gradient(135deg, #f0fdf4 0%, #ecfdf5 100%)';
                    alertBox.style.borderColor = '#a7f3d0';
                }

                alertBox.style.display = 'block';
                
                // Store recommendation data for copy action
                btnApplyNotes._recommendation = d.recommendation;
            } else {
                alert('Có lỗi xảy ra: ' + (res.error || 'Lỗi không xác định'));
            }
        } catch (error) {
            console.error(error);
            alert('Lỗi kết nối máy chủ AI.');
        }

        btnAiAnalyze.disabled = false;
        btnAiAnalyze.innerHTML = '<i class="fa-solid fa-brain me-1"></i> AI Phân tích Sinh hiệu';
    });

    // Copy recommendation to care notes
    btnApplyNotes.addEventListener('click', function() {
        const rec = btnApplyNotes._recommendation;
        if (!rec) return;

        const careNotesTextarea = document.getElementById('care_notes');
        let currentText = careNotesTextarea.value.trim();
        if (currentText) {
            currentText += '\n\n';
        }
        currentText += '[AI Khuyến nghị]: ' + rec;
        careNotesTextarea.value = currentText;

        // Visual success effect
        btnApplyNotes.innerHTML = '<i class="fa-solid fa-check me-1"></i> Đã sao chép!';
        btnApplyNotes.classList.remove('btn-outline-success');
        btnApplyNotes.classList.add('btn-success');
        setTimeout(() => {
            btnApplyNotes.innerHTML = '<i class="fa-solid fa-copy me-1"></i> Chép khuyến nghị vào Ghi chú chăm sóc';
            btnApplyNotes.classList.remove('btn-success');
            btnApplyNotes.classList.add('btn-outline-success');
        }, 2000);
    });
});
</script>
