<!-- Form Sửa Thuốc -->
<div class="form-section">
    <h5 class="mb-4"><i class="bi bi-pencil-square me-2"></i>Sửa thông tin Thuốc</h5>

    <form method="POST" action="index.php?page=medicines&action=update">
                            <?php echo Security::csrfField(); ?>
        <input type="hidden" name="id" value="<?= $medicine['id'] ?>">

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label">Tên thuốc <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" required 
                       value="<?= htmlspecialchars($medicine['name']) ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label for="price" class="form-label">Giá (VNĐ)</label>
                <input type="number" class="form-control" id="price" name="price" min="0" step="100" 
                       value="<?= $medicine['price'] ?? 0 ?>">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="quantity" class="form-label">Số lượng tồn kho</label>
                <input type="number" class="form-control" id="quantity" name="quantity" min="0" 
                       value="<?= $medicine['quantity'] ?? 0 ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label for="expiry_date" class="form-label">Hạn sử dụng</label>
                <input type="date" class="form-control" id="expiry_date" name="expiry_date" 
                       value="<?= $medicine['expiry_date'] ?? '' ?>">
            </div>
        </div>

        <div class="mb-4">
            <label for="description" class="form-label">Mô tả</label>
            <textarea class="form-control" id="description" name="description" rows="3"><?= htmlspecialchars($medicine['description'] ?? '') ?></textarea>
        </div>

        <div class="d-flex gap-3">
            <button type="submit" class="btn-submit"><i class="bi bi-check-lg"></i> Cập nhật</button>
            <a href="index.php?page=medicines" class="btn-cancel"><i class="bi bi-x-lg"></i> Hủy</a>
        </div>
    </form>
</div>

