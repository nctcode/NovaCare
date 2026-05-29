<?php
/**
 * DoctorController - CRUD bác sĩ
 * Quyền: Admin = full CRUD
 */
require_once __DIR__ . '/../models/Doctor.php';
require_once __DIR__ . '/../helpers/Security.php';
require_once __DIR__ . '/../helpers/AuditLog.php';

class DoctorController {
    private $doctorModel;

    public function __construct() {
        $this->doctorModel = new Doctor();
    }

    public function index() {
        Security::requireRole(['admin', 'doctor', 'nurse', 'receptionist']);
        $doctors = $this->doctorModel->getAll();
        $pageTitle = 'Quản lý Bác sĩ';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/doctors/index.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    public function create() {
        Security::requireRole('admin');
        $departments = $this->doctorModel->getDepartments();
        $pageTitle = 'Thêm Bác sĩ';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/doctors/create.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    public function store() {
        Security::requireRole('admin');
        Security::requirePost('index.php?page=doctors');
        Security::requireCsrf();

        $data = [
            'name'             => trim($_POST['name'] ?? ''),
            'email'            => trim($_POST['email'] ?? ''),
            'phone'            => trim($_POST['phone'] ?? ''),
            'password'         => '123456',
            'department_ids'   => $_POST['department_ids'] ?? [],
            'specialty'        => trim($_POST['specialty'] ?? ''),
            'experience_years' => $_POST['experience_years'] ?? 0,
        ];

        try {
            $id = $this->doctorModel->create($data);
            AuditLog::logCreate('doctors', $id, ['name' => $data['name'], 'email' => $data['email']]);
            $_SESSION['success'] = 'Thêm bác sĩ thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        header('Location: index.php?page=doctors');
        exit;
    }

    public function edit() {
        Security::requireRole('admin');
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

    public function update() {
        Security::requireRole('admin');
        Security::requirePost('index.php?page=doctors');
        Security::requireCsrf();

        $id = $_POST['id'] ?? 0;
        $data = [
            'name'             => trim($_POST['name'] ?? ''),
            'email'            => trim($_POST['email'] ?? ''),
            'phone'            => trim($_POST['phone'] ?? ''),
            'department_ids'   => $_POST['department_ids'] ?? [],
            'specialty'        => trim($_POST['specialty'] ?? ''),
            'experience_years' => $_POST['experience_years'] ?? 0,
        ];

        try {
            $this->doctorModel->update($id, $data);
            AuditLog::logUpdate('doctors', $id, null, ['name' => $data['name']]);
            $_SESSION['success'] = 'Cập nhật thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        header('Location: index.php?page=doctors');
        exit;
    }

    public function delete() {
        Security::requireRole('admin');
        Security::requirePost('index.php?page=doctors');
        Security::requireCsrf();

        $id = $_POST['id'] ?? 0;
        try {
            $doctor = $this->doctorModel->findById($id);
            $this->doctorModel->delete($id);
            AuditLog::logDelete('doctors', $id, $doctor ? ['name' => $doctor['name']] : null);
            $_SESSION['success'] = 'Xóa bác sĩ thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        header('Location: index.php?page=doctors');
        exit;
    }

    public function view() {
        Security::requireRole(['admin', 'doctor', 'nurse', 'receptionist']);
        $id = $_GET['id'] ?? 0;
        $doctor = $this->doctorModel->findById($id);

        if (!$doctor) {
            $_SESSION['error'] = 'Không tìm thấy bác sĩ.';
            header('Location: index.php?page=doctors');
            exit;
        }

        $pageTitle = 'Chi tiết Bác sĩ';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/doctors/view.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }
}

