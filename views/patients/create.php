<!-- Form Thêm Bệnh nhân -->
<div class="card shadow-sm border-0 rounded-4 mb-4">
    <div class="card-header bg-white border-bottom-0 pt-4 pb-2 px-4">
        <h4 class="text-primary fw-bold mb-0"><i class="fa-solid fa-user-plus me-2"></i>Đăng ký Bệnh nhân Mới</h4>
        <p class="text-muted small mt-1 mb-0">Vui lòng điền đầy đủ thông tin hoặc sử dụng công cụ nhập liệu tự động.</p>
    </div>
    <div class="card-body p-4">
        <form method="POST" action="index.php?page=patients&action=store" id="createPatientForm">
            <?php echo Security::csrfField(); ?>
            
            <!-- Nút OCR & QR Tối ưu -->
            <div class="alert border-0 shadow-sm mb-4 d-flex align-items-center justify-content-between flex-wrap gap-3" style="background: linear-gradient(135deg, #e0eafc 0%, #cfdef3 100%); border-radius: 12px;">
                <div class="d-flex align-items-center">
                    <div class="bg-white p-3 rounded-circle text-primary me-3 shadow-sm d-flex align-items-center justify-content-center" style="width: 50px; height: 50px;">
                        <i class="fa-solid fa-bolt fs-4"></i>
                    </div>
                    <div>
                        <h6 class="mb-1 fw-bold text-dark">Nhập liệu siêu tốc</h6>
                        <span class="small text-secondary">Sử dụng CCCD gắn chip hoặc VNeID để điền form tự động 100%</span>
                    </div>
                </div>
                <div class="d-flex gap-2">
                    <input type="file" id="ocrImageInput" accept="image/*" class="d-none">
                    <button type="button" class="btn btn-primary rounded-pill px-4 shadow-sm fw-medium" onclick="openQRModal()">
                        <i class="fa-solid fa-qrcode me-2"></i> Quét QR
                    </button>
                    <button type="button" class="btn btn-light rounded-pill px-4 shadow-sm text-primary fw-medium" onclick="openCameraModal()">
                        <i class="fa-solid fa-camera me-2"></i> Chụp ảnh
                    </button>
                    <button type="button" class="btn btn-outline-primary rounded-pill px-4 bg-white shadow-sm fw-medium" onclick="document.getElementById('ocrImageInput').click()" title="Tải ảnh lên">
                        <i class="fa-solid fa-folder-open"></i>
                    </button>
                </div>
            </div>

        <div id="ocrLoading" class="alert d-none mb-4 d-flex align-items-center" style="background-color: #fdfae2; border: 1px solid #fbeeb8; color: #856404; border-radius: 8px;">
            <div class="spinner-border spinner-border-sm me-3 text-warning" role="status"></div>
            <strong>Hệ thống đang AI hóa dữ liệu...</strong> Vui lòng chờ trong giây lát.
        </div>

        <h6 class="text-uppercase text-secondary fw-bold mb-3 mt-2" style="font-size: 0.85rem; letter-spacing: 0.5px;">Thông tin cá nhân</h6>
        
        <div class="row g-3 mb-4">
            <div class="col-md-6">
                <label for="name" class="form-label fw-medium text-dark">Họ tên <span class="text-danger">*</span></label>
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-regular fa-user"></i></span>
                    <input type="text" class="form-control border-start-0 ps-0" id="name" name="name" required placeholder="Nhập họ tên...">
                </div>
            </div>
            <div class="col-md-6">
                <label for="email" class="form-label fw-medium text-dark">Email <span class="text-muted fw-normal">(Tùy chọn)</span></label>
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-regular fa-envelope"></i></span>
                    <input type="email" class="form-control border-start-0 ps-0" id="email" name="email" placeholder="Nhập email nếu có...">
                </div>
            </div>
            
            <div class="col-md-4">
                <label for="phone" class="form-label fw-medium text-dark">Số điện thoại</label>
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-phone"></i></span>
                    <input type="text" class="form-control border-start-0 ps-0" id="phone" name="phone" placeholder="09xxxxxxxx">
                </div>
            </div>
            <div class="col-md-4">
                <label for="date_of_birth" class="form-label fw-medium text-dark">Ngày sinh</label>
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-regular fa-calendar"></i></span>
                    <input type="date" class="form-control border-start-0 ps-0" id="date_of_birth" name="date_of_birth">
                </div>
            </div>
            <div class="col-md-4">
                <label for="gender" class="form-label fw-medium text-dark">Giới tính</label>
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-venus-mars"></i></span>
                    <select class="form-select border-start-0 ps-0" id="gender" name="gender">
                        <option value="">-- Chọn --</option>
                        <option value="male">Nam</option>
                        <option value="female">Nữ</option>
                        <option value="other">Khác</option>
                    </select>
                </div>
            </div>
        </div>

        <h6 class="text-uppercase text-secondary fw-bold mb-3 mt-4" style="font-size: 0.85rem; letter-spacing: 0.5px;">Thông tin y tế & Liên hệ</h6>
        
        <div class="row g-3 mb-4">
            <div class="col-md-4">
                <label for="blood_type" class="form-label fw-medium text-dark">Nhóm máu</label>
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-white border-end-0 text-danger"><i class="fa-solid fa-droplet"></i></span>
                    <select class="form-select border-start-0 ps-0" id="blood_type" name="blood_type">
                        <option value="">-- Chọn --</option>
                        <option value="" selected>Không rõ</option>
                        <option value="A+">A+</option>
                        <option value="A-">A-</option>
                        <option value="B+">B+</option>
                        <option value="B-">B-</option>
                        <option value="O+">O+</option>
                        <option value="O-">O-</option>
                        <option value="AB+">AB+</option>
                        <option value="AB-">AB-</option>
                    </select>
                </div>
            </div>
            <div class="col-md-8">
                <label for="address" class="form-label fw-medium text-dark">Địa chỉ</label>
                <div class="input-group shadow-sm">
                    <span class="input-group-text bg-white border-end-0 text-muted"><i class="fa-solid fa-map-location-dot"></i></span>
                    <input type="text" class="form-control border-start-0 ps-0" id="address" name="address" placeholder="Nhập địa chỉ thường trú...">
                </div>
            </div>
            
            <div class="col-12">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label for="medical_history" class="form-label fw-medium text-dark mb-0">Tiền sử bệnh</label>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 voice-input-btn shadow-sm fw-medium" data-target="medical_history" title="Nhập liệu bằng giọng nói">
                        <i class="fa-solid fa-microphone me-1"></i> Đọc chính tả
                    </button>
                </div>
                <textarea class="form-control shadow-sm" id="medical_history" name="medical_history" rows="3" placeholder="Mô tả tiền sử bệnh, dị ứng thuốc (nếu có)..."></textarea>
            </div>
        </div>

        <hr class="my-4 text-muted">

        <div class="d-flex justify-content-end gap-3">
            <a href="index.php?page=patients" class="btn btn-light rounded-pill px-4 shadow-sm border">
                <i class="fa-solid fa-xmark me-2"></i> Hủy bỏ
            </a>
            <button type="submit" class="btn btn-primary rounded-pill px-5 shadow" id="btnSubmitPatient">
                <i class="fa-solid fa-check me-2"></i> Lưu Hồ Sơ
            </button>
        </div>
    </form>
    </div> <!-- end card-body -->
