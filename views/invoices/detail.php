<!-- Invoice Detail -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-custom alert-dismissible fade show">
        <i class="fa-solid fa-check-circle"></i> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php
$statusClasses = ['pending'=>'badge-pending','paid'=>'badge-confirmed','cancelled'=>'badge-cancelled'];
$statusLabels = ['pending'=>'Chờ thanh toán','paid'=>'Đã thanh toán','cancelled'=>'Đã hủy'];
$methodLabels = ['cash'=>'💵 Tiền mặt','card'=>'💳 Thẻ','momo'=>'📱 MoMo','vnpay'=>'🏦 VNPay','transfer'=>'🔄 Chuyển khoản'];
?>

<div class="content-card" data-aos="fade-up" style="border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.03);" id="printArea">
    <!-- Invoice Header -->
    <div class="card-header bg-white border-bottom pt-4 pb-3">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h4 class="m-0" style="font-weight:700;"><i class="fa-solid fa-file-invoice-dollar me-2 text-primary"></i>Hóa đơn #<?= $invoice['id'] ?></h4>
                <p class="text-muted mb-0" style="font-size:13px;">Ngày tạo: <?= date('d/m/Y H:i', strtotime($invoice['created_at'])) ?></p>
            </div>
            <span class="badge-status <?= $statusClasses[$invoice['status']] ?? '' ?>" style="font-size:14px; padding:8px 20px;">
                <?= $statusLabels[$invoice['status']] ?? $invoice['status'] ?>
            </span>
        </div>
    </div>

    <div class="card-body">
        <!-- Patient & Payment Info -->
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="p-3" style="background:var(--gray-50); border-radius:12px;">
                    <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-user me-1"></i> Thông tin bệnh nhân</h6>
                    <table class="table table-borderless mb-0" style="font-size:14px;">
                        <tr><td class="text-muted" style="width:120px;">Họ tên:</td><td class="fw-bold"><?= htmlspecialchars($invoice['patient_name']) ?></td></tr>
                        <tr><td class="text-muted">SĐT:</td><td><?= htmlspecialchars($invoice['patient_phone'] ?? '-') ?></td></tr>
                        <tr><td class="text-muted">Email:</td><td><?= htmlspecialchars($invoice['patient_email'] ?? '-') ?></td></tr>
                        <tr><td class="text-muted">Địa chỉ:</td><td><?= htmlspecialchars($invoice['patient_address'] ?? '-') ?></td></tr>
                    </table>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-3" style="background:var(--gray-50); border-radius:12px;">
                    <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-credit-card me-1"></i> Thông tin thanh toán</h6>
                    <table class="table table-borderless mb-0" style="font-size:14px;">
                        <tr><td class="text-muted" style="width:140px;">Phương thức:</td><td class="fw-bold"><?= $methodLabels[$invoice['payment_method']] ?? $invoice['payment_method'] ?></td></tr>
                        <tr><td class="text-muted">Người tạo:</td><td><?= htmlspecialchars($invoice['created_by_name'] ?? '-') ?></td></tr>
                        <?php if ($invoice['notes']): ?>
                        <tr><td class="text-muted">Ghi chú:</td><td><?= htmlspecialchars($invoice['notes']) ?></td></tr>
                        <?php endif; ?>
                    </table>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <h6 class="fw-bold mb-3"><i class="fa-solid fa-list me-1"></i> Chi tiết hóa đơn</h6>
        <div class="table-wrapper mb-4">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Loại</th>
                        <th>Mô tả</th>
                        <th style="text-align:center;">Số lượng</th>
                        <th style="text-align:right;">Đơn giá</th>
                        <th style="text-align:right;">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($items)): ?>
                        <tr><td colspan="6" class="text-center text-muted py-3">Không có chi tiết</td></tr>
                    <?php else: ?>
                        <?php foreach ($items as $idx => $item): ?>
                        <tr>
                            <td><?= $idx + 1 ?></td>
                            <td>
                                <?php
                                $type = 'other';
                                if (!empty($item['service_id'])) $type = 'service';
                                elseif (!empty($item['medicine_id'])) $type = 'medicine';
                                elseif (!empty($item['room_id'])) $type = 'room';
                                
                                $typeLabels = ['service'=>'🏥 Dịch vụ','medicine'=>'💊 Thuốc','room'=>'🛏️ Phòng','other'=>'📝 Khác'];
                                echo $typeLabels[$type];
                                ?>
                            </td>
                            <td><?= htmlspecialchars($item['description']) ?></td>
                            <td style="text-align:center;"><?= $item['quantity'] ?></td>
                            <td style="text-align:right;"><?= number_format($item['unit_price'], 0, ',', '.') ?>đ</td>
                            <td style="text-align:right; font-weight:600;"><?= number_format($item['amount'], 0, ',', '.') ?>đ</td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan="5" class="text-end fw-bold">Tổng cộng:</td>
                        <td style="text-align:right; font-weight:600;"><?= number_format($invoice['total_amount'], 0, ',', '.') ?>đ</td>
                    </tr>
                    <?php if ($invoice['discount'] > 0): ?>
                    <tr>
                        <td colspan="5" class="text-end fw-bold text-danger">Giảm giá:</td>
                        <td style="text-align:right; font-weight:600; color:var(--danger);">-<?= number_format($invoice['discount'], 0, ',', '.') ?>đ</td>
                    </tr>
                    <?php endif; ?>
                    <tr style="background:var(--primary-light);">
                        <td colspan="5" class="text-end fw-bold" style="font-size:17px;">THÀNH TIỀN:</td>
                        <td style="text-align:right; font-weight:700; font-size:17px; color:var(--primary);"><?= number_format($invoice['final_amount'], 0, ',', '.') ?>đ</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Action Buttons -->
        <div class="d-flex gap-2 flex-wrap no-print">
            <a href="index.php?page=invoices" class="btn btn-outline-secondary" style="border-radius:20px; font-weight:500;">
                <i class="fa-solid fa-arrow-left me-2"></i>Quay lại
            </a>
            <button onclick="window.print()" class="btn btn-outline-primary" style="border-radius:20px; font-weight:500;">
                <i class="fa-solid fa-print me-2"></i>In hóa đơn
            </button>
            <?php if ($invoice['status'] === 'pending' && in_array($_SESSION['user']['role'], ['admin', 'receptionist'])): ?>
                <div class="dropdown">
                    <button class="btn btn-success dropdown-toggle" style="border-radius:20px; font-weight:500;" data-bs-toggle="dropdown">
                        <i class="fa-solid fa-check me-2"></i>Thanh toán
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="postAction('index.php?page=invoices&action=markPaid&id=<?= $invoice['id'] ?>&method=cash')">💵 Tiền mặt</a></li>
                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="postAction('index.php?page=invoices&action=markPaid&id=<?= $invoice['id'] ?>&method=card')">💳 Thẻ ngân hàng</a></li>
                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="postAction('index.php?page=invoices&action=markPaid&id=<?= $invoice['id'] ?>&method=momo')">📱 MoMo</a></li>
                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="postAction('index.php?page=invoices&action=markPaid&id=<?= $invoice['id'] ?>&method=vnpay')">🏦 VNPay</a></li>
                        <li><a class="dropdown-item" href="javascript:void(0)" onclick="postAction('index.php?page=invoices&action=markPaid&id=<?= $invoice['id'] ?>&method=transfer')">🔄 Chuyển khoản</a></li>
                    </ul>
                </div>
                <a href="index.php?page=invoices&action=cancel&id=<?= $invoice['id'] ?>" class="btn btn-outline-danger" style="border-radius:20px; font-weight:500;" onclick="return confirm('Bạn chắc chắn muốn hủy hóa đơn này?')">
                    <i class="fa-solid fa-ban me-2"></i>Hủy hóa đơn
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<style>
@media print {
    .sidebar, .top-navbar, .no-print { display: none !important; }
    .main-content { margin-left: 0 !important; padding: 0 !important; }
    .page-content { padding: 0 !important; }
    .content-card { box-shadow: none !important; border: 1px solid #ddd !important; }
}
</style>

