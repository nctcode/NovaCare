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
                            
                            <!-- Appointment Info Banner -->
                            <div class="alert alert-info border-0 shadow-sm d-flex align-items-center gap-3 p-3 mb-4 rounded-3" style="background: rgba(14, 165, 233, 0.08);">
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center" style="width: 40px; height: 40px; flex-shrink: 0;">
                                    <i class="fa-solid fa-calendar-check fs-5"></i>
                                </div>
                                <div>
                                    <h6 class="mb-0 text-dark fw-bold" style="font-size: 14px;">Tiếp nhận theo lịch hẹn khám</h6>
                                    <span class="text-secondary text-xs">
                                        Khung giờ đã đặt: <strong class="text-primary"><?= date('H:i - d/m/Y', strtotime($selectedAppointment['appointment_date'])) ?></strong> 
                                        với bác sĩ: <strong>BS. <?= htmlspecialchars($selectedAppointment['doctor_name']) ?></strong>.
                                    </span>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Box 1: Chọn bệnh nhân -->
                        <div class="bg-white p-4 rounded-3 shadow-sm mb-4 border">
                            <h6 class="text-uppercase text-secondary text-xs font-weight-bolder mb-3"><i class="fas fa-user-injured me-2"></i>1. Chọn Bệnh Nhân</h6>
                            
                            <div class="row g-3">
                                <!-- Quick QR Scanner Card (Multi-mode) -->
                                <div class="col-md-12 mb-2 p-3 rounded-3 border" style="background: linear-gradient(135deg, #f0fdf4 0%, #ffffff 100%); border-color: #bbf7d0 !important;">
                                    <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
                                        <label class="form-label text-success mb-0 fw-bold" style="font-size:12.5px;"><i class="fa-solid fa-qrcode me-1"></i>Quét nhanh mã QR BHYT / CCCD</label>
                                        <div class="btn-group btn-group-sm" role="group">
                                            <button type="button" class="btn btn-outline-success btn-xs active" id="btn_checkin_reader" onclick="switchCheckinQRMode('reader')"><i class="fa-solid fa-barcode me-1"></i>Đầu đọc</button>
                                            <button type="button" class="btn btn-outline-success btn-xs" id="btn_checkin_camera" onclick="switchCheckinQRMode('camera')"><i class="fa-solid fa-camera me-1"></i>Camera</button>
                                            <button type="button" class="btn btn-outline-success btn-xs" id="btn_checkin_file" onclick="switchCheckinQRMode('file')"><i class="fa-solid fa-image me-1"></i>Tải ảnh</button>
                                        </div>
                                    </div>

                                    <!-- Mode: Reader -->
                                    <div id="checkin_qr_reader_section" class="checkin-qr-section">
                                        <div class="input-group">
                                            <span class="input-group-text bg-success-subtle text-success border-success-subtle"><i class="fa-solid fa-barcode"></i></span>
                                            <input type="text" id="checkin_qr_input_reader" class="form-control border-success-subtle" placeholder="Đặt con trỏ chuột vào đây và Quét mã QR..." style="font-size: 13px; background: rgba(240, 253, 244, 0.3);">
                                        </div>
                                    </div>

                                    <!-- Mode: Camera -->
                                    <div id="checkin_qr_camera_section" class="checkin-qr-section" style="display:none;">
                                        <div class="d-flex flex-column align-items-center justify-content-center border rounded-3 p-2 bg-dark position-relative" style="min-height: 180px;">
                                            <div id="checkin_camera_view" style="width: 100%; max-width: 320px;"></div>
                                            <div class="mt-2 d-flex gap-2">
                                                <button type="button" class="btn btn-success btn-xs px-3" id="btn_checkin_start_cam" onclick="startCheckinCamera()"><i class="fa-solid fa-play me-1"></i>Bắt đầu</button>
                                                <button type="button" class="btn btn-secondary btn-xs px-3" id="btn_checkin_stop_cam" onclick="stopCheckinCamera()" style="display:none;"><i class="fa-solid fa-stop me-1"></i>Dừng</button>
                                            </div>
                                        </div>
                                    </div>

                                    <!-- Mode: File -->
                                    <div id="checkin_qr_file_section" class="checkin-qr-section" style="display:none;">
                                        <div class="input-group">
                                            <span class="input-group-text bg-success-subtle text-success border-success-subtle"><i class="fa-solid fa-upload"></i></span>
                                            <input type="file" id="checkin_qr_file_input" class="form-control border-success-subtle" accept="image/*">
                                        </div>
                                    </div>

                                    <div id="checkin_qr_status" class="text-xs mt-1 fw-semibold text-secondary" style="font-size:11px;">
                                        <i class="fa-solid fa-circle-info me-1"></i>Hỗ trợ quét qua đầu đọc mã vạch cầm tay từ ứng dụng VssID, thẻ BHYT hoặc CCCD.
                                    </div>
                                </div>

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

