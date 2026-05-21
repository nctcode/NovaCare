<!-- Appointment List -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-custom alert-dismissible fade show">
        <i class="bi bi-check-circle-fill"></i> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-custom alert-dismissible fade show">
        <i class="bi bi-exclamation-triangle-fill"></i> <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<!-- Advanced Filter Bar -->
<div class="filter-bar" data-aos="fade-up">
    <span class="filter-label"><i class="fa-solid fa-filter me-1"></i>Lọc</span>

    <select id="filterStatus" class="filter-select">
        <option value="">Tất cả trạng thái</option>
        <option value="pending">⏳ Chờ xác nhận</option>
        <option value="confirmed">✅ Đã xác nhận</option>
        <option value="completed">🏁 Hoàn thành</option>
        <option value="cancelled">❌ Đã hủy</option>
        <option value="emergency">🚨 Khẩn cấp</option>
    </select>

    <div class="filter-divider"></div>

    <label class="filter-label" style="margin:0;">Từ ngày</label>
    <input type="date" id="filterFrom" class="filter-input">
    <label class="filter-label" style="margin:0;">Đến ngày</label>
    <input type="date" id="filterTo" class="filter-input">

    <div class="filter-divider"></div>

    <button class="btn-filter-clear" id="filterClear">
        <i class="fa-solid fa-xmark"></i> Xóa bộ lọc
    </button>
</div>

<div class="content-card" data-aos="fade-up" style="border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.03);">
    <div class="card-header d-flex justify-content-between align-items-center bg-white border-bottom-0 pt-4 pb-0">
        <div>
            <h5 class="m-0" style="font-weight:700;"><i class="fa-solid fa-calendar-check me-2 text-primary"></i>Danh sách Lịch hẹn</h5>
            <p class="text-muted" style="font-size:13px; margin:5px 0 0 0;">Quản lý và cập nhật trạng thái lịch khám</p>
        </div>
        <div class="d-flex gap-2">
            <?php if ($_SESSION['user']['role'] === 'admin' || $_SESSION['user']['role'] === 'doctor'): ?>
                <a href="index.php?page=appointments&action=calendar" class="btn btn-outline-primary" style="border-radius:20px; font-weight:500;">
                    <i class="fa-regular fa-calendar-days me-2"></i>Dạng Lịch (Calendar)
                </a>
            <?php endif; ?>
            <?php if ($_SESSION['user']['role'] === 'admin' || $_SESSION['user']['role'] === 'patient'): ?>
            <a href="index.php?page=appointments&action=create" class="btn btn-primary" style="border-radius:20px; font-weight:500;">
                <i class="fa-solid fa-plus me-2"></i>Đặt lịch khám
            </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body">
        <div class="table-wrapper">
            <table class="data-table" id="appointmentsTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <?php if ($_SESSION['user']['role'] !== 'patient'): ?>
                        <th>Bệnh nhân</th>
                        <?php endif; ?>
                        <?php if ($_SESSION['user']['role'] !== 'doctor'): ?>
                        <th>Bác sĩ</th>
                        <?php endif; ?>
                        <th>Ngày khám</th>
                        <th>Lý do</th>
                        <th>Trạng thái</th>
                        <?php if (in_array($_SESSION['user']['role'], ['admin', 'receptionist', 'doctor', 'nurse'])): ?>
                        <th>Cập nhật</th>
                        <?php endif; ?>
                        <?php if ($_SESSION['user']['role'] === 'patient'): ?>
                        <th>Thao tác</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($appointments)): ?>
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <div class="empty-icon"><i class="fa-regular fa-calendar-xmark"></i></div>
                                    <h6>Không có lịch hẹn nào</h6>
                                    <p>Chưa có dữ liệu lịch hẹn để hiển thị.</p>
                                </div>
                            </td>
                        </tr>
                    <?php else: ?>
                        <?php foreach ($appointments as $idx => $a):
                            $dateOnly = isset($a['appointment_date']) ? date('Y-m-d', strtotime($a['appointment_date'])) : '';
                            $statusVal = $a['status'] ?? 'pending';

                            $statusLabels = [
                                'pending'   => 'Chờ xác nhận',
                                'confirmed' => 'Đã xác nhận',
                                'completed' => 'Hoàn thành',
                                'cancelled' => 'Đã hủy',
                                'emergency' => 'Khẩn cấp',
                            ];
                            $statusLabel = $statusLabels[$statusVal] ?? ucfirst($statusVal);
                        ?>
                        <tr data-status="<?= htmlspecialchars($statusVal) ?>" data-date="<?= $dateOnly ?>">
                            <td><?= $idx + 1 ?></td>
                            <?php if ($_SESSION['user']['role'] !== 'patient'): ?>
                            <td><strong><?= htmlspecialchars($a['patient_name'] ?? '') ?></strong></td>
                            <?php endif; ?>
                            <?php if ($_SESSION['user']['role'] !== 'doctor'): ?>
                            <td><?= htmlspecialchars($a['doctor_name'] ?? '') ?></td>
                            <?php endif; ?>
                            <td><i class="fa-regular fa-clock me-1 text-muted"></i><?= date('d/m/Y H:i', strtotime($a['appointment_date'])) ?></td>
                            <td><?= htmlspecialchars($a['reason'] ?? '') ?></td>
                            <td><span class="badge-status badge-<?= $statusVal ?>"><?= $statusLabel ?></span></td>
                            <?php if (in_array($_SESSION['user']['role'], ['admin', 'receptionist', 'doctor', 'nurse'])): ?>
                            <td>
                                <div class="dropdown">
                                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                        Cập nhật
                                    </button>
                                    <ul class="dropdown-menu">
                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="postAction('index.php?page=appointments&action=updateStatus&id=<?= $a['id'] ?>&status=confirmed')">
                                            <i class="bi bi-check-circle text-primary"></i> Xác nhận</a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="postAction('index.php?page=appointments&action=updateStatus&id=<?= $a['id'] ?>&status=completed')">
                                            <i class="bi bi-check-all text-success"></i> Hoàn thành</a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="postAction('index.php?page=appointments&action=updateStatus&id=<?= $a['id'] ?>&status=cancelled')">
                                            <i class="bi bi-x-circle text-danger"></i> Hủy</a></li>
                                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="postAction('index.php?page=appointments&action=updateStatus&id=<?= $a['id'] ?>&status=emergency')">
                                            <i class="fa-solid fa-triangle-exclamation text-danger"></i> Đánh dấu khẩn cấp</a></li>
                                    </ul>
                                </div>
                            </td>
                            <?php endif; ?>
                            <?php if ($_SESSION['user']['role'] === 'patient'): ?>
                            <td>
                                <?php if ($statusVal === 'pending'): ?>
                                <button type="button" class="btn btn-sm btn-outline-danger" style="border-radius:8px;" onclick="if(confirm('Bạn có chắc chắn muốn hủy lịch hẹn này?')) { postAction('index.php?page=appointments&action=cancelMyAppointment&id=<?= $a['id'] ?>'); }">
                                    <i class="fa-solid fa-xmark"></i> Hủy
                                </button>
                                <?php else: ?>
                                <span class="text-muted" style="font-size:12px;">Không thể hủy</span>
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


