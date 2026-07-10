<?php
/**
 * ApiNotificationController - Quản lý thông báo cho API v1
 */
require_once __DIR__ . '/../core/BaseApiController.php';
require_once __DIR__ . '/../../models/Notification.php';

class ApiNotificationController extends BaseApiController {

    private $notificationModel;

    public function __construct() {
        $this->notificationModel = new Notification();
    }

    /**
     * GET /api/v1/notifications
     */
    public function index() {
        $user = AuthMiddleware::$currentUser;
        if (!$user) {
            return $this->sendUnauthorized();
        }

        $isReadParam = $_GET['is_read'] ?? null;
        $isRead = null;
        if ($isReadParam === 'true' || $isReadParam === '1') {
            $isRead = true;
        } elseif ($isReadParam === 'false' || $isReadParam === '0') {
            $isRead = false;
        }

        $filters = [
            'is_read' => $isRead,
            'page'    => $_GET['page'] ?? 1,
            'limit'   => $_GET['limit'] ?? 10
        ];

        $result = $this->notificationModel->getNotificationsForApi($user['user_id'], $filters);

        return $this->sendSuccess(
            $result['data'],
            'Lấy danh sách thông báo thành công',
            200,
            $result['meta']
        );
    }

    /**
     * GET /api/v1/notifications/unread-count
     */
    public function unreadCount() {
        $user = AuthMiddleware::$currentUser;
        if (!$user) {
            return $this->sendUnauthorized();
        }

        $count = $this->notificationModel->countUnread($user['user_id']);

        return $this->sendSuccess([
            'unread_count' => (int)$count
        ], 'Lấy số lượng thông báo chưa đọc thành công');
    }

    /**
     * POST /api/v1/notifications/{id}/read
     */
    public function read($id) {
        $user = AuthMiddleware::$currentUser;
        if (!$user) {
            return $this->sendUnauthorized();
        }

        $db = new Database();
        $conn = $db->getConnection();

        // 1. Kiểm tra sự tồn tại của thông báo
        $stmt = $conn->prepare("SELECT user_id FROM notifications WHERE id = :id");
        $stmt->execute([':id' => $id]);
        $noti = $stmt->fetch();

        if (!$noti) {
            return $this->sendNotFound('Thông báo không tồn tại.');
        }

        // 2. Chống IDOR: Chỉ cho phép đánh dấu đọc của chính mình
        if ((int)$noti['user_id'] !== (int)$user['user_id']) {
            return $this->sendForbidden('Bạn không có quyền đánh dấu đã đọc cho thông báo này.');
        }

        $result = $this->notificationModel->markAsRead($id);

        if ($result) {
            return $this->sendSuccess(null, 'Đã đánh dấu thông báo là đã đọc.');
        }

        return $this->sendServerError('Đã xảy ra lỗi khi cập nhật thông báo.');
    }

    /**
     * POST /api/v1/notifications/read-all
     */
    public function readAll() {
        $user = AuthMiddleware::$currentUser;
        if (!$user) {
            return $this->sendUnauthorized();
        }

        $result = $this->notificationModel->markAllAsReadForUser($user['user_id']);

        if ($result) {
            return $this->sendSuccess(null, 'Đã đánh dấu tất cả thông báo của bạn là đã đọc.');
        }

        return $this->sendServerError('Đã xảy ra lỗi khi cập nhật các thông báo.');
    }
}