<script src="https://unpkg.com/html5-qrcode"></script>
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
    
    // Trigger BHYT check on page load if patient is already pre-selected
    if (patientSelect.selectedIndex > 0) {
        patientSelect.dispatchEvent(new Event('change'));
    }

    // --- MULTI-MODE QR CODE SCANNER (READER, CAMERA, FILE) ---
    const checkinQrInputReader = document.getElementById('checkin_qr_input_reader');
    const checkinQrFile = document.getElementById('checkin_qr_file_input');
    const checkinQrStatus = document.getElementById('checkin_qr_status');
    let html5QrCode = null;

    function cleanString(str) {
        return str ? str.trim() : '';
    }

    function removeDiacritics(str) {
        return str.normalize('NFD')
                  .replace(/[\u0300-\u036f]/g, '')
                  .replace(/đ/g, 'd')
                  .replace(/Đ/g, 'd')
                  .toLowerCase();
    }

    function handleQRScan(rawValue) {
        if (!rawValue || !rawValue.includes('|')) return;
        
        const parts = rawValue.split('|');
        let scannedInsurance = '';
        let scannedName = '';
        
        // CCCD has parts[0] as 12 digit number, parts[2] as name
        const isCCCD = /^\d{12}$/.test(parts[0]);
        
        if (isCCCD) {
            scannedName = cleanString(parts[2]);
        } else {
            // BHYT: GD4797918800045|Nguyễn Văn A|15/08/1992|...
            scannedInsurance = cleanString(parts[0]);
            scannedName = cleanString(parts[1]);
        }

        let found = false;
        // Search in patient select options
        for (let i = 0; i < patientSelect.options.length; i++) {
            const opt = patientSelect.options[i];
            const optInsurance = opt.getAttribute('data-insurance') || '';
            const optText = opt.textContent || '';
            
            // Match BHYT number or loose match Name
            const matchInsurance = scannedInsurance && optInsurance.toUpperCase() === scannedInsurance.toUpperCase();
            const matchName = scannedName && removeDiacritics(optText).includes(removeDiacritics(scannedName));
            
            if (matchInsurance || matchName) {
                patientSelect.selectedIndex = i;
                patientSelect.dispatchEvent(new Event('change'));
                found = true;
                break;
            }
        }

        if (found) {
            const selectedOptText = patientSelect.options[patientSelect.selectedIndex].textContent.trim();
            checkinQrStatus.className = "text-xs mt-1 fw-bold text-success animate__animated animate__pulse";
            checkinQrStatus.innerHTML = `<i class="fa-solid fa-circle-check me-1"></i>Đã chọn bệnh nhân: <strong class="text-dark">${selectedOptText}</strong>`;
        } else {
            checkinQrStatus.className = "text-xs mt-1 fw-bold text-danger";
            checkinQrStatus.innerHTML = `<i class="fa-solid fa-circle-exclamation me-1"></i>Không khớp BHYT/Họ tên quét được: <strong class="text-dark">${scannedInsurance || scannedName}</strong>`;
        }
        checkinQrInputReader.value = ''; // clear
    }

    // Keydown for reader
    if (checkinQrInputReader) {
        checkinQrInputReader.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                handleQRScan(this.value);
            }
        });

        checkinQrInputReader.addEventListener('change', function() {
            handleQRScan(this.value);
        });
    }

    // File Input Scanning
    if (checkinQrFile) {
        checkinQrFile.addEventListener('change', function(e) {
            if (e.target.files.length === 0) return;
            const file = e.target.files[0];
            
            if (!html5QrCode) {
                html5QrCode = new Html5Qrcode("checkin_camera_view");
            }
            
            checkinQrStatus.className = "text-xs mt-1 fw-bold text-primary";
            checkinQrStatus.innerHTML = `<i class="fa-solid fa-spinner fa-spin me-1"></i>Đang phân tích hình ảnh...`;
            
            html5QrCode.scanFile(file, true)
                .then(decodedText => {
                    handleQRScan(decodedText);
                    checkinQrFile.value = '';
                })
                .catch(err => {
                    console.error("Lỗi quét file ảnh:", err);
                    checkinQrStatus.className = "text-xs mt-1 fw-bold text-danger";
                    checkinQrStatus.innerHTML = `<i class="fa-solid fa-triangle-exclamation me-1"></i>Không tìm thấy mã QR hợp lệ trong ảnh.`;
                    checkinQrFile.value = '';
                });
        });
    }

    // Global toggle and start/stop controls for Camera
    window.switchCheckinQRMode = function(mode) {
        stopCheckinCamera();
        
        document.querySelectorAll('.checkin-qr-section').forEach(el => el.style.display = 'none');
        document.getElementById('btn_checkin_reader').classList.remove('active');
        document.getElementById('btn_checkin_camera').classList.remove('active');
        document.getElementById('btn_checkin_file').classList.remove('active');

        if (mode === 'reader') {
            document.getElementById('checkin_qr_reader_section').style.display = 'block';
            document.getElementById('btn_checkin_reader').classList.add('active');
            checkinQrStatus.className = "text-xs mt-1 fw-semibold text-secondary";
            checkinQrStatus.innerHTML = '<i class="fa-solid fa-circle-info me-1"></i>Hỗ trợ quét qua đầu đọc mã vạch cầm tay từ ứng dụng VssID, thẻ BHYT hoặc CCCD.';
            checkinQrInputReader.focus();
        } else if (mode === 'camera') {
            document.getElementById('checkin_qr_camera_section').style.display = 'block';
            document.getElementById('btn_checkin_camera').classList.add('active');
            checkinQrStatus.className = "text-xs mt-1 fw-semibold text-secondary";
            checkinQrStatus.innerHTML = '<i class="fa-solid fa-circle-info me-1"></i>Sử dụng webcam thiết bị để quét trực tiếp.';
        } else if (mode === 'file') {
            document.getElementById('checkin_qr_file_section').style.display = 'block';
            document.getElementById('btn_checkin_file').classList.add('active');
            checkinQrStatus.className = "text-xs mt-1 fw-semibold text-secondary";
            checkinQrStatus.innerHTML = '<i class="fa-solid fa-circle-info me-1"></i>Tải lên ảnh chụp mã QR từ điện thoại hoặc máy tính.';
        }
    };

    window.startCheckinCamera = function() {
        if (!html5QrCode) {
            html5QrCode = new Html5Qrcode("checkin_camera_view");
        }
        
        document.getElementById('btn_checkin_start_cam').style.display = 'none';
        document.getElementById('btn_checkin_stop_cam').style.display = 'inline-block';
        
        const config = { fps: 10, qrbox: { width: 220, height: 220 } };
        
        html5QrCode.start(
            { facingMode: "environment" }, 
            config, 
            (decodedText, decodedResult) => {
                handleQRScan(decodedText);
                stopCheckinCamera();
            },
            (errorMessage) => {}
        ).catch(err => {
            console.error("Camera error:", err);
            checkinQrStatus.className = "text-xs mt-1 fw-bold text-danger";
            checkinQrStatus.innerHTML = `<i class="fa-solid fa-triangle-exclamation me-1"></i>Không thể khởi động camera. Hãy cấp quyền camera.`;
            document.getElementById('btn_checkin_start_cam').style.display = 'inline-block';
            document.getElementById('btn_checkin_stop_cam').style.display = 'none';
        });
    };

    window.stopCheckinCamera = function() {
        if (html5QrCode && html5QrCode.isScanning) {
            html5QrCode.stop().then(() => {
                document.getElementById('btn_checkin_start_cam').style.display = 'inline-block';
                document.getElementById('btn_checkin_stop_cam').style.display = 'none';
            }).catch(err => console.error("Stop camera error:", err));
        } else {
            document.getElementById('btn_checkin_start_cam').style.display = 'inline-block';
            document.getElementById('btn_checkin_stop_cam').style.display = 'none';
        }
    };

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

    // Trigger department room filter on page load if department is already pre-selected
    if (deptSelect.selectedIndex > 0) {
        deptSelect.dispatchEvent(new Event('change'));
    }
});
</script>