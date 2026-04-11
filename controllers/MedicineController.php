<?php
/**
 * MedicineController - CRUD thuốc + quản lý tồn kho (Admin)
 */
require_once __DIR__ . '/../models/Medicine.php';

class MedicineController {
    private $medicineModel;

    public function __construct() {
        $this->medicineModel = new Medicine();
    }

    // Danh sách thuốc
    public function index() {
        $medicines = $this->medicineModel->getAll();
        $pageTitle = 'Quản lý Thuốc';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/medicines/index.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Form thêm thuốc
    public function create() {
        $pageTitle = 'Thêm Thuốc';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/medicines/create.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Lưu thuốc mới
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        }
        header('Location: index.php?page=medicines');
        exit;
    }

    // Form sửa thuốc
    public function edit() {
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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
        }
        header('Location: index.php?page=medicines');
        exit;
    }

    // Xóa thuốc
    public function delete() {
        $id = $_GET['id'] ?? 0;
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
