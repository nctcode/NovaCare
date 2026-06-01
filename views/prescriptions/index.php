<!-- Prescription List -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-custom alert-dismissible fade show">
        <i class="bi bi-check-circle-fill"></i> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-custom alert-dismissible fade show">
        <i class="bi bi-exclamation-triangle-fill"></i> <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<div class="card shadow-sm border-0 rounded-4 mb-4" data-aos="fade-up">
    <div class="card-header d-flex flex-wrap justify-content-between align-items-center bg-white border-bottom-0 pt-4 pb-0 px-4">
        <div>
            <h5 class="m-0 fw-bold text-primary"><i class="fa-solid fa-file-prescription me-2"></i>Danh sách Đơn thuốc</h5>
            <p class="text-muted small mt-1 mb-0">Quản lý và cập nhật trạng thái cấp phát thuốc</p>
        </div>
        <div class="d-flex gap-2 align-items-center mt-3 mt-md-0">
            <div class="input-group shadow-sm" style="width: 250px; border-radius: 20px; overflow: hidden;">
                <span class="input-group-text bg-white border-end-0 border-light-subtle"><i class="fa-solid fa-magnifying-glass text-muted"></i></span>
                <input type="text" class="form-control border-start-0 border-light-subtle table-search-js shadow-none" data-table="prescriptionTable" placeholder="Tìm tên bệnh nhân...">
            </div>
            
            <?php if (in_array($_SESSION['user']['role'], ['admin', 'pharmacist'])): ?>
            <button class="btn btn-success fw-medium shadow-sm" id="btnScanQRWebcamPrescription" style="border-radius:20px; padding:6px 16px;">
                <i class="fa-solid fa-camera me-1"></i>Quét
            </button>
            <button class="btn btn-outline-success fw-medium bg-white" id="btnScanQRImagePrescription" style="border-radius:20px; padding:6px 16px;">
                <i class="fa-solid fa-image me-1"></i>Ảnh
            </button>
            <input type="file" id="qrImageFileInputPrescription" accept="image/*" style="display:none;" />
            <?php endif; ?>
            
            <?php if ($_SESSION['user']['role'] === 'doctor'): ?>
            <a href="index.php?page=prescriptions&action=create" class="btn btn-primary fw-medium shadow-sm" style="border-radius:20px; padding:8px 20px;">
                <i class="fa-solid fa-plus me-1"></i>Tạo đơn thuốc
            </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body p-4">
        <div class="table-responsive" style="overflow-x: auto; white-space: nowrap;">
            <table class="table table-hover align-middle mb-0" id="prescriptionTable">
                <thead class="table-light">
                    <tr>
                        <th class="text-center text-muted" width="5%">#</th>
                        <th width="20%">Bệnh nhân</th>
                        <th width="15%">Bác sĩ</th>
                        <th width="30%">Chẩn đoán</th>
                        <th width="10%">Ngày tạo</th>
                        <th width="12%">Trạng thái</th>
                        <th class="text-center" width="8%">Thao tác</th>
                    </tr>
                </thead>
                <tbody class="border-top-0">
                    <?php if (empty($prescriptions)): ?>
                        <tr><td colspan="7" class="text-center py-5"><div class="empty-state"><div class="empty-icon text-muted mb-3"><i class="fa-solid fa-file-prescription fa-3x"></i></div><h6 class="text-secondary fw-bold">Chưa có đơn thuốc</h6><p class="text-muted small">Chưa có đơn thuốc nào được tạo.</p></div></td></tr>
                    <?php else: ?>
                        <?php foreach ($prescriptions as $idx => $pr): ?>
                        <tr>
                            <td class="text-center text-muted"><?= $idx + 1 ?></td>
                            <td><strong class="text-dark"><?= htmlspecialchars($pr['patient_name'] ?? '') ?></strong></td>
                            <td class="text-secondary"><?= htmlspecialchars($pr['doctor_name'] ?? '') ?></td>
                            <td class="text-truncate text-secondary" style="max-width: 250px;" title="<?= htmlspecialchars($pr['diagnosis'] ?? '') ?>">
                                <?= htmlspecialchars($pr['diagnosis'] ?? '') ?>
                            </td>
                            <td class="text-secondary"><i class="fa-regular fa-calendar text-muted me-1"></i><?= date('d/m/Y H:i', strtotime($pr['created_at'])) ?></td>
                            <td>
                                <?php
                                 $status = $pr['status'] ?? 'draft';
                                 if ($status === 'draft') {
                                     echo '<span class="badge rounded-pill bg-warning text-dark px-3 py-2"><i class="fa-regular fa-clock me-1"></i>Chưa thu tiền</span>';
                                 } elseif ($status === 'paid') {
                                     echo '<span class="badge rounded-pill bg-primary px-3 py-2"><i class="fa-solid fa-hourglass-half me-1"></i>Chờ giao thuốc</span>';
                                 } elseif ($status === 'dispensed') {
                                     echo '<span class="badge rounded-pill bg-success px-3 py-2"><i class="fa-solid fa-check-double me-1"></i>Đã giao thuốc</span>';
                                 } elseif ($status === 'cancelled') {
                                     echo '<span class="badge rounded-pill bg-danger px-3 py-2"><i class="fa-solid fa-xmark me-1"></i>Đã hủy</span>';
                                 }
                                ?>
                            </td>
                            <td class="text-center">
                                <a href="index.php?page=prescriptions&action=view&id=<?= $pr['id'] ?>" class="btn btn-sm btn-outline-primary rounded-circle" style="width: 32px; height: 32px; padding: 4px;" title="Xem chi tiết">
                                    <i class="fa-solid fa-eye"></i>
                                </a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Camera Scanner Modal -->
