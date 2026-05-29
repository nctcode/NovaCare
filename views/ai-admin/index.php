<!-- Executive AI Control Center Dashboard -->
<div class="row g-4 mb-4">
    <!-- Left panel: Live Hospital Data Context Feed -->
    <div class="col-lg-4" data-aos="fade-right">
        <div class="content-card p-4 border-0 shadow-sm h-100" style="border-radius: 16px; background: #ffffff;">
            <h6 class="fw-bold mb-3 text-dark d-flex align-items-center gap-2">
                <i class="fa-solid fa-database text-primary"></i> 
                Cơ sở dữ liệu thời gian thực
            </h6>
            <p class="text-muted mb-4" style="font-size: 12.5px; line-height: 1.5;">
                Dữ liệu thực tế dưới đây được trích xuất trực tiếp từ database của hệ thống và liên tục đồng bộ làm dữ liệu đầu vào cho AI phân tích.
            </p>

            <div class="d-flex flex-column gap-3">
                <!-- Personnel Stats -->
                <div class="p-3" style="background: var(--gray-50); border-radius: 12px; border: 1px solid var(--gray-100);">
                    <small class="text-muted d-block mb-2" style="font-weight: 600; font-size: 10px; letter-spacing: 0.5px;">CƠ CẤU NHÂN SỰ</small>
                    <div class="d-flex flex-wrap gap-2">
                        <span class="badge bg-white text-dark border px-2 py-1.5" style="font-size: 11px;">
                            <i class="fa-solid fa-user-doctor text-primary me-1"></i> Bác sĩ: <strong><?= $rolesCount['doctor'] ?? 0 ?></strong>
                        </span>
                        <span class="badge bg-white text-dark border px-2 py-1.5" style="font-size: 11px;">
                            <i class="fa-solid fa-user-nurse text-primary me-1"></i> Y tá: <strong><?= $rolesCount['nurse'] ?? 0 ?></strong>
                        </span>
                        <span class="badge bg-white text-dark border px-2 py-1.5" style="font-size: 11px;">
                            <i class="fa-solid fa-flask-vial text-primary me-1"></i> KTV: <strong><?= $rolesCount['technician'] ?? 0 ?></strong>
                        </span>
                        <span class="badge bg-white text-dark border px-2 py-1.5" style="font-size: 11px;">
                            <i class="fa-solid fa-address-book text-primary me-1"></i> Lễ tân: <strong><?= $rolesCount['receptionist'] ?? 0 ?></strong>
                        </span>
                        <span class="badge bg-white text-dark border px-2 py-1.5" style="font-size: 11px;">
                            <i class="fa-solid fa-pills text-primary me-1"></i> Dược sĩ: <strong><?= $rolesCount['pharmacist'] ?? 0 ?></strong>
                        </span>
                    </div>
                </div>

                <!-- Patient & Treatment Stats -->
                <div class="p-3" style="background: var(--gray-50); border-radius: 12px; border: 1px solid var(--gray-100);">
                    <small class="text-muted d-block mb-2" style="font-weight: 600; font-size: 10px; letter-spacing: 0.5px;">LƯU LƯỢNG BỆNH NHÂN</small>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="bg-white p-2 rounded border text-center">
                                <small class="text-secondary d-block" style="font-size: 11px;">Hồ sơ bệnh nhân</small>
                                <strong class="fs-6 text-dark"><?= $patientCount ?></strong>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-white p-2 rounded border text-center">
                                <small class="text-secondary d-block" style="font-size: 11px;">Lượt đặt khám</small>
                                <strong class="fs-6 text-dark"><?= $appointmentCount ?></strong>
                            </div>
                        </div>
                        <div class="col-12 mt-2">
                            <div class="bg-white p-2 rounded border text-center">
                                <small class="text-secondary d-block" style="font-size: 11px;">Đang điều trị nội trú</small>
                                <strong class="fs-6 text-dark"><?= $admissionCount ?> bệnh nhân</strong>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Supplies & Medical Devices Stats -->
                <div class="p-3" style="background: var(--gray-50); border-radius: 12px; border: 1px solid var(--gray-100);">
                    <small class="text-muted d-block mb-2" style="font-weight: 600; font-size: 10px; letter-spacing: 0.5px;">CƠ SỞ VẬT CHẤT & VẬT TƯ</small>
                    <div class="row g-2">
                        <div class="col-6">
                            <div class="bg-white p-2 rounded border text-center">
                                <small class="text-secondary d-block" style="font-size: 11px;">Tổng máy móc</small>
                                <strong class="fs-6 text-dark"><?= $deviceStats['total'] ?? 0 ?> máy</strong>
                                <span class="d-block text-danger" style="font-size: 9.5px; font-weight: 500;">Bảo trì: <?= $deviceStats['maint'] ?? 0 ?></span>
                            </div>
                        </div>
                        <div class="col-6">
                            <div class="bg-white p-2 rounded border text-center">
                                <small class="text-secondary d-block" style="font-size: 11px;">Danh mục vật tư</small>
                                <strong class="fs-6 text-dark"><?= $equipmentStats['total'] ?? 0 ?> loại</strong>
                                <span class="d-block text-success" style="font-size: 9.5px; font-weight: 500;">SL: <?= $equipmentStats['qty'] ?? 0 ?> cái</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Right panel: Interactive AI Commands -->
    <div class="col-lg-8" data-aos="fade-left">
        <div class="content-card p-4 border-0 shadow-sm h-100" style="border-radius: 16px; background: #ffffff;">
            <div class="d-flex align-items-center justify-content-between mb-3">
                <h6 class="fw-bold m-0 text-dark d-flex align-items-center gap-2">
                    <i class="fa-solid fa-robot text-primary"></i> 
                    Trung tâm Điều hành Phân tích AI
                </h6>
                <span class="badge bg-success-subtle text-success border border-success-subtle px-2.5 py-1" style="font-size: 11px; border-radius: 8px;">
                    <i class="fa-solid fa-circle-check me-1"></i> AI Sẵn sàng
                </span>
            </div>
            <p class="text-muted mb-4" style="font-size: 12.5px;">
                Chọn một trong các danh mục nghiệp vụ quản trị bên dưới. AI sẽ tự động phân tích cơ sở dữ liệu hiện có và phản hồi một văn bản báo cáo vận hành chuyên nghiệp gửi riêng cho Admin.
            </p>

            <div class="row g-3">
                <div class="col-md-6">
                    <button onclick="triggerAIAnalysis('operations')" class="w-100 text-start p-3 border rounded-3 bg-light-hover transition-all d-flex align-items-start gap-3 btn-ai-action" style="background: var(--gray-50); outline: none; border-color: var(--gray-200);">
                        <div class="p-2.5 bg-primary-subtle text-primary rounded-3">
                            <i class="fa-solid fa-chart-line" style="font-size: 18px;"></i>
                        </div>
                        <div>
                            <strong class="text-dark d-block" style="font-size: 13.5px;">Báo cáo vận hành hàng ngày</strong>
                            <small class="text-muted d-block mt-0.5" style="font-size: 11px; line-height: 1.4;">Chấm điểm sức khỏe bệnh viện, phân tích hiệu suất nhân lực & tối ưu.</small>
                        </div>
                    </button>
                </div>

                <div class="col-md-6">
                    <button onclick="triggerAIAnalysis('influx')" class="w-100 text-start p-3 border rounded-3 bg-light-hover transition-all d-flex align-items-start gap-3 btn-ai-action" style="background: var(--gray-50); outline: none; border-color: var(--gray-200);">
                        <div class="p-2.5 bg-success-subtle text-success rounded-3">
                            <i class="fa-solid fa-calendar-check" style="font-size: 18px;"></i>
                        </div>
                        <div>
                            <strong class="text-dark d-block" style="font-size: 13.5px;">Dự báo quá tải & Lịch trực</strong>
                            <small class="text-muted d-block mt-0.5" style="font-size: 11px; line-height: 1.4;">Đánh giá lượt đặt lịch khám, phân bổ ca trực Bác sĩ/Y tá tối ưu.</small>
                        </div>
                    </button>
                </div>

                <div class="col-md-6">
                    <button onclick="triggerAIAnalysis('supplies')" class="w-100 text-start p-3 border rounded-3 bg-light-hover transition-all d-flex align-items-start gap-3 btn-ai-action" style="background: var(--gray-50); outline: none; border-color: var(--gray-200);">
                        <div class="p-2.5 bg-warning-subtle text-warning rounded-3" style="color: #d97706 !important;">
                            <i class="fa-solid fa-boxes-packing" style="font-size: 18px;"></i>
                        </div>
                        <div>
                            <strong class="text-dark d-block" style="font-size: 13.5px;">Kiểm toán Vật tư & Thiết bị</strong>
                            <small class="text-muted d-block mt-0.5" style="font-size: 11px; line-height: 1.4;">Cảnh báo hết hạn hoặc thiếu hụt vật tư, đề xuất mua sắm và bảo trì máy móc.</small>
                        </div>
                    </button>
                </div>

                <div class="col-md-6">
                    <button onclick="triggerAIAnalysis('security')" class="w-100 text-start p-3 border rounded-3 bg-light-hover transition-all d-flex align-items-start gap-3 btn-ai-action" style="background: var(--gray-50); outline: none; border-color: var(--gray-200);">
                        <div class="p-2.5 bg-danger-subtle text-danger rounded-3">
                            <i class="fa-solid fa-shield-halved" style="font-size: 18px;"></i>
                        </div>
                        <div>
                            <strong class="text-dark d-block" style="font-size: 13.5px;">Đánh giá Bảo mật & Audit Logs</strong>
                            <small class="text-muted d-block mt-0.5" style="font-size: 11px; line-height: 1.4;">Phân tích các hoạt động mới nhất trên hệ thống và cảnh báo bất thường.</small>
                        </div>
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Dynamic Executive AI Report Display -->
<div class="row">
    <div class="col-12" data-aos="fade-up">
        <div class="content-card border-0 shadow-sm" style="border-radius: 16px; background: #ffffff; min-height: 400px;">
            <!-- Header of report area -->
            <div class="card-header bg-white border-bottom p-4 d-flex justify-content-between align-items-center" style="border-top-left-radius: 16px; border-top-right-radius: 16px;">
                <div class="d-flex align-items-center gap-2">
                    <div style="width: 10px; height: 10px; border-radius: 50%; background-color: var(--primary);"></div>
                    <h5 class="fw-bold m-0 text-dark" id="reportTitle">Báo cáo Phân tích Chiến lược & Quyết định</h5>
                </div>
                <div id="actionLoadingIndicator" style="display: none;">
                    <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                    <span class="text-secondary" style="font-size: 12.5px; font-weight: 500;">AI đang lập báo cáo...</span>
                </div>
            </div>

            <!-- Body: Document Report Area -->
            <div class="card-body p-4" id="reportArea">
                <!-- Initial Welcome Screen inside report area -->
                <div id="initialStateReport" class="text-center py-5">
                    <div class="mb-4">
                        <i class="fa-solid fa-file-invoice text-muted" style="font-size: 5rem; opacity: 0.15;"></i>
                    </div>
                    <h5 class="fw-bold text-dark">Chưa có báo cáo nào được khởi tạo</h5>
                    <p class="text-secondary mx-auto" style="max-width: 500px; font-size: 13px; line-height: 1.6;">
                        Hãy bấm chọn một trong các nút phân tích nghiệp vụ vận hành ở bảng phía trên, hoặc gửi yêu cầu phân tích dữ liệu tùy chỉnh bên dưới để AI lập báo cáo chi tiết.
                    </p>
                </div>

                <!-- Container where AI report html is rendered -->
                <div id="aiGeneratedReport" style="display: none;">
                    <!-- Beautiful custom corporate header styled like a real paper -->
                    <div class="p-4 mb-4" style="background: linear-gradient(135deg, #f8fafc, #f1f5f9); border-radius: 12px; border-left: 5px solid var(--primary);">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <small class="text-primary fw-bold" style="font-size: 11px; letter-spacing: 1px;">NOVACARE AI DECISION SUPPORT SYSTEM</small>
                                <h4 class="fw-bold text-dark m-0 mt-1" id="reportDocumentType">Báo cáo Phân tích Vận hành</h4>
                            </div>
                            <span class="badge bg-light text-secondary border px-2 py-1.5" style="font-size: 11px;" id="reportTimestamp">Time: 28/05/2026</span>
                        </div>
                        <p class="text-muted m-0" style="font-size: 12px;">Được biên soạn tự động dựa trên phân tích dữ liệu thực tế tại thời điểm truy vấn.</p>
                    </div>

                    <!-- AI Content Output -->
                    <div class="ai-report-body-text px-2" id="aiReportContentText" style="line-height: 1.7; font-size: 14px; color: var(--gray-700);">
                        <!-- Rendered Markdown goes here -->
                    </div>
                </div>
            </div>

            <!-- Custom Command Input at the bottom of the card -->
            <div class="card-footer bg-light p-4" style="border-bottom-left-radius: 16px; border-bottom-right-radius: 16px; border-top: 1px solid var(--gray-100);">
                <h6 class="fw-bold text-dark mb-2.5" style="font-size: 13.5px;"><i class="fa-solid fa-terminal text-primary me-2"></i>Đặt yêu cầu phân tích tùy chỉnh với AI</h6>
                <form id="customCommandForm" onsubmit="handleCustomCommandSubmit(event)">
                    <div class="input-group">
                        <textarea class="form-control py-2.5 px-3 border border-end-0" id="customPrompt" rows="1" placeholder="Ví dụ: Lập kế hoạch phân công trực cho khoa Nhi dựa trên số lượt khám hoặc phân tích bảo mật hệ thống..." style="border-radius: 10px 0 0 10px; font-size: 13px; resize: none; box-shadow: none; outline: none;"></textarea>
                        <button class="btn btn-primary px-4 d-flex align-items-center gap-2" type="submit" id="btnSubmitCustom" style="border-radius: 0 10px 10px 0; font-weight: 600;">
                            <i class="fa-solid fa-circle-play"></i> Phân tích
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
async function triggerAIAnalysis(actionType) {
    let prompt = '';
    let reportDocType = '';
    
    switch (actionType) {
        case 'operations':
            prompt = "Hãy lập báo cáo phân tích toàn diện tình hình vận hành của bệnh viện hôm nay, chấm điểm hiệu suất (Health Score) dựa trên tổng số lượng bác sĩ, y tá, kỹ thuật viên, bệnh nhân, lượt khám hiện tại và đề xuất các việc cần tối ưu gấp.";
            reportDocType = "BÁO CÁO PHÂN TÍCH VẬN HÀNH TOÀN DIỆN & HEALTH SCORE";
            break;
        case 'influx':
            prompt = "Dựa vào số liệu bác sĩ, y tá, kỹ thuật viên và lượt đặt khám hiện tại, hãy dự báo lượng bệnh nhân và đề xuất phương án phân bổ ca trực tối ưu cho các khoa để tránh quá tải.";
            reportDocType = "DỰ BÁO LƯỢNG BỆNH NHÂN & ĐỀ XUẤT PHÂN BỔ CA TRỰC";
            break;
        case 'supplies':
            prompt = "Phân tích tình hình máy móc y tế và số lượng vật tư tồn kho hiện tại, lọc ra các vật tư cảnh báo sắp hết và thiết bị cần bảo trì, đưa ra kế hoạch mua sắm/sửa chữa.";
            reportDocType = "BÁO CÁO KIỂM TOÁN VẬT TƯ & BẢO TRÌ THIẾT BỊ Y TẾ";
            break;
        case 'security':
            prompt = "Kiểm tra danh sách nhật ký hoạt động hệ thống (Audit Logs) mới nhất được trích xuất từ database, đánh giá mức độ an toàn thông tin và phát hiện các hành vi đáng ngờ hoặc rủi ro vận hành.";
            reportDocType = "BÁO CÁO ĐÁNH GIÁ BẢO MẬT & KIỂM TOÁN NHẬT KÝ HỆ THỐNG";
            break;
    }

    await submitToAI(prompt, reportDocType);
}

