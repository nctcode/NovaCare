<?php
/**
 * PatientController - CRUD bệnh nhân
 * Quyền: Admin = full CRUD, Doctor = xem, Patient = xem hồ sơ mình
 */
require_once __DIR__ . '/../models/Patient.php';
require_once __DIR__ . '/../helpers/Security.php';
require_once __DIR__ . '/../helpers/AuditLog.php';

class PatientController {
    private $patientModel;

    public function __construct() {
        $this->patientModel = new Patient();
    }

    // Danh sách bệnh nhân
    public function index() {
        Security::requireRole(['admin', 'doctor']);
        $patients = $this->patientModel->getAll();
        $pageTitle = 'Quản lý Bệnh nhân';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/patients/index.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Form thêm bệnh nhân
    public function create() {
        Security::requireRole('admin');
        $pageTitle = 'Thêm Bệnh nhân';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/patients/create.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Lưu bệnh nhân mới
    public function store() {
        Security::requireRole('admin');
        Security::requirePost('index.php?page=patients');
        Security::requireCsrf();

        $data = [
            'name'            => trim($_POST['name'] ?? ''),
            'email'           => trim($_POST['email'] ?? ''),
            'phone'           => trim($_POST['phone'] ?? ''),
            'password'        => '123456',
            'date_of_birth'   => $_POST['date_of_birth'] ?? '',
            'gender'          => $_POST['gender'] ?? '',
            'address'         => trim($_POST['address'] ?? ''),
            'blood_type'      => trim($_POST['blood_type'] ?? ''),
            'medical_history' => trim($_POST['medical_history'] ?? ''),
        ];

        try {
            $id = $this->patientModel->create($data);
            AuditLog::logCreate('patients', $id, ['name' => $data['name'], 'email' => $data['email']]);
            $_SESSION['success'] = 'Thêm bệnh nhân thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        header('Location: index.php?page=patients');
        exit;
    }

    // Form sửa bệnh nhân
    public function edit() {
        Security::requireRole('admin');
        $id = $_GET['id'] ?? 0;
        $patient = $this->patientModel->findById($id);

        if (!$patient) {
            $_SESSION['error'] = 'Không tìm thấy bệnh nhân.';
            header('Location: index.php?page=patients');
            exit;
        }

        $pageTitle = 'Sửa Bệnh nhân';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/patients/edit.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Lưu cập nhật
    public function update() {
        Security::requireRole('admin');
        Security::requirePost('index.php?page=patients');
        Security::requireCsrf();

        $id = $_POST['id'] ?? 0;
        $data = [
            'name'            => trim($_POST['name'] ?? ''),
            'email'           => trim($_POST['email'] ?? ''),
            'phone'           => trim($_POST['phone'] ?? ''),
            'date_of_birth'   => $_POST['date_of_birth'] ?? '',
            'gender'          => $_POST['gender'] ?? '',
            'address'         => trim($_POST['address'] ?? ''),
            'blood_type'      => trim($_POST['blood_type'] ?? ''),
            'medical_history' => trim($_POST['medical_history'] ?? ''),
        ];

        try {
            $this->patientModel->update($id, $data);
            AuditLog::logUpdate('patients', $id, null, ['name' => $data['name'], 'email' => $data['email']]);
            $_SESSION['success'] = 'Cập nhật thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        header('Location: index.php?page=patients');
        exit;
    }

    // Xóa bệnh nhân (POST only)
    public function delete() {
        Security::requireRole('admin');
        Security::requirePost('index.php?page=patients');
        Security::requireCsrf();

        $id = $_POST['id'] ?? 0;
        try {
            $patient = $this->patientModel->findById($id);
            $this->patientModel->delete($id);
            AuditLog::logDelete('patients', $id, $patient ? ['name' => $patient['name']] : null);
            $_SESSION['success'] = 'Xóa bệnh nhân thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        header('Location: index.php?page=patients');
        exit;
    }
}