<div class="modal fade" id="qrCameraModalPrescription" tabindex="-1" aria-labelledby="qrCameraModalPrescriptionLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="qrCameraModalPrescriptionLabel"><i class="fa-solid fa-camera me-2 text-primary"></i>Quét mã QR từ Camera</h5>
                <button type="button" class="btn-close" id="btnStopQRCameraPrescriptionX" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center pt-2">
                <p class="text-muted small mb-3">Vui lòng căn chỉnh mã QR vào giữa khung hình bên dưới để quét.</p>
                <div class="qr-video-wrapper position-relative mx-auto rounded-3 overflow-hidden bg-black" style="width: 100%; max-width: 380px; aspect-ratio: 4/3; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                    <video id="qrScanVideoPrescription" class="w-100 h-100" style="object-fit: cover;" autoplay playsinline></video>
                    <!-- Laser scanning guide animation -->
                    <div class="scan-laser-line" style="position: absolute; left: 0; right: 0; height: 3px; background: rgba(46, 204, 113, 0.8); box-shadow: 0 0 8px #2ecc71; animation: scanLaserAnim 2s infinite linear; top: 0;"></div>
                    <div class="scan-target-frame" style="position: absolute; width: 60%; height: 60%; border: 2px dashed rgba(255,255,255,0.7); left: 20%; top: 20%; pointer-events: none; border-radius: 8px;"></div>
                </div>
                <div class="mt-3">
                    <span id="qrCamStatusPrescription" class="badge bg-secondary p-2"><i class="fa-solid fa-spinner fa-spin me-2"></i>Đang khởi động camera...</span>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0 justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCancelQRCameraPrescription" style="border-radius: 20px; font-weight: 500;">Hủy bỏ</button>
            </div>
        </div>
    </div>
</div>

<style>
@keyframes scanLaserAnim {
    0% { top: 10%; }
    50% { top: 90%; }
    100% { top: 10%; }
}
</style>

