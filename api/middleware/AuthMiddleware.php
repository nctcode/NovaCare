<?php
/**
 * AuthMiddleware - Xác thực JWT Access Token gửi lên từ Mobile App.
 */
require_once __DIR__ . '/../core/ApiResponse.php';
require_once __DIR__ . '/../core/JWTHelper.php';

class AuthMiddleware {

    public static $currentUser = null;

    /**
     * Chạy middleware xác thực
     * @return array - Trả về payload JWT đã giải mã nếu hợp lệ
     */
    public static function handle() {
        // Lấy headers
        $headers = self::getRequestHeaders();
        $authHeader = $headers['Authorization'] ?? $headers['authorization'] ?? $_SERVER['HTTP_AUTHORIZATION'] ?? '';

        if (empty($authHeader) || !preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            ApiResponse::error('Chưa xác thực. Thiếu Access Token.', 401);
        }

        $token = $matches[1];
        $payload = JWTHelper::verifyAccessToken($token);

        if (!$payload) {
            ApiResponse::error('Access Token không hợp lệ hoặc đã hết hạn.', 401);
        }

        // Lưu thông tin user vào context tĩnh để sử dụng trong controller
        self::$currentUser = $payload;
        $_REQUEST['current_user'] = $payload;

        return $payload;
    }

    /**
     * Lấy danh sách các Request Headers tương thích với mọi Web server (Apache/Nginx)
     */
    private static function getRequestHeaders() {
        if (function_exists('getallheaders')) {
            return getallheaders();
        }

        $headers = [];
        foreach ($_SERVER as $name => $value) {
            if (substr($name, 0, 5) == 'HTTP_') {
                $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
            }
        }
        return $headers;
    }
}
