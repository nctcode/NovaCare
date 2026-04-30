/**
 * NovaCare AI Chat - JavaScript xử lý chat AJAX real-time
 * 
 * Gửi tin nhắn đến backend (AIAssistantController::chat) qua AJAX,
 * hiển thị kết quả trực tiếp trên giao diện mà không reload trang.
 */

document.addEventListener('DOMContentLoaded', function () {

    // ── DOM Elements ──
    const chatBody     = document.getElementById('aiChatBody');
    const chatInput    = document.getElementById('aiChatInput');
    const sendBtn      = document.getElementById('aiSendBtn');
    const chatForm     = document.getElementById('aiChatForm');
    const clearBtn     = document.getElementById('aiClearBtn');
    const suggestBtns  = document.querySelectorAll('.ai-suggest-btn');
    const welcomeEl    = document.getElementById('aiWelcome');

    if (!chatBody || !chatInput || !chatForm) return;

    let isProcessing = false;

    // ── Gửi tin nhắn ──
    chatForm.addEventListener('submit', function (e) {
        e.preventDefault();
        const message = chatInput.value.trim();
        if (!message || isProcessing) return;
        sendMessage(message);
    });

    // ── Quick Suggestion ──
    suggestBtns.forEach(btn => {
        btn.addEventListener('click', function () {
            const text = this.dataset.text || this.textContent.trim();
            if (!isProcessing) sendMessage(text);
        });
    });

    // ── Clear History ──
    if (clearBtn) {
        clearBtn.addEventListener('click', function () {
            if (confirm('Bạn muốn bắt đầu cuộc hội thoại mới?')) {
                fetch('index.php?page=ai-assistant&action=clearHistory&ajax=1')
                    .then(res => res.json())
                    .then(() => {
                        // Xóa tất cả tin nhắn trên giao diện
                        chatBody.innerHTML = '';
                        if (welcomeEl) {
                            chatBody.appendChild(welcomeEl);
                            welcomeEl.style.display = 'flex';
                        }
                        // Hiện lại suggestions
                        const suggestionsEl = document.getElementById('aiSuggestions');
                        if (suggestionsEl) suggestionsEl.style.display = 'flex';
                    })
                    .catch(err => console.error('Error clearing history:', err));
            }
        });
    }

    // ── Phím Enter để gửi ──
    chatInput.addEventListener('keydown', function (e) {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            chatForm.dispatchEvent(new Event('submit'));
        }
    });

    /**
     * Gửi tin nhắn chính
     */
    function sendMessage(message) {
        isProcessing = true;
        sendBtn.disabled = true;

        // Ẩn welcome screen
        if (welcomeEl) welcomeEl.style.display = 'none';

        // Ẩn suggestions sau khi gửi tin đầu tiên
        const suggestionsEl = document.getElementById('aiSuggestions');
        if (suggestionsEl) suggestionsEl.style.display = 'none';

        // Hiển thị tin nhắn user
        appendMessage('user', message);
        chatInput.value = '';
        chatInput.focus();

        // Hiển thị typing indicator
        const typingEl = showTypingIndicator();

        // Gửi AJAX request
        fetch('index.php?page=ai-assistant&action=chat', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ message: message })
        })
        .then(res => {
            if (!res.ok) throw new Error('HTTP error ' + res.status);
            return res.json();
        })
        .then(data => {
            // Xóa typing indicator
            removeTypingIndicator(typingEl);

            if (data.success && data.data) {
                appendAIResponse(data.data, data.fallback || false);
            } else {
                appendMessage('bot', data.error || 'Đã xảy ra lỗi, vui lòng thử lại.');
            }
        })
        .catch(err => {
            removeTypingIndicator(typingEl);
            appendMessage('bot', '⚠️ Không thể kết nối đến máy chủ AI. Vui lòng kiểm tra kết nối mạng và thử lại.');
            console.error('AI Chat Error:', err);
        })
        .finally(() => {
            isProcessing = false;
            sendBtn.disabled = false;
        });
    }

    /**
     * Thêm một tin nhắn đơn giản (user hoặc bot)
     */
    function appendMessage(role, text) {
        const now = new Date();
        const timeStr = now.getHours().toString().padStart(2, '0') + ':' + 
                        now.getMinutes().toString().padStart(2, '0');

        const msgEl = document.createElement('div');
        msgEl.className = `ai-msg ${role}`;

        const icon = role === 'user' ? 'fa-user' : 'fa-robot';

        msgEl.innerHTML = `
            <div class="ai-msg-avatar">
                <i class="fa-solid ${icon}"></i>
            </div>
            <div class="ai-msg-content">
                <div class="ai-msg-bubble">${escapeHtml(text)}</div>
                <div class="ai-msg-time">${timeStr}</div>
            </div>
        `;

        chatBody.appendChild(msgEl);
        scrollToBottom();
    }

    /**
     * Thêm phản hồi AI có cấu trúc (diagnosis card)
     */
    function appendAIResponse(data, isFallback) {
        const now = new Date();
        const timeStr = now.getHours().toString().padStart(2, '0') + ':' + 
                        now.getMinutes().toString().padStart(2, '0');

        const severity = data.severity || 'low';
        const severityLabels = { 
            low: '<i class="fa-solid fa-circle-check"></i> Nhẹ', 
            medium: '<i class="fa-solid fa-circle-exclamation"></i> Trung bình', 
            high: '<i class="fa-solid fa-triangle-exclamation"></i> Nghiêm trọng' 
        };

        let cardHtml = `
            <div class="ai-diagnosis-card">
                <div class="ai-diagnosis-title">
                    <i class="fa-solid fa-stethoscope"></i>
                    ${escapeHtml(data.condition || 'Phân tích')}
                </div>
                <div class="ai-diagnosis-advice">${escapeHtml(data.advice || '')}</div>
                <div class="ai-diagnosis-meta">
        `;

        if (data.department) {
            cardHtml += `<span class="ai-meta-badge ai-badge-dept"><i class="fa-solid fa-building-columns"></i> ${escapeHtml(data.department)}</span>`;
        }

        cardHtml += `<span class="ai-meta-badge ai-badge-severity severity-${severity}">${severityLabels[severity] || severity}</span>`;
        cardHtml += `</div>`;

        // Emergency alert
        if (severity === 'high') {
            cardHtml += `
                <div class="ai-emergency-alert">
                    <i class="fa-solid fa-phone me-1"></i> Đây có thể là tình huống khẩn cấp! Hãy gọi 115 hoặc đến cơ sở y tế gần nhất NGAY LẬP TỨC.
                </div>
            `;
        }

        // Follow up
        if (data.follow_up) {
            cardHtml += `
                <div class="ai-followup-box">
                    <i class="fa-solid fa-circle-question me-1"></i> ${escapeHtml(data.follow_up)}
                </div>
            `;
        }

        // Action button (book appointment)
        if (severity !== 'high') {
            cardHtml += `
                <a href="index.php?page=appointments&action=create" class="ai-action-btn">
                    <i class="fa-solid fa-calendar-plus"></i> Đặt lịch khám ${escapeHtml(data.department || '')}
                </a>
            `;
        }

        cardHtml += `</div>`;

        // Fallback notice
        if (isFallback) {
            cardHtml += `<div style="margin-top:8px;font-size:11px;color:#94a3b8;"><i class="fa-solid fa-info-circle me-1"></i>Phản hồi từ hệ thống cơ bản (AI chưa được kết nối)</div>`;
        }

        const msgEl = document.createElement('div');
        msgEl.className = 'ai-msg bot';
        msgEl.innerHTML = `
            <div class="ai-msg-avatar">
                <i class="fa-solid fa-robot"></i>
            </div>
            <div class="ai-msg-content">
                <div class="ai-msg-bubble">${cardHtml}</div>
                <div class="ai-msg-time">${timeStr}</div>
            </div>
        `;

        chatBody.appendChild(msgEl);
        scrollToBottom();
    }

    /**
     * Hiển thị typing indicator (3 chấm nhảy)
     */
    function showTypingIndicator() {
        const el = document.createElement('div');
        el.className = 'ai-typing';
        el.id = 'aiTypingIndicator';
        el.innerHTML = `
            <div class="ai-msg-avatar" style="background:linear-gradient(135deg,#667eea,#764ba2);color:#fff;width:38px;height:38px;border-radius:12px;display:flex;align-items:center;justify-content:center;">
                <i class="fa-solid fa-robot"></i>
            </div>
            <div class="ai-typing-dots">
                <span></span><span></span><span></span>
            </div>
        `;
        chatBody.appendChild(el);
        scrollToBottom();
        return el;
    }

    function removeTypingIndicator(el) {
        if (el && el.parentNode) el.parentNode.removeChild(el);
    }

    /**
     * Scroll xuống bottom của chat
     */
    function scrollToBottom() {
        requestAnimationFrame(() => {
            chatBody.scrollTop = chatBody.scrollHeight;
        });
    }

    /**
     * Escape HTML để tránh XSS
     */
    function escapeHtml(text) {
        if (!text) return '';
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // ── Auto-focus input ──
    chatInput.focus();
});
