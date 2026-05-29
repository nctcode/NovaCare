<!-- Chi tiết Đơn thuốc -->
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

<div class="content-card" style="max-width: 800px;">
    <div class="card-header">
        <h5><i class="bi bi-file-earmark-medical-fill me-2"></i>Chi tiết Đơn thuốc #<?= $prescription['id'] ?></h5>
        <a href="index.php?page=prescriptions" class="btn-action btn-edit">
            <i class="bi bi-arrow-left"></i> Quay lại
        </a>
    </div>
    <div class="card-body">
        <div class="row mb-4">
            <div class="col-md-6">
                <p><strong>Bệnh nhân:</strong> <?= htmlspecialchars($prescription['patient_name']) ?></p>
                <p><strong>Bác sĩ:</strong> <?= htmlspecialchars($prescription['doctor_name']) ?></p>
            </div>
            <div class="col-md-6">
                <p><strong>Chẩn đoán:</strong> <?= htmlspecialchars($prescription['diagnosis'] ?? '') ?></p>
                <p><strong>Ngày tạo:</strong> <?= date('d/m/Y H:i', strtotime($prescription['created_at'])) ?></p>
                <p><strong>Trạng thái:</strong> 
                    <?php
                    $status = $prescription['status'] ?? 'draft';
                    if ($status === 'draft') {
                        echo '<span class="badge bg-warning text-dark"><i class="bi bi-clock-history me-1"></i>Chưa thanh toán</span>';
                    } elseif ($status === 'paid') {
                        echo '<span class="badge bg-info text-dark"><i class="bi bi-shield-check me-1"></i>Chờ duyệt</span>';
                    } elseif ($status === 'approved') {
                        echo '<span class="badge bg-primary"><i class="bi bi-check-circle me-1"></i>Chờ giao thuốc</span>';
                    } elseif ($status === 'dispensed') {
                        echo '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Đã giao thuốc</span>';
                    } elseif ($status === 'cancelled') {
                        echo '<span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Đã hủy</span>';
                    }
                    ?>
                </p>
            </div>
        </div>

        <?php if (!empty($prescription['approved_by_name'])): ?>
        <div class="p-3 mb-4" style="background:#eafaf1; border-left: 4px solid #2ecc71; border-radius: 8px;">
            <p class="mb-1 text-success fw-bold"><i class="bi bi-shield-check"></i> Đơn thuốc đã được Dược sĩ duyệt</p>
            <p class="mb-1" style="font-size:13px;"><strong>Dược sĩ duyệt:</strong> <?= htmlspecialchars($prescription['approved_by_name']) ?> vào lúc <?= date('d/m/Y H:i', strtotime($prescription['approved_at'])) ?></p>
            <?php if (!empty($prescription['pharmacist_notes'])): ?>
            <p class="mb-0" style="font-size:13px;"><strong>Ghi chú duyệt thuốc:</strong> <?= htmlspecialchars($prescription['pharmacist_notes']) ?></p>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php if (!empty($prescription['notes'])): ?>
        <div class="alert alert-info alert-custom mb-4">
            <i class="bi bi-info-circle"></i> <strong>Ghi chú bác sĩ:</strong> <?= htmlspecialchars($prescription['notes']) ?>
        </div>
        <?php endif; ?>

        <h6 class="mb-3"><i class="bi bi-capsule me-2"></i>Danh sách thuốc</h6>
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Tên thuốc</th>
                        <th>Liều lượng</th>
                        <th>Thời gian</th>
                        <th>Hướng dẫn</th>
                        <th>Giá (VNĐ)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $total = 0; ?>
                    <?php foreach ($items as $idx => $item): ?>
                    <tr>
                        <td><?= $idx + 1 ?></td>
                        <td><strong><?= htmlspecialchars($item['medicine_name']) ?></strong></td>
                        <td><?= htmlspecialchars($item['dosage'] ?? '') ?></td>
                        <td><?= htmlspecialchars($item['duration'] ?? '') ?></td>
                        <td><?= htmlspecialchars($item['instructions'] ?? '') ?></td>
                        <td><?= number_format($item['price'] ?? 0, 0, ',', '.') ?></td>
                    </tr>
                    <?php $total += ($item['price'] ?? 0); ?>
                    <?php endforeach; ?>
                    <tr>
                        <td colspan="5" class="text-end fw-bold">Tổng cộng:</td>
                        <td class="fw-bold text-primary"><?= number_format($total, 0, ',', '.') ?> VNĐ</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Action Buttons -->
        <?php 
        $user = $_SESSION['user'] ?? null;
        if ($user): 
        ?>
            <!-- Bọc khối duyệt thuốc riêng để căn lề đẹp mắt -->
            <?php if (($user['role'] === 'pharmacist' || $user['role'] === 'admin') && $status === 'paid'): ?>
                <div class="mt-4 pt-3 border-top w-100">
                    <form method="POST" action="index.php?page=prescriptions&action=approve" class="p-3 border rounded" style="background: #f8fafc; border-radius: 12px !important;">
                        <?= Security::csrfField(); ?>
                        <input type="hidden" name="id" value="<?= $prescription['id'] ?>">
                        <h6 class="fw-bold mb-2 text-info"><i class="bi bi-shield-check me-1"></i>Duyệt đơn thuốc này</h6>
                        <div class="mb-3">
                            <label class="form-label text-muted small mb-1">Ghi chú duyệt thuốc (Không bắt buộc)</label>
                            <textarea name="pharmacist_notes" class="form-control form-control-sm" rows="2" placeholder="Nhập dặn dò cách dùng thuốc, lưu ý cho bệnh nhân..."></textarea>
                        </div>
                        <div class="text-end">
                            <button type="submit" class="btn btn-info btn-sm px-4 text-dark fw-bold" style="border-radius: 20px;">
                                <i class="bi bi-check-lg"></i> Xác nhận duyệt đơn thuốc
                            </button>
                        </div>
                    </form>
                </div>
            <?php endif; ?>

            <div class="d-flex justify-content-end gap-2 mt-3 <?= $status !== 'paid' ? 'pt-3 border-top' : '' ?>">
                <!-- Nút giao thuốc cho Dược sĩ / Admin khi đơn đã duyệt (approved) -->
                <?php if (($user['role'] === 'pharmacist' || $user['role'] === 'admin') && $status === 'approved'): ?>
                    <form method="POST" action="index.php?page=prescriptions&action=dispense">
                        <?= Security::csrfField(); ?>
                        <input type="hidden" name="id" value="<?= $prescription['id'] ?>">
                        <button type="submit" class="btn btn-success px-4" style="border-radius: 20px;">
                            <i class="bi bi-check-circle-fill me-2"></i>Xác nhận giao thuốc
                        </button>
                    </form>
                <?php endif; ?>

                <!-- Nút hủy đơn thuốc cho Bác sĩ / Admin khi đơn chưa giao (draft, paid hoặc approved) -->
                <?php if (($user['role'] === 'doctor' || $user['role'] === 'admin') && in_array($status, ['draft', 'paid', 'approved'])): ?>
                    <form method="POST" action="index.php?page=prescriptions&action=cancel" onsubmit="return confirm('Bạn có chắc chắn muốn hủy đơn thuốc này và giải phóng tồn kho đã đặt trước?');">
                        <?= Security::csrfField(); ?>
                        <input type="hidden" name="id" value="<?= $prescription['id'] ?>">
                        <button type="submit" class="btn btn-outline-danger px-4" style="border-radius: 20px;">
                            <i class="bi bi-x-circle me-2"></i>Hủy đơn thuốc
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</div>
