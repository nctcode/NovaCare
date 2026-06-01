<!-- Inpatient Index -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-custom alert-dismissible fade show">
        <i class="fa-solid fa-check-circle"></i> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-custom alert-dismissible fade show">
        <i class="fa-solid fa-exclamation-triangle"></i> <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if (!empty($pendingAdmissions)): ?>
<div class="content-card mb-4" data-aos="fade-up" style="border-radius:16px; border-left: 5px solid var(--bs-warning); box-shadow:0 4px 20px rgba(0,0,0,0.03);">
    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
        <h6 class="m-0 text-warning fw-bold" style="font-size:16px;"><i class="fa-solid fa-clock me-2"></i>Danh sách chờ xếp giường (<?= count($pendingAdmissions) ?>)</h6>
        <p class="text-muted mb-0" style="font-size:12.5px; margin:5px 0 0 0;">Bệnh nhân đã có chỉ định nhập viện từ bác sĩ, đang chờ y tá hoặc lễ tân phân giường & phòng</p>
    </div>
    <div class="card-body">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Bệnh nhân</th>
                        <th>Bác sĩ chỉ định</th>
                        <th>Chẩn đoán ban đầu</th>
                        <th>Ngày chỉ định</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pendingAdmissions as $idx => $pa): ?>
                    <tr>
                        <td><?= $idx + 1 ?></td>
                        <td><strong><?= htmlspecialchars($pa['patient_name']) ?></strong> (<?= $pa['patient_phone'] ?? '-' ?>)</td>
                        <td>BS. <?= htmlspecialchars($pa['doctor_name']) ?></td>
                        <td><?= htmlspecialchars($pa['diagnosis']) ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($pa['created_at'])) ?></td>
                        <td>
                            <div class="d-flex gap-1">
                                <?php if (in_array($_SESSION['user']['role'], ['admin', 'nurse', 'receptionist'])): ?>
                                <a href="index.php?page=inpatient&action=assignBed&id=<?= $pa['id'] ?>" class="btn btn-sm btn-warning d-flex align-items-center gap-1.5" style="border-radius:8px; font-size:12px; font-weight:600; padding: 6px 12px;">
                                    <i class="fa-solid fa-bed"></i> Xếp giường
                                </a>
                                <?php else: ?>
                                <span class="badge bg-light text-warning px-2 py-1.5" style="font-size: 11.5px;">Chờ xử lý</span>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php endif; ?>

<!-- Stats -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="content-card text-center p-3" style="border-radius:16px;">
            <i class="fa-solid fa-door-open text-primary" style="font-size:28px;"></i>
            <h3 class="mt-2 mb-0" style="font-weight:700;"><?= $stats['totalRooms'] ?></h3>
            <small class="text-muted">Tổng phòng</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="content-card text-center p-3" style="border-radius:16px;">
            <i class="fa-solid fa-bed text-success" style="font-size:28px;"></i>
            <h3 class="mt-2 mb-0" style="font-weight:700;"><?= $stats['availableBeds'] ?></h3>
            <small class="text-muted">Giường trống</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="content-card text-center p-3" style="border-radius:16px;">
            <i class="fa-solid fa-user-injured text-warning" style="font-size:28px;"></i>
            <h3 class="mt-2 mb-0" style="font-weight:700;"><?= $stats['activeAdmissions'] ?></h3>
            <small class="text-muted">Đang nằm viện</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="content-card text-center p-3" style="border-radius:16px;">
            <i class="fa-solid fa-clipboard-list text-info" style="font-size:28px;"></i>
            <h3 class="mt-2 mb-0" style="font-weight:700;"><?= $stats['totalAdmissions'] ?></h3>
            <small class="text-muted">Tổng ca nhập viện</small>
        </div>
    </div>
</div>

