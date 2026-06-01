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
                        <?php if (!empty($invoice['insurance_number'])): ?>
                        <tr><td class="text-muted">Mã số BHYT:</td><td class="fw-bold text-primary"><?= htmlspecialchars($invoice['insurance_number']) ?></td></tr>
                        <?php endif; ?>
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
                        <?php if (!empty($invoice['vnpay_transaction_no'])): ?>
                            <tr><td class="text-muted">Mã GD VNPay:</td><td class="fw-bold text-success"><?= htmlspecialchars($invoice['vnpay_transaction_no']) ?></td></tr>
                        <?php endif; ?>
                        <?php if (!empty($invoice['vnpay_txn_ref'])): ?>
                            <tr><td class="text-muted">Mã Đơn hàng:</td><td class="text-secondary" style="font-size:12px;"><?= htmlspecialchars($invoice['vnpay_txn_ref']) ?></td></tr>
                        <?php endif; ?>
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
                        <td colspan="5" class="text-end fw-bold text-danger">Giảm giá khác:</td>
                        <td style="text-align:right; font-weight:600; color:var(--danger);">-<?= number_format($invoice['discount'], 0, ',', '.') ?>đ</td>
                    </tr>
                    <?php endif; ?>
                    <?php if (!empty($invoice['insurance_rate']) && $invoice['insurance_rate'] > 0): ?>
                    <tr>
                        <td colspan="5" class="text-end fw-bold text-success">BHYT chi trả (<?= number_format($invoice['insurance_rate'], 0) ?>%):</td>
                        <td style="text-align:right; font-weight:600; color:var(--success);">-<?= number_format($invoice['insurance_coverage'], 0, ',', '.') ?>đ</td>
                    </tr>
                    <?php endif; ?>
                    <tr style="background:var(--primary-light);">
                        <td colspan="5" class="text-end fw-bold" style="font-size:16px;">BỆNH NHÂN CẦN TRẢ:</td>
                        <td style="text-align:right; font-weight:700; font-size:18px; color:var(--primary);"><?= number_format($invoice['patient_payment'] > 0 ? $invoice['patient_payment'] : $invoice['final_amount'], 0, ',', '.') ?>đ</td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <!-- Mã QR nhận thuốc tại quầy Dược (hiển thị khi hóa đơn đã thanh toán và có đơn thuốc) -->
        <?php if ($invoice['status'] === 'paid' && !empty($invoice['prescription_id'])): ?>
            <?php
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
            $host = $_SERVER['HTTP_HOST'];
            $script = $_SERVER['SCRIPT_NAME'];
            $baseUrl = "$protocol://$host" . $script;
            $prescriptionUrl = $baseUrl . "?page=prescriptions&action=view&id=" . $invoice['prescription_id'];
            ?>
            <div class="qr-pharmacy-card text-center my-4">
                <h6 class="fw-bold text-success mb-2"><i class="fa-solid fa-qrcode me-1"></i> MÃ QR NHẬN THUỐC (QUẦY DƯỢC)</h6>
                <p class="text-muted small mb-3 no-print">Bệnh nhân mang phiếu này hoặc xuất trình mã để nhận thuốc tại quầy dược.</p>
                <div class="mb-2">
                    <img src="https://quickchart.io/qr?text=<?= urlencode($prescriptionUrl) ?>&size=150" alt="QR Code Dược sĩ" class="qr-code-img img-fluid" style="border: 4px solid white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);" />
                </div>
                <span class="badge-paid-qr">Đã thanh toán hóa đơn</span>
            </div>
        <?php endif; ?>

        <!-- Action Buttons -->
        <div class="d-flex gap-2 flex-wrap no-print">
            <a href="index.php?page=invoices" class="btn btn-outline-secondary" style="border-radius:20px; font-weight:500;">
                <i class="fa-solid fa-arrow-left me-2"></i>Quay lại
            </a>
            <button onclick="window.print()" class="btn btn-outline-primary" style="border-radius:20px; font-weight:500;">
                <i class="fa-solid fa-print me-2"></i>In hóa đơn
            </button>
            <?php if ($invoice['status'] === 'pending'): ?>
                
                <?php if (in_array($_SESSION['user']['role'], ['admin', 'cashier', 'receptionist'])): ?>
                    <button type="button" class="btn btn-success" style="border-radius:20px; font-weight:500;" data-bs-toggle="modal" data-bs-target="#paymentMethodModal">
                        <i class="fa-solid fa-check me-2"></i>Xác nhận thanh toán
                    </button>
                    <a href="index.php?page=invoices&action=cancel&id=<?= $invoice['id'] ?>" class="btn btn-outline-danger" style="border-radius:20px; font-weight:500;" onclick="return confirm('Bạn chắc chắn muốn hủy hóa đơn này?')">
                        <i class="fa-solid fa-ban me-2"></i>Hủy hóa đơn
                    </a>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </div>
