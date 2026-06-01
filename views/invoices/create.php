<!-- Create Invoice (EMR Premium Design) -->
<div class="premium-card" data-aos="fade-up">
    <div class="premium-header">
        <h5 class="m-0"><i class="fa-solid fa-file-circle-plus me-2"></i>Lập Hóa Đơn & Thanh Toán</h5>
        <p class="text-muted mb-0" style="font-size:13px;">Chọn bệnh nhân để tải dữ liệu viện phí/đơn thuốc cần thanh toán.</p>
    </div>
    
    <div class="card-body p-4">
        <form method="POST" action="index.php?page=invoices&action=store" id="invoiceForm">
            <?php echo Security::csrfField(); ?>
            
            <!-- 1. Thông tin chung -->
            <div class="info-box mb-4">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label mb-1">Bệnh nhân <span class="text-danger">*</span></label>
                        <select name="patient_id" id="patientSelect" class="form-select select2" required>
                            <option value="">-- Tìm và chọn bệnh nhân --</option>
                            <?php foreach ($patients as $p): ?>
                                <?php 
                                    $dobStr = !empty($p['date_of_birth']) ? date('d/m/Y', strtotime($p['date_of_birth'])) : '-'; 
                                    $label = htmlspecialchars($p['name']) . " (Mã: #" . $p['id'] . " - SĐT: " . htmlspecialchars($p['phone'] ?? '-') . " - NS: " . $dobStr . ")";
                                ?>
                                <option value="<?= $p['id'] ?>" data-insurance="<?= htmlspecialchars($p['insurance_number'] ?? '') ?>"><?= $label ?></option>
                            <?php endforeach; ?>
                        </select>
                        
                        <!-- Đơn thuốc tự động xếp gọn bên dưới select bệnh nhân khi xuất hiện -->
                        <div id="prescriptionContainer" class="mt-2.5 p-2 rounded-3 border" style="display: none; background: rgba(59, 130, 246, 0.03); border-color: rgba(59, 130, 246, 0.15) !important;">
                            <label class="form-label text-primary mb-1" style="font-size:12px;"><i class="fa-solid fa-prescription me-1"></i>Đơn thuốc chưa thanh toán</label>
                            <select name="prescription_id" id="prescriptionSelect" class="form-select form-select-sm">
                                <option value="">-- Không chọn --</option>
                            </select>
                        </div>

                        <!-- Hồ sơ điều trị nội trú tự động xếp gọn bên dưới select bệnh nhân khi xuất hiện -->
                        <div id="inpatientContainer" class="mt-2.5 p-2 rounded-3 border" style="display: none; background: rgba(16, 185, 129, 0.03); border-color: rgba(16, 185, 129, 0.15) !important;">
                            <div class="form-check form-switch m-0 d-flex align-items-center gap-2">
                                <input class="form-check-input" type="checkbox" role="switch" id="inpatientCheck" style="cursor: pointer;">
                                <label class="form-check-label text-success fw-semibold" for="inpatientCheck" style="font-size:12.5px; cursor: pointer;">
                                    <i class="fa-solid fa-bed me-1"></i>Thanh toán phí giường nội trú
                                </label>
                            </div>
                            <div id="inpatientBedDetails" class="mt-1 small text-muted" style="font-size:11px; margin-left: 28px;"></div>
                        </div>
                    </div>
                    
                    <div class="col-md-4">
                        <label class="form-label mb-1">Mã số BHYT</label>
                        <input type="text" name="insurance_number" id="insuranceNumber" class="form-control" placeholder="Tự động điền hoặc nhập tay">
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label mb-1">Tỷ lệ BHYT (%)</label>
                        <select name="insurance_rate" id="insuranceRate" class="form-select" onchange="recalc()">
                            <option value="0">0% (Không BHYT)</option>
                            <option value="40">40% (Trái tuyến)</option>
                            <option value="60">60% (Trái tuyến tỉnh)</option>
                            <option value="80">80% (Đúng tuyến)</option>
                            <option value="100">100% (Ưu đãi/Hộ nghèo)</option>
                        </select>
                    </div>
                    
                    <div class="col-md-2">
                        <label class="form-label mb-1">Giảm giá (VNĐ)</label>
                        <input type="number" name="discount" class="form-control" value="0" min="0" id="discountInput" onchange="recalc()">
                    </div>
                </div>
            </div>

            <input type="hidden" name="appointment_id" id="appointmentIdInput" value="">
            <input type="hidden" name="admission_id" id="admissionIdInput" value="<?= $presetAdmissionId > 0 ? $presetAdmissionId : '' ?>">

            <!-- 2. Chi tiết hóa đơn (Bảng dữ liệu) -->
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold mb-0 text-dark" style="font-size:14.5px;"><i class="fa-solid fa-list me-1.5 text-primary"></i> Chi tiết khoản phí hóa đơn</h6>
                    <span class="badge bg-secondary-subtle text-secondary" style="font-size:11px;">Mức giá theo quy định viện phí</span>
                </div>
                
                <div class="table-wrapper border rounded-4 overflow-hidden shadow-sm bg-white mb-2">
                    <table class="premium-table" id="itemsTable">
                        <thead>
                            <tr>
                                <th style="width: 18%;">Phân loại</th>
                                <th style="width: 38%;">Tên dịch vụ / Thuốc chỉ định</th>
                                <th style="width: 10%; text-align: center;">Số lượng</th>
                                <th style="width: 16%; text-align: right;">Đơn giá</th>
                                <th style="width: 18%; text-align: right;">Thành tiền</th>
                                <?php if ($_SESSION['user']['role'] !== 'cashier'): ?>
                                <th style="width: 6%; text-align: center;"></th>
                                <?php endif; ?>
                            </tr>
                        </thead>
                        <tbody id="itemsBody">
                            <!-- Dynamic rows loaded here -->
                        </tbody>
                    </table>
                </div>
                
                <div class="d-flex justify-content-between align-items-center">
                    <!-- Nút "+ Thêm dòng" chỉ hiển thị đối với Admin, ẩn với Thu ngân (Cashier) -->
                    <div>
                        <?php if ($_SESSION['user']['role'] !== 'cashier'): ?>
                            <button type="button" class="btn btn-sm btn-outline-primary" style="border-radius:20px; font-weight:600; padding: 6px 18px;" onclick="addRow()">
                                <i class="fa-solid fa-plus me-1"></i>Thêm dòng thu
                            </button>
                        <?php else: ?>
                            <small class="text-muted"><i class="fa-solid fa-circle-info me-1"></i> Thu ngân không có quyền thêm bớt hoặc sửa đổi chi tiết hóa đơn.</small>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Bảng tính toán tổng cộng ở bên phải dưới chân bảng -->
                    <div style="min-width: 320px;">
                        <div class="total-summary-box">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-secondary small fw-bold">TỔNG CỘNG:</span>
                                <span class="fw-bold text-dark" id="totalDisplay">0đ</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-danger small fw-bold">GIẢM GIÁ KHÁC:</span>
                                <span class="fw-bold text-danger" id="discountDisplay">-0đ</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2" id="insuranceRow" style="display: none;">
                                <span class="text-success small fw-bold">BHYT CHI TRẢ (<span id="insuranceRateDisplay">0%</span>):</span>
                                <span class="fw-bold text-success" id="insuranceDisplay">-0đ</span>
                            </div>
                            <div class="final-payment-banner d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-primary" style="font-size:13.5px;">BỆNH NHÂN THỰC TRẢ:</span>
                                <span class="fw-extrabold text-primary" id="finalDisplay" style="font-size: 1.35rem; font-weight: 800;">0đ</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- 3. Ghi chú -->
            <div class="mb-4">
                <label class="form-label mb-1">Ghi chú hóa đơn</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="Ghi chú thêm về miễn giảm, lý do đặc biệt (nếu có)..."></textarea>
            </div>

            <!-- 4. Nút hành động -->
            <div class="d-flex justify-content-end gap-2.5 border-top pt-4">
                <a href="index.php?page=invoices" class="btn btn-premium btn-premium-secondary">
                    <i class="fa-solid fa-arrow-left me-2"></i>Quay lại danh sách
                </a>
                <button type="submit" class="btn btn-premium btn-premium-primary">
                    <i class="fa-solid fa-save me-2"></i>Xác nhận & Lưu hóa đơn
                </button>
            </div>
        </form>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<style>
