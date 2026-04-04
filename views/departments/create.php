<!-- Form Thêm Khoa -->
<div class="form-section" data-aos="fade-up">
    <h5 class="mb-4"><i class="fa-solid fa-building-columns me-2"></i>Thêm Khoa mới</h5>
    <form method="POST" action="index.php?page=departments&action=store">
        <div class="mb-3">
            <label for="name" class="form-label">Tên khoa <span class="text-danger">*</span></label>
            <input type="text" class="form-control" id="name" name="name" required placeholder="Nhập tên khoa...">
        </div>
        <div class="mb-4">
            <label for="description" class="form-label">Mô tả</label>
            <textarea class="form-control" id="description" name="description" rows="4" placeholder="Mô tả khoa..."></textarea>
        </div>
        <div class="d-flex gap-3">
            <button type="submit" class="btn-submit"><i class="fa-solid fa-save me-2"></i>Lưu</button>
            <a href="index.php?page=departments" class="btn-cancel"><i class="fa-solid fa-arrow-left me-2"></i>Quay lại</a>
        </div>
    </form>
</div>