</div>

<?php
// Tính số tiền cần trả
$payAmount = $invoice['patient_payment'] > 0 ? $invoice['patient_payment'] : $invoice['final_amount'];
$payAmountFormatted = number_format($payAmount, 0, ',', '.');
$invoiceDesc = "HD" . $invoice['id'] . " NovaCare Hospital";

// MoMo QR format: 2|99|SĐT|||0|0|SốTiền|MôTả|transfer_myqr
// Khi quét bằng app MoMo -> tự động mở màn hình chuyển tiền với số tiền đã điền sẵn
$qrDataMomo = "2|99|0909123456|||0|0|" . intval($payAmount) . "|" . $invoiceDesc . "|transfer_myqr";

// VietQR (VNPay) - Sử dụng API img.vietqr.io để sinh QR chuẩn ngân hàng
// Khi quét bằng bất kỳ app ngân hàng nào -> tự hiện số tiền cần thanh toán
$vietqrBank = "MB";  // Ngân hàng MB Bank (demo)
$vietqrAccount = "0909123456";  // Số tài khoản demo
$vietqrTemplate = "compact2";
$vietqrAmount = intval($payAmount);
$vietqrDesc = urlencode($invoiceDesc);
$vietqrName = urlencode("BENH VIEN NOVACARE");
$vietqrImgUrl = "https://img.vietqr.io/image/{$vietqrBank}-{$vietqrAccount}-{$vietqrTemplate}.png?amount={$vietqrAmount}&addInfo={$vietqrDesc}&accountName={$vietqrName}";
?>

<!-- ==================== MODAL CHỌN PHƯƠNG THỨC THANH TOÁN ==================== -->
<div class="modal fade" id="paymentMethodModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius:20px; border:none; box-shadow: 0 10px 40px rgba(0,0,0,0.1);">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" style="font-size:1.25rem;"><i class="fa-solid fa-wallet me-2 text-primary"></i>Chọn phương thức thanh toán</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 pt-3">
                <p class="text-muted mb-4" style="font-size:0.95rem;">Hóa đơn <span class="fw-bold text-dark">#<?= $invoice['id'] ?></span> - Bệnh nhân cần trả: <span class="fw-bold text-danger fs-5"><?= $payAmountFormatted ?>đ</span>.</p>
                
                <div class="d-grid gap-3">
                    <button type="button" class="btn btn-outline-light text-start d-flex align-items-center justify-content-between p-3 payment-method-btn" onclick="selectPaymentMethod('cash')">
                        <span class="fw-bold text-dark"><i class="fa-solid fa-money-bill-wave me-2 text-success fs-5 align-middle"></i>Tiền mặt</span>
                        <i class="fa-solid fa-chevron-right text-muted"></i>
                    </button>
                    <button type="button" class="btn btn-outline-light text-start d-flex align-items-center justify-content-between p-3 payment-method-btn" onclick="selectPaymentMethod('card')">
                        <span class="fw-bold text-dark"><i class="fa-solid fa-credit-card me-2 text-primary fs-5 align-middle"></i>Thẻ ngân hàng (POS)</span>
                        <i class="fa-solid fa-chevron-right text-muted"></i>
                    </button>
                    <button type="button" class="btn btn-outline-light text-start d-flex align-items-center justify-content-between p-3 payment-method-btn" onclick="selectPaymentMethod('momo')">
                        <span class="fw-bold text-dark"><img src="https://upload.wikimedia.org/wikipedia/vi/f/fe/MoMo_Logo.png" width="24" class="me-2 rounded shadow-sm">Ví MoMo (Quét QR)</span>
                        <i class="fa-solid fa-chevron-right text-muted"></i>
                    </button>
                    <button type="button" class="btn btn-outline-light text-start d-flex align-items-center justify-content-between p-3 payment-method-btn" onclick="selectPaymentMethod('vnpay')">
                        <span class="fw-bold text-dark"><img src="https://vnpay.vn/s1/statics.vnpay.vn/2023/9/06ncktiwd6dc1694418196384.png" width="24" class="me-2 rounded shadow-sm bg-white p-1">VNPay / VietQR</span>
                        <i class="fa-solid fa-chevron-right text-muted"></i>
                    </button>
                    <button type="button" class="btn btn-outline-light text-start d-flex align-items-center justify-content-between p-3 payment-method-btn" onclick="selectPaymentMethod('transfer')">
                        <span class="fw-bold text-dark"><i class="fa-solid fa-money-bill-transfer me-2 text-info fs-5 align-middle"></i>Chuyển khoản thủ công</span>
                        <i class="fa-solid fa-chevron-right text-muted"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
