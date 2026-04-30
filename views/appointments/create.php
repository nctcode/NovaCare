<!-- Form Đặt lịch khám -->
<div class="form-section">
    <h5 class="mb-4"><i class="bi bi-calendar-plus me-2"></i>Đặt lịch khám mới</h5>

    <form method="POST" action="index.php?page=appointments&action=store" id="createAppointmentForm">
                            <?php echo Security::csrfField(); ?>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="doctor_id" class="form-label">Chọn Bác sĩ <span class="text-danger">*</span></label>
                <select class="form-select" id="doctor_id" name="doctor_id" required>
                    <option value="">-- Chọn bác sĩ --</option>
                    <?php foreach ($doctors as $d): ?>
                        <option value="<?= $d['id'] ?>">
                            <?= htmlspecialchars($d['name']) ?> - <?= htmlspecialchars($d['specialty'] ?? 'N/A') ?> 
                            (<?= htmlspecialchars($d['department_name'] ?? '') ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="appointment_date" class="form-label">Ngày giờ khám <span class="text-danger">*</span></label>
                <input type="datetime-local" class="form-control" id="appointment_date" name="appointment_date" required>
            </div>
        </div>

        <div class="mb-4">
            <label for="reason" class="form-label">Lý do khám <span class="text-danger">*</span></label>
            <textarea class="form-control" id="reason" name="reason" rows="3" required 
                      placeholder="Mô tả triệu chứng hoặc lý do khám bệnh..."></textarea>
        </div>

        <div class="d-flex gap-3">
            <button type="submit" class="btn-submit">
                <i class="bi bi-check-lg"></i> Đặt lịch
            </button>
            <a href="index.php?page=appointments" class="btn-cancel">
                <i class="bi bi-x-lg"></i> Hủy
            </a>
        </div>
    </form>
</div>

