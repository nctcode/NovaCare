<?php
/**
 * AuditLog Helper - Ghi lại mọi hành động trong hệ thống NovaCare
 * 
 * Đây là yêu cầu pháp lý cho hệ thống y tế:
 * - Ai đã làm gì, lúc nào
 * - Dữ liệu cũ và mới (cho truy vết)
 * 
 * Sử dụng bảng: audit_logs (InnoDB)
 */
require_once __DIR__ . '/../config/database.php';

class AuditLog {

    /**
     * Ghi log hành động vào bảng audit_logs
     * 
     * @param string $action - Loại hành động: INSERT, UPDATE, DELETE, LOGIN, LOGOUT
     * @param string|null $tableName - Tên bảng liên quan
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

            $sql = "INSERT INTO audit_logs (user_id, action, table_name, record_id, old_values, new_values) 
                    VALUES (:user_id, :action, :table_name, :record_id, :old_values, :new_values)";
            
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':action', $action);
            $stmt->bindParam(':table_name', $tableName);
            $stmt->bindParam(':record_id', $recordId);
            
            $oldJson = $oldData ? json_encode($oldData, JSON_UNESCAPED_UNICODE) : null;
            $newJson = $newData ? json_encode($newData, JSON_UNESCAPED_UNICODE) : null;
            $stmt->bindParam(':old_values', $oldJson);
            $stmt->bindParam(':new_values', $newJson);
            
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
        self::log('INSERT', $tableName, $recordId, null, $newData);
    }

    /**
     * Shortcut: Log hành động cập nhật
     */
    public static function logUpdate($tableName, $recordId, $oldData = null, $newData = null) {
        self::log('UPDATE', $tableName, $recordId, $oldData, $newData);
    }

    /**
     * Shortcut: Log hành động xóa (soft delete)
     */
    public static function logDelete($tableName, $recordId, $oldData = null) {
        self::log('DELETE', $tableName, $recordId, $oldData, null);
    }

    /**
     * Shortcut: Log đăng nhập
     */
    public static function logLogin($userId, $email) {
        self::log('LOGIN', 'users', $userId, null, ['email' => $email]);
    }

    /**
     * Shortcut: Log đăng xuất
     */
    public static function logLogout() {
        self::log('LOGOUT', 'users', $_SESSION['user']['id'] ?? null);
    }

    /**
     * Lấy danh sách log (có phân trang)
     */
    public static function getLogs($limit = 50, $offset = 0) {
        $db = new Database();
        $conn = $db->getConnection();
        $sql = "SELECT al.*, u.name as user_name 
                FROM audit_logs al 
                LEFT JOIN users u ON al.user_id = u.id 
                ORDER BY al.created_at DESC LIMIT :limit OFFSET :offset";
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
        $stmt = $conn->query("SELECT COUNT(*) as total FROM audit_logs");
        return $stmt->fetch()['total'];
    }
}