async function handleCustomCommandSubmit(e) {
    e.preventDefault();
    const promptInput = document.getElementById('customPrompt');
    const prompt = promptInput.value.trim();
    if (!prompt) return;

    promptInput.value = '';
    await submitToAI(prompt, "BÁO CÁO PHÂN TÍCH THEO YÊU CẦU TÙY CHỈNH");
}

async function submitToAI(message, reportDocType) {
    // Show Loading
    document.getElementById('actionLoadingIndicator').style.display = 'block';
    document.getElementById('initialStateReport').style.display = 'none';
    
    const reportContainer = document.getElementById('aiGeneratedReport');
    const reportTextContainer = document.getElementById('aiReportContentText');
    const docTypeLabel = document.getElementById('reportDocumentType');
    const timestampLabel = document.getElementById('reportTimestamp');
    
    // Disable inputs
    const btnSubmit = document.getElementById('btnSubmitCustom');
    const promptInput = document.getElementById('customPrompt');
    btnSubmit.disabled = true;
    promptInput.disabled = true;

    // Scroll to report area smoothly
    document.getElementById('reportTitle').scrollIntoView({ behavior: 'smooth' });

    try {
        const response = await fetch('index.php?page=ai-admin&action=chat', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ message: message })
        });

        const result = await response.json();

        if (result.success) {
            // Update labels
            docTypeLabel.textContent = reportDocType;
            const now = new Date();
            timestampLabel.textContent = "Ngày lập: " + now.toLocaleDateString('vi-VN') + " " + now.toLocaleTimeString('vi-VN');
            
            // Parse Markdown response
            reportTextContainer.innerHTML = parseMarkdown(result.data);
            
            // Show Report
            reportContainer.style.display = 'block';
        } else {
            reportTextContainer.innerHTML = `<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i>Lỗi phân tích: ${result.error || "Không có phản hồi"}</div>`;
            reportContainer.style.display = 'block';
        }
    } catch (err) {
        reportTextContainer.innerHTML = `<div class="alert alert-danger"><i class="fa-solid fa-triangle-exclamation me-2"></i>Lỗi kết nối đến máy chủ: ${err.message}</div>`;
        reportContainer.style.display = 'block';
    } finally {
        // Hide Loading & Enable inputs
        document.getElementById('actionLoadingIndicator').style.display = 'none';
        btnSubmit.disabled = false;
        promptInput.disabled = false;
    }
}

