<?php
/**
 * UserController - Quản lý tài khoản người dùng và phân quyền
 */
require_once 'models/User.php';
require_once 'helpers/Security.php';
require_once 'helpers/AuditLog.php';

class UserController {
    private $userModel;

    public function __construct() {
        if (!isset($_SESSION['user']) || !Security::hasRole('admin')) {
            header('Location: index.php?page=login');
            exit;
        }
        $this->userModel = new User();
    }

    // Hiển thị danh sách người dùng
    public function index() {
        $users = $this->userModel->getAll();
        require 'views/users/index.php';
    }

    // Hiển thị form thêm mới
    public function create() {
        require 'views/users/create.php';
    }

    // Xử lý thêm mới
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Security::verifyCsrfToken($_POST['csrf_token'] ?? '');

            $data = [
                'name' => $_POST['name'] ?? '',
                'email' => $_POST['email'] ?? '',
                'password' => $_POST['password'] ?? '123456',
                'phone' => $_POST['phone'] ?? '',
                'role' => $_POST['role'] ?? 'patient'
            ];

            // Validate
            if (empty($data['name']) || empty($data['email']) || empty($data['role'])) {
                $_SESSION['error'] = 'Vui lòng nhập đầy đủ thông tin bắt buộc!';
                header('Location: index.php?page=users&action=create');
                exit;
            }

            // Kiểm tra email tồn tại
            if ($this->userModel->findByEmail($data['email'])) {
                $_SESSION['error'] = 'Email này đã được sử dụng!';
                header('Location: index.php?page=users&action=create');
                exit;
            }

            try {
                $userId = $this->userModel->create($data);
                
                AuditLog::log('user_created', 'Created new user: ' . $data['email'], $userId, 'users', [
                    'name' => $data['name'],
                    'role' => $data['role']
                ]);

                $_SESSION['success'] = 'Thêm tài khoản thành công!';
                header('Location: index.php?page=users');
                exit;
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi khi tạo tài khoản: ' . $e->getMessage();
                header('Location: index.php?page=users&action=create');
                exit;
            }
        }
    }

    // Hiển thị form cập nhật
    public function edit() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?page=users');
            exit;
        }

        $editUser = $this->userModel->findById($id);
        if (!$editUser) {
            $_SESSION['error'] = 'Không tìm thấy tài khoản!';
            header('Location: index.php?page=users');
            exit;
        }

        require 'views/users/edit.php';
    }

    // Xử lý cập nhật
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Security::verifyCsrfToken($_POST['csrf_token'] ?? '');

            $id = $_POST['id'] ?? null;
            if (!$id) {
                header('Location: index.php?page=users');
                exit;
            }

            $editUser = $this->userModel->findById($id);
            if (!$editUser) {
                $_SESSION['error'] = 'Không tìm thấy tài khoản!';
                header('Location: index.php?page=users');
                exit;
            }

            $data = [
                'name' => $_POST['name'] ?? $editUser['name'],
                'email' => $_POST['email'] ?? $editUser['email'],
                'phone' => $_POST['phone'] ?? $editUser['phone'],
                'role' => $_POST['role'] ?? $editUser['role']
            ];

            // Nếu đổi email, kiểm tra xem email mới đã tồn tại chưa
            if ($data['email'] !== $editUser['email']) {
                if ($this->userModel->findByEmail($data['email'])) {
                    $_SESSION['error'] = 'Email này đã được sử dụng!';
                    header('Location: index.php?page=users&action=edit&id=' . $id);
                    exit;
                }
            }

            try {
                $this->userModel->update($id, $data);
                
                // Cập nhật mật khẩu nếu có
                if (!empty($_POST['password'])) {
                    $hashedPassword = Security::hashPassword($_POST['password']);
                    $this->userModel->updatePassword($id, $hashedPassword);
                }

                AuditLog::log('user_updated', 'Updated user: ' . $data['email'], $id, 'users', $data);

                $_SESSION['success'] = 'Cập nhật tài khoản thành công!';
                header('Location: index.php?page=users');
                exit;
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi khi cập nhật tài khoản: ' . $e->getMessage();
                header('Location: index.php?page=users&action=edit&id=' . $id);
                exit;
            }
        }
    }

    // Xử lý xóa
    public function delete() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            Security::verifyCsrfToken($_POST['csrf_token'] ?? '');

            $id = $_POST['id'] ?? null;
            
            // Không cho phép xóa chính mình
            if ($id == $_SESSION['user']['id']) {
                $_SESSION['error'] = 'Bạn không thể xóa tài khoản của chính mình!';
                header('Location: index.php?page=users');
                exit;
            }

            try {
                $this->userModel->delete($id);
                AuditLog::log('user_deleted', 'Deleted user ID: ' . $id, $id, 'users');
                $_SESSION['success'] = 'Xóa tài khoản thành công!';
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi khi xóa tài khoản!';
            }
            header('Location: index.php?page=users');
            exit;
        }
    }
}