<script src="https://cdn.jsdelivr.net/npm/jsqr@1.4.0/dist/jsQR.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // --- 1. Quét QR từ File ảnh ---
    const btnScan = document.getElementById('btnScanQRImagePrescription');
    const fileInput = document.getElementById('qrImageFileInputPrescription');
    
    if (btnScan && fileInput) {
        btnScan.addEventListener('click', function() {
            fileInput.click();
        });
        
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            const reader = new FileReader();
            reader.onload = function(event) {
                const img = new Image();
                img.onload = function() {
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');
                    canvas.width = img.width;
                    canvas.height = img.height;
                    ctx.drawImage(img, 0, 0);
                    
                    const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                    const code = jsQR(imageData.data, imageData.width, imageData.height, {
                        inversionAttempts: "dontInvert",
                    });
                    
                    if (code) {
                        const url = code.data;
                        if (url.includes('index.php?page=')) {
                            let targetUrl = url;
                            const currentUrlObj = window.location;
                            try {
                                const parseUrl = url.startsWith('http') ? url : currentUrlObj.origin + (url.startsWith('/') ? '' : '/') + url;
                                const scannedUrlObj = new URL(parseUrl);
                                const params = new URLSearchParams(scannedUrlObj.search);
                                let prescriptionId = null;
                                
                                if (params.has('prescription_id')) {
                                    prescriptionId = params.get('prescription_id');
                                } else if (params.get('page') === 'prescriptions' && params.has('id')) {
                                    prescriptionId = params.get('id');
                                }
                                
                                if (prescriptionId) {
                                    targetUrl = currentUrlObj.origin + scannedUrlObj.pathname + "?page=prescriptions&action=view&id=" + prescriptionId;
                                } else {
                                    targetUrl = currentUrlObj.origin + scannedUrlObj.pathname + scannedUrlObj.search;
                                }
                            } catch(e) {
                                console.error("Lỗi parse URL quét được: ", e);
                            }
                            alert('Quét thành công! Đang chuyển hướng...');
                            window.location.href = targetUrl;
                        } else {
                            alert('Mã QR không hợp lệ cho hệ thống. Nội dung quét được: ' + url);
                        }
                    } else {
                        alert('Không tìm thấy mã QR trong hình ảnh. Vui lòng thử lại với ảnh rõ nét hơn.');
                    }
                };
                img.src = event.target.result;
            };
            reader.readAsDataURL(file);
            fileInput.value = '';
        });
    }

    // --- 2. Quét QR từ Webcam Camera ---
    let videoStream = null;
    let animationFrameId = null;
    const btnScanWebcam = document.getElementById('btnScanQRWebcamPrescription');
    const qrCameraModal = document.getElementById('qrCameraModalPrescription');
    
    if (btnScanWebcam && qrCameraModal) {
        const bsModal = new bootstrap.Modal(qrCameraModal);
        
        btnScanWebcam.addEventListener('click', function() {
            bsModal.show();
            startCamera();
        });
        
        document.getElementById('btnStopQRCameraPrescriptionX').addEventListener('click', stopCamera);
        document.getElementById('btnCancelQRCameraPrescription').addEventListener('click', stopCamera);
        qrCameraModal.addEventListener('hidden.bs.modal', stopCamera);
    }
    
    function startCamera() {
        const video = document.getElementById('qrScanVideoPrescription');
        const statusLabel = document.getElementById('qrCamStatusPrescription');
        
        statusLabel.innerHTML = '<i class="fa-solid fa-spinner fa-spin me-2"></i>Đang yêu cầu quyền truy cập camera...';
        statusLabel.className = 'badge bg-secondary p-2';
        
        navigator.mediaDevices.getUserMedia({ video: { facingMode: "environment" } })
            .then(function(stream) {
                videoStream = stream;
                video.srcObject = stream;
                video.setAttribute("playsinline", true); 
                video.play();
                statusLabel.innerHTML = '<i class="fa-solid fa-video me-2"></i>Camera đang hoạt động';
                statusLabel.className = 'badge bg-success p-2';
                
                animationFrameId = requestAnimationFrame(tick);
            })
            .catch(function(err) {
                console.error("Lỗi truy cập camera:", err);
                statusLabel.innerHTML = '<i class="fa-solid fa-triangle-exclamation me-2"></i>Không truy cập được camera. Vui lòng cấp quyền!';
                statusLabel.className = 'badge bg-danger p-2';
            });
    }

    function stopCamera() {
        if (animationFrameId) {
            cancelAnimationFrame(animationFrameId);
            animationFrameId = null;
        }
        if (videoStream) {
            videoStream.getTracks().forEach(track => track.stop());
            videoStream = null;
        }
        const video = document.getElementById('qrScanVideoPrescription');
        if (video) video.srcObject = null;
    }

    function tick() {
        const video = document.getElementById('qrScanVideoPrescription');
        const statusLabel = document.getElementById('qrCamStatusPrescription');
        
        if (video && video.readyState === video.HAVE_ENOUGH_DATA) {
            const canvas = document.createElement('canvas');
            canvas.width = video.videoWidth;
            canvas.height = video.videoHeight;
            const ctx = canvas.getContext('2d');
            ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
            
            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
            const code = jsQR(imageData.data, imageData.width, imageData.height, {
                inversionAttempts: "dontInvert",
            });
            
            if (code) {
                const url = code.data;
                if (url.includes('index.php?page=')) {
                    // Beep sound
                    try {
                        const audioCtx = new (window.AudioContext || window.webkitAudioContext)();
                        const osc = audioCtx.createOscillator();
                        osc.type = "sine";
                        osc.frequency.setValueAtTime(800, audioCtx.currentTime);
                        osc.connect(audioCtx.destination);
                        osc.start();
                        osc.stop(audioCtx.currentTime + 0.15);
                    } catch(e) {
                        console.log("Audio not supported or blocked");
                    }
                    
                    statusLabel.innerHTML = '<i class="fa-solid fa-circle-check me-2"></i>Đã quét thành công!';
                    statusLabel.className = 'badge bg-success p-2';
                    
                    stopCamera();
                    
                    const bsModal = bootstrap.Modal.getInstance(qrCameraModal);
                    if (bsModal) bsModal.hide();
                    
                    let targetUrl = url;
                    const currentUrlObj = window.location;
                    try {
                        const parseUrl = url.startsWith('http') ? url : currentUrlObj.origin + (url.startsWith('/') ? '' : '/') + url;
                        const scannedUrlObj = new URL(parseUrl);
                        const params = new URLSearchParams(scannedUrlObj.search);
                        let prescriptionId = null;
                        
                        if (params.has('prescription_id')) {
                            prescriptionId = params.get('prescription_id');
                        } else if (params.get('page') === 'prescriptions' && params.has('id')) {
                            prescriptionId = params.get('id');
                        }
                        
                        if (prescriptionId) {
                            targetUrl = currentUrlObj.origin + scannedUrlObj.pathname + "?page=prescriptions&action=view&id=" + prescriptionId;
                        } else {
                            targetUrl = currentUrlObj.origin + scannedUrlObj.pathname + scannedUrlObj.search;
                        }
                    } catch(e) {
                        console.error("Lỗi parse URL quét được: ", e);
                    }
                    
                    alert('Quét thành công! Đang chuyển hướng...');
                    window.location.href = targetUrl;
                    return;
                }
            }
        }
        
        if (videoStream) {
            animationFrameId = requestAnimationFrame(tick);
        }
    }
});
</script>