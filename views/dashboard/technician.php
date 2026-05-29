<!-- Dashboard Kỹ thuật viên -->
<div class="row g-4 mb-4">
    <div class="col-md-3 col-6" data-aos="fade-up">
        <div class="stat-card card-warning">
            <div class="stat-icon"><i class="fa-solid fa-clock"></i></div>
            <div class="stat-value"><?= $pendingLabOrders ?? 0 ?></div>
            <div class="stat-label">Chờ xử lý</div>
        </div>
    </div>
    <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="100">
        <div class="stat-card card-primary">
            <div class="stat-icon"><i class="fa-solid fa-spinner"></i></div>
            <div class="stat-value"><?= $inProgressLabOrders ?? 0 ?></div>
            <div class="stat-label">Đang thực hiện</div>
        </div>
    </div>
    <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="200">
        <div class="stat-card card-success">
            <div class="stat-icon"><i class="fa-solid fa-check-circle"></i></div>
            <div class="stat-value"><?= $completedLabOrders ?? 0 ?></div>
            <div class="stat-label">Hoàn thành</div>
        </div>
    </div>
    <div class="col-md-3 col-6" data-aos="fade-up" data-aos-delay="300">
        <div class="stat-card card-info">
            <div class="stat-icon"><i class="fa-solid fa-flask-vial"></i></div>
            <div class="stat-value"><?= $totalLabOrders ?? 0 ?></div>
            <div class="stat-label">Tổng chỉ định</div>
        </div>
    </div>
</div>

<!-- Chỉ định cần xử lý -->
<div class="content-card" data-aos="fade-up" data-aos-delay="400">
    <div class="card-header">
        <h5><i class="fa-solid fa-list-check me-2"></i>Chỉ định chờ xử lý</h5>
        <a href="index.php?page=lab-orders" class="btn-action btn-view">Xem tất cả <i class="fa-solid fa-arrow-right ms-1"></i></a>
    </div>
    <div class="card-body">
        <?php if (!empty($pendingOrders)): ?>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Bệnh nhân</th>
                        <th>Loại</th>
                        <th>Tên XN/Chụp</th>
                        <th>Ưu tiên</th>
                        <th>BS chỉ định</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pendingOrders as $idx => $o): ?>
                    <tr>
                        <td><?= $idx + 1 ?></td>
                        <td><strong><?= htmlspecialchars($o['patient_name']) ?></strong></td>
                        <td>
                            <?= $o['order_type'] === 'lab_test' 
                                ? '<span class="badge bg-info text-dark">Xét nghiệm</span>' 
                                : '<span class="badge bg-secondary">CĐHA</span>' ?>
                        </td>
                        <td><?= htmlspecialchars($o['test_name']) ?></td>
                        <td>
                            <?= $o['priority'] === 'urgent' 
                                ? '<span class="badge bg-danger"><i class="fa-solid fa-bolt me-1"></i>Cấp cứu</span>' 
                                : '<span class="badge bg-light text-dark border">Thường</span>' ?>
                        </td>
                        <td><?= htmlspecialchars($o['doctor_name']) ?></td>
                        <td>
                            <a href="index.php?page=lab-orders&action=result&id=<?= $o['id'] ?>" class="btn btn-sm btn-primary" style="border-radius:15px">
                                <i class="fa-solid fa-pen-to-square me-1"></i>Nhập KQ
                            </a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        <?php else: ?>
        <div class="text-center text-muted py-4">
            <i class="fa-solid fa-circle-check fa-2x mb-2" style="opacity:.3"></i>
            <p>Không có chỉ định nào đang chờ xử lý</p>
        </div>
        <?php endif; ?>
    </div>
</div>
