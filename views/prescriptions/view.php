<!-- Chi tiết Đơn thuốc -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-custom alert-dismissible fade show no-print">
        <i class="bi bi-check-circle-fill"></i> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-custom alert-dismissible fade show no-print">
        <i class="bi bi-exclamation-triangle-fill"></i> <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0 rounded-4 mb-4 mx-auto" style="max-width: 900px;" data-aos="fade-up">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center bg-white border-bottom-0 pt-4 pb-3 px-4 no-print">
        <div class="mb-3 mb-md-0">
            <h4 class="m-0 text-primary fw-bold"><i class="fa-solid fa-file-prescription me-2"></i>Chi tiết Đơn thuốc #<?= $prescription['id'] ?></h4>
            <p class="text-muted small mt-1 mb-0">Xem và in ấn đơn thuốc cho bệnh nhân.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="index.php?page=prescriptions" class="btn btn-light border fw-medium rounded-pill shadow-sm px-4 py-2">
                <i class="fa-solid fa-arrow-left me-1"></i> Quay lại
            </a>
            <button onclick="window.print()" class="btn btn-primary fw-bold rounded-pill shadow-sm px-4 py-2">
                <i class="fa-solid fa-print me-1"></i> In đơn thuốc
            </button>
        </div>
    </div>
    
    <div class="card-body px-4 pb-4 pt-3">
        <!-- Header chỉ hiển thị khi in -->
        <div class="print-only clinic-header mb-4 pb-2 border-bottom border-dark">
            <div class="row align-items-center">
                <div class="col-8">
                    <h5 class="fw-bold text-uppercase mb-1" style="font-size: 16px; color: #1e3c72;"><i class="fa-solid fa-hospital me-2"></i>Hệ thống Y tế NovaCare</h5>
                    <p class="mb-0 text-muted" style="font-size: 11px; line-height: 1.4;">Địa chỉ: 123 Đường Ba Tháng Hai, Quận 10, TP. Hồ Chí Minh</p>
                    <p class="mb-0 text-muted" style="font-size: 11px; line-height: 1.4;">Hotline: 1900 6060 | Email: contact@novacare.vn</p>
                </div>
                <div class="col-4 text-end">
                    <div class="fw-bold" style="font-size: 13px;">ĐƠN THUỐC KHÁM BỆNH</div>
                    <div class="text-muted" style="font-size: 11px;">Mã đơn: #<?= $prescription['id'] ?></div>
                    <div class="text-muted" style="font-size: 11px;">Ngày: <?= date('d/m/Y', strtotime($prescription['created_at'])) ?></div>
                </div>
            </div>
        </div>

        <div class="print-only prescription-title text-center fw-bold mb-4" style="font-size: 20px; text-transform: uppercase; letter-spacing: 0.5px;">Đơn Thuốc</div>

        <!-- Thông tin bệnh nhân & bác sĩ -->
        <div class="row mb-4">
            <div class="col-md-6 col-sm-6">
                <p class="mb-2"><strong>Bệnh nhân:</strong> <?= htmlspecialchars($prescription['patient_name']) ?></p>
                <p class="mb-2"><strong>Bác sĩ kê đơn:</strong> <?= htmlspecialchars($prescription['doctor_name']) ?></p>
            </div>
            <div class="col-md-6 col-sm-6">
                <p class="mb-2"><strong>Chẩn đoán:</strong> <?= htmlspecialchars($prescription['diagnosis'] ?? 'Bình thường') ?></p>
                <p class="mb-2"><strong class="no-print">Trạng thái: </strong> 
                    <?php
                    $status = $prescription['status'] ?? 'draft';
                    if ($status === 'draft') {
                        echo '<span class="badge bg-warning text-dark no-print"><i class="bi bi-clock-history me-1"></i>Chưa thanh toán</span>';
                    } elseif ($status === 'paid') {
                        echo '<span class="badge bg-primary no-print"><i class="bi bi-hourglass-split me-1"></i>Chờ giao thuốc</span>';
                    } elseif ($status === 'dispensed') {
                        echo '<span class="badge bg-success no-print"><i class="bi bi-check-circle-fill me-1"></i>Đã giao thuốc</span>';
                    } elseif ($status === 'cancelled') {
                        echo '<span class="badge bg-danger no-print"><i class="bi bi-x-circle me-1"></i>Đã hủy</span>';
                    }
                    ?>
                </p>
                <p class="print-only mb-2"><strong>Thời gian:</strong> <?= date('d/m/Y H:i', strtotime($prescription['created_at'])) ?></p>
            </div>
        </div>

        <!-- Cảnh báo & Hóa đơn Bệnh nhân dành cho Dược sĩ/Admin -->
        <?php if ($user && in_array($user['role'], ['admin', 'pharmacist'])): ?>
            <?php if ($status === 'draft'): ?>
                <div class="alert alert-danger d-flex align-items-center p-3 mb-4 border-2 no-print" style="border-radius: 12px; background-color: #fff5f5; border-color: #feb2b2;">
                    <i class="fa-solid fa-triangle-exclamation text-danger fs-3 me-3"></i>
                    <div>
                        <h6 class="alert-heading fw-bold text-danger mb-1" style="font-size: 14px;">Hóa đơn chưa thanh toán!</h6>
                        <p class="mb-0 text-dark" style="font-size: 13px;">Yêu cầu bệnh nhân thanh toán tại quầy thu ngân trước khi lĩnh/nhận thuốc.</p>
                    </div>
                </div>
            <?php elseif (in_array($status, ['paid', 'dispensed'])): ?>
                <?php
                require_once __DIR__ . '/../../models/Invoice.php';
                $invoiceModel = new Invoice();
                $invoice = $invoiceModel->findByPrescriptionId($prescription['id']);
                if ($invoice):
                ?>
                <div class="card border-0 mb-4 no-print" style="background: linear-gradient(135deg, #f0fdf4 0%, #e6fcf5 100%); border-left: 5px solid #2ecc71 !important; border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.03);">
                    <div class="card-body p-4">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h6 class="fw-bold text-success m-0" style="font-size: 15px;">
                                <i class="fa-solid fa-file-invoice-dollar me-2"></i>Thông tin Hóa đơn bệnh nhân đã thanh toán
                            </h6>
                            <span class="badge bg-success px-3 py-1.5 rounded-pill" style="font-size: 11px; font-weight: 600;">
                                <i class="fa-solid fa-check-circle me-1"></i> Đã thanh toán
                            </span>
                        </div>
                        <div class="row g-3" style="font-size: 13.5px;">
                            <div class="col-sm-6">
                                <p class="mb-1 text-muted">Mã hóa đơn:</p>
                                <p class="mb-3 fw-bold text-dark">#<?= $invoice['id'] ?></p>
                                
                                <p class="mb-1 text-muted">Phương thức thanh toán:</p>
                                <p class="mb-0 fw-bold text-dark text-capitalize">
                                    <?php 
                                    $methods = ['cash' => 'Tiền mặt', 'card' => 'Thẻ thanh toán', 'momo' => 'Ví MoMo', 'vnpay' => 'Cổng VNPay', 'transfer' => 'Chuyển khoản'];
                                    echo $methods[$invoice['payment_method']] ?? $invoice['payment_method'];
                                    ?>
                                </p>
                            </div>
                            <div class="col-sm-6">
                                <p class="mb-1 text-muted">Bảo hiểm chi trả (BHYT):</p>
                                <p class="mb-3 fw-bold text-dark"><?= number_format($invoice['insurance_coverage'], 0, ',', '.') ?>đ (Miễn giảm <?= number_format($invoice['insurance_rate'], 0) ?>%)</p>
                                
                                <p class="mb-1 text-muted">Số tiền bệnh nhân đã nộp:</p>
                                <p class="mb-0 fw-bold text-primary" style="font-size: 16px;"><?= number_format($invoice['patient_payment'], 0, ',', '.') ?>đ</p>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        <?php endif; ?>

        <?php if (!empty($prescription['notes'])): ?>
        <div class="alert alert-info alert-custom mb-4 no-print">
            <i class="bi bi-info-circle"></i> <strong>Ghi chú bác sĩ:</strong> <?= htmlspecialchars($prescription['notes']) ?>
        </div>
        <?php endif; ?>

        <h6 class="mb-3 fw-bold"><i class="fa-solid fa-capsules me-2 text-primary no-print"></i>Danh sách chỉ định thuốc</h6>
        <div class="table-responsive mb-4" style="overflow-x: auto; white-space: nowrap;">
            <table class="table table-hover table-bordered align-middle mb-0">
                <thead class="table-light">
                    <tr>
                        <th class="text-center text-muted" width="5%">#</th>
                        <th width="20%">Tên thuốc / Hàm lượng</th>
                        <th width="12%">Liều lượng</th>
                        <th width="12%">Thời gian</th>
                        <th width="25%">Hướng dẫn sử dụng</th>
                        <th class="text-center" width="8%">Số lượng</th>
                        <th class="text-end" width="10%">Đơn giá</th>
                        <th class="text-end" width="12%">Thành tiền</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    <?php $total = 0; ?>
                    <?php foreach ($items as $idx => $item): ?>
                    <?php 
                    $qty = $item['quantity'] ?? 1;
                    $price = $item['price'] ?? 0;
                    $amount = $qty * $price;
                    $total += $amount;
                    ?>
                    <tr>
                        <td class="text-center text-muted"><?= $idx + 1 ?></td>
                        <td><strong class="text-dark"><?= htmlspecialchars($item['medicine_name']) ?></strong></td>
                        <td class="text-secondary"><?= htmlspecialchars($item['dosage'] ?? '') ?></td>
                        <td class="text-secondary"><?= htmlspecialchars($item['duration'] ?? '') ?></td>
                        <td class="text-truncate text-secondary" style="max-width: 250px;" title="<?= htmlspecialchars($item['instructions'] ?? '') ?>">
                            <?= htmlspecialchars($item['instructions'] ?? '') ?>
                        </td>
                        <td class="text-center fw-medium"><?= $qty ?></td>
                        <td class="text-end text-secondary"><?= number_format($price, 0, ',', '.') ?>đ</td>
                        <td class="text-end fw-bold text-dark"><?= number_format($amount, 0, ',', '.') ?>đ</td>
                    </tr>
                    <?php endforeach; ?>
                    <tr class="table-light">
                        <td colspan="7" class="text-end fw-bold text-dark border-end-0">Tổng cộng:</td>
                        <td class="text-end fw-bold text-primary fs-6 border-start-0"><?= number_format($total, 0, ',', '.') ?>đ</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Mã QR Code thanh toán -->
        <?php if ($status === 'draft'): ?>
            <?php
            $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
            $host = $_SERVER['HTTP_HOST'];
            $script = $_SERVER['SCRIPT_NAME'];
            $baseUrl = "$protocol://$host" . $script;
            $billingUrl = $baseUrl . "?page=invoices&action=create&prescription_id=" . $prescription['id'];
            ?>
            <div class="qr-payment-card text-center my-4">
                <h6 class="fw-bold text-warning mb-2"><i class="fa-solid fa-qrcode me-1"></i> MÃ QR THANH TOÁN (QUẦY THU NGÂN)</h6>
                <p class="text-muted small mb-3 no-print">Vui lòng quét mã này tại quầy thu ngân hoặc xuất trình để thanh toán và lĩnh thuốc.</p>
                <div class="mb-2">
                    <img src="https://quickchart.io/qr?text=<?= urlencode($billingUrl) ?>&size=150" alt="QR Code Thanh Toán" class="qr-code-img img-fluid" style="border: 4px solid white; border-radius: 8px; box-shadow: 0 2px 8px rgba(0,0,0,0.05);" />
                </div>
                <span class="badge-unpaid">Chờ thanh toán hóa đơn</span>
            </div>
        <?php endif; ?>

        <!-- Khối chữ ký khi in đơn thuốc -->
        <div class="print-only print-signatures mt-5 pt-4">
            <div class="row text-center">
                <div class="col-6">
                    <p class="fw-bold mb-1">Bệnh nhân</p>
                    <p class="text-muted small mb-0" style="font-size: 10px;">(Ký và ghi rõ họ tên)</p>
                    <div style="height: 65px;"></div>
                    <p class="fw-bold" style="font-size: 13px;"><?= htmlspecialchars($prescription['patient_name']) ?></p>
                </div>
                <div class="col-6">
                    <p class="text-muted small mb-1" style="font-size: 11px;">Ngày ..... tháng ..... năm 20...</p>
                    <p class="fw-bold mb-1">Bác sĩ điều trị</p>
                    <p class="text-muted small mb-0" style="font-size: 10px;">(Ký và ghi rõ họ tên)</p>
                    <div style="height: 65px;"></div>
                    <p class="fw-bold" style="font-size: 13px;"><?= htmlspecialchars($prescription['doctor_name']) ?></p>
                </div>
            </div>
        </div>

        <?php 
        if ($user): 
        ?>
            <!-- Giao thuốc trực tiếp khi đã thanh toán (paid) -->
            <?php if (($user['role'] === 'pharmacist' || $user['role'] === 'admin') && $status === 'paid'): ?>
                <div class="mt-4 pt-3 border-top w-100 no-print">
                    <form method="POST" action="index.php?page=prescriptions&action=dispense" class="p-4 border-0" style="background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%); border-radius: 16px; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
                        <?= Security::csrfField(); ?>
                        <input type="hidden" name="id" value="<?= $prescription['id'] ?>">
                        <h6 class="fw-bold mb-2 text-dark"><i class="fa-solid fa-prescription-bottle-medical me-2 text-success"></i>Tiến hành kiểm tra & Giao thuốc</h6>
                        <p class="text-muted small mb-3">Vui lòng lấy đủ số lượng thuốc chỉ định trên danh sách trước khi nhấn nút giao thuốc dưới đây. Việc này sẽ trực tiếp trừ số lượng trong kho vật lý.</p>
                        <div class="text-end">
                            <button type="submit" class="btn btn-success px-4 py-2" style="border-radius: 24px; font-weight: 600; box-shadow: 0 4px 12px rgba(46, 204, 113, 0.2);">
                                <i class="fa-solid fa-truck-ramp-box me-1"></i> Xác nhận đã giao thuốc
                            </button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>

            <div class="d-flex justify-content-end gap-2 mt-3 no-print pt-3 border-top">
                <!-- Nút hủy đơn thuốc cho Bác sĩ / Admin khi đơn chưa giao (draft hoặc paid) -->
                <?php if (($user['role'] === 'doctor' || $user['role'] === 'admin') && in_array($status, ['draft', 'paid'])): ?>
                    <form method="POST" action="index.php?page=prescriptions&action=cancel" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn thuốc này và giải phóng tồn kho đã đặt trước?');">
                        <?= Security::csrfField(); ?>
                        <input type="hidden" name="id" value="<?= $prescription['id'] ?>">
                        <button type="submit" class="btn btn-outline-danger px-4" style="border-radius: 20px; font-weight: 500;">
                            <i class="bi bi-x-circle me-1"></i> Hủy đơn thuốc
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
.print-only { display: none !important; }

