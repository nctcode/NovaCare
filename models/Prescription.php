<?php
/**
 * Prescription Model - Quản lý đơn thuốc
 */
require_once __DIR__ . '/../config/database.php';

class Prescription {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    // Lấy tất cả đơn thuốc
    public function getAll() {
        $sql = "SELECT pr.*, 
                    du.name as doctor_name,
                    pu.name as patient_name,
                    mr.diagnosis
                FROM prescriptions pr
                JOIN doctors d ON pr.doctor_id = d.id
                JOIN users du ON d.user_id = du.id
                JOIN medical_records mr ON pr.medical_record_id = mr.id
                JOIN patients p ON mr.patient_id = p.id
                JOIN users pu ON p.user_id = pu.id
                ORDER BY pr.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Lấy đơn thuốc theo bác sĩ
    public function getByDoctorId($doctorId) {
        $sql = "SELECT pr.*, 
                    du.name as doctor_name,
                    pu.name as patient_name,
                    mr.diagnosis
                FROM prescriptions pr
                JOIN doctors d ON pr.doctor_id = d.id
                JOIN users du ON d.user_id = du.id
                JOIN medical_records mr ON pr.medical_record_id = mr.id
                JOIN patients p ON mr.patient_id = p.id
                JOIN users pu ON p.user_id = pu.id
                WHERE pr.doctor_id = :doctor_id
                ORDER BY pr.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':doctor_id', $doctorId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Lấy đơn thuốc theo bệnh nhân
    public function getByPatientId($patientId) {
        $sql = "SELECT pr.*, 
                    du.name as doctor_name,
                    mr.diagnosis
                FROM prescriptions pr
                JOIN doctors d ON pr.doctor_id = d.id
                JOIN users du ON d.user_id = du.id
                JOIN medical_records mr ON pr.medical_record_id = mr.id
                WHERE mr.patient_id = :patient_id
                ORDER BY pr.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':patient_id', $patientId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Tìm đơn thuốc theo ID
    public function findById($id) {
        $sql = "SELECT pr.*, 
                    du.name as doctor_name,
                    pu.name as patient_name,
                    mr.diagnosis, mr.notes
                FROM prescriptions pr
                JOIN doctors d ON pr.doctor_id = d.id
                JOIN users du ON d.user_id = du.id
                JOIN medical_records mr ON pr.medical_record_id = mr.id
                JOIN patients p ON mr.patient_id = p.id
                JOIN users pu ON p.user_id = pu.id
                WHERE pr.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    // Lấy items trong đơn thuốc
    public function getItems($prescriptionId) {
        $sql = "SELECT pi.*, m.name as medicine_name, m.price
                FROM prescription_items pi
                JOIN medicines m ON pi.medicine_id = m.id
                WHERE pi.prescription_id = :prescription_id";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':prescription_id', $prescriptionId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Tạo đơn thuốc mới
    public function create($data) {
        $sql = "INSERT INTO prescriptions (medical_record_id, doctor_id) 
                VALUES (:medical_record_id, :doctor_id)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':medical_record_id', $data['medical_record_id']);
        $stmt->bindParam(':doctor_id', $data['doctor_id']);
        $stmt->execute();
        return $this->conn->lastInsertId();
    }

    // Thêm thuốc vào đơn
    public function addItem($data) {
        $sql = "INSERT INTO prescription_items (prescription_id, medicine_id, dosage, duration, instructions) 
                VALUES (:prescription_id, :medicine_id, :dosage, :duration, :instructions)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':prescription_id', $data['prescription_id']);
        $stmt->bindParam(':medicine_id', $data['medicine_id']);
        $stmt->bindParam(':dosage', $data['dosage']);
        $stmt->bindParam(':duration', $data['duration']);
        $stmt->bindParam(':instructions', $data['instructions']);
        return $stmt->execute();
    }

    // Lấy danh sách medical_records
    public function getMedicalRecords() {
        $sql = "SELECT mr.*, pu.name as patient_name, du.name as doctor_name
                FROM medical_records mr
                JOIN patients p ON mr.patient_id = p.id
                JOIN users pu ON p.user_id = pu.id
                JOIN doctors d ON mr.doctor_id = d.id
                JOIN users du ON d.user_id = du.id
                ORDER BY mr.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    // Lấy medical_records theo doctor_id
    public function getMedicalRecordsByDoctorId($doctorId) {
        $sql = "SELECT mr.*, pu.name as patient_name
                FROM medical_records mr
                JOIN patients p ON mr.patient_id = p.id
                JOIN users pu ON p.user_id = pu.id
                WHERE mr.doctor_id = :doctor_id
                ORDER BY mr.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':doctor_id', $doctorId);
        $stmt->execute();
        return $stmt->fetchAll();
    }
}
