<!-- Form Tạo Ca trực -->
<div class="form-section">
    <h5 class="mb-4"><i class="bi bi-clock-history me-2"></i>Tạo Ca trực mới</h5>

    <form method="POST" action="index.php?page=shifts&action=store">
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="shift_date" class="form-label">Ngày <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="shift_date" name="shift_date" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="shift_type" class="form-label">Loại ca <span class="text-danger">*</span></label>
                <select class="form-select" id="shift_type" name="shift_type" required>
                    <option value="day">☀️ Ca ngày</option>
                    <option value="night">🌙 Ca đêm</option>
                </select>
            </div>
        </div>

        <div class="alert alert-info alert-custom">
            <i class="bi bi-info-circle"></i>
            <strong>Quy tắc:</strong> Mỗi ca đêm tối đa 20 bác sĩ. Mỗi bác sĩ phải đăng ký ít nhất 2 ca đêm / tuần.
        </div>

        <div class="d-flex gap-3">
            <button type="submit" class="btn-submit"><i class="bi bi-check-lg"></i> Tạo</button>
            <a href="index.php?page=shifts" class="btn-cancel"><i class="bi bi-x-lg"></i> Hủy</a>
        </div>
    </form>
</div>
