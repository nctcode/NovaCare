<?php
/**
 * DoctorController - CRUD bác sĩ (Admin) + Xem lịch khám (Doctor)
 */
require_once __DIR__ . '/../models/Doctor.php';

class DoctorController {
    private $doctorModel;

    public function __construct() {
        $this->doctorModel = new Doctor();
    }

    // Danh sách bác sĩ
    public function index() {
        $doctors = $this->doctorModel->getAll();
        $pageTitle = 'Quản lý Bác sĩ';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/doctors/index.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Form thêm bác sĩ
    public function create() {
        $departments = $this->doctorModel->getDepartments();
        $pageTitle = 'Thêm Bác sĩ';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/doctors/create.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Lưu bác sĩ mới
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'name'             => trim($_POST['name'] ?? ''),
                'email'            => trim($_POST['email'] ?? ''),
                'phone'            => trim($_POST['phone'] ?? ''),
                'password'         => '123456',
                'department_id'    => $_POST['department_id'] ?? null,
                'specialty'        => trim($_POST['specialty'] ?? ''),
                'experience_years' => $_POST['experience_years'] ?? 0,
            ];

            try {
                $this->doctorModel->create($data);
                $_SESSION['success'] = 'Thêm bác sĩ thành công!';
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            }
        }
        header('Location: index.php?page=doctors');
        exit;
    }

    // Form sửa bác sĩ
    public function edit() {
        $id = $_GET['id'] ?? 0;
        $doctor = $this->doctorModel->findById($id);
        $departments = $this->doctorModel->getDepartments();

        if (!$doctor) {
            $_SESSION['error'] = 'Không tìm thấy bác sĩ.';
            header('Location: index.php?page=doctors');
            exit;
        }

        $pageTitle = 'Sửa Bác sĩ';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/doctors/edit.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Lưu cập nhật
    public function update() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? 0;
            $data = [
                'name'             => trim($_POST['name'] ?? ''),
                'email'            => trim($_POST['email'] ?? ''),
                'phone'            => trim($_POST['phone'] ?? ''),
                'department_id'    => $_POST['department_id'] ?? null,
                'specialty'        => trim($_POST['specialty'] ?? ''),
                'experience_years' => $_POST['experience_years'] ?? 0,
            ];

            try {
                $this->doctorModel->update($id, $data);
                $_SESSION['success'] = 'Cập nhật thành công!';
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            }
        }
        header('Location: index.php?page=doctors');
        exit;
    }

    // Xóa bác sĩ
    public function delete() {
        $id = $_GET['id'] ?? 0;
        try {
            $this->doctorModel->delete($id);
            $_SESSION['success'] = 'Xóa bác sĩ thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        header('Location: index.php?page=doctors');
        exit;
    }
}