/**
 * Basic Markdown to HTML parser
 */
function parseMarkdown(text) {
    if (!text) return "";
    
    // Escape HTML first for safety
    let html = text
        .replace(/&/g, "&amp;")
        .replace(/</g, "&lt;")
        .replace(/>/g, "&gt;");
    
    // Convert Headers
    html = html.replace(/^### (.*?)$/gm, '<h6 class="fw-bold mt-4 mb-2 text-primary" style="font-size: 15px;"><i class="fa-solid fa-chart-bar text-primary me-2"></i>$1</h6>');
    html = html.replace(/^## (.*?)$/gm, '<h5 class="fw-bold mt-4 mb-3 text-dark border-bottom pb-2" style="font-size: 17px;"><i class="fa-solid fa-clipboard-list text-primary me-2"></i>$1</h5>');
    html = html.replace(/^# (.*?)$/gm, '<h4 class="fw-bold mt-4 mb-3 text-dark" style="font-size: 19px;">$1</h4>');
    
    // Bold tags
    html = html.replace(/\*\*(.*?)\*\*/g, '<strong class="text-dark">$1</strong>');
    
    // Bullet Lists (replace single lines with li)
    html = html.replace(/^\- (.*?)$/gm, '<li class="mb-2 text-secondary" style="font-size: 13.5px; margin-left: 15px; list-style-type: square;">$1</li>');
    
    // Code inline tags
    html = html.replace(/`(.*?)`/g, '<code class="bg-light px-2 py-0.5 rounded text-danger" style="font-size: 12.5px; font-weight: 500; border: 1px solid var(--gray-200);">$1</code>');
    
    // Convert newlines to breaks
    html = html.replace(/\n/g, '<br>');

    // Clean up empty lines and multiple breaks
    html = html.replace(/(<br>\s*){3,}/g, '<br><br>');
    
    return html;
}
</script>

<style>
.btn-ai-action {
    transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
    border: 1px solid var(--gray-200) !important;
}
.btn-ai-action:hover {
    transform: translateY(-3px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.06);
    background-color: var(--white) !important;
    border-color: var(--primary) !important;
}
.ai-report-body-text li strong {
    color: var(--dark) !important;
}
</style>
