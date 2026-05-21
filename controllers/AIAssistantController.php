<?php
/**
 * AIAssistantController - Trợ lý AI y tế (Powered by Google Gemini)
 * 
 * Nâng cấp từ keyword-based thành AI thực sự.
 * Hỗ trợ chat multi-turn qua session, tích hợp AJAX API.
 */

require_once __DIR__ . '/../models/BeeknoeeAI.php';

class AIAssistantController {

    private $ai;

    public function __construct() {
        $this->ai = new BeeknoeeAI();
    }

    /**
     * Trang chính - Giao diện chatbot AI
     */
    public function index() {
        $response = null;
        $userInput = '';
        $aiConfigured = $this->ai->isConfigured();

        // Khởi tạo lịch sử chat trong session
        if (!isset($_SESSION['ai_chat_history'])) {
            $_SESSION['ai_chat_history'] = [];
        }

        // Xử lý form POST (fallback khi JS bị tắt)
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && !isset($_POST['ajax'])) {
            $userInput = trim($_POST['symptoms'] ?? '');
            if (!empty($userInput)) {
                if ($aiConfigured) {
                    $result = $this->ai->chat($userInput, $_SESSION['ai_chat_history']);
                    
                    if ($result['success']) {
                        $response = $result['data'];
                        
                        // Lưu vào lịch sử
                        $_SESSION['ai_chat_history'][] = ['role' => 'user', 'text' => $userInput];
                        $_SESSION['ai_chat_history'][] = ['role' => 'model', 'text' => $result['raw'] ?? json_encode($result['data'])];
                        
                        // Giới hạn lịch sử (giữ 20 tin nhắn gần nhất)
                        if (count($_SESSION['ai_chat_history']) > 20) {
                            $_SESSION['ai_chat_history'] = array_slice($_SESSION['ai_chat_history'], -20);
                        }
                    } else {
                        $response = [
                            'condition'  => 'Lỗi hệ thống',
                            'advice'     => $result['error'],
                            'department' => '',
                            'severity'   => 'low',
                            'follow_up'  => ''
                        ];
                    }
                } else {
                    // Fallback: dùng phân tích keyword cũ khi chưa có API key
                    $response = $this->analyzeSymptomsFallback($userInput);
                }
            }
        }

        $chatHistory = $_SESSION['ai_chat_history'] ?? [];

        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/ai-assistant/index.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    /**
     * API endpoint cho AJAX chat (gọi từ JavaScript)
     */
    public function chat() {
        header('Content-Type: application/json; charset=utf-8');

        // Chỉ chấp nhận POST
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            exit;
        }

        // Lấy input từ JSON body hoặc form data
        $input = json_decode(file_get_contents('php://input'), true);
        $message = trim($input['message'] ?? ($_POST['message'] ?? ''));

        if (empty($message)) {
            echo json_encode(['success' => false, 'error' => 'Vui lòng nhập tin nhắn']);
            exit;
        }

        if (!$this->ai->isConfigured()) {
            // Fallback keyword khi chưa cấu hình API Key
            $fallback = $this->analyzeSymptomsFallback($message);
            echo json_encode([
                'success'  => true,
                'data'     => $fallback,
                'fallback' => true
            ]);
            exit;
        }

        // Lấy lịch sử chat từ session
        if (!isset($_SESSION['ai_chat_history'])) {
            $_SESSION['ai_chat_history'] = [];
        }

        $result = $this->ai->chat($message, $_SESSION['ai_chat_history']);

        if ($result['success']) {
            // Lưu vào lịch sử
            $_SESSION['ai_chat_history'][] = ['role' => 'user', 'text' => $message];
            $_SESSION['ai_chat_history'][] = ['role' => 'model', 'text' => $result['raw'] ?? json_encode($result['data'])];

            if (count($_SESSION['ai_chat_history']) > 20) {
                $_SESSION['ai_chat_history'] = array_slice($_SESSION['ai_chat_history'], -20);
            }
        }

