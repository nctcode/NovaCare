<!-- Form Tạo Chỉ định CLS -->
<div class="content-card" style="max-width: 700px;">
    <div class="card-header">
        <h5><i class="fa-solid fa-flask-vial me-2"></i>Tạo Chỉ định Cận lâm sàng</h5>
        <a href="index.php?page=lab-orders" class="btn-action btn-edit">
            <i class="fa-solid fa-arrow-left"></i> Quay lại
        </a>
    </div>
    <div class="card-body">
        <form method="POST" action="index.php?page=lab-orders&action=store">
            <?= Security::csrfField() ?>

            <div class="mb-3">
                <label class="form-label fw-semibold">Bệnh nhân <span class="text-danger">*</span></label>
                <div class="dropdown" id="patientDropdown">
                    <button class="form-select text-start d-flex justify-content-between align-items-center" type="button" id="patientDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false" style="background-image: none; height: 38px;">
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

            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Loại chỉ định <span class="text-danger">*</span></label>
                    <select name="order_type" class="form-select" required>
                        <option value="lab_test">🧪 Xét nghiệm</option>
                        <option value="imaging">📷 Chẩn đoán hình ảnh</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label fw-semibold">Mức ưu tiên</label>
                    <select name="priority" class="form-select">
                        <option value="normal">Bình thường</option>
                        <option value="urgent">🔴 Cấp cứu</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Tên xét nghiệm / Chụp chiếu <span class="text-danger">*</span></label>
                <input type="text" name="test_name" class="form-control" placeholder="VD: Xét nghiệm công thức máu, X-quang phổi..." required>
                <div class="form-text">Nhập tên cụ thể của xét nghiệm hoặc phương pháp chẩn đoán hình ảnh</div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Ghi chú lâm sàng</label>
                <textarea name="notes" class="form-control" rows="3" placeholder="Ghi chú thêm cho KTV (triệu chứng, yêu cầu đặc biệt...)"></textarea>
            </div>

            <input type="hidden" name="appointment_id" value="<?= $_GET['appointment_id'] ?? '' ?>">

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="index.php?page=lab-orders" class="btn btn-outline-secondary px-4" style="border-radius:20px">Hủy</a>
                <button type="submit" class="btn btn-primary px-4" style="border-radius:20px">
                    <i class="fa-solid fa-paper-plane me-2"></i>Gửi chỉ định
                </button>
            </div>
        </form>
    </div>
</div>

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

<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropdownBtn = document.getElementById('patientDropdownBtn');
    const searchInput = document.getElementById('patient_search_input');
    const listContainer = document.getElementById('patient_list_container');
    const patientIdInput = document.getElementById('patient_id');
    const selectedLabel = document.getElementById('selected_patient_label');
    
    let debounceTimer;

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
                listContainer.innerHTML = '';
                
                if (!data || data.length === 0) {
                    const item = document.createElement('div');
                    item.className = 'text-muted disabled text-center py-3';
                    item.style.fontSize = '13px';
                    item.textContent = 'Không tìm thấy bệnh nhân nào';
                    listContainer.appendChild(item);
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
                        listContainer.appendChild(item);
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
    dropdownMenu.addEventListener('shown.bs.dropdown', function () {
        searchInput.focus();
    });

    // Prevent dropdown from closing when clicking inside search input or list container
    searchInput.addEventListener('click', function(e) {
        e.stopPropagation();
    });
    listContainer.addEventListener('click', function(e) {
        e.stopPropagation();
    });

    // Search when typing
    searchInput.addEventListener('input', function(e) {
        clearTimeout(debounceTimer);
        const query = this.value.trim();
        
        debounceTimer = setTimeout(() => {
            fetchAndRenderPatients(query);
        }, 300);
    });
});
</script>