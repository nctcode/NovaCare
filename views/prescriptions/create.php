<!-- Form Tạo Đơn thuốc Premium -->
<div class="card shadow-sm border-0 rounded-4 mx-auto mb-5" style="max-width: 1000px;" data-aos="fade-up">
    <div class="card-header bg-white border-bottom-0 pt-4 pb-3 px-4">
        <h4 class="m-0 text-primary fw-bold"><i class="fa-solid fa-file-prescription me-2"></i>Tạo Đơn thuốc mới</h4>
        <p class="text-muted small mt-1 mb-0">Cấp phát thuốc dựa trên hồ sơ bệnh án của bệnh nhân.</p>
    </div>

    <div class="card-body px-4 pb-4 pt-0">
        <form method="POST" action="index.php?page=prescriptions&action=store" id="createPrescriptionForm">
            <?php echo Security::csrfField(); ?>
        
        <div class="mb-4">
            <label class="form-label fw-bold text-muted text-uppercase" style="font-size: 0.85rem;"><i class="fa-solid fa-folder-open me-1"></i>Hồ sơ bệnh án <span class="text-danger">*</span></label>
            <div class="dropdown" id="mrDropdown">
                <button class="form-select form-select-lg text-start d-flex justify-content-between align-items-center bg-light border-0 shadow-none" type="button" id="mrDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false" style="background-image: none;">
                    <span id="selected_mr_label" class="text-muted">-- Tìm và chọn hồ sơ bệnh án --</span>
                    <i class="fa-solid fa-chevron-down text-muted fs-7"></i>
                </button>
                <div class="dropdown-menu w-100 p-3 shadow border-0 rounded-3 mt-1" aria-labelledby="mrDropdownBtn" style="min-width: 100%;">
                    <!-- Search input -->
                    <div class="input-group mb-3">
                        <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-magnifying-glass"></i></span>
                        <input type="text" id="mr_search_input" class="form-control border-start-0 ps-0 shadow-none" placeholder="Tìm tên bệnh nhân hoặc chẩn đoán..." autocomplete="off">
                    </div>
                    
                    <!-- Scrollable list -->
                    <div id="mr_list_container" style="max-height: 250px; overflow-y: auto;">
                        <?php foreach ($medicalRecords as $mr): ?>
                            <a class="dropdown-item py-2 px-3 border-bottom mr-item" href="#" data-value="<?= $mr['id'] ?>" data-name="<?= htmlspecialchars($mr['patient_name']) ?>" data-diagnosis="<?= htmlspecialchars($mr['diagnosis']) ?>">
                                <div class="d-flex justify-content-between">
                                    <strong class="text-dark"><i class="fa-regular fa-user me-1"></i> <?= htmlspecialchars($mr['patient_name']) ?></strong>
                                    <small class="text-muted"><i class="fa-regular fa-calendar me-1"></i> <?= date('d/m/Y', strtotime($mr['created_at'])) ?></small>
                                </div>
                                <div class="text-muted small mt-1 text-truncate" style="max-width: 90%;"><i class="fa-solid fa-stethoscope me-1 text-danger"></i> <?= htmlspecialchars($mr['diagnosis']) ?></div>
                            </a>
                        <?php endforeach; ?>
                        <?php if(empty($medicalRecords)): ?>
                            <div class="text-center text-muted p-3">Không có hồ sơ bệnh án nào.</div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            <input type="hidden" name="medical_record_id" id="medical_record_id" required>
        </div>

        <!-- ====== AI GỢI Ý THUỐC ====== -->
        <div class="mb-4 rounded-3 border" style="background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%); overflow:hidden;">
            <div class="p-3 d-flex justify-content-between align-items-center cursor-pointer" id="toggleAiMedicineBtn" style="cursor: pointer;">
                <div>
                    <h6 class="mb-1 text-success fw-bold"><i class="fa-solid fa-robot me-2"></i>AI Trợ lý Dược sĩ (Beta)</h6>
                    <p class="mb-0 text-muted small">Phân tích chẩn đoán bệnh án và gợi ý phác đồ thuốc tối ưu nhất.</p>
                </div>
                <button type="button" class="btn btn-success rounded-pill fw-medium shadow-sm px-4 btn-sm" id="btnAnalyzeMedicine">
                    <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Phân tích
                </button>
            </div>
            <div id="aiMedicinePanel" class="p-3 border-top border-success border-opacity-25 bg-white" style="display: none;">
                <div id="aiMedicineLoading" class="text-center py-4" style="display: none;">
                    <div class="spinner-grow text-success" role="status" style="width: 3rem; height: 3rem;"></div>
                    <p class="mt-3 text-success fw-medium">Đang truy cập cơ sở dữ liệu Dược Quốc gia...</p>
                    <p class="small text-muted" id="aiMedicineLoadingText">Đang phân tích chẩn đoán của bệnh nhân...</p>
                </div>
                <div id="aiMedicineResult" style="display: none;">
                    <div class="alert alert-success border-0 bg-success bg-opacity-10 mb-3">
                        <i class="fa-solid fa-check-circle me-2"></i> Đã tìm thấy phác đồ điều trị phù hợp cho chẩn đoán: <strong id="aiDiagnosisText">...</strong>
                    </div>
                    <div id="aiMedicineList" class="d-flex flex-column gap-2 mb-3">
                        <!-- Medicine suggestions will appear here -->
                    </div>
                    <p class="text-muted small fst-italic mb-0"><i class="fa-solid fa-circle-info me-1"></i> Lưu ý: Bác sĩ cần kiểm tra lại tương tác thuốc và thể trạng thực tế của bệnh nhân trước khi kê đơn.</p>
                </div>
            </div>
        </div>

        <h6 class="mb-3 fw-bold"><i class="fa-solid fa-pills me-2 text-primary"></i>Danh sách thuốc kê</h6>

        <div id="medicineItems" class="d-flex flex-column gap-3 mb-3">
            <div class="medicine-item card border border-primary border-opacity-25 shadow-sm rounded-3">
                <div class="card-body p-3">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="badge bg-primary rounded-pill medicine-index">Thuốc #1</span>
                        <button type="button" class="btn btn-sm btn-link text-danger p-0 shadow-none btn-remove-medicine" style="display: none;" onclick="removeMedicineItem(this)"><i class="fa-solid fa-trash"></i> Xóa</button>
                    </div>
                    <div class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Chọn thuốc</label>
                            <select class="form-select bg-light border-0 shadow-none medicine-select" name="medicine_id[]" required>
                                <option value="">-- Danh mục thuốc --</option>
                                <?php foreach ($medicines as $m): ?>
                                    <option value="<?= $m['id'] ?>" data-name="<?= htmlspecialchars($m['name']) ?>"><?= htmlspecialchars($m['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted small fw-bold">Liều lượng</label>
                            <input type="text" class="form-control bg-light border-0 shadow-none medicine-dosage" name="dosage[]" placeholder="VD: 500mg">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label text-muted small fw-bold">Thời gian</label>
                            <input type="text" class="form-control bg-light border-0 shadow-none medicine-duration" name="duration[]" placeholder="VD: 5 ngày">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label text-muted small fw-bold">Hướng dẫn sử dụng</label>
                            <input type="text" class="form-control bg-light border-0 shadow-none medicine-instructions" name="instructions[]" placeholder="VD: Sáng 1 viên, tối 1 viên">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <button type="button" class="btn btn-outline-primary fw-medium rounded-pill px-4 mb-4" onclick="addMedicineItem()">
            <i class="fa-solid fa-plus me-1"></i> Thêm loại thuốc khác
        </button>

        <div class="d-flex gap-3 justify-content-end border-top pt-4 mt-2">
            <a href="index.php?page=prescriptions" class="btn btn-light border fw-medium rounded-pill px-4 py-2"><i class="fa-solid fa-xmark me-2"></i>Hủy bỏ</a>
            <button type="submit" class="btn btn-primary fw-bold rounded-pill shadow-sm px-5 py-2"><i class="fa-solid fa-save me-2"></i>Lưu đơn thuốc</button>
        </div>
    </form>
    </div>
</div>

<script>
// --- Quản lý Bệnh án Dropdown & Tìm kiếm ---
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('mr_search_input');
    const items = document.querySelectorAll('.mr-item');
    const dropdownBtn = document.getElementById('mrDropdownBtn');
    const hiddenInput = document.getElementById('medical_record_id');
    const labelSpan = document.getElementById('selected_mr_label');

    // Chức năng tìm kiếm
    if (searchInput) {
        searchInput.addEventListener('keyup', function() {
            const filter = this.value.toLowerCase();
            items.forEach(item => {
                const text = item.textContent || item.innerText;
                item.style.display = text.toLowerCase().indexOf(filter) > -1 ? "" : "none";
            });
        });
    }

    // Chức năng chọn hồ sơ
    items.forEach(item => {
        item.addEventListener('click', function(e) {
            e.preventDefault();
            const val = this.getAttribute('data-value');
            const name = this.getAttribute('data-name');
            const diag = this.getAttribute('data-diagnosis');
            
            hiddenInput.value = val;
            labelSpan.innerHTML = `<span class="text-dark fw-bold">${name}</span> - <span class="text-danger">${diag}</span>`;
            
            // Ẩn dropdown menu
            const dropdown = bootstrap.Dropdown.getInstance(dropdownBtn);
            if (dropdown) dropdown.hide();
        });
    });
});

