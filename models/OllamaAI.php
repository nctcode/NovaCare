<?php
/**
 * OllamaAI Model - Service class giao tiếp với Ollama (Local AI)
 * 
 * Extends BaseAI: sử dụng parseAIResponse(), normalizeResponse(), buildMedicalRecordText() từ class cha.
 * Sử dụng cURL thuần (PHP native).
 * Hỗ trợ: gửi tin nhắn, duy trì lịch sử hội thoại (multi-turn), 
 *          phân tích triệu chứng y tế.
 */

require_once __DIR__ . '/BaseAI.php';

class OllamaAI extends BaseAI {

    private $apiUrl;
    private $model;

    public function __construct() {
        $this->apiUrl       = defined('OLLAMA_API_URL') ? OLLAMA_API_URL : 'http://localhost:11434/api/chat';
        $this->model        = defined('OLLAMA_MODEL') ? OLLAMA_MODEL : 'llama3';
        $this->systemPrompt = AI_SYSTEM_PROMPT;
        $this->temperature  = AI_TEMPERATURE;
    }

    /**
     * Kiểm tra API đã được cấu hình chưa (với Ollama thì luôn coi là cấu hình nếu URL khác rỗng)
     */
    public function isConfigured() {
        return !empty($this->apiUrl);
    }

    /**
     * Gửi tin nhắn đến Ollama API và nhận phản hồi
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
                'error'   => 'URL Ollama chưa được cấu hình. Vui lòng kiểm tra file config/ai.php.',
                'raw'     => null
            ];
        }

        // Xây dựng mảng messages cho Ollama
        $messages = [];

        // System prompt luôn là message đầu tiên
        $messages[] = [
            'role'    => 'system',
            'content' => $this->systemPrompt
        ];

        // Thêm lịch sử hội thoại cũ (nếu có)
        if (!empty($history)) {
            foreach ($history as $msg) {
                // Đảm bảo role là 'user' hoặc 'assistant' (Ollama dùng 'assistant' thay vì 'model')
                $role = ($msg['role'] === 'model') ? 'assistant' : $msg['role'];
                $messages[] = [
                    'role'    => $role,
                    'content' => $msg['text']
                ];
            }
        }

        // Thêm tin nhắn hiện tại của user
        $messages[] = [
            'role'    => 'user',
            'content' => $userMessage
        ];

        // Chuẩn bị payload
        $payload = [
            'model'    => $this->model,
            'messages' => $messages,
            'stream'   => false,
            'options'  => [
                'temperature' => $this->temperature
            ]
        ];

        // Gửi request tới Ollama
        $ch = curl_init($this->apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_TIMEOUT        => 120, // Tăng timeout cho Local AI (đôi khi chạy chậm)
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
                'error'   => 'Lỗi kết nối Ollama: ' . $curlError . '. Hãy chắc chắn bạn đã chạy ứng dụng Ollama trên máy tính!',
                'raw'     => null
            ];
        }

        // Xử lý lỗi HTTP
        if ($httpCode !== 200) {
            $errorBody = json_decode($response, true);
            $errorMsg = $errorBody['error'] ?? "HTTP Error {$httpCode}";
            return [
                'success' => false,
                'data'    => null,
                'error'   => 'Lỗi Ollama: ' . $errorMsg,
                'raw'     => $response
            ];
        }

        // Parse response từ Ollama
        $result = json_decode($response, true);

        if (!isset($result['message']['content'])) {
            return [
                'success' => false,
                'data'    => null,
                'error'   => 'Không nhận được phản hồi từ Ollama. Cấu trúc dữ liệu không đúng.',
                'raw'     => $response
            ];
        }

        $aiText = $result['message']['content'];

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
     */
    public function summarizeMedicalRecords($records) {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'data'    => null,
                'error'   => 'URL Ollama chưa được cấu hình.'
            ];
        }

        // Sử dụng method từ BaseAI
        $recordText = $this->buildMedicalRecordText($records);
        $summaryPrompt = $recordText . $this->getSummaryPromptSuffix();

        $payload = [
            'model'    => $this->model,
            'messages' => [
                ['role' => 'user', 'content' => $summaryPrompt]
            ],
            'stream'   => false,
            'options'  => [
                'temperature' => 0.2
            ]
        ];

        $ch = curl_init($this->apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => ['Content-Type: application/json'],
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_TIMEOUT        => 120,
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
        $text = $result['message']['content'] ?? null;

        return [
            'success' => true,
            'data'    => $text,
            'error'   => null
        ];
    }
}
