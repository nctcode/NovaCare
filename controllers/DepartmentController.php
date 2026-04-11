<?php
/**
 * DepartmentController - Quản lý Khoa (admin only)
 */
require_once __DIR__ . '/../models/Department.php';

class DepartmentController {
    private $deptModel;

    public function __construct() {
        $this->deptModel = new Department();
    }

    public function index() {
        $departments = $this->deptModel->getAll();
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/departments/index.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    public function create() {
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/departments/create.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name' => $_POST['name'],
                'description' => $_POST['description'] ?? '',
            ];
            $this->deptModel->create($data);
            $_SESSION['success'] = 'Thêm khoa thành công!';
        }
        header('Location: index.php?page=departments');
        exit;
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        $department = $this->deptModel->findById($id);
        if (!$department) {
            $_SESSION['error'] = 'Không tìm thấy khoa.';
            header('Location: index.php?page=departments');
            exit;
        }
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/departments/edit.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $data = [
                'name' => $_POST['name'],
                'description' => $_POST['description'] ?? '',
            ];
            $this->deptModel->update($id, $data);
            $_SESSION['success'] = 'Cập nhật khoa thành công!';
        }
        header('Location: index.php?page=departments');
        exit;
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->deptModel->delete($id);
            $_SESSION['success'] = 'Đã xóa khoa.';
        }
        header('Location: index.php?page=departments');
        exit;
    }

    public function view() {
        $id = $_GET['id'] ?? null;
        $department = $this->deptModel->findById($id);
        if (!$department) {
            header('Location: index.php?page=departments');
            exit;
        }
        $doctors = $this->deptModel->getDoctorsByDept($id);
        $nurses = $this->deptModel->getNursesByDept($id);
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/departments/view.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }
}
