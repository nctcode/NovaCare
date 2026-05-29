<!-- Form Tạo Hồ sơ bệnh án + AI Gợi ý Chẩn đoán + ICD-10 Autocomplete 4.0 -->
<div class="form-section form-card" data-aos="fade-up" style="max-width:900px; margin:0 auto; padding:30px; border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.03);">
    <h5 class="mb-4" style="color:var(--primary); font-weight:700;"><i class="fa-solid fa-file-medical me-2"></i>Tạo Hồ sơ Cấp Bệnh án</h5>
    
    <form method="POST" action="index.php?page=records&action=store" id="formMedicalRecord">
                            <?php echo Security::csrfField(); ?>
        <div class="row">
            <?php if ($user['role'] === 'admin'): ?>
            <div class="col-md-12 mb-3">
                <label for="doctor_id" class="form-label fw-bold">Bác sĩ khám <span class="text-danger">*</span></label>
                <select class="form-select border-2" id="doctor_id" name="doctor_id" required>
                    <option value="">-- Chọn bác sĩ --</option>
                    <?php 
                    require_once __DIR__ . '/../../models/Doctor.php';
                    $doctorList = (new Doctor())->getAll();
                    foreach ($doctorList as $doc): ?>
                        <option value="<?= $doc['id'] ?>">BS. <?= htmlspecialchars($doc['name']) ?> (<?= htmlspecialchars($doc['specialty']) ?>)</option>
                    <?php endforeach; ?>
                </select>
            </div>
            <?php endif; ?>

            <div class="col-md-6 mb-3">
                <label class="form-label fw-bold">Bệnh nhân <span class="text-danger">*</span></label>
                <div class="dropdown" id="patientDropdown">
                    <button class="form-select text-start d-flex justify-content-between align-items-center border-2" type="button" id="patientDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false" style="background-image: none; height: 38px;">
                        <span id="selected_patient_label" class="text-muted">-- Chọn bệnh nhân từ danh sách --</span>
                        <i class="fa-solid fa-chevron-down text-muted fs-7"></i>
                    </button>
                    <div class="dropdown-menu w-100 p-2 shadow-sm border-light-subtle" aria-labelledby="patientDropdownBtn" style="min-width: 100%;">
                        <!-- Search input at the top of list -->
                        <div class="input-group mb-2">
                            <span class="input-group-text bg-light border-opacity-50 py-1 px-2"><i class="fa-solid fa-magnifying-glass fs-7"></i></span>
                            <input type="text" id="patient_search_input" class="form-control form-control-sm" placeholder="Tìm tên hoặc số điện thoại..." autocomplete="off">
                        </div>
                        
                        <!-- Scrollable list of patients -->
                        <div id="patient_list_container" style="max-height: 200px; overflow-y: auto;">
                            <!-- Patients will be rendered here -->
                        </div>
                    </div>
                </div>
                <input type="hidden" name="patient_id" id="patient_id" required>
            </div>
            
            <div class="col-md-6 mb-3">
                <label for="appointment_id" class="form-label fw-bold">Lịch hẹn liên kết (Tùy chọn)</label>
                <select class="form-select border-2" id="appointment_id" name="appointment_id">
                    <option value="">-- Không liên kết --</option>
                    <?php foreach ($appointments as $apt): 
                        if ($apt['status'] === 'pending' || $apt['status'] === 'confirmed'):
                    ?>
                        <option value="<?= $apt['id'] ?>"><?= date('d/m/Y H:i', strtotime($apt['appointment_date'])) ?> - <?= htmlspecialchars($apt['patient_name']) ?></option>
                    <?php endif; endforeach; ?>
                </select>
                <small class="text-muted"><i class="fa-solid fa-circle-info mt-1"></i> Sẽ tự động đánh dấu "Hoàn thành" lịch hẹn</small>
            </div>
        </div>

        <!-- ====== AI GỢI Ý CHẨN ĐOÁN 4.0 ====== -->
        <div class="mb-4 rounded-3 border" style="background: linear-gradient(135deg, #f0f4ff 0%, #fdf2f8 100%); overflow:hidden;">
            <button type="button" class="btn w-100 text-start p-3 d-flex align-items-center gap-2" id="btnToggleAI" style="background:none; border:none; font-weight:700; color:#6366f1;">
                <i class="fa-solid fa-wand-magic-sparkles"></i>
                <span>🤖 Trợ lý AI Chẩn đoán — Smart Hospital 4.0</span>
                <i class="fa-solid fa-chevron-down ms-auto" id="aiChevron" style="transition:transform 0.3s"></i>
            </button>
            <div id="aiDiagnosePanel" style="display:none; padding:0 20px 20px;">
                <p class="text-muted small mb-3">Mô tả triệu chứng bệnh nhân, AI sẽ phân tích tiền sử + bệnh án cũ để gợi ý chẩn đoán & điều trị.</p>
                <div class="mb-3">
                    <textarea class="form-control" id="aiSymptoms" rows="2" placeholder="VD: Bệnh nhân đau ngực trái khi gắng sức, kèm khó thở khi nằm, phù 2 chi dưới..." style="border:2px solid #c7d2fe; border-radius:12px;"></textarea>
                </div>
                <button type="button" class="btn px-4 py-2" id="btnAiDiagnose" style="background:linear-gradient(135deg, #6366f1, #8b5cf6); color:#fff; border-radius:20px; border:none; font-weight:600;">
                    <i class="fa-solid fa-brain me-2"></i>Phân tích & Gợi ý
                </button>
                <span id="aiLoading" style="display:none" class="ms-3 text-muted">
                    <i class="fa-solid fa-spinner fa-spin me-1"></i>Đang phân tích...
                </span>

                <!-- Kết quả AI -->
                <div id="aiResult" style="display:none;" class="mt-3">
                    <div class="rounded-3 p-3" style="background:rgba(255,255,255,0.8); border:1px solid #e0e7ff;">
                        <div class="d-flex align-items-center mb-2 gap-2">
                            <span class="badge" id="aiSeverityBadge">—</span>
                            <strong style="color:#6366f1;">Kết quả phân tích AI</strong>
                        </div>
                        
                        <div class="mb-2">
                            <small class="fw-bold text-danger"><i class="fa-solid fa-stethoscope me-1"></i>Chẩn đoán gợi ý:</small>
                            <div id="aiDiagnosisText" class="ms-3 mt-1" style="white-space:pre-wrap;"></div>
                        </div>
                        <div class="mb-2">
                            <small class="fw-bold text-success"><i class="fa-solid fa-prescription me-1"></i>Điều trị gợi ý:</small>
                            <div id="aiTreatmentText" class="ms-3 mt-1" style="white-space:pre-wrap;"></div>
                        </div>
                        <div class="mb-2" id="aiDifferentialWrap" style="display:none">
                            <small class="fw-bold" style="color:#f59e0b"><i class="fa-solid fa-triangle-exclamation me-1"></i>Chẩn đoán phân biệt:</small>
                            <div id="aiDifferentialText" class="ms-3 mt-1" style="white-space:pre-wrap;"></div>
                        </div>
                        <div class="mb-2" id="aiTestsWrap" style="display:none">
                            <small class="fw-bold" style="color:#3b82f6"><i class="fa-solid fa-flask-vial me-1"></i>CLS cần thêm:</small>
                            <div id="aiTestsText" class="ms-3 mt-1" style="white-space:pre-wrap;"></div>
                        </div>
                        <div class="mb-0" id="aiNotesWrap" style="display:none">
                            <small class="fw-bold text-info"><i class="fa-solid fa-clipboard me-1"></i>Lời dặn:</small>
                            <div id="aiNotesText" class="ms-3 mt-1" style="white-space:pre-wrap;"></div>
                        </div>

                        <hr class="my-2">
                        <button type="button" class="btn btn-sm px-3" id="btnApplyAI" style="background:#6366f1; color:#fff; border-radius:16px;">
                            <i class="fa-solid fa-check me-1"></i>Áp dụng vào form
                        </button>
                        <small class="text-muted ms-2">⚠️ Đây chỉ là gợi ý, BS quyết định cuối cùng.</small>
                    </div>
                </div>
                <div id="aiError" style="display:none" class="mt-3 alert alert-warning py-2 mb-0"></div>
            </div>
        </div>
        <!-- ====== END AI ====== -->

        <!-- ====== CHUẨN HÓA ICD-10 & CHẨN ĐOÁN ====== -->
        <div class="row">
            <!-- Mã ICD-10 -->
            <div class="col-md-5 mb-3 position-relative">
                <label for="icd10_search" class="form-label fw-bold text-primary"><i class="fa-solid fa-barcode me-1"></i>Phân loại bệnh ICD-10 <span class="text-danger">*</span></label>
                <div class="input-group">
                    <span class="input-group-text bg-light border-2"><i class="fa-solid fa-search"></i></span>
                    <input type="text" class="form-control border-2" id="icd10_search" placeholder="Nhập mã hoặc tên bệnh..." autocomplete="off">
                </div>
                <input type="hidden" name="icd10_code" id="icd10_code">
                
                <!-- Autocomplete Dropdown Panel -->
                <div id="icd10_suggestions" class="list-group position-absolute w-100 shadow" style="display:none; z-index:1050; max-height:250px; overflow-y:auto; border-radius:8px; border:1px solid #ddd;">
                    <!-- Suggesstions populated by JS -->
                </div>
                <small class="text-muted"><i class="fa-solid fa-info-circle mt-1"></i> Nhập để tìm kiếm (Ví dụ: I10, J00, đái tháo đường...)</small>
            </div>

            <!-- Chẩn đoán bệnh -->
            <div class="col-md-7 mb-3">
                <label for="diagnosis" class="form-label fw-bold text-danger"><i class="fa-solid fa-stethoscope me-1"></i>Chẩn đoán chi tiết <span class="text-danger">*</span></label>
                <textarea class="form-control border-danger border-opacity-50" id="diagnosis" name="diagnosis" rows="2" required placeholder="Ghi rõ tình trạng, triệu chứng, bệnh lý..." style="background:#fff5f5;"></textarea>
            </div>
        </div>

        <div class="mb-3">
            <label for="treatment" class="form-label fw-bold text-success">Hướng điều trị / Kê đơn thuốc</label>
            <textarea class="form-control border-success border-opacity-50" id="treatment" name="treatment" rows="4" placeholder="Phương pháp điều trị, chỉ định thuốc, phác đồ..." style="background:#f0fdf4;"></textarea>
        </div>

        <div class="mb-4">
            <label for="notes" class="form-label fw-bold">Ghi chú thêm & Lời dặn</label>
            <textarea class="form-control border-2" id="notes" name="notes" rows="2" placeholder="Tái khám sau bao nhiêu ngày, chế độ ăn kiêng..."></textarea>
        </div>

        <div class="d-flex gap-3 justify-content-end border-top pt-4">
            <a href="index.php?page=records" class="btn btn-outline-secondary" style="border-radius:20px; padding:10px 25px;"><i class="fa-solid fa-xmark me-2"></i>Hủy bỏ</a>
            <button type="submit" class="btn btn-primary" style="border-radius:20px; padding:10px 25px;"><i class="fa-solid fa-save me-2"></i>Lưu Bệnh án</button>
        </div>
    </form>