.payment-method-btn {
    border: 2px solid #e2e8f0;
    border-radius: 14px;
    background: white;
    transition: all 0.2s ease;
}
.payment-method-btn:hover {
    border-color: #3b82f6;
    background: #eff6ff;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(59, 130, 246, 0.1);
}
</style>

<?php
// Tính số tiền cần trả
$payAmount = $invoice['patient_payment'] > 0 ? $invoice['patient_payment'] : $invoice['final_amount'];
$payAmountFormatted = number_format($payAmount, 0, ',', '.');
$invoiceDesc = "HD" . $invoice['id'] . " NovaCare Hospital";

// MoMo QR format: 2|99|SĐT|||0|0|SốTiền|MôTả|transfer_myqr
// Khi quét bằng app MoMo -> tự động mở màn hình chuyển tiền với số tiền đã điền sẵn
$qrDataMomo = "2|99|0909123456|||0|0|" . intval($payAmount) . "|" . $invoiceDesc . "|transfer_myqr";

// VietQR (VNPay) - Sử dụng API img.vietqr.io để sinh QR chuẩn ngân hàng
// Khi quét bằng bất kỳ app ngân hàng nào -> tự hiện số tiền cần thanh toán
$vietqrBank = "MB";  // Ngân hàng MB Bank (demo)
$vietqrAccount = "0909123456";  // Số tài khoản demo
$vietqrTemplate = "compact2";
$vietqrAmount = intval($payAmount);
$vietqrDesc = urlencode($invoiceDesc);
$vietqrName = urlencode("BENH VIEN NOVACARE");
$vietqrImgUrl = "https://img.vietqr.io/image/{$vietqrBank}-{$vietqrAccount}-{$vietqrTemplate}.png?amount={$vietqrAmount}&addInfo={$vietqrDesc}&accountName={$vietqrName}";
?>

