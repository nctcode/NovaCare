<!-- Admit Patient -->
<div class="content-card" data-aos="fade-up" style="border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.03);">
    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
        <?php if ($_SESSION['user']['role'] === 'doctor'): ?>
            <h5 class="m-0" style="font-weight:700;"><i class="fa-solid fa-file-medical me-2 text-primary"></i>Chỉ định nhập viện bệnh nhân</h5>
            <p class="text-muted" style="font-size:13px; margin:5px 0 0 0;">Chọn bệnh nhân, bác sĩ phụ trách và thời gian chỉ định</p>
        <?php else: ?>
            <h5 class="m-0" style="font-weight:700;"><i class="fa-solid fa-hospital-user me-2 text-primary"></i>Nhập viện bệnh nhân</h5>
            <p class="text-muted" style="font-size:13px; margin:5px 0 0 0;">Chọn bệnh nhân, bác sĩ phụ trách và phòng-giường</p>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <form method="POST" action="index.php?page=inpatient&action=storeAdmit">
                            <?php echo Security::csrfField(); ?>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Bệnh nhân <span class="text-danger">*</span></label>
                    <select name="patient_id" class="form-select" required>
                        <option value="">-- Chọn bệnh nhân --</option>
                        <?php foreach ($patients as $p): ?>
                            <option value="<?= $p['id'] ?>" <?= (isset($preselectedPatientId) && $preselectedPatientId == $p['id']) ? 'selected' : '' ?>><?= htmlspecialchars($p['name']) ?> (<?= $p['phone'] ?? '' ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Bác sĩ phụ trách <span class="text-danger">*</span></label>
                    <select name="doctor_id" class="form-select" required>
                        <option value="">-- Chọn bác sĩ --</option>
                        <?php foreach ($doctors as $d): ?>
                            <option value="<?= $d['id'] ?>" <?= (isset($currentDoctorId) && $currentDoctorId == $d['id']) ? 'selected' : '' ?>>BS. <?= htmlspecialchars($d['name']) ?> (<?= $d['department_name'] ?? '' ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
            </div>

            <?php if ($_SESSION['user']['role'] !== 'doctor'): ?>
            <div class="row g-3 mb-4">
                <div class="col-md-6">
                    <label class="form-label fw-bold">Phòng - Giường <span class="text-danger">*</span></label>
                    <select name="bed_id" class="form-select" required>
                        <option value="">-- Chọn giường --</option>
                        <?php foreach ($beds as $b): ?>
                            <option value="<?= $b['id'] ?>">
                                Phòng <?= $b['room_number'] ?> (<?= ucfirst($b['room_type']) ?>) - Giường <?= $b['bed_number'] ?> — <?= number_format($b['price_per_day'], 0, ',', '.') ?>đ/ngày
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-bold">Ngày nhập viện</label>
                    <input type="datetime-local" name="admission_date" class="form-control" value="<?= date('Y-m-d\TH:i') ?>">
                </div>
            </div>
            <?php else: ?>
            <div class="row g-3 mb-4">
                <div class="col-md-12">
                    <label class="form-label fw-bold">Ngày chỉ định nhập viện</label>
                    <input type="datetime-local" name="admission_date" class="form-control" value="<?= date('Y-m-d\TH:i') ?>">
                </div>
            </div>
            <?php endif; ?>

            <div class="mb-3">
                <label class="form-label fw-bold">Chẩn đoán ban đầu <span class="text-danger">*</span></label>
                <textarea name="diagnosis" class="form-control" rows="3" required placeholder="Nhập chẩn đoán ban đầu..."></textarea>
            </div>

            <div class="mb-4">
                <label class="form-label fw-bold">Ghi chú</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="Ghi chú thêm (nếu có)..."></textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary" style="border-radius:20px; font-weight:500; padding:10px 30px;">
                    <?php if ($_SESSION['user']['role'] === 'doctor'): ?>
                        <i class="fa-solid fa-file-medical me-2"></i>Xác nhận chỉ định
                    <?php else: ?>
                        <i class="fa-solid fa-hospital-user me-2"></i>Xác nhận nhập viện
                    <?php endif; ?>
                </button>
                <a href="index.php?page=inpatient" class="btn btn-outline-secondary" style="border-radius:20px; font-weight:500; padding:10px 30px;">
                    <i class="fa-solid fa-arrow-left me-2"></i>Quay lại
                </a>
            </div>
        </form>
    </div>
</div>
