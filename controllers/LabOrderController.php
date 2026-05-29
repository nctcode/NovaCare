<?php
/**
 * LabOrderController - Quản lý Chỉ định Cận lâm sàng
 * 
 * - Doctor: Tạo chỉ định, xem kết quả
 * - Technician: Nhận chỉ định, nhập kết quả
 * - Admin: Xem tất cả
 */
require_once __DIR__ . '/../models/LabOrder.php';
require_once __DIR__ . '/../models/Doctor.php';
require_once __DIR__ . '/../helpers/Security.php';
require_once __DIR__ . '/../helpers/AuditLog.php';

class LabOrderController {
    private $labModel;

    public function __construct() {
        $this->labModel = new LabOrder();
    }

    // Danh sách chỉ định CLS
    public function index() {
        Security::requireRole(['admin', 'doctor', 'technician']);
        $user = $_SESSION['user'];

        if ($user['role'] === 'doctor') {
            require_once __DIR__ . '/../models/Doctor.php';
            $doctorModel = new Doctor();
            $doctor = $doctorModel->findByUserId($user['id']);
            $orders = $doctor ? $this->labModel->getByDoctorId($doctor['id']) : [];
        } elseif ($user['role'] === 'technician') {
            $orders = $this->labModel->getAll();
        } else {
            $orders = $this->labModel->getAll();
        }

        $pageTitle = 'Chỉ định Cận lâm sàng';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/lab_orders/index.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Form tạo chỉ định CLS (BS)
    public function create() {
        Security::requireRole(['admin', 'doctor']);
        $patients = $this->labModel->getPatients();

        $pageTitle = 'Tạo Chỉ định CLS';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/lab_orders/create.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Lưu chỉ định CLS mới
    public function store() {
        Security::requireRole(['admin', 'doctor']);
        Security::requirePost('index.php?page=lab-orders');
        Security::requireCsrf();

        $user = $_SESSION['user'];

        // Lấy doctor_id
        $doctorId = null;
        if ($user['role'] === 'doctor') {
            require_once __DIR__ . '/../models/Doctor.php';
            $doctorModel = new Doctor();
            $doctor = $doctorModel->findByUserId($user['id']);
            $doctorId = $doctor ? $doctor['id'] : null;
        } else {
            $doctorId = $_POST['doctor_id'] ?? null;
        }

        if (!$doctorId) {
            $_SESSION['error'] = 'Không xác định được bác sĩ chỉ định.';
            header('Location: index.php?page=lab-orders');
            exit;
        }

        $data = [
            'patient_id'     => $_POST['patient_id'] ?? 0,
            'doctor_id'      => $doctorId,
            'appointment_id' => $_POST['appointment_id'] ?? null,
            'order_type'     => $_POST['order_type'] ?? 'lab_test',
            'test_name'      => trim($_POST['test_name'] ?? ''),
            'priority'       => $_POST['priority'] ?? 'normal',
            'notes'          => trim($_POST['notes'] ?? ''),
            'created_by'     => $user['id'],
        ];

        if (empty($data['patient_id']) || empty($data['test_name'])) {
            $_SESSION['error'] = 'Vui lòng điền đầy đủ thông tin bắt buộc.';
            header('Location: index.php?page=lab-orders&action=create');
            exit;
        }

        try {
            $this->labModel->create($data);
            $_SESSION['success'] = 'Tạo chỉ định CLS thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        header('Location: index.php?page=lab-orders');
        exit;
    }

    // Xem chi tiết chỉ định + kết quả
    public function view() {
        Security::requireRole(['admin', 'doctor', 'technician']);
        $id = $_GET['id'] ?? 0;
        $order = $this->labModel->findById($id);

        if (!$order) {
            $_SESSION['error'] = 'Không tìm thấy chỉ định.';
            header('Location: index.php?page=lab-orders');
            exit;
        }

        $result = $this->labModel->getResult($id);

        $pageTitle = 'Chi tiết Chỉ định CLS';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/lab_orders/view.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Form nhập kết quả (KTV)
    public function result() {
        Security::requireRole(['admin', 'technician']);
        $id = $_GET['id'] ?? 0;
        $order = $this->labModel->findById($id);

        if (!$order) {
            $_SESSION['error'] = 'Không tìm thấy chỉ định.';
            header('Location: index.php?page=lab-orders');
            exit;
        }

        if ($order['status'] === 'completed') {
            $_SESSION['error'] = 'Chỉ định này đã có kết quả.';
            header('Location: index.php?page=lab-orders&action=view&id=' . $id);
            exit;
        }

        // Cập nhật trạng thái thành in_progress nếu đang pending
        if ($order['status'] === 'pending') {
            $this->labModel->updateStatus($id, 'in_progress');
        }

        $pageTitle = 'Nhập Kết quả CLS';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/lab_orders/result.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Lưu kết quả CLS
    public function saveResult() {
        Security::requireRole(['admin', 'technician']);
        Security::requirePost('index.php?page=lab-orders');
        Security::requireCsrf();

        $user = $_SESSION['user'];
        $labOrderId = $_POST['lab_order_id'] ?? 0;

        $imagePath = null;
        if (isset($_FILES['result_image']) && $_FILES['result_image']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['result_image']['tmp_name'];
            $fileName = $_FILES['result_image']['name'];
            $fileExtension = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));
            
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
            if (in_array($fileExtension, $allowedExtensions)) {
                $uploadDir = __DIR__ . '/../uploads/lab_results/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0755, true);
                }
                
                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                $destPath = $uploadDir . $newFileName;
                
                if (move_uploaded_file($fileTmpPath, $destPath)) {
                    $imagePath = 'uploads/lab_results/' . $newFileName;
                }
            }
        }

        $data = [
            'lab_order_id'  => $labOrderId,
            'technician_id' => $user['id'],
            'result_text'   => trim($_POST['result_text'] ?? ''),
            'result_value'  => trim($_POST['result_value'] ?? ''),
            'normal_range'  => trim($_POST['normal_range'] ?? ''),
            'unit'          => trim($_POST['unit'] ?? ''),
            'conclusion'    => $_POST['conclusion'] ?? 'normal',
            'image_path'    => $imagePath,
        ];

        if (empty($data['result_text'])) {
            $_SESSION['error'] = 'Vui lòng nhập kết quả.';
            header('Location: index.php?page=lab-orders&action=result&id=' . $labOrderId);
            exit;
        }

        try {
            $this->labModel->saveResult($data);
            
            // Gửi thông báo cho bác sĩ chỉ định và bệnh nhân
            require_once __DIR__ . '/../models/Notification.php';
            require_once __DIR__ . '/../models/Doctor.php';
            require_once __DIR__ . '/../models/Patient.php';
            
            $notif = new Notification();
            $doctorModel = new Doctor();
            $patientModel = new Patient();
            
            $order = $this->labModel->findById($labOrderId);
            if ($order) {
                $doc = $doctorModel->findById($order['doctor_id']);
                $pat = $patientModel->findById($order['patient_id']);
                
                if ($doc) {
                    $notif->create($doc['user_id'], 'Kết quả CLS của bệnh nhân', 'Đã có kết quả xét nghiệm/chẩn đoán hình ảnh "' . $order['test_name'] . '" cho bệnh nhân ' . $order['patient_name'] . '.');
                }
                if ($pat) {
                    $notif->create($pat['user_id'], 'Kết quả cận lâm sàng', 'Đã có kết quả chỉ định cận lâm sàng "' . $order['test_name'] . '" của bạn.');
                }
            }

            $_SESSION['success'] = 'Lưu kết quả thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        header('Location: index.php?page=lab-orders&action=view&id=' . $labOrderId);
        exit;
    }

    // Trợ lý AI phân tích & tạo kết quả thông minh 4.0
    public function analyzeAI() {
        header('Content-Type: application/json; charset=utf-8');
        Security::requireRole(['admin', 'technician']);

        $input = json_decode(file_get_contents('php://input'), true);
        $testName = trim($input['test_name'] ?? '');
        $notes = trim($input['notes'] ?? '');

        if (empty($testName)) {
            echo json_encode(['success' => false, 'error' => 'Tên xét nghiệm không hợp lệ.']);
            exit;
        }

        require_once __DIR__ . '/../models/BeeknoeeAI.php';
        $ai = new BeeknoeeAI();

        $prompt = "Bạn là trợ lý y tế thông minh AI của bệnh viện thông minh NovaCare 4.0. Hãy trích xuất/giả lập kết quả cận lâm sàng chi tiết, thực tế cho chỉ định cận lâm sàng sau:\n"
                . "- Tên chỉ định/xét nghiệm: {$testName}\n"
                . "- Ghi chú lâm sàng của bác sĩ: {$notes}\n\n"
                . "Bắt buộc trả về kết quả dưới định dạng JSON duy nhất, không thêm bất kỳ văn bản nào khác ngoài JSON:\n"
                . "{\n"
                . "  \"result_text\": \"Mô tả kết quả cận lâm sàng chi tiết, chuyên nghiệp ở cấp độ bác sĩ chuyên khoa\",\n"
                . "  \"result_value\": \"Giá trị cụ thể (ví dụ: '7.2' hoặc 'Dương tính' hoặc 'Không phát hiện tổn thương')\",\n"
                . "  \"normal_range\": \"Khoảng giá trị bình thường hoặc tiêu chuẩn tham chiếu (ví dụ: '4.0 - 10.0' hoặc 'Âm tính' hoặc 'Bình thường')\",\n"
                . "  \"unit\": \"Đơn vị đo (ví dụ: 'mmol/L' hoặc 'mg/dL' hoặc để trống nếu là chẩn đoán hình ảnh)\",\n"
                . "  \"conclusion\": \"Phải là một trong ba giá trị sau: 'normal' (nếu bình thường), 'abnormal' (nếu có bất thường nhẹ), 'critical' (nếu nguy cấp)\"\n"
                . "}";

        // Đặt system prompt chuyên nghiệp cho việc trích xuất
        $ai->systemPrompt = "Bạn là máy phân tích kết quả cận lâm sàng tự động của bệnh viện thông minh NovaCare 4.0. Chỉ trả về JSON duy nhất.";
        
        $result = $ai->chat($prompt);

        if ($result['success']) {
            $parsed = $result['data'];
            if (isset($parsed['result_text'])) {
                echo json_encode(['success' => true, 'data' => $parsed]);
            } else {
                $rawText = $result['raw'] ?? '';
                if (preg_match('/\{.*\}/s', $rawText, $matches)) {
                    $jsonDecoded = json_decode($matches[0], true);
                    if ($jsonDecoded && isset($jsonDecoded['result_text'])) {
                        echo json_encode(['success' => true, 'data' => $jsonDecoded]);
                        exit;
                    }
                }
                echo json_encode(['success' => false, 'error' => 'Kết quả AI trả về không đúng định dạng JSON.', 'raw' => $rawText]);
            }
        } else {
            echo json_encode(['success' => false, 'error' => $result['error'] ?? 'Không thể kết nối dịch vụ AI.']);
        }
        exit;
    }

    // Hủy chỉ định
    public function cancel() {
        Security::requireRole(['admin', 'doctor']);
        Security::requirePost('index.php?page=lab-orders');
        Security::requireCsrf();

        $id = $_POST['id'] ?? 0;
        $this->labModel->updateStatus($id, 'cancelled');
        $_SESSION['success'] = 'Đã hủy chỉ định CLS.';
        header('Location: index.php?page=lab-orders');
        exit;
    }
}
