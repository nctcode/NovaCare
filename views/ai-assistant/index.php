<!-- Trợ lý AI y tế -->
<div class="row g-4 justify-content-center">
    <!-- Chat Interface -->
    <div class="col-lg-8" data-aos="fade-up">
        <div class="content-card" style="border:1px solid var(--gray-200); box-shadow:0 8px 30px rgba(0,0,0,0.06);">
            <div class="card-header bg-white border-bottom" style="padding:20px 24px;">
                <h5 class="m-0" style="color:var(--primary);"><i class="fa-solid fa-robot me-2"></i>NovaCare AI Assistant</h5>
                <p class="text-muted m-0 mt-1" style="font-size:13px;">Nhập triệu chứng của bạn, AI sẽ phân tích và gợi ý chuyên khoa phù hợp.</p>
            </div>
            <div class="card-body p-4 bg-light" style="min-height: 300px;">
                <!-- Hướng dẫn ban đầu -->
                <?php if (!isset($response)): ?>
                <div class="d-flex mb-4">
                    <div class="me-3">
                        <div style="width:40px;height:40px;border-radius:50%;background:var(--primary);color:var(--white);display:flex;align-items:center;justify-content:center;">
                            <i class="fa-solid fa-robot"></i>
                        </div>
                    </div>
                    <div style="background:var(--white);padding:14px 20px;border-radius:0 16px 16px 16px;box-shadow:0 2px 10px rgba(0,0,0,0.05);">
                        <p class="m-0">Xin chào! Tôi là Trợ lý AI của NovaCare. Bạn đang cảm thấy như thế nào? (VD: Tôi bị đau đầu và sốt).</p>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Trả lời của AI -->
                <?php if (isset($response)): ?>
                <!-- Tin nhắn user -->
                <div class="d-flex justify-content-end mb-4">
                    <div style="background:var(--primary);color:white;padding:14px 20px;border-radius:16px 0 16px 16px;box-shadow:0 2px 10px rgba(0,0,0,0.05);max-width:80%;">
                        <p class="m-0"><?= htmlspecialchars($_POST['symptoms']) ?></p>
                    </div>
                </div>

                <!-- Tin nhắn AI -->
                <div class="d-flex mb-4">
                    <div class="me-3">
                        <div style="width:40px;height:40px;border-radius:50%;background:var(--primary);color:var(--white);display:flex;align-items:center;justify-content:center;">
                            <i class="fa-solid fa-robot"></i>
                        </div>
                    </div>
                    <div style="background:var(--white);padding:18px 20px;border-radius:0 16px 16px 16px;box-shadow:0 2px 10px rgba(0,0,0,0.05);max-width:80%;">
                        <h6 style="color:var(--primary); font-weight:700;"><i class="fa-solid fa-stethoscope me-2"></i>Chẩn đoán dự kiến: <?= htmlspecialchars($response['condition']) ?></h6>
                        <hr class="my-2">
                        <p class="m-0 mb-3" style="line-height:1.6;"><?= htmlspecialchars($response['advice']) ?></p>
                        <div class="d-flex align-items-center gap-2">
                            <span class="badge-status badge-<?= $response['severity'] == 'high' ? 'cancelled' : ($response['severity'] == 'medium' ? 'pending' : 'confirmed') ?>">
                                Cấp độ: <?= ucfirst($response['severity']) ?>
                            </span>
                            <span class="badge-status" style="background:var(--primary-light);color:var(--primary);">
                                <i class="fa-solid fa-building me-1"></i> Khoa: <?= htmlspecialchars($response['department']) ?>
                            </span>
                        </div>
                        
                        <?php if ($response['severity'] == 'high'): ?>
                        <div class="mt-3 p-3" style="background:#fef2f2;border-radius:8px;border-left:4px solid #ef4444;">
                            <strong style="color:#ef4444;"><i class="fa-solid fa-triangle-exclamation me-1"></i>Cảnh báo:</strong> Đây là triệu chứng nguy hiểm, vui lòng gọi cấp cứu hoặc đến bệnh viện ngay!
                        </div>
                        <?php else: ?>
                        <div class="mt-3">
                            <a href="index.php?page=appointments&action=create" class="btn-submit" style="padding:6px 16px;font-size:13px;text-decoration:none;display:inline-block;">Đặt lịch khám chuyên khoa này</a>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

            </div>
            
            <!-- Input area -->
            <div class="card-footer bg-white border-top p-3">
                <form method="POST" action="index.php?page=ai-assistant" class="d-flex gap-2">
                    <input type="text" name="symptoms" class="form-control" placeholder="Mô tả triệu chứng của bạn..." required style="border-radius:20px;padding:10px 20px;height:auto;" value="<?= isset($_POST['symptoms']) ? htmlspecialchars($_POST['symptoms']) : '' ?>">
                    <button type="submit" class="btn-submit" style="border-radius:50%;width:46px;height:46px;padding:0;min-width:46px;"><i class="fa-solid fa-paper-plane"></i></button>
                    <?php if(isset($response)): ?>
                        <a href="index.php?page=ai-assistant" class="btn-cancel" style="border-radius:50%;width:46px;height:46px;padding:0;min-width:46px;display:flex;align-items:center;justify-content:center;"><i class="fa-solid fa-rotate-right"></i></a>
                    <?php endif; ?>
                </form>
            </div>
        </div>
        <p class="text-center text-muted mt-3" style="font-size:12px;"><i class="fa-solid fa-circle-info me-1"></i>Lưu ý: Kết quả phân tích từ AI chỉ mang tính chất tham khảo, không thể thay thế quyết định trị liệu của Bác sĩ.</p>
    </div>
</div>
