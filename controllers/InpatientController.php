<?php
/**
 * InpatientController - Quản lý nhập viện / nội trú
 * - Receptionist: xem tất cả, nhập viện, xuất viện, quản lý phòng
 * - Doctor: xem bệnh nhân mình phụ trách
 */
require_once __DIR__ . '/../models/Room.php';
require_once __DIR__ . '/../models/Admission.php';
require_once __DIR__ . '/../models/Patient.php';
require_once __DIR__ . '/../models/Doctor.php';
require_once __DIR__ . '/../helpers/Security.php';

class InpatientController {
    private $roomModel;
    private $admissionModel;
    private $patientModel;
    private $doctorModel;

    public function __construct() {
        $this->roomModel = new Room();
        $this->admissionModel = new Admission();
        $this->patientModel = new Patient();
        $this->doctorModel = new Doctor();
    }

    // Danh sách nhập viện
    public function index() {
        Security::requireRole(['admin', 'doctor', 'nurse']);
        $user = $_SESSION['user'];
        $filter = $_GET['filter'] ?? 'active';

        $doctorId = null;
        if ($user['role'] === 'doctor') {
            $doctor = $this->doctorModel->findByUserId($user['id']);
            if ($doctor) {
                $doctorId = $doctor['id'];
            }
        }

        if ($filter === 'all') {
            $admissions = $this->admissionModel->getAll($doctorId);
        } else {
            $admissions = $this->admissionModel->getActive($doctorId);
        }

        $pendingAdmissions = $this->admissionModel->getPending($doctorId);

        $stats = [
            'totalRooms' => $this->roomModel->count(),
            'availableBeds' => $this->roomModel->countAvailableBeds(),
            'activeAdmissions' => $this->admissionModel->countActive(),
            'totalAdmissions' => $this->admissionModel->count(),
        ];

        $pageTitle = 'Quản lý Nội trú';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/inpatient/index.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Sơ đồ phòng
    public function rooms() {
        Security::requireRole(['admin', 'doctor', 'nurse']);
        $rooms = $this->roomModel->getAll();

        // Lấy giường cho mỗi phòng
        foreach ($rooms as &$room) {
            $room['beds'] = $this->roomModel->getBeds($room['id']);
        }

        $pageTitle = 'Sơ đồ Phòng bệnh';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/inpatient/rooms.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Form nhập viện
    public function admit() {
        Security::requireRole(['admin', 'nurse', 'doctor']);
        $patients = [];
        $allPatients = $this->patientModel->getAll();
        foreach ($allPatients as $p) {
            $patients[] = $p;
        }

        $doctors = $this->doctorModel->getAll();
        $beds = $this->roomModel->getAvailableBeds();

        $user = $_SESSION['user'];
        $currentDoctorId = null;
        if ($user['role'] === 'doctor') {
            $doctor = $this->doctorModel->findByUserId($user['id']);
            if ($doctor) {
                $currentDoctorId = $doctor['id'];
            }
        }
        $preselectedPatientId = $_GET['patient_id'] ?? null;

        $pageTitle = 'Nhập viện bệnh nhân';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/inpatient/admit.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Lưu nhập viện
    public function storeAdmit() {
        Security::requireRole(['admin', 'nurse', 'doctor']);
        Security::requirePost('index.php?page=inpatient');
        Security::requireCsrf();

        try {
            $bedId = !empty($_POST['bed_id']) ? $_POST['bed_id'] : null;
            $status = ($bedId !== null) ? 'active' : 'pending';

            $data = [
                'patient_id' => $_POST['patient_id'],
                'doctor_id' => $_POST['doctor_id'],
                'bed_id' => $bedId,
                'status' => $status,
                'admission_date' => $_POST['admission_date'] ?: date('Y-m-d H:i:s'),
                'diagnosis' => trim($_POST['diagnosis'] ?? ''),
                'notes' => trim($_POST['notes'] ?? ''),
            ];

            $admissionId = $this->admissionModel->admit($data);

            if ($bedId !== null) {
                // Cập nhật trạng thái giường
                $this->roomModel->updateBedStatus($data['bed_id'], 'occupied');

                // Lấy room_id từ bed để refresh room status (qua Model)
                $roomId = $this->roomModel->getRoomIdByBedId($data['bed_id']);
                if ($roomId) {
                    $this->roomModel->refreshRoomStatus($roomId);
                }
                $_SESSION['success'] = 'Nhập viện thành công! Mã: #' . $admissionId;
            } else {
                $_SESSION['success'] = 'Chỉ định nhập viện thành công! Hồ sơ đang ở trạng thái Chờ xếp giường. Mã: #' . $admissionId;
            }

            header("Location: index.php?page=inpatient&action=detail&id=$admissionId");
            exit;

        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            header('Location: index.php?page=inpatient&action=admit');
            exit;
        }
    }

    // Chi tiết ca nhập viện
    public function detail() {
        Security::requireRole(['admin', 'doctor', 'nurse']);
        $id = $_GET['id'] ?? 0;
        $admission = $this->admissionModel->findById($id);

        if (!$admission) {
            $_SESSION['error'] = 'Không tìm thấy thông tin nhập viện.';
            header('Location: index.php?page=inpatient');
            exit;
        }

        // IDOR check: bác sĩ chỉ xem bệnh nhân mình phụ trách
        $user = $_SESSION['user'];
        if ($user['role'] === 'doctor') {
            $doctor = $this->doctorModel->findByUserId($user['id']);
            if (!$doctor || $admission['doctor_id'] != $doctor['id']) {
                $_SESSION['error'] = 'Bạn không có quyền xem thông tin nhập viện này.';
                header('Location: index.php?page=inpatient');
                exit;
            }
        }

        // Tính số ngày nằm viện
        $startDate = new DateTime($admission['admission_date']);
        $endDate = $admission['discharge_date'] ? new DateTime($admission['discharge_date']) : new DateTime();
        $days = $startDate->diff($endDate)->days;
        if ($days == 0) $days = 1;
        $roomCost = $days * ($admission['price_per_day'] ?? 0);

        $pageTitle = 'Chi tiết Nhập viện #' . $id;
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/inpatient/detail.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Xuất viện
    public function discharge() {
        Security::requireRole(['admin', 'nurse', 'doctor']);
        Security::requirePost('index.php?page=inpatient');
        Security::requireCsrf();

        $id = $_POST['id'] ?? 0;

        try {
            $admission = $this->admissionModel->findById($id);
            if (!$admission) throw new Exception('Không tìm thấy.');

            $this->admissionModel->discharge($id);

            // Giải phóng giường
            $this->roomModel->updateBedStatus($admission['bed_id'], 'available');
            $this->roomModel->refreshRoomStatus($admission['room_id']);

            $_SESSION['success'] = 'Xuất viện thành công! Giường ' . $admission['bed_number'] . ' (Phòng ' . $admission['room_number'] . ') đã được giải phóng.';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }

        header('Location: index.php?page=inpatient');
        exit;
    }

    // Cập nhật ghi chú chăm sóc (Nurse/Doctor)
    public function updateNotes() {
        Security::requireRole(['admin', 'doctor', 'nurse']);
        Security::requirePost('index.php?page=inpatient');
        Security::requireCsrf();

        $id = $_POST['id'] ?? 0;
        $notes = trim($_POST['notes'] ?? '');

        try {
            $admission = $this->admissionModel->findById($id);
            if (!$admission) throw new Exception('Không tìm thấy ca nhập viện.');

            $newNotes = $admission['notes'];
            if ($newNotes) {
                $newNotes .= "\n---\n";
            }
            $newNotes .= "[" . date('d/m/Y H:i') . " - " . $_SESSION['user']['name'] . "]:\n" . $notes;

            $this->admissionModel->updateNotes($id, $newNotes);
            $_SESSION['success'] = 'Đã thêm ghi chú chăm sóc thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }

        header("Location: index.php?page=inpatient&action=detail&id=$id");
        exit;
    }

    // Form ghi nhận chăm sóc điều dưỡng
    public function addNursingRecord() {
        Security::requireRole(['admin', 'nurse']);
        $id = $_GET['id'] ?? 0;
        $admission = $this->admissionModel->findById($id);

        if (!$admission) {
            $_SESSION['error'] = 'Không tìm thấy ca nhập viện.';
            header('Location: index.php?page=inpatient');
            exit;
        }

        $pageTitle = 'Ghi nhận Chăm sóc';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/inpatient/nursing_record.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Lưu hồ sơ chăm sóc điều dưỡng
    public function saveNursingRecord() {
        Security::requireRole(['admin', 'nurse']);
        Security::requirePost('index.php?page=inpatient');
        Security::requireCsrf();

        $admissionId = $_POST['admission_id'] ?? 0;
        $user = $_SESSION['user'];

        // Lấy nurse_id
        require_once __DIR__ . '/../models/Nurse.php';
        $nurseModel = new Nurse();
        $nurse = $nurseModel->findByUserId($user['id']);
        
        if (!$nurse && $user['role'] !== 'admin') {
            $_SESSION['error'] = 'Không tìm thấy thông tin y tá.';
            header("Location: index.php?page=inpatient&action=detail&id=$admissionId");
            exit;
        }

        $nurseId = $nurse ? $nurse['id'] : 1; // fallback for admin

        require_once __DIR__ . '/../models/NursingRecord.php';
        $nrModel = new NursingRecord();

        $data = [
            'admission_id'       => $admissionId,
            'nurse_id'           => $nurseId,
            'temperature'        => $_POST['temperature'] ?? null,
            'blood_pressure_sys' => $_POST['blood_pressure_sys'] ?? null,
            'blood_pressure_dia' => $_POST['blood_pressure_dia'] ?? null,
            'heart_rate'         => $_POST['heart_rate'] ?? null,
            'respiratory_rate'   => $_POST['respiratory_rate'] ?? null,
            'spo2'               => $_POST['spo2'] ?? null,
            'care_notes'         => trim($_POST['care_notes'] ?? ''),
            'medication_given'   => trim($_POST['medication_given'] ?? ''),
            'diet_notes'         => trim($_POST['diet_notes'] ?? ''),
        ];

        try {
            $nrModel->create($data);
            $_SESSION['success'] = 'Ghi nhận chăm sóc thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }

        header("Location: index.php?page=inpatient&action=detail&id=$admissionId");
        exit;
    }

    // Lịch sử chăm sóc điều dưỡng (JSON cho AJAX)
    public function nursingHistory() {
        Security::requireRole(['admin', 'doctor', 'nurse']);
        $id = $_GET['id'] ?? 0;

        require_once __DIR__ . '/../models/NursingRecord.php';
        $nrModel = new NursingRecord();
        $records = $nrModel->getByAdmissionId($id);

        header('Content-Type: application/json');
        echo json_encode($records, JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * AI Cảnh báo Vitals bất thường - Smart Hospital 4.0
     * Phân tích các chỉ số sinh tồn vừa nhập → cảnh báo nếu bất thường
     */
    public function aiVitalsAlert() {
        header('Content-Type: application/json; charset=utf-8');
        Security::requireRole(['admin', 'nurse', 'doctor']);

        $input = json_decode(file_get_contents('php://input'), true);
        $admissionId = intval($input['admission_id'] ?? 0);

        if (!$admissionId) {
            echo json_encode(['success' => false, 'error' => 'Thiếu thông tin nhập viện.']);
            exit;
        }

        $admission = $this->admissionModel->findById($admissionId);
        if (!$admission) {
            echo json_encode(['success' => false, 'error' => 'Không tìm thấy ca nhập viện.']);
            exit;
        }

        // Vitals hiện tại từ input
        $vitals = [
            'temperature' => $input['temperature'] ?? null,
            'blood_pressure_sys' => $input['blood_pressure_sys'] ?? null,
            'blood_pressure_dia' => $input['blood_pressure_dia'] ?? null,
            'heart_rate' => $input['heart_rate'] ?? null,
            'respiratory_rate' => $input['respiratory_rate'] ?? null,
            'spo2' => $input['spo2'] ?? null,
        ];

        // Lấy lịch sử vitals trước đó
        require_once __DIR__ . '/../models/NursingRecord.php';
        $nrModel = new NursingRecord();
        $history = $nrModel->getByAdmissionId($admissionId);

        $historyContext = '';
        if (!empty($history)) {
            $historyContext = "LỊCH SỬ VITALS (gần nhất trước):\n";
            foreach (array_slice($history, 0, 5) as $h) {
                $parts = [];
                if ($h['temperature']) $parts[] = "T=" . $h['temperature'] . "°C";
                if ($h['blood_pressure_sys']) $parts[] = "HA=" . $h['blood_pressure_sys'] . "/" . ($h['blood_pressure_dia'] ?? '?');
                if ($h['heart_rate']) $parts[] = "HR=" . $h['heart_rate'];
                if ($h['spo2']) $parts[] = "SpO2=" . $h['spo2'] . "%";
                $historyContext .= "- [" . ($h['recorded_at'] ?? '?') . "] " . implode(", ", $parts) . "\n";
            }
        }

        $vitalsDesc = [];
        if ($vitals['temperature']) $vitalsDesc[] = "Nhiệt độ: " . $vitals['temperature'] . "°C";
        if ($vitals['blood_pressure_sys']) $vitalsDesc[] = "Huyết áp: " . $vitals['blood_pressure_sys'] . "/" . ($vitals['blood_pressure_dia'] ?? '?') . " mmHg";
        if ($vitals['heart_rate']) $vitalsDesc[] = "Nhịp tim: " . $vitals['heart_rate'] . " bpm";
        if ($vitals['respiratory_rate']) $vitalsDesc[] = "Nhịp thở: " . $vitals['respiratory_rate'] . " /phút";
        if ($vitals['spo2']) $vitalsDesc[] = "SpO2: " . $vitals['spo2'] . "%";

        if (empty($vitalsDesc)) {
            echo json_encode(['success' => false, 'error' => 'Chưa nhập chỉ số sinh tồn nào.']);
            exit;
        }

        $prompt = "Bạn là AI giám sát sinh hiệu của Bệnh viện thông minh NovaCare 4.0.\n\n"
            . "BỆNH NHÂN NỘI TRÚ:\n"
            . "- Họ tên: " . ($admission['patient_name'] ?? 'N/A') . "\n"
            . "- Chẩn đoán: " . ($admission['diagnosis'] ?? 'N/A') . "\n"
            . "- Ngày nhập viện: " . ($admission['admission_date'] ?? 'N/A') . "\n\n"
            . (!empty($historyContext) ? $historyContext . "\n" : "")
            . "CHỈ SỐ SINH TỒN VỪA ĐO:\n" . implode("\n", $vitalsDesc) . "\n\n"
            . "YÊU CẦU: Phân tích các chỉ số này, so sánh với ngưỡng bình thường và trend trước đó.\n"
            . "BẮT BUỘC trả về JSON duy nhất:\n"
            . "{\n"
            . "  \"alert_level\": \"normal / warning / danger / critical\",\n"
            . "  \"abnormal_items\": [\"Liệt kê chỉ số bất thường (nếu có)\"],\n"
            . "  \"trend\": \"improving / stable / deteriorating\",\n"
            . "  \"analysis\": \"Phân tích ngắn gọn tình trạng bệnh nhân\",\n"
            . "  \"recommendation\": \"Khuyến nghị hành động cho y tá/bác sĩ\"\n"
            . "}";

        require_once __DIR__ . '/../models/BeeknoeeAI.php';
        $ai = new BeeknoeeAI();
        if (!$ai->isConfigured()) {
            require_once __DIR__ . '/../models/GeminiAI.php';
            $ai = new GeminiAI();
            if (!$ai->isConfigured()) {
                echo json_encode(['success' => false, 'error' => 'AI chưa được cấu hình.']);
                exit;
            }
        }

        $ai->systemPrompt = "Bạn là AI giám sát y tế. CHỈ trả về JSON, KHÔNG giải thích.";
        $result = $ai->chat($prompt);

        if ($result['success']) {
            $parsed = $result['data'];
            if (isset($parsed['alert_level'])) {
                echo json_encode(['success' => true, 'data' => $parsed]);
            } else {
                $rawText = $result['raw'] ?? '';
                if (preg_match('/\{.*\}/s', $rawText, $matches)) {
                    $jsonDecoded = json_decode($matches[0], true);
                    if ($jsonDecoded && isset($jsonDecoded['alert_level'])) {
                        echo json_encode(['success' => true, 'data' => $jsonDecoded]);
                        exit;
                    }
                }
                echo json_encode(['success' => false, 'error' => 'AI trả về không đúng định dạng.']);
            }
        } else {
            echo json_encode(['success' => false, 'error' => $result['error'] ?? 'Không thể kết nối AI.']);
        }
        exit;
    }

    // Giao diện xếp giường cho bệnh nhân đang chờ
    public function assignBed() {
        Security::requireRole(['admin', 'nurse', 'receptionist']);
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $_SESSION['error'] = 'Không tìm thấy mã hồ sơ nhập viện.';
            header('Location: index.php?page=inpatient');
            exit;
        }

        $admission = $this->admissionModel->findById($id);
        if (!$admission || $admission['status'] !== 'pending') {
            $_SESSION['error'] = 'Hồ sơ không tồn tại hoặc đã được xếp giường.';
            header('Location: index.php?page=inpatient');
            exit;
        }

        $beds = $this->roomModel->getAvailableBeds();
        $pageTitle = 'Xếp giường bệnh nhân';
        
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/inpatient/assign_bed.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Lưu xếp giường
    public function storeAssignBed() {
        Security::requireRole(['admin', 'nurse', 'receptionist']);
        Security::requirePost('index.php?page=inpatient');
        Security::requireCsrf();

        $id = $_GET['id'] ?? null;
        $bedId = $_POST['bed_id'] ?? null;
        $admissionDate = $_POST['admission_date'] ?? null;

        if (!$id || !$bedId) {
            $_SESSION['error'] = 'Thiếu thông tin hồ sơ hoặc giường bệnh.';
            header('Location: index.php?page=inpatient');
            exit;
        }

        try {
            // Cập nhật hồ sơ admission
            $this->admissionModel->assignBed($id, $bedId, $admissionDate);

            // Cập nhật trạng thái giường thành occupied
            $this->roomModel->updateBedStatus($bedId, 'occupied');

            // Cập nhật trạng thái phòng
            $roomId = $this->roomModel->getRoomIdByBedId($bedId);
            if ($roomId) {
                $this->roomModel->refreshRoomStatus($roomId);
            }

            $_SESSION['success'] = 'Xếp giường bệnh thành công!';
            header("Location: index.php?page=inpatient&action=detail&id=$id");
            exit;

        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            header("Location: index.php?page=inpatient&action=assignBed&id=$id");
            exit;
        }
    }
}
