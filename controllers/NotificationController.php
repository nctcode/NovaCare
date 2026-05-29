<?php
/**
 * NotificationController - Điều khiển các hành động thông báo
 */
require_once __DIR__ . '/../models/Notification.php';
require_once __DIR__ . '/../helpers/Security.php';

class NotificationController {
    private $notificationModel;

    public function __construct() {
        $this->notificationModel = new Notification();
    }

    /**
     * Danh sách toàn bộ thông báo (lịch sử)
     */
    public function index() {
        $user = $_SESSION['user'];
        $notifications = $this->notificationModel->getAllForUser($user['id']);

        $pageTitle = 'Lịch sử Thông báo';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/notifications/index.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    /**
     * Đánh dấu đã đọc
     */
    public function markAsRead() {
        Security::requirePost();
        Security::requireCsrf();

        $id = intval($_POST['id'] ?? 0);
        $result = false;
        if ($id > 0) {
            $result = $this->notificationModel->markAsRead($id);
        }

        // Kiểm tra xem là gọi AJAX hay request thông thường
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => $result]);
            exit;
        }

        $_SESSION['success'] = 'Đã đánh dấu thông báo là đã đọc.';
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php?page=notifications'));
        exit;
    }

    /**
     * Đánh dấu tất cả đã đọc
     */
    public function markAllAsRead() {
        Security::requirePost();
        Security::requireCsrf();

        $user = $_SESSION['user'];
        $result = $this->notificationModel->markAllAsReadForUser($user['id']);

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => $result]);
            exit;
        }

        $_SESSION['success'] = 'Đã đánh dấu tất cả thông báo là đã đọc.';
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php?page=notifications'));
        exit;
    }

    /**
     * Xóa thông báo
     */
    public function delete() {
        Security::requirePost();
        Security::requireCsrf();

        $id = intval($_POST['id'] ?? 0);
        $result = false;
        if ($id > 0) {
            $result = $this->notificationModel->delete($id);
        }

        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
            header('Content-Type: application/json');
            echo json_encode(['success' => $result]);
            exit;
        }

        $_SESSION['success'] = 'Đã xóa thông báo.';
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? 'index.php?page=notifications'));
        exit;
    }
}
