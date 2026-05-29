<!-- World-Class Clinical Medical Record Detail View (EMR Layout) -->
<div class="row g-4 mb-4" data-aos="fade-up">
    <!-- Main Clinical Content (Left Column - 8 cols) -->
    <div class="col-lg-8">
        <!-- Main EMR Document -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px; background: #ffffff; border: 1px solid #e2e8f0; overflow: hidden;">
            <!-- Document Header -->
            <div class="p-4 border-bottom d-flex justify-content-between align-items-center" style="background: linear-gradient(135deg, #f0f9ff 0%, #ffffff 100%);">
                <div>
                    <span class="badge bg-primary-subtle text-primary border border-primary-subtle px-2.5 py-1 mb-2" style="font-size: 11px; font-weight: 700; border-radius: 6px; background-color: rgba(14, 165, 233, 0.08) !important;">
                        <i class="fa-solid fa-file-waveform me-1"></i> TRÍCH SAO BỆNH ÁN ĐIỆN TỬ
                    </span>
                    <h4 class="fw-bold text-dark m-0" style="letter-spacing: -0.5px;">BỆNH ÁN CHI TIẾT #<?= str_pad($record['id'], 5, '0', STR_PAD_LEFT) ?></h4>
                </div>
                <button onclick="window.print()" class="btn btn-outline-primary px-3.5 py-2 fw-semibold d-inline-flex align-items-center gap-1.5" style="border-radius: 10px; font-size: 13px;">
                    <i class="fa-solid fa-print"></i> In Bản Sao EMR
                </button>
            </div>

            <div class="card-body p-4">
                <!-- Encounter Date and Status -->
                <div class="d-flex align-items-center justify-content-between p-3 rounded-4 mb-4" style="background: #f8fafc; border: 1px solid #f1f5f9;">
                    <span class="text-secondary" style="font-size: 13.5px;">
                        <i class="fa-regular fa-clock text-primary me-1.5"></i>
                        Ngày thực hiện khám: <strong><?= date('d/m/Y H:i', strtotime($record['created_at'])) ?></strong>
                    </span>
                    <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1" style="font-size: 11px; font-weight: 700; border-radius: 6px; background-color: rgba(16, 185, 129, 0.08) !important;">
                        <i class="fa-solid fa-circle-check me-1"></i> ĐÃ HOÀN TẤT ĐIỀU TRỊ
                    </span>
                </div>

                <!-- Diagnosis Section (Minimalist with Left Accent Line) -->
                <div class="mb-4">
                    <h6 class="text-dark fw-bold mb-2.5 d-flex align-items-center gap-2" style="font-size: 14.5px;">
                        <i class="fa-solid fa-stethoscope text-danger"></i> Chẩn đoán lâm sàng của Bác sĩ:
                    </h6>
                    <div class="p-3.5 border rounded-3 text-dark fw-bold" style="font-size:15px; line-height: 1.6; background: #ffffff; border-color: #e2e8f0 !important; border-left: 5px solid #ef4444 !important; color: #0f172a !important; box-shadow: 0 2px 8px rgba(0,0,0,0.01);">
                        <?= nl2br(htmlspecialchars($record['diagnosis'])) ?>
                    </div>
                </div>

                <!-- Treatment Plan / Prescription -->
                <div class="mb-4">
                    <h6 class="text-dark fw-bold mb-2.5 d-flex align-items-center gap-2" style="font-size: 14.5px;">
                        <i class="fa-solid fa-pills text-success"></i> Phương án điều trị & Kê đơn thuốc:
                    </h6>
                    <div class="p-3.5 border rounded-3 text-dark" style="font-size:14.5px; line-height:1.7; background: #ffffff; border-color: #e2e8f0 !important; border-left: 5px solid #10b981 !important; color: #334155; box-shadow: 0 2px 8px rgba(0,0,0,0.01);">
                        <?php if (empty($record['treatment'])): ?>
                            <span class="text-muted fst-italic"><i class="fa-solid fa-info-circle me-1"></i> Không có hướng điều trị đặc hiệu được chỉ định thêm.</span>
                        <?php else: ?>
                            <div style="white-space: pre-line; font-weight: 600; color: #1e293b;"><?= htmlspecialchars($record['treatment']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>

                <!-- Warnings / Lời dặn -->
                <div>
                    <h6 class="text-dark fw-bold mb-2.5 d-flex align-items-center gap-2" style="font-size: 14.5px;">
                        <i class="fa-solid fa-clipboard text-warning"></i> Dặn dò y khoa & Hướng dẫn sinh hoạt:
                    </h6>
                    <div class="p-3.5 border rounded-3 text-dark" style="font-size:13.5px; line-height: 1.6; background: #ffffff; border-color: #e2e8f0 !important; border-left: 5px solid #f59e0b !important; color: #475569; box-shadow: 0 2px 8px rgba(0,0,0,0.01);">
                        <?php if (empty($record['notes'])): ?>
                            <span class="text-muted"><i class="fa-solid fa-comment-slash me-1"></i> Không có dặn dò bổ sung từ bác sĩ phụ trách.</span>
                        <?php else: ?>
                            <div style="white-space: pre-line; font-weight: 600; color: #334155;"><?= htmlspecialchars($record['notes']) ?></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Metadata & Administrative Info (Right Column - 4 cols) -->
    <div class="col-lg-4">
        <!-- Patient Info Card -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px; background: #ffffff; border: 1px solid #e2e8f0;">
            <div class="card-body p-4">
                <h6 class="text-uppercase text-muted fw-bold mb-4" style="font-size: 11px; letter-spacing: 0.5px;"><i class="fa-solid fa-hospital-user text-primary me-1.5"></i>Đối tượng khám bệnh</h6>
                
                <div class="d-flex align-items-center mb-4">
                    <div class="text-white d-flex justify-content-center align-items-center" 
                         style="width: 52px; height: 52px; border-radius: 16px; font-size: 20px; font-weight: 800; background: linear-gradient(135deg, #0ea5e9, #2563eb); flex-shrink: 0; box-shadow: 0 4px 10px rgba(14, 165, 233, 0.15);">
                        <?= mb_substr($record['patient_name'], 0, 1, 'utf-8') ?>
                    </div>
                    <div class="ms-3">
                        <h6 class="m-0 fw-bold text-dark" style="font-size: 15px;"><?= htmlspecialchars($record['patient_name']) ?></h6>
                        <small class="text-muted d-block mt-0.5" style="font-size: 12.5px;">Mã BN: PAT-<?= str_pad($record['patient_id'], 4, '0', STR_PAD_LEFT) ?></small>
                    </div>
                </div>

                <!-- Linked Appointment details -->
                <?php if (!empty($record['appointment_id'])): ?>
                <div class="p-3 rounded-3" style="background: #f8fafc; border: 1.5px dashed #e2e8f0; font-size: 13px;">
                    <span class="text-secondary d-block mb-1"><i class="fa-solid fa-link me-1 text-primary"></i> Liên kết Lịch hẹn</span>
                    <strong class="text-dark d-block mb-1"><?= date('H:i - d/m/Y', strtotime($record['appointment_date'])) ?></strong>
                    <span class="text-muted" style="font-size: 12px; line-height: 1.4; display: block;">Lý do khám: <em><?= htmlspecialchars($record['reason']) ?></em></span>
                </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Doctor Details Card -->
        <div class="card border-0 shadow-sm mb-4" style="border-radius: 20px; background: #ffffff; border: 1px solid #e2e8f0;">
            <div class="card-body p-4">
                <h6 class="text-uppercase text-muted fw-bold mb-4" style="font-size: 11px; letter-spacing: 0.5px;"><i class="fa-solid fa-user-doctor text-success me-1.5"></i>Bác sĩ điều trị</h6>
                
                <div class="d-flex align-items-center">
                    <div class="text-white d-flex justify-content-center align-items-center" 
                         style="width: 52px; height: 52px; border-radius: 16px; font-size: 20px; font-weight: 800; background: linear-gradient(135deg, #10b981, #059669); flex-shrink: 0; box-shadow: 0 4px 10px rgba(16, 185, 129, 0.15);">
                        <i class="fa-solid fa-user-md"></i>
                    </div>
                    <div class="ms-3">
                        <h6 class="m-0 fw-bold text-success" style="font-size: 15px;">BS. <?= htmlspecialchars($record['doctor_name']) ?></h6>
                        <small class="text-muted d-block mt-0.5" style="font-size: 12.5px;">Khoa Khám Bệnh - NovaCare</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Navigation actions -->
        <div class="mt-4">
            <a href="index.php?page=records" class="btn btn-outline-secondary w-100 py-2.5 fw-semibold d-flex align-items-center justify-content-center gap-1.5" style="border-radius: 12px; font-size: 13.5px;">
                <i class="fa-solid fa-arrow-left"></i> Trở về Trang quản lý
            </a>
        </div>
    </div>
</div>

<!-- Polish Printing Mode Layout Stylesheet -->
<style>
@media print {
    body * { visibility: hidden; }
    .col-lg-8, .col-lg-8 * { visibility: visible; }
    .col-lg-8 { 
        position: absolute; 
        left: 0; 
        top: 0; 
        width: 100%; 
        box-shadow: none !important; 
        border: none !important;
    }
    .btn, aside, .top-navbar, .col-lg-4 { display: none !important; }
}
</style>
