<!-- Create Invoice -->
<div class="content-card" data-aos="fade-up" style="border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.03);">
    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
        <h5 class="m-0" style="font-weight:700;"><i class="fa-solid fa-file-circle-plus me-2 text-primary"></i>Tạo hóa đơn mới</h5>
        <p class="text-muted" style="font-size:13px; margin:5px 0 0 0;">Chọn bệnh nhân và thêm các dịch vụ/thuốc vào hóa đơn</p>
    </div>
    <div class="card-body">
        <form method="POST" action="index.php?page=invoices&action=store" id="invoiceForm">
            <?php echo Security::csrfField(); ?>
            
            <!-- 1. Thông tin chung -->
            <div class="row g-3 mb-4">
                <div class="col-md-3">
                    <label class="form-label fw-bold">Bệnh nhân <span class="text-danger">*</span></label>
                    <select name="patient_id" id="patientSelect" class="form-select" required>
                        <option value="">-- Chọn bệnh nhân --</option>
                        <?php foreach ($patients as $p): ?>
                            <option value="<?= $p['id'] ?>" data-insurance="<?= htmlspecialchars($p['insurance_number'] ?? '') ?>"><?= htmlspecialchars($p['name']) ?> (<?= htmlspecialchars($p['phone'] ?? '') ?>)</option>
                        <?php endforeach; ?>
                    </select>
                    <!-- Đơn thuốc tự động xếp gọn bên dưới select bệnh nhân khi xuất hiện -->
                    <div id="prescriptionContainer" class="mt-2" style="display: none;">
                        <label class="form-label fw-bold text-primary" style="font-size:13px; margin-bottom: 4px;">Đơn thuốc chưa thanh toán</label>
                        <select name="prescription_id" id="prescriptionSelect" class="form-select">
                            <option value="">-- Không chọn --</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">Mã số BHYT</label>
                    <input type="text" name="insurance_number" id="insuranceNumber" class="form-control" placeholder="Tự động điền hoặc nhập tay">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Tỷ lệ BHYT (%)</label>
                    <select name="insurance_rate" id="insuranceRate" class="form-select" onchange="recalc()">
                        <option value="0">0% (Không BHYT)</option>
                        <option value="40">40% (Trái tuyến)</option>
                        <option value="60">60% (Trái tuyến tỉnh)</option>
                        <option value="80">80% (Đúng tuyến)</option>
                        <option value="100">100% (Ưu đãi/Hộ nghèo)</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Giảm giá (VNĐ)</label>
                    <input type="number" name="discount" class="form-control" value="0" min="0" id="discountInput" onchange="recalc()">
                </div>
                <div class="col-md-2">
                    <label class="form-label fw-bold">Phương thức thanh toán</label>
                    <select name="payment_method" class="form-select">
                        <option value="cash">💵 Tiền mặt</option>
                        <option value="card">💳 Thẻ ngân hàng</option>
                        <option value="momo">📱 MoMo</option>
                        <option value="vnpay">🏦 VNPay</option>
                        <option value="transfer">🔄 Chuyển khoản</option>
                    </select>
                </div>
            </div>

            <input type="hidden" name="appointment_id" value="">
            <input type="hidden" name="admission_id" value="">

            <!-- 2. Chi tiết hóa đơn (Bảng dữ liệu) -->
            <div class="mb-3">
                <h6 class="fw-bold mb-3"><i class="fa-solid fa-list me-1"></i> Chi tiết hóa đơn</h6>
                <div class="table-wrapper">
                    <table class="data-table" id="itemsTable">
                        <thead>
                            <tr>
                                <th style="width: 15%;">Loại</th>
                                <th style="width: 38%;">Mô tả</th>
                                <th style="width: 10%; text-align: center;">SL</th>
                                <th style="width: 15%; text-align: right;">Đơn giá</th>
                                <th style="width: 16%; text-align: right;">Thành tiền</th>
                                <th style="width: 6%; text-align: center;"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody">
                            <!-- Dynamic rows loaded here -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-end fw-bold" style="padding: 12px 16px; border-top: 1px solid #dee2e6;">Tổng cộng:</td>
                                <td class="fw-bold text-end" style="padding: 12px 16px; border-top: 1px solid #dee2e6;" id="totalDisplay">0đ</td>
                                <td style="border-top: 1px solid #dee2e6;"></td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end fw-bold text-danger" style="padding: 12px 16px;">Giảm giá khác:</td>
                                <td class="fw-bold text-danger text-end" style="padding: 12px 16px;" id="discountDisplay">-0đ</td>
                                <td></td>
                            </tr>
                            <tr id="insuranceRow" style="display: none;">
                                <td colspan="4" class="text-end fw-bold text-success" style="padding: 12px 16px;">BHYT chi trả (<span id="insuranceRateDisplay">0%</span>):</td>
                                <td class="fw-bold text-success text-end" style="padding: 12px 16px;" id="insuranceDisplay">-0đ</td>
                                <td></td>
                            </tr>
                            <tr class="final-row">
                                <td colspan="4" class="text-end fw-bold">BỆNH NHÂN CẦN TRẢ:</td>
                                <td class="fw-bold text-end" id="finalDisplay" style="font-size: 1.15rem; color: #1e3c72;">0đ</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
                
                <!-- Nút "+ Thêm dòng" chuyển xuống dưới bảng bên trái -->
                <div class="mt-2 text-start">
                    <button type="button" class="btn btn-sm btn-outline-primary" style="border-radius:20px; font-weight:500; padding: 6px 18px;" onclick="addRow()">
                        <i class="fa-solid fa-plus me-1"></i>Thêm dòng
                    </button>
                </div>
            </div>

            <!-- 3. Ghi chú -->
            <div class="mb-4">
                <label class="form-label fw-bold">Ghi chú</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="Ghi chú thêm (nếu có)..."></textarea>
            </div>

            <!-- 4. Nút hành động (Xếp góc dưới bên phải và phân cấp rõ ràng) -->
            <div class="d-flex justify-content-end gap-2 mt-4">
                <a href="index.php?page=invoices" class="btn btn-outline-secondary" style="border-radius:20px; font-weight:500; padding:10px 30px;">
                    <i class="fa-solid fa-arrow-left me-2"></i>Quay lại
                </a>
                <button type="submit" class="btn btn-primary" style="border-radius:20px; font-weight:500; padding:10px 30px;">
                    <i class="fa-solid fa-save me-2"></i>Lưu hóa đơn
                </button>
            </div>
        </form>
    </div>
