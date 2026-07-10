<?php
/**
 * RoleMiddleware - Phân quyền vai trò người dùng cho API endpoints.
 */
require_once __DIR__ . '/../core/ApiResponse.php';
require_once __DIR__ . '/AuthMiddleware.php';

class RoleMiddleware {

    /**
     * Chạy middleware phân quyền
     * @param string|array $allowedRoles - Các role được phép truy cập
     */
    public static function handle($allowedRoles) {
        // Đảm bảo đã chạy qua AuthMiddleware trước
        $user = AuthMiddleware::$currentUser ?? $_REQUEST['current_user'] ?? null;

        if (!$user) {
            ApiResponse::error('Chưa xác thực hoặc mất ngữ cảnh người dùng.', 401);
        }

        $allowedRoles = (array) $allowedRoles;
        $userRole = $user['role'] ?? '';

        if (!in_array($userRole, $allowedRoles)) {
            ApiResponse::error('Bạn không có quyền truy cập tài nguyên này.', 403);
        }
    }
}
