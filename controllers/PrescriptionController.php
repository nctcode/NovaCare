<?php
/**
 * PrescriptionController - Quản lý đơn thuốc
 * - Doctor: tạo đơn, xem đơn mình kê
 * - Pharmacist: xem tất cả, cấp phát
 * - Patient: xem đơn thuốc của mình
 */
require_once __DIR__ . '/../models/Prescription.php';
require_once __DIR__ . '/../models/Doctor.php';
require_once __DIR__ . '/../models/Patient.php';
require_once __DIR__ . '/../models/Medicine.php';
require_once __DIR__ . '/../helpers/Security.php';

class PrescriptionController {
    private $prescriptionModel;
    private $doctorModel;
    private $patientModel;
    private $medicineModel;

    public function __construct() {
        $this->prescriptionModel = new Prescription();
        $this->doctorModel = new Doctor();
        $this->patientModel = new Patient();
        $this->medicineModel = new Medicine();
    }

    // Danh sách đơn thuốc
    public function index() {
        $user = $_SESSION['user'];
        $role = $user['role'];

        if ($role === 'admin' || $role === 'pharmacist') {
            $prescriptions = $this->prescriptionModel->getAll();
        } elseif ($role === 'doctor') {
            $doctor = $this->doctorModel->findByUserId($user['id']);
            $prescriptions = $doctor ? $this->prescriptionModel->getByDoctorId($doctor['id']) : [];
        } elseif ($role === 'patient') {
            $patient = $this->patientModel->findByUserId($user['id']);
            $prescriptions = $patient ? $this->prescriptionModel->getByPatientId($patient['id']) : [];
        } else {
            $prescriptions = [];
        }

        $pageTitle = 'Đơn thuốc';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/prescriptions/index.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Form tạo đơn thuốc (Doctor)
    public function create() {
        Security::requireRole('doctor');
        $user = $_SESSION['user'];
        $doctor = $this->doctorModel->findByUserId($user['id']);
        $medicalRecords = $doctor ? $this->prescriptionModel->getMedicalRecordsByDoctorId($doctor['id']) : [];
        $medicines = $this->medicineModel->getAll();

        $pageTitle = 'Tạo Đơn thuốc';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/prescriptions/create.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Lưu đơn thuốc
    public function store() {
        Security::requireRole('doctor');
        Security::requirePost('index.php?page=prescriptions');
        Security::requireCsrf();

        $user = $_SESSION['user'];
        $doctor = $this->doctorModel->findByUserId($user['id']);

        if (!$doctor) {
            $_SESSION['error'] = 'Không tìm thấy thông tin bác sĩ.';
            header('Location: index.php?page=prescriptions');
            exit;
        }

        // Tạo đơn thuốc
        $prescriptionData = [
            'medical_record_id' => $_POST['medical_record_id'] ?? 0,
            'doctor_id'         => $doctor['id'],
        ];

        try {
            // Validate thuốc trước khi tạo đơn
            $medicineIds   = $_POST['medicine_id'] ?? [];
            $quantities    = $_POST['quantity'] ?? [];
            $dosages       = $_POST['dosage'] ?? [];
            $durations     = $_POST['duration'] ?? [];
            $instructions  = $_POST['instructions'] ?? [];

            // Kiểm tra thuốc hết hạn + đủ tồn kho trước khi kê
            for ($i = 0; $i < count($medicineIds); $i++) {
                if (!empty($medicineIds[$i])) {
                    $med = $this->medicineModel->findById($medicineIds[$i]);
                    if (!$med) continue;

                    if ($this->medicineModel->isExpired($medicineIds[$i])) {
                        $_SESSION['error'] = "Thuốc '{$med['name']}' đã hết hạn (HSD: {$med['expiry_date']}). Không thể kê đơn.";
                        header('Location: index.php?page=prescriptions&action=create');
                        exit;
                    }

                    $qty = max(1, intval($quantities[$i] ?? 1));
                    $available = $med['quantity'] - ($med['reserved'] ?? 0);
                    if ($available < $qty) {
                        $_SESSION['error'] = "Thuốc '{$med['name']}' không đủ tồn kho khả dụng (còn {$med['quantity']}, đã đặt trước {$med['reserved']}, cần {$qty}).";
                        header('Location: index.php?page=prescriptions&action=create');
                        exit;
                    }
                }
            }

            $prescriptionId = $this->prescriptionModel->create($prescriptionData);

            // Thêm các thuốc vào đơn + trừ tồn kho theo số lượng thực tế
            for ($i = 0; $i < count($medicineIds); $i++) {
                if (!empty($medicineIds[$i])) {
                    $qty = max(1, intval($quantities[$i] ?? 1));
                    $itemData = [
                        'prescription_id' => $prescriptionId,
                        'medicine_id'     => $medicineIds[$i],
                        'quantity'        => $qty,
                        'dosage'          => $dosages[$i] ?? '',
                        'duration'        => $durations[$i] ?? '',
                        'instructions'    => $instructions[$i] ?? '',
                    ];
                    $this->prescriptionModel->addItem($itemData);

                    // Đặt trước tồn kho theo số lượng kê thực tế (chưa trừ kho vật lý)
                    $this->medicineModel->reserveStock($medicineIds[$i], $qty);
                }
            }

            // Gửi thông báo cho bệnh nhân
            require_once __DIR__ . '/../models/Notification.php';
            $notif = new Notification();
            $presc = $this->prescriptionModel->findById($prescriptionId);
            if ($presc) {
                $pat = $this->patientModel->findById($presc['patient_id']);
                if ($pat) {
                    $notif->create($pat['user_id'], 'Đơn thuốc mới', 'Bác sĩ ' . $presc['doctor_name'] . ' đã kê đơn thuốc mới #' . $prescriptionId . ' cho bạn.');
                }
            }

            $_SESSION['success'] = 'Tạo đơn thuốc thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        header('Location: index.php?page=prescriptions');
        exit;
    }

    // Xem chi tiết đơn thuốc
    public function view() {
        $user = $_SESSION['user'];
        $id = $_GET['id'] ?? 0;
        $prescription = $this->prescriptionModel->findById($id);
        $items = $this->prescriptionModel->getItems($id);

        if (!$prescription) {
            $_SESSION['error'] = 'Không tìm thấy đơn thuốc.';
            header('Location: index.php?page=prescriptions');
            exit;
        }

        // IDOR check: bệnh nhân chỉ xem được đơn thuốc của chính mình
        if ($user['role'] === 'patient') {
            $patient = $this->patientModel->findByUserId($user['id']);
            if (!$patient || $prescription['patient_id'] != $patient['id']) {
                $_SESSION['error'] = 'Bạn không có quyền xem đơn thuốc này.';
                header('Location: index.php?page=prescriptions');
                exit;
            }
        }

        // IDOR check: bác sĩ chỉ xem đơn thuốc mình kê
        if ($user['role'] === 'doctor') {
            $doctor = $this->doctorModel->findByUserId($user['id']);
            if (!$doctor || $prescription['doctor_id'] != $doctor['id']) {
                $_SESSION['error'] = 'Bạn không có quyền xem đơn thuốc này.';
                header('Location: index.php?page=prescriptions');
                exit;
            }
        }

        $pageTitle = 'Chi tiết Đơn thuốc';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/prescriptions/view.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Duyệt đơn thuốc (Pharmacist)
    public function approve() {
        Security::requireRole(['admin', 'pharmacist']);
        Security::requirePost('index.php?page=prescriptions');
        Security::requireCsrf();

        $id = $_POST['id'] ?? 0;
        $notes = trim($_POST['pharmacist_notes'] ?? '');

        try {
            $prescription = $this->prescriptionModel->findById($id);
            if (!$prescription) {
                throw new Exception('Không tìm thấy đơn thuốc.');
            }
            if ($prescription['status'] !== 'paid') {
                throw new Exception('Đơn thuốc chưa được thanh toán hoặc đã được xử lý.');
            }

            $user = $_SESSION['user'];
            $result = $this->prescriptionModel->approve($id, $user['id'], $notes);
            if (!$result) {
                throw new Exception('Lỗi hệ thống khi duyệt đơn thuốc.');
            }
            $_SESSION['success'] = 'Duyệt đơn thuốc thành công cho đơn thuốc #' . $id;
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        header("Location: index.php?page=prescriptions&action=view&id=" . $id);
        exit;
    }

    // Xác nhận giao thuốc (Pharmacist)
    public function dispense() {
        Security::requireRole(['admin', 'pharmacist']);
        Security::requirePost('index.php?page=prescriptions');
        Security::requireCsrf();

        $id = $_POST['id'] ?? 0;
        try {
            $prescription = $this->prescriptionModel->findById($id);
            if (!$prescription) {
                throw new Exception('Không tìm thấy đơn thuốc.');
            }
            if ($prescription['status'] !== 'paid') {
                throw new Exception('Đơn thuốc chưa được thanh toán. Vui lòng thanh toán trước khi giao thuốc.');
            }

            $this->prescriptionModel->updateStatus($id, 'dispensed');
            $_SESSION['success'] = 'Xác nhận giao thuốc thành công cho đơn thuốc #' . $id;
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        header("Location: index.php?page=prescriptions&action=view&id=" . $id);
        exit;
    }

    // Hủy đơn thuốc (Doctor hoặc Admin)
    public function cancel() {
        Security::requireRole(['admin', 'doctor']);
        Security::requirePost('index.php?page=prescriptions');
        Security::requireCsrf();

        $id = $_POST['id'] ?? 0;
        try {
            $prescription = $this->prescriptionModel->findById($id);
            if (!$prescription) {
                throw new Exception('Không tìm thấy đơn thuốc.');
            }
            if ($prescription['status'] === 'dispensed') {
                throw new Exception('Không thể hủy đơn thuốc đã được giao.');
            }

            $this->prescriptionModel->updateStatus($id, 'cancelled');
            $_SESSION['success'] = 'Hủy đơn thuốc #' . $id . ' thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        header("Location: index.php?page=prescriptions&action=view&id=" . $id);
        exit;
    }

    // Gợi ý thuốc bằng AI (AJAX Endpoint gọi từ Javascript)
    public function suggestMedicineAI() {
        header('Content-Type: application/json; charset=utf-8');
        
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            echo json_encode(['success' => false, 'error' => 'Method not allowed']);
            exit;
        }

        $input = json_decode(file_get_contents('php://input'), true);
        $diagnosis = trim($input['diagnosis'] ?? '');
        
        if (empty($diagnosis)) {
            echo json_encode(['success' => false, 'error' => 'Không có dữ liệu chẩn đoán']);
            exit;
        }

        // Đảm bảo các hằng số cấu hình AI tồn tại trước khi khởi tạo Model
        if (!defined('AI_SYSTEM_PROMPT')) define('AI_SYSTEM_PROMPT', 'Bạn là trợ lý AI y tế thông minh.');
        if (!defined('AI_TEMPERATURE')) define('AI_TEMPERATURE', 0.2);

        require_once __DIR__ . '/../models/BeeknoeeAI.php';
        $ai = new BeeknoeeAI();
        
        if (!$ai->isConfigured()) {
            echo json_encode(['success' => false, 'error' => 'API Key của BEEKNOEE chưa được cấu hình đúng trong env.php.']);
            exit;
        }

        // Tạo câu lệnh y khoa chuyên sâu ép AI trả về chuẩn JSON
        $prompt = "Bạn là một Dược sĩ lâm sàng cấp cao. Dựa vào chẩn đoán sau của bệnh nhân: '{$diagnosis}'. \n"
                . "Hãy đề xuất phác đồ điều trị bằng thuốc tối ưu nhất.\n"
                . "YÊU CẦU BẮT BUỘC: Bạn CHỈ ĐƯỢC PHÉP TRẢ VỀ kết quả theo ĐÚNG định dạng JSON sau, tuyệt đối KHÔNG giải thích thêm hay dùng định dạng markdown (như ```json):\n"
                . "{\n"
                . "  \"medicines\": [\n"
                . "    {\n"
                . "      \"name\": \"Tên thuốc (Kèm hàm lượng, VD: Amoxicillin 500mg)\",\n"
                . "      \"type\": \"Phân loại (VD: Kháng sinh, Giảm đau...)\",\n"
                . "      \"dosage\": \"Liều dùng 1 lần (VD: 1 viên, 10ml)\",\n"
                . "      \"duration\": \"Thời gian dùng (VD: 5 ngày)\",\n"
                . "      \"instructions\": \"Hướng dẫn chi tiết (VD: Uống sau khi ăn sáng)\"\n"
                . "    }\n"
                . "  ]\n"
                . "}";

        // Gọi hàm chat của BeeknoeeAI (đóng vai trò là system/user)
        $result = $ai->chat($prompt);

        if ($result['success']) {
            $jsonString = $result['data'] ?? $result['raw'];
            
            // Dọn dẹp JSON phòng trường hợp AI chèn ký tự ```json 
            $jsonString = preg_replace('/```json/i', '', $jsonString);
            $jsonString = preg_replace('/```/', '', $jsonString);
            $jsonString = trim($jsonString);

            $parsed = json_decode($jsonString, true);
            
            if (json_last_error() === JSON_ERROR_NONE && isset($parsed['medicines'])) {
                echo json_encode(['success' => true, 'medicines' => $parsed['medicines']]);
            } else {
                // Trả về mảng rỗng nếu không parse được
                echo json_encode(['success' => false, 'error' => 'Lỗi dịch dữ liệu từ AI', 'raw' => $jsonString]);
            }
        } else {
            echo json_encode(['success' => false, 'error' => $result['error']]);
        }
        exit;
    }
}
