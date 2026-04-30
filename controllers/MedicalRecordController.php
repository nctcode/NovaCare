<?php
/**
 * MedicalRecordController - Quản lý Hồ sơ Bệnh án
 */
require_once __DIR__ . '/../models/MedicalRecord.php';
require_once __DIR__ . '/../models/Doctor.php';
require_once __DIR__ . '/../models/Patient.php';
require_once __DIR__ . '/../models/Appointment.php';
require_once __DIR__ . '/../helpers/Security.php';

class MedicalRecordController {
    private $recordModel;
    private $doctorModel;
    private $patientModel;
    private $appointmentModel;

    public function __construct() {
        $this->recordModel = new MedicalRecord();
        $this->doctorModel = new Doctor();
        $this->patientModel = new Patient();
        $this->appointmentModel = new Appointment();
    }

    public function index() {
        $user = $_SESSION['user'];
        $role = $user['role'];
        $records = [];

        if ($role === 'admin') {
            $records = $this->recordModel->getAll();
        } elseif ($role === 'doctor') {
            $doctor = $this->doctorModel->findByUserId($user['id']);
            if ($doctor) $records = $this->recordModel->getByDoctorId($doctor['id']);
        } elseif ($role === 'patient') {
            $patient = $this->patientModel->findByUserId($user['id']);
            if ($patient) $records = $this->recordModel->getByPatientId($patient['id']);
        }

        $pageTitle = 'Hồ sơ Bệnh án';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/medical_records/index.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    public function view() {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            header('Location: index.php?page=records');
            exit;
        }

        $record = $this->recordModel->findById($id);
        if (!$record) {
            $_SESSION['error'] = 'Không tìm thấy hồ sơ bệnh án.';
            header('Location: index.php?page=records');
            exit;
        }

        $pageTitle = 'Chi tiết Hồ sơ Bệnh án';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/medical_records/view.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    public function create() {
        $user = $_SESSION['user'];
        if ($user['role'] !== 'doctor' && $user['role'] !== 'admin') {
            $_SESSION['error'] = 'Bạn không có quyền truy cập.';
            header('Location: index.php?page=dashboard');
            exit;
        }

        $patients = $this->patientModel->getAll();
        $doctor = $this->doctorModel->findByUserId($user['id']);
        
        $appointments = [];
        if ($doctor) {
            $appointments = $this->appointmentModel->getByDoctorId($doctor['id']);
        }

        $pageTitle = 'Tạo Hồ sơ Bệnh án';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/medical_records/create.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    public function store() {
        Security::requireRole(['admin', 'doctor']);
        Security::requirePost('index.php?page=records');
        Security::requireCsrf();

        $user = $_SESSION['user'];
            
        // Tìm doctor_id nếu là bác sĩ đăng nhập
        $doctor_id = $_POST['doctor_id'] ?? 0;
        if ($user['role'] === 'doctor') {
            $doctor = $this->doctorModel->findByUserId($user['id']);
            if ($doctor) $doctor_id = $doctor['id'];
        }

        $data = [
            'patient_id' => $_POST['patient_id'],
            'doctor_id' => $doctor_id,
            'appointment_id' => $_POST['appointment_id'] ?? null,
            'diagnosis' => $_POST['diagnosis'],
            'treatment' => $_POST['treatment'] ?? '',
            'notes' => $_POST['notes'] ?? ''
        ];

        try {
            $this->recordModel->create($data);
            
            // Nếu hồ sơ gắn với lịch hẹn, đổi trạng thái lịch hẹn thành completed
            if (!empty($data['appointment_id'])) {
                $this->appointmentModel->updateStatus($data['appointment_id'], 'completed');
            }
            
            $_SESSION['success'] = 'Tạo hồ sơ bệnh án thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        
        header('Location: index.php?page=records');
        exit;
    }

    /**
     * AI Tóm tắt bệnh án của bệnh nhân
     * Gọi hàm summarizeMedicalRecords() từ OllamaAI/GeminiAI
     */
    public function summarize() {
        Security::requireRole(['admin', 'doctor']);

        $patientId = $_GET['patient_id'] ?? 0;
        if (!$patientId) {
            $_SESSION['error'] = 'Thiếu thông tin bệnh nhân.';
            header('Location: index.php?page=records');
            exit;
        }

        // Lấy tất cả bệnh án của bệnh nhân
        $records = $this->recordModel->getByPatientId($patientId);
        $patient = $this->patientModel->findById($patientId);

        if (empty($records)) {
            if (isset($_GET['ajax'])) {
                echo json_encode(['success' => false, 'error' => 'Bệnh nhân chưa có bệnh án nào.']);
                exit;
            }
            $_SESSION['error'] = 'Bệnh nhân chưa có bệnh án nào để tóm tắt.';
            header('Location: index.php?page=records');
            exit;
        }

        // Nếu là request AJAX, tiến hành gọi AI và trả về JSON
        if (isset($_GET['ajax'])) {
            header('Content-Type: application/json; charset=utf-8');
            require_once __DIR__ . '/../models/BeeknoeeAI.php';
            $ai = new BeeknoeeAI();
            
            if ($ai->isConfigured()) {
                $result = $ai->summarizeMedicalRecords($records);
                if ($result['success']) {
                    echo json_encode(['success' => true, 'summary' => $result['data']]);
                } else {
                    $error = $result['error'];
                    // Thử fallback sang GeminiAI
                    require_once __DIR__ . '/../models/GeminiAI.php';
                    $gemini = new GeminiAI();
                    if ($gemini->isConfigured()) {
                        $resultGemini = $gemini->summarizeMedicalRecords($records);
                        if ($resultGemini['success']) {
                            echo json_encode(['success' => true, 'summary' => $resultGemini['data'], 'fallback' => true]);
                            exit;
                        }
                    }
                    echo json_encode(['success' => false, 'error' => $error]);
                }
            } else {
                echo json_encode(['success' => false, 'error' => 'AI chưa được cấu hình. Vui lòng kiểm tra config/ai.php.']);
            }
            exit;
        }

        // Nếu là load trang bình thường, chỉ render giao diện ngay lập tức
        $pageTitle = 'AI Tóm tắt Bệnh án';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/medical_records/summarize.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }
}
