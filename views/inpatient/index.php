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
            <h5 class="m-0" style="font-weight:700;"><i class="fa-solid fa-hospital-user me-2 text-primary"></i>Danh sách Nội trú</h5>
            <p class="text-muted" style="font-size:13px; margin:5px 0 0 0;">Quản lý bệnh nhân đang nằm viện</p>
        </div>
        <div class="d-flex gap-2">
            <?php $filter = $_GET['filter'] ?? 'active'; ?>
            <a href="index.php?page=inpatient&action=rooms" class="btn btn-outline-info" style="border-radius:20px; font-weight:500;">
                <i class="fa-solid fa-map me-1"></i>Sơ đồ phòng
            </a>
            <a href="index.php?page=inpatient&filter=<?= $filter === 'active' ? 'all' : 'active' ?>" class="btn btn-outline-secondary" style="border-radius:20px; font-weight:500;">
                <i class="fa-solid fa-filter me-1"></i><?= $filter === 'active' ? 'Xem tất cả' : 'Chỉ đang nằm viện' ?>
            </a>
            <a href="index.php?page=inpatient&action=admit" class="btn btn-primary" style="border-radius:20px; font-weight:500;">
                <i class="fa-solid fa-plus me-1"></i>Nhập viện
            </a>
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
                                <span class="badge bg-light text-dark" style="font-size:12px;">
                                    🚪 <?= $a['room_number'] ?> / 🛏️ <?= $a['bed_number'] ?>
                                </span>
                            </td>
                            <td><?= htmlspecialchars($a['department_name'] ?? '-') ?></td>
                            <td><?= date('d/m/Y', strtotime($a['admission_date'])) ?></td>
                            <td style="max-width:200px;"><?= htmlspecialchars(mb_substr($a['diagnosis'] ?? '', 0, 50)) ?><?= mb_strlen($a['diagnosis'] ?? '') > 50 ? '...' : '' ?></td>
                            <td>
                                <?php
                                $sClasses = ['active'=>'badge-confirmed','discharged'=>'badge-completed','transferred'=>'badge-pending'];
                                $sLabels = ['active'=>'Đang nằm viện','discharged'=>'Đã xuất viện','transferred'=>'Chuyển khoa'];
                                ?>
                                <span class="badge-status <?= $sClasses[$a['status']] ?? '' ?>"><?= $sLabels[$a['status']] ?? $a['status'] ?></span>
                            </td>
                            <td>
                                <div class="d-flex gap-1">
                                    <a href="index.php?page=inpatient&action=detail&id=<?= $a['id'] ?>" class="btn btn-sm btn-outline-primary" style="border-radius:8px;"><i class="fa-solid fa-eye"></i></a>
                                    <?php if ($a['status'] === 'active'): ?>
                                    <a href="index.php?page=inpatient&action=discharge&id=<?= $a['id'] ?>" class="btn btn-sm btn-outline-success" style="border-radius:8px;" onclick="return confirm('Xác nhận xuất viện?')">
                                        <i class="fa-solid fa-right-from-bracket"></i>
                                    </a>
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
