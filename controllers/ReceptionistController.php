<?php
/**
 * ReceptionistController - Quản lý Lễ tân (Admin only)
 */
require_once __DIR__ . '/../models/Receptionist.php';
require_once __DIR__ . '/../helpers/Security.php';

class ReceptionistController {
    private $receptionistModel;

    public function __construct() {
        $this->receptionistModel = new Receptionist();
    }

    public function index() {
        Security::requireRole(['admin', 'doctor', 'nurse', 'receptionist']);
        $receptionists = $this->receptionistModel->getAll();
        $pageTitle = 'Danh sách Lễ tân';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/receptionists/index.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    public function view() {
        Security::requireRole(['admin', 'doctor', 'nurse', 'receptionist']);
        $id = $_GET['id'] ?? 0;
        $receptionist = $this->receptionistModel->findById($id);

        if (!$receptionist) {
            $_SESSION['error'] = 'Không tìm thấy lễ tân.';
            header('Location: index.php?page=receptionists');
            exit;
        }

        $pageTitle = 'Chi tiết Lễ tân';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/receptionists/view.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    public function create() {
        Security::requireRole('admin');
        $pageTitle = 'Thêm Lễ tân mới';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/receptionists/create.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    public function store() {
        Security::requireRole('admin');
        Security::requirePost('index.php?page=receptionists');
        Security::requireCsrf();

        $data = [
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'] ?? '',
            'password' => '123456'
        ];
        $result = $this->receptionistModel->create($data);
        if ($result) {
            $_SESSION['success'] = 'Thêm lễ tân thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra. Email có thể đã tồn tại.';
        }
        
        header('Location: index.php?page=receptionists');
        exit;
    }

    public function edit() {
        Security::requireRole('admin');
        $id = $_GET['id'] ?? null;
        $receptionist = $this->receptionistModel->findById($id);
        if (!$receptionist) {
            $_SESSION['error'] = 'Không tìm thấy lễ tân.';
            header('Location: index.php?page=receptionists');
            exit;
        }
        $pageTitle = 'Sửa Lễ tân';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/receptionists/edit.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    public function update() {
        Security::requireRole('admin');
        Security::requirePost('index.php?page=receptionists');
        Security::requireCsrf();

        $id = $_POST['id'];
        $data = [
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'] ?? '',
        ];
        $this->receptionistModel->update($id, $data);
        $_SESSION['success'] = 'Cập nhật lễ tân thành công!';
        
        header('Location: index.php?page=receptionists');
        exit;
    }

    public function delete() {
        Security::requireRole('admin');
        Security::requirePost('index.php?page=receptionists');
        Security::requireCsrf();

        $id = $_POST['id'] ?? null;
        if ($id) {
            $this->receptionistModel->delete($id);
            $_SESSION['success'] = 'Đã xóa lễ tân.';
        }
        header('Location: index.php?page=receptionists');
        exit;
    }
}
