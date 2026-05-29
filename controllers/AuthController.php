<?php
/**
 * AuthController - Xử lý đăng nhập / đăng xuất
 * 
 * Bảo mật: bcrypt password, CSRF token
 */
require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../helpers/Security.php';
require_once __DIR__ . '/../helpers/AuditLog.php';

class AuthController {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    // Hiển thị form login
    public function index() {
        // Nếu đã login → về dashboard
        if (isset($_SESSION['user'])) {
            header('Location: index.php?page=dashboard');
            exit;
        }

        $error = '';

        // Xử lý POST (submit form login)
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Validate CSRF
            if (!Security::validateCsrf()) {
                $error = 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang (F5) và thử lại.';
            } else {
                $email = trim($_POST['email'] ?? '');
                $password = trim($_POST['password'] ?? '');

                if (empty($email) || empty($password)) {
                    $error = 'Vui lòng nhập email và mật khẩu.';
                } else {
                    $user = $this->userModel->findByEmail($email);

                    if ($user) {
                        $passwordValid = false;

                        // Kiểm tra password (hỗ trợ cả bcrypt hash và plaintext cũ)
                        if (password_get_info($user['password'])['algo'] !== null 
                            && password_get_info($user['password'])['algo'] !== 0) {
                            // Password đã được hash → dùng password_verify
                            $passwordValid = Security::verifyPassword($password, $user['password']);
                        } else {
                            // Password plaintext cũ → so sánh trực tiếp
                            $passwordValid = ($user['password'] === $password);
                            
                            // Tự động hash password cũ nếu đăng nhập thành công
                            if ($passwordValid) {
                                $hashedPassword = Security::hashPassword($password);
                                $this->userModel->updatePassword($user['id'], $hashedPassword);
                            }
                        }

                        if ($passwordValid) {
                            // Đăng nhập thành công → regenerate session ID (chống session fixation)
                            session_regenerate_id(true);
                            
                            $_SESSION['user'] = [
                                'id'    => $user['id'],
                                'name'  => $user['name'],
                                'email' => $user['email'],
                                'role'  => $user['role'],
                                'phone' => $user['phone']
                            ];
                            AuditLog::logLogin($user['id'], $user['email']);
                            header('Location: index.php?page=dashboard');
                            exit;
                        }
                    }
                    
                    $error = 'Email hoặc mật khẩu không đúng.';
                }
            }
        }

        require_once __DIR__ . '/../views/auth/login.php';
    }

    // Đăng xuất
    public function logout() {
        AuditLog::logLogout();
        session_destroy();
        header('Location: index.php?page=login');
        exit;
    }
}
