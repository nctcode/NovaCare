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

    public function create($appointmentId) {
        $meetingId = 'NC-' . strtoupper(substr(md5(uniqid()), 0, 8));
        $meetingLink = 'https://meet.novacare.com/' . $meetingId;

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

    public function getAvailableAppointments() {
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
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
