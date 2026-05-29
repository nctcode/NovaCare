<!-- views/shifts/staff_quota.php -->
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

<div class="mb-4">
    <a href="index.php?page=shifts" class="btn btn-outline-secondary"><i class="fa-solid fa-arrow-left me-1"></i> Quay lại Ca trực</a>
</div>

<!-- Thống kê nhanh -->
<div class="row g-3 mb-4" data-aos="fade-up">
    <div class="col-md-3">
        <div class="stat-card p-3 shadow-sm border-0 d-flex flex-column h-100 bg-white" style="border-radius: 14px;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted fw-semibold" style="font-size: 13px;">Tổng nhân viên</span>
                <div class="d-flex align-items-center justify-content-center text-primary" style="width: 34px; height: 34px; border-radius: 8px; background: rgba(59, 130, 246, 0.08); font-size: 16px;">
                    <i class="fa-solid fa-users"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1 text-dark"><?= $quotaStats['total_staff'] ?></h3>
            <div class="mt-auto" style="font-size: 11px; color: #64748b; font-weight: 500;">
                Bác sĩ và Điều dưỡng thuộc khoa
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card p-3 shadow-sm border-0 d-flex flex-column h-100 bg-white" style="border-radius: 14px;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted fw-semibold" style="font-size: 13px;">Đạt chỉ tiêu (≥2 đêm)</span>
                <div class="d-flex align-items-center justify-content-center text-success" style="width: 34px; height: 34px; border-radius: 8px; background: rgba(34, 197, 94, 0.08); font-size: 16px;">
                    <i class="fa-solid fa-circle-check"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1 text-success"><?= $quotaStats['sufficient'] ?></h3>
            <div class="mt-auto" style="font-size: 11px; color: #16a34a; font-weight: 500;">
                Đã đăng ký đủ ca trực đêm
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card p-3 shadow-sm border-0 d-flex flex-column h-100 bg-white" style="border-radius: 14px;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted fw-semibold" style="font-size: 13px;">Chưa đạt chỉ tiêu</span>
                <div class="d-flex align-items-center justify-content-center text-danger" style="width: 34px; height: 34px; border-radius: 8px; background: rgba(220, 38, 38, 0.08); font-size: 16px;">
                    <i class="fa-solid fa-circle-exclamation"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1 text-danger"><?= $quotaStats['insufficient'] ?></h3>
            <div class="mt-auto" style="font-size: 11px; color: #dc2626; font-weight: 500;">
                Cần chỉ định hoặc nhắc nhở
            </div>
        </div>
    </div>

    <div class="col-md-3">
        <div class="stat-card p-3 shadow-sm border-0 d-flex flex-column h-100 bg-white" style="border-radius: 14px;">
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted fw-semibold" style="font-size: 13px;">Đăng ký chờ duyệt</span>
                <div class="d-flex align-items-center justify-content-center text-warning" style="width: 34px; height: 34px; border-radius: 8px; background: rgba(245, 158, 11, 0.08); font-size: 16px;">
                    <i class="fa-solid fa-hourglass-half"></i>
                </div>
            </div>
            <h3 class="fw-bold mb-1 text-warning"><?= $quotaStats['pending_count'] ?></h3>
            <div class="mt-auto" style="font-size: 11px; color: #d97706; font-weight: 500;">
                Chờ Trưởng khoa phê duyệt
            </div>
        </div>
    </div>
</div>

