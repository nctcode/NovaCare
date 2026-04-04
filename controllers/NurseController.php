<?php
/**
 * NurseController - Quản lý Y tá (admin only)
 */
require_once __DIR__ . '/../models/Nurse.php';
require_once __DIR__ . '/../models/Department.php';

class NurseController {
    private $nurseModel;
    private $deptModel;

    public function __construct() {
        $this->nurseModel = new Nurse();
        $this->deptModel = new Department();
    }

    public function index() {
        $nurses = $this->nurseModel->getAll();
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/nurses/index.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    public function create() {
        $departments = $this->deptModel->getAll();
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/nurses/create.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'phone' => $_POST['phone'] ?? '',
                'department_id' => $_POST['department_id'] ?: null,
            ];
            $result = $this->nurseModel->create($data);
            if ($result) {
                $_SESSION['success'] = 'Thêm y tá thành công!';
            } else {
                $_SESSION['error'] = 'Có lỗi xảy ra. Email có thể đã tồn tại.';
            }
        }
        header('Location: index.php?page=nurses');
        exit;
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        $nurse = $this->nurseModel->findById($id);
        if (!$nurse) {
            $_SESSION['error'] = 'Không tìm thấy y tá.';
            header('Location: index.php?page=nurses');
            exit;
        }
        $departments = $this->deptModel->getAll();
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/nurses/edit.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $data = [
                'name' => $_POST['name'],
                'email' => $_POST['email'],
                'phone' => $_POST['phone'] ?? '',
                'department_id' => $_POST['department_id'] ?: null,
            ];
            $this->nurseModel->update($id, $data);
            $_SESSION['success'] = 'Cập nhật y tá thành công!';
        }
        header('Location: index.php?page=nurses');
        exit;
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->nurseModel->delete($id);
            $_SESSION['success'] = 'Đã xóa y tá.';
        }
        header('Location: index.php?page=nurses');
        exit;
    }
}