<!-- ==================== MODAL QR THANH TOÁN ==================== -->
<div class="modal fade" id="qrPaymentModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered" style="max-width: 480px;">
        <div class="modal-content" style="border:none; border-radius:24px; overflow:hidden;">
            
            <!-- MoMo Header -->
            <div id="qrHeader-momo" class="qr-modal-header" style="display:none; background: linear-gradient(135deg, #a50064 0%, #d8247e 50%, #ff6b9d 100%); padding: 28px 24px 20px;">
                <div class="d-flex align-items-center justify-content-center gap-3 mb-2">
                    <img src="https://upload.wikimedia.org/wikipedia/vi/f/fe/MoMo_Logo.png" width="48" height="48" style="border-radius:12px; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
                    <div class="text-white text-start">
                        <h5 class="mb-0 fw-bold">Thanh toán MoMo</h5>
                        <small style="opacity:0.85;">Mở app MoMo → Quét mã QR</small>
                    </div>
                </div>
            </div>

            <!-- VNPay Header -->
            <div id="qrHeader-vnpay" class="qr-modal-header" style="display:none; background: linear-gradient(135deg, #005baa 0%, #0071ce 50%, #0095ff 100%); padding: 28px 24px 20px;">
                <div class="d-flex align-items-center justify-content-center gap-3 mb-2">
                    <img src="https://vnpay.vn/s1/statics.vnpay.vn/2023/9/06ncktiwd6dc1694418196384.png" width="48" height="48" style="border-radius:12px; background:white; padding:4px; box-shadow: 0 4px 12px rgba(0,0,0,0.2);">
                    <div class="text-white text-start">
                        <h5 class="mb-0 fw-bold">Thanh toán VNPay</h5>
                        <small style="opacity:0.85;">Mở app Ngân hàng → Quét mã VietQR</small>
                    </div>
                </div>
            </div>

            <!-- Modal Body -->
            <div class="modal-body text-center" style="padding: 30px 24px;">
                <!-- Thông tin hóa đơn -->
                <div class="mb-4" style="background: #f8fafc; border-radius: 16px; padding: 16px;">
                    <div class="d-flex justify-content-between align-items-center mb-2" style="font-size:14px;">
                        <span class="text-muted">Mã hóa đơn:</span>
                        <span class="fw-bold">#<?= $invoice['id'] ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2" style="font-size:14px;">
                        <span class="text-muted">Bệnh nhân:</span>
                        <span class="fw-bold"><?= htmlspecialchars($invoice['patient_name']) ?></span>
                    </div>
                    <div class="d-flex justify-content-between align-items-center mb-2" style="font-size:14px;">
                        <span class="text-muted">Người thụ hưởng:</span>
                        <span class="fw-bold text-primary">BENH VIEN NOVACARE</span>
                    </div>
                    <hr class="my-2" style="border-color:#e2e8f0;">
                    <div class="d-flex justify-content-between align-items-center">
                        <span class="fw-bold" style="font-size:15px;">Số tiền cần trả:</span>
                        <span class="fw-bold" style="font-size:22px; color:#e11d48;" id="qrPayAmount"><?= $payAmountFormatted ?>đ</span>
                    </div>
                </div>

                <!-- QR Code MoMo -->
                <div class="qr-code-wrapper mb-3" id="qrCodeContainer">
                    <div class="qr-code-frame" id="qrFrame-momo" style="display:none; border: 3px solid #a50064; border-radius: 20px; padding: 16px; position:relative; background:white;">
                        <img src="https://quickchart.io/qr?text=<?= urlencode($qrDataMomo) ?>&size=240&margin=1" 
                             alt="MoMo QR" class="qr-img" style="border-radius:8px; width:240px; height:240px;">
                        <div style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); background:white; border-radius:12px; padding:6px; box-shadow: 0 2px 8px rgba(0,0,0,0.1);">
                            <img src="https://upload.wikimedia.org/wikipedia/vi/f/fe/MoMo_Logo.png" width="36" height="36" style="border-radius:8px;">
                        </div>
                        <div class="mt-2" style="font-size:11px; color:#a50064; font-weight:600;">
                            <i class="fa-solid fa-mobile-screen me-1"></i> Mở MoMo → Quét QR → Số tiền tự động hiện
                        </div>
                    </div>

                    <!-- QR Code VNPay (VietQR chuẩn ngân hàng) -->
                    <div class="qr-code-frame" id="qrFrame-vnpay" style="display:none; border: 3px solid #005baa; border-radius: 20px; padding: 16px; position:relative; background:white;">
                        <img src="<?= $vietqrImgUrl ?>" 
                             alt="VietQR VNPay" class="qr-img" style="border-radius:8px; width:240px; height:auto; min-height:240px;"
                             onerror="this.onerror=null; this.src='https://quickchart.io/qr?text=<?= urlencode("https://me.momo.vn/novacare?a=" . intval($payAmount)) ?>&size=240&margin=1';">
                        <div class="mt-2" style="font-size:11px; color:#005baa; font-weight:600;">
                            <i class="fa-solid fa-building-columns me-1"></i> Mở App Ngân hàng → Quét VietQR → Số tiền tự động hiện
                        </div>
                    </div>
                </div>

                <!-- Trạng thái chờ -->
                <div id="qrWaitingStatus" class="mb-3">
                    <div class="d-flex align-items-center justify-content-center gap-2" style="color:#64748b; font-size:14px;">
                        <div class="spinner-border spinner-border-sm" role="status" id="qrSpinner"></div>
                        <span>Đang chờ bệnh nhân quét mã thanh toán...</span>
                    </div>
                    <div class="mt-2" style="font-size:12px; color:#94a3b8;">
                        Mã QR sẽ hết hạn sau: <span id="qrCountdown" class="fw-bold text-danger">05:00</span>
                    </div>
                </div>

                <!-- Trạng thái thành công (ẩn ban đầu) -->
                <div id="qrSuccessStatus" style="display:none;" class="mb-3">
                    <div class="qr-success-animation">
                        <div class="qr-success-checkmark">
                            <i class="fa-solid fa-circle-check" style="font-size:64px; color:#10b981;"></i>
                        </div>
                        <h5 class="fw-bold mt-3" style="color:#10b981;">Thanh toán thành công!</h5>
                        <p class="text-muted mb-0" style="font-size:14px;">Giao dịch đã được xác nhận</p>
                    </div>
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="modal-footer justify-content-center border-0" style="padding: 0 24px 28px; gap:12px;" id="qrModalFooter">
                <button type="button" class="btn btn-lg" id="btnSimulateSuccess" style="border-radius:16px; font-weight:600; padding: 12px 28px; font-size:15px;" onclick="simulatePaymentSuccess()">
                    <i class="fa-solid fa-check-circle me-2"></i>Giả lập thanh toán thành công
                </button>
                <button type="button" class="btn btn-outline-secondary btn-lg" id="btnChangeMethod" style="border-radius:16px; font-weight:500; padding: 12px 28px; font-size:15px;" onclick="changePaymentMethod()">
                    <i class="fa-solid fa-rotate me-2"></i>Đổi phương thức
                </button>
            </div>

            <!-- Footer sau khi thành công (ẩn ban đầu) -->
            <div class="modal-footer justify-content-center border-0" style="padding: 0 24px 28px; display:none;" id="qrSuccessFooter">
                <button type="button" class="btn btn-success btn-lg" style="border-radius:16px; font-weight:600; padding: 12px 36px; font-size:15px;" onclick="confirmQRPayment()">
                    <i class="fa-solid fa-check me-2"></i>Xác nhận & Đóng
                </button>
            </div>
        </div>
    </div>
