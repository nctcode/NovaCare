<?php
/**
 * Notification Model - Quản lý thông báo trong hệ thống
 * 
 * Chức năng:
 * - Nhắc nhở ca trực (BS/ĐD chưa đủ quota đêm)
 * - Thông báo được chỉ định trực
 * - Thông báo chung từ hệ thống (user_id = NULL)
 * - Quản lý trạng thái đã đọc/chưa đọc
 */
require_once __DIR__ . '/../config/database.php';

class Notification {
    private $conn;
    private $table = 'notifications';

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Lấy thông báo chưa đọc của user
     * (Hỗ trợ cả thông báo chung toàn hệ thống)
     * 
     * @param int $userId ID người dùng
     * @param int $limit Số lượng giới hạn (mặc định 5)
     * @return array Danh sách thông báo
     */
    public function getUnreadForUser($userId, $limit = 5) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE (user_id = :user_id OR user_id IS NULL) AND status = 'unread' 
                ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy thông báo chưa đọc của user (phiên bản cũ - giữ để tương thích)
     * 
     * @param int $userId ID người dùng
     * @return array Danh sách thông báo
     */
    public function getUnreadByUserId($userId) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id AND status = 'unread' ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy tất cả thông báo của user (phục vụ trang danh sách)
     * 
     * @param int $userId ID người dùng
     * @param int $limit Số lượng giới hạn (mặc định 50)
     * @return array Danh sách thông báo
     */
    public function getAllForUser($userId, $limit = 50) {
        $sql = "SELECT * FROM {$this->table} 
                WHERE (user_id = :user_id OR user_id IS NULL) 
                ORDER BY created_at DESC LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Lấy tất cả notification của user (có phân trang)
     * 
     * @param int $userId ID người dùng
     * @param int $limit Số lượng giới hạn (mặc định 20)
     * @return array Danh sách thông báo
     */
    public function getAllByUserId($userId, $limit = 20) {
        $sql = "SELECT * FROM {$this->table} WHERE user_id = :user_id ORDER BY created_at DESC LIMIT :lim";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindValue(':lim', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Đếm thông báo chưa đọc của user
     * 
     * @param int $userId ID người dùng
     * @return int Số lượng thông báo chưa đọc
     */
    public function countUnread($userId) {
        $sql = "SELECT COUNT(*) as cnt FROM {$this->table} 
                WHERE (user_id = :user_id OR user_id IS NULL) AND status = 'unread'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['cnt'] ?? 0;
    }

    /**
     * Tạo thông báo mới
     * 
     * @param int|null $userId ID người dùng (null cho thông báo toàn hệ thống)
     * @param string $title Tiêu đề thông báo
     * @param string $message Nội dung thông báo
     * @return int|bool ID thông báo vừa tạo hoặc false nếu thất bại
     */
    public function create($userId, $title, $message, $type = 'general') {
        $sql = "INSERT INTO {$this->table} (user_id, title, message, status, type) 
                VALUES (:user_id, :title, :message, 'unread', :type)";
        $stmt = $this->conn->prepare($sql);
        
        // Hỗ trợ NULL cho thông báo chung cho toàn hệ thống
        if ($userId === null) {
            $stmt->bindValue(':user_id', null, PDO::PARAM_NULL);
        } else {
            $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        }
        
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':message', $message);
        $stmt->bindParam(':type', $type);
        
        if ($stmt->execute()) {
            return $this->conn->lastInsertId();
        }
        return false;
    }

    /**
     * Đánh dấu 1 thông báo đã đọc
     * 
     * @param int $id ID thông báo
     * @return bool Kết quả thực thi
     */
    public function markAsRead($id) {
        $sql = "UPDATE {$this->table} SET status = 'read', read_at = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Đánh dấu tất cả đã đọc cho 1 user
     * 
     * @param int $userId ID người dùng
     * @return bool Kết quả thực thi
     */
    public function markAllAsReadForUser($userId) {
        $sql = "UPDATE {$this->table} SET status = 'read', read_at = NOW() 
                WHERE (user_id = :user_id OR user_id IS NULL) AND status = 'unread'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Đánh dấu tất cả đã đọc cho user (phiên bản cũ - giữ để tương thích)
     * 
     * @param int $userId ID người dùng
     * @return bool Kết quả thực thi
     */
    public function markAllAsRead($userId) {
        return $this->markAllAsReadForUser($userId);
    }

    /**
     * Xóa thông báo
     * 
     * @param int $id ID thông báo
     * @return bool Kết quả thực thi
     */
    public function delete($id) {
        $sql = "DELETE FROM {$this->table} WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Kiểm tra đã gửi notification nhắc ca trực tuần này chưa
     * (Tránh spam - chỉ gửi 1 lần/tuần cho mỗi user)
     * 
     * @param int $userId ID người dùng
     * @return bool Đã gửi hay chưa
     */
    public function hasShiftReminderThisWeek($userId) {
        $weekStart = date('Y-m-d', strtotime('monday this week'));
        $sql = "SELECT COUNT(*) as cnt FROM {$this->table} 
                WHERE user_id = :user_id 
                AND title LIKE '%ca trực đêm%' 
                AND created_at >= :week_start";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId, PDO::PARAM_INT);
        $stmt->bindParam(':week_start', $weekStart);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return ($result['cnt'] ?? 0) > 0;
    }

    // Lấy thông báo phân trang phục vụ API
    public function getNotificationsForApi($userId, $filters = []) {
        $page = isset($filters['page']) ? (int)$filters['page'] : 1;
        $limit = isset($filters['limit']) ? (int)$filters['limit'] : 10;
        if ($limit > 50) $limit = 50;
        $offset = ($page - 1) * $limit;

        $sql = "SELECT id, title, message, type, 
                       CASE WHEN status = 'read' THEN 1 ELSE 0 END as is_read, 
                       created_at
                FROM {$this->table} 
                WHERE (user_id = :user_id OR user_id IS NULL)";

        $params = [':user_id' => $userId];

        if (isset($filters['is_read'])) {
            $statusVal = $filters['is_read'] ? 'read' : 'unread';
            $sql .= " AND status = :status";
            $params[':status'] = $statusVal;
        }

        $sql .= " ORDER BY created_at DESC LIMIT :limit OFFSET :offset";

        $stmt = $this->conn->prepare($sql);
        foreach ($params as $key => $val) {
            $stmt->bindValue($key, $val);
        }
        $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        $notifications = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Convert is_read to boolean
        foreach ($notifications as &$noti) {
            $noti['is_read'] = (bool)$noti['is_read'];
            if (empty($noti['type'])) {
                $noti['type'] = 'general';
            }
        }

        // Đếm tổng
        $countSql = "SELECT COUNT(*) as total FROM {$this->table} WHERE (user_id = :user_id OR user_id IS NULL)";
        if (isset($filters['is_read'])) {
            $countSql .= " AND status = :status";
        }
        $countStmt = $this->conn->prepare($countSql);
        foreach ($params as $key => $val) {
            $countStmt->bindValue($key, $val);
        }
        $countStmt->execute();
        $total = (int)$countStmt->fetch()['total'];

        return [
            'data' => $notifications,
            'meta' => [
                'page' => $page,
                'limit' => $limit,
                'total' => $total,
                'total_pages' => ceil($total / $limit)
            ]
        ];
    }
}
?>