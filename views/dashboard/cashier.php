<!-- Dashboard Thu ngân -->
<div class="row g-4 mb-4">
    <div class="col-md-4" data-aos="fade-up">
        <div class="stat-card card-success">
            <div class="stat-icon"><i class="fa-solid fa-money-bill-wave"></i></div>
            <div class="stat-value"><?= number_format($data['totalRevenue'] ?? 0, 0, ',', '.') ?></div>
            <div class="stat-label">Tổng doanh thu (VNĐ)</div>
        </div>
    </div>
    <div class="col-md-4" data-aos="fade-up" data-aos-delay="100">
        <div class="stat-card card-warning">
            <div class="stat-icon"><i class="fa-solid fa-clock"></i></div>
            <div class="stat-value"><?= $data['pendingInvoicesCount'] ?? 0 ?></div>
            <div class="stat-label">Hóa đơn chờ thanh toán</div>
        </div>
    </div>
    <div class="col-md-4" data-aos="fade-up" data-aos-delay="200">
        <a href="index.php?page=invoices" class="text-decoration-none">
            <div class="stat-card card-primary" style="cursor:pointer;">
                <div class="stat-icon"><i class="fa-solid fa-file-invoice-dollar"></i></div>
                <div class="stat-value" style="font-size:16px">Quản lý Hóa đơn</div>
                <div class="stat-label">Xem tất cả & thanh toán</div>
            </div>
        </a>
    </div>
</div>

<!-- Quick Actions -->
<div class="row g-3 mb-4" data-aos="fade-up">
    <div class="col-12">
        <div class="d-flex flex-wrap gap-2">
            <a href="index.php?page=invoices&action=create" class="btn btn-outline-primary" style="border-radius:20px; padding:8px 20px; font-weight:500; font-size:14px;"><i class="fa-solid fa-file-circle-plus me-2"></i>Tạo Hóa đơn mới</a>
            <a href="index.php?page=invoices" class="btn btn-outline-info" style="border-radius:20px; padding:8px 20px; font-weight:500; font-size:14px;"><i class="fa-solid fa-list-check me-2"></i>Danh sách Hóa đơn</a>
        </div>
    </div>
</div>

<!-- Hóa đơn gần đây -->
<?php if (!empty($data['recentInvoices'])): ?>
<div class="content-card" data-aos="fade-up" data-aos-delay="300">
    <div class="card-header">
        <h5><i class="fa-solid fa-receipt me-2"></i>Hóa đơn gần đây</h5>
        <a href="index.php?page=invoices" class="btn-action btn-view">Xem tất cả <i class="fa-solid fa-arrow-right ms-1"></i></a>
    </div>
    <div class="card-body">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Bệnh nhân</th>
                        <th>Tổng tiền</th>
                        <th>Trạng thái</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach (array_slice($data['recentInvoices'], 0, 8) as $inv): ?>
                    <tr>
                        <td>#<?= $inv['id'] ?></td>
                        <td><strong><?= htmlspecialchars($inv['patient_name'] ?? '') ?></strong></td>
                        <td class="fw-bold"><?= number_format($inv['total_amount'] ?? 0, 0, ',', '.') ?>đ</td>
                        <td>
                            <?php
                            $st = $inv['status'] ?? 'pending';
                            if ($st === 'paid') echo '<span class="badge bg-success">Đã thanh toán</span>';
                            elseif ($st === 'cancelled') echo '<span class="badge bg-danger">Đã hủy</span>';
                            else echo '<span class="badge bg-warning text-dark">Chờ thanh toán</span>';
                            ?>
                        </td>
                        <td><small><?= date('d/m/Y', strtotime($inv['created_at'])) ?></small></td>
                        <td>
                            <a href="index.php?page=invoices&action=detail&id=<?= $inv['id'] ?>" class="btn-action btn-view" title="Xem"><i class="fa-solid fa-eye"></i></a>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<?php else: ?>
<div class="content-card" data-aos="fade-up" data-aos-delay="300">
    <div class="card-body text-center text-muted py-4">
        <i class="fa-solid fa-file-invoice fa-2x mb-2" style="opacity:.3"></i>
        <p>Chưa có hóa đơn nào</p>
    </div>
</div>
<?php endif; ?>
