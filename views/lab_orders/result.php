<!-- Form Nhập Kết quả CLS (KTV) -->
<div class="content-card" style="max-width: 750px;">
    <div class="card-header">
        <h5><i class="fa-solid fa-microscope me-2"></i>Nhập Kết quả CLS</h5>
        <a href="index.php?page=lab-orders" class="btn-action btn-edit">
            <i class="fa-solid fa-arrow-left"></i> Quay lại
        </a>
    </div>
    <div class="card-body">
        <!-- Thông tin chỉ định -->
        <div class="row mb-4 p-3 rounded" style="background: var(--bg-secondary, #f8f9fa);">
            <div class="col-md-6">
                <p class="mb-1"><strong>Bệnh nhân:</strong> <?= htmlspecialchars($order['patient_name']) ?></p>
                <p class="mb-1"><strong>SĐT:</strong> <?= htmlspecialchars($order['patient_phone'] ?? 'N/A') ?></p>
                <?php if (!empty($order['date_of_birth'])): ?>
                <p class="mb-0"><strong>Ngày sinh:</strong> <?= date('d/m/Y', strtotime($order['date_of_birth'])) ?></p>
                <?php endif; ?>
            </div>
            <div class="col-md-6">
                <p class="mb-1"><strong>Loại:</strong> 
                    <?= $order['order_type'] === 'lab_test' ? '🧪 Xét nghiệm' : '📷 CĐHA' ?>
                </p>
                <p class="mb-1"><strong>Tên:</strong> <?= htmlspecialchars($order['test_name']) ?></p>
                <p class="mb-0"><strong>BS chỉ định:</strong> <?= htmlspecialchars($order['doctor_name']) ?></p>
            </div>
            <?php if (!empty($order['notes'])): ?>
            <div class="col-12 mt-2">
                <p class="mb-0 text-muted"><i class="fa-solid fa-note-sticky me-1"></i><strong>Ghi chú:</strong> <?= htmlspecialchars($order['notes']) ?></p>
            </div>
            <?php endif; ?>
        </div>

        <form method="POST" action="index.php?page=lab-orders&action=saveResult" enctype="multipart/form-data">
            <?= Security::csrfField() ?>
            <input type="hidden" name="lab_order_id" value="<?= $order['id'] ?>">

            <div class="mb-4 d-flex justify-content-between align-items-center bg-light p-3 rounded border">
                <div>
                    <span class="fw-semibold"><i class="fa-solid fa-robot text-primary me-2"></i>Tính năng Bệnh viện 4.0</span>
                    <div class="small text-muted">Sử dụng AI thông minh để phân tích chỉ định và tự động điền các trường kết quả</div>
                </div>
                <button type="button" id="btnAnalyzeAI" class="btn btn-primary btn-sm" style="border-radius:20px; font-weight: 500;">
                    <i class="fa-solid fa-wand-magic-sparkles me-1"></i> Trợ lý AI Phân tích CLS
                </button>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Tải lên hình ảnh kết quả (X-quang, MRI, Phiếu kết quả...)</label>
                <input type="file" name="result_image" class="form-control" accept="image/*,application/pdf">
                <div class="form-text">Định dạng hỗ trợ: JPG, PNG, PDF. Tối đa 5MB.</div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Kết quả chi tiết <span class="text-danger">*</span></label>
                <textarea name="result_text" id="result_text" class="form-control" rows="5" placeholder="Mô tả kết quả xét nghiệm / chẩn đoán hình ảnh..." required></textarea>
            </div>

            <div class="row g-3 mb-3">
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Giá trị đo được</label>
                    <input type="text" name="result_value" id="result_value" class="form-control" placeholder="VD: 7.2, Dương tính">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Khoảng bình thường</label>
                    <input type="text" name="normal_range" id="normal_range" class="form-control" placeholder="VD: 4.0 - 10.0">
                </div>
                <div class="col-md-4">
                    <label class="form-label fw-semibold">Đơn vị</label>
                    <input type="text" name="unit" id="unit" class="form-control" placeholder="VD: mmol/L, mg/dL">
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label fw-semibold">Kết luận <span class="text-danger">*</span></label>
                <select name="conclusion" id="conclusion" class="form-select" required>
                    <option value="normal">✅ Bình thường</option>
                    <option value="abnormal">⚠️ Bất thường</option>
                    <option value="critical">🔴 Nguy hiểm</option>
                </select>
            </div>

            <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                <a href="index.php?page=lab-orders" class="btn btn-outline-secondary px-4" style="border-radius:20px">Hủy</a>
                <button type="submit" class="btn btn-success px-4" style="border-radius:20px">
                    <i class="fa-solid fa-check-circle me-2"></i>Lưu kết quả
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const btnAnalyzeAI = document.getElementById('btnAnalyzeAI');
    const resultText = document.getElementById('result_text');
    const resultValue = document.getElementById('result_value');
    const normalRange = document.getElementById('normal_range');
    const unit = document.getElementById('unit');
    const conclusion = document.getElementById('conclusion');

    btnAnalyzeAI.addEventListener('click', function() {
        // Show loading state
        const originalHtml = btnAnalyzeAI.innerHTML;
        btnAnalyzeAI.disabled = true;
        btnAnalyzeAI.innerHTML = '<span class="spinner-border spinner-border-sm me-1" role="status" aria-hidden="true"></span> Đang phân tích...';

        const testName = <?= json_encode($order['test_name']) ?>;
        const notes = <?= json_encode($order['notes'] ?? '') ?>;

        fetch('index.php?page=lab-orders&action=analyzeAI', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                test_name: testName,
                notes: notes
            })
        })
        .then(response => response.json())
        .then(res => {
            if (res.success && res.data) {
                const data = res.data;
                
                // Typewriter effect or direct fill
                resultText.value = data.result_text || '';
                resultValue.value = data.result_value || '';
                normalRange.value = data.normal_range || '';
                unit.value = data.unit || '';
                
                if (data.conclusion) {
                    conclusion.value = data.conclusion;
                }
                
                // Visual feedback success
                btnAnalyzeAI.classList.replace('btn-primary', 'btn-success');
                btnAnalyzeAI.innerHTML = '<i class="fa-solid fa-check me-1"></i> Hoàn tất!';
                setTimeout(() => {
                    btnAnalyzeAI.classList.replace('btn-success', 'btn-primary');
                    btnAnalyzeAI.innerHTML = originalHtml;
                    btnAnalyzeAI.disabled = false;
                }, 2000);
            } else {
                alert('Có lỗi khi gọi trợ lý AI: ' + (res.error || 'Vui lòng thử lại.'));
                btnAnalyzeAI.innerHTML = originalHtml;
                btnAnalyzeAI.disabled = false;
            }
        })
        .catch(err => {
            console.error(err);
            alert('Không thể kết nối đến máy chủ AI.');
            btnAnalyzeAI.innerHTML = originalHtml;
            btnAnalyzeAI.disabled = false;
        });
    });
});
</script>
