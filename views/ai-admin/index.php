<?php
/**
 * Admin AI Chatbot View - Giao diện dành cho Quản trị viên
 */
?>

<div class="row h-100">
    <div class="col-12 h-100 d-flex flex-column">
        <div class="card flex-grow-1 shadow-sm border-0 d-flex flex-column" style="height: 80vh;">
            <div class="card-header bg-primary text-white d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="fa-solid fa-brain me-2"></i>AI Quản trị & Vận hành</h5>
                <div>
                    <span class="badge bg-light text-primary me-2"><i class="fa-solid fa-circle-check text-success"></i> Đã kết nối</span>
                    <button class="btn btn-sm btn-outline-light" onclick="clearAdminHistory()" title="Xóa lịch sử chat">
                        <i class="fa-solid fa-trash-can"></i> Mới
                    </button>
                </div>
            </div>
            
            <div class="card-body d-flex flex-column p-0 bg-light" style="overflow: hidden;">
                <!-- Chat Window -->
                <div class="chat-window flex-grow-1 p-3" id="adminChatWindow" style="overflow-y: auto;">
                    <?php if (empty($chatHistory)): ?>
                        <div class="text-center mt-5">
                            <div class="mb-3">
                                <i class="fa-solid fa-robot text-primary" style="font-size: 4rem; opacity: 0.2;"></i>
                            </div>
                            <h5 class="text-muted">Xin chào Quản trị viên!</h5>
                            <p class="text-muted">Tôi là Trợ lý AI phân tích và quản trị hệ thống. Hãy hỏi tôi về:</p>
                            
                            <div class="d-flex flex-wrap justify-content-center gap-2 mt-4 max-w-600 mx-auto">
                                <button class="btn btn-outline-primary btn-sm rounded-pill" onclick="sendAdminPrompt('Phân tích cách tối ưu hóa lịch trực của y tá để tránh quá tải.')">
                                    <i class="fa-solid fa-clock me-1"></i> Tối ưu lịch trực
                                </button>
                                <button class="btn btn-outline-primary btn-sm rounded-pill" onclick="sendAdminPrompt('Đề xuất chiến lược quản lý tồn kho vật tư y tế hiệu quả.')">
                                    <i class="fa-solid fa-box me-1"></i> Quản lý vật tư
                                </button>
                                <button class="btn btn-outline-primary btn-sm rounded-pill" onclick="sendAdminPrompt('Làm thế nào để giảm thời gian chờ đợi của bệnh nhân tại quầy lễ tân?')">
                                    <i class="fa-solid fa-users me-1"></i> Giảm thời gian chờ
                                </button>
                            </div>
                        </div>
                    <?php else: ?>
                        <?php foreach ($chatHistory as $msg): ?>
                            <?php if ($msg['role'] === 'user'): ?>
                                <div class="d-flex justify-content-end mb-3">
                                    <div class="bg-primary text-white p-3 rounded-3" style="max-width: 75%; border-bottom-right-radius: 0;">
                                        <?= nl2br(htmlspecialchars($msg['text'])) ?>
                                    </div>
                                    <div class="ms-2">
                                        <div class="avatar-circle bg-secondary text-white" style="width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;">AD</div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="d-flex justify-content-start mb-3">
                                    <div class="me-2">
                                        <div class="avatar-circle bg-white text-primary border border-primary" style="width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;">
                                            <i class="fa-solid fa-robot"></i>
                                        </div>
                                    </div>
                                    <div class="bg-white border p-3 rounded-3 shadow-sm" style="max-width: 85%; border-bottom-left-radius: 0;">
                                        <!-- Model AI response could be markdown, so we just use nl2br and htmlspecialchars for safety, though a real markdown parser is better -->
                                        <div class="ai-response-content">
                                            <?= nl2br(htmlspecialchars($msg['text'])) ?>
                                        </div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <!-- Input Form -->
                <div class="card-footer bg-white border-top p-3">
                    <form id="adminChatForm" onsubmit="handleAdminChatSubmit(event)">
                        <div class="input-group">
                            <textarea class="form-control" id="adminMessage" name="message" rows="2" placeholder="Nhập câu hỏi hoặc yêu cầu phân tích..." style="resize: none;" required></textarea>
                            <button class="btn btn-primary px-4" type="submit" id="btnAdminSend">
                                <i class="fa-solid fa-paper-plane me-1"></i> Gửi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    const chatWindow = document.getElementById('adminChatWindow');
    const chatForm = document.getElementById('adminChatForm');
    const msgInput = document.getElementById('adminMessage');
    const btnSend = document.getElementById('btnAdminSend');

    // Scroll to bottom
    if(chatWindow) chatWindow.scrollTop = chatWindow.scrollHeight;

    // Handle Enter to submit (Shift+Enter for new line)
    msgInput.addEventListener('keydown', function(e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            if (this.value.trim() !== '') {
                chatForm.dispatchEvent(new Event('submit'));
            }
        }
    });

    function sendAdminPrompt(text) {
        msgInput.value = text;
        chatForm.dispatchEvent(new Event('submit'));
    }

    async function handleAdminChatSubmit(e) {
        e.preventDefault();
        const message = msgInput.value.trim();
        if (!message) return;

        // Add User Message to UI
        appendUserMessage(message);
        msgInput.value = '';
        msgInput.focus();
        
        // Disable input while loading
        setLoading(true);

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
                appendAIMessage(result.data);
            } else {
                appendAIMessage("❌ Lỗi: " + (result.error || "Không thể kết nối đến AI"));
            }
        } catch (err) {
            appendAIMessage("❌ Lỗi kết nối máy chủ: " + err.message);
        } finally {
            setLoading(false);
        }
    }

    function appendUserMessage(text) {
        const div = document.createElement('div');
        div.className = 'd-flex justify-content-end mb-3';
        
        // Escape HTML
        const escapedText = text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;").replace(/\n/g, "<br>");
        
        div.innerHTML = `
            <div class="bg-primary text-white p-3 rounded-3" style="max-width: 75%; border-bottom-right-radius: 0;">
                ${escapedText}
            </div>
            <div class="ms-2">
                <div class="avatar-circle bg-secondary text-white" style="width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;">AD</div>
            </div>
        `;
        chatWindow.appendChild(div);
        chatWindow.scrollTop = chatWindow.scrollHeight;

        // Remove empty state if exists
        const emptyState = chatWindow.querySelector('.text-center.mt-5');
        if (emptyState) emptyState.remove();
    }

    function appendAIMessage(text) {
        const div = document.createElement('div');
        div.className = 'd-flex justify-content-start mb-3';
        
        const escapedText = text.replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;").replace(/\n/g, "<br>");

        div.innerHTML = `
            <div class="me-2">
                <div class="avatar-circle bg-white text-primary border border-primary" style="width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;">
                    <i class="fa-solid fa-robot"></i>
                </div>
            </div>
            <div class="bg-white border p-3 rounded-3 shadow-sm" style="max-width: 85%; border-bottom-left-radius: 0;">
                <div class="ai-response-content">${escapedText}</div>
            </div>
        `;
        chatWindow.appendChild(div);
        chatWindow.scrollTop = chatWindow.scrollHeight;
    }

    function appendLoadingIndicator() {
        const div = document.createElement('div');
        div.id = 'aiLoadingIndicator';
        div.className = 'd-flex justify-content-start mb-3';
        div.innerHTML = `
            <div class="me-2">
                <div class="avatar-circle bg-white text-primary border border-primary" style="width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;">
                    <i class="fa-solid fa-robot"></i>
                </div>
            </div>
            <div class="bg-white border p-3 rounded-3 shadow-sm" style="max-width: 85%; border-bottom-left-radius: 0;">
                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div> Đang phân tích...
            </div>
        `;
        chatWindow.appendChild(div);
        chatWindow.scrollTop = chatWindow.scrollHeight;
    }

    function setLoading(isLoading) {
        msgInput.disabled = isLoading;
        btnSend.disabled = isLoading;
        if (isLoading) {
            btnSend.innerHTML = '<span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>';
            appendLoadingIndicator();
        } else {
            btnSend.innerHTML = '<i class="fa-solid fa-paper-plane me-1"></i> Gửi';
            const loadingInd = document.getElementById('aiLoadingIndicator');
            if(loadingInd) loadingInd.remove();
        }
    }

    function clearAdminHistory() {
        if(confirm('Bạn có chắc chắn muốn xóa toàn bộ phiên chat này?')) {
            window.location.href = 'index.php?page=ai-admin&action=clearHistory';
        }
    }
</script>

<style>
.ai-response-content strong { color: var(--primary); }
/* Custom scrollbar for chat */
#adminChatWindow::-webkit-scrollbar { width: 6px; }
#adminChatWindow::-webkit-scrollbar-track { background: transparent; }
#adminChatWindow::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.1); border-radius: 3px; }
#adminChatWindow::-webkit-scrollbar-thumb:hover { background: rgba(0,0,0,0.2); }
</style>
