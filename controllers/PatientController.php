<?php
/**
 * PatientController - CRUD bệnh nhân
 * Quyền: Admin/Receptionist = full CRUD, Doctor = xem, Patient = xem hồ sơ mình
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
        Security::requireRole(['admin', 'receptionist', 'doctor']);
        
        $user = $_SESSION['user'];
        if ($user['role'] === 'doctor') {
            require_once __DIR__ . '/../models/Doctor.php';
            $doctorModel = new Doctor();
            $doctorInfo = $doctorModel->findByUserId($user['id']);
            $doctorId = $doctorInfo ? $doctorInfo['id'] : 0;
            $patients = $this->patientModel->getByDoctorId($doctorId);
        } else {
            $patients = $this->patientModel->getAll();
        }

        $pageTitle = 'Quản lý Bệnh nhân';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/patients/index.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Xem chi tiết bệnh nhân (gồm thông tin chung, lịch sử bệnh án, và AI support)
    public function view() {
        Security::requireRole(['admin', 'receptionist', 'doctor']);
        $id = $_GET['id'] ?? 0;
        $patient = $this->patientModel->findById($id);

        if (!$patient) {
            $_SESSION['error'] = 'Không tìm thấy bệnh nhân.';
            header('Location: index.php?page=patients');
            exit;
        }

        // Lấy lịch sử khám bệnh (medical records) của bệnh nhân này (chỉ dành cho bác sĩ)
        $records = [];
        if ($_SESSION['user']['role'] === 'doctor') {
            require_once __DIR__ . '/../models/MedicalRecord.php';
            $recordModel = new MedicalRecord();
            $records = $recordModel->getByPatientId($id);
        }

        $pageTitle = 'Chi tiết Bệnh nhân';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/patients/view.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Form thêm bệnh nhân
    public function create() {
        Security::requireRole(['admin', 'receptionist']);
        $pageTitle = 'Thêm Bệnh nhân';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/patients/create.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Lưu bệnh nhân mới
    public function store() {
        Security::requireRole(['admin', 'receptionist']);
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
        Security::requireRole(['admin', 'receptionist']);
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
        Security::requireRole(['admin', 'receptionist']);
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
        Security::requireRole(['admin', 'receptionist']);
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
    // Bệnh nhân xem hồ sơ cá nhân của chính mình
    public function myProfile() {
        Security::requireRole('patient');
        $user = $_SESSION['user'];
        $patient = $this->patientModel->findByUserId($user['id']);

        if (!$patient) {
            $_SESSION['error'] = 'Không tìm thấy thông tin bệnh nhân.';
            header('Location: index.php?page=dashboard');
            exit;
        }

        $pageTitle = 'Tài khoản của tôi';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/patients/my_profile.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Bệnh nhân tự cập nhật hồ sơ
    public function updateMyProfile() {
        Security::requireRole('patient');
        Security::requirePost('index.php?page=patients&action=myProfile');
        Security::requireCsrf();

        $user = $_SESSION['user'];
        $patient = $this->patientModel->findByUserId($user['id']);

        if (!$patient) {
            header('Location: index.php?page=dashboard');
            exit;
        }

        $data = [
            'name'            => trim($_POST['name'] ?? $patient['name']),
            'phone'           => trim($_POST['phone'] ?? $patient['phone']),
            'date_of_birth'   => $_POST['date_of_birth'] ?? $patient['date_of_birth'],
            'gender'          => $_POST['gender'] ?? $patient['gender'],
            'address'         => trim($_POST['address'] ?? $patient['address']),
            'blood_type'      => trim($_POST['blood_type'] ?? $patient['blood_type']),
            'medical_history' => $patient['medical_history'], // Giữ nguyên
        ];

        try {
            $this->patientModel->update($patient['id'], $data);
            
            // Đồng bộ tên và SĐT sang bảng users
            require_once __DIR__ . '/../models/User.php';
            $userModel = new User();
            $userModel->updateNamePhone($user['id'], $data['name'], $data['phone']);
            
            // Cập nhật session
            $_SESSION['user']['name'] = $data['name'];
            
            AuditLog::logUpdate('patients', $patient['id'], null, ['name' => $data['name'], 'action' => 'self_update']);
            $_SESSION['success'] = 'Cập nhật thông tin thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        header('Location: index.php?page=patients&action=myProfile');
        exit;
    }
}
