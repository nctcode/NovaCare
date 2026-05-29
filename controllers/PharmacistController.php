<?php
/**
 * PharmacistController - Quản lý Dược sĩ (Admin only)
 */
require_once __DIR__ . '/../models/Pharmacist.php';
require_once __DIR__ . '/../helpers/Security.php';

class PharmacistController {
    private $pharmacistModel;

    public function __construct() {
        $this->pharmacistModel = new Pharmacist();
    }

    public function index() {
        Security::requireRole(['admin', 'doctor', 'nurse', 'receptionist']);
        $pharmacists = $this->pharmacistModel->getAll();
        $pageTitle = 'Danh sách Dược sĩ';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/pharmacists/index.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    public function view() {
        Security::requireRole(['admin', 'doctor', 'nurse', 'receptionist']);
        $id = $_GET['id'] ?? 0;
        $pharmacist = $this->pharmacistModel->findById($id);

        if (!$pharmacist) {
            $_SESSION['error'] = 'Không tìm thấy dược sĩ.';
            header('Location: index.php?page=pharmacists');
            exit;
        }

        $pageTitle = 'Chi tiết Dược sĩ';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/pharmacists/view.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    public function create() {
        Security::requireRole('admin');
        $pageTitle = 'Thêm Dược sĩ mới';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/pharmacists/create.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    public function store() {
        Security::requireRole('admin');
        Security::requirePost('index.php?page=pharmacists');
        Security::requireCsrf();

        $data = [
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'] ?? '',
            'password' => '123456'
        ];
        $result = $this->pharmacistModel->create($data);
        if ($result) {
            $_SESSION['success'] = 'Thêm dược sĩ thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra. Email có thể đã tồn tại.';
        }
        
        header('Location: index.php?page=pharmacists');
        exit;
    }

    public function edit() {
        Security::requireRole('admin');
        $id = $_GET['id'] ?? null;
        $pharmacist = $this->pharmacistModel->findById($id);
        if (!$pharmacist) {
            $_SESSION['error'] = 'Không tìm thấy dược sĩ.';
            header('Location: index.php?page=pharmacists');
            exit;
        }
        $pageTitle = 'Sửa Dược sĩ';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/pharmacists/edit.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    public function update() {
        Security::requireRole('admin');
        Security::requirePost('index.php?page=pharmacists');
        Security::requireCsrf();

        $id = $_POST['id'];
        $data = [
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'] ?? '',
        ];
        $this->pharmacistModel->update($id, $data);
        $_SESSION['success'] = 'Cập nhật dược sĩ thành công!';
        
        header('Location: index.php?page=pharmacists');
        exit;
    }

    public function delete() {
        Security::requireRole('admin');
        Security::requirePost('index.php?page=pharmacists');
        Security::requireCsrf();

        $id = $_POST['id'] ?? null;
        if ($id) {
            $this->pharmacistModel->delete($id);
            $_SESSION['success'] = 'Đã xóa dược sĩ.';
        }
        header('Location: index.php?page=pharmacists');
        exit;
    }
}
