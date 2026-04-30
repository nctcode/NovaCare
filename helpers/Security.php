<?php
/**
 * Security Helper - Các hàm bảo mật dùng chung cho hệ thống NovaCare
 * 
 * Bao gồm:
 * - Hash / Verify password (bcrypt)
 * - CSRF Token protection
 * - Phân quyền theo role
 * - Enforce HTTP method
 * - Sanitize input
 */

class Security {

    // ==========================================
    //  PASSWORD HASHING (bcrypt)
    // ==========================================

    /**
     * Hash password bằng bcrypt
     * @param string $password - Password plaintext
     * @return string - Hashed password
     */
    public static function hashPassword($password) {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
    }

    /**
     * Kiểm tra password có khớp hash không
     * @param string $password - Password plaintext
     * @param string $hash - Hashed password từ DB
     * @return bool
     */
    public static function verifyPassword($password, $hash) {
        return password_verify($password, $hash);
    }

    // ==========================================
    //  CSRF PROTECTION
    // ==========================================

    /**
     * Tạo CSRF token mới (nếu chưa có) và trả về token hiện tại
     * @return string
     */
    public static function getCsrfToken() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Tạo HTML hidden input cho CSRF token
     * Dùng trong form: <?= Security::csrfField() ?>
     * @return string
     */
    public static function csrfField() {
        $token = self::getCsrfToken();
        return '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($token) . '">';
    }

    /**
     * Validate CSRF token từ POST request
     * @return bool
     */
    public static function validateCsrf() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        $token = $_POST['csrf_token'] ?? '';
        $sessionToken = $_SESSION['csrf_token'] ?? '';
        
        if (empty($token) || empty($sessionToken)) {
            return false;
        }
        
        return hash_equals($sessionToken, $token);
    }

    /**
     * Kiểm tra CSRF và dừng nếu không hợp lệ
     * Gọi ở đầu mỗi action xử lý POST
     */
    public static function requireCsrf() {
        if (!self::validateCsrf()) {
            $_SESSION['error'] = 'Yêu cầu không hợp lệ (CSRF). Vui lòng thử lại.';
            header('Location: index.php?page=dashboard');
            exit;
        }
    }

    // ==========================================
    //  PHÂN QUYỀN (ROLE-BASED)
    // ==========================================

    /**
     * Kiểm tra user đã đăng nhập và có role phù hợp
     * @param string|array $allowedRoles - Role được phép (VD: 'admin' hoặc ['admin','doctor'])
     */
    public static function requireRole($allowedRoles) {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?page=login');
            exit;
        }

        $allowedRoles = (array) $allowedRoles;
        $userRole = $_SESSION['user']['role'] ?? '';

        if (!in_array($userRole, $allowedRoles)) {
            $_SESSION['error'] = 'Bạn không có quyền thực hiện hành động này.';
            header('Location: index.php?page=dashboard');
            exit;
        }
    }

    /**
     * Kiểm tra user có phải role cụ thể không (không redirect, trả về bool)
     * @param string|array $roles
     * @return bool
     */
    public static function hasRole($roles) {
        if (!isset($_SESSION['user'])) return false;
        $roles = (array) $roles;
        return in_array($_SESSION['user']['role'] ?? '', $roles);
    }

    // ==========================================
    //  HTTP METHOD ENFORCEMENT
    // ==========================================

    /**
     * Yêu cầu request phải là POST. Nếu không → redirect.
     * @param string $redirectUrl - URL redirect khi không phải POST
     */
    public static function requirePost($redirectUrl = 'index.php?page=dashboard') {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('Location: ' . $redirectUrl);
            exit;
        }
    }

    // ==========================================
    //  INPUT SANITIZE
    // ==========================================

    /**
     * Sanitize input: trim + htmlspecialchars
     * @param string $input
     * @return string
     */
    public static function sanitize($input) {
        if ($input === null) return '';
        return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sanitize mảng input
     * @param array $data
     * @param array $keys - Các key cần sanitize
     * @return array
     */
    public static function sanitizeArray($data, $keys) {
        foreach ($keys as $key) {
            if (isset($data[$key])) {
                $data[$key] = self::sanitize($data[$key]);
            }
        }
        return $data;
    }
}
