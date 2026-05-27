<!-- Form Thêm Thiết bị Y tế -->
<div class="form-section p-4 shadow-sm border-0 bg-white" style="border-radius: 16px;" data-aos="fade-up">
    <div class="d-flex align-items-center gap-3 mb-4">
        <div class="d-flex align-items-center justify-content-center text-primary" style="width: 48px; height: 48px; border-radius: 12px; background: rgba(59, 130, 246, 0.08); font-size: 20px;">
            <i class="fa-solid fa-laptop-medical"></i>
        </div>
        <div>
            <h5 class="m-0 fw-bold text-dark">Thêm Máy móc & Thiết bị Y tế</h5>
            <small class="text-muted">Nhập thông tin máy móc kỹ thuật cao để đưa vào danh mục giám sát</small>
        </div>
    </div>

    <form method="POST" action="index.php?page=devices&action=store">
        <?php echo Security::csrfField(); ?>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label fw-semibold text-secondary" style="font-size: 13px;">Tên thiết bị y tế <span class="text-danger">*</span></label>
                <input type="text" class="form-control py-2.5" id="name" name="name" required placeholder="Ví dụ: Máy MRI 3.0T, Máy siêu âm 4D, Máy chụp X-quang..." style="border-radius: 10px;">
            </div>
            <div class="col-md-6 mb-3">
                <label for="device_code" class="form-label fw-semibold text-secondary" style="font-size: 13px;">Mã thiết bị / Ký hiệu kiểm kê <span class="text-danger">*</span></label>
                <input type="text" class="form-control py-2.5" id="device_code" name="device_code" required placeholder="Ví dụ: MRI-001, US-004..." style="border-radius: 10px;">
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="department_id" class="form-label fw-semibold text-secondary" style="font-size: 13px;">Khoa phụ trách / Sử dụng</label>
                <select class="form-select py-2.5" id="department_id" name="department_id" style="border-radius: 10px;">
                    <option value="">-- Chọn khoa --</option>
                    <?php foreach ($departments as $dept): ?>
                        <option value="<?= $dept['id'] ?>"><?= htmlspecialchars($dept['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label for="status" class="form-label fw-semibold text-secondary" style="font-size: 13px;">Trạng thái vận hành ban đầu</label>
                <select class="form-select py-2.5" id="status" name="status" style="border-radius: 10px;">
                    <option value="available">Sẵn sàng vận hành</option>
                    <option value="in_use">Đang được sử dụng</option>
                    <option value="maintenance">Đang bảo trì / Kiểm định</option>
                </select>
            </div>
            <div class="col-md-4 mb-3">
                <label for="purchase_date" class="form-label fw-semibold text-secondary" style="font-size: 13px;">Ngày mua / Đưa về viện</label>
                <input type="date" class="form-control py-2.5" id="purchase_date" name="purchase_date" style="border-radius: 10px;">
            </div>
        </div>

        <div class="d-flex gap-3 mt-3">
            <button type="submit" class="btn-submit px-4 py-2.5 d-inline-flex align-items-center gap-2" style="border-radius: 10px; font-weight: 600;">
                <i class="fa-solid fa-save"></i>
                Lưu thông tin
            </button>
            <a href="index.php?page=devices" class="btn-cancel px-4 py-2.5 d-inline-flex align-items-center gap-2" style="border-radius: 10px; font-weight: 600;">
                <i class="fa-solid fa-arrow-left"></i>
                Hủy bỏ
            </a>
        </div>
    </form>
</div>