</div>

<!-- Scripts -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnToggle = document.getElementById('btnToggleAI');
    const panel = document.getElementById('aiDiagnosePanel');
    const chevron = document.getElementById('aiChevron');
    const btnDiagnose = document.getElementById('btnAiDiagnose');
    const btnApply = document.getElementById('btnApplyAI');

    // Toggle panel
    btnToggle.addEventListener('click', function() {
        const isHidden = panel.style.display === 'none';
        panel.style.display = isHidden ? 'block' : 'none';
        chevron.style.transform = isHidden ? 'rotate(180deg)' : 'rotate(0)';
    });

    // Call AI
    btnDiagnose.addEventListener('click', async function() {
        const patientId = document.getElementById('patient_id').value;
        const symptoms = document.getElementById('aiSymptoms').value.trim();

        if (!patientId) { alert('Vui lòng chọn bệnh nhân trước.'); return; }
        if (!symptoms) { alert('Vui lòng mô tả triệu chứng.'); return; }

        btnDiagnose.disabled = true;
        document.getElementById('aiLoading').style.display = 'inline';
        document.getElementById('aiResult').style.display = 'none';
        document.getElementById('aiError').style.display = 'none';

        try {
            const resp = await fetch('index.php?page=records&action=aiDiagnose', {
                method: 'POST',
                headers: {'Content-Type': 'application/json'},
                body: JSON.stringify({ patient_id: patientId, symptoms: symptoms })
            });
            const data = await resp.json();

            if (data.success && data.data) {
                const d = data.data;
                document.getElementById('aiDiagnosisText').textContent = d.diagnosis || '';
                document.getElementById('aiTreatmentText').textContent = d.treatment || '';
                
                // Severity badge
                const badge = document.getElementById('aiSeverityBadge');
                const severityMap = { 
                    low: {text:'Nhẹ', bg:'#22c55e'}, 
                    medium: {text:'Trung bình', bg:'#f59e0b'}, 
                    high: {text:'Nặng', bg:'#ef4444'}, 
                    critical: {text:'Nguy cấp', bg:'#dc2626'} 
                };
                const sev = severityMap[d.severity] || {text:d.severity, bg:'#94a3b8'};
                badge.textContent = sev.text;
                badge.style.background = sev.bg;
                badge.style.color = '#fff';

                // Optional fields
                if (d.differential) {
                    document.getElementById('aiDifferentialText').textContent = d.differential;
                    document.getElementById('aiDifferentialWrap').style.display = 'block';
                } else { document.getElementById('aiDifferentialWrap').style.display = 'none'; }

                if (d.suggested_tests) {
                    document.getElementById('aiTestsText').textContent = d.suggested_tests;
                    document.getElementById('aiTestsWrap').style.display = 'block';
                } else { document.getElementById('aiTestsWrap').style.display = 'none'; }

                if (d.notes) {
                    document.getElementById('aiNotesText').textContent = d.notes;
                    document.getElementById('aiNotesWrap').style.display = 'block';
                } else { document.getElementById('aiNotesWrap').style.display = 'none'; }

                document.getElementById('aiResult').style.display = 'block';

                // Store for apply
                btnApply._data = d;
            } else {
                document.getElementById('aiError').textContent = '⚠️ ' + (data.error || 'Lỗi không xác định');
                document.getElementById('aiError').style.display = 'block';
            }
        } catch(err) {
            document.getElementById('aiError').textContent = '⚠️ Lỗi kết nối: ' + err.message;
            document.getElementById('aiError').style.display = 'block';
        }

        btnDiagnose.disabled = false;
        document.getElementById('aiLoading').style.display = 'none';
    });

    // Apply AI result to form
    btnApply.addEventListener('click', function() {
        const d = btnApply._data;
        if (!d) return;
        document.getElementById('diagnosis').value = d.diagnosis || '';
        document.getElementById('treatment').value = d.treatment || '';
        
        let notes = '';
        if (d.notes) notes += d.notes;
        if (d.differential) notes += (notes ? '\n\n' : '') + '⚠️ Chẩn đoán phân biệt: ' + d.differential;
        if (d.suggested_tests) notes += (notes ? '\n\n' : '') + '🔬 CLS cần thêm: ' + d.suggested_tests;
        document.getElementById('notes').value = notes;

        // Try to automatically query ICD-10 matching AI diagnosis
        const cleanDiag = (d.diagnosis || '').split(',')[0].split(';')[0].trim();
        if (cleanDiag) {
            document.getElementById('icd10_search').value = cleanDiag;
            searchICD10(cleanDiag);
        }

        // Visual feedback
        btnApply.innerHTML = '<i class="fa-solid fa-check me-1"></i>Đã áp dụng!';
        btnApply.style.background = '#22c55e';
        setTimeout(() => { 
            btnApply.innerHTML = '<i class="fa-solid fa-check me-1"></i>Áp dụng vào form'; 
            btnApply.style.background = '#6366f1'; 
        }, 2000);
    });

    // ====== ICD-10 AUTOCOMPLETE LOGIC ======
    const icdSearch = document.getElementById('icd10_search');
    const icdCode = document.getElementById('icd10_code');
    const icdSuggest = document.getElementById('icd10_suggestions');

    let debounceTimer;

    icdSearch.addEventListener('input', function() {
        clearTimeout(debounceTimer);
        const q = icdSearch.value.trim();
        
        if (q.length < 1) {
            icdSuggest.style.display = 'none';
            icdCode.value = '';
            return;
        }

        debounceTimer = setTimeout(() => {
            searchICD10(q);
        }, 300);
    });

    async function searchICD10(query) {
        try {
            const resp = await fetch('index.php?page=records&action=searchIcd10&q=' + encodeURIComponent(query));
            const data = await resp.json();

            icdSuggest.innerHTML = '';
            if (data.length > 0) {
                data.forEach(item => {
                    const a = document.createElement('a');
                    a.href = '#';
                    a.className = 'list-group-item list-group-item-action py-2';
                    a.style.fontSize = '13px';
                    a.innerHTML = `<span class="badge bg-primary me-2">${item.code}</span><strong>${item.name}</strong> <span class="text-muted">(${item.category})</span>`;
                    
                    a.addEventListener('click', function(e) {
                        e.preventDefault();
                        
                        // Select code
                        icdCode.value = item.code;
                        icdSearch.value = `[${item.code}] ${item.name}`;
                        icdSuggest.style.display = 'none';

                        // Pre-populate diagnosis field if it is currently empty or contains placeholder
                        const diagText = document.getElementById('diagnosis');
                        if (diagText.value.trim() === '' || diagText.value.trim() === cleanDiagPlaceholder(diagText.value)) {
                            diagText.value = item.name;
                        }
                    });
                    icdSuggest.appendChild(a);
                });
                icdSuggest.style.display = 'block';
            } else {
                icdSuggest.style.display = 'none';
            }
        } catch(err) {
            console.error('ICD-10 Search Error:', err);
        }
    }

    function cleanDiagPlaceholder(val) {
        // checks if value is just some code name
        return val;
    }

    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!icdSearch.contains(e.target) && !icdSuggest.contains(e.target)) {
            icdSuggest.style.display = 'none';
        }
    });

    // ====== PATIENT AUTOCOMPLETE LOGIC ======
    const dropdownBtn = document.getElementById('patientDropdownBtn');
    const patientSearchInput = document.getElementById('patient_search_input');
    const patientListContainer = document.getElementById('patient_list_container');
    const patientIdInput = document.getElementById('patient_id');
    const selectedLabel = document.getElementById('selected_patient_label');
    
    let patientDebounceTimer;

    function escapeHtml(str) {
        if (!str) return '';
        return str.replace(/&/g, "&amp;")
                  .replace(/</g, "&lt;")
                  .replace(/>/g, "&gt;")
                  .replace(/"/g, "&quot;")
                  .replace(/'/g, "&#039;");
    }

    // Load patients and render the list
    function fetchAndRenderPatients(query = '') {
        fetch(`index.php?page=patients&action=searchAjax&q=${encodeURIComponent(query)}`)
            .then(res => res.json())
            .then(data => {
                patientListContainer.innerHTML = '';
                
                if (!data || data.length === 0) {
                    const item = document.createElement('div');
                    item.className = 'text-muted disabled text-center py-3';
                    item.style.fontSize = '13px';
                    item.textContent = 'Không tìm thấy bệnh nhân nào';
                    patientListContainer.appendChild(item);
                } else {
                    data.forEach(patient => {
                        const item = document.createElement('button');
                        item.type = 'button';
                        item.className = 'dropdown-item d-flex justify-content-between align-items-center py-2 border-bottom border-light';
                        item.style.fontSize = '13.5px';
                        item.style.textAlign = 'left';
                        item.style.width = '100%';
                        item.style.background = 'none';
                        item.style.border = 'none';
                        item.innerHTML = `
                            <div>
                                <strong class="text-dark">${escapeHtml(patient.name)}</strong>
                                <div class="text-muted small">${patient.phone ? escapeHtml(patient.phone) : 'Chưa cập nhật SĐT'}</div>
                            </div>
                            <span class="badge bg-light text-secondary border">Chọn</span>
                        `;
                        
                        item.addEventListener('click', function(e) {
                            e.preventDefault();
                            selectPatient(patient.id, patient.name, patient.phone);
                        });
                        patientListContainer.appendChild(item);
                    });
                }
            })
            .catch(err => {
                console.error('Lỗi khi tải bệnh nhân:', err);
            });
    }

    function selectPatient(id, name, phone) {
        patientIdInput.value = id;
        selectedLabel.innerHTML = `<span class="fw-bold text-success">${escapeHtml(name)}</span> <span class="text-muted small ms-2">(${phone ? escapeHtml(phone) : 'Chưa có SĐT'})</span>`;
        selectedLabel.classList.remove('text-muted');
        
        // Hide dropdown
        const dropdownEl = document.getElementById('patientDropdownBtn');
        const dropdown = bootstrap.Dropdown.getOrCreateInstance(dropdownEl);
        dropdown.hide();
    }

    // Load initial list on page load
    fetchAndRenderPatients('');

    // Focus search input when dropdown opens
    const dropdownMenu = document.querySelector('#patientDropdown .dropdown-menu');
    if (dropdownMenu) {
        dropdownMenu.addEventListener('shown.bs.dropdown', function () {
            patientSearchInput.focus();
        });
    }

    // Prevent dropdown from closing when clicking inside search input or list container
    if (patientSearchInput) {
        patientSearchInput.addEventListener('click', function(e) {
            e.stopPropagation();
        });
        
        patientSearchInput.addEventListener('input', function(e) {
            clearTimeout(patientDebounceTimer);
            const query = this.value.trim();
            
            patientDebounceTimer = setTimeout(() => {
                fetchAndRenderPatients(query);
            }, 300);
        });
    }
    if (patientListContainer) {
        patientListContainer.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
});
</script>

<style>
#patient_list_container .dropdown-item {
    cursor: pointer;
    transition: background 0.15s ease-in-out;
    border-radius: 4px;
    margin-bottom: 2px;
}
#patient_list_container .dropdown-item:hover {
    background-color: #f8f9fa;
}
#patientDropdownBtn {
    background-color: #fff;
    border: 1px solid #dee2e6;
}
#patientDropdownBtn:focus {
    box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
    border-color: #86b7fe;
}
</style>