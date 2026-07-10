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
     * @param string|null $logType - Loại log: auth, data_change, system
     */
    public static function log($action, $tableName = null, $recordId = null, $oldData = null, $newData = null, $logType = null, $userId = null) {
        try {
            $db = new Database();
            $conn = $db->getConnection();

            // Lấy thông tin user từ parameter hoặc session
            $userId = $userId ?? $_SESSION['user']['id'] ?? null;
            
            // Lấy IP
            $ipAddress = $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';

            // Xác định log type nếu không truyền vào
            if (!$logType) {
                if (in_array($action, ['LOGIN', 'LOGOUT'])) {
                    $logType = 'auth';
                } else {
                    $logType = 'data_change';
                }
            }

            $sql = "INSERT INTO audit_logs (user_id, log_type, action, table_name, record_id, old_values, new_values, ip_address) 
                    VALUES (:user_id, :log_type, :action, :table_name, :record_id, :old_values, :new_values, :ip_address)";
            
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':log_type', $logType);
            $stmt->bindParam(':action', $action);
            $stmt->bindParam(':table_name', $tableName);
            $stmt->bindParam(':record_id', $recordId);
            
            $oldJson = $oldData ? json_encode($oldData, JSON_UNESCAPED_UNICODE) : null;
            $newJson = $newData ? json_encode($newData, JSON_UNESCAPED_UNICODE) : null;
            $stmt->bindParam(':old_values', $oldJson);
            $stmt->bindParam(':new_values', $newJson);
            $stmt->bindParam(':ip_address', $ipAddress);
            
            $stmt->execute();
        } catch (Exception $e) {
            // Không để lỗi audit làm crash ứng dụng
            error_log("AuditLog Error: " . $e->getMessage());
        }
    }

    /**
     * Shortcut: Log hành động tạo mới
     */
    public static function logCreate($tableName, $recordId, $newData = null, $userId = null) {
        self::log('INSERT', $tableName, $recordId, null, $newData, null, $userId);
    }

    /**
     * Shortcut: Log hành động cập nhật
     */
    public static function logUpdate($tableName, $recordId, $oldData = null, $newData = null, $userId = null) {
        self::log('UPDATE', $tableName, $recordId, $oldData, $newData, null, $userId);
    }

    /**
     * Shortcut: Log hành động xóa (soft delete)
     */
    public static function logDelete($tableName, $recordId, $oldData = null, $userId = null) {
        self::log('DELETE', $tableName, $recordId, $oldData, null, null, $userId);
    }

    /**
     * Shortcut: Log đăng nhập
     */
    public static function logLogin($userId, $email) {
        self::log('LOGIN', 'users', $userId, null, ['email' => $email], null, $userId);
    }

    /**
     * Shortcut: Log đăng xuất
     */
    public static function logLogout($userId = null) {
        self::log('LOGOUT', 'users', $userId ?? $_SESSION['user']['id'] ?? null, null, null, null, $userId);
    }

    /**
     * Lấy danh sách log (có phân trang + bộ lọc)
     * 
     * @param int $limit
     * @param int $offset
     * @param array $filters - ['action', 'table_name', 'user_name', 'date_from', 'date_to']
     */
    public static function getLogs($limit = 50, $offset = 0, $filters = []) {
        $db = new Database();
        $conn = $db->getConnection();

        $where = [];
        $params = [];

        if (!empty($filters['log_type'])) {
            $where[] = "al.log_type = :log_type";
            $params[':log_type'] = $filters['log_type'];
        }
        if (!empty($filters['action'])) {
            $where[] = "al.action = :action";
            $params[':action'] = $filters['action'];
        }
        if (!empty($filters['table_name'])) {
            $where[] = "al.table_name = :table_name";
            $params[':table_name'] = $filters['table_name'];
        }
        if (!empty($filters['user_name'])) {
            $where[] = "u.name LIKE :user_name";
            $params[':user_name'] = '%' . $filters['user_name'] . '%';
        }
        if (!empty($filters['date_from'])) {
            $where[] = "DATE(al.created_at) >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $where[] = "DATE(al.created_at) <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "SELECT al.*, u.name as user_name 
                FROM audit_logs al 
                LEFT JOIN users u ON al.user_id = u.id 
                $whereClause
                ORDER BY al.created_at DESC 
                LIMIT :limit OFFSET :offset";

        $stmt = $conn->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Đếm tổng số log (có bộ lọc)
     */
    public static function count($filters = []) {
        $db = new Database();
        $conn = $db->getConnection();

        $where = [];
        $params = [];

        if (!empty($filters['log_type'])) {
            $where[] = "al.log_type = :log_type";
            $params[':log_type'] = $filters['log_type'];
        }
        if (!empty($filters['action'])) {
            $where[] = "al.action = :action";
            $params[':action'] = $filters['action'];
        }
        if (!empty($filters['table_name'])) {
            $where[] = "al.table_name = :table_name";
            $params[':table_name'] = $filters['table_name'];
        }
        if (!empty($filters['user_name'])) {
            $where[] = "u.name LIKE :user_name";
            $params[':user_name'] = '%' . $filters['user_name'] . '%';
        }
        if (!empty($filters['date_from'])) {
            $where[] = "DATE(al.created_at) >= :date_from";
            $params[':date_from'] = $filters['date_from'];
        }
        if (!empty($filters['date_to'])) {
            $where[] = "DATE(al.created_at) <= :date_to";
            $params[':date_to'] = $filters['date_to'];
        }

        $whereClause = !empty($where) ? 'WHERE ' . implode(' AND ', $where) : '';

        $sql = "SELECT COUNT(*) as total 
                FROM audit_logs al 
                LEFT JOIN users u ON al.user_id = u.id 
                $whereClause";

        $stmt = $conn->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->execute();
        return $stmt->fetch()['total'];
    }

    /**
     * Lấy danh sách các bảng đã có trong audit log (cho dropdown filter)
     */
    public static function getDistinctTables() {
        $db = new Database();
        $conn = $db->getConnection();
        $stmt = $conn->query("SELECT DISTINCT table_name FROM audit_logs ORDER BY table_name ASC");
        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
