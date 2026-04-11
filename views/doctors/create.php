<!-- Form Thêm Bác sĩ -->
<div class="form-section">
    <h5 class="mb-4"><i class="bi bi-person-plus-fill me-2"></i>Thêm Bác sĩ mới</h5>

    <form method="POST" action="index.php?page=doctors&action=store" id="createDoctorForm">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label">Họ tên <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" required placeholder="Nhập họ tên bác sĩ...">
            </div>
            <div class="col-md-6 mb-3">
                <label for="email" class="form-label">Email <span class="text-danger">*</span></label>
                <input type="email" class="form-control" id="email" name="email" required placeholder="Nhập email...">
            </div>
        </div>

        <div class="row">
            <div class="col-md-4 mb-3">
                <label for="phone" class="form-label">Số điện thoại</label>
                <input type="text" class="form-control" id="phone" name="phone" placeholder="09xxxxxxxx">
            </div>
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
                <label for="experience_years" class="form-label">Kinh nghiệm (năm)</label>
                <input type="number" class="form-control" id="experience_years" name="experience_years" min="0" value="0">
            </div>
        </div>

        <div class="mb-4">
            <label for="specialty" class="form-label">Chuyên khoa</label>
            <input type="text" class="form-control" id="specialty" name="specialty" placeholder="VD: Chuyên khoa tim mạch">
        </div>

        <div class="d-flex gap-3">
            <button type="submit" class="btn-submit">
                <i class="bi bi-check-lg"></i> Lưu
            </button>
            <a href="index.php?page=doctors" class="btn-cancel">
                <i class="bi bi-x-lg"></i> Hủy
            </a>
        </div>
    </form>
</div>
