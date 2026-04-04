<?php
/**
 * Admission Model - Quản lý nhập viện / nội trú
 */
require_once __DIR__ . '/../config/database.php';

class Admission {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Lấy tất cả ca nhập viện
    public function getAll() {
        $sql = "SELECT a.*, 
                    u.name as patient_name, u.phone as patient_phone,
                    du.name as doctor_name,
                    b.bed_number, r.room_number, r.room_type, r.price_per_day,
                    dep.name as department_name
                FROM admissions a
                JOIN patients p ON a.patient_id = p.id
                JOIN users u ON p.user_id = u.id
                JOIN doctors d ON a.doctor_id = d.id
                JOIN users du ON d.user_id = du.id
                JOIN beds b ON a.bed_id = b.id
                JOIN rooms r ON b.room_id = r.id
                LEFT JOIN departments dep ON r.department_id = dep.id
                ORDER BY a.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Lấy ca nhập viện đang active
    public function getActive() {
        $sql = "SELECT a.*, 
                    u.name as patient_name, u.phone as patient_phone,
                    du.name as doctor_name,
                    b.bed_number, r.room_number, r.room_type, r.price_per_day,
                    dep.name as department_name
                FROM admissions a
                JOIN patients p ON a.patient_id = p.id
                JOIN users u ON p.user_id = u.id
                JOIN doctors d ON a.doctor_id = d.id
                JOIN users du ON d.user_id = du.id
                JOIN beds b ON a.bed_id = b.id
                JOIN rooms r ON b.room_id = r.id
                LEFT JOIN departments dep ON r.department_id = dep.id
                WHERE a.status = 'active'
                ORDER BY a.admission_date DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Tìm theo ID
    public function findById($id) {
        $sql = "SELECT a.*, 
                    u.name as patient_name, u.phone as patient_phone, u.email as patient_email,
                    p.address as patient_address, p.date_of_birth, p.blood_type, p.gender,
                    du.name as doctor_name, d.specialty,
                    b.bed_number, b.room_id, r.room_number, r.room_type, r.price_per_day,
                    dep.name as department_name
                FROM admissions a
                JOIN patients p ON a.patient_id = p.id
                JOIN users u ON p.user_id = u.id
                JOIN doctors d ON a.doctor_id = d.id
                JOIN users du ON d.user_id = du.id
                JOIN beds b ON a.bed_id = b.id
                JOIN rooms r ON b.room_id = r.id
                LEFT JOIN departments dep ON r.department_id = dep.id
                WHERE a.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Nhập viện
    public function admit($data) {
        $sql = "INSERT INTO admissions (patient_id, doctor_id, bed_id, admission_date, diagnosis, notes, status) 
                VALUES (:patient_id, :doctor_id, :bed_id, :admission_date, :diagnosis, :notes, 'active')";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':patient_id', $data['patient_id']);
        $stmt->bindParam(':doctor_id', $data['doctor_id']);
        $stmt->bindParam(':bed_id', $data['bed_id']);
        $stmt->bindParam(':admission_date', $data['admission_date']);
        $stmt->bindParam(':diagnosis', $data['diagnosis']);
        $stmt->bindParam(':notes', $data['notes']);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    // Xuất viện
    public function discharge($id) {
        $sql = "UPDATE admissions SET status = 'discharged', discharge_date = NOW() WHERE id = :id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        return $stmt->execute();
    }

    // Lấy theo bệnh nhân
    public function getByPatientId($patientId) {
        $sql = "SELECT a.*, du.name as doctor_name, b.bed_number, r.room_number
                FROM admissions a
                JOIN doctors d ON a.doctor_id = d.id
                JOIN users du ON d.user_id = du.id
                JOIN beds b ON a.bed_id = b.id
                JOIN rooms r ON b.room_id = r.id
                WHERE a.patient_id = :patient_id
                ORDER BY a.admission_date DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':patient_id', $patientId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Đếm
    public function count() {
        $sql = "SELECT COUNT(*) as total FROM admissions";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch()['total'];
    }

    public function countActive() {
        $sql = "SELECT COUNT(*) as total FROM admissions WHERE status = 'active'";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetch()['total'];
    }
}
