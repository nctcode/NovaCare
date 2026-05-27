<!-- Form Ghi nhận Chăm sóc Điều dưỡng -->
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

<div class="content-card" style="max-width: 800px;">
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

        <form method="POST" action="index.php?page=inpatient&action=saveNursingRecord">
            <?= Security::csrfField() ?>
            <input type="hidden" name="admission_id" value="<?= $admission['id'] ?>">

            <!-- Dấu hiệu sinh tồn -->
            <h6 class="mb-3 pb-2 border-bottom"><i class="fa-solid fa-thermometer-half me-2"></i>Dấu hiệu sinh tồn</h6>
            <div class="row g-3 mb-4">
                <div class="col-md-4 col-6">
                    <label class="form-label fw-semibold">Nhiệt độ (°C)</label>
                    <input type="number" name="temperature" class="form-control" step="0.1" min="34" max="43" placeholder="36.5">
                </div>
                <div class="col-md-4 col-6">
                    <label class="form-label fw-semibold">HA Tâm thu (mmHg)</label>
                    <input type="number" name="blood_pressure_sys" class="form-control" min="60" max="250" placeholder="120">
                </div>
                <div class="col-md-4 col-6">
                    <label class="form-label fw-semibold">HA Tâm trương (mmHg)</label>
                    <input type="number" name="blood_pressure_dia" class="form-control" min="40" max="150" placeholder="80">
                </div>
                <div class="col-md-4 col-6">
                    <label class="form-label fw-semibold">Nhịp tim (bpm)</label>
                    <input type="number" name="heart_rate" class="form-control" min="30" max="250" placeholder="72">
                </div>
                <div class="col-md-4 col-6">
                    <label class="form-label fw-semibold">Nhịp thở (/phút)</label>
                    <input type="number" name="respiratory_rate" class="form-control" min="8" max="50" placeholder="18">
                </div>
                <div class="col-md-4 col-6">
                    <label class="form-label fw-semibold">SpO2 (%)</label>
                    <input type="number" name="spo2" class="form-control" min="50" max="100" placeholder="98">
                </div>
            </div>

            <!-- Chăm sóc -->
            <h6 class="mb-3 pb-2 border-bottom"><i class="fa-solid fa-notes-medical me-2"></i>Ghi nhận chăm sóc</h6>
            <div class="mb-3">
                <label class="form-label fw-semibold">Ghi chú chăm sóc</label>
                <textarea name="care_notes" class="form-control" rows="3" placeholder="Tình trạng bệnh nhân, các can thiệp đã thực hiện..."></textarea>
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