// --- Quản lý Danh sách thuốc ---
let medicineCount = 1;

function updateMedicineIndices() {
    const items = document.querySelectorAll('.medicine-item');
    items.forEach((item, index) => {
        item.querySelector('.medicine-index').textContent = `Thuốc #${index + 1}`;
        const removeBtn = item.querySelector('.btn-remove-medicine');
        if (items.length > 1) {
            removeBtn.style.display = 'block';
        } else {
            removeBtn.style.display = 'none';
        }
    });
}

function addMedicineItem() {
    const container = document.getElementById('medicineItems');
    const originalItem = container.querySelector('.medicine-item');
    const clone = originalItem.cloneNode(true);
    
    // Clear values
    clone.querySelectorAll('input').forEach(input => input.value = '');
    clone.querySelectorAll('select').forEach(select => select.selectedIndex = 0);
    
    // Add visual effect
    clone.style.opacity = '0';
    clone.style.transform = 'translateY(-10px)';
    clone.style.transition = 'all 0.3s ease';
    
    container.appendChild(clone);
    
    // Trigger reflow & animate
    setTimeout(() => {
        clone.style.opacity = '1';
        clone.style.transform = 'translateY(0)';
        updateMedicineIndices();
    }, 10);
}

function removeMedicineItem(btn) {
    const items = document.querySelectorAll('.medicine-item');
    if (items.length > 1) {
        const item = btn.closest('.medicine-item');
        item.style.opacity = '0';
        item.style.transform = 'translateY(10px)';
        setTimeout(() => {
            item.remove();
            updateMedicineIndices();
        }, 300);
    }
}

