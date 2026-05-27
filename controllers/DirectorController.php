<?php
/**
 * DirectorController - Quản lý Ban giám đốc (Admin only)
 */
require_once __DIR__ . '/../models/Director.php';
require_once __DIR__ . '/../helpers/Security.php';

class DirectorController {
    private $directorModel;

    public function __construct() {
        $this->directorModel = new Director();
    }

    public function index() {
        Security::requireRole(['admin', 'doctor', 'nurse', 'receptionist']);
        $directors = $this->directorModel->getAll();
        $pageTitle = 'Danh sách Ban giám đốc';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/directors/index.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    public function view() {
        Security::requireRole(['admin', 'doctor', 'nurse', 'receptionist']);
        $id = $_GET['id'] ?? 0;
        $director = $this->directorModel->findById($id);

        if (!$director) {
            $_SESSION['error'] = 'Không tìm thấy giám đốc.';
            header('Location: index.php?page=directors');
            exit;
        }

        $pageTitle = 'Chi tiết Ban giám đốc';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/directors/view.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    public function create() {
        Security::requireRole('admin');
        $pageTitle = 'Thêm Giám đốc mới';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/directors/create.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    public function store() {
        Security::requireRole('admin');
        Security::requirePost('index.php?page=directors');
        Security::requireCsrf();

        $data = [
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'] ?? '',
            'password' => '123456'
        ];
        $result = $this->directorModel->create($data);
        if ($result) {
            $_SESSION['success'] = 'Thêm giám đốc thành công!';
        } else {
            $_SESSION['error'] = 'Có lỗi xảy ra. Email có thể đã tồn tại.';
        }
        
        header('Location: index.php?page=directors');
        exit;
    }

    public function edit() {
        Security::requireRole('admin');
        $id = $_GET['id'] ?? null;
        $director = $this->directorModel->findById($id);
        if (!$director) {
            $_SESSION['error'] = 'Không tìm thấy giám đốc.';
            header('Location: index.php?page=directors');
            exit;
        }
        $pageTitle = 'Sửa Giám đốc';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/directors/edit.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    public function update() {
        Security::requireRole('admin');
        Security::requirePost('index.php?page=directors');
        Security::requireCsrf();

        $id = $_POST['id'];
        $data = [
            'name' => $_POST['name'],
            'email' => $_POST['email'],
            'phone' => $_POST['phone'] ?? '',
        ];
        $this->directorModel->update($id, $data);
        $_SESSION['success'] = 'Cập nhật thông tin giám đốc thành công!';
        
        header('Location: index.php?page=directors');
        exit;
    }

    public function delete() {
        Security::requireRole('admin');
        Security::requirePost('index.php?page=directors');
        Security::requireCsrf();

        $id = $_POST['id'] ?? null;
        if ($id) {
            $this->directorModel->delete($id);
            $_SESSION['success'] = 'Đã xóa giám đốc.';
        }
        header('Location: index.php?page=directors');
        exit;
    }
}
