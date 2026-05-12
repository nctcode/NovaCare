<?php
/**
 * ConsultationController - Tư vấn trực tuyến (Online Meetings)
 * Replaces old MeetingController with improved functionality
 */
require_once __DIR__ . '/../models/OnlineConsultation.php';
require_once __DIR__ . '/../helpers/Security.php';
require_once __DIR__ . '/../helpers/MailHelper.php';

class ConsultationController {
    private $model;

    public function __construct() {
        $this->model = new OnlineConsultation();
    }

    public function index() {
        $user = $_SESSION['user'];

        if ($user['role'] === 'admin' || $user['role'] === 'receptionist') {
            $meetings = $this->model->getAll();
            $availableAppointments = $this->model->getAvailableAppointments();
        } elseif ($user['role'] === 'doctor') {
            $meetings = $this->model->getByDoctorUserId($user['id']);
            $availableAppointments = [];
        } elseif ($user['role'] === 'patient') {
            $meetings = $this->model->getByPatientUserId($user['id']);
            $availableAppointments = [];
        } else {
            $meetings = [];
            $availableAppointments = [];
        }

        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/consultations/index.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    public function store() {
        Security::requireRole(['admin', 'receptionist']);
        Security::requirePost('index.php?page=consultations');
        Security::requireCsrf();

        $appointmentId = $_POST['appointment_id'];
        $result = $this->model->create($appointmentId);
        
        if ($result) {
            $this->sendEmailReminder($result['meeting_id']);
            $_SESSION['success'] = 'Phòng tư vấn đã tạo thành công! Mã phòng: ' . htmlspecialchars($result['meeting_id']) . '. Hệ thống đã gửi email (giả lập) tới bệnh nhân.';
        } else {
            $_SESSION['error'] = 'Không thể tạo phòng tư vấn.';
        }
        header('Location: index.php?page=consultations');
        exit;
    }

    public function room() {
        $meetingId = $_GET['id'] ?? '';
        if (!$meetingId) {
            $_SESSION['error'] = 'Không tìm thấy phòng họp.';
            header('Location: index.php?page=consultations');
            exit;
        }

        $meeting = $this->model->getByMeetingId($meetingId);
        if (!$meeting) {
            $_SESSION['error'] = 'Phòng họp không tồn tại.';
            header('Location: index.php?page=consultations');
            exit;
        }

        $user = $_SESSION['user'];

        // Access check
        $hasAccess = false;
        if ($user['role'] === 'admin') {
            $hasAccess = true;
        } elseif ($user['role'] === 'doctor' && $user['id'] == $meeting['doctor_user_id']) {
            $hasAccess = true;
        } elseif ($user['role'] === 'patient' && $user['id'] == $meeting['patient_user_id']) {
            $hasAccess = true;
        }

        if (!$hasAccess) {
            $_SESSION['error'] = 'Bạn không có quyền tham gia phòng tư vấn này.';
            header('Location: index.php?page=consultations');
            exit;
        }

        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/consultations/room.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    private function sendEmailReminder($meetingId) {
        $meeting = $this->model->getByMeetingId($meetingId);
        if (!$meeting) return;

        $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : 'localhost';
        $link = "http://{$host}/CNM1/index.php?page=consultations&action=room&id={$meetingId}";

        // Gửi email thật qua SMTP
        $isSent = MailHelper::sendMeetingLink(
            $meeting['patient_email'],
            $meeting['patient_name'],
            $meeting['doctor_name'],
            $meeting['reason'],
            $link
        );

        // Fallback ghi log nếu chưa cấu hình Gmail (tránh lỗi khi demo)
        if (!$isSent) {
            $logFile = __DIR__ . '/../logs/email_log.txt';
            $dir = dirname($logFile);
            if (!is_dir($dir)) mkdir($dir, 0777, true);
            $log = date('Y-m-d H:i:s') . " | FAILED TO SEND EMAIL, LOGGING INSTEAD | EMAIL TO: " . $meeting['patient_email'] . "\n";
            file_put_contents($logFile, $log, FILE_APPEND);
        }
    }
}
