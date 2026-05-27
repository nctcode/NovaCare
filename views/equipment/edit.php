<!-- Form Sửa Vật tư y tế -->
<div class="form-section p-4 shadow-sm border-0 bg-white" style="border-radius: 16px;" data-aos="fade-up">
    <div class="d-flex align-items-center gap-3 mb-4">
        <div class="d-flex align-items-center justify-content-center text-primary" style="width: 48px; height: 48px; border-radius: 12px; background: rgba(59, 130, 246, 0.08); font-size: 20px;">
            <i class="fa-solid fa-pen-to-square"></i>
        </div>
        <div>
            <h5 class="m-0 fw-bold text-dark">Chỉnh sửa thông tin Vật tư Y tế</h5>
            <small class="text-muted">Cập nhật số lượng, trạng thái hoặc thông số kỹ thuật</small>
        </div>
    </div>

    <form method="POST" action="index.php?page=equipment&action=update">
        <?php echo Security::csrfField(); ?>
        <input type="hidden" name="id" value="<?= $equipment['id'] ?>">
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="equipment_name" class="form-label fw-semibold text-secondary" style="font-size: 13px;">Tên vật tư / Thiết bị <span class="text-danger">*</span></label>
                <input type="text" class="form-control py-2.5" id="equipment_name" name="equipment_name" required value="<?= htmlspecialchars($equipment['equipment_name']) ?>" style="border-radius: 10px;">
            </div>
            <div class="col-md-6 mb-3">
                <label for="quantity" class="form-label fw-semibold text-secondary" style="font-size: 13px;">Số lượng nhập kho <span class="text-danger">*</span></label>
                <input type="number" class="form-control py-2.5" id="quantity" name="quantity" required min="0" value="<?= $equipment['quantity'] ?>" style="border-radius: 10px;">
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 mb-3">
                <label for="status" class="form-label fw-semibold text-secondary" style="font-size: 13px;">Trạng thái hiện tại</label>
                <select class="form-select py-2.5" id="status" name="status" style="border-radius: 10px;">
                    <option value="available" <?= $equipment['status'] == 'available' ? 'selected' : '' ?>>Sẵn sàng đưa vào sử dụng</option>
                    <option value="maintenance" <?= $equipment['status'] == 'maintenance' ? 'selected' : '' ?>>Đang bảo trì / Chưa sẵn sàng</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 mb-4">
                <label for="description" class="form-label fw-semibold text-secondary" style="font-size: 13px;">Mô tả chi tiết / Ghi chú kỹ thuật</label>
                <textarea class="form-control" id="description" name="description" rows="4" style="border-radius: 10px;"><?= htmlspecialchars($equipment['description'] ?? '') ?></textarea>
            </div>
        </div>

        <div class="d-flex gap-3 mt-2">
            <button type="submit" class="btn-submit px-4 py-2.5 d-inline-flex align-items-center gap-2" style="border-radius: 10px; font-weight: 600;">
                <i class="fa-solid fa-save"></i>
                Cập nhật thay đổi
            </button>
            <a href="index.php?page=equipment" class="btn-cancel px-4 py-2.5 d-inline-flex align-items-center gap-2" style="border-radius: 10px; font-weight: 600;">
                <i class="fa-solid fa-arrow-left"></i>
                Quay lại
            </a>
        </div>
    </form>
</div>
