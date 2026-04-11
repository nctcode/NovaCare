<!-- Chi tiết Bệnh án -->
<div class="content-card mb-4" data-aos="fade-up" style="border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.03);">
    <div class="card-header bg-primary bg-opacity-10 border-0 pt-4 pb-3" style="border-radius:16px 16px 0 0;">
        <div class="d-flex justify-content-between align-items-center">
            <h5 class="m-0 text-primary" style="font-weight:700;"><i class="fa-solid fa-file-waveform me-2"></i>HỒ SƠ BỆNH ÁN #<?= str_pad($record['id'], 5, '0', STR_PAD_LEFT) ?></h5>
            <span class="badge bg-primary text-white" style="font-size:13px; padding:6px 15px;"><i class="fa-regular fa-clock me-1"></i><?= date('d/m/Y H:i', strtotime($record['created_at'])) ?></span>
        </div>
    </div>
    <div class="card-body p-4">
        
        <div class="row mb-4 border-bottom pb-4">
            <div class="col-md-6 mb-3 mb-md-0 border-end">
                <h6 class="text-muted text-uppercase" style="font-size:12px; letter-spacing:1px; font-weight:700;">Thông tin Bệnh nhân</h6>
                <div class="d-flex align-items-center mt-3">
                    <div class="avatar bg-primary text-white d-flex justify-content-center align-items-center" style="width:50px; height:50px; border-radius:50%; font-size:20px; font-weight:700;">
                        <?= strtoupper(substr($record['patient_name'], 0, 1)) ?>
                    </div>
                    <div class="ms-3">
                        <h5 class="m-0" style="font-weight:700; color:var(--dark);"><?= htmlspecialchars($record['patient_name']) ?></h5>
                        <div class="text-muted mt-1" style="font-size:14px;"><i class="fa-solid fa-id-card me-1"></i>ID: PAT-<?= str_pad($record['patient_id'], 4, '0', STR_PAD_LEFT) ?></div>
                    </div>
                </div>
            </div>
            <div class="col-md-6 ps-md-4">
                <h6 class="text-muted text-uppercase" style="font-size:12px; letter-spacing:1px; font-weight:700;">Thông tin Bác sĩ phụ trách</h6>
                <div class="d-flex align-items-center mt-3">
                    <div class="avatar bg-success text-white d-flex justify-content-center align-items-center" style="width:50px; height:50px; border-radius:50%; font-size:20px; font-weight:700;">
                        <i class="fa-solid fa-user-doctor"></i>
                    </div>
                    <div class="ms-3">
                        <h5 class="m-0 text-success" style="font-weight:700;">BS. <?= htmlspecialchars($record['doctor_name']) ?></h5>
                        <div class="text-muted mt-1" style="font-size:14px;">Khoa Khám Bệnh - NovaCare</div>
                    </div>
                </div>
            </div>
        </div>

        <?php if (!empty($record['appointment_id'])): ?>
        <div class="alert alert-info bg-info bg-opacity-10 border-info border-opacity-25 d-flex align-items-center mb-4">
            <i class="fa-solid fa-link me-3 text-info" style="font-size:20px;"></i>
            <div>
                <strong class="text-info d-block">Liên kết Lịch hẹn lúc <?= date('H:i - d/m/Y', strtotime($record['appointment_date'])) ?></strong>
                <span class="text-muted" style="font-size:13px;">Lý do khám ban đầu: <?= htmlspecialchars($record['reason']) ?></span>
            </div>
        </div>
        <?php endif; ?>

        <div class="mb-4">
            <h6 class="text-danger mb-3 pb-2 border-bottom" style="font-weight:700;"><i class="fa-solid fa-virus me-2"></i>KẾT QUẢ CHẨN ĐOÁN</h6>
            <div class="p-3 bg-danger bg-opacity-10 text-dark" style="border-radius:10px; font-size:16px; font-weight:500;">
                <?= nl2br(htmlspecialchars($record['diagnosis'])) ?>
            </div>
        </div>

        <hr class="my-4" style="border-color:var(--gray-200);">

        <div class="mb-4">
            <h6 class="text-success mb-3 pb-2 border-bottom" style="font-weight:700;"><i class="fa-solid fa-pills me-2"></i>HƯỚNG ĐIỀU TRỊ / ĐƠN THUỐC</h6>
            <div class="p-3 bg-success bg-opacity-10 text-dark" style="border-radius:10px; font-size:15px; line-height:1.6;">
                <?php if (empty($record['treatment'])): ?>
                    <span class="text-muted fst-italic">Không có dữ liệu điều trị đặc biệt.</span>
                <?php else: ?>
                    <?= nl2br(htmlspecialchars($record['treatment'])) ?>
                <?php endif; ?>
            </div>
        </div>

        <hr class="my-4" style="border-color:var(--gray-200);">

        <div class="mb-2 border p-3 rounded bg-light">
            <h6 class="text-dark mb-2" style="font-weight:700;"><i class="fa-solid fa-pencil me-2 text-warning"></i>GHI CHÚ THÊM DẶN DÒ</h6>
            <div class="text-dark" style="font-size:14px;">
                <?php if (empty($record['notes'])): ?>
                    <span class="text-muted">Không có lời dặn.</span>
                <?php else: ?>
                    <?= nl2br(htmlspecialchars($record['notes'])) ?>
                <?php endif; ?>
            </div>
        </div>

    </div>
    <div class="card-footer bg-white border-top p-4 text-end" style="border-radius:0 0 16px 16px;">
        <button onclick="window.print()" class="btn btn-secondary me-2" style="border-radius:20px;"><i class="fa-solid fa-print me-2"></i>In Bệnh án</button>
        <a href="index.php?page=records" class="btn btn-primary" style="border-radius:20px;"><i class="fa-solid fa-arrow-left me-2"></i>Về Trang quản lý</a>
    </div>
</div>

<style>
/* CSS cho in ấn (ẩn header sidebar đi) */
@media print {
    body * { visibility: hidden; }
    .content-card, .content-card * { visibility: visible; }
    .content-card { position: absolute; left: 0; top: 0; width: 100%; box-shadow: none !important; }
    .btn, aside, .top-navbar { display: none !important; }
}
</style>
