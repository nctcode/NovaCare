<!-- Create Invoice -->
<div class="content-card" data-aos="fade-up" style="border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.03);">
    <div class="card-header bg-white border-bottom-0 pt-4 pb-0">
        <h5 class="m-0" style="font-weight:700;"><i class="fa-solid fa-file-circle-plus me-2 text-primary"></i>Tạo hóa đơn mới</h5>
        <p class="text-muted" style="font-size:13px; margin:5px 0 0 0;">Chọn bệnh nhân và thêm các dịch vụ/thuốc vào hóa đơn</p>
    </div>
    <div class="card-body">
        <form method="POST" action="index.php?page=invoices&action=store" id="invoiceForm">
            <!-- Thông tin chung -->
            <div class="row g-3 mb-4">
                <div class="col-md-4">
                    <label class="form-label fw-bold">Bệnh nhân <span class="text-danger">*</span></label>
                    <select name="patient_id" class="form-select" required>
                        <option value="">-- Chọn bệnh nhân --</option>
                        <?php foreach ($patients as $p): ?>
                            <option value="<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?> (<?= $p['phone'] ?>)</option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Phương thức thanh toán</label>
                    <select name="payment_method" class="form-select">
                        <option value="cash">💵 Tiền mặt</option>
                        <option value="card">💳 Thẻ ngân hàng</option>
                        <option value="momo">📱 MoMo</option>
                        <option value="vnpay">🏦 VNPay</option>
                        <option value="transfer">🔄 Chuyển khoản</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-bold">Giảm giá (VNĐ)</label>
                    <input type="number" name="discount" class="form-control" value="0" min="0" id="discountInput">
                </div>
            </div>

            <input type="hidden" name="appointment_id" value="">
            <input type="hidden" name="admission_id" value="">

            <!-- Items Table -->
            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <h6 class="fw-bold m-0"><i class="fa-solid fa-list me-1"></i> Chi tiết hóa đơn</h6>
                    <button type="button" class="btn btn-sm btn-outline-primary" style="border-radius:20px;" onclick="addRow()">
                        <i class="fa-solid fa-plus me-1"></i>Thêm dòng
                    </button>
                </div>
                <div class="table-wrapper">
                    <table class="data-table" id="itemsTable">
                        <thead>
                            <tr>
                                <th style="width:140px;">Loại</th>
                                <th>Mô tả</th>
                                <th style="width:80px;">SL</th>
                                <th style="width:140px;">Đơn giá</th>
                                <th style="width:140px;">Thành tiền</th>
                                <th style="width:50px;"></th>
                            </tr>
                        </thead>
                        <tbody id="itemsBody">
                            <!-- Dynamic rows -->
                        </tbody>
                        <tfoot>
                            <tr>
                                <td colspan="4" class="text-end fw-bold">Tổng cộng:</td>
                                <td class="fw-bold" id="totalDisplay">0đ</td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="4" class="text-end fw-bold text-danger">Giảm giá:</td>
                                <td class="fw-bold text-danger" id="discountDisplay">-0đ</td>
                                <td></td>
                            </tr>
                            <tr style="background:var(--primary-light);">
                                <td colspan="4" class="text-end fw-bold" style="font-size:16px;">THÀNH TIỀN:</td>
                                <td class="fw-bold text-primary" style="font-size:16px;" id="finalDisplay">0đ</td>
                                <td></td>
                            </tr>
                        </tfoot>
                    </table>
                </div>
            </div>

            <!-- Ghi chú -->
            <div class="mb-4">
                <label class="form-label fw-bold">Ghi chú</label>
                <textarea name="notes" class="form-control" rows="2" placeholder="Ghi chú thêm (nếu có)..."></textarea>
            </div>

            <div class="d-flex gap-2">
                <button type="submit" class="btn btn-primary" style="border-radius:20px; font-weight:500; padding:10px 30px;">
                    <i class="fa-solid fa-save me-2"></i>Lưu hóa đơn
                </button>
                <a href="index.php?page=invoices" class="btn btn-outline-secondary" style="border-radius:20px; font-weight:500; padding:10px 30px;">
                    <i class="fa-solid fa-arrow-left me-2"></i>Quay lại
                </a>
            </div>
        </form>
    </div>
</div>

<script>
// Services & Medicines data from PHP
const servicesData = <?= json_encode($services) ?>;
const medicinesData = <?= json_encode($medicines) ?>;

let rowCount = 0;

function addRow() {
    rowCount++;
    const tbody = document.getElementById('itemsBody');
    const tr = document.createElement('tr');
    tr.id = 'row_' + rowCount;
    tr.innerHTML = `
        <td>
            <select name="item_type[]" class="form-select form-select-sm" onchange="onTypeChange(${rowCount}, this.value)">
                <option value="service">Dịch vụ</option>
                <option value="medicine">Thuốc</option>
                <option value="other">Khác</option>
            </select>
            <input type="hidden" name="item_id[]" id="item_id_${rowCount}" value="">
        </td>
        <td>
            <select class="form-select form-select-sm" id="select_${rowCount}" onchange="onItemSelect(${rowCount}, this.value)">
                <option value="">-- Chọn --</option>
            </select>
            <input type="text" name="description[]" class="form-control form-control-sm mt-1" id="desc_${rowCount}" placeholder="Mô tả" required>
        </td>
        <td><input type="number" name="quantity[]" class="form-control form-control-sm" value="1" min="1" onchange="recalc()" id="qty_${rowCount}"></td>
        <td><input type="number" name="unit_price[]" class="form-control form-control-sm" value="0" min="0" onchange="recalc()" id="price_${rowCount}"></td>
        <td id="amount_${rowCount}" style="font-weight:600;">0đ</td>
        <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="removeRow(${rowCount})"><i class="fa-solid fa-trash"></i></button></td>
    `;
    tbody.appendChild(tr);
    onTypeChange(rowCount, 'service');
}

function onTypeChange(idx, type) {
    const sel = document.getElementById('select_' + idx);
    sel.innerHTML = '<option value="">-- Chọn --</option>';
    let items = type === 'service' ? servicesData : (type === 'medicine' ? medicinesData : []);
    items.forEach(item => {
        const name = item.service_name || item.name;
        const price = item.price;
        sel.innerHTML += `<option value="${item.id}" data-price="${price}" data-name="${name}">${name} (${Number(price).toLocaleString('vi-VN')}đ)</option>`;
    });
    if (type === 'other') {
        sel.style.display = 'none';
    } else {
        sel.style.display = '';
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
    const final_ = total - discount;

    document.getElementById('totalDisplay').textContent = total.toLocaleString('vi-VN') + 'đ';
    document.getElementById('discountDisplay').textContent = '-' + discount.toLocaleString('vi-VN') + 'đ';
    document.getElementById('finalDisplay').textContent = final_.toLocaleString('vi-VN') + 'đ';
}

// Auto recalc when discount changes
document.getElementById('discountInput').addEventListener('input', recalc);

// Add first row by default
addRow();
</script>
