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

    // ==========================================
    //  IDOR PROTECTION (Data-Level Security)
    // ==========================================

    /**
     * Lấy profile ID (patient_id hoặc doctor_id) của user đang đăng nhập.
     * Dùng để so sánh với owner_id trên dữ liệu.
     * 
     * @param string|null $forceRole - Ép role cần tìm (nếu null, dùng role thực tế)
     * @return int|null
     */
    public static function getUserProfileId($forceRole = null) {
        if (!isset($_SESSION['user'])) return null;
        $user = $_SESSION['user'];
        $role = $forceRole ?? $user['role'];

        require_once __DIR__ . '/../config/database.php';
        $db = new Database();
        $conn = $db->getConnection();

        switch ($role) {
            case 'patient':
                $stmt = $conn->prepare("SELECT id FROM patients WHERE user_id = :uid");
                $stmt->execute([':uid' => $user['id']]);
                $row = $stmt->fetch();
                return $row ? (int)$row['id'] : null;

            case 'doctor':
                $stmt = $conn->prepare("SELECT id FROM doctors WHERE user_id = :uid");
                $stmt->execute([':uid' => $user['id']]);
                $row = $stmt->fetch();
                return $row ? (int)$row['id'] : null;

            case 'nurse':
                $stmt = $conn->prepare("SELECT id FROM nurses WHERE user_id = :uid");
                $stmt->execute([':uid' => $user['id']]);
                $row = $stmt->fetch();
                return $row ? (int)$row['id'] : null;

            default:
                return null;
        }
    }

    /**
     * IDOR Check: Kiểm tra user có quyền truy cập dữ liệu cụ thể không.
     * So sánh owner_id trên dữ liệu với profile_id của user đang đăng nhập.
     * 
     * @param int $ownerId - ID chủ sở hữu trên dữ liệu (VD: invoice.patient_id)
     * @param array $allowedRoles - Các role được quyền truy cập tất cả (VD: ['admin', 'receptionist'])
     * @param string $ownerRole - Role sở hữu dữ liệu (default: dùng role hiện tại của user)
     * @return bool - true nếu được phép, false nếu không
     */
    public static function authorizeOwnership($ownerId, $allowedRoles = ['admin'], $ownerRole = null) {
        if (!isset($_SESSION['user'])) return false;
        $user = $_SESSION['user'];

        // Các role quản trị luôn được phép xem tất cả
        if (in_array($user['role'], (array)$allowedRoles)) {
            return true;
        }

        // So sánh profile ID của user hiện tại với owner_id
        $profileId = self::getUserProfileId($ownerRole);
        return $profileId !== null && (int)$ownerId === $profileId;
    }

    /**
     * IDOR Check + Redirect: Kiểm tra quyền sở hữu, redirect nếu thất bại.
     * 
     * @param int $ownerId - ID chủ sở hữu trên dữ liệu
     * @param array $allowedRoles - Các role quản trị được truy cập tất cả
     * @param string $redirectUrl - URL redirect khi bị từ chối
     * @param string|null $ownerRole - Role sở hữu
     */
    public static function requireOwnership($ownerId, $allowedRoles = ['admin'], $redirectUrl = 'index.php?page=dashboard', $ownerRole = null) {
        if (!self::authorizeOwnership($ownerId, $allowedRoles, $ownerRole)) {
            $_SESSION['error'] = 'Bạn không có quyền truy cập dữ liệu này.';
            header('Location: ' . $redirectUrl);
            exit;
        }
    }

    // ==========================================
    //  TRƯỞNG KHOA / ĐIỀU DƯỠNG TRƯỞNG
    // ==========================================

    /**
     * Kiểm tra user hiện tại có phải Trưởng khoa (is_head) không.
     * Trả về mảng thông tin nếu đúng, false nếu không.
     * 
     * @return array|false - Thông tin doctor/nurse row (bao gồm department_id) hoặc false
     */
    public static function isHeadOfDepartment() {
        if (!isset($_SESSION['user'])) return false;
        $user = $_SESSION['user'];

        require_once __DIR__ . '/../config/database.php';
        $db = new Database();
        $conn = $db->getConnection();

        if ($user['role'] === 'doctor') {
            $stmt = $conn->prepare("SELECT d.*, dep.name as department_name FROM doctors d LEFT JOIN departments dep ON d.department_id = dep.id WHERE d.user_id = :uid AND d.is_head = 1 AND d.deleted_at IS NULL LIMIT 1");
            $stmt->execute([':uid' => $user['id']]);
            return $stmt->fetch() ?: false;
        }

        if ($user['role'] === 'nurse') {
            $stmt = $conn->prepare("SELECT n.*, dep.name as department_name FROM nurses n LEFT JOIN departments dep ON n.department_id = dep.id WHERE n.user_id = :uid AND n.is_head = 1 LIMIT 1");
            $stmt->execute([':uid' => $user['id']]);
            return $stmt->fetch() ?: false;
        }

        return false;
    }

    /**
     * Yêu cầu user phải là Trưởng khoa / Điều dưỡng trưởng.
     * Redirect nếu không đủ quyền.
     * 
     * @param string $redirectUrl
     */
    public static function requireHeadRole($redirectUrl = 'index.php?page=shifts') {
        if (!isset($_SESSION['user'])) {
            header('Location: index.php?page=login');
            exit;
        }

        $headInfo = self::isHeadOfDepartment();
        if (!$headInfo) {
            $_SESSION['error'] = 'Chỉ Trưởng khoa hoặc Điều dưỡng trưởng mới có quyền thực hiện hành động này.';
            header('Location: ' . $redirectUrl);
            exit;
        }
        return $headInfo;
    }
}
