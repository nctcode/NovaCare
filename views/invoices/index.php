<!-- Invoice List -->
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

<?php if ($_SESSION['user']['role'] === 'admin'): ?>
<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="content-card text-center p-3" style="border-radius:16px;">
            <i class="fa-solid fa-file-invoice text-primary" style="font-size:28px;"></i>
            <h3 class="mt-2 mb-0" style="font-weight:700;"><?= $stats['total'] ?></h3>
            <small class="text-muted">Tổng hóa đơn</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="content-card text-center p-3" style="border-radius:16px;">
            <i class="fa-solid fa-clock text-warning" style="font-size:28px;"></i>
            <h3 class="mt-2 mb-0" style="font-weight:700;"><?= $stats['pending'] ?></h3>
            <small class="text-muted">Chờ thanh toán</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="content-card text-center p-3" style="border-radius:16px;">
            <i class="fa-solid fa-check-circle text-success" style="font-size:28px;"></i>
            <h3 class="mt-2 mb-0" style="font-weight:700;"><?= $stats['paid'] ?></h3>
            <small class="text-muted">Đã thanh toán</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="content-card text-center p-3" style="border-radius:16px;">
            <i class="fa-solid fa-coins text-info" style="font-size:28px;"></i>
            <h3 class="mt-2 mb-0" style="font-weight:700;"><?= number_format($stats['revenue'], 0, ',', '.') ?>đ</h3>
            <small class="text-muted">Doanh thu</small>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="content-card" data-aos="fade-up" style="border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.03);">
    <div class="card-header d-flex justify-content-between align-items-center bg-white border-bottom-0 pt-4 pb-0">
        <div>
            <h5 class="m-0" style="font-weight:700;"><i class="fa-solid fa-file-invoice-dollar me-2 text-primary"></i>Danh sách Hóa đơn</h5>
            <p class="text-muted" style="font-size:13px; margin:5px 0 0 0;">Quản lý hóa đơn và thanh toán</p>
        </div>
        <?php if ($_SESSION['user']['role'] === 'admin'): ?>
        <a href="index.php?page=invoices&action=create" class="btn btn-primary" style="border-radius:20px; font-weight:500;">
            <i class="fa-solid fa-plus me-2"></i>Tạo hóa đơn
        </a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Mã HĐ</th>
                        <th>Bệnh nhân</th>
                        <th>Ngày tạo</th>
                        <th>Tổng tiền</th>
                        <th>Giảm giá</th>
                        <th>Thành tiền</th>
                        <th>Thanh toán</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($invoices)): ?>
                        <tr><td colspan="9" class="text-center text-muted py-4">Chưa có hóa đơn nào</td></tr>
                    <?php else: ?>
                        <?php foreach ($invoices as $inv): ?>
                        <tr>
                            <td><strong>#<?= $inv['id'] ?></strong></td>
                            <td><?= htmlspecialchars($inv['patient_name'] ?? '') ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($inv['created_at'])) ?></td>
                            <td style="text-align:right;"><?= number_format($inv['total_amount'], 0, ',', '.') ?>đ</td>
                            <td style="text-align:right;"><?= $inv['discount'] > 0 ? '-' . number_format($inv['discount'], 0, ',', '.') . 'đ' : '-' ?></td>
                            <td style="text-align:right; font-weight:700;"><?= number_format($inv['final_amount'], 0, ',', '.') ?>đ</td>
                            <td>
                                <?php 
                                $methodLabels = ['cash'=>'Tiền mặt','card'=>'Thẻ','momo'=>'MoMo','vnpay'=>'VNPay','transfer'=>'Chuyển khoản'];
                                echo $methodLabels[$inv['payment_method']] ?? $inv['payment_method'];
                                ?>
                            </td>
                            <td>
                                <?php
                                $statusClasses = ['pending'=>'badge-pending','paid'=>'badge-confirmed','cancelled'=>'badge-cancelled'];
                                $statusLabels = ['pending'=>'Chờ TT','paid'=>'Đã TT','cancelled'=>'Đã hủy'];
                                ?>
                                <span class="badge-status <?= $statusClasses[$inv['status']] ?? '' ?>"><?= $statusLabels[$inv['status']] ?? $inv['status'] ?></span>
                            </td>
                            <td>
                                <a href="index.php?page=invoices&action=detail&id=<?= $inv['id'] ?>" class="btn btn-sm btn-outline-primary" style="border-radius:8px;">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