// --- AI Trợ lý Dược sĩ ---
document.getElementById('btnAnalyzeMedicine').addEventListener('click', function(e) {
    e.preventDefault();
    const mrId = document.getElementById('medical_record_id').value;
    if (!mrId) {
        alert("Vui lòng CHỌN HỒ SƠ BỆNH ÁN trước để AI có thể phân tích chẩn đoán!");
        return;
    }
    
    // Lấy chẩn đoán từ dropdown
    let diagnosis = "";
    document.querySelectorAll('.mr-item').forEach(item => {
        if (item.getAttribute('data-value') === mrId) {
            diagnosis = item.getAttribute('data-diagnosis');
        }
    });
    
    document.getElementById('aiMedicinePanel').style.display = 'block';
    document.getElementById('aiMedicineLoading').style.display = 'block';
    document.getElementById('aiMedicineResult').style.display = 'none';
    
    const loadingText = document.getElementById('aiMedicineLoadingText');
    loadingText.textContent = "Đang gửi dữ liệu chẩn đoán tới Server AI...";
    
    // Gọi thẳng vào Backend PHP của hệ thống (Endpoint an toàn, giấu kín API Key)
    const AI_API_URL = "index.php?page=prescriptions&action=suggestMedicineAI"; 
    
    fetch(AI_API_URL, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            diagnosis: diagnosis
        })
    })
    .then(response => {
        if (!response.ok) throw new Error("Lỗi kết nối Backend (Status: " + response.status + ")");
        return response.json();
    })
    .then(data => {
        document.getElementById('aiMedicineLoading').style.display = 'none';
        document.getElementById('aiMedicineResult').style.display = 'block';
        document.getElementById('aiDiagnosisText').textContent = diagnosis;
        
        const aiList = document.getElementById('aiMedicineList');
        aiList.innerHTML = ''; // Xóa kết quả cũ
        
        // Backend trả về: { success: true, medicines: [{...}] }
        if (data.success && data.medicines && data.medicines.length > 0) {
            data.medicines.forEach(med => {
                aiList.innerHTML += `
                    <div class="d-flex justify-content-between align-items-center bg-light p-2 rounded border">
                        <div>
                            <i class="fa-solid fa-pills text-primary me-2"></i>
                            <strong>${med.name}</strong> 
                            <span class="badge bg-secondary ms-2">${med.type || 'Điều trị'}</span>
                        </div>
                        <button type="button" class="btn btn-sm btn-outline-success rounded-pill" 
                                onclick="applyAiMedicine('${med.name}', '${med.dosage}', '${med.duration}', '${med.instructions}')">
                            <i class="fa-solid fa-plus"></i> Kê đơn này
                        </button>
                    </div>
                `;
            });
        } else {
            const errorMsg = data.error || 'AI không tìm thấy phác đồ phù hợp cho chẩn đoán này.';
            aiList.innerHTML = '<div class="alert alert-warning"><i class="fa-solid fa-triangle-exclamation me-2"></i>' + errorMsg + '</div>';
        }
    })
    .catch(error => {
        console.error("AI Error:", error);
        document.getElementById('aiMedicineLoading').style.display = 'none';
        
        // Báo lỗi để bạn dễ dàng Debug API
        alert("Không thể kết nối đến AI API: " + error.message + "\nVui lòng kiểm tra lại đường dẫn API.");
        
        // Phục hồi lại dữ liệu giả lập (Fallback) nếu API chết để không bị trắng màn hình
        document.getElementById('aiMedicineResult').style.display = 'block';
        document.getElementById('aiDiagnosisText').textContent = diagnosis + " (DỮ LIỆU DỰ PHÒNG)";
        document.getElementById('aiMedicineList').innerHTML = `
            <div class="d-flex justify-content-between align-items-center bg-danger bg-opacity-10 p-2 rounded border mb-2">
                <div class="text-danger small"><i class="fa-solid fa-triangle-exclamation me-1"></i>Lỗi gọi API. Đang hiển thị kết quả dự phòng nội bộ.</div>
            </div>
            <div class="d-flex justify-content-between align-items-center bg-light p-2 rounded border mb-1">
                <div><i class="fa-solid fa-pills text-primary me-2"></i>Kháng sinh / Điều trị chính</div>
                <button type="button" class="btn btn-sm btn-outline-success rounded-pill" onclick="applyAiMedicine('Amoxicillin 500mg', '1 viên', '5 ngày', 'Uống sau bữa ăn sáng và tối')"><i class="fa-solid fa-plus"></i> Kê đơn này</button>
            </div>
            <div class="d-flex justify-content-between align-items-center bg-light p-2 rounded border mb-1">
                <div><i class="fa-solid fa-tablets text-warning me-2"></i>Giảm đau / Hạ sốt</div>
                <button type="button" class="btn btn-sm btn-outline-success rounded-pill" onclick="applyAiMedicine('Paracetamol 500mg', '1 viên', '3 ngày', 'Uống khi sốt > 38.5 độ hoặc đau nhức')"><i class="fa-solid fa-plus"></i> Kê đơn này</button>
            </div>
        `;
    });
});