</div> <!-- end card -->

<!-- Camera Modal -->
<div class="modal fade" id="cameraModal" tabindex="-1" aria-labelledby="cameraModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="cameraModalLabel"><i class="fa-solid fa-camera me-2"></i>Chụp ảnh thẻ từ Camera</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="stopCamera()"></button>
      </div>
      <div class="modal-body text-center">
        <video id="cameraVideo" width="100%" autoplay playsinline style="border-radius: 8px; background: #000; max-height: 300px;"></video>
        <canvas id="cameraCanvas" style="display:none;"></canvas>
      </div>
      <div class="modal-footer justify-content-center">
        <button type="button" class="btn btn-danger rounded-pill px-4" onclick="captureImage()">
            <i class="fa-solid fa-circle-dot me-1"></i> Chụp & Quét OCR
        </button>
      </div>
    </div>
  </div>
</div>

<!-- QR Scanner Modal -->
<div class="modal fade" id="qrModal" tabindex="-1" aria-labelledby="qrModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="qrModalLabel"><i class="fa-solid fa-qrcode me-2"></i>Quét mã QR CCCD / VNeID</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" onclick="stopQRScanner()"></button>
      </div>
      <div class="modal-body text-center">
        <div id="qr-reader" style="width:100%; border-radius: 8px; overflow: hidden;"></div>
      </div>
    </div>
  </div>
