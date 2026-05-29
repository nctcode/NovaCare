<?php
/**
 * OnlineConsultation Model - Tư vấn trực tuyến
 */
require_once __DIR__ . '/../config/database.php';

class OnlineConsultation {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getAll() {
        $sql = "SELECT om.*, 
                    pu.name as patient_name, du.name as doctor_name, 
                    a.appointment_date, a.reason
                FROM online_meetings om
                JOIN appointments a ON om.appointment_id = a.id
                JOIN patients p ON a.patient_id = p.id
                JOIN users pu ON p.user_id = pu.id
                JOIN doctors d ON a.doctor_id = d.id
                JOIN users du ON d.user_id = du.id
                ORDER BY om.start_time DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByDoctorUserId($userId) {
        $sql = "SELECT om.*, pu.name as patient_name, du.name as doctor_name, a.appointment_date, a.reason
                FROM online_meetings om
                JOIN appointments a ON om.appointment_id = a.id
                JOIN patients p ON a.patient_id = p.id
                JOIN users pu ON p.user_id = pu.id
                JOIN doctors d ON a.doctor_id = d.id
                JOIN users du ON d.user_id = du.id
                WHERE d.user_id = :user_id
                ORDER BY om.start_time DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByDoctorId($doctorId) {
        $sql = "SELECT om.*, pu.name as patient_name, du.name as doctor_name, a.appointment_date, a.reason
                FROM online_meetings om
                JOIN appointments a ON om.appointment_id = a.id
                JOIN patients p ON a.patient_id = p.id
                JOIN users pu ON p.user_id = pu.id
                JOIN doctors d ON a.doctor_id = d.id
                JOIN users du ON d.user_id = du.id
                WHERE d.id = :doctor_id
                ORDER BY om.start_time DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':doctor_id', $doctorId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByPatientUserId($userId) {
        $sql = "SELECT om.*, pu.name as patient_name, du.name as doctor_name, a.appointment_date, a.reason
                FROM online_meetings om
                JOIN appointments a ON om.appointment_id = a.id
                JOIN patients p ON a.patient_id = p.id
                JOIN users pu ON p.user_id = pu.id
                JOIN doctors d ON a.doctor_id = d.id
                JOIN users du ON d.user_id = du.id
                WHERE p.user_id = :user_id
                ORDER BY om.start_time DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByPatientId($patientId) {
        $sql = "SELECT om.*, pu.name as patient_name, du.name as doctor_name, a.appointment_date, a.reason
                FROM online_meetings om
                JOIN appointments a ON om.appointment_id = a.id
                JOIN patients p ON a.patient_id = p.id
                JOIN users pu ON p.user_id = pu.id
                JOIN doctors d ON a.doctor_id = d.id
                JOIN users du ON d.user_id = du.id
                WHERE p.id = :patient_id
                ORDER BY om.start_time DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':patient_id', $patientId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByMeetingId($meetingId) {
        $sql = "SELECT om.*, pu.name as patient_name, du.name as doctor_name, 
                       pu.email as patient_email, du.email as doctor_email,
                       d.user_id as doctor_user_id, p.user_id as patient_user_id,
                       a.appointment_date, a.reason
                FROM online_meetings om
                JOIN appointments a ON om.appointment_id = a.id
                JOIN patients p ON a.patient_id = p.id
                JOIN users pu ON p.user_id = pu.id
                JOIN doctors d ON a.doctor_id = d.id
                JOIN users du ON d.user_id = du.id
                WHERE om.meeting_id = :meeting_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':meeting_id', $meetingId);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function create($appointmentId) {
        $meetingId = 'NC-' . strtoupper(substr(md5(uniqid()), 0, 8));
        // Use an internal link instead of an external one
        $meetingLink = 'index.php?page=consultations&action=room&id=' . $meetingId;

        // Lấy thời gian từ appointment
        $stmt = $this->conn->prepare("SELECT appointment_date FROM appointments WHERE id = :id");
        $stmt->bindParam(':id', $appointmentId);
        $stmt->execute();
        $apt = $stmt->fetch();

        $sql = "INSERT INTO online_meetings (appointment_id, meeting_id, meeting_link, start_time, status)
                VALUES (:apt_id, :meeting_id, :meeting_link, :start_time, 'scheduled')";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':apt_id', $appointmentId);
        $stmt->bindParam(':meeting_id', $meetingId);
        $stmt->bindParam(':meeting_link', $meetingLink);
        $stmt->bindParam(':start_time', $apt['appointment_date']);
        $stmt->execute();

        return ['id' => $this->conn->lastInsertId(), 'meeting_id' => $meetingId, 'meeting_link' => $meetingLink];
    }

    public function getAvailableAppointments($doctorUserId = null) {
        if ($doctorUserId) {
            $sql = "SELECT a.*, pu.name as patient_name, du.name as doctor_name
                    FROM appointments a
                    JOIN patients p ON a.patient_id = p.id
                    JOIN users pu ON p.user_id = pu.id
                    JOIN doctors d ON a.doctor_id = d.id
                    JOIN users du ON d.user_id = du.id
                    WHERE a.id NOT IN (SELECT appointment_id FROM online_meetings)
                    AND a.status IN ('confirmed','pending')
                    AND d.user_id = :doctor_user_id
                    ORDER BY a.appointment_date ASC";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(':doctor_user_id', $doctorUserId);
        } else {
            $sql = "SELECT a.*, pu.name as patient_name, du.name as doctor_name
                    FROM appointments a
                    JOIN patients p ON a.patient_id = p.id
                    JOIN users pu ON p.user_id = pu.id
                    JOIN doctors d ON a.doctor_id = d.id
                    JOIN users du ON d.user_id = du.id
                    WHERE a.id NOT IN (SELECT appointment_id FROM online_meetings)
                    AND a.status IN ('confirmed','pending')
                    ORDER BY a.appointment_date ASC";
            $stmt = $this->conn->prepare($sql);
        }
        $stmt->execute();
        return $stmt->fetchAll();
    }
    // Lấy tư vấn online mới nhất của bệnh nhân
    public function getRecentByPatientId($patientId, $limit = 2) {
        $sql = "SELECT om.*, pu.name as patient_name, du.name as doctor_name, a.appointment_date, a.reason
                FROM online_meetings om
                JOIN appointments a ON om.appointment_id = a.id
                JOIN patients p ON a.patient_id = p.id
                JOIN users pu ON p.user_id = pu.id
                JOIN doctors d ON a.doctor_id = d.id
                JOIN users du ON d.user_id = du.id
                WHERE p.id = :patient_id
                ORDER BY om.start_time DESC LIMIT :limit";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindValue(':patient_id', $patientId, PDO::PARAM_INT);
        $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}
