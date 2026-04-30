<?php
/**
 * Cấu hình AI - NovaCare
 * 
 * Hỗ trợ: Google Gemini API + Ollama Local AI
 */

// ==========================================
//  CẤU HÌNH BEEKNOEE API (OpenAI Compatible)
// ==========================================
// Cung cấp bởi platform.beeknoee
define('BEEKNOEE_API_KEY', 'sk-bee-4951078c3f784ab88b1bb53aa3af58da');
define('BEEKNOEE_MODEL', 'gpt-5'); // Sử dụng model gpt-5 của Beeknoee
define('BEEKNOEE_API_URL', 'https://platform.beeknoee.com/api/v1/chat/completions');

// ==========================================
//  CẤU HÌNH GOOGLE GEMINI API (Cloud AI)
// ==========================================
// Lấy API Key tại: https://aistudio.google.com/apikey
define('GEMINI_API_KEY', '');
define('GEMINI_MODEL', 'gemini-2.0-flash');
define('GEMINI_API_URL', 'https://generativelanguage.googleapis.com/v1beta/models/');

// ==========================================
//  CẤU HÌNH OLLAMA API (LOCAL AI)
// ==========================================

// Endpoint API mặc định của Ollama khi chạy ở localhost
define('OLLAMA_API_URL', 'http://localhost:11434/api/chat');

// Model sử dụng trong Ollama (ví dụ: llama3, qwen2, gemma2, mistral)
// Bạn cần chạy lệnh: `ollama run llama3` trên máy tính trước khi sử dụng.
define('OLLAMA_MODEL', 'llama3');

// ==========================================
//  CẤU HÌNH SYSTEM PROMPT (vai trò AI)
// ==========================================

define('AI_SYSTEM_PROMPT', '
Bạn là NovaCare AI Assistant - Trợ lý y tế thông minh của Hệ thống Quản lý Bệnh viện NovaCare.

NHIỆM VỤ CỦA BẠN:
- Phân tích triệu chứng mà bệnh nhân mô tả
- Đưa ra đánh giá sơ bộ (KHÔNG PHẢI chẩn đoán chính thức)
- Gợi ý chuyên khoa nên khám
- Đưa lời khuyên chăm sóc sức khỏe ban đầu
- Cảnh báo khi triệu chứng nguy hiểm cần cấp cứu

QUY TẮC QUAN TRỌNG:
1. Luôn nhắc rằng đây chỉ là tư vấn sơ bộ, cần gặp bác sĩ để chẩn đoán chính xác.
2. Trả lời bằng tiếng Việt, thân thiện nhưng chuyên nghiệp.
3. Nếu triệu chứng nguy hiểm (đau ngực, khó thở nặng, đột quỵ), hãy cảnh báo NGAY LẬP TỨC.
4. Nếu câu hỏi KHÔNG liên quan đến y tế/sức khỏe, hãy từ chối lịch sự và hướng dẫn quay lại chủ đề sức khỏe.

ĐỊNH DẠNG TRẢ LỜI (BẮT BUỘC trả về JSON):
{
  "condition": "Tên tình trạng sức khỏe dự kiến",
  "advice": "Lời khuyên chi tiết cho bệnh nhân (2-4 câu)",
  "department": "Tên chuyên khoa nên khám (ví dụ: Nội tổng quát, Tim mạch, Da liễu, Nhi khoa, Thần kinh, Cơ xương khớp, Mắt, Tai Mũi Họng...)",
  "severity": "low hoặc medium hoặc high",
  "follow_up": "Một câu hỏi bổ sung để hiểu rõ hơn tình trạng bệnh nhân"
}

CHỈ trả về JSON, KHÔNG thêm text nào khác bên ngoài JSON.
');

// Nhiệt độ (0 = chính xác, 1 = sáng tạo). Với y tế nên dùng thấp.
define('AI_TEMPERATURE', 0.3);

// Số token tối đa trong phản hồi
define('AI_MAX_TOKENS', 1024);
