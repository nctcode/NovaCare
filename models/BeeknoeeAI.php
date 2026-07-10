<?php
/**
 * BeeknoeeAI Model - Service class giao tiếp với Beeknoee API (OpenAI Compatible)
 * 
 * Extends BaseAI.
 * Sử dụng định dạng API của OpenAI (chat/completions) với Authorization Bearer.
 */

require_once __DIR__ . '/BaseAI.php';

class BeeknoeeAI extends BaseAI {

    private $apiKey;
    private $model;
    private $apiUrl;

    public function __construct() {
        $this->apiKey       = defined('BEEKNOEE_API_KEY') ? BEEKNOEE_API_KEY : '';
        $this->model        = defined('BEEKNOEE_MODEL') ? BEEKNOEE_MODEL : 'gemini-2.0-flash';
        $this->apiUrl       = defined('BEEKNOEE_API_URL') ? BEEKNOEE_API_URL : 'https://platform.beeknoee.com/api/v1/chat/completions';
        $this->systemPrompt = AI_SYSTEM_PROMPT;
        $this->temperature  = AI_TEMPERATURE;
    }

    /**
     * Kiểm tra API Key đã được cấu hình chưa
     */
    public function isConfigured() {
        return !empty($this->apiKey) && strlen($this->apiKey) > 10;
    }

    /**
     * Gửi tin nhắn đến Beeknoee API và nhận phản hồi
     */
    public function chat($userMessage, $history = []) {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'data'    => null,
                'error'   => 'Beeknoee API Key chưa được cấu hình.',
                'raw'     => null
            ];
        }

        // Xây dựng mảng messages theo chuẩn OpenAI
        $messages = [];
        $messages[] = [
            'role'    => 'system',
            'content' => $this->systemPrompt
        ];

        if (!empty($history)) {
            foreach ($history as $msg) {
                // Đảm bảo role là 'user' hoặc 'assistant'
                $role = ($msg['role'] === 'model') ? 'assistant' : $msg['role'];
                $messages[] = [
                    'role'    => $role,
                    'content' => $msg['text']
                ];
            }
        }

        $messages[] = [
            'role'    => 'user',
            'content' => $userMessage
        ];

        $payload = [
            'model'       => $this->model,
            'messages'    => $messages,
            'temperature' => $this->temperature
        ];

        return $this->sendRequest($payload, true);
    }

    /**
     * Tóm tắt bệnh án bằng AI
     */
    public function summarizeMedicalRecords($records) {
        if (!$this->isConfigured()) {
            return [
                'success' => false,
                'data'    => null,
                'error'   => 'Beeknoee API Key chưa được cấu hình.'
            ];
        }

        $recordText = $this->buildMedicalRecordText($records);
        $summaryPrompt = $recordText . $this->getSummaryPromptSuffix();

        $payload = [
            'model'    => $this->model,
            'messages' => [
                ['role' => 'system', 'content' => 'Bạn là trợ lý y khoa chuyên nghiệp.'],
                ['role' => 'user', 'content' => $summaryPrompt]
            ],
            'temperature' => 0.2
        ];

        return $this->sendRequest($payload, false);
    }

    /**
     * Hàm dùng chung để gửi cURL request đến Beeknoee/OpenAI
     */
    private function sendRequest($payload, $isChat) {
        $ch = curl_init($this->apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey
            ],
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_TIMEOUT        => 120, // Tăng timeout lên 120s vì API AI đôi khi phản hồi chậm
            CURLOPT_SSL_VERIFYPEER => false, // Tạm tắt xác minh SSL cho localhost (WAMP/XAMPP)
        ]);

        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $curlError = curl_error($ch);
        curl_close($ch);

        if ($curlError) {
            return [
                'success' => false,
                'data'    => null,
                'error'   => 'Lỗi kết nối API: ' . $curlError,
                'raw'     => null
            ];
        }

        if ($httpCode !== 200) {
            $errorBody = json_decode($response, true);
            $errorMsg = $errorBody['error']['message'] ?? "HTTP Error {$httpCode}";
            
            // Tự động hồi phục nếu model được chỉ định không tồn tại trên Beeknoee
            if ((strpos($errorMsg, 'không tồn tại') !== false || strpos($errorMsg, 'not exist') !== false || strpos($errorMsg, 'vô hiệu') !== false) && $payload['model'] !== 'gpt-5.5') {
                error_log("Beeknoee model {$payload['model']} not found, retrying with gpt-5.5...");
                $payload['model'] = 'gpt-5.5';
                return $this->sendRequest($payload, $isChat);
            }

            return [
                'success' => false,
                'data'    => null,
                'error'   => 'Lỗi Beeknoee API: ' . $errorMsg,
                'raw'     => $response
            ];
        }

        $result = json_decode($response, true);

        if (!isset($result['choices'][0]['message']['content'])) {
            return [
                'success' => false,
                'data'    => null,
                'error'   => 'Dữ liệu trả về không đúng định dạng OpenAI.',
                'raw'     => $response
            ];
        }

        $aiText = $result['choices'][0]['message']['content'];

        if ($isChat) {
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
        } else {
            return [
                'success' => true,
                'data'    => $aiText,
                'error'   => null
            ];
        }
    }
}
