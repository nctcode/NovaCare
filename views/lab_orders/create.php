<!-- Form Tạo Chỉ định CLS -->
<div class="content-card" style="max-width: 700px;">
    <div class="card-header">
        <h5><i class="fa-solid fa-flask-vial me-2"></i>Tạo Chỉ định Cận lâm sàng</h5>
        <a href="index.php?page=lab-orders" class="btn-action btn-edit">
            <i class="fa-solid fa-arrow-left"></i> Quay lại
        </a>
    </div>
    <div class="card-body">
        <form method="POST" action="index.php?page=lab-orders&action=store">
            <?= Security::csrfField() ?>

            <div class="mb-3">
                <label class="form-label fw-semibold">Bệnh nhân <span class="text-danger">*</span></label>
                <select name="patient_id" class="form-select" required>
                    <option value="">-- Chọn bệnh nhân --</option>
                    <?php foreach ($patients as $p): ?>
                    <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?> (<?= htmlspecialchars($p['phone'] ?? '') ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Loại chỉ định <span class="text-danger">*</span></label>
                    <select name="order_type" class="form-select" required>
                        <option value="lab_test">🧪 Xét nghiệm</option>
                        <option value="imaging">📷 Chẩn đoán hình ảnh</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Mức ưu tiên</label>
                    <select name="priority" class="form-select">
                        <option value="normal">Bình thường</option>
                        <option value="urgent">🔴 Cấp cứu</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Tên xét nghiệm / Chụp chiếu <span class="text-danger">*</span></label>
                <input type="text" name="test_name" class="form-control" placeholder="VD: Xét nghiệm công thức máu, X-quang phổi..." required>
                <div class="form-text">Nhập tên cụ thể của xét nghiệm hoặc phương pháp chẩn đoán hình ảnh</div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Ghi chú lâm sàng</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Ghi chú thêm cho KTV (triệu chứng, yêu cầu đặc biệt...)"></textarea>
            </div>

            <input type="hidden" name="appointment_id" value="<?= $_GET['appointment_id'] ?? '' ?>">

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="index.php?page=lab-orders" class="btn btn-outline-secondary px-4" style="border-radius:20px">Hủy</a>
                <button type="submit" class="btn btn-primary px-4" style="border-radius:20px">
                    <i class="fa-solid fa-paper-plane me-2"></i>Gửi chỉ định
                </button>
            </div>
        </form>
    </div>
</div>
