<?php
/**
 * TechnicianController - Quản lý Kỹ thuật viên (Admin only)
 */
require_once __DIR__ . '/../models/Technician.php';
require_once __DIR__ . '/../models/Department.php';
require_once __DIR__ . '/../helpers/Security.php';

class TechnicianController {
    private $techModel;
    private $deptModel;

    public function __construct() {
        $this->techModel = new Technician();
        $this->deptModel = new Department();
    }

    public function index() {
        Security::requireRole(['admin', 'doctor', 'nurse', 'receptionist', 'technician']);
        $technicians = $this->techModel->getAll();
        $pageTitle = 'Danh sách Kỹ thuật viên';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/technicians/index.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    public function view() {
        Security::requireRole(['admin', 'doctor', 'nurse', 'receptionist', 'technician']);
        $id = $_GET['id'] ?? 0;
        $tech = $this->techModel->findById($id);

        if (!$tech) {
            $_SESSION['error'] = 'Không tìm thấy kỹ thuật viên.';
            header('Location: index.php?page=technicians');
            exit;
        }

        $pageTitle = 'Chi tiết Kỹ thuật viên';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/technicians/view.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    public function create() {
        Security::requireRole('admin');
        $departments = $this->deptModel->getAll();
        $pageTitle = 'Thêm Kỹ thuật viên';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/technicians/create.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    public function store() {
        Security::requireRole('admin');
        Security::requirePost('index.php?page=technicians');
        Security::requireCsrf();

        $data = [
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'] ?? '',
            'password' => '123456',
            'department_id' => $_POST['department_id'] ?: null,
            'specialty' => $_POST['specialty'] ?? '',
        ];
        $result = $this->techModel->create($data);
        if ($result) {
            $_SESSION['success'] = 'Thêm kỹ thuật viên thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra. Email có thể đã tồn tại.';
        }
        
        header('Location: index.php?page=technicians');
        exit;
    }

    public function edit() {
        Security::requireRole('admin');
        $id = $_GET['id'] ?? null;
        $tech = $this->techModel->findById($id);
        if (!$tech) {
            $_SESSION['error'] = 'Không tìm thấy kỹ thuật viên.';
            header('Location: index.php?page=technicians');
            exit;
        }
        $departments = $this->deptModel->getAll();
        $pageTitle = 'Sửa Kỹ thuật viên';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/technicians/edit.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    public function update() {
        Security::requireRole('admin');
        Security::requirePost('index.php?page=technicians');
        Security::requireCsrf();

        $id = $_POST['id'];
        $data = [
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'] ?? '',
            'department_id' => $_POST['department_id'] ?: null,
            'specialty' => $_POST['specialty'] ?? '',
        ];
        $this->techModel->update($id, $data);
        $_SESSION['success'] = 'Cập nhật kỹ thuật viên thành công!';
        
        header('Location: index.php?page=technicians');
        exit;
    }

    public function delete() {
        Security::requireRole('admin');
        Security::requirePost('index.php?page=technicians');
        Security::requireCsrf();

        $id = $_POST['id'] ?? null;
        if ($id) {
            $this->techModel->delete($id);
            $_SESSION['success'] = 'Đã xóa kỹ thuật viên.';
        }
        header('Location: index.php?page=technicians');
        exit;
    }
}
