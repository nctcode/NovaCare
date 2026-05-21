<?php
/**
 * MedicineController - CRUD thuốc + quản lý tồn kho (Pharmacist)
 */
require_once __DIR__ . '/../models/Medicine.php';
require_once __DIR__ . '/../helpers/Security.php';

class MedicineController {
    private $medicineModel;

    public function __construct() {
        $this->medicineModel = new Medicine();
    }

    // Danh sách thuốc
    public function index() {
        Security::requireRole(['admin', 'pharmacist', 'doctor']);
        $medicines = $this->medicineModel->getAll();
        $pageTitle = 'Quản lý Thuốc';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/medicines/index.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Form thêm thuốc
    public function create() {
        Security::requireRole(['admin', 'pharmacist']);
        $pageTitle = 'Thêm Thuốc';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/medicines/create.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Lưu thuốc mới
    public function store() {
        Security::requireRole(['admin', 'pharmacist']);
        Security::requirePost('index.php?page=medicines');
        Security::requireCsrf();

        $data = [
            'name'        => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'quantity'    => intval($_POST['quantity'] ?? 0),
            'expiry_date' => $_POST['expiry_date'] ?? '',
            'price'       => floatval($_POST['price'] ?? 0),
        ];

        try {
            $this->medicineModel->create($data);
            $_SESSION['success'] = 'Thêm thuốc thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        header('Location: index.php?page=medicines');
        exit;
    }

    // Form sửa thuốc
    public function edit() {
        Security::requireRole(['admin', 'pharmacist']);
        $id = $_GET['id'] ?? 0;
        $medicine = $this->medicineModel->findById($id);

        if (!$medicine) {
            $_SESSION['error'] = 'Không tìm thấy thuốc.';
            header('Location: index.php?page=medicines');
            exit;
        }

        $pageTitle = 'Sửa Thuốc';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/medicines/edit.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Lưu cập nhật thuốc
    public function update() {
        Security::requireRole(['admin', 'pharmacist']);
        Security::requirePost('index.php?page=medicines');
        Security::requireCsrf();

        $id = $_POST['id'] ?? 0;
        $data = [
            'name'        => trim($_POST['name'] ?? ''),
            'description' => trim($_POST['description'] ?? ''),
            'quantity'    => intval($_POST['quantity'] ?? 0),
            'expiry_date' => $_POST['expiry_date'] ?? '',
            'price'       => floatval($_POST['price'] ?? 0),
        ];

        try {
            $this->medicineModel->update($id, $data);
            $_SESSION['success'] = 'Cập nhật thuốc thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        header('Location: index.php?page=medicines');
        exit;
    }

    // Xóa thuốc
    public function delete() {
        Security::requireRole(['admin', 'pharmacist']);
        Security::requirePost('index.php?page=medicines');
        Security::requireCsrf();

        $id = $_POST['id'] ?? 0;
        try {
            $this->medicineModel->delete($id);
            $_SESSION['success'] = 'Xóa thuốc thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        header('Location: index.php?page=medicines');
        exit;
    }
}
