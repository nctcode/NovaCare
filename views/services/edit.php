<!-- Form Sửa Dịch vụ -->
<div class="form-section" data-aos="fade-up">
    <h5 class="mb-4"><i class="fa-solid fa-pen-to-square me-2"></i>Sửa thông tin Dịch vụ</h5>
    <form method="POST" action="index.php?page=services-admin&action=update">
        <input type="hidden" name="id" value="<?= $service['id'] ?>">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="service_name" class="form-label">Tên dịch vụ <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="service_name" name="service_name" required value="<?= htmlspecialchars($service['service_name']) ?>">
            </div>
            <div class="col-md-6 mb-3">
                <label for="price" class="form-label">Giá tiền (VNĐ) <span class="text-danger">*</span></label>
                <input type="number" class="form-control" id="price" name="price" required value="<?= $service['price'] ?>">
            </div>
        </div>
        <div class="mb-4">
            <label for="description" class="form-label">Mô tả chi tiết</label>
            <textarea class="form-control" id="description" name="description" rows="4"><?= htmlspecialchars($service['description'] ?? '') ?></textarea>
        </div>
        <div class="d-flex gap-3">
            <button type="submit" class="btn-submit"><i class="fa-solid fa-save me-2"></i>Cập nhật</button>
            <a href="index.php?page=services-admin" class="btn-cancel"><i class="fa-solid fa-arrow-left me-2"></i>Quay lại</a>
        </div>
    </form>
</div>
