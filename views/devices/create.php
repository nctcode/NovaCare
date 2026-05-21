<!-- Form Thêm Thiết bị Y tế -->
<div class="form-section">
    <h5 class="mb-4"><i class="bi bi-cpu me-2"></i>Thêm Thiết bị Y tế</h5>

    <form method="POST" action="index.php?page=devices&action=store">
                            <?php echo Security::csrfField(); ?>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label">Tên thiết bị <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" required placeholder="VD: Máy đo huyết áp">
            </div>
            <div class="col-md-6 mb-3">
                <label for="device_code" class="form-label">Mã thiết bị <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="device_code" name="device_code" required placeholder="VD: DEV004">
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="department_id" class="form-label">Khoa</label>
                <select class="form-select" id="department_id" name="department_id">
                    <option value="">-- Chọn khoa --</option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?= $dept['id'] ?>"><?= htmlspecialchars($dept['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label for="status" class="form-label">Trạng thái</label>
                <select class="form-select" id="status" name="status">
                    <option value="available">Sẵn sàng</option>
                    <option value="in_use">Đang sử dụng</option>
                    <option value="maintenance">Bảo trì</option>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label for="purchase_date" class="form-label">Ngày mua</label>
                <input type="date" class="form-control" id="purchase_date" name="purchase_date">
            </div>
        </div>

        <div class="d-flex gap-3">
            <button type="submit" class="btn-submit"><i class="bi bi-check-lg"></i> Lưu</button>
            <a href="index.php?page=devices" class="btn-cancel"><i class="bi bi-x-lg"></i> Hủy</a>
        </div>
    </form>
</div>

