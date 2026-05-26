<?php
/**
 * AdminBeeknoeeAI Model - AI chuyên biệt cho Admin (Quản trị hệ thống)
 * 
 * Extends BaseAI.
 * Trả về văn bản thuần túy (Markdown/HTML), không ép parse JSON.
 */

require_once __DIR__ . '/BaseAI.php';

class AdminBeeknoeeAI extends BaseAI {

    private $apiKey;
    private $model;
    private $apiUrl;

    public function __construct() {
        $this->apiKey       = defined('BEEKNOEE_API_KEY') ? BEEKNOEE_API_KEY : '';
        $this->model        = defined('BEEKNOEE_MODEL') ? BEEKNOEE_MODEL : 'gemini-2.0-flash';
        $this->apiUrl       = defined('BEEKNOEE_API_URL') ? BEEKNOEE_API_URL : 'https://platform.beeknoee.com/api/v1/chat/completions';
        // Sử dụng Prompt riêng cho Admin
        $this->systemPrompt = defined('ADMIN_AI_SYSTEM_PROMPT') ? ADMIN_AI_SYSTEM_PROMPT : 'Bạn là AI Quản trị hệ thống.';
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

        return $this->sendRequest($payload);
    }

    public function summarizeMedicalRecords($records) {
        // Không dùng cho Admin AI
        return [
            'success' => false,
            'data'    => null,
            'error'   => 'Not implemented for Admin AI.'
        ];
    }

    /**
     * Gửi request và trả về RAW TEXT (Không parse JSON)
     */
    private function sendRequest($payload) {
        $ch = curl_init($this->apiUrl);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST           => true,
            CURLOPT_HTTPHEADER     => [
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->apiKey
            ],
            CURLOPT_POSTFIELDS     => json_encode($payload),
            CURLOPT_TIMEOUT        => 120,
            CURLOPT_SSL_VERIFYPEER => false,
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

        // Với Admin AI, ta trả về dữ liệu raw dạng text trực tiếp (Markdown)
        return [
            'success' => true,
            'data'    => $aiText,
            'error'   => null,
            'raw'     => $aiText
        ];
    }
}
