<?php
/**
 * EquipmentController - Quản lý trang thiết bị y tế (Admin)
 */
require_once __DIR__ . '/../models/Equipment.php';
require_once __DIR__ . '/../helpers/Security.php';

class EquipmentController {
    private $equipmentModel;

    public function __construct() {
        $this->equipmentModel = new Equipment();
    }

    public function index() {
        Security::requireRole('admin');
        $equipmentList = $this->equipmentModel->getAll();
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/equipment/index.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    public function create() {
        Security::requireRole('admin');
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/equipment/create.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    public function store() {
        Security::requireRole('admin');
        Security::requirePost('index.php?page=equipment');
        Security::requireCsrf();

        $data = [
            'equipment_name' => $_POST['equipment_name'],
            'quantity' => (int)$_POST['quantity'],
            'status' => $_POST['status'],
            'description' => $_POST['description'] ?? '',
        ];
        $this->equipmentModel->create($data);
        $_SESSION['success'] = 'Thêm vật tư y tế thành công!';
        
        header('Location: index.php?page=equipment');
        exit;
    }

    public function edit() {
        Security::requireRole('admin');
        $id = $_GET['id'] ?? null;
        $equipment = $this->equipmentModel->findById($id);
        if (!$equipment) {
            $_SESSION['error'] = 'Không tìm thấy vật tư y tế.';
            header('Location: index.php?page=equipment');
            exit;
        }
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/equipment/edit.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    public function update() {
        Security::requireRole('admin');
        Security::requirePost('index.php?page=equipment');
        Security::requireCsrf();

        $id = $_POST['id'];
        $data = [
            'equipment_name' => $_POST['equipment_name'],
            'quantity' => (int)$_POST['quantity'],
            'status' => $_POST['status'],
            'description' => $_POST['description'] ?? '',
        ];
        $this->equipmentModel->update($id, $data);
        $_SESSION['success'] = 'Cập nhật vật tư y tế thành công!';
        
        header('Location: index.php?page=equipment');
        exit;
    }

    public function delete() {
        Security::requireRole('admin');
        Security::requirePost('index.php?page=equipment');
        Security::requireCsrf();

        $id = $_POST['id'] ?? null;
        if ($id) {
            $this->equipmentModel->delete($id);
            $_SESSION['success'] = 'Đã xóa vật tư y tế.';
        }
        header('Location: index.php?page=equipment');
        exit;
    }
}
