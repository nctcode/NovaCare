<!-- Trợ lý AI y tế (AJAX Version) -->
<div class="row g-4 justify-content-center">
    <div class="col-lg-8" data-aos="fade-up">
        <div class="content-card" style="border:1px solid var(--gray-200); box-shadow:0 8px 30px rgba(0,0,0,0.06); display:flex; flex-direction:column; height: 600px;">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center" style="padding:20px 24px;">
                <div>
                    <h5 class="m-0" style="color:var(--primary);"><i class="fa-solid fa-robot me-2"></i>NovaCare AI Assistant</h5>
                    <p class="text-muted m-0 mt-1" style="font-size:13px;">Nhập triệu chứng của bạn, AI sẽ phân tích và gợi ý chuyên khoa phù hợp.</p>
                </div>
                <button type="button" id="aiClearBtn" class="btn btn-outline-secondary btn-sm" title="Bắt đầu đoạn chat mới">
                    <i class="fa-solid fa-broom"></i>
                </button>
            </div>
            
            <!-- Chat Body -->
            <div class="card-body p-4 bg-light" id="aiChatBody" style="flex:1; overflow-y:auto; display:flex; flex-direction:column; gap: 15px;">
                
                <!-- Welcome Screen -->
                <div id="aiWelcome" class="d-flex mb-2">
                    <div class="me-3">
                        <div style="width:40px;height:40px;border-radius:50%;background:var(--primary);color:var(--white);display:flex;align-items:center;justify-content:center;">
                            <i class="fa-solid fa-robot"></i>
                        </div>
                    </div>
                    <div style="background:var(--white);padding:14px 20px;border-radius:0 16px 16px 16px;box-shadow:0 2px 10px rgba(0,0,0,0.05);">
                        <p class="m-0">Xin chào! Tôi là Trợ lý AI của NovaCare. Bạn đang cảm thấy như thế nào?</p>
                    </div>
                </div>

                <!-- Lịch sử Chat (nếu có từ PHP session) -->
                <?php if (!empty($chatHistory)): ?>
                    <script>
                        // Ẩn welcome nếu có lịch sử
                        document.addEventListener('DOMContentLoaded', function() {
                            document.getElementById('aiWelcome').style.display = 'none';
                        });
                    </script>
                    <?php foreach ($chatHistory as $msg): ?>
                        <?php if ($msg['role'] === 'user'): ?>
                            <div class="ai-msg user">
                                <div class="ai-msg-avatar"><i class="fa-solid fa-user"></i></div>
                                <div class="ai-msg-content">
                                    <div class="ai-msg-bubble"><?= htmlspecialchars($msg['text']) ?></div>
                                </div>
                            </div>
                        <?php else: ?>
                            <?php 
                                // Parse JSON to HTML
                                $data = json_decode($msg['text'], true);
                                if ($data && isset($data['condition'])):
                                    $severity = $data['severity'] ?? 'low';
                                    $sevLabels = ['low' => 'Nhẹ', 'medium' => 'Trung bình', 'high' => 'Nghiêm trọng'];
                                    $sevLabel = $sevLabels[$severity] ?? 'Nhẹ';
                            ?>
                                <div class="ai-msg bot">
                                    <div class="ai-msg-avatar"><i class="fa-solid fa-robot"></i></div>
                                    <div class="ai-msg-content">
                                        <div class="ai-msg-bubble">
                                            <div class="ai-diagnosis-card">
                                                <div class="ai-diagnosis-title"><i class="fa-solid fa-stethoscope"></i> <?= htmlspecialchars($data['condition']) ?></div>
                                                <div class="ai-diagnosis-advice"><?= htmlspecialchars($data['advice']) ?></div>
                                                <div class="ai-diagnosis-meta">
                                                    <?php if(!empty($data['department'])): ?><span class="ai-meta-badge ai-badge-dept"><i class="fa-solid fa-building-columns"></i> <?= htmlspecialchars($data['department']) ?></span><?php endif; ?>
                                                    <span class="ai-meta-badge ai-badge-severity severity-<?= $severity ?>"><?= $sevLabel ?></span>
                                                </div>
                                                <?php if($severity === 'high'): ?>
                                                    <div class="ai-emergency-alert"><i class="fa-solid fa-phone me-1"></i> Đây có thể là tình huống khẩn cấp! Hãy gọi cấp cứu ngay.</div>
                                                <?php endif; ?>
                                                <?php if(!empty($data['follow_up'])): ?>
                                                    <div class="ai-followup-box"><i class="fa-solid fa-circle-question me-1"></i> <?= htmlspecialchars($data['follow_up']) ?></div>
                                                <?php endif; ?>
                                                <?php if($severity !== 'high'): ?>
                                                    <a href="index.php?page=appointments&action=create" class="ai-action-btn"><i class="fa-solid fa-calendar-plus"></i> Đặt lịch khám</a>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php else: ?>
                                <div class="ai-msg bot">
                                    <div class="ai-msg-avatar"><i class="fa-solid fa-robot"></i></div>
                                    <div class="ai-msg-content">
                                        <div class="ai-msg-bubble"><?= htmlspecialchars($msg['text']) ?></div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>

            </div>
            
            <!-- Input area -->
            <div class="card-footer bg-white border-top p-3">
                <form id="aiChatForm" class="d-flex gap-2" onsubmit="event.preventDefault();">
                    <input type="text" id="aiChatInput" class="form-control" placeholder="Mô tả triệu chứng của bạn..." autocomplete="off" required style="border-radius:20px;padding:10px 20px;height:auto;">
                    <button type="submit" id="aiSendBtn" class="btn-submit" style="border-radius:50%;width:46px;height:46px;padding:0;min-width:46px;">
                        <i class="fa-solid fa-paper-plane"></i>
                    </button>
                </form>
            </div>
        </div>
        <p class="text-center text-muted mt-3" style="font-size:12px;"><i class="fa-solid fa-circle-info me-1"></i>Lưu ý: Kết quả phân tích từ AI chỉ mang tính chất tham khảo, không thể thay thế quyết định trị liệu của Bác sĩ.</p>
    </div>
