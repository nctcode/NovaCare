<!-- Chi tiết Đơn thuốc -->
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
            </div>
        </div>

        <?php if (!empty($prescription['notes'])): ?>
        <div class="alert alert-info alert-custom mb-4">
            <i class="bi bi-info-circle"></i> <strong>Ghi chú:</strong> <?= htmlspecialchars($prescription['notes']) ?>
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
    </div>
</div>
