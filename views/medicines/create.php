<!-- Form Thêm Thuốc -->
<div class="form-section">
    <h5 class="mb-4"><i class="bi bi-capsule me-2"></i>Thêm Thuốc mới</h5>

    <form method="POST" action="index.php?page=medicines&action=store">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label">Tên thuốc <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" required placeholder="Nhập tên thuốc...">
            </div>
            <div class="col-md-6 mb-3">
                <label for="price" class="form-label">Giá (VNĐ) <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="price" name="price" required min="0" step="100" placeholder="0">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="quantity" class="form-label">Số lượng tồn kho</label>
                <input type="number" class="form-control" id="quantity" name="quantity" min="0" value="0">
            </div>
            <div class="col-md-6 mb-3">
                <label for="expiry_date" class="form-label">Hạn sử dụng</label>
                <input type="date" class="form-control" id="expiry_date" name="expiry_date">
            </div>
        </div>

        <div class="mb-4">
            <label for="description" class="form-label">Mô tả</label>
            <textarea class="form-control" id="description" name="description" rows="3" placeholder="Mô tả công dụng thuốc..."></textarea>
        </div>

        <div class="d-flex gap-3">
            <button type="submit" class="btn-submit"><i class="bi bi-check-lg"></i> Lưu</button>
            <a href="index.php?page=medicines" class="btn-cancel"><i class="bi bi-x-lg"></i> Hủy</a>
        </div>
    </form>
</div>