</div>

<style>
@media print {
    .sidebar, .top-navbar, .no-print { display: none !important; }
    .main-content { margin-left: 0 !important; padding: 0 !important; }
    .page-content { padding: 0 !important; }
    .content-card { box-shadow: none !important; border: 1px solid #ddd !important; }
    .qr-pharmacy-card {
        max-width: 120px !important;
        padding: 8px !important;
        margin: 15px 0 0 0 !important;
        border: 1px solid #555 !important;
        box-shadow: none !important;
        float: left;
        page-break-inside: avoid;
    }
    .qr-pharmacy-card h6 {
        font-size: 8px !important;
        margin-bottom: 2px !important;
    }
    .qr-pharmacy-card p {
        display: none !important;
    }
    .qr-pharmacy-card img {
        width: 80px !important;
        height: 80px !important;
    }
    .qr-pharmacy-card .badge-paid-qr {
        font-size: 7px !important;
        padding: 2px 6px !important;
        border: 1px solid #555 !important;
        color: black !important;
        background: none !important;
    }
}

/* Glassmorphism styling on screen */
.qr-pharmacy-card {
    background: linear-gradient(135deg, rgba(240, 253, 244, 0.5) 0%, rgba(220, 252, 231, 0.6) 100%);
    border: 1px dashed #10b981;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 10px 25px -5px rgba(16, 185, 129, 0.1), 0 8px 10px -6px rgba(16, 185, 129, 0.05);
    backdrop-filter: blur(8px);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    max-width: 450px;
    margin: 24px auto;
}
.qr-pharmacy-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 25px -5px rgba(16, 185, 129, 0.15), 0 10px 10px -5px rgba(16, 185, 129, 0.08);
    border-color: #059669;
}
.qr-pharmacy-card:hover .qr-code-img {
    transform: scale(1.05);
}
.badge-paid-qr {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    font-weight: 600;
    letter-spacing: 0.5px;
    padding: 6px 16px;
    border-radius: 30px;
    box-shadow: 0 4px 6px -1px rgba(16, 185, 129, 0.3);
    display: inline-block;
}

/* ===== QR Payment Modal Styles ===== */
#qrPaymentModal .modal-content {
    box-shadow: 0 25px 60px rgba(0,0,0,0.15);
}
#qrPaymentModal .qr-code-frame {
    transition: all 0.4s cubic-bezier(0.4, 0, 0.2, 1);
    display: inline-block;
}
#qrPaymentModal .qr-code-frame:hover {
    transform: scale(1.03);
}
#qrPaymentModal .qr-img {
    transition: all 0.3s ease;
}
#btnSimulateSuccess {
    background: linear-gradient(135deg, #10b981, #059669);
    color: white;
    border: none;
    box-shadow: 0 4px 14px rgba(16, 185, 129, 0.35);
    transition: all 0.3s ease;
}
#btnSimulateSuccess:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 20px rgba(16, 185, 129, 0.45);
    background: linear-gradient(135deg, #059669, #047857);
    color: white;
}
.qr-success-animation {
    animation: qrSuccessPop 0.5s cubic-bezier(0.175, 0.885, 0.32, 1.275);
}
@keyframes qrSuccessPop {
    0% { transform: scale(0.3); opacity: 0; }
    50% { transform: scale(1.08); }
    100% { transform: scale(1); opacity: 1; }
}
@keyframes qrPulse {
    0%, 100% { box-shadow: 0 0 0 0 rgba(16, 185, 129, 0.4); }
    50% { box-shadow: 0 0 0 12px rgba(16, 185, 129, 0); }
}
.qr-success-checkmark {
    animation: qrPulse 1.5s ease infinite;
    display: inline-block;
    border-radius: 50%;
}
</style>

