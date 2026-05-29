<!-- Form Thêm Giám đốc -->
<div class="form-section" data-aos="fade-up">
    <h5 class="mb-4"><i class="fa-solid fa-plus-circle me-2"></i>Thêm Giám đốc mới</h5>

    <form method="POST" action="index.php?page=directors&action=store">
        <?php echo Security::csrfField(); ?>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label">Họ tên <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" required placeholder="Nhập họ tên...">
            </div>
            <div class="col-md-6 mb-3">
                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" required placeholder="Nhập email...">
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="phone" class="form-label">Số điện thoại</label>
                <input type="text" class="form-control" id="phone" name="phone" placeholder="Nhập SĐT...">
            </div>
        </div>
        <div class="d-flex gap-3 mt-3">
            <button type="submit" class="btn-submit"><i class="fa-solid fa-save me-2"></i>Lưu</button>
            <a href="index.php?page=directors" class="btn-cancel"><i class="fa-solid fa-arrow-left me-2"></i>Quay lại</a>
        </div>
    </form>
</div>
