<?php
/**
 * GeminiAI Model - Service class giao tiếp với Google Gemini API
 * 
 * Extends BaseAI: sử dụng parseAIResponse(), normalizeResponse(), buildMedicalRecordText() từ class cha.
 * Sử dụng cURL thuần (PHP native), không cần Composer/Guzzle.
 * Hỗ trợ: gửi tin nhắn, duy trì lịch sử hội thoại (multi-turn), 
 *          phân tích triệu chứng y tế.
 * 
 * Bảo mật: API key được gửi qua header (x-goog-api-key) thay vì URL query param.
 * SSL: CURLOPT_SSL_VERIFYPEER = true (nhất quán).
 */

require_once __DIR__ . '/BaseAI.php';

class GeminiAI extends BaseAI {

    private $apiKey;
    private $model;
    private $apiUrl;
    private $maxTokens;

    public function __construct() {
        $this->apiKey       = GEMINI_API_KEY;
        $this->model        = GEMINI_MODEL;
        $this->apiUrl       = GEMINI_API_URL;
        $this->systemPrompt = AI_SYSTEM_PROMPT;
        $this->temperature  = AI_TEMPERATURE;
        $this->maxTokens    = AI_MAX_TOKENS;
    }

    /**
     * Kiểm tra API Key đã được cấu hình chưa
     */
    public function isConfigured() {
        // Chỉ cần kiểm tra key không rỗng và độ dài hợp lệ
        return !empty($this->apiKey) && strlen($this->apiKey) > 20;
    }

    /**
     * Gửi tin nhắn đến Gemini API và nhận phản hồi
     * 
     * @param string $userMessage - Tin nhắn/triệu chứng của người dùng
     * @param array  $history     - Lịch sử hội thoại trước đó (tùy chọn, cho multi-turn)
     * @return array ['success' => bool, 'data' => array|null, 'error' => string|null, 'raw' => string|null]
     */
    public function chat($userMessage, $history = []) {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'data'    => null,
                'error'   => 'API Key chưa được cấu hình. Vui lòng cập nhật file config/ai.php.',
                'raw'     => null
            ];
        }

        // Xây dựng mảng contents cho multi-turn conversation
        $contents = [];

        // Thêm lịch sử hội thoại cũ (nếu có)
        if (!empty($history)) {
            foreach ($history as $msg) {
                $contents[] = [
                    'role'  => $msg['role'], // 'user' hoặc 'model'
                    'parts' => [['text' => $msg['text']]]
                ];
            }
        }

        // Thêm tin nhắn hiện tại của user
        $contents[] = [
            'role'  => 'user',
            'parts' => [['text' => $userMessage]]
        ];

        // Chuẩn bị payload
        $payload = [
            'system_instruction' => [
                'parts' => [['text' => $this->systemPrompt]]
            ],
            'contents' => $contents,
            'generationConfig' => [
                'temperature'     => $this->temperature,
                'maxOutputTokens' => $this->maxTokens,
                'responseMimeType' => 'application/json'
            ]
        ];

        // Gọi API (API key trong header thay vì URL để bảo mật)
        $url = $this->apiUrl . $this->model . ':generateContent';

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'x-goog-api-key: ' . $this->apiKey
            ],
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        // Xử lý lỗi cURL
        if ($curlError) {
            return [
                'success' => false,
                'data'    => null,
                'error'   => 'Lỗi kết nối: ' . $curlError,
                'raw'     => null
            ];
        }

        // Xử lý lỗi HTTP
        if ($httpCode !== 200) {
            $errorBody = json_decode($response, true);
            $errorMsg = $errorBody['error']['message'] ?? "HTTP Error {$httpCode}";
            return [
                'success' => false,
                'data'    => null,
                'error'   => 'Lỗi API Gemini: ' . $errorMsg,
                'raw'     => $response
            ];
        }

        // Parse response
        $result = json_decode($response, true);

        if (!isset($result['candidates'][0]['content']['parts'][0]['text'])) {
            // Kiểm tra nếu bị block bởi safety
            if (isset($result['candidates'][0]['finishReason']) && $result['candidates'][0]['finishReason'] === 'SAFETY') {
                return [
                    'success' => false,
                    'data'    => null,
                    'error'   => 'Nội dung bị chặn bởi bộ lọc an toàn của Google. Vui lòng thử lại với mô tả khác.',
                    'raw'     => $response
                ];
            }
            return [
                'success' => false,
                'data'    => null,
                'error'   => 'Không nhận được phản hồi từ AI. Cấu trúc dữ liệu không đúng.',
                'raw'     => $response
            ];
        }

        $aiText = $result['candidates'][0]['content']['parts'][0]['text'];

        // Parse JSON từ phản hồi AI (sử dụng method từ BaseAI)
        $parsedData = $this->parseAIResponse($aiText);

        if ($parsedData === null) {
            return $this->buildFallbackResponse($aiText);
        }

        return [
            'success' => true,
            'data'    => $parsedData,
            'error'   => null,
            'raw'     => $aiText
        ];
    }

    /**
     * Tóm tắt bệnh án bằng AI
     * 
     * @param array $records - Danh sách bệnh án (diagnosis, treatment, notes, date...)
     * @return array
     */
    public function summarizeMedicalRecords($records) {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'data'    => null,
                'error'   => 'API Key chưa được cấu hình.'
            ];
        }

        // Sử dụng method từ BaseAI
        $recordText = $this->buildMedicalRecordText($records);
        $summaryPrompt = $recordText . $this->getSummaryPromptSuffix();

        $payload = [
            'contents' => [
                ['role' => 'user', 'parts' => [['text' => $summaryPrompt]]]
            ],
            'generationConfig' => [
                'temperature'     => 0.2,
                'maxOutputTokens' => 800,
            ]
        ];

        $url = $this->apiUrl . $this->model . ':generateContent';

        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'x-goog-api-key: ' . $this->apiKey
            ],
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_TIMEOUT        => 30,
            CURLOPT_SSL_VERIFYPEER => true,
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError || $httpCode !== 200) {
            return [
                'success' => false,
                'data'    => null,
                'error'   => $curlError ?: "HTTP Error {$httpCode}"
            ];
        }

        $result = json_decode($response, true);
        $text = $result['candidates'][0]['content']['parts'][0]['text'] ?? null;

        return [
            'success' => true,
            'data'    => $text,
            'error'   => null
        ];
    }
}
