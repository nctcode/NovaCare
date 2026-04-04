<!-- Prescription List -->
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

<div class="content-card">
    <div class="card-header">
        <h5><i class="bi bi-file-earmark-medical-fill me-2"></i>Danh sách Đơn thuốc</h5>
        <?php if ($_SESSION['user']['role'] === 'doctor'): ?>
        <a href="index.php?page=prescriptions&action=create" class="btn-action btn-add">
            <i class="bi bi-plus-lg"></i> Tạo đơn thuốc
        </a>
        <?php endif; ?>
    </div>
    <div class="card-body">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Bệnh nhân</th>
                        <th>Bác sĩ</th>
                        <th>Chẩn đoán</th>
                        <th>Ngày tạo</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($prescriptions)): ?>
                        <tr><td colspan="6" class="text-center text-muted py-4">Không có đơn thuốc</td></tr>
                    <?php else: ?>
                        <?php foreach ($prescriptions as $idx => $pr): ?>
                        <tr>
                            <td><?= $idx + 1 ?></td>
                            <td><strong><?= htmlspecialchars($pr['patient_name'] ?? '') ?></strong></td>
                            <td><?= htmlspecialchars($pr['doctor_name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($pr['diagnosis'] ?? '') ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($pr['created_at'])) ?></td>
                            <td>
                                <a href="index.php?page=prescriptions&action=view&id=<?= $pr['id'] ?>" 
                                   class="btn-action btn-view">
                                    <i class="bi bi-eye"></i> Xem
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
