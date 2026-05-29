<?php
/**
 * AdminAIController - Trợ lý AI dành riêng cho Admin Quản trị (Dưới dạng Báo cáo Vận hành & Phân tích 4.0)
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/AdminBeeknoeeAI.php';
require_once __DIR__ . '/../helpers/Security.php';

class AdminAIController {

    private $ai;

    public function __construct() {
        Security::requireRole('admin');
        $this->ai = new AdminBeeknoeeAI();
    }

    /**
     * Trang chính - Executive Control Center Dashboard
     */
    public function index() {
        $db = new Database();
        $conn = $db->getConnection();

        // 1. Thống kê nhân lực theo các vai trò
        $stmt = $conn->query("SELECT role, COUNT(*) as qty FROM users GROUP BY role");
        $rolesCount = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

        // 2. Tổng số bệnh nhân
        $stmt = $conn->query("SELECT COUNT(*) FROM patients");
        $patientCount = $stmt->fetchColumn();

        // 3. Thống kê vật tư y tế
        $stmt = $conn->query("SELECT COUNT(*) as total, SUM(quantity) as qty, SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as avail, SUM(CASE WHEN status = 'maintenance' THEN 1 ELSE 0 END) as maint FROM equipment");
        $equipmentStats = $stmt->fetch();

        // 4. Thống kê máy móc y tế
        $stmt = $conn->query("SELECT COUNT(*) as total, SUM(CASE WHEN status = 'available' THEN 1 ELSE 0 END) as avail, SUM(CASE WHEN status = 'in_use' THEN 1 ELSE 0 END) as in_use, SUM(CASE WHEN status = 'maintenance' THEN 1 ELSE 0 END) as maint FROM medical_devices");
        $deviceStats = $stmt->fetch();

        // 5. Thống kê lịch khám
        $stmt = $conn->query("SELECT COUNT(*) FROM appointments");
        $appointmentCount = $stmt->fetchColumn();

        // 6. Thống kê hồ sơ nhập viện nội trú
        $stmt = $conn->query("SELECT COUNT(*) FROM admissions");
        $admissionCount = $stmt->fetchColumn();

        // 7. Lấy 5 nhật ký audit log gần đây nhất
        $stmt = $conn->query("SELECT al.*, u.name as user_name FROM audit_logs al LEFT JOIN users u ON al.user_id = u.id ORDER BY al.id DESC LIMIT 5");
        $latestLogs = $stmt->fetchAll();

        // 8. Tổng hợp ngữ cảnh cho AI
        $statsContext = "Dữ liệu vận hành thực tế của bệnh viện NovaCare hiện tại:\n";
        $statsContext .= "- Nhân sự: " . ($rolesCount['doctor'] ?? 0) . " Bác sĩ, " . ($rolesCount['nurse'] ?? 0) . " Y tá/Điều dưỡng, " . ($rolesCount['technician'] ?? 0) . " Kỹ thuật viên, " . ($rolesCount['receptionist'] ?? 0) . " Lễ tân, " . ($rolesCount['pharmacist'] ?? 0) . " Dược sĩ, " . ($rolesCount['cashier'] ?? 0) . " Thu ngân, " . ($rolesCount['director'] ?? 0) . " Ban giám đốc.\n";
        $statsContext .= "- Bệnh nhân: " . $patientCount . " hồ sơ bệnh nhân đăng ký.\n";
        $statsContext .= "- Lượt khám: " . $appointmentCount . " lịch khám hẹn trước.\n";
        $statsContext .= "- Nhập viện: " . $admissionCount . " bệnh nhân điều trị nội trú.\n";
        $statsContext .= "- Vật tư y tế: " . ($equipmentStats['total'] ?? 0) . " loại vật tư, tổng số lượng " . ($equipmentStats['qty'] ?? 0) . " đơn vị (Sẵn sàng: " . ($equipmentStats['avail'] ?? 0) . ", Đang bảo trì: " . ($equipmentStats['maint'] ?? 0) . ").\n";
        $statsContext .= "- Máy móc y tế: " . ($deviceStats['total'] ?? 0) . " máy (Sẵn sàng: " . ($deviceStats['avail'] ?? 0) . ", Đang sử dụng: " . ($deviceStats['in_use'] ?? 0) . ", Bảo trì: " . ($deviceStats['maint'] ?? 0) . ").\n";
        $statsContext .= "- Nhật ký hoạt động hệ thống mới nhất:\n";
        foreach ($latestLogs as $log) {
            $statsContext .= "  + [" . $log['created_at'] . "] Người dùng " . ($log['user_name'] ?? 'Hệ thống') . " thực hiện hành động '" . ($log['action'] ?? '') . "' trên bảng '" . $log['table_name'] . "' (ID dòng: " . $log['record_id'] . ").\n";
        }

        // Lưu ngữ cảnh vào Session
        $_SESSION['admin_ai_stats_context'] = $statsContext;

        $pageTitle = 'Trực quan AI Quản trị & Ra quyết định';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/ai-admin/index.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    /**
     * API Endpoint cho AJAX phân tích (gọi từ JS)
     */
    public function chat() {
        header('Content-Type: application/json; charset=utf-8');

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $message = trim($input['message'] ?? '');

        if (empty($message)) {
            echo json_encode(['success' => false, 'error' => 'Vui lòng chọn hoặc nhập yêu cầu phân tích']);
            exit;
        }

        if (!$this->ai->isConfigured()) {
            echo json_encode([
                'success' => false,
                'error'   => 'Trợ lý AI chưa được cấu hình API Key.'
            ]);
            exit;
        }

        // Tạo prompt nâng cao kết hợp ngữ cảnh thực tế của bệnh viện
        $statsContext = $_SESSION['admin_ai_stats_context'] ?? '';
        
        $promptWithContext = "Bạn là Trợ lý AI cao cấp chuyên phân tích dữ liệu và tư vấn giải pháp vận hành cho Giám đốc Bệnh viện NovaCare.\n";
        if (!empty($statsContext)) {
            $promptWithContext .= "Dưới đây là báo cáo số liệu hiện tại của bệnh viện được trích xuất từ database:\n";
            $promptWithContext .= "====================================\n";
            $promptWithContext .= $statsContext;
            $promptWithContext .= "====================================\n";
        }
        $promptWithContext .= "Hãy phân tích dữ liệu trên và trả lời chi tiết yêu cầu sau của Admin Quản trị:\n\"" . $message . "\"\n";
        $promptWithContext .= "Yêu cầu định dạng phản hồi:\n";
        $promptWithContext .= "- Trình bày báo cáo rõ ràng, ngắn gọn, phân chia mục lục cụ thể.\n";
        $promptWithContext .= "- Sử dụng Markdown để định dạng các thẻ tiêu đề (##, ###), bôi đậm các số liệu quan trọng, sử dụng danh sách gạch đầu dòng.\n";
        $promptWithContext .= "- Đưa ra các khuyến nghị hành động cụ thể, có số liệu thực tế dựa trên bối cảnh để thuyết phục Admin.\n";

        // Thực hiện gọi API trực tiếp
        // Không lưu history nhiều để tập trung vào việc tạo báo cáo chuyên sâu từng lần click
        $result = $this->ai->chat($promptWithContext, []);

        echo json_encode($result);
        exit;
    }
}