@media print {
    .no-print, .sidebar, .top-navbar, .btn-action, .btn, form, .alert, .card-header { display: none !important; }
    .main-content { margin-left: 0 !important; padding: 0 !important; }
    .page-content { padding: 0 !important; }
    
    .content-card {
        max-width: 100% !important;
        box-shadow: none !important;
        border: none !important;
        padding: 0 !important;
        margin: 0 !important;
        background: white !important;
        color: black !important;
    }
    
    .print-only { display: block !important; }
    .print-only.prescription-title { display: block !important; text-align: center; }
    .print-only.print-signatures { display: block !important; }
    .print-only.clinic-header { display: block !important; }
    
    .table-wrapper {
        box-shadow: none !important;
        border: none !important;
    }
    
    table.data-table {
        border-collapse: collapse !important;
        width: 100% !important;
    }
    
    table.data-table th, table.data-table td {
        border: 1px solid #333 !important;
        padding: 6px 8px !important;
        color: black !important;
        font-size: 12px !important;
    }
    
    .qr-payment-card {
        max-width: 120px !important;
        padding: 8px !important;
        margin: 15px 0 0 0 !important;
        border: 1px solid #555 !important;
        box-shadow: none !important;
        float: left;
        page-break-inside: avoid;
    }
    
    .qr-payment-card h6 {
        font-size: 8px !important;
        margin-bottom: 2px !important;
    }
    
    .qr-payment-card p {
        display: none !important;
    }
    
    .qr-payment-card img {
        width: 80px !important;
        height: 80px !important;
    }
    
    .qr-payment-card .badge-unpaid {
        font-size: 7px !important;
        padding: 2px 6px !important;
        border: 1px solid #555 !important;
        color: black !important;
        background: none !important;
    }
}

