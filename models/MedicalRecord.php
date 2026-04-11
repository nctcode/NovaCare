<?php
/**
 * MedicalRecord Model - Quản lý hồ sơ bệnh án
 */
require_once __DIR__ . '/../config/database.php';

class MedicalRecord {
    private $conn;

    public function __construct() {
        $db = new Database();
        $this->conn = $db->getConnection();
    }

    public function getAll() {
        $sql = "SELECT m.*, p.user_id as patient_user_id, d.user_id as doctor_user_id, 
                       up.name as patient_name, ud.name as doctor_name
                FROM medical_records m
                JOIN patients p ON m.patient_id = p.id
                JOIN users up ON p.user_id = up.id
                JOIN doctors d ON m.doctor_id = d.id
                JOIN users ud ON d.user_id = ud.id
                ORDER BY m.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function findById($id) {
        $sql = "SELECT m.*, p.user_id as patient_user_id, d.user_id as doctor_user_id, 
                       up.name as patient_name, ud.name as doctor_name,
                       a.appointment_date, a.reason
                FROM medical_records m
                JOIN patients p ON m.patient_id = p.id
                JOIN users up ON p.user_id = up.id
                JOIN doctors d ON m.doctor_id = d.id
                JOIN users ud ON d.user_id = ud.id
                LEFT JOIN appointments a ON m.appointment_id = a.id
                WHERE m.id = :id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':id', $id);
        $stmt->execute();
        return $stmt->fetch();
    }

    public function getByPatientId($patientId) {
        $sql = "SELECT m.*, d.user_id as doctor_user_id, ud.name as doctor_name
                FROM medical_records m
                JOIN doctors d ON m.doctor_id = d.id
                JOIN users ud ON d.user_id = ud.id
                WHERE m.patient_id = :patient_id
                ORDER BY m.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':patient_id', $patientId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function getByDoctorId($doctorId) {
        $sql = "SELECT m.*, p.user_id as patient_user_id, up.name as patient_name
                FROM medical_records m
                JOIN patients p ON m.patient_id = p.id
                JOIN users up ON p.user_id = up.id
                WHERE m.doctor_id = :doctor_id
                ORDER BY m.created_at DESC";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':doctor_id', $doctorId);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function create($data) {
        $sql = "INSERT INTO medical_records (patient_id, doctor_id, appointment_id, diagnosis, treatment, notes) 
                VALUES (:patient_id, :doctor_id, :appointment_id, :diagnosis, :treatment, :notes)";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(':patient_id', $data['patient_id']);
        $stmt->bindParam(':doctor_id', $data['doctor_id']);
        
        $aptId = !empty($data['appointment_id']) ? $data['appointment_id'] : null;
        $stmt->bindValue(':appointment_id', $aptId, PDO::PARAM_INT);
        
        $stmt->bindParam(':diagnosis', $data['diagnosis']);
        $stmt->bindParam(':treatment', $data['treatment']);
        $stmt->bindParam(':notes', $data['notes']);
        
        $stmt->execute();
        return $this->conn->lastInsertId();
    }
}
