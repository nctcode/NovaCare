<!-- Form Tạo Hồ sơ bệnh án -->
<div class="form-section form-card" data-aos="fade-up" style="max-width:800px; margin:0 auto; padding:30px; border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.03);">
    <h5 class="mb-4" style="color:var(--primary); font-weight:700;"><i class="fa-solid fa-file-medical me-2"></i>Tạo Hồ sơ Cấp Bệnh án</h5>
    
    <form method="POST" action="index.php?page=records&action=store">
                            <?php echo Security::csrfField(); ?>
        <div class="row">
            <?php if ($user['role'] === 'admin'): ?>
            <div class="col-md-12 mb-3">
                <label for="doctor_id" class="form-label fw-bold">Bác sĩ khám <span class="text-danger">*</span></label>
                <select class="form-select border-2" id="doctor_id" name="doctor_id" required>
                    <option value="">-- Chọn bác sĩ --</option>
                    <?php 
                    require_once __DIR__ . '/../../models/Doctor.php';
                    $doctorList = (new Doctor())->getAll();
                    foreach ($doctorList as $doc): ?>
                        <option value="<?= $doc['id'] ?>">BS. <?= htmlspecialchars($doc['name']) ?> (<?= htmlspecialchars($doc['specialty']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <div class="col-md-6 mb-3">
                <label for="patient_id" class="form-label fw-bold">Bệnh nhân <span class="text-danger">*</span></label>
                <select class="form-select border-2" id="patient_id" name="patient_id" required>
                    <option value="">-- Chọn bệnh nhân --</option>
                    <?php foreach ($patients as $pat): ?>
                        <option value="<?= $pat['id'] ?>"><?= htmlspecialchars($pat['name']) ?> (<?= htmlspecialchars($pat['phone']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="appointment_id" class="form-label fw-bold">Lịch hẹn liên kết (Tùy chọn)</label>
                <select class="form-select border-2" id="appointment_id" name="appointment_id">
                    <option value="">-- Không liên kết --</option>
                    <?php foreach ($appointments as $apt): 
                        if ($apt['status'] === 'pending' || $apt['status'] === 'confirmed'):
                    ?>
                        <option value="<?= $apt['id'] ?>"><?= date('d/m/Y H:i', strtotime($apt['appointment_date'])) ?> - <?= htmlspecialchars($apt['patient_name']) ?></option>
                    <?php endif; endforeach; ?>
                </select>
                <small class="text-muted"><i class="fa-solid fa-circle-info mt-1"></i> Sẽ tự động đánh dấu "Hoàn thành" lịch hẹn</small>
            </div>
        </div>

        <div class="mb-3 mt-2">
            <label for="diagnosis" class="form-label fw-bold text-danger">Chẩn đoán bệnh <span class="text-danger">*</span></label>
            <textarea class="form-control border-danger border-opacity-50" id="diagnosis" name="diagnosis" rows="2" required placeholder="Ghi rõ tình trạng, triệu chứng, bệnh lý..." style="background:#fff5f5;"></textarea>
        </div>

        <div class="mb-3">
            <label for="treatment" class="form-label fw-bold text-success">Hướng điều trị / Kê đơn thuốc</label>
            <textarea class="form-control border-success border-opacity-50" id="treatment" name="treatment" rows="4" placeholder="Phương pháp điều trị, chỉ định thuốc, phác đồ..." style="background:#f0fdf4;"></textarea>
        </div>

        <div class="mb-4">
            <label for="notes" class="form-label fw-bold">Ghi chú thêm & Lời dặn</label>
            <textarea class="form-control border-2" id="notes" name="notes" rows="2" placeholder="Tái khám sau bao nhiêu ngày, chế độ ăn kiêng..."></textarea>
        </div>

        <div class="d-flex gap-3 justify-content-end border-top pt-4">
            <a href="index.php?page=records" class="btn btn-outline-secondary" style="border-radius:20px; padding:10px 25px;"><i class="fa-solid fa-xmark me-2"></i>Hủy bỏ</a>
            <button type="submit" class="btn btn-primary" style="border-radius:20px; padding:10px 25px;"><i class="fa-solid fa-save me-2"></i>Lưu Bệnh án</button>
        </div>
    </form>
</div>