@import url('https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&family=JetBrains+Mono:wght@400;500&display=swap');

.premium-card {
    font-family: 'Outfit', sans-serif;
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 24px;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.02);
    overflow: hidden;
}

.premium-header {
    background: linear-gradient(135deg, #f0f9ff 0%, #ffffff 100%);
    border-bottom: 1px solid #e2e8f0;
    padding: 24px;
}

.premium-header h5 {
    font-weight: 800;
    background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-size: 1.25rem;
}

.info-box {
    background: #f8fafc;
    border: 1px solid #e2e8f0;
    border-radius: 16px;
    padding: 20px;
    margin-bottom: 24px;
}

.form-label {
    font-size: 12.5px;
    font-weight: 600;
    color: #475569;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.form-control, .form-select {
    border-radius: 12px;
    border: 1.5px solid #cbd5e1;
    padding: 10px 16px;
    font-size: 14px;
    font-weight: 500;
    color: #1e293b;
    transition: all 0.2s ease-in-out;
}

.form-control:focus, .form-select:focus {
    border-color: #3b82f6;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
    background-color: #ffffff;
}

/* Custom Select2 Styling */
.select2-container .select2-selection--single {
    height: 43px;
    border-radius: 12px;
    border: 1.5px solid #cbd5e1;
    display: flex;
    align-items: center;
    padding: 0 8px;
    font-weight: 500;
    color: #1e293b;
    font-size: 14px;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
    height: 41px;
    right: 12px;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: #1e293b;
    line-height: 41px;
}
.select2-container--open .select2-selection--single {
    border-color: #3b82f6;
    box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.1);
}
.select2-dropdown {
    border: 1.5px solid #cbd5e1;
    border-radius: 12px;
    box-shadow: 0 10px 25px rgba(0,0,0,0.08);
    overflow: hidden;
}
.select2-search__field {
    border-radius: 8px !important;
}

