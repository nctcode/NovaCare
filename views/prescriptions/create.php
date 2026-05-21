<!-- Form Tạo Đơn thuốc -->
<div class="form-section" style="max-width: 900px;">
    <h5 class="mb-4"><i class="bi bi-file-earmark-medical-fill me-2"></i>Tạo Đơn thuốc mới</h5>

    <form method="POST" action="index.php?page=prescriptions&action=store" id="createPrescriptionForm">
                            <?php echo Security::csrfField(); ?>
        <div class="mb-3">
            <label for="medical_record_id" class="form-label">Hồ sơ bệnh án <span class="text-danger">*</span></label>
            <select class="form-select" id="medical_record_id" name="medical_record_id" required>
                <option value="">-- Chọn hồ sơ bệnh án --</option>
                <?php foreach ($medicalRecords as $mr): ?>
                    <option value="<?= $mr['id'] ?>">
                        <?= htmlspecialchars($mr['patient_name']) ?> - <?= htmlspecialchars($mr['diagnosis']) ?>
                        (<?= date('d/m/Y', strtotime($mr['created_at'])) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <hr>
        <h6 class="mb-3"><i class="bi bi-capsule me-2"></i>Danh sách thuốc kê</h6>

        <div id="medicineItems">
            <div class="medicine-item border rounded p-3 mb-3">
                <div class="row">
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Thuốc</label>
                        <select class="form-select" name="medicine_id[]" required>
                            <option value="">-- Chọn --</option>
                            <?php foreach ($medicines as $m): ?>
                                <option value="<?= $m['id'] ?>"><?= htmlspecialchars($m['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="form-label">Liều lượng</label>
                        <input type="text" class="form-control" name="dosage[]" placeholder="VD: 500mg">
                    </div>
                    <div class="col-md-2 mb-2">
                        <label class="form-label">Thời gian</label>
                        <input type="text" class="form-control" name="duration[]" placeholder="VD: 5 ngày">
                    </div>
                    <div class="col-md-4 mb-2">
                        <label class="form-label">Hướng dẫn</label>
                        <input type="text" class="form-control" name="instructions[]" placeholder="VD: Uống sau khi ăn">
                    </div>
                </div>
            </div>
        </div>

        <button type="button" class="btn btn-outline-primary mb-4" onclick="addMedicineItem()">
            <i class="bi bi-plus-circle"></i> Thêm thuốc
        </button>

        <div class="d-flex gap-3">
            <button type="submit" class="btn-submit"><i class="bi bi-check-lg"></i> Tạo đơn thuốc</button>
            <a href="index.php?page=prescriptions" class="btn-cancel"><i class="bi bi-x-lg"></i> Hủy</a>
        </div>
    </form>
</div>

<script>
function addMedicineItem() {
    const container = document.getElementById('medicineItems');
    const item = container.querySelector('.medicine-item').cloneNode(true);
    // Clear values
    item.querySelectorAll('input').forEach(input => input.value = '');
    item.querySelectorAll('select').forEach(select => select.selectedIndex = 0);
    container.appendChild(item);
}
</script>

