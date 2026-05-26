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

<div class="content-card" data-aos="fade-up">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5><i class="fa-solid fa-clock-rotate-left me-2"></i>Danh sách Ca trực</h5>
        <?php if (isset($isHead) && $isHead): ?>
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
                        <th>Thông tin ca trực</th>
                        <th>Ngày trực</th>
                        <th>Giờ trực</th>
                        <th>Bác sĩ (Đã duyệt / Cần)</th>
                        <th>Y tá/ĐD (Đã duyệt / Cần)</th>
                        <th>Loại ca</th>
                        <th>Ghi chú</th>
                        <?php if ($user['role'] === 'doctor' || $user['role'] === 'nurse' || (isset($isHead) && $isHead)): ?>
                        <th>Thao tác</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($shifts)): ?>
                        <tr><td colspan="9" class="text-center text-muted py-4">Không có ca trực phù hợp</td></tr>
                    <?php else: ?>
                        <?php foreach ($shifts as $idx => $s): ?>
                        <tr>
                            <td><?= $idx + 1 ?></td>
                            <td>
                                <strong><?= htmlspecialchars($s['name'] ?? 'Ca trực') ?></strong>
                                <?php if ($s['department_name']): ?>
                                    <br><span class="badge bg-light text-dark border"><i class="fa-solid fa-house-medical me-1"></i>Khoa: <?= htmlspecialchars($s['department_name']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <strong><?= date('d/m/Y', strtotime($s['shift_date'])) ?></strong>
                                <br><small class="text-muted"><?php 
                                    $dayOfWeek = date('N', strtotime($s['shift_date']));
                                    $days = ['', 'Thứ 2', 'Thứ 3', 'Thứ 4', 'Thứ 5', 'Thứ 6', 'Thứ 7', 'CN'];
                                    echo $days[$dayOfWeek];
                                ?></small>
                            </td>
                            <td>
                                <span class="text-nowrap"><?= date('H:i', strtotime($s['start_time'])) ?> - <?= date('H:i', strtotime($s['end_time'])) ?></span>
                            </td>
                            <td>
                                <span class="badge bg-info text-dark"><?= $s['approved_doctors'] ?> / <?= $s['required_doctors'] ?></span>
                                <?php if ($s['total_registered_doctors'] > $s['approved_doctors']): ?>
                                    <small class="text-warning d-block">(Chờ duyệt: <?= $s['total_registered_doctors'] - $s['approved_doctors'] ?>)</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge bg-info text-dark"><?= $s['approved_nurses'] ?> / <?= $s['required_nurses'] ?></span>
                                <?php if ($s['total_registered_nurses'] > $s['approved_nurses']): ?>
                                    <small class="text-warning d-block">(Chờ duyệt: <?= $s['total_registered_nurses'] - $s['approved_nurses'] ?>)</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="badge-status badge-<?= $s['shift_type'] ?>">
                                    <?= $s['shift_type'] === 'day' ? '☀️ Ca ngày' : '🌙 Ca đêm' ?>
                                </span>
                            </td>
                            <td>
                                <small class="text-muted text-wrap d-block" style="max-                            <td>
                                <div class="d-flex flex-column gap-1 align-items-start">
                                    <?php if (isset($isHead) && $isHead && $user['role'] !== 'admin'): ?>
                                        <!-- Đối với Trưởng khoa: Chỉ hiện nút Xem danh sách & Duyệt đăng ký -->
                                        <?php 
                                            $pendingCount = ($s['total_registered_doctors'] - $s['approved_doctors']) + ($s['total_registered_nurses'] - $s['approved_nurses']);
                                        ?>
                                        <a href="index.php?page=shifts&action=manage&shift_id=<?= $s['id'] ?>" 
                                           class="btn-action btn-edit text-nowrap mb-1">
                                            <i class="fa-solid fa-list-check"></i> Danh sách đăng ký
                                            <?php if ($pendingCount > 0): ?>
                                                <span class="badge bg-danger ms-1"><?= $pendingCount ?></span>
                                            <?php endif; ?>
                                        </a>
                                    <?php elseif ($user['role'] === 'admin'): ?>
                                        <!-- Đối với Admin: Hiện nút Xem danh sách đăng ký -->
                                        <a href="index.php?page=shifts&action=manage&shift_id=<?= $s['id'] ?>" 
                                           class="btn-action btn-info text-white text-nowrap mb-1" style="background-color: #17a2b8; border-color: #17a2b8;">
                                            <i class="fa-solid fa-eye"></i> Danh sách đăng ký
                                        </a>
                                    <?php else: ?>
                                        <!-- Đối với Nhân viên thường (Doctor/Nurse): Hiện nút Đăng ký trực hoặc trạng thái cá nhân -->
                                        <?php if (in_array($s['id'], $myShifts)): ?>
                                            <?php 
                                                $status = $myShiftStatuses[$s['id']] ?? 'pending';
                                                if ($status === 'approved') {
                                                    echo '<span class="badge bg-success mb-1"><i class="fa-solid fa-check-double me-1"></i>Đã duyệt</span>';
                                                } elseif ($status === 'rejected') {
                                                    echo '<span class="badge bg-danger mb-1"><i class="fa-solid fa-ban me-1"></i>Từ chối</span>';
                                                } else {
                                                    echo '<span class="badge bg-warning text-dark mb-1"><i class="fa-solid fa-hourglass-half me-1"></i>Chờ duyệt</span>';
                                                }
                                            ?>
                                            <form method="POST" action="index.php?page=shifts&action=unregister" onsubmit="return confirm('Bạn chắc chắn muốn hủy đăng ký ca trực này?');">
                                                <?= Security::csrfField() ?>
                                                <input type="hidden" name="shift_id" value="<?= $s['id'] ?>">
                                                <button type="submit" class="btn-action btn-delete text-nowrap" style="padding: 5px 10px; font-size: 12px; border: none;">
                                                    <i class="fa-solid fa-xmark"></i> Hủy đăng ký
                                                </button>
                                            </form>
                                        <?php else: ?>
                                            <a href="index.php?page=shifts&action=register&shift_id=<?= $s['id'] ?>" 
                                               class="btn-action btn-add text-nowrap" style="padding:5px 10px; font-size:12px;">
                                                <i class="fa-solid fa-check"></i> Đăng ký trực
                                            </a>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