<div class="content-card mb-4" data-aos="fade-up">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5>
            <i class="fa-solid fa-calendar-check me-2 text-primary"></i>
            Theo dõi Chỉ tiêu trực đêm (Tuần từ <?= date('d/m/Y', strtotime($weekStart)) ?> đến <?= date('d/m/Y', strtotime($weekEnd)) ?>)
        </h5>
        <span class="badge bg-teal text-white border border-teal-subtle px-3 py-1.5" style="background: #0d9488 !important; font-size: 12px; border-radius: 8px;">
            <i class="fa-solid fa-hospital-user me-1"></i> Khoa: <?= htmlspecialchars($headInfo['department_name'] ?? 'Khoa Lâm Sàng') ?>
        </span>
    </div>
    
    <div class="card-body">
        <div class="alert alert-info alert-custom mb-4" style="background-color: rgba(14, 165, 233, 0.08); border-color: rgba(14, 165, 233, 0.2); color: #0369a1;">
            <i class="fa-solid fa-circle-info me-2" style="font-size: 16px;"></i>
            <strong>Quy định nhân sự:</strong> Mỗi bác sĩ/điều dưỡng bắt buộc trực ít nhất <strong>2 ca trực đêm/tuần</strong>. 
            Khi nhân viên quên đăng ký, bạn có thể bấm <strong>"Chỉ định trực"</strong> để chủ động sắp xếp lịch trực đêm cho họ.
        </div>

        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nhân viên</th>
                        <th>Vai trò</th>
                        <th>Chuyên môn</th>
                        <th>Số ca đêm trong tuần</th>
                        <th>Trạng thái chỉ tiêu</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($staffList)): ?>
                        <tr><td colspan="7" class="text-center text-muted py-4">Không có nhân viên nào trong khoa</td></tr>
                    <?php else: ?>
                        <?php foreach ($staffList as $idx => $s): ?>
                        <tr>
                            <td><?= $idx + 1 ?></td>
                            <td><strong><?= htmlspecialchars($s['name']) ?></strong></td>
                            <td>
                                <?php if ($s['role'] === 'doctor'): ?>
                                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2 py-1" style="font-size:11px;"><i class="fa-solid fa-user-doctor me-1"></i>Bác sĩ</span>
                                <?php else: ?>
                                    <span class="badge bg-info-subtle text-info border border-info-subtle px-2 py-1" style="font-size:11px;"><i class="fa-solid fa-user-nurse me-1"></i>Y tá/ĐD</span>
                                <?php endif; ?>
                            </td>
                            <td><small class="text-muted"><?= htmlspecialchars($s['specialty'] ?? '-') ?></small></td>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span class="fw-bold fs-5 text-dark"><?= $s['night_shifts_count'] ?></span>
                                    <span class="text-muted" style="font-size:12px;">/ 2 ca đêm</span>
                                </div>
                                <div class="progress mt-1" style="height: 6px; width: 120px; border-radius: 3px; background-color: #f1f5f9;">
                                    <?php 
                                        $pct = min(100, ($s['night_shifts_count'] / 2) * 100);
                                        $color = $s['night_shifts_count'] >= 2 ? '#22c55e' : ($s['night_shifts_count'] == 1 ? '#eab308' : '#ef4444');
                                    ?>
                                    <div class="progress-bar" role="progressbar" style="width: <?= $pct ?>%; background-color: <?= $color ?>; border-radius: 3px;"></div>
                                </div>
                            </td>
                            <td>
                                <?php if ($s['night_shifts_count'] >= 2): ?>
                                    <span class="badge bg-success" style="background-color: #22c55e !important;"><i class="fa-solid fa-circle-check me-1"></i>Đạt chỉ tiêu</span>
                                <?php elseif ($s['night_shifts_count'] == 1): ?>
                                    <span class="badge bg-warning text-dark" style="background-color: #f59e0b !important;"><i class="fa-solid fa-triangle-exclamation me-1"></i>Thiếu 1 ca đêm</span>
                                <?php else: ?>
                                    <span class="badge bg-danger" style="background-color: #ef4444 !important;"><i class="fa-solid fa-circle-xmark me-1"></i>Chưa có ca đêm</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($s['night_shifts_count'] < 2): ?>
                                    <button class="btn-action btn-add text-nowrap btn-sm px-2 py-1" style="font-size:12px; border:none;" 
                                            data-bs-toggle="modal" 
                                            data-bs-target="#assignModal" 
                                            data-staff-id="<?= $s['staff_id'] ?>" 
                                            data-staff-name="<?= htmlspecialchars($s['name']) ?>" 
                                            data-role="<?= $s['role'] ?>">
                                        <i class="fa-solid fa-calendar-plus me-1"></i> Chỉ định trực đêm
                                    </button>
                                <?php else: ?>
                                    <span class="text-muted"><i class="fa-solid fa-check-double text-success me-1"></i>Đã đủ</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Modal chỉ định ca trực đêm -->