</div>

<script>
// Services & Medicines data from PHP
const servicesData = <?= json_encode($services) ?>;
const medicinesData = <?= json_encode($medicines) ?>;

// Preset values from URL scanning
const presetPrescriptionId = <?= json_encode($presetPrescriptionId ?? 0) ?>;
const presetPatientId = <?= json_encode($presetPatientId ?? 0) ?>;

let rowCount = 0;

function addRow() {
    rowCount++;
    const tbody = document.getElementById('itemsBody');
    const tr = document.createElement('tr');
    tr.id = 'row_' + rowCount;
    tr.innerHTML = `
        <td style="vertical-align: middle;">
            <select name="item_type[]" class="form-select form-select-sm" onchange="onTypeChange(${rowCount}, this.value)">
                <option value="service">Dịch vụ</option>
                <option value="medicine">Thuốc</option>
                <option value="other">Khác</option>
            </select>
            <input type="hidden" name="item_id[]" id="item_id_${rowCount}" value="">
        </td>
        <td style="vertical-align: middle;">
            <select class="form-select form-select-sm" id="select_${rowCount}" onchange="onItemSelect(${rowCount}, this.value)">
                <option value="">-- Chọn --</option>
            </select>
            <input type="text" name="description[]" class="form-control form-control-sm" id="desc_${rowCount}" placeholder="Mô tả" required style="display:none;">
        </td>
        <td style="vertical-align: middle;"><input type="number" name="quantity[]" class="form-control form-control-sm text-center" value="1" min="1" onchange="recalc()" id="qty_${rowCount}"></td>
        <td style="vertical-align: middle;"><input type="number" name="unit_price[]" class="form-control form-control-sm text-end" value="0" min="0" onchange="recalc()" id="price_${rowCount}"></td>
        <td id="amount_${rowCount}" style="font-weight:600; text-align: right; vertical-align: middle;">0đ</td>
        <td class="text-center" style="vertical-align: middle;">
            <button type="button" class="btn btn-sm btn-trash-subtle" onclick="removeRow(${rowCount})">
                <i class="fa-solid fa-trash"></i>
            </button>
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

// Patient selection change handler (AJAX load unpaid prescriptions)
document.getElementById('patientSelect').addEventListener('change', function() {
    const patientId = this.value;
    const container = document.getElementById('prescriptionContainer');
    const select = document.getElementById('prescriptionSelect');
    
    // Auto fill insurance number
    const selectedOption = this.options[this.selectedIndex];
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
    document.getElementById('itemsBody').innerHTML = '';
    
    if (!patientId) {
        addRow();
        recalc();
        return;
    }
    
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
                addRow();
            }
            recalc();
        })
        .catch(err => {
            console.error('Lỗi khi tải đơn thuốc:', err);
            addRow();
            recalc();
        });
});

// Prescription selection change handler
document.getElementById('prescriptionSelect').addEventListener('change', function() {
    const prescriptionId = this.value;
    const tbody = document.getElementById('itemsBody');
    
    // Clear all existing rows
    tbody.innerHTML = '';
    
    if (!prescriptionId) {
        addRow();
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
                addRow();
            }
        })
        .catch(err => {
            console.error('Lỗi tải chi tiết đơn thuốc:', err);
            addRow();
        });
});

function addPrescriptionItemRow(item) {
    rowCount++;
    const tbody = document.getElementById('itemsBody');
    const tr = document.createElement('tr');
    tr.id = 'row_' + rowCount;
    tr.innerHTML = `
        <td style="vertical-align: middle;">
            <select name="item_type[]" class="form-select form-select-sm" onchange="onTypeChange(${rowCount}, this.value)">
                <option value="service">Dịch vụ</option>
                <option value="medicine" selected>Thuốc</option>
                <option value="other">Khác</option>
            </select>
            <input type="hidden" name="item_id[]" id="item_id_${rowCount}" value="${item.medicine_id}">
        </td>
        <td style="vertical-align: middle;">
            <select class="form-select form-select-sm" id="select_${rowCount}" onchange="onItemSelect(${rowCount}, this.value)">
                <option value="">-- Chọn --</option>
            </select>
            <input type="text" name="description[]" class="form-control form-control-sm" id="desc_${rowCount}" value="${item.medicine_name}" placeholder="Mô tả" required style="display:none;">
        </td>
        <td style="vertical-align: middle;"><input type="number" name="quantity[]" class="form-control form-control-sm text-center" value="${item.quantity}" min="1" onchange="recalc()" id="qty_${rowCount}"></td>
        <td style="vertical-align: middle;"><input type="number" name="unit_price[]" class="form-control form-control-sm text-end" value="${item.price}" min="0" onchange="recalc()" id="price_${rowCount}"></td>
        <td id="amount_${rowCount}" style="font-weight:600; text-align: right; vertical-align: middle;">0đ</td>
        <td class="text-center" style="vertical-align: middle;">
            <button type="button" class="btn btn-sm btn-trash-subtle" onclick="removeRow(${rowCount})">
                <i class="fa-solid fa-trash"></i>
            </button>
        </td>
    `;
    tbody.appendChild(tr);
    
    // Set selection
    onTypeChange(rowCount, 'medicine');
    document.getElementById('select_' + rowCount).value = item.medicine_id;
}

// Auto init page if preset variables exist
document.addEventListener('DOMContentLoaded', function() {
    if (presetPatientId > 0) {
        const patientSelect = document.getElementById('patientSelect');
        patientSelect.value = presetPatientId;
        patientSelect.dispatchEvent(new Event('change'));
    } else {
        addRow();
    }
});
</script>
