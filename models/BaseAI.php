<?php
/**
 * BaseAI - Abstract class cho AI Services trong NovaCare
 * 
 * Chứa các method dùng chung giữa OllamaAI và GeminiAI:
 * - parseAIResponse(): Parse JSON từ phản hồi AI
 * - normalizeResponse(): Đảm bảo response có đủ các trường
 * - buildMedicalRecordText(): Xây dựng text từ bệnh án
 */

require_once __DIR__ . '/../config/ai.php';

abstract class BaseAI {

    protected $systemPrompt;
    protected $temperature;

    /**
     * Kiểm tra API đã được cấu hình chưa
     */
    abstract public function isConfigured();

    /**
     * Gửi tin nhắn và nhận phản hồi
     */
    abstract public function chat($userMessage, $history = []);

    /**
     * Tóm tắt bệnh án bằng AI
     */
    abstract public function summarizeMedicalRecords($records);

    // ==========================================
    //  SHARED METHODS (dùng chung)
    // ==========================================

    /**
     * Parse JSON response từ AI (có thể lẫn markdown code blocks)
     * 
     * Thứ tự thử: 
     * 1. Parse trực tiếp (nếu AI trả JSON thuần)
     * 2. Tìm trong ```json ... ``` block
     * 3. Tìm JSON giữa { và }
     * 
     * @param string $text - Raw response text from AI
     * @return array|null - Parsed data hoặc null nếu không parse được
     */
    protected function parseAIResponse($text) {
        // Thử parse trực tiếp
        $data = json_decode($text, true);
        if ($data !== null && isset($data['condition'])) {
            return $this->normalizeResponse($data);
        }

        // Thử tìm JSON trong markdown code block
        if (preg_match('/```(?:json)?\s*\n?(.*?)\n?```/s', $text, $matches)) {
            $data = json_decode($matches[1], true);
            if ($data !== null && isset($data['condition'])) {
                return $this->normalizeResponse($data);
            }
        }

        // Thử tìm JSON giữa { và }
        if (preg_match('/\{.*\}/s', $text, $matches)) {
            $data = json_decode($matches[0], true);
            if ($data !== null && isset($data['condition'])) {
                return $this->normalizeResponse($data);
            }
        }

        return null;
    }

    /**
     * Đảm bảo response có đủ các trường cần thiết
     * 
     * @param array $data - Parsed JSON data
     * @return array - Normalized response
     */
    protected function normalizeResponse($data) {
        return [
            'condition'  => $data['condition']  ?? 'Không xác định',
            'advice'     => $data['advice']     ?? 'Vui lòng mô tả chi tiết hơn.',
            'department' => $data['department'] ?? 'Nội tổng quát',
            'severity'   => $data['severity']   ?? 'low',
            'follow_up'  => $data['follow_up']  ?? '',
        ];
    }

    /**
     * Xây dựng text từ danh sách bệnh án (dùng chung cho cả Ollama và Gemini)
     * 
     * @param array $records - Danh sách bệnh án
     * @return string - Text mô tả lịch sử khám bệnh
     */
    protected function buildMedicalRecordText($records) {
        $recordText = "Dưới đây là lịch sử khám bệnh của bệnh nhân:\n\n";
        foreach ($records as $i => $rec) {
            $recordText .= "--- Lần khám " . ($i + 1) . " ---\n";
            $recordText .= "Ngày: " . ($rec['created_at'] ?? 'N/A') . "\n";
            $recordText .= "Bác sĩ: " . ($rec['doctor_name'] ?? 'N/A') . "\n";
            $recordText .= "Chẩn đoán: " . ($rec['diagnosis'] ?? 'N/A') . "\n";
            $recordText .= "Điều trị: " . ($rec['treatment'] ?? 'N/A') . "\n";
            $recordText .= "Ghi chú: " . ($rec['notes'] ?? 'N/A') . "\n\n";
        }
        return $recordText;
    }

    /**
     * Prompt tóm tắt bệnh án (dùng chung)
     */
    protected function getSummaryPromptSuffix() {
        return "\nHãy tóm tắt ngắn gọn lịch sử khám bệnh trên, nêu bật: bệnh lý nền (nếu có), xu hướng sức khỏe, và các điểm cần lưu ý cho lần khám tiếp theo. Trả lời bằng tiếng Việt, chuyên nghiệp.";
    }

    /**
     * Tạo fallback response khi không parse được JSON
     * 
     * @param string $aiText - Raw AI response text
     * @return array
     */
    protected function buildFallbackResponse($aiText) {
        return [
            'success' => true,
            'data'    => [
                'condition'  => 'Phản hồi AI',
                'advice'     => $aiText,
                'department' => 'Nội tổng quát',
                'severity'   => 'low',
                'follow_up'  => ''
            ],
            'error' => null,
            'raw'   => $aiText
        ];
    }
}
