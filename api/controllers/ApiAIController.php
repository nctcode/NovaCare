<?php
/**
 * ApiAIController - Trợ lý y khoa AI dành cho Mobile API v1
 */
require_once __DIR__ . '/../core/BaseApiController.php';
require_once __DIR__ . '/../../models/Patient.php';
require_once __DIR__ . '/../../models/Notification.php';
require_once __DIR__ . '/../../models/BeeknoeeAI.php';
require_once __DIR__ . '/../../models/GeminiAI.php';
require_once __DIR__ . '/../../models/OllamaAI.php';
require_once __DIR__ . '/../../helpers/AuditLog.php';

class ApiAIController extends BaseApiController {

    private $patientModel;
    private $notificationModel;

    public function __construct() {
        $this->patientModel = new Patient();
        $this->notificationModel = new Notification();
    }

    /**
     * POST /api/v1/ai/chat
     */
    public function chat() {
        $user = AuthMiddleware::$currentUser;
        if (!$user) {
            return $this->sendUnauthorized();
        }

        $input = $this->getJsonInput();
        $message = isset($input['message']) ? trim($input['message']) : '';

        // Validate
        if ($message === '') {
            return $this->sendError('Tin nhắn không được để trống.', 422);
        }
        if (mb_strlen($message, 'UTF-8') > 2000) {
            return $this->sendError('Tin nhắn không được dài quá 2000 ký tự.', 422);
        }

        $patient = $this->patientModel->findByUserId($user['user_id']);
        if (!$patient) {
            return $this->sendNotFound('Không tìm thấy hồ sơ bệnh nhân.');
        }
        $patientId = $patient['id'];

        $db = new Database();
        $conn = $db->getConnection();

        // 1. Lấy tối đa 10 tin nhắn gần nhất của chính patient này làm ngữ cảnh (context)
        $sqlContext = "SELECT sender, message FROM chat_histories 
                       WHERE patient_id = :patient_id AND deleted_at IS NULL 
                       ORDER BY id DESC LIMIT 10";
        $stmtContext = $conn->prepare($sqlContext);
        $stmtContext->execute([':patient_id' => $patientId]);
        $rows = $stmtContext->fetchAll(PDO::FETCH_ASSOC);

        // Đảo ngược lại để theo đúng thứ tự thời gian tăng dần
        $rows = array_reverse($rows);

        $history = array_map(function($row) {
            return [
                'role' => $row['sender'] === 'assistant' ? 'model' : 'user',
                'text' => $row['message']
            ];
        }, $rows);

        // 2. Lựa chọn AI Provider theo chuỗi fallback thông minh
        $aiResult = ['success' => false];
        $providerName = 'unknown';
        $modelName = 'unknown';

        // Thử Beeknoee
        $beeknoee = new BeeknoeeAI();
        if ($beeknoee->isConfigured()) {
            $providerName = 'beeknoee';
            $modelName = defined('BEEKNOEE_MODEL') ? BEEKNOEE_MODEL : 'gpt-5';
            $aiResult = $beeknoee->chat($message, $history);
        }

        // Fallback sang Gemini
        if (!$aiResult['success']) {
            $gemini = new GeminiAI();
            if ($gemini->isConfigured()) {
                $providerName = 'gemini';
                $modelName = defined('GEMINI_MODEL') ? GEMINI_MODEL : 'gemini-2.0-flash';
                $aiResult = $gemini->chat($message, $history);
            }
        }

        // Fallback sang Ollama
        if (!$aiResult['success']) {
            $ollama = new OllamaAI();
            if ($ollama->isConfigured()) {
                $providerName = 'ollama';
                $modelName = defined('OLLAMA_MODEL') ? OLLAMA_MODEL : 'llama3';
                $aiResult = $ollama->chat($message, $history);
            }
        }

        if (!$aiResult['success']) {
            error_log("All AI providers failed or are not configured.");
            return $this->sendError('Hệ thống dịch vụ AI đang bận hoặc chưa được cấu hình.', 503);
        }

        $responseObj = $aiResult['data'];
        $reply = $responseObj['advice'] ?? 'Tôi chưa hiểu ý bạn, vui lòng mô tả chi tiết hơn.';
        $severity = $responseObj['severity'] ?? 'low';
        $department = $responseObj['department'] ?? 'Nội tổng quát';
        $rawResponse = $aiResult['raw'] ?? json_encode($responseObj);

        // Disclaimer mặc định
        $disclaimer = "AI chỉ hỗ trợ tham khảo triệu chứng, không thay thế chỉ định và chẩn đoán chính thức từ bác sĩ chuyên khoa.";

        // Xử lý cảnh báo khẩn cấp nếu mức khẩn cấp là high
        if ($severity === 'high') {
            $reply .= "\n\n⚠️ Khuyến nghị khẩn cấp: Triệu chứng của bạn có vẻ nguy kịch hoặc nguy hiểm. Hãy liên hệ ngay với người thân, gọi xe cấp cứu (115) hoặc đến ngay cơ sở y tế gần nhất!";
            
            // Tự động tạo thông báo khẩn cấp gửi cho bệnh nhân
            try {
                $this->notificationModel->create(
                    $user['user_id'],
                    '⚠️ CẢNH BÁO SỨC KHỎE KHẨN CẤP',
                    'Hệ thống AI phát hiện bạn đang có triệu chứng có độ nguy hiểm cao. Hãy đến cơ sở y tế gần nhất ngay lập tức!',
                    'ai_alert'
                );
            } catch (Exception $ne) {
                error_log("Failed to create high severity notification: " . $ne->getMessage());
            }
        }

        try {
            $conn->beginTransaction();

            // Lưu tin nhắn của user vào chat_histories
            $sqlUserMsg = "INSERT INTO chat_histories (user_id, patient_id, sender, message) 
                           VALUES (:user_id, :patient_id, 'user', :message)";
            $conn->prepare($sqlUserMsg)->execute([
                ':user_id'    => $user['user_id'],
                ':patient_id' => $patientId,
                ':message'    => $message
            ]);

            // Lưu câu trả lời của AI trợ lý vào chat_histories
            $sqlAiMsg = "INSERT INTO chat_histories (user_id, patient_id, sender, message, provider, model, urgency_level, department_suggestion, raw_response) 
                         VALUES (:user_id, :patient_id, 'assistant', :message, :provider, :model, :urgency_level, :dept, :raw)";
            
            $conn->prepare($sqlAiMsg)->execute([
                ':user_id'       => $user['user_id'],
                ':patient_id'    => $patientId,
                ':message'       => $reply,
                ':provider'      => $providerName,
                ':model'         => $modelName,
                ':urgency_level' => $severity,
                ':dept'          => $department,
                ':raw'           => $rawResponse
            ]);

            $conn->commit();

            return $this->sendSuccess([
                'reply'                 => $reply,
                'disclaimer'            => $disclaimer,
                'urgency_level'         => $severity,
                'department_suggestion' => $department,
                'created_at'            => date('Y-m-d H:i:s')
            ], 'Gửi và phản hồi AI thành công');

        } catch (Exception $e) {
            if ($conn->inTransaction()) {
                $conn->rollBack();
            }
            error_log("AI chat save history error: " . $e->getMessage());
            return $this->sendServerError('Đã xảy ra lỗi khi ghi nhận lịch sử cuộc hội thoại AI.');
        }
    }

