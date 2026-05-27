<?php
/**
 * CashierController - Quản lý Thu ngân (Admin only)
 */
require_once __DIR__ . '/../models/Cashier.php';
require_once __DIR__ . '/../helpers/Security.php';

class CashierController {
    private $cashierModel;

    public function __construct() {
        $this->cashierModel = new Cashier();
    }

    public function index() {
        Security::requireRole(['admin', 'doctor', 'nurse', 'receptionist']);
        $cashiers = $this->cashierModel->getAll();
        $pageTitle = 'Danh sách Thu ngân';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/cashiers/index.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    public function view() {
        Security::requireRole(['admin', 'doctor', 'nurse', 'receptionist']);
        $id = $_GET['id'] ?? 0;
        $cashier = $this->cashierModel->findById($id);

        if (!$cashier) {
            $_SESSION['error'] = 'Không tìm thấy thu ngân.';
            header('Location: index.php?page=cashiers');
            exit;
        }

        $pageTitle = 'Chi tiết Thu ngân';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/cashiers/view.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    public function create() {
        Security::requireRole('admin');
        $pageTitle = 'Thêm Thu ngân mới';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/cashiers/create.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    public function store() {
        Security::requireRole('admin');
        Security::requirePost('index.php?page=cashiers');
        Security::requireCsrf();

        $data = [
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'] ?? '',
            'password' => '123456'
        ];
        $result = $this->cashierModel->create($data);
        if ($result) {
            $_SESSION['success'] = 'Thêm thu ngân thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra. Email có thể đã tồn tại.';
        }
        
        header('Location: index.php?page=cashiers');
        exit;
    }

    public function edit() {
        Security::requireRole('admin');
        $id = $_GET['id'] ?? null;
        $cashier = $this->cashierModel->findById($id);
        if (!$cashier) {
            $_SESSION['error'] = 'Không tìm thấy thu ngân.';
            header('Location: index.php?page=cashiers');
            exit;
        }
        $pageTitle = 'Sửa Thu ngân';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/cashiers/edit.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    public function update() {
        Security::requireRole('admin');
        Security::requirePost('index.php?page=cashiers');
        Security::requireCsrf();

        $id = $_POST['id'];
        $data = [
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'] ?? '',
        ];
        $this->cashierModel->update($id, $data);
        $_SESSION['success'] = 'Cập nhật thu ngân thành công!';
        
        header('Location: index.php?page=cashiers');
        exit;
    }

    public function delete() {
        Security::requireRole('admin');
        Security::requirePost('index.php?page=cashiers');
        Security::requireCsrf();

        $id = $_POST['id'] ?? null;
        if ($id) {
            $this->cashierModel->delete($id);
            $_SESSION['success'] = 'Đã xóa thu ngân.';
        }
        header('Location: index.php?page=cashiers');
        exit;
    }
}
