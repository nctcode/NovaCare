<!-- Danh sách Hồ sơ bệnh án -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-custom alert-dismissible fade show">
        <i class="fa-solid fa-check-circle"></i> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-custom alert-dismissible fade show">
        <i class="fa-solid fa-triangle-exclamation"></i> <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="d-flex justify-content-between align-items-center mb-4" data-aos="fade-up">
    <div>
        <h5 class="m-0"><i class="fa-solid fa-file-medical me-2 text-warning"></i>Hồ sơ Bệnh án (<?= count($records) ?>)</h5>
        <p class="text-muted" style="font-size:13px; margin:5px 0 0 0;">Lịch sử khám bệnh và điều trị</p>
    </div>
    <?php if ($user['role'] === 'admin' || $user['role'] === 'doctor'): ?>
    <a href="index.php?page=records&action=create" class="btn btn-warning text-dark" style="border-radius:20px; font-weight:500;">
        <i class="fa-solid fa-plus me-2"></i>Thêm Bệnh án
    </a>
    <?php endif; ?>
</div>

<div class="row" data-aos="fade-up" data-aos-delay="100">
    <div class="col-12">
        <div class="content-card" style="border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.03);">
            <div class="card-body p-4">
                <?php if (empty($records)): ?>
                    <div class="text-center py-5">
                        <div style="font-size:60px; color:var(--gray-200); margin-bottom:15px;"><i class="fa-solid fa-folder-open"></i></div>
                        <h5 class="text-muted">Chưa có hồ sơ bệnh án nào.</h5>
                    </div>
                <?php else: ?>
                    <div class="timeline medical-timeline">
                        <?php foreach($records as $rec): ?>
                        <div class="timeline-item position-relative mb-5" style="border-left:3px solid var(--primary-light); padding-left:25px; margin-left:15px;">
                            <div class="timeline-icon position-absolute" style="left:-34px; top:0; width:25px; height:25px; background:var(--primary); color:white; border-radius:50%; display:flex; align-items:center; justify-content:center; border:4px solid white; font-size:10px;"><i class="fa-solid fa-stethoscope"></i></div>
                            
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <span class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-25" style="font-size:13px; font-weight:600;"><i class="fa-regular fa-clock me-1"></i><?= date('d/m/Y - H:i', strtotime($rec['created_at'])) ?></span>
                                <a href="index.php?page=records&action=view&id=<?= $rec['id'] ?>" class="btn btn-sm btn-outline-secondary" style="border-radius:20px; font-size:12px;">Xem chi tiết</a>
                            </div>
                            
                            <div class="card border-0 shadow-sm mt-3" style="border-radius:12px; background:#f8fafc;">
                                <div class="card-body">
                                    <h6 style="color:var(--dark); font-weight:700; margin-bottom:10px;"><i class="fa-solid fa-disease me-2 text-danger"></i>Chẩn đoán: <?= htmlspecialchars($rec['diagnosis']) ?></h6>
                                    
                                    <div class="row mt-3 text-muted" style="font-size:14px;">
                                        <?php if ($user['role'] !== 'patient'): ?>
                                        <div class="col-md-6 mb-2">
                                            <i class="fa-solid fa-user text-primary me-2"></i>Bệnh nhân: <strong class="text-dark"><?= htmlspecialchars($rec['patient_name']) ?></strong>
                                        </div>
                                        <?php endif; ?>
                                        <?php if ($user['role'] !== 'doctor'): ?>
                                        <div class="col-md-6 mb-2">
                                            <i class="fa-solid fa-user-doctor text-success me-2"></i>Bác sĩ khám: <strong class="text-dark">BS. <?= htmlspecialchars($rec['doctor_name']) ?></strong>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <?php if (!empty($rec['treatment'])): ?>
                                    <div class="mt-3 p-3 bg-white" style="border-radius:8px; border-left:4px solid var(--success);">
                                        <strong style="font-size:13px; color:var(--success); d-block mb-1">Hướng điều trị / Đơn thuốc:</strong>
                                        <div style="font-size:14px;"><?= nl2br(htmlspecialchars($rec['treatment'])) ?></div>
                                    </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
