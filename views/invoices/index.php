<!-- Invoice List -->
<?php if (isset($_SESSION['success'])): ?>
    <div class="alert alert-success alert-custom alert-dismissible fade show">
        <i class="fa-solid fa-check-circle"></i> <?= $_SESSION['success']; unset($_SESSION['success']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if (isset($_SESSION['info'])): ?>
    <div class="alert alert-info alert-custom alert-dismissible fade show">
        <i class="fa-solid fa-info-circle"></i> <?= $_SESSION['info']; unset($_SESSION['info']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>
<?php if (isset($_SESSION['error'])): ?>
    <div class="alert alert-danger alert-custom alert-dismissible fade show">
        <i class="fa-solid fa-exclamation-triangle"></i> <?= $_SESSION['error']; unset($_SESSION['error']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
<?php endif; ?>

<?php if ($_SESSION['user']['role'] === 'admin'): ?>
<!-- Stats Cards -->
<div class="row g-3 mb-4">
    <div class="col-md-3">
        <div class="content-card text-center p-3" style="border-radius:16px;">
            <i class="fa-solid fa-file-invoice text-primary" style="font-size:28px;"></i>
            <h3 class="mt-2 mb-0" style="font-weight:700;"><?= $stats['total'] ?></h3>
            <small class="text-muted">Tổng hóa đơn</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="content-card text-center p-3" style="border-radius:16px;">
            <i class="fa-solid fa-clock text-warning" style="font-size:28px;"></i>
            <h3 class="mt-2 mb-0" style="font-weight:700;"><?= $stats['pending'] ?></h3>
            <small class="text-muted">Chờ thanh toán</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="content-card text-center p-3" style="border-radius:16px;">
            <i class="fa-solid fa-check-circle text-success" style="font-size:28px;"></i>
            <h3 class="mt-2 mb-0" style="font-weight:700;"><?= $stats['paid'] ?></h3>
            <small class="text-muted">Đã thanh toán</small>
        </div>
    </div>
    <div class="col-md-3">
        <div class="content-card text-center p-3" style="border-radius:16px;">
            <i class="fa-solid fa-coins text-info" style="font-size:28px;"></i>
            <h3 class="mt-2 mb-0" style="font-weight:700;"><?= number_format($stats['revenue'], 0, ',', '.') ?>đ</h3>
            <small class="text-muted">Doanh thu</small>
        </div>
    </div>
</div>
<?php endif; ?>

<div class="content-card" data-aos="fade-up" style="border-radius:16px; box-shadow:0 4px 20px rgba(0,0,0,0.03);">
    <div class="card-header d-flex justify-content-between align-items-center bg-white border-bottom-0 pt-4 pb-0">
        <div>
            <h5 class="m-0" style="font-weight:700;"><i class="fa-solid fa-file-invoice-dollar me-2 text-primary"></i>Danh sách Hóa đơn</h5>
            <p class="text-muted" style="font-size:13px; margin:5px 0 0 0;">Quản lý hóa đơn và thanh toán</p>
        </div>
        <div class="d-flex align-items-center">
            <?php if (in_array($_SESSION['user']['role'], ['admin', 'cashier'])): ?>
            <a href="index.php?page=invoices&action=create" class="btn btn-primary" style="border-radius:20px; font-weight:500; padding: 6px 18px;">
                <i class="fa-solid fa-plus me-2"></i>Tạo hóa đơn
            </a>
            <button class="btn btn-success ms-2" id="btnScanQRWebcam" style="border-radius:20px; font-weight:500; padding: 6px 18px; display: inline-flex; align-items: center; justify-content: center;">
                <i class="fa-solid fa-camera me-2"></i>Quét QR từ Camera
            </button>
            <button class="btn btn-outline-success ms-2" id="btnScanQRImage" style="border-radius:20px; font-weight:500; padding: 6px 18px; display: inline-flex; align-items: center; justify-content: center;">
                <i class="fa-solid fa-qrcode me-2"></i>Quét QR từ ảnh
            </button>
            <input type="file" id="qrImageFileInput" accept="image/*" style="display:none;" />
            <?php endif; ?>
        </div>
    </div>
    <div class="card-body">
        <div class="table-wrapper">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Mã HĐ</th>
                        <th>Bệnh nhân</th>
                        <th>Ngày tạo</th>
                        <th>Tổng tiền</th>
                        <th>Giảm giá</th>
                        <th>Thành tiền</th>
                        <th>Thanh toán</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($invoices)): ?>
                        <tr><td colspan="9" class="text-center text-muted py-4">Chưa có hóa đơn nào</td></tr>
                    <?php else: ?>
                        <?php foreach ($invoices as $inv): ?>
                        <tr>
                            <td><strong>#<?= $inv['id'] ?></strong></td>
                            <td>
                                <?= htmlspecialchars($inv['patient_name'] ?? '') ?>
                                <?php if (!empty($inv['insurance_rate']) && $inv['insurance_rate'] > 0): ?>
                                    <br><span class="badge bg-light text-success border border-success" style="font-size: 9px; padding: 2px 6px; font-weight: 600;">BHYT <?= number_format($inv['insurance_rate'], 0) ?>%</span>
                                <?php endif; ?>
                            </td>
                            <td><?= date('d/m/Y H:i', strtotime($inv['created_at'])) ?></td>
                            <td style="text-align:right;"><?= number_format($inv['total_amount'], 0, ',', '.') ?>đ</td>
                            <td style="text-align:right;"><?= $inv['discount'] > 0 ? '-' . number_format($inv['discount'], 0, ',', '.') . 'đ' : '-' ?></td>
                            <td style="text-align:right; font-weight:700; color: #1e3c72;">
                                <?= number_format($inv['patient_payment'] > 0 ? $inv['patient_payment'] : $inv['final_amount'], 0, ',', '.') ?>đ
                            </td>
                            <td>
                                <?php 
                                $methodLabels = ['cash'=>'Tiền mặt','card'=>'Thẻ','momo'=>'MoMo','vnpay'=>'VNPay','transfer'=>'Chuyển khoản'];
                                echo $methodLabels[$inv['payment_method']] ?? $inv['payment_method'];
                                ?>
                            </td>
                            <td>
                                <?php
                                $statusClasses = ['pending'=>'badge-pending','paid'=>'badge-confirmed','cancelled'=>'badge-cancelled'];
                                $statusLabels = ['pending'=>'Chờ TT','paid'=>'Đã TT','cancelled'=>'Đã hủy'];
                                ?>
                                <span class="badge-status <?= $statusClasses[$inv['status']] ?? '' ?>"><?= $statusLabels[$inv['status']] ?? $inv['status'] ?></span>
                            </td>
                            <td>
                                <a href="index.php?page=invoices&action=detail&id=<?= $inv['id'] ?>" class="btn btn-sm btn-outline-primary" style="border-radius:8px;">
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
<div class="modal fade" id="qrCameraModal" tabindex="-1" aria-labelledby="qrCameraModalLabel" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content" style="border-radius: 16px; border: none; box-shadow: 0 10px 30px rgba(0,0,0,0.15);">
            <div class="modal-header border-bottom-0 pb-0">
                <h5 class="modal-title fw-bold" id="qrCameraModalLabel"><i class="fa-solid fa-camera me-2 text-primary"></i>Quét mã QR từ Camera</h5>
                <button type="button" class="btn-close" id="btnStopQRCameraX" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center pt-2">
                <p class="text-muted small mb-3">Vui lòng căn chỉnh mã QR vào giữa khung hình bên dưới để quét.</p>
                <div class="qr-video-wrapper position-relative mx-auto rounded-3 overflow-hidden bg-black" style="width: 100%; max-width: 380px; aspect-ratio: 4/3; box-shadow: 0 4px 12px rgba(0,0,0,0.15);">
                    <video id="qrScanVideo" class="w-100 h-100" style="object-fit: cover;" autoplay playsinline></video>
                    <!-- Laser scanning guide animation -->
                    <div class="scan-laser-line" style="position: absolute; left: 0; right: 0; height: 3px; background: rgba(46, 204, 113, 0.8); box-shadow: 0 0 8px #2ecc71; animation: scanLaserAnim 2s infinite linear; top: 0;"></div>
                    <div class="scan-target-frame" style="position: absolute; width: 60%; height: 60%; border: 2px dashed rgba(255,255,255,0.7); left: 20%; top: 20%; pointer-events: none; border-radius: 8px;"></div>
                </div>
                <div class="mt-3">
                    <span id="qrCamStatus" class="badge bg-secondary p-2"><i class="fa-solid fa-spinner fa-spin me-2"></i>Đang khởi động camera...</span>
                </div>
            </div>
            <div class="modal-footer border-top-0 pt-0 justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal" id="btnCancelQRCamera" style="border-radius: 20px; font-weight: 500;">Hủy bỏ</button>
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
    const btnScan = document.getElementById('btnScanQRImage');
    const fileInput = document.getElementById('qrImageFileInput');
    
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
                            if (url.startsWith('http')) {
                                try {
                                    const scannedUrlObj = new URL(url);
                                    targetUrl = currentUrlObj.origin + scannedUrlObj.pathname + scannedUrlObj.search;
                                } catch(e) {
                                    console.error("Lỗi parse URL quét được: ", e);
                                }
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
    const btnScanWebcam = document.getElementById('btnScanQRWebcam');
    const qrCameraModal = document.getElementById('qrCameraModal');
    
    if (btnScanWebcam && qrCameraModal) {
        // Init Bootstrap modal instance
        const bsModal = new bootstrap.Modal(qrCameraModal);
        
        btnScanWebcam.addEventListener('click', function() {
            bsModal.show();
            startCamera();
        });
        
        // Listeners for closing modal to release camera
        document.getElementById('btnStopQRCameraX').addEventListener('click', stopCamera);
        document.getElementById('btnCancelQRCamera').addEventListener('click', stopCamera);
        qrCameraModal.addEventListener('hidden.bs.modal', stopCamera);
    }
    
    function startCamera() {
        const video = document.getElementById('qrScanVideo');
        const statusLabel = document.getElementById('qrCamStatus');
        
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
        const video = document.getElementById('qrScanVideo');
        if (video) video.srcObject = null;
    }

    function tick() {
        const video = document.getElementById('qrScanVideo');
        const statusLabel = document.getElementById('qrCamStatus');
        
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
                    if (url.startsWith('http')) {
                        try {
                            const scannedUrlObj = new URL(url);
                            targetUrl = currentUrlObj.origin + scannedUrlObj.pathname + scannedUrlObj.search;
                        } catch(e) {
                            console.error(e);
                        }
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