<script>
// ===== QR Payment Logic =====
let currentQRMethod = '';
let countdownInterval = null;
let countdownSeconds = 300; // 5 phút

function openQRPayment(method) {
    currentQRMethod = method;
    countdownSeconds = 300;

    // Ẩn tất cả header & frame
    document.querySelectorAll('.qr-modal-header').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.qr-code-frame').forEach(el => el.style.display = 'none');

    // Hiện header & frame tương ứng
    document.getElementById('qrHeader-' + method).style.display = 'block';
    document.getElementById('qrFrame-' + method).style.display = 'inline-block';

    // Reset trạng thái
    document.getElementById('qrWaitingStatus').style.display = 'block';
    document.getElementById('qrSuccessStatus').style.display = 'none';
    document.getElementById('qrModalFooter').style.display = 'flex';
    document.getElementById('qrSuccessFooter').style.display = 'none';
    document.getElementById('qrCodeContainer').style.display = 'block';

    // Bắt đầu đếm ngược
    startCountdown();

    // Mở modal
    var modal = new bootstrap.Modal(document.getElementById('qrPaymentModal'));
    modal.show();
}

function startCountdown() {
    if (countdownInterval) clearInterval(countdownInterval);
    updateCountdownDisplay();
    countdownInterval = setInterval(function() {
        countdownSeconds--;
        if (countdownSeconds <= 0) {
            clearInterval(countdownInterval);
            document.getElementById('qrCountdown').textContent = 'Hết hạn!';
            document.getElementById('qrCountdown').style.color = '#dc2626';
        } else {
            updateCountdownDisplay();
        }
    }, 1000);
}

function updateCountdownDisplay() {
    var m = Math.floor(countdownSeconds / 60);
    var s = countdownSeconds % 60;
    document.getElementById('qrCountdown').textContent = 
        String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0');
}

function simulatePaymentSuccess() {
    if (countdownInterval) clearInterval(countdownInterval);

    // Ẩn QR + chờ, hiện thành công
    document.getElementById('qrCodeContainer').style.display = 'none';
    document.getElementById('qrWaitingStatus').style.display = 'none';
    document.getElementById('qrSuccessStatus').style.display = 'block';
    document.getElementById('qrModalFooter').style.display = 'none';
    document.getElementById('qrSuccessFooter').style.display = 'flex';
}

function confirmQRPayment() {
    // Gọi API đánh dấu đã thanh toán
    postAction('index.php?page=invoices&action=markPaid&id=<?= $invoice['id'] ?>&method=' + currentQRMethod);
}

function selectPaymentMethod(method) {
    // Đóng modal chọn phương thức
    var pmModalEl = document.getElementById('paymentMethodModal');
    var pmModal = bootstrap.Modal.getInstance(pmModalEl);
    if (pmModal) pmModal.hide();

    if (method === 'momo' || method === 'vnpay') {
        // Đợi modal cũ đóng rồi mở modal QR
        setTimeout(() => {
            openQRPayment(method);
        }, 400);
    } else {
        // Phương thức thường
        var methodNames = {'cash':'Tiền mặt', 'card':'Thẻ ngân hàng (POS)', 'transfer':'Chuyển khoản thủ công'};
        if (confirm('Xác nhận thanh toán hóa đơn này bằng ' + methodNames[method] + '?')) {
            postAction('index.php?page=invoices&action=markPaid&id=<?= $invoice['id'] ?>&method=' + method);
        }
    }
}

function changePaymentMethod() {
    if (countdownInterval) clearInterval(countdownInterval);
    // Đóng modal QR hiện tại
    var modalEl = document.getElementById('qrPaymentModal');
    var modal = bootstrap.Modal.getInstance(modalEl);
    if (modal) modal.hide();

    // Mở lại modal chọn phương thức thanh toán
    setTimeout(function() {
        var pmModal = new bootstrap.Modal(document.getElementById('paymentMethodModal'));
        pmModal.show();
    }, 400);
}

// Cleanup khi modal đóng
document.getElementById('qrPaymentModal')?.addEventListener('hidden.bs.modal', function() {
    if (countdownInterval) clearInterval(countdownInterval);
});
</script>

