<?php
/**
 * MeetingController - Tư vấn online (Online Consultation)
 * 
 * @deprecated Đã được thay thế bởi ConsultationController.
 * File này được giữ lại để tương thích ngược. 
 * Hãy sử dụng ConsultationController cho tất cả tính năng tư vấn online.
 * 
 * Tạo meeting_id, meeting_link. Giả lập email reminder.
 */
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../models/Appointment.php';

class MeetingController {
    private $conn;
    private $appointmentModel;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
        $this->appointmentModel = new Appointment();
    }

    // Danh sách meetings
    public function index() {
        $sql = "SELECT om.*, 
                    a.appointment_date, a.reason,
                    pu.name as patient_name,
                    du.name as doctor_name
                FROM online_meetings om
                JOIN appointments a ON om.appointment_id = a.id
                JOIN patients p ON a.patient_id = p.id
                JOIN users pu ON p.user_id = pu.id
                JOIN doctors d ON a.doctor_id = d.id
                JOIN users du ON d.user_id = du.id
                ORDER BY om.start_time DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $meetings = $stmt->fetchAll();

        // Lấy appointments chưa có meeting
        $sql = "SELECT a.*, pu.name as patient_name, du.name as doctor_name
                FROM appointments a
                JOIN patients p ON a.patient_id = p.id
                JOIN users pu ON p.user_id = pu.id
                JOIN doctors d ON a.doctor_id = d.id
                JOIN users du ON d.user_id = du.id
                WHERE a.id NOT IN (SELECT appointment_id FROM online_meetings)
                AND a.status IN ('pending', 'confirmed')
                ORDER BY a.appointment_date ASC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $availableAppointments = $stmt->fetchAll();

        $pageTitle = 'Tư vấn Online';
        require_once __DIR__ . '/../views/layout/header.php';
        require_once __DIR__ . '/../views/meetings/index.php';
        require_once __DIR__ . '/../views/layout/footer.php';
    }

    // Tạo meeting mới
    public function store() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $appointmentId = $_POST['appointment_id'] ?? 0;

            // Tạo meeting_id ngẫu nhiên
            $meetingId = 'MEET' . str_pad(rand(100, 9999), 4, '0', STR_PAD_LEFT);
            $meetingLink = 'https://meet.benhvien.vn/' . $meetingId;

            // Lấy thông tin appointment
            $appointment = $this->appointmentModel->findById($appointmentId);
            $startTime = $appointment ? $appointment['appointment_date'] : date('Y-m-d H:i:s');

            $sql = "INSERT INTO online_meetings (appointment_id, meeting_id, meeting_link, start_time, status) 
                    VALUES (:appointment_id, :meeting_id, :meeting_link, :start_time, 'scheduled')";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':appointment_id', $appointmentId);
            $stmt->bindParam(':meeting_id', $meetingId);
            $stmt->bindParam(':meeting_link', $meetingLink);
            $stmt->bindParam(':start_time', $startTime);

            try {
                $stmt->execute();

                // Giả lập gửi email reminder
                $this->sendEmailReminder($appointment, $meetingLink);

                $_SESSION['success'] = 'Tạo phòng tư vấn online thành công! Email nhắc nhở đã được gửi (giả lập).';
            } catch (Exception $e) {
                $_SESSION['error'] = 'Lỗi: ' . $e->getMessage();
            }
        }
        header('Location: index.php?page=meetings');
        exit;
    }

    // Giả lập gửi email reminder
    private function sendEmailReminder($appointment, $meetingLink) {
        // Giả lập - trong thực tế sẽ dùng PHPMailer hoặc SMTP
        $logFile = __DIR__ . '/../logs/email_log.txt';
        $dir = dirname($logFile);
        if (!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }

        $log = date('Y-m-d H:i:s') . " | ";
        $log .= "Email gửi đến: " . ($appointment['patient_name'] ?? 'N/A') . " | ";
        $log .= "Bác sĩ: " . ($appointment['doctor_name'] ?? 'N/A') . " | ";
        $log .= "Link: {$meetingLink}\n";

        file_put_contents($logFile, $log, FILE_APPEND);
    }
}
