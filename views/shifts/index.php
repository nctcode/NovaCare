<!-- Shift List -->
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
    <div class="alert alert-warning alert-custom alert-dismissible fade show" data-aos="fade-up">
        <i class="fa-solid fa-circle-exclamation"></i> <strong>Lưu ý:</strong> Bạn cần đăng ký thêm <strong><?= 2 - $nightShiftsThisWeek ?></strong> ca trực đêm trong tuần này để đủ chỉ tiêu (ít nhất 2 ca/tuần).
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php 
$user = $_SESSION['user'];
$myShiftIds = [];
if (!empty($myShifts)) {
    foreach ($myShifts as $ms) {
        $myShiftIds[] = $ms['id'];
    }
}
?>

<div class="content-card" data-aos="fade-up">
    <div class="card-header">
        <h5><i class="fa-solid fa-clock-rotate-left me-2"></i>Danh sách Ca trực</h5>
        <?php if ($user['role'] === 'admin'): ?>
        <a href="index.php?page=shifts&action=create" class="btn-action btn-add">
            <i class="fa-solid fa-plus"></i> Tạo ca trực
        </a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Ngày</th>
                        <th>Ca</th>
                        <th>Đã đăng ký</th>
                        <?php if ($user['role'] === 'doctor' || $user['role'] === 'nurse'): ?>
                        <th>Thao tác</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($shifts)): ?>
                        <tr><td colspan="5" class="text-center text-muted py-4">Không có ca trực</td></tr>
                    <?php else: ?>
                        <?php foreach ($shifts as $idx => $s): ?>
                        <tr>
                            <td><?= $idx + 1 ?></td>
                            <td><strong><?= date('d/m/Y', strtotime($s['shift_date'])) ?></strong>
                                <br><small class="text-muted"><?php 
                                    $dayOfWeek = date('N', strtotime($s['shift_date']));
                                    $days = ['', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7', 'CN'];
                                    echo $days[$dayOfWeek];
                                ?></small>
                            </td>
                            <td>
                                <span class="badge-status badge-<?= $s['shift_type'] ?>">
                                    <?= $s['shift_type'] === 'day' ? '☀️ Ca ngày' : '🌙 Ca đêm' ?>
                                </span>
                            </td>
                            <td>
                                <span class="badge bg-secondary"><?= $s['registered_count'] ?> người</span>
                                <?php if ($s['shift_type'] === 'night'): ?>
                                    <small class="text-muted">(tối đa 20)</small>
                                <?php endif; ?>
                            </td>
                            <?php if ($user['role'] === 'doctor' || $user['role'] === 'nurse'): ?>
                            <td>
                                <?php if (in_array($s['id'], $myShiftIds)): ?>
                                    <span class="badge bg-success me-2"><i class="fa-solid fa-check"></i> Đã đăng ký</span>
                                    <a href="index.php?page=shifts&action=unregister&shift_id=<?= $s['id'] ?>" 
                                       class="btn-action btn-delete" title="Hủy đăng ký">
                                        <i class="fa-solid fa-xmark"></i> Hủy
                                    </a>
                                <?php else: ?>
                                    <a href="index.php?page=shifts&action=register&shift_id=<?= $s['id'] ?>" 
                                       class="btn-action btn-add" style="padding:7px 14px; font-size:13px;">
                                        <i class="fa-solid fa-check"></i> Đăng ký
                                    </a>
                                <?php endif; ?>
                            </td>
                            <?php endif; ?>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
