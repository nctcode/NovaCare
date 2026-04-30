<!-- Trang Tóm tắt Bệnh án AI -->
<div class="row g-4 justify-content-center">
    <div class="col-lg-10">
        
        <!-- Header -->
        <div class="d-flex align-items-center mb-4" data-aos="fade-down">
            <a href="index.php?page=records" class="btn btn-outline-secondary" style="border-radius: 50%; width: 40px; height: 40px; display: flex; align-items: center; justify-content: center; border-color: var(--gray-200);">
                <i class="fa-solid fa-arrow-left"></i>
            </a>
            <div class="ms-3">
                <h4 class="m-0" style="color:var(--dark); font-weight:700;"><i class="fa-solid fa-brain me-2" style="color:var(--primary);"></i>AI Phân tích & Tóm tắt</h4>
                <p class="text-muted m-0 mt-1" style="font-size:14px;">
                    Hồ sơ Bệnh nhân: <strong style="color:var(--primary);"><?= htmlspecialchars($patient['name'] ?? 'N/A') ?></strong> 
                    <span class="mx-2">•</span> <i class="fa-solid fa-file-medical me-1"></i><?= count($records) ?> lần khám
                </p>
            </div>
        </div>

        <div id="aiErrorBox" class="alert alert-danger alert-custom alert-dismissible fade show" style="display:none;" data-aos="fade-in">
            <i class="fa-solid fa-triangle-exclamation"></i> <strong id="aiErrorTitle">Lỗi kết nối AI:</strong> <span id="aiErrorMsg"></span>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>

        <!-- AI Summary Card -->
        <div class="content-card mb-5" data-aos="fade-up" style="border: 1px solid rgba(102, 126, 234, 0.2); box-shadow: 0 10px 40px rgba(102, 126, 234, 0.08); background: linear-gradient(to bottom right, #ffffff, #f8faff);">
            <div class="card-header bg-transparent border-0 pt-4 pb-0 px-4 d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div id="aiIconBox" style="width:48px;height:48px;border-radius:14px;background:var(--gray-200);color:var(--gray-500);display:flex;align-items:center;justify-content:center;font-size:22px;transition:all 0.5s;">
                        <i class="fa-solid fa-wand-magic-sparkles"></i>
                    </div>
                    <div class="ms-3">
                        <h5 class="m-0" id="aiStatusTitle" style="color:var(--gray-600); font-weight:700;">Đang chuẩn bị dữ liệu...</h5>
                        <div style="font-size:12px; color:var(--gray-400);"><i class="fa-solid fa-clock me-1"></i><span id="aiTimeSpan"><?= date('H:i - d/m/Y') ?></span></div>
                    </div>
                </div>
            </div>
            
            <div class="card-body p-4">
                <!-- Skeleton Loader -->
                <div id="aiSkeletonLoader" style="display:block;">
                    <div class="d-flex align-items-center mb-3">
                        <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                        <span class="text-primary font-weight-bold">Robot AI đang đọc <?= count($records) ?> hồ sơ bệnh án và tổng hợp. Vui lòng đợi...</span>
                    </div>
                    <div class="skeleton-line" style="width: 100%; height: 16px; background: var(--gray-200); border-radius: 4px; margin-bottom: 10px; animation: pulse 1.5s infinite;"></div>
                    <div class="skeleton-line" style="width: 90%; height: 16px; background: var(--gray-200); border-radius: 4px; margin-bottom: 10px; animation: pulse 1.5s infinite 0.1s;"></div>
                    <div class="skeleton-line" style="width: 95%; height: 16px; background: var(--gray-200); border-radius: 4px; margin-bottom: 10px; animation: pulse 1.5s infinite 0.2s;"></div>
                    <div class="skeleton-line" style="width: 60%; height: 16px; background: var(--gray-200); border-radius: 4px; margin-bottom: 10px; animation: pulse 1.5s infinite 0.3s;"></div>
                </div>

                <!-- Rendered Content -->
                <div id="aiSummaryRendered" class="markdown-body" style="color: var(--text-dark); line-height: 1.8; font-size: 15px; display:none;">
                    <!-- Nội dung render bằng JS -->
                </div>
            </div>
            
            <div class="card-footer bg-transparent border-top p-3 text-center" style="border-color: rgba(0,0,0,0.05) !important;">
                <p class="m-0 text-muted" style="font-size:12px;"><i class="fa-solid fa-circle-info me-1"></i><strong>Lưu ý:</strong> Văn bản được tổng hợp hoàn toàn tự động bởi Trí tuệ nhân tạo. Vui lòng đối chiếu với hồ sơ gốc bên dưới để đảm bảo tính chính xác.</p>
            </div>
        </div>

        <!-- Danh sách bệnh án gốc -->
        <h5 class="mb-3 mt-4" style="font-weight:700; color:var(--gray-600);" data-aos="fade-up"><i class="fa-regular fa-folder-open me-2"></i>Dữ liệu gốc (<?= count($records) ?>)</h5>
        
        <div class="timeline medical-timeline" data-aos="fade-up" data-aos-delay="100">
            <?php foreach ($records as $i => $rec): ?>
            <div class="timeline-item position-relative mb-4" style="border-left:3px solid var(--gray-200); padding-left:25px; margin-left:15px;">
                <div class="timeline-icon position-absolute" style="left:-34px; top:0; width:25px; height:25px; background:var(--white); color:var(--gray-500); border-radius:50%; display:flex; align-items:center; justify-content:center; border:4px solid var(--gray-200); font-size:10px;"><i class="fa-solid fa-notes-medical"></i></div>
                
                <div class="card border-0 shadow-sm" style="border-radius:12px;">
                    <div class="card-header bg-white border-bottom-0 pt-3 pb-0">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="badge bg-light text-dark border"><i class="fa-regular fa-calendar me-1"></i><?= date('d/m/Y - H:i', strtotime($rec['created_at'])) ?></span>
                            <span class="text-muted" style="font-size:13px;"><i class="fa-solid fa-user-doctor me-1"></i>BS. <?= htmlspecialchars($rec['doctor_name'] ?? 'N/A') ?></span>
                        </div>
                    </div>
                    <div class="card-body">
                        <h6 style="color:var(--dark); font-weight:700; margin-bottom:8px;"><span class="text-danger">Chẩn đoán:</span> <?= htmlspecialchars($rec['diagnosis'] ?? '') ?></h6>
                        <div style="font-size:14px; color:var(--gray-600); background:#f8fafc; padding:12px; border-radius:8px; border-left:3px solid var(--success);">
                            <strong style="color:var(--success); display:block; margin-bottom:5px; font-size:13px;">Hướng điều trị:</strong>
                            <?= nl2br(htmlspecialchars($rec['treatment'] ?? 'Không có ghi chú điều trị.')) ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

    </div>
