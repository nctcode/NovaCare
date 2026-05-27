<!-- Form Thêm Vật tư y tế -->
<div class="form-section p-4 shadow-sm border-0 bg-white" style="border-radius: 16px;" data-aos="fade-up">
    <div class="d-flex align-items-center gap-3 mb-4">
        <div class="d-flex align-items-center justify-content-center text-primary" style="width: 48px; height: 48px; border-radius: 12px; background: rgba(59, 130, 246, 0.08); font-size: 20px;">
            <i class="fa-solid fa-toolbox"></i>
        </div>
        <div>
            <h5 class="m-0 fw-bold text-dark">Thêm Vật tư & Thiết bị Y tế mới</h5>
            <small class="text-muted">Nhập thông tin chi tiết của vật tư để đưa vào kho lưu trữ</small>
        </div>
    </div>

    <form method="POST" action="index.php?page=equipment&action=store">
        <?php echo Security::csrfField(); ?>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="equipment_name" class="form-label fw-semibold text-secondary" style="font-size: 13px;">Tên vật tư / Thiết bị <span class="text-danger">*</span></label>
                <input type="text" class="form-control py-2.5" id="equipment_name" name="equipment_name" required placeholder="Ví dụ: Máy đo huyết áp, Bông băng y tế, Kim tiêm..." style="border-radius: 10px;">
            </div>
            <div class="col-md-6 mb-3">
                <label for="quantity" class="form-label fw-semibold text-secondary" style="font-size: 13px;">Số lượng nhập kho <span class="text-danger">*</span></label>
                <input type="number" class="form-control py-2.5" id="quantity" name="quantity" required min="0" value="1" style="border-radius: 10px;">
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 mb-3">
                <label for="status" class="form-label fw-semibold text-secondary" style="font-size: 13px;">Trạng thái ban đầu</label>
                <select class="form-select py-2.5" id="status" name="status" style="border-radius: 10px;">
                    <option value="available">Sẵn sàng đưa vào sử dụng</option>
                    <option value="maintenance">Đang bảo trì / Chưa sẵn sàng</option>
                </select>
            </div>
        </div>

        <div class="row">
            <div class="col-md-12 mb-4">
                <label for="description" class="form-label fw-semibold text-secondary" style="font-size: 13px;">Mô tả chi tiết / Ghi chú kỹ thuật</label>
                <textarea class="form-control" id="description" name="description" rows="4" placeholder="Nhập thông tin xuất xứ, hãng sản xuất, hạn sử dụng hoặc ghi chú hướng dẫn..." style="border-radius: 10px;"></textarea>
            </div>
        </div>

        <div class="d-flex gap-3 mt-2">
            <button type="submit" class="btn-submit px-4 py-2.5 d-inline-flex align-items-center gap-2" style="border-radius: 10px; font-weight: 600;">
                <i class="fa-solid fa-save"></i>
                Lưu thông tin
            </button>
            <a href="index.php?page=equipment" class="btn-cancel px-4 py-2.5 d-inline-flex align-items-center gap-2" style="border-radius: 10px; font-weight: 600;">
                <i class="fa-solid fa-arrow-left"></i>
                Quay lại
            </a>
        </div>
    </form>
</div>
