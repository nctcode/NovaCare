<?php
/**
 * Appointment Model - Quản lý lịch hẹn khám bệnh
 */
require_once __DIR__ . '/../config/database.php';

class Appointment {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Lấy tất cả lịch hẹn
    public function getAll() {
        $sql = "SELECT a.*, 
                    pu.name as patient_name, 
                    du.name as doctor_name,
                    dep.name as department_name
                FROM appointments a 
                JOIN patients p ON a.patient_id = p.id 
                JOIN users pu ON p.user_id = pu.id 
                JOIN doctors d ON a.doctor_id = d.id 
                JOIN users du ON d.user_id = du.id 
                LEFT JOIN departments dep ON d.department_id = dep.id
                ORDER BY a.appointment_date DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Lấy lịch hẹn theo bác sĩ (doctor_id từ bảng doctors)
    public function getByDoctorId($doctorId) {
        $sql = "SELECT a.*, 
                    pu.name as patient_name, pu.phone as patient_phone
                FROM appointments a 
                JOIN patients p ON a.patient_id = p.id 
                JOIN users pu ON p.user_id = pu.id 
                WHERE a.doctor_id = :doctor_id 
                ORDER BY a.appointment_date DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':doctor_id', $doctorId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Lấy lịch hẹn theo bệnh nhân
    public function getByPatientId($patientId) {
        $sql = "SELECT a.*, 
                    du.name as doctor_name, d.specialty
                FROM appointments a 
                JOIN doctors d ON a.doctor_id = d.id 
                JOIN users du ON d.user_id = du.id 
                WHERE a.patient_id = :patient_id 
                ORDER BY a.appointment_date DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':patient_id', $patientId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Tìm lịch hẹn theo ID
    public function findById($id) {
        $sql = "SELECT a.*, 
                    pu.name as patient_name, 
                    du.name as doctor_name
                FROM appointments a 
                JOIN patients p ON a.patient_id = p.id 
                JOIN users pu ON p.user_id = pu.id 
                JOIN doctors d ON a.doctor_id = d.id 
                JOIN users du ON d.user_id = du.id 
                WHERE a.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Tạo lịch hẹn mới
    public function create($data) {
        $sql = "INSERT INTO appointments (patient_id, doctor_id, appointment_date, reason, status) 
                VALUES (:patient_id, :doctor_id, :appointment_date, :reason, 'pending')";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':patient_id', $data['patient_id']);
        $stmt->bindParam(':doctor_id', $data['doctor_id']);
        $stmt->bindParam(':appointment_date', $data['appointment_date']);
        $stmt->bindParam(':reason', $data['reason']);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    // Cập nhật trạng thái lịch hẹn
    public function updateStatus($id, $status) {
        $sql = "UPDATE appointments SET status = :status WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Đếm lịch hẹn
    public function count() {
        $sql = "SELECT COUNT(*) as total FROM appointments";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'];
    }

    // Đếm lịch hẹn pending
    public function countPending() {
        return $this->countByStatus('pending');
    }

    // Đếm lịch hẹn theo trạng thái
    public function countByStatus($status) {
        $sql = "SELECT COUNT(*) as total FROM appointments WHERE status = :status";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        $row = $stmt->fetch();
        return $row['total'];
    }
}
