<!-- Shift List - Horizontal Weekly Schedule View -->
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

<?php if ($user['role'] === 'doctor' && isset($nightShiftsThisWeek) && $nightShiftsThisWeek < 2): ?>
    <div class="alert alert-warning alert-custom alert-dismissible fade show" data-aos="fade-up" style="border-radius: 14px; border-left: 5px solid #d97706 !important; background-color: #fffbeb;">
        <div class="d-flex align-items-center gap-2">
            <i class="fa-solid fa-circle-exclamation text-warning" style="font-size: 18px;"></i>
            <span class="text-dark-emphasis">
                <strong>Lưu ý định mức:</strong> Bạn mới chỉ đăng ký <strong><?= $nightShiftsThisWeek ?>/2</strong> ca trực đêm tối thiểu cho tuần này. Vui lòng đăng ký thêm để tránh bị Trưởng khoa tự động chỉ định trực.
            </span>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php 
$hasAction = in_array($user['role'], ['admin', 'doctor', 'nurse']) || (isset($isHead) && $isHead); 

// Xác định tuần hiển thị (Mặc định là tuần hiện tại)
$selectedWeekMonday = isset($_GET['week']) ? $_GET['week'] : date('Y-m-d', strtotime('monday this week'));
$startTs = strtotime($selectedWeekMonday);
$endTs = strtotime('+6 days', $startTs);

// Lọc ca trực thuộc tuần được chọn
$weekShifts = array_filter($shifts ?? [], function($s) use ($startTs, $endTs) {
    if (empty($s['shift_date'])) return false;
    $shiftTs = strtotime($s['shift_date']);
    return $shiftTs >= $startTs && $shiftTs <= $endTs;
});

// Khởi tạo mảng 7 ngày trong tuần
$daysOfWeek = [
    1 => ['name' => 'Thứ Hai', 'shifts' => [], 'date' => date('Y-m-d', $startTs)],
    2 => ['name' => 'Thứ Ba', 'shifts' => [], 'date' => date('Y-m-d', strtotime('+1 day', $startTs))],
    3 => ['name' => 'Thứ Tư', 'shifts' => [], 'date' => date('Y-m-d', strtotime('+2 days', $startTs))],
    4 => ['name' => 'Thứ Năm', 'shifts' => [], 'date' => date('Y-m-d', strtotime('+3 days', $startTs))],
    5 => ['name' => 'Thứ Sáu', 'shifts' => [], 'date' => date('Y-m-d', strtotime('+4 days', $startTs))],
    6 => ['name' => 'Thứ Bảy', 'shifts' => [], 'date' => date('Y-m-d', strtotime('+5 days', $startTs))],
    7 => ['name' => 'Chủ Nhật', 'shifts' => [], 'date' => date('Y-m-d', strtotime('+6 days', $startTs))],
];

foreach ($weekShifts as $s) {
    $dayNum = (int)date('N', strtotime($s['shift_date']));
    if (isset($daysOfWeek[$dayNum])) {
        $daysOfWeek[$dayNum]['shifts'][] = $s;
    }
}
?>

<!-- Thanh Điều hướng Tuần -->
<div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4" data-aos="fade-down">
    <div class="d-flex align-items-center gap-2 bg-white p-2 shadow-sm rounded-3" style="border: 1px solid #f1f5f9;">
        <a href="index.php?page=shifts&week=<?= date('Y-m-d', strtotime('-1 week', $startTs)) ?>" class="btn btn-light btn-sm" style="border-radius: 8px; font-weight: 500;">
            <i class="fa-solid fa-chevron-left"></i> Tuần trước
        </a>
        <a href="index.php?page=shifts" class="btn btn-outline-primary btn-sm px-3" style="border-radius: 8px; font-weight: 600;">
            Tuần này
        </a>
        <a href="index.php?page=shifts&week=<?= date('Y-m-d', strtotime('+1 week', $startTs)) ?>" class="btn btn-light btn-sm" style="border-radius: 8px; font-weight: 500;">
            Tuần sau <i class="fa-solid fa-chevron-right"></i>
        </a>
        
        <div class="ms-3 px-2 py-1 bg-light rounded-2 text-dark fw-bold" style="font-size: 13.5px;">
            <i class="fa-regular fa-calendar-days me-1.5 text-primary"></i>Từ <?= date('d/m/Y', $startTs) ?> đến <?= date('d/m/Y', $endTs) ?>
        </div>
    </div>

    <?php if (isset($isHead) && $isHead): ?>
    <div class="d-flex gap-2">
        <a href="index.php?page=shifts&action=staffQuota" class="btn btn-outline-info btn-sm px-3 py-2" style="border-radius: 10px; font-weight: 600;">
            <i class="fa-solid fa-chart-line me-1.5"></i> Theo dõi chỉ tiêu trực đêm
        </a>
        <a href="index.php?page=shifts&action=bulkCreate" class="btn btn-outline-success btn-sm px-3 py-2" style="border-radius: 10px; font-weight: 600;">
            <i class="fa-solid fa-calendar-check me-1.5"></i> Tạo ca theo tuần
        </a>
        <a href="index.php?page=shifts&action=create" class="btn btn-primary btn-sm px-3 py-2 text-white" style="border-radius: 10px; font-weight: 600; background-color: #0284c7; border-color: #0284c7;">
            <i class="fa-solid fa-plus me-1.5"></i> Tạo ca trực
        </a>
    </div>
    <?php endif; ?>