        echo json_encode($result);
        exit;
    }

    /**
     * Xóa lịch sử chat (bắt đầu cuộc hội thoại mới)
     */
    public function clearHistory() {
        $_SESSION['ai_chat_history'] = [];

        if (isset($_GET['ajax'])) {
            header('Content-Type: application/json');
            echo json_encode(['success' => true, 'message' => 'Đã xóa lịch sử']);
            exit;
        }

        header('Location: index.php?page=ai-assistant');
        exit;
    }

    /**
     * Fallback: Phân tích triệu chứng bằng keyword khi chưa cấu hình API
     * (giữ logic cũ để đảm bảo hệ thống vẫn hoạt động)
     */
    private function analyzeSymptomsFallback($input) {
        $input = mb_strtolower($input, 'UTF-8');

        $conditions = [
            [
                'keywords' => ['đau đầu', 'nhức đầu', 'headache', 'đau nửa đầu'],
                'condition' => 'Đau đầu / Migraine',
                'advice' => 'Có thể do căng thẳng, thiếu ngủ, hoặc migraine. Nghỉ ngơi, uống nhiều nước. Nếu đau kéo dài hơn 3 ngày, hãy đến gặp bác sĩ chuyên khoa Thần kinh.',
                'department' => 'Thần kinh',
                'severity' => 'medium',
                'follow_up' => 'Bạn bị đau đầu ở vị trí nào? Đau liên tục hay theo cơn?',
            ],
            [
                'keywords' => ['đau ngực', 'tức ngực', 'chest pain', 'tim đập nhanh', 'khó thở'],
                'condition' => 'Vấn đề Tim mạch',
                'advice' => '⚠️ ĐÂY CÓ THỂ LÀ DẤU HIỆU NGHIÊM TRỌNG! Đau ngực kèm khó thở cần được xử lý ngay. Hãy đến phòng cấp cứu hoặc gặp bác sĩ Tim mạch ngay lập tức.',
                'department' => 'Tim mạch',
                'severity' => 'high',
                'follow_up' => '',
            ],
            [
                'keywords' => ['sốt', 'fever', 'nóng', 'ớn lạnh'],
                'condition' => 'Sốt / Cảm cúm',
                'advice' => 'Có thể là cảm cúm hoặc nhiễm virus. Uống nhiều nước, nghỉ ngơi, có thể dùng thuốc hạ sốt. Nếu sốt cao > 39°C kéo dài, cần đi khám bệnh viện.',
                'department' => 'Nội tổng quát',
                'severity' => 'medium',
                'follow_up' => 'Bạn sốt bao nhiêu độ? Có kèm triệu chứng nào khác không?',
            ],
            [
                'keywords' => ['phát ban', 'nổi mẩn', 'ngứa', 'skin rash', 'nổi đỏ', 'dị ứng da'],
                'condition' => 'Viêm da / Dị ứng da',
                'advice' => 'Có thể do dị ứng thời tiết, thức ăn hoặc viêm da tiếp xúc. Tránh gãi nhiều để không gây nhiễm trùng. Nên khám chuyên khoa Da liễu.',
                'department' => 'Da liễu',
                'severity' => 'low',
                'follow_up' => 'Vùng da bị ảnh hưởng ở đâu? Bạn có ăn thức ăn lạ gần đây không?',
            ],
            [
                'keywords' => ['đau bụng', 'tiêu chảy', 'buồn nôn', 'nôn', 'đau dạ dày'],
                'condition' => 'Rối loạn tiêu hóa',
                'advice' => 'Có thể do ngộ độc thức ăn, viêm dạ dày, hoặc rối loạn tiêu hóa. Ăn chín uống sôi, bù nước. Nếu đau dữ dội, cần gặp bác sĩ Nội Tiêu hóa.',
                'department' => 'Nội tổng quát',
                'severity' => 'medium',
                'follow_up' => 'Bạn đau bụng ở vị trí nào? Đau âm ỉ hay đau quặn?',
            ],
            [
                'keywords' => ['ho', 'đau họng', 'viêm họng', 'cough', 'sổ mũi', 'nghẹt mũi'],
                'condition' => 'Viêm đường hô hấp trên',
                'advice' => 'Có thể là cảm lạnh, viêm họng, hoặc viêm phế quản. Nghỉ ngơi, súc miệng nước muối. Nếu ho kéo dài > 2 tuần, cần gặp bác sĩ.',
                'department' => 'Nội tổng quát',
                'severity' => 'low',
                'follow_up' => 'Bạn ho khan hay ho có đờm? Ho đã bao lâu rồi?',
            ],
            [
                'keywords' => ['đau lưng', 'đau cơ', 'đau khớp', 'back pain', 'nhức mỏi'],
                'condition' => 'Đau cơ xương khớp',
                'advice' => 'Có thể do sai tư thế, thoát vị đĩa đệm, hoặc viêm khớp. Chườm nóng, nghỉ ngơi. Nếu đau lan xuống chân, cần gặp bác sĩ.',
                'department' => 'Nội tổng quát',
                'severity' => 'medium',
                'follow_up' => 'Bạn đau ở vị trí nào? Đau khi vận động hay khi nghỉ ngơi?',
            ],
            [
                'keywords' => ['mất ngủ', 'khó ngủ', 'insomnia', 'lo âu', 'stress', 'trầm cảm'],
                'condition' => 'Rối loạn giấc ngủ / Tâm lý',
                'advice' => 'Có thể do căng thẳng hoặc rối loạn tâm lý. Hạn chế thiết bị điện tử trước ngủ. Nếu mất ngủ kéo dài, nên gặp chuyên khoa Thần kinh.',
                'department' => 'Thần kinh',
                'severity' => 'medium',
                'follow_up' => 'Bạn khó ngủ bao lâu rồi? Có gặp căng thẳng trong công việc/cuộc sống không?',
            ],
        ];

        foreach ($conditions as $cond) {
            foreach ($cond['keywords'] as $keyword) {
                if (strpos($input, $keyword) !== false) {
                    return $cond;
                }
            }
        }

        return [
            'condition'  => 'Không xác định',
            'advice'     => 'Xin lỗi, tôi không thể xác định chính xác tình trạng từ mô tả của bạn. Vui lòng mô tả chi tiết hơn (VD: triệu chứng gì, ở đâu, bao lâu rồi) hoặc đặt lịch khám trực tiếp.',
            'department' => 'Nội tổng quát',
            'severity'   => 'low',
            'follow_up'  => 'Bạn có thể mô tả triệu chứng cụ thể hơn không? Ví dụ: đau ở đâu, mức độ ra sao?',
        ];
    }
}
