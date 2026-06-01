<!-- Online Consultations -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-custom alert-dismissible fade show">
        <i class="fa-solid fa-check-circle"></i> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-custom alert-dismissible fade show">
        <i class="fa-solid fa-triangle-exclamation"></i> <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($user['role'] === 'doctor'): ?>
<!-- Tạo phòng tư vấn mới (Bác sĩ) -->
<div class="card shadow-sm border-0 rounded-4 mb-4" data-aos="fade-up">
    <div class="card-header bg-white border-bottom-0 pt-4 pb-0 px-4">
        <h5 class="m-0 fw-bold text-primary"><i class="fa-solid fa-video me-2"></i>Tạo phòng Tư vấn trực tuyến</h5>
        <p class="text-muted small mt-1 mb-0">Khởi tạo phòng họp video cho các lịch hẹn online đã được xác nhận</p>
    </div>
    <div class="card-body p-4">
        <form method="POST" action="index.php?page=consultations&action=store" class="row align-items-end g-3">
            <?php echo Security::csrfField(); ?>
            <div class="col-md-9">
                <label for="appointment_id" class="form-label fw-bold text-muted text-uppercase" style="font-size: 0.85rem;">Chọn lịch hẹn cần tư vấn online <span class="text-danger">*</span></label>
                <select class="form-select form-select-lg bg-light border-0 shadow-none" id="appointment_id" name="appointment_id" required>
                    <option value="">-- Chọn một lịch hẹn khả dụng từ danh sách --</option>
                    <?php foreach ($availableAppointments as $apt): ?>
                        <option value="<?= $apt['id'] ?>">Ngày: <?= date('d/m/Y H:i', strtotime($apt['appointment_date'])) ?> | Bác sĩ: <?= htmlspecialchars($apt['doctor_name']) ?> | Bệnh nhân: <?= htmlspecialchars($apt['patient_name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary btn-lg w-100 fw-bold rounded-pill shadow-sm" style="height: 48px;">
                    <i class="fa-solid fa-plus me-2"></i>Tạo phòng
                </button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<!-- Danh sách cuộc họp -->
<div class="card shadow-sm border-0 rounded-4 mb-4" data-aos="fade-up" data-aos-delay="100">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center bg-white border-bottom-0 pt-4 pb-2 px-4">
        <h5 class="m-0 fw-bold mb-3 mb-md-0"><i class="fa-solid fa-list-check me-2 text-primary"></i>Danh sách Lịch tư vấn <span class="badge bg-primary rounded-pill ms-2"><?= count($meetings) ?></span></h5>
        
        <!-- Ô tìm kiếm -->
        <div class="input-group shadow-sm" style="width: 250px; border-radius: 20px; overflow: hidden;">
            <span class="input-group-text bg-light border-0"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
            <input type="text" id="consultationSearch" class="form-control bg-light border-0 shadow-none" placeholder="Tìm tên bệnh nhân...">
        </div>
    </div>
    <div class="card-body p-4 pt-2">
        <div class="table-responsive" style="overflow-x: auto; white-space: nowrap;">
            <table class="table table-hover align-middle mb-0" id="consultationTable">
                <thead class="table-light">
                    <tr>
                        <th width="18%">Bệnh nhân</th>
                        <th width="15%">Bác sĩ</th>
                        <th width="15%">Thời gian</th>
                        <th width="28%">Lý do khám</th>
                        <th width="12%">Trạng thái</th>
                        <th class="text-center" width="12%">Phòng họp</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    <?php foreach ($meetings as $m): ?>
                    <tr>
                        <td><strong class="text-dark"><?= htmlspecialchars($m['patient_name']) ?></strong></td>
                        <td class="text-secondary"><?= htmlspecialchars($m['doctor_name']) ?></td>
                        <td class="text-secondary"><i class="fa-regular fa-calendar text-muted me-1"></i><?= date('d/m/Y H:i', strtotime($m['start_time'])) ?></td>
                        <td class="text-truncate text-secondary" style="max-width: 250px;" title="<?= htmlspecialchars($m['reason'] ?? '') ?>">
                            <?= htmlspecialchars($m['reason'] ?? 'Không có lý do') ?>
                        </td>
                        <td>
                            <?php if ($m['status'] == 'completed'): ?>
                                <span class="badge rounded-pill bg-success px-3 py-2"><i class="fa-solid fa-check-circle me-1"></i>Đã hoàn thành</span>
                            <?php else: ?>
                                <span class="badge rounded-pill bg-warning text-dark px-3 py-2"><i class="fa-regular fa-clock me-1"></i>Đã lên lịch</span>
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <?php if ($m['status'] == 'scheduled' || $m['status'] == 'pending'): ?>
                                <a href="index.php?page=consultations&action=room&id=<?= htmlspecialchars($m['meeting_id']) ?>" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm fw-medium">
                                    <i class="fa-solid fa-video me-1"></i> Tham gia
                                </a>
                            <?php else: ?>
                                <span class="badge bg-light text-muted border px-3 py-2 rounded-pill"><i class="fa-solid fa-lock me-1"></i>Đã đóng</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                    <?php if (empty($meetings)): ?>
                    <tr><td colspan="6" class="text-center py-5"><div class="empty-state"><div class="empty-icon text-muted mb-3"><i class="fa-solid fa-video-slash fa-3x"></i></div><h6 class="text-secondary fw-bold">Chưa có lịch tư vấn</h6><p class="text-muted small">Hiện tại không có phòng họp trực tuyến nào được lên lịch.</p></div></td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('consultationSearch');
    const tableBody = document.querySelector('#consultationTable tbody');
    
    if (searchInput && tableBody) {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            const rows = tableBody.getElementsByTagName('tr');
            
            for (let i = 0; i < rows.length; i++) {
                // Bỏ qua dòng thông báo trống
                if (rows[i].querySelector('.empty-state')) continue;
                
                const patientCell = rows[i].getElementsByTagName('td')[0];
                if (patientCell) {
                    const txtValue = patientCell.textContent || patientCell.innerText;
                    if (txtValue.toLowerCase().indexOf(filter) > -1) {
                        rows[i].style.display = "";
                    } else {
                        rows[i].style.display = "none";
                    }
                }       
            }
        });
    }
});
</script>