/* Custom Table Style */
.premium-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.premium-table th {
    background: #f8fafc;
    color: #475569;
    font-size: 12px;
    font-weight: 700;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    padding: 14px 16px;
    border-bottom: 1.5px solid #e2e8f0;
}

.premium-table td {
    padding: 14px 16px;
    border-bottom: 1.5px solid #f1f5f9;
    vertical-align: middle;
}

.premium-table tr:hover td {
    background-color: #f8fafc;
}

.total-summary-box {
    background: #f8fafc;
    border: 1.5px solid #cbd5e1;
    border-radius: 16px;
    padding: 20px;
    margin-top: 10px;
}

.final-payment-banner {
    background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    border: 1px solid #bfdbfe;
    border-radius: 12px;
    padding: 14px 16px;
    margin-top: 12px;
}

.btn-premium {
    border-radius: 12px;
    padding: 10px 24px;
    font-weight: 600;
    font-size: 14px;
    transition: all 0.2s ease;
}

.btn-premium-primary {
    background: linear-gradient(135deg, #0ea5e9 0%, #2563eb 100%);
    color: #ffffff;
    border: none;
    box-shadow: 0 4px 12px rgba(37, 99, 235, 0.15);
}

.btn-premium-primary:hover {
    background: linear-gradient(135deg, #38bdf8 0%, #2563eb 100%);
    transform: translateY(-1px);
    box-shadow: 0 6px 16px rgba(37, 99, 235, 0.25);
}

.btn-premium-secondary {
    background: #ffffff;
    border: 1.5px solid #cbd5e1;
    color: #475569;
}

.btn-premium-secondary:hover {
    background: #f1f5f9;
    color: #0f172a;
    border-color: #94a3b8;
}

.btn-trash-subtle {
    width: 36px;
    height: 36px;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    border-radius: 10px;
    background: #fef2f2;
    border: 1px solid #fee2e2;
    color: #ef4444;
    transition: all 0.2s ease;
}

.btn-trash-subtle:hover {
    background: #ef4444;
    color: #ffffff;
    box-shadow: 0 4px 10px rgba(239, 68, 68, 0.15);
}
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
// Services & Medicines data from PHP
const servicesData = <?= json_encode($services) ?>;
const medicinesData = <?= json_encode($medicines) ?>;

// Preset values from URL scanning
const presetPrescriptionId = <?= json_encode($presetPrescriptionId ?? 0) ?>;
const presetAdmissionId = <?= json_encode($presetAdmissionId ?? 0) ?>;
const presetPatientId = <?= json_encode($presetPatientId ?? 0) ?>;
const userRole = <?= json_encode($_SESSION['user']['role']) ?>;

let rowCount = 0;

function addRow() {
    rowCount++;
    const tbody = document.getElementById('itemsBody');
    const tr = document.createElement('tr');
    tr.id = 'row_' + rowCount;
    
    // Phân quyền cho Thu ngân (Cashier)
    const isCashier = (userRole === 'cashier');
    const pointerEventsStyle = isCashier ? 'style="pointer-events: none; background-color: #f1f5f9; cursor: not-allowed;" tabindex="-1"' : '';
    const readOnlyAttr = isCashier ? 'readonly tabindex="-1" style="background-color: #f1f5f9; cursor: not-allowed;"' : '';
    const trashBtn = isCashier ? '' : `
        <button type="button" class="btn btn-sm btn-trash-subtle" onclick="removeRow(${rowCount})">
            <i class="fa-solid fa-trash"></i>
        </button>
    `;

    tr.innerHTML = `
        <td style="vertical-align: middle;">
            <select name="item_type[]" class="form-select form-select-sm" onchange="onTypeChange(${rowCount}, this.value)" ${pointerEventsStyle}>
                <option value="service">Dịch vụ</option>
                <option value="medicine">Thuốc</option>
                <option value="other">Khác</option>
            </select>
            <input type="hidden" name="item_id[]" id="item_id_${rowCount}" value="">
        </td>
        <td style="vertical-align: middle;">
            <select class="form-select form-select-sm" id="select_${rowCount}" onchange="onItemSelect(${rowCount}, this.value)" ${pointerEventsStyle}>
                <option value="">-- Chọn --</option>
            </select>
            <input type="text" name="description[]" class="form-control form-control-sm" id="desc_${rowCount}" placeholder="Mô tả" required style="display:none;" ${readOnlyAttr}>
        </td>
        <td style="vertical-align: middle;"><input type="number" name="quantity[]" class="form-control form-control-sm text-center" value="1" min="1" onchange="recalc()" id="qty_${rowCount}" ${readOnlyAttr}></td>
        <td style="vertical-align: middle;"><input type="number" name="unit_price[]" class="form-control form-control-sm text-end" value="0" min="0" onchange="recalc()" id="price_${rowCount}" ${readOnlyAttr}></td>
        <td id="amount_${rowCount}" style="font-weight:600; text-align: right; vertical-align: middle; color:#1e293b;">0đ</td>
        <td class="text-center" style="vertical-align: middle;">
            ${trashBtn}
        </td>
    `;
    tbody.appendChild(tr);
    onTypeChange(rowCount, 'service');
}

function onTypeChange(idx, type) {
    const sel = document.getElementById('select_' + idx);
    const desc = document.getElementById('desc_' + idx);
    sel.innerHTML = '<option value="">-- Chọn --</option>';
    let items = type === 'service' ? servicesData : (type === 'medicine' ? medicinesData : []);
    items.forEach(item => {
        const name = item.service_name || item.name;
        const price = item.price;
        sel.innerHTML += `<option value="${item.id}" data-price="${price}" data-name="${name}">${name} (${Number(price).toLocaleString('vi-VN')}đ)</option>`;
    });
    if (type === 'other') {
        sel.style.display = 'none';
        desc.style.display = '';
        desc.value = '';
        document.getElementById('item_id_' + idx).value = '';
    } else {
        sel.style.display = '';
        desc.style.display = 'none';
    }
}

function onItemSelect(idx, itemId) {
    const sel = document.getElementById('select_' + idx);
    const opt = sel.options[sel.selectedIndex];
    if (opt && itemId) {
        document.getElementById('item_id_' + idx).value = itemId;
        document.getElementById('desc_' + idx).value = opt.getAttribute('data-name');
        document.getElementById('price_' + idx).value = opt.getAttribute('data-price');
        recalc();
    }
}

function removeRow(idx) {
    const row = document.getElementById('row_' + idx);
    if (row) row.remove();
    recalc();
}

function recalc() {
    let total = 0;
    document.querySelectorAll('#itemsBody tr').forEach(tr => {
        const idx = tr.id.replace('row_', '');
        const qty = parseInt(document.getElementById('qty_' + idx)?.value || 0);
        const price = parseFloat(document.getElementById('price_' + idx)?.value || 0);
        const amount = qty * price;
        total += amount;
        const amountEl = document.getElementById('amount_' + idx);
        if (amountEl) amountEl.textContent = amount.toLocaleString('vi-VN') + 'đ';
    });

    const discount = parseFloat(document.getElementById('discountInput')?.value || 0);
    const preFinal = total - discount;

    // BHYT
    const rate = parseFloat(document.getElementById('insuranceRate')?.value || 0);
    const insuranceCoverage = preFinal * (rate / 100);
    const final_ = preFinal - insuranceCoverage;

    document.getElementById('totalDisplay').textContent = total.toLocaleString('vi-VN') + 'đ';
    document.getElementById('discountDisplay').textContent = '-' + discount.toLocaleString('vi-VN') + 'đ';

    const insRow = document.getElementById('insuranceRow');
    if (rate > 0) {
        insRow.style.display = '';
        document.getElementById('insuranceRateDisplay').textContent = rate + '%';
        document.getElementById('insuranceDisplay').textContent = '-' + insuranceCoverage.toLocaleString('vi-VN') + 'đ';
    } else {
        insRow.style.display = 'none';
    }

    document.getElementById('finalDisplay').textContent = final_.toLocaleString('vi-VN') + 'đ';
}

// Auto recalc when discount changes
document.getElementById('discountInput').addEventListener('input', recalc);

// Patient selection change handler (AJAX load unpaid prescriptions and inpatient bills)
function handlePatientChange(patientId, selectedOption) {
    const container = document.getElementById('prescriptionContainer');
    const select = document.getElementById('prescriptionSelect');
    const inpatientContainer = document.getElementById('inpatientContainer');
    const inpatientCheck = document.getElementById('inpatientCheck');
    const admissionIdInput = document.getElementById('admissionIdInput');
    const inpatientBedDetails = document.getElementById('inpatientBedDetails');
    
    // Auto fill insurance number
    const insuranceNum = selectedOption ? selectedOption.getAttribute('data-insurance') : '';
    const insuranceInput = document.getElementById('insuranceNumber');
    const insuranceRateSelect = document.getElementById('insuranceRate');
    
    if (insuranceNum && insuranceNum.trim() !== '') {
        insuranceInput.value = insuranceNum;
        insuranceRateSelect.value = "80"; // Default to 80% standard rate
    } else {
        insuranceInput.value = '';
        insuranceRateSelect.value = "0";
    }
    
    // Reset and hide
    container.style.display = 'none';
    select.innerHTML = '<option value="">-- Không chọn --</option>';
    inpatientContainer.style.display = 'none';
    inpatientCheck.checked = false;
    admissionIdInput.value = '';
    inpatientBedDetails.innerHTML = '';
    window.currentPendingAdmission = null;
    document.getElementById('itemsBody').innerHTML = '';
    
    if (!patientId) {
        addRow();
        recalc();
        return;
    }
    
    // Load unpaid prescriptions
    fetch(`index.php?page=invoices&action=getUnpaidPrescriptions&patient_id=${patientId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success && data.prescriptions && data.prescriptions.length > 0) {
                data.prescriptions.forEach(p => {
                    const date = new Date(p.created_at).toLocaleDateString('vi-VN');
                    const isSelected = (presetPrescriptionId > 0 && p.id == presetPrescriptionId) ? 'selected' : '';
                    select.innerHTML += `<option value="${p.id}" ${isSelected}>Đơn thuốc #${p.id} - Bác sĩ ${p.doctor_name} (${date})</option>`;
                });
                container.style.display = 'block';
                // If there's a preset prescription ID, trigger the prescriptionSelect change event
                if (presetPrescriptionId > 0) {
                    select.dispatchEvent(new Event('change'));
                }
            } else {
                // If not loading prescription and inpatient isn't loaded/checked yet
                setTimeout(() => {
                    if (document.querySelectorAll('#itemsBody tr').length === 0) {
                        addRow();
                    }
                }, 300);
            }
            recalc();
        })
        .catch(err => {
            console.error('Lỗi khi tải đơn thuốc:', err);
            recalc();
        });

    // Load pending inpatient discharge bill details
    fetch(`index.php?page=invoices&action=getPendingAdmissionBill&patient_id=${patientId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success && data.admission) {
                inpatientContainer.style.display = 'block';
                inpatientBedDetails.innerHTML = `
                    Giường: <strong>${data.admission.bed_number}</strong> (Phòng ${data.admission.room_number})<br>
                    Số ngày lưu trú: <strong>${data.admission.days} ngày</strong> (${new Date(data.admission.admission_date).toLocaleDateString('vi-VN')} - ${data.admission.discharge_ordered_at ? new Date(data.admission.discharge_ordered_at).toLocaleDateString('vi-VN') : 'Hiện tại'})<br>
                    Đơn giá phòng: <strong>${Number(data.admission.price_per_day).toLocaleString('vi-VN')}đ / ngày</strong><br>
                    Tổng chi phí tạm tính: <strong class="text-danger">${Number(data.admission.room_cost).toLocaleString('vi-VN')}đ</strong>
                `;
                window.currentPendingAdmission = data.admission;

                // Auto-check if presetAdmissionId matches
                if (presetAdmissionId > 0 && data.admission.id == presetAdmissionId) {
                    inpatientCheck.checked = true;
                    inpatientCheck.dispatchEvent(new Event('change'));
                }
            }
        })
        .catch(err => {
            console.error('Lỗi khi kiểm tra hồ sơ nội trú:', err);
        });
}

// Map the change event to handle patient selection changes
if (typeof jQuery !== 'undefined') {
    $('#patientSelect').on('change', function() {
        handlePatientChange(this.value, this.options[this.selectedIndex]);
    });
} else {
    document.getElementById('patientSelect').addEventListener('change', function() {
        handlePatientChange(this.value, this.options[this.selectedIndex]);
    });
}

// Inpatient Checkbox change event handler
document.getElementById('inpatientCheck').addEventListener('change', function() {
    const admissionIdInput = document.getElementById('admissionIdInput');
    
    // Clear existing inpatient row if any
    document.querySelectorAll('.inpatient-row').forEach(row => row.remove());
    
    if (this.checked) {
        if (window.currentPendingAdmission) {
            admissionIdInput.value = window.currentPendingAdmission.id;
            addInpatientItemRow(window.currentPendingAdmission);
            
            // If there's a single empty default row, remove it
            const rows = document.querySelectorAll('#itemsBody tr');
            if (rows.length === 2 && rows[0].id && !document.getElementById('item_id_' + rows[0].id.replace('row_', '')).value && !rows[0].classList.contains('inpatient-row')) {
                rows[0].remove();
            }
        }
    } else {
        admissionIdInput.value = '';
        recalc();
        // If items body is empty, add a default row
        if (document.querySelectorAll('#itemsBody tr').length === 0) {
            addRow();
        }
    }
});

function addInpatientItemRow(admission) {
    rowCount++;
    const tbody = document.getElementById('itemsBody');
    const tr = document.createElement('tr');
    tr.id = 'row_' + rowCount;
    tr.className = 'inpatient-row';
    
    const pointerEventsStyle = 'style="pointer-events: none; background-color: #f1f5f9; cursor: not-allowed;" tabindex="-1"';
    const readOnlyAttr = 'readonly tabindex="-1" style="background-color: #f1f5f9; cursor: not-allowed;"';

    tr.innerHTML = `
        <td style="vertical-align: middle;">
            <select name="item_type[]" class="form-select form-select-sm" ${pointerEventsStyle}>
                <option value="room" selected>Giường/Phòng</option>
            </select>
            <input type="hidden" name="item_id[]" id="item_id_${rowCount}" value="${admission.room_id}">
        </td>
        <td style="vertical-align: middle;">
            <input type="text" name="description[]" class="form-control form-control-sm" id="desc_${rowCount}" value="Tiền giường ${admission.bed_number} (Phòng ${admission.room_number} - ${admission.days} ngày)" required ${readOnlyAttr}>
        </td>
        <td style="vertical-align: middle;"><input type="number" name="quantity[]" class="form-control form-control-sm text-center" value="${admission.days}" min="1" onchange="recalc()" id="qty_${rowCount}" ${readOnlyAttr}></td>
        <td style="vertical-align: middle;"><input type="number" name="unit_price[]" class="form-control form-control-sm text-end" value="${admission.price_per_day}" min="0" onchange="recalc()" id="price_${rowCount}" ${readOnlyAttr}></td>
        <td id="amount_${rowCount}" style="font-weight:600; text-align: right; vertical-align: middle; color:#1e293b;">0đ</td>
        <td class="text-center" style="vertical-align: middle;">
            <small class="text-success fw-bold"><i class="fa-solid fa-lock"></i></small>
        </td>
    `;
    tbody.appendChild(tr);
    recalc();
}

// Prescription selection change handler
document.getElementById('prescriptionSelect').addEventListener('change', function() {
    const prescriptionId = this.value;
    const tbody = document.getElementById('itemsBody');
    
    // Clear all existing non-inpatient rows
    document.querySelectorAll('#itemsBody tr:not(.inpatient-row)').forEach(row => row.remove());
    
    if (!prescriptionId) {
        if (document.querySelectorAll('#itemsBody tr').length === 0) {
            addRow();
        }
        recalc();
        return;
    }
    
    fetch(`index.php?page=invoices&action=getPrescriptionDetails&id=${prescriptionId}`)
        .then(res => res.json())
        .then(data => {
            if (data.success && data.items && data.items.length > 0) {
                data.items.forEach(item => {
                    addPrescriptionItemRow(item);
                });
                recalc();
            } else {
                if (document.querySelectorAll('#itemsBody tr').length === 0) {
                    addRow();
                }
            }
        })
        .catch(err => {
            console.error('Lỗi tải chi tiết đơn thuốc:', err);
            if (document.querySelectorAll('#itemsBody tr').length === 0) {
                addRow();
            }
        });
});

function addPrescriptionItemRow(item) {
    rowCount++;
    const tbody = document.getElementById('itemsBody');
    const tr = document.createElement('tr');
    tr.id = 'row_' + rowCount;
    
    // Phân quyền cho Thu ngân (Cashier)
    const isCashier = (userRole === 'cashier');
    const pointerEventsStyle = isCashier ? 'style="pointer-events: none; background-color: #f1f5f9; cursor: not-allowed;" tabindex="-1"' : '';
    const readOnlyAttr = isCashier ? 'readonly tabindex="-1" style="background-color: #f1f5f9; cursor: not-allowed;"' : '';
    const trashBtn = isCashier ? '' : `
        <button type="button" class="btn btn-sm btn-trash-subtle" onclick="removeRow(${rowCount})">
            <i class="fa-solid fa-trash"></i>
        </button>
    `;

    tr.innerHTML = `
        <td style="vertical-align: middle;">
            <select name="item_type[]" class="form-select form-select-sm" onchange="onTypeChange(${rowCount}, this.value)" ${pointerEventsStyle}>
                <option value="service">Dịch vụ</option>
                <option value="medicine" selected>Thuốc</option>
                <option value="other">Khác</option>
            </select>
            <input type="hidden" name="item_id[]" id="item_id_${rowCount}" value="${item.medicine_id}">
        </td>
        <td style="vertical-align: middle;">
            <select class="form-select form-select-sm" id="select_${rowCount}" onchange="onItemSelect(${rowCount}, this.value)" ${pointerEventsStyle}>
                <option value="">-- Chọn --</option>
            </select>
            <input type="text" name="description[]" class="form-control form-control-sm" id="desc_${rowCount}" value="${item.medicine_name}" placeholder="Mô tả" required style="display:none;" ${readOnlyAttr}>
        </td>
        <td style="vertical-align: middle;"><input type="number" name="quantity[]" class="form-control form-control-sm text-center" value="${item.quantity}" min="1" onchange="recalc()" id="qty_${rowCount}" ${readOnlyAttr}></td>
        <td style="vertical-align: middle;"><input type="number" name="unit_price[]" class="form-control form-control-sm text-end" value="${item.price}" min="0" onchange="recalc()" id="price_${rowCount}" ${readOnlyAttr}></td>
        <td id="amount_${rowCount}" style="font-weight:600; text-align: right; vertical-align: middle; color:#1e293b;">0đ</td>
        <td class="text-center" style="vertical-align: middle;">
            ${trashBtn}
        </td>
    `;
    tbody.appendChild(tr);
    
    // Set selection
    onTypeChange(rowCount, 'medicine');
    document.getElementById('select_' + rowCount).value = item.medicine_id;
}

// Auto init page if preset variables exist or add default row
document.addEventListener('DOMContentLoaded', function() {
    // Init Select2 for patient search
    if (typeof jQuery !== 'undefined') {
        $('#patientSelect').select2({
            width: '100%',
            placeholder: '-- Tìm và chọn bệnh nhân --'
        });
    }

    if (presetPatientId > 0) {
        if (typeof jQuery !== 'undefined') {
            $('#patientSelect').val(presetPatientId).trigger('change');
        } else {
            const patientSelect = document.getElementById('patientSelect');
            patientSelect.value = presetPatientId;
            patientSelect.dispatchEvent(new Event('change'));
        }
    } else {
        addRow();
    }
});
</script>
