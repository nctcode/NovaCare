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
        Security::requireRole(['doctor', 'patient']);
        $user = $_SESSION['user'];
        $role = $user['role'];
        $records = [];

        if ($role === 'doctor') {
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
        Security::requireRole(['doctor', 'patient']);
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

        $user = $_SESSION['user'];

        // IDOR check: bệnh nhân chỉ xem được bệnh án của chính mình
        if ($user['role'] === 'patient') {
            $patient = $this->patientModel->findByUserId($user['id']);
            if (!$patient || $record['patient_id'] != $patient['id']) {
                $_SESSION['error'] = 'Bạn không có quyền xem hồ sơ bệnh án này.';
                header('Location: index.php?page=records');
                exit;
            }
        }

        // IDOR check: bác sĩ chỉ xem được bệnh án mình phụ trách
        if ($user['role'] === 'doctor') {
            $doctor = $this->doctorModel->findByUserId($user['id']);
            if (!$doctor || $record['doctor_id'] != $doctor['id']) {
                $_SESSION['error'] = 'Bạn không có quyền xem hồ sơ bệnh án này.';
                header('Location: index.php?page=records');
                exit;
            }
        }

        $pageTitle = 'Chi tiết Hồ sơ Bệnh án';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/medical_records/view.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    public function create() {
        $user = $_SESSION['user'];
        if ($user['role'] !== 'doctor') {
            $_SESSION['error'] = 'Chỉ bác sĩ mới có quyền tạo hồ sơ bệnh án.';
            header('Location: index.php?page=dashboard');
            exit;
        }

        $doctor = $this->doctorModel->findByUserId($user['id']);
        $patients = [];
        if ($doctor) {
            $patients = $this->patientModel->getByDoctorId($doctor['id']);
        } else {
            $patients = $this->patientModel->getAll();
        }
        
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
        Security::requireRole('doctor');
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
            'icd10_code' => $_POST['icd10_code'] ?? null,
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
     * Tìm kiếm mã ICD-10 (AJAX API) - Smart Hospital 4.0
     */
    public function searchIcd10() {
        header('Content-Type: application/json; charset=utf-8');
        Security::requireRole(['doctor']);

        $q = trim($_GET['q'] ?? '');
        if (strlen($q) < 1) {
            echo json_encode([]);
            exit;
        }

        $db = new Database();
        $conn = $db->getConnection();
        
        $sql = "SELECT code, name, name_en, category 
                FROM icd10_codes 
                WHERE code LIKE :q OR name LIKE :q OR name_en LIKE :q OR category LIKE :q
                LIMIT 15";
        $stmt = $conn->prepare($sql);
        $searchQuery = "%" . $q . "%";
        $stmt->bindValue(':q', $searchQuery);
        $stmt->execute();
        
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode($results, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * AI Tóm tắt bệnh án của bệnh nhân
     * Gọi hàm summarizeMedicalRecords() từ OllamaAI/GeminiAI
     */
    public function summarize() {
        Security::requireRole(['doctor']);

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

    /**
     * AI Gợi ý Chẩn đoán (AJAX API) - Smart Hospital 4.0
     * 
     * Phân tích tiền sử BN + triệu chứng hiện tại → gợi ý diagnosis + treatment
     */
    public function aiDiagnose() {
        header('Content-Type: application/json; charset=utf-8');
        Security::requireRole(['doctor']);

        $input = json_decode(file_get_contents('php://input'), true);
        $patientId = intval($input['patient_id'] ?? 0);
        $symptoms = trim($input['symptoms'] ?? '');

        if (empty($patientId) || empty($symptoms)) {
            echo json_encode(['success' => false, 'error' => 'Vui lòng chọn bệnh nhân và nhập triệu chứng.']);
            exit;
        }

        // 1. Thu thập ngữ cảnh bệnh nhân
        $patient = $this->patientModel->findById($patientId);
        if (!$patient) {
            echo json_encode(['success' => false, 'error' => 'Không tìm thấy bệnh nhân.']);
            exit;
        }

        // 2. Lấy lịch sử bệnh án cũ
        $pastRecords = $this->recordModel->getByPatientId($patientId);
        $recordContext = '';
        if (!empty($pastRecords)) {
            $recordContext = "LỊCH SỬ BỆNH ÁN (gần nhất trước):\n";
            foreach (array_slice($pastRecords, 0, 5) as $r) {
                $recordContext .= "- [" . ($r['created_at'] ?? '?') . "] Chẩn đoán: " . ($r['diagnosis'] ?? 'N/A') 
                    . " | Điều trị: " . ($r['treatment'] ?? 'N/A') . "\n";
            }
        }

        // 3. Tạo prompt chuyên nghiệp
        $prompt = "Bạn là AI hỗ trợ chẩn đoán của Bệnh viện thông minh NovaCare 4.0.\n\n"
            . "THÔNG TIN BỆNH NHÂN:\n"
            . "- Họ tên: " . ($patient['name'] ?? 'N/A') . "\n"
            . "- Giới tính: " . ($patient['gender'] === 'male' ? 'Nam' : ($patient['gender'] === 'female' ? 'Nữ' : 'Khác')) . "\n"
            . "- Ngày sinh: " . ($patient['date_of_birth'] ?? 'N/A') . "\n"
            . "- Nhóm máu: " . ($patient['blood_type'] ?? 'N/A') . "\n"
            . "- Tiền sử bệnh: " . ($patient['medical_history'] ?? 'Không có thông tin') . "\n\n"
            . (!empty($recordContext) ? $recordContext . "\n" : "")
            . "TRIỆU CHỨNG HIỆN TẠI (Bác sĩ mô tả):\n\"" . $symptoms . "\"\n\n"
            . "YÊU CẦU: Dựa trên tiền sử và triệu chứng, hãy gợi ý chẩn đoán và phương pháp điều trị.\n"
            . "BẮT BUỘC trả về JSON duy nhất, không thêm text khác:\n"
            . "{\n"
            . "  \"diagnosis\": \"Chẩn đoán sơ bộ chi tiết (có thể gồm nhiều khả năng)\",\n"
            . "  \"treatment\": \"Phương pháp điều trị gợi ý chi tiết (thuốc, liều lượng, phác đồ)\",\n"
            . "  \"severity\": \"low / medium / high / critical\",\n"
            . "  \"notes\": \"Lời dặn cho bệnh nhân, chế độ ăn, tái khám\",\n"
            . "  \"differential\": \"Các chẩn đoán phân biệt cần loại trừ (nếu có)\",\n"
            . "  \"suggested_tests\": \"Xét nghiệm/CLS cần làm thêm để xác nhận (nếu có)\"\n"
            . "}";

        // 4. Gọi AI
        require_once __DIR__ . '/../models/BeeknoeeAI.php';
        $ai = new BeeknoeeAI();

        if (!$ai->isConfigured()) {
            // Fallback sang Gemini
            require_once __DIR__ . '/../models/GeminiAI.php';
            $ai = new GeminiAI();
            if (!$ai->isConfigured()) {
                echo json_encode(['success' => false, 'error' => 'AI chưa được cấu hình. Vui lòng kiểm tra config/ai.php.']);
                exit;
            }
        }

        $ai->systemPrompt = "Bạn là trợ lý AI chẩn đoán y khoa chuyên nghiệp của Bệnh viện NovaCare 4.0. CHỈ trả về JSON, KHÔNG giải thích thêm.";
        $result = $ai->chat($prompt);

        if ($result['success']) {
            $parsed = $result['data'];
            if (isset($parsed['diagnosis'])) {
                echo json_encode(['success' => true, 'data' => $parsed]);
            } else {
                // Try parse raw
                $rawText = $result['raw'] ?? '';
                if (preg_match('/\{.*\}/s', $rawText, $matches)) {
                    $jsonDecoded = json_decode($matches[0], true);
                    if ($jsonDecoded && isset($jsonDecoded['diagnosis'])) {
                        echo json_encode(['success' => true, 'data' => $jsonDecoded]);
                        exit;
                    }
                }
                echo json_encode(['success' => false, 'error' => 'AI trả về không đúng định dạng.', 'raw' => $rawText]);
            }
        } else {
            echo json_encode(['success' => false, 'error' => $result['error'] ?? 'Không thể kết nối AI.']);
        }
        exit;
    }
}