<div class="content-card" data-aos="fade-up" style="border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.03);">
    <div class="card-header d-flex justify-content-between align-items-center bg-white border-bottom-0 pt-4 pb-0">
        <div>
            <?php if ($_SESSION['user']['role'] === 'doctor'): ?>
                <h5 class="m-0" style="font-weight:700;"><i class="fa-solid fa-file-medical me-2 text-primary"></i>Danh sách Chỉ định Nội trú</h5>
                <p class="text-muted" style="font-size:13px; margin:5px 0 0 0;">Danh sách bệnh nhân được chỉ định điều trị nội trú</p>
            <?php else: ?>
                <h5 class="m-0" style="font-weight:700;"><i class="fa-solid fa-hospital-user me-2 text-primary"></i>Danh sách Nội trú</h5>
                <p class="text-muted" style="font-size:13px; margin:5px 0 0 0;">Quản lý bệnh nhân đang nằm viện</p>
            <?php endif; ?>
        </div>
        <div class="d-flex gap-2">
            <?php $filter = $_GET['filter'] ?? 'active'; ?>
            <a href="index.php?page=inpatient&action=rooms" class="btn btn-outline-info" style="border-radius:20px; font-weight:500;">
                <i class="fa-solid fa-map me-1"></i>Sơ đồ phòng
            </a>
            <a href="index.php?page=inpatient&filter=<?= $filter === 'active' ? 'all' : 'active' ?>" class="btn btn-outline-secondary" style="border-radius:20px; font-weight:500;">
                <i class="fa-solid fa-filter me-1"></i><?= $filter === 'active' ? 'Xem tất cả' : 'Chỉ đang nằm viện' ?>
            </a>
            <?php if (in_array($_SESSION['user']['role'], ['admin', 'receptionist', 'nurse', 'doctor'])): ?>
            <a href="index.php?page=inpatient&action=admit" class="btn btn-primary" style="border-radius:20px; font-weight:500;">
                <i class="fa-solid fa-plus me-1"></i><?= $_SESSION['user']['role'] === 'doctor' ? 'Chỉ định nhập viện' : 'Nhập viện' ?>
            </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Bệnh nhân</th>
                        <th>Bác sĩ phụ trách</th>
                        <th>Phòng / Giường</th>
                        <th>Khoa</th>
                        <th>Ngày nhập</th>
                        <th>Chẩn đoán</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($admissions)): ?>
                        <tr><td colspan="9" class="text-center text-muted py-4">Không có ca nhập viện</td></tr>
                    <?php else: ?>
                        <?php foreach ($admissions as $idx => $a): ?>
                        <tr>
                            <td><?= $idx + 1 ?></td>
                            <td><strong><?= htmlspecialchars($a['patient_name']) ?></strong></td>
                            <td><?= htmlspecialchars($a['doctor_name']) ?></td>
                            <td>
                                <?php if ($a['status'] === 'pending'): ?>
                                    <span class="badge bg-warning text-dark" style="font-size:12px;">
                                        🕒 Chờ xếp giường
                                    </span>
                                <?php else: ?>
                                    <span class="badge bg-light text-dark" style="font-size:12px;">
                                        🚪 <?= $a['room_number'] ?> / 🛏️ <?= $a['bed_number'] ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td><?= htmlspecialchars($a['department_name'] ?? '-') ?></td>
                            <td><?= $a['admission_date'] ? date('d/m/Y', strtotime($a['admission_date'])) : '-' ?></td>
                            <td style="max-width:200px;"><?= htmlspecialchars(mb_substr($a['diagnosis'] ?? '', 0, 50)) ?><?= mb_strlen($a['diagnosis'] ?? '') > 50 ? '...' : '' ?></td>
                            <td>
                                <?php
                                $sClasses = ['pending'=>'badge-pending', 'active'=>'badge-confirmed','discharged'=>'badge-completed','transferred'=>'badge-pending'];
                                $sLabels = ['pending'=>'Chờ xếp giường', 'active'=>'Đang nằm viện','discharged'=>'Đã xuất viện','transferred'=>'Chuyển khoa'];
                                ?>
                                <span class="badge-status <?= $sClasses[$a['status']] ?? '' ?>"><?= $sLabels[$a['status']] ?? $a['status'] ?></span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="index.php?page=inpatient&action=detail&id=<?= $a['id'] ?>" class="btn btn-sm btn-outline-primary" style="border-radius:8px;"><i class="fa-solid fa-eye"></i></a>
                                    <?php if ($a['status'] === 'pending' && in_array($_SESSION['user']['role'], ['admin', 'receptionist', 'nurse'])): ?>
                                    <a href="index.php?page=inpatient&action=assignBed&id=<?= $a['id'] ?>" class="btn btn-sm btn-outline-warning" style="border-radius:8px;" title="Xếp giường"><i class="fa-solid fa-bed"></i></a>
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