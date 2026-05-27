<!-- Admission Detail -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-custom alert-dismissible fade show">
        <i class="fa-solid fa-check-circle"></i> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php
$sClasses = ['active'=>'badge-confirmed','discharged'=>'badge-completed','transferred'=>'badge-pending'];
$sLabels = ['active'=>'Đang nằm viện','discharged'=>'Đã xuất viện','transferred'=>'Chuyển khoa'];
$typeLabels = ['standard'=>'Phòng thường','vip'=>'Phòng VIP','icu'=>'Phòng ICU'];
$genderLabels = ['male'=>'Nam','female'=>'Nữ','other'=>'Khác'];
?>

<div class="content-card" data-aos="fade-up" style="border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.03);">
    <div class="card-header bg-white border-bottom pt-4 pb-3">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h4 class="m-0" style="font-weight:700;"><i class="fa-solid fa-hospital-user me-2 text-primary"></i>Hồ sơ Nhập viện #<?= $admission['id'] ?></h4>
                <p class="text-muted mb-0" style="font-size:13px;">Nhập viện: <?= date('d/m/Y H:i', strtotime($admission['admission_date'])) ?></p>
            </div>
            <span class="badge-status <?= $sClasses[$admission['status']] ?? '' ?>" style="font-size:14px; padding:8px 20px;">
                <?= $sLabels[$admission['status']] ?? $admission['status'] ?>
            </span>
        </div>
    </div>

    <div class="card-body">
        <div class="row g-4 mb-4">
            <!-- Patient Info -->
            <div class="col-md-6">
                <div class="p-3" style="background:var(--gray-50); border-radius:12px;">
                    <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-user me-1"></i> Thông tin bệnh nhân</h6>
                    <table class="table table-borderless mb-0" style="font-size:14px;">
                        <tr><td class="text-muted" style="width:120px;">Họ tên:</td><td class="fw-bold"><?= htmlspecialchars($admission['patient_name']) ?></td></tr>
                        <tr><td class="text-muted">Giới tính:</td><td><?= $genderLabels[$admission['gender'] ?? ''] ?? '-' ?></td></tr>
                        <tr><td class="text-muted">Ngày sinh:</td><td><?= $admission['date_of_birth'] ? date('d/m/Y', strtotime($admission['date_of_birth'])) : '-' ?></td></tr>
                        <tr><td class="text-muted">Nhóm máu:</td><td class="fw-bold text-danger"><?= $admission['blood_type'] ?? '-' ?></td></tr>
                        <tr><td class="text-muted">SĐT:</td><td><?= htmlspecialchars($admission['patient_phone'] ?? '-') ?></td></tr>
                        <tr><td class="text-muted">Địa chỉ:</td><td><?= htmlspecialchars($admission['patient_address'] ?? '-') ?></td></tr>
                    </table>
                </div>
            </div>

            <!-- Room & Doctor Info -->
            <div class="col-md-6">
                <div class="p-3 mb-3" style="background:var(--gray-50); border-radius:12px;">
                    <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-bed me-1"></i> Phòng & Giường</h6>
                    <table class="table table-borderless mb-0" style="font-size:14px;">
                        <tr><td class="text-muted" style="width:120px;">Phòng:</td><td class="fw-bold"><?= $admission['room_number'] ?> (<?= $typeLabels[$admission['room_type']] ?? '' ?>)</td></tr>
                        <tr><td class="text-muted">Giường:</td><td><?= $admission['bed_number'] ?></td></tr>
                        <tr><td class="text-muted">Khoa:</td><td><?= htmlspecialchars($admission['department_name'] ?? '-') ?></td></tr>
                        <tr><td class="text-muted">Giá phòng:</td><td class="fw-bold"><?= number_format($admission['price_per_day'], 0, ',', '.') ?>đ/ngày</td></tr>
                    </table>
                </div>
                <div class="p-3" style="background:var(--gray-50); border-radius:12px;">
                    <h6 class="fw-bold text-primary mb-3"><i class="fa-solid fa-user-doctor me-1"></i> Bác sĩ phụ trách</h6>
                    <table class="table table-borderless mb-0" style="font-size:14px;">
                        <tr><td class="text-muted" style="width:120px;">Bác sĩ:</td><td class="fw-bold">BS. <?= htmlspecialchars($admission['doctor_name']) ?></td></tr>
                        <tr><td class="text-muted">Chuyên khoa:</td><td><?= htmlspecialchars($admission['specialty'] ?? '-') ?></td></tr>
                    </table>
                </div>
            </div>
        </div>

        <!-- Diagnosis & Notes -->
        <div class="row g-4 mb-4">
            <div class="col-md-6">
                <div class="p-3" style="background:#fef3c7; border-radius:12px; border:1px solid #fcd34d;">
                    <h6 class="fw-bold mb-2"><i class="fa-solid fa-stethoscope me-1"></i> Chẩn đoán</h6>
                    <p class="mb-0"><?= nl2br(htmlspecialchars($admission['diagnosis'] ?? '-')) ?></p>
                </div>
            </div>
            <div class="col-md-6">
                <div class="p-3 h-100" style="background:#e0f2fe; border-radius:12px; border:1px solid #7dd3fc;">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <h6 class="fw-bold mb-0"><i class="fa-solid fa-note-sticky me-1"></i> Ghi chú chăm sóc</h6>
                        <?php if ($admission['status'] === 'active' && in_array($_SESSION['user']['role'], ['admin', 'doctor', 'nurse'])): ?>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addNoteModal" style="border-radius:12px;">
                            <i class="fa-solid fa-plus me-1"></i>Thêm ghi chú
                        </button>
                        <?php endif; ?>
                    </div>
                    <div class="notes-content p-2 bg-white rounded" style="max-height: 200px; overflow-y: auto; font-size: 14px;">
                        <?= nl2br(htmlspecialchars($admission['notes'] ?? 'Không có ghi chú')) ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Cost Summary -->
        <div class="p-3 mb-4" style="background:linear-gradient(135deg, var(--primary-light), #e0f2fe); border-radius:12px;">
            <div class="row align-items-center">
                <div class="col-md-4 text-center">
                    <small class="text-muted d-block">Số ngày nằm viện</small>
                    <h3 class="m-0 fw-bold text-primary"><?= $days ?> ngày</h3>
                </div>
                <div class="col-md-4 text-center">
                    <small class="text-muted d-block">Giá phòng / ngày</small>
                    <h4 class="m-0 fw-bold"><?= number_format($admission['price_per_day'], 0, ',', '.') ?>đ</h4>
                </div>
                <div class="col-md-4 text-center">
                    <small class="text-muted d-block">Chi phí phòng (ước tính)</small>
                    <h3 class="m-0 fw-bold text-danger"><?= number_format($roomCost, 0, ',', '.') ?>đ</h3>
                </div>
            </div>
        </div>

        <?php if ($admission['discharge_date']): ?>
        <div class="alert alert-success">
            <i class="fa-solid fa-check-circle me-2"></i>
            <strong>Đã xuất viện:</strong> <?= date('d/m/Y H:i', strtotime($admission['discharge_date'])) ?>
        </div>
        <?php endif; ?>

        <!-- Hồ sơ chăm sóc Điều dưỡng -->
        <?php
        require_once __DIR__ . '/../../models/NursingRecord.php';
        $nrModel = new NursingRecord();
        $nursingRecords = $nrModel->getByAdmissionId($admission['id']);
        $vitalData = $nrModel->getVitalChartData($admission['id'], 10);
        ?>
        <div class="mb-4">
            <div class="d-flex justify-content-between align-items-center mb-3">
                <h6 class="fw-bold mb-0"><i class="fa-solid fa-heart-pulse me-2 text-danger"></i>Hồ sơ Chăm sóc Điều dưỡng</h6>
                <?php if ($admission['status'] === 'active' && in_array($_SESSION['user']['role'], ['admin', 'nurse'])): ?>
                <a href="index.php?page=inpatient&action=addNursingRecord&id=<?= $admission['id'] ?>" class="btn btn-sm btn-primary" style="border-radius:12px;">
                    <i class="fa-solid fa-plus me-1"></i>Ghi nhận mới
                </a>
                <?php endif; ?>
            </div>

            <?php if (!empty($vitalData)): ?>
            <!-- Biểu đồ sinh hiệu -->
            <div class="p-3 mb-3 rounded" style="background:var(--bg-secondary, #f8f9fa); border-radius:12px;">
                <canvas id="vitalSignsChart" height="180"></canvas>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const vd = <?= json_encode($vitalData) ?>;
                const labels = vd.map(r => {
                    const d = new Date(r.record_time);
                    return d.getDate()+'/'+d.getMonth() + ' ' + d.getHours()+':'+String(d.getMinutes()).padStart(2,'0');
                });
                new Chart(document.getElementById('vitalSignsChart'), {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: [
                            { label: 'Nhiệt độ (°C)', data: vd.map(r => r.temperature), borderColor: '#ef4444', borderWidth: 2, tension: 0.3, fill: false, pointRadius: 3 },
                            { label: 'Nhịp tim (bpm)', data: vd.map(r => r.heart_rate), borderColor: '#3b82f6', borderWidth: 2, tension: 0.3, fill: false, pointRadius: 3 },
                            { label: 'SpO2 (%)', data: vd.map(r => r.spo2), borderColor: '#22c55e', borderWidth: 2, tension: 0.3, fill: false, pointRadius: 3 }
                        ]
                    },
                    options: {
                        responsive: true,
                        plugins: { legend: { position: 'bottom', labels: { usePointStyle: true } } },
                        scales: { y: { beginAtZero: false }, x: { grid: { display: false } } }
                    }
                });
            });
            </script>
            <?php endif; ?>

            <?php if (!empty($nursingRecords)): ?>
            <div class="table-wrapper">
                <table class="data-table" style="font-size:13px;">
                    <thead>
                        <tr>
                            <th>Thời gian</th>
                            <th>Điều dưỡng</th>
                            <th>Nhiệt độ</th>
                            <th>HA</th>
                            <th>Nhịp tim</th>
                            <th>SpO2</th>
                            <th>Ghi chú</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($nursingRecords as $nr): ?>
                        <tr>
                            <td><small><?= date('d/m H:i', strtotime($nr['record_time'])) ?></small></td>
                            <td><small><?= htmlspecialchars($nr['nurse_name']) ?></small></td>
                            <td><?= $nr['temperature'] ? $nr['temperature'].'°C' : '-' ?></td>
                            <td><?= ($nr['blood_pressure_sys'] && $nr['blood_pressure_dia']) ? $nr['blood_pressure_sys'].'/'.$nr['blood_pressure_dia'] : '-' ?></td>
                            <td><?= $nr['heart_rate'] ?? '-' ?></td>
                            <td><?= $nr['spo2'] ? $nr['spo2'].'%' : '-' ?></td>
                            <td><small><?= htmlspecialchars(mb_strimwidth($nr['care_notes'] ?? '', 0, 50, '...')) ?></small></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php else: ?>
            <div class="text-center text-muted py-3">
                <i class="fa-solid fa-clipboard-list fa-2x mb-2" style="opacity:.3"></i>
                <p>Chưa có hồ sơ chăm sóc nào</p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Actions -->
        <div class="d-flex gap-2 flex-wrap">
            <a href="index.php?page=inpatient" class="btn btn-outline-secondary" style="border-radius:20px; font-weight:500;">
                <i class="fa-solid fa-arrow-left me-2"></i>Quay lại
            </a>
            <?php if ($admission['status'] === 'active' && in_array($_SESSION['user']['role'], ['admin', 'nurse', 'receptionist'])): ?>
            <a href="javascript:void(0)" class="btn btn-success" style="border-radius:20px; font-weight:500;" onclick="if(confirm('Xác nhận xuất viện bệnh nhân?')) postAction('index.php?page=inpatient&action=discharge&id=<?= $admission['id'] ?>')">
                <i class="fa-solid fa-right-from-bracket me-2"></i>Xuất viện
            </a>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Modal Thêm ghi chú -->
<div class="modal fade" id="addNoteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 16px;">
            <div class="modal-header border-0 pb-0">
                <h5 class="modal-title fw-bold"><i class="fa-solid fa-plus-circle text-primary me-2"></i>Thêm ghi chú chăm sóc</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form action="index.php?page=inpatient&action=updateNotes" method="POST">
                <div class="modal-body">
                    <?php echo Security::csrfField(); ?>
                    <input type="hidden" name="id" value="<?= $admission['id'] ?>">
                    <div class="mb-3">
                        <label class="form-label text-muted">Nội dung ghi chú (Sinh hiệu, thuốc, diễn biến...)</label>
                        <textarea name="notes" class="form-control" rows="5" required placeholder="Nhập ghi chú chăm sóc hàng ngày..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0 pt-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal" style="border-radius: 12px;">Hủy</button>
                    <button type="submit" class="btn btn-primary" style="border-radius: 12px;">Lưu ghi chú</button>
                </div>
            </form>
        </div>
    </div>
</div>
