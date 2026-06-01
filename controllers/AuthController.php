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

    // Hiển thị và xử lý form đăng ký
    public function register() {
        // Nếu đã login → về dashboard
        if (isset($_SESSION['user'])) {
            header('Location: index.php?page=dashboard');
            exit;
        }

        require_once __DIR__ . '/../models/Patient.php';
        $patientModel = new Patient();

        $error = '';

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            if (!Security::validateCsrf()) {
                $error = 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang (F5) và thử lại.';
            } else {
                $name = trim($_POST['name'] ?? '');
                $email = trim($_POST['email'] ?? '');
                $phone = trim($_POST['phone'] ?? '');
                $password = $_POST['password'] ?? '';
                $confirm_password = $_POST['confirm_password'] ?? '';

                if (empty($name) || empty($email) || empty($password) || empty($confirm_password)) {
                    $error = 'Vui lòng nhập đầy đủ họ tên, email và mật khẩu.';
                } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $error = 'Định dạng email không hợp lệ.';
                } elseif ($password !== $confirm_password) {
                    $error = 'Mật khẩu xác nhận không khớp.';
                } elseif (strlen($password) < 6) {
                    $error = 'Mật khẩu phải có ít nhất 6 ký tự.';
                } else {
                    // Kiểm tra email tồn tại
                    $existingUser = $this->userModel->findByEmail($email);
                    if ($existingUser) {
                        $error = 'Email này đã được sử dụng. Vui lòng chọn email khác hoặc đăng nhập.';
                    } else {
                        // Tạo tài khoản và bệnh nhân
                        $data = [
                            'name' => $name,
                            'email' => $email,
                            'phone' => $phone,
                            'password' => $password,
                            'role' => 'patient', // Fix cứng role bệnh nhân
                            'date_of_birth' => null,
                            'gender' => 'other',
                            'address' => null,
                            'blood_type' => null,
                            'medical_history' => null
                        ];

                        try {
                            $patientId = $patientModel->create($data);
                            
                            // Lấy user vừa tạo để login
                            $newUser = $this->userModel->findByEmail($email);
                            if ($newUser) {
                                session_regenerate_id(true);
                                $_SESSION['user'] = [
                                    'id'    => $newUser['id'],
                                    'name'  => $newUser['name'],
                                    'email' => $newUser['email'],
                                    'role'  => $newUser['role'],
                                    'phone' => $newUser['phone']
                                ];
                                AuditLog::logLogin($newUser['id'], $newUser['email']);
                                // Đăng ký thành công, tự động chuyển về dashboard
                                $_SESSION['success'] = 'Đăng ký tài khoản thành công! Chào mừng bạn đến với NovaCare.';
                                header('Location: index.php?page=dashboard');
                                exit;
                            }
                        } catch (Exception $e) {
                            $error = 'Đã xảy ra lỗi hệ thống khi tạo tài khoản. Vui lòng thử lại sau.';
                            error_log("Register Error: " . $e->getMessage());
                        }
                    }
                }
            }
        }

        require_once __DIR__ . '/../views/auth/register.php';
    }
}
