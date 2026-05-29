<!-- Danh sách Hồ sơ bệnh án EMR -->
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

<!-- Title and Quick Actions -->
<div class="d-flex flex-column flex-sm-row justify-content-between align-items-sm-center gap-3 mb-4" data-aos="fade-up">
    <div>
        <h5 class="m-0 fw-bold text-dark"><i class="fa-solid fa-file-waveform me-2 text-primary"></i>Hồ sơ Bệnh án Lâm sàng</h5>
        <p class="text-muted mb-0" style="font-size:13px; margin-top: 4px;">Danh sách lịch sử thăm khám, điều trị ngoại trú EMR</p>
    </div>
    
    <div class="d-flex align-items-center gap-2">
        <!-- Live Search Bar -->
        <div class="table-search-input">
            <i class="fa-solid fa-magnifying-glass" style="position: absolute; left: 12px; top: 12px; color: #94a3b8; font-size: 13px;"></i>
            <input type="text" id="recordSearch" class="form-control ps-5" placeholder="Tìm nhanh mã, tên bệnh nhân, bác sĩ..." style="border-radius: 10px; font-size: 13px; width: 250px; height: 38px; border: 1.5px solid #e2e8f0; background: #f8fafc;" onkeyup="filterRecords()">
        </div>
        
        <?php if ($user['role'] === 'admin' || $user['role'] === 'doctor'): ?>
        <a href="index.php?page=records&action=create" class="btn btn-primary px-3.5" style="border-radius:10px; font-weight:600; font-size:13px; height: 38px; display: inline-flex; align-items: center; background: #0284c7; border-color: #0284c7;">
            <i class="fa-solid fa-plus me-1.5"></i> Thêm Bệnh án
        </a>
        <?php endif; ?>
    </div>
</div>

<!-- Balanced Data Table View -->
<div class="row" data-aos="fade-up" data-aos-delay="100">
    <div class="col-12">
        <div class="content-card border-0 shadow-sm p-4 bg-white" style="border-radius:16px;">
            <?php if (empty($records)): ?>
                <div class="empty-state py-5 text-center text-muted">
                    <div class="empty-icon fs-1 text-secondary opacity-25 mb-2"><i class="fa-solid fa-folder-open"></i></div>
                    <h6 class="fw-bold text-secondary">Không tìm thấy hồ sơ bệnh án nào</h6>
                    <p class="small text-muted mb-0">Hệ thống chưa ghi nhận lượt điều trị ngoại trú EMR nào.</p>
                </div>
            <?php else: ?>
                <div class="table-wrapper">
                    <table class="data-table" id="recordTable" style="font-size: 13.5px;">
                        <thead>
                            <tr>
                                <th style="width: 90px;">Mã Số</th>
                                <th>Ngày Khám</th>
                                <?php if ($user['role'] !== 'patient'): ?>
                                <th>Bệnh Nhân</th>
                                <?php endif; ?>
                                <?php if ($user['role'] !== 'doctor'): ?>
                                <th>Bác Sĩ Đảm Nhiệm</th>
                                <?php endif; ?>
                                <th>Chẩn Đoán Lâm Sàng</th>
                                <th style="width: 120px;" class="text-end">Hành Thao Tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach($records as $rec): ?>
                            <tr>
                                <td>
                                    <span class="badge bg-secondary-subtle text-secondary" style="font-size: 11px; font-weight: 700; border-radius: 4px; padding: 4px 6px;">
                                        #<?= str_pad($rec['id'], 5, '0', STR_PAD_LEFT) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="text-primary fw-semibold">
                                        <i class="fa-regular fa-clock me-1"></i>
                                        <?= date('d/m/Y H:i', strtotime($rec['created_at'])) ?>
                                    </span>
                                </td>
                                <?php if ($user['role'] !== 'patient'): ?>
                                <td>
                                    <strong class="text-dark patient-name"><?= htmlspecialchars($rec['patient_name']) ?></strong>
                                </td>
                                <?php endif; ?>
                                <?php if ($user['role'] !== 'doctor'): ?>
                                <td>
                                    <span class="text-secondary doctor-name">BS. <?= htmlspecialchars(preg_replace('/^(Bác sĩ|BS\.|Bs\.|Bs|BS)\s+/iu', '', $rec['doctor_name'] ?? 'N/A')) ?></span>
                                </td>
                                <?php endif; ?>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <?php if (!empty($rec['icd10_code'])): ?>
                                        <span class="badge bg-danger-subtle text-danger border border-danger-subtle px-1.5 py-0.5 me-1.5 icd10-code" style="font-size: 10px; border-radius: 4px; background-color: rgba(239, 68, 68, 0.08) !important;">
                                            <?= htmlspecialchars($rec['icd10_code']) ?>
                                        </span>
                                        <?php endif; ?>
                                        <div class="text-truncate diagnosis-text" style="max-width: 200px;" title="<?= htmlspecialchars($rec['diagnosis']) ?>">
                                            <?= htmlspecialchars($rec['diagnosis']) ?>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-end">
                                    <a href="index.php?page=records&action=view&id=<?= $rec['id'] ?>" class="btn btn-sm btn-outline-primary px-3" style="border-radius: 8px; font-weight: 600; font-size: 12px; transition: all 0.2s;">
                                        Xem chi tiết
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Interactive Client-side Live Search Script -->
<script>
function filterRecords() {
    const input = document.getElementById("recordSearch");
    const filter = input.value.toLowerCase();
    const table = document.getElementById("recordTable");
    if (!table) return;
    
    const trs = table.getElementsByTagName("tr");
    
    for (let i = 1; i < trs.length; i++) {
        const tr = trs[i];
        let found = false;
        
        // Search columns: Record ID, Patient Name, Doctor Name, Diagnosis, ICD-10
        const patientNameEl = tr.querySelector(".patient-name");
        const doctorNameEl = tr.querySelector(".doctor-name");
        const diagnosisEl = tr.querySelector(".diagnosis-text");
        const icd10El = tr.querySelector(".icd10-code");
        const recordIdEl = tr.cells[0]; // First cell is ID
        
        const patientName = patientNameEl ? patientNameEl.textContent.toLowerCase() : "";
        const doctorName = doctorNameEl ? doctorNameEl.textContent.toLowerCase() : "";
        const diagnosis = diagnosisEl ? diagnosisEl.textContent.toLowerCase() : "";
        const icd10 = icd10El ? icd10El.textContent.toLowerCase() : "";
        const recordId = recordIdEl ? recordIdEl.textContent.toLowerCase() : "";
        
        if (
            patientName.includes(filter) || 
            doctorName.includes(filter) || 
            diagnosis.includes(filter) || 
            icd10.includes(filter) || 
            recordId.includes(filter)
        ) {
            found = true;
        }
        
        if (found) {
            tr.style.display = "";
        } else {
            tr.style.display = "none";
        }
    }
}
</script>