    /**
     * GET /api/v1/ai/chat-history
     */
    public function chatHistory() {
        $user = AuthMiddleware::$currentUser;
        if (!$user) {
            return $this->sendUnauthorized();
        }

        $patient = $this->patientModel->findByUserId($user['user_id']);
        if (!$patient) {
            return $this->sendNotFound('Không tìm thấy hồ sơ bệnh nhân.');
        }
        $patientId = $patient['id'];

        $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
        if ($limit > 50) $limit = 50;
        $offset = ($page - 1) * $limit;

        $db = new Database();
        $conn = $db->getConnection();

        // Query lịch sử chat phân trang (chưa bị xóa mềm)
        $sql = "SELECT id, sender, message, urgency_level, department_suggestion, created_at 
                FROM chat_histories 
                WHERE patient_id = :patient_id AND deleted_at IS NULL 
                ORDER BY id DESC LIMIT :limit OFFSET :offset";
        
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':patient_id', $patientId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $chatData = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Đảo ngược lại thứ tự thời gian tăng dần để render chat đúng trình tự
        $chatData = array_reverse($chatData);

        // Đếm tổng số bản ghi phục vụ phân trang
        $countSql = "SELECT COUNT(*) as total FROM chat_histories WHERE patient_id = :patient_id AND deleted_at IS NULL";
        $countStmt = $conn->prepare($countSql);
        $countStmt->execute([':patient_id' => $patientId]);
        $total = (int)$countStmt->fetch()['total'];

        return $this->sendSuccess(
            $chatData,
            'Lấy lịch sử trò chuyện AI thành công',
            200,
            [
                'page'        => $page,
                'limit'       => $limit,
                'total'       => $total,
                'total_pages' => ceil($total / $limit)
            ]
        );
    }

    /**
     * DELETE /api/v1/ai/chat-history
     */
    public function deleteChatHistory() {
        $user = AuthMiddleware::$currentUser;
        if (!$user) {
            return $this->sendUnauthorized();
        }

        $patient = $this->patientModel->findByUserId($user['user_id']);
        if (!$patient) {
            return $this->sendNotFound('Không tìm thấy hồ sơ bệnh nhân.');
        }
        $patientId = $patient['id'];

        try {
            $db = new Database();
            $conn = $db->getConnection();

            // Thực hiện Soft Delete lịch sử chat
            $sql = "UPDATE chat_histories SET deleted_at = NOW() WHERE patient_id = :patient_id AND deleted_at IS NULL";
            $stmt = $conn->prepare($sql);
            $stmt->execute([':patient_id' => $patientId]);

            // Ghi AuditLog
            AuditLog::logDelete('chat_histories', $patientId, ['info' => 'Bệnh nhân tự xóa lịch sử AI chat'], $user['user_id']);

            return $this->sendSuccess(null, 'Xóa lịch sử trò chuyện AI thành công.');

        } catch (Exception $e) {
            error_log("Delete chat history error: " . $e->getMessage());
            return $this->sendServerError('Đã xảy ra lỗi khi xóa lịch sử trò chuyện.');
        }
    }
}
