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
        <h5><i class="fa-solid fa-file-prescription me-2"></i>Danh sách Đơn thuốc</h5>
        <div class="table-search-bar">
            <div class="table-search-input">
                <i class="fa-solid fa-magnifying-glass"></i>
                <input type="text" class="table-search-js" data-table="prescriptionTable" placeholder="Tìm kiếm...">
            </div>
            <?php if ($_SESSION['user']['role'] === 'doctor'): ?>
            <a href="index.php?page=prescriptions&action=create" class="btn-action btn-add">
                <i class="fa-solid fa-plus"></i> Tạo đơn thuốc
            </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body">
        <div class="table-wrapper">
            <table class="data-table" id="prescriptionTable">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Bệnh nhân</th>
                        <th>Bác sĩ</th>
                        <th>Chẩn đoán</th>
                        <th>Ngày tạo</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($prescriptions)): ?>
                        <tr><td colspan="7"><div class="empty-state"><div class="empty-icon"><i class="fa-solid fa-file-prescription"></i></div><h6>Chưa có đơn thuốc</h6><p>Chưa có đơn thuốc nào được tạo.</p></div></td></tr>
                    <?php else: ?>
                        <?php foreach ($prescriptions as $idx => $pr): ?>
                        <tr>
                            <td><?= $idx + 1 ?></td>
                            <td><strong><?= htmlspecialchars($pr['patient_name'] ?? '') ?></strong></td>
                            <td><?= htmlspecialchars($pr['doctor_name'] ?? '') ?></td>
                            <td><?= htmlspecialchars($pr['diagnosis'] ?? '') ?></td>
                            <td><?= date('d/m/Y H:i', strtotime($pr['created_at'])) ?></td>
                            <td>
                                <?php
                                $status = $pr['status'] ?? 'draft';
                                if ($status === 'draft') {
                                    echo '<span class="badge bg-warning text-dark"><i class="bi bi-clock-history me-1"></i>Chưa thanh toán</span>';
                                } elseif ($status === 'paid') {
                                    echo '<span class="badge bg-primary"><i class="bi bi-currency-dollar me-1"></i>Chờ giao thuốc</span>';
                                } elseif ($status === 'dispensed') {
                                    echo '<span class="badge bg-success"><i class="bi bi-check-circle me-1"></i>Đã giao thuốc</span>';
                                } elseif ($status === 'cancelled') {
                                    echo '<span class="badge bg-danger"><i class="bi bi-x-circle me-1"></i>Đã hủy</span>';
                                }
                                ?>
                            </td>
                            <td>
                                <a href="index.php?page=prescriptions&action=view&id=<?= $pr['id'] ?>" 
                                   class="btn-action btn-view">
                                    <i class="bi bi-eye">Xem</i>
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
