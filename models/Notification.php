<?php
/**
 * Notification Model - Quản lý thông báo hệ thống
 * 
 * Dùng cho:
 * - Nhắc nhở ca trực (BS/ĐD chưa đủ quota đêm)
 * - Thông báo được chỉ định trực
 * - Thông báo chung từ hệ thống
 */
require_once __DIR__ . '/../config/database.php';

class Notification {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    /**
     * Tạo thông báo mới
     */
    public function create($userId, $title, $message) {
        $sql = "INSERT INTO notifications (user_id, title, message) VALUES (:user_id, :title, :message)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':message', $message);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    /**
     * Lấy thông báo chưa đọc của user
     */
    public function getUnreadByUserId($userId) {
        $sql = "SELECT * FROM notifications WHERE user_id = :user_id AND status = 'unread' ORDER BY created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    /**
     * Đếm thông báo chưa đọc
     */
    public function countUnread($userId) {
        $sql = "SELECT COUNT(*) as cnt FROM notifications WHERE user_id = :user_id AND status = 'unread'";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetch()['cnt'];
    }

    /**
     * Đánh dấu đã đọc
     */
    public function markAsRead($id) {
        $sql = "UPDATE notifications SET status = 'read' WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':id' => $id]);
    }

    /**
     * Đánh dấu tất cả đã đọc cho user
     */
    public function markAllAsRead($userId) {
        $sql = "UPDATE notifications SET status = 'read' WHERE user_id = :user_id AND status = 'unread'";
        $stmt = $this->conn->prepare($sql);
        return $stmt->execute([':user_id' => $userId]);
    }

    /**
     * Kiểm tra đã gửi notification nhắc ca trực tuần này chưa
     * (Tránh spam - chỉ gửi 1 lần/tuần cho mỗi user)
     */
    public function hasShiftReminderThisWeek($userId) {
        $weekStart = date('Y-m-d', strtotime('monday this week'));
        $sql = "SELECT COUNT(*) as cnt FROM notifications 
                WHERE user_id = :user_id 
                AND title LIKE '%ca trực đêm%' 
                AND created_at >= :week_start";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':week_start', $weekStart);
        $stmt->execute();
        return $stmt->fetch()['cnt'] > 0;
    }

    /**
     * Lấy tất cả notification của user (có phân trang)
     */
    public function getAllByUserId($userId, $limit = 20) {
        $sql = "SELECT * FROM notifications WHERE user_id = :user_id ORDER BY created_at DESC LIMIT :lim";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':lim', $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