<div class="modal fade" id="assignModal" tabindex="-1" aria-labelledby="assignModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; overflow: hidden; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
            <div class="modal-header bg-primary text-white p-3">
                <h5 class="modal-title" id="assignModalLabel"><i class="fa-solid fa-calendar-check me-2"></i>Chỉ định ca trực đêm</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <form method="POST" action="index.php?page=shifts&action=assign">
                <?= Security::csrfField() ?>
                <input type="hidden" name="staff_id" id="modal_staff_id">
                <input type="hidden" name="role" id="modal_role">
                
                <div class="modal-body p-4">
                    <p class="mb-3 fs-6">
                        Bạn đang chuẩn bị chỉ định ca trực đêm cho: <br>
                        <strong><span id="modal_staff_name" class="text-primary fs-5"></span></strong>
                    </p>
                    
                    <div class="mb-3">
                        <label for="shift_id" class="form-label fw-semibold">Chọn Ca trực đêm còn trống trong tuần:</label>
                        <select class="form-select" id="shift_id" name="shift_id" required style="border-radius: 10px; padding: 10px;">
                            <option value="">-- Chọn ca trực đêm --</option>
                            <?php if (empty($nightShifts)): ?>
                                <option value="" disabled>Không có ca trực đêm nào được lên lịch trong tuần này</option>
                            <?php else: ?>
                                <?php foreach ($nightShifts as $ns): ?>
                                    <?php 
                                        $totalRegistered = $ns['registered_doctors'] + $ns['registered_nurses'];
                                        $fullText = ($totalRegistered >= 20) ? ' [ĐÃ ĐẦY 20 NGƯỜI]' : '';
                                        $disabled = ($totalRegistered >= 20) ? 'disabled' : '';
                                    ?>
                                    <option value="<?= $ns['id'] ?>" <?= $disabled ?>>
                                        <?= date('d/m/Y', strtotime($ns['shift_date'])) ?> - <?= htmlspecialchars($ns['name']) ?> 
                                        (Giờ: <?= date('H:i', strtotime($ns['start_time'])) ?>-<?= date('H:i', strtotime($ns['end_time'])) ?>) 
                                        [Hiện tại: <?= $totalRegistered ?>/20 người]<?= $fullText ?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                    </div>
                    
                    <div class="alert alert-warning mb-0" style="font-size: 12.5px; border-radius: 10px;">
                        <i class="fa-solid fa-circle-exclamation me-1"></i>
                        <strong>Lưu ý:</strong> Nhân viên sẽ nhận được thông báo hệ thống ngay lập tức khi bạn thực hiện chỉ định trực.
                    </div>
                </div>
                
                <div class="modal-footer p-3 bg-light">
                    <button type="button" class="btn btn-outline-secondary px-4 py-2" data-bs-dismiss="modal" style="border-radius: 10px;">Hủy</button>
                    <button type="submit" class="btn btn-primary px-4 py-2" style="border-radius: 10px; background-color: #0284c7; border-color: #0284c7;">
                        <i class="fa-solid fa-check me-1"></i> Xác nhận chỉ định
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var assignModal = document.getElementById('assignModal');
    if (assignModal) {
        assignModal.addEventListener('show.bs.modal', function(event) {
            var button = event.relatedTarget;
            var staffId = button.getAttribute('data-staff-id');
            var staffName = button.getAttribute('data-staff-name');
            var role = button.getAttribute('data-role');
            
            document.getElementById('modal_staff_id').value = staffId;
            document.getElementById('modal_staff_name').textContent = staffName;
            document.getElementById('modal_role').value = role;
        });
    }
});
</script>