function applyAiMedicine(medName, dosage, duration, instructions) {
    // Tìm select có option tương ứng với medName
    let medId = "";
    const options = document.querySelectorAll('.medicine-select option');
    options.forEach(opt => {
        if (opt.getAttribute('data-name') && opt.getAttribute('data-name').includes(medName.split(' ')[0])) {
            medId = opt.value;
        }
    });
    
    if (!medId) {
        alert("Thuốc " + medName + " tạm hết trong kho hoặc không có trong danh mục của bệnh viện.");
        return;
    }
    
    // Tìm thẻ thuốc trống đầu tiên, nếu không có thì thêm mới
    let targetItem = null;
    const items = document.querySelectorAll('.medicine-item');
    items.forEach(item => {
        const select = item.querySelector('.medicine-select');
        if (!targetItem && select.value === "") {
            targetItem = item;
        }
    });
    
    if (!targetItem) {
        addMedicineItem();
        // Cần đợi render xong mới lấy item cuối cùng
        setTimeout(() => {
            const newItems = document.querySelectorAll('.medicine-item');
            fillMedicineData(newItems[newItems.length - 1], medId, dosage, duration, instructions);
        }, 100);
    } else {
        fillMedicineData(targetItem, medId, dosage, duration, instructions);
    }
}

function fillMedicineData(item, medId, dosage, duration, instructions) {
    item.querySelector('.medicine-select').value = medId;
    item.querySelector('.medicine-dosage').value = dosage;
    item.querySelector('.medicine-duration').value = duration;
    item.querySelector('.medicine-instructions').value = instructions;
    
    // Highlight effect
    item.style.transition = 'all 0.5s ease';
    item.style.boxShadow = '0 0 0 3px rgba(46, 204, 113, 0.4)';
    item.style.borderColor = '#2ecc71';
    
    // Create an animated text to indicate auto-fill
    const aiBadge = document.createElement('span');
    aiBadge.className = 'badge bg-success ms-2 rounded-pill ai-badge';
    aiBadge.innerHTML = '<i class="fa-solid fa-robot"></i> AI Tự điền';
    aiBadge.style.opacity = '0';
    aiBadge.style.transition = 'opacity 0.5s ease';
    
    const indexBadge = item.querySelector('.medicine-index');
    indexBadge.parentNode.insertBefore(aiBadge, indexBadge.nextSibling);
    
    setTimeout(() => {
        aiBadge.style.opacity = '1';
    }, 50);
    
    setTimeout(() => {
        item.style.boxShadow = '0 0.125rem 0.25rem rgba(0, 0, 0, 0.075)';
        item.style.borderColor = 'rgba(13, 110, 253, 0.25)';
        setTimeout(() => { aiBadge.style.opacity = '0'; }, 2000);
        setTimeout(() => { aiBadge.remove(); }, 2500);
    }, 2000);
}
</script>

