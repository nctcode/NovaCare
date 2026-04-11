<!-- Rooms Overview -->
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h5 class="m-0" style="font-weight:700;"><i class="fa-solid fa-map me-2 text-primary"></i>Sơ đồ Phòng bệnh</h5>
        <p class="text-muted" style="font-size:13px; margin:5px 0 0 0;">Tổng quan phòng và trạng thái giường</p>
    </div>
    <a href="index.php?page=inpatient" class="btn btn-outline-secondary" style="border-radius:20px; font-weight:500;">
        <i class="fa-solid fa-arrow-left me-1"></i>Quay lại
    </a>
</div>

<div class="row g-4">
    <?php foreach ($rooms as $room): ?>
    <div class="col-lg-4 col-md-6" data-aos="fade-up">
        <div class="content-card p-4" style="border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.03); height:100%;">
            <!-- Room Header -->
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h5 class="m-0 fw-bold">
                        <?php
                        $roomIcons = ['standard'=>'🚪','vip'=>'⭐','icu'=>'🏥'];
                        echo ($roomIcons[$room['room_type']] ?? '🚪') . ' Phòng ' . $room['room_number'];
                        ?>
                    </h5>
                    <small class="text-muted"><?= htmlspecialchars($room['department_name'] ?? '') ?></small>
                </div>
                <?php
                $typeBadges = ['standard'=>'bg-secondary','vip'=>'bg-warning text-dark','icu'=>'bg-danger'];
                $typeLabels = ['standard'=>'Thường','vip'=>'VIP','icu'=>'ICU'];
                ?>
                <span class="badge <?= $typeBadges[$room['room_type']] ?? 'bg-secondary' ?>" style="border-radius:20px;">
                    <?= $typeLabels[$room['room_type']] ?? $room['room_type'] ?>
                </span>
            </div>

            <!-- Price -->
            <div class="mb-3 p-2 text-center" style="background:var(--primary-light); border-radius:10px;">
                <strong class="text-primary"><?= number_format($room['price_per_day'], 0, ',', '.') ?>đ</strong>
                <small class="text-muted">/ ngày</small>
            </div>

            <!-- Bed Grid -->
            <div class="d-flex flex-wrap gap-2 mb-3">
                <?php foreach ($room['beds'] as $bed): ?>
                <div class="p-2 text-center" style="border-radius:10px; min-width:80px; flex:1;
                    background:<?= $bed['status'] === 'available' ? '#dcfce7' : ($bed['status'] === 'occupied' ? '#fef3c7' : '#fee2e2') ?>;
                    border:1px solid <?= $bed['status'] === 'available' ? '#86efac' : ($bed['status'] === 'occupied' ? '#fcd34d' : '#fca5a5') ?>;">
                    <div style="font-size:20px;"><?= $bed['status'] === 'available' ? '🛏️' : ($bed['status'] === 'occupied' ? '🧑‍⚕️' : '🔧') ?></div>
                    <div style="font-size:12px; font-weight:600;"><?= $bed['bed_number'] ?></div>
                    <?php if ($bed['patient_name']): ?>
                    <div style="font-size:11px; color:var(--gray-500);"><?= htmlspecialchars($bed['patient_name']) ?></div>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Summary -->
            <div class="d-flex justify-content-between" style="font-size:13px;">
                <span class="text-muted">Tổng giường: <strong><?= $room['total_beds'] ?></strong></span>
                <span style="color:var(--success);">Trống: <strong><?= $room['available_beds'] ?></strong></span>
            </div>
        </div>
    </div>
    <?php endforeach; ?>
</div>