/* Glassmorphism screen styling */
.qr-payment-card {
    background: linear-gradient(135deg, rgba(254, 243, 199, 0.4) 0%, rgba(253, 242, 233, 0.6) 100%);
    border: 1px dashed #f59e0b;
    border-radius: 16px;
    padding: 24px;
    box-shadow: 0 10px 25px -5px rgba(245, 158, 11, 0.1), 0 8px 10px -6px rgba(245, 158, 11, 0.05);
    backdrop-filter: blur(8px);
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    max-width: 450px;
    margin: 24px auto;
}
.qr-payment-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 20px 25px -5px rgba(245, 158, 11, 0.15), 0 10px 10px -5px rgba(245, 158, 11, 0.08);
    border-color: #d97706;
}
.qr-code-img {
    transition: transform 0.4s cubic-bezier(0.175, 0.885, 0.32, 1.275);
    cursor: pointer;
}
.qr-payment-card:hover .qr-code-img {
    transform: scale(1.05);
}
.badge-unpaid {
    background: linear-gradient(135deg, #f59e0b, #d97706);
    color: white;
    font-weight: 600;
    letter-spacing: 0.5px;
    padding: 6px 16px;
    border-radius: 30px;
    box-shadow: 0 4px 6px -1px rgba(245, 158, 11, 0.3);
    display: inline-block;
}
</style>