</div>

<!-- Lịch Trực Dòng Ngang (Horizontal Schedule rows) -->
<div class="d-flex flex-column gap-3 mb-5" data-aos="fade-up">
    <?php foreach ($daysOfWeek as $num => $day): 
        $isToday = (date('Y-m-d') === $day['date']);
        $rowBg = $isToday ? '#f8fafc' : '#ffffff';
        $borderLeft = $isToday ? '6px solid #0284c7' : '6px solid #cbd5e1';
    ?>
    <div class="card border-0 shadow-sm" style="border-radius: 16px; border-left: <?= $borderLeft ?> !important; background-color: <?= $rowBg ?>;">
        <div class="card-body p-3.5">
            <div class="row align-items-center g-3">
                <!-- Day Label Column -->
                <div class="col-lg-2 col-md-3">
                    <div class="d-flex flex-column align-items-start align-items-md-center text-md-center">
                        <span class="fs-5 fw-bold text-dark mb-0"><?= $day['name'] ?></span>
                        <span class="text-muted fw-semibold" style="font-size: 13px;"><?= date('d/m/Y', strtotime($day['date'])) ?></span>
                        <?php if ($isToday): ?>
                            <span class="badge bg-primary text-white py-1 px-2.5 mt-1.5" style="font-size: 10px; border-radius: 6px; box-shadow: 0 2px 6px rgba(2, 132, 199, 0.2);">HÔM NAY</span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Shifts Content Column -->
                <div class="col-lg-10 col-md-9 border-start-md" style="border-color: #f1f5f9 !important;">
                    <div class="d-flex flex-wrap gap-3 align-items-center">
                        <?php if (empty($day['shifts'])): ?>
                            <div class="text-muted py-2 px-3 d-flex align-items-center gap-2 border border-dashed rounded-3 bg-light bg-opacity-25" style="border-color: #e2e8f0 !important; font-size: 13px;">
                                <i class="fa-solid fa-calendar-xmark opacity-75"></i>
                                Không có lịch ca trực được lên cho ngày này
                            </div>
                        <?php else: ?>
                            <?php foreach ($day['shifts'] as $s): 
                                $isNight = ($s['shift_type'] === 'night');
                                $shiftTypeColor = $isNight ? '#6366f1' : '#eab308'; // Indigo for night, yellow for day
                                $shiftTypeBg = $isNight ? 'rgba(99, 102, 241, 0.08)' : 'rgba(234, 179, 8, 0.08)';
                            ?>
                            <div class="shift-card-item bg-white p-3.5 border border-light-subtle rounded-3 shadow-sm position-relative d-flex flex-column" 
                                 style="width: 290px; border-radius: 12px !important; min-height: 165px; border-top: 3px solid <?= $shiftTypeColor ?> !important;">
                                
                                <!-- Header: Shift Name & Type Badge -->
                                <div class="d-flex justify-content-between align-items-start mb-2">
                                    <h6 class="fw-bold text-dark mb-0 text-truncate" style="font-size: 13.5px; max-width: 75%;" title="<?= htmlspecialchars($s['name'] ?? 'Ca trực') ?>">
                                        <?= htmlspecialchars($s['name'] ?? 'Ca trực') ?>
                                    </h6>
                                    <span class="badge py-1 px-2 d-flex align-items-center gap-1" style="font-size: 10px; color: <?= $shiftTypeColor ?>; background-color: <?= $shiftTypeBg ?>; border-radius: 6px;">
                                        <?= $isNight ? '🌙 Đêm' : '☀️ Ngày' ?>
                                    </span>
                                </div>

                                <!-- Time & Department info -->
                                <div class="mb-2.5">
                                    <span class="text-primary fw-bold" style="font-size: 13px;">
                                        <i class="fa-regular fa-clock me-1 text-primary-emphasis"></i>
                                        <?= date('H:i', strtotime($s['start_time'])) ?> - <?= date('H:i', strtotime($s['end_time'])) ?>
                                    </span>
                                    <?php if ($s['department_name']): ?>
                                        <div class="text-muted mt-1 text-truncate" style="font-size: 11px;" title="Khoa: <?= htmlspecialchars($s['department_name']) ?>">
                                            <i class="fa-solid fa-hospital-user me-1 text-muted"></i>Khoa: <?= htmlspecialchars($s['department_name']) ?>
                                        </div>
                                    <?php endif; ?>
                                </div>

                                <!-- Registration Quota Slots Progress -->
                                <div class="p-2 rounded-2 mb-3 bg-light" style="font-size: 11.5px;">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <span class="text-muted"><i class="fa-solid fa-user-doctor me-1"></i>Bác sĩ:</span>
                                        <span class="fw-bold text-dark"><?= $s['approved_doctors'] ?>/<?= $s['required_doctors'] ?></span>
                                    </div>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted"><i class="fa-solid fa-user-nurse me-1"></i>Y tá/ĐD:</span>
                                        <span class="fw-bold text-dark"><?= $s['approved_nurses'] ?>/<?= $s['required_nurses'] ?></span>
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                <?php if ($hasAction): ?>
                                <div class="mt-auto d-flex gap-1 justify-content-end align-items-center">
                                    <?php if (isset($isHead) && $isHead && $user['role'] !== 'admin'): ?>
                                        <?php 
                                            $pendingCount = ($s['total_registered_doctors'] - $s['approved_doctors']) + ($s['total_registered_nurses'] - $s['approved_nurses']);
                                        ?>
                                        <a href="index.php?page=shifts&action=manage&shift_id=<?= $s['id'] ?>" 
                                           class="btn btn-sm btn-outline-primary px-3 py-1.5 d-flex align-items-center gap-1" style="font-size: 11px; border-radius: 8px; font-weight: 600;">
                                            <i class="fa-solid fa-user-check"></i> Duyệt ca
                                            <?php if ($pendingCount > 0): ?>
                                                <span class="badge bg-danger ms-0.5 text-white" style="font-size: 9px; border-radius: 10px;"><?= $pendingCount ?></span>
                                            <?php endif; ?>
                                        </a>
                                    <?php elseif ($user['role'] === 'admin'): ?>
                                        <a href="index.php?page=shifts&action=manage&shift_id=<?= $s['id'] ?>" 
                                           class="btn btn-sm btn-outline-secondary px-3 py-1.5" style="font-size: 11px; border-radius: 8px; font-weight: 600;">
                                            <i class="fa-solid fa-eye me-1"></i> Chi tiết
                                        </a>
                                    <?php else: ?>
                                        <!-- Doctor/Nurse dynamic registration -->
                                        <?php if (in_array($s['id'], $myShifts)): ?>
                                            <?php 
                                                $status = $myShiftStatuses[$s['id']] ?? 'pending';
                                                if ($status === 'approved') {
                                                    echo '<span class="badge bg-success py-1.5 px-2.5 d-flex align-items-center gap-1" style="font-size:11px; border-radius: 8px;"><i class="fa-solid fa-circle-check"></i> Đã duyệt</span>';
                                                } elseif ($status === 'rejected') {
                                                    echo '<span class="badge bg-danger py-1.5 px-2.5 d-flex align-items-center gap-1" style="font-size:11px; border-radius: 8px;"><i class="fa-solid fa-circle-xmark"></i> Từ chối</span>';
                                                } elseif ($status === 'assigned') {
                                                    echo '<span class="badge py-1.5 px-2.5 d-flex align-items-center gap-1" style="font-size:11px; border-radius: 8px; color: #7c3aed; background-color: #f5f3ff; border: 1px solid #ddd6fe;"><i class="fa-solid fa-user-shield"></i> Được chỉ định</span>';
                                                } else {
                                                    echo '<span class="badge bg-warning text-dark py-1.5 px-2.5 d-flex align-items-center gap-1" style="font-size:11px; border-radius: 8px;"><i class="fa-solid fa-hourglass-half"></i> Chờ duyệt</span>';
                                                }
                                            ?>
                                            
                                            <!-- Hủy đăng ký nếu là tự nguyện -->
                                            <?php if ($status !== 'assigned'): ?>
                                            <form method="POST" action="index.php?page=shifts&action=unregister" onsubmit="return confirm('Bạn chắc chắn muốn hủy đăng ký ca trực này?');" class="d-inline ms-1.5">
                                                <?= Security::csrfField() ?>
                                                <input type="hidden" name="shift_id" value="<?= $s['id'] ?>">
                                                <button type="submit" class="btn btn-outline-danger btn-sm p-1.5" style="border-radius: 8px; border:none; background: #fff1f2; color: #e11d48;" title="Hủy đăng ký">
                                                    <i class="fa-solid fa-trash-can"></i>
                                                </button>
                                            </form>
                                            <?php endif; ?>
                                        <?php else: ?>
                                            <a href="index.php?page=shifts&action=register&shift_id=<?= $s['id'] ?>" 
                                               class="btn btn-primary btn-sm px-3.5 py-1.5 text-white" style="font-size: 11px; border-radius: 8px; font-weight: 600; background-color: #0284c7; border-color: #0284c7;">
                                                <i class="fa-solid fa-plus me-1"></i> Đăng ký trực
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                                <?php endif; ?>
                            </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>

<style>
@media (min-width: 768px) {
    .border-start-md {
        border-left: 1px solid #f1f5f9 !important;
        padding-left: 1.5rem !important;
    }
}
.shift-card-item {
    transition: transform 0.2s, box-shadow 0.2s;
}
.shift-card-item:hover {
    transform: translateY(-3px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.08) !important;
}
</style>