</div>

<!-- Tesseract OCR Library -->
<script src="https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js"></script>
<!-- HTML5 QR Code Library -->
<script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const ocrInput = document.getElementById('ocrImageInput');
    if(ocrInput) {
        ocrInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            processOCR(file);
            e.target.value = '';
        });
    }

    // Dừng camera khi modal bị đóng thủ công
    const cameraModalEl = document.getElementById('cameraModal');
    if (cameraModalEl) {
        cameraModalEl.addEventListener('hidden.bs.modal', function () {
            stopCamera();
        });
    }

    // Dừng máy quét QR khi modal bị đóng
    const qrModalEl = document.getElementById('qrModal');
    if (qrModalEl) {
        qrModalEl.addEventListener('hidden.bs.modal', function () {
            stopQRScanner();
        });
    }

    // Auto-generate email if empty before form submit
    const createPatientForm = document.getElementById('createPatientForm');
    if (createPatientForm) {
        createPatientForm.addEventListener('submit', function(e) {
            const emailInput = document.getElementById('email');
            if (!emailInput.value.trim()) {
                const phone = document.getElementById('phone').value.trim();
                // Generate a dummy email to satisfy backend validation if required
                emailInput.value = (phone ? phone : 'bn_' + Date.now()) + '@novacare.local';
            }
        });
    }
});

let html5QrcodeScanner = null;

function openQRModal() {
    const qrModal = new bootstrap.Modal(document.getElementById('qrModal'));
    qrModal.show();

    if (!html5QrcodeScanner) {
        html5QrcodeScanner = new Html5QrcodeScanner(
            "qr-reader",
            { fps: 10, qrbox: {width: 250, height: 250} },
            /* verbose= */ false
        );
    }
    
    html5QrcodeScanner.render(onScanSuccess, onScanFailure);
}

function stopQRScanner() {
    if (html5QrcodeScanner) {
        try {
            html5QrcodeScanner.clear();
        } catch (error) {
            console.error("Failed to clear html5QrcodeScanner.", error);
        }
    }
}

function onScanSuccess(decodedText, decodedResult) {
    // Dừng quét
    stopQRScanner();
    const modalInstance = bootstrap.Modal.getInstance(document.getElementById('qrModal'));
    if (modalInstance) modalInstance.hide();
    
    console.log("QR Code detected: ", decodedText);
    
    // Định dạng: 079090123456|123456789|NGUYỄN VĂN A|01011990|Nam|123 Đường A, Phường B, Quận C, TP.HCM|15102021
    const parts = decodedText.split('|');
    if (parts.length >= 6) {
        const name = parts[2];
        const dobStr = parts[3];
        const genderStr = parts[4];
        const address = parts[5];
        
        // 1. Tên
        document.getElementById('name').value = name;
        
        // 2. Ngày sinh (DDMMYYYY -> YYYY-MM-DD)
        if (dobStr.length === 8) {
            const d = dobStr.substring(0, 2);
            const m = dobStr.substring(2, 4);
            const y = dobStr.substring(4, 8);
            document.getElementById('date_of_birth').value = `${y}-${m}-${d}`;
        }
        
        // 3. Giới tính
        if (genderStr.toLowerCase() === 'nam') {
            document.getElementById('gender').value = 'male';
        } else if (genderStr.toLowerCase() === 'nữ' || genderStr.toLowerCase() === 'nu') {
            document.getElementById('gender').value = 'female';
        }
        
        // 4. Địa chỉ
        document.getElementById('address').value = address;
        
        alert('Quét mã QR VNeID / CCCD thành công!');
    } else {
        alert('Định dạng mã QR không hợp lệ. Vui lòng thử lại.');
    }
}

function onScanFailure(error) {
    // Bỏ qua lỗi quét để quét tiếp tục
}

