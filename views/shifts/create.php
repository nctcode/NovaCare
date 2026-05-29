<!-- Form Tạo Ca trực -->
<div class="form-section shadow-sm p-4 rounded bg-white">
    <h5 class="mb-4 text-primary"><i class="bi bi-clock-history me-2"></i>Tạo Ca trực mới</h5>

    <form method="POST" action="index.php?page=shifts&action=store">
        <?php echo Security::csrfField(); ?>
        
        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="name" class="form-label">Tên ca trực <span class="text-danger">*</span></label>
                <input type="text" class="form-control" id="name" name="name" placeholder="VD: Ca sáng thường, Ca trực cấp cứu..." required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="shift_date" class="form-label">Ngày trực <span class="text-danger">*</span></label>
                <input type="date" class="form-control" id="shift_date" name="shift_date" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="start_time" class="form-label">Giờ bắt đầu <span class="text-danger">*</span></label>
                <input type="time" class="form-control" id="start_time" name="start_time" required>
            </div>
            <div class="col-md-6 mb-3">
                <label for="end_time" class="form-label">Giờ kết thúc <span class="text-danger">*</span></label>
                <input type="time" class="form-control" id="end_time" name="end_time" required>
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="required_doctors" class="form-label">Số bác sĩ cần</label>
                <input type="number" class="form-control" id="required_doctors" name="required_doctors" min="0" value="0">
            </div>
            <div class="col-md-6 mb-3">
                <label for="required_nurses" class="form-label">Số y tá/điều dưỡng cần</label>
                <input type="number" class="form-control" id="required_nurses" name="required_nurses" min="0" value="0">
            </div>
        </div>

        <div class="row">
            <div class="col-md-6 mb-3">
                <label for="shift_type" class="form-label">Loại ca <span class="text-danger">*</span></label>
                <select class="form-select" id="shift_type" name="shift_type" required>
                    <option value="day">☀️ Ca ngày</option>
                    <option value="night">🌙 Ca đêm</option>
                </select>
            </div>
            <div class="col-md-6 mb-3">
                <label for="notes" class="form-label">Ghi chú</label>
                <textarea class="form-control" id="notes" name="notes" rows="2" placeholder="Ghi chú thêm về ca trực nếu có..."></textarea>
            </div>
        </div>

        <div class="alert alert-info alert-custom mb-4">
            <i class="bi bi-info-circle me-1"></i>
            <strong>Quy tắc:</strong> Mỗi ca đêm tối đa 20 bác sĩ. Mỗi bác sĩ phải đăng ký ít nhất 2 ca đêm / tuần.
        </div>

        <div class="d-flex gap-3">
            <button type="submit" class="btn-submit"><i class="bi bi-check-lg"></i> Tạo ca trực</button>
            <a href="index.php?page=shifts" class="btn-cancel"><i class="bi bi-x-lg"></i> Hủy</a>
        </div>
    </form>
</div>