</div>

<style>
/* Chat Styles */
.ai-msg { display: flex; margin-bottom: 15px; width: 100%; }
.ai-msg.user { flex-direction: row-reverse; }
.ai-msg-avatar { width: 40px; height: 40px; border-radius: 50%; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
.ai-msg.user .ai-msg-avatar { background: var(--gray-200); color: var(--gray-600); margin-left: 15px; }
.ai-msg.bot .ai-msg-avatar { background: var(--primary); color: white; margin-right: 15px; }
.ai-msg-content { max-width: 80%; }
.ai-msg-bubble { padding: 14px 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
.ai-msg.user .ai-msg-bubble { background: var(--primary); color: white; border-radius: 16px 16px 0 16px; }
.ai-msg.bot .ai-msg-bubble { background: white; color: var(--text-dark); border-radius: 0 16px 16px 16px; }
.ai-msg-time { font-size: 11px; color: var(--gray-400); margin-top: 5px; text-align: right; }
.ai-msg.user .ai-msg-time { text-align: left; }

/* AI Card inside Bubble */
.ai-diagnosis-card { line-height: 1.6; }
.ai-diagnosis-title { color: var(--primary); font-weight: bold; font-size: 16px; margin-bottom: 10px; border-bottom: 1px solid var(--gray-200); padding-bottom: 8px; }
.ai-diagnosis-meta { display: flex; gap: 8px; margin: 12px 0; flex-wrap: wrap; }
.ai-meta-badge { padding: 4px 10px; border-radius: 20px; font-size: 12px; font-weight: 500; }
.ai-badge-dept { background: var(--primary-light); color: var(--primary); }
.severity-low { background: #dcfce7; color: #166534; }
.severity-medium { background: #fef9c3; color: #854d0e; }
.severity-high { background: #fee2e2; color: #991b1b; }
.ai-emergency-alert { background: #fef2f2; color: #ef4444; padding: 10px; border-radius: 8px; border-left: 4px solid #ef4444; margin-top: 10px; font-size: 13px; font-weight: bold; }
.ai-followup-box { background: #f8fafc; color: #475569; padding: 10px; border-radius: 8px; margin-top: 10px; font-size: 13px; font-style: italic; }
.ai-action-btn { display: inline-block; margin-top: 12px; background: var(--primary); color: white; padding: 6px 14px; border-radius: 6px; font-size: 13px; text-decoration: none; }
.ai-action-btn:hover { background: var(--primary-dark); color: white; }

/* Typing Indicator */
.ai-typing { display: flex; margin-bottom: 15px; }
.ai-typing-dots { background: white; padding: 15px 20px; border-radius: 0 16px 16px 16px; display: flex; align-items: center; gap: 5px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
.ai-typing-dots span { width: 8px; height: 8px; background: var(--primary); border-radius: 50%; animation: typing 1.4s infinite ease-in-out both; opacity: 0.6; }
.ai-typing-dots span:nth-child(1) { animation-delay: -0.32s; }
.ai-typing-dots span:nth-child(2) { animation-delay: -0.16s; }
@keyframes typing { 0%, 80%, 100% { transform: scale(0); } 40% { transform: scale(1); } }
</style>

<!-- Script xử lý AJAX Chat -->
<script src="assets/js/ai-chat.js?v=<?= time() ?>"></script>