let videoStream = null;

function openCameraModal() {
    const cameraModal = new bootstrap.Modal(document.getElementById('cameraModal'));
    cameraModal.show();
    
    const video = document.getElementById('cameraVideo');
    navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } })
        .then(stream => {
            videoStream = stream;
            video.srcObject = stream;
        })
        .catch(err => {
            console.error("Camera error:", err);
            alert("Không thể truy cập camera. Vui lòng cấp quyền sử dụng camera trên trình duyệt.");
        });
}

function stopCamera() {
    if (videoStream) {
        videoStream.getTracks().forEach(track => track.stop());
        videoStream = null;
    }
}

function captureImage() {
    if (!videoStream) return;
    
    const video = document.getElementById('cameraVideo');
    const canvas = document.getElementById('cameraCanvas');
    canvas.width = video.videoWidth;
    canvas.height = video.videoHeight;
    const ctx = canvas.getContext('2d');
    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
    
    // Đóng modal & dừng camera
    const modalInstance = bootstrap.Modal.getInstance(document.getElementById('cameraModal'));
    if (modalInstance) modalInstance.hide();
    stopCamera();
    
    // Chuyển canvas thành file blob để quét OCR
    canvas.toBlob(blob => {
        if (blob) {
            const file = new File([blob], "camera_capture.jpg", { type: "image/jpeg" });
            processOCR(file);
        }
    }, 'image/jpeg', 0.9);
}

function processOCR(file) {
    const loadingEl = document.getElementById('ocrLoading');
    loadingEl.classList.remove('d-none');

    Tesseract.recognize(
        file,
        'vie', // Dùng mô hình Tiếng Việt
        { logger: m => console.log(m) }
    ).then(({ data: { text } }) => {
        loadingEl.classList.add('d-none');
        console.log('OCR Raw Text:', text);
        
        // 1. Tách Ngày sinh (dd/mm/yyyy)
        const dobMatch = text.match(/(\d{2})[\/\-\.](\d{2})[\/\-\.](\d{4})/);
        if (dobMatch) {
            const [_, day, month, year] = dobMatch;
            document.getElementById('date_of_birth').value = `${year}-${month}-${day}`;
        }
        
        // 2. Tách Giới tính
        if (text.match(/\b(Nam)\b/i)) {
            document.getElementById('gender').value = 'male';
        } else if (text.match(/\b(Nữ|Nu)\b/i)) {
            document.getElementById('gender').value = 'female';
        }

        // 3. Tách Họ tên
        const nameRegex = /Họ\s*(?:và)?\s*tên[:\s]*([A-Z\s]+)/i;
        const nameMatch = text.match(nameRegex);
        if (nameMatch && nameMatch[1]) {
            document.getElementById('name').value = nameMatch[1].trim();
        } else {
            const lines = text.split('\n');
            for (let line of lines) {
                line = line.trim();
                if (line.length > 5 && /^[A-ZÀÁẠẢÃÂẦẤẬẨẪĂẰẮẶẲẴÈÉẸẺẼÊỀẾỆỂỄÌÍỊỈĨÒÓỌỎÕÔỒỐỘỔỖƠỜỚỢỞỠÙÚỤỦŨƯỪỨỰỬỮỲÝỴỶỸĐ\s]+$/.test(line) && !line.includes('CỘNG HÒA') && !line.includes('ĐỘC LẬP') && !line.includes('CĂN CƯỚC')) {
                    document.getElementById('name').value = line;
                    break;
                }
            }
        }

        // 4. Tách Địa chỉ (Nơi thường trú:)
        const addressRegex = /thường\s*trú[:\s]*([^\n]+)/i;
        const addressMatch = text.match(addressRegex);
        if (addressMatch && addressMatch[1]) {
            document.getElementById('address').value = addressMatch[1].trim();
        }
        
        alert('Trích xuất dữ liệu thành công. Vui lòng kiểm tra lại độ chính xác trên form!');
    }).catch(err => {
        loadingEl.classList.add('d-none');
        console.error('OCR Error:', err);
        alert('Có lỗi xảy ra khi đọc thẻ. Vui lòng thử lại với ảnh thẻ chụp rõ nét, đủ sáng.');
    });
}
</script>
