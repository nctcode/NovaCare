<?php
/**
 * PatientController - CRUD bệnh nhân (Admin)
 */
require_once __DIR__ . '/../models/Patient.php';

class PatientController {
    private $patientModel;

    public function __construct() {
        $this->patientModel = new Patient();
    }

    // Danh sách bệnh nhân
    public function index() {
        $patients = $this->patientModel->getAll();
        $pageTitle = 'Quản lý Bệnh nhân';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/patients/index.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Form thêm bệnh nhân
    public function create() {
        $pageTitle = 'Thêm Bệnh nhân';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/patients/create.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Lưu bệnh nhân mới
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
                $this->patientModel->create($data);
                $_SESSION['success'] = 'Thêm bệnh nhân thành công!';
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            }
        }
        header('Location: index.php?page=patients');
        exit;
    }

    // Form sửa bệnh nhân
    public function edit() {
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
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
                $_SESSION['success'] = 'Cập nhật thành công!';
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            }
        }
        header('Location: index.php?page=patients');
        exit;
    }

    // Xóa bệnh nhân
    public function delete() {
        $id = $_GET['id'] ?? 0;
        try {
            $this->patientModel->delete($id);
            $_SESSION['success'] = 'Xóa bệnh nhân thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        header('Location: index.php?page=patients');
        exit;
    }
}
