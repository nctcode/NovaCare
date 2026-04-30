<?php
/**
 * AppointmentController - Quản lý lịch hẹn
 * Quyền: Patient = đặt lịch; Admin = full; Doctor = xem lịch mình
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

        if ($role === 'admin') {
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
        Security::requireRole(['admin', 'patient']);
        $doctors = $this->doctorModel->getAll();
        $pageTitle = 'Đặt lịch khám';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/appointments/create.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Lưu lịch hẹn
    public function store() {
        Security::requireRole(['admin', 'patient']);
        Security::requirePost('index.php?page=appointments');
        Security::requireCsrf();

        $user = $_SESSION['user'];

        // Tìm patient_id
        $patient = $this->patientModel->findByUserId($user['id']);
        if (!$patient && $user['role'] !== 'admin') {
            $_SESSION['error'] = 'Không tìm thấy thông tin bệnh nhân.';
            header('Location: index.php?page=appointments');
            exit;
        }

        $data = [
            'patient_id'       => $user['role'] === 'admin' ? ($_POST['patient_id'] ?? 0) : $patient['id'],
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
            $_SESSION['success'] = 'Đặt lịch khám thành công!';
        } catch (Exception $e) {
            $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
        }
        header('Location: index.php?page=appointments');
        exit;
    }

    // Cập nhật trạng thái (Admin/Doctor - POST only)
    public function updateStatus() {
        Security::requireRole(['admin', 'doctor']);
        Security::requirePost('index.php?page=appointments');
        Security::requireCsrf();

        $id = $_POST['id'] ?? 0;
        $status = $_POST['status'] ?? '';

        $validStatuses = ['pending', 'confirmed', 'cancelled', 'completed'];
        if (in_array($status, $validStatuses)) {
            try {
                $this->appointmentModel->updateStatus($id, $status);
                AuditLog::logUpdate('appointments', $id, null, ['status' => $status]);
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
        Security::requireRole(['admin', 'doctor']);
        $user = $_SESSION['user'];
        $role = $user['role'];

        if ($role === 'admin') {
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
                'title' => 'Khám ' . ($role === 'admin' ? '- BS. ' . $apt['doctor_name'] : '- Bệnh nhân ' . $apt['patient_name']),
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
}
