<!-- Form Cấp Số Thứ Tự (Check-in) -->
<div class="container-fluid py-4">
    <div class="row justify-content-center">
        <div class="col-lg-10 col-xl-8">
            <div class="card shadow-lg border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-gradient-primary text-white p-4">
                    <div class="d-flex align-items-center">
                        <div class="bg-white text-primary rounded-circle d-flex align-items-center justify-content-center me-3 shadow-sm" style="width: 48px; height: 48px;">
                            <i class="fas fa-ticket fs-4"></i>
                        </div>
                        <div>
                            <h4 class="mb-0 text-white fw-bold">Cấp Số Thứ Tự Khám Bệnh</h4>
                            <p class="mb-0 text-white-50 text-sm">Tiếp nhận bệnh nhân tại quầy lễ tân</p>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4 p-md-5 bg-light">
                    <form method="POST" action="index.php?page=queue&action=storeCheckin" id="checkinForm" class="needs-validation" novalidate>
                        <?= Security::csrfField() ?>
                        <?php if (isset($selectedAppointment)): ?>
                            <input type="hidden" name="appointment_id" value="<?= htmlspecialchars($selectedAppointment['id']) ?>">
                            <input type="hidden" name="doctor_id" value="<?= htmlspecialchars($selectedAppointment['doctor_id']) ?>">
                        <?php endif; ?>

                        <!-- Box 1: Chọn bệnh nhân -->
                        <div class="bg-white p-4 rounded-3 shadow-sm mb-4 border">
                            <h6 class="text-uppercase text-secondary text-xs font-weight-bolder mb-3"><i class="fas fa-user-injured me-2"></i>1. Chọn Bệnh Nhân</h6>
                            
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <div class="d-flex justify-content-between align-items-center mb-1">
                                        <label for="patient_id" class="form-label text-sm fw-bold text-dark mb-0">Bệnh nhân <span class="text-danger">*</span></label>
                                        <?php if (in_array($_SESSION['user']['role'], ['receptionist'])): ?>
                                        <a href="index.php?page=patients&action=create" class="text-xs text-decoration-none fw-semibold" target="_blank">
                                            <i class="fa-solid fa-user-plus me-1"></i>Thêm bệnh nhân mới
                                        </a>
                                        <?php endif; ?>
                                    </div>
                                    <select class="form-select border border-primary bg-white" id="patient_id" name="patient_id" required>
                                        <option value="">-- Chọn bệnh nhân --</option>
                                        <?php foreach ($patients as $p): ?>
                                            <option value="<?= $p['id'] ?>" data-insurance="<?= htmlspecialchars($p['insurance_number'] ?? '') ?>" <?= (isset($selectedAppointment) && $selectedAppointment['patient_id'] == $p['id']) ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($p['name']) ?> (SĐT: <?= htmlspecialchars($p['phone'] ?? 'N/A') ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback text-xs">Vui lòng chọn một bệnh nhân.</div>
                                </div>

                                <div class="col-md-12 mt-3">
                                    <div class="form-check form-switch p-0" style="margin-left: 20px;">
                                        <input class="form-check-input" type="checkbox" id="insurance_verified" name="insurance_verified" value="1">
                                        <label class="form-check-label text-sm fw-bold text-dark" for="insurance_verified">
                                            Đã kiểm tra thẻ BHYT hợp lệ
                                        </label>
                                        <div id="insurance_info" class="text-muted text-xs mt-1" style="display:none;">
                                            Số thẻ BHYT của bệnh nhân: <span class="fw-bold text-primary" id="insurance_number_display"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Box 2: Chỉ định Khoa & Phòng khám -->
                        <div class="bg-white p-4 rounded-3 shadow-sm mb-4 border">
                            <h6 class="text-uppercase text-secondary text-xs font-weight-bolder mb-3"><i class="fas fa-stethoscope me-2"></i>2. Phân Phòng Khám</h6>
                            
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label for="department_id" class="form-label text-sm fw-bold text-dark">Khoa khám</label>
                                    <select class="form-select border border-primary bg-white" id="department_id" name="department_id">
                                        <option value="">-- Đa khoa / Chọn khoa --</option>
                                        <?php foreach ($departments as $dept): ?>
                                            <option value="<?= $dept['id'] ?>" <?= (isset($selectedAppointment) && $selectedAppointment['department_id'] == $dept['id']) ? 'selected' : '' ?>><?= htmlspecialchars($dept['name']) ?></option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-6">
                                    <label for="examination_room_id" class="form-label text-sm fw-bold text-dark">Phòng khám</label>
                                    <select class="form-select border border-primary bg-white" id="examination_room_id" name="examination_room_id">
                                        <option value="">-- Tự động / Chọn phòng khám --</option>
                                        <?php foreach ($rooms as $room): ?>
                                            <option value="<?= $room['id'] ?>" data-dept="<?= $room['department_id'] ?>">
                                                <?= htmlspecialchars($room['room_name']) ?> (Khoa: <?= htmlspecialchars($room['department_name'] ?? 'Chung') ?>)
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col-md-12 mt-3">
                                    <label for="priority" class="form-label text-sm fw-bold text-dark">Độ ưu tiên</label>
                                    <div class="d-flex gap-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="priority" id="priority_normal" value="normal" checked>
                                            <label class="form-check-label" for="priority_normal">Khám thường</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="priority" id="priority_high" value="priority">
                                            <label class="form-check-label text-warning fw-bold" for="priority_high">Ưu tiên (Người già, Trẻ em, Bầu)</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="priority" id="priority_emergency" value="emergency">
                                            <label class="form-check-label text-danger fw-bold animate__animated animate__flash animate__infinite" for="priority_emergency">CẤP CỨU</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Box 3: Ghi chú & Tùy chọn khác -->
                        <div class="bg-white p-4 rounded-3 shadow-sm mb-4 border">
                            <h6 class="text-uppercase text-secondary text-xs font-weight-bolder mb-3"><i class="fas fa-edit me-2"></i>3. Ghi chú thêm</h6>
                            <div class="row g-3">
                                <div class="col-md-12">
                                    <textarea class="form-control border p-3 bg-white" id="notes" name="notes" rows="3" placeholder="Ghi chú triệu chứng ban đầu, đo huyết áp nhịp tim, lý do đặc biệt..."></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="d-flex align-items-center justify-content-between pt-2">
                            <a href="index.php?page=queue" class="btn btn-outline-secondary btn-lg mb-0 px-4 rounded-pill shadow-sm">
                                <i class="fas fa-arrow-left me-2"></i> Quay lại
                            </a>
                            <button type="submit" class="btn btn-primary btn-lg mb-0 px-5 rounded-pill shadow-sm">
                                <i class="fas fa-ticket me-2"></i> Cấp số & In phiếu
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);
    }
    .form-select, .form-control {
        transition: all 0.3s ease;
    }
    .form-select:focus, .form-control:focus {
        border-color: #2a5298;
        box-shadow: 0 0 0 0.25rem rgba(42, 82, 152, 0.25);
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Bootstrap validation
    const form = document.getElementById('checkinForm');
    form.addEventListener('submit', function(event) {
        if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
        }
        form.classList.add('was-validated');
    }, false);

    // Patient card / insurance switch auto-trigger
    const patientSelect = document.getElementById('patient_id');
    const insuranceCheckbox = document.getElementById('insurance_verified');
    const insuranceInfo = document.getElementById('insurance_info');
    const insuranceDisplay = document.getElementById('insurance_number_display');

    patientSelect.addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const insuranceNum = selectedOption.getAttribute('data-insurance');

        if (insuranceNum && insuranceNum.trim() !== '') {
            insuranceCheckbox.checked = true;
            insuranceDisplay.textContent = insuranceNum;
            insuranceInfo.style.display = 'block';
        } else {
            insuranceCheckbox.checked = false;
            insuranceDisplay.textContent = '';
            insuranceInfo.style.display = 'none';
        }
    });

    // Dynamically filter rooms based on department
    const deptSelect = document.getElementById('department_id');
    const roomSelect = document.getElementById('examination_room_id');
    const originalRoomOptions = Array.from(roomSelect.options);

    deptSelect.addEventListener('change', function() {
        const selectedDept = this.value;

        // Clear current options except default placeholder
        roomSelect.innerHTML = '';
        roomSelect.appendChild(originalRoomOptions[0]);

        originalRoomOptions.slice(1).forEach(opt => {
            const optDept = opt.getAttribute('data-dept');
            if (selectedDept === '' || optDept === selectedDept || optDept === '') {
                roomSelect.appendChild(opt);
            }
        });
        roomSelect.selectedIndex = 0;
    });
});
</script>
