<!-- Form Thêm Vật tư y tế -->
<div class="form-section" data-aos="fade-up">
    <h5 class="mb-4"><i class="fa-solid fa-bed-pulse me-2"></i>Thêm Vật tư y tế</h5>
    <form method="POST" action="index.php?page=equipment&action=store">
                            <?php echo Security::csrfField(); ?>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="equipment_name" class="form-label">Tên vật tư <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="equipment_name" name="equipment_name" required placeholder="Bơm kim tiêm, bông băng...">
            </div>
            <div class="col-md-6 mb-3">
                <label for="quantity" class="form-label">Số lượng <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="quantity" name="quantity" required min="0" value="1">
            </div>
        </div>
        <div class="mb-3">
            <label for="status" class="form-label">Trạng thái</label>
            <select class="form-select" id="status" name="status">
                <option value="available">Sẵn sàng sử dụng</option>
                <option value="maintenance">Đang bảo trì / Hư hỏng</option>
            </select>
        </div>
        <div class="mb-4">
            <label for="description" class="form-label">Mô tả / Ghi chú</label>
            <textarea class="form-control" id="description" name="description" rows="4" placeholder="Thông tin thêm..."></textarea>
        </div>
        <div class="d-flex gap-3">
            <button type="submit" class="btn-submit"><i class="fa-solid fa-save me-2"></i>Lưu vật tư</button>
            <a href="index.php?page=equipment" class="btn-cancel"><i class="fa-solid fa-arrow-left me-2"></i>Quay lại</a>
        </div>
    </form>
</div>

