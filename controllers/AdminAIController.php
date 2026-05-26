<?php
/**
 * AdminAIController - Trợ lý AI dành riêng cho Admin Quản trị
 * 
 * Sử dụng AdminBeeknoeeAI để hỗ trợ vận hành, phân tích, ra quyết định.
 */

require_once __DIR__ . '/../models/AdminBeeknoeeAI.php';
require_once __DIR__ . '/../helpers/Security.php';

class AdminAIController {

    private $ai;

    public function __construct() {
        Security::requireRole('admin');
        $this->ai = new AdminBeeknoeeAI();
    }

    /**
     * Trang chính - Giao diện chatbot quản trị
     */
    public function index() {
        $response = null;
        $userInput = '';
        $aiConfigured = $this->ai->isConfigured();

        // Khởi tạo lịch sử chat trong session riêng cho admin
        if (!isset($_SESSION['admin_ai_chat_history'])) {
            $_SESSION['admin_ai_chat_history'] = [];
        }

        // Xử lý form POST (fallback khi JS bị tắt)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['ajax'])) {
            Security::requireCsrf();
            $userInput = trim($_POST['message'] ?? '');
            if (!empty($userInput)) {
                if ($aiConfigured) {
                    $result = $this->ai->chat($userInput, $_SESSION['admin_ai_chat_history']);
                    
                    if ($result['success']) {
                        $response = $result['data']; // Đây là plain text markdown
                        
                        // Lưu vào lịch sử
                        $_SESSION['admin_ai_chat_history'][] = ['role' => 'user', 'text' => $userInput];
                        $_SESSION['admin_ai_chat_history'][] = ['role' => 'model', 'text' => $response];
                        
                        // Giới hạn lịch sử (giữ 20 tin nhắn gần nhất)
                        if (count($_SESSION['admin_ai_chat_history']) > 20) {
                            $_SESSION['admin_ai_chat_history'] = array_slice($_SESSION['admin_ai_chat_history'], -20);
                        }
                    } else {
                        $response = "Lỗi hệ thống: " . $result['error'];
                    }
                } else {
                    $response = "Trợ lý AI chưa được cấu hình API Key. Vui lòng kiểm tra file env.php.";
                }
            }
        }

        $chatHistory = $_SESSION['admin_ai_chat_history'] ?? [];
        $pageTitle = 'AI Quản trị & Vận hành';

        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/ai-admin/index.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    /**
     * API endpoint cho AJAX chat (gọi từ JavaScript)
     */
    public function chat() {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $message = trim($input['message'] ?? ($_POST['message'] ?? ''));

        if (empty($message)) {
            echo json_encode(['success' => false, 'error' => 'Vui lòng nhập câu hỏi']);
            exit;
        }

        if (!$this->ai->isConfigured()) {
            echo json_encode([
                'success' => false,
                'error'   => 'Trợ lý AI Quản trị chưa được cấu hình API Key.'
            ]);
            exit;
        }

        if (!isset($_SESSION['admin_ai_chat_history'])) {
            $_SESSION['admin_ai_chat_history'] = [];
        }

        $result = $this->ai->chat($message, $_SESSION['admin_ai_chat_history']);

        if ($result['success']) {
            $_SESSION['admin_ai_chat_history'][] = ['role' => 'user', 'text' => $message];
            $_SESSION['admin_ai_chat_history'][] = ['role' => 'model', 'text' => $result['data']];

            if (count($_SESSION['admin_ai_chat_history']) > 20) {
                $_SESSION['admin_ai_chat_history'] = array_slice($_SESSION['admin_ai_chat_history'], -20);
            }
        }

        echo json_encode($result);
        exit;
    }

    /**
     * Xóa lịch sử chat
     */
    public function clearHistory() {
        $_SESSION['admin_ai_chat_history'] = [];

        if (isset($_GET['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Đã xóa lịch sử']);
            exit;
        }

        header('Location: index.php?page=ai-admin');
        exit;
    }
}
