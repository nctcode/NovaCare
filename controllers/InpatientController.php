<?php
/**
 * InpatientController - Quản lý nhập viện / nội trú
 * - Admin: xem tất cả, nhập viện, xuất viện, quản lý phòng
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
        Security::requireRole(['admin', 'doctor']);
        $user = $_SESSION['user'];
        $filter = $_GET['filter'] ?? 'active';

        if ($filter === 'all') {
            $admissions = $this->admissionModel->getAll();
        } else {
            $admissions = $this->admissionModel->getActive();
        }

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
        Security::requireRole('admin');
        $patients = [];
        $allPatients = $this->patientModel->getAll();
        foreach ($allPatients as $p) {
            $patients[] = $p;
        }

        $doctors = $this->doctorModel->getAll();
        $beds = $this->roomModel->getAvailableBeds();

        $pageTitle = 'Nhập viện bệnh nhân';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/inpatient/admit.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Lưu nhập viện
    public function storeAdmit() {
        Security::requireRole('admin');
        Security::requirePost('index.php?page=inpatient');
        Security::requireCsrf();

        try {
            $data = [
                'patient_id' => $_POST['patient_id'],
                'doctor_id' => $_POST['doctor_id'],
                'bed_id' => $_POST['bed_id'],
                'admission_date' => $_POST['admission_date'] ?: date('Y-m-d H:i:s'),
                'diagnosis' => trim($_POST['diagnosis'] ?? ''),
                'notes' => trim($_POST['notes'] ?? ''),
            ];

            $admissionId = $this->admissionModel->admit($data);

            // Cập nhật trạng thái giường
            $this->roomModel->updateBedStatus($data['bed_id'], 'occupied');

            // Lấy room_id từ bed để refresh room status (qua Model)
            $roomId = $this->roomModel->getRoomIdByBedId($data['bed_id']);
            if ($roomId) {
                $this->roomModel->refreshRoomStatus($roomId);
            }

            $_SESSION['success'] = 'Nhập viện thành công! Mã: #' . $admissionId;
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
        Security::requireRole(['admin', 'doctor']);
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
        $roomCost = $days * $admission['price_per_day'];

        $pageTitle = 'Chi tiết Nhập viện #' . $id;
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/inpatient/detail.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Xuất viện
    public function discharge() {
        Security::requireRole('admin');
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
}
