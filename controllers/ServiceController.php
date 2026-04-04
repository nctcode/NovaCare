<?php
/**
 * ServiceController - Quản lý Dịch vụ Y tế (admin only)
 */
require_once __DIR__ . '/../models/Service.php';

class ServiceController {
    private $serviceModel;

    public function __construct() {
        $this->serviceModel = new Service();
    }

    public function index() {
        $services = $this->serviceModel->getAll();
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/services/index.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    public function create() {
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/services/create.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'service_name' => $_POST['service_name'],
                'price' => $_POST['price'],
                'description' => $_POST['description'] ?? '',
            ];
            $this->serviceModel->create($data);
            $_SESSION['success'] = 'Thêm dịch vụ thành công!';
        }
        header('Location: index.php?page=services-admin');
        exit;
    }

    public function edit() {
        $id = $_GET['id'] ?? null;
        $service = $this->serviceModel->findById($id);
        if (!$service) {
            $_SESSION['error'] = 'Không tìm thấy dịch vụ.';
            header('Location: index.php?page=services-admin');
            exit;
        }
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/services/edit.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'];
            $data = [
                'service_name' => $_POST['service_name'],
                'price' => $_POST['price'],
                'description' => $_POST['description'] ?? '',
            ];
            $this->serviceModel->update($id, $data);
            $_SESSION['success'] = 'Cập nhật dịch vụ thành công!';
        }
        header('Location: index.php?page=services-admin');
        exit;
    }

    public function delete() {
        $id = $_GET['id'] ?? null;
        if ($id) {
            $this->serviceModel->delete($id);
            $_SESSION['success'] = 'Đã xóa dịch vụ.';
        }
        header('Location: index.php?page=services-admin');
        exit;
    }
}