</div>

<style>
/* Animation cho Skeleton */
@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

/* Markdown Styles cho AI Summary */
.markdown-body h1, .markdown-body h2, .markdown-body h3, .markdown-body h4 {
    color: var(--primary-dark);
    font-weight: 700;
    margin-top: 1.5rem;
    margin-bottom: 1rem;
}
.markdown-body h3 { font-size: 1.2rem; }
.markdown-body p { margin-bottom: 1rem; }
.markdown-body ul, .markdown-body ol { margin-bottom: 1rem; padding-left: 1.5rem; }
.markdown-body li { margin-bottom: 0.5rem; }
.markdown-body strong { color: var(--dark); font-weight: 700; }
.markdown-body blockquote {
    border-left: 4px solid var(--primary-light);
    padding-left: 1rem;
    color: var(--gray-600);
    background: rgba(102, 126, 234, 0.05);
    padding: 10px 15px;
    border-radius: 0 8px 8px 0;
}
</style>

<!-- Thêm Marked.js để render Markdown từ AI -->
<script src="https://cdn.jsdelivr.net/npm/marked/marked.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const patientId = <?= intval($patientId) ?>;
    
    // Bắt đầu gọi AJAX để lấy tóm tắt
    fetch('index.php?page=records&action=summarize&patient_id=' + patientId + '&ajax=1')
        .then(response => response.json())
        .then(data => {
            document.getElementById('aiSkeletonLoader').style.display = 'none';
            
            if (data.success) {
                // Đổi UI sang trạng thái hoàn thành
                const iconBox = document.getElementById('aiIconBox');
                iconBox.style.background = 'linear-gradient(135deg, var(--primary), var(--primary-dark))';
                iconBox.style.color = 'white';
                iconBox.style.boxShadow = '0 4px 15px rgba(102,126,234,0.3)';
                
                const title = document.getElementById('aiStatusTitle');
                title.style.color = 'var(--primary)';
                title.textContent = 'Kết quả tóm tắt' + (data.fallback ? ' (Chế độ dự phòng)' : '');
                
                const renderedEl = document.getElementById('aiSummaryRendered');
                renderedEl.innerHTML = marked.parse(data.summary);
                renderedEl.style.display = 'block';
            } else {
                // Hiển thị lỗi
                const errBox = document.getElementById('aiErrorBox');
                document.getElementById('aiErrorMsg').textContent = data.error;
                errBox.style.display = 'block';
                document.getElementById('aiStatusTitle').textContent = 'Quá trình bị lỗi';
            }
        })
        .catch(err => {
            console.error('Fetch error:', err);
            document.getElementById('aiSkeletonLoader').style.display = 'none';
            const errBox = document.getElementById('aiErrorBox');
            document.getElementById('aiErrorMsg').textContent = 'Lỗi kết nối mạng hoặc máy chủ AI không phản hồi kịp (Timeout).';
            errBox.style.display = 'block';
            document.getElementById('aiStatusTitle').textContent = 'Quá trình bị lỗi';
        });
});
</script>
