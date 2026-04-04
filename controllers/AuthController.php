<?php
/**
 * AuthController - Xử lý đăng nhập / đăng xuất
 */
require_once __DIR__ . '/../models/User.php';

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
            $email = trim($_POST['email'] ?? '');
            $password = trim($_POST['password'] ?? '');

            if (empty($email) || empty($password)) {
                $error = 'Vui lòng nhập email và mật khẩu.';
            } else {
                $user = $this->userModel->findByEmail($email);

                if ($user && $user['password'] === $password) {
                    // Đăng nhập thành công → lưu session
                    $_SESSION['user'] = [
                        'id'    => $user['id'],
                        'name'  => $user['name'],
                        'email' => $user['email'],
                        'role'  => $user['role'],
                        'phone' => $user['phone']
                    ];
                    header('Location: index.php?page=dashboard');
                    exit;
                } else {
                    $error = 'Email hoặc mật khẩu không đúng.';
                }
            }
        }

        require_once __DIR__ . '/../views/auth/login.php';
    }

    // Đăng xuất
    public function logout() {
        session_destroy();
        header('Location: index.php?page=login');
        exit;
    }
}
