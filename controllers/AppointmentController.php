<?php
/**
 * AppointmentController - Quản lý lịch hẹn
 * Quyền: Patient = đặt lịch; Receptionist = quản lý tổng; Doctor = xem lịch mình
 */
require_once __DIR__ . '/../models/Appointment.php';
require_once __DIR__ . '/../models/Doctor.php';
require_once __DIR__ . '/../models/Patient.php';
require_once __DIR__ . '/../helpers/Security.php';
require_once __DIR__ . '/../helpers/AuditLog.php';

class AppointmentController {
    private $appointmentModel;
    private $doctorModel;
    private $patientModel;

    public function __construct() {
        $this->appointmentModel = new Appointment();
        $this->doctorModel = new Doctor();
        $this->patientModel = new Patient();
    }

    // Danh sách lịch hẹn
    public function index() {
        $user = $_SESSION['user'];
        $role = $user['role'];

        if ($role === 'admin' || $role === 'receptionist' || $role === 'nurse') {
            $appointments = $this->appointmentModel->getAll();
        } elseif ($role === 'doctor') {
            $doctor = $this->doctorModel->findByUserId($user['id']);
            $appointments = $doctor ? $this->appointmentModel->getByDoctorId($doctor['id']) : [];
        } elseif ($role === 'patient') {
            $patient = $this->patientModel->findByUserId($user['id']);
            $appointments = $patient ? $this->appointmentModel->getByPatientId($patient['id']) : [];
        } else {
            $appointments = [];
        }

        $pageTitle = 'Quản lý Lịch hẹn';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/appointments/index.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Form đặt lịch
    public function create() {
        Security::requireRole(['admin', 'receptionist', 'patient']);
        $doctors = $this->doctorModel->getAll();
        
        // Fetch departments for filtering
        $db = new Database();
        $conn = $db->getConnection();
        $stmt = $conn->query("SELECT * FROM departments ORDER BY name ASC");
        $departments = $stmt->fetchAll();

        $pageTitle = 'Đặt lịch khám';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/appointments/create.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Lưu lịch hẹn
    public function store() {
        Security::requireRole(['admin', 'receptionist', 'patient']);
        Security::requirePost('index.php?page=appointments');
        Security::requireCsrf();

        $user = $_SESSION['user'];

        // Tìm patient_id
        $patient = $this->patientModel->findByUserId($user['id']);
        if (!$patient && $user['role'] !== 'receptionist') {
            $_SESSION['error'] = 'Không tìm thấy thông tin bệnh nhân.';
            header('Location: index.php?page=appointments');
            exit;
        }

        $data = [
            'patient_id'       => $user['role'] === 'receptionist' ? ($_POST['patient_id'] ?? 0) : $patient['id'],
            'doctor_id'        => $_POST['doctor_id'] ?? 0,
            'appointment_date' => $_POST['appointment_date'] ?? '',
            'reason'           => trim($_POST['reason'] ?? ''),
        ];

        // Validate ngày không ở quá khứ
        if (!empty($data['appointment_date'])) {
            $appointmentTime = strtotime($data['appointment_date']);
            if ($appointmentTime === false || $appointmentTime < time()) {
                $_SESSION['error'] = 'Ngày khám không hợp lệ hoặc đã ở quá khứ.';
                header('Location: index.php?page=appointments&action=create');
                exit;
            }
        } else {
            $_SESSION['error'] = 'Vui lòng chọn ngày khám.';
            header('Location: index.php?page=appointments&action=create');
            exit;
        }

        // Validate doctor_id
        if (empty($data['doctor_id'])) {
            $_SESSION['error'] = 'Vui lòng chọn bác sĩ.';
            header('Location: index.php?page=appointments&action=create');
            exit;
        }

        // Kiểm tra trùng lịch (cùng bác sĩ, cùng khung giờ ±30 phút)
        if ($this->appointmentModel->checkDuplicate($data['doctor_id'], $data['appointment_date'])) {
            $_SESSION['error'] = 'Bác sĩ đã có lịch hẹn trong khung giờ này. Vui lòng chọn giờ khác.';
            header('Location: index.php?page=appointments&action=create');
            exit;
        }

        try {
            $id = $this->appointmentModel->create($data);
            AuditLog::logCreate('appointments', $id, ['doctor_id' => $data['doctor_id'], 'date' => $data['appointment_date']]);
            
            // Tạo thông báo cho bác sĩ và bệnh nhân
            require_once __DIR__ . '/../models/Notification.php';
            $notif = new Notification();
            $doc = $this->doctorModel->findById($data['doctor_id']);
            $pat = $this->patientModel->findById($data['patient_id']);
            if ($doc && $pat) {
                $notif->create($doc['user_id'], 'Lịch hẹn mới đăng ký', 'Bệnh nhân ' . $pat['name'] . ' đã đặt lịch khám vào ngày ' . date('d/m/Y H:i', strtotime($data['appointment_date'])) . '.');
                $notif->create($pat['user_id'], 'Đăng ký lịch khám', 'Lịch hẹn ngày ' . date('d/m/Y H:i', strtotime($data['appointment_date'])) . ' với BS. ' . $doc['name'] . ' đã gửi và chờ xác nhận.');
            }

            $_SESSION['success'] = 'Đặt lịch khám thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        header('Location: index.php?page=appointments');
        exit;
    }

    // Cập nhật trạng thái (Admin/Receptionist/Doctor/Nurse - POST only)
    public function updateStatus() {
        Security::requireRole(['admin', 'receptionist', 'doctor', 'nurse']);
        Security::requirePost('index.php?page=appointments');
        Security::requireCsrf();

        $id = $_POST['id'] ?? 0;
        $status = $_POST['status'] ?? '';
        $user = $_SESSION['user'];

        // IDOR check: Doctor chỉ được cập nhật lịch hẹn của chính mình
        if ($user['role'] === 'doctor') {
            $doctor = $this->doctorModel->findByUserId($user['id']);
            $appointment = $this->appointmentModel->findById($id);
            if (!$doctor || !$appointment || $appointment['doctor_id'] != $doctor['id']) {
                $_SESSION['error'] = 'Bạn chỉ có thể cập nhật lịch hẹn của chính mình.';
                header('Location: index.php?page=appointments');
                exit;
            }
        }

        $validStatuses = ['pending', 'confirmed', 'cancelled', 'completed', 'emergency'];
        if (in_array($status, $validStatuses)) {
            try {
                $this->appointmentModel->updateStatus($id, $status);
                AuditLog::logUpdate('appointments', $id, null, ['status' => $status]);
                
                // Gửi thông báo cho bệnh nhân
                require_once __DIR__ . '/../models/Notification.php';
                $notif = new Notification();
                $appt = $this->appointmentModel->findById($id);
                if ($appt) {
                    $pat = $this->patientModel->findById($appt['patient_id']);
                    if ($pat) {
                        $statusNames = [
                            'pending' => 'Chờ duyệt',
                            'confirmed' => 'Xác nhận',
                            'cancelled' => 'Hủy bỏ',
                            'completed' => 'Hoàn thành',
                            'emergency' => 'Cấp cứu'
                        ];
                        $statusName = $statusNames[$status] ?? $status;
                        $notif->create($pat['user_id'], 'Cập nhật lịch khám', 'Lịch khám ngày ' . date('d/m/Y H:i', strtotime($appt['appointment_date'])) . ' với BS. ' . $appt['doctor_name'] . ' đã đổi trạng thái thành: ' . $statusName . '.');
                    }
                }

                $_SESSION['success'] = 'Cập nhật trạng thái thành công!';
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            }
        } else {
            $_SESSION['error'] = 'Trạng thái không hợp lệ.';
        }
        header('Location: index.php?page=appointments');
        exit;
    }

    // Xem lịch hẹn dạng Calendar (Admin/Doctor)
    public function calendar() {
        Security::requireRole(['admin', 'receptionist', 'doctor']);
        $user = $_SESSION['user'];
        $role = $user['role'];

        if ($role === 'admin' || $role === 'receptionist') {
            $appointments = $this->appointmentModel->getAll();
        } else {
            $doctor = $this->doctorModel->findByUserId($user['id']);
            $appointments = $doctor ? $this->appointmentModel->getByDoctorId($doctor['id']) : [];
        }

        // Chuyển dữ liệu sang format của FullCalendar
        $events = [];
        foreach ($appointments as $apt) {
            $color = '#3788d8'; // default
            if ($apt['status'] === 'pending') $color = '#f59e0b';
            if ($apt['status'] === 'confirmed') $color = '#10b981';
            if ($apt['status'] === 'completed') $color = '#64748b';
            if ($apt['status'] === 'cancelled') $color = '#ef4444';

            $events[] = [
                'id' => $apt['id'],
                'title' => 'Khám ' . (in_array($role, ['admin','receptionist']) ? '- BS. ' . $apt['doctor_name'] : '- Bệnh nhân ' . $apt['patient_name']),
                'start' => $apt['appointment_date'],
                'backgroundColor' => $color,
                'borderColor' => $color,
                'url' => 'index.php?page=appointments'
            ];
        }

        $eventsJson = json_encode($events);

        $pageTitle = 'Lịch hẹn (Calendar)';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/appointments/calendar.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Bệnh nhân tự hủy lịch hẹn đang chờ
    public function cancelMyAppointment() {
        Security::requireRole('patient');
        Security::requirePost('index.php?page=appointments');
        Security::requireCsrf();

        $id = $_POST['id'] ?? 0;
        $user = $_SESSION['user'];
        $patient = $this->patientModel->findByUserId($user['id']);

        if (!$patient) {
            header('Location: index.php?page=appointments');
            exit;
        }

        $appointment = $this->appointmentModel->findById($id);
        
        if ($appointment && $appointment['patient_id'] == $patient['id'] && $appointment['status'] === 'pending') {
            try {
                $this->appointmentModel->updateStatus($id, 'cancelled');
                AuditLog::logUpdate('appointments', $id, null, ['status' => 'cancelled', 'action' => 'patient_self_cancel']);
                
                // Gửi thông báo cho bác sĩ
                require_once __DIR__ . '/../models/Notification.php';
                $notif = new Notification();
                $doc = $this->doctorModel->findById($appointment['doctor_id']);
                if ($doc) {
                    $notif->create($doc['user_id'], 'Lịch hẹn bị hủy', 'Bệnh nhân ' . $patient['name'] . ' đã hủy lịch hẹn khám ngày ' . date('d/m/Y H:i', strtotime($appointment['appointment_date'])) . '.');
                }

                $_SESSION['success'] = 'Hủy lịch hẹn thành công!';
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            }
        } else {
            $_SESSION['error'] = 'Bạn không có quyền hủy lịch hẹn này hoặc lịch hẹn đã được xử lý.';
        }
        header('Location: index.php?page=appointments');
        exit;
    }

    // API AJAX lấy danh sách khung giờ đã đặt của bác sĩ trong một ngày cụ thể
    public function getDoctorBookedSlots() {
        header('Content-Type: application/json; charset=utf-8');
        Security::requireRole(['admin', 'patient', 'receptionist']);

        $doctorId = intval($_GET['doctor_id'] ?? 0);
        $date = trim($_GET['date'] ?? '');

        if ($doctorId <= 0 || empty($date)) {
            echo json_encode([]);
            exit;
        }

        $db = new Database();
        $conn = $db->getConnection();
        
        // Lấy tất cả lịch hẹn của bác sĩ trong ngày này (ngoại trừ lịch đã bị hủy)
        $sql = "SELECT appointment_date 
                FROM appointments 
                WHERE doctor_id = :doctor_id 
                AND DATE(appointment_date) = :date 
                AND status != 'cancelled' 
                AND deleted_at IS NULL";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':doctor_id', $doctorId, PDO::PARAM_INT);
        $stmt->bindParam(':date', $date, PDO::PARAM_STR);
        $stmt->execute();
        
        $bookedSlots = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $bookedSlots[] = $row['appointment_date'];
        }

        echo json_encode($bookedSlots, JSON_UNESCAPED_UNICODE);
        exit;
    }
}

