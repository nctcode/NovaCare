<?php
/**
 * AuditLog Helper - Ghi lại mọi hành động trong hệ thống NovaCare
 * 
 * Đây là yêu cầu pháp lý cho hệ thống y tế:
 * - Ai đã làm gì, lúc nào
 * - Dữ liệu cũ và mới (cho truy vết)
 */
require_once __DIR__ . '/../config/database.php';

class AuditLog {

    /**
     * Ghi log hành động
     * 
     * @param string $action - Loại hành động: create, update, delete, login, logout, etc.
     * @param string|null $tableName - Tên bảng liên quan (VD: patients, doctors)
     * @param int|null $recordId - ID record bị tác động
     * @param array|null $oldData - Dữ liệu cũ (cho update/delete)
     * @param array|null $newData - Dữ liệu mới (cho create/update)
     */
    public static function log($action, $tableName = null, $recordId = null, $oldData = null, $newData = null) {
        try {
            $db = new Database();
            $conn = $db->getConnection();

            // Lấy thông tin user từ session
            $userId = $_SESSION['user']['id'] ?? null;
            $userName = $_SESSION['user']['name'] ?? 'System';

            // Lấy IP address
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
            if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                $ipAddress = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR'])[0];
            }

            // User agent
            $userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? '', 0, 500);

            $sql = "INSERT INTO activity_logs (user_id, user_name, action, table_name, record_id, old_data, new_data, ip_address, user_agent) 
                    VALUES (:user_id, :user_name, :action, :table_name, :record_id, :old_data, :new_data, :ip_address, :user_agent)";
            
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':user_name', $userName);
            $stmt->bindParam(':action', $action);
            $stmt->bindParam(':table_name', $tableName);
            $stmt->bindParam(':record_id', $recordId);
            
            $oldJson = $oldData ? json_encode($oldData, JSON_UNESCAPED_UNICODE) : null;
            $newJson = $newData ? json_encode($newData, JSON_UNESCAPED_UNICODE) : null;
            $stmt->bindParam(':old_data', $oldJson);
            $stmt->bindParam(':new_data', $newJson);
            $stmt->bindParam(':ip_address', $ipAddress);
            $stmt->bindParam(':user_agent', $userAgent);
            
            $stmt->execute();
        } catch (Exception $e) {
            // Không để lỗi audit làm crash ứng dụng
            error_log("AuditLog Error: " . $e->getMessage());
        }
    }

    /**
     * Shortcut: Log hành động tạo mới
     */
    public static function logCreate($tableName, $recordId, $newData = null) {
        self::log('create', $tableName, $recordId, null, $newData);
    }

    /**
     * Shortcut: Log hành động cập nhật
     */
    public static function logUpdate($tableName, $recordId, $oldData = null, $newData = null) {
        self::log('update', $tableName, $recordId, $oldData, $newData);
    }

    /**
     * Shortcut: Log hành động xóa
     */
    public static function logDelete($tableName, $recordId, $oldData = null) {
        self::log('delete', $tableName, $recordId, $oldData, null);
    }

    /**
     * Shortcut: Log đăng nhập
     */
    public static function logLogin($userId, $email) {
        self::log('login', 'users', $userId, null, ['email' => $email]);
    }

    /**
     * Shortcut: Log đăng xuất
     */
    public static function logLogout() {
        self::log('logout', 'users', $_SESSION['user']['id'] ?? null);
    }

    /**
     * Lấy danh sách log (có phân trang)
     * 
     * @param int $limit
     * @param int $offset
     * @return array
     */
    public static function getLogs($limit = 50, $offset = 0) {
        $db = new Database();
        $conn = $db->getConnection();
        $sql = "SELECT * FROM activity_logs ORDER BY created_at DESC LIMIT :limit OFFSET :offset";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Đếm tổng số log
     */
    public static function count() {
        $db = new Database();
        $conn = $db->getConnection();
        $stmt = $conn->query("SELECT COUNT(*) as total FROM activity_logs");
        return $stmt->fetch()['total'];
    }
}
