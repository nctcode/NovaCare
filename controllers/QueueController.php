<?php
/**
 * QueueController - Quản lý Hàng chờ / Số thứ tự
 * 
 * - Receptionist: cấp số, hủy, xem tất cả
 * - Doctor: gọi BN, bắt đầu khám, hoàn thành
 * - Admin: full access
 * - Public: màn hình hiển thị (display)
 */
require_once __DIR__ . '/../models/QueueTicket.php';
require_once __DIR__ . '/../models/ExaminationRoom.php';
require_once __DIR__ . '/../models/Doctor.php';
require_once __DIR__ . '/../models/Appointment.php';
require_once __DIR__ . '/../helpers/Security.php';
require_once __DIR__ . '/../helpers/AuditLog.php';

class QueueController {
    private $queueModel;
    private $roomModel;

    public function __construct() {
        $this->queueModel = new QueueTicket();
        $this->roomModel = new ExaminationRoom();
    }

    /**
     * Danh sách hàng chờ hôm nay
     */
    public function index() {
        Security::requireRole(['receptionist', 'doctor', 'nurse']);
        $user = $_SESSION['user'];

        $filters = ['date' => date('Y-m-d')];

        // Bác sĩ chỉ xem queue của khoa mình hoặc được chỉ định riêng cho mình
        if ($user['role'] === 'doctor') {
            $doctorModel = new Doctor();
            $doctor = $doctorModel->findByUserId($user['id']);
            if ($doctor) {
                $filters['doctor_id'] = $doctor['id'];
                $filters['doctor_dept_ids'] = $doctor['department_ids'] ?? [];
            }
        }

        if (!empty($_GET['status'])) {
            $filters['status'] = $_GET['status'];
        }
        if (!empty($_GET['department_id'])) {
            $filters['department_id'] = $_GET['department_id'];
        }

        $tickets = $this->queueModel->getToday($filters);
        $stats = $this->queueModel->getStatsToday();
        $departments = $this->queueModel->getDepartments();

        $pageTitle = 'Hàng chờ Khám bệnh';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/queue/index.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    /**
     * Form cấp số thứ tự (Check-in)
     */
     public function checkin() {
        Security::requireRole(['receptionist']);
        
        $patients = $this->queueModel->getPatients();
        $departments = $this->queueModel->getDepartments();
        $rooms = $this->roomModel->getActive();
        $nextNumber = $this->queueModel->getNextNumber();

        // Tải chi tiết lịch hẹn để tự điền form nếu được yêu cầu
        $selectedAppointment = null;
        if (!empty($_GET['appointment_id'])) {
            $appointmentModel = new Appointment();
            $selectedAppointment = $appointmentModel->findById((int)$_GET['appointment_id']);
            if ($selectedAppointment) {
                // Lấy thêm khoa của bác sĩ đã đặt lịch
                $doctorModel = new Doctor();
                $doctor = $doctorModel->findById($selectedAppointment['doctor_id']);
                if ($doctor) {
                    $selectedAppointment['department_id'] = $doctor['department_id'] ?? (!empty($doctor['department_ids']) ? $doctor['department_ids'][0] : null);
                }
            }
        }

        $pageTitle = 'Cấp Số Thứ Tự';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/queue/checkin.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    /**
     * Lưu số thứ tự mới
     */
    public function storeCheckin() {
        Security::requireRole(['receptionist']);
        Security::requirePost('index.php?page=queue');
        Security::requireCsrf();

        $data = [
            'patient_id'          => $_POST['patient_id'] ?? 0,
            'department_id'       => (!empty($_POST['department_id'])) ? $_POST['department_id'] : null,
            'doctor_id'           => (!empty($_POST['doctor_id'])) ? $_POST['doctor_id'] : null,
            'examination_room_id' => (!empty($_POST['examination_room_id'])) ? $_POST['examination_room_id'] : null,
            'appointment_id'      => (!empty($_POST['appointment_id'])) ? $_POST['appointment_id'] : null,
            'priority'            => $_POST['priority'] ?? 'normal',
            'insurance_verified'  => isset($_POST['insurance_verified']) ? 1 : 0,
            'notes'               => trim($_POST['notes'] ?? ''),
            'queue_date'          => date('Y-m-d'),
        ];

        if (empty($data['patient_id'])) {
            $_SESSION['error'] = 'Vui lòng chọn bệnh nhân.';
            header('Location: index.php?page=queue&action=checkin');
            exit;
        }

        try {
            // Tự động phân phòng khám nếu để trống
            if (empty($data['examination_room_id'])) {
                $data['examination_room_id'] = $this->roomModel->autoAssignRoom($data['department_id']);
            }

            $result = $this->queueModel->create($data);

            // Cập nhật trạng thái lịch hẹn sang 'completed' (đã đến và lấy số)
            if (!empty($data['appointment_id'])) {
                $appointmentModel = new Appointment();
                $appointmentModel->updateStatus($data['appointment_id'], 'completed');
            }

            // Lấy thông tin phòng khám để hướng dẫn bệnh nhân
            $roomDetails = '';
            if ($data['examination_room_id']) {
                $room = $this->roomModel->findById($data['examination_room_id']);
                if ($room) {
                    $roomDetails = ' tại <strong>' . htmlspecialchars($room['room_name']) 
                        . '</strong> (' . htmlspecialchars($room['department_name'] ?? 'Khoa khám') . ')';
                }
            }

            $_SESSION['success'] = 'Cấp số thứ tự thành công! Số: <span class="fs-4 fw-bold text-success">' 
                . str_pad($result['ticket_number'], 4, '0', STR_PAD_LEFT) . '</span>. Vui lòng hướng dẫn bệnh nhân đến khám' . $roomDetails . '.';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }

        header('Location: index.php?page=queue');
        exit;
    }

    /**
     * Gọi BN tiếp theo (Doctor / Admin)
     */
    public function callNext() {
        Security::requireRole(['doctor']);
        Security::requirePost('index.php?page=queue');
        Security::requireCsrf();

        $user = $_SESSION['user'];
        $departmentId = $_POST['department_id'] ?? null;
        $doctorId = null;
        $deptParam = $departmentId;

        if ($user['role'] === 'doctor') {
            $doctorModel = new Doctor();
            $doctor = $doctorModel->findByUserId($user['id']);
            if ($doctor) {
                $doctorId = $doctor['id'];
                if (empty($deptParam)) {
                    $deptParam = $doctor['department_ids'] ?? [];
                }
            }
        }

        $ticket = $this->queueModel->callNext($deptParam, $doctorId);
        if ($ticket) {
            $_SESSION['success'] = 'Đang gọi số <strong>' 
                . str_pad($ticket['ticket_number'], 4, '0', STR_PAD_LEFT) 
                . '</strong> - ' . htmlspecialchars($ticket['patient_name']);
        } else {
            $_SESSION['error'] = 'Không có bệnh nhân nào đang chờ.';
        }

        header('Location: index.php?page=queue');
        exit;
    }

    /**
     * Cập nhật trạng thái (generic)
     */
    public function updateStatus() {
        Security::requireRole(['receptionist', 'doctor']);
        Security::requirePost('index.php?page=queue');
        Security::requireCsrf();

        $id = $_POST['id'] ?? 0;
        $status = $_POST['status'] ?? '';
        $validStatuses = ['waiting', 'called', 'in_progress', 'completed', 'cancelled'];

        if (!in_array($status, $validStatuses)) {
            $_SESSION['error'] = 'Trạng thái không hợp lệ.';
            header('Location: index.php?page=queue');
            exit;
        }

        // Kiểm tra quyền: receptionist chỉ được cancel, doctor mới được call/start/complete
        $user = $_SESSION['user'];
        if ($user['role'] === 'receptionist' && !in_array($status, ['cancelled', 'waiting'])) {
            $_SESSION['error'] = 'Bạn không có quyền thực hiện thao tác này.';
            header('Location: index.php?page=queue');
            exit;
        }

        try {
            $this->queueModel->updateStatus($id, $status);
            $statusLabels = [
                'called' => 'Đã gọi',
                'in_progress' => 'Đang khám',
                'completed' => 'Hoàn thành',
                'cancelled' => 'Đã hủy',
                'waiting' => 'Chờ khám'
            ];
            $_SESSION['success'] = 'Cập nhật trạng thái: ' . ($statusLabels[$status] ?? $status);
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }

        header('Location: index.php?page=queue');
        exit;
    }

    /**
     * Màn hình hiển thị số thứ tự (Public - không cần đăng nhập)
     */
    public function display() {
        $calledTickets = $this->queueModel->getCurrentCalled();
        $waitingTickets = $this->queueModel->getNextWaiting(10);
        $stats = $this->queueModel->getStatsToday();

        // Render fullscreen, không dùng layout header/footer
        require_once __DIR__ . '/../views/queue/display.php';
        exit;
    }

    /**
     * API JSON - Dữ liệu realtime cho màn hình display
     */
    public function displayData() {
        $calledTickets = $this->queueModel->getCurrentCalled();
        $waitingTickets = $this->queueModel->getNextWaiting(10);
        $stats = $this->queueModel->getStatsToday();

        header('Content-Type: application/json; charset=utf-8');
        echo json_encode([
            'called' => $calledTickets,
            'waiting' => $waitingTickets,
            'stats' => $stats,
            'timestamp' => date('H:i:s')
        ], JSON_UNESCAPED_UNICODE);
        exit;
    }

    /**
     * API JSON - Lấy phòng khám theo khoa (cho AJAX dropdown)
     */
    public function getRooms() {
        $departmentId = $_GET['department_id'] ?? 0;
        $rooms = $departmentId 
            ? $this->roomModel->getByDepartment($departmentId) 
            : $this->roomModel->getActive();

        header('Content-Type: application/json');
        echo json_encode($rooms, JSON_UNESCAPED_UNICODE);
        exit;
    }
}
