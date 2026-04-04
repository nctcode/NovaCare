<?php
/**
 * AppointmentController - Quản lý lịch hẹn
 * - Patient: đặt lịch khám
 * - Admin: xem tất cả, cập nhật trạng thái
 * - Doctor: xem lịch của mình
 */
require_once __DIR__ . '/../models/Appointment.php';
require_once __DIR__ . '/../models/Doctor.php';
require_once __DIR__ . '/../models/Patient.php';

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
        $doctors = $this->doctorModel->getAll();
        $pageTitle = 'Đặt lịch khám';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/appointments/create.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Lưu lịch hẹn
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

            try {
                $this->appointmentModel->create($data);
                $_SESSION['success'] = 'Đặt lịch khám thành công! 📧 (Hệ thống đã gửi email xác nhận đến bạn).';
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            }
        }
        header('Location: index.php?page=appointments');
        exit;
    }

    // Cập nhật trạng thái (Admin)
    public function updateStatus() {
        $id = $_GET['id'] ?? 0;
        $status = $_GET['status'] ?? '';

        $validStatuses = ['pending', 'confirmed', 'cancelled', 'completed'];
        if (in_array($status, $validStatuses)) {
            try {
                $this->appointmentModel->updateStatus($id, $status);
                $_SESSION['success'] = 'Cập nhật trạng thái thành công!';
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            }
        }
        header('Location: index.php?page=appointments');
        exit;
    }
}
