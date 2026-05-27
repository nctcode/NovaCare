<!-- Chi tiết Chỉ định CLS -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-custom alert-dismissible fade show">
        <i class="fa-solid fa-check-circle"></i> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php $user = $_SESSION['user']; ?>

<div class="content-card" style="max-width: 800px;">
    <div class="card-header">
        <h5><i class="fa-solid fa-flask-vial me-2"></i>Chi tiết Chỉ định CLS #<?= $order['id'] ?></h5>
        <a href="index.php?page=lab-orders" class="btn-action btn-edit">
            <i class="fa-solid fa-arrow-left"></i> Quay lại
        </a>
    </div>
    <div class="card-body">
        <!-- Thông tin chỉ định -->
        <div class="row mb-4">
            <div class="col-md-6">
                <p><strong>Bệnh nhân:</strong> <?= htmlspecialchars($order['patient_name']) ?></p>
                <p><strong>SĐT:</strong> <?= htmlspecialchars($order['patient_phone'] ?? 'N/A') ?></p>
                <?php if (!empty($order['date_of_birth'])): ?>
                <p><strong>Ngày sinh:</strong> <?= date('d/m/Y', strtotime($order['date_of_birth'])) ?></p>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <p><strong>Loại:</strong> 
                    <?= $order['order_type'] === 'lab_test' 
                        ? '<span class="badge bg-info text-dark"><i class="fa-solid fa-vial me-1"></i>Xét nghiệm</span>' 
                        : '<span class="badge bg-secondary"><i class="fa-solid fa-x-ray me-1"></i>CĐHA</span>' ?>
                </p>
                <p><strong>Tên:</strong> <?= htmlspecialchars($order['test_name']) ?></p>
                <p><strong>BS chỉ định:</strong> <?= htmlspecialchars($order['doctor_name']) ?></p>
                <p><strong>Ưu tiên:</strong> 
                    <?= $order['priority'] === 'urgent' 
                        ? '<span class="badge bg-danger"><i class="fa-solid fa-bolt me-1"></i>Cấp cứu</span>' 
                        : '<span class="badge bg-light text-dark border">Bình thường</span>' ?>
                </p>
                <p><strong>Trạng thái:</strong> 
                    <?php
                    $statusMap = [
                        'pending' => '<span class="badge bg-warning text-dark"><i class="fa-solid fa-clock me-1"></i>Chờ xử lý</span>',
                        'in_progress' => '<span class="badge bg-primary"><i class="fa-solid fa-spinner me-1"></i>Đang thực hiện</span>',
                        'completed' => '<span class="badge bg-success"><i class="fa-solid fa-check-circle me-1"></i>Hoàn thành</span>',
                        'cancelled' => '<span class="badge bg-danger"><i class="fa-solid fa-times-circle me-1"></i>Đã hủy</span>',
                    ];
                    echo $statusMap[$order['status']] ?? $order['status'];
                    ?>
                </p>
                <p><strong>Ngày tạo:</strong> <?= date('d/m/Y H:i', strtotime($order['created_at'])) ?></p>
            </div>
        </div>

        <?php if (!empty($order['notes'])): ?>
        <div class="alert alert-info alert-custom mb-4">
            <i class="fa-solid fa-note-sticky"></i> <strong>Ghi chú:</strong> <?= htmlspecialchars($order['notes']) ?>
        </div>
        <?php endif; ?>

        <!-- Kết quả CLS -->
        <?php if ($result): ?>
        <div class="border-top pt-4 mt-3">
            <h6 class="mb-3"><i class="fa-solid fa-microscope me-2"></i>Kết quả Cận lâm sàng</h6>
            
            <div class="p-3 rounded mb-3" style="background: var(--bg-secondary, #f8f9fa);">
                <div class="row">
                    <?php if (!empty($result['result_value'])): ?>
                    <div class="col-md-4 mb-2">
                        <small class="text-muted d-block">Giá trị</small>
                        <strong class="fs-5"><?= htmlspecialchars($result['result_value']) ?> 
                            <?= htmlspecialchars($result['unit'] ?? '') ?></strong>
                    </div>
                    <?php endif; ?>
                    <?php if (!empty($result['normal_range'])): ?>
                    <div class="col-md-4 mb-2">
                        <small class="text-muted d-block">Khoảng bình thường</small>
                        <strong><?= htmlspecialchars($result['normal_range']) ?> <?= htmlspecialchars($result['unit'] ?? '') ?></strong>
                    </div>
                    <?php endif; ?>
                    <div class="col-md-4 mb-2">
                        <small class="text-muted d-block">Kết luận</small>
                        <?php
                        $conclusionMap = [
                            'normal' => '<span class="badge bg-success"><i class="fa-solid fa-check me-1"></i>Bình thường</span>',
                            'abnormal' => '<span class="badge bg-warning text-dark"><i class="fa-solid fa-exclamation-triangle me-1"></i>Bất thường</span>',
                            'critical' => '<span class="badge bg-danger"><i class="fa-solid fa-skull-crossbones me-1"></i>Nguy hiểm</span>',
                        ];
                        echo $conclusionMap[$result['conclusion']] ?? $result['conclusion'];
                        ?>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <strong>Chi tiết kết quả:</strong>
                <div class="p-3 border rounded mt-1"><?= nl2br(htmlspecialchars($result['result_text'])) ?></div>
            </div>

            <?php if (!empty($result['image_path'])): ?>
            <div class="mb-3">
                <strong>Tệp kết quả / Hình ảnh đính kèm:</strong>
                <div class="mt-2 p-2 border rounded text-center bg-light">
                    <?php 
                    $ext = strtolower(pathinfo($result['image_path'], PATHINFO_EXTENSION));
                    if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif'])): 
                    ?>
                        <img src="<?= htmlspecialchars($result['image_path']) ?>" alt="Kết quả cận lâm sàng" class="img-fluid rounded" style="max-height: 400px; object-fit: contain; box-shadow: 0 4px 8px rgba(0,0,0,0.1);">
                    <?php else: ?>
                        <div class="p-3">
                            <i class="fa-solid fa-file-pdf text-danger fa-2x mb-2"></i>
                            <p class="mb-0 small"><a href="<?= htmlspecialchars($result['image_path']) ?>" target="_blank" class="btn btn-outline-danger btn-sm" style="border-radius:15px;"><i class="fa-solid fa-download me-1"></i> Xem / Tải file đính kèm (<?= strtoupper($ext) ?>)</a></p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            <?php endif; ?>

            <div class="row text-muted">
                <div class="col-md-6"><small><i class="fa-solid fa-user-gear me-1"></i>KTV: <?= htmlspecialchars($result['technician_name']) ?></small></div>
                <div class="col-md-6"><small><i class="fa-solid fa-clock me-1"></i>Hoàn thành: <?= date('d/m/Y H:i', strtotime($result['completed_at'])) ?></small></div>
            </div>
        </div>
        <?php elseif ($order['status'] !== 'cancelled'): ?>
        <div class="border-top pt-4 mt-3 text-center text-muted">
            <i class="fa-solid fa-hourglass-half fa-2x mb-2"></i>
            <p>Chưa có kết quả</p>
            <?php if ($user['role'] === 'technician'): ?>
            <a href="index.php?page=lab-orders&action=result&id=<?= $order['id'] ?>" class="btn btn-primary px-4" style="border-radius:20px">
                <i class="fa-solid fa-pen-to-square me-2"></i>Nhập kết quả
            </a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <!-- Nút hành động -->
        <?php if ($user['role'] === 'technician' && in_array($order['status'], ['pending', 'in_progress']) && !$result): ?>
        <div class="d-flex justify-content-end mt-4 pt-3 border-top">
            <a href="index.php?page=lab-orders&action=result&id=<?= $order['id'] ?>" class="btn btn-primary px-4" style="border-radius:20px">
                <i class="fa-solid fa-pen-to-square me-2"></i>Nhập kết quả
            </a>
        </div>
        <?php endif; ?>
    </div>
</div